<?php

namespace Drupal\Ignite\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

use Drupal\Ignite\Vcs\Adapter\GitonomyRepository;
use Drupal\Ignite\Url\Url;
use Drupal\Ignite\Filesystem\Path;
use Drupal\Ignite\Vcs\Branch\Name as BranchName;

use InvalidArgumentException;

class SetupCommand extends Command
{
    const TEMPLATE_DEFAULT = 'git@github.com:FriendsOfDrupal/drupal-ignite-standard.git';

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
        $this->repository = new GitonomyRepository();
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

            return 1;
        }

        $output->writeln("Creating the new instance of the project...");

        if ($this->fs->has($docroot)) {
            $this->fs->deleteDir($docroot);
        }

        try {
            $this->repository->download(new Path($docroot), new Url(self::TEMPLATE_DEFAULT), new BranchName('master'), false);
        } catch (InvalidArgumentException $iae) {
            $output->writeln("Error while cloning the repository: " . $iae->getMessage());

            return 1;
        }

        $output->writeln("Done!");

        return 0;
    }
}
