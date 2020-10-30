<?php

use PHPUnit\Framework\TestCase;
use PK\Http\Response;
use PK\Exceptions\Http\NotFound;

class ResponseTest extends TestCase
{
    private $res;

    public function testGetHeaders()
    {
        $this->assertEquals(['Content-type: application/json'], $this->res->getHeaders());
    }

    public function testGetCode()
    {
        $this->assertEquals(404, $this->res->getCode());
    }

    public function testGetBody()
    {
        $this->assertEquals([
            'payload' => [
                'posts' => []
            ],
            'version' => '1.0.0',
            'error' => [
                'type' => 'PK\Exceptions\Http\NotFound',
                'message' => 'Нет постов'
            ]
        ], $this->res->getBody());
    }

    protected function setUp(): void
    {
        $this->res = (new Response(['posts' => []], 404, ['Content-type: application/json']))->setException(new NotFound('Нет постов'));
    }
}
