<html>
<head>
  	<title>MATRIX Consulta a la Bitacora de Pacientes</title>
  	<!-- UTF-8 is the recommended encoding for your pages -->

    <!-- <title>Zapatec DHTML Calendar</title>

<!-- Loading Theme file(s) -->
   <!--  <link rel="stylesheet" href="../../zpcal/themes/winter.css" />-->

<!-- Loading Calendar JavaScript files -->
   <!--  <script type="text/javascript" src="../../zpcal/src/utils.js"></script>-->
   <!--  <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>-->
   <!--  <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>-->
    <!-- Loading language definition file -->
   <!--<script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>-->
    <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo4{color:#000066;background:#dddddd;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;}
    	.tipo6{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo7{color:#FFFFFF;background:#000066;font-size:12pt;font-family:Tahoma;font-weight:bold;width:30em;}
    	#tipo8{color:#99CCFF;background:#000066;font-size:6pt;font-family:Tahoma;font-weight:bold;}
    	#tipo9{color:#660000;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo10{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo11{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo12{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo13{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo14{color:#FFFFFF;background:#000066;font-size:14pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo15{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo17{color:#000066;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo18{color:#000066;background:#FFCC66;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo19{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}

    	#tipoG00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#FFFFFF;background:#FFFFFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG54{color:#000066;background:#DDDDDD;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG11{color:#FFFFFF;background:#99CCFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG21{color:#FFFFFF;background:#CC3333;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG32{color:#FF0000;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG33{color:#006600;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG34{color:#000066;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG42{color:#FF0000;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG41{color:#FFFFFF;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG44{color:#000066;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}

    	#tipoM00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoM01{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:3em;}
    	#tipoM02{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:3em;}

    </style>
</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.rBitacora.submit();
	}
	function teclado()
	{
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
	function teclado1()
	{
		if ((event.keyCode < 48 || event.keyCode > 57 ) & event.keyCode != 46 & event.keyCode != 13)  event.returnValue = false;
	}
	function teclado2()
	{
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13) event.returnValue = false;
	}
	function teclado3()
	{
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13 & event.keyCode != 45) event.returnValue = false;
	}
	function cerrarVentana()
	 {
      window.close()
     }

//-->
</script>



<?php
include_once("conex.php");

include_once("root/comun.php");

	$wactualiz = '2014-03-17';
/**********************************************************************************************************************
	   PROGRAMA : rbitacora.php
	   Fecha de Liberación : 2007-11-21
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2009-02-10

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite generar un informe de datos
	   de pacientes almacenados en la bitacora para todos los ingresos que haya tenido en la clinica.


	   REGISTRO DE MODIFICACIONES :

	    .2019-05-27
			Se comentan ECHOS de queries
		
	    .2014-03-17
			Se modifica para que se pueda consultar por tema y para todos los temas solo se puede hacer para una historia
			especifica. Tambiém se modifica que al editar un registro se muestre solo el tema seleccionado inicialmente.
			-Juan C. Hdez

	    .2013-02-04
			Cada textarea tendra el tamano justo para el texto.
			Se agrega la funcion encabezadotabla.
			La fila con los titulos "Numero	Fecha	Hora	Usuario	Tema	Observacion" no se repetira por cada registro
			-Frederick Aguirre

	   .2007-11-21
	   		Release de Versión Beta.

	   .2009-02-10
	   		La variable whis no se estaba refrescando por HIDDEN en la parte de detalle del programa.

***********************************************************************************************************************/
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='rBitacora' action='rBitacora.php' method=post>";
	

	include_once("root/comun.php");
	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'codemp' value='".$codemp."'>";

	//==============================================================================================
	//para cambiar el fondo de la fila aleatoriamente y asi poder saber cuando responde la consulta,
	//para cuando no hay registros en dos o mas consultas que se hagan seguidas
	//==============================================================================================
	$wfilas[0]= "fila1";
	$wfilas[1]= "fila2";
	$wfilas[2]= "fondoGris";

	$get= count($wfilas)-1;
	$wfila= rand(0,$get);
	//==============================================================================================

	if($ok == 99)
	{
		echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
		echo "<table border=0 align=center id=tipo5>";
		?>
		<script>
			function ira(){document.rBitacora.whis.focus();}
		</script>
		<?php
		$wemp_pmla = $codemp;
		encabezado("CONSULTA A LA BITACORA DE PACIENTES", $wactualiz, "clinica");

		echo "</table>";
		echo "<table align=center border=0>";

		//TEMA A CONSULTAR  //Mar 10 2014 - Juan C.
		echo "<tr><td align=center colspan=6 bgcolor=".$color."><b>Tema a Consultar: </b>";
		$q = "SELECT Codigo, Descripcion "
			."	FROM ".$empresa."_000034 "
			." WHERE Estado='on' ";
		$err = mysql_query($q,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<select name='wtema' id=tipo1>";
		if ($num>0)
		{
			echo "<option>TODOS</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wtema=ver($wtema);
				if($wtema == $row[0])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select></td></tr>";
		echo "</table>";
		echo "<table align=center border=0>";

		$num=0;

		//Restar 1 mes
		//$wfechaAnt = date('Y-m-d', strtotime('-1 month')) ; // resta 1 mes

		//Restar 1 semana
		$wfechaAnt = date('Y-m-d', strtotime('-1 week')) ;

		echo "<tr class='seccion1'>";
			echo "<td align=center><b>FECHA INICIAL: </b>";

			if(isset($wfec_i) && isset($wfec_f))
			  campoFechaDefecto("wfec_i", $wfec_i);
			 else
			  campoFechaDefecto("wfec_i", $wfechaAnt);
			 echo "</td>";

			 echo "<td align=center><b>FECHA FINAL: </b>";
			 if (isset($wfec_i) && isset($wfec_f))
			   campoFechaDefecto("wfec_f", $wfec_f);
			  else
			   campoFechaDefecto("wfec_f", date("Y-m-d"));
			echo "</td>";
		echo "</tr>";


		echo "<tr><td bgcolor=#cccccc align=center>Historia</td>";
		if(!isset($whis))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=10 maxlength=15></td></tr>";
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=10 maxlength=15 value=".$whis."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Cedula</td>";
		if(!isset($wced))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wced' size=12 maxlength=20></td></tr>";
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wced' size=12 maxlength=20 value=".$wced."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Primer Nombre</td>";
		if(!isset($wno1))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wno1' size=20 maxlength=30></td></tr>";
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wno1' size=20 maxlength=30 value=".$wno1."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Segundo Nombre</td>";
		if(!isset($wno2))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wno2' size=20 maxlength=30></td></tr>";
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wno2' size=20 maxlength=30 value=".$wno2."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Primer Apellido</td>";
		if(!isset($wap1))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wap1' size=20 maxlength=30></td></tr>";
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wap1' size=20 maxlength=30 value=".$wap1."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Segundo Apellido</td>";
		if(!isset($wap2))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wap2' size=20 maxlength=30></td></tr>";
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wap2' size=20 maxlength=30 value=".$wap2."></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='CONSULTAR'></td></tr></table><br><br>";

		echo "<br><br>";
		echo "<table align=center>";
		echo "<tr><td align=center colspan=4><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
		echo "</table>";
		echo "<br><br>";

		//2014-03-17
		if (  (isset($wtema) and $wtema != "TODOS")
		   or (isset($whis)  and trim($whis) != "") or (isset($wced) and trim($wced) != "")
		   or (isset($wno1)  and trim($wno1) != "") or (isset($wno2) and trim($wno2) != "")
		   or (isset($wap1)  and trim($wap1) != "") or (isset($wap2) and trim($wap2) != ""))
		   {
			//    $inicio = microtime(true);

		   	// 	$q  = "CREATE TEMPORARY TABLE temp_root_000036
			// 			SELECT Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac
			// 			  FROM root_000036 A
			// 			 WHERE pacced != ''";
			// if(isset($wno1) and $wno1 != "")
			// 	$q .= "   AND Pacno1 like '%".$wno1."%' ";
			// if(isset($wno2) and $wno2 != "")
			// 	$q .= "   AND Pacno2 like '%".$wno2."%' ";
			// if(isset($wap1) and $wap1 != "")
			// 	$q .= "   AND Pacap1 like '%".$wap1."%' ";
			// if(isset($wap2) and $wap2 != "")
			// 	$q .= "   AND Pacap2 like '%".$wap2."%' ";

			// $err = mysql_query($q,$conex) or die(mysql_errno().":".mysql_error());

			$q  = "SELECT Orihis, Biting, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac ";
			$q .= "  FROM root_000037, root_000036,".$empresa."_000021 A";
			$q .= " WHERE Oriced != ''  ";
			$q .= "   AND Oriori = '".$wemp_pmla."' ";
			$q .= "   AND Oriced = Pacced ";
			if(isset($wtema) and $wtema != "TODOS")
				$q .= "   and Bittem = '".$wtema."' ";
			if(isset($whis) and $whis != "")
				$q .= "   AND Orihis = '".$whis."' ";
			if(isset($wced) and $wced != "")
				$q .= "   AND Oriced like '".$wced."' ";
				$q .= "   AND Orihis = Bithis ";
			if(isset($wno1) and $wno1 != "")
				$q .= "   AND Pacno1 like '%".$wno1."%' ";
			if(isset($wno2) and $wno2 != "")
				$q .= "   AND Pacno2 like '%".$wno2."%' ";
			if(isset($wap1) and $wap1 != "")
				$q .= "   AND Pacap1 like '%".$wap1."%' ";
			if(isset($wap2) and $wap2 != "")
				$q .= "   AND Pacap2 like '%".$wap2."%' ";
			$q .= "   AND A.fecha_data BETWEEN '".$wfec_i."' AND '".$wfec_f."'";
			$q .= " GROUP BY Orihis, Biting ";
			$q .= " ORDER BY Orihis, Biting ";

			$err = mysql_query($q,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);

			// $fin = microtime(true);
			// $diferencia = $fin - $inicio;
			// echo "Diferencia: " . $diferencia;

		   }

		if ($num>0)
			{
				echo "<table border=0 align=center id=tipo5>";
				echo "<tr class='encabezadotabla'><td align=center colspan=9>RESULTADO DE LA CONSULTA (Cantidad: ".$num.")</td></tr>";
				echo "<tr><td bgcolor='#999999' align=center>HISTORIA</td><td bgcolor='#999999' align=center>NRO. INGRESO</td><td bgcolor='#999999' align=center>IDENTIFICACION</td><td bgcolor='#999999' align=center>TIPO<BR>IDENTIFICACION</td><td bgcolor='#999999' align=center>NOMBRE</td><td bgcolor='#999999' align=center>FECHA<br>NACIMIENTO</td><td bgcolor='#999999' align=center>SELECCION</td></tr>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($i % 2 == 0)
						$tipo="tipo12";
					else
						$tipo="tipo13";
					$nombre=$row[4]." ".$row[5]." ".$row[6]." ".$row[7];
					$path="/matrix/movhos/procesos/rBitacora.php?ok=0&empresa=".$empresa."&codemp=".$codemp."&whis=".$row[0]."&whis1=".$whis."&wnin=".$row[1]."&wced=".$wced."&wno1=".$wno1."&wno2=".$wno2."&wap1=".$wap1."&wap2=".$wap2."&wtema=".$wtema."&wfec_i=".$wfec_i."&wfec_f=".$wfec_f;
					echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$row[2]."</td><td id=".$tipo.">".$row[3]."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[8]."</td><td id=".$tipo."><A HREF='".$path."'>Editar</A></td></tr>";
				}
			}
		   else
              {
			    if (isset($wtema) and $wtema == "TODOS" and
				   ((!isset($whis) or trim($whis) == "") and
				    (!isset($wced) or trim($wced) == "") and
					(!isset($wno1) or trim($wno1) == "") and
					(!isset($wno2) or trim($wno2) == "") and
					(!isset($wap1) or trim($wap1) == "") and
					(!isset($wap2) or trim($wap2) == "")))
					{
					 echo "<br>";
				     echo "<table align=center>";
					 echo "<tr class=".$wfilas[$wfila].">";
					 echo "<td>Solo se pueden consultar todos los temas para un paciente o historia especifica</td>";
					 echo "</tr>";
					 echo "<br><br>";
          			}
                  else
				    {
					 echo "<br>";
				     echo "<table align=center>";
					 echo "<tr class=".$wfilas[$wfila].">";
					 echo "<td>No existen registros para los datos consultados en el rango fechas</td>";
					 echo "</tr>";
          			}
              }
		echo "</table></center>";
	}
	else
	{
		echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
		echo "<input type='HIDDEN' name= 'wtema' value='".$wtema."'>";
		echo "<input type='HIDDEN' name= 'wfila' value='".$wfila."'>";
		echo "<input type='HIDDEN' name= 'whis' value='".$whis."'>";
		echo "<input type='HIDDEN' name= 'wnin' value='".$wnin."'>";
		echo "<input type='HIDDEN' name= 'wced' value='".$wced."'>";
		echo "<input type='HIDDEN' name= 'wno1' value='".$wno1."'>";
		echo "<input type='HIDDEN' name= 'wno2' value='".$wno2."'>";
		echo "<input type='HIDDEN' name= 'wap1' value='".$wap1."'>";
		echo "<input type='HIDDEN' name= 'wap2' value='".$wap2."'>";
		echo "<input type='HIDDEN' name= 'wfec_i' value='".$wfec_i."'>";
		echo "<input type='HIDDEN' name= 'wfec_f' value='".$wfec_f."'>";

		encabezado("CONSULTA A LA BITACORA DE PACIENTES", $wactualiz, "clinica");

		echo "<table border=0 align=center id=tipo2>";

		//********************************************************************************************************
		//*                                         DATOS DEL PACIENTE                                           *
		//********************************************************************************************************
		//                  0       1       2       3       4       5       6       7       8       9      10      11
		$query = "select Inghis, Inging, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre ";
		$query .= " from ".$empresa."_000016,root_000037,root_000036 ";
		$query .= " where Inghis = '".$whis."' ";
		$query .= "   and Inging = '".$wnin."' ";
		$query .= "   and Inghis = orihis  ";
		$query .= "   and Inging = oriing  ";
		$query .= "   and oriori = '".$codemp."'  ";
		$query .= "   and Oriced = Pacced ";

		// .2019-05-27
		// echo ' query1 '.$query;

		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);

		//echo "<tr><td align=center colspan=6 id=tipo14><b>CONSULTA A LA BITACORA DE PACIENTES</td></tr>";
		$color="#dddddd";
		$color1="#000099";
		$color2="#006600";
		$color3="#cc0000";
		$color4="#CC99FF";
		$color5="#99CCFF";
		$color6="#FF9966";
		$color7="#cccccc";
		?>
		<script>
			function ira(){document.rBitacora.wtema.focus();}
		</script>
		<?php
		echo "<tr class='encabezadotabla'><td align=center colspan=6><b>DATOS DEL PACIENTE</b></td></tr>";

		//PRIMERA LINEA
		echo "<tr>";
		echo "<td bgcolor=".$color." align=center>Historia :<br>".$whis."</td>";
		echo "<td bgcolor=".$color." align=center>Nro Ing. :<br>".$wnin."</td>";
		echo "<td bgcolor=".$color." align=center>Tipo doc. :<br>".$row[3]."</td>";
		echo "<td bgcolor=".$color." align=center>Identificacion. :<br>".$row[2]."</td>";
		$nombre=$row[4]." ".$row[5]." ".$row[6]." ".$row[7];
		echo "<td bgcolor=".$color." align=center>Nombre :<br>".$nombre."</td>";
		echo "</tr>";

		//SEGUNDA LINEA
		echo "<tr>";
		echo "<td bgcolor=".$color." align=center>F. Nacimiento :<br>".$row[8]."</td>";
		echo "<td bgcolor=".$color." align=center>Sexo :<br>".$row[9]."</td>";
		echo "<td bgcolor=".$color." align=center>Responsable :<br>".$row[10]."</td>";
		echo "<td bgcolor=".$color." align=center colspan=2>Descripcion :<br>".$row[11]."</td>";
		echo "</tr>";

		//PARTE CENTRAL DE LA PANTALLA
		//"/matrix/movhos/procesos/rBitacora.php?ok=0&empresa=".$empresa."&codemp=".$codemp."&wtema=".$wtema;
		echo "<tr><td bgcolor=#999999 colspan=6 align=center><input type='submit' value='OK'></td></tr>";
		echo "<tr><td bgcolor=#ffffff colspan=6 align=center><A HREF='/matrix/movhos/procesos/rBitacora.php?ok=99&empresa=".$empresa."&codemp=".$codemp."&wtema=".$wtema."&wfec_i=".$wfec_i."&wfec_f=".$wfec_f."&whis=".$whis."&wced=".$wced."&wno1=".$wno1."&wno2=".$wno2."&wap1=".$wap1."&wap2=".$wap2."'><IMG SRC='/matrix/images/medical/movhos/pac.png' alt='Lista'><br>Retornar a la Lista</A></td></tr></table><br><br></center>";


		//********************************************************************************************************
		//*                             DATOS ASOCIADOS A LA BITACORA DEL PACIENTE                               *
		//********************************************************************************************************
		echo "<table border=0 align=center id=tipo2>";
		echo "<tr><td colspan=6 id=tipo19><b>Tema : ";
		$query = "SELECT Codigo, Descripcion  from  ".$empresa."_000034 where Estado='on' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<select name='wtema' id=tipo1 OnChange='enter()'>";
		if ($num>0)
		{
			echo "<option>TODOS</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wtema=ver($wtema);
				if($wtema == $row[0])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select>";
		echo"</td></tr>";

		$query = "select Bitnum, Bitusr, usuarios.Descripcion, Bittem, ".$empresa."_000034.Descripcion, Bitobs, ".$empresa."_000021.fecha_data, ".$empresa."_000021.Hora_data  ";
		$query .= " from ".$empresa."_000021,usuarios,".$empresa."_000034 ";
		$query .= " where Bithis = '".$whis."' ";
		$query .= "   and Biting = '".$wnin."' ";
		$query .= "   and Bitusr = usuarios.Codigo  ";
		if(isset($wtema) and $wtema != "TODOS")            //2014-03-17
			$query .= "   and Bittem = '".$wtema."' ";
		$query .= "   and Bittem = ".$empresa."_000034.Codigo  ";
		$query .= " order by Bitnum desc";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);

		//.2019-05-27
		// echo ' query2 '.$query;		

		if ($num>0)
		{
			echo "<tr class='encabezadotabla'>";
			echo "<td align='center'>Registro</td>";
			echo "<td align='center'>Fecha</td>";
			echo "<td align='center'>Hora</td>";
			echo "<td align='center'>Usuario</td>";
			echo "<td align='center'>Tema</td>";
			echo "<td align='center'>Observacion</td>";
			echo "</tr>";

			$cols = 80;//Numero de caracteres en horizontal para el textarea

			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
				{
					$tipo="tipo12";
				}
				else
				{
					$tipo="tipo13";
				}
				$row = mysql_fetch_array($err);

				//reemplaza todos los saltos de linea por <br>
				$aux = preg_match("/[\n|\r|\n\r]/", '<br>', $row['Bitobs']);
				$cont = substr_count($aux, '<br><br>'); //cuentas cuantos saltos de linea tiene
				$rows = 0;
				if( $cont == 0){ //Si no tiene saltos de linea, Determina si se debe crear una nueva columna si el texto es muy largo (mas del 90% del texto horizontal)
					$rows = ceil(strlen(implode($row['Bitobs'])) / ($cols*0.9))+1;
				}else{
					$rowsaux = 0;
					$lineas = explode( '<br><br>' , $aux ); //Crea un arreglo donde en cada posicion hay una cadena de texto por cada salto de linea
					foreach( $lineas as $linea ){
						$conta = ceil(strlen($linea) / ($cols*0.9));	//Determina si se debe crear una nueva columna si el texto es muy largo(mas del 90% del texto horizontal)
						if( $conta > 1 ){
							$rowsaux+=$conta;
						}
					}
					$rows = $cont + $rowsaux; //El numero de filas es: numero de saltos de linea + filas extras por texto muy largo
				}
				//el minimo de filas es 3
				if( $rows < 3 ){
					$rows = 3;
				}else{
					$rows++;
				}
				echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[6]."</td><td id=".$tipo.">".$row[7]."</td><td id=".$tipo.">".$row[2]."</td><td id=".$tipo.">".$row[4]."</td><td id=".$tipo."><textarea name='wobs[".$i."]' cols=80 readonly='readonly' rows=".$rows." class=tipo3>".$row[5]."</textarea></td></tr>";
			}
			echo "</table>";
		}
		else
		   {
		    echo "</table>";
			if (isset($wtema))
			   {
			    echo "<br><br><br>";
			    echo "<table align=center><tr class=".$wfilas[$wfila]."><td>No existen registros para el paciente del tema seleccionado</tr></table>";
			   }
		   }
		echo "</table>";
	}

	echo "<br><br>";
	echo "<table align=center>";
	echo "<tr><td align=center colspan=4><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
	echo "</table>";
	echo "<br><br>";

	echo"</form>";

}
?>
</body>
</html>
