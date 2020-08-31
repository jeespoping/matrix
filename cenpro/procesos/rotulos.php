<html>
<head>
  <title>APLICACION DE CENTRAL DE MEZCLAS</title>
  </head>
    <body>
<?php
include_once("conex.php");

/*========================================================DOCUMENTACION PROGRAMA================================================================================*/
/*
1. AREA DE VERSIONAMIENTO

Nombre del programa: ROTULOS.php
Fecha de creacion:  2007-06-15
Autor: Carolina Castano P
Ultima actualizacion: 2007-07-03


2. AREA DE DESCRIPCION:

Este script realiza las historias:
8. impresion de rotulo

3. AREA DE VARIABLES DE TRABAJO
$can         numerico     cantidad de prodcuto
$codigo      caracter     codigo del producto
$filas       numerico     numero de filas a imprimir
$lote        caracter     numero del lote
$nombre      caracter     nombre del producto
$nombre1     caracter     nombre del producto primera linea  modificar
$nombre2     caracter     nombre del producto segunda linea  modificar
$venci       date         fecha de vnecimiento

*////////////////////////////////////////////////////PROGRAMA/////////////////////////////////////////////////////////

if (!isset($user))
{
	if(!isset($_SESSION['user']))
	session_register("user");
}

if(!isset($_SESSION['user']))
{
	echo "error";
}
else
{
	$wbasedato='cenpro';
	

	or die("No se ralizo Conexion");
	


	$filas=ceil($can/3);
	if (!isset($nombre1))
	{
		$nombre2='';
		$exp=explode(' ',$nombre);
		$filas=ceil($can/3);

		$nombre1= $exp[0].' ';
		if (isset ($exp[1]))
		{
			$nombre1=$nombre1.$exp[1].' ';
		}
		if (isset ($exp[2]))
		{
			$nombre1=$nombre1.$exp[2];
		}
		if (isset ($exp[3]))
		{
			$nombre2=$exp[3].' ';
		}
		if (isset ($exp[4]))
		{
			$nombre2=$nombre2.$exp[4].' ';
		}
		if (isset ($exp[5]))
		{
			$nombre2=$nombre2.$exp[5];
		}
		if (isset ($exp[6]))
		{
			$nombre2=$nombre2.$exp[6];
		}

		$q= " SELECT Artdes "
		."       FROM ".$wbasedato."_000002 "
		."    WHERE Artcod = '".$codigo."' "
		."       AND Artest = 'on' ";


		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);
		$presentaciones[0]=$row[0];

		//consulto los conceptos
		$q =  " SELECT Subcodigo, Descripcion "
		."        FROM det_selecciones "
		."      WHERE Codigo != mid('".$presentaciones[0]."',1,instr('".$presentaciones[0]."','-')-1) AND "
		."        Medico = '".$wbasedato."' "
		."        AND Codigo = '02' "
		."        AND Activo = 'A' ";
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);

		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$presentaciones[$i]=$row1['Subcodigo'].'-'.$row1['Descripcion'];
			}

		}

		if ($presentaciones[0]!='')
		{
			$presentaciones[$i]='';
		}
		echo "<fieldset style='border:solid;border-color:#00008B; width=800' align=center></br>";
		echo "<form NAME='rotulo' ACTION='' METHOD='POST'>";
		echo "<table align='center'>";
		echo "<tr><td align=center bgcolor=#336699 ><font size=3  face='arial' color='#ffffff'>NOMBRE PARA ROTULO:</td></tr>";
		echo "<tr><td align=center bgcolor=#336699 ><font size=3  face='arial' color='#ffffff'>L1: </font><input type='text' name='nombre1' value='".$nombre1."' size='50'></td></tr>";
		echo "<tr><td align=center bgcolor=#336699 ><font size=3  face='arial' color='#ffffff'>L2: </font><input type='text' name='nombre2' value='".$nombre2."' size='50'></td></tr>";
		echo "<tr><td align=center bgcolor=#336699 ><font size=3  face='arial' color='#ffffff'>L3: </font>";
		echo "<select name='presentacion'>";
		if ($presentaciones!='')
		{
			for ($i=0;$i<count($presentaciones);$i++)
			{
				echo "<option>".$presentaciones[$i]."</option>";
			}
		}
		echo "</select></td></tr>";
		echo "</TABLE></br>";
		echo "<TABLE align=center><tr>";
		echo "<tr>";
		echo "<input type='hidden' name='can' value='".$can."' />";
		echo "<input type='hidden' name='lote' value='".$lote."' />";
		echo "<input type='hidden' name='codigo' value='".$codigo."' />";
		echo "<input type='hidden' name='venci' value='".$venci."' />";
		echo "<td align=center colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='ACEPTAR' ></td></tr>";
		echo "</TABLE>";
		echo "</td>";
		echo "</tr>	";
		echo "</form>";
		echo "</fieldset>";

	}
	else
	{
		echo "<table width='100%'>";
		for ($i=0; $i<$filas; $i++)
		{
			echo "<tr  height='100'>";
			for ($j=0; $j<3; $j++)
			{
				echo "<td width='20%'>";
				echo '<font size="3">';
				if (isset ($nombre1))
				{
					echo $nombre1;
				}

				if (isset ($nombre2) and $nombre2!='')
				{
					echo '<br>';
					echo $nombre2;
				}

				echo '<br></font><font size="3">';
				if ($presentacion!='')
				{
					$exp=explode('-',$presentacion);
					echo $exp[1];
					echo '<br>';
				}
				echo 'Codigo: '.$codigo;
				echo '<br>';
				echo 'Lote: '.$lote;
				echo '<br>';
				echo 'FV: '.$venci;
				echo '<br>';
				echo '</font>';
				echo "</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
}
?>
<body>
</html>
