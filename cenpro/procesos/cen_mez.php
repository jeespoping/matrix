<?php
include_once("conex.php");



include_once("root/comun.php");






/*======================================================DOCUMENTACION APLICACION==========================================================================

APLICACION PARA CENTRAL DE MEZCLAS EN MATRIX

1. AREA DE DESCRIPCION FUCNIONAL

1.1 DESCRIPCION:

La central de mezclas tiene un proceso que consiste en la generación de productos a partir de insumos. Estos insumos son trasladados
desde otros centros de costo a la central. Algunos insumos son fraccionables y otros no fraccionables. Los fraccionables son manejados
por los centros de costos que proveen a la central en una unidad determinada (por ejemplo ampolla) y la central los fracciona utilizando
otra unidad, por ejemplo (mililitros). El sistema de inventario para la central utilizara para el llevar el kardex de los insumos
unos codigos propios con la unidad minima de trabajo (por ejemplo mililitros), estos insumos estaran relacionados en una tabla
con las diferentes presentaciones para el insumo y la unidad en que los utilizan en los demás centros de costo.

Previo a la produccion, se hace una lista de los productos pedidos por piso, acompañados por la cantidad, y se consulta en el sistema
el stock existente, con lo anterior y los estándares establecidos para el número de productos que deben quedar en bodega (el super habit),
se sabe cuanto debe ser la producción inicial.

Existen tres tipos de productos:
Codificados: Son aquellos productos que tienen codigo o existencia en Unix
No codificados, dosis adaptadas: sin codigo en Unix y generalemente con pocos insumos
No codificados, nutriciones: sin codigo en Unix y generalemente con mas de tres insumos

Una vez determinada la cantidad de producción inicial necesaria se empieza el proceso de creación de productos en el sistema así:
Los productos codificados ya existiran en la mayoria de los casos en el sistema (serán cargados antes de la salida a produccion del sistema).
los no codificados puede que ya existan en el sistema, si alguna vez se creó algun producto con los mismos insumos y cantidades respectivas.
Los productos que no existan en el sistema deberán ser creados como incio del proceso de produccion, para ser almacenados en el maestro de
inventario del sistema (alli existiran los insumos cargados a traves de formulario y los productos que sean creados).

Una vez el producto exista en el sistema (en el maestro de inventario), se creara el lote de produccion.
La cantidad de productos del lote dependerá de la cantidad de producto solicitada y el superhabit existente. Cuando se crea el lote,
el regente debera seleccionar para cada insumo la cantidad que usara de cada presentacion. Para esto, el sistema le indicara
si quedan algunas fracciones de algunas presentaciones que puedan ser aprovechadas. El regente puede aprovechar estas fracciones para la
creacion del lote o puede descartarlas y destapar nuevas presentaciones. El sistema almacenara las cantidades de cada presentacion
en la unidad minima, la fecha de vencimiento de la ultima presentacion usada y su factor de conversion a la unidad en Unix,
por lo que en cualquier momento sabra cuantas unidades completas existen en inventario y cuantas fracciones aprovechables. Una vez
se hay indicado las cantidades para cada presentacion, el sistema validara su disponiblidad en el inventario y que la suma de las cantidades
de cada presentacion concuerden con la cantidad requerida de cada insumo para el lote, segun la cantidad de producto indicado. Si se
pasa la validacion, el sistema descontara los insumos del kardex, descontara la cantidad respectiva a cada presentacion y sumara la cantidad
de producto al kardex y creará un nuevo lote en la tabla de lotes del sistema. Adicionalmente se generara un movimiento de salida de insumos
y un movimiento de entrada de productos. Los productos deben ser rotulados utlizando el codigo del producto y el lote.

Los productos de la central son cargados a pacientes o vendidos a entidades externas. En el caso de cargo a pacientes,
se ingresara el codigo del producto y de esta manera el sistema sabra si se carga el producto (para productos codificado)
o se cargan los insumos (para productos no codificados). En caso de ser un producto no codificado, el regente deberá seleccionar
que presentacion de cada insumo le sera cargada al paciente. Como la entidad responsable no admite que se le cobre mas de lo necesario
el sistema almacenara un saldo a favor del paciente, en caso de que el tratamiento continue y requiera el mismo producto, este saldo se
indicara en el sistema al igual que la necesidad o no de cargar de nuevo una presentacion. El proceso de carga, valida la existencia del la
historia clinica a la que se le va a hacer el cargo, si el paciente esta activo y no esta de alta, valida de disponiblidad del producto,
la existencia del producto o los insumos (producto codificado o no respectivamente) y realiza el cargo al paciente a través del integrador
destinado para tal fin y actualizando la tabla de saldos de paciente. Adicionalmente descuenta el producto del kardex y del lote y graba un
movimeinto de salida por cargo.

Para la venta a otras entidades, existe en unix tarifa en mililitros para los diferentes insumos de la central de mezclas
y la venta se realiza como la suma de las tarifas de los insumos, por lo que en matrix, solamente se requerira para los
productos de venta a entidades externas, que sean descontados del inventario, es decir, señalar los productos y las cantidades
respectivas, los cuales serán descontados del kardex  y del lote y se graba un movimeinto de salida por venta.

Existiran algunos productos que no podrán usarse por averia, para esto el sistema debera permitir seleccionar el producto y el centro
de costos al que se responsabiliza de la avería. Si es la misma central de mezclas, el sistema  unicamente
descontará el producto del kardex  y del lote y grabara un movimeinto de salida por averia. Cuando el a otro centro de costos,
se realizar adicionamente un traslado en un unix, del producto si es codificado o de los insumos (para no codificado), en el
ultimo caso, el regente seleccionara las presentaciones que serán cargadas al piso. Esto esta por verificarse con auditoria.

1.2 OBJETIVO

Sistematizar la gestión de inventario y cargo a pacientes para la central de mezclas, de manera que se lleve un inventario teorico
lo más ajustado al físico.


1.3 CARACTERISTICAS O HISTORIAS

1. TRASLADO DE INSUMOS HACIA LA CENTRAL

Precondiciones:
Maestro de tipos de articulos llenos
Usuario registrado con centro costos de un centro que realiza traslados y no tiene inventario en mAtrix

Pasos:
*El sistema indentifica el codigo del usuario y el centro de costos al que pertenece, de esta manera despliega como centros
de origen del traslado aquellos que no tienen inventario en matrix y de destino, lo contrario
*El usuario selecciona el centro de origen y destino respectivamente
*El usuario busca el articulo que desea trasladar por codigo, o nombre comercial o generico
*El sistema le despliega los articulos encontrados con el parametro de busqueda
*el usuario selecciona el articulo, el lote en caso de ser un producto y la cantidad
*El usuario repite los dos ultimos pasos segun el numero de articulos a trasladar
*El usuario activa la opcion crear y da click en grabar
*El sistema valida las cantidades disponibles en unix para el centro de costos origen y la existencia de los articulos en matrix
*Si pasa las validaciones el sistema realiza el movimiento de inventario segun se decribe en area dinamica, sino despliega mensaje


2. TRASLADOS DESDE LA CENTRAL
Precondiciones:
Maestro de tipos de articulos llenos
Usuario registrado con centro costos de un centro que realiza traslados y tiene inventario en MAtrix

Pasos:
*El sistema indentifica el codigo del usuario y el centro de costos al que pertenece, de esta manera despliega como centros
de origen del traslado aquellos que tienen inventario en matrix y de destino, lo contrario
*El usuario selecciona el centro de origen y destino respectivamente
*El usuario busca el articulo que desea trasladar por codigo, o nombre comercial o generico
*El sistema le despliega los articulos encontrados con el parametro de busqueda, los lotes disponibles y las cantidades por lote
si es un producto, o las presentaciones disponibles si es un insumo
*El usuario selecciona el articulo, el lote en caso de ser un producto y la cantidad
*El usuario repite los dos ultimos pasos segun el numero de articulos a trasladar
*El usuario activa la opcion crear y da click en grabar
*El sistema valida las cantidades disponibles en matrix para el centro de costos origen y la existencia de los articulos en unix
para el centro de costos destino
*Si pasa las validaciones el sistema realiza el movimiento de inventario segun se decribe en area dinamica, sino despliega mensaje

3. CONSULTA DE TRASLADOS

Pasos:
* El usuario selecciona la forma de busqueda del traslado (numero de traslado, codigo del producto, centro de costos)
e ingresa el valor de busqueda. Si la forma de busqueda es por codigo de articulo o centro de costos,
el sistema pregunta tambien un rago de fechas
* El sistema despliega los traslados encontrados para el parametro de busqueda
* El usuario selecciona el traslado
* El sistema despliega todos los detalles del traslado

4. CREACION DE PRODUCTO

Precondiciones:
Maestro de tipos de productos llenos
Maestro de articulos lleno con los insumos que se van a utilizar

Pasos:
* El usuario selecciona el tipo de producto que desea crear e ingresa los datos generales del producto
Si el producto es codificado, se cargan automaticamente el codigo, la presentacion y el nombre generico y comercial del producto
Si es un producto no codificado y el nombre no es compuesto, el codigo se carga a partide del indicador
y el concecutivo para el tipo de producto, el nombre sera el nombre del tipo de producto
Si el producto es no codificado y con nombre compuesto, el codigo se carga a partir del indicador y el consecutivo para el tipo de
producto, el nombre se cagara al ingresar los insumos que hacen parte de el
* El usuario busca uno de los insumos que hacen parte del producto, por nombre o por codigo
* El sistema despliega la lista de insumos encontrados con los parametros de busqueda
* El usuario ingresa el insumo seleccionando uno del resultado de busqueda e indicando la cantidad para el producto
* Se repiten los ultimos 3 pasos hasta ingresar todos los insumos que hacen parte del producto
Si el producto es no codificado con nombre compuesto, los insumos deben ser ingresados, en el orden que haran parte del nombre
Y dejar chuleados aquellos que forman parte del nombre
Si se quiere retirar un insumo de la lista, se da click sobre el cuadrito correspondiente de anular
* Una vez ingresados todos los insumos, se activa Crear y se da click en grabar
* El sistema valida que todos los datos generales esten completos
* El sistema valida que se hallan ingresado al menos dos articulos
* El sistema valida que no existe ya otro producto con los mismos insumos y cantidades
* El sistema graba el producto como se describe en el área de descripcion dinamica

5. MODIFICACION DE PRODUCTOS

Precondiciones:
Debe existir el producto en el maestro de articulos
Debe existir el detalle de los insumos del producto
No se puede haber creado ningun lote con ese producto, en ese caso ya no puede ser modificado, debe desactivarse y crearse un producto nuevo

Pasos:
* El usuario selecciona la forma de busqueda del producto y el parametro de busqueda
Si la forma de busqueda es por insumo, el sistema pregunta tambien la cantidad del insumo en el producto
* El sistema despliega los porductos encontrados para el parametro de busqueda
* El usuario selecciona el producto
* El sistema despliega todas las caracteristicas del prodcuto
* El usuario puede modificar las caracterisiticas generales del producto o adicionar insumos o elimiar insumos
* El usuario da click sobre la opcion modificar
* El sistema valida que al menos se hallan dejado dos insumos
* El sistema valida que todos los datos generales esten completos
* El sistema valida que no existe ya otro producto con los mismos insumos y cantidades
* El sistema graba los cambios pertinentes segun se describe en area de descripcion dinamica


6. DESACTIVACION DE PRODUCTOS

Precondiciones:
Debe existir el producto en el maestro de articulos
Debe existir el detalle de los insumos del producto

Pasos:
* El usuario selecciona la forma de busqueda del producto y el parametro de busqueda
Si la forma de busqueda es por insumo, el sistema pregunta tambien la cantidad del insumo en el producto
* El sistema despliega los porductos encontrados para el parametro de busqueda
* El usuario selecciona el producto
* El sistema despliega todas las caracteristicas del prodcuto
* El usuario da click sobre la opcion desactivar
* El sistema graba los cambios pertinentes segun se describe en area de descripcion dinamica (estado del producto en off)

7. CONSULTA DE PRODUCTO

Precondiciones:
Debe existir el producto en el maestro de articulos
Debe existir el detalle de los insumos del producto

Pasos:
* El usuario selecciona la forma de busqueda del producto y el parametro de busqueda
Si la forma de busqueda es por insumo, el sistema pregunta tambien la cantidad del insumo en el producto
* El sistema despliega los porductos encontrados para el parametro de busqueda
* El usuario selecciona el producto
* El sistema despliega todas las caracteristicas del prodcuto

8. CREACION DE LOTES

Precondiciones:
El producto esta previemente creado
Hay existencias de todos los insumos

Pasos:
*El usuario busca el producto mediante el caso de uso, busqueda de producto
*El sistema le despliega las caracteristicas del producto y un enlace para crear lote
*El sistema presenta el formaulario de creacion del lote, e indica si hay otros lotes con saldo para ese producto
*El usuario indica la cantidad de producto a realizar
*El sistema despliega la cantidad de insumo necesaria para crear el lote, la lista de presentaciones para cada insumo
con sus existencias en la unidad minima de trabajo, cantidad de unidades y cantidad en fraccion, adicionalmente la fecha
de vencimiento de la fraccion.
* el usuario activa la opcion crear y da click en grabar
* El sistema valida la existencia de los insumos, y de las presentaciones en las cantidades ingresadas, ademas que las
cantidades ingresadas para las diferentes presentaciones sumen la cantidad requerida por insumo. Si no se pasan estas
validaciones, el sistema despliega un mensaje de alerta y recomienda las cantidades que esten disponibles, señalando los
insumos en rojo
* El sistema almacena la informacion relevante al lote, como se indica en el area de descripcion dinamica

9. ANULACION DE LOTES

Precondiciones:
El lote esta previemente creado

Pasos:
* El usuario busca el lote como se describe en la historia Consulta de lotes
* El sistema despliega todas las carateristicas del lote
* El usuario da click sobre la opcion anular
* El sistema valida que no se halla consumido ningun producto del lote, Si es asi ya no se puede ser anulado el lote
* En caso de pasar validacion el sistema anula el lote y los movminetos de creacion como se describe en area dinamica


11. CONSULTA DE LOTES

Precondiciones:
El lote esta previemente creado

Pasos:
* El usuario selecciona la forma de busqueda del lote y el parametro indicado
* El sistema busca los lotes con la forma y parametro indicado y despliega la lista de lotes,
mostrando la informacion del primero en la lista
* El usuario puede seleccionar de la lista (select), el lote que le interesa
* El sistema depliega la informacion del lote seleccionado

12. Impresion de rotulos
13. Cargo a pacientes
14. Devolucion de cargos
15. Consulta de cargos o devolucion de cargos a paciente
16. Cargo de averias propias
17. Devolucion de averias propias
18. Cargo de averias a otros centros de costo
19. Devolucion de cargos de averias de otros centros de costo
20. Consulta de averias
21. Salida de productos por venta a entidades
22. Entrada de productos por devolucion de venta a entidades
23. Consulta de movimientos por venta a entidades
24. Descarte de fracciones de insumos por presentacion
25. Consulta de movimientos de Descarte de fracciones de insumos por presentacion
26. Anulacion de movimientos de Descarte de fracciones de insumos por presentacion

1.4 CARACTERISTICAS DE SEGURIDAD
* Para realizar un traslado, el usuario puede trasladar desde un centro de costos con inventario en matrix, si
tiene registrado como centro de costos alguno con inventario en matrix, de lo contrario podra trasladar desde un
centro de costos sin inventario en matrix. Mas adelante se pensara en una tabla de usuarios por centro de costos

2. AREA DE DESCRIPCION ESTRUCTURAL

2.1 TABLAS PROPIAS DEL GRUPO
cenpro_000001 Maestro de tipos de articulos (dice si es producto o no y dice si es o no codificado entre otros)
cenpro_000002 Maestro de articulos (articulos de la central: productos o insumos propios de la central con su unidad minima de trabajo)
cenpro_000003 Detalle de producto (lista los insumos que forman el producto y sus cantidades)
cenpro_000004 Lotes (lista los lotes numero y producto, con su fecha de vencimiento, cantidad incial y saldo)
cenpro_000005 Kardex de inventario (cantidades para los productos y los insumos propios de la central)
cenpro_000006 Encabezado de movimiento de inventario
cenpro_000007 Detalle de movimiento de inventario
cenpro_000008 Conceptos de inventario (los diferentes conceptos con su consecutivo e indicador de salida o entrada)
cenpro_000009 Tabla de presentaciones (las diferentes presentaciones con el insumo propio al que corresponde y la cantidad que hay)
cenpro_000010 Tabla de ajustes de (para una histori e ingreso determinado cuando se tiene de saldo de una presentacion)

2.2 OTRAS TABLAS DE MATRIX
movhos_000026 Tabla general de articulos (los mismos articulos de unix, allis existiran como articulos las presentaciones)
movhos_000011 Tabla de centros de costos (alli se indica el centro de costos que realiza traslados y el concepto que utiliza para ello)
farstore_000002  Maestro de unidades (para consultar el nombre de la unidad de medida)

2.1 TABLAS DE UNIX
ivmov tabla de encabezado de movimeintos de inventario
ivmovdet tabal de detalle de movimientos de inventario
ivsal tabla de inventario por centro de costos
itedro tabla para el integrador de cargos a paciente


3. AREA DE DESCRIPCION DINAMICA

Creacion de producto
cenpro_000001 ----- modifica------ 1
cenpro_000002 ------crea-----------1
cenpro_000003 ------crea-----------n

Modificacion de un producto
cenpro_000002 ------modifica-----------1
cenpro_000003 ------modifica----------n

Desactivacion de un producto
cenpro_000002 ------modifica-----------1

Creacion de lote
000006-----------crea----------------------------2 (1 movmiento de entrada y un movimiento de salida)
000007-----------crea----------------------------n (n movimientos de salida, 1 movimeinto de entrada)
000005-----------modifica/crea-------------------n (aumenta el kardex del producto o lo crea si no existe, disminuye kardex de insumos)
000009-----------modifica------------------------n
000004-----------crea----------------------------1

Anulacion de lote
000006-----------modifica en off----------------------------2 (1 movmiento de entrada y un movimiento de salida)
000007------------modifica en off---------------------------n (n movimientos de salida, 1 movimeinto de entrada)
000005-----------modifica-----------------------------------1
000004-----------modifica en off----------------------------1
000009-----------modifica-----------------------------------n

Traslado de Matrix a Unix
000006-----------crea----------------------------1 (un movimiento de salida)
000007-----------crea----------------------------n (n movimientos de salida)
000005-----------modifica------------------------n (disminuye kardex)
000009-----------modifica------------------------n (disminuye kardex si hay articulos codificados)
000004-----------crea----------------------------n (disminuye kardex si hay productos)
ivmov-----------crea-----------------------------1
ivmovdet--------crea-----------------------------n (igual a cantidad de articulos trasladados)
ivsal-----------modifica-------------------------n (ivsal del centro de costos destino)


Traslado de Unix a Matrix
000006-----------crea----------------------------1 (un movimiento de entrada)
000007-----------crea----------------------------n (n movimientos de entrada)
000005-----------modifica------------------------n (aumenta kardex)
000009-----------modifica------------------------n (aumenta kardex si hay articulos codificados)
000004-----------crea----------------------------n (aumenta kardex si hay productos)
ivmov-----------crea-----------------------------1
ivmovdet--------crea-----------------------------n (igual a cantidad de articulos trasladados)
ivsal-----------modifica-------------------------n (ivsal del centro de costos origen)

========================================================DOCUMENTACION PROGRAMA================================================================================*/
/*

1. AREA DE VERSIONAMIENTO

Nombre del programa:cen_mez.php
Fecha de creacion: 2007-06-15
Autor: Carolina Castano P
Ultima actualizacion:
2019-11-19	Jessica	- Se modifica el redondeo de la fracción del producto (Deffra en movhos_000059) para que tenga en cuenta 3 
					  decimales y así evitar que la cantidad quede redondeada al hacer el reemplazo del producto.
2019-09-20	Jessica	- Se agrega filtro de tipo de documento al hacer join entre root_000036 y root_000037 para evitar obtener información incorrecta.
2019-08-29	Edwin MG - Se modifica de donde se obtiene el valor de la concentración del medicamento utilizado para la preparación de la dosis adaptada cuando
					   la unidad es diferente del maestro de articulos de central (cenpro 2) y la orden médica(movhos 54) ya que que debe tomar el valor de la 
					   tabla de fracciones de articulo( movhos 59 )
2018-05-21	Jessica -  En la función pintarFormulario() y mostrarCantidadDAPendientes() se agrega a la consulta de movhos_000224 el filtro 
					   con cenpro_000002 para saber si la dosis adaptada esta activa, de esta forma si la inactivaron permite crear otra, 
					   para así poder crear el lote y reemplazarla ya que antes de este cambio si creaban una dosis adaptada y la desactivaban 
					   solo ya no podían crear otra.
					- En la función grabarRelacionDA() si ya existe el registro de la dosis adaptada en movhos_000224 permite actualizarlo.
2018-04-03	Edwin	- Si la unidad del medicamento prescrito por el medico es diferente a la unidad del insumo en central de mezclas
					  se modifica el calculo para que adicional al valor de conversión se tenga en cuenta la concentración del articulo.
			Jessica	- Se redondea la dosis a dos decimales
2018-02-15	- Se habilita el select de instituciones de las NPT ya que si esta disabled al guardar el registro de las NPT en cenpro_000002 
			  quedaba vacio el campo si era la institucion 01-Clinica las americas
			- Se cambia la descripción GENERAR por IMPRIMIR ROTULO, y debe tener lotes creados para poder visualizar el rotulo  
2017-10-03	- Se agrega filtro adicional en la funcion consultarRangoEtario() para evitar tener en cuenta el rango etario  TODOS, 
			  ya que si hay un neonato con cero días no estaba quedando con rango etario N.
2017-09-20	- Se agrega la opción quitar marca DA en la lista de dosis adaptadas pendientes de crear.
			- Se modifica la posicion de los manuales adicionales ya en algunos casos no se visualizaba correctamente.
2017-09-12	Se agrega la funcionalidad de cargar automaticamente las dosis adaptadas (medicamento con dosis ordenada por el medico y su
			vehiculo de dilucion y realiza la sugerencia de dextrosas o salinos si es el caso), se muestra la lista de dosis adaptadas 
			nuevas (marcados como DA, antibioticos y DA genericas) pendientes decrear y con su contador. Se carga el codigo equivalente 
			de central de mezclas del medicamento prescrito desde ordenes con la misma dosis (no se modifica), si es un medicamento compuesto 
			toma la dosis del antibiotico, si tiene purga diferente a cero en el campo Cccopda de movhos_000011 aplica la purga para 
			la dosis y los diluyentes. Si el paciente es neotato muestra una lista de jeringas y solo podrá seleccionar una, si es pediatrico 
			puede elegir una jeringa o un diluyente, si es adulto sugiere un diluyente.
2017-05-15	Se agrega el parametro codigoEquiposNPTneonatos y se valida segun la edad del paciente si el equipo es para neonatos o no.
2016-09-19	Cuando se crean las NPT se oculta el boton crear
2016-09-08	Se agrega contador de NPT pendientes.
2016-08-03	Para nutriciones parenterales se carga la prescripcion de insumos desde ordenes, si es kardex u otra institucion continua funcionando como lo 
			hacía anteriormente (debe ingresar manualmente la prescripcion para cada uno de los insumos).
2013-02-27	Edwin Molina Grisales. Cambio la ruta de cargos.php a cargoscm.php
2012-12-04	Edwin Molina Grisales. Cuando se crea un producto, la via se agrega a la tabla de FRACCION DE ARTICULOS (movhos_000059) y se cambia las vias de administracion de cm  
			que se encuentran en det_selecciones por el de movimientos hospitalario(movhos_000040), para ello se crea un campo nuevo en (movhos_000040) que indica
			el codigo en cm.
2010-08-26	Edwin Molina Grisales. Se parametriza la carga de insumos automaticos al momento de crear productos tipo nutriciones parenterales.
			Para ello se crea un campo en la tabla 000002 que indica el volumen mínimo y otro que indica el volumen máximos, para que un insumo
			se cargue automaticamente, el producto debe estar entre estos dos valores y el campo artord debe ser diferente de 0.
2007-08-22  Carolina Castano para las nutriciones se guardara la historia clinica y se calcularan insumos a partir de peso y purga
207-07-12   Carolina Castano para las nutciones se cargan por defecto los insumos tipicos de una nutricion
207-07-09   Carolina Castano se agrega que se pueda modificar foto y neve
2007-07-06  Carolina Castano Publicacion


2. AREA DE DESCRIPCION:

Este script realiza las historias:

4. Creacion de productos
5. Modificacion de productos
6. Desactivacion de productos
7. Consulta de productos


3. AREA DE VARIABLES DE TRABAJO

NOMBRE        TIPO         DESCRIPCION

$cantidad     numerico      Cantidad de un insumo que se ingresar a la lista de insumos que componene el producto
$clase        caracter      variable concatenada con el distintivo del tipo de producto, el consecutivo y si es codificado o no
$codi         boolean       El producto es o no codificado
$compu        boolean       El nombre del producto se forma a partir de los insumos que lo componen
$consulta     caracter      Producto escogido en el select al consultar un producto por un parametro determinado
$consultas    vector        Lista de resultados de la busqueda de productos por un parametro determiando
$contador     numerico      Cuenta para los productos no codificados con nombre compuesto, cuantos insumos hacen parte del nombre
$crear		  boolean       Viene de un check box e indica si se desea grabar el producto
$eli          boolean       Viene de un check box e indica si se desea sacar un insumo de la lista
$eliminar	  numerica      Indica cual posicion del vector de insumos es la que se desea eliminar
$estado		  caracter      Estado del producto, Inicio, Creado o desactivado
$fecha		  date          Fecha de creacion del producto
$forbus		  caracter		Forma de busqueda de un producto codificado en unix, por codigo o por nombre
$forbus2      caracter      Forma de busqueda de un insumo para ingresar como componenete del producto
$forcon       caracter      Forma de busqueda para el producto en Matrix, por codigo, nombre o insumo
$foto		  boolean       Indica si el producto es fotosensible o no
$insfor       caracter      Forma de busqueda del insumo cuando se usa busqueda de producto por insumo, por codigo o por nombre
$inslis       vector        Vector con los insumos que hacen parte del producto
$insumo       caracter      Insumo que se va a ingresar en la lista de insumos que forman el producto
$insumos      vector        Lista de insumos encontrados bajo parametro de busqueda, se despliegan en drop down
$neve         boolean       Se deben guardar en nevera inmediatemente o no
$parbus       caracter      Parametro de busqueda de un producto en unix (el codigo o el nombre que se va a buscar)
$parbus2      caracter      Parametro de busqueda de un insumo, el nombre o el codigo de un producto
$parcon       caracter      Parametro de busqueda de un producto (el codigo o el nombre que se va a buscar)
$parcon2      caracter      Cuando se busca por insumo, parbus es el codigo o nombre de insumo y parbus2 es la cantidad
$presentacion caracter      Unidad de medida seleccionada para el producto
$presentaciones vector      Lista de unidades de medida que pueden ser seleccionadas para el producto
$producto     caracter      Producto seleccionado de la lista de productos encontrados bajo un parametro de busqueda
$productos    vector        Lista de productos encontrados bajo un parametro de busqueda
$realizar     carater       Indica si se ha pedido realizar una 'modifiacion' o 'desactivar' un producto
$repetido     boolean       Indica si un insumo que se ha ingresado ya esta en la lista, para sumar las cantidades
$tfd          numerico      Tiempo de infusion en dias
$tfh          numerico      Tiempo de infusion en horas, que se adiciona a los dias (No se usa por el momento realmente)
$tin          numerico      Timpo de infusion total en horas ($tfd*24 + $tfh )
$tipos        vector        Vector con tipos de productos que existen en el maestro
$tippro       caracter      Tipo de producto que fue seleccionado por el usuario
$tvd          numerico      Tiempo  de vencimiento en dias
$tve          numerico      Tiempo de vencimiento en horas adicional a los dias
$tvh          numerico      Tiempo total de vencimiento ($tvh*24 + $tvh)
$val          boolean       Si es positivo indica que ya existe un producto con la misma composicion
$via          caracter      Via de administracion seleccionada por el usuario para el producto
$vias         vector        Lista de vias de administracion que se pueden seleccionar para el producto


4. Area de Tablas
cenpro_000001 Select, Update
cenpro_000002 Select, Insert, Update
cenpro_000003 Select, Insert, Delete
movhos_000026 Select
farstore_000002 Select

*/

/*========================================================FUNCIONES==========================================================================*/

//----------------------------------------------------------funciones de persitencia------------------------------------------------

function consultarViaMovhos( $conex, $wbasedato, $viaCM ){

	$val = "";

	//Consulto el codigo correspondiente en CM
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000040
			WHERE
				viavcm = '$viaCM'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 'Viacod' ];
	}
	
	return $val;
}

/**
 * Actualiza la fraccion del articulo
 * 
 * @param $articulo				Codigo del articulo
 * @param $fraccion				Fraccion del articulo a actualizar
 * @param $unidad				Unidad de la fraccion
 * @param $tiempoVencimiento	Tiempo de vencimiento
 * @param $cco					Centro de costos
 * @return unknown_type
 */
function actualizarFraccionArticulo( $articulo, $fraccion, $unidad, $tiempoVencimiento, $cco ){
	
	global $conex;
	global $wbasedato;
	global $bd;
	
	$sql = "UPDATE
				{$bd}_000059
			SET
				Deffra = '".round($fraccion,3)."',
				Deffru = '$unidad',
				Defdie = '$tiempoVencimiento'
			WHERE
				Defart = '$articulo'
				AND defcco = '$cco'
			";
				
	$res = mysql_query( $sql, $conex ) or die( msyql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}

/**
 * Hallo el codigo del centro de costos de central de Mezclas
 * @return unknown_type
 */
function centroCostos(){
	
	global $conex;
	global $wbasedato;
	
	$sql = "SELECT
				Ccocod
			FROM
				movhos_000011
			WHERE
				ccotra = 'on'
				AND ccoima = 'on'
				AND ccoest = 'on'
			";
	
	$res= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return $rows[ 'Ccocod' ];
	}
}


/**
 * Para productos que no son Nutriciones, se suma todos los insumos con la misma presentación, para calcular la Fraccion del articulo
 * @param $unidad		Unidad
 * @param $inslis		Lista de insumos
 * @return unknown_type
 */
function consultarFraccionProducto( $unidad, $inslis ){
	
	$cantidad = 0;

	// for( $i = 0; $i < count($inslis); $i++ )
	foreach ($inslis as $i => $value)
	{
		if( $inslis[$i]['pre'] == $unidad ){
			$cantidad += $inslis[$i]['can'];
		}
	}

	return $cantidad;
}

/**
 * Consulta la Unidad del articulo(MG, ML, G, L, etc) del producto, este es el insumo que mas cantidad tenga
 * 
 * @param $inslis
 * @return unknown_type
 */
function consultarUnidadInsumoMaximo( $inslis ){
	
	$unidad = '';
	$max = 0;
	
	// for( $i = 0; $i < count($inslis); $i++ )
	foreach ($inslis as $i => $value)
	{
		
		if( $inslis[$i]['can'] > $max ){
			$unidad = $inslis[$i]['pre'];
			$max = $inslis[$i]['can'];  
		}
	}

	return $unidad;
}

/**
 * Registra la fraccion del articulo creado en la tabla 59 de movhos
 * @return unknown_type
 * 
 * @param $articulo
 * @param $fraccion
 * @param $unidad
 * @return unknown_type
 */

/**
 * 
 * @param $articulo				Codigo del aritculo
 * @param $fraccion				Fraccion del articulo
 * @param $unidad				Unidad del aritculo
 * @param $tiempoVencimiento	Tiempo de vencimiento
 * @param $via					Via de aplicación o administración
 * @param $cco					Centro de costos
 * @return unknown_type
 */
function registrarFraccion( $articulo, $fraccion, $unidad, $tiempoVencimiento, $via, $cco ){
	
	global $conex;
	global $wbasedato;
	global $wusuario;
	global $bd;
	
	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );
	
	$sql = "INSERT INTO {$bd}_000059( Medico, fecha_data, hora_data, Defcco ,   Defart   ,  Deffra  ,  Deffru  , Defest , Defven ,       Defdie        , Defdis, Defdup , Defcon, Defnka, Defdim , Defdom , Defvia,   Seguridad  )
							  VALUES( '$bd' , '$fecha'  , '$hora'  , '$cco' , '$articulo', '".round($fraccion,3)."', '$unidad',  'on'  , 'on'   , '$tiempoVencimiento',  'on' ,  'on'  ,  'on' ,   ''  ,   ''   ,    ''  , '$via', 'C-$wusuario' )
			";
			    			
	$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;	
	}
}


/**
 * Consulta el volumen de los insumos utilizados o a utilizar en la creación de productos a los cuales no se le va 
 * a aplicar purga.
 * 
 * @param	$inslis			Lista de insumos
 * @param	$voltot			Volumen total de la mezcla
 * 
 * @return	$vol			Volumen total al que no se le va a aplicar purga
 */
function consultarVolNoPurgar( $inslis, $voltot, $peso ){
	
	$vol = 0;
	
	if( $peso <= 0 ){
		$peso = 1;
	}
		
	if( $voltot > 0 ){
		// for( $i = 0; $i < count($inslis); $i++ )
		foreach ($inslis as $i => $value)
		{
			//es afectado por el peso
			if( $inslis[$i]['ppe'] != 'on' ){
				$pesoaux = 1;
			}
			else{
				$pesoaux = $peso;
			}
			if( $inslis[$i]['app'] == 'on' && $inslis[$i]['mat'] != 'on' ){
				// $vol = $vol + ($inslis[$i]['can']*$pesoaux*$inslis[$i]['fac']);
				$vol = $vol + ((float)$inslis[$i]['can']*$pesoaux*(float)$inslis[$i]['fac']);
			}
		}
		$vol = $voltot - $vol;
	}
	

	return $vol;
}

/**
 * Consulta los tipos de productos existentes en el maestro
 *
 * recibe:
 * 		$tipo: tipo de articulo que ya fue seleccionado
 * retorna:
 * 		$tipos: tipo de articulos para armar el select
 */
