<html>
<head>
  <title>Cambio Banco</title>

  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.titulo1{color:#FFFFFF;background:#006699;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	<!-- -->
    	.titulo2{color:#003366;background:#57C8D5;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#A4E1E8;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.error1{color:#FF0000;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.ok{color:Green;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:center;font-weight:bold;}

    </style>

     <script type="text/javascript">
     function enter()
     {
     	document.forma.submit();
     }
   	</script>
</head>
<body>
<?php
include_once("conex.php");
/**
 * PROGRAMA QUE CAMBIA EL BANCO DE DESTINO DE UNA FORMA DE PAGO DE UN RECIBO DETERMINADO
 *
 * El programa muestra la información de los registros asociados a una forma de pago (especificada por parámetro),
 * de un recibo cuya fuente, numero y centro de costos fue especificada al llamar al programa.
 * Adicionalmente permite modificar la forma de pago para los diferentes registros, el usuario selecciona
 * "guardar cambios" si el usuario no realiza ningun cambio, el sistema le informa por medio de un mensaje de error,
 * si el cambio fue exitoso se imprime la palabra
 * "exitoso" en frente del registro, en caso de haber un error durante la modificación se imprime el error al
 * lado de registro que no puedo ser modificado.
 *
 * @name	Cambio de Banco
 * @author	Ana María Betancur Vargas
 * @created	2006-12-28
 * @version 2007-05-04
 *
 * @wvar String[2] 	$fue 		fuente del recibo
 * @wvar Integer	$num	 	numero del recibo
 * @wvar String[4]	$cco 		centro de costos del recibo
 * @wvar String[2]	$fpa		código de la forma de pago
 * @wvar String		$fpaDes 	descripción de la forma de pago
 * @wvar Integer	$grabar 	indica si el usuario eligio grabar los cambios.
 * @wvar Array		$fpaArr 	Toda la información de las formas de pago del recibo elegido.<br>
 * 								['vfp']:Valor forma de pago.<br>
 * 								['dan']:Documento anexo.<br>
 * 								['obs']:Observaciones.<br>
 * 								['caf']:Caja final de la forma de pago.<br>
 * 								['pla']:Plaza.<br>
 * 								['aut']:Número de la autorización.<br>
 * 								['ban']:Banco.<br>
 * 								['id']:id del registro (de la forma de pago en la tabla 000021).
 * @wvar Array		$bancos  	Contiene la información de los códigos de los bancosd que tienen activado Banrec y su estado esta activo.
 * 								['cod']: código del banco.<br>
 * 								['nom']: nombre del banco.
 * @wvar String		$tipoTablas Primera parte del nombre de las tablas en donde se encuentra la información, en este caso corresponde a la empresa.
 *
 * @table 000022 SELECT, UPDATE
 * @table 000069 SELECT
 *
 * @modified Se cambia la función Terminar por  Grabar, y asi mismo la variable $terminar por $grabar
 * @modified Carolina Castano 2007-04-30  Se actualiza tambien el banco incial en tabla 000022
 * @modified Carolina Castano 2007-05-04  Se modifica a un cambio de forma de pago, que actualiza automaticamente el banco por defecto del maestro
 * @modified Carolina Castano 2007-05-10  faltaba preguntar el banco de origen
 */
$wversion="2007-05-10";

/**
 * Trae el detalle de los registros de la tabla 000022
 *
 * información de los registros asociados a una forma de pago (especificada por parámetro),
 * de un recibo cuya fuente, numero y centro de costos fue especificada al llamar la función.
 *
 * @param Integer 	$num 		número del recibo
 * @param string[2] $fue 		fuente del recibo
 * @param String[4] $cco 		centro de costos del recibo
 * @param String[2] $fpa 		forma de pago
 * @param Array		$fpaArr 	información de los registros de la tabla 000022 que concuerdan co los demás parámetros.
 * 								['vfp']:Valor forma de pago.<br>
 * 								['dan']:Documento anexo.<br>
 * 								['obs']:Observaciones.<br>
 * 								['caf']:Caja final de la forma de pago.<br>
 * 								['pla']:Plaza.<br>
 * 								['aut']:Número de la autorización.<br>
 * 								['ban']:Banco.<br>
 * 								['id']:id del registro (de la forma de pago en la tabla 000021).
 *
 * @table 000022 SELECT
 */
function fpaDetalle ($num, $fue, $cco, $fpa, &$fpaArr) {
	global $tipoTablas;
	global $conex;

	$q = "SELECT * "
	."FROM ".$tipoTablas."_000022 "
	."WHERE Rfpnum = ".$num." "
	."AND	Rfpfpa = '".$fpa."' "
	."AND	Rfpcco = '".$cco."' "
	."AND	Rfpfue = '".$fue."' "
	."AND	Rfpest = 'on' ";
	$err = mysql_query($q,$conex);
	echo mysql_error();
	$numi = mysql_num_rows($err);
	if ($numi > 0)	{
		for($i=0; $i<$numi; $i++){
			$row=mysql_fetch_array($err);
			$fpaArr['vfp'][$i] = $row['Rfpvfp'];
			$fpaArr['obs'][$i] = $row['Rfpobs'];
			$fpaArr['pla'][$i] = $row['Rfppla'];
			$fpaArr['ban'][$i] = $row['Rfpban'];
			$fpaArr['caf'][$i] = $row['Rfpcaf'];
			$fpaArr['aut'][$i] = $row['Rfpaut'];
			$fpaArr['dan'][$i] = $row['Rfpdan'];
			$fpaArr['id'][$i] = $row['id'];

		}
	}
}

/**
 * Trae el código y nombre de los bancos con Banrec='on'
 *
 * @param Array $bancos Informaci+on de los bancos.<br>
 * 						['cod']: código del banco.<br>
 * 						['nom']: nombre del banco. *
 * @table 000069 SELECT
 */
function Bancos($banco)
{
	global $tipoTablas;
	global $conex;

	if ($banco) //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$q =  " SELECT  bannom "
		."   FROM ".$tipoTablas."_000069 "
		."  where bancod='".$banco."' and banest='on' and bancag<>'on' ";

		$res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
		$row = mysql_fetch_array($res);
		$bancos['codigo'][1]=$banco;
		$bancos['nombre'][1]=$row[0];
		$cadena="bancod <> '".$banco."' and ";
		$acu=2;
	}
	else
	{
		$cadena='';
		$acu=1;
	}

	$q =  " SELECT bancod, bannom "
	."   FROM ".$tipoTablas."_000069 "
	."  where ".$cadena." banest='on' and bancag<>'on' ";

	$res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

	if ($num>0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			$bancos['codigo'][$acu]=$row[0];
			$bancos['nombre'][$acu]=$row[1];
			$acu++;
		}
		return $bancos;
	}
	else
	{
		return false;
	}
}

