<?php
use Yetione\EventBus\Listeners\TestListener;

return [
    'producer'=>'event_bus',
    'listener'=>'event_bus',
    'listeners'=>[
        'auth_users'=>[
            'source'=>'auth',
            'scope'=>'users',
            'name'=>'project_created',
            'listener'=> TestListener::class
        ],
    ],

];
