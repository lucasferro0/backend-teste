<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HealthCheckController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Healthcheck
Route::get('healthcheck', [HealthCheckController::class, 'healthCheck']);

// Users (Rota Pública)
Route::prefix('users')->group(function () {
    Route::post('register', [UserController::class, 'register']);

    Route::post('login', [UserController::class, 'login'])->middleware('auth.basic'); // Poderia deixar login em um outro controller como por exemplo AuthController, onde ficaria tudo login, logout, me e refresh
});

Route::group(['middleware' => ['auth:sanctum', 'policies.app']], function () {
    // Companies
    Route::prefix('company')->group(function () {
        Route::get('', [CompanyController::class, 'show']);

        Route::patch('', [CompanyController::class, 'update']);
    });

    // Users
    Route::prefix('users')->group(function () {
        Route::get('', [UserController::class, 'index']);

        Route::get('{id}', [UserController::class, 'show']);

        Route::post('', [UserController::class, 'create']);

        Route::patch('{id}', [UserController::class, 'update']);

        // Accounts
        Route::prefix('{id}/account')->group(function () {
            Route::get('', [AccountController::class, 'show']);

            Route::put('active', [AccountController::class, 'active']);

            Route::put('block', [AccountController::class, 'block']);

            Route::post('register', [AccountController::class, 'register']);
        });

        Route::prefix('{id}/card')->group(function () {
            Route::get('', [CardController::class, 'show']);

            Route::post('register', [CardController::class, 'register']);
        });
    });
});

/**
 * [ANÁLISE]
 *
 * Pensando em escalabilidade e separação de responsabilidades, bem como em legibilidade, eu faria as seguintes alterações.
 *
 * - Versionamento nas rotas
 *      Faltou realizar o versionamento das rotas. Visto que será uma api,
 *      e poderá ter diversos clients integrados, é fundamental ter versionamento nas rotas.
 *      Dessa forma, será possível evoluir o projeto, criando v2, v3 e não teremos a preocupação de quebrar algum client.
 *
 * - Realizando o versionamento das rotas, é importante ter versionamento nos outros arquivos do projeto
 *   É importante versionar Middlewares, FormRequests, Controllers ...
 *
 * - Separação de responsabilidades nas rotas por conta da escalabilidade do projeto
 *      É uma boa prática separar rotas por contextos, dessa forma não incharia o arquivo api.php principal com todas as rotas da aplicação.
 *      Nesse caso, poderia ser separado em 3 contextos principais: api-financial, api-management, api-auth.
 *      A parte de financial, ficaria todas as rotas que tem relação com a parte financeira, ou seja, cartão, contas bancárias, movimentações, transações ...
 *      A parte de management, ficaria o que tem a ver com gerencimento, ou seja, usuário, empresa, roles, permissions, menus ...
 *      A parte de api-auth, ficaria apenas rotas que tem a ver com autenticação, ou seja, login, logout, me, refresh ...
 *
 * - Usaria mais os recursos do laravel e seguiria as laravel conventions
 *   Nesse caso, poderia melhorar a legibilidade também utilizando as rotas Route::apiResource().
 *   Além disso, seguiria as laravel conventions, mudando o nome dos métodos do controller para index, store, update, destroy e show
 *
 * - Pensando em segurança, e por tratar de uma api que mexe com dados financeiros, é fundamental ter roles e permissions implementado, bem como feature flags
 *    Como se trata de uma api que mexe com dados financeiros,
 *    é fundamental ter um middleware para verificar as roles dos usuários, bem como as permissions das roles desses usuários.
 *    Dessa forma, conseguimos evitar problemas de acesso a recursos que não deveriam ser acessados, bem como conseguimos realizar revogações de permissions.
 *    Além disso, é importante ter feature flags, visto que um erro pode ocasionar um prejuízo enorme.
 *    Digamos que há um bug no registro do cartão, que cria o cartão na instituição bancária, porém não persiste no banco de dados que a api usa,
 *    E o usuário se depara com o erro, e fica clicando para criar repetidas vezes ...
 *    Nesse caso, com feature flags, poderíamos desabilitar essa funcionalidade temporariamente, corrigir o bug com segurança, e após corrigido, ativar a funcionalidade.
 *
 *  - Abaixo está um exemplo aproximado da minha ideia:
 *   Route::group(['prefix' => 'v1', 'middleware' => ['sanctum']], function () {
 *        Route::get('up', [HealthCheckController::class, 'healthCheck'])->withoutMiddleware(['sanctum']);
 *        Route::group(['prefix' => 'auth'], base_path('routes/api/v1/api-auth.php'));
 *        Route::group(['prefix' => 'management'], base_path('routes/api/v1/api-management.php'));
 *        Route::group(['prefix' => 'finacial'], base_path('routes/api/v1/api-financial.php'));
 *   });
 *
 *   Dentro de cada arquivo, por exemplo o api-financial.php, teria rotas como os exemplos abaixo:
 *
 *   Route::apiResource('users.accounts', AccountController::class);
 *
 *   É importante destacar que utilizar agrupamento de rota é uma boa prática,
 *   porém, tem que tomar cuidado com o excesso, visto que aumenta a complexidade de leitura, pois aumenta a quantidade de identação.
 *   Quanto mais identação no código, mais complexo vai ser a leitura dele, essa regra vem do object calisthenics (o ideal é ter um nível de identação por método).
 */
