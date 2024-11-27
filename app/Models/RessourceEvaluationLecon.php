<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RessourceEvaluationLecon extends Model
{
    use HasFactory;

    protected $fillable = [
        "original_name",
        "name",
        "type",
        "url",
        "slug",
        "is_deleted",
        "evaluation_lecon_id",
    ];
}
