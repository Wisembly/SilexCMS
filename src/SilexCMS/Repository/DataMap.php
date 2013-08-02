<?php

namespace SilexCMS\Repository;

use Doctrine\DBAL\Types\Type;

class DataMap
{
    private $db;
    private $schema;

    public function __construct($db, $schema)
    {
        $this->db = $db;
        $this->schema = $schema;
    }

    public function mapFromDb($data, $mapForeigns = true)
    {
        $mappedData = array();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $mappedData[$key] = $this->mapFromDb($value, $mapForeigns);
                continue;
            }

            if ($mapForeigns && false !== strpos($key, '_id')) {
                $table = str_replace('_id', '', $key);

                if (false !== $foreign = $this->mapForeignKeys($table, $this->schema[$key], $value)) {
                    $value = $foreign;
                    $this->schema[$key]->setType(Type::getType('string'));
                }
            }

            if (isset($this->schema[$key])) {
                switch ($this->schema[$key]->getType()) {
                    case 'Boolean' :
                        $value = $value == 1 ;
                    break;
                    case 'Integer' :
                        $value = (int) $value;
                    break;
                    case 'Array' :
                    case 'Object':
                        $value = unserialize($value);
                    break;
                }
            }

            $mappedData[$key] = $value;
        }

        return $mappedData;
    }

    public function mapToDb($data)
    {
        $mappedData = array();

        foreach ($data as $key => $value) {
            if (isset($this->schema[$key])) {
                switch ($this->schema[$key]->getType()) {
                    case 'Boolean' :
                        $value = $value ? 1 : 0 ;
                    break;
                    case 'Integer' :
                        $value = (int) $value;
                    break;
                    case 'Array' :
                    case 'Object':
                        $value = serialize($value);
                    break;
                }
            }

            $mappedData[$key] = $value;
        }

        return $mappedData;
    }

    public function mapForeignKeys($table, $column, $id = null)
    {
        try {
            $relatedRows = $this->db->executeQuery("SELECT * FROM $table ORDER BY id ASC")->fetchAll();

            if (isset($relatedRows[0]['name'])) {
                $foreign = 'name';
            } else {
                $comments = json_decode($column->getComment(), true);

                if (!empty($comments) && is_array($comments)) {
                    $foreign = $foreign['foreign'];
                } else {
                    $foreign = 'id';
                }
            }

            $choices = array();

            foreach ($relatedRows as $relatedRow) {
                if (null !== $id && $relatedRow['id'] == $id) {
                    return $relatedRow[$foreign];
                }

                $choices[$relatedRow['id']] = $relatedRow[$foreign];
            }

            return $choices;
        } catch (\Exception $e) {
            return false;
        }
    }
}