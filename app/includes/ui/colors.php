<?php

if (!function_exists('sm_color')) {

    function sm_color(string $name): string
    {
        $colors = [

            'primary'   => '#2563eb',
            'success'   => '#16a34a',
            'danger'    => '#dc2626',
            'warning'   => '#f59e0b',
            'info'      => '#0ea5e9',

            'dark'      => '#1f2937',
            'light'     => '#f4f6f9',

            'white'     => '#ffffff',
            'border'    => '#e5e7eb'

        ];

        return $colors[$name] ?? '#000000';
    }

}