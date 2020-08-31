<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Codificacion de Movimiento Contable Real</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro7.php Ver. 2015-09-11</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro7.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wmesp) or !isset($wmesp) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center>CODIFICACION MOVIMIENTO CONTABLE REAL</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
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
			$count=0;
			$query = "SELECT * from ".$empresa."_000027 ";
			$query = $query." where mepind = '0' ";
			$query = $query."   and mepano = ".$wanop;
			$query = $query."   and mepmes = ".$wmesp;
			$query = $query."   and mepemp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$actual=0;
        			$query = "SELECT rcpcup,rcpccd from ".$empresa."_000042 ";
        			$query = $query." where rcpcuc = '".$row[6]."'";
       			    $query = $query. "  and rcpcco = '".$row[7]."'";
       			    $query = $query."   and rcpemp = '".$wemp."' ";
        			$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if ($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$ini=strpos($row1[0],"-");
           				$wcodpre =substr($row1[0],0,$ini);
                		$wpot = 0;
						switch (substr($row[6], 0, 1))
						{
    						Case "4":
        						If ($row[8] == "1" Or $row[8] == "3")
        							$wpot = 1;
        						break;
    						Case "5":
       		 					If ($row[8] == "2" Or $row[8] == "4")
        							$wpot = 1;
        						break;
   		 					Case "6":
       					 		If ($row[8] == "2" Or $row[8] == "3")
        							$wpot = 1;
        						break;
        				}
        				$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$valor=$row[9]*(pow((-1),$wpot));
						if($empresa == "costosyp")
						{
							if($row1[1] == "1051")
								$row1[1]="1050";
							if($row1[1] == "3160")
								$row1[1]="1135";
							if($row1[1] == "1251")
								$row1[1]="1135";
							if($row1[1] == "1750")
								$row1[1]="2240";
						}
        				$query = "insert ".$empresa."_000026 (medico,fecha_data,hora_data,mecemp,meccco,meccpr,mecano,mecmes,meccue,mecval,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row1[1]."','".$row1[0]."',".$row[4].",".$row[5].",'".$row[6]."',".$valor.",'C-".$empresa."')";
						$err1 = mysql_query($query,$conex);
       					$query = "update ".$empresa."_000027 set mepind='1' where id=".$row[12];
						$err1 = mysql_query($query,$conex);
						$count++;
					}
                }
				$query = "SELECT mepmes,mepcue,mepval,mepcco,mepnat from ".$empresa."_000027 ";
				$query = $query." where mepind = '0' ";
				$query = $query."   and mepano = ".$wanop;
				$query = $query."   and mepmes = ".$wmesp;
				$query = $query."   and mepemp = '".$wemp."' ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 > 0)
				{
    				$wnroerr = 0;
    				echo "<center><table border=1>";
					echo "<tr><td align=center colspan=6><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center  colspan=6>APLICACION DE PRESUPUESTOS</td></tr>";
					echo "<tr><td align=center  colspan=6>ERRORES GENERACION DE MOVIMIENTO PRESUPUESTAL</td></tr>";
					echo "<tr><td align=center  colspan=6>A&Ntilde;O : ".$wanop." MES : ".$wmesp."</td></tr>";
					echo "<tr><td>MES</td><td>CUENTA</td><td>DESCRIPCION</td><td>CENTRO DE COSTOS</td><td>NATURALEZA</td><td>VALOR</td></tr>";
   					 for ($i=0;$i<$num1;$i++)
   					 {
	   					 $row1 = mysql_fetch_array($err1);
       					 $wnroerr = $wnroerr + 1;
       					 echo "<tr>";
       					 echo "<td>".$row1[0]."</td>";
       					 echo "<td>".$row1[1]."</td>";
       					 $query = "SELECT mcunom from ".$empresa."_000024 ";
						 $query = $query." where mcucue = '".$row1[1]."'";
						 $query = $query."   and mcuemp = '".$wemp."' ";
						 $err2 = mysql_query($query,$conex);
						 $num2 = mysql_num_rows($err2);
						 if ($num2 == 0)
       						 echo "<td><B>CUENTA CONTABLE NO!! EXISTE</B></td>";
       					else
       					{
	       					$row2 = mysql_fetch_array($err2);
       						echo "<td>".$row2[0]."</td>";
   						}
       					 echo "<td align=center>".$row1[3]."</td>";
       					 echo "<td align=center>".$row1[4]."</td>";
       					 echo "<td align=right>".number_format($row1[2],2,'.',',')."</td></tr>";
      				 }
      				 echo "<tr><td bgcolor=#cccccc colspan=6><b>REGISTROS INSERTADOS : ".$count."</b></td></tr>";
   					echo "<tr><td bgcolor=#cccccc colspan=6><b>NUMERO DE REGISTROS INCONSISTENTES : ".number_format($wnroerr,0,'.',',')."</b></td></tr></table>";
				}
		}
		else
			echo "ALERTA !!! ptomec SIN REGISTROS EN EL A&Ntilde;O :".$wanop." MES :".$wmesp;
	}
}
?>
</body>
</html>
