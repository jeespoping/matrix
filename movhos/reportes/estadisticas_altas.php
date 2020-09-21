<head>
  <title>ESTADISTICAS DE ALTAS</title>
  
  <link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
</head>
<body onload=ira()>
<script type="text/javascript">

	$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		dayNamesMin: ['D','L','M','M','J','V','S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['esp']);	

	$(document).ready(function(){
            
		var currentTime = new Date() ;
		
		$("#wfec_i").datepicker({
				
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonText: "Seleccione la fecha inicial",
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			maxDate:currentTime,
			
			onClose: function (selectedDate, instance) {
				if (selectedDate != '') {
					$("#wfec_f").datepicker("option", "minDate", selectedDate);
					var date = $.datepicker.parseDate(instance.settings.dateFormat, selectedDate, instance.settings);
					date.setMonth(date.getMonth() + 1);
					date.setDate(date.getDate() - 1);
					$("#wfec_f").datepicker("option", "minDate", selectedDate);
					$("#wfec_f").datepicker("option", "maxDate", date);
				}
			}
		});
		
		$("#wfec_f").datepicker({
			
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonText: "Seleccione la fecha final",
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			maxDate:currentTime,
			onClose: function (selectedDate) {
			/*$("#wfec_i").datepicker("option", "maxDate", selectedDate);*/
			}
		});
	});
		
		
	function enter()
	{
	 document.forms.estaltas.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     }
</script>	
<?php
include_once("conex.php");
  /***********************************************
   *            ESTADISTICAS DE ALTAS            *
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
 	
  

  

  include_once("root/comun.php");
  
	                                                 // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="Junio 21 de 2017";                    // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                 // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	                                         
	                                         
  //===========================================================================================================================================\\
  //===========================================================================================================================================\\
  //===========================================================================================================================================\\
  //ACTUALIZACIONES                                                                                                                            \\
  //===========================================================================================================================================\\
  // 2020-09-20 Edwin MG.  Se cambia query por que la nueva BD no soporta consulta con fecha vacia
  // ___________________________________________________________________________________________________________________________________________\\
  // 2019-03-05 Arleyda I.C.  Migración realizada
  // ___________________________________________________________________________________________________________________________________________\\
  // 2017-06-21 Jessica Madrid
  // Se agrega filtro de empresa Ccoemp en las dos consultas (Promedio desde <Alta en Proceso> hasta <Alta Definitiva>  y 
  // ***** ** MODA **  POR SERVICIO *****) que incluyen costosyp_000005 ($wtabcco)
  // ___________________________________________________________________________________________________________________________________________\\
  // 23-12-2015:	
  // - Se comenta el query: RESUMEN DE TIEMPOS POR CADA PASO DEL ALTA  y se agrega uno nuevo para que sea más rapida la consulta de los tiempos \\ 
  // Promedio de Alta en Proceso y Alta Definitiva																								\\
  // - Se modifica query: TIEMPOS CAMILLEROS comentando la condicion  "INSTR(habitacion, ubihac) > 0"  y agregando dos condiciones mas:			\\
  //  "motivo IN (".$motivos.")" y "Central IN (".$centrales.")" donde $motivos y $centrales son parametros en la root_000051			   		\\
  // - Se valida la seleccion de un rango de fechas no superior a un mes para generar el reporte.											    \\
  //____________________________________________________________________________________________________________________________________________\\
  // Junio 26 de 2012 En todas las consultas donde se verifica el centro de costo donde se hace la estadistica de las altas se cambio
  //  .(substr($wcco,0,strpos($wcco,'-')-1)). por .trim(substr($wcco,0,strpos($wcco,'-')-0)). porque como se cambio el select llegaba cortado \\
  // el centro de costo y cuando se consulan todos le ponia un espacio despues de %.
  //
  //Junio 20 de 2012
  //Se agregaron las funciones consultaCentrosCostos y dibujarSelect que listan los centros de costos en orden alfabetico, de un grupo seleccionado \\
  // y dibujarSelect que hace el select de dichos centros de costos. Viviana Rodas
  //___________________________________________________________________________________________________________________________________________\\
  //Junio 8 DE 2010:                                                                                                                           \\
  //___________________________________________________________________________________________________________________________________________\\
  //Se modifica el reporte para tome la meta del sistema desde la root_000050 el calculo del cumplimiento sea basado en este dato.             \\
  //___________________________________________________________________________________________________________________________________________\\
  //                                                                                                                                           \\
  //Junio 30 de 2009                                                                                                                           \\
  //===========================================================================================================================================\\
  //Con este reporte se obtiene la estadistica de todo el sistema de altas y se puede medir y evaluar su comportamiento general y por servicios\\ 
  //                                                                                                                                           \\
  //===========================================================================================================================================\\
  //===========================================================================================================================================\\
  //___________________________________________________________________________________________________________________________________________\\
  //O C T U B R E   6   DE 2009:                                                                                                               \\
  //___________________________________________________________________________________________________________________________________________\\
  //Se modifica el reporte para que no tenga en cuenta las 'altas por muerte' y se elimina de los promedios el alta mas demorada, porque la    \\
  //constante es que sea un tiempo exagerado en el alta (Ej:15, 16, 18, 14 horas lo que no es logico), es por eso que se elimina ese limite    \\                                                                                                             
  //superior, no se elimina el inferior porque los ejemplos o casos son razonables en su mayoria.                                              \\
  //                                                                                                                                           \\
  //___________________________________________________________________________________________________________________________________________\\                     
	               
  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           
	                                                                                                       
  
  $q = " SELECT empdes, empmsa "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  $wmeta_sist_altas=$row[1];  //Esta es la meta en tiempo promedio para las altas
  
  
  $q = " SELECT (HOUR(TIMEDIFF(empmsa,'00:00:00'))*60) + MINUTE(TIMEDIFF(empmsa,'00:00:00')) "
      ."   FROM root_000050 "
      ."  WHERE empcod = '01' "
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res);
  
  $wmeta_minutos=$row[0];      //Meta en minutos
  
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
	         
	      if ($row[0] == "camilleros")
	         $wcencam=$row[1];   
	         
	      $winstitucion=$row[2];   
         }  
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
  
  
  encabezado("ESTADISTICAS SISTEMA DE ALTAS",$wactualiz, "clinica");
  
  
  function marcar_alta_mas_demorada_por_dia()
     {
	  global $wbasedato;
	  global $wfec_i;
	  global $wfec_f;
	  global $dias;
	  global $conex;
	  global $dias;
	  global $wfecha;
	  
	  
	  $wdias=diasDiferenciaFechas($wfec_i,$wfec_f);
	  
	  $wdias=abs($wdias)+1;  //Le sumo 1 a la diferencia de las fecha para que tome el dia inicial
      
	  for ($i=0;$i<=($wdias-1);$i++)
	     {
		  //Esto porque se puede correr el reporte mas de una vez en el dia y pueden haber altas mas demoradas cada vez que se corra.
		  //Si el reporte se corre en el dia X y se marca un alta como la mas demorada a esa hora que se corra, pero no se vuelve a correr
		  //el reporte y luego puede haber otra mas demorada, entonces esta no quedaria marcada, por eso siempre se busca la mas demorada
		  //al momento de correr el reporte y se anula las anteriores marcas que puedan haber.
		  //Abajo en el UPDATE se marcaria la mas demorada.   
		  $q = " UPDATE ".$wbasedato."_000018 "
	          ."    SET ubiamd = '' "
		      ."  WHERE ubifad  = ADDDATE('".$wfec_i."',".$i.") "
		      ."    AND ubiald  = 'on' "
		      ."    AND ubifap  = ubifad "
		      ."    AND ubimue != 'on' ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
		     
		     
	  	  $q =  " SELECT MAX(TIMEDIFF(ubihad,ubihap)), ubihis, ubiing, ubifad "
		       ."   FROM ".$wbasedato."_000018 "
		       ."  WHERE ubifad  = ADDDATE('".$wfec_i."',".$i.") "
		       ."    AND ubiald  = 'on' "
		       ."    AND ubifap  = ubifad "
		       ."    AND ubimue != 'on' "
		       ."  GROUP BY 2,3,4 "
		       ."  ORDER BY 1 DESC LIMIT 1 ";   //Se limita a un solo regisro
		   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		   $num = mysql_num_rows($res);    
	   
		   If ($num>=0)
		      {
			      
			   $row = mysql_fetch_array($res);
			   
			   $q = " UPDATE ".$wbasedato."_000018 "
			       ."    SET ubiamd = 'on' "
			       ."  WHERE ubihis  = '".$row[1]."'"
			       ."    AND ubiing  = '".$row[2]."'"
			       ."    AND ubifad  = '".( empty( $row[3] ) ? '0000-00-00' : $row[3] )."'"	//2020-09-20 Se modifica por cambio de BD, la fecha no puede ser vacia
			       ."    AND ubiald  = 'on' "
			       ."    AND ubiamd != 'on' "
			       ."    AND ubimue != 'on' ";
			   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());       
			  }
	     }	      
	 }    
  
  
  //FORMA ================================================================
  echo "<form name='altas' action='estadisticas_altas.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  
  
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 

  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
   if (!isset($wfec_i) or trim($wfec_i) == "" or !isset($wfec_f) or trim($wfec_f) == "" or !isset($wcco) or trim($wcco) == "" )
     {
	  
	  $fecha_actual=date("Y-m-d");
	  $cco="Ccohos";
	  $sub="off";
	  $tod="Todos";
	  //$cco=" ";
	  $ipod="off";
	  $centrosCostos = consultaCentrosCostos($cco);
	  echo "<table align='center' border=0 >";
	  $dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);
					
	  echo $dib; 
	  echo "</table>";
	  
	  echo "<center><table cellspacing=1>";
      
      echo "<br>";
	  
	  echo "<tr class=seccion1>";
      echo "<td align=center><b>Fecha Inicial</b><br>";
      echo "<input type='text' id='wfec_i' name='wfec_i' size='11' value=".$fecha_actual.">";
      
      // campoFecha("wfec_i");
      echo "</td>";
      echo "<td align=center><b>Fecha Final</b><br>";
	  echo "<input type='text' id='wfec_f' name='wfec_f' size='11' value=".$fecha_actual.">";
      // campoFecha("wfec_f");
      echo "</td>";
      echo "</tr>";
      
	  echo "<tr><td align=center bgcolor=cccccc colspan=2></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
	  echo "</table>";
     }
    else 
      { 
	  
	   echo "<center><table cellspacing=1>";
	   
	   echo "<th align=center colspan=4 class=titulo><b>ESTADISTICAS TIEMPOS DE ALTAS POR SERVICIO</b></th>";
	   
	   echo "<tr class=seccion1>";
	   echo "<th align=center><b>Fecha Inicial</b></th>";
	   echo "<th align=center colspan=3><b>Fecha Final</b></th>";
	   echo "</tr>";	   
	   
	   echo "<tr class=seccion2>";
	   echo "<td align=center><b>".$wfec_i."</b></td>";
	   echo "<td align=center colspan=3><b>".$wfec_f."</b></td>";
	   echo "</tr>";
	   
	   echo "<tr class=fila1><td align=center colspan=3><b><font size=4>META (HH:MM:SS) </font><font size=5>".$wmeta_sist_altas."</font></b></td></tr>";
	   
	   //Se va a marcar de cada dia del rango cual es el alta mas demorada, para que no sean incluidas en las estadisticas
	   marcar_alta_mas_demorada_por_dia();
	   
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //***** ALTA MAS DEMORADA *****
	   //Este registro se calcula al principio para excluirlo de los promedios.
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   $q = " SELECT MAX(TIMEDIFF(ubihad,ubihap)), ubisac, ubihis, ubiing "
	       ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011 "
	       ."  WHERE ubifad  BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND ubisac  LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND ubiald  = 'on' "
	       ."    AND ubifap  = ubifad "
	       ."    AND ubisac  = ccocod "
	       ."    AND ccohos  = 'on' "
	       ."    AND ccopal  = 'on' "    //Indica que si promedio en las Altas
	       ."    AND ubimue != 'on' "    //Oct 6 2009
	       //."    AND ubiamd  = 'on' "
	       ."  GROUP BY 2,3,4 "
	       ."  ORDER BY 1 DESC LIMIT 1";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   if ($num > 0)
	      {
		   $row = mysql_fetch_array($res);
		   
		   $wtiempo_alta_mas_demorada=$row[0];
		   $wcco_alta_mas_demorada=$row[1];
		   $whis_alta_mas_demorada=$row[2];
		   $wing_alta_mas_demorada=$row[3];
		  
		   //mas abajo se imprime esta informacion 
		  }    
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   
	   
	   //========================================================================================================   
	   //Promedio desde <Alta en Proceso> hasta <Alta Definitiva>  
	   $q = " SELECT SUM(HOUR(TIMEDIFF(ubihad,ubihap))), "
	       ."        SUM(MINUTE(TIMEDIFF(ubihad,ubihap))), "
	       ."        SUM(SECOND(TIMEDIFF(ubihad,ubihap))), "
	       ."        COUNT(*), ubisac, ".$wtabcco.".cconom "
	       ."   FROM ".$wbasedato."_000018, ".$wtabcco.", ".$wbasedato."_000011 "
	       ."  WHERE ubifad              BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND ubisac              LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND ubiald              = 'on' "
	       ."    AND ubifap              = ubifad "
	       ."    AND ubisac              = ".$wtabcco.".ccocod "
	       ."    AND ".$wtabcco.".ccoemp = '".$wemp_pmla."'"
	       ."    AND ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
	       ."    AND ccohos              = 'on' "
	       ."    AND ccopal              = 'on' "    //Indica que si promedio en las Altas
	       ."    AND ubimue             != 'on' "
	       ."    AND ubiamd             != 'on' "                                                     //Oct 6 2009
	       //."    AND CONCAT(ubihis,ubiing) != '".$whis_alta_mas_demorada.$wing_alta_mas_demorada."'"  //Oct 6 2009
	       ."  GROUP BY 5, 6 ";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   if ($num > 0)
	      {
		   echo "<tr class=encabezadoTabla>";
		   echo "<td align=center><b>Servicio</b></td>";
		   echo "<td align=center><b>Promedio Alta</b></td>";
		   echo "<td align=center><b>Cant. Altas</b></td>";
		   echo "</tr>";
	      
		   $wtotgenseg=0;
		   $wtotgenalt=0;
		   for ($i=1;$i<=$num;$i++)
		      {   
			   if (is_integer($i/2))
                  $wclass="fila1";
                 else
                    $wclass="fila2";     
			      
		       $row = mysql_fetch_array($res);
			     
		       echo "<tr class=".$wclass.">";
	   		   echo "<td align=left><b>".$row[4]." - ".$row[5]."</b></td>";
			   
		       $wtotseg = $row[0]*3600;                                         //Horas
			   $wtotseg = $wtotseg+($row[1]*60);                                //Minutos
			   $wtotseg = $wtotseg+($row[2]);                                   //Segundos
			     
			   $wproaltser=($wtotseg/$row[3]);                                  //Total segundos dividido el numero de altas
		       $wproaltser=number_format(($wproaltser/3600),2,'.',',');         //Promedio por alta dividido 3600 segundos que equivale a una hora
		       $wpro=explode(".",$wproaltser);
		       
		       $wprohor=$wpro[0];
		       $wpromin=number_format((((3600*$wpro[1])/100)/60),0,'.',',');    //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltser
			     
		       $wtotgenseg=$wtotgenseg+$wtotseg;
		       $wtotgenalt=$wtotgenalt+$row[3];
		       
		       $wproaltgen=($wtotgenseg/$wtotgenalt);                           //Total segundos dividido el numero de altas
		       $wproaltgen=number_format(($wproaltgen/3600),2,'.',',');         //Promedio por alta dividido 3600 segundos que equivale a una hora
		       
		       echo "<td align=center><b>".$wprohor." Horas, ".$wpromin." Minutos</b></td>";
		       echo "<td align=right ><b>".number_format($row[3],0,'.',',')."</b></td>";
		       echo "</tr>";
		      }
	          
	        $wprogen=explode(".",$wproaltgen);
		       
		    $wprohorgen=$wprogen[0];
		    $wpromingen=number_format((((3600*$wprogen[1])/100)/60),0,'.',',');  //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltser
		         
		    echo "<tr class=encabezadoTabla>";
	        echo "<td colspan=1 align=center><font size=4><b>Promedio y Cantidad total</b></font></td>";
	        echo "<td align=center><font size=5><b>".$wprohorgen." Horas, ".$wpromingen." Minutos</b></font></td>";
	        echo "<td align=right><font size=4><b>".number_format($wtotgenalt,0,'.',',')."</b></font></td>";
	        echo "</tr>";  
	      } 
	     else
	        {
	         echo "<tr class=seccion1>";
	         echo "<td alig=center><b>Sin Datos</b></td>";
	         echo "</tr>"; 
            } 
	    
	   echo "</table>";         
	   //========================================================================================================
	   
	   
	   
	   //Oct 6 2009
	   //========================================================================================================
	   //Total altas en el Periodo INCLUIDAS o NO en el PROMEDIO  
	   //========================================================================================================
	   $q = " SELECT COUNT(*) "
	       ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011 "
	       ."  WHERE ubifad BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND ubisac LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND ubiald = 'on' "
	       ."    AND ubisac = ccocod "
	       ."    AND ccohos = 'on' "
	       ."    AND ccopal = 'on' ";       //Indica que si promedio en las Altas
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   if ($num > 0)
	      {
		   $row = mysql_fetch_array($res);
		      
		   
		   //Oct 6 2009
		   echo "<br>";   
		   echo "<center><table cellspacing=1>";
		   echo "<tr class=seccion1>";
		   echo "<td align=left  ><font size=4><b>Total Altas en el Período (Incluye todas): </b></font></td>";
		   echo "<td align=center colspan=2><font size=4><b>[ ".number_format($row[0],0,'.',',')." ]</b></font></td>";
		   echo "</tr>"; 
		   echo "<tr class=seccion1>";
		   echo "<td align=left  ><font size=4><b>Altas NO promediadas (Por muerte, porque pasan de un día a otro o por ser la más demorada): </b></font></td>";
		   echo "<td align=center><font size=4><b>[ ".number_format(($row[0]-$wtotgenalt),0,'.',',')." ]</b></font></td>";
		   echo "<td align=center><font size=4><b>[ ".number_format(((($row[0]-$wtotgenalt)/$row[0])*100),1,'.',',')." % ]</b></font></td>";
		   echo "</tr>";
	      } 
	     else
	        {
	         echo "<tr class=seccion1>";
	         echo "<td alig=center><b>Sin Datos</b></td>";
	         echo "</tr>"; 
            } 
	    
	   echo "</table>";         
	   //========================================================================================================
	   
	   
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //***** ALTAS POR ENCIMA DEL PROMEDIO *****
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   $q = " SELECT COUNT(*) "
	       ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011 "
	       ."  WHERE ubifad                 BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND ubisac                 LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND ubiald                 = 'on' "
	       ."    AND ubifap                 = ubifad "
	       ."    AND ubisac                 = ccocod "
	       ."    AND ccohos                 = 'on' "
	       ."    AND ccopal                 = 'on' "    //Indica que si promedio en las Altas
	       ."    AND (HOUR(TIMEDIFF(ubihad,ubihap))*3600)+(MINUTE(TIMEDIFF(ubihad,ubihap))*60)+(SECOND(TIMEDIFF(ubihad,ubihap))) >= ".(($wprohorgen*3600)+($wpromingen*60))
	       ."    AND ubimue                != 'on' "                                                  //Oct 6 2009
	       ."    AND ubiamd                != 'on' ";
	       //."    AND CONCAT(ubihis,ubiing) != '".$whis_alta_mas_demorada.$wing_alta_mas_demorada."'"; //Oct 6 2009
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   if ($num > 0)
	      {
		   $row = mysql_fetch_array($res);
		   
		   echo "<br>";   
		   echo "<center><table cellspacing=1>";
		   echo "<tr class=seccion1>";
		   echo "<td align=left  ><font size=4><b>Cantidad de Altas por encima del Promedio: </b></font></td>";
		   echo "<td align=center><font size=4><b>[".$row[0]."]</b></font></td>";
		   echo "<td align=center><font size=4><b>[".number_format(($row[0]/$wtotgenalt)*100,2,'.',',')."% ]</b></font></td>";
		   echo "</tr>"; 
		   //echo "</table>"; 
	      }    
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //***** ALTA MAS DEMORADA *****   
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //Oct 6 2009
	   echo "<tr class=seccion1>";
	   echo "<td align=left  ><font size=4><b>Alta más demorada (NO afecta el promedio, NO incluye muertes): </b></font></td>";
	   echo "<td align=center><font size=4><b>[ ".$wtiempo_alta_mas_demorada." ]</b></font></td>";
	   echo "<td align=center><font size=4><b>Servicio: ".$wcco_alta_mas_demorada." Historia: ".$whis_alta_mas_demorada."-".$wing_alta_mas_demorada."</b></font></td>";
	   echo "</tr>"; 
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   
	   
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //***** ALTA MAS RAPIDA *****
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   $q = " SELECT MIN(TIMEDIFF(ubihad,ubihap)), ubisac, ubihis, ubiing "
	       ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011 "
	       ."  WHERE ubifad                            BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND ubisac                            LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND ubiald                            = 'on' "
	       ."    AND ubifap                            = ubifad "
	       ."    AND ubisac                            = ccocod "
	       ."    AND ccohos                            = 'on' "
	       ."    AND ccopal                            = 'on' "    //Indica que si promedio en las Altas
	       ."    AND (HOUR(TIMEDIFF(ubihad,ubihap))   >= 1 "
	       ."     OR  MINUTE(TIMEDIFF(ubihad,ubihap)) > 2) "
	       ."    AND ubimue                           != 'on' "   //Oct 6 2009
	       ."  GROUP BY 2,3,4 "
	       ."  ORDER BY 1 ";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   if ($num > 0)
	      {
		   $row = mysql_fetch_array($res);
		   
		   echo "<tr class=seccion1>";
		   echo "<td align=left  ><font size=4><b>Alta más Rápida: </b></font></td>";
		   echo "<td align=center><font size=4><b>[ ".$row[0]." ]</b></font></td>";
		   echo "<td align=center><font size=4><b>Servicio: ".$row[1]." Historia: ".$row[2]."-".$row[3]."</b></font></td>";
		   echo "</tr>"; 
		   echo "</table>"; 
	      }    
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   
	   
	   
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //***** ** MODA **  POR SERVICIO *****
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   $q= " SELECT SUM((HOUR(TIMEDIFF(ubihad,ubihap))*60*60)+ "
          ."            (MINUTE(TIMEDIFF(ubihad,ubihap))*60)+ "
          ."            (SECOND(TIMEDIFF(ubihad,ubihap)))), "
          ."            ubihis, ubiing, ubisac, ".$wtabcco.".cconom "
          ."   FROM movhos_000018, ".$wtabcco.", ".$wbasedato."_000011 "
          ."  WHERE ubifad                 BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
          ."    AND ubisac                 LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
          ."    AND ubiald                 = 'on' "
          ."    AND ubifap                 = ubifad "
          ."    AND ubisac                 = ".$wbasedato."_000011.ccocod "
          ."    AND ccohos                 = 'on' "
          ."    AND ccopal                 = 'on' "    //Indica que si promedio en las Altas
		  ."    AND ".$wtabcco.".ccoemp    = '".$wemp_pmla."'"
          ."    AND ".$wtabcco.".ccocod    = ".$wbasedato."_000011.ccocod "
          ."    AND ubimue                != 'on' "                                                  //Oct 6 2009
          ."    AND ubiamd                != 'on' "
          //."    AND CONCAT(ubihis,ubiing) != '".$whis_alta_mas_demorada.$wing_alta_mas_demorada."'"  //No tengo en cuenta el alta mas demorada //Oct 6 2009
          ."  GROUP BY 2, 3, 4, 5 "
          ."  ORDER BY 4 ";  
       $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   if ($num > 0)
	      {
		   echo "<br><br>";   
	       echo "<center><table cellspacing=1>";   
	       
	       $totgen00_30  =0;     //0:00 min   a 30 minutos
	       $totgen31_60  =0;     //0:31 min   a 1:00 horas
	       $totgen61_90  =0;     //1:01 horas a 1:30 horas
	       $totgen91_120 =0;     //1:31 horas a 2:00 horas
	       $totgen121_150=0;     //2:01 horas a 2:30 horas
	       $totgen151_180=0;     //2:31 horas a 3:00 horas
	       $totgen181_210=0;     //3:01 horas a 3:30 horas
	       $totgen211_240=0;     //3:31 horas a 4:00 horas
	       $totgen241_270=0;     //4:01 horas a 4:30 horas
	       $totgen271_300=0;     //4:31 horas a 5:00 horas
	       $totgenmas_300=0;     //Mas de 5 horas
	       
	       $row = mysql_fetch_array($res);
	       
	       //==============================================
	       //Inicializo el arreglo
	       //==============================================
	       for ($i=1;$i<=$num;$i++)
	          {
		       $wtotcump[$i]=0;   
	           for ($j=0;$j<=13;$j++)
	              $wmoda[$i][$j]=0;
              }    
	       
	       for ($i=2;$i<=12;$i++)
	          {
	           $wtotran[$i]=0;
	          } 
	       //==============================================      
	       
	       $j=1;
	       
	       
	       $totcco00_30  =0;     //0:00 min   a 30 minutos
	       $totcco31_60  =0;     //0:31 min   a 1:00 horas
	       $totcco61_90  =0;     //1:01 horas a 1:30 horas
	       $totcco91_120 =0;     //1:31 horas a 2:00 horas
	       $totcco121_150=0;     //2:01 horas a 2:30 horas
	       $totcco151_180=0;     //2:31 horas a 3:00 horas
	       $totcco181_210=0;     //3:01 horas a 3:30 horas
	       $totcco211_240=0;     //3:31 horas a 4:00 horas
	       $totcco241_270=0;     //4:01 horas a 4:30 horas
	       $totcco271_300=0;     //4:31 horas a 5:00 horas
	       $totccomas_300=0;     //Mas de 5 horas 
	       
	       $wtotgen_cump =0;
	       
	       $i=1;
	       while ($i <= $num)
		      {   
			   $wccoant=$row[3];
		       
		       $wmoda[$j][0]=$row[3];    //Codigo Centro de costo
		       $wmoda[$j][1]=$row[4];    //Nombre Centro de costo
		       
		       while ($i<=$num and $row[3]==$wccoant)
		            {
			         $wminutos=($row[0]/60);
			         
			         if ($wminutos <= $wmeta_minutos)
			            $wtotcump[$j]++;
			         
			         switch (TRUE)
		               {
		                case ($wminutos >=0 and $wminutos <= 30):
		                   $wmoda[$j][2]++;
		                   $wtotran[2]++; 
		                   
		                   break;
		                case ($wminutos > 30 and $wminutos <=60):
		                   $wmoda[$j][3]++;
		                   $wtotran[3]++; 
		                   break; 
		                case ($wminutos > 60 and $wminutos <=90):
		                   $wmoda[$j][4]++;
		                   $wtotran[4]++; 
		                   break;
		                case ($wminutos > 90 and $wminutos <=120):
		                   $wmoda[$j][5]++;
		                   $wtotran[5]++;   
		                   break; 
		                case ($wminutos > 120 and $wminutos <=150):
		                   $wmoda[$j][6]++;
		                   $wtotran[6]++;   
		                   break;  
		                case ($wminutos > 150 and $wminutos <=180):
		                   $wmoda[$j][7]++;
		                   $wtotran[7]++;   
		                   break;
		                case ($wminutos > 180 and $wminutos <=210):
		                   $wmoda[$j][8]++;
		                   $wtotran[8]++;  
		                   break;
		                case ($wminutos > 210 and $wminutos <=240):
		                   $wmoda[$j][9]++;
		                   $wtotran[9]++;   
		                   break;
		                case ($wminutos > 240 and $wminutos <=270):
		                   $wmoda[$j][10]++;
		                   $wtotran[10]++; 
		                   break;
		                case ($wminutos > 270 and $wminutos <=300):
		                   $wmoda[$j][11]++;
		                   $wtotran[11]++;  
		                   break;
		                case ($wminutos > 300):
		                   $wmoda[$j][12]++;
		                   $wtotran[12]++;   
		                   break; 
		               }
		               
		             $wmoda[$j][13]++;      //Aca se totaliza cada centro de costo
		               
	                 $i++;
	                 $row = mysql_fetch_array($res); 
	                } 
	           $j++; 
	          }
	       
	       echo "<tr class=encabezadoTabla>";
		   echo "<td align=center rowspan=3 colspan=2><font size=4><b>Servicio</b></font></td>";
		   echo "<td align=center colspan=13><font size=4><b>MODA por Servicio</b></font></td>";
		   echo "</tr>";
		   
		   echo "<tr class=encabezadoTabla>";
		   echo "<td align=center colspan=13><font size=4><b>Rango en horas</b></font></td>";
		   echo "</tr>";
		   
		   echo "<tr class=encabezadoTabla>";
		   echo "<td align=center><b>0:00-0:30</b></td>";
		   echo "<td align=center><b>0:30-1:00</b></td>";
		   echo "<td align=center><b>1:00-1:30</b></td>";
		   echo "<td align=center><b>1:30-2:00</b></td>";
		   echo "<td align=center><b>2:00-2:30</b></td>";
		   echo "<td align=center><b>2:30-3:00</b></td>";
		   echo "<td align=center><b>3:00-3:30</b></td>";
		   echo "<td align=center><b>3:30-4:00</b></td>";
		   echo "<td align=center><b>4:00-4:30</b></td>";
		   echo "<td align=center><b>4:30-5:00</b></td>";
		   echo "<td align=center><b>+ de 5:00</b></td>";
		   echo "<td align=center><b>Total</b></td>";
		   echo "<td align=center><b>% de<br>Cumplimiento</b></td>";
		   echo "</tr>";   
		   
		   //BUSCO LA MODA POR SERVICIO Y TOTAL
		   for ($i=1;$i<=$j-1;$i++)
	          {
		       $wmoda_cco[$i]=0;
		       $wtot_mod=0;
		       for ($k=0;$k<=12;$k++)
		          {
			       //Aca tomo la MODA para cada servicio CCO.   
	               if ($wmoda[$i][$k] > $wmoda_cco[$i])                  //Comparo la moda actual en el arreglo con la anterior.
		              $wmoda_cco[$i]=$wmoda[$i][$k];
		              
		           //Aca tomo la MODA total o general de la clinica.   
	               if (($k > 1) and ($wtotran[$k] > $wtot_mod))          //Comparo la moda anterior por rango de tiempo con la del actual rango en el vector
		              $wtot_mod=$wtotran[$k];   
	              }    
		      }
		   
		   
		   $wtotgen=0;   //total general de altas
	       for ($i=1;$i<=$j-1;$i++)
	          {
		       if (is_integer($i/2))
                  $wclass="fila1";
                 else
                    $wclass="fila2"; 
           
               echo "<tr class=".$wclass.">";                    
		       for ($k=0;$k<=12;$k++)
	              {
		           if ($wmoda[$i][$k] > 0 and ($k > 1))
		              {
			           if ($wmoda_cco[$i]==$wmoda[$i][$k])   
		                  echo "<td align=center bgcolor=FFFF00><b>".$wmoda[$i][$k]."</b></td>";
		                 else
		                    echo "<td align=center><b>".$wmoda[$i][$k]."</b></td>"; 
	                  } 
		             else
		               {
		                if ($k==0 or $k==1)
		                   echo "<td align=left><b>".$wmoda[$i][$k]."</b></td>";
		                  else 
		                     echo "<td><b>&nbsp</b></td>"; 
	                   }      
		          }
		          
		       $wtotgen=$wtotgen+$wmoda[$i][13];   
		       echo "<td align=right><b>".$wmoda[$i][13]."</b></td>";                                                                                   //Total de altas de cada SERVICIO
		       //echo "<td align=right><b>".number_format(((($wmoda[$i][2]+$wmoda[$i][3]+$wmoda[$i][4])/$wmoda[$i][13])*100),1,'.',',')." %</b></td>";  //% de Cumplimiento
		       
		       echo "<td align=right><b>".number_format(((($wtotcump[$i])/$wmoda[$i][13])*100),1,'.',',')." %</b></td>";                                      //% de Cumplimiento por CCO
	           echo "</tr>";
	           
	           $wtotgen_cump=$wtotgen_cump + $wtotcump[$i];
              }
           echo "<tr class=encabezadoTabla>";
		   echo "<td align=center colspan=2><b>Total</b></font></td>";
		   for ($i=2;$i<=12;$i++)
		      if ($wtotran[$i]==$wtot_mod)
		         echo "<td align=center bgcolor=FFFF00><font color=2A5DB0><b>".$wtotran[$i]."</b></font></td>";                                       //MODA DE LA CLINICA
		        else
		           echo "<td align=center><b>".$wtotran[$i]."</b></font></td>"; 
		   echo "<td align=right><b>".$wtotgen."</b></td>";                                                                                           //Total de altas CLINICA
		   //echo "<td align=right><b>".number_format(((($wtotran[2]+$wtotran[3]+$wtotran[4])/$wtotgen)*100),1,'.',',')." %</b></td>";                //% de Cumplimiento
		   echo "<td align=right><b>".number_format((($wtotgen_cump/$wtotgen)*100),1,'.',',')." %</b></td>";                                          //% de Cumplimiento General
		   echo "</tr>"; 
           echo "</table>"; 
	      }    
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   
	   
	   
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   //***** RESUMEN DE TIEMPOS POR CADA PASO DEL ALTA *****
	   //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/
	   echo "<br><br>";   
	   echo "<center><table cellspacing=1>";
	   echo "<tr class=encabezadoTabla>";
	   echo "<td align=center rowspan=5><font size=4><b>Servicio</b></font></td>";
	   echo "<td align=center colspan=14><font size=4><b>Tiempo Promedio en cada paso del Alta</b></font></td>";
	   echo "</tr>";
	   echo "<tr class=encabezadoTabla>";
	   echo "<td align=center colspan=14 class=fila1><font size=4><b>Tiempo en HH:MM</b></font></td>";
	   echo "</tr>";
	   
	   
	   echo "<tr>";
	   echo "<td align=center colspan=4 class=fila1><font size=4><b>Alta en Proceso</b></font></td>";
	   echo "<td align=center colspan=4 rowspan=2 class=fila2><font size=4><b>Facturación</b></font></td>";
	   echo "<td align=center colspan=2 rowspan=2 class=fila1><font size=4><b>Caja</b></font></td>";
	   echo "<td align=center colspan=2 rowspan=2 class=fila2><font size=4><b>Alta Definitiva</b></font></td>";
	   echo "<td align=center colspan=2 rowspan=2 class=fila1><font size=4><b>Camillero</b></font></td>";
	   echo "</tr>";
	   
	   
	   echo "<tr>";
	   echo "<td align=center colspan=2 rowspan=1 class=fila2><font size=4><b>Devol. Antes</b></font></td>";
	   echo "<td align=center colspan=2 rowspan=1 class=fila1><font size=4><b>Devol. Despues</b></font></td>";
	   echo "</tr>";
	   
	   echo "<tr class=fila1>";
	   echo "<td align=center><b>Cant.</b></td>";
	   echo "<td align=center><b>Tiempo</b></td>";
	   echo "<td align=center><b>Cant.</b></td>";
	   echo "<td align=center><b>Tiempo</b></td>";
	   echo "<td align=center><b>Cant.</b></td>";
	   echo "<td align=center><b>Tiempo entre <br>Devol. y Fact</b></td>";
	   echo "<td align=center><b>Cant.</b></td>";
	   echo "<td align=center><b>Tiempo en <br>Facturar</b></td>";
	   echo "<td align=center><b>Cant.</b></td>";
	   echo "<td align=center><b>Tiempo</b></td>";
	   echo "<td align=center><b>Cant.</b></td>";
	   echo "<td align=center><b>Tiempo</b></td>";
	   echo "<td align=center><b>Cant.</b></td>";
	   echo "<td align=center><b>Tiempo</b></td>";
	   echo "</tr>";
	   
	   
	   //====================================================================
	   //=== OJO ** OJO ** OJO ====
	   //====================================================================
	   //Otra forma de hacer el Query seria esta:
	   //====================================================================
	   /*
	    CREATE TEMPORARY TABLE if not exists tempo1 AS
		SELECT denhis his1, dening ing1, MAX(movhos_000035.hora_data) hor1                                                        
		   FROM movhos_000018, movhos_000035
		WHERE ubifad = movhos_000035.fecha_data
		     AND ubifad BETWEEN '2009-06-01' AND '2009-06-24' 
		GROUP BY 1, 2;

		CREATE TEMPORARY TABLE if not exists tempo2 AS
		SELECT his1 his, ing1 ing, hor1 hora
		   FROM movhos_000018, tempo1
		WHERE ubihis = his1 
		     AND ubiing = ing1 
		     AND ubifad BETWEEN '2009-06-01' AND '2009-06-24' 
		     AND ubiald = 'on' 
		     AND ubifap = ubifad 
		     AND ubihap > hor1 
		GROUP BY 1, 2, 3;


		SELECT SUM(HOUR(TIMEDIFF(A.hora, ubihap))), 
		              SUM(MINUTE(TIMEDIFF(A.hora, ubihap))), 
		              SUM(SECOND(TIMEDIFF(A.hora, ubihap))), 
		              COUNT(*), 
		              ubisac 
		   FROM movhos_000018,movhos_000011, tempo2
		  WHERE ubifad BETWEEN '2009-06-01' AND '2009-06-24' 
		        AND ubisac LIKE '%' 
		        AND ubiald = 'on' 
		        AND ubifap = ubifad 
		        AND ubisac = ccocod 
		        AND ccohos = 'on' 
		        AND ubihis = A.his 
		        AND ubiing = A.ing 
		   GROUP BY 5 
		   ORDER BY 5
       */
       //====================================================================
	   
	   
	   //==================================================================================================================================   
	   //Tiempos Promedio desde <Alta en Proceso> hasta <Alta Definitiva> en cada paso
	   //==================================================================================================================================
	   //==================================================================================================================================
	   // //*** DEVOLUCION ** ANTES ** DEL ALTA (Cuando hacen primero la devolucion y luego colocan el Alta en Proceso) ***
	   // $q= "   SELECT SUM(HOUR(TIMEDIFF(A.hora, ubihap))), "
	      // ."                SUM(MINUTE(TIMEDIFF(A.hora, ubihap))), "
	      // ."                SUM(SECOND(TIMEDIFF(A.hora, ubihap))), "
	      // ."                COUNT(*), ubisac "                       //Solo Tomo la historia cuya hora de alta de en proceso sea mayor a la hora maxima de la devolucion
	      // ."     FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ( SELECT his1 his, ing1 ing, hor1 hora "   
	                                                                                                // //Tomo la hora maxima de las devoluciones en esa fecha para cada historia
	      // ."                                                            FROM ".$wbasedato."_000018, (SELECT denhis his1, dening ing1, MAX(".$wbasedato."_000035.hora_data) hor1 "
	      // ."                          		                                                           FROM ".$wbasedato."_000018, ".$wbasedato."_000035 "
	      // ."                         		                                                          WHERE ubifad = ".$wbasedato."_000035.fecha_data "
	      // ."                           		                                                            AND ubifad BETWEEN '".$wfec_i."' AND '".$wfec_f."'" 
	      // ."                         		                                                          GROUP BY 1, 2) B "
	      // ."                                                           WHERE ubihis = his1 "
	      // ."                                                             AND ubiing = ing1 "
	      // ."                                                             AND ubifad BETWEEN '".$wfec_i."' AND '".$wfec_f."'"  
	      // ."    							                             AND ubiald = 'on' "
	      // ."    							                             AND ubifap = ubifad "
	      // ."                                                             AND ubihap > hor1 "     //Solo trae las devoluciones cuya alta en proceso fue ** despues ** de la devolución
	      // ."  								                           GROUP BY 1, 2, 3 ) A "
	      // ."   WHERE ubifad  BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	      // ."     AND ubisac  LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	      // ."     AND ubiald  = 'on' "
	      // ."     AND ubifap  = ubifad "
	      // ."     AND ubisac  = ccocod " 
	      // ."     AND ccohos  = 'on' "
	      // ."     AND ccopal  = 'on' "    //Indica que si promedio en las Altas
	      // ."     AND ubihis  = A.his "    //Solo tomo las historias de la tablla A
	      // ."     AND ubiing  = A.ing "
	      // ."     AND ubimue != 'on' "                                                                 //Oct 6 2009
	      // ."     AND ubiamd != 'on' "
	      // //."     AND CONCAT(ubihis,ubiing) != '".$whis_alta_mas_demorada.$wing_alta_mas_demorada."'"  //No tengo en cuenta el alta mas demorada //Oct 6 2009
	      // ."   GROUP BY 5 "
	      // ."   ORDER BY 5 ";
		  
		    // // **************************************************************************************
		  
		   $q="SELECT SUM(HOUR(TIMEDIFF(A.hora, ubihap))), SUM(MINUTE(TIMEDIFF(A.hora, ubihap))), SUM(SECOND(TIMEDIFF(A.hora, ubihap))), COUNT(*), ubisac
				 FROM ".$wbasedato."_000018, ".$wbasedato."_000011, 
					  ( SELECT his1 his, ing1 ing, hor1 hora 
						  FROM ".$wbasedato."_000018,
							   (SELECT denhis his1, dening ing1, MAX(hora_data) hor1 
								  FROM ".$wbasedato."_000035 
								 WHERE fecha_data BETWEEN '".$wfec_i."' AND '".$wfec_f."' 
								 GROUP BY 1, 2) B 
						 WHERE ubihis = his1 
						   AND ubiing = ing1 
						   AND ubifad BETWEEN '".$wfec_i."' AND '".$wfec_f."' 
						   AND ubiald = 'on' 
						   AND ubifap = ubifad 
						   AND ubihap > hor1 
						 GROUP BY 1, 2, 3 ) A 
				WHERE ubifad BETWEEN '".$wfec_i."' AND '".$wfec_f."'
				  AND ubisac LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."' 
				  AND ubiald = 'on' 
				  AND ubifap = ubifad 
				  AND ubisac = ccocod 
				  AND ccohos = 'on' 
				  AND ccopal = 'on' 
				  AND ubihis = A.his 
				  AND ubiing = A.ing 
				  AND ubimue != 'on' 
				  AND ubiamd != 'on' 
				GROUP BY 5 
				ORDER BY 5";
				
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   
	   
	   //====================================
	   //Inicializo el arreglo
	   //====================================
	   $wtotfil=100;   //Filas del arreglo    (alcanzaria para 100 cco o servicios)
	   $wtotcol=30;    //Columnas del arreglo (alcanzaria para 15 etapas del alta, cada etapa se toma 2 columnas)
	   for ($i=1;$i<=$wtotfil;$i++)
	      for ($j=0;$j<=$wtotcol;$j++)
	          $warr[$i][$j]=0;
	   //====================================
	   
	   
	   if ($num > 0)
	      {
		   $wtotnum=$num;   
		      
		   $wtotand=$num;       //Total registros Antes del alta
		   
		   $wtotgensegand=0;    //total General segundos Antes de la Devolucion
		   $wtotgenaltand=0;    //total General Altas Antes de la Devolucion
		   for ($i=1;$i<=$num;$i++)
		      {   
		       $row = mysql_fetch_array($res);
		       
		       $warr[$i][0]=$row[4];                                                  //Cco
			   
			   if (isset($row[3]))
		          $warr[$i][1]=$row[3];                                               //Cantidad de altas definitivas con Devolucion antes de Alta en proceso
		         else
		            $warr[$i][1]=0; 
		       
		       $wtotsegand = $row[0]*3600;                                            //Horas
			   $wtotsegand = $wtotsegand+($row[1]*60);                                //Minutos
			   $wtotsegand = $wtotsegand+($row[2]);                                   //Segundos
			     
			   $wproaltand=($wtotsegand/$row[3]);                                     //Total segundos dividido el numero de altas
		       $wproaltand=number_format(($wproaltand/3600),2,'.',',');               //Promedio por alta dividido 3600 segundos que equivale a una hora
		       $wproand=explode(".",$wproaltand);
		       
		       $wprohorand=$wproand[0];
		       $wprominand=number_format((((3600*$wproand[1])/100)/60),0,'.',',');    //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltfac
			     
		       $wtotgensegand=$wtotgensegand+$wtotsegand;
		       $wtotgenaltand=$wtotgenaltand+$row[3];
		       
		       $wproaltgenand=($wtotgensegand/$wtotgenaltand);                        //Total segundos dividido el numero de altas
		       $wproaltgenand=number_format(($wproaltgenand/3600),2,'.',',');         //Promedio por alta dividido 3600 segundos que equivale a una hora
		       
		       $warr[$i][2]=$wprohorand;                                              //Promedio horas Antes de devolucion
		       $warr[$i][3]=$wprominand;                                              //Promedio minutos Antes de devolucion
		      }
          }
	   
	   //==================================================================================================================================
	   
	   
	   
	   
	   //==================================================================================================================================   
	   //*** DEVOLUCION (Desde que se coloca Alta en proceso - hasta que se graba la devolucion ***
	   $q = " SELECT SUM(HOUR(TIMEDIFF(".$wbasedato."_000035.hora_data, ubihap))), "      //Termino Devolucion - Inicio Proceso de Alta
	       ."        SUM(MINUTE(TIMEDIFF(".$wbasedato."_000035.hora_data, ubihap))), "    //Termino Devolucion - Inicio Proceso de Alta
	       ."        SUM(SECOND(TIMEDIFF(".$wbasedato."_000035.hora_data, ubihap))), "    //Termino Devolucion - Inicio Proceso de Alta
	       ."        COUNT(*), ubisac "
	       ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ".$wbasedato."_000035 "
	       ."  WHERE ubifad  BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND ubisac  LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND ubiald  = 'on' "
	       ."    AND ubifap  = ubifad "
	       ."    AND ubisac  = ccocod "
	       ."    AND ccohos  = 'on' "
	       ."    AND ccopal  = 'on' "    //Indica que si promedio en las Altas
	       ."    AND ubihis  = denhis "
	       ."    AND ubiing  = dening "
	       ."    AND ubihap <= ".$wbasedato."_000035.hora_data "
	       ."    AND ubifad  = ".$wbasedato."_000035.fecha_data "
	       ."    AND ubimue != 'on' "                                                                 //Oct 6 2009
	       ."    AND ubiamd != 'on' "
	       //."    AND CONCAT(ubihis,ubiing) != '".$whis_alta_mas_demorada.$wing_alta_mas_demorada."'"  //No tengo en cuenta el alta mas demorada - Oct 6 2009
	       ."  GROUP BY 5 "
	       ."  ORDER BY 5 ";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   
	   if ($num > 0)
	      {
		   $wtotnum=$num;   
		      
		   $wtotgensegdev=0;
		   $wtotgenaltdev=0;
		   for ($i=1;$i<=$num;$i++)
		      {   
		       $row = mysql_fetch_array($res);
		       
		       //================================================================================
		       //Busco el Centro de Costo en el arreglo
		       $wencontro="N";
		       $j=1;
		       while ($wencontro=="N" AND $j<=$wtotfil)  
		            {
		             if ($warr[$j][0]==$row[4] or $warr[$j][0]=="0")
			            $wencontro="S";
				       else
				          $j++;  
			        }
			   //================================================================================
		       
			   $warr[$j][0]=$row[4];                                                  //Cco
		       if (isset($row[3]))
		          $warr[$j][4]=$row[3];                                               //Cantidad de altas con devolucion
		         else
		            $warr[$j][4]=0; 
		       
		       $wtotsegdev = $row[0]*3600;                                            //Horas
			   $wtotsegdev = $wtotsegdev+($row[1]*60);                                //Minutos
			   $wtotsegdev = $wtotsegdev+($row[2]);                                   //Segundos
			     
			   $wproaltdev=($wtotsegdev/$row[3]);                                     //Total segundos dividido el numero de altas
		       $wproaltdev=number_format(($wproaltdev/3600),2,'.',',');               //Promedio por alta dividido 3600 segundos que equivale a una hora
		       $wprodev=explode(".",$wproaltdev);
		       
		       $wprohordev=$wprodev[0];
		       $wpromindev=number_format((((3600*$wprodev[1])/100)/60),0,'.',',');    //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltfac
			     
		       $wtotgensegdev=$wtotgensegdev+$wtotsegdev;
		       $wtotgenaltdev=$wtotgenaltdev+$row[3];
		       
		       $wproaltgendev=($wtotgensegdev/$wtotgenaltdev);                        //Total segundos dividido el numero de altas
		       $wproaltgendev=number_format(($wproaltgendev/3600),2,'.',',');         //Promedio por alta dividido 3600 segundos que equivale a una hora
		       
		       $warr[$j][5]=$wprohordev;                                              //Promedio horas devolucion
		       $warr[$j][6]=$wpromindev;                                              //Promedio minutos devolucion
		      }
          }
          
       //*** TIEMPO ENTRE LA DEVOLUCION O ALTA EN PROCESO HASTA QUE SE EMPIEZA A FACTURAR ***
       $q = " SELECT SUM(HOUR(TIMEDIFF(A.ubihap, movhos_000022.hora_data))), " 
		   ."        SUM(MINUTE(TIMEDIFF(A.ubihap, movhos_000022.hora_data))), " 
		   ."        SUM(SECOND(TIMEDIFF(A.ubihap, movhos_000022.hora_data))), " 
			."        COUNT(*), A.ubisac "
			."   FROM movhos_000018 A, movhos_000022, movhos_000011, ( SELECT ubihis, ubiing " 
			."                                                             FROM movhos_000018 "
			."                                                            WHERE CONCAT(ubihis,ubiing) NOT IN (SELECT CONCAT(denhis,dening) "
			."                                                                                                  FROM movhos_000035 "
			."                                                                                                 WHERE ubifad BETWEEN '2010-02-17' AND '2010-02-17' "
			."                                                                                                   AND ubihis = denhis "
			."                                                                                                   AND ubiing = dening "
			."                                                                                                   AND ubifad = movhos_000035.fecha_data "
			."                                                                                                   AND ubihap > movhos_000035.hora_data) "
			."                                                              AND ubifad BETWEEN '2010-02-17' AND '2010-02-17') B "
			."   WHERE A.ubifad  BETWEEN '2010-02-17' AND '2010-02-17' "
			."     AND A.ubisac  LIKE '%' "
			."     AND A.ubiald  = 'on' " 
			."     AND A.ubifap  = A.ubifad " 
			."     AND A.ubihis  = cuehis " 
			."     AND A.ubiing  = cueing "
			."     AND A.ubifap  = movhos_000022.fecha_data " 
			."     AND A.ubisac  = ccocod "
			."     AND ccohos  = 'on' "
			."     AND ccopal  = 'on' " 
			."     AND A.ubimue != 'on' "                    
			."     AND A.ubiamd != 'on' " 
			."     AND A.ubihis  = B.ubihis "
			."     AND A.ubiing  = B.ubiing "
			."   GROUP BY 5 "
			//"   ORDER BY 5 "
			." UNION ALL "
            ." SELECT SUM(HOUR(TIMEDIFF(".$wbasedato."_000022.hora_data, ".$wbasedato."_000035.hora_data))), "      //Ingreso facturacion - Devolucion 
	  // $q = " SELECT SUM(HOUR(TIMEDIFF(".$wbasedato."_000022.hora_data, ".$wbasedato."_000035.hora_data))), "      //Ingreso facturacion - Devolucion 
	       ."        SUM(MINUTE(TIMEDIFF(".$wbasedato."_000022.hora_data, ".$wbasedato."_000035.hora_data))), "    //Ingreso facturacion - Devolucion
	       ."        SUM(SECOND(TIMEDIFF(".$wbasedato."_000022.hora_data, ".$wbasedato."_000035.hora_data))), "    //Ingreso facturacion - Devolucion
	       ."        COUNT(*), ubisac "
	       ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000022, ".$wbasedato."_000035, ".$wbasedato."_000011 "
	       ."  WHERE ubifad  BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND ubisac  LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND ubiald  = 'on' "
	       ."    AND ubifap  = ubifad "
	       ."    AND ubihis  = cuehis "
	       ."    AND ubiing  = cueing "
	       ."    AND ubihis  = denhis "
	       ."    AND ubiing  = dening "
	       ."    AND ubihap <= ".$wbasedato."_000035.hora_data "                              //Hora de alta en proceso menor o igual a la devolución
	       ."    AND ".$wbasedato."_000022.hora_data >= ".$wbasedato."_000035.hora_data "     //Hora de ingreso a facturar mayor a hora devolución
	       ."    AND ubifap  = ".$wbasedato."_000022.fecha_data "                             //Fecha de alta en proceso igual a fecha de facturación
	       ."    AND ubifap  = ".$wbasedato."_000035.fecha_data "                             //Fecha de alta en proceso igual a fecha de devolución
	       ."    AND ubisac  = ccocod "
	       ."    AND ccohos  = 'on' "
	       ."    AND ccopal  = 'on' "                                                         //Indica que si promedia en las Altas
	       ."    AND ubimue != 'on' "                                                         //Oct 6 2009
	       ."    AND ubiamd != 'on' "
	       ."  GROUP BY 5 "
	       ."  ORDER BY 5 ";   
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   
	   if ($num > 0)
	      {
		   if (isset($wtotnum))
		      {
		       if ($wtotnum < $num)
		          $wtotnum=$num;
	          }
	         else
	            $wtotnum=$num;     
		            
		      
		   $wtotgensegdfa=0;
		   $wtotgenaltdfa=0;
		   for ($i=1;$i<=$num;$i++)
		      {   
			   $row = mysql_fetch_array($res);
			   
			   //================================================================================
		       //Busco el Centro de Costo en el arreglo
		       $wencontro="N";
		       $j=1;
		       while ($wencontro=="N" AND $j<=$wtotfil)  
		            {
			         if ($warr[$j][0]==$row[4] or $warr[$j][0]=="0")
			            $wencontro="S";
				       else
				          $j++;  
			        }
			   //================================================================================
			        
		       $warr[$j][0]=$row[4];                                                 //Cco
		       $warr[$j][7]=$warr[$j][7]+$row[3];                                                 //Cantidad de altas entre la devolucion y facturacion
		       
		       $wtotsegdfa = $row[0]*3600;                                            //Horas
			   $wtotsegdfa = $wtotsegdfa+($row[1]*60);                                //Minutos
			   $wtotsegdfa = $wtotsegdfa+($row[2]);                                   //Segundos
			   
			   $wproaltdfa=($wtotsegdfa/$row[3]);                                     //Total segundos dividido el numero de altas
		       $wproaltdfa=number_format(($wproaltdfa/3600),2,'.',',');               //Promedio por alta dividido 3600 segundos que equivale a una hora
		       $wprodfa=explode(".",$wproaltdfa);
		       
		       $wprohordfa=$wprodfa[0];
		       $wpromindfa=number_format((((3600*$wprodfa[1])/100)/60),0,'.',',');    //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltdfa
			     
		       $wtotgensegdfa=$wtotgensegdfa+$wtotsegdfa;
		       $wtotgenaltdfa=$wtotgenaltdfa+$row[3];
		       
		       $wproaltgendfa=($wtotgensegdfa/$wtotgenaltdfa);                        //Total segundos dividido el numero de altas
		       $wproaltgendfa=number_format(($wproaltgendfa/3600),2,'.',',');         //Promedio por alta dividido 3600 segundos que equivale a una hora
		       
		       $warr[$j][8]=$wprohordfa;                                              //Promedio horas entre la devolucion y facturacion
		       $warr[$j][9]=$wpromindfa;                                              //Promedio minutos entre la devolucion y facturacion
		       //============================================================================================================================================================
              }
          }   
	   
	   //*** TIEMPOS DE FACTURACION ***
	   $q = " SELECT SUM(HOUR(TIMEDIFF(cuehfa, ".$wbasedato."_000022.hora_data))), "      //Sal. Facturacion - Ingreso facturacion
	       ."        SUM(MINUTE(TIMEDIFF(cuehfa, ".$wbasedato."_000022.hora_data))), "    //Sal. Facturacion - Ingreso facturacion
	       ."        SUM(SECOND(TIMEDIFF(cuehfa, ".$wbasedato."_000022.hora_data))), "    //Sal. Facturacion - Ingreso facturacion
	       ."        COUNT(*), ubisac "
	       ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000022, ".$wbasedato."_000011 "
	       ."  WHERE ubifad  BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND ubisac  LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND ubiald  = 'on' "
	       ."    AND ubifap  = ubifad "
	       ."    AND ubihis  = cuehis "
	       ."    AND ubiing  = cueing "
	       ."    AND cuehfa >= ".$wbasedato."_000022.hora_data "
	       ."    AND ubisac  = ccocod "
	       ."    AND ccohos  = 'on' "
	       ."    AND ccopal  = 'on' "    //Indica que si promedio en las Altas
	       ."    AND ubimue != 'on' "                                                                 //Oct 6 2009
	       ."    AND ubiamd != 'on' "
	       //."    AND CONCAT(ubihis,ubiing) != '".$whis_alta_mas_demorada.$wing_alta_mas_demorada."'"  //No tengo en cuenta el alta mas demorada - Oct 6 2009
	       ."  GROUP BY 5 "
	       ."  ORDER BY 5 ";   
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   
	   if ($num > 0)
	      {
		   if (isset($wtotnum))
		      {
		       if ($wtotnum < $num)
		          $wtotnum=$num;
	          }
	         else
	            $wtotnum=$num;     
		            
		      
		   $wtotgensegfac=0;
		   $wtotgenaltfac=0;
		   
		   for ($i=1;$i<=$num;$i++)
		      {   
		       $row = mysql_fetch_array($res);
			     
		       //================================================================================
		       //Busco el Centro de Costo en el arreglo
		       $wencontro="N";
		       $j=1;
		       while ($wencontro=="N" AND $j<=$wtotfil)  
		            {
		             if ($warr[$j][0]==$row[4] or $warr[$j][0]=="0")
			            $wencontro="S";
				       else
				          $j++;  
				    }
			   //================================================================================
			   
			   $warr[$j][0]=$row[4];                                                  //Cco
		       $warr[$j][10]=$row[3];                                                 //Cantidad de altas facturacion
		       
		       $wtotsegfac = $row[0]*3600;                                            //Horas
			   $wtotsegfac = $wtotsegfac+($row[1]*60);                                //Minutos
			   $wtotsegfac = $wtotsegfac+($row[2]);                                   //Segundos
			     
			   $wproaltfac=($wtotsegfac/$row[3]);                                     //Total segundos dividido el numero de altas
		       $wproaltfac=number_format(($wproaltfac/3600),2,'.',',');               //Promedio por alta dividido 3600 segundos que equivale a una hora
		       $wprofac=explode(".",$wproaltfac);
		       
		       $wprohorfac=$wprofac[0];
		       $wprominfac=number_format((((3600*$wprofac[1])/100)/60),0,'.',',');    //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltfac
			     
		       $wtotgensegfac=$wtotgensegfac+$wtotsegfac;
		       $wtotgenaltfac=$wtotgenaltfac+$row[3];
		       
		       $wproaltgenfac=($wtotgensegfac/$wtotgenaltfac);                        //Total segundos dividido el numero de altas
		       $wproaltgenfac=number_format(($wproaltgenfac/3600),2,'.',',');         //Promedio por alta dividido 3600 segundos que equivale a una hora
		       
		       $warr[$j][11]=$wprohorfac;                                              //Promedio horas facturacion
		       $warr[$j][12]=$wprominfac;                                              //Promedio minutos facturacion
		       //============================================================================================================================================================
              }
          }    
	          
	   //*** TIEMPOS DE CAJA ***
	   $q = " SELECT SUM(HOUR(TIMEDIFF(cuehpa, cuehfa))), "                               //Sal. Caja - Ingreso Caja
	       ."        SUM(MINUTE(TIMEDIFF(cuehpa, cuehfa))), "                             //Sal. Caja - Ingreso Caja
	       ."        SUM(SECOND(TIMEDIFF(cuehpa, cuehfa))), "                             //Sal. Caja - Ingreso Caja
	       ."        COUNT(*), ubisac "
	       ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000022, ".$wbasedato."_000011 "
	       ."  WHERE ubifad  BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND ubisac  LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND ubiald  = 'on' "
	       ."    AND ubifap  = ubifad "
	       ."    AND ubihis  = cuehis "
	       ."    AND ubiing  = cueing "
	       ."    AND ubihap <= ".$wbasedato."_000022.hora_data "
	       ."    AND cuehfa >= ".$wbasedato."_000022.hora_data "
	       ."    AND ubisac  = ccocod "
	       ."    AND ccohos  = 'on' "
	       ."    AND ccopal  = 'on' "    //Indica que si promedio en las Altas
	       ."    AND ubimue != 'on' "                                                                 //Oct 6 2009
	       ."    AND ubiamd != 'on' "
	       //."    AND CONCAT(ubihis,ubiing) != '".$whis_alta_mas_demorada.$wing_alta_mas_demorada."'"  //No tengo en cuenta el alta mas demorada - Oct 6 2009
	       ."  GROUP BY 5 "
	       ."  ORDER BY 5 ";   
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   if ($num > 0)
	      {
		   if (isset($wtotnum))
		      {
		       if ($wtotnum < $num)
		          $wtotnum=$num;
	          }
	         else
	            $wtotnum=$num;     
		      
		   $wtotgensegcaj=0;
		   $wtotgenaltcaj=0;
		   for ($i=1;$i<=$num;$i++)
		      {   
		       $row = mysql_fetch_array($res);
			     
		       //================================================================================
		       //Busco el Centro de Costo en el arreglo
		       $wencontro="N";
		       $j=1;
		       while ($wencontro=="N" AND $j<=$wtotfil)  
		            {
		             if ($warr[$j][0]==$row[4] or $warr[$j][0]=="0")
			            $wencontro="S";
				       else
				          $j++;  
			        }
			   //================================================================================
			        
			   $warr[$j][0]=$row[4];                                                  //Cco
		       $warr[$j][13]=$row[3];                                                 //Cantidad de altas caja
		       
		       $wtotsegcaj = $row[0]*3600;                                            //Horas
			   $wtotsegcaj = $wtotsegcaj+($row[1]*60);                                //Minutos
			   $wtotsegcaj = $wtotsegcaj+($row[2]);                                   //Segundos
			     
			   $wproaltcaj=($wtotsegcaj/$row[3]);                                     //Total segundos dividido el numero de altas
		       $wproaltcaj=number_format(($wproaltcaj/3600),2,'.',',');               //Promedio por alta dividido 3600 segundos que equivale a una hora
		       $wprocaj=explode(".",$wproaltcaj);
		       
		       $wprohorcaj=$wprocaj[0];
		       $wpromincaj=number_format((((3600*$wprocaj[1])/100)/60),0,'.',',');    //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltser
			     
		       $wtotgensegcaj=$wtotgensegcaj+$wtotsegcaj;
		       $wtotgenaltcaj=$wtotgenaltcaj+$row[3];
		       
		       $wproaltgencaj=($wtotgensegcaj/$wtotgenaltcaj);                        //Total segundos dividido el numero de altas
		       $wproaltgencaj=number_format(($wproaltgencaj/3600),2,'.',',');         //Promedio por alta dividido 3600 segundos que equivale a una hora
		       
		       $warr[$j][14]=$wprohorcaj;                                             //Promedio horas caja
		       $warr[$j][15]=$wpromincaj;                                             //Promedio minutos caja
		       //============================================================================================================================================================
	          }
          }    

       //============================================================================================================================================================   		       
	   //*** TIEMPOS ALTA DEFINITIVA ***
	   //============================================================================================================================================================
	   $q = " SELECT SUM(HOUR(TIMEDIFF(ubihad, cuehpa))), "                           //Alta Definitiva - Sal. Caja
	       ."        SUM(MINUTE(TIMEDIFF(ubihad, cuehpa))), "                         //Alta Definitiva - Sal. Caja
	       ."        SUM(SECOND(TIMEDIFF(ubihad, cuehpa))), "                         //Alta Definitiva - Sal. Caja
	       ."        COUNT(*), ubisac "
	       ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000022, ".$wbasedato."_000011 "
	       ."  WHERE ubifad  BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND ubisac  LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND ubiald  = 'on' "
	       ."    AND ubifap  = ubifad "
	       ."    AND ubihis  = cuehis "
	       ."    AND ubiing  = cueing "
	       ."    AND ubihap <= ".$wbasedato."_000022.hora_data "
	       ."    AND cuehfa >= ".$wbasedato."_000022.hora_data "
	       ."    AND ubifap <= ".$wbasedato."_000022.fecha_data "
	       ."    AND ubisac  = ccocod "
	       ."    AND ccohos  = 'on' "
	       ."    AND ccopal  = 'on' "    //Indica que si promedio en las Altas
	       ."    AND ubimue != 'on' "                                                                 //Oct 6 2009
	       ."    AND ubiamd != 'on' "
	       //."    AND CONCAT(ubihis,ubiing) != '".$whis_alta_mas_demorada.$wing_alta_mas_demorada."'"  //No tengo en cuenta el alta mas demorada - Oct 6 2009
	       ."  GROUP BY 5 "
	       ."  ORDER BY 5 ";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   
	   if ($num > 0)
	      {
		   if (isset($wtotnum))
		      {
		       if ($wtotnum < $num)
		          $wtotnum=$num;
	          }
	         else
	            $wtotnum=$num;     
		      
		   $wtotgensegald=0;
		   $wtotgenaltald=0;
		   for ($i=1;$i<=$num;$i++)
		      {   
		       $row = mysql_fetch_array($res);
			     
		       //================================================================================
		       //Busco el Centro de Costo en el arreglo
		       $wencontro="N";
		       $j=1;
		       while ($wencontro=="N" AND $j<=$wtotfil)  
		            {
		             if ($warr[$j][0]==$row[4] or $warr[$j][0]=="0")
			            $wencontro="S";
				       else
				          $j++;  
			        }
			   //================================================================================
			   
			   $warr[$j][0]=$row[4];                                                  //Cco
		       $warr[$j][16]=$row[3];                                                 //Cantidad de altas definitivas
		              
		       $wtotsegald = $row[0]*3600;                                            //Horas
			   $wtotsegald = $wtotsegald+($row[1]*60);                                //Minutos
			   $wtotsegald = $wtotsegald+($row[2]);                                   //Segundos
			     
			   $wproaltald=($wtotsegald/$row[3]);                                     //Total segundos dividido el numero de altas
		       $wproaltald=number_format(($wproaltald/3600),2,'.',',');               //Promedio por alta dividido 3600 segundos que equivale a una hora
		       $wproald=explode(".",$wproaltald);
		       
		       $wprohorald=$wproald[0];
		       $wprominald=number_format((((3600*$wproald[1])/100)/60),0,'.',',');    //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltser
			     
		       $wtotgensegald=$wtotgensegald+$wtotsegald;
		       $wtotgenaltald=$wtotgenaltald+$row[3];
		       
		       $wproaltgenald=($wtotgensegald/$wtotgenaltald);                        //Total segundos dividido el numero de altas
		       $wproaltgenald=number_format(($wproaltgenald/3600),2,'.',',');         //Promedio por alta dividido 3600 segundos que equivale a una hora
		       
		       $warr[$j][17]=$wprohorald;                                             //Promedio horas caja
		       $warr[$j][18]=$wprominald;                                             //Promedio minutos caja
		     }
          }   
	   //============================================================================================================================================================
	   
	   $motivos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'motivoParaPromedioDeAltas');
	   $centrales = consultarAliasPorAplicacion($conex, $wemp_pmla, 'centralParaPromedioDeAlta');
	   //============================================================================================================================================================   
       //*** TIEMPOS CAMILLEROS ***
       //============================================================================================================================================================
	   // $q = " SELECT SUM(HOUR(TIMEDIFF(".$wcencam."_000003.hora_llegada, ubihad))), "                           //LLegada camillero - Alta Definitiva 
	       // ."        SUM(MINUTE(TIMEDIFF(".$wcencam."_000003.hora_llegada, ubihad))), "                         //LLegada camillero - Alta Definitiva
	       // ."        SUM(SECOND(TIMEDIFF(".$wcencam."_000003.hora_llegada, ubihad))), "                         //LLegada camillero - Alta Definitiva
	       // ."        COUNT(*), ubisac "
	       // ."   FROM ".$wbasedato."_000018, ".$wcencam."_000003, ".$wbasedato."_000011 "
	       // ."  WHERE ubifad        BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       // ."    AND ubisac        LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       // ."    AND ubiald        = 'on' "
	       // ."    AND ubifap        = ubifad "
	       // ."    AND ubisac        = ccocod "
	       // ."    AND ccohos        = 'on' "
	       // ."    AND ccopal        = 'on' "    //Indica que si promedio en las Altas
	       // //."    AND ubihac        = habitacion "
	       // ."    AND INSTR(habitacion, ubihac) > 0 "
	       // ."    AND ubifad        = ".$wcencam."_000003.fecha_data "
	       // ."    AND ubihad        = ".$wcencam."_000003.hora_data "
	       // ."    AND hora_llegada >= ".$wcencam."_000003.hora_data "
	       // ."    AND anulada       = 'No' "
	       // ."    AND ubimue       != 'on' "                                                           //Oct 6 2009
	       // ."    AND ubiamd       != 'on' "
	       // //."    AND CONCAT(ubihis,ubiing) != '".$whis_alta_mas_demorada.$wing_alta_mas_demorada."'"  //No tengo en cuenta el alta mas demorada - Oct 6 2009
	       // ."  GROUP BY 5 "
	       // ."  ORDER BY 5 ";
		   
		   $q = " SELECT SUM(HOUR(TIMEDIFF(".$wcencam."_000003.hora_llegada, ubihad))), "                           //LLegada camillero - Alta Definitiva 
	       ."        SUM(MINUTE(TIMEDIFF(".$wcencam."_000003.hora_llegada, ubihad))), "                         //LLegada camillero - Alta Definitiva
	       ."        SUM(SECOND(TIMEDIFF(".$wcencam."_000003.hora_llegada, ubihad))), "                         //LLegada camillero - Alta Definitiva
	       ."        COUNT(*), ubisac "
	       ."   FROM ".$wbasedato."_000018, ".$wcencam."_000003, ".$wbasedato."_000011 "
	       ."  WHERE ubifad        BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND ubisac        LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND ubiald        = 'on' "
	       ."    AND ubifap        = ubifad "
	       ."    AND ubisac        = ccocod "
	       ."    AND ccohos        = 'on' "
	       ."    AND ccopal        = 'on' "    //Indica que si promedio en las Altas
	       //."    AND ubihac        = habitacion "
	       // ."    AND INSTR(habitacion, ubihac) > 0 "
	       ."    AND ubifad        = ".$wcencam."_000003.fecha_data "
	       ."    AND ubihad        = ".$wcencam."_000003.hora_data "
	       ."    AND hora_llegada >= ".$wcencam."_000003.hora_data "
	       ."    AND anulada       = 'No' "
	       ."    AND ubimue       != 'on' "                                                           //Oct 6 2009
	       ."    AND ubiamd       != 'on' "
	       ."    AND motivo IN (".$motivos.")  "
	       ."    AND Central IN (".$centrales.") "
	       //."    AND CONCAT(ubihis,ubiing) != '".$whis_alta_mas_demorada.$wing_alta_mas_demorada."'"  //No tengo en cuenta el alta mas demorada - Oct 6 2009
	       ."  GROUP BY 5 "
	       ."  ORDER BY 5 ";
		  
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   
	   if ($num > 0)
	      {
		   if (isset($wtotnum))
		      {
		       if ($wtotnum < $num)
		          $wtotnum=$num;
	          }
	         else
	            $wtotnum=$num;     
		      
		   $wtotgensegcam=0;
		   $wtotgenaltcam=0;
		   for ($i=1;$i<=$num;$i++)
		      {   
		       $row = mysql_fetch_array($res);
			     
		       //================================================================================
		       //Busco el Centro de Costo en el arreglo
		       $wencontro="N";
		       $j=1;
		       while ($wencontro=="N" AND $j<=$wtotfil)  
		            {
		             if ($warr[$j][0]==$row[4] or $warr[$j][0]=="0")
			            $wencontro="S";
				       else
				          $j++;
			        }
			   //================================================================================
			   
			   $warr[$j][0]=$row[4];                                                  //Cco
		       $warr[$j][19]=$row[3];                                                 //Cantidad de altas camilleros
		              
		       $wtotsegcam = $row[0]*3600;                                            //Horas
			   $wtotsegcam = $wtotsegcam+($row[1]*60);                                //Minutos
			   $wtotsegcam = $wtotsegcam+($row[2]);                                   //Segundos
			     
			   $wproaltcam=($wtotsegcam/$row[3]);                                     //Total segundos dividido el numero de altas
		       $wproaltcam=number_format(($wproaltcam/3600),2,'.',',');               //Promedio por alta dividido 3600 segundos que equivale a una hora
		       $wprocam=explode(".",$wproaltcam);
		       
		       $wprohorcam=$wprocam[0];
		       $wpromincam=number_format((((3600*$wprocam[1])/100)/60),0,'.',',');    //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltser
			     
		       $wtotgensegcam=$wtotgensegcam+$wtotsegcam;
		       $wtotgenaltcam=$wtotgenaltcam+$row[3];
		       
		       $wproaltgencam=($wtotgensegcam/$wtotgenaltcam);                        //Total segundos dividido el numero de altas
		       $wproaltgencam=number_format(($wproaltgencam/3600),2,'.',',');         //Promedio por alta dividido 3600 segundos que equivale a una hora
		       
		       $warr[$j][20]=$wprohorcam;                                             //Promedio horas camilleros
		       $warr[$j][21]=$wpromincam;                                             //Promedio minutos camilleros
		     }
          }   
          //============================================================================================================================================================
          
          $wtotand=0;
          $wtotdev=0;
          $wtotdfa=0;
          $wtotfac=0;
          $wtotcaj=0;
          $wtotald=0;
          $wtotcam=0;
          for ($i=1;$i<=$wtotnum;$i++)
             {
	          echo "<tr>";   
	          for ($j=0;$j<=21;$j++)
	            {
		         if (is_integer($i/2))
                  $wcolor="33FFFF";
                 else
                    $wcolor="99FFFF";     
		            
	             if (isset($warr[$i][$j]))
	                {
		             if ($j==2 or $j==5 or $j==8 or $j==11 or $j==14 or $j==17 or $j==20)             // son las horas promedio y sumando 1 son los minutos
		                {
			             if ($j==2) $wclass="fila2";            //Devolucion ANTES del Alta  
	   	                 if ($j==5) $wclass="fila1";            //Devolucion
			             if ($j==8 or $j==11) $wclass="fila2";  //Facturacion
			             if ($j==14) $wclass="fila1";           //Caja
			             if ($j==17) $wclass="fila2";           //Alta Definitiva
			             if ($j==20) $wclass="fila1";           //Camillero
			             
			             if (strlen($warr[$i][$j+1]) == 1)
			                echo "<td align=center class=".$wclass."><b><font color=000000>".$warr[$i][$j].":0".$warr[$i][$j+1]."</font></b></td>";
                           else
			                  echo "<td align=center class=".$wclass."><b><font color=000000>".$warr[$i][$j].":".$warr[$i][$j+1]."</font></b></td>";
	                     $j++;
                        } 
                       else
                          {
	                       if ($j==1)                      //Devolucion antes del Alta
                              {
	                           $wtotand=$wtotand+$warr[$i][$j];
	                           $wclass="fila2";
                              }   
                           if ($j==4)                      //Devolucion
                              {
	                           $wtotdev=$wtotdev+$warr[$i][$j];
	                           $wclass="fila1";
                              } 
                           if ($j==7)                      //Facturacion
                              {
                               $wtotdfa=$wtotdfa+$warr[$i][$j];   
                               $wclass="fila2";
                              } 
                           if ($j==10)                      //Facturacion
                              { 
                               $wtotfac=$wtotfac+$warr[$i][$j];
                               $wclass="fila2";
                              } 
                           if ($j==13)                     //Caja
                              {
                               $wtotcaj=$wtotcaj+$warr[$i][$j];
                               $wclass="fila1";
                              } 
                           if ($j==16)                     //Alta definitva
                              {
                               $wtotald=$wtotald+$warr[$i][$j];
                               $wclass="fila2";  
                              }  
                           if ($j==19)                     //Camilleros
                              {
                               $wtotcam=$wtotcam+$warr[$i][$j];
                               $wclass="fila1";
                              } 
                             
                           if ($j==0)                      //Centro de costo
                              $wclass="encabezadoTabla";
                              
                           echo "<td align=center class=".$wclass."><b>".$warr[$i][$j]."</b></td>";   
                          }     
	                } 
	               else
	                 if ($j==2 or $j==5 or $j==8 or $j==11 or $j==14 or $j==17 or $j==20)    //Estas son las cantidades
	                    {
		                 if ($j==2) $wclass="fila2";            //Devolucion Antes del Alta   
		                 if ($j==5) $wclass="fila1";            //Devolucion
			             if ($j==8 or $j==11) $wclass="fila2";  //Facturacion
			             if ($j==14) $wclass="fila1";           //Caja
			             if ($j==17) $wclass="fila2";           //Alta Definitiva  
			             if ($j==20) $wclass="fila1";           //Camillero
	                     echo "<td align=center class=".$wclass."><b>0:00</b></td>";     
	                     $j++;
                        }
                       else
                          {
	                       if ($j==1) $wclass="fila1";            //Devolucion Antes del Alta
	                       if ($j==4) $wclass="fila1";            //Devolucion
	                       if ($j==7 or $j==10) $wclass="fila2";  //Facturacion
			               if ($j==13) $wclass="fila1";           //Caja
			               if ($j==16) $wclass="fila2";           //Alta Definitiva
			               if ($j==19) $wclass="fila1";           //Camillero
                           echo "<td align=center class=".$wclass."><b>0</b></td>"; 
                          }  
                }      
	          echo "</tr>";   
             }    
          
            echo "<tr>";
            echo "<td align=center class=encabezadoTabla>&nbsp</td>";
            echo "<td align=center class=fila2 colspan=2>&nbsp</td>";
	        echo "<td align=center class=fila1 colspan=2>&nbsp</td>";
	        echo "<td align=center class=fila2 colspan=4>&nbsp</td>";
	        echo "<td align=center class=fila1 colspan=2>&nbsp</td>";
	        echo "<td align=center class=fila2 colspan=2>&nbsp</td>";  
	        echo "<td align=center class=fila1 colspan=2>&nbsp</td>";
	        echo "</tr>";
            
            
	        echo "<tr>";
	        echo "<td colspan=1 class=encabezadoTabla align=center><b><font size=4><b>Promedio total</b></font></b></td>";
	          
	        //Total Devolucion Antes del Alta
	        echo "<td align=center class=fila2><font size=4><b>".number_format($wtotand,0,'.',',')."</b></font></td>";             //Catidad devoluciones antes del Alta
	        if (isset($wproaltgenand)) $wprogenand=explode(".",$wproaltgenand);
		    if (isset($wprogenand[0])) $wprohorgenand=$wprogenand[0];
		    if (isset($wprogenand[1])) $wpromingenand=number_format((((3600*$wprogenand[1])/100)/60),0,'.',',');                   //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltser
		    if (isset($wprohorgenand)) 
		       if (strlen($wpromingenand) == 1)
		          echo "<td align=center class=fila2><font size=4><b>".$wprohorgenand.":0".$wpromingenand."</b></font></td>";
		         else 
		            echo "<td align=center class=fila2><font size=4><b>".$wprohorgenand.":".$wpromingenand."</b></font></td>";
		      else
		         echo "<td align=center class=fila1>&nbsp</td>";
	        
	        //Total Devolucion
	        echo "<td align=center class=fila1><font size=4><b>".number_format($wtotdev,0,'.',',')."</b></font></td>";             //Catidad devoluciones
	        if (isset($wproaltgendev)) $wprogendev=explode(".",$wproaltgendev);
		    if (isset($wprogendev[0])) $wprohorgendev=$wprogendev[0];
		    if (isset($wprogendev[1])) $wpromingendev=number_format((((3600*$wprogendev[1])/100)/60),0,'.',',');                   //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltser
		    if (isset($wprohorgendev)) 
		       if (strlen($wpromingendev) == 1)
		          echo "<td align=center class=fila1><font size=4><b>".$wprohorgendev.":0".$wpromingendev."</b></font></td>";
		         else 
		            echo "<td align=center class=fila1><font size=4><b>".$wprohorgendev.":".$wpromingendev."</b></font></td>";
		      else
		         echo "<td align=center class=fila1>&nbsp</td>"; 
		    
		    //Total tiempo entre devolucion y facturacion
	        echo "<td align=center class=fila2><font size=4><b>".number_format($wtotdfa,0,'.',',')."</b></td>";                    //Cantidad Facturaciones
	        if (isset($wproaltgendfa)) $wprogendfa=explode(".",$wproaltgendfa);
		    if (isset($wprogendfa[0])) $wprohorgendfa=$wprogendfa[0];
		    if (isset($wprogendfa[1])) $wpromingendfa=number_format((((3600*$wprogendfa[1])/100)/60),0,'.',',');                   //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltser
		    if (isset($wprohorgendfa)) 
		       if (strlen($wpromingendfa) == 1)
		          echo "<td align=center class=fila2><font size=4><b>".$wprohorgendfa.":0".$wpromingendfa."</b></font></td>";
		         else 
  		            echo "<td align=center class=fila2><font size=4><b>".$wprohorgendfa.":".$wpromingendfa."</b></font></td>"; 
		      else
		         echo "<td align=center class=fila2>&nbsp</td>";     
		         
	        //Total facturacion
	        echo "<td align=center class=fila2><font size=4><b>".number_format($wtotfac,0,'.',',')."</b></td>";                    //Cantidad Facturaciones
	        $wprogenfac=explode(".",$wproaltgenfac);
		    $wprohorgenfac=$wprogenfac[0];
		    $wpromingenfac=number_format((((3600*$wprogenfac[1])/100)/60),0,'.',',');                                              //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltser
		    if (strlen($wpromingenfac) == 1)
		       echo "<td align=center class=fila2><font size=4><b>".$wprohorgenfac.":0".$wpromingenfac."</b></font></td>";
		      else 
		         echo "<td align=center class=fila2><font size=4><b>".$wprohorgenfac.":".$wpromingenfac."</b></font></td>";
	        
		    //Total Caja
		    echo "<td align=center class=fila1><font size=4><b>".number_format($wtotcaj,0,'.',',')."</b></td>";                    //Cantidad Salidas por Caja
	        $wprogencaj=explode(".",$wproaltgencaj);
		    $wprohorgencaj=$wprogencaj[0];
		    $wpromingencaj=number_format((((3600*$wprogencaj[1])/100)/60),0,'.',',');                                              //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltser
		    if (strlen($wpromingencaj) == 1)
		       echo "<td align=center class=fila1><font size=4><b>".$wprohorgencaj.":0".$wpromingencaj."</b></font></td>";
		      else 
		         echo "<td align=center class=fila1><font size=4><b>".$wprohorgencaj.":".$wpromingencaj."</b></font></td>";
		    
		    //Total Alta Definitiva
		    echo "<td align=center class=fila2><font size=4><b>".number_format($wtotald,0,'.',',')."</b></td>";                    //Cantidad Altas Definitivas
	        $wprogenald=explode(".",$wproaltgenald);
		    $wprohorgenald=$wprogenald[0];
		    $wpromingenald=number_format((((3600*$wprogenald[1])/100)/60),0,'.',',');                                              //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltser
		    if (strlen($wpromingencaj) == 1)
		       echo "<td align=center class=fila2><font size=4><b>".$wprohorgenald.":0".$wpromingenald."</b></font></td>";
		      else
		         echo "<td align=center class=fila2><font size=4><b>".$wprohorgenald.":".$wpromingenald."</b></font></td>"; 
		    
		    //Total Camilleros
		    echo "<td align=center class=fila1><font size=4><b>".number_format($wtotcam,0,'.',',')."</b></td>";                    //Cantidad Altas Definitivas
	        $wprogencam=explode(".",$wproaltgencam);
		    $wprohorgencam=$wprogencam[0];
		    $wpromingencam=number_format((((3600*$wprogencam[1])/100)/60),0,'.',',');                                              //Saco los minutos a que equivale los decimales que da el promedio de alta $wproaltser
		    if (strlen($wpromingencam) == 1)
		       echo "<td align=center class=fila1><font size=4><b>".$wprohorgencam.":0".$wpromingencam."</b></font></td>";
		      else
		         echo "<td align=center class=fila1><font size=4><b>".$wprohorgencam.":".$wpromingencam."</b></font></td>"; 
		    
	        echo "</tr>";  
	       
	   //========================================================================================================
	   
	   	    
	   echo "<td align=center colspan=15 class=link><A href='estadisticas_altas.php?wemp_pmla=".$wemp_pmla."&wfec_i=".$wfec_i."&wfec_f=".$wfec_f."'><b><font size=3 color=660099>Retornar</font></b></A></td>";
	         
      }      
	echo "</table>";   	  
	echo "</form>";
	  
    echo "<br>";
    echo "<center><table>"; 
    echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
    
} // if de register

include_once("free.php");

?>