<html>
<head>
  <title>MATRIX Ayuda del Programa de Inventarios</title>

</head>
<body BGCOLOR="dddddd" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
	echo "<A NAME='Arriba'><table border=0 align=center>";
	echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td><td align=center><font size=2>Powered by : MATRIX</font></td></tr></table><br><br>";
?>
<H1>TABLA DE CONENIDO</H1>
<ol>
	<li><A HREF="#m1">Conceptos de Inventario</a>
	<ol>
		<li><A HREF="#m11">Conceptos de Compra</a>
		<ol>
			<li><A HREF="#m111">900-Orden de Compra</a>
			<li><A HREF="#m112">001-Registro entrada de compras</a>
			<li><A HREF="#m113">101-Registro de devol. al proveedor</a>
			<li><A HREF="#m114">011-Entrada por menor vlr compras</a>
			<li><A HREF="#m115">111-Salida por mayor vlr en compras</a>
		</ol>
		<li><A HREF="#m12">Conceptos de Bonificaci�n</a>
		<ol>
			<li><A HREF="#m121">007-Bonificaciones</a>
		</ol>
		<li><A HREF="#m13">Conceptos de Mercanc�a en Consignaci�n</a>
		<ol>
			<li><A HREF="#m131">009-Consignaci�n recibida</a>
			<li><A HREF="#m132">119-Devol. Mcia. consignaci�n</a>
		</ol>
		<li><A HREF="#m14">Conceptos de Prestamo</a>
		<ol>
			<li><A HREF="#m141">008-Pr�stamo recibido de terceros</a>
			<li><A HREF="#m142">108-Devol. pr�stamo recibido de 3ro.</a>
			<li><A HREF="#m143">129-Pr�stamo otorgado</a>
			<li><A HREF="#m144">029-Reintegro de pr�stamo otorgado</a>
		</ol>
		<li><A HREF="#m15">Conceptos de Translado</a>
		<ol>
			<li><A HREF="#m151">002-Traslado de insumos</a>
		</ol>
		<li><A HREF="#m16">Conceptos de Venta Comercial</a>
		<ol>
			<li><A HREF="#m161">802-Ventas de farmacia</a>
			<li><A HREF="#m162">801-Devol. Ventas de farmacia</a>
		</ol>
		<li><A HREF="#m17">Conceptos de Consumo Interno</a>
		<ol>
			<li><A HREF="#m171">105-Consumo interno</a>
			<li><A HREF="#m172">005-Reintegro de consumo interno</a>
			<li><A HREF="#m173">110-Aver�as</a>
		</ol>
		<li><A HREF="#m18">Conceptos de Ajuste</a>
		<ol>
			<li><A HREF="#m181">006-Entrada por ajuste en Cantidad</a>
			<li><A HREF="#m182">106-Salida por ajuste en Cantidad</a>
			<li><A HREF="#m183">013-Entrada por ajuste en valor</a>
			<li><A HREF="#m184">113-Salida por ajuste en valor</a>
			<li><A HREF="#m185">020-Entrada por inventario f�sico</a>
			<li><A HREF="#m186">120-Salida por inventario f�sico</a>
		</ol>
		<li><A HREF="#m19">Conceptos de Importaci�n de Insumos</a>
		<ol>
			<li><A HREF="#m191">017-Reg. importaci�n de insumos</a>
		</ol>
	</ol><br>
	<li><A HREF="#m2">Mensajes de Error</a>
	<ol>
		<li><A HREF="#m21">Errores Codificados</a>
		<ol>
			<li><A HREF="#m2101">Error Nro. 1</a>
			<li><A HREF="#m2102">Error Nro. 2</a>
			<li><A HREF="#m2103">Error Nro. 3</a>
			<li><A HREF="#m2104">Error Nro. 4</a>
			<li><A HREF="#m2105">Error Nro. 5</a>
			<li><A HREF="#m2106">Error Nro. 6</a>
			<li><A HREF="#m2107">Error Nro. 7</a>
			<li><A HREF="#m2108">Error Nro. 8</a>
			<li><A HREF="#m2109">Error Nro. 9</a>
			<li><A HREF="#m2110">Error Nro.10</a>
			<li><A HREF="#m2111">Error Nro. 11</a>
			<li><A HREF="#m2112">Error Nro. 12</a>
			<li><A HREF="#m2113">Error Nro. 13</a>
			<li><A HREF="#m2114">Error Nro. 14</a>
			<li><A HREF="#m2115">Error Nro. 15</a>
			<li><A HREF="#m2116">Error Nro. 16</a>
			<li><A HREF="#m2117">Error Nro. 17</a>
			<li><A HREF="#m2118">Error Nro. 18</a>
			<li><A HREF="#m2119">Error Nro. 19</a>
			<li><A HREF="#m2120">Error Nro. 20</a>
			<li><A HREF="#m2121">Error Nro. 21</a>
			<li><A HREF="#m2122">Error Nro. 22</a>
			<li><A HREF="#m2123">Error Nro. 23</a>
			<li><A HREF="#m2124">Error Nro. 24</a>
			<li><A HREF="#m2125">Error Nro. 25</a>
			<li><A HREF="#m2126">Error Nro. 26</a>
			<li><A HREF="#m2127">Error Nro. 27</a>
			<li><A HREF="#m2128">Error Nro. 28</a>
			<li><A HREF="#m2129">Error Nro. 29</a>
		</ol>
		<li><A HREF="#m22">Errores NO Codificados</a>
		<ol>
		</ol>
</ol>
<br><br>
<b><A NAME="m1"><A HREF='#Arriba'>CONCEPTOS DE INVENTARIO</a></b>
<p>
		Los conceptos de inventario son el coraz�n del aplicativo. Este maestro es el que determina que acci�n debe realizar el aplicativo cuando se efect�a un registro. <br>
</p>
<b><A NAME="m11"><A HREF='#Arriba'>Conceptos de Compra</a></b>
<p>
		Por medio de estos conceptos se registran las transacciones con terceros, y que originan como contraprestaci�n un pago.<br>
