<?php

namespace App\Http\Controllers;

use App\Models\Lecon;
use App\Models\Chapitre;
use App\Models\RessourceLecon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LeconController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Leçons"},
     *      summary="Liste des leçons",
     *      description="Retourne la liste des leçons",
     *      path="/api/lecons",
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
        $data = Lecon::where("is_deleted",false)->with("chapitre.periode","chapitre.matiereDeLaClasse.matiere","chapitre.matiereDeLaClasse.classe")->get();

        if($request->chapitre){

            $chapitre = Chapitre::where([
                "slug" =>$request->chapitre,
            ])->first();
            $chapitre = isset($chapitre) ? $chapitre->id:"";
            $data = Lecon::where([
                "is_deleted" => false,
                "chapitre_id" => $chapitre,
            ])->with("chapitre.periode","chapitre.matiereDeLaClasse.matiere","chapitre.matiereDeLaClasse.classe")->get();
        }

        /*if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucune leçon trouvée'], 404);
        }*/

        return response()->json(['message' => 'leçons récupérées', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Leçons"},
     *     description="Crée une nouvelle leçon et retourne la leçon créée",
     *     path="/api/lecons",
     *     summary="Création d'une leçon",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","abreviation","type","chapitre"},
     *             @OA\Property(property="label", type="string", example="Histoire des royaumes moose"),
     *             @OA\Property(property="abreviation", type="string", example="Histoire des royaumes moose"),
     *             @OA\Property(property="chapitre", type="string", example="Slug du chapitre"),
     *             @OA\Property(property="description", type="string", example="Lorem ipsum lores ipomd")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Leçon créée avec succès"),
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
            'chapitre' => 'required|string|max:10',
            'abreviation' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $chapitre = Chapitre::where(["slug" => $request->chapitre,"is_deleted" => false])->first();
        if (!$chapitre) {
            return response()->json(['message' => 'Chapitre non trouvé'], 404);
        }

        $data = Lecon::create([
            'label' => $request->input('label'),
            'abreviation' => $request->input('abreviation'),
            'chapitre_id' => $chapitre->id,
            'description' => $request->input('description'),
            'slug' => Str::random(10),
        ]);

        return response()->json(['message' => 'Leçon créée avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Leçons"},
     *      summary="Récupère une leçon par son slug",
     *      description="Retourne une leçon",
     *      path="/api/lecons/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la leçon à récupérer",
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
        $data = Lecon::where(["slug"=> $slug, "is_deleted" => false])->with("chapitre.periode","chapitre.matiereDeLaClasse.matiere","chapitre.matiereDeLaClasse.classe","cours","evaluations_lecons")->first();

        if (!$data) {
            return response()->json(['message' => 'Leçon non trouvée'], 404);
        }

        return response()->json(['message' => 'Leçon trouvée', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Leçons"},
     *     description="Modifie une leçon et retourne la leçon modifiée",
     *     path="/api/lecons/{slug}",
     *     summary="Modification d'une leçon",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","abreviation","chapitre"},
     *             @OA\Property(property="label", type="string", example="Histoire des royaumes moose"),
     *             @OA\Property(property="abreviation", type="string", example="Histoire des royaumes moose"),
     *             @OA\Property(property="chapitre", type="string", example="Slug du chapitre"),
     *             @OA\Property(property="description", type="string", example="Lorem ipsum lores ipomd")
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la leçon à modifiée",
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
     *             @OA\Property(property="message", type="string", example="Leçon modifiée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Slug validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Leçon non trouvée"),
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
            'chapitre' => 'required|string|max:10',
            'abreviation' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $chapitre = Chapitre::where(["slug" => $request->chapitre,"is_deleted" => false])->first();
        if (!$chapitre) {
            return response()->json(['message' => 'Chapitre non trouvé'], 404);
        }


        $data = Lecon::where("slug", $slug)->where("is_deleted",false)->first();

        if (!$data) {
            return response()->json(['message' => 'Leçon non trouvée'], 404);
        }

        $data->update([
            'label' => $request->input('label'),
            'chapitre_id' => $chapitre->id,
            'abreviation' => $request->input('abreviation'),
            'description' => $request->input('description'),
        ]);

        return response()->json(['message' => 'Leçon modifiée avec succès', 'data' => $data], 200);

    }

    /**
     * @OA\Delete(
     *      tags={"Leçons"},
     *      summary="Suppression d'une leçon par son slug",
     *      description="Retourne la leçon supprimée",
     *      path="/api/lecons/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="leçon supprimée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Slug validation error",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="leçon non trouvée"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la leçon à supprimer",
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

        $data = Lecon::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'Leçon non trouvée'], 404);
        }


        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'Leçon supprimée avec succès',"data" => $data]);
    }

    /**
     * @OA\Get(
     *      tags={"Leçons"},
     *      summary="Récupère la liste des leçons d'un chapitre",
     *      description="Retourne la liste des leçons",
     *      path="/api/lecons/chapitre/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug du chapitre",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function leconChapitre($slug)
    {
        $chapitre = Chapitre::where(["slug"=> $slug, "is_deleted" => false])->first();

        if (!$chapitre) {
            return response()->json(['message' => 'Chapitre non trouvée'], 404);
        }

        $data = Lecon::where(["chapitre_id"=> $chapitre->id, "is_deleted" => false])->get();

        if (!$data) {
            return response()->json(['message' => 'Chapitre non trouvée'], 404);
        }

        return response()->json(['message' => 'Leçon trouvée', 'data' => $data], 200);
    }

    public function getFile()
    {
        $ressources = RessourceLecon::where("is_deleted",false)->get();

        return response()->json(['message' => 'Fichiers récupérés', 'data' => $ressources], 200);
    }

    public function getLeconFile($slug)
    {
        $data = Lecon::where(["slug"=> $slug, "is_deleted" => false])->first();

        if (!$data) {
            return response()->json(['message' => 'Leçon non trouvée'], 404);
        }

        $ressources = RessourceLecon::where([
            "is_deleted" => false,
            "lecon_id" => $data->id
        ])->get();

        return response()->json(['message' => 'Fichiers récupérés', 'data' => $ressources], 200);
    }


    public function storeFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'lecon' => 'required|string|max:255',
            'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf,wav,mp3,mp4|max:20240', // 1 mega pour les images et les pdf
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $lecon = Lecon::where([
            "is_deleted" => false,
            "slug" => $request->lecon
        ])->first();

        if (!$lecon) {
            return response()->json(['message' => 'Leçon non trouvée'], 404);
        }

        $filePaths = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                //$path = $file->store('uploads');
                //$filePaths[] = $path;
                $newFileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/uploads', $newFileName);
                // Enregistrer le fichier dans la base de données
                RessourceLecon::create([
                    'original_name' => $file->getClientOriginalName(),
                    'name' => $newFileName,
                    'type' => $request->type,
                    'lecon_id' => $lecon->id,
                    'slug' => Str::random(10),
                    'url' => Storage::url($path),
                ]);
            }
        }

        return response()->json(['filePaths' => $filePaths, 'message' => "Fichiers enregistrés"], 201);
    }



}
