<html>
<head>
<title>DEVOLUCION</title>
<?php
include_once("conex.php"); 
if (!isset($percod))
{?>
<script>
function ira(){document.carga.percod.focus();}
</script><?php
}
else if (!isset($hist))
{?>
<script>
function ira(){document.carga.hist.focus();}
</script><?php
}
else
{?>
<script>
function ira(){document.carga.artcod.focus();}
</script><?php
}?>
</head>
<BODY onload=ira() TEXT="#000066"  BGCOLOR="#FFFFFF">
<?php
/********************************************************
*		DEVOLUCIÓN DE ARTÍCULOS A PACIENTES			*
*********************************************************/

//==================================================================================================================================
//GRUPO						:PDA
//AUTOR						:Ana María Betancur V.
$wautor="Ana María Betancur V.";
//FECHA CREACIÓN			:2005-02-08
//FECHA ULTIMA ACTUALIZACIÓN 	:
$wactualiz="(Versión 2006-08-08)";
//DESCRIPCIÓN					: Valida sobre las tablas de INFORMIX inpaci (si el paciente esta activo), ivart-ivartcba
//								  (el articulo existe), ivsal (existe el saldo para el articulo y el cc) , ivarttar (que exista la
//								  tarifa para el articulo en ese año mes).  Si existe conexión ODBC con el INFORMIX realiza las
//								  comprobaciones mencionadas, usa el include numera para obtener un numero de registro y de lines y
//								  graba sobre itdro (INFORMIX) y farmpda_000001 (MATRIX)la informacion que debe quedar en la factura.
//
//								  Si no existe conexion ODBC con el UNIX no se realizan las validaciones y se graba en farmpda_000003
//								  con Fuente=1, para que posteriormente reppda/ingreso_unix.php ingrese los articulos a itdro (UNIX).
//
//								  Las validaciones que se realizan en MATRIX son para ambos casos (con y sibn ODBC) y son las siguientes:
//								  Ademas valida el cc sobre la tabla costosyp_000005 de Matrix, tambien sobre la tabla farmpda_000002 busca
//								   si el articulo tiene una cantidad diferente a 1, o si requiere de justificacion, formula, ambas o ninguna.
//
//								  Para ambos casos si existe un error tanto en las validaciones como en el ingreso de la informacíon a las
//								  tablas se llama al include error.
//------------------------------------------------------------------------------------------------------------------------------------------
//ACTUALIZACIONES
//	2006-08-08
//		Se cambia el n$num por $numCco en lo que tiene que ver con traer la info de los Cco de la bd.
//	2006-07-04
//		Se cambia $tipGrab por $tipTrans y $fuente segun sea el caso
//		Se modifica todo lo referente a los centro de costos y sus atributos, pues se extrae de la tabla farmpda_000014.
//	2006-04-28
//		Se pone el trim(hist) cuando conex_o==0 , es decir esta fuera de UNIX.
//	2006-04-24
//		A partir de este momento, cuando no hay ODBC
//			Ya no busca el paciente con la historia en la tabla farmpda_000003 si no en la farmpda_000003, para tener mayor probabilidad de
//			encontrarlo.
//			El if(isset($artcod) and $artcod != "") cambia por
//	2006-04-20
//		Se modifica la seccion de validaciones cuando conex_o=0, es decir cuando no hay conexión con el ODBC, se pone todo lo qye respecta
//		a los artículos en el include validacion_articulo_SinODBC.php, lavalidación de que no ingresen una histora vacia se hizo dentro del
//		programa.
//	2006-04-11
//		Se documenta.
//		Se cambia Clinica Medica LAs Americas, por Clinica las Americas.
//		Se borra la selección de turnos d la página inicial y tambien las fechas.
//		En el insert sin Unix a la tabla farmpda_000003 se borra turno y fecha.
//		Se modifica el hypervinculo al reporte.
//	2005-08-31
//		Todas las pantallas deben decir devolución.
//	2005-08-30
//		Cuando el CC sea 1050 la fuente es GD para grabación y  DD para devolución.
//	2005-05-20
//		Pide codigo de nomina al usuario, si es de 6 digitos (tal y como aparece en el codigo de barras del carnet) lo recorta a 5, con el
//		codigo define a que si el cc de costos es productivo o pertenece a farmacia, el cc 1320 (neumologia) tiene condiciones especiales,
//		deben ingresar el CC sobre el cual estan grabando.
//		La mayoria de los procesos de que hacia el programa han sido trasladado a includes que pda_ingreso.php tambien usa.
//		Los includes son
//		pda/socket.php para comprobar la conexion al unix,
//		pda/validacion_hist.php, para validar que la historia exista y extraer datos del paciente,
//		pda/validacion_articulo.php pra validar que el articulo sea correcto y traer la informacion necesaria del mismo,
//		pda/registro_tablas.php para registrar la informacion en las tablas respectivas, pda/articulos_especiales.php,
//		para buscar en MATRIX informacion del articulo si la hay,
//		pda/impresion_pantalla.php para imprimir la info en pantalla.
//		Tambien usa un include propio.
//
//--------------------------------------------------------------------------------------------------------------------------------------
//TABLAS
//
//	root_000025
//	farmpda_000003
//	farmpda_000014
/**
 * @modified 2007-02-26 Se modifican los if para cambiar las preguntas relacionadas con $artgen por preguntas relacionadas con $artValido y $cantVariable
 * @modified 
 */





/* COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente-HISTORIA- Y CC )*/
$warning="";
$odbc="NO INFO";
if (isset($user) and !isset($percod)){
	$percod=substr($user,1);
}
if(!isset($percod)) {
	/*PANTALLA DE INGRESO DE NUMERO DE CC**/
	?>		<form name="carga" action="" method=post>		<?php
	echo "<center><table border=0 width=300>";
	echo "<tr><td align=center ><b>CLÍNICA  LAS AMERICAS </b></td></tr>";
	echo "<tr><td align='center' colspan='2'><b><font color=#ff0000>DEVOLUCIONES</b></td></tr>";
	echo "<tr><td bgcolor=#cccccc ><font color=#000066><b>CODIGO NOMINA: </font>";
	?>	<input type='text' cols='10' name='percod'></td></tr>	<?php
	echo"<tr><td align=center bgcolor=#cccccc  ><input type='submit' value='ACEPTAR'></td></tr></form>";
	/*FIN::::PANTALLA DE INGRESO DE nUMERO DE CC*/
}
elseif (!isset($hist)) {?>		<form name="carga" action="" method=post>		<?php
/*PANTALLA DE INGRESO DE NUMERO DE HISTORIA*/
/*buscar el cc en matrix*/
if(strlen($percod) == 6)
$percod=substr($percod,1);
$query="select Ccocod, Cconom, Ccosel from root_000025,farmpda_000014 where Empleado='".$percod."' and Ccocod=Cc and Ccofac='on' ";
$err = mysql_query($query,$conex);
$num = mysql_num_rows($err);
if($num >0)	{
	echo "<center><table border=0 width=300>";
	echo "<tr><td align=center ><b>CLÍNICA LAS AMERICAS </b></td></tr>";
	echo "<tr><td align='center' colspan='2'><b><font color=#ff0000>DEVOLUCIONES</b></td></tr>";
	$row=mysql_fetch_array($err);
	if(trim($row['Ccosel']) == 'off' ){
		$cc=$row[0]."-".$row[1];
		echo "<tr><td align=center><font color=#000066><b>$cc";
		echo "<input type='hidden' name ='cc' value='$cc' ></TR>";
		echo "<tr><td align=center><font color=#000066><b>USUARIO: $percod";
		echo "<input type='hidden' name ='percod' value='$percod' ></b></TR>";
	}else{
		echo "<tr><td align=center><font color=#000066><b>USUARIO: $percod";
		echo "<input type='hidden' name ='percod' value='$percod' ></b></TR>";
		echo "<tr><td bgcolor=#cccccc ><font color=#000066><b>CC: </font>";
		$query="select Ccocod, Cconom from farmpda_000014 where Ccofac='on' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num >0)	{
			echo "<select name='cc'>";
			for($i=0;$i<$num;$i++){
				$row=mysql_fetch_array($err);
				echo "<option>".$row['Ccocod']."-".$row['Cconom']."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
	}
	echo "<tr><td bgcolor=#cccccc ><font color=#000066><b>N° HISTORIA: </font>";
	?>	<input type='text' cols='10' name='hist'></td></tr>	<?php
	echo"<tr><td align=center bgcolor=#cccccc  ><input type='submit' value='ACEPTAR'></td></tr></form>";
	/*FIN::::PANTALLA DE INGRESO DE nUMERO DE HISTORIA*/
}else {
	/*No esta el CC en Matrix*/
	$cc=".";
	$date=date("Y-m-d");
	$hora=date("H:i:s");
	$num=0;
	$numi=0;
	$hist="00000";
	$artcod="NO APLICA";
	$codInt="0002";
	if($err == "") {
		$codSis=mysql_errno($conex);
		$descSis=str_replace("'","*",mysql_error($conex));
	}
	else  {
		$codSis=$err;
		$descSis=$err;
	}
	if(isset($user))
	unset($user);
	$tipTrans=".";
	include_once("pda/error.php");

	echo "<CENTER>EL CODIGO NO EXISTE O NO PERTENECE A NINGUN<br>CENTRO DE COSTOS<BR>QUE TENGA HABILITADA LA FACTURACIÓN POR MATRIX";
	ECHO "<BR/><B><A HREF='pda_ingreso.php'>Retornar</a>";
}
}
else
{


	?>	<form name="carga" action="" method=post>	<?php
	if(!isset($tipTrans)){
		$query="select * from farmpda_000014 where Ccocod='".substr($cc,0,4)."' and Ccofac='on' ";
		$err = mysql_query($query,$conex);
		$numCco = mysql_num_rows($err);
		if($numCco >0)	{
			$row=mysql_fetch_array($err);
			$fuente=$row['Ccofde'];
			$tipTrans='D';
			$negativo=$row['Cconce'];
			// 2006-07-10
			$preHistM=$row['Ccophm'];
			if($row['Ccoapl'] == 'on')
			$invetras=true;
			else
			$invetras=false;
			if($row['Ccohcr'] == 'on')
			$histCero=true;
			else
			$histCero=false;
			//FIN 2006-07-10
		}
	}

	/********************************************
	*	Aqui empieza el Programa de grabación	*
	********************************************/

	/*Comprobacion de existencia de UNIX*/
	if(!isset($conex_o)) {
		include_once("pda/socket.php");
	}else if($conex_o != false){
		$conex_o = odbc_connect('inventarios','','');
	}
	echo "<input type='hidden' name='conex_o' value='".$conex_o."'>";

	echo "<center><table border=0>";
	echo "<tr><td align='center' colspan='2'><b><font color=#ff0000>DEVOLUCIONES</b></td></tr>";//CAMBIO 2005-08-31
	$warning="";
	$color="#cccccc";
	IF(!isset($todos))
	$todos="";
	IF(!isset($show))
	$show="";
	$hora=date("H:i:s");


	if($conex_o != 0 )//and isset($artcod) and $artcod != "")
	/*Existe una conexión con el servidor del UNIX*/
	{
		$odbc="ACTIVO";
		if(!isset($pacnom)){
			/*Buscar si existe el numero de historia*/
			include_once("pda/validacion_hist.php");
		}
		if($pacnom != "." and $cc != "" and isset($artcod) and $artcod != "")
		/*Existe el Paciente y el CC*/
		{
			include_once("pda/validacion_articulo.php");
		}
		else if($pacnom != "." and $cc != "" and isset($artcod))
		{
			//$warning=$warning."NO ENTRO NINGUN ARTICULO<BR>";
			$artValido = false;
			$artcod="NO APLICA";
			$codInt="2001";
			$codSis="NO APLICA";
			$descSis="NO APLICA";
			include_once("pda/error.php");
			unset($artcod);
		}
		if(isset($artValido) and $artValido and !$cantVariable)
		/*Si esta set artgen implica que el codigo existe en el sistema Ademas:
		isset $pacnom and $pacnom != 0 osea que la historia esta activa
		$cc != "" es decir el centro de costos existe*/
		{
			/*YA ESTAN HECHAS TODAS LAS VERIFICACIONES
			Procedemos a ingresar los registros al sistema*/

			include_once("pda/validacion_dev.php");
			$artValido = ValidacionDev($artcod, $cant, $hist, $cc, $error);
			if($artValido) {

				if(!isset($num) or (isset($date) and  ($date != date("Y-m-d")))) {
					/*Si no existe num, es el primer articulo que entro
					para esta historia esta vez, por lo que	debo ir a buscar a la tabla numera
					o la fecha cambio durante el ingreso de los articulos*/
					include_once("pda/numera.php");
				}
				/*ya que tengo num lo envio para la proxima*/
				echo "<input type='hidden' name='num' value='".($num)."'>";

				include_once("pda/registro_tablas.php");
			}else{
				$codInt ="0007";
				$codSis ="NO APLICA";
				$descSis ="NO APLICA";
				include_once("pda/error.php");
			}

		}
	}else if ( $conex_o == 0 )
	/*NO existe una conexión con el servidor del UNIX
	Por daño en la conexion ODBC*/
	{

		$programa = "devolucion.php";
		$odbc="INACTIVO";

		echo "<B>FUERA DE UNIX</B>";
		if(!isset($pacnom))	{
			$hist=trim($hist); //2006-04-28
			/*buscar el nombre del paciente en ;Matrix dado que no hay conexión
			con facturacion en el UNIX*/
			$query="select paciente from farmpda_000001 where historia='".$hist."'";
			$err=mysql_query($query,$conex);
			$numa=mysql_num_rows($err);
			if($numa > 0)
			{
				$row=mysql_fetch_row($err);
				$pacnom=$row[0];
				$pachab="";//2006-04-27
			}else{//2006-04-20
				if($hist == "") //2006-04-26
				$hist=false;  //2006-04-27
				$pacnom=".";
				echo "<tr><td align='center'><IMG SRC='/matrix/images/medical/root/cabeza.gif' align=center size=50% width=50%></td><tr>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffff00 LOOP=-1>NUMERO DE HISTORIA $hist ERRADO, INTENTELO NUEVAMENTE!!!</MARQUEE></FONT>";
				$date=date("Y-m-d");
				$num=0;
				$numi=0;
				$artcod="NO APLICA";
				$codInt="0001";
				$codSis="Problemas Query";
				$descSis="Problemas Query";
				include_once("pda/error.php");
			}//2006-04-20
		}
		if($hist and isset($artcod) and $artcod != "" and $artcod != "NO APLICA") { //2006-04-27
			/*Si esta set entonces no es la primeravez que se abre el programa,
			si es diferente de "" es porr que ingresaron algún articulo */
			$artuni="";
			include_once("pda/validacion_articulo_SinODBC.php");
			if(!isset($num) or (isset($date) and  ($date != date("Y-m-d")))) {
				include_once("pda/numera.php");
			}
			if($artValido and !$cantVariable) 	{ //2006-04-24
				/*No se valida la cantidad por si entro un codigo de proveedor y no hay grabado con ese codigo*/
				/* INSERTO EL REGISTRO del medicamento en la tabla*/
				$query1 = "INSERT INTO farmpda_000003 (medico, fecha_data, hora_data, Cc, Reg_num, historia, paciente, cod_articulo, descripcion_art, cantidad, Fuente,Seguridad) values ('farmpda', '".$date."', '".$hora."', '".substr($cc,0,4)."', '".$num."-".$numi."', '".trim($hist)."', '".$pacnom."', '".$artcod."', '".$artgen."-".$art1."', ".$cant.", 2, 'A-$percod')";
				$err1 = mysql_query($query1,$conex);
				if($err1 == 0 ){
					$codInt="1002";
					$codSis=mysql_errno();
					$descSis=str_replace("'","*",mysql_error());
					include_once("pda/error.php");
					echo "<input type='hidden' name='numo' value='".$numo."'>";
					echo "<input type='hidden' name='numi' value='".$numi."'>";
					$artgen="";
				}
			}
		}
		else if (isset($artcod) and $artcod == "" )
		{
			$artValido=false;
			$artcod="NO APLICA";
			$codInt="2001";
			$codSis="NO APLICA";
			$descSis="NO APLICA";
			include_once("pda/error.php");
			unset($artcod);
		}

	}//fin de $conex_o == 0
	include_once("pda/impresion_pantalla.php");
	echo "<tr><td bgcolor=#cccccc align='center'><font color=#000066><b><A HREF='devolucion.php?percod=$percod'>Retornar con Usuario</a> </font><br>";
	echo "<tr><td bgcolor=#cccccc align='center'><font color=#000066><b><A HREF='devolucion.php'>Retornar</a> </font><br>";
	echo "<input type='hidden' name='cc' value='".$cc."'>";
	if($conex_o == false) {
		echo "<input type='hidden' name='conex_o' value='$conex_o'>";
	}
	echo "<input type='hidden' name='tipTrans' value='".$tipTrans."'>";
	echo "<input type='hidden' name='fuente' value='".$fuente."'>";
	//2006-07-10
	echo "<input type='hidden' name='preHistM' value='".$preHistM."'>";
	echo "<input type='hidden' name='negativo' value='".$negativo."'>";
	echo "<input type='hidden' name='invetras' value='".$invetras."'>";
	//FIN 2006-07-10
	echo"<tr><td align=center bgcolor=#cccccc  ><input type='submit' value='ACEPTAR'></td></tr></form>";
	if(isset($pacnom))
	{
		echo"</font>";
		echo "<tr><td bgcolor=#cccccc align='center'><font color=#000066><b><A HREF='reporte.php?percod=$percod&amp;pac=".trim($hist)."&amp;cc=".$cc."&amp;tipTrans=".$tipTrans."&amp;conex_o=$conex_o'>Reporte dia paciente</a> </font><br>";
	}
}
	include_once("free.php");?>
</body>
</html>