<?php
include_once("conex.php");

/***********************************************
 *              REGISTRO DE ALTAS              *
 *     		    CONEX, FREE => OK              *
 ***********************************************/
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if (!isset($_SESSION['user']))
    echo "error";
else
{	             
  
  include_once("root/magenta.php");
  include_once("root/comun.php");
  
  $conex = obtenerConexionBD("matrix");

  if (strpos($user,"-") > 0)
      $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           
	                                                                                                       
  $q = " SELECT empdes "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  
  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
  $q = " SELECT detapl, detval, empdes "
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
	      
	      if ($row[0] == "cenmez")
	          $wcenmez=$row[1];
	         
	      if ($row[0] == "afinidad")
	          $wafinidad=$row[1];
	         
	      if ($row[0] == "movhos")
	          $wbasedato=$row[1];

		  if ($row[0] == "cliame")
	          $wcliame=$row[1];	      
	         
	      if ($row[0] == "tabcco")
	          $wtabcco=$row[1];
	         
	      if (strtoupper($row[0]) == "HCE")
	          $whce=$row[1];
	         
	      $winstitucion=$row[2];   
      }  
  }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";     

 //-----------------------------------------------------------------------
 // ----------------> Funciones PHP 
 //-----------------------------------------------------------------------
 
 //---------------------------------------------------------
 // --> Realizar un registro en el log de movimientos
 //---------------------------------------------------------	
 function guardarLog($turno, $accion, $tema)
 {
		global $wcliame;
		global $conex;
		global $wuse;
		
		$sqlRegLLamado = "
		INSERT INTO ".$wcliame."_000303 (Medico,				Fecha_data,				Hora_data,				Logtem,			Logtur,			Logacc,			Logusu,			Seguridad,			id)
									VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$tema."',	'".$turno."', 	'".$accion."',	'".$wuse."', 	'C-".$wbasedato."',	NULL)
		";
		mysql_query($sqlRegLLamado, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlRegLLamado."):</b><br>".mysql_error());
 }

 if (isset($consultaAjax) && $consultaAjax == 'llamarPacienteAtencion')
 {

		     $respuesta = array('Error' => FALSE, 'Mensaje' => '');

		     // --> Consultar el Servicio la cual pertenece el consultorio
			 $sqlConsultorio 	= "
									SELECT Rsppue,Rsptem,Rspser
									  FROM ".$wcliame."_000302  
									 WHERE Rsppue = '".$puestoTrabajo."' 
									   AND Rspest = 'on'
								";
			 $resConsultorio = mysql_query($sqlConsultorio, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlConsultorio."):</b><br>".mysql_error());

			 while( $rowConsultorio =mysql_fetch_array($resConsultorio) )
			 {
				    $tema     = $rowConsultorio['Rsptem'];
				    $servicio = $rowConsultorio['Rspser'];
			 }

		     // --> Validar que el paciente no este siendo llamado en este momento
			 $sqlValLla = "
						 SELECT Descripcion
						  FROM ".$wcliame."_000304, usuarios
						 WHERE Turtem = '".$tema."'
						   AND Turtur = '".$turno."'
						   AND (Turllv = 'on' OR Turpat = 'on')
						   AND Turull != '".$wusuario."'
						   AND Turull = Codigo
						   AND Turest = 'on'
						 ";
            
			$resValLla = mysql_query($sqlValLla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValLla):</b><br>".mysql_error());
			if($rowValLla = mysql_fetch_array($resValLla))
			{
				$respuesta['Error'] 	= TRUE;
				$respuesta['Mensaje'] 	= "El turno ya est&aacute; siendo llamado por:<br>".utf8_encode($rowValLla['Descripcion']);
			}
			else
			{
				// --> Validar que el usuario no esté llamando a otro turno en el mismo momento
				$sqlValLla2 = "
							SELECT Turtur
							  FROM ".$wcliame."_000304 
							 WHERE Turtem = '".$tema."' 
							   AND Fecha_data = '".date("Y-m-d")."'
							   AND (Turllv = 'on' OR Turpat = 'on')
							   AND Turull = '".$wuse."'
							   AND Turest = 'on'
							";
				$resValLla2 = mysql_query($sqlValLla2, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValLla2):</b><br>".mysql_error());
				if($rowValLla2 = mysql_fetch_array($resValLla2))
				{
					$turno 		= substr($rowValLla2['Turtur'], 7);
					$turno 		= substr($turno, 0, 2)." ".substr($turno, 2, 5);
					$respuesta['Error'] 	= TRUE;
					$respuesta['Mensaje'] 	= "Para poder llamar a otro turno primero debe terminar el<br>proceso de atenci&oacute;n con el turno: <b>".$turno."</b>";
				}
				else
				{
					$strTema = "";
				
					$sqlTema = " SELECT Codtem,Codnom
								  FROM ".$wcliame."_000305
								 WHERE Codaso = '".$tema."'
								   AND Codest = 'on'
								";
					$resTema = mysql_query($sqlTema, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTema):</b><br>".mysql_error());
					while ($rowTema = mysql_fetch_array($resTema))
						   $strTema = $rowTema['Codtem'];

					// --> realizar el llamado
					$sqlLlamar = "
					UPDATE ".$wcliame."_000304
					   SET  Turllv = 'on',
						    Turhll = '".date('Y-m-d')." ".date("H:i:s")."',
						    Turull = '".$wusuario."',
						    Turven = '".$puestoTrabajo."'
					 WHERE  Turtur = '".$turno."' 
					   AND (Turtem = '".$tema."' 
					        OR Turtem = '".$strTema."')";


					mysql_query($sqlLlamar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLlamar):</b><br>".mysql_error());

					// --> Registrar en el log el llamado
					guardarLog($turno, "llamadoVentanilla", $tema);
				}
			}

			echo json_encode($respuesta);
			return;

}

function consultarServicio($puestoTrabajo,$wcliame,$opcion)
{
		// --> Consultar el Servicio la cual pertenece el consultorio
		$sqlConsultorio 	= "
							SELECT Rsppue,Rsptem,Rspser
							  FROM ".$wcliame."_000302  
							 WHERE Rsppue = '".$puestoTrabajo."' 
							   AND Rspest = 'on'
							";
        						
		$resConsultorio = mysql_query($sqlConsultorio, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlConsultorio."):</b><br>".mysql_error());

		$strTema  = '';
		$servicio = '';

		while($rowConsultorio =mysql_fetch_array($resConsultorio))
		{
			  $strTema = $rowConsultorio['Rsptem'];
			  $servicio = $rowConsultorio['Rspser'];
		}

		//Consultar temas asociados
		if ($strTema !== '' && $opcion==2)
		{
			    // --> Seleccionar los temas para redireccionamiento de turno
				$tema = $strTema;
				$strTema .= ",";
				
				$sqlTema = "
							SELECT Codtem,Codnom
							  FROM ".$wcliame."_000305
							 WHERE Codaso = '".$tema."'
							   AND Codest = 'on'
							";
				$resTema = mysql_query($sqlTema, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTema):</b><br>".mysql_error());
				while ($rowTema = mysql_fetch_array($resTema))
					   $strTema .= $rowTema['Codtem'].",";

				$strTema = substr_replace($strTema ,"", -1);
        }

		return $strTema.'|'.$servicio;
}
 
if (isset($consultaAjax) && $consultaAjax=='cambiarPuestoTrabajo')
{
  	    global $wcliame;
		$servicio       = "";
		$respuesta 		= array("Error" => FALSE, "Mensaje" => "");

        // Consultar el tema y servicio según el consultorio seleccionado
		$infoServicio = consultarServicio($puestoTrabajo,$wcliame,1);
		list($tema, $servicio) = explode('|', $infoServicio);	

		// --> Validar que el puesto de trabajo este disponible
		$sqlValPuesTra = "
						SELECT Descripcion
						  FROM ".$wcliame."_000301, usuarios
						 WHERE Puetem = '".$tema."' 
						   AND Puecod = '".$puestoTrabajo."'
						   AND Pueusu != ''
						   AND Pueusu = Codigo
						";
		$resValPuesTra = mysql_query($sqlValPuesTra, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlValPuesTra."):</b><br>".mysql_error());

		if($respetarOcupacion == 'true' && $rowValPuesTra = mysql_fetch_array($resValPuesTra))
		{
			$respuesta["Error"] 	= TRUE;
			$respuesta["Mensaje"] 	= utf8_encode('Este cub&iacuteculo ya est&aacute ocupado por <br>'.$rowValPuesTra['Descripcion']);
		}
		else
		{
			// --> Quitar cualquier puesto de trabajo asociado al usuario
			$sqlUpdatePues = "
							UPDATE ".$wcliame."_000301
							   SET Pueusu = '',
							       Pueser = '',
							       Puesse = ''
							 WHERE Pueusu = '".$wusuario."'
							";
			mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlUpdatePues."):</b><br>".mysql_error());

			if($puestoTrabajo != '')
			{
				
				// --> Asignar el nuevo puesto de trabajo
				$sqlUpdatePues = "
								UPDATE ".$wcliame."_000301
								   SET Pueusu = '".$wusuario."',
							           Pueser = '".$servicio."'
								 WHERE Puetem = '".$tema."' 
								   AND Puecod = '".$puestoTrabajo."'
								";
				mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlUpdatePues."):</b><br>".mysql_error());

				
				// --> Guardar log del cambio de puesto
				$sqlLog = "
				INSERT INTO ".$wcliame."_000300
				        SET Medico 		= 'cliame',
					        Fecha_data 	= '".date("Y-m-d")."',
					        Hora_data 	= '".date("H:i:s")."',
							Logtem 		= '".$tema."',
							Logusu 		= '".$wusuario."',
							Logpue 		= '".$puestoTrabajo."',
							Logser 		= '".$servicio."',
							Seguridad	= 'C-".$wuse."'
				";
				mysql_query($sqlLog, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlLog."):</b><br>".mysql_error());
			}
		}

		echo json_encode($respuesta);
		return;			
}


if (isset($consultaAjax) && $consultaAjax == 'cancelarLlamarPacienteAtencion')
{
		global $wcliame;
		$respuesta = array('Error' => FALSE, 'Mensaje' => '');

		// Consultar el tema y servicio según el consultorio seleccionado
		$infoServicio = consultarServicio($puestoTrabajo,$wcliame,2);
		list($tema, $servicio) = explode('|', $infoServicio);	

		// --> Cancelar el turno
		$sqlCancelar = "
						UPDATE ".$wcliame."_000304
						   SET Turllv = 'off',
							   Turhll = '0000-00-00 00:0:00',
							   Turull = '',
							   Turven = ''
						 WHERE Turtem in (".$tema.")
						   AND Turtur = '".$turno."'
						";
	
		$resCancelar = mysql_query($sqlCancelar, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlCancelar."):</b><br>".mysql_error());

		// --> Registrar en el log la cancelacion del llamado
		guardarLog($turno, "llamadoCancelado", $tema);			

		echo json_encode($respuesta);
		return;	
}

   

