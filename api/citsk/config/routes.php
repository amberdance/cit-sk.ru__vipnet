<?php
$routes = [
    [
        'path'       => '/^\/api\/auth\//',
        'controller' => 'Citsk\Controllers\UserController',
    ],

    [
        'path'       => '/^\/api\/refs\//',
        'controller' => 'Citsk\Controllers\ReferenceController',
    ],

    [
        'path'       => '/^\/api\/service\//',
        'controller' => 'Citsk\Controllers\ServiceAPIController',
    ],
];
