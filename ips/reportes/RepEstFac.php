<html>
<head>
  <title>REPORTE DE ESTADO DE FACTURAS EN CARTERA</title>
<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
//-->
function Seleccionar()
{
	document.forma.submit();
}
</SCRIPT>
</head>
<?php
include_once("conex.php");
 /*************************************************************************************
   *     REPORTE DE CARTERRA DE FACTURAS NO ENVIADAS O DEVUELTAS                     *
   *                      DE CLINICA DEL SUR                                         *
 *************************************************************************************/
//=================================================================================================================================
//PROGRAMA: RepEstFac.php
//AUTOR: Gabriel Agudelo.
  $wautor="Gabriel Agudelo.";
//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\pos\procesos\RepCar.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
		//-------------------I------------------------I---------------------------------------------------------------------
		//	  FECHA           I     AUTOR              I   MODIFICACION
		//-------------------I------------------------I------------------------------------------------------------------------
		//  2006-10-24       I Gabriel Agudelo        I creación del script.
		//-------------------I------------------------I-----------------------------------------------------------------------
		//     I   I
		//-------------------I------------------------I-----------------------------------------------------------------------

//FECHA ULTIMA ACTUALIZACION 	: 2006-09-25 2:00 pm
  $wactualiz="(Versión octubre 24 de 2006)";

/*DESCRIPCION:Este reporte presenta la lista de facturas por centro(s) de costo(s) y por empresa(s)

TABLAS QUE UTILIZA:
 $wbasedato."_000018: encabezado de factura, select
 $wbasedato."_000024: maestro de empresas, select

 INCLUDES:
  conex.php = include para conexión mysql

 VARIABLES:
 $wbasedato= variable que permite el codigo multiempresa, se incializa desde invocación de programa
 $wini= lleva el estado del documento, si se esta abriendo por primera vez o no, se incializa desde invocación de programa con 'S'
 $senal= Indica el mensaje de alerta que se debe presentar segun los errores
 $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
 $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
 $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
 $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
 $wfecha=date("Y-m-d");
  $wfecini)= fecha inicial del reporte
 $wfecfin = fecha final del reporte
 $wfue = fuente
 $wemp = empresa
 $wtip = variable que nos dice si es por codigo o nit
 $resultado =
 $bandera2= controla que sea la primera vez que entra en el ciclo para la empresa
 $j=1 sirve como variable de control para intercambiar colores
=================================================================================================================================*/

