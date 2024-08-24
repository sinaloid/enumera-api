<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecon extends Model
{
    use HasFactory;

    protected $fillable = [
        "label",
        "abreviation",
        //"type",
        "description",
        "slug",
        "is_deleted",
        "chapitre_id",
        "periode_id",

    ];

    public function chapitre() {

        return $this->belongsTo(Chapitre::class);
    }

    public function cours() {

        return $this->hasOne(Cours::class);
    }
    public function evaluations_lecons() {

        return $this->hasMany(EvaluationLecon::class);
    }

    public function periode() {

        return $this->belongsTo(Periode::class);
    }
}
