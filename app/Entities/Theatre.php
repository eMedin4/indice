<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Theatre extends Model
{

	public $timestamps = false;
	protected $dates = ['date'];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function getReleaseAttribute($test)
    {
        return $test;
    }

    public function getTestAttribute()
    {
        return 'hola';
    }


}
