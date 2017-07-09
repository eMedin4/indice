<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $guarded = [];

    public function characters()
    {
    	return $this->belongsToMany(Character::class)->withPivot('order');
    }

    public function genres()
    {
    	return $this->belongsToMany(Genre::class);
    }

    public function critics()
    {
    	return $this->hasMany(Critic::class);
    }

    public function listing()
    {
        return $this->belongsToMany(Listing::class, 'list_movie', 'movie_id', 'list_id');
    }

    public function theatre()
    {
        return $this->hasOne(Theatre::class);
    }

    public function movistarSchedule()
    {
        return $this->hasOne(MovistarSchedule::class);
    }


    /*
        ATTRIBUTES
    */

    public function getAvgStarsAttribute()
    {
        return $this->stars($this->avg);    
    }

    public function getFaStarsAttribute()
    {
        return $this->stars($this->fa_rat);
    }

    public function getImStarsAttribute()
    {
        if (!$this->im_rat) return NULL;
        return $this->stars($this->im_rat);

    }

    public function getRtStarsAttribute()
    {
        if (!$this->rt_rat) return NULL;
            return $this->stars($this->rt_rat / 10);
    }

    public function stars($value)
    {
        switch (true) {
            case ($value >= 8): return 5;
            case ($value >= 7): return 4;
            case ($value >= 6): return 3;
            case ($value >= 5): return 2;
            case ($value >= 4): return 1;
            default: return 0;
        }
    }
}
