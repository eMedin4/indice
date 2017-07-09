<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'name'];
}
