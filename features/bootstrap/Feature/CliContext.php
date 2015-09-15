<?php

namespace Feature;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Drupal\Ignite\Command\SetupCommand;
use Drupal\Ignite\Tests\FakeRepository;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

/**
 * Defines application features from the specific context.
 */
class CliContext implements Context, SnippetAcceptingContext
{
    /**
     * Current Application being run.
     *
     * @var Application
     */
    private $application;

    /**
     * Current Command being run.
     *
     * @var CommandInterface
     */
    private $command;

    /**
     * Current CommandTester associated to the Command being run.
     *
     * @var CommandTester
     */
    private $commandTester;

    /**
     * @AfterScenario
     */
    public function tearDownCliScenario()
    {
        if ($this->getDocRoot()) {
            $fs = new Filesystem(new Local('/'));
            $fs->deleteDir($this->getDocRoot());
        }
    }

    /**
     * @When I start a new Drupal Ignite setup
     * @Given I started a new Drupal Ignite setup
     */
    public function iStartANewDrupalIgniteSetup()
    {
        $this->application = new Application();
        $this->application->add(new SetupCommand('drig:setup', new FakeRepository()));

        $this->command = $this->application->find('drig:setup');
    }

    /**
     * @When I enter the following values for setup:
     */
    public function iEnterTheFollowingValuesForSetup(TableNode $table)
    {
        $this->commandTester = new CommandTester($this->command);

        $arguments = array_combine($table->getRow(0), $table->getRow(1));
        $arguments['docroot'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $arguments['docroot'];

        $this->commandTester->execute(array_merge([
            'command' => $this->command->getName(),
        ], $arguments));
    }

    /**
     * @Then I should see :string
     */
    public function iShouldSee($string)
    {
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute([
            'command' => $this->command->getName(),
        ]);

        expect($this->commandTester->getDisplay())->toMatch('/' . $string . '/i');
    }

    /**
     * @Then a new project should have been succesfully created
     */
    public function aNewProjectShouldBeSuccesfullyCreated()
    {
        expect(file_exists($this->getDocRoot()))->toBe(true);
    }

    /**
     * @Then the standard template should have been cloned from github
     */
    public function theStandardTemplateShouldHaveBeenClonedFromGithub()
    {
        expect(file_exists($this->getDocRoot() . DIRECTORY_SEPARATOR . '.git'))->toBe(true);
    }

    /**
     * @Then the its placeholders should have been replaced by the specified values
     */
    public function theItsPlaceholdersShouldHaveBeenReplacedByTheSpecifiedValues()
    {
        $drushMakeFile = $this->getDocRoot() . DIRECTORY_SEPARATOR . 'foo.make';
        $phingBuildFile = $this->getDocRoot() . DIRECTORY_SEPARATOR . 'build.xml';

        // check a file name replacement has been done
        expect(file_exists($drushMakeFile))->toBe(true);

        // check a file content replacement has been done
        expect(file_get_contents($phingBuildFile))->toMatch('/\<project\ name\=\"foo\"\ default\=\"dummy\"\>/');
    }

    private function getDocRoot()
    {
        return $this->commandTester->getInput()->getArgument('docroot');
    }

    private function getName()
    {
        return $this->commandTester->getInput()->getArgument('name');
    }
}
