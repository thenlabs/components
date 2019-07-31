
# Components

*Components* es un paquete PHP que ofrece unas implementaciones genéricas y reutilizables para la creación de tipos de componentes personalizados. Dichas implementaciones cubren una serie de funcionalidades comunes en las estructuras formadas por componentes como es el caso de la propagación de eventos en árboles y la gestión de dependencias.

>*Components* es básicamente una implementación del patrón [Composite](https://es.wikipedia.org/wiki/Composite_(patr%C3%B3n_de_dise%C3%B1o)) por lo que es muy recomendable conocer el mismo para una mejor comprensión del proyecto.

## Instalación.

>Para hacer la instalación se requiere PHP >= 7.1

    composer require nubeculabs/components dev-master

## Introducción.

Del patrón [Composite](https://es.wikipedia.org/wiki/Composite_(patr%C3%B3n_de_dise%C3%B1o)), es sabido que básicamente existen dos tipos de componentes, los simples y los compuestos. La única diferencia entre ambos está en que los compuestos pueden contener a otros componentes, mientras que los simples solo pueden ser contenidos.

Por tanto, a la hora de crear un nuevo tipo de componente se tienen ambas opciones y en dependencia de las necesidades que se tenga es que se debe elegir la alternativa.

Un componente compuesto constituye el nodo raíz de un árbol, ya que el mismo puede tener varios hijos que a su vez pueden contener otros hijos tal y como se muestra en el siguiente diagrama.

```
$component (C)
    |
    |____$child1 (S)
    |____$child2 (S)
    |____$child3 (C)
    |       |
    |       |____$child31 (C)
    |       |____$child32 (S)
    |       |____$child33 (S)
    |
    |____$child4 (C)
    |       |
    |       |____$child41 (C)
    |               |____$child411 (C)
    |               |____$child412 (S)
    |               |____$child413 (S)
```
C: Compuesto, S: Simple

## Creando tipos de componentes.

El procedimiento para crear un nuevo tipo de componente es común tanto para los simples como para los compuestos. En ambos casos consiste en crear una clase que implemente una interfaz y use un *trait*.

### Ejemplo: Creando un componente simple.

```php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\ComponentTrait;

class SimpleComponent implements ComponentInterface
{
    use ComponentTrait;
}
```

### Ejemplo: Creando un componente compuesto.

```php

use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\CompositeComponentTrait;

class CompositeComponent extends SimpleComponent implements CompositeComponentInterface
{
    use CompositeComponentTrait;
}
```

>En el caso del ejemplo anterior, el componente compuesto hereda del componente simple. Esto es algo totalmente opcional y dependerá de las necesidades que se tengan para el diseño del mismo.

A la clase del nuevo componente se le podrá especificar todos los datos y métodos que se necesiten, pero es importante tener en cuenta que en el respectivo *trait* se incluye la implementación básica del componente la cual no debe ser alterada en la clase.

### Trabajando con las dependencias.

Por lo general los componentes son entidades que tienen ciertos tipos de dependencias las cuales clasificamos en tres tipos. Por una parte van a existir las **dependencias propias** que no son más que las que tiene el tipo de componente en cuestión. Además, existirán las **dependencias adicionales** de las cuales hablaremos más adelante, y en el caso de los componentes compuestos tendrán también las **dependencias de sus hijos**.

Todas las dependencias se obtienen a través del método `getDependencies()` el cual "intentará" devolver de manera ordenada todos los tipos antes mencionados. Decimos "intentará" porque la tarea de organizarlas muchas veces no puede ser resuelta de manera automática y en esos casos se requiere intervención manual.

#### Creando un tipo de dependencia.

Una dependencia es una instancia cuya clase implementa la interfaz `NubecuLabs\Components\DependencyInterface` la cual contiene cuatro métodos que deberán ser implementados en la clase.

El siguiente ejemplo muestra como crear un nuevo tipo de dependencia donde se muestra una implementación de los cuatro métodos. También se han implementado unos métodos *getters* y *setters* para la propiedad *uri*.

```php

use NubecuLabs\Components\DependencyInterface;

class ScriptAsset implements DependencyInterface
{
    protected $name;
    protected $version;
    protected $incompatibleVersions;
    protected $includedDependencies;
    protected $uri;

    public function __construct(string $name, ?string $version, ?string $incompatibleVersions = null, array $includedDependencies = [])
    {
        $this->name = $name;
        $this->version = $version;
        $this->incompatibleVersions = $incompatibleVersions;
        $this->includedDependencies = $includedDependencies;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getIncompatibleVersions(): ?string
    {
        return $this->incompatibleVersions;
    }

    public function getIncludedDependencies(): array
    {
        return $this->includedDependencies;
    }

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    public function getUri(string $uri): string
    {
        return $this->uri;
    }
}
```

En este caso, se ha creado un tipo de dependencia cuya clase es `ScriptAsset` donde a través del constructor se le puede especificar sus datos. Aclaramos que la implementación que se le de a la clase dependerá de sus necesidades.

El método `getName()` se explica por sí solo. Cuando se está procesando un grupo de dependencias y se encuentran dos con igual nombre, entonces se compararán los valores de los métodos `getVersion()` y `getIncompatibleVersions()` para determinar cual de las dos instancias será la que se incluirá en el resultado.

El método `getVersion()` debe devolver un [valor de versión exacta](https://getcomposer.org/doc/articles/versions.md#exact-version-constraint). Cuando se tienen dos dependencias de igual nombre y diferentes versiones, por defecto se tomará la de mayor versión.

Por otra parte el método `getIncompatibleVersions()` debe devolver un [rango de versiones](https://getcomposer.org/doc/articles/versions.md#version-range). Este valor se usa para determinar qué versiones son incompatibles con una dependencia. Por ejemplo, suponga hipotéticamente que se tienen dos dependencias de nombre 'jquery' cuyas versiones son 1.11.1 y 2.2.22 respectivamente y en el caso de la segunda, su método `getIncompatibleVersions()` devuelve el valor `<2.0`. En ese caso se producirá una excepción del tipo `NubecuLabs\Components\Exception\IncompatibilityException` ya que una dependencia indica explícitamente que es incompatible con la otra.

Por último, el método `getIncludedDependencies()` debe devolver en un *array* todas las otras dependencias que están incluidas dentro de la actual. Por ejemplo, suponga que existe un componente compuesto que tiene una dependencia llamada 'bootstrap-js' la cual incluye a otra dependencia de nombre 'modal-js', y dicho componente tiene un hijo que depende de 'modal-js'. Cuando en el componente padre se llame al método `getDependencies()`, el resultado contendrá solamente la dependencia 'bootstrap-js' dado que 'modal-js' se encuentra implítica en la primera.

#### Declarando las dependencias de los componentes.

Para declarar las dependencias de un componente, es preciso implementar en su clase el método `getOwnDependencies()` el cual debe devolver en un *array* todas las instancias de las dependencias tal y como se muestra en el siguiente ejemplo:

```php

use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\CompositeComponentTrait;

class CompositeComponent extends SimpleComponent implements CompositeComponentInterface
{
    use CompositeComponentTrait;

    // ...

    public function getOwnDependencies(): array
    {
        $jquery = new ScriptAsset('jquery', '1.12.1');
        $jquery->setUri('https://code.jquery.com/ui/1.12.1/jquery-ui.min.js');

        return [$jquery];
    }

    // ...
}
```

#### Declarando las dependencias adicionales.

En ocasiones, es necesario contar con ciertas dependencias que se determinan de alguna manera especial. Es ahí donde entra el concepto de las dependencias adicionales las cuales se definen en la clase del componente implementando el método `getAdditionalDependencies()` el cual también debe devolver un *array* con las instancias de las dependencias.

Es muy común que existan componentes que colaboren con otros donde en esos casos por lo general se necesita que el componente también tenga las dependencias que tiene el componente con el cual tiene la colaboración.

## Conociendo las características de los componentes.

Todos los componentes presentan una serie de propiedades comunes que vamos a comentar seguidamente.

Primeramente vamos a mencionar al **identificador único** la cual es una propiedad de solo lectura y permite que el componente pueda ser referenciado de manera segura. Su valor se asigna internamente y consiste en una cadena de caracteres aleatoria.

Otra propiedad que sirve para referenciar al componente es el **nombre** pero en este caso es un valor especificado por el usuario por lo que puede ocurrir que dos componentes o más se puedan llamar igual.

Otra propiedad a tener en cuenta es el **componente padre** cuyo valor puede ser nulo o un componente compuesto.

También existirá un **despachador de eventos** el cual será una instancia de `Symfony\Component\EventDispatcher\EventDispatcherInterface` creada internamente pero que también puede ser especificada por el usuario.

Para que los componentes puedan contener datos personalizados es que existe la propiedad **datos** cuyo valor no es más que un *array* asociativo.

En el caso de los componentes compuestos tendrán además un par de propiedades adicionales las cuales se corresponden con un *array* de **hijos** y el **despachador de eventos de captura**. Más adelante hablaremos de este último concepto.

## Trabajando con los componentes.

### Componentes simples.

#### Iterando sobre cada padre.

En el siguiente ejemplo el orden de iteración será: $child41, $child4 y $component.

```php
foreach ($child411->parents() as $parent) {
    # code...
}
```

>El método `getParents()` devuelve un *array* con todos los padres del componente.

### Componentes compuestos.

#### Iterando sobre cada hijo.

```php
foreach ($child4->children() as $component) {
    # code...
}
```

En el ejemplo anterior el orden de iteración sería: $child4, $child411, $child412 y $child413.

## TEMP.

Del patrón [Composite](https://es.wikipedia.org/wiki/Composite_(patr%C3%B3n_de_dise%C3%B1o)), es sabido que básicamente existen dos tipos de componentes, los simples y los compuestos. La única diferencia entre ambos está en que los compuestos pueden contener a otros componentes, mientras que los simples solo pueden ser contenidos.

Por lo general los componentes son entidades que tienen ciertos tipos de dependencias donde en el caso de los compuestos también dependerán de las dependencias que tengan sus hijos.

A la hora de utilizar un componente con un fin específico, en la mayoría de los casos será necesario darle un tratamiento determinado a sus dependencias lo cual puede resultar complejo dado que muchas veces estas dependencias presentan conflictos entre sí. En muchos casos esos conflictos pueden ser resueltos de manera automática pero en otros solo pueden ser resueltos por los usuarios.

Otra de las características más importantes que presentan estos componentes es que pueden reaccionar a eventos. Cuando en un componente hijo se produce un determinado evento entonces se va a producir lo que se conoce como propagación del evento.
