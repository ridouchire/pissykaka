<?php

namespace PK\Database\Persistences;

use Medoo\Medoo;
use PK\Database\Persistences\IPersistence;

class MysqlPersistence implements IPersistence
{
    private $db;

    public function __construct(string $database, string $hostname, string $username, string $password)
    {
        $this->db = new Medoo([
            'database_type' => 'mysql',
            'database_name' => $database,
            'server'        => $hostname,
            'username'      => $username,
            'password'      => $password,
            'charset'       => 'utf8',
            'collation'     => 'utf8_unicode_ci'
        ]);
    }

    public function select(string $table, array $fields, array $joins = [], array $conditions = [])
    {
        return $this->db->select($table, $fields, $joins, $conditions);
    }

    public function insert(string $table, array $state): int
    {
        $this->db->insert($table, $state);

        return $this->db->id();
    }

    public function update(string $table, array $state, array $conditions)
    {
        return $this->db->update($table, $state, $conditions);
    }

    public function delete(string $table, array $conditions = [])
    {
        return $this->db->delete($table, $conditions);
    }

    public function getLastId(): int
    {
        return $this->db->id();
    }
}
