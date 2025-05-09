<?php

namespace App\Http\Controllers;

use App\Models\Meet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MeetController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/meets",
     *     summary="Liste des sessions",
     *     tags={"Meet Sessions"},
     *     @OA\Response(response=200, description="Liste des sessions"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        #return Meet::with('moderator')->orderBy('scheduled_at', 'desc')->get();
        //return Meet::orderBy('scheduled_at', 'desc')->get();
        $data = Meet::where("is_deleted",false)->orderBy('date', 'desc')->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Aucun meet trouvé'], 404);
        }
        return response()->json(['message' => 'Meets récupérées', 'data' => $data], 200);

    }

    /**
     * @OA\Post(
     *     path="/api/meets",
     *     summary="Créer une session",
     *     tags={"Meet Sessions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "scheduled_at", "duration"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="date", type="02/05/2025"),
     *             @OA\Property(property="heure", type="16:12"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="scheduled_at", type="string", format="date-time"),
     *             @OA\Property(property="duration", type="integer"),
     *             @OA\Property(property="moderator_id", type="integer"),
     *             @OA\Property(property="extra_info", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Session créée"),
     *     security={{"bearerAuth":{}}}
     * 
     * 
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|string|max:255',
            'heure' => 'required|string|max:255',
            'description' => 'nullable|string',
            //'scheduled_at' => 'required|date',
            'duration' => 'required|integer',
            'extra_info' => 'nullable|string',
        ]);

        $roomName = "salle-" . Str::slug($request->title);
        $meetingLink = "https://meet.enumera.tech/" . $roomName;

        $session = Meet::create([
            'title' => $request->title,
            'date' => $request->date,
            'heure' => $request->heure,
            'description' => $request->description,
            //'scheduled_at' => $request->scheduled_at,
            #'scheduled_at' => \Carbon\Carbon::parse($request->scheduled_at)->format('Y-m-d H:i:s'),
            'duration' => $request->duration,
            'moderator_id' => $request->moderator_id,
            'jitsi_room_name' => $roomName,
            'jitsi_meeting_link' => $meetingLink,
            //'extra_info' => $request->extra_info,
            'slug' => Str::random(10),
        ]);

        return response()->json(['message' => 'Session meet crée avec succès', 'session' => $session]);

        return response()->json($session, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/meets/{slug}",
     *     summary="Afficher une session",
     *     tags={"Meet Sessions"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="slug de la session",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Session récupérée"),
     *     @OA\Response(response=404, description="Non trouvé"),
     *     security={{"bearerAuth":{}}}
     * 
     * )
     */
    public function show($slug)
    {
        $data = Meet::where(["slug"=> $slug, "is_deleted" => false])->first();

        if (!$data) {
            return response()->json(['message' => 'Meet non trouvé'], 404);
        }

        return response()->json(['message' => 'Meet trouvée', 'data' => $data], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/meets/{slug}",
     *     summary="Mettre à jour le statut de la session",
     *     tags={"Meet Sessions"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="slug de la session",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "scheduled_at", "duration","status"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="date", type="02/05/2025"),
     *             @OA\Property(property="heure", type="16:12"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="scheduled_at", type="string", format="date-time"),
     *             @OA\Property(property="duration", type="integer"),
     *             @OA\Property(property="moderator_id", type="integer"),
     *             @OA\Property(property="extra_info", type="string"),
     *             @OA\Property(property="status", type="string", enum={"planned", "ongoing", "completed"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Statut mis à jour"),
     *     @OA\Response(response=404, description="Session non trouvée"),
     *     security={{"bearerAuth":{}}}
     * 
     * )
     */
    public function update(Request $request, $slug)
    {
        $session = Meet::where(["slug"=> $slug, "is_deleted" => false])->first();
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|string|max:255',
            'heure' => 'required|string|max:255',
            'description' => 'nullable|string',
            //'scheduled_at' => 'required|date',
            'duration' => 'required|integer',
            'extra_info' => 'nullable|string',
            //'status' => 'required|in:planned,ongoing,completed'
        ]);

        $roomName = "salle-" . Str::slug($request->title);
        $meetingLink = "https://meet.enumera.tech/" . $roomName;

        $session->update([
            'title' => $request->title,
            'date' => $request->date,
            'heure' => $request->heure,
            'description' => $request->description,
            //'scheduled_at' => $request->scheduled_at,
            //'scheduled_at' => \Carbon\Carbon::parse($request->scheduled_at)->format('Y-m-d H:i:s'),
            'duration' => $request->duration,
            'moderator_id' => $request->moderator_id,
            'jitsi_room_name' => $roomName,
            'jitsi_meeting_link' => $meetingLink,
            'status' => 'planned',//$request->status,
            //'extra_info' => $request->extra_info,
        ]);

        return response()->json(['message' => 'Session mis à jour', 'session' => $session]);
    }

    /**
     * @OA\Delete(
     *      tags={"Meet Sessions"},
     *      path="/api/meets/{slug}",
     *      summary="MettrSuppression d'une session meet",
     *      description="Retourne l de lae meet supprimée",
     *      path="/api/meets/{slug}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Meets supprimée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Slug validation error",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Meet non trouvée"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug du meet à supprimer",
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

        $data = Meet::where("slug",$slug)->where("is_deleted",false)->first();
        if (!$data) {
            return response()->json(['message' => 'Meet non trouvé'], 404);
        }

        $data->update(["is_deleted" => true]);

        return response()->json(['message' => 'Meet supprimée avec succès',"data" => $data]);
    }
}
