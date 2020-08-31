<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Distribucion de Recursos a los Subprocesos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc142.php Ver. 2016-03-22</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function bi($d,$n,$k,$i)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			//echo " Medio : ".$lm." valor: ".$d[$lm][$i]."<br>";
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][$i]),20);
			//if(strtoupper($k) == strtoupper($d[$lm][$i]))
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
			//echo $k." ".$d[$li][$i]." ".$d[$ls][$i]." ".$d[$lm][$i]." ".$li." ".$ls." ".$lm."<br>";
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc142.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or $wper1 < 1 or $wper1 > 12 or !isset($wcco1) or !isset($wcco2) or !isset($wgasi) or ($wgasi == "0-SELECCIONE" and $wgasin == 0) or !isset($wgasf) or ($wgasf == "0-SELECCIONE" and $wgasfn == 0))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE DISTRIBUCION DE RECURSOS A LOS SUBPROCESOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro Costos Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro Costos Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Gasto Inicial</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wgasin' size=3 maxlength=10 value=0>";
			$query = "SELECT Cogcod, Cognom  from ".$empresa."_000079 order by Cogcod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wgasi'>";
				echo "<option>0-SELECCIONE</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Gasto Final</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wgasfn' size=3 maxlength=10 value=0>";
			$query = "SELECT Cogcod, Cognom  from ".$empresa."_000079 order by Cogcod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wgasf'>";
				echo "<option>0-SELECCIONE</option>";
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
			$wanopa=$wanop - 1;
			if($wgasin==0)
				$wgasi=substr($wgasi,0,strpos($wgasi,"-"));
			else
				$wgasi=$wgasin;
			if($wgasfn==0)
				$wgasf=substr($wgasf,0,strpos($wgasf,"-"));
			else
				$wgasf=$wgasfn;
			$dins=array();
			$query = "SELECT  Inscod, Insdes from ".$empresa."_000089 ";
			$query .= "  where Insemp = '".$wemp."'";
			$query .= "     ORDER BY  Inscod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$totins=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$dins[$i][0]=$row[0];
				$dins[$i][1]=$row[1];
			}
			$dnit=array();
			$query = "select Expnit,Expnte from ".$empresa."_000011 ";
		    $query .= "  where expano between ".$wanopa." and ".$wanop;
			$query .= "    and expemp = '".$wemp."'"; 
			$query .= "   group by expnit ";
			$query .= "   order by expnit ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$totnit=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$dnit[$i][0]=$row[0];
				$dnit[$i][1]=$row[1];
			}
			$dind=array();
			$query = "select Cogcod, Cognom  from ".$empresa."_000079 ";
			$query .= "   order by Cogcod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$totind=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$dind[$i][0]=$row[0];
				$dind[$i][1]=$row[1];
			}
			$dotr=array();
			$query = "select Almcod, Almdes from ".$empresa."_000002 ";
		    $query .= "  where Almano between ".$wanopa." and ".$wanop;
			$query .= "    and Almemp = '".$wemp."'"; 
			$query .= "   group by Almcod ";
			$query .= "   order by Almcod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$tototr=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$dotr[$i][0]=$row[0];
				$dotr[$i][1]=$row[1];
			}
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=10><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
			echo "<tr><td align=center colspan=10><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
			echo "<tr><td align=center colspan=10><font size=2>INFORME DE DISTRIBUCION DE RECURSOS A LOS SUBPROCESOS</font></td></tr>";
			echo "<tr><td align=center colspan=10><font size=2>EMPRESA : ".$wempt."</font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=2><b>A&Ntilde;O : ".$wanop." MES : ".$wper1."</b></font></td></tr>";
			echo "<tr><td bgcolor=#CCCCCC align=center><b>CENTRO DE <BR>COSTOS</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>GASTO</b></td><td bgcolor=#CCCCCC align=center><b>NOMBRE DEL<BR>GASTO</b></td><td bgcolor=#CCCCCC align=center><b>SUBGASTO</b></td><td bgcolor=#CCCCCC align=center><b>NOMBRE DEL<BR>SUBGASTO</b></td><td bgcolor=#CCCCCC align=center><b>TIPO</b></td><td bgcolor=#CCCCCC align=center><b>DRIVER/SUBP</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>PORCENTAJE</b></td></tr>";
			//                  0      1      2      3       4      5      6       7  
			$query  = "select Rcdcco,Cconom,Rcdgas, Rcdsga,Rcdtip,Rcddri ,Drides,Rcdpor from ".$empresa."_000101,".$empresa."_000005,".$empresa."_000085 ";
			$query .= " where rcdano = ".$wanop;
			$query .= "   and rcdemp = '".$wemp."'"; 
		  	$query .= "   and rcdmes = ".$wper1; 
		  	$query .= "   and rcdcco between '".$wcco1."' and '".$wcco2."'"; 
		  	$query .= "   and rcdgas between '".$wgasi."' and '".$wgasf."'"; 
		  	$query .= "   and rcdcco = ccocod "; 
		  	$query .= "   and rcdemp = ccoemp "; 
		  	$query .= "   and rcddri = dricod "; 
		  	$query .= "   and Rcdtip = 'D' "; 
			$query .= " Group by 1,3,4,5,6 "; 
	  		$query .= " UNION ALL  "; 
	  		$query .= " select Rcdcco,Cconom,Rcdgas,Rcdsga,Rcdtip,Rcddri ,Subdes,Rcdpor from ".$empresa."_000101,".$empresa."_000005,".$empresa."_000104 "; 
			$query .= " where rcdano = ".$wanop;
			$query .= "   and rcdemp = '".$wemp."'"; 
		  	$query .= "   and rcdmes = ".$wper1;  
		  	$query .= "   and rcdcco between '".$wcco1."' and '".$wcco2."'"; 
		  	$query .= "   and rcdgas between '".$wgasi."' and '".$wgasf."'"; 
		  	$query .= "   and rcdcco = ccocod "; 
		  	$query .= "   and rcdemp = ccoemp "; 
		  	$query .= "   and rcddri = Subcod "; 
		  	$query .= "   and Rcdtip = 'S' "; 
			$query .= " Group by 1,3,4,5,6 "; 
			$query .= " order by 1,3,4 "; 
 			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$color="#99CCFF";
				else
					$color="#ffffff";
				$row = mysql_fetch_array($err);
				$pos=bi($dind,$totind,$row[2],0);
				if($pos == -1)
					$nomgas="CODIGO DEL GASTO NO DETERMINADO!!!";
				else
					$nomgas=$dind[$pos][1];
						
				$pos=bi($dotr,$tototr,$row[3],0);
				if($pos == -1)
				{
					$pos=bi($dnit,$totnit,$row[3],0);
					if($pos == -1)
					{
						$pos=bi($dind,$totind,$row[3],0);
						if($pos == -1)
						{
							$pos=bi($dins,$totins,$row[3],0);
							if($pos == -1)
								$nomsubg="CODIGO DEL SUBGASTO NO DETERMINADO!!!";
							else
								$nomsubg=$dins[$pos][1];
						}
						else
							$nomsubg=$dind[$pos][1];	
					}
					else
						$nomsubg=$dnit[$pos][1];
				}
				else
					$nomsubg=$dotr[$pos][1]; 
				echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color."><font size=2>".$row[2]."</font></td><td bgcolor=".$color."><font size=2>".$nomgas."</font></td><td bgcolor=".$color."><font size=2>".$row[3]."</font></td><td bgcolor=".$color."><font size=2>".$nomsubg."</font></td><td bgcolor=".$color."><font size=2>".$row[4]."</font></td><td bgcolor=".$color."><font size=2>".$row[5]."</font></td><td bgcolor=".$color."><font size=2>".$row[6]."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[7],2,'.',',')."</font></td></tr>";
    		}
    		echo "</table></center>";
		}
	}
?>
</body>
</html>
