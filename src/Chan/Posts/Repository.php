<?php

namespace PK\Chan\Posts;

use PK\Chan\Posts\Post;
use PK\Chan\Posts\Exceptions\NotFound;
use PK\Database\Persistences\IPersistence;

class Repository
{
    /** @var string Имя таблицы постов */
    protected const TABLE      = 'posts';

    /** @var string Имя поля идентификатора поста */
    protected const ID         = 'id';

    /** @var string Имя поля автора поста */
    protected const POSTER     = 'poster';

    /** @var string Имя поля темы поста */
    protected const SUBJECT    = 'subject';

    /** @var string Имя поля сообщения поста */
    protected const MESSAGE    = 'message';

    /** @var string Имя поля времени создания поста */
    protected const TIMESTAMP  = 'timestamp';

    /** @var string Имя поля идентификатора доски, которой принадлежит пост */
    protected const BOARD_ID   = 'board_id';

    /** @var string Имя поля идентификатора поста которому принадлежит этот пост */
    protected const PARENT_ID  = 'parent_id';

    /** @var string Имя поля времени обновления поста */
    protected const UPDATED_AT = 'updated_at';

    /** @var string Имя поля оценки поста */
    protected const ESTIMATE   = 'estimate';

    /** @var IPersistence */
    protected $persistence;

    public function __construct(IPersistence $persistence)
    {
        $this->persistence = $persistence;
    }

    /**
     * Возвращает список ответов на пост
     *
     * @param int $id Идентификатор поста
     *
     * @throws NotFound Если ответов на пост не существует
     *
     * @return array Список объектов Post
     */
    public function findByParentId(int $id): array
    {
        $replies_data = $this->persistence->select(self::TABLE, $this->getFields(), [self::PARENT_ID => $id]);

        if (!$replies_data) {
            throw new NotFound('Ответы не найдены');
        }

        $replies = [];

        foreach ($replies_data as $reply_data) {
            $replies[] = Post::fromState($reply_data);
        }

        return $replies;
    }

    /**
     * Возвращает список тредов для указанных досок
     *
     * @param array $board_ids Список идентификаторов досок
     * @param int   $limit     Количество тредов в ответе
     * @param int   $offset    Смещение относительно первого треда в списке
     *
     * @throws NotFound Если тредов у доски нет
     *
     * @return array Список объектов Post
     */
    public function findByBoardIds(array $board_ids, int $limit, int $offset): array
    {
        $posts_data = $this->persistence->select(
            self::TABLE,
            $this->getFields(),
            [
                'AND' => [
                    self::BOARD_ID  => $board_ids,
                    self::PARENT_ID => null
                ],
                'ORDER' => [
                    self::UPDATED_AT => 'DESC'
                ],
                'LIMIT' => [$offset, $limit]
            ]
        );

        if (!$posts_data) {
            throw new NotFound('Треды не найдены');
        }

        $posts = [];

        foreach ($posts_data as $post_data) {
            $posts[] = Post::fromState($post_data);
        }

        return $posts;
    }

    /**
     * Возвращает пост
     *
     * @param int $id Идентификатор поста
     *
     * @throws NotFound Если поста с таким идентификатором нет
     *
     * @return Post
     */
    public function findById(int $id): Post
    {
        $post_data = $this->persistence->select(self::TABLE, $this->getFields(), [self::ID => $id]);

        if (!$post_data) {
            throw new NotFound('Пост не найден');
        }

        return Post::fromState(end($post_data));
    }

    /**
     * Сохраняет пост в БД
     *
     * @param Post $post Объект поста
     *
     * @return int
     */
    public function save(Post $post)
    {
        $this->persistence->insert(self::TABLE, [
            self::POSTER => $post->getPoster(),
            self::SUBJECT => $post->getSubject(),
            self::MESSAGE => $post->getMessage(),
            self::TIMESTAMP => $post->getTimestamp(),
            self::BOARD_ID => $post->getBoardId(),
            self::PARENT_ID => $post->getParentId(),
            self::UPDATED_AT => $post->getUpdatedAt(),
            self::ESTIMATE => $post->getEstimate()
        ]);

        return $this->persistence->getLastId();
    }

    /**
     * Обновляет пост в БД
     *
     * @param Post $post Объект поста
     *
     * @return bool
     */
    public function update(Post $post): bool
    {
        $this->persistence->update(self::TABLE, [
            self::POSTER => $post->getPoster(),
            self::SUBJECT => $post->getSubject(),
            self::MESSAGE => $post->getMessage(),
            self::TIMESTAMP => $post->getTimestamp(),
            self::BOARD_ID => $post->getBoardId(),
            self::PARENT_ID => $post->getParentId(),
            self::UPDATED_AT => $post->getUpdatedAt(),
            self::ESTIMATE => $post->getEstimate()
        ], [self::ID => $post->getId()]);

        return true;
    }

    /**
     * Возвращает список полей поста в таблице
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            self::ID,
            self::POSTER,
            self::SUBJECT,
            self::MESSAGE,
            self::TIMESTAMP,
            self::BOARD_ID,
            self::PARENT_ID,
            self::UPDATED_AT,
            self::ESTIMATE
        ];
    }
}
