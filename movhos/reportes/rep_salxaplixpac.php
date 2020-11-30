<html>
<head>
  <title>Reporte de Saldos X Aplicar X Paciente</title>
  
  <style type="text/css">
    	
    	A	{text-decoration: none;color: #000066;}
    	.tipo3V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:30pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo4V:hover {color: #000066; background: #999999;}
    	
  </style>
    
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.salxaplixpac.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
</script>


<?php
include_once("conex.php");

/**
* REPORTE DE SALDOS X APLICAR X PACIENTE	                                                   *
*/
// ==========================================================================================================================================
// PROGRAMA				      :Reporte para saber los saldos pendientes de aplicar por paciente.                                             |
// AUTOR				      :Ing. Gustavo Alberto Avendano Rivera.                                                                         |
// FECHA CREACION			  :Octubre 3 DE 2007.                                                                                            |
// FECHA ULTIMA ACTUALIZACION :03 de Octubre de 2007.                                                                                        |
// DESCRIPCION			      :Este reporte sirve para ver por centro de costos-habitacion y paciente que saldo tiene pendiente de aplicar.  |
//                                                                                                                                           |
// TABLAS UTILIZADAS :                                                                                                                       |
// root_000050       : Tabla de Empresas para escojer empresa y esta traer un campo para saber que centros de costos escojer.                |
// costosyp_000005   : Tabla de Centros de costos de Clinica las Americas, Laboratorio de Patologia, y Laboratorio Medico.                   |
// clisur_000003     : Tabla de Centros de costos de clinica del sur.                                                                        |
// farstore_000003   : Tabla de Centros de costos de farmastore.                                                                             |
// root_000041       : Tabla de Tipos de requerimientos.                                                                                     |
// root_000042       : Tabla de Responsables por centro de costos.                                                                           |
// usuarios          : Tabla de Usuarios con su codigo y descripcion.                                                                        |
// root_000040       : Tabla de Requerimientos.                                                                                              |
// root_000043       : Tabla de Clases.                                                                                                      |
// root_000049       : Tabla de Estados.                                                                                                     |
// 																																			 |
// Modificaciones:										
// Marzo 14 de 2019     (Arleyda I.C.)      Migraci�n realizada																					 |
// Julio 13 de 2016 	 (Edwin MG)			Para las observaciones, se muestra la obsercaci�n m�s reciente									 | 
// Noviembre 12 de 2013 Jonatan	Lopez		Se agregan "UNION" a las consultas donde se encuentre la tabla movhos_000003 					 |
// 											para que traiga los datos de contingencia (tabla movhos_00143) con estado activo.				 |
// Mayo 10 de 2013		Camilo Zapata		Se modific� el query para que no tenga en cuenta los articulos de lactario. 					 |																								 |
// Abril 24 de 2013		 (Edwin MG)			Se agrega order by en la consulta principal para que tenga en cuenta los saldos para articulos   |
//											codificados cargados desde CM.																	 |
// Octubre 30 de 2012	 (Edwin MG)			Se agrega el origen en la consulta principal para que no genere articulos repetidos				 |																									 |
// Septiembre 19 de 2012 (Edwin MG)			Se adicionan al reporte las columnas Saldo por Recibir y Saldo Actual.							 |
// Septiembre 03 de 2012 (Viviana Rodas)	Se borran unos echos que habian quedado de las consultas para realizar las pruebas               |
// Agosto 30 de 2012 	(Viviana Rodas) 	Se modifican las consultas agregando la tabla movhos_000020 para imprimir en orden las 			 |
//											habitaciones.    																				 | 
// Junio 15 de 2012 	(Viviana Rodas) 	Se agregaron las funciones consultaCentrosCostos y dibujarSelect que listan los centros de  	 |
//                  						costos en orden alfabetico, de un grupo seleccionado y dibujarSelect que construye el select	 |
//											de dichos centros de costos																		 |
// Noviembre 29 de 2011	(Edwin MG)	En la consulta principal, se verfica que los articulos de CM, no existan en SF para evitar duplicados	 |
//																																			 |
// ==========================================================================================================================================
$wactualiz = "Julio 13 de 2016";

session_start();
if (!isset($_SESSION['user']))
{
    echo "error";
} 
else
{
    $empresa = 'root';

    

    

    include_once("root/comun.php");

    $wactualiz="Noviembre 12 de 2013";
    
    //==============================================================================================================================================================================================
    // F U N C I O N E S
    //==============================================================================================================================================================================================
	
	/******************************************************************************************
	 * Consulta el Saldo por recibir en un piso
	 ******************************************************************************************/
	function consultarSaldoSinRecibir( $conex, $wbasedato, $whis, $wing, $wcco, $wart ){
	
		 $q = " SELECT SUM(fdecan) "
			 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000011 "
			 ."  WHERE fenhis = '".$whis."'"
			 ."    AND fening = '".$wing."'"
			 ."    AND ((fencco = '".trim($wcco)."'"
			 ."    AND   fencco = ccocod ) "
			 ."     OR  (fencco = ccocod "
			 ."    AND   ccotra = 'on' "
			 ."    AND   ccoima != 'on' "
			 ."    AND   ccofac = 'on')) "
			 ."    AND   fennum = fdenum "
			 ."    AND   fdeart = '".$wart."'"
			 ."    AND   fdedis = 'on' "
			 ."    AND   fdeest = 'on' "
			 ." HAVING COUNT(*) > 0 ";
		//Este segundo query es para los de CM
		 $q .="  UNION " 
			 ." SELECT COUNT( DISTINCT fdenum ) "
			 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000011 "
			 ."  WHERE fenhis = '".$whis."'"
			 ."    AND fening = '".$wing."'"
			 ."    AND ((fencco = '".trim($wcco)."'"
			 ."    AND   fencco = ccocod ) "
			 ."     OR  (fencco = ccocod "
			 ."    AND   ccotra = 'on' "
			 ."    AND   ccoima = 'on' "
			 ."    AND   ccofac = 'on')) "
			 ."    AND   fennum = fdenum "
			 ."    AND   fdeari = '".$wart."'"
			 ."    AND   fdedis = 'on' "
			 ."    AND   fdeest = 'on' "
			 ."    AND   fdelot != '' "
			 ." HAVING COUNT(*) > 0 ";
		 /*********************************************************************************************************************/
		/* Noviembre 12 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
		/*********************************************************************************************************************/	
		 $q  .=" UNION "
		     ." SELECT SUM(fdecan) "
			 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000143, ".$wbasedato."_000011 "
			 ."  WHERE fenhis = '".$whis."'"
			 ."    AND fening = '".$wing."'"
			 ."    AND ((fencco = '".trim($wcco)."'"
			 ."    AND   fencco = ccocod ) "
			 ."     OR  (fencco = ccocod "
			 ."    AND   ccotra = 'on' "
			 ."    AND   ccoima != 'on' "
			 ."    AND   ccofac = 'on')) "
			 ."    AND   fennum = fdenum "
			 ."    AND   fdeart = '".$wart."'"
			 ."    AND   fdedis = 'on' "
			 ."    AND   fdeest = 'on' "
			 ." HAVING COUNT(*) > 0 "
		//Este segundo query es para los de CM
		     ."  UNION " 
			 ." SELECT COUNT( DISTINCT fdenum ) "
			 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000143, ".$wbasedato."_000011 "
			 ."  WHERE fenhis = '".$whis."'"
			 ."    AND fening = '".$wing."'"
			 ."    AND ((fencco = '".trim($wcco)."'"
			 ."    AND   fencco = ccocod ) "
			 ."     OR  (fencco = ccocod "
			 ."    AND   ccotra = 'on' "
			 ."    AND   ccoima = 'on' "
			 ."    AND   ccofac = 'on')) "
			 ."    AND   fennum = fdenum "
			 ."    AND   fdeari = '".$wart."'"
			 ."    AND   fdedis = 'on' "
			 ."    AND   fdeest = 'on' "
			 ."    AND   fdelot != '' "
			 ." HAVING COUNT(*) > 0 ";
			 
		 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $row = mysql_fetch_array($res);
		 
		 $wsalsre = $row[0] or 0;  //Cantidad SIN RECIBIR
		 
		 return $wsalsre;
	}
	
    function mostrar_empresa($wemp_pmla)
     {  
	  global $user;   
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
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
	         }  
	     }
	    else
	       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	  
	  $winstitucion=$row[2];
	      
	  encabezado("REPORTE DE SALDOS X APLICAR X PACIENTE",$wactualiz, "clinica");
     }
	 
	 
	 
	function consultaCentrosCostosNoDomiciliarios( $conex, $wbasedato ){
	
		$coleccion = array();
		
		$sql = "SELECT Ccocod, UPPER( Cconom )
				  FROM ".$wbasedato."_000011
				 WHERE Ccoest  = 'on' 
				   AND Ccohos  = 'on' 
				   AND ccodom != 'on'
			  ORDER BY Ccoord, Ccocod; ";
						  
		$res1 = mysql_query($sql,$conex) or die (" Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
		$num1 = mysql_num_rows($res1);

		if ($num1 > 0 )
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$cco = new centroCostosDTO();
				$row1 = mysql_fetch_array($res1);

				$cco->codigo = $row1[0];
				$cco->nombre = $row1[1];

				$coleccion[] = $cco;
			}
		}
		
		return $coleccion;
	}
	
	function consultaCentrosCostosDomiciliarios( $conex, $wbasedato ){
	
		$coleccion = array();
		
		$sql = "SELECT Ccocod, UPPER( Cconom )
				  FROM ".$wbasedato."_000011
				 WHERE Ccoest  = 'on' 
				   AND Ccohos  = 'on' 
				   AND ccodom  = 'on'
			  ORDER BY Ccoord, Ccocod; ";
						  
		$res1 = mysql_query($sql,$conex) or die (" Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
		$num1 = mysql_num_rows($res1);

		if ($num1 > 0 )
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$cco = new centroCostosDTO();
				$row1 = mysql_fetch_array($res1);

				$cco->codigo = $row1[0];
				$cco->nombre = $row1[1];

				$coleccion[] = $cco;
			}
		}
		
		return $coleccion;
	}
    
    function elegir_centro_de_costo()   
     {
	  global $conex;
	  global $wbasedato;
	  global $wtabcco;
	  global $wcco;  
	  
	  
	  global $whora_par_actual;
	  global $whora_par_anterior;
	  
	  global $esServicioDomiciliario;
	  
	  //Seleccionar CENTRO DE COSTOS
	  //*********************llamado a las funciones que listan los centros de costos y la que dibuja el select************************
		$cco="Ccohos";
		$sub="on";
		$tod="";
		//$cco=" ";
		$ipod="on";
		// $centrosCostos = consultaCentrosCostos($cco);
		
		if( $esServicioDomiciliario )
			$centrosCostos = consultaCentrosCostosDomiciliarios($conex,$wbasedato);
		else
			$centrosCostos = consultaCentrosCostosNoDomiciliarios($conex,$wbasedato);
		
		echo "<center><table align='center' border=0 >";
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);
					
		echo $dib;
		echo "</table></center>";
      
     } 
     
     
    function query_articulos($whis, $wing, $wfecha, &$resart, $wart, &$wnom, &$wsus)
     {
	  global $conex;
	  global $wbasedato;
	  global $wcenmez;
	  global $wemp_pmla;
	  
	  	  
	  //Traigo los Kardex GENERADOS con articulos de DISPENSACION 
	  $q = " SELECT kadhin, percan, peruni, kadcfr, kadufr, artcom, kadfin, kadcnd, kadobs, kadsus "
	      ."   FROM ".$wbasedato."_000054 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C " 
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"  
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'SF' "
	      ."    AND kadper  = percod "
	      ."    AND kadart  = '".$wart."'"
	      ." UNION "
	      //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION 
	      ." SELECT kadhin, percan, peruni, kadcfr, kadufr, artcom, kadfin, kadcnd, kadobs, kadsus "
	      ."   FROM ".$wbasedato."_000060 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C " 
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"  
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'SF' "
	      ."    AND kadper  = percod "
	      ."    AND kadart  = '".$wart."'";
	  $resart = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($resart); 
	  
	  if ($num==0)
	     {
		  //Traigo los Kardex GENERADOS con articulos de CENTRAL DE MEZCLAS
	      $q = " SELECT kadhin, percan, peruni, kadcfr, kadufr, artcom, kadfin, kadcnd, kadobs, kadsus "
	      	  ."   FROM ".$wbasedato."_000054 A, ".$wcenmez."_000002 B, ".$wbasedato."_000043 C " 
		      ."  WHERE kadhis  = '".$whis."'"
		      ."    AND kading  = '".$wing."'"
		      ."    AND kadfec  = '".$wfecha."'"  
		      ."    AND kadest  = 'on' "
		      ."    AND kadart  = artcod "
		      ."    AND kadori  = 'CM' "
		      ."    AND kadper  = percod "
		      ."    AND kadart  = '".$wart."'"
		      ." UNION "   
			  //Traigo los Kardex en TEMPORAL (000060) con articulos de CENTRAL DE MEZCLAS
		      ." SELECT kadhin, percan, peruni, kadcfr, kadufr, artcom, kadfin, kadcnd, kadobs, kadsus "
		      ."   FROM ".$wbasedato."_000060 A, ".$wcenmez."_000002 B, ".$wbasedato."_000043 C " 
		      ."  WHERE kadhis  = '".$whis."'"
		      ."    AND kading  = '".$wing."'"
		      ."    AND kadfec  = '".$wfecha."'"  
		      ."    AND kadest  = 'on' "
		      ."    AND kadart  = artcod "
		      ."    AND kadori  = 'CM' "
		      ."    AND kadper  = percod "
		      ."    AND kadart  = '".$wart."'";
		  $resart = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
		  $num = mysql_num_rows($resart);
	     }    
	  
	  $row = mysql_fetch_array($resart);
	  $wsus = $row[9];                             //Aca se identifica si el medicamento esta SUSPENDIDO
	  if ($num > 0) mysql_data_seek ($resart,0); 
	     
	  //Si es igual a 0 es porque el articulo ya no esta en el kardex (pudo ser suspendido), entonces entro a buscar el nombre del articulo
	  if ($num == 0)
	     {
		  $q = " SELECT artcom "
		      ."   FROM ".$wbasedato."_000026 "
		      ."  WHERE artcod = '".$wart."'";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
		  $num = mysql_num_rows($res);
		  
		  if ($num==0)   //Si entra articulo NO existe en la 000026
		     {
			  $q = " SELECT artcom "
			      ."   FROM ".$wcenmez."_000002 "
			      ."  WHERE artcod = '".$wart."'";
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
			 }
		     
		   $row = mysql_fetch_array($res);
		   $num = mysql_num_rows($res);
		   
		   if ($num > 0)   //Si entra es porque encontro el nombre
		      $wnom=$row[0];
		     else
	            $wnom="Articulo No existe";
		 }       
	 }      
	//==============================================================================================================================================================================================
	//==============================================================================================================================================================================================
	
	
	//==============================================================================================================================================================================================
	// P R I N C I P A L
	//==============================================================================================================================================================================================
	$wfecha = date("Y-m-d");
     
    echo "<form NAME=salxaplixpac action='rep_salxaplixpac.php' method=post>";
    
    echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";   
    
    mostrar_empresa($wemp_pmla);
	
	$esServicioDomiciliario = false;
	if( isset($servicioDomiciliario) && $servicioDomiciliario == 'on' ){
		$esServicioDomiciliario = true;
		echo "<input type='HIDDEN' NAME= 'servicioDomiciliario' value='".$servicioDomiciliario."'/>";
	}
    
    if (!isset($wcco))
     {
      elegir_centro_de_costo();
     } 
    else // Cuando ya estan todos los datos escogidos
       {
		   
        $wcco1 = explode('-', $wcco);

		$tablaHabitaciones = consultarTablaHabitaciones( $conex, $wbasedato, $wcco1[0] );
		
		$query = "SELECT ccogka
					FROM ".$wbasedato."_000011
					WHERE ccolac = 'on'";
		$rs = mysql_query( $query, $conex );
		$row = mysql_fetch_array( $rs );
		$aux = explode( ",", $row[0] );
		foreach( $aux as $i=>$dato )
		{
			$aux[$i] = "'".$dato."'";
		}
		$grupos = implode( ",", $aux );
		
        echo '<center><h2 class="seccion1"><b>CENTRO DE COSTOS:'.$wcco1[0].'-'.$wcco1[1].'</b></h2></center>'; 
        
        ////////////////////
        ////////////////////
		$q = "DROP TEMPORARY TABLE IF EXISTS TEMPO";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		$q = " CREATE TEMPORARY TABLE if not exists TEMPO as "
            ." SELECT ubihac, ubisac, spaart as art, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, SUM(spauen-spausa) as sal, artuni, habcod, habord "
            ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000004, ".$wbasedato."_000026, root_000036, root_000037, ".$wbasedato."_000029, ".$tablaHabitaciones." "
            ."  WHERE ubiald                              <> 'on' "
            ."    AND ubihis                               = spahis "
            ."    AND ubiing                               = spaing " 
            ."    AND spaart                               = artcod "
            ."    AND (spauen-spausa)                      > 0 "
            ."    AND spahis                               = orihis "
            ."    AND spaing                               = oriing "
            ."    AND oriced                               = pacced "
            ."    AND oritid                               = pactid "
            ."    AND ubisac                            LIKE '".trim($wcco1[0])."'"
            ."    AND mid(artgru,1,instr(artgru,'-')-1)    = gjugru "
			."    AND mid(artgru,1,instr(artgru,'-')-1)    NOT IN ( ".$grupos." ) "
			."    AND ubihac                               = habcod "
			."    AND ubisac                               = habcco "
			."	  AND oriori							   = '$wemp_pmla' "		// Octubre 30 de 2012
			."GROUP BY 3,12 "
            ."  UNION "
            ." SELECT ubihac, ubisac, spaart as art, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, (spauen-spausa) as sal, artuni, habcod, habord "
            ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000004, ".$wcenmez."_000002, root_000036, root_000037, ".$tablaHabitaciones." "
            ."  WHERE ubiald         <> 'on' "
            ."    AND ubihis          = spahis "
            ."    AND ubiing          = spaing " 
            ."    AND spaart          = artcod "
            ."    AND (spauen-spausa) > 0 "
            ."    AND spahis          = orihis "
            ."    AND spaing          = oriing "
            ."    AND oriced          = pacced "
            ."    AND oritid          = pactid "
            ."    AND ubisac          = '".trim($wcco1[0])."'"
			."    AND spaart NOT IN (SELECT artcod FROM {$wbasedato}_000026 WHERE artcod = spaart )"
			."    AND ubihac                               = habcod "
			."    AND ubisac                               = habcco "
			."	  AND oriori							   = '$wemp_pmla' "	// Octubre 30 de 2012
         //   ."  ORDER BY 4,5,1,3";
			."  ORDER BY habord,1 ";
			
		 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

         $q = " SELECT ubihac, ubisac, cconom, art, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, sum(sal), artuni, " .$tablaHabitaciones.".habcod, " .$tablaHabitaciones.".habord "
             ."   FROM TEMPO, ".$wbasedato."_000011," .$tablaHabitaciones." "
             ."  WHERE ubisac  = Ccocod"
             ."    AND ubihis <> '' "
			 ."    AND ubihac = " .$tablaHabitaciones.".habcod "
			 ."    AND ubisac = " .$tablaHabitaciones.".habcco "
             ."  GROUP BY 1,2,3,4,5,6,7,8,9,10, 12 "
             //."  ORDER BY 2,1,6,4";
			 ."  ORDER BY habord,1";
         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num = mysql_num_rows($res); 
        
         echo "<table align=center>";
         
         $row = mysql_fetch_array($res);
         if ($num > 0) mysql_data_seek ($res,1);
         
         $i=1;
         while ($i<=$num)
            {
             $whab = $row[0];
             $wpac = $row[6]." ".$row[7]." ".$row[8]." ".$row[9];
             $whis = $row[4];
             $wing = $row[5];

             echo "<tr></tr>";
             echo "<tr class=encabezadoTabla>";
             echo "<th align=center colspan=1><b>Hab: <font color='FFFF00' size=5>".$whab."</font></b></th>";
             echo "<th align=center colspan=1><b>Histor�a: ".$whis." - ".$wing."</b></th>";
             echo "<th align=center colspan=11><b>Paciente: ".$wpac."</b></th>";
             echo "</tr>";
             echo "<tr bgcolor='BDBDBD'>";
	         echo "<th align=center colspan=2><b>Articulo</b></th>";
	         echo "<th align=center><b>Unidad</b></th>";
	         echo "<th align=center colspan=2><b>Dosis</b></th>";
	         echo "<th align=center><b>Fecha Inicio</b></th>";
	         echo "<th align=center><b>Hora Inicio</b></th>";
	         echo "<th align=center><b>Frecuencia</b></th>";
	         echo "<th align=center><b>Saldo Total</b></th>";
			 echo "<th align=center><b>Saldo por recibir</b></th>";
			 echo "<th align=center><b>Saldo Actual</b></th>";
	         echo "<th align=center><b>Condici�n</b></th>";
	         echo "<th align=center><b>Observaciones</b></th>";
	         echo "</tr>";
             
             while ($whis==$row[4] and $i<=$num)
                {  
	             //==========================================================================================================   
	             query_articulos($whis, $wing, $wfecha, $resart, $row[3], $wnom, $wsus);
			     $numart = mysql_num_rows($resart);
			    
			     if ($numart == 0)  //Si no se encuentra Kardex Confirmado en la fecha actual, traigo kardex del dia anterior
			        {
				     $dia = time()-(1*24*60*60); //Te resta un dia. (2*24*60*60) te resta dos y //asi...
	                 $wayer = date('Y-m-d', $dia); //Formatea dia 
					
	                 query_articulos($whis, $wing, $wayer, $resart, $row[3], $wnom, $wsus);
				     $numart = mysql_num_rows($resart);
				    } 
				    
				 if ($numart > 0)
				   {
					$rowart = mysql_fetch_array($resart);   
					$whin=$rowart[0];                      // Hora Inicio  
					$wfre=$rowart[1]."&nbsp".$rowart[2];   // Frecuencia 
					$wdos=$rowart[3];                      // Dosis
					$wfra=$rowart[4];                      // Fraccion de la dosis
					$wnom=$rowart[5];                      // Descripcion del articulo
					$wfin=$rowart[6];                      // Fecha de Inicio
					$wobs=$rowart[8];                      // Observaciones
					
					if( !empty($wobs) ){
						//Dejo las observaciones mostrando lo �ltimo ingresado
						$wobs = explode( "<div>", $wobs );
						$wobs = explode( "</div>", $wobs[0] );
						$wobs = $wobs[0]."</div>";
					}
					
					if ($rowart[7] != "")
					   {
						$q = " SELECT condes "
					        ."   FROM ".$wbasedato."_000042 "
					        ."  WHERE concod = '".$rowart[7]."'";
					    $rescnd = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					      
					    $rowcnd = mysql_fetch_array($rescnd);
					      
					    $wcnd = $rowcnd[0];                //Condicion
				       }
				      else
				         $wcnd=""; 	   
			       }
			      else
			        {
					 $whin="";   // Hora Inicio  
					 $wfre="";   // Frecuencia 
					 $wdos="";   // Dosis
					 $wfra="";   // Fraccion de la dosis
					 //$wnom="";   // Descripcion del articulo
					 $wfin="";
					 $wcnd="";
					 $wobs="";
			        }    
	             //==========================================================================================================
	                
	             if (is_int ($i / 2))
	                {$wclass = "fila1";} 
	               else
	                 {$wclass = "fila2";}
                 
	             if ($wsus=="on")
	                {
		             echo "<tr class=fondoVioleta>";
			         echo "<td align=center>".$row[3]."</td>";                     //Codigo Articulo
			         echo "<td>".$wnom."</td>";                                    //Nombre Articulo
			         echo "<td>".$row[11]."</td>";                                 //Unidad Medida
			         echo "<td align=center colspan=5><b>Suspendido</b></td>";     //Suspendido
			         echo "<td align=center>".number_format($row[10],2,'.',',')."</td>";                    //Saldo
			         echo "<td colspan=4>&nbsp</td>";                              //Espacio en blanco
			         echo "</tr>";
	                }
	               else
	                  {               
					   //Consulto el saldo por recibir
					   $wsalxrec = consultarSaldoSinRecibir( $conex, $wbasedato, $whis, $wing, $wcco1[0], $row[3] );	//Septiembre 19 de 2012
			          
					   echo "<tr class=".$wclass.">";
			           echo "<td align=center>".$row[3]."</td>";    //Codigo Articulo
			           echo "<td>".$wnom."</td>";                   //Nombre Articulo
			           echo "<td>".$row[11]."</td>";                //Unidad Medida
			           echo "<td align=right>".$wdos."</td>";       //Dosis
			           echo "<td>".$wfra."</td>";                   //Fraccion de la dosis
			           echo "<td align=center>".$wfin."</td>";      //Fecha de Inicio
			           echo "<td align=center>".$whin."</td>";      //Hora de Inicio
			           echo "<td align=right>C / ".$wfre."S</td>";  //Frecuencia
					   echo "<td align=center>".number_format($row[10],2,'.',',')."</td>";   //Saldo total
					   echo "<td align=center>".number_format($wsalxrec,2,'.',',')."</td>";   //Saldo por recibir
					   echo "<td align=center>".number_format($row[10] - $wsalxrec,2,'.',',')."</td>";   //Saldo actual (total - por recibir)
			           echo "<td align=center>".$wcnd."</td>";      //Condicion
			           echo "<td align=left  >".$wobs."</td>";      //Observacion
			           echo "</tr>";
		              } 
	             
	             $row = mysql_fetch_array($res);
	             
	             $i++;
	            } 
	        } // fin del for
	        
	        echo "<br><br><br>";
		    echo "<center><table>";
			if($esServicioDomiciliario)
				echo "<tr><td><A HREF='rep_salxaplixpac.php?wemp_pmla=".$wemp_pmla."&servicioDomiciliario=on' class=tipo3V>Retornar</A></td></tr>";
			else
				echo "<tr><td><A HREF='rep_salxaplixpac.php?wemp_pmla=".$wemp_pmla."' class=tipo3V>Retornar</A></td></tr>";
		    echo "</table>";
	        
        } // cierre del else donde empieza la impresi�n
        echo "</table>"; // cierra la tabla o cuadricula de la impresi�n
        
        echo "<br>";
        echo "<center><table>";
        echo "<tr><td align=center colspan=8><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
        echo "</table>";
    } 

    ?>
