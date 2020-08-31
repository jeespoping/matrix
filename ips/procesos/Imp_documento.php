<?php
include_once("conex.php");


/************************************************************************************
*     PROGRAMA PARA IMPRESION DE NOTAS Y RECIBOS                               		*
*************************************************************************************/
//=================================================================================================================================
//PROGRAMA: cartera.php
//AUTOR: Juan Carlos Hernández M.
$wautor="Juan C. Hernandez M.";

//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\pos\procesos\catera.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
//-------------------I------------------------I---------------------------------------------------------------------
//	  FECHA          I     AUTOR              I   MODIFICACION
//-------------------I------------------------I------------------------------------------------------------------------
//  2005-10-18       I Juan Carlos Hernández  I creación del script.
//-------------------I------------------------I-----------------------------------------------------------------------
//  2006-05-30       I  Carolina Castano      I modificación y nuevas funcionalidades.
//-------------------I------------------------I-----------------------------------------------------------------------
//  2007-03-29       I Carolina Castano       I se imprime banco destino cuando existe wobs y se muestra valor a cancelar y no total cuando es recibo
//  2011-03-17       I Mario Cadavid          I se define una condicional para el llamado a las funciones de modo que generar_impresion_notas.php pueda hacer uso de este script en su ciclo de impresion de documentos. También se modificó la condición que no permitia mostrar los documentos anulados para que los muestre con un texto de "Anulado"
//-------------------I------------------------I-----------------------------------------------------------------------

//FECHA ULTIMA ACTUALIZACION 	: 2011-03-17 15:24 hrs
$wactualiz="(Diciembre 24 de 2013)";

//-------------------------------------------------------------------------------------------------------------------------------------------
//	-->	2013-12-24, Jerson trujillo.
//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//-------------------------------------------------------------------------------------------------------------------------------------------

//DESCRIPCION:Este programa sirve para hacer los recibos de caja a varias facturas de una misma empresa o las notas debito y credito, pudiendose
//hacer la cancelacion con conceptos de cartera por cada una de las facturas detalladas.

//TABLAS QUE MODIFICA:
// $wbasedato."_000045: Tabla temporal de almacenamiento de operaciones (notas y recibos), select, delete
// $wbasedato."_000030: Busqueda de cajeros autorizados, select
// $wbasedato."_000040: Maestro de Fuentes, select
// $wbasedato."_000018: encabezado de factura, select
// $wbasedato."_000044: maestro de conceptos de cartera, select


//=================================================================================================================================

/**********************************************FUNCIONES******************/
/**
 * Devuelve verdadero si la fuente de la factura es una nota credio
 *
 * @param $fuente
 * @return bool
 */
if(!isset($imp) || (isset($imp) && $imp=='1'))
{
	function consultarNC($fuente)
	{
		global $wbasedato;
		global $conex;

		$val = false;

		if( $fuente != "" ){

			$q="SELECT
					*
				FROM
					{$wbasedato}_000040
				WHERE
					carncr='on'
					AND carfue='$fuente'
					AND carest='on'
					AND carcfa<>'on'";

			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

			if( $row = mysql_fetch_row($res) )
				$val = true;
		}

		return $val;
	}

	function consultarMulti ($codigo, $fuente)
	{
		global $wbasedato;
		global $conex;

		$q="select conmul from ".$wbasedato."_000044 where concod='".$codigo."' and confue='".$fuente."'  ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row = mysql_fetch_row($res);
		return (-1*$row[0]);
	}
}
////////////////////////////////////////////////////////////////////////////////////////////////////
//FUNCION PARA MOSTRAR LAS OPCIONES DE RECIBOS DE DINERO - FORMAS DE PAGO
////////////////////////////////////////////////////////////////////////////////////////////////////





echo "<input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>";

//---------------------------------------------------------------------------------------------
// --> 	Consultar si esta en funcionamiento la nueva facturacion
//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
//---------------------------------------------------------------------------------------------
	$q = " SELECT Detval
			 FROM root_000051
			WHERE Detemp = '".$wemp_pmla."'
			  AND Detapl = 'NuevaFacturacionActiva' ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$nuevaFacturacion = '';
	if(mysql_num_rows($res) > 0)
	{
		$rs = mysql_fetch_array($res);
		$nuevaFacturacion = $rs['Detval'];
	}
	else
		die("La institucion con el codigo :".$codigoInstitucion." no se encuentra, ni la aplicacion: ".$nombreAplicacion.".  Por favor verifique el valor de wemp_pmla");
