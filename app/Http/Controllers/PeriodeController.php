<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PeriodeController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Periodes"},
     *      summary="Liste des periodes",
     *      description="Retourne la liste des periodes",
     *      path="/api/periodes",
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
        $data = Periode::where("is_deleted",false)->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucune periode trouvée'], 404);
        }

        return response()->json(['message' => 'Periodes récupérées', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Periodes"},
     *     description="Crée une nouvelle periode et retourne la periode créée",
     *     path="/api/periodes",
     *     summary="Création d'une periode",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","abreviation"},
     *             @OA\Property(property="label", type="string", example="Trimestre 1"),
     *             @OA\Property(property="abreviation", type="string", example="T1"),
     *             @OA\Property(property="description", type="string", example="Trimestre 1")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Periode créée avec succès"),
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
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }


        $data = Periode::create([
            'label' => $request->input('label'),
            'abreviation' => $request->input('abreviation'),
            'description' => $request->input('description'),
            'slug' => Str::random(10),
        ]);

        return response()->json(['message' => 'Periodes créée avec succès', 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *      tags={"Periodes"},
     *      summary="Récupération d'une periode par son slug",
     *      description="Retourne une periode",
     *      path="/api/periodes/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la periode à récupérer",
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
        $data = Periode::where(["slug"=> $slug, "is_deleted" => false])->first();

        if (!$data) {
            return response()->json(['message' => 'Matière non trouvée'], 404);
        }

        return response()->json(['message' => 'Matière trouvée', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Periodes"},
     *     description="Modifie une periode et retourne la periode modifiée",
     *     path="/api/periodes/{slug}",
     *     summary="Modification d'une periode",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"label","abreviation"},
     *             @OA\Property(property="label", type="string", example="Trimestre 1"),
     *             @OA\Property(property="abreviation", type="string", example="T1"),
     *             @OA\Property(property="description", type="string", example="Trimestre 1")
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la periode à modifiée",
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
     *             @OA\Property(property="message", type="string", example="Periode modifiée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Slug validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Periode non trouvée"),
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
            'description' => 'nullable|string|max:10000',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $data = Periode::where("slug", $slug)->where("is_deleted",false)->first();

        if (!$data) {
            return response()->json(['message' => 'Periode non trouvée'], 404);
        }

        $data->update([
            'label' => $request->input('label'),
            'abreviation' => $request->input('abreviation'),
            'description' => $request->input('description'),
        ]);

        return response()->json(['message' => 'Periode modifiée avec succès', 'data' => $data], 200);

    }

    /**
     * @OA\Delete(
     *      tags={"Periodes"},
     *      summary="Suppression d'une periode par son slug",
     *      description="Retourne la periode supprimée",
     *      path="/api/periodes/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Periode supprimée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Slug validation error",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Periode non trouvée"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la periode à supprimer",
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

        $data = Periode::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'Periode non trouvée'], 404);
        }


        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'Periode supprimée avec succès',"data" => $data]);
    }
}
