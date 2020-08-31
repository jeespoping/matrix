<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe Comparativo de Rubros x Mes</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc175.php Ver. 2016-03-08</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc175.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2)  or ($wrub1 == "Seleccione" and $wrub2 == "Seleccione" and $wrub3 == "Seleccione"))
		{
			echo $wrub1."<br>";
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME COMPARATIVO DE RUBROS X MES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Rubro Nro 1</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Mgacod,Mganom  from ".$empresa."_000028 group by 1 order by 2";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wrub1'>";
				echo "<option>Seleccione</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Rubro Nro 2</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Mgacod,Mganom  from ".$empresa."_000028 group by 1 order by 2";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wrub2'>";
				echo "<option>Seleccione</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Rubro Nro 3</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Mgacod,Mganom  from ".$empresa."_000028 group by 1 order by 2";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wrub3'>";
				echo "<option>Seleccione</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
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
			$wrubros = "(";
			if($wrub1 != "Seleccione")
				$wrubros .= "'".substr($wrub1,0,strpos($wrub1,"-"))."',";
			else
				$wrubros .= "'',";
			if($wrub2 != "Seleccione")
				$wrubros .= "'".substr($wrub2,0,strpos($wrub2,"-"))."',";
			else
				$wrubros .= "'',";
			if($wrub3 != "Seleccione")
				$wrubros .= "'".substr($wrub3,0,strpos($wrub3,"-"))."'";
			else
				$wrubros .= "''";
			$wrubros .= ")";
			$query = "select Rescco,Cconom,Resper,sum(Resmon) ";
			$query = $query."  from ".$empresa."_000043,".$empresa."_000005 ";
			$query = $query."  where Resano = ".$wanop; 
			$query = $query."    and resemp = '".$wemp."' ";
			$query = $query."    and Resper between ".$wper1." and ".$wper2; 
			$query = $query."    and Rescpr in ".$wrubros; 
			$query = $query."    and Rescco = Ccocod ";
			$query = $query."    and resemp = Ccoemp ";
			$query = $query."  Group by 1,2,3 ";
			$query = $query."  Order by 1,3 ";
			$err = mysql_query($query,$conex)  or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			
			echo "<table border=1>";
			echo "<tr><td colspan=14 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=14 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=14 align=center>INFORME COMPARATIVO DE RUBROS X MES</td></tr>";
			echo "<tr><td colspan=14 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=14 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			if($wrub1 != "Seleccione")
				echo "<tr><td colspan=14 align=center>RUBRO : ".$wrub1."</td></tr>";
			if($wrub2 != "Seleccione")
				echo "<tr><td colspan=14 align=center>RUBRO : ".$wrub2."</td></tr>";
			if($wrub3 != "Seleccione")
				echo "<tr><td colspan=14 align=center>RUBRO : ".$wrub3."</td></tr>";
			$wdata=array();
			$wdatat=array();
			$wmeses=array();
			for ($i=$wper1;$i<=$wper2;$i++)
			{
				switch ($i)
					{
						case 1:
							$wmese[$i]="ENERO";
							break;
						case 2:
							$wmese[$i]="FEBRERO";
							break;
						case 3:
							$wmese[$i]="MARZO";
							break;
						case 4:
							$wmese[$i]="ABRIL";
							break;
						case 5:
							$wmese[$i]="MAYO";
							break;
						case 6:
							$wmese[$i]="JUNIO";
							break;
						case 7:
							$wmese[$i]="JULIO";
							break;
						case 8:
							$wmese[$i]="AGOSTO";
							break;
						case 9:
							$wmese[$i]="SEPTIEMBRE";
							break;
						case 10:
							$wmese[$i]="OCTUBRE";
							break;
						case 11:
							$wmese[$i]="NOVIEMBRE";
							break;
						case 12:
							$wmese[$i]="DICIEMBRE";
							break;
					}
			}
			echo "<tr><td><b>UNIDAD</b></td>";
			for ($i=$wper1;$i<=$wper2;$i++)
				echo "<td align=right><b>".$wmese[$i]."</b></td>";
			echo "<td align=right><b>TOTAL</b></td></tr>";
			
			for ($i=0;$i<14;$i++)
			{
				$wdata[$i]=0;
				$wdatat[$i]=0;
			}
			$wcco = "";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wcco != $row[0])
				{
					if ($wcco != "")
					{
						echo"<tr><td>".$wcco."-".$wcconom."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
						{
							echo "<td align=right>".number_format((double)$wdata[$j],0,'.',',')."</td>";
						}
						echo "<td align=right>".number_format((double)$wdata[13],0,'.',',')."</td></tr>";
					}
					$wcco=$row[0];
					$wcconom=$row[1];
					for ($j=0;$j<14;$j++)
						$wdata[$j]=0;
				}
				$wdata[$row[2]] += $row[3];
				$wdata[13] += $row[3];
				$wdatat[$row[2]] += $row[3];
				$wdatat[13] += $row[3];
			}
			echo"<tr><td>".$wcco."-".$wcconom."</td>";
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				echo "<td align=right>".number_format((double)$wdata[$j],0,'.',',')."</td>";
			}
			echo "<td align=right>".number_format((double)$wdata[13],0,'.',',')."</td></tr>";
			echo"<tr><td>TOTAL GENERAL</td>";
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				echo "<td align=right>".number_format((double)$wdatat[$j],0,'.',',')."</td>";
			}
			echo "<td align=right>".number_format((double)$wdatat[13],0,'.',',')."</td></tr>";
			echo "</table>";
		}
	}
?>
</body>
</html>
