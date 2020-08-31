<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Actualizacion de Esquemas x Usuario</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> esquemas.php Ver. 2012-07-05</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
/**********************************************************************************************************************  
	   PROGRAMA : esquemas.php
	   Fecha de Liberación : 2005-05-31
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2012-07-05
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite generar las vistas logicas a
	   los usuarios del grupo AMERICAS o Portal.
	   Los usuarios se matriculan en los grupos y en las opciones de grupos para configurar su Portal.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2012-07-05
			Se adiciona al programa un icono que permite modificar los esquemas o vistas logicas de otros usuarios en el
			encabezado de la tabla.
			
	   .2012-07-04
			Se adiciona al programa un icono que permite modificar los esquemas o vistas logicas de otros usuarios.
			
	   .2005-05-31
	   		Se modifica el programa para que se puedan matricular a los usuarios en las opciones sin necesidad de 
	   		matricularlos en los grupos y asi poder crear grupos y subgrupos.
		
	   .2005-05-31
	   		Release de Versión Beta.
	   
***********************************************************************************************************************/
function array_search1($item,$arreglo)
{
	$wsw=0;
	for ($j=0;$j<count($arreglo);$j++)
		if($item == $arreglo[$j])
			return true;
	return false;
}
			
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='esquemas.php' method=post>";
	

	

	if(isset($ok))
	{
		$query = "SELECT root_000020.Codigo,  root_000020.Usuarios, root_000021.Codopt, root_000021.Usuarios  from root_000020,root_000021 where codigo = codgru ";
		$query = $query." order by codigo,codopt";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$codgru="";
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($codgru != $row[0])
				{
					if(isset($actgru[$row[0]]))
					{
						$egru=explode("-",$row[1]);
						if(!array_search1($wuser,$egru))
						{
							if(strlen($row[1]) == 0)
								$row[1] = $wuser;
							else
								$row[1] = $row[1]."-".$wuser;
							$query = "update root_000020 set Usuarios='".$row[1]."' where codigo=".$row[0];
							$err1 = mysql_query($query,$conex) or die("Error en la Actualizacion");
						}
					}
					else
					{
						$egru=explode("-",$row[1]);
						if(array_search1($wuser,$egru))
						{
							$ini=strpos($row[1],$wuser);
							$row[1] = substr($row[1],0,$ini).substr($row[1],$ini+strlen($wuser)+1);
							if(substr($row[1],strlen($row[1])-1,1) == "-")
							{
								$row[1]=substr($row[1],0,strlen($row[1])-1);
							}
							$query = "update root_000020 set Usuarios='".$row[1]."' where codigo=".$row[0];
							$err1 = mysql_query($query,$conex) or die("Error en la Actualizacion");
						}
					}
				}
				//if(isset($actgru[$row[0]]) and (isset($actopt[$row[0]][$row[2]]) or isset($acttot[$row[0]])))
				if(isset($actopt[$row[0]][$row[2]]) or isset($acttot[$row[0]]))
				{
					$egru=explode("-",$row[3]);
					if(!array_search1($wuser,$egru))
					{
						if(strlen($row[3]) == 0)
							$row[3] = $wuser;
						else
							$row[3] = $row[3]."-".$wuser;
						$query = "update root_000021 set Usuarios='".$row[3]."' where codgru=".$row[0]." and Codopt=".$row[2];
						$err1 = mysql_query($query,$conex) or die("Error en la Actualizacion");
					}
				}
				else
				{
					$egru=explode("-",$row[3]);
					if(array_search1($wuser,$egru))
					{
						$ini=strpos($row[3],$wuser);
						$row[3] = substr($row[3],0,$ini).substr($row[3],$ini+strlen($wuser)+1);
						if(substr($row[3],strlen($row[3])-1,1) == "-")
						{
							$row[3]=substr($row[3],0,strlen($row[3])-1);
						}
						$query = "update root_000021 set Usuarios='".$row[3]."' where codgru=".$row[0]." and Codopt=".$row[2];
						$err1 = mysql_query($query,$conex) or die("Error en la Actualizacion");
					}
				}
			}
		}
		unset($ok);
	}
	if(!isset($wuser))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION DE ESQUEMAS X USUARIO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Usuario</td>";
		echo "<td bgcolor=#cccccc align=center>";
		$query = "SELECT codigo,descripcion from usuarios where grupo = 'AMERICAS' order by codigo";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wuser'>";
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
		if(!isset($wuser1))
		{
			$wuser1=substr($wuser,strpos($wuser,"-")+1);
			$wuser =substr($wuser,0,strpos($wuser,"-"));
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' width=160 high=160></td><td  align=center bgcolor=#cccccc colspan=4><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=5 align=center><b>ACTUALIZACION DE ESQUEMAS X USUARIO</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>USUARIO: ".$wuser."-".$wuser1."</b></td></tr>";
		echo "<tr><td bgcolor=#dddddd colspan=5 align=center><A HREF='/MATRIX/esquemas.php'><IMG SRC='/MATRIX/images/medical/root/user.png'</A></td></tr>";
		echo "<tr><td bgcolor=#999999 align=center><b>NRO OPCION</b></td><td bgcolor=#999999 align=center colspan=2><b>DESCRIPCION</b></td><td bgcolor=#999999  align=center><b>POSICION</b></td><td bgcolor=#999999  align=center><b>ACTIVO</b></td></tr>";
		$query = "SELECT root_000020.Codigo, root_000020.Descripcion, root_000020.Usuarios, root_000020.Comentarios, root_000021.Codopt, root_000021.Descripcion, root_000021.Usuarios  from root_000020,root_000021 where codigo = codgru ";
		$query = $query." order by codigo,codopt";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$codgru="";
		$k=0;
		if ($num>0)
		{
			$actgru=array();
			$acttot=array();
			$actopt=array();
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($codgru != $row[0])
				{
					$k=0;
					$codgru=$row[0];
					$egru=explode("-",$row[2]);
					echo "<tr><td bgcolor=#99CCFF align=center><b>Grupo: </b>".$row[0]."</td><td bgcolor=#99CCFF align=center><b>Descripcion: </b>".$row[1]."</td><td bgcolor=#99CCFF align=center><b>Comentarios: </b>".$row[3]."</td>";
					if(array_search1($wuser,$egru))
						echo "<td bgcolor=#99CCFF align=center>Activo en Grupo<br><input type='checkbox' name='actgru[".$row[0]."]' checked><td bgcolor=#99CCFF align=center>Activar Todas las Opciones<br><input type='checkbox' name='acttot[".$row[0]."]'></td></tr>";
					else
						echo "<td bgcolor=#99CCFF align=center>Activo en Grupo<br><input type='checkbox' name='actgru[".$row[0]."]'><td bgcolor=#99CCFF align=center>Activar Todas las Opciones<br><input type='checkbox' name='acttot[".$row[0]."]'></td></tr>";
				}
				$egru=explode("-",$row[6]);
				echo "<tr><td bgcolor=#dddddd align=center>".$row[4]."</td><td bgcolor=#dddddd colspan=2><b>".$row[5]."</b></td>";
				$k++;
				if(array_search1($wuser,$egru))
					echo "<td bgcolor=#dddddd align=center>".$k."</td><td bgcolor=#dddddd align=center><input type='checkbox' name='actopt[".$row[0]."][".$row[4]."]' checked></td></tr>";
				else
					echo "<td bgcolor=#dddddd align=center>".$k."</td><td bgcolor=#dddddd align=center><input type='checkbox' name='actopt[".$row[0]."][".$row[4]."]'></td></tr>";
			}
		}
		echo "<input type='HIDDEN' name= 'wuser' value='".$wuser."'>";
		echo "<input type='HIDDEN' name= 'wuser1' value='".$wuser1."'>";
		echo "<tr><td bgcolor=#cccccc colspan=5 align=center>DATOS OK!! <input type='checkbox' name='ok'></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=5 align=center><input type='submit' value='ACTUALIZAR'></td></tr>";
		echo "<tr><td bgcolor=#dddddd colspan=5 align=center><A HREF='/MATRIX/esquemas.php'><IMG SRC='/MATRIX/images/medical/root/user.png'</A></td></tr>";
		echo"</table>";
	}
}
?>
</body>
</html>
