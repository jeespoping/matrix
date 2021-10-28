<html>
<head>
<title>MATRIX - SGC SISTEMA DE GESTION DE LA CALIDAD</title>
<style type="text/css">
	#tipoT02{color:#000000;background:#C3D9FF;font-size:12pt;font-family:Arial;font-weight:bold;width:52em;text-align:left;height:2em;}
	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;}
</style>
<!--

<script language="JavaScript">
var message="Botón derecho del ratón inhabilitado";
function click(e) {
if (document.all) {
if (event.button == 2) {
alert(message);
return false;
}
}
if (document.layers) {
if (e.which == 3) {
alert(message);
return false;
}
}
}
if (document.layers) {
document.captureEvents(Event.MOUSEDOWN);
}
document.onmousedown=click;
</script>
//-->
</head>

<body class="{layout: {type: 'border', resize: false, hgap: 6}}">
<?php
/**  .... Juan Carlos Hernández Marzo 18 de 2020
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	$IPOK=0;
	$query = "select ctanip, ctausu from proceso_000016 ";
	$query .= " where ctaest = 'on'";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if(($row[0] == substr($IIPP,0,strlen($row[0])) and $key == $row[1]) or ($row[0] == substr($IIPP,0,strlen($row[0])) and $row[1] == "*"))
			{
				$IPOK=1;
				$i=$num+1;
			}
		}
	}   .... Juan Carlos Hernández Marzo 18 de 2020
**/	  

	$IPOK=1;   //Esto se coloco para que deje ingresar por la VPN para el teletrabajo .... Juan Carlos Hernández Marzo 18 de 2020
	
	if($IPOK > 0)
	{
		echo "	<iframe src='http://promotora.vps.arkix.com/'></iframe>";
		echo "	 <style type='text/css'>";
		echo "	 html, body, div, iframe { margin:0; padding:0; height:100%; }";
		echo "	 iframe { display:block; width:100%; border:none; }";
		echo "	 </style>";
	}
	else
	{
		echo "<center><table border=0 align=center id=tipo5>";
		echo "<tr><td id=tipoT02><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;LA PAGINA DEL SISTEMA DE GESTION DE LA CALIDAD NO PUEDE SER USADA FUERA DE LA INSTITUCION !!!</td></tr>";
		echo "</table></center>";
	}
// }  .... Juan Carlos Hernández Marzo 18 de 2020
?>
</body>
</html>
