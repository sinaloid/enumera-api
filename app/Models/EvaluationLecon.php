<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationLecon extends Model
{
    use HasFactory;
    protected $fillable = [
        "question",
        "choix",
        "type",
        "reponses",
        "point",
        "slug",
        "is_deleted",
        "lecon_id",
    ];
}
