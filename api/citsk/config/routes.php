<?php
$routes = [
    [
        'path'       => '/^\/auth\//',
        'controller' => 'Citsk\Controllers\UserController',
    ],

    [
        'path'       => '/^\/refs\//',
        'controller' => 'Citsk\Controllers\ReferenceController',
    ],

    [
        'path'       => '/^\/service\//',
        'controller' => 'Citsk\Controllers\ServiceAPIController',
    ],
];
