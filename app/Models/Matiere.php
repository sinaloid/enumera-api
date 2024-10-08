<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    use HasFactory;

    protected $fillable = [
        "label",
        "abreviation",
        "description",
        "slug",
        "is_deleted",
    ];

    public function matiereDeLaClasses(){

        return $this->hasMany(MatiereDeLaClasse::class);
    }
}