/**
 * Realiza el cambio de banco en la Base de datos
 *
 * Verifica que algun banco haya cambiado y realiza la modificación en la base de datos,
 * si no hay ningún banco para modificar ingresa un mensaje de alerta a la variable $mensaje.
 *
 * @param Array $fpaArr información de los registros.
 * @param String $mensaje mensaje de error en caso de que el ususrio no haya modificado ningun banco.
 *
 * @table 000022 UPDATE
 */
function Grabar (&$fpaArr, &$mensaje, $fuente, $numero, &$fpa){
	global $tipoTablas;
	global $conex;

	//if($terminar == 1) {
	$count=count($fpaArr['vfp']);
	for ($i=0;$i<$count;$i++)
	{
		$q="select fpache, fpatar "
		."from	".$tipoTablas."_000023 "
		."where	fpaest='on' and fpacod='".$fpaArr['baM'][$i]."' ";

		$err = mysql_query($q,$conex);
		$row = mysql_fetch_array($err);
		if ($row[0]=='on' or $row[1]=='on')
		{
			IF ($fpaArr['dan'][$i]=='' or $fpaArr['obs'][$i]=='' or $fpaArr['pla'][$i]=='' or $fpaArr['aut'][$i]=='')
			{

				$parar=1;
				$mensaje="<center><table border='0'>"
				."<tr>"
				."<td class='error1'>DEBE INGRESAR TODA LA INFORMACION REQUERIDA PARA LAS FORMAS DE PAGO QUE LO OBLIGAN</td>"
				."</tr>"
				."</table></center>";
			}

		}
		else
		{
			$fpaArr['pla'][$i]='';
			$fpaArr['aut'][$i]='';
		}

		if (!isset($parar))
		{
			//consulto el banco por defecto para cada uno de los registros
			$q = "SELECT bancod "
			."  FROM ".$tipoTablas."_000023, ".$tipoTablas."_000069 "
			." WHERE fpacod = '".$fpaArr['baM'][$i]."'"
			."   AND fpaest = 'on' "
			."   AND fpacba = bancod "
			."   AND banest='on' ";
			$res = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA ENCONTRADO EL BANCO PARA LA FORMA DE PAGO ".mysql_error());
			$row= mysql_fetch_array($res);

			$q = "UPDATE ".$tipoTablas."_000022 "
			."SET Rfpban = '".$row[0]."', Rfpbai = '".$row[0]."', Rfpfpa = '".$fpaArr['baM'][$i]."', Rfpdan='".$fpaArr['dan'][$i]."', Rfpobs='".$fpaArr['obs'][$i]."', Rfppla='".$fpaArr['pla'][$i]."', Rfpaut='".$fpaArr['aut'][$i]."' "
			."WHERE	id = '".$fpaArr['id'][$i]."' ";
			$err = mysql_query($q,$conex);
			echo mysql_error();
			$num = mysql_affected_rows();
			echo mysql_error();
			if($num>0){

				$q = "UPDATE ".$tipoTablas."_000074 "
				." SET Cdefpa = '".$fpaArr['baM'][$i]."' "
				."WHERE	Cdecaj = '".$fpaArr['caf'][$i]."' and Cdefue= '".$fuente."' and Cdenum = '".$numero."' and Cdefpa = '".$fpa."' ";
				$err = mysql_query($q,$conex);

				//tambien debemos actualizar la tabla 37 donde van quedando los documentos pendientes
				$q = "UPDATE ".$tipoTablas."_000037 "
				." SET Cdefpa = '".$fpaArr['baM'][$i]."' "
				."WHERE	Cdecaj = '".$fpaArr['caf'][$i]."' and Cdefue= '".$fuente."' and Cdenum = '".$numero."' and Cdefpa = '".$fpa."' ";
				$err = mysql_query($q,$conex);

				$fpa=$fpaArr['baM'][$i];
				$fpaArr['ok'][$i]=true;
				$fpaArr['mod'][$i]="Exitoso";
				$mensaje =  "<center><table border='0'>"
				."<tr>"
				."<td class='error1'>SE HAN ALMACENADO CORRECTAMENTE LOS CAMBIOS</td>"
				."</tr>"
				."</table></center>";
			}
			else {
				$fpaArr['ok'][$i]=false;
				$fpaArr['mod'][$i]="Hubo un problema al realizar el cambio,<br>por favor vuelva a intentarlo o <br>comuniquese con la Dirección de Informática.";
				$mensaje =  "<center><table border='0'>"
				."<tr>"
				."<td class='error1'>NO HA SIDO POSIBLE ALMACENAR LOS CAMBIO</td>"
				."</tr>"
				."</table></center>";
			}
		}
	}
}

