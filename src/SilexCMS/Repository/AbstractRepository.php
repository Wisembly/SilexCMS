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

    public function getTables()
    {
        $listTables = array();
        $dbOptions = $this->db->getParams();

        if ('pdo_mysql' === $dbOptions['driver']) {
            $tables = $this->db->fetchAll('SHOW tables');

            foreach ($tables as $table) {
                $listTables[] = array_shift($table);
            }

            return $listTables;
        }

        if ('pdo_sqlite' === $dbOptions['driver']) {
            $tables = $this->db->fetchAll('SELECT * FROM sqlite_master WHERE type="table"');

            foreach ($tables as $table) {
                $listTables[] = $table['tbl_name'];
            }

            return $listTables;
        }
    }
}
