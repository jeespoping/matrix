<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>CIRUGIAS X ENTIDAD</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Turxepsxfec.php Ver. 2008-04-08</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
include_once("root/comun.php");
$wemp_pmla=$_REQUEST['wemp_pmla'];
$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
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




	echo "<form action='Turxepsxfec.php?wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($v0) or !isset($v1) or !isset($v2))
	{
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>CIRUGIAS X ENTIDAD</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Entidad Sistema Anterior</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Entcod, Entdes from ".$empresa."_000003 where Entest='on' order by Entdes";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<select name='v0'>";
		echo "<option>0-SELECCIONE</option>";
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select>";
		echo"</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Entidad Sistema Actual</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Empcod, Empnom from ".$wcliame."_000024 where Empest='on' order by Empnom ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<select name='v3'>";
		echo "<option>0-SELECCIONE</option>";
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select>";
		echo"</td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v2' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wv=$v0;
		$v0=ver($v0);
		$v3=ver($v3);
		$query  = "select Turtur,Turqui,Turhin,Turhfi,Turfec,Turdoc,Turnom,Turcir,Turmed from ".$empresa."_000011 ";
		$query .= " where tureps= '".$v0."' ";
		$query .= "   and  turfec BETWEEN '".$v1."' and '".$v2."'";
		$query .= "   and tcx_000011.Fecha_data < '2015-02-24' ";
		$query .= "  UNION ALL  ";
		$query .= " select Turtur,Turqui,Turhin,Turhfi,Turfec,Turdoc,Turnom,Turcir,Turmed from ".$empresa."_000011 ";
		$query .= " where tureps= '".$v3."' ";
		$query .= "   and  turfec BETWEEN '".$v1."' and '".$v2."'";
		$query .= "   and tcx_000011.Fecha_data >= '2015-02-24' ";
		$query .= "  order by Turtur  ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<table border=1>";
		echo "<tr><td colspan=9 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=9 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=9 align=center><b>CIRUGIAS X ENTIDAD X FECHAS</b></td></tr>";
		echo "<tr><td colspan=9 align=center><b>ENTIDAD : ".$wv."</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc><b>CODIGO<BR>TURNO</b></td>";
		echo "<td bgcolor=#cccccc><b>QUIROFANO</b></td>";
		echo "<td bgcolor=#cccccc><b>HORA<BR>INICIO</b></td>";
		echo "<td bgcolor=#cccccc><b>HORA<BR>FINAL</b></td>";
		echo "<td bgcolor=#cccccc><b>FECHA</b></td>";
		echo "<td bgcolor=#cccccc><b>IDENTIFICACION</b></td>";
		echo "<td bgcolor=#cccccc><b>PACIENTE</b></td>";
		echo "<td bgcolor=#cccccc><b>CIRUGIAS</b></td>";
		echo "<td bgcolor=#cccccc><b>MEDICOS</b></td>";
		echo "</tr>";
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
			echo "<td>".$row[6]."</td>";
			echo "<td>".$row[7]."</td>";
			echo "<td>".$row[8]."</td>";
			echo "</tr>";
		}
		 echo "</table>";
	}
}
?>
</body>
</html>
