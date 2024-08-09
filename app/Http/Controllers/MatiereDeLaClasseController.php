<?php

namespace App\Http\Controllers;

use App\Models\MatiereDeLaClasse;
use App\Models\Classe;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MatiereDeLaClasseController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Matières de la classe"},
     *      summary="Liste des matières de la classe",
     *      description="Retourne la liste des matières de la classe",
     *      path="/api/matiere-de-la-classe",
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
        $data = MatiereDeLaClasse::where("is_deleted",false)->with("classe","matiere")->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucune matière de la classe trouvée'], 404);
        }

        return response()->json(['message' => 'Matières de la classe récupérées', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Matières de la classe"},
     *     description="Crée une nouvelle matière de la classe et retourne la matière de la classe créée",
     *     path="/api/matiere-de-la-classe",
     *     summary="Création d'une matière de la classe",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"classe","matiere"},
     *             @OA\Property(property="classe", type="string", example="slug de la classe"),
     *             @OA\Property(property="matiere", type="string", example="slug de la matière"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Matière de la classe créée avec succès"),
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
            'classe' => 'required|string|max:10',
            'matiere' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $classe = Classe::where(["slug" => $request->classe,"is_deleted" => false])->first();
        $matiere = Matiere::where(["slug" => $request->matiere,"is_deleted" => false])->first();

        if(!$classe){
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }

        if(!$matiere){
            return response()->json(['message' => 'Matiere non trouvée'], 404);
        }


        $data = MatiereDeLaClasse::create([
            'classe_id' => $classe->id,
            'matiere_id' => $matiere->id,
            'slug' => Str::random(10),
        ]);

        return response()->json(['message' => 'Matière de la classe créée avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Matières de la classe"},
     *      summary="Récupération d'une matière de la classe par son slug",
     *      description="Retourne une matière de la classe",
     *      path="/api/matiere-de-la-classe/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la Matière de la classe à récupérer",
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
        $data = MatiereDeLaClasse::where(["slug"=> $slug, "is_deleted" => false])->first();

        if (!$data) {
            return response()->json(['message' => 'Matière de la classe non trouvée'], 404);
        }

        return response()->json(['message' => 'Matière de la classe trouvée', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Matières de la classe"},
     *     description="Modifie une matière de la classe et retourne la matière de la classe modifiée",
     *     path="/api/matiere-de-la-classe/{slug}",
     *     summary="Modification d'une matière de la classe",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"classe","matiere"},
     *             @OA\Property(property="classe", type="string", example="slug de la classe"),
     *             @OA\Property(property="matiere", type="string", example="slug de la matiere"),
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la matière de la classe à modifiée",
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
     *             @OA\Property(property="message", type="string", example="Matière de la classe modifiée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Slug validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Matière de la classe non trouvée"),
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
            'classe' => 'required|string|max:10',
            'matiere' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $classe = Classe::where(["slug" => $request->classe,"is_deleted" => false])->first();
        $matiere = Matiere::where(["slug" => $request->matiere,"is_deleted" => false])->first();

        if(!$classe){
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }

        if(!$matiere){
            return response()->json(['message' => 'Matière non trouvée'], 404);
        }


        $data = MatiereDeLaClasse::where("slug", $slug)->where("is_deleted",false)->first();

        if (!$data) {
            return response()->json(['message' => 'Matière de la classe non trouvée'], 404);
        }

        $data->update([
            'classe_id' => $classe->id,
            'matiere_id' => $matiere->id,
        ]);

        return response()->json(['message' => 'Matière de la classe modifié avec succès', 'data' => $data], 200);

    }

    /**
     * @OA\Delete(
     *      tags={"Matières de la classe"},
     *      summary="Suppression d'une matière de la classe par son slug",
     *      description="Retourne la matière de la classe supprimée",
     *      path="/api/matiere-de-la-classe/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Matière de la classe supprimée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Slug validation error",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Matière de la classe non trouvée"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la matière de la classe à supprimer",
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

        $data = MatiereDeLaClasse::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'Matière de la classe non trouvée'], 404);
        }


        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'Matière de la classe supprimée avec succès',"data" => $data]);
    }

    /**
     * @OA\Get(
     *      tags={"Matières de la classe"},
     *      summary="Liste des matières de la classe",
     *      description="Retourne la liste des matières de la classe",
     *      path="/api/matiere-de-la-classe/classe/{slug}",
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la classe à supprimer",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *     @OA\PathItem (
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getMatiereDeLaClasseByClasseSlug($slug)
    {
        $classe = Classe::where([
                "slug" =>$slug
            ])->first();

        if (!$classe) {
            return response()->json(['message' => 'Aucune classe trouvée'], 404);
        }

        $data = MatiereDeLaClasse::where([
            "is_deleted" => false,
            "classe_id" => $classe->id,
        ])->with("classe","matiere")->get();

        return response()->json(['message' => 'Matières de la classe récupérées', 'data' => $data], 200);
    }
}