</p>
<b><A NAME="m111"><A HREF='#Arriba'>900-Orden de Compra</a></b>
<p>
		Este documento representa la solicitud (escrita) a un proveedor, por determinados art�culos a un precio convenido. Todos los art�culos comprados deber�n acompa�arse <br>
		de la orden de compra. Cuando los art�culos solicitados en varias �rdenes sean facturados por el proveedor en una sola factura, se podr� diligenciar una nueva orden <br>
		concatenando las �rdenes en que figuran los art�culos pedidos. Si el proveedor factura un art�culo que no se registro en la Orden de Compra o a un valor diferente, se debe <br>
		adicionar a la orden de compra para proceder a registrarla, previa autorizaci�n.<br><br>
		<ol>
			<li>Este documento acepta modificaci�n
			<li>Es requisito para registrar la entrada de la compra.
			<li>En el campo -Centro de Costos Origen- se indica la sede que realiza el pedido.
			<li>En el campo Proveedor se debe se�alar el tercero a quien se le solicita los insumos
			<li>En el campo Criterio se digita el art�culo solicitado.
			<li>Se digita la Cantidad solicitada y el Valor Total de los insumos antes de IVA
			<li>El sistema informa el Valor unitario de la �ltima compra, el cual permite hacer un an�lisis inmediato de precios.
			<li>No altera los inventarios ni su costo promedio, el documento es netamente informativo.
			<li>Concatenar: Este proceso se realiza diligenciando en el campo Doc. Anexo los n�meros de las �rdenes de compra que se presentaron en una sola entrega separados 
					por dos puntos (:). 
		</ol>
</p>
<b><A NAME="m112"><A HREF='#Arriba'>001-Registro entrada de compras</a></b>
<p>
		Este opci�n permite ingresar los art�culos adquiridos que entran a formar parte del inventario, el documento soporte para su registro es la Factura.<br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>El registro de entrada se graba con base en una orden de compra previa, para esto se digita en el campo -Documento Anexo- el n�mero interno de la 
					Orden de Compra.  
			<li>En el campo Doc. Soporte debe digitarse el n�mero de la Factura soporte de la compra.
			<li>El sistema valida el registro de entrada con la orden de compra los siguientes campos:
			<li>El centro de costos origen, es decir, el registro de entrada se debe realizar en la sede que efectu� el pedido
			<li>Proveedor, por lo tanto, debe ser a quien se le solicit� el pedido.
			<li>Los art�culos solicitados, la cantidad y el valor unitario de los mismos. Por consiguiente no permite registrar art�culos o cantidades adicionales o a 
					costos unitarios diferentes, no obstante, se permite el registro de entrada de compras parciales hasta completar la cantidad pedida pero sin variar
					el costro unitario. 
			<li>Se digita la cantidad recibida y el valor total de los insumos antes de IVA, teniendo en cuenta las validaciones que se realizan en estos campos.
			<li>Se debe digitar para los art�culos que se tenga establecido, la fecha de vencimiento y el n�mero del lote que posean los mismos.
			<li>Una vez se graba el documento, se modifica la cantidad disponible en los inventarios y el costo promedio
			<li>El porcentaje de los campos IVA y de Retenci�n en la Fuente son �nicamente informativos
		</ol>
</p>
<b><A NAME="m113"><A HREF='#Arriba'>101-Registro de devol. al proveedor</a></b>
<p>
		Esta opci�n permite registrar los art�culos a ser devueltos al proveedor, estos se retiran del inventario al costo al que ingresaron previamente.<br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>La devoluci�n se asocia a una compra realizada, en consecuencia se digita en el campo -Documento Anexo- el n�mero interno del Registro de 
					Entrada de Compras mediante el cual se ingreso la Factura.
			<li>En el campo Doc. Soporte debe digitarse el n�mero del documento que soporte la devoluci�n.
			<li>El sistema valida el registro de devoluci�n con el registro de entrada de compras los siguientes campos:
			<li>El centro de costos origen, es decir, la devoluci�n se debe efectuar desde la sede que realiz� la entrada 
			<li>Proveedor, por lo tanto, debe ser a quien se le adquiri� los insumos.
			<li>Los art�culos comprados, la cantidad y el valor unitario de los mismos. Por consiguiente no permite devolver art�culos diferentes o cantidades 
					superiores a las adquiridas o a costos unitarios diferentes, no obstante, se permite el registro de devoluciones parciales hasta completar la 
					cantidad comprada pero sin variar el costro unitario.
			<li>Se digita la cantidad a devolver y el valor total de los insumos antes de IVA, teniendo en cuenta las validaciones que se realizan en estos campos
			<li>Una vez se graba el documento, se modifica la cantidad disponible en los inventarios y el costo promedio
		</ol>
</p>
<b><A NAME="m114"><A HREF='#Arriba'>011-Entrada por menor vlr compras</a></b>
<p>
		Esta opci�n permite corregir el costo de los insumos adquiridos, aumentando el valor de la compra inicial. El soporte del registro es la nota d�bito generada <br>
		por el proveedor.<br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado.
			<li>El centro de costos origen se debe digitar la sede que realiz� la entrada. 
			<li>En el campo Proveedor se debe se�alar el tercero a quien se le adquiri� los insumos
			<li>En el campo Doc. soporte debe digitarse el n�mero de la nota soporte del mayor valor.
			<li>En el campo Criterio se digita el art�culo al que se le aumenta el costo.
			<li>En el campo cantidad se digita cero (0) y en el campo valor total se digita el monto total a aumentar antes de IVA.
			<li>(Si la cantidad no es digitada o es diferente a cero, el sistema autom�ticamente asume cero)
			<li>Una vez se graba el documento, se modifica el costo promedio pero no las cantidades en existencia.
		</ol>
