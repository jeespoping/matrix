<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Generacion Drivers Talento Humano</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro54.php Ver. 2018-05-16</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro54.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wcco1)  or !isset($wcco2) or !isset($wdrit) or !isset($wdrie) or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION DRIVERS TALENTO HUMANO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Driver Personal Total</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wdrit' size=6 value='PERT' maxlength=6></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Driver Personal Enfermeria</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wdrie' size=6 value='PERE' maxlength=6></td></tr>";
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
		$wtote=0;
		$wtott=0;
		$query = "delete from ".$empresa."_000091  ";
		$query = $query."  where Mdrano =  ".$wanop;
		$query = $query."    and Mdremp = '".$wemp."'";
		$query = $query."    and Mdrmes =  ".$wper1;
		$query = $query."    and Mdrcco between '".$wcco1."' and '".$wcco2."'";
		$query = $query."    and (Mdrcod =  '".$wdrit."'";
		$query = $query."     or Mdrcod =  '".$wdrie."')";
		$err = mysql_query($query,$conex);
		$query = "SELECT  Mnocco, TRIM(Pdisub), TRIM(Pdisub), Mnoofi, Pdipor, Cartip, 1, sum(Mnothb) from ".$empresa."_000094,".$empresa."_000098,".$empresa."_000004  ";
		$query = $query."  where Mnoano =  ".$wanop;
		$query = $query."    and Mnoemp = '".$wemp."'";
		$query = $query."    and Mnomes =  ".$wper1;
		$query = $query."    and Mnoemp =  Pdiemp";
		$query = $query."    and Mnoano =  Pdiano";
		$query = $query."    and Mnomes =  Pdimes";
		$query = $query."    and Mnocco between '".$wcco1."' and '".$wcco2."'";
		$query = $query."    and Mnocco not in (select ciccco from ".$empresa."_000131 where Cicano=".$wanop." and Cicmes=".$wper1." and Cicemp='".$wemp."') ";
		$query = $query."    and Mnocco =  Pdicco";
		$query = $query."    and Mnoofi =  Pdiofi";
		$query = $query."    and Pditip = 'S' ";
		$query = $query."    and Mnoofi =  carcod";
		$query = $query."    and Mnoemp =  caremp";
		$query = $query."  group by 1,2,3,4,5,6,7 " ;
		$query = $query."  UNION ALL " ;
		$query = $query." SELECT  Mnocco, TRIM(Mdrsub) ,TRIM(Mdrsub), Mnoofi, Pdipor, Cartip, Mdrpor, sum(Mnothb) from costosyp_000094,costosyp_000098,costosyp_000091,costosyp_000004 ";
		$query = $query."  where Mnoano =  ".$wanop;
		$query = $query."  	and Mnoemp = '".$wemp."'";
		$query = $query."  	and Mnomes =  ".$wper1;
		$query = $query."  	and Mnoemp =  Pdiemp";
		$query = $query."  	and Mnoano =  Pdiano";
		$query = $query."  	and Mnomes =  Pdimes";
		$query = $query."  	and Mnocco between '".$wcco1."' and '".$wcco2."'";
		$query = $query."  	and Mnocco not in (select ciccco from costosyp_000131 where Cicano=".$wanop." and Cicmes=".$wper1." and Cicemp='".$wemp."') ";
		$query = $query."  	and Mnocco =  Pdicco";
		$query = $query."  	and Mnoofi =  Pdiofi";
		$query = $query."  	and Pditip = 'D' ";
		$query = $query."  	and Pdiemp = Mdremp";
		$query = $query."  	and Pdiano = Mdrano";
		$query = $query."  	and Pdimes = Mdrmes";
		$query = $query."  	and Pdicco = Mdrcco";
		$query = $query."  	and Pdisub = Mdrcod";
		$query = $query."  	and Mnoofi = carcod";
		$query = $query."  	and Mnoemp = caremp";
		$query = $query."  group by 1,2,3,4,5,6,7 ";
		$query = $query."  order by  1,2 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$Wkey="";
		$count=0;
		for ($i=0;$i<$num;$i++)
		{	
			$row = mysql_fetch_array($err);
			if($row[0].$row[2] != $Wkey)
			{
				if($i != 0)
				{
					if($wtote > 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000091 (medico,fecha_data,hora_data,Mdremp, Mdrano, Mdrmes, Mdrcco, Mdrsub, Mdrcod, Mdrval, Mdrpor  ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".substr($Wkey,0,4)."','".substr($Wkey,4)."','".$wdrie."',".$wtote.",0,'C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Driver1 ".mysql_errno().":".mysql_error());
						$count++;
						echo "REGISTROS INSERTADOS : ".$count."<BR>";
					}
					if($wtott > 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000091 (medico,fecha_data,hora_data,Mdremp, Mdrano, Mdrmes, Mdrcco, Mdrsub, Mdrcod, Mdrval, Mdrpor  ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".substr($Wkey,0,4)."','".substr($Wkey,4)."','".$wdrit."',".$wtott.",0,'C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Driver2 ".mysql_errno().":".mysql_error());
						$count++;
						echo "REGISTROS INSERTADOS : ".$count."<BR>";
					}
				}
				$Wkey=$row[0].$row[2];
				$wtote=0;
				$wtott=0;
			}
			if($row[5] == "1" or $row[5] == "2")
				$wtote+=$row[7]*$row[4]*$row[6];
			$wtott+=$row[7]*$row[4]*$row[6];
		}
		if($wtote > 0)
		{
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000091 (medico,fecha_data,hora_data,Mdremp, Mdrano, Mdrmes, Mdrcco, Mdrsub, Mdrcod, Mdrval, Mdrpor  ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".substr($Wkey,0,4)."','".substr($Wkey,4)."','".$wdrie."',".$wtote.",0,'C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Driver3 ".mysql_errno().":".mysql_error());
			$count++;
			echo "REGISTROS INSERTADOS : ".$count."<BR>";
		}
		if($wtott > 0)
		{
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000091 (medico,fecha_data,hora_data,Mdremp, Mdrano, Mdrmes, Mdrcco, Mdrsub, Mdrcod, Mdrval, Mdrpor  ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".substr($Wkey,0,4)."','".substr($Wkey,4)."','".$wdrit."',".$wtott.",0,'C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Driver4 ".mysql_errno().":".mysql_error());
			$count++;
			echo "REGISTROS INSERTADOS : ".$count."<BR>";
		}
		echo "<B>REGISTROS ADICIONADOS : ".$count."</B><BR>";
	}
}
?>
</body>
</html>
