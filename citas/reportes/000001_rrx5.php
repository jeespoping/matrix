<?php
include_once("conex.php");
echo "<html>";
echo "<head>";
echo "<title>PROGRAMACION DE QUIROFANOS</title>";
echo "</head>";
echo "<body BGCOLOR=''>";
echo "<BODY TEXT='#000066'>";
echo "<center>";
$wfec=date("Y-m-d");
echo "</center>";
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{		
	echo "<form action='000001_rrx5.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wequ))
	{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CITAS X EQUIPO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Equipo : </td>";
			echo "<td bgcolor=#cccccc>";
			echo "<select name='wequ'>";
			$query = "select codigo,descripcion from ".$empresa."_000003 order by codigo";
		   	$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($i=0;$i<$num1;$i++)
			{	
				$row1 = mysql_fetch_array($err1);		
				echo "<option>".$row1[0]."-".$row1[1]."</option>";
			}
			echo "</td></tr>";	
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
		{
			$ini = strpos($wequ,'-');
			$wtit=substr($wequ,$ini+1,strlen($wequ));
			$wequ1=substr($wequ,0,$ini);
			echo "<center>";
			echo "<A NAME='Arriba'><h1>Citas x Equipo :".$wtit."</h1></a>";
			echo "</center>";
			if (isset($Ano) and isset($Mes) and isset($Dia))
			{
				if ($Mes < "10")
					$Mes = "0".$Mes;
				if ($Dia < "10")
					$Dia = "0".$Dia;
				$wfec = $Ano."-".$Mes."-".$Dia; 
			}
			else
			{
				$wfec1 = (string)$wfec;
				$Ano = substr($wfec1,0,4);
				$Mes = substr($wfec1,5,2);
				$Dia = substr($wfec1,8,2);
			}
			echo "<table border=1 align=center cellpadding=6>";
			echo "<tr>";
			echo "<td bgcolor='#cccccc'><b>Fecha :</b></td>";
			echo "<td bgcolor='#cccccc'><font size=3><b>".$wfec."</b></font></td>";
			echo "<td bgcolor='#cccccc'><b>Año :</b><td bgcolor='#cccccc'>";
			echo "<select name='Ano'>";
			for ($i=2003;$i<2051;$i++)
			{
				if (isset($Ano) and $Ano == (string)$i)
				{
					echo "<option selected>".$i."</option>";
				}
				else
					echo "<option>".$i."</option>";
			}
			echo "</td><td bgcolor='#cccccc'><b>Mes :</b><td bgcolor='#cccccc'>";
			echo "<select name='Mes'>";
			for ($i=1;$i<13;$i++)
			{
				if (isset($Mes) and $Mes == (string)$i)
					echo "<option selected>".$i."</option>";
				else
					echo "<option>".$i."</option>";
			}
			echo "</td><td bgcolor='#cccccc'><b>Dia :</b><td bgcolor='#cccccc'>";
			echo "<select name='Dia'>";
			for ($i=1;$i<32;$i++)
			{
				if (isset($Dia) and $Dia == (string)$i)
					echo "<option selected>".$i."</option>";
				else
					echo "<option>".$i."</option>";
			}
			echo "<input type='HIDDEN' name= 'wfec' value='".$wfec."'>";
			echo "<input type='HIDDEN' name= 'wequ' value='".$wequ."'>";
			echo "<input type='HIDDEN' name= 'wtit' value='".$wtit."'>";
			echo "<td bgcolor='#cccccc'><input type='submit' value='IR'>";
			echo "</td></tr></table><br>";
			$query = "select fecha,equipo,uni_hora,hi,hf from ".$empresa."_000004 where fecha = '".$wfec."' and equipo = '".$wequ1."'";
			$err = mysql_query($query,$conex);	
			$num = mysql_num_rows($err);
			if ($num == 0)
			{
				$query = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$empresa."_000003 where codigo='".$wequ1."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$row = mysql_fetch_array($err);

			}
			else
			{
				$row = mysql_fetch_array($err);
			}
			$whi = $row[3];
			$wul = $row[4];
			$inc = $row[2];
			$part1 = (int)substr($whi,0,2);
			$part2 = (int)substr($whi,2,2);
			$part2 = $part2 + $inc;
			while ($part2 >= 60)
			{
				$part2 = $part2 - 60;
				$part1 = $part1 + 1;
			}
			$whf = (string)$part1.(string)$part2;
			if ($part1 < 10)
				$whf = "0".$whf;
			if ($part2 < 10)
				$whf = substr($whf,0,2)."0".substr($whf,2,1);	
			$color="#999999";
			echo "<table border=1 align=center>";
			echo "<tr><td align=center colspan=9><IMG SRC='/matrix/images/medical/Citas/logo_".$empresa.".png'></td></tr>";
			echo "<tr>";
			echo "<td bgcolor=".$color."><font size=2><b>Hora Inicio</b></font></td>";
  			echo "<td bgcolor=".$color."><font size=2><b>Medico</b></font></td>";
  			echo "<td bgcolor=".$color."><font size=2><b>Examen</b></font></td>";
  			echo "<td bgcolor=".$color."><font size=2><b>Paciente</b></font></td>";	
  			echo "<td bgcolor=".$color."><font size=2><b>Responsable</b></font></td>";	
  			echo "<td bgcolor=".$color."><font size=2><b>Telefono</b></font></td>";
  			echo "<td bgcolor=".$color."><font size=2><b>Edad</b></font></td>";
  			echo "<td bgcolor=".$color."><font size=2><b>Comentarios</b></font></td>";
  			echo "<td bgcolor=".$color."><font size=2><b>Usuario</b></font></td>";
			echo "</tr>";
			$r = 0;
			$i = 0;
			$fila = 0;
			$spaces = "";
			for ($i=0;$i<30;$i++)
			{
				$spaces = $spaces.".";
			}
			$query = "select  cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo from ".$empresa."_000001 where fecha='".$wfec."' and cod_equ='".$wequ1."' order by hi";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0)
			{
				$row = mysql_fetch_array($err);
			}
			while ($whi < $wul)
			{
				$r = $i/2;
				if ($r*2 === $i)
					$color="#CCCCCC";
				else
					$color="#999999";
				echo "<tr>";
				echo "<td bgcolor=".$color." align=center><font size=1>".substr($whi,0,2).":".substr($whi,2,2)."</font></td>";
				if ($num > 0 and $row[4] == $whi)
				{	
					$query = "select codigo,nombre,oficio,tipo,edad_pac,activo from ".$empresa."_000008 where codigo='".$row[0]."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$row1 = mysql_fetch_array($err1);		
					echo "<td bgcolor=".$color."><font size=1>".$row1[1]."</font></td>";
					$query = "select codigo,descripcion,preparacion,cod_equipo,activo,especial from ".$empresa."_000006 where codigo='".$row[2]."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$row2 = mysql_fetch_array($err1);
					echo "<td bgcolor=".$color."><font size=1>".$row2[1]."</font></td>";
					echo "<td bgcolor=".$color."><font size=1>".$row[6]."</font></td>";
					$query = "select nit,descripcion,activo from ".$empresa."_000002 where nit='".$row[7]."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$row3 = mysql_fetch_array($err1);
					echo "<td bgcolor=".$color."><font size=1>".$row3[1]."</font></td>";
					echo "<td bgcolor=".$color."><font size=1>".$row[8]."</font></td>";
					echo "<td bgcolor=".$color."><font size=1>".$row[9]."</font></td>";
					echo "<td bgcolor=".$color."><font size=1>".$row[10]."</font></td>";
					echo "<td bgcolor=".$color."><font size=1>".$row[11]."</font></td>";
					$row = mysql_fetch_array($err);
					$fila = $fila + 1;
				}
				else
				{
					echo "<td bgcolor=".$color.">".$spaces."</td>";
					echo "<td bgcolor=".$color.">".$spaces."</td>";
					echo "<td bgcolor=".$color.">".$spaces."</td>";
					echo "<td bgcolor=".$color.">".$spaces."</td>";
					echo "<td bgcolor=".$color.">-</td>";
					echo "<td bgcolor=".$color.">-</td>";
					echo "<td bgcolor=".$color.">-</td>";
					echo "<td bgcolor=".$color.">-</td>";
				}
				$whi = $whf;
				$part1 = (int)substr($whi,0,2);
				$part2 = (int)substr($whi,2,2);
				$part2 = $part2 + $inc;
				while ($part2 >= 60)
				{
					$part2 = $part2 - 60;
					$part1 = $part1 + 1;
				}
				$whf = (string)$part1.(string)$part2;
				if ($part1 < 10)
					$whf = "0".$whf;
				if ($part2 < 10)
					$whf = substr($whf,0,2)."0".substr($whf,2,1);
				$i = $i + 1;
			}
			echo "</table>";
			echo "<table border=0 align=center>";
			echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></table>";
			echo "</body>";
			echo "</html>";
			include_once("free.php");
	}
}
?>