function calculartiempoEstancia($whis,$wing, $wfec)
{
	 global $conex;
	 global $wbasedato;
	 
	 $q = " SELECT TIMEDIFF($wfec,fecha_data) "
         ."   FROM ".$wbasedato."_000018 "
         ."  WHERE ubihis  = '".$whis."' "
         ."    AND ubiing  = '".$wing."' "
         ."    AND ubiald != 'on' "; 
     $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());		 
	 $row = mysql_fetch_array($res);
	 
	 return $row[0]; 
}       
      
	  
function ponerConsulta($whce, $whis, $wing, $wusuario, $irhce, $wesp, $wamb)
{

	global $conex;
	$wfecha = date("Y-m-d");   
    $whora  = (string)date("H:i:s");
    
	if ($irhce != "on")
	{
		
		if($wesp=='1')
		{
			//2013-06-25
			$q = "UPDATE ".$whce."_000022 "
				."	 SET mtrcur = 'on', "                         //Indica que esta en consulta
				."       mtrfco = '".$wfecha."', "                //Fecha en que comienza la consulta
				."       mtrhco = '".$whora."', "
				."       mtrmed = '".$wusuario."' "
				." WHERE mtrhis = '".$whis."' "
				."	 AND mtring = '".$wing."' ";
		}
		else
		{
			$q = "UPDATE ".$whce."_000022 "
				."	 SET mtrcur = 'on', "                         //Indica que esta en consulta
				."       mtrfco = '".$wfecha."', "                //Fecha en que comienza la consulta
				."       mtrhco = '".$whora."' "
				." WHERE mtrhis = '".$whis."' "
				."	 AND mtring = '".$wing."' "
				."	 AND mtrmed = '".$wusuario."' ";
		}

		
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
		liberarConexionBD($conex);
		
		if ($res)
			return "ok";
		else
			return "No se pudo realizar la asignación. \n Error: ".$res;
	}
}


function consultarInformacionturno($conex,$cedula,$wtipoide,$wfechacon,$wcliame,$wmovhos)
{
	    $wturno = array();

	    if ($wtipoide != '')
	    	$filtro = " AND Turtdo = '".$wtipoide."' ";
	    else
	    	$filtro = "";

        $sqlTurno ="	SELECT Turtur,Turllv,Turpat,Turull
				  FROM ".$wcliame."_000304 
				 WHERE Fecha_data = '".date("Y-m-d")."'				   
				   AND Turdoc = '".$cedula."'
				   AND Turest = 'on' 
				   ".$filtro."
		      ORDER BY Fecha_data Desc ";

			$resTurno = mysqli_query_multiempresa($conex, $sqlTurno) or die("<b>ERROR EN QUERY MATRIX():</b><br>".mysqli_error());

		if( $resTurno && $resTurno->num_rows>0){

			$rowTurno = mysqli_fetch_assoc($resTurno);
			$wturno['Turtur']   = $rowTurno['Turtur'];
			$wturno['Turllv']   = $rowTurno['Turllv'];
			$wturno['Turpat']   = $rowTurno['Turpat'];
			$wturno['Turull']   = $rowTurno['Turull'];
		}
		
		return $wturno;
}


function consultarInformacioncitas($conex,$cedula,$wfechacon,$wcliame,$wmovhos,$opcion)
{
          //Recorrer en la tabla de servicios consultando código del centro de costos 
          //para buscar el prefijo e iniciar la búsqueda
          
          $restem    = "";
          $arrCitas  = array();
          $resultado = "";

          $sqlPrefijo = "SELECT Sercod,Sercdc,Sernom
                         FROM  ".$wcliame."_000298
                        WHERE    Serbus  = 'on' 
                          AND    Sercdc != ''
                          AND    Serest  = 'on' 
                        GROUP BY Sercdc";

			$resPrefijo = mysqli_query_multiempresa($conex, $sqlPrefijo) or die("<b>ERROR EN QUERY MATRIX():</b><br>".mysqli_error());

          while( $rowPre = mysqli_fetch_assoc($resPrefijo) )
          {
                //buscar centro de costos
                $sqlCentrocos =  " SELECT Ccocod,Cconom,Ccocip,Ccococ 
                                  FROM ".$wmovhos."_000011                        
                                  WHERE Ccocod = '".$rowPre['Sercdc']."'
                                    AND Ccocip !='' ";

				$resCentro  =  mysqli_query_multiempresa($conex,$sqlCentrocos) or die ("Error: en el query:  - ".mysqli_error());

                if( $resCentro && $resCentro->num_rows>0)
                {
              
	                if( $rowCentro = mysqli_fetch_assoc($resCentro) )
	                {

	                    $prefijo = $rowCentro['Ccocip'];
	                    $nomcen  = $rowCentro['Cconom'];
	                    $tabla   = $rowCentro['Ccococ'];
	                    
	                    //Busco las citas
	                    if ( $opcion==1 )
	                    {
		                     $filtro = " AND P9.Cedula = '".$cedula."'";
	                    }         
	                    else
	                    {
	                    	 $filtro = " AND P10.Meddoc = '".$cedula."'"; //Busco por la cédula del médico
	                    } 

	                    $resTabexiste =  mysqli_query_multiempresa($conex,"SHOW TABLES LIKE '".$prefijo."_".$tabla."'");

	                    if( $resTabexiste && $resTabexiste->num_rows>0)
	                    {

		                    $sqlCitas =  " SELECT P9.Cedula,P9.Nom_pac,P9.Hi,P9.Cod_equ,P9.Fecha,P10.Descripcion
			                                      FROM ".$prefijo."_".$tabla." P9  
			                                      INNER JOIN ".$prefijo."_000010 P10
			                                         ON P9.Cod_equ = P10.Codigo                   
			                                      WHERE P9.Activo = 'A'
			                                        AND P9.Fecha  ='".$wfechacon."'
			                                        ".$filtro."
			                                      GROUP BY P9.Fecha,P9.Hi  ";         

			                //echo ' con '.$sqlCitas;

							$resCitas  =  mysqli_query_multiempresa($conex,$sqlCitas) or die ("Error: en el query:  - ".mysqli_error());

		                    if( $resCitas && $resCitas->num_rows>0)
		                    {

		                        while($rowCitas = mysqli_fetch_assoc($resCitas))
		                        {
									if ( $opcion==1 )
									{                                  
									    $nomcen  = $rowCitas['Descripcion'];                   
			                            $restem = "<span id='spProfesional' data-html='true' title='Profesional:"."\n".strtoupper($rowCitas["Descripcion"])."'>".substr($rowCitas['Hi'],0,2).":".substr($rowCitas['Hi'],2,2)." - ".$nomcen."</span>";

			                            $arrCitas[$rowCitas['Hi']] = $restem;
		                            }
		                            else
		                            {
		                            	$arrCitas[$rowCitas['Cedula']] = array( "Hi"     => $rowCitas["Hi"], 
		                            										    "Cedula" => $rowCitas["Cedula"], 
		                            										    "Nompac" => $rowCitas["Nom_pac"]);
		                            }

		                        }
		                    }
	                    }

	                }
                }//fin if$resCentro->num_rows>0
          
          }

		  if ( $opcion==1 )
		  {
	           //Reordenar array resultado
	           arsort($arrCitas);

	           foreach( $arrCitas as $cod=>$nom )
	           {
		                if ($resultado == '')
		                    $resultado .= "<br>";
		                else 
		                    $resultado .= "<hr>";

		                $resultado .= $nom;
	           }

	           if ($resultado == '')
	               $resultado ='Sin cita';

	           return $resultado;
          }
          else
          {
          	 uasort($arrCitas, 'ordenarHoracita');
        	 return $arrCitas;
          }
          
}
 
// Esta función ordena por subindice de un array
function ordenarHoracita ($a, $b) {
    return $a['Hi'] - $b['Hi'];
}  
   
function ponerConducta($whce, $whis, $wing, $wconducta)
{
    global $conex;
	global $wbasedato;
	
	$wfecha=date("Y-m-d");   
    $whora = (string)date("H:i:s");
    
	//Si la conducta es nula, ELSE solo termino la consulta y asigno la conducta nula osea borro lo que habia
	//por el THEN coloco la conducta y la hora de terminacion de la consulta
	if (trim($wconducta) != "")
	{
		$q = " UPDATE ".$whce."_000022 "
			."    SET mtrcur = 'off', "                        //Termina la consulta
			."        mtrcon = '".$wconducta."', "             //Asume una conducta, lo que indica que ya termino la consulta
			."        mtrftc = '".$wfecha."', "                //Fecha en que Termina la consulta
			."        mtrhtc = '".$whora."' "				   //Hora en que Termina la consulta	
			."  WHERE mtrhis = '".$whis."' "
			."    AND mtring = '".$wing."' ";
	}
    else
    {
		  $q = " UPDATE ".$whce."_000022 "
			  ."    SET mtrcur = 'off', "                        //Termina la consulta
			  ."        mtrcon = '".$wconducta."', "             //Asume una conducta, lo que indica que ya termino la consulta
			  ."        mtrfco = '0000-00-00', "
              ."        mtrhco = '00:00:00', "		
			  ."        mtrftc = '0000-00-00', "
              ."        mtrhtc = '00:00:00' "			  
		      ."  WHERE mtrhis = '".$whis."' "
			  ."    AND mtring = '".$wing."' ";
    }		 
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	//Evaluo si la conducta colocada es de Alta o Muerte para hacer el egreso por cualquiera de estas dos condcutas
	$q = " SELECT conalt, conmue 
	         FROM ".$whce."_000035 
			WHERE concod = '$wconducta' ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$wnum = mysql_num_rows($res);
			
	if ($wnum > 0)
	{
	   $row = mysql_fetch_array($res);
	   $walt=$row[0];
	   $wmue=$row[1];
	   
	   if ($walt=="on" or $wmue=="on")
	   {
		   $wmot="Alta";
		   if ($wmue=="on")
		      { $wmot="Muerte";}
			  
		   //=============  Mayo 13 de 2011	===================================================================================
		   //Coloco en proceso de Alta la historia por cualquiera de las dos conductas, para que luego el facturador de
           //el Alta Definitiva		   
		   $q = " UPDATE ".$wbasedato."_000018 "
		       ."    SET ubialp = 'on', "
			   ."        ubifap = '".$wfecha."', "
			   ."        ubihap = '".$whora."' "
			   ."  WHERE ubihis = '".$whis."' "
			   ."    AND ubiing = '".$wing."' ";
		   $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		   
		   //OJO ========================================================
		   //Se quita para que todas las altas las hagan los facturadores
		   //   altaDefinitiva($whis, $wing, $wmot, $wmue);	  
	    }
		else
        {
			 //=============  Mayo 13 de 2011	===================================================================================
			 //Si la conducta es diferente a Alta o Muerte, me aseguro de colocar el 'ubialp' en 'off'
             $q = " UPDATE ".$wbasedato."_000018 "
		         ."    SET ubialp = 'off', "
				 ."        ubifap = '0000-00-00', "
			     ."        ubihap = '00:00:00'  "
			     ."  WHERE ubihis = '".$whis."' "
			     ."    AND ubiing = '".$wing."' ";
		     $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
        }			
	} 
	liberarConexionBD($conex);
}

   
function altaDefinitiva($whis, $wing, $wmot, $wmue)
{
	  global $conex;
	  global $wbasedato;
	  global $wcco;
	  global $wusuario;
	   
	  $wfecha=date("Y-m-d");   
      $whora = (string)date("H:i:s");
	   
	  //Actualizo la historia como Alta Definitiva   
	  $q = " UPDATE ".$wbasedato."_000018 "
		  ."    SET ubiald  = 'on', "
		  ."        ubimue  = 'on', "
		  ."        ubifad  = '".$wfecha."',"
		  ."        ubihad  = '".$whora."', "
		  ."        ubiuad  = '".$wusuario."' "
		  ."  WHERE ubihis  = '".$whis."'"
		  ."    AND ubiing  = '".$wing."'"
		  ."    AND ubiald != 'on' " 
		  ."    AND ubiptr != 'on' ";
	  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 
			
      $wnuming=1;			
      $wdiastan=calculartiempoEstancia($whis, $wing, $wfecha);
	  if ($wdiastan=="")
          $wdiastan=0;
		 
      $wmotivo="ALTA";			
	  if ($wmot == "Muerte")
	  {
		  if ($wdiastan>=2)
			 $wmotivo="MUERTE MAYOR A 48 HORAS";
		  else 
			  $wmotivo="MUERTE MENOR A 48 HORAS";

		  cancelar_pedido_alimentacion($whis, $wing, 'Muerte');
		  $wmotivo="Muerte";
      }
	  else
           cancelar_pedido_alimentacion($whis, $wing, 'Cancelar');		
		 
	  //Grabo el registro de egreso del paciente del servicio
	  $q = " INSERT INTO ".$wbasedato."_000033 (   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica,   Num_ingreso,   Servicio ,  Num_ing_Serv,   Fecha_Egre_Serv ,   Hora_egr_Serv ,    Tipo_Egre_Serv,  Dias_estan_Serv, Seguridad        ) "
	      ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."'        ,'".$wing."'   ,'".$wcco."' ,".$wnuming."  ,'".$wfecha."'      ,'".$whora."'     , '".$wmotivo."'   ,".$wdiastan."    , 'C-".$wusuario."')";   
	  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
}

   

