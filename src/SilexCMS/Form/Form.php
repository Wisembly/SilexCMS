<?php

namespace SilexCMS\Form;

use SilexCMS\Set\SetInterface;

class Form
{
    private $repository;

    public function __construct(SetInterface $set)
    {
        $this->set = $set;
        $this->repository = $set->getRepository();
    }

    public function getData($primaryKey, $index = null)
    {
        if (null === $index) {
            $index = 'row';
        }

        if (empty($primaryKey)) {
            return array($index => array(array_map(function ($val) { return null; }, $this->repository->getSchema())));
        }

        return array($index => $this->repository->fetchAll("SELECT * FROM " . $this->repository->getTable() . " WHERE `" . $this->repository->getPrimaryKey() . "` = '" . addslashes($primaryKey) . "'", false));
    }
}
