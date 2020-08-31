<html>
<head>
  <title>MATRIX</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body BGCOLOR="dddddd">
<font size=2 face="tahoma">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
	function contador($conex)
	{
		$query = "lock table root_000038 LOW_PRIORITY WRITE ";
		$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO VISITAS : ".mysql_errno().":".mysql_error());
		$query = "select contador from root_000038 ";
		$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO VISITAS");
		$row = mysql_fetch_array($err1);
		$consecutivo=$row[0] + 1;
		$query =  " update root_000038 set contador = contador + 1 where id = 1 ";
		$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO VISITAS");
		$query = " UNLOCK TABLES";													
		$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO VISITAS");
		$longstr = strlen($consecutivo);
		for ($x=0; $x < $longstr; $x++)  
		{
			$image = substr($consecutivo,$x,1);
			echo "<IMG SRC='/matrix/images/digital/$image.gif'>";
		}
	}
	@session_start();
	if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
	else
	{
		if(substr($user,2,strlen($user)) != $usera)
			$user="1-".$usera;
		

		

		$query = "select Codigo, Password, Passdel, Feccap, Tablas, Descripcion, Prioridad, Grupo, Empresa, Ccostos, Activo  from usuarios where codigo = '".substr($user,2,strlen($user))."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		if(date("Y-m-d") > $row[3])
			$grupo = "PASSWD";
		echo "<center>";
		//echo "<IMG SRC='/matrix/images/medical/root/logo.jpg'>";
		echo "<hr>";
		echo "<B>Numero de Visitantes : </B>";
		echo "<br>";
		contador($conex);
		echo "<br>";
		echo "<font size=3><B>Usuario : ".substr($user,2,strlen($user))."</b></font>";
		echo "<hr>";
		echo "<font size=3><center><B>MENU DE OPCIONES</b></center></font></center>";
		switch($grupo)
		{
			case 'AMERICAS':
				echo "<ol>";
				echo "<li><A HREF='general.php?grupo=".$grupo."' target='main'>Inicio</A>";
				echo "<hr>";
				$key = substr($user,2,strlen($user));
				$query = "select codigo,descripcion from root_000020 where usuarios like '%-".$key."-%' ";
				$query .= " or usuarios like '".$key."-%' ";
				$query .= " or usuarios like '%-".$key."' ";
				$query .= " or usuarios = '".$key."' ";
				$query .= " order by codigo";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<li><A HREF='item.php?grupo=".$grupo."&amp;codigo=".$row[0]."' target='main'>".$row[1]."</A>";
						echo "<hr>";
					}
				}
				echo "<li><A HREF='F1.php' target='_top'>Salir del programa</A>";
				echo "<hr>";
				echo "</ol>";
			break;
			case 'PASSWD':
				echo "<font size=2><ol>";
				echo "<li><font color=#FF0000><b>PASSWORD VENCIDO</b></font>";
				echo "<hr>";
				echo "<ol>";
				echo "<li><A HREF='password.php?wtipo=N' target='main'>Cambio de Password</A>";
				echo "<li><A HREF='F1.php' target='_top'>Salir del programa</A>";
				echo "</ol>";
			break;
			default:
				echo "<font size=2><ol>";
				echo "<li>Menu de Maestros";
				echo "<ol>";
				if ($num > 0)
				{
					if ($row[6] > 1)
					{			
						echo "<li><A HREF='general.php?grupo=".$grupo."' target='main'>Inicio</A>";
						echo "<li><A HREF='formularios.php' target='main'>Formularios</A>";
						echo "<li><A HREF='detform.php' target='main'>Detalle de Formularios</A>";
						echo "<li><A HREF='selecciones.php' target='main'>Selecciones</A>";
						echo "<li><A HREF='detsel.php' target='main'>Detalle de Selecciones</A>";
						echo "<li><A HREF='numeracion.php' target='main'>Control de Numeracion</A>";
						echo "<li><A HREF='seguridad_matrix.php' target='main'>Control de Acceso</A>";
						echo "<li><A HREF='defpro.php' target='main'>Definicion de Procesos</A>";
						echo "<li><A HREF='defrep.php' target='main'>Definicion de Reportes</A>";
						if ($row[6] > 2)
							echo "<li><A HREF='usuarios.php' target='main'>Usuarios</A>";
					}
					else
					{
						echo "<li>Formularios";
						echo "<li>Detalle de Formularios";
						echo "<li>Seleciones";
						echo "<li>Detalle de Selecciones";
						echo "<li>Control de Numeracion";
						echo "<li>Control de Acceso";
					}
				}
				echo "</ol>";
				echo "<hr>";
				echo "<li><A HREF='registro.php' target='main'>REGISTRO</A>";
				echo "<hr>";
				echo "<li><A HREF='consultas_matrix.php' target='main'>CONSULTAS</A>";
				echo "<hr>";
				echo "<li><A HREF='procesos.php?grupo=".$grupo."&amp;prioridad=".$prioridad."' target='main'>PROCESOS</A>";
				echo "<hr>";
				echo "<li><A HREF='reportes.php?grupo=".$grupo."&amp;prioridad=".$prioridad."' target='main'>REPORTES</A>";
				echo "<hr>";
				echo "<li><A HREF='carga.php' target='main'>Carga de Archivos Planos</A>";
				echo "<hr>";
				echo "<li><A HREF='cargaG.php' target='main'>Carga de Archivos Planos Grandes</A>";
				//echo "<hr>";
				//echo "<li><A HREF='publicar.php?grupo=".$grupo."&amp;prioridad=".$prioridad."' target='main'>Publicacion de Archivos</A>";
				if ($row[6] > 2)
				{
					echo "<hr>";
					echo "<li><A HREF='esquemas.php' target='main'>Esquemas x Usuario</A>";
					echo "<hr>";
					echo "<li><A HREF='cu_esquemas.php' target='main'>Cambios de Esquemas x Usuario</A>";
				}
				echo "<hr>";
				echo "<li><A HREF='help.php' target='_blank'>Manual de Operacion</A>";
				echo "<hr>";
				echo "<li><A HREF='F1.php' target='_top'>Salir del programa</A>";
				echo "</ol>";
			break;
		}
	}
?>
</font>
</body>
</html>
