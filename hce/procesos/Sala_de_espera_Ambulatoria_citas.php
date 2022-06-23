<html>
<head>
  <title>SALA DE ESPERA AMBULATORIA CON CITA</title>
  
  <script type="text/javascript">

  
    function validarConsulta(i)
	   {
	    var cont1 = 1;
		
		debugger;
		
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
				var parametros = "consultaAjax=activarcur&wemp_pmla="+document.forms.sala.wemp_pmla.value+"&whce="+document.forms.sala.whce.value+"&whis="+his+"&wing="+ing+"&wservicio="+ser+"&wusuario="+document.forms.sala.wusuario.value+"&irhce="+irhce+"&wesp="+document.getElementById("wesp").value;
				
			try{
				//$.blockUI({ message: $('#msjEspere') });
				var ajax = nuevoAjax();
				
				ajax.open("POST", "Sala_de_espera_Ambulatoria_citas.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);
				 
				//ajax.onreadystatechange=function() 
				//{ 
					if (ajax.readyState==4 && ajax.status==200)
					{ 
						//if(ajax.responseText!="ok")
							//alert(ajax.responseText);
					} 
				//}
				//if ( !estaEnProceso(ajax) ) {
				//    ajax.send(null);
				//}
				}catch(e){    }

				//LLamado a la historia HCE
				
				url="HCE_iFrames.php?empresa="+document.forms.sala.whce.value+"&whisa="+his+"&winga="+ing+"&wservicio=*"+"&wcedula="+doc+"&wtipodoc="+tid+"&wdbmhos="+document.forms.sala.wbasedato.value+"&wemp_pmla="+document.forms.sala.wemp_pmla.value;
				//open(url,'','top=50,left=100,width=960,height=940') ;
				//open(url,'',resizable='yes') ;
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
		    var parametros = "consultaAjax=conducta&wemp_pmla="+document.forms.sala.wemp_pmla.value+"&whce="+document.forms.sala.whce.value+"&whis="+his+"&wing="+ing+"&wusuario="+document.forms.sala.wusuario.value+"&wconducta="+document.getElementById("conducta"+i).value+"&wesp="+document.getElementById("wesp").value;
			try
			  {
				var ajax = nuevoAjax();
			
				ajax.open("POST", "Sala_de_espera_Ambulatoria_citas.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);
				if (ajax.readyState==4 && ajax.status==200)
					{
					 //if(ajax.responseText!="ok")
					 //	alert(ajax.responseText);
					} 
			  }catch(e){    }
			  
			document.getElementById('wirhce'+i).checked=false;
			
			enter();
		   }	
	  }

	function enter()
	  {
	   document.forms.sala.submit();
	  }
	
	function cerrarVentana()
	  {
       window.close();		  
      }
</script>
  
</head>
<body>
<?php
$wemp_pmla = $_REQUEST['wemp_pmla'];
include_once("conex.php");
  /***********************************************
   *    SALA DE ESPERA AMBULATORIA CON CITA      *
   *   		  CONEX, FREE => OK		             *
   ***********************************************/
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  
  
  include_once("root/magenta.php");
  include_once("root/comun.php");
  
  $conex = obtenerConexionBD("matrix");
  
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));
	                                                           
