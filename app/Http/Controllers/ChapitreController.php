<?php

namespace App\Http\Controllers;

use App\Models\Chapitre;
use App\Models\Periode;
use App\Models\MatiereDeLaClasse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ChapitreController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Chapitres"},
     *      summary="Liste des chapitres",
     *      description="Retourne la liste des chapitres",
     *      path="/api/chapitres",
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
        $data = Chapitre::where("is_deleted",false)->with("matiereDeLaClasse.matiere","matiereDeLaClasse.classe")->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucun chapitre trouvé'], 404);
        }

        return response()->json(['message' => 'Chapitres récupérés', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Chapitres"},
     *     description="Crée un nouveau chapitre et retourne le chapitre créé",
     *     path="/api/chapitres",
     *     summary="Création d'un chapitre",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","periode","matiereClasse"},
     *             @OA\Property(property="label", type="string", example="Histoire du Burkina Faso"),
     *             @OA\Property(property="periode", type="string", example="slug de la periode"),
     *             @OA\Property(property="matiereClasse", type="string", example="slug de la matiereClasse"),
     *             @OA\Property(property="abreviation", type="string", example="abreviation du chapitre"),
     *             @OA\Property(property="description", type="string", example="Lorem ipsum ipsom lores")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Chapitre créé avec succès"),
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
            'periode' => 'required|string|max:10',
            'matiereClasse' => 'required|string|max:10',
            'abreviation' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $periode = Periode::where(["slug" => $request->periode,"is_deleted" => false])->first();
        $matiereClasse = MatiereDeLaClasse::where(["slug" => $request->matiereClasse,"is_deleted" => false])->first();

        if(!$periode){
            return response()->json(['message' => 'Periode non trouvée'], 404);
        }

        if(!$matiereClasse){
            return response()->json(['message' => 'Matiere de la classe non trouvée'], 404);
        }


        $data = Chapitre::create([
            'label' => $request->input('label'),
            'periode_id' => $periode->id,
            'matiere_de_la_classe_id' => $matiereClasse->id,
            'abreviation' => $request->input('abreviation'),
            'description' => $request->input('description'),
            'slug' => Str::random(10),
        ]);

        return response()->json(['message' => 'Chapitre créé avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Chapitres"},
     *      summary="Récupération d'un chapitre par son slug",
     *      description="Retourne un chapitre",
     *      path="/api/chapitres/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug du chapitre à récupérer",
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
        $data = Chapitre::where(["slug"=> $slug, "is_deleted" => false])->first();

        if (!$data) {
            return response()->json(['message' => 'Chapitre non trouvé'], 404);
        }

        return response()->json(['message' => 'Chapitre trouvé', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Chapitres"},
     *     description="Modifie un chapitre et retourne le chapitre modifié",
     *     path="/api/chapitres/{slug}",
     *     summary="Modification d'un chapitre",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","periode","matiereClasse"},
     *             @OA\Property(property="label", type="string", example="Histoire du Burkina Faso"),
     *             @OA\Property(property="periode", type="string", example="slug de la periode"),
     *             @OA\Property(property="matiereClasse", type="string", example="slug de la matiereClasse"),
     *             @OA\Property(property="abreviation", type="string", example="abreviation du chapitre"),
     *             @OA\Property(property="description", type="string", example="Lorem ipsum ipsom lores")
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug du chapitre à modifié",
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
     *             @OA\Property(property="message", type="string", example="Chapitre modifié avec succès"),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Slug validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Chapitre non trouvé"),
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
            'periode' => 'required|string|max:10',
            'matiereClasse' => 'required|string|max:10',
            'abreviation' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $periode = Periode::where(["slug" => $request->periode,"is_deleted" => false])->first();
        $matiereClasse = MatiereDeLaClasse::where(["slug" => $request->matiereClasse,"is_deleted" => false])->first();

        if(!$periode){
            return response()->json(['message' => 'Periode non trouvée'], 404);
        }

        if(!$matiereClasse){
            return response()->json(['message' => 'Matière de la classe non trouvée'], 404);
        }


        $data = Chapitre::where("slug", $slug)->where("is_deleted",false)->first();

        if (!$data) {
            return response()->json(['message' => 'Chapitre non trouvé'], 404);
        }

        $data->update([
            'label' => $request->input('label'),
            'periode_id' => $periode->id,
            'matiere_de_la_classe' => $matiereClasse->id,
            'abreviation' => $request->input('abreviation'),
            'description' => $request->input('description'),
        ]);

        return response()->json(['message' => 'Chapitre modifié avec succès', 'data' => $data], 200);

    }

    /**
     * @OA\Delete(
     *      tags={"Chapitres"},
     *      summary="Suppression d'un chapitre par son slug",
     *      description="Retourne le chapitre supprimé",
     *      path="/api/chapitres/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Chapitre supprimé avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Slug validation error",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Chapitre non trouvé"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug du chapitre à supprimer",
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

        $data = Chapitre::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'Chapitre non trouvé'], 404);
        }


        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'Chapitre supprimé avec succès',"data" => $data]);
    }
}
