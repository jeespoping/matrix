<head>
	<title>IMPRESION DEL DETALLE DE UNA FACTURA</title>
	<style type="text/css">
		.fila1
		{
			 background-color: #C3D9FF;
			 color: #000000;
		}
		.fila2
		{
			 background-color: #E8EEF7;
			 color: #000000;
		}
	</style>
	<script type="text/javascript">
		function enter()
		{
		   document.forms.Imp_det_Factura.submit();
		}

		function enter1()
		{
		   document.forms.Imp_det_Factura.submit();
		   alert ("Pulse de nuevo la tecla ENTER");
		}

	</script>
</head>

<body onload=ira()>

<?php
include_once("conex.php");
  /*******************************************************
   *   REPORTE DEL DETALLE DE UNA FACTURA HOSPITALARIA   *
   *******************************************************/

//==================================================================================================================================
//PROGRAMA                   : Imp_det_factura.php
//AUTOR                      : Juan Carlos Hernández M.
  $wautor="Juan C. Hernandez M.";
//FECHA CREACION             : Septiembre 27 de 2006
//FECHA ULTIMA ACTUALIZACION : Enero 27 de 2011
  $wactualiz="(Diciembre 24 de 2013)";
//DESCRIPCION
//====================================================================================================================================\\
//Objetivo:                                                                                                                           \\
//====================================================================================================================================\\
//Poder imprimir en forma detallado los cargos cobrados o facturados en una factura, independiente del responsable

//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//-------------------------------------------------------------------------------------------------------------------------------------------
//	-->	2013-12-24, Jerson trujillo.
//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//-------------------------------------------------------------------------------------------------------------------------------------------
//________________________________________________________________________________________________________________________________________\\
//Enero 27 DE 2011:                                                                                                         		  	  \\
//________________________________________________________________________________________________________________________________________\\
//Modificación de estilos para adaptar el espacio físico de impresión como se venía manjando antes de la actualización de Enero 24 DE 2011\\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//Enero 24 DE 2011:                                                                                                         		  	  \\
//________________________________________________________________________________________________________________________________________\\
//Actualización de estilos para el reporte						 							                                              \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//Diciembre 30 DE 2010:                                                                                                         		  \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica los tipos de letras, colores, y fondos usando los estilos del css del sistema                                               \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//Septiembre 17 DE 2008:                                                                                                         		  \\
//________________________________________________________________________________________________________________________________________\\
//Se adiciona la columna de usuario que grabo el cargo                                                                                    \\
//________________________________________________________________________________________________________________________________________\\
//Noviembre  27 DE 2007:                                                                                                         		  \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa con el fin de que detalle los conceptos de las facturas que vienen por paquete pero que no pertenecen a dicho   \\
//                                                                                                                                        \\
//========================================================================================================================================\\
//========================================================================================================================================\\



//require_once('FirePHP.class.php');

$MSJ_ERROR_FALTA_PARAMETRO = "Por favor contactar a soporte.  Error: no se encuentra parametro: ";
$MSJ_ERROR_SESION_CADUCADA = "Error: La sesión ha caducado.";

// --> 2013-12-24. Jerson Trujillo.
function consultarAliasPorAplicacion($conexion, $codigoInstitucion, $nombreAplicacion){
	$q = " SELECT
				Detval
			FROM
				root_000051
			WHERE
				Detemp = '".$codigoInstitucion."'
				AND Detapl = '".$nombreAplicacion."'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$alias = "";
	if ($num > 0)
	{
		$rs = mysql_fetch_array($res);

		$alias = $rs['Detval'];
	} else {
		die("La institucion con el codigo :".$codigoInstitucion." no se encuentra, ni la aplicacion: ".$nombreAplicacion.".  Por favor verifique el valor de wemp_pmla");
	}
	return $alias;
}

/**
 * Esta es la forma antigua de conexion a la base de datos... Refinar implementacion con el pool
 *
 * @return conexionBD
 */

