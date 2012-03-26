<?php

namespace SilexCMS\Repository;

class GenericRepository
{
    protected $table = null;
    
    public function __construct($db, $table)
    {
        $this->table = $table;
        
        parent::__construct($db);
    }
}
