<?php


    return [

        // Default Port
        'default_port' => env('IBSng_port',80),

        // Default Admin UserName
        'default_admin_username' => env('IBSng_admin_username', 'system'),

        // Default Admin Password
        'default_admin_password' => env('IBSng_admin_password', 'system'),

        // Default User Agent
        'default_requests_agent' => env('IBSng_user_agent', 'BlackPanda IBSng Wrapper'),

        // Default User Credit
        'default_user_credit' => env('IBSng_default_credit' , 9999),

        // Default User Group Name
        'default_user_group' => env('IBSng_default_group' , 'Test'),



    ];
