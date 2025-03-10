<?php

namespace App\Http\Controllers;

use App\Models\EvaluationLecon;
use App\Models\Lecon;
use App\Models\RessourceLecon;
use App\Models\RessourceEvaluationLecon;
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
        $data = EvaluationLecon::where("is_deleted",false)->with('lecon')->get();

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
     *             required={"label","abreviation","description","lecon"},
     *             @OA\Property(property="label", type="string", example="Intitulé de l'evaluation"),
     *             @OA\Property(property="type_de_correction", type="string", example="Correction automatique ou Correction manuelle"),
     *             @OA\Property(property="abreviation", type="string", example="Intitulé de l'evaluation"),
     *             @OA\Property(property="enonce", type="string", example="Enoncé de l'exercice"),
     *             @OA\Property(property="description", type="string", example="courte description"),
     *             @OA\Property(property="lecon", type="string", example="Slug de la leçon"),
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
            'enonce' => 'nullable|string|max:10000',
            'description' => 'required|string|max:255',
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
            'label' => $request->input('label'),
            'abreviation' => $request->input('abreviation'),
            'type_de_correction' => $request->input('type_de_correction'),
            'enonce' => $request->input('enonce'),
            'description' => $request->input('description'),
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
        $data = EvaluationLecon::where(["slug"=> $slug, "is_deleted" => false])->with([
            "question_lecons"  => function($query){
                $query->where('is_deleted',false);
            }
        ])->first();

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
     *             required={"label","abreviation","description","lecon"},
     *             @OA\Property(property="label", type="string", example="Intitulé de l'evaluation"),
     *             @OA\Property(property="type_de_correction", type="string", example="Correction automatique ou Correction manuelle"),
     *             @OA\Property(property="abreviation", type="string", example="Intitulé de l'evaluation"),
     *             @OA\Property(property="enonce", type="string", example="Enoncé de l'exercice"),
     *             @OA\Property(property="description", type="string", example="courte description"),
     *             @OA\Property(property="lecon", type="string", example="Slug de la leçon"),
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
            'label' => 'required|string|max:255',
            'abreviation' => 'required|string|max:255',
            'type_de_correction' => 'required|string|max:255',
            'enonce' => 'nullable|string|max:10000',
            'description' => 'required|string|max:255',
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
            'label' => $request->input('label'),
            'abreviation' => $request->input('abreviation'),
            'type_de_correction' => $request->input('type_de_correction'),
            'enonce' => $request->input('enonce'),
            'description' => $request->input('description'),
            'lecon_id' => $lecon->id,
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

    /**
     * @OA\Get(
     *      tags={"Evaluations leçons"},
     *      summary="Récupère la liste des evaluations en fonction du slug d'une leçon",
     *      description="Retourne la liste des evaluations",
     *      path="/api/evaluations-lecons/profile/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug du utilisateur à récupérer",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getEvaluationByLeconSlug($slug)
    {
        $lecon = Lecon::where([
            "slug" =>$slug,
        ])->first();
        if (!$lecon) {
            return response()->json(['message' => 'Aucune lecon trouvée'], 404);
        }
        $data = EvaluationLecon::where([
            "is_deleted" => false,
            "lecon_id" => $lecon->id,
        ])->with("question_lecons")->get();

        /*if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucune leçon trouvée'], 404);
        }*/

        return response()->json(['message' => 'leçons récupérées', 'data' => $data], 200);
    }



    public function getFile()
    {
        $ressources = RessourceEvaluationLecon::where("is_deleted",false)->get();

        return response()->json(['message' => 'Fichiers récupérés', 'data' => $ressources], 200);
    }

    public function getFileBySlug($slug)
    {
        $data = EvaluationLecon::where(["slug"=> $slug, "is_deleted" => false])->first();

        if (!$data) {
            return response()->json(['message' => 'Leçon non trouvée'], 404);
        }

        $ressources = RessourceEvaluationLecon::where([
            "is_deleted" => false,
            "evaluation_lecon_id" => $data->id
        ])->get();

        return response()->json(['message' => 'Fichiers récupérés', 'data' => $ressources], 200);
    }


    public function storeFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'evaluation' => 'required|string|max:255',
            //'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf,wav,mp3,mp4|max:20240', // 1 mega pour les images et les pdf
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $evaluationLecon = EvaluationLecon::where([
            "is_deleted" => false,
            "slug" => $request->evaluation
        ])->first();

        if (!$evaluationLecon) {
            return response()->json(['message' => 'Leçon non trouvée'], 404);
        }

        $filePaths = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $newFileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/uploads', $newFileName);
                // Enregistrer le fichier dans la base de données
                RessourceEvaluationLecon::create([
                    'original_name' => $file->getClientOriginalName(),
                    'name' => $newFileName,
                    'type' => $request->type,
                    'evaluation_lecon_id' => $evaluationLecon->id,
                    'slug' => Str::random(10),
                    'url' => Storage::url($path),
                ]);
            }
            return response()->json(['filePaths' => $filePaths, 'message' => "Fichiers enregistrés"], 201);

        }
        return response()->json(['filePaths' => $filePaths, 'message' => "Fichiers non enregistré"], 200);


    }
}
