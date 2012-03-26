<?php

namespace SilexCMS\Repository;

abstract class AbstractRepository
{
    protected $table = null;
	
    public function __construct($db)
    {
        $this->db = $db;
    }
	
    public function query($query, $arguments = array())
    {
        return $this->db->executeQuery($query, $arguments);
    }
	
    public function execute($query, $arguments = array())
    {
        return $this->db->executeUpdate($query, $arguments);
    }
	
    public function select($condition)
    {
        if (is_numeric($condition)) {
            $condition = array('id' => $condition);
        }
        
        $where = implode(' AND ', array_map(function ($name) { return $name . ' = ?'; }, array_keys($condition)));
		
        if (!empty($where)) {
            $where = ' WHERE ' . $where;
        }
        
        return $this->db->executeQuery("SELECT * FROM {$this->table}{$where}", array_values($condition));
    }
	
    public function insert($values)
    {
        return $this->db->insert($this->table, $values);
    }
	
    public function update($condition, $values)
    {
        if (is_numeric($condition)) {
            $condition = array('id' => $condition);
        }
        
        return $this->db->update($this->table, $values, $condition);
    }
	
    public function delete($condition)
    {
        if (is_numeric($condition)) {
            $condition = array('id' => $condition);
        }
        
        return $this->db->delete($this->table, $condition);
    }
	
    public function findAll()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }
}
