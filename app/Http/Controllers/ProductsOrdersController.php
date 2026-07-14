<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use App\Models\PharmaProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use Symfony\Component\Process\Process;

class ProductsOrdersController extends Controller
{
    /**
     * Chemins des binaires OCR, configurables via .env (TESSERACT_BIN / PDFTOPPM_BIN)
     * pour rester portable entre un poste Windows de dev et un serveur Linux
     * de production (voir config/pharma.php).
     */
    private function pdftoppmBin(): string
    {
        return (string) config('pharma.pdftoppm_bin', 'pdftoppm');
    }

    private function tesseractBin(): string
    {
        return (string) config('pharma.tesseract_bin', 'tesseract');
    }

    /**
     * Liste des produits que le client peut commander.
     */
    public function index(Request $request)
    {
        $q       = $request->query('q');
        $perPage = (int) $request->query('per_page', 20);

        $products = PharmaProduct::query()
            ->whereIn('stock', ['Disponible', 'Stock bas'])
            ->when($q, function ($query, $q) {
                $query->where('libelle', 'like', "%{$q}%");
            })
            ->orderBy('libelle')
            ->paginate($perPage);

        return view('produits_commande.index', [
            'products' => $products,
            'params'   => $request->only('q', 'per_page'),
        ]);
    }

    /**
     * Affiche la liste des pharmacies partenaires.
     */
    public function selectPharmacy(Request $request)
    {
        $q = $request->query('q');
        $userGeo = $this->resolveCustomerCoordinates($request);

        if (empty($q)) {
            $pharmacies = Pharmacy::orderBy('name')->get();
            $this->attachDistanceMetadata($pharmacies, $userGeo);

            return view('client.produits.pharmacies', [
                'pharmacies' => $pharmacies,
                'q'          => $q,
                'userGeo'    => $userGeo,
            ]);
        }

        $qTerm = $q;

        $pharmacyIdsByProduct = PharmaProduct::where('libelle', 'like', "%{$qTerm}%")
            ->pluck('pharmacy_id')
            ->unique()
            ->toArray();

        $pharmacies = Pharmacy::query()
            ->where(function ($query) use ($qTerm, $pharmacyIdsByProduct) {
                $query->where('name', 'like', "%{$qTerm}%")
                    ->orWhere('district', 'like', "%{$qTerm}%")
                    ->orWhere('address', 'like', "%{$qTerm}%")
                    ->orWhereIn('id', $pharmacyIdsByProduct);
            })
            ->withCount([
                'pharmaProducts as matching_products_count' => function ($pQuery) use ($qTerm) {
                    $pQuery->where('libelle', 'like', "%{$qTerm}%");
                }
            ])
            ->orderBy('name')
            ->get();

        $this->attachDistanceMetadata($pharmacies, $userGeo);

        return view('client.produits.pharmacies', [
            'pharmacies' => $pharmacies,
            'q'          => $q,
            'userGeo'    => $userGeo,
        ]);
    }

