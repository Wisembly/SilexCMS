<?php

namespace SilexCMS\Twig\Extension;

use Silex\Application;

class ForeignKeyExtension extends \Twig_Extension
{
    private $app;

    function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getName() {
        return "SilexCMS_ForeignKey";
    }

    public function getFunctions()
    {
        return array(
            "foreign" => new \Twig_Function_Method($this, "foreign"),
        );
    }

    public function foreign($dataset, $id)
    {
        if (!isset($dataset[0]['id'])) {
            return false;
        }

        foreach ($dataset as $data) {
            if ($data['id'] == $id) {
                return $data;
            }
        }

        return false;
    }
}