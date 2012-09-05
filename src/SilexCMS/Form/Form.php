<?php

namespace SilexCMS\Form;

use SilexCMS\Repository\GenericRepository;

class Form
{
    private $repository;

    public function __construct(GenericRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getData($id, $index = null)
    {
        if (null === $index) {
            $index = 'row';
        }

        if (empty($id)) {
            return array($index => array(array_map(function ($val) { return null; }, $this->repository->getSchema())));
        }

        return array($index => $this->repository->fetchAll("SELECT * FROM " . $this->repository->getTable() . " WHERE id = {$id}", false));
    }
}