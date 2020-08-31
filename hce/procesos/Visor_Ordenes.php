<head>
  <title>VISOR ORDENES NO HL7</title>
  
  <style type="text/css">
    	
    	A	{text-decoration: none;color: #000066;}
    	.tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px;width:50em}
        .tipo4V:hover {color: #000066; background: #999999;}
        .tipo3VTurno{color:#000066;background:#FFFFCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3VTurno:hover {color: #000066; background: #999999;}
        .tipoTA{color:#000066;background:#FFFFCC;font-size:15pt;font-family:Arial;font-weight:bold;text-align:left;}
        .tipoMx{color:#000066;background:#FFFFCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
        <!--.tipoTA:hover {color: #000066; background: #999999;} -->
    	
    </style>
  
</head>

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.accordion.js"></script>	<!-- Acordeon -->
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script>	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>

<script type="text/javascript">

function inicializarJquery()
  {
   $("#acExamenes").accordion({ collapsible: true });
   $("#null").accordion({ collapsible: true });
  }

function intercalarExamenAnterior(idElemento)
   {
    //$("#"+idElemento).toggle("normal");
    
    if ( document.getElementById(idElemento).style.display=='')
       {
    	document.getElementById(idElemento).style.display='none';
       }
      else
        {
	     document.getElementById(idElemento).style.display='';
        }
    
    //<!--$("#ex"+idElemento).toggle("normal"); -->
   }  

function enter()
	{
	 document.forms.visor.submit();
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
//PROGRAMA                   : Visor_Ordenes.php
//AUTOR                      : Juan Carlos Hernández M.
//$wautor="Juan C. Hernandez M. ";
//FECHA CREACION             : Enero 25 de 2011
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(Enero 31 de 2011)"; 
//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//     Programa usado para visualizar los resultados de las ordenes generadas desde la HCE y que no sean HL7.                             \\
//     ** Funcionamiento General:                                                                                                         \\
//     Basado en la tabla de Ordenes de la HCE, se toman las ordenes que esten 'Realizadas' parcial o totalmente y se muestran            \\
//     en pantalla.                                                                                                                       \\
//========================================================================================================================================\\
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
	      
	  encabezado("Visor de Ordenes Realizadas",$wactualiz, "clinica");  
     }
     
     
  function traer_diagnostico($whis, $wing, $wfecha)
     {
	  global $conex;
	  global $wbasedato;   
	     
	  $q = " SELECT kardia "
          ."   FROM ".$wbasedato."_000053 A"
          ."  WHERE karhis       = '".$whis."'"
          ."    AND karing       = '".$wing."'"
          ."    AND karest       = 'on' "
          ."    AND A.fecha_data = '".$wfecha."'";  
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	  
	  if ($wnum > 0)
	     {
		  $row = mysql_fetch_array($res);   
		  
		  return $row[0];   
	     }
	    else
	      return "Sin Diagnostico";     
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
     
     
  function mostrar_resultado($wresultado)
     {
	  global $whce;
	  global $conex;
	  
	  $wmensaje=explode("@",$wresultado);       		 			 //Separo el encabezado del resto de la información, el '@' indica fin del encabezado
		 
	  //===========================================================================================================================
	  //* * * Encabezado * * *
	  //===========================================================================================================================
	  $wencabezado=explode("!",$wmensaje[0]);            			 //Separo las filas del encabezado, '!' indica fin de fila <tr>
	  
	  echo "<center><table border=0>";
	  for ($k=0;$k < count($wencabezado); $k++)
	     {
		   $wnegrilla=false; 
		   if (($k+1)==count($wencabezado))                         //Si (k+1) == count, es porque es el nombre del Estudio
		      {
		       $wnegrilla=true;
		      } 
		     
		   $wlinea=explode(":",$wencabezado[$k]);
		   if (!isset($wlinea[1]))                                  //Si no hay ':' es porque es un Titulo
		      $wlinea=$wencabezado[$k];
		     else
		       $wlinea="<b>".$wlinea[0].":</b>&nbsp".$wlinea[1];
		      
		   echo "<tr>";
		   if ($wnegrilla)
		      echo "<td align=center colspan=3 class=tipoTA>".$wlinea."</td>";
		     else
		        echo "<td align=center colspan=3>".$wlinea."</td>"; 
		   echo "</tr>";
		 }
	  //===========================================================================================================================
	  
		 
	  //===========================================================================================================================
	  //* * * Detalle * * *
	  //===========================================================================================================================
	  echo "<tr class='encabezadoTabla'>";
	  echo "<th align=center>Descripción</th>";
	  echo "<th align=center>Valor Resultado</th>";
	  echo "<th align=center>Valor de Referencia</th>";
	  echo "</tr>";
	  
	  $wfilas=explode("!",$wmensaje[1]);        		 			 //La información diferente al encabezado, la separo en filas, como si fuera un registro
		 
	  for ($i=0;$i<count($wfilas);$i++)           
	     {
	      $wcolumnas=explode("$",$wfilas[$i]);     		 			 //Cada fila o registro lo separo en columnas o campos, '$' indica fin del campo
		  
	      if (isset($wclase) and $wclase == "fila1")
		    $wclase = "fila2";
		   else 
		      $wclase = "fila1";
	      
	      echo "<tr class='".$wclase."'>"; 
		  for ($j=0;$j<count($wcolumnas);$j++)
		     {
			  if (count($wcolumnas) == 1)                            //Si entra aca es porque es un SubTitulo
	             echo "<td align=left colspan=3 class='encabezadoTabla'><b>".$wcolumnas[$j]."</b></td>";          //Imprimo cada columna
	            else
	               echo "<td align=center>".$wcolumnas[$j]."</td>";  //Imprimo cada columna 
		     }
	      echo "</tr>";	
	     }
	  echo "</table></center>";
	  //===========================================================================================================================     
	 }    
  	 
     
  function traer_resultado($wtabla, $wcco, $whis, $wing, $wnor, $wite)
     {
	  global $whce;
	  global $conex;
	  
	  $q = " SELECT hl7rdo "
	      ."   FROM ".$whce."_".$wtabla
	      ."  WHERE hl7his = '".$whis."'"
	      ."    AND hl7ing = '".$wing."'"
	      ."    AND hl7des = '".trim($wcco)."'"
	      ."    AND hl7nor = ".$wnor
	      ."    AND hl7nit = '".$wnor."-".$wite."'"
	      ."    AND hl7edo = 'Realizado' "
	      ."    AND hl7est = 'on' ";
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
     
     
  function mostrar_paciente($wcco, $wncc, $whis, $wing)
     {
	  global $wbasedato;
	  global $conex;
	  global $wemp_pmla;
	  global $wfecha;
	  global $wfechadeIngreso;
	     
	     
	  $q = " SELECT pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, ubihac, C.fecha_data  "
	      ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C "
	      ."  WHERE orihis = '".$whis."'"
	      ."    AND oriing = '".$wing."'"
	      ."    AND oriori = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
	      ."    AND oriced = pacced "
	      ."    AND orihis = ubihis "
	      ."    AND oriing = ubiing "
	      ."  GROUP BY 1,2,3,4,5,6,7,8 ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);    
	  
	  if ($num > 0)
	     {
		  $row = mysql_fetch_array($res);
		  
		  $wpac=$row[0]." ".$row[1]." ".$row[2]." ".$row[3]; 
		  
		  $wnac=$row[4];
		  //Calculo la edad
	      $wfnac=(integer)substr($wnac,0,4)*365 +(integer)substr($wnac,5,2)*30 + (integer)substr($wnac,8,2);
		  $wfhoy=(integer)date("Y")*365 +(integer)date("m")*30 + (integer)date("d");
		  $weda=(($wfhoy - $wfnac)/365);
		  if ($weda < 1)
	        $weda = number_format(($weda*12),0,'.',',')."<b> Meses</b>";
           else
	          $weda=number_format($weda,0,'.',',')." Años"; 
	      $whab=$row[7];
	      $wfechadeIngreso=$row[8];   //Fecha de Ingreso
	     }    
	  
	  $wdiag=traer_diagnostico($whis, $wing, $wfecha);
      if ($wdiag=="Sin Diagnostico")    //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
         {
	      $dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
	      $wayer = date('Y-m-d', $dia); //Formatea dia
		   
	      $wdiag=traer_diagnostico($whis, $wing, $wayer);
	     }
	     
	  traer_datos_del_kardex($whis, $wing, &$wanp, &$wale, &$wtal, &$wpes); 
	  
	  echo "<center><table>";
		        
      echo "<tr class=encabezadoTabla>";
      echo "<td align=center colspan=8><b><font size=4>".$wcco." - ".$wncc."</font></b></td>";
      echo "</tr>";
    
      echo "<tr class=fila1>";
      echo "<th><font size=3>Habitación</font></th>";
      echo "<th><font size=3>Historía</font></th>";
      echo "<th><font size=3>Paciente</font></th>";
      echo "<th><font size=3>Edad</font></th>";
      echo "<th><font size=3>Talla</font></th>";
      echo "<th><font size=3>Peso</font></th>";
      echo "</tr>";
    
      echo "<tr class=fila2>";
      echo "<td align=center><font size=3><b>".$whab."</b></font></td>";
      echo "<td align=center><font size=3><b>".$whis."-".$wing."&nbsp&nbsp</b></font></td>";
      echo "<td align=center><font size=3><b>".$wpac."&nbsp&nbsp</b></font></td>";
      echo "<td align=center><font size=3><b>".$weda."</b></td>";
      echo "<td align=center>".$wtal."</td>";
      echo "<td align=center>".$wpes." Kg</td>";
      echo "</tr>";
      
      //Diagnostico y Medico tratante
	  echo "<tr class=encabezadoTabla>";
	  echo "<td align=center colspan=2>Diagnostico(s)</td>";
	  echo "<td align=center colspan=1>Antecedentes</td>";
	  echo "<td align=center colspan=3>Alergias</td>";
	  echo "</tr>";
	  echo "<tr class=fila2>";
	  echo "<td align=center colspan=2><textarea rows=3 cols=40 readonly class=tipoTA>".$wdiag."</textarea></td>";
	  echo "<td align=center colspan=1><textarea rows=3 cols=40 readonly class=tipoTA>".$wanp."</textarea></td>";
	  echo "<td align=center colspan=3><textarea rows=3 cols=40 readonly class=tipoTA>".$wale."</textarea></td>";
	  echo "</tr>";
    
      echo "</table>";
			     
	 }
	 
  //===========================================================================================================================================  
  //*******************************************************************************************************************************************
  
  
  
  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L 
  //===========================================================================================================================================
  //===========================================================================================================================================
  echo "<form name='visor' action='Visor_Ordenes.php' method=post>";
  
  //if (!isset($wfecha)) 
  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wcco' VALUE='".$wcco."'>";
  echo "<input type='HIDDEN' name='whis' VALUE='".$whis."'>";
  echo "<input type='HIDDEN' name='wing' VALUE='".$wing."'>";
  
  mostrar_empresa($wemp_pmla);
  
  if (isset($wcco))
     {
	  $wcco1=explode("-",$wcco);  
	     
	  $q = " SELECT ccocod, cconom "
	      ."   FROM ".$wbasedato."_000011 "
	      ."  WHERE ccocod = '".$wcco."'";
	  $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error());
	  $wcco1 = mysql_fetch_array($res);
	  
	      
	  mostrar_paciente($wcco1[0], $wcco1[1], $whis, $wing);
	     
	  echo "<hr></hr>";   
	  echo "<center><table>";
	  echo "<tr class=encabezadoTabla><td align=center colspan=5>ORDENES A BUSCAR EN UN RANGO DE FECHAS</td></tr>";
	  echo "<tr>";
      echo "<td align=right  class=fila1><b>Fecha Inicial: </b></td>";
      echo "<td align=center class=fila2>";
      if (!isset($wfeci))
         {
	      $wfeci=$wfechadeIngreso;   
          campoFechaDefecto("wfeci",$wfechadeIngreso);
         } 
        else
          campoFechaDefecto("wfeci",$wfeci);
      echo "</td>";
      
      echo "<td align=right  class=fila1><b>Fecha Final: </b></td>";
      echo "<td align=center class=fila2>";
      if (!isset($wfecf))
         {
	      $wfecf=$wfecha;   
          campoFechaDefecto("wfecf",$wfecha);
         } 
        else
          campoFechaDefecto("wfecf",$wfecf);
      echo "</td>";
      echo "<td align=center>";
      echo "<input type='submit' value='BUSCAR'>";
      echo "</td>";
      echo "</tr>";
      echo "</table></center>"; 
      
      echo "<hr></hr>";         //Linea
      
      echo "<br><br>";  
	    
	  $wclase="fila1";
	       
	  //Traigo todas las Ordenes del Servicio seleccionado y de la historia ingreso dado y que esten 'Realizadas'
	  $q = " SELECT A.ordfec, A.ordhor, B.detcod, C.descripcion, B.detrdo, D.Arc_HL7, D.Programa, D.Formato, A.ordnro, B.detite "
	      ."   FROM ".$whce."_000027 A, ".$whce."_000028 B, ".$whce."_000017 C, ".$whce."_000015 D "
	      ."  WHERE A.ordfec      BETWEEN '".$wfeci."' AND '".$wfecf."'"
	      ."    AND A.ordtor      = '".trim($wcco1[0])."'"
	      ."    AND A.ordtor      = B.dettor "
	      ."    AND A.ordnro      = B.detnro "
	      ."    AND B.detesi      = 'Realizado'"
	      ."    AND A.ordest      = 'on' "
	      ."    AND B.detcod      = C.codigo "
	      ."    AND C.tipoestudio = D.codigo "
	      ."    AND A.ordhis      = '".$whis."'"
	      ."    AND A.ording      = '".$wing."'"
	      ."  ORDER BY 1 desc ";
	  $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error());
	  $num = mysql_num_rows($res);
	    	        
	  if ($num > 0)
	     {	  
	      $row = mysql_fetch_array($res);
         } 
         
         
      echo "<center><table>";   
	  $i=1;
	  while ($i <= $num)
	    {
		 ///$row = mysql_fetch_array($res);
			   
		 $wfecant=$row[0];
		 
		 //Traigo la cantidad de registros que hay por fecha, para poder hacer el 'rowspan'
		 $q = " SELECT COUNT(*) "
		      ."   FROM ".$whce."_000027 A, ".$whce."_000028 B, ".$whce."_000017 C, ".$whce."_000015 D "
		      ."  WHERE A.ordfec      = '".$wfecant."'"
		      ."    AND A.ordtor      = '".trim($wcco1[0])."'"
		      ."    AND A.ordtor      = B.dettor "
		      ."    AND A.ordnro      = B.detnro "
		      ."    AND B.detesi      = 'Realizado'"
		      ."    AND A.ordest      = 'on' "
		      ."    AND B.detcod      = C.codigo "
		      ."    AND C.tipoestudio = D.codigo "
		      ."    AND A.ordhis      = '".$whis."'"
		      ."    AND A.ording      = '".$wing."'";
		 $res1 = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error());
		 $row1 = mysql_fetch_array($res1);
		 
		 $wfilas=$row1[0];  //Cantidad de registro por fecha para el 'rowspan'
	      
		 if ($wclase == "fila1")
		    $wclase = "fila2";
		   else 
		      $wclase = "fila1";
			      
		 //echo "<center><table>";
		 echo "<tr class='".$wclase."'>";
		 echo "<td rowspan=".$wfilas."><font size=4><b>".$wfecant."</b></font></td>";    //Rowspan
	  
		 for ($k=1; $k<=$wfilas; $k++)
		    {
		     if ($k > 1)
		        {
			     ///$row = mysql_fetch_array($res);   
		         echo "<tr>";
	            } 
		          
			 $wnomdiv=$row[0]."-".$row[1]."-".$row[2];   //Nombre del DIV
				 
			 echo "<td>";   
			 echo "<a href='#null' onclick=javascript:intercalarExamenAnterior('".$wnomdiv."'); class=tipo4V>".$row[3]."</a>";
			 
			 echo "<div id='".$wnomdiv."' style='display:none'>";
			 echo "<TABLE align='center' border=0>";
				
			 //echo "<tr class='encabezadoTabla' align='center'>";
			 //echo "<td align=center><font size=4>".$row[3]."</font></td>";                                        //Nombre del Estudio
			 //echo "</tr>";
				
			 //Formatos
			 // 1: Descriptivo                  ej: Patologia, Mamografia, Endoscopia, Cardiología (hasta que no se tome por HL7), etc.
			 // 2: Descriptivo com Imagen       ej: Imagenología (TAC, RX) con HL7, etc.
			 // 3: Por valores y con referencia ej: Laboratorio Clínico
			 
			 $wtabla=$row[5];             //Archivo en el que se almacena el resultado, si tiene valor es porque es HL7, este valor esta en la tabla hce_000015
			 $wformatoVista=$row[7];      //Formato en que se ve el resultado, Viene de la tabla hce_000015
			 $wnor  =$row[8];             //Numero de orden
			 $wite  =$row[9];             //Numero de Orden es el numero de orden seguido de un guion y la fila correspodiente al examen o procedimiento
			 
			 switch ($wformatoVista)
			    {
				  case "1":  
				      if (trim($wtabla)=="" or strtoupper(trim($wtabla)) == "NO APLICA") 
				         {
						  echo "<tr class='encabezadoTabla' align='center'>";
					      echo "<td align=center><textarea rows=22 cols=140 readonly>".$row[4]."</textarea></td>";                       //Muestra el Resultado
					      echo "</tr>";
				         }
				        else 
				           traer_resultado($wtabla, $wcco1[0], $whis, $wing, $wnor, $wite);
				      break;
				      
				  case "2":    
				  
				      $wresultado=traer_resultado($wtabla, $wcco1[0], $whis, $wing, $wnor, $wite);
				      
				      echo "<tr class='encabezadoTabla' align='center'>";
				      echo "<td align=center><textarea rows=22 cols=140 name=wrdo readonly>".$wresultado."</textarea></td>";              //Muestra el Resultado
				      echo "</tr>";
				      echo "<tr><td><A HREF='Entrega_de_turno_enfermeria.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."&wfec=".$wfec."' class=tipo4V>Ver Imagen</A></td></tr>";
				      
				      break;
				      
				  case "3":
				  
				      $wresultado = traer_resultado($wtabla, $wcco1[0], $whis, $wing, $wnor, $wite);
				      $wresultado_parciado = mostrar_resultado($wresultado);
				      
				      //echo "<tr class='encabezadoTabla' align='center'>";
				      //echo "<td align=center><textarea rows=22 cols=140 name=wrdo readonly>".$wresultado_parciado."</textarea></td>";    //Muestra el Resultado
				      //echo "</tr>";
				      
				      //break;
				  
				      break;
				      
				  default: 
				      echo "<tr class='encabezadoTabla' align='center'>";
				      echo "<td align=center><textarea rows=22 cols=140 name=wrdo readonly>".$row[4]."</textarea></td>";                 //Muestra el Resultado
				      echo "</tr>";
				      break;
		        }  
					
			 echo "</TABLE>";
			 echo "</div>";
			 echo "</td>";
			 echo "</tr>";
			 $i++;
			 $row = mysql_fetch_array($res);
			}
	 	 echo "</tr>";
		 //echo "</table></center>";	
		}
	  echo "</table></center>";	
	 }
echo "<br><br>";
echo "<center><table>";   
echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
echo "</table>";	          
} // if de register

?>