//---------------------------------------------------------------------------------------------
// --> 	MAESTRO DE CONCEPTOS:
//		- Antigua facturacion 	--> 000004
//		- Nueva facturacion 	--> 000200
//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
//		de conceptos cambiara por la tabla 000200.
//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
//----------------------------------------------------------------------------------------------
$tablaConceptos = $wbasedato.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
//----------------------------------------------------------------------------------------------

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//ACA TRAIGO LOS DATOS NECESARIOS PARA IMPRIMIR LA FACTURA DESDE LA TABLA DE CONFIGURACION
$q = " SELECT cfgnit, cfgnom, cfgtre, cfgtel, cfgdir, cfgpin, cfgmai, cfgdom "
."   FROM ".$wbasedato."_000049 "
."  WHERE cfgcco = '".$wcco."'";

$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
$row = mysql_fetch_array($res);

$wnit_pos  =$row[0];
$wnomemppos=$row[1];
$wtipregiva=$row[2];
$wtel_pos  =$row[3];
$wdir_pos  =$row[4];
$wpagintern=$row[5];
$wemail_pos=$row[6];
$wteldompos=$row[7];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//$wempresa=explode("-",$wnomnit);

$wfuedoc1=explode("-",$wfuedoc);
if ($wfuedoc1 != "")
$wfuedoc=$wfuedoc1[0];

$wno_existe="off";

