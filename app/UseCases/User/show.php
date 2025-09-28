<?php

namespace App\UseCases\User;

use Throwable;
use App\UseCases\BaseUseCase;
use App\Repositories\User\Find;

class show extends BaseUseCase // Nome de classe fora do padrão da PSR12
{
    /**
     * Id do usuário
     *
     * @var string
     */
    protected string $a; // Péssima prática utilizar variável desse jeito.

    /**
     * Id da empresa
     *
     * @var string
     */
    protected string $b; // mesma coisa

    /**
     * Usuário
     *
     * @var array|null
     */
    protected ?array $c; // mesma coisa

    public function __construct(string $a, string $b) // mesma coisa
    {
        $this->a = $a; // mesma coisa
        $this->b = $b; // mesma coisa
    }

    /**
     * Encontra o usuário
     *
     * @return void
     */
    protected function find(): void
    {
        $this->c = (new Find($this->a, $this->b))->handle(); // mesma coisa
    }

    /**
     * Retorna usuário, se encontrado
     */
    public function handle(): ?array
    {
        try {
            $this->find();
        } catch (Throwable $th) {
            $this->defaultErrorHandling(
                $th,
                [
                    'a' => $this->a,
                    'b' => $this->b,
                ]
            );
        }

        return $this->c; // mesma coisa
    }
}
