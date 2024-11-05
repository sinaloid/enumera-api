<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationDevoir extends Model
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
    ];

    public function questions() {
        return $this->hasMany(Question::class);
    }

    public function evaluationMatiereDeLaClasses(){
        return $this->hasMany(EvaluationMatiereDeLaClasse::class);
    }
}
