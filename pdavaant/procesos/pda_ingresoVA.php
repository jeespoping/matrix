<html>
<head>
<title>MEDICAMENTOS V13.00</title>
<?php
include_once("conex.php"); 
if (!isset($user) and !isset($percod))
{?>
<script>
function ira(){document.carga.percod.focus();}
</script><?php
}
else
if (!isset($hist))
{?>
<script>
function ira(){document.carga.hist.focus();}
</script><?php
}
else if(!isset($artValido))
{?>
<script>
function ira(){document.carga.artcod.focus();}
</script><?php
}
?>

</head>
<BODY onload=ira() TEXT="#000066"  BGCOLOR="#FFFFFF">
<?php

/********************************************************
*		FACTURACIÓN DE ARTÍCULOS A PACIENTES			*
*********************************************************/

//==================================================================================================================================
//GRUPO						:PDA
//AUTOR						:Ana María Betancur V.
$wautor="Ana María Betancur V.";
//FECHA CREACIÓN			:2005-02-08
//FECHA ULTIMA ACTUALIZACIÓN 	:
$wactualiz="(Versión 2006-08-08)";
//DESCRIPCIÓN					: Valida sobre las tablas de INFORMIX inpaci (si el paciente esta activo), ivart-ivartcba (el articulo
//								  existe), ivsal (existe el saldo para el articulo y el cc) , ivarttar (que exista la tarifa para el
//								  articulo en ese año mes).  Ademas valida el cc sobre la tabla costosyp_000005 de Matrix.
//								  Si existe conexión ODBC con el INFORMIX realiza las comprobaciones mencionadas, usa el include
//								  numera para obtener un numero de registro y de lines y graba sobre itdro (INFORMIX) y farmpda_000001
//								  (MATRIX)la informacion que debe quedar en la factura.
//
//								  Si no existe conexion ODBC con el UNIX no se realizan las validaciones y se graba en farmpda_000001
//								  con Fuente=1, para que posteriormente reppda/ingreso_unix.php ingrese los articulos a itdro (UNIX).
//
//								  Las validaciones que se realizan en MATRIX son para ambos casos (con y sibn ODBC) y son las siguientes:
//								  Ademas valida el cc sobre la tabla costosyp_000005 de Matrix, tambien sobre la tabla farmpda_000002
//								  busca si el articulo tiene una cantidad diferente a 1, o si requiere de justificacion, formula, ambas
//								   o ninguna.
//
//								  Para ambos casos si existe un error tanto en las validaciones como en el ingreso de la informacíon a
//								  las tablas se llama al include error.
//------------------------------------------------------------------------------------------------------------------------------------------
//ACTUALIZACIONES
//	2006-08-08
//		Se cambia el n$num por $numCco en lo que tiene que ver con traer la info de los CCo de la bd.
//	2006-07-27
//		En el query de los centros de costos se reemplaza los campos pedidos en el select con *.
//	2006-06-20
//		Se cambia $tipGrab por $tipTrans
//		Se modifica todo lo referente a los centro de costos y su manejo, pues se extrae de la tabla farmpda_000014
//	2006-04-28
//		Se pone el trim(hist) cuando conex_o==0 , es decir esta fuera de UNIX.
//	2006-04-26
//		S encontro un bug, cuando conex_o==0 y entreban una historia vacia se gravaba un articulo con codigo NO APLICA, que al buscarlo en
//		farmpda_000003correspondia al NUTREN. Se soluciono anexando and hist != 'VACIA' en donde correspondia.
//		Tambien se subio el artuni="" para antes del llamado al include validacion_articulo_SinODBC.php
//	2006-04-20
//		Se modifica la seccion de validaciones cuando conex_o=0, es decir cuando no hay conexión con el ODBC, se pone todo lo qye respecta
//		a los artículos en el include validacion_articulo_SinODBC.php, lavalidación de que no ingresen una histora vacia se hizo dentro del
//		programa.
//	2005-05-20
//		Pide codigo de nomina al usuario, si es de 6 digitos (tal y como aparece en el codigo de barras del carnet) lo recorta a 5, con
//		el codigo define a que si el cc de costos es productivo o pertenece a farmacia, el cc 1320 (neumologia) tiene condiciones especiales,
//		deben ingresar el CC sobre el cual estan grabando.//
//		La mayoria de los procesos de que hacia el programa han sido trasladado a includes que devolucion.php tambien usa.
//		Los includes son pda/socket.php para comprobar la conexion al unix,
//		pda/validacion_hist.php, para validar que la historia exista y extraer datos del paciente,
//		pda/validacion_articulo.php pra validar que el articulo sea correcto y traer la informacion necesaria del mismo,
//		pda/registro_tablas.php para registrar la informacion en las tablas respectivas,
//		pda/articulos_especiales.php, para buscar en MATRIX informacion del articulo si la hay,
//		pda/impresion_pantalla.php para imprimir la info en pantalla.
//
//	2005-08-31
//			Debe mostrar la habitación del paciente, esta en la variable $pachab
//
//	2005-08-30
//			Cuando el CC sea 1050 la fuente es GD para grabación y  DD para devolución.
//
//--------------------------------------------------------------------------------------------------------------------------------------
//TABLAS
//	root_000025
//	farmpda_000001
//	farmpda_000014

