<?php

namespace PK\Chan\Boards\Controllers;

use PK\Chan\Boards\Repository;
use PK\Http\Request;
use PK\Http\Response;
use PK\Chan\Boards\Exceptions\BoardNotFound;

final class GetAllBoards
{
    /** @var Repository */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $req): Response
    {        
        try {
            $boards = $this->repository->fetch();
        } catch (BoardNotFound $e) {
            return (new Response([], 404))->setException(new BoardNotFound('Нет досок'));
        }

        $results = [];

        foreach ($boards as $board) {
            array_push($results, $board->toArray());
        }

        return new Response($results, 200);
    }
}
