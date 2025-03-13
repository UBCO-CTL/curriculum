<?php

use Illuminate\Support\Facades\Facade;

return [

    'login_url' => env('LOGIN_URL'),

    'register_url' => env('REGI_URL'),

    'aliases' => Facade::defaultAliases()->merge([
        'PDF' => Barryvdh\DomPDF\Facade::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
    ])->toArray(),

];
