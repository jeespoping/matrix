<html>
<head>
<title>MATRIX - HCE-Historia Clinica Electronica</title>
<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" />	<!-- Nucleo jquery -->

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script> 	<!-- Nucleo jquery -->
<script type="text/javascript" src="../../../include/root/jquery-ui-1.7.2.custom.min.js"></script> 	<!--  -->
<script type="text/javascript" src="../../../include/root/jquery.metadata.js"></script> 	<!--  -->
<script type="text/javascript" src="../../../include/root/jquery.sizes.js"></script> 	<!--  -->
<script type="text/javascript" src="../../../include/root/jlayout.border.js"></script> 	<!--  -->
<script type="text/javascript" src="../../../include/root/jlayout.grid.js"></script> 	<!--  -->
<script type="text/javascript" src="../../../include/root/jlayout.flexgrid.js"></script> 	<!--  -->
<script type="text/javascript" src="../../../include/root/jlayout.flow.js"></script> 	<!--  -->
<script type="text/javascript" src="../../../include/root/jquery.jlayout.js"></script> 	<!--  -->
<script type="text/javascript" src="../../Jquery/ui.draggable.js"></script>

<script type="text/javascript" src="../../../include/root/jquery.blockUI.js"></script> <!-- Block UI -->
<script type="text/javascript">
$(document).ready(function(){
	
	var container = $('body'),
		west = $('body > .west'),
		east = $('body > .east'),
		center = $('body > .center');

function layout() {
	container.layout();
}
//Contenedores jlayout
west.layout();
layout();
$(window).resize(layout);
//$("#flotanteIframe").draggable();

});

function cerrarModal(){
	$.unblockUI();
}

function ocultarFlotante(numdiv)
{
	//debugger;
	//$('#flotanteIframe'+numdiv).hide();
	document.getElementById("flotanteIframe"+numdiv).style.display="none";
}
</script>

<style>
			html, body {
				width: 100%;
				height: 100%;
				padding: 0;
				margin: 0;
				overflow: hidden;
			}

			.north {
				width: 100%;
				height: 15.3%;
			}

			.east {
				width: 5.5%;
				height: 100%;
				font-size: 0.78em;
			}

			.west {
				width: 260px;
				font-size: 0.78em;
			}

			.west .panel {
				width: 260px;
			}
		</style>

</head>
<body class="{layout: {type: 'border', resize: false, hgap: 6}}">
<?php
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//	16/05/2022 - Brigith Lagares:  se corrige el tamaño del iframe del encabezado 
//
//	11/03/2022 - Brigith Lagares:  Se realiza estadarización del wemp_pmla
//
//	2020-05-20	-	Jessica Madrid Mejía:	Se valida si en la tabla movhos_000282 el centro de costos tiene 
// 											restricción y de ser así si el usuario esta habilitado para acceder 
// 											a los pacientes de dicho centro de costos.	
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
if(isset($_REQUEST['origen']) && !isset($_REQUEST['wemp_pmla'])){
	$wemp_pmla=$_REQUEST['origen'];
}
elseif(isset($_REQUEST['wemp_pmla'])){
	$wemp_pmla = $_REQUEST['wemp_pmla'];
}
else{
	die('Falta parametro wemp_pmla...');
}
include_once("conex.php");
//$wemp_pmla=$origen;
function GetIP()
{
	$IP_REAL = " ";
	$IP_PROXY = " ";
	if (@getenv("HTTP_X_FORWARDED_FOR") != "") 
	{ 
		$IP_REAL = getenv("HTTP_X_FORWARDED_FOR"); // Muestra la IP real del usuario, es decir, la Pública 
		$IP_PROXY = getenv("REMOTE_ADDR"); // Muestra la IP de un posible Proxy 
	} 
	else 
	{ 
		$IP_REAL = getenv("REMOTE_ADDR"); // En caso de que no exista un Proxy solo mostrara la IP Publica del visitante 
	}
	$IPS=$IP_REAL."|".$IP_PROXY;
	return $IPS;
}

function consultarCcoPaciente($conex, $wdbmhos, $historia, $ingreso)
{
	$queryUbicacion = "SELECT Ubisac 
						 FROM ".$wdbmhos."_000018 
						WHERE Ubihis='".$historia."' 
						  AND Ubiing='".$ingreso."';";
	
	$resUbicacion = mysqli_query($conex,$queryUbicacion) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
	$numUbicacion = mysql_num_rows($resUbicacion);
	
	$ccoPaciente = "";
	if($numUbicacion>0)
	{
		$rowUbicacion = mysqli_fetch_array($resUbicacion);
		$ccoPaciente = $rowUbicacion['Ubisac'];
	}
	
	return $ccoPaciente;
}

function consultarCcoRestriccion($conex, $wdbmhos, $ccoPaciente)
{
	$query = "SELECT Cracco 
				FROM ".$wdbmhos."_000282 
			   WHERE Cracco='".$ccoPaciente."' 
			     AND Craest='on' 
			   LIMIT 1;";
		
	$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
	$num = mysql_num_rows($res);
	
	$ccoConRestriccion = false;
	if($num>0)
	{
		$ccoConRestriccion = true;
	}
	
	return $ccoConRestriccion;
}

