
<div align="center">
    <a href="https://github.com/thenlabs/components/blob/v1/LICENSE.txt" target="_blank">
        <img src="https://img.shields.io/github/license/thenlabs/components?style=for-the-badge">
    </a>
    <img src="https://img.shields.io/packagist/php-v/thenlabs/components?style=for-the-badge">
    <a href="https://travis-ci.com/github/thenlabs/components" target="_blank">
        <img src="https://img.shields.io/travis/com/thenlabs/components?style=for-the-badge">
    </a>
    <a href="https://twitter.com/ThenLabsOrg" target="_blank">
        <img src="https://img.shields.io/twitter/follow/thenlabs?style=for-the-badge">
    </a>
</div>

<br>

<h1 align="center">Components</h1>
<h3 align="center">A custom implementation of the Composite pattern in PHP for create component types with useful functionalities.</h3>

## 🌟 Features.

- Two component types(simple and composite).
- Tree structure.
- Dependency management.
- Useful methods for filter and iterate over the tree.
- Events dispatching.
- Event propagation(capture and bubbling) across the tree.
- Support of custom data.

## 🔌 Installation.

    $ composer require thenlabs/components

## 📖 Documentation.

1. 🇬🇧 English (Pending)
2. [🇪🇸 Español](https://thenlabs.org/es/doc/components/master/index.html)

## 🧪 Running the tests.

All the tests of this project was written with our testing framework [PyramidalTests][pyramidal-tests] wich is an extension of [PHPUnit][phpunit].

After clone this repository, install the Composer dependencies:

    $ composer install

Run PHPUnit:

    $ ./vendor/bin/phpunit

[phpunit]: https://phpunit.de
[pyramidal-tests]: https://github.com/thenlabs/pyramidal-tests

If you want to run the tests with a specific version of PHP, it is possible to use Docker as follows:

    $ docker run -it --rm -v "$PWD":/usr/src/myapp -w /usr/src/myapp php:7.2-cli php vendor/bin/phpunit

>Change 7.2 for the desired PHP version.
