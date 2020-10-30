<?php

namespace PK\Chan\Posts\Controllers;

use PK\Http\Request;
use PK\Http\Response;
use PK\Chan\Boards\Repository as BoardRepository;
use PK\Chan\Posts\Repository as PostRepository;
use PK\Chan\Boards\Exceptions\NotFound as BoardNotFound;
use PK\Chan\Posts\Exceptions\NotFound as PostNotFound;
use PK\Chan\Boards\Board;

final class GetAllThreads
{
    private const INTERSECTION_OPERATOR = '+';
    private const METABOARD_TAG = '~';
    
    /** @var PostRepository */
    private $post_repo;

    /** @var BoardRepository */
    private $board_repo;

    public function __construct(PostRepository $post_repo, BoardRepository $board_repo)
    {
        $this->post_repo  = $post_repo;
        $this->board_repo = $board_repo;
    }

    public function __invoke(Request $req, array $vars): Response
    {
        $board_ids = $results = $posts = [];
        $limit = $req->getParams('limit') ? $req->getParams('limit') : 10;
        $offset = $req->getParams('offset') ? $req->getParams('offset') : 0;

        if ($vars['tags'] == self::METABOARD_TAG) {
            $boards = $this->board_repo->fetch();

            $board_ids = array_map(function ($board) {
                return $board->getId();
            }, $boards);
        } else {
            $tags = explode(self::INTERSECTION_OPERATOR, $vars['tags']);

            foreach ($tags as $tag) {
                try {
                    $board = $this->board_repo->findByTag($tag);

                    array_push($board_ids, $board->getId());
                } catch (BoardNotFound $e) {
                    return (new Response([], 404))->setException($e);
                }
            }
        }

        $posts = $this->post_repo->findByBoardIds($board_ids, $limit, $offset);

        foreach ($posts as $post) {
            array_push($results, $post->toArray());
        }

        return new Response($results, 200);
    }
}
