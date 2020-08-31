<html>
<head>
<title>Reporte</title>
<style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.tituloSup{color:#006699;background:#FFFFF;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloSup1{color:#006699;background:#FFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloSup2{color:#4DBECB;background:#FFFFF;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo1{color:#FFFFFF;background:#006699;font-size:12pt;font-family:Arial;text-align:center;}
    	<!-- -->
    	<!--.titulo2{color:#003366;background:#57C8D5;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}-->
    	.titulo2{color:#003366;background:#4DBECB;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#0A3D6F;background:#61D2DF;font-size:11pt;font-family:Arial;text-align:center;}
    	.texto{color:#006699;background:#CCFFFF;font-size:10pt;font-family:Tahoma;text-align:center;}
    	.texto2{color:#006699;background:#CCFFFF;font-size:11pt;font-family:Tahoma;font-weight:bold;}
    	.texto3{color:#FFFFF;background:red;font-size:10pt;font-family:Tahoma;text-align:center;}
    	<!-- .acumulado1{color:#003366;background:#FFCC66;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	-->
    	.errorTitulo{color:#FF0000;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}

    	.alert{background:#FFFFAA;color:#FF9900;font-size:10pt;font-family:Arial;text-align:center;}
    	.warning{background:#FFCC99;color:#FF6600;font-size:10pt;font-family:Arial;text-align:center;}
    	.error{background:#FFAAAA;color:#FF0000;font-size:10pt;font-family:Arial;text-align:center;}

    	.tituloA1{color:#FFFFFF;background:#660099;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#660066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;text-align:center;}

    </style>
</head>
<body>
<?php
include_once("conex.php");
/**
* 
* @wvar $pac
* 				[nom]: Nom
* 				[act]: El paciente esta ctivo en UNIX, es decir fue encontrado en la tabla inpac.
* 				[alt]:Boolean. El usuario quiere hacerle el alta definitiva al paciente.<br>
* 				[permisoAlta]:Boolean. Todos los registros activos del apciente estan en cargados a la cuanta, se puede efectuar el alta.<br>
* 				[]:<br>
* @wvar $ok	Determina si se deben o no buscar registros procesados del paciente. Se da si el paciente esta activo en UNIX o se encontro su información en MATRIX.
* @wvar $alta  false:debe preguntar al usuario si desea dar un alta.<br>
* 				cco:el usuario eligio hacer un alta del paciente a un centro de costos.<br>
* 				def: el usuario eligio hacer el alta definitiva de la institucicón.<br>
*/
/**
* Aqui se van a hacer las altas parciales, es decir a los centros de costos y las altas totales, de salida de la institución.
* Solo las altas totales hacen que los registros del día queden en estado procesado. Así mismo cuando se quita un alta total todos los registros del día deben
* pasar a estado transición, por eso se debe correr la función actualizacionDetalleRegistros con estado sin alta .
* 
* PARA LA PRÓXIMA VERSIÓN ESTO NO DEBE SER ASÍ.
*/

function pintarNombres()
{
	echo "<tr>";
	echo "<td class='titulo1'>Fecha</td>";
	echo "<td class='titulo1'>Historia</td>";
	echo "<td class='titulo1'>Ingreso</td>";
	echo "<td class='titulo1'>Doc. UNIX</td>";
	echo "<td class='titulo1'>Doc. Matrix</td>";
	echo "<td class='titulo1'>Articulo</td>";
	echo "<td class='titulo1'>Cantidad Unix</td>";
	echo "<td class='titulo1'>Cantidad Matrix</td>";

	echo "</tr>";
}

session_start();
if (!isset($_SESSION['user']))
echo "error";
else
{

	

	


	include_once("movhos/otros.php");
	connectOdbc($conex_o, 'inventarios');

	echo "</br></br><table align='center' border='0'>";
	echo "<tr><td align=center class='tituloSup' colspan='2'><b>REPORTE DE INCONSISTENCIAS UNIX-INTEGRADOR</b></td></tr>";
	echo "<tr><td align=center class='tituloSup1' colspan='2'>retorte.php Versión 2007-07-12<br><br></td></tr>";
	echo "<tr>";

	if (!isset($fec1))
	{
		/**
        * Pide la historia y el ingreso
        */
		echo "<form action='' method='POST'>";
		echo "<td align='center'><br><table border=0 width=400>";
		echo "<tr>";
		echo "<td class='texto2'><input type='checkbox' name='chkHis'></td>";
		echo "<td class='texto2' ><b>Historia: </b><input type='text' size='10' name='his'> &nbsp; ";
		echo "Ingreso: </b><input type='text' size='4' name='ing'></td></tr>";
		echo "<td class='texto2'><input type='checkbox' name='chkCco'></td>";
		echo "<td class='texto2' ><b>Cco: </b><input type='text' size='4' name='cco'></td></tr>";
		echo "<td class='texto2'><input type='checkbox' name='chkArt'></td>";
		echo "<td class='texto2' ><b>Artículo: </b><input type='text' size='10' name='art'></td></tr>";
		echo "<td class='texto2'></td>";
		echo "</td></tr>";
		echo "<td class='texto2'></td>";
		echo "<td class='texto2'><b>Fecha: </b><input type='text' size='10' name='fec1' value='" . date("Y-m-d") . "'></td></tr>";
		echo"<tr><td   class='texto2' colspan='2' align='center'><input type='submit' value='ACEPTAR'></td></tr></form>";
		echo "</form>";
	}
	else  if ($conex_o != 0)
	{
		$fen = "";
		$fde = "";
		$fit = "";
		$fdeit = "";
		$fiv = "";
		$fdeiv = "";
		$fec2=$fec1;
		
			$exp=explode ('-', $fec1);
			if(isset($exp[1]))
			{
				$tiempo=mktime(0,0,0,$exp[1],$exp[2],$exp[0])-(8*24*60*60);
				$fec1=date('Y-m-d', $tiempo);
			}
		
		$fec3 = str_replace('-', '/', $fec1);
		$fec4 = str_replace('-', '/', $fec2);
		if (isset($chkHis))
		{
			$fit = " Drohis = '" . $his . "' and";
		}

		if (isset($chkCco))
		{
			$fit = $fit . " Drocco = '" . $cco . "' and";
		}

		$art=strtoupper($art);
		if (isset($chkArt))
		{
			$fit = $fit . "        AND Droart = '" . $art . "' ";
		}

		if ($fec3==$fec4)
		{
			echo"<tr><td   class='texto2' colspan='2' align='center'>Fecha: ".$fec3." </td></tr>";
		}
		else
		{
			echo"<tr><td   class='texto2' colspan='2' align='center'>Fecha: ".$fec3." a ".$fec4."</td></tr></form>";
		}

		//buscamos los que estan en Unix que no estan en el integrador

		$time1=time();
		$q=" Select dronum num,drodocfue,drodocdoc,drofec,count(*) lin "
		."from itdro,itdrodoc "
		."where " . $fit. " "
		." dronum = drodocnum "
		."and droest = 'P' "
		."and drofec between '".$fec3."' and '".$fec4."' "
		."group by 1,2,3,4 "
		."into temp tempdro";

		$err_o= odbc_do($conex_o,$q);
		$time2=time();
		$q=" select num,drodocfue fue, drodocdoc doc,drofec fec,lin, count(*) itedet,sum(cardetvfa) vfa "
		." from tempdro,facardet "
		." where drodocfue=cardetfue "
		." and drodocdoc=cardetdoc "
		." group by 1,2,3,4,5 "
		." into temp tempdro1 ";

		$err_o= odbc_do($conex_o,$q);

		$q=" select * from tempdro1 "
		."where lin<>itedet "
		."into temp dife";

		$err_o= odbc_do($conex_o,$q);

		$time3=time();
		//echo '-'.($time2-$time1);
		//echo '-'.($time3-$time2);
		if (odbc_num_rows($err_o)>0) //Para cada registro encontrado en UNIX;
		{
			/*$q="unload to 'itdro.txt' select * from dife";
			echo $q;
			$err_o= odbc_do($conex_o,$q);

			$q="delete from ameitdro ";
			$err_o= odbc_do($conex_o,$q);

			$q="load from 'itdro.txt' insert into ameitdro ";
			$err_o= odbc_do($conex_o,$q);

			$q="select num,fue,doc,fec,lin,itedet,vfa,logope "
			." from ameitdro,outer ivlog "
			." where fue=logva1 "
			." and doc=logva2 ";
			$err_1= odbc_do($conex_o,$q);*/

			$q="select dronum,fue,doc,droart art,SUM(drocan) can "
			."from dife,itdro "
			."where num=dronum "
			."group by 1,2,3,4 "
			."order by 1,2,3,4 "
			."into temp tmpdro ";

			$err_o= odbc_do($conex_o,$q);

			$q="select num num1,fue fue1,doc doc1,drodetart art1,SUM(drodetcan) can1 "
			."from dife,ivdrodet "
			."where fue=drodetfue "
			."and doc=drodetdoc "
			."group by 1,2,3,4 "
			."order by 1,2,3,4 "
			."into temp tmpivdro ";

			$err_o= odbc_do($conex_o,$q);

			$q="select num1,fue1,doc1,art1,can1,can,ivdro.drohis his,ivdro.dronum ing,ivdro.drofec fec, drocco "
			."from tmpivdro,ivdro, tmpdro "
			."where num1=tmpdro.dronum "
			."and fue1=fue "
			."and doc1=doc "
			."and art1=art "
			."and can1<>can "
			."and fue1=drofue "
			."and doc1=drodoc "
			."order by 10,2,1,3,4 ";

			$err_o= odbc_do($conex_o,$q);


			echo "<tr><td align='center'><br><table border=0 align='center'>";
			$fueant = '';
			$ccoAnt = '';
			$numAnt = '';
			$i=0;
			while (odbc_fetch_row($err_o)) //Para cada registro encontrado en UNIX;
			{
				if ($i % 2 == 0)
				{
					$class = "texto";
				}
				else
				{
					$class = "texto1";
				}
				$i++;
				$colspan='10';

				if ($ccoAnt != odbc_result($err_o,10))
				{
					echo "<tr><td colspan='" . $colspan . "'>&nbsp;</td></tr>";
					echo "<tr><td bgcolor='#f5f5dc' colspan='" . $colspan . "'><font color='#006699'><B>Centro de costos:</B> " . odbc_result($err_o,10) . "</font></td></tr>";
					echo "<tr><td bgcolor='#FFDBA8' colspan='" . $colspan . "'><font color='#006699'><B>Fuente:</B> " . odbc_result($err_o,2) . "</font></td></tr>";
					$ccoAnt = odbc_result($err_o,10);
					$fueAnt = odbc_result($err_o,2);
					pintarNombres();
				}
				else if ($fueAnt != odbc_result($err_o,2))
				{
					echo "<tr><td bgcolor='#FFDBA8' colspan='" . $colspan . "'><font color='#006699'><B>Fuente:</B> " . odbc_result($err_o,2) . "</font></td></tr>";
					$fueAnt = odbc_result($err_o,2);
					pintarNombres();
				}

				echo "<tr>";
				echo "<td class='" . $class . "'>" . odbc_result($err_o,9) . "</td>";
				echo "<td class='" . $class . "'>" . odbc_result($err_o,7) . "</td>";
				echo "<td class='" . $class . "'>" . odbc_result($err_o,8) . "</td>";
				echo "<td class='" . $class . "'>" . odbc_result($err_o,3) . "</td>";
				echo "<td class='" . $class . "'>" . odbc_result($err_o,1) . "</td>";
				echo "<td class='" . $class . "'>" . odbc_result($err_o,4). "</td>";
				echo "<td class='" . $class . "'>" . odbc_result($err_o,5). "</td>";
				echo "<td class='" . $class . "'>" . odbc_result($err_o,6) . "</td>";

				echo "</tr>";

			}
			if($i==0)
			{
				echo "<td class='errorTitulo'>SIN NINGUNA INCONSITENCIA</td></tr>";
			}
		}
		else
		{
			echo "<td class='errorTitulo'>SIN NINGUNA INCONSITENCIA</td></tr>";
		}
	}
	else
	{
		echo "<td class='errorTitulo'>EN ESTO MOMENTO NO HAY CONEXION CON UNIX</td></tr>";
	}
	echo "</table>";
}

?>
</body>
</html>
