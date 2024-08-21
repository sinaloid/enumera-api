<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AsposeService;
use App\Models\Cours;
use App\Models\Lecon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DocumentController extends Controller {

    protected $asposeService;

    public function __construct(AsposeService $asposeService)
    {
        $this->asposeService = $asposeService;
    }

    public function convertDocumentToHtml(Request $request)
    {
        // Valider que le fichier est présent dans la requête
        /*$request->validate([
            'file' => 'required|mimes:doc,docx|max:200048', // Limiter le type et la taille du fichier
        ]);*/

        // Récupérer le fichier envoyé dans la requête
        $file = $request->file('file');

        // Obtenir le chemin temporaire du fichier
        $filePath = $file->getPathname();

        // Convertir le document en HTML
        $result = $this->asposeService->convertDocumentToHtml($filePath);

        // Retourner la réponse JSON au client

        $request['description'] = $result;
        $this->store($request);

        return $result;
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'lecon' => 'required|string|max:10',
            'abreviation' => 'required|string|max:255',
            'description' => 'nullable|string',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $lecon = Lecon::where(["slug" => $request->lecon,"is_deleted" => false])->first();
        if (!$lecon) {
            return response()->json(['message' => 'Leçon non trouvé'], 404);
        }

        $data = Cours::create([
            'label' => $request->input('label'),
            'abreviation' => $request->input('abreviation'),
            'type' => $request->input('type'),
            'lecon_id' => $lecon->id,
            'description' => $request->input('description'),
            'slug' => Str::random(10),
        ]);

        return response()->json(['message' => 'Cours créé avec succès', 'data' => $data], 200);
    }
}
