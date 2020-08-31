<head>
  <title>RECIBOS DE CAJA Y NOTAS DEBITO Y CREDITO</title>
    
   <script type="text/javascript">
   function enter(bandera)
   {
   	document.forms.recibos_y_notas.bandera.value=bandera;
   	document.forms.recibos_y_notas.submit();
   }

   function enter8(bandera)
   {
   	document.forms.recibos_y_notas.bandera.value=bandera;
   	document.forms.recibos_y_notas.indicador.value=1;
   	document.forms.recibos_y_notas.cancon.value='';
   	document.forms.recibos_y_notas.submit();
   }

   function enter2(fecha)//pone en blanco el valor de fuente manual
   {
   	document.forms.recibos_y_notas.bandera.value=1;
   	document.forms.recibos_y_notas.wfcon.value='';
   	document.forms.recibos_y_notas.wfecdoc.value='';
   	document.forms.recibos_y_notas.submit();
   }

   function enter7(bandera)
   {
   	document.forms.recibos_y_notas.bandera.value=bandera;
   	document.forms.recibos_y_notas.canfac.value='';
   	document.forms.recibos_y_notas.cancon.value='';
   	document.forms.recibos_y_notas.indicador.value=1;
   	document.forms.recibos_y_notas.submit();
   }

   function enter6(bandera)
   {
   	document.forms.recibos_y_notas.bandera.value=bandera;
   	document.forms.recibos_y_notas.canfac.value=document.forms.recibos_y_notas.canbru.value-document.forms.recibos_y_notas.cancon.value;
   	document.forms.recibos_y_notas.cancon.value='';
   	document.forms.recibos_y_notas.submit();
   }

   function enter9( )//pone en blanco el valor de fuente manual
   {
   	document.forms.recibos_y_notas.bandera.value=1;
   	document.forms.recibos_y_notas.submit();
   }

   function enter5()
   {
   	document.forms.recibos_y_notas.bandera.value=5;
   	document.forms.recibos_y_notas.wfcon.value='';
   	document.forms.recibos_y_notas.submit();
   }

   function enter3()//pone en blanco el valor de fuente manual
   {
   	document.forms.recibos_y_notas.bandera.value=1;
   	if (document.forms.recibos_y_notas.wfuente.options[document.forms.recibos_y_notas.wfuente.selectedIndex].text!='')
   	{
   		document.forms.recibos_y_notas.wfcon.value=document.forms.recibos_y_notas.wfuente.options[document.forms.recibos_y_notas.wfuente.selectedIndex].text;
   		document.forms.recibos_y_notas.wfuente.options[document.forms.recibos_y_notas.wfuente.selectedIndex].text='';
   	}
   	document.forms.recibos_y_notas.submit();
   }

   function agregarCausa()
   {
   	document.recibos_y_notas.ingCau.value=1;
   	document.forms.recibos_y_notas.submit();
   }

   function eliminarCausa(i)
   {
   	document.recibos_y_notas.eliCau.value=1;
   	document.recibos_y_notas.index.value=i;
   	document.forms.recibos_y_notas.submit();
   }

   function hacerFoco(tipo)
   {
   	if (tipo==1)
   	{
   		if (document.forms.recibos_y_notas.bandera.value==2 && document.forms.recibos_y_notas.elements[14].name=='fuefac')
   		{
   			if (document.forms.recibos_y_notas.bandera.value==2 && document.forms.recibos_y_notas.elements[14].value!='')
   			{
   				if (document.forms.recibos_y_notas.elements[16].value!='' )
   				{
   					if (document.forms.recibos_y_notas.elements[22].name=='cancon' && document.forms.recibos_y_notas.elements[22].value!='')
   					{
   						document.recibos_y_notas.elements[22].focus();
   					}
   					else if (document.forms.recibos_y_notas.elements[22].name=='cancon' && document.forms.recibos_y_notas.elements[22].value=='')
   					{
   						if (document.forms.recibos_y_notas.elements[21].options[document.forms.recibos_y_notas.wfuente.selectedIndex].text!='' && document.forms.recibos_y_notas.elements[19].value!='')
   						{
   							document.recibos_y_notas.elements[22].focus();
   						}
   						else
   						{
   							document.recibos_y_notas.elements[19].focus();
   						}
   					}
   					else
   					{
   						document.recibos_y_notas.elements[22].focus();
   					}
   				}
   				else
   				{
   					document.recibos_y_notas.elements[16].focus();
   				}

   			}
   			else
   			{
   				document.recibos_y_notas.elements[14].focus();
   			}
   		}

   		if (document.forms.recibos_y_notas.bandera.value==2 && document.forms.recibos_y_notas.elements[11].name=='fuefac')
   		{
   			if (document.forms.recibos_y_notas.elements[11].value!='')
   			{
   				if (document.forms.recibos_y_notas.elements[16].type=='chekbox')
   				{
   					if (document.forms.recibos_y_notas.elements[16].options[document.forms.recibos_y_notas.wfuente.selectedIndex].text=='' && document.forms.recibos_y_notas.elements[17].options[document.forms.recibos_y_notas.wfuente.selectedIndex].text=='')
   					{
   						document.recibos_y_notas.elements[16].focus();
   					}

   					if (document.forms.recibos_y_notas.elements[16].options[document.forms.recibos_y_notas.wfuente.selectedIndex].text!='' && document.forms.recibos_y_notas.elements[17].options[document.forms.recibos_y_notas.wfuente.selectedIndex].text=='')
   					{
   						document.recibos_y_notas.elements[17].focus();
   					}

   					if (document.forms.recibos_y_notas.elements[17].options[document.forms.recibos_y_notas.wfuente.selectedIndex].text!='' && document.forms.recibos_y_notas.elements[18].value!='')
   					{
   						document.recibos_y_notas.elements[19].focus();
   					}

   					if (document.forms.recibos_y_notas.elements[17].options[document.forms.recibos_y_notas.wfuente.selectedIndex].text!='' && document.forms.recibos_y_notas.elements[18].value=='')
   					{
   						document.recibos_y_notas.elements[18].focus();
   					}
   				}
   				else
   				{

   					if (document.forms.recibos_y_notas.elements[17].options[document.forms.recibos_y_notas.wfuente.selectedIndex].text=='')
   					{
   						document.recibos_y_notas.elements[17].focus();
   					}

   					if (document.forms.recibos_y_notas.elements[17].options[document.forms.recibos_y_notas.wfuente.selectedIndex].text!='' && document.forms.recibos_y_notas.elements[18].value!='')
   					{
   						document.recibos_y_notas.elements[19].focus();
   					}

   					if (document.forms.recibos_y_notas.elements[17].options[document.forms.recibos_y_notas.wfuente.selectedIndex].text!='' && document.forms.recibos_y_notas.elements[18].value=='')
   					{
   						document.recibos_y_notas.elements[18].focus();
   					}
   				}
   			}
   			else
   			{
   				document.recibos_y_notas.elements[11].focus();
   			}
   		}
   	}

   	if (tipo==2)
   	{
   		document.recibos_y_notas.centro.focus();
   	}
   }
  </script>
 
</head>

<body onload="hacerFoco(1)">

<?php
include_once("conex.php");


/**
 * Enter description here...
 * 
 * NOMBRE: RECIBOS Y NOTAS
 *
 * PROGRAMA: Recibos-y-notas.php
 * TIPO DE SCRIPT: PRINCIPAL
 * DESCRIPCION: Este programa sirve para hacer los recibos de caja a varias facturas de una misma empresa o las notas debito y credito, pudiendose
 *               hacer la cancelacion con conceptos de cartera por cada una de las facturas detalladas. 
 * 
 * 
 * Modo de funcionamiento
 * 
 */
/*
Este programa sirve para hacer recibos de caja a varias facturas de una misma empresa, recibos por otros conceptos (sin factura asociada), o notas debito o crédito a una factura, por conceptos de cartera o de facturación. Permitiendo también su consulta, impresión o anulación bajo ciertas condiciones de validación.

PRIMER INGRESO AL PROGRAMA:

Siempre que se carga el programa de recibos y notas se identifica al usuario y su caja, adicionalmente se valida si el usuario tiene permiso para la realización de los documentos que se graban por medio del programa (información que esta consignada en la tabla 000081 de cajas). En caso de que no tener ningún permiso, el usuario tendrá únicamente la posibilidad de consulta. (En la próxima versión del programa se cambiará el esquema de seguridad, el cual indicará que tareas puede realizar el usuario sobre la fuente, grabar, actualizar, consultar o anular).

Siempre que se carga el programa de recibos y notas se prepara en la parte superior de la pantalla un formulario de selección de parámetros INICIALES para la generación de documentos (fuente del documento, fechas del documento, empresa responsable, número del documento, sucursal o centro de costos y caja), estos sirven como parámetros para la realización de un documento o como formulario de entrada para una consulta. El programa trae dentro de sus parámetros todas las fuentes que el usuario está autorizado para grabar, en un menú de selección, pero le permite ingresar cualquier otra fuente que desee consultar mediante un campo de texto, igualmente hay un campo de texto para ingresar el número de un documento que se desee consultar.

Cuando se ingresa por primera vez al programa (no existe la variable bandera), el programa borra los registros grabados hace más de dos días, de una tabla de almacenamiento temporal (000045) en la que se va guardando toda la información que el usuario va consignando en el sistema, es decir, los registros de todos los documentos que van a ser creados pero que el usuario aún no ha grabado de forma definitiva. Se entiende por registro, cada uno de lo conceptos ingresados por el usuario para la creación de un documento, por ejemplo:

Para un recibo:
Una factura con el valor a cancelar correspondiente
Una factura con un concepto de cartera por aplicar
Una factura con valor a cancelar y concepto de cartera por aplicar

Para un recibo por otros conceptos:
Un concepto de cartera para aplicar

Para una nota crédito o debito con conceptos de cartera
Un concepto de cartera por aplicar a la factura

Para una nota crédito o débito con conceptos de facturación
El valor a aplicar a un concepto de facturación (cargo) de la factura



Esta tabla tiene la finalidad de que en caso de alguna caída del sistema o de que el usuario deba suspender su trabajo, no pierda los registros previamente ingresados, sino que estos queden almacenados temporalmente mientras se pueda realizar todo el documento. Posteriormente carga los parámetros INICIALES del programa.

El parámetro fecha pude ser modificado por el usuario, una vez modificado, el programa valida que el número de días entre la fecha actual y la fecha ingresada no supere el número de días de diferencia permitido para la fuente seleccionada (maestro de fuentes de cartera, campo cardia en tabla 000040).

INGRESO DE DATOS AL PROGRAMA:

Cuando el usuario envía el formulario de parametros INICIALES  (la variable bandera toma el valor de 1), dependiendo de los campos diligenciados en los parámetros INICIALES, el programa decide si se realizará una consulta (se ha introducido un número de documento) o se creará un nuevo documento con la fuente seleccionada.

En el caso de un nuevo ingreso, el sistema consulta en la tabla temporal (000045) si para esa fuente seleccionada hay registros previamente ingresados para la creación del documento. Si existen registros en la tabla temporal, el sistema los carga automáticamente, incluyendo la empresa responsable y no permite su cambio hasta que se finalice el documento o se eliminen todos los datos de la temporal.

Posteriormente se calculan los totales de cada registro de valor, es decir el valor total a cancelar (en el caso de recibos) y el valor total de los conceptos de cartera o facturación y la suma total del valor a cancelar y el valor de los conceptos (para recibos).

En este momento el programa debe decidir que tipo de formulario debe desplegar de acuerdo a las características de la fuente:

Los recibos siempre pintarán el formulario de cartera, pero pueden pedir o no conceptos de cartera dependiendo de su configuración en el maestro de fuentes (campo carcca en la tabla 40). Un maestro con una fuente de recibo cuyo campo carcfa (conceptos de facturación) este activado, causa una alerta del programa.

Los recibos cuyo campo carroc esté activo en el maestro, indican que no están asociados a una factura, simplemente tienen conceptos de cartera (Recibos por otros conceptos). Estos deben siempre tener activo carcca en el maestro para que muestre los conceptos de cartera.

Las notas sean debito o crédito deberán tener al menos uno de los campos carcca o carcfa activos, para saber si corresponden a notas con conceptos de cartera o de facturación, en caso de tener las dos, el usuario escogerá que realizará durante la ejecución del programa. Una fuente que corresponda a una nota y no tenga al menos uno de los campos carcca o carcfa activos, causara una alerta del programa.

Para las notas crédito o débito, el programa da la opción de seleccionar una o varias de las causas registradas en el maestro de causas para cada fuente (tabla 000072).

Una vez se despliega el formulario de acuerdo a las características de la fuente, el usuario empieza a ingresar los datos solicitados y el sistema realiza por cada registro ingresado (la bandera pasa a tomar el valor de 2), las validaciones según el tipo de fuente como se describe a continuación:

Si es un recibo por otros conceptos, el sistema valida que se seleccione un concepto y que se ingrese un centro de costos válido para el concepto, en caso de que este lo requiera (campo concco activo en la tabla 000044). Adicionalmente va calculando el total de los conceptos, sumando o restando el concepto de acuerdo al multiplicador del concepto en el maestro de conceptos de cartera (conmul en tabla 000044).

Para todos los documentos excepto recibos por otros conceptos,  primero se valida que se haya ingresado una factura y que esta exista para el responsable seleccionado. Cada que se ingresa un registro, el sistema calcula el nuevo saldo de la factura, generando un saldo acumulado que corresponde al saldo real (saldo almacenado de la factura) más o menos los valores  de los registros ingresados por el usuario. Adicionalmente en el caso de ser una nota se valida que se encuentre la factura en estado generada o  devuelta para permitir la realización de la nota.

Para el caso de recibos sin conceptos de cartera únicamente se valida que el valor a cancelar no supere el valor del saldo acumulado anterior. Adicionalmente al ingresar cada registro el sistema va calculando el total del valor a cancelar.

Para el caso de recibos con conceptos de cartera, debe validarse que la suma del valor a cancelar y el valor del concepto no superen  el saldo acumulado de la factura. Adicionalmente se valida que si se ha elegido algún concepto de cartera se haya ingresado algún valor del concepto y que si el concepto elegido pide centro de costos, este se haya diligenciado correctamente.  Finalmente al ingresar cada registro el sistema va calculando el total del  valor a cancelar, del valor de los conceptos y del valor total del documento (valor a cancelar más valor de los conceptos).  El valor de los conceptos para calcular el total o el nuevo saldo acumulado, puede ser positivo o negativo dependiendo de su multiplicador en el maestro de conceptos de cartera, sin embargo este siempre se muestra positivo en el programa.

En el caso de una nota crédito con conceptos de cartera se valida que se seleccione un concepto y que se ingrese el centro de costos para el concepto en caso de que este lo requiera (campo concco activo en tabla 44), adicionalmente que el valor de la nota no supere el  saldo acumulado de la factura. Finalmente al ingresar cada registro el sistema va calculando el total del valor de los conceptos. Para las notas crédito el programa despliega las glosas que afectan la factura seleccionada (las glosas se encuentran registradas en la tabla 000021), para que el usuario seleccione una a relacionar con la nota, si así lo desea.

En el caso de una nota debito con conceptos de cartera se valida que seleccione un concepto y que se ingrese el centro de costos para el concepto en caso de que este lo requiera (campo concco activo en tabla 44), adicionalmente que el valor de la nota no supere el valor de la factura menos su saldo acumulado. Finalmente al ingresar cada registro el sistema va calculando el total del valor de los conceptos.

En el caso de una nota con conceptos de facturación, el programa una vez ingresada la factura, consulta los cargos cuyos saldo sea mayor a cero para notas crédito y mayores o iguales a cero para notas débito (en este caso no se utilizan los cargos por abonos), desplegándolos en un menú de selección. Si a un cargo ya se le aplicó la nota durante la operación del programa, ya no se presenta en el menú, es decir solo pueden ser seleccionados una vez.

Cuando se selecciona un cargo determinado para una nota con conceptos de facturación, se obtienen el saldo, el valor máximo y el valor en la factura. El valor máximo  en una nota crédito corresponde al dato menor entre el saldo acumulado de la factura y el valor inicial del concepto en la factura más o menos las notas que se han aplicado al concepto.  El valor máximo en una nota debito con conceptos de facturación es el dato menor entre el valor de la factura menos el saldo y la sumatoria de notas que se le han hecho al concepto (notas crédito menos notas débito). El sistema valida que el registro no supere el valor máximo correspondiente, calcula el nuevo saldo acumulado para la factura y el total del valor de los conceptos. Para las notas crédito el programa despliega las glosas que afectan la factura seleccionada (las glosas se encuentran registradas en la tabla 000021), para que el usuario seleccione una a relacionar con la nota, si así lo desea.

Como ya se dijo cada registro es validado con las condiciones anteriores y al pasar las validaciones, es almacenado en una tabla temporal de trabajo (000045). Cuando se da clic a la opción eliminar de un registro específico, este es eliminado de la tabla temporal actualizando los registros (información de saldo acumulado al ingreso del registro) para su misma fuente y que fueron ingresados al sistema posteriormente (que el id es mayor).


GRABACIÓN DE UN DOCUMENTO:

Cuando se elige la opción grabar, el sistema almacena o crea las tablas del documento basándose en la información de la tabla temporal.

Para recibos se realiza inicialmente el ingreso de las formas de pago la cual pide dependiendo de la forma de pago diferentes datos. Actualmente para cheques (fpache activo en maestro 000023) y para tarjetas crédito (fpatar activo en maestro 000023) exige los datos de número de documento, banco de origen, ubicación y número de autorización. Para las otras formas de pago no se exigen estos datos y únicamente se preguntan el documento anexo y observación. Siempre se pregunta el banco destino y el valor para la forma de pago. Cada que se ingresa una nueva forma de pago el sistema calcula el nuevo saldo del valor a cancelar, es decir el valor a cancelar del documento (no se tiene en cuenta el valor de los conceptos) menos los valores a cancelar para las formas de pago ya ingresadas. El sistema valida que el nuevo saldo para cancelar no sea menor a cero. Cuando el valor de este saldo es igual al cero, automáticamente se inicia la grabación de documento.

Una vez se puede grabar el documento se realizan las siguientes acciones en el siguiente orden:

•	Se incrementa el consecutivo de la fuente en la tabla 40
•	Se graba el encabezado del documento en la tabla 20
•	Se entra en un ciclo de registro por registro del documento, este hace los siguientes pasos:

1. Si el documento no es un recibo por otros conceptos, se consulta la historia del paciente y numero de ingreso asociado a la factura.

2. Si el documento es por conceptos de cartera se almacenan n registros en la tabla 21 como registros haya ingresado el usuario. Si no es un recibo por otros conceptos, se realiza una agrupación del valor aplicado para cada factura implicada en el documento.

3. Si el documento tiene conceptos de facturación se graba un único registro en el detalle del documento (tabla 000021), donde no se especifica ningún valor o concepto pero si contiene la historia y número de ingreso para la factura. Adicionalmente para las notas con conceptos de facturación, se realiza un redesgloce de los conceptos que hacen parte de la factura, el proceso se realiza dentro del ciclo por cada uno de los conceptos o registros involucrados en la nota y consiste en los siguiente: Se consulta el valor inicial de la factura (valor + iva +cuota moderadora+copago+abono) y se descuenta o suman las notas por conceptos de facturación que se le han hecho y el valor de la nota para el concepto para el cual se esta realizando el proceso en el ciclo de registros. Igualmente se consultan cada uno de los conceptos de la factura y su valor inicial y se le descuentan las notas con concepto de facturación que se le han hecho a cada concepto (de tabla 65), para el concepto para el cual se esta haciendo el proceso del ciclo también se le descuenta el valor que se le esta ingresando mediante la nota en realización.  Con el valor de la factura calculado y el valor calculado de cada concepto se hallan los porcentajes para realizar el resesgloce de cada documento con conceptos de cartera que se le ha hecho a la factura. Entonces se coge documento por documento con conceptos de cartera y se va calculando la proporción que se debe descontar o sumar al valor inicial de cada concepto y se va modificando el saldo de los conceptos con las proporciones encontradas. Cada documento de cartera para la factura crea 1 registro (en la tabla 65) por cada concepto que indica el valor o proporción que se le descontó o sumo al concepto y el saldo que le dejo. Estos registros se actualizan cada vez que se vuelve a realizar una nota por concepto de facturación, es decir, cuando se hace necesario volver a realizar un redesgloce de todos los documentos que afectan la factura.  Posteriormente se actualiza el saldo del concepto de facturación que está en el registro sobre el cual el ciclo está trabajando. Finalmente se graba el detalle de la nota para ese concepto (en la tabla 65).

4. Posteriormente, para documentos que no sean recibos por otros conceptos, se actualiza el saldo de la factura  y el valor que se ha descontando según el tipo de documento (tabla 18),  aplicando los valores del registro.

5. Posteriormente borro el registro de la tabla 45

•	Una vez terminado el ciclo de registros, si el documento es con conceptos de cartera y no es recibo por otros conceptos, para cada factura que afecta el documento y que fueron agrupadas durante el ciclo, se encuentra la proporción del saldo de cada uno de sus conceptos de facturación en el saldo de la factura, y con estas proporciones se hace un desglose del valor del documento para esa factura, es decir, se descuenta proporcionalmente el valor del documento en los saldos de los conceptos para la factura. De esta manera tenemos saldos en línea para cada concepto.

•	Si el documento tiene causas se graban en la tabla 71

•	Si el documento tiene formas de pago, se graba por cada forma de pago un registro en la tabla 22.

Una vez guardado todo el documento se depliega en pantalla el resumen del documento con la posibilidad de imprimirlo con observación, sin observación o de anularlo.


CONSULTA DE UN DOCUMENTO:

Para consultar un documento se ingresa normalmente a la aplicación y se selecciona la fuente del documento (si no aparece en el menú de selección, puede digitarse en la casilla del lado) posteriormente se ingresa el número del documento (el programa tendrá la bandera en 1 y la variable del menú de selección vacía, mientras existirá el valor de la fuente ingresada en el campo de texto), de esta manera el sistema sabe que debe consultar el documento, desplegarlo según el tipo de fuente, mostrar las causas si las tiene, las observaciones y las formas de pago si las tiene.

El sistema también muestra la opción para la consulta del mismo número de documento con la misma fuente, pero para otro centro de costos.  El sistema despliega la opción de imprimir con observación, sin observación o de anularlo. La opción de anular solo la presenta cuando el centro de costos de la consulta es el mismo centro de costos de la caja del usuario.

Para la impresión del documento con o sin observación invoca al programa Imp_documento.php, pasándole el número del documento, la fuente, el centro de costos y el grupo de la base da datos en la que debe realizar la consulta.


ANULACIÓN DE UN DOCUMENTO:

Cuando se decide anular el documento (la bandera toma el valor de cuatro), el sistema realiza las siguientes validaciones:

•	Que no hayan formas de pago para el documento que ya hayan sido cuadradas total o parcialmente (en la tabla 22)
•	Que no hayan formas de pago para el documento que ya hayan sido trasladadas (en la tabla 22)
•	Que la caja donde se realizó el documento sea la misma donde se encuentra cada una de las formas de pago del documento.
•	Que el documento no pertenezca a otro periodo contable (mes y año)

La anulación realiza los siguientes pasos:

•	Se pone en off el estado del encabezado del documento (tabla 20)

•	Si el documento es por conceptos de facturación, para cada uno de los conceptos de facturación de la nota a anular, se realiza el redesgloce para cada uno de los documentos con conceptos de cartera que se han aplicado a la factura, de igual manera que como se explicó para la grabación. Posteriormente se pone en off el registro referente a la nota para el concepto y se actualiza el saldo del concepto. Finalmente se anula el registro o detalle que existe en la tabla 21 (estado en off).

•	Si no es por conceptos de facturación, devolvemos los saldos de cada concepto de la factura de acuerdo a la proporción de cada concepto en el saldo de la factura (si el documento no es un recibo por otros conceptos). Finalmente cambiamos a off el estado del detalle para el registro en la tabla 21

•	Por cada registro del documento se actualiza o devuelve el saldo de la factura si no es recibo por otros conceptos.

•	Finalmente se anulan (pone estado en off) las formas de pago si es un recibo y de las causas si es una nota.


GRABACION AUTOMATICA DE UN RECIBO:

Al seleccionar las fuentes de recibos que están asociadas a facturas, el programa dentro de los parámetros iniciales,
permite ingresar el número de un envío para realizar de manera más rápida el recibo correspondiente.
Una vez el usuario ingresa el envío, el sistema presenta la lista de facturas relacionadas,
de manera que el usuario pueda seleccionar cuales quiere incluir en el recibo e
ingresar los valores a cancelar y conceptos adicionales para aplicar a cada factura incluida.
Una vez seleccionadas las facturas, el sistema valida que el valor a cancelar más el valor del concepto
no supere el saldo de cada factura incluida, adicionalmente que para los conceptos que requieren centro
de costos, el usuario haya seleccionado un centro de costos válido. Una vez se hayan pasado estas validaciones
se graban todos los registros de las facturas incluidas en la tabla temporal (000045) y se continúa con el ingreso de las
formas de pago, como fue explicado con anterioridad, para finalmente realizar la grabación.
*/
/*
*
* HISTORIAL DE ACTAULIZACIONES:
*
* 2005-10-18 Juan Carlos Hernandez, creacion del script
* 2006-05-30 carolina castano, modificación y nuevas funcionalidades
* 2006-07-24 carolina castano, mejora de detalles y arreglo de errores
* 2006-10-17 carolina castano, los usuarios pueden consultar cualquier documento
* 2006-10-24 carolina castano, cada usuario tiene unas fuentes determinadas que puede utilizar
* 2006-10-25 carolina castano, la fuente del documento dice si usa concepto de cartera o de facturación,
* 2006-10-26 carolina castano, los conceptos se ingresan positivos y según el multiplicador restan o suman
* 								se pide centro de costos para los conceptos que lo requieren
* 2006-10-27 carolina castano, se pide banco destino en la forma de pago
* 							  , para anular se debe estar en el mismo periodo del documento
* 2006-11-01 carolina castano, se corrige error en la anulación grabando movimientos en la 65
* 2006-12-05 carolina castano, se realiza redesgloce despues de un concepto de facturacion
* 2006-12-07 carolina castano, se adecua campo libre para consulta de abonos o fuentes canceladas
* 2006-11-07 carolina castano, se adecua validacion de notas debito que no sobrepasen el valor del saldo
* 2007-02-06 carolina castano, se habilita posibilidad de recibos por otros conceptos (sin factura)
* 2007-04-11 carolina castano, se rediseña totalmente el programa y se da posibilidad de cambiar la fecha de creacion
*                              segun lo permito en el maestro para la fuente y de seleccionar glosas de la
*                               factura para relacionarlas con una nota credito
* 2007-05-03  carolina castano, Se permite anular documentos de otro periodo contable si cumple con los dias de holgura de
*								tabla 40
Se muestra en el banco destino siempre el banco incial
Se implementa para recibos de caja que el banco destino venga por defecto con la forma de pago
* 2007-05-10 carolina Castano, para la fuente 31 se despliega tambien caja general en banco destino
*                               Se permiten notas debito de facturas negativas, siempre que no le deje saldo positivo
* 2007-12-27 Carolina Castaño, Se calcula retencion en la fuente y se realiza refacturacion

* Tablas que utiliza:
* $wbasedato."_000045: Tabla temporal de almacenamiento de operaciones (notas y recibos), select, delete insert
* $wbasedato."_000030: Busqueda de cajeros autorizados, select
* $wbasedato."_000040: Maestro de Fuentes, select
* $wbasedato."_000018: encabezado de factura, select y update
* $wbasedato."_000023: formas de pago select
* $wbasedato."_000069:  select
* $wbasedato."_000065: insert, select
* $wbasedato."_000022: detalle de forma de pago select, update, insert
* $wbasedato."_000020: encabesado de documentos insert select
* $wbasedato."_000021: detalle de documento, select update, insert
* $wbasedato."_000071: causas del documento update insert
* $wbasedato."_000024:  select
* $wbasedato."_000044: select
* $wbasedato."_000064: select
* $wbasedato."_000072: causas select
* $wbasedato."_000066: select
* $wbasedato."_000106: select
* $wbasedato."_000044: select
* @author ccastano
* @package defaultPackage
*/
/*
$anular  indica si la accion a ejecutar es una anulación
$bandera1  segun valor indica la accion a realizar, si vale 1 ya se habia ingresado al sistema, 2 es solo consulta de documento
$cancelar cantidad a restar en un recibo o nota, se le dan valores de vez en cuando
$causas  vector de causas para asignar a un documento
$cauSel  la causa seleccionada para ir asignando al documento
$cco centro de costos del concepto de facturacion
$centro centro de costos para la consulta de un documento
$codCpto codigo del concepto de facturacion
$continua indica que ya estan listas las formas de pago y se puede grabar el recibo
$Cpto conceptos de facturacion
$docu numero del documento para anular
$factura factura con fuente y numero
$fk va llevando la cuenta del numero de formas de pago
$fuen fuente del documento que se va a anular
$grabado indica si fue grabado
$indicador informa de errores de validacion para crear unb recibo de forma automatica
$ingCau  indica que se va a ingresar una causa
$mul indica el multiplicador (+1 o -1) segun el tipo de documento
$nrofac numero de factura prefijo y numero
$nueSal nuevo saldo de la factura
$obliga indica si en la forma de pago los campos de banco estan obligados
$permiso indica si se puede o no anular el documento (no se puede cuando este ha sido cuadrado)
$pide indica si el concepto requiere centro de costos
$plaza plaza del banco del recibo
$selCpto concepto de facturacion seleccionado
$senal da indicaciones de errores, por ejemplo cuando el usuario no esta registrado en una caja
$ter tercero para los conceptos de facturacion
$tipdoc tipo de documento, recibo, nota debito o credito
$tipo tipo de documento, recibo, nota debito o credito
$total acumula totales a cancelar
$ubica plaza seleccionada del banco
$valconf valor concepto de facturacion
$valDoc valor a cancelar del documento en caso de anular
$vector aca voy guardando todos las facturas y los datos que haran parte del recibo en modo automatico
$vol al hacer recibo de caja si lo quiero manual o automatico
$wautori nuero de autorizacion de la forma de pago
$wborrar si se desea eliminar una factura del documento en tabla temporal
$wbuscador ayuda a buscar el nombre de la empresa en el drop down
$wcaja caja del usuario
$wcajdoc caja donde se grabo el documento, para la consulta
$wcarfpa  tiene la fuente o no forma de pafo
$wcarncr  la fuente es nota credito
$wcarndb  la fuente es nota debito
$wcarrec  la fuente es recibo
$wcco     centro de costos del usuario
$wccocon  centro de costos para el concepto de cartera
$wcod  codigo de la empresa para el documento
$wcodfpa codigo de la forma de pago
$wconcar concepto de cartera
$wdocane datos del cheque o forma de pago
$wempresa empresa
$wfecdoc  fecha del documento
$wfenfac fecha de la factura
$wfpa forma de pago
$wfuedoc fuente del documento
$wfueEnv fuento del envio
$wfuente fuente del documento
$wgrabar indica si se debe grabar el documento
$wid id de la factur a borrar de tabla temporal del documento
$wini se puede generar una nueva linea
$wmul  multiplicador del documento para conceptos de cartera
$wnomcaj nombre de a caja
$wnomcco nombre del centro de costos
$wnomemp nombre de la empresa
$wnomfue nombre de lka fuetne
$wnrodoc  nuero de documento
$wnrofac numero de la factura
$wnuelin  mueva linea
$wnumEnv  nuermo de envio
$wobs observaciones del recibo
$wobsm observaciones del recibo en la consulta
$wobsrec observaciones de la forma de pago
$wprefac  prefijo de la factura
$wsalfac saldo de la factura
$wtipdoc tipo de documento nota credito debito o recibo
$wtotfpa total acumulado en la forma de pago
$wtotvalcon  total valor de conceptos de cartera
$wtotvalfcon total de valor de conceptos de factura
$wtotvca  total valor a cancelar
$wtotvcafac total cancelar factura
$wtotvco total valor concepto
$wubica ubicacion del banco
$wusudoc usuario del documento
$wvalcon valor concepto
$wvaldoc  valor documento
$wvalfac valor factura
$wvalfpa valor forma de pago
$wvcafac valor a cancelar factura
*/
//=================================================================================================================================

