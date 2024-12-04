<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageDefilant extends Model
{
    use HasFactory;

    protected $fillable = [
        "titre",
        "contenu",
        "type",
        "date_debut",
        "date_fin",
        "slug",
        "is_deleted",
    ];
}
