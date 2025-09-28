<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * [ANÁLISE]
     *
     * - Dependendo do SGBD não é interessante usar uuid como pk, pois pode ter problema de performance.
     *   Usar integer vai ser mais rápido, porque uuid é string.
     *   Nesse caso de conta, devemos tomar cuidado ao usar uma pk integer, pois ela será um id sequencial.
     *   Dessa forma, podemos estar abrindo uma brecha, para conseguirem chutar ids das contas e consultarem via endpoint.
     *   Minha sugestão seria usar pk integer e nos endpoints de show não utilizar a pk da conta para buscar,
     *   e sim utilizar algum hash, ou combinação de caracteres que formam uma string, dessa forma será difícil chutar os valores.
     *
     * - Faltou criar index para user_id para ter melhor desempenho
     *
     * - Faltou criar index para external_id para ter melhor desempenho
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('external_id')->nullable();
            $table->enum('status', ['BLOCK', 'ACTIVE']); // Trocaria essa coluna por uma fk e criaria uma tabela chamada accounts_status. Dessa forma ficaria mais escalável.
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
