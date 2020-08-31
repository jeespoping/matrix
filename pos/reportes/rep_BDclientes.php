<html>
<head>
<title>Reporte base de datos de clientes</title>
</head>
<body >
<font face='arial'>
<BODY TEXT="#000066">
<?php
include_once("conex.php");

/********************************************************
*     REPORTE BASE DE DATOS DE CLIENTES					*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte base de datos de clientes 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:ABRIL 2006
//FECHA ULTIMA ACTUALIZACION 	:19 de Abril de 2006
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
		echo "<tr><td align=center><font size='5'><br>REPORTE BASE DE DATOS DE CLIENTES</td></tr>";
		echo "</table>";
		echo "<br>";
		
		echo "<table border=(1) align=center>";
		echo "<tr><td colspan=3 align=center><input type='TEXT' name='num' size=30 maxlength=30 ></td></tr>";
		echo "<tr><td>Tipo de cliente:</td><td><select name='tipo'>";
		$query = "SELECT Clecla  
			FROM ".$empresa."_000042";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				echo "<option>TODOS</option>";	
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."</option>";
				
				}
			}
			
		echo "<tr><td><input type='radio' name='nume' value='ced'>Cedula&nbsp&nbsp&nbsp&nbsp&nbsp</td>
			<td><input type='radio' name='nume' value='nom'>Nombre&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr>
			<tr><td><input type='radio' name='nume' value='tel'>Telefono&nbsp&nbsp&nbsp&nbsp&nbsp</td>
			<td><input type='radio' name='nume' value='tarpu'>Numero de tarjeta&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr>
			<tr><td colspan=2 align=center><input type='radio' name='nume' value='all' checked>Todos los clientes&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr>";
		echo "<tr><td colspan=3 align=center><input type='submit' value='ENTER'></td></tr></table>";
		echo "</table>";
	}else
	
	{


		////////////////////////////////////////////////////////////////////////////////
		
		if ($tipo=="TODOS")
			{
				$variable="%";
			}else
			{
				$variable=$tipo;
			}
		
		
		if ($nume=='ced')
		   {
			$query = " SELECT Clidoc, Clinom, Clidir, Clite1, Clipun, Fecha_data 
			             FROM ".$empresa."_000041
			            WHERE Clidoc ='".$num."'
			              AND clitip like '%".$variable."%'
						ORDER BY Clinom";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			$datgr = mysql_fetch_row($err);
		   }
		else if ($nume=='tarpu'){
			$query = "SELECT Clidoc, Clinom, Clidir, Clite1, Clipun, Fecha_data  
					FROM ".$empresa."_000041
					WHERE Clipun='".$num."'
					AND clitip like '%".$variable."%'
					ORDER BY Clinom";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			$datgr = mysql_fetch_row($err);
		}
		
		else if ($nume=='tel'){
			$query = "SELECT Clidoc, Clinom, Clidir, Clite1, Clipun, Fecha_data  
					FROM ".$empresa."_000041
					WHERE Clite1='".$num."'
					AND clitip like '%".$variable."%'
					ORDER BY Clinom";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			$datgr = mysql_fetch_row($err);
			
		}
		
		
		
		///////////// IMPRESION ///////////////////////////////////////////////////////////
		
		echo "<table border=0 align=center >";
		echo "<tr><td align=center><img SRC='/MATRIX/images/medical/pos/logo_".$empresa.".png'></td>";
		echo "<tr><td align=center><font size='5'><br>REPORTE BASE DE DATOS DE CLIENTES</td></tr>";
		echo "<tr><td align=center><font size='4'><br>TIPO DE CLIENTES: $tipo</td></tr>";
		echo "</table>";
		echo "<br>";
		echo "<br>";
		echo "<table border=1 align=center >";
		echo "<tr><td bgcolor=#99CCFF><font size='4'>Cedula</td><td bgcolor=#99CCFF><font size='4'>Nombre</td><td bgcolor=#99CCFF><font size='4'>Dirección</td>
				  <td bgcolor=#99CCFF><font size='4'>Teléfono</td><td bgcolor=#99CCFF><font size='4'>Numero de Tarjeta</td><td bgcolor=#99CCFF><font size='4'>Ultima fecha de compra</td>";

		$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
 		$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
		
		
		if ($nume=='ced'or $nume=='tarpu' or $nume=='tel'){
			
			echo "<tr><td align=left>$datgr[0]</td><td align=left>$datgr[1]</td><td align=left>$datgr[2]</td><td align=right>$datgr[3]</td><td align=right>$datgr[4]</td><td align=right>$datgr[5]</td></tr>";
		
		}else if($nume=='nom')
		{	
			$query = "SELECT Clidoc, Clinom, Clidir, Clite1, Clipun, Fecha_data  
					FROM ".$empresa."_000041
					WHERE Clinom like'%".$num."%'
					AND clitip like '%".$variable."%'
					ORDER BY Clinom";
			$err = mysql_query($query,$conex);
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
					
					$datgr = mysql_fetch_row($err);
					echo "<tr><td align=left bgcolor=".$wcf.">$datgr[0]</td><td align=left bgcolor=".$wcf.">$datgr[1]</td><td align=left bgcolor=".$wcf.">$datgr[2]</td>
					<td align=right bgcolor=".$wcf.">$datgr[3]</td><td align=right bgcolor=".$wcf.">$datgr[4]</td><td align=right bgcolor=".$wcf.">$datgr[5]</td></tr>";
				}
			}
			echo "<tr><td bgcolor=#99CCFF colspan=4><font size='4'>TOTAL CLIENTES</td><td colspan=3 td bgcolor=#99CCFF align=right><font size='4'>$num</td></tr>";
		
		}else if ($nume=='all'){
			$query = "SELECT Clidoc, Clinom, Clidir, Clite1, Clipun, Fecha_data
					FROM farstore_000041 
					WHERE clitip like '%".$variable."%'
					ORDER BY Clinom";
			
			$err = mysql_query($query,$conex);
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
					echo "<tr><td align=left bgcolor=".$wcf.">$datgr[0]</td><td align=left bgcolor=".$wcf.">$datgr[1]</td><td align=left bgcolor=".$wcf.">$datgr[2]</td>
					<td align=right bgcolor=".$wcf.">$datgr[3]</td><td align=right bgcolor=".$wcf.">$datgr[4]</td><td align=right bgcolor=".$wcf.">$datgr[5]</td></tr>";

				}
			}
			echo "<tr><td bgcolor=#99CCFF colspan=4><font size='4'>TOTAL CLIENTES</td><td colspan=3 td bgcolor=#99CCFF align=right><font size='4'>$num</td></tr>";
		}
	}
}
?>	