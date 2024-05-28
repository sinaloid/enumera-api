<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatiereDeLaClasse extends Model
{
    use HasFactory;

    protected $fillable = [
        "slug",
        "is_deleted",
        "matiere_id",
        "classe_id",
    ];
}
