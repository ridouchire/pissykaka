<?php

namespace PK\Database\Persistences;

use PK\Database\Persistences\IPersistence;

class ImMemoryPersistence implements IPersistence
{
    private $storage;
    private $counter;

    public function __construct(array $state = [])
    {
        $this->storage = $state;
    }

    public function fetch(string $table, array $fields = [], int $limit = 0, int $offset = 0)
    {
        if (!isset($this->storage[$table]) || empty($this->storage[$table])) {
            return false;
        }

        if ($limit !== 0) {
            return array_slice($this->storage[$table], $offset, $limit);
        }

        return $this->storage[$table];
    }

    public function save(string $table, array $state): int
    {
        $this->storage[$table] = $state;
    }
}
