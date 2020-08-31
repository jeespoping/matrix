<html>
<head>
<title>ADMISIÓN</title>
<style type="text/css">         
        <!--Fondo Azul no muy oscuro y letra blanca -->
        .tituloSup{color:#006699;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;font-size:10pt;}
        .tituloPeq{color:#006699;background:#FFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
        .tituloSup1{color:#57C8D5;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;font-size:10pt;}
        .titulo1{color:#FFFFFF;background:#006699;font-family:Arial;font-weight:bold;text-align:center;font-size:10pt;}
        .titulo2{color:#003366;background:#57C8D5;font-size:10pt;font-family:Arial;text-align:center;}
        .titulo3{color:#003366;background:#A4E1E8;font-size:10pt;font-family:Tahoma;text-align:center;}
        .texto{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
        .acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        .acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        .errorTitulo{color:#FF0000;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
        .alert{background:#FFFF00;color:#000000;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
        .warning{background:#FF6600;color:#000000;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
        .error{background:#FF0000;color:#000000;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}                   
        .textoA{color:#660066;background:#FFFFFF;font-size:10pt;font-family:Arial;}
</style>
  <script type="text/javascript">
  function enter()
  {
  	document.admision.submit();
  }

  function confirmar()
  {
  	if(confirm(" Esta seguro de que desea cancelar la admision?") )
  	{
  		document.admision2.cancelar.value=1;
  		document.admision2.submit();
  	}
  }
    </script>
      
</head>
<BODY BGCOLOR="#FFFFFF">
<?php
include_once("conex.php");                    


/**
 * PROGRAMA QUE REALIZA LA ADMISIÓN DE UN PACIENTE A UN SERVICIO<br>
 * 
 * Los parámetros de entrada que pide son el servicio que esta admitiendo ($serOrigen), la historia del paciente ($historia=$pac['his'])
 * el ingreso ($ingreso=$pac['ing']), el servicio destino $serSelected y la habitación en ese servicio ($hab).<br>
 * 
 * El programa, mediante la función Servicios, genera los servicios en el array $ser, $ser['I'] para los servicios de ingreso y  $ser['H'] para los servicios de destino 
 * que deben ser hospitalarios.<br>
 * 
 * Las validaciones realizadas por el programa son:<br>
 * * La historia no puede estar en la tabla 000018 con el ingreso seleccionado por que significaría que el paciente ya fue admitido con esa historia e ingreso.
 * * La historia debe estar activa en UNIX, es decir debe existir con ese ingreso en la tabla inpac de UNIX.
 * 
 * Laa transacciones que efectua el programa son:<br>
 * * A través de la funcion Validación UNIX registra el paciente en el sistema. Es decir que inserta, si hace falta, registros en las tablas de paciente
 *   root_000036, de origen root_000037, de ubicación de paciente 000018, de responsable 000016, segun la información que haya en UNIX.<br>
 * * Hace un Update en la tabla 000018 la habitación anterior (Ubihan) vacio, el servicio anterior (Ubisan) con el código del servicio de ingreso.<br>
 * * Hace un Update en la tabla 000018 la habitación actual (Ubisac) vacio, el servicio anterior (Ubihac) con el código del servicio de ingreso, y pone al paciente en proceso de traslado (Ubiprt='on').</br>
 * * Hace un update sobre el registro de la habitación del paciente para que se sepa que el paciente esta ahi (Habhis=$pac['his'], Habing=$pac['ing']), para que ya no este disponible (Habdis='off').<br>
 * * Hace un insert en la tabla 000032 donde ingrsa la información del centro de costos de destino en el censo
 * 
 * @modified 2007-12-19 Se graba en tabla 17 la admision como una entrega y se permite grabacion ambulatoria
 * @modified 2007-11-27 Se quita insert sobre tabla del censo diario
 * @modified 2007-09-27 Se hace la conexión odbc a facturación antes de llamar a ValidacionHistoriaUnix
 * @modified 2007-09-10 Se agrega el query que hace el insert a la tabla 000020 del censo
 * @modified 2007-09-10 Se modifica para que no necesite ingreso
 * @modified 2007-09-06 Se modifica para que ponga el como servicio anterior el servicio que hace el ingreso (000018.Ubisan).
 * @modified 2007-09-06 Se usa infoPaciente() para comprobar que el paciente no haya sido admitido.
 * @created 2007-08-02
 * 
 * @table 000020 SELECT, UPDATE
 * @table 000018 SELECT, UPDATE
 * @table 000011 SELECT
 * @table costosyp_000005 SELECT
 * 
 */



/**
 * Include donde se encuentran las funciones de validación de histosia
 */
include_once("movhos/validacion_hist.php");

include_once("movhos/otros.php");

function Servicios (&$ser)
{
	global $conex;
	global $bd;

	$tablaCco="costosyp_000005";

	$q = " SELECT ".$bd."_000011.Ccocod as cod, ".$tablaCco.".Cconom as nom, Ccoing, Ccohos "
	."       FROM ".$bd."_000011, ".$tablaCco." "
	."      WHERE (".$bd."_000011.Ccohos = 'on' or ".$bd."_000011.Ccoing = 'on') "
	."        AND ".$bd."_000011.Ccoest = 'on' "
	."        AND ".$tablaCco.".Ccocod = ".$bd."_000011.Ccocod ";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	echo mysql_error();
	if($num>0)
	{
		$i=0;
		$h=0;
		for($j=0;$j<$num;$j++)
		{
			$row= mysql_fetch_array($err);
			if($row['Ccohos'] == 'on')
			{
				$ser['H'][$h]['cod']=$row['cod'];
				$ser['H'][$h]['nom']=$row['nom'];
				$h++;
			}
			if($row['Ccoing'] == 'on')
			{
				$ser['I'][$i]['cod']=$row['cod'];
				$ser['I'][$i]['nom']=$row['nom'];
				$i++;
			}
		}
	}
}

function Habitaciones (&$hab, $cco)
{
	global $conex;
	global $bd;

	$q = " SELECT Habcod "
	."       FROM ".$bd."_000020 "
	."      WHERE ".$bd."_000020.Habcco = '".$cco."' "
	."        AND ".$bd."_000020.Habdis = 'on' "
	."        AND ".$bd."_000020.Habali = 'off' "
	."        AND ".$bd."_000020.Habest = 'on' ";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	echo mysql_error();
	if($num>0)
	{
		for($i=0;$i<$num;$i++)
		{
			$row= mysql_fetch_array($err);
			$hab[$i]=$row['Habcod'];
		}
	}
}

if(!isset($_SESSION['user']))
echo "error";
else
{
	

	


	connectOdbc(&$conex_o, 'inventarios');
	$usu['codM']=substr($user,2);

	echo "<table align='center' border='0'>";
	echo "<td class='tituloSup' >ADMISIÓN</td></tr>";
	echo "<td class='tituloPeq' >admision.php Version 2007-11-27<br><br></td></tr>";
	echo "<tr>";
	if($conex_o != 0)
	{
		if(!isset($cancelar) or $cancelar==0)
		{
			if(!isset($serOrigen))
			{
				Servicios(&$ser);
				echo "<form action='' method='POST' name='admision'>";
				echo "<td><center><table border='0' width=300>";
				//echo "<tr><td align=center class='titulo2' colspan='2'><b>DEVORLUCIÓN</b></td></tr>";
				echo "<tr>";
				echo "<td class='titulo2' ><b>Servicio que admite al Paciente: </b>";

				echo "<select name='serOrigen' onChange='enter()'>";
				$countSer=count($ser['I']);
				$select=false;
				for($i=0;$i<$countSer;$i++)
				{
					if(isset($serSelected) and substr($serSelected,0,4) == $ser['I'][$i]['cod'])
					{
						echo "<option selected>".$serSelected."</option>";
						$select=true;
					}
					else
					{
						echo "<option>".$ser['I'][$i]['cod']."-".$ser['I'][$i]['nom']."</option>";
					}
				}
				if($select)
				{
					echo "<option>Seleccionar ...</option>";
				}
				else
				{
					echo "<option selected  value=''>Seleccionar ...</option>";
				}
				echo "</select>";
			}
			else if(!isset($hab) or $hab == "")
			{
				/**
                         * Pide la historia y el ingreso
                         */
				if(!isset($historia) or $historia=='' or $serSelected!='')
				{
					if(!isset($historia) or $historia=='')
					{
						$historia="";
						$ingreso="";
					}

					Servicios(&$ser);
					echo "<form action='' method='POST' name='admision'>";
					echo "<td><center><table border='0' width=500>";
					echo "<tr>";
					echo "<td class='titulo2' ><b>N° Historia: </b><input type='text' size='8' name='historia' class='textoA' value='".$historia."'></td></tr><tr>";
					echo "<td class='titulo2' ><b>Servicio: </b>";
					echo "<select name='serSelected' onChange='enter()'>";
					$countSer=count($ser['H']);
					$select=false;
					for($i=0;$i<$countSer;$i++)
					{
						if(isset($serSelected) and substr($serSelected,0,4) == $ser['H'][$i]['cod'])
						{
							echo "<option selected>".$serSelected."</option>";
							$select=true;
						}
						else
						{
							echo "<option>".$ser['H'][$i]['cod']."-".$ser['H'][$i]['nom']."</option>";
						}
					}
					if(isset($serSelected) and $serSelected == 'AMBULATORIO')
					{
						echo "<option selected>AMBULATORIO</option>";
						$select=true;
					}
					else
					{
						echo "<option>AMBULATORIO</option>";
					}
					if($select)
					{
						echo "<option>Seleccionar ...</option>";
					}
					else
					{
						echo "<option selected  value=''>Seleccionar ...</option>";
					}
					echo "</select>";


					if(isset($serSelected) and $serSelected != "" and $serSelected!='AMBULATORIO')
					{


						echo "<br><b>Habitación: </b>";
						$ccoSelected['cod']=substr($serSelected,0,4);
						getCco(&$ccoSelected,"C",$emp);
						if($ccoSelected['urg'])
						{
							//El servicio de urgencias no tiene habitaciones
							echo "<input type='text' name='hab' value='NINGUNA' disabled >";
							echo "<input type='hidden' name='hab' value=' '  >";
						}
						else
						{

							Habitaciones(&$hab, substr($serSelected,0,4));
							echo "<select name='hab' onChange='enter()'>";
							$countHab=count($hab);
							echo "<option selected  value=''>Seleccionar ...</option>";
							for($i=0;$i<$countHab;$i++)
							{
								echo "<option>".$hab[$i]."</option>";
							}
						}
					}
					else if( isset($serSelected) AND $serSelected=='AMBULATORIO')
					{
						//El servicio de urgencias no tiene habitaciones
						echo "<input type='text' name='hab' value='NINGUNA' disabled >";
						echo "<input type='hidden' name='hab' value=' '  >";
					}

					echo "<input type='hidden' name='serOrigen' value='".$serOrigen."' >";

					echo"<tr><td  class='titulo2' colspan='2'><input type='submit' value='ACEPTAR'></td></tr>";
					echo "</form>";
					echo "</table>";
				}
				else
				{
					//consultamos donde esta el paciente
					/**
                 	* Aqui empieza la admisión del paciente a la institución.
             		*/

					$pac['his']=trim($historia);

					$ind=0;
					while ($ind==0)
					{
						if (substr($pac['his'],0,1)=='0')
						{
							$pac['his']=substr($pac['his'],1);
						}
						else
						{
							$ind=1;
						}
					}
					$pacEnMatrix=infoPaciente(&$pac, $emp);

					$pacEnMatrix=infoPaciente(&$pac, $emp);
					if(isset($pac['ing']))
					$ingMatrix=$pac['ing'];
					else
					$ingMatrix="";
					/**
                         * Se llama a la función ValidacionHistoriaUnix() con el fin de que haga el ingreso del paciente en las tablas necesarias
                         * para posteriormente llenar las que hacen falta.
                         */     
					$conex_f = odbc_connect('facturacion','','');
					$pacEnUNIX=ValidacionHistoriaUnix(&$pac,&$warning,&$error);
					odbc_close($conex_f);

					if($pacEnMatrix and $ingMatrix==$pac['ing'] and isset($pac['san']) and $pac['san']!='')
					{

						$cco['cod']=$pac['sac'];
						getCco(&$cco, "C", $emp);

						$cco2['cod']=$pac['san'];
						getCco(&$cco2, "C", $emp);

						echo "<tr><td class='TituloSup' ><IMG SRC='/matrix/images/medical/root/feliz.ico'><br><br>";
						echo "El paciente <i>".$pac['nom']."</i> con historia <i>".$pac['his']."</i> e ingreso <i>".$pac['ing']."</i><br>";
						echo "esta admitido en la institución en el servicio <i>".$cco['cod']."-".$cco['nom']. "</i><br>procedente del servicio <i>".$cco2['cod']."</i> </td></tr>";

						$q = "SELECT * "
						."      FROM  ".$bd."_000011 "
						."     WHERE ccocod  = '".$pac['san']."' "
						."       AND ccoest  = 'on' "
						."       AND ccoing = 'on' ";

						$err=mysql_query($q,$conex);
						echo mysql_error();
						$num=mysql_num_rows($err);
						if($num >0 and $pac['ptr']=='on')
						{
							echo "<form action='' method='POST' name='admision2'>";
							echo "<input type='hidden' name='cancelar' value=0> ";
							echo "<input type='hidden' name='his' value='".$pac['his']."'> ";
							echo "<input type='hidden' name='ing' value='".$pac['ing']."'> ";
							echo "<input type='hidden' name='bd' value='".$bd."'> ";
							echo "<input type='hidden' name='emp' value='".$emp."'> ";
							echo "<input type='hidden' name='nom' value='".$pac['nom']."'> ";
							echo "<input type='hidden' name='san' value='".$pac['san']."'> ";
							echo"<tr>&nbsp;</td></tr>";
							echo"<tr>&nbsp;</td></tr>";
							echo"<tr><td align =center colspan='1'><input type='button' value='cancelar admision' name='boton' onclick='confirmar()'></td></tr>";
							echo"<tr>&nbsp;</td></tr>";
							echo"<tr>&nbsp;</td></tr>";
							echo"<tr>&nbsp;</td></tr>";
							echo"<tr><td  class='titulo2' colspan='1'><a href='admision.php?bd=movhos&emp=01'>VOLVER</a></td></tr>";

							echo "</form>";
							echo "</table>";
						}

					}
					else
					{
						if($pacEnUNIX)
						{
							echo "<tr><td class='errorTitulo'><IMG SRC='/matrix/images/medical/root/Malo.ico'><br><br>";
							echo "El paciente <i>con historia <i>".$pac['his']."</i> e ingreso <i>".$pac['ing']."</i><br>";
							echo "aun no ha sido admitido a la institucion en Matrix <i></td></tr>";
							echo"<tr><td  class='titulo2' colspan='1'><a href='admision.php?bd=movhos&emp=01'>VOLVER</a></td></tr>";
						}
						else
						{
							echo "<tr><td class='errorTitulo'><IMG SRC='/matrix/images/medical/root/Malo.ico'><BR>La historia <i>".$pac['his']."</i> no esta activa en UNIX";
							echo"<tr><td  class='titulo2' colspan='1'><a href='admision.php?bd=movhos&emp=01'>VOLVER</a></td></tr>";
						}
					}

				}
			}
			else
			{
				/**
                 * Aqui empieza la admisión del paciente a la institución.
             */
				$pac['his']=trim($historia);

				$ind=0;
				while ($ind==0)
				{
					if (substr($pac['his'],0,1)=='0')
					{
						$pac['his']=substr($pac['his'],1);
					}
					else
					{
						$ind=1;
					}
				}

				$pacEnMatrix=infoPaciente(&$pac, $emp);
				if(isset($pac['ing']))
				$ingMatrix=$pac['ing'];
				else
				$ingMatrix="";
				/**
                         * Se llama a la función ValidacionHistoriaUnix() con el fin de que haga el ingreso del paciente en las tablas necesarias
                         * para posteriormente llenar las que hacen falta.
                         */     
				$conex_f = odbc_connect('facturacion','','');
				$pacEnUNIX=ValidacionHistoriaUnix(&$pac,&$warning,&$error);
				odbc_close($conex_f);

				if($pacEnMatrix and $ingMatrix==$pac['ing'] and isset($pac['san']) and $pac['san']!='')
				{
					/**
                                 * El paciente ya existia en la base de datos.
                                 * si le estan haciendo un ingreso no debería.
                                 */
					$cco['cod']=$pac['sac'];
					getCco(&$cco, "C", $emp);


					echo "<tr><td class='errorTitulo'>NO PUEDE REALIZARSE LA ADMISIÓN <IMG SRC='/matrix/images/medical/root/Malo.ico'><br><br>";
					echo "El paciente <i>".$pac['nom']."</i> con historia <i>".$pac['his']."</i> e ingreso <i>".$pac['ing']."</i><br>";
					echo "esta admitido en la institución en el servicio <i>".$cco['cod']."-".$cco['nom']."</i></td></tr>";
					echo"<tr><td  class='titulo2' colspan='1'><a href='admision.php?bd=movhos&emp=01'>VOLVER</a></td></tr>";
				}
				else
				{
					/**
                                 * El paciente no esta admitido en la institución, por lo menos no con el ingreso elegido.
                                 * ASÍ QUE SE PUEDE HACER LA ADMISIÓN
                                 */                                             

					if($pacEnUNIX)
					{
						if($hab != " ")
						{
							/**
                                                 * Mofificar el estado de la habitación que va a ser ocupada por el paciente.
                                                 */
							$q = "UPDATE ".$bd."_000020 "
							."       SET Habdis='off', Habhis='".$pac['his']."', Habing='".$pac['ing']."' "
							."     WHERE Habcod = '".$hab."' "
							."       AND Habcco = '".substr($serSelected,0,4)."' ";
							$err = mysql_query($q,$conex);
							$num = mysql_affected_rows();
							echo mysql_error();
							if($num == 1)
							{
								$ok=true;
							}
							else
							{
								/**
                                                         * Paso algo con la habitación
                                                         */
								echo "<tr><td class='errorTitulo'>NO SE PUDO EFECTUAR LA ADMISIÓN<IMG SRC='/matrix/images/medical/root/Malo.ico'><BR>.";
								echo "El paciente ".$pac['nom'].",<BR> con historia ".$pac['his']." e ingreso ".$pac['ing']."<br>";
								echo "no fue enviado al servicio ".$serSelected.", verifique la disponibilidad de la habitación,<br>";
								echo "INTENTELO NUEVAMENTE";
								echo"<tr><td  class='titulo2' colspan='1'><a href='admision.php?bd=movhos&emp=01'>VOLVER</a></td></tr>";
								$ok=false;
							}
						}
						else
						{
							$ok=true;
						}

						if($ok)
						{
							if($serSelected!='AMBULATORIO')
							{
								/**
                             * Es necesario hacer el update de la ubicación del paciente
                            */

								$q = "UPDATE ".$bd."_000018 "
								."       SET Ubihac='".$hab."', Ubisac='".substr($serSelected,0,4)."', Ubisan='".substr($serOrigen,0,4)."', Ubihan='', Ubiptr='on' "
								."     WHERE Ubihis = '".$pac['his']."' "
								."       AND Ubiing = '".$pac['ing']."' ";
								$err = mysql_query($q,$conex);
								$num = mysql_affected_rows();
								echo mysql_error();
								if($num == 1)
								{
									$q = "lock table " . $bd . "_000001 LOW_PRIORITY WRITE";
									$err = mysql_query($q, $conex);

									$wconsec = "";

									$q = " UPDATE " . $bd . "_000001 "
									. "    SET connum=connum + 1 "
									. "  WHERE contip='entyrec' ";
									$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

									$q = "SELECT connum "
									. "  FROM " . $bd . "_000001 "
									. " WHERE contip='entyrec' ";
									$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
									$row = mysql_fetch_array($err);
									$wconsec = $row[0];

									$q = " UNLOCK TABLES";
									$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());


									$q = " INSERT INTO " . $bd . "_000017 (   Medico       ,   Fecha_data,   Hora_data,   Eyrnum     ,   Eyrhis  ,   Eyring  ,   Eyrsor  ,   Eyrsde         ,   Eyrhor  ,   Eyrhde         ,   Eyrtip   , Eyrest, Seguridad     ) "
									. "                            VALUES ('".$bd."', '".date("Y-m-d")."',  '".date("h:i:s")."','" . $wconsec . "','" . $pac['his'] . "','" . $pac['ing']. "','" . substr($serOrigen,0,4) . "','" . substr($serSelected,0,4) . "','','" . $hab . "','Entrega', 'on'  , 'C-".$usu['codM']."')";
									$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
									$num = mysql_affected_rows();
									/**
                                                         * Agregar la información del nuevo ingreso a las tablas de cendo diario.
                                                         */
									/*$q = "INSERT INTO ".$bd."_000032  (    medico,          fecha_data,            hora_data,  Historia_clinica,       Num_ingreso,                       Servicio, Num_ing_serv,           Fecha_ing,            Hora_ing,                  Procedencia,            Seguridad ) "
									."                         VALUES ( '".$bd."', '".date("Y-m-d")."',  '".date("h:i:s")."', '".$pac['his']."', '".$pac['ing']."', '".substr($serSelected,0,4)."',            1, '".date("Y-m-d")."', '".date("h:i:s")."', '".substr($serOrigen,0,4)."', 'A-".$usu['codM']."' )";
									$err = mysql_query($q,$conex);
									$num = mysql_affected_rows();
									echo mysql_error();*/
									if($num == 1)
									{
										/**
                                                                 * El ingreso se realizo existosamente.
                                                                 */
										echo "<td class='tituloSup' >EL INGRESO SE REALIZO EXITOSAMENTE.<IMG SRC='/matrix/images/medical/root/feliz.ico'>";
										echo "<BR><br>Del paciente <i>".$pac['nom']."</i>, con historia <i>".$pac['his']."</i> e ingreso <i>".$pac['ing']."</i>.";
									}
									else
									{
										echo "<tr><td class='errorTitulo'>NO SE PUDO EFECTUAR LA ADMISIÓN DE FORMA COMPLETA <IMG SRC='/matrix/images/medical/root/Malo.ico'>.<br>Al paciente <i>".$pac['nom']."</i>, con historia <i>".$pac['his']."</i> e ingreso <i>".$pac['ing']."</i> .<br> (No se guardo la información en el censo)<br>. VUELVA A INTENTARLO";
									}
									echo"<tr><td  class='titulo2' colspan='1'><a href='admision.php?bd=movhos&emp=01'>VOLVER</a></td></tr>";
								}
								else
								{
									/**
                                                         * Hay un error
                                                         */
									echo "<tr><td class='errorTitulo'>NO SE PUDO EFECTUAR LA ADMISIÓN DE FORMA COMPLETA <IMG SRC='/matrix/images/medical/root/Malo.ico'>.<br>Al paciente <i>".$pac['nom']."</i>, con historia <i>".$pac['his']."</i> e ingreso <i>".$pac['ing']."</i> .<br> (No se guardo la ubicación del paciente)<br>. VUELVA A INTENTARLO";
									echo"<tr><td  class='titulo2' colspan='1'><a href='admision.php?bd=movhos&emp=01'>VOLVER</a></td></tr>";
								}

							}
							else
							{
								$q = "UPDATE ".$bd."_000018 "
								."       SET Ubisac='".substr($serOrigen,0,4)."', ubihac='' "
								."     WHERE Ubihis = '".$pac['his']."' "
								."       AND Ubiing = '".$pac['ing']."' ";

								$err = mysql_query($q,$conex) or die (mysql_errno() . $q . " - " . mysql_error());;

								echo "<td class='tituloSup' >EL INGRESO AMBULATORIO SE REALIZO EXITOSAMENTE.<IMG SRC='/matrix/images/medical/root/feliz.ico'>";
								echo "<BR><br>Del paciente <i>".$pac['nom']."</i>, con historia <i>".$pac['his']."</i> e ingreso <i>".$pac['ing']."</i>.";
							}
						}
					}
					else
					{
						if(isset($pac['nom']))
						{
							echo "<tr><td class='errorTitulo'>NO SE PUDO EFECTUAR LA ADMISIÓN, OCURRIO UN PROBLEMA<IMG SRC='/matrix/images/medical/root/Malo.ico'><BR> Al ingresar al paciente <i>".$pac['nom']."</i>, con historia <i>".$pac['his']."</i> e ingreso <i>".$pac['ing']."</i>.<BR>".$warning."<br>".$error['codInt']."-".$error['descSis'];
						}
						else
						{
							echo "<tr><td class='errorTitulo'>NO SE PUDO EFECTUAR LA ADMISIÓN<IMG SRC='/matrix/images/medical/root/Malo.ico'><BR>La historia <i>".$pac['his']."</i> no esta activa en UNIX";
						}
						echo"<tr><td  class='titulo2' colspan='1'><a href='admision.php?bd=movhos&emp=01'>VOLVER</a></td></tr>";
					}
				}
			}
		}
		else
		{
			/* Mofificar el estado de la habitación en que estaba el paciente.*/
			$q = "UPDATE ".$bd."_000020 "
			."       SET Habdis='on', Habhis='', Habing='' "
			."     WHERE Habhis = '".$his."' "
			."       AND Habing = '".$ing."' ";
			$err = mysql_query($q,$conex);
			$num = mysql_affected_rows();
			echo mysql_error();
			if($num == 1)
			{
				$ok=true;
			}
			else
			{
				/**
                                                         * Paso algo con la habitación
                                                         */
				echo "<tr><td class='errorTitulo'>NO SE PUDO CANCELAR LA ADMISION<IMG SRC='/matrix/images/medical/root/Malo.ico'><BR>.";
				echo "La habitacion donde se encontraba El paciente con historia ".$his." e ingreso ".$ing."<br>";
				echo "no fue liberada, verifique la disponibilidad de la habitación,<br>";
				$ok=false;
			}

			if($ok)
			{
				/**
                                                         * Es necesario hacer el update de la ubicación del paciente
                                                         */
				$q = "UPDATE ".$bd."_000018 "
				."       SET Ubihac='', Ubisac='".$san."', Ubisan='', Ubihan='', Ubiptr='off' "
				."     WHERE Ubihis = '".$his."' "
				."       AND Ubiing = '".$ing."' ";
				$err = mysql_query($q,$conex);
				$num = mysql_affected_rows();
				echo mysql_error();
				if($num == 1)
				{
					$q = "UPDATE ".$bd."_000017 "
					."       SET Eyrest='off' "
					."     WHERE Eyrhis = '".$his."' "
					."       AND Eyring = '".$ing."' ";
					$err = mysql_query($q,$conex);
					$num = mysql_affected_rows();
					echo mysql_error();

					/**
                      * Agregar la información del nuevo ingreso a las tablas de cendo diario.
                    */

					/*$q = "DELETE FROM  ".$bd."_000032 "
					."     WHERE Historia_clinica = '".$his."' "
					."       AND Num_ingreso = '".$ing."' ";

					$err = mysql_query($q,$conex);
					$num = mysql_affected_rows();
					echo mysql_error();*/
					if($num > 0)
					{
						/**
                         * El ingreso se realizo existosamente.
                         */
						echo "<td class='tituloSup' >SE CANCELO LA ADMISION EXITOSAMENTE.<IMG SRC='/matrix/images/medical/root/feliz.ico'>";
						echo "<BR><br>Del paciente <i>".$nom."</i>, con historia <i>".$his."</i> e ingreso <i>".$ing."</i>.";
						echo"<tr><td  class='titulo2' colspan='1'><a href='admision.php?bd=movhos&emp=01'>VOLVER</a></td></tr>";
					}
					else
					{
						echo "<tr><td class='errorTitulo'>NO SE PUDO EFECTUAR LA ADMISIÓN DE FORMA COMPLETA <IMG SRC='/matrix/images/medical/root/Malo.ico'>.<br>Al paciente <i>".$nom."</i>, con historia <i>".$his."</i> e ingreso <i>".$ing."</i> .<br> (No se elimino la información en el censo)<br>. VUELVA A INTENTARLO";
						echo"<tr><td  class='titulo2' colspan='1'><a href='admision.php?bd=movhos&emp=01'>VOLVER</a></td></tr>";
					}
				}
				else
				{
					/**
                                                         * Hay un error
                                                         */
					echo "<tr><td class='errorTitulo'>NO SE PUDO CANCELAR LA ADMISIÓN DE FORMA COMPLETA <IMG SRC='/matrix/images/medical/root/Malo.ico'>.<br>Al paciente <i>".$nom."</i>, con historia <i>".$his."</i> e ingreso <i>".$ing."</i> .<br> (No se actualizo la ubicación del paciente)<br>. VUELVA A INTENTARLO";
					echo"<tr><td  class='titulo2' colspan='1'><a href='admision.php?bd=movhos&emp=01'>VOLVER</a></td></tr>";
				}
			}
		}

	}
	else
	{
		echo "<tr><td class='errorTitulo'>No hay conexión con UNIX, no puede efectuarce la transacción<IMG SRC='/matrix/images/medical/root/Malo.ico'>";
	}
	echo "</td></tr></table>";


}
?>
</body>
</html>
