<?php

namespace App\Http\Middleware\App;

use Closure;
use App\Traits\ResponseHelpers;

/**
 * [ANÁLISE]
 *
 * - Não é uma boa prática deixar esse middleware sem o sufixo Middleware
 *
 * - Esse tipo de gerenciamento de policies, acredito que fica mais escalável com RBAC (role-based access control) via banco de dados.
 *   Até porque, podemos construir toda uma estrutura no banco de dados para isso ficar dinâmico e colocar também feature flags nos endpoints
 *
 * - Além disso, vejo que ocorreu uma confusão entre a camada de Policy e Domain na estrutura do projeto
 *
 * - Vai dar erro caso alguma Policy dê errado na hora de gravar o log
 *
 * - Não seguiu laravel conventions no pattern de Policy
 */
class ResourcePolicies
{
    use ResponseHelpers; // Herda dessa trait, porém não utiliza nenhum método

    private const POLICIES_NAMESPACE = 'App\Policies\App';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->applyPolicy();

        return $next($request);
    }

    /**
     * Aplica a validação da política, encontrando a classe
     * de política corresondente com a controller e o método da
     * classe politica também correspondente com o método da controller
     *
     * @return void
     */
    public function applyPolicy()
    {
        $action = explode('\\', request()->route()->getActionName());
        $parameters = array_values(request()->route()->parameters);
        $parameters[] = request()->all();

        $resource = explode('@', end($action))[0];
        $resource = str_replace('Controller', '', $resource);

        $method = explode('@', end($action))[1];

        app(self::POLICIES_NAMESPACE . '\\' . $resource)->{$method}(...$parameters);
    }
}
