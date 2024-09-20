<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;


class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view permission', ['only' => ['index']]);
        $this->middleware('permission:create permission', ['only' => ['store']]);
        $this->middleware('permission:update permission', ['only' => ['update']]);
        $this->middleware('permission:delete permission', ['only' => ['destroy']]);
    }

    /**
     * @OA\Get(
     *     path="/api/permissions",
     *     summary="Récupérer toutes les permissions",
     *     tags={"Permissions"},
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *     ),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/permissions",
     *     summary="Créer une nouvelle permission",
     *     tags={"Permissions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="edit articles")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Permission créée avec succès"
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
            'name' => 'required|string|unique:permissions,name'
        ]);

        $permission = Permission::create(['name' => $validated['name']]);

        return response()->json(['message' => 'Permission créée avec succès', 'permission' => $permission], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/permissions/{id}",
     *     summary="Mettre à jour une permission existante",
     *     tags={"Permissions"},
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
     *             @OA\Property(property="name", type="string", example="edit articles")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission mise à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Permission non trouvée"
     *     ),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, $permissionId)
    {
        $permission = Permission::findOrFail($permissionId);

        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id
        ]);

        $permission->update(['name' => $validated['name']]);

        return response()->json(['message' => 'Permission mise à jour avec succès', 'permission' => $permission], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/permissions/{id}",
     *     summary="Supprimer une permission",
     *     tags={"Permissions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Permission non trouvée"
     *     ),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function destroy($permissionId)
    {
        $permission = Permission::findOrFail($permissionId);
        $permission->delete();

        return response()->json(['message' => 'Permission supprimée avec succès'], 200);
    }
}
