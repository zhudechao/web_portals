<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

///usr/local/Cellar/php\@7.2/7.2.14/bin/php /Users/zhudechao/githup/web_portals/src/artisan word
Artisan::command('word', function () {
    $this->comment((new \App\Console\Commands\word())->handle());
})->describe('Display an inspiring quote');

//http://collocationdictionary.freedicts.com/words
///usr/local/Cellar/php\@7.2/7.2.14/bin/php /Users/zhudechao/githup/web_portals/src/artisan words
Artisan::command('words', function () {
    $this->comment((new \App\Console\Commands\words())->handle());
})->describe('Display an inspiring quote');

Artisan::command('aliexpress', function () {
    $this->comment((new \App\Console\Commands\aliexpress())->handle());
})->describe('Display an inspiring quote');
Artisan::command('jiucha', function () {
    $this->comment((new \App\Console\Commands\jiucha())->handle());
})->describe('Display an inspiring quote');