</p>
<b><A NAME="m115"><A HREF='#Arriba'>111-Salida por mayor vlr en compras</a></b>
<p>
		Esta opci�n permite corregir el costo de los insumos adquiridos, disminuyendo el valor de la compra inicial. El soporte del registro es la nota cr�dito generada <br>
		por el proveedor. <br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>El centro de costos origen se debe digitar la sede que realiz� la entrada. 
			<li>En el campo Proveedor se debe se�alar el tercero a quien se le adquiri� los insumos.
			<li>En el campo Doc. soporte debe digitarse el n�mero de la nota soporte del menor valor.
			<li>En el campo Criterio se digita el art�culo al que se le disminuye el costo.
			<li>En el campo cantidad se digita cero (0) y en el campo Valor Total se digita el monto total a disminuir antes de IVA.
			<li>(Si la cantidad no es digitada o es diferente a cero, el sistema autom�ticamente asume cero)
			<li>Una vez se graba el documento, se modifica el costo promedio pero no las cantidades en existencia.
		</ol>
</p>
<b><A NAME="m12"><A HREF='#Arriba'>Conceptos de Bonificaci�n</a></b>
<p>
		Este concepto permite registrar la entrada de los art�culos entregados como bonificaci�n. Se registran a un peso ($1) por unidad.<br>
</p>
<b><A NAME="m121"><A HREF='#Arriba'>007-Bonificaciones</a></b>
<p>
		Se utiliza cuando el proveedor env�a en car�cter de bonificaci�n insumos adicionales.<br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>Se requiere digitar en el campo centro de costos origen, la sede donde se recibi� la bonificaci�n y el campo Proveedor, el tercero de quien se 
					reciben los art�culos.
			<li>En el campo Doc. soporte se digita el n�mero del documento externo soporte de la bonificaci�n.
			<li>En el campo Criterio se digita el c�digo del art�culo entregado en bonificaci�n.
			<li>En el campo cantidad se digita el n�mero de art�culos recibidos y en el campo valor total se digita un peso $1 por unidad recibida.
			<li>Una vez se graba el documento, se modifica la cantidad disponible en los inventarios y el costo promedio.
		</ol>
</p>
<b><A NAME="m13"><A HREF='#Arriba'>Conceptos de Mercanc�a en Consignaci�n</a></b>
<p>
		Por medio de estos se registra la mercanc�a en consignaci�n <br> <br> 
</p>
<b><A NAME="m131"><A HREF='#Arriba'>009-Consignaci�n recibida</a></b>
<p>
		Este opci�n permite ingresar los art�culos entregados como mercanc�a en consignaci�n que entran a formar parte del inventario, el documento soporte <br> 
		para su registro es la Remisi�n.<br><br> 
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>No requiere una orden de compra previa.
			<li>Se requiere digitar el centro de costos origen donde se recibe la mercanc�a y el campo Proveedor el tercero de quien se reciben los art�culos.
			<li>En el campo Doc. soporte debe digitarse el n�mero de la remisi�n soporte de la consignaci�n.
			<li>En el campo Criterio se digita el c�digo del art�culo recibido en consignaci�n
			<li>Se digita en el campo cantidad el n�mero de art�culos recibidos y en el campo valor total el costo al que ser�n facturados los insumos antes de IVA
			<li>Una vez se graba el documento, se modifica la cantidad disponible en los inventarios y el costo promedio.
		</ol>
</p>
<b><A NAME="m132"><A HREF='#Arriba'>119-Devol. Mcia. consignaci�n</a></b>
<p>
		Mediante este concepto se realiza la devoluci�n de los art�culos que ingresaron bajo la modalidad de mercanc�a en consignaci�n.<br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>La devoluci�n se asocia a una consignaci�n recibida, en consecuencia se digita en el campo -Documento Anexo- el n�mero interno del Registro de la 
					Consignaci�n Recibida. 
			<li>En el campo Doc. Soporte debe digitarse el n�mero del documento soporte de la devoluci�n.
			<li>El sistema valida los siguientes campos entre el registro de devoluci�n con el registro de la consignaci�n recibida:
			<li>El centro de costos origen, es decir, la devoluci�n se debe efectuar desde la sede que recibi� la mercanc�a en consignaci�n. 
			<li>Proveedor, debe ser el tercero que envi� la mercanc�a.
			<li>Los art�culos entregados en consignaci�n, la cantidad y el valor unitario de los mismos. Por consiguiente no permite devolver art�culos diferentes o 
					cantidades superiores a las consignadas o a costos unitarios diferentes, no obstante, se permite el registro de devoluciones parciales hasta completar 
					la cantidad recibida pero sin variar el costro unitario.
			<li>Se digita la cantidad a devolver y el valor total de los insumos antes de IVA, teniendo en cuenta las validaciones que se realizan en estos campos
			<li>Una vez se graba el documento, se modifica la cantidad disponible en los inventarios y el costo promedio.
		</ol>
</p>
</p>
<b><A NAME="m14"><A HREF='#Arriba'>Conceptos de Prestamo</a></b>
<p>
		Estos conceptos permiten realizar transacciones con otras instituciones del sector salud � intermediaci�n con proveedores. <br>
</p>
<b><A NAME="m141"><A HREF='#Arriba'>008-Pr�stamo recibido de terceros</a></b>
<p>
		Opci�n que permite ingresar los art�culos recibidos de terceros en calidad de pr�stamo, previa solicitud de la empresa.<br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>No requiere documento anexo.
			<li>En el campo Doc. soporte debe digitarse el n�mero del documento generado por el tercero donde conste el env�o de los art�culos en calidad de pr�stamo.
			<li>Se requiere digitar el centro de costos origen donde se recibe la mercanc�a y el campo Proveedor de quien se reciben los art�culos
			<li>En el campo Criterio se digita el c�digo del art�culo recibido en pr�stamo.
			<li>Se digita la cantidad recibida y en el campo de valor total se digita cero (0) dado que el sistema autom�ticamente calcula el costo de entrada 
					al valor promedio del momento.
			<li>(Si el valor no es digitado o se digita una cantidad diferente de cero, el sistema autom�ticamente coloca el campo en cero)
			<li>Una vez se graba el documento, se modifica la cantidad disponible en los inventarios, conservando el costo promedio.
		</ol>