//=====================================================================================================================================================================
//Esta funcion tambien se utiliza en el programa de 'hoteleria.php'   Febrero 10 2010
function cancelar_pedido_alimentacion($whis,$wing,$wtrans)
{
	 global $wbasedato;
	 global $conex;
	 global $wfecha;   
	 global $whora;
	 global $wusuario;
	 
	 
	 switch ($wtrans)
	 {
	    case "Cancelar":         //Se presiono alta definitiva
		    {   
		 	 //Busco cual es el ultimo Servicio que tiene registrado el paciente en la fecha y hora
			 //junto con la accion realizada sobre este sin importar si esta activa o no.
			 //si tiene alguno valido que pueda ser cancelado
			 $q = " SELECT MAX(A.fecha_data), movser, audacc, movest "
		         ."   FROM ".$wbasedato."_000077 A, ".$wbasedato."_000078 B"
		         ."  WHERE movfec      >= '".$wfecha."'"
		         ."    AND movhis       = '".$whis."'"
		         ."    AND moving       = '".$wing."'"
		         //."    AND movest       = 'on' "
		         ."    AND A.fecha_data = B.fecha_data "
		         ."    AND A.hora_data  = B.hora_data "
		         ."    AND movhis       = audhis "
		         ."    AND moving       = auding "
		         ."    AND movser       = audser " 
		         ."  GROUP BY 2, 3, 4 ";
		     $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		     $wnum = mysql_num_rows($res);
			 
		     if ($wnum > 0)
		        {
			     $row = mysql_fetch_array($res);    
			     
			     for ($i=1; $i<=$wnum;$i++)                //Marzo 1 de 2010
			        {   
				     $row = mysql_fetch_array($res);   
					     $wser=$row[1];
					     $west=$row[3];   
					        
					     if ($west == "on")
					     {
						     if ($row[2] != "ADICION")  //Osea que puede ser Pedido o Modificacion 
						     {
								 //Busco que el SERVICIO se pueda cancelar en el momento
								 $q = " SELECT COUNT(*) "
								     ."   FROM ".$wbasedato."_000076 "
								     ."  WHERE serhca >= '".$whora."'"
								     ."    AND serest = 'on' "
								     ."    AND sernom = '".$wser."'";
							 }
							 else
							 {
								   //Busco que el SERVICIO se pueda cancelar en el momento si es una ADICION
								   $q = " SELECT COUNT(*) "
								       ."   FROM ".$wbasedato."_000076 "
								       ."  WHERE serhad >= '".$whora."'"
								       ."    AND serest = 'on' "
								       ."    AND sernom = '".$wser."'";   
							 }    
							 $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
						     $row = mysql_fetch_array($res);
						     
						     if ($row[0] > 0)   //Si entra es porque SI se puede CANCELAR
						     {
							     $q = " SELECT COUNT(*) "
							         ."   FROM ".$wbasedato."_000077 "
							         ."  WHERE movfec = '".$wfecha."'"
							         ."    AND movhis = '".$whis."'"
							         ."    AND moving = '".$wing."'"
							         ."    AND movser = '".$wser."'"
							         ."    AND movest = 'on' ";
							     $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
							     $row = mysql_fetch_array($res);
							     
							     if ($row[0] > 0)
							     {
								     //Cancelo el PEDIDO de alimentacion   
								     $q = " UPDATE ".$wbasedato."_000077 "
								         ."    SET movest = 'off' "
								         ."  WHERE movfec = '".$wfecha."'"
								         ."    AND movhis = '".$whis."'"
								         ."    AND moving = '".$wing."'"
								         ."    AND movser = '".$wser."'"
								         ."    AND movest = 'on' ";
								     $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
								     
								     //Inserto en la auditoria la cancelacion por el alta definitiva
									 $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis  ,   auding  ,   audser  , audacc              ,   audusu      ,     Seguridad   ) "
									     ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."', 'CANCELACION X ALTA','".$wusuario."','C-".$wusuario."') ";
									 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
								  }    
							 }
							 else
						     {
							      //Inserto en la auditoria la cancelacion por el alta definitiva
								  $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis  ,   auding  ,   audser  , audacc                        ,   audusu      ,     Seguridad   ) "
								      ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."', 'ALTA - SERVICIO SIN CANCELAR','".$wusuario."','C-".$wusuario."') ";
								  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());   
							         
						          echo "<script language='Javascript'>";
								  echo "alert ('¡¡¡ ATENCION !!! EL SERVICIO ".$wser.", NO SE PUDO CANCELAR POR ESTAR FUERA DEL HORARIO');"; 
								  echo "</script>";
							 }
						  }    
					  }
		    	}
		    }
		    break;
	   
		case "Muerte":         //Se presiono Muerte
		    {   
		 	 //Busco que servicio se puede cancelar en el momento
			 $q = " SELECT sernom "
			     ."   FROM ".$wbasedato."_000076 "
			     ."  WHERE serhca <= '".$whora."'"
			     ."    AND serhad >= '".$whora."'"
			     ."    AND serest = 'on' ";
			 $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		     $wnum = mysql_num_rows($res);
		     
		     //Busco el servicio correspondiente a la hora actual, si lo encuentra es porque se puede cancelar alguno
		     if ($wnum > 0)
		        {
			     for ($i= 1; $i<=$wnum;$i++)
			        {
				     $row = mysql_fetch_array($res);   
				        
				     $q = " SELECT COUNT(*) "
				         ."   FROM ".$wbasedato."_000077 "
				         ."  WHERE movfec = '".$wfecha."'"
				         ."    AND movhis = '".$whis."'"
				         ."    AND moving = '".$wing."'"
				         ."    AND movser = '".$row[0]."'"
				         ."    AND movest = 'on' ";
				     $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
				     $wnum = mysql_num_rows($res);
				     
				     if ($wnum > 0)
				        {
					     //Cancelo el PEDIDO de alimentacion   
					     $q = " UPDATE ".$wbasedato."_000077 "
					         ."    SET movest = 'off' "
					         ."  WHERE movfec = '".$wfecha."'"
					         ."    AND movhis = '".$whis."'"
					         ."    AND moving = '".$wing."'"
					         ."    AND movser = '".$row[0]."'"
					         ."    AND movest = 'on' ";
					     $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
					     
					     //Inserto en la auditoria la cancelacion por el alta definitiva
					     $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis  ,   auding  ,   audser    , audacc              ,   audusu      ,     Seguridad   ) "
						     ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$row[0]."', 'CANCELACION X MUERTE','".$wusuario."','C-".$wusuario."') ";
						 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());   
				        }
				    }        
			    }
			  else
		         {
		          echo "<script language='Javascript'>";
				  echo "alert ('¡¡¡ ATENCION !!! TENIA PEDIDO DE ALIMENTACION, NO SE PUDO CANCELAR');"; 
				  echo "</script>";
			     }
		    }
		    break;
	  }	//Fin del swicht           
}
	
function convenciones($fecing, $hora)
{
    $wfecha=date("Y-m-d");   
    
    $a1=$hora;
	$a2=date("H:i:s");
	$a3=((integer)substr($a2,0,2)-(integer)substr($a1,0,2))*60 + ((integer)substr($a2,3,2)-(integer)substr($a1,3,2)) + ((integer)substr($a2,6,2)-(integer)substr($a1,6,2))/60;
	
	$wcolor="";
	
	//Aca configuro la presentacion de los colores segun el tiempo de respuesta
	if ($a3 > 35 or $wfecha != $fecing)                   //Mas de 35 Minutos
	{
		$wcolor = "FFCC99";        //Rojo
	}
	if ($a3 > 20.1 and $a3 <= 35 and $wfecha == $fecing)  //de 20 Minutos a 35
	{
		$wcolor = "FFFF66";        //Amarillo  
	} 
	if ($a3 <= 20 and $wfecha == $fecing)                 //20 Minutos
	{ 
		$wcolor = "99FFCC";        //Verde   
	} 
	 
    return $wcolor;
}

// --> Consulta con información de la cita, si se le ha realizado admisión en la fecha actual
function consultarAdmision($conex,$wemp_pmla,$cedula,$wtid,$wfechaact,$wcliame,$wbasedato,$whce)
{
      // Consultar por identificación del paciente en cliame_000100      
      $historia  = '';
      $documento = '';
      $tipodocu  = '';
      $resultado = array();
      
      $conHistoria = "SELECT cli100.Pachis, cli100.Pacdoc, cli100.Pactdo
                     FROM ".$wcliame."_000100 cli100
                     WHERE cli100.Pacdoc = '".$cedula."' 
                       AND cli100.Pacact = 'on' ";

	 $resHistoria = mysqli_query_multiempresa($conex,$conHistoria) or die(mysqli_errno()." - Error en el query  - ".mysqli_error());

      if( $resHistoria && $resHistoria->num_rows>0 )
      {
          $row = mysqli_fetch_assoc($resHistoria);
          $historia  = $row['Pachis'];
          $documento = $row['Pacdoc'];
          $tipodocu  = $row['Pactdo'];
      }


 	  // Cuando el programa contenga la constante $wamb=1 en la url, deberá acceder a los parámetros como servicio ambulatorio
      $conAdmision = " SELECT C101.Inghis, C101.Ingnin, C101.fecha_data, m18.Ubiald, r36.pacno1, r36.pacno2, r36.pacap1,  
                              r36.pacap2, r36.pactid, r36.pacced, h22.mtrcur, h22.mtrcon, C101.hora_data "
					  ."   FROM root_000036 r36, root_000037 r37, ".$wcliame."_000101 C101, ".$wbasedato."_000018 m18, ".$whce."_000022 h22"
					  ."  WHERE C101.Inghis = r37.orihis "
					  ."    AND C101.Ingnin = r37.oriing "
					  ."    AND C101.Ingfei = '".$wfechaact."' "		  
					  ."    ANd C101.Inghis = '".$historia."'  "
					  ."    AND r37.oriori  = '".$wemp_pmla."' "  //Empresa Origen de la historia, 
					  ."    AND r37.oriced  = r36.pacced "
					  ."    AND r37.oritid  = r36.pactid "
					  ."    AND m18.Ubihis  = C101.Inghis "
			          ."    AND m18.Ubiing  = C101.Ingnin "	
					  ."    AND C101.Inghis = h22.mtrhis "
					  ."    AND C101.Ingnin = h22.mtring "
					  //."    AND m18.ubiald != 'on' " 	
					  ."  ORDER BY fecha_data desc LIMIT 1";

	 $resAdmision = mysqli_query_multiempresa($conex,$conAdmision) or die(mysqli_errno()." - Error en el query  - ".mysqli_error());

      if( $resAdmision && $resAdmision->num_rows>0 ){
          $row = mysqli_fetch_assoc($resAdmision);
          $resultado['whis']    = $row['Inghis'];
          $resultado['wing']    = $row['Ingnin'];
          $resultado['wfecha']  = $row['fecha_data'];
          $resultado['whora']   = $row['hora_data'];
          $resultado['wcur']    = $row['mtrcur'];
          $resultado['wcon']    = $row['mtrcon'];
          $resultado['wnompa']  = $row['pacno1'].' '.$row['pacno2'].' '.$row['pacap1'].' '.$row['pacap2'];
          $resultado['wdpa']    = $documento;
          $resultado['wtid']    = $tipodocu;          
          $resultado['wubiald'] = $row['Ubiald'];
      }



      return $resultado;
}

