<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RessourceLecon extends Model
{
    use HasFactory;
    protected $fillable = [
        "original_name",
        "name",
        "type",
        "url",
        "slug",
        "is_deleted",
        "lecon_id",
    ];
}
