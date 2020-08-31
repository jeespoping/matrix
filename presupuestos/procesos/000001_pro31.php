<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Grabacion Indirectos Reales Distribuidos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro31.php Ver. 2015-11-17</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro31.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>GRABACION INDIRECTOS REALES DISTRIBUIDOS</td></tr>";
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
			$query = $query."    and Emp = '".$wemp."' ";
			$query = $query."    and mes =  ".$wper1;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$query = "UPDATE  root_000019 set ano=".$wanop." , mes=".$wper1." where codigo= '1' ";
			$err1 = mysql_query($query,$conex);
			if($num > 0 and $row[0] == "off")
			{
			$query = "SELECT mirano,mirmes,mircco,mircue,mircpr from ".$empresa."_000058  ";
			$query = $query."  where mirano = ".$wanop;
			$query = $query."    and miremp = '".$wemp."' ";
			$query = $query."    and mirmes = ".$wper1;
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($err1);
					$query = "delete from ".$empresa."_000026 ";
					$query = $query."  where mecano = ".$row1[0];
					$query = $query."    and mecemp = '".$wemp."' ";
					$query = $query."    and mecmes = ".$row1[1];
					$query = $query."    and meccco = '".$row1[2]."'";
					$query = $query."    and meccue = '".$row1[3]."'";
					$query = $query."    and meccpr = '".$row1[4]."'";
					$err2 = mysql_query($query,$conex);
				}
			}
			$query = "delete from ".$empresa."_000026 ";
			$query = $query."  where mecano = ".$wanop;
			$query = $query."    and mecemp = '".$wemp."' ";
			$query = $query."    and mecmes = ".$wper1;
			$query = $query."    and meccue = '99999998'";
			$err2 = mysql_query($query,$conex);
			$count=0;
			$query = "select Mdiano,Mdimes,Mdicco,Midcpr,sum(Mdimon) ";
			$query = $query."from ".$empresa."_000054,".$empresa."_000050,".$empresa."_000005 ";
			$query = $query." where mdiano = ".$wanop;
			$query = $query."   and mdiemp = '".$wemp."' ";
			$query = $query."   and mdimes = ".$wper1;
			$query = $query."   and mditip = 'R'";
			$query = $query."   and mdiind = midcod  ";
			$query = $query."   and mdiemp = midemp  ";
			$query = $query."   and mdicco = ccocod ";
			$query = $query."   and midemp = ccoemp ";
			$query = $query."   and ccoclas != 'IND'  ";
			$query = $query." group by mdiano,mdimes,mdicco,midcpr";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000026 (medico,fecha_data,hora_data,mecemp,meccco,meccpr,mecano,mecmes,meccue,mecval,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[2]."','".$row[3]."',".$wanop.",".$wper1.",'99999998',".$row[4].",'C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				$count++;
    		}
			echo "REGISTROS INSERTADOS UNIDADES NO INDEPENDIENTES : ".$count."<br>";
			$count=0;
			$query = "select Mdiano,Mdimes,Mdicco,sum(Mdimon) ";
			$query = $query."from ".$empresa."_000054,".$empresa."_000005 ";
			$query = $query." where mdiano = ".$wanop;
			$query = $query."   and mdiemp = '".$wemp."' ";
			$query = $query."   and mdimes = ".$wper1;
			$query = $query."   and mditip = 'R'";
			$query = $query."   and mdicco = ccocod ";
			$query = $query."   and mdiemp = ccoemp ";
			$query = $query."   and ccoclas = 'IND'  ";
			$query = $query." group by mdiano,mdimes,mdicco";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000026 (medico,fecha_data,hora_data,mecemp,meccco,meccpr,mecano,mecmes,meccue,mecval,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[2]."','516',".$wanop.",".$wper1.",'99999998',".$row[3].",'C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				$count++;
    		}
			echo "REGISTROS INSERTADOS UNIDADES INDEPENDIENTES : ".$count."<br>";
         }
         else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO  ESTA!!! CERRADO -- LLAME A COSTOS Y PRSUPUESTOS</MARQUEE></FONT>";
			echo "<br><br>";			
		}
		}
	}
?>
</body>
</html>