</p>
<b><A NAME="m142"><A HREF='#Arriba'>108-Devol. pr�stamo recibido de 3ro.</a></b>
<p>
		Opci�n que permite registrar la cancelaci�n de pr�stamos a terceros. El comprobante es impreso y enviado con los art�culos devueltos para ser firmado <br>
		en constancia de recibido.<br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>La devoluci�n se asocia a un pr�stamo recibido previamente, en consecuencia se digita en el campo -Documento Anexo- el n�mero interno del registro del Pr�stamo Recibido de Tercero.
			<li>El sistema valida los siguientes campos entre el registro de devoluci�n con el registro de la consignaci�n recibida:
			<li>El centro de costos origen, es decir, la devoluci�n se debe efectuar desde la sede que realiz� la entrada.
			<li>Proveedor, debe ser el tercero que realiz� el pr�stamo de los insumos.
			<li>Los art�culos recibidos en pr�stamo, la cantidad y el valor unitario de los mismos. Por consiguiente no permite devolver art�culos diferentes o cantidades superiores a las 
				prestadas o a costos unitarios diferentes, no obstante, se permite el registro de devoluciones parciales hasta completar la cantidad recibida pero sin variar el costo unitario.
			<li>Se digita la cantidad a devolver y el valor total de los insumos antes de IVA, teniendo en cuenta las validaciones de estos campos.
			<li>Una vez se graba el documento, se modifica la cantidad disponible en los inventarios y el costo promedio, el cual se recalcula por la diferencia ocasionada entre: el costo promedio 
				al que se devuelve (igual al costo que se recibi�) y el costo promedio en que se encuentra al momento de la devoluci�n.
			<li>Si con la devoluci�n, el inventario queda sin existencias y el costo promedio es diferente al valor que se debe devolver, se debe realizar un ajuste mediante los 
				conceptos 013 y 113
		</ol>
</p>
<b><A NAME ="m143"><A HREF='#Arriba'>129-Pr�stamo otorgado</a></b>
<p>
		Opci�n que permite ingresar los art�culos entregados a terceros en calidad de pr�stamo.El pr�stamo se basa en una solicitud por parte de un <br>
		tercero, previa aprobaci�n por parte de la administraci�n. <br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>No requiere documento anexo.
			<li>Se requiere digitar el centro de costos origen de donde se va a enviar la mercanc�a y el campo Proveedor con el nombre del tercero a quien se remiten.
			<li>En el campo Criterio se digita el c�digo del art�culo prestado.
			<li>Se digita la cantidad a prestar y en el campo de valor total se digita cero (0) dado que el sistema autom�ticamente calcula el costo de salida al valor promedio del momento.
			<li>(Si el valor no es digitado o se digita una cantidad diferente de cero, el sistema autom�ticamente coloca el campo en cero)
			<li>Una vez se graba el documento, se modifica la cantidad disponible en los inventarios, conservando el costo promedio.
		</ol>
</p>

<b><A NAME ="m144"><A HREF='#Arriba'>029-Reintegro de pr�stamo otorgado</a></b>
<p>
		Opci�n que permite registrar la cancelaci�n de pr�stamos a terceros.<br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>La devoluci�n se asocia a un pr�stamo concedido, en consecuencia se digita en el campo -Documento Anexo- el n�mero interno del registro del Pr�stamo Otorgado.
			<li>En el campo Doc. soporte debe digitarse el n�mero del documento soporte del reintegro del pr�stamo.
			<li>El sistema valida los siguientes campos entre el registro de Reintegro del Pr�stamo con el registro del Pr�stamo Otorgado:
			<li>El centro de costos origen, es decir, el reintegro se debe efectuar a la sede que realiz� el pr�stamo
			<li>Proveedor, quien cancela el pr�stamo de los insumos.
			<li>Los art�culos entregados en pr�stamo, la cantidad y el valor unitario de los mismos. Por consiguiente no permite recibir art�culos diferentes o cantidades superiores
					a las prestadas o a costos unitarios diferentes, no obstante, se permite el registro de reintegros parciales hasta completar la cantidad prestada pero sin variar
					el costo unitario.
			<li>Se digita la cantidad reintegrada y el valor total de los insumos antes de IVA, teniendo en cuenta las validaciones que se realizan en estos campos
			<li>Una vez se graba el documento, se modifica la cantidad disponible en los inventarios y el costo promedio 
		</ol>
</p>
<b><A NAME ="m15"><A HREF='#Arriba'>Conceptos de Translado</a></b>
<p>
		Por medio de este se realizan salidas desde una sede hacia otra, moviendo el inventario en ambas.<br>
</p>
<b><A NAME ="m151"><A HREF='#Arriba'>002-Traslado de insumos</a></b>
<p>
		Concepto que permite realizar traslados de una sede a otra, moviendo los inventarios en ambas partes.<br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>No requiere documento anexo ni Proveedor 
			<li>Se requiere digitar en el campo centro de costos origen, la sede que env�a los art�culos y en el campo centro de costos destino quien recibe la mercanc�a 
			<li>En el campo Criterio se digita el c�digo del art�culo a trasladar.
			<li>Se digita la cantidad a ser despachada y en el campo de valor total se digita cero (0) dado que el sistema autom�ticamente calcula el costo de salida al valor promedio del momento.
			<li>(Si el valor no es digitado, el sistema autom�ticamente coloca el campo en cero)
			<li>Una vez se graba el documento para la sede que env�a se modifica la cantidad disponible en los inventarios, conservando el costo promedio. Para la sede que recibe se modifica la c
					antidad disponible en los inventarios y el costo promedio.
			<li>Considerar en la devoluci�n de un traslado, que el valor a devolver es el promedio que se tenga en la sede que devuelve, como el costo promedio de este puede ser diferente genera 
					un ajuste.
		</ol>
</p>

<b><A NAME ="m16"><A HREF='#Arriba'>Conceptos de Venta Comercial</a></b>
<p>
		Por medio de estos se realizan ventas y devoluciones en la farmacia comercial.<br>
