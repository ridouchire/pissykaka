<?php

namespace PK\Chan\Posts;

class Post
{
    /** @var int Коэффициент для расчёта оценки */
    private const COEFFICIENT = 1602370000;

    /** @var int Идентификатор доски /b */
    private const BOARD_ID = 1;

    /** @var string Стандартное имя постера */
    private const NAME = 'Anonymous';

    /** @var Id Идентификатор*/
    private $id;

    /** @var Poster Автор */
    private $poster;

    /** @var Subject Тема */
    private $subject;

    /** @var Message Сообщение */
    private $message;

    /** @var Timestamp Время создания */
    private $timestamp;

    /** @var BoardId Идентификатор доски */
    private $board_id;

    /** @var ParentId Идентификатор родительского поста */
    private $parent_id;

    /** @var UpdatedAt Время обновления */
    private $updated_at;

    /** @var int Оценка */
    private $estimate;

    public static function draft(int $board_id, string $message = '', $parent_id = null, string $subject = '', string $poster = '')
    {
        return new self(
            0,
            empty($poster) ? self::NAME : $poster,
            $subject,
            $message,
            time(),
            $board_id,
            $parent_id,
            time(),
            0
        );
    }

    /**
     * Post constructor
     *
     * @param int    $id         Идентификатор
     * @param string $poster     Автор
     * @param string $subject    Тема
     * @param string $message    Сообщение
     * @param int    $timestamp  Время создания
     * @param int    $board_id   Идентификатор доски
     * @param int    $parent_id  Идентификатор родительского поста
     * @param int    $updated_at Время обновления
     * @param int    $estimate   Оценка
     */
    public function __construct(
        int $id,
        string $poster,
        string $subject,
        string $message,
        int $timestamp,
        int $board_id,
        $parent_id,
        int $updated_at,
        int $estimate = 0
    ) {
        $this->id         = $id;
        $this->poster     = $poster;
        $this->subject    = $subject;
        $this->message    = $message;
        $this->timestamp  = $timestamp;
        $this->board_id   = $board_id;
        $this->parent_id  = $parent_id;
        $this->updated_at = $updated_at;
        $this->estimate   = $estimate;
    }

    /**
     * Создаёт пост из внешнего состояния
     *
     * @param array $state Список, содержащий поля поста и данные как его состояние
     *
     * @return self
     */
    public static function fromState(array $state): self
    {
        return new self(
            $state['id'],
            $state['poster'],
            $state['subject'],
            $state['message'],
            $state['timestamp'],
            $state['board_id'],
            $state['parent_id'],
            $state['updated_at'],
            $state['estimate']
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPoster(): string
    {
        return $this->poster;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getBoardId(): int
    {
        return $this->board_id;
    }

    public function getParentId()
    {
        return $this->parent_id;
    }

    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

    /**
     * Возвращает оценку поста
     *
     * @return int
     */
    public function getEstimate(): int
    {
        if ($this->estimate !== 0) {
            return $this->estimate;
        }

        $x = $this->getTimestamp() / self::COEFFICIENT;

        if ($this->getPoster() !== self::NAME) {
            $x = $x / 2;
        }

        if ($this->getParentId() == null) {
            $x = $x + 1;
        }

        if ($this->getBoardId() !== self::BOARD_ID) {
            $x = $x + 2;
        }

        $x = $x / sprintf('0.%d', strlen($this->getMessage()));

        return (int) $x;
    }

    /**
     * Увеличивает оценку
     *
     * @return void
     */
    public function bumpEstimate(): void
    {
        $this->estimate = $this->getEstimate() + 1;
    }

    /**
     * Обновляет время обновления
     *
     * @return void
     */
    public function bumpUpdatedAt(): void
    {
        $this->updated_at = time();
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'poster' => $this->poster,
            'subject' => $this->subject,
            'message' => $this->message,
            'timestamp' => $this->timestamp,
            'board_id' => $this->board_id,
            'parent_id' => $this->parent_id,
            'updated_at' => $this->updated_at,
            'estimate'   => $this->estimate
        ];
    }
}
