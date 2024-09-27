<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClasseMatiere extends Model
{
    use HasFactory;

    protected $fillable = [
        "matiere_label",
        "matiere_id",
        "matiere_slug",
        "slug",
        "is_deleted",
        "user_classe_id",
    ];

    public function userClasse(){

        return $this->beLongsTo(UserClasse::class);
    }
}
