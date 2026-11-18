<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name',
        'role',
        'photo_path',
        'display_order',
        'facebook_url',
        'tiktok_url',
        'linkedin_url',
        'x_url',
    ];
}