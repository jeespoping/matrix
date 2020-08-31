<html>
<head>
<title>Ingresar Itdro</title>
</head>
<body BGCOLOR="">
<?php
include_once("conex.php");
/*************************************************
*  INGRESO AL UNIX DE MATERIAL Y MEDICAMENTOS	 *
*		A PARTIR DE TABLAS DEL MATRIX			 *
*************************************************/

//==================================================================================================================================
//GRUPO						:PDA
//AUTOR						:Ana María Betancur V.
$wautor="Ana María Betancur V.";
//FECHA CREACIÓN			:2005-02-08
//FECHA ULTIMA ACTUALIZACIÓN 	:
$wactualiz="(Versión 2007-02-06)";
//DESCRIPCIÓN					:
//-----------------------------------------------------------------------------------------------------------------------------------
//ACTUALIZACIONES
//	2006-07-17
//	Se quitan los llamados del include validaciones y se reemplazan por los llamados al include a validacion_hist.php y
//	validacion_articulo.php
//	2006-07-14
//	Se hace unset(artgen) para que cuando no encuentre el artículo no quede setiado con la información del anterior.
//	Se modifica para el else del if(	ok != "ok"), para que valide primero si artgen esta vacio para no perder información, es decir
//	para que muestre el nombre del articulo si se conoce aunque no se pueda realizar la transaccion correspondiente por otro motivo.
//	Se crea la variable $art1 para nunca perder la indformación del código quoriginal que paso el usuario.
//	2006-07-04
//	Se cambia $tipGrab por $tipTrans
//	Se modifica todo lo referente a los centro de costos y su manejo, pues se extrae de la tabla farmpda_000014 en la function CC
//2006-04-24
//	Se modifica para que muestre también los Centros de costos.
//2005-08-30
//	Cuando el CC sea 1050 la fuente es GD para grabación y  DD para devolución.
//------------------------------------------------------------------------------------------------------------------------------------------
//TABLAS
//	farmpda_000001
//	farmpda_000003
//	farmpda_000014
//	itdro
//	invetras_000003
/**
 * @modified 2007-02-28 $artgen se llena desde el principio , al igual que $negativoArt, pues el primer carácter de $row['Descripcion_art'] indica si permite Negativos (N) o si no los permite (P)
 * @modified 2007-02-28	Se cambia $row['Paciente'] por $pacnom en la impresion, para que aparesca el pantalla el nombre del paciente así no hubiera sido identificado en la grabación original.
 * @modified 2007-02-20 Cambian las validaciones, ya no se verifica si el registro ya esta en UNIX.
 * @modified 2007-02-15 Se modifica la validación prodia de las devoluciones.
 * @modified 2007-02-15 Se empieza a usar la variable $artValido con el fin de no validar con $artgen,  y se disminuyen los if y else de validación, y 
 */

echo "<center>";
echo "<table border=0 align=center>";
echo "<tr><td align=center bgcolor='#cccccc'><font size='5'>Ingresar a Itdro</font></a></tr></td>";
echo "<tr><td align=center bgcolor='#cccccc'><font size='3'> <b>ingreso_unix.php ".$wactualiz."</b></font></tr></td></table><br><br>";
echo "</center>";

/**
 * Función que busca todos los atribustos del centro de costos y va generando un array
 * con todos los cc y sus atributos con el fin de no recurrir  a la BD para cada cc
 *
 * @param String[4] 	$cc 		código del centro de costos a buscar
 * @param String[1] 	$tipTrans 	tipo de transacción C (cargos) o D (devolución)
 * @param String[][]	$CC		I	nformación de los diferentes centros de costos
 * @param String[2]		$fuente		Fuente de grabación	del cc para ese tipo de transacción
 * @param Bool			$negativo	Si el cc buscado permite negativos
 */
