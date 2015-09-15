<?php

namespace spec\Drupal\Ignite\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

use Drupal\Ignite\Tests\FakeRepository;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class SetupCommandSpec extends ObjectBehavior
{
    /**
     * @var string
     */
    private $docroot;

    /**
     * @var Filesystem
     */
    private $fs;

    function let()
    {
        $this->beConstructedWith('drig:setup', new FakeRepository());

        $this->fs = new Filesystem(new Local('/'));

        $this->docroot = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'drig-test' . DIRECTORY_SEPARATOR . 'foo';

        $this->fs->deleteDir($this->docroot);
    }

    function letGo()
    {
        $this->fs->deleteDir($this->docroot);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Symfony\Component\Console\Command\Command');

        $this->getDefinition()->getArgument('name')->shouldNotThrow('InvalidArgumentException');
        $this->getDefinition()->getArgument('domain')->shouldNotThrow('InvalidArgumentException');
        $this->getDefinition()->getArgument('docroot')->shouldNotThrow('InvalidArgumentException');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('drig:setup');
    }

    function it_can_run(OutputInterface $output)
    {
        $input = new ArrayInput([]);

        $this->run($input, $output)->shouldReturn(1);
    }

    function it_welcomes_the_user(OutputInterface $output)
    {
        $input = new ArrayInput([]);

        $output->writeln(Argument::any())->shouldBeCalled();

        $output->writeln("Drupal Ignite setup")->shouldBeCalled(1);

        $this->run($input, $output);
    }

    function it_prompts_for_the_site_name_when_one_is_not_provided_as_argument(OutputInterface $output)
    {
        $input = new ArrayInput([]);

        $output->writeln(Argument::any())->shouldBeCalled();

        $output->writeln("Please enter Site's Name:")->shouldBeCalled(1);

        $this->run($input, $output);
    }

    function it_prompts_for_the_site_domain_when_one_is_not_provided_as_argument(OutputInterface $output)
    {
        $input = new ArrayInput(['name' => 'foo']);

        $output->writeln(Argument::any())->shouldBeCalled();

        $output->writeln("Please enter Site's Domain:")->shouldBeCalled(1);

        $this->run($input, $output);
    }

    function it_prompts_for_the_docroot_when_one_is_not_provided_as_argument(OutputInterface $output)
    {
        $input = new ArrayInput(['name' => 'foo', 'domain' => 'foo.com']);

        $output->writeln(Argument::any())->shouldBeCalled();

        $output->writeln("Please enter Site's Document Root:")->shouldBeCalled(1);

        $this->run($input, $output);
    }

    function it_creates_a_new_instance(OutputInterface $output)
    {
        $input = new ArrayInput([
            'name' => 'foo',
            'domain' => 'foo.com',
            'docroot' => $this->docroot,
        ]);

        $output->writeln("Drupal Ignite setup")->shouldBeCalled(1);

        $output->writeln("Creating the new instance of the project...")->shouldBeCalled(1);

        $output->writeln("Done!")->shouldBeCalled(1);

        $this->run($input, $output);

        expect($this->fs->has($this->docroot))->toBe(true);
    }

    function it_clones_a_template(OutputInterface $output)
    {
        $input = new ArrayInput([
            'name' => 'foo',
            'domain' => 'foo.com',
            'docroot' => $this->docroot,
        ]);

        $this->run($input, $output);

        expect($this->fs->has($this->docroot . DIRECTORY_SEPARATOR . '.git'))->toBe(true);
    }

    function it_replaces_file_name_placeholders(OutputInterface $output)
    {
        $input = new ArrayInput([
            'name' => 'foo',
            'domain' => 'foo.com',
            'docroot' => $this->docroot,
        ]);

        $this->run($input, $output);

        expect($this->fs->has($this->docroot . DIRECTORY_SEPARATOR . 'build.xml'))->toBe(true);
        expect($this->fs->has($this->docroot . DIRECTORY_SEPARATOR . 'foo.make'))->toBe(true);
        expect($this->fs->has($this->docroot . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'foo.info'))->toBe(true);
    }

    function it_replaces_file_content_placeholders(OutputInterface $output)
    {
        $input = new ArrayInput([
            'name' => 'foo',
            'domain' => 'foo.com',
            'docroot' => $this->docroot,
        ]);

        $this->run($input, $output);

        expect(file_get_contents($this->docroot . DIRECTORY_SEPARATOR . 'build.xml'))
            ->toMatch('/\<project\ name\=\"foo\"\ default\=\"dummy\"\>/');
    }
}
