<?php

namespace App\Http\Controllers;

use App\Models\EvaluationDevoir;
use App\Models\EvaluationMatiereDeLaClasse;
use App\Models\MatiereDeLaClasse;
use App\Models\RessourceLecon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EvaluationController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Evaluations"},
     *      summary="Liste des evaluations",
     *      description="Retourne la liste des evaluations",
     *      path="/api/evaluations",
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
        $data = EvaluationDevoir::where("is_deleted",false)->with('evaluationMatiereDeLaClasses.matiereDeLaClasse.matiere','evaluationMatiereDeLaClasses.matiereDeLaClasse.classe')->get();

        /*if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucune evaluation trouvée'], 404);
        }*/

        return response()->json(['message' => 'evaluations récupérées', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Evaluations"},
     *     description="Crée une nouvelle evaluation et retourne la evaluation créée",
     *     path="/api/evaluations",
     *     summary="Création d'une evaluation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","abreviation","description","lecon"},
     *             @OA\Property(property="label", type="string", example="Intitulé de l'evaluation"),
     *             @OA\Property(property="abreviation", type="string", example="Intitulé de l'evaluation"),
     *             @OA\Property(property="type_de_correction", type="string", example="Correction automatique ou Correction manuelle"),
     *             @OA\Property(property="date", type="string", example="20/9/2021"),
     *             @OA\Property(property="heure_debut", type="string", example="15:00"),
     *             @OA\Property(property="heure_fin", type="string", example="17:00"),
     *             @OA\Property(property="classe", type="string", example="slug de la classe"),
     *             @OA\Property(property="matiere", type="string", example="slug de la matière"),
     *             @OA\Property(property="description", type="string", example="courte description"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Evaluation créée avec succès"),
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
            'type_de_correction' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'classe' => 'required|string|max:10',
            //'matiere' => 'required|string|max:10',
            'matieres' => 'required|array',
            'matieres.*' => 'string|exists:matieres,slug', // Chaque classe doit être une chaîne valide et exister dans la table des classes
            'date' => 'required|string|max:10',
            'heure_debut' => 'required|string|max:10',
            'heure_fin' => 'required|string|max:10',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $data = EvaluationDevoir::create([
            'label' => $request->input('label'),
            'abreviation' => $request->input('abreviation'),
            'type_de_correction' => $request->input('type_de_correction'),
            'date' => $request->input('date'),
            'heure_debut' => $request->input('heure_debut'),
            'heure_fin' => $request->input('heure_fin'),
            'description' => $request->input('description'),
            //'matiere_de_la_classe_id' => $matiereDeLaClasse->id,
            'slug' => Str::random(10),
        ]);

        foreach ($request->matieres as $matiereSlug) {
            $matiereDeLaClasse = MatiereDeLaClasse::where(["is_deleted" => false])
            ->whereHas('classe', function($query) use ($request){
                $query->where([
                    'is_deleted' => false,
                    'slug' => $request->classe
                ]);
            })
            ->whereHas('matiere', function($query) use ($matiereSlug){
                $query->where([
                    'is_deleted' => false,
                    'slug' => $matiereSlug
                ]);
            })->first();
            EvaluationMatiereDeLaClasse::create([
                'slug' => Str::random(10),
                "evaluation_devoir_id" => $data->id,
                "matiere_de_la_classe_id" => $matiereDeLaClasse->id,
            ]);
            /*if (!$matiereDeLaClasse) {
                return response()->json(['message' => 'Matière de la classe non trouvée'], 404);
            }*/
        }

        return response()->json(['message' => 'Evaluation créée avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Evaluations"},
     *      summary="Récupère une evaluation par son slug",
     *      description="Retourne une evaluation",
     *      path="/api/evaluations/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la evaluation à récupérer",
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
        $data = EvaluationDevoir::where(["slug"=> $slug, "is_deleted" => false])->with('questions','matiereDeLaClasse.matiere','matiereDeLaClasse.classe')->first();

        if (!$data) {
            return response()->json(['message' => 'evaluation non trouvée'], 404);
        }

        return response()->json(['message' => 'evaluation trouvée', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Evaluations"},
     *     description="Modifie une evaluation et retourne la evaluation modifiée",
     *     path="/api/evaluations/{slug}",
     *     summary="Modification d'une evaluation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","abreviation","description","lecon"},
     *             @OA\Property(property="label", type="string", example="Intitulé de l'evaluation"),
     *             @OA\Property(property="abreviation", type="string", example="Intitulé de l'evaluation"),
     *             @OA\Property(property="type_de_correction", type="string", example="Correction automatique ou Correction manuelle"),
     *             @OA\Property(property="date", type="string", example="20/9/2021"),
     *             @OA\Property(property="heure_debut", type="string", example="15:00"),
     *             @OA\Property(property="heure_fin", type="string", example="17:00"),
     *             @OA\Property(property="classe", type="string", example="slug de la classe"),
     *             @OA\Property(property="matiere", type="string", example="slug de la matière"),
     *             @OA\Property(property="description", type="string", example="courte description"),
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la evaluation à modifiée",
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
     *             @OA\Property(property="message", type="string", example="evaluation modifiée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Slug validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="evaluation non trouvée"),
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
        // Validation des données
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'abreviation' => 'required|string|max:255',
            'type_de_correction' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'classe' => 'required|string|max:10',
            'matieres' => 'required|array',
            'matieres.*' => 'string|exists:matieres,slug',
            'date' => 'required|string|max:10',
            'heure_debut' => 'required|string|max:10',
            'heure_fin' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        // Récupération de l'évaluation
        $evaluation = EvaluationDevoir::where([
            'slug' => $slug,
            'is_deleted' => false
        ])->first();
        if (!$evaluation) {
            return response()->json(['message' => 'Évaluation non trouvée'], 404);
        }

        // Mise à jour des informations de l'évaluation
        $evaluation->update([
            'label' => $request->input('label'),
            'abreviation' => $request->input('abreviation'),
            'type_de_correction' => $request->input('type_de_correction'),
            'date' => $request->input('date'),
            'heure_debut' => $request->input('heure_debut'),
            'heure_fin' => $request->input('heure_fin'),
            'description' => $request->input('description'),
        ]);

        // Supprimer les matières existantes associées
        EvaluationMatiereDeLaClasse::where('evaluation_devoir_id', $evaluation->id)->delete();

        // Réassigner les nouvelles matières
        foreach ($request->matieres as $matiereSlug) {
            $matiereDeLaClasse = MatiereDeLaClasse::where(["is_deleted" => false])
                ->whereHas('classe', function($query) use ($request) {
                    $query->where([
                        'is_deleted' => false,
                        'slug' => $request->classe
                    ]);
                })
                ->whereHas('matiere', function($query) use ($matiereSlug) {
                    $query->where([
                        'is_deleted' => false,
                        'slug' => $matiereSlug
                    ]);
                })->first();

            if ($matiereDeLaClasse) {
                EvaluationMatiereDeLaClasse::create([
                    'slug' => Str::random(10),
                    "evaluation_devoir_id" => $evaluation->id,
                    "matiere_de_la_classe_id" => $matiereDeLaClasse->id,
                ]);
            } else {
                return response()->json(['message' => "Matière de la classe non trouvée pour le slug $matiereSlug"], 404);
            }
        }

        return response()->json(['message' => 'Évaluation mise à jour avec succès', 'data' => $evaluation], 200);
    }


    /**
     * @OA\Delete(
     *      tags={"Evaluations"},
     *      summary="Suppression d'une evaluation par son slug",
     *      description="Retourne la evaluation supprimée",
     *      path="/api/evaluations/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="evaluation supprimée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Slug validation error",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="evaluation non trouvée"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la evaluation à supprimer",
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

        $data = EvaluationDevoir::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'evaluation non trouvée'], 404);
        }


        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'evaluation supprimée avec succès',"data" => $data]);
    }

    /**
     * @OA\Get(
     *      tags={"Evaluations"},
     *      summary="Récupère la liste des evaluations en fonction d'une classe et d'une matière",
     *      description="Retourne la liste des evaluations",
     *      path="/api/evaluations/classe/{slugClasse}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
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
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getEvaluationByClasse($slugClasse)
    {
        $data = EvaluationDevoir::where([
            "is_deleted" => false,
        ])
        ->whereHas('matiereDeLaClasse', function($query) use ($slugClasse){
            $query->where([
                'is_deleted' => false,
            ])->whereHas('classe', function($query) use ($slugClasse){
                $query->where([
                    'is_deleted' => false,
                    'slug' => $slugClasse
                ]);
            });
        })
        ->with("questions")->get();

        /*if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucune leçon trouvée'], 404);
        }*/

        return response()->json(['message' => 'Evaluations récupérées', 'data' => $data], 200);
    }
}
