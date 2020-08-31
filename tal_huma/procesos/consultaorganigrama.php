<?php
include_once("conex.php");

/*********************************************************
 *               VISUALIZACION DE RELACIONES             *
 *                                                       *
 *********************************************************/
//==================================================================================================================================
//PROGRAMA                   : consultaorganigrama.php
//AUTOR                      : Jessica Madrid Mejía
//
//FECHA CREACION             : Junio 17 de 2016
//FECHA ULTIMA ACTUALIZACION : Julio 22 de 2016
 
// DOCUMENTACION ORGANIGRAMA :	http://www.basicprimitives.com/index.php?option=com_local&view=local&Itemid=43&lang=en	
 
//DESCRIPCION
//========================================================================================================================================\\
// Programa para visualizar y modificar facilmente las relaciones de empleados con su respectivo jefe (talhuma_000008) 			          \\        
//========================================================================================================================================\\
// Modificaciones
// 
// 2017-09-04    - Arleyda Insignares Ceballos
// 				   Se adiciona al proceso de inactivación de empleados, el estado 'off' para las evaluaciones pendientes por realizar.
// 2016-07-22    - Arleyda Insignares Ceballos
//                 Se agrega una barra con tres botones, la cual se activa cuando se selecciona un empleado, los tres procesos son: 
//                 Inactivar un empleado, Cambiar el Centros de Costos y Mostrar las Evaluaciones programadas.
//                                                                                                                                            
//========================================================================================================================================\\

function modalesDialog()
{
	echo "	<div id='confirmRestaurar' style='display:none;font-family: verdana;font-weight:bold;font-size: 10pt;'>
				Desea volver a cargar el organigrama? se perder&aacute;n los cambios realizados
			</div>";
			
	echo "	<div id='confirmGuardar' style='display:none;font-family: verdana;font-weight:bold;font-size: 10pt;'>
				Desea guardar los cambios realizados?
			</div>";
			
	echo "	<div id='confirmSinPadre' style='display:none;font-family: verdana;font-weight:bold;font-size: 10pt;'>
				
			</div>";

	echo "	<div id='confirmInactivar' style='display:none;font-family: verdana;font-weight:bold;font-size: 10pt;'>
				<br>
				<center>Desea Inactivar el empleado?</center>
			</div>";		
			
	echo "	<div id='alertMsj' title='Alerta' style='display:none;font-family: verdana;font-weight:bold;font-size: 10pt;'>
				
			</div>";		
	
}


function ConsultarCcoAutocomplete($conex, $wemp_pmla,$wbasedato)
{
	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

	
	$arr_cco = array();
	
	$q_cco = " SELECT Ccocod AS codigo, Cconom AS nombre 
				 FROM costosyp_000005
				WHERE Ccoest = 'on' 	
				ORDER BY nombre ";
			
	$r_cco = mysql_query($q_cco,$conex) or die("Error en el query: ".$q_cco."<br>Tipo Error:".mysql_error());

	while($row_cco = mysql_fetch_array($r_cco))
	{
		$row_cco['nombre'] = str_replace($caracter_ma, $caracter_ok, $row_cco['nombre']);
		$arr_cco[trim($row_cco['codigo'])] = trim($row_cco['nombre']);
	}
	
	return $arr_cco;
}

function ConsultarEmpleadosAutocomplete($conex, $wemp_pmla,$wbasedato,$wcco)
{
	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

	
	if($wcco!="")
	{
		$qEmpCco = "  SELECT Ideuse,Ideno1,Ideno2,Ideap1,Ideap2 
						FROM ".$wbasedato."_000013 
					   WHERE Idecco = '".$wcco."' 
						 AND Ideest='on'
					GROUP BY Ideuse;";
	}
	else
	{
		$qEmpCco = "  SELECT Ideuse,Ideno1,Ideno2,Ideap1,Ideap2 
						FROM ".$wbasedato."_000013 
					   WHERE Ideest='on'
					GROUP BY Ideuse;";
	}
		
	$resEmpCco=  mysql_query($qEmpCco,$conex) or die ("Error talhuma_000013: ".mysql_errno()." - en el query: ".$qEmpCco." - ".mysql_error());
	$numEmpCco = mysql_num_rows($resEmpCco);
	
	$arrayNombres = array();
	
	
	if($numEmpCco > 0)
	{
		while($rowEmpCco = mysql_fetch_array($resEmpCco))
		{
			$nombreCompleto = "";
			if($rowEmpCco['Ideno1'] != "NO APLICA" && $rowEmpCco['Ideno1'] != "")
			{
				$nombreCompleto .= $rowEmpCco['Ideno1']." ";
			}
			if($rowEmpCco['Ideno2'] != "NO APLICA" && $rowEmpCco['Ideno2'] != "")
			{
				$nombreCompleto .= $rowEmpCco['Ideno2']." ";;
			}
			if($rowEmpCco['Ideap1'] != "NO APLICA" && $rowEmpCco['Ideap1'] != "")
			{
				$nombreCompleto .= $rowEmpCco['Ideap1']." ";
			}
			if($rowEmpCco['Ideap2'] != "NO APLICA" && $rowEmpCco['Ideap2'] != "")
			{
				$nombreCompleto .= $rowEmpCco['Ideap2']." ";
			}
			
			$arrayNombres[$rowEmpCco['Ideuse']] = str_replace($caracter_ma, $caracter_ok, $nombreCompleto);;
		}
	}
	
	return $arrayNombres;
}

function consultarCCO($conex, $wemp_pmla,$wbasedato,$arrayCambios)
{
	$arrayCco = array();
	$cadenaIdeuse="";
	foreach ($arrayCambios as $keyHijo => $valuePadre)
	{
		$cadenaIdeuse .= "'".$keyHijo."','".$valuePadre."',";
	}
	
	if($cadenaIdeuse!="")
	{
		$cadenaIdeuse=substr($cadenaIdeuse, 0, -1);
		$qEmpCco = "  SELECT Ideuse,Idecco 
						FROM ".$wbasedato."_000013 
					   WHERE Ideuse IN (".$cadenaIdeuse.") 
						 AND Ideest='on' 
					GROUP BY Ideuse;";
		
		$resEmpCco=  mysql_query($qEmpCco,$conex) or die ("Error talhuma_000013: ".mysql_errno()." - en el query: ".$qEmpCco." - ".mysql_error());
		$numEmpCco = mysql_num_rows($resEmpCco);
		
		if($numEmpCco > 0)
		{
			while($rowEmpCco = mysql_fetch_array($resEmpCco))
			{
				$arrayCco[$rowEmpCco['Ideuse']] = $rowEmpCco['Idecco'];
			}
		}
	}
	
	return $arrayCco;
}

function guardarNuevasRelaciones($conex, $wemp_pmla,$wbasedato,$wcco,$arrayCambios,$arrayNombres,$arrayCco,$aceptaSinPadre)
{
	$data = array('error'=>0,'mensErrorIns'=>"",'mensErrorAct'=>"");
	
	$data['mensErrorAct'] = "Error al actualizar las relaciones de los siguientes empleados: ";
	$data['mensErrorIns'] = "Error al insertar las relaciones de los siguientes empleados: ";
	$data['mensErrorEli'] = "Error al eliminar las relaciones de los siguientes empleados: ";
	
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");
	
	$ccoActuales = consultarCCO($conex, $wemp_pmla,$wbasedato,$arrayCambios);
	
	foreach ($arrayCambios as $keyHijo => $valuePadre)
	{
		if(($valuePadre == "" && $aceptaSinPadre=="true") || $valuePadre != "")
		{
			$qConsultarRelacion = "SELECT * 
									 FROM ".$wbasedato."_000008 
									WHERE Ajeuco='".$keyHijo."' 
									  AND Forest='on';";
			
			$resConsultarRelacion=  mysql_query($qConsultarRelacion,$conex) or die ("Error al consultar relacion talhuma_000008: ".mysql_errno()." - en el query: ".$qConsultarRelacion." - ".mysql_error());
			$numConsultarRelacion = mysql_num_rows($resConsultarRelacion);
			
			if($numConsultarRelacion > 0)
			{
				if($valuePadre != "" )
				{

					
					//Actualizar las evaluaciones pendientes
					$qActualizarEval = "  UPDATE ".$wbasedato."_000058 A
					                          LEFT JOIN ".$wbasedato."_000032 B
					                              ON  A.Arecdr = B.Mcaucr 
					                              AND A.Arecdo = B.Mcauco
					                              AND A.Arefor = B.Mcafor
					                              AND A.Areper = B.Mcaper
					                              AND A.Areano = B.Mcaano 
											  SET A.Arecdr='".$valuePadre."'
											WHERE isnull(B.Mcaucr)
											  AND A.Arecdo = '".$keyHijo."' 
											  AND Areest='on'";
					
					$resActualizarEval=  mysql_query($qActualizarEval,$conex);


					//Si ya exite la relacion la modifica con el nuevo padre
					$qActualizarPadre = "  UPDATE ".$wbasedato."_000008 
											  SET Ajeucr='".$valuePadre."',
												  Ajeccr='".$ccoActuales[$valuePadre]."',
												  Ajecco='".$ccoActuales[$keyHijo]."'
											WHERE Ajeuco='".$keyHijo."' 
											AND Forest='on'";
					
					$resActualizarPadre=  mysql_query($qActualizarPadre,$conex);

					
					if(mysql_affected_rows()>0)
					{
						$data['error'] = 0;
					}
					else
					{
						if(mysql_error() != "")
						{
							$data['error'] = 1;
							$data['mensErrorAct'] .= $arrayNombres[$keyHijo] ."," ;
						}
						else
						{
							$data['error'] = 0;
						}
						
					}
				}
				else
				{
					//Si no tiene padre y ya exite la relacion elimina el registro
					$qEliminarHijo = "  DELETE FROM ".$wbasedato."_000008 
											WHERE Ajeuco='".$keyHijo."' 
											AND Forest='on';";
					
					$resEliminarHijo=  mysql_query($qEliminarHijo,$conex);
					
					if(mysql_affected_rows()>0)
					{
						$data['error'] = 0;
					}
					else
					{
						if(mysql_error() != "")
						{
							$data['error'] = 1;
							$data['mensErrorEli'] .= $arrayNombres[$keyHijo]  ."," ;
						}
						else
						{
							$data['error'] = 0;
						}
					}
				}
			}
			else
			{


				//Actualizar las evaluaciones pendientes
				$qActualizarEval = "  UPDATE ".$wbasedato."_000058 A
					                          LEFT JOIN ".$wbasedato."_000032 B
					                              ON  A.Arecdr = B.Mcaucr 
					                              AND A.Arecdo = B.Mcauco
					                              AND A.Arefor = B.Mcafor
					                              AND A.Areper = B.Mcaper
					                              AND A.Areano = B.Mcaano 
											  SET A.Arecdr='".$valuePadre."'
											WHERE isnull(B.Mcaucr)
											  AND A.Arecdo = '".$keyHijo."' 
											  AND Areest='on'";
					
				$resActualizarEval=  mysql_query($qActualizarEval,$conex);



				//Si no exite la relacion inserta una nueva
				$qInsertarRelacion = "INSERT INTO ".$wbasedato."_000008 (Medico,Fecha_data,Hora_data,Ajeucr,Ajeccr,Ajeuco,Ajenco,Ajecco,Ajefor,Forest,Ajecoo,Ajeccc,Seguridad)
																 VALUES ('".$wbasedato."','".$fecha."','".$hora."','".$valuePadre."','".$ccoActuales[$valuePadre]."','".$keyHijo."','','".$ccoActuales[$keyHijo]."','','on','','','C-".$wbasedato."');";
																 
				$resInsertarRelacion=  mysql_query($qInsertarRelacion,$conex);



				
				if(mysql_affected_rows()>0)
				{
					$data['error'] = 0;
				}
				else
				{
					if(mysql_error() != "")
					{
						$data['error'] = 1;
						$data['mensErrorIns'] .= $arrayNombres[$keyHijo]  ."," ;
					}
					else
					{
						$data['error'] = 0;
					}
				}
			}
		}
	}
	
	if($data['mensErrorAct'] == "Error al actualizar las relaciones de los siguientes empleados: ")
	{
		$data['mensErrorAct'] = "";	
	}
	else
	{
		$data['mensErrorAct'] = substr($data['mensErrorAct'], 0, -1);	
	}
	
	
	if($data['mensErrorIns'] == "Error al insertar las relaciones de los siguientes empleados: ")
	{
		$data['mensErrorIns'] = "";	
	}
	else
	{
		$data['mensErrorIns'] = substr($data['mensErrorIns'], 0, -1);	
	}
	
	
	if($data['mensErrorEli'] == "Error al eliminar las relaciones de los siguientes empleados: ")
	{
		$data['mensErrorEli'] = "";	
	}
	else
	{
		$data['mensErrorEli'] = substr($data['mensErrorEli'], 0, -1);	
	}
	
	return $data;	
}
	
