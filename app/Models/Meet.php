<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meet extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
        'heure',
        'description',
        'scheduled_at',
        'duration',
        'moderator_id',
        'jitsi_room_name',
        'jitsi_meeting_link',
        'status',
        'slug',
        'is_deleted'
        #'extra_info'
    ];

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }
    
    public function participants()
    {
        return $this->hasMany(MeetSessionParticipant::class);
    }
}
