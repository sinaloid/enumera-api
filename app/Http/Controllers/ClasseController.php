<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Matiere;
use App\Models\MatiereDeLaClasse;
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
            return response()->json(['message' => 'Aucune classe trouvée'], 404);
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
     *          description="slug de la classe à supprimer",
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

    /**
     * @OA\Get(
     *      tags={"Classes"},
     *      summary="Récupération d'une classe par son slug avec la liste des matières de la classe",
     *      description="Retourne une matière de la classe",
     *      path="/api/classes/{slug}/matieres",
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
    public function getClasseMatiere($slug)
    {
        $data = Classe::where(["slug"=> $slug, "is_deleted" => false])->with([
            "matiereDeLaClasse" => function($query){
                $query->where([
                    "is_deleted" => false
                ]);
            },
            "matiereDeLaClasse.matiere" => function($query){
                $query->where([
                    "is_deleted" => false
                ]);
            }
        ])->first();

        if (!$data) {
            return response()->json(['message' => 'Matière de la classe non trouvée'], 404);
        }

        return response()->json(['message' => 'Matière de la classe trouvée', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Classes"},
     *      summary="Récupération d'une matière par son slug avec la liste des chapitres de la matière",
     *      description="Retourne une matière avec la liste des chapitres de la matière",
     *      path="/api/classes/{classe}/matieres/{matiere}/chapitres",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="classe",
     *          in="path",
     *          description="slug de la classe",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="matiere",
     *          in="path",
     *          description="slug de la Matière",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getClasseMatiereChapitres($classe, $matiere)
    {
        $dataClasse = Classe::where(["slug"=> $classe, "is_deleted" => false])->first();
        if (!$dataClasse) {
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }

        $dataMatiere = Matiere::where(["slug"=> $matiere, "is_deleted" => false])->first();
        if (!$dataMatiere) {
            return response()->json(['message' => 'Matière non trouvée'], 404);
        }

        $data = MatiereDeLaClasse::where([
            "classe_id"=> $dataClasse->id,
            "matiere_id"=> $dataMatiere->id,
            "is_deleted" => false
        ])->with([
            "chapitres" => function($query){
                $query->where([
                    "is_deleted" => false,
                ]);
            }
        ])->first();

        if (!$data) {
            return response()->json(['message' => 'Matière de la classe non trouvée'], 404);
        }

        $data['classe'] = $dataClasse;
        $data['matiere'] = $dataMatiere;
        return response()->json(['message' => 'Matière de la classe trouvée', 'data' => $data], 200);
    }
}
