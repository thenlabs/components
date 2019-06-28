
# Components

*Components* es un paquete PHP que ofrece unas implementaciones genéricas y reutilizables para la creación de tipos de componentes personalizados. Dichas implementaciones cubren una serie de funcionalidades comunes en las estructuras formadas por componentes como es el caso de la propagación de eventos en árboles y la gestión de dependencias.

>Components es básicamente una implementación del patrón [Composite](https://es.wikipedia.org/wiki/Composite_(patr%C3%B3n_de_dise%C3%B1o)) por lo que es muy recomendable conocer el mismo para una mejor comprensión del proyecto.

## Instalación.

>Para hacer la instalación se requiere PHP >= 7.1

    composer require nubeculabs/components dev-master

## Introducción.

Del patrón [Composite](https://es.wikipedia.org/wiki/Composite_(patr%C3%B3n_de_dise%C3%B1o)), es sabido que básicamente existen dos tipos de componentes, los simples y los compuestos. La única diferencia entre ambos está en que los compuestos pueden contener a otros componentes, mientras que los simples solo pueden ser contenidos.

Un componente compuesto constituye el nodo raíz de un árbol, ya que el mismo puede tener varios hijos que a su vez pueden ser compuestos también tal y como se muestra en el siguiente diagrama.

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
C: Compuesto, S: Simple

Por lo general los componentes son entidades que tienen ciertos tipos de dependencias donde en el caso de los compuestos también dependerán de las dependencias que tengan sus hijos.

A la hora de utilizar un componente con un fin específico, en la mayoría de los casos será necesario darle un tratamiento determinado a sus dependencias los cual puede resultar complejo dado que muchas veces estas dependencias pueden ser incompatibles entre sí.