//--> Consultar los pacientes que pertenecen por fecha, servicio y médico en la admisión o tienen cita asignada con dicho médico
function mostrarPacientesPropios($wbasedato, $wcliame, $whce, $wemp_pmla, $wcco, $wusuario, &$i, $wservicio, $wesp, $wamb, $ccoLista)
{
  global $conex;
 
  $wespecialidad = "";
  $wfechaact     = date("Y-m-d");
  $mensajecon    = "NO HAY PACIENTES PENDIENTES DE ATENCION"; 

  //Obtengo la especialidad del profesional
  $q = " SELECT medesp,meddoc "
      ."   FROM ".$wbasedato."_000048 "
	  ."  WHERE meduma = '".$wusuario."'"
	  ."    AND medest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

  if ($num > 0)
  {
      $row = mysql_fetch_array($res);
	  $stresp  = explode("-",$row[0]);
	  $wespecialidad = trim($stresp[0]);
	  $wmedico = $row[1];
  }

  
  if ( isset($wamb) &&  $wamb == '1' )
  {
  	  // Busqueda de centros de costos relacionados  	  
  	  // Cuando el programa contenga la constante $wamb=1 en la url, deberá acceder a los parámetros como servicio ambulatorio
      $q = " SELECT C101.Inghis, C101.Ingnin, r36.pacno1, r36.pacno2, r36.pacap1, r36.pacap2, 
                    C101.fecha_data, r36.pactid, r36.pacced,h22.mtrcur, h22.mtrcon, C101.hora_data "
		  ."   FROM root_000036 r36, root_000037 r37, ".$wcliame."_000101 C101, ".$wbasedato."_000018 m18, ".$whce."_000022 h22"
		  ."  WHERE C101.Inghis  =  r37.orihis "
		  ."    AND C101.Ingnin  =  r37.oriing "
		  ."    AND C101.Ingfei  =  '".$wfechaact."' "		  
		  ."    AND C101.Ingmei  =  '".$wmedico."' "
		  ."    AND r37.oriori   =  '".$wemp_pmla."' "  //Empresa Origen de la historia, 
		  ."    AND r37.oriced   =  r36.pacced "
		  ."    AND r37.oritid   =  r36.pactid "
     	  ."    AND C101.Ingsei  in  (".$ccoLista.") " //Servicio Actual
		  ."    AND m18.Ubihis   =  C101.Inghis "
          ."    AND m18.Ubiing   =  C101.Ingnin "	
		  ."    AND C101.Inghis  =  h22.mtrhis "
		  ."    AND C101.Ingnin  =  h22.mtring "
		  ."    AND m18.ubiptr  !=  'on' "             //Solo los pacientes que no esten siendo trasladados
		  ."    AND m18.ubiald  !=  'on' " 	
		  ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
		  ."  ORDER BY 7, 12 ";

	  // Adicionar los pacientes que tienen la cita pero que no aparecen en la admisión por tener varios servicios
      $pacientesCita = consultarInformacioncitas($conex,$wmedico,$wfechaact,$wcliame,$wbasedato,'2');

  }
  else
  {
  	  //2013-06-25
	  if($wesp=='1')
		 $condEspMed = " AND mtreme  = '".$wespecialidad."'";
	  else
		 $condEspMed = " AND mtrmed  = '".$wusuario."'";

	  //Aca trae los pacientes que estan en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
	  //y que no esten ni en proceso ni en alta
	  $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data "
		  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D"
		  ."  WHERE ubihis  = orihis "
		  ."    AND ubiing  = oriing "
		  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
		  ."    AND oriced  = pacced "
		  ."    AND oritid  = pactid "
		  ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
		  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
		  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
		  ."    AND ubihis  = mtrhis "
		  ."    AND ubiing  = mtring "
		  .$condEspMed
		  ."    AND mtrcon IN ('', 'NO APLICA') "
		  ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
		  ."  ORDER BY 7, 12 ";

  }

    echo "<table>";
	echo "<tr class=encabezadoTabla>";
	echo "<th>Semaforo</th>";
    if ( isset($wamb) &&  $wamb == '1' )
      	 echo "<th>Acciones</th>";
	echo "<th>Fecha de Ingreso</th>";
	echo "<th>Hora de Ingreso</th>";
	echo "<th>Historia</th>";
    echo "<th>Documento <br> de Identidad</th>";
	echo "<th>Paciente</th>";  
	if ( isset($wamb) &&  $wamb == '1' )
	{
	     echo "<th><center>Informaci&oacute;n Cita(s)</center></th>";
	}
	echo "<th>Ir a Historia</th>";
	echo "<th>Conducta a Seguir</th>";
	echo "<th>Afinidad</th>";
	echo "</tr>";

   //////////////////////////////////////////////////////////////////////////////////////////////////////
   // --> Consultar registros de pacientes desde citas x cedula. Solo para centros de costos ambulatorios
   if ( isset($wamb) &&  $wamb == '1' )
   {
   	        $contad=0;
			// --> Recorrer pacientes asignados por cita
		    $i=1;
			foreach( $pacientesCita as $codCita => $nomCita )
			{
				   $contad++;
				   $mensajecon='';
				   $wegresado=0;
				   $wclass = (($wclass == 'fila2') ? 'fila1' : 'fila2');
						   /////////////////////////////////////////////////////////////////////////////////////////////////
				   // --> Consultar información de admisión para obtener numero de historia e ingreso
				   $whis = '';
				   $wing = '';
				   $wdpa = '';
				   $wtid = '';
				   $wfin = '';
				   $whin = '';
				   $winfoAdmision = consultarAdmision($conex,$wemp_pmla,$nomCita['Cedula'],$wtid,$wfechaact,$wcliame,$wbasedato,$whce);

                   //Verificar que el paciente esté admitido pero dado de alta
				   if ( count($winfoAdmision) >0 && $winfoAdmision['wubiald'] == 'on' ){
                       $wegresado = 1;
				   }
		   	  	   
		   	  	   if ( $wegresado==0 )
		   	  	   {
		   	  	        echo "<tr class=".$wclass.">";
		                echo "<td align=center bgcolor=".$wcolor.">&nbsp</td>";

				        $irhce= "on";  //Permite ingresar a la hce sin dar clic sobro el radio button de ir a hce
				  
  				   		/////////////////////////////////////////////////////////////////////////////////////////////////
		           		// --> Buscar si el paciente ya ingresó al centro médico (si solicitó turno)
		           		$wtid ='';
		           		$winfoTurno = consultarInformacionturno($conex,$nomCita['Cedula'],$wtid,$wfechaact,$wcliame,$wbasedato);	
		           	 
					   // --> Adicionar columna de acciones para llamar en el Monitor y cancelar el respectivo llamado
					   if ( count($winfoTurno) > 0 && count($winfoAdmision) > 0)
					   {
		                  if( $winfoTurno['Turllv']=='on' && $winfoTurno['Turull']==$wusuario ){
		                  	  $styleLlamar='cursor:pointer;display:none';
		                  	  $stylecolgar='cursor:pointer;';
		                  }
		                  else{	
		                  	  $styleLlamar='cursor:pointer;';
		                  	  $stylecolgar='cursor:pointer;display:none';
		                  }

		                  list($fectur,$numtur) = explode('-',$winfoTurno['Turtur']);

						  echo " <td align='center' >
									<img id='imgLlamar".$winfoTurno['Turtur'] ."' 	style='".$styleLlamar."' 				class='botonLlamarPaciente' width='20' heigth='20' tooltip='si' title='Llamar ".$numtur."' 				src='../../images/medical/root/Call2.png'		onclick='llamarPacienteAtencion(\"".$whis."\",\"".$wing."\",\"".$winfoTurno['Turtur']."\")'>
									<img id='botonColgar".$winfoTurno['Turtur'] ."' 	style='".$stylecolgar."' 	class='botonColgarPaciente' width='20' heigth='20' tooltip='si' title='Cancelar llamado ".$numtur."'  	src='../../images/medical/root/call3.png'		onclick='cancelarLlamarPacienteAtencion(\"".$winfoTurno['Turtur'] ."\")'>
									<img id='botonLlamando".$winfoTurno['Turtur'] ."' style='".$stylecolgar."' 					class='botonColgarPaciente' 																src='../../images/medical/ajax-loader1.gif'>
									<span id='enProcesoAtencion".$winfoTurno['Turtur'] ."' style='display:none;font-size:7pt;color:#E1017B' class='botonColgarPaciente'>Atenci&oacute;n en proceso...</span>
								</td>";
					   }
					   else{
					   		if (count($winfoTurno) > 0){
					   			list($fectur,$numtur) = explode('-',$winfoTurno['Turtur']);
                                echo "<td align='center'>Turno ".$numtur."<br> Ingres&oacute; al centro</td>";
						  	}						  	
						  	else	
					            echo " <td align='center'></td> ";
					   }

					   ////////////////////////////////////////////////////////////////////////////////////////////////////
		               // Si el paciente ya tiene admisión ese día se habilita el llamado e ingreso a la historia
		                  
		                  $wnompaci = $nomCita['Nompac'];
						  if (count($winfoAdmision) > 0){
							  $whis     = $winfoAdmision['whis'];
							  $wing     = $winfoAdmision['wing'];
							  $wdpa     = $winfoAdmision['wdpa'];
							  $wtid     = $winfoAdmision['wtid'];
							  $wfin     = $winfoAdmision['wfecha'];
							  $whin     = $winfoAdmision['whora'];
							  $wnompaci = $winfoAdmision['wnompa'];
						  }
		                  
				 		  echo "<td align=center>   ".$wfin."</td>";
					  	  echo "<td align=center>   ".$whin."</td>";
					  	  echo "<td align=center width=10%><b>".$whis." - ".$wing."</b></td>";
					  	  echo "<td align=center width=10%><b>".$wtid." - ".$wdpa."</b></td>";
						  echo "<td align=left  width=15%><b>".$wnompaci."</b></td>";
				          		  
				          /////////////////////////////////////////////////////////////////////////////////////////////////////
						  // --> Consultar si el paciente tiene citas en otros servicios de la unidad
						  $winfoCitas = consultarInformacioncitas($conex,$nomCita['Cedula'],$wfechaact,$wcliame,$wbasedato,'1');
						  echo "<td align=left nowrap><b><center>".$winfoCitas."</center></b></td>";

						  if (count($winfoAdmision) >0 && $winfoAdmision['wubiald'] !== 'on'){

							  if ($winfoAdmision['wcur'] == "on"){
								  echo "<td align=center><input type='radio' name='wirhce$i' id='wirhce$i' onclick='activarConsulta($whis, $wing,\"$wservicio\", \"$wdpa\", \"$wtid\", $i)' checked></td>";
							  }
							  else {
								  echo "<td align=center><input type='radio' name='wirhce$i' id='wirhce$i' onclick='activarConsulta($whis, $wing,\"$wservicio\", \"$wdpa\", \"$wtid\", $i, \"$irhce\")'>";
							  }
							  
							  echo "<td align=center><select id='conducta$i' name='wconducta$i' onchange='colocarConducta($whis, $wing, $i, \"$irhce\")'>"; 
						 
							  echo "<option value=''>&nbsp</option>";
							 
							  //============================================================================================================
							  //Aca coloco todas las conductas
							  //============================================================================================================
							  $qConducta = " SELECT concod, condes "
										 . "   FROM ".$whce."_000035 "
										 . "  WHERE conest = 'on' "
										 ."     AND INSTR(conser,'".$wservicio."') > 0 ";
							  
							  $resConducta = mysql_query($qConducta, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$qConducta." - ".mysql_error());

							  $sel ='';
							  
							  $num1 = mysql_num_rows($resConducta);
							  for ($j=1;$j<=$num1;$j++)
							  {
								   $row1 = mysql_fetch_array($resConducta);
								   if (isset($winfoAdmision['wcon']) ) // Si selecciono una opcion del dropdown
							       {
							       	    echo '<br> $winfoAdmision '.$winfoAdmision['wcon'];
							       	    
		 								if ($row1['concod'] == $winfoAdmision['wcon'])
		 									$sel = 'selected';
		 								else
		 									$sel = '';
							       }
								   echo "<option value=$row1[0] ".$sel.">".$row1[1]."</option>";
							  }
							  echo "</select></td>";
						  }
						  else
						  {
				
							  echo "<td>&nbsp</td>";
							  echo "<td>&nbsp</td>";

						  }
						  		  
						  //======================================================================================================
						  //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
						  $wafin=clienteMagenta($wdpa,$wtid,$wtpa,$wcolorpac);
						  if ($wafin)
						  	 echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
						  	else
						  echo "<td>&nbsp</td>";
						  //======================================================================================================     

					  	  echo "</tr>";
				  }
			  	  $i++;
			  	 
			}	  
  }// Fin if para servicios ambulatorios
  
  /////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Pintar pacientes ingresados por admisión asignados al médico logueado

  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

  if ( !isset($wamb) or $wamb !== '1' )
       $i=1;

  if ($num > 0)
  {
  	  //Recorrer todos los pacientes registrados desde la admisión
	  for($k=1;$k<=$num;$k++)
	  {
		  $row = mysql_fetch_array($res);

		  if (is_integer($k/2))
			  $wclass="fila1";
		  else
			  $wclass="fila2";
		  
		  $whis = $row[0];
		  $wing = $row[1];
		  $wpac = $row[2]." ".$row[3]." ".$row[4]." ".$row[5];
		  $wfin = $row[6];     //Fecha de Ingreso
		  $wtid = $row[7];		  
		  $wdpa = $row[8];     
		  $wcur = $row[9];     //Indicador de si esta en Consulta
		  $wcon = $row[10];    //Conducta
		  $whin = $row[11];    //Hora de Ingreso
		  
		  $wcolor    = convenciones($wfin, $whin);
		  $wregistro = 1;

          // Consultar si el paciente ya fue registrado por la opción citas
          if (isset($wamb) &&  $wamb == '1')
          {
		      if ( array_key_exists($wdpa,$pacientesCita) ){	  
			  	   $wregistro = 0;
			  }
		  }
								
		  if ($wregistro == 1)
		  {
			  echo "<tr class=".$wclass.">";
			  echo "<td align=center bgcolor=".$wcolor.">&nbsp</td>";

			  //Consultar información del Turnero
			  if ( isset($wamb) &&  $wamb == '1' )
			  {

			       $winfoTurno = consultarInformacionturno($conex,$wdpa,$wtid,$wfechaact,$wcliame,$wbasedato);		  
	  
				   //Adicionar columna de acciones para llamar en el Monitor y cancelar el respectivo llamado
				   if (count($winfoTurno)>0)
				   {
	                  if( $winfoTurno['Turllv']=='on' && $winfoTurno['Turull']==$wusuario ){
	                  	  $styleLlamar='cursor:pointer;display:none';
	                  	  $stylecolgar='cursor:pointer;';
	                  }
	                  else{	
	                  	  $styleLlamar='cursor:pointer;';
	                  	  $stylecolgar='cursor:pointer;display:none';

	                  }
                      list($fectur,$numtur) = explode('-',$winfoTurno['Turtur']);
					  echo " <td align='center' >
								<img id='imgLlamar".$winfoTurno['Turtur'] ."' 	style='".$styleLlamar."' 				class='botonLlamarPaciente' width='20' heigth='20' tooltip='si' title='Llamar ".$numtur ."' 				src='../../images/medical/root/Call2.png'		onclick='llamarPacienteAtencion(\"".$whis."\",\"".$wing."\",\"".$winfoTurno['Turtur']."\")'>
								<img id='botonColgar".$winfoTurno['Turtur'] ."' 	style='".$stylecolgar."' 	class='botonColgarPaciente' width='20' heigth='20' tooltip='si' title='Cancelar llamado ".$numtur ."'  	src='../../images/medical/root/call3.png'		onclick='cancelarLlamarPacienteAtencion(\"".$winfoTurno['Turtur'] ."\")'>
								<img id='botonLlamando".$winfoTurno['Turtur'] ."' style='".$stylecolgar."' 					class='botonColgarPaciente' 																src='../../images/medical/ajax-loader1.gif'>
								<span id='enProcesoAtencion".$winfoTurno['Turtur'] ."' style='display:none;font-size:7pt;color:#E1017B' class='botonColgarPaciente'>Atenci&oacute;n en proceso...</span>
							</td>";
				   }
				   else
				      echo " <td align='center'></td> ";
				  
			  }

			  echo "<td align=center>   ".$wfin."</td>";
			  echo "<td align=center>   ".$whin."</td>";
			  echo "<td align=center><b>".$whis." - ".$wing."</b></td>";
			  echo "<td align=center><b>".$wtid." - ".$wdpa."</b></td>";
			  echo "<td align=left  ><b>".$wpac."</b></td>";
	          
	          if ( isset($wamb) &&  $wamb == '1' )
			  {
			       $winfoCitas = consultarInformacioncitas($conex,$wdpa,$wfechaact,$wcliame,$wbasedato,'1');
			       echo "<td align=left nowrap><b><center>".$winfoCitas."</center></b></td>";
	          }
			  
			  $irhce="off";  //Permite ingresar a la hce sin dar clic sobro el radio button de ir a hce
			  if ($wcur == "on")
				 echo "<td align=center><input type='radio' name='wirhce$i' id='wirhce$i' onclick='activarConsulta($whis, $wing,\"$wservicio\", \"$wdpa\", \"$wtid\", $i)' checked></td>";
			  else 
				   echo "<td align=center><input type='radio' name='wirhce$i' id='wirhce$i' onclick='activarConsulta($whis, $wing,\"$wservicio\", \"$wdpa\", \"$wtid\", $i, \"$irhce\")'>";
			  
			  echo "<td align=center><select id='conducta$i' name='wconducta$i' onchange='colocarConducta($whis, $wing, $i, \"$irhce\")'>";

			  if (isset($wcon) )                              //Si selecciono una opcion del dropdown
			  {
				  $q = " SELECT condes "
					 . "   FROM ".$whce."_000035 "
					 . "  WHERE concod = '".$wcon."'"
					 ."     AND INSTR(conser,'".$wservicio."') > 0 ";
				  $res2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row2 = mysql_fetch_array($res2);  	  
				 
				  echo "<option selected value=$wcon>".$row2[0]."</option>";
			  }
			 
			  echo "<option value=''>&nbsp</option>";
			 
			  //========================================================================================================
			  //Aca coloco todas las conductas
			  //========================================================================================================
			  $q = " SELECT concod, condes "
				 . "   FROM ".$whce."_000035 "
				 . "  WHERE conest = 'on' "
				 ."     AND INSTR(conser,'".$wservicio."') > 0 ";
			  $res1 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num1 = mysql_num_rows($res1);
			  for ($j=1;$j<=$num1;$j++)
			  {
				  $row1 = mysql_fetch_array($res1);
				  echo "<option value=$row1[0]>".$row1[1]."</option>";
			  }
			  echo "</select></td>";
			  
			  //=======================================================================================================		  
			  //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
			  $wafin=clienteMagenta($wdpa,$wtid,$wtpa,$wcolorpac);
			  if ($wafin)
				 echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
				else
				  echo "<td>&nbsp</td>";
			  //=======================================================================================================    
			  echo "</tr>";
		 } //fin vble wregistro;
		 $i++;
		}//fin for
	  }

   else
   {
		echo $mensajecon;
   }
   echo "</table>"; 
}  	
   
