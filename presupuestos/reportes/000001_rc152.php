<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Protocolos o Paquetes Inactivos o Sin Costo</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc152.php Ver. 2016-03-22</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc152.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wcco1)  or !isset($wcco2) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PROTOCOLOS O PAQUETES INACTIVOS O SIN COSTO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wemp'>";
				echo "<option>Seleccione</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$wcco2=strtolower ($wcco2);
			//                    0        1      2       3      4       5       6       7       8
			$query = " SELECT Proccp, Procod, Progrp, Procoa, Protip, Procco, Propro, Progru, Procon from ".$empresa."_000100 ";
			$query .= "  where Procco  between '".$wcco1."' and '".$wcco2."'";
			$query .= "    and Proemp = '".$wemp."'"; 
			$query .= "    and Protip = '3' ";
			$query .= "    and Procod not in (SELECT Pcacod from ".$empresa."_000097  where Pcacco = Proccp and Pcagru = Progrp and Pcacon = Procoa and Pcaano=".$wanop." and Pcames=".$wper1." and Pcaemp = '".$wemp."') ";
			$query .= "  Group by 1,2 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=12 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=12 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=12 align=center>PROTOCOLOS INACTIVOS O SIN COSTO O SIN NOMBRE</td></tr>";
			echo "<tr><td colspan=12 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=12 align=center>PERIODO  : ".$wper1. " AÑO : ".$wanop."</td></tr>";
			echo "<tr><td colspan=4 bgcolor=#cccccc>ORIGEN</td><td colspan=5 bgcolor=#cccccc>ANEXO</td><td colspan=3 bgcolor=#cccccc>TIPOS Y COMENTARIOS</td></tr>";
			echo "<tr><td><b>C.C.</b></td><td><b>PROTOCOLO</b></td><td><b>GRUPO</b></td><td><b>CONCEPTO</b></td><td><b>C.C.</b></td><td><b>PROTOCOLO</b></td><td><b>DESCRIPCION</b></td><td><b>GRUPO</b></td><td><b>CONCEPTO</b></td><td><b>TIPO T100</b></td><td><b>TIPO T95</b></td><td><b>COMENTARIOS</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "SELECT Mprnom, Mprtip from ".$empresa."_000095 ";
				$query = $query." where Mprcco = '".$row[0]."'";
				$query = $query."   and Mpremp = '".$wemp."'"; 
			    $query = $query."   and Mprpro = '".$row[1]."'";
			    $query = $query."   and Mprgru = '".$row[2]."'";
			    $query = $query."   and Mprcon = '".$row[3]."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					echo "<tr><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row1[0]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row1[1]."</td><td>PROTOCOLO SIN COSTO</td></tr>";
				}
				else
				{
					echo "<tr><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[0]."</td><td>".$row[1]."</td><td></td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td></td><td>PROTOCOLO SIN NOMBRE Y SIN COSTO</td></tr>";
				}
			}
			echo "</table>";
			$query = " SELECT Pquccp, Pqucod, Pqugrp, Pqucoa, Pqutip, Pqucco, Pqupro, Pqucon, Pqucon from ".$empresa."_000099 ";
			$query .= "  where Pqucco  between '".$wcco1."' and '".$wcco2."'";
			$query .= "    and Pquemp = '".$wemp."'"; 
			$query .= "    and Pqutip = '3' ";
			$query .= "    and Pqucod not in (SELECT Pcacod from ".$empresa."_000097  where Pcacco = Pquccp and Pcagru = Pqugrp and Pcacon = Pqucoa and Pcaano=".$wanop." and Pcames=".$wper1." and Pcaemp = '".$wemp."') ";
			$query .= "  Group by 1,2 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<br><br><table border=1>";
			echo "<tr><td colspan=12 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=12 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=12 align=center>PAQUETES INACTIVOS O SIN COSTO O SIN NOMBRE</td></tr>";
			echo "<tr><td colspan=12 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=12 align=center>PERIODO  : ".$wper1. " AÑO : ".$wanop."</td></tr>";
			echo "<tr><td colspan=4 bgcolor=#cccccc>ORIGEN</td><td colspan=5 bgcolor=#cccccc>ANEXO</td><td colspan=3 bgcolor=#cccccc>TIPOS Y COMENTARIOS</td></tr>";
			echo "<tr><td><b>C.C.</b></td><td><b>PROTOCOLO</b></td><td><b>GRUPO</b></td><td><b>CONCEPTO</b></td><td><b>C.C.</b></td><td><b>PROTOCOLO</b></td><td><b>DESCRIPCION</b></td><td><b>GRUPO</b></td><td><b>CONCEPTO</b></td><td><b>TIPO T100</b></td><td><b>TIPO T95</b></td><td><b>COMENTARIOS</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "SELECT Mprnom, Mprtip from ".$empresa."_000095 ";
				$query = $query." where Mprcco = '".$row[0]."'";
				$query = $query."   and Mpremp = '".$wemp."'"; 
			    $query = $query."   and Mprpro = '".$row[1]."'";
			    $query = $query."   and Mprgru = '".$row[2]."'";
			    $query = $query."   and Mprcon = '".$row[3]."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					echo "<tr><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row1[0]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row1[1]."</td><td>PROTOCOLO SIN COSTO</td></tr>";
				}
				else
				{
					echo "<tr><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[0]."</td><td>".$row[1]."</td><td></td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td></td><td>PROTOCOLO SIN NOMBRE Y SIN COSTO</td></tr>";
				}
			}
			echo "</table>";
		}
	}
?>
</body>
</html>