function obtenerConexionBD($nombreBD){
	global $conex;

	if(!empty($nombreBD)){
		//$cn = mysql_connect('localhost','root','1301') or die("Imposible conectarse a la base de datos ".$nombreBD.":: ".mysql_error());
		if($conex){
			mysql_select_db($nombreBD);
		} else {
			die("Imposible conectarse a la base de datos ".$nombreBD.":: ".mysql_error());
		}
	}
	return $conex;
}

/**
 * Implementacion de terminacion de aplicacion
 *
 * @param unknown_type $mensaje
 */
function terminarEjecucion($mensaje){
	die($mensaje);
}


$conex = obtenerConexionBD("matrix");

$wactualiz="Vers. 27-Enero-2011";

//VAlidación de usuario
$usuarioValidado = true;
if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();

//Si el usuario no es válido se informa y no se abre el reporte
if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} // Fin IF si el usuario no es válido
else //Si el usuario es válido comenzamos con el reporte
{  //Inicio ELSE reporte

 //Conexion base de datos
 



  $wfecha=date("Y-m-d");
  $whora = (string)date("H:i:s");

  echo "<form name='Imp_det_factura' action='Imp_det_factura.php' method=post>";

  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";

	//---------------------------------------------------------------------------------------------
	// --> 	Consultar si esta en funcionamiento la nueva facturacion
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//---------------------------------------------------------------------------------------------
	$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
	//---------------------------------------------------------------------------------------------
	// --> 	MAESTRO DE CONCEPTOS:
	//		- Antigua facturacion 	--> 000004
	//		- Nueva facturacion 	--> 000200
	//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
	//		de conceptos cambiara por la tabla 000200.
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//----------------------------------------------------------------------------------------------
	$tablaConceptos = $wbasedato.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
	//----------------------------------------------------------------------------------------------

 /*
 $q = " SELECT fenffa, fenfac, fenfec, fencod, fennit, empnom, fenval, fenviv, fencop, fencmo, fendes, fenabo, fenvnd, fenvnc, fensal, fenesf, "
     ."        fenhis, fening, fencre, fenpde, fenrec, fentop, fenrln, fenest, pacap1, pacap2, pacno1, pacno2, cfgnom, fentip, pacdoc "
     ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000024, ".$wbasedato."_000100, ".$wbasedato."_000049 "
 */

  $q = " SELECT Venffa,Venfec,Vennum,Empcod ,".$wbasedato."_000016.Fecha_data,Empnit,Empnom, Clinom ,Venvto, Venviv,Vendes, Vencop
           FROM ".$wbasedato."_000016 LEFT JOIN ".$wbasedato."_000041 ON Clidoc = Vennit , ".$wbasedato."_000024
       	  WHERE Vennum like '".$wfac."'
           	AND Vencod = empcod";
 $res = mysql_query($q,$conex);
 $num = mysql_num_rows($res);
 $row = mysql_fetch_array($res);

 $wtid = "";
 $query100 = " SELECT Pactdo
                 FROM {$wbasedato}_000100
                WHERE Pachis = '{$row[16]}'";
 $rs100    = mysql_query( $query100, $conex );
 while ( $row100 = mysql_fetch_array( $rs100 ) ){
     $wtid = $row100['Pactdo'];
 }

     //$wfue       = $row[0];
     $wfac       = $row['Vennum'];
     $wfec       = $row['Venfec'];
     $wcem       = $row['Empcod'];
     $wnit       = $row['Empnit'];
     $wnem       = $row['Empnom'];
     /*$wval       = $row[6];
     $wiva       = $row[7];
     $wcop       = $row[8];
     $wcmo       = $row[9];
     $wdes       = $row[10];
     $wabo       = $row[11];
     $wvnd       = $row[12];
     $wvnc       = $row[13];
     $wsal       = $row[14];
     $wesf       = $row[15];
     $whis       = $row[16];
     $wing       = $row[17];
     $wcre       = $row[18];
     $wpde       = $row[19];
     $wrec       = $row[20];
     $wtop       = $row[21];
     $wrln       = $row[22];
     $west       = $row[23];
     //$wap1       = $row[24];
     //$wap2       = $row[25];
     //$wno1       = $row[26];
     //$wno2       = $row[27];

     $wins       = $row[25];  //Institucion o clinica
     $wtfa       = $row[26];  //Tipo de factura
	 */
	 $wnpa       = $row['Clinom'];
     $wdoc       = $row['Vennit'];  //Identificacion del paciente

     echo "<br>";
     echo "<center><table border=0>";
     // echo "<tr>";
     // echo "<td align=left class='fila2' colspan=1><b>FACTURACION</td>";
	 // echo "<td align=center class='fila2' colspan=1><b>".$wins."</b></td>";
	 // echo "<td align=right class='fila2' colspan=1><b> Fecha:".$wfecha."</b></td>";
	 // echo "</tr>";

	 // echo "<tr>";
     // echo "<td align=left class='fila2' colspan=1>&nbsp;<b>Imp_det_factura.php</b>&nbsp;</td>";
	 // echo "<td align=center class='fila2' colspan=1>&nbsp;<b>DETALLE DE CARGOS POR VENTA</b>&nbsp;</td>";
	 // echo "<td align=right class='fila2' colspan=1>&nbsp;<b>Hora:".$whora."</b>&nbsp;</td>";
	 // echo "</tr>";

	 //echo "</table>";


     //echo "<center><table border=0>";

     //ACA PINTO EL ENCABEZADO DE LA FACTURA
     echo "<tr>";
	// echo "<td align=center colspan=3 class='fila2' colspan=1> Fuente: &nbsp&nbsp <b>".$wfue."</b> &nbsp&nbsp Factura: &nbsp&nbsp <b>".$wfac."</b> &nbsp&nbsp Fecha: &nbsp&nbsp <b>".$wfec."</b></td>";
	 echo "<td align=center colspan=3 class='fila2' colspan=1> Venta: &nbsp&nbsp <b>".$wfac."</b> &nbsp&nbsp Fecha: &nbsp&nbsp <b>".$wfec."</b></td>";
	 echo "</tr>";
	 echo "<tr>";
	 echo "<td align=center colspan=3 class='fila2' colspan=1> Paciente:<b>&nbsp;&nbsp;".$wnpa."</b> &nbsp&nbsp Historia: &nbsp&nbsp<b>".$whis."-".$wing."</b> &nbsp&nbsp Identificación: <b>&nbsp;&nbsp;{$wtid}&nbsp&nbsp".$wdoc."</b></td>";
	 echo "</tr>";
	 echo "<tr>";

	 echo "</table>";


	 //Busco si la empresa actual factura Hospitalario o No
     $q = " SELECT emphos "
	     ."   FROM root_000050"
	     ."  WHERE empcod = '".$wemp_pmla."'"
	     ."    AND empest = 'on' ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $num = mysql_num_rows($res);
	 $row = mysql_fetch_array($res);

	 if ($row[0] == "on")  //Facturacion Hospitalaria
	    {
		 $q = "  SELECT rcfreg, tcarconcod, grudes, tcarfec, tcarprocod, pronom, tcartercod, tcarcan, tcarvun, tcarvto, tcarfex, tcarfre, rcfval, tcarusu  "
	         ."    FROM ".$wbasedato."_000106, ".$wbasedato."_000066, ".$tablaConceptos.", ".$wbasedato."_000103 "
	         ."   WHERE rcfffa      = '".$wfue."'"
	         ."     AND rcffac      = '".$wfac."'"
	         ."     AND rcfreg      = ".$wbasedato."_000106.id "
	         ."     AND tcarconcod  = grucod "
	         ."     AND grutab     != 'on' "
	         ."     AND tcarprocod  = procod "
	         ."     AND tcarprocod  = procod "
			 ."		AND proest = 'on' "
	         ." UNION "

	         ."  SELECT rcfreg, tcarconcod, grudes, tcarfec, tcarprocod, artnom, tcartercod, tcarcan, tcarvun, tcarvto, tcarfex, tcarfre, rcfval, tcarusu  "
	         ."    FROM ".$wbasedato."_000106, ".$wbasedato."_000066, ".$tablaConceptos.", ".$wbasedato."_000001 "
	         ."   WHERE rcfffa      = '".$wfue."'"
	         ."     AND rcffac      = '".$wfac."'"
	         ."     AND rcfreg      = ".$wbasedato."_000106.id "
	         ."     AND tcarconcod  = grucod "
	         ."     AND grutab     != 'on' "
	         ."     AND tcarprocod  = artcod "
			 ."		AND artest = 'on' "
	         ."   ORDER BY 2, 4 ";
	     $res1 = mysql_query($q,$conex);
	     $num1 = mysql_num_rows($res1);

        }
       else             //Facturacion POS
          {
			// Nota: Aqui el uso de la tabla 4 es para manejo de grupos de inventario, no como conceptos de facturacion. 2013-12-24, Jerson trujillo.
	       // $q = "  SELECT rcfreg, artgru, grudes, ".$wbasedato."_000017.fecha_data, artcod, artnom, '', vdecan, vdevun, vdecan*vdevun, 0, 0, rcfval, ".$wbasedato."_000017.seguridad  "
		       // ."    FROM ".$wbasedato."_000017, ".$wbasedato."_000066, ".$wbasedato."_000004, ".$wbasedato."_000001 "
		       // ."   WHERE rcfffa      = '".$wfue."'"
		       // ."     AND rcffac      = '".$wfac."'"
		       // ."     AND rcfreg      = ".$wbasedato."_000017.id "
		       // ."     AND vdeart      = artcod "
		       // ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
		       // ."     AND grutab     != 'on' ";
		$q = "  SELECT '', artgru, grudes, ".$wbasedato."_000017.fecha_data, artcod, artnom, '', vdecan, vdevun, vdecan*vdevun, 0, 0, vdecan*vdevun, ".$wbasedato."_000017.seguridad  "
		       ."    FROM ".$wbasedato."_000017, ".$wbasedato."_000004, ".$wbasedato."_000001 , ".$wbasedato."_000016  "
		       ."   WHERE Vdenum     = '".$wfac."'"
			   ."     AND Vdenum     =  Vennum "
		       ."     AND vdeart      = artcod "
		       ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
		       ."     AND grutab     != 'on' ";
		   $res1 = mysql_query($q,$conex);
		   $num1 = mysql_num_rows($res1);
	      }

     if ($num1 > 0)
        {
	     $row1 = mysql_fetch_array($res1);

	     echo "<center><table border=0>";
	     $j=1;

	    // echo "<th class='fila1'>Registro</th>";
         echo "<th class='fila1'>Fecha</th>";
         echo "<th class='fila1'>Procedimiento o Articulo</th>";
         echo "<th class='fila1'>Tercero</th>";
         echo "<th class='fila1'>Cantidad</th>";
         echo "<th class='fila1'>Valor Unit.</th>";
         echo "<th class='fila1'>Valor Total</th>";
         echo "<th class='fila1'>Valor Reconocido</th>";
         if ($wtfa != "01-PARTICULAR")
            echo "<th class='fila1'>Valor Excedente</th>";
         echo "<th class='fila1'>Usuario Grabo</th>";

         $wgtotgracon=0;
	     $wgtotfaccon=0;

	     //===========================================================
	     $wtiene_paq="off";
	     // Aca busco si la factura corresponde a un paquete
	     //===========================================================
			// no hace nada
		 /*$q = " SELECT movpaqcod, paqnom, sum(rcfval) "
	         ."   FROM ".$wbasedato."_000066, ".$wbasedato."_000115, ".$wbasedato."_000018, ".$wbasedato."_000113 "
	         ."  WHERE rcfffa    = '".$wfue."'"
	         ."    AND rcffac    = '".$wfac."'"
	         ."    AND rcfreg    = movpaqreg "
	         ."    AND rcfffa    = fenffa "
	         ."    AND rcffac    = fenfac "
	         ."    AND paqcod    = movpaqcod "
	         ."    AND movpaqest = 'on' "
	         ."  GROUP BY 1, 2 ";
	     $res_paq = mysql_query($q,$conex);
	     $num_paq = mysql_num_rows($res_paq);

	     if ($num_paq > 0)
	        {
	         $wtiene_paq="on";
	         for ($i=1;$i<=$num_paq;$i++)
	            {
		         $row_paq = mysql_fetch_array($res_paq);

		         $wcodpaq[$i]=$row_paq[0];
		         $wnompaq[$i]=$row_paq[1];
		         $wvalpaq[$i]=$row_paq[2];
		        }
	        }*/

         if ($wtiene_paq == "off")
            {
		     while ($j <= $num1)
		        {
		         echo "<tr>";
			     echo "<td align=left class='fila2' colspan=9><b>Concepto:".$row1[1]." : ".$row1[2]."</b></td>";
			     echo "</tr>";
			     $wcodcon=$row1[1];
			     $wnomcon=$row1[2];


			     $wtotgracon=0;
		         $wtotfaccon=0;

		         while ($wcodcon==$row1[1] and $j<=$num1)
		            {
			         echo "<tr class='fondoBlanco' >";
			         //echo "<td>".$row1[0]."</td>";	                                 //Registro
			         echo "<td>".$row1[3]."</td>";	                                 //Fecha

			         //Aca busco si el procedimiento y la empresa tienen algun registro en la tabla relacion procedimeintos-empresas
			         //e imprimo con el codigo de la empresa.
			         $q = " SELECT proemppro, proempnom "
			             ."   FROM ".$wbasedato."_000070 "
			             ."  WHERE proempcod = '".$row1[4]."'"
			             ."    AND proempemp = '".$wcem."'"
			             ."    AND proempest = 'on' ";
			         $res2 = mysql_query($q,$conex);
	                 $num2 = mysql_num_rows($res2);

	                 if ($num2 > 0)
	                    {
		                 $row2 = mysql_fetch_array($res2);
	                     $wcodpro = $row2[0];
	                     $wnompro = $row2[1];
	                    }
	                   else
	                      {
		                   $wcodpro = $row1[4];
	                       $wnompro = $row1[5];
	                      }


			         echo "<td>".$wcodpro." - ".$wnompro."</td>";                                //Procedimiento o Examen
			         if ($row1[6] != "")
			            echo "<td>".$row1[6]."</td>";                                            //Tercero
			           else
			              echo "<td>&nbsp</td>";                                                 //Tercero
					 echo "<td align=right>".number_format($row1[7],2,'.',',')."</td>";          //Cantidad
			         echo "<td align=right>".number_format($row1[8],0,'.',',')."</td>";          //Valor Unitario
			         echo "<td align=right>".number_format($row1[9],0,'.',',')."</td>";          //Valor total
			         echo "<td align=right>".number_format($row1[12],0,'.',',')."</td>";         //Valor Facturado en esta factura
			         if ($wtfa != "01-PARTICULAR")
			            echo "<td align=right>".number_format(($row1[9]-$row1[12]),0,'.',',')."</td>";
			         echo "<td align=right>".$row1[13]."</td>";                                  //Usuario que grabo el cargo


			         $wtotgracon=$wtotgracon+$row1[9];
			         $wtotfaccon=$wtotfaccon+$row1[12];

			         $wgtotgracon=$wgtotgracon+$row1[9];
		             $wgtotfaccon=$wgtotfaccon+$row1[12];

		             $row1 = mysql_fetch_array($res1);
			         $j=$j+1;
			        }
			     echo "<tr>";
			     echo "<td align=LEFT colspan=5 class='fila2'><b>Total Concepto ".$wcodcon." : ".$wnomcon."</b></td>";
		         echo "<td align=RIGHT colspan=1 class='fila2'><b>".number_format($wtotgracon,0,'.',',')."</b></td>";
		         echo "<td align=RIGHT colspan=1 class='fila2'><b>".number_format($wtotfaccon,0,'.',',')."</b></td>";
		         if ($wtfa != "01-PARTICULAR")
		            echo "<td align=RIGHT colspan=1 class='fila2'><b>".number_format($wtotgracon-$wtotfaccon,0,'.',',')."</b></td>";
		         echo "<td align=RIGHT colspan=1 class='fila2'>&nbsp</td>";      //Columna en blanco
		         echo "</tr>";
	            }
	        }
	       else
             {  //Si es PAQUETE entra por aca
              $wtotal=0;
/*
               //2007-11-27
	           // para traer los conceptos que no pertenecen al paquete pero que estan en la factura
		      $q = " SELECT distinct(Tcarconcod), Grudes, fdeter, fdepte, fdevco, grutip"
				  ."   FROM ".$wbasedato."_000066, ".$wbasedato."_000106, ".$tablaConceptos.", ".$wbasedato."_000065"
		          ."  WHERE rcfffa    = '".$wfue."'"
		          ."    AND rcffac    = '".$wfac."'"
		          ."    AND fdefue = rcfffa"
		          ."    AND fdedoc = rcffac"
		          ."	AND grucod = fdecon"
		          ."    AND ".$wbasedato."_000106.id =Rcfreg"
		          ."    AND Tcarconcod = Grucod"
		          ."    and  Tcartfa !='PAQUETE'"
		          ."    and  Tcartfa !='ABONO'"
		          ."    and  Tcarfac ='S' "
		          ."    AND Tcarest = 'on'"
		          ."    AND Rcfest ='on'"
		          ."    AND Fdeest ='on'"
		          ."    AND Gruabo='off'"
		          ."    AND Rcfreg  not in (SELECT Movpaqreg from ".$wbasedato."_000115 where Movpaqhis=".$whis." and Movpaqing=".$wing." and  Movpaqcod='".$row_paq[0]."' and Movpaqest='on')";
		      $res_reg = mysql_query($q,$conex);
		      $num_reg = mysql_num_rows($res_reg);*/
/*
		      // entra solo si tiene conceptos que no pertenecen a la factura
		      if ($num_reg>0)
		      {
		      	//DETALLE DE LOS CONCEPTOS
		      	for ($i=1;$i<=$num_reg;$i++)
	             {
	             	$row_reg = mysql_fetch_array($res_reg);

	             	echo "<tr>";
			     	echo "<td align=left class='fila2' colspan=10><b>Concepto:".$row_reg[0]." : ".$row_reg[1]."</b></td>";
			     	echo "</tr>";

	             	 $q1 = "  SELECT rcfreg, tcarconcod, grudes, tcarfec, tcarprocod, tcarpronom, tcartercod, tcarcan, tcarvun, tcarvto, tcarfex, tcarfre, rcfval, tcarusu  "
				         ."    FROM ".$wbasedato."_000106, ".$tablaConceptos.", ".$wbasedato."_000066"
				         ."   WHERE tcarhis     = '".$whis."'"
				         ."     AND tcaring     = '".$wing."'"
				         ."     AND Tcarconcod  = '".$row_reg[0]."'"
				         ."     AND Tcartfa !='PAQUETE'"
				         ."     AND tcarfac = 'S' "
				         ."     AND tcarfre != 0 " // el query estaba < 0, debido a que era una devolucion, tenia que tenerla en cuenta 2010-12-23 (CS-161779)
				         ."     AND tcarconcod  = grucod "
				         ."     AND Tcarest = 'on'"
				         ."     AND rcfreg      = ".$wbasedato."_000106.id "
				         ."     AND rcffac      = '".$wfac."'" // se anexo esto porque estaba trayento todos los conceptos del ingreso 2010-12-21
				         ."   GROUP BY 1"
				         ."   ORDER BY 2, 4 ";
				     $res1 = mysql_query($q1,$conex);
				     $num1 = mysql_num_rows($res1);
				     //echo $q1;

				     $wtotgracon=0;
		        	 $wtotfaccon=0;
				     for ($K=1;$K<=$num1;$K++)
		             {
		             	$row1 = mysql_fetch_array($res1);

		             	echo "<tr>";
			         	echo "<td>".$row1[0]."</td>";	                                 //Registro
			         	echo "<td>".$row1[3]."</td>";	                                 //Fecha

			         	//Aca busco si el procedimiento y la empresa tienen algun registro en la tabla relacion procedimeintos-empresas
			         //e imprimo con el codigo de la empresa.
			         $q = " SELECT proemppro, proempnom "
			             ."   FROM ".$wbasedato."_000070 "
			             ."  WHERE proempcod = '".$row1[4]."'"
			             ."    AND proempemp = '".$wcem."'"
			             ."    AND proempest = 'on' ";
			         $res2 = mysql_query($q,$conex);
	                 $num2 = mysql_num_rows($res2);

	                 if ($num2 > 0)
	                    {
		                 $row2 = mysql_fetch_array($res2);
	                     $wcodpro = $row2[0];
	                     $wnompro = $row2[1];
	                    }
	                   else
	                      {
		                   $wcodpro = $row1[4];
	                       $wnompro = $row1[5];
	                      }


			         echo "<td>".$wcodpro." - ".$wnompro."</td>";                                //Procedimiento o Examen
			         if ($row1[6] != "")
			            echo "<td>".$row1[6]."</td>";                                            //Tercero
			           else
			              echo "<td>&nbsp</td>";                                                 //Tercero
					 echo "<td align=right>".number_format($row1[7],2,'.',',')."</td>";          //Cantidad
			         echo "<td align=right>".number_format($row1[8],0,'.',',')."</td>";          //Valor Unitario
			         echo "<td align=right>".number_format($row1[9],0,'.',',')."</td>";          //Valor total
			         echo "<td align=right>".number_format($row1[12],0,'.',',')."</td>";         //Valor Facturado en esta factura
			         if ($wtfa != "01-PARTICULAR")
			            echo "<td align=right>".number_format(($row1[9]-$row1[12]),0,'.',',')."</td>";
			         echo "<td align=right>".$row1[13]."</td>";                                  //Usuario oque grabo


			         $wtotgracon=$wtotgracon+$row1[9];
			         $wtotfaccon=$wtotfaccon+$row1[12];

			         $wgtotgracon=$wgtotgracon+$row1[9];
		             $wgtotfaccon=$wgtotfaccon+$row1[12];

		             }
				   $wcodcon=$row1[1];
			       $wnomcon=$row1[2];
					 echo "<tr>";
				     echo "<td align=LEFT colspan=6 class='fila2'><b>Total Concepto ".$wcodcon." : ".$wnomcon."</b></td>";
			         echo "<td align=RIGHT colspan=1 class='fila2'><b>".number_format($wtotgracon,0,'.',',')."</b></td>";
			         echo "<td align=RIGHT colspan=1 class='fila2'><b>".number_format($wtotfaccon,0,'.',',')."</b></td>";
			         if ($wtfa != "01-PARTICULAR")
			            echo "<td align=RIGHT colspan=1 class='fila2'><b>".number_format($wtotgracon-$wtotfaccon,0,'.',',')."</b></td>";
			         echo "<td align=RIGHT colspan=1 class='fila2'>&nbsp</td>";                 //Columna en Blanco
			         echo "</tr>";
				 }


		      }*/
/*
	          for ($i=1;$i<=$num_paq;$i++)
	             {

		          $wtotfaccon=0;
		          $wtotgracon=$wvalpaq[$i];

				  $qcan = "	 SELECT Tcarconcod, SUM(Tcarcan) AS cant  "
						 ."    FROM ".$wbasedato."_000106 a, ".$wbasedato."_000066 b "
						 ."   WHERE tcarhis = '".$whis."'"
						 ."     AND tcaring = '".$wing."'"
						 ."		AND Tcarprocod = '".$wcodpaq[$i]."' "
						 ."     AND Tcartfa = 'PAQUETE'"
						 ."     AND tcarfac = 'S' "
						 ."     AND tcarfre != 0 " // el query estaba < 0, debido a que era una devolucion, tenia que tenerla en cuenta 2010-12-23 (CS-161779)
						 ."     AND Tcarest = 'on'"
						 ."     AND a.id = Rcfreg"
						 ."     AND Rcffac = '".$wfac."'"
						 ."     AND Rcfffa = '".$wfue."'"
						 ."	  GROUP BY Tcarconcod "
						 ."	  ORDER BY cant DESC ";
				  $rescan = mysql_query($qcan,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcan." - ".mysql_error());;
				  $rowcan = mysql_fetch_array($rescan);

				  $cantidad = $rowcan['cant'];
				  if(!isset($cantidad) || !$cantidad || $cantidad=="" || $cantidad==0)
						$cantidad = 1;
				  $valor_unitario = $wvalpaq[$i] / $cantidad;
		          echo "<tr>";
			      echo "<td align=left class='fila2' colspan=4><b>Paquete:".$wcodpaq[$i]." - ".$wnompaq[$i]."</b> &nbsp; </td>";
			      echo "<td align=right class='fila2'>".$cantidad."</td>";
			      echo "<td align=right class='fila2'>".number_format($valor_unitario,0,'.',',')."</td>";
			      echo "<td align=RIGHT class='fila2' colspan=1><b>".number_format($wvalpaq[$i],0,'.',',')."</b></td>";


			      $wtotal=$wtotal+$wvalpaq[$i];
			      echo "<td align=RIGHT class='fila2' colspan=1><b>".number_format($wvalpaq[$i],0,'.',',')."</b></td>";
			      echo "</tr>";
	             }*/

	             //entra para sumar los conceptos que no pertenecen a la factura al total
	             if ($num_reg>0)
			      {
			      	$wtotal=$wtotal+$wgtotfaccon;
			      }

		     }
         echo "<tr>";
	     echo "<td align=LEFT colspan=5 class='fila1'><b>Total Cuenta :</b></td>";
	     if ($wtiene_paq == "off")
	        {
	         echo "<td align=RIGHT colspan=1 class='fila1'><b>".number_format($wgtotfaccon,0,'.',',')."</b></td>";
             echo "<td align=RIGHT colspan=1 class='fila1'><b>".number_format($wgtotfaccon,0,'.',',')."</b></td>";
		    }
	       else
	          {
               echo "<td align=RIGHT colspan=1 class='fila1'><b>".number_format($wtotal,0,'.',',')."</b></td>";
               echo "<td align=RIGHT colspan=1 class='fila1'><b>".number_format($wtotal,0,'.',',')."</b></td>";
              }

         echo "<td align=LEFT colspan=2 class='fila1'><b>&nbsp</b></td>";
         echo "</tr>";
        }

        echo "<tr></tr>";
        echo "<tr>";
        echo "<td align=center colspan=10 class='fila2'><b>Factura: ".$wfac." - Responsable : ".$wcem." - ".$wnit." - ".$wnem."</b></td>";
        echo "</tr>";
  echo "</table>";
  //  }
}
?>