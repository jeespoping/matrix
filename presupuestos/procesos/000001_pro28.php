<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo de Resultados en Cuentas de Participacion</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro28.php Ver. 2017-01-13</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro28.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1)  or !isset($wper2) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DE RESULTADOS EN CUENTAS DE PARTICIPACION</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
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
			$wemp = substr($wemp,0,2);
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$query = $query."    and emp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
			$query = "delete from ".$empresa."_000026 ";
			$query = $query."  where mecano = ".$wanop;
			$query = $query."    and mecmes between ".$wper1." and ".$wper2;
			$query = $query."    and mecemp = '".$wemp."' ";
			$query = $query."    and meccpr = '750'";
			$err = mysql_query($query,$conex);
			$query = "SELECT mecmes,cupcco,meccpr,cuppor,sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000057 ";
			$query = $query."  where mecano = ".$wanop;
			$query = $query."    and mecmes between ".$wper1." and ".$wper2;
			$query = $query."    and mecemp = '".$wemp."' ";
			$query = $query."    and mecemp = cupemp";
			$query = $query."    and meccco = cupcco";
			$query = $query."   group by mecmes,cupcco,meccpr,cuppor";
			$query = $query."   order by mecmes,cupcco,meccpr,cuppor";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$wtotal=array();
			$wmesant=0;
			$wccoant="0";
			$wporant=0;
			$count=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[0] != $wmesant or $row[1] != $wccoant)
				{
					if($wmesant != 0)
					{
						$wtotal[10] = $wtotal[1] - $wtotal[2] - $wtotal[3] + $wtotal[4] - $wtotal[5] - $wtotal[6] + $wtotal[7] - $wtotal[8];
						$wtotal[10]=$wtotal[10]*($wporant/100);
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
        				$query = "insert ".$empresa."_000026 (medico,fecha_data,hora_data,mecemp,meccco,meccpr,mecano,mecmes,meccue,mecval,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$wccoant."','750',".$wanop.",".$wmesant.",'750',".$wtotal[10].",'C-".$empresa."')";
						$err1 = mysql_query($query,$conex);
						$count++;
					}
					$wmesant = $row[0];
					$wccoant = $row[1];
					$wporant = $row[3];
					for ($j=0;$j<9;$j++)
						$wtotal[$j]=0;
				}
				$it=(integer)substr($row[2],0,1);
				if($it != 7)
					$wtotal[$it]=$wtotal[$it]+$row[4];
                 else
                 	if($row[0] == "700")
                 		$wtotal[7]=$wtotal[7]+$row[4];

    		}
    		$wtotal[10] = $wtotal[1] - $wtotal[2] - $wtotal[3] + $wtotal[4] - $wtotal[5] - $wtotal[6] + $wtotal[7] - $wtotal[8];
			$wtotal[10]=$wtotal[10]*($row[3]/100);
			$wtotal[10]=(integer)round($wtotal[10]);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
        	$query = "insert ".$empresa."_000026 (medico,fecha_data,hora_data,mecemp,meccco,meccpr,mecano,mecmes,meccue,mecval,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$wccoant."','750',".$wanop.",".$wmesant.",'750',".$wtotal[10].",'C-".$empresa."')";
			$err1 = mysql_query($query,$conex);
			$count++;
			echo "REGISTROS INSERTADOS : ".$count;
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
