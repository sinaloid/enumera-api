<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ClasseController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Classes"},
     *      summary="Liste des classes",
     *      description="Retourne la liste des classes",
     *      path="/api/classes",
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
        $data = Classe::where("is_deleted",false)->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucun classe trouvé'], 404);
        }

        return response()->json(['message' => 'Classes récupérées', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Classes"},
     *     description="Crée une nouvelle classe et retourne la classe créée",
     *     path="/api/classes",
     *     summary="Création d'une classe",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label"},
     *             @OA\Property(property="label", type="string", example="6 ième"),
     *             @OA\Property(property="description", type="string", example="La classe de 6 ième")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Classe créée avec succès"),
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
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }


        $data = Classe::create([
            'label' => $request->input('label'),
            'description' => $request->input('description'),
            'slug' => Str::random(10),
        ]);

        return response()->json(['message' => 'Classe créée avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Classes"},
     *      summary="Récupère une classe par son slug",
     *      description="Retourne une classe",
     *      path="/api/classes/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la classe à récupérer",
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
        $data = Classe::where(["slug"=> $slug, "is_deleted" => false])->first();

        if (!$data) {
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }

        return response()->json(['message' => 'Classe trouvée', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Classes"},
     *     description="Modifie une classe et retourne la classe modifiée",
     *     path="/api/classes/{slug}",
     *     summary="Modification d'une classe",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label"},
     *             @OA\Property(property="label", type="string", example="6 ième"),
     *             @OA\Property(property="description", type="string", example="La classe de 6 ième")
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la classe à modifiée",
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
     *             @OA\Property(property="message", type="string", example="Classe modifiée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Slug validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Classe non trouvée"),
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
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $data = Classe::where("slug", $slug)->where("is_deleted",false)->first();

        if (!$data) {
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }

        $data->update([
            'label' => $request->input('label'),
            'description' => $request->input('description'),
        ]);

        return response()->json(['message' => 'Classe modifiée avec succès', 'data' => $data], 200);

    }

    /**
     * @OA\Delete(
     *      tags={"Classes"},
     *      summary="Supprime une classe par son slug",
     *      description="Retourne la classe supprimée",
     *      path="/api/classes/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="'Classe supprimée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Slug validation error",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Classe non trouvée"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la classe à récupérer",
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

        $data = Classe::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }


        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'Classe supprimée avec succès',"data" => $data]);
    }
}
