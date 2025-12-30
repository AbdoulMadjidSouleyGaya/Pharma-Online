<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuardGroup;
use App\Models\GuardGroupPharmacy;
use App\Models\GuardRotationSetting;
use App\Services\GuardRotationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

use PhpOffice\PhpSpreadsheet\IOFactory as XlsxIO;

class AdminGuardController extends Controller
{
    public function index(Request $request, GuardRotationService $rotation)
    {
        $state = $rotation->current();
        $preview = $rotation->preview(8);

        $groups = GuardGroup::query()->orderBy('id')->get();

        // groupe actif + pharmacies
        $activeGroupId = $state['configured'] ? (int)$state['current_group'] : null;
        $activeGroup = $activeGroupId ? GuardGroup::find($activeGroupId) : null;
        $activePharmacies = $activeGroup
            ? $activeGroup->pharmacies()->orderBy('created_at')->get()
            : collect();

        return view('admin.guards.index', compact('state','preview','groups','activeGroup','activePharmacies'));
    }

    // Page de configuration rotation (une seule ligne en DB)
    public function create()
    {
        $cfg = GuardRotationSetting::query()->first();

        // s'assurer que les 5 groupes existent
        for ($i=1; $i<=5; $i++) {
            GuardGroup::firstOrCreate(['id'=>$i], ['label'=>"Groupe {$i}"]);
        }

        $groups = GuardGroup::query()->orderBy('id')->get();

        return view('admin.guards.create', compact('cfg','groups'));
    }

    // Sauvegarder config rotation
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'region' => ['nullable','string','max:150'],
            'rotation_start_at' => ['required','date'], // datetime-local
            'start_group' => ['required','integer','min:1','max:5'],
        ]);

        $cfg = GuardRotationSetting::query()->first();
        if (!$cfg) $cfg = new GuardRotationSetting();

        $cfg->region = $data['region'] ?? null;
        $cfg->rotation_start_at = $data['rotation_start_at'];
        $cfg->start_group = (int) $data['start_group'];
        $cfg->cycle_length = 5;
        $cfg->period_days = 7;
        $cfg->save();

        return redirect()->route('admin.guards.index')->with('status', 'Rotation configurée ✅');
    }

    // Afficher un groupe (liste)
    public function show(GuardGroup $group)
    {
        $pharmacies = $group->pharmacies()->orderBy('created_at')->get();
        return view('admin.guards.show', compact('group','pharmacies'));
    }

    // Importer XLSX/CSV d’un groupe (on accepte ton fichier Excel)
    public function import(Request $request, GuardGroup $group): RedirectResponse
    {
        $data = $request->validate([
            'file' => [
                'required','file','max:102400',
                'mimes:xlsx,xls,csv,txt',
            ],
        ]);

        // purge puis re-import (pour éviter doublons)
        $group->pharmacies()->delete();

        $ext = strtolower($request->file('file')->getClientOriginalExtension());
        $abs = $request->file('file')->getRealPath();

        $rows = [];
        try {
            if (in_array($ext, ['xlsx','xls'])) {
                $rows = $this->parseSpreadsheet($abs);
            } else {
                $rows = $this->parseCsv($abs);
            }
        } catch (\Throwable $e) {
            Log::error("Group import error: ".$e->getMessage());
            return back()->withErrors(['file' => "Erreur import : ".$e->getMessage()]);
        }

        $inserted = 0;
        foreach ($rows as $r) {
            $name = trim((string)($r['name'] ?? $r['nom'] ?? ''));
            if ($name === '') continue;

            GuardGroupPharmacy::create([
                'guard_group_id' => $group->id,
                'name' => $name,
                'district' => trim((string)($r['district'] ?? $r['quartier'] ?? '')) ?: null,
                'address'  => trim((string)($r['address']  ?? $r['localisation'] ?? '')) ?: null,
                'phone'    => trim((string)($r['phone']    ?? $r['contact'] ?? '')) ?: null,
            ]);
            $inserted++;
        }

        return back()->with('status', "Import OK : {$inserted} pharmacie(s) dans {$group->label} ✅");
    }

    // =================== Helpers CSV/XLSX ===================

    private function parseCsv(string $abs): array
    {
        $contents = file_get_contents($abs);
        if ($contents === false) return [];
        $sep = (substr_count($contents, ';') > substr_count($contents, ',')) ? ';' : ',';

        $lines = preg_split("/\r\n|\n|\r/", trim($contents));
        if (!$lines) return [];

        $first = preg_replace('/^\xEF\xBB\xBF/', '', $lines[0]);
        $cols  = str_getcsv($first, $sep);

        $header = array_map(fn($h)=>mb_strtolower(trim((string)$h),'UTF-8'), $cols);
        $isHeader = in_array('nom', $header) || in_array('name', $header);

        $rows = [];
        if (!$isHeader) $rows[] = $this->mapColumns($cols);

        foreach (array_slice($lines, 1) as $line) {
            $row = str_getcsv($line, $sep);
            if (count(array_filter($row, fn($v)=>trim((string)$v) !== '')) === 0) continue;
            $rows[] = $this->mapColumns($row, $isHeader ? $header : null);
        }

        return $rows;
    }

    private function parseSpreadsheet(string $abs): array
    {
        $reader = XlsxIO::createReader(XlsxIO::identify($abs));
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($abs);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        $rows = [];
        $header = null;

        foreach ($data as $idx => $row) {
            $vals = array_values($row);

            if ($idx === 1) {
                $maybeHeader = array_map(fn($v)=>mb_strtolower(trim((string)$v),'UTF-8'), $vals);
                $isHeader = in_array('nom', $maybeHeader) || in_array('name', $maybeHeader);
                if ($isHeader) { $header = $maybeHeader; continue; }
            }

            if (count(array_filter($vals, fn($v)=>trim((string)$v) !== '')) === 0) continue;
            $rows[] = $this->mapColumns($vals, $header);
        }

        return $rows;
    }

    private function mapColumns(array $vals, ?array $header = null): array
    {
        if ($header) {
            $find = function($keys) use ($header, $vals) {
                foreach ((array)$keys as $k) {
                    $i = array_search($k, $header, true);
                    if ($i !== false) return trim((string)($vals[$i] ?? ''));
                }
                return '';
            };
            return [
                'name'     => $find(['nom','name']),
                'district' => $find(['quartier','district']),
                'address'  => $find(['localisation','adresse','address']),
                'phone'    => $find(['contact','telephone','téléphone','phone']),
            ];
        }

        return [
            'name'     => trim((string)($vals[0] ?? '')),
            'district' => trim((string)($vals[1] ?? '')),
            'address'  => trim((string)($vals[2] ?? '')),
            'phone'    => trim((string)($vals[3] ?? '')),
        ];
    }
}