    /**
     * ✅ Affiche UNIQUEMENT les produits DISPONIBLES d'une pharmacie choisie.
     */
    public function pharmacyProducts(Request $request, Pharmacy $pharmacy)
    {
        $q = $request->query('q');
        $fromRx = $request->boolean('from_rx');
        $rxTerms = $fromRx ? $request->session()->get('rx_terms', []) : [];
        $rxTerms = is_array($rxTerms) ? array_values(array_filter($rxTerms, fn ($term) => is_string($term) && trim($term) !== '')) : [];

        $products = PharmaProduct::query()
            ->where('pharmacy_id', $pharmacy->id)
            ->where('stock', 'Disponible') // ✅ ferme
            ->when($q, function ($query) use ($q) {
                $query->where('libelle', 'like', "%{$q}%");
            })
            ->orderBy('libelle')
            ->paginate(12)
            ->withQueryString();

        $autoSelectedProductIds = [];
        $autoSelectedOrderItems = [];

        if ($fromRx && !empty($rxTerms)) {
            $matchingProducts = PharmaProduct::query()
                ->where('pharmacy_id', $pharmacy->id)
                ->where('stock', 'Disponible')
                ->orderBy('libelle')
                ->get(['id', 'libelle', 'prix']);

            foreach ($matchingProducts as $product) {
                if (! $this->productMatchesPrescriptionTerms((string) $product->libelle, $rxTerms)) {
                    continue;
                }

                $autoSelectedProductIds[] = (int) $product->id;
                $autoSelectedOrderItems[] = [
                    'id' => (int) $product->id,
                    'price' => is_null($product->prix) ? 0 : (float) $product->prix,
                ];
            }

            $autoSelectedProductIds = array_values(array_unique($autoSelectedProductIds));
            $autoSelectedOrderItems = array_values(collect($autoSelectedOrderItems)
                ->unique('id')
                ->map(fn ($item) => [
                    'id' => (int) $item['id'],
                    'price' => (float) $item['price'],
                ])
                ->all());
        }

        return view('client.produits.pharmacy-products', [
            'pharmacy' => $pharmacy,
            'products' => $products,
            'q'        => $q,
            'fromRx'   => $fromRx,
            'rxTerms'  => $rxTerms,
            'autoSelectedProductIds' => $autoSelectedProductIds,
            'autoSelectedOrderItems' => $autoSelectedOrderItems,
        ]);
    }

    /**
     * Téléverse une ordonnance (image ou PDF).
     */
    public function uploadPrescription(Request $request)
    {
        $validated = $request->validate([
            'prescription' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
        ], [
            'prescription.required' => "Veuillez sélectionner un fichier.",
            'prescription.mimes'    => "Formats autorisés : PDF, JPG, JPEG, PNG, WEBP.",
            'prescription.max'      => "Le fichier ne doit pas dépasser 10 Mo.",
        ]);

        $userId = (int) auth()->id();
        $file   = $validated['prescription'];

        // Supprimer ancienne ordonnance
        $oldPath = $request->session()->get('prescription_path');
        if ($oldPath && Storage::disk('local')->exists($oldPath)) {
            Storage::disk('local')->delete($oldPath);
        }

        // Sauvegarder nouvelle ordonnance (disque privé, jamais accessible via /storage)
        $filename = 'ordonnance_user_'.$userId.'_'.now()->format('Ymd_His').'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('prescriptions', $filename, 'local');

        // Session meta
        $request->session()->put('prescription_path', $path);
        $request->session()->put('prescription_original', $file->getClientOriginalName());
        $request->session()->put('prescription_uploaded_at', now()->toDateTimeString());

        // Reset résultats précédents
        $request->session()->forget(['rx_terms', 'rx_total_terms']);

        return redirect()->route('produits.commande.pharmacies', $request->only('q'))
            ->with('success', "✅ Ordonnance téléversée : ".$file->getClientOriginalName());
    }

    /**
     * Retirer/Réinitialiser l’ordonnance.
     */
    public function clearPrescription(Request $request)
    {
        $oldPath = $request->session()->get('prescription_path');
        if ($oldPath && Storage::disk('local')->exists($oldPath)) {
            Storage::disk('local')->delete($oldPath);
        }

        $request->session()->forget([
            'prescription_path',
            'prescription_original',
            'prescription_uploaded_at',
            'rx_terms',
            'rx_total_terms',
        ]);

        return redirect()->route('produits.commande.pharmacies', $request->only('q'))
            ->with('success', "✅ Ordonnance retirée.");
    }

