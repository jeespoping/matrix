<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Prestaciones Sociales Reales</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro13.php Ver. 2016-09-30</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro13.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesp) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE PRESTACIONES SOCIALES REALES</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesp' size=2 maxlength=2></td></tr>";
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
			$query = "SELECT norano,norper,norcco from ".$empresa."_000036 ";
  			$query = $query." WHERE norano = ".$wanop;
  			$query = $query."   and norper = ".$wmesp;
  			$query = $query."   and norfil = '".$wemp."' ";
   			$query = $query." GROUP BY norano,norper,norcco ";
   			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$k=0;
   			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
   					$row = mysql_fetch_array($err);
   					$query = "SELECT sum(mecval) as F1 from ".$empresa."_000026,".$empresa."_000024 ";
   					$query = $query." where mecano = ".$row[0];
            		$query = $query."   and mecmes = ".$row[1];
            		$query = $query."   and mecemp = '".$wemp."' ";
            		$query = $query."   and meccco = '".$row[2]."'";
            		$query = $query."   and meccue = mcucue ";
            		$query = $query."   and mecemp = mcuemp ";
           			$query = $query."   and mcutip = '1' ";
           			$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$WF1 = $row1[0];
					}
           			Else
               			$WF1 = 0;
               	    $query = "SELECT sum(normon) as B from ".$empresa."_000036,".$empresa."_000008 ";
               	    $query = $query." where norano = ".$row[0];
            		$query = $query."   and norper = ".$row[1];
            		$query = $query."   and norfil = '".$wemp."' ";
            		$query = $query."   and norcco = '".$row[2]."'";
            		$query = $query."   and norcod = concod ";
            		$query = $query."   and norfil = conemp ";
            		$query = $query."   and (contip = 'B' ";
            		$query = $query."   or contip = 'R') ";
            		$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
               			 $WB = $row1[0];
               		}
           			 Else
                		$WB = 0;
                	if($WB != 0)
                	  $WFP = ($WF1 / $WB) - 1;
           			Else
                	  $WFP = 0;
                	$query = "SELECT sum(normon) as B from ".$empresa."_000036,".$empresa."_000008 ";
               	    $query = $query." where norano = ".$row[0];
            		$query = $query."   and norper = ".$row[1];
            		$query = $query."   and norfil = '".$wemp."' ";
            		$query = $query."   and norcco = '".$row[2]."'";
            		$query = $query."   and norcod = concod ";
            		$query = $query."   and norfil = conemp ";
            		$query = $query."   and contip = 'B' ";
            		$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
               			 $WB = $row1[0];
               		}
           			 Else
                		$WB = 0;
                	$query = "SELECT sum(normon) as R from ".$empresa."_000036,".$empresa."_000008 ";
               	    $query = $query." where norano = ".$row[0];
            		$query = $query."   and norper = ".$row[1];
            		$query = $query."   and norfil = '".$wemp."' ";
            		$query = $query."   and norcco = '".$row[2]."'";
            		$query = $query."   and norcod = concod ";
            		$query = $query."   and norfil = conemp ";
            		$query = $query."   and contip = 'R' ";
            		$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
               			 $WR = $row1[0];
               		}
           			 Else
                		$WR = 0;
                	if($WB != 0)
                	  $WFR = $WR / $WB;
           			Else
                	  $WFR = 0;
                	$k++;
                	$query = "update ".$empresa."_000036 set norpre=".$WFP.", norrec=".$WFR." where norfil='".$wemp."' and norano=".$row[0]." and norper=".$row[1]." and norcco='".$row[2]."'";
					$err1 = mysql_query($query,$conex);
        		}
        		 echo "NUMERO DE REGISTROS ACTUALIZADOS : ".$k."<br>";
        	}
        	else
				 echo "NUMERO DE REGISTROS ACTUALIZADOS : ".$k."<br>";
        }
}		
?>
</body>
</html>
