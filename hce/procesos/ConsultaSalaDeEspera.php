<html>
<head>
  <title>SALA DE ESPERA</title>
  
 
  <!--========================================================== 
  <style type="text/css">
	* {
	   margin: 0;
	  }
	html, body 
	   {
	    height: 100%;
	    overflow: auto;
	   }
	.wrapper 
	  {
	   position: relative;
	   width: 100%;
	   height: 100%;
	   overflow: auto;
	  }
	.box 
	   {
	    <!-- border: 2px solid rgb(255, 215, 0);  -->
		<!-- padding: 5px;                        -->
		<!-- background: rgb(255, 255, 204) none repeat scroll 0% 0%; 
		background: none repeat scroll 0% 0%;
		position: fixed;
		border: 0px;
		<!-- z-index: 99;   
		width: 1000px;  
		height: 93px;  
		right: 40px;   
		top: 10px;
		left: 50px
	   }
	* html .box 
	   {
	    position: absolute;
	   }
   </style> -->
	
	<!--/// ======================================================== -->
  
  
  
  
  
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
	   
	   
    function activarConsulta(his, ing, doc, tid, i, irhce) 
	  {
	    
			wok=validarConsulta(i); 
		  
			if (wok==true)
			   {
				var parametros = "consultaAjax=activarcur&wemp_pmla="+document.forms.sala.wemp_pmla.value+"&whce="+document.forms.sala.whce.value+"&whis="+his+"&wing="+ing+"&wusuario="+document.forms.sala.wusuario.value+"&irhce="+irhce;
			try{
				//$.blockUI({ message: $('#msjEspere') });
				var ajax = nuevoAjax();
				
				ajax.open("POST", "Sala_de_espera.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);
				 
				//ajax.onreadystatechange=function() 
				//{ 
					if (ajax.readyState==4 && ajax.status==200)
					{ 
						//if(ajax.responseText!="ok")
						//	alert(ajax.responseText);
					} 
				//}
				//if ( !estaEnProceso(ajax) ) {
				//    ajax.send(null);
				//}
				}catch(e){    }

				//LLamado a la historia HCE
				//location.href="HCE_iFrames.php?empresa="+document.forms.sala.whce.value+"&whis="+his+"&wing="+ing+"&accion=M&ok=0&wcedula="+doc+"&wtipodoc="+tid;
				url="HCE_iFrames.php?empresa="+document.forms.sala.whce.value+"&whis="+his+"&wing="+ing+"&accion=F&ok=0&wcedula="+doc+"&wtipodoc="+tid;
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
		    var parametros = "consultaAjax=conducta&wemp_pmla="+document.forms.sala.wemp_pmla.value+"&whce="+document.forms.sala.whce.value+"&whis="+his+"&wing="+ing+"&wusuario="+document.forms.sala.wusuario.value+"&wconducta="+document.getElementById("conducta"+i).value;
			try
			  {
				var ajax = nuevoAjax();
			
				ajax.open("POST", "Sala_de_espera.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);
				if (ajax.readyState==4 && ajax.status==200)
					{
					// if(ajax.responseText!="ok")
					//	alert(ajax.responseText);
					} 
			  }catch(e){    }
			  
			document.getElementById('wirhce'+i).checked=false;
			
			enter();
		   }	
	  }

	
	function cerrarVentana()
	  {
       window.close()		  
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
include_once("conex.php");
  /***********************************************
   *              REGISTRO DE ALTAS              *
   *     		  CONEX, FREE => OK		         *
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
*               SALA DE ESPERA URGENCIAS                *
*     				CONEX, FREE => OK				   *
*********************************************************/
//==================================================================================================================================
//PROGRAMA                   : Sala_de_espera.php
//AUTOR                      : Juan Carlos Hernández M.
//$wautor="Juan C. Hernandez M. ";
//FECHA CREACION             : Abril 27 de 2011
//FECHA ULTIMA ACTUALIZACION :
	$wactualiz="(Noviembre 18 de 2021)"; 
//DESCRIPCION
//==========================================================================================================================================\\
//==========================================================================================================================================\\
//     Programa usado para la atencion de los pacientes en Urgencias.                                                                       \\
//     ** Funcionamiento General:                                                                                                           \\
//     En esta pantalla se muestran todos los pacientes que se hallan ingresado a Matrix por el programa de asignacion de medico que        \\
//     tienen los facturadores o auxiliares de admisiones de Urgencias.                                                                     \\
//     Asi: El facturador debe crear el ingreso en el Unix y luego debe ingresarlo a matrix con el programa de asignacion de medico o       \\
//     'agenda_urgencias.php' en el cual se le asigna el medico al paciente antes de ingresar al consultorio, por lo cual el médico solo    \\ 
//     podrá ver los pacientes que le asignaron a él en esta programa y los que se cencuentren en observacion o procedimientos y que sea    \\
//     factible que él lo pueda ver. Desde este programa también prodrá acceder a la HCE y luego de esto debe asignar una conducta a seguir \\
//     con el paciente.                                                                                                                     \\
//         * Tablas: hce_000022      : Medico tratante, consulta Urgencias, conducta a seguir                                               \\
//                   hce_000035      : Maestro de conductas indica si se da de alta o se indica muerte                                      \\
//                   movhos_000018   : Ubicación del paciente y se indica el alta definitiva.                                               \\
//==========================================================================================================================================\\
//==========================================================================================================================================\\
//==========================================================================================================================================\\
//Abril 27 de 2011                                                                                                                          \\
//==========================================================================================================================================\\
//Modificaciones:
//	
//Noviembre 18 de 2021 Daniel CB.  -Se realiza modificación en parametros 01 quemados.                                                                            	                                                                            \\
//==========================================================================================================================================\\
	               
				  
//=========================================================================================================================================================================================
//=========================================================================================================================================================================================
function convenciones($fecing, $hora, $wtiempo)
  {
    $wfecha=date("Y-m-d");   
    
    $a1=$hora;
	$a2=date("H:i:s");
	$a3=((integer)substr($a2,0,2)-(integer)substr($a1,0,2))*60 + ((integer)substr($a2,3,2)-(integer)substr($a1,3,2)) + ((integer)substr($a2,6,2)-(integer)substr($a1,6,2))/60;
	
	$wcolor="";
	
	$wfec1= strtotime($fecing." ".$hora);
	$wfec2= strtotime(date("Y-m-d H:i:s"));
	
	//Calculo la diferencia entre fecha con dias, horas y minutos
	//$wtiempo = date("d H:i",($wfec2-$wfec1)+strtotime("1970-01-01 00:00:00"));
	
	//Calculo la diferencia entre fecha con horas y minutos
	$wtiempo = date("H:i",($wfec2-$wfec1)+strtotime("1970-01-01 00:00:00"));
	
	//Aca configuro la presentacion de los colores segun el tiempo de respuesta
	if ($a3 > 35 or $wfecha != $fecing)                   //Mas de 35 Minutos
	   {
		$wcolor = "FFCC99";        //Rojo
		if ($wfecha != $fecing)
		   $wtiempo="<b>+</b>de 24 horas";
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
	
function mostrarPacientesSinMedico($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, &$i)
 {
  global $conex;
  global $whora;
 
  //Aca trae los pacientes que estan en Urgenciasen el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
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
	  ."    AND mtrmed  = '' "
	  ."    AND mtrcur != 'on' "
	  ."    AND mtrcon IN ('', 'NO APLICA') "
	  ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
	  ."  ORDER BY 7, 12 ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);
		 
  
  //echo "<div id='fixeddiv' style='position:absolute;z-index:99;width:228px;height:93px;right:10px;top:10px;padding:5px;background:#FFFFCC;border:2px solid #FFD700'>";
  echo "<div id='fixeddiv' style='position:absolute;z-index:99;width:920px;height:5px;left:370px;right:300px;top:1px;font-size: 14pt;text-align: center;padding:5px;background:#FFFFCC;border:0px solid #FFD700'>";
  echo "<b>PACIENTES EN ESPERA ** SIN ** ASIGNARLE MEDICO</b>";
  echo "</div>";

  echo "<br>";
  echo "<center><table>";
  echo "<tr><td align=left bgcolor=#ffffff colspan=3><font size=2><b>Hora: ".$whora."</b></font></td><td align=left bgcolor=#ffffff colspan=10><font size=2 text color=#CC0000><b>Cantidad de Registros: ".$num."</b></font></td></tr>";
  echo "<tr class=encabezadoTabla>";
  echo "<th>Semaforo<br>(Horas : Min.)</th>";
  echo "<th>Fecha de Ingreso Unix</th>";
  echo "<th>Hora de Ingreso Unix</th>";
  echo "<th>Historia</th>";
  echo "<th>Paciente</th>";
  //echo "<th>Conducta</th>";
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
		  $wfin = $row[6];     //Fecha de Ingreso
		  $wtid = $row[7];		  
		  $wdpa = $row[8];     
		  $wcur = $row[9];     //Indicador de si esta en Consulta
		  $wcon = $row[10];    //Conducta
		  $whin = $row[11];    //Hora de Ingreso
		  
		  $wcolor=convenciones($wfin, $whin, $wtiempo);
								
		  echo "<tr class=".$wclass.">";
		  echo "<td align=center bgcolor=".$wcolor.">".$wtiempo."</td>";
		  echo "<td align=center>   ".$wfin."</td>";
		  echo "<td align=center>   ".$whin."</td>";
		  echo "<td align=center><b>".$whis." - ".$wing."</b></td>";
		  echo "<td align=left  ><b>".$wpac."</b></td>";
		  
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
	  }
	 else
		echo "NO HAY PACIENTES PENDIENTES DE ATENCION"; 
   echo "</table>"; 
   }  	
   
   
function mostrarPacientesConMedicoEnEspera($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, &$i)
 {
  global $conex;
  global $whora;
 
  //Aca trae los pacientes que estan en Urgencias en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
  //y que no esten ni en proceso ni en alta                     
  $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, Mtrfam, pactid, pacced, mtrcur, mtrcon, Mtrham, medno1, medno2, medap1, medap2 "
	  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D , ".$wbasedato."_000048 E"
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
	  ."    AND mtrmed != '' "
	  ."    AND mtrcur != 'on' "
	  ."    AND mtrcon IN ('', 'NO APLICA') "
	  ."    AND mtrmed = meduma "
	  ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16 "
	  ."  ORDER BY 7, 12 ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);
		 
  
  echo "<div id='fixeddiv' style='position:absolute;z-index:99;width:920px;height:5px;left:370px;right:300px;top:1px;font-size: 14pt;text-align: center;padding:5px;background:#FFFFCC;border:0px solid #FFD700'>";
  echo "<b>PACIENTES CON MEDICO ASIGNADO EN ESPERA DE ATENCION</b>";
  echo "</div>";
  
  echo "<br>";
  echo "<center><table>";
  echo "<tr><td align=left bgcolor=#ffffff colspan=3><font size=2><b>Hora: ".$whora."</b></font></td><td align=left bgcolor=#ffffff colspan=10><font size=2 text color=#CC0000><b>Cantidad de Registros: ".$num."</b></font></td></tr>";
  echo "<tr class=encabezadoTabla>";
  echo "<th>Semaforo<br>(Horas : Min.)</th>";
  echo "<th>Fecha Asigna Medico</th>";
  echo "<th>Hora Asigna Medico</th>";
  echo "<th>Historia</th>";
  echo "<th>Paciente</th>";
  echo "<th>Medico</th>";
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
		  $wfin = $row[6];     //Fecha en que se asigno el medico
		  $wtid = $row[7];		  
		  $wdpa = $row[8];     
		  $wcur = $row[9];     //Indicador de si esta en Consulta
		  $wcon = $row[10];    //Conducta
		  $whin = $row[11];    //Hora en que se asigno el medico
		  $wmed = $row[12]." ".$row[13]." ".$row[14]." ".$row[15];    //Medico
		  
		  $wcolor=convenciones($wfin, $whin, $wtiempo);
								
		  echo "<tr class=".$wclass.">";
		  echo "<td align=center bgcolor=".$wcolor.">".$wtiempo."</td>";
		  echo "<td align=center>   ".$wfin."</td>";
		  echo "<td align=center>   ".$whin."</td>";
		  echo "<td align=center>   ".$whis." - ".$wing."</td>";
		  echo "<td align=left  >   ".$wpac."</td>";
		  echo "<td align=left  ><b>".$wmed."</b></td>";
		  
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
	  }
	 else
		echo "NO HAY PACIENTES PENDIENTES DE ATENCION"; 
   echo "</table>"; 
   }

   
function mostrarPacientesEnObservacion($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, $k)
 {
  global $conex;
  global $whora;
 
  //Aca trae los pacientes que estan en Urgenciasen el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
  //y que no esten ni en proceso ni en alta                       
  $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, Mtrftc, pactid, pacced, mtrcur, condes, Mtrhtc, medno1, medno2, medap1, medap2, medped, medort "
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
	  ."    AND mtrmed != '' "
	  //."  GROUP BY 1,2,3,4,5,6,7,8,9 "
	  ."  ORDER BY 7, 12 ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);
		
		
  echo "<div id='fixeddiv' style='position:absolute;z-index:99;width:920px;height:5px;left:370px;right:300px;top:1px;font-size: 14pt;text-align: center;padding:5px;background:#FFFFCC;border:0px solid #FFD700'>";
  echo "<b>PACIENTES ADULTOS, PEDIATRICOS Y ORTOPEDIA EN OBSERVACION U OTRO</b>";
  echo "</div>";
  
  echo "<br><br>";
  echo "<center><table>";
  echo "<tr><td align=left bgcolor=#ffffff colspan=3><font size=2><b>Hora: ".$whora."</b></font></td><td align=left bgcolor=#ffffff colspan=10><font size=2 text color=#CC0000><b>Cantidad de Registros: ".$num."</b></font></td></tr>";
  echo "<tr class=encabezadoTabla>";
  echo "<th>Semaforo<br>(Horas : Min.)</th>";
  echo "<th>Fecha Termino Consulta</th>";
  echo "<th>Hora Termino Consulta</th>";
  echo "<th>Historia</th>";
  echo "<th>Paciente</th>";
  echo "<th>Conducta</th>";
  echo "<th>Médico a Cargo</th>";
  echo "<th>Especialidad</th>";
  echo "<th>Afinidad</th>";
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
		  $wfin = $row[6];     //Fecha en que termino la consulta
		  $wtid = $row[7];		  
		  $wdpa = $row[8];     
		  $wcur = $row[9];     //Indicador de si esta en Consulta
		  $wcon = $row[10];    //Conducta
		  $whin = $row[11];    //Hora en que termino en consulta
		  $wmed = $row[12]." ".$row[13]." ".$row[14]." ".$row[15];    //Medico
		  $wped = $row[16];    //Pediatra
		  $wort = $row[17];    //Ortopedista
		  
		  $wesp="General";
		  if ($wped=="on")
		     $wesp="Pediatra";
		  if ($wort=="on")
		     $wesp="Ortopedista";
		  
		  $wcolor=convenciones($wfin, $whin, $wtiempo);
								
		  echo "<tr class=".$wclass.">";
		  echo "<td align=center bgcolor=".$wcolor.">".$wtiempo."</td>";
		  echo "<td align=center>".$wfin."</td>";
		  echo "<td align=center>".$whin."</td>";
		  echo "<td align=center>".$whis." - ".$wing."</td>";
		  echo "<td align=left  >".$wpac."</td>";
		  echo "<td align=left  ><b>".$wcon."</b></td>";
		  echo "<td align=left  >".$wmed."</td>";
		  echo "<td align=center><b>".$wesp."</b></td>";
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
	  }
	echo "</table>"; 
   }   
   
function mostrarPacientesSinDarledeAlta($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, &$i)
 {
  global $conex;
  global $whora;
 
  //Aca trae los pacientes que estan en Urgencias en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
  //y que no esten ni en proceso ni en alta
  $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, mtrfco, pactid, pacced, mtrcur, condes, mtrhco, medno1, medno2, medap1, medap2 "
	  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E, ".$wbasedato."_000048 F"
	  ."  WHERE  ubihis  = orihis "
	  ."    AND  ubiing  = oriing "
	  ."    AND  oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
	  ."    AND  oriced  = pacced "
	  ."    AND  oritid  = pactid "
	  ."    AND  ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
	  ."    AND  ubiald != 'on' "             //Que no este en Alta Definitiva
	  ."    AND  ubisac  = '".$wcco."'"       //Servicio Actual
	  ."    AND  ubihis  = mtrhis "
	  ."    AND  ubiing  = mtring "
	  ."    AND  mtrcur != 'on' "
	  ."    AND  mtrcon  = concod "
	  ."    AND (conalt  = 'on' "
	  ."     OR  conmue  = 'on') "
	  ."    AND  mtrmed  = meduma "
	  ."    AND  mtrmed != '' "
	  ."  UNION ALL "
	  ." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, mtrfco, pactid, pacced, mtrcur, condes, mtrhco, '', '', '', '' "
	  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E "
	  ."  WHERE  ubihis  = orihis "
	  ."    AND  ubiing  = oriing "
	  ."    AND  oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
	  ."    AND  oriced  = pacced "
	  ."    AND  oritid  = pactid "
	  ."    AND  ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
	  ."    AND  ubiald != 'on' "             //Que no este en Alta Definitiva
	  ."    AND  ubisac  = '".$wcco."'"       //Servicio Actual
	  ."    AND  ubihis  = mtrhis "
	  ."    AND  ubiing  = mtring "
	  ."    AND  mtrcur != 'on' "
	  ."    AND  mtrcon  = concod "
	  ."    AND (conalt  = 'on' "
	  ."     OR  conmue  = 'on') "
	  ."    AND  mtrmed  = '' "
	  ."  ORDER BY 7, 12 ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);
		 
  echo "<div id='fixeddiv' style='position:absolute;z-index:99;width:920px;height:5px;left:370px;right:300px;top:1px;font-size: 14pt;text-align: center;padding:5px;background:#FFFFCC;border:0px solid #FFD700'>";
  echo "<b>PACIENTES DADOS DE ALTA POR EL MEDICO Sin DARLE ALTA EN EL SISTEMA</b>";
  echo "</div>";
  
  
  echo "<br><br>";
  echo "<center><table>";
  echo "<tr><td align=left bgcolor=#ffffff colspan=3><font size=2><b>Hora: ".$whora."</b></font></td><td align=left bgcolor=#ffffff colspan=10><font size=2 text color=#CC0000><b>Cantidad de Registros: ".$num."</b></font></td></tr>";
  echo "<tr class=encabezadoTabla>";
  echo "<th>Semaforo<br>(Horas : Min.)</th>";
  echo "<th>Fecha Conducta</th>";
  echo "<th>Hora Conducta</th>";
  echo "<th>Historia</th>";
  echo "<th>Paciente</th>";
  echo "<th>Conducta</th>";
  echo "<th>Médico</th>";
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
		  $wfin = $row[6];     //Fecha de Ingreso
		  $wtid = $row[7];		  
		  $wdpa = $row[8];     
		  $wcur = $row[9];     //Indicador de si esta en Consulta
		  $wcon = $row[10];    //Conducta
		  $whin = $row[11];    //Hora de Ingreso
		  $wmed = $row[12]." ".$row[13]." ".$row[14]." ".$row[15];    //Medico
		  
		  $wcolor=convenciones($wfin, $whin, $wtiempo);
								
		  echo "<tr class=".$wclass.">";
		  echo "<td align=center bgcolor=".$wcolor.">".$wtiempo."</td>";
		  echo "<td align=center>   ".$wfin."</td>";
		  echo "<td align=center>   ".$whin."</td>";
		  echo "<td align=center><b>".$whis." - ".$wing."</b></td>";
		  echo "<td align=left  ><b>".$wpac."</b></td>";
		  echo "<td align=center><b>".$wcon."</b></td>";
		  echo "<td align=left><b>".$wmed."</b></td>";
		  
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
	  }
	 else
		echo "NO HAY PACIENTES PENDIENTES DE ATENCION"; 
   echo "</table>"; 
   }  	   
   
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
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
  
  //encabezado("CONSULTA ESTADO URGENCIAS",$wactualiz, "clinica");
  
  //On echo "<div class=wrapper>";
  
  //FORMA ================================================================
  echo "<form name='sala' action='ConsultaSalaDeEspera.php' method=post>";
  
  $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
      ."   FROM ".$wtabcco.", ".$wbasedato."_000011"
      ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
      ."    AND ccourg = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);
  
  $row = mysql_fetch_array($res);
     
  $wcco=$row[0];
  $wnomcco=$row[1];   
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
  echo "<input type='HIDDEN' name='whce' value='".$whce."'>";
  echo "<input type='HIDDEN' name='wusuario' value='".$wusuario."'>";
  echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";
  
  //===============================================================================================================================================
  //Imprimo el nombre del Médico
  //===============================================================================================================================================
  /*
  $q = " SELECT medno1, medno2, medap1, medap2 "
       ."  FROM ".$wbasedato."_000048 "
	   ." WHERE meduma = '".$wusuario."'";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 

  echo "<p class='tituloPagina' align=center>Dr(a). ".$row[0]." ".$row[1]." ".$row[2]." ".$row[3]."</p>";
  echo "<br>";
  */
  //===============================================================================================================================================
  
  
  //===============================================================================================================================================
  //C O N  V E N C I O N E S
  //===============================================================================================================================================
  switch ($wopcion)
	   {
	    case "mpsm":
	    case "mpcm":    //Mostrar Pacientes Sin Medico
		  echo "<HR align=center></hr>";  //Linea horizontal
		  echo "<table border=1 align=right>";
		  echo "<caption bgcolor=#ffcc66>Convenciones</caption>";
		  echo "<tr><td colspan=3 bgcolor="."99FFCC"."><font size=1 color='"."000000"."'>&nbsp Menos de 20 minutos</font></td></tr>";           //Verde  
		  echo "<tr><td colspan=3 bgcolor="."FFFF66"."><font size=1 color='"."000000"."'>&nbsp De 20 a 35 minutos</font></td></tr>";            //Amarillo
		  echo "<tr><td colspan=3 bgcolor="."FFCC99 "."><font size=1 color='"."000000"."'>&nbsp Mas de 35 minutos</font></td></tr>";      //Rojo
		  echo "</table>";	
		  
		  break;
		}  
  //===============================================================================================================================================  
  
  
  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	echo "<frameset rows=50%,* frameborder=1 framespacing=2 bordercolor='#FF0000'>";
       echo "<frameset cols=50%,* frameborder=1 framespacing=2 bordercolor='#FF0000'>";
	   echo "<frame src='ConsultaSalaDeEspera.php?wemp_pmla=".$wemp_pmla."'&wopcion=mpsm' name='mpsm' marginwidth=0 marginheiht=0>";
	   echo "<frame src='ConsultaSalaDeEspera.php?wemp_pmla=".$wemp_pmla."'&wopcion=mpcm' name='mpcm' marginwidth=0 marginheiht=0>";
	   echo "</frameset>";
	   echo "<frame src='ConsultaSalaDeEspera.php?wemp_pmla=".$wemp_pmla."'&wopcion=mpso' name='mpso' marginwidth=0 marginheiht=0>";
  echo "</frameset>";
  
  switch ($wopcion)
	   {
	    case "0":
		   $wtitulo=" MONITOR HCE URGENCIAS ";
	       encabezado($wtitulo, $wactualiz, 'clinica');
		   break;
	   case "mpsm":    //Mostrar Pacientes Sin Medico
		   mostrarPacientesSinMedico($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, $i);
		   break;
		case "mpcm":    //Mostrar Pacientes CON Medico              
           mostrarPacientesConMedicoEnEspera($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, $i);
		   break;
		case "mpso":    //Mostrar Pacientes en Sala de Observacion u Otro
           mostrarPacientesEnObservacion($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, $i);
		   break;
		case "mpsa":    //Mostrar Pacientes sin darle ALTA o MUERTE
           mostrarPacientesSinDarledeAlta($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, $i);
		   break;   
	   }	   
  
  echo "</form>";

  echo "<meta http-equiv='refresh' content='90;url=ConsultaSalaDeEspera.php?wemp_pmla=".$wemp_pmla."&wuser=".$wusuario."&user=".$user."&wopcion=".$wopcion."'>";
  
  echo "<table>"; 
  echo "<tr class=boton><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";
  
  echo "</div>";
  
  /*
  echo "<div class=box>";
  echo "<p><span>PACIENTES EN ESPERA ** SIN ** ASIGNARLE MEDICO</span></p>";
  echo "</div>";
  */
}
include_once("free.php");
?>

