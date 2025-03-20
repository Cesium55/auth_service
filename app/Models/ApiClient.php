<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiClient extends Model
{
    use HasFactory;

    protected $table = 'api_clients';

    protected $fillable = [
        'name',
        'client_secret',
        'is_admin',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
    ];
}
