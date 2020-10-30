<?php

namespace PK\Chan\Posts\Controllers;

use PK\Http\Request;
use PK\Http\Response;
use PK\Chan\Posts\Repository;
use PK\Chan\Posts\Exceptions\NotFound;

final class GetPost
{
    /** @var Repository */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $req, array $vars)
    {
        $id = $vars['id'];

        try {
            $post = $this->repository->findById($id);
        } catch (NotFound $e) {
            return (new Response([], 404))->setException(new NotFound('Нет такого поста: ' . $id));
        }

        $results = $post->toArray();

        try {
            $replies = $this->repository->findByParentId($id);

            foreach ($replies as $reply) {
                $results['replies'][] = $reply->toArray();
            }
        } catch (NotFound $e) {
            $results['replies'] = [];
        }

        return new Response($results, 200);
    }
}