</p>
<b><A NAME ="m161"><A HREF='#Arriba'>802-Ventas de farmacia</a></b>
<p>
		Este concepto se mueve automaticamante por el programa de Ventas.<br>
</p>
<b><A NAME ="m162"><A HREF='#Arriba'>801-Devol. Ventas de farmacia</a></b>
<p>
		Mediante este concepto se realiza la devoluci�n de los art�culos que salieron bajo la modalidad de Venta Comercial.<br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>La devoluci�n se asocia a una venta realizada, en consecuencia se digita en el campo -Documento Anexo- el n�mero interno del registro de la venta.
			<li>En el campo Doc. soporte debe digitarse el n�mero del documento soporte de la venta.
			<li>El sistema valida los siguientes campos entre el registro de Reintegro de la venta con el registro de la venta realizada:
			<li>El centro de costos origen, es decir, el reintegro se debe efectuar a la sede que realiz� la venta.
			<li>Los art�culos entregados en la venta, la cantidad y el valor unitario de los mismos. Por consiguiente no permite recibir art�culos diferentes o cantidades superiores a las 
					vendidas o a costos unitarios diferentes, no obstante, se permite el registro de reintegros parciales hasta completar la cantidad vendida pero 
					sin variar el costo unitario.
			<li>Se digita la cantidad reintegrada y el valor total de los insumos antes de IVA, teniendo en cuenta las validaciones que se realizan en estos campos
			<li>Una vez se graba el documento, se modifica la cantidad disponible en los inventarios y el costo promedio 
		</ol>
</p>
<b><A NAME ="m17"><A HREF='#Arriba'>Conceptos de Consumo Interno</a></b>
<p>
		Por medio de estos se realizan cargos y devoluciones de insumos a las dependencias, originando un costo � gasto. <br>
</p>
<b><A NAME ="m171"><A HREF='#Arriba'>105-Consumo interno</a></b>
<p>
		Concepto que permite realizar cargos de insumos a las dependencias originando costos o gastos, moviendo los inventarios s�lo en la sede que env�a los art�culos <br>
		para el consumo.<br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>No requiere digitar documento anexo ni Proveedor
			<li>Se requiere digitar en el campo centro de costos origen, la sede que env�a los art�culos y en el campo centro de costos destino quien recibe la mercanc�a para su consumo.
			<li>En el campo Criterio se digita el c�digo del art�culo a utilizar.
			<li>Se digita la cantidad a ser despachada y en el campo de valor total se digita cero (0) dado que el sistema autom�ticamente calcula el costo de salida al valor promedio del momento.
			<li>(Si el valor no es digitado o se digita una cantidad diferente de cero, el sistema autom�ticamente coloca el campo en cero)
			<li>Una vez se graba el documento para la sede que env�a, se modifica la cantidad disponible en los inventarios, conservando el costo promedio. Sin embargo, la dependencia que
					recibe no presenta movimiento en los inventarios.
		</ol>
</p>
<b><A NAME ="m172"><A HREF='#Arriba'>005-Reintegro de consumo interno</a></b>
<p>
		Permite el reintegro de art�culos entregado a las dependencias para su consumo, moviendo los inventarios s�lo en la sede que recibe los art�culos.<br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado
			<li>La devoluci�n se asocia a un consumo realizado, en consecuencia se digita en el campo -Documento Anexo- el n�mero interno del registro del Consumo Interno.
			<li>El sistema valida los siguientes campos entre el registro de Reintegro del Consumo con el registro del Consumo Interno:
			<li>Centro de costos origen, debe ser la sede que despach� los art�culos y a donde van a ser reintegrados
			<li>Centro de costos destino, quien devuelve el consumo
			<li>Los art�culos entregados para el consumo y la cantidad. Por consiguiente no permite reintegrar art�culos diferentes o cantidades superiores a las entregadas, no obstante, 
					se permite el registro de reintegros parciales hasta completar la cantidad prestada.
			<li>Se digita la cantidad reintegrada y en el campo valor total se digita cero (0) dado que el sistema autom�ticamente calcula el costo del reintegro al valor promedio del momento.
			<li>(Si el valor no es digitado o se digita una cantidad diferente de cero, el sistema autom�ticamente coloca el campo en cero)
			<li>Una vez se graba el documento para la sede que recibe, se modifica la cantidad disponible en los inventarios, conservando el costo promedio. Sin embargo, la dependencia que 
					env�a no presenta movimiento en los inventarios.
		</ol>
</p>
<b><A NAME ="m173"><A HREF='#Arriba'>110-Aver�as</a></b>
<p>
		Permite disminuir las cantidades de los art�culos en el inventario cuando se presentan aver�as por manipulaci�n. Su uso es restringido <br><br>
		<ol>
			<li>No se requiere Documento anexo ni Proveedor
			<li>Se digita en el campo centro de costos origen la sede donde se realiza el ajuste.
			<li>En el campo Criterio se digita el c�digo del art�culo averiado
			<li>Se digita la cantidad de art�culos averiados y en el campo de valor total se digita cero (0) dado que el sistema autom�ticamente calcula el costo del ajuste, 
					el cual obedece al valor promedio del momento.
			<li>(Si el valor no es digitado o se digita una cantidad diferente de cero, el sistema autom�ticamente coloca el campo en cero)
			<li>Una vez se graba el documento, se disminuye la cantidad disponible en los inventarios, conservando el costo promedio. 
		</ol>
</p>
<b><A NAME ="m18"><A HREF='#Arriba'>Conceptos de Ajuste</a></b>
<p>
		Estos conceptos permiten corregir problemas de cantidades te�ricas con relaci�n a la existencia f�sica de los insumos, igualmente permiten corregir problemas  <br>
		de costo promedio <br> <br>
