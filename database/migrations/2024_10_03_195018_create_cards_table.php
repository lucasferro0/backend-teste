<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * [ANÁLISE]
     *
     * - Faltou criar index para a fk de account_id para ter melhor desempenho
     *
     * - Dependendo do SGBD não é interessante usar uuid como pk, pois pode ter problema de performance.
     *   Usar integer vai ser mais rápido, porque uuid é string.
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_id');
            $table->uuid('external_id')->nullable();
            $table->enum('status', ['BLOCK', 'ACTIVE']); // Trocaria essa coluna por uma fk e criaria uma tabela chamada cards_status. Dessa forma ficaria mais escalável.
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')
                ->references('id')
                ->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
};
