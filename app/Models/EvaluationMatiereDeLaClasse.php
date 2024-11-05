<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationMatiereDeLaClasse extends Model
{
    use HasFactory;

    protected $fillable = [
        "slug",
        "is_deleted",
        "evaluation_devoir_id",
        "matiere_de_la_classe_id",
    ];


    public function matiereDeLaClasse(){

        return $this->belongsTo(MatiereDeLaClasse::class);
    }
}