</p>
<b><A NAME ="m181"><A HREF='#Arriba'>006-Entrada por ajuste en Cantidad</a></b>
<p>
		Permite aumentar las cantidades de los art�culos en el inventario para corregir problemas. Su uso es restringido <br><br>
		<ol>
			<li>No se requiere Documento anexo ni Proveedor
			<li>Se digita en el campo centro de costos origen la sede donde se realiza el ajuste.
			<li>En el campo Criterio se digita el c�digo del art�culo a ajustar.
			<li>Se digita la cantidad a aumentar y en el campo de valor total se digita cero (0) dado que el sistema autom�ticamente calcula el costo del ajuste, el cual obedece 
					al valor promedio del momento.
			<li>(Si el valor no es digitado o se digita una cantidad diferente de cero, el sistema autom�ticamente coloca el campo en cero)
			<li>Una vez se graba el documento, se aumentan la cantidad disponible en los inventarios, conservando el costo promedio. 
		</ol>
</p>
<b><A NAME ="m182"><A HREF='#Arriba'>106-Salida por ajuste en Cantidad</a></b>
<p>
		Permite disminuir las cantidades de los art�culos en el inventario para corregir problemas. Su uso es restringido <br><br>
		<ol>
			<li>No se requiere Documento anexo ni Proveedor
			<li>Se digita en el campo centro de costos origen la sede donde se realiza el ajuste.
			<li>En el campo Criterio se digita el c�digo del art�culo a ajustar
			<li>Se digita la cantidad a disminuir y en el campo de valor total se digita cero (0) dado que el sistema autom�ticamente calcula el costo del ajuste, el cual obedece 
					al valor promedio del momento.
			<li>(Si el valor no es digitado o se digita una cantidad diferente de cero, el sistema autom�ticamente coloca el campo en cero)
			<li>Una vez se graba el documento, se disminuye la cantidad disponible en los inventarios, conservando el costo promedio. 
		</ol>
</p>
<b><A NAME ="m183"><A HREF='#Arriba'>013-Entrada por ajuste en valor</a></b>
<p>
		Permite aumentar el costo promedio de los art�culos en el inventario para corregir problemas. Su uso es restringido <br><br>
		<ol>
			<li>No se requiere Documento anexo ni Proveedor
			<li>Se digita en el campo centro de costos origen la sede donde se realiza el ajuste.
			<li>En el campo Criterio se digita el c�digo del art�culo a ajustar.
			<li>En el campo cantidad se digita cero (0) 
			<li>(Si la cantidad no es digitada o se digita una cantidad diferente de cero, el sistema autom�ticamente coloca el campo en cero)
			<li>Se digita en el campo valor total: el monto que se requiere ajustar (aumentar) al promedio unitario multiplicado por las cantidades en inventario.
			<li>Una vez se graba el documento, se aumentan el valor promedio de los art�culos, conservando la cantidad disponible en los inventarios 
		</ol>
</p>
<b><A NAME ="m184"><A HREF='#Arriba'>113-Salida por ajuste en valor</a></b>
<p>
		Permite disminuir el costo promedio de los art�culos en el inventario para corregir problemas. Su uso es restringido<br><br>
		<ol>
			<li>No se requiere Documento anexo ni Proveedor
			<li>Se digita en el campo centro de costos origen la sede donde se realiza el ajuste.
			<li>En el campo Criterio se digita el c�digo del art�culo a ajustar.
			<li>En el campo cantidad se digita cero (0) 
			<li>(Si la cantidad no es digitada o se digita una cantidad diferente de cero, el sistema autom�ticamente coloca el campo en cero)
			<li>Se digita en el campo valor total: el monto que se requiere ajustar (disminuir) al promedio unitario multiplicado por las cantidades en inventario.
			<li>Una vez se graba el documento, se aumentan el valor promedio de los art�culos, conservando la cantidad disponible en los inventarios 
		</ol>
</p>
<b><A NAME ="m185"><A HREF='#Arriba'>020-Entrada por inventario f�sico</a></b>
<p>
		Permite aumentar las cantidades de los art�culos en el inventario cuando se detectan sobrantes. Su uso es restringido <br><br>
		<ol>
			<li>No se requiere Documento anexo ni Proveedor
			<li>Se digita en el campo centro de costos origen la sede donde se realiza el ajuste, producto de un inventario f�sico.
			<li>En el campo Criterio se digita el c�digo del art�culo a ajustar
			<li>Se digita la cantidad a ser ajustada (aumentada) y en el campo de valor total se digita cero (0) dado que el sistema autom�ticamente calcula el costo 
					del ajuste, el cual obedece al valor promedio del momento.
			<li>(Si el valor no es digitado o se digita una cantidad diferente de cero, el sistema autom�ticamente coloca el campo en cero)
			<li>Una vez se graba el documento, se aumentan la cantidad disponible en los inventarios, conservando el costo promedio. 
		</ol>
</p>
<b><A NAME ="m186"><A HREF='#Arriba'>120-Salida por inventario f�sico</a></b>
<p>
		Permite disminuir las cantidades de los art�culos en el inventario cuando se detectan faltantes. Su uso es restringido <br><br>
		<ol>
			<li>No se requiere Documento anexo ni Proveedor
			<li>Se digita en el campo centro de costos origen la sede donde se realiza el ajuste, producto de un inventario f�sico.
			<li>En el campo Criterio se digita el c�digo del art�culo a ajustar
			<li>Se digita la cantidad a ser ajustada (disminuida) y en el campo de valor total se digita cero (0) dado que el sistema autom�ticamente calcula
					el costo del ajuste, el cual obedece al valor promedio del momento.
			<li>(Si el valor no es digitado o se digita una cantidad diferente de cero, el sistema autom�ticamente coloca el campo en cero)
			<li>Una vez se graba el documento, se disminuye la cantidad disponible en los inventarios, conservando el costo promedio
		</ol>
</p>
<b><A NAME="m19"><A HREF='#Arriba'>Conceptos de Importaci�n de Insumos</a></b>
<p>
		Por medio de este se registra la importaci�n de los insumos <br> <br> 
