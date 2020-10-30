<?php

namespace PK\Chan\Boards;

class Board
{
    /** @var int */
    private $id;

    /** @var string */
    private $tag;

    /** @var string */
    private $name;

    public static function fromState(array $state): Board
    {
        return new self($state['id'], $state['tag'], $state['name']);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tag' => $this->tag,
            'name' => $this->name
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    private function __construct(int $id, string $tag, string $name)
    {
        $this->id = $id;
        $this->tag = $tag;
        $this->name = $name;
    }
}