function consultarTipos($tipo)
{
	global $conex;
	global $wbasedato;

	if ($tipo!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$tipos[0]=$tipo;
		$cadena="Tipcod != mid('".$tipo."',1,instr('".$tipo."','-')-1) AND";
		$inicio=1;
	}
	else
	{
		$tipos[0]='';
		$cadena='';
		$inicio=0;
	}

	//consulto los conceptos
	$q =  " SELECT Tipcod, Tipdes, Tipcdo "
	."        FROM ".$wbasedato."_000001 "
	."      WHERE ".$cadena." "
	."            Tippro='on' "
	."            and Tipest='on' ";
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			if ($row1['Tipcdo']=='on')
			{
				$row1['Tipcdo']='CODIFICADO';
			}
			else
			{
				$row1['Tipcdo']='NO CODIFICADO';
			}
			$tipos[$inicio]=$row1['Tipcod'].'-'.$row1['Tipdes'].'-'.$row1['Tipcdo'];
			$inicio++;
		}

	}
	else
	{
		$tipos= false;
	}

	return $tipos;
}


/**
 * Consulta las instituciones a las que se le distribuyen nutriciones
 *
 * recibe:
 * 		$insti:institucion que que ya fue seleccionada en el select
 * retorna:
 * 		$instituciones: lista de instituciones para armar el select
 */
function consultarInstituciones($insti)
{
	global $conex;
	global $wbasedato;

	if ($insti!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$exp=explode('-', $insti);
		$instituciones[0]['cod']=$exp[0];
		$instituciones[0]['nom']=$exp[1];
		$cadena="Subcodigo != '".$exp[0]."' AND";
		$inicio=1;
	}
	else
	{
		$cadena='';
		$inicio=0;
	}

	//consulto los conceptos
	$q =  " SELECT Subcodigo, Descripcion "
	."        FROM det_selecciones "
	."      WHERE ".$cadena." "
	."        Medico = '".$wbasedato."' "
	."        AND Codigo = '04' "
	."        AND Activo = 'A' ";
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);

	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$instituciones[$inicio]['cod']=$row1['Subcodigo'];
			$instituciones[$inicio]['nom']=$row1['Descripcion'];
			$inicio++;
		}

	}
	else if ($inicio==0)
	{
		$instituciones= false;
	}

	return $instituciones;
}

/**
 * segun un parametro de busqueda y dato ingresados por el usuario, se busca un producto por insumo
 *
 * @param caracter $parbus  dato de busqueda 1
 * @param caracter_type $parbus2 dato de busqueda 2
 * @param caracter_type $forbus parametro de busqueda del insumo, segun este se hace el query
 * @return vector de productos encontrados, en caso de no encontar, retorna false
 * 
 * $productos[$i]['cod']= codigo del producto
 * $productos[$i]['nom']= nombre comenrcial del producto
 * $productos[$i]['gen']= nombre generico del producto
 * $productos[$i]['pre']= unidad de trabajo del producto
 */
function consultarPorductosXInsumo($parbus, $parbus2, $forbus)
{
	global $conex;
	global $wbasedato;

	if ($parbus2=='')
	{
		switch ($forbus)
		{
			case 'Codigo':
			$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
			."       FROM ".$wbasedato."_000002, ".$wbasedato."_000003, farstore_000002 "
			."    WHERE Artest = 'on' "
			."       AND Pdeins like '%".$parbus."%' "
			."       AND Pdeest= 'on'  "
			."       AND Pdepro=Artcod "
			."       AND Artuni= Unicod "
			."    Order by 1 ";

			break;
			case 'Nombre comercial':

			$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
			."       FROM ".$wbasedato."_000002, ".$wbasedato."_000003, farstore_000002 "
			."    WHERE Artest = 'on' "
			."       AND Pdeins in ( SELECT Artcod FROM ".$wbasedato."_000002 WHERE Artcom LIKE '%".$parbus."%' and artest='on' )"
			."       AND Pdeest= 'on'  "
			."       AND Pdepro=Artcod "
			."       AND Artuni= Unicod "
			."    Order by 1 ";

			break;
			case 'Nombre genérico':

			$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
			."       FROM ".$wbasedato."_000002, ".$wbasedato."_000003, farstore_000002 "
			."    WHERE Artest = 'on' "
			."       AND Pdeins in ( SELECT Artcod FROM ".$wbasedato."_000002 WHERE Artgen LIKE '%".$parbus."%' and artest='on' )"
			."       AND Pdeest= 'on'  "
			."       AND Pdepro=Artcod "
			."       AND Artuni= Unicod "
			."    Order by 1 ";

			break;
		}
	}
	else
	{
		switch ($forbus)
		{
			case 'Codigo':
			$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
			."       FROM ".$wbasedato."_000002, ".$wbasedato."_000003, farstore_000002 "
			."    WHERE Artest = 'on' "
			."       AND Pdeins like '%".$parbus."%' "
			."       AND Pdecan ='".$parbus2."' "
			."       AND Pdeest= 'on'  "
			."       AND Pdepro=Artcod "
			."       AND Artuni= Unicod "
			."    Order by 1 ";

			break;
			case 'Nombre comercial':

			$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
			."       FROM ".$wbasedato."_000002, ".$wbasedato."_000003, farstore_000002 "
			."    WHERE Artest = 'on' "
			."       AND Pdeins in ( SELECT Artcod FROM ".$wbasedato."_000002 WHERE Artcom LIKE '%".$parbus."%' and artest='on' )"
			."       AND Pdecan ='".$parbus2."' "
			."       AND Pdeest= 'on'  "
			."       AND Pdepro=Artcod "
			."       AND Artuni= Unicod "
			."    Order by 1 ";

			break;
			case 'Nombre genérico':

			$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
			."       FROM ".$wbasedato."_000002, ".$wbasedato."_000003, farstore_000002 "
			."    WHERE Artest = 'on' "
			."       AND Pdeins in ( SELECT Artcod FROM ".$wbasedato."_000002 WHERE Artgen LIKE '%".$parbus."%' and artest='on' )"
			."       AND Pdecan ='".$parbus2."' "
			."       AND Pdeest= 'on'  "
			."       AND Pdepro=Artcod "
			."       AND Artuni= Unicod "
			."    Order by 1 ";

			break;
		}
	}
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$productos[$i]['cod']=$row[0];
			$productos[$i]['nom']=str_replace('-',' ',$row[1]);
			$productos[$i]['gen']=str_replace('-',' ',$row[2]);
			$productos[$i]['pre']=$row[3].'-'.$row[4];
		}
	}
	else
	{
		$productos=false;
	}
	return $productos;
}

/**
 *segun un parametro de busqueda y dato ingresados por el usuario, se busca un producto determinado en la central
 *
 * @param caracter_type $parbus dato de busqueda 1
 * @param caracter_type $forbus parametro de busqueda del producto segun este se hace el query
 * @return vector de productos encontrados, en caso de no encontar, retorna false
 * 
 * $productos[$i]['cod']= codigo del producto
 * $productos[$i]['nom']= nombre comenrcial del producto
 * $productos[$i]['gen']= nombre generico del producto
 * $productos[$i]['pre']= unidad de trabajo del producto
 */
function consultarCentral($parbus, $forbus)
{
	global $conex;
	global $wbasedato;

	switch ($forbus)
	{
		case 'Codigo':
		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, '' "
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, farstore_000002 "
		."    WHERE Artcod like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Tippro = 'on' "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;
		case 'Nombre comercial':

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, '' "
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, farstore_000002 "
		."    WHERE Artcom like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Tippro = 'on' "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;
		case 'Nombre genérico':

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, '' "
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, farstore_000002 "
		."    WHERE Artgen like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Tippro = 'on' "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;

		case 'Paciente':

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, '' "
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, farstore_000002 "
		."    WHERE Arthis like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Tippro = 'on' "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;


		case 'Institucion':

		$fecha1=mktime(0, 0, 0, date('m'), date('d'), date('Y')) - (1 * 24 * 60 * 60);
		$fecha1= date('Y-m-d', $fecha1);

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Pacnom "
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000020, farstore_000002 "
		."    WHERE Artins = '".$parbus."' "
		."       AND Artest = 'on' "
		."       AND Artfec between  '".$fecha1."' and '".date('Y-m-d')."' "
		."       AND Pacpro = Artcod "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;
	}

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$productos[$i]['cod']=$row[0];
			$productos[$i]['nom']=str_replace('-',' ',$row[1]);
			$productos[$i]['gen']=str_replace('-',' ',$row[2]);
			$productos[$i]['pre']=$row[3].'-'.$row[4];
			$productos[$i]['pac']=$row[5];
		}
	}
	else
	{
		$productos=false;
	}
	return $productos;
}

/**
 *segun un parametro de busqueda y dato ingresados por el usuario, se busca un producto determinado en el maestro de articulos de Unix
 *
 * @param caracter_type $parbus dato de busqueda 1
 * @param caracter_type $forbus parametro de busqueda del producto segun este se hace el query
 * @return vector de productos encontrados, en caso de no encontar, retorna false
 * 
 * $productos[$i]['cod']= codigo del producto
 * $productos[$i]['nom']= nombre comenrcial del producto
 * $productos[$i]['gen']= nombre generico del producto
 * $productos[$i]['pre']= unidad de trabajo del producto
 */
function consultarProductos($parbus, $forbus)
{
	global $conex;
	global $wbasedato;

	switch ($forbus)
	{
		case 'Codigo':
		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
		."       FROM movhos_000026, farstore_000002 "
		."    WHERE Artcod like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;
		case 'Nombre comercial':

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
		."       FROM movhos_000026, farstore_000002 "
		."    WHERE Artcom like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;
		case 'Nombre genérico':

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
		."       FROM movhos_000026, farstore_000002 "
		."    WHERE Artgen like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;
	}
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$productos[$i]['cod']=$row[0];
			$productos[$i]['nom']=str_replace('-',' ',$row[1]);
			$productos[$i]['gen']=str_replace('-',' ',$row[2]);
			$productos[$i]['pre']=$row[3].'-'.$row[4];
		}
	}
	else
	{
		$productos=false;
	}
	return $productos;
}

/**
 *segun un parametro de busqueda y dato ingresados por el usuario, se busca un insumo determinado en la central
 *
 * @param caracter_type $parbus dato de busqueda 1
 * @param caracter_type $forbus parametro de busqueda del producto segun este se hace el query
 * @return vector de productos encontrados, en caso de no encontar, retorna false
 * 
 * $productos[$i]['cod']= codigo del insumo
 * $productos[$i]['nom']= nombre comenrcial del insumo
 * $productos[$i]['gen']= nombre generico del insumo
 * $productos[$i]['pre']= unidad de trabajo del insumo
 */
function consultarInsumos($parbus, $forbus)
{
	global $conex;
	global $wbasedato;

	switch ($forbus)
	{
		case 'Codigo':
		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, farstore_000002 "
		."    WHERE Artcod like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Artuni= Unicod "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Tippro <> 'on' "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;
		case 'Nombre comercial':

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
		."       FROM ".$wbasedato."_000002,  ".$wbasedato."_000001, farstore_000002 "
		."    WHERE Artcom like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Tippro <> 'on' "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;
		case 'Nombre generico':

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
		."       FROM ".$wbasedato."_000002,  ".$wbasedato."_000001, farstore_000002 "
		."    WHERE Artgen like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Tippro <> 'on' "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;
	}

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$productos[$i]['cod']=$row[0];
			$productos[$i]['nom']=str_replace('-',' ',$row[1]);
			$productos[$i]['gen']=str_replace('-',' ',$row[2]);
			$productos[$i]['pre']=$row[3].'-'.$row[4];
		}
	}
	else
	{
		$productos=false;
	}
	return $productos;
}

//nueva funcion de modificacion de 2007-07-12 para cargar los insumos tipicos de una nutricion
function consultarNutriciones($tipo, $volumen)
{
	global $conex;
	global $wbasedato;
	global $purga;

	$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tipppe, Tipmat, Artord "
	."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, farstore_000002 "
	."    WHERE tipinu = 'on' "
	."       AND Tipmat <> 'on' "
	."       AND Tipmmq <> 'on' "
	."       AND Artest = 'on' "
	."       AND Artuni= Unicod "
	."       AND Tipest = 'on' "
	."       AND Tipcod = Arttip "
	."       AND Uniest='on' "
	."    Order by 8 ";
	
	$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tipppe, Tipmat, Artord, Artapp "
	."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, farstore_000002 "
	."    WHERE tipinu = 'on' "
	."       AND Tipmat <> 'on' "
	."       AND Tipmmq <> 'on' "
	."       AND Artest = 'on' "
	."       AND Artuni= Unicod "
	."       AND Tipest = 'on' "
	."       AND Tipcod = Arttip "
	."       AND Uniest='on' "
	."    Order by 8 ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$inslis[$i]['cod']=$row[0];
			$inslis[$i]['nom']=str_replace('-',' ',$row[1]);
			$inslis[$i]['gen']=str_replace('-',' ',$row[2]);
			$inslis[$i]['pre']=$row[3].'-'.$row[4];
			$inslis[$i]['can']='';
			$inslis[$i]['pri']='checked';
			$inslis[$i]['ppe']=$row[5];
			$inslis[$i]['mat']=$row[6];
			
			$inslis[$i]['app']=$row[8];
		}


		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tipppe, Tipmat, Artord "
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, farstore_000002 "
		."    WHERE tipinu = 'on' "
		."       AND Tipmat <> 'on' "
		."       AND Tipmmq = 'on' "
		."       AND Artest = 'on' "
		."       AND Artuni= Unicod "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Uniest='on' ";
		
		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tipppe, Tipmat, Artord, Artapp "
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, farstore_000002 "
		."    WHERE tipinu = 'on' "
		."       AND Tipmat <> 'on' "
		."       AND Tipmmq = 'on' "
		."       AND Artest = 'on' "
		."       AND Artuni= Unicod "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Uniest='on' ";

		$res = mysql_query($q,$conex);
		$num2 = mysql_num_rows($res);

		if ($num2>0)
		{
			$row = mysql_fetch_array($res);

			$inslis[$num]['cod']=$row[0];
			$inslis[$num]['nom']=str_replace('-',' ',$row[1]);
			$inslis[$num]['gen']=str_replace('-',' ',$row[2]);
			$inslis[$num]['pre']=$row[3].'-'.$row[4];
			$inslis[$num]['can']='';
			$inslis[$num]['pri']='checked';
			$inslis[$num]['ppe']=$row[5];
			$inslis[$num]['mat']=$row[6];
			
			$inslis[$num]['app']=$row[8];
			$num=$num+1;
		}


		if($volumen=='')
		{
			$volumen=0;
		}

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tipppe, Tipmat, Artord, Tipvdi, Artapp, Artmin, Artmax "
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, farstore_000002 "
		."    WHERE tipinu = 'on' "
		."       AND Tipmat = 'on' "
		."       AND Artest = 'on' "
		."       AND Artuni= Unicod "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Uniest='on' "
		."    Order by 8 ";

		$res = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res);

		if ($num1>0)
		{
			for ($i=$num;$i<$num+$num1;$i++)
			{
				$row = mysql_fetch_array($res);

				$inslis[$i]['cod']=$row[0];
				$inslis[$i]['nom']=str_replace('-',' ',$row[1]);
				$inslis[$i]['gen']=str_replace('-',' ',$row[2]);
				$inslis[$i]['pre']=$row[3].'-'.$row[4];

				if ($row[8]=='on')
				{
					// if( $row[10] > 0 && $row[11] > 0 && $volumen+@$purga >= $row[10] and $volumen+@$purga <= $row[11] and $row[7]!=0 ){
					if( (float)$row[10] > 0 && (float)$row[11] > 0 && (float)$volumen+(float)@$purga >= (float)$row[10] and (float)$volumen+(float)@$purga <= (float)$row[11] and (float)$row[7]!=0 ){
						$inslis[$i]['can']=1;
					}
					else{
						$inslis[$i]['can']='';
					}
				}
				else
				{
					$inslis[$i]['can']='';
				}
				$inslis[$i]['pri']='checked';
				$inslis[$i]['ppe']=$row[5];
				$inslis[$i]['mat']=$row[6];
				$inslis[$i]['app']=$row[9];
			}
		}
	}
	else
	{
		$inslis=false;
	}
	return $inslis;

}

function consultarMas(&$inslis)
{
	global $conex;
	global $wbasedato;

	$q= " SELECT Tipppe, Tipmat "
	."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001 "
	."    WHERE Artcod = '".$inslis['cod']."' "
	."       AND Artest = 'on' "
	."       AND Tipest = 'on' "
	."       AND Tipcod = Arttip ";

	$res = mysql_query($q,$conex);
	$row = mysql_fetch_array($res);

	$inslis['ppe']=$row[0];
	$inslis['mat']=$row[1];

	if ($inslis['ppe']=='on')
	{
		$q= " SELECT Tipppe, Tipmat "
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, ".$wbasedato."_000015 "
		."    WHERE Artcod = '".$inslis['cod']."' "
		."       AND Artest = 'on' "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Facest = 'on' "
		."       AND Facart = Artcod ";

		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);

		$inslis['fac']=$row[1];
	}
	else
	{
		$inslis['fac']=1;
	}
}

function consultarFactor($codigo, $peso, $tiempoInfusion)
{
	global $conex;
	global $wbasedato;
	global $bd;

	$q = "SELECT Facval, Facdes, Facpes,Facreq,Reqmti 
			FROM ".$wbasedato."_000015,".$bd."_000212 
		   WHERE Facart = '".$codigo."' 
		     AND Facest = 'on' 
			 AND Facreq = Reqcod 
		ORDER BY Faccod";
	
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	
	if ($num>0)
	{
		$row = mysql_fetch_array($res);
		if( $row[2]=='on')
		{
			if($row['Reqmti']=='on')
			{
				return $row[0]*$tiempoInfusion;
			}
			else
			{
				return $row[0];
			}
		}
		else
		{
			
			if($row['Reqmti']=='on')
			{
				return ($row[0]/$peso)*$tiempoInfusion;
			}
			else
			{
				return ($row[0]/$peso);
			}
		}
	}
	else
	{
		return false;
	}
}

/**
 * Se consulta si un producto determinado a crear, ya existe en el maestro de la central por su codigo
 *
 * @param caracter_type $codigo
 * @return boolean, falso o verdadero
 */
function consultarExistencia($codigo)
{
	global $conex;
	global $wbasedato;

	$q= " SELECT * "
	."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001 "
	."    WHERE Artcod = '".$codigo."' "
	."       AND Artest = 'on' "
	."       AND Tipest = 'on' "
	."       AND Tipcod = Arttip "
	."       AND Tippro = 'on' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * Se consulta si un producto determinado a crear, ya existe en el maestro de la central por su codigo
 *
 * @param caracter_type $codigo
 * @return boolean, falso o verdadero
 */
function consultarAgua($codigo)
{
	global $conex;
	global $wbasedato;

	$q= " SELECT * "
	."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001 "
	."    WHERE Artcod = '".$codigo."' "
	."       AND Artest = 'on' "
	."       AND Tipest = 'on' "
	."       AND Tipcod = Arttip "
	."       AND Tipcod = Arttip "
	."       AND Tipmmq = 'on' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * Se consultan todos los datos de un producto mediante su codio
 *
 * @param caracter_type $codigo, codigo del producto
 * 
 * Retorna:
 * @param caracter_type $via, via de administracion del producto
 * @param int_type $tfd, tiempo en dias de infusion
 * @param int_type $tfh, tiempo en horas de infusion
 * @param int_type $tvd, tiempo en dias de vencimiento
 * @param int_type $tvh, tiempo en horas de vencimiento
 * @param date_type $fecha, fecha de creacion del articulo
 * @param array_type $inslis, vector con lo sinsumos del producto
 * 
 * 	$inslis[$i]['cod'] codigo del insumo
 *  $inslis[$i]['nom'] nombre comercial del insumo
 *  $inslis[$i]['gen'] nombre comercial del insumo
 *  $inslis[$i]['pre'] unidad de trabajo del insumo
 *  $inslis[$i]['can'] cantidad del insumo
 *  $inslis[$i]['pri'] se inicializa en vacio y sera un checkbox para indicar que hace parte del nombre del producto
 * @param boolean_type $tippro, tipo de producto y si es codificado o no
 * @param boolean_type $estado, estado del producto (Creado o desactivado)
 * @param boolean_type $foto, si el producto es sensible a la luz
 * @param boolean_type $neve, si el producto requiere conservacion inmediata en nevera
 */
function consultarProducto($codigo, &$via, &$tfd, &$tfh, &$tvd, &$tvh, &$fecha, &$inslis, &$tippro, &$estado, &$foto, &$neve, &$des, &$peso, &$purga, &$historia, &$volumen, &$insti)
{
	global $conex;
	global $wbasedato;
	
	$nuncaapp = true;

	$q= " SELECT Artvia, Arttin, Arttve, Artfec, Arttip, Artest, Artfot, Artnev, Tipdes, Tipcdo, Artdes, Artpes, Artpur, Arthis, Tipnco, Artins "
	."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001 "
	."    WHERE Artcod = '".$codigo."' "
	."       AND Artest = 'on' "
	."       AND Arttip= Tipcod "
	."    Order by 1 ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		$row = mysql_fetch_array($res);
		$via=$row['Artvia'];
		$des=$row['Artdes'];
		$tfd=floor($row['Arttin']/60);
		$tfh=$row['Arttin']%60;
		$tvd=floor($row['Arttve']/24);
		$tvh=$row['Arttve']%24;
		$fecha=$row['Artfec'];
		$foto=$row['Artfot'];
		$neve=$row['Artnev'];
		$peso=$row['Artpes'];
		$purga=$row['Artpur'];
		$historia=$row['Arthis'];

		if ($row['Tipcdo']=='on')
		{
			$row['Tipcdo']='CODIFICADO';
		}
		else
		{
			$row['Tipcdo']='NO CODIFICADO';
		}

		$tippro=$row['Arttip'].'-'.$row['Tipdes'].'-'.$row['Tipcdo'];
		if ($row['Artest']=='on')
		{
			$estado='Creado';
		}
		else
		{
			$estado='Desactivado';
		}

		if($row['Artins']!='')
		{
			$q =  " SELECT Subcodigo, Descripcion "
			."        FROM det_selecciones "
			."      WHERE Subcodigo='".$row['Artins']."' "
			."        AND Medico = '".$wbasedato."' "
			."        AND Codigo = '04' "
			."        AND Activo = 'A' ";
			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1>0)
			{
				$row1 = mysql_fetch_array($res1);
				$insti=$row1[0].'-'.$row1[1];
			}
			else
			{
				$insti='';
			}


		}
		else
		{
			$insti='';
		}
	}

	$q= " SELECT Pdeins, Pdecan, Artcom, Artgen, Artuni, Unides, Pdefac, Tipppe, Tipmat, Artord, Pdeapp, Pdeant "
	."       FROM ".$wbasedato."_000003, ".$wbasedato."_000002, farstore_000002, ".$wbasedato."_000001 "
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeest = 'on' "
	."       AND Pdeins= Artcod "
	."       AND Artuni= Unicod "
	."       AND Uniest='on' "
	."       AND Arttip=Tipcod "
	."       AND Tipest='on' "
	."    Order by 10, 3 ";
	
	$q= " SELECT Pdeins, Pdecan, Artcom, Artgen, Artuni, Unides, Pdefac, Tipppe, Tipmat, Artord, Pdeapp "
	."       FROM ".$wbasedato."_000003, ".$wbasedato."_000002, farstore_000002, ".$wbasedato."_000001 "
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeest = 'on' "
	."       AND Pdeins= Artcod "
	."       AND Artuni= Unicod "
	."       AND Uniest='on' "
	."       AND Arttip=Tipcod "
	."       AND Tipest='on' "
	."    Order by 10, 3 ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	
	$volnp = 0;

	if ($num>0)
	{
		$vol=0;
		for ($i=0;$i<$num;$i++)
		{
			$row2 = mysql_fetch_array($res);
			$inslis[$i]['cod']=$row2[0];
			$inslis[$i]['nom']=str_replace('-',' ',$row2[2]);
			$inslis[$i]['gen']=str_replace('-',' ',$row2[3]);
			$inslis[$i]['can']=$row2[1];
			$inslis[$i]['mat']=$row2[8];
			
			//Calculo de volumen total
			if($inslis[$i]['mat']!='on')
			{
				$vol=$vol+$inslis[$i]['can'];
			}
			
			$inslis[$i]['pre']=$row2[4].'-'.$row2[5];
			$inslis[$i]['fac']=$row2[6];
			$inslis[$i]['ppe']=$row2[7];
			
			$inslis[$i]['app']=$row2['Pdeapp'];
//			$inslis[$i]['app']=$row2['Pdeant'];
			
			if( $row2['Pdeapp'] == '' ){
				$inslis[$i]['ant']='off';
			}
			else{
				$inslis[$i]['ant']='on';
				$nuncaapp = false;
			}
			
			//Calculo de volume no purgado
			if($inslis[$i]['mat'] != 'on' && $inslis[$i]['app'] == 'on')
			{
				$volnp=$volnp+$inslis[$i]['can'];
			}
			
			//$inslis[$i]['pri']='';
		}
		
		$volnp = $vol-$volnp;
		
		for ($i=0;$i<$num;$i++)
		{
			if ($row['Tipcdo']=='NO CODIFICADO' and $row['Tipnco']=='off')
			{
				if( $row2['Pdeapp'] == '' && $nuncaapp == true ){
					if($peso==0 or $peso=='' or $inslis[$i]['ppe']!='on')
					{
						if($inslis[$i]['mat']!='on')
						{
							$inslis[$i]['can']=round($inslis[$i]['can']-$inslis[$i]['can']*$purga/$vol,2);
						}
					}
					else
					{
						$inslis[$i]['can']=round(($inslis[$i]['can']-($inslis[$i]['can']*$purga/$vol))/($peso*(float)$inslis[$i]['fac']),2);
					}
				}
				else{
					if($peso==0 or $peso=='' or $inslis[$i]['ppe']!='on')
					{	
						if( $inslis[$i]['mat']!='on' && $inslis[$i]['app'] == 'on' )
						{
							$inslis[$i]['can']=round((float)$inslis[$i]['can']-(float)$inslis[$i]['can']*(float)$purga/($vol-$volnp),2);
						}
					}
					else
					{
						if( $inslis[$i]['app'] == 'on')
						{
							if( $inslis[$i]['fac'] > 0 ){
								$inslis[$i]['can']=round(($inslis[$i]['can']-($inslis[$i]['can']*$purga/($vol-$volnp)))/($peso*(float)$inslis[$i]['fac']),2);
							}
							else{
								$inslis[$i]['can']=0;
							}
						}
						else
 							$inslis[$i]['can']=round(($inslis[$i]['can'])/($peso*(float)$inslis[$i]['fac']),2);
					}
				}
			}
		}
		$volumen=$vol-(float)$purga;
	}
}

/**
 * Consulta los tipos de unidades existentes en el maestro, para que se escoja la del producto
 *
 * @param caracter_type $presentacion, si ya se ha seleccionado alguna, que salga de primera en dorp down
 * @param caracter_type $tipo, tipo del producto, para buscar la unidad por defecto en el mestro de tipos de articulo
 * @return array_type $presentaciones, vector con las diferentes unidades de medida
 */
function consultarPresentaciones($presentacion, $tipo)
{
	global $conex;
	global $wbasedato;

	if ($presentacion!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$presentaciones[0]=$presentacion;
		$cadena="Unicod != mid('".$presentacion."',1,instr('".$presentacion."','-')-1) AND";
		$inicio=1;
	}
	else
	{
		$q =  " SELECT Tipuni, Unides "
		."        FROM ".$wbasedato."_000001, farstore_000002 "
		."      WHERE Tipcod= mid('".$tipo."',1,instr('".$tipo."','-')-1) "
		."            AND Tipest='on' "
		."            AND Tipuni=Unicod "
		."            AND Uniest='on' ";
		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		$presentaciones[0]=$row1[0].'-'.$row1[1];
		$cadena="Unicod !='".$row1[0]."' AND " ;
		$inicio=1;
	}

	$q =  " SELECT Unicod, Unides "
	."        FROM farstore_000002 "
	."      WHERE ".$cadena." "
	."            Uniest='on' ";
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$presentaciones[$inicio]=$row1['Unicod'].'-'.$row1['Unides'];
			$inicio++;
		}

	}
	else
	{
		$presentaciones= false;
	}

	return $presentaciones;
}

/**
 * Consulta de det_selecciones los tipos de vias de administracion existentes
 *
 * @param caracter_type $via, por si ya se ha seleccionado alguna via, que salga de primera en el drop down
 * @return array_type, vias vector con las diferentes vias de adminsitraicon para desplegar en drop down
 */
function consultarVias($via)
{
	global $conex;
	global $wbasedato;
	global $bd;

	if ($via!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$vias[0]=$via;
		// $cadena="Codigo != mid('".$via."',1,instr('".$via."','-')-1) AND";
		list($auxVia) = explode( "-", $via );
		$cadena="viavcm != '".$auxVia."' AND";
		$inicio=1;
	}
	else
	{
		$vias[0]='';
		$cadena='';
		$inicio=1;
	}

	//consulto los conceptos
	// $q =  " SELECT Subcodigo, Descripcion "
	// ."        FROM det_selecciones "
	// ."      WHERE ".$cadena." "
	// ."        Medico = '".$wbasedato."' "
	// ."        AND Codigo = '01' "
	// ."        AND Activo = 'A' ";
	
	$q =  " SELECT
				Viavcm as Subcodigo, Viades as Descripcion
			FROM
				{$bd}_000040
			WHERE
				$cadena
				TRIM(viavcm) != ''
			";
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);

	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$vias[$inicio]=$row1['Subcodigo'].'-'.$row1['Descripcion'];
			$inicio++;
		}

	}
	else if ($inicio==0)
	{
		$vias= false;
	}

	return $vias;
}


/**
 * Consulta de det_selecciones los tipos de descripcion para los productos
 *
 * @param caracter_type $via, por si ya se ha seleccionado alguna descripcion, que salga de primera en el drop down
 * @return array_type, vias vector con las diferentes descripciones para desplegar en drop down
 */
function consultarDes($via)
{
	global $conex;
	global $wbasedato;

	if ($via!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$vias[0]=$via;
		$cadena="Codigo != mid('".$via."',1,instr('".$via."','-')-1) AND";
		$inicio=1;
	}
	else
	{
		$vias[0]='';
		$cadena='';
		$inicio=1;
	}

	//consulto los conceptos
	$q =  " SELECT Subcodigo, Descripcion "
	."        FROM det_selecciones "
	."      WHERE ".$cadena." "
	."        Medico = '".$wbasedato."' "
	."        AND Codigo = '02' "
	."        AND Activo = 'A' ";
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);

	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$vias[$inicio]=$row1['Subcodigo'].'-'.$row1['Descripcion'];
			$inicio++;
		}

	}
	else if ($inicio==0)
	{
		$vias= false;
	}

	return $vias;
}

/**
 * Para un tipo de producto seleccionado, se consulta sus caracteristicas, para que el programa realice diferentes actividades
 *
 * @param caracter_type $tipo, tipo de producto
 * @return caracter_type $clase, variable concatenada con el distintivo del tipo, el consecutivo y si es codificado o no
 */
function consultarClase($tipo)
{
	global $conex;
	global $wbasedato;

	//consulto los conceptos
	$q =  " SELECT Tipdis, Tipcon, Tipnco "
	."        FROM ".$wbasedato."_000001 "
	."      WHERE Tipcod= mid('".$tipo."',1,instr('".$tipo."','-')-1) "
	."            and Tipest='on' ";
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		$row1 = mysql_fetch_array($res1);
		$clase=$row1['Tipdis'].'-'.($row1['Tipcon']+1).'-'.$row1['Tipnco'];
	}
	else
	{
		$clase= false;
	}

	return $clase;
}

/**
 * Busca si existen lotes para el producto
 *
 * @param caracter $codigo, codigo del producto
 * @return boolean  si existen lotes retorna false
 */
function consultarPermiso($codigo)
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT * "
	."        FROM ".$wbasedato."_000004 "
	."      WHERE plopro='".$codigo."' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);

	if ($num1>0)
	{
		return false;
	}
	else
	{
		return true;
	}
}

/**
 * Incrementar el consecutivo para el tipo de producto y generar el codigo del producto
 *
 * @param unknown_type $tipo, tipo de producto
 * @param unknown_type $codigo, codigo del producto
 * @return caracter_type, codigo del producto
 */
function incrementarConsecutivo($tipo, $codigo)
{
	global $conex;
	global $wbasedato;

	//consulto los conceptos
	$q =  " SELECT Tipdis, Tipcdo "
	."        FROM ".$wbasedato."_000001 "
	."      WHERE Tipcod= '".$tipo."' "
	."            and Tipest='on' ";
	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);


	if ($row1['Tipcdo']!='on')
	{
		$q = "lock table ".$wbasedato."_000001 LOW_PRIORITY WRITE";
		$errlock = mysql_query($q,$conex);

		$q= "   UPDATE ".$wbasedato."_000001 "
		."      SET Tipcon = Tipcon + 1 "
		."    WHERE Tipcod = '".$tipo."'"
		."      AND Tipest = 'on' ";

		$res1 = mysql_query($q,$conex);

		$q= "   SELECT Tipcon from ".$wbasedato."_000001 "
		."    WHERE Tipcod = '".$tipo."'"
		."      AND Tipest = 'on' ";

		$res1 = mysql_query($q,$conex);
		$row2 = mysql_fetch_array($res1);

		$q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
		$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		$codigo=$row1['Tipdis'];
		for ($i=0; $i<(4-strlen($row2[0])); $i++)
		{
			$codigo=$codigo.'0';
		}
		$codigo=$codigo.$row2[0];
	}

	return $codigo;

}

/**
 * Busca si ya existe algun producto con la misma composicion que esta siendo ingresada en el programa
 *
 * @param unknown_type $inslis, vector con los insumos para el producto
 *                              $inslis[$i]['cod'], codigo del insumo
 *                              $inslis[$i]['can'], cantidad del insumo
 * @return false si no encuentra un producto con igual composicion
 *         codigo del prodcuto si lo encuentra
 */
