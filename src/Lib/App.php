<?php

namespace App\Lib;

use App\Services\GenerateImport;


class App
{
    public static function run() {
        $generator = new GenerateImport('ywe3crmpll');

        $generator->loadResources();

        $generator->generate();
    }
}
