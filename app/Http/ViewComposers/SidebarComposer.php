<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\showRepository;
use Auth;

class SidebarComposer
{
    private $show;

    public function __construct(showRepository $show)
    {
        $this->show = $show;
    }

    public function compose(View $view)
    {
        $popular = $this->show->popularLists();

        if (Auth::check()) {
            $mylists = $this->show->listsByUser(Auth::user()->id);
            $mylikelists = $this->show->likeLists(Auth::user()->id);
        } else {
            $mylists = '';
            $mylikelists = '';
        }
        $view->with(['popular' => $popular, 'mylists' => $mylists, 'mylikelists' => $mylikelists]);
    }
}