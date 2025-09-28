<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\UseCases\Company\Show;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\Company\UpdateRequest;
use App\Http\Resources\Company\ShowResource;
use App\Http\Resources\Company\UpdateResource;
use App\Domains\Company\Update as UpdateDomain;
use App\Repositories\Company\Update as CompanyUpdate;

class CompanyController extends Controller
{
    /**
     * Endpoint de dados de empresa
     *
     * GET api/company
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $response = (new Show(Auth::user()->company_id))->handle();

        return $this->response(
            new DefaultResponse(
                new ShowResource($response)
            )
        );
    }

    /**
     * Endpoint de modificação de empresa
     *
     * PATCH api/company
     *
     * @return JsonResponse
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        /**
         * [ANÁLISE]
         *
         * - Realizando essa query abaixo estou jogando fora o pattern do repository
         *
         * - Além de que estou causando um problema de performance, pois a empresa é buscada duas vezes nesse método update do controller
         *   A primeira vez, é buscada aqui (new CompanyUpdate($dominio))->handle(); e esse método handle já retorna os dados da empresa, poderia utilizar.
         *   A segunda consulta é realizada aqui Company::find(Auth::user()->company_id)->first()
         *
         * - Outro ponto importante é que o padrão que estava sendo utilizado que UseCase não está sendo utilizado aqui
         *   Tem regra de negócio no controller e tem chamada de repository no controller, isso não é um padrão MVC e não favorece à reutilização de código.
         *
         * - Além disso, está chamando a classe de Domain diretamente
         *
         * - A classe UpdateDomain abaixo não faz nada quando é chamado o método handle (código morto)
         */

        $dominio = (new UpdateDomain(
            Auth::user()->company_id,
            $request->name,
        ))->handle();
        (new CompanyUpdate($dominio))->handle();

        $resposta = Company::find(Auth::user()->company_id)->first()->toArray();

        return $this->response(
            new DefaultResponse(
                new UpdateResource($resposta)
            )
        );
    }
}
