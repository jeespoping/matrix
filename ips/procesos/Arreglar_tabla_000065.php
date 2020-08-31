<head>
  <title>Arreglar_tabla_000065</title>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	   document.forms.consultar_factura.submit();
	}

</script>
<script type="text/javascript">
	function enter1()
	{
	   document.forms.consultar_factura.submit();
	   alert ("Pulse de nuevo la tecla ENTER");
	}

</script>

<?php
include_once("conex.php");
  /************************************************
   *     PROGRAMA PARA LA CONSULTA DE FACTURAS    *
   ************************************************/

//==================================================================================================================================
//PROGRAMA                   : consultar_factura.php
//AUTOR                      : Juan Carlos Hernández M.
  $wautor="Juan C. Hernandez M.";
//FECHA CREACION             : Agosto 11 de 2006
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(Versión Diciembre 24 de 2013)";
//DESCRIPCION
//====================================================================================================================================\\
//Objetivo:                                                                                                                           \\
//====================================================================================================================================\\


//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//O C T U B R E  30  DE 2006:                                                                                                             \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que se puedan anular las facturas y liberen de nuevo los cargos y poderlos volver a facturar, además se    \\
//graba en la tabla de auditoria (000107) el registro de anulacion, teniendo en cuenta la historia, ingreso, fuente y numero de factura   \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//X X X X X X X X X  ## DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxx.                                                                                                                    \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//X X X X X X X X X  ## DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxx.                                                                                                                    \\
//                                                                                                                                        \\
//                                                                                                                                        \\
//========================================================================================================================================\\
//========================================================================================================================================\\

//session_start();

//if (!isset($user))
//	{
//	 if(!isset($_SESSION['user']))
//		session_register("user");
//	}

//if(!isset($_SESSION['user']))
//	echo "error";
//else
//{
  //session_register("wpagook");
  //session_register("wprestamo");

  

			      or die("No se ralizo Conexion");
  


  //$conexunix = odbc_pconnect('facturacion','infadm','1201')
  //					    or die("No se ralizo Conexion con el Unix");


  //$pos = strpos($user,"-");
  //$wusuario = substr($user,$pos+1,strlen($user));

  ///$wactualiz="(Versión Octubre 25 de 2006)";                 // Aca se coloca la ultima fecha de actualizacion de este programa \\
	                                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= \\

  $wfecha=date("Y-m-d");
  $hora = (string)date("H:i:s");

  echo "<form name='Arreglar_tabla_000065' action='Arreglar_tabla_000065.php' method=post>";

  //echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";

	//---------------------------------------------------------------------------------------------
	// --> 	Consultar si esta en funcionamiento la nueva facturacion
	//		Fecha cambio: 2013-12-23	Autor: Jerson trujillo.
	//---------------------------------------------------------------------------------------------
	$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
	//---------------------------------------------------------------------------------------------
	// --> 	MAESTRO DE CONCEPTOS:
	//		- Antigua facturacion 	--> 000004
	//		- Nueva facturacion 	--> 000200
	//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
	//		de conceptos cambiara por la tabla 000200.
	//		Fecha cambio: 2013-12-23	Autor: Jerson trujillo.
	//----------------------------------------------------------------------------------------------
	$tablaConceptos = 'clisur'.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
	//----------------------------------------------------------------------------------------------

  //===========================================================================================================================================
  //INICIO DEL PROGRAMA
  //===========================================================================================================================================

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

  echo "<p align=right><font size=1><b>Version: ".$wactualiz." &nbsp&nbsp&nbsp Autor: ".$wautor."</b></font></p>";
  //===========================================================================================================================================
  //ACA COMIENZA EL ENCABEZADO DE LA VENTA
  echo "<center><table border>";
  //echo "<tr><td align=center rowspan=2 colspan=6><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=300 HEIGHT=100></td></tr>";
  echo "<tr><td align=center colspan=13 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>A R R E G L O&nbsp&nbsp D E &nbsp&nbsp F A C T U R A S</b></font></td></tr>";
  echo "</table>";





 $q = " SELECT fdefue, fdedoc "
     ."   FROM clisur_000065, ".$tablaConceptos
     ."  WHERE fdefue in ('19','20') "
     ."    AND fdeest = 'on' "
     ."    AND fdecon = grucod "
     ."    AND gruabo = 'on' ";
 $res = mysql_query($q,$conex);
 $num = mysql_num_rows($res);


 if ($num > 0)
    {
	 for ($i=1;$i<=$num;$i++)
        {
	     $row = mysql_fetch_array($res);

	     $q = " SELECT fdefue, fdedoc, fdecon, fdevco, fdesal "
		     ."   FROM clisur_000065, ".$tablaConceptos
		     ."  WHERE fdefue = '".$row[0]."'"
		     ."    AND fdedoc = '".$row[1]."'"
		     ."    AND fdeest = 'on' "
		     ."    AND fdecon = grucod "
		     ."    AND gruabo = 'off' ";
		 $res2 = mysql_query($q,$conex);
		 $num2 = mysql_num_rows($res2);

		 for ($j=1;$j<=$num2;$j++)
	        {
		     $row2 = mysql_fetch_array($res2);

		     $q = " SELECT fdecon, count(*) "
		         ."   FROM clisur_000065, ".$tablaConceptos
		         ."  WHERE fdefue = '".$row2[0]."'"
		         ."    AND fdedoc = '".$row2[1]."'"
		         ."    AND fdecon = grucod "
		         ."    AND gruabo = 'off' "
		         ."  GROUP BY 1 ";
		     $res1 = mysql_query($q,$conex);
	         $row1 = mysql_fetch_array($res1);

	         if ($row1[0] > 1)
	            {
		         //Valor total conceptos
		         $q = " SELECT sum(fdevco) "
			         ."   FROM clisur_000065, ".$tablaConceptos
			         ."  WHERE fdefue = '".$row2[0]."'"
			         ."    AND fdedoc = '".$row2[1]."'"
			         ."    AND fdecon = grucod "
			         ."    AND gruabo = 'off' ";
			     $res1 = mysql_query($q,$conex);
		         $row1 = mysql_fetch_array($res1);

		         $wtotal=$row1[0];        //*****


		         //Valor abonos
		         $q = " SELECT sum(fdevco) "
			         ."   FROM clisur_000065, ".$tablaConceptos
			         ."  WHERE fdefue = '".$row2[0]."'"
			         ."    AND fdedoc = '".$row2[1]."'"
			         ."    AND fdecon = grucod "
			         ."    AND gruabo = 'on' ";
			     $res1 = mysql_query($q,$conex);
		         $row1 = mysql_fetch_array($res1);

		         $wabonos=$row1[0];      //*****

		         $wvalor_con = $row2[3];  //*****

		         $wsaldo = $wvalor_con-(round(($wvalor_con/$wtotal)*abs($wabonos)));

		         $q = "UPDATE clisur_000065, ".$tablaConceptos
		             ."   SET fdesal = ".$wsaldo
		             ."  WHERE fdefue = '".$row2[0]."'"
			         ."    AND fdedoc = '".$row2[1]."'"
			         ."    AND fdecon = '".$row2[2]."'"
			         ."    AND fdecon = grucod "
			         ."    AND gruabo = 'off' ";
			     $res1 = mysql_query($q,$conex);

			     echo $q."<br>";

	            }
	        }
        }
        ECHO "<TR><TD>TERMINO EL ARREGLO<TD><TR>";
	}
  echo "<tr><td align=center bgcolor=#cccccc colspan=13><input type='submit' value='OK'></td></tr>";
  echo "</table>";

//}
?>
