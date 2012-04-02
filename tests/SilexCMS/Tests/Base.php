<?php

namespace SilexCMS\Tests;

use SilexCMS\Application;

class Base extends \PHPUnit_Framework_TestCase
{
    public function getTemplateStream($text)
    {
        return fopen('data://text/plain,' . $text, 'r');
    }
    
    public function getApplication()
    {
        $app = new Application(array(
            'db.options' => array( 'driver' => 'pdo_sqlite', 'memory' => true ),
            'twig.path' => '.',
            'locale_fallback' => 'en',
            'translator.messages' => array(),
            'debug' => true
        ));
        
        $this->populateDatabase($app['db']);
        
        return $app;
    }
    
    public function populateDatabase($db)
    {
        $db->executeQuery('CREATE TABLE letters (val char)');
        $db->insert('letters', array('val' => 'a'));
        $db->insert('letters', array('val' => 'b'));
        $db->insert('letters', array('val' => 'c'));
        
        $db->executeQuery('CREATE TABLE digits (val int)');
        $db->insert('digits', array('val' => '1'));
        $db->insert('digits', array('val' => '2'));
        $db->insert('digits', array('val' => '3'));
    }
}
