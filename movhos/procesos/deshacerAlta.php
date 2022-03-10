<html>
<head>
<title>Levantar Altas</title>
<style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.tituloSup{color:#006699;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloSup1{color:#57C8D5;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo1{color:#FFFFFF;background:#006699;font-family:Arial;font-weight:bold;text-align:center;}
    	<!-- -->
    	.titulo2{color:#003366;background:#57C8D5;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#336666;background:#AAFFFF;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto{color:#006699;background:#CCFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
    	<!-- .acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.errorTitulo{color:#FF0000;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	-->
    	.alert{background:#FFFFAA;color:#FF9900;font-size:9pt;font-family:Arial;text-align:center;}
    	.warning{background:#FFCC99;color:#FF6600;font-size:9pt;font-family:Arial;text-align:center;}
    	.error{background:#FFAAAA;color:#FF0000;font-size:9pt;font-family:Arial;text-align:center;}
    	
    	.tituloA1{color:#FFFFFF;background:#660099;font-family:Arial;font-weight:bold;text-align:center;}
    	.textoA{color:#660066;background:#FFFFFF;font-size:10pt;font-family:Arial;}
    	    	
    </style>
</head>
<body>
<?php
$wemp_pmla = $_REQUEST['wemp_pmla'];

include_once("conex.php");
include_once("root/comun.php");

include_once("movhos/validacion_hist.php");
include_once("movhos/fxValidacionArticulo.php");
include_once("movhos/registro_tablas.php");
include_once("movhos/otros.php");

$db = $_REQUEST['db'];

$wactualiz = '2022-02-16';
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );
encabezado("DEVOLVER EL ALTA A UN PACIENTE ",$wactualiz, $wbasedato1);



echo "<center><table border=0 width=300>";

if(!isset($historia))
{
	echo "<form action='' method='POST'>";
	//echo "<td class='titulo1' colspan='2'>DEVOLVER EL ALTA A UN PACIENTE</td></tr>";
	//echo "<tr><td align=center class='tituloSup' colspan='2'><b>CLÍNICA LAS AMÉRICAS </b></td></tr>";
	echo "<tr>";
	echo "<td class='titulo2' ><b>N° HISTORIA: </font>";
	?>	<input type='text' cols='10' name='historia'>	<?php
	echo "</td>";
	echo "<td class='titulo2' ><b>Ingreso: </font>";
	?>	<input type='text' cols='4' name='ing'>	<?php
	echo "</td></tr>";
	echo"<tr><td  class='titulo1' colspan='2'><input type='submit' value='ACEPTAR'></td></tr>";
	echo "</form>";
}
else
{
	echo "<td class='titulo1' colspan='2'>DEVOLVER EL ALTA A UN PACIENTE</td></tr>";

	$pac['his']=$historia;
	$pac['ing']=$ing;

	$pac['act'] =infoPaciente($pac,$wemp_pmla);//paciente existio


	/**
		 * No hace falta buscar si la historia esta activa en UNIX, por que de no estarlo los programas uqeu cargan y
		 * devuelven a la cuenta del paciente no permitiran realizar transacciones al paciente.
		 */

	if($pac['act'])
	{
		$altas=true;
		echo "<form action='' method='POST'>";
		echo "<td class='titulo1' colspan='2'>".$pac['nom']."</td></tr>";
		echo "<tr><td class='titulo1' colspan='2'>HISTORIA:".$pac['his']."&nbsp;&nbsp;INGRESO:".$pac['ing']."</td>";
		echo "</tr><tr>";

		if(isset($def))
		{
			/**
			 * Deshacer el alta Definitiva
			 */
			$q = " UPDATE ".$bd."_000018 "
			."        SET Ubialp ='off', Ubiald='off' "
			."      WHERE Ubihis = '".$pac['his']."' "
			."        AND Ubiing = '".$pac['ing']."' ";
			$err = mysql_query($q,$conex);
			echo mysql_error();
			$num=mysql_affected_rows();
			if($num >0)
			{
				/**
				 * Hizo bien el UPDATE
				 */
				echo "<td class='titulo3' colspan='2'>Se levanto el alta Definitiva.</td></tr>";
			}
		}

		if(isset($bCco))
		{			
			for($i=0; $i<$bCco; $i++)
			{
				if(isset($cco[$i]))
				{
					$q = " UPDATE ".$bd."_000006 "
					."        SET Alta ='off' "
					."      WHERE Historia = '".$pac['his']."' "
					."        AND Ingreso = '".$pac['ing']."' "
					."        AND Cco = '".$cco[$i]."' ";
					$err = mysql_query($q,$conex);
					echo mysql_error();
					$num=mysql_affected_rows();
					if($num >0)
					{
						/**
						 * Hizo bien el UPDATE
						 */
						echo "<td class='titulo3' colspan='2'>Se levanto el alta para el Centro de costos:".$cco[$i]."</td></tr>";
					}
				}//fin del if(isset($cco[$bCco]))
			}//fin del for
		}


		$sinAltas=true;

		$q = " SELECT * "
		."       FROM ".$bd."_000018 "
		."      WHERE Ubihis = '".$pac['his']."' "
		."        AND Ubiing = '".$pac['ing']."' "
		."        AND (Ubialp = 'on' OR Ubiald='on') ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		$num=mysql_num_rows($err);
		if($num >0)
		{
			$sinAltas=false;
			$row=mysql_fetch_array($err);
			echo "<tr>";
			echo "<td class='texto'><input type='checkbox' name='def'></td>";
			echo "<td class='texto'><b>Alta definitiva de la institución</b></td>";
			echo "<tr>";
		}

		$q = " SELECT * "
		."       FROM ".$bd."_000006 "
		."      WHERE Historia = '".$pac['his']."' "
		."        AND Ingreso = '".$pac['ing']."' "
		."        AND Alta = 'on' ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		$num=mysql_num_rows($err);
		if($num >0)
		{
			$sinAltas=false;
			echo "<input type='hidden' name='bCco' value='$num'>";
			for($i=0; $i<$num; $i++)
			{
				$row=mysql_fetch_array($err);

				echo "<tr>";
				echo "<td class='texto'><input type='checkbox' name='cco[".$i."]' value='".$row['Cco']."'></td>";
				echo "<td class='texto'>Alta al Centro de costos: ".$row['Cco']."</td>";
				echo "<tr>";
			}
		}

		if($sinAltas)
		{
			echo "<td class='error' colspan='2'>El paciente no tiene ningun Alta</td></tr>";
		}
		else
		{
			echo "<input type='hidden' name='historia' value='".$pac['his']."'>";
			echo "<input type='hidden' name='ing' value='".$pac['ing']."'>";
			echo "<td class='texto' colspan='2'><input type='submit' value='ACEPTAR'></td></tr>";
		}

		echo "</form>";
	}
	else
	{
		echo "<td class='error' colspan='2'>NO EXISTEN REGISTROS EN MATRIX <BR> PARA UN PACIENTE CON HISTORIA=".$pac['his']." E INGRESO=".$pac['ing']."</td></tr>";
	}
}
echo "</table>";
?>
</body>
</html>