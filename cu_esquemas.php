<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Cambio de Esquemas x Usuario</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> cu_esquemas.php Ver. 2007-07-06</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
/**********************************************************************************************************************  
	   PROGRAMA : cu_esquemas.php
	   Fecha de Liberación : 2007-03-29
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2007-07-06
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite cambiar los esquemas que
	   pertenecen a un usuario x otro usuario.
	   
	   
	   REGISTRO DE MODIFICACIONES :

	   .2007-07-06
	   	Se modifico el programa para que copiara correctamente las opciones de submenus.
	   	La anterior modificacion estaba incompleta.
	  
	   .2007-07-04
	   	Se modifico el programa para que copiara correctamente las opciones de submenus.
	   	
	   .2007-03-29
	   		Release de Versión Beta.
	   	
	   .2007-05-31
	   		Se modifico el programa para dar explicaciones a las operaciones.
	   
***********************************************************************************************************************/
function array_search1($item,$arreglo)
{
	$wsw=0;
	for ($j=0;$j<count($arreglo);$j++)
		if($item == $arreglo[$j])
			return true;
	return false;
}
			
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='cu_esquemas.php' method=post>";
	

	

	if(!isset($wuser1) or !isset($wtip))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>CAMBIO DE ESQUEMAS X USUARIO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Usuario Objetivo</td>";
		echo "<td bgcolor=#cccccc align=center>";
		$query = "SELECT codigo,descripcion from usuarios where grupo = 'AMERICAS' order by codigo";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wuser1'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Usuario Modelo y/o Receptor</td>";
		echo "<td bgcolor=#cccccc align=center>";
		$query = "SELECT codigo,descripcion from usuarios where grupo = 'AMERICAS' order by codigo";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wuser2'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc>PROCESOS SOBRE LOS ESQUEMAS</TD><td bgcolor=#cccccc><input type='RADIO' name='wtip' value=0> Copiar<br>";
		echo "<input type='RADIO' name='wtip' value=1> Replicar<br>";
		echo "<input type='RADIO' name='wtip' value=2> Cambiar</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=2> 1. En la <b>COPIA</b> el usuario Objetivo recibe en esquema del usuario Modelo pero conserva el suyo.<br>";
		echo " 2. En la <b>REPLICACION</b> el usuario Objetivo recibe en esquema del usuario Modelo y quedan iguales.<br>";
		echo " 3. En el <b>CAMBIO</b> el usuario Modelo recibe en esquema del usuario Objetivo y el usuario Objetivo queda vacio o sin opciones.<br>";
		echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		switch ($wtip)
		{
			case 0:
				$wuser1a=substr($wuser1,0,strpos($wuser1,"-"));
				$wuser2a=substr($wuser2,0,strpos($wuser2,"-"));
				$query = "SELECT root_000020.Codigo, root_000020.Descripcion, root_000021.Codopt, root_000021.Descripcion, root_000020.Usuarios, root_000021.Usuarios from root_000020,root_000021 where codigo = codgru AND root_000021.Usuarios like '%".$wuser2a."%'  ORDER BY root_000020.Codigo, root_000021.Codopt";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				$codgru="";
				$k=0;
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						if($i % 2 == 0)
							$color ="#ffffff";
						else
							$color ="#cccccc";
						$row = mysql_fetch_array($err);
						$t20=str_replace("-".$wuser1a,"",$row[4]);
						$t20=str_replace($wuser1a."-","",$t20);
						$query = "UPDATE root_000020 SET root_000020.Usuarios='".$t20."' where codigo = '".$row[0]."'";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$t21=str_replace("-".$wuser1a,"",$row[5]);
						$t21=str_replace($wuser1a."-","",$t21);
						$query = "UPDATE root_000021 SET root_000021.Usuarios='".$t21."' where codgru = '".$row[0]."' and Codopt = '".$row[2]."' ";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					}
				}
				echo "<table border=0 align=center>";
				echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' width=160 high=160 ></td><td  align=center bgcolor=#cccccc colspan=4><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=5 align=center><b>REPLICA DE ESQUEMAS X USUARIO</b></td></tr>";
				echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>USUARIO ACTUAL : ".$wuser1."</b></td></tr>";
				echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>USUARIO MODELO  : ".$wuser2."</b></td></tr>";
				echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>OPCIONES ACTUALIZADAS</b></td></tr>";
				echo "<tr><td bgcolor=#999999 align=center><b>GRUPO</b></td><td bgcolor=#999999 align=center><b>NOMBRE</b></td><td bgcolor=#999999 align=center><b>NRO OPCION</b></td><td bgcolor=#999999 align=center colspan=2><b>DESCRIPCION</b></td></tr>";
				$query = "SELECT root_000020.Codigo, root_000020.Descripcion, root_000021.Codopt, root_000021.Descripcion, root_000020.Usuarios, root_000021.Usuarios from root_000020,root_000021 where codigo = codgru AND root_000021.Usuarios like '%".$wuser2a."%'  ORDER BY root_000020.Codigo, root_000021.Codopt";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				$codgru="";
				$k=0;
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						if($i % 2 == 0)
							$color ="#ffffff";
						else
							$color ="#cccccc";
						$row = mysql_fetch_array($err);
						if(strpos($row[4],$wuser2a) !== false)
						{
							$t20=$row[4]."-".$wuser1a;
							$query = "UPDATE root_000020 SET root_000020.Usuarios='".$t20."' where codigo = '".$row[0]."'";
							$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						}
						$t21=$row[5]."-".$wuser1a;
						$query = "UPDATE root_000021 SET root_000021.Usuarios='".$t21."' where codgru = '".$row[0]."' and Codopt = '".$row[2]."' ";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						echo "<td bgcolor=".$color." align=center>".$row[0]."</td><td bgcolor=".$color." align=center>".$row[1]."</td><td bgcolor=".$color.">".$row[2]."</td><td bgcolor=".$color.">".$row[3]."</td></tr>";
					}
				}
				echo"</table>";
			break;
			case 1:
				$wuser1a=substr($wuser1,0,strpos($wuser1,"-"));
				$wuser2a=substr($wuser2,0,strpos($wuser2,"-"));
				$query = "SELECT root_000020.Codigo, root_000020.Descripcion, root_000021.Codopt, root_000021.Descripcion, root_000020.Usuarios, root_000021.Usuarios from root_000020,root_000021 where codigo = codgru AND root_000021.Usuarios like '%".$wuser1a."%'  ORDER BY root_000020.Codigo, root_000021.Codopt";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				$codgru="";
				$k=0;
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						if($i % 2 == 0)
							$color ="#ffffff";
						else
							$color ="#cccccc";
						$row = mysql_fetch_array($err);
						$t20=str_replace("-".$wuser1a,"",$row[4]);
						$t20=str_replace($wuser1a."-","",$t20);
						$query = "UPDATE root_000020 SET root_000020.Usuarios='".$t20."' where codigo = '".$row[0]."'";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$t21=str_replace("-".$wuser1a,"",$row[5]);
						$t21=str_replace($wuser1a."-","",$t21);
						$query = "UPDATE root_000021 SET root_000021.Usuarios='".$t21."' where codgru = '".$row[0]."' and Codopt = '".$row[2]."' ";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					}
				}
				echo "<table border=0 align=center>";
				echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' width=160 high=160></td><td  align=center bgcolor=#cccccc colspan=4><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=5 align=center><b>REPLICA DE ESQUEMAS X USUARIO</b></td></tr>";
				echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>USUARIO ACTUAL : ".$wuser1."</b></td></tr>";
				echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>USUARIO MODELO  : ".$wuser2."</b></td></tr>";
				echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>OPCIONES ACTUALIZADAS</b></td></tr>";
				echo "<tr><td bgcolor=#999999 align=center><b>GRUPO</b></td><td bgcolor=#999999 align=center><b>NOMBRE</b></td><td bgcolor=#999999 align=center><b>NRO OPCION</b></td><td bgcolor=#999999 align=center colspan=2><b>DESCRIPCION</b></td></tr>";
				$query = "SELECT root_000020.Codigo, root_000020.Descripcion, root_000021.Codopt, root_000021.Descripcion, root_000020.Usuarios, root_000021.Usuarios from root_000020,root_000021 where codigo = codgru AND root_000021.Usuarios like '%".$wuser2a."%'  ORDER BY root_000020.Codigo, root_000021.Codopt";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				$codgru="";
				$k=0;
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						if($i % 2 == 0)
							$color ="#ffffff";
						else
							$color ="#cccccc";
						$row = mysql_fetch_array($err);
						if(strpos($row[4],$wuser2a) !== false)
						{
							$t20=$row[4]."-".$wuser1a;
							$query = "UPDATE root_000020 SET root_000020.Usuarios='".$t20."' where codigo = '".$row[0]."'";
							$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						}
						$t21=$row[5]."-".$wuser1a;
						$query = "UPDATE root_000021 SET root_000021.Usuarios='".$t21."' where codgru = '".$row[0]."' and Codopt = '".$row[2]."' ";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						echo "<td bgcolor=".$color." align=center>".$row[0]."</td><td bgcolor=".$color." align=center>".$row[1]."</td><td bgcolor=".$color.">".$row[2]."</td><td bgcolor=".$color.">".$row[3]."</td></tr>";
					}
				}
				echo"</table>";
			break;
			case 2:
				$wuser1a=substr($wuser1,0,strpos($wuser1,"-"));
				$wuser2a=substr($wuser2,0,strpos($wuser2,"-"));
				echo "<table border=0 align=center>";
				echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' width=160 high=160></td><td  align=center bgcolor=#cccccc colspan=4><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=5 align=center><b>CAMBIO DE ESQUEMAS X USUARIO</b></td></tr>";
				echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>USUARIO ACTUAL : ".$wuser1."</b></td></tr>";
				echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>USUARIO NUEVO  : ".$wuser2."</b></td></tr>";
				echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>OPCIONES ACTUALIZADAS</b></td></tr>";
				echo "<tr><td bgcolor=#999999 align=center><b>GRUPO</b></td><td bgcolor=#999999 align=center><b>NOMBRE</b></td><td bgcolor=#999999 align=center><b>NRO OPCION</b></td><td bgcolor=#999999 align=center colspan=2><b>DESCRIPCION</b></td></tr>";
				$query = "SELECT root_000020.Codigo, root_000020.Descripcion, root_000021.Codopt, root_000021.Descripcion, root_000020.Usuarios, root_000021.Usuarios from root_000020,root_000021 where codigo = codgru AND root_000021.Usuarios like '%".$wuser1a."%'  ORDER BY root_000020.Codigo, root_000021.Codopt";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				$codgru="";
				$k=0;
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						if($i % 2 == 0)
							$color ="#ffffff";
						else
							$color ="#cccccc";
						$row = mysql_fetch_array($err);
						$t20=str_replace($wuser1a,$wuser2a,$row[4]);
						$query = "UPDATE root_000020 SET root_000020.Usuarios='".$t20."' where codigo = '".$row[0]."'";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$t21=str_replace($wuser1a,$wuser2a,$row[5]);
						$query = "UPDATE root_000021 SET root_000021.Usuarios='".$t21."' where codgru = '".$row[0]."' and Codopt = '".$row[2]."' ";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						echo "<td bgcolor=".$color." align=center>".$row[0]."</td><td bgcolor=".$color." align=center>".$row[1]."</td><td bgcolor=".$color.">".$row[2]."</td><td bgcolor=".$color.">".$row[3]."</td></tr>";
					}
				}
				echo"</table>";
			break;
		}
	}
}
?>
</body>
</html>