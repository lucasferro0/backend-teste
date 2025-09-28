<?php

namespace App\Policies\App;

use App\Policies\BasePolicy;

/**
 * [ANÁLISE]
 *
 * - As policies não seguiram as laravel conventions
 *
 * - Houve um conflito entre policy pattern e domain pattern no projeto
 *   É ideal escolher um pattern apenas e utilizá-lo mantendo o padrão.
 *
 * - O diretório App dentro de Policies não tem sentido, visto que não agrega em nada na contextualização da arquitetura do projeto.
 */
class Account extends BasePolicy
{
    /**
     * Política de acesso necessária para registrar a conta
     * (Apenas gestor da mesma empresa pode acessar ou próprio usuário logado)
     *
     * @return void
     */
    public function register(string $userId): void
    {
        $this->isManagerOrOwnerResource($userId);
    }

    /**
     * Política de acesso necessária para bloquear a conta
     * (Apenas gestor da mesma empresa pode acessar ou próprio usuário logado)
     *
     * @return void
     */
    public function block(string $userId): void
    {
        $this->isManagerOrOwnerResource($userId);
    }

    /**
     * Política de acesso necessária para ativar a conta
     * (Apenas gestor da mesma empresa pode acessar ou próprio usuário logado)
     *
     * @return void
     */
    public function active(string $userId): void
    {
        $this->isManagerOrOwnerResource($userId);
    }

    /**
     * Política de acesso necessária para visualizar a conta
     * (Apenas gestor da mesma empresa pode acessar ou próprio usuário logado)
     *
     * @return void
     */
    public function show(string $userId): void
    {
        $this->isManagerOrOwnerResource($userId);
    }
}
