<?php
declare(strict_types=1);

namespace NubecuLabs\Components\Tests\Entity;

use NubecuLabs\Components\Annotation\Component;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait AnnotatedPropertiesTrait
{
    /**
     * @Component
     */
    private $property1;

    /**
     * @Component
     */
    protected $property2;

    /**
     * @Component
     */
    public $property3;

    public function setProperty1($value): void
    {
        $this->property1 = $value;
    }

    public function setProperty2($value): void
    {
        $this->property2 = $value;
    }
}
