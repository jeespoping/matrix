<html>
<head>
  	<title>MATRIX Impresion de Honorarios SALAM</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Control' action='Honorarios.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($whon))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>IMPRESION DE HONORARIOS SALAM</td></tr>";
		echo "<tr><td  bgcolor=#cccccc>Honorario Nro.</td><td bgcolor=#cccccc><INPUT TYPE='text' NAME='whon'></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=1 align=left>";
		echo "<tr><td align=center colspan=4><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=4><font size=5 face='tahoma'><b>IMPRESION DE HONORARIOS SALAM Ver. 1.00</font></b></font></td></tr>";
		echo "<tr><td align=right  bgcolor=#dddddd colspan=4><font size=4 face='tahoma'><b>NRo. : ".$whon."</b></font></font></td></tr>";
		$query = "SELECT  Numero, Fecha, Cedula_Paciente, Nombre, Edad, Sexo, Metodo, Agente, Tipo_Atencion, Especialidad, Turno, Asa, Expliacacion_Asa, Tipo_Asa, Anestesia, Entidad, Ptos_Eval_Preanestesica, Cirugia, Otras_Cirugias, Ptos_Cirugia, Tiempo_anestesia, Ptos_tiempo_anestesia, Posicion, Procedimientos, Ptos_unidades_mod, Unidades_mod, Total_puntos, Cortesia, Total_honorarios, Anestesiologo, Cancelada from salam_000009 ";
		$query .= "  WHERE Numero=".$whon;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>FECHA : </b></font><font face='tahoma' size=2>".$row[1]."</font></td><td colspan=2><font face='tahoma' size=2><b>C.C. NRo. </b></font><font face='tahoma' size=2>".$row[2]."</font></td></tr>";
		echo "<tr><td colspan=4><font face='tahoma' size=2><b>PACIENTE : </b></font><font face='tahoma' size=2>".$row[3]."</font></td></tr>";
		echo "<tr><td><font face='tahoma' size=2><b>EDAD : </b></font><font face='tahoma' size=2>".$row[4]."</font></td><td><font face='tahoma' size=2><b>SEXO : </b>".substr($row[5],strpos($row[5],"-")+1)."</font></td><td colspan=2><font face='tahoma' size=2><b>METODO :</b> ".substr($row[6],strpos($row[6],"-")+1)."</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>AGENTE : </b></font><font face='tahoma' size=2>".substr($row[7],strpos($row[7],"-")+1)."</font></td><td colspan=2><font face='tahoma' size=2><b>ATENCION : </b>".substr($row[8],strpos($row[8],"-")+1)."</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>ESPECIALIDAD : </b></font><font face='tahoma' size=2>".substr($row[9],strpos($row[9],"-")+1)."</font></td><td colspan=2><font face='tahoma' size=2><b>TURNO : </b>".substr($row[10],strpos($row[10],"-")+1)."</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>ASA : </b></font><font face='tahoma' size=2>".substr($row[11],strpos($row[11],"-")+1)." --  ".substr($row[13],strpos($row[13],"-")+1)."</font></td><td colspan=2><font face='tahoma' size=2><b>TIPO ANESTESIA : </b></font><font face='tahoma' size=2>".substr($row[14],strpos($row[14],"-")+1)."</font></td></tr>";
		echo "<tr><td colspan=4><font face='tahoma' size=2><b>EXPLICACION ASA : </b></font><font face='tahoma' size=2>";
		$asa=explode(",",$row[12]);
		$count=count($asa);
		if($count >1)
		{
			for ($i=0;$i<$count;$i++)
				echo $asa[$i]."<br>";
			echo "</font></td></tr>";
		}
		else
			echo $row[12]."</font></td></tr>";
		echo "<tr><td colspan=4><font face='tahoma' size=2><b>ENTIDAD : </b></font><font face='tahoma' size=2>".substr($row[15],strpos($row[15],"-")+1)."</font></td></tr>";
		if(substr($row[15],0,strpos($row[15],"-")) != "09")
			echo "<tr><td colspan=2><font face='tahoma' size=2><b>PUNTOS EVALUACION PREANESTESICA : </b></font></td><td align=right colspan=2><font face='tahoma' size=2>".$row[16]."</font></td></tr>";
		else
		{
			$query="select Tarifa from salam_000010 where Codigo='09'";
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_row($err1);
			$wh=$row1[0] * 4;
			echo "<tr><td colspan=2><font face='tahoma' size=2><b>CONSULTA PREANESTESICA : </b></font></td><td align=right colspan=2><font face='tahoma' size=2>$".number_format((double)$wh,0,'.',',')."</font></td></tr>";
		}
		echo "<tr><td colspan=4><font face='tahoma' size=2><b>CIRUGIA : </b></font><font face='tahoma' size=2>".$row[17]."<BR>";
		$cir=explode(",",$row[18]);
		$count=count($cir);
		if($count >1)
		{
			for ($i=0;$i<$count;$i++)
				echo $cir[$i]."<br>";
			echo "</font></td></tr>";
		}
		else
			echo "</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>PUNTOS CIRUGIA : </b></font></td><td align=right colspan=2><font face='tahoma' size=2>".number_format((double)$row[19],0,'.',',')."</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>PUNTOS TIEMPO ANESTESIA : </b>".substr($row[20],0,strpos($row[20],"."))."h ".substr($row[20],strpos($row[20],".") + 1)."m</font></td><td align=right colspan=2><font face='tahoma' size=2>".number_format((double)$row[21],0,'.',',')."</font></td></tr>";
		echo "<tr><td colspan=4><font face='tahoma' size=2><b>D. U. MODIFICADORAS : </b></font><font face='tahoma' size=2>".$row[25]."</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>PUNTOS D. U. MODIFICADORAS : </b></font></td><td align=right colspan=2><font face='tahoma' size=2>".number_format((double)$row[24],0,'.',',')."</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>TOTAL PUNTOS : </b></font></td><td align=right colspan=2><font face='tahoma' size=2>".number_format((double)$row[26],0,'.',',')."</font></td></tr>";
		echo "<tr><td colspan=2 bgcolor=#dddddd><font face='tahoma' size=2><b>TOTAL HONORARIOS : </b></font></td><td align=right bgcolor=#dddddd colspan=2><font face='tahoma' size=2><b>$".number_format((double)$row[28],0,'.',',')."</b></font></td></tr>";
		if(substr($row[15],0,strpos($row[15],"-")) == "09")
		{
			$query="select Tarifa from salam_000010 where Codigo='99'";
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_row($err1);
			$wh= $wh - $row1[0];
			echo "<tr><td colspan=2 bgcolor=#dddddd><font face='tahoma' size=2><b>TOTAL PACIENTE : </b></font></td><td align=right bgcolor=#dddddd colspan=2><font face='tahoma' size=2><b>$".number_format((double)$wh,0,'.',',')."</b></font></td></tr>";
		}
		echo "<tr><td colspan=4><font face='tahoma' size=2><b>ANESTESIOLOGO : </b></font><font face='tahoma' size=2>".substr($row[29],strpos($row[29],"-")+1)."</font></td></tr>";
		echo"</table>";
		
		echo "<table border=1 align=right>";
		echo "<tr><td align=center colspan=4><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=4><font size=5 face='tahoma'><b>IMPRESION DE HONORARIOS SALAM Ver. 1.00</font></b></font></td></tr>";
		echo "<tr><td align=right  bgcolor=#dddddd colspan=4><font size=4 face='tahoma'><b>NRo. : ".$whon."</b></font></font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>FECHA : </b></font><font face='tahoma' size=2>".$row[1]."</font></td><td colspan=2><font face='tahoma' size=2><b>C.C. NRo. </b></font><font face='tahoma' size=2>".$row[2]."</font></td></tr>";
		echo "<tr><td colspan=4><font face='tahoma' size=2><b>PACIENTE : </b></font><font face='tahoma' size=2>".$row[3]."</font></td></tr>";
		echo "<tr><td><font face='tahoma' size=2><b>EDAD : </b></font><font face='tahoma' size=2>".$row[4]."</font></td><td><font face='tahoma' size=2><b>SEXO : </b>".substr($row[5],strpos($row[5],"-")+1)."</font></td><td colspan=2><font face='tahoma' size=2><b>METODO :</b> ".substr($row[6],strpos($row[6],"-")+1)."</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>AGENTE : </b></font><font face='tahoma' size=2>".substr($row[7],strpos($row[7],"-")+1)."</font></td><td colspan=2><font face='tahoma' size=2><b>ATENCION : </b>".substr($row[8],strpos($row[8],"-")+1)."</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>ESPECIALIDAD : </b></font><font face='tahoma' size=2>".substr($row[9],strpos($row[9],"-")+1)."</font></td><td colspan=2><font face='tahoma' size=2><b>TURNO : </b>".substr($row[10],strpos($row[10],"-")+1)."</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>ASA : </b></font><font face='tahoma' size=2>".substr($row[11],strpos($row[11],"-")+1)." --  ".substr($row[13],strpos($row[13],"-")+1)."</font></td><td colspan=2><font face='tahoma' size=2><b>TIPO ANESTESIA : </b></font><font face='tahoma' size=2>".substr($row[14],strpos($row[14],"-")+1)."</font></td></tr>";
		echo "<tr><td colspan=4><font face='tahoma' size=2><b>EXPLICACION ASA : </b></font><font face='tahoma' size=2>";
		$asa=explode(",",$row[12]);
		$count=count($asa);
		if($count >1)
		{
			for ($i=0;$i<$count;$i++)
				echo $asa[$i]."<br>";
			echo "</font></td></tr>";
		}
		else
			echo $row[12]."</font></td></tr>";
		echo "<tr><td colspan=4><font face='tahoma' size=2><b>ENTIDAD : </b></font><font face='tahoma' size=2>".substr($row[15],strpos($row[15],"-")+1)."</font></td></tr>";
		if(substr($row[15],0,strpos($row[15],"-")) != "09")
			echo "<tr><td colspan=2><font face='tahoma' size=2><b>PUNTOS EVALUACION PREANESTESICA : </b></font></td><td align=right colspan=2><font face='tahoma' size=2>".$row[16]."</font></td></tr>";
		else
		{
			$queryp="select Tarifa from salam_000010 where Codigo='09'";
			$err1 = mysql_query($queryp,$conex);
			$row1 = mysql_fetch_row($err1);
			$wh=$row1[0] * 4;
			echo "<tr><td colspan=2><font face='tahoma' size=2><b>CONSULTA PREANESTESICA : </b></font></td><td align=right colspan=2><font face='tahoma' size=2>$".number_format((double)$wh,0,'.',',')."</font></td></tr>";
		}
		echo "<tr><td colspan=4><font face='tahoma' size=2><b>CIRUGIA : </b></font><font face='tahoma' size=2>".$row[17]."<BR>";
		$cir=explode(",",$row[18]);
		$count=count($cir);
		if($count >1)
		{
			for ($i=0;$i<$count;$i++)
				echo $cir[$i]."<br>";
			echo "</font></td></tr>";
		}
		else
			echo "</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>PUNTOS CIRUGIA : </b></font></td><td align=right colspan=2><font face='tahoma' size=2>".number_format((double)$row[19],0,'.',',')."</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>PUNTOS TIEMPO ANESTESIA : </b>".substr($row[20],0,strpos($row[20],"."))."h ".substr($row[20],strpos($row[20],".") + 1)."m</font></td><td align=right colspan=2><font face='tahoma' size=2>".number_format((double)$row[21],0,'.',',')."</font></td></tr>";
		echo "<tr><td colspan=4><font face='tahoma' size=2><b>D. U. MODIFICADORAS : </b></font><font face='tahoma' size=2>".$row[25]."</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>PUNTOS D. U. MODIFICADORAS : </b></font></td><td align=right colspan=2><font face='tahoma' size=2>".number_format((double)$row[24],0,'.',',')."</font></td></tr>";
		echo "<tr><td colspan=2><font face='tahoma' size=2><b>TOTAL PUNTOS : </b></font></td><td align=right colspan=2><font face='tahoma' size=2>".number_format((double)$row[26],0,'.',',')."</font></td></tr>";
		echo "<tr><td colspan=2 bgcolor=#dddddd><font face='tahoma' size=2><b>TOTAL HONORARIOS : </b></font></td><td align=right bgcolor=#dddddd colspan=2><font face='tahoma' size=2><b>$".number_format((double)$row[28],0,'.',',')."</b></font></td></tr>";
		if(substr($row[15],0,strpos($row[15],"-")) == "09")
		{
			$query="select Tarifa from salam_000010 where Codigo='99'";
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_row($err1);
			$wh= $wh - $row1[0];
			echo "<tr><td colspan=2 bgcolor=#dddddd><font face='tahoma' size=2><b>TOTAL PACIENTE : </b></font></td><td align=right bgcolor=#dddddd colspan=2><font face='tahoma' size=2><b>$".number_format((double)$wh,0,'.',',')."</b></font></td></tr>";
		}
		echo "<tr><td colspan=4><font face='tahoma' size=2><b>ANESTESIOLOGO : </b></font><font face='tahoma' size=2>".substr($row[29],strpos($row[29],"-")+1)."</font></td></tr>";
		echo"</table>";
	}
}
?>
</body>
</html>