function mostrarPacientesComunes($wbasedato, $wcliame, $whce, $wemp_pmla, $wcco, $wusuario, $k, $wservicio)
{
  global $conex;
  global $wamb;
 
  $wgen  = "on";
  $wped  = "off";
  $wort  = "off";
  $wfechaact = date("Y-m-d");
  
  //Traigo los indicadores de si el medico es de urgencias y ademas es Pediatra u Ortopedista, si no, es porque es general
  $q = " SELECT medurg, medped, medort, medseu, medesp, medgen, medees "
      ."   FROM ".$wbasedato."_000048 "
	  ."  WHERE meduma = '".$wusuario."'"
	  ."    AND medest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);
  
  if ($num > 0)
  {
      $row = mysql_fetch_array($res);
	  
	  $wurg = $row[0];
	  $wped = $row[1];
	  $wort = $row[2];
      $wseu = $row[3];
	  $wesp = $row[4];
	  $wmge = $row[5];   //Medico general
	  $wees = $row[6];   //Indica si es especialista
      
      $wseu_aux = explode(",", $wseu);
      
      if(array_search($wservicio, $wseu_aux) !== false)      
         $wcomplementoquery = " AND mtrmed = '".$wusuario."' ";      
      
      
	  if ($wped == "on" or $wort == "on" or $wurg != "on" )   //Indica que es Especialista
	      $wgen = "off";
		
	  if ($wmge == "on")
	      $wgen = "on";
  }	 
  
  if ($wamb == 1)
  {
	  // Cuando el programa contenga la constante $wamb=1 en la url, deberá acceder a los parámetros como servicio ambulatorio
      $q = " SELECT C101.Inghis, C101.Ingnin, r36.pacno1, r36.pacno2, r36.pacap1, r36.pacap2, 
                    C101.fecha_data, r36.pactid, r36.pacced, h22.mtrcur, h22.mtrcon, 
                    C101.hora_data, m48.medno1, m48.medno2, m48.medap1, m48.medap2  "
		  ."   FROM root_000036 r36, root_000037 r37, ".$wcliame."_000101 C101,
		            ".$wbasedato."_000018 m18, ".$whce."_000022 h22, ".$wbasedato."_000048 m48"
		  ."  WHERE C101.Inghis = r37.orihis "
		  ."    AND C101.Ingnin = r37.oriing "
		  ."    AND C101.Ingmei = m48.Meddoc "
		  ."    AND C101.Ingsei = '".$wcco."'"       // Servicio Actual		  
		  ."    AND C101.Ingfei = '".$wfechaact."' "		  
		  ."    AND r37.oriori  = '".$wemp_pmla."' "  // Empresa Origen de la historia, 
		  ."    AND r37.oriced  = r36.pacced "
		  ."    AND r37.oritid  = r36.pactid "
		  ."    AND m18.Ubihis  = C101.Inghis "
          ."    AND m18.Ubiing  = C101.Ingnin "	
		  ."    AND C101.Inghis = h22.mtrhis "
		  ."    AND C101.Ingnin = h22.mtring "
		  ."    AND m18.ubiald = 'on' " 	
		  ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
		  ."  ORDER BY 7, 12 ";	  
  }
  else
  {
	  if ($wgen=="on")
	  {

		  //Aca trae los pacientes que estan en Urgencias en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
		  //y que no esten en proceso ni en alta y que sean de Medicos Generales
		  $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, medno1, medno2, medap1, medap2 "
			  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E, ".$wbasedato."_000048 F"
			  ."  WHERE ubihis  = orihis "
			  ."    AND ubiing  = oriing "
			  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
			  ."    AND oriced  = pacced "
			  ."    AND oritid  = pactid "
			  ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
			  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
			  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
			  ."    AND ubihis  = mtrhis "
			  ."    AND ubiing  = mtring "
			  ."    AND mtrcur != 'on' "
			  ."    AND mtrcon  = concod "
			  ."    AND conalt != 'on' "
			  ."    AND conmue != 'on' "
			  ."    AND concom  = 'on' "
			  ."    AND mtrmed  = meduma "
			  ."    AND medurg  = 'on' "
			  ."    AND medped != 'on' "
			  ."    AND medort != 'on' "
			  ."    AND INSTR(conser,'".$wservicio."') > 0 "     //Que la conducta que tiene sea del servicio por el cual ingreso a la HCE
			  ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
			  ."  ORDER BY 7, 12 ";
	  }
	  else
	  {
		    if ($wped == "on")     //Pediatras
			{
			  //Aca trae los pacientes que estan en Urgencias en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
			  //y que no esten en proceso ni en alta y que sean de Medicos Pediatras
			  $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, medno1, medno2, medap1, medap2 "
				  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E, ".$wbasedato."_000048 F"
				  ."  WHERE ubihis  = orihis "
				  ."    AND ubiing  = oriing "
				  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
				  ."    AND oriced  = pacced "
				  ."    AND oritid  = pactid "
				  ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
				  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
				  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
				  ."    AND ubihis  = mtrhis "
				  ."    AND ubiing  = mtring "
				  ."    AND mtrcur != 'on' "
				  ."    AND mtrcon  = concod "
				  ."    AND conalt != 'on' "
				  ."    AND conmue != 'on' "
				  ."    AND concom  = 'on' "
				  ."    AND mtrmed  = meduma "
				  ."    AND medurg  = 'on' "
				  ."    AND medped  = 'on' "
				  ."    AND medort != 'on' "
				  ."    AND INSTR(conser,'".$wservicio."') > 0 "     //Que la conducta que tiene sea del servicio por el cual ingreso a la HCE
				  ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16 "
				  ."  UNION ALL "
				  ." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, medno1, medno2, medap1, medap2 "
				  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E, ".$wbasedato."_000048 F"
				  ."  WHERE ubihis  = orihis "
				  ."    AND ubiing  = oriing "
				  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
				  ."    AND oriced  = pacced "
				  ."    AND oritid  = pactid "
				  ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
				  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
				  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
				  ."    AND ubihis  = mtrhis "
				  ."    AND ubiing  = mtring "
				  ."    AND mtrcur != 'on' "             //Indica que no este en Consulta en Urgencias
				  ."    AND mtrcon  = concod "           //Conducta que tiene   
				  ."    AND conalt != 'on' "             //Que la conducta no sea de Alta
				  ."    AND conmue != 'on' "             //Que la conducta no sea de Muerte
				  ."    AND concom  = 'on' "             //Que la conducta sea Comun osea que todos los medicos la puedan ever en la sala de espera
				  ."    AND mtrmed  = meduma "
				  ."    AND medurg  = 'on' "             //Que el medico sea de Urgencias
				  ."    AND medurg  = conurg "           //Que corresponda el indicador del Medico con el de la Conducta
				  ."    AND conped  = 'on' "             //Que sea una conducta de Pediatria              
				  ."    AND INSTR(conser,'".$wservicio."') > 0 "     //Que la conducta que tiene sea del servicio por el cual ingreso a la HCE
				  ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16 "
				  ."  ORDER BY 7, 12 ";
			}
			else
			   if ($wort == "on")   //Ortopedistas
			   {
				  //Aca trae los pacientes que estan en Urgencias en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
				  //y que no esten en proceso ni en alta y que sean de Medicos Ortopedistas

				  $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, medno1, medno2, medap1, medap2 "
					  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E, ".$wbasedato."_000048 F"
					  ."  WHERE ubihis  = orihis "
					  ."    AND ubiing  = oriing "
					  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
					  ."    AND oriced  = pacced "
					  ."    AND oritid  = pactid "
					  ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
					  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
					  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
					  ."    AND ubihis  = mtrhis "
					  ."    AND ubiing  = mtring "
					  ."    AND mtrcur != 'on' "             //Indica que no este en Consulta en Urgencias
					  ."    AND mtrcon  = concod "           //Conducta que tiene   
					  ."    AND conalt != 'on' "             //Que la conducta no sea de Alta
					  ."    AND conmue != 'on' "             //Que la conducta no sea de Muerte
					  ."    AND concom  = 'on' "             //Que la conducta sea Comun osea que todos los medicos la puedan ever en la sala de espera
					  ."    AND mtrmed  = meduma "
					  ."    AND medurg  = 'on' "             //Que el medico sea de Urgencias
					  ."    AND medurg  = conurg "          //Que corresponda el indicador del Medico con el de la Conducta                  
					  ."    $wcomplementoquery "             //Esta variable se da cuando el medico tiene como especialidad ortopedia y en la variable medseu tiene el servicio 07     
					  ."    AND conort  = 'on' "
					  ."    AND INSTR(conser,'".$wservicio."') > 0 "     //Que la conducta que tiene sea del servicio por el cual ingreso a la HCE
					  ."  UNION ALL "
					  ." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, medno1, medno2, medap1, medap2 "
					  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E, ".$wbasedato."_000048 F"
					  ."  WHERE ubihis  = orihis "
					  ."    AND ubiing  = oriing "
					  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
					  ."    AND oriced  = pacced "
					  ."    AND oritid  = pactid "
					  ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
					  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
					  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
					  ."    AND ubihis  = mtrhis "
					  ."    AND ubiing  = mtring "
					  ."    AND mtrcur != 'on' "             //Indica que no este en Consulta en Urgencias
					  ."    AND mtrcon  = concod "           //Conducta que tiene   
					  ."    AND conalt != 'on' "             //Que la conducta no sea de Alta
					  ."    AND conmue != 'on' "             //Que la conducta no sea de Muerte
					  ."    AND concom  = 'on' "             //Que la conducta sea Comun osea que todos los medicos la puedan ever en la sala de espera
					  ."    AND mtrmed  = meduma "
					  ."    $wcomplementoquery "                 //Esta variable se da cuando el medico tiene como especialidad ortopedia y en la variable medseu tiene el servicio 07
					  ."    AND INSTR(medseu,".$wservicio.") > 0 "       //Que el médico que esta ingresando sea del servicio por el cual ingreso a la HCE
					  ."    AND INSTR(conser,'".$wservicio."') > 0 "     //Que la conducta que tiene sea del servicio por el cual ingreso a la HCE
					  ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16 "
					  ."  ORDER BY 7, 12 ";
					  
					  //On
					  //echo $q."<br>";
				}
				else
				{ 
					//Septiembre 22 de 2011  ***
					//Por aca entra para los medicos que tengan especialidad diferente a los anteriores
					//Aca trae los pacientes que estan en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
					//y que no esten en proceso ni en alta.
					$q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, medno1, medno2, medap1, medap2 "
						."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E, ".$wbasedato."_000048 F"
						."  WHERE ubihis  = orihis "
						."    AND ubiing  = oriing "
						."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
						."    AND oriced  = pacced "
						."    AND oritid  = pactid "
						."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
						."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
						."    AND ubisac  = '".$wcco."'"       //Servicio Actual
						."    AND ubihis  = mtrhis "
						."    AND ubiing  = mtring "
						."    AND mtrcur != 'on' "
						."    AND mtrcon  = concod "
						."    AND conalt != 'on' "
						."    AND conmue != 'on' "
						."    AND concom  = 'on' "
						."    AND mtrmed  = meduma "
						."    AND INSTR(conser,'".$wservicio."') > 0 "    //Que la conducta que tiene sea del servicio por el cual ingreso a la HCE
						."    AND (conesp = '".$wesp."'"
						."     OR  conesp in ('','NO APLICA'))"           //Feb 19 2014 Juan C. Hdez
						."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16 "
						."  ORDER BY 7, 12 ";  
				}
	  }		
  }


  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);
		 
  echo "<br><br>";		 
  
  echo "<table>";
  echo "<tr class='tituloPagina'>";
  echo "<td align=center bgcolor=C3D9FF colspan=10>PACIENTES ATENDIDOS Y ACTIVOS</td>";
  echo "</tr>";
  echo "<tr class=encabezadoTabla>";
  echo "<th>Semaforo</th>";
  echo "<th>Fecha de Ingreso</th>";
  echo "<th>Hora de Ingreso</th>";
  echo "<th>Historia</th>";
  echo "<th>Documento <br> de Identidad</th>";
  echo "<th>Paciente</th>";
  echo "<th>Ir a Historia</th>";
  echo "<th>Conducta a Seguir</th>";
  echo "<th>Afinidad</th>";
  echo "<th>Medico Tratante</th>";
  echo "</tr>";
  
  if ($num > 0)
  {
	 for($i=$k;($i<($num+$k));$i++)
	 {
		  $row = mysql_fetch_array($res);  	  
		  
		  if (is_integer($i/2))
		 	  $wclass="fila1";
		  else
			  $wclass="fila2";
		  
		  $whis = $row[0];
		  $wing = $row[1];
		  $wpac = $row[2]." ".$row[3]." ".$row[4]." ".$row[5];
		  $wfin = $row[6];     //Fecha de Ingreso
		  $wtid = $row[7];		  
		  $wdpa = $row[8];     
		  $wcur = $row[9];     //Indicador de si esta en Consulta
		  $wcon = $row[10];    //Conducta
		  $whin = $row[11];    //Hora de Ingreso
		  $wmed = $row[12]." ".$row[13]." ".$row[14]." ".$row[15];    //Medico
		  
		  
		  $wcolor=convenciones($wfin, $whin);
		  
		  echo "<tr class=".$wclass.">";
		  echo "<td bgcolor='".$wcolor."'>&nbsp</td>";
		  echo "<td align=center>   ".$wfin."</td>";
		  echo "<td align=center>   ".$whin."</td>";
		  echo "<td align=center><b>".$whis." - ".$wing."</b></td>";
		  echo "<td align=center><b>".$wtid." - ".$wdpa."</b></td>";
		  echo "<td align=left  ><b>".$wpac."</b></td>";
		  
		  $irhce="on";  //Permite ingresar a la hce sin dar clic sobro el radio button de ir a hce
		  if ($wcur == "on")																					
			 echo "<td align=center><input type='radio' name='wirhce$i' id='wirhce$i' onclick='activarConsulta($whis, $wing, \"$wservicio\", \"$wdpa\", \"$wtid\", $i)' checked></td>";
			else 
			   echo "<td align=center><input type='radio' name='wirhce$i' id='wirhce$i' onclick='activarConsulta($whis, $wing, \"$wservicio\",\"$wdpa\", \"$wtid\", $i, \"$irhce\")'>";
		  
		  echo "<td align=center><select id='conducta$i' name='wconducta$i' onchange='colocarConducta($whis, $wing, $i, \"$irhce\")'>";

		  if (isset($wcon))                              //Si selecciono una opcion del dropdown
		  {
			  $q = " SELECT condes "
				 . "   FROM ".$whce."_000035 "
				 . "  WHERE concod = '".$wcon."'";
			  $res2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $row2 = mysql_fetch_array($res2);  	  
			 
			  echo "<option selected value=$wcon>".$row2[0]."</option>";
		  }
		 
		  echo "<option value=''>&nbsp</option>";
		 
		  //============================================================================================================
		  //Aca coloco todas las conductas
		  //============================================================================================================
		  $q = " SELECT concod, condes "
			 . "   FROM ".$whce."_000035 "
			 . "  WHERE conest = 'on' "
			 ."     AND INSTR(conser,'".$wservicio."') > 0 ";
		  $res1 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num1 = mysql_num_rows($res1);
		  for ($j=1;$j<=$num1;$j++)
		  {
			  $row1 = mysql_fetch_array($res1);
			  echo "<option value=$row1[0]>".$row1[1]."</option>";
		  }
		  echo "</select></td>";
		  //============================================================================================================
		  
		  //======================================================================================================
		  //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
		  $wafin=clienteMagenta($wdpa,$wtid,$wtpa,$wcolorpac);
		  if ($wafin)
		 	  echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
		  else
			  echo "<td>&nbsp</td>";
		  //======================================================================================================     
		  echo "<td align=center>".$wmed."</td>";
		  echo "</tr>";
		 }	     
	}
	echo "</table>"; 
}   
?>
<html>
<head>
  <title>SALA DE ESPERA</title>
