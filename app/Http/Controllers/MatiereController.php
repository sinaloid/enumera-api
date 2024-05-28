<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MatiereController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Matieres"},
     *      summary="Liste des matières",
     *      description="Retourne la liste des matières",
     *      path="/api/matieres",
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
        $data = Matiere::where("is_deleted",false)->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucune matière trouvée'], 404);
        }

        return response()->json(['message' => 'Matières récupérées', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Matieres"},
     *     description="Crée une nouvelle matière et retourne la matière créée",
     *     path="/api/matieres",
     *     summary="Création d'une matière",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","abreviation"},
     *             @OA\Property(property="label", type="string", example="Histoire Gréographie"),
     *             @OA\Property(property="abreviation", type="string", example="HG"),
     *             @OA\Property(property="description", type="string", example="Histoire Gréographie")
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
            'abreviation' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }


        $data = Matiere::create([
            'label' => $request->input('label'),
            'abreviation' => $request->input('abreviation'),
            'description' => $request->input('description'),
            'slug' => Str::random(10),
        ]);

        return response()->json(['message' => 'Matières créée avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Matieres"},
     *      summary="Récupère une matière par son slug",
     *      description="Retourne une matière",
     *      path="/api/matieres/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la matière à récupérer",
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
        $data = Matiere::where(["slug"=> $slug, "is_deleted" => false])->first();

        if (!$data) {
            return response()->json(['message' => 'Matière non trouvée'], 404);
        }

        return response()->json(['message' => 'Matière trouvée', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Matieres"},
     *     description="Modifie une matière et retourne la matière modifiée",
     *     path="/api/matieres/{slug}",
     *     summary="Modification d'une matière",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","abreviation"},
     *             @OA\Property(property="label", type="string", example="Histoire Gréographie"),
     *             @OA\Property(property="abreviation", type="string", example="HG"),
     *             @OA\Property(property="description", type="string", example="Histoire Gréographie")
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la matière à modifiée",
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
     *             @OA\Property(property="message", type="string", example="Matière modifiée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Slug validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Matière non trouvée"),
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
            'abreviation' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $data = Matiere::where("slug", $slug)->where("is_deleted",false)->first();

        if (!$data) {
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }

        $data->update([
            'label' => $request->input('label'),
            'abreviation' => $request->input('abreviation'),
            'description' => $request->input('description'),
        ]);

        return response()->json(['message' => 'Matière modifiée avec succès', 'data' => $data], 200);

    }

    /**
     * @OA\Delete(
     *      tags={"Matieres"},
     *      summary="Suppression d'une matière par son slug",
     *      description="Retourne la matière supprimée",
     *      path="/api/matieres/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Matière supprimée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Slug validation error",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Matière non trouvée"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la matière à supprimer",
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

        $data = Matiere::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'Matière non trouvée'], 404);
        }


        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'Matière supprimée avec succès',"data" => $data]);
    }
}
