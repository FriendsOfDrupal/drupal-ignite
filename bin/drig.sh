#!/bin/bash

# Get drig script's directory
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Run Drupal Ignite's setup using RoboTask
$DIR/robo setup
