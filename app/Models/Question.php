<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
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
        "evaluation_id",
    ];

    public function evaluation(){

        return $this->belongsTo(Evaluation::class);
    }
}
