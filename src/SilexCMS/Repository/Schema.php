<?php

namespace SilexCMS\Repository;

use SilexCMS\Repository\AbstractRepository;
use Doctrine\DBAL\Connection as Database;

class Schema extends AbstractRepository
{
    protected $table = null;

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }
}