/*********************************************************
*          SALA DE ESPERA AMBULATORIA CON CITA           *
*     				CONEX, FREE => OK				     *
*********************************************************/
//==================================================================================================================================
//PROGRAMA                   : Sala_de_espera_Ambulatoria_citas.php
//AUTOR                      : Juan Carlos Hernández M.
//$wautor                    : "Juan C. Hernandez M. "
//FECHA CREACION             : Abril 1 de 2014
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz = "2022-03-16"; 
//DESCRIPCION
//==========================================================================================================================================\\
//==========================================================================================================================================\\
//     Programa usado para la atencion de los pacientes en Unidades Ambulatarias.                                                           \\
//     ** Funcionamiento General:                                                                                                           \\
//     En esta pantalla se muestran todos los pacientes que se hallan ingresado a Matrix por el programa de asignacion de medico y que      \\
//     tenga cita para el día de actual, también trae los pacientes de Citas de fisioterapeuta.                                             \\
//     Es decir trae los pacientes que tengan cita tanto para medico fisiatra, como para fisioterapeutas, mostrandole a cada profesional    \\
//     solo los pacientes correspondientes a el.                                                                                            \\ 
//         * Tablas: citasfi_000009  : Citas de médicos fisiatras                                                                           \\
//                   citasfi_000017  : Citas de fisioterapeutas                                                                             \\
//                   citasfi_000010  : Maestro de médicos fisiatras                                                                         \\
//                   citasfi_000015  : Maestro de fisioterapeutas 
//==========================================================================================================================================\\
//16/03/2022 - Brigith Lagares: Se realiza estadarización del wemp_pmla.
//==========================================================================================================================================\\
//2016-04-19 Verónica Arismendy
//           Se modifica la consulta para agregar validación de estado activo del fisiatra o fisioterapeuta.
//==========================================================================================================================================\\
//2016-04-13 Verónica Arismendy
//           Se modifica la consulta porque no se estaban teniendno en cuenta las citas de la tabla citasfi_000017 
//==========================================================================================================================================\\
//2016-03-11 Verónica Arismendy
//           Se modifica la consulta por error que se estaba presentando al quitar el group by por todos los campos, se crea una temporal para
//           guardar los datos de los ingresos máximos de cada usuario con cita y luego consultar la demás información
//==========================================================================================================================================\\
//2016-03-03 Verónica Arismendy                                                                                                          	\\
//        	 Se modifica la consulta para que tenga en cuenta sólo el último ingreso del paciente, ya que en la lista aparecía varias veces \\
//           con diferente ingreso.  
//           Nota: si tiene ingresos activos con diferentes empresas igual se toma el mayor ingreso y la empresa que tenga ese registro.    \\                                                                                    	            \\
//==========================================================================================================================================\\
//Abril 7 de 2014                                                                                                                       	\\
//==========================================================================================================================================\\
//Se pone en modo de prueba para que sea validado por el personal de fisiatria                                               				\\
//==========================================================================================================================================\\
	               
				  
//=========================================================================================================================================================================================
//=========================================================================================================================================================================================
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
      
	  
function ponerConsulta($whce, $whis, $wing, $wusuario, $irhce, $wesp)
   {
    //$conexion = obtenerConexionBD("matrix");
	global $conex;
	$wfecha=date("Y-m-d");   
    $whora = (string)date("H:i:s");
    
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
		
		if($res)
			return "ok";
		else
			return "No se pudo realizar la asignación. \n Error: ".$res;
	   }
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
		 
		 //On 
		 //echo $q."<br>";
		 
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
	
