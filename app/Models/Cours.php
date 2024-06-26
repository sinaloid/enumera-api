<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    use HasFactory;

    protected $fillable = [
        "label",
        "abreviation",
        "type",
        "description",
        "slug",
        "is_deleted",
        "lecon_id",
    ];

    public function lecon() {

        return $this->belongsTo(Lecon::class);
    }
}
