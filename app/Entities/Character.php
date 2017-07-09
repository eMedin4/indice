<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    public $timestamps = false;
    protected $fillable = ['id'];

    public function movies()
	{
		return $this->belongsToMany(Movie::class);
	}

    public function getSlugAttribute()
	{
		return str_slug($this->name);
	}
}
