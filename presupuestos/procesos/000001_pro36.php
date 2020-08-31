<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Costos Operacionales (IF)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro36.php Ver. 2015-09-25</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro36.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesp) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE COSTOS OPERACIONALES </td></tr>";
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
			$query = "DELETE from ".$empresa."_000037 where orumes = ".$wmesp;
    		$query = $query."   and oruano = ".$wanop;
    		$query = $query."   and oruemp = '".$wemp."' ";
    		$query = $query."   and orucod =  'CO' ";
			$err = mysql_query($query,$conex);
			$query = "SELECT ccocod from ".$empresa."_000005 where ccoemp = '".$wemp."' ";
   			#$query =$query." WHERE ccoclas != 'PR'";
   			#$query =$query."   AND ccoclas != 'OGI'";
   			#$query =$query."   AND ccoclas != 'NO'";
   			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$k=0;
   			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);               		
               		$query = "SELECT sum(mecval) as V4 from ".$empresa."_000026,".$empresa."_000028 ";
            		$query = $query." where mecmes = ".$wmesp;
            		$query = $query."   and mecano = ".$wanop;
            		$query = $query."   and mecemp = '".$wemp."' ";
           			$query = $query."   and meccco = '".$row[0]."'";
            		$query = $query."   and meccpr >='200'";
            		$query = $query."   and meccpr <='299'";
            		$query = $query."   and meccpr = mgacod";
            		$query = $query."   and mgatip != '1'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if(is_numeric($num1))
					{
						$row1 = mysql_fetch_array($err1);
						if(is_numeric( $row1[0]))
							$wmon = $row1[0];
						else
							$wmon = 0;
					}
           			Else
               			$wmon = 0;
               		$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$k++;
               		$query = "insert ".$empresa."_000037 (medico,fecha_data,hora_data,oruemp,orucco,oruano,orumes,orucod,orumon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$wmesp.",'CO',".$wmon.",'C-".$empresa."')";
               		$err2 = mysql_query($query,$conex) OR DIE ("ERROR EN INSERCION");
               		 echo "NUMERO DE REGISTROS INSERTADOS : ".$k."<br>";
        		}
        		 echo "<B>NUMERO TOTAL DE REGISTROS INSERTADOS : ".$k."</B><br>";
        	}
        }
}		
?>
</body>
</html>
