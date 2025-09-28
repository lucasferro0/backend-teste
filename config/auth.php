<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    /**
     * [ANÁLISE]
     *
     * - Está usando como default o guard web, que utiliza driver de session, ou seja, autenticação por sessão.
     *   API funciona com autenticação por token, logo tem que utilizar um guard configurado com o driver do sanctum.
     *
     * - Guard web geralmente é utilizado quando estamos trabalho com projeto laravel monolítico
     */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session"
    |
    */

    /**
     * [ANÁLISE]
     *
     * - Faltou criar um guard para utilizar o driver do sanctum e apontar para o provider user
     */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800,

    /**
     * [ANÁLISE]
     *
     * - O token_name deve ficar no arquivo config/sanctum.php para garantir a separação adequada de responsabilidades
     *   O arquivo auth.php é um arquivo de contém configuração de autenticação da aplicação do laravel, gerenciamento de guards e com isso diversos tipos de autenticação.
     *   token_name é uma característica apenas da autenticação da lib do sanctum
     */

    'token_name' => env('TOKEN_NAME', 'token_name'),

    /**
     * [ANÁLISE]
     *
     * - Todos os dados de banking não devem estar aqui
     *   Esses dados poderiam ficar em um arquivo criado config/banking-service.php, por exemplo
     */
    'banking_base_url' => env('BANKING_BASE_URL', null),
    'banking_client_id' => env('BANKING_CLIENT_ID', null),
    'banking_client_secret' => env('BANKING_CLIENT_SECRET', null),
    'banking_request_cooldown' => env('BANKING_REQUEST_COOLDOWN', 1),
];
