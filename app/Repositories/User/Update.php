<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use App\Domains\User\Update as UpdateDomain;

/**
 * [ANÁLISE]
 *
 * - É necessário ver se tem necessidade de utilizar repository no projeto ou não
 *   Dependendo do projeto, o repository pode encaixar bem ou não, isso também a depender dos cenários.
 *   O repository adiciona uma complexidade a mais no código para a camada de consultas, visto que a proposta dele é caso por ventura tenhamos que trocar de ORM ...
 *   Porém, a realidade é que dependendo da implementação do repository, ele vai deixar bastante engessado o código,
 *   podendo causar problemas de performance com várias consultas,
 *   e adicionando uma complexidade que ao meu ver, não tem tanta necessidade assim,
 *   visto que o eloquent que é o ORM do laravel já foi feito para isso, ele já nos dá muita coisa pronta.
 *   Porém, caso a escolha do repository seja para encapsular consultas grandes e complexas,
 *   nesse caso há vantagem sim, porque podemos reutilizar código e melhorar e legibilidade do código
 *
 * - Ao usar repository, os métodos dos repositories retornam arrays, o que prejudica no desenvolvimento
 *   Não é uma boa prática retornar array, passar array como argumento e tal, pois dentro do array pode ter muita coisa
 *   e nós não conseguimos utilizar o intelisense para saber se tem alguma key que queremos acessar ou não, dessa forma, perdemos a vantagem do intelisense.
 */
class Update extends BaseRepository
{
    /**
     * Parâmetros de criação de usuário
     *
     * @var UpdateDomain
     */
    protected UpdateDomain $domain;

    /**
     * Setar a model do usuário
     *
     * @return void
     */
    public function setModel(): void
    {
        $this->model = User::class;
    }

    public function __construct(UpdateDomain $domain)
    {
        $this->domain = $domain;

        parent::__construct();
    }

    /**
     * Modificação de usuário
     *
     * @return array
     */
    public function handle(): array
    {
        $this->builder->where('company_id', $this->domain->companyId);

        return $this->update(
            $this->domain->id,
            array_filter(
                [
                    'name'     => $this->domain->name,
                    'email'    => $this->domain->email,
                    'password' => $this->domain->password,
                    'type'     => $this->domain->type,
                ]
            )
        );
    }
}
