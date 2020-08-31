<html>
<head>
<title>MATRIX</title>
<script type="text/javascript">

function retornar(wemp_pmla,wfecha_i,wfecha_f,bandera,empresa)
	{
		location.href = "rep_ingxempresa.php?wemp_pmla="+wemp_pmla+"&wfecha_i="+wfecha_i+"&wfecha_f="+wfecha_f+"&bandera="+bandera+"&empresa="+empresa;


    }

function cerrar_ventana(cant_inic)
	{
		window.close();
    }

</script>



</head>
<body BGCOLOR="">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 {
	$key = substr($user,2,strlen($user));


	include_once("root/comun.php");


	echo "<form action='rep_ingxempresa.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
	$titulo = "Reporte de Ingresos por Nueva Eps";
	$wactualiz = "2012-01-27";
	encabezado($titulo,$wactualiz, "clinica");

	//consultamos la base de datos de la empresa correspondiente
	 $q = " SELECT detapl, detval "
        ."    FROM root_000050, root_000051 "
        ."   WHERE empcod = '".$wemp_pmla."'"
        ."     AND empest = 'on' "
        ."     AND empcod = detemp ";


     $res = mysql_query($q,$conex) or die ("Error1: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
     $num = mysql_num_rows($res);

     if($num > 0 )
        {
	     for($i=1;$i<=$num;$i++)
	        {
	         $row = mysql_fetch_array($res);

	         if($row[0] == "cenmez")
	            $wcenmez=$row[1];

	         if($row[0] == "afinidad")
	            $wafinidad=$row[1];

	         if($row[0] == "movhos")
	            $wbasedato=strtolower($row[1]);

			 if(strtoupper($row[0]) == "HCE")
	            $whce=$row[1];

	         if($row[0] == "tabcco")
	            $wtabcco=$row[1];

			 if($row[0] == "camilleros")
	            $wcencam=$row[1];

			if($row[0] == "facturacion")
	            $wclisur=$row[1];
            }
        }
        else
		    {
             echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";

	        }

	if(!isset($wfecha_i) or !isset($wfecha_f) or isset($bandera))
	{
		if(!isset($wfecha_i ) && !isset($wfecha_i ))
		{
			$wfecha_i = date("Y-m-d");
			$wfecha_f = date("Y-m-d");
		}

		echo "<center><table border=0>";
		echo  "<tr><td class=fila1 align=center><b>Fecha Inicial: </b> ";
		campoFechaDefecto("wfecha_i", $wfecha_i);
		echo "</td>";
		echo  "<td class=fila1 align=center><b>Fecha Final: </b> ";
		campoFechaDefecto("wfecha_f", $wfecha_f);
		echo "</td>";
		echo "</tr>";
		echo "<td class=fila1 colspan=2> <b>Empresa:</b>";
		echo "<select name='empresa' id='empresa'>";


		$q="SELECT Empnit,Empnom
			  FROM ".$wclisur."_000024
			 WHERE empcod = empres
			 GROUP BY Empnit
			 ORDER BY Empnom ";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		echo "<option value ='todos'>todos</option>";
		for($i = 1; $i <= $num; $i++)
	    {
         $row = mysql_fetch_array($res);

		 if(isset($empresa) && $row[0]==$empresa)
			echo "<option selected value='" . $row[0] . " - " . $row[1] . "'>" . $row[0] . " - " . $row[1] . "</option>";
		 else
			echo "<option value='" . $row[0] . " - " . $row[1] . "'>" . $row[0] . " - " . $row[1] . "</option>";
        }

		echo "</select>";

		echo "</td>";

		echo"</tr>";
		echo "<tr><td colspan=4	 align='center' ><br/><input type='submit' align='left' value='ENTER'>&nbsp; &nbsp;<input type='button' align='right' name='btn_cerrar' value='CERRAR' onclick='cerrar_ventana()'/></td></tr></table>";
	}
	else
	{
		$empresa1 = explode("-",$empresa);


		$q = " SELECT CS101.INGHIS, CS101.INGNIN, CS101.INGFEI, CS101.INGHIN, CS100.PACDOC, CS100.PACNO1,
					  CS100.PACNO2, CS100.PACAP1, CS100.PACAP2, CS100.PACACT, CS105.Seldes, CS24.Empnit,CS24.Empnom
				 FROM ".$wclisur."_000101 CS101, ".$wclisur."_000100 CS100, ".$wclisur."_000105 CS105, ".$wclisur."_000024 CS24
			    WHERE  CS101.Ingfei BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'
				  AND CS100.Pachis = CS101.Inghis";
		if(	$empresa1[0]!="todos")
			$q.=" AND CS101.Ingcem = '".$empresa1[0]."' ";
		$q.="     AND CS101.Ingcem = CS24.Empnit
				  AND CS105.Seltip = '01'
				  AND CS105.Selcod = CS100.PACTDO
				ORDER BY CS101.Ingcem ";

		$bandera = 1;

	echo "<center><input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecha_i."\",\"".$wfecha_f."\",\"".$bandera."\",\"".$empresa1[0]."\")'/>&nbsp; &nbsp; <input type='button' align='right' name='btn_cerrar2' value='Cerrar' onclick='cerrar_ventana()'/></center>";
		echo "<br/>";

		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			echo "<center>";
			echo "<table border=0>";
			echo "<tr class=fila2>";
			echo "<td align=center ><b>Empresa </b> </td>";
			echo "<td   align='left'>".$empresa."</td>";
			echo "</tr>";
			echo "<tr class=fila2>";
			echo "<td align=center ><b>Fecha Inicial </b> </td>";
			echo "<td   align='left'>".$wfecha_i."</td>";
			echo "</tr>";
			echo "<tr class=fila2	>";
			echo "<td align=center ><b> Fecha Final </b> </td>";
			echo "<td  align='left'>".$wfecha_f."</td>";
			echo "</tr>";
			echo "</table>";
			echo "</center>";
			echo "<br/>";

			$row = mysql_fetch_array($err);
			$wemp = $row["Empnit"];
			echo "<table border=0>";
			echo "<tr class=titulo><td colspan=8>";
			echo $row["Empnit"]." - ".$row["Empnom"];
			echo "</td></tr>";
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center >Historia</td>";
			echo "<td align=center > Ingreso</td>";
			echo "<td align=center>Fecha<br>Ingreso</td>";
			echo "<td align=center>Hora<br>Ingreso</td>";
			echo "<td align=center>Tipo<br>Documento</td>";
			echo "<td align=center>Número<br>Documento</td>";
			echo "<td align=center>Nombres del Paciente</td>";
			echo "<td align=center>Paciente<br>Activo</td>";
			echo "</tr>";

			$i=0;
			$wtotal = 0;
			$wgtotal = 0;
			while ($i<$num)
			{
				if($wemp != $row["Empnit"] )
				{
					echo "<tr class='encabezadoTabla'><td colspan='8' align=right> Total Servicios : ".$wtotal."</td></tr>";
					echo "<tr><td colspan=8>&nbsp;</td></tr>";
					echo "<tr class=titulo><td colspan=8>";
					echo $row["Empnit"]." - ".$row["Empnom"];
					echo "</td></tr>";
					echo "<tr class=encabezadoTabla>";
					echo "<td align=center >Historia</td>";
					echo "<td align=center > Ingreso</td>";
					echo "<td align=center>Fecha<br>Ingreso</td>";
					echo "<td align=center>Hora<br>Ingreso</td>";
					echo "<td align=center>Tipo<br>Documento</td>";
					echo "<td align=center>Número<br>Documento</td>";
					echo "<td align=center>Nombres del Paciente</td>";
					echo "<td align=center>Paciente<br>Activo</td>";
					echo "</tr>";

					$wgtotal+=$wtotal;
					$wtotal = 0;
					$wemp= $row["Empnit"];

				}


				if($i % 2 == 0)
					$wclass="fila1";
				else
					$wclass="fila2";
				echo "<tr>";
				echo "<td class=".$wclass." align=center>".$row["INGHIS"]."</td>";
				echo "<td class=".$wclass." align=center>".$row["INGNIN"]."</td>";
				echo "<td class=".$wclass." align=center>".$row["INGFEI"]."</td>";
				echo "<td class=".$wclass." align=center>".$row["INGHIN"]."</td>";
				echo "<td class=".$wclass." align=center>".$row["Seldes"]."</td>";
				echo "<td class=".$wclass." align=center>".$row["PACDOC"]."</td>";
				echo "<td class=".$wclass.">".$row["PACNO1"]." ".$row["PACNO2"]." ".$row["PACAP1"]." ".$row["PACAP2"]."</td>";
				echo "<td class=".$wclass." align=center>".$row["PACACT"]."</td>";
				echo "</tr>";

				$row = mysql_fetch_array($err);
			    $wtotal++;
				$i++;
			}
			echo "<tr class='encabezadoTabla'><td colspan='8' align=right> Total Servicios : ".$wtotal."</td></tr>";
			echo "<tr><td colspan=11>&nbsp;</td></tr>";
			if($wgtotal == 0)
				$wgtotal=$wtotal;
			echo "<tr class='encabezadoTabla'><td colspan='8' align=right>Gran Total de Servicios : ".$wgtotal." </td> </tr>";
			echo "</table>";
			echo "<br/>";

			echo "<center><input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecha_i."\",\"".$wfecha_f."\",\"".$bandera."\",\"".$empresa1[0]."\")'/>&nbsp; &nbsp; <input type='button' align='right' name='btn_cerrar2' value='Cerrar' onclick='cerrar_ventana()'/></center>";
	    }
		else
		{
			echo "<center class='textoMedio'>NO SE ENCONTRARON REGISTROS</center>";
		}
	}
}
?>
</body>
</html>
