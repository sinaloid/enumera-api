<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailClasse extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "classe_id",
        "slug",
        "is_deleted",
    ];
}