function consultarFoto($conex,$wemp_pmla,$wbasedato,$wcedula,$genero)
{
    $extensiones_img = array(   '.jpg','.Jpg','.jPg','.jpG','.JPg','.JpG','.JPG','.jPG',
                                '.png','.Png','.pNg','.pnG','.PNg','.PnG','.PNG','.pNG');
    $wruta_fotos = "../../images/medical/tal_huma/";
	
	if($genero == "")
	{
		$genero = "M";
	}
	
    $wfoto = "silueta".$genero.".png";

    $wfoto_em = '';
    $ext_arch = '';

    foreach($extensiones_img as $key => $value)
	{
		$ext_arch = $wruta_fotos.trim($wcedula).$value;
		if (file_exists($ext_arch))
		{
			$wfoto_em = $ext_arch;
			break;
		}
	}
   

    if ($wfoto_em == '')
    {
        $wfoto_em = $wruta_fotos.$wfoto;
    }

    return $wfoto_em;
}



// *********  Colocar el estado del empleado 'off' en las tablas : usuarios, talhuma_000008 y talhuma_000013 ***
function InactivarEmpleado($conex, $wemp_pmla,$wbasedato,$wempleadoid)
{
	  $loncodusu   = strlen($wempleadoid);
	  $varusuario  = substr($wempleadoid,0,$loncodusu-3);
	  $varusuario2 = '01'.$varusuario;

	  // inactivar en talhuma_000013
	  $q=" UPDATE ".$wbasedato."_000013
				    SET Ideest = 'off'
				    WHERE Ideuse = '".$wempleadoid."' ";
	  $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


	  // inactivar en talhuma_000008
	  $q=" UPDATE ".$wbasedato."_000008
				    SET Forest = 'off'
				    WHERE Ajeuco = '".$wempleadoid."' ";
	  $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 


      // inactivar en usuarios	
	  $q    =" UPDATE usuarios SET Activo = 'I' WHERE Codigo = '".$varusuario."' ";
	  $resp2= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


	  //Inactivar evaluaciones pendientes
	  $qActualizarEval = "  UPDATE ".$wbasedato."_000058 A
		                          LEFT JOIN ".$wbasedato."_000032 B
		                              ON  A.Arecdr = B.Mcaucr 
		                              AND A.Arecdo = B.Mcauco
		                              AND A.Arefor = B.Mcafor
		                              AND A.Areper = B.Mcaper
		                              AND A.Areano = B.Mcaano 
								  SET A.Areest='off'
								WHERE isnull(B.Mcaucr)
								  AND A.Arecdo = '".$wempleadoid."' 
								  AND Areest='on'";
		
	  $resActualizarEval=  mysql_query($qActualizarEval,$conex);

      
	  // Verificar si lo actualizo
	  if ($resp2 = 0)
	  {
		 $q =" UPDATE usuarios SET Activo = 'I' WHERE Codigo = '".$varusuario2."' ";
	     $resp2= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  } 	

	  echo $resp;
	  return;
}



// *********      Consultar El Jefe segun el Centro de Costos digitado      ********
function ConsultarJefe($conex, $wemp_pmla,$wbasedato,$wcentrocos)
{
	$vjefe = '';

	$q = "SELECT A.Ajeucr, A.Ajeccr, concat(B.Ideno1,' ', B.Ideno2, ' ', B.Ideap1, ' ', B.Ideap2) as 'Nomemp'
          FROM ".$wbasedato."_000008 A
          Inner join ".$wbasedato."_000013 B
          on A.Ajeucr = B.Ideuse
          Where Ajeccr = '".$wcentrocos."' 
          Order by A.Fecha_data desc";

    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res);

    if ($num > 0)
	   {
	     $row   = mysql_fetch_assoc($res);
	     $vjefe = $row['Ajeucr'].'|'. $row['Nomemp'];
	   }

	echo $vjefe;
	return;   
}


function GrabarEmail($conex, $wemp_pmla,$wbasedato,$wemail,$wempleadoid)
{
  // Cambiar centro de costos en talhuma_000013
	  $q = " UPDATE ".$wbasedato."_000013
				    SET Ideeml = '".$wemail."'
				    WHERE Ideuse = '".$wempleadoid."' ";

	  $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  echo $resp;
	  return;
}

// *********      Cambiar el Centro de costos en la tabla talhuma_000013    ********
function CambiarCentro($conex, $wemp_pmla,$wbasedato,$wempleadoid,$wcentroid,$wjefeid)
{
	  // Cambiar centro de costos en talhuma_000013
	  $q = " UPDATE ".$wbasedato."_000013
				    SET Idecco = '".$wcentroid."'
				    WHERE Ideuse = '".$wempleadoid."' ";

	  $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  echo $resp;

      // Consultar si el empleado se encuentra en talhuma_000008
      $q   = "SELECT Ajeuco,Ajecco From ".$wbasedato."_000008 where Ajeuco='".$wempleadoid."' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);

	  if ($num > 0)
	  {
		    // Cambiar El centro de Costos y Jefe inmediato en talhuma_000008
		    $q = "  UPDATE ".$wbasedato."_000008
							  SET Ajeucr='".$wjefeid."',
							      Ajeccr='".$wcentroid."', 
								  Ajecco='".$wcentroid."'
							  WHERE Ajeuco='".$wempleadoid."' 
							  AND Forest='on'";			  
      }
      else
      {
			$q = "INSERT INTO ".$wbasedato."_000008 (Medico,Fecha_data,Hora_data,Ajeucr,Ajeccr,Ajeuco,Ajenco,Ajecco,Ajefor,Forest,Ajecoo,Ajeccc,Seguridad)
			VALUES ('".$wbasedato."','".$fecha."','".$hora."','".$wjefeid."','".$wcentroid."','".$wempleadoid."','','".$wcentroid."','','on','','','C-".$wbasedato."');";

      }  
       
	  $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  

	  //Actualizar las evaluaciones pendientes
	  $qActualizarEval = "  UPDATE ".$wbasedato."_000058 A
	                          LEFT JOIN ".$wbasedato."_000032 B
		                              ON  A.Arecdr = B.Mcaucr 
		                              AND A.Arecdo = B.Mcauco
		                              AND A.Arefor = B.Mcafor
		                              AND A.Areper = B.Mcaper
		                              AND A.Areano = B.Mcaano 
							  SET A.Arecdr='".$wjefeid."'
							WHERE isnull(B.Mcaucr)
							  AND A.Arecdo = '".$wempleadoid."' 
							  AND Areest='on'";
	
	  $resActualizarEval=  mysql_query($qActualizarEval,$conex);

	  return;

}

// *********      	Consultar Email empleado    	
function ConsultarEmail($conex,$wemp_pmla,$wbasedato,$wempleadoid)
{ 
    $q = " SELECT Ideuse, Ideeml 
           FROM ".$wbasedato."_000013
           WHERE Ideuse = '".$wempleadoid."' ";
  
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res);
    if ($num > 0)
       {
         $row   = mysql_fetch_assoc($res);
         $email = $row['Ideeml'];
       }
    
    echo $email;
	return;   
}


// *********      Consultar Centro de Costos Actual del Empleado         *******
function ConsultarCentroactual($conex, $wemp_pmla,$wbasedato,$wempleadoid,$wcentrocos)
{    
	$wcentros   = unserialize(base64_decode($wcentrocos));
	
    $q = " SELECT Ideuse, Idecco 
          FROM ".$wbasedato."_000013
          WHERE Ideuse = '".$wempleadoid."' ";

    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res);
    if ($num > 0)
       {
         $row = mysql_fetch_assoc($res);
         $NOM = (array_key_exists($row['Idecco'],$wcentros)) ? $wcentros[$row['Idecco']]:'Sin Centro de Costos';
       }
    echo $NOM;
	return;   
}


