<?php

namespace App\Repositories\Company;

use App\Models\Company;
use App\Repositories\BaseRepository;

class CanUseDocumentNumber extends BaseRepository
{
    /**
     * CNPJ
     *
     * @var string
     */
    protected string $documentNumber;

    /**
     * Setar a model da empresa
     *
     * @return void
     */
    public function setModel(): void
    {
        $this->model = Company::class;
    }

    public function __construct(string $documentNumber)
    {
        $this->documentNumber = $documentNumber;

        parent::__construct();
    }


    /**
     * [ANÁLISE]
     *
     * - Repostory com responsabilidade errada, visto que está realizando uma validação.
     *   A responsabilidade do repository é apenas de se comunicar com o banco de dados e ele não deve fazer nenhuma validação.
     *   De acordo com o padrão do projeto, a validação de regra de negócio tem que ficar na camada de Domain
     */
    
    /**
     * Valida se o documento é único
     *
     * @return bool
     */
    public function handle(): bool
    {
        $user = $this->builder
            ->where('document_number', $this->documentNumber)
            ->first();

        return is_null($user);
    }
}
