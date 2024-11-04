<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationLecon extends Model
{
    use HasFactory;

    protected $fillable = [
        "label",
        "abreviation",
        "type_de_correction",
        "description",
        "slug",
        "is_deleted",
        "lecon_id",
    ];

    public function lecon() {

        return $this->belongsTo(Lecon::class);
    }

    public function question_lecons() {
        return $this->hasMany(QuestionLecon::class);
    }

    public function evaluation_lecon_reponse_eleves() {
        return $this->hasMany(EvaluationLeconReponseEleve::class);
    }
}
