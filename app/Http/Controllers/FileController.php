<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Ressource;

class FileController extends Controller
{
     /**
     * Afficher la liste des fichiers.
     */
    public function index()
    {
        $ressources = Ressource::all();

        return response()->json(['message' => 'Cours récupérés', 'data' => $ressources], 200);
    }

    /**
     * Stocker un nouveau fichier.
     */
    public function store(Request $request)
    {
        /*$request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip'
        ]);*/

        $filePaths = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('uploads');
                $filePaths[] = $path;

                // Enregistrer le fichier dans la base de données
                Ressource::create([
                    'name' => $file->getClientOriginalName(),
                    'url' => Storage::url($path),
                ]);
            }
        }

        return response()->json(['filePaths' => $filePaths, 'message' => "Fichiers enregistrés"], 201);
    }

    /**
     * Afficher un fichier spécifique.
     */
    public function show($file)
    {
        $ressource = Ressource::where('name', $file)->first();

        if ($ressource && Storage::exists("uploads/$file")) {
            return Storage::download("uploads/$file");
        }

        return response()->json(['error' => 'File not found'], 404);
    }
}