</head>
<meta charset="UTF-8">
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<script type="text/javascript">
  
    function validarConsulta(i)
	{

	    var cont1 = 1;
		
		//debugger;
		
		while(document.getElementById("wirhce"+cont1.toString()))
		{
		    if ((document.getElementById("wirhce"+cont1.toString()).checked) && (cont1 != i))
			{
			    document.getElementById("wirhce"+i.toString()).checked=false;
				alert ("No es posible tener dos consultas al mismo tiempo");
				return false;
			}
			cont1++;
		}
        return true; 		  
	}
	   
	   
	function validarConducta(i, irhce)
	{

	    var cont1 = 1;
		while(document.getElementById("wirhce"+cont1.toString()))
		{
		    if (irhce != 'on')
		    {
				if ((document.getElementById("wirhce"+cont1.toString()).checked==false) && (cont1 == i) && (document.getElementById("conducta"+i.toString()).value)!='' && (document.getElementById("conducta"+i.toString()).value)!=' ')
			    {
					document.getElementById("conducta"+i.toString()).value='';
					alert ("Debe ingresar a la HCE antes de tomar una conducta");
					return false;
			    }
			}
			cont1++;
		}
		return true; 		  
	}   
	   
	   
    function activarConsulta(his, ing, ser, doc, tid, i, irhce) 
	{
	    
			wok=validarConsulta(i); 
		  
			if (wok==true)
			   {
				var parametros = "consultaAjax=activarcur&wemp_pmla="+document.forms.sala.wemp_pmla.value+"&whce="+document.forms.sala.whce.value+"&whis="+his+"&wing="+ing+"&wservicio="+ser+"&wusuario="+document.forms.sala.wusuario.value+"&irhce="+irhce+"&wesp="+document.getElementById("wesp").value+"&wamb="+document.getElementById("wamb").value;
				
			try{

				var ajax = nuevoAjax();
				
				ajax.open("POST", "Sala_de_espera_Ambulatoria.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);
				 
				}catch(e){    }

				//LLamado a la historia HCE
				
				url="HCE_iFrames.php?empresa="+document.forms.sala.whce.value+"&whis="+his+"&wing="+ing+"&wservicio="+ser+"&accion=F&ok=0&wcedula="+doc+"&wtipodoc="+tid+"&wdbmhos="+document.forms.sala.wbasedato.value+"&origen="+document.forms.sala.wemp_pmla.value;

				window.open(url,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
				}
				if (irhce=="on")
		        { 
		            document.getElementById('wirhce'+i).checked=false; 
			    }
	  }
	
	function colocarConducta(his, ing, i, irhce) 
	{

	    wok=validarConducta(i, irhce); 
	  
	    if (wok==true)
		{ 
		    var parametros = "consultaAjax=conducta&wemp_pmla="+document.forms.sala.wemp_pmla.value+"&whce="+document.forms.sala.whce.value+"&whis="+his+"&wing="+ing+"&wusuario="+document.forms.sala.wusuario.value+"&wconducta="+document.getElementById("conducta"+i).value+"&wesp="+document.getElementById("wesp").value+"&wamb="+document.getElementById("wamb").value;
			try
			  {
				var ajax = nuevoAjax();
			
				ajax.open("POST", "Sala_de_espera_Ambulatoria.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);
				if (ajax.readyState==4 && ajax.status==200)
					{
					 //if(ajax.responseText!="ok")
					 //	alert(ajax.responseText);
					} 
			  }catch(e){    }
			  
			//document.getElementById('wirhce'+i).checked=false;
			
			//enter();
		}	
	 }

	//-----------------------------------------------------------------------
	// --> Funcion que genera el llamado del paciente para que sea atendido
	//-----------------------------------------------------------------------
	function llamarPacienteAtencion(historia, ingreso, turno)
	{

	        if ($("#puestoTrabajo").val()=='')
	        {
	        	alert('Debe seleccionar el consultorio');
                return 0;
            }

			$.post("Sala_de_espera_Ambulatoria.php",
			{
				consultaAjax:   		'llamarPacienteAtencion',
				wemp_pmla:        		$('#wemp_pmla').val(),
				turno:					turno,
				historia:				historia,
				ingreso:				ingreso,
				puestoTrabajo:			$("#puestoTrabajo").val()
			}, function(respuesta){
                
				if(respuesta.Error)
				{
					jAlert("<span style='color:red'>"+respuesta.Mensaje+"</span>", "Mensaje");
					$(".botonLlamarPaciente").show();
				    $(".botonColgarPaciente").hide();
				}
				else
				{
					$(".botonLlamarPaciente").hide();
					$(".botonColgarPaciente").hide();
					$(".botonCancelarTurno").hide();
					$("#imgLlamar"+turno).hide();
					$("#botonColgar"+turno).show();
					$("#botonLlamando"+turno).show();
				}
			}, 'json');
	}

	//-------------------------------------------------------------
	// --> Actualiza el usuario asociado a un puesto de trabajo
	//-------------------------------------------------------------
	function cambiarPuestoTrabajo(respetarOcupacion)
	{

		$.post("Sala_de_espera_Ambulatoria.php",
		{
			consultaAjax:   		'cambiarPuestoTrabajo',
			wemp_pmla:        		$('#wemp_pmla').val(),
			puestoTrabajo:			$("#puestoTrabajo").val()
		}, function(respuesta){

			if(respuesta.Error)
			{
				jConfirm("<span style='color:#2A5DB0'>"+respuesta.Mensaje+"\nDesea liberarlo?</span>", 'Confirmar', function(respuesta) {
					if (respuesta)
						cambiarPuestoTrabajo(false);
	
				});
			}

		},"json");
	}

	//-----------------------------------------------------------------------
	// --> Funcion que cancela el llamado del paciente a la consulta
	//-----------------------------------------------------------------------
	function cancelarLlamarPacienteAtencion(turno)
	{

		$.post("Sala_de_espera_Ambulatoria.php",
		{
			consultaAjax:   		'cancelarLlamarPacienteAtencion',
			wemp_pmla:        		$('#wemp_pmla').val(),
			puestoTrabajo:			$("#puestoTrabajo").val(),
			turno:					turno
		}, function(respuesta){

			$(".botonLlamarPaciente").show();
			$(".botonColgarPaciente").hide();
			$("#trTurno_"+turno).attr("class", $("#trTurno_"+turno).attr("classAnterior"));
		});
	}

	
	function cerrarVentana()
	{
       window.close()		  
    }
	 
	function enter()
	{
	   document.forms.sala.submit();
	}
	
</script>
<style>
	body{  width: 97%;  }
</style>  
</head>
<body>
<?php
	                                                           
 /*********************************************************
*               SALA DE ESPERA URGENCIAS                *
*     				CONEX, FREE => OK				   *
*********************************************************/
//==================================================================================================================================
//PROGRAMA                   : Sala_de_espera_Ambulatoria.php
//AUTOR                      : Juan Carlos Hernández M.
//$wautor="Juan C. Hernandez M. ";
//FECHA CREACION             : Febrero 15 de 2011
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="2020-03-10"; 
//DESCRIPCION
//==========================================================================================================================================\\
//==========================================================================================================================================\\
//     Programa usado para la atencion de los pacientes en Unidades Ambulatarias.                                                           \\
//     ** Funcionamiento General:                                                                                                           \\
//     En esta pantalla se muestran todos los pacientes que se hallan ingresado a Matrix por el programa de asignacion de medico que        \\
//     tienen los facturadores o auxiliares de admision.                                                                     \\
//     Asi: El facturador debe abrir el programa de asignacion de medico o 'agenda_ambulatorias.php' en el cual se le asigna el medico al   \\
//     paciente antes de ingresar al consultorio, por lo cual el médico solo podrá ver los pacientes que le asignaron a él en esta programa \\
//     y los que se encuentren en observacion o procedimientos y que sea factible que él lo pueda ver.                                      \\
//     Desde este programa también prodrá acceder a la HCE y luego de esto debe asignar una conducta a seguir con el paciente.              \\
//         * Tablas: hce_000022      : Medico tratante, consulta Urgencias, conducta a seguir                                               \\
//                   hce_000035      : Maestro de conductas indica si se da de alta o se indica muerte                                      \\
//                   movhos_000018   : Ubicación del paciente y se indica el alta definitiva.                                               \\
//==========================================================================================================================================\\
//==========================================================================================================================================\\
//2020-03-10 Arleyda Insignares C.
//Se adiciona filtro para la admisión y tenga en cuenta además del médico registrado en la admisión, el grupo de centros de costos de Arkadia
//2020-02-27 Arleyda Insignares C.
//Se adiciona mensaje en la columna de turno para que muestre cuando el paciente llego pero que está pendiente de admisión
//se modifica consulta en cliame_000100 adicionando el campo Pacact en el filtro del query
//Se retira el filtro por centro de costos cuando el paciente tiene admisión pero no tiene cita asignada, con el objetivo de filtrar solo por el médico y no tengan que cambiar el centro de costos en la admisión.
//2020-02-03 Arleyda Insignares C.
//Se adiciona busqueda en la tabla movhos_000011 para consultar según el prefijo, las citas que tiene el médico
//Se adiciona llamado del paciente a monitor para todo paciente que tenga admisión
//Se integra a la consulta ambulatoria el ingreso por cliame_000101
//
//Marzo 12 de 2014 Jonatan Lopez
//Se cambia Sala_de_espera_Ambulatoria_borrar.php por Sala_de_espera_Ambulatoria.php.
//==========================================================================================================================================\\
//Febrero 20 de 2014                                                                                                                     	\\
//==========================================================================================================================================\\
//Se modifica el query de los pacientes comunes para que solo traigan las conductas que corresponden con el servicio al que pertenece el    \\    				\\
//el usuario Matrix, según configuración en la tabla de conductas _000035 - Juan C. Hernández				
//==========================================================================================================================================\\
//Febrero 19 de 2014                                                                                                                     	\\
//==========================================================================================================================================\\
//Se modifica el query para cuando un medico no es de urgencias vea en los pacientes comunes solo los de su especialidad    				\\
//Juan C. Hernández				
//==========================================================================================================================================\\
//Junio 25 de 2013                                                                                                                     		\\
//==========================================================================================================================================\\
//Se agregó el parámetro wesp que permite determinar si el programa consultará los pacientes por especialidad o por médico. 				\\
//En la función mostrarPacientesPropios se condicionó la consulta para que si wesp=1 consulte los pacientes de la especialidad y no solo    \\
//los del médico tratante																													\\
//En la función ponerConsulta se adicionó la condición por especialidad para que actualice el médico tratante si el programa está 			\\
//consultando los pacientes por especialidad. - Mario Cadavid																				\\
//==========================================================================================================================================\\
//Octubre 17 de 2012                                                                                                                     	\\
//==========================================================================================================================================\\
//Se agrega un filtro en la funcion mostrarPacientesComunes para que a los medicos de ortopedia solo se les muestre sus pacientes en la     \\
//lista de pacientes atendidos y activos, para las otras especialidades si les mostrara los pacientes de todos los medicos de su especialidad.\\
//==========================================================================================================================================\\
//Septiembre 22 de 2011                                                                                                                     \\
//==========================================================================================================================================\\
//Se adiciona un query para que traiga los pacientes de cada unidad, determinados por la variable $wcco la cual viene desde la opción de    \\
//menu de Matrix, en la cual se coloca como parametro el centro de costo y el codigo de servicio de la unidad en la variable $wservicio,    \\
//estos codigo deben de corresponder con la codificación que se tiene para estos en el unix, tanta para cco como para el servicio.          \\
//==========================================================================================================================================\\
//Mayo 20 de 2011                                                                                                                           \\
//==========================================================================================================================================\\
//Se crean 3 campos en la tabla hce_000035 que indican si la conducta es de Urgencias, Pediatria u Ortopedia esto para que un medico general\\
//pueda trasladar un paciente a las especialidades de Pediatria u Ortopedia, cuando esto se hace los medicos generales podran seguir viendo \\
//el paciente en la parte de abajo de este programa, pero los pediatras u ortopedistas solo podran ver los pacientes que tengan asignado un \\
//medico de su especialidad o que tengan una conducta correspodiente a su especialidad.                                                     \\
//Los médicos generales no pueden ver los pacientes que tengan asignado un medico pediatra u ortopedista, pero si tienen una conducta de    \\
//estas especialidades si.                                                                                                                  \\
//==========================================================================================================================================\\
//Mayo 13 de 2011                                                                                                                           \\
//==========================================================================================================================================\\
//Se controla que al momento de colocar una conducta que implique un Alta se coloque en proceso de alta en la tabla movhos_000018,  y a la  \\
//vez si la conducta no implica un Alta coloque el indicador ubialp='off'                                                                   \\
//==========================================================================================================================================\\
//Enero 24 de 2011                                                                                                                          \\
//==========================================================================================================================================\\
//Se adiciona el campo de alertas que se registra en el Kardex                                                                 
               
//=======================================================================================================================================
  encabezado("SALA DE ESPERA AMBULATORIA",$wactualiz, "clinica");  
  
  //FORMA ================================================================
  echo "<form name='sala' action='' method=post>";  
  echo "<input type='HIDDEN' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
  echo "<input type='HIDDEN' id='wcliame'   name='wcliame'   value='".$wcliame."'>";
  echo "<input type='HIDDEN' id='whce'      name='whce'      value='".$whce."'>";
  echo "<input type='HIDDEN' id='wusuario'  name='wusuario'  value='".$wusuario."'>";
  echo "<input type='HIDDEN' id='wcco'      name='wcco'      value='".$wcco."'>";
  echo "<input type='HIDDEN' id='wservicio' name='wservicio' value='".$wservicio."'>";
  echo "<input type='hidden' id='turnoLlamadoPorEsteUsuario' name='turnoLlamadoPorEsteUsuario'>";
  
  //Capturo lista centros de costos
  if ( isset($wamb) &&  $wamb == '1' )
  {
       ////////////////////////////////////////////////////
       //buscar centro de costos y seleccionar la unidad //
       ////////////////////////////////////////////////////
       $sqlCentrocos =  " SELECT Ccocun,Ccocod
                          FROM ".$wbasedato."_000011                        
                          WHERE Ccocod = '".$wcco."' ";

       $res = mysql_query($sqlCentrocos,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$sqlCentrocos." - ".mysql_error());
	   $row = mysql_fetch_array($res); 
       
       ////////////////////////////////////
       //Buscar listado centro de costos //
       ////////////////////////////////////
       $sqlListacen =  " SELECT GROUP_CONCAT(DISTINCT ccocod
					ORDER BY Ccocod ASC
					SEPARATOR ',')
                          FROM ".$wbasedato."_000011                        
                          WHERE Ccocun = '".$row['Ccocun']."' ";

       $res = mysql_query($sqlListacen,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$sqlListacen." - ".mysql_error());
	   $row = mysql_fetch_array($res); 
       $ccoLista = $row[0];  

  }
  else
  	  $ccoLista = '';
    
  
  //=====================================================================================================================================
  //Imprimo el nombre del Médico
  //=====================================================================================================================================
  $q = " SELECT medno1, medno2, medap1, medap2 "
       ."  FROM ".$wbasedato."_000048 "
	   ." WHERE meduma = '".$wusuario."'";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 

  echo "<p class='tituloPagina' align=center>Dr(a). ".$row[0]." ".$row[1]." ".$row[2]." ".$row[3]."</p>";
  echo "<br>";

  // --> Obtener el maestro de puestos de trabajo (Consultorios) 
  $sqlVentanillas	= "
					SELECT Puecod, Puenom, Pueusu
					  FROM ".$wcliame."_000301 C1
					  INNER JOIN ".$wcliame."_000302 C2
					    ON C1.Puecod = C2.Rsppue
					 WHERE Puecon = 'on'
					   AND Pueest = 'on'
					";
  $resVentanillas 	  =  mysql_query($sqlVentanillas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVentanillas):</b><br>".mysql_error());
  $consultorioActUsu  =  '';

  while($rowVentanillas = mysql_fetch_array($resVentanillas))
  {
		$arrayConsultorios[$rowVentanillas['Puecod']] = $rowVentanillas['Puenom'];

		if( $rowVentanillas['Pueusu'] == $wusuario )
			$consultorioActUsu = $rowVentanillas['Puecod'];
  }

   echo "<table width='100%'>
		<tr>
			<td align='left'>
				<div align='center'>
  					<span align='center' style='padding:5px;border-radius: 4px;border:2px solid #AFAFAF;width:200px;font-family: verdana;font-weight:bold;font-size: 15pt;'>
						Consultorio:&nbsp;&nbsp;</b>
						<select id='puestoTrabajo' type='text' style='cursor:pointer;border-radius: 4px;border:1px solid #AFAFAF;width:200px' consultorioActUsu='".$consultorioActUsu."' onChange='cambiarPuestoTrabajo(this)'>
							<option value='' usuario=''>Seleccione..</option>
						";
					foreach($arrayConsultorios as $codConsultorio => $nomConsultorio)
						echo "
							<option value='".$codConsultorio."' ".(($codConsultorio == $consultorioActUsu) ? "SELECTED='SELECTED'" : "" ).">".$nomConsultorio."</option>";
					echo "
						</select>
					</span>
				</div>
			</td>
		</tr>
		</table>";

  //===============================================================================================================================================
  //C O N  V E N C I O N E S
  //===============================================================================================================================================
  echo "<HR align=center></hr>";  //Linea horizontal
  echo "<table border=1 align=right>";
  echo "<caption bgcolor=#ffcc66>Convenciones</caption>";
  echo "<tr><td colspan=3 bgcolor="."99FFCC"."><font size=1 color='"."000000"."'>&nbsp Menos de 20 minutos</font></td></tr>";           //Verde  
  echo "<tr><td colspan=3 bgcolor="."FFFF66"."><font size=1 color='"."000000"."'>&nbsp De 20 a 35 minutos</font></td></tr>";            //Amarillo
  echo "<tr><td colspan=3 bgcolor="."FFCC99 "."><font size=1 color='"."000000"."'>&nbsp Mas de 35 minutos</font></td></tr>";      //Rojo
  echo "</table>";	
  //=====================================================================================================================================
  
  echo "<center><table>";
  
  $q = " SELECT empdes, empmsa "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  $wmeta_sist_altas=$row[1];  //Esta es la meta en tiempo promedio para las altas   
     
     
  //=====================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //=====================================================================================================================================
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

   
  if(isset($consultaAjax))
  {
	switch($consultaAjax)
	{
	    case 'activarcur':
			 echo ponerConsulta($whce, $whis, $wing, $wusuario, $irhce, $wesp, $wamb);
			 break;
		case 'conducta':
		     echo ponerConducta($whce, $whis, $wing, $wconducta);
		     break;
    }
  }
  
  $wesp = '0';
  if(isset($_GET['wesp']) && $_GET['wesp']!='')
	$wesp = $_GET['wesp'];

  if(isset($_GET['wamb']) && $_GET['wamb']!='')
	$wamb = $_GET['wamb'];

  mostrarPacientesPropios($wbasedato, $wcliame, $whce, $wemp_pmla, $wcco, $wusuario, $i, $wservicio, $wesp, $wamb,$ccoLista);
  mostrarPacientesComunes($wbasedato, $wcliame, $whce, $wemp_pmla, $wcco, $wusuario, $i, $wservicio);

  echo "<input type='hidden' name='wesp' id='wesp' value='".$wesp."'>";
  echo "<input type='hidden' name='wamb' id='wamb' value='".$wamb."'>";

  echo "</form>";
  
  if (isset($wsup) and $wsup=="on")  //Es superusuario
     echo "<meta http-equiv='refresh' content='300;url=Sala_de_espera_Ambulatoria.php?wemp_pmla=".$wemp_pmla."&wuser=".$wusuario."&user=".$user."&wcco=".$wcco."&wservicio=".$wservicio."&wesp=".$wesp."&wamb=".$wamb."'>";
  else 
     echo "<meta http-equiv='refresh' content='30;url=Sala_de_espera_Ambulatoria.php?wemp_pmla=".$wemp_pmla."&wuser=".$wusuario."&user=".$user."&wcco=".$wcco."&wservicio=".$wservicio."&wesp=".$wesp."&wamb=".$wamb."'>";
	  
  echo "<table>"; 
  echo "<tr class=boton><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";
}
include_once("free.php");
?>
