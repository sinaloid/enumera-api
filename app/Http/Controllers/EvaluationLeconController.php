<?php

namespace App\Http\Controllers;

use App\Models\EvaluationLecon;
use App\Models\Lecon;
use App\Models\RessourceLecon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EvaluationLeconController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Evaluations leçons"},
     *      summary="Liste des evaluations",
     *      description="Retourne la liste des evaluations",
     *      path="/api/evaluations-lecons",
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
        $data = EvaluationLecon::where("is_deleted",false)->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucune evaluation trouvée'], 404);
        }

        return response()->json(['message' => 'evaluations récupérées', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Evaluations leçons"},
     *     description="Crée une nouvelle evaluation et retourne la evaluation créée",
     *     path="/api/evaluations-lecons",
     *     summary="Création d'une evaluation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question","choix","type","lecon"},
     *             @OA\Property(property="question", type="string", example="Quel est un principe d'AgilePM ?"),
     *             @OA\Property(property="choix", type="string", example="Communiquer souvent;Communiquer verbalement;Communiquer de manière formelle;Communiquer de façon continue et claire"),
     *             @OA\Property(property="reponses", type="string", example="1;2"),
     *             @OA\Property(property="point", type="string", example="1"),
     *             @OA\Property(property="lecon", type="string", example="Slug de la leçon"),
     *             @OA\Property(property="type", type="string", example="CHOIX_MULTIPLE")
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
            'question' => 'required|string|max:255',
            'choix' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'reponses' => 'required|string|max:255',
            'point' => 'required|string|max:255',
            'lecon' => 'required|string|max:10',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $lecon = Lecon::where(["slug" => $request->lecon,"is_deleted" => false])->first();
        if (!$lecon) {
            return response()->json(['message' => 'Leçon non trouvée'], 404);
        }

        $data = EvaluationLecon::create([
            'question' => $request->input('question'),
            'choix' => $request->input('choix'),
            'type' => $request->input('type'),
            'reponses' => $request->input('reponses'),
            'point' => $request->input('point'),
            'lecon_id' => $lecon->id,
            'slug' => Str::random(10),
        ]);

        return response()->json(['message' => 'Evaluation créée avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Evaluations leçons"},
     *      summary="Récupère une evaluation par son slug",
     *      description="Retourne une evaluation",
     *      path="/api/evaluations-lecons/{slug}",
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
        $data = EvaluationLecon::where(["slug"=> $slug, "is_deleted" => false])->first();

        if (!$data) {
            return response()->json(['message' => 'evaluation non trouvée'], 404);
        }

        return response()->json(['message' => 'evaluation trouvée', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Evaluations leçons"},
     *     description="Modifie une evaluation et retourne la evaluation modifiée",
     *     path="/api/evaluations-lecons/{slug}",
     *     summary="Modification d'une evaluation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question","choix","type","lecon"},
     *             @OA\Property(property="question", type="string", example="Quel est un principe d'AgilePM ?"),
     *             @OA\Property(property="choix", type="string", example="Communiquer souvent;Communiquer verbalement;Communiquer de manière formelle;Communiquer de façon continue et claire"),
     *             @OA\Property(property="reponses", type="string", example="2;3"),
     *             @OA\Property(property="point", type="string", example="1"),
     *             @OA\Property(property="lecon", type="string", example="Slug de la leçon"),
     *             @OA\Property(property="type", type="string", example="CHOIX_MULTIPLE")
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
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'choix' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'reponses' => 'required|string|max:255',
            'point' => 'required|string|max:255',
            'lecon' => 'required|string|max:10',

        ]);


        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $lecon = Lecon::where(["slug" => $request->lecon,"is_deleted" => false])->first();
        if (!$lecon) {
            return response()->json(['message' => 'Leçon non trouvée'], 404);
        }


        $data = EvaluationLecon::where("slug", $slug)->where("is_deleted",false)->first();

        if (!$data) {
            return response()->json(['message' => 'evaluation non trouvée'], 404);
        }

        $data->update([
            'question' => $request->input('question'),
            'choix' => $request->input('choix'),
            'type' => $request->input('type'),
            'lecon_id' => $lecon->id,
            'reponses' => $request->input('reponses'),
            'point' => $request->input('point'),
        ]);

        return response()->json(['message' => 'evaluation modifiée avec succès', 'data' => $data], 200);

    }

    /**
     * @OA\Delete(
     *      tags={"Evaluations leçons"},
     *      summary="Suppression d'une evaluation par son slug",
     *      description="Retourne la evaluation supprimée",
     *      path="/api/evaluations-lecons/{slug}",
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

        $data = EvaluationLecon::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'evaluation non trouvée'], 404);
        }


        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'evaluation supprimée avec succès',"data" => $data]);
    }

    /**
     * @OA\Post(
     *     tags={"Evaluations leçons"},
     *     description="Importe une liste de qcm",
     *     path="/api/evaluations-lecons/import",
     *     summary="Création d'une evaluation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question","choix","type","lecon"},
     *             @OA\Property(property="lecon", type="string", example="Slug de la leçon"),
     *             @OA\Property(property="qcm", type="string", example="fichier excel")
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
    public function storeExcel(Request $request){

        $validator = Validator::make($request->all(), [
            'lecon' => 'required|string|max:10',
            'qcm' => 'required|max:10000|mimes:csv,xlsx',
        ]);

        //dd(Str::slug($request->titre));
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $lecon = Lecon::where(["slug" => $request->lecon,"is_deleted" => false])->first();
        if (!$lecon) {
            return response()->json(['message' => 'Leçon non trouvée'], 404);
        }

        $qcmName = Str::random(10) . '.' . $request->qcm->getClientOriginalExtension();

            // Enregistrer l'image dans le dossier public/images
            $importPath = $request->qcm->move(public_path('excels'), $qcmName);

            if ($importPath) {
                Excel::import(new EvaluationLeconImport($lecon),'excels/' . $qcmName);
            }else{
                return response()->json(['error' => "Échec lors de l'enregistrement des données"], 422);

            }

        return response()->json(['message' => 'Produits importés avec succès', 'data' => "ok"], 200);
    }
}
