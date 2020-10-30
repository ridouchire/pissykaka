<?php

namespace PK\Http;

class Response
{
    /** @var string */
    private const API_VERSION = '1.0.0';

    /** @var array */
    private $data;

    /** @var int */
    private $code;

    /** @var array */
    private $headers;

    /** @var Throwable|null */
    private $error;

    /**
     * Response constructor
     *
     * @param array $data    Список с данными ответа
     * @param int   $code    HTTP-код ответа
     * @param array $headers Список заголовков ответа
     */
    public function __construct(array $data = [], int $code = 200, array $headers = ['Content-type: application/json'])
    {
        $this->data = $data;
        $this->code = $code;
        $this->headers = $headers;
        $this->error = null;
    }

    /**
     * Возвращает список заголовков ответа
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Возвращает список с данными ответа
     *
     * @return array
     */
    public function getBody(): array
    {
        $body = [
            'payload' => $this->data,
            'version' => self::API_VERSION,
            'error'   => null
        ];

        if ($this->error) {
            $body['error'] = $this->getErrorData($this->error);
        }

        return $body;
    }

    /**
     * Возвращает код ответа
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Задаёт исключение для содержания информации о нём в ответе
     *
     * @param Throwable $e Исключение
     *
     * @return self
     */
    public function setException(\Throwable $e): self
    {
        $this->error = $e;

        return $this;
    }

    /**
     * Возвращает информацию о исключении
     *
     * @param Throwable $e Исключение
     *
     * @param array
     */
    private function getErrorData(\Throwable $e): array
    {
        return [
            'type'    => get_class($e),
            'message' => $e->getMessage(),
        ];
    }
}
