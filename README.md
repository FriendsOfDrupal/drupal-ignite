Drupal Ignite
=============

This project contains a Drupal 7 template project that can be used to quickly set up a new environment.

NOTICE
------

This software is in early development stage and could still change a lot, so don't get mad if it still has a few raw edges :)

Usage
-----

Robo syntax:

```
robo setup [project-name] [project-domain] [document_root]
```

Where:
* [project-name] is your drupal site name
* [project-domain] is your drupal domain name
* [document-root] is the destination folder

Example:

```
robo setup my-blog myblog.loc /home/foobar/webapps/dev/blog
```

Drupal Ignite robo can also be executed without arguments, the wizard will guide
you to configure the environment:

```
$ robo setup
➜
➜             Drupal-ignite setup
➜
?  Please enter Site's Name: my-blog
?  Please enter Site's Domain: blog.tb.loc
?  Please enter Site's Root Folder: /home/foobar/webapps/dev/blog
```

See ```robo setup -h``` for all commands.

Installation
------------

* Run ```composer install``` from the root of the project
* Run ```bin/robo setup``` and provide an installation folder (eg: **/var/www/acme/website**), a domain and a site name (eg: **AcmeSite**), optionally a drupal ignite custom template git url.
* go to the installation folder (eg: ```cd /var/www/acme/website```)
* review and fix the parameters in the **build.loc.properties**, **build.dev.properties** and **build.stage.properties** files;
* start your database (MySQL, for instance);
* let phing build the local environment by typing ```bin/phing loc-app -verbose```.

Requirements
------------
* PHP 5.4.0+
* [Composer](https://getcomposer.org)
