<?php

namespace SilexCMS\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheCommand extends Command
{
    protected function configure()
    {
        $this->setName('silexcms:cache:clear')
            ->setDescription('Reset cache version to clear silexcms cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getSilexApplication();

        if (!isset($app['silexcms.cache.manager'])) {
            $output->writeln('Cache manager not enabled, nothing to clear..');
            return;
        }

        $app['silexcms.cache.manager']->update();
        $output->writeln('<info>Cache version successfully updated!</info>');
    }
}