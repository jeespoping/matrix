<html><head><title>MATRIX</title></head><body BGCOLOR=""><BODY TEXT="#000066"><center><table border=0 align=center><tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Opciones de Matrix Por Usuario</font></a></tr></td><tr><td align=center bgcolor="#cccccc"><font size=2> <b> optxusr.php ver 2010-04-21</b></font></tr></td></table></center><?php
include_once("conex.php"); session_start(); if(!isset($_SESSION['user'])) echo "error"; else { 	$key = substr($user,2,strlen($user));	
	
	echo "<form action='optxusr.php' method=post>";	if(!isset($wuser))	{		echo "<center><table border=0>";		echo "<tr><td colspan=2 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";		echo "<tr><td colspan=2 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";		echo "<tr><td colspan=2 align=center><b>Opciones de Matrix</b></td></tr>";		echo "<tr><td colspan=2 align=center><b>Por Usuario</b></td></tr>";		echo "<tr><td bgcolor=#cccccc align=center>Usuario</td><td bgcolor=#cccccc align=center>";		$query = "SELECT codigo,descripcion from usuarios where grupo = 'AMERICAS' and Activo='A' order by codigo";		$err = mysql_query($query,$conex);		$num = mysql_num_rows($err);		if ($num>0)		{			echo "<select name='wuser'>";			for ($i=0;$i<$num;$i++)			{				$row = mysql_fetch_array($err);				echo "<option>".$row[0]."-".$row[1]."</option>";			}			echo "</select>";		}		echo "</td></tr>";		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";	}	else	{		$query  = "select cast(root_000021.Codgru as UNSIGNED),root_000020.Descripcion,cast(root_000021.Codopt as UNSIGNED),root_000021.Descripcion from root_000021,root_000020 ";		$query .= " where root_000021.usuarios like '%".substr($wuser,0,strpos($wuser,"-"))."%' ";		$query .= "   and root_000021.codgru = root_000020.codigo order by 1,3";		$err = mysql_query($query,$conex);		$num = mysql_num_rows($err);		echo "<table border=0>";		echo "<tr><td colspan=4 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";		echo "<tr><td colspan=4 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";		echo "<tr><td colspan=4 align=center><b>Opciones de Matrix</b></td></tr>";		echo "<tr><td colspan=4 align=center><b>Por Usuario</b></td></tr>";		echo "<tr>";		echo "<td bgcolor=#cccccc><b>Grupo</b></td>";		echo "<td bgcolor=#cccccc><b>Nombre Grupo</b></td>";		echo "<td bgcolor=#cccccc><b>Opcion</b></td>";		echo "<td bgcolor=#cccccc><b>Nombre Opcion</b></td>";		echo "</tr>"; 		for ($i=0;$i<$num;$i++)		{			$row = mysql_fetch_array($err);			if($i % 2 == 0)				$tipo="#99CCFF";			else				$tipo="#FFFFFF";			echo "<tr>";			echo "<td bgcolor=".$tipo.">".$row[0]."</td>";			echo "<td bgcolor=".$tipo.">".$row[1]."</td>";			echo "<td bgcolor=".$tipo.">".$row[2]."</td>";			echo "<td bgcolor=".$tipo.">".$row[3]."</td>";			echo "</tr>"; 		}		$tipo="#CCCCCC";		echo "<tr>";		echo "<td bgcolor=".$tipo." colspan=2><b>NUMERO TOTAL DE OPCIONES</b></td>";		echo "<td bgcolor=".$tipo." colspan=2><b>".$num."</b></td>";		echo "</tr>"; 		echo "</table>"; 	}}?></body></html>