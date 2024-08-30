<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Lecon;
use App\Models\Evaluation;
use App\Models\RessourceLecon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Imports\QuestionImport;
use Excel;
class QuestionController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Questions"},
     *      summary="Liste des questions",
     *      description="Retourne la liste des questions",
     *      path="/api/questions",
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
        $data = Question::where("is_deleted",false)->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucune question trouvée'], 404);
        }

        return response()->json(['message' => 'questions récupérées', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Questions"},
     *     description="Crée une nouvelle question et retourne la question créée",
     *     path="/api/questions",
     *     summary="Création d'une question",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question","choix","type","lecon"},
     *             @OA\Property(property="question", type="string", example="Quel est un principe d'AgilePM ?"),
     *             @OA\Property(property="choix", type="string", example="Communiquer souvent;Communiquer verbalement;Communiquer de manière formelle;Communiquer de façon continue et claire"),
     *             @OA\Property(property="reponses", type="string", example="1;2"),
     *             @OA\Property(property="point", type="string", example="1"),
     *             @OA\Property(property="evaluation", type="string", example="Slug de l'evaluation"),
     *             @OA\Property(property="type", type="string", example="CHOIX_MULTIPLE")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="question créée avec succès"),
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
            'question' => 'required|string|max:255',
            'choix' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'reponses' => 'required|string|max:255',
            'point' => 'required|string|max:255',
            'evaluation' => 'required|string|max:10',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $evaluation = Evaluation::where(["slug" => $request->evaluation,"is_deleted" => false])->first();
        if (!$evaluation) {
            return response()->json(['message' => 'Evaluation non trouvée'], 404);
        }

        $data = Question::create([
            'question' => $request->input('question'),
            'choix' => $request->input('choix'),
            'type' => $request->input('type'),
            'reponses' => $request->input('reponses'),
            'point' => $request->input('point'),
            'evaluation_id' => $evaluation->id,
            'slug' => Str::random(10),
        ]);

        return response()->json(['message' => 'question créée avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Questions"},
     *      summary="Récupère une question par son slug",
     *      description="Retourne une question",
     *      path="/api/questions/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la question à récupérer",
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
        $data = Question::where(["slug"=> $slug, "is_deleted" => false])->first();

        if (!$data) {
            return response()->json(['message' => 'question non trouvée'], 404);
        }

        return response()->json(['message' => 'question trouvée', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Questions"},
     *     description="Modifie une question et retourne la question modifiée",
     *     path="/api/questions/{slug}",
     *     summary="Modification d'une question",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question","choix","type","lecon"},
     *             @OA\Property(property="question", type="string", example="Quel est un principe d'AgilePM ?"),
     *             @OA\Property(property="choix", type="string", example="Communiquer souvent;Communiquer verbalement;Communiquer de manière formelle;Communiquer de façon continue et claire"),
     *             @OA\Property(property="reponses", type="string", example="2;3"),
     *             @OA\Property(property="point", type="string", example="1"),
     *             @OA\Property(property="evaluation", type="string", example="Slug de la levaluation_lecon"),
     *             @OA\Property(property="type", type="string", example="CHOIX_MULTIPLE")
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la question à modifiée",
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
     *             @OA\Property(property="message", type="string", example="question modifiée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Slug validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="question non trouvée"),
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
            'question' => 'required|string|max:255',
            'choix' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'reponses' => 'required|string|max:255',
            'point' => 'required|string|max:255',
            'evaluation' => 'required|string|max:10',

        ]);


        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $evaluation = Evaluation::where(["slug" => $request->evaluation,"is_deleted" => false])->first();
        if (!$evaluation) {
            return response()->json(['message' => 'Evaluation non trouvée'], 404);
        }


        $data = Question::where("slug", $slug)->where("is_deleted",false)->first();

        if (!$data) {
            return response()->json(['message' => 'question non trouvée'], 404);
        }

        $data->update([
            'question' => $request->input('question'),
            'choix' => $request->input('choix'),
            'type' => $request->input('type'),
            'evaluation_id' => $evaluation->id,
            'reponses' => $request->input('reponses'),
            'point' => $request->input('point'),
        ]);

        return response()->json(['message' => 'question modifiée avec succès', 'data' => $data], 200);

    }

    /**
     * @OA\Delete(
     *      tags={"Questions"},
     *      summary="Suppression d'une question par son slug",
     *      description="Retourne la question supprimée",
     *      path="/api/questions/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="question supprimée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Slug validation error",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="question non trouvée"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la question à supprimer",
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

        $data = Question::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'question non trouvée'], 404);
        }


        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'question supprimée avec succès',"data" => $data]);
    }

    /**
     * @OA\Post(
     *     tags={"Questions"},
     *     description="Importe une liste de qcm",
     *     path="/api/questions/import",
     *     summary="Création d'une question",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question","choix","type","lecon"},
     *             @OA\Property(property="evaluation_lecon", type="string", example="Slug de l'evaluation_lecon"),
     *             @OA\Property(property="qcm", type="string", example="fichier excel")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="question créée avec succès"),
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
    public function storeExcel(Request $request){

        $validator = Validator::make($request->all(), [
            'evaluation' => 'required|string|max:10',
            'qcm' => 'required|max:10000|mimes:csv,xlsx',
        ]);

        //dd(Str::slug($request->titre));
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $evaluation = Evaluation::where(["slug" => $request->evaluation,"is_deleted" => false])->first();
        if (!$evaluation) {
            return response()->json(['message' => 'Evaluation leçon non trouvée'], 404);
        }

        //dd($evaluation_lecon);

        $qcmName = Str::random(10) . '.' . $request->qcm->getClientOriginalExtension();

            // Enregistrer l'image dans le dossier public/images
            $importPath = $request->qcm->move(public_path('excels'), $qcmName);
            //dd($evaluation_lecon);
            if ($importPath) {
                Excel::import(new QuestionImport($evaluation),'excels/' . $qcmName);
            }else{
                return response()->json(['error' => "Échec lors de l'enregistrement des données"], 422);

            }

        return response()->json(['message' => 'Questions importés avec succès', 'data' => "ok"], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Questions"},
     *      summary="Récupère la liste des question en fonction d'une evaluation",
     *      description="Retourne une question",
     *      path="/api/questions/evaluation/{slugEvaluation}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slugEvaluation",
     *          in="path",
     *          description="slug de l'évaluation",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getQuestionByEvaluation($slugEvaluation)
    {
        $data = Question::where(["is_deleted" => false])
        ->whereHas('evaluation', function($query) use ($slugEvaluation){
            $query->where([
                'is_deleted' => false,
                'slug' => $slugEvaluation
            ]);
        })
        ->get();

        return response()->json(['message' => 'question trouvée', 'data' => $data], 200);
    }
}
