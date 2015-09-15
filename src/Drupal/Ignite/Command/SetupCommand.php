<?php

namespace Drupal\Ignite\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Plugin\ListPaths;
use League\Flysystem\Plugin\ListFiles;

use Drupal\Ignite\Vcs\Adapter\GitonomyRepository;
use Drupal\Ignite\Url\Url;
use Drupal\Ignite\Filesystem\Path;
use Drupal\Ignite\Vcs\Repository;
use Drupal\Ignite\Flysystem\Plugin\ListDirs;
use Drupal\Ignite\Flysystem\Plugin\RenameDirs;
use Drupal\Ignite\Flysystem\Plugin\RenameFiles;
use Drupal\Ignite\Flysystem\Plugin\ReplaceFileContent;

use InvalidArgumentException;

class SetupCommand extends Command
{
    const TEMPLATE_DEFAULT = 'git@github.com:FriendsOfDrupal/drupal-ignite-standard.git';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var array
     */
    private $placeholders;

    /**
     * @param string|null     $name
     * @param Repository|null $repository
     */
    public function __construct($name = null, Repository $repository = null)
    {
        parent::__construct($name);

        $this->repository = $repository ?: new GitonomyRepository();

        $this->filesystem = new Filesystem(new Local(self::FILESYSTEM_ROOT));
        $this->filesystem->addPlugin(new ListDirs());
        $this->filesystem->addPlugin(new ListFiles());
        $this->filesystem->addPlugin(new ListPaths());
        $this->filesystem->addPlugin(new RenameDirs());
        $this->filesystem->addPlugin(new RenameFiles());
        $this->filesystem->addPlugin(new ReplaceFileContent());
    }

    /**
     * @{inheritdoc}
     */
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
    }

    /**
     * @{inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Drupal Ignite setup");

        $name = $input->getArgument("name");
        $domain = $input->getArgument("domain");
        $docroot = $input->getArgument("docroot");

        if (!$name) {
            $output->writeln("Please enter Site's Name:");
        }

        if (!$domain) {
            $output->writeln("Please enter Site's Domain:");
        }

        if (empty($docroot)) {
            $output->writeln("Please enter Site's Document Root:");

            return 1;
        }

        $output->writeln("Creating the new instance of the project...");

        try {
            $this->repository->download(new Path($docroot), new Url(self::TEMPLATE_DEFAULT));
        } catch (InvalidArgumentException $iae) {
            $output->writeln("Error while cloning the repository: " . $iae->getMessage());

            return 1;
        }

        $this->filesystem->renameFiles($docroot, true, [
            '__name__' => $name,
            '__domain__' => $domain,
        ]);

        $this->filesystem->renameDirs($docroot, true, [
            '__name__' => $name,
            '__domain__' => $domain,
        ]);

        $this->filesystem->replaceFileContent($docroot, true, [
            '__name__' => $name,
            '__domain__' => $domain,
        ]);

        $output->writeln("Done!");

        return 0;
    }
}
