<html>
	<head>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<title>MATRIX - [COMPROBANTE DE CAJA Y BANCOS]</title>

		<script type="text/javascript">
		function Seleccionar(){
			document.forma.submit();
		}
		/******************************************************************************************************************************
		 *Redirecciona a la pagina inicial
		 ******************************************************************************************************************************/
		function inicio(){
			document.location.href='comRecAbo.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&wbasedato='+document.forms.forma.wbasedato.value;
		}
		/*****************************************************************************************************************************
		 * Consulta de recibos detallado
		 ******************************************************************************************************************************/
		function consultar(){
			var wbasedato = document.forms.forma.wbasedato.value;
			var wemp_pmla = document.forms.forma.wemp_pmla.value;
			var fini = document.forms.forma.wfecini.value;
			var ffin = document.forms.forma.wfecfin.value;
			var fuente = document.forms.forma.wfuente.value;
			var fechaComprobante = document.forms.forma.wfeccor.value;
			var numeroComprobante = document.forms.forma.wdoccom.value;
			var fuenteCombrobante = document.forms.forma.wfuecom.value;
			var tipoReporte = document.forms.forma.wtiporep;

			var graba = (document.forms.forma.gra && document.forms.forma.gra.checked == true) ? true : false;
			var reemplaza = (document.forms.forma.ree && document.forms.forma.ree.checked == true) ? true : false;

			var accion = '';

			if(tipoReporte[0].checked){
				accion = 'a';
			}

			if(tipoReporte[1].checked){

				accion = 'b';
			}

			var bdUnix = $('#selectBdUnix').val();


			var url = 'comRecAbo.php?wemp_pmla='+wemp_pmla+'&wbasedato='+wbasedato+'&waccion='+accion+'&wfecini='+fini+'&wfecfin='+ffin+'&wfuente='+fuente+'&wfeccor='+fechaComprobante+'&wdoccom='+numeroComprobante+'&wfuecom='+fuenteCombrobante;

			if(graba){
				url += '&gra=on';
				if(bdUnix=='')
				{
					alert ("Debe seleccionar una base de datos destino");
					return;
				}

				if($("#wdoccom").val() =='')
				{
					alert ("Debe ingresar numero de comprobante");
					return;
				}

				if($("#wfuecom").val() =='')
				{
					alert ("Debe ingresar la fuente del comprobante");
					return;
				}

				var feci = $("#wfecini").val();
				var fecf = $("#wfecfin").val();
				var fec = $("#wfeccor").val();

				f1 = new Date(feci);
				f2 = new Date(fecf);
				f3 = new Date(fec);
				var validacion_fecha = 0;
				if(f3 >= f1  )
				{

					if( f2 >= f3 )
						{
							validacion_fecha = 1;
						}
				}

				if(validacion_fecha == 0)
				{
					alert("La fecha del comprobante debe corresponder a un dia entre la fecha inicial y final");
					return;
				}

			}

			if(reemplaza){
				url += '&ree=on';
				if(bdUnix=='')
				{
					alert ("Debe seleccionar una base de datos destino");
					return;
				}

				if($("#wdoccom").val() =='')
				{
					alert ("Debe ingresar numero de comprobante");
					return;
				}

				if($("#wfuecom").val() =='')
				{
					alert ("Debe ingresar la fuente del comprobante");
					return;
				}

				var feci = $("#wfecini").val();
				var fecf = $("#wfecfin").val();
				var fec = $("#wfeccor").val();

				f1 = new Date(feci);
				f2 = new Date(fecf);
				f3 = new Date(fec);
				var validacion_fecha = 0;
				if(f3 >= f1  )
				{

					if( f2 >= f3 )
						{
							validacion_fecha = 1;
						}
				}

				if(validacion_fecha == 0)
				{
					alert("La fecha del comprobante debe corresponder a un dia entre la fecha inicial y final");
					return;
				}
			}
			url += '&wbdunix='+bdUnix;



			//El numero del comprobante y la fuente son obligatorias
			if(tipoReporte[1].checked && (graba || reemplaza)){
				if(numeroComprobante == '' || fuenteCombrobante == ''){
					alert("Debe especificar el numero y la fuente del comprobante para grabar o reemplazar.");
					return;
				}
			}

			document.location.href = url;
		}
		</script>
	</head>
<body>
<?php
include_once("conex.php");

/**
 * NOMBRE:  COMPROBANTE DE CAJA Y BANCOS
 *
 * PROGRAMA: comRecAbo.php
 * TIPO DE SCRIPT: PRINCIPAL
 * //DESCRIPCION:Este comprobante muestra la partida y contrapartida de recibos o abonos en caja o bancos
 *
 * HISTORIAL DE ACTAULIZACIONES:
 * 2006-11-30 carolina castano, creacion del script
 * 2007-01-18 carolina castano, creacion del script
 * 2007-05-11 Esta parte de modifica de manera que cuando no son notas por otros conceptos
 *            el codigo del recibo se saque de la factura
 * 2009-11-06 msanchez: Generación de comprobantes resumidos, actualizacion con el comun.php
 * 2010-04-16 msanchez: Disgregacion de cuentas de comprobantes resumidos
 *
 * Tablas que utiliza:
 * $wbasedato."_000024: Maestro de Fuentes, select
 * $wbasedato."_000018: select de facturas entre dos fechas
 * $wbasedato."_000020: select en encabezado de cartera
 * $wbasedato."_000021: select en detalle de cartera
 *
 * @author ccastano
 * @package defaultPackage
 */
//=================================================================================================================================
include_once("root/comun.php");

class CuentaResumida{
	var $numero = "";
	var $nombre = "";
	var $naturaleza = "";
	var $total = "";
}

session_start();

