<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    use HasFactory;
    protected $fillable = ['key', 'value', 'description', 'slug', 'is_deleted'];
    protected $attributes = ['is_deleted' => false];
}
