<?php

namespace SilexCMS\Set;

use Silex\Application;

interface SetInterface
{
    public function registerSet(Application $app);

    public function getSet($mappForeign = false);

    public function getRepository();
}