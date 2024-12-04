<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\MessageDefilant;

class MessageDefilantController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Messages Défilants"},
     *      summary="Liste des messages défilants",
     *      description="Retourne la liste des messages défilants",
     *      path="/api/messages-defilants",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $messages = MessageDefilant::where('is_deleted', false)->get();

        if ($messages->isEmpty()) {
            return response()->json(['message' => 'Aucun message trouvé'], 404);
        }

        return response()->json(['message' => 'Messages récupérés', 'data' => $messages], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Messages Défilants"},
     *     description="Crée un nouveau message défilant",
     *     path="/api/messages-defilants",
     *     summary="Création d'un message défilant",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"titre", "contenu", "type", "date_debut", "date_fin"},
     *             @OA\Property(property="titre", type="string", example="Nouvelle Actualité"),
     *             @OA\Property(property="contenu", type="string", example="Détails du message défilant"),
     *             @OA\Property(property="type", type="string", example="info"),
     *             @OA\Property(property="date_debut", type="string", format="date", example="2024-01-01"),
     *             @OA\Property(property="date_fin", type="string", format="date", example="2024-01-10")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'type' => 'required|string|max:50',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message = MessageDefilant::create([
            'titre' => $request->input('titre'),
            'contenu' => $request->input('contenu'),
            'type' => $request->input('type'),
            'date_debut' => $request->input('date_debut'),
            'date_fin' => $request->input('date_fin'),
            'slug' => Str::slug($request->input('titre')) . '-' . Str::random(6),
        ]);

        return response()->json(['message' => 'Message créé avec succès', 'data' => $message], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Messages Défilants"},
     *      summary="Récupère un message par son slug",
     *      path="/api/messages-defilants/{slug}",
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="Slug du message à récupérer",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show($slug)
    {
        $message = MessageDefilant::where('slug', $slug)->where('is_deleted', false)->first();

        if (!$message) {
            return response()->json(['message' => 'Message non trouvé'], 404);
        }

        return response()->json(['message' => 'Message trouvé', 'data' => $message], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Messages Défilants"},
     *     description="Modifie un message",
     *     path="/api/messages-defilants/{slug}",
     *     summary="Modification d'un message défilant",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"titre", "contenu", "type", "date_debut", "date_fin"},
     *             @OA\Property(property="titre", type="string", example="Nouvelle Actualité Modifiée"),
     *             @OA\Property(property="contenu", type="string", example="Nouveau contenu"),
     *             @OA\Property(property="type", type="string", example="evenement"),
     *             @OA\Property(property="date_debut", type="string", format="date", example="2024-02-01"),
     *             @OA\Property(property="date_fin", type="string", format="date", example="2024-02-15")
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="Slug du message à modifier",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'type' => 'required|string|max:50',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message = MessageDefilant::where('slug', $slug)->where('is_deleted', false)->first();

        if (!$message) {
            return response()->json(['message' => 'Message non trouvé'], 404);
        }

        $message->update($request->only(['titre', 'contenu', 'type', 'date_debut', 'date_fin']));

        return response()->json(['message' => 'Message modifié avec succès', 'data' => $message], 200);
    }

    /**
     * @OA\Delete(
     *      tags={"Messages Défilants"},
     *      summary="Supprime un message par son slug",
     *      path="/api/messages-defilants/{slug}",
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="Slug du message à supprimer",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function destroy($slug)
    {
        $message = MessageDefilant::where('slug', $slug)->where('is_deleted', false)->first();

        if (!$message) {
            return response()->json(['message' => 'Message non trouvé'], 404);
        }

        $message->update(['is_deleted' => true]);

        return response()->json(['message' => 'Message supprimé avec succès', 'data' => $message], 200);
    }
}
