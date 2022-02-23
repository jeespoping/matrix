<head>
  <title>APLICACION DE CENTRAL DE MEZCLAS</title>
  
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo3{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#FFDBA8;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}	
    	.texto2{color:#003366;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.texto3{color:#003366;background:#FFFFFF;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto4{color:#003366;background:#f5f5dc;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto5{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	
   </style>
  
   <script type="text/javascript">
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

   function enter7()
   {
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

   function validarEntero(valor)
   {
   	valor = parseInt(valor);
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

   	if (document.producto.elements[23].value=='')
   	{
   		document.producto.elements[21].focus();
   	}
   	else
   	{
   		document.producto.elements[24].focus();
   	}

   }

    </script>
  
</head>

<body onload="hacerFoco()">

<?php
include_once("conex.php");

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
15. Consulta de cargos o devolucion de cargos a paciente+
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

4. Observaciones

Aun no se ha realizado:
* Busqueda de productos por paciente (en cen_mez.php)
* Hay que definir como se realizara la numeracion del lote y que pasara si se pasa de 5 digitos
========================================================DOCUMENTACION PROGRAMA================================================================================*/
/*

1. AREA DE VERSIONAMIENTO

Nombre del programa:cen_Mez.php
Fecha de creacion: 2007-06-15
Autor: Carolina Castano P
Ultima actualizacion: 2007-07-06


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
			$productos[$i]['nom']=$row[1];
			$productos[$i]['gen']=$row[2];
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
		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
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

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
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

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
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
	}

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$productos[$i]['cod']=$row[0];
			$productos[$i]['nom']=$row[1];
			$productos[$i]['gen']=$row[2];
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
			$productos[$i]['nom']=$row[1];
			$productos[$i]['gen']=$row[2];
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
			$productos[$i]['nom']=$row[1];
			$productos[$i]['gen']=$row[2];
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
function consultarProducto($codigo, &$via, &$tfd, &$tfh, &$tvd, &$tvh, &$fecha, &$inslis, &$tippro, &$estado, &$foto, &$neve, &$des)
{
	global $conex;
	global $wbasedato;

	$q= " SELECT Artvia, Arttin, Arttve, Artfec, Arttip, Artest, Artfot, Artnev, Tipdes, Tipcdo, Artdes "
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
	}

	$q= " SELECT Pdeins, Pdecan, Artcom, Artgen, Artuni, Unides "
	."       FROM ".$wbasedato."_000003, ".$wbasedato."_000002, farstore_000002 "
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeest = 'on' "
	."       AND Pdeins= Artcod "
	."       AND Artuni= Unicod "
	."       AND Uniest='on' "
	."    Order by 1 ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);
			$inslis[$i]['cod']=$row[0];
			$inslis[$i]['nom']=$row[2];
			$inslis[$i]['gen']=$row[3];
			$inslis[$i]['pre']=$row[4].'-'.$row[5];
			$inslis[$i]['can']=$row[1];
			//$inslis[$i]['pri']='';
		}
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
	."        AND Codigo = '01' "
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
function validarComposicion($inslis)
{
	global $conex;
	global $wbasedato;

	$q= "   SELECT Artcod from ".$wbasedato."_000002 "
	."    WHERE Artest = 'on' ";

	for ($i=0; $i<count($inslis); $i++)
	{
		$q = $q. "AND Artcod in (SELECT Pdepro FROM ".$wbasedato."_000003 where Pdeins='".$inslis[$i]['cod']."' and Pdeest='on' and Pdecan='".$inslis[$i]['can']."' )";
	}

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
function grabarProducto($codigo, $nom, $gen, $presentacion, $via, $tin, $tve, $fecha, $tip, $usuario, $foto, $neve, $des)
{
	global $conex;
	global $wbasedato;

	$exp=explode('-',$presentacion);
	$q= " INSERT INTO ".$wbasedato."_000002 (   Medico       ,   Fecha_data,                  Hora_data,              Artcod,      Artcom ,     Artgen   ,   Artuni  ,             Artvia ,   Arttin ,         Arttve    ,  Artfec,       Arttip,  Artcon, Artfot, Artnev, Artest, Artdes, Seguridad) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$nom."', '".$gen."' , '".$exp[0]."' , '".$via."', '".$tin."'  , '".$tve."' ,  '".$fecha."', '".$tip."',  0, '".$foto."', '".$neve."', 'on', '".$des."' ,'C-".$usuario."') ";


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
function grabarInsumo($procod, $inscod, $inscan, $usuario)
{
	global $conex;
	global $wbasedato;

	$q= " INSERT INTO ".$wbasedato."_000003 (   Medico       ,   Fecha_data,                  Hora_data,              Pdepro,      Pdeins ,          Pdecan   ,  Pdeest, Seguridad) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$procod."', '".$inscod."', '".$inscan."' ,  'on', 'C-".$usuario."') ";


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
function modificarProducto($codigo, $nom, $gen, $presentacion, $via, $tin, $tve, $fecha, $tip, $des)
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
	."      	Arttip = '".$tip."' "
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

	global $wemp_pmla;
	echo "<table ALIGN=CENTER width='50%'>";
	//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	echo "<tr><td class='titulo1'>PRODUCCION CENTRAL DE MEZCLAS</td></tr>";
	echo "<tr><td class='titulo2'>Fecha: ".date('Y-m-d')."&nbsp Hora: ".(string)date("H:i:s")."</td></tr></table></br>";

	echo "<table ALIGN=CENTER width='90%' >";
	//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	echo "<tr><a href='cen_Mez.php?wbasedato=cen_mez'><td class='texto6' width='15%'>PRODUCTOS</td></a>";
	echo "<a href='lotes.php?wbasedato=lotes.php'><td class='texto5' width='15%'>LOTES</td></a>";
	echo "<a href='cargos.php?wbasedato=lotes.php&tipo=C'><td class='texto5' width='15%'>CARGOS A PACIENTES</td></a>";
	echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<a href='pos.php?wbasedato=lotes.php&tipo=A'><td class='texto5' width='15%'>VENTA EXTERNA</td></TR></a>";
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
	echo "<form name='producto2' action='cen_Mez.php' method=post>";
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
	}

	echo "&nbsp; Resultados: <select name='consulta' class='texto5' onchange='enter7()'>";
	if ($consultas[0]['cod']!='')
	{
		for ($i=0;$i<count($consultas);$i++)
		{
			echo "<option value='".$consultas[$i]['cod']."-".$consultas[$i]['nom']."-".$consultas[$i]['gen']."-".$consultas[$i]['pre']."'>".$consultas[$i]['cod']."-".$consultas[$i]['nom']."</option>";
		}
	}
	else
	{
		echo "<option value=''></option>";
	}
	echo "</select>";
	echo "</td></tr>";
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
function pintarFormulario($tipos, $codi, $productos, $presentaciones, $fecha, $via, $tvd, $tvh, $tfd, $tfh, $estado, $compu, $consultas, $vias, $foto, $neve, $dess)
{
	echo "<form name='producto3' action='cen_Mez.php' method=post>";
	echo "<tr><td colspan=3 class='titulo3' align='center'><INPUT TYPE='submit' NAME='NUEVO' VALUE='Nuevo' class='texto5' ></td></tr>";
	echo "</table></form>";

	echo "<form name='producto' action='cen_Mez.php' method=post>";
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan=3 class='titulo3' align='center'><b>Informacion general del Producto</b></td></tr>";

	echo "<tr><td class='texto1' colspan='3' align='center'>Tipo: ";
	echo "<select name='tippro' onchange='enter1()' class='texto5'>";
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
		if ($productos[0]['cod']!='')
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

		echo "<tr><td class='texto2' colspan='1' align='left'>Codigo: <input type='TEXT' name='productos[0][cod]' value='".$productos[0]['cod']."' readonly='readonly' class='texto2' size='10'></td>";
		echo "<td class='texto2' colspan='2' align='left'>Nombre comercial: <input type='TEXT' name='productos[0][nom]' value='".$productos[0]['nom']."' readonly='readonly' class='texto2' size='50'></td></tr>";
		echo "<tr><td class='texto2' colspan='2' align='left'>Nombre genérico: <input type='TEXT' name='productos[0][gen]' value='".$productos[0]['gen']."' readonly='readonly' class='texto2' size='50'></td>";
	}
	else
	{

		$exp=explode('-',$tipos[0]);
		echo "<tr><td class='texto2' colspan='1' align='left'>Codigo: <input type='TEXT' name='productos[0][cod]' value='".$productos[0]['cod']."' readonly='readonly' class='texto2'>";
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

	echo "<td class='texto2' colspan='1' align='left'>Presentación: ";
	echo "<select name='presentacion' class='texto5' >";
	for ($i=0;$i<count($presentaciones);$i++)
	{
		echo "<option >".$presentaciones[$i]."</option>";
	}
	echo "</select>";
	echo "</td></tr>";
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

	echo "<td class='texto2' colspan='1' align='left'>Descripcion:<select name='des' class='texto5'>";
	for ($i=0;$i<count($dess);$i++)
	{
		echo "<option>".$dess[$i]."</option>";
	}
	echo "</select></td>";
	
	switch($estado)
	{
		case 'inicio':
		echo "<tr><td colspan=3 class='titulo3' align='center'><input type='checkbox' name='crear' class='titulo3'>Crear &nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Aceptar' class='texto5'></td></tr>";
		break;
		case 'creado':
		echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO HA SIDO CREADO EXITOSAMENTE &nbsp;&nbsp;<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1'>/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></tr>";
		break;
		case 'desactivado':
		echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO HA SIDO DESACTIVADO EXITOSAMENTE &nbsp;&nbsp;<a href='cen_Mez.php'>INICIAR</a> </td></tr>";
		break;
		case 'modificado':
		echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO HA SIDO MODIFICADO EXITOSAMENTE &nbsp;&nbsp;<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1'>/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></td></tr>";
		break;
		case 'Creado':
		echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO EXISTE ACTUALMENTE &nbsp;&nbsp;<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1'>/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></tr>";
		break;
		case 'Desactivado':
		echo "<tr><td colspan=3 class='titulo3' align='center'>PRODUCTO DESACTIVADO &nbsp;&nbsp;<a href='cen_Mez.php'>INICIAR</a> </td></tr>";
		break;

	}
	echo "<input type='hidden' name='estado' value='".$estado."'></td>";
	echo "<input type='hidden' name='tvh' value='0'></td>";
	echo "</table></br>";
}

/**
 * Pinta la lista de insumos para el producto
 *
 * @param unknown_type $insumos, vector de insumos encontrados para dorp down
 * @param unknown_type $inslis, vector de insumos que se han ingresado para el prodcuto
 * @param unknown_type $compu, si es compuesto el nombre o no
 */
function pintarInsumos($insumos, $inslis, $compu)
{
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan=4 class='titulo3' align='center'><b>Informacion detallada del Producto</b></td></tr>";


	echo "<tr><td class='texto1' colspan='4' align='center'>Buscar Insumo por: ";
	echo "<select name='forbus2' class='texto5'>";
	echo "<option>Codigo</option>";
	echo "<option>Nombre comercial</option>";
	echo "<option>Nombre generico</option>";
	echo "</select><input type='TEXT' name='parbus2' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter1()' class='texto5'></td> ";
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
	echo "<td class='texto1' colspan='2' align='center'>Cantidad: <input type='TEXT' name='cantidad' value=''  class='texto5' onchange='validarFormulario4()'><input type='TEXT' name='nompro' value='".$insumos[0]['pre']."'  class='texto5' >";
	echo "</td></tr>";
	echo "<tr><td colspan=4 class='texto1' align='center'><INPUT TYPE='button' NAME='buscar' VALUE='Agregar' onclick='enter()' class='texto5'></td></tr>";
	echo "<tr><td colspan=4 class='titulo3' align='center'>&nbsp</td></tr>";


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

		echo "<td class='texto2' colspan='1' align='center'>Cantidad</td>";
		echo "<td class='texto2' colspan='1' align='center'>Eliminar</td></tr>";

		for ($i=0;$i<count($inslis);$i++)
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

			echo "<td class='".$class."' colspan='1' align='center'><input type='TEXT' name='inslis[".$i."][can]' value='".$inslis[$i]['can']."'  class='texto3' size='5'><input type='TEXT' name='inslis[".$i."][pre]' value='".$inslis[$i]['pre']."'  class='texto3' size='20'></td>";
			echo "<td class='".$class."' colspan='1' align='center'><input type='checkbox' name='eli' class='texto3' onclick='enter2(".$i.")'></td></tr>";

		}
	}

	echo "<input type='hidden' name='accion' value='0'></td>";
	echo "<input type='hidden' name='realizar' value='0'></td>";
	echo "<input type='hidden' name='eliminar' value='0'></td>";
	echo "</table></br></form>";
}

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
	$wbasedato='cenpro';
	

	or die("No se ralizo Conexion");
	


	include_once("CENPRO/funciones.php");

	//pintarVersion(); //Escribe en el programa el autor y la version del Script.
	pintarTitulo();  //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts

	//consulto los datos del usuario de la sesion
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user)); //extraigo el codigo del usuario

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
			//se valida que se hallan ingresado almenos dos insumos
			if (!isset($inslis) or count($inslis)<2)
			{
				pintarAlert1('Debe ingresar al menos dos insumos para crear el producto');
			}
			else
			{
				//se valida que no exista un producto con la misma conposicion
				$val=validarComposicion($inslis);
				if ($val)
				{
					$forcon='Codigo';
					$parcon=$val;
					pintarAlert1('Ya existe un producto con dicha composicion');
				}
				else
				{
					$exp=explode('-',$tippro);
					$tve=$tvd*24+$tvh;
					$tin=$tfd*60+$tfh;
					$productos[0]['cod']=incrementarConsecutivo($exp[0], $productos[0]['cod']);
					grabarProducto($productos[0]['cod'], $productos[0]['nom'], $productos[0]['gen'], $presentacion, $via, $tin, $tve, $fecha, $exp[0], $wusuario, $foto, $neve, $des);
					//se graba uno a uno los insumos del producto
					for ($i=0; $i<count($inslis); $i++)
					{
						grabarInsumo($productos[0]['cod'], $inslis[$i]['cod'], $inslis[$i]['can'], $wusuario);
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
				modificarProducto($productos[0]['cod'], $productos[0]['nom'], $productos[0]['gen'], $presentacion, $via, $tin, $tve, $fecha, $exp[0], $des);
				borrarInsumos($productos[0]['cod']);
				//se modifican uno a uno los insumos
				for ($i=0; $i<count($inslis); $i++)
				{
					grabarInsumo($productos[0]['cod'], $inslis[$i]['cod'], $inslis[$i]['can'], $wusuario);
				}
				$estado='modificado';
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
			consultarProducto($productos[0]['cod'], &$via, &$tfd, &$tfh, &$tvd, &$tvh, &$fecha, &$inslis, &$tippro, &$estado, &$foto, &$neve, &$des);
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
		consultarProducto($productos[0]['cod'], &$via, &$tfd, &$tfh, &$tvd, &$tvh, &$fecha, &$inslis, &$tippro, &$estado, &$foto, &$neve, &$des);
		$producto=$productos[0]['cod']."-".$productos[0]['nom']."-".$productos[0]['gen']."-".$productos[0]['pre'];
	}


	//consultamos los tipos de produto
	if(!isset($tippro))
	{
		$tipos=consultarTipos('');
		$via='';
		$tfd='';
		$tfh='';
		$tvd='';
		$tvh='';
		$des='';

	}
	else
	{
		$tipos=consultarTipos($tippro);
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
				consultarProducto($productos[0]['cod'], &$via, &$tfd, &$tfh, &$tvd, &$tvh, &$fecha, &$inslis, &$tippro, &$estado, &$foto, &$neve, &$des);
				$producto=$productos[0]['cod']."-".$productos[0]['nom']."-".$productos[0]['gen']."-".$productos[0]['pre'];
			}
		}
		else
		{
			if (isset($producto) and $producto!='') //si se ha escogido un producto codificado del maestro de unix
			{

				$exp=explode('-',$producto);
				$existe=consultarExistencia($exp[0]);
				if ($existe) //se valida si ya existe en la central
				{
					consultarProducto($exp[0], &$via, &$tfd, &$tfh, &$tvd, &$tvh, &$fecha, &$inslis, &$tippro, &$estado, &$foto, &$neve, &$des);
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
	}
	else //el producto no es codificado
	{

		$codi='off';
		$clase=consultarClase($tipos[0]); //se ve si el producto se llama segun los insumos que lo componen
		$exp=explode('-',$clase);
		if ($exp[2]=='on') //si es nombre compuesto
		{
			//si aun no se ha determinado en codigo
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
				for ($i=0; $i<count($inslis); $i++) //se recorre vector de insumos
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
		$consultas='';
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
	else
	{
		pintarBusqueda($consultas,1, $forcon);
	}
	pintarFormulario($tipos, $codi, $productos, $presentaciones, $fecha, $via, $tvd, $tvh, $tfd, $tfh, $estado, $compu, $consultas, $vias, $foto, $neve, $dess);


	if (isset($parbus2) and $parbus2!='')//para consultar un insumo a ingresar al producto
	{
		$insumos=consultarInsumos($parbus2, $forbus2);
	}
	else
	{
		if (isset($insumo) and $insumo!='' and isset($cantidad) and $cantidad!='')//$accion==1
		{
			$exp=explode('-',$insumo);
			if (!isset($inslis))
			{
				$inslis[0]['cod']=$exp[0];
				$inslis[0]['nom']=$exp[1];
				$inslis[0]['gen']=$exp[2];
				$inslis[0]['pre']=$exp[3].'-'.$exp[4];
				$inslis[0]['can']=$cantidad;
				$inslis[0]['pri']='checked';
			}
			else
			{
				for ($i=0; $i<count($inslis); $i++)
				{
					if($inslis[$i]['cod']==$exp[0])
					{
						$inslis[$i]['can']=$inslis[$i]['can']+$cantidad;
						$repetido='on';
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
				}
			}
			$insumos='';
		}
		else if (isset($insumo) and $insumo!='')
		{
			$exp=explode('-',$insumo);
			$insumos=consultarInsumos($exp[0], 'Codigo');
		}
		else
		{
			$insumos='';
		}

		if (isset($eli))
		{
			$inslis=eliminarInsumo($inslis, $eliminar);
			if ($inslis==false)
			{
				$inslis='';
			}
		}
	}
	if (!isset($inslis))
	{
		$inslis='';
	}

	pintarInsumos($insumos,$inslis, $compu);
}
/*===========================================================================================================================================*/

?>


</body >
</html >
