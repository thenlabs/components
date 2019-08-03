
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

A la clase del nuevo componente se le podrán especificar todos los datos y métodos que se necesiten, pero es importante tener en cuenta que en el respectivo *trait* se incluye la implementación básica del componente la cual no debería ser alterada en la clase.

### Introducción a las dependencias.

Por lo general los componentes son entidades que tienen ciertos tipos de dependencias las cuales clasificamos en tres tipos. Por una parte van a existir las **dependencias propias** que no son más que las que tiene el tipo de componente en cuestión. Además, existirán las **dependencias adicionales** de las cuales hablaremos más adelante, y en el caso de los componentes compuestos tendrán también las **dependencias de sus hijos**.

Todas las dependencias se obtienen a través del método `getDependencies()` el cual "intentará" devolver de manera ordenada todos los tipos antes mencionados. Decimos "intentará" porque la tarea de organizarlas muchas veces no puede ser resuelta de manera automática y en esos casos se requerirá intervención manual cuyo tema será abordado más adelante.

#### Creando un tipo de dependencia.

Una dependencia es una instancia cuya clase implementa la interfaz `NubecuLabs\Components\DependencyInterface` la cual contiene cuatro métodos que deberán ser implementados en la clase.

El siguiente ejemplo muestra como crear un nuevo tipo de dependencia donde se muestra una implementación de esos cuatro métodos. Adicionalmente se ha implementado un método *getter* y uno *setter* para la propiedad *uri* de la clase.

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

En este caso, se ha creado un tipo de dependencia cuya clase es `ScriptAsset` donde a través del constructor se le puede especificar sus datos. Aclaramos que la implementación que se le de a la clase dependerá de sus necesidades ya que solo con que implemente la interfaz `NubecuLabs\Components\DependencyInterface` bastará para que sus instancias sean consideradas como dependencias.

El método `getName()` se explica por sí solo. Cuando se está procesando un grupo de dependencias y se encuentran dos con igual nombre, entonces se compararán los valores de los métodos `getVersion()` y `getIncompatibleVersions()` para determinar cual de las dos instancias será la que se incluirá en el resultado.

