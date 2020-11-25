<html>
<head>
  <title>MATRIX Maestro de Usuarios</title>
    <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    </style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.det_usuarios.submit();
	}
//-->
</script>
<center>
<table border=0 align=center id='tipo1'>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Maestro de Usuarios</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>det_usuarios.php Ver. 2020-08-02</b></font></tr></td></table>
</center>
<?php
//2020-08-02, DIDIER OROZCO MODIFICA: SE AGREGAN LOS NUEVOS CAMPOS QUE SE CREARON EN LA TABLA Usuarios CON EL FIN DE RECUPERAR LA CONTRASEÑA.
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$superglobals = array($_SESSION,$_REQUEST);
	foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
	{
		foreach ($valueSuperglobals as $variable => $dato)
		{
			$$variable = $dato; 
		}
	}
	$key = substr($user,2,strlen($user));
	echo "<form name='det_usuarios' action='det_usuarios.php' method=post>";
	

	

	if (isset($ok) and $ok == 1)
	{
		if((strlen($Codigo) < 5 and $Grupo == "AMERICAS") or strlen($Password) < 4 or $Codigo == "     " or  $Password == "    " or $Feccap == "0000-00-00" or strlen($Descripcion) < 10)
		{
			$error="ERROR EN DATOS POR FAVOR VERIFIQUE LA INFORMACION DEL CODIGO, PASSWORD, FECHA DE CAMBIO DE PASSWORD O DESCRIPCION!!!";
		}
		else
		{
			if(substr($Empresa,0,strpos($Empresa,"-")) == "")
				$Empresa="NO-NO";
			if(!isset($Ccostos) or substr($Ccostos,0,strpos($Ccostos,"-")) == "")
				$Ccostos="NO-NO";
			if ($Prioridad > 3)
				$Prioridad = 3;
			switch ($wpar)
			{
				case 1:
				$query = "update usuarios set Password=SHA1('".$Password."'), Passdel='".$Passdel."', Tablas='".$Tablas."', Prioridad=".$Prioridad.", Grupo='".$Grupo."', Activo='".substr($Activo,0,1)."', Descripcion='".$Descripcion."', Feccap='".$Feccap."', Empresa='".substr($Empresa,0,strpos($Empresa,"-"))."', Ccostos='".substr($Ccostos,0,strpos($Ccostos,"-"))."', Documento='".$Documento."', Email='".$Email."', PasswordTemporal='".$PasswordTemporal."', FechaPasswordTemp='".$FechaPasswordTemp."', HoraPasswordTemp='".$HoraPasswordTemp."' where codigo='".strtolower($Codigo)."'";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				break;
				case 2:
				$query = "insert usuarios values ('".strtolower($Codigo)."',SHA1('".$Password."'),'".$Passdel."','".$Feccap."','".$Tablas."','".$Descripcion."',".$Prioridad.",'".$Grupo."','".substr($Empresa,0,strpos($Empresa,"-"))."','".substr($Ccostos,0,strpos($Ccostos,"-"))."','".substr($Activo,0,1)."','".$Documento."','".$Email."',SHA1('".$PasswordTemporal."'),'".$FechaPasswordTemp."','".$HoraPasswordTemp."')";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				break;
			}
		}
	}
	echo "<A HREF='usuarios.php'>Retornar</a><br>";
	echo "<input type='HIDDEN' name= 'pos' value='".$pos."'>";
	echo "<center><table border=0 aling=center id=tipo2>";
	$color="#CC99FF";
	if(isset($error))
		echo "<tr><td align=center bgcolor=".$color."><IMG SRC='/matrix/images/medical/root/Malo.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color."><font color=#000000 face='tahoma'><b>".$error."</b></font></td></tr>";
	echo "</table><br><br></center><br><br>";
	echo "<table border=0 align=center id='tipo1'>";
	if(isset($ok))
	{
		$query = "select Codigo, Password, Passdel, Feccap, Tablas, Descripcion, Prioridad, Grupo, Empresa, Ccostos, Activo, Documento, Email, PasswordTemporal, FechaPasswordTemp, HoraPasswordTemp  from usuarios where codigo='".$pos."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$row = mysql_fetch_array($err);
			$Codigo=$row[0];        
			$Password=$row[1];        
			$Passdel=$row[2];         
			$Feccap=$row[3];          
			$Tablas=$row[4];          
			$Descripcion=$row[5];     
			$Prioridad=$row[6];       
			$Grupo=$row[7];   
			$query="select Empdes,Emptcc from root_000050 where Empcod='".$row[8]."'  ";	
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_array($err1);       
			$Empresa=$row[8]."-".$row1[0]; 
			switch ($row1[1])
			{
				case "clisur_000003":
					$query="select Ccocod, Ccodes from clisur_000003 where Ccocod='".$row[9]."'  ";	
				break;
				case "farstore_000003":
					$query="select Ccocod, Ccodes from farstore_000003  where Ccocod='".$row[9]."'  ";	
				break;
				case "costosyp_000005":
					$query="select Ccocod, Cconom from costosyp_000005 where Ccocod='".$row[9]."'   ";	
				break;
				case "uvglobal_000003":
					$query="select Ccocod, Ccodes from uvglobal_000003 where Ccocod='".$row[9]."'   ";	
				break;
				default:
					$query="select Ccocod, Cconom from costosyp_000005  where Ccocod='' ";	
			}
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$row1 = mysql_fetch_array($err1);
			$Ccostos=$row1[0]."-".$row1[1]; 
			$Activo=$row[10];
			$Documento=$row[11];
			$Email=$row[12];
			$PasswordTemporal=$row[13];
			$FechaPasswordTemp=$row[14];
			$HoraPasswordTemp=$row[15];
			$wpar=1;
		}
		else
		{
			$Codigo="";        
			$Password=""; 
			$Passdel="";
			$Feccap="0000-00-00"; 
			$Tablas=""; 
			$Descripcion="";
			$Prioridad=0; 
			$Grupo=""; 
			$Empresa="";
			$Ccostos="";
			$Activo="";
			$Documento="";
			$Email="";
			$PasswordTemporal=""; 
			$FechaPasswordTemp="0000-00-00";
			$HoraPasswordTemp="00:00:00";
			$tablacc="";
			$wpar=2;
		}
	}
	echo "<input type='HIDDEN' name= 'wpar' value='".$wpar."'>";	
	echo "<tr>";
	echo "<td bgcolor=#999999><b>Item</td></b>";
	echo "<td bgcolor=#999999><b>Valor</b></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Codigo</td>";			
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Codigo' size=8 maxlength=8 value='".$Codigo."' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Password</td>";			
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Password' size=8 maxlength=8 value='".$Password."' class=tipo3></td>";
	echo "</tr>";	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Password <br>de Borrado</td>";			
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Passdel' size=8 maxlength=8 value='".$Passdel."' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Fecha Cambio<br> de Password</td>";			
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Feccap' size=10 maxlength=10 value='".$Feccap."' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Tablas<br> de Borrado</td>";	
	echo "<td bgcolor=#cccccc><textarea name='Tablas' cols=40 rows=5 class=tipo3>".$Tablas."</textarea>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Descripcion</td>";			
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Descripcion' size=52 maxlength=80 value='".$Descripcion."' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Prioridad</td>";			
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Prioridad' size=1 maxlength=1 value='".$Prioridad."' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Grupo</td>";
	echo "<td bgcolor=#cccccc><select name='Grupo' id=tipo1>";
	$query="select Descripcion from det_selecciones where medico='root' and codigo='grupos' order by Descripcion";		
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	for ($j=0;$j<$num1;$j++)
	{	
		$row1 = mysql_fetch_array($err1);
		if($row1[0] == $Grupo)
				echo "<option selected>".$row1[0]."</option>";
			else
				echo "<option>".$row1[0]."</option>";
	}	
	echo "</td></tr>";	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Empresa</td>";
	echo "<td bgcolor=#cccccc><select name='Empresa' onchange='enter()' id=tipo1>";
	$query="select Empcod, Empdes, Emptcc from root_000050 where Empest='on' ";		
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	echo "<option>SELECCIONE</option>";
	for ($j=0;$j<$num1;$j++)
	{	
		$row1 = mysql_fetch_array($err1);
		if($row1[0] == substr($Empresa,0,strpos($Empresa,"-")))
				echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
			else
				echo "<option>".$row1[0]."-".$row1[1]."</option>";
	}	
	echo "</select></td></tr>";
	if($Empresa != "" AND $Empresa != "SELECCIONE")
	{
		$query="select Emptcc from root_000050 where Empcod='".substr($Empresa,0,strpos($Empresa,"-"))."' and Empest='on' ";	
		$err1 = mysql_query($query,$conex);
		$row1 = mysql_fetch_array($err1);
		$tablacc = $row1[0];
	}
	else
		$tablacc = "";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Centro Costos</td>";
	echo "<td bgcolor=#cccccc>";
	switch ($tablacc)
	{
		case "clisur_000003":
			$query="select Ccocod, Ccodes from clisur_000003  ";	
		break;
		case "farstore_000003":
			$query="select Ccocod, Ccodes from farstore_000003  ";	
		break;
		case "costosyp_000005":
			$query="select Ccocod, Cconom from costosyp_000005  ";	
		break;
		case "uvglobal_000003":
			$query="select Ccocod, Ccodes from uvglobal_000003 ";	
		break;
		default:
			$query="select Ccocod, Cconom from costosyp_000005  where Ccocod='' ";	
	}
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	echo "<select name='Ccostos' id=tipo1>";
	for ($j=0;$j<$num1;$j++)
	{	
		$row1 = mysql_fetch_array($err1);
		if($row1[0] == substr($Ccostos,0,strpos($Ccostos,"-")))
				echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
			else
				echo "<option>".$row1[0]."-".$row1[1]."</option>";
	}	
	echo "</td></tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Activo</td>";
	echo "<td bgcolor=#cccccc>";			
	echo "<select name='Activo' id=tipo1>";
	if ($Activo == substr("A-Activo", 0, 1))
		echo "<option selected>A-Activo</option>";
	else
		echo "<option>A-Activo</option>";
	if ($Activo == substr("I-Inactivo", 0, 1))
		echo "<option selected>I-Inactivo</option>";
	else
		echo "<option>I-Inactivo</option>";	
	echo "</td>";	
	echo "</tr>";
	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Documento</td>";			
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Documento' size=15 maxlength=15 value='".$Documento."' class=tipo3></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Email</td>";			
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Email' size=52 maxlength=80 value='".$Email."' class=tipo3></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Password Temporal</td>";			
	echo "<td bgcolor=#cccccc><input type='TEXT' name='PasswordTemporal' size=8 maxlength=8 value='".$PasswordTemporal."' class=tipo3></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Fecha Password Temp</td>";			
	echo "<td bgcolor=#cccccc><input type='TEXT' name='FechaPasswordTemp' size=8 maxlength=8 value='".$FechaPasswordTemp."' class=tipo3></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Hora Password Temp</td>";			
	echo "<td bgcolor=#cccccc><input type='TEXT' name='HoraPasswordTemp' size=8 maxlength=8 value='".$HoraPasswordTemp."' class=tipo3></td>";
	echo "</tr>";
	
	echo "<td bgcolor=#cccccc>Datos Completos</td>";
	echo "<td bgcolor=#cccccc><input type='RADIO' name=ok value=1></td>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='GRABAR'></td>";
	echo "</tr>";
	echo "</table>";
	echo "<table border=0 align=center>";
	echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";
}
?>
</body>
</html>
