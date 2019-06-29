
# Components

*Components* es un paquete PHP que ofrece unas implementaciones genéricas y reutilizables para la creación de tipos de componentes personalizados. Dichas implementaciones cubren una serie de funcionalidades comunes en las estructuras formadas por componentes como es el caso de la propagación de eventos en árboles y la gestión de dependencias.

>Components es básicamente una implementación del patrón [Composite](https://es.wikipedia.org/wiki/Composite_(patr%C3%B3n_de_dise%C3%B1o)) por lo que es muy recomendable conocer el mismo para una mejor comprensión del proyecto.

## Instalación.

>Para hacer la instalación se requiere PHP >= 7.1

    composer require nubeculabs/components dev-master

## Creando tipos de componentes.

Del patrón [Composite](https://es.wikipedia.org/wiki/Composite_(patr%C3%B3n_de_dise%C3%B1o)), es sabido que básicamente existen dos tipos de componentes, los simples y los compuestos. La única diferencia entre ambos está en que los compuestos pueden contener a otros componentes, mientras que los simples solo pueden ser contenidos.

Por tanto, a la hora de crear un nuevo tipo de componente se tienen ambas opciones y en dependencia de las necesidades que se tenga es que se debe elegir la alternativa.

El procedimiento para crear un nuevo tipo de componente es común tanto para los simples como para los compuestos. En ambos casos consiste en crear una clase que implemente una interfaz y use un *trait*.

##### Ejemplo: Creando un componente simple.

```php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\ComponentTrait;

class SimpleComponent implements ComponentInterface
{
    use ComponentTrait;
}
```

##### Ejemplo: Creando un componente compuesto.

```php

use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\CompositeComponentTrait;

class CompositeComponent extends SimpleComponent implements CompositeComponentInterface
{
    use CompositeComponentTrait;
}
```

Como se muestra en el ejemplo anterior, el componente compuesto extiende del componente simple lo cual es algo totalmente opcional y dependerá de las necesidades que se tengan.

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

## TEMP.

Del patrón [Composite](https://es.wikipedia.org/wiki/Composite_(patr%C3%B3n_de_dise%C3%B1o)), es sabido que básicamente existen dos tipos de componentes, los simples y los compuestos. La única diferencia entre ambos está en que los compuestos pueden contener a otros componentes, mientras que los simples solo pueden ser contenidos.

Por lo general los componentes son entidades que tienen ciertos tipos de dependencias donde en el caso de los compuestos también dependerán de las dependencias que tengan sus hijos.

A la hora de utilizar un componente con un fin específico, en la mayoría de los casos será necesario darle un tratamiento determinado a sus dependencias lo cual puede resultar complejo dado que muchas veces estas dependencias presentan conflictos entre sí. En muchos casos esos conflictos pueden ser resueltos de manera automática pero en otros solo pueden ser resueltos por los usuarios.

Otra de las características más importantes que presentan estos componentes es que pueden reaccionar a eventos. Cuando en un componente hijo se produce un determinado evento entonces se va a producir lo que se conoce como propagación del evento.