// function validarComposicion($inslis)
function validarComposicion($inslis,$esDosisAdaptada)
{
	global $conex;
	global $wbasedato;

	$q= "   SELECT Artcod from ".$wbasedato."_000002 "
	."    WHERE Artest = 'on' ";

	
	$cantidadInsumos = 0;
	// for ($i=0; $i<count($inslis); $i++)
	foreach ($inslis as $i => $value)
	{
		if($esDosisAdaptada=="on")
		{
			if($inslis[$i]['can']>0 && $inslis[$i]['can']!="")
			{
				$q = $q. "AND Artcod in (SELECT Pdepro FROM ".$wbasedato."_000003 where Pdeins='".$inslis[$i]['cod']."' and Pdeest='on' and Pdecan='".$inslis[$i]['can']."' )";
				$cantidadInsumos++;
			}
		}
		else
		{
			$q = $q. "AND Artcod in (SELECT Pdepro FROM ".$wbasedato."_000003 where Pdeins='".$inslis[$i]['cod']."' and Pdeest='on' and Pdecan='".$inslis[$i]['can']."' )";
		}
		
		// $q = $q. "AND Artcod in (SELECT Pdepro FROM ".$wbasedato."_000003 where Pdeins='".$inslis[$i]['cod']."' and Pdeest='on' and Pdecan='".$inslis[$i]['can']."' )";
	}
	
	if($esDosisAdaptada!="on")
	{
		$cantidadInsumos = count( $inslis );
	}
	
	// $q = $q." AND ".count( $inslis )." IN ( SELECT COUNT(*) FROM {$wbasedato}_000003 WHERE pdepro = artcod )";
	$q = $q." AND ".$cantidadInsumos." IN ( SELECT COUNT(*) FROM {$wbasedato}_000003 WHERE pdepro = artcod )";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		$row2 = mysql_fetch_array($res1);
		return $row2[0];
	}
	else
	{
		return false;
	}
}

function validarHistoria($cco, $historia, &$ingreso, &$mensaje, &$nombre, &$habitacion)
{
	global $conex;
	global $wbasedato;

	if(is_numeric($historia))
	{
		$q = "SELECT Oriing, Pacno1, Pacno2, Pacap1, Pacap2 "
		."      FROM root_000037, root_000036 "
		."     WHERE Orihis = '".$historia."' "
		."       AND Oriori = '01' "
		."       AND Oriced = Pacced 
				 AND Oritid = Pactid";

		$err=mysql_query($q,$conex);
		$num=mysql_num_rows($err);
		if($num > 0)
		{
			$row=mysql_fetch_array($err);
			$ingreso=$row['Oriing'];
			$nombre=$row['Pacno1'].' '.$row['Pacno2'].' '.$row['Pacap1'].' '.$row['Pacap2'];

			$q = "SELECT * "
			."      FROM movhos_000018 "
			."     WHERE Ubihis = '".$historia."' "
			."       AND Ubiing = '".$ingreso."' "
			."       AND Ubialp <> 'on' "
			."       AND Ubiald <> 'on' ";

			$err1=mysql_query($q,$conex);
			$num1=mysql_num_rows($err1);
			if($num1 > 0 )
			{
				$row=mysql_fetch_array($err1);
				$habitacion=$row['Ubihac'];
				return (true);
			}
			else
			{
				$mensaje = "EL PACIENTE ESTA EN PROCESO DE ALTA";
				return(false);
			}
		}
		else
		{
			$mensaje = "EL PACIENTE NO SE ENCUENTRA ACTIVO";
			return (false);
		}
	}
	else if( !is_numeric($historia))
	{
		$mensaje = "LAS HISTORIAS CLINICAS DEBEN SER NUMERICAS";
		return (false);
	}
	return(true);
}


function consultarInstitucion($insti, &$mensaje)
{
	global $conex;
	global $wbasedato;

	$exp=explode('-',$insti);
	$q = "SELECT * "
	."      FROM root_000050 "
	."     WHERE Empcod = '".$exp[0]."' "
	."       AND Empest = 'on' ";

	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num > 0)
	{
		return (true);
	}
	else
	{
		return (false);
	}
}

/**
 * Graba el nuevo producto en db
 *
 * @param caracter_type $codigo codigo del producto
 * @param caracter_type $nom nombre comercial del producto
 * @param caracter_type $gen nombre generico del producto
 * @param caracter_type $presentacion, unidad de trabajo del producto
 * @param caracter_type $via, via de administracion del producto
 * @param int_type $tin, tiempo de infusion del prodcuto
 * @param int_type $tve, tiempo de vencimiento del producto
 * @param date_type $fecha, fecha de creacion del producto
 * @param caracter_type $tip, tipo de producto
 * @param caracter_type $usuario, usuario que crea el producto
 * @param boolean_type $foto, si es sensible a la luz
 * @param boolean_type $neve, si debe meterse inmediatamente en nevera
 */
function grabarProducto($codigo, $nom, $gen, $presentacion, $via, $tin, $tve, $fecha, $tip, $usuario, $foto, $neve, $des, $peso, $purga, $historia, $insti)
{	
	global $conex;
	global $wbasedato;
	
	if( empty($peso) || $peso == '' ){
		$peso = 0;
	}

	$exp=explode('-',$presentacion);
	$exp1=explode('-',$insti);
	$q= " INSERT INTO ".$wbasedato."_000002 (   Medico       ,   Fecha_data        ,        Hora_data           ,    Artcod    ,    Artcom ,   Artgen   ,   Artuni      ,  Artvia   ,   Arttin    ,   Arttve   ,  Artfec      ,   Arttip  ,  Artcon,   Artfot   , Artnev     , Artest, Artdes     , Artfac, Artpes     , Artpur      , Arthis         , Artins        , Seguridad) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$nom."', '".$gen."' , '".$exp[0]."' , '".$via."', '".$tin."'  , '".$tve."' ,  '".$fecha."', '".$tip."',  0     , '".$foto."', '".$neve."', 'on'  , '".$des."' ,   ''  , '".$peso."', '".$purga."', '".$historia."', '".$exp1[0]."', 'C-".$usuario."') ";
	
	$q= " INSERT INTO ".$wbasedato."_000002 (   Medico       ,   Fecha_data        ,        Hora_data           ,    Artcod    ,    Artcom ,   Artgen   ,   Artuni      ,  Artvia   ,   Arttin    ,   Arttve   ,  Artfec      ,   Arttip  ,  Artcon,   Artfot   , Artnev     , Artest, Artdes     ,  Artpes     , Artpur      , Arthis         , Artins        , Artapp , Seguridad) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$nom."', '".$gen."' , '".$exp[0]."' , '".$via."', '".$tin."'  , '".$tve."' ,  '".$fecha."', '".$tip."',  0     , '".$foto."', '".$neve."', 'on'  , '".$des."' , '".$peso."', '".$purga."', '".$historia."', '".$exp1[0]."',   'off'  , 'C-".$usuario."') ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ARTICULO ".mysql_error());
}

/**
 * Graba un insumo del producto
 *
 * @param caracter_type $procod  codigo del producto
 * @param caracter_type $inscod  codigo del insumo
 * @param integer_type $inscan  cantidad del insumo
 * @param caracter_type $usuario  usuario que crea
 */
//function grabarInsumo($procod, $inscod, $inscan, $usuario, $fac)
function grabarInsumo($procod, $inscod, $inscan, $usuario, $fac, $app)
{

	if( empty($fac) ){
		$fac = 0;
	}
	
	global $conex;
	global $wbasedato;

	$q= " INSERT INTO ".$wbasedato."_000003 (   Medico       ,   Fecha_data,                  Hora_data,              Pdepro,      Pdeins ,          Pdecan   ,  Pdefac     , Pdeest,   Pdeapp    , Pdeant, Seguridad    ) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$procod."', '".$inscod."', '".$inscan."' ,  '".$fac."' , 'on'  ,  '".$app."' ,  'on' ,'C-".$usuario."') ";
	
	$q= " INSERT INTO ".$wbasedato."_000003 (   Medico       ,   Fecha_data,                  Hora_data,              Pdepro,      Pdeins ,          Pdecan   ,  Pdefac     , Pdeest,   Pdeapp    ,  Seguridad    ) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$procod."', '".$inscod."', '".$inscan."' ,  '".$fac."' , 'on'  ,  '".$app."' , 'C-".$usuario."') ";


	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR UN INSUMO DEL PRODUCTO ".mysql_error());
}

/**
 * Elima, pone en off el producto
 *
 * @param caracter_type $producto, codigo del producto
 */
function desactivarProducto($producto)
{
	global $conex;
	global $wbasedato;

	$q= "   UPDATE ".$wbasedato."_000002 "
	."      SET Artest = 'off' "
	."    WHERE Artcod = '".$producto."'"
	."      AND Artest = 'on' ";

	$res1 = mysql_query($q,$conex);
}

/**
 * Modifica los datos del prodcuto
 *
 * @param caracter_type $codigo, codigo del producto
 * @param caracter_type $nom, nombre comercial del producto
 * @param caracter_type $gen, nombre generico del producto
 * @param caracter_type $presentacion, unidad del producto
 * @param caracter_type $via, via de administracion del producto
 * @param int_type $tin, tiempo de infusion en dias
 * @param int_type $tve, tiempo de vencimiento en dias
 * @param date_type $fecha, fecha de modificacion del producto
 * @param caracter_type $tip, tipo de producto
 */
function modificarProducto($codigo, $nom, $gen, $presentacion, $via, $tin, $tve, $fecha, $tip, $des, $foto, $neve, $peso, $purga)
{
	global $conex;
	global $wbasedato;

	$q= "   UPDATE ".$wbasedato."_000002 "
	."      SET Artcom = '".$nom."', "
	."      	Artgen = '".$gen."', "
	."      	Artuni = mid('".$presentacion."',1,instr('".$presentacion."','-')-1), "
	."      	Artvia = '".$via."', "
	."      	Artdes = '".$des."', "
	."      	Arttin = '".$tin."', "
	."      	Arttve = '".$tve."', "
	."      	Artfec = '".$fecha."', "
	."      	Arttip = '".$tip."', "
	."      	Artfot = '".$foto."', "
	."      	Artnev = '".$neve."', "
	."      	Artpes = '".$peso."', "
	."      	Artpur = '".$purga."' "
	."    WHERE Artcod = '".$codigo."'"
	."      AND Artest = 'on' ";


	$res1 = mysql_query($q,$conex);
}

/**
 * Borra los insumos del producto, codigo del producto
 *
 * @param caracter_type $procod 
 */
function borrarInsumos($procod)
{
	global $conex;
	global $wbasedato;


	$q= "   DELETE FROM ".$wbasedato."_000003 "
	."    WHERE Pdepro= '".$procod."' "
	."      AND Pdeest = 'on' ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO MODIFICAR CORRECTAMENTE LOS INSUMOS ".mysql_error());
}


function realizarDescarte($cco, $usuario)
{
	global $conex;
	global $wbasedato;

	// $q = "SELECT * "
	// ."      FROM ".$wbasedato."_000006, ".$wbasedato."_000008 "
	// ."     WHERE Condes= 'on' "
	// ."       AND Conind = '-1' "
	// ."      AND Conest = 'on' "
	// ."       AND Concod = Mencon "
	// ."       AND Menfec = '".date('Y-m-d')."' "
	// ."       AND Menaut = 'on' ";
	
	$q = "SELECT * "
	."      FROM ".$wbasedato."_000006, ".$wbasedato."_000008 "
	."     WHERE Condes= 'on' "
	."       AND Conind = '-1' "
	."      AND Conest = 'on' "
	."       AND Concod = Mencon "
	."		 AND Mendoc IN (SELECT mdedoc 
							  FROM cenpro_000007 
							 WHERE Fecha_data='".date('Y-m-d')."'
							   AND Mdeest='on'
						  GROUP BY mdedoc)"
	."       AND Menfec = '".date('Y-m-d')."' "
	."       AND Menaut = 'on' ";

	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num > 0)
	{
		return (false);
	}
	else
	{
		$q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
		//$errlock = mysql_query($q,$conex);

		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '-1' "
		."      AND condes = 'on' "
		."      AND Conest = 'on' ";

		$res1 = mysql_query($q,$conex);

		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '-1'"
		."      AND condes = 'on' "
		."      AND Conest = 'on' ";

		$res1 = mysql_query($q,$conex);
		$row2 = mysql_fetch_array($res1);


		$q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
		$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());


		$q= " INSERT INTO ".$wbasedato."_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menaut, Menest, Seguridad) "
		."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".date('Y')."', '".date('m')."','".$row2[0]."', '".$row2[1]."' , '".date('Y-m-d')."', '".$cco."' , '' ,       '', '".$usuario."',      '' , 'on', 'on', 'C-".$usuario."') ";

		$err = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DE MOVIMIENTO DE DESCARTE ".mysql_error());

		$q = "SELECT Descod, Descan, Appcod, Artcom, Artgen "
		."      FROM ".$wbasedato."_000019, ".$wbasedato."_000009, ".$wbasedato."_000002 "
		."     WHERE Desest = 'on' "
		."      AND Descod = Apppre "
		."      AND Appest = 'on' "
		."      AND Appcod = Artcod"
		."      AND Artest = 'on'";

		$err=mysql_query($q,$conex);
		$num=mysql_num_rows($err);

		for ($i=0; $i<$num; $i++)
		{
			$row = mysql_fetch_array($err);
			$articulo=$row[0].'-'.$row[3].'-'.$row[4];

			$q= " INSERT INTO ".$wbasedato."_000007 (   Medico       ,   Fecha_data        ,       Hora_data            ,     Mdecon    ,    Mdedoc     ,     Mdeart  ,     Mdecan    , Mdefve, Mdenlo, Mdepre          , Mdeest,  Seguridad) "
			."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$row2[1]."', '".$row2[0]."','".$row[2]."', '".$row[1]."' ,  ''   ,  ''   , '".$articulo."' , 'on'  , 'C-".$usuario."') ";

			$res = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL DETALLE DE MOVIMIENTO DE DESCARTE ".mysql_error());

			$q= "   UPDATE ".$wbasedato."_000005 "
			."      SET karexi = karexi - ".$row[1]." "
			."    WHERE Karcod = '".$row[2]."' "
			."      AND karcco = '".$cco."' ";

			$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());

			$q= "   UPDATE ".$wbasedato."_000009 "
			."      SET Appexi = Appexi-".$row[1].""
			."    WHERE Apppre =  '".$row[0]."' "
			."      AND Appcod ='".$row[2]."' "
			."      AND Appest ='on' "
			."      AND Appcco = '".$cco."' ";

			$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());
		}

		return (true);
	}
}

//----------------------------------------------------------funciones del modelo---------------------------
/**
 * Elimina un insumo del producto de la lista que esta ingresando el usuario
 *
 * @param array_type $lista, vector con la lista de insumos
 * @param int_type $index numero de fila en la lista que se va a eliminar
 * @return array_type $lista, vector con la fial eliminada
 */
function eliminarInsumo($lista, $index)
{
	if (count($lista)>1)
	{
		unset($lista[$index]);
		$lista = array_reverse(array_reverse($lista));
	}
	else
	{
		$lista =false;
	}
	return $lista;
}
//----------------------------------------------------------funciones de presentacion------------------------------------------------

/**
 * Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a los scripts consulta.php, seguimiento.php
 * para consulta.php existen dos opciones mandandole el paramentro para=recibidos o para=enviados, asi ese Script consultara
 * uno u otro tipo de requerimiento
 * 
 * Adicionalmente esta funcione se encarga de abrir la forma del Script que se llama informatica
 *
 * No necesita ningun parametro ni devuelve
 */
function pintarTitulo()
{
	echo "<table ALIGN=CENTER width='50%'>";
	//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	echo "<tr><td class='titulo1'>PRODUCCION CENTRAL DE MEZCLAS</td></tr>";
	echo "<tr><td class='titulo2'>Fecha: ".date('Y-m-d')."&nbsp Hora: ".(string)date("H:i:s")."</td></tr></table></br>";

	echo "<table ALIGN=CENTER width='90%' >";
	//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	echo "<tr><td class='texto6' width='15%'><a style='text-decoration:none;color:white' href='cen_mez.php?wbasedato=cen_mez'><b>PRODUCTOS</b></a></td>";
	echo "<td class='texto5' width='15%'><a style='text-decoration:none;color:black' href='lotes.php?wbasedato=lotes.php'>LOTES</a></td>";
	echo "<td class='texto5' width='15%'><a style='text-decoration:none;color:black' href='cargoscm.php?wbasedato=lotes.php&tipo=C'>CARGOS A PACIENTES</a></td>";
	echo "<td class='texto5' width='15%'><a style='text-decoration:none;color:black' href='pos.php?wbasedato=lotes.php&tipo=A'>VENTA EXTERNA</a></td></TR>";
	//echo "<a href='cargos.php?wbasedato=lotes.php&tipo=A'><td class='texto5' width='15%'>AVERIAS</td></a>";
	//echo "<a href='descarte.php?wbasedato=cenmez'><td class='texto5' width='15%'>DESCARTES</td></TR></a>";
	//echo "<a href='consulta.php?para=enviados'><td class='texto5' width='20%'>LISTADO POR PRODUCIR</td></a>";
	//echo "<a href='enviado.php'><td class='texto5' width='20%'>LISTADO PRODUCIDO</td></tr></a>";

	echo "</tr><td class='texto6' >&nbsp;</td>";
	echo "<td class='texto6' >&nbsp;</td>";
	echo "<td class='texto6' >&nbsp;</td>";
	echo "<td class='texto6' >&nbsp;</td></tr></table>";
}

/**
 * Pinta el primer formulario para la busqueda de un producto
 *
 * @param caracter_type $consultas, vector con el resultado de la busqueda por el parametro determinado por usuario
 * @param caracter_type $tipo, 1 busqueda por producto, 2 cuando es busqueda por insumo
 * @param caracter_type $forcon forma de busqueda, si por codigo, nombre, etc
 */
function pintarBusqueda($consultas, $tipo, $forcon)
{
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<form name='producto2' action='cen_mez.php' method=post>";
	echo "<tr><td class='titulo3' colspan='3' align='center'>Consulta de Productos: ";
	echo "<select name='forcon' class='texto5' onchange='enter7()'>";
	echo "<option>".$forcon."</option>";
	if ($forcon!='Codigo')
	echo "<option>Codigo</option>";
	if ($forcon!='Nombre comercial')
	echo "<option>Nombre comercial</option>";
	if ($forcon!='Nombre genérico')
	echo "<option>Nombre genérico</option>";
	if ($forcon!='Insumo')
	echo "<option>Insumo</option>";
	if ($forcon!='paciente')
	echo "<option>Paciente</option>";
	if ($forcon!='Institucion')
	echo "<option>Institucion</option>";
	echo "</select>";

	switch ($tipo)
	{
		case 2:
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de Insumo: ";
		echo "<select name='insfor' class='texto5'>";
		echo "<option>Codigo</option>";
		echo "<option>Nombre comercial</option>";
		echo "<option>Nombre genérico</option>";
		echo "</select>";
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp; ";
		echo "&nbsp; Cantidad:<input type='TEXT' name='parcon2' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'>";
		break;

		case 1:
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		break;

		case 4:
		$lista=consultarInstituciones('');
		echo "<select name='parcon' class='texto5'>";
		echo "<option></option>";
		for ($i=0; $i<count($lista); $i++)
		{
			echo "<option VALUE='".$lista[$i]['cod']."'>".$lista[$i]['cod']."-".$lista[$i]['nom']."</option>";
		}
		echo "</SELECT>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		break;

		case 3:
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de ".$forcon.": ";
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp; ";
		echo "&nbsp; Fecha inicial:<input type='TEXT' name='parcon2' value='' size=10 class='texto5'>&nbsp;Fecha final:<input type='TEXT' name='parcon3' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'>";
		break;
		
	}
	echo "&nbsp; Resultados: <select name='consulta' class='texto5' onchange='enter7()'>";

	if ($consultas[0]['cod']!='')
	{
		for ($i=0;$i<count($consultas);$i++)
		{
			if($tipo!=4)
			{
				echo "<option value='".$consultas[$i]['cod']."-".$consultas[$i]['nom']."-".$consultas[$i]['gen']."-".$consultas[$i]['pre']."'>".$consultas[$i]['cod']."-".$consultas[$i]['nom']."</option>";
			}
			else
			{
				echo "<option value='".$consultas[$i]['cod']."-".$consultas[$i]['nom']."-".$consultas[$i]['gen']."-".$consultas[$i]['pre']."'>".$consultas[$i]['cod']."-".$consultas[$i]['pac']."</option>";
			}
		}
	}
	else
	{
		echo "<option value=''></option>";
	}
	echo "</select>";
	echo "</td></tr>";
	echo "<input type='hidden' name='tippro' value=''></td>";
	echo "</form>";	
}

/**
 * se le envia para pintar en html el formulario con las caracteristicas del prodcuto:
 *
 * @param array_type $tipos, vector con la lista de tipos de producto
 * @param caracter_type $codi, indica si el producto es codificado o no 
 * @param unknown_type $productos, vector de productos encontrados
 * @param unknown_type $presentaciones, vector con unidades para drop down
 * @param unknown_type $fecha, fecha de creacion del producto
 * @param unknown_type $via, via de administracion del producto
 * @param unknown_type $tvd, tiempo de vencimiento en dias
 * @param unknown_type $tvh, tiempo de vnecimeinto en horas
 * @param unknown_type $tfd, tiempo de infusion en dias
 * @param unknown_type $tfh, tiempo de infusion en horas
 * @param unknown_type $estado, estado del producto
 * @param unknown_type $compu, si el nombre es compuesto o no
 * @param unknown_type $consultas, 
 * @param unknown_type $vias, vector con vias de adminsitracion para dropdown
 * @param unknown_type $foto, boolean si es fotosensible
 * @param unknown_type $neve, boolean para conservar en nevera
 */
