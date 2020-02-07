
# Components

*Components* es un paquete PHP que ofrece unas implementaciones genéricas y reutilizables para la creación de tipos de componentes personalizados. Dichas implementaciones cubren una serie de funcionalidades comunes en las estructuras formadas por componentes como es el caso de la propagación de eventos en árboles y la gestión de dependencias.

>*Components* es básicamente una implementación del patrón [Composite](https://es.wikipedia.org/wiki/Composite_(patr%C3%B3n_de_dise%C3%B1o)) por lo que es muy recomendable conocer el mismo para una mejor comprensión de este proyecto.

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
    |       |        |____$child411 (C)
    |       |        |____$child412 (S)
    |       |        |____$child413 (S)
    |       |
    |       |____$child42 (S)
```
C: Compuesto, S: Simple

## Creando tipos de componentes.

El procedimiento para crear un nuevo tipo de componente es común tanto para los simples como para los compuestos. En ambos casos consiste en crear una clase que implemente una interfaz y use un *trait*.

### Ejemplo: Creando un componente simple.

```php

use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\ComponentTrait;

class SimpleComponent implements ComponentInterface
{
    use ComponentTrait;
}
```

### Ejemplo: Creando un componente compuesto.

```php

use ThenLabs\Components\CompositeComponentInterface;
use ThenLabs\Components\CompositeComponentTrait;

class CompositeComponent extends SimpleComponent implements CompositeComponentInterface
{
    use CompositeComponentTrait;
}
```

>En el caso del ejemplo anterior, el componente compuesto hereda del componente simple. Esto es algo totalmente opcional y dependerá de las necesidades que se tengan para el diseño del mismo.

A la clase del nuevo componente se le podrán especificar todos los datos y métodos que se necesiten, pero es importante tener en cuenta que en el respectivo *trait* se incluye la implementación básica del componente la cual no debería ser alterada en la clase.

### Introducción a las dependencias.

Por lo general los componentes son entidades que tienen ciertos tipos de dependencias las cuales clasificamos en tres tipos. Por una parte van a existir las **dependencias propias** que no son más que las que tiene el tipo de componente en cuestión. Además, existirán las **dependencias adicionales** de las cuales hablaremos más adelante, y en el caso de los componentes compuestos tendrán también las **dependencias de sus hijos**.

Todas las dependencias se obtienen a través del método `getDependencies()` el cual "intentará" devolver de manera ordenada todos los tipos antes mencionados. Decimos "intentará" porque la tarea de organizarlas muchas veces no puede ser resuelta de manera automática y en esos casos se requerirá intervención manual cuyo tema será abordado más adelante.

#### Creando un tipo de dependencia.

Una dependencia es una instancia cuya clase implementa la interfaz `ThenLabs\Components\DependencyInterface` la cual contiene cuatro métodos que deberán ser implementados en la clase.

El siguiente ejemplo muestra como crear un nuevo tipo de dependencia donde se muestra una implementación de esos cuatro métodos. Adicionalmente se ha implementado un método *getter* y uno *setter* para la propiedad *uri* de la clase.

```php

use ThenLabs\Components\DependencyInterface;

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

>El *trait* `ThenLabs\Components\EditableDependencyTrait` brinda una implementación genérica que contiene métodos *getters* y *setters* para cada una de las propiedades de una dependencia.

En este caso, se ha creado un tipo de dependencia cuya clase es `ScriptAsset` donde a través del constructor se le puede especificar sus datos. Aclaramos que la implementación que se le de a la clase dependerá de sus necesidades ya que solo con que implemente la interfaz `ThenLabs\Components\DependencyInterface` bastará para que sus instancias sean consideradas como dependencias.

El método `getName()` se explica por sí solo. Cuando se está procesando un grupo de dependencias y se encuentran dos con igual nombre, entonces se compararán los valores de los métodos `getVersion()` y `getIncompatibleVersions()` para determinar cual de las dos instancias será la que se incluirá en el resultado.

El método `getVersion()` debe devolver un [valor de versión exacta](https://getcomposer.org/doc/articles/versions.md#exact-version-constraint). Cuando se tienen dos dependencias de igual nombre y diferentes versiones, por defecto se tomará la de mayor versión.

Por otra parte, el método `getIncompatibleVersions()` debe devolver un [rango de versiones](https://getcomposer.org/doc/articles/versions.md#version-range). Este valor se usa para determinar qué versiones son incompatibles con una dependencia. Por ejemplo, suponga hipotéticamente que se tienen dos dependencias de nombre 'jquery' cuyas versiones son `1.11.1` y `2.2.22` respectivamente y en el caso de la segunda, su método `getIncompatibleVersions()` devuelve el valor `<2.0`. En ese caso se producirá una excepción del tipo `ThenLabs\Components\Exception\IncompatibilityException` ya que una dependencia indica explícitamente que es incompatible con la otra.

Por último, el método `getIncludedDependencies()` debe devolver en un *array* todas las otras dependencias que están incluidas dentro de la actual. Por ejemplo, suponga que existe un componente compuesto que tiene una dependencia llamada "bootstrap-js" la cual incluye a otra dependencia de nombre "modal-js", y dicho componente tiene un hijo que depende de "modal-js". Cuando en el componente padre se llame al método `getDependencies()`, el resultado contendrá solamente la dependencia "bootstrap-js" dado que "modal-js" se encuentra implítica en la primera.

#### Declarando las dependencias de los componentes.

Para declarar las dependencias de un componente, es preciso implementar en su clase el método `getOwnDependencies()` el cual debe devolver en un *array* todas las instancias de las dependencias tal y como se muestra en el siguiente ejemplo:

```php

use ThenLabs\Components\CompositeComponentInterface;
use ThenLabs\Components\CompositeComponentTrait;

class CompositeComponent extends SimpleComponent implements CompositeComponentInterface
{
    // ...

    public function getOwnDependencies(): array
    {
        $jquery = new ScriptAsset('jquery', '1.12.1');
        $jquery->setUri('https://code.jquery.com/ui/1.12.1/jquery-ui.min.js');

        return [$jquery];
    }
}
```

#### Declarando las dependencias adicionales.

En ocasiones, es necesario contar con ciertas dependencias que se determinan de alguna manera especial. Es ahí donde entra el concepto de las dependencias adicionales las cuales se definen implementando el método `getAdditionalDependencies()` en la clase del componente.

Dado que es muy común que ciertos componentes colaboren con otros, y en cuyos casos por lo general se necesita que dicho componente también tenga las dependencias de esos otros componentes, existe implementada una funcionalidad que posibilita de manera muy sencilla, indicar que el componente también incluya esas otras dependencias.

Para ello, basta con especificar una anotación del tipo `ThenLabs\Components\Annotation\Component` sobre los atributos de la clase donde se van a referenciar componentes. Adicionalmente, se deberá usar el *trait* `ThenLabs\Components\AdditionalDependenciesFromAnnotationsTrait` en la clase tal y como se muestra en el siguiente ejemplo.

```php

use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\ComponentTrait;
use ThenLabs\Components\AdditionalDependenciesFromAnnotationsTrait;
use ThenLabs\Components\Annotation\Component;

class SimpleComponent implements ComponentInterface
{
    use ComponentTrait;
    use AdditionalDependenciesFromAnnotationsTrait;

    /**
     * @Component
     */
    protected $otherComponent;

    // ...
}
```

De esta manera, cuando a un componente del tipo `SimpleComponent` se le llame a su método `getDependencies()` también incluirá las dependencias del componente que exista referenciado en el atributo `otherComponent`.

## Conociendo las características de los componentes.

Todos los componentes presentan una serie de propiedades comunes que seguidamente vamos a comentar solo de manera general y más adelante serán abordadas con ejemplos.

Primeramente vamos a mencionar al **identificador único** la cual es una propiedad de solo lectura y permite que un componente pueda ser referenciado de manera segura. Su valor se asigna internamente y consiste en una cadena de caracteres aleatoria.

Otra propiedad que sirve para referenciar a los componentes es el **nombre** pero en este caso es un valor especificado por el usuario por lo que puede ocurrir que dos componentes o más se puedan llamar de igual manera.

Otra propiedad a tener en cuenta es el **componente padre** cuyo valor puede ser nulo o algún componente compuesto.

También existirá un **despachador de eventos** el cual será una instancia de la clase `Symfony\Component\EventDispatcher\EventDispatcherInterface` creada internamente aunque también puede ser especificada por el usuario.

Para que a los componentes se les pueda especificar datos personalizados es que existe la propiedad **datos** la cual no es más que un *array* asociativo.

En el caso de los componentes compuestos tendrán además un par de propiedades adicionales las cuales se corresponden con un *array* para referenciar a los componentes **hijos** y un **despachador de eventos de captura**. Más adelante hablaremos de este último concepto.

## Trabajando con eventos.

Una de las características más importantes que presentan estos componentes es que pueden reaccionar a eventos. Cuando en un componente hijo se produce un determinado evento, se va a producir por la respectiva rama del árbol lo que se conoce como propagación del evento, cuyo concepto es tomado del [DOM](https://es.wikipedia.org/wiki/Document_Object_Model) presente en los navegadores web.

La propagación de los eventos tiene lugar en tres etapas diferentes denominadas *captura*, *ejecución* y *burbujeo*. Dar una explicación detallada sobre estos conceptos no es objetivo de esta guía, no obstante, en [este enlace](https://www.tutorialrepublic.com/javascript-tutorial/javascript-event-propagation.php) puede encontrarse documentación al respecto.

### Registrando manejadores de eventos.

A través del método `on()` se puede vincular un nombre de evento con un manejador tal y como se muestra en el siguiente fragmento.

```php
$component->on('click', function ($event) {
    // ...
});
```

Como puede verse, el primer argumento de la función se corresponde con el nombre del evento mientras que el segundo con algún tipo de *callback* que será ejecutado una vez que sobre el componente se produzca un evento de igual nombre. Puede verse que este *callback* recibe un único argumento cuyo valor será una instancia de la clase `ThenLabs\Components\Event\Event` la cual contendrá información del respectivo evento en curso.

>La clase `ThenLabs\Components\Event\Event` hereda de `Symfony\Component\EventDispatcher\Event`.

Solo los componentes compuestos pueden reaccionar a eventos en la etapa de la captura. Para hacer esto basta con especificar `true` como tercer argumento de la función `on()`.

```php
// listening for event capture
$component->on('myevent', function ($event) {
    // ...
}, true);

// listening for event capture
$child4->on('myevent', function ($event) {
    // ...
}, true);

$child4->on('myevent', function ($event) {
    // ...
});

// listening for event bubbling
$child4->on('myevent', function ($event) {
    // ...
});

// listening for event bubbling
$component->on('myevent', function ($event) {
    // ...
});
```

El ejemplo anterior se muestra el orden de ejecución de los manejadores de evento cuando sobre `$child4` se dispara el evento de nombre 'myevent'. Puede verse como desde la raíz del árbol comienza a desencadenarse la captura hasta llegar a la ejecución en el propio componente, donde más tarde se regresa a la raíz en la etapa del burbujeo. Es importante aclarar que sobre los padres, los manejadores que se ejecutan en la etapa de captura no son los mismos que los que se ejecutan durante el burbujeo.

>Con el método `off()` es posible desvincular manejadores que antes hayan sido vinculados.

### Disparando eventos.

Para disparar un evento sobre un componente se debe llamar al método `dispatchEvent()`. A este se le debe indicar el nombre del evento así como una instancia de la clase `ThenLabs\Components\Event\Event` la cual contendrá información útil sobre el respectivo evento.

```php
use ThenLabs\Components\Event\Event;

// ...

$event = new Event;
$event->setSource($component);

$component->dispatchEvent('myevent', $event);
```

>Para crear un tipo de evento personalizado se debe crear una clase que extienda `ThenLabs\Components\Event\Event`.

Cuando se llama al método `dispatchEvent()` de la manera antes mostrada, se producirá sobre el árbol la propagación del evento tal y como lo hemos comentado antes. Este método acepta un par de argumentos más que sirven para indicar si se debe efectuar la *captura* y/o el *burbujeo*.

El siguiente ejemplo muestra como producir la *captura* pero no el *burbujeo*.

```php
$component->dispatchEvent('myevent', $event, true, false);
```

## Trabajando con los componentes.

Seguidamente mostraremos algunos de los aspectos más específicos para el trabajo con los componentes. En los *traits* de los componentes existen varios métodos que no mencionamos aquí pero que por razones obvias se puede deducir su existencia. Recomendamos mirar las implementaciones de esos *traits* para un mayor conocimiento de la API.

### Iterando sobre cada padre.

El método `parents()` puede ser usado para iterar sobre cada padre de un componente. En el siguiente ejemplo el orden de iteración sería:

1. $child41
2. $child4
3. $component

```php
foreach ($child411->parents() as $parent) {
    // ...
}
```

>El método `getParents()` devuelve un *array* con todos los padres del componente.

### Iterando sobre cada hijo.

En el caso de los componentes compuestos, a través del método `children()` es posible hacer una recorrido en profundidad sobre el árbol que estos representan.

En el siguiente ejemplo el orden de iteración sería:

1. $child41
2. $child411
3. $child412
4. $child413
5. $child42

```php
foreach ($child4->children() as $component) {
    // ...
}
```

### Haciendo búsquedas en árboles.

Hacer búsquedas en el árbol que representa un componente compuesto es sencillo gracias a los métodos `findChild()` y `findChilds()`. En ambos casos se les debe pasar un *callback* que será usado como criterio de búsqueda. En el caso del primer método se usará para buscar un solo componente por lo que la búsqueda finalizará con la primera coincidencia, mientras que en el caso del segundo deberá ser usado cuando se necesite buscar a más de un componente.

El *closure* del siguiente ejemplo sirve para determinar si un componente es compuesto o no. Vea a través de las expresiones los resultados de cada caso.

```php
$callback = function ($child) {
    if ($child instanceof CompositeComponentInterface) {
        return true;
    } else {
        return false;
    }
};

$component->findChild($callback) === $child3; // true
$component->findChilds($callback) === [$child3, $child31, $child4, $child41, $child411]; // true
```

Existe implementado un método llamado `findChildById(string $id)` que sirve para buscar un componente por su identificador único.

Para hacer búsquedas por nombre existen los métodos `findChildByName(string $name)` y `findChildsByName(string $name)`. Tal y como podrá suponer, en el caso del primero devolverá el primer componente cuyo nombre coincida con el argumento, mientras que en el caso del segundo devolverá en un *array* todas las coincidencias.

### Validando los tipos de los hijos.

Un componente compuesto por defecto acepta cualquier instancia de la interfaz `ThenLabs\Components\ComponentInterface` como un hijo, pero en ciertas ocasiones se desea restringir que solo ciertos tipos sean aceptados.

Para implementar esta restricción se debe implementar en la clase el método `validateChild()` tal y como se muestra en el siguiente ejemplo:

```php
use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\CompositeComponentInterface;
use ThenLabs\Components\CompositeComponentTrait;

class CompositeComponent extends SimpleComponent implements CompositeComponentInterface
{
    // ...

    public function validateChild(ComponentInterface $child): bool
    {
        if ($child instanceof SimpleComponent) {
            return true;
        } else {
            return false;
        }
    }
}
```

En el ejemplo anterior, el componente solo aceptará como hijo a los que sean del tipo `SimpleComponent`.

Desde un componente compuesto se puede insertar uno o varios hijos a través de los métodos `addChild($child)` y `addChilds($child1, $child2, ...)` respectivamente. Además de esto la llamada al método `setParent($parent)` de cualquier componente provoca que el padre registre al hijo automáticamente. Siempre que haya alguna inserción de un componente hijo primeramente se llamará al método `validateChild($child)` sobre el padre y si el resultado es `true` la inserción se llevará a cabo normalmente. En caso contrario, se producirá una excepción del tipo `ThenLabs\Components\Exception\InvalidChildException`.

### Conociendo los eventos internos.

Varias de las operaciones que hemos mostrado anteriormente provocan que internamente se lancen ciertos eventos sobre determinados componentes los cuales vamos a comentar seguidamente.

>La mayoría de los nombres de los eventos internos se corresponden con el nombre de su respectiva clase.

>Para conocer los datos que almacena cada tipo de evento se debe mirar la API de la respectiva clase.

Por defecto cuando a un componente compuesto se le añade un nuevo hijo se produce sobre dicho componente un evento de tipo `ThenLabs\Components\Event\BeforeInsertionEvent` el cual tiene lugar antes de que la inserción se haya efectuado, y otro de tipo `ThenLabs\Components\Event\AfterInsertionEvent` después de que la misma fue llevada a cabo.

De manera similar ocurre cuando un hijo es separado del árbol, donde en esos casos los eventos que se producen son de tipo `ThenLabs\Components\Event\BeforeDeletionEvent` y `ThenLabs\Components\Event\AfterDeletionEvent` respectivamente.

Una funcionalidad que presentan los componentes compuestos es que se les puede especificar el orden de sus hijos a través del método `setChildrenOrder(array $order)` el cual recibirá un *array* de cadenas cuyos valores deben ser los identificadores únicos de los hijos en el orden deseado. Ejemplo:

```php
$id411 = $child411->getId();
$id412 = $child412->getId();
$id413 = $child413->getId();

$child4->setChildrenOrder([$id413, $id411, $id412]);
```

Al realizar esta operación se producirán un par de eventos de tipo `BeforeOrderEvent` y `AfterOrderEvent`.

>Los eventos de tipo *before* pueden cancelar la operación.

Por otra parte, cuando sobre un componente se le llama a su método `getDependencies()` se produce sobre el mismo un evento del tipo `ThenLabs\Components\Event\FilterDependenciesEvent` después de que las dependencias fueron organizadas internamente y justo antes de devolver el resultado final. Este evento puede ser usado para modificar de manera dinámica las dependencias de un determinado componente. El nombre de este evento tiene la forma `ThenLabs\Components\Event\FilterDependenciesEvent_{$component->getId()}`.

Tal y como comentamos anteriormente, en ocasiones las dependencias de los componentes presentan conflictos entre sí donde muchas veces esos conflictos necesitan ser resueltos manualmente. Cuando se produce un conflicto de este tipo, se lanza en el componente un evento del tipo `ThenLabs\Components\Event\DependencyConflictEvent` cuyo objetivo consiste en que a través del objeto del evento se especifique la solución del conflicto.

>Cuando un conflicto de dependencias no es resuelto se lanzará una exceptión del tipo `ThenLabs\Components\Exception\UnresolvedDependencyConflictException`.

El siguiente ejemplo, muestra como tomar la dependencia de menor versión cuando en el componente `$child4` se produzca un conflicto.

```php
use ThenLabs\Components\Event\DependencyConflictEvent;
use Composer\Semver\Comparator;

// ...

$child4->on(DependencyConflictEvent::class, function (DependencyConflictEvent $event) {
    $dependency1 = $event->getDependency1();
    $dependency2 = $event->getDependency2();
    $version1 = $dependency1->getVersion();
    $version2 = $dependency2->getVersion();

    if (Comparator::lessThan($version1, $version2)) {
        $event->setSolution($version1);
    } else {
        $event->setSolution($version2);
    }
});
```

Tenga en cuenta que gracias a la captura y burbujeo de los eventos, los conflictos podrían ser resueltos desde cualquier padre en alguna de estas etapas.

### Datos personalizados.

Gracias al método `setData(string $key, $value)` es posible asignar un determinado dato a un componente, mientras que con `getData(string $key)` es posible obtenerlo.

Dada la estructura de árbol que forman los componentes, a veces se necesita que un determinado hijo tome cierta información de la rama a la que pertenece. Gracias a la función `getTopData(string $key)` esto puede ser posible de una manera sencilla tal y como se muestra en el siguiente ejemplo.

```php
$key = 'mydata';

$component->setData($key, 10);
$child4->setData($key, 20);

$child411->getTopData($key) === 20; // true
```

El ejemplo anterior muestra como el respectivo dato es buscado por cada padre del componente.