/**
 * @modified 2007-02-28 Se modifican los if del javascrit por no esta funcionando adecuadamente
 * @modified 2007-02-14 Se Se cambia isset($artgen) and $artgen != "" por $artValido para llamar al include pda/registro_tablas.php
 * @modified 2007-02-14 se comenta el llamado al include pda/tiempos.php
 */





/* COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente-HISTORIA- Y CC )*/
$warning="";
$odbc="NO INFO";
if (isset($user) and  !isset($percod)){
	$percod=substr($user,1);
}
if(!isset($percod) )
{
	/*PANTALLA DE INGRESO DE NUMERO DE CC**/
	?>		<form name="carga" action="" method=post>		<?php
	echo "<center><table border=0 width=300>";
	echo "<tr><td align=center ><b>CLÍNICA LAS AMERICAS </b></td></tr>";
	echo "<tr><td bgcolor=#cccccc ><font color=#000066><b>CODIGO NOMINA: </font>";
	?>	<input type='text' cols='10' name='percod'></td></tr>	<?php
	echo"<tr><td align=center bgcolor=#cccccc  ><input type='submit' value='ACEPTAR'></td></tr></form>";
	/*FIN::::PANTALLA DE INGRESO DE nUMERO DE CC*/
}
elseif (!isset($hist))
{
	?>		<form name="carga" action="" method=post>		<?php
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
}else{
	if(!isset($conex_o))
	{
		include_once("pda/socket.php");
	}else if($conex_o != false){
		$conex_o = odbc_connect('inventarios','','');
	}
	?>	<form name="carga" action="" method=post>	<?php
	/********************************************
	*	Aqui empieza el Programa de grabación	*
	********************************************/
	//Buscar la fuente para el CCo
	//AnitaDice: esto deberia esr en una función
	if(!isset($tipTrans)){
		$query="select * from farmpda_000014 where Ccocod='".substr($cc,0,4)."' and Ccofac='on' "; //2006-07-27
		$err = mysql_query($query,$conex);
		$numCco = mysql_num_rows($err);
		if($numCco >0)	{
			$row=mysql_fetch_array($err);
			$fuente=$row['Ccofca'];
			$tipTrans='C';
			$negativo=$row['Cconce'];
			//2006-07-10
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

	/*Comprobacion de existencia de UNIX*/
	echo "<center><table border=0>";
	$warning="";
	$color="#cccccc";
	IF(!isset($todos))
	$todos="";
	IF(!isset($show))
	$show="";
	$hora=date("H:i:s");
	$tiempo['Especiales']=0;
	$tiempo['Validacion']=0;
	$tiempo['Matrix']=0;
	$tiempo['Invetras']=0;
	$tiempo['Odbc']=0;
	$tiempo['Error']=0;
	if($conex_o != 0 ) {
		/*Existe una conexión con el servidor del UNIX*/
		$odbc="ACTIVO";
		if(!isset($pacnom)){
			/**
			 * Buscar si existe el numero de historia
			 */
			include_once("pda/validacion_hist.php");
			/**
			 * Verifica si es necesario traer de UNIX las nuvas tablas de artículos.
			 */
			include_once("pda/ivartcba.php");
		}
		if($pacnom != "." and $cc != "" and isset($artcod) and $artcod != "") {
			/*Existe el Paciente y el CC*/
			/*Hace todas las validaciones,en artcba, ivartcba, en ivarttar, ivsal.
			Adicionalmente llama internamente al include articulos_especiales.php
			que se fija si es un articulo especial o no	*/
			include_once("pda/validacion_articulo.php");
		}
		else if($pacnom != "." and $cc != "" and isset($artcod))
		{
			//NO ENTRO NINGUN ARTICULO
			$artValido = false;
			$artcod="NO APLICA";
			$codInt="2001";
			$codSis="NO APLICA";
			$descSis="NO APLICA";
			include_once("pda/error.php");
			unset($artcod);
		}

		if(isset($artValido) and $artValido and !$cantVariable) {
			/*Si esta set artgen implica que el codigo existe en el sistema Ademas:
			isset $pacnom and $pacnom != 0 osea que la historia esta activa
			$cc != "" es decir el centro de costos existe*/

			if(!isset($num) or (isset($date) and  ($date != date("Y-m-d")) ) ) {
				/*Si no existe num, es el primer articulo que entro
				para esta historia esta vez, por lo que	debo ir a buscar a la tabla numera
				o la fecha cambio durante el ingreso de los articulos*/
				include_once("pda/numera.php");
			}
			/*ya que tengo num lo envio para la proxima*/
			echo "<input type='hidden' name='num' value='".($num)."'>";

			include_once("pda/registro_tablas.php");
		}
	}else{
		/*NO existe una conexión con el servidor del UNIX
		Por daño en la conexion ODBC*/
		$odbc="INACTIVO";
		echo "<B>FUERA DE UNIX</B>";
		if(!isset($pacnom)) {
			/*buscar el nombre del paciente en ;Matrix dado que no hay conexión
			con facturacion en el UNIX*/
			$hist=trim($hist); //2006-04-28
			if($hist != "") { //2006-04-20
				$query="select paciente from farmpda_000001 where historia='".$hist."'";
				$err=mysql_query($query,$conex);
				$numa=mysql_num_rows($err);
				if($numa > 0) {
					$row=mysql_fetch_row($err);
					$pacnom=$row[0];

				}else{
					$pacnom="NOMBRE NO ENCONTRADO <b>(HISTORIA:".$hist.")</b>";
				}
				$pachab="";//		CAMBIO		2005-08-31
			}else{//2006-04-20
				// Entro una historia vacia.
				$pacnom=".";
				$hist=false;
				echo "<tr><td align='center'><IMG SRC='/matrix/images/medical/root/cabeza.gif' align=center size=50% width=50%></td><tr>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffff00 LOOP=-1>NO INGRESO HISTORIA, INTENTELO NUEVAMENTE!!!</MARQUEE></FONT>";
				$date=date("Y-m-d");
				$num=0;
				$numi=0;
				$artcod="NO APLICA";
				$codInt="0001";
				$codSis="NO APLICA";
				$descSis="NO APLICA";
				include_once("pda/error.php");
			}//2006-04-20
		}

		if(isset($artcod) and $hist){
			if($artcod != "" ) 	{
				/*Si esta set entonces no es la primeravez que se abre el programa,
				si es diferente de "" es porr que ingresaron algún articulo */
				/*Primero se busca si esta en la tabla de articulos especiales*/
				/*Compruebo si esta en la lista de articulos especiales de Matrix*/
				$artuni="";  //2006-04-26
				include_once("pda/validacion_articulo_SinODBC.php"); //2006-04-26
				if(!isset($num) or (isset($date) and  ($date != date("Y-m-d")))) {
					include_once("pda/numera.php");
				}
				if($artValido and !$cantVariable) {
				/* INSERTO EL REGISTRO del medicamento en la tabla*/
				$query1 = "INSERT INTO farmpda_000001 (medico,fecha_data,hora_data,Cc,Reg_num,historia,paciente,cod_articulo,descripcion_art,cantidad,reposicion,recibido,justificacion,fuente,seguridad) values ('farmpda','".$date."','".$hora."','".substr($cc,0,4)."','".$num."-".$numi."','".trim($hist)."','".$pacnom."','".$artcod."','".$artgen."-".$art1."',".$cant.",'0000-00-00','0000-00-00','".$just."',1,'A-$percod')";
				$err1 = mysql_query($query1,$conex);
				if($err1 == 0 )	{
					//$warning= $warning."EL MEDICAMENTO NO FUE INGRESADO A LA FACTURACIÓN<BR>";
					$codInt="1002";
					$codSis=mysql_errno();
					$descSis=str_replace("'","*",mysql_error());
					include_once("pda/error.php");
					echo "<input type='hidden' name='numo' value='".$numo."'>";
					echo "<input type='hidden' name='numi' value='".$numi."'>";
					$artValido = false;
				}
			}
			}else {//2006-04-26
				//NO INGRESO NINGUN ARTICULO
				$artValido = false;
				$artcod="NO APLICA";
				$codInt="2001";
				$codSis="NO APLICA";
				$descSis="NO APLICA";
				include_once("pda/error.php");
				unset($artcod);
			}
		}
	}//fin de $conex_o == 0

	include_once("pda/impresion_pantalla.php");
	echo "<tr><td bgcolor=#cccccc align='center'><font color=#000066><b><A HREF='pda_ingreso.php?percod=$percod'>Retornar con Usuario</a> </font><br>";
	echo "<tr><td bgcolor=#cccccc align='center'><font color=#000066><b><A HREF='pda_ingreso.php'>Retornar</a> </font><br>";
	echo "<input type='hidden' name='cc' value='".$cc."'>";
	if($conex_o == false) {
		echo "<input type='hidden' name='conex_o' value='$conex_o'>";
	}
	echo "<input type='hidden' name='tipTrans' value='".$tipTrans."'>";
	echo "<input type='hidden' name='fuente' value='".$fuente."'>";
	echo "<input type='hidden' name='negativo' value='".$negativo."'>";
	//2006-07-10
	echo "<input type='hidden' name='preHistM' value='".$preHistM."'>";
	echo "<input type='hidden' name='invetras' value='".$invetras."'>";
	//FIN 2006-07-10
	echo"<tr><td align=center bgcolor=#cccccc  ><input type='submit' value='ACEPTAR'></td></tr></form>";
	if(isset($pacnom))
	{
		echo"</font>";
		echo "<tr><td bgcolor=#cccccc align='center'><font color=#000066><b><A HREF='reporte.php?pac=".trim($hist)."&amp;cc=".$cc."&amp;tipTrans=".$tipTrans."&amp;percod=$percod'>Reporte dia paciente</a> </font><br>";

	}
	/*	if(isset($artcod) and $artcod != "" and $hist!='VACIA') //??????????????????????????????????????REVISAR
	//anitalavalatina include_once('pda/tiempos.php');
	*/
}

	include_once("free.php");?>
</body>
</html>