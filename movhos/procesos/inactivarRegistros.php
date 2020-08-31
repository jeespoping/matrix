<html>
<head>
<title>Inactivar Registros</title>
<style type="text/css">
    	<!--body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.tituloSup{color:#006699;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloSup1{color:#57C8D5;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo1{color:#FFFFFF;background:#006699;font-family:Arial;font-weight:bold;text-align:center;}
    	<!-- -->
    	.titulo2{color:#003366;background:#57C8D5;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#336666;background:#AAFFFF;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto{color:#006699;background:#CCFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
    	<!-- .acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.errorTitulo{color:#FF0000;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	-->
    	.alert{background:#FFFFAA;color:#FF9900;font-size:9pt;font-family:Arial;text-align:center;}
    	.warning{background:#FFCC99;color:#FF6600;font-size:9pt;font-family:Arial;text-align:center;}
    	.error{background:#FFAAAA;color:#FF0000;font-size:9pt;font-family:Arial;text-align:center;}
    	
    	
    	.tituloA1{color:#FFFFFF;background:#660099;font-family:Arial;font-weight:bold;text-align:center;}
    	.textoA{color:#660066;background:#FFFFFF;font-size:10pt;font-family:Arial;}
    	    
    </style>
</head>
<body>
<?php
include_once("conex.php");

/**
 * A continuación las validaciones que verifica enel programa con el fin de saber si sí se puede inactivar el registro:<br>
 *  * Que exista conexión con UNIX, si no no se puede inactivar por que no se puede asegurar que no haya pasado a los cargos del paciente.<br>
 *  * Que exista el registro, es decir que exista el detalle (facfde 000002) y que exista el encabezado.<br>
 *  * Verifica si esta en UNIX, si esta se pueden dar tres situaciones:<br>
 *  ** Los campos del registro de UNIX no corresponde con los campos del registro de matrix, se puede inactivar.  <br>
 *     Este caso puede darse, por ejemplo, si alguien manipula el consecutivo inadecuadamente mientras el sistema esta por fuera de UNIX. <br>
 *     Se da muy rara vez, pues si el consecutivo se manipula mal, es decir se disminuye,  el sistema no permitira que se ingresen<br>
 *     registros que dupliquen el indice(dronum-drolin), entonces no dejari a cargar nada a mucha gente y el problema tendria que ser resuelto muy rápidamente.<br>
 *     En cambio si no hay UNIX si se podrian hacer, siempre y cuando no haya registros con esa númeración en MATRIX, <br>
 *     es decir que se hayan borrado los registros en MATRIX y en UNIX no.<br>
 *  ** El registro este inconsistente (droest=I), se puede inactivar.<br>
 *  ** El registro esta sin procesar (droest=S) o procesado (droest=P), No se permite inactivarlos.<br>
 *  * Si no es una devolución, el primer caracter de Fentip no es D, verifica que haya suficiente cantidad del articulo disponible para el cco y la fuente.<br>
 *  
 * <br><br>
 * Las verificaciones extra que realiza, que si son negativas no impide que se haga la incativación son:<br>
 *  * NO valida que exista el cco y que este habilitado para realizar cargos, por que esto lo deben validar los programas de cargos, y si efectivamente el cco no es ta bien ese es un buen motivo para inactivrar el registro.<br> 
 *  * Verifica que la historia existe en UNIX... si no existe este es un buen motivo para inactivar.  
 * 
 * <br><br>
 * Si efectivamente se puede efectuar la inactivación el sistema le muestra al usuario la información del registro y pide que confirme que desea inactivarlo.
 * Existe un tiempo límite para inactivar un registro que no sea de devolución. Por que si el usuario se demora mucho en confirmar la inactivación puede
 * cambiar las circunstancias, por ejemplo que se de una devolución o una aplicación, al paciente, del mismo artículo, centro de costos y fuente del registro, 
 * lo que significa que una inactivación generaria errores en la tabla de saldos del apciente.
 * 
 * 
 * @version 2007-06-27
 * @modified 2007-09-27 Se hace la conexión odbc a facturación antes de llamar a ValidacionHistoriaUnix
 * @modified 2007-06-17 Ya no se hace el querie a la tabla 000026 cuando se desea buscar el nombre del artículo, si no que se llama a la función ArticuloExiste del include FxValidacionArticulo. Por lo que lambuien en el hidden de name='art[cod]' no tenca como valor $row['Fdeart'] si no $art['cod'].
 * @modified 2007-06-19 Si el centro de costos aplica se anula una aplicación que tenga la misma historia, ingreso, articulo, centro de costos, fecha y que la hora coincida con la ronda que el cargo.  No se igualan las horas por que puede que haya segundos de diferencia entre el cargo y la aplicación.
 * 
 */
/**
 * 
 **/
include_once("movhos/validacion_hist.php");
include_once("movhos/fxValidacionArticulo.php");
include_once("movhos/registro_tablas.php");
include_once("movhos/otros.php");
if(!isset($_SESSION['user']))
echo "error";
else
{
	

	


	$pos = strpos($user,"-");
	$usuario = substr($user,$pos+1);

	

	


	connectOdbc(&$conex_o, 'inventarios');
	if($conex_o != 0)
	{
		if(!isset($numReg))
		{
			echo "<center><table border='0' width='300'>";
			echo "<form action='' method='POST'>";
			echo "<tr><td class='titulo1' colspan='2'>INACTIVACIÓN DE UN REGISTRO</td></tr>";
			echo "<tr>";
			echo "<td class='titulo2' ><b>Doc. Matrix: </b><input type='text' size='8' name='numReg'></td>";
			echo "<td class='titulo2' ><b>Línea: </b><input type='text' size='3' name='lin'>";
			echo "</td></tr>";
			echo"<tr><td  class='titulo1' colspan='2'><input type='submit' name='aceptar' value='ACEPTAR'></td></tr>";
			echo "</table>";
			echo "</form>";
		}
		else if(!isset($confirmar))
		{
			echo "<center><table border='0' >";
			echo "<td class='titulo1' colspan='2'>INACTIVACIÓN DE UN REGISTRO</td></tr>";
			echo "<tr><td colspan='2' class='titulo2'>Información del Registro</td></tr>";
			echo "<tr><td class='texto'><b>Doc. Matrix: </b>".$numReg."&nbsp;&nbsp; <b>Línea:</b>".$lin."</td>";


			/**
			 * Buscar el registro en la tabla de detalles de MATRIX
			 */
			$q = " SELECT Fdeart, Fdecan, Fdeest, ".$bd."_000003.Fecha_data as fec, ".$bd."_000003.Hora_data as hor, ".$bd."_000003.Seguridad as seg, Fenfue, Fdeubi, Fencco, Fenhis, Fening, Fenest, Fentip "
			."       FROM ".$bd."_000002, ".$bd."_000003 "
			."      WHERE  Fdenum = ".$numReg." "
			."        AND Fdelin = ".$lin." "
			."        AND Fennum = ".$numReg." ";
			$err = mysql_query($q,$conex)or die(mysql_errno()."3:".mysql_error());
			$num=mysql_num_rows($err);
			if($num>0)
			{
				$row=mysql_fetch_array($err);

				echo "<td class='texto'><b>Fuente: </b>".$row['Fenfue']."</td></tr>";

				if(substr($row['Fentip'],1,1) == "A")
				{
					$aprov =true;
				}
				else
				{
					$aprov=false;
				}

				$tipTrans=substr($row['Fentip'],0,1);

				$ubi=$row['Fdeubi'];
				$fec=$row['fec'];
				$hor=$row['hor'];
				$seg=$row['seg'];
				/**
				 * Buscar Nombre y unidad del artículo.
				 */
				$art['cod']=$row['Fdeart'];
				$art['can']=$row['Fdecan'];

				if($row['Fdeest'] == 'on')
				{
					//El registro esta inactivo.
					$pac['his']=$row['Fenhis'];

					/**
			 * Valida que este activo en UNIX, si no lo esta puede realizar la inactivación, solo si el paciente no esta de alta.
			 * Esto de inactivar aunque el pacienteno este en UNIX se permite por si al momento de hacer cargos
			 * con el sistema sin comunicación con UNIX se ingresa una historia inválida.
			 */
					$conex_f = odbc_connect('facturacion','','');		
					if(!ValidacionHistoriaUnix(&$pac, &$warning, &$error))
					{
						$pac['ing']=$row['Fening'];
						infoPaciente(&$pac, $emp);
					}
					else {
						$pac['ing']=$row['Fening'];
					}
					odbc_close($conex_f);

					if(!pacienteDeAlta(&$pac, $row['Fencco']))
					{
						/**
				 * El paciente no esta de alta.
				 * Se valida el estado del paciente en UNIX.
				 */

						$q=" SELECT drohis, drofue, droser, droart, drocan, droest  "
						."     FROM itdro "
						."    WHERE dronum = ".$numReg." "
						."      AND drolin = ".$lin." ";
						$err_o= odbc_do($conex_o,$q);
						if(odbc_fetch_row($err_o)) //Para cada registro encontrado en UNIX;
						{

							/**
							 * Puede ocurrir un error de registro o de numeración en donde los datos que hay para el num lin de UNIX no correspondan a los de MATRIX,
							 * por ejemplo si alguien manipula el consecutivo inadecuadamente mientras el sistema esta por fuera de UNIX.
							 * Se da muy rara vez, pues si el consecutivo se manipula mal, es decir se disminuye,  el sistema no permitira que se ingresen
 							 * registros que dupliquen el indice(dronum-drolin), entonces no dejari a cargar nada a mucha gente y el problema tendria que ser resuelto muy rápidamente.
							 * En cambio si no hay UNIX si se podrian hacer, siempre y cuando no haya registros con esa númeración en MATRIX.
							 */

							if( odbc_result($err_o,1) == $row['Fenhis'] and
							odbc_result($err_o,2) == $row['Fenfue'] and
							odbc_result($err_o,3) == $row['Fencco'] and
							odbc_result($err_o,4) == $row['Fdeart'] and
							odbc_result($err_o,5) == $row['Fdecan'])
							{

								/**
							 * Aun si todos los registros estan bien, se puede inactivar mientras el registro
							 * este inconsistente
							 */
								if(odbc_result($err_o,6) == "I")
								{
									$inAct=true;
									$ubi="U";
								}
								else
								{
									$inAct=false;
									switch (odbc_result($err_o,6))
									{
										case 'S':
										$text="SIN PROCESAR, <BR>ES DECIR QUE NO HA SIDO PROCESADO POR EL INTEGRADOR. ";
										break;

										case 'P':
										$text="PROCESADO. <BR>ES DECIR QUE EL INTEGRADOR YA LO PROCESO Y LO PASO A LOS CARGOS DEL PACIENTE.";
										break;
									}
									echo "<td class='error' colspan='2'>El REGISTRO ESTA EN ESTADO <b>".$text."</b><IMG SRC='/matrix/images/medical/root/Malo.ico'><BR>SOLO LOS REGISTROS QUE YA FUERON PROCESADO POR EL INTEGRADOR <br>Y ESTAN EN ESTADO INCONSISTENTE PUEDEN SER INACTIVADOS.</td></tr>";
								}
							}
							else
							{
								/**
							 * Si los registros son diferentes es un buen motivo para inactivar
							 */
								$inAct=true;
								/**
							 * No se sabe si al momento de cargar quedo en MATRIX o en UNIX ... 
							 * Sin Embargo se asume que deben esta r en MATRIX y así se pone, por el moemnto.
							 */
								$ubi="M";

							}
						}
						else
						{
							/**
							 * No esta en UNIX, esta solo en MATRIX, sepuede inactivar
							 */
							$inAct=true;
							$ubi="M";
						}

						if($inAct)
						{
							$cco['cod']=$row['Fencco'];
							getCco(&$cco,$tipTrans, $emp);
							
							/**
							 * Hay que validar si cuando cargo o devolución genero una aplicación automática
							 * sea por que el centro de costos de dosn se hizo tuviera apliación automática
							 * o por que el paciente se encontrara en un centro de costos con aplicación automática
							 */
							$q = " SELECT id "
							."      FROM ".$bd."_000015 "
							."      WHERE Aplnum = '".$numReg."' "
							."        AND Apllin = '".$lin."' ";
							$err1 = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
							echo mysql_error();
							$num1=mysql_num_rows($err1);
							if($num1 > 0)
							{
								$row1=mysql_fetch_array($err1);
								$idApl=$row1['id'];
								$cco['apl']=true;
							}

							/**
							 * Si es una devolución no hace falta verificar que haya suficiente cantidad.
							 * Tampoco si no
							 */
							if(substr($row['Fentip'],0,1) == "C" and !$cco['ima'])
							{
								/**
								 * No es una devolución y el registro no se aplica
								 * Buscar si hay saldo suficiente para inactivar el registro.
								 */
								if(!validacionDevolucion($cco, $pac, $art, $aprov, false, &$error))
								{
									$inAct=false;
									echo "<td class='error' colspan='2'><b>NO HAY UNA CANTIDAD DISPONIBLE SUFICIENTE PARA INACTIVAR EL REGISTRO</b></td></tr>";
									echo "<td class='error' colspan='2'>ESTO QUIERE DECIR QUE PARA INACTIVARLO ES NECESARIO: <ul>";
									echo "<li>Cargar suficiente cantidad del artículos al mismo paciente,<br> centro de costos y con la misma fuente que el registro.</li>";
									echo "<li>Deshacer o anular las transacciones que fueron hechas para <br>contrarestrar el registro, como las devoluciones o las aplicaciones.</li>";
									echo "</ul></td></tr>";
								}
							}
						}
					}
					else
					{
						echo "<td class='error' colspan='2'>EL PACIENTE CON HISTORIA =".$pac['his']." E INGRESO =".$pac['ing']."<br> YA FUE DADO DE ALTA DEFINITIVA O PARA EL CCO ".$row['Fencco']."<IMG SRC='/matrix/images/medical/root/Malo.ico'></td></tr>";
						$inAct=false;
					}
				}
				else
				{
					echo "<td class='error' colspan='2'>EL REGISTRO CON DOCUMENTO MATRIX =".$numReg." Y LÍNEA =".$lin."<br> YA ESTA INACTIVO<IMG SRC='/matrix/images/medical/root/Malo.ico'></td></tr>";
					$inAct=false;
				}
			}
			else
			{
				echo "<td class='texto'><b>Fuente: </b></td></tr>";
				echo "<td class='error' colspan='2'>NO EXISTE EL REGISTRO CON DOCUMENTO MATRIX =".$numReg." Y LÍNEA =".$lin."<IMG SRC='/matrix/images/medical/root/Malo.ico'></td></tr>";
				$inAct=false;
			}

			if($inAct)
			{

				//2007-06-17
				if(!ArticuloExiste(&$art, &$error))
				{
					$art['nom']="NO ENCONTRADO";
					$art['uni']="";
				}
				//Fin cambios 2007-06-17
				$art['can']=$row['Fdecan'];

				/**
				 * Mostrar la opción de confirmación
				 */
				echo "<form action='' method='POST'>";


				echo "<tr><td class='texto'><b>Historia: </b>".$pac['his']."</td>";
				echo "<td  class='texto'><b>Ingreso:</b>".$pac['ing']."</td></tr>";

				echo "<tr><td colspan='2' class='texto'><b>Nombre: </b>".$pac['nom']."</td></tr>";
				echo "<tr><td colspan='2' class='texto'><b>Cco: </b>".$cco['cod']."-".$cco['nom']."</td></tr>";
				echo "<tr><td colspan='2' class='texto'><b>Articulo: </b>".$art['cod']."-".$art['nom']."</td>";
				echo "<tr><td colspan='2' class='texto'><b>Cantidad: </b>".$art['can']." ".$art['uni']."</td></tr>";

				echo "<input type='hidden' name='pac[his]' value='".$pac['his']."'>";
				echo "<input type='hidden' name='pac[ing]' value='".$pac['ing']."'>";
				echo "<input type='hidden' name='pac[nom]' value='".$pac['nom']."'>";
				echo "<input type='hidden' name='fue' value='".$row['Fenfue']."'>";
				echo "<input type='hidden' name='tip' value='".$row['Fentip']."'>";
				echo "<input type='hidden' name='fec' value='".$fec."'>";
				echo "<input type='hidden' name='hor' value='".$hor."'>";
				echo "<input type='hidden' name='cco[cod]' value='".$cco['cod']."'>";
				if($cco['apl'])
				{
					echo "<input type='hidden' name='cco[apl]' value='1'>";
				}
				else
				{
					echo "<input type='hidden' name='cco[apl]' value='0'>";
				}
				echo "<input type='hidden' name='numReg' value='".$numReg."'>";
				echo "<input type='hidden' name='lin' value='".$lin."'>";
				echo "<input type='hidden' name='tipTrans' value='".$tipTrans."'>";
				echo "<input type='hidden' name='time' value='".time()."'>";
				echo "<input type='hidden' name='ubi' value='".$ubi."'>";
				echo "<input type='hidden' name='seg' value='".$seg."'>";
				echo "<input type='hidden' name='pac[nom]' value='".$pac['nom']."'>";
				echo "<input type='hidden' name='cco[nom]' value='".$cco['nom']."'>";
				echo "<input type='hidden' name='art[cod]' value='".$art['cod']."'>";//2007-06-17
				echo "<input type='hidden' name='art[nom]' value='".$art['nom']."'>";
				echo "<input type='hidden' name='art[can]' value='".$art['can']."'>";
				echo "<input type='hidden' name='art[uni]' value='".$art['uni']."'>";
				if(isset($idApl))
				{
					echo "<input type='hidden' name='idApl' value='".$idApl."'>";
				}
				//echo "<input type='hidden' name='' value='".."'>";


				echo "<tr><td class='texto' colspan='2'><b>¿Esta seguro de que desea inactivar el registro?<br>";
				echo "<input type='checkbox' name='confirmar'>SI </b>(Debe confirmar en menos de 60 segundos)</td><tr>";
				echo "<tr><td class='texto' colspan='2'><input type='submit' value='CONTINUAR >>'></b></td></tr>";
				echo "</form>";

				//hideen: tip(CoD), aprov, hora de mostrar confirmación, numero línea o id...
			}
			echo "</table>";
		}
		else
		{
			echo "<center><table border='0' >";
			echo "<td class='titulo1' colspan='2'>INACTIVACIÓN DE UN REGISTRO</td></tr>";
			echo "<tr><td class='texto' colspan='2'><b>Doc. Matrix: </b>".$numReg."&nbsp;&nbsp; <b>Línea:</b>".$lin."</td>";
			/**
			 * El sistema ya comprobo que el registro se podia inactivar.
			 * El usuario ha confirmado que se debe inactivar.
 			 * Se verifica que no haya pasado mas de un minuto desde que se hizo la pregunta de confirmación
			 * hasta que el usuario confirmos, pues si pasa mucho tiempo pueden
			 * aplicar o devolver el artículo y es posible que ya lo hayan aplicado
			 */

			if((substr($tip,0,1) != "D" or  !$cco['apl'] ) and (time()-$time) > 60 )
			{
				/**
			 * Si es una devolución, o el centro de costos no se aplica automáticamente durante el proceso de cargos
			 * entonces debe verificar que no haya pasado mas de un minuto desde el tiempo en que 
			 * se pidio al cliente confirmar y el momento en que confirmo. De ser así es un error
			 */

				echo "<form action='' method='POST'>";
				echo "<tr><td colspan='2' ></br></br></tr>";
				echo "<tr><td colspan='2' class='error'><b>PASO MAS DE UN MINUTO</b></br> DESDE EL MOMENTO ";
				echo "EN QUE SE LE DIO LA OPCIÓN DE CONFIRMAR</br> LA INACTIVACIÓN, ";
				echo "Y EL MOMENTO EN QUE CONFIRMO</td></tr>";
				echo "<tr><td colspan='2' class='warning'> Esta ventana de tiempo existe para evitar<br>";
				echo "que mientras se espera la confirmación, en algun lugar<br>";
				echo "de la institución alguien realice una transacción;<br>";
				echo "como una devolución,una incativación o una aplicación;<br>";
				echo "que involucre el mismo paciente, centro de costos, fuente y artículo<br>";
				echo "que el registro a incativar; causando así una inconsistencia<br>";
				echo "en la cuenta del paciente</td></tr>";
				echo "<input type='hidden' name='numReg' value='".$numReg."'>";
				echo "<input type='hidden' name='lin' value='".$lin."'>";
				//echo "<input type='hidden' name='' value='".."'>";

				echo "<tr><td class='error' colspan='2'><input type='submit' value='<< Regresar en intentarlo nuevamente'></b></td></tr>";
				echo "</form>";
			}
			else
			{
				/**
				 * Iniciar la inactivación
				 */
				if(substr($tip,1,1) == "A")
				{
					$aprov =true;
				}
				else
				{
					$aprov=false;
				}

				echo "<tr><td colspan='2' ></br></td></tr>";
				echo "<tr><td colspan='2' class='titulo2'>Información del Registro</td></tr>";
				echo "<tr><td class='texto'><b>Historia: </b>".$pac['his']."</td>";
				echo "<td  class='texto'><b>Ingreso:</b>".$pac['ing']."</td></tr>";
				echo "<tr><td colspan='2' class='texto'><b>Nombre: </b>".$pac['nom']."</td></tr>";
				echo "<tr><td colspan='2' class='texto'><b>Fuente: </b>".$fue."</td></tr>";
				echo "<tr><td colspan='2' class='texto'><b>Cco: </b>".$cco['cod']."-".$cco['nom']."</td></tr>";
				echo "<tr><td colspan='2' class='texto'><b>Articulo: </b>".$art['cod']."-".$art['nom']."</td>";
				echo "<tr><td colspan='2' class='texto'><b>Cantidad: </b>".$art['can']." ".$art['uni']."</td></tr>";

				/**
				 * Por el momento y mientras se generan los registros de saldo no se involurara el usuario, 
				 * pues siempre se efectuara un update y nunaca un insert.
				 * Para la Version2 Hay que manejar el usuario.
				 */				

				if($cco['apl'])
				{
					/**
					 * El cargo o devolución genero una aplicación automática
					 * sea por que el centro de costos de dosn se hizo tuviera apliación automática
					 * o por que el paciente se encontrara en un centro de costos con aplicación automática.
					 */

					$q = " UPDATE ".$bd."_000015 "
					."        SET Aplest = 'off' "
					."      WHERE id = ".$idApl." ";
					$err1 = mysql_query($q,$conex)or die(mysql_errno()."1:".mysql_error());
					echo mysql_error();
					$num1=mysql_affected_rows();
					if($num1<1)
					{
						/**
						 * Error
						 */
						echo "<td class='error' colspan='2'><B>NO PUDO SER ANULADA LA APLICACIÓN.<BR>COMUNIQUESE CON SISTEMAS.</B>.<IMG SRC='/matrix/images/medical/root/Malo.ico'></td></tr>";
					}
					else
					{
						$ok=true;
					}
				}

				if($ok)
				{
					if(registrarSaldos($pac,$art,$cco,$aprov,date('Y-m-d'),$usuario,$tipTrans,true,&$error))
					{
						$q = " UPDATE ".$bd."_000003 "
						."        SET Fdeest = 'off', Fdeinf='".date('Y-m-d')."' , Fdeinh='".date('H:i:s')."', Fdeinu='".$usuario."' "
						."      WHERE Fdenum = ".$numReg." "
						."        AND Fdelin = ".$lin." ";
						$err = mysql_query($q,$conex)or die(mysql_errno()."2:".mysql_error());
						echo mysql_error();
						$num=mysql_affected_rows();
						if($num < 1)
						{
							/**
					 	* No se inactivo el registro
						*/
							echo "<td class='error' colspan='2'><B>SE AFECTO EL SALDO !!!!!<br> PERO NO SE INACTIVO EL REGISTRO.<BR>ESCRIBA ESTE ERROR ".mysql_error()."<br> Y COMUNIQUESELO A SISTEMAS.</B>.<IMG SRC='/matrix/images/medical/root/Malo.ico'></td></tr>";
						}
						else
						{
							/**
							 * Registro de la inactivación.
							 */
							echo "<tr><td colspan='2' class='texto'><b>EL REGISTRO SE INACTIVO EXITOSAMENTE <IMG SRC='/matrix/images/medical/root/feliz.ico'></b></td></tr>";
						}
					}
					else
					{
						/**
					* Error
				 	*/
						echo "<td class='error' colspan='2'><B>NO SE AFECTO EL SALDO,<br> NO SE INACTIVO EL REGISTRO.<BR> INTENTELO NUEVAMENTE Y SI NO ES EXITOSA LA INACTIVACIÓN, <BR>COMUNIQUESE CON SISTEMAS.</B>.<IMG SRC='/matrix/images/medical/root/Malo.ico'></td></tr>";
					}
				}
			}
		}
	}
	else
	{
		echo "<td class='error' colspan='2'>NO EXISTECONEXIÓN CON UNIX. NO PUEDE INACTIVAR REGISTROS.</td></tr>";
	}
}
?>
</body>
</html>
