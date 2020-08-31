
<html>
<head>
<title>Reporte de tarjeta de puntos</title>
</head>
<body >
<font face='arial'>
<BODY TEXT="#000066">
<?php
include_once("conex.php");

/********************************************************
*     REPORTE DE TARJETA DE PUNTOS						*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte de tarjeta de puntos 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:MARZO 2006
//FECHA ULTIMA ACTUALIZACION 	:07 de Marzo de 2006
//DESCRIPCION					:
//								 
//==================================================================================================================================



session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$empresa='farstore';
	
	

	


	echo "<form action='' method=post>";

	if(!isset($num))
	{

		echo "<table border=0 align=center >";
		echo "<tr><td align=center><img SRC='/MATRIX/images/medical/pos/logo_".$empresa.".png'></td>";
		echo "<tr><td align=center><font size='5'><br>REPORTE DE TARJETA DE PUNTOS</td></tr>";
		echo "</table>";
		echo "<br>";
		
		echo "<table border=(1) align=center>";
		echo "<tr><td colspan=3 align=center><input type='TEXT' name='num' size=30 maxlength=30 ></td></tr>";
		echo "<tr><td><input type='radio' name='nume' value='ced'>Cedula&nbsp&nbsp&nbsp&nbsp&nbsp</td>
			<td><input type='radio' name='nume' value='tarpu'>Numero de tarjeta&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr>
			<tr><td><input type='radio' name='nume' value='nom'>Nombre&nbsp&nbsp&nbsp&nbsp&nbsp</td>
			<td><input type='radio' name='nume' value='tel'>Telefono&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr>
			<tr><td colspan=4><input type='radio' name='nume' value='all' checked>Todos los clientes&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr>";
		echo "<tr><td colspan=3 align=center><input type='submit' value='ENTER'></td></tr></table>";
		echo "</table>";
	}else
	
	{


		////////////////////////////////////////////////////////////////////////////////
		
		
		if ($nume=='ced')
		{
			$query = "SELECT Clidoc, Clinom, Climai, Clite1, Clipun, Salcau, Salred, Saldev, Salsal 
			FROM ".$empresa."_000041,".$empresa."_000060 
			WHERE Clidoc like'".$num."'
			and Saldto=Clidoc";
			$err = mysql_query($query,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numero = mysql_num_rows($err);
			//echo mysql_errno() ."=". mysql_error();
			
			
		}

		else if ($nume=='tarpu'){
			$query = "SELECT Clidoc, Clinom, Climai, Clite1, Clipun, Salcau, Salred, Saldev, Salsal 
			FROM ".$empresa."_000041,".$empresa."_000060 
			WHERE Clipun like'".$num."'
			and Saldto=Clidoc";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$numero = mysql_num_rows($err);
			//echo mysql_errno() ."=". mysql_error();
			
		}
		
		else if ($nume=='tel'){
			$query = "SELECT Clidoc, Clinom, Climai, Clite1, Clipun, Salcau, Salred, Saldev, Salsal 
			FROM ".$empresa."_000041,".$empresa."_000060 
			WHERE Clite1 like '".$num."'
			and Saldto=Clidoc";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$numero = mysql_num_rows($err);
			//echo mysql_errno() ."=". mysql_error();
		}
		
		else if ($nume=='nom'){
			$query = "SELECT Clidoc, Clinom, Climai, Clite1, Clipun, Salcau, Salred, Saldev, Salsal 
			FROM ".$empresa."_000041,".$empresa."_000060 
			WHERE Clinom like '".$num."'
			and Saldto=Clidoc";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$numero = mysql_num_rows($err);
			//echo mysql_errno() ."=". mysql_error();
			
			
		}
		
		///////////// IMPRESION ///////////////////////////////////////////////////////////
		
		echo "<table border=0 align=center >";
		echo "<tr><td align=center><img SRC='/MATRIX/images/medical/pos/logo_".$empresa.".png'></td>";
		echo "<tr><td align=center><font size='5'><br>REPORTE DE TARJETA DE PUNTOS</td></tr>";
		echo "</table>";
		echo "<br>";
		echo "<br>";
		echo "<table border=1 align=center >";
		echo "<tr><td bgcolor=#99CCFF><font size='4'>Cedula</td><td bgcolor=#99CCFF><font size='4'>Nombre</td><td bgcolor=#99CCFF><font size='4'>E-mail</td><td bgcolor=#99CCFF><font size='4'>Telefono</td><td bgcolor=#99CCFF><font size='4'>Numero de Tarjeta</td>
				  <td bgcolor=#99CCFF><font size='4'>Puntos Causados</td><td bgcolor=#99CCFF><font size='4'>Puntos Redimidos</td><td bgcolor=#99CCFF><font size='4'>Puntos Devueltos</td>
				  <td bgcolor=#99CCFF><font size='4'>Puntos Acumulados</td>";

		$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
 		$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
		
		
		if ($nume=='nom' or $nume=='ced'or $nume=='tarpu' or $nume=='tel'){
			
			for ($i=0;$i<$numero;$i++)
				{
					if (is_int ($i/2))
					$wcf="DDDDDD";
					else
					$wcf="CCFFFF";
					
					$datgr = mysql_fetch_row($err);
					echo "<tr bgcolor=".$wcf."><td align=left>$datgr[0]</td><td align=left>$datgr[1]</td><td align=left>$datgr[2]</td><td align=left>$datgr[3]</td><td align=left>$datgr[4]</td><td align=right>$datgr[5]</td><td align=right>$datgr[6]</td><td align=right>$datgr[7]</td>
					<td align=right>$datgr[8]</td></tr>";
				}
			echo "<tr><td bgcolor=#99CCFF colspan=4><font size='4'>TOTAL CLIENTES</td><td colspan=4 td bgcolor=#99CCFF align=right><font size='4'>$numero</td></tr>";
		}
		else if ($nume=='all'){
			$query = "SELECT *
			FROM farstore_000041, farstore_000060 
			WHERE  Saldto=Clidoc
			AND Clipun<>'' and Clipun<>'000000'";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			//echo mysql_errno() ."=". mysql_error();
			$num = mysql_num_rows($err);
			
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					if (is_int ($i/2))
					$wcf="DDDDDD";
					else
					$wcf="CCFFFF";
					
					$datgr = mysql_fetch_array($err);
					echo "<tr><td align=left bgcolor=".$wcf.">$datgr[Clidoc]</td><td align=left bgcolor=".$wcf.">$datgr[Clinom]</td><td align=left bgcolor=".$wcf.">$datgr[Climai]</td><td align=left bgcolor=".$wcf.">$datgr[Clite1]</td><td align=left bgcolor=".$wcf.">$datgr[Clipun]</td>
					<td align=right bgcolor=".$wcf.">$datgr[Salcau]</td><td align=right bgcolor=".$wcf.">$datgr[Salred]</td><td align=right bgcolor=".$wcf.">$datgr[Saldev]</td><td align=right bgcolor=".$wcf.">$datgr[Salsal]</td></tr>";

				}
			}
			echo "<tr><td bgcolor=#99CCFF colspan=4><font size='4'>TOTAL CLIENTES</td><td colspan=5 td bgcolor=#99CCFF align=right><font size='4'>$num</td></tr>";
		}
	}
}
?>	
