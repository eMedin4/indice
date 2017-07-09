<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $table = 'lists';

    public function movies()
    {
    	return $this->belongsToMany(Movie::class, 'list_movie', 'list_id', 'movie_id')->withPivot('order');
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function likedToUsers()
    {
        return $this->belongsToMany(User::class, 'list_user', 'list_id', 'user_id');
    }

    /*
        ATTRIBUTES
    */

    public function getAutolinkDescriptionAttribute()
    {
        $str = htmlentities($this->description);
        $attributes = array('rel' => 'nofollow', 'target' => '_blank');
        $str = str_replace(["http://www", "https://www"], "www", $str);
        $attrs = '';

        foreach ($attributes as $attribute => $value) {
            $attrs .= " {$attribute}=\"{$value}\"";
        } 

        $str = ' ' . $str;
        $str = preg_replace('`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i', '$1<a href="$2"'.$attrs.'>$2</a>', $str);
        $str = preg_replace('`([^"=\'>])((www).[^\s<]+[^\s<\.)])`i', '$1<a href="http://$2"'.$attrs.'>$2</a>', $str);
        $str = substr($str, 1);
        return $str;
    }
}