/////////////////////////////////////
if ($wnrodoc != "")
{
	$q = " SELECT cardes, carfpa, carndb, carlet "
	    ."   FROM ".$wbasedato."_000040 "
	    ."  WHERE carfue = '".$wfuedoc."'";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$wnomfue = $row[0];
		$wfpa=$row[1];
		$wcarndb=$row[2];
		$wtamlet=$row[3];

		$q = " SELECT renfec, rencod, rennom, renvca, rencaj, renusu, rencco, renobs, empnit, empraz "
		    ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000024 "
		    ."  WHERE renfue = '".$wfuedoc."'"
	    	."    AND rennum = ".$wnrodoc
    		."    AND rencod = empcod "
	    	."    AND rencco = '".$wcco."' "
		    ."    AND renest = 'on'";
        $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);

		if ($num > 0)
		{
			$row = mysql_fetch_array($res);

			$wfecdoc = $row[0];
			$wcodemp = $row[1];
			$wnomemp = $row[9];
			$wvaldoc = $row[3];
			$wcajdoc = $row[4];
			$wusudoc = $row[5];
			$wccodoc = $row[6];
			$wobs = $row[7];
			$wnitemp = $row[8];
		}
		else
		$wno_existe="on";
	}

	echo "<center><table border=0 width='750'>";

	// Se comenta para que se muestre tambien los documentos anulados
	/*if ($wno_existe=="off")
	{*/
		//IMPRESION CON EL LOGO AL PRINCIPIO
		if ($wtamlet > 3)
		   echo "<tr><td align=left  rowspan='4' width='30%'><img src='/matrix/images/medical/IPS/logo_".$wbasedato.".png' WIDTH='550' HEIGHT='200'></td>";
		  else
		     echo "<tr><td align=left  rowspan='4' width='30%'><img src='/matrix/images/medical/IPS/logo_".$wbasedato.".png' WIDTH='225' HEIGHT='100'></td>";
		echo "<td width='40%'>&nbsp</td>";
		echo "<td width='30%'><font size=".$wtamlet."><b>".$wnomfue."</b></font></td></tr>";
		echo "<tr><td width='40%'>&nbsp</td>";
		echo "<td width='30%'><font size=".$wtamlet."><b>Nro: ".$wnrodoc."</b></font></td></tr>";
		echo "<tr><td width='40%'>&nbsp</td>";	
		$q = " SELECT Rdevta  "
			."   FROM ".$wbasedato."_000021
			    WHERE Rdenum = '".$wnrodoc."' 
			      AND Rdecco  = '".$wcco."' ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row = mysql_fetch_array($res);
		echo "<td width='30%'><font size=".$wtamlet."><b>Nro Venta: ".$row['Rdevta']."</b></font></td></tr>";
		echo "<tr><td width='40%'>&nbsp</td>";
		echo "<td width='30%'><font size=".$wtamlet."><b>Fecha: ".$wfecdoc."</b></font></td></tr>";
		echo "<tr><td width='40%'>&nbsp</td>";
		if ($wfpa=='off')
		{
			echo "<td width='30%'><font size=".$wtamlet."><b>Valor: ".number_format($wvaldoc,0,'.',',')."</b></font></td></tr>";
		}
		else
		{
			$q = " SELECT sum(rdevco*conmul*-1) "
			."   FROM ".$wbasedato."_000021, ".$wbasedato."_000044 "
			."  WHERE rdefue = '".trim($wfuedoc)."'"
			."    AND rdenum = ".$wnrodoc
			."    AND rdecco = ".$wcco
			."    AND rdefac <> '' "
			."    AND conest ='on' "
			."    AND confue =rdefue "
			."    AND concod =mid(rdecon,1,instr(rdecon,'-')-1) ";
			//."    AND rdecco = '".$wcco."'";

			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);
			$row = mysql_fetch_array($res);
			echo "<td width='30%'><font size=".$wtamlet."><b>Valor: ".number_format($wvaldoc-$row[0],0,'.',',')."</b></font></td></tr>";
		}

		echo "<tr><td align=center colspan=3 ><font size=".$wtamlet."><b>".$wnomemppos."</b></font></td></tr>";
		echo "<tr><td align=center colspan=3 ><font size=".$wtamlet."><b>Nit. : ".$wnit_pos."</b></font></td></tr>";

		echo "<tr><td width='30%'>&nbsp</td><td width='40%'>&nbsp</td><td width='30%'>&nbsp</td></tr>";
		echo "<tr>";



		$q = " SELECT * "
		."   FROM ".$wbasedato."_000021 "
		."  WHERE rdefue = '".trim($wfuedoc)."'"
		."    AND rdenum = ".$wnrodoc
		."    AND rdecco = ".$wcco;
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);


		$q= "   SELECT cardes "
		."     FROM ".$wbasedato."_000040 "
		."    WHERE carfue = '".trim($wfuedoc)."' "
		."      AND carrec ='on' and carest='on' "
		."      AND carfue  in (select ccofrc from ".$wbasedato."_000003) ";

		$res1 = mysql_query($q,$conex);
		$abono = mysql_num_rows($res1);

		$query= "select fdecco, fdecon, fdevco, fdeter, grudes from ".$wbasedato."_000065, ".$tablaConceptos."    ";
		$query = $query. " where fdefue='".trim($wfuedoc)."' and fdedoc='".$wnrodoc."' and grucod=fdecon ";

		$err = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
		$can = mysql_num_rows($err);
		if(!isset($wbandera))
		{
		if ($num > 0)
		{

			$wtotvca=0;
			$wtotvco=0;

			$row = mysql_fetch_array($res);

			if ($wcodemp!='01')
			{

				if($wemp_pmla =='06')
				{

				}
				else
				{
					echo "<td align=left colspan=3><font size=".$wtamlet."><b>A favor de:  ".$wnitemp."-".$wnomemp."</b></font></td>";
				}
			}
			else
			{


				if ($row[6]!='')
				{
					$q = " SELECT fendpa, fennpa  "
					."   FROM ".$wbasedato."_000018 "
					."  WHERE fenffa = '".$row[12]."' and fenfac='".$row[6]."' ";


					$res5 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$row5 = mysql_fetch_array($res5);

					echo "<td align=left colspan=3><font size=".$wtamlet."><b>A favor de:  ".$row5[0]."-".$row5[1]."</b></font></td>";
				}
				else
				{
					echo "<td align=left colspan=3><font size=".$wtamlet."><b>A favor de:  PARTICULAR</b></font></td>";
				}
			}
			echo "</tr>";


			if ($wfpa=='on' and ($row[6]!='' or $abono>0))
			{

				if($wemp_pmla =='06')
				{
					$q4 = " SELECT 	Rdefac ,Rdevta  "
						."   FROM ".$wbasedato."_000021 "
						."  WHERE rdefue = '".trim($wfuedoc)."'"
						."    AND rdenum = ".$wnrodoc
						."    AND rdecco = ".$wcco;

					$res4 = mysql_query($q4,$conex) or die("ERROR CONSULTANDO VENTAS");
					$row4 = mysql_fetch_array($res4);

					$q5= "SELECT Vencco, Venfec, Vennit, Venvto, Venviv, Vencop, Vencmo, Vendes, Venrec, Vennum , Clinom ,Rdevca,Empnom,Empcod,Clidoc
								FROM ".$wbasedato."_000016 LEFT JOIN ".$wbasedato."_000041 ON Clidoc = Vennit LEFT JOIN  ".$wbasedato."_000021 ON Rdevta=Vennum AND Rdeest='on' , ".$wbasedato."_000017 ,  ".$wbasedato."_000024
							   WHERE  Vennum = '".$row4['Rdevta']."'
							     AND Venest = 'on'
								 AND Vencod = Empcod
								 AND Vennum = Vdenum
							GROUP BY Vennum ";
					$res5 = mysql_query($q5,$conex) or die("ERROR CONSULTANDO VENTAS");
					$row5 = mysql_fetch_array($res5);

						echo "<tr>";
						echo "<td align=left colspan=3><font size=".$wtamlet."><b>Nombre Paciente:</b> ".$row5['Clinom']."</font></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=left colspan=3><font size=".$wtamlet."><b>Cedula:</b> ".$row5['Clidoc']."</font></td>";
						echo "</tr>";

				}
				else
				{
					if ($num==1)
					{
						$h="select rdehis, pacdoc, pacno1, pacno2, pacap1, pacap2, rdeing "
						  ."  from ".$wbasedato."_000021,  ".$wbasedato."_000100 "
						  ." where rdefue='".trim($wfuedoc)."'"
						  ."   and rdenum='".$wnrodoc."'"
						  ."   and rdecco='".$wcco."'"
						  ."   and pachis=rdehis ";

						$errh = mysql_query($h,$conex) or die (mysql_errno()." - ".mysql_error());
						$rowh = mysql_fetch_array($errh);

						echo "<tr>";
						echo "<td align=left colspan=3><font size=".$wtamlet.">Paciente:  ".$rowh[1]."-".$rowh[2]." ".$rowh[3]." ".$rowh[4]." ".$rowh[5]."</font></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=left colspan=3><font size=".$wtamlet.">Numero de Historia:  ".$rowh[0]."</font></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=left colspan=3><font size=".$wtamlet.">Numero de Ingreso:  ".$rowh[6]."</font></td>";
						echo "</tr>";
					}else
					{
						echo "<tr>";
						echo "<td align=left colspan=3><font size=".$wtamlet.">Paciente:  varios</font></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=left colspan=3><font size=".$wtamlet.">Numero de Historia:  varias</font></td>";
						echo "</tr>";
					}
				}
			}

			echo "</table>";





			echo "<left><table border=0 width='750'>";
			echo "<tr><td align=center colspan=5><font size=".$wtamlet."><b>D E T A L L E &nbsp&nbsp  D E &nbsp&nbsp  F A C T U R A S</b></font></td></tr>";
			echo "<tr><td colspan=5 ><hr></td></tr>";

			$rdevca=$row[8];
			$rdecon=$row[10];



			if (($rdevca=='' or $rdevca==0) and $rdecon=='')
			{

				echo "<th align=leftr><font size=".$wtamlet.">Factura</font></th>";
				echo "<th align=left><font size=".$wtamlet.">Concepto de Facturacion</font></th>";
				echo "<th align=left><font size=".$wtamlet.">Centro de costos</font></th>";
				echo "<th align=left><font size=".$wtamlet.">Tercero</font></th>";
				echo "<th align=right><font size=".$wtamlet.">Vr Concepto</font></th>";
				if ($can > 0)
				{
					for ($i=1;$i<=$can;$i++)
					{
						echo "<tr>";
						echo "<td align=left><font size=".$wtamlet.">".strtoupper($row[6])."</font></td>";
						$row2 = mysql_fetch_array($err);
						echo "<td align=left><font size=".$wtamlet.">".$row2[1]."-".$row2[4]."</font></td>";                                    //Valor cancelado
						echo "<td align=center><font size=".$wtamlet.">".$row2[0]."</font></td>";                                      //Concepto de cartera
						echo "<td align=left><font size=".$wtamlet.">".$row2[3]."</font></td>";
						echo "<td align=right><font size=".$wtamlet.">".number_format(($row2[2]),0,'.',',')."</font></td>";                          //Valor recibido
						echo "</tr>";

						$wtotvca = 0;
						$wtotvco = $wtotvco + $row2[2];
					}
				}else
				{
					$row2 = mysql_fetch_array($err);
					echo "<td align=right></td>";                                    //Valor cancelado
					echo "<td align=center></td>";                                      //Concepto de cartera
					echo "<td align=center></td>";
					echo "<td align=right></td>";                          //Valor recibido
					echo "</tr>";

					$wtotvco = $wtotvco + $row[2];
				}

				echo "<tr>&nbsp</tr>";
				echo "<tr><b>";
				echo "<td><b><font size=".$wtamlet.">Totales: </font></b></td>";
				echo "<td align=right></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td align=right><font size=".$wtamlet."><b>".number_format($wtotvco,0,'.',',')."</b></font></td>";
				echo "</tr>";
			}else
			{
				echo "<th align=left width='15%' ><font size=".$wtamlet.">Factura</font></th>";
				echo "<th align=right width='15%'><font size=".$wtamlet.">Vr Cancelado</font></th>";
				echo "<th align=center width='35%'><font size=".$wtamlet.">Concepto</font></th>";
				echo "<th align=right width='20%'><font size=".$wtamlet.">Vr Concepto</font></th>";
				echo "<th align=right width='15%'><font size=".$wtamlet.">Vr Recibido</font></th>";

				for ($i=1;$i<=$num;$i++)
				{
					$signo1 = 1;
					$esnc = consultarNC( $row[12] );
					if( $esnc ){
						$signo1 = -1;
					}

					$exp=explode ('-',$row[10]);
					$exp2=explode('-',$wfuedoc);
					$signo=consultarMulti($exp[0], $exp2[0]);
					$facturanum =strtoupper($row[6]);
					echo "<tr>";
					echo "<td><font size=".$wtamlet.">".strtoupper($row[6])."</font></td>";                                        //factura
					echo "<td align=right><font size=".$wtamlet.">".number_format($row[8],0,'.',',')."</font></td>";               //Valor cancelado
					echo "<td align=center><font size=".$wtamlet.">".$row[10]."</fotn></td>";                                      //Concepto de cartera
					if ($signo<0 and $wcarndb=='off')
					{
						echo "<td align=right><font size=".$wtamlet.">".number_format(-$row[11],0,'.',',')."</font></td>";         //Valor concepto
						echo "<td align=right><font size=".$wtamlet.">".number_format(($row[8]*$signo1+($row[11]*$signo)),0,'.',',')."</font></td>";
					}
					else
					{
						echo "<td align=right ><font size=".$wtamlet.">".number_format($row[11],0,'.',',')."</font></td>";         //Valor concepto
						echo "<td align=right ><font size=".$wtamlet.">".number_format(($row[8]*$signo1+$row[11]),0,'.',',')."</fotn></td>";
					}
					echo "</tr>";

					if( $esnc ){

						$sig = -2;
						$wtotvca = $wtotvca+($row[8]*$sig);
					}

					$wtotvca = $wtotvca + $row[8];
					if ($wcarndb=='off')
					{
						$wtotvco = $wtotvco + ($row[11]*$signo);
					}else
					{
						$wtotvco = $wtotvco + $row[11];
					}

					$row = mysql_fetch_array($res);
				}

				echo "<tr>&nbsp</tr>";
				echo "<tr><b>";
				echo "<td><b><font size=".$wtamlet.">Totales: </font></b></td>";
				echo "<td align=right><b><font size=".$wtamlet.">".number_format($wtotvca,0,'.',',')."</font></b></td>";
				echo "<td><b>&nbsp</b></td>";
				echo "<td align=right><b><font size=".$wtamlet.">".number_format($wtotvco,0,'.',',')."</font></b></td>";
				echo "<td align=right><b><font size=".$wtamlet.">".number_format(($wtotvca+$wtotvco),0,'.',',')."</font></b></td>";
				echo "</tr>";
			}


		}else
		{
			echo "<tr><tr><tr>";
			echo "<tr><td colspan=5 align=center><font size=".$wtamlet."><b>EL DOCUMENTO NO PERTENECE AL CENTRO DE COSTO AL QUE USTED ESTA MATRICULADO</b></font></td></tr>";
			echo "<tr><td colspan=5 align=center><font size=".$wtamlet."><b>POR LO QUE NO SE PUEDE MOSTRAR EL DETALLE LAS FACTURAS</b></font></td></tr>";
			echo "<tr><tr><tr>";
		}

		}

		if(isset($wbandera))
		{
			echo "<tr></tr>";
			echo "<tr></tr>";
			echo "<tr></tr>";
			echo "<tr><td align=left colspan=5><font size=".$wtamlet."><b>Nombre Paciente:</b>".$wnombrerec."</font></td><td></td></tr>";
			echo "<tr><td align=left colspan=1><font size=".$wtamlet."><b>Cedula:</b>".$wcedularec."</font></td><td></td></tr>";

		}

		if ($wfpa=='on')
		{
			$q = "SELECT rfpfpa, fpades, rfpdan, rfpobs,  rfpvfp, rfppla, rfpaut, rfpbai "
			."  FROM ".$wbasedato."_000022, ".$wbasedato."_000023 "
			." WHERE rfpfue = '".trim($wfuedoc)."'"
			."   AND rfpnum = ".$wnrodoc
			."   AND rfpfpa = fpacod "
			."   AND rfpest = 'on' "
			."   AND rfpcco = '".$wcco."'";

			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
				echo "<tr><td colspan=5><b><hr></b></td></tr>";
				echo "<tr><td align=center colspan=5><font size=".$wtamlet."><b>F O R M A   D E   P A G O</b></font></td></tr>";
				echo "<tr><td colspan=5><b><hr></b></td></tr>";

				echo "<th align=left width='15%'><font size=".$wtamlet.">Formas de Pago</font></th>";
				echo "<th align=center width='15%'><font size=".$wtamlet.">Documento Anexo</font></th>";
				echo "<th align=center width='35%'><font size=".$wtamlet.">Entidad</font></th>";
				//	echo "<th align=left  >Ubicación</th>";
				if (isset ($obser)) //para saber si se imprime la observacion o no
				{
					echo "<th align=center width='20%'><font size=".$wtamlet.">Banco destino</font></th>";
				}
				else
				{
					echo "<th align=left width='20%'>&nbsp;</th>";
				}
				//echo "<th align=left  >Nº autorización</th>";
				echo "<th align=right width='15%'><font size=".$wtamlet.">Valor</font></th>";
				$vartottal = 0;
				for ($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_array($res);

					echo "<tr>";
					echo "<td><font size=".$wtamlet.">".$row[0]."-".$row[1]."</font></td>";

					if ($row[2] != "" and $row[2] != " ")
					echo "<td align=center><font size=".$wtamlet.">".$row[2]."</font></td>";
					else
					echo "<td>&nbsp</td>";


					if ($row[3] != "" and $row[3] != " ")
					{
						$q = "SELECT bannom, bancue "
						."  FROM ".$wbasedato."_000069 "
						." WHERE bancod = '".$row[3]."'"
						."   AND banest = 'on' ";

						$res2 = mysql_query($q,$conex);
						$row2 = mysql_fetch_array($res2);
						echo "<td align=center><font size=".$wtamlet.">".$row2[0]."</font></td>";
					}
					else
					echo "<td>&nbsp</td>";

					$plaza='';
					switch ($row[5])
					{
						case "L": //nota credito
						{ $plaza='Local';  //multiplo
						break;
						}

						case "O": //nota credito
						{ $plaza='Otras plazas';  //multiplo
						break;
						}
					}

					/* if ($plaza != "" and $plaza != " ")
					echo "<td  >".$plaza."</td>";
					else
					echo "<td >&nbsp</td>"; */

					/*if ($row[6] != "" and $row[6] != " ")
					echo "<td  >".$row[6]."</td>";
					else
					echo "<td  >&nbsp</td>"; */

					if (isset ($obser)) //para saber si se imprime la observacion o no
					{
						$q = "SELECT bannom, bancue "
						."  FROM ".$wbasedato."_000069 "
						." WHERE bancod = '".$row[7]."'"
						."   AND banest = 'on' ";

						$res2 = mysql_query($q,$conex);
						$row2 = mysql_fetch_array($res2);
						echo "<td align=center><font size=".$wtamlet.">".$row2[0]."-".$row2[1]."</font></td>";
					}
					else
					{
						echo "<th align=left width='20%'>&nbsp;</th>";
					}
					echo "<td align=right><font size=".$wtamlet.">".number_format($row[4],0,'.',',')."</font></td>";
					echo "</tr>";
					$vartottal =$vartottal + $row[4];
				}
				echo "<tr><td colspan=5>&nbsp</td></tr>";
				echo "<tr><td colspan=5>&nbsp</td></tr>";
				// echo "<tr><td colspan=5>holaaaaaaaaaaaa".$wabentregado."aaaaaaaaaaaa</td></tr>";

				if (!isset($wabentregado) AND !isset( $notas) )
				{


				}
				else
				{

					if ($notas=='si'  )
					{

						// este query saca las ventas a facturar  , si los parametros de busqueda estan llenos se agrega un filtro al query
						$query = "SELECT Venvto,Vennum
									FROM ".$wbasedato."_000016 LEFT JOIN ".$wbasedato."_000041 ON Clidoc = Vennit LEFT JOIN  ".$wbasedato."_000021 ON Rdevta=Vennum , ".$wbasedato."_000017 ,  ".$wbasedato."_000024
								   WHERE  Venest = 'on'
									   AND Vencod = Empcod
									   AND Vennfa = '".$facturanum."'
									   AND Vennum = Vdenum
									   AND Vennum not in (select Traven from uvglobal_000055)
								   GROUP BY Vennum ";

						$res3 = mysql_query($query,$conex);
						while($row4 = mysql_fetch_array($res3))
						{
							$valormovimieto = $row4 ['Venvto'];
							$numeroventa = $row4 ['Vennum'];
						}




						$err = mysql_query($query,$conex);

						$query_saldo = "SELECT  Rdevta,Rdevca ,id
									      FROM  ".$wbasedato."_000021
									     WHERE  Rdevta='".$numeroventa."' AND Rdeest='on'";

						$res2 = mysql_query($query_saldo,$conex);
						$saldo = 0;
						$ids ='';
						while($row3 = mysql_fetch_array($res2))
						{

							$saldo =$saldo + $row3['Rdevca'] ;
							$ids .= ",'".$row3['id']."'";

						}
						
						$ids = substr($ids,1);
						if($facturanum !='')
						{
							// se hace esto por que pueden quedar recibos y no relacionar la venta entonces se busca por factura.
							//
							if($wbasedato == "uvglobal")
							{
								//
								
								$query_saldo_facturas="	SELECT Rdevta,Rdevca
														  FROM ".$wbasedato."_000021
														 WHERE  Rdefac = '".$facturanum."' 
														 AND id NOT IN (".$ids.")";
								
								$res2 = mysql_query($query_saldo_facturas,$conex);
								while($row3 = mysql_fetch_array($res2))
								{

									$saldo =$saldo + $row3['Rdevca'] ;

								}
							}
							
							// if ($wquiero =='si')
							// {
									// echo $query_saldo_facturas."<br>".$query_saldo;
								
							// }
						}
						

						$wsaldototal = $valormovimieto - $saldo;

						echo "<tr><td colspan=4><font size=".$wtamlet.">Total Entregado:</font></td><td align=right ><font size=".$wtamlet.">".number_format($vartottal,0,'.',',')."</font></td></tr>";
						echo "<tr><td colspan=4><font size=".$wtamlet.">Total Abonado:</font></td><td align=right><font size=".$wtamlet.">".number_format($vartottal,0,'.',',')."</font></td></tr>";
						echo "<tr><td colspan=4 ><font size=".$wtamlet.">Cambio:</font></td><td align=right><font size=".$wtamlet.">0.00</font></td></tr>";
						if($wbasedato == "uvglobal")
						{
							echo "<tr><td colspan=4><font size=".$wtamlet.">Saldo:</font></td><td align=right ><font size=".$wtamlet.">".number_format($wsaldototal,0,'.',',')."</font></td></tr>";
						}

					}
					else
					{
						echo "<tr><td colspan=4><font size=".$wtamlet.">Total Entregado:</font></td><td align=right ><font size=".$wtamlet.">".number_format($wabentregado,0,'.',',').".00</font></td></tr>";
						echo "<tr><td colspan=4><font size=".$wtamlet.">Total Abonado:</font></td><td align=right><font size=".$wtamlet.">".$wababonado."</font></td></tr>";
						echo "<tr><td colspan=4 ><font size=".$wtamlet.">Cambio:</font></td><td align=right><font size=".$wtamlet.">".$wabcambio."</font></td></tr>";
						echo "<tr><td colspan=4><font size=".$wtamlet.">Saldo:</font></td><td align=right ><font size=".$wtamlet.">".$wabsaldo."</font></td></tr>";
					}
				}


				//echo "<tr><td colspan=5></td></tr>";
				//echo "<tr><td colspan=5>&nbsp</td></tr>";
			}
		}
		$wfecha=date("Y-m-d");
		$hora = (string)date("H:i:s");

		echo "</table>";

		if (isset ($obser)) //para saber si se imprime la observacion o no
		{
			echo "</br><table align=center width='750' >";
			echo "<tr>";
			echo "<td><font size=".$wtamlet."><b>OBSERVACION:</b> ".$wobs."</font></td>";
			echo "</tr>";
			echo "</table></br>";
		}

		if ($wfpa!='on')
		{
			echo "<table align=center width='750'>";
			echo "<tr><td align=left height='150' colspan=6><font size=".$wtamlet.">Firma: ____________________________________</font></td></tr>";
			echo "</table>";
		}

		echo "<table align=center width='750' >";
		echo "<tr><td colspan=5><b><hr></b></td></tr>";

		echo "<tr>";
		echo "<td><font size=".$wtamlet.">Fecha: ".$wfecha."</font></td>";
		echo "<td><font size=".$wtamlet.">Hora: ".$hora."</font></td>";
		echo "<td><font size=".$wtamlet.">Usuario: ".$wusudoc."</font></td>";
		echo "<td><font size=".$wtamlet.">Caja: ".$wcajdoc."</font></td>";
		echo "<td><font size=".$wtamlet.">Sucursal: ".$wccodoc."</font></td>";
		echo "<tr><td colspan=5><b><hr></b></td></tr>";
		// Si la factura es anulada acá lo pone
		if ($wno_existe=="on")
		{
			echo "<tr>";
			echo "<td colspan='5' align='center'><font size=".$wtamlet."><b> ** DOC. ANULADO** </b></font></td>";
		}
		echo "</table>";

	// Se comenta para que se muestren también los documentos anulados
	/*}
	else
	{
		echo "<br><br><br><br><br><br>";
		echo "<tr><td colspan=5 align=center><font size=".$wtamlet."><b>!!! ATENCION !!!  EL DOCUMENTO NO EXISTE</b></font></td></tr>";
		echo "<br><br><br><br><br><br>";

		//echo "<form name='recibos_y_notas' action='recibos_y_notas.php' method=post>";

		echo "<tr>";
		echo "<tr><td colspan=5 align=center><font size=".$wtamlet."><b>PRESIONE (ALT+F4) </b></font></td></tr>";
		echo "</tr>";
	}*/
}
echo "</table></center>";


?>
