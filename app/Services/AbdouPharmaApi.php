<?php namespace 
App\Services; 
use Illuminate\Support\Facades\Http; 
use Illuminate\Http\Client\PendingRequest; 
use RuntimeException; 
class AbdouPharmaApi 
{ 
    protected string $base; 
    protected ?string $token; 
    public function __construct() 
    { 
        // Cast en string pour éviter les TypeError si la clé de config est absente 
    $this->base = rtrim((string) config('services.abdoupharma.base', ''), '/'); 
    $this->token = config('services.abdoupharma.token'); 
    } 
    protected function client(): PendingRequest 
    { 
        if ($this->base === '') 
            { 
                throw new RuntimeException("ABDOUPHARMA_API_BASE manquant. Renseigne-le dans .env et config/services.php."); 
            } 
        $req = Http::acceptJson() ->baseUrl($this->base) ->timeout(10); 
        if ($this->token) 
            { 
                $req = $req->withToken($this->token); 
            } 
        return $req; 
    } 
    public function products(array $params = []): array 
    { 
        return $this->client() ->get('/api/v1/products', $params) ->throw() ->json(); 
    } 
    public function health(): array 
    { 
        try 
        { 
            return $this->client()->get('/api/v1/health')->throw()->json(); 
        } 
        catch (\Throwable $e) 
        { 
            // Fallback "ping" si /health n'existe pas 
        $r = $this->client()->get('/api/v1/products', ['per_page' => 1]); 
        return [ 'ok' => $r->successful(), 'status'=> $r->status(), 'time' => now()->toISOString(), ]; 
        } 
    } 
} 