function consultarUsuarioPermitido($conex, $wdbmhos, $ccoPaciente, $usuario)
{
	$query = "SELECT Cracco 
				FROM ".$wdbmhos."_000282 
			   WHERE Cracco='".$ccoPaciente."' 
			     AND Crausu='".$usuario."' 
			     AND Craest='on';";
		
	$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
	$num = mysql_num_rows($res);
	
	$usuarioHabilitado = false;
	if($num>0)
	{
		$usuarioHabilitado = true;
	}
	
	return $usuarioHabilitado;
}

function consultarUsuarioHabilitado($conex, $wemp_pmla, $wdbmhos, $usuario, $wcedula, $wtipodoc, $whisa, $winga)
{
	$historia = "";
	$ingreso = "";
	
	if(isset($whisa) && isset($winga))
	{
		$historia = $whisa;
		$ingreso = $winga;
	}
	else
	{
		$queryPaciente = "SELECT Orihis, Oriing 
							FROM root_000037 
						   WHERE Oriced='".$wcedula."' 
							 AND Oritid='".$wtipodoc."' 
							 AND Oriori='".$wemp_pmla."';";
		
		$resPaciente = mysqli_query($conex,$queryPaciente) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		$numPaciente = mysql_num_rows($resPaciente);
		
		if($numPaciente>0)
		{
			$rowPaciente = mysqli_fetch_array($resPaciente);
			
			$historia = $rowPaciente['Orihis'];
			$ingreso = $rowPaciente['Oriing'];
		}
	}
	
	$usuarioHabilitado = true;
	if($historia!="" && $ingreso!="")
	{
		$ccoPaciente = consultarCcoPaciente($conex, $wdbmhos, $historia, $ingreso);
		
		if($ccoPaciente != "")
		{
			$ccoConRestriccion = consultarCcoRestriccion($conex, $wdbmhos, $ccoPaciente);
			if($ccoConRestriccion)
			{
				$usuarioHabilitado = consultarUsuarioPermitido($conex, $wdbmhos, $ccoPaciente, $usuario);
			}
		}
	}
	
	return $usuarioHabilitado;
}

@session_start();
if(!isset($_SESSION['user']))
{
	if(isset($key))
	{
		
		$user="1-".strtolower($key);
		$usera=strtolower($key);
		$ipdir=explode("|",GetIP());
		$IIPP=$ipdir[0];
		$_SESSION["user"]  = $user;
		$_SESSION["usera"] = $usera;
		$_SESSION["IIPP"]  = $IIPP;
		$sess=1;
	}
	else
		echo "Error en Session ";
}
else
	$sess=1;
