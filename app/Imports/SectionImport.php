<?php

namespace App\Imports;

use App\Models\Chapitre;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;


class SectionImport implements ToModel
{
    protected $matiereClasse;
    public function __construct($matiereClasse)
    {
        //
        $this->matiereClasse = $matiereClasse;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row[0] && strtolower($row[0]) !=="sections" && strtolower($row[0]) !=="section"){
            return new Chapitre([
                'label' => $row[0],
                'matiere_de_la_classe_id' => $this->matiereClasse->id,
                'abreviation' => $row[0],
                'description' => $row[0],
                'slug' => Str::random(10),
            ]);
        }

    }
}
