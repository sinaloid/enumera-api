<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        "label",
        "abreviation",
        "type_de_correction",
        "date",
        "heure_debut",
        "heure_fin",
        "etat",
        "description",
        "slug",
        "is_deleted",
        //"matiere_de_la_classe_id", à supprimer
    ];

    public function questions() {
        return $this->hasMany(Question::class);
    }

    public function matiereDeLaClasse() {

        return $this->belongsTo(MatiereDeLaClasse::class);
    }

}
