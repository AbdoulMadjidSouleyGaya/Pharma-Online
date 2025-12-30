<?php namespace 
App\Services; 
use App\Models\PharmaProduct; 
class PharmaSyncService { public function __construct(private AbdouPharmaApi 
    $api) {} /** * Synchronise tous les produits de l'API vers la DB locale. * 
    @return array{created:int,updated:int,total:int,pages:int} */ 
    public function syncAll(int $perPage = 100): array { $page = 1; 
        $created = 0; 
        $updated = 0; 
        $totalSeen = 0; 
        $pages = 0; 
        do { 
            $resp = $this->api->products(['per_page' => $perPage, 'page' => $page]); 
            $data = $resp['data'] ?? []; 
            $meta = $resp['meta'] ?? []; 
            $pages++; 
            foreach ($data as $p) { 
                $totalSeen++; 
                $local = PharmaProduct::updateOrCreate( ['remote_id' => $p['id']], [ 'libelle' => $p['libelle'], 'prix' => (float)($p['prix'] ?? 0), 'stock' => (string)($p['stock'] ?? ''), 'synced_at' => now(), ] ); 
                if ($local->wasRecentlyCreated) 
                    { 
                        $created++; 
                    } 
                    elseif ($local->wasChanged()) 
                        { 
                            $updated++; 
                        } 
                    
                } 
                $current = $meta['current_page'] ?? $page; 
                $last = $meta['last_page'] ?? $page; 
                $page++; 
            } 
            while (!empty($data) && $current < $last); 
            return ['created' => $created, 'updated' => $updated, 'total' => $totalSeen, 'pages' => $pages]; 
        } 
}