<?php

namespace PK\Http;

class Request
{
    /** @var string */
    private $method;

    /** @var string */
    private $path;

    /** @var array */
    private $params;

    /** @var array */
    private $headers;

    /** @var array */
    private $files;

    /**
     * Request constructor
     *
     * @param array $server Список, содержащий серверные параметры из $_SERVER
     * @param array $post   Список, содержащий параметры POST-запроса из $_POST
     * @param array $files  Список, содержащий список загружаемых файлов из $_FILES
     */
    public function __construct(array $server = [], array $post = [], array $files = [])
    {
        $this->method = isset($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD'] : 'GET';
        $this->path    = isset($server['REQUEST_URI']) ? $server['REQUEST_URI'] : '/';
        $parameters   = !empty($this->path) ? parse_url($this->path) : '';

        if (isset($parameters['query'])) {
            parse_str($parameters['query'], $query);
        }

        $server['CONTENT_TYPE'] = isset($server['CONTENT_TYPE']) ? $server['CONTENT_TYPE'] : '';

        if (preg_match('/^application\/json.*/', $server['CONTENT_TYPE'])) {
            $postData = file_get_contents('php://input');
            $post = json_decode($postData, true);
        }

        foreach ($server as $name => $value) {
            if (preg_match('/HTTP_\w+/', $name)) {
                $this->headers[$name] = $value;
            }
        }

        $this->params = isset($query) ? array_merge($query, $post) : $post;
        $this->path = isset($parameters['path']) ? $parameters['path'] : '';
        $this->path = preg_replace('/\/$/', '', $this->path);
        $this->files = $files;
    }

    /**
     * Возвращает метод запроса
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Возвращает маршрут запроса
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Возвращает список параметров или значение параметра, если передано его имя
     *
     * @param null|string $key Имя параметра
     *
     * @return array|mixed
     */
    public function getParams($key = null)
    {
        if ($key) {
            if (isset($this->params[$key])) {
                return $this->params[$key];
            }

            return null;
        }

        return $this->params;
    }

    /**
     * Возвращает список заголовков запроса или значение заголовка, если передано его имя
     *
     * @param null|string Имя заголовка
     *
     * @return array|mixed
     */
    public function getHeaders($key = null)
    {
        if ($key) {
            if (isset($this->headers[$key])) {
                return $this->headers[$key];
            }
        }

        return $this->headers;
    }

    /**
     * Возвращает список загружаемых файлов
     *
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
