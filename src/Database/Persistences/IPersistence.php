<?php

namespace PK\Database\Persistences;

interface IPersistence
{
    public function select(string $table, array $fields, array $joins, array $conditions);
    public function insert(string $table, array $state);
    public function update(string $table, array $state, array $conditions);
    public function delete(string $table, array $conditions);
    public function getLastId(): int;
}
