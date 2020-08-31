<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Codigos Nuevos En Produccion</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc151.php Ver. 2016-02-22</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc151.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)or !isset($wcco1) or !isset($wcco2) or $wper1 < 1 or $wper1 > 12 or !isset($wper2) or $wper2 < 1 or $wper2 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CODIGOS NUEVOS EN PRODUCCION</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
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
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=6><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
			echo "<tr><td align=center colspan=6><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
			echo "<tr><td align=center colspan=6><font size=2>CODIGOS NUEVOS PRODUCCION</font></td></tr>";
			echo "<tr><td align=center colspan=6><font size=2>EMPRESA : ".$wempt."</font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=6><font size=2><b>A&Ntilde;O : ".$wanop." MES ".$wper1." - ".$wper2."</b></font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=6><font size=2><b>C.C. INICIAL: ".$wcco1." C.C. FINAL: ".$wcco2."</b></font></td></tr>";
			echo "<tr><td bgcolor=#CCCCCC align=center><b>CCO</b></td><td bgcolor=#CCCCCC align=center><b>CONCEPTO</b></td><td bgcolor=#CCCCCC align=center><b>NOM. CONCEPTO</b></td><td bgcolor=#CCCCCC align=center><b>CODIGO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>CANTIDAD</b></td></tr>";
			//                  0      1      2     3      4           5
			$query = "select Estcco,Estcon,Estcod,Esttip,Estnom,sum(Estcan) from ".$empresa."_000141  ";
			$query .= "  where Estano = ".$wanop;
			$query .= "    and Estemp = '".$wemp."'"; 
			$query .= "    and Estmes between ".$wper1." and ".$wper2;
			$query .= "    and Estcco between '".$wcco1."' and '".$wcco2."'";
			$query .= "    and Estcod not in (select mprpro from  ".$empresa."_000095 where mprcco=Estcco and mprcon = Estcon and mpremp = '".$wemp."') ";
			$query .= "   group by 1,2,3 ";
			$query .= "   Order by 1,2,3 ";
 			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$color="#99CCFF";
				else
					$color="#ffffff";
				$row = mysql_fetch_array($err);
				$query = "select Cfades  from ".$empresa."_000060 ";
				$query .= "  where Cfacod = '".$row[1]."'";
				$query .= "    and Cfaemp = '".$wemp."'"; 
	 			$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$desc=$row1[0];
				}
				else
					$desc="";
				if($row[1] == "0168" or $row[1] == "0169" or $row[1] == "0616" or $row[1] == "0626")
				{
					$query = "select Insdes from ".$empresa."_000089 ";
					$query .= "  where Inscod ='".$row[2]."' ";
					$query .= "    and Insemp = '".$wemp."'"; 
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$deso=$row1[0];
					}
				}
				else
					$deso=$row[4];
	 			
				echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color."><font size=2>".$desc."</font></td><td bgcolor=".$color."><font size=2>".$row[2]."</font></td><td bgcolor=".$color."><font size=2>".$deso."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format((double)$row[5],0,'.',',')."</font></td></tr>";
    		}
    		echo "</table></center>";
		}
	}
?>
</body>
</html>
