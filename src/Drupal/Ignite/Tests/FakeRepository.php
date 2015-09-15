<?php

namespace Drupal\Ignite\Tests;

use Drupal\Ignite\Vcs\Repository;
use Drupal\Ignite\Filesystem\Path;
use Drupal\Ignite\Url\Url;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

use RuntimeException;

final class FakeRepository implements Repository
{
    /**
     * @var Filesystem
     */
    private $fs;

    public function __construct()
    {
        $this->fs = new Filesystem(new Local('/'));
    }

    public function download(Path $path, Url $url)
    {
        $this->createBasepath((string)$path);
        $this->createGitFolder((string)$path);
        $this->createDrushMakeFile((string)$path);
        $this->createPhingBuildFile((string)$path);
        $this->createDrupalInstallProfile((string)$path);
    }

    private function createBasepath($basePath)
    {
        if ($this->fs->has($basePath)) {
            throw new RuntimeException("Directory $basePath already exists.");
        }

        $this->fs->createDir($basePath);
    }

    private function createGitFolder($basePath)
    {
        $gitFolderPath = $basePath . DIRECTORY_SEPARATOR . '.git';

        if ($this->fs->has($gitFolderPath)) {
            throw new RuntimeException("Directory $gitFolderPath already exists.");
        }

        $this->fs->createDir($gitFolderPath);
    }

    private function createDrushMakeFile($basePath)
    {
        $drushMakeFilePath = $basePath . DIRECTORY_SEPARATOR . '__name__.make';
        $drushMakeFileContent = <<<INI
core = 7.x

api = 2

projects[] = "drupal"

; Modules

;; Contrib
projects[boxes][subdir] = "contrib"
projects[boxes][version] = "1.1"
INI;

        $this->fs->write($drushMakeFilePath, $drushMakeFileContent);
    }

    private function createPhingBuildFile($basePath)
    {
        $buildFilePath = $basePath . DIRECTORY_SEPARATOR . 'build.xml';
        $buildFileContent = <<<XML
<?xml version="1.0" encoding="UTF-8"?>

<project name="__name__" default="dummy">
    <target name="dummy">
        <echo>Dummy task. It's here to prevent unwanted default execution of the build script.</echo>
    </target>
</project>
XML;
        $this->fs->write($buildFilePath, $buildFileContent);
    }

    private function createDrupalInstallProfile($basePath)
    {
        $drupalInstallProfilePath = $basePath . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR . '__name__';
        $drupalInstallProfileInfoFilePath = $drupalInstallProfilePath . DIRECTORY_SEPARATOR . '__name__.info';
        $drupalInstallProfileInfoFileContent = <<<INI
name = __originalname__
description = Install Profile for __originalname__
version = 1.0-dev
core = 7.x

dependencies[] = block
INI;

        if ($this->fs->has($drupalInstallProfilePath)) {
            throw new RuntimeException("Directory $drupalInstallProfilePath already exists.");
        }

        $this->fs->createDir($drupalInstallProfilePath);

        $this->fs->write($drupalInstallProfileInfoFilePath, $drupalInstallProfileInfoFileContent);
    }
}