</p>
<b><A NAME ="m191"><A HREF='#Arriba'>017-Reg. importaci�n de insumos</a></b>
<p>
		Opci�n que permite ingresar los art�culos importados. <br><br>
		<ol>
			<li>Este documento no acepta modificaci�n una vez grabado.
			<li>No requiere documento anexo.
			<li>Se digita el centro de costos origen donde se recibe la mercanc�a.
			<li>En el campo Proveedor digite el Nit gen�rico 6611 para importaciones.
			<li>En el campo Doc. Soporte debe digitarse el n�mero del documento soporte de la liquidaci�n de la importaci�n.
			<li>En el campo Criterio se digita el c�digo del art�culo importado
			<li>En el campo cantidad se digita el n�mero de art�culos importados y en el campo valor total el costo total de la liquidaci�n de la importaci�n.
			<li>Una vez se graba el documento, se modifica la cantidad disponible en los inventarios y el costo promedio.
		</ol>
</p>
<br><br>
<b><A NAME="m2"><A HREF='#Arriba'>MENSAJES DE ERROR</a></b>
<p>
		Los siguientes son los mensajes de error que la aplicaci�n de Inventarios produce, las causas que los generan  y las acciones que se deben tomar para solucionarlos.   <br>
		Los errores se dividen en Codifficados y NO Codificados. Los primeros generan un mensaje de color con un icono asociado y con dos niveles de severidad.  Los codificados <br>
		permiten continuar la ejecuci�n del programa  y encontrar la soluci�n a la causa que los gener�. Los NO codificados son de mucha gravedad ya que abortan la ejecuci�n <br>
		del programa. <br>
</p>
<b><A NAME="m21"><A HREF='#Arriba'>Errores Codificados</a></b><br>
<p>
		Tienen dos niveles de severidad :   <br>
		Nivel 1 : <b>Color lila</b>, se generan basicamente por errores de digitaci�n y su correcci�n es elemental. <br>
		Nivel 2 : <b>Color Naranja</b>, su origen puede estar basado en incosistencias con el kardex o con otros documentos relacionados.  su correcci�n generalmente requiere de an�lisis. <br>
</p>
<b><A NAME="m2101"><A HREF='#Arriba'>Error Nro. 1</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err1.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR sin haber escogido el Concepto de Inventario que se desea mover.  <br>
		Aunque es de digitaci�n, es de nivel 2 ya el concepto de inventario es el par�metro fundamental de validaci�n.   <br>
</p>
<b><A NAME="m2102"><A HREF='#Arriba'>Error Nro. 2</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err2.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted intenta Regrabar un Documento que ya existe despues de Realizar una consulta <br>
		Solamente los documentos que no afectan el Kardex se pueden modificar. <br>
</p>
<b><A NAME="m2103"><A HREF='#Arriba'>Error Nro. 3</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err3.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted intenta Ejecutar el programa digitando su URL en un navegador sin haberse dado login en MATRIX <br>
		Para eliminar este error haga ingreso a la aplicaci�n a traves del login de MATRIX en el usuario que se le asigno.  <br>
</p>
<b><A NAME="m2104"><A HREF='#Arriba'>Error Nro. 4</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err4.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR sin haber escogido el Centro de Costos Origen.  <br>
		Todo movimiento de inventario requiere Centro de Costos Origen. Para eliminar este error haga una seleccion en el drop down  C.C. ORIGEN.   <br>
</p>
<b><A NAME="m2105"><A HREF='#Arriba'>Error Nro. 5</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err5.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR sin haber escogido el Centro de Costos Destino y el concepto esta parametrizado  <br>
		para pedirlo. Para eliminar este error haga una seleccion en el drop down  C.C. DESTINO.   <br>
</p>
<b><A NAME="m2106"><A HREF='#Arriba'>Error Nro. 6</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err6.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR sin haber digitado el Documento Anexo y el concepto esta parametrizado   <br>
		para pedirlo o lo digit� de forma incorrecta. Para eliminar este error digite correctamente el Documento Anexo.   <br>
</p>
<b><A NAME="m2107"><A HREF='#Arriba'>Error Nro. 7</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err7.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR sin haber digitado el Documento Soporte y el concepto esta parametrizado   <br>
		para pedirlo o lo digit� de forma incorrecta. Para eliminar este error digite correctamente el Documento Soporte.   <br>
</p>
<b><A NAME="m2108"><A HREF='#Arriba'>Error Nro. 8</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err8.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR sin haber escogido el Proveedor y el concepto esta parametrizado   <br>
		para pedirlo. Para eliminar este error haga una seleccion en el drop down  PROVEEDOR.   <br>
</p>
<b><A NAME="m2109"><A HREF='#Arriba'>Error Nro. 9</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err9.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR sin haber digitado el A�o o lo digit� de forma incorrecta  <br>
		Para eliminar este error digite correctamente el A�o.    <br>
</p>
<b><A NAME="m2110"><A HREF='#Arriba'>Error Nro. 10</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err10.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR sin haber digitado el Mes o lo digit� de forma incorrecta  <br>
		Para eliminar este error digite correctamente el Mes.    <br>
</p>
<b><A NAME="m2111"><A HREF='#Arriba'>Error Nro. 11</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err11.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR y digit� el Retenci�n de forma incorrecta  <br>
		Para eliminar este error digite correctamente la Retenci�n.    <br>
</p>
<b><A NAME="m2112"><A HREF='#Arriba'>Error Nro. 12</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err12.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR sin haber digitado la Fecha o la digit� de forma incorrecta  <br>
		Para eliminar este error digite correctamente la Fecha. La fecha debe ser la misma de la asignada x el Sistema.    <br>
</p>
<b><A NAME="m2113"><A HREF='#Arriba'>Error Nro. 13</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err13.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR sin haber digitado al menos un articulo en el Detalle del Documento <br>
		Todo movimiento de inventario requiere Detalle. Para eliminar este error digite el movimiento de al menos un articulo.   <br>
</p>
<b><A NAME="m2114"><A HREF='#Arriba'>Error Nro. 14</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err14.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR y la cantidad de un articulo determinado supera la suma de las cantidades <br>
		de los documentos con conceptos asociados por ejemplo:la suma de las cantidades de los conceptos de deboluci�n de prestamo debe <br>
		ser igual o menor que la cantidad del concepto de prestamo asociado. Para eliminar este error verifique mediante consultas que las suma <br>
		de las cantidades no supere la del concepto asociado. <br>
