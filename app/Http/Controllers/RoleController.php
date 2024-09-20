<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;


class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view role', ['only' => ['index']]);
        $this->middleware('permission:create role', ['only' => ['store', 'addPermissionToRole', 'givePermissionToRole']]);
        $this->middleware('permission:update role', ['only' => ['update']]);
        $this->middleware('permission:delete role', ['only' => ['destroy']]);
    }

    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="Récupérer tous les rôles",
     *     tags={"Rôles"},
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *     ),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Créer un nouveau rôle",
     *     tags={"Rôles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rôle créé avec succès"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     ),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'display_name' => 'required|string',
            'description' => 'required|string'
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description']
        ]);

        return response()->json(['message' => 'Groupe créé avec succès', 'role' => $role], 201);
    }

    /**
     * @OA\Get(
     *      tags={"Rôles"},
     *      summary="Récupère un groupe par son slug",
     *      description="Retourne un groupe",
     *      path="/api/roles/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug de la groupe",
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
        //$data = Role::where(["slug"=> $slug, "is_deleted" => false])->first();
        $data = Role::where(["slug"=> $slug])->first();

        if (!$data) {
            return response()->json(['message' => 'Groupe non trouvée'], 404);
        }

        return response()->json(['message' => 'Groupe trouvée', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/roles/{id}",
     *     summary="Mettre à jour un rôle existant",
     *     tags={"Rôles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="editeur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rôle mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rôle non trouvé"
     *     ),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'display_name' => 'required|string',
            'description' => 'required|string'
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description']
        ]);

        return response()->json(['message' => 'Groupe mis à jour avec succès', 'role' => $role], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/roles/{id}",
     *     summary="Supprimer un rôle",
     *     tags={"Rôles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rôle supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rôle non trouvé"
     *     ),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function destroy($roleId)
    {
        $role = Role::findOrFail($roleId);
        $role->delete();

        return response()->json(['message' => 'Groupe supprimé avec succès'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/roles/{id}/permissions",
     *     summary="Assigner des permissions à un rôle",
     *     tags={"Rôles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"permission"},
     *             @OA\Property(property="permission", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permissions ajoutées au rôle"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rôle non trouvé"
     *     ),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function givePermissionToRole(Request $request, $roleId)
    {
        $validated = $request->validate([
            'permission' => 'required|array'
        ]);

        $role = Role::findOrFail($roleId);
        $role->syncPermissions($validated['permission']);

        return response()->json(['message' => 'Permissions ajoutées au rôle'], 200);
    }
}
