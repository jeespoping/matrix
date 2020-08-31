<?php
include_once("conex.php");
/*Reportar Los errores importantes*/




	$query="
	select  *  from farmpda_000004,farmpda_000005 where farmpda_000004.Cod_int IN ('1003','1004',1006','1007') and farmpda_000005.Codigo=farmpda_000004.Cod_int order by farmpda_000004.Fecha_data,farmpda_000004.Hora_Data DESC";
	$err=mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num>0)
	{
	
		echo "</table><table border=1 align='center' width=750>";
		echo "<TR><td><font size=2 face='arial'><b>FECHA<b></td>";
		echo "<td ><font size=2 face='arial'><b>HORA</td>";
		echo "<td ><font size=2 face='arial'><b>ODBC</td>";
		echo "<td ><font size=2 face='arial'><b>CC</td>";
		echo "<td ><font size=2 face='arial'><b>REG.</td>";
		echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
		echo "<td ><font size=2 face='arial'><b>ARTICULO</td>";
		echo "<td ><font size=2 face='arial'><b>TIPO_GRABACION</td>";
		echo "<td ><font size=2 face='arial'><b>COD. INT.</td>";
		echo "<td ><font size=2 face='arial'><b>DESC.</td></TR>";
		for($i=0;$i<$num;$i++)
		{
			$row=mysql_fetch_array($err);
			
		echo "<TR><td><font size=2 face='arial'>".$row["Fecha_data"]."</td>";
		echo "<td ><font size=2 face='arial'>".$row["Hora_data"]."</td>";
		echo "<td ><font size=2 face='arial'>".$row["Odbc"]."</td>";
		echo "<td ><font size=2 face='arial'>".$row["Cc"]."</td>";
		echo "<td ><font size=2 face='arial'>".$row["Reg"]."-".$row["Num"]."</td>";
		echo "<td ><font size=2 face='arial'>".$row["Historia"]."</td>";
		echo "<td ><font size=2 face='arial'>".$row["Articulo"]."</td>";
		echo "<td ><font size=2 face='arial'>".$row["Tipo_grabacion"]."</td>";
		echo "<td ><font size=2 face='arial'>".$row["Cod_int"]."</td>";
		echo "<td ><font size=2 face='arial'>".$row["Desc"]."</td></TR>";
			
		}
	}
?>