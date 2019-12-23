<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */

    'supportsCredentials' => false,
    'allowedOrigins' => [],
    'allowedOriginsPatterns' => [
        '/^.*127.0.0.1.*$/',
        '/^.*localhost.*$/',
        '/^.*\.loc.*$/',
        '/^.*\.geaecuador.ec$/',
        '/^.*\.geainternacional.com$/',
    ],
    'allowedHeaders' => ['*'],
    'allowedMethods' => ['*'],
    'exposedHeaders' => [],
    'maxAge' => 0,

];