$wautor='Carolina Castano P';

//////////////////////////////////////////////////////////////FUNCIONES//////////////////////////////////////////////////////////////////
/***********************************************FUNCIONES DE PERSISTENCIA*************************************/

/**
 * BORRA LOS REGISTROS TEMPORALES DE RECIBOS O NOTAS 
 *
 * @param unknown_type $wfecha_bor FECHA PARA RESTARLE DOS DIAS Y BORRAR
 */

function eliminarRegistro($wid, $ref)
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT temfue, temdoc, temsuc, temres, temcaj, temvcf, temdco, temvco, temnfa, temcon, Seguridad "
	."   FROM ".$wbasedato."_000045 "
	."  where id = ".$wid ;

	$res2 = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	$row2 = mysql_fetch_array($res2);

	if($ref=='')
	{
		//hago query del valor concepto y valor a cancelar para saber que descontar
		$q="    DELETE FROM ".$wbasedato."_000045 "
		."     WHERE id = ".$wid;
		$res = mysql_query($q,$conex);

		$exp=explode('-', $row2[0]);
		$fuen= $exp[0];

		//consulto el tipo de documento
		$q= "  SELECT carncr, carndb, carrec, carcon "
		."    FROM ".$wbasedato."_000040 "
		."   WHERE carfue = '".$fuen."'";
		$err1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL TIPO DE DOCUMENTO PARA LA FUENTE ".mysql_error());
		$row1 = mysql_fetch_array($err1) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL TIPO DE DOCUMENTO PARA LA FUENTE ".mysql_error());

		$suma=$row2[5]-($row2[7]*$row2[6]);

		$q =  " SELECT temsfa, id"
		."   FROM ".$wbasedato."_000045 "
		."  where temfue = '".$row2[0]."' and temdoc = '".$row2[1]."' and temsuc    = '".$row2[2]."' and temres = '".$row2[3]."' and temcaj = '".$row2[4]."' and temnfa='".$row2[8]."' and id > '".$wid."' ";

		$resx = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
		$num = mysql_num_rows($resx);

		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_row($resx);
			$resul=$row[0]+$suma;
			$q="update ".$wbasedato."_000045 ";
			$q=$q." set temsfa=".$resul."  where temfue = '".$row2[0]."' and temdoc = '".$row2[1]."' and temsuc    = '".$row2[2]."' and temres = '".$row2[3]."' and temcaj = '".$row2[4]."' and temnfa='".$row2[8]."' and id = ".$row[1]." ";
			$res = mysql_query($q,$conex);
		}
	}
	else
	{
		$q="    DELETE FROM ".$wbasedato."_000045 "
		."  WHERE Seguridad = '".$row2[10]."'"
		."    AND temcaj    = '".$row2[4]."' and temfue = '".$row2[0]."' "
		."    AND temres= '".$row2[3]."' ";

		$res = mysql_query($q,$conex);
	}
}

function borrarTem($wfecha_bor)
{
	global $conex;
	global $wbasedato;

	$q = "  DELETE FROM ".$wbasedato."_000045 "
	."   WHERE fecha_data <= str_to_date(ADDDATE('".$wfecha_bor."',-3),'%Y-%m-%d')";

	$res = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HAN PODIDO BORRAR LOS REGISTROS DE HACE DOS DIAS DE LA TABLA TEMPORAL 000045 ".mysql_error());
}

/**
 * extrae los datos de la caja a la que pertenece el usuario, codigo, nombre y centro de costos
 *
 * @param unknown_type $wusuario se manda el usuario
 * @param unknown_type $wcco retorna codigo del centro de costos
 * @param unknown_type $wnomcco retorna nombre del centro de cosots
 * @param unknown_type $wcaja, retorna codigo de la caja
 * @param unknown_type $wnomcaj retorna nombre de la caja
 * @return unknown retorna true si encuentra caja asignada al usuario
 */
function consultarCaja($wusuario, &$wcco, &$wnomcco, &$wcaja, &$wnomcaj)
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT cjecco, cjecaj "
	."   FROM ".$wbasedato."_000030 "
	."  WHERE cjeusu = '".$wusuario."'"
	."    AND cjeest = 'on' ";
	
	//On
	echo $q."<br>";
	
    $res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	if ($num > 0)
	{
		$row = mysql_fetch_array($res);

		$pos = strpos($row[0],"-");
		$wcco = substr($row[0],0,$pos);
		$wnomcco = substr($row[0],$pos+1,strlen($row[0]));

		$pos = strpos($row[1],"-");
		$wcaja = substr($row[1],0,$pos);
		$wnomcaj = substr($row[1],$pos+1,strlen($row[1]));

		return true;
	}else
	{
		return false;
	}
}

function consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref)
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT temres, temfco, empraz "
	."   FROM ".$wbasedato."_000045, ".$wbasedato."_000024 "
	."  WHERE ".$wbasedato."_000045.seguridad = 'C-".$wusuario."'"
	."    AND temcaj    = '".$wcaja."' and temfue = '".$wfuente."' "
	."    AND empcod    = (mid(temres,1,instr(temres,'-')-1)) and empcod = empres and empest='on'  ";

	$res2 = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	$num2 = mysql_num_rows($res2);   // or die (mysql_errno()." - ".mysql_error());;

	if ($num2 > 0)
	{
		$row1 = mysql_fetch_array($res2);
		$wempresa= $row1[0];
		$ref='';

		$q =  " SELECT temnfa, temvfa, temsfa, temvcf, temcon, temdco, temvco, temcco, temter, temffa, temccc, temfco, temglo, id"
		."   FROM ".$wbasedato."_000045 "
		."  WHERE seguridad = 'C-".$wusuario."'"
		."    AND temcaj    = '".$wcaja."' and temfue = '".$wfuente."' order by id desc ";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		for ($i=0; $i<$num; $i++)
		{
			$row = mysql_fetch_array($res);
			$temporal[$i]['temnfa']=$row ['temnfa'];
			$temporal[$i]['temvfa']=$row ['temvfa'];
			$temporal[$i]['temsfa']=$row ['temsfa'];
			$temporal[$i]['temvcf']=$row ['temvcf'];
			$temporal[$i]['temcon']=$row ['temcon'];
			$temporal[$i]['temdco']=$row ['temdco'];
			$temporal[$i]['temvco']=$row ['temvco'];
			$temporal[$i]['temcco']=$row ['temcco'];
			$temporal[$i]['temter']=$row ['temter'];
			$temporal[$i]['temffa']=$row ['temffa'];
			$temporal[$i]['temccc']=$row ['temccc'];
			$temporal[$i]['temfco']=$row ['temfco'];
			$temporal[$i]['temglo']=$row ['temglo'];
			$temporal[$i]['id']=$row ['id'];

			if($temporal[$i]['temvco']<0)
			{
				$ref='on';
			}
		}
		return $temporal;
	}
	else
	{
		return false;
	}
}

function consultarEnvio($wnumEnv, $wcco )
{
	global $conex;
	global $wbasedato;

	//consulto la fuente del envio
	$q="select carfue from ".$wbasedato."_000040 where carenv='on' ";
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);  //asumiendo que solo hay un envio
	$wfueEnv=$row[0];

	$contador=0;

	$q="select rdefac, rdeffa from ".$wbasedato."_000021, ".$wbasedato."_000020 "
	.  "where rdefue='".$wfueEnv."' and rdenum='".$wnumEnv."' and rdecco='".$wcco."'"
	.  "and rdeest='on' and rdenum=rennum and rdefue=renfue ";
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);
			$exp=explode('-',$row[0]) ;
			$q="select fenffa, fenfac, fenval, fensal, fennit, fencod, empnom, empraz from ".$wbasedato."_000018, ".$wbasedato."_000024 "
			.  "where fenfac='".$row[0]."' and fenffa='".$row[1]."' and fenest='on' and fensal>0 and fennit=empnit and empcod=empres and empest='on' ";
			$err = mysql_query($q,$conex);
			$num2 = mysql_num_rows($err);

			if ($num2>0)
			{
				$row2 = mysql_fetch_array($err);
				$exp=explode('-',$row2[1]);
				$temporal[$contador]['temenv']=$wnumEnv;
				$temporal[$contador]['temffa']=$row2[0];
				$temporal[$contador]['temnfa']=$exp[0].'-'.$exp[1];
				$temporal[$contador]['temvfa']=$row2[2];
				$temporal[$contador]['temsfa']=$row2[3];
				$temporal[$contador]['temnit']=$row2[4];
				$temporal[$contador]['temcod']=$row2[5];
				$temporal[$contador]['temnom']=$row2[6];
				$temporal[$contador]['temraz']=$row2[7];
				$temporal[$contador]['temvcf']=$row2[3];
				$temporal[$contador]['temfco']='off';
				$temporal[$contador]['temdco']=1;
				$temporal[$contador]['temcon']='';
				$temporal[$contador]['temvco']=0;
				$temporal[$contador]['temccc']='off';
				$temporal[$contador]['chk']='checked';
				$contador++;
			}
		}
		if($contador>0)
		{
			return $temporal;
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

function guardarTemporal($wfecha, $hora, $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, $prefac, $numfac, $canfac, $wvalfac, $wsalfac, $centro, $tipo, $fuefac, $multi, $wusuario, $nomcon, $cancon, $wcarcca, $wcarcfa, $wcarrec, $wcarndb, $wcarncr, $glosa)
{
	global $conex;
	global $wbasedato;


	if ($wcarrec or $wcarcca)
	{
		$wsalfac=$wsalfac-$canfac+($cancon*$multi);

		$q= " INSERT INTO ".$wbasedato."_000045 (       Medico  ,   Fecha_data ,    Hora_data,      Temfue,       temdoc  ,        temfec ,     temsuc,      temcaj,       temres  ,  temvre,          temnfa         ,     temvfa   ,       temsfa,        temvcf ,    temcon   ,   temdco ,   temvco      ,  temffa,     temfco,      temccc,        temcco,      temter,  temglo, Seguridad) "
		."                            	VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' , '".$wfuente."','".$wnrodoc."','".$wfecdoc."' ,'".$wcco."','".$wcaja."','".$wempresa."', '0'   , '".$prefac."-".$numfac."', '".$wvalfac."','".$wsalfac."','".$canfac."','".$nomcon."',  $multi,'".$cancon."',  '".$fuefac."', '".$tipo."', '".$centro."'  ,'',        ''  , '".$glosa."' , 'C-".$wusuario."')";

	}
	else if ($wcarcfa)
	{
		$exp=explode('-', $nomcon);
		if ($wcarncr)
		{
			$wsalfac=$wsalfac-$cancon;
			$q= " INSERT INTO ".$wbasedato."_000045 (       Medico  ,   Fecha_data ,    Hora_data,      Temfue,       temdoc  ,        temfec ,     temsuc,      temcaj,       temres  ,  temvre,         temnfa         ,     temvfa   ,       temsfa,        temvcf ,    temcon   ,              temdco ,   temvco         ,  temffa,     temfco,      temccc,      temcco,      temter, temglo, Seguridad) "
			."                            	VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wfuente."','".$wnrodoc."','".$wfecdoc."' ,'".$wcco."','".$wcaja."','".$wempresa."', '0'   ,'".$prefac."-".$numfac."', '".$wvalfac."','".$wsalfac."',        '".$canfac."',     '".$exp[0]."-".$exp[1]."',  -1,   '".$cancon."',    '".$fuefac."', '".$tipo."', '".$centro."', '".$exp[2]."' , '".$exp[3]."', '".$glosa."' , 'C-".$wusuario."')";

		}

		if ($wcarndb)
		{
			$wsalfac=$wsalfac+$cancon;
			$q= " INSERT INTO ".$wbasedato."_000045 (       Medico  ,   Fecha_data ,    Hora_data,      Temfue,       temdoc  ,        temfec ,     temsuc,      temcaj,       temres  ,  temvre,         temnfa         ,     temvfa   ,       temsfa,        temvcf ,    temcon   ,             temdco ,   temvco         ,  temffa,     temfco,        temccc,     temcco,      temter, temglo, Seguridad) "
			."                            	VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wfuente."','".$wnrodoc."','".$wfecdoc."' ,'".$wcco."','".$wcaja."','".$wempresa."', '0'   ,'".$prefac."-".$numfac."', '".$wvalfac."', '".$wsalfac."',    0,  '".$exp[0]."-".$exp[1]."',  1,     '".$cancon."',   '".$fuefac."', '".$tipo."', '".$centro."', '".$exp[2]."', '".$exp[3]."', '".$glosa."' , 'C-".$wusuario."')";
		}
	}

	$res2 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GUARDAR EL NUEVO REGISTRO EN LA TABLA TEMPORAL ".mysql_error());
}

/**
 * Consulta el tipo de fuente (nota, recibo), si tiene forma de pago, si tiene conceptos de cartera o de facturacion
 *
 * @param unknown_type $wfuente fuente del documento
 * @param unknown_type $wcarndb nota debito
 * @param unknown_type $wcarncr nota credito
 * @param unknown_type $wcarrec recibo
 * @param unknown_type $wcarcca con conceptos de cartera
 * @param unknown_type $wcarcfa con conceptos de facturacion
 * @param unknown_type $wcarfpa con forma de pago
 */
function consultarFuente($wfuente, &$wcarndb, &$wcarncr, &$wcarrec, &$wcarcca, &$wcarcfa, &$wcarfpa, &$wcarroc, &$wnrodoc )
{
	global $conex;
	global $wbasedato;

	//Aca traigo el consecutivo de la fuente
	$q= "   SELECT carcon "
	."     FROM ".$wbasedato."_000040 "  //maestro de fuentes
	."    WHERE carfue ='".$wfuente."' "
	."      AND carest = 'on' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	$row1 = mysql_fetch_array($res1);
	if ($row1[0] != "")
	$wnrodoc=$row1[0]+1;  //valor del consecutivo de la fuente mas 1, para incrementar el numero, pero que no me cancele el pedido

	//Aca traigo que tipo de documento es la fuente y si tiene forma de pago o no
	$q= "   SELECT carfpa, carncr, carndb, carrec, carcca, carcfa, carroc  "
	."     FROM ".$wbasedato."_000040 "
	."    WHERE carfue = '".$wfuente."' "
	."      AND carfue > '24' ";
	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);
	if ($row1[0]=='on')
	{
		$wcarfpa=true;
	}
	else
	{
		$wcarfpa=false;
	}
	if ($row1[2]=='on')
	{
		$wcarndb=true;
	}
	else
	{
		$wcarndb=false;
	}
	if ($row1[1]=='on')
	{
		$wcarncr=true;
	}
	else
	{
		$wcarncr=false;
	}
	if ($row1[3]=='on')
	{
		$wcarrec=true;
	}
	else
	{
		$wcarrec=false;
	}
	if ($row1[4]=='on')
	{
		$wcarcca=true;
	}
	else
	{
		$wcarcca=false;
	}
	if ($row1[5]=='on')
	{
		$wcarcfa=true;
	}
	else
	{
		$wcarcfa=false;
	}
	if ($row1[6]=='on')
	{
		$wcarroc=true;
	}
	else
	{
		$wcarroc=false;
	}
}

function consultarAbono($wfuente)
{
	global $conex;
	global $wbasedato;

	$q= "   SELECT carabo  "
	."     FROM ".$wbasedato."_000040 "
	."    WHERE carfue = '".$wfuente."' ";
	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);
	if ($row1[0]=='on')
	{
		return true;
	}
	else
	{
		return false;
	}
}

function preguntarCentro($wfuente, $nomcon, &$multi)
{
	global $conex;
	global $wbasedato;

	$exp=explode('-', $nomcon);

	$q =  " SELECT concco, conmul "
	."   FROM ".$wbasedato."_000044 "
	."  WHERE concod='".$exp[0]."' and "
	."   conest = 'on' "
	."   AND confue = '".$wfuente."' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		$row1 = mysql_fetch_array($res1);
		$multi=$row1[1];
		if ($row1[0]=='on')
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

function calcularRetencion($wfuente, $nomcon, $canbru, &$canfac, $indi)
{
	global $conex;
	global $wbasedato;

	$exp=explode('-', $nomcon);
	$exp2=explode('-', $wfuente);

	$q =  " SELECT conrfe, conmul "
	."   FROM ".$wbasedato."_000044 "
	."  WHERE concod='".$exp[0]."' and "
	."   conest = 'on' "
	."   AND confue = '".$exp2[0]."' ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	$row1 = mysql_fetch_array($res1);
	if ($row1[0]>0 and $indi==1)
	{
		$cancon=round($canbru*$row1[0]/100);
		$canfac=$canbru-$cancon;

	}
	else
	{
		if($row1[1]<0)
		{
			$cancon=round($canbru-$canfac);
		}
		else
		{
			$cancon=abs($canbru-$canfac);
			$canfac=$canbru;
		}

	}
	if($cancon==0)
	{
		$cancon='';
	}
	return $cancon;
}

function validarCentro($centro)
{
	global $conex;
	global $wbasedato;

	$q="select * from ".$wbasedato."_000003 where ccocod='".$centro."' ";

	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num2 = mysql_num_rows($res);

	if ($num2>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function validarFecha($fecha, $fuente, &$dias)
{
	global $conex;
	global $wbasedato;

	$tiempo=mktime(0,0,0,substr($fecha,5,2),substr($fecha,8,2),substr($fecha,0,4))-mktime(0,0,0,date('m'),date('d'),date('Y'));
	$tiempo=((($tiempo/60)/60)/24)*(-1);

	$q="select cardia from ".$wbasedato."_000040 where carfue='".$fuente."' ";

	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num2 = mysql_num_rows($res);

	if ($num2>0)
	{
		$row2 = mysql_fetch_array($res);
		$dias =$row2[0];
	}
	else
	{
		$dias=0;
	}

	if ($tiempo>$dias)
	{
		return false;
	}
	else
	{
		return true;
	}
}

/**
 * Consulta el multiplicaador del concepto, es decir si resta o multiplica a una factura
 *
 * @param unknown_type $codigo el codigo del concepto de cartera, la fuente del documento al que pertenece
 * @param unknown_type $fuente
 * @return unknown
 */
function consultarMulti ($codigo, $fuente)
{
	global $wbasedato;
	global $conex;

	$q="select conmul from ".$wbasedato."_000044 where concod='".$codigo."' and confue='".$fuente."'  ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$row = mysql_fetch_row($res);
	return (-1*$row[0]);

}

function consultarPermiso ($fuen, $docu, $wcco)
{
	global $wbasedato;
	global $conex;

	$q = " SELECT * FROM ".$wbasedato."_000022 WHERE rfpfue = '".$fuen."' AND rfpnum = '".$docu."' AND rfpcco = '".$wcco."' and rfpecu='C' and rfpest='on' ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		return false;
	}else
	{
		$q = " SELECT * FROM ".$wbasedato."_000022 WHERE rfpfue = '".$fuen."' AND rfpnum = '".$docu."' AND rfpcco = '".$wcco."' and rfpecu='I' and rfpest='on' ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);

		if ($num>0)
		{
			return false;
		}else
		{
			$q = " SELECT * FROM ".$wbasedato."_000022 WHERE rfpfue = '".$fuen."' AND rfpnum = '".$docu."' AND rfpcco = '".$wcco."' and rfpecu='T' and rfpest='on' ";
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num2 = mysql_num_rows($res);
			if ($num2>0)
			{
				return false;
			}else
			{
				//2007-05-03 se adiciona como permiso para anular los dias de holgura
				//adicionalmente para anular un documento debe estar en el mismo periodo (año y mes)
				$q = " SELECT renfec FROM ".$wbasedato."_000020 WHERE renfue = '".$fuen."' AND rennum = '".$docu."' AND rencco = '".$wcco."' and renest='on' ";
				$res = mysql_query($q,$conex);
				$row = mysql_fetch_array($res);

				$exp=explode('-', $row[0]);
				$exp2=explode('-', date('Y-m-d'));
				if ($exp[0]!=$exp2[0] or $exp[1]!=$exp2[1])
				{
					$tiempo=mktime(0,0,0,date('m'),date('d'),date('Y'))-mktime(0,0,0,substr($row[0],5,2),substr($row[0],8,2),substr($row[0],0,4));
					$tiempo=((($tiempo/60)/60)/24);

					$q="select cardia from ".$wbasedato."_000040 where carfue='".$fuen."' ";
					$res = mysql_query($q,$conex);
					$row = mysql_fetch_array($res);
					if($tiempo>$row[0] )
					{
						return false;
					}
				}

				//que la caja donde esta el recibo sea igual a la caja donde se genero
				$q = " SELECT carrec FROM ".$wbasedato."_000040 WHERE  carest='on' and carfue='".$fuen."' ";

				$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$row = mysql_fetch_array($res);

				if ($row[0]=='on')
				{
					$q = " SELECT * FROM ".$wbasedato."_000022, ".$wbasedato."_000020 WHERE rfpfue = '".$fuen."' AND rfpnum = '".$docu."' AND rfpcco = '".$wcco."' and rfpest='on' and renest='on' and rfpcco=rencco and rfpfue=renfue and rfpnum=rennum and rencaj<>rfpcaf";

					$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$num2 = mysql_num_rows($res);
					if ($num2>0)
					{
						return false;
					}
					else
					{
						return true;
					}
				}
				else
				{
					return true;
				}

			}
		}
	}
}

function consultarDocumento($wfuedoc, $wnrodoc, $wcco, &$wobs, &$estado, &$empresa, $wcarndb, &$wfecdoc, &$ref)
{
	global $wbasedato;
	global $conex;

	//indicara si la nota es por refacturacion
	$ref='';

	//consulto el encabezado del documento
	$q = " SELECT renfec, rencod, rennom, renvca, rencaj, renusu, renobs, renest, empnit, empraz "
	."   FROM ".$wbasedato."_000020, ".$wbasedato."_000024 "
	."  WHERE renfue = '".$wfuedoc."'"
	."    AND rennum = '".$wnrodoc."' "
	."    AND rencco = '".$wcco."' "
	."    AND rencod = empcod "
	."    AND empest = 'on' ";

	$res = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO REALIZAR LA CONSULTA DEL ENCABEZADO DEL DOCUMENTO ".mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);

		$estado=$row['renest'];
		$wobs=$row['renobs'];
		$wfecdoc=$row['renfec'];
		$empresa=$row['rencod'].' - '.$row['empnit'].' - '.$row['rennom'].' - '.$row['empraz'];

		$q = " SELECT  rdefac, rdevca , rdeest,  rdecon,  rdevco, rdeffa,  rdesfa,  rdehis, rdeing, rdeccc, rdeglo, id "
		."   FROM ".$wbasedato."_000021 "
		."  WHERE rdefue = '".trim($wfuedoc)."'"
		."    AND rdenum = '".$wnrodoc."'"
		."    AND rdecco = '".$wcco."'";

		$res2 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO CONSULTAR EL DETALLE DEL DOCUMENTO ".mysql_error());
		$num2 = mysql_num_rows($res2);

		$query= "select  fdeest, fdecco,  fdecon,  fdevco,   fdeter,  fdesal, fdeffa, fdefac, grudes, ".$wbasedato."_000065.id as id from ".$wbasedato."_000065, ".$wbasedato."_000004    ";
		$query = $query. " where fdefue='".trim($wfuedoc)."' and fdedoc='".$wnrodoc."' and grucod=fdecon ";

		$err = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
		$can = mysql_num_rows($err);

		if ($num2 > 0)
		{
			$row2 = mysql_fetch_array($res2);
			$rdevca=$row2['rdevca'];
			$rdecon=$row2['rdecon'];


			if (($rdevca=='' or $rdevca==0) and $rdecon=='')//indica que es una nota con conceptos de facturacion
			{

				if ($can > 0)
				{
					$suma=0;
					for ($i=0;$i<$can;$i++)
					{
						$row3 = mysql_fetch_array($err);
						$temporal[$i]['temglo']=$row2['rdeglo'];
						$temporal[$i]['temnfa']=$row2 ['rdefac'];
						$temporal[$i]['temffa']=$row2 ['rdeffa'];


						$q= "   SELECT fenval, fensal "
						."     FROM ".$wbasedato."_000018 "
						."    WHERE fenfac = '".$temporal[$i]['temnfa']."' "
						."      AND fenest = 'on' and fenffa='".$temporal[$i]['temffa']."' ";

						$res1 = mysql_query($q,$conex);
						$num1 = mysql_num_rows($res1);
						$row1 = mysql_fetch_array($res1);
						$temporal[$i]['temvfa']=$row1[0];
						$temporal[$i]['temsfa']=$row1[1];

						if ($wcarndb)
						{
							$temporal[$i]['temdco']=1;
						}
						else
						{
							$temporal[$i]['temdco']=-1;
						}

						if($row3 ['fdevco']<0)
						{
							$ref='on';
							$abono=-$row3['fdevco'];
						}
						else
						{
							$suma=$suma+abs($row3 ['fdevco']);
						}

						$temporal[$i]['temvco']=$row3 ['fdevco'];
						$temporal[$i]['temcco']=$row3 ['fdecco'];
						$temporal[$i]['temter']=$row3 ['fdeter'];
						$temporal[$i]['temcon']=$row3 ['fdecon'].'-'.$row3 ['grudes'];
						$temporal[$i]['temccc']='';
						$temporal[$i]['temfco']='on';
						$temporal[$i]['temvcf']=0;
						$temporal[$i]['id']=$row3['id'];
					}
					if($ref!='')
					{
						for ($i=0;$i<$can;$i++)
						{
							if($temporal[$i]['temvco']>=0)
							{
								$temporal[$i]['temvcf']=round($temporal[$i]['temvco']-($temporal[$i]['temvco']*$abono/$suma));
							}
							else
							{
								$temporal[$i]['temvcf']=$temporal[$i]['temvco'];
							}
						}
					}
				}

			}else //NO ES POR CONCEPTOS DE FACTURACION
			{
				for ($i=0;$i<$num2;$i++)
				{
					$temporal[$i]['temnfa']=$row2 ['rdefac'];
					$temporal[$i]['temffa']=$row2 ['rdeffa'];

					if ($temporal[$i]['temnfa']!='' and $temporal[$i]['temffa']!='')
					{
						$q= "   SELECT fenval "
						."     FROM ".$wbasedato."_000018 "
						."    WHERE fenfac = '".$temporal[$i]['temnfa']."' "
						."      AND fenest = 'on' and fenffa='".$temporal[$i]['temffa']."' ";

						$res1 = mysql_query($q,$conex);
						$num1 = mysql_num_rows($res1);
						$row1 = mysql_fetch_array($res1);
						$temporal[$i]['temvfa']=$row1[0];
					}
					else
					{
						$temporal[$i]['temvfa']='';
					}

					$temporal[$i]['temsfa']=$row2 ['rdesfa'];
					$temporal[$i]['temvcf']=$row2 ['rdevca'];
					$temporal[$i]['temcon']=$row2 ['rdecon'];
					$temporal[$i]['temglo']=$row2['rdeglo'];
					$exp=explode('-', $row2 ['rdecon']);
					$temporal[$i]['temdco']=consultarMulti ($exp[0], $wfuedoc);
					$temporal[$i]['temdco']=-$temporal[$i]['temdco'];
					$temporal[$i]['temvco']=$row2 ['rdevco'];
					$temporal[$i]['temcco']=$wcco;
					$temporal[$i]['temter']='';
					$temporal[$i]['temccc']=$row2 ['rdeccc'];
					$temporal[$i]['temfco']='off';
					$temporal[$i]['id']=$row2 ['id'];
					$row2 = mysql_fetch_array($res2);
				}
			}
			return $temporal;
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

function consultarFormas($wfuedoc, $wnrodoc, $centro, $wtotvcafac, $estado)
{
	global $wbasedato;
	global $conex;

	//se cambia en query rfpban por rfpbai

	if ($estado=='on')
	{
		$q = "SELECT rfpfpa, fpades, rfpdan, rfpobs,  rfpvfp, rfppla, rfpaut, rfpbai, fpache, fpatar "
		."  FROM ".$wbasedato."_000022, ".$wbasedato."_000023 "
		." WHERE rfpfue = '".trim($wfuedoc)."'"
		."   AND rfpnum = ".$wnrodoc
		."   AND rfpfpa = fpacod "
		."   AND rfpest = 'on' "
		."   AND rfpcco = '".$centro."' order by ".$wbasedato."_000022.id";
	}
	else
	{
		$q = "SELECT rfpfpa, fpades, rfpdan, rfpobs,  rfpvfp, rfppla, rfpaut, rfpbai, fpache, fpatar "
		."  FROM ".$wbasedato."_000022, ".$wbasedato."_000023 "
		." WHERE rfpfue = '".trim($wfuedoc)."'"
		."   AND rfpnum = ".$wnrodoc
		."   AND rfpfpa = fpacod "
		."   AND rfpcco = '".$centro."' order by ".$wbasedato."_000022.id";
	}

	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$fk[$i][0]=$row['rfpfpa'].'-'.$row['fpades'];
			$fk[$i][1]=$row['rfpdan'];
			$fk[$i][2]=$row['rfpobs'];
			$fk[$i][3]=$row['rfppla'];
			$fk[$i][4]=$row['rfpaut'];
			$fk[$i][5]=$row['rfpbai'];
			$fk[$i][6]=$row['rfpvfp'];
			$fk[$i][7]=$wtotvcafac-$row['rfpvfp'];
			$wtotvcafac=$fk[$i][7];

			if ($row['fpache']=='on' or $row['fpatar']=='on')
			{
				$fk[$i][8]=true;
			}
			else
			{
				$fk[$i][8]=false;
			}

			if ($fk[$i][2] != "" and $fk[$i][2] != " " and $fk[$i][8] )
			{
				$q = "SELECT bannom, bancue "
				."  FROM ".$wbasedato."_000069 "
				." WHERE bancod = '".$fk[$i][2]."'";

				$res2 = mysql_query($q,$conex);
				$row2 = mysql_fetch_array($res2);
				$fk[$i][2]=$fk[$i][2].'-'.$row2[0];
			}

			if ($fk[$i][5]!= "" and $fk[$i][5]!= " ")
			{
				$q = "SELECT bannom, bancue "
				."  FROM ".$wbasedato."_000069 "
				." WHERE bancod = '".$fk[$i][5]."'";

				$res2 = mysql_query($q,$conex);
				$row2 = mysql_fetch_array($res2);
				$fk[$i][5]=$fk[$i][5]."-".$row2[0]."-".$row2[1];
			}

			switch ($fk[$i][3])
			{
				case "L": //nota credito
				{ $fk[$i][3]='Local';  //multiplo
				break;
				}

				case "O": //nota credito
				{ $fk[$i][3]='Otras plazas';  //multiplo
				break;
				}
			}
		}
		return $fk;
	}
	else
	{
		return false;
	}
}


/**
 * Consulta la lista de documentos o fuentes que el usuario puede realizar con el programa
 *
 * @param unknown_type $wfuente fuente del documento previamente seleccionada, vacia si no se ha seleccionado
 * @param unknown_type $wusuario usuario del programa
 */
function consultarFuentes($wfuente, $wusuario)
{
	global $conex;
	global $wbasedato;

	if ($wfuente!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$fuentes[0]=$wfuente;
		$cadena="perfue != (mid('".$wfuente."',1,instr('".$wfuente."','-')-1)) AND";
	}
	else
	{
		$fuentes[0]='';
		$cadena='';
	}
	$con=1;
	//consulto los recibos
	$q= "   SELECT perfue, cardes "
	."     FROM ".$wbasedato."_000081, ".$wbasedato."_000040"
	."    WHERE ".$cadena." perusu ='".$wusuario."' and perest='on' and pergra='on' "
	."      AND carfue=perfue and carest='on' and carrec='on' and carabo<>'on'  ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=$con;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$fuentes[$i]=$row1[0].' - '.$row1[1];
		}

		$con=$con+$num1;
	}

	//consulto las notas credito
	$q= "   SELECT perfue, cardes "
	."     FROM ".$wbasedato."_000081, ".$wbasedato."_000040"
	."    WHERE ".$cadena." perusu ='".$wusuario."' and perest='on' and pergra='on' "
	."      AND carfue=perfue and carest='on' and carncr='on'  ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=$con;$i<=$con+$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$fuentes[$i]=$row1[0].' - '.$row1[1];
		}

		$con=$con+$num1;
	}

	//consulto las notas debito
	$q= "   SELECT perfue, cardes "
	."     FROM ".$wbasedato."_000081, ".$wbasedato."_000040"
	."    WHERE ".$cadena." perusu ='".$wusuario."' and perest='on' and pergra='on' "
	."      AND carfue=perfue and carest='on' and carndb='on'   ";
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=$con;$i<$con+$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$fuentes[$i]=$row1[0].' - '.$row1[1];
		}
	}

	if ($wfuente!='')
	{
		$fuentes[$con+$num1]=''; //se deja vacia esta opcion para casos de consulta
	}
	return $fuentes;
}

