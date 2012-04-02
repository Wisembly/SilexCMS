<?php

namespace SilexCMS\Tests\Repository;

use SilexCMS\Repository\AbstractRepository;

use SilexCMS\Tests\Base;

class FooRepository extends AbstractRepository
{
    protected $table = 'foo';
}

class AbstractRepositoryTest extends Base
{
    public function testInsertAndFindAll()
    {
        $repo = $this->getFooRepository();
        $repo->insert(array('id' => 1, 'val' => 42));
        $repo->insert(array('id' => 2, 'val' => 69));
        $repo->insert(array('id' => 3, 'val' => 1337));
        $res = $repo->findAll();
        
        $sum = 0;
        foreach ($res as $r) {
            $sum += $r['val'];
        }
        
        $this->assertEquals(42 + 69 + 1337, $sum);
    }
    
    public function testInsertAndQuery()
    {
        $repo = $this->getFooRepository();
        $repo->insert(array('id' => 1, 'val' => 42));
        $repo->insert(array('id' => 2, 'val' => 69));
        $repo->insert(array('id' => 3, 'val' => 1337));
        $res = $repo->query('SELECT SUM(val) FROM foo')->fetch();
        $this->assertEquals(42 + 69 + 1337, $res[0]);
    }
    
    public function testInsertUpdateAndQuery()
    {
        $repo = $this->getFooRepository();
        $repo->insert(array('id' => 1, 'val' => 42));
        $repo->insert(array('id' => 2, 'val' => 69));
        $repo->insert(array('id' => 3, 'val' => 1337));
        $repo->update(array('id' => 2), array('val' => 1));
        $res = $repo->query('SELECT SUM(val) FROM foo')->fetch();
        $this->assertEquals(42 + 1 + 1337, $res[0]);
    }
    
    public function testInsertDeleteAndQuery()
    {
        $repo = $this->getFooRepository();
        $repo->insert(array('id' => 1, 'val' => 42));
        $repo->insert(array('id' => 2, 'val' => 69));
        $repo->insert(array('id' => 3, 'val' => 1337));
        $repo->delete(array('id' => 2));
        $res = $repo->query('SELECT SUM(val) FROM foo')->fetch();
        $this->assertEquals(42 + 1337, $res[0]);
    }
    
    public function testInsertAndSelect()
    {
        $repo = $this->getFooRepository();
        $repo->insert(array('id' => 1, 'val' => 42));
        $repo->insert(array('id' => 2, 'val' => 69));
        $repo->insert(array('id' => 3, 'val' => 1337));
        $res = $repo->select(array('id' => 2))->fetch();
        $this->assertEquals(69, $res['val']);
    }
    
    public function testInsertUpdateConditionShortcutAndQuery()
    {
        $repo = $this->getFooRepository();
        $repo->insert(array('id' => 1, 'val' => 42));
        $repo->insert(array('id' => 2, 'val' => 69));
        $repo->insert(array('id' => 3, 'val' => 1337));
        $repo->update(2, array('val' => 1));
        $res = $repo->query('SELECT SUM(val) FROM foo')->fetch();
        $this->assertEquals(42 + 1 + 1337, $res[0]);
    }
    
    public function testInsertDeleteConditionShortcutAndQuery()
    {
        $repo = $this->getFooRepository();
        $repo->insert(array('id' => 1, 'val' => 42));
        $repo->insert(array('id' => 2, 'val' => 69));
        $repo->insert(array('id' => 3, 'val' => 1337));
        $repo->delete(2);
        $res = $repo->query('SELECT SUM(val) FROM foo')->fetch();
        $this->assertEquals(42 + 1337, $res[0]);
    }
    
    public function testInsertAndSelectConditionShortcut()
    {
        $repo = $this->getFooRepository();
        $repo->insert(array('id' => 1, 'val' => 42));
        $repo->insert(array('id' => 2, 'val' => 69));
        $repo->insert(array('id' => 3, 'val' => 1337));
        $res = $repo->select(2)->fetch();
        $this->assertEquals(69, $res['val']);
    }
    
    public function getFooRepository()
    {
        $app = $this->getApplication();
        $app['db']->executeQuery('CREATE TABLE foo (id int, val int)');
        return new FooRepository($app['db']);
    }
}
