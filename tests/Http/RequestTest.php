<?php

use PHPUnit\Framework\TestCase;
use PK\Http\Request;

class RequestTest extends TestCase
{
    private $req;

    public function testGetHeaders()
    {
        $this->assertEquals([
            'HTTP_COOKIE' => 'test',
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], $this->req->getHeaders());
    }

    public function testGetMethod()
    {
        $this->assertEquals('POST', $this->req->getMethod());
    }

    public function testGetPath()
    {
        $this->assertEquals('/test', $this->req->getPath());
    }

    public function testGetParams()
    {
        $this->assertEquals(['param' => 'value', 'foo' => 'bar'], $this->req->getParams());
        $this->assertEquals('value', $this->req->getParams('param'));
    }

    public function testGetFiles()
    {
        $this->assertEquals([
            [
                'name' => 'test.png',
                'type' => 'image/png',
                'tmp_name' => '/tmp/phpn3FmFr',
                'error' => 0,
                'size' => 15343
            ]
        ], $this->req->getFiles());
    }

    protected function setUp(): void
    {
        $this->req = new Request(
            [
                'HTTP_COOKIE' => 'test',
                'HTTP_CONTENT_TYPE' => 'application/json',
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/test?param=value'
            ],
            [
                'foo' => 'bar'
            ],
            [
                [
                    'name' => 'test.png',
                    'type' => 'image/png',
                    'tmp_name' => '/tmp/phpn3FmFr',
                    'error' => 0,
                    'size' => 15343
                ]
            ]
        );
    }
}