// *********       Consultar las Evaluaciones que ha tenido el empleado      *******
function MostrarEvaluaciones($conex, $wemp_pmla,$wbasedato,$wempleadoid,$wcentrocos)
{

	$wcentros   = unserialize(base64_decode($wcentrocos));
    $vresultado = '';

    $q = " SELECT A.Arecdr, A.Arecdo, A.Aretem, A.Arefor, A.Areper, Areano, 
              A.Areest, B.Idecco, D.Mcaano, D.Mcaper, C.Idefin, 
              concat(B.Ideno1,' ', B.Ideno2, ' ', B.Ideap1, ' ', B.Ideap2) as 'nomemp', 
              concat(C.Ideno1,' ', C.Ideno2, ' ', C.Ideap1, ' ', C.Ideap2) as 'nomjefe' 
           FROM ".$wbasedato."_000058 A 
           Inner Join ".$wbasedato."_000013 B on A.Arecdo = B.Ideuse 
           Inner Join ".$wbasedato."_000013 C on A.Arecdr = C.Ideuse 
           Left Join ".$wbasedato."_000032 D on A.Arecdr = D.Mcauco 
           AND A.Areano = D.Mcaano AND  A.Areper = D.Mcaper 
           WHERE  A.Arecdo = '".$wempleadoid."' ";

    $res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res);
    $cont1 = 0;
    
    if ($num > 0)
       {	 
			$rowtit = mysql_fetch_assoc($res);

            $vresultado  = "<tbody><p><tr border=1 style='font-size: 14px;'><td width='300px' colspan=6>Codigo Empleado &nbsp;&nbsp;".$rowtit['Arecdo']."&nbsp;&nbsp;- Nombre Empleado &nbsp;&nbsp;".$rowtit['nomemp']."&nbsp;&nbsp;- Fecha de Ingreso &nbsp;&nbsp;".$rowtit['Idefin']."</td></tr></p><br>";

       	    $vresultado .= "<tr><td></td></tr>
       	                   <tr class=encabezadotabla>
       	                   <td align='center' width='300px'>Codigo Jefe</td>
       	                   <td align='center' width='400px'>Nombre Jefe</td>
       	                   <td align='center' width='300px'>Centro de costos</td>
       	                   <td align='center' width='300px'>Descripcion C. de C.</td>
       	                   <td align='center' width='200px'>Fecha Programada</td>
       	                   <td align='center' width='200px'>Realizado</td></tr>";

			while($row = mysql_fetch_assoc($res)){	
			 	$cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
                $cont1++;				                
        	    $NOM = (array_key_exists($row['Idecco'],$wcentros)) ? $wcentros[$row['Idecco']]:'Sin Centro de Costos';
        	    $REA = (is_null($row['Mcaper']) AND is_null($row['Mcaano'])) ? "<img src='../../images/medical/root/borrar.png'/>"
        	           : "<img src='../../images/medical/root/grabar.png'/>";
        	    $vresultado .= '<tr class="'.$clase.'"><td align="center">'.$row['Arecdr'].'</td>
    	                   <td align="center">'.$row['nomjefe'].'</td>
    	                   <td align="center">'.$row['Idecco'].'</td>
    	                   <td align="center">'.$NOM.'</td><td align="center">'
    	                   .$row['Areper'].'-'.$row['Areano'].'</td><td align="center">'
    	                   .$REA.'</td></tr>';
			 }

			 $vresultado .= '<br><br><tr class="fila2" ><td align="center" colspan="9">
							 <input type="button" id=""btnregresarcen" name="btnregresarcen" class="button" 
							 value="Regresar" onclick="CerrarEvaluacion()"</td></tr></tbody>';
       }
    else
       {$vresultado='N';}   
    
    echo $vresultado;
    return;   
}

function consultarlisJefes($wbasedato,$conex,$wemp_pmla){
	
	$strtipvar = array();
	$q  = " SELECT A.Ajeucr, A.Ajeccr, concat(B.Ideno1,' ', B.Ideno2, ' ', B.Ideap1, ' ', B.Ideap2) as 'Nomemp'
		    From ".$wbasedato."_000008 A 
		    Inner join ".$wbasedato."_000013 B 
		    on A.Ajeucr = B.Ideuse
		    where A.Forest ='on'";

	$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	while($row = mysql_fetch_assoc($res))
	     {
	 	     $strtipvar[$row['Ajeucr']] = utf8_encode($row['Nomemp']);
	     }
	return $strtipvar;
}


function consultarContratos($wbasedato,$conex,$wemp_pmla){
	
	$strcontrato = '';
	$q  = " SELECT Detval From root_000051 where Detapl='contratosorganigrama' ";

	$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	while($row = mysql_fetch_assoc($res))
	     {
	 	     $strcontrato = utf8_encode($row['Detval']);
	     }

	return $strcontrato;
}


// ***********         Consultar todos los Centros de Costos para el campo array       **********
function consultarCentros($wbasedato,$conex,$wemp_pmla){
  
  $strtipvar = array();
  $q = "  SELECT  Empdes,Emptcc
           FROM    root_000050
           WHERE   Empcod = '".$wemp_pmla."'";
  $res = mysql_query($q,$conex);

  if($row = mysql_fetch_array($res))
  {
      $tabla_CCO = $row['Emptcc'];
      switch ($tabla_CCO)
      {
                case "clisur_000003":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                    FROM    clisur_000003 AS tb1
                                            INNER JOIN
                                            ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Ccodes";
                        break;
                case "farstore_000003":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                    FROM    farstore_000003 AS tb1
                                            INNER JOIN
                                            ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Ccodes";
                        break;
                case "costosyp_000005":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                    FROM    costosyp_000005 AS tb1
                                            INNER JOIN
                                            ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Cconom";
                        break;
                case "uvglobal_000003":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                    FROM    uvglobal_000003 AS tb1
                                            INNER JOIN
                                            ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Ccodes";
                        break;
                default:
                        $query="    SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                    FROM    costosyp_000005 AS tb1
                                            INNER JOIN
                                            ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Cconom";
            }

    $res = mysql_query($query,$conex);
    $res = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
    while($row = mysql_fetch_assoc($res))
       {
         $strtipvar[$row['codigo']] = $row['nombre'];
       }
    }
  
  return $strtipvar;
}

