<?php

namespace SilexCMS\Twig\Extension;

use Silex\Application;

class MapExtension extends \Twig_Extension
{
    private $app;

    function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getName() {
        return "SilexCMS_Map";
    }

    public function getFunctions()
    {
        return array(
            "map" => new \Twig_Function_Method($this, "map"),
        );
    }

    public function map($dataset, $key)
    {
        $mapped = [];

        if (empty($key)) {
            throw new \Exception("You must provide a valid key");
        }

        if (!isset($dataset[0][$key])) {
            throw new \Exception("Could not map with '{$key}' key");
        }

        foreach ($dataset as $data) {
            $mapped[$data[$key]] = $data;
        }

        return $mapped;
    }
}