<html>
<head>
  <title>MATRIX - ITEM 2012-05-31</title>
	<style type="text/css">
	<!--
		.BlueThing
		{
			background: #CCCCFF;
		}
		
		.SilverThing
		{
			background: #CCCCCC;
		}
		
		.GrayThing
		{
			background: #999999;
		}
	
	//-->
	</style>
	<script type="text/javascript">
		<!--
			function ejecutar(path)
			{
				window.open(path,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
			}
			function mostrar(pos) 
			{
				alert(pos);
				document.forms.inventario.position.value=pos;
				document.forms.inventario.submit();
		    }
		    function onover() 
			{
				document.buscar.src="/matrix/images/medical/root/buscar2.ico";
		    }
		    function goBack()
			{
				window.history.back()
			}
		//-->
	</script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">

<!--=========================================================================================================================================================================================================================//-->
<!--Mensaje Temporal//-->
<!--=========================================================================================================================================================================================================================//-->
<!--
<center><table>
<tr>
<td align=center><font size=5><EM><b>!! FAVOR AVISAR ¡¡ &nbsp</b></em>SI USTED CORRE UN PROCESO EN MATRIX Y CONSIDERA QUE ESTA MUY DEMORADO, POR FAVOR AVISAR INMEDIATAMENTE A LAS EXTENSIONES 1024, 1219, 2026 o 2216</td></font>
</tr>
</table>
<!--=========================================================================================================================================================================================================================//-->
		
		
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Opciones del Grupo de Informacion</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>item.php Ver. 2011-06-03</b></font></tr></td></table><br><br>
</center>

<?php
include_once("conex.php");
		     /* function onout() 
			{
				alert('out');
				document.buscar.src="/matrix/images/medical/root/buscar1.ico";
		    }*/

//if(path == 1){
					//window.open('/matrix/farmastore/procesos/inventario.php?ok=9','','fullscreen=1,status=0,menubar=0,toolbar =0,directories =0,resizable=0');}
@session_start();
if(!isset($_SESSION['user']))
echo "error";
else
	{
	if(isset($position))
		echo "Posicion : ".$position;
	$key = substr($user,2,strlen($user));
	echo "<form name='inventario' action='item.php' method=post>";
	

	

	switch($grupo)
	{
		case 'gerencia':
			$query = "select descripcion from root_000002 where codigo ='".$codigo."'";
		break;
		case 'sif':
			$query = "select descripcion from root_000013 where codigo ='".$codigo."'";
		break;
		case 'sic':
		    if($codigo == "001")
				$user="1-costosyp";
			$query = "select descripcion from root_000015 where codigo ='".$codigo."'";
		break;
		case 'AMERICAS':
			$query = "select descripcion from root_000020 where codigo ='".$codigo."'";
		break;
	}
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	switch($grupo)
	{
		case 'gerencia':
			$query = "select codigo_opcion,descripcion,programa,ruta from root_000003 where codigo_grupo='".$codigo."' order by codigo_opcion";
		break;
		case 'sif':
			$query = "select codigo_opcion,descripcion,programa,ruta from root_000014 where codigo_grupo='".$codigo."' order by codigo_opcion";
		break;
		case 'sic':
		    if($codigo == "001")
				$user="1-costosyp";
			$query = "select codigo_opcion,descripcion,programa,ruta from root_000016 where codigo_grupo='".$codigo."' and usuarios like '%".$usera."%' order by codigo_opcion";
		break;
		case 'AMERICAS':
			$query = "select codopt,descripcion,programa,ruta from root_000021 where codgru='".$codigo."' and (usuarios like '%-".$usera."-%' ";
			$query .= " or usuarios like '".$usera."-%' ";
			$query .= " or usuarios like '%-".$usera."' ";
			$query .= " or usuarios = '".$usera."') ";
			$query .= " order by codopt";
		break;
	}
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	echo "<center>";
	echo "<table border=0 align=center>";
	echo "<tr><td align=center bgcolor='#cccccc'><font size=5 face='Tahoma'> <b>".$row[0]." -  Grupo : ".$codigo."</b></font></tr></td></table><br><br>";
	echo "</center>";
	if ($num > 0)
	{
		$color="#999999";
		echo "<table border=0 align=center>";
		echo "<tr>";
  		echo "<td bgcolor=".$color."><b>Opcion Nro.</b></td>";
  		echo "<td bgcolor=".$color."><b>Descripcion</b></td>";
  		echo "<td bgcolor=".$color."><b>Programa</b></td>";
		echo "</tr>";
		$r = 0;
		echo "<input type='HIDDEN' name= 'position'>";
		echo "<input type='HIDDEN' name= 'codigo' value='".$codigo."'>";
		echo "<input type='HIDDEN' name= 'grupo' value='".$grupo."'>";
		for ($i=0;$i<$num;$i++)
		{
			if ($r == 0)
			{
				$color="#CCCCCC";
				$r = 1;
			}
			else
			{
				$color="#999999";
				$r = 0;
			}
			$row = mysql_fetch_array($err);
			if($r == 0)
				echo "<tr onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." bgcolor=".$color.">";
			else
				echo "<tr onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='SilverThing';".chr(34)." bgcolor=".$color.">";
			$k=$i+1;
			while(strlen($k) < 4)
				$k="0".$k;
			//echo "<td align=center onclick='mostrar(".chr(34).$k.chr(34).")'>".$k."</td>";
			echo "<td align=center>".$k."</td>";
			echo "<td>".$row[1]."</td>";
			if(strtoupper(substr($row[3],0,3)) == "JSP")
			{
				$path=substr($row[3],3).$row[2];
				echo "<td align=center><input type='button' value='Ok'  onclick='ejecutar(".chr(34).$path.chr(34).")'></td>";
				//echo "<td align=center onmouseover='document.images.buscar".$i.".src=".chr(34)."/matrix/images/medical/root/check.ico".chr(34)."' onmouseout='document.images.buscar".$i.".src=".chr(34)."/matrix/images/medical/root/ok.ico".chr(34)."'><IMG SRC='/matrix/images/medical/root/ok.ico' name='buscar".$i."' onclick='ejecutar(".chr(34).$path.chr(34).")'></td>";
			}
			elseif(substr($row[3],0,4) == "http")
					echo "<td align=center><A HREF='".$row[2]."'>Ejecutar</td>";
				else
					echo "<td align=center><A HREF='".$row[3].$row[2]."'>Ejecutar</td>";
			echo "</tr>";
		}
		echo "</tabla>";
		echo "<table border=0 align=center>";
		if(isset($ret) and $ret == 1)
		{
			echo "<tr><td align=center><A HREF='#Arriba'><B>Arriba</B></A></td></tr>";
			echo "<tr><td align=center><input type='button' value='Retornar' onclick='goBack()' /></td></tr></table>";	
		}
		else
		{
			echo "<tr><td align=center><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";
		}
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
