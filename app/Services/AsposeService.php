<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AsposeService{
    protected $clientId;
    protected $clientSecret;
    protected $tokenCacheKey = 'aspose_token';

    public function __construct()
    {
        $this->clientId = env('ASPOSE_CLOUD_CLIENT_ID');
        $this->clientSecret = env('ASPOSE_CLOUD_CLIENT_SECRET');
    }

    public function getAccessToken()
    {
        // Vérifie si le token est déjà en cache
        if (Cache::has($this->tokenCacheKey)) {
            returnCache::get($this->tokenCacheKey);
        }

        // Si le token n'est pas en cache, obtenir un nouveau token
        $response = Http::asForm()->post('https://api.aspose.cloud/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        $data = $response->json();
        $accessToken = $data['access_token'];
        $expiresIn = $data['expires_in'];

        // Stocker le token en cache pour la durée de sa validité (moins quelques secondes pour la sécurité)Cache::put($this->tokenCacheKey, $accessToken, $expiresIn - 60);

        return $accessToken;
    }

    public function convertDocumentToHtml($filePath)
    {
        // Obtenir le token d'accès
        $accessToken = $this->getAccessToken();

        // Envoyer la requête PUT à l'API Aspose pour convertir le document
        $response = Http::withToken($accessToken)
            ->attach('file', file_get_contents($filePath), 'example.doc')
            ->put('https://api.aspose.cloud/v4.0/words/convert?format=html');

        // Vérifier si la requête a réussi
        if ($response->successful()) {
            // Retourner le contenu HTML en JSON

            return $response->body();
            return response()->json([
                'status' => 'success',
                'html' => $response->body()
            ]);
        } else {
            // Retourner une erreur en JSON
            return response()->json([
                'status' => 'error',
                'message' => $response->json('message')
            ], $response->status());
        }
    }
}
