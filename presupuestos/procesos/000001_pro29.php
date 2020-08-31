<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Seleccion Indirectos a Distribuir</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro29.php Ver. 2017-05-30</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro29.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>SELECCION INDIRECTOS A DISTRIBUIR</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
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
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = ".$wper1;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$wsw=0;
			$query = "SELECT ano,mes from root_000019  ";
			$query = $query."  where codigo = '1' ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$row1 = mysql_fetch_array($err1);
			if($row1[0] < $wanop or ($row1[0] == $wanop and $row1[1] < $wper1))
				$wsw=1;
			if($num > 0 and $row[0] == "off"  and $wsw == 1)
			{
				$count=0;
				$query = "delete from ".$empresa."_000058 ";
				$query = $query."  where mirano = ".$wanop;
				$query = $query."    and miremp = '".$wemp."' ";
				$query = $query."    and mirmes = ".$wper1;
				$err = mysql_query($query,$conex);
				$query = "SELECT mecano,mecmes,meccco,meccue,meccpr,midcod,rcicri,sum(mecval) from ".$empresa."_000026,".$empresa."_000050,".$empresa."_000052 ";
				$query = $query."  where mecano = ".$wanop;
				$query = $query."    and mecemp = '".$wemp."' ";
				$query = $query."    and mecmes = ".$wper1;
				$query = $query."    and meccue = midcue";
				$query = $query."    and meccco = midcco";
				$query = $query."    and mecemp = midemp";
				$query = $query."    and midcod = rciind";
				$query = $query."    and midemp = rciemp";
				$query = $query."   group by mecano,mecmes,meccco,meccue,meccpr,midcod,rcicri";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000058 (medico,fecha_data,hora_data,miremp,mirano,mirmes,mircco,mircue,mircpr,mircod,mircri,mirvai,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[2]."','".$row[3]."','".$row[4]."','".$row[5]."','".$row[6]."',".$row[7].",'C-".$empresa."')";
					$err1 = mysql_query($query,$conex);
					$count++;
				}
				echo "REGISTROS INSERTADOS : ".$count;
			}
         else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PROCESO DE GRABACION DE INDIRECTOS YA SE CORRIO NO!! PUEDE VOLVER A CORRER ESTE PROCESO O EL PERIODO ESTA CERRADO -- LLAME A COSTOS Y PRESUPUESTOS</MARQUEE></FONT>";
			echo "<br><br>";			
		}
		}
	}
?>
</body>
</html>
