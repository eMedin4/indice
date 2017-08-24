<?php

namespace App\Console\Commands;

use App\Libraries\movistarScrap;
use Illuminate\Console\Command;

class movistar extends Command
{

    protected $signature = 'command:movistar';
    protected $description = 'Descarga progamación de Movistar';
    protected $movistarScrap;

    public function __construct(movistarScrap $movistarScrap)
    {
        parent::__construct();
        $this->movistarScrap = $movistarScrap;
    }

    public function handle()
    {
        $this->movistarScrap->init();
    }
}