<script>
if(document.getElementById("fixeddiv")) { fixedMenuId = "fixeddiv"; var fixedMenu = {hasInner:typeof window.innerWidth == "number", hasElement:document.documentElement != null && document.documentElement.clientWidth, menu:document.getElementById ? document.getElementById(fixedMenuId) : document.all ? document.all[fixedMenuId] : document.layers[fixedMenuId]}; fixedMenu.computeShifts = function() { fixedMenu.shiftX = fixedMenu.hasInner ? pageXOffset : fixedMenu.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu.shiftX += fixedMenu.targetLeft > 0 ? fixedMenu.targetLeft : (fixedMenu.hasElement ? document.documentElement.clientWidth : fixedMenu.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu.targetRight - fixedMenu.menu.offsetWidth; fixedMenu.shiftY = fixedMenu.hasInner ? pageYOffset : fixedMenu.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu.shiftY += fixedMenu.targetTop > 0 ? fixedMenu.targetTop : (fixedMenu.hasElement ? document.documentElement.clientHeight : fixedMenu.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu.targetBottom - fixedMenu.menu.offsetHeight }; fixedMenu.moveMenu = function() { fixedMenu.computeShifts(); if(fixedMenu.currentX != fixedMenu.shiftX || fixedMenu.currentY != fixedMenu.shiftY) { fixedMenu.currentX = fixedMenu.shiftX; fixedMenu.currentY = fixedMenu.shiftY; if(document.layers) { fixedMenu.menu.left = fixedMenu.currentX; fixedMenu.menu.top = fixedMenu.currentY }else { fixedMenu.menu.style.left = fixedMenu.currentX + "px"; fixedMenu.menu.style.top = fixedMenu.currentY + "px" } }fixedMenu.menu.style.right = ""; fixedMenu.menu.style.bottom = "" }; fixedMenu.floatMenu = function() { fixedMenu.moveMenu(); setTimeout("fixedMenu.floatMenu()", 20) }; fixedMenu.addEvent = function(a, b, f) { if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { a[b + "_num"] = 0; if(typeof a[b] == "function") { a[b + 0] = a[b]; a[b + "_num"]++ }a[b] = function(c) { var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g } }for(var e = 0;e < a[b + "_num"];e++)if(a[b + e] == f)return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu.init = function() { if(fixedMenu.supportsFixed())fixedMenu.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu.menu : fixedMenu.menu.style; fixedMenu.targetLeft = parseInt(a.left); fixedMenu.targetTop = parseInt(a.top); fixedMenu.targetRight = parseInt(a.right); fixedMenu.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu.addEvent(window, "onscroll", fixedMenu.moveMenu); fixedMenu.floatMenu() } }; fixedMenu.addEvent(window, "onload", fixedMenu.init); fixedMenu.hide = function() { fixedMenu.menu.style.display = "none"; return false }; fixedMenu.show = function() { fixedMenu.menu.style.display = "block"; return false } }if(document.getElementById("fixeddiv2")) { fixedMenuId2 = "fixeddiv2"; var fixedMenu2 = {hasInner:typeof window.innerWidth == "number", hasElement:document.documentElement != null && document.documentElement.clientWidth, menu:document.getElementById ? document.getElementById(fixedMenuId2) : document.all ? document.all[fixedMenuId2] : document.layers[fixedMenuId2]}; fixedMenu2.computeShifts = function() { fixedMenu2.shiftX = fixedMenu2.hasInner ? pageXOffset : fixedMenu2.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu2.shiftX += fixedMenu2.targetLeft > 0 ? fixedMenu2.targetLeft : (fixedMenu2.hasElement ? document.documentElement.clientWidth : fixedMenu2.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu2.targetRight - fixedMenu2.menu.offsetWidth; fixedMenu2.shiftY = fixedMenu2.hasInner ? pageYOffset : fixedMenu2.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu2.shiftY += fixedMenu2.targetTop > 0 ? fixedMenu2.targetTop : (fixedMenu2.hasElement ? document.documentElement.clientHeight : fixedMenu2.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu2.targetBottom - fixedMenu2.menu.offsetHeight }; fixedMenu2.moveMenu = function() { fixedMenu2.computeShifts(); if(fixedMenu2.currentX != fixedMenu2.shiftX || fixedMenu2.currentY != fixedMenu2.shiftY) { fixedMenu2.currentX = fixedMenu2.shiftX; fixedMenu2.currentY = fixedMenu2.shiftY; if(document.layers) { fixedMenu2.menu.left = fixedMenu2.currentX; fixedMenu2.menu.top = fixedMenu2.currentY }else { fixedMenu2.menu.style.left = fixedMenu2.currentX + "px"; fixedMenu2.menu.style.top = fixedMenu2.currentY + "px" } }fixedMenu2.menu.style.right = ""; fixedMenu2.menu.style.bottom = "" }; fixedMenu2.floatMenu = function() { fixedMenu2.moveMenu(); setTimeout("fixedMenu2.floatMenu()", 20) }; fixedMenu2.addEvent = function(a, b, f) { if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { a[b + "_num"] = 0; if(typeof a[b] == "function") { a[b + 0] = a[b]; a[b + "_num"]++ }a[b] = function(c) { var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g } }for(var e = 0;e < a[b + "_num"];e++)if(a[b + e] == f)return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu2.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu2.init = function() { if(fixedMenu2.supportsFixed())fixedMenu2.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu2.menu : fixedMenu2.menu.style; fixedMenu2.targetLeft = parseInt(a.left); fixedMenu2.targetTop = parseInt(a.top); fixedMenu2.targetRight = parseInt(a.right); fixedMenu2.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu2.addEvent(window, "onscroll", fixedMenu2.moveMenu); fixedMenu2.floatMenu() } }; fixedMenu2.addEvent(window, "onload", fixedMenu2.init); fixedMenu2.hide = function() { if(fixedMenu2.menu.style.display != "none")fixedMenu2.menu.style.display = "none"; return false }; fixedMenu2.show = function(a) { document.getElementById("wtipoprot"); var b = 0; for(b = 0;b < document.forms.forma.wtipoprot.length;b++)document.forms.forma.wtipoprot[b].disabled = true; for(b = 0;b < document.forms.forma.wtipoprot.length;b++)if(a.indexOf(document.forms.forma.wtipoprot[b].value) != -1) { document.forms.forma.wtipoprot[b].checked = true; document.forms.forma.wtipoprot[b].disabled = false }fixedMenu2.menu.style.display = "block"; return false } };
</script>