function consultarResponsables($wempresa, $wbuscador)
{
	global $conex;
	global $wbasedato;

	if ($wbuscador!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$cadena="empraz like '%".$wbuscador."%' and ";
		$wempresas[1]='';
		$acu=1;
	}
	else
	{
		if ($wempresa!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
		{
			$wempresas[1]=$wempresa;
			$cadena="empcod <> (mid('".$wempresa."',1,instr('".$wempresa."','-')-1)) and ";
			$acu=2;
		}
		else
		{
			$cadena='';
			$wempresas[1]='01 - 99999 - PARTICULAR - PARTICULAR';
			$acu=2;

		}
	}

	$q =  " SELECT empcod, empnit, empnom, empraz "
	."   FROM ".$wbasedato."_000024 "
	."  WHERE ".$cadena." empcod = empres and empest='on' "
	."  ORDER BY empraz ";

	$res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

	if ($num>0)
	{
		$ant=1;
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			if ($row[1]!=$ant)
			{
				$wempresas[$acu]=$row[0]." - ".$row[1]." - ".$row[2]." - ".$row[3];
				$acu++;
			}
			$ant=$row[1];
		}
	}else if ($wbuscador!='')
	{
		$q =  " SELECT empcod, empnit, empnom, empraz "
		."   FROM ".$wbasedato."_000024 "
		."  WHERE  empcod = empres and empest='on' "
		."  ORDER BY empraz ";

		$res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
		$ant=1;
		$acu=1;
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			if ($row[1]!=$ant)
			{
				$wempresas[$acu]=$row[0]." - ".$row[1]." - ".$row[2]." - ".$row[3];
				$acu++;
			}
			$ant=$row[1];
		}
	}
	return $wempresas;
}

function consultarConceptos($wconcepto, $wfuente)
{
	global $conex;
	global $wbasedato;

	if ($wconcepto!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$conceptos[0]=$wconcepto;
		$cadena="concod != mid('".$wconcepto."',1,instr('".$wconcepto."','-')-1) AND";
	}
	else
	{
		$conceptos[0]='';
		$cadena='';
	}

	//consulto los conceptos
	$q =  " SELECT concod, condes "
	."   FROM ".$wbasedato."_000044 "
	."  WHERE ".$cadena." "
	."   conest = 'on' "
	."    AND confue = '".$wfuente."' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$conceptos[$i]=$row1[0].'-'.$row1[1];
		}
		if ($conceptos[0]!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
		{
			$conceptos[$num1+1]='';
		}
		return $conceptos;
	}
	else if ($wconcepto!='')
	{

		return $conceptos;
	}
	else
	{
		return false;
	}
}

function consultarGlosas($glosa, $fuefac, $prefac, $numfac)
{

	global $conex;
	global $wbasedato;

	//consulto la fuente para glosas
	$q= "  SELECT carfue "
	."    FROM ".$wbasedato."_000040 "
	."   WHERE carglo = 'on'";
	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);

	$fuente=$row1[0];

	if ($glosa!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$exp=explode('-', $glosa);

		$q = " SELECT  id "
		."   FROM ".$wbasedato."_000021 "
		."  WHERE rdefue = '".$fuente."'"
		."    AND rdeffa = '".$fuefac."' "
		."    AND rdefac = '".$prefac."-".$numfac."' "
		."    AND rdenum = '".$exp[1]."' "
		."    AND rdeest = 'on' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);

		if ($num1>0)
		{
			$glosas[0]=$glosa;
			$cadena=" rdenum<>".$exp[1]." AND";
		}
		else
		{
			$cadena='';
			$glosas[0]='';
		}
	}
	else
	{
		$glosas[0]='';
		$cadena='';
	}

	//consulto los conceptos
	$q = " SELECT  rdenum, rdevca "
	."   FROM ".$wbasedato."_000021 "
	."  WHERE ".$cadena." rdefue = '".$fuente."'"
	."    AND rdeffa = '".$fuefac."' "
	."    AND rdefac = '".$prefac."-".$numfac."' "
	."    AND rdeest = 'on' ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$glosas[$i]=$fuente.'-'.$row1[0].'-'.$row1[1].'$';
		}
		if ($glosas[0]!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
		{
			$glosas[$num1+1]='';
		}
		return $glosas;
	}
	else if ($glosa!='')
	{

		return $glosas;
	}
	else
	{
		return false;
	}
}

function consultarPago($pago)
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT fpache, fpatar "
	."   FROM ".$wbasedato."_000023 where fpacod=(mid('".$pago."',1,instr('".$pago."','-')-1)) and fpaest='on' ";

	$res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
	$row = mysql_fetch_array($res);
	if ($row[0]=='on' or $row[1]=='on')
	{
		return true;
	}

	else
	{
		return false;
	}
}

function consultarPagos($pago)
{
	global $conex;
	global $wbasedato;

	if ($pago) //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$wfpa[1]=$pago;
		$cadena="fpacod <> (mid('".$pago."',1,instr('".$pago."','-')-1)) and ";
		$acu=2;
	}
	else
	{
		$cadena='';
		$wfpa[1]=$pago;
		$acu=1;
	}

	$q =  " SELECT fpacod, fpades "
	."   FROM ".$wbasedato."_000023 where ".$cadena." fpaest='on' "
	."  ORDER BY fpacod ";

	$res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

	if ($num>0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			$wfpa[$acu]=$row[0]."-".$row[1];
			$acu++;
		}
		return $wfpa;
	}
	else
	{
		return false;
	}
}

function consultarCargos($temporal, $cargo, $prefac, $numfac, $fuefac, $wcarndb, $wcarncr, $tipo)
{
	global $conex;
	global $wbasedato;

	if ($cargo and $tipo)
	{
		$cargos[1]=$cargo;
		$contador=2;
		$exp=explode('-',$cargo);
	}
	else
	{
		$contador=1;
	}

	if ($tipo)
	{
		//consulto los cargos para una factura
		$q =  " SELECT fdecon, fdecco, fdeter, fdesal, grudes "
		."   FROM ".$wbasedato."_000065, ".$wbasedato."_000004 "
		."  WHERE  fdedoc = '".$prefac."-".$numfac."' "
		."    AND fdefue = '".$fuefac."'"
		."    AND grucod = fdecon ";
	}
	else
	{
		//consulto los cargos para una factura
		$q =  " SELECT fdecon, fdecco, fdeter, fdesal, grudes "
		."   FROM ".$wbasedato."_000065, ".$wbasedato."_000004 "
		."  WHERE fdecon = '".$cargo."' and fdedoc = '".$prefac."-".$numfac."' "
		."    AND fdefue = '".$fuefac."'"
		."    AND grucod = fdecon ";
	}
	$res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

	if ($num>0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			$guardar=true;
			if ($temporal)
			{
				for ($j=0;$j<count($temporal);$j++)
				{
					$exp=explode('-',$temporal[$j]['temcon']);
					if ($row[0]==$exp[0] and $row[1]==$temporal[$j]['temcco'] and $row[2]==$temporal[$j]['temter'])
					{
						$guardar=false;
					}
				}
			}

			if ($cargo and $tipo)
			{
				$exp=explode('-',$cargos[1]);
				if ($row[0]==$exp[0] and $row[1]==$exp[2] and $row[2]==$exp[3])
				{
					$guardar=false;
				}
			}

			if ($guardar)
			{
				if (($wcarndb and $row[3]>=0) or ($wcarncr and $row[3]>0))
				{
					$cargos[$contador]=$row[0].'-'.$row[4].'-'.$row[1].'-'.$row[2];
					$contador++;
				}
			}
		}
	}

	if ($contador==1)
	{
		return false;
	}
	else
	{
		return $cargos;
	}
}

function consultarValorMaximo ($fuen, $docu, $concepto, $centro, $ter, $wvalmax)
{
	global $wbasedato;
	global $conex;


	//consulto las fuentes para notas debito
	$q= "  SELECT carfue "
	."    FROM ".$wbasedato."_000040 "
	."   WHERE carndb = 'on'" ;
	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($err);

	for ($i=1;$i<=$num;$i++)
	{
		$row = mysql_fetch_array($err) or die (mysql_errno()." - ".mysql_error());

		//consulto las notas debito por concepto de facturación y voy sumando
		$q = " SELECT sum(fdevco) FROM ".$wbasedato."_000021, ".$wbasedato."_000065 WHERE rdeffa = '".$fuen."' AND rdefac = '".$docu."' and rdefue='".$row[0]."' AND rdeest='on' and rdecon='' and rdevco='0' and fdefue= rdefue and fdedoc= rdenum and fdecco= '".$centro."' and fdecon= '".$concepto."' and fdeter= '".$ter."' and fdeest='on' ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row2 = mysql_fetch_row($res);

		$wvalmax=$wvalmax+$row2[0];
	}

	$q= "  SELECT carfue "
	."    FROM ".$wbasedato."_000040 "
	."   WHERE carncr = 'on'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($err);

	for ($i=1;$i<=$num;$i++)
	{
		$row = mysql_fetch_array($err) or die (mysql_errno()." - ".mysql_error());

		//consulto las notas credito por concepto de facturación y voy sumando
		$q = " SELECT sum(fdevco) FROM ".$wbasedato."_000021, ".$wbasedato."_000065 WHERE rdeffa = '".$fuen."' AND rdefac = '".$docu."' and rdefue='".$row[0]."' and rdecon='' and rdevco='0' and rdeest='on' and fdefue= rdefue and fdedoc= rdenum and fdecco= '".$centro."' and fdecon= '".$concepto."' and fdeter= '".$ter."' and fdeest='on' ";

		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row2 = mysql_fetch_row($res);

		$wvalmax=$wvalmax-$row2[0];
	}

	return $wvalmax;

}

function consultarCargo($factura, $fuente, $concepto,  $centro, $tercero, &$valini, &$salcon)
{
	global $wbasedato;
	global $conex;
	$q =  " SELECT fdesal, fdevco "
	."   FROM ".$wbasedato."_000065 "
	."  WHERE fdedoc = '".$factura."' "
	."    AND fdefue = '".$fuente."' "
	."    AND fdecon = '".$concepto."'"
	."    AND fdecco = '".$centro."'"
	."    AND fdeter = '".$tercero."'"
	."    AND fdeest = 'on'";
	$err = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	$mun = mysql_num_rows($err);   // or die (mysql_errno()." - ".mysql_error());;
	$row = mysql_fetch_array($err);

	$valini=$row[1];
	$salcon=$row[0];
}

function consultarOrigenes($banco)
{
	global $conex;
	global $wbasedato;

	if ($banco) //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$bancos[1]=$banco;
		$cadena="bancod <> (mid('".$banco."',1,instr('".$banco."','-')-1)) and ";
		$acu=2;
	}
	else
	{
		$cadena='';
		$acu=1;
	}

	$q =  " SELECT bancod, bannom "
	."   FROM ".$wbasedato."_000069 "
	."  where ".$cadena." banest='on' and bancag<>'on' ";

	$res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

	if ($num>0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			$bancos[$acu]=$row[0]."-".$row[1];
			$acu++;
		}
		return $bancos;
	}
	else
	{
		return false;
	}
}

function consultarDestinos($banco, $fuente, $fpa)
{
	global $conex;
	global $wbasedato;

	//2007-05-03 para recibos de caja se pone el banco destino automaticamente segun forma de pago
	$q= "  SELECT carrec, carcca "
	."    FROM ".$wbasedato."_000040 "
	."   WHERE carfue = '".$fuente."'";
	$err1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($err1);

	if ($row1[0]=='on' and $row1[1]!='on')
	{
		if ($fpa)
		{
			$exp=explode('-',$fpa);
			$q = "SELECT bancod, bannom, bancue "
			."  FROM ".$wbasedato."_000023, ".$wbasedato."_000069 "
			." WHERE fpacod = '".$exp[0]."'"
			."   AND fpaest = 'on' "
			."   AND fpacba = bancod "
			."   AND banest='on' ";

			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$row = mysql_fetch_array($res);
		}

		$q =  " SELECT bancod, bannom, bancue "
		."   FROM ".$wbasedato."_000069 "
		."  where banest='on' and banrec='on' and bancag='on' ";

		$res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row2 = mysql_fetch_array($res2);

		if (isset ($row))
		{
			if ($row[0]!=$row2[0])
			{
				$bancos[1]=$row[0]."-".$row[1]."-".$row[2];
				$bancos[2]=$row2[0]."-".$row2[1]."-".$row2[2];
			}
			else
			{
				$bancos[1]=$row[0]."-".$row[1]."-".$row[2];
			}
		}
		else
		{
			$bancos[1]=$row2[0]."-".$row2[1]."-".$row2[2];
		}

		return $bancos;

	}
	else
	{
		if ($banco) //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
		{
			$bancos[1]=$banco;
			$cadena="bancod <> (mid('".$banco."',1,instr('".$banco."','-')-1)) and ";
			$acu=2;
		}
		else
		{
			$q =  " SELECT bancod, bannom, bancue "
			."   FROM ".$wbasedato."_000069 "
			."  where banest='on' and banrec='on' and bancag='on' ";

			$res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

			$row = mysql_fetch_array($res);

			$bancos[1]=$row[0]."-".$row[1]."-".$row[2];
			$cadena="bancod <> ".$row[0]." and ";
			$acu=2;

		}

		$q =  " SELECT bancod, bannom, bancue "
		."   FROM ".$wbasedato."_000069 "
		."  where ".$cadena." banest='on' and banrec='on' ";

		$res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

		if ($num>0)
		{
			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($res);
				$bancos[$acu]=$row[0]."-".$row[1]."-".$row[2];
				$acu++;
			}
			return $bancos;
		}
		else
		{
			return false;
		}
	}
}


function consultarCausas($wcarncr, $wcarndb)
{
	global $conex;
	global $wbasedato;

	if ($wcarncr) //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$q="select caucod, caunom from ".$wbasedato."_000072 where cauest='on' and cauncr='on' order by caunom ";
	}
	else
	{
		$q="select caucod, caunom from ".$wbasedato."_000072 where cauest='on' and caundb='on' order by caunom  ";
	}

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);

	if ($num1>0)
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$causas[$i]=$row1[0]."-".$row1[1];
		}
		return $causas;
	}
	else
	{
		return false;
	}

}

function consultarLista($numero, $fuente, $wcarncr)
{
	global $conex;
	global $wbasedato;

	if ($wcarncr) //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$q="select caucod, caunom from ".$wbasedato."_000071, ".$wbasedato."_000072 where docfue='".$fuente."' and docnum='".$numero."' and  caucod=doccau and cauest='on' and cauncr='on' order by caunom ";
	}
	else
	{
		$q="select caucod, caunom from ".$wbasedato."_000071, ".$wbasedato."_000072 where docfue='".$fuente."' and docnum='".$numero."' and caucod=doccau and cauest='on' and caundb='on' order by caunom ";
	}

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);

	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$lista[$i]=$row1[0]."-".$row1[1];
		}
		return $lista;
	}
	else
	{
		return false;
	}

}

function buscarFactura($wcarrec, $fuefac, $prefac, $numfac, $wempresa, &$wvalfac, &$wsalfac, &$tipo, $wfuente, $wcarndb, $wcaja, $wcarncr, $wcarcca)
{
	global $conex;
	global $wbasedato;

	$tipo=0;
	$exp=explode('-', $wempresa);

	//Busco si existe la factura
	$q= "   SELECT count(*) "
	."     FROM ".$wbasedato."_000018 "
	."    WHERE fenfac = '".$prefac."-".$numfac."' "
	."      AND fennit = '".trim($exp[1])."'"
	."      AND fenest = 'on' and fenffa='".$fuefac."' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	$row1 = mysql_fetch_array($res1);

	if ($row1[0]<=0)
	{
		$tipo=1;
	}

	//busco si existe la misma factura en estado generado

	if (!$wcarrec and $tipo==0)
	{
		//Busco si existe la factura
		$q= "   SELECT count(*) "
		."     FROM ".$wbasedato."_000018 "
		."    WHERE fenfac = '".$prefac."-".$numfac."' "
		."      AND fennit = '".trim($exp[1])."' "
		."      AND fenest = 'on' and fenffa='".$fuefac."' and fenesf='GE' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		$rol2 = mysql_fetch_array($res1);


		//busco si existe la misma factura en estado devuelto

		//Busco si existe la factura
		$q= "   SELECT count(*) "
		."     FROM ".$wbasedato."_000018 "
		."    WHERE fenfac = '".$prefac."-".$numfac."' "
		."      AND fennit = '".trim($exp[1])."' "
		."      AND fenest = 'on' and fenffa='".$fuefac."' and fenesf='DV' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		$rol3 = mysql_fetch_array($res1);

		//busco si existe la misma factura en estado glosado
		//Busco si existe la factura
		$q= "   SELECT count(*) "
		."     FROM ".$wbasedato."_000018 "
		."    WHERE fenfac = '".$prefac."-".$numfac."' "
		."      AND fennit = '".trim($exp[1])."' "
		."      AND fenest = 'on' and fenffa='".$fuefac."' and fenesf='GL' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		$rol4 = mysql_fetch_array($res1);

		//se le da oportunidad a las notas credito por concenptos de cartera de realizarse en estado radicado
		$rol5[0]=0;
		if($wcarncr and $wcarcca)
		{
			$q= "   SELECT count(*) "
			."     FROM ".$wbasedato."_000018, ".$wbasedato."_000024 "
			."    WHERE fenfac = '".$prefac."-".$numfac."' "
			."      AND fennit = '".trim($exp[1])."' "
			."      AND fenest = 'on' and fenffa='".$fuefac."' and fenesf='RD' "
			."      AND fennit = Empnit and Fencod=Empcod and Empnra='on' ";

			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			$rol5 = mysql_fetch_array($res1);
		}

		if ($rol2[0]<=0 and $rol3[0]<=0 and $rol4[0]<=0 and $rol5[0]<=0)
		{
			$tipo=2;
		}
	}

	if (($wcarrec and $row1[0] > 0) or (!$wcarrec and $row1[0] > 0 and ($rol2[0] > 0 or $rol3[0] > 0 or $rol4[0] > 0 or $rol5[0] > 0 )))
	{
		//se pide valor y saldo de la factura
		$q= "   SELECT fenval, fensal, fenffa, fencmo "
		."     FROM ".$wbasedato."_000018 "
		."    WHERE fenfac = '".$prefac."-".$numfac."' "
		."      AND fenest = 'on' and fenffa='".$fuefac."' AND fennit = '".trim($exp[1])."' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		$row1 = mysql_fetch_array($res1);
		$wvalfac=$row1[0];
		$wsalfac=$row1[1];


		$q="  SELECT temvcf, temvco, temdco "
		."    FROM ".$wbasedato."_000045 "
		."   WHERE temnfa = '".$prefac."-".$numfac."' and temfue='".$wfuente."' and temffa='".$fuefac."' and temcaj='".$wcaja."' ";
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);

		for ($y=0; $y<$num1; $y++)
		{
			$row1 = mysql_fetch_array($res1);
			if ($wcarndb)
			{
				$wsalfac=$wsalfac+$row1[1];
			}
			else
			{
				$wsalfac=$wsalfac-$row1[0]+($row1[1]*$row1[2]);
			}
		}
		return true;
	}
	else
	{
		return false;
	}
}

function incrementarFuente($wfuente)
{
	global $conex;
	global $wbasedato;

	$q = "lock table ".$wbasedato."_000040 LOW_PRIORITY WRITE";
	$errlock = mysql_query($q,$conex);

	$q= "   UPDATE ".$wbasedato."_000040 "
	."      SET carcon = carcon + 1 "
	."    WHERE carfue = '".$wfuente."'"
	."      AND carest = 'on' ";

	$res1 = mysql_query($q,$conex);

	$q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
	$errunlock = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO INCREMENTAR LA FUENTE ".mysql_error());
}

function grabarEncabezado($wfecha, $hora, $wcodfue, $wnrodoc, $total, $wempcod, $wempnom, $wcaja, $wusuario, $wcco, $wobs)
{
	global $conex;
	global $wbasedato;

	//GRABO EN LA TABLA DEL -- <ENCABEZADO DE RECIBOS Y NOTAS> -- EN EL **** RECIBO DE CAJA ****
	$q= " INSERT INTO ".$wbasedato."_000020 (   Medico       ,   Fecha_data,   Hora_data,    renfue       ,  rennum   ,                     renvca                                             ,   rencod      ,   rennom      ,   rencaj    ,   renusu      ,   rencco    , renest , renfec,  renobs,  Seguridad       ) "
	."                               VALUES ('".$wbasedato."','".date('Y-m-d')."','".$hora."' ,'".$wcodfue."',".strtoupper($wnrodoc).",".number_format(($total),0,'.','').",'".trim($wempcod)."','".trim($wempnom)."','".$wcaja."','".$wusuario."','".$wcco."', 'on', '".$wfecha."'   , '".$wobs."' ,  'C-".$wusuario."')";
	$resins = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL DOCUMENTO ".mysql_error());
}

function anularEncabezado($fuen, $docu, $wcco)
{
	global $conex;
	global $wbasedato;

	$q="     UPDATE ".$wbasedato."_000020 SET renest = 'off' WHERE renfue = '".$fuen."' AND rennum = '".$docu."' AND rencco = '".$wcco."'";
	$res = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ANULAR EL ENCABEZADO DEL DOCUMENTO ".mysql_error());
}

function anularPagos($fuen, $docu, $wcco)
{
	global $conex;
	global $wbasedato;

	$q="     UPDATE ".$wbasedato."_000022 SET rfpest = 'off' WHERE rfpfue='".$fuen."' and rfpnum='".$docu."' and rfpcco='".$wcco."' ";
	$resdel = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HAN PODIDO ANULAR LAS FORMAS DE PAGO DEL DOCUMENTO".mysql_error());
}

function anularDetalle($id, $wfuedoc, $wnrodoc, $wcco)
{
	global $conex;
	global $wbasedato;

	IF ($id)
	{
		$q="     UPDATE ".$wbasedato."_000021 SET rdeest = 'off' WHERE id = ".$id;
	}
	else
	{
		$q="     UPDATE ".$wbasedato."_000021 SET rdeest = 'off' "
		."   WHERE rdefue = '".trim($wfuedoc)."' AND rdenum = '".$wnrodoc."' AND rdecco = '".$wcco."' ";
	}
	$resdel = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HAN PODIDO ANULAR EL DETALLE DEL DOCUMENTO".mysql_error());
}


function anularCausas($fuen, $docu)
{
	global $conex;
	global $wbasedato;

	$q="     UPDATE ".$wbasedato."_000071 SET docest = 'off' WHERE docfue='".$fuen."' and docnum='".$docu."' ";
	$resdel = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ANULAR LAS CAUSAS DEL DOCUMENTO".mysql_error());
}

function consultarPaciente($fueFac, $numFac, &$historia, &$numIng)
{
	global $conex;
	global $wbasedato;
	global $wemphos;

	
	if ($wemphos=="on")  //Indica que la empresa tiene facturacion hospitalaria
	   {
		$h="select B.tcarhis, B.tcaring from ".$wbasedato."_000066 A,  ".$wbasedato."_000106 B where rcfffa='".$fueFac."' and rcffac='".$numFac."' and A.rcfreg=B.id ";
	
		$errh = mysql_query($h,$conex) or die (mysql_errno()." -NO SE HA ENCONTRADO EL PACIENTE ASOCIADO A LA FACTURA ".mysql_error());
		$rowh = mysql_fetch_array($errh) or die (mysql_errno()." -NO SE HA ENCONTRADO EL PACIENTE ASOCIADO A LA FACTURA ".mysql_error());
		$historia=$rowh[0];
		$numIng=$rowh[1];
       }
      else
         {
	      $historia=0;
		  $numIng=0;   
         }     	
}

function consultarCostos($wfuenCod, $wnrodoc, $wcco)
{
	global $wbasedato;
	global $conex;

	$costos[0]=$wcco;

	//consulto el encabezado del documento
	$q = " SELECT rencco "
	."   FROM ".$wbasedato."_000020 "
	."  WHERE renfue = '".$wfuenCod."'"
	."    AND rennum = ".$wnrodoc
	."    AND rencco != '".$wcco."' ";

	$res = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO REALIZAR LA BUSQUEDA DE CENTROS DE COSTOS CON LOS PARAMETROS SELECCIONADOS ".mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			$costos[$i]=$row[0];
		}

	}
	return $costos;

}


