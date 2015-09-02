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
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @When I start a new Drupal Ignite setup
     * @Given I started a new Drupal Ignite setup
     */
    public function iStartANewDrupalIgniteSetup()
    {
        $this->application = new Application();
        $this->application->add(new SetupCommand());

        $this->command = $this->application->find('drig:setup');
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
     * @When I enter the following values for setup:
     */
    public function iEnterTheFollowingValuesForSetup(TableNode $table)
    {
        $arguments = array_combine($table->getRow(0), $table->getRow(1));

        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(array_merge([
            'command' => $this->command->getName(),
        ], $arguments));
    }

    /**
     * @Then a new project should be succesfully created
     */
    public function aNewProjectShouldBeSuccesfullyCreated()
    {
        $docroot = $this->commandTester->getInput()->getArgument('docroot');

        expect(file_exists($docroot))->toBe(true);
    }
}
