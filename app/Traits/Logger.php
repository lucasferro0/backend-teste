<?php

namespace App\Traits;

use Throwable;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use App\Exceptions\BaseException;
use Illuminate\Support\Facades\Log;

trait Logger
{
    /**
     * Manipula a exception para deixar mais legivel
     *
     * @param Throwable $e
     *
     * @return array
     */
    protected static function beautifyException(Throwable $e): array
    {
        return [
            'msg'  => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ];
    }

    /**
     * Cria um log padrão para o Chronos
     *
     * @param string         $description      Não é usado no filtro, pode ser descritivo
     * @param string         $action           Usado no filtro, texto simples
     * @param mixed          $value            Conteúdo do log
     * @param Throwable|null $error            Erro ocorrido (caso seja um log de erro)
     * @param string|null    $userId           ID do usuário, usuário logado do JWT caso null
     * @param string|null    $companyId        ID da empresa, empresa do usuário logado do JWT caso null
     * @param string|null    $entityId         ID da entidade
     * @param string|null    $entity           Entidade
     * @param string         $logLevel         Nivel do log: DEBUG/ERROR/INFO/CRITICAL/WARNING
     * @param string         $logType          Tipo de log: SERVER/AUDIT
     * @param Carbon|null    $requestDatetime  Datetime do ínicio de uma request
     * @param Carbon|null    $responseDatetime Datetime da response de uma request
     *
     * @return array
     */
    public function createLog(
        string $description,
        string $action,
        $value,
        Throwable $error = null, // Atribuindo valor null para um argumento que só pode ter o tipo Throwable
        string $idUser = null, // Mesma coisa
        string $idCompany = null, // Mesma coisa
        string $entityId = null, // Mesma coisa
        string $entity = null, // Mesma coisa
        string $logLevel = 'DEBUG', // Mesma coisa
        string $logType = 'SERVER', // Mesma coisa
        Carbon $requestDatetime = null, // Mesma coisa
        Carbon $responseDatetime = null // Mesma coisa
    ): array {
        try {
            $value    = is_array($value) ? $value : [$value];
            $uuid     = Uuid::uuid4()->toString();
            $logLevel = mb_strtoupper($logLevel);

            if ($error) {
                $errorLog = self::beautifyException($error);
                $value    = array_merge($value, compact('errorLog'));
            }

            /**
             * [ANÁLISE]
             *
             * - O método $this->getUserFromJwt() não existe em lugar nenhum do projeto
             *   Dessa forma, causará um erro ao tentar gravar o log.
             */
            $getUserResponse = $this->getUserFromJwt();
            $requestDuration = null;

            if ($requestDatetime && $responseDatetime) {
                $requestDuration = (float) $requestDatetime->diffInMicroseconds($responseDatetime)
                    / Carbon::MICROSECONDS_PER_MILLISECOND;
            }

            /**
             * [ANÁLISE]
             *
             * - Há dados repetidos, como por exemplo a url
             *
             */
            $context = [
                'description'                   => $description,
                'action'                        => $action,
                'entity_id'                     => $entityId,
                'entity'                        => mb_strtoupper($entity),
                'log_type'                      => mb_strtoupper($logType),
                'log_level'                     => $logLevel,
                'user_id'                       => $idUser ?: data_get($getUserResponse, 'user_id'), // Se o id já está na variável $idUser, por que tentar pegar do array $getUserResponse ?
                'company_id'                    => $idCompany ?: data_get($getUserResponse, 'company_id'), // Mesma coisa da linha de cima
                'admin_user_id'                 => data_get($getUserResponse, 'admin_user_id'),
                'value'                         => $value,
                'uuid'                          => $uuid,
                'date'                          => Carbon::now(),
                'request_datetime'              => $requestDatetime?->format('Y-m-d\TH:i:s.uP'),
                'response_datetime'             => $responseDatetime?->format('Y-m-d\TH:i:s.uP'),
                'request_duration_milliseconds' => $requestDuration,
                'origin'                        => request()->headers->get('origin'),
                'referer'                       => request()->headers->get('referer'),
                'url'                           => request()->url(),
                'ip'                            => request()->server('REMOTE_ADDR'),
                'endpoint'                      => [
                    'url'    => request()->url(),
                    'method' => request()->getMethod(),
                ],
            ];

            /**
             * [ANÁLISE]
             *
             * - O canal utilizado para esse log não existe no arquivo logging.php
             */
            Log::channel('log_service')->{$logLevel}($context['description'], $context);
        } catch (Throwable $e) {
            /**
             * [ANÁLISE]
             *
             * - Esses returns não estão sendo usados
             */
            return [
                'success' => false,
                'message' => 'Falha ao enviar Log',
                'data'    => $e,
            ];
        }

        /**
         * [ANÁLISE]
         *
         * - Esses returns não estão sendo usados
         */

        return [
            'success' => true,
            'message' => 'Log feito com sucesso',
            'data'    => $context,
            'uuid'    => $uuid,
        ];
    }

    /**
     * Tratamento padrão de exceptions
     *
     * @param Throwable   $exception
     * @param mixed|null  $data
     * @param string|null $idEntity
     * @param string|null $entity
     * @param string      $level
     *
     * @return void
     */
    public function defaultErrorHandling(
        Throwable $exception,
        $data = null,
        string $idEntity = null, // Valor default null para argumento do tipo string
        string $entity = null, // Valor default null para argumento do tipo string
        string $level = 'ERROR'
    ): void {
        // Caso seja um erro esperado BaseException, continua sem criar log
        // de erro inesperado
        if ($exception instanceof BaseException) {
            throw $exception;
        }

        $description = get_called_class();

        // Formatando o nome do método que o erro ocorreu
        $trace    = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $function = $trace[count($trace) - 1]['function'];
        $action   = mb_strtoupper(Str::snake($function)) . '_ERROR';

        $this->createLog(
            $description,
            $action,
            $data,
            $exception,
            null,
            null,
            $idEntity,
            $entity,
            $level
        );

        /**
         * [ANÁLISE]
         *
         * - Foi deixado esse dump() no código, isso é um erro e pode ser uma vulnerabilidade.
         */
        dump($exception);

        /**
         * [ANÁLISE]
         *
         * - Esse comentário abaixo já descreve a falta de confiabilidade nessa estrutura de log.
         *   Lança uma exception para evitar log duplicado.
         */

        // Para evitar propagação de log duplicado, o erro é propagado como
        // BaseException
        throw new BaseException(
            'UNKNOW_ERROR_TRY_AGAIN',
            0
        );
    }
}