// function pintarFormulario($tipos, $codi, $productos, $presentaciones, $fecha, $via, $tvd, $tvh, $tfd, $tfh, $estado, $compu, $consultas, $vias, $foto, $neve, $dess, $nutri, $peso, $purga, $pac, $historia, $volumen, $instituciones,$pintarListaNPTPendientes,$wemp_pmla,$NPT_origen,$pintarListaDAPendientes,$DA_tipo)
function pintarFormulario($tipos, $codi, $productos, $presentaciones, $fecha, $via, $tvd, $tvh, $tfd, $tfh, $estado, $compu, $consultas, $vias, $foto, $neve, $dess, $nutri, $peso, $purga, $pac, $historia, $volumen, $instituciones,$pintarListaNPTPendientes,$wemp_pmla,$NPT_origen,$pintarListaDAPendientes,$DA_tipo,$DA_historia,$DA_ingreso,$DA_articulo,$DA_ido,$wronda,$wfecharonda)
{
	global $wbasedato;
	global $bd;
	global $conex;
	global $wusuario;
	
	$esDosisAdaptada = consultarSiTipoEsDA($tipos[0]);
	$esNPToDA = "off";
	
	if($nutri=='on' || ($esDosisAdaptada=='on' && $pintarListaDAPendientes==null && $productos[0]['nom']=="" && ($DA_tipo==null || $DA_tipo=="Generica")))
	{
		$esNPToDA = "on";
	}

	// if($nutri=="on")
	if($nutri=="on" || $esDosisAdaptada=="on")
	{
		$cantColspan = "9";
	}
	else
	{
		$cantColspan = "3";
	}
	
	echo "<form name='producto3' action='cen_mez.php' method=post>";
	echo "<tr><td colspan=3 class='titulo3' align='center'><INPUT TYPE='submit' NAME='NUEVO' VALUE='Nuevo' class='texto5' ></td></tr>";
	echo "</table></form>";
	
	if($DA_historia != "" && $DA_ingreso!="")
	{
		pintarEncabezadoDA($DA_historia,$DA_ingreso,$cantColspan);
	}

	echo "<form name='producto' action='cen_mez.php' method=post>";
	echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='hidden' name='wusuario' id='wusuario' value='".$wusuario."'/>";
	echo "<input type='hidden' name='wbd' id='wbd' value='".$bd."'/>";
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan=".$cantColspan." class='titulo3' align='center'><b>Informacion general del Producto</b></td></tr>";

	echo "<tr><td class='texto1' colspan='".$cantColspan."' align='center'>Tipo: ";
	echo "<select name='tippro' onchange='enter7()' class='texto5'>";
	for ($i=0;$i<count($tipos);$i++)
	{
		echo "<option>".$tipos[$i]."</option>";
	}
	echo "</select>";
	echo "</td></tr>";

	if ($codi=='on')
	{
		echo "<tr><td class='texto1' colspan='3' align='center'>Buscar Producto por: ";
		echo "<select name='forbus' class='texto5'>";
		echo "<option>Codigo</option>";
		echo "<option>Nombre comercial</option>";
		echo "<option>Nombre genérico</option>";
		echo "</select><input type='TEXT' name='parbus' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter1()' class='texto5'> ";
		echo "<select name='producto' class='texto5' onchange='enter1()'>";
		if (is_array($productos) && $productos[0]['cod']!='')
		{
			for ($i=0;$i<count($productos);$i++)
			{
				echo "<option value='".$productos[$i]['cod']."-".$productos[$i]['nom']."-".$productos[$i]['gen']."-".$productos[$i]['pre']."'>".$productos[$i]['cod']."-".$productos[$i]['nom']."</option>";
			}
		}
		else
		{
			echo "<option value=''></option>";
		}
		echo "</select>";
		echo "</td></tr>";

		$datos = array('cod'=>'','nom'=>'','gen'=>'');
		if (is_array($productos))
		{
			$datos['cod'] = $productos[0]['cod'];
			$datos['nom'] = $productos[0]['nom'];
			$datos['gen'] = $productos[0]['gen'];
		}
		
		echo "<tr><td class='texto2' colspan='1' align='left'>Codigo: <input type='TEXT' name='productos[0][cod]' value='".$datos['cod']."' readonly='readonly' class='texto2' size='10'></td>";
		echo "<td class='texto2' colspan='2' align='left'>Nombre comercial: <input type='TEXT' name='productos[0][nom]' value='".$datos['nom']."' readonly='readonly' class='texto2' size='50'></td></tr>";
		echo "<tr><td class='texto2' colspan='2' align='left'>Nombre genérico: <input type='TEXT' name='productos[0][gen]' value='".$datos['gen']."' readonly='readonly' class='texto2' size='50'></td>";
	}
	else
	{
		if($nutri=='on')
		{
			// global $wbasedato;
			// global $bd;
			// global $conex;
			
			$qNutriciones  = "SELECT Kadhis,Kading,Kadart,Kadido,Kadfin,Kadhin,Pacno1,Pacno2,Pacap1,Pacap2,Ccocod,Cconom,Habcpa,Artgen,Enuobs,Enupes,Enutin,Enupur,Enuvol,Enurea ,'ordenes' AS origen  
								FROM ".$bd."_000214,".$bd."_000054,root_000036,root_000037,".$bd."_000018,".$bd."_000020,".$bd."_000011,".$wbasedato."_000002
							   WHERE Kadhis = Enuhis
								 AND Kading = Enuing
								 AND Kadart = Enuart
								 AND Kadido = Enuido
								 AND Kadfec='".date('Y-m-d')."' 
								 AND Kadsus = 'off'
								 AND Kadest = 'on'
								 AND Kadcon = 'on'
								 AND Kadhis = Orihis
								 AND Kading = Oriing
								 AND Oriori = '01'
								 AND Oriced = Pacced
								 AND Oritid = Pactid
								 AND Kadhis = Ubihis
								 AND Kading = Ubiing
								 AND Kadhis = Habhis
								 AND Kading = Habing
								 AND Ccocod = Ubisac
								 AND Artcod = Kadart
								 AND Enuest = 'on'
								 AND Enuord = 'on'
								 
							   UNION
							   
							  SELECT Kadhis,Kading,Kadart,Kadido,Kadfin,Kadhin,Pacno1,Pacno2,Pacap1,Pacap2,Ccocod,Cconom,Habcpa,Artgen,'' AS Enuobs,'' AS Enupes,'' AS Enutin,'' AS Enupur,'' AS Enuvol,'' AS Enurea ,'kardex' AS origen 
								FROM ".$bd."_000054,root_000036,root_000037,".$bd."_000018,".$bd."_000020,".$bd."_000011 ,".$wbasedato."_000002,".$wbasedato."_000001,".$bd."_000068   
							   WHERE Kadfec='".date('Y-m-d')."' 
								 AND Kadest='on' 
								 AND Kadsus='off'
								 AND Kadhis = Orihis 
								 AND Kading = Oriing
								 AND Kadcon = 'on'
								 AND Kadart = Arkcod
								 AND Oriori = '01' 
								 AND Oriced = Pacced 
								 AND Oritid = Pactid 
								 AND Kadhis = Ubihis 
								 AND Kading = Ubiing 
								 AND Kadhis = Habhis 
								 AND Kading = Habing 
								 AND Ccocod = Ubisac 
								 AND Artcod = Kadart 
								 
								 AND tippro = 'on' 
								 AND Tipnco = 'off' 
								 AND Tipcdo != 'on'
								 AND Tipest = 'on'
								 AND Arttip = Tipcod
								 AND Artcod = Arkcod
								 AND Tiptpr = Arktip
								   
							ORDER BY Kadfin,Kadhin,Kadhis,Kading,origen DESC;";
			
			// echo"<pre>".print_r($qNutriciones,true)."<pre>";
			
			
			$resNutriciones = mysql_query($qNutriciones, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qNutriciones . " - " . mysql_error());
			$numNutriciones = mysql_num_rows($resNutriciones);
			
			$soloLectura="";
			if(($pintarListaNPTPendientes==null && $instituciones[0]['cod']==$wemp_pmla && $historia=="") || ($instituciones[0]['cod']==$wemp_pmla && $NPT_origen=="ordenes"))
			{
				$soloLectura = "readOnly='readOnly'";
			}
						
			echo "<tr class='titulo3'><td colspan='".$cantColspan."'>CONSULTAR</td></tr>";
			
			echo "<tr><td class='texto2' colspan='1' align='left'>Peso Kg.: <input type='TEXT' id='peso' name='peso' value='".$peso."'  class='texto5' size='5' onchange='validarFormulario6()' ".$soloLectura."></td>";
				echo "<td class='texto2' colspan='1' align='left'>Purga: <input type='TEXT' id='purga' name='purga' value='".$purga."'  class='texto5' size='10' onchange='validarFormulario7()' ".$soloLectura."></td>";
				echo "<td class='texto2' colspan='1' align='left'>Volumen: <input type='TEXT' id='volumen' name='volumen' value='".$volumen."'  class='texto5' size='10' onchange='validarFormulario8()' ".$soloLectura.">";
				echo "<td class='texto2' colspan='1' align='center'>Institucion: ";
				
					if($instituciones[0]['cod']==$wemp_pmla && $historia!="")
					{
						echo "<select name='insti' id='insti' class='texto5' ".$deshabilitar." onchange='cambiarInstitucionNPT(this,$wemp_pmla)'>";
						echo "<option>".$instituciones[0]['cod']."-".$instituciones[0]['nom']."</option>";
						echo "</select>";
						
					}
					else
					{
						echo "<select name='insti' id='insti' class='texto5' ".$deshabilitar." onchange='cambiarInstitucionNPT(this,$wemp_pmla)'>";
						
						if ($instituciones[0]['cod']!='')
						{
							for ($i=0;$i<count($instituciones);$i++)
							{
								echo "<option>".$instituciones[$i]['cod']."-".$instituciones[$i]['nom']."</option>";
							}
						}
						else
						{
							echo "<option value=''></option>";
						}
						echo "</select>";
					}
			echo "</td>";

			echo "<td class='texto2' colspan='1' align='left'>Historia: <input type='TEXT' id='historia' name='historia' value='".$historia."' class='texto5' ".$soloLectura."></td>";
			echo "<td class='texto2' colspan='1' align='left'>Nombre: <input type='TEXT' id='pac' name='pac' value='".$pac."' class='texto2' size='50' ".$soloLectura."></td>";
			echo "<td colspan=2 class='titulo3' align='center'><input type='checkbox' name='crear' id='checkCrearNPT' class='titulo3'>Crear &nbsp;<INPUT TYPE='submit' id='btnAceptarNPT' NAME='buscar' VALUE='Aceptar' class='texto5'></td></tr>";
			
			echo "<input type='hidden' id='opcionInstitucion' name='opcionInstitucion' value=''>";
			
			if($pintarListaNPTPendientes==null && $instituciones[0]['cod']==$wemp_pmla && $historia=="")
			{
				echo "	<tr class='titulo3 NPT_PENDIENTES' ><td colspan='".$cantColspan."'>NUTRICIONES PARENTERALES PENDIENTES</td></tr>";
				echo "	<tr class='texto2 NPT_PENDIENTES' align='center' >
							<td align='center'>Origen</td>
							<td align='center'>Historia</td>
							<td align='center'>Nombre del paciente</td>
							<td align='center'>Servicio</td>
							<td align='center'>Habitacion</td>
							<td align='center'>Nutricion</td>
							<td align='center'>Fecha y hora de inicio</td>
							<td align='center'>Observaciones</td>
						</tr>";
				
				echo "<input type='hidden' id='NPT_historia' name='NPT_historia' value=''>";
				echo "<input type='hidden' id='NPT_ingreso' name='NPT_ingreso' value=''>";
				echo "<input type='hidden' id='NPT_articulo' name='NPT_articulo' value=''>";
				echo "<input type='hidden' id='NPT_ido' name='NPT_ido' value=''>";
				echo "<input type='hidden' id='NPT_tiempoInfusion' name='NPT_tiempoInfusion' value=''>";
				echo "<input type='hidden' id='NPT_origen' name='NPT_origen' value=''>";
								
				if($numNutriciones > 0)
				{
					$contNPTPendientes = 0;
					
					while($rowsNutriciones = mysql_fetch_array($resNutriciones))
					{
						$sinRealizar = true;
						//Si es de Kardex validar que no se haya realizado la nutricion
						if($rowsNutriciones['origen']=="kardex")
						{
							$qValidarNPTKardex = "SELECT *  
													FROM ".$bd."_000214 
												   WHERE Enuhis='".$rowsNutriciones['Kadhis']."' 
													 AND Enuing='".$rowsNutriciones['Kading']."' 
													 AND Enuart='".$rowsNutriciones['Kadart']."' 
													 AND Enuido='".$rowsNutriciones['Kadido']."' 
													 AND Enuord='off'
													 AND Enurea='on'
													 ;";
							
							$resValidarNPTKardex = mysql_query($qValidarNPTKardex, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qValidarNPTKardex . " - " . mysql_error());
							$numValidarNPTKardex = mysql_num_rows($resValidarNPTKardex);
							
							if($numValidarNPTKardex > 0)
							{
								$sinRealizar = false;
							}
										
						}
						
						if($sinRealizar)
						{
							//armar array
							$idArrayNPT = $rowsNutriciones['Kadhis']."-".$rowsNutriciones['Kading']."-".$rowsNutriciones['Kadart']."-".$rowsNutriciones['Kadido'];
							if(!isset($arrayNPTPendientes[$idArrayNPT]))
							{
								$arrayNPTPendientes[$idArrayNPT]['Kadhis'] =$rowsNutriciones['Kadhis'] ;
								$arrayNPTPendientes[$idArrayNPT]['Kading'] =$rowsNutriciones['Kading'] ;
								$arrayNPTPendientes[$idArrayNPT]['Kadart'] =$rowsNutriciones['Kadart'] ;
								$arrayNPTPendientes[$idArrayNPT]['Kadido'] =$rowsNutriciones['Kadido'] ;
								$arrayNPTPendientes[$idArrayNPT]['Kadfin'] =$rowsNutriciones['Kadfin'] ;
								$arrayNPTPendientes[$idArrayNPT]['Kadhin'] =$rowsNutriciones['Kadhin'] ;
								$arrayNPTPendientes[$idArrayNPT]['Pacno1'] =$rowsNutriciones['Pacno1'] ;
								$arrayNPTPendientes[$idArrayNPT]['Pacno2'] =$rowsNutriciones['Pacno2'] ;
								$arrayNPTPendientes[$idArrayNPT]['Pacap1'] =$rowsNutriciones['Pacap1'] ;
								$arrayNPTPendientes[$idArrayNPT]['Pacap2'] =$rowsNutriciones['Pacap2'] ;
								$arrayNPTPendientes[$idArrayNPT]['Ccocod'] =$rowsNutriciones['Ccocod'] ;
								$arrayNPTPendientes[$idArrayNPT]['Cconom'] =$rowsNutriciones['Cconom'] ;
								$arrayNPTPendientes[$idArrayNPT]['Habcpa'] =$rowsNutriciones['Habcpa'] ;
								$arrayNPTPendientes[$idArrayNPT]['Artgen'] =$rowsNutriciones['Artgen'] ;
								$arrayNPTPendientes[$idArrayNPT]['Enuobs'] =$rowsNutriciones['Enuobs'] ;
								$arrayNPTPendientes[$idArrayNPT]['Enupes'] =$rowsNutriciones['Enupes'] ;
								$arrayNPTPendientes[$idArrayNPT]['Enutin'] =$rowsNutriciones['Enutin'] ;
								$arrayNPTPendientes[$idArrayNPT]['Enupur'] =$rowsNutriciones['Enupur'] ;
								$arrayNPTPendientes[$idArrayNPT]['Enuvol'] =$rowsNutriciones['Enuvol'] ;
								$arrayNPTPendientes[$idArrayNPT]['Enurea'] =$rowsNutriciones['Enurea'] ;
								$arrayNPTPendientes[$idArrayNPT]['origen'] =$rowsNutriciones['origen'] ;
							}
						}
						
					}
					
					if(count($arrayNPTPendientes)>0)
					{
						foreach($arrayNPTPendientes as $key => $value)
						{
							if($value['Enurea']!="on")
							{
								$contNPTPendientes++;
								if ($fila_lista=="texto4")
									$fila_lista = "texto3";
								else
									$fila_lista = "texto4";
								// unset($accion);
								$nombrePaciente = $value['Pacno1']." ".$value['Pacno2']." ".$value['Pacap1']." ".$value['Pacap2'];
								$funcionOnclickNPTPendientes= "onClick='crearNPT(\"".$value['Kadhis']."\",\"".$value['Kading']."\",\"".$value['Enupes']."\",\"".$value['Enutin']."\",\"".$value['Enupur']."\",\"".$value['Enuvol']."\",\"".$nombrePaciente."\",\"".$value['Kadart']."\",\"".$value['Kadido']."\",\"".$value['origen']."\");'";
								
								echo "	<tr class='".$fila_lista." NPT_PENDIENTES' style='cursor:pointer;'>
											<td ".$funcionOnclickNPTPendientes.">".strtoupper($value['origen'])."</td>
											<td ".$funcionOnclickNPTPendientes.">".$value['Kadhis']."-".$value['Kading']."</td>
											<td ".$funcionOnclickNPTPendientes.">".$nombrePaciente."</td>
											<td ".$funcionOnclickNPTPendientes.">".$value['Ccocod']."-".$value['Cconom']."</td>
											<td ".$funcionOnclickNPTPendientes.">".$value['Habcpa']."</td>
											<td ".$funcionOnclickNPTPendientes.">".$value['Kadart']."-".$value['Artgen']."</td>
											<td ".$funcionOnclickNPTPendientes.">".$value['Kadfin']." ".$value['Kadhin']."</td>
											<td ".$funcionOnclickNPTPendientes.">".$value['Enuobs']."</td>
									</tr>";
							}
						}
					}
					
					
					if($contNPTPendientes == 0)
					{
						echo "	<tr class='texto4 NPT_PENDIENTES'>
									<td colspan='".$cantColspan."' align='center'><b>No hay nutriciones pendientes por crear</b></td>
							</tr>";
					}
				}
				else
				{
					echo "	<tr class='texto4 NPT_PENDIENTES'>
									<td colspan='".$cantColspan."' align='center'><b>No hay nutriciones pendientes por crear</b></td>
							</tr>";
				}
				
			}
			
			
		}
		
		if($esDosisAdaptada=="on")
		{
			global $wbasedato;
			global $bd;
			global $conex;
			
			$qDosisAdaptadas = queryDA($wemp_pmla,$wbasedato,$bd);
			// echo"<pre>".print_r($qDosisAdaptadas,true)."<pre>";
			
			$resDosisAdaptadas = mysql_query($qDosisAdaptadas, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDosisAdaptadas . " - " . mysql_error());
			$numDosisAdaptadas = mysql_num_rows($resDosisAdaptadas);


			if($numDosisAdaptadas > 0)
			{
				$contDosisAdaptadas = 0;
				$cantSinEquivalente = 0;
				$arrayDAPendientes = array();
				
				if($pintarListaDAPendientes==null && $productos[0]['nom']=="" && $productos[0]['gen']=="")
				{
					echo "<input type='hidden' id='DA_historia' name='DA_historia' value=''>";
					echo "<input type='hidden' id='DA_ingreso' name='DA_ingreso' value=''>";
					echo "<input type='hidden' id='DA_articulo' name='DA_articulo' value=''>";
					echo "<input type='hidden' id='DA_ido' name='DA_ido' value=''>";
					echo "<input type='hidden' id='DA_articuloCM' name='DA_articuloCM' value=''>";
					echo "<input type='hidden' id='DA_cantidadSinPurga' name='DA_cantidadSinPurga' value=''>";
					echo "<input type='hidden' id='DA_cantidad' name='DA_cantidad' value=''>";
					echo "<input type='hidden' id='DA_tipo' name='DA_tipo' value=''>";
					echo "<input type='hidden' id='wronda' name='wronda' value=''>";
					echo "<input type='hidden' id='wfecharonda' name='wfecharonda' value=''>";
					echo "<input type='hidden' id='DA_cco' name='DA_cco' value=''>";
					
					echo "	<tr class='titulo3 DA_PENDIENTES' ><td colspan='".$cantColspan."'>DOSIS ADAPTADAS PARENTERALES PENDIENTES</td></tr>";
					echo "	<tr class='texto2 DA_PENDIENTES' align='center' >
								<td align='center'>Historia</td>
								<td align='center'>Nombre del paciente</td>
								<td align='center'>Servicio</td>
								<td align='center'>Habitacion</td>
								<td align='center'>Articulo</td>
								<td align='center'>Tipo</td>
								<td align='center'>Fecha y hora de inicio</td>
								<td align='center'>Observaciones</td>
								<td align='center'></td>
							</tr>";
					
					while($rowsDosisAdaptadas = mysql_fetch_array($resDosisAdaptadas))
					{
						$qValidarDArealizada = "SELECT *  
												FROM ".$bd."_000224,".$wbasedato."_000002
											   WHERE Rdahis='".$rowsDosisAdaptadas['Kadhis']."' 
												 AND Rdaing='".$rowsDosisAdaptadas['Kading']."' 
												 AND Rdaart='".$rowsDosisAdaptadas['Kadart']."' 
												 AND Rdaido='".$rowsDosisAdaptadas['Kadido']."' 
												 AND Rdaest='on'
												 AND Artcod=Rdacda
												 AND Artest='on';";
						
						$resValidarDArealizada = mysql_query($qValidarDArealizada, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qValidarDArealizada . " - " . mysql_error());
						$numValidarDArealizada = mysql_num_rows($resValidarDArealizada);
						
						if($numValidarDArealizada == 0)
						{
							$idArrayDA = $rowsDosisAdaptadas['Kadhis']."-".$rowsDosisAdaptadas['Kading']."-".$rowsDosisAdaptadas['Kadart']."-".$rowsDosisAdaptadas['Kadido'];
							if(!isset($arrayDAPendientes[$idArrayDA]))
							{
								$arrayDAPendientes[$idArrayDA]['Kadhis'] = $rowsDosisAdaptadas['Kadhis'];
								$arrayDAPendientes[$idArrayDA]['Kading'] = $rowsDosisAdaptadas['Kading'];
								$arrayDAPendientes[$idArrayDA]['Kadart'] = $rowsDosisAdaptadas['Kadart'];
								$arrayDAPendientes[$idArrayDA]['Kadido'] = $rowsDosisAdaptadas['Kadido'];
								$arrayDAPendientes[$idArrayDA]['Kadcfr'] = $rowsDosisAdaptadas['Kadcfr'];
								$arrayDAPendientes[$idArrayDA]['Kadufr'] = $rowsDosisAdaptadas['Kadufr'];
								$arrayDAPendientes[$idArrayDA]['Kadfin'] = $rowsDosisAdaptadas['Kadfin'];
								$arrayDAPendientes[$idArrayDA]['Kadhin'] = $rowsDosisAdaptadas['Kadhin'];
								$arrayDAPendientes[$idArrayDA]['Kadobs'] = $rowsDosisAdaptadas['Kadobs'];
								$arrayDAPendientes[$idArrayDA]['Pacno1'] = $rowsDosisAdaptadas['Pacno1'];
								$arrayDAPendientes[$idArrayDA]['Pacno2'] = $rowsDosisAdaptadas['Pacno2'];
								$arrayDAPendientes[$idArrayDA]['Pacap1'] = $rowsDosisAdaptadas['Pacap1'];
								$arrayDAPendientes[$idArrayDA]['Pacap2'] = $rowsDosisAdaptadas['Pacap2'];
								$arrayDAPendientes[$idArrayDA]['Artgen'] = $rowsDosisAdaptadas['Artgen'];
								$arrayDAPendientes[$idArrayDA]['Ccocod'] = $rowsDosisAdaptadas['Ccocod'];
								$arrayDAPendientes[$idArrayDA]['Cconom'] = $rowsDosisAdaptadas['Cconom'];
								$arrayDAPendientes[$idArrayDA]['Habcpa'] = $rowsDosisAdaptadas['Habcpa'];
								$arrayDAPendientes[$idArrayDA]['tipo']   = $rowsDosisAdaptadas['tipo'];
							}
						}		
					}
					
					if(count($arrayDAPendientes)>0)
					{
						foreach($arrayDAPendientes as $key => $value)
						{
							$qArticuloEquivalenteCM = "SELECT Appcod,Appcnv 
														 FROM ".$wbasedato."_000009 
														 WHERE apppre='".$value['Kadart']."' 
														   AND Appest='on';";
							
							$resArticuloEquivalenteCM = mysql_query($qArticuloEquivalenteCM, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qArticuloEquivalenteCM . " - " . mysql_error());
							$numArticuloEquivalenteCM = mysql_num_rows($resArticuloEquivalenteCM);	
							
							$codArtCM = "";
							$cantidadEquivalenteCM = 0;
							if($numArticuloEquivalenteCM>0)
							{
								$rowsArticuloEquivalenteCM = mysql_fetch_array($resArticuloEquivalenteCM);
								
								if($rowsArticuloEquivalenteCM['Appcod']!="")
								{
									$codArtCM = $rowsArticuloEquivalenteCM['Appcod'];
									$cantidadEquivalenteCM = (float)$rowsArticuloEquivalenteCM['Appcnv'];
								}
							}
							
							// ------------------------------
							
							if($codArtCM!="")
							{
								$qUnidadArticuloEquivalenteCM = "SELECT Artuni 
																   FROM ".$wbasedato."_000002 
																  WHERE Artcod='".$codArtCM."' 
																	AND Artest='on';";
								
								$resUnidadArticuloEquivalenteCM = mysql_query($qUnidadArticuloEquivalenteCM, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qUnidadArticuloEquivalenteCM . " - " . mysql_error());
								$numUnidadArticuloEquivalenteCM = mysql_num_rows($resUnidadArticuloEquivalenteCM);	
								
								$unidadArtCM = "";
								if($numUnidadArticuloEquivalenteCM>0)
								{
									$rowsUnidadArticuloEquivalenteCM = mysql_fetch_array($resUnidadArticuloEquivalenteCM);
									
									if($rowsUnidadArticuloEquivalenteCM['Artuni']!="")
									{
										$unidadArtCM = $rowsUnidadArticuloEquivalenteCM['Artuni'];
									}
								}
							}
							
							// ------------------------------
							if ($fila_lista=="texto4")
								$fila_lista = "texto3";
							else
								$fila_lista = "texto4";
							
							$codigoGenerico = esArticuloGenerico( $conex, $bd, $wbasedato, $value['Kadart'] );
							
							if($codArtCM=="" && !$codigoGenerico)
							{
								$fila_lista = "SinEquivalente";
								$cantSinEquivalente++;
							}
							
							// ---------------------------------------------------------------
							// dosis
							// ---------------------------------------------------------------
							
							$dosis = (float)$value['Kadcfr'];
							
							// // Valida si el medicamento esta marcado como compuesto en movhos_000059, si es así consultar en movhos_000208 o movhos_000209 la dosis del antibiotico
							// $dosisMedicamentoCompuesto = consultarDosisSiMedicamentoCompuesto($value['Kadhis'],$value['Kading'],$value['Kadart'],$value['Kadido']);
							
							// if($dosisMedicamentoCompuesto!="")
							// {
								// $dosis = $dosisMedicamentoCompuesto;
							// }
							
							
							
						
							//calculo
							if( $value['Kadufr'] != $unidadArtCM )
							{
								//Se busca la concentración del articulo, para dejar la dosis del médico en las unidades en que se factura
								$concentracionArticuloSF = (float)consultarConcentracionArticuloSF( $conex, $bd, $value['Kadart'] );
								$dosis 					 = $dosis/$concentracionArticuloSF*$cantidadEquivalenteCM;
							}
							
							
							// tener en cuenta la purga
							$dosisConPurga=$dosis;
							$purgaDA = consultarPurgaDA($value['Ccocod']);
							
							if($purgaDA!="0" && $codArtCM!="")
							{
								$datosArticulo = consultarValoresArticulo($codArtCM,$value['Kadhis']);
								$dosisConPurga = ($purgaDA*$datosArticulo['concInfusion'])+ $dosis;
							}
							
							$dosis = round($dosis*1,2);
							$dosisConPurga = round($dosisConPurga,2);
							// ---------------------------------------------------------------
							
							$horaInicio = explode(":",$value['Kadhin']);
							
							$ronda = $horaInicio[0];
							$fechaRonda = $value['Kadfin'];
							
							$nombrePaciente = $value['Pacno1']." ".$value['Pacno2']." ".$value['Pacap1']." ".$value['Pacap2'];
							$funcionOnclickDAPendientes= "";
							$funcionOnclickDAPendientes= "onClick='crearDA(\"".$value['Kadhis']."\",\"".$value['Kading']."\",\"".$value['Kadart']."\",\"".trim($value['Artgen'])."\",\"".$value['Kadido']."\",\"".$codArtCM."\",\"".$dosis."\",\"".$dosisConPurga."\",\"".$value['tipo']."\",\"".$ronda."\",\"".$fechaRonda."\",\"".$value['Ccocod']."\");'";
														
							$kadobs1="";
							if(trim($value['Kadobs'])!="")
							{
								$observaciones=explode("<div",$value['Kadobs']);
							
								for($s=1;$s<count($observaciones);$s++)
								{
									$observacion="<div".$observaciones[$s];
									
									$obs = nl2br(strip_tags( substr( $observacion, 0, strpos($observacion, "<span" ) ) ));
									
									if(trim($obs)!="")
									{
										$kadobs1 .= "- ".$obs."<br>";
									}
									
								}
							}
							
							$obs = strip_tags($value['Kadobs'],"<br><span>");
							
							$cancelarDA = "";
							if($value['tipo']=="Dosis adaptada")
							{
								$cancelarDA = "<span style='font-size:8pt;'><input type='checkbox' id='noPrepararDA' name='noPrepararDA' onclick='cancelarPreparacionDA(\"".$value['Kadhis']."\",\"".$value['Kading']."\",\"".$value['Kadart']."\",\"".$value['Kadido']."\",event,this);'>No preparar como DA</span>";
							}
							
							echo "	<tr class='".$fila_lista." DA_PENDIENTES' style='cursor:pointer;' ".$funcionOnclickDAPendientes.">
										<td>".$value['Kadhis']."-".$value['Kading']."</td>
										<td>".$nombrePaciente."</td>
										<td>".$value['Ccocod']."-".$value['Cconom']."</td>
										<td>".$value['Habcpa']."</td>
										<td>".$value['Kadart']."-".$value['Artgen']."</td>
										<td>".$value['tipo']."</td>
										<td>".$value['Kadfin']." ".$value['Kadhin']."</td>
										<td align='left'>".$kadobs1."</td>
										<td align='left'>".$cancelarDA."</td>
								</tr>";
								
							$contDosisAdaptadas++;	
						}
					}	
					
					if($contDosisAdaptadas==0)
					{
						echo "	<tr class='texto4 NPT_PENDIENTES'>
										<td colspan='".$cantColspan."' align='center'><b>No hay dosis adaptadas pendientes por crear</b></td>
								</tr>";
					}
					else
					{
						if($cantSinEquivalente!=0)
						{
							// convencion
							echo "	<tr class='texto7' align='left'>
											<td colspan='".$cantColspan."'><br>&nbsp;<span class='SinEquivalente'>&nbsp;&nbsp;&nbsp;&nbsp;</span> Articulo sin equivalente activo en central de mezclas (cenpro_000009)</td>
									</tr>";
						}
					}
				}
			}
			else
			{
				echo "	<tr class='texto4 NPT_PENDIENTES'>
								<td colspan='".$cantColspan."' align='center'><b>No hay dosis adaptadas pendientes por crear</b></td>
						</tr>";
			}
			
		}

		// if ((isset($pac) and $pac!='') or $nutri!='on')
		if ((isset($pac) and $pac!='') or $esNPToDA!='on')
		{
			$exp=explode('-',$tipos[0]);
			echo "<tr><td class='texto2' colspan='1' align='left'>Codigo: <input type='TEXT' name='productos[0][cod]' value='".$productos[0]['cod']."' readonly='readonly' class='texto2'>";
			
			if($nutri=='on')
			{
				if ($compu=='off' or $productos[0]['nom']=='')
				{
					echo "<td class='texto2' colspan='2' align='left'>Nombre comercial: <input type='TEXT' name='productos[0][nom]' value='".$productos[0]['nom']."' class='texto2' readonly='readonly' size='50'></td>";
					echo "<td class='texto2' colspan='2' align='left'>Nombre genérico: <input type='TEXT' name='productos[0][gen]' value='".$productos[0]['gen']."' class='texto2' readonly='readonly' size='50'></td>";
				}
				else
				{
					echo "<td class='texto2' colspan='2' align='left'>Nombre comercial: <input type='TEXT' name='productos[0][nom]' value='".$productos[0]['nom']."' class='texto2' size='50'></td>";
					echo "<td class='texto2' colspan='2' align='left'>Nombre genérico: <input type='TEXT' name='productos[0][gen]' value='".$productos[0]['gen']."' class='texto2' size='50'></td>";
				}
			}
			else
			{
				if ($compu=='off' or $productos[0]['nom']=='')
				{
					echo "<td class='texto2' colspan='2' align='left'>Nombre comercial: <input type='TEXT' name='productos[0][nom]' value='".$productos[0]['nom']."' class='texto2' readonly='readonly' size='50'></td></tr>";
					echo "<tr><td class='texto2' colspan='2' align='left'>Nombre genérico: <input type='TEXT' name='productos[0][gen]' value='".$productos[0]['gen']."' class='texto2' readonly='readonly' size='50'></td>";
				}
				else
				{
					echo "<td class='texto2' colspan='2' align='left'>Nombre comercial: <input type='TEXT' name='productos[0][nom]' value='".$productos[0]['nom']."' class='texto2' size='50'></td></tr>";
					echo "<tr><td class='texto2' colspan='2' align='left'>Nombre genérico: <input type='TEXT' name='productos[0][gen]' value='".$productos[0]['gen']."' class='texto2' size='50'></td>";
				}
			}
		}
	}

	// if ((isset($pac) and $pac!='') or $nutri!='on' or $codi=='on')
	if ((isset($pac) and $pac!='') or $esNPToDA!='on' or $codi=='on')
	{
		$cantColspan = "1";
		if($nutri=="on")
		{
			$cantColspan = "2";
		}
		
		echo "<td class='texto2' colspan='".$cantColspan."' align='left'>Presentación: ";
		echo "<select name='presentacion' class='texto5' >";
		for ($i=0;$i<count($presentaciones);$i++)
		{
			echo "<option >".$presentaciones[$i]."</option>";
		}
		echo "</select>";
		echo "</td></tr>";
		
		if($nutri=="on")
		{
			$soloLectura="";
			if($instituciones[0]['cod']==$wemp_pmla && $NPT_origen=="ordenes")
			{
				$soloLectura = "readOnly='readOnly'";
			}
						
			echo "<tr><td class='texto2' colspan='1' align='left'>Vía de administración:<select name='via' class='texto5'>";
			for ($i=0;$i<count($vias);$i++)
			{
				echo "<option>".$vias[$i]."</option>";
			}
			echo "</select></td>";
			echo "<td class='texto2' colspan='2' align='left'>Tiempo de infusion: <input type='TEXT' name='tfd' value='".$tfd."'  class='texto5' size='5' ".$soloLectura." onchange='validarFormulario1()'>&nbsp; horas &nbsp;<input type='TEXT' name='tfh' value='".$tfh."'  class='texto5' size='5' ".$soloLectura."  onchange='validarFormulario2()'>&nbsp; min </td>";
			echo "<td class='texto2' colspan='1' align='left'>Tiempo de vencimiento: <input type='TEXT' name='tvd' value='".$tvd."'  class='texto5' size='5' onchange='validarFormulario3()'>&nbsp; dias </td>";
			$cad='validarFormulario5("'.$fecha.'")';
			echo "<td class='texto2' colspan='1' align='left'>Fecha de creación: <input type='TEXT' name='fecha' value='".$fecha."'  class='texto5' onblur='".$cad."'></td>";
			if ($foto=='on')
			{
				echo "<td class='texto2' colspan='1' align='left'>Fotosensible: <input type='checkbox' name='foto' value='on'  checked class='texto2' size='5'>&nbsp&nbsp&nbsp&nbsp";
			}
			else
			{
				echo "<td class='texto2' colspan='1' align='left'>Fotosensible <input type='checkbox' name='foto' value='on'  class='texto2'>&nbsp&nbsp&nbsp&nbsp";
			}
			if ($neve=='on')
			{
				echo "Conservar en nevera: <input type='checkbox' name='neve' value='on' checked class='texto2'></td>";

			}
			else
			{
				echo "Conservar en nevera: <input type='checkbox' name='neve' value='on' class='texto2'></td>";
			}
		}
		else
		{
			echo "<tr><td class='texto2' colspan='1' align='left'>Vía de administración:<select name='via' class='texto5'>";
			for ($i=0;$i<count($vias);$i++)
			{
				echo "<option>".$vias[$i]."</option>";
			}
			echo "</select></td>";
			echo "<td class='texto2' colspan='1' align='left'>Tiempo de infusion: <input type='TEXT' name='tfd' value='".$tfd."'  class='texto5' size='5' onchange='validarFormulario1()'>&nbsp; horas &nbsp;<input type='TEXT' name='tfh' value='".$tfh."'  class='texto5' size='5'  onchange='validarFormulario2()'>&nbsp; min </td>";
			echo "<td class='texto2' colspan='1' align='left'>Tiempo de vencimiento: <input type='TEXT' name='tvd' value='".$tvd."'  class='texto5' size='5' onchange='validarFormulario3()'>&nbsp; dias </td></tr>";
			$cad='validarFormulario5("'.$fecha.'")';
			echo "<tr><td class='texto2' colspan='1' align='left'>Fecha de creación: <input type='TEXT' name='fecha' value='".$fecha."'  class='texto5' onblur='".$cad."'></td>";
			if ($foto=='on')
			{
				echo "<td class='texto2' colspan='1' align='left'>Fotosensible: <input type='checkbox' name='foto' value='on'  checked class='texto2' size='5'>&nbsp&nbsp&nbsp&nbsp";
			}
			else
			{
				echo "<td class='texto2' colspan='1' align='left'>Fotosensible <input type='checkbox' name='foto' value='on'  class='texto2'>&nbsp&nbsp&nbsp&nbsp";
			}
			if ($neve=='on')
			{
				echo "Conservar en nevera: <input type='checkbox' name='neve' value='on' checked class='texto2'></td>";

			}
			else
			{
				echo "Conservar en nevera: <input type='checkbox' name='neve' value='on' class='texto2'></td>";
			}
		}
				
		

		if($nutri=='off')
		{
			echo "<td class='texto2' colspan='1' align='left'>Descripcion:<select name='des' class='texto5'>";
			for ($i=0;$i<count($dess);$i++)
			{
				echo "<option>".$dess[$i]."</option>";
			}
			echo "</select></td>";
		}
		else
		{
			echo "<td class='texto2' colspan='1' align='left'>&nbsp;</td></tr>";
		}
	}
		
	switch($estado)
	{
		case 'inicio':
		// if($nutri=='off')
		if($nutri=='off' && (($esDosisAdaptada=='off')||($esDosisAdaptada=='on' && $pintarListaDAPendientes!=null)))
		{
			echo "<tr><td colspan=3 class='titulo3' align='center'><input type='checkbox' name='crear' class='titulo3'>Crear &nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Aceptar' class='texto5'></td></tr>";
		}
		break;
		case 'creado':
		if($nutri=='off')
		{
			$linkCrearLote = "<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1'>";
			if($DA_historia!="")
			{
				$linkCrearLote = "<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1&whistoria=".$DA_historia."&wingreso=".$DA_ingreso."&warticuloda=".$DA_articulo."&idoda=".$DA_ido."&wronda=".$wronda."&wfecharonda=".$wfecharonda."'>";
			}
			
			echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO HA SIDO CREADO EXITOSAMENTE &nbsp;&nbsp;".$linkCrearLote."/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></tr>";
			
			// echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO HA SIDO CREADO EXITOSAMENTE &nbsp;&nbsp;<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1'>/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></tr>";
		}
		else
		{
			$rotuloNPT = "<a href='rotulo2.php?historia=".$historia."&codigo=".$productos[0]['cod']."&horas=$tfd&insti=".$instituciones[0]['cod']."' target='new'>";
			if($instituciones[0]['cod']==$wemp_pmla && $NPT_origen=="ordenes")
			{
				$rotuloNPT = "<a href='rotuloNPT.php?wemp_pmla=".$wemp_pmla."&historia=".$historia."&codigo=".$productos[0]['cod']."&horas=$tfd&insti=".$instituciones[0]['cod']."' target='new'>";
			}
			echo "<tr><td colspan=8 class='titulo3' align='center'><span class='blinkProdCreado'>EL PRODUCTO HA SIDO CREADO EXITOSAMENTE</span> &nbsp;&nbsp;<a href='#' onclick='enter5()'>/COPIAR</a>&nbsp;&nbsp;<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1'>/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></tr>";
			// echo "<tr><td colspan=8 class='titulo3' align='center'>".$rotuloNPT."GENERAR ROTULO</a></td></tr>";
			echo "<tr><td colspan=8 class='titulo3' align='center'>".$rotuloNPT."IMPRIMIR ROTULOS</a></td></tr>";
			// echo "<tr><td colspan=7 class='titulo3' align='center'><a href='rotulo2.php?historia=".$historia."&codigo=".$productos[0]['cod']."&horas=$tfd&insti=".$instituciones[0]['cod']."' target='new'>GENERAR ROTULO</a></td></tr>";
			
			//Desactivar boton crear
			echo"<script>desactivarBotonCrear();</script>";
		}
		break;
		case 'desactivado':
		if($nutri=='off')
		{
			echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO HA SIDO DESACTIVADO EXITOSAMENTE &nbsp;&nbsp;<a href='cen_mez.php'>INICIAR</a> </td></tr>";
		}
		else
		{
			echo "<tr><td colspan=8 class='titulo3' align='center'><span class='blinkProdCreado'>EL PRODUCTO HA SIDO DESACTIVADO EXITOSAMENTE</span> &nbsp;&nbsp;<a href='cen_mez.php'>INICIAR</a> </td></tr>";
			
			//Desactivar boton crear
			echo"<script>desactivarBotonCrear();</script>";
		}
		
		break;
		case 'modificado':
		if($nutri=='off')
		{
			echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO HA SIDO MODIFICADO EXITOSAMENTE &nbsp;&nbsp;<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1'>/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></td></tr>";
		}
		else
		{
			//consultar origen
			$qOrigenOrdenes = "SELECT * 
								 FROM ".$bd."_000214 
								WHERE Enuhis='".$historia."' 
								  AND Enucnu='".$productos[0]['cod']."' 
								  AND Enuord='on' 
								  AND Enurea='on' 
								  AND Enuest='on';";
								  
			$resOrigenOrdenes = mysql_query($qOrigenOrdenes,$conex);
			$numOrigenOrdenes = mysql_num_rows($resOrigenOrdenes);					  
			
			$rotuloNPT = "<a href='rotulo2.php?historia=".$historia."&codigo=".$productos[0]['cod']."&horas=$tfd&insti=".$instituciones[0]['cod']."' target='new'>";
			if($numOrigenOrdenes>0)
			{
				$rotuloNPT = "<a href='rotuloNPT.php?wemp_pmla=".$wemp_pmla."&historia=".$historia."&codigo=".$productos[0]['cod']."&horas=$tfd&insti=".$instituciones[0]['cod']."' target='new'>";
			}
			echo "<tr><td colspan=8 class='titulo3' align='center'><span class='blinkProdCreado'>EL PRODUCTO HA SIDO MODIFICADO EXITOSAMENTE</span> &nbsp;&nbsp;<a href='#' onclick='enter5()'>/COPIAR</a>&nbsp;&nbsp;<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1'>/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></td></tr>";
			echo "<tr><td colspan=8 class='titulo3' align='center'>".$rotuloNPT."IMPRIMIR ROTULOS</a></td></tr>";
			// echo "<tr><td colspan=7 class='titulo3' align='center'><a href='rotulo2.php?historia=".$historia."&codigo=".$productos[0]['cod']."&horas=$tfd&insti=".$instituciones[0]['cod']."' target='new'>GENERAR ROTULO</a></td></tr>";
			
			//Desactivar boton crear
			echo"<script>desactivarBotonCrear();</script>";
		}
		break;
		case 'Creado':
		if($nutri=='off')
		{
			$linkCrearLote = "<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1'>";
			if($esDosisAdaptada=="on")
			{
				if($DA_historia!="")
				{
					$linkCrearLote = "<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1&whistoria=".$DA_historia."&wingreso=".$DA_ingreso."&warticuloda=".$DA_articulo."&idoda=".$DA_ido."&wronda=".$wronda."&wfecharonda=".$wfecharonda."'>";
					// echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO EXISTE ACTUALMENTE &nbsp;&nbsp;<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1'>/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></tr>";
					echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO EXISTE ACTUALMENTE &nbsp;&nbsp;".$linkCrearLote."/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></tr>";
				}
				else
				{
					// echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO EXISTE ACTUALMENTE </td></tr>";
					echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO EXISTE ACTUALMENTE &nbsp;&nbsp;<a href='#' onclick='enter3()'>MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></tr>";
				}
			}
			else
			{
				echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO EXISTE ACTUALMENTE &nbsp;&nbsp;".$linkCrearLote."/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></tr>";
			}
		}
		else
		{
			
			//consultar origen
			$qOrigenOrdenes = "SELECT * 
								 FROM ".$bd."_000214 
								WHERE Enuhis='".$historia."' 
								  AND Enucnu='".$productos[0]['cod']."' 
								  AND Enuord='on' 
								  AND Enurea='on' 
								  AND Enuest='on';";
								  
			$resOrigenOrdenes = mysql_query($qOrigenOrdenes,$conex);
			$numOrigenOrdenes = mysql_num_rows($resOrigenOrdenes);					  
			
			$rotuloNPT = "<a href='rotulo2.php?historia=".$historia."&codigo=".$productos[0]['cod']."&horas=$tfd&insti=".$instituciones[0]['cod']."' target='new'>";
			
			if($numOrigenOrdenes>0)
			{
				$rotuloNPT = "<a href='rotuloNPT.php?wemp_pmla=".$wemp_pmla."&historia=".$historia."&codigo=".$productos[0]['cod']."&horas=$tfd&insti=".$instituciones[0]['cod']."' target='new'>";
			}
			
			echo "<tr><td colspan=8 class='titulo3' align='center'><span class='blinkProdCreado'>EL PRODUCTO EXISTE ACTUALMENTE</span> &nbsp;&nbsp;<a href='#' onclick='enter5()'>/COPIAR</a>&nbsp;&nbsp;<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1'>/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></tr>";
			echo "<tr><td colspan=8 class='titulo3' align='center'>".$rotuloNPT."IMPRIMIR ROTULOS</a></td></tr>";
			// echo "<tr><td colspan=7 class='titulo3' align='center'><a href='rotulo2.php?historia=".$historia."&codigo=".$productos[0]['cod']."&horas=$tfd&insti=".$instituciones[0]['cod']."' target='new'>GENERAR ROTULO</a></td></tr>";
			
			//Desactivar boton crear
			echo"<script>desactivarBotonCrear();</script>";
		}
		break;
		case 'Desactivado':
		echo "<tr><td colspan=3 class='titulo3' align='center'><span class='blinkProdCreado'>PRODUCTO DESACTIVADO</span> &nbsp;&nbsp;<a href='cen_mez.php'>INICIAR</a> </td></tr>";
		break;

	}
	echo "<input type='hidden' name='pintarListaNPTPendientes' id='pintarListaNPTPendientes' value=''>";
	echo "<input type='hidden' name='estado' value='".$estado."'></td>";
	echo "<input type='hidden' name='tvh' value='0'></td>";
	echo "<input type='hidden' name='nutri' value='".$nutri."'></td>";
	echo "<input type='hidden' name='origenNPT' value='".$NPT_origen."'></td>";
	
	if(isset($pintarListaDAPendientes))
	{
		echo "<input type='hidden' name='pintarListaDAPendientes' id='pintarListaDAPendientes' value='".$pintarListaDAPendientes."'>";
	}
	else
	{
		echo "<input type='hidden' name='pintarListaDAPendientes' id='pintarListaDAPendientes' value=''>";
	}
	
	echo "</table></br>";
	
}

