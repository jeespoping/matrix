<html>
<head>
<title>Rep Devoluciones</title>
<style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.tituloSup{color:#006699;background:#FFFFF;font-size:13pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloPeq{color:#006699;background:#FFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloSup1{color:#57C8D5;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	<!-- -->
    	<!--.titulo2{color:#003366;background:#57C8D5;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}-->
    	.titulo2{color:#003366;background:#4DBECB;font-size:8pt;font-family:Arial;text-align:center;}
    	.titulo3{color:#0A3D6F;background:#61D2DF;font-size:8pt;font-family:Arial;text-align:center;}
    	.texto{color:#006699;background:#CCFFFF;font-size:8pt;font-family:Tahoma;text-align:center;}
    	.acumulado1{color:#003366;background:#FFCC66;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
    	    	.errorTitulo{color:#FF0000;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo2{color:#003366;background:#4DBECB;font-size:8pt;font-family:Arial;text-align:center;}
    	.errorTit{background:#FFAAAA;color:#FF0000;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.errorTit1{background:#FFAAAA;color:#FF0000;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.alert{background:#FFFFAA;color:#FF9900;font-size:8pt;font-family:Arial;text-align:center;}
    	.warning{background:#FFCC99;color:#FF6600;font-size:8pt;font-family:Arial;text-align:center;}
    	.error{background:#FFAAAA;color:#FF0000;font-size:8pt;font-family:Arial;text-align:center;}
    	
    	.tituloA1{color:#FFFFFF;background:#660099;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#660066;background:#FFFFFF;font-size:8pt;font-family:Arial;}
    	.exito{color:#006699;background:#AADDDD;font-size:8pt;font-family:Tahoma;text-align:center;font-weight:bold}
    	    	
    </style>
</head>
<body>
<?php
include_once("conex.php");

if(!isset($_SESSION['user']))
echo "error";
else
{
	include_once("movhos/otros.php");
	include_once("movhos/validacion_hist.php");
	

	

	$usu['codM']=substr($user,2);

	echo "<table align='center' border='0'>";
	echo "<td class='tituloSup' >REPORTE DEVOLUCIONES</td></tr>";
	echo "<td class='tituloPeq' >repDevolucion.php Version 2007-06-19 <br><br></td></tr>";
	echo "<tr>";

	if(!isset($devCons))
	{
		/**
		 * Pide la historia y el ingreso
		 */
		echo "<form action='' method='POST'>";
		echo "<td><center><table border='0' width=300>";
		//echo "<tr><td align=center class='titulo2' colspan='2'><b>DEVOLUCIÓN</b></td></tr>";
		echo "<tr>";
		echo "<td class='titulo2' ><b>Devolución Número: </b><input type='text' size='10' name='historia'></td>";
		echo "</form>";
	}
	else
	{
		
		$table="repDev".date('Ymdhis');
		$q = "CREATE TEMPORARY TABLE ".$table." "
		." SELECT Devces,  Devjud, Devcfs, Devjus, Devnum, Devlin, Fenhis, Fening, Fenfue,Fencco, Fentip, 'artcod' as Artc, 'artnom' as Artn , 0 as Des, '' as Dde "
		."      FROM ".$bd."_000028, ".$bd."_000002 "
		."     WHERE Devcon = ".$devCons." "
		."       AND Fennum = Devnum ";
		$err = mysql_query($q,$conex);
		echo mysql_error();

		$q= "ALTER TABLE `".$table."` CHANGE `Artc` `Artc` VARCHAR( 10 ) ";//AND `Artn` `Artn` VARCHAR( 80 ) ";	
		$err = mysql_query($q,$conex);
		echo mysql_error();
		
		$q= "ALTER TABLE `".$table."` CHANGE `Artn` `Artn` VARCHAR( 80 ) ";//AND `Artn` `Artn` VARCHAR( 80 ) ";	
		$err = mysql_query($q,$conex);
		echo mysql_error();
		
		$q= "ALTER TABLE `".$table."` CHANGE `Dde` `Dde` VARCHAR( 80 ) ";//AND `Artn` `Artn` VARCHAR( 80 ) ";	
		$err = mysql_query($q,$conex);
		echo mysql_error();
		
		
		$q = "UPDATE ".$table.", ".$bd."_000003 "
		."       SET Artc = Fdeart "
		."     WHERE Devlin <> 0 "
		."       AND Fdenum = Devnum "
		."       AND Fdelin = Devlin ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		
		$q = "UPDATE ".$table.", ".$bd."_000003 "
		."       SET Artc = Fdeari "
		."     WHERE Devlin = 0 "
		."       AND Fdenum = Devnum ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
				
		$apl="repDevApl".date('Ymdhis');
		$q = " CREATE TEMPORARY TABLE ".$apl." "
		."     SELECT * "
		."       FROM ".$bd."_000015  "
		."      WHERE Aplnde = '".$devCons."' ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		
		$q = "UPDATE ".$table.", ".$apl."  "
		."       SET Des = Aplaap, Artn = Apldes, Dde=Apldde "
		."     WHERE Artc = Aplart "
		."       AND Fenfue = Aplfde ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
				
		$q = "DELETE FROM ".$apl." "
		."     WHERE EXISTS ( "
		."                         SELECT * "
		."                           FROM ".$table." "
		."                          WHERE Artc = Aplart "
		."                            AND Fenfue = Aplfde "
		."                        ) ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		
		$q = "UPDATE ".$table.", ".$bd."_000026 "
		."       SET Artn = Artcom "
		."     WHERE Artn = 'artnom' "
		."       AND Devlin <> 0 "
		."       AND Artcod = Artc ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		
		$q = "UPDATE ".$table.", cenpro_000002 "
		."       SET Artn = Artcom "
		."     WHERE Devlin = 0 "
		."       AND Artn ='artnom' "
		."       AND Artcod = Artc ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
				
		$q = "INSERT INTO ".$table." "	
		."    SELECT      0,    NULL,      0,   NULL,    NULL,  NULL,   Aplhis, Apling, Aplfde, Aplcco,     '',           Aplart,            Apldes,  Aplaap, Apldde  "
		."      FROM ".$apl." ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		
		$q = " SELECT * "
		."       FROM ".$table." "
		."   ORDER BY Fencco, Artc ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		$ccoNum=-1;
		$cco[$ccoNum]['cod']="";
		$artNum=0;
		$num = mysql_num_rows($err);
		if($num >0)
		{
			for($i=0;$i<$num;$i++)
			{
				$row=mysql_fetch_array($err);
				if($i==0)
				{
					$pac['his']=$row['Fenhis'];
					/**
						 * Traer información del paciente
						 */
					infoPaciente(&$pac, $emp);
					$pac['ing']=$row['Fening'];
					echo "<td><table border=0>";
					echo "<td class='tituloSup' colspan='8'>Historia:".$pac['his']."&nbsp;&nbsp;&nbsp;Ingreso:".$pac['ing']."</td></tr>";
					echo "<td class='tituloSup' colspan='8'>Devolución Número ".$devCons."</td></tr>";
				}

				if($cco[$ccoNum]['cod'] != $row['Fencco'])
				{
					$ccoNum=$ccoNum+1;
					$cco[$ccoNum]['cod']=$row['Fencco'];
					getCco(&$cco[$ccoNum],"D",$emp);
					echo "<tr><td><td><tr>";
					echo "<tr><td class='acumulado1' colspan='8'>".$cco[$ccoNum]['cod']."-".$cco[$ccoNum]['nom']."<td><tr>";
					echo "<tr><td class='titulo2'>Artículo</td>";
					echo "<td class='titulo2'>Fuente</td>";
					echo "<td class='titulo2'>Dev</td>";
					echo "<td class='titulo2'>Justificación Dev</td>";
					echo "<td class='titulo2'>Fal.</td>";
					echo "<td class='titulo2'>Justificación Faltante</td>";
					echo "<td class='titulo2'>Descarte</td>";
					echo "<td class='titulo2'>Destino Descarte</td>";
					echo "</tr>";
					/*
					$q = " SELECT * "
					."       FROM ".$bd."_000015 "
					."      WHERE Aplnde = '".$devCons." ";
					$err1 = mysql_query($q,$conex);
					echo mysql_error();
					$num1 = mysql_num_rows($err);
					if($num1 >0)
					{

					for($j=0;$j<$num1;$j++)
					{
					$row1=mysql_fetch_array($err1);
					}
					}
					*/
				}
				$artNom="";
				/**
					 * Buscar si hubo descarte
					 */


				/**
					 * Si no hubo descarte buscar el nombre del artículo
					 */
				/*
				$k=0;
				while ($k<$artNum and $artNom == "")
				{
				if($art[$k]['cod']== $row['Fdeart'])
				{
				$artNom=$art[$k]['nom'];
				}
				$k=$k+1;
				}
				if($artNom == "")
				{
				/**
				* Todavía no se ha encontrado el nombre del artículo buscar en el maestro
				*---
				$q = "SELECT Artcom, Artuni, Artgru "
				."      FROM ".$bd."_000026 "
				."     WHERE Artcod='".strtoupper($row['Fdeart'])."' ";
				$err1 = mysql_query($q,$conex);
				echo mysql_error();
				$num1 = mysql_num_rows($err1);
				if ($num1>0)
				{
				$row1= mysql_fetch_array($err);
				$art[$artNum]['cod']=$row['Fdeart'];
				$art[$artNum]['nom']= $row1['Artcom'];
				$art[$artNum]['uni']= $row1['Artuni'];
				$art[$artNum]['gru']= $row1['Artgru'];

				$artNom=$art[$artNum]['nom'];
				$artNum=$artNum+1;
				}
				else
				{
				/**
				* No esta en el maestro de artículos buscar en el maestro del centro productivo
				*---
				$q = " SELECT * "
				."      FROM cenpro_000002 "
				."     WHERE Artcod = '".$artCod."' ";
				$err1 = mysql_query($q,$conex);
				echo mysql_error();
				$num1 = mysql_num_rows($err1);
				if($num1 >0)
				{
				$row1=mysql_fetch_array($err1);
				$art[$artNum]['cod']=$row['Fdeart'];
				$art[$artNum]['nom']=$row1['Artcom'];

				$artNom=$art[$artNum]['nom'];
				$artNum=$artNum+1;
				}
				else
				{
				$artNom="";
				}
				}
				}// Fin del if($artNom == "")

				*/
				echo "<tr><td class='texto'>".$row['Artc']."-".$row['Artn']."</td>";
				if(substr($row['Fentip'], 1,1) == "P")
				{
					$tip="Simple";
				}
				else
				{
					$tip="Aprov.";
				}
				echo "<td class='texto'>".$tip." ".$row['Fenfue']."</td>";
				echo "<td class='texto'>".$row['Devces']."</td>";
				echo "<td class='texto'>".$row['Devjud']."</td>";
				echo "<td class='texto'>".$row['Devcfs']."</td>";
				echo "<td class='texto'>".$row['Devjus']."</td>";
				echo "<td class='texto'>".$row['Des']."</td>";
				echo "<td class='texto'>".$row['Dde']."</td>";
				echo "</tr>";



			}// fin del for($i=0;$i<$num;$i++)
			echo "</table>";
		}//fin del if($num>0)
	}
}

?>

</body>
</html>