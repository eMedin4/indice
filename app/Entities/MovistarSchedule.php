<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MovistarSchedule extends Model
{
    protected $table = 'movistar_schedule';
    protected $dates = ['time'];

    public function movie()
	{
		return $this->belongsTo(Movie::class);
	}

    /*
        ATTRIBUTES
    */

	public function getFormatTimeAttribute()
    {
    	$now = Carbon::create('2017', '02', '13', '6', '00', '00');
    	if ($this->time < $now) {
    		return '<time class="tv-alert"><span>hace</span> ' . $this->time->diffInMinutes($now) . '<span> m.</span></time>';
    	} elseif ($this->time->isToday()) {
    		return '<time>' . $this->time->format('G:i') . ' <span>hoy</span></time>';
    	} else {
    		return '<time>' . $this->time->format('G:i') . ' <span>' . $this->time->formatLocalized('%a') . '</span></time>';
    	}
    }
}
