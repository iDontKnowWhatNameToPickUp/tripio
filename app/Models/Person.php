<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotel_id',
        'name',
        'lastname',
        'patronymic',
        'passport',
        'email',
        'phone'
    ];

    protected $table = 'person';
}
