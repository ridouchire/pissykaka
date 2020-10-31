<?php

namespace PK\Chan\Posts\Controllers;

use PK\Http\Request;
use PK\Http\Response;
use PK\Database\Persistences\IPersistence;
use PK\Chan\Posts\Exceptions\NotFound;

final class GetFeedPosts
{
    /** @var IPersistence */
    private $persistence;

    public function __construct(IPersistence $persistence)
    {
        $this->persistence = $persistence;
    }

    public function __invoke(Request $req)
    {
        $limit = $req->getParams('limit') ? $req->getParams('limit') : 10;
        $offset = $req->getParams('offset') ? $req->getParams('offset') : 0;

        $posts = $this->persistence->select(
            'posts',
            [
                '[>]boards' => ['board_id' => 'id']
            ],
            [
                'posts.id',
                'posts.poster',
                'posts.subject',
                'posts.message',
                'posts.timestamp',
                'posts.parent_id',
                'boards.tag'
            ],
            [
                'AND' => ['boards.tag[!]' => 'test'],
                'LIMIT' => [$offset, $limit],
                'ORDER' => ['posts.timestamp' => 'DESC']
            ]
        );

        if (!$posts) {
            return (new Response([], 404))->setException(new NotFound());
        }

        return new Response($posts, 200);
    }
}
