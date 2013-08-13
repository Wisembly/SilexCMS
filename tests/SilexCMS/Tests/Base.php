<?php

namespace SilexCMS\Tests;

use Silex\WebTestCase;
use SilexCMS\Application;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Bundle\FrameworkBundle\Client;

class Base extends WebTestCase
{
    public function getTemplateStream($text)
    {
        return $text;
    }

    public function createApplication(array $options = array())
    {
        $app = new Application(array_merge(array(
            'db.options'            => array('driver' => 'pdo_sqlite', 'memory' => true),
            'twig.path'             => __DIR__ . '/Resources/views',
            'locale_fallback'       => 'en',
            'debug'                 => isset($options['debug']) ? $options['debug'] : true,
        ), $options));

        // simulate sessions for tests
        $app['session.test'] = true;
        $this->populateDatabase($app['db']);

        // fake translations
        $app['translator.domains'] = array(
            'messages' => array(
                'fr' => array('hello' => 'Bonjour'),
                'en' => array('hello' => 'Hello'),
            ),
        );

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

        $db->executeQuery('CREATE TABLE category (id int, name char)');
        $db->insert('category', array('id' => 1, 'name' => 'sci-fi'));
        $db->insert('category', array('id' => 2, 'name' => 'fantasy'));

        $db->executeQuery('CREATE TABLE book (id int, name char, category_id int)');
        $db->insert('book', array('id' => 1, 'name' => 'Lord Of The Rings', 'category_id' => 2));
        $db->insert('book', array('id' => 2, 'name' => 'Dune', 'category_id' => 1));

        $db->executeQuery('CREATE TABLE map (key char, value char)');
        $db->insert('map', array('key' => 'foo', 'value' => 'bar'));
        $db->insert('map', array('key' => 'bar', 'value' => 'baz'));
    }

    /**
     * Runs a command and returns it output
     */
    public function runCommand(Application $app, $command)
    {
        $app['console']->setAutoExit(false);

        $fp = tmpfile();
        $input = new StringInput($command);
        $output = new StreamOutput($fp);

        $app['console']->run($input, $output);

        fseek($fp, 0);
        $output = '';
        while (!feof($fp)) {
            $output = fread($fp, 4096);
        }
        fclose($fp);

        return $output;
    }
}
