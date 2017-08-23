<?php

namespace App\Console\Commands;

use App\Libraries\icback;
use Illuminate\Console\Command;

class cartelera extends Command
{

    protected $signature = 'command:cartelera';
    protected $description = 'Descarga cartelera actual';
    protected $icback;

    public function __construct(Icback $icback)
    {
        parent::__construct();
        $this->icback = $icback;
    }

    public function handle()
    {
        $this->icback->fromCartelera();
    }
}
