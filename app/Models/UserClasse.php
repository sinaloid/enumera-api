<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClasse extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id",
        "classe_id",
        "matiere",
        "slug",
        "is_deleted",
        "lecon_id",
    ];
    public function classe(){

        return $this->beLongsTo(Classe::class);
    }

    public function userClasseMatieres(){

        return $this->hasMany(UserClasseMatiere::class);
    }
}
