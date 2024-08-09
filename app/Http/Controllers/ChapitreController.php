<?php

namespace App\Http\Controllers;

use App\Models\Chapitre;
use App\Models\Periode;
use App\Models\MatiereDeLaClasse;
use App\Models\Classe;
use App\Models\Matiere;
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
    public function index(Request $request)
    {
        $data = Chapitre::where("is_deleted",false)->with("matiereDeLaClasse.matiere","matiereDeLaClasse.classe","periode")->get();

        if($request->classe && $request->matiere){

            $classe = Classe::where([
                "slug" =>$request->classe,
            ])->first();
            $classe = isset($classe) ? $classe->id:"";

            $matiere = Matiere::where([
                "slug" =>$request->matiere,
            ])->first();
            $matiere = isset($matiere) ? $matiere->id:"";

            $classeMatiere = MatiereDeLaClasse::where([
                "is_deleted" => false,
                "classe_id" => $classe,
                "matiere_id" => $matiere,

            ])->with("classe","matiere")->first();
            $classeMatiere = isset($classeMatiere) ? $classeMatiere->id:"";


        $data = Chapitre::where([
            "is_deleted" => false,
            "matiere_de_la_classe_id" => $classeMatiere,

        ])->with("matiereDeLaClasse.matiere","matiereDeLaClasse.classe","periode")->get();

        }

        /*if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucun chapitre trouvé'], 404);
        }*/

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
     *             @OA\Property(property="matiere", type="string", example="slug de la matiere"),
     *             @OA\Property(property="classe", type="string", example="slug de la classe"),
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
            'matiere' => 'required|string|max:10',
            'classe' => 'required|string|max:10',
            'abreviation' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $matiere = Matiere::where(["slug" => $request->matiere,"is_deleted" => false])->first();
        if(!$matiere){
            return response()->json(['message' => 'Matière non trouvée'], 404);
        }
        $classe = Classe::where(["slug" => $request->classe,"is_deleted" => false])->first();
        if(!$classe){
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }


        $periode = Periode::where(["slug" => $request->periode,"is_deleted" => false])->first();
        $matiereClasse = MatiereDeLaClasse::where([
            "classe_id" => $classe->id,
            "matiere_id" => $matiere->id,
            "is_deleted" => false
        ])->first();

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
            'matiere' => 'required|string|max:10',
            'classe' => 'required|string|max:10',
            'abreviation' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $matiere = Matiere::where(["slug" => $request->matiere,"is_deleted" => false])->first();
        if(!$matiere){
            return response()->json(['message' => 'Matière non trouvée'], 404);
        }
        $classe = Classe::where(["slug" => $request->classe,"is_deleted" => false])->first();
        if(!$classe){
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }

        $periode = Periode::where(["slug" => $request->periode,"is_deleted" => false])->first();
        $matiereClasse = MatiereDeLaClasse::where([
            "classe_id" => $classe->id,
            "matiere_id" => $matiere->id,
            "is_deleted" => false
        ])->first();

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


    /**
     * @OA\Get(
     *      tags={"Chapitres"},
     *      summary="Récupération la liste des chapitres d'une matière en fonction d'une classe et d'une periode",
     *      description="Retourne la liste des chapitres chapitres d'une matière en fonction d'une classe et d'une periode",
     *      path="/api/chapitres/{slugMatiere}/{slugClasse}/{slugPeriode}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slugMatiere",
     *          in="path",
     *          description="slug de la matière",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="slugClasse",
     *          in="path",
     *          description="slug de la classe",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="slugPeriode",
     *          in="path",
     *          description="slug de la periode",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function chapitreMatiereClasse($slugMatiere, $slugClasse,$slugPeriode)
    {
        $matiere = Matiere::where(["slug"=> $slugMatiere, "is_deleted" => false])->first();

        if (!$matiere) {
            return response()->json(['message' => 'Matière non trouvé'], 404);
        }

        $classe = Classe::where(["slug"=> $slugClasse, "is_deleted" => false])->first();

        if (!$classe) {
            return response()->json(['message' => 'Classe non trouvé'], 404);
        }

        $matiereClasse = MatiereDeLaClasse::where([
            "matiere_id"=> $matiere->id,
            "classe_id"=> $classe->id,
            "is_deleted" => false
        ])->first();

        if (!$matiereClasse) {
            return response()->json(['message' => "La matière n'existe pas dans la classe "], 404);
        }

        $periode = Periode::where(["slug"=> $slugPeriode, "is_deleted" => false])->first();

        if (!$periode) {
            return response()->json(['message' => 'Periode non trouvée'], 404);
        }

        $data = Chapitre::where([
            "matiere_de_la_classe_id"=> $matiereClasse->id,
            "periode_id"=> $periode->id,
            "is_deleted" => false
            ])->get();

        /*if (!$data) {
            return response()->json(['message' => 'Classe non trouvé'], 404);
        }*/
        // qz3Vvi26KY/1Dk3XSAPFg/Qhxt0fyHi3 /AYMVxEB8uZ
        return response()->json(['message' => 'Chapitres trouvée', 'data' => $data], 200);
    }
}