//2007-05-04
function consultarFormas($wformaF)
{
	//consulto las formas de pago diferentes a efectivo

	global $conex;
	global $tipoTablas;

	if ($wformaF!='')
	{
		$formas['codigo'][0]=$wformaF;


		$q="select fpades "
		."from	".$tipoTablas."_000023 "
		."where	fpaest='on' and fpacod='".$wformaF."' ";

		$err = mysql_query($q,$conex);
		$row = mysql_fetch_array($err);

		$formas['nombre'][0]=$row[0];

		$q="select fpacod, fpades  "
		."from	".$tipoTablas."_000023 "
		."where	fpaest='on' and fpacod<>'".$wformaF."'  ";

	}else
	{
		$formas['codigo'][0]='';
		$formas['nombre'][0]='';

		$q="select fpacod, fpades "
		."from	".$tipoTablas."_000023 "
		."where	fpaest='on' ";
	}
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$formas['codigo'][$i]=$row[0];
			$formas['nombre'][$i]=$row[1];
		}

	}
	return $formas;
}

if(!isset($_SESSION['user']))
echo "error";
else
{
	if(!isset($fue))	{

	}else{






		/*Encabezado del Programa*/
		echo "<center><table border='1' width='360'>";
		echo "<tr><td colspan='2' class='titulo1'><img src='/matrix/images/medical/POS/logo_".$tipoTablas.".png' WIDTH='359' HEIGHT='127'></td></tr>";
		echo "<tr><td colspan='2'class='titulo1'><b>CAMBIO DE BANCO</b></font></td></tr>";
		echo "<tr><td colspan='2'class='titulo1'><b>cambioBanco.php Versión ".$wversion."</b></font></td></tr>";
		echo "</table><br><br>";

		echo "<form method='POST' action='' name='forma'>";

		/*Información del recibo*/
		echo "<center><table border='1'>";
		echo "<tr><td colspan='2'class='titulo2'><b>RECIBO</b></font></td></tr>";
		echo "<tr><td class='titulo3'>Fuente</td><td class='texto'>".$fue."</td></tr>";
		echo "<tr><td class='titulo3'>Número</td><td class='texto'>".$num."</td></tr>";
		echo "<tr><td class='titulo3'>Centro de costos</td><td class='texto'>".$cco."</td></tr>";
		echo "<tr><td class='titulo3'>Forma de Pago</td><td class='texto'>".$fpa."-".$fpaDes."</td></tr>";
		echo "</table><br><br>";

		echo "<input type='hidden' name='fue' value='".$fue."'>";
		echo "<input type='hidden' name='num' value='".$num."'>";
		echo "<input type='hidden' name='cco' value='".$cco."'>";
		echo "<input type='hidden' name='fpa' value='".$fpa."'>";
		echo "<input type='hidden' name='' value='".$fpaDes."'>";
		//echo "<input type='hidden' name='' value='".$."'>";

		if (!isset ($fpaArr))
		{
			fpaDetalle($num, $fue, $cco, $fpa, $fpaArr);
		}

		$mensaje='';

		/*Si terminar esta set es por que el usuario eligio grabar
		se llama la función determinado para tal fin*/
		if(isset($grabar)) {
			Grabar($fpaArr,$mensaje, $fue, $num, $fpa);
		}

		$count = count($fpaArr['vfp']);

		if(isset($fpaArr['mod'])) {
			$countMod = count($fpaArr['mod']);	;
		}else{
			$countMod=0;
		}

		/*Encabezado de la información de los registros*/
		echo "<center><table border='1'>";
		echo "<tr><td colspan='7' class='titulo2'><b>DETALLE DE LA FORMA DE PAGO</b></font></td></tr>";
		echo "<td class='titulo3' rowspan='2'>Valor</td>";
		echo "<td class='titulo3' rowspan='2'>Nueva forma de pago</td>";
		echo "<td class='titulo3' rowspan='2'>Doc. Anexo</td>";
		echo "<td class='titulo3' rowspan='2'>Banco Origen (observacion para efectivo)</td>";
		echo "<td class='titulo3' rowspan='2'>Ubicación (no aplica para efectivo)</td>";
		echo "<td class='titulo3' rowspan='2'>N° Autorización (no aplica para efectivo)</td>";

		if($countMod >0){
			echo "<td class='titulo3' rowspan='2'>Cambio</td>";
		}
		echo "<tr>";

		for($i=0;$i<$count;$i++) {


			if (!isset ($fpaArr['baM'][$i]))
			{
				$formas=consultarFormas($fpa);

			}
			else
			{
				$formas=consultarFormas($fpaArr['baM'][$i]);
			}
			echo "<tr>";

			/*Select en donde se puede elegir el banco a modificar*/


			echo "<td class='texto' rowspan='2'> $ ".number_format($fpaArr['vfp'][$i],",","",".")."</td>";
			/*echo "<td class='texto' rowspan='2'>".$fpaArr['dan'][$i]."</td>";
			echo "<td class='texto' rowspan='2'>".$fpaArr['obs'][$i]."</td>";
			echo "<td class='texto' rowspan='2'>".$fpaArr['pla'][$i]."</td>";
			echo "<td class='texto' rowspan='2'>".$fpaArr['aut'][$i]."</td>";*/

			echo "<td class='texto' rowspan='2'><select name='fpaArr[baM][".$i."]' onchange='enter()'>";
			for($j=0;$j<count($formas['codigo']);$j++) {
				echo "<option value='".$formas['codigo'][$j]."'>".$formas['codigo'][$j]."-".$formas['nombre'][$j]."</option>";
			}
			echo "</select></td>";

			echo "<input type='hidden' name='fpaArr[vfp][".$i."]' value='".$fpaArr['vfp'][$i]."'>";
			echo "<input type='hidden' name='fpaArr[caf][".$i."]' value='".$fpaArr['caf'][$i]."'>";
			echo "<td class='texto' rowspan='2'><input type='text' name='fpaArr[dan][".$i."]' value='".$fpaArr['dan'][$i]."'></td>";

			if ($formas['codigo'][0]=='99')
			{
				echo "<td class='texto' rowspan='2'><input type='text' name='fpaArr[obs][".$i."]' value='".$fpaArr['obs'][$i]."'></td>";
			}
			else
			{

				$bancos=Bancos($fpaArr['obs'][$i]);
				echo "<td class='texto' rowspan='2'><select name='fpaArr[obs][".$i."]'>";
				for($j=1;$j<=count($bancos['codigo']);$j++) {
					echo "<option value='".$bancos['codigo'][$j]."'>".$bancos['codigo'][$j]."-".$bancos['nombre'][$j]."</option>";
				}
				echo "</select></td>";
			}

			switch ($fpaArr['pla'][$i])
			{
				case '1-Local':
				$otro='2-Otras plazas';
				$otro2='';
				break;
				case '2-Otras plazas':
				$otro='1-Local';
				$otro2='';
				break;
				case 'L':
				$fpaArr['pla'][$i]='1-Local';
				$otro='2-Otras plazas';
				$otro2='';
				break;
				case 'O':
				$otro='1-Local';
				$fpaArr['pla'][$i]='2-Otras plazas';
				$otro2='';
				break;
				default:
				$otro='1-Local';
				$fpaArr['pla'][$i]='';
				$otro2='2-Otras plazas';
				break;
			}
			echo "<td class='texto' rowspan='2'><select name='fpaArr[pla][".$i."]' ><option selected>".$fpaArr['pla'][$i]."</option ><option>".$otro."</option><option>".$otro2."</option></select></td>";

			//echo "<td class='texto' rowspan='2'><input type='text' name='fpaArr[pla][".$i."]' value='".$fpaArr['pla'][$i]."'></td>";
			echo "<td class='texto' rowspan='2'><input type='text' name='fpaArr[aut][".$i."]' value='".$fpaArr['aut'][$i]."'></td>";
			echo "<input type='hidden' name='fpaArr[ban][".$i."]' value='".$fpaArr['ban'][$i]."'>";
			echo "<input type='hidden' name='fpaArr[id][".$i."]' value='".$fpaArr['id'][$i]."'>";
			//echo "<input type='hidden' name='fpaArr[][".$i."]' value='".$fpaArr[''][$i]."'>";



			/*Si existe un cambio debe mostrar si fue exitoso o no*/
			echo "<tr>";
			if(isset($fpaArr['mod'][$i])) {
				if($fpaArr['ok'][$i]) {
					echo "<td class='ok'>";
				}else {
					echo "<td class='error1'>";
				}
				echo $fpaArr['mod'][$i]."</td>";
			}else{
				/*No se realizo ningun cambio al registro*/
				echo "<td></td>";
			}
			echo "</tr>";
		}
		echo "</table><br><br>";

		/*Mensaje de problema en caso de exisitir*/
		echo $mensaje;

		echo "<center><table border='0'>";
		echo "<tr>";
		echo "<td class='texto'><input type='checkbox' name='grabar'> Guardar cambios</td>";
		echo "<td><input type='submit' name='Aceptar' value='ACEPTAR'></td></tr>";
		echo "<tr><td align=center colspan=2>&nbsp;</td></tr>";
		echo "<tr><td align=center colspan=2><a href='cuadreN.php?wbasedato=".$tipoTablas."'>Volver</a></td></tr>";
		echo "</tr>";
		echo "</table></center>";

		echo "</form>";
	}
}
?>
</body>
</html>