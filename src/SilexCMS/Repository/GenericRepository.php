<?php

namespace SilexCMS\Repository;

use SilexCMS\Repository\AbstractRepository;
use Doctrine\DBAL\Connection as Database;

class GenericRepository extends AbstractRepository
{
    protected $table;
    protected $schema;
    protected $primaryKey;

    public function __construct(Database $db, $table, $primaryKey = 'id')
    {
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->schema = $db->getSchemaManager()->listTableColumns($table);

        parent::__construct($db, $this->schema);

        $this->init();
    }

    private function init()
    {
        $options = $this->db->getParams();

        if ('pdo_mysql' === $options['driver'] && isset($options['charset'])) {
            $this->db->query("SET NAMES '" . $options['charset'] . "'");
        }
    }

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function getSchema()
    {
        return $this->schema;
    }
}
