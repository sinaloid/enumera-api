<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatiereDeLaClasse extends Model
{
    use HasFactory;

    protected $fillable = [
        "coefficient",
        "slug",
        "is_deleted",
        "matiere_id",
        "classe_id",
    ];

    public function matiere(){

        return $this->belongsTo(Matiere::class);
    }

    public function classe(){

        return $this->belongsTo(Classe::class);
    }

    public function chapitres(){

        return $this->hasMany(Chapitre::class);
    }
}