if(!isset($_SESSION['user'])){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} else {
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$wactualiz = "2015-05-20";
	$key = substr($user,2,strlen($user));

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	$wbasedato = $institucion->baseDeDatos;

	$basedatosUnix = $wbdunix;
	if(isset($gra))
	{
		$conexunix = odbc_connect($basedatosUnix,'informix','sco') or die("No se realizo conexion con Unix");
	}



	// $basedatosUnix = consultarAliasPorAplicacion($conex,$wemp_pmla,"unix_contabilidad");

	// $conexunix = odbc_connect($basedatosUnix,'informix','sco') or die("No se realizo conexion con Unix");


	//Encabezado
	encabezado("Comprobante de caja y bancos",$wactualiz,"logo_".$wbasedato);

	echo "<form action='comRecAbo.php' method=post name='forma'>";
	echo $basedatosUnix;
	$wfecha = date("Y-m-d");

	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	//Estrategia de FC con parámetro waccion
	if(!isset($waccion)){
		$waccion = "";
	}

	switch ($waccion){
		case 'a':
			echo "<table align=center width='60%'>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td><B>Fecha: ".$wfecha."</B></td></tr>";

			echo "<tr><td><B>COMPROBANTE DETALLADO DE CAJA Y BANCOS</B></td></tr>";
			echo "</tr><td align=right><A href='comRecAbo.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wfeccor=".$wfeccor."&amp;wfuente=".$wfuente."&amp;wbasedato=$wbasedato'>VOLVER</A></td></tr>";
			echo "</table>";

			$exp=explode('-', $wfuente);
			$wfuecom=trim($exp[0]);

			echo "<table align=center width='1000'>";

			echo "<tr><td><tr><td>Fecha inicial: ".$wfecini."</td></tr>";
			echo "<tr><td>Fecha final: ".$wfecfin."</td></tr>";
			echo "<tr><td>Fecha del comprobante: ".$wfeccor."</td></tr>";
			echo "<tr><td>Fuente: ".$wfuente."</td></tr>";
			echo "<tr><td align=right >(*)PAGO EN EFECTIVO</td></tr>";
			echo "</table></br>";

			echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."' id='wfecini'>";
			echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'  id='wfecfin'>";
			echo "<input type='HIDDEN' NAME= 'wfeccor' value='".$wfeccor."' id='wfeccor'>";
			echo "<input type='HIDDEN' NAME= 'wfuente' value='".$wfuente."' >";

			echo "<table align=center>";

			//Encabezado tabla
			echo "<tr class='encabezadoTabla' align='center'>";
			echo "<td>FUENTE</td>";
			echo "<td>DOCUMENTO</td>";
			echo "<td>FECHA</td>";
			echo "<td>CUENTA</td>";
			echo "<td>NOMBRE</td>";
			echo "<td>C.COSTO</td>";
			echo "<td>N. AUTO</td>";
			echo "<td>NIT/CED</td>";
			echo "<td>NOMBRE</td>";
			echo "<td>DEBITOS</td>";
			echo "<td>CREDITOS</td>";
			echo "</tr>";

			$q = " SELECT
					a.rennum, a.rencod, a.rennom, a.renvca, a.renfec, a.renfue, a.rencco
				FROM
					{$wbasedato}_000020 a
				WHERE
					a.renfec between '{$wfecini}'
					AND '{$wfecfin}'
					AND a.renest = 'on'
					AND a.renfue = '{$wfuecom}'
				ORDER BY
					a.rennum, a.renfec ";

//				echo $q;

				$err = mysql_query($q,$conex) or die("Error en la consulta: ".$q." - ".mysql_error());
				$num = mysql_num_rows($err);

				//si hay resultados grabo el encabezado del comprobante
				$wgraba="off";
				$sw="off";
				$inicial=strpos($user,"-");
				$wusuario=substr($user, $inicial+1, strlen($user));

				//se busca en la tabla 20 y 21 registros, empresa por empresa en un for y entre las fechas escogidas
				$cuenta=0;
				$wtotdebito = 0;
				$wtotcredito= 0;
				$clase1='fila1';
				$clase2='fila2';

				$k=1;

				for ($i=0;$i<$num;$i++)
				{
					$wtdebito = 0;
					$wtcredito= 0;

					$row = mysql_fetch_array($err);

					//si se ha dado en grabar, grabo el ncabezado del comprobante por recibo
					if (isset($gra))
					{
						$wfec=explode("-",$wfeccor);

						$q = "SELECT
								count(*)
							FROM
								sicie
							WHERE
								cieanc = '".$wfec[0]."'
								AND ciemes = '".$wfec[1]."'
								AND ciefec <> ''
								AND cieapl= 'CONTAB'";

						$resunix = odbc_do($conexunix,$q);
						$wnumunix=odbc_result($resunix,1);

						if ($wnumunix == 0)
						{
							//inyeccion de ceros para el numero del comprobante
							$wdoccom=0;
							$x=7-strlen($row[0]);

							for ($j=1;$j<$x;$j++)
							{
								$wdoccom="0".$wdoccom;
							}
							$wdoccom=$wdoccom.$row[0];

							$q = "SELECT
									count(*)
								FROM
									comovenc
								WHERE
									movencfue = '".$wfuecom."'
									AND movencdoc = '".$wdoccom."'
									AND movencanu = '0'
									AND movencano='".$wfec[0]."'
									AND movencmes='".$wfec[1]."'";

							$resunix = odbc_do($conexunix,$q);
							$wexiste=odbc_result($resunix,1);

							if ($wexiste > 0)
							{
								if (isset($ree)) {
									//BORRO EL COMPROBANTE EXISTENTE
									$q = "DELETE FROM comov WHERE movfue = '".$wfuecom."' AND movdoc = '".$wdoccom."' and movano = '".$wfec[0]."' and movmes = '".$wfec[1]."'";
									$resunix = odbc_do($conexunix,$q);

									$q = "DELETE FROM comovenc WHERE movencfue = '".$wfuecom."' AND movencdoc = '".$wdoccom."' and movencano = '".$wfec[0]."' AND movencmes = '".$wfec[1]."'";
									$resunix = odbc_do($conexunix,$q);

									//GRABO EL COMPROBANTE
									$q = "INSERT INTO comovenc(   movencano  ,   movencmes  ,   movencfue   ,   movencdoc   ,   movencusu   , movencanu) "
									."                      VALUES('".$wfec[0]."','".$wfec[1]."','".$wfuecom."','".$wdoccom."','".$wusuario."', '0' )   ";
									$resunix = odbc_do($conexunix,$q);

									$sw="on";
									$wgraba="on";
									$exito=0;
								} else {
									$wgraba="off";
									$sw="off";
									$exito=1;

								}
							} else {
								//SI NO EXISTE LO GRABO
								$wfec=explode("-",$wfeccor);

								$q = "INSERT INTO comovenc(   movencano  ,   movencmes  ,   movencfue   ,   movencdoc   ,   movencusu   , movencanu) "
								."              VALUES('".$wfec[0]."','".$wfec[1]."','".$wfuecom."','".$wdoccom."','".$wusuario."', '0' )   ";
								$resunix = odbc_do($conexunix,$q);

								$sw="on";
								$exito=0;
							}
						} else {
							$wgraba="off";
							$sw = "off";
							$exito=2;
						}
					} else {
						$exito=0;
					}

					$q = "SELECT
						carroc, carabo
					FROM
					{$wbasedato}_000040
					WHERE
						carfue = '".$wfuecom."'";

					$roc = mysql_query($q,$conex) or die("Error en la consulta: ".$q." - ".mysql_error());
					$rocr = mysql_fetch_array($roc);

					if ($rocr[0]=='on' or $rocr[1]=='on')
					{
						$q = "SELECT
						a.empnit,  a.empnom, b.relfuecta, b.relfuenat, b.relfuenit, b.relfueter
					FROM
					{$wbasedato}_000024 a, {$wbasedato}_000078 b
					WHERE
						a.empcod = '".$row[1]."'
						AND b.relfuecod = '".$row[5]."'
						AND b.relfuetem = (mid(a.emptem,1,instr(a.emptem,'-')-1))
						AND b.relfueest ='on'";

					$err2 = mysql_query($q,$conex) or die("Error en la consulta: ".$q." - ".mysql_error());
					$row2 = mysql_fetch_array($err2);

					if ( $rocr[1]=='on' and $row[1]=='01')
					{
						$q = "SELECT
							fendpa, fennpa
						FROM
						{$wbasedato}_000021 , ".$wbasedato."_000018
						WHERE
							rdenum = '".$row[0]."'
							AND rdefue = '".$row[5]."'
							AND rdecco = '".$row[6]."'
							AND fenffa = rdeffa
							AND fenfac=rdefac ";

						$res5 = mysql_query($q,$conex) or die("Error en la consulta: ".$q." - ".mysql_error());
						$row5 = mysql_fetch_array($res5);

						$row2[0]=$row5[0];
						$row2[1]=$row5[1];
					}
					} else {
						$q = "SELECT
						fendpa, fennpa, fencod
					FROM
					{$wbasedato}_000021 , {$wbasedato}_000018
					WHERE
						rdenum = '".$row[0]."'
						AND rdefue = '".$row[5]."'
						AND rdecco = '".$row[6]."'
						AND fenffa = rdeffa
						AND fenfac=rdefac ";

					$res5 = mysql_query($q,$conex) or die("Error en la consulta: ".$q." - ".mysql_error());
					$row5 = mysql_fetch_array($res5);

					$q = "SELECT
							a.empnit,  a.empnom, b.relfuecta, b.relfuenat, b.relfuenit, b.relfueter
						FROM
						{$wbasedato}_000024 a, {$wbasedato}_000078 b
						WHERE
							a.empcod = '".$row5[2]."'
							AND b.relfuecod = '".$row[5]."'
							AND b.relfuetem = (mid(a.emptem,1,instr(a.emptem,'-')-1))
							AND b.relfueest ='on' ";

						$err2 = mysql_query($q,$conex) or die("Error en la consulta: ".$q." - ".mysql_error());
						$row2 = mysql_fetch_array($err2);

						if ($row5[2]=='01')
						{
							$row2[0]=$row5[0];
							$row2[1]=$row5[1];
						}
					}

					echo '<tr>';
					echo "<td align=CENTER class=".$clase1." width='5%'>".$row[5]."</td>";
					echo "<td align=CENTER class=".$clase1." width='5%'>".$row[0]."</td>";
					echo "<td align=CENTER class=".$clase1." width='10%'>".$row[4]."</td>";
					echo "<td align=CENTER class=".$clase1." width='5%'>".$row2[2]."</td>";
					echo "<td align=CENTER class=".$clase1." width='15%'>&nbsp;</td>";
					echo "<td align=CENTER class=".$clase1." width='5%'>&nbsp;</td>";
					echo "<td align=CENTER class=".$clase1." width='10%'>&nbsp;</td>";

					if ($row2[4]=='on')
					{
						$wdre=$row2[0];
						echo "<td align=CENTER class=".$clase1." width='10%'>".$row2[0]."</td>";
						echo "<td align=CENTER class=".$clase1." width='15%'>".$row2[1]."</td>";
					}
					else
					{
						if ($rocr[0]!='on')
						{
							$wdre=$row2[5];
							echo "<td align=CENTER class=".$clase1." width='10%'>".$row2[5]."</td>";
							echo "<td align=CENTER class=".$clase1." width='15%'>&nbsp;</td>";
						}
						else
						{
							$wdre='';
							echo "<td align=CENTER class=".$clase1." width='10%'>&nbsp;</td>";
							echo "<td align=CENTER class=".$clase1." width='15%'>&nbsp;</td>";
						}
					}

					if ($row2[3]=='D')
					{
						echo "<td align=CENTER class=".$clase2." width='10%'>".number_format($row[3],0,'.',',')."</td>";
						echo "<td align=CENTER class=".$clase2." width='10%'>&nbsp;</td>";
						$wtdebito=$wtdebito+$row[3];
					}
					else
					{
						echo "<td align=CENTER class=".$clase2." width='10%'>&nbsp;</td>";
						echo "<td align=CENTER class=".$clase2." width='10%'>".number_format($row[3],0,'.',',')."</td>";
						$wtcredito=$wtcredito+$row[3];
					}

					echo '</tr>';

					//grabo el registro en la contabilidad
					if ($sw == "on")
					{
						if ($row2[3]=="D"){
							$wnat="1";
						} else {
							$wnat="2";
						}

						$wfec=explode("-",$wfeccor);

						$q = "INSERT INTO comov(movfue,        movdoc,   movane,     movano,        movmes,     movite,    movfec,        movcue,       movcco,       movnit,       movdes,                             movind,    movval,   movcon,  movbas,  movfac, movuni, movcam, movbaj, movanu) "
						."           VALUES('".$wfuecom."','".$wdoccom."', ''    ,'".$wfec[0]."','".$wfec[1]."',".$k." ,'".$wfeccor."','".$row2[2]."',   '',        '".$wdre."','COMPROBANTE FUENTE ".$wfuente."',   '".$wnat."',".$row[3].", ''    , 0     , 0     , 0     , 0     , 'N'   , '0' )   ";

						$res = odbc_do($conexunix,$q);
						$k++;
					}

					$q = "SELECT
						a.rdecon, SUM(a.rdevco), b.condes, b.connat, b.concue, a.rdeccc, b.connit, b.conter
					FROM
					{$wbasedato}_000021 a, {$wbasedato}_000044 b
					WHERE
						a.rdenum = '".$row[0]."'
						AND a.rdefue = '".$row[5]."'
						AND a.rdecco = '".$row[6]."'
						AND a.rdeest = 'on'
						AND b.confue = '".$row[5]."'
						AND b.concod =trim(mid(a.rdecon,1,instr(a.rdecon,'-')-1))
						AND b.conest = 'on'
					GROUP BY
						a.rdecon, a.rdeccc";

//					echo $q;

					$err3 = mysql_query($q,$conex) or die("Error en la consulta: ".$q." - ".mysql_error());
					$num3 = mysql_num_rows($err3);

					for ($j=0;$j<$num3;$j++){
						$row3 = mysql_fetch_array($err3);
						echo '<tr>';
						echo "<td align=CENTER class=".$clase1." width='5%'>".$row[5]."</td>";
						echo "<td align=CENTER class=".$clase1." width='5%'>".$row[0]."</td>";
						echo "<td align=CENTER class=".$clase1." width='10%'>".$row[4]."</td>";
						echo "<td align=CENTER class=".$clase1." width='5%'>".$row3[4]."</td>";
						echo "<td align=CENTER class=".$clase1." width='15%'>".$row3[2]."</td>";

						if ($row3[5]!='' and $row3[5]!=' ') {
							echo "<td align=CENTER class=".$clase1." width='5%'>".$row3[5]."</td>";
							$wdco=$row3[5];
						} else {
							echo "<td align=CENTER class=".$clase1." width='10%'>&nbsp;</td>";
							$wdco='';
						}

						echo "<td align=CENTER class=".$clase1." width='10%'>&nbsp;</td>";

						if ($row3[6]!='off'){
							$wdre=$row2[0];
							echo "<td align=CENTER class=".$clase1." width='10%'>".$row2[0]."</td>";
							echo "<td align=CENTER class=".$clase1." width='15%'>".$row2[1]."</td>";
						} else {
							$wdre=$row3[7];
							echo "<td align=CENTER class=".$clase1." width='10%'>".$row3[7]."</td>";
							echo "<td align=CENTER class=".$clase1." width='15%'>&nbsp;</td>";
						}

						if ($row3[3]=='D'){
							echo "<td align=CENTER class=".$clase2." width='10%'>".number_format($row3[1],0,'.',',')."</td>";
							echo "<td align=CENTER class=".$clase2." width='10%'>&nbsp;</td>";
							$wtdebito=$wtdebito+$row3[1];
						} else if ($row3[3]=='C') {
							echo "<td align=CENTER class=".$clase2." width='10%'>&nbsp;</td>";
							echo "<td align=CENTER class=".$clase2." width='10%'>".number_format($row3[1],0,'.',',')."</td>";
							$wtcredito=$wtcredito+$row3[1];
						}

						echo '</tr>';
						if(isset($gra))
						{
							$q = "SELECT
									cueicc, cuescc
								FROM
									cocue
								WHERE
									cuecod = '".$row3[4]."'";

							$resunix1 = odbc_do($conexunix,$q);
							$pide1=odbc_result($resunix1,1);
							$pide2=odbc_result($resunix1,2);
						}
						if ($pide1!='S' and $pide2!='S'){
							$wdco='';
						}

						//grabo el registro en la contabilidad
						if ($sw == "on"){
							if ($row3[3]=="D"){
								$wnat="1";
							} else {
								$wnat="2";
							}

							$wfec=explode("-",$wfeccor);

							$q = "INSERT INTO comov(movfue,        movdoc,   movane,     movano,        movmes,     movite,    movfec,        movcue,       movcco,       movnit,       movdes,                             movind,    movval,   movcon,  movbas,  movfac, movuni, movcam, movbaj, movanu) "
							."           VALUES('".$wfuecom."','".$wdoccom."', ''    ,'".$wfec[0]."','".$wfec[1]."',".$k." ,'".$wfeccor."','".$row3[4]."',   '".$wdco."', '".$wdre."','COMPROBANTE FUENTE ".$wfuente."',   '".$wnat."',".$row3[1].", ''    , 0     , 0     , 0     , 0     , 'N'   , '0' )   ";

							$res = odbc_do($conexunix,$q);
							$k++;
						}
					}

					$q= "   SELECT
						carroc
					FROM
						".$wbasedato."_000040
					WHERE
						carfue = '".$wfuecom."'";


					$roc = mysql_query($q,$conex) or die("Error en la consulta: ".$q." - ".mysql_error());
					$rocr = mysql_fetch_array($roc);

					if ($rocr[0]!='on')
					{
						$q = "SELECT
						a.rfpbai, a.rfpvfp, b.bannom, b.bancue, rfpfpa, rfpdan
					FROM
					{$wbasedato}_000022 a, {$wbasedato}_000069 b
					WHERE
						a.rfpnum = '".$row[0]."'
						AND a.rfpfue = '".$row[5]."'
						AND a.rfpcco = '".$row[6]."'
						AND a.rfpest = 'on'
						AND b.bancod = a.rfpbai
						AND b.banest = 'on' ";

					$err3 = mysql_query($q,$conex) or die("Error en la consulta: ".$q." - ".mysql_error());
					$num3 = mysql_num_rows($err3);

					for ($j=0;$j<$num3;$j++)
					{
						$row3 = mysql_fetch_array($err3);
						echo '<tr>';
						if ($row3[4]=='99'){
							echo "<td align=CENTER class=".$clase1." width='5%'>*".$row[5]."</td>";
						} else {
							echo "<td align=CENTER class=".$clase1." width='5%'>".$row[5]."</td>";
						}

						if ($row3[5]==' '){
							$row3[5]='';
						}

						echo "<td align=CENTER class=".$clase1." width='5%'>".$row[0]."</td>";
						echo "<td align=CENTER class=".$clase1." width='10%'>".$row[4]."</td>";
						echo "<td align=CENTER class=".$clase1." width='5%'>".$row3[3]."</td>";
						echo "<td align=CENTER class=".$clase1." width='15%'>".$row3[2]."</td>";
						echo "<td align=CENTER class=".$clase1." width='5%'>&nbsp;</td>";

						if ($row3[5]!=''){
							echo "<td align=CENTER class=".$clase1." width='10%'>".$row3[5]."</td>";
						} else {
							echo "<td align=CENTER class=".$clase1." width='10%'>&nbsp;</td>";
						}
						echo "<td align=CENTER class=".$clase1." width='10%'>&nbsp;</td>";
						echo "<td align=CENTER class=".$clase1." width='15%'>&nbsp;</td>";

						echo "<td align=CENTER class=".$clase2." width='10%'>".number_format($row3[1],0,'.',',')."</td>";
						echo "<td align=CENTER class=".$clase2." width='10%'>&nbsp;</td>";
						$wtdebito=$wtdebito+$row3[1];

						echo '</tr>';

						if ($sw == "on")
						{
							$wnat="1";
							$wfec=explode("-",$wfeccor);

							$q = "INSERT INTO comov(movfue,        movdoc,   movane,     movano,        movmes,     movite,    movfec,        movcue,       movcco,       movnit,       movdes,                             movind,    movval,   movcon,  movbas,  movfac, movuni, movcam, movbaj, movanu) "
							."           VALUES('".$wfuecom."','".$wdoccom."', '".$row3[5]."', '".$wfec[0]."','".$wfec[1]."',".$k." ,'".$wfeccor."','".$row3[3]."',   '',             '','COMPROBANTE FUENTE ".$wfuente."',       '".$wnat."',".$row3[1].", ''    , 0     , 0     , 0     , 0     , 'N'   , '0' )   ";
							$res = odbc_do($conexunix,$q);

							$k++;
						}

					}
					}

					echo "<tr>";
					echo "<th align=CENTER class='fila2' colspan='9' >TOTAL DOCUMENTO</th>";
					if ($wtdebito==$wtcredito){
						echo "<th align=CENTER class='fila1' >".number_format($wtdebito,0,'.',',')."</th>";
						echo "<th align=CENTER class='fila1' >".number_format($wtcredito,0,'.',',')."</th>";
					} else {
						echo "<th align=CENTER class='fila1' >".number_format($wtdebito,0,'.',',')."</th>";
						echo "<th align=CENTER class='fila1' >".number_format($wtcredito,0,'.',',')."</th>";
					}
					echo "</tr>";

					$wtotcredito=$wtotcredito+$wtcredito;
					$wtotdebito=$wtotdebito+$wtdebito;
					$cuenta++;
				}

				if (isset ($exito)){
					switch ($exito){
						case 1:
							mensajeEmergente("YA EXISTEN ALGUNOS COMPROBANTES Y NO SE SEÑALO REEMPLAZAR. NO SE ACTUALIZO ESTA INFORMACION EN CONTABILIDAD");
							break;
						case 2:
							mensajeEmergente("EL PERIODO SE ENCUENTRA CERRADO PARA LA FECHA SELECCIONADA. NO SE ACTUALIZO LA INFORMACION EN CONTABILIDAD");
							break;
					}
				}

				if ($cuenta==0){
					echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
					echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningun documento en el rango de fechas seleccionado</td><tr>";

				}

				else if ($cuenta>0){
					echo "<tr class='encabezadoTabla'>";
					echo "<th align=CENTER colspan='9' >TOTAL</th>";
					if ($wtotdebito==$wtotcredito)
					{
						echo "<th align=CENTER>".number_format($wtotdebito,0,'.',',')."</th>";
						echo "<th align=CENTER>".number_format($wtotcredito,0,'.',',')."</th>";
					}
					else
					{
						echo "<th align=CENTER>".number_format($wtotdebito,0,'.',',')."</th>";
						echo "<th align=CENTER>".number_format($wtotcredito,0,'.',',')."</th>";
					}
					echo "</tr>";

				}
				echo "</table>";
				echo "<center><A href='comRecAbo.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wfeccor=".$wfeccor."&amp;wfuente=".$wfuente."&amp;wbasedato=$wbasedato'>VOLVER</A></center>";

				if (isset ($wgraba) and $wgraba=='on'){
					mensajeEmergente("EL COMPROBANTE HA SIDO REEMPLAZADO CON EXITO");
				} elseif (isset ($sw) and $sw=='on') {
					mensajeEmergente("EL COMPROBANTE HA SIDO INGRESADO CON EXITO");
				}
			//----------------------------------------------------
			break;
		case 'b':
			echo "<table align=center width='60%'>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td><B>Fecha: ".$wfecha."</B></td></tr>";

			echo "<tr><td><B>COMPROBANTE RESUMIDO DE CAJA Y BANCOS</B></td></tr>";
			echo "</tr><td align=right><A href='comRecAbo.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wfeccor=".$wfeccor."&amp;wfuente=".$wfuente."&amp;wbasedato=$wbasedato'>VOLVER</A></td></tr>";
			echo "</table>";

			$exp=explode('-', $wfuente);
			$wfuecom=trim($exp[0]);

			echo "<table align=center width='1000'>";

			echo "<tr><td><tr><td>Fecha inicial: ".$wfecini."</td></tr>";
			echo "<tr><td>Fecha final: ".$wfecfin."</td></tr>";
			echo "<tr><td>Fecha del comprobante: ".$wfeccor."</td></tr>";
			echo "<tr><td>Fuente: ".$wfuente."</td></tr>";
			echo "</table></br>";

			echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
			echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
			echo "<input type='HIDDEN' NAME= 'wfeccor' value='".$wfeccor."'>";
			echo "<input type='HIDDEN' NAME= 'wfuente' value='".$wfuente."'>";

			//Indicador si graba y reemplaza
			$graba = false;
			if (isset($gra)){
				$graba = true;
			}
			$reemplaza = false;

			if (isset($ree)){
				$reemplaza = true;
			}

			$colCreditos = array();
			$colDebitos = array();

			$registros = false;
			$puedeGrabar = false;
			$existe = false;

			$totalCreditos = 0;
			$totalDebitos = 0;

			//Verificación de que el cierre contable no se haya realizado
			if ($graba) {
				$wfec=explode("-",$wfeccor);

				$q = "SELECT
						count(*)
					FROM
						sicie
					WHERE
						cieanc = '".$wfec[0]."'
						AND ciemes = '".$wfec[1]."'
						AND ciefec <> ''
						AND cieapl= 'CONTAB'";

				$resunix = odbc_do($conexunix,$q);
				$wnumunix=odbc_result($resunix,1);

				if ($wnumunix == 0){
					$puedeGrabar = true;

					//inyeccion de ceros para el numero del comprobante
					$x=7-strlen($wdoccom);

					for ($j=1;$j<=$x;$j++)
					{
						$wdoccom="0".$wdoccom;
					}

					$q = "SELECT
								count(*)
							FROM
								comovenc
							WHERE
								movencfue = '".$wfuecom."'
								AND movencdoc = '".$wdoccom."'
								AND movencanu = '0'
								AND movencano='".$wfec[0]."'
								AND movencmes='".$wfec[1]."'";

					$resunix = odbc_do($conexunix,$q);
					$wexiste=odbc_result($resunix,1);

					if ($wexiste > 0){
						$existe = true;
					}
				}
			}

			$cuentasCredito = array();
			$cuentasDebito = array();

			/*******************************************************************************************************************
			 * CONSULTA PRINCIPAL.  Recibos dada la fuente en un rango de fechas
			 *******************************************************************************************************************/
			$q = "SELECT
						a.rennum, a.rencod, a.rennom, a.renvca, a.renfec, a.renfue, a.rencco, b.carroc, b.carabo
					FROM
						{$wbasedato}_000020 a, {$wbasedato}_000040 b
					WHERE
						a.renfec between '{$wfecini}' AND '{$wfecfin}'
						AND a.renest = 'on'
						AND a.renfue = '{$wfuecom}'
						AND b.carfue = a.renfue
					ORDER BY
						a.rennum, a.renfec";

//			echo $q."<br>";

			$rs = mysql_query($q,$conex) or die("Error en la consulta: ".$q." - ".mysql_error());

			while($fila = mysql_fetch_array($rs)){
				$cuentaResumida = new CuentaResumida();

				if($fila['carroc']== 'on' || $fila['carabo']== 'on'){
					$q2 = "SELECT
								a.empnit, a.empnom, b.relfuecta, b.relfuenat, b.relfuenit, b.relfueter
							FROM
								{$wbasedato}_000024 a, {$wbasedato}_000078 b
							WHERE
								a.empcod = '{$fila['rencod']}'
								AND b.relfuecod = '{$fila['renfue']}'
								AND b.relfuetem = (mid(a.emptem,1,instr(a.emptem,'-')-1))
								AND b.relfueest ='on'";

					$rs2 = mysql_query($q2,$conex) or die("Error en la consulta: ".$q2." - ".mysql_error());
					$fila2 = mysql_fetch_array($rs2);

					if($fila['carabo']== 'on' && $fila['rencod'] == '01'){
						$q3 = "SELECT
										fendpa, fennpa, fencod
									FROM
										{$wbasedato}_000021, {$wbasedato}_000018
									WHERE
										rdenum = '{$fila['rennum']}'
										AND rdefue = '{$fila['renfue']}'
										AND rdecco = '{$fila['rencco']}'
										AND fenffa = rdeffa
										AND fenfac = rdefac";

						$rs3 = mysql_query($q3,$conex) or die("Error en la consulta: ".$q3." - ".mysql_error());
						$fila3 = mysql_fetch_array($rs3);
					}
				} else { //Fin si carroc es on o carabo es on
					$q4 = "SELECT
								fendpa, fennpa, fencod
							FROM
								{$wbasedato}_000021 , {$wbasedato}_000018
							WHERE
								rdenum = '{$fila['rennum']}'
								AND rdefue = '{$fila['renfue']}'
								AND rdecco = '{$fila['rencco']}'
								AND fenffa = rdeffa
								AND fenfac=rdefac";

					$rs4 = mysql_query($q4,$conex) or die("Error en la consulta: ".$q4." - ".mysql_error());
					$fila4 = mysql_fetch_array($rs4);

					$q5 = "SELECT
							a.empnit,  a.empnom, b.relfuecta, b.relfuenat, b.relfuenit, b.relfueter
						FROM
						{$wbasedato}_000024 a, {$wbasedato}_000078 b
						WHERE
							a.empcod = '{$fila4['fencod']}'
							AND b.relfuecod = '{$fila['renfue']}'
							AND b.relfuetem = (mid(a.emptem,1,instr(a.emptem,'-')-1))
							AND b.relfueest ='on' ";

					$rs5 = mysql_query($q5,$conex) or die("Error en la consulta: ".$q5." - ".mysql_error());
					$fila5 = mysql_fetch_array($rs5);
				}

			//Llenado de cuenta
			$cuentaResumida->numero = $fila5['relfuecta'];
			$cuentaResumida->nombre = "";
			$cuentaResumida->naturaleza = $fila5['relfuenat'];
			$cuentaResumida->total = $fila['renvca'];

			if($cuentaResumida->naturaleza == 'C'){
				if(isset($cuentasCredito[$cuentaResumida->numero]) && !empty($cuentasCredito[$cuentaResumida->numero])){
					$temp = $cuentasCredito[$cuentaResumida->numero];
					$temp->total += $cuentaResumida->total;
					$cuentasCredito[$cuentaResumida->numero] = $temp;
				} else {
					$cuentasCredito[$cuentaResumida->numero] = $cuentaResumida;
				}
			} else {
				if(isset($cuentasDebito[$cuentaResumida->numero]) && !empty($cuentasDebito[$cuentaResumida->numero])){
					$temp = $cuentasDebito[$cuentaResumida->numero];
					$temp->total += $cuentaResumida->total;
					$cuentasDebito[$cuentaResumida->numero] = $temp;
				} else {
					$cuentasDebito[$cuentaResumida->numero] = $cuentaResumida;
				}
			}
			$cuentaResumida = new CuentaResumida();
			//Fin llenado de cuenta

				$q6 = "SELECT
						a.rdecon, SUM(a.rdevco) rdevco, b.condes, b.connat, b.concue, a.rdeccc, b.connit, b.conter
					FROM
						{$wbasedato}_000021 a, {$wbasedato}_000044 b
					WHERE
						a.rdenum = '{$fila['rennum']}'
						AND a.rdefue = '{$fila['renfue']}'
						AND a.rdecco = '{$fila['rencco']}'
						AND a.rdeest = 'on'
						AND b.confue = '{$fila['renfue']}'
						AND b.concod =trim(mid(a.rdecon,1,instr(a.rdecon,'-')-1))
						AND b.conest = 'on'
					GROUP BY
						a.rdecon, a.rdeccc";

				$rs6 = mysql_query($q6,$conex) or die("Error en la consulta: ".$q6." - ".mysql_error());

				while($fila6 = mysql_fetch_array($rs6)){

					//Llenado de cuenta
					$cuentaResumida->numero = $fila6['concue'];
					$cuentaResumida->nombre = $fila6['condes'];
					$cuentaResumida->naturaleza = $fila6['connat'];
					$cuentaResumida->total = $fila6['rdevco'];

					if($cuentaResumida->naturaleza == 'C'){
						if(isset($cuentasCredito[$cuentaResumida->numero]) && !empty($cuentasCredito[$cuentaResumida->numero])){
							$temp = $cuentasCredito[$cuentaResumida->numero];
							$temp->total += $cuentaResumida->total;
							$cuentasCredito[$cuentaResumida->numero] = $temp;
						} else {
							$cuentasCredito[$cuentaResumida->numero] = $cuentaResumida;
						}
					} else {
						if(isset($cuentasDebito[$cuentaResumida->numero]) && !empty($cuentasDebito[$cuentaResumida->numero])){
							$temp = $cuentasDebito[$cuentaResumida->numero];
							$temp->total += $cuentaResumida->total;
							$cuentasDebito[$cuentaResumida->numero] = $temp;
						} else {
							$cuentasDebito[$cuentaResumida->numero] = $cuentaResumida;
						}
					}
					$cuentaResumida = new CuentaResumida();
				}

				if($fila['carroc'] != 'on'){
					$q7 = "SELECT
							a.rfpbai, a.rfpvfp, b.bannom, b.bancue, rfpfpa, rfpdan
						FROM
							{$wbasedato}_000022 a, {$wbasedato}_000069 b
						WHERE
							a.rfpnum = '{$fila['rennum']}'
							AND a.rfpfue = '{$fila['renfue']}'
							AND a.rfpcco = '{$fila['rencco']}'
							AND a.rfpest = 'on'
							AND b.bancod = a.rfpbai
							AND b.banest = 'on' ";

					$rs7 = mysql_query($q7,$conex) or die("Error en la consulta: ".$q7." - ".mysql_error());
					while($fila7 = mysql_fetch_array($rs7)){

						//Llenado de cuenta
						$cuentaResumida->numero = $fila7['bancue'];
						$cuentaResumida->nombre = $fila7['bannom'];
						$cuentaResumida->naturaleza = '';
						$cuentaResumida->total = $fila7['rfpvfp'];

						if($cuentaResumida->naturaleza == 'C'){
							if(isset($cuentasCredito[$cuentaResumida->numero]) && !empty($cuentasCredito[$cuentaResumida->numero])){
								$temp = $cuentasCredito[$cuentaResumida->numero];
								$temp->total += $cuentaResumida->total;
								$cuentasCredito[$cuentaResumida->numero] = $temp;
							} else {
								$cuentasCredito[$cuentaResumida->numero] = $cuentaResumida;
							}
						} else {
							if(isset($cuentasDebito[$cuentaResumida->numero]) && !empty($cuentasDebito[$cuentaResumida->numero])){
								$temp = $cuentasDebito[$cuentaResumida->numero];
								$temp->total += $cuentaResumida->total;
								$cuentasDebito[$cuentaResumida->numero] = $temp;
							} else {
								$cuentasDebito[$cuentaResumida->numero] = $cuentaResumida;
							}
						}
						$cuentaResumida = new CuentaResumida();
					}
				}
			}

			//Si hay creditos y debitos
			if(count($cuentasCredito) > 0 || count($cuentasDebito) > 0){
				echo "<table align=center>";

				//Encabezado tabla
				echo "<tr class='encabezadoTabla' align='center'>";
				echo "<td>CUENTA</td>";
				echo "<td>NOMBRE</td>";
				echo "<td>DEBITOS</td>";
				echo "<td>CREDITOS</td>";
				echo "</tr>";

				$clase = "fila2";
				$item = 1;
				$wnat = "2";				//1: Debito, 2: Credito

				foreach ($cuentasCredito as $credito){

					if($clase == "fila1"){
						$clase = "fila2";
					} else {
						$clase = "fila1";
					}

					echo "<tr class='$clase' align='center'>";

					echo "<td>$credito->numero</td>";
					echo "<td>$credito->nombre</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>".number_format($credito->total,0,'.',',')."</td>";

					//Graba cada detalle de créditos
					if($graba && $puedeGrabar){
						if($existe){
							if($reemplaza){
								$q = "DELETE FROM
										comov
									WHERE
										movfue = '".$wfuecom."'
										AND movdoc = '".$wdoccom."'
										AND movano = '".$wfec[0]."'
										AND movmes = '".$wfec[1]."'";

								$resunix = odbc_do($conexunix,$q);
							}
						}
						$q = "INSERT INTO comov
									(movfue,movdoc,movane,movano,movmes,movite,movfec,movcue,movcco,movnit,movdes,movind,movval,movcon,movbas,movfac,movuni,movcam,movbaj,movanu)
								VALUES
									('{$wfuecom}','{$wdoccom}','','{$wfec[0]}','{$wfec[1]}','{$item}','{$wfeccor}','{$credito->numero}','','','COMPROBANTE FUENTE $wfuente','{$wnat}',{$credito->total},'',0,0,0,0,'N','0')";
						$res = odbc_do($conexunix,$q);

						$item++;
					}

					echo "</tr>";
					$totalCreditos += $credito->total;
				}

				$wnat = "1";				//1: Debito, 2: Credito
				foreach ($cuentasDebito as $debito){

					if($clase == "fila1"){
						$clase = "fila2";
					} else {
						$clase = "fila1";
					}

					echo "<tr class='$clase' align='center'>";

					echo "<td>$debito->numero</td>";
					echo "<td>$debito->nombre</td>";
					echo "<td>".number_format($debito->total,0,'.',',')."</td>";
					echo "<td>&nbsp;</td>";

					echo "</tr>";

					//Graba cada detalle de créditos
					if($graba && $puedeGrabar){
						if($existe){
							if($reemplaza){
								$q = "DELETE FROM
										comov
									WHERE
										movfue = '".$wfuecom."'
										AND movdoc = '".$wdoccom."'
										AND movano = '".$wfec[0]."'
										AND movmes = '".$wfec[1]."'";

//								echo $q;
								$resunix = odbc_do($conexunix,$q);
							}
						}
						$q = "INSERT INTO comov
									(movfue,movdoc,movane,movano,movmes,movite,movfec,movcue,movcco,movnit,movdes,movind,movval,movcon,movbas,movfac,movuni,movcam,movbaj,movanu)
								VALUES
									('{$wfuecom}','{$wdoccom}','','{$wfec[0]}','{$wfec[1]}','{$item}','{$wfeccor}','{$debito->numero}','','','COMPROBANTE FUENTE $wfuente','{$wnat}',{$debito->total},'',0,0,0,0,'N','0')";

//						echo $q;
						$res = odbc_do($conexunix,$q);

						$item++;
					}
					$totalDebitos += $debito->total;
				}

				//si se ha dado en grabar, grabo el encabezado del comprobante por recibo
				if ($graba) {
					if ($puedeGrabar){
						if($existe){
								$q = "DELETE FROM comovenc
									WHERE
										movencfue = '".$wfuecom."'
										AND movencdoc = '".$wdoccom."'
										AND movencano = '".$wfec[0]."' AND movencmes = '".$wfec[1]."'";

								$resunix = odbc_do($conexunix,$q);

						}
						$q = "INSERT INTO comovenc
								(movencano,movencmes,movencfue,movencdoc,movencusu,movencanu)
							VALUES
								('".$wfec[0]."','".$wfec[1]."','".$wfuecom."','".$wdoccom."','".$wusuario."', '0' )";

						$resunix = odbc_do($conexunix,$q);
					}
				}

				echo "<tr class='encabezadoTabla' align='center'>";
				echo "<td colspan=2>TOTAL</td>";
				echo "<td>".number_format($totalDebitos,0,'.',',')."</td>";
				echo "<td>".number_format($totalCreditos,0,'.',',')."</td>";
				echo "</tr>";
				echo "</table>";

			} else {
				echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningun documento en el rango de fechas seleccionado</td><tr>";
				echo "</table>";
			}
			break;
		default:	//Pantalla principal de filtros
			if($conexunix || true){
				//Cuerpo de la pagina
				echo "<table align='center' border=0>";

				//Ingreso de fecha de consulta
				echo '<span class="subtituloPagina2">';
				echo 'Par&aacute;metros de consulta de recibos';
				echo "</span>";
				echo "<br>";
				echo "<br>";

				//Fecha inicial del comprobante
				echo "<tr><td class='fila1'>Fecha inicial</td>";
				echo "<td class='fila2' align='center'>";
				if(isset($wfecini) && !empty($wfecini)){
					campoFechaDefecto("wfecini",$wfecini);
				} else {
					campoFecha("wfecini");
				}
				echo "</td></tr>";

				//Fecha final del comprobante
				echo "<tr><td class='fila1'>Fecha final</td>";
				echo "<td class='fila2' align='center'>";
				if(isset($wfecfin) && !empty($wfecfin)){
					campoFechaDefecto("wfecfin",$wfecfin);
				} else {
					campoFecha("wfecfin");
				}
				echo "</td></tr>";

				//Fuente
				echo "<tr><td class='fila1'>Fuente</td>";
				echo "<td class='fila2' align='center'>";

				echo "<select name='wfuente' class=seleccionNormal>";
				if (isset($wfuente)) //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
				{
					$q= "SELECT carfue, cardes FROM ".$wbasedato."_000040 WHERE carfue != (mid('".$wfuente."',1,instr('".$wfuente."','-')-1)) AND carrec ='on'";

					$res1 = mysql_query($q,$conex);
					$num1 = mysql_num_rows($res1);
					for ($i=1;$i<=$num1;$i++)
					{
						$row1 = mysql_fetch_array($res1);
						echo "<option value='$row1[0]'>".$row1[0]." - ".$row1[1]."</option>";
					}
				} else {
					$q= "SELECT carfue, cardes FROM ".$wbasedato."_000040 WHERE carrec ='on'";

					$res1 = mysql_query($q,$conex);
					$num1 = mysql_num_rows($res1);
					for ($i=1;$i<=$num1;$i++)
					{
						$row1 = mysql_fetch_array($res1);
						echo "<option value='$row1[0]'>".$row1[0]." - ".$row1[1]."</option>";
					}
				}
				echo "</select>";
				echo "</td></tr>";

				echo "<tr><td>";
				echo "<br>";
				echo '<span class="subtituloPagina2">';
				echo 'Par&aacute;metros del comprobante';
				echo "</span>";
				echo "<br>";
				echo "<br>";
				echo "</td></tr>";

				//Fecha del comprobante
				echo "<tr><td class='fila1'>Fecha</td>";
				echo "<td class='fila2' align='center'>";
				if(isset($wfeccor) && !empty($wfeccor)){
					campoFechaDefecto("wfeccor",$wfeccor);
				} else {
					campoFecha("wfeccor");
				}
				echo "</td></tr>";

				//Numero de comprobante
				echo "<tr><td class='fila1'>N&uacute;mero</td>";
				echo "<td class='fila2' align='center'>";
				if(isset($wdoccom)){
					echo "<input type='text' name='wdoccom' maxlength=7 value='$wdoccom' id='wdoccom'>";
				} else {
					echo "<input type='text' name='wdoccom' maxlength=7 id='wdoccom'>";
				}
				echo "</td></tr>";

				//Fuente comprobante
				echo "<tr><td class='fila1'>Fuente</td>";
				echo "<td class='fila2' align='center'>";
				if(isset($wfuecom)){
					echo "<input type='text' name='wfuecom' id='wfuecom' value='$wfuecom' maxlength='2' onKeyPress='return validarEntradaEntera(event);'>";
				} else {
					echo "<input type='text' name='wfuecom' id='wfuecom' maxlength='2' onKeyPress='return validarEntradaEntera(event);'>";
				}
				echo "</td></tr>";

				echo "<tr><td><br></td></tr>";

				//Grabacion reemplazo comprobante

				echo "<tr class=seccion1 ><td ><b>Base de Datos Destino:</b></td><td><select id='selectBdUnix' name='selectBdUnix'>";


				$q="SELECT Conexion, Aplicacion
					  FROM  root_000023
					 WHERE  Empresa ='".$wemp_pmla."'
					   AND  Tipo_aplicacion='contabilidad'";

				$res = mysql_query($q,$conex);
				echo "<option value='' >Seleccione...</option>";

				while( $row = mysql_fetch_array($res))
				{
					echo "<option value='".$row['Conexion']."' >".$row['Aplicacion']." (".$row['Conexion'].")</option>";
				}
				echo "</select></td>


				</tr><tr><td class='fila2' colspan=2 align='center'>";
				echo "<input type='checkbox' name='gra'>Grabar comprobante&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='ree'>Reemplazar si existe";
				echo "</td></tr>";

				//Resumido o detallado
				echo "<tr><td class='fila2' colspan=2 align='center'>";
				echo "<input type='radio' id='wtiporep' name='wtiporep' value='d' checked>Detallado";
				echo "<input type='radio' id='wtiporep' name='wtiporep' value='r'>Resumido";
				echo "</td></tr>";

				echo "<tr><td class='fila1' colspan=2 align='center'>";
				echo "<input type='button' name='comprobante' value='Generar comprobante' onClick='javascript:consultar();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'>";
				echo "</b></td></tr></table></br>";
			} else {
				echo '<span class="subtituloPagina2" align="center">';
				echo 'No se pudo establecer conexi&oacute;n con Unix';
				echo "</span><br><br>";

				terminarEjecucion("Por favor comuniquese con el área de soporte.   Dirección de informática");
			}
		break;
	}
	if(isset($gra))
	{
		odbc_close($conexunix);
		odbc_close_all();
	}
}
?>
</body>
</html>