<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'hashed_password',
        'is_admin',
        'is_confirmed',
        'token_version',
    ];

    protected $hidden = [
        'hashed_password',
    ];
}