function ConsultarArbol($conex, $wemp_pmla,$wbasedato,$wcco)
{
	$data = array('arrayRelacion'=>array(), 'arrayCedulas'=>array(), 'arrayFotos'=>array(), 'arrayCargos'=>array(), 
	              'arrayCco'=>array(), 'arrayContrato'=>array(), 'arrayNombres'=>array(), 'arrayCC'=>array(), 'error'=>0);
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
	
	if($wcco!="")
	{
		$qEmpCco = " SELECT Ideuse,Ideno1,Ideno2,Ideap1,Ideap2,Ideced,Idegen,Idecco,Idetco,Cardes,Cconom  
					 FROM ".$wbasedato."_000013, root_000079, costosyp_000005  
				    WHERE Idecco = '".$wcco."' 
					 AND Ideest='on'
					 AND Ideccg = Carcod
					 AND Idecco = Ccocod
					 AND Ccoest = 'on'
					 AND Ideced IS NOT NULL;";
	}
	else
	{
		$qEmpCco = " SELECT Ideuse,Ideno1,Ideno2,Ideap1,Ideap2,Ideced,Idegen,Idecco,Idetco,Cardes,Cconom  
					 FROM ".$wbasedato."_000013, root_000079, costosyp_000005  
					WHERE Ideest='on'
					 AND Ideccg = Carcod
					 AND Idecco = Ccocod
					 AND Ccoest = 'on'
					 AND Ideced IS NOT NULL;";
	}	
	
	
	$resEmpCco=  mysql_query($qEmpCco,$conex) or die ("Error talhuma_000013: ".mysql_errno()." - en el query: ".$qEmpCco." - ".mysql_error());
	$numEmpCco = mysql_num_rows($resEmpCco);
	
	$arrayRelacion = array();
	$arrayCedulas  = array();
	$arrayCargos   = array();
	$arrayContrato = array();
	
	if($numEmpCco > 0)
	{
		$contSinPadre = 0;

		while($rowEmpCco = mysql_fetch_array($resEmpCco))
		{
			$nombreCompleto = "";
			if($rowEmpCco['Ideno1'] != "NO APLICA" && $rowEmpCco['Ideno1'] != "")
			{
				$nombreCompleto .= $rowEmpCco['Ideno1']." ";
			}
			if($rowEmpCco['Ideno2'] != "NO APLICA" && $rowEmpCco['Ideno2'] != "")
			{
				$nombreCompleto .= $rowEmpCco['Ideno2']." ";
			}
			if($rowEmpCco['Ideap1'] != "NO APLICA" && $rowEmpCco['Ideap1'] != "")
			{
				$nombreCompleto .= $rowEmpCco['Ideap1']." ";
			}
			if($rowEmpCco['Ideap2'] != "NO APLICA" && $rowEmpCco['Ideap2'] != "")
			{
				$nombreCompleto .= $rowEmpCco['Ideap2']." ";
			}
			
			
			$arrayNombres[$rowEmpCco['Ideuse']] = utf8_encode($nombreCompleto);
			$arrayCC[$rowEmpCco['Ideuse']] = $rowEmpCco['Idecco'];
			

			$qHijo = "SELECT Ajeuco,Ajeucr,Ideno1,Ideno2,Ideap1,Ideap2,Ideced,Idegen,Idetco,Cardes,Cconom  
						FROM ".$wbasedato."_000008,".$wbasedato."_000013, root_000079, costosyp_000005   
					   WHERE Ajeucr='".$rowEmpCco['Ideuse']."' 
						 AND Forest='on'
						 AND Ajeuco=Ideuse 
						 AND Ideest='on' 
						 AND Ideccg = Carcod
						 AND Idecco = Ccocod
						 AND Ccoest = 'on';";
			
			$resHijo=  mysql_query($qHijo,$conex) or die ("Error talhuma_000008: ".mysql_errno()." - en el query: ".$qHijo." - ".mysql_error());
			$numHijos = mysql_num_rows($resHijo);
	
			if(!isset($arrayTienePadre[$rowEmpCco['Ideuse']]))
			{
				$arrayRelacion[0][$rowEmpCco['Ideuse']] = utf8_encode($nombreCompleto);
				$arrayCedulas[0][$rowEmpCco['Ideuse']]  = $rowEmpCco['Ideced'];
				$arrayFotos[0][$rowEmpCco['Ideuse']]    = consultarFoto($conex,$wemp_pmla,$wbasedato,$rowEmpCco['Ideced'],$rowEmpCco['Idegen']);
				$arrayCargos[0][$rowEmpCco['Ideuse']]   = utf8_encode($rowEmpCco['Cardes']);
				$arrayCco[0][$rowEmpCco['Ideuse']]      = utf8_encode($rowEmpCco['Cconom']);
				$arrayContrato[0][$rowEmpCco['Ideuse']] = $rowEmpCco['Idetco'];
			}
			if($numHijos > 0)
			{
				while($rowHijo = mysql_fetch_array($resHijo))
				{
					$nombreCompleto = "";
					if($rowHijo['Ideno1'] != "NO APLICA" && $rowHijo['Ideno1'] != "")
					{
						$nombreCompleto .= $rowHijo['Ideno1']." ";
					}
					if($rowHijo['Ideno2'] != "NO APLICA" && $rowHijo['Ideno2'] != "")
					{
						$nombreCompleto .= $rowHijo['Ideno2']." ";;
					}
					if($rowHijo['Ideap1'] != "NO APLICA" && $rowHijo['Ideap1'] != "")
					{
						$nombreCompleto .= $rowHijo['Ideap1']." ";
					}
					if($rowHijo['Ideap2'] != "NO APLICA" && $rowHijo['Ideap2'] != "")
					{
						$nombreCompleto .= $rowHijo['Ideap2']." ";
					}
								
					unset($arrayRelacion[0][$rowHijo['Ajeuco']]);
					unset($arrayCedulas[0][$rowHijo['Ajeuco']]);
					unset($arrayFotos[0][$rowHijo['Ajeuco']]);
					unset($arrayCargos[0][$rowHijo['Ajeuco']]);
					unset($arrayCco[0][$rowHijo['Ajeuco']]);
					unset($arrayContrato[0][$rowHijo['Ajeuco']]);
					
					$arrayRelacion[$rowHijo['Ajeucr']][$rowHijo['Ajeuco']] = utf8_encode($nombreCompleto);
					$arrayCedulas[$rowHijo['Ajeucr']][$rowHijo['Ajeuco']]  = $rowHijo['Ideced'];
					$arrayFotos[$rowHijo['Ajeucr']][$rowHijo['Ajeuco']]    = consultarFoto($conex,$wemp_pmla,$wbasedato,$rowHijo['Ideced'],$rowHijo['Idegen']);
					$arrayCargos[$rowHijo['Ajeucr']][$rowHijo['Ajeuco']]   = utf8_encode($rowHijo['Cardes']);
					$arrayCco[$rowHijo['Ajeucr']][$rowHijo['Ajeuco']] = $rowHijo['Cconom'];
					$arrayContrato[$rowHijo['Ajeucr']][$rowHijo['Ajeuco']] = $rowHijo['Idetco'];
					
					$arrayTienePadre[$rowHijo['Ajeuco']]='on';
					$arrayTieneHijos[$rowHijo['Ajeucr']]='on';
				
				}
				
			}
		}
	}
	
	if(count($arrayRelacion) > 0)
	{
		foreach ($arrayRelacion[0] as $key => $value)
		{	
			$tieneHijos = false;
			foreach ($arrayTieneHijos as $keyTieneHijos => $valueTieneHijos)
			{		
				if($keyTieneHijos == $key)
				{
					$tieneHijos = true;
				}
			}
			if($tieneHijos == false)
			{
				$arrayRelacion[1][$key] = $arrayRelacion[0][$key];
				$arrayCedulas[1][$key]  = $arrayCedulas[0][$key];
				$arrayFotos[1][$key]  	= $arrayFotos[0][$key];
				$arrayCargos[1][$key] 	= $arrayCargos[0][$key];
				$arrayCco[1][$key] 		= $arrayCco[0][$key];
				$arrayContrato[1][$key] = $arrayContrato[0][$key];
					
				unset($arrayRelacion[0][$key]);
				unset($arrayCedulas[0][$key]);
				unset($arrayFotos[0][$key]);
				unset($arrayCargos[0][$key]);
				unset($arrayCco[0][$key]);
				unset($arrayContrato[0][$key]);
			}
		}
		
		$data['error'] = 0;
	}
	else
	{
		$data['error'] = 1;
	}	

	$data['arrayRelacion'] = $arrayRelacion;
	$data['arrayCedulas']  = $arrayCedulas;
	$data['arrayFotos']    = $arrayFotos;
	$data['arrayCargos']   = $arrayCargos;
	$data['arrayCco']      = $arrayCco;
	$data['arrayContrato'] = $arrayContrato;
	$data['arrayNombres']  = $arrayNombres;
	$data['arrayCC']       = $arrayCC;
	
	return $data;	
		
}

if(isset($accion))
{
	include_once("root/comun.php");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
	switch($accion)
	{
		case 'inicioprograma':
		{
			$data = ConsultarArbol($conex, $wemp_pmla,$wbasedato,$wcco);
			echo json_encode($data);
			break;
		}
		case 'guardarRelaciones':
		{
			$data = guardarNuevasRelaciones($conex, $wemp_pmla,$wbasedato,$wcco,$arrayCambios,$arrayNombres,$arrayCco,$aceptaSinPadre);
			echo json_encode($data);
			break;
		}
		case 'ConsultarEmpleadosAutocomplete':
		{
			$data = ConsultarEmpleadosAutocomplete($conex, $wemp_pmla,$wbasedato,$wcco);
			echo json_encode($data);
			break;
		}
		case 'ConsultarCcoAutocomplete':
		{
			$data = ConsultarCcoAutocomplete($conex, $wemp_pmla,$wbasedato);
			echo json_encode($data);
			break;
		}
		case 'InactivarEmpleado':
		{
			$data = InactivarEmpleado($conex, $wemp_pmla,$wbasedato,$wempleadoid);
			echo $data;
			break;
		}

		case 'CambiarCentro':
		{
			$data = CambiarCentro($conex, $wemp_pmla,$wbasedato,$wempleadoid,$wcentroid,$wjefeid);
			echo $data;
			break;
		}

		case 'GrabarEmail':
		{
			$data = GrabarEmail($conex, $wemp_pmla,$wbasedato,$wemail,$wempleadoid);
			echo $data;
			break;	
		}

		case 'MostrarEvaluaciones':
		{
			$data = MostrarEvaluaciones($conex, $wemp_pmla,$wbasedato,$wempleadoid,$wcentrocos);
			echo $data;
			break;
		}

		case 'ConsultarCentroactual':
		{
			$data = ConsultarCentroactual($conex, $wemp_pmla,$wbasedato,$wempleadoid,$wcentrocos);
			echo $data;
			break;
		}

		case 'ConsultarEmail':
		{
			$data = ConsultarEmail($conex, $wemp_pmla,$wbasedato,$wempleadoid);
			echo $data;
			break;
		}
		
		case 'ConsultarJefe':
		{
			$data = ConsultarJefe($conex, $wemp_pmla,$wbasedato,$wcentrocos);
			echo $data;
			break;	
		}
		
	}
	return;
}

/**********************************************************************************/
// 							CARGA INICIAL DE LA PÁGINA
/**********************************************************************************/

include_once("root/comun.php");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');

$query= "SELECT * "
	  . "  FROM  ".$wbasedato."_000013 "
	  ."  WHERE  Idecco = '".$wcco."' "
	  . "	AND  Ideest = 'on' ";