if(isset($sess))
{
	$key = substr($user,2,strlen($user));
	
	$usuarioHabilitado = consultarUsuarioHabilitado($conex, $wemp_pmla, $wdbmhos, $key, $wcedula, $wtipodoc, $whisa, $winga);
	
	if($usuarioHabilitado)
	{	
		$IPOK=0;
		$query = "select ctanip, ctausu from ".$empresa."_000039 ";
		$query .= " where ctaest = 'on'";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if(($row[0] == substr($IIPP,0,strlen($row[0])) and $key == $row[1]) or ($row[0] == substr($IIPP,0,strlen($row[0])) and $row[1] == "*") or ($row[0] == "*" and $key == $row[1]))
				{
					$IPOK=1;
					$i=$num+1;
				}
			}
		}
		if($IPOK > 0)
		{
			if(!isset($wservicio))
				$wservicio = "*";
			echo "<div id='msjEsperePadre' style='display:none;'></div>"; 
				
			echo "<div class='north'>";
			if(isset($whisa))
				echo "<iframe id='fd' name='fd' src='HCE.php?accion=T&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wdbmhos=".$wdbmhos."&whisa=".$whisa."&winga=".$winga."' scrolling=no frameborder=0 style='width:100%;height:110;border:0px;dotted #FFFFFF;margin-left: -0px; margin-top: -10px;' allowTransparency='true'></iframe>";
			else
				echo "<iframe id='fd' name='fd' src='HCE.php?accion=T&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wdbmhos=".$wdbmhos."' scrolling=no frameborder=0 style='width:100%;height:110;border:0px;dotted #FFFFFF;margin-left: -0px; margin-top: -10px;' allowTransparency='true'></iframe>";
			echo "</div>";

			echo "<div class='center'>";
			if(isset($whisa))
			{
				echo "<iframe id='fd1' src='HCE.php?accion=D&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wservicio=".$wservicio."&wdbmhos=".$wdbmhos."&whisa=".$whisa."&winga=".$winga."' name='demograficos' scrolling=no frameborder=0 style='width:100%;height:25%;border:0px;dotted #FFFFFF;margin-left: -0px; margin-top: -0px;' allowTransparency='true'></iframe>";
				echo "<iframe id='fd2' src='HCE.php?accion=M&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wdbmhos=".$wdbmhos."&whisa=".$whisa."&winga=".$winga."' name='principal' frameborder=0 style='width:100%;height:75%;border:0px;dotted #FFFFFF;margin-top: -0px;' allowTransparency='true'></iframe>";
			}
			else
			{
				echo "<iframe id='fd1' src='HCE.php?accion=D&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wservicio=".$wservicio."&wdbmhos=".$wdbmhos."' name='demograficos' scrolling=no frameborder=0 style='width:100%;height:25%;border:0px;dotted #FFFFFF;margin-left: -0px; margin-top: -0px;' allowTransparency='true'></iframe>";
				echo "<iframe id='fd2' src='HCE.php?accion=M&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wdbmhos=".$wdbmhos."' name='principal' frameborder=0 style='width:100%;height:75%;border:0px;dotted #FFFFFF;margin-top: -0px;' allowTransparency='true'></iframe>";
			}
			//echo "<iframe id='fd3' src='HCE.php?accion=H&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' name='vistas' frameborder=0 marginheight=2 style='width:100%;height:9%;border:0px;dotted #FFFFFF;margin-top: 0px;' allowTransparency='true' scrolling=no></iframe>";
			echo "</div>";

			echo "<div class=\"west {layout: {type: 'grid', columns: 1, resize: false}}\">";
			echo "<div class='panel' align='center'>";
			if(isset($whisa))
			{
				echo "<iframe id='f1' name='f1' src='HCE.php?accion=U&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wdbmhos=".$wdbmhos."&whisa=".$whisa."&winga=".$winga."' scrolling=no frameborder=0 style='width:100%;height:11%;border:0px;dotted #FFFFFF;'allowTransparency='true'></iframe>";
				echo "<iframe id='f2' name='f2' src='HCE.php?accion=A&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wdbmhos=".$wdbmhos."&whisa=".$whisa."&winga=".$winga."' frameborder=0 scrolling=yes  style='width:100%;height:20%;border:0px;dotted #FFFFFF;'allowTransparency='true'></iframe>";
				echo "<iframe id='f3' name='f3' src='HCE.php?accion=F&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wservicio=".$wservicio."&wdbmhos=".$wdbmhos."&whisa=".$whisa."&winga=".$winga."' frameborder=0 style='width:100%;height:69%;border:0px;dotted #FFFFFF;'allowTransparency='true';scrolling=auto></iframe>";
			}
			else
			{
				echo "<iframe id='f1' name='f1' src='HCE.php?accion=U&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wdbmhos=".$wdbmhos."' scrolling=no frameborder=0 style='width:100%;height:11%;border:0px;dotted #FFFFFF;'allowTransparency='true'></iframe>";
				echo "<iframe id='f2' name='f2' src='HCE.php?accion=A&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wdbmhos=".$wdbmhos."' frameborder=0 scrolling=yes  style='width:100%;height:20%;border:0px;dotted #FFFFFF;'allowTransparency='true'></iframe>";
				echo "<iframe id='f3' name='f3' src='HCE.php?accion=F&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wservicio=".$wservicio."&wdbmhos=".$wdbmhos."' frameborder=0 style='width:100%;height:69%;border:0px;dotted #FFFFFF;'allowTransparency='true';scrolling=auto></iframe>";
			}
			echo "</div>";
			echo "</div>";
			echo "</div>";

			echo "<div class='east'>";
			//echo "<iframe id='f2' name='f2' src='HCE.php?accion=UT&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' frameborder=0 scrolling=no  style='position: absolute;width:100%;height:100%;border:0px;dotted #FFFFFF;'allowTransparency='true'></iframe>";
			if(isset($whisa))
				echo "<iframe id='f2' name='f2' src='HCE.php?accion=UT&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wservicio=".$wservicio."&wdbmhos=".$wdbmhos."&whisa=".$whisa."&winga=".$winga."' frameborder=0 scrolling=no  style='width:100%;height:100%;border:0px;dotted #FFFFFF;'allowTransparency='true'></iframe>";
			else
				echo "<iframe id='f2' name='f2' src='HCE.php?accion=UT&ok=0&empresa=".$empresa."&origen=".$wemp_pmla."&wemp_pmla=".$wemp_pmla."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wservicio=".$wservicio."&wdbmhos=".$wdbmhos."' frameborder=0 scrolling=no  style='width:100%;height:100%;border:0px;dotted #FFFFFF;'allowTransparency='true'></iframe>";
			echo "</div>";
		}
	}
	else
	{
		echo "	<div style='background:#E8EEF7;width:100vw;height:100vh;align-content:center;justify-content:center;display:flex;align-items:center; font-size:15pt; color: #000066;flex-direction:column;'>
					El usuario no esta habilitado para acceder a la HCE de pacientes en este centro de costos.
					<img src='/matrix/images/medical/HCE/button.gif' onclick='javascript:top.close();' style='margin:10px'>
				</div>";
	}
	
	
}
else
	echo "Error en Session ";
?>
</body>
</html>