function CC($conex,$cc,$tipTrans,&$CC,&$fuente,&$negativo) {
	$num=count($CC['cco']);
	$i=0;
	$encontrado=false;
	While($i<$num and $encontrado == true){
		if($cc == $CC['cco'][$i]){
			$encontrado=true;
			if($tipTrans == 'C')
			$fuente=$CC['fca'][$i];
			else
			$fuente=$CC['fcd'][$i];
			$negativo=$CC['neg'][$i];
		}
		$i++;
	}
	if(!$encontrado) {
		$query="select * from farmpda_000014 where Ccocod='".substr($cc,0,4)."' and Ccofac='on' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num >0)	{
			$row=mysql_fetch_array($err);
			$CC['cco'][$num]=$row['Ccocod'];
			$CC['fca'][$num]=$row['Ccofca'];
			$CC['fde'][$num]=$row['Ccofde'];
			$CC['neg'][$num]=$row['Cconce'];
			if($tipTrans == 'C')
			$fuente=$CC['fca'][$num];
			else
			$fuente=$CC['fde'][$num];
			$negativo=$row['Cconce'];
		}
		//else ERROR
	}
}
$programa="ingreso_unix.php";

echo "<meta http-equiv='refresh' content='60;url=ingreso_unix.php'>";





$conex_o = odbc_connect('inventarios','','');
IF($conex_o != 0)
{
	$percod="sistema";
	$negativo="";
	$fuente="";
	$numAnt="";
	/*DEBE EXISTIR UNA CONEXION CON EL SERVIDOR DEL UNIX
	PARA QUE SEA POSIBLE HACER LAS VALIDACIONES NECESARIAS*/
	$query="select * from farmpda_000001 where fuente='1' order by historia";
	$err_ppal=mysql_query($query,$conex);
	$num_ppal = mysql_num_rows($err_ppal);
	if($num_ppal>0)
	{
		$odbc="ACTIVO";
		$tipTrans='C';
		echo "<table border=1 align='center' width=750>";
		echo "<TR><td width=75 colspan='10' align='center'><font size=4 face='arial'><b>INGRESOS<b></td></tr>";
		echo "<TR><td width=75><font size=2 face='arial'><b>CC</td>";
		echo "<td width=75><font size=2 face='arial'><b>FECHA</td>";
		echo "<td ><font size=2 face='arial'><b>HORA</td>";
		echo "<td ><font size=2 face='arial'><b>REG.</td>";
		echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
		echo "<td ><font size=2 face='arial'><b>PACIENTE</td>";
		echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
		echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
		echo "<td ><font size=2 face='arial'><b>CANT.</td>";
		echo "<td ><font size=2 face='arial'><b>OK</td></TR>";
		for($ppal=0;$ppal<$num_ppal;$ppal++)
		{
			$row=mysql_fetch_array($err_ppal);
			$color="#ffffff";
			$warning="";
			$date=$row["Fecha_data"];
			$hora=$row["Hora_data"];
			$cc=$row["Cc"];
			$exp=explode("-",$row["Reg_num"]);
			$num=$exp[0];
			$numi=$exp[1];
			if($num!=$numAnt){
				$numAnt=$num;
				CC($conex,$cc,$tipTrans,&$CC,&$fuente,&$negativo);
			}
			$hist=$row["Historia"];
			$pacnom="";
			$cant=$row["Cantidad"];
			$artcod=$row["Cod_articulo"];
			$ini=explode("-",$row["Seguridad"]);
			$usuario=$ini[1];
			$ok="ok";
			$exp=explode('-',$row["Descripcion_art"]);
			$art1=$exp[count($exp)-1];
			$artgen=substr($exp[0],2);//2007-02-28
			unset($artValido);
			if(substr($row["Descripcion_art"],0,1) == "P")
			$negativoArt=false;
			else
			$negativoArt=true;


			/*Buscar que no este el registro en itdro*/
			/*$queryo= "select * from itdro where dronum='".$num."' and drolin='".$numi."'";
			$err_o=odbc_do($conex_o,$queryo);
			if(!odbc_fetch_row($err_o))	{
			*/
			include_once("pda/validacion_hist.php");

			IF ($ok == "ok") {
				include_once("pda/validacion_articulo.php");

				if($artValido)	{	/*Artículo válido*/

					/*INGRESAR EL REGISTRO A ITDRO*/
					$queryo= "insert into itdro values (".$num.", ".$numi.", '".$fuente."', '".substr($date,0,4)."', '".substr($date,5,2)."', '".str_replace("-","/",$date)."',  ".$hist.", '".$cc."', '".$cc."', '".$artcod."' , ".$cant.", 'S', '".$date." ".$hora."', 'facadm','','') ";
					$err_o=odbc_do($conex_o,$queryo);

					if(odbc_num_rows($err_o) > 0) {
						/*Si fue insertado en itdro*/
						/*Ingresar a invetras*/

						if( substr($cc,0,4) != '1050' or substr($cc,0,4) != '1051')
						{
							$ronda=date("g - A", mktime(substr($row["Hora_data"],0,2),substr($row["Hora_data"],3,2),substr($row["Hora_data"],6,2),substr($row["Fecha_data"],5,2),substr($row["Fecha_data"],8,2),substr($row["Fecha_data"],0,4)));
							$querym = "INSERT INTO invetras_000003 (medico, fecha_data, hora_data, Ronda, Historia, Ingreso, Articulo, Descripcion, Cantidad, Usu_ult_mvto, Aprobado, Activo, Cco,Seguridad) values ('invetras', '".$date."', '".$hora."', '".$ronda."' , '".$hist."', '$pacnum', '".$artcod."', '".$artgen."', ".$cant.", $usuario, 'Off', 'A','".$cc."', 'C-invetras')";
							$err1 = mysql_query($querym,$conex);
							if($err1 == "")
							{
								/*No ingreso a invetras*/
								$codInt="1004";
								$codSis=mysql_errno();
								$descSis=str_replace("'","*",mysql_error());
								include_once("pda/error.php");
							}
						}
						/*hacer el update en Matrix*/
						$query1="update farmpda_000001 set fuente='".$fuente."', paciente='".$pacnom."', Cod_articulo='".$artcod."', Descripcion_art='".$artgen."-".$art1."', Cantidad='".$cant."' where id='".$row["id"]."' ";
						$err1=mysql_query($query1,$conex);
						if($err1 == "") {						
							echo "NO FUE POSIBLE HACER EL UPDATE EN FARMPDA_000001 DEL REGISTRO ".$row["Reg_num"].",<br> PERO SI INGRESO A ITDRO";
							$color="#ff0000";
							$ok="NO PASO";
							/*No ingreso a invetras*/
							$codInt="1005";
							$codSis=mysql_errno();
							$descSis=str_replace("'","*",mysql_error());
							include_once("pda/error.php");
						}else					{
							$ok="ok";
							$color="#ffffff";
						}
					}	else	{
						/*2007-02-20*/
						$queryo= "select * from itdro where dronum='".$num."' and drolin= '".$numi."' ";
						$err_o=odbc_do($conex_o,$queryo);
						if(!odbc_fetch_row($err_o))	{
							$ok = "NO FUE POSIBLE INGRESAR EL REGISTRO ".$row["Reg_num"]." A ITDRO";
						}else {
							$ok = $row["Reg_num"]." NO INGRESO A ITDRO, YA EXISTE UN REGISTRO CON IGUAL NUMERACIÓN";
						}
					}

				}
			}
			echo "<TR><td bgcolor='".$color."'><font size=2 face='arial'><b>".$cc."</b></td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$date."</td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$hora."</td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row["Reg_num"]."</td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$hist."</td>";
			echo "<td bgcolor='".$color."'><font size=1 face='arial'>".$pacnom."</td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$artcod."</td>";
			echo "<td bgcolor='".$color."'><font size=1 face='arial'>".$artgen."</td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$cant."</td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$ok."</td></TR>";
		}
	}else{
		echo "NO HAY ARTICULOS PARA INGRESAR A ITDRO CON FUENTE 11 O GD";
	}

	/********************************************************************************
	*********************************************************************************
	Buscar las devoluciones que no ingresaron a itdro
	*********************************************************************************/
	$query="select * from farmpda_000003 where fuente='2' order by historia";
	$err_ppal=mysql_query($query,$conex);
	$num_ppal = mysql_num_rows($err_ppal);
	if($num_ppal>0)
	{
		include_once("pda/validacion_dev.php");
		$odbc="ACTIVO";
		$tipTrans='D';
		$negativo="";
		$fuente="";
		$numAnt="";
		echo "<table border=1 align='center' width=750>";
		echo "<TR><td width=75 colspan='10' align='center'><font size=4 face='arial'><b>DEVOLUCIONES</b></td></tr>";
		echo "<TR><td width=75><font size=2 face='arial'><b>CC</td>";
		echo "<td width=75><font size=2 face='arial'><b>FECHA</td>";
		echo "<td ><font size=2 face='arial'><b>HORA</td>";
		echo "<td ><font size=2 face='arial'><b>REG.</td>";
		echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
		echo "<td ><font size=2 face='arial'><b>PACIENTE</td>";
		echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
		echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
		echo "<td ><font size=2 face='arial'><b>CANT.</td>";
		echo "<td ><font size=2 face='arial'><b>OK</td></TR>";
		for($ppal=0;$ppal<$num_ppal;$ppal++)
		{
			$row=mysql_fetch_array($err_ppal);
			$cc=$row["Cc"];
			$hist= $row["Historia"];
			$pacnom="";
			$date=$row["Fecha_data"];
			$hora=$row["Hora_data"];
			$odbc="ACTIVO";
			$exp=explode("-",$row["Reg_num"]);
			$num=$exp[0];
			$numi=$exp[1];
			if($num!=$numAnt){
				$numAnt=$num;
				CC($conex, $cc,$tipTrans,&$CC,&$fuente,&$negativo);
			}
			$cant=$row["Cantidad"];
			$artcod=$row["Cod_articulo"];
			$ini=explode("-",$row["Seguridad"]);
			$usuario=$ini[1];
			$exp=explode('-',$row["Descripcion_art"]);
			$art1=$exp[count($exp)-1];
			$artgen=substr($exp[0],2);//2007-02-28
			if(substr($row["Descripcion_art"],0,1) == "P")
			$negativoArt=false;
			else
			$negativoArt=true;

			if(substr($row["Descripcion_art"],0,9) == 'VERIFIQUE') {
				$just = "99-NO ENCONTRADO";
			}
			unset($artValido);
			$warning="";
			$color="#ffffff";
			$ok="ok";

			/*Buscar que no este el registro en itdro*
			$queryo= "select * from itdro where dronum='".$num."' and drolin= '".$numi."' ";
			$err_o=odbc_do($conex_o,$queryo);
			if(!odbc_fetch_row($err_o))	{
			*/
			include_once("pda/validacion_hist.php");

			IF ($ok == "ok") {
				include_once("pda/validacion_articulo.php");

				if($artValido)	{	/*Artículo válido*/
					$artValido=ValidacionDev($artcod, $cant, $hist, $cc, $error);

					if($artValido) {

						$color="#ffffff";

						if($cant != 0) {
							$queryo= "insert into itdro values (".$num.",".$numi.", '".$fuente."', '".substr($date,0,4)."', '".substr($date,5,2)."', '".str_replace("-","/",$date)."', ".$hist.", '".$cc."' ,'".$cc."', '".$artcod."' , ".$cant.", 'S', '".$date." ".$hora."', 'facadm', '', '') ";//		CAMBIO 2005-08-30

							$err_o=odbc_do($conex_o,$queryo);
							if(odbc_num_rows($err_o) > 0) {
							
								if(substr($cc,0,4) != '1050' or substr($cc,0,4) != '1051')	{
									$ronda=date("g - A", mktime(substr($row["Hora_data"],0,2),substr($row["Hora_data"],3,2),substr($row["Hora_data"],6,2),substr($row["Fecha_data"],5,2),substr($row["Fecha_data"],8,2),substr($row["Fecha_data"],0,4)));

									$querym = "INSERT INTO invetras_000003 (medico, fecha_data, hora_data, Ronda, Historia, Ingreso, Articulo, Descripcion, Cantidad, Usu_ult_mvto, Aprobado, Activo, Cco,Seguridad) values ('invetras', '".$date."', '".$hora."', '".$ronda."' , '".$hist."', '$pacnum', '".$artcod."', '".$artgen."', -".$cant.", $usuario, 'Off', 'A','".$cc."', 'C-invetras')";
									$err1 = mysql_query($querym,$conex);
									if($err1 == "") {
										/*No ingreso a invetras*/
										$codInt="1004";
										$codSis=mysql_errno();
										$descSis=str_replace("'","*",mysql_error());
										include_once("pda/error.php");
									}
								}

								/*Si fue insertado en itdro hacer el update en Matrix*/
								$query1="update farmpda_000003 set fuente='".$fuente."',Cod_articulo='".$artcod."',Descripcion_art='".$artgen."-".$art1."', Cantidad='".$cant."' where Reg_num='".$row["Reg_num"]."'  and id='".$row["id"]."' ";//		CAMBIO 2006-07-14
								$err1=mysql_query($query1,$conex);
								if($err1 == "")	{
									
									echo "NO FUE POSIBLE HACER EL UPDATE EN FARMPDA_000003 DEL REGISTRO ".$row["Reg_num"].",<br> PERO SI INGRESO A ITDRO";
									$color="#ff0000";
									$ok="NO PASO, NO UPDATE";
									$codInt="1006";
									$codSis=mysql_errno();
									$descSis=str_replace("'","*",mysql_error());
									include_once("pda/error.php");
								}	else	{
									$ok="ok";
									$color="#ffffff";
								}
							}
							else{
								$queryo= "select * from itdro where dronum='".$num."' and drolin= '".$numi."' ";
								$err_o=odbc_do($conex_o,$queryo);
								if(!odbc_fetch_row($err_o))	{
									$ok = "NO FUE POSIBLE INGRESAR EL REGISTRO ".$row["Reg_num"]." A ITDRO";
								}else {
									$ok = $row["Reg_num"]." NO INGRESO A ITDRO, YA EXISTE UN REGISTRO CON IGUAL NUMERACIÓN";
								}
							}

							/*FIN DE INGRESO DE LA INFORMACION*/
						}/*La cantidad a devolver excede la ingresada*/
						else{
							/*Modifico Matrix para que no se siga ingresando
							grabo el error correspondiente	*/
							$query1="update farmpda_000003 set fuente='".$fuente."',Cod_articulo='".$artcod."',Descripcion_art='".$artgen."' where id='".$row["id"]."' ";
							$err1=mysql_query($query1,$conex);
							/*No ingreso a invetras*/
							if($err1 != "")	{
								$codInt="1007";
								$codSis="NO APLICA";
								$descSis="NO APLICA";
								include_once("pda/error.php");
							}
						}
					}else  {
						$ok= $error['ok'];
						$color= $color['ok'];
					}
				}
			}

			echo "<TR><td bgcolor='".$color."'><font size=2 face='arial'><b>".$cc."</b></td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$date."</td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$hora."</td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row["Reg_num"]."</td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$hist."</td>";
			echo "<td bgcolor='".$color."'><font size=1 face='arial'>".$pacnom."</td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$artcod."</td>";
			echo "<td bgcolor='".$color."'><font size=1 face='arial'>".$artgen."</td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$cant."</td>";
			echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$ok."</td></TR>";
		}
	}else{
		echo "<BR>NO HAY ARTICULOS PARA INGRESAR A ITDRO CON FUENTE 12";
	}
}else{
	echo "NO EXISTE CONEXION CON EL SERVIDOR.";
}

?>
</body>
</html>