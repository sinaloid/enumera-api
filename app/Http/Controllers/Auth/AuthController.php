<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
     /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Données JSON",
     *     description="Création d'un nouveau compte utilisateur",
     *     operationId="receiveJson",
     *     tags={"Autentification"},
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
     *             @OA\Property(property="message", type="string", example="Votre compte a été crée avec succès"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="nom", type="string", example="Doe"),
     *                 @OA\Property(property="prenom", type="string", example="John"),
     *                 @OA\Property(property="date_de_naissance", type="string", format="date", example="1990-01-01"),
     *                 @OA\Property(property="genre", type="string", example="M"),
     *                 @OA\Property(property="profile", type="string", example="ELEVE"),
     *                 @OA\Property(property="telephone", type="string", example="75000000"),
     *                 @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *                 @OA\Property(property="password", type="string", example="@test@password#2024")
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Les données fournies ne sont pas valides."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_de_naissance' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'genre' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'telephone' => 'nullable|integer|digits:8|starts_with:5,6,7,01,02,03,05,06,07|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'date_de_naissance' => $request->date_de_naissance,
                'genre' => $request->genre,
                'profile' => $request->profile,
                'genre' => $request->genre,
                'telephone' => $request->telephone,
                'email' => $request->email,
                'slug' => Str::random(10),
                //'isActive' => ($request->type === "livreur") ? false : true,
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

            $token = $user->createToken('my-app-token')->accessToken;

            return response()->json(['data' => $user, 'access_token' => $token],200);

    }

     /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Données JSON",
     *     description="Connexion d'un utilisateur à son compte",
     *     tags={"Autentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user","password"},
     *             @OA\Property(property="user", type="string", example="john.doe@example.com ou 75000000"),
     *             @OA\Property(property="password", type="string", example="@test@password#2024")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Connexion réussi avec succès"),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Les données fournies ne sont pas valides."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        // Validation des données de la requête
        $validator = Validator::make($request->all(), [
            'user' => 'required_without:telephone|email',
            'telephone' => 'required_without:user|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Préparer les identifiants pour l'authentification
        $credentialsEmail = ['email' => $request->user, 'password' => $request->password];
        $credentialsTelephone = ['telephone' => $request->telephone, 'password' => $request->password];

        // Essayer de se connecter avec l'email ou le téléphone
        if (Auth::attempt($credentialsEmail) || Auth::attempt($credentialsTelephone)) {
            $user = Auth::user();

            // Vérifier si le compte est actif
            if (!$user->isActive) {
                return response()->json(['errors' => 'Votre compte a été désactivé temporairement'], 401);
            }

            // Vérifier si l'email est vérifié
            /*if (!$user->email_verified_at) {
                return response()->json(['errors' => "Votre email n'a pas été validé, veuillez valider votre email avant de pouvoir continuer"], 401);
            }*/

            // Générer un token d'accès
            $token = $user->createToken('my-app-token')->accessToken;
            return response()->json(['user' => $user, 'access_token' => $token]);
        }

        // Retourner une réponse en cas d'identifiants invalides
        return response()->json(['error' => 'Identifiants de connexion invalides'], 401);
    }

    public function editPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }


        $user = User::where('email', $request->email)->first();

        if ($user) {

            $user->update([
                'password' => bcrypt($request->password),
            ]);

            return response()->json(['message' => 'Votre mot de passe à bien été modifier, vous pouvez vous connecter maintenant'], 200);

        } else {
            // Réponse d'erreur
            return response()->json(['error' => "Votre adresse e-mail ou votre numéro de téléphone est incorrect"], 422);
        }

    }


}
