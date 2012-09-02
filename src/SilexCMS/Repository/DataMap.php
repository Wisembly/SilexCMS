<?php

namespace SilexCMS\Repository;

class DataMap
{
    private $db;
    private $schema;

    public function __construct($db, $schema)
    {
        $this->db = $db;
        $this->schema = $schema;
    }

    public function mapFromDb($data)
    {
        $mappedData = array();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $mappedData[$key] = $this->mapFromDb($value);
                continue;
            }

            switch ($this->schema[$key]->getType()) {
                case 'Boolean' :
                    $value = $value == 1 ;
                break;
                case 'Integer' :
                    $value = (int)$value;
                break;
                case 'Array' :
                case 'Object':
                    $value = unserialize($value);
                break;
            }

            $mappedData[$key] = $value;
        }

        return $mappedData;
    }

    public function mapToDb($data)
    {
        $mappedData = array();

        foreach ($data as $key => $value) {
            switch ($this->schema[$key]->getType()) {
                case 'Boolean' :
                    $value = $value ? 1 : 0 ;
                break;
                case 'Integer' :
                    $value = (int)$value;
                break;
                case 'Array' :
                case 'Object':
                    $value = serialize($value);
                break;
            }

            $mappedData[$key] = $value;
        }

        return $mappedData;
    }
}