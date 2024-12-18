<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametre;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $parametres = [
            [
                'key' => 'duree_minimum_lecture_cours',
                'value' => '90',
                'description' => "Durée minimum en secondes de lecture qu'il faut pour qu'un cours soit considéré comme vu",
            ],
            [
                'key' => 'nombre_minimum_tentatives_exercice',
                'value' => '5',
                'description' => "Nombre minimum de tentatives d'un exercice sans avoir la moyenne qu'il faut pour pouvoir voir la correction",
            ],
        ];

        // Créer les paramètres avec slug généré automatiquement
        foreach ($parametres as $param) {
            Parametre::firstOrCreate([
                'key' => $param['key'],
                'value' => $param['value'],
                'slug' => Str::slug($param['key']), // Génération dynamique du slug
                'description' => $param['description'],
            ]);
        }
    }
}
