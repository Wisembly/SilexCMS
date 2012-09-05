<?php

namespace SilexCMS\Repository;

use SilexCMS\Repository\AbstractRepository;

class GenericRepository extends AbstractRepository
{
    protected $table = null;
    protected $schema = null;

    public function __construct($db, $table)
    {
        $this->table = $table;
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
