<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Mensaje HL7 Mal Integrados</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> BAD_INTEGRADOS.php Ver. 2010-03-10</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='BAD_INTEGRADOS.php' method=post>";
		if(!isset($wper1) or !isset($wper2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE MENSAJE HL7 MAL INTEGRADOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=10 maxlength=10></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=10 maxlength=10></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=3><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
			echo "<tr><td align=center colspan=3><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
			echo "<tr><td align=center colspan=3><font size=2>INFORME DE MENSAJE HL7 MAL INTEGRADOS</font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=4><font size=2><b>DESDE : ".$wper1." HASTA : ".$wper2."</b></font></td></tr>";
			echo "<tr><td bgcolor=#CCCCCC align=center><b>FECHA</b></td><td bgcolor=#CCCCCC align=center><b>NUMERO</b></td><td bgcolor=#CCCCCC align=center><b>TEXTO</b></td></tr>";
			//                    0        1       2      
			$query = "select Fecha_data, Numero, Texto  from agfa_000007  ";
			$query .= " where Fecha_data between '".$wper1."' and  '".$wper2."'";
            $query .= "   and Tipo = 'ORU' ";
            $query .= "  ORDER by 2 ";
 			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$tot=0;
			$totbad=0;
			$DATA=array();
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$DATA[$i][0]=$row[0];
				$DATA[$i][1]=$row[1];
				$DATA[$i][2]=$row[2];
			}
			echo "TERMINO QUERY<br>";
			for ($i=0;$i<$num;$i++)
			{
				$tot++;
				$wsw=0;
				for ($k=0;$k<strlen($DATA[$i][2]);$k++)
				{
					$caracter=ord(substr($DATA[$i][2],$k,1));
					if($caracter < 32 and $caracter != 9 and $caracter != 10 and $caracter != 13)
					{
						$wsw++;
						if($wsw > 10)
							break;
					}
				}
				if($wsw > 10)
				{
					$totbad++;
					if($totbad % 2 == 0)
						$color="#99CCFF";
					else
						$color="#ffffff";
					echo "<tr><td bgcolor=".$color."><font size=2>".$DATA[$i][0]."</font></td><td bgcolor=".$color."><font size=2>".$DATA[$i][1]."</font></td><td bgcolor=".$color."><font size=2>".$DATA[$i][2]."</font></td></td></tr>";
				}
    		}
    		echo "<tr><td bgcolor=#999999><font size=2><b>TOTALES</b></td><td bgcolor=#999999><font size=2><b>BUENOS : ".number_format((double)$tot,0,'.',',')."</b></font></td><td bgcolor=#999999><font size=2><b>MALOS : ".number_format((double)$totbad,0,'.',',')."</b></font></td></tr>";
    		echo "</table></center>";
		}
	}
?>
</body>
</html>
