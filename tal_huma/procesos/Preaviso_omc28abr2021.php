<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066" onload=ira()>
    
<center>
<?php
include_once("conex.php");

//=======================================================
//AUTOR			:Pedro Ortiz Tamayo

//FECHA CREACION :2016-01-26
//FECHA ULTIMA ACTUALIZACION 	:2016-01-26

/* DESCRIPCION	:Actualiza las fecha de preaviso del los contratos con prorroga automatica */
                 

 session_start();
 if (!isset($_SESSION['user']))
    echo "error";
   else
	{ 
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='Preaviso.php' method=post>";
		$query = "SELECT Concon, Connum, Contip, Conpa1, Conpa2, Connit, Conobj, Convac, Confin, Conffi, Confpr, Conres, Conpro, Conter,id   "
			   ."    FROM talento_000002 "
			   ."    WHERE Conter = 'off' "
			   ."      AND Conpro = 'on' "
			   ."      AND Confpr <= '".date("Y-m-d")."' ";
		

		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);

		echo "<table border=1 align=center>";
		echo "<tr><td colspan=14 align=center bgcolor=#DBDFF8><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=14 align=center bgcolor=#DBDFF8><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=14 align=center bgcolor=#DBDFF8><b>ACTUALIZACION DE FECHAS DE PREAVISO</b></td></tr>";	

		echo "<tr>";
		echo "<td align=center bgcolor=#DBDFF8><b>#</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>CONTRATO</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>TIPO CONTRATO</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>PARTE 1</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>PARTE 2</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>NIT</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>OBJETO</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>VALOR</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>FECHA INICIAL</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>FECHA FINAL</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>FECHA PREAVISO</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>RESPONSABLE</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>PRORROGA</b></td>";
		echo "<td align=center bgcolor=#DBDFF8><b>FIN</b></td>";
		echo "</tr>";
		$s = 0;
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$row[10]=date("Y-m-d", strtotime("$row[10] + 1 year"));
				
				$fecha = date('Y-m-j');
				$nuevafecha = strtotime ( '+1 year' , strtotime ( $fecha ) ) ;
				$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
				
				$nuevafecha = $row[10];
				
				$query = "UPDATE talento_000002 set Confpr = '".$nuevafecha."' WHERE id = '".$row[14]."' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				echo "<tr>";
				echo "<td align=center>".$row[0]."</td>";
				echo "<td align=center>".$row[1]."</td>";
				echo "<td align=center>".$row[2]."</td>";
				echo "<td align=center>".$row[3]."</td>";
				echo "<td align=center>".$row[4]."</td>";
				echo "<td align=center>".$row[5]."</td>";
				echo "<td align=center>".$row[6]."</td>";
				echo "<td align=center>".$row[7]."</td>";
				echo "<td align=center>".$row[8]."</td>";
				echo "<td align=center>".$row[9]."</td>";
				echo "<td align=center>".$row[10]."</td>";
				echo "<td align=center>".$row[11]."</td>";
				echo "<td align=center>".$row[12]."</td>";
				echo "<td align=center>".$row[13]."</td>";
				echo "</tr>";
				$s++;    
			}
		}
		echo "<td colspan=12 align=center bgcolor=#DBDFF8><b>TOTAL CONTRATOS ACTUALIZADOS: </b></td>";
		echo "<td colspan=2 align=center bgcolor=#DBDFF8><b>".$s."</b></td>";
		echo "</table>"; 
	}
?>
</body>
</html>
