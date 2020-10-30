<?php

namespace PK\Chan\Posts\Controllers;

use PK\Http\Request;
use PK\Http\Response;
use PK\Chan\Posts\Repository;
use PK\Chan\Posts\Exceptions\NotFound;
use PK\Chan\Posts\Post;

final class CreateReply
{
    /** @var Repository */
    private $repo;

    public function __construct(Repository $repo)
    {
        $this->repo = $repo;
    }

    public function __invoke(Request $req, array $vars): Response
    {
        $parent_id = $vars['id'];

        try {
            /** @var Post */
            $parent_post = $this->repo->findById($parent_id);
        } catch (NotFound $e) {
            return (new Response([], 400))->setException(new NotFound(
                sprintf('Поста с идентификатором #%s не существует, ответ невозможен', $parent_id)
            ));
        }

        $post = Post::draft(
            $parent_post->getBoardId(),
            $req->getParams('message') ? $req->getParams('message') : '',
            $parent_id,
            $req->getParams('subject') ? $req->getParams('subject') : '',
            $req->getParams('poster') ? $req->getParams('poster') : ''
        );

        $id = $this->repo->save($post);

        if (!$req->getParams('sage')) {
            $parent_post->bumpUpdatedAt();
            $parent_post->bumpEstimate();

            $this->repo->update($parent_post);
        }

        return new Response([$id], 201);
    }
}