function grabarDetalle($wfecha, $hora, $wcodfue, $wnrodoc, $wusuario, $wcco, $numFac, $canfac, $nomcon, $cancon, $fuefac, $salfac, $historia, $numIng, $centro, $glosa)
{
	global $conex;
	global $wbasedato;

	if ($glosa!='')
	{
		$exp=explode('-', $glosa);
		$q= " INSERT INTO ".$wbasedato."_000021 (   Medico       , Fecha_data,   Hora_data,   rdefue        ,  rdenum    ,               rdecco    ,   rdefac    , rdevta ,   rdevca    , rdeest,    rdecon    ,  rdevco    ,        rdeffa,          rdesfa,        rdehis,        rdeing,     rdeccc,  rdeglo,     Seguridad        ) "
		."                            	VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wcodfue."',".strtoupper($wnrodoc).",'".$wcco."','".$numFac."', '' ,'".$canfac."', 'on'   ,'".$nomcon."','".$cancon."', '".$fuefac."',           '".$salfac."', '".$historia."', '".$numIng."', '".$centro."', '".$exp[0]."-".$exp[1]."', 'C-".$wusuario."')";
	}
	else
	{
		$q= " INSERT INTO ".$wbasedato."_000021 (   Medico       , Fecha_data,   Hora_data,   rdefue        ,  rdenum    ,               rdecco    ,   rdefac    , rdevta ,   rdevca    , rdeest,    rdecon    ,  rdevco    ,        rdeffa,          rdesfa,        rdehis,        rdeing,     rdeccc,  rdeglo,     Seguridad        ) "
		."                            	VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wcodfue."',".strtoupper($wnrodoc).",'".$wcco."','".$numFac."', '' ,'".$canfac."', 'on'   ,'".$nomcon."','".$cancon."', '".$fuefac."',           '".$salfac."', '".$historia."', '".$numIng."', '".$centro."', '', 'C-".$wusuario."')";

	}

	$resins = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR CORRECTAMENTE EL DETALLE DEL DOCUMENTO ".mysql_error());
}

function realizarRedesgloce($fuen, $docu, $concepto, $centro, $ter, $wvalmax, $wusuario, $wfecha, $hora)
{
	global $wbasedato;
	global $conex;

	//consulto el valor incial de la factura
	$q1=" select (fenval+fenviv+fencop+fencmo+fendes+fenabo) from ".$wbasedato."_000018  where fenffa='".$fuen."' and fenfac='".$docu."' ";
	$res1 = mysql_query($q1,$conex) or die (mysql_errno()." - ".mysql_error());
	$row1 = mysql_fetch_row($res1);
	$valfac=$row1[0]-$wvalmax;

	//primero consulto los conceptos para esa factura
	$q =  " SELECT fdecon, fdecco, fdeter, fdevco "
	."   FROM ".$wbasedato."_000065 "
	."  WHERE fdedoc = '".$docu."' "
	."    AND fdefue = '".$fuen."' and fdeest='on' and fdecon not in (select grucod from ".$wbasedato."_000004 where gruabo='on') ";

	$err = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	$num = mysql_num_rows($err);   // or die (mysql_errno()." - ".mysql_error());;

	//consulto el verdadero valor para concepto y lo meto en un vector
	for ($i=1;$i<=$num;$i++)
	{
		$row = mysql_fetch_array($err) or die (mysql_errno()." - ".mysql_error());

		$vector['con'][$i]=$row[0];
		$vector['cco'][$i]=$row[1];
		$vector['ter'][$i]=$row[2];
		$vector['valmax'][$i]=consultarValorMaximo($fuen, $docu, $row[0], $row[1], $row[2], $row[3]);
		$valfac=$valfac-($row[3]-$vector['valmax'][$i]); //es el valor de la factura menos las notas que se le han hecho

		if ($vector['con'][$i]==trim($concepto) and $vector['cco'][$i]==trim($centro) and $vector['ter'][$i]==trim($ter))
		{
			$vector['valmax'][$i]=$vector['valmax'][$i]-$wvalmax;

		}
	}

	//consulto las fuentes para notas debito para redesglosarlas

	$q= "  SELECT carfue "
	."    FROM ".$wbasedato."_000040 "
	."   WHERE carndb = 'on'";

	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

	$num = mysql_num_rows($err);

	for ($i=1;$i<=$num;$i++) //para cada fuente
	{
		$row = mysql_fetch_array($err) or die (mysql_errno()." - ".mysql_error());

		//consulto las notas debito por concepto de cartera
		$q = " SELECT SUM(rdevco), rdefue, rdenum FROM ".$wbasedato."_000021 WHERE rdeest='on' and rdeffa = '".$fuen."' AND rdefac = '".$docu."' and rdefue='".$row[0]."' and rdecon<>'' and rdevco<>'' group by rdefue, rdenum";

		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num2 = mysql_num_rows($res);

		for ($j=1;$j<=$num2;$j++) //para cada nota
		{
			//recorro el vector de conceptos para acomodar los valores
			$row2 = mysql_fetch_row($res);

			$q = "  DELETE FROM ".$wbasedato."_000065 "
			."   WHERE fdefue='".$row2[1]."'  and  fdedoc='".$row2[2]."' and fdefac='".$docu."' and fdeffa='".$fuen."' and fdeest='on' ";
			$res5 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

			for ($k=1;$k<=count($vector['con']);$k++)
			{
				$porcen=$vector['valmax'][$k]+round($vector['valmax'][$k]*$row2[0]/($valfac));
				$val=round($vector['valmax'][$k]*$row2[0]/($valfac));

				if ($vector['con'][$k]==trim($concepto) and $vector['cco'][$k]==trim($centro) and $vector['ter'][$k]==trim($ter) )
				{
					$vector['valmax'][$k]=$porcen;
					$porcen=$porcen-$wvalmax;
					$vector['valmax'][$k];

				}
				else
				{
					$vector['valmax'][$k]=$porcen;
				}

				$q= "   UPDATE ".$wbasedato."_000065 "
				."      SET fdesal = '".$porcen."' "
				."    where fdefue='".$fuen."' and fdedoc='".$docu."' and fdecco='".$vector['cco'][$k]."' and fdecon='".$vector['con'][$k]."' and fdeter='".$vector['ter'][$k]."'";
				$resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());


				$q= " INSERT INTO ".$wbasedato."_000065 (   Medico,   Fecha_data,   Hora_data,   fdefue,           fdedoc ,    fdeest  ,    fdecco    ,   fdecon    ,                       fdevco ,         fdeter    ,           fdepte,  fdesal, fdeffa, fdefac, Seguridad        ) "
				."     VALUES ('".$wbasedato."',   '".$wfecha."', '".$hora."' ,'".$row2[1]."', '".$row2[2]."',                        'on' , '".$vector['cco'][$k]."', '".$vector['con'][$k]."' , '".$val."' , '".$vector['ter'][$k]."'  , 0 , '".$porcen."'  , '".$fuen."'  ,'".$docu."'  , 'C-".$wusuario."') ";
				$resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

			}
			$valfac=$valfac+$row2[0];
		}
	}

	//consulto notas credito

	$q= "  SELECT carfue "
	."    FROM ".$wbasedato."_000040 "
	."   WHERE carncr = 'on'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($err);


	for ($i=1;$i<=$num;$i++) //para cada fuente
	{
		$row = mysql_fetch_array($err) or die (mysql_errno()." - ".mysql_error());

		//consulto las notas debito por concepto de cartera
		$q = " SELECT sum(rdevco), rdefue, rdenum FROM ".$wbasedato."_000021 WHERE rdeest='on' and rdeffa = '".$fuen."' AND rdefac = '".$docu."' and rdefue='".$row[0]."' and rdecon<>'' and rdevco<>'' group by rdefue, rdenum";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num2 = mysql_num_rows($res);

		for ($j=1;$j<=$num2;$j++) //para cada recibo
		{
			//recorro el vector de conceptos para acomodar los valores
			$row2 = mysql_fetch_row($res);


			$q = "  DELETE FROM ".$wbasedato."_000065 "
			."   WHERE fdefue='".$row2[1]."'  and  fdedoc='".$row2[2]."' and fdefac='".$docu."' and fdeffa='".$fuen."' and fdeest='on' ";
			$res5 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

			for ($k=1;$k<=count($vector['con']);$k++)
			{
				$porcen=$vector['valmax'][$k]-round($vector['valmax'][$k]*$row2[0]/($valfac));

				$val=round($vector['valmax'][$k]*$row2[0]/($valfac));


				if ($vector['con'][$k]==trim($concepto) and $vector['cco'][$k]==trim($centro) and $vector['ter'][$k]==trim($ter) )
				{
					$vector['valmax'][$k]=$porcen;
					$porcen=$porcen+$wvalmax;
					$vector['valmax'][$k];

				}
				else
				{
					$vector['valmax'][$k]=$porcen;
				}
				//echo 'por1:'.$porcen;
				$q= "   UPDATE ".$wbasedato."_000065 "
				."      SET fdesal = '".$porcen."' "
				."    where fdefue='".$fuen."' and fdedoc='".$docu."' and fdecco='".$vector['cco'][$k]."' and fdecon='".$vector['con'][$k]."' and fdeter='".$vector['ter'][$k]."'";
				$resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());


				$q= " INSERT INTO ".$wbasedato."_000065 (   Medico       ,   Fecha_data,   Hora_data,   fdefue,           fdedoc ,    fdeest  ,    fdecco    ,   fdecon    ,                       fdevco ,         fdeter    ,           fdepte,  fdesal, fdeffa, fdefac, Seguridad        ) "
				."     VALUES ('".$wbasedato."',   '".$wfecha."', '".$hora."' ,'".$row2[1]."', '".$row2[2]."',                        'on' , '".$vector['cco'][$k]."', '".$vector['con'][$k]."' , '".$val."' , '".$vector['ter'][$k]."'  , 0 , '".$porcen."'  , '".$fuen."'  ,'".$docu."'  , 'C-".$wusuario."') ";
				$resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

			}
			$valfac=$valfac-$row2[0];

		}

	}

	//consulto recibos

	$q= "  SELECT carfue "
	."    FROM ".$wbasedato."_000040 "
	."   WHERE carrec = 'on'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($err);

	for ($i=1;$i<=$num;$i++) //para cada fuente
	{
		$row = mysql_fetch_array($err) or die (mysql_errno()." - ".mysql_error());

		$q = " SELECT rdevca, rdevco, rdecon, rdefue, rdenum FROM ".$wbasedato."_000021 WHERE rdeest='on' and rdeffa = '".$fuen."' AND rdefac = '".$docu."' and rdefue='".$row[0]."' ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num2 = mysql_num_rows($res);

		for ($j=1;$j<=$num2;$j++) //para cada recibo
		{

			$row2 = mysql_fetch_row($res);

			if ($row2[2]=='')
			{
				$sig=1;
			}
			else
			{
				$exp=explode('-',$row2[2]);
				$sig=consultarMulti($exp[0], $row2[3]);
			}

			if ($j==1)
			{
				//echo 'hola';
				$contador=1;
				$acumulador ['fuente'][$contador]=$row2[3];
				$acumulador ['numero'][$contador]=$row2[4];
				$acumulador ['valor'][$contador]=$row2[0]+($row2[1]*$sig);
			}else
			{
				if ($row2[3]==$acumulador ['fuente'][$contador] and $row2[4]==$acumulador ['numero'][$contador])
				{
					$acumulador ['valor'][$contador]=$acumulador ['valor'][$contador]+$row2[0]+($row2[1]*$sig);
				}else
				{
					$contador++;
					$acumulador ['fuente'][$contador]=$row2[3];
					$acumulador ['numero'][$contador]=$row2[4];
					$acumulador ['valor'][$contador]=$row2[0]+($row2[1]*$sig);
				}

			}
		}


		if (isset ($acumulador ['fuente']) and $num2>0)
		{
			for ($j=1;$j<=count($acumulador ['fuente']);$j++) //para cada recibo
			{
				//recorro el vector de conceptos para acomodar los valores

				//echo '-'.$acumulador ['valor'][$j].'-';
				$q = "  DELETE FROM ".$wbasedato."_000065 "
				."   WHERE fdefue='".$acumulador ['fuente'][$j]."'  and  fdedoc='".$acumulador ['numero'][$j]."' and fdefac='".$docu."' and fdeffa='".$fuen."' and fdeest='on' ";
				$res5 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

				for ($k=1;$k<=count($vector['con']);$k++)
				{
					$porcen=$vector['valmax'][$k]-round($vector['valmax'][$k]*$acumulador ['valor'][$j]/($valfac));
					$val=round($vector['valmax'][$k]*$acumulador ['valor'][$j]/($valfac));


					if ($vector['con'][$k]==trim($concepto) and $vector['cco'][$k]==trim($centro) and $vector['ter'][$k]==trim($ter) )
					{
						$vector['valmax'][$k]=$porcen;
						$porcen=$porcen+$wvalmax;
						$vector['valmax'][$k];

					}
					else
					{
						$vector['valmax'][$k]=$porcen;
					}


					$q= "   UPDATE ".$wbasedato."_000065 "
					."      SET fdesal = '".$porcen."' "
					."    where fdefue='".$fuen."' and fdedoc='".$docu."' and fdecco='".$vector['cco'][$k]."' and fdecon='".$vector['con'][$k]."' and fdeter='".$vector['ter'][$k]."'";
					$resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());



					$q= " INSERT INTO ".$wbasedato."_000065 (   Medico       ,   Fecha_data,   Hora_data,   fdefue,           fdedoc ,    fdeest  ,    fdecco    ,   fdecon    ,                       fdevco ,         fdeter    ,           fdepte,  fdesal, fdeffa, fdefac, Seguridad        ) "
					."     VALUES ('".$wbasedato."',   '".$wfecha."', '".$hora."' ,'".$acumulador ['fuente'][$j]."', '".$acumulador ['numero'][$j]."',                        'on' , '".$vector['cco'][$k]."', '".$vector['con'][$k]."' , '".$val."' , '".$vector['ter'][$k]."'  , 0 , '".$porcen."'  , '".$fuen."'  ,'".$docu."'  , 'C-".$wusuario."') ";
					$resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

				}
				$valfac=$valfac-$acumulador ['valor'][$j];

			}
		}
	}

}

function grabarConcepto($fueFac, $numFac, $centro, $nomcon, $ter, $cancon, $wfecha, $hora, $wfuenCod, $wnrodoc, $wcco, $wusuario, $ref )
{
	global $wbasedato;
	global $conex;

	$q=" select fdesal from ".$wbasedato."_000065  where fdefue='".$fueFac."' and fdedoc='".$numFac."' and fdecco='".$centro."' and fdecon='".$nomcon."' and fdeter='".$ter."'";

	$res3 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num3 = mysql_num_rows($res3) or die (mysql_errno()." - ".mysql_error());
	$row3 = mysql_fetch_array($res3);


	if($ref=='')
	{
		$consal=$row3[0]-$cancon;
		$cancon=abs($cancon);
	}
	else
	{
		$consal=0;
	}

	$q= " INSERT INTO ".$wbasedato."_000065 (   Medico       ,   Fecha_data,   Hora_data,   fdefue,           fdedoc ,  fdeest  ,   fdecco    ,   fdecon    ,        fdevco ,         fdeter    , fdepte,  fdesal,  Seguridad        ) "
	."                            VALUES ('".$wbasedato."',   '".$wfecha."', '".$hora."' ,'".$wfuenCod."', '".$wnrodoc."', 'on' , '".$centro."', '".$nomcon."' , '".$cancon."' , '".$ter."'  , 0 , '".$consal."'  , 'C-".$wusuario."') ";
	$resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

	$q= "   UPDATE ".$wbasedato."_000065 "
	."      SET fdesal = '".$consal."' "
	."    where fdefue='".$fueFac."' and fdedoc='".$numFac."' and fdecco='".$centro."' and fdecon='".$nomcon."' and fdeter='".$ter."'";


	$resins = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PRODIDO GRABAR LA NOTA CON CONCEPTOS DE FACTURACION ".mysql_error());
}

function anularConcepto($valor, $numfac, $fuefac, $concepto, $cco, $ter, $id)
{
	global $wbasedato;
	global $conex;

	$q= "   UPDATE ".$wbasedato."_000065 "
	."      SET fdesal = fdesal - ".($valor)
	."    where fdefue='".$fuefac."' and fdedoc='".$numfac."' and fdecon='".$concepto."' and fdecco='".$cco."' and fdeter='".$ter."'";

	$resins = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PRODIDO DEVOLVER EL SALDO DE UNO DE LOS CONCEPTOS ".mysql_error());

	//anulo tabla 65
	$q="    UPDATE ".$wbasedato."_000065 SET fdeest = 'off' WHERE id = ".$id;
	$resdel = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PRODIDO ANULAR EL CONCEPTO DE FACTURACION ".mysql_error());
}

function actualizarFactura($val1, $fueFac, $numFac, $wcarndb, $wcarrec, $wcarncr)
{
	global $wbasedato;
	global $conex;


	if ($wcarncr)
	{
		$q= "  UPDATE ".$wbasedato."_000018 "
		."     SET fensal = fensal + ".$val1
		."        ,fenvnc = fenvnc - ".$val1
		."   WHERE fenfac = '".$numFac."' and fenffa='".$fueFac."'  ";
	}

	if ($wcarndb)
	{

		$q= "  UPDATE ".$wbasedato."_000018 "
		."     SET fensal = fensal + ".$val1
		."        ,fenvnd = fenvnd + ".$val1
		."   WHERE fenfac = '".$numFac."' and fenffa='".$fueFac."'  ";
	}

	if ($wcarrec)
	{
		$q= "  UPDATE ".$wbasedato."_000018 "
		."     SET fensal = fensal + ".$val1
		."        ,fenrbo = fenrbo - ".$val1
		."   WHERE fenfac = '".$numFac."' and fenffa='".$fueFac."'  ";
	}

	$resupd = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA ACTUALIZADO EL SALDO DE LA FACTURA ".mysql_error());
}

function borrarRegistro($id)
{
	global $wbasedato;
	global $conex;

	$q = " DELETE FROM ".$wbasedato."_000045 "
	."  WHERE id = ".$id;
	$res_bor = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO BORRAR EL REGISTRO DE LA TABLA TEMPORAL ".mysql_error());

}

function cuadrarSaldos($factura, $fuente, $valor, $tipo, $documento, $fuendoc, $wfecha, $hora, $wusuario )
{
	global $wbasedato;
	global $conex;

	$q1=" select fensal from ".$wbasedato."_000018  where fenffa='".$fuente."' and fenfac='".$factura."' ";
	$res1 = mysql_query($q1,$conex) or die (mysql_errno()." - ".mysql_error());
	$row1 = mysql_fetch_row($res1);
	$salfac=$row1[0];

	$q2=" select fdesal, fdecco, fdecon, fdeter from ".$wbasedato."_000065  where fdefue='".$fuente."' and fdedoc='".$factura."' and fdeest='on' and fdecon not in (select grucod from ".$wbasedato."_000004 where gruabo='on') ";
	$res2 = mysql_query($q2,$conex) or die (mysql_errno()." - ".mysql_error());
	$num2 = mysql_num_rows($res2);

	for ($i=1;$i<=$num2;$i++)
	{
		$row2 = mysql_fetch_row($res2);

		if($tipo)
		{
			$porcen=$row2[0]+round(-$row2[0]*$valor/($salfac+$valor));
			$val=round(-$row2[0]*$valor/($salfac+$valor));
		}else
		{
			$porcen=$row2[0]-round($row2[0]*$valor/($salfac+$valor));
			$val=round($row2[0]*$valor/($salfac+$valor));
		}

		$q= "   UPDATE ".$wbasedato."_000065 "
		."      SET fdesal = '".$porcen."' "
		."    where fdefue='".$fuente."' and fdedoc='".$factura."' and fdecco='".$row2[1]."' and fdecon='".$row2[2]."' and fdeter='".$row2[3]."'";
		$resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());


		$q= " INSERT INTO ".$wbasedato."_000065 (   Medico       ,   Fecha_data,   Hora_data,   fdefue,           fdedoc ,    fdeest  ,    fdecco    ,   fdecon    ,        fdevco ,         fdeter    , fdepte,  fdesal, fdeffa, fdefac, Seguridad        ) "
		."     VALUES ('".$wbasedato."',   '".$wfecha."', '".$hora."' ,'".$fuendoc."', '".$documento."', 'on' , '".$row2[1]."', '".$row2[2]."' , '".$val."' , '".$row2[3]."'  , 0 , '".$porcen."'  , '".$fuente."'  ,'".$factura."'  , 'C-".$wusuario."') ";
		$resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

	}
}

function DevolverSaldos($factura, $fuente, $wcarndb, $documento, $fuendoc )
{
	global $wbasedato;
	global $conex;


	// consulto los conceptos para la factura
	$q2=" select fdesal, fdecco, fdecon, fdeter from ".$wbasedato."_000065  where fdefue='".$fuente."' and fdedoc='".$factura."' ";

	$res2 = mysql_query($q2,$conex) or die (mysql_errno()." - ".mysql_error());
	$num2 = mysql_num_rows($res2);


	for ($i=1;$i<=$num2;$i++)
	{
		$row2 = mysql_fetch_row($res2);
		//consulto el valor que se le resto o sumo al documento

		$q=" select fdevco from ".$wbasedato."_000065  where fdefue='".$fuendoc."' and fdedoc='".$documento."' and fdecon='".$row2[2]."' and fdecco='".$row2[1]."' and fdeter='".$row2[3]."' and fdeffa='".$fuente."' and fdefac='".$factura."'";

		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);
		$row = mysql_fetch_row($res);

		if ($num >0)
		{
			if($wcarndb)
			{
				$porcen=$row2[0]-$row[0];
			}else
			{
				$porcen=$row2[0]+$row[0];
			}


			$q= "   UPDATE ".$wbasedato."_000065 "
			."      SET fdesal = '".$porcen."' "
			."    where fdefue='".$fuente."' and fdedoc='".$factura."' and fdecco='".$row2[1]."' and fdecon='".$row2[2]."' and fdeter='".$row2[3]."'";
			$resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

			$q= "   UPDATE ".$wbasedato."_000065 "
			."      SET fdeest = 'off' "
			."    where fdefue='".$fuendoc."' and fdedoc='".$documento."' and fdecco='".$row2[1]."' and fdecon='".$row2[2]."' and fdeter='".$row2[3]."'";
			$resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		}
	}
}

function grabarCausas($causas, $wfecha, $hora, $wfuenCod, $wnrodoc )
{
	global $wbasedato;
	global $conex;
	global $wusuario;

	for ($j=0;$j<count($causas);$j++)
	{
		$causa=explode('-',$causas[$j]);
		$q= " INSERT INTO ".$wbasedato."_000071 (   Medico       ,   Fecha_data,   Hora_data,          docfue        ,  docnum              ,   doccau   ,     docest    , Seguridad        ) "
		."                            VALUES ('".$wbasedato."',  '".$wfecha."',  '".$hora."' ,   '".$wfuenCod."',".strtoupper($wnrodoc).",'".$causa[0]."',   'on',    'C-".$wusuario."')";

		$resins = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GUARDAR LAS CAUSAS DEL DOCUMENTO ".mysql_error());
	}
}

function consultarCausa($wcarncr, $wfuedoc, $wnrodoc)
{
	global $wbasedato;
	global $conex;

	if ($wcarncr)
	$q="select doccau, caunom FROM ".$wbasedato."_000071, ".$wbasedato."_000072 where docfue='".$wfuedoc."' and docnum='".$wnrodoc."' and caucod=doccau and cauest='on' and docest='on' and cauncr='on'  " ;
	else
	$q="select doccau, caunom FROM ".$wbasedato."_000071, ".$wbasedato."_000072 where docfue='".$wfuedoc."' and docnum='".$wnrodoc."' and caucod=doccau and cauest='on' and docest='on' and caundb='on'  " ;

	$res3 = mysql_query($q,$conex);
	$num3 = mysql_num_rows($res3);

	if ($num3>0)
	{
		//pinto vector de causas
		for ($j=0;$j<$num3;$j++)
		{
			$row3 = mysql_fetch_array($res3);
			$lista[$j]=$row3[0].'-'.$row3[1];
		}
		return $lista;
	}
	else
	{
		return false;
	}
}

function grabarPagos($fk, $wfecha, $hora, $wfuenCod, $wnrodoc, $wcco, $wcaja,  $wusuario)
{
	global $wbasedato;
	global $conex;

	for ($j=1;$j<=count($fk);$j++)
	{
		$wcodfpa=explode("-",$fk[$j][0]);
		$expban2=explode('-',$fk[$j][5]);
		if ($fk[$j][8])
		{
			$expban=explode('-',$fk[$j][2]);
			$expubi=explode('-',$fk[$j][3]);
			$ubica[$j]=substr($expubi[1],0,1);
			$q= " INSERT INTO ".$wbasedato."_000022 (   Medico       , Fecha_data,   Hora_data,    rfpfue              ,   rfpnum    ,                rfpfpa              ,  rfpvfp        ,   rfpdan         ,   rfpobs     , rfpest,   rfpcco  ,  rfppla,           rfpaut,       rfpecu,  rfpcaf,  rfpban,   rfpbai,   Seguridad        ) "
			."                             VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".trim($wfuenCod)."',".strtoupper($wnrodoc).",'".trim($wcodfpa[0])."',".$fk[$j][6].",'".$fk[$j][1]."','".$expban[0]."', 'on'  ,'".$wcco."', '".$ubica[$j]."', '".$fk[$j][4]."', 'S', '".$wcaja."',  '".$expban2[0]."', '".$expban2[0]."', 'C-".$wusuario."') ";
		}else
		{
			$q= " INSERT INTO ".$wbasedato."_000022 (   Medico       ,   Fecha_data,   Hora_data,   rfpfue              ,  rfpnum    ,   rfpfpa              ,  rfpvfp        ,   rfpdan         ,   rfpobs         , rfpest,   rfpcco  ,          rfpecu,  rfpcaf,   rfpban,   rfpbai, Seguridad        ) "
			."                             VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".trim($wfuenCod)."',".strtoupper($wnrodoc).",'".trim($wcodfpa[0])."',".$fk[$j][6].",'".$fk[$j][1]."','".$fk[$j][2]."', 'on'  ,'".$wcco."', 'S', '".$wcaja."',  '".$expban2[0]."', '".$expban2[0]."', 'C-".$wusuario."') ";
		}
		$resfpa = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GRABAR CORRECTAMENTE LAS FORMAS DE PAGO ".mysql_error());
	}
}
/***********************************************FUNCIONES DE MODELO*************************************/

function calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref)
{
	$wtotvcafac=0; //valor a cancelar factura
	$wtotvalcon=0; //valor a cancelar concepto

	for ($k=0;$k<count($temporal);$k++)
	{
		if (!$wcarndb)
		{
			$wtotvcafac = $wtotvcafac + $temporal[$k]['temvcf'];
			$wtotvalcon = $wtotvalcon - ($temporal[$k]['temvco']*$temporal[$k]['temdco']);
		}
		else
		{
			$wtotvalcon = $wtotvalcon + ($temporal[$k]['temvco']*$temporal[$k]['temdco']);
		}
	}

	if($ref=='')
	{
		$total=$wtotvcafac+$wtotvalcon;
	}
	else
	{
		$total=$wtotvalcon;
	}
}

function calcularTotales2($temporal, &$wtotvcafac, &$wtotvalcon, &$total)
{
	$wtotvcafac=0; //valor a cancelar factura
	$wtotvalcon=0; //valor a cancelar concepto

	for ($k=0;$k<count($temporal);$k++)
	{
		if (isset($temporal[$k]['chk']))
		{
			$wtotvcafac = $wtotvcafac + $temporal[$k]['temvcf'];
			$wtotvalcon = $wtotvalcon - ($temporal[$k]['temvco']*$temporal[$k]['temdco']);
		}
		$total=$wtotvcafac+$wtotvalcon;
	}
}

function agregarCausa($lista, $cauSel)
{
	if ($lista!='')
	{
		$lista[count($lista)]=$cauSel;
	}
	else
	{
		$lista[0]=$cauSel;
	}
	return $lista;
}

function eliminarCausa($lista, $index)
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

function crearRefacturacion($prefac, $numfac, $fuefac, $wusuario, $wcaja, $wfuente, $wempresa, $wnrodoc, $wfecdoc, $wcco, $wvalfac, $wsalfac, $glosa)
{
	global $wbasedato;
	global $conex;

	//primero nos aseguramos que esa factura no tenga ningun documento de notas o recibos
	$q= "   SELECT * "
	."     FROM ".$wbasedato."_000021, ".$wbasedato."_000040"
	."  WHERE  rdefac = '".$prefac."-".$numfac."' "
	."    AND rdeffa = '".$fuefac."'"
	."    AND rdeest = 'on'"
	."    AND rdefue = carfue "
	."    AND carest = 'on' "
	."    AND carrec = 'on' "
	."    AND carabo <> 'on' "
	."    UNION "
	."   SELECT * "
	."     FROM ".$wbasedato."_000021, ".$wbasedato."_000040"
	."  WHERE  rdefac = '".$prefac."-".$numfac."' "
	."    AND rdeffa = '".$fuefac."'"
	."    AND rdeest = 'on'"
	."    AND rdefue = carfue "
	."    AND carest = 'on' "
	."    AND carncr = 'on' "
	."    UNION "
	."   SELECT * "
	."     FROM ".$wbasedato."_000021, ".$wbasedato."_000040"
	."  WHERE  rdefac = '".$prefac."-".$numfac."' "
	."    AND rdeffa = '".$fuefac."'"
	."    AND rdeest = 'on'"
	."    AND rdefue = carfue "
	."    AND carest = 'on' "
	."    AND carndb = 'on' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1<=0)
	{

		//se consultan todos los cargos de la factura incluyendo los abonos, es decir el valor incial de todo
		//consulto los cargos para una factura
		$q =  " SELECT fdecon, fdecco, fdeter, fdevco, grudes, fdesal "
		."   FROM ".$wbasedato."_000065, ".$wbasedato."_000004 "
		."  WHERE  fdedoc = '".$prefac."-".$numfac."' "
		."    AND fdefue = '".$fuefac."'"
		."    AND grucod = fdecon ";

		$res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
		$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

		if ($num>0)
		{
			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($res);
				$nomcon=$row[0].'-'.$row[4].'-'.$row[1].'-'.$row[2];
				//se guarda cada uno de los datos en tabla temporal
				guardarTemporal(date('Y-m-d'), (string)date("H:i:s"), $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, $prefac, $numfac, $row[5], $wvalfac, $wsalfac, '', 'on', $fuefac, '', $wusuario, $nomcon, $row[3], false, true, false, false, true, $glosa);
				$wsalfac=$wsalfac-$row[3];
			}
		}

		$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
		return $temporal;
	}
	else
	{
		pintarAlert3('NO SE PUEDE REALIZAR NOTA CREDITO DE REFACTURACION DEBIDO A LA EXISTENCIA DE OTRO DOCUMENTO');
		return false;
	}

}
/***********************************************FUNCIONES DE PRESENTACION*************************************/
function pintarVersion()
{
	$wautor="Carolina Castaño P.";
	$wversion="2007-12-27";
	echo "<table align='right'>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
	echo "</tr>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br></br>" ;
}