function mostrarPacientesPropios($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, $i, $wservicio, $wesp, $wcitas)
 {
  global $conex;
 
 $wfecha=date("Y-m-d");
 
 $wespecialidad = "";

  //Obtengo la especialidad del profesional
  $q = " SELECT medesp "
      ."   FROM ".$wbasedato."_000048 "
	  ."  WHERE meduma = '".$wusuario."'"
	  ."    AND medest = 'on' ";
     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
     $num = mysql_num_rows($res);

	if ($num > 0)
	{
      $row = mysql_fetch_array($res);
	  $stresp = explode("-",$row[0]);
	  $wespecialidad = trim($stresp[0]);
	}
 
  		
	//2016-03-15 Verónica Arismendy Se modifica la forma de consultar para corregir error y lentitud en la consulta			
	$qCreateTable = "CREATE TEMPORARY TABLE IF NOT EXISTS max_ingreso_historia(historia VARCHAR(80), max_ingreso VARCHAR(80), max_fecha DATE)";
	$resTemporary = mysql_query($qCreateTable,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qCreateTable." - ".mysql_error());
		
	$sqlInsertTemporal = "";
	$sqlConsultaFinal = "";
	
	// Se consulta si el usuario logueado es un(a) Fisiatra.	
	$sqlValidFisiatra = "SELECT count(0) as existe
						FROM ".$wcitas."_000010 
						WHERE codmat = '".$wusuario."'
						AND Activo = 'A'
	";
	$result = mysql_query($sqlValidFisiatra, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlValidFisiatra."):</b><br>".mysql_error());				
	$row = mysql_fetch_assoc($result);		
		
		
	if(isset($row["existe"]) && $row["existe"] >= 1){
		$sqlInsertTemporal = "SELECT mtrhis historia, MAX(CAST(mtring AS UNSIGNED)) AS max_ingreso, MAX(D.fecha_data) as max_fecha
							FROM root_000036 A, root_000037 B, ".$wcitas."_000009 C, ".$whce."_000022 D, ".$wcitas."_000010, ".$wbasedato."_000016 
							WHERE oriori = '".$wemp_pmla."' 
								AND oriced = pacced 
								AND oritid = pactid 
								AND pacced = Cedula 
								AND orihis = mtrhis 
								AND C.fecha = '".$wfecha."' 
								AND cod_equ = codigo 
								AND codmat = '".$wusuario."'
								AND mtrcci = '".$wcco."' 
								AND mtrhis = inghis 
								AND mtring = inging 
							GROUP BY 1
		";			
			
		$sqlConsultaFinal = "SELECT mtrhis, mtring, pacno1, pacno2, pacap1, pacap2, C.fecha, pactid, pacced, mtrcur, mtrcon
							, concat(left(C.hi,2),':',RIGHT(C.hi,2)) AS hora, D.fecha_data, ingnre, mtrftc
							FROM root_000036 A, root_000037 B, ".$wcitas."_000009 C, ".$whce."_000022 D, ".$wcitas."_000010, ".$wbasedato."_000016 , max_ingreso_historia mi
							WHERE oriori = '".$wemp_pmla."' 
								AND oriced = pacced 
								AND oritid = pactid 
								AND pacced = Cedula 
								AND orihis = mtrhis 
								AND C.fecha = '".$wfecha."' 
								AND cod_equ = codigo 
								AND codmat = '".$wusuario."'
								AND mtrcci = '".$wcco."'
								AND mtrhis = inghis 
								AND mtring = inging 
								AND mtrhis  = mi.historia
								AND mtring = mi.max_ingreso
								AND D.fecha_data = mi.max_fecha
							GROUP BY 1
							ORDER BY 7, 12, 1, 2 
		";
			
	}else{
		// Se consulta si el usuario logueado es un(a) fisioterapeuta.
		$sqlValidTerapeuta = "SELECT count(0) as existe
							FROM ".$wcitas."_000015 
							WHERE codmat = '".$wusuario."'
							AND Estado = 'on'
		";
		$result = mysql_query($sqlValidTerapeuta, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlValidTerapeuta."):</b><br>".mysql_error());				
		$row = mysql_fetch_assoc($result);		
			
		if(isset($row["existe"]) && $row["existe"] >= 1){
			$sqlInsertTemporal = "SELECT mtrhis historia, MAX(CAST(mtring AS UNSIGNED)) AS max_ingreso, MAX(D.fecha_data) as max_fecha
								FROM root_000036 A, root_000037 B, ".$wcitas."_000017 C, ".$whce."_000022 D, ".$wcitas."_000015, ".$wbasedato."_000016 
								WHERE oriori = '".$wemp_pmla."'
									AND oriced = pacced 
									AND oritid = pactid 
									AND pacced = Cedula 
									AND orihis = mtrhis 
									AND C.fecha =  '".$wfecha."'
									AND terapeuta = codigo 
									AND codmat = '".$wusuario."'
									AND mtrcci = '".$wcco."'
									AND mtrhis = inghis 
									AND mtring = inging 
								GROUP BY 1	
			";
				
			$sqlConsultaFinal = "SELECT mtrhis, mtring, pacno1, pacno2, pacap1, pacap2, C.fecha, pactid, pacced, mtrcur, mtrcon
								, concat(left(C.hora_inicial,2),':',RIGHT(C.hora_inicial,2)) AS hora, D.fecha_data, ingnre, mtrftc 
								FROM root_000036 A, root_000037 B, ".$wcitas."_000017 C, ".$whce."_000022 D, ".$wcitas."_000015, ".$wbasedato."_000016, max_ingreso_historia mi
								WHERE oriori = '".$wemp_pmla."'
									AND oriced = pacced 
									AND oritid = pactid 
									AND pacced = Cedula 
									AND orihis = mtrhis 
									AND C.fecha =  '".$wfecha."' 
									AND terapeuta = codigo 
									AND codmat = '".$wusuario."'
									AND mtrcci = '".$wcco."'
									AND mtrhis = inghis 
									AND mtring = inging 
									AND mtrhis  = mi.historia
									AND mtring = mi.max_ingreso
									AND D.fecha_data = mi.max_fecha
								GROUP BY 1
								ORDER BY 7, 12, 1, 2 
			";
		}			
	}		
		
	if($sqlInsertTemporal != "" && $sqlConsultaFinal != ""){
		$qTemporary = " INSERT INTO 
						max_ingreso_historia 
						" .$sqlInsertTemporal. "
		";	
			
		//Se insertan los datos en la temporal
		$resInsert = mysql_query($qTemporary,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qTemporary." - ".mysql_error());
			
		//Se consultan los datos finales
		$res = mysql_query($sqlConsultaFinal,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$sqlConsultaFinal." - ".mysql_error());
		$num = mysql_num_rows($res);
			
	}else{
		$num = 0;
	}
			
	//Se elimina la tabla luego de tener el resultado para que cuando vuelvan a consultar se cree una nueva
	$qDropTemporary = "DROP TABLE IF EXISTS max_ingreso_historia";
	$resdDropTemporary = mysql_query($qDropTemporary,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qDropTemporary." - ".mysql_error());
	
			 
	echo "<table>";
	echo "<tr class=encabezadoTabla>";
	//echo "<th>Semaforo</th>";
	echo "<th>Fecha del Ingreso</th>";
	echo "<th>Fecha Cita</th>";
	echo "<th>Hora Cita</th>";
	echo "<th>Historia-Ingreso</th>";
	echo "<th>Doc. Identidad</th>";
	echo "<th>Paciente</th>";
	echo "<th>Responsable</th>";
	echo "<th>Ir a Historía</th>";
	echo "<th>Conducta a Seguir</th>";
	echo "<th>Afinidad</th>";
	echo "</tr>";
  
	if ($num > 0)
	{
		for($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);  	  
				  
			if (is_integer($i/2))
				$wclass="fila1";
			else
				$wclass="fila2";
			  
			$whis = $row[0];
			$wing = $row[1];
			$wpac = $row[2]." ".$row[3]." ".$row[4]." ".$row[5];
			$wfci = $row[6];     //Fecha de la cita
			$wtid = $row[7];		  
			$wdpa = $row[8];     //Documento de Identidad
			$wcur = $row[9];     //Indicador de si esta en Consulta
			$wcon = $row[10];    //Conducta
			$whci = $row[11];    //Hora de la cita
			$wfin = $row[12];    //Fecha del Ingreso
			$wres = $row[13];
			$wftc = $row[14];    //Fecha terminacion consulta
			  
			$wcolor=convenciones($wfin, $whin);
									
			echo "<tr class=".$wclass.">";
			//echo "<td align=center bgcolor=".$wcolor.">&nbsp</td>";
			echo "<td align=center><b>   ".$wfin."</b></td>";
			echo "<td align=center>   ".$wfci."</td>";
			echo "<td align=center>   ".$whci."</td>";
			echo "<td align=center><b>".$whis." - ".$wing."</b></td>";
			echo "<td align=center><b>".$wdpa."</b></td>";
			echo "<td align=left  ><b>".$wpac."</b></td>";
			echo "<td align=left  ><b>".$wres."</b></td>";
			  
			$irhce="off";  //Permite ingresar a la hce sin dar clic sobro el radio button de ir a hce
			if ($wcur == "on")
				echo "<td align=center><input type='radio' name='wirhce$i' id='wirhce$i' onclick='activarConsulta($whis, $wing,\"$wservicio\", \"$wdpa\", \"$wtid\", $i)' checked></td>";
			else 
				echo "<td align=center><input type='radio' name='wirhce$i' id='wirhce$i' onclick='activarConsulta($whis, $wing,\"$wservicio\", \"$wdpa\", \"$wtid\", $i, \"$irhce\")'>";
			  
			echo "<td align=center><select id='conducta$i' name='wconducta$i' onchange='colocarConducta($whis, $wing, $i, \"$irhce\")'>";

			//On
			echo $wftc." fecha actual: ".$wfecha."<br>";
			  
			  
			if (isset($wcon))                              //Si selecciono una opcion del dropdown
			{
				if ($wftc == $wfecha)
				{				
					$q = " SELECT condes "
						. "   FROM ".$whce."_000035 "
						. "  WHERE concod = '".$wcon."'"
						."     AND INSTR(conser,'".$wservicio."') > 0 ";
					$res2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row2 = mysql_fetch_array($res2);  	  
					 
					echo "<option selected value=$wcon>".$row2[0]."</option>";
				}	
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
			echo "<option value=''>&nbsp</option>";	 
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
			echo "</tr>";
		}	     
	}else{
		echo "NO HAY PACIENTES PENDIENTES DE ATENCION"; 
	}
   echo "</table>"; 
   }  	
   
   
