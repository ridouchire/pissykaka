<?php

namespace PK\Chan\Posts\Controllers;

use PK\Http\Request;
use PK\Http\Response;
use PK\Chan\Posts\Repository as PostRepository;
use PK\Chan\Boards\Repository as BoardRepository;
use PK\Chan\Boards\Exceptions\NotFound;
use PK\Chan\Posts\Post\Id;
use PK\Chan\Posts\Post\Poster;
use PK\Chan\Posts\Post\Subject;
use PK\Chan\Posts\Post\Message;
use PK\Chan\Posts\Post\Timestamp;
use PK\Chan\Posts\Post\ParentId;
use PK\Chan\Posts\Post\UpdatedAt;
use PK\Chan\Boards\Board\Id as BoardId;
use PK\Chan\Posts\Post;

final class CreatePost
{
    /** @var PostRepository */
    private $post_repo;

    /** @var BoardRepository */
    private $board_repo;

    public function __construct(PostRepository $post_repo, BoardRepository $board_repo)
    {
        $this->post_repo  = $post_repo;
        $this->board_repo = $board_repo;
    }

    public function __invoke(Request $req): Response
    {
        if (!$req->getParams('tag')) {
            return (new Response([], 400))->setException(new \InvalidArgumentException("Не задан тег доски, на которой будет создан пост"));
        }

        try {
            $board = $this->board_repo->findByTag($req->getParams('tag'));
        } catch (NotFound $e) {
            return (new Response([], 400))->setException(new NotFound('Нет доски с тегом: ' . $req->getParams('tag')));
        }

        try {
            $post = Post::draft(
                $board->getId(),
                $req->getParams('message') ? $req->getParams('message') : '',
                null,
                $req->getParams('subject') ? $req->getParams('subject') : '',
                $req->getParams('poster') ? $req->getParams('poster') : ''
            );
            
            $id = $this->post_repo->save($post);

            return new Response([$id], 201);
        } catch (\Throwable $e) {
            return (new Response([], 400))->setException($e);
        }
    }
}