$res = mysql_query($query,$conex) or die ("Error talhuma_000013: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
$numr = mysql_num_rows($res);

$html_cco = '';
if ($numr <= 0)
{
	
	// $html_cco =  "<table align='center'><tr class='fila1'><td>El Centro de costos no tiene Personal asignado
	// </td></tr></table>";
}

?>
<html>
<head>
  <title>Organigrama</title>
  
</head>

		<script type="text/javascript" src="../../../include/root/jquery.min.js"></script>

		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
		<!--<script type="text/javascript" src="../../../include/root/jquery.blockUI-2-70-0.js"></script>-->
		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>		
		
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>		
		
		<!-- ORGANIGRAMA -->		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		 <!-- jQuery UI Layout -->
		<script type="text/javascript" src="../../../include/root/jquery.layout-latest.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../../../include/root/layout-default-latest.css" />
		
		<!-- header -->
		
		<link href="../../../include/root/primitives_2_1_10.css" media="screen" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="../../../include/root/primitives_2_1_10.min.js"></script>
		
<script type="text/javascript">

	var orgdiagram = null;
	var orgdiagram2 = null;

	var counter = 0;
	var m_timer = null;
	var fromValue = null;
	var fromChart = null;
	var toValue = null;
	var toChart = null;
	var items = {};
	
	// var cambios = [];
	var cambios = {};
	var nombres = {};
	var cco = {};
	
	var conPadre = 0;
	
	function SetupWidget(element,name,arrayRelacion,arrayCedulas,arrayFotos,arrayCargos,arrayCco,arrayContrato,estado,contratoex ) {
		
		var result;
		var options = new primitives.orgdiagram.Config();
		
		var itemsToAdd = [];
		var Arraycolores = ["green","blue","red"]
				
		if(estado == "relacionado")
		{
			for( var x in arrayRelacion ){
				padre = x.split("-");
				
				for( var y in arrayRelacion[x] ){
					emp = y.split("-");
					
					id=y;
					
					if (x == 0)
					{
						var newItem = new primitives.orgdiagram.ItemConfig({
							id: id,
							parent: null,
							title: arrayRelacion[x][y],
							description: arrayCargos[x][y],
							image: arrayFotos[x][y]
						});
						itemsToAdd.push(newItem);
						items[newItem.id] = newItem;

						if (options.cursorItem == null) {
							options.cursorItem = newItem.id;
						}
					}
					else if (x != 1)
					{
						conPadre++;
						var newSubItem = new primitives.orgdiagram.ItemConfig({
							id: id,
							parent: x,
							title: arrayRelacion[x][y],
							description: arrayCargos[x][y],
							image:  arrayFotos[x][y]
						});
						itemsToAdd.push(newSubItem);
						items[newSubItem.id] = newSubItem;
					}
					
					
				}
			}
		}
		else if(estado == "sinRelacion")
		{ 			
			cont=0;
			for( var x in arrayRelacion ){			    
				padre = x.split("-");
				
				for( var y in arrayRelacion[x] ){
					emp = y.split("-");
					id=y;
					if (x == 1)
					{
						if (contratoex.search(arrayContrato[x][y]) == -1)
						{	
							cont++;
							var newItem = new primitives.orgdiagram.ItemConfig({
								id: id,
								parent: null,
								title: arrayRelacion[x][y],
								description: arrayCargos[x][y],
								image:  arrayFotos[x][y]
							});
							itemsToAdd.push(newItem);
							items[newItem.id] = newItem;

							if (options.cursorItem == null) {
								options.cursorItem = newItem.id;
							}
					    }	
					}
				}
			}
			$("#txtempsin").val(cont);
		}
		
		var buttons = [];
        buttons.push(new primitives.orgdiagram.ButtonConfig("Inactivar", "ui-icon-circle-close", "Inactivar"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("Cambiarcentro", "ui-icon-copy", "Cambiar Centro de Costos"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("Ingresaremail", "ui-icon-mail-closed", "Ingresar Email"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("Consultarevaluacion", "ui-icon-clipboard", "Mostrar Evaluaciones"));

		options.items = itemsToAdd;
		options.normalLevelShift = 20;
		options.dotLevelShift = 10;
		options.lineLevelShift = 10;
		options.normalItemsInterval = 20;
		options.dotItemsInterval = 10;
		options.lineItemsInterval = 5;
		options.buttonsPanelSize = 48;
		
		// Esta propiedad es la que ajusta los items del arbol en la div o layout
		// options.pageFitMode = primitives.common.PageFitMode.None;
		options.pageFitMode = primitives.common.PageFitMode.FitToPage;
		
		if(estado == "sinRelacion")
		{
			options.orientationType = primitives.common.OrientationType.Left;
		}
		
		options.graphicsType = primitives.text.Config;
		options.graphicsType = primitives.common.GraphicsType.Auto;
		options.hasSelectorCheckbox = primitives.common.Enabled.False;
		options.buttons = buttons;
		//options.hasButtons = primitives.common.Enabled.True; 
		options.templates = [getContactTemplate()];
		options.defaultTemplateName = "contactTemplate";
		options.onItemRender = (name == "orgdiagram") ? onOrgDiagramTemplateRender : onOrgDiagram2TemplateRender;
		
		/* chart uses mouse drag to pan items, disable it in order to avoid conflict with drag & drop */
		options.enablePanning = false;
		var arr_cen   = $("#arr_cen").val(); 
		options.onButtonClick = function (e, data) {
                
                switch(data.name) {
                case 'Inactivar' :
					$( "#confirmInactivar" ).dialog({
					    resizable: true,
						title: "Confirmar Inactivar el Empleado",
						height:200,
						width:700,
						position: ['left+180', 'top+100'],
						modal: true,
						buttons: {
							"SI": function() {

			                     $.post("consultaorganigrama.php",
								 {
									consultaAjax : true,
									accion       : 'InactivarEmpleado',
									wemp_pmla	 : $('#wemp_pmla').val(),
									wempleadoid  : data.context.id
								 }, function(respuesta){
					                if (respuesta==1)
					                {
					                	mostraralerta('El Usuario ha sido Inactivado');
					                	location.reload();
					                }
								 });
								 $( "#confirmInactivar" ).dialog( "close" );							
							},
							"NO": function() {
								$( "#confirmInactivar" ).dialog( "close" );
							}
						}
					});
                    break;
                
                case 'Cambiarcentro' :
                     $.post("consultaorganigrama.php",
							 {
								consultaAjax : true,
						        async 		 : false,	
								accion       : 'ConsultarCentroactual',
								wemp_pmla	 : $('#wemp_pmla').val(),
								wempleadoid  : data.context.id,
								wcentrocos   : arr_cen
							 }, function(respuesta){
							 	$("#txtcentroant").val(respuesta);
							 	$("#wcontexid").val(data.context.id);  
							 	$("#txtcentrocco").val('');
							 	$("#txtnuejefe").val('');
                                $("#pancambiarcentro").dialog("open");   
							 });
                	 break;
                
                case 'Ingresaremail' :
                     $.post("consultaorganigrama.php",
							 {
								consultaAjax : true,
						        async 		 : false,	
								accion       : 'ConsultarEmail',
								wemp_pmla	 : $('#wemp_pmla').val(),
								wempleadoid  : data.context.id
							 }, function(respuesta){
							 	$("#txtemail").val(respuesta);
							 	$("#wcontexid").val(data.context.id);   
                                $("#pancambiaremail").dialog("open");   
							 });
                	 break;	 
                
                case 'Consultarevaluacion' :                   
                     $("#wcontexid").val(data.context.id);
					 $.post("consultaorganigrama.php",
							 {
								consultaAjax : true,
						        async 		 : false,	
								accion       : 'MostrarEvaluaciones',
								wemp_pmla	 : $('#wemp_pmla').val(),
								wempleadoid  : data.context.id,
								wcentrocos   : arr_cen
							 }, function(respuesta){
							    if (respuesta=='N'){
							        mostraralerta('El Empleado no tiene Evaluaciones');}
							    else{
							    	$("#panevaluacion").dialog("open");
								 	$("#panevaluacion").append(respuesta);}
							 });
                	 break;	
                }
                var message = "opcion '" + data.name + "' seleccion '" + data.context.id + "'.";                
        };
		
		result = element.orgDiagram(options);

		element.droppable({
			greedy: true,
			drop: function (event, ui) {
				/* Check drop event cancelation flag
				* This fixes following issues:
				* 1. The same event can be received again by updated chart
				* so you changed hierarchy, updated chart and at the same drop position absolutly 
				* irrelevant item receives again drop event, so in order to avoid this use primitives.common.stopPropagation
				* 2. This particlular example has nested drop zones, in order to 
				* suppress drop event processing by nested droppable and its parent we have to set "greedy" to false,
				* but it does not work.
				* In this example items can be droped to other items (except immidiate children in order to avoid looping)
				* and to any free space in order to make them rooted.
				* So we need to cancel drop  event in order to avoid double reparenting operation.
				*/
				if (!event.cancelBubble) {
					toValue = null;
					toChart = name;

					//Si no tiene padre queda en el organigrama 2 (empleados sin relacion)
					if(toValue==null)
					{
						
						//Validar si hay algo en orgdiagram, si no hay nada puede poner uno
						conPadre = jQuery("#orgdiagram").orgDiagram("option", "items").length;
						if(conPadre == 0)
						{
							toChart = "orgdiagram";
						}
						else
						{
							toChart = "orgdiagram2";
						}
						

						Reparent(fromChart, fromValue, toChart, toValue);
					}
					else
					{
						toChart = "orgdiagram";
						
						Reparent(fromChart, fromValue, toChart, toValue);
					
					}
					
					conPadre = jQuery("#orgdiagram").orgDiagram("option", "items").length;
				
					primitives.common.stopPropagation(event);
				}
			}
		});

		return result;
	}
	
	function getContactTemplate() {
		var result = new primitives.orgdiagram.TemplateConfig();
		result.name = "contactTemplate";

		// result.labelSize = new primitives.common.Size(180, 100);
		result.itemSize = new primitives.common.Size(140, 100);
		// result.itemSize = new primitives.common.Size(180, 100);
		result.minimizedItemSize = new primitives.common.Size(4, 4);
		result.highlightPadding = new primitives.common.Thickness(2, 2, 2, 2);

		result.fontSize = primitives.text.Config('6px');
		
		var itemTemplate = jQuery(
		  '<div class="bp-item bp-corner-all bt-item-frame">'
			+ '<div name="titleBackground" class="bp-item bp-corner-all bp-title-frame" style="top: 2px; left: 2px; width: 136px; height: 20px;">'
				+ '<div name="title" class="bp-item bp-title" style="top: 3px; left: 6px; width: 128px; height: 18px; font-size:9.5px">'
				+ '</div>'
			+ '</div>'
			+ '<div class="bp-item bp-photo-frame" style="top: 26px; left: 2px; width: 50px; height: 60px;">'
				+ '<img name="photo" style="height:60px; width:50px;" />'
			+ '</div>'
			+ '<div name="description" class="bp-item" style="top: 26px; left: 56px; width: 82px; height: 52px; font-size: 10px;"></div>'
		+ '</div>'
		).css({
			width: result.itemSize.width + "px",
			height: result.itemSize.height + "px"
		}).addClass("bp-item bp-corner-all bt-item-frame");
		result.itemTemplate = itemTemplate.wrap('<div>').parent().html();

		return result;
	}

	function onOrgDiagramTemplateRender(event, data) {
		switch (data.renderingMode) {
			case primitives.common.RenderingMode.Create:
				data.element.draggable({
					revert: "invalid",
					containment: "document",
					appendTo: "body",
					helper: "clone",
					cursor: "move",
					"zIndex": 10000,
					delay: 300,
					distance: 10,
					start: function (event, ui) {
						fromValue = jQuery(this).attr("data-value");
						fromChart = "orgdiagram";
					}
				});
				data.element.droppable({
					/* this option is supposed to suppress event propogation from nested droppable to its parent
					*  but it does not work
					*/
					greedy: true,
					drop: function (event, ui) {
						if (!event.cancelBubble) {
							console.log("Drop accepted!");
							// toValue = parseInt(jQuery(this).attr("data-value"), 10);
							toValue = jQuery(this).attr("data-value");
							toChart = "orgdiagram";

							Reparent(fromChart, fromValue, toChart, toValue);
							
							primitives.common.stopPropagation(event);
						} else {
							console.log("Drop ignored!");
						}
					},
					over: function (event, ui) {
						// toValue = parseInt(jQuery(this).attr("data-value"), 10);
						toValue = jQuery(this).attr("data-value");
						toChart = "orgdiagram";

						/* this is needed in order to update highlighted item in chart, 
						* so this creates consistent mouse over feed back  
						*/
						jQuery("#orgdiagram").orgDiagram({ "highlightItem": toValue });
						jQuery("#orgdiagram").orgDiagram("update", primitives.common.UpdateMode.PositonHighlight);
					},
					accept: function (draggable) {
						/* be carefull with this event it is called for every available droppable including invisible items on every drag start event.
						* don't varify parent child relationship between draggable & droppable here it is too expensive.
						*/
						return (jQuery(this).css("visibility") == "visible");
					}
				});
				/* Initialize widgets here */
				break;
			case primitives.common.RenderingMode.Update:
				/* Update widgets here */
				break;
		}

		var itemConfig = data.context;

		/* Set item id as custom data attribute here */
		data.element.attr("data-value", itemConfig.id);

		RenderField(data, itemConfig);
	}

	function onOrgDiagram2TemplateRender(event, data) {
		switch (data.renderingMode) {
			case primitives.common.RenderingMode.Create:
				data.element.draggable({
					revert: "invalid",
					containment: "document",
					appendTo: "body",
					helper: "clone",
					cursor: "move",
					"zIndex": 10000,
					delay: 300,
					distance: 10,
					start: function (event, ui) {
						fromValue = jQuery(this).attr("data-value");
						fromChart = "orgdiagram2";
					}
				});
				data.element.droppable({
					greedy: true,
					drop: function (event, ui) {
						if (!event.cancelBubble) {
							console.log("Drop accepted!");
							toValue = jQuery(this).attr("data-value");
							toChart = "orgdiagram2";

							Reparent(fromChart, fromValue, toChart, toValue);
							primitives.common.stopPropagation(event);
						} else {
							console.log("Drop ignored!");
						}
					},
					over: function (event, ui) {
						toValue = jQuery(this).attr("data-value");
						toChart = "orgdiagram2";

						jQuery("#orgdiagram2").orgDiagram({ "highlightItem": toValue });
						jQuery("#orgdiagram2").orgDiagram("update", primitives.common.UpdateMode.PositonHighlight);
					},
					accept: function (draggable) {
						return (jQuery(this).css("visibility") == "visible");
					}
				});
				/* Initialize widgets here */
				break;
			case primitives.common.RenderingMode.Update:
				/* Update widgets here */
				break;
		}

		var itemConfig = data.context;

		data.element.attr("data-value", itemConfig.id);

		RenderField(data, itemConfig);
	}

	function Reparent(fromChart, value, toChart, toParent) {
		/* following verification needed in order to avoid conflict with jQuery Layout widget */
		if (fromChart != null && value != null && toChart != null) {
			console.log("Reparent fromChart:" + fromChart + ", value:" + value + ", toChart:" + toChart + ", toParent:" + toParent);
			
			cambios[value] = toParent;
			
			var item = items[value];
			var fromItems = jQuery("#" + fromChart).orgDiagram("option", "items");
			var toItems = jQuery("#" + toChart).orgDiagram("option", "items");
			if (toParent != null) {
				var toParentItem = items[toParent];
				if (!isParentOf(item, toParentItem)) {

					var children = getChildrenForParent(item);
					children.push(item);
					for (var index = 0; index < children.length; index++) {
						var child = children[index];
						fromItems.splice(primitives.common.indexOf(fromItems, child), 1);
						toItems.push(child);
					}
					item.parent = toParent;
				} else {
					console.log("Droped to own child!");
				}
			} else {
				var children = getChildrenForParent(item);
				children.push(item);
				for (var index = 0; index < children.length; index++) {
					var child = children[index];
					fromItems.splice(primitives.common.indexOf(fromItems, child), 1);
					toItems.push(child);
				}
				item.parent = null;
			}
			jQuery("#orgdiagram").orgDiagram("update", primitives.common.UpdateMode.Refresh);
			jQuery("#orgdiagram2").orgDiagram("update", primitives.common.UpdateMode.Refresh);
		}
	}


	function getChildrenForParent(parentItem) {
		var children = {};
		for (var id in items) {
			var item = items[id];
			if (children[item.parent] == null) {
				children[item.parent] = [];
			}
			children[item.parent].push(id);
		}
		var newChildren = children[parentItem.id];
		var result = [];
		if (newChildren != null) {
			while (newChildren.length > 0) {
				var tempChildren = [];
				for (var index = 0; index < newChildren.length; index++) {
					var item = items[newChildren[index]];
					result.push(item);
					if (children[item.id] != null) {
						tempChildren = tempChildren.concat(children[item.id]);
					}
				}
				newChildren = tempChildren;
			}
		}
		return result;
	}

	function isParentOf(parentItem, childItem) {
		var result = false,
			index,
			len,
			itemConfig;
		if (parentItem.id == childItem.id) {
			result = true;
		} else {
			while (childItem.parent != null) {
				childItem = items[childItem.parent];
				if (childItem.id == parentItem.id) {
					result = true;
					break;
				}
			}
		}
		return result;
	};

	function RenderField(data, itemConfig) {
		if (data.templateName == "contactTemplate") {
			data.element.find("[name=photo]").attr({ "src": itemConfig.image, "alt": itemConfig.title });
			data.element.find("[name=titleBackground]").css({ "background": itemConfig.itemTitleColor });

			var fields = ["title", "description", "phone", "email"];
			for (var index = 0; index < fields.length; index++) {
				var field = fields[index];

				var element = data.element.find("[name=" + field + "]");
				if (element.text() != itemConfig[field]) {
					element.text(itemConfig[field]);
				}
			}
		}
	}

	function ResizePlaceholder() {
				
		var panel = jQuery("#centerpanel");
		// var panelSize = new primitives.common.Rect(200, 30, 1200, 550);
		
		var position = new primitives.common.Rect(150, 30, 1050, 550);
		// var position = new primitives.common.Rect(150, 30, 650, 550);
		position.offset(-2);
		
		var position2 = new primitives.common.Rect(1200, 30, 200, 550);
		// var position2 = new primitives.common.Rect(800, 30, 200, 550);
		position2.offset(-2);
		
		jQuery("#orgdiagram").css(position.getCSS());
		jQuery("#orgdiagram2").css(position2.getCSS());
	}

    // Verificar Email digitado y actualizarlo en la tabla talhuma_000013
    function GrabarEmail(){
	    
	    var email = $("#txtemail").val();        
	    re=/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/;
	    if(!re.exec(email))    {
	        mostraralerta('El Email digitado es incorrecto');
	        return;
	    }

		$.post("consultaorganigrama.php",
			 {
				consultaAjax : true,
				accion       : 'GrabarEmail',
				wemp_pmla	 : $('#wemp_pmla').val(),
				wemail       : $('#txtemail').val(),
				wempleadoid  : $("#wcontexid").val()
			 }, function(respuesta){
	            if (respuesta==1)
	            {
	            	mostraralerta('Grabado Exitoso');
	            	$("#pancambiaremail").dialog("close");
	            }
			 });	    

    }

	function GrabarCentroCostos()
	{
		$.post("consultaorganigrama.php",
			  {
				consultaAjax : true,
				accion       : 'CambiarCentro',
				wemp_pmla	 : $('#wemp_pmla').val(),
				wcentroid    : $("#wcencos").val(),
				wempleadoid  : $("#wcontexid").val(),
				wjefeid      : $("#wjefe").val()
			  }, function(respuesta){
	            if (respuesta==1)
	            {
	            	mostraralerta('Grabado Exitoso');
	            	$("#pancambiarcentro").dialog("close");
	                location.reload();
	            }
			  });
	}

	function cerrarVentana()
	{
		window.close();
	}


	// ******** Asignar el Jefe segun el centro de costos digitado *****
	function AsignarJefe(centro)
	{
		var wcentrocos = $("#wcencos").val();
		$.post("../procesos/consultaorganigrama.php",
		      {
				  consultaAjax:   true,
				  accion      :  'ConsultarJefe',
				  wemp_pmla   :   $('#wemp_pmla').val(),		 
				  wcentrocos  :   wcentrocos
			  }, function(respuesta){
			      var nuejefe = respuesta.split('|')
			      $("#txtnuejefe").val(nuejefe[1]);
			      $("#wjefe").val(nuejefe[0]);
			  });
	}

	function CerrarEvaluacion()
	{
		$("#panevaluacion").dialog("close");
		$("#panevaluacion tbody ").remove(0);
	}

$(document).ready(function() {

	$("#panevaluacion").dialog({
        autoOpen: false,
        height: 500,
        width: 1200,
        position: ['left+70', 'top+50'],
        modal: true
	});
    
	$("#panevaluacion").dialog("widget").find(".ui-dialog-titlebar").hide();

	$("#pancambiarcentro").dialog({
        autoOpen: false,
        height: 380,
        width: 600,
        title:'Centro de Costos',
        position: ['left+180', 'top+80'],
        modal: true
	});

	$("#pancambiaremail").dialog({
        autoOpen: false,
        height: 300,
        width: 600,
        title:'Email',
        position: ['left+180', 'top+90'],
        modal: true
	});


    //Consultar el listado de Jefes 
    var arr_jefe   =  eval('(' + $('#arr_jefe').val() + ')');
    var jefes      =  new Array();
	var index      =  -1;

    for (var cod_jefe in arr_jefe)
    {
        index++;
        jefes[index]                = {};
        jefes[index].value          = cod_jefe;
        jefes[index].label          = cod_jefe+'-'+arr_jefe[cod_jefe];
        jefes[index].codigo         = cod_jefe;
        jefes[index].nombre         = arr_jefe[cod_jefe];
    }            

    $("#txtnuejefe").autocomplete({
    	minLength: 	0,
          source: jefes,
       autoFocus: true,
	      select: function( event, ui ){
				$( "#txtnuejefe" ).val(ui.item.nombre);
				$( "#txtnuejefe" ).attr('valor', ui.item.value);
				$( "#txtnuejefe" ).attr('label', ui.item.label);
				$( "#txtnuejefe" ).attr("codigo",ui.item.codigo);
				$("#wjefe").val(ui.item.codigo);                
                return false;
        }
    });   

	$.post("../procesos/consultaorganigrama.php",
		{
			consultaAjax 	: '',
			inicial			: 'no',
			accion			: 'inicioprograma',
			wemp_pmla		: $('#wemp_pmla').val(),
			wbasedato		: $('#wbasedato').val(),
			wcco			: $('#wcco').val()
		}
		, function(data) {

			contratoex = $('#arr_cont').val();
			nombres    = data.arrayNombres;
			cco = data.arrayCC;
			
			jQuery.ajaxSetup({
				cache: false
			});

			jQuery('#contentpanel').layout(
			{
				center__paneSelector: "#centerpanel"
				, center__onresize: function () {
					if (orgdiagram != null && orgdiagram2 != null) {
						ResizePlaceholder();
						jQuery("#orgdiagram").orgDiagram("update", primitives.common.UpdateMode.Refresh);
						jQuery("#orgdiagram2").orgDiagram("update", primitives.common.UpdateMode.Refresh);
					}
				}
			});
			
			if(data.error == 1)
			{
				$( "#alertMsj" ).html("El centro de costos no tiene personal asignado");
			
				$( "#alertMsj" ).dialog({
					modal: true,
					width:350,
					position: {my: "middle",
							   at: "middle",
							   of: "#contentpanel"},
					buttons: {
					OK: function() {
					 $( "#alertMsj" ).dialog( "close" );
					}
				  }
				});
			}

			ResizePlaceholder();
			orgdiagram = SetupWidget(jQuery("#orgdiagram"), "orgdiagram",data.arrayRelacion,data.arrayCedulas,data.arrayFotos,data.arrayCargos,data.arrayCco,data.arrayContrato,"relacionado",contratoex);
			orgdiagram2 = SetupWidget(jQuery("#orgdiagram2"), "orgdiagram2",data.arrayRelacion,data.arrayCedulas,data.arrayFotos,data.arrayCargos,data.arrayCco,data.arrayContrato,"sinRelacion",contratoex);
						
			cargarAutocompleteEmpleados($('#wcco').val());
			cargarAutocompleteCco();
		
		},'json');

});

function mostraralerta(mensaje)
{
	$( "#alertMsj" ).html(mensaje);
			
	$( "#alertMsj" ).dialog({
		modal: true,
		width:350,
		position: ['left+250', 'top+100'],
		buttons: {
		OK: function() {
		 $( "#alertMsj" ).dialog( "close" );
		}
	  }
	});
}

function recargar()
{
	$( "#confirmRestaurar" ).dialog({
		resizable: true,
		title: "Confirmar restaurar",
		width:350,
		height:200,
		position: {my: "middle",
				   at: "middle",
				   of: "#contentpanel"},
		modal: true,
		buttons: {
			"Aceptar": function() {
			$( this ).dialog( "close" );
			location.reload();
        },
			"Cancelar": function() {
			$( this ).dialog( "close" );
			}
		}
    });
}

function guardar()
{
	
	$( "#confirmGuardar" ).dialog({
      resizable: true,
	  title: "Confirmar guardar",
      height:200,
	  width:350,
	  position: {  my: "middle",
				   at: "middle",
				   of: "#contentpanel"},
      modal: true,
      buttons: {
        "Aceptar": function() {
			
			$( "#confirmGuardar" ).dialog( "close" );

			var cadenaSinPadre = "";
			
			for( var x in cambios )
			{
				console.log(x+" - "+cambios[x]);
				if(cambios[x] == null)
				{
					console.log(nombres[x])
					cadenaSinPadre += nombres[x]+", ";
				}
				
			}
			
			aceptaSinPadre = true;
			if(cadenaSinPadre!="")
			{
				$( "#confirmSinPadre" ).html("Los empleados "+cadenaSinPadre.substr(0,cadenaSinPadre.length-2)+" quedar&aacute;n sin un jefe relacionado, Desea guardar estas relaciones?");
				
				$( "#confirmSinPadre" ).dialog({
				    resizable: true,
					title: "Confirmar guardar relaci&oacute;n sin jefe",
					height:200,
					width:700,
					position: {my: "middle",
							   at: "middle",
							   of: "#contentpanel"},
					modal: true,
					buttons: {
						"SI": function() {
							aceptaSinPadre = true;
							$( "#confirmSinPadre" ).dialog( "close" );
							guardarRelaciones(cambios,nombres,cco,aceptaSinPadre);
						},
						"NO": function() {
							aceptaSinPadre = false;
							$( "#confirmSinPadre" ).dialog( "close" );
							guardarRelaciones(cambios,nombres,cco,aceptaSinPadre);
						}
					}
				});
				
				
			}
			else
			{
				aceptaSinPadre = false;
				guardarRelaciones(cambios,nombres,cco,aceptaSinPadre);
			}
			
			
			
        },
        "Cancelar": function() {
			$( this ).dialog( "close" );
        }
      }
    });
	
	
	
}

function guardarRelaciones(cambios,nombres,cco,aceptaSinPadre)
{
	$.post("../procesos/consultaorganigrama.php",
	{
		consultaAjax 	: '',
		accion			: 'guardarRelaciones',
		wemp_pmla		: $('#wemp_pmla').val(),
		wbasedato		: $('#wbasedato').val(),
		wcco			: $('#wcco').val(),
		arrayCambios	: cambios,
		arrayNombres	: nombres,
		arrayCco		: cco,
		aceptaSinPadre	: aceptaSinPadre
	}
	, function(data) {
		
		if(data.error == 0)
		{
			$( "#alertMsj" ).html("Se guardaron los cambios correctamente");
			
			$( "#alertMsj" ).dialog({
				modal: true,
				width:350,
				position: {my: "middle",
						   at: "middle",
						   of: "#contentpanel"},
				buttons: {
				OK: function() {
				  $( "#alertMsj" ).dialog( "close" );
				  cco = $("#wcco").val();
				  $("#buscarCco").attr('valor',cco);
				  seleccionCcostos();
				}
			  }
			});
			
		}
		else if(data.error == 1)
		{
			var mensError = "";
			if(data.mensErrorAct != "")
			{
				mensError += data.mensErrorAct+"\n";
			}
			if(data.mensErrorIns != "")
			{
				mensError += data.mensErrorIns+"\n";
			}
			if(data.mensErrorEli != "")
			{
				mensError += data.mensErrorEli+"\n";
			}
			
			if(mensError != "")
			{
				$( "#alertMsj" ).html(mensError);
			
				$( "#alertMsj" ).dialog({
					modal: true,
					width:350,
					position: {my: "middle",
							   at: "middle",
							   of: "#contentpanel"},
					buttons: {
					OK: function() {
					 $( "#alertMsj" ).dialog( "close" );
					}
				  }
				});
				
			}
		}
	},'json');
}


function cargarAutocompleteCco()
{
	$.post("../procesos/consultaorganigrama.php",
	{
		consultaAjax 	: '',
		accion			: 'ConsultarCcoAutocomplete',
		wemp_pmla		: $('#wemp_pmla').val(),
		wbasedato		: $('#wbasedato').val()
	}
	, function(data) {
		
		var arrayCcoAC = data;
		// console.log(arrayCcoAC);
		var ccostos	= new Array();
		var index		= -1;
						
		for (var codCostos in arrayCcoAC)
		{
			index++;
			ccostos[index] = {};
			ccostos[index].value  = codCostos;
			ccostos[index].label  = codCostos+'-'+arrayCcoAC[codCostos];
			ccostos[index].nombre = arrayCcoAC[codCostos];
			ccostos[index].codigo = codCostos;
		}
		
		index++;
		ccostos[index] = {};
		ccostos[index].value  = "";
		ccostos[index].label  = "* - Todos los centros de costos";
		ccostos[index].nombre = "";
		ccostos[index].codigo = "";
		
		$( "#buscarCco" ).autocomplete({
			
			minLength: 	0,
			source: 	ccostos,
			select: 	function( event, ui ){					
				$( "#buscarCco" ).val(ui.item.nombre);
				$( "#buscarCco" ).attr('valor', ui.item.value);
				$( "#buscarCco" ).attr('label', ui.item.label);
				seleccionCcostos();
				return false;
			}
			
		});

		// Asignar el Array al campo ubicado en el Modal que se activa cuando el usuario va a cambiar el c. de costos
		$( "#txtcentrocco" ).autocomplete({
			
			minLength: 	0,
			source: 	ccostos,
			select: 	function( event, ui ){					
				$( "#txtcentrocco" ).val(ui.item.nombre);
				$( "#txtcentrocco" ).attr('valor', ui.item.value);
				$( "#txtcentrocco" ).attr('label', ui.item.label);
				$( "#txtcentrocco" ).attr("codigo",ui.item.codigo);                
                    SeleccionNuecentro(ui.item.codigo);
				return false;
			}
			
		});

	},'json');
	
}


function seleccionCcostos()
{
	var codEmp= $("#buscarCco").attr('valor');
	contratoex = $('#arr_cont').val();
	
	$("#wcco").val(codEmp);
	$("#buscarCco").val('');
	
	$.post("../procesos/consultaorganigrama.php",
	{
		consultaAjax 	: '',
		inicial			: 'no',
		accion			: 'inicioprograma',
		wemp_pmla		: $('#wemp_pmla').val(),
		wbasedato		: $('#wbasedato').val(),
		wcco			: codEmp
	}
	, function(data) {
		nombres = data.arrayNombres;
		cco = data.arrayCC;
		
		jQuery.ajaxSetup({
			cache: false
		});

		jQuery('#contentpanel').layout(
		{
			center__paneSelector: "#centerpanel"
			, center__onresize: function () {
				if (orgdiagram != null && orgdiagram2 != null) {
					ResizePlaceholder();
					jQuery("#orgdiagram").orgDiagram("update", primitives.common.UpdateMode.Refresh);
					jQuery("#orgdiagram2").orgDiagram("update", primitives.common.UpdateMode.Refresh);
				}
			}
		});

		ResizePlaceholder();
		
		if(data.error == 1)
		{
			$( "#alertMsj" ).html("El centro de costos no tiene personal asignado");
			
			$( "#alertMsj" ).dialog({
				modal: true,
				width:350,
				position: {my: "middle",
						   at: "middle",
						   of: "#contentpanel"},
				buttons: {
				OK: function() {
				 $( "#alertMsj" ).dialog( "close" );
				}
			  }
			});
		}
		
		orgdiagram = SetupWidget(jQuery("#orgdiagram"), "orgdiagram",data.arrayRelacion,data.arrayCedulas,data.arrayFotos,data.arrayCargos,data.arrayCco,data.arrayContrato,"relacionado",contratoex);
		orgdiagram2 = SetupWidget(jQuery("#orgdiagram2"), "orgdiagram2",data.arrayRelacion,data.arrayCedulas,data.arrayFotos,data.arrayCargos,data.arrayCco,data.arrayContrato,"sinRelacion",contratoex);
		
		jQuery("#orgdiagram").orgDiagram("update", primitives.orgdiagram.UpdateMode.Refresh);
		jQuery("#orgdiagram2").orgDiagram("update", primitives.orgdiagram.UpdateMode.Refresh);
		
		// cargarAutocompleteEmpleados($('#wcco').val());
		cargarAutocompleteEmpleados(codEmp);
		cargarAutocompleteCco();
	
	},'json');
	
	
	
}


function cargarAutocompleteEmpleados(cco)
{
	$.post("../procesos/consultaorganigrama.php",
	{
		consultaAjax 	: '',
		accion			: 'ConsultarEmpleadosAutocomplete',
		wemp_pmla		: $('#wemp_pmla').val(),
		wbasedato		: $('#wbasedato').val(),
		wcco			: cco
	}
	, function(data) {
		
		var arrayNombres = data;
		// console.log(arrayNombres);
		var nombres	= new Array();
		var index		= -1;
		
		for (var codEmp in arrayNombres)
		{
			index++;
			nombres[index] = {};
			nombres[index].value  = codEmp;
			nombres[index].label  = arrayNombres[codEmp];
			nombres[index].nombre = arrayNombres[codEmp];
		}
		
		
		$( "#buscarEmpleado" ).autocomplete({
			minLength: 	0,
			source: 	nombres,
			select: 	function( event, ui ){					
				$( "#buscarEmpleado" ).val(ui.item.nombre);
				$( "#buscarEmpleado" ).attr('valor', ui.item.value);
				$( "#buscarEmpleado" ).attr('label', ui.item.label);
				seleccionEmpleado();
				return false;
			}
		});	
	},'json');
	
}

function seleccionEmpleado()
{
	var codEmp= $("#buscarEmpleado").attr('valor');
	
	jQuery("#orgdiagram").orgDiagram({ cursorItem: codEmp });
    jQuery("#orgdiagram").orgDiagram("update", primitives.orgdiagram.UpdateMode.Refresh);
	
	jQuery("#orgdiagram2").orgDiagram({ cursorItem: codEmp });
    jQuery("#orgdiagram2").orgDiagram("update", primitives.orgdiagram.UpdateMode.Refresh);
	
	$("#buscarEmpleado").val('');
}

function SeleccionNuecentro(vcodigo)
{
  if (vcodigo !== '' && $("#txtnuejefe").val() =='')
  {

	  $("#wcencos").val(vcodigo);
	  
	  $.post("../procesos/consultaorganigrama.php",
	      {
			  consultaAjax:   true,
			  accion      :  'ConsultarJefe',
			  wemp_pmla   :   $('#wemp_pmla').val(),		 
			  wcentrocos  :   vcodigo
		  }, function(respuesta){
		      var nuejefe = respuesta.split('|')
		      $("#txtnuejefe").val(nuejefe[1]);
		      $("#wjefe").val(nuejefe[0]);
		  });

  }
}

</script>

<style type="text/css">
    .displ{
        display:block;
    }
    .borderDiv {
        border: 2px solid #2A5DB0;
        padding: 5px;
    }
    .resalto{
        font-weight:bold;
    }
    .parrafo1{
        color: #676767;
        font-family: verdana;
    }
	.ui-autocomplete{
		max-width: 	300px;
		max-height: 150px;
		overflow-y: auto;
		overflow-x: hidden;
		font-size: 	9pt;
	}
	.titulo1{
        background-color: #0A3385;
        font-family: verdana;
		color: white;
		margin-left:157px; 
		float: left; 
		width: 1046px; 
		text-align: center;
		border-radius: 3px;
    }
	.titulo2{
        background-color: #0A3385;
        font-family: verdana;
		color: white;
		float: right; 
		width: 196px; 
		text-align: center;
		border-radius: 3px;
    }
    .button{
        color:#2471A3;
        font-weight: bold;
        font-size: 12,75pt;
        width: 100px; height: 30px;
		background: rgb(240,248,252);
		background: -moz-linear-gradient(top,  rgba(240,248,252,1) 0%, rgba(236,246,254,1) 50%, rgba(219,238,251,1) 51%, rgba(220,237,254,1) 100%); 
		background: -webkit-linear-gradient(top,  rgba(240,248,252,1) 0%,rgba(236,246,254,1) 50%,rgba(219,238,251,1) 51%,rgba(220,237,254,1) 100%); 
		background: linear-gradient(to bottom,  rgba(240,248,252,1) 0%,rgba(236,246,254,1) 50%,rgba(219,238,251,1) 51%,rgba(220,237,254,1) 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f8fc', endColorstr='#dcedfe',GradientType=0 );
		border: 1px solid #ccc; 
        border-radius: 8px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
    }

    .button:hover {background-color: #3e8e41}

	.button:active {
	  background-color: #3e8e41;
	  box-shadow: 0 5px #666;
	  transform: translateY(4px);
}
</style>

<body>
<input type="hidden" id="wemp_pmla" value="<?=$wemp_pmla?>">
<input type="hidden" id="wbasedato" value="<?=$wbasedato?>">
<input type="hidden" id="wcco"      value="<?=$wcco?>">
<input type="hidden" id="wcencos"   name="wcencos">
<input type="hidden" id="wjefe"     name="wjefe">
<input type="hidden" id="wcontexid" name="wcontexid">
<?php
$wactualiz = "2017-09-04";
encabezado("ORGANIGRAMA",$wactualiz, "clinica");
echo $html_cco;
modalesDialog();
$arr_cen   = consultarCentros($wbasedato,$conex,$wemp_pmla);
$arr_jefe  = consultarlisJefes($wbasedato,$conex,$wemp_pmla);
$arr_cont  = consultarContratos($wbasedato,$conex,$wemp_pmla);
?>
	<table align='center' style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
		<tr class='encabezadoTabla'>
			<td align='center' colspan='2'>Consultar organigrama </td>
		</tr>
		<tr>
			<td class='fila1'>Centro de costos:&nbsp;&nbsp;</td>
			<td class='fila1'><input id='buscarCco' type='text' placeholder='Centro de costos' style='border-radius: 4px;border:1px solid #AFAFAF;'></td>
		</tr>
	</table>

<br>
		
	<span style='font-family: verdana;font-weight:bold;font-size: 10pt;margin-left:157px;'>
		Buscar empleado:&nbsp;&nbsp;</b><input id='buscarEmpleado' type='text' placeholder='Nombre del empleado' style='border-radius: 4px;border:1px solid #AFAFAF;'>
	</span>
	
	<div id="contentpanel" style="padding: 0px;width:1400px;height:600px" >
		<div id="centerpanel" style="overflow: hidden; padding: 0px; margin: 0px; border: 0px;">
			<span class="titulo1">Empleados relacionados</span>
			<div id="orgdiagram" style="position: absolute; overflow: hidden; left: 0px; padding: 0px; margin: 0px; border-style: dotted; border-color: navy; border-width: 1px;"></div>
			<span class="titulo2">Empleados sin relaci&oacute;n</span>
			<div id="orgdiagram2" style="position: absolute; overflow: hidden; left: 0px; padding: 0px; margin: 0px; border-style: dotted; border-color: navy; border-width: 1px;"><table><tr><td class='fila1'>Nro Empleados sin Relacion &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="txtempsin" id="txtempsin" size=4></td></tr></table></div>
		</div>
	</div>	

	<table align="right">	
		<tr>
			<td><input type="button" value="Restaurar" onClick="recargar();"></td>
			<td><input type="button" value="Guardar" onClick="guardar();"></td>
		</tr>
	</table>
	
	<table align='center'>
		<tr><td align='center' colspan=9><input type='button' value='Cerrar Ventana' onclick='cerrarVentana();'></td></tr>
	</table>
    <div id='pancambiarcentro' name='pancambiarcentro' align='center' style='display:none;' title'Centro de Costos'>
    <br>
    <table align='center'>
	    <tr><td align=center class=encabezadoTabla colspan=4 ><font><b>Cambiar Centro de Costos</b></font></td></tr>
	    <tr class='fila1'><td width='300' height='40'><b>Centro de Costos Anterior</b></td><td width='300'><input type='text' id='txtcentroant' name='txtcentroant' size='50px' readonly></td></tr>
	    <tr class='fila1'><td width='300' height='40'><b>Nuevo Centro de Costos</b></td><td width='300'><input type='text' id='txtcentrocco' name='txtcentrocco' size='50px' ></td></tr>
	    <tr class='fila1'><td width='300' height='40'><b>Nuevo Jefe Inmediato</b></td><td width='300'><input type='text' id='txtnuejefe' name='txtnuejefe' size='50px'></td></tr>
	    <tr class='fila1'>
	    <td align='center' colspan=2><input type='button' class='button' id='btngrabarcen' name='btngrabarcen' value='Grabar' onclick='GrabarCentroCostos();'>
	    <input type='button' id='btnregresarcen' name='btnregresarcen' class='button' value='Regresar' onclick='$("#pancambiarcentro").dialog("close");'></td>
	    </tr>
    </table>
    <br>
    <legend><font size="3">El Jefe diligenciado en blanco, dejara el empleado sin Relacion</font></legend>
    </div>
    <div id='pancambiaremail' name='pancambiaremail' class='pancambiaremail' align='center' style='display:none;'>
    <br>
    <table>
        <tr><td align=center class=encabezadoTabla colspan=4><font size=2><b>Diligenciar Email</b></font></td></tr>
        <br>
	    <tr class='fila1'><td width='300' height='40'><b>Email</b></td><td width='300'><input type='text' id='txtemail' name='txtemail' size='50px'></td></tr> 
	    <tr class='fila1'>
	    <td align='center' colspan=2><input type='button' class='button' id='btngrabaremail' name='btngrabaremail' value='Grabar' onclick='GrabarEmail();'>
	    <input type='button' id='btnregresarema' name='btnregresarema' class='button' value='Regresar' onclick='$("#pancambiaremail").dialog("close");'></td>
	    </tr>
    </table>
    </div>
    <div id='panevaluacion' name='panevaluacion' class='panevaluacion' align='center' style='display:none;'>
    <br><br>
    <table>
        <thead>
    	<p><tr ><td align=center colspan='5'><font><b>Listado Evaluaciones</b></font></td></tr></p><br>
    	</thead>
    </table>
    </div>
</body>
<input type="hidden" name="arr_cen" id="arr_cen" value='<?=base64_encode(serialize($arr_cen))?>'>
<input type="hidden" name="arr_jefe" id="arr_jefe" value='<?=json_encode($arr_jefe)?>'>
<input type="hidden" name="arr_cont" id="arr_cont" value='<?=$arr_cont?>'>
</html>