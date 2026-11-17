<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',  
     'http://localhost:5174',
        'http://localhost:5175',// front en développement local
        'https://lenuxwood-frontend.vercel.app', 
           'https://lenuxwood.com',
        'https://lenuxwood.com',// TODO: remplacer par ton vrai domaine Vercel
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
