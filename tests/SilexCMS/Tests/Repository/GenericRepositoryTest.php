<?php

namespace SilexCMS\Tests\Repository;

use SilexCMS\Repository\GenericRepository;

use SilexCMS\Tests\Base;

class GenericRepositoryTest extends Base
{
    public function testLettersTable()
    {
        $repository = $this->getRepository('letters');

        $vals = array();
        foreach ($repository->findAll() as $res) {
            $vals[] = $res['val'];
        }

        $this->assertEquals('abc', implode($vals));
    }

    public function testDigitsTable()
    {
        $repository = $this->getRepository('digits');

        $vals = array();
        foreach ($repository->findAll() as $res) {
            $vals[] = $res['val'];
        }

        $this->assertEquals('123', implode($vals));
    }

    public function getRepository($repository)
    {
        $app = $this->getApplication();
        return new GenericRepository($app['db'], $repository);
    }
}
