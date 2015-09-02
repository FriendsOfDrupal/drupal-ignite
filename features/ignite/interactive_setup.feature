Feature: developer creates a new project interactively

    As a developer
    I want to create a new project interactively
    So that I can specify the parameters step by step

    Scenario: start setup
        When I start a new Drupal Ignite setup
        Then I should see "Drupal Ignite setup"
        And I should see "Please enter Site's Name:"

    Scenario: configure instance via command-line options
        Given I started a new Drupal Ignite setup
        When I enter the following values for setup:
            | name | domain  | docroot            |
            | foo  | foo.com | /tmp/drig-test/foo |
        Then a new project should be succesfully created
