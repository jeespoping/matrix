<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>TOTAL CIRUGIAS X ENTIDAD</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Totxemp.php Ver 2015-02-09</b></font></tr></td></table>
</center>
<?php
$consultaAjax = '';
include_once("conex.php");
include_once("root/comun.php");
$wemp_pmla=$_REQUEST['wemp_pmla'];
 @session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='Totxemp.php?wemp_pmla=".$wemp_pmla."' method=post>";
	if(!isset($v0) or !isset($v1))
	{
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>TOTAL CIRUGIAS X ENTIDAD</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Inicial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='RADIO' name=x value=0> Primer Nivel <input type='RADIO' name=x value=1> Segundo Nivel <input type='RADIO' name=x value=2> Todos </td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		
		$wtcx = consultarAliasPorAplicacion($conex, $wemp_pmla, "tcx");
		$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
		
		$query = "select Empcod, Empnom, count(*) as k  from ".$wtcx."_000008,".$wtcx."_000011,".$wcliame."_000024  ";
		$query .= " where ".$wtcx."_000008.Mcifec between  '".$v0."' and  '".$v1."' ";
		if ($x == 0)
			$query .= " and CONV(Mciqui,10,10) between  1 and  10 ";
		elseif ($x == 1)
				$query .= " and CONV(Mciqui,10,10) > 10 ";
		$query .= "   and ".$wtcx."_000008.Mcitur = ".$wtcx."_000011.Turtur ";
		$query .= "   and ".$wtcx."_000011.Tureps = ".$wcliame."_000024.Empcod  ";
		$query .= "   and ".$wtcx."_000011.Fecha_data >= '2015-02-24' ";
		$query .= "  Group by Empcod, Empnom  ";
		$query .= " UNION ALL  ";
		$query .= " select Entcod, Entdes, count(*) as k  from ".$wtcx."_000008,".$wtcx."_000011,".$wtcx."_000003  ";
		$query .= " where ".$wtcx."_000008.Mcifec between  '".$v0."' and  '".$v1."' ";
		if ($x == 0)
			$query .= " and CONV(Mciqui,10,10) between  1 and  10 ";
		elseif ($x == 1)
				$query .= " and CONV(Mciqui,10,10) > 10 ";
		$query .= "   and ".$wtcx."_000008.Mcitur = ".$wtcx."_000011.Turtur ";
		$query .= "   and ".$wtcx."_000011.Tureps = ".$wtcx."_000003.Entcod  ";
		$query .= "   and ".$wtcx."_000011.Fecha_data < '2015-02-24' ";
		$query .= "  Group by Entcod, Entdes  ";
		$query .= "  Order by k desc";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<center><table border=1>";
		echo "<tr><td colspan=4 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=4 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=4 align=center><b>TOTAL CIRUGIAS X ENTIDAD</b></td></tr>";
		switch ($x)
		{
			case 0:
				echo "<tr><td colspan=4 align=center><b>QUIROFANOS PRIMER NIVEL</b></td></tr>";
			break;
			case 1:
				echo "<tr><td colspan=4 align=center><b>QUIROFANOS SEGUNDO NIVEL</b></td></tr>";
			break;
			case 2:
				echo "<tr><td colspan=4 align=center><b>TODOS LOS QUIROFANOS</b></td></tr>";
			break;
		}
		echo "<tr><td colspan=4 align=center><b>Desde : ".$v0." Hasta ".$v1."</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc><b>POSICION</b></td>";
		echo "<td bgcolor=#cccccc><b>NIT</b></td>";
		echo "<td bgcolor=#cccccc><b>DESCRIPCION</b></td>";
		echo "<td align=right bgcolor=#cccccc><b>CANTIDAD</b></td>";
		echo "</tr>"; 
		$t=array();
		$t[0] = 0;
		$t[1] = 0;
		$t[2] = 0;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			echo "<tr>";
			$j = $i + 1;
			echo "<td align=center>".$j."</td>";
			echo "<td>".$row[0]."</td>";
			echo "<td>".$row[1]."</td>";
			$t[2]+=$row[2];
			echo "<td align=right>".number_format($row[2],0,'.',',')."</td>";
			echo "</tr>"; 
		}
		echo "<tr><td  bgcolor=#999999 colspan=3 align=center><b>TOTALES</b></td>";
		echo "<td bgcolor=#999999 align=right><b>".number_format($t[2],0,'.',',')."</b></td>";
		echo "</tr>"; 
		echo "</table></center>"; 
	}
}
?>
</body>
</html>
