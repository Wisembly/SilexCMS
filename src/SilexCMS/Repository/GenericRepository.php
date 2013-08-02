<?php

namespace SilexCMS\Repository;

use SilexCMS\Repository\AbstractRepository;
use Doctrine\DBAL\Connection as Database;

class GenericRepository extends AbstractRepository
{
    protected $table = null;
    protected $schema = null;

    public function __construct(Database $db, $table)
    {
        $this->table = mysql_real_escape_string($table);
        $this->schema = $db->getSchemaManager()->listTableColumns($table);

        $dbOptions = $db->getParams();

        if ('pdo_mysql' === $dbOptions['driver'] && isset($dbOptions['charset'])) {
            $db->query("SET NAMES '" . $dbOptions['charset'] . "'");
        }

        parent::__construct($db);
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getSchema()
    {
        return $this->schema;
    }
}
