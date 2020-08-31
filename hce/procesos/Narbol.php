<html>
<head>
  <title>MATRIX</title>
  <script type="text/javascript">

  function teclado(e)  
	{ 
		var navegador = navigator.appName;
		var version = navigator.appVersion;
		if(navegador.substring(0,9) == "Microsoft")
		{
			if (event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13)  event.returnValue = false;
		}
		else
		{
			return (e.which >= 48 && e.which <= 57  || e.which == 13 || e.which < 32); //Solo números
		}
	}
</script>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Reorganizacion del Arbol de Formularios HCE</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Narbol.php Ver. 2011-10-05</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function validar1($chain)
{
	$decimal ="^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$";
	if (ereg($decimal,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
}
function validar2($chain)
{
	$regular="^(\+|-)?([[:digit:]]+)$";
	if (ereg($regular,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Narbol' action='Narbol.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(isset($ok))
	{
		for ($i=0;$i<$witem;$i++)
		{
			$query =  " update ".$empresa."_000009 set Precod = '".$val[$i][1]."' where Precod = '".$val[$i][0]."' ";
			$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ARCHIVO 09 ARBOL HCE : ".mysql_errno().":".mysql_error());
			$query =  " update ".$empresa."_000021 set Rararb = '".$val[$i][1]."' where Rararb = '".$val[$i][0]."' ";
			$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ARCHIVO 21 RELACION ROL ARBOL HCE : ".mysql_errno().":".mysql_error());
			$query =  " update ".$empresa."_000037 set Forcod = '".$val[$i][1]."' where Forcod = '".$val[$i][0]."' ";
			$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ARCHIVO 37 FORMULARIOS X SERVICIO : ".mysql_errno().":".mysql_error());
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>REORGANIZACION DEL ARBOL DE FORMULARIOS HCE</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>REORGANIZACION REALIZADA</b></td></tr>";
		echo "<td bgcolor=#cccccc colspan=8 align=center><input type='submit' value='CONTINUAR'></td><tr>";
		echo"</table>";
		unset($ok);
		unset($witem);
	}
	else
	{
		if(!isset($witem))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>REORGANIZACION DEL ARBOL DE FORMULARIOS HCE</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Numero de Items</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='witem' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			if ($witem>0)
			{
				$val=array();
				for ($i=0;$i<$witem;$i++)
				{
					$val[$i][0]="";
					$val[$i][1]="";
				}
			}

			echo "<table border=0 align=center>";
			echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/lmatrix.jpg' ></td><td  align=center bgcolor=#cccccc colspan=2><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=3 align=center><b>REORGANIZACION DEL ARBOL DE FORMULARIOS HCE</b></td></tr>";
			echo "<tr><td bgcolor=#CCCCCC align=center><b>NRo.</b></td><td bgcolor=#CCCCCC align=center><b>ITEM ACTUAL</b></td><td bgcolor=#CCCCCC align=center><b>ITEM NUEVO</b></td></tr>";

			if ($witem>0)
			{
				for ($i=0;$i<$witem;$i++)
				{
					if($i % 2 == 0)
						$color="#C3D9FF";
					else
						$color="#E8EEF7";
					echo "<tr><td bgcolor=".$color." align=center>".$i."</td><td bgcolor=".$color." align=center><input type='TEXT' name='val[".$i."][0]' size=20 maxlength=20 onkeypress='return teclado(event)'></td><td bgcolor=".$color." align=center><input type='TEXT' name='val[".$i."][1]' size=20 maxlength=20 onkeypress='return teclado(event)'></td>";
					echo "</tr>";

				}
				echo "<td bgcolor=#cccccc colspan=3 align=center>DATOS OK!! <input type='checkbox' name='ok'></td><tr>";
				echo "<td bgcolor=#999999 colspan=3 align=center><input type='submit' value='ACTUALIZAR'></td><tr>";
				echo "<input type='HIDDEN' name= 'witem' value='".$witem."'>";
			}
			echo"</table>";
		}
	}
}
?>
</body>
</html>
