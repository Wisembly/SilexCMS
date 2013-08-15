<?php

namespace SilexCMS\Tests\Repository;

use SilexCMS\Repository\DataMap;

use SilexCMS\Tests\Base;

class Type
{
    private $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }
}

class DataMapTest extends Base
{
    public function setUp()
    {
        $db = $this->getMockBuilder('Doctrine\\DBAL\\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $schema = array(
            'boolean' => new Type('Boolean'),
            'integer' => new Type('Integer'),
            'array'   => new Type('Array'),
            'object'  => new Type('Object'),
        );

        $this->dataMap = new DataMap($db, $schema);
    }

    /**
     * @dataProvider getCastFromDb
     */
    public function testCastFromDb($key, $value, $expected)
    {
        $this->assertEquals($expected, $this->dataMap->castFromDb($key, $value));
    }

    public function getCastFromDb()
    {
        return array(
            array('boolean', 1, true),
            array('boolean', 0, false),
            array('integer', 0, 0),
            array('integer', "0", 0),
            array('array', "a:0:{}", array()),
            array('object', "a:0:{}", array()),
        );
    }

    /**
     * @dataProvider getCastToDb
     */
    public function testCastToDb($key, $value, $expected)
    {
        $this->assertEquals($expected, $this->dataMap->castToDb($key, $value));
    }

    public function getCastToDb()
    {
        return array(
            array('boolean', true, 1),
            array('boolean', false, 0),
            array('integer', 0, "0"),
            array('integer', "0", "0"),
            array('array', array(),  "a:0:{}"),
            array('object', array(),  "a:0:{}"),
        );
    }
}