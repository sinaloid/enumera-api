<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationLeconReponseEleve extends Model
{
    use HasFactory;

    protected $fillable = [
        "point_obtenu",
        "user_response",
        "slug",
        "is_deleted",
        "user_id",
        "evaluation_lecon_id",
    ];

    public function evaluationLecon(){

        return $this->belongsTo(EvaluationLecon::class);
    }

    public function user(){

        return $this->belongsTo(User::class);
    }
}