El método `getVersion()` debe devolver un [valor de versión exacta](https://getcomposer.org/doc/articles/versions.md#exact-version-constraint). Cuando se tienen dos dependencias de igual nombre y diferentes versiones, por defecto se tomará la de mayor versión.

Por otra parte, el método `getIncompatibleVersions()` debe devolver un [rango de versiones](https://getcomposer.org/doc/articles/versions.md#version-range). Este valor se usa para determinar qué versiones son incompatibles con una dependencia. Por ejemplo, suponga hipotéticamente que se tienen dos dependencias de nombre 'jquery' cuyas versiones son 1.11.1 y 2.2.22 respectivamente y en el caso de la segunda, su método `getIncompatibleVersions()` devuelve el valor `<2.0`. En ese caso se producirá una excepción del tipo `NubecuLabs\Components\Exception\IncompatibilityException` ya que una dependencia indica explícitamente que es incompatible con la otra.

Por último, el método `getIncludedDependencies()` debe devolver en un *array* todas las otras dependencias que están incluidas dentro de la actual. Por ejemplo, suponga que existe un componente compuesto que tiene una dependencia llamada "bootstrap-js" la cual incluye a otra dependencia de nombre "modal-js", y dicho componente tiene un hijo que depende de "modal-js". Cuando en el componente padre se llame al método `getDependencies()`, el resultado contendrá solamente la dependencia "bootstrap-js" dado que "modal-js" se encuentra implítica en la primera.

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

En ocasiones, es necesario contar con ciertas dependencias que se determinan de alguna manera especial. Es ahí donde entra el concepto de las dependencias adicionales las cuales se definen implementando el método `getAdditionalDependencies()` en la clase del componente.

Dado que es muy común que ciertos componentes colaboren con otros, y en cuyos casos por lo general se necesita que dicho componente también tenga las dependencias de esos otros componentes, existe implementada una funcionalidad que posibilita de manera muy sencilla, indicar que el componente también incluya esas otras dependencias.

Para ello, basta con especificar una anotación del tipo `NubecuLabs\Components\Annotation\Component` sobre los atributos de la clase donde se van a referenciar componentes. Adicionalmente, se deberá usar el *trait* `NubecuLabs\Components\AdditionalDependenciesFromAnnotationsTrait` en la clase tal y como se muestra en el siguiente ejemplo.

```php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\ComponentTrait;
use NubecuLabs\Components\AdditionalDependenciesFromAnnotationsTrait;
use NubecuLabs\Components\Annotation\Component;

class SimpleComponent implements ComponentInterface
{
    use ComponentTrait;
    use AdditionalDependenciesFromAnnotationsTrait;

    /**
     * @Component
     */
    protected $otherComponent;

    public function getOtherComponent(): ?ComponentInterface
    {
        return $this->otherComponent;
    }

    public function setOtherComponent(?ComponentInterface $otherComponent): void
    {
        $this->otherComponent = $otherComponent;
    }

    // ...
}
```

De esta manera, cuando a un componente del tipo `SimpleComponent` se le llame a su método `getDependencies()` también incluirá las dependencias del componente que exista referenciado en el atributo `otherComponent`.

## Conociendo las características de los componentes.

Todos los componentes presentan una serie de propiedades comunes que seguidamente vamos a comentar solo de manera general y más adelante serán abordadas con ejemplos.

Primeramente vamos a mencionar al **identificador único** la cual es una propiedad de solo lectura y permite que un componente pueda ser referenciado de manera segura. Su valor se asigna internamente y consiste en una cadena de caracteres aleatoria.

Otra propiedad que sirve para referenciar a los componentes es el **nombre** pero en este caso es un valor especificado por el usuario por lo que puede ocurrir que dos componentes o más se puedan llamar igual.

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

Como puede verse, el primer argumento de la función se corresponde con el nombre del evento mientras que el segundo con algún tipo de *callback* que será ejecutado una vez que sobre el componente se produzca un evento de igual nombre. Puede verse que este *callback* recibe un único argumento cuyo valor será una instancia de la clase `NubecuLabs\Components\Event\Event` la cual contendrá información del respectivo evento en curso.

Solo los componentes compuestos pueden reaccionar a eventos en la etapa de la captura. Para hacer esto basta con especificar `true` como tercer argumento de la función `on()`.

```php
// listening for the event capture.
$component->on('click', function ($event) {
    // ...
}, true);
```

>Con el método `off()` es posible desvincular manejadores que antes hayan sido vinculados.

### Disparando eventos.

Para disparar un evento sobre un componente se debe llamar al método `dispatchEvent()`. A este se le debe indicar el nombre del evento así como una instancia de la clase `NubecuLabs\Components\Event\Event` la cual contendrá información útil sobre el respectivo evento.

```php
use NubecuLabs\Components\Event\Event;

// ...

$event = new Event;
$event->setSource($component);

$component->dispatchEvent('myevent', $event);
```

>Para crear un tipo de evento personalizado se debe crear una clase que extienda `NubecuLabs\Components\Event\Event`.

Cuando se llama al método `dispatchEvent()` de la manera antes mostrada, se producirá sobre el árbol la propagación del evento tal y como lo hemos comentado antes. Este método acepta un par de argumentos más que sirven para indicar si se debe efectuar la *captura* y/o el *burbujeo* del evento.

El siguiente ejemplo muestra como producir la *captura* pero no el *burbujeo*.

```php
$component->dispatchEvent('myevent', $event, true, false);
```

## Trabajando con los componentes.

### Componentes simples.

#### Iterando sobre cada padre.

En el siguiente ejemplo el orden de iteración será: `$child41`, `$child4` y `$component`.

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

En el ejemplo anterior el orden de iteración sería: `$child4`, `$child411`, `$child412` y `$child413`.
