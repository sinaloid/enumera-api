<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Lecon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CoursController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Cours"},
     *      summary="Liste des cours",
     *      description="Retourne la liste des cours",
     *      path="/api/cours",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *     @OA\PathItem (
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $data = Cours::where("is_deleted",false)->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucun cours trouvé'], 404);
        }

        return response()->json(['message' => 'Cours récupérés', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Cours"},
     *     description="Crée une nouveau cours et retourne le cours créé",
     *     path="/api/cours",
     *     summary="Création d'un cours",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","abreviation","type","lecon"},
     *             @OA\Property(property="label", type="string", example="Intitulé du cours"),
     *             @OA\Property(property="abreviation", type="string", example="Intitulé du cours"),
     *             @OA\Property(property="type", type="string", example="pdf"),
     *             @OA\Property(property="lecon", type="string", example="Slug de la leçon"),
     *             @OA\Property(property="description", type="string", example="Lorem ipsum lores ipomd")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cours créé avec succès"),
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
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'lecon' => 'required|string|max:10',
            'abreviation' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $lecon = Lecon::where(["slug" => $request->lecon,"is_deleted" => false])->first();
        if (!$lecon) {
            return response()->json(['message' => 'Leçon non trouvé'], 404);
        }

        $data = Cours::create([
            'label' => $request->input('label'),
            'abreviation' => $request->input('abreviation'),
            'type' => $request->input('type'),
            'lecon_id' => $lecon->id,
            'description' => $request->input('description'),
            'slug' => Str::random(10),
        ]);

        return response()->json(['message' => 'Cours créé avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Cours"},
     *      summary="Récupère un cours par son slug",
     *      description="Retourne un cours",
     *      path="/api/cours/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug du cours à récupérer",
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
        $data = Cours::where(["slug"=> $slug, "is_deleted" => false])->first();

        if (!$data) {
            return response()->json(['message' => 'Cours non trouvé'], 404);
        }

        return response()->json(['message' => 'Cours trouvé', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Cours"},
     *     description="Modifie un cours et retourne le cours modifié",
     *     path="/api/cours/{slug}",
     *     summary="Modification d'un cours",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","abreviation","type","lecon"},
     *             @OA\Property(property="label", type="string", example="Intitulé du cours"),
     *             @OA\Property(property="abreviation", type="string", example="Intitulé du cours"),
     *             @OA\Property(property="type", type="string", example="pdf"),
     *             @OA\Property(property="lecon", type="string", example="Slug de la leçon"),
     *             @OA\Property(property="description", type="string", example="Lorem ipsum lores ipomd")
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug du cours à modifié",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cours modifié avec succès"),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Slug validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cours non trouvé"),
     *             @OA\Property(property="errors", type="object")
     *         )
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
    public function update(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'lecon' => 'required|string|max:10',
            'abreviation' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $lecon = Lecon::where(["slug" => $request->lecon,"is_deleted" => false])->first();
        if (!$lecon) {
            return response()->json(['message' => 'Leçon non trouvée'], 404);
        }


        $data = Cours::where("slug", $slug)->where("is_deleted",false)->first();

        if (!$data) {
            return response()->json(['message' => 'Cours non trouvé'], 404);
        }

        $data->update([
            'label' => $request->input('label'),
            'type' => $request->input('type'),
            'lecon_id' => $lecon->id,
            'abreviation' => $request->input('abreviation'),
            'description' => $request->input('description'),
        ]);

        return response()->json(['message' => 'Cours modifié avec succès', 'data' => $data], 200);

    }

    /**
     * @OA\Delete(
     *      tags={"Cours"},
     *      summary="Suppression d'un cours par son slug",
     *      description="Retourne le cours supprimé",
     *      path="/api/cours/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cours supprimé avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Slug validation error",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cours non trouvé"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug du cours à supprimer",
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

        $data = Cours::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'Cours non trouvé'], 404);
        }


        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'Cours supprimé avec succès',"data" => $data]);
    }
}
