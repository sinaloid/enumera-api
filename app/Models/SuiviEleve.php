<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuiviEleve extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'lecon_id', 'type_activite', 'temps_passe', 'score'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function lecon() {
        return $this->belongsTo(Lecon::class);
    }
}
