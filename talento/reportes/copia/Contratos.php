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
	<style type="text/css">
		.tipoTABLEGRID{font-family:Arial;border-style:solid;border-collapse:collapse;table-layout:fixed;}
		.tipoAL02GRID1{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
		.tipoAL02GRID2{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
		.tipoAL02GRID3{color:#000066;background:#CCCCCC;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
		.tipoAL02GRID3O{color:#000066;background:#CCCCCC;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
		.tipoAL02GRID4A{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:justify;}
		.tipoAL02GRID4B{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:justify;}
	</style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Busqueda de Contratos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Contratos.php Ver. 2017-10-3	0</b></font></tr></td></table>
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
		$query = "SELECT MID(Usutip,1,1),Usuobj  from ".$empresa."_000003 where Usucod = '".$key."' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			$wtt = "";
			$row = mysql_fetch_array($err);
			if($row[0] == "G")
			{
				$tipo = explode(",",$row[1]);
				$wtt = "(";
				for ($i=0;$i<count($tipo);$i++)
					if($i == 0)
						$wtt .= chr(34).$tipo[$i].chr(34);
					else
						$wtt .= chr(44).chr(34).$tipo[$i].chr(34);
				$wtt .= ")";
			}
		}
		if($wtt == "")
			$query = "SELECT Numtip,Nomtip  from ".$empresa."_000001 order by Numtip";
		else
			$query = "SELECT Numtip,Nomtip  from ".$empresa."_000001 where Numtip in ".$wtt." order by Numtip";
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
		$query = "SELECT Usutip,Usuobj  from ".$empresa."_000003 where Usucod = '".$key."' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			$row = mysql_fetch_array($err);
			$tipo = explode(",",$row[1]);
			$wtt = "(";
			for ($i=0;$i<count($tipo);$i++)
				if($i == 0)
					$wtt .= chr(34).$tipo[$i].chr(34);
				else
					$wtt .= chr(44).chr(34).$tipo[$i].chr(34);
			$wtt .= ")";
		}
		else
			$wtipo = $key;
		echo "<center><table border=1 class='tipoTABLEGRID'>";
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
		if($wtipo == $key)
		    $query .= "    and Conres like '".$tipo."%' ";
		else
			if($tipo[0] != "T")
				$query .= "    and MID(Contip,1,2) in ".$wtt ." ";
		$query = $query."   order by Concon";
		echo $query."<br>";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			$gridcolor="tipoAL02GRID3";
			$gridcoloro="tipoAL02GRID3O";
			echo "<tr><td class='".$gridcolor."'><b>NRO CONTRATO</b></td><td class='".$gridcolor."'><b>TIPO</b></td><td class='".$gridcolor."'><b>PARTE 1</b></td><td class='".$gridcolor."'><b>PARTE 2</b></td><td class='".$gridcolor."'><b>NIT</b></td><td class='".$gridcoloro."'><b>OBJETO</b></td><td class='".$gridcolor."'><b>VISUALIZAR</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
				{
					$gridcolor="tipoAL02GRID1";
					$gridcoloro="tipoAL02GRID4A";
				}
				else
				{
					$gridcolor="tipoAL02GRID2";
					$gridcoloro="tipoAL02GRID4B";
				}
				$row = mysql_fetch_array($err);
				 echo "<tr>";
				 echo "<td class='".$gridcolor."'>".$row[0]."</td>";
				 echo "<td class='".$gridcolor."'>".$row[1]."</td>";
				 echo "<td class='".$gridcolor."'>".$row[2]."</td>";
				 echo "<td class='".$gridcolor."'>".$row[3]."</td>";
				 echo "<td class='".$gridcolor."'>".$row[4]."</td>";
				 echo "<td class='".$gridcoloro."'>".wordwrap($row[5], 100, "\n", true)."</td>";
				 $path="/matrix/verPDF.php?documento=/".$empresa."/".$row[0].".pdf";
				 echo "<td onclick='ejecutar(".chr(34).$path.chr(34).")' class='".$gridcolor."'>".$row[0]."</td>";
			}
		}
	}
}
?>
</body>
</html>
