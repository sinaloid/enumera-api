<?php

namespace App\Http\Controllers;

use App\Models\EvaluationLecon;
use App\Models\EvaluationLeconReponseEleve;
use App\Models\RessourceLecon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EvaluationLeconReponseEleveController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Evaluations leçons reponses élèves"},
     *      summary="Liste des réponses des élvès",
     *      description="Retourne la liste des réponses des élvès",
     *      path="/api/res-lecons-eleves",
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
        $data = EvaluationLeconReponseEleve::where("is_deleted",false)->orderBy('id','desc')->with('evaluationLecon','user')->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucune réponse trouvée'], 404);
        }

        return response()->json(['message' => 'Réponses récupérées', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Evaluations leçons reponses élèves"},
     *     description="Crée une nouvelle réponse d'un élève et retourne la réponse créée",
     *     path="/api/res-lecons-eleves",
     *     summary="Création d'une réponse",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_response","eleve","evaluation"},
     *             @OA\Property(property="user_response", type="string", example="reponse de l'eleve"),
     *             @OA\Property(property="evaluation", type="string", example="Slug de l'evaluation"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Réponse créée avec succès"),
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
            'user_response.*' => 'required',
            'evaluation' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $evaluationLecon = EvaluationLecon::where(["slug" => $request->evaluation,"is_deleted" => false])->first();
        if (!$evaluationLecon) {
            return response()->json(['message' => 'Evaluation leçon non trouvée'], 404);
        }

        $point_obtenu = 0;
        foreach ($request->user_response as $value) {
            $point_obtenu = $point_obtenu + floatval($value['user_point']);
        }

        $data = EvaluationLeconReponseEleve::create([
            'user_response' => json_encode($request->input('user_response')),
            'point_obtenu' => $point_obtenu,
            'evaluation_lecon_id' => $evaluationLecon->id,
            'user_id' => Auth()->user()->id,
            'slug' => Str::random(10),
        ]);

        return response()->json(['message' => 'Réponse créée avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Evaluations leçons reponses élèves"},
     *      summary="Récupère une evaluation par son slug",
     *      description="Retourne une evaluation",
     *      path="/api/res-lecons-eleves/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la réponse à récupérer",
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
        $data = EvaluationLeconReponseEleve::where(["slug"=> $slug, "is_deleted" => false])->with("evaluationLecon","user")->first();

        if (!$data) {
            return response()->json(['message' => 'Réponse non trouvée'], 404);
        }

        return response()->json(['message' => 'Réponse trouvée', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Evaluations leçons reponses élèves"},
     *     description="Modifie une evaluation et retourne la evaluation modifiée",
     *     path="/api/res-lecons-eleves/{slug}",
     *     summary="Modification d'une evaluation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_response"},
     *              @OA\Property(property="user_response", type="string", example="reponse de l'eleve"),
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
            'user_response.*' => 'required',
        ]);


        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $data = EvaluationLeconReponseEleve::where("slug", $slug)->where("is_deleted",false)->first();

        if (!$data) {
            return response()->json(['message' => 'evaluation non trouvée'], 404);
        }

        $point_obtenu = 0;
        foreach ($request->user_response as $value) {
            $point_obtenu = $point_obtenu + floatval($value['user_point']);
        }

        $data->update([
            'point_obtenu' => $point_obtenu,
            'user_response' => json_encode($request->input('user_response')),
        ]);

        return response()->json(['message' => 'modifiée avec succès', 'data' => $data], 200);

    }

    /**
     * @OA\Delete(
     *      tags={"Evaluations leçons reponses élèves"},
     *      summary="Suppression d'une réponse d'un élève par son slug",
     *      description="Retourne la réponse supprimée",
     *      path="/api/res-lecons-eleves/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Réponse supprimée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Slug validation error",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Réponse non trouvée"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la réponse à supprimer",
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

        $data = EvaluationLeconReponseEleve::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'Réponse non trouvée'], 404);
        }


        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'Réponse supprimée avec succès',"data" => $data]);
    }
}
