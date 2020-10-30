<?php

namespace PK\Chan\Boards;

use PK\Chan\Boards\Board;
use PK\Chan\Boards\Exceptions\NotFound;
use PK\Database\Persistences\IPersistence;

class Repository
{
    /** @var string  Имя таблицы досок */
    private const TABLE = 'boards';

    /** @var string Имя поля идентификатора доски */
    private const ID = 'id';

    /** @var string Имя поля тега доски */
    private const TAG = 'tag';

    /** @var string Имя поля названия доски */
    private const NAME = 'name';

    /** @var IPersistence */
    private $persistence;

    public function __construct(IPersistence $persistence)
    {
        $this->persistence = $persistence;
    }

    /**
     * Возвращает список досок
     *
     * @throws NotFound Если нет ни одной доски
     *
     * @param array Список объектов Board
     */
    public function fetch(): array
    {
        $boards_data = $this->persistence->select(self::TABLE, $this->getFields());

        if (!$boards_data) {
            throw new NotFound('Не найдено ни одной доски');
        }

        $boards = [];

        foreach ($boards_data as $board_data) {
            $boards[] = Board::fromState($board_data);
        }

        return $boards;
    }

    /**
     * Возвращает доску по её тегу
     *
     * @param string $tag Тег доски
     *
     * @throws NotFound Если доски с таким тегом не найдено
     *
     * @return Board
     */
    public function findByTag(string $tag): Board
    {
        $board_data = $this->persistence->select(self::TABLE, $this->getFields(), ['AND' => ['tag' => $tag]]);

        if (!$board_data) {
            throw new NotFound('Доски с таким тегом не найдено');
        }

        return Board::fromState(end($board_data));
    }

    /**
     * Возвращает доску по её идентификатору
     *
     * @param int $id Идентификатор доски
     *
     * @throws NotFound Если доски с таким идентификатором не найдено
     *
     * @return Board
     */
    public function findById(int $id): Board
    {
        $board_data = $this->persistence->select(self::TABLE, $this->getFields(), ['id' => $id]);

        if (!$board_data) {
            throw new NotFound('Доски с таким идентификатором не найдено');
        }

        return Board::fromState(end($board_data));
    }

    /**
     * Возвращает список полей таблицы досок
     *
     * @return array
     */
    private function getFields(): array
    {
        return [
            self::ID,
            self::TAG,
            self::NAME
        ];
    }
}
