<?php

if (!function_exists('sm_icon')) {

    function sm_icon(string $name): string
    {
        $icons = [

            'students'      => 'bi bi-mortarboard-fill',
            'parents'       => 'bi bi-people-fill',
            'teachers'      => 'bi bi-person-workspace',
            'classes'       => 'bi bi-building',
            'trips'         => 'bi bi-bus-front-fill',
            'buses'         => 'bi bi-bus-front-fill',
            'finance'       => 'bi bi-cash-stack',
            'announcements' => 'bi bi-megaphone-fill',
            'settings'      => 'bi bi-gear-fill',
            'logout'        => 'bi bi-box-arrow-right',
            'dashboard'     => 'bi bi-speedometer2',
            'health'        => 'bi bi-heart-pulse-fill',
            'calendar'      => 'bi bi-calendar-event-fill',
            'messages'      => 'bi bi-chat-dots-fill',
            'users'         => 'bi bi-people-fill',
            'home'          => 'bi bi-house-fill'

        ];

        return $icons[$name] ?? 'bi bi-circle';
    }

}