function pintarAlert1($mensaje)
{
	echo "</table>";
	echo"<form action='recibosNotasN_1.php' method='post' name='form1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>".$mensaje."</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:window.close()'></td><tr>";
	echo "</table></fieldset></form>";
}

function pintarAlert2($mensaje)
{
	echo "</table>";
	echo"<CENTER>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>".$mensaje."</td></tr>";
	echo "</table>";
}

function pintarTitulo()
{
	global $wbasedato;
	echo "<form name='recibos_y_notas' action='recibosNotasN_1.php' method=post>";
	echo "<input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>";
	echo "<table border=1 ALIGN=CENTER width='1000'>";
	echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' height='61' width='300' ></td>";
	echo "<td align=center colspan=4   bgcolor=006699><font size=6 text color=#FFFFFF><b>RECIBOS DE CAJA Y NOTAS</b></font></td></tr>";
}

function pintarObservaciones($wobs, $cerrar, $wnrodoc, $wfuedoc, $wcco, $anular)
{
	global $wbasedato;
	echo "</br><center><table border=1 width='85%'>";
	echo "<tr>";
	echo "<td bgcolor=#57C8D5 align=center><b>OBSERVACION:</b></td>";
	echo "</tr>";

	if ($cerrar=='1')
	{
		echo "<tr>";
		echo "<td bgcolor='DDDDDD' align=center><b><textarea name='wobs' cols='80' rows='3'>".$wobs."</textarea></td>";
		echo "</tr>";
		echo "<tr><td align=center bgcolor=#dddddd ><font color=#000066 size=5><b>GRABAR</b><input type='checkbox' name='grabar' ></font></td></tr>";
		echo "<input type='HIDDEN' name= 'bandera' value='2'>";
		echo "<td align=center bgcolor=#cccccc ><input type='submit' value='OK'></td>";
	}
	else if ($cerrar=='2')
	{
		echo "<tr>";
		echo "<td bgcolor='DDDDDD' align=center><b><textarea name='wobs' cols='80' rows='3'>".$wobs."</textarea></td>";
		echo "</tr>";
		echo "<tr><td align=center bgcolor=#dddddd ><font color=#000066 size=5><b>GRABAR</b><input type='checkbox' name='grabar' ></font></td></tr>";
		echo "<input type='HIDDEN' name= 'bandera' value='5'>";
		echo "<td align=center bgcolor=#cccccc ><input type='submit' value='OK'></td>";
	}
	else
	{
		echo "<tr>";
		echo "<td bgcolor='DDDDDD' align=center><b>".$wobs."</td>";
		echo "</tr>";
		echo "<tr><td align=CENTER><Font size=4><b>DOCUMENTO GRABADO</b></font></td></tr>";
		echo "<td align=center colspan=10 bgcolor=#ffcc66><font color=#000066 size=4>Imprimir Documento: <A href='Imp_documento.php?wnrodoc=".strtoupper($wnrodoc)."&wfuedoc=".$wfuedoc."&wbasedato=".$wbasedato."&wcco=".$wcco."' target='_blank'>PARA PACIENTES</A>";
		echo "&nbsp;/<A href='Imp_documento.php?wnrodoc=".strtoupper($wnrodoc)."&wfuedoc=".$wfuedoc."&wbasedato=".$wbasedato."&wcco=".$wcco."&obser=1 ' target='_blank'> USO INTERNO </A></font></td>";
		if ($anular)
		{
			echo "<tr><td align=CENTER  bgcolor=#ffcc66><font color=#000066 size=4><A href='recibosNotasN_1.php?docu=".strtoupper($wnrodoc)."&fuen=".$wfuedoc."&wbasedato=".$wbasedato."&bandera=4'>Anular</A></font></td></tr>";
		}
		echo "<input type='hidden' NAME='bandera' value='1' />";
	}
	echo "</table></form>";
}

function pintarCostos($costos, $cerrar)
{
	echo "<table align='center' >";
	echo "<tr><td align=center><b>";
	echo "CENTRO DE COSTOS: &nbsp;&nbsp;&nbsp;&nbsp;</b>";
	echo "<input type='radio' name='wcco2' value='".$costos[0]."' onclick='enter9()' checked>".$costos[0]."&nbsp;&nbsp;&nbsp;&nbsp;";
	for ($i=1; $i<count($costos); $i++)
	{
		echo "<input type='radio' name='wcco2' value='".$costos[$i]."' onclick='enter9()'>".$costos[$i]."&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	echo "</b></td></tr></table></br>";

	if ($cerrar)
	{
		echo "<input type='HIDDEN' name= 'bandera' value='1'>";
		echo "</form>";
	}
}

function pintarParametros($fuentes, $wfuente, $wfcon, $wnrodoc, $wfecdoc, $wnomcco, $wnomcaj, $wempresas, $wcarrec, $wcarroc, $vol, $wnumEnv)
{
	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
	echo "<tr><td align=center  bgcolor='DDDDDD'><b><font text color='003366' size=3> Fuente: <br></font></b><select name='wfuente' onchange='enter2()'>";
	for ($i=0;$i<count($fuentes);$i++)
	{
		echo "<option>".$fuentes[$i]."</option>";
	}
	echo "</select>";

	//*******NUMERO FUENTE CONSULTA:
	if ($wfuente!='')
	echo "<b><font text color=".$wclfg."></font></b><INPUT TYPE='text' NAME='wfcon' VALUE='".$wfuente."' size='5' ></td>";
	else if ($wfcon!='')
	echo "<b><font text color=".$wclfg."></font></b><INPUT TYPE='text' NAME='wfcon' VALUE='".$wfcon."' size='5'></td>";
	else
	echo "<b><font text color=".$wclfg."></font></b><INPUT TYPE='text' NAME='wfcon' size='5' ></td>";

	//*******NUMERO DOCUMENTO:
	if ($wnrodoc!='')
	echo "<td align=center bgcolor=".$wcf."  ><b><font text color=".$wclfg.">Documento Nro: <br></font></b><INPUT TYPE='text' NAME='wnrodoc' VALUE='".$wnrodoc."' onchange='enter3()'></td>";
	else
	echo "<td align=center bgcolor=".$wcf."  ><b><font text color=".$wclfg.">Documento Nro: <br></font></b><INPUT TYPE='text' NAME='wnrodoc' onchange='enter3()'></td>";


	//*******FECHA DEL DOCUMENTO:
	echo "<td align=center bgcolor=".$wcf." ><b><font text color=".$wclfg.">Fecha: <br></font></b><INPUT TYPE='text' NAME='wfecdoc'  VALUE='".$wfecdoc."'></td>";


	//*******SUCURSAL:
	echo "<td align=center bgcolor=".$wcf."  ><b><font text color=".$wclfg.">Sucursal: <br></font></b>".$wnomcco."</td>";

	//*******CAJA:
	echo "<td align=center bgcolor=".$wcf."  colspan=1  ><b><font text color=".$wclfg.">Caja: <br></font></b>".$wnomcaj."</td></tr>";

	//responsables
	echo "<tr><td align=center bgcolor=".$wcf."  colspan=5 width='70%'><b><font text color=".$wclfg."> Responsable: <br></font></b><select name='wempresa' onchange='enter(1)'>";
	for ($i=1;$i<=count($wempresas);$i++)
	{
		$exp=explode('-', $wempresas[$i]);
		echo "<option value='".trim($exp[0])." - ".trim($exp[1])." - ".trim($exp[2])." - ".trim($exp[3])."'>".trim($exp[1])." - ".trim($exp[3])."</option>";
	}
	echo "</select><INPUT TYPE='text' NAME='wbuscador' VALUE=''></td></tr>";

	if (!$wcarroc and $wcarrec)
	{
		if (!isset($vol) or $vol=='manual')
		{
			echo "<tr><td bgcolor=".$wcf." colspan=2 align=center><input type='radio' name='vol' value='manual'checked onclick='enter(1)'> <font size='2'>MANUAL&nbsp;</td>";
			echo "<td bgcolor=".$wcf." align=center colspan=3 ><font text color=".$wclfg."><b>Envio:</b></font><INPUT TYPE='text' NAME='wnumEnv'  VALUE=''>";
			echo "<input type='radio' name='vol' value='auto'onclick='enter5()' > <font size='2'>AUTOMATICA&nbsp;";
		}else
		{
			echo "<tr><td bgcolor=".$wcf." colspan=2 align=center><input type='radio' name='vol' value='manual' onclick='enter(1)'> <font size='2'>MANUAL&nbsp;</td>";
			echo "<td bgcolor=".$wcf." align=center colspan=3 ><font text color=".$wclfg."><b>Envio:</b></font><INPUT TYPE='text' NAME='wnumEnv'  VALUE='".$wnumEnv."'>";
			echo "<input type='radio' name='vol' value='auto' checked onclick='enter5()' > <font size='2'>AUTOMATICA&nbsp;";
		}
		echo"</td></tr>";
	}

	echo "<INPUT TYPE='hidden' NAME='ingCau' VALUE='0'>"; //estos tres a continuacion manejan las causas de una nota
	echo "<INPUT TYPE='hidden' NAME='eliCau' VALUE='0'>";
	echo "<INPUT TYPE='hidden' NAME='index' VALUE='0'>";

}

function pintarBoton($bandera, $cerrar)
{
	echo "<tr>";
	if ($cerrar)
	{
		echo "<td align=center bgcolor=DDDDDD colspan=5 ><INPUT TYPE='submit' NAME='buscador' VALUE='OK'></td>";
		echo "</tr>";
		echo "</table></br></br>";
		echo "<input type='HIDDEN' name= 'bandera' value='".$bandera."'>";
		echo "</form>";
	}
	else
	{
		echo "<td align=center bgcolor=DDDDDD colspan=5 ><INPUT TYPE='button' NAME='nose' VALUE='OK' onclick='enter(1)'></td>";
		echo "</tr>";
		echo "</table></br></br>";
	}
}

function pintarCausas($causas, $lista, $tipo)
{
	echo "</table><br/>";
	echo "<table align=center width='85%' border=1>";
	echo "<tr>";
	echo "<td bgcolor=#57C8D5 colspan=10 align=center><b>CAUSAS</b></td>";
	echo "</tr>";

	if ($causas)
	{
		echo "<tr>";
		echo "<td bgcolor=DDDDDD colspan=10 align=center><select name='cauSel' onchange='agregarCausa()'>";
		echo "<option selected></option>";

		for ($i=1;$i<=count($causas);$i++)
		{
			if (!$lista)
			{
				echo "<option>".$causas[$i]."</option>";
			}else
			{
				$pinto=1;
				for ($j=0;$j<count($lista);$j++)
				{
					if ($lista[$j]==$causas[$i])
					{
						$pinto=0;
					}
				}
				if ($pinto==1)
				echo "<option>".$causas[$i]."</option>";
			}
		}
		echo "</select></td></tr>";
	}

	//pinto vector de causas
	if ($lista)
	{
		$wcf="#c0c0c0";
		for ($j=0;$j<count($lista);$j++)
		{
			if ($wcf=="DDDDDD")
			$wcf="#c0c0c0";
			else
			$wcf="DDDDDD";

			echo "<input type='HIDDEN' name='lista[".$j."]' value='".$lista[$j]."'>";
			if ($tipo)
			{
				echo "<tr><td bgcolor=".$wcf." colspan=10 align=center><A href='#' onclick='eliminarCausa(".$j.")'>Eliminar</A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

			}
			else
			{
				echo "<tr><td bgcolor=".$wcf." colspan=10 align=center>";
			}
			echo $lista[$j]."</td></tr>";
		}
	}
	echo "</table>";
}

function pintarAlert3($mensaje)
{
	echo '<script language="Javascript">';
	echo 'alert ("'.$mensaje.'")';
	echo '</script>';
}


function pintarAlert4($mensaje)
{
	echo "<CENTER><H1><FONT COLOR='#006699'>ANULADO</FONT></H1></CENTER>";
}

function pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $canfac, $conceptos, $pidecentro, $centro, $cancon, $nuevalinea, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, $canbru)
{
	global $wbasedato;
	global $conex;


	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro


	if ($wcarrec and !$wcarroc) //recibos tradicionales
	{
		if ($wcarcca)
		{
			$colspan='11';
		}
		else
		{
			$colspan='7';
		}
	}
	else if ($wcarroc)//recibos por otros conceptos y que no tienen valor a cancelar
	{
		$colspan='4';
	}
	else if (!$wcarrec)
	{
		if ($wcarndb)
		{
			$colspan='9';
		}
		else
		{
			$colspan='10';
		}
	}

	echo "<center><table border=1  width='85%'>";

	echo "<tr>";
	echo "<td bgcolor=#57C8D5 colspan=".$colspan." align=center><b>DETALLE DE FACTURAS Y CONCEPTOS DE CARTERA</b></td>";
	echo "</tr>";

	if ($wcarrec and !$wcarroc) //recibos tradicionales
	{
		echo "<th bgcolor=#A4E1E8 colspan=5>Factura</th>";
		if ($wcarcca)
		{
			echo "<th bgcolor=#A4E1E8 colspan=2>Recibo</th>";
			echo "<th bgcolor=#A4E1E8 colspan=3>Conceptos de cartera</th>";
		}
		else
		{
			echo "<th bgcolor=#A4E1E8>Recibo</th>";
		}
		echo "<th bgcolor=#A4E1E8 >&nbsp;</th>";
		echo "</tr>";
		echo "<th bgcolor=#A4E1E8 size=3% >Fuente</th>";
		echo "<th bgcolor=#A4E1E8 size=3% >Prefijo</th>";
		echo "<th bgcolor=#A4E1E8 size=3% >Numero</th>";
		echo "<th bgcolor=#A4E1E8>Valor</th>";
		echo "<th bgcolor=#A4E1E8>Saldo</th>";
	}
	else if ($wcarroc)//recibos por otros conceptos y que no tienen valor a cancelar
	{
		echo "<th bgcolor=#A4E1E8 colspan=3>Recibos por otros conceptos</th>";
		echo "<th bgcolor=#A4E1E8 >&nbsp;</th>";
		echo "</tr>";
	}
	else if (!$wcarrec)
	{
		echo "<th bgcolor=#A4E1E8 colspan=5>Factura</th>";
		if ($wcarndb)
		{
			echo "<th bgcolor=#A4E1E8 colspan=3>Nota</th>";
		}
		else
		{
			echo "<th bgcolor=#A4E1E8 colspan=4>Nota</th>";
		}
		echo "<th bgcolor=#A4E1E8 >&nbsp;</th>";
		echo "</tr>";

		echo "<th bgcolor=#A4E1E8 size=3% >Fuente</th>";
		echo "<th bgcolor=#A4E1E8 size=3% >Prefijo</th>";
		echo "<th bgcolor=#A4E1E8 size=3% >Numero</th>";
		echo "<th bgcolor=#A4E1E8>Valor</th>";
		echo "<th bgcolor=#A4E1E8>Saldo</th>";
	}

	if ($wcarrec and !$wcarroc) //recibos tradicionales
	{
		if ($wcarcca)
		{
			echo "<th bgcolor=#A4E1E8>Valor a Cancelar</th>";
		}
		echo "<th bgcolor=#A4E1E8>Valor Neto</th>";
	}
	if ($wcarcca)
	{
		if ($wcarncr)
		{
			echo "<th bgcolor=#A4E1E8 align=center>Glosas</th>";
		}

		echo "<th bgcolor=#A4E1E8 align=center>Concepto</th>";
		echo "<th bgcolor=#A4E1E8 align=center>Valor</th>";
		echo "<th bgcolor=#A4E1E8 align=center>Centro de Costos</th>";
	}
	echo "<th bgcolor=#A4E1E8 >&nbsp;</th>";

	if ($nuevalinea==1)
	{
		//se imprime la nueva linea con los datos enviados
		echo "<tr>";
		if (($wcarrec and !$wcarroc) or !$wcarrec) //recibos tradicionales y notas
		{
			echo "<td align='center' bgcolor=".$wcf." ><INPUT TYPE='text' NAME='fuefac' size=5   value='".$fuefac."' ></td>";
			if ($prefac!='')
			{
				echo "<td align='center' bgcolor=".$wcf." ><INPUT TYPE='text' NAME='prefac' size=5   value='".$prefac."' ></td>";
			}
			else
			{
				echo "<td align='center' bgcolor=".$wcf." ><INPUT TYPE='text' NAME='prefac' size=5   value='' ></td>";
			}

			if ($numfac!='')
			{
				echo "<td align='center' bgcolor=".$wcf." size='5'><INPUT TYPE='text' NAME='numfac' VALUE='".$numfac."' size=5 onchange='enter(2)'></td>";
			}
			else
			{
				echo "<td align='center' bgcolor=".$wcf." size='5'><INPUT TYPE='text' NAME='numfac' VALUE='' size=5 onchange='enter(2)'></td>";
			}
			echo "<td align=right bgcolor=".$wcf."> ".number_format($wvalfac,0,'.',',')." </td>";
			echo "<input type='HIDDEN' name='wvalfac' value='".$wvalfac."'>";
			echo "<td align=right bgcolor=".$wcf."> ".number_format($wsalfac,0,'.',',')." </td>";
			echo "<input type='HIDDEN' name='wsalfac' value='".$wsalfac."'>";
			if ($wcarrec)
			{
				if ($wcarcca)
				{
					echo "<td align=center bgcolor=".$wcf."><INPUT TYPE='text' NAME='canbru' VALUE='".$canbru."'  onchange='enter7(2)' size='10'></td>";
					echo "<td align=center bgcolor=".$wcf."><INPUT TYPE='text' NAME='canfac' VALUE='".$canfac."'  size='10' readonly='readonly'></td>";
				}
				else
				{
					echo "<td align=center bgcolor=".$wcf."><INPUT TYPE='text' NAME='canfac' VALUE='".$canfac."' size='10'></td>";
				}
			}

		}
		if ($wcarcca)
		{
			if ($wcarncr)
			{
				if ($glosas)
				{
					echo "<td align=center bgcolor=".$wcf." colspan><select name='glosa' >";
					for ($i=0;$i<count($glosas);$i++)
					{
						$row = mysql_fetch_array($res);
						echo "<option>".$glosas[$i]."</option>";
					}
					echo "</select></td>";
				}
				else
				{
					echo "<td align=right bgcolor=".$wcf."> &nbsp</td>";
					echo "<input type='HIDDEN' name='glosa' value=''>";
				}
			}
			else if ($wcarndb)
			{
				echo "<input type='HIDDEN' name='glosa' value=''>";
			}

			if ($conceptos)
			{
				echo "<td align=center bgcolor=".$wcf." colspan><select name='nomcon' onchange='enter8(2)' >";
				for ($i=0;$i<count($conceptos);$i++)
				{
					$row = mysql_fetch_array($res);
					echo "<option>".$conceptos[$i]."</option>";
				}
				echo "</select></td>";

				if ($wcarrec and $wcarcca)
				{

					echo "<td align=center bgcolor=".$wcf."><INPUT TYPE='text' NAME='cancon'  VALUE='".$cancon."'  onchange='enter6(2)' size='10'></td>";

					$exp=explode('-', $conceptos[0]);

					$q =  " SELECT conrfe "
					."   FROM ".$wbasedato."_000044, ".$wbasedato."_000040 "
					."  WHERE concod='".$exp[0]."' and "
					."   conest = 'on' "
					."   AND confue = carfue "
					."   AND carrec = 'on' "
					."   AND carcca = 'on' "
					."   AND carest = 'on' "
					."   AND carroc != 'on' "
					."   AND carabo != 'on' ";

					$res1 = mysql_query($q,$conex);
					$num1 = mysql_num_rows($res1);
					$row1 = mysql_fetch_array($res1);

					if (($row1[0]>0 and $cancon=='') or $exp[0]=='')
					{

						echo "<input type='HIDDEN' name='indicador' value='1'>";
					}
					else
					{

						echo "<input type='HIDDEN' name='indicador' value='2'>";
					}
				}
				else
				{
					echo "<input type='HIDDEN' name='indicador' value='1'>";
					echo "<td align=center bgcolor=".$wcf."><INPUT TYPE='text' NAME='cancon'  VALUE='".$cancon."' size='10'></td>";
				}
				if ($pidecentro=='1')
				{
					echo "<td align=center bgcolor=".$wcf."><INPUT TYPE='text' NAME='centro' VALUE='".$centro."' size='5'></td>";
					echo '<script language="Javascript">';
					echo 'hacerFoco(2)';
					echo '</script>';
				}
				else
				{
					echo "<td align=right bgcolor=".$wcf."> &nbsp</td>";
				}
			}
			else
			{
				if ($wcarrec and $wcarcca)
				{

					echo "<input type='HIDDEN' name='indicador' value='1'>";
				}
				echo "<td align=right bgcolor=".$wcf."> &nbsp</td>";
				echo "<td align=right bgcolor=".$wcf."> &nbsp</td>";
				echo "<td align=right bgcolor=".$wcf."> &nbsp</td>";
			}
			echo "<td align=right bgcolor=".$wcf."> &nbsp</td>";
			echo "</tr>";
		}
		else
		{
			echo "<td align=left bgcolor=".$wcf.">&nbsp;</td>";
			echo "</tr>";
		}
	}

	if ($temporal)
	{
		if ($temporal[0]['temfco']=='off')
		{
			echo "<tr><td bgcolor=#A4E1E8 colspan='".$colspan."'><hr></td></tr>";
			for ($i=0; $i<count($temporal); $i++)
			{
				echo "<tr>";
				if (($wcarrec and !$wcarroc) or !$wcarrec) //recibos tradicionales
				{
					echo "<td align=center bgcolor=".$wcf." > ".$temporal[$i]['temffa']." </td>";
					if($temporal[$i]['temnfa']!='')
					{
						$exp=explode('-', $temporal[$i]['temnfa']);
						echo "<td align=center bgcolor=".$wcf." > ".trim($exp[0])." </td>";
						echo "<td align=center bgcolor=".$wcf." > ".trim($exp[1])." </td>";
					}
					else
					{
						echo "<td align=center bgcolor=".$wcf." >&nbsp; </td>";
						echo "<td align=center bgcolor=".$wcf." > &nbsp;</td>";
					}
					echo "<td align=right bgcolor=".$wcf."> ".number_format($temporal[$i]['temvfa'],0,'.',',')." </td>";
					echo "<td align=right bgcolor=".$wcf."> ".number_format($temporal[$i]['temsfa'],0,'.',',')." </td>";
					if ($wcarrec)
					{
						if ($wcarcca)
						{
							echo "<td align=right bgcolor=".$wcf.">&nbsp;</td>";
						}
						echo "<td align=right bgcolor=".$wcf."> ".number_format($temporal[$i]['temvcf'],0,'.',',')." </td>";
					}
				}
				if ($wcarcca)
				{
					if ($wcarncr)
					{
						if ($temporal[$i]['temglo']!='')
						{
							echo "<td align=left bgcolor=".$wcf." > ".$temporal[$i]['temglo']." </td>";
						}
						else
						{
							echo "<td align=left bgcolor=".$wcf." > &nbsp;</td>";
						}
					}

					if ($temporal[$i]['temcon']!='')
					{
						echo "<td align=left bgcolor=".$wcf." > ".$temporal[$i]['temcon']." </td>";
					}
					else
					{
						echo "<td align=left bgcolor=".$wcf." > &nbsp;</td>";
					}
					if ($temporal[$i]['temdco']=='1' and !$wcarndb)
					{
						echo "<td align=right bgcolor=".$wcf."> ".number_format(-$temporal[$i]['temvco'],0,'.',',')." </td>";
					}
					else
					{
						echo "<td align=right bgcolor=".$wcf."> ".number_format($temporal[$i]['temvco'],0,'.',',')." </td>";
					}
					if ($temporal[$i]['temccc']!='')
					{
						echo "<td align=center bgcolor=".$wcf." > ".$temporal[$i]['temccc']." </td>";
					}
					else
					{
						echo "<td align=center bgcolor=".$wcf." >&nbsp;</td>";
					}
				}
				if ($nuevalinea=='1')
				{
					echo "<td align=center bgcolor=".$wcf."><A href='RecibosNotasN_1.php?wid=".$temporal[$i]['id']."&bandera=1"."&wfuente=".$wfuente."&wnrodoc=".$wnrodoc."&wfecdoc=".$wfecdoc."&wnomcco=".$wnomcco."&wcco=".$wcco."&wempresa=".$wempresa."&wbuscador=".$wbuscador."&wemp_pmla=".$wemp_pmla."'> Eliminar</A></td>";
				}
				else
				{
					echo "<td align=center bgcolor=".$wcf." >&nbsp;</td>";
				}
				echo "</tr>";
			}

			//imprimo los totales del documento
			if ($wcarrec and !$wcarroc) //recibos tradicionales
			{
				if ($wcarcca)
				{
					echo "<td align=right bgcolor='#ffcc66' colspan=".($colspan-5)."  >TOTAL</td>";
					echo "<td align=right bgcolor='#ffcc66'> ".number_format($wtotvcafac,0,'.',',')." </td>";
					echo "<td align=right bgcolor='#ffcc66'> &nbsp</td>";
					echo "<td align=right bgcolor='#ffcc66'> ".	number_format($wtotvalcon,0,'.',',')." </td>";
					echo "<td align=right bgcolor='#ffcc66'> &nbsp</td>";
				}
				else
				{
					echo "<td align=right bgcolor='#ffcc66' colspan=".($colspan-2)."  >TOTAL</td>";
					echo "<td align=right bgcolor='#ffcc66'> ".number_format($wtotvcafac,0,'.',',')." </td>";
				}
				echo "<td align=right bgcolor='#ffcc66'> ".	number_format($total,0,'.',',')." </td>";
			}
			else if ($wcarroc)//recibos por otros conceptos y que no tienen valor a cancelar
			{
				echo "<td align=right bgcolor='#ffcc66' colspan=".($colspan-3)." >TOTAL</td>";
				echo "<td align=right bgcolor='#ffcc66'> ".	number_format($wtotvalcon,0,'.',',')." </td>";
				echo "<td align=right bgcolor='#ffcc66'> &nbsp</td>";
				echo "<td align=right bgcolor='#ffcc66'> ".	number_format($total,0,'.',',')." </td>";
			}
			else if (!$wcarrec)
			{
				echo "<td align=right bgcolor='#ffcc66' colspan=".($colspan-3)." >TOTAL</td>";
				echo "<td align=right bgcolor='#ffcc66'> ".	number_format($wtotvalcon,0,'.',',')." </td>";
				echo "<td align=right bgcolor='#ffcc66'> &nbsp</td>";
				echo "<td align=right bgcolor='#ffcc66'> ".	number_format($total,0,'.',',')." </td>";
			}
		}
	}
	echo "</table>";
}

