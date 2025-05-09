<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        "meet_token",
        "user_id",
        "slug",
        "is_moderator",
        "is_deleted",
        "meet_id",
        "name",
        "email",
    ];
}
