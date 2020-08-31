<html>
<head>
  <title>MATRIX</title>
  <script type="text/javascript">
		<!--
			function ejecutar(path)
			{
				window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=0,titlebar=0');
			}
		//-->
	</script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Busqueda de Contratos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Contratos.php Ver. 2016-06-10</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='Contratos.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wnum))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>SISTEMA DE INFORMACION DE CONTRATOS</td></tr>";
		echo "<tr><td align=center colspan=2>BUSQUEDA DE CONTRATOS</td></tr>";
		echo "<tr><td bgcolor=#999999 align=center colspan=2>Criterio de Busqueda</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Numero de Contrato</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnum' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Tipo de Contrato</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Numtip,Nomtip  from ".$empresa."_000001 order by Numtip";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wtip'>";
			echo "<option>Seleccione</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Parte 1</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpa1' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Parte 2</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpa2' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nit Contrato</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnit' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Objeto Contrato</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wobj' size=30 maxlength=30></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<center><table border=1>";
		echo "<tr><td align=center colspan=7><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td align=center colspan=7><b>SISTEMA DE INFORMACION DE CONTRATOS</b></td></tr>";
		echo "<tr><td align=center colspan=7><b>BUSQUEDA DE CONTRATOS</b></td></tr>";
		$query  = "SELECT Concon, Contip, Conpa1, Conpa2, Connit, Conobj from ".$empresa."_000002 ";
		$query .= "  where Conest = 'on' ";
		if($wnum != "")
			$query .= "    and Concon = '".$wnum."' ";
		if($wtip != "Seleccione")
			$query .= "    and Contip = '".$wtip."' ";
		if($wpa1 != "")
			$query .= "    and Conpa1 like '%".$wpa1."%'";
		if($wpa2 != "")
			$query .= "    and Conpa2 like '%".$wpa2."%' ";
		if($wnit != "")
			$query .= "    and Connit = '".$wnit."' ";
		if($wobj != "")
			$query .= "    and Conobj like '%".$wobj."%' ";
		$query = $query."   order by Concon";
		//echo $query."<br>";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<tr><td><b>NRO CONTRATO</b></td><td><b>TIPO</b></td><td><b>PARTE 1</b></td><td><b>PARTE 2</b></td><td><b>NIT</b></td><td><b>OBJETO</b></td><td><b>VISUALIZAR</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				 echo "<tr>";
				 echo "<td>".$row[0]."</td>";
				 echo "<td>".$row[1]."</td>";
				 echo "<td>".$row[2]."</td>";
				 echo "<td>".$row[3]."</td>";
				 echo "<td>".$row[4]."</td>";
				 echo "<td>".$row[5]."</td>";
				 $path="/matrix/verPDF.php?documento=/".$empresa."/".$row[0].".pdf";
				 echo "<td onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row[0]."</td>";
			}
		}
	}
}
?>
</body>
</html>
