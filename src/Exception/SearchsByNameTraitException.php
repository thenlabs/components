<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Exception;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class SearchsByNameTraitException extends \Exception
{
    public function __construct()
    {
        parent::__construct('The SearchByNameTrait only can be used on a class that implements the CompositeComponentInterface.');
    }
}
