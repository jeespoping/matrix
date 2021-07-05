<html>
<head>
  <title>MATRIX</title>
    <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    </style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Maestro de Usuarios</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>usuarios.php Ver. 2007-05-10</b></font></tr></td></table>
</center>

<?php
//2020-08-02, DIDIER OROZCO MODIFICA: SE AGREGAN LOS NUEVOS CAMPOS QUE SE CREARON EN LA TABLA Usuarios CON EL FIN DE RECUPERAR LA CONTRASEÑA.
//2021-06-30, JULIAN MEJIA MODIFICA: SE AGREGA LA VALIDACION PARA QUE SOLO PUEDAN ACCEDER A ESTE SCRIPT LOS USUARIOS AUTORIZADOS (POR DEFECTO root).
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
{
		?>
		<div align="center">
			<label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
		</div>
		<?php
		return;
}
else
{
	include_once("root/comun.php");
	$usuarioPermitidoAux = 'root';
	$permitirAcceso = false;
	$usuarioPermitido = consultarAliasPorAplicacion($conex,'*', 'maestroUsuarios');
	$usuarioPermitido = ($usuarioPermitido == '') ? $usuarioPermitidoAux : $usuarioPermitido;
	if ($usuarioPermitido != ''){
		$pos = strpos('*', $usuarioPermitido);
		if ($pos !== false) $permitirAcceso = true;
		else
		{
			$codigosP = explode(',',$usuarioPermitido);
			if (is_array($codigosP)){
				$user_session = explode('-', $_SESSION['user']);
				$wuse = $user_session[1];
				if (in_array($wuse,$codigosP)) $permitirAcceso = true;
			}
		}
	}
	
    if(!$permitirAcceso){
        ?>
        <div align="center">
            <label>USUARIO NO AUTORIZADO PARA CONSULTAR<br/>
            Ingrese de nuevo a Matrix con un usuario v&aacutelido</label>
        </div>
        <?php
        return;
    }
	

	$superglobals = array($_SESSION,$_REQUEST);
	foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
	{
		foreach ($valueSuperglobals as $variable => $dato)
		{
			$$variable = $dato; 
		}
	}
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='usuarios.php' method=post>";
	echo "<table border=0 align=center id='tipo1'>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Criterio de busqueda</td>";		
	if (isset($criterio))	
	{
		$criterio=stripslashes($criterio);
		echo "<td bgcolor=#cccccc><textarea name='criterio' cols=60 rows=5 class=tipo3>".$criterio."</textarea></td>";
	}
	else
		echo "<td bgcolor=#cccccc><textarea name='criterio' cols=60 rows=5 class=tipo3></textarea></td>";
	if(isset($back))
		echo "<td bgcolor=#cccccc><input type='checkbox' name=back checked>Back</td>";
	else
		echo "<td bgcolor=#cccccc><input type='checkbox' name=back>Back</td>";
	echo "<td bgcolor=#cccccc  align=center><input type='submit' value='IR'></td>";
	echo "</tr></table><br>";	
	
	if(isset($cri))
		$cri=stripslashes($cri);
	else 
		$cri="";
	if(!isset($criterio))
	{
		$criterio="";
		$cri="";
	}
	else
	{
		if($cri != $criterio)
		{
			$cri = $criterio;
			unset($Inicial);
		}
	}
	echo "<table border=0 align=left id='tipo1'>";
	echo "<tr><td align=center><A HREF='det_usuarios.php?pos=0&ok=99'>Nuevo</td></tr></table><BR>\n";
	if(isset($criterio) and strlen($criterio) > 0)
	{
		$criterio=stripslashes($criterio);
		$query = "select * from usuarios where ".$criterio;
	}
	else
		$query = "select * from usuarios";
	$err = mysql_query($query,$conex) OR die("CRITERIO NO APLICABLE");
	$Totales = mysql_num_rows($err);
	if(!isset($Pagina))
		if(!isset($Inicial))
			$Pagina=1;
		else
			$Pagina=$Inicial / 30 + 1;
	if(isset($Pagina) and $Pagina > 0)
	{
		$Paginas=(integer)($Totales / 30);
		if($Paginas * 30 < $Totales)
			$Paginas++;
		if($Pagina > $Paginas)
			$Pagina=$Paginas;
		if($Pagina == 0)
			$Pagina++;
		$Inicial=($Pagina - 1 ) * 30;
		$Final= $Inicial + 30;
	}
	else
	{
		if (!isset($Inicial))
		{
			$Inicial=0;
			$Final=30;
		}
		else
			if(!isset($back))
			{
				if($Final < $Totales)
				{
					$Inicial = $Final;
					$Final=$Final+30;
				}
			}
			else
			{
				if($Inicial >= 30)
				{
					$Final = $Inicial;
					$Inicial=$Inicial-30;
				}
			}
			$Pagina=$Inicial / 30 + 1;
		}
	echo "<input type='HIDDEN' name= 'Inicial' value='".$Inicial."'>";
	echo "<input type='HIDDEN' name= 'Final' value='".$Final."'>";
	echo "<input type='HIDDEN' name= 'cri' value='".$cri."'>";
	if(isset($criterio) and strlen($criterio) > 0)
	{
		$criterio=stripslashes($criterio);
		$query = "select Codigo, Password, Passdel, Feccap, Tablas, Descripcion, Prioridad, Grupo, Empresa, Ccostos, Activo, Documento, Email, PasswordTemporal, FechaPasswordTemp, HoraPasswordTemp from usuarios where ".$criterio;
	}
	else
		$query = "select Codigo, Password, Passdel, Feccap, Tablas, Descripcion, Prioridad, Grupo, Empresa, Ccostos, Activo, Documento, Email, PasswordTemporal, FechaPasswordTemp, HoraPasswordTemp from usuarios";
	$query = $query." limit ".$Inicial.",30";
	$err = mysql_query($query,$conex) OR die("CRITERIO NO APLICABLE");
	$num = mysql_num_rows($err);
	echo "Registros :<b>".$Inicial."</b> a <b>".$Final."</b>&nbsp &nbsp";
	echo "De :<b>".$Totales."</b>&nbsp &nbsp";
	$Paginas=(integer)($Totales / 30);
	if($Paginas * 30 < $Totales)
		$Paginas++;
	echo " Pagina Nro :<b> ".$Pagina."</b>&nbsp &nbspDe :<b>".$Paginas."</b>&nbsp &nbsp <b>Vaya a la Pagina Nro :</b> <input type='TEXT' name='Pagina' size=10 maxlength=10 value=0>&nbsp &nbsp<input type='submit' value='IR'><br>";
	if ($num > 0)
	{
		$color="#999999";
		echo "<table border=0 align=center>";
		echo "<tr>";
  		echo "<td bgcolor=".$color."><b>Codigo</b></td>";
  		echo "<td bgcolor=".$color."><b>Password</b></td>";
  		echo "<td bgcolor=".$color."><b>Password Del</b></td>";
  		echo "<td bgcolor=".$color."><b>Fecha Cambio<br>Password</b></td>";
  		echo "<td bgcolor=".$color."><b>Tablas Del</b></td>";
  		echo "<td bgcolor=".$color."><b>Descripcion</b></td>";
  		echo "<td bgcolor=".$color."><b>Prioridad</b></td>";
  		echo "<td bgcolor=".$color."><b>Grupo</b></td>";
  		echo "<td bgcolor=".$color."><b>Empresa</b></td>";
  		echo "<td bgcolor=".$color."><b>Centro<br>Costos</b></td>";
  		echo "<td bgcolor=".$color."><b>Activo</b></td>";
		echo "<td bgcolor=".$color."><b>Documento</b></td>";
		echo "<td bgcolor=".$color."><b>Email</b></td>";
		echo "<td bgcolor=".$color."><b>Clave<br>Temporal</b></td>";
		echo "<td bgcolor=".$color."><b>Fecha de<br>Clave Temporal</b></td>";
		echo "<td bgcolor=".$color."><b>Hora de<br>Clave Temporal</b></td>";
  		echo "<td bgcolor=".$color."><b>Seleccion</b></td>"; 		
		echo "</tr>";
		$r = 0;
		for ($i=0;$i<$num;$i++)
		{
			$r = $i/2;
			if ($r*2 === $i)
				$color="#CCCCCC";
			else
				$color="#999999";
			$row = mysql_fetch_array($err);
			echo "<tr>";			
			echo "<td bgcolor=".$color.">".$row[0]."</td>";
			echo "<td bgcolor=".$color.">".$row[1]."</td>";
			echo "<td bgcolor=".$color.">".$row[2]."</td>";
			echo "<td bgcolor=".$color.">".$row[3]."</td>";
			echo "<td bgcolor=".$color.">".substr($row[4],0,60)." ....</td>";
			echo "<td bgcolor=".$color.">".$row[5]."</td>";
			echo "<td bgcolor=".$color." align=center>".$row[6]."</td>";
			echo "<td bgcolor=".$color.">".$row[7]."</td>";
			echo "<td bgcolor=".$color.">".$row[8]."</td>";
			echo "<td bgcolor=".$color.">".$row[9]."</td>";
			switch ($row[10])
			{
				case "A":
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/activo.gif' ></td>";
					break;
				case "I":
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/inactivo.gif' ></td>";
					break;
				default:
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
					break;
			}
			echo "<td bgcolor=".$color.">".$row[11]."</td>";
			echo "<td bgcolor=".$color.">".$row[12]."</td>";
			echo "<td bgcolor=".$color.">".$row[13]."</td>";
			echo "<td bgcolor=".$color.">".$row[14]."</td>";
			echo "<td bgcolor=".$color.">".$row[15]."</td>";
			echo "<td bgcolor=".$color." align=center><A HREF='det_usuarios.php?pos=$row[0]&ok=99'>Editar</td>";
			echo "</tr>";
		}
		echo "</tabla>";
		echo "<table border=0 align=center>";
		echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";	
	}
	else
	{
		echo " Tabla Vacia";
	}
	mysql_free_result($err);
	mysql_close($conex);
}
?>
</body>
</html>
