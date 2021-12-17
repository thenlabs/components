# Components

A custom implementation of the Composite pattern in PHP for create component types with useful functionalities.

>If you like this project gift us a ⭐.

## Features.

- PHP >= 7.2
- Two component types(simple and composite).
- Tree structure.
- Dependency management.
- Useful methods for filter and iterate over the tree.
- Events dispatching.
- Event propagation(capture and bubbling) across the tree.
- Support of custom data.

## Installation.

    $ composer require thenlabs/components

## Documentation.

1. English (Pending)
2. [Español](https://thenlabs.org/es/doc/components/master/index.html)

## Development.

Clone this repository and install the Composer dependencies.

    $ composer install

### Running the tests.

All the tests of this project was written with our testing framework [PyramidalTests][pyramidal-tests] wich is based on [PHPUnit][phpunit].

Run tests:

    $ composer test

[phpunit]: https://phpunit.de
[pyramidal-tests]: https://github.com/thenlabs/pyramidal-tests