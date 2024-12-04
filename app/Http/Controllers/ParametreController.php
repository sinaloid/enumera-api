<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametre;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ParametreController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Paramètres"},
     *      summary="Liste des paramètres",
     *      description="Retourne la liste des paramètres",
     *      path="/api/parametres",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $data = Parametre::where('is_deleted', false)->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucun paramètre trouvé'], 404);
        }

        return response()->json(['message' => 'Paramètres récupérés', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Paramètres"},
     *     description="Crée un nouveau paramètre et retourne le paramètre créé",
     *     path="/api/parametres",
     *     summary="Création d'un paramètre",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"key", "value"},
     *             @OA\Property(property="key", type="string", example="duree_minimum_lecture_cours"),
     *             @OA\Property(property="value", type="string", example="10"),
     *             @OA\Property(property="description", type="string", example="Durée minimum en minutes"),
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
            'key' => 'required|string|max:255|unique:parametres,key',
            'value' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $data = Parametre::create([
            'key' => $request->input('key'),
            'value' => $request->input('value'),
            'description' => $request->input('description'),
            'slug' => Str::slug($request->input('key')),
        ]);

        return response()->json(['message' => 'Paramètre créé avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Paramètres"},
     *      summary="Récupère un paramètre par son slug",
     *      description="Retourne un paramètre",
     *      path="/api/parametres/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="Slug du paramètre à récupérer",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show($slug)
    {
        $data = Parametre::where(['slug' => $slug, 'is_deleted' => false])->first();

        if (!$data) {
            return response()->json(['message' => 'Paramètre non trouvé'], 404);
        }

        return response()->json(['message' => 'Paramètre trouvé', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Paramètres"},
     *     description="Modifie un paramètre et retourne le paramètre modifié",
     *     path="/api/parametres/{slug}",
     *     summary="Modification d'un paramètre",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="value", type="string", example="20"),
     *             @OA\Property(property="description", type="string", example="Durée minimum mise à jour"),
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="Slug du paramètre à modifier",
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
    public function update(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $data = Parametre::where(['slug' => $slug, 'is_deleted' => false])->first();

        if (!$data) {
            return response()->json(['message' => 'Paramètre non trouvé'], 404);
        }

        $data->update([
            'value' => $request->input('value'),
            'description' => $request->input('description'),
        ]);

        return response()->json(['message' => 'Paramètre modifié avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Delete(
     *      tags={"Paramètres"},
     *      summary="Suppression d'un paramètre par son slug",
     *      description="Retourne le paramètre supprimé",
     *      path="/api/parametres/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="Slug du paramètre à supprimer",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy($slug)
    {
        $data = Parametre::where(['slug' => $slug, 'is_deleted' => false])->first();

        if (!$data) {
            return response()->json(['message' => 'Paramètre non trouvé'], 404);
        }

        $data->update(['is_deleted' => true]);

        return response()->json(['message' => 'Paramètre supprimé avec succès', 'data' => $data]);
    }
}
