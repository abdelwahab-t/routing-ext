<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Controller Directories
    |--------------------------------------------------------------------------
    |
    | Define the directories where the package should scan for controllers
    | with route attributes. Paths are relative to the application base path.
    | You may use wildcard (*) patterns for dynamic directory segments.
    |
    */

    'controllers_directories' => [
        'app/Http/Controllers',
        'app/Modules/*/App/Http/Controllers',
    ],

];