function buscar_pacientes($wdocide)
 {
  global $conex;
  global $wemp_pmla;
  global $wfecha;
  global $wusuario;
  global $wcitas;
  global $whce;
  global $wcco;
  
 
  $wpaciente="";
  if (isset($wdocide) and trim($wdocide) != "")
     {
	  //Busco si el documento existe
	  $q = " SELECT count(*) "
	      ."   FROM root_000037 "
		  //."  WHERE oriori = '".$wemp_pmla."'"
		  ." WHERE oriced = '".$wdocide."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);  
	  if ($row[0] == 0)
	     {
		  $wpacok="off";
		  echo "<center><table><tr class=fila1><td>No Existe un paciente con este documento de identidad : ".$wdocide."</td></tr></table>";
		 }
		else
		  {
		   //Traigo el nombre del paciente
		   $q = " SELECT pacno1, pacno2, pacap1, pacap2 "
		  	   ."   FROM root_000036, root_000037 "
			   ."  WHERE oriced = '".$wdocide."'"
			   ."    AND oriced = pacced "
			   ."    AND oritid = pactid ";
		   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		   $row = mysql_fetch_array($res);  
		  
		   $wpaciente = $row[0]." ".$row[1]." ".$row[2]." ".$row[3];
		   $whispac = $row[4];
		   $wpacok="on";
		   
		   $wpaciente = $row[0]." ".$row[1]." ".$row[2]." ".$row[3]; 
		  }
	
      //Busco si existe una cita para este documento con el profesional correspondiente
	  $q = " SELECT count(*) "
		  ."   FROM ".$wcitas."_000009 C, ".$wcitas."_000010 "
		  ."  WHERE C.fecha   = '".$wfecha."'"        //Fecha de la citas
	      ."    AND cod_equ   = codigo "              //Codigo del medico, el cual esta en la tabla de la cita y en la tabla de 10 de medicos
	      ."    AND codmat    = '".$wusuario."'"      //Codigo Matrix del especialista de la salud, que debe existir en la tabla 000015
		  ."    AND cedula    = '".$wdocide."'"
		  ." UNION ALL "
	      ." SELECT count(*) "
		  ."   FROM ".$wcitas."_000017 C, ".$wcitas."_000015 "
		  ."  WHERE C.fecha   = '".$wfecha."'"      //Fecha de la cita
		  ."    AND terapeuta = codigo "            //Codigo del terapeuta, el cual esta en la tabla de la cita y en la tabla de 15 de terapeutas
		  ."    AND codmat    = '".$wusuario."'"    //Codigo Matrix del especialista de la salud, que debe existir en la tabla 000015
		  ."    AND cedula    = '".$wdocide."'"
		  ."  ORDER BY 1 desc ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $row = mysql_fetch_array($res); 

      if ($row[0] == 0 and $wpacok == "on")
	     echo "<center><table><tr class=fila1><td>El Paciente: ".$wpaciente." No tiene cita asignada</td></tr></table>";
		else
		  if ($wpacok == "on")
             echo "<center><table><tr class=fila1><td>El Paciente: ".$wpaciente." debe estar en el tablero superior</td></tr></table>"; 		
		  
      //Busco si tiene ingreso para este servicio
      $q = " SELECT count(*) "
	      ."   FROM root_000037, ".$whce."_000022 "
		  ."  WHERE oriori = '".$wemp_pmla."'"
		  ."    AND oriced = '".$wdocide."'"
		  ."    AND orihis = mtrhis "
		  ."    AND mtrcci = '".$wcco."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $row = mysql_fetch_array($res);
	  
	  if ($row[0] == 0 and $wpacok == "on")
	     echo "<center><table><tr class=fila1><td>El Paciente : ".$wpaciente." no Tiene un ingreso para este servicio</td></tr></table>";
	 }
}
   
   
   /*
function mostrarPacientesComunes($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, $k, $wservicio)
 {
  global $conex;
 
  $wgen = "on";
  $wped = "off";
  $wort = "off";
  
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
      {
       $wcomplementoquery = " AND mtrmed = '".$wusuario."' ";      
      }
      
	  if ($wped == "on" or $wort == "on" or $wurg != "on" )   //Indica que es Especialista
	     $wgen = "off";
		
	  if ($wmge == "on")
	     $wgen = "on";
     }	 
 
  if ($wgen=="on")            //Generales
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
			 /* $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, medno1, medno2, medap1, medap2 "
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
				  ."    AND medort  = 'on' "
				  ."    AND INSTR(conser,'".$wservicio."') > 0 "     //Que la conducta que tiene sea del servicio por el cual ingreso a la HCE
				  ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16 "
				  ."  UNION ALL " */    /*
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
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);
		 
  echo "<br><br>";		 
  
  echo "<table>";
  echo "<tr class='tituloPagina'>";
  echo "<td align=center bgcolor=C3D9FF colspan=9>PACIENTES ATENDIDOS Y ACTIVOS</td>";
  echo "</tr>";
  echo "<tr class=encabezadoTabla>";
  echo "<th>Semaforo</th>";
  echo "<th>Fecha de Ingreso</th>";
  echo "<th>Hora de Ingreso</th>";
  echo "<th>Historia</th>";
  echo "<th>Paciente</th>";
  echo "<th>Ir a Historía</th>";
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
		  $wafin=clienteMagenta($wdpa,$wtid,&$wtpa,&$wcolorpac);
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
   
   */
