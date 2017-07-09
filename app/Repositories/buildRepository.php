<?php

namespace App\Repositories;

use App\Entities\Character;
use App\Entities\Comment;
use App\Entities\Critic;
use App\Entities\Genre;
use App\Entities\Listing;
use App\Entities\Movie;
use App\Entities\MovistarSchedule;
use App\Entities\Param;
use App\Entities\User;
use App\Entities\Theatre;


class BuildRepository {


    public function newList($name, $movie, $ordered, $description, $user, $position)
    {
        if (Listing::where([['name', '=', $name], ['user_id', '=', $user]])->count() > 0) {
            return ['state' => false, 'error' => 'duplicate', 'message' => 'Esta lista ya existe'];
        }

        $list = New Listing;
        $list->name = $name;
        if ($description) $list->description = $description;
        $list->ordered = $ordered;
        $list->relevance = 0;
        $list->user_id = $user;
        $list->save();
        if ($position == 'add-to-list') { //si estamos añadiendo a la vez alguna película $movie será su id, sino será 0
            $list->movies()->attach($movie, ['order' => 0]);
        }
        return ['state' => true, 'name' => $name, 'id' => $list->id];
    }

    public function updateList($name, $description, $ordered, $id)
    {
        $list = Listing::find($id);
        $list->name = $name;
        $list->description = $description;
        $list->save();
    }

    public function addList($listId, $movieId)
    {
        $list = Listing::find($listId);
        if (!$list->movies->contains($movieId)) {
            $max = $list->movies()->max('list_movie.order');
            $list->movies()->attach($movieId, ['order' => $max + 1]);
            $list->touch();
        }
    }

    public function extractList($listId, $movieId)
    {
        $list = Listing::find($listId);
        $del = $list->movies()->detach($movieId);
        return $del ? 'deleted' : 'error';
    }

    public function deleteList($listId)
    {
        $list = Listing::find($listId);
        /*$list->savedToUsers()->detach();*/
        $list->movies()->detach();
        $del = Listing::where('id', $listId)->delete();
        return $del ? 'deleted' : 'error';
    }

    public function likeList($id, $userId)
    {
        $list = Listing::find($id);
        echo 'like';
        if (!$list->likedToUsers->contains($userId)) {
            $list->likedToUsers()->attach($userId);
        }
    }

    public function dislikeList($id, $userId)
    {
        echo 'dislike';
        $list = Listing::find($id);
        $list->likedToUsers()->detach($userId);
    }

}
