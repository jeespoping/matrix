<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Indicadores Para Una Unidad de Negocio</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc14.php Ver. 2016-03-04</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc14.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wgru) or (!isset($wcco1) and !isset($wccof)) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE INDICADORES PARA UNA UNIDAD DE NEGOCIO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Forma de Agrupacion</td>";
			echo "<td bgcolor=#cccccc align=center><select name='wgru'>";
			$query = "SELECT grucod ,grudes  from ".$empresa."_000013 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}	
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod order by Cc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wccof'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
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
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
			}
			$wanopa=$wanop - 1;
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = ".$wper1;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
			$wgru1=$wgru;
			$ini=strpos($wgru,"-");
			$wgru=substr($wgru,0,$ini);
			$wgru1=substr($wgru1,$ini+1,strlen($wgru1));
			if($wgru != "MENS")
				$wper1=13;
			$query = "SELECT cconom from ".$empresa."_000005  where ccocod = '".$wcco1."' and ccoemp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$unidad=$row[0];
			$query = "SELECT infcod,inddes,infmon from ".$empresa."_000016,".$empresa."_000015 ";
			$query = $query."  where infano = ".$wanopa;
			$query = $query."    and infemp = '".$wemp."' ";
			$query = $query."    and infmes = ".$wper1;
			$query = $query."    and infgru = '".$wgru."'";
			$query = $query."    and infcco = '".$wcco1."'";
			$query = $query."    and infcod = indcod";
			$query = $query."   order by infano,infmes,infcod";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT infcod,inddes,infmon from ".$empresa."_000016,".$empresa."_000015 ";
			$query = $query."  where infano = ".$wanop;
			$query = $query."    and infemp = '".$wemp."' ";
			$query = $query."    and infmes = ".$wper1;
			$query = $query."    and infgru = '".$wgru."'";
			$query = $query."    and infcco = '".$wcco1."'";
			$query = $query."    and infcod = indcod";
			$query = $query."   order by infano,infmes,infcod";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=4 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=4 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=4 align=center>INFORME DE INDICADORES PARA UNA UNIDAD DE NEGOCIO</td></tr>";
			echo "<tr><td colspan=4 align=center><font size=2>EMPRESA : ".$wempt."</font></td></tr>";
			echo "<tr><td colspan=4 align=center>UNIDAD  :<b> ".$unidad. "</b></td></tr>";
			echo "<tr><td colspan=4 align=center>AGRUPACION  :<b> ".$wgru1."</b> PERIODO  :<b> ".$wper1."</b> A&Ntilde;O :<b> ".$wanop."</b></td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>CODIGO</b></td><td bgcolor=#cccccc><b>NOMBRE INDICADOR</b></td><td align=right bgcolor=#cccccc><b>VALOR : ".$wanopa."</b></td><td align=right bgcolor=#cccccc><b>VALOR : ".$wanop."</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='zzz';
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$key1= $row1[0];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2='zzz';
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$key2= $row2[0];
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($key1 == $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row2[2];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="zzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0];
					}
					if($k2 > $num2)
						$key2="zzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0];
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=0;
					$k1++;
					if($k1 > $num1)
						$key1="zzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					$wdata[$num][2]=0;
					$wdata[$num][3]=$row2[2];
					$k2++;
					if($k2 > $num2)
						$key2="zzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0];
					}
				}
			}
			for ($i=0;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wdata[$i][0] == "RID" or $wdata[$i][0] == "RCA")
					$id="D";
				else
					$id="%";
				echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],2,'.',',')." ".$id."</td><td align=right>".number_format((double)$wdata[$i][3],2,'.',',')." ".$id."</td></tr>";
    		}
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO NO!! ESTA CERRADO -- LLAME A COSTOS Y PRSUPUESTOS</MARQUEE></FONT>";
			echo "<br><br>";			
		}
		}
	}
?>
</body>
</html>