//=========================================================================================================================================================================================
//=========================================================================================================================================================================================

				   
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
	         
	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];
	         
	      if (strtoupper($row[0]) == "HCE")
	         $whce=$row[1];
     
	      $winstitucion=$row[2];   
         }  
		 
	  //Traigo la BD de las citas dependiendo de la variable $wcit, si esta variable esta setiada es porque
	  //debe buscar de que servicio son las citas porque de este depende el nombre de la BD
	  // * * *   C I T A S   * * *//
	  if (isset($wcit))
		 {
		  $q = " SELECT detval "
			  ."   FROM root_000050, root_000051 "
			  ."  WHERE empcod = '".$wemp_pmla."'"
			  ."    AND empest = 'on' "
			  ."    AND empcod = detemp "
			  ."    AND detdes = '".$wcit."'"
			  ."    AND detapl = 'citas'";
			

		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res); 
			  
		  if ($num > 0 )
			 {
			  $row = mysql_fetch_array($res);
			  $wcitas=$row[0];
			 }  				  
		 }
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
  
  encabezado("SALA DE ESPERA AMBULATORIA",$wactualiz, "clinica");
  
  //FORMA ================================================================
  echo "<form name='sala' action='' method=post>";
  
  /*
  //ACA TRAIGO LOS DESTINOS DIGITADOS EN LA TABLA DE MATRIX
  $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
      ."   FROM ".$wtabcco.", ".$wbasedato."_000011"
      ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
      ."    AND ccourg = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);
  
  $row = mysql_fetch_array($res);
     
  $wcco=$row[0];
  $wnomcco=$row[1];   
  */
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
  echo "<input type='HIDDEN' name='whce' value='".$whce."'>";
  echo "<input type='HIDDEN' name='wusuario' value='".$wusuario."'>";
  echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";
  echo "<input type='HIDDEN' name='wservicio' value='".$wservicio."'>";
  echo "<input type='HIDDEN' name='wcit' value='".$wcit."'>";
  
  
  //===============================================================================================================================================
  //Imprimo el nombre del Médico
  //===============================================================================================================================================
  $q = " SELECT medno1, medno2, medap1, medap2, espnom "
       ."  FROM ".$wbasedato."_000048, ".$wbasedato."_000044 "
	   ." WHERE meduma = '".$wusuario."'"
	   ."   AND medesp = espcod ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 

  echo "<p class='tituloPagina' align=center>".$row[4]." : ".$row[0]." ".$row[1]." ".$row[2]." ".$row[3]."</p>";
  echo "<br>";
  //===============================================================================================================================================
  
  
  //===============================================================================================================================================
  //C O N  V E N C I O N E S
  //===============================================================================================================================================
  /*
  echo "<HR align=center></hr>";  //Linea horizontal
  echo "<table border=1 align=right>";
  echo "<caption bgcolor=#ffcc66>Convenciones</caption>";
  echo "<tr><td colspan=3 bgcolor="."99FFCC"."><font size=1 color='"."000000"."'>&nbsp Menos de 20 minutos</font></td></tr>";           //Verde  
  echo "<tr><td colspan=3 bgcolor="."FFFF66"."><font size=1 color='"."000000"."'>&nbsp De 20 a 35 minutos</font></td></tr>";            //Amarillo
  echo "<tr><td colspan=3 bgcolor="."FFCC99 "."><font size=1 color='"."000000"."'>&nbsp Mas de 35 minutos</font></td></tr>";      //Rojo
  echo "</table>";	
  */
  //===============================================================================================================================================  
  

  
  
  echo "<center><table>";
  
  $q = " SELECT empdes, empmsa "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  //$wmeta_sist_altas=$row[1];  //Esta es la meta en tiempo promedio para las altas   
     
     
  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
  if(isset($consultaAjax))
    {
	switch($consultaAjax)
	  {
	    case 'activarcur':
		   echo ponerConsulta($whce, $whis, $wing, $wusuario, $irhce, $wesp);
		 break;
		case 'conducta':
		  {
		   echo ponerConducta($whce, $whis, $wing, $wconducta);
		  }
		 break; 
        default :
            break;
      }
    }
  
  $wesp = '0';
  if(isset($_GET['wesp']) && $_GET['wesp']!='')
	$wesp = $_GET['wesp'];

  mostrarPacientesPropios($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, $i, $wservicio, $wesp, $wcitas);
  //mostrarPacientesComunes($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, $i, $wservicio);

  
  //========================================================================================================
  //AREA PARA BUSQUEDAD DE PACIENTES QUE NO ESTEN EN LA LISTA 
  //========================================================================================================
  echo "<br><br><center><table border=1>";
  echo "<tr class=seccion1><td align=center><b>Buscar un paciente que no este en la lista</b></td></tr>";
  echo "<tr class=fila1><td>Digite Documento de Identidad: <input name='wdocide'></td></tr>";
  echo "<tr><td class=boton align='center'><input type='submit' value='Buscar'></td></tr>";
  echo "</table><br><br>";
  
  buscar_pacientes($wdocide);
  
  
  echo "<input type='HIDDEN' name='wesp' id='wesp' value='".$wesp."'>";

  echo "</form>";
  
  if (isset($wsup) and $wsup=="on")  //Es superusuario
     echo "<meta http-equiv='refresh' content='300;url=Sala_de_espera_Ambulatoria_citas.php?wemp_pmla=".$wemp_pmla."&wuser=".$wusuario."&user=".$user."&wcco=".$wcco."&wservicio=".$wservicio."&wesp=".$wesp."&wcit=".$wcit."'>";
	else 
       echo "<meta http-equiv='refresh' content='30;url=Sala_de_espera_Ambulatoria_citas.php?wemp_pmla=".$wemp_pmla."&wuser=".$wusuario."&user=".$user."&wcco=".$wcco."&wservicio=".$wservicio."&wesp=".$wesp."&wcit=".$wcit."'>";
	  
  echo "<br>";
  echo "<table>"; 
  echo "<tr class=boton><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";
}
unset($wdocide);

include_once("free.php");
?>
