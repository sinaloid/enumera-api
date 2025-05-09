<?php

namespace App\Http\Controllers;

use App\Models\MeetParticipant;
use Illuminate\Http\Request;
use App\Models\Meet;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use App\Mail\MeetInvitationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class MeetParticipantController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/meets/{meetId}/participants",
     *     summary="Lister les participants d'une session",
     *     tags={"Meet Participants"},
     *     @OA\Parameter(
     *         name="meetId",
     *         in="path",
     *         required=true,
     *         description="ID de la session",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des participants",
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index($slug)
    {
        $meet = Meet::where(['slug' => $slug, 'is_deleted' => false])->first();
        if(!$meet){
            return response()->json(['message' => 'Aucun meet trouvé'], 404);
        }
        $participants = MeetParticipant::where('meet_id', $meet->id)->get();

        return response()->json(['message' => 'Particpants récupérés', 'data' => $participants, 'meet' => $meet], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/meets/{meetId}/participants",
     *     summary="Ajouter un participant à une session",
     *     tags={"Meet Participants"},
     *     @OA\Parameter(
     *         name="meetId",
     *         in="path",
     *         required=true,
     *         description="ID de la session",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id"},
     *             @OA\Property(property="user_ids", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Participant ajouté",
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request, $meetId)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $meet = Meet::where(['id' => $meetId, 'is_deleted' => false])->first();

        if (!$meet) {
            return response()->json(['error' => 'Meet non trouvé.'], 404);
        } 

        foreach ($validated['user_ids'] as $userId) {
            // Appel à user-service pour récupérer les infos
            $user = User::where('id',$userId)->first();

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
            }  
            
            // Construire le payload JWT
            $payload = [
                'aud' => 'jitsi',
                'iss' => config('services.jitsi.app_id'),
                'sub' => parse_url(config('services.jitsi.app_url'), PHP_URL_HOST),
                'room' => "meeting-{$meet->id}",
                'exp' => time() + 3600,
                'context' => [
                    'user' => [
                        'name' => $user->nom.''.$user->prenom,
                        'email' => $user->email
                    ],
                    'moderator' => $user->id === $meet->moderator_id ? true : false,
                ]
            ];
        
            $token = JWT::encode($payload, config('services.jitsi.app_secret'), 'HS256');


            // Créer le participant
            MeetParticipant::create([
                'meet_id' => $meetId,
                'user_id'         => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'meet_token'      => $token,
                'is_moderator'      => $user->id === $meet->moderator_id ? true : false,
                'slug' => Str::random(10),
            ]);
        }
        // Vérifier si la session existe
        
        $participants = MeetParticipant::where('meet_id', $meetId)->get();
        if ($participants->isEmpty()) {
            return response()->json(['error' => 'Aucun participant ajouté.'], 404);
        }

        return response()->json(['message' => 'Participants ajoutés avec succès', 'data' => $participants], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/meets/{sessionId}/participants/{id}",
     *     summary="Afficher un participant",
     *     tags={"Meet Participants"},
     *     @OA\Parameter(
     *         name="sessionId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du participant",
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show($sessionId, $id)
    {
        $participant = MeetParticipant::where('meet_id', $sessionId)->findOrFail($id);
        if (!$participant) {
            return response()->json(['error' => 'Participant non trouvé.'], 404);
        }

        return response()->json(['message' => 'Participant trouvé', 'data' => $participant], 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/meets/{sessionId}/participants/{id}",
     *     summary="Mettre à jour les infos d'un participant",
     *     tags={"Meet Participants"},
     *     @OA\Parameter(
     *         name="sessionId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Participant mis à jour",
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, $sessionId, $id)
    {
        $participant = MeetParticipant::where('meet_session_id', $sessionId)->findOrFail($id);

        $participant->update($request->only(['name', 'email']));

        return response()->json($participant);
    }

    /**
     * @OA\Delete(
     *     path="/api/meets/{sessionId}/participants/{id}",
     *     summary="Supprimer un participant",
     *     tags={"Meet Participants"},
     *     @OA\Parameter(
     *         name="sessionId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Participant supprimé"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy($meetSlug, $slug)
    {
        $meet = Meet::where('slug', $meetSlug)->first();
        
        if(!$meet){
            return response()->json(['message' => "La session n'existe pas"], 404);
        }

        $participant = MeetParticipant::where(['meet_id' => $meet->id, 'slug' => $slug])->first();

        if(!$participant){
            return response()->json(['message' => "Le participant n'existe pas"], 404);
        }


        $participant->delete();

        return response()->json(null, 204);
    }

    /**
    * @OA\Post(
    *     path="/api/meets/{meetId}/participants-by-email",
    *     summary="Ajouter des participants à une session",
    *     tags={"Meet Participants"},
    *     @OA\Parameter(
    *         name="meetId",
    *         in="path",
    *         required=true,
    *         description="ID de la session",
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"users"},
    *             @OA\Property(
    *                 property="users",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     required={"nom", "email"},
    *                     @OA\Property(property="nom", type="string", example="Jean Dupont"),
    *                     @OA\Property(property="email", type="string", format="email", example="jean@example.com"),
    *                     @OA\Property(property="is_moderator", type="boolean", example=true)
    *                 )
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=201,
    *         description="Participants ajoutés avec succès",
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Meet non trouvé ou aucun participant ajouté",
    *     ),
    *     security={{"bearerAuth":{}}}
    * )
    */

    public function store_users(Request $request, $meetId)
    {
        $validator = Validator::make($request->all(), [
            'users' => 'required|array',
            'users.*.nom' => 'required|string',
            'users.*.prenom' => 'nullable|string',
            'users.*.email' => 'required|email',
            //'users.*.is_moderator' => 'boolean'

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $meet = Meet::where(['slug' => $meetId, 'is_deleted' => false])->first();

        if (!$meet) {
            return response()->json(['error' => 'Meet non trouvé.'], 404);
        }

        foreach ($request['users'] as $user) {
            $fullName = isset($user['prenom']) ? $user['nom'].' '.$user['prenom'] : $user['nom'];
            $email = $user['email'];
            $isModerator = $user['is_moderator'] ?? false;

            $payload = [
                'aud' => 'jitsi',
                'iss' => config('services.jitsi.app_id'),
                'sub' => parse_url(config('services.jitsi.app_url'), PHP_URL_HOST),
                'room' => $meet->jitsi_room_name, //"salle-" . Str::slug($meet->titre),
                'exp' => time() + 3600,
                'context' => [
                    'user' => [
                        'name' => $fullName,
                        'email' => $email
                    ],
                    'moderator' => $isModerator === 'true' ? true : false,
                ]
            ];

            $token = JWT::encode($payload, config('services.jitsi.app_secret'), 'HS256');

            $participant = MeetParticipant::create([
                'meet_id'       => $meet->id,
                'user_id'       => null, // si pas de lien avec la table users
                'name'          => $fullName,
                'email'         => $email,
                'meet_token'    => $token,
                'is_moderator'  => $isModerator === 'true' ? true : false,
                'slug'          => Str::random(10),
            ]);

            // Envoi du mail
            Mail::to($email)->send(new MeetInvitationMail($participant, $meet));
        }

        $participants = MeetParticipant::where('meet_id', $meet->id)->get();

        if ($participants->isEmpty()) {
            return response()->json(['error' => 'Aucun participant ajouté.'], 404);
        }



        return response()->json([
            'message' => 'Participants ajoutés avec succès',
            'data' => $participants
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/my-meets",
     *     tags={"Meets"},
     *     summary="Lister les sessions virtuelles de l'utilisateur connecté",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des sessions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="titre", type="string", example="Cours de mathématiques"),
     *                 @OA\Property(property="lien", type="string", example="https://meet.enumera.tech/salle-cours-de-mathematiques?jwt=...")
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * 
     * )
     */

        public function myMeets()
    {
        $user = auth()->user();

        //$meets = $user->meets()->withPivot('is_moderator')->get();
        $meets = Meet::where([
            'is_deleted' => false,
        ])->get();

        $result = $meets->map(function ($meet) use ($user) {
            $participant = MeetParticipant::where([
                'is_deleted' => false,
                'user_id' => $user->id,
                'meet_id' => $meet->id
            ])->first();
            if($participant){
                $token = $participant->meet_token;
                $link = config('services.jitsi.app_url') . '/salle-' . $meet->jitsi_room_name . '?jwt=' . $token;

                return [
                    'id' => $meet->id,
                    'titre' => $meet->titre,
                    'lien' => $link,
                    'meet' => $meet
                ];
            }
        });

        return response()->json($result);
    }

}
