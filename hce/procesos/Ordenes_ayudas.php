<head>
  <title>ORDENES AYUDAS DIAGNOSTICAS - PACIENTES PENDIENTES</title>
  
  <style type="text/css">
    	
    	A	{text-decoration: none;color: #000066;}
    	.tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo4V:hover {color: #000066; background: #999999;}
        .tipo3VTurno{color:#000066;background:#FFFFCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3VTurno:hover {color: #000066; background: #999999;}
        .tipoTA{color:#000066;background:#FFFFCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:left;}
        .tipoMx{color:#000066;background:#FFFFCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
        <!--.tipoTA:hover {color: #000066; background: #999999;} -->
    	
    </style>
  
</head>

<script type="text/javascript">

document.onkeydown = mykeyhandler; 

function mykeyhandler(event) 
   {
      //keyCode 116 = F5
	  //keyCode 122 = F11
	  //keyCode 8   = Backspace
	  //keyCode 37  = LEFT ROW
	  //keyCode 78  = N
	  //keyCode 39  = RIGHT ROW
	  //keyCode 67  = C
	  //keyCode 86  = V
	  //keyCode 85  = U
	  //keyCode 45  = Insert
	  
	  //keyCode 18  =  alt
	  //keyCode 19  = pause/break
	  //keyCode 27  = escape
	  //keyCode 32  = space bar
	  //keyCode 33  = page up
	  //keyCode 34  = page down
	  //keyCode 35  = end
	  //keyCode 40  = down row
	  //keyCode 46  = delete
	  //keyCode 91  = left window key
	  //keyCode 92  = right window key
	  //keyCode 93  = select key
	  //keyCode 112 = f1
	  //keyCode 113 = f2
	  //keyCode 114 = f3
	  //keyCode 115 = f4
	  //keyCode 116 = f5
	  //keyCode 117 = f6
	  //keyCode 118 = f7
	  //keyCode 119 = f8
	  //keyCode 120 = f9
	  //keyCode 121 = f10
	  //keyCode 123 = f12
	  //keyCode 124 = num lock
	  //keyCode 145 = scroll lock
	  //keyCode 154 = print screen 
	  //         44 = print screen
	  
	  
	  event = event || window.event;
	  var tgt = event.target || event.srcElement;
	  
	  if (event.keyCode == 8 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
	    {
	     return false;
	    }
	    
	  if (event.constructor.DOM_VK_PRINTSCREEN==event.keyCode)
		 {
	      alert("Función no permitida");		 
		  return false;
		 } 
  
	  if ((event.keyCode == 116) || (event.keyCode == 122)) 
	     {
	      if (navigator.appName == "Microsoft Internet Explorer")
	        {
	         window.event.keyCode=0;
	        }
	      return false;
	     }
   }

function enter()
	{
	 document.forms.ordenes_ayudas.submit();
	}
	
function cerrarVentana()
	 {
      window.close()		  
     }
     
function recarga()   
     {
	  var dvAux = document.createElement( "div" );

	  dvAux.innerHTML = "<INPUT type='hidden' name='mostrar'>";
      dvAux.firstChild.value = document.getElementById( "Ordenes" ).innerHTML;
      document.forms[0].appendChild( dvAux.firstChild );
      document.forms[0].submit();    
     }      
     
</script>

<body>

<?php
include_once("conex.php");
  /*********************************************************
   *            ORDENES DE AYUDAS DIAGNOSTICAS             *
   *    EN LA UNIDAD EN DONDE SE ENCUENTRE EL PACIENTE     *
   *     				CONEX, FREE => OK				   *
   *********************************************************/
//==================================================================================================================================
//PROGRAMA                   : Ordenes_ayudas.php
//AUTOR                      : Juan Carlos Hernández M.
//$wautor="Juan C. Hernandez M. ";
//FECHA CREACION             : Enero 25 de 2011
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(Diciembre 18 de 2017)"; 
//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//     Programa usado por las ayudas diagnosticas que NO estan conectadas electronicamente.                                               \\
//     ** Funcionamiento General:                                                                                                         \\
//     Basado en la tabla de Ordenes de la HCE, se toman las ordenes que esten pendientes de realizarse parcial o totalmente y se muestran\\
//     en pantalla, para que a medida que se vayan realizando se les coloque el resultado con un copiar y pegar desde la aplicación en la \\
//     que se transcriben los resultados de cada ayuda, luego de hacer este proceso el paciente desaparece de pantalla.                   \\
//========================================================================================================================================\\
// Modificaciones
//========================================================================================================================================\\
// 	2017-12-18	Jessica		- Se agrega el include a movhos.inc.php
// 							- Se comenta la función traer_diagnostico()
//========================================================================================================================================\\
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
		
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  

  include_once("root/comun.php");
  include_once("movhos/movhos.inc.php");
  

  
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
  		
 
  echo "<br>";				
  echo "<br>";
  
  //*******************************************************************************************************************************************
  //F U N C I O N E S
  //===========================================================================================================================================
  function mostrar_empresa($wemp_pmla)
     {  
	  global $user;   
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $whce;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;   
	     
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
		      if (strtolower($row[0]) == "hce")
		         $whce=$row[1];
	         }  
	     }
	    else
	       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	  
	  $winstitucion=$row[2];
	      
	  encabezado("Ordenes Ayudas Diagnosticas (Pendientes)",$wactualiz, "clinica");  
     }
     
  // function traer_diagnostico($whis, $wing, $wfecha)
     // {
	  // global $conex;
	  // global $wbasedato;   
	     
	  // $q = " SELECT kardia "
          // ."   FROM ".$wbasedato."_000053 A"
          // ."  WHERE karhis       = '".$whis."'"
          // ."    AND karing       = '".$wing."'"
          // ."    AND karest       = 'on' "
          // ."    AND A.fecha_data = '".$wfecha."'";  
	  // $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  // $wnum = mysql_num_rows($res);
	  
	  // if ($wnum > 0)
	     // {
		  // $row = mysql_fetch_array($res);   
		  
		  // return $row[0];   
	     // }
	    // else
	      // return "Sin Diagnostico";     
	 // }       

	 	 
  function traer_medico_ordeno($wtor, $word, $wcod, $wite)
     {
	  global $conex;
	  global $wbasedato;
	  global $whce;
	     
	  $q = " SELECT Medno1, Medno2, Medap1, Medap2  "
          ."   FROM ".$whce."_000028, ".$wbasedato."_000048 "
          ."  WHERE dettor = '".$wtor."'"
          ."    AND detnro = ".$word
          ."    AND detcod = '".$wcod."'"
          ."    AND detest = 'on' "
          ."    AND detusu = meduma "
          ."    AND trim(detusu) != '' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	  
	  if ($wnum > 0)
	     {
		  $wmed="";   
		  $row = mysql_fetch_array($res);    
			  
		  return $wmed;   
	     }
	    else
	      return "Sin Médico";     
	 }       	 
	 
  function traer_orden($whis, $wing, $wfecha, &$i, $wcco1, $wnor, $wite, $wcod)
     {
	  global $conex;
	  global $whce;  
	  
	  global $wser; 
	  global $wexa;
	  global $west;
	  global $wjus;
	  global $wrdo;
	  global $wfec1;
	  
	  $q = " SELECT descripcion, detesi, detfec, detjus, detrdo "
          ."   FROM ".$whce."_000027, ".$whce."_000028, ".$whce."_000017 "
          ."  WHERE ordnro  = ".$wnor
          ."    AND ordhis  = '".$whis."' "
          ."    AND ording  = '".$wing."' "
          ."    AND ordtor  = '".$wcco1."'"
          ."    AND ordtor  = dettor "
          ."    AND ordnro  = detnro "
          ."    AND ordest  = 'on' "
          ."    AND detcod  = codigo "
          ."    AND ordfec <= '".$wfecha."'"
          ."    AND detcod  = '".$wcod."'"
          ."    AND detesi  = 'Pendiente' "    //Solo traigo los pendientes
          ."    AND detite  = ".$wite
          ."  ORDER BY 1, 2, 3 ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	  
	  $i = $wnum;
	  
	  if ($wnum > 0)
	     {
		  $row = mysql_fetch_array($res);    
			  
		  $wexa  = $row[0];   //Estado de la orden
		  $west  = $row[1];   //Estado de la orden
		  $wfec1 = $row[2];   //Fecha a realizarse
		  $wjus  = $row[3];   //Justificación
		  $wrdo  = $row[4];   //Resultado
	     }
	 }

  function traer_nombre_usuario($wusuario)
     {
	  global $conex;
	  
	  $q = " SELECT descripcion "
	      ."   FROM usuarios "
	      ."  WHERE codigo = '".$wusuario."'"
	      ."    AND activo = 'A' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	  
	  if ($wnum > 0)
	    {
	     $row = mysql_fetch_array($res);
	     return $row[0]; 
        }
       else
          return "";  
	 }
     
  function traer_datos_del_kardex($whis, $wing, &$wanp, &$wale, &$wtal, &$wpes)
     {
	  global $wbasedato;
	  global $conex;
	 
	  
	  $wanp="";
	  $wale=""; 
	     
	  $q = " SELECT karanp, karale, kartal, karpes, MAX(fecha_data) "
	      ."   FROM ".$wbasedato."_000053 "
	      ."  WHERE karhis = '".$whis."'"
	      ."    AND karing = '".$wing."'"
	      ."  GROUP BY 1, 2, 3, 4 ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	     
	  if ($wnum > 0)
	     {
		  $row = mysql_fetch_array($res);
		  
		  $wanp=$row[0];
		  $wale=$row[1];
		  $wtal=$row[2];
		  $wpes=$row[3];
         }
     }    	 
	 
	 	 
  function elegir_historia()   
     {
	  global $user;
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $whce;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz; 
	  global $wemp_pmla;
	  global $wser;
	  
	  global $wcco; 
	  global $wnomcco;
	  
	  global $whab;
	  global $whis;
	  global $wing;
	  global $wpac;
	  global $wtid;                                      //Tipo documento paciente
	  global $wdpa;
	  global $weda;
	  
	  global $wfec;
	  global $wfecha;
	  
	  global $wmed;
	  global $wdiag;
	  
	  global $whora_par_actual;
	  
	  $wcco1=explode("-",$wcco);
	  
	  //Seleccionar CENTRO DE COSTOS
	  echo "<center><table>";
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011 "
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
          ."    AND ".$wbasedato."_000011.ccoayu = 'on' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
		
      
	  echo "<tr class=fila1><td align=center><font size=4>Seleccione la Unidad : </font></td>";
	  echo "<td align=center><select name='wcco' onchange='enter()'>";
	  if (isset($wcco)) 
	     echo "<option selected>".$wcco."</option>";
	    else 
	     echo "<option>&nbsp</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";
	  
	  //Selecciono todos los pacientes del servicio seleccionado
	  $q = " SELECT ubisac, cconom, ubihac, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, ordnro, detcod, descripcion, detite, dettor "
	      ."   FROM ".$wbasedato."_000018, root_000036, root_000037, ".$whce."_000027, ".$whce."_000028, ".$wbasedato."_000011, ".$whce."_000017 "
	      ."  WHERE ordtor  = '".trim($wcco1[0])."'"
	      ."    AND ordhis  = ubihis "            
	      ."    AND ording  = ubiing "  
	      ."    AND ordest  = 'on' "          
	      ."    AND ordhis  = orihis "
	      ."    AND ording  = oriing "
	      ."    AND ubiald != 'on' "
	      ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
	      ."    AND oriced  = pacced "
	      ."    AND ubisac  = ccocod "
	      ."    AND ordtor  = dettor "
	      ."    AND ordnro  = detnro "
	      ."    AND detcod  = codigo "
	      ."    AND detesi  = 'Pendiente' "    //Solo traigo los pendientes
	      ."  GROUP BY 1,2,3,4,5,6,7,8,10,11,12,13,14,15,16 "
	      ."  ORDER BY 13 "; 
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);  
	  	     
	  echo "<center><table>";
	  echo "<tr class=encabezadoTabla>";
	  echo "<th><font size=4>Servicio Origen</font></th>";
	  echo "<th><font size=4>Habitación</font></th>";
	  echo "<th><font size=4>Historia</font></th>";
	  echo "<th><font size=4>Paciente</font></th>";
	  echo "<th><font size=4>Médico que Ordena</font></th>";
	  echo "<th><font size=4>Nro Orden</font></th>";
	  echo "<th><font size=4>Estudio o Examén</font></th>";
	  echo "</tr>";
		                       
	  $whabant = "";
	  if ($num > 0)
	     {
		  for($i=1;$i<=$num;$i++)
			 {
			   $row = mysql_fetch_array($res);  	  
				  
			   $wclass_entregado="";
			   if (is_integer($i/2))
                  $wclass="fila1";
                 else
                    $wclass="fila2";
                    
               $wser = $row[0];  //Codigo CC o servicio origen
               $wnse = $row[1];  //Nombre del servicio origen    
               $whab = $row[2];
			   $whis = $row[3];
			   $wing = $row[4];
			   $wpac = $row[5]." ".$row[6]." ".$row[7]." ".$row[8];
			   
			   $wnac=$row[9];
			   //Calculo la edad
		       $wfnac=(integer)substr($wnac,0,4)*365 +(integer)substr($wnac,5,2)*30 + (integer)substr($wnac,8,2);
			   $wfhoy=(integer)date("Y")*365 +(integer)date("m")*30 + (integer)date("d");
			   $weda=(($wfhoy - $wfnac)/365);
			   if ($weda < 1)
		         $weda = number_format(($weda*12),0,'.',',')."<b> Meses</b>";
	            else
		           $weda=number_format($weda,0,'.',',')." Años"; 
			   
			   $wtid = $row[10];                 //Tipo documento paciente
			   $wdpa = $row[11];                 //Documento del paciente
			   $wnor = $row[12];                 //Numero de la Orden
			   $wcod = $row[13];                 //Codigo del examen, procedimiento o Estudio
			   $wexa = $row[14];                 //Descripcion del examen, procedimiento o Estudio
			   $wite = $row[15];                 //Numero de item: equivale al consecutivo del estudio dentro de la orden
			   $wtor = $row[16];                 //Tipo de Orden, equivale al centro de costo que realiza el proceso
			       
			   echo "<tr class=".$wclass.">";
			   echo "<td align=left><b>".$wnse."</b></td>";
			   echo "<td align=center><b>".$whab."</b></td>";
		       echo "<td align=center>".$whis." - ".$wing."</td>";
		       echo "<td align=left  >".$wpac."</td>";
		       
		       $wdiag=traer_diagnostico($whis, $wing, $wfec);
		       if ($wdiag=="Sin Diagnostico")    //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
		          {
			       $dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
				   $wayer = date('Y-m-d', $dia); //Formatea dia
				   
				   $wdiag=traer_diagnostico($whis, $wing, $wayer);
			      } 
			      
		       $wmed=traer_medico_ordeno($wtor, $wnor, $wcod, $wite);
		       
		       echo "<td align=left  ><b>".$wmed."</b></td>";
		       echo "<td align=center><b>".$wnor."</b></td>";
		       echo "<td align=center><b>".$wexa."</b></td>";
		       
		       traer_datos_del_kardex($whis, $wing, &$wanp, &$wale, &$wtal, &$wpes);
		       
		       echo "<td align=center><A HREF='Ordenes_ayudas.php?wemp_pmla=".$wemp_pmla."&user=".$user."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wfec=".$wfec."&whab=".$whab."&wpac=".$wpac."&wtid=".$wtid."&wdpa=".$wdpa."&weda=".$weda."&wdiag=".$wdiag."&wmed=".$wmed."&wser=".$wser."&wnse=".$wnse."&wnor=".$wnor."&wanp=".$wanp."&wale=".$wale."&wtal=".$wtal."&wpes=".$wpes."&wcod=".$wcod."&wite=".$wite."&wtor=".$wtor."' class=tipo3V>Ver</A></td>";
		       
		       echo "</tr>";
		     }	     
		  }
		 else
		    echo "NO HAY ORDENES PENDIENTES";  
	  echo "</table>"; 
	  
	 }    	 
	 
  function query_orden($whis, $wing, $wcco, &$res)
    {
	 global $conex;
	 global $wbasedato;
	 global $whce;
	 global $wemp_pmla;   
	    
	 $q = " SELECT ordnro, ordhor, ordhis, ording, ordtor, ordobs, ordfec "
	     ."   FROM ".$whce."_000027 A "
	     ."  WHERE ordhis  = '".$whis."'"
	     ."    AND ording  = '".$wing."'"
	     ."    AND ordesp  not in ('Realizado','Cancelado') "
	     ."    AND ordest  = 'on' "
	     ."    AND ordtor  = '".$wcco."'"
	     ."    AND ordfec <= '".date("Y-m-d")."'";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());    
	}    

  //===========================================================================================================================================  
  //*******************************************************************************************************************************************
  
  
  
  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L 
  //===========================================================================================================================================
  //===========================================================================================================================================
  echo "<form name='ordenes_ayudas' action='Ordenes_ayudas.php' method=post>";
  
  if (!isset($wfecha)) $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  
  mostrar_empresa($wemp_pmla);
  
  if ((isset($wgrabar) and $wgrabar == "on"))
     {
	  $wcco1=explode("-",$wcco);
	     
	  $q = " UPDATE ".$whce."_000028 "
	      ."    SET detrdo = '".$wrdo."' "
	      ."  WHERE dettor = '".trim($wcco1[0])."'"
	      ."    AND detnro = ".$wnor
	      ."    AND detite = ".$wite
	      ."    AND detcod = '".$wcod."'";
	  $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error());
	  
	  $q = " UPDATE ".$whce."_000028 "
	      ."    SET detesi = 'Realizado',  "
	      ."        deture = '".$wusuario."' "
	      ."  WHERE dettor = '".trim($wcco1[0])."'"
	      ."    AND detnro = ".$wnor
	      ."    AND detite = ".$wite
	      ."    AND detcod = '".$wcod."'";
	  $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error());
	  
	  //Busco si hay algun item de la orden 'pendiente', si si, cambio la orden por parcial, si no, coloco el encabezado como 'Realizado'
	  $q = " SELECT COUNT(*) "
	      ."   FROM ".$whce."_000028 "
	      ."  WHERE dettor = '".trim($wcco1[0])."'"
	      ."    AND detnro = ".$wnor
	      ."    AND detesi = 'Pendiente' ";
	  $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error());    
	  $num = mysql_num_rows($res);
	  if ($num > 0)
		 {
		  $q = " UPDATE ".$whce."_000027 "
		      ."    SET ordesp = 'Parcial'  "
		      ."  WHERE ordtor = '".trim($wcco1[0])."'"
		      ."    AND ordnro = ".$wnor;
		  $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error());
		 }
		else
		   {
		  $q = " UPDATE ".$whce."_000027 "
		      ."    SET ordesp = 'Realizado'  "
		      ."  WHERE ordtor = '".trim($wcco1[0])."'"
		      ."    AND ordnro = ".$wnor;
		  $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error());
		 } 		 	    
	  
	  echo $mostrar;      //Esto muestra el DIV donde esta toda la orden con el resultado incluido
	  
	  echo "<br><br>";
      echo "<center><table>";
      echo "<tr><td><A HREF='Ordenes_ayudas.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."&wfec=".$wfec."' class=tipo4V>Retornar</A></td></tr>";
      echo "</table>";
	 } 
	else
       {
	    if (isset($whis) and isset($wcco))
	       {
		    $wcco1=explode("-",$wcco);   
		       
		    //echo "<input type='HIDDEN' name='wser' VALUE='".$wser."'>";   
		    echo "<input type='HIDDEN' name='wcco' VALUE='".$wcco."'>";
	        echo "<input type='HIDDEN' name='wfec' VALUE='".$wfec."'>";
	        if (isset($wdiag)) echo "<input type='HIDDEN' name='wdiag' VALUE='".$wdiag."'>";
	        if (isset($wmed))  echo "<input type='HIDDEN' name='wmed' VALUE='".$wmed."'>";   
		       
	        echo "<input type='HIDDEN' name='wnor' value='".$wnor."'>";
		    echo "<input type='HIDDEN' name='whis' value='".$whis."'>";
		    echo "<input type='HIDDEN' name='wing' value='".$wing."'>";
		    echo "<input type='HIDDEN' name='whab' value='".$whab."'>";
		    echo "<input type='HIDDEN' name='wpac' value='".$wpac."'>";
		    echo "<input type='HIDDEN' name='wtid' value='".$wtid."'>";
		    echo "<input type='HIDDEN' name='wdpa' value='".$wdpa."'>";
		    echo "<input type='HIDDEN' name='weda' value='".$weda."'>";
		    echo "<input type='HIDDEN' name='wanp' value='".$wanp."'>";
		    echo "<input type='HIDDEN' name='wale' value='".$wale."'>";
		    echo "<input type='HIDDEN' name='wtal' value='".$wtal."'>";
		    echo "<input type='HIDDEN' name='wpes' value='".$wpes."'>";
		    echo "<input type='HIDDEN' name='wcod' value='".$wcod."'>";
		    echo "<input type='HIDDEN' name='wite' value='".$wite."'>";
		    		    
		    query_orden($whis, $wing, $wcco1[0], &$res);
		    $num = mysql_num_rows($res);
		    			   
			if ($num > 0)
		       {
		        $row = mysql_fetch_array($res);
		        
		        echo "<div id='Ordenes'>";    ///**** div ****
		        
		        echo "<center><table>";
		        
		        echo "<tr class=encabezadoTabla>";
			    echo "<td align=center colspan=8><b><font size=5>".$wcco."</font></b></td>";
			    echo "</tr>";
		        
			    echo "<tr class=fila1>";
			    echo "<th><font size=3>Servicio "."</font></th>";
			    echo "<th><font size=3>Habitación "."</font></th>";
			    echo "<th><font size=3>Documento</font></th>";
			    echo "<th><font size=3>Historía</font></th>";
			    echo "<th><font size=3>Nombre</font></th>";
			    echo "<th><font size=3>Edad</font></th>";
			    echo "<th><font size=3>Talla</font></th>";
			    echo "<th><font size=3>Peso</font></th>";
			    echo "</tr>";
			    
			    echo "<tr class=fila2>";
			    echo "<td bgcolor=2A5DB0 align=center><b><font size=5 color='white'>".$wser."</font></b></td>";
			    echo "<td bgcolor=2A5DB0 align=center><b><font size=5 color='white'>".$whab."</font></b></td>";
			    echo "<td align=center>".$wdpa."</td>";
			    echo "<td align=center>".$whis."</td>";
			    echo "<td align=center><font size=4><b>".$wpac."&nbsp&nbsp</b></font></td>";
			    echo "<td align=center><font size=4><b>".$weda."</b></td>";
			    echo "<td align=center>".$wtal."</td>";
			    echo "<td align=center>".$wpes." Kg</td>";
			    echo "</tr>";
			    
			    echo "</table>";
			    
			    //echo "<br>";
			    echo "<center><table>"; 
			    
			    //Diagnostico y Medico tratante
			    echo "<tr class=encabezadoTabla>";
			    echo "<td align=center colspan=3>Diagnostico(s)</td>";
			    echo "<td align=center colspan=3>Médico(s) Tratantes</td>";
			    echo "</tr>";
			    echo "<tr class=fila2>";
			    echo "<td align=center colspan=3><textarea rows=3 cols=60 readonly class=tipoTA>".$wdiag."</textarea></td>";
			    echo "<td align=center colspan=3 class=tipoMx>".$wmed."</td>";
			    echo "</tr>";
			    
			    //Antecedentes Personales
			    if (trim($wanp) != "" or trim($wale) != "")
			       {
				    echo "<tr class=encabezadoTabla>";
				    echo "<td colspan=3 align=center><b>ANTECEDENTES PERSONALES</b></td>";
				    echo "<td colspan=3 align=center><b>ANTECEDENTES ALERGICOS</b></td>";
				    echo "</tr>";
				    echo "<tr class=fila2>";
				    echo "<td align=center colspan=3><textarea rows=2 cols=60 readonly class=tipoTA>".$wanp."</textarea></td>";
				    echo "<td align=center colspan=3><textarea rows=2 cols=60 readonly class=tipoTA>".$wale."</textarea></td>";
				    echo "</tr>";
			       }
			       
			    echo "</table>";
			    //echo "<br>";
			    echo "<center><table>";
			    
			    $j=0;                                            //Nro Orden
			    traer_orden($whis, $wing, $wfecha, &$j, $wcco1[0], $wnor, $wite, $wcod);   //Esto lo hago aca arriba porque necesito saber si tiene examenes para sacar o no el titulo de CONTROLES
			     
			    if ($j > 0)
			       { 
				    //Examenes
			        echo "<tr class=encabezadoTabla>";
				    echo "<td colspan=2 align=center><font size=6 color='white'><b>".$wexa."</b></font></td>";
				    echo "</tr>";
				    echo "<tr class=fila1>";
				    echo "<td align=center><b>Justificación</b></td>";
				    echo "<td align=center><b>Fecha a<br>Realizar</b></td>";
				    echo "</tr>";
				    
				    echo "<tr class=fila2>";
					echo "<td align=center><textarea rows=2 cols=120 readonly class=tipoTA>".$wjus."</textarea></td>";
					echo "<td align=center>".$wfec1."</td>";
					echo "</tr>";
				    
				    echo "<tr class='encabezadoTabla'>";
				    echo "<td align=center colspan=2><b>Resultado</b></td>";
				    echo "</tr>";
				    
				    if ($j > 0)
				       {
					    echo "<tr class=fila2>";
						if (trim($wrdo) != "")
						   echo "<td align=center colspan=2><textarea rows=22 cols=140 name=wrdo>".$wrdo."</textarea></td>";
						  else
						     echo "<td align=center colspan=2><textarea rows=22 cols=140 name=wrdo></textarea></td>"; 
						echo "</tr>";
					   }
				   }
		        echo "</table>";
			    
		        echo "</div>"; 
		        
			    echo "<br>";
			    
			    if (trim($wrdo) == "")
			       {
				    echo "<center><table>";
				    echo "<tr class=encabezadoTabla>";
				    echo "<td colspan=3 align=center><input type=checkbox name=wgrabar>Grabar&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type='button' onclick='recarga()' value='Enviar a HCE'></b></td>";
		  		    echo "</tr>";
		  		    echo "</table>";
	  		       }
			   } 
             else  //del 2do if ($num > 0)
		        {
			     echo "<table>"; 
				 echo "<tr class=encabezadoTabla>";
				 echo "<td>No existen datos para este paciente</td>";
				 echo "</tr>";
				 echo "</table>";   
			    }
			  
			             
		    echo "<br><br>";
		    echo "<table>";
		    echo "<tr><td><A HREF='Ordenes_ayudas.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."&wfec=".$wfec."' class=tipo4V>Retornar</A></td></tr>";
		    echo "</table>";	  
		   }
	      else
	         {
		      elegir_historia();  
		      
			  echo "<br>";
		      echo "<center><table>";
		      echo "<tr><td><A HREF='Ordenes_ayudas.php?wemp_pmla=".$wemp_pmla."' class=tipo4V>Retornar</A></td></tr>";
		      echo "</table>";	  
		     }
       }
       
      echo "<br><br>";
	  echo "<table>";   
	  echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
	  echo "</table>";	          
} // if de register

?>