function pintarAutomatico($temporal, $wcarcca, $wtotvcafac, $wtotvalcon, $total, $conceptos)
{
	global $wbasedato;

	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

	if ($wcarcca)
	{
		$colspan=8;
	}
	else
	{
		$colspan=5;
	}
	echo "<center><table border=1  width='85%'>";

	echo "<tr>";
	echo "<td bgcolor=#57C8D5 colspan=".$colspan." align=center><b>DETALLE DE FACTURAS Y CONCEPTOS DE CARTERA</b></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<th bgcolor=#A4E1E8 colspan=3>Factura</th>";
	echo "<th bgcolor=#A4E1E8>Recibo</th>";
	if ($wcarcca)
	{
		echo "<th bgcolor=#A4E1E8 colspan=3>Conceptos de cartera</th>";
	}
	echo "<th bgcolor=#A4E1E8 >&nbsp;</th>";
	echo "</tr>";
	echo "<th bgcolor=#A4E1E8 size=3% >Factura</th>";
	echo "<th bgcolor=#A4E1E8>Valor</th>";
	echo "<th bgcolor=#A4E1E8>Saldo</th>";
	echo "<th bgcolor=#A4E1E8>Valor a Cancelar</th>";
	if ($wcarcca)
	{
		echo "<th bgcolor=#A4E1E8 align=center>Concepto</th>";
		echo "<th bgcolor=#A4E1E8 align=center>Valor</th>";
		echo "<th bgcolor=#A4E1E8 align=center>Centro de Costos</th>";
	}
	echo "<th bgcolor=#A4E1E8 >Seleccionar</th>";
	echo "</tr>";

	for ($i=0; $i<count($temporal); $i++)
	{
		echo "<tr>";
		echo "<td align=center bgcolor=".$wcf." > ".$temporal[$i]['temffa']."-".$temporal[$i]['temnfa']." </td>";
		echo "<input type='HIDDEN' name='temporal[".$i."][temffa]' value='".$temporal[$i]['temffa']."'>";
		echo "<input type='HIDDEN' name='temporal[".$i."][temnfa]' value='".$temporal[$i]['temnfa']."'>";
		echo "<td align=right bgcolor=".$wcf."> ".number_format($temporal[$i]['temvfa'],0,'.',',')." </td>";
		echo "<td align=right bgcolor=".$wcf."> ".number_format($temporal[$i]['temsfa'],0,'.',',')." </td>";
		echo "<input type='HIDDEN' name='temporal[".$i."][temvfa]' value='".$temporal[$i]['temvfa']."'>";
		echo "<input type='HIDDEN' name='temporal[".$i."][temsfa]' value='".$temporal[$i]['temsfa']."'>";
		echo "<input type='HIDDEN' name='temporal[".$i."][temdco]' value='".$temporal[$i]['temdco']."'>";
		echo "<input type='HIDDEN' name='temporal[".$i."][temfco]' value='".$temporal[$i]['temfco']."'>";
		echo "<input type='HIDDEN' name='temporal[".$i."][temenv]' value='".$temporal[$i]['temenv']."'>";

		echo "<td align=center bgcolor=".$wcf."><INPUT TYPE='text' NAME='temporal[".$i."][temvcf]' VALUE='".$temporal[$i]['temvcf']."' ></td>";

		if ($wcarcca)
		{
			echo "<td align=center bgcolor=".$wcf." colspan><select name='temporal[".$i."][temcon]'  >";
			if ($temporal[$i]['temcon']!='')
			{
				echo "<option selected>".$temporal[$i]['temcon']."</option>";
			}

			for ($j=0;$j<count($conceptos);$j++)
			{
				$row = mysql_fetch_array($res);
				echo "<option>".$conceptos[$j]."</option>";
			}
			echo "</select></td>";

			echo "<td align=center bgcolor=".$wcf."><INPUT TYPE='text' NAME='temporal[".$i."][temvco]'  VALUE='".$temporal[$i]['temvco']."' size='10'></td>";

			if ($temporal[$i]['temccc']!='off')
			{
				echo "<td align=center bgcolor=".$wcf."><INPUT TYPE='text' NAME='temporal[".$i."][temccc]' VALUE='".$temporal[$i]['temccc']."' size='5'></td>";
			}
			else
			{
				echo "<td align=right bgcolor=".$wcf."> &nbsp</td>";
				echo "<input type='HIDDEN' name='temporal[".$i."][temccc]' value='".$temporal[$i]['temccc']."'>";
			}

		}
		else
		{
			echo "<input type='HIDDEN' name='temporal[".$i."][temvco]' value=''>";
			echo "<input type='HIDDEN' name='temporal[".$i."][temcon]' value=''>";
		}
		echo "<td bgcolor=".$wcf." align ='center'><input type='checkbox' name='temporal[".$i."][chk]' ".$temporal[$i]['chk']." ></td>";
		echo "</tr>";
	}

	//imprimo los totales del documento
	echo "<td align=right bgcolor='#ffcc66' colspan=3  >TOTAL</td>";
	if ($wcarcca)
	{
		echo "<td align=right bgcolor='#ffcc66'> ".number_format($wtotvcafac,0,'.',',')." </td>";
		echo "<td align=right bgcolor='#ffcc66'> &nbsp</td>";
		echo "<td align=right bgcolor='#ffcc66'> ".	number_format($wtotvalcon,0,'.',',')." </td>";
		echo "<td align=right bgcolor='#ffcc66'> &nbsp</td>";
	}
	else
	{
		echo "<td align=right bgcolor='#ffcc66'> ".number_format($wtotvcafac,0,'.',',')." </td>";
	}
	echo "<td align=right bgcolor='#ffcc66'> ".	number_format($total,0,'.',',')." </td>";

	echo "</table>";
}

function pintarFacturacion($temporal, $wcarndb, $wcarncr, $wtotvalcon, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $salcon, $cargos, $valini, $valmax, $valcon, $nuevalinea, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $glosas, $ref)
{
	global $wbasedato;
	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

	echo "<center><table border=1 width='85%'>";

	echo "<tr>";

	if (!$wcarcca)
	{
		echo "<td bgcolor=#57C8D5 colspan=10 align=center><b>DETALLE DE FACTURA </b></td>";
		echo '</tr>';
		echo '<tr>';
		echo "<th bgcolor=#A4E1E8 size=3% >Fuente</th>";
		echo "<th bgcolor=#A4E1E8 size=3% >Prefijo</th>";
		echo "<th bgcolor=#A4E1E8 size=3% >Numero</th>";
		echo "<th bgcolor=#A4E1E8 >Valor</th>";
		echo "<th bgcolor=#A4E1E8 >Saldo</th>";
		if ($wcarncr)
		{
			echo "<th bgcolor=#A4E1E8 >Glosas</th>";
			echo "<th bgcolor=#A4E1E8 >Refacturacion</th>";
		}
		else
		{
			echo "<th bgcolor=#A4E1E8 >&nbsp;</th>";
			echo "<th bgcolor=#A4E1E8 >&nbsp;</th>";
		}
		echo "<th bgcolor=#A4E1E8 >&nbsp;</th>";
		echo "<th bgcolor=#A4E1E8 >&nbsp;</th>";
		echo "<th bgcolor=#A4E1E8 >&nbsp;</th></tr>";

		if ($temporal)
		{
			echo "<tr>";
			echo "<td align=left bgcolor=".$wcf." > ".$temporal[0]['temffa']." </td>";
			$exp=explode('-', $temporal[0]['temnfa']);
			echo "<td align=left bgcolor=".$wcf." > ".trim($exp[0])." </td>";
			echo "<td align=left bgcolor=".$wcf." > ".trim($exp[1])." </td>";
			echo "<td align=right bgcolor=".$wcf."> ".number_format($temporal[0]['temvfa'],0,'.',',')." </td>";
			echo "<td align=right bgcolor=".$wcf."> ".number_format($temporal[0]['temsfa'],0,'.',',')." </td>";
			//echo 'hola';
			if ($nuevalinea=='1' and $wcarncr )
			{
				if ($glosas)
				{
					echo "<td align=center bgcolor=".$wcf."><select name='glosa'>";
					for ($i=0;$i<count($glosas);$i++)
					{
						echo "<option>".$glosas[$i]."</option>";
					}
					echo "</select></td>";
					if($ref=='')
					{
						echo "<td align=center bgcolor=".$wcf." ><input type='checkbox' name='ref' value='on' ></td>";
					}
					else
					{
						echo "<td align=center bgcolor=".$wcf." ><input type='checkbox' checked name='ref' value='on' ></td>";
					}
				}
				else
				{
					echo "<td align=right bgcolor=".$wcf." > &nbsp</td>";
					echo "<input type='HIDDEN' name='glosa' value=''>";
					if($ref=='')
					{
						echo "<td align=center bgcolor=".$wcf." ><input type='checkbox' name='ref' value='on'></td>";
					}
					else
					{
						echo "<td align=center bgcolor=".$wcf." ><input type='checkbox' checked name='ref' value='on'></td>";
					}
				}
			}
			else if ($wcarncr)
			{

				echo "<td align=right bgcolor=".$wcf." > ".$glosas."</td>";
				if($ref=='')
				{
					echo "<td align=center bgcolor=".$wcf." ><input type='checkbox' name='ref' value='on'></td>";
				}
				else
				{
					echo "<td align=center bgcolor=".$wcf." ><input type='checkbox' checked name='ref' value='on'></td>";
				}
			}

			if (!$wcarncr)
			{
				echo "<td align=center bgcolor=".$wcf." > &nbsp</td>";
				echo "<input type='HIDDEN' name='glosa' value=''>";
				echo "<th bgcolor=".$wcf.">&nbsp;</th>";
			}
			echo "<th bgcolor=".$wcf." >&nbsp;</th>";
			echo "<th bgcolor=".$wcf.">&nbsp;</th>";
			echo "<th bgcolor=".$wcf." >&nbsp;</th>";
			echo "</tr>";
			echo "<input type='HIDDEN' name='fuefac' value='".$temporal[0]['temffa']."'>";
			echo "<input type='HIDDEN' name='prefac' value='".trim($exp[0])."'>";
			echo "<input type='HIDDEN' name='numfac' value='".trim($exp[1])."'>";
			echo "<input type='HIDDEN' name='wvalfac' value='".$temporal[0]['temvfa']."'>";
			echo "<input type='HIDDEN' name='wsalfac' value='".$temporal[0]['temsfa']."'>";
		}
		else
		{
			echo "<td align='center' bgcolor=".$wcf." ><INPUT TYPE='text' NAME='fuefac' size=5   value='".$fuefac."' ></td>";
			if ($numfac!='')
			{
				$exp=explode('-', $numfac);
				echo "<td align='center' bgcolor=".$wcf." ><INPUT TYPE='text' NAME='prefac' size=5   value='".$prefac."' ></td>";
				echo "<td align='center' bgcolor=".$wcf." size='5'><INPUT TYPE='text' NAME='numfac' VALUE='".$numfac."' size=5 onchange='enter(2)'></td>";
			}
			else
			{
				echo "<td align='center' bgcolor=".$wcf." ><INPUT TYPE='text' NAME='prefac' size=5   value='' ></td>";
				echo "<td align='center' bgcolor=".$wcf." size='5'><INPUT TYPE='text' NAME='numfac' VALUE='' size=5 onchange='enter(2)'></td>";
			}
			echo "<td align=right bgcolor=".$wcf."> ".number_format($wvalfac,0,'.',',')." </td>";
			echo "<input type='HIDDEN' name='wvalfac' value='".$wvalfac."'>";
			echo "<td align=right bgcolor=".$wcf."> ".number_format($wsalfac,0,'.',',')." </td>";
			echo "<input type='HIDDEN' name='wsalfac' value='".$wsalfac."'>";
			if ($glosas and $wcarncr)
			{
				echo "<td align=center bgcolor=".$wcf." ><select name='glosa'>";
				for ($i=0;$i<count($glosas);$i++)
				{
					echo "<option>".$glosas[$i]."</option>";
				}
				echo "</select></td>";
			}
			else if ($wcarncr)
			{
				echo "<td align=right bgcolor=".$wcf." > &nbsp</td>";
				if (isset ($glosa))
				{
					echo "<input type='HIDDEN' name='glosa' value='".$glosa."'>";
				}
				else
				{
					echo "<input type='HIDDEN' name='glosa' value=''>";
				}
			}
			if (!$wcarncr)
			{
				echo "<td align=right bgcolor=".$wcf." > &nbsp</td>";
				echo "<input type='HIDDEN' name='glosa' value=''>";
				echo "<th bgcolor=".$wcf.">&nbsp;</th>";
			}

			if($wcarncr and $numfac!='')
			{
				if($ref=='')
				{
					echo "<td align=center bgcolor=".$wcf." ><input type='checkbox' name='ref' value='on' onclick='enter(2)'></td>";
				}
				else
				{
					echo "<td align=center bgcolor=".$wcf." ><input type='checkbox' checked name='ref' value='on' onclick='enter(2)'></td>";
				}
			}
			else
			{
				echo "<th bgcolor=".$wcf.">&nbsp;</th>";
			}
			echo "<th bgcolor=".$wcf.">&nbsp;</th>";
			echo "<th bgcolor=".$wcf.">&nbsp;</th>";
			echo "<th bgcolor=".$wcf.">&nbsp;</th></tr>";
		}
	}

	echo "<tr>";
	echo "<td bgcolor=#57C8D5 colspan=10 align=center><b>DETALLE DE CONCEPTOS DE FACTURA</b></td>";
	echo "</tr>";

	echo "<th bgcolor=#A4E1E8 colspan=5 >Concepto</th>";
	echo "<th bgcolor=#A4E1E8 colspan=1 >Valor inicial</th>";
	echo "<th bgcolor=#A4E1E8 colspan=1 >Valor Maximo</th>";
	echo "<th bgcolor=#A4E1E8  colspan=1 >Saldo</th>";
	echo "<th bgcolor=#A4E1E8 colspan=1 >Valor Nota</th>";
	echo "<th bgcolor=#A4E1E8  colspan=1 >&nbsp;</th>";

	//se imprime la nueva linea con los datos enviados
	echo "<tr>";

	if ($nuevalinea=='1')
	{

		if ($cargos)
		{
			echo "<td align=center bgcolor=".$wcf." colspan=1 ><INPUT TYPE='text' name='codcar' size=5 onchange='enter(2)' ></td>";
			echo "<td align=center bgcolor=".$wcf." colspan='4'><select name='cargo' onchange='enter(2)' >";
			for ($i=1;$i<=count($cargos);$i++)
			{
				echo "<option>".$cargos[$i]."</option>";
			}
			echo "</select></td>";
		}
		else
		{
			echo "<td align=right bgcolor=".$wcf." colspan='4'> &nbsp</td>";
			echo "<td align=right bgcolor=".$wcf." > &nbsp</td>";
		}

		if ($cargos)
		{
			echo "<td align=center bgcolor=".$wcf.">".$valini."</td>";
			echo "<input type='HIDDEN' name='valini' value='".$valini."'>";
			echo "<td align=center bgcolor=".$wcf.">".$valmax."</td>";
			echo "<input type='hidden' name='valmax' value='".$valmax."'>";
			echo "<td align=center bgcolor=".$wcf.">".$salcon."</td>";
			echo "<input type='HIDDEN' name='salcon' value='".$salcon."'>";
			echo "<td align=center bgcolor=".$wcf."><INPUT TYPE='text' NAME='valcon'  VALUE='".$valcon."' size='10'></td>";
		}
		else
		{
			echo "<td align=center bgcolor=".$wcf.">&nbsp</td>";
			echo "<td align=center bgcolor=".$wcf.">&nbsp</td>";
			echo "<td align=center bgcolor=".$wcf.">&nbsp</td>";
			echo "<td align=center bgcolor=".$wcf.">&nbsp</td>";
			echo "<input type='hidden' name='valmax' value=''>";
		}

		echo "<td align=right bgcolor=".$wcf."> &nbsp;</td>";
		echo "</tr>";
	}


	if ($temporal)
	{
		echo "<tr><td bgcolor=#57C8D5 colspan=10 align=center><b><hr></b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<th bgcolor=#A4E1E8 colspan='5'>Concepto</th>";
		echo "<th bgcolor=#A4E1E8 >Centro de costos</th>";
		echo "<th bgcolor=#A4E1E8 colspan=2>Tercero</th>";
		echo "<th bgcolor=#A4E1E8>Valor Cpto</th>";
		echo "<th bgcolor=#A4E1E8>&nbsp</th>";

		for ($i=0; $i<count($temporal); $i++)
		{

			echo "<tr>";
			echo "<td align=center bgcolor=".$wcf." colspan=5 > ".$temporal[$i]['temcon']."</td>";
			echo "<td align=center bgcolor=".$wcf." colspan=1 > ".$temporal[$i]['temcco']." </td>";
			if ($temporal[$i]['temter']!='')
			{
				echo "<td align=center bgcolor=".$wcf." colspan=2> ".$temporal[$i]['temter']." </td>";
			}
			else
			{
				echo "<td align=center bgcolor=".$wcf." colspan=2>&nbsp;</td>";
			}
			echo "<td align=right bgcolor=".$wcf."> ".number_format($temporal[$i]['temvco'],0,'.',',')." </td>";
			if ($nuevalinea=='1')
			echo "<td align=center bgcolor=".$wcf."><A href='RecibosNotasN_1.php?wid=".$temporal[$i]['id']."&amp;bandera=1"."&amp;wfuente=".$wfuente."&amp;wnrodoc=".$wnrodoc."&amp;wfecdoc=".$wfecdoc."&amp;wnomcco=".$wnomcco."&amp;wcco=".$wcco."&amp;wempresa=".$wempresa."&amp;wbuscador=".$wbuscador."&amp;wemp_pmla=".$wemp_pmla."&amp;ref=".$ref."'>Eliminar</A></td>";
			else
			echo "<td align=center bgcolor=".$wcf." >&nbsp;</td>";
			echo "</tr>";
		}

		echo "<tr>";
		echo "<td align=left bgcolor='#ffcc66' colspan=8 >TOTAL</td>";
		echo "<td align=right bgcolor='#ffcc66'> ".number_format($wtotvalcon,0,'.',',')." </td>";
		echo "<td align=right bgcolor='#ffcc66'> &nbsp</td>";
		echo "</tr>";
	}


	echo "</table>";

}

function pintarPagos($fk, $wfpa, $origenes, $destinos, $pago, $anexo, $origen, $destion, $ubicacion, $autorizacion, $obliga, $saldo, $wobs, $nuevalinea)
{
	global $wbasedato;
	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

	echo '</table>';
	echo "<table width='85%'>";

	if ($fk)
	{
		echo "<tr><td bgcolor='#57C8D5' colspan='5' align=center><b>DETALLE DE FORMAS DE PAGO</b></td></tr>";
		for ($j=1;$j<=count($fk);$j++)
		{
			echo "<tr>";
			//FORMA DE PAGO
			echo "<td align=left bgcolor='DDDDDD'><b>Forma de pago:</b></br>".$fk[$j][0]."</td>";

			//DOCUMENTO ANEXO
			echo "<td align=left bgcolor='DDDDDD'><b>Dcto Anexo:</b><br>".$fk[$j][1]."</td>";

			if ($fk[$j][8])
			{
				//Datos del banco de origen
				echo "<td align=left bgcolor='DDDDDD'><b>Datos del Banco de Origen:</b></br> ".$fk[$j][2]."</td>";

				//Si es plaza o local
				echo "<td bgcolor='DDDDDD'><b>Ubicacion:</b></br>".$fk[$j][3]."</td>";

				//numero de autorizacion
				echo "<td bgcolor='DDDDDD' ><b>Nº autorizacion:</b></br> ".$fk[$j][4]."</td>";
			}
			else
			{
				//observaciones
				echo "<td bgcolor='DDDDDD'><b>Observacion:<b></br>".$fk[$j][2]."</td>";
				echo "<td bgcolor='DDDDDD'>&nbsp;</td>";
				echo "<td bgcolor='DDDDDD'>&nbsp;</td>";
			}

			echo '</tr>';
			echo '<tr>';
			/////////////////////////////////BANCO DESTINO//////////////////

			echo "<td bgcolor='#c0c0c0' colspan=2><b>Datos del Banco de Destino:</b>".$fk[$j][5]."</td >";
			echo "<td bgcolor='#c0c0c0' align='center'><b>Valor:</b> ".number_format($fk[$j][6],2,'.',',')."</td>";       //wvalfpa
			echo "<td bgcolor='#ffcc66' align='center' colspan=2><b>Saldo:</b> ".number_format($fk[$j][7],2,'.',',')."</td>";            //wtotventot-wtotfpa
			echo "</tr>";

			for ($k=0;$k<=8;$k++)
			{
				echo "<input type='hidden' NAME='fk[".$j."][".$k."]' value='".$fk[$j][$k]."'/>";
			}
			echo "<tr><td bgcolor='#57C8D5' colspan='5' align=center><hr></td></tr>";
		}

	}
	else
	{
		echo "<tr><td bgcolor='#57C8D5' colspan=5 align=center><b>DETALLE DE FORMAS DE PAGO</b></td></tr>";
	}

	if ($nuevalinea==1)
	{
		echo "<tr>";
		//FORMA DE PAGO
		echo "<td align=left bgcolor='DDDDDD'><b>Forma de pago:</b><br><select name='pago' onchange='enter(3)'>";
		for ($i=1;$i<=count($wfpa);$i++)
		{
			echo "<option>".$wfpa[$i]."</option>";
		}
		echo "</select></td>";

		//DOCUMENTO ANEXO
		echo "<td bgcolor='DDDDDD'><b><font text >Dcto Anexo: </font></b><br><INPUT TYPE='text' NAME='anexo' VALUE='".$anexo."'></td>";  //wdocane

		if ($obliga)
		{
			//observacioes de banco, consulto lista de bancos
			echo "<td bgcolor='DDDDDD'><b><font text >Datos del Banco de Origen:<br></font></b><select name='origen' >";

			for ($y=1;$y<=count($origenes);$y++)
			{
				echo "<option>".$origenes[$y]."</option >";
			}
			echo "</select></td>";

			$colspan=10;

			//Si es plaza o local

			if ($ubicacion) //Si ya fue digitado el documento anexo
			{
				If ($ubicacion=='1-Local')
				$otro='2-Otras plazas';
				else
				$otro='1-Local';
				echo "<td bgcolor='DDDDDD'><b><font text >Ubicacion: </font></b><br><select name='ubicacion' ><option selected>".$ubicacion."</option ><option>".$otro."</option></select></td>";
			}
			else
			{
				echo "<td bgcolor='DDDDDD'><b><font text >Ubicacion: </font></b><br><select name='ubicacion' ><option selected>1-Local</option ><option>2-Otras plazas</option></select></td>";                        //wdocane
			}

			//numero de autorizacion
			echo "<td bgcolor='DDDDDD'><b><font text >Nº autorizacion: </font></b><br><INPUT TYPE='text' NAME='autorizacion' VALUE='".$autorizacion."'></td>";     //wobsrec
		}else
		{
			//observaciones
			echo "<td bgcolor='DDDDDD'><b><font text >Observación.: </font></b><br><INPUT TYPE='text' NAME='origen' VALUE=''></td>";     //wobsrec
			//espacios en blanco para nivelar
			echo "<td bgcolor='DDDDDD'><b><font text >&nbsp;</td>";
			echo "<td bgcolor='DDDDDD'><b>&nbsp;</td>";
			echo "<input type='hidden' NAME='ubicacion' value='' />";
			echo "<input type='hidden' NAME='autorizacion' value='' />";
		}
		echo '</tr>';
		echo '<tr>';

		//observaciones de banco
		echo "<td bgcolor='#c0c0c0' colspan=2><b><font >Datos del Banco de Destino:</font></b><select name='destino' >";
		for ($y=1;$y<=count($destinos);$y++)
		{
			echo "<option>".$destinos[$y]."</option>";
		}
		echo "</select></td>";

		//valor
		echo "<td bgcolor='#c0c0c0' align='center'><b><font text >Valor: </b><INPUT TYPE='text' NAME='valor'></font></td>";  //wvalfpa
		echo "<td bgcolor='#ffcc66' colspan=2 align='center'><b><font text >Saldo: ".number_format($saldo,2,'.',',')."</b>&nbsp;</font></td>";
		echo "</tr>";

		echo "<input type='hidden' NAME='saldo' value='".$saldo."' />";
		echo "<input type='hidden' NAME='wobs' value='".$wobs."' />";
		echo "<input type='hidden' NAME='grabar' value='1' />";
		echo "<input type='hidden' NAME='bandera' value='3' />";
		echo "<tr><td align=center bgcolor=#cccccc colspan='5'><input type='submit' value='OK'></td></tr>";
		echo "</table></form>";
	}

}

