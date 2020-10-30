<?php

use Medoo\Medoo;
use PK\Router;
use PK\Http\Request;
use PK\Http\Response;
use PK\Database\Persistences\MysqlPersistence;
use PK\Chan\Boards\Repository as BoardRepository;
use PK\Chan\Posts\Repository as PostRepository;
use PK\Chan\Boards\Controllers\GetAllBoards;
use PK\Chan\Posts\Controllers\GetPost;
use PK\Chan\Posts\Controllers\CreatePost;
use PK\Chan\Posts\Controllers\CreateReply;
use PK\Chan\Posts\Controllers\GetAllThreads;

require_once "vendor/autoload.php";

/** @var array */
$config = require "config.php";

$persistence = new MysqlPersistence(
    $config['db']['database'],
    $config['db']['hostname'],
    $config['db']['username'],
    $config['db']['password']
);

/** @var BoardRepository */
$board_repo = new BoardRepository($persistence);

/** @var PostRepository */
$post_repo  = new PostRepository($persistence);

/** @var Router */
$router = new Router();
$router->addRoute('GET', '/board/all', new GetAllBoards($board_repo)); // return list all boards
$router->addRoute('GET', '/post/{id:[0-9]+}', new GetPost($post_repo)); // return thread and replies by post id
$router->addRoute('GET', '/post/{tags}', new GetAllThreads($post_repo, $board_repo)); // return threads by board tag
$router->addRoute('POST', '/post', new CreatePost($post_repo, $board_repo)); // create post
$router->addRoute('POST', '/post/{id:[0-9]+}', new CreateReply($post_repo)); // create reply by post id

/** @var Request */
$req = new Request($_SERVER, $_POST, $_FILES);

/** @var Response */
$res = $router->handle($req);

if (PHP_SAPI !== 'cli') {
    if (!empty($res->getHeaders())) {
        foreach ($res->getHeaders() as $header) {
            header($header);
        }
    }

    http_response_code($res->getCode());
}

echo json_encode($res->getBody());
exit(0);
