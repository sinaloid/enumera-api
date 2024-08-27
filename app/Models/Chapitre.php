<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapitre extends Model
{
    use HasFactory;

    protected $fillable = [
        "label",
        "abreviation",
        "description",
        "slug",
        "is_deleted",
        "matiere_de_la_classe_id",
    ];

    public function matiereDeLaClasse() {

        return $this->belongsTo(MatiereDeLaClasse::class);
    }

    public function lecons() {

        return $this->hasMany(Lecon::class);
    }

}