/**********************************************PROGRAMA*********************************************************************/
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
	pintarVersion();
	pintarTitulo();
	

	or die("No se ralizo Conexion");
	


	$senal=0; //Inicializacion de mensajes de alerta

	//INCIALIZACION DE VARIABLES DE CONTEXTO
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user));
	$wfecha=date("Y-m-d");
	$hora = (string)date("H:i:s");

	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
	
	
	//Aca traigo las variables necesarias de la empresa
	$q = " SELECT empdes "
	    ."   FROM root_000050 "
	    ."  WHERE empcod = '".$wemp_pmla."'"
	    ."    AND empest = 'on' ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res); 
	  
	$wnominst=$row[0];
	 
	/////////////////////////////////////////////////////////////////////////////////////////
	//Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
	$q = " SELECT detapl, detval, empdes, empbda, emphos "
	    ."   FROM root_000050, root_000051 "
	    ."  WHERE empcod = '".$wemp_pmla."'"
	    ."    AND empest = 'on' "
	    ."    AND empcod = detemp "; 
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res); 
	  
	if ($num > 0 )
	   {
	    for ($i=1;$i<=$num;$i++)
	     {   
	      $row = mysql_fetch_array($res);
	      
	      $wbasedato=$row[3];   //Base de dato de la empresa
	      $wemphos=$row[4];     //Indica si la facturacion es Hospitalaria o POS
	      
	      if ($row[0] == "cenmez")
	         $wcenmez=$row[1];
	         
	      if ($row[0] == "afinidad")
	         $wafinidad=$row[1];
		         
	      if ($row[0] == "movhos")
	         $wbasedato=$row[1];
		         
	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];
		         
	      if ($row[0] == "camilleros")
	         $wcencam=$row[1];   
		         
	      $winstitucion=$row[2];   
	     }  
	   }
	  else
	    echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	///////////////////////////////////////////////////////////
	
	
	//consulto la caja asignada al usuario
	$caja=consultarCaja($wusuario, &$wcco, &$wnomcco, &$wcaja, &$wnomcaj, &$wpabono, &$wprecibo, &$wpcredito, &$wpdebito, &$wtiping);

	if(!isset ($ref))
	{
		$ref='';
	}

	if ($caja)
	{
		//se verifica el estado del programa, si es primera vez que se abre para incializar variables
		if (!isset ($bandera))  //'S' Indica que se esta iniciando un recibo o nota
		{
			borrarTem(date("Y-m-d")); //BORRO LOS REGISTROS DE TRES DIA ANTES DE LA TABLA TEMPORAL DE RECIBOS Y NOTAS
			//consulto dropdown de fuentes que el usuario puede utilizar
			$fuentes= consultarFuentes('', $wusuario);
			//consulto dropdown de responsables
			$wempresas=consultarResponsables('','');
			pintarParametros($fuentes, '', '', '', date('Y-m-d'), $wnomcco, $wnomcaj, $wempresas, false, false, '', '');
			pintarBoton(1, true);
		}
		else
		{
			//hacemos una recepcion de los parametros INICIALES
			if (isset($wfecdoc) and $wfecdoc=='')
			{
				$wfecdoc=date('Y-m-d');
			}
			//hacemos un explode de la fuente seleccionada en el programa para separar codigo y nombre
			if (isset ($wfuente) and $wfuente!='')
			{
				$exp=explode('-',$wfuente);
				$wfuenCod=$exp[0];
				$wfuenNom=$exp[1];
				//hacemos la validacion de fechas
				$valfec=validarFecha($wfecdoc, $wfuenCod, &$dias);
				if(!$valfec)
				{
					pintarAlert3('LA FECHA PARA EL DOCUMENTO NO ESTA PREMITIDA, ESTA DEBE SER DE HACE ' .$dias. ' DIAS MAXIMO. Se ha cambiado la fecha ingresada por la actual');
					$wfecdoc=date('Y-m-d');
				}
			}
			else
			{
				$wfuenCod='';
				$wfuenNom='';
				$wfuente='';
				$wfecdoc=date('Y-m-d');
			}
			$fuentes= consultarFuentes($wfuente, $wusuario);

			//hacemos un explode de la empresa responsable
			if (isset ($wempresa) and $wempresa!='')
			{
				$exp=explode('-',$wempresa);
				$wempCod=$exp[0];
				$wempNit=$exp[1];
				$wempNom=$exp[2];
				$wempRaz=$exp[3];
			}
			else
			{
				$wempCod='';
				$wempNit='';
				$wempNom='';
				$wempRaz='';
				$wempresa='';
			}

			switch ($bandera)
			{
				case 1: //en este caso puede haber dos opciones, seleccion de fuente del drop down o ingreso de fuente para consulta
				if (isset($grabar))
				{
					unset($grabar);
				}

				//si hay registros ya ingresado y se dio en la opcion eliminar
				if (isset($wid))
				{
					//se manda ref porque si es una refacturación al eliominar algun registro se deben eliminar todos
					if(!isset($ref))
					{
						$ref='';
					}
					eliminarRegistro($wid, $ref);
				}

				if (isset($lista))
				{
					unset($lista);
				}

				if ($wfuente!='') //vamos a crear un documento
				{
					//se averiguan las caracteristicas de la fuente seleccionada
					consultarFuente($wfuenCod,&$wcarndb, &$wcarncr, &$wcarrec, &$wcarcca, &$wcarcfa, &$wcarfpa, &$wcarroc, &$wnrodoc);
					//consulto si hay documentos en tabla temporal
					$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
					if (!$temporal) //si no hay archivos en temporal para la fuente, construyo la lista de empresas
					{
						$wempresas=consultarResponsables($wempresa, $wbuscador);
						$temporal=false;
					}
					else //de lo contario no, porque se obliga a trabajar sobre la empresa que se habia empezado a generar el documento
					{
						if ($temporal[0]['temfco']=='off')
						{
							//por conceptos de facturacion
							$wcarcfa=false;
						}
						else
						{
							$wcarcca=false;
						}
						$wempresas[1]=$wempresa;
						calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
					}

					pintarParametros($fuentes, $wfuente, '', $wnrodoc, $wfecdoc, $wnomcco, $wnomcaj, $wempresas, $wcarrec, $wcarroc, 'manual', '');
					pintarBoton(1, false);

					if ($wcarcca or $wcarrec)
					{
						if ($wcarroc or (!$wcarrec and !$wcarcfa))//recibos por otros conceptos
						{
							$conceptos=consultarConceptos('', $wfuenCod);
						}
						else
						{
							$conceptos=false;
						}
						if ($temporal)
						{
							if ($wcarrec)
							{
								if (!isset($grabar))
								pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, '', '','','', '',  '',$conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
							}
							else if ($temporal[0]['temfco']=='off')
							{
								$exp=explode('-',$temporal[0]['temnfa']);
								if (!isset($grabar))
								pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $temporal[0]['temffa'], $exp[0],$exp[1],$temporal[0]['temvfa'], $temporal[0]['temsfa'],  '',$conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
							}
						}
						else
						{
							if (!isset($grabar))
							pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', '', '', '', '', '', '', $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
						}
					}

					if ($wcarcfa and !$wcarrec)
					{
						if ($temporal)
						{
							$exp=explode('-',$temporal[0]['temnfa']);
							$cargos=consultarCargos($temporal, false, $exp[0], $exp[1], $temporal[0]['temffa'], $wcarndb, $wcarncr, true);
							$glosas=consultarGlosas($temporal[0]['temglo'], $temporal[0]['temffa'], $exp[0], $exp[1]);
							if ($cargos)
							{
								$exp2=explode('-', $cargos[1]);
								consultarCargo($temporal[0]['temnfa'], $temporal[0]['temffa'], $exp2[0],  $exp2[2], $exp2[3], &$valini, &$salcon);
								$valmax=consultarValorMaximo ($temporal[0]['temffa'], $temporal[0]['temnfa'], $exp2[0], $exp2[2], $exp2[3], $valini);
								if ($wcarncr) //calculo el valor maximo para cada uno de los casos
								{
									if ($valmax>$temporal[0]['temsfa'])
									{
										$valmax=$temporal[0]['temsfa'];
									}
								}
								else
								{
									if (($temporal[0]['temvfa']-$temporal[0]['temsfa']) > ($valini-$valmax))
									{
										$valmax=$valini-$valmax;

									}
									else
									{
										$valmax=$temporal[0]['temvfa']-$temporal[0]['temsfa'];
									}
								}
								if (!isset($grabar))
								pintarFacturacion($temporal, $wcarndb, $wcarncr, $wtotvalcon, $temporal[0]['temffa'], $exp[0], $exp[1], $temporal[0]['temvfa'], $temporal[0]['temsfa'], $salcon, $cargos, $valini, $valmax, '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $glosas, $ref);
							}
							else
							{
								if (!isset($grabar))
								pintarFacturacion($temporal, $wcarndb, $wcarncr, $wtotvalcon, $temporal[0]['temffa'], $exp[0], $exp[1], $temporal[0]['temvfa'], $temporal[0]['temsfa'], '', '', '', '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, false, $ref);
							}
						}
						else
						{
							if (!isset($grabar))
							pintarFacturacion($temporal, $wcarndb, $wcarncr, '' ,'', '', '', '', '', '', '', '', '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, false, '', $ref);
						}
					}

					$aviso=true;

					if ($wcarcfa!='on' and $wcarcca!='on' and $wcarrec!='on')
					{
						pintarAlert3('LA FUENTE NO TIENE REGISTRADO EN EL MAESTRO QUE TIPO DE CONCEPTOS UTILIZA');
						$aviso=false;
					}

					if ($wcarcfa=='on' and $wcarrec=='on')
					{
						pintarAlert3('LOS RECIBOS NO PUDEN UTILIZAR CONCEPTOS DE FACTURACION, REVISE SU MAESTRO');
						$aviso=false;
					}

					if ($wcarroc=='on' and $wcarrec=='on' and $wcarcca!='on' )
					{
						pintarAlert3('LOS RECIBOS POR OTROS CONCEPTOS DEBEN TENER ACTIVADOS LOS CONCEPTOS DE CARTERA, REVISE SU MAESTRO');
						$aviso=false;
					}

					if ($aviso)
					{
						if ($wcarncr or $wcarndb) //las notas debito y credito utilizan causas introducidas por el usuario
						{
							$causas=consultarCausas($wcarncr, $wcarndb);
							if ($causas)
							{
								if (!isset ($lista))
								{
									$lista=false;
								}
								if (isset ($ingCau) and $ingCau=='1' and $cauSel!='')//se ha dado click sobre una causa para registrarla
								{
									$lista= agregarCausa($lista, $cauSel);
								}
								else if (isset($eliCau) and $eliCau=='1')
								{
									$lista=eliminarCausa($lista, $index);
								}
								pintarCausas($causas, $lista, true);
							}
						}
						pintarObservaciones('', 1, $wnrodoc, $wfuenCod, $wcco, false);
					}

				}
				else if ($wfcon!='')//consulta de un documento
				{
					$exp=explode('-',$wfcon);
					$wfuenCod=trim($exp[0]);

					consultarFuente($wfuenCod,&$wcarndb, &$wcarncr, &$wcarrec, &$wcarcca, &$wcarcfa, &$wcarfpa, &$wcarroc, &$wnrocon);
					if ($wnrodoc=='')
					{
						$wnrodoc=$wnrocon;
					}

					if(isset ($wcco2))//intercambio el valor del wcco2 y del wcco
					{
						$c=$wcco2;
						$wcco2=$wcco;
						$wcco=$c;
					}
					else
					{
						$wcco2=$wcco;
					}

					//consulto el documento

					$temporal=consultarDocumento($wfuenCod, $wnrodoc, $wcco, &$wobs, &$estado, &$empresa, $wcarndb, &$wfecdoc, &$ref);
					if (!$temporal) //No se encontro ningun documento
					{
						pintarAlert3('NO EXISTE EL DOCUMENTO PARA LOS PARAMETROS INGRESADOS');
						$costos=consultarCostos($wfuenCod, $wnrodoc, $wcco);
						if (!$costos)
						{
							$wempresas=consultarResponsables('', '');
							pintarParametros($fuentes, '', '', '', $wfecdoc, $wnomcco, $wnomcaj, $wempresas, false, false, '', '');
							pintarBoton(1, true);
						}
						else
						{
							$wempresas[1]=$empresa;
							calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
							pintarParametros($fuentes, '', $wfcon, $wnrodoc, $wfecdoc, $wnomcco, $wnomcaj, $wempresas, false, false, '', '');
							pintarBoton(1, false);
							pintarCostos($costos, true);
						}
					}
					else //de lo contario no, porque se obliga a trabajar sobre la empresa que se habia empezado a generar el documento
					{
						if ($temporal[0]['temfco']=='off')
						{
							$wcarcfa=false;
						}
						else
						{
							$wcarcca=false;
						}
						$wempresas[1]=$empresa;
						calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
						pintarParametros($fuentes, '', $wfcon, $wnrodoc, $wfecdoc, $wnomcco, $wnomcaj, $wempresas, false, false, '', '');
						pintarBoton(1, false);

						if ($estado!='on')
						{
							pintarAlert4('ANULADO');
						}

						$costos=consultarCostos($wfuenCod, $wnrodoc, $wcco);
						pintarCostos($costos, false);

						if ($wcarcca or $wcarrec)
						{
							if ($wcarrec)
							{
								pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, '', '','','', '',  '',false, false, '', '', 0, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
							}
							else
							{
								$exp=explode('-',$temporal[0]['temnfa']);
								pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $temporal[0]['temffa'], $exp[0],$exp[1],$temporal[0]['temvfa'], $temporal[0]['temsfa'],  '','', false, '', '', 0, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
							}
						}
						if ($wcarcfa and !$wcarrec)
						{
							//variable para indicar que la nota es de refacturacion
							if(!isset($ref))
							{
								$ref='';
							}
							$exp=explode('-',$temporal[0]['temnfa']);
							pintarFacturacion($temporal, $wcarndb, $wcarncr, $wtotvalcon, $temporal[0]['temffa'], $exp[0], $exp[1], $temporal[0]['temvfa'], $temporal[0]['temsfa'], '', false, '', '', '', 0, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $temporal[0]['temglo'], $ref);
						}
						if ($wcarncr or $wcarndb) //las notas debito y credito utilizan causas introducidas por el usuario
						{
							$lista=consultarLista($wnrodoc, $wfuenCod, $wcarncr);
							if (isset($lista[0]))
							{
								pintarCausas(false, $lista, false);
							}
						}
						if ($wcarfpa)
						{
							if ($wcarroc)
							{
								$fk=consultarFormas($wfuenCod, $wnrodoc, $wcco, $wtotvalcon, $estado);
							}
							else
							{
								$fk=consultarFormas($wfuenCod, $wnrodoc, $wcco, $wtotvcafac, $estado);
							}
							pintarPagos($fk,'', '', '', '', '', '', '', '', '',  '', '', '', 0);
						}
						if ($wcco==$wcco2)
						{
							if ($estado=='on')
							{
								$wcarabo=consultarAbono($wfuenCod);
								if (!$wcarabo)
								{
									pintarObservaciones($wobs, 0, $wnrodoc, $wfuenCod, $wcco, true);
								}
								else
								{
									pintarObservaciones($wobs, 0, $wnrodoc, $wfuenCod, $wcco, false);
								}
							}
							else
							{
								pintarObservaciones($wobs, 0, $wnrodoc, $wfuenCod, $wcco, false);
							}
						}
						else
						{
							pintarObservaciones($wobs, 0, $wnrodoc, $wfuenCod, $wcco, false);
						}
					}
				}
				else //no se ha seleccionado ninguno de los dos casos
				{
					$wempresas=consultarResponsables($wempresa, $wbuscador);
					pintarParametros($fuentes, $wfuente, '', $wnrodoc, $wfecdoc, $wnomcco, $wnomcaj, $wempresas, false, false, 'manual', '');
					pintarBoton(1, true);
				}
				break;

				case 2:

				//cambio valores negativos
				if(isset($cancon) and $cancon!='' and $cancon<0)
				{
					$cancon=-$cancon;
				}
				if(isset($canfac) and $canfac!='' and $canfac<0)
				{
					$canfac=-$canfac;
				}

				//consulto si hay documentos en tabla temporal
				$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
				//consulto las caracteristicas de la fuente seleccionada

				consultarFuente($wfuenCod,&$wcarndb, &$wcarncr, &$wcarrec, &$wcarcca, &$wcarcfa, &$wcarfpa, &$wcarroc, &$wnrodoc);
				if (!$temporal) //si no hay archivos en temporal para la fuente, construyo la lista de empresas
				{
					$wempresas=consultarResponsables($wempresa, $wbuscador);
					$temporal=false;

					if (isset ($valcon) and $valcon!='')
					{
						$wcarcca=false;
					}
				}
				else //de lo contario no, porque se obliga a trabajar sobre la empresa que se habia empezado a generar el documento
				{
					$wempresas[1]=$wempresa;
					if ($temporal[0]['temfco']=='off')
					{
						$wcarcfa=false;
					}
					else
					{
						$wcarcca=false;
					}
				}

				pintarParametros($fuentes, $wfuente, '', $wnrodoc, $wfecdoc, $wnomcco, $wnomcaj, $wempresas, $wcarrec, $wcarroc, 'manual', '');
				pintarBoton(1, false);

				//Realizo validaciones según el tipo de actividad que se esta realizando
				if ($wcarroc) //cuando no utiliza factura por ser un recibo por otros conceptos
				{
					$conceptos=consultarConceptos($nomcon, $wfuenCod);

					if ($cancon==0)
					{
						$cancon='';
					}
					if ($cancon!='' and $nomcon!='')
					{
						$preguntar=preguntarCentro($wfuenCod, $nomcon, &$multi);
						if ($preguntar)
						{
							if (isset ($centro) and $centro!='')
							{
								$validar=validarCentro($centro);
								if ($validar)
								{
									//guardamos documento en temporal
									guardarTemporal(date('Y-m-d'), (string)date("H:i:s"), $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, '', '', '0', '', '', $centro, 'off', '', $multi, $wusuario, $nomcon, $cancon, $wcarcca, $wcarcfa, $wcarrec, $wcarndb, $wcarncr, '');
									$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
									calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
									if (isset ($grabar) and $total<0)
									{
										pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
										unset($grabar);
									}
									if (!isset($grabar))
									pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, '', '', '', '', '', '', $conceptos, '', '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
								}
								else
								{
									pintarAlert3('DEBE INGRESAR UN CENTRO DE COSTOS VALIDO PARA EL CONCEPTO');
									if (isset($grabar))
									unset($grabar);
									$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
									if ($temporal)
									{
										calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
										pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, '', '', '', '', '', $conceptos, 1, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
									}
									else
									{
										pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', '', '', '', '', '', '', $conceptos, 1, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
									}
								}
							}
							else
							{
								if (isset($grabar))
								unset($grabar);
								if ($temporal)
								{
									calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
									pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, '', '', '', '', '', '', $conceptos, 1, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
								}
								else
								{
									pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', '', '', '', '', '', '', $conceptos, 1, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
								}
							}
						}
						else
						{
							//guardamos documento en temporal
							guardarTemporal(date('Y-m-d'), (string)date("H:i:s"), $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, '', '', '0', '', '', '', 'off', '', $multi, $wusuario, $nomcon, $cancon, $wcarcca, $wcarcfa, $wcarrec, $wcarndb, $wcarncr, '');
							$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
							calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
							if (isset ($grabar) and $total<0)
							{
								pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
								unset($grabar);
							}
							if (!isset($grabar))
							pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, '', '', '', '', '', '', $conceptos, '', '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
						}
					}
					else
					{
						if ($temporal)
						{
							calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
							if (isset ($grabar) and $total<0)
							{
								pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
								unset($grabar);
							}
							if (!isset($grabar))
							pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, '', '', '', '', '', '', $conceptos,0, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
						}
						else
						{
							if (isset($grabar))
							unset($grabar);
							pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', '', '', '', '', '', '', $conceptos, 0, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
						}
					}
				}
				else  //cuando utiliza factura (demas casos diferentes a recibos por otros conceptos)
				{
					if ($fuefac!='' and $prefac!='' and $numfac!='')//EN TODO TIPO DE DOCUMENTO RESTANTE ESTA ASOCIADA UNA FACTURA
					{
						$existe=buscarFactura($wcarrec, $fuefac, $prefac, $numfac, $wempresa, &$wvalfac, &$wsalfac, &$tipo, $wfuente, $wcarndb, $wcaja,$wcarncr, $wcarcca);
						if ($existe)//la factura existe para el responsable
						{
							if ($wcarrec or $wcarcca )//se realizan las validaciones para conceptos de cartera
							{
								if ($wcarrec and !$wcarcca )//validaciones para recibos sin conceptos de cartera
								{

									if ($canfac!='' and $canfac!=0)
									{
										//unicamente se valida que el valor a cancelar no supere el saldo de la factura
										if (($wsalfac-$canfac)<0)
										{
											pintarAlert3('EL VALOR A CANCELAR NO PUEDE SER MAYOR AL SALDO DE LA FACTURA');
											if (isset($grabar))
											unset($grabar);
											if ($temporal)
											{
												calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
												pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, '', false, 1, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
											}
											else
											{
												pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, '', false, 1, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
											}
										}
										else
										{
											//guardamos documento en temporal
											guardarTemporal(date('Y-m-d'), (string)date("H:i:s"), $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, $prefac, $numfac, $canfac, $wvalfac, $wsalfac, '', 'off', $fuefac, 1, $wusuario, '', 0, $wcarcca, $wcarcfa, $wcarrec, $wcarndb, $wcarncr, '');
											$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
											calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
											if (isset ($grabar) and $total<0)
											{
												pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
												unset($grabar);
											}
											$exp=explode('-',$temporal[0]['temnfa']);
											if (!isset($grabar))
											pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $temporal[0]['temffa'], $exp[0], '', '', '', '', false, 1, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
										}
									}
									else
									{
										if ($temporal)
										{
											calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
											if (isset ($grabar) and $total<0)
											{
												pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
												unset($grabar);
											}
											if (!isset($grabar))
											pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, false, 1, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
										}
										else
										{
											pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, false, 1, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
										}
									}
								}
								if ($wcarrec and $wcarcca)//validaciones para recibos con conceptos de cartera
								{
									if (isset ($nomcon))
									{
										$conceptos=consultarConceptos($nomcon, $wfuenCod);
									}
									else
									{
										$conceptos=consultarConceptos('', $wfuenCod);
									}

									if ($canfac!='' and $canfac!=0 and isset($nomcon) and $nomcon=='' and isset($cancon) and $cancon=='')
									{

										//unicamente se valida que el valor a cancelar no supere el saldo de la factura
										if (($wsalfac-$canfac)<0)
										{
											pintarAlert3('EL VALOR A CANCELAR NO PUEDE SER MAYOR AL SALDO DE LA FACTURA');
											if (isset($grabar))
											unset($grabar);
											if ($temporal)
											{
												calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
												pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, $canfac);
											}
											else
											{
												pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, $canfac);
											}
										}
										else
										{
											//guardamos documento en temporal
											guardarTemporal(date('Y-m-d'), (string)date("H:i:s"), $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, $prefac, $numfac, $canfac, $wvalfac, $wsalfac, '', 'off', $fuefac, 1, $wusuario, '', 0, $wcarcca, $wcarcfa, $wcarrec, $wcarndb, $wcarncr, '');
											$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
											calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
											if (isset ($grabar) and $total<0)
											{
												pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
												unset($grabar);
											}
											$exp=explode('-',$temporal[0]['temnfa']);
											if (!isset($grabar))
											pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $temporal[0]['temffa'], $exp[0], '', '', '', '', $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, $wsalfac);
										}
									}
									else if (isset($nomcon) and $nomcon!='' and isset($cancon) and $cancon!='' and $cancon!=0)
									{
										if ($canfac=='')
										{
											$canfac=0;
										}
										$preguntar=preguntarCentro($wfuenCod, $nomcon, &$multi);
										if (($wsalfac-$canfac+($cancon*$multi))>=0)
										{
											if ($preguntar)
											{
												if (isset ($centro) and $centro!='')
												{
													$validar=validarCentro($centro);
													if ($validar)
													{
														//guardamos documento en temporal
														guardarTemporal(date('Y-m-d'), (string)date("H:i:s"), $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, $prefac, $numfac, $canfac, $wvalfac, $wsalfac,  $centro, 'off', $fuefac, $multi, $wusuario, $nomcon, $cancon, $wcarcca, $wcarcfa, $wcarrec, $wcarndb, $wcarncr, '');
														$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
														calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
														if (isset ($grabar) and $total<0)
														{
															pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
															unset($grabar);
														}
														$exp=explode('-',$temporal[0]['temnfa']);
														if (!isset($grabar))
														pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $temporal[0]['temffa'], $exp[0], '', '', '', '', false, '', '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
													}
													else
													{
														pintarAlert3('DEBE INGRESAR UN CENTRO DE COSTOS VALIDO PARA EL CONCEPTO');
														if (isset($grabar))
														unset($grabar);
														$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
														if ($temporal)
														{
															calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
															pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $canfac, $conceptos, 1, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, $canbru);
														}
														else
														{
															pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '',$fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $canfac, $conceptos, 1, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, $canbru);
														}
													}
												}
												else
												{
													if (isset($grabar))
													unset($grabar);
													if ($temporal)
													{
														calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
														pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $canfac, $conceptos, 1, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, $canbru);
													}
													else
													{
														pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '',$fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $canfac, $conceptos, 1, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, $canbru);
													}
												}
											}
											else
											{
												//guardamos documento en temporal
												guardarTemporal(date('Y-m-d'), (string)date("H:i:s"), $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, $prefac, $numfac, $canfac, $wvalfac, $wsalfac,  '', 'off', $fuefac, $multi, $wusuario, $nomcon, $cancon, $wcarcca, $wcarcfa, $wcarrec, $wcarndb, $wcarncr, '');
												$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
												calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
												if (isset ($grabar) and $total<0)
												{
													pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
													unset($grabar);
												}
												$exp=explode('-',$temporal[0]['temnfa']);
												if (!isset($grabar))
												pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $temporal[0]['temffa'], $exp[0], '', '', '', '', false, '', '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
											}
										}
										else
										{
											pintarAlert3('LA SUMA DEL VALOR A CANCELAR Y EL VALOR DEL CONCEPTO NO PUEDEN SER MAYORES AL SALDO DE LA FACTURA');
											if (isset($grabar))
											unset($grabar);
											if ($temporal)
											{
												calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
												pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, $wsalfac);
											}
											else
											{
												pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, $wsalfac);
											}
										}
									}
									else
									{
										if (!isset ($canfac) or $canfac=='')
										{
											if (!isset($nomcon) or $nomcon=='')
											{
												if(!isset($canbru) or $canbru=='')
												{
													$canbru=$wsalfac;
													$canfac=$wsalfac;
												}
												else
												{
													$canfac=$canbru;
												}
											}
											else
											{
												if(!isset($canbru) or $canbru=='')
												{
													$canbru=$wsalfac;
													$canfac=$wsalfac;
												}
												else
												{
													$canfac=$canbru;
												}
											}
										}

										if(isset($nomcon) and $nomcon!='' and (!isset($cancon) or $cancon==''))
										{
											if (!isset($indicador))
											{
												$indicador=1;
											}
											$cancon=calcularRetencion($wfuente, $nomcon, $canbru, &$canfac, $indicador);
										}
										else if (!isset($cancon))
										{
											$cancon='';
										}

										if ($temporal)
										{
											calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
											if (isset ($grabar) and $total<0)
											{
												pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
												unset($grabar);
											}
											if (!isset($grabar))
											pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $canfac, $conceptos, false, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, $canbru);
										}
										else
										{
											pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $canfac, $conceptos, false, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, $canbru);
										}
									}
								}
								if ($wcarncr or $wcarndb)
								{
									if (isset($nomcon))
									{
										$conceptos=consultarConceptos($nomcon, $wfuenCod);
									}
									else
									{
										$conceptos=consultarConceptos('', $wfuenCod);
									}

									if (isset ($glosa))
									{
										$glosas=consultarGlosas($glosa, $fuefac, $prefac, $numfac);
									}
									else
									{
										$glosas=consultarGlosas('', $fuefac, $prefac, $numfac);
									}

									if (isset($cancon) and $cancon!='' and isset($nomcon) and $nomcon!='')
									{
										$wcarcfa=false;
										$preguntar=preguntarCentro($wfuenCod, $nomcon, &$multi);
										$aviso=0;
										if ($wcarncr)
										{
											if (($wsalfac+($cancon*$multi))<0)
											{
												$aviso=1;
											}
										}
										if ($wcarndb)
										{
											if ($wvalfac>0)
											{
												if (($wvalfac-$wsalfac-$cancon)<0)
												{
													$aviso=2;
												}
											}
											else
											{
												if (($wsalfac+$cancon)>0)
												{
													$aviso=3;
												}
											}
										}

										if ($aviso==0)
										{
											if ($preguntar)
											{
												if (isset ($centro) and $centro!='')
												{
													$validar=validarCentro($centro);
													if ($validar)
													{
														//guardamos documento en temporal
														guardarTemporal(date('Y-m-d'), (string)date("H:i:s"), $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, $prefac, $numfac, 0, $wvalfac, $wsalfac,  $centro, 'off', $fuefac, $multi, $wusuario, $nomcon, $cancon, $wcarcca, $wcarcfa, $wcarrec, $wcarndb, $wcarncr, $glosa);
														$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
														calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
														if (isset ($grabar) and $total<0)
														{
															pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
															unset($grabar);
														}
														$exp=explode('-',$temporal[0]['temnfa']);
														if (!isset($grabar))
														pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $temporal[0]['temffa'], $exp[0], $exp[1], $temporal[0]['temvfa'], $temporal[0]['temsfa'], '', $conceptos, '', '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
													}
													else
													{
														pintarAlert3('DEBE INGRESAR UN CENTRO DE COSTOS VALIDO PARA EL CONCEPTO');
														if (isset($grabar))
														unset($grabar);
														$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
														if ($temporal)
														{
															calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
															pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, 1, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
														}
														else
														{
															pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, 1, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
														}
													}
												}
												else
												{
													if (isset($grabar))
													unset($grabar);
													if ($temporal)
													{
														calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
														pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, 1, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
													}
													else
													{
														pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, 1, '', $cancon, 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
													}
												}
											}
											else
											{
												//guardamos documento en temporal
												guardarTemporal(date('Y-m-d'), (string)date("H:i:s"), $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, $prefac, $numfac, 0, $wvalfac, $wsalfac,  '', 'off', $fuefac, $multi, $wusuario, $nomcon, $cancon, $wcarcca, $wcarcfa, $wcarrec, $wcarndb, $wcarncr, $glosa);
												$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
												calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
												if (isset ($grabar) and $total<0)
												{
													pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
													unset($grabar);
												}
												$exp=explode('-',$temporal[0]['temnfa']);
												if (!isset($grabar))
												pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $temporal[0]['temffa'], $exp[0], $exp[1], $temporal[0]['temvfa'], $temporal[0]['temsfa'], '', $conceptos, '', '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
											}
										}
										else if ($aviso==1)
										{
											pintarAlert3('EL VALOR DE LA NOTA CREDITO NO DEBE SUPERAR EL SALDO DE LA FACTURA');
											if (isset($grabar))
											unset($grabar);
											if ($temporal)
											{
												calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
												pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
											}
											else
											{
												pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
											}
										}
										else if ($aviso>1)
										{
											if ($aviso==2)
											{
												pintarAlert3('EL VALOR DE LA NOTA DEBITO NO DEBE SUPERAR EL VALOR DE LA FACTURA MENOS EL SALDO');
											}
											if ($aviso==3)
											{
												pintarAlert3('EL VALOR DE LA NOTA DEBITO NO DEBE DEJAR LA FACTURA CON SALDO POSITIVO');
											}
											if (isset($grabar))
											unset($grabar);
											if ($temporal)
											{
												calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
												pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
											}
											else
											{
												pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
											}
										}
									}
									else if (isset($nomcon)and $nomcon=='')
									{
										if ($temporal)
										{
											calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
											if (isset ($grabar) and $total<0)
											{
												pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
												unset($grabar);
											}
											if (!isset($grabar))
											pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
										}
										else
										{
											pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
										}
									}
									else
									{
										if ($temporal)
										{
											calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
											if (isset ($grabar) and $total<0)
											{
												pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
												unset($grabar);
											}
											if (!isset($grabar))
											pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
										}
										else
										{
											pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $wsalfac, $conceptos, false, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $glosas, '');
										}

									}
								}
							}

							if ($wcarcfa)//se realizan las validaciones para conceptos de facturacion
							{
								if ($temporal)
								{
									if (isset($cargo))
									{
										if ($codcar!='')
										{
											$cargos=consultarCargos($temporal, $codcar, $prefac, $numfac, $fuefac, $wcarndb, $wcarncr, false);
											$exp=explode('-', $cargos[1]);
										}
										else
										{
											$cargos=consultarCargos($temporal, $cargo, $prefac, $numfac, $fuefac, $wcarndb, $wcarncr, true);
											$exp=explode('-', $cargo);
										}
										consultarCargo(($prefac.'-'.$numfac), $fuefac, $exp[0],  $exp[2], $exp[3], &$valini, &$salcon);
										$valmax=consultarValorMaximo ($fuefac, ($prefac.'-'.$numfac), $exp[0], $exp[2], $exp[3], $valini);
									}
									else
									{
										$cargos=consultarCargos($temporal, false, $prefac, $numfac, $fuefac, $wcarndb, $wcarncr, true);
										if ($cargos)
										{
											$exp=explode('-', $cargos[1]);
											consultarCargo(($prefac.'-'.$numfac), $fuefac, $exp[0],  $exp[2], $exp[3], &$valini, &$salcon);
											$valmax=consultarValorMaximo ($fuefac, ($prefac.'-'.$numfac), $exp[0], $exp[2], $exp[3], $valini);
										}
									}
								}
								else
								{
									if(!isset($ref) or $ref=='')
									{
										if (isset($cargo))
										{
											if ($codcar!='')
											{
												$cargos=consultarCargos(false, $codcar, $prefac, $numfac, $fuefac, $wcarndb, $wcarncr, false);
												$exp=explode('-', $cargos[1]);
											}
											else
											{
												$cargos=consultarCargos(false, $cargo, $prefac, $numfac, $fuefac, $wcarndb, $wcarncr, true);
												$exp=explode('-', $cargo);
											}
											consultarCargo(($prefac.'-'.$numfac), $fuefac, $exp[0],  $exp[2], $exp[3], &$valini, &$salcon);
											$valmax=consultarValorMaximo ($fuefac, ($prefac.'-'.$numfac), $exp[0], $exp[2], $exp[3], $valini);
										}
										else
										{
											$cargos=consultarCargos(false, false, $prefac, $numfac, $fuefac, $wcarndb, $wcarncr, true);
											if ($cargos)
											{
												$exp=explode('-', $cargos[1]);
												consultarCargo(($prefac.'-'.$numfac), $fuefac, $exp[0],  $exp[2], $exp[3], &$valini, &$salcon);
												$valmax=consultarValorMaximo ($fuefac, ($prefac.'-'.$numfac), $exp[0], $exp[2], $exp[3], $valini);
											}
										}
									}
									else
									{
										//aca creamos todos los datos de una vez en el programa
										$temporal=crearRefacturacion($prefac, $numfac, $fuefac, $wusuario, $wcaja, $wfuente, $wempresa, $wnrodoc, $wfecdoc, $wcco, $wvalfac, $wsalfac, $glosa);
										if($temporal)
										{
											unset($cargo);
										}
										else
										{
											$ref='';
										}
										if (isset($cargo))
										{
											if ($codcar!='')
											{
												$cargos=consultarCargos($temporal, $codcar, $prefac, $numfac, $fuefac, $wcarndb, $wcarncr, false);
												$exp=explode('-', $cargos[1]);
											}
											else
											{
												$cargos=consultarCargos($temporal, $cargo, $prefac, $numfac, $fuefac, $wcarndb, $wcarncr, true);
												$exp=explode('-', $cargo);
											}
											consultarCargo(($prefac.'-'.$numfac), $fuefac, $exp[0],  $exp[2], $exp[3], &$valini, &$salcon);
											$valmax=consultarValorMaximo ($fuefac, ($prefac.'-'.$numfac), $exp[0], $exp[2], $exp[3], $valini);
										}
										else
										{
											$cargos=consultarCargos($temporal, false, $prefac, $numfac, $fuefac, $wcarndb, $wcarncr, true);
											if ($cargos)
											{
												$exp=explode('-', $cargos[1]);
												consultarCargo(($prefac.'-'.$numfac), $fuefac, $exp[0],  $exp[2], $exp[3], &$valini, &$salcon);
												$valmax=consultarValorMaximo ($fuefac, ($prefac.'-'.$numfac), $exp[0], $exp[2], $exp[3], $valini);
											}
										}
									}
								}

								if (isset($glosa))
								{
									$glosas=consultarGlosas($glosa, $fuefac, $prefac, $numfac);
								}
								else
								{
									$glosas=consultarGlosas('', $fuefac, $prefac, $numfac);
								}

								if ($cargos)
								{
									if ($wcarncr) //calculo el valor maximo para cada uno de los casos
									{
										if ($valmax>$wsalfac)
										{
											$valmax=$wsalfac;
										}
									}
									else
									{
										if (($wvalfac-$wsalfac) > ($valini-$valmax))
										{
											$valmax=$valini-$valmax;

										}
										else
										{
											$valmax=$wvalfac-$wsalfac;
										}
									}
								}

								if (isset($valcon) and $valcon!='')//validacion del valor ingresado para la nota para el concepto
								{
									if ($valcon>$valmax)
									{
										pintarAlert3('EL VALOR DEL CONCEPTO NO PUEDE SER MAYOR AL VALOR MAXIMO ESTABLECIDO PARA LA NOTA');
										if (isset($grabar))
										unset($grabar);
										if ($temporal)
										{
											calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
											pintarFacturacion($temporal, $wcarndb, $wcarncr, $wtotvalcon, $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $salcon, $cargos, $valini, $valmax, '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $glosas, $ref);

										}
										else
										{
											pintarFacturacion($temporal, $wcarndb, $wcarncr, '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $salcon, $cargos, $valini, $valmax, '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $glosas, $ref);
										}
									}
									else
									{
										//guardamos documento en temporal
										guardarTemporal(date('Y-m-d'), (string)date("H:i:s"), $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, $prefac, $numfac, 0, $wvalfac, $wsalfac,  '', 'on', $fuefac, '', $wusuario, $cargo, $valcon, false, $wcarcfa, $wcarrec, $wcarndb, $wcarncr, $glosa);
										$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
										calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
										if (isset ($grabar) and $total<0)
										{
											pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
											unset($grabar);
										}
										$exp=explode('-',$temporal[0]['temnfa']);
										$cargos=consultarCargos($temporal, false, $prefac, $numfac, $fuefac, $wcarndb, $wcarncr, true);
										if ($cargos)
										{

											$exp=explode('-', $cargos[1]);
											consultarCargo(($prefac.'-'.$numfac), $fuefac, $exp[0],  $exp[2], $exp[3], &$valini, &$salcon);
											$valmax=consultarValorMaximo ($fuefac, ($prefac.'-'.$numfac), $exp[0], $exp[2], $exp[3], $valini);
											if ($wcarncr) //calculo el valor maximo para cada uno de los casos
											{
												if ($valmax>$temporal[0]['temsfa'])
												{
													$valmax=$temporal[0]['temsfa'];
												}
											}
											else
											{
												if (($wvalfac-$temporal[0]['temsfa']) > ($valini-$valmax))
												{
													$valmax=$valini-$valmax;

												}
												else
												{
													$valmax=$wvalfac-$temporal[0]['temsfa'];
												}
											}
											if (!isset($grabar))
											{
												pintarFacturacion($temporal, $wcarndb, $wcarncr, $wtotvalcon, $temporal[0]['temffa'], $exp[0], $exp[1], $temporal[0]['temvfa'], ($temporal[0]['temsfa']+$temporal[0]['temvco']*$temporal[0]['temdco']), $salcon, $cargos, $valini, $valmax, '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $glosas, $ref);
											}
										}
										else
										{
											if (!isset($grabar))
											{
												pintarFacturacion($temporal, $wcarndb, $wcarncr, $wtotvalcon, $temporal[0]['temffa'], $exp[0], $exp[1], $temporal[0]['temvfa'], ($temporal[0]['temsfa']+$temporal[0]['temvco']*$temporal[0]['temdco']), '', '', '', '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $glosas, $ref);
											}
										}
									}
								}
								else if (isset($valcon) and $valcon=='')
								{
									if ($temporal)
									{
										calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
										$exp=explode('-',$temporal[0]['temnfa']);
										if (!isset($grabar))
										{
											pintarFacturacion($temporal, $wcarndb, $wcarncr, $wtotvalcon, $temporal[0]['temffa'], $exp[0], $exp[1], $temporal[0]['temvfa'], ($temporal[0]['temsfa']+$temporal[0]['temvco']*$temporal[0]['temdco']), $salcon, $cargos, $valini, $valmax, '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $glosas, $ref);


										}
									}
									else
									{
										pintarFacturacion($temporal, $wcarndb, $wcarncr, '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $salcon, $cargos, $valini, $valmax, '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $glosas, $ref);
									}
								}
								else
								{
									if ($temporal)
									{
										calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
										$exp=explode('-',$temporal[0]['temnfa']);
										if (!isset($grabar))
										pintarFacturacion($temporal, $wcarndb, $wcarncr, $wtotvalcon, $temporal[0]['temffa'], $exp[0], $exp[1], $temporal[0]['temvfa'], ($temporal[0]['temsfa']+$temporal[0]['temvco']*$temporal[0]['temdco']), '', $cargos, '', '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $glosas, $ref);
									}
									else
									{
										if ($cargos)
										{
											pintarFacturacion($temporal, $wcarndb, $wcarncr, '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, $salcon, $cargos, $valini, $valmax, '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $glosas, $ref);
										}
										else
										{
											pintarFacturacion($temporal, $wcarndb, $wcarncr, '', $fuefac, $prefac, $numfac, $wvalfac, $wsalfac, '', $cargos, '', $valmax, '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $glosas, $ref);
										}
									}
								}
							}
						}
						else if ($tipo=='1')//la factura no existe para el responsable
						{
							pintarAlert3('LA FACTURA INGRESADA NO EXISTE PARA EL RESPONSABLE');
							$comienzo=1; //para pintar la alerta a continuacion
						}
						else
						{
							pintarAlert3('LA FACTURA INGRESADA SE ENCUENTRA EN UN ESTADO QUE NO PERMITE LA REALIZACION DE NOTAS');
							$comienzo=1; //para pintar la alerta a continuacion
						}
					}
					else
					{
						$comienzo=1; //para pintar la alerta a continuacion
					}

					if (isset ($comienzo) and $comienzo==1)//no se metio bien el responsable o no se ha digitado factura
					{
						if ($temporal)
						{
							calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
							if (isset ($grabar) and $total<0)
							{
								pintarAlert3('EL TOTAL DEL DOCUMENTO DEBE SER POSITIVO');
								unset($grabar);
							}
							$exp=explode('-',$temporal[0]['temnfa']);
							if ($wcarrec or $wcarcca)
							{
								if (!isset($grabar))
								pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $temporal[0]['temffa'], $exp[0], '', '', '', '', false, 1, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
							}

							if ($wcarcfa)
							{
								if (!isset($grabar))
								{
									pintarFacturacion($temporal, $wcarndb, $wcarncr, $wtotvalcon, '', '', '', '', '', '', '', '', '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, false, $ref);
								}
							}
						}
						else
						{
							if ($wcarrec or $wcarcca)
							{
								pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, '', '', '', '', '', '', '', '', '', false, 1, '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
							}
							if ($wcarcfa)
							{
								if(!isset($ref))
								{
									$ref='';
								}
								pintarFacturacion($temporal, $wcarndb, $wcarncr, '', '', '', '', '', '', '', '', '', '', '', 1, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, false, $ref);
							}
						}
					}
				}

				if ($wcarncr or $wcarndb) //las notas debito y credito utilizan causas introducidas por el usuario
				{
					$causas=consultarCausas($wcarncr, $wcarndb);
					if ($causas)
					{
						if (!isset ($lista))
						{
							$lista=false;
						}
						if (isset ($ingCau) and $ingCau=='1' and $cauSel!='')//se ha dado click sobre una causa para registrarla
						{
							$lista= agregarCausa($lista, $cauSel);
						}
						else if (isset($eliCau) and $eliCau=='1')
						{
							$lista=eliminarCausa($lista, $index);
						}
						if (!isset($grabar))
						pintarCausas($causas, $lista, true);
					}
				}
				if (!isset($grabar) or !$temporal)
				pintarObservaciones($wobs, 1, $wnrodoc, $wfuenCod, $wcco, false);

				break;

				case 3: //aca se mete cuando estoy procesando la forma de pago

				$temporal=consultarTemporal($wusuario, $wcaja, $wfuente, &$wempresa, &$wcarcca, &$wcarcfa, &$ref);
				if (!$temporal) //si no hay archivos en temporal para la fuente, construyo la lista de empresas
				{
					$wempresas=consultarResponsables($wempresa, $wbuscador);
					$temporal=false;
				}
				else //de lo contario no, porque se obliga a trabajar sobre la empresa que se habia empezado a generar el documento
				{
					$wempresas[1]=$wempresa;
					if ($temporal[0]['temfco']=='off')
					{
						$wcarcfa=false;
					}
					else
					{
						$wcarcca=false;
					}
				}
				consultarFuente($wfuenCod,&$wcarndb, &$wcarncr, &$wcarrec, &$wcarcca, &$wcarcfa, &$wcarfpa, &$wcarroc, &$wnrodoc);
				pintarParametros($fuentes, $wfuente, '', $wnrodoc, $wfecdoc, $wnomcco, $wnomcaj, $wempresas, $wcarrec, $wcarroc, 'manual', '');
				pintarBoton(1, false);
				calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);

				$obliga=consultarPago($pago);
				if ($valor!='' and $valor!=0)
				{
					$aviso=false;
					if ($valor<=$saldo)
					{

						IF ($obliga and ($origen=='' or $ubicacion=='' or $autorizacion==''))
						{
							pintarAlert3('DEBE DILIGENCIAR TODA LA INFORMACION SOLICITADA');
							$aviso=true;
						}
						else
						{
							if (isset ($fk))
							{
								$acu=count($fk)+1;
							}
							else
							{
								$acu=1;
							}

							if ($valor==$saldo)
							{
								$almacenar='on';
							}
							else
							{
								$almacenar='off';
								$aviso=true;
							}
							$fk[$acu][0]=$pago;
							$fk[$acu][1]=$anexo;
							$anexo='';
							$fk[$acu][2]=$origen;
							$origen='';
							$fk[$acu][3]=$ubicacion;
							$fk[$acu][4]=$autorizacion;
							$autorizacion='';
							$fk[$acu][5]=$destino;
							$fk[$acu][6]=$valor;
							$fk[$acu][7]=$saldo-$valor;
							$saldo=$saldo-$valor;
							$fk[$acu][8]=$obliga;
						}
					}
					else
					{
						pintarAlert3('EL VALOR DE LA FORMA DE PAGO NO DEBE SUPERAR EL SALDO RESTANTE');
						$aviso=true;
					}
					if ($aviso)
					{
						pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, '', '', '', '', '', '', false, false, '', '', 0, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, '', '');
						$wfpa=consultarPagos($pago);
						$origenes=consultarOrigenes($origen);
						$destinos=consultarDestinos($destino, $wfuenCod, $pago);
						if (isset ($fk))
						{
							pintarPagos($fk,$wfpa, $origenes, $destinos, $pago, $anexo, $origen, $destino, $ubicacion, $autorizacion,  $obliga, $saldo, $wobs, 1);
						}
						else
						{
							pintarPagos(false,$wfpa, $origenes, $destinos, $pago, $anexo, $origen, $destino, $ubicacion, $autorizacion,  $obliga, $saldo, $wobs, 1);
						}
						$almacenar='off';
					}
				}
				else
				{
					pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, '', '', '', '', '', '', false, false, '', '', 0, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, '', '');
					$wfpa=consultarPagos($pago);
					$origenes=consultarOrigenes($origen);
					$destinos=consultarDestinos($destino, $wfuenCod, $pago);
					if (isset ($fk))
					{
						pintarPagos($fk,$wfpa, $origenes, $destinos, $pago, $anexo, $origen, $destino, $ubicacion, $autorizacion,  $obliga, $saldo, $wobs, 1);
					}
					else
					{
						pintarPagos(false,$wfpa, $origenes, $destinos, $pago, $anexo, $origen, $destino, $ubicacion, $autorizacion,  $obliga, $saldo, $wobs, 1);
					}
					$almacenar='off';
				}
				break;

				case 4: //proceso de anulacion de un documento
				$permiso=consultarPermiso($fuen, $docu, $wcco);
				if ($permiso)
				{
					consultarFuente($fuen,&$wcarndb, &$wcarncr, &$wcarrec, &$wcarcca, &$wcarcfa, &$wcarfpa, &$wcarroc, &$wnrocon);
					$temporal=consultarDocumento($fuen, $docu, $wcco, &$wobs, &$estado, &$empresa, $wcarndb, &$wfecdoc, &$ref);
					calcularTotales($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);
					anularEncabezado($fuen, $docu, $wcco);

					for ($i=0;$i<count($temporal);$i++)
					{
						if ($temporal[$i]['temfco']=='on')
						{
							$exp=explode('-',$temporal[$i]['temcon']);
							if($ref=='')
							{
								realizarRedesgloce($fuen, $docu,  $exp[0], $temporal[$i]['temcco'], $temporal[$i]['temter'], ($temporal[$i]['temvco']*$temporal[$i]['temdco']), $wusuario, $wfecdoc, (string)date("H:i:s") );
								anularConcepto(($temporal[$i]['temvco']*$temporal[$i]['temdco']), $temporal[$i]['temnfa'], $temporal[$i]['temffa'], $exp[0], $temporal[$i]['temcco'], $temporal[$i]['temter'], $temporal[$i]['id']);
							}
							else
							{
								anularConcepto(($temporal[$i]['temvcf']*-1), $temporal[$i]['temnfa'], $temporal[$i]['temffa'], $exp[0], $temporal[$i]['temcco'], $temporal[$i]['temter'], $temporal[$i]['id']);
							}
							anularDetalle(false, $fuen, $docu, $wcco);
						}else
						{
							if (!$wcarroc)
							{
								$indi=0;

								if ($i==0)
								{
									$facturas[1]=$temporal[$i]['temnfa'];
									$fuentis[1]=$temporal[$i]['temffa'];
									DevolverSaldos($temporal[$i]['temnfa'], $temporal[$i]['temffa'], $wcarndb, $docu, $fuen);
									$tamano=1;
								}


								for ($j=1;$j<=$tamano;$j++)
								{
									if ($facturas[$j]==$temporal[$i]['temnfa'] and $fuentis[$j]==$temporal[$i]['temffa'])
									{
										$indi=1;
									}
								}

								if ($indi==0)
								{
									$tamano++;
									$facturas[$tamano]=$temporal[$i]['temnfa'];
									$fuentis[$tamano]=$temporal[$i]['temffa'];
									DevolverSaldos($temporal[$i]['temnfa'], $temporal[$i]['temffa'], $wcarndb, $docu,$fuen);
								}
							}
							anularDetalle($temporal[$i]['id'], $fuen, $docu, $wcco);
						}
						//cambio saldo en tabla 18
						if (!$wcarroc)
						{
							if($ref=='')
							{
								actualizarFactura(($temporal[$i]['temvcf']-$temporal[$i]['temvco']*$temporal[$i]['temdco']), $temporal[$i]['temffa'], $temporal[$i]['temnfa'], $wcarndb, $wcarrec, $wcarncr);
							}
							else
							{
								actualizarFactura(($temporal[$i]['temvco']), $temporal[$i]['temffa'], $temporal[$i]['temnfa'], $wcarndb, $wcarrec, $wcarncr);
							}
						}
					}
					if ($wcarrec)
					{
						//anulo tabla 22
						anularPagos($fuen, $docu, $wcco);
					}else
					{
						//anulo tabla 71
						anularCausas($fuen, $docu);
					}
					pintarAlert3('El documento ha sido anulado exitosamente');
				}else
				{
					pintarAlert3('El documento ya no puede ser anulado');
				}
				$wempresas=consultarResponsables($wempresa, '');
				pintarParametros($fuentes, '', '', '', $wfecdoc, $wnomcco, $wnomcaj, $wempresas, '', '', 'manual', '');
				pintarBoton(1, true);
				break;

				case 5: //se va a realizar un recibo para las facturas de un envio

				consultarFuente($wfuenCod,&$wcarndb, &$wcarncr, &$wcarrec, &$wcarcca, &$wcarcfa, &$wcarfpa, &$wcarroc, &$wnrodoc);

				if (isset ($temporal) and $temporal[0]['temenv']!=$wnumEnv)
				{
					unset ($temporal);
				}

				if (!isset ($temporal))
				{
					$temporal=consultarEnvio($wnumEnv, $wcco);
					$wempresas[1]=$temporal[0]['temcod'].'-'.$temporal[0]['temnit'].'-'.$temporal[0]['temnom'].'-'.$temporal[0]['temraz'];
				}
				else
				{
					$wnumEnv=$temporal[0]['temenv'];
					for ($i=0;$i<count($temporal);$i++)
					{
						if (!isset($temporal[$i]['chk']))
						{
							$temporal[$i]['temvco']=0;
							$temporal[$i]['temvcf']=0;
							$temporal[$i]['temcon']='';
							$temporal[$i]['temccc']='off';
							$temporal[$i]['chk']='';
						}
						else
						{
							$temporal[$i]['chk']='checked';
						}
						if ($temporal[$i]['temvco']=='')
						{
							$temporal[$i]['temvco']=0;
						}

						if ($temporal[$i]['temvcf']=='')
						{
							$temporal[$i]['temvcf']=0;
						}
						if ($temporal[$i]['temvco']==0 and $temporal[$i]['temvcf']==0)
						{
							$temporal[$i]['temcon']='';
							$temporal[$i]['temccc']='off';
							$temporal[$i]['chk']='';
						}

						if ($temporal[$i]['temcon']!='')
						{
							$pideCentro=preguntarCentro($wfuenCod, $temporal[$i]['temcon'], &$multi);
							if ($pideCentro and $temporal[$i]['temccc']=='off')
							{
								$temporal[$i]['temccc']='';
							}
							if ($pideCentro and $temporal[$i]['temccc']=='')
							{
								pintarAlert3('DEBE INGRESAR CENTROS DE COSTO VALIDOS PARA LOS CONCEPTOS QUE LO SOLICITEN');
								if (isset ($grabar))
								{
									unset ($grabar);
								}
							}else if ($pideCentro and $temporal[$i]['temccc']!='')
							{
								if (!validarCentro($temporal[$i]['temccc']))
								{
									pintarAlert3('DEBE INGRESAR CENTROS DE COSTO VALIDOS PARA LOS CONCEPTOS QUE LO SOLICITEN');
									if (isset ($grabar))
									{
										unset ($grabar);
									}
								}
							}
							else if (!$pideCentro)
							{
								$temporal[$i]['temccc']='off';
							}
						}
						else
						{
							$temporal[$i]['temccc']='off';
							$multi=1;
						}

						$temporal[$i]['temdco']=$multi;
						$suma=$temporal[$i]['temvcf']+($temporal[$i]['temvco']*$temporal[$i]['temdco']);

						if ($suma>$temporal[$i]['temsfa'])
						{
							pintarAlert3('EL VALOR A CANCELAR MAS EL VALOR DEL CONCEPTO NO DEBEN SUPERAR EL SALDO DE LA FACTURA');
							if (isset ($grabar))
							{
								unset ($grabar);
							}
						}

						if ($temporal[$i]['temvco']<0)
						{
							pintarAlert3('DEBE INGRESAR VALORES POSITIVOS PARA LOS CONCEPTOS');
							if (isset ($grabar))
							{
								unset ($grabar);
							}
						}

						if ($temporal[$i]['temcon']=='' and $temporal[$i]['temvco']!='' and $temporal[$i]['temvco']!=0)
						{
							pintarAlert3('HA INGRESADO VALORES DE CONCEPTO SIN SELECCIONAR EL CONCEPTO DE CARTERA');
							if (isset ($grabar))
							{
								unset ($grabar);
							}
						}
					}
					$wempresas[1]=$wempresa;
				}
				calcularTotales2($temporal, &$wtotvcafac, &$wtotvalcon, &$total);
				pintarParametros($fuentes, $wfuente, '', $wnrodoc, $wfecdoc, $wnomcco, $wnomcaj, $wempresas, $wcarrec, $wcarroc, 'auto', $wnumEnv);
				pintarBoton(1, false);

				if (!isset($grabar))
				{
					$conceptos=consultarConceptos('', $wfuenCod);
					pintarAutomatico($temporal, $wcarcca, $wtotvcafac, $wtotvalcon, $total, $conceptos);
					pintarObservaciones($wobs, 2, $wnrodoc, $wfuenCod, $wcco, false);
				}

				break;
			}

			if (isset ($grabar) and $bandera!=1 and $temporal)
			{
				if ($wcarfpa and !isset($almacenar))
				{
					if (isset($wnumEnv) and $wnumEnv!='')
					{
						for ($i=0;$i<count($temporal);$i++)
						{
							$exp=explode('-',$temporal[$i]['temnfa']);
							if ($temporal[$i]['temccc']=='off')
							{
								$temporal[$i]['temccc']='';
							}
							if (isset($temporal[$i]['chk']) and $temporal[$i]['chk']!='')
							{
								guardarTemporal(date('Y-m-d'), (string)date("H:i:s"), $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, $exp[0], $exp[1], $temporal[$i]['temvcf'], $temporal[$i]['temvfa'], $temporal[$i]['temsfa'], $temporal[$i]['temccc'],   'off', $temporal[$i]['temffa'], $temporal[$i]['temdco'], $wusuario, $temporal[$i]['temcon'], $temporal[$i]['temvco'], $wcarcca, $wcarcfa, $wcarrec, $wcarndb, $wcarncr, '');
							}
						}
					}

					pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, '', '', '', '', '', '', false, false, '', '', 0, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, '', '');
					$wfpa=consultarPagos(false);
					$origenes=consultarOrigenes(false);
					$destinos=consultarDestinos(false, $wfuenCod, false);
					if ($wcarroc)
					{
						pintarPagos(false,$wfpa, $origenes, $destinos, '', '', '', '', '', '', false, $wtotvalcon, $wobs, 1);
					}
					else
					{
						pintarPagos(false,$wfpa, $origenes, $destinos, '', '', '', '', '', '', false, $wtotvcafac, $wobs, 1);
					}

				}
				else if ((isset ($almacenar) and $almacenar=='on' ) or !$wcarfpa) //proceso de almacenamiento de las notas o recibos
				{
					incrementarFuente($wfuenCod);
					IF ($temporal[0]['temfco']!='on')//es con concetos de facturacion
					{
						$temporal=array_reverse($temporal);
					}
					grabarEncabezado($wfecdoc, (string)date("H:i:s"), $wfuenCod, $wnrodoc, $total, $wempCod, $wempNom, $wcaja, $wusuario, $wcco, $wobs);

					IF ($temporal[0]['temfco']=='on') //es con conceptos de facturacion
					{
						consultarPaciente($temporal[0]['temffa'], $temporal[0]['temnfa'], &$historia, &$numIng);
						if (!isset ($glosa))
						{
							$glosa='';
						}
						else if ($glosa!='')
						{
							$glo=explode('-', $glosa);
							$glosa=$glo[0].'-'.$glo[1];
						}
						grabarDetalle(date('Y-m-d'), (string)date("H:i:s"), $wfuenCod, $wnrodoc, $wusuario, $wcco, $temporal[0]['temnfa'], 0, '', 0, $temporal[0]['temffa'], $temporal[0]['temsfa'], $historia, $numIng, $temporal[0]['temccc'], $glosa);
					}

					for ($i=0;$i<count($temporal);$i++)
					{
						if(!$wcarroc and $temporal[0]['temfco']=='off')
						{
							consultarPaciente($temporal[$i]['temffa'], $temporal[$i]['temnfa'], &$historia, &$numIng);
						}
						else
						{
							$historia='';
							$numIng='';
						}

						IF ($temporal[0]['temfco']=='off')//no es con conceptos de facturacion
						{
							if ($wcarroc)
							{
								$temporal[$i]['temnfa']='';
								$temporal[$i]['temsfa']='';
							}
							grabarDetalle(date('Y-m-d'), (string)date("H:i:s"), $wfuenCod, $wnrodoc, $wusuario, $wcco, $temporal[$i]['temnfa'], $temporal[$i]['temvcf'], $temporal[$i]['temcon'], $temporal[$i]['temvco'], $temporal[$i]['temffa'], $temporal[$i]['temsfa'], $historia, $numIng, $temporal[$i]['temccc'], $temporal[$i]['temglo']);

							//preparo los acumulados o descontables para cada factura
							$indi=0;
							if ($i==0)
							{
								$facturas[1]=$temporal[$i]['temnfa'];
								$fuentis[1]=$temporal[$i]['temffa'];
								$tamano=1;
								$valor = array(1=>0);
							}

							for ($j=1;$j<=$tamano;$j++)
							{
								if ($facturas[$j]==$temporal[$i]['temnfa'] and $fuentis[$j]==$temporal[$i]['temffa'])
								{
									$valor[$j]=$valor[$j]+$temporal[$i]['temvcf']-($temporal[$i]['temvco']*$temporal[$i]['temdco']);
									$indi=1;
								}
							}

							if ($indi==0)
							{
								$tamano++;
								$facturas[$tamano]=$temporal[$i]['temnfa'];
								$fuentis[$tamano]=$temporal[$i]['temffa'];
								$valor[$tamano]=$temporal[$i]['temvcf']-($temporal[$i]['temvco']*$temporal[$i]['temdco']);
							}
						}
						else
						{
							$conexp=explode('-',$temporal[$i]['temcon']);
							$conexp= $conexp[0];
							if($ref=='')
							{
								realizarRedesgloce($temporal[$i]['temffa'], $temporal[$i]['temnfa'], $conexp, $temporal[$i]['temcco'], $temporal[$i]['temter'], (-$temporal[$i]['temvco']*$temporal[$i]['temdco']), $wusuario, date('Y-m-d'), (string)date("H:i:s"));
							}
							grabarConcepto($temporal[$i]['temffa'], $temporal[$i]['temnfa'], $temporal[$i]['temcco'], $conexp, $temporal[$i]['temter'], (-$temporal[$i]['temvco']*$temporal[$i]['temdco']), date('Y-m-d'), (string)date("H:i:s"), $wfuenCod, $wnrodoc, $wcco, $wusuario, $ref );
							/*}
							else
							{
							grabarConcepto($temporal[$i]['temffa'], $temporal[$i]['temnfa'], $temporal[$i]['temcco'], $conexp, $temporal[$i]['temter'], ($temporal[$i]['temvcf']), date('Y-m-d'), (string)date("H:i:s"), $wfuenCod, $wnrodoc, $wcco, $wusuario, $ref );
							}*/

						}

						//actualizamos el valor de la factura
						if (!$wcarroc)
						{
							if($ref=='')
							{
								actualizarFactura(($temporal[$i]['temvcf']-$temporal[$i]['temvco']*$temporal[$i]['temdco'])*-1, $temporal[$i]['temffa'], $temporal[$i]['temnfa'], $wcarndb, $wcarrec, $wcarncr);
							}
							else
							{
								actualizarFactura(($temporal[$i]['temvco']*-1), $temporal[$i]['temffa'], $temporal[$i]['temnfa'], $wcarndb, $wcarrec, $wcarncr);
							}
						}

						//borramos registro de l 45
						borrarRegistro($temporal[$i]['id']);
					}

					if (!$wcarroc and $temporal[0]['temfco']!='on')
					{
						for ($i=1;$i<=$tamano;$i++)
						{
							CuadrarSaldos($facturas[$i], $fuentis[$i], $valor[$i], $wcarndb, $wnrodoc, $wfuenCod, date('Y-m-d'), (string)date("H:i:s"), $wusuario );
						}
					}

					if (isset($lista[0]))
					{
						grabarCausas($lista, date('Y-m-d'), (string)date("H:i:s"), $wfuenCod, $wnrodoc );
					}

					if($wcarfpa)
					{
						grabarPagos($fk, date('Y-m-d'), (string)date("H:i:s"), $wfuenCod, $wnrodoc, $wcco, $wcaja,  $wusuario);
					}

					$temporal1=consultarDocumento($wfuenCod, $wnrodoc, $wcco, $wobs, &$estado, &$empresa, $wcarndb, &$wfecdoc, &$ref);
					calcularTotales($temporal1, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, &$wtotvcafac, &$wtotvalcon, &$total, $ref);

					if ($temporal[0]['temfco']=='off')
					{
						if ($wcarrec)
						{
							pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, '', '','','', '',  '',false, false, '', '', 0, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
						}
						else
						{
							$exp=explode('-',$temporal[0]['temnfa']);
							pintarCartera($temporal, $wcarndb, $wcarncr, $wcarrec, $wcarcca, $wcarcfa, $wcarfpa, $wcarroc, $wtotvcafac, $wtotvalcon, $total, $temporal[0]['temffa'], $exp[0],$exp[1],$temporal[0]['temvfa'], $temporal[0]['temsfa'],  '',$conceptos, false, '', '', 0, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, false, '');
						}
					}
					if ($wcarcfa and !$wcarrec)
					{
						$exp=explode('-',$temporal[0]['temnfa']);
						if(!isset($ref))
						{
							$ref='';
						}
						pintarFacturacion($temporal, $wcarndb, $wcarncr, $wtotvalcon, $temporal[0]['temffa'], $exp[0], $exp[1], $temporal[0]['temvfa'], $temporal[0]['temsfa'], '', '', '', '', '', 0, $wfuente, $wnrodoc, $wempresa, $wbuscador, $wcco, $wnomcco, $wfecdoc, $wcarcca, $temporal[0]['temglo'], $ref);
					}
					if ($wcarncr or $wcarndb) //las notas debito y credito utilizan causas introducidas por el usuario
					{
						if (isset($lista[0]))
						{
							pintarCausas(false, $lista, false);
						}
					}
					if ($wcarfpa)
					{
						if ($wcarroc)
						{
							$fk=consultarFormas($wfuenCod, $wnrodoc, $wcco, $wtotvalcon, 'on');
						}
						else
						{
							$fk=consultarFormas($wfuenCod, $wnrodoc, $wcco, $wtotvcafac, 'on');
						}

						pintarPagos($fk,'', '', '', '', '', '', '', '', '',  '', '', '', 0);
					}
					pintarObservaciones($wobs, 0, $wnrodoc, $wfuenCod, $wcco, true);
				}
			}
		}
	}
	else if (!$caja)
	{
		pintarAlert1('EL USUARIO NO TIENE UNA CAJA ASIGNADA PARA LA REALIZACION DE RECIBOS O NOTAS');
	}
	
	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
}
?>
