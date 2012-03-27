<?php

namespace SilexCMS\Repository;

use SilexCMS\Repository\AbstractRepository;

class GenericRepository extends AbstractRepository
{
    protected $table = null;
    
    public function __construct($db, $table)
    {
        $this->table = $table;
        
        parent::__construct($db);
    }
}
