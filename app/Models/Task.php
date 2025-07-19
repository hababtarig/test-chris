<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'type', 'server', 'username', 'status', 'log'
    ];
}
