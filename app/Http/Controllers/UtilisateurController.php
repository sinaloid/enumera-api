<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lecon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UtilisateurController extends Controller
{
    /**
     * @OA\Get(
     *      tags={"Utilisateurs"},
     *      summary="Liste des utilisateur",
     *      description="Retourne la liste des utilisateur",
     *      path="/api/utilisateurs",
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
        $data = User::where("is_deleted",false)->get();

        if($request->profile){
            $data = User::where([
                "is_deleted"=> false,
                "profile" => $request->profile
            ])->get();
        }

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucun utilisateurs trouvé'], 404);
        }

        return response()->json(['message' => 'utilisateurs récupérés', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Utilisateurs"},
     *     description="Crée une nouveau utilisateur et retourne le utilisateur créé",
     *     path="/api/utilisateurs",
     *     summary="Création d'un utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom","prenom","date_de_naissance","genre","telephone","email","password"},
     *             @OA\Property(property="nom", type="string", example="Doe"),
     *             @OA\Property(property="prenom", type="string", example="John"),
     *             @OA\Property(property="date_de_naissance", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="genre", type="string", example="M"),
     *             @OA\Property(property="profile", type="string", example="ELEVE"),
     *             @OA\Property(property="telephone", type="string", example="75000000"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="@test@password#2024")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="utilisateur créé avec succès"),
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
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_de_naissance' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'telephone' => 'nullable|integer|digits:8|starts_with:5,6,7,01,02,03,05,06,07|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

            /*$otpController = app(OtpController::class);
            $response = $otpController->generateOTP($request);
            $response = $response->getData();*/

            $matricule = $this->matriculeGenerator("ENA");
            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'date_de_naissance' => $request->date_de_naissance,
                'genre' => $request->genre,
                'profile' => $request->profile,
                'genre' => $request->genre,
                'telephone' => $request->telephone,
                'matricule' => $matricule,
                'email' => $request->email,
                'slug' => Str::random(10),
                "isActive" => true,
                "email_verified_at" => Carbon::now(),
                'password' => bcrypt($request->password),
            ]);

            if ($request->hasFile('image')) {
                // Générer un nom aléatoire pour l'image
                $imageName = Str::random(10) . '.' . $request->image->getClientOriginalExtension();

                // Enregistrer l'image dans le dossier public/images
                $imagePath = $request->image->move(public_path('users'), $imageName);

                if ($imagePath) {
                    $user->update([
                        'image' => 'users/' . $imageName,
                    ]);
                }
            }

            //$token = null;//$user->createToken('my-app-token')->accessToken;
            //$user->notify(new OtpCode(["slug" => $user->slug, "code" => $response->code]));

            return response()->json(['message' => "Compte utilisateur crée avec succes",'data' => $user],200);
    }

    public function matriculeGenerator($prefixe){

        $last_user = User::orderBy("id",'desc')->first();
        $id = isset($last_user) ? $last_user->id : 0;
        $order = str_pad('', 4 - strlen($id), '0', STR_PAD_LEFT);

        return $prefixe."-".date('y')."-".$order."".$id;
    }

    /**
     * @OA\Get(
     *      tags={"Utilisateurs"},
     *      summary="Récupère un utilisateur par son slug",
     *      description="Retourne un utilisateur",
     *      path="/api/utilisateurs/{slug}",
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
    public function show($slug)
    {
        $data = User::where(["slug"=> $slug, "is_deleted" => false])->with('roles.permissions','permissions')->first();

        if (!$data) {
            return response()->json(['message' => 'utilisateur non trouvé'], 404);
        }

        return response()->json(['message' => 'utilisateur trouvé', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     tags={"Utilisateurs"},
     *     description="Modifie un utilisateur et retourne le utilisateur modifié",
     *     path="/api/utilisateurs/{slug}",
     *     summary="Modification d'un utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom","prenom","date_de_naissance","genre","telephone","email","password"},
     *             @OA\Property(property="nom", type="string", example="Doe"),
     *             @OA\Property(property="prenom", type="string", example="John"),
     *             @OA\Property(property="date_de_naissance", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="genre", type="string", example="M"),
     *             @OA\Property(property="profile", type="string", example="ELEVE"),
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug du utilisateur à modifié",
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
     *             @OA\Property(property="message", type="string", example="utilisateur modifié avec succès"),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Slug validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="utilisateur non trouvé"),
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
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_de_naissance' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            //'telephone' => 'nullable|integer|digits:8|starts_with:5,6,7,01,02,03,05,06,07|unique:users',
            //'email' => 'required|string|email|max:255|unique:users',
            //'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }


        $data = User::where("slug", $slug)->where("is_deleted",false)->first();

        if (!$data) {
            return response()->json(['message' => 'utilisateur non trouvé'], 404);
        }

        $data->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'date_de_naissance' => $request->date_de_naissance,
            'genre' => $request->genre,
            'profile' => $request->profile,
            'genre' => $request->genre,
            //'telephone' => $request->telephone,
            //'matricule' => $matricule,
            //'email' => $request->email,
            'slug' => Str::random(10),
            "isActive" => true,
            //"email_verified_at" => Carbon::now(),
            //'password' => bcrypt($request->password),
        ]);


        return response()->json(['message' => 'utilisateur modifié avec succès', 'data' => $data], 200);

    }

    /**
     * @OA\Delete(
     *      tags={"Utilisateurs"},
     *      summary="Suppression d'un utilisateur par son slug",
     *      description="Retourne le utilisateur supprimé",
     *      path="/api/utilisateurs/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="utilisateur supprimé avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Slug validation error",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="utilisateur non trouvé"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug du utilisateur à supprimer",
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

        $data = User::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'utilisateur non trouvé'], 404);
        }


        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'utilisateur supprimé avec succès',"data" => $data]);
    }


    /**
     * @OA\Get(
     *      tags={"Utilisateurs"},
     *      summary="Récupère un utilisateur par son slug",
     *      description="Retourne la liste des utilisateurs",
     *      path="/api/utilisateurs/profilep/{slug}",
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
    public function getUtilisateurByProfile($slug)
    {
        $data = User::where(["profile"=> $slug, "is_deleted" => false])->get();



        return response()->json(['message' => 'utilisateur trouvé', 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/utilisateurs/{slug}/groupe",
     *     summary="Assigner un groupe d'utilisateur à un utilisateur",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"permission"},
     *             @OA\Property(property="groupe", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Groupe attribué à l'utilisateur"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rôle non trouvé"
     *     ),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function giveRoleToUser(Request $request, $slug)
    {
        $validated = $request->validate([
            'groupe' => 'required|string'
        ]);

        $role = Role::where("slug",$request->groupe)->first();
        if(!$role){
            return response()->json(['message' => "Le groupe n'existe pas"], 404);
        }
        $utilisateur = User::where("slug",$slug)->first();

        if(!$utilisateur){
            return response()->json(['message' => "L'utilisateur n'existe pas"], 404);
        }
        //$role->syncPermissions($validated['permission']);
        //$utilisteur->assignRole($role);
        $utilisateur->syncRoles($role);

        return response()->json(['message' => "Groupe attribué à l'utilisateur"], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/utilisateurs/{slug}/droits",
     *     summary="Assigner des permissions à un utilisateur",
     *     tags={"Rôles"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
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
    public function givePermissionsToUser(Request $request, $slug)
    {
    // Valider les données d'entrée
    $validated = $request->validate([
        'permissions' => 'required|array',
        'permissions.*' => 'string|exists:permissions,name', // Chaque permission doit être une chaîne valide et exister dans la table des permissions
    ]);

    // Rechercher l'utilisateur par le slug
    $utilisateur = User::where('slug', $slug)->first();

    if (!$utilisateur) {
        return response()->json(['message' => "L'utilisateur n'existe pas"], 404);
    }

        // Supprimer les permissions actuelles de l'utilisateur et attribuer les nouvelles
        $utilisateur->syncPermissions($validated['permissions']);

        return response()->json(['message' => 'Permissions attribuées avec succès à l\'utilisateur'], 200);
    }

}
