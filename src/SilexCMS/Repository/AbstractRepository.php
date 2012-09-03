<?php

namespace SilexCMS\Repository;

use SilexCMS\Repository\DataMap;

abstract class AbstractRepository extends DataMap
{
    private $db;
    protected $table;
    protected $schema;

    public function __construct($db)
    {
        $this->db = $db;

        parent::__construct($db, $this->schema);
    }

    public function query($query, $arguments = array())
    {
        return $this->db->executeQuery($query, $arguments);
    }

    public function execute($query, $arguments = array())
    {
        return $this->db->executeUpdate($query, $arguments);
    }

    public function findOneBy($condition)
    {
        return array_shift($this->select($condition));
    }

    public function select($condition = null)
    {
        if (is_null($condition)) {
            $condition = array();
        }

        if (is_numeric($condition)) {
            $condition = array('id' => $condition);
        }

        $where = implode(' AND ', array_map(function ($name) { return $name . ' = ?'; }, array_keys($condition)));

        if (!empty($where)) {
            $where = ' WHERE ' . $where;
        }

        return $this->db->executeQuery("SELECT * FROM {$this->table}{$where}", array_values($condition))->fetchAll();
    }

    public function insert($values)
    {
        if (is_object($values)) {
            $values = $values->toArray();
        }

        return $this->db->insert($this->table, $this->mapToDB($values));
    }

    public function update($values, $condition)
    {
        if (is_numeric($condition)) {
            $condition = array('id' => $condition);
        }

        return $this->db->update($this->table, $this->mapToDB($values), $condition);
    }

    public function delete($condition)
    {
        if (is_numeric($condition)) {
            $condition = array('id' => $condition);
        }

        return $this->db->delete($this->table, $condition);
    }

    public function fetchAll($query)
    {
        return $this->mapFromDb($this->db->fetchAll($query));
    }

    public function findAll()
    {
        return $this->mapFromDb($this->db->fetchAll("SELECT * FROM {$this->table}"));
    }
}
