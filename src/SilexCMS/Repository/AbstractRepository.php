<?php

namespace SilexCMS\Repository;

use SilexCMS\Repository\DataMap;
use Doctrine\DBAL\Connection as Database;

abstract class AbstractRepository extends DataMap
{
    protected $db;
    protected $table;

    public function __construct(Database $db, array $schema = array())
    {
        $this->db = $db;

        parent::__construct($db, $schema);
    }

    public function setTable($table)
    {
        $this->table = mysql_real_escape_string($table);

        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function query($query, $arguments = array())
    {
        return $this->db->executeQuery($query, $arguments);
    }

    public function execute($query, $arguments = array())
    {
        return $this->db->executeUpdate($query, $arguments);
    }

    public function findOneBy($condition, $mapForeigns = false)
    {
        $result = $this->select($condition, $mapForeigns);

        return array_shift($result);
    }

    public function select($condition = null, $mapForeigns = false)
    {
        if (is_null($condition)) {
            $condition = array();
        }

        if (is_numeric($condition)) {
            $condition = array('id' => $condition);
        }

        $where = implode(' AND ', array_map(function ($name) { return mysql_real_escape_string($name) . ' = ?'; }, array_keys($condition)));

        if (!empty($where)) {
            $where = ' WHERE ' . mysql_real_escape_string($where);
        }

        return $this->mapFromDb($this->db->executeQuery("SELECT * FROM {$this->table}{$where}", array_values($condition))->fetchAll(), $mapForeigns);
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

    public function fetchAll($query, $mapForeigns = false)
    {
        return $this->mapFromDb($this->db->fetchAll($query), false);
    }

    public function findAll($mapForeigns = false)
    {
        return $this->mapFromDb($this->db->fetchAll("SELECT * FROM {$this->table}"), $mapForeigns);
    }
}