</p>
<b><A NAME="m2115"><A HREF='#Arriba'>Error Nro. 15</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err15.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR y el valor promedio digitado es diferente del documento del concepto <br>
		asociado. Para eliminar este error digite el valor promedio que sale en el mensaje de error<br>
</p>
<b><A NAME="m2116"><A HREF='#Arriba'>Error Nro. 16</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err16.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR y el centro de costos o el proveedor seleccionado son diferentes <br>
		al del deocumento anexo asociado. Para corregir este error seleccione el centro de costos  y el proveedor que salen en el mensaje de error<br>
</p>
<b><A NAME="m2117"><A HREF='#Arriba'>Error Nro. 17</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err17.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR y el documento anexo NO existe para el concepto asociado.  <br>
		Para corregir este error por consulta busque el documento anexo correcto<br>
</p>
<b><A NAME="m2118"><A HREF='#Arriba'>Error Nro. 18</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err18.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR y digit� un documento anexo multiple, cuando el documento anexo.  <br>
		es obligatorio. Para corregir este error digite el documento anexo de forma correcta.<br>
</p>
<b><A NAME="m2119"><A HREF='#Arriba'>Error Nro. 19</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err19.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR no digit� el documento anexo o lo hizo de forma icorrecta .  <br>
		Para corregir este error digite el documento anexo de forma correcta.<br>
</p>
<b><A NAME="m2120"><A HREF='#Arriba'>Error Nro. 20</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err20.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR Y digit� un art�culo que ya habia digitado en el mismo documento .  <br>
		soporte. Para corregir este error por consulta verifique el art�culo en el documento soporte que esta digitando.<br>
</p>
<b><A NAME="m2121"><A HREF='#Arriba'>Error Nro. 21</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err21.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR Y digit� una cantidad que no es numerica.  <br>
		Para corregir este error digite una cantidad num�rica en el art�culo del mensaje.<br>
</p>
<b><A NAME="m2122"><A HREF='#Arriba'>Error Nro. 22</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err22.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR Y el porcentaje de iva en el maestro de art�culos esta incorrecto.  <br>
		Para corregir este error digite un porcentaje de iva correcto en el maestro de articulos.<br>
</p>
<b><A NAME="m2123"><A HREF='#Arriba'>Error Nro. 23</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err23.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR Y digit� un valor total que no es numerico.  <br>
		Para corregir este error digite un valor total num�rico en el art�culo del mensaje.<br>
</p>
<b><A NAME="m2124"><A HREF='#Arriba'>Error Nro. 24</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err24.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR Y digit� una fecha en formato incorrecto (aaaa-mm-dd).  <br>
		Para corregir este error digite una fecha correcta en el art�culo del mensaje.<br>
</p>
<b><A NAME="m2125"><A HREF='#Arriba'>Error Nro. 25</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err25.gif'><br>
<p>
		Nivel 1.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR Y digit� un nro de lote que no es numerico.  <br>
		Para corregir este error digite un nro de lote num�rico en el art�culo del mensaje.<br>
</p>
<b><A NAME="m2126"><A HREF='#Arriba'>Error Nro. 26</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err26.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR Y digit� datos que generan VALORES NEGATIVOS en el kardex. <br>
		Para corregir este error consulte el kardex y revise el movimiento que desea grabar.<br>
</p>
<b><A NAME="m2127"><A HREF='#Arriba'>Error Nro. 27</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err27.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR y se produjo un error en la Base de Datos que impide grabar el documento. <br>
		Cuendo se le presente este error, escriba el mensaje completo y llame inmediatamente a la Direcci�n de Inform�tica.<br>
</p>
<b><A NAME="m2128"><A HREF='#Arriba'>Error Nro. 28</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err28.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR y se produjo un error en la Base de Datos que impide grabar el documento. <br>
		Cuendo se le presente este error, escriba el mensaje completo y llame inmediatamente a la Direcci�n de Inform�tica.<br>
</p>
<b><A NAME="m2129"><A HREF='#Arriba'>Error Nro. 29</a></b>
<br><br><IMG SRC='/matrix/images/medical/FARMASTORE/err29.gif'><br>
<p>
		Nivel 2.  <br>
		Este error se genera cuando usted selecciona la opci�n de GRABAR y se produjo un error en la Base de Datos que impide grabar el documento. <br>
		Cuendo se le presente este error, escriba el mensaje completo y llame inmediatamente a la Direcci�n de Inform�tica.<br>
</p>
<b><A NAME="m22"><A HREF='#Arriba'>Errores NO Codificados</a></b>
<p>
		Estos mensaje son de ALTA GRAVEDAD debido a que interrumpen la ejecuci�n del programa, e indican un posible da�o en la Base de Datos.<br>
		Cuando le aparezca uno de los siguientes mensajes en la pantalla : <br><br>
		<ol>
			<li>ERROR CONSULTANDO KARDEX
			<li>ERROR ACTUALIZANDO KARDEX
			<li>ERROR INICIALIZANDO KARDEX 
			<li>ERROR BLOQUEANDO KARDEX Y DETALLE DE MOVIMIENTO
			<li>ERROR BLOQUEANDO CONSECUTIVO
			<li>ERROR CONSULTANDO CONSECUTIVO
			<li>ERROR DESBLOQUEANDO ENCABEZADO Y CONSECUTIVO
			<li>ERROR INCREMENTANDO CONSECUTIVO
			<li>ERROR DESBLOQUEANDO
			<li>ERROR CONSULTANDO KARDEX
			<li>ERROR DESBLOQUEANDO KARDEX Y DETALLE
			<li>ERROR ACTUALIZANDO ENCABEZADO
		</ol>
		<br>
		Escriba el mensaje completo y <b>llame inmediatamente a la Direcci�n de Inform�tica.</b><br>
</p>
</body>
</html>