include_once("root/comun.php");
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 {
 	if(!isset($wemp_pmla)){
 		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
 	}

$key = substr($user,2,strlen($user));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;

echo "<form action='RepEstFac.php' method=post name='forma'>";
  $hora = (string)date("H:i:s");
  $wnomprog="-RepEstFac.php-";  //nombre del reporte
  $wcf1="003366";  // color del fondo   -- Azul mas claro
  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wcf3="0099CC";  //COLOR DEL FONDO 3  -- GRIS MAS OSCURO
  $wcf4="99CCFF";  //COLOR DEL FONDO 4  -- AZUL
  $wcf5="00CCFF";
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
  $wfecha=date("Y-m-d");

  echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

if (!isset($wfecini) or !isset($wfecfin) or !isset($wemp) or !isset($west) or !isset($wtip) or !isset($resultado))
{

	  echo "<center><table border=2>";
	 // echo "<tr><td align=center rowspan=2><img src='/reportes1/logo_".$wbasedato.".png' WIDTH=500 HEIGHT=100></td></tr>";
	  echo "<tr><td align=center rowspan=2 COLSPAN=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=500 HEIGHT=100></td></tr>";
	  echo "<tr><td align=center bgcolor=".$wcf2."><font size=4 text color=#FFFFFF><b>REPORTE DE ESTADO DE FACTURAS EN CARTERA</b></font></td></tr>";

	//INGRESO DE VARIABLES PARA EL REPORTE//
	if (!isset ($bandera))
	{
		 $wfecini=$wfecha;
		 $wfecfin=$wfecha;
	}

	echo "<tr>";

	echo "<td bgcolor=".$wcf." align=center  COLSPAN=2><b><font text color=".$wclfg.">FECHA INICIAL DE FACTURACION (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecini." SIZE=10><HR><b><font text color=".$wclfg.">FECHA FINAL DE FACTURACION  (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecfin." SIZE=10></td>";

//	echo "</tr>";
//	echo "<tr>";
	echo "<td align=center bgcolor=".$wcf." COLSPAN='2'><font text color=".$wclfg."><b>ESTADO : </b></font>";
	echo "<select name='west'>";
		echo "<option>%-Todos los estados</option>";
		echo "<option>GE-Generado</option>";
		echo "<option>DV-Devolucion</option>";
		echo "<option>RD-Radicado</option>";
		echo "<option>EV-Enviado</option>";
		echo "<option>GL-Glosado</option>";
		echo "</select></td>";
		echo "</tr>";
		echo "<tr>";
		//seleccionar fuente
		echo "<td align=center bgcolor=".$wcf." colspan=2><b><font text color=".$wclfg."> Fuente : <br></font></b><select name='wfue'>";
			$q1= "   SELECT carfue, cardes "
		            ."     FROM ".$wbasedato."_000040 "
	        		."    WHERE carest = 'on' "
	        		."          AND carfpa = 'on' "
		    	    ."          AND carncr != 'on' "
		    	    ."          AND carndb != 'on' "
		    	    ."          AND carrec != 'on' "
		    	    ."          AND carenv != 'on' "
		    	    ."          AND carrad != 'on' "
		    	    ."          AND cardev != 'on' "
		    	    ."          AND carglo != 'on' "
		    	    ."          AND cartra != 'on' "
		    	    ."     order by carfue ";
				    $res2 = mysql_query($q1,$conex);
				    $num2 = mysql_num_rows($res2);
				    echo "<option>%-Todas las fuentes</option>";
			   		for ($i=1;$i<=$num2;$i++)
			         	{
	  						$row2 = mysql_fetch_array($res2);
	  						echo "<option>".$row2[0]." - ".$row2[1]."</option>";
	       				}

	      	echo "</select></td>";


	//SELECCIONAR tipo de reporte

	echo "<td align=center bgcolor=".$wcf." ><font text color=".$wclfg."><b>PARAMETROS DEL REPORTE: </b></font>";
	echo "<select name='wtip'>";

	if (isset ($wtip))
		{
			if ($wtip=='CODIGO')
				{
					echo "<option>CODIGO</option>";
					echo "<option>NIT</option>";
				}
			if ($wtip=='NIT')
				{
					echo "<option>NIT</option>";
					echo "<option>CODIGO</option>";
				}
		}
	else
		{
			echo "<option>CODIGO</option>";
			echo "<option>NIT</option>";
		}
	echo "</select>";
	echo "EMPRESA</td></tr>";
	//echo "</tr>";
	//echo "<tr>";

	//SELECCIONAR EMPRESA
	if (isset($wemp))
	    {
			echo "<td align=center bgcolor=".$wcf."  colspan=6 ><b><font text color=".$wclfg."> Responsable: <br></font></b><select name='wemp'>";
			if ($wemp!='% - Todas las empresas')
				{
					$q= "   SELECT count(*) "
		            ."     FROM ".$wbasedato."_000024 "
	        		."    WHERE empcod = (mid('".$wemp."',1,instr('".$wemp."','-')-1)) "
		    	    ."      AND empcod = empres "
		    	    ."      AND empcod != 01 ";
				      $res1 = mysql_query($q,$conex);
				      $num1 = mysql_num_rows($res1);
				      $row1 = mysql_fetch_array($res1);
	      		}else
	      			{
		      			$row1[0] =1;
		     		}

	      	if ($row1[0] > 0 )
	      		{
		      		echo "<option selected>".$wemp."</option>";
		      		if ($wemp!='% - Todas las empresas')
						{
							echo "<option>% - Todas las empresas</option>";
						}

	      		$q= "   SELECT count(*) "
		         ."     FROM ".$wbasedato."_000024 "
	             ."    WHERE empcod != (mid('".$wemp."',1,instr('".$wemp."','-')-1)) "
	             ."      AND empcod = empres "
	             ."      AND empcod != 01 ";
	          $res = mysql_query($q,$conex);
	          $num = mysql_num_rows($res);
	          $row = mysql_fetch_array($res);
	          if ($row[0] > 0)
	             {
					$q= "   SELECT empcod, empnit, empnom "
	         		."     FROM ".$wbasedato."_000024 "
	                 ."    WHERE empcod != (mid('".$wemp."',1,instr('".$wemp."','-')-1)) "
	                 ."      AND empcod != 01 "
	                 ."      AND empcod = empres order by 2";
	       			$res1 = mysql_query($q,$conex);
	          		$num1 = mysql_num_rows($res1);
	          		for ($i=1;$i<=$num1;$i++)
			         	{
	  						$row1 = mysql_fetch_array($res1);
	  						echo "<option>".$row1[0]." - ".$row1[1]." - ".$row1[2]."</option>";
	       				}
		         }
	      	}
	     echo "</select></td>";
	   }
	   else
	   		{
		 	    echo "<td align=center bgcolor=".$wcf." colspan=6  ><b><font text color=".$wclfg."> Responsable: <br></font></b><select name='wemp'>";
	   			$q =  " SELECT empcod, empnit, empnom "
	             ."   FROM ".$wbasedato."_000024 "
	             ."  WHERE empcod = empres "
	             ."      AND empcod != 01 "
	             ."  ORDER BY empcod ";
		        $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
		        $num2 = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
	        	echo "<option>% - Todas las empresas</option>";
			    for ($i=1;$i<=$num2;$i++)
			       {
			        $row = mysql_fetch_array($res);
			        echo "<option>".$row[0]." - ".$row[1]." - ".$row[2]."</option>";
			       }
			    echo "</select></td>";
	   		}
	  	echo "</tr>";
// seleccionar tipo de fuente
	//		echo "<tr>";

	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
	echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
    echo "<tr><td align=center bgcolor=".$wcf." COLSPAN='4'><font text color=".$wclfg." ><b>";
	echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked> DESPLEGAR REPORTE DETALLADO&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<input type='radio' name='vol' value='NO'  onclick='Seleccionar()' > DESPLEGAR REPORTE RESUMIDO&nbsp;&nbsp;";                //submit
    echo "</font></b></td></tr></table></br>";
	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
}

//MUESTRA DE DATOS DEL REPORTE
else
  {
	echo "<table border=0 align=center 'width='70%>";
	echo "<tr>";

	echo "<td align=center><H2>$wentidad</H2></td></tr>";

		if ($vol=='SI')
		  	echo "<tr><td><B>REPORTE DEL ESTADO DE FACTURAS EN CARTERA DETALLADO</B></td></tr>";
		else
		    echo "<tr><td><B>REPORTE DEL ESTADO DE FACTURAS EN CARTERA RESUMIDO</B></td></tr>";
	echo "</table></br>";
	echo "<table border=0 align=center >";
    echo "<tr><td><B>Facturacion:</B>$wentidad</td>";
    echo "<td><B>Fecha:</B> ".date('Y-m-d')."</td>";
    echo "<td><B>Programa:</B> ".$wnomprog."</td></tr>";
	echo "<tr><td><B>Fecha inicial:</B> ".$wfecini."</td>";
	echo "<td><B>Fecha final:</B> ".$wfecfin."</td>";
	echo "<td><B>Hora :</B> ".$hora."</td></tr>";
	echo "<tr><td><B>Estado de la factura :</B> ".$west."</td>";
	echo "<td><B>Empresa:</B> ".$wemp."</td>";
	echo "<td><B>Clasificado por:</B> ".$wtip."</td></tr>";
	echo "<tr><td><B>Fuente de factura: </B> ".$wfue."</td></tr>";
	echo "</table></br>";
    echo "<A href='RepEstFac.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wtip=".$wtip."&amp;west=".$west."&amp;wemp=".$wemp."&amp;wfue=".$wfue."&amp;bandera='1'><center>VOLVER</center></A><br>";
    echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
  	echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
  	echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
  	echo "<input type='HIDDEN' NAME= 'west' value='".$west."'>";
  	echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
  	echo "<input type='HIDDEN' NAME= 'wtip' value='".$wtip."'>";
	echo "<input type='HIDDEN' NAME= 'wfue' value='".$wfue."'>";
  echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

/***********************************Consulto lo pedido ********************/

// si la empresa es diferente a todas las empresas, la meto en el vector solo
// si es todas las empresas meto todas en un vector para luego preguntarlas en un for
	if ($west!='%-Todos los estados')
		{
			$print1=explode('-', $west);
			$westado=trim ($print1[0]);
		}
	else
		{
			$westado='%';
		}
	if ($wfue !='%-Todas las fuentes')
		{
			$print2=explode('-', $wfue);
			$wfuente=trim ($print2[0]);
		}
	else
		{
			$wfuente='%';

		}

if ($wemp !='% - Todas las empresas')
	{

		$print=explode('-', $wemp);
		$wempcod[0]=trim ($print[0]);
		$wempnit[0]=trim ($print[1]);
		$empnom[0]=trim ($print[2]);
	//	$empresa[0]=$empCod[0]." - ".$empNit[0]." - ".$empNom[0];
		$num2=1;

	}
else
	{
		$wempcod='%';
		$wempnit='%';
		$num2=2;
	}

// si el centro de costos es diferente a todas los centros de costos, la meto en el vector solo
// si son todos los costos los empresas meto todas en un vector para luego preguntarlas en un for

		$q2 = " SELECT empcod, empnom, empnit,fenffa, fenfac, fensal, fenfec, fenesf "
		."     FROM  ".$wbasedato."_000018,".$wbasedato."_000024 "
		."     WHERE  fenfec between '".$wfecini."'"
		."     AND '".$wfecfin."'"
		."     AND fenesf like '".$westado."' "
		."     AND fenesf != '' "
		."     AND fenffa like  '".$wfuente."' "
		."     AND fencod like '".$wempcod[0]."' "
		."     AND fenest = 'on' "
		."     AND fencod=empcod "
		."      AND empcod != 01 "
		."     order by empcod,empnit,fenffa,fenfac,fenesf ";

		$err = mysql_query($q2,$conex);
		$num = mysql_num_rows($err);

	$cuenta=0;
	$wtotal = 0;
	$wtotsal= 0;
	$ccostos=' ';
	$wemptot = 0;
	$wempsal = 0;
	$bandera1=0;
	$bandera2=0;
	$wtotfac=0;
	$wsaldo=0;
	$coloresumido='#DDDDDD';
	$j=1;
	$k=1;
	echo "<table border=0 align =center>";
	$i=1;

	while ($i <= $num)
		{
			$row = mysql_fetch_array($err);

			if ($bandera2==0)
			 	{
		  			$wempcod=$row[0];
		  			$wempnom=$row[1];
		  			$wempnit=$row[2];
		 		}
		 	if ($wempcod!=$row[0])
		 		{
			 		if ($vol=='SI')
				  		{
				 			echo "<th align=left bgcolor=$wcf2 colspan='4'><font size=2 color='FFFFFF'>TOTAL EMPRESA</font></th>";
				 			echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wtotfac,0,'.',',')."</font></th>";
				 		}
				 	else
			 			{
				 			echo "<tr>";
				 			if (is_int ($j/2))
				 				{
   									$coloresumido='#FFFFFF';
   									$j=$j+1;
									}
								else
									{
   									$coloresumido='66CCFF';
   									$j=$j+1;
									}
								if ($wtip=='CODIGO')
	  							{
									echo "<th align=left bgcolor=$coloresumido colspan='4'><font size=2 ><b>Empresa: ".$wempcod." - ".$wempnom."</b></font></th>";
								}
							if ($wtip=='NIT')
								{
									echo "<th align=left bgcolor=$coloresumido colspan='4'><font size=2 ><b>Empresa: ".$wempnit." - ".$wempnom."</b></font></th>";
								}

			 				echo "<th align=right bgcolor=$coloresumido><font size=2 >".number_format($wtotfac,0,'.',',')."</font></th>";
			 				echo "</tr>";
		 				}


		    			$wtotal = $wtotal+$wtotfac;
						$wtotfac=0;

		 		}
			if (($bandera2==0) or ($wempcod!=$row[0]))
		 		{
				 	$wempcod=$row[0];
		  			$wempnom=$row[1];
		  			$wempnit=$row[2];
		  			$bandera2=1;
		  			$pinto=0;
		  			$waux=$row[0];
		  		if ($vol=='SI')
		  			{
		  				if ($wtip=='CODIGO')
		  					{
								echo "<tr><td colspan=9 bgcolor=$wcf2><font color='FFFFFF'><b>Empresa: ".$wempcod." - ".$wempnom."</b></font></td></tr>";
							}
						if ($wtip=='NIT')
							{
								echo "<tr><td colspan=9 bgcolor=$wcf2><font color='FFFFFF'><b>Empresa: ".$wempnit." - ".$wempnom."</b></font></td></tr>";
							}
					}

	  			}


			if ($vol=='SI')
				{
					if (is_int ($k/2))
						{
								$color='#DDDDDD';
								$k=$k+1;
							}
						else
							{
								$color='#FFFFFF';
								$k=$k+1;
							}
						if ($pinto==0)
							{
				  			echo "<th align=CENTER bgcolor=#ffcc66><font size=2>FUENTE FACTURA</font></th>";
		        			echo "<th align=CENTER bgcolor=#ffcc66><font size=2>NRO FACTURA</font></th>";
		        			echo "<th align=CENTER bgcolor=#ffcc66><font size=2>FECHA FACTURA</font></th>";
		        			echo "<th align=CENTER bgcolor=#ffcc66><font size=2>ESTADO CARTERA</font></th>";
		        			echo "<th align=CENTER bgcolor=#ffcc66><font size=2>SALDO FACTURA</font></th>";

							$pinto=1;
	   					}

				   		echo '<tr>';
						echo "<th align=right bgcolor=".$color."><font size=2>".$row[3]."</font></th>";
						echo "<th align=right bgcolor=".$color."><font size=2>".$row[4]."</font></th>";
						echo "<th align=right bgcolor=".$color."><font size=2>".$row[6]."</font></th>";
						echo "<th align=right bgcolor=".$color."><font size=2>".$row[7]."</font></th>";
						echo "<th align=right bgcolor=".$color."><font size=2>".number_format($row[5],0,'.',',')."</font></th>";

						echo '</tr>';
				}
			$wtotfac = $wtotfac+$row[5];
			$i= $i + 1;
		}
	if ($wtotfac==0)
		{
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningún documento en el rango de fechas seleccionado</td><tr>";
		}
	else
		{
		  $wtotal = $wtotal+$wtotfac;
		  if ($vol=='SI')
				{
						echo "<th align=left bgcolor=$wcf2 colspan='4'><font size=2 color='FFFFFF'>TOTAL EMPRESA</font></th>";
					 	echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wtotfac,0,'.',',')."</font></th>";

				}
          else
				{
		 			if (is_int ($j/2))
		 				{
								$coloresumido='#FFFFFF';
								$j=$j+1;
							}
						else
							{
								$coloresumido='66CCFF';
								$j=$j+1;
							}
						if ($wtip=='CODIGO')
							{
								echo "<th align=left bgcolor=$coloresumido colspan='4'><font size=2 ><b>Empresa: ".$wempcod." - ".$wempnom."</b></font></th>";
							}
						if ($wtip=='NIT')
							{
								echo "<th align=left bgcolor=$coloresumido colspan='4'><font size=2 ><b>Empresa: ".$wempnit." - ".$wempnom."</b></font></th>";
							}
	 			echo "<th align=right bgcolor=$coloresumido><font size=2 >".number_format($wtotfac,0,'.',',')."</font></th>";
 				}


				echo "<tr><th align=left bgcolor=$wcf3 colspan='4'><font size=2 color='FFFFFF'>TOTAL GENERAL </font></th>";
		    	echo "<th align=right bgcolor=$wcf3><font color='FFFFFF' size=2>".number_format($wtotal,0,'.',',')."</font></th>";
		}
    echo "</table>";
	echo "</br><center><A href='RepEstFac.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wfue=".$wfue."&amp;wtip=".$wtip."&amp;west=".$west."&amp;wemp=".$wemp."&amp;bandera='1'>VOLVER</A></center>";
	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
}
}
liberarConexionBD($conex);
?>
</body>
</html>
