<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class ChatController extends Controller
{
    /**
     * @OA\Post(
     *     tags={"ChatGPt"},
     *     description="Crée une nouvelle leçon et retourne la leçon créée",
     *     path="/api/chatgpt",
     *     summary="Prompt gpt",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","abreviation","type","chapitre"},
     *             @OA\Property(property="message", type="string", example="Histoire des royaumes moose"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Prompt gpt"),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Les données fournies ne sont pas valides"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    /*public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }



        $data = Http::withHeaders([
            'Authorization' => 'Bearer '.config('services.openai.api_key'),
            "Content-Type" => "application/json"
        ])->post('https://api.openai.com/v1/chat/completions',[
            'messages' => [$request->message],
            "model" => "gpt-4o-mini",
        ]);

        //$data = $data->json()['choices'][0]['text'] ?? "Sorry, I could not understand that.";
        return response()->json(['message' => 'prompt gpt', 'data' => $data], 200);
    }*/


    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $message = "what is laravel";
        $response = $this->httpClient->post('chat/completions', [
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a teacher'],
                    ['role' => 'user', 'content' => $request->message],
                ],
            ],
        ]);

        return json_decode($response->getBody(), true)['choices'][0]['message']['content'];
    }

}