/**
 * Pinta la lista de insumos para el producto
 *
 * @param unknown_type $insumos, vector de insumos encontrados para dorp down
 * @param unknown_type $inslis, vector de insumos que se han ingresado para el prodcuto
 * @param unknown_type $compu, si es compuesto el nombre o no
 */
function pintarInsumos($insumos, $inslis, $compu, $nutri, $peso, $purga, $estado, $vol,$tfd)
{
	global $conex;
	global $wbasedato;
	
	$esDosisAdaptada = consultarSiTipoEsDA($tipos[0]);
	
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan=5 class='titulo3' align='center'><b>Informacion detallada del Producto</b></td></tr>";


	echo "<tr><td class='texto1' colspan='5' align='center'>Buscar Insumo por: ";
	echo "<select name='forbus2' class='texto5'>";
	echo "<option>Codigo</option>";
	echo "<option>Nombre comercial</option>";
	echo "<option>Nombre generico</option>";
	if ($nutri=='on' and $estado!='inicio')
	{
		echo "</select><input type='TEXT' name='parbus2' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Buscar' class='texto5'></td> ";
	}
	else
	{
		echo "</select><input type='TEXT' name='parbus2' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter1()' class='texto5'></td> ";
	}
	echo "<tr><td class='texto1' colspan='2' align='center'>Insumo: <select name='insumo' class='texto5' onchange='enter1()'>";
	if ($insumos!='')
	{
		for ($i=0;$i<count($insumos);$i++)
		{
			echo "<option value='".$insumos[$i]['cod']."-".$insumos[$i]['nom']."-".$insumos[$i]['gen']."-".$insumos[$i]['pre']."'>".$insumos[$i]['cod']."-".$insumos[$i]['nom']."</option>";
		}
	}
	else
	{
		echo "<option ></option>";
	}
	echo "</select></td>";

	$cant = "";
	if (is_array($insumos))
	{	$cant = $insumos[0]['pre']; }

	if($peso=='' or $peso==0 )
	{
		echo "<td class='texto1' colspan='3' align='center'>Cantidad: <input type='TEXT' name='cantidad' value=''  class='texto5' onchange='validarFormulario4()'><input type='TEXT' name='nompro' value='".$cant."'  class='texto5' >";
	}
	else
	{
		echo "<td class='texto1' colspan='3' align='center'>Requerimiento: <input type='TEXT' name='cantidad' value=''  class='texto5' onchange='validarFormulario4()'><input type='hidden' name='nompro' value='".$cant."'  class='texto5' >";
	}
	echo "</td></tr>";
	echo "<tr><td colspan=5 class='texto1' align='center'><INPUT TYPE='button' NAME='buscar' VALUE='Agregar' onclick='enter()' class='texto5'></td></tr>";
	echo "<tr><td colspan=5 class='titulo3' align='center'>&nbsp</td></tr>";


	if ($inslis!='')
	{
		if ($compu=='on')
		{
			echo "<tr><td class='texto2' colspan='1' align='center'>Parte del nombre</td>";
			echo "<td class='texto2' colspan='1' align='center'>Insumo</td>";
		}
		else
		{
			echo "<tr><td class='texto2' colspan='2' align='center'>Insumo</td>";
		}

		if ($nutri=='off')
		{
			echo "<td class='texto2' colspan='1' align='center'>Cantidad</td>";
			echo "<td class='texto2' colspan='1' align='center'>Eliminar</td></tr>";
		}
		else
		{
			echo "<td class='texto2' colspan='1' align='center'>Factor</td>";
			echo "<td class='texto2' colspan='1' align='center'>Requerimiento / Consumo</td>";
			
			echo "<td class='texto2' colspan='1' align='center' width=10%>Aplicar Purga</td>";
		}
		$cadenaInsumosNPTSinFraccion="";
		$voltol=0;
		
		// for ($i=0;$i<count($inslis);$i++)
		foreach ($inslis as $i => $value)
		{
			if (is_int($i/2))
			{
				$class='texto3';
			}
			else
			{
				$class='texto4';
			}

			if ($compu=='on')
			{
				echo "<tr><td class='".$class."' colspan='1' align='center'><input type='checkbox' name='inslis[".$i."][pri]' ".$inslis[$i]['pri']." class='texto3' onclick='enter()'></td>";
				echo "<td class='".$class."' colspan='1' align='center'>".$inslis[$i]['cod']."-".$inslis[$i]['nom']."<input type='hidden' name='inslis[".$i."][cod]' value='".$inslis[$i]['cod']."'><input type='hidden' name='inslis[".$i."][nom]' value='".$inslis[$i]['nom']."'><input type='hidden' name='inslis[".$i."][gen]' value=".$inslis[$i]['gen']."></td>";
			}
			else
			{
				echo "<tr><td class='".$class."' colspan='2' align='center'>".$inslis[$i]['cod']."-".$inslis[$i]['nom']."<input type='hidden' name='inslis[".$i."][cod]' value='".$inslis[$i]['cod']."'><input type='hidden' name='inslis[".$i."][nom]' value='".$inslis[$i]['nom']."'><input type='hidden' name='inslis[".$i."][gen]' value=".$inslis[$i]['gen']."></td>";
				echo "<input type='hidden' name='inslis[".$i."][pri]'  value='checked'></td>";
			}

			if($nutri=='off')
			{
				echo "<td class='".$class."' colspan='1' align='center'><input type='TEXT' name='inslis[".$i."][can]' value='".$inslis[$i]['can']."'  class='texto3' size='5'><input type='TEXT' name='inslis[".$i."][pre]' value='".$inslis[$i]['pre']."'  class='texto3' size='20' readonly='readonly'></td>";
				echo "<td class='".$class."' colspan='1' align='center'><input type='checkbox' name='eli' class='texto3' onclick='enter2(".$i.")'></td></tr>";
			}
			else
			{
				if ($peso=='' or $inslis[$i]['ppe']!='on' or $peso==0 )
				{
					$pe=1;
					if( $peso == '' || $peso == 0 ){
						$peso = 1;
					}
				}
				else
				{
					$pe=$peso;
				}

				if($purga=='')
				{
					$pur=0;
				}
				else
				{
					$pur=$purga;
				}
				
				$volnp = consultarVolNoPurgar($inslis, $vol, $peso);
				
				if( $volnp == $vol )
					$volnp = 0;

				if ($inslis[$i]['fac']!=1)
				{
					$q= " SELECT Facval, Facdes "
					."       FROM ".$wbasedato."_000015 "
					."    WHERE Facart = '".$inslis[$i]['cod']."' "
					."       AND Facest = 'on' "
					."       AND Facval = '".((float)$inslis[$i]['fac']*$pe)."' "
					."    Order by Faccod ";

					$resp = mysql_query($q,$conex);
					$nump = mysql_num_rows($resp);

					if ($nump>0)
					{
						$factor=(float)$inslis[$i]['fac']*$pe;
					}
					else
					{
						$factor=(float)$inslis[$i]['fac'];
					}

					$q= " SELECT Facval, Facdes, Facpes, Facreq "
					."       FROM ".$wbasedato."_000015 "
					."    WHERE Facart = '".$inslis[$i]['cod']."' "
					."       AND Facest = 'on' "
					."       AND Facval <> '".$factor."' "
					."    Order by Faccod ";

					$res = mysql_query($q,$conex);
					$num = mysql_num_rows($res);

					if ($num>0)
					{

						$q= " SELECT Facval, Facdes, Facpes, Facreq "
						."       FROM ".$wbasedato."_000015 "
						."    WHERE Facart = '".$inslis[$i]['cod']."' "
						."       AND Facest = 'on' "
						."       AND Facval = '".$factor."' ";

						$res1 = mysql_query($q,$conex);
						$num1 = mysql_num_rows($res1);
						$row1 = mysql_fetch_array($res1);

						echo "<td class='".$class."' colspan='1' align='center' ><select name='inslis[".$i."][fac]' class='texto5' onchange='enter1()'>";
						if($num1>0)
						{
							$valorFactor = (float)$row1[0];
							if($row1['Facreq']!="")
							{
								$multiplicaPorTiempoInfusion = consultarSiMultiplicaPorTiempoInfusion($row1['Facreq']);
								
								if($multiplicaPorTiempoInfusion)
								{
									$valorFactor = $row1[0]*$tfd;
									$inslis[$i]['fac']=$valorFactor;
								}
							}
							
							if($row1[2]=='on')
							{
								// echo "<option value='".$row1[0]."'>".round($row1[0],10)."-".$row1[1]."</option>";
								echo "<option value='".$valorFactor."'>".round($row1[0],10)."-".$row1[1]."</option>";
							}
							else
							{
								// echo "<option value='".($row1[0]/$pe)."'>".round($row1[0],10)."-".$row1[1]."</option>";
								echo "<option value='".($valorFactor/$pe)."'>".round($row1[0],10)."-".$row1[1]."</option>";
							}
						}
						
						for ($j=0;$j<$num;$j++)
						{
							$row = mysql_fetch_array($res);
							
							$valorFactor = $row[0];
							if($row['Facreq']!="")
							{
								$multiplicaPorTiempoInfusion = consultarSiMultiplicaPorTiempoInfusion($row['Facreq']);
								
								if($multiplicaPorTiempoInfusion)
								{
									$valorFactor = $row[0]*$tfd;
									$inslis[$i]['fac']=$valorFactor;
								}
							}
							
							if($row[2]=='on')
							{
								// echo "<option value='".$row[0]."'>".round($row[0],10)."-".$row[1]."</option>";
								echo "<option value='".$valorFactor."'>".round($row[0],10)."-".$row[1]."</option>";
							}
							else
							{
								// echo "<option value='".($row[0]/$pe)."'>".round($row[0],10)."-".$row[1]."</option>";
								echo "<option value='".($valorFactor/$pe)."'>".round($row[0],10)."-".$row[1]."</option>";
							}
						}
						echo "</select></td>";
					}
					else
					{
						echo "<td class='".$class."' colspan='1' align='center'>&nbsp;</td>";
						echo "<input type='hidden' name='inslis[".$i."][fac]' value='".$factor."'></td>";
					}
				}
				else
				{
					echo "<td class='".$class."' colspan='1' align='center'>&nbsp;</td>";
					echo "<input type='hidden' name='inslis[".$i."][fac]' value='".$inslis[$i]['fac']."'></td>";
				}
				
				echo "<td class='".$class."' colspan='1' align='center'><input type='TEXT' name='inslis[".$i."][can]' value='".$inslis[$i]['can']."'  class='texto3' size='5'>&nbsp;/&nbsp;";
				if($inslis[$i]['can']>0 and $inslis[$i]['can']!='')
				{
					if( ($inslis[$i]['mat']!='on' && $inslis[$i]['app']=='on' ))
					{	
						echo round(($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac'])+($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac'])*$pur/($vol-$volnp),2)." ".$inslis[$i]['pre']."</td>";
						$voltol=round($voltol+($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac'])+(($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac'])*$pur)/($vol-$volnp),2);
						
						if(round(($inslis[$i]['can']*$pe*$inslis[$i]['fac'])+($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac'])*$pur/($vol-$volnp),2)==0)
						{
							$cadenaInsumosNPTSinFraccion .= $inslis[$i]['cod']." - ".$inslis[$i]['nom']."\n";
						}
						
					}else if($inslis[$i]['mat']!='on'&& $inslis[$i]['app']!='on'){
						
						echo round(($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac']),2)." ".$inslis[$i]['pre']."</td>";
						$voltol=round($voltol+($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac']),2);
						
						if(round(($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac']),2)==0)
						{
							$cadenaInsumosNPTSinFraccion .= $inslis[$i]['cod']." - ".$inslis[$i]['nom']."\n";
						}
					}
					else
					{
						echo $inslis[$i]['can']."&nbsp;".$inslis[$i]['pre']."</td>";
						
						if($inslis[$i]['can']==0)
						{
							$cadenaInsumosNPTSinFraccion .= $inslis[$i]['cod']." - ".$inslis[$i]['nom']."\n";
						}
					}

				}
				else
				{
					echo "</td>";
				}
				
				if( $inslis[$i]['app'] == 'on' ){
//					echo "<td align=center class='".$class."'><input type='checkbox' name='inslis[".$i."][app]' checked='{$inslis[$i]['app']}' onclick='javascript:cambiarValue( this, $i );'></td></tr>";
					echo "<td align=center class='".$class."'><input type='checkbox' name='cb$i' checked='{$inslis[$i]['app']}' onclick='javascript:cambiarValue( this, $i );'></td></tr>";
					$val='on';
				}
				else
				{
//					echo "<td align=center class='".$class."'><input type='checkbox' name='inslis[".$i."][app]' onclick='javascript:cambiarValue( this, $i );'></td></tr>";
					echo "<td align=center class='".$class."'><input type='checkbox' name='cb$i' onclick='javascript:cambiarValue( this, $i );'></td></tr>";
					$val='off';
				}

				echo "<input type='hidden' name='inslis[".$i."][pre]' value='".$inslis[$i]['pre']."'></td>";
				echo "<input type='hidden' name='inslis[".$i."][ppe]' value='".$inslis[$i]['ppe']."'></td>";
				echo "<input type='hidden' name='inslis[".$i."][mat]' value='".$inslis[$i]['mat']."'></td>";
				echo "<input type='hidden' id='inslis[".$i."][app]' name='inslis[".$i."][app]' value='$val'></td>";
				echo "<input type='hidden' name='vol' value='".$vol."'></td>";
				
				echo "<input type='hidden' name='inslis[".$i."][ant]' value='on'></td>";
			}

		}

		if($nutri=='on')
		{
			echo "<tr><td colspan='4' align='center'><font color='red' >Volumen total: ".$voltol."</font></td></tr>";
			
			if($cadenaInsumosNPTSinFraccion!="")
			{
				echo "<br><br><tr><td colspan='4' align='center'><b>Debe configurar los siguientes insumos en cenpro_000015 para poder crear la NPT: <br>".$cadenaInsumosNPTSinFraccion."</b></td></tr>";
				echo"<script>$('#checkCrearNPT').attr('disabled',true);</script>";
			}
		}

	}

	echo "<input type='hidden' name='accion' value='0'></td>";
	echo "<input type='hidden' name='realizar' value='0'></td>";
	echo "<input type='hidden' name='eliminar' value='0'></td>";
	echo "</table></form>";
	
}


function pintarInsumosNPT($insumos, $inslis, $compu, $nutri, $peso, $purga, $estado, $vol, $NPT_historia, $NPT_ingreso, $NPT_articulo, $NPT_ido,$hacerSubmitInsumosNPT,$tfd)
{
	global $conex;
	global $wbasedato;
	global $bd;
	global $wemp_pmla;
	
	if(isset($hacerSubmitInsumosNPT))
	{
		echo "<input type='hidden' id='hacerSubmitInsumosNPT' name='hacerSubmitInsumosNPT' value='off'>";
	}
	else
	{
		echo "<input type='hidden' id='hacerSubmitInsumosNPT' name='hacerSubmitInsumosNPT' value='on'>";
	}
	
	$rangoEtario = consultarRangoEtario($NPT_historia);
	
	if($rangoEtario=="N")
	{
		$codigoEquiposNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoEquiposNPTneonatos" );
	}
	else
	{
		$codigoEquiposNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoEquiposNPT" );
	}
	
	$codigoAguaNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoAguaNPT" );
	// $codigoEquiposNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoEquiposNPT" );
	$codigoBolsasNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoBolsasNPT" );
	
	
	$codigosAguaNPT = explode(",",$codigoAguaNPT);
	$codigosEquiposNPT = explode(",",$codigoEquiposNPT);
	$codigosBolsasNPT = explode(",",$codigoBolsasNPT);
		
	echo "<table border=0 ALIGN=CENTER width=90% id='tablaPrescripcionesNPT'>";
	echo "<tr><td colspan=5 class='titulo3' align='center'><b>Informacion detallada del Producto</b></td></tr>";


	if ($inslis!='')
	{
		if ($compu=='on')
		{
			echo "<tr><td class='texto2' colspan='1' align='center'>Parte del nombre</td>";
			echo "<td class='texto2' colspan='1' align='center'>Insumo</td>";
		}
		else
		{
			echo "<tr><td class='texto2' colspan='2' align='center'>Insumo</td>";
		}

		echo "<td class='texto2' colspan='1' align='center'>Factor</td>";
		echo "<td class='texto2' colspan='1' align='center'>Requerimiento / Consumo</td>";
		
		echo "<td class='texto2' colspan='1' align='center' width=10%>Aplicar Purga</td>";
		
		$qInsumosOrdenados = "SELECT Dnuhis,Dnuing,Dnuart,Dnuido,Dnucod,Dnupre,Insreq,Requni,Reqmti
								FROM ".$bd."_000215,".$bd."_000210,".$bd."_000212 
							   WHERE Dnuhis='".$NPT_historia."' 
							     AND Dnuing='".$NPT_ingreso."' 
								 AND Dnuart='".$NPT_articulo."' 
								 AND Dnuido='".$NPT_ido."'
								 AND Dnuest='on'
								 AND Dnuest='on' 
								 AND Inscod = Dnucod
								 AND Insreq = Reqcod;";
		
		$resInsumosOrdenados = mysql_query($qInsumosOrdenados, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qInsumosOrdenados . " - " . mysql_error());
		$numInsumosOrdenados = mysql_num_rows($resInsumosOrdenados);
		
		$contInsOrd=0;
		$arrayInsumosOrdenado = array();
		if($numInsumosOrdenados>0)
		{
			while($rowsInsumosOrdenados = mysql_fetch_array($resInsumosOrdenados))
			{
				$arrayInsumosOrdenado[$contInsOrd]['Dnuhis'] = $rowsInsumosOrdenados['Dnuhis'];
				$arrayInsumosOrdenado[$contInsOrd]['Dnuing'] = $rowsInsumosOrdenados['Dnuing'];
				$arrayInsumosOrdenado[$contInsOrd]['Dnuart'] = $rowsInsumosOrdenados['Dnuart'];
				$arrayInsumosOrdenado[$contInsOrd]['Dnuido'] = $rowsInsumosOrdenados['Dnuido'];
				$arrayInsumosOrdenado[$contInsOrd]['Dnucod'] = $rowsInsumosOrdenados['Dnucod'];
				$arrayInsumosOrdenado[$contInsOrd]['Dnupre'] = $rowsInsumosOrdenados['Dnupre'];
				$arrayInsumosOrdenado[$contInsOrd]['Insreq'] = $rowsInsumosOrdenados['Insreq'];
				$arrayInsumosOrdenado[$contInsOrd]['Requni'] = $rowsInsumosOrdenados['Requni'];
				$arrayInsumosOrdenado[$contInsOrd]['Reqmti'] = $rowsInsumosOrdenados['Reqmti'];
				
				$contInsOrd++;
			}
		}
		
		//Agua esteril
		for($i=0;$i<count($codigosAguaNPT);$i++)
		{
			$arrayInsumosOrdenado[$contInsOrd]['Dnucod'] = $codigosAguaNPT[$i];
			
			$contInsOrd++;
		}
		
		//Equipos NPT
		for($i=0;$i<count($codigosEquiposNPT);$i++)
		{
			$arrayInsumosOrdenado[$contInsOrd]['Dnucod'] = $codigosEquiposNPT[$i];
			$arrayInsumosOrdenado[$contInsOrd]['Dnupre'] = 1;
			
			$contInsOrd++;
		}
		
		//Bolsas NPT
		for($i=0;$i<count($codigosBolsasNPT);$i++)
		{
			$arrayInsumosOrdenado[$contInsOrd]['Dnucod'] = $codigosBolsasNPT[$i];
			
			$contInsOrd++;
		}
		
		$cadenaInsumosNPTSinFraccion="";
		$voltol=0;
		// for ($i=0;$i<count($inslis);$i++)
		foreach ($inslis as $i => $value)
		{
			if(count($arrayInsumosOrdenado)>0)
			{
				for($j=0;$j<count($arrayInsumosOrdenado);$j++)
				{
					if($inslis[$i]['cod']==$arrayInsumosOrdenado[$j]['Dnucod'])
					{
						
						if($class == 'texto4'){
							$class = 'texto3';
						} else {
							$class = 'texto4';
						}
						
						if ($compu=='on')
						{
							echo "<tr><td class='".$class."' colspan='1' align='center'><input type='checkbox' name='inslis[".$i."][pri]' ".$inslis[$i]['pri']." class='texto3' onclick='enter()'></td>";
							echo "<td class='".$class."' colspan='1' align='center'>".$inslis[$i]['cod']."-".$inslis[$i]['nom']."<input type='hidden' name='inslis[".$i."][cod]' value='".$inslis[$i]['cod']."'><input type='hidden' name='inslis[".$i."][nom]' value='".$inslis[$i]['nom']."'><input type='hidden' name='inslis[".$i."][gen]' value=".$inslis[$i]['gen']."></td>";
						}
						else
						{
							echo "<tr><td class='".$class."' colspan='2' align='center'>".$inslis[$i]['cod']."-".$inslis[$i]['nom']."<input type='hidden' name='inslis[".$i."][cod]' value='".$inslis[$i]['cod']."'><input type='hidden' name='inslis[".$i."][nom]' value='".$inslis[$i]['nom']."'><input type='hidden' name='inslis[".$i."][gen]' value=".$inslis[$i]['gen']."></td>";
							echo "<input type='hidden' name='inslis[".$i."][pri]'  value='checked'></td>";
						}
						
						if ($peso=='' or $inslis[$i]['ppe']!='on' or $peso==0 )
						{
							$pe=1;
							if( $peso == '' || $peso == 0 ){
								$peso = 1;
							}
						}
						else
						{
							$pe=$peso;
						}

						if($purga=='')
						{
							$pur=0;
						}
						else
						{
							$pur=$purga;
						}
						
						$volnp = consultarVolNoPurgar($inslis, $vol, $peso);
						
						if( $volnp == $vol )
							$volnp = 0;
					// -------------
						if ($inslis[$i]['fac']!=1)
						{
							$q= " SELECT Facval, Facdes "
							."       FROM ".$wbasedato."_000015 "
							."    WHERE Facart = '".$inslis[$i]['cod']."' "
							."       AND Facest = 'on' "
							."       AND Facval = '".((float)$inslis[$i]['fac']*$pe)."' "
							."    Order by Faccod ";

							$resp = mysql_query($q,$conex);
							$nump = mysql_num_rows($resp);

							if ($nump>0)
							{
								$factor=(float)$inslis[$i]['fac']*$pe;
							}
							else
							{
								$factor=(float)$inslis[$i]['fac'];
							}

							$q= " SELECT Facval, Facdes, Facpes "
							."       FROM ".$wbasedato."_000015 "
							."    WHERE Facart = '".$inslis[$i]['cod']."' "
							."       AND Facest = 'on' "
							."       AND Facval <> '".$factor."' "
							."    Order by Faccod ";

							$res = mysql_query($q,$conex);
							$num = mysql_num_rows($res);

							if ($num>0)
							{
								$q= " SELECT Facval, Facdes, Facpes, Facreq,Requni 
										FROM ".$wbasedato."_000015,".$bd."_000210,".$bd."_000212 
									   WHERE Facart = '".$inslis[$i]['cod']."' 
									     AND Facest = 'on' 
										 AND Facreq = Reqcod 
										 AND Inscod=Facart 
										 AND Insreq=Facreq;";

										 
								$resFactor = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
								$numFactor = mysql_num_rows($resFactor);
								
								if($numFactor>0)
								{
									$rowsFactor = mysql_fetch_array($resFactor);
									
									
									if($rowsFactor['Facpes']=='on')
									{
										echo "<td class='".$class."' colspan='1' align='center'>". $arrayInsumosOrdenado[$j]['Requni']."</td>";
										echo "<input type='hidden' name='inslis[".$i."][fac]' value='".$inslis[$i]['fac']."'></td>";
									}
									else
									{
										echo "<td class='".$class."' colspan='1' align='center'>". $arrayInsumosOrdenado[$j]['Requni']."</td>";
										echo "<input type='hidden' name='inslis[".$i."][fac]' value='".$inslis[$i]['fac']/$pe."'></td>";
									}
								}
							}
							else
							{
								echo "<td class='".$class."' colspan='1' align='center'>". $arrayInsumosOrdenado[$j]['Requni']."</td>";
								echo "<input type='hidden' name='inslis[".$i."][fac]' value='".$factor."'></td>";
							}
						}
						else
						{
							echo "<td class='".$class."' colspan='1' align='center'>". $arrayInsumosOrdenado[$j]['Requni']."</td>";
							echo "<input type='hidden' name='inslis[".$i."][fac]' value='".$inslis[$i]['fac']."'></td>";
						}
						
					// -------------	
						
						if($inslis[$i]['can']!="")
						{
							echo "<td class='".$class."' colspan='1' align='center'><input type='TEXT' id='prescripcionNPT_".$arrayInsumosOrdenado[$j]['Dnucod']."' name='inslis[".$i."][can]' value='".$inslis[$i]['can']."'  class='texto3' size='5'>&nbsp;/&nbsp;";
						}
						else
						{
							echo "<td class='".$class."' colspan='1' align='center'><input type='TEXT' id='prescripcionNPT_".$arrayInsumosOrdenado[$j]['Dnucod']."' name='inslis[".$i."][can]' value='".$arrayInsumosOrdenado[$j]['Dnupre']."'  class='texto3' size='5'>&nbsp;/&nbsp;";
							$inslis[$i]['can']=$arrayInsumosOrdenado[$j]['Dnupre'];
						}
						
						if($arrayInsumosOrdenado[$j]['Reqmti']=="on")
						{
							$tiempoInfusion = $tfd;
						}
						else
						{
							$tiempoInfusion = 1;
						}
						
						if($inslis[$i]['can']>0 and $inslis[$i]['can']!='')
						{
							if( ($inslis[$i]['mat']!='on' && $inslis[$i]['app']=='on' ))
							{	
								echo round(($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac'])+($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac'])*$pur/($vol-$volnp),2)." ".$inslis[$i]['pre']."</td>";
								$voltol=round($voltol+($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac'])+(($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac'])*$pur)/($vol-$volnp),2);
								
								if(round(($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac'])+($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac'])*$pur/($vol-$volnp),2)==0)
								{
									$cadenaInsumosNPTSinFraccion .= $inslis[$i]['cod']." - ".$inslis[$i]['nom']."\n";
								}
							}
							else if($inslis[$i]['mat']!='on'&& $inslis[$i]['app']!='on')
							{
								echo round(($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac']),2)." ".$inslis[$i]['pre']."</td>";
								$voltol=round($voltol+($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac']),2);
								
								if(round(($inslis[$i]['can']*$pe*(float)$inslis[$i]['fac']),2)==0)
								{
									$cadenaInsumosNPTSinFraccion .= $inslis[$i]['cod']." - ".$inslis[$i]['nom']."\n";
								}
							}
							else
							{
								
								echo $inslis[$i]['can']."&nbsp;".$inslis[$i]['pre']."</td>";
								if(round($inslis[$i]['can'])==0)
								{
									$cadenaInsumosNPTSinFraccion .= $inslis[$i]['cod']." - ".$inslis[$i]['nom']."\n";
								}
							}

						}
						else
						{
							echo "</td>";
						}
						
						if( $inslis[$i]['app'] == 'on' ){
							echo "<td align=center class='".$class."'><input type='checkbox' name='cb$i' checked='{$inslis[$i]['app']}' onclick='javascript:cambiarValue( this, $i );'></td></tr>";
							$val='on';
						}
						else
						{
							echo "<td align=center class='".$class."'><input type='checkbox' name='cb$i' onclick='javascript:cambiarValue( this, $i );'></td></tr>";
							$val='off';
						}

						echo "<input type='hidden' name='inslis[".$i."][pre]' value='".$inslis[$i]['pre']."'></td>";
						echo "<input type='hidden' name='inslis[".$i."][ppe]' value='".$inslis[$i]['ppe']."'></td>";
						echo "<input type='hidden' name='inslis[".$i."][mat]' value='".$inslis[$i]['mat']."'></td>";
						echo "<input type='hidden' id='inslis[".$i."][app]' name='inslis[".$i."][app]' value='$val'></td>";
						echo "<input type='hidden' name='vol' value='".$vol."'></td>";
						
						echo "<input type='hidden' name='inslis[".$i."][ant]' value='on'></td>";

					}
				}
			}
		}
		
		if($nutri=='on')
		{
			echo "<tr><td colspan='4' align='center'><font color='red' >Volumen total: ".$voltol."</font></td></tr>";
			
			if($cadenaInsumosNPTSinFraccion!="")
			{
				echo "<br><br><tr><td colspan='4' align='center'><b>Debe configurar los siguientes insumos en cenpro_000015 para poder crear la NPT: <br>".$cadenaInsumosNPTSinFraccion."</b></td></tr>";
				echo"<script>$('#checkCrearNPT').attr('disabled',true);</script>";
			}
		}

	}
	
	echo "<input type='hidden' name='accion' value='0'></td>";
	echo "<input type='hidden' name='realizar' value='0'></td>";
	echo "<input type='hidden' name='eliminar' value='0'></td>";
	echo "</table></br></form>";
}

function marcarNPTRealizada($NPT_historia,$NPT_ingreso,$NPT_articulo,$NPT_ido,$codigoNutricion)
{
	global $conex;
	global $wbasedato;
	global $bd;
	global $wemp_pmla;
	
	
	$qUpdateRealizada = "UPDATE ".$bd."_000214 
							SET Enurea='on',
								Enucnu='".$codigoNutricion."'
						  WHERE Enuhis='".$NPT_historia."' 
							AND Enuing='".$NPT_ingreso."' 
							AND Enuart='".$NPT_articulo."' 
							AND Enuido='".$NPT_ido."' 
							AND Enuest='on';";
	
	$resultadoUpdateRealizada = mysql_query($qUpdateRealizada,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qUpdateRealizada." - ".mysql_error());
				
}

function guardarPrescripcionNPTCentralMezclas($NPT_historia,$NPT_ingreso,$NPT_articulo,$NPT_ido,$codInsumo,$prescripcion)
{
	global $conex;
	global $wbasedato;
	global $bd;
	global $wemp_pmla;
	
	$qUpdateRealizada = "UPDATE ".$bd."_000215 
							SET Dnupcm='".$prescripcion."' 
						  WHERE Dnuhis='".$NPT_historia."' 
							AND Dnuing='".$NPT_ingreso."' 
							AND Dnuart='".$NPT_articulo."' 
							AND Dnuido='".$NPT_ido."' 
							AND Dnucod='".$codInsumo."' 
							AND Dnuest='on';";
	
	$resultadoUpdateRealizada = mysql_query($qUpdateRealizada,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qUpdateRealizada." - ".mysql_error());
		
}

function ponerPrescripcionesEnCeroNPT($codProducto,$historia,$ingreso,$articulo,$ido,$codInsumo,$prescripcion)
{	
	global $conex;
	global $wbasedato;
	global $bd;
	global $wemp_pmla;
	
	//consultar origen
	$qOrigenOrdenes = "SELECT Enuhis,Enuing,Enuart,Enuido,Dnucod 
						 FROM ".$bd."_000214, ".$bd."_000215 
						WHERE Enuhis='".$historia."' 
						  AND Enucnu='".$codProducto."' 
						  AND Enuord='on' 
						  AND Enurea='on' 
						  AND Enuest='on'
						  AND Enuhis=Dnuhis
						  AND Enuing=Dnuing
						  AND Enuart=Dnuart
						  AND Enuido=Dnuido
						  AND Dnuest='on';";
						  
	$resOrigenOrdenes = mysql_query($qOrigenOrdenes,$conex);
	$numOrigenOrdenes = mysql_num_rows($resOrigenOrdenes);					  
	
	if($numOrigenOrdenes>0)
	{
		while($rowOrigenOrdenes = mysql_fetch_array($resOrigenOrdenes))
		{
			$qUpdateRealizada = "UPDATE ".$bd."_000215 
									SET Dnupcm='0' 
								  WHERE Dnuhis='".$historia."' 
									AND Dnuing='".$rowOrigenOrdenes['Enuing']."' 
									AND Dnuart='".$rowOrigenOrdenes['Enuart']."' 
									AND Dnuido='".$rowOrigenOrdenes['Enuido']."' 
									AND Dnucod='".$rowOrigenOrdenes['Dnucod']."' 
									AND Dnuest='on';";
		
			$resultadoUpdateRealizada = mysql_query($qUpdateRealizada,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qUpdateRealizada." - ".mysql_error());
		}
		
	}
}		

function actualizarPrescripcionNPTCentralMezclas($historia,$ingreso,$articulo,$ido,$codInsumo,$prescripcion)
{	
	global $conex;
	global $wbasedato;
	global $bd;
	global $wemp_pmla;
	
	$qUpdateRealizada = "UPDATE ".$bd."_000215 
							SET Dnupcm='".$prescripcion."' 
						  WHERE Dnuhis='".$historia."' 
							AND Dnuing='".$ingreso."' 
							AND Dnuart='".$articulo."' 
							AND Dnuido='".$ido."' 
							AND Dnucod='".$codInsumo."' 
							AND Dnuest='on';";
	
	$resultadoUpdateRealizada = mysql_query($qUpdateRealizada,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qUpdateRealizada." - ".mysql_error());
	
}

function consultarTiempoInfusionPorRequerimieno($codigo)
{
	global $conex;
	global $wbasedato;
	global $bd;

	$q= " SELECT Reqmti 
			FROM ".$wbasedato."_000015,".$bd."_000212 
		   WHERE Facart = '".$codigo."' 
			 AND Facest = 'on' 
			 AND Facreq = Reqcod 
			 AND Reqest = 'on'
			Order by Faccod ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		$row = mysql_fetch_array($res);
		if( $row[0]=='on')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function consultarSiMultiplicaPorTiempoInfusion($codigoRequerimiento)
{
	global $conex;
	global $wbasedato;
	global $bd;

	$q= " SELECT Reqmti 
			FROM ".$bd."_000212 
		   WHERE Reqcod = '".$codigoRequerimiento."' 
			 AND Reqest = 'on';";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		$row = mysql_fetch_array($res);
		if( $row[0]=='on')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
function crearEncabezadoNPTKardex($historia,$ingreso,$articulo,$ido,$peso,$tiempoInfusion,$purga,$volumen,$codigoNutricion)
{
	global $conex;
	global $bd;
	global $wemp_pmla;
	
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");
	
	$queryInsertEncab="INSERT INTO ".$bd."_000214 (Medico,Fecha_data,Hora_data,Enuhis,Enuing,Enuart,Enuido,Enupes,Enutin,Enupur,Enuvol,Enuobs,Enurea,Enuord,Enuest,Enucnu,Seguridad) VALUES ('".$bd."','".$fecha."','".$hora."','".$historia."','".$ingreso."','".$articulo."','".$ido."','".$peso."','".$tiempoInfusion."','".$purga."','".$volumen."','','on','off','on','".$codigoNutricion."','C-".$bd."');";

	$resultadoInsertEncab = mysql_query($queryInsertEncab,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryInsertEncab." - ".mysql_error());
		
}

function mostrarCantidadNPTPendientes($actualizar)
{
	$wemp_pmla="01";
	$conex = obtenerConexionBD("matrix");
	$bd = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	
	$data = array('html'=>"");
	
	// Query nuevo
	$qNutriciones  = "SELECT Kadhis,Kading,Kadart,Kadido,Enurea,'ordenes' AS origen  
						FROM ".$bd."_000214,".$bd."_000054
					   WHERE Kadhis = Enuhis
						 AND Kading = Enuing
						 AND Kadart = Enuart
						 AND Kadido = Enuido
						 AND Kadfec='".date('Y-m-d')."' 
						 AND Kadsus = 'off'
						 AND Kadest = 'on'
						 AND Kadcon = 'on'
						 AND Enuest = 'on'
						 AND Enuord = 'on'
						 
					   UNION
					   
					  SELECT Kadhis,Kading,Kadart,Kadido,'' AS Enurea,'kardex' AS origen 
						FROM ".$bd."_000054,".$wbasedato."_000002,".$wbasedato."_000001,".$bd."_000068   
					   WHERE Kadfec='".date('Y-m-d')."' 
						 AND Kadest='on' 
						 AND Kadsus='off'
						 AND Kadcon = 'on'
						 AND Artcod = Kadart 
						 AND tippro = 'on' 
						 AND Tipnco = 'off' 
						 AND Tipcdo != 'on'
						 AND Tipest = 'on'
						 AND Arttip = Tipcod
						 AND Artcod = Arkcod
						 AND Tiptpr = Arktip
					   
					ORDER BY Kadhis,Kading,origen DESC;";
	
	// echo"<pre>".print_r($qNutriciones,true)."<pre>";
	
		$resNutriciones = mysql_query($qNutriciones, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qNutriciones . " - " . mysql_error());
	$numNutriciones = mysql_num_rows($resNutriciones);
	
	$contadorNPTPendientes = 0;
	if($numNutriciones > 0)
	{
		while($rowsNutriciones = mysql_fetch_array($resNutriciones))
		{
			$sinRealizar = true;
			//Si es de Kardex validar que no se haya realizado la nutricion
			if($rowsNutriciones['origen']=="kardex")
			{
				$qValidarNPTKardex = "SELECT *  
										FROM ".$bd."_000214 
									   WHERE Enuhis='".$rowsNutriciones['Kadhis']."' 
										 AND Enuing='".$rowsNutriciones['Kading']."' 
										 AND Enuart='".$rowsNutriciones['Kadart']."' 
										 AND Enuido='".$rowsNutriciones['Kadido']."' 
										 AND Enuord='off'
										 AND Enurea='on'
										 ;";
				
				$resValidarNPTKardex = mysql_query($qValidarNPTKardex, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qValidarNPTKardex . " - " . mysql_error());
				$numValidarNPTKardex = mysql_num_rows($resValidarNPTKardex);
				
				if($numValidarNPTKardex > 0)
				{
					$sinRealizar = false;
				}
							
			}
			
			if($sinRealizar)
			{
				//armar array
				$idArrayNPT = $rowsNutriciones['Kadhis']."-".$rowsNutriciones['Kading']."-".$rowsNutriciones['Kadart']."-".$rowsNutriciones['Kadido'];
				if(!isset($arrayNPTPendientes[$idArrayNPT]))
				{
					
					$arrayNPTPendientes[$idArrayNPT]['Enurea'] = $rowsNutriciones['Enurea'] ;
					
					if($rowsNutriciones['Enurea']!= "on")
					{
						$contadorNPTPendientes++;
					}
					
				}
			}
			
		}
		
	}
	
	if($contadorNPTPendientes>1)
	{
		$tablaCantNPT = "<table id='tablaCantidadNPTPendientes' border=0 align='center' width='90%'><tr class='texto1'><td class='blink'>TIENE ".$contadorNPTPendientes." NUTRICIONES PARENTERALES PENDIENTES DE REALIZAR</td></tr></table>";
	}
	elseif($contadorNPTPendientes==1)
	{
		$tablaCantNPT = "<table id='tablaCantidadNPTPendientes' border=0 align='center' width='90%'><tr class='texto1'><td class='blink'>TIENE ".$contadorNPTPendientes." NUTRICION PARENTERAL PENDIENTE DE REALIZAR</td></tr></table>";
	}
	else
	{
		$tablaCantNPT = "<table id='tablaCantidadNPTPendientes' border=0 align='center' width='90%'><tr class='texto1'><td class='blink'>NO TIENE NUTRICIONES PARENTERALES PENDIENTES DE REALIZAR</td></tr></table>";
	}
	
	if($actualizar == "on")
	{
		$data['html'] = $tablaCantNPT;
		return json_encode($data);
	}
	else
	{
		return $tablaCantNPT;
	}
}

function queryDA($wemp_pmla,$wbasedatocm,$bd)
{
	global $conex;
	global $wbasedato;
	
	$gruposAntibioticos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "gruposMedicamentosAntibioticos" );
	
	$grupoAntib = explode(",",$gruposAntibioticos);
	
	$cadenaGruposAntibioticos = "";
	for($u=0;$u<count($grupoAntib);$u++)
	{
		$cadenaGruposAntibioticos .= "Artgru LIKE '".$grupoAntib[$u]."%' OR ";
	}
	
	if($cadenaGruposAntibioticos!="")
	{
		$cadenaGruposAntibioticos = " AND (".substr($cadenaGruposAntibioticos, 0, -3).")";
	}
	
	/*******************************************************************************
	 * Consultando las diferentes tablas de habitaciones
	 *******************************************************************************/
	$nameSelect = "slCcoDestino";
	$cco="ccodom";	//Febrero 02 de 2016:
	$sub="off";
	$tod="";
	$ipod="off";

	//$cco=" ";
	//Consulto los cco domiciliarios
	$wcenmez = $wbasedato; 
	$wbasedato = $bd;
	$centrosCostos = consultaCentrosCostos("Ccotra != 'on' AND ccodom = 'on'", "otros" );
	$wbasedato = $wcenmez;
	
	$tablasHabitaciones = [];
	
	//Hago esto(*otro*) para traer la tabla de ubicaciones por defecto
	$tablasHabitaciones[] = consultarTablaHabitaciones( $conex, $bd, '*otro*' );
	
	foreach( $centrosCostos as $key => $value ){
		$tablasHabitaciones[] = consultarTablaHabitaciones( $conex, $bd, $value->codigo );
	}
	/************************************************************************************/
	
	$val = false;
	$qDosisAdaptadas = '';
	foreach( $tablasHabitaciones as $tablaHabitaciones ){
		
		if( $val ){
			$qDosisAdaptadas .= " UNION ";
		}
		
		$val = true;
		
		$qDosisAdaptadas .= "  SELECT Kadhis,Kading,Kadart,Kadido,Kadcfr,Kadufr,Kadfin,Kadhin,Kadobs, Pacno1,Pacno2,Pacap1,Pacap2, Artgen,Ccocod,Cconom,Habcpa,'Antibiotico' AS tipo  
								FROM ".$bd."_000054 a,".$bd."_000026,root_000036,root_000037,".$bd."_000018,".$tablaHabitaciones.",".$bd."_000011
							   WHERE Kadfec='".date('Y-m-d')."'
								 AND Kadsus='off'
								 AND Kadest='on'
								 AND Kadart=Artcod
								 AND (Kaddis='0' AND a.Kadfin=Kadfec)
								 AND Artest='on'
								 ".$cadenaGruposAntibioticos."
								 AND Orihis = Kadhis
								 AND Oriing = Kading
								 AND Oriori = '01'
								 AND Oriced = Pacced
								 AND Oritid = Pactid
								 AND Kadhis = Ubihis
								 AND Kading = Ubiing
								 AND Kadhis = Habhis
								 AND Kading = Habing
								 AND Ccocod = Ubisac

							   UNION

							  SELECT Kadhis,Kading,Kadart,Kadido,Kadcfr,Kadufr,Kadfin,Kadhin,Kadobs, Pacno1,Pacno2,Pacap1,Pacap2, Artgen,Ccocod,Cconom,Habcpa,'Dosis adaptada' AS tipo  
								FROM ".$bd."_000054,".$bd."_000026,root_000036,root_000037,".$bd."_000018,".$tablaHabitaciones.",".$bd."_000011
							   WHERE Kadfec='".date('Y-m-d')."'
								 AND Kadsus='off'
								 AND Kadest='on'
								 AND Kaddoa='on'
								 AND Kadart=Artcod
								 AND Artest='on'
								 AND Orihis = Kadhis
								 AND Oriing = Kading
								 AND Oriori = '01'
								 AND Oriced = Pacced
								 AND Oritid = Pactid
								 AND Kadhis = Ubihis
								 AND Kading = Ubiing
								 AND Kadhis = Habhis
								 AND Kading = Habing
								 AND Ccocod = Ubisac
								 
							   UNION
								 
							  SELECT Kadhis,Kading,Kadart,Kadido,Kadcfr,Kadufr,Kadfin,Kadhin,Kadobs, Pacno1,Pacno2,Pacap1,Pacap2, Artgen,Ccocod,Cconom,Habcpa,'Generica' AS tipo  
								FROM ".$bd."_000054,root_000036,root_000037,".$bd."_000018,".$tablaHabitaciones.",".$bd."_000011,".$wbasedatocm."_000001,".$wbasedatocm."_000002,".$bd."_000068
							   WHERE Kadfec='".date('Y-m-d')."'
								 AND Kadsus='off'
								 AND Kadest='on'
								 AND Orihis = Kadhis
								 AND Oriing = Kading
								 AND Oriori = '01'
								 AND Oriced = Pacced
								 AND Oritid = Pactid
								 AND Kadhis = Ubihis
								 AND Kading = Ubiing
								 AND Kadhis = Habhis
								 AND Kading = Habing
								 AND Ccocod = Ubisac
								 AND Artcod = Kadart 

								 AND tippro = 'on' 
								 AND Tipnco = 'on' 
								 AND Tipcdo != 'on'
								 AND Tipest = 'on'
								 AND Arttip = Tipcod
								 AND Artcod = Arkcod
								 AND Artest = 'on'
								 AND Tiptpr = Arktip
								 AND Tiptpr IN ('DA','DS','DD')
								 
								 ";
		
	}
	
	if( !empty( $qDosisAdaptadas ) )
		$qDosisAdaptadas .= " ORDER BY Kadfin,Kadhin,Kadhis,Kading,tipo DESC; ";
	
	return $qDosisAdaptadas;
	
	
	
	// $qDosisAdaptadas = "  SELECT Kadhis,Kading,Kadart,Kadido,Kadcfr,Kadufr,Kadfin,Kadhin,Kadobs, Pacno1,Pacno2,Pacap1,Pacap2, Artgen,Ccocod,Cconom,Habcpa,'Antibiotico' AS tipo  
							// FROM ".$bd."_000054 a,".$bd."_000026,root_000036,root_000037,".$bd."_000018,".$bd."_000020,".$bd."_000011
						   // WHERE Kadfec='".date('Y-m-d')."'
							 // AND Kadsus='off'
							 // AND Kadest='on'
							 // AND Kadart=Artcod
							 // AND (Kaddis='0' AND a.Kadfin=Kadfec)
							 // AND Artest='on'
							 // ".$cadenaGruposAntibioticos."
							 // AND Orihis = Kadhis
							 // AND Oriing = Kading
							 // AND Oriori = '01'
							 // AND Oriced = Pacced
							 // AND Oritid = Pactid
							 // AND Kadhis = Ubihis
							 // AND Kading = Ubiing
							 // AND Kadhis = Habhis
							 // AND Kading = Habing
							 // AND Ccocod = Ubisac

						   // UNION

						  // SELECT Kadhis,Kading,Kadart,Kadido,Kadcfr,Kadufr,Kadfin,Kadhin,Kadobs, Pacno1,Pacno2,Pacap1,Pacap2, Artgen,Ccocod,Cconom,Habcpa,'Dosis adaptada' AS tipo  
							// FROM ".$bd."_000054,".$bd."_000026,root_000036,root_000037,".$bd."_000018,".$bd."_000020,".$bd."_000011
						   // WHERE Kadfec='".date('Y-m-d')."'
							 // AND Kadsus='off'
							 // AND Kadest='on'
							 // AND Kaddoa='on'
							 // AND Kadart=Artcod
							 // AND Artest='on'
							 // AND Orihis = Kadhis
							 // AND Oriing = Kading
							 // AND Oriori = '01'
							 // AND Oriced = Pacced
							 // AND Oritid = Pactid
							 // AND Kadhis = Ubihis
							 // AND Kading = Ubiing
							 // AND Kadhis = Habhis
							 // AND Kading = Habing
							 // AND Ccocod = Ubisac
							 
						   // UNION
							 
						  // SELECT Kadhis,Kading,Kadart,Kadido,Kadcfr,Kadufr,Kadfin,Kadhin,Kadobs, Pacno1,Pacno2,Pacap1,Pacap2, Artgen,Ccocod,Cconom,Habcpa,'Generica' AS tipo  
							// FROM ".$bd."_000054,root_000036,root_000037,".$bd."_000018,".$bd."_000020,".$bd."_000011,".$wbasedato."_000001,".$wbasedato."_000002,".$bd."_000068
						   // WHERE Kadfec='".date('Y-m-d')."'
							 // AND Kadsus='off'
							 // AND Kadest='on'
							 // AND Orihis = Kadhis
							 // AND Oriing = Kading
							 // AND Oriori = '01'
							 // AND Oriced = Pacced
							 // AND Oritid = Pactid
							 // AND Kadhis = Ubihis
							 // AND Kading = Ubiing
							 // AND Kadhis = Habhis
							 // AND Kading = Habing
							 // AND Ccocod = Ubisac
							 // AND Artcod = Kadart 

							 // AND tippro = 'on' 
							 // AND Tipnco = 'on' 
							 // AND Tipcdo != 'on'
							 // AND Tipest = 'on'
							 // AND Arttip = Tipcod
							 // AND Artcod = Arkcod
							 // AND Artest = 'on'
							 // AND Tiptpr = Arktip
							 // AND Tiptpr IN ('DA','DS','DD')
							 
							 // ORDER BY Kadfin,Kadhin,Kadhis,Kading,tipo DESC;";
							 
	// // echo "<pre>".print_r($qDosisAdaptadas,true)."</pre>";						 
	// return $qDosisAdaptadas;						 
}

function pintarInsumosDA($insumos, $inslis, $compu, $nutri, $peso, $purga, $estado, $vol,$tfd,$hacerSubmitInsumosDA,$DA_historia,$DA_ingreso,$DA_articulo,$DA_ido,$DA_cantidadSinPurga,$wronda,$wfecharonda,$DA_cco)
{
	global $conex;
	global $wbasedato;
	global $wemp_pmla;

	echo "<table id='tablaInsumosDA' border=0 ALIGN=CENTER width=90%>";
	
	if ($inslis!='')
	{
		if(isset($hacerSubmitInsumosDA))
		{
			echo "<input type='hidden' id='hacerSubmitInsumosDA' name='hacerSubmitInsumosDA' value='off'>";
		}
		else
		{
			echo "<input type='hidden' id='hacerSubmitInsumosDA' name='hacerSubmitInsumosDA' value='on'>";
		}
		
		echo "<tr><td colspan=5 class='titulo3' align='center'><b>Informacion detallada del Producto</b></td></tr>";
		if ($compu=='on')
		{
			echo "<tr><td class='texto2' colspan='1' align='center'>Seleccionar</td>";
			echo "<td class='texto2' colspan='1' align='center'>Parte del nombre</td>";
			echo "<td class='texto2' colspan='1' align='center'>Insumo</td>";
		}

		if ($nutri=='off')
		{
			$purgaDA = consultarPurgaDA($DA_cco);
			
			if($purgaDA=="0")
			{
				echo "<td class='texto2' colspan='1' align='center'>Cantidad</td>";
			}
			else
			{
				echo "<td class='texto2' colspan='1' align='center'>Dosis / Dosis con purga</td>";
			}
			echo "<td class='texto2' colspan='1' align='center'>Eliminar</td></tr>";
		}
		
		$cadenaInsumosNPTSinFraccion="";
		$voltol=0;
		
		foreach ($inslis as $i => $value)
		{
			if (is_int($i/2))
			{
				$class='texto3';
			}
			else
			{
				$class='texto4';
			}

			if ($compu=='on')
			{
				$esDiluyente = "esDiluyente='off'";
				if($inslis[$i]['dil']=="on")
				{
					$esDiluyente = "esDiluyente='on'";
				}
				
				$esJeringa = "esJeringa='off'";
				if($inslis[$i]['jer']=="on")
				{
					$esJeringa = "esJeringa='on'";
				}
				
				if($inslis[$i]['ele']=="")
				{
					$inslis[$i]['ele']=$inslis[$i]['pri'];
				}
				elseif($inslis[$i]['ele']=="on")
				{
					$inslis[$i]['ele']="checked";
				}
				else
				{
					$inslis[$i]['ele']="";
				}
					
				if(!isset($hacerSubmitInsumosDA) && $inslis[$i]['can']!="")
				{
					$inslis[$i]['ele']="checked";
					$inslis[$i]['pri']="checked";
				}
				
				$onclickSeleccionar = "onclick='validarDiluyente(this,\"".$inslis[$i]['cod']."\");'";
				$onclickNombre = "onclick='validarParteNombreDiluyente(this,\"".$inslis[$i]['cod']."\")'";
				$onclickEliminar = "onclick='enter2(".$i.")'";
				$readOnlyCantidad = "";
				$cantidadSinPurga = "";
				if($esDiluyente=="esDiluyente='off'" && $esJeringa=="esJeringa='off'")
				{
					$onclickSeleccionar = "onclick='return false;'";
					$onclickNombre = "onclick='return false;'";
					$onclickEliminar = "disabled='disabled'";
					$readOnlyCantidad = "readonly='readonly'";
					if($purgaDA!="0")
					{
						$cantidadSinPurga = $DA_cantidadSinPurga."/";
						$cantidadCaracteresMed = (strlen($cantidadSinPurga))*2;
					}
				}
				if($cantidadSinPurga=="")
				{
					$cantidadSinPurga = str_repeat("&nbsp;",$cantidadCaracteresMed+1);
				}
				
				echo "<tr  ".$esDiluyente." ".$esJeringa." id='filaInsumoDA_".$inslis[$i]['cod']."'>
						<td class='".$class."' colspan='1' align='center'><input type='checkbox' id='elementoSeleccionado_".$inslis[$i]['cod']."' name='inslis[".$i."][ele]' ".$inslis[$i]['ele']." ".$onclickSeleccionar." class='texto3' ></td>";
				
				echo "<td class='".$class."' colspan='1' align='center'><input type='checkbox' name='inslis[".$i."][pri]' ".$inslis[$i]['pri']." class='texto3' ".$onclickNombre."></td>";
				
				echo "<td class='".$class."' colspan='1' align='center'>".$inslis[$i]['cod']."-".$inslis[$i]['nom']."<input type='hidden' name='inslis[".$i."][cod]' value='".$inslis[$i]['cod']."'><input type='hidden' name='inslis[".$i."][nom]' value='".$inslis[$i]['nom']."'><input type='hidden' name='inslis[".$i."][gen]' value=".$inslis[$i]['gen']."><input type='hidden' name='inslis[".$i."][dil]' value=".$inslis[$i]['dil']."><input type='hidden' name='inslis[".$i."][jer]' value=".$inslis[$i]['jer']."></td>";
			
			}
			
			if($nutri=='off')
			{
				echo "<td class='".$class."' colspan='1' align='center'>".$cantidadSinPurga."<input type='TEXT' name='inslis[".$i."][can]' value='".$inslis[$i]['can']."' class='texto3' size='5' ".$readOnlyCantidad." onblur='validarCantidadDiluyente(this,\"".$inslis[$i]['cod']."\");'><input type='TEXT' name='inslis[".$i."][pre]' value='".$inslis[$i]['pre']."'  class='texto3' size='20' readonly='readonly'></td>";
				echo "<td class='".$class."' colspan='1' align='center'><input type='checkbox' name='eli' class='texto3' ".$onclickEliminar."></td></tr>";
			}
			

		}
	}
	
	if($DA_historia!=null)
	{
		echo "<input type='hidden' id='DA_historia' name='DA_historia' value='".$DA_historia."'>";
		echo "<input type='hidden' id='DA_ingreso' name='DA_ingreso' value='".$DA_ingreso."'>";
		echo "<input type='hidden' id='DA_articulo' name='DA_articulo' value='".$DA_articulo."'>";
		echo "<input type='hidden' id='DA_ido' name='DA_ido' value='".$DA_ido."'>";
		echo "<input type='hidden' id='DA_cantidadSinPurga' name='DA_cantidadSinPurga' value='".$DA_cantidadSinPurga."'>";
		echo "<input type='hidden' id='wronda' name='wronda' value='".$wronda."'>";
		echo "<input type='hidden' id='wfecharonda' name='wfecharonda' value='".$wfecharonda."'>";
		echo "<input type='hidden' id='DA_cco' name='DA_cco' value='".$DA_cco."'>";
	}
	
	echo "<input type='hidden' name='accion' value='0'></td>";
	echo "<input type='hidden' name='realizar' value='0'></td>";
	echo "<input type='hidden' name='eliminar' value='0'></td>";
	echo "</table></form>";
	
}

function consultarNombrePaciente($DA_historia)
{
	global $conex;
	global $wemp_pmla;
	
	$qNombrePaciente = "  SELECT Pacno1,Pacno2,Pacap1,Pacap2
							FROM root_000037,root_000036
						   WHERE Orihis='".$DA_historia."'
							 AND Oriori='".$wemp_pmla."'
							 AND Oriced=Pacced
							 AND Oritid=Pactid;";
	
	$resNombrePaciente = mysql_query($qNombrePaciente, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qNombrePaciente . " - " . mysql_error());
	$numNombrePaciente = mysql_num_rows($resNombrePaciente);
	
	$nombrePaciente = "";
	if($numNombrePaciente > 0)
	{
		$rowsNombrePaciente = mysql_fetch_array($resNombrePaciente);
		
		$nombrePaciente = $rowsNombrePaciente['Pacno1'].' '.$rowsNombrePaciente['Pacno2'].' '.$rowsNombrePaciente['Pacap1'].' '.$rowsNombrePaciente['Pacap2'];
	}
	
	return $nombrePaciente;	
}

function consultarServicio($historia,$ingreso)
{
	global $conex;
	global $bd;
	
	
	$q = "SELECT Habcco,Cconom 
			FROM ".$bd."_000020,".$bd."_000011 
		   WHERE Habhis='".$historia."' 
			 AND Habing='".$ingreso."'
			 AND Habcco=Ccocod;";

	$res=mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	

	$servicio = "";
	if($num>0)
	{
		$row=mysql_fetch_array($res);
		
		// $servicio = $row['Habcco'].' - '.$row['Cconom'];
		$servicio = $row['Cconom'];
	}
	
	return $servicio;
}

function consultarEdad($historia,$completa)
{
	global $conex;
	
	global $wemp_pmla;
	
	
	$q = "SELECT Pacnac 
			FROM root_000037,root_000036 
		   WHERE Orihis='".$historia."' 
			 AND Oriori='".$wemp_pmla."' 
			 AND Oriced=Pacced
			 AND Oritid = Pactid;";

	$res=mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	
	$edad = "";
	$anos = "";
	if($num>0)
	{
		$row=mysql_fetch_array($res);

		$fechaNacimiento = $row['Pacnac'];
		
		//Edad
		$ann=(integer)substr($fechaNacimiento,0,4)*360 +(integer)substr($fechaNacimiento,5,2)*30 + (integer)substr($fechaNacimiento,8,2);
		$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		$ann1=($aa - $ann)/360;
		$meses=(($aa - $ann) % 360)/30;
		if ($ann1<1){
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
			$anos = 0;
		} else {
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
			$anos = (integer)$ann1;
		}
		
		$edad = $wedad; 
	
	}
	
	if($completa=="on")
	{
		return $edad;
	}
	else
	{
		return $anos;
	}
	
}

function consultarCama($historia,$ingreso)
{
	global $conex;
	global $bd;
	
	$q = "SELECT Habcpa
			FROM ".$bd."_000020
		   WHERE Habhis='".$historia."' 
			 AND Habing='".$ingreso."';";

	$res=mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	
	$cama = "";
	if($num > 0)
	{
		$row=mysql_fetch_array($res);

		$cama = $row['Habcpa'];
	}
	
	return $cama;
}

function consultarRangoEtario($DA_historia)
{
	global $conex;
	global $wemp_pmla;
	
	$qFechaNacimiento = " SELECT Pacnac
							FROM root_000037,root_000036
						   WHERE Orihis='".$DA_historia."'
							 AND Oriori='".$wemp_pmla."'
							 AND Oriced=Pacced
							 AND Oritid=Pactid;";
	
	$resFechaNacimiento = mysql_query($qFechaNacimiento, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qFechaNacimiento . " - " . mysql_error());
	$numFechaNacimiento = mysql_num_rows($resFechaNacimiento);
	
	if($numFechaNacimiento > 0)
	{
		$rowsFechaNacimiento = mysql_fetch_array($resFechaNacimiento);
		
		$fechaNacimiento = $rowsFechaNacimiento['Pacnac'];
	}	
	
	//Edad
	$ann=(integer)substr($fechaNacimiento,0,4)*360 +(integer)substr($fechaNacimiento,5,2)*30 + (integer)substr($fechaNacimiento,8,2);
	$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$wedad=($aa - $ann);
	
	$wbasedatohce = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
	
	// $qEdad = "SELECT Raecod,Raedes,Raerin,Raerfi 
				// FROM ".$wbasedatohce."_000041 
			   // WHERE Raeest='on';";
			   
	$qEdad = "SELECT Raecod,Raedes,Raerin,Raerfi 
				FROM ".$wbasedatohce."_000041 
			   WHERE Raeest='on'
			     AND Raecod!='T';";
			   
	$resEdad = mysql_query($qEdad, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qEdad . " - " . mysql_error());
	$numEdad = mysql_num_rows($resEdad);
	
	$rangoEtario = "";
	if($numEdad > 0)
	{
		while($rowsEdad = mysql_fetch_array($resEdad))
		{
			if($wedad>=$rowsEdad['Raerin'] && $wedad<=$rowsEdad['Raerfi'])
			{
				$rangoEtario = $rowsEdad['Raecod'];
				break;
			}
		}
	}		
	
	return $rangoEtario;
}

function consultarJeringas()
{
	global $conex;
	global $wbasedato;
	global $bd;
	
	$qJeringas = "SELECT Artcod,Artcom,Artgen,Artuni,Unides
					FROM ".$wbasedato."_000002,farstore_000002   
				   WHERE Artjer='on'
					 AND Artest='on'
					 AND Artuni= Unicod
					 AND Uniest='on'
				ORDER BY Artcod;";
	
	$resJeringas = mysql_query($qJeringas, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qJeringas . " - " . mysql_error());
	$numJeringas = mysql_num_rows($resJeringas);
	
	$contadorJeringas = 0;
	$arrayJeringas = array();
	if($numJeringas > 0)
	{
		while($rowsJeringas = mysql_fetch_array($resJeringas))
		{
			$arrayJeringas[$contadorJeringas]['cod'] = $rowsJeringas['Artcod'];
			$arrayJeringas[$contadorJeringas]['nom'] = $rowsJeringas['Artcom'];
			$arrayJeringas[$contadorJeringas]['gen'] = $rowsJeringas['Artgen'];
			$arrayJeringas[$contadorJeringas]['pre'] = $rowsJeringas['Artuni']."-".$rowsJeringas['Unides'];
			$arrayJeringas[$contadorJeringas]['can'] = "";
			$arrayJeringas[$contadorJeringas]['pri'] = '';
			$arrayJeringas[$contadorJeringas]['dil'] = 'off';
			$arrayJeringas[$contadorJeringas]['ele'] = '';
			$arrayJeringas[$contadorJeringas]['jer'] = "on";
			
			$contadorJeringas++;
			
		}
	}		
	
	return $arrayJeringas;
}

function consultarDiluyentes($DA_articuloCM,$DA_cantidad,$DA_historia,$DA_cco)
{
	global $conex;
	global $wbasedato;
	global $bd;
	global $wemp_pmla;
	
	$purgaDA = consultarPurgaDA($DA_cco);
	
	$datosArticulo = consultarValoresArticulo($DA_articuloCM,$DA_historia);
	
	$volumenSugerido = 0;
	if($datosArticulo['concInfusion']!=null)
	{
		$volumenSugerido = $DA_cantidad/$datosArticulo['concInfusion'];
	}
	
	// $volumenSugerido = $volumenSugerido+$purgaDA;
				 
	$qDiluyentes = 	" SELECT Artcod,Artcom,Artgen,Artdex,Artsal,Artmin,Artmax,Artuni,Unides,CAST(Deffra AS UNSIGNED) AS Deffra,Deffru
						FROM ".$wbasedato."_000002,farstore_000002,".$bd."_000059 
					   WHERE (Artdex='on' OR Artsal='on' )
						 AND Artest='on' 
						 AND Artuni= Unicod 
						 AND Uniest='on'
						 AND Artcod=Defart
						 AND Defest='on'
					ORDER BY Artsal DESC,Artdex DESC,Deffra;";
						 
	$resDiluyentes = mysql_query($qDiluyentes, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDiluyentes . " - " . mysql_error());
	$numDiluyentes = mysql_num_rows($resDiluyentes);
	
	$contadorDiluyentes = 0;
	$arrayDiluyentes = array();
	if($numDiluyentes > 0)
	{
		while($rowsDiluyentes = mysql_fetch_array($resDiluyentes))
		{
			$arrayDiluyentes[$contadorDiluyentes]['cod'] = $rowsDiluyentes['Artcod'];
			$arrayDiluyentes[$contadorDiluyentes]['nom'] = $rowsDiluyentes['Artcom'];
			$arrayDiluyentes[$contadorDiluyentes]['gen'] = $rowsDiluyentes['Artgen'];
			$arrayDiluyentes[$contadorDiluyentes]['pre'] = $rowsDiluyentes['Artuni']."-".$rowsDiluyentes['Unides'];
			$arrayDiluyentes[$contadorDiluyentes]['can'] = calcularDiluyenteSugerido($volumenSugerido,$rowsDiluyentes['Artmin'],$rowsDiluyentes['Artmax'],$datosArticulo['enDextrosa'],$rowsDiluyentes['Artdex'],$rowsDiluyentes['Artsal']); // calcular sugerido
			$arrayDiluyentes[$contadorDiluyentes]['pri'] = '';
			$arrayDiluyentes[$contadorDiluyentes]['dil'] = 'on';
			$arrayDiluyentes[$contadorDiluyentes]['ele'] = '';
			$arrayDiluyentes[$contadorDiluyentes]['dex'] = $rowsDiluyentes['Artdex'];
			$arrayDiluyentes[$contadorDiluyentes]['sal'] = $rowsDiluyentes['Artsal'];
			
			$contadorDiluyentes++;
			
		}
	}		
	
	return $arrayDiluyentes;
}

function consultarValoresArticulo($DA_articuloCM,$DA_historia)
{
	global $conex;
	global $wbasedato;
	
	$qDatos = 	" SELECT Edainf,Edadex,Edaemi,Edaema 
					FROM cenpro_000021 
				   WHERE Edains='".$DA_articuloCM."' 
					 AND Edaest='on'
				ORDER BY Edaemi,Edaema;";
						 
	$resDatos = mysql_query($qDatos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDatos . " - " . mysql_error());
	$numDatos = mysql_num_rows($resDatos);
	
	$wedad = consultarEdad($DA_historia,"off");
	
	$arrayDatos = array();
	if($numDatos > 0)
	{
		while($rowsDatos = mysql_fetch_array($resDatos))
		{
			if($wedad>=$rowsDatos['Edaemi'] && $wedad<=$rowsDatos['Edaema'])
			{
				$arrayDatos['concInfusion'] = $rowsDatos['Edainf'];
				$arrayDatos['enDextrosa'] = $rowsDatos['Edadex'];
				
				break;
			}
		}
	}
	
	return $arrayDatos;
}

function calcularDiluyenteSugerido($volumenSugerido,$volMin,$volMaximo,$enDextrosa,$dextrosa,$salino)
{
	$cantidadBolsaDiluyente = "";
	
	if(($enDextrosa=="on" && $dextrosa=="on") || ($enDextrosa=="off" && $salino=="on"))
	{
		if($volumenSugerido>=$volMin && $volumenSugerido<=$volMaximo)
		{
			$cantidadBolsaDiluyente = 1;
		}
	}
	
	return $cantidadBolsaDiluyente;
}

function grabarRelacionDA($DA_historia,$DA_ingreso,$DA_articulo,$DA_ido,$codigoDA,$existe)
{
	global $conex;
	global $bd;
	
	$qRelacion = "SELECT * 
					FROM ".$bd."_000224 
				   WHERE Rdahis='".$DA_historia."' 
				     AND Rdaing='".$DA_ingreso."' 
					 AND Rdaart='".$DA_articulo."' 
					 AND Rdaido='".$DA_ido."' 
					 AND Rdaest='on';";
	
	$resRelacion= mysql_query($qRelacion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qRelacion . " - " . mysql_error());
	$numRelacion = mysql_num_rows($resRelacion);
	
	if($numRelacion==0)
	{
		$qInsert = "INSERT INTO ".$bd."_000224 (Medico,Fecha_data,Hora_data,Rdahis,Rdaing,Rdaart,Rdaido,Rdacda,Rdaest,Seguridad) VALUES ('".$bd."','".date("Y-m-d")."','".date("H:i:s")."','".$DA_historia."','".$DA_ingreso."','".$DA_articulo."','".$DA_ido."','".$codigoDA."','on','C-".$bd."');";
	
		$resultadoInsertRelacion = mysql_query($qInsert,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qInsert." - ".mysql_error());
		
		if($resultadoInsertRelacion)
		{
			if($existe=="on")
			{
				// validar si el producto es dosis adaptada o no
				$mensajeNoEsDA = consultarTipoProductoCM($codigoDA);
				if($mensajeNoEsDA == "")
				{
					pintarAlert1('Ya existe un producto con dicha composicion, se relacionará la Dosis adaptada a la historia: '.$DA_historia."-".$DA_ingreso);
				}
				else
				{
					pintarAlert1($mensajeNoEsDA);
				}
			}
			else
			{
				pintarAlert1('Se creó y relacionó la Dosis adaptada a la historia: '.$DA_historia."-".$DA_ingreso);
			}
		}
		else
		{
			pintarAlert1('Error, no se relacionaró la Dosis adaptada a la historia: '.$DA_historia."-".$DA_ingreso);
		}
		
	}
	else
	{
		// update
		$queryActualizarDA = "UPDATE ".$bd."_000224
								 SET Rdacda='".$codigoDA."'
							   WHERE Rdahis='".$DA_historia."' 
								 AND Rdaing='".$DA_ingreso."' 
								 AND Rdaart='".$DA_articulo."' 
								 AND Rdaido='".$DA_ido."' 
								 AND Rdaest='on';";
								 
		$resultadoActualizarDA = mysql_query($queryActualizarDA,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryActualizarDA." - ".mysql_error());						 
		
		$numActualizarDA = mysql_affected_rows();
								
		if($numActualizarDA>0)
		{
			if($existe=="on")
			{
				// validar si el producto es dosis adaptada o no
				$mensajeNoEsDA = consultarTipoProductoCM($codigoDA);
				if($mensajeNoEsDA == "")
				{
					pintarAlert1('Ya existe un producto con dicha composición, se actualizará la relación de la Dosis adaptada a la historia: '.$DA_historia."-".$DA_ingreso);
				}
				else
				{
					pintarAlert1($mensajeNoEsDA);
				}
			}
			else
			{
				pintarAlert1('Se creó y actualizó la relación de la Dosis adaptada a la historia: '.$DA_historia."-".$DA_ingreso);
			}
		}
		else
		{
			pintarAlert1('Ya se relacionó la Dosis adaptada con dicha composicion a la historia: '.$DA_historia."-".$DA_ingreso);
		}
		
	}	
		
}

function mostrarCantidadDAPendientes($actualizar)
{
	$wemp_pmla="01";
	$conex = obtenerConexionBD("matrix");
	$bd = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	
	$data = array('html'=>"");
	
	$qDosisAdaptadas = queryDA($wemp_pmla,$wbasedato,$bd);
	// echo"<pre>".print_r($qDosisAdaptadas,true)."<pre>";
	
	$resDosisAdaptadas = mysql_query($qDosisAdaptadas, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDosisAdaptadas . " - " . mysql_error());
	$numDosisAdaptadas = mysql_num_rows($resDosisAdaptadas);
	
	$arrayDAPendientes = array();
	$contadorDosisAdaptadas = 0;
	if($numDosisAdaptadas > 0)
	{
		while($rowsDosisAdaptadas = mysql_fetch_array($resDosisAdaptadas))
		{
			$idArrayDA = $rowsDosisAdaptadas['Kadhis']."-".$rowsDosisAdaptadas['Kading']."-".$rowsDosisAdaptadas['Kadart']."-".$rowsDosisAdaptadas['Kadido'];
			if(!isset($arrayDAPendientes[$idArrayDA]))
			{
				$arrayDAPendientes[$idArrayDA]['Kadhis'] = $rowsDosisAdaptadas['Kadhis'];
				$arrayDAPendientes[$idArrayDA]['Kading'] = $rowsDosisAdaptadas['Kading'];
				$arrayDAPendientes[$idArrayDA]['Kadart'] = $rowsDosisAdaptadas['Kadart'];
				$arrayDAPendientes[$idArrayDA]['Kadido'] = $rowsDosisAdaptadas['Kadido'];
			}
			
		}
		
		if(count($arrayDAPendientes)>0)
		{
			foreach($arrayDAPendientes as $key => $value)
			{
				$qValidarDArealizada = "SELECT *  
										  FROM ".$bd."_000224,".$wbasedato."_000002
									     WHERE Rdahis='".$value['Kadhis']."' 
										   AND Rdaing='".$value['Kading']."' 
										   AND Rdaart='".$value['Kadart']."' 
										   AND Rdaido='".$value['Kadido']."' 
										   AND Rdaest='on'
										   AND Artcod=Rdacda
										   AND Artest='on';";
				
				$resValidarDArealizada = mysql_query($qValidarDArealizada, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qValidarDArealizada . " - " . mysql_error());
				$numValidarDArealizada = mysql_num_rows($resValidarDArealizada);
				
				if($numValidarDArealizada > 0)
				{
					$contadorDosisAdaptadas++;
				}	
			}
				
		}
	}
	$contadorDosisAdaptadas = count($arrayDAPendientes) - $contadorDosisAdaptadas;
	
	if($contadorDosisAdaptadas>1)
	{
		$tablaCantDA = "<table id='tablaCantidadDAPendientes' border=0 align='center' width='90%'><tr class='texto1'><td class='blink'>TIENE ".$contadorDosisAdaptadas." DOSIS ADAPTADAS PENDIENTES DE REALIZAR</td></tr></table>";
	}
	elseif($contadorDosisAdaptadas==1)
	{
		$tablaCantDA = "<table id='tablaCantidadDAPendientes' border=0 align='center' width='90%'><tr class='texto1'><td class='blink'>TIENE ".$contadorDosisAdaptadas." DOSIS ADAPTADA PENDIENTE DE REALIZAR</td></tr></table>";
	}
	else
	{
		$tablaCantDA = "<table id='tablaCantidadDAPendientes' border=0 align='center' width='90%'><tr class='texto1'><td class='blink'>NO TIENE DOSIS ADAPTADAS PENDIENTES DE REALIZAR</td></tr></table>";
	}
	
	if($actualizar == "on")
	{
		$data['html'] = $tablaCantDA;
		return json_encode($data);
	}
	else
	{
		return $tablaCantDA;
	}
}

function pintarEncabezadoDA($DA_historia,$DA_ingreso,$cantColspan)
{
	$soloLectura = "readOnly='readOnly'";
	
	$fondoTextbox = "style='background-color:#FFFFCC;'";
	
	echo "<table border=0 ALIGN=CENTER width=90%>";
		echo "<tr><td colspan=".$cantColspan." class='texto1' align='center'><b>Informacion del paciente</b></td></tr>";
	
		echo "<tr><td class='texto2' colspan='1' align='left'>Historia: <input type='TEXT' id='hisIngDA' name='hisIngDA' value='".$DA_historia." - ".$DA_ingreso."'  class='texto5' size='10'".$soloLectura." ".$fondoTextbox."></td>";
			echo "<td class='texto2' colspan='1' align='left'>Nombre: <input type='TEXT' id='nombrePacDA' name='nombrePacDA' value='".consultarNombrePaciente($DA_historia)."'  class='texto5' size='40' ".$soloLectura." ".$fondoTextbox."></td>";
			echo "<td class='texto2' colspan='1' align='left'>Edad: <input type='TEXT' id='edadPacDA' name='edadPacDA' value='".consultarEdad($DA_historia,"on")."'  class='texto5' size='30' ".$soloLectura." ".$fondoTextbox."></td>";
			echo "<td class='texto2' colspan='1' align='left'>Servicio: <input type='TEXT' id='servicioPacDA' name='servicioPacDA' value='".consultarServicio($DA_historia,$DA_ingreso)."'  class='texto5' size='30'  ".$soloLectura." ".$fondoTextbox."></td>";
			echo "<td class='texto2' colspan='1' align='left'>Habitacion: <input type='TEXT' id='HabDA' name='HabDA' value='".consultarCama($DA_historia,$DA_ingreso)."'  class='texto5' size='10'  ".$soloLectura." ".$fondoTextbox."></td>";
		echo "</tr>";
	echo "</table>";
}

function consultarDosisSiMedicamentoCompuesto($historia,$ingreso,$articulo,$ido)
{
	global $conex;
	global $bd;
	
	$queryMedCompuesto = "SELECT Defcmp,Defcpa,Defcsa 
							FROM ".$bd."_000059 
						   WHERE Defart='".$articulo."' 
							 AND Defest='on';";
							 
	$resMedCompuesto = mysql_query($queryMedCompuesto, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryMedCompuesto . " - " . mysql_error());
	$numMedCompuesto = mysql_num_rows($resMedCompuesto);
	
	$dosisAntibiotico = "";
	if($numMedCompuesto > 0)
	{
		while($rowsMedCompuesto = mysql_fetch_array($resMedCompuesto))
		{
			if($rowsMedCompuesto['Defcmp']=="on" && $rowsMedCompuesto['Defcpa']!="" && $rowsMedCompuesto['Defcsa']!="")
			{
				// consultar la dosis de antibiotico
				$queryDosisAntibiotico = " SELECT Ekxin1 
											 FROM ".$bd."_000208 
											WHERE Ekxhis='".$historia."' 
											  AND Ekxing='".$ingreso."' 
											  AND Ekxart='".$articulo."' 
											  AND Ekxido='".$ido."' 
											  AND Ekxfec='".date("Y-m-d")."'
											  AND Ekxest='on'

											UNION

										   SELECT Ekxin1 
											 FROM ".$bd."_000209 
											WHERE Ekxhis='".$historia."' 
											  AND Ekxing='".$ingreso."' 
											  AND Ekxart='".$articulo."' 
											  AND Ekxido='".$ido."' 
											  AND Ekxfec='".date("Y-m-d")."'
											  AND Ekxest='on';";
										 
				$resDosisAntibiotico = mysql_query($queryDosisAntibiotico, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryDosisAntibiotico . " - " . mysql_error());
				$numDosisAntibiotico = mysql_num_rows($resDosisAntibiotico);
				
				if($numDosisAntibiotico>0)
				{
					while($rowsDosisAntibiotico = mysql_fetch_array($resDosisAntibiotico))
					{
						$dosisAntibiotico = $rowsDosisAntibiotico['Ekxin1'];
					}
				}
			}
		}
	}

	return $dosisAntibiotico;	
}

function esArticuloGenerico( $conexion, $wbasedatoMH, $wbasedatoCM, $codArticulo ){
	
	$sql = "SELECT
				*
			FROM
				{$wbasedatoMH}_000068,
				{$wbasedatoCM}_000002,
				{$wbasedatoCM}_000001
			WHERE
				arkcod = '$codArticulo'
				AND artcod = arkcod
				AND arttip = tipcod
				AND tiptpr = arktip
				AND artest = 'on'
				AND arkest = 'on'
				AND tipest = 'on' 
			";
	
	$res = mysql_query( $sql, $conexion ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		return true;
	}
	else{
		return false;
	}
}

function consultarSiTipoEsDA($tipoProducto)
{
	global $conex;
	global $wbasedato;
	
	$tipo = explode("-",$tipoProducto);
	
	$queryTiposDosisAdaptadas= "SELECT Tipcod 
								  FROM ".$wbasedato."_000001 
							     WHERE Tipdis IN ('DA','DS','DD')
								   AND Tipest='on';";
							 
	$resTiposDosisAdaptadas = mysql_query($queryTiposDosisAdaptadas, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryTiposDosisAdaptadas . " - " . mysql_error());
	$numTiposDosisAdaptadas = mysql_num_rows($resTiposDosisAdaptadas);
	
	$esDosisAdaptada = "off";
	if($numTiposDosisAdaptadas > 0)
	{
		while($rowsTiposDosisAdaptadas = mysql_fetch_array($resTiposDosisAdaptadas))
		{
			if($rowsTiposDosisAdaptadas['Tipcod']==$tipo[0])
			{
				$esDosisAdaptada = "on";
				break;
			}	
		}
	}
	
	return $esDosisAdaptada;
}

	function consultarTipoProductoCM($codProdCM)
	{
		global $conex;
		global $wbasedato;
		
		$qDA = 	" SELECT Arttip,Tiptpr,Tipdes  
					FROM cenpro_000002,cenpro_000001
				   WHERE Artcod='".$codProdCM."' 
					 AND Artest='on'
					 AND Arttip=Tipcod;";
							 
		$resDA = mysql_query($qDA, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDA . " - " . mysql_error());
		$numDA = mysql_num_rows($resDA);
		
		$mensajeNoEsDA = "";
		if($numDA > 0)
		{
			while($rowsDA = mysql_fetch_array($resDA))
			{
				$esDosisAdaptada = consultarSiTipoEsDA($rowsDA['Arttip']."-".$rowsDA['Tipdes']);
				
				if($esDosisAdaptada=="off")
				{
					$mensajeNoEsDA = "Ya existe un producto ".$rowsDA['Tipdes']." con dicha composicion";
				}
			}
		}
		
		return $mensajeNoEsDA;
	}
	
	function pintarManuales($wusuario)
	{
		global $conex;
		
		//Consulta que trae los usuarios que tienen acceso al manual tecnico
		$q_desarro = "SELECT Perusu, Descripcion
						FROM root_000042, usuarios
					   WHERE perest = 'on'
						 AND Percco = '(01)1710'
						 AND Pertip IN('03', '02')
						 AND Codigo = Perusu
						 AND Perusu = '".$wusuario."'";


		$res_desarro = mysql_query($q_desarro,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar desarrolladores): ".$q_desarro." - ".mysql_error());
		$numDesarrolladores = mysql_num_rows($res_desarro);
				
		$esDesarrollador = false;
		if($numDesarrolladores > 0)
		{
			$esDesarrollador = true;
		}
		
		$nombreArchivo = "DA";
		
		//Link manual de usuario
		$manualusuario ='';
		$nombreArchivoManUsu = $nombreArchivo.".pdf";
		if(file_exists("../manuales/".$nombreArchivoManUsu) ==1)
		{
			$manualusuario ="<br> <a href='../manuales/".$nombreArchivoManUsu."'  onclick='window.open(this.href);return false'   style='cursor : pointer'>Manual de Usuario DA<a/>";
		}
		
		//Link manual tecnico
		$manualtecnicoDA='';
		$nombreArchivoManTec = $nombreArchivo.".tec.pdf"; 
		if( $esDesarrollador == true && file_exists("../manuales/".$nombreArchivoManTec) == 1  ) // si existe el archivo y el usuario tiene permiso de visualizacion se crea el link
		{
			$manualtecnicoDA="<br><a href='../manuales/".$nombreArchivoManTec."'  onclick='window.open(this.href);return false'   style='cursor : pointer'>Manual Tecnico DA</a>";
		}
		
		//Link manual tecnico
		$nombreArchivo = "NPT";
		$manualtecnico='';
		$nombreArchivoManTec = $nombreArchivo.".tec.pdf"; 
		if( $esDesarrollador == true && file_exists("../manuales/".$nombreArchivoManTec) == 1  ) // si existe el archivo y el usuario tiene permiso de visualizacion se crea el link
		{
			$manualtecnicoNPT="<br><a href='../manuales/".$nombreArchivoManTec."'  onclick='window.open(this.href);return false'   style='cursor : pointer'>Manual Tecnico NPT</a>";
		}
		
		if($manualusuario!="" || $manualtecnicoDA!="" || $manualtecnicoNPT!="")
		{
			// echo "<span style='font-size:8px;position:absolute;right:26.6%;' class=''>Otros manuales: ".$manualusuario." ".$manualtecnicoDA." ".$manualtecnicoNPT."</span>";
			echo "<span style='font-size:8px;position:relative;right:-82%;' class=''>Otros manuales: ".$manualusuario." ".$manualtecnicoDA." ".$manualtecnicoNPT."</span>";
		}
		
	}
	
	function consultarPurgaDA($cco)
	{
		global $conex;
		global $bd;
		
		//Consulta el valor de la purga para las dosis adaptadas por centro de costo
		$queryPurga = "SELECT Ccopda 
						 FROM ".$bd."_000011 
						WHERE Ccocod='".$cco."' 
						  AND Ccoest='on';";

		$resPurga = mysql_query($queryPurga,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar purga DA): ".$queryPurga." - ".mysql_error());
		$numPurga = mysql_num_rows($resPurga);
				
		$purga = 0;
		if($numPurga > 0)
		{
			$rowPurga = mysql_fetch_array($resPurga);
			$purga = $rowPurga['Ccopda'];
		}
		
		return $purga;
	}
	
	 //funcion
	function consultarConcentracionArticuloSF( $conex, $wmovhos, $artcod )
	{

		$val = 1;
		//****************************************************************************************************************************************
		// Agosto 29 de 2019. (2019-08-29)
		// Lo que se pretende es convertir la dosis ordenada del médico por la unidad mínima de facturación.
		// Es conversión la tiene la tabla de fracciones por articulo(movhos 59), por tal motivo se quita la relacción con la tabla 115
		//****************************************************************************************************************************************
		//Consulto el codigo correspondiente en CM
		$sql = "SELECT Deffra as Relcon
				  FROM ".$wmovhos."_000026 a, ".$wmovhos."_000059 c
				 WHERE a.artcod = '".$artcod."'
				   AND c.defart = a.artcod
				   AND a.artuni != c.deffru
				   AND c.defcco = '1050'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array($res) ){
			$val = $rows[ 'Relcon' ];
		}
		
		return $val;
	}


	

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
$noEsAjax = false;
if(isset($accion)) 
{	
	switch($accion)
	{
		case 'recargarCantNPTPendientes':
			echo mostrarCantidadNPTPendientes($actualizar);
			break;
		case 'recargarCantDAPendientes':
			echo mostrarCantidadDAPendientes($actualizar);
			break;
		default: 
			$noEsAjax = true;
			break;
	}
}
else
{
	$noEsAjax = true;
}

if($noEsAjax)
{
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X 
//=======================================================================================================================================================	
?>

<head>
  <title>APLICACION DE CENTRAL DE MEZCLAS</title>
  
  
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.tooltip.js"     type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
	
	 
	 
	 
	
	<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
	
	<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
	<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
	 
	 
	<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
	
  
  
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo3{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#FFDBA8;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}	
    	.texto2{color:#003366;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.texto3{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto4{color:#003366;background:#f5f5dc;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto5{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto7{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
		
		
		.SinEquivalente{color:#003366;background:#FFB5B5;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	
   </style>
  
   <script type="text/javascript">
	
	function cancelarPreparacionDA(historia,ingreso,codArticulo,ido,e,elemento)
	{
	
		//evita onClick crearDA cuando cambia el checkbox
		e.stopPropagation();
	
		// jAlert("jhdsfkjsf","ALERTA");
		// var marcado = $("#noPrepararDA").prop("checked");
		var marcado = $(elemento).prop("checked");
		
		
		var mensajeConfirm = "";
		if(marcado)
		{
			mensajeConfirm = "Desea cancelar la preparacion de la dosis adaptada";
		}
		else
		{
			mensajeConfirm = "Desea activar la preparacion de la dosis adaptada";
		}
		
		jConfirm( mensajeConfirm, "ALERTA", function(resp){
			if( resp ){
				
				$.post("../../movhos/procesos/Monitor_Kardex.php",
				{
					consultaAjax 	: '',
					accion			: 'cancelarPreparacionDA',
					wbasedato		: $('#wbd').val(),
					wemp_pmla		: $('#wemp_pmla').val(),
					wusuario		: $('#wusuario').val(),
					historia		: historia,
					ingreso			: ingreso,
					codArticulo		: codArticulo,
					ido				: ido,
					marcado			: marcado
				}
				, function(data) {
					console.log(data);
					jAlert(data.mensaje,"ALERTA");
					
					
					// si hubo error quitar check
					if(data.error==1)
					{
						// $("#noPrepararDA").prop("checked",false);
						$(elemento).prop("checked",false);
					}
					
					
				},'json');
			}
			else
			{
				// $("#noPrepararDA").prop("checked",false);
				$(elemento).prop("checked",false);
			}
		});
		
	}
	
	function recargarCantDAPendientes()
	{
		$.post("cen_mez.php",
		{
			consultaAjax : 		'',
			accion:   		'recargarCantDAPendientes',
			actualizar:			'on'
		
		}
		,function(data) {
			
			$( "#tablaCantidadDAPendientes" ).html( data.html );

			$('.blink').effect("pulsate", {times:120}, 120000);
			
		},"json");
		
		
	}
	
	function validarParteNombreDiluyente(elemento,codInsumo)
	{
		if($("#elementoSeleccionado_"+codInsumo).attr('checked')=='checked')
		{
			enter();
		}
		else
		{
			$(elemento).removeAttr('checked');
		}
	}
	
	function validarCantidadDiluyente(elemento,codInsumo)
	{
		// validar que solo se llene la cantidad de un diluyente
		esDiluyente = $("#filaInsumoDA_"+codInsumo).attr('esDiluyente');
		esJeringa = $("#filaInsumoDA_"+codInsumo).attr('esJeringa');
		nameElemSeleccionado = $(elemento).attr('name');
	
		if(esDiluyente=="on" || esJeringa=="on")
		{
			cantidad = $(elemento).val();
			
			// si llenó cantidad
			if(cantidad!="" && cantidad !="0")
			{
				$("[id^=filaInsumoDA_]").each(function(){
					
					// busco todos los diluyentes
					if($(this).attr('esDiluyente')=="on" || $(this).attr('esJeringa')=="on")
					{
						campoCantidad = $(this).children(":nth-child(4)").children(":first");
						
						cantidadDiluyente = campoCantidad.val();
						if(cantidadDiluyente!="" && cantidadDiluyente !=0)
						{
							if(campoCantidad.attr("name")==$(elemento).attr('name'))
							{
								// Checkear
								$(this).children(":nth-child(1)").children().attr('checked',"");
								$(this).children(":nth-child(2)").children().attr('checked',"");
							}
							else
							{
								campoCantidad.val("");
								$(this).children(":nth-child(1)").children().removeAttr('checked');
								$(this).children(":nth-child(2)").children().removeAttr('checked');
							}
						}
					}
				});
			}
			
			enter();
		}
		else
		{
			$(elemento).attr('readOnly',"readOnly");
		}
		
	}
	
	function validarDiluyente(elemento,codInsumo)
	{
		esDiluyente = $("#filaInsumoDA_"+codInsumo).attr('esDiluyente');
		esJeringa = $("#filaInsumoDA_"+codInsumo).attr('esJeringa');
		nameElemSeleccionado = $(elemento).attr('name');
		
		if(esDiluyente=="on" || esJeringa=="on")
		{
			$("[id^=filaInsumoDA_]").each(function(){
				
				// busco todos los diluyentes
				if($(this).attr('esDiluyente')=="on" || $(this).attr('esJeringa')=="on")
				{
					elemDiluyente = $(this).attr('esDiluyente');
					
					campoSeleccion = $(this).children(":first").children();
					campoParteNombre = $(this).children(":nth-child(2)").children();
					campoCantidad = $(this).children(":nth-child(4)").children(":first");
					
					if(campoSeleccion.attr("name") == $(elemento).attr('name'))
					{
						campoSeleccion.attr('checked',"");
						campoParteNombre.attr('checked',"");
						
						campoCantidad.val("1");
					}
					else
					{
						campoSeleccion.removeAttr('checked');
						campoParteNombre.removeAttr('checked');
						
						campoCantidad.val("");
					}
				}
			});
		}
		
		enter();
	}
	
	function crearDA(historia,ingreso,articulo,descripcion,ido,articuloCM,cantidadSinPurga,cantidad,tipo,ronda,fechaRonda,cco)
	{
		if(articuloCM!="" || tipo=="Generica")
		{
			$( "#pintarListaDAPendientes" ).val(true);
			
			$( "#DA_historia" ).val(historia);
			$( "#DA_ingreso" ).val(ingreso);
			$( "#DA_articulo" ).val(articulo);
			$( "#DA_ido" ).val(ido);
			$( "#DA_articuloCM" ).val(articuloCM);
			$( "#DA_cantidadSinPurga" ).val(cantidadSinPurga);
			$( "#DA_cantidad" ).val(cantidad);
			$( "#DA_tipo" ).val(tipo);
			$( "#wronda" ).val(ronda);
			$( "#wfecharonda" ).val(fechaRonda);
			$( "#DA_cco" ).val(cco);
			
			
			document.producto.estado.value='inicio';
			document.producto.submit();
		}
		else
		{
			alert("El articulo "+articulo+" - "+descripcion+" no tiene un equivalente activo en central de mezclas (cenpro_000009)" );
		}
		
	}
	
	
	function desactivarBotonCrear()
	{
		$('#checkCrearNPT').hide();
		$('#btnAceptarNPT').hide();
		$('#btnAceptarNPT').parent().html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
	}
	
	function recargarCantNPTPendientes()
	{
		$.post("cen_mez.php",
		{
			consultaAjax : 		'',
			accion:   		'recargarCantNPTPendientes',
			actualizar:			'on'
		
		}
		,function(data) {
			
			$( "#tablaCantidadNPTPendientes" ).html( data.html );

			$('.blink').effect("pulsate", {times:120}, 120000);
			
		},"json");
		
		
	}
	
	function cambiarInstitucionNPT(institucion,wemp_pmla)
	{
		opcInstitucion = $( institucion ).val();
		$( "#opcionInstitucion" ).val(opcInstitucion);
		
		codOpcInstitucion = opcInstitucion.split("-");
		if(codOpcInstitucion[0]==wemp_pmla)
		{
			enter7();
		}
		else
		{
			$( "#peso" ).val("");
			$( "#purga" ).val("");
			$( "#volumen" ).val("");
			$( "#historia" ).val("");
			$( "#pac" ).val();
			
			enter1();
		}
	}
	
	function crearNPT(historia,ingreso,peso,tiempoinfusion,purga,volumenTotal,paciente,articulo,ido,origen)
	{
		$( "#peso" ).val(peso);
		$( "#purga" ).val(purga);
		$( "#volumen" ).val(volumenTotal);
		$( "#historia" ).val(historia);
		
		$( "#pintarListaNPTPendientes" ).val(true);
		
		$( "#NPT_historia" ).val(historia);
		$( "#NPT_ingreso" ).val(ingreso);
		$( "#NPT_articulo" ).val(articulo);
		$( "#NPT_ido" ).val(ido);
		
		$( "#NPT_tiempoInfusion" ).val(tiempoinfusion);
		$( "#NPT_origen" ).val(origen);
		
		document.producto.estado.value='inicio';
		document.producto.submit();
	}
   
   function cambiarValue( check, index ){
	   if( check.checked == true){
		   document.getElementById( "inslis["+index+"][app]" ).value = "on";
	   }
	   else{
		   document.getElementById( "inslis["+index+"][app]" ).value = "off";
	   }
   }
   
   function enter()
   {
   	document.producto.accion.value=1;
   	document.producto.submit();
   }

   function enter1()
   {
   	document.producto.submit();
   }

   function enter2(i)
   {
   	document.producto.eliminar.value=i;
   	document.producto.submit();
   }

   function enter3()
   {
   	document.producto.realizar.value='modificar';
   	document.producto.submit();
   }

   function enter4()
   {
   	document.producto.realizar.value='desactivar';
   	document.producto.submit();
   }


   function enter5()
   {
   	document.producto.estado.value='inicio';
   	document.producto.submit();
   }

   function enter7()
   {
   	document.producto2.tippro.value=document.producto.tippro.options[document.forms.producto.tippro.selectedIndex].text;
   	document.producto2.submit();
   }

   function validarFormulario1()
   {
   	textoCampo = window.document.producto.tfd.value;
   	textoCampo = validarEntero(textoCampo);
   	window.document.producto.tfd.value = textoCampo;
   }

   function validarFormulario2()
   {
   	textoCampo = window.document.producto.tfh.value;
   	textoCampo = validarEntero(textoCampo);
   	window.document.producto.tfh.value = textoCampo;
   }

   function validarFormulario3()
   {
   	textoCampo = window.document.producto.tvd.value;
   	textoCampo = validarEntero(textoCampo);
   	document.producto.tvd.value = textoCampo;
   }

   function validarFormulario4()
   {
   	textoCampo = window.document.producto.cantidad.value;
   	textoCampo = validarEntero(textoCampo);
   	window.document.producto.cantidad.value = textoCampo;
   }


   function validarFormulario5(valor)
   {
   	textoCampo = window.document.producto.fecha.value;
   	textoCampo = validarFecha(textoCampo);
   	if (!textoCampo)
   	{
   		window.document.producto.fecha.value = valor;
   	}
   }

   function validarFormulario6()
   {
   	textoCampo = window.document.producto.peso.value;
   	textoCampo = validarEntero(textoCampo);
   	window.document.producto.peso.value = textoCampo;
   }

   function validarFormulario7()
   {
   	textoCampo = window.document.producto.purga.value;
   	textoCampo = validarEntero(textoCampo);
   	window.document.producto.purga.value = textoCampo;
   }


   function validarFormulario8()
   {
   	textoCampo = window.document.producto.volumen.value;
   	textoCampo = validarEntero(textoCampo);
   	window.document.producto.volumen.value = textoCampo;
   }

   function validarEntero(valor)
   {
   	if (isNaN(valor))
   	{
   		alert('Debe ingresar un numero entero');
   		return '';
   	}else
   	{
   		if (valor<0)
   		{
   			alert('Debe ingresar un numero positivo');
   			return '';
   		}else
   		{
   			return valor;
   		}
   	}
   }


   function validarFecha(Fecha)
   {
   	if (Fecha.length!=10)
   	{
   		alert('Fecha invalida');
   		return false;
   	}
   	else
   	{
   		// Cadena Año
   		Ano= Fecha.substring(0,Fecha.indexOf("-"));

   		// Cadena Mes
   		Mes= Fecha.substring(Fecha.indexOf("-")+1,Fecha.lastIndexOf("-"));

   		// Cadena Día
   		Dia= Fecha.substring(Fecha.lastIndexOf("-")+1,Fecha.length);

   		// Valido el año
   		if (isNaN(Ano) || Ano.length<4 || parseFloat(Ano)<1900)
   		{
   			alert('Año inválido');
   			return false;
   		}

   		// Valido el Mes
   		if (isNaN(Mes) || parseFloat(Mes)<1 || parseFloat(Mes)>12)
   		{
   			alert('Mes inválido');
   			return false;
   		}

   		// Valido el Dia
   		if (isNaN(Dia) || parseInt(Dia, 10)<1 || parseInt(Dia, 10)>31)
   		{
   			alert('Día inválido');
   			return false;
   		}
   	}
   	return true   ;
   }

   function hacerFoco()
   {
	timer = setInterval('recargarCantNPTPendientes()', 120000);  
	timer2 = setInterval('recargarCantDAPendientes()', 120000);
	
	if( $("#hacerSubmitInsumosNPT").length > 0 && $("#hacerSubmitInsumosNPT").val() == 'on' ){
		document.producto.submit();
	}
	$('.blink').effect("pulsate", {times:120}, 200000);
	
	setInterval(function() {

		$(".blinkProdCreado").effect("pulsate", {}, 10000);

	}, 1000);
	
	
	if( $("#hacerSubmitInsumosDA").length > 0 && $("#hacerSubmitInsumosDA").val() == 'on' ){
		document.producto.submit();
	}
	
   
   	// if (document.producto.elements[23].value=='')
   	// {
   		// document.producto.elements[21].focus();
   	// }
   	// else
   	// {
   		// document.producto.elements[24].focus();
   	// }
	
		
   }

    </script>
  
</head>

<body onload="hacerFoco()">

<?php


/*===========================================================================================================================================*/
/*=========================================================PROGRAMA==========================================================================*/
session_start();

if (!isset($user))
{
	if(!isset($_SESSION['user']))
	session_register("user");
}

if(!isset($_SESSION['user']))
echo "error";
else
{
	$esDA = "off";
	if($DA_historia!="")
	{
		$esDA = "on";
	}
	
	$wbasedato='cenpro';
	$bd = "movhos";
	$wemp_pmla = "01";
	//$conex = mysql_connect('localhost','root','')
	//or die("No se ralizo Conexion");
	
	// 

	
	


	include_once("cenpro/funciones.php");
	include_once("root/comun.php");

	
	if(!isset($NPT_origen))
	{
		$NPT_origen=$origenNPT;
	}

	//pintarVersion(); //Escribe en el programa el autor y la version del Script.
	// pintarTitulo();  //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts

	//consulto los datos del usuario de la sesion
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user)); //extraigo el codigo del usuario
	
	$wactualiz = "Noviembre 19 de 2019";
	encabezado("PRODUCCION CENTRAL DE MEZCLAS",$wactualiz,"clinica");
	pintarManuales($wusuario);
	
	pintarTitulo();  //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts

	//se incializa el estado de las transaccion en 'inicio'
	if (!isset($estado))
	{
		$estado='inicio';
	}

	//foto y neve son dos checkbox, que si no se activaron se pasan a off
	if (!isset($foto))
	{
		$foto='off';
	}


	if (!isset($neve))
	{
		$neve='off';
	}

	if (!isset($via))
	{
		$neve='on';
	}
	/**
	 * si se ha seleccionado almacenar el prodcuto
	 */
	if (isset($crear))
	{
		if ($tfd=='')
		{
			$tfd=0;
		}

		if ($tfh=='')
		{
			$tfh=0;
		}

		//se verifica que todos los datos indispensables del prodcuto hallan sido seleccionados
		if ($productos[0]['nom']=='' or $productos[0]['gen']==''  or $presentacion=='' or $via=='' or ($tfd==0 and $tfh==0) or $tvd=='' or $tvh=='' or $fecha=='')
		{
			pintarAlert1('Debe ingresar todos los datos generales del producto correctamente');
		}
		else
		{
			// contar insumos con cantidades mayores a cero y diferentes de vacio
			$contInslis = count($inslis);
			if($esDA=="on")
			{
				$contInslis = 0;
				foreach ($inslis as $i => $value)
				{
					if ($inslis[$i]['can']>0 and $inslis[$i]['can']!='')
					{
						$contInslis++;
					}
				}
			}
			
			//se valida que se hallan ingresado almenos dos insumos
			// if (!isset($inslis) or count($inslis)<2)
			if (!isset($inslis) or $contInslis<2)
			{
				pintarAlert1('Debe ingresar al menos dos insumos para crear el producto');
			}
			else
			{
				//se valida que no exista un producto con la misma conposicion
				if ($nutri=='off')
				{
					$val=validarComposicion($inslis,$esDA);
				}
				else
				{
					$val=false;
				}

				if ($val)
				{
					$forcon='Codigo';
					$parcon=$val;
					// pintarAlert1('Ya existe un producto con dicha composicion');
					
					// si ya existe validar si es DA y guardar relacion
					if($esDA=="on")
					{
						grabarRelacionDA($DA_historia,$DA_ingreso,$DA_articulo,$DA_ido,$parcon,"on");
					}
					else
					{
						pintarAlert1('Ya existe un producto con dicha composicion');
					}
				}
				else
				{
					$exp=explode('-',$tippro);
					$tve=$tvd*24+$tvh;
					$tin=$tfd*60+$tfh;
					$productos[0]['cod']=incrementarConsecutivo($exp[0], $productos[0]['cod']);
					//echo $productos[0]['cod'];
					if ($nutri=='off')
					{
						grabarProducto($productos[0]['cod'], $productos[0]['nom'], $productos[0]['gen'], $presentacion, $via, $tin, $tve, $fecha, $exp[0], $wusuario, $foto, $neve, $des, '', '', '', '');
						
						//En la tabla de fracciones solo se guarda la unidad del insumo con mayor cantidad
						$unidad = consultarUnidadInsumoMaximo($inslis);
						$fraccion =  consultarFraccionProducto( $unidad, $inslis );
						
						$unidad = explode( "-", $unidad );
						$cco = centroCostos();
						
						list( $viaAux ) = explode( "-", $via );
						$viaCM = consultarViaMovhos( $conex, $bd, trim( $viaAux ) );
						registrarFraccion( $productos[0]['cod'], $fraccion, $unidad[0], intval($tve/24), $viaCM, $cco );
						
						if($esDA=="on")
						{
							// Grabar DA asociada a historia, ingreso, articulo, ido
							grabarRelacionDA($DA_historia,$DA_ingreso,$DA_articulo,$DA_ido,$productos[0]['cod'],"off");
						}
						
					}
					else
					{
						$vol=0;
						if (isset($inslis)) //si ya se han ingresado insumos se puede construir el nombre del producto
						{
							foreach ($inslis as $i => $value) //se recorre vector de insumos
							{
								if (!isset($peso) or $peso=='' or $inslis[$i]['ppe']!='on' or $peso==0)
								{
									$pe=1;
									$inslis[$i]['fac']=1;
									
								}
								else
								{
									$pe=$peso;
									if(!isset($inslis[$i]['fac']) or $inslis[$i]['fac']==1)
									{
										$inslis[$i]['fac']=(float)consultarFactor($inslis[$i]['cod'], $pe,$NPT_tiempoInfusion);
									}
								}

								$agua=consultarAgua($inslis[$i]['cod']);
								if ($agua)
								{

									$aguap=$i;
								}
								else
								{
									if($inslis[$i]['mat']!='on')
									{
										// $vol=$vol+$inslis[$i]['can']*$pe*$inslis[$i]['fac'];
										$vol=$vol+(float)$inslis[$i]['can']*(float)$pe*(float)$inslis[$i]['fac'];
									}
								}
							}

							$inslis[$aguap]['can']=round(($volumen-$vol),1);
							if ($inslis[$aguap]['can']<=0)
							{
								$inslis[$aguap]['can']='';
							}
							else
							{

								$vol=$vol+$inslis[$aguap]['can'];
							}
						}
						
						grabarProducto($productos[0]['cod'], $productos[0]['nom'], $productos[0]['gen'], $presentacion, $via, $tin, $tve, $fecha, $exp[0], $wusuario, $foto, $neve, '', $peso, $purga, $historia, $insti);
						
						if($NPT_origen=="ordenes")
						{
							marcarNPTRealizada($NPT_historia,$NPT_ingreso,$NPT_articulo,$NPT_ido,$productos[0]['cod']);
						}
						elseif($NPT_origen=="kardex")
						{
							crearEncabezadoNPTKardex($NPT_historia,$NPT_ingreso,$NPT_articulo,$NPT_ido, $peso,$tfd,$purga,$vol,$productos[0]['cod']);
						}
						

						
						$unidad = consultarUnidadInsumoMaximo($inslis);
						$unidad = explode( "-", $unidad );
						
						$cco = centroCostos();
						list( $viaAux ) = explode( "-", $via );
						$viaCM = consultarViaMovhos( $conex, $bd, trim( $viaAux ) );
						registrarFraccion( $productos[0]['cod'], $vol+(float)$purga, $unidad[0], intval((float)$tve/24), $viaCM, $cco );
					}

					//se graba uno a uno los insumos del producto	
					foreach ($inslis as $i => $value)
					{
						if ($inslis[$i]['can']>0 and $inslis[$i]['can']!='')
						{
							if ($nutri=='on')
							{
								$pesoaux = $peso;
								if ($peso=='' or $inslis[$i]['ppe']!='on' or $peso==0)
								{
									$pes=1;
									$inslis[$i]['fac']=1;
									if( $peso == '' || $peso == 0 ){
										$pesoaux = $peso;
										$peso = 1;
									}
								}
								else
								{
									$pes=$peso;
								}
								if($purga=='')
								{
									$pu=0;
								}
								else
								{
									$exp=explode('-',$purga);
									$pu=$exp[0];
								}
								
								$volnp = consultarVolNoPurgar( $inslis, $vol, $peso );

//								if($inslis[$i]['mat']!='on')
								if( $inslis[$i]['mat'] != 'on' && $inslis[$i]['app'] == 'on' )
								{
//									$can=round($inslis[$i]['can']*$pes*$inslis[$i]['fac']+round($inslis[$i]['can']*$pes*$inslis[$i]['fac']*$pu/($vol-$volnp), 2),2);
									$can=round($inslis[$i]['can']*$pes*(float)$inslis[$i]['fac']+$inslis[$i]['can']*$pes*(float)$inslis[$i]['fac']*$pu/($vol-$volnp),2);
								}
								else if( $inslis[$i]['mat'] != 'on' ){
									$can=round($inslis[$i]['can']*$pes*(float)$inslis[$i]['fac'],2);
								}
								else
								{
									$can=$inslis[$i]['can'];
								}
								
								$peso = $pesoaux;
								
								//guardar prescripcion central de mezclas
								if($NPT_origen=="ordenes")
								{
									guardarPrescripcionNPTCentralMezclas($NPT_historia,$NPT_ingreso,$NPT_articulo,$NPT_ido, $inslis[$i]['cod'], $inslis[$i]['can']);
								}
							}
							else
							{
								$can=$inslis[$i]['can'];
								$inslis[$i]['fac']=1;
							}
							grabarInsumo($productos[0]['cod'], $inslis[$i]['cod'], $can, $wusuario,$inslis[$i]['fac'], @$inslis[$i]['app']);
						}
						else
						{
							$can=0;
						}
					}

					$estado='creado';
				}
			}
		}
	}


	//si se ha seleccionado alguno de los hipervinculos sobre un producto ya creado
	if (isset ($realizar))
	{
		//la variable tiene el valor modificar o desactivar
		switch ($realizar)
		{
			case 'modificar':
			// VALIDAR QUE EL PRODUCTO NO HAYA SIDO UTILIZADO EN NINGUN LOTE, SINO DEBE SER DESACTIVADO Y CREAR UNO NUEVO
			$ver=consultarPermiso($productos[0]['cod']);
			if ($ver)
			{
				if ($tfd=='')
				{
					$tfd=0;
				}
				if ($tfh=='')
				{
					$tfh=0;
				}
				$exp=explode('-',$tippro);
				$tve=$tvd*24+$tvh;
				$tin=$tfd*60+$tfh;

				If (!isset($peso))
				{
					$peso='';
				}
				if(!isset($purga))
				{
					$purga='';
				}
				if(!isset($des))
				{
					$des='';
				}
				if (!isset($inslis) or count($inslis)<2)
				{
					pintarAlert1('Debe ingresar al menos dos insumos para modificar el producto');
				}
				else
				{
					//2007-07-09 se agrega que se pueda modificar foto y neve
					modificarProducto($productos[0]['cod'], $productos[0]['nom'], $productos[0]['gen'], $presentacion, $via, $tin, $tve, $fecha, $exp[0], $des, $foto, $neve, $peso, $purga);
					
					$cco = centroCostos();
					$unidad = consultarUnidadInsumoMaximo($inslis);
					
					
					if( !empty($vol) ){
						$fraccion = $vol+$purga;
					}
					else{
						$fraccion =  consultarFraccionProducto( $unidad, $inslis );
					}
					
					$unidad = explode( "-", $unidad );
					
					actualizarFraccionArticulo($productos[0]['cod'], $fraccion, $unidad[0], $tve/24, $cco );
					
					borrarInsumos($productos[0]['cod']);
					
					if ($nutri=='on')
					{
						ponerPrescripcionesEnCeroNPT($productos[0]['cod'],$historia,$rowOrigenOrdenes['Enuing'],$rowOrigenOrdenes['Enuart'],$rowOrigenOrdenes['Enuido'], $inslis[$i]['cod'], $inslis[$i]['can']);
					}
				
					//se modifican uno a uno los insumos
					foreach ($inslis as $i => $value)
					{
						if ($inslis[$i]['can']>0 and $inslis[$i]['can']!='')
						{
							if ($nutri=='on')
							{
								if ($peso=='' or $inslis[$i]['ppe']!='on' or $peso==0)
								{
									$pes=1;
									$inslis[$i]['fac']=1;
									if( $peso == '' || $peso == 0 ){
										$pesoaux  = 1;
									}
								}
								else
								{
									$pes=$peso;
									$pesoaux  = $peso;
								}
								if($purga=='')
								{
									$pu=0;
								}
								else
								{
									$exp=explode('-',$purga);
									$pu=$exp[0];
								} 
								
								$volnp = consultarVolNoPurgar( $inslis, $vol, @$pesoaux );
								
//								if ($inslis[$i]['mat']!='on')
								if ($inslis[$i]['mat']!='on' && $inslis[$i]['app'] == 'on')
								{	
									$can=round($inslis[$i]['can']*$pes*(float)$inslis[$i]['fac']+(($inslis[$i]['can']*$pes*(float)$inslis[$i]['fac']*$pu)/($vol-$volnp)),2);
								}
								else if( $inslis[$i]['mat']!='on' ){
									$can=round($inslis[$i]['can']*$pes*(float)$inslis[$i]['fac'],2);
								}
								else
								{
									$can=$inslis[$i]['can'];
								}
								
								
								
								//consultar origen
								$qOrigenOrdenes = "SELECT Enuhis,Enuing,Enuart,Enuido 
													 FROM ".$bd."_000214 
													WHERE Enuhis='".$historia."' 
													  AND Enucnu='".$productos[0]['cod']."' 
													  AND Enuord='on' 
													  AND Enurea='on' 
													  AND Enuest='on';";
													  
								$resOrigenOrdenes = mysql_query($qOrigenOrdenes,$conex);
								$numOrigenOrdenes = mysql_num_rows($resOrigenOrdenes);					  
								
								if($numOrigenOrdenes>0)
								{
									$rowOrigenOrdenes = mysql_fetch_array($resOrigenOrdenes);
									actualizarPrescripcionNPTCentralMezclas($historia,$rowOrigenOrdenes['Enuing'],$rowOrigenOrdenes['Enuart'],$rowOrigenOrdenes['Enuido'], $inslis[$i]['cod'], $inslis[$i]['can']);
								}
							}

							else
							{
								$can=$inslis[$i]['can'];
								$inslis[$i]['fac']=1;
							}
							// grabarInsumo($productos[0]['cod'], $inslis[$i]['cod'], $can, $wusuario, $inslis[$i]['fac']);
							grabarInsumo($productos[0]['cod'], $inslis[$i]['cod'], $can, $wusuario, $inslis[$i]['fac'], @$inslis[$i]['app']);
						}
						else
						{
							$can=0;
						}

					}
					
					$estado='modificado';
				}
			}
			else
			{
				pintarAlert1('El producto no puede ser modificado pues ha sido utilizado en un lote, si desea puede desactivarlo y crear uno nuevo');
			}
			break;

			case 'desactivar':
			desactivarProducto($productos[0]['cod']);
			$estado='desactivado';
			break;

		}
	}
	
	if($esDA=="on" && $parcon!="")
	{
		$inslis = array();
	}

	//se ha ingresado un parametro de busqueda de un producto
	if (isset($parcon) and $parcon!='')
	{
		//si la forma de busqueda es por insumo
		if ($forcon=='Insumo')
		{ 
			$consultas=consultarPorductosXInsumo($parcon, $parcon2, $insfor);
		}
		else
		{
			$consultas=consultarCentral($parcon, $forcon);
		}

		//si se encontraron resultados de la busqueda
		if ($consultas)
		{
			$productos[0]['cod']=$consultas[0]['cod'];
			$productos[0]['nom']=$consultas[0]['nom'];
			$productos[0]['gen']=$consultas[0]['gen'];
			$productos[0]['pre']=$consultas[0]['pre'];
			$presentacion=$consultas[0]['pre'];
			consultarProducto($productos[0]['cod'], $via, $tfd, $tfh, $tvd, $tvh, $fecha, $inslis, $tippro, $estado, $foto, $neve, $des, $peso, $purga, $historia, $volumen, $insti);
			$producto=$productos[0]['cod']."-".$productos[0]['nom']."-".$productos[0]['gen']."-".$productos[0]['pre'];
		}
	}
	else if (isset($consulta) and $consulta!='') //se ha seleccionado del vector de resultados de la busqueda
	{
		$exp=explode('-',$consulta);
		$productos[0]['cod']=$exp[0];
		$productos[0]['nom']=$exp[1];
		$productos[0]['gen']=$exp[2];
		$productos[0]['pre']=$exp[3].'-'.$exp[4];
		$presentacion=$exp[3].'-'.$exp[4];
		consultarProducto($productos[0]['cod'], $via, $tfd, $tfh, $tvd, $tvh, $fecha, $inslis, $tippro, $estado, $foto, $neve, $des, $peso, $purga, $historia, $volumen, $insti);
		$producto=$productos[0]['cod']."-".$productos[0]['nom']."-".$productos[0]['gen']."-".$productos[0]['pre'];
	}


	//consultamos los tipos de produto
	if(!isset($tippro))
	{
		$tipos=consultarTipos('');
	}
	else
	{
		$tipos=consultarTipos($tippro);
	}

	if (!isset($tfh))
	{
		$via='';
		$tfd='';
		$tfh='';
		$tvd='';
		$tvh='';
		$des='';
		$fecha=date('Y-m-d');
	}

	$exp=explode('-',$tipos[0]);
	if ($exp[2]=='CODIFICADO') //si el timepo de producto seleccionado e codificado
	{
		$codi='on';
		if (isset($parbus) and $parbus!='') //se ha ingresado el parametro para buscar el producto en los articulos de unix
		{ 
			$productos=consultarProductos($parbus, $forbus);
			$presentacion=$productos[0]['pre'];
			$existe=consultarExistencia($productos[0]['cod']);
			if ($existe) //si existe ya en la central
			{
				consultarProducto($productos[0]['cod'], $via, $tfd, $tfh, $tvd, $tvh, $fecha, $inslis, $tippro, $estado, $foto, $neve, $des, $peso, $purga, $historia, $volumen, $insti);
				$producto=$productos[0]['cod']."-".$productos[0]['nom']."-".$productos[0]['gen']."-".$productos[0]['pre'];
			}
		}
		else
		{
			if (isset($producto) && $producto!='' && !empty($producto) ) //si se ha escogido un producto codificado del maestro de unix
			{

				$exp=explode('-',$producto);
				$existe=consultarExistencia($exp[0]);
				if ($existe) //se valida si ya existe en la central
				{
					consultarProducto($exp[0], $via, $tfd, $tfh, $tvd, $tvh, $fecha, $inslis, $tippro, $estado, $foto, $neve, $des, $peso, $purga, $historia, $volumen, $insti);
				}
				$productos[0]['cod']=$exp[0];
				$productos[0]['nom']=$exp[1];
				$productos[0]['gen']=$exp[2];
				$productos[0]['pre']=$exp[3].'-'.$exp[4];
				$presentacion=$exp[3].'-'.$exp[4];

			}
			else
			{
				$productos='';
//				$productos=array();
			}
		}
		$compu='off'; //no es nombre compuesto, el producto es codificado

		if (isset($presentacion)) //se ha seleccionado la unidad de trabajo del producto
		{
			$presentaciones[0]=$presentacion;
		}
		else //se buscan las diferentes unidades para drop down
		{
			$presentaciones=consultarPresentaciones('', $tipos[0]);
		}
		$nutri='off';
	}
	else //el producto no es codificado
	{ 
		$codi='off';
		$clase=consultarClase($tipos[0]); //se ve si el producto se llama segun los insumos que lo componen
		$exp=explode('-',$clase);
		if ($exp[2]=='on') //si es nombre compuesto
		{
			//si aun no se ha determinado en codigo
			if(!isset($productos[0]['cod']) or $productos[0]['cod']=='' or ( substr($productos[0]['cod'],0,2)!=$exp[0] and !consultarExistencia($productos[0]['cod']) ) )
			{
				$productos[0]['cod']=$exp[0];
				
				if (isset($crear))
				{
					$exp[1]=$exp[1]-1;
				}
				for ($i=0; $i<(4-strlen($exp[1])); $i++)
				{
					$productos[0]['cod']=$productos[0]['cod'].'0';
				}
 
				$productos[0]['cod']=$productos[0]['cod'].$exp[1];
				$presentaciones=consultarPresentaciones('', $tipos[0]);
			}
			else
			{
				if (isset($presentacion)) //si ya se ha seleccionado la unidad de trabajo
				{
					$presentaciones=consultarPresentaciones($presentacion, $tipos[0]);
				}
				else
				{
					$presentaciones=consultarPresentaciones('', $tipos[0]);
				}
			}
			$contador=0;
			if (isset($inslis)) //si ya se han ingresado insumos se puede construir el nombre del producto
			{
				foreach ($inslis as $i => $value) //se recorre vector de insumos
				{
					//si esta seleccionado el check box como insumo que hace parte del nombre
					if(isset($inslis[$i]['pri'])and (!isset($eli) or (isset($eli) and $eliminar!=$i)))
					{
						$exp=explode('-',$inslis[$i]['pre'] );
						if ($contador==0)
						{
							$productos[0]['nom']=$inslis[$i]['nom'].' '.$inslis[$i]['can'].' '.$exp[0];
							$productos[0]['gen']=$inslis[$i]['gen'].' '.$inslis[$i]['can'].' '.$exp[0];
						}
						else
						{
							$productos[0]['nom']=$productos[0]['nom'].' '.$inslis[$i]['nom'].' '.$inslis[$i]['can'].' '.$exp[0];
							$productos[0]['gen']=$productos[0]['gen'].' '.$inslis[$i]['nom'].' '.$inslis[$i]['can'].' '.$exp[0];
						}
						$inslis[$i]['pri']='checked';
						$contador++;
					}
					else
					{
						$inslis[$i]['pri']='';
					}
				}
			}

			//si se ha ingresado un insumo con su cantidad, se incorpora en la lista de insumos
			if(isset($insumo) and $insumo!='' and isset($cantidad) and $cantidad!='')//$accion==1
			{
				$exp=explode('-',$insumo);
				if ($contador==0)
				{
					$productos[0]['nom']=$exp[1].' '.$cantidad.' '.$exp[3];
					$productos[0]['gen']=$exp[2].' '.$cantidad.' '.$exp[3];
				}
				else
				{
					$productos[0]['nom']=$productos[0]['nom'].' '.$exp[1].' '.$cantidad.' '.$exp[3];
					$productos[0]['gen']=$productos[0]['gen'].' '.$exp[2].' '.$cantidad.' '.$exp[3];
				}
				$contador++;
			}

			if($contador==0 and (!isset($productos[0]['nom'])or isset($eli)))
			{
				$productos[0]['nom']='';
				$productos[0]['gen']='';
			}
			$productos[0]['pre']='';
			$compu='on';
			$nutri='off';
		}
		else //el producto no tiene nombre compuesto, se llama como el indicador lo dice en tabla 000001
		{
			//si no esta setiado el codigo del producto
			If (!isset($productos[0]['cod']) or $productos[0]['cod']=='' or substr($productos[0]['cod'],0,2)!=$exp[0])
			{
				$productos[0]['cod']=$exp[0];
				if (isset($crear))
				{
					$exp[1]=$exp[1]-1;
				}

				for ($i=0; $i<(4-strlen($exp[1])); $i++)
				{
					$productos[0]['cod']=$productos[0]['cod'].'0';
				}

				$productos[0]['cod']=$productos[0]['cod'].$exp[1];
				$presentaciones=consultarPresentaciones('', $tipos[0]);

			}
			else
			{
				if (isset($presentacion))
				{
					$presentaciones=consultarPresentaciones($presentacion, $tipos[0]);

				}
				else
				{
					$presentaciones=consultarPresentaciones('', $tipos[0]);
				}
			}
			$exp2=explode('-',$tipos[0]);
			$productos[0]['nom']=$exp2[1];
			$productos[0]['gen']=$exp2[1];
			$productos[0]['pre']='';
			$compu='off';


			//nueva funcion de modificacion de 2007-07-12 para cargar los insumos tipicos de una nutricion
			if (!isset($inslis))
			{
				if(!isset($volumen))
				{
					$volumen='';
				}

				$inslis=consultarNutriciones($tipos[0], $volumen);
				//se incializan los varoles de una nutricion
				$via='03-Intravenoso';
				$tfd=24;
				$tvd=3;

			}
			$nutri='on';

			
			
			
			
			//nueva funcion de modificacion de 2007-08-21 para asociar a historia clinica
			if((isset($historia) and $historia!='') or (isset($insti) and $insti!=''))
			{
				if($historia=='')
				{
					//consultamos si la institucion seleccionada esta en el maestro de empresas
					$res=consultarInstitucion($insti, $mensaje);
					if (!$res)
					{
						$historia=strtoupper($historia);
						$pac='PACIENTE EXTERNO';
					}
				}
				else
				{
					$res=validarHistoria('', $historia, $ingreso, $mensaje, $pac, $habitacion);
					if (!$res)
					{
						pintarAlert1($mensaje);
					}
				}

				//consultamos las instituciones en la seleccion
				$instituciones=consultarInstituciones($insti);
			}
			else
			{
				if($estado!='inicio')
				{
					$historia='NO FUE INGRESADA';
					$pac='NO FUE INGRESADA';
				}
				$instituciones=consultarInstituciones('');
			}

			$vol=0;
			
			if (isset($inslis))
			{
				foreach ($inslis as $i => $value) //se recorre vector de insumos
				{
					if (!isset($peso) or $peso=='' or $inslis[$i]['ppe']!='on' or $peso==0)
					{
						$pe=1;
						$inslis[$i]['fac']=1;
					}
					else
					{
						$pe=$peso;
						if(!isset($inslis[$i]['fac']) or $inslis[$i]['fac']==1)
						{
							$inslis[$i]['fac']=(float)consultarFactor($inslis[$i]['cod'],$pe,$NPT_tiempoInfusion);
						}
					}
					
					
					$agua=consultarAgua($inslis[$i]['cod']);
					if ($agua)
					{
						$aguap=$i;
					}
					else
					{
						if($inslis[$i]['mat']!='on')
						{
							$vol=$vol+(float)$inslis[$i]['can']*$pe*(float)$inslis[$i]['fac'];
						}

					}
				}

				if(!isset($volumen))
				{
					$volumen=0;
				}

				if(isset($aguap))
				{
					if($volumen=="")
					{
						$volumen=0;
					}
					if($inslis[$aguap]['can']=="")
					{
						$inslis[$aguap]['can']=0;
					}
					
					$inslis[$aguap]['can']=round(($volumen-$vol),1);
					if ($inslis[$aguap]['can']<=0)
					{
						$inslis[$aguap]['can']='';
					}
					else
					{
						$vol=$vol+$inslis[$aguap]['can'];
					}
				}
			}
		}
	}
	
	if (isset($via)) //se consulta el dropdown de vias de administracion
	{
		$vias=consultarVias($via);
	}
	else
	{
		$vias=consultarVias('');
	}

	if (isset($des)) //se consulta el dropdown de descripcion
	{
		$dess=consultarDes($des);
	}
	else
	{
		$dess=consultarDes('');
	}

	if(!isset($fecha) or $fecha=='' ) //se inicializa la fecha, si no se ha ingresado ya
	{
		if (!isset($producto))
		{
			$fecha='';
		}
		else
		{
			$fecha=date('Y-m-d');
		}
	}

	if (!isset($consultas))
	{
//		$consultas='';
		$consultas=array();
	}

	if (!isset($forcon))
	{
		$forcon='Codigo';
	}
	if ($forcon=='Insumo')//se pinta el formulario de busqueda de productos
	{
		pintarBusqueda($consultas,2, $forcon);
	}
	else if ($forcon=='Paciente')
	{
		pintarBusqueda($consultas,3, $forcon);
	}
	else if ($forcon=='Institucion')
	{
		pintarBusqueda($consultas,4, $forcon);
	}
	else
	{
		pintarBusqueda($consultas,1, $forcon);
	}

	if (!isset($peso))
	{
		$peso='';
	}

	if (!isset($purga))
	{
		$purga='';
	}

	if (!isset($pac))
	{
		$pac='';
	}

	if (!isset($historia))
	{
		$historia='';
	}

	if (!isset($volumen))
	{
		$volumen='';
	}

	if (!isset($instituciones))
	{
		$instituciones[0]['cod']='';
	}
	
	//se va a realizar el descarte de las vias que se debe hacer diariamente
	realizarDescarte('1051', $wusuario);

	if($NPT_tiempoInfusion!="")
	{
		$tfd = $NPT_tiempoInfusion;
	}

	// pintarFormulario($tipos, $codi, $productos, $presentaciones, $fecha, $via, $tvd, $tvh, $tfd, $tfh, $estado, $compu, $consultas, $vias, $foto, $neve, $dess, $nutri, $peso, $purga, $pac, $historia, $volumen, $instituciones,$pintarListaNPTPendientes,$wemp_pmla,$NPT_origen,$pintarListaDAPendientes,$DA_tipo);
	pintarFormulario($tipos, $codi, $productos, $presentaciones, $fecha, $via, $tvd, $tvh, $tfd, $tfh, $estado, $compu, $consultas, $vias, $foto, $neve, $dess, $nutri, $peso, $purga, $pac, $historia, $volumen, $instituciones,$pintarListaNPTPendientes,$wemp_pmla,$NPT_origen,$pintarListaDAPendientes,$DA_tipo,$DA_historia,$DA_ingreso,$DA_articulo,$DA_ido,$wronda,$wfecharonda);
	
	if($DA_articuloCM!="" && $DA_tipo != "Generica")
	{
		$insumosDA=consultarInsumos($DA_articuloCM, "Codigo");
		
		if($insumosDA != false)
		{
			$insumo= $insumosDA[0]['cod']."-".$insumosDA[0]['nom']."-".$insumosDA[0]['gen']."-".$insumosDA[0]['pre'];
			$cantidad = $DA_cantidad;
		}
		else
		{
			echo "<p align='center'><b>Debe configurar los siguientes insumos en cenpro_000002 (Maestro de articulos de la central) para poder crear la DA: ".$DA_articuloCM."</b></p>";
		}
		
	}
	
	if (isset($parbus2) and $parbus2!='')//para consultar un insumo a ingresar al producto
	{
		$insumos=consultarInsumos($parbus2, $forbus2);
	}
	else
	{
		if (isset($insumo) and $insumo!='' and isset($cantidad) and $cantidad!='')//$accion==1
		{
			$exp=explode('-',$insumo);
			if (!isset($inslis) or !$inslis)
			{
				$inslis[0]['cod']=$exp[0];
				$inslis[0]['nom']=$exp[1];
				$inslis[0]['gen']=$exp[2];
				$inslis[0]['pre']=$exp[3].'-'.$exp[4];
				$inslis[0]['can']=$cantidad;
				$inslis[0]['pri']='checked';

				if($nutri=='on')
				{
					consultarMas($inslis[0]);
				}
			}
			else
			{
				foreach ($inslis as $i => $value)
				{
					if($inslis[$i]['cod']==$exp[0])
					{
						$inslis[$i]['can']=$inslis[$i]['can']+$cantidad;
						$repetido='on';

						if($inslis[$i]['mat']!='on')
						{
							$inslis[$aguap]['can']=round(($inslis[$aguap]['can']-$cantidad),1);
							if ($inslis[$aguap]['can']<=0)
							{
								$inslis[$aguap]['can']='';
							}
							else
							{
								$vol=$vol+$cantidad;
							}
						}
					}
				}
				if (!isset($repetido))
				{
					$inslis[count($inslis)]['cod']=$exp[0];
					$inslis[count($inslis)-1]['nom']=$exp[1];
					$inslis[count($inslis)-1]['gen']=$exp[2];
					$inslis[count($inslis)-1]['pre']=$exp[3].'-'.$exp[4];
					$inslis[count($inslis)-1]['can']=$cantidad;
					$inslis[count($inslis)-1]['pri']='checked';

					if($nutri=='on')
					{
						consultarMas($inslis[count($inslis)-1]);
						if($inslis[$i]['mat']!='on')
						{
							if(isset($aguap))
							{
								$inslis[$aguap]['can']=round(($inslis[$aguap]['can']-$cantidad),1);
								if ($inslis[$aguap]['can']<=0)
								{
									$inslis[$aguap]['can']='';
								}
								else
								{

									$vol=$vol+$cantidad;
								}
							}
						}
					}
				}
			}
			$insumos='';
//			$insumos=array();
		}
		else if (isset($insumo) and $insumo!='' && !empty($insumo) )
		{
			$exp=explode('-',$insumo);
			$insumos=consultarInsumos($exp[0], 'Codigo' );
		}
		else
		{
			$insumos='';
//			$insumos=array();
		}

		if (isset($eli))
		{
			$inslis=eliminarInsumo($inslis, $eliminar);
			if ($inslis==false)
			{
				$inslis='';
//				$inslis=array();
			}
		}
	}
	if (!isset($inslis))
	{
		$inslis='';
//		$inslis=array();
	}

	if (!isset($vol))
	{
		$vol=0;
	}
	
	$esDosisAdaptada = consultarSiTipoEsDA($tipos[0]);
	$rangoEtario = consultarRangoEtario($DA_historia);
	
	if($insumosDA != false && count($inslis)>0)
	{
		echo "<input type='hidden' id='DA_historia' name='DA_historia' value='".$DA_historia."'>";
		echo "<input type='hidden' id='DA_ingreso' name='DA_ingreso' value='".$DA_ingreso."'>";
		echo "<input type='hidden' id='DA_articulo' name='DA_articulo' value='".$DA_articulo."'>";
		echo "<input type='hidden' id='DA_ido' name='DA_ido' value='".$DA_ido."'>";
		echo "<input type='hidden' id='DA_tipo' name='DA_tipo' value='".$DA_tipo."'>";
		echo "<input type='hidden' id='wronda' name='wronda' value='".$wronda."'>";
		echo "<input type='hidden' id='wfecharonda' name='wfecharonda' value='".$wfecharonda."'>";
		echo "<input type='hidden' id='DA_cco' name='DA_cco' value='".$DA_cco."'>";
		
		// validar si debe mostrar diluyentes o jeringas
		switch ($rangoEtario) {
			case "N":
				
				$arrayJeringas = consultarJeringas();
				
				if(count($arrayJeringas)>0)
				{
					$inslis = array_merge($inslis,$arrayJeringas);
				}
		
				break;
				
			case "P":
				
				$arrayJeringas = consultarJeringas();
				
				if(count($arrayJeringas)>0)
				{
					$inslis = array_merge($inslis,$arrayJeringas);
				}
				
				$arrayDiluyentes = consultarDiluyentes($DA_articuloCM,$DA_cantidad,$DA_historia,$DA_cco);
				
				if(count($arrayDiluyentes)>0)
				{
					$inslis = array_merge($inslis,$arrayDiluyentes);
				}
				break;
				
			case "A":
				
				$arrayDiluyentes = consultarDiluyentes($DA_articuloCM,$DA_cantidad,$DA_historia,$DA_cco);
				
				if(count($arrayDiluyentes)>0)
				{
					$inslis = array_merge($inslis,$arrayDiluyentes);
				}
				break;
			default: 
			
			break;
		}
	}
	
	if( (isset($pac) and $pac!='') or $nutri!='on' or $codi=='on')
	{
		// pintarInsumos($insumos,$inslis, $compu, $nutri, $peso, $purga, $estado, $vol);
		
		IF($nutri=="on")
		{
			echo "<input type='hidden' id='NPT_historia' name='NPT_historia' value='".$NPT_historia."'>";
			echo "<input type='hidden' id='NPT_ingreso' name='NPT_ingreso' value='".$NPT_ingreso."'>";
			echo "<input type='hidden' id='NPT_articulo' name='NPT_articulo' value='".$NPT_articulo."'>";
			echo "<input type='hidden' id='NPT_ido' name='NPT_ido' value='".$NPT_ido."'>";
		}
				
		if($nutri=='on' && $NPT_origen=="ordenes")
		{
			pintarInsumosNPT($insumos,$inslis, $compu, $nutri, $peso, $purga, $estado, $vol, $NPT_historia, $NPT_ingreso, $NPT_articulo, $NPT_ido,$hacerSubmitInsumosNPT,$tfd);
		}
		elseif($esDosisAdaptada == "on" && $DA_tipo!="Generica")
		{
			pintarInsumosDA($insumos,$inslis, $compu, $nutri, $peso, $purga, $estado, $vol,$tfd,$hacerSubmitInsumosDA,$DA_historia,$DA_ingreso,$DA_articulo,$DA_ido,$DA_cantidadSinPurga,$wronda,$wfecharonda,$DA_cco);
		}
		else
		{
			if($esDosisAdaptada == "on")
			{
				echo "<input type='hidden' id='DA_historia' name='DA_historia' value='".$DA_historia."'>";
				echo "<input type='hidden' id='DA_ingreso' name='DA_ingreso' value='".$DA_ingreso."'>";
				echo "<input type='hidden' id='DA_articulo' name='DA_articulo' value='".$DA_articulo."'>";
				echo "<input type='hidden' id='DA_ido' name='DA_ido' value='".$DA_ido."'>";
				echo "<input type='hidden' id='DA_tipo' name='DA_tipo' value='".$DA_tipo."'>";
				echo "<input type='hidden' id='wronda' name='wronda' value='".$wronda."'>";
				echo "<input type='hidden' id='wfecharonda' name='wfecharonda' value='".$wfecharonda."'>";
				echo "<input type='hidden' id='DA_cco' name='DA_cco' value='".$DA_cco."'>";
			}
			
			
			pintarInsumos($insumos,$inslis, $compu, $nutri, $peso, $purga, $estado, $vol,$tfd);
		}
	}
	
	
	$tablaCantNPT = mostrarCantidadNPTPendientes("off");
	
	echo $tablaCantNPT;
	
	$tablaCantDA = mostrarCantidadDAPendientes("off");
	
	echo $tablaCantDA;

}


/*===========================================================================================================================================*/


?>
</body >
</html >

<?php
}
?>
