<?php

namespace Drupal\Ignite\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class SetupCommand extends Command
{
    /**
     * @var Filesystem
     */
    private $fs;

    protected function configure()
    {
        $this
            ->setName('drig:setup')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'The name of the project you are about to create.'
            )
            ->addArgument(
                'domain',
                InputArgument::OPTIONAL,
                'The domain of the project you are about to create.'
            )
            ->addArgument(
                'docroot',
                InputArgument::OPTIONAL,
                'The path on the filesystem where you will be storing the project.'
            )
        ;

        $this->fs = new Filesystem(new Local('/'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $docroot = $input->getArgument("docroot");

        $output->writeln("Drupal Ignite setup");

        if (!$input->getArgument("name")) {
            $output->writeln("Please enter Site's Name:");
        }

        if (!$input->getArgument("domain")) {
            $output->writeln("Please enter Site's Domain:");
        }

        if (empty($docroot)) {
            $output->writeln("Please enter Site's Document Root:");
        }

        $output->writeln("Creating the new instance of the project...");

        $this->fs->createDir($docroot);

        $output->writeln("Done!");
    }
}
