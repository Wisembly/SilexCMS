<?php

namespace SilexCMS\Tests\Command;

use SilexCMS\Tests\Base;
use SilexCMS\Command\ClearCacheComand;

class DataSetTest extends Base
{
    public function testWithoutCacheEnabled()
    {
        $app = $this->createApplication();
        $response = $this->runCommand($app, 'silexcms:cache:clear');
        $this->assertContains('Cache manager not enabled, nothing to clear..', $this->runCommand($app, 'silexcms:cache:clear'));
    }

    public function testWithCacheEnabled()
    {
        $app = $this->createApplication(array(
            'silexcms.cache' => array(
                'active'    => true,
                'type'      => 'array',
            ),
        ));
        $response = $this->runCommand($app, 'silexcms:cache:clear');
        $this->assertContains('Cache version successfully updated!', $this->runCommand($app, 'silexcms:cache:clear'));
    }
}
