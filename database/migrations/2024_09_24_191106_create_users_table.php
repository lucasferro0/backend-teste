<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * [ANÁLISE]
     *
     * - Faltou criar index para a fk de company_id para ter melhor desempenho
     *
     * - Dependendo do SGBD não é interessante usar uuid como pk, pois pode ter problema de performance.
     *   Usar integer vai ser mais rápido, porque uuid é string.
     *
     * - Ao trocar a pk para um integer, devemos ter cuidado nos endpoints do cartão e de conta, visto que usa o id do usuário para mostrar o cartão e a conta dele.
     *   Dessa forma, temos uma brecha,
     *   pois o id é integer e alguém pode ficar chutando valores sequenciais até acertar o id de um usuário e dessa forma conseguiria pegar os dados da conta ou do cartão.
     *   Para solucionar isso, seria ideal usar algum hash, uuid, ou combinação de strings nos endpoints de show de conta, e cartão para ter uma maior segurança.
     *   Além de que teria que reforçar com validações no código também.
     *
     * - Outro ponto sobre performance, é o tamanho das colunas da tabela, algumas colunas poderiam ter tamanhos menores.
     *   Como por exemplo a coluna name, poderia ser um varchar(100) caracteres por exemplo.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', ['USER', 'VIRTUAL', 'MANAGER']);
            $table->string('document_number')->unique();
            $table->string('email')->unique();
            $table->uuid('company_id'); // Eu colocaria essa declaração de criação dessa mais em cima logo após a pk, para facilitar quando fosse olhar os metadados da tabela.
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