    /**
     * Recherche pharmacie via ordonnance (PDF texte ou scanné, image).
     */
    public function searchPharmacyByPrescription(Request $request)
    {
        $userGeo = $this->resolveCustomerCoordinates($request);
        $path = $request->session()->get('prescription_path');

        if (!$path || !Storage::disk('local')->exists($path)) {
            return redirect()->route('produits.commande.pharmacies', $request->only('q'))
                ->with('error', "⚠️ Aucune ordonnance valide n'a été trouvée. Téléverse d'abord ton ordonnance.");
        }

        $absolutePath = Storage::disk('local')->path($path);

        // Type fichier
        $mime = @mime_content_type($absolutePath) ?: '';
        $isPdf = str_contains($mime, 'pdf') || Str::endsWith(Str::lower($absolutePath), '.pdf');
        $isImage = str_starts_with($mime, 'image/') || preg_match('/\.(jpg|jpeg|png|webp)$/i', $absolutePath);

        if (!$isPdf && !$isImage) {
            return redirect()->route('produits.commande.pharmacies', $request->only('q'))
                ->with('error', "⚠️ Format non pris en charge. Téléverse une image (JPG/PNG/WEBP) ou un PDF.");
        }

        // Extraction texte (PDF texte -> PdfParser, sinon OCR)
        try {
            $rawText = $this->extractTextFromPrescription($absolutePath, $isPdf ? 'pdf' : 'image');
        } catch (\Throwable $e) {
            Log::error("OCR/Extraction ordonnance échouée", [
                'path'  => $path,
                'mime'  => $mime,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('produits.commande.pharmacies', $request->only('q'))
                ->with('error', "⚠️ Impossible de lire l'ordonnance. Détail: ".$e->getMessage());
        }

        $text = $this->normalizeExtractedText($rawText);

        Log::info("🧾 Ordonnance - extrait (début)", [
            'path' => $path,
            'preview' => mb_substr($text, 0, 800),
        ]);

        // Détection produits
        $terms = $this->extractProductTermsFromText($text);

        Log::info("✅ RX TERMS DETECTES", [
            'count' => count($terms),
            'terms' => $terms,
        ]);

        if (count($terms) === 0) {
            return redirect()->route('produits.commande.pharmacies', $request->only('q'))
                ->with('error', "⚠️ Aucun produit détecté sur l’ordonnance. Essaie une photo plus nette ou un PDF plus lisible.");
        }

        // Ranking
        [$orderedPharmacies, $scoresById, $matchesById] = $this->rankPharmaciesByPrescriptionTerms($terms);

        $totalTerms = count($terms);

        foreach ($orderedPharmacies as $p) {
            $p->setAttribute('rx_match_count', (int)($scoresById[$p->id] ?? 0));
            $p->setAttribute('rx_total_terms', $totalTerms);
            $p->setAttribute('rx_match_terms', $matchesById[$p->id] ?? []);
        }

        $this->attachDistanceMetadata($orderedPharmacies, $userGeo);

        if ($orderedPharmacies->count() === 0) {
            return redirect()->route('produits.commande.pharmacies', $request->only('q'))
                ->with('error', "⚠️ Aucun partenaire ne dispose des produits détectés sur l’ordonnance.");
        }

        // session (affichage)
        $request->session()->put('rx_terms', $terms);
        $request->session()->put('rx_total_terms', $totalTerms);

        return view('client.produits.pharmacies', [
            'pharmacies' => $orderedPharmacies,
            'q'          => $request->query('q', ''),
            'rx_terms'   => $terms,
            'rx_total'   => $totalTerms,
            'rx_only_matches' => true,
            'userGeo'    => $userGeo,
        ])->with('success', "✅ ".count($terms)." produit(s) détecté(s). ".count($orderedPharmacies)." pharmacie(s) correspondante(s) trouvée(s).");
    }

    /**
     * Classement des pharmacies par termes trouvés (Disponible + Stock bas).
     */
    private function rankPharmaciesByPrescriptionTerms(array $terms): array
    {
        $pharmacies = Pharmacy::query()->orderBy('name')->get();

        $productsByPharmacy = PharmaProduct::query()
            ->select('pharmacy_id', 'libelle', 'stock')
            ->get()
            ->groupBy('pharmacy_id');

        $scores = [];
        $matchesById = [];

        foreach ($pharmacies as $p) {
            $scores[$p->id] = 0;
            $matchesById[$p->id] = [];
        }

        $norm = function (string $s): string {
            $s = mb_strtolower($s);
            $tmp = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
            if (is_string($tmp) && $tmp !== '') $s = $tmp;
            $s = preg_replace('/[^a-z0-9\s\+]/', ' ', $s);
            $s = preg_replace('/\s+/', ' ', $s);
            return trim($s);
        };

        $rxTerms = [];
        foreach ($terms as $t) {
            $t = $norm((string) $t);
            if (mb_strlen($t) >= 3) $rxTerms[] = $t;
        }
        $rxTerms = array_values(array_unique($rxTerms));

        $allowedStocks = ['Disponible', 'Stock bas'];

        foreach ($pharmacies as $p) {
            $rows = $productsByPharmacy->get($p->id, collect());
            if ($rows->isEmpty()) continue;

            $libelles = $rows
                ->filter(fn($r) => in_array(($r->stock ?? ''), $allowedStocks, true))
                ->pluck('libelle')
                ->map(fn($l) => $norm((string) $l))
                ->values()
                ->all();

            if (empty($libelles)) continue;

            foreach ($rxTerms as $term) {
                foreach ($libelles as $lib) {
                    if ($lib !== '' && str_contains($lib, $term)) {
                        if (!in_array($term, $matchesById[$p->id], true)) {
                            $matchesById[$p->id][] = $term;
                            $scores[$p->id] += 1;
                        }
                        break;
                    }
                }
            }
        }

        $filtered = $pharmacies->filter(fn($p) => (int)($scores[$p->id] ?? 0) > 0);

        $ordered = $filtered->sort(function ($a, $b) use ($scores) {
            $sa = (int)($scores[$a->id] ?? 0);
            $sb = (int)($scores[$b->id] ?? 0);
            if ($sa === $sb) return strcasecmp($a->name ?? '', $b->name ?? '');
            return $sb <=> $sa;
        })->values();

        return [$ordered, $scores, $matchesById];
    }


    private function productMatchesPrescriptionTerms(string $libelle, array $rxTerms): bool
    {
        $libelle = $this->normalizeComparableString($libelle);
        if ($libelle === '') {
            return false;
        }

        foreach ($rxTerms as $term) {
            $term = $this->normalizeComparableString((string) $term);
            if ($term !== '' && str_contains($libelle, $term)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeComparableString(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if (is_string($ascii) && $ascii !== '') {
            $value = $ascii;
        }

        $value = preg_replace('/[^a-z0-9\s\+]/', ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return trim((string) $value);
    }

    /**
     * ✅ Extraction texte depuis PDF ou image (OCR).
     */
    private function extractTextFromPrescription(string $absolutePath, string $type): string
    {
        if ($type === 'pdf') {
            // PDF TEXTE d'abord
            if (class_exists(Parser::class)) {
                try {
                    $parser = new Parser();
                    $pdf = $parser->parseFile($absolutePath);
                    $text = trim((string) $pdf->getText());
                    if ($text !== '') return $text;
                } catch (\Throwable $e) {
                    Log::warning("PdfParser a échoué, fallback OCR", ['err' => $e->getMessage()]);
                }
            }
            // PDF scanné -> OCR
            return $this->ocrScannedPdf($absolutePath);
        }

        // IMAGE -> OCR
        return $this->ocrImage($absolutePath);
    }

    /**
     * ✅ OCR image (jpg/png/webp)
     */
    private function ocrImage(string $imagePath): string
    {
        $tesseract = $this->tesseractBin();

        if (! $this->commandExists($tesseract)) {
            throw new \RuntimeException("OCR indisponible : 'tesseract' n'est pas installé ou pas dans PATH.");
        }

        $outBase = storage_path('app/tmp/ocr_' . uniqid());
        @mkdir(dirname($outBase), 0777, true);

        $process = new Process([
            $tesseract,
            $imagePath,
            $outBase,
            '-l', 'fra+eng',
            '--psm', '6',
        ]);

        $process->setTimeout(120);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException("OCR image échoué : " . trim($process->getErrorOutput()));
        }

        $txtFile = $outBase . '.txt';
        $text = is_file($txtFile) ? file_get_contents($txtFile) : '';
        @unlink($txtFile);

        $text = trim((string) $text);
        if ($text === '') {
            throw new \RuntimeException("OCR image : aucun texte détecté. Essaie une image plus nette.");
        }

        return $text;
    }

    /**
     * ✅ OCR PDF scanné : pdftoppm -> images -> tesseract
     */
    private function ocrScannedPdf(string $pdfPath): string
    {
        // pdftoppm peut être absent du PATH -> on autorise un chemin complet via .env (PDFTOPPM_BIN)
        $pdftoppm  = $this->pdftoppmBin();
        $tesseract = $this->tesseractBin();

        if ($pdftoppm === 'pdftoppm' && ! $this->commandExists('pdftoppm')) {
            throw new \RuntimeException("OCR PDF scanné indisponible : 'pdftoppm' (Poppler) n'est pas dans PATH. Ajoute Poppler\\Library\\bin au PATH ou mets le chemin complet dans PDFTOPPM_BIN.");
        }

        if (! $this->commandExists($tesseract)) {
            throw new \RuntimeException("OCR PDF scanné indisponible : 'tesseract' n'est pas installé ou pas dans PATH.");
        }

        $tmpDir = storage_path('app/tmp/pdf_ocr_' . uniqid());
        @mkdir($tmpDir, 0777, true);

        $prefix = $tmpDir . DIRECTORY_SEPARATOR . 'page';

        $process = new Process([
            $pdftoppm,
            '-png',
            '-r', '200',
            $pdfPath,
            $prefix,
        ]);

        $process->setTimeout(180);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException("Conversion PDF->images échouée : " . trim($process->getErrorOutput()));
        }

        $images = glob($tmpDir . DIRECTORY_SEPARATOR . 'page-*.png') ?: [];
        sort($images);

        if (count($images) === 0) {
            throw new \RuntimeException("Aucune page image générée depuis le PDF (pdftoppm).");
        }

        $allText = [];
        foreach ($images as $img) {
            try {
                $allText[] = $this->ocrImage($img);
            } catch (\Throwable $e) {
                Log::warning("OCR page échoué", ['img' => basename($img), 'err' => $e->getMessage()]);
            }
        }

        foreach ($images as $img) @unlink($img);
        @rmdir($tmpDir);

        $text = trim(implode("\n\n", array_filter($allText)));

        if ($text === '') {
            throw new \RuntimeException("OCR PDF scanné : aucun texte détecté. Essaie un scan plus net.");
        }

        return $text;
    }

    /**
     * ✅ Nettoyage texte extrait (MANQUAIT chez toi)
     */
    private function normalizeExtractedText(string $text): string
    {
        $t = $text ?? '';
        $t = str_replace(["\r\n", "\r"], "\n", $t);
        $t = preg_replace('/[|`´]/u', ' ', $t);
        $t = preg_replace('/[ \t]+/u', ' ', $t);
        $t = preg_replace("/\n{2,}/u", "\n", $t);
        $t = preg_replace('/\s*(?:•|·|\*|\-)\s+/u', "\n", $t);
        $t = preg_replace('/\s*;\s*/u', "\n", $t);
        $t = preg_replace('/\s*\|\s*/u', "\n", $t);
        return trim($t);
    }


    /**
     * Récupère la position actuelle de l'utilisateur depuis la requête ou la session.
     */
    private function resolveCustomerCoordinates(Request $request): ?array
    {
        $lat = $request->input('user_latitude', $request->query('user_latitude'));
        $lng = $request->input('user_longitude', $request->query('user_longitude'));

        $lat = is_numeric($lat) ? (float) $lat : null;
        $lng = is_numeric($lng) ? (float) $lng : null;

        if ($lat !== null && $lng !== null
            && $lat >= -90 && $lat <= 90
            && $lng >= -180 && $lng <= 180) {
            $geo = [
                'lat' => round($lat, 7),
                'lng' => round($lng, 7),
            ];

            $request->session()->put('customer_geo', $geo);

            return $geo;
        }

        $geo = $request->session()->get('customer_geo');
        if (is_array($geo)
            && isset($geo['lat'], $geo['lng'])
            && is_numeric($geo['lat'])
            && is_numeric($geo['lng'])) {
            return [
                'lat' => (float) $geo['lat'],
                'lng' => (float) $geo['lng'],
            ];
        }

        return null;
    }

    /**
     * Ajoute les informations de distance à chaque pharmacie.
     */
    private function attachDistanceMetadata($pharmacies, ?array $userGeo): void
    {
        if (! $pharmacies) {
            return;
        }

        foreach ($pharmacies as $pharmacy) {
            $pharmacy->setAttribute('distance_meters', null);
            $pharmacy->setAttribute('distance_label', null);
            $pharmacy->setAttribute('distance_available', false);

            if (! $userGeo
                || ! isset($pharmacy->latitude, $pharmacy->longitude)
                || ! is_numeric($pharmacy->latitude)
                || ! is_numeric($pharmacy->longitude)) {
                continue;
            }

            $distance = $this->distanceInMeters(
                (float) $userGeo['lat'],
                (float) $userGeo['lng'],
                (float) $pharmacy->latitude,
                (float) $pharmacy->longitude,
            );

            $pharmacy->setAttribute('distance_meters', $distance);
            $pharmacy->setAttribute('distance_label', $this->formatDistance($distance));
            $pharmacy->setAttribute('distance_available', true);
        }
    }

    /**
     * Distance entre l'utilisateur et la pharmacie en mètres.
     */
    private function distanceInMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Format d'affichage de distance.
     */
    private function formatDistance(float $distanceMeters): string
    {
        if ($distanceMeters < 1000) {
            return (string) round($distanceMeters).' m';
        }

        return number_format($distanceMeters / 1000, 2, ',', ' ').' km';
    }

    /**
     * ✅ Extraction termes produits (fix numérotation "1." / "2)" etc.)
     */
    private function extractProductTermsFromText(string $text): array
    {
        $lines = preg_split("/\n+/u", $text) ?: [];
        if (count($lines) <= 1) {
            $lines = preg_split("/[.;]+/u", $text) ?: [];
        }

        $stopContains = [
            'ordonnance','docteur','dr ','médecin','hopital','hôpital',
            'patient','âge','date','signature','cachet','posologie',
            'matin','soir','midi','pendant','jours','semaines',
            'tel','tél','phone','niamey','niger'
        ];

        $terms = [];

        foreach ($lines as $line) {
            $l = trim((string)$line);
            if ($l === '') continue;

            // ✅ enlever "1." "2)" "-" "•" en début de ligne
            $l = preg_replace('/^\s*(?:\d+\s*[\.\)]\s*|[-–—]\s*|[•·\*]\s*)/u', '', $l);

            $lower = mb_strtolower($l);

            foreach ($stopContains as $s) {
                if (str_contains($lower, $s)) {
                    $l = preg_replace('/\b'.preg_quote($s, '/').'\b/iu', ' ', $l);
                }
            }

            // Retirer dosages & formes (mais on garde le nom du produit)
            $l = preg_replace('/\b(\d+)\s*(mg|g|ml|mcg|µg|ui|iu|%)\b/iu', ' ', $l);
            $l = preg_replace('/\b(\d+)\s*(x|×)\s*(\d+)\b/iu', ' ', $l);
            $l = preg_replace('/\b(qsp|cp|comprim[ée]s?|g[ée]lules?|sachet|ampoule|flacon|sirop)\b/iu', ' ', $l);

            $l = preg_replace('/[ \t]+/u', ' ', $l);
            $l = trim($l, " \t\n\r\0\x0B-–—,.;:()[]");

            if (mb_strlen($l) < 3) continue;

            $parts = preg_split('/\s*,\s*/u', $l) ?: [$l];
            foreach ($parts as $p) {
                $p = trim($p);
                if (mb_strlen($p) < 3) continue;
                if (mb_strlen($p) > 70) $p = mb_substr($p, 0, 70);
                $terms[] = $p;
            }
        }

        $terms = array_values(array_unique($terms));
        if (count($terms) > 20) $terms = array_slice($terms, 0, 20);

        return $terms;
    }

    /**
     * Vérifie si une commande est disponible (Windows/Linux).
     */
    private function commandExists(string $cmd): bool
    {
        $where = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where' : 'command -v';
        $result = shell_exec($where.' '.escapeshellarg($cmd).' 2>&1');
        return is_string($result) && trim($result) !== '';
    }
}
