<html>
<head>
  	<title>Reporte de conceptos facturados por paciente</title>
</head>
<body  BGCOLOR="FFFFFF">
<font face='arial'>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
//==================================================================================================================================
//PROGRAMA						:Reporte de conceptos facturados por paciente
//AUTOR							:Juan David Londoño
//FECHA CREACION				:2007-05-04
//FECHA ULTIMA ACTUALIZACION 	:2007-05-04
$wactualiz="Diciembre 24 de 2013";
//==================================================================================================================================
//ACTUALIZACIONES
//==================================================================================================================================
//	-->	2013-12-24, Jerson trujillo.
//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//
//==================================================================================================================================
// xxxx
//==================================================================================================================================
include_once("root/comun.php");

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower($institucion->baseDeDatos);
	$wentidad = $institucion->nombre;

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

	echo "<form name=rep_Confac action='' method=post>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	if (!isset($wfec_f))
	   {
	   	$wfecha=date("Y-m-d");// esta es la fecha actual
        echo "<br><br><br>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><font size=5><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></font></td></tr>";
		echo "<tr><td><br></td></tr>";
		echo "<tr><td align=center colspan=2><font size=5>REPORTE DE CONCEPTOS FACTURADOS POR PACIENTE</font></td></tr>";
		echo "<tr><td td bgcolor=#dddddd align=center colspan=2><b>Empresa:</b> <select name='empresa'>";
		// query para traerme las empresas
        $query =  " SELECT DISTINCT Tcarres
                      FROM ".$wbasedato."_000106" .
                   " ORDER BY 1";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);
        echo "<option>*TODAS LAS EMPRESAS</option>";
	       for ($i=1;$i<=$num;$i++)
	        {
	            $row = mysql_fetch_array($err);
	            echo "<option>".$row[0]."</option>";
	        }
        echo "</select></td></tr>";
        echo "<tr><td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_i' value=".$wfecha." SIZE=10></td>";
	    echo "<td bgcolor=#dddddd align=center><b>Fecha Final (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_f' value=".$wfecha." SIZE=10></td>";
	    echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'>&nbsp;&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr></table>";
	   }
	else
	   {
	   		if ($empresa !='*TODAS LAS EMPRESAS')
	   		{
	   			$vble="AND Tcarres='".$empresa."'";
	   		}
	   		else
	   		{
	   			$vble=" ";
	   		}
	   			// este query me trae los datos que necesito que me muestre en pantalla como empresa, documento, nombre, codigo del concepto, nombre del concepto, fecha y telefono
		   		$query =  " SELECT Tcarres, Tcardoc, concat(Tcarno1,' ',Tcarno2,' ',Tcarap1,' ',Tcarap2), Tcarconcod, Tcarconnom, Tcarfec, Pactel, count(*), sum(Tcarvto)
			                  FROM ".$wbasedato."_000106, ".$wbasedato."_000100,  ".$tablaConceptos."
			              	 WHERE Tcarhis = Pachis
			              	   AND  Tcarconcod = Grucod
			              	   AND      Gruinv = 'off'
			              	   AND      Gruabo = 'off'
			              	   AND  Tcarfec between '".$wfec_i."' and '".$wfec_f."'
			              	 ".$vble."
			              	 GROUP BY 1,2,4
			              	 ORDER BY 1,2,4 ";
		        $err = mysql_query($query,$conex);
		        $num = mysql_num_rows($err);
		        //echo mysql_errno() ."=". mysql_error();
		        //echo $query;
		        echo "<center><table border=1>";
			    echo "<tr><td align=center ><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png'></td></tr>";
			    echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>REPORTE DE CONCEPTOS FACTURADOS POR PACIENTE</b></font><br><font size=1 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";
				echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>FECHA INICIAL: <i>".$wfec_i."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$wfec_f."</i></b></font></b></font></td></tr>";
				echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>EMPRESA: ".$empresa."</b></td></tr>";
				echo "</table>";
				echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
				echo "<br>";
				echo "<center><table border=0>";
				//$tot=0;
				//echo $num;
				for ($i=1;$i<=$num;$i++)
		         {
		            $row = mysql_fetch_array($err);
		            $arr[$i]['responsable']=$row[0];
		            $arr[$i]['documento']=$row[1];
		            $arr[$i]['nombre']=$row[2];
		            $arr[$i]['numcon']=$row[3];
		            $arr[$i]['nomcon']=$row[4];
		            $arr[$i]['feccon']=$row[5];
		            $arr[$i]['telpac']=$row[6];
		            $arr[$i]['cancon']=$row[7];
		            $arr[$i]['valcon']=$row[8];

		            if ($i ==1)
		            {
		            	echo "<tr bgcolor=#FFFFCC><td colspan=3><b>RESPONSABLE:</b> ".$arr[$i]['responsable']."</td></tr>";
		            	echo "<tr bgcolor=#cccccc><td><b>PACIENTE:</b> ".$arr[$i]['documento']."-".$arr[$i]['nombre']."</td><td><b>FECHA:</b> ".$arr[$i]['feccon']."</td><td><b>TELEFONO:</b> ".$arr[$i]['telpac']."</td></tr>";
		            	echo "<tr bgcolor=#CCFFFF><td>".$arr[$i]['numcon']."-".$arr[$i]['nomcon']."</td><td align=right>".$arr[$i]['cancon']."</td><td align=right>".number_format($arr[$i]['valcon'],0,'.',',')."</td></tr>";
		           }

		            else if ($arr[$i]['responsable'] != $arr[$i-1]['responsable'])
		            {
		            	echo "<tr bgcolor=#FFFFCC><td colspan=3>".$arr[$i]['responsable']."</td></tr>";
		            	echo "<tr bgcolor=#cccccc><td><b>PACIENTE:</b> ".$arr[$i]['documento']."-".$arr[$i]['nombre']."</td><td><b>FECHA:</b> ".$arr[$i]['feccon']."</td><td><b>TELEFONO:</b> ".$arr[$i]['telpac']."</td></tr>";
		            	echo "<tr bgcolor=#CCFFFF><td>".$arr[$i]['numcon']."-".$arr[$i]['nomcon']."</td><td align=right>".$arr[$i]['cancon']."</td><td align=right>".number_format($arr[$i]['valcon'],0,'.',',')."</td></tr>";

		            }
		            else if ($arr[$i]['documento'] != $arr[$i-1]['documento'])
	            	{
	            		echo "<tr bgcolor=#cccccc><td><b>PACIENTE:</b> ".$arr[$i]['documento']."-".$arr[$i]['nombre']."</td><td><b>FECHA:</b> ".$arr[$i]['feccon']."</td><td><b>TELEFONO:</b> ".$arr[$i]['telpac']."</td></tr>";
		            	echo "<tr bgcolor=#CCFFFF><td>".$arr[$i]['numcon']."-".$arr[$i]['nomcon']."</td><td align=right>".$arr[$i]['cancon']."</td><td align=right>".number_format($arr[$i]['valcon'],0,'.',',')."</td></tr>";

	            	}

	            	else
	            	{
	            		echo "<tr bgcolor=#CCFFFF><td>".$arr[$i]['numcon']."-".$arr[$i]['nomcon']."</td><td align=right>".$arr[$i]['cancon']."</td><td align=right>".number_format($arr[$i]['valcon'],0,'.',',')."</td></tr>";
	            	}

	         	 }
		         echo "</table>";
		         echo"<br>";
		         echo"<br>";

		        $query =  " SELECT Tcarconcod, Tcarconnom, count(*), sum(Tcarvto)
			                  FROM ".$wbasedato."_000106, ".$tablaConceptos."
			              	 WHERE  Tcarconcod = Grucod
			              	   AND      Gruinv = 'off'
			              	   AND      Gruabo = 'off'
			              	   AND  Tcarfec between '".$wfec_i."' and '".$wfec_f."'
			              	 ".$vble."
			              	 GROUP BY 1,2
			              	 ORDER BY 1,2 ";
		        $errt = mysql_query($query,$conex);
		        $numt = mysql_num_rows($errt);
		        //echo mysql_errno() ."=". mysql_error();

				echo "<center><table border=1>";
			    echo "<tr><td align=center colspan=4 bgcolor=#006699><font text color=#FFFFFF><b>CONSOLIDADO TOTAL DE:</b> ".$empresa."</font></td></tr>";
				echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>CODIGO DEL CONCEPTO</b></td><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>NOMBRE DEL CONCEPTO</b></td>";
				echo "<td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>CANTIDAD</b></td><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>VALOR TOTAL</b></td></tr>";
				//echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>FECHA INICIAL: <i>".$wfec_i."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$wfec_f."</i></b></font></b></font></td></tr>";
				$total=0;
				$totcon=0;
		        for ($i=1;$i<=$numt;$i++)
		         {
		         	 $row = mysql_fetch_array($errt);
		         	 echo "<tr bgcolor=CCFFFF><td align=center colspan=1>".$row[0]."</td>";
		         	 echo "<td align=left colspan=1>".$row[1]."</td>";
		         	 echo "<td align=center colspan=1>".$row[2]."</td>";
		         	 echo "<td align=right colspan=1>".number_format($row[3],0,'.',',')."</td></tr>";
		         	 $total=$total+$row[3];
		         	 $totcon=$totcon+$row[2];
		         }
		        echo "<tr><td align=left colspan=2 bgcolor=#006699><font text color=#FFFFFF><b>TOTAL</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>".$totcon."</b></font></td><td align=right bgcolor=#006699><font text color=#FFFFFF><b>".number_format($total,0,'.',',')."</b></font></td></tr>";
		        echo "</table>";
		        echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
		  }
}
liberarConexionBD($conex);
?>
</body>
</html>
