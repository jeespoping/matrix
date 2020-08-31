<html>
	<head>
	<title>MATRIX - [COMPROBANTE DE CONSIGNACION INTERNA]</title>
	<script type="text/javascript">
	function Seleccionar(){
		document.forma.submit();
	}
	/******************************************************************************************************************************
	 *Redirecciona a la pagina inicial
	 ******************************************************************************************************************************/
	function inicio(){
		document.location.href='comConsignacion.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&wbasedato='+document.forms.forma.wbasedato.value;
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

		var url = 'comConsignacion.php?wemp_pmla='+wemp_pmla+'&wbasedato='+wbasedato+'&waccion='+accion+'&wfecini='+fini+'&wfecfin='+ffin+'&wfuente='+fuente+'&wfeccor='+fechaComprobante+'&wdoccom='+numeroComprobante+'&wfuecom='+fuenteCombrobante;

		if(graba){
			url += '&gra=on';
		}

		if(reemplaza){
			url += '&ree=on';
		}

//		alert(url);
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
 * NOMBRE:  COMPROBANTE DE CONSIGNACION
 *
 * PROGRAMA: comConsignacion.php
 * TIPO DE SCRIPT: PRINCIPAL
 * //DESCRIPCION:Este comprobante muestra la partida y contrapartida de los movimientos de consignacion interna
 *
 * HISTORIAL DE ACTAULIZACIONES:
 * 2012-08-15 Camilo Zapata, se modifico el script para que funcione en uvglobal sin conectarse a unix, quemando un if que verifica que la empresa no sea
							 uvglobal antes de intentar hacer el link a unix
 * 2006-05-17 carolina castano, creacion del script
 * 2009-12-01 msanchez: Generación de comprobantes resumidos, actualizacion con el comun.php
 *
 * Tablas que utiliza:
 * $wbasedato."_000040: Maestro de Fuentes, select
 * $wbasedato."_000075: select de documentos entre dos fechas
 * $wbasedato."_000069: select en maestro de bancos para saber las cuentas
 *
 * @author ccastano
 * @package defaultPackage
 */
//=================================================================================================================================
include_once("root/comun.php");

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

	$wactualiz = "2012-08-15";
	$key = substr($user,2,strlen($user));

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	$wbasedato = $institucion->baseDeDatos;

	$basedatosUnix = consultarAliasPorAplicacion($conex,$wemp_pmla,"unix_contabilidad");
	if($wemp_pmla!=06)
		$conexunix = odbc_connect($basedatosUnix,'informix','sco') or die("No se realizo conexion con unix");//

	//Encabezado
	encabezado("Comprobante de consignacion interna",$wactualiz,"logo_".$wbasedato);

	echo "<form action='comConsignacion.php' method=post name='forma'>";

	$wfecha=date("Y-m-d");

	//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

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
			echo "<tr><td><B>Fecha: $wfecha</B></td></tr>";

			echo "<tr><td><B>COMPROBANTE CONSIGNACION INTERNA</B></td></tr>";
			echo "</tr><td align=right ><A href='comConsignacion.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wfeccor=".$wfeccor."&amp;wfuente=".$wfuente."&amp;bandera='1'>Volver</A>&nbsp;|&nbsp;<a href='javascript:cerrarVentana();'>Cerrar</a></td></tr>";
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
			echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

			echo "<table align=center>";

			//Encabezado tabla
			echo "<tr class='encabezadoTabla' align='center'>";
			echo "<td>FUENTE</td>";
			echo "<td>DOCUMENTO</td>";
			echo "<td>FECHA</td>";
			echo "<td>CUENTA</td>";
			echo "<td>NOMBRE</td>";
			echo "<td>AUTORIZACION</td>";
			echo "<td>DEBITOS</td>";
			echo "<td>CREDITOS</td>";
			echo "</tr>";

			$q = " SELECT  a.tennum, a.tencai, a.tencaf, a.tenval, a.tenfec, a.tenfue, a.tenaci "
			."    FROM ".$wbasedato."_000075 a "
			."   	WHERE  a.tenfec between '".$wfecini."'"
			."     AND '".$wfecfin."'"
			."     AND a.tenest = 'on' "
			."     AND a.tenfue = '".$wfuecom."' "
			."     ORDER BY  a.tennum, a.tenfec ";

			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);

			//si hay resultados grabo el encabezado del comprobante
			$wgraba="off";
			$sw="off";
			$inicial=strpos($user,"-");
			$wusuario=substr($user, $inicial+1, strlen($user));

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

					$q = " SELECT count(*) "
					."   FROM sicie "
					."  WHERE cieanc = '".$wfec[0]."'"
					."    AND ciemes = '".$wfec[1]."'"
					."    AND ciefec <> '' "
					."    AND cieapl= 'CONTAB' ";

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

						$q = " SELECT count(*) "
						."   FROM comovenc "
						." WHERE movencfue = '".$wfuecom."'"
						."   AND movencdoc = '".$wdoccom."'"
						."   AND movencanu = '0' "
						."   and movencano='".$wfec[0]."' "
						."   and movencmes='".$wfec[1]."' ";

						$resunix = odbc_do($conexunix,$q);
						$wexiste=odbc_result($resunix,1);

						if ($wexiste > 0)
						{
							if (isset($ree))
							{
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
							}
							else
							{
								$wgraba="off";
								$sw="off";
								$exito=1;
							}
						}
						else  //SI NO EXISTE LO GRABO
						{
							$wfec=explode("-",$wfeccor);

							$q = "INSERT INTO comovenc(   movencano  ,   movencmes  ,   movencfue   ,   movencdoc   ,   movencusu   , movencanu) "
							."              VALUES('".$wfec[0]."','".$wfec[1]."','".$wfuecom."','".$wdoccom."','".$wusuario."', '0' )   ";
							$resunix = odbc_do($conexunix,$q);

							$sw="on";
							$exito=0;
						}
					}else
					{
						$wgraba="off";
						$sw = "off";
						$exito=2;
					}
				}
				else
				{
					$exito=0;
				}

				$q = " SELECT  b.bannom, b.bancue "
				."    FROM   ".$wbasedato."_000069 b "
				."   	WHERE  b.bancod = '".$row[2]."' ";

				$err3 = mysql_query($q,$conex);
				$num3 = mysql_num_rows($err3);
				$row3 = mysql_fetch_array($err3);

				echo "<td align=CENTER class=".$clase1."  width='5%'>".$row[5]."</td>";
				echo "<td align=CENTER class=".$clase1."  width='5%'>".$row[0]."</td>";
				echo "<td align=CENTER class=".$clase1."  width='10%'>".$row[4]."</td>";
				echo "<td align=CENTER class=".$clase1."  width='5%'>".$row3[1]."</td>";
				echo "<td align=CENTER class=".$clase1."  width='15%'>".$row3[0]."</td>";
				if ($row[6]!='')
				{
					echo "<td align=CENTER class=".$clase1."  width='15%'>".$row[6]."</td>";
				}
				else
				{
					echo "<td align=CENTER class=".$clase1."  width='15%'>&nbsp;</td>";
				}
				echo "<td align=CENTER class=".$clase2." width='10%'>".number_format($row[3],0,'.',',')."</td>";
				echo "<td align=CENTER class=".$clase2." width='10%'>&nbsp;</td>";
				$wtdebito=$wtdebito+$row[3];
				echo '</tr>';

				if ($sw == "on")
				{
					$wnat="1";
					$wfec=explode("-",$wfeccor);

					$q = "INSERT INTO comov(movfue,        movdoc,   movane,     movano,        movmes,     movite,    movfec,        movcue,       movcco,       movnit,       movdes,                             movind,    movval,   movcon,  movbas,  movfac, movuni, movcam, movbaj, movanu) "
					."           VALUES('".$wfuecom."','".$wdoccom."', '".$row[6]."', '".$wfec[0]."','".$wfec[1]."',".$k." ,'".$wfeccor."','".$row3[1]."',   '',             '','COMPROBANTE FUENTE ".$wfuente."',       '".$wnat."',".$row[3].", ''    , 0     , 0     , 0     , 0     , 'N'   , '0' )   ";
					$res = odbc_do($conexunix,$q);
					$k++;
				}

				$q = " SELECT  b.bannom, b.bancue "
				."    FROM   ".$wbasedato."_000069 b "
				."   	WHERE  b.bancod = '".$row[1]."' ";

				$err3 = mysql_query($q,$conex);
				$num3 = mysql_num_rows($err3);
				$row3 = mysql_fetch_array($err3);

				echo "<td align=CENTER class=".$clase1." width='5%'>".$row[5]."</td>";
				echo "<td align=CENTER class=".$clase1." width='5%'>".$row[0]."</td>";
				echo "<td align=CENTER class=".$clase1." width='10%'>".$row[4]."</td>";
				echo "<td align=CENTER class=".$clase1." width='5%'>".$row3[1]."</td>";
				echo "<td align=CENTER class=".$clase1." width='15%'>".$row3[0]."</td>";
				echo "<td align=CENTER class=".$clase1." width='5%'>&nbsp;</td>";
				echo "<td align=CENTER class=".$clase2." width='10%'>&nbsp;</td>";
				echo "<td align=CENTER class=".$clase2." width='10%'>".number_format($row[3],0,'.',',')."</td>";

				$wtcredito=$wtcredito+$row[3];
				echo '</tr>';

				if ($sw == "on")
				{
					$wnat="2";
					$wfec=explode("-",$wfeccor);

					$q = "INSERT INTO comov(movfue,        movdoc,   movane,     movano,        movmes,     movite,    movfec,        movcue,       movcco,       movnit,       movdes,                             movind,    movval,   movcon,  movbas,  movfac, movuni, movcam, movbaj, movanu) "
					."           VALUES('".$wfuecom."','".$wdoccom."', '', '".$wfec[0]."','".$wfec[1]."',".$k." ,'".$wfeccor."','".$row3[1]."',   '',             '','COMPROBANTE FUENTE ".$wfuente."',       '".$wnat."',".$row[3].", ''    , 0     , 0     , 0     , 0     , 'N'   , '0' )   ";
					$res = odbc_do($conexunix,$q);

					$k++;
				}

				echo "<tr>";
				echo "<th align=CENTER class='$clase2' colspan='6' >TOTAL DOCUMENTO</th>";
				if ($wtdebito==$wtcredito)
				{
					echo "<th align=CENTER class=".$clase1.">".number_format($wtdebito,0,'.',',')."</th>";
					echo "<th align=CENTER class=".$clase1.">".number_format($wtcredito,0,'.',',')."</th>";
				}
				else
				{
					echo "<th align=CENTER class=".$clase1.">".number_format($wtdebito,0,'.',',')."</th>";
					echo "<th align=CENTER class=".$clase1.">".number_format($wtcredito,0,'.',',')."</th>";
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

			if ($cuenta==0)
			{
				echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningun documento en el rango de fechas seleccionado</td><tr>";
			}

			else if ($cuenta>0)
			{
				echo "<tr class='encabezadoTabla'>";
				echo "<th align=CENTER colspan='6' >TOTAL</th>";
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
			echo "<center><A href='comConsignacion.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wfeccor=".$wfeccor."&amp;wfuente=".$wfuente."&amp;bandera='1'>Volver</A>&nbsp;|&nbsp;<a href='javascript:cerrarVentana();'>Cerrar</a></center>";

			if (isset ($wgraba) and $wgraba=='on'){
				mensajeEmergente("EL COMPROBANTE HA SIDO REEMPLAZADO CON EXITO");
			}elseif (isset ($sw) and $sw=='on'){
				mensajeEmergente("EL COMPROBANTE HA SIDO INGRESADO CON EXITO");
			}
		break;
	case 'b':
			echo "<table align=center width='60%'>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td><B>Fecha: ".$wfecha."</B></td></tr>";

			echo "<tr><td><B>COMPROBANTE CONSIGNACION INTERNA</B></td></tr>";
			echo "</tr><td align=right><A href='comConsignacion.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wfeccor=".$wfeccor."&amp;wfuente=".$wfuente."&amp;wbasedato=$wbasedato'>Volver</A>&nbsp;|&nbsp;<a href='javascript:cerrarVentana();'>Cerrar</a></td></tr>";
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
			if (isset($gra))
			{
				$graba = true;
			}

			$reemplaza = false;
			if (isset($ree))
			{
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

			/**
			 * CONSULTA DE LAS CUENTAS DE NATURALEZA CREDITO
			 */
			$q = "SELECT
					SUM( Tenval ) tenval, Tencaf, Bannom, Bancue
				FROM
					{$wbasedato}_000075, {$wbasedato}_000069
				WHERE
					Tenfec BETWEEN '{$wfecini}' AND '{$wfecfin}'
					AND Tenest = 'on'
					AND Bancod = Tencaf
					AND Tenfue = '{$wfuecom}'
				GROUP BY
					Tencaf";

			$err = mysql_query($q,$conex) or die("Error en la consulta: ".$q." - ".mysql_error());
			$num = mysql_num_rows($err);

			while($fila = mysql_fetch_array($err)){
				$reg = new RegistroTriple();

				$reg->campo1 = $fila['Bancue'];
				$reg->campo2 = $fila['tenval'];
				$reg->campo3 = $fila['Bannom']." (".$fila['Tencaf'].")";

				$totalCreditos += $reg->campo2;

				$colCreditos[] = $reg;
			}

			/*
			 * CONSULTA DE LAS CUENTAS DE NATURALEZA DEBIT
			 */

			$q = "SELECT
					SUM( Tenval ) tenval, tencai, Bannom, Bancue
				FROM
					{$wbasedato}_000075, {$wbasedato}_000069
				WHERE
					Tenfec BETWEEN '{$wfecini}' AND '{$wfecfin}'
					AND Tenest = 'on'
					AND Bancod = tencai
					AND Tenfue = '{$wfuecom}'
				GROUP BY
					tencai";

			$err = mysql_query($q,$conex) or die("Error en la consulta: ".$q." - ".mysql_error());
			$num = mysql_num_rows($err);

			while($fila = mysql_fetch_array($err)){
				$reg = new RegistroTriple();

				$reg->campo1 = $fila['Bancue'];
				$reg->campo2 = $fila['tenval'];
				$reg->campo3 = $fila['Bannom']." (".$fila['tencai'].")";;

				$totalDebitos += $reg->campo2;

				$colDebitos[] = $reg;
			}

			//Si hay creditos y debitos
			if(count($colCreditos) > 0 || count($colDebitos) > 0){
				echo "<table align=center>";

				//Encabezado tabla
				echo "<tr class='encabezadoTabla' align='center'>";
				echo "<td>CUENTA</td>";
				echo "<td>DEBITOS</td>";
				echo "<td>CREDITOS</td>";
				echo "</tr>";

				$clase = "fila2";
				$item = 1;
				$wnat = "2";				//1: Debito, 2: Credito

				foreach ($colCreditos as $credito){

					if($clase == "fila1"){
						$clase = "fila2";
					} else {
						$clase = "fila1";
					}

					echo "<tr class='$clase'>";

					echo "<td>$credito->campo1 - $credito->campo3</td>";
					echo "<td>&nbsp;</td>";
					echo "<td align='right'>".number_format($credito->campo2,0,'.',',')."</td>";

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
									('{$wfuecom}','{$wdoccom}','','{$wfec[0]}','{$wfec[1]}','{$item}','{$wfeccor}','{$credito->campo1}','','','COMPROBANTE FUENTE $wfuente','{$wnat}',{$credito->campo2},'',0,0,0,0,'N','0')";

						$res = odbc_do($conexunix,$q);

						$item++;
					}

					echo "</tr>";
				}

				$wnat = "1";				//1: Debito, 2: Credito
				foreach ($colDebitos as $debito){

					if($clase == "fila1"){
						$clase = "fila2";
					} else {
						$clase = "fila1";
					}

					echo "<tr class='$clase'>";

					echo "<td>$debito->campo1 - $debito->campo3</td>";
					echo "<td align='right'>".number_format($debito->campo2,0,'.',',')."</td>";
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
								$resunix = odbc_do($conexunix,$q);
							}
						}
						$q = "INSERT INTO comov
									(movfue,movdoc,movane,movano,movmes,movite,movfec,movcue,movcco,movnit,movdes,movind,movval,movcon,movbas,movfac,movuni,movcam,movbaj,movanu)
								VALUES
									('{$wfuecom}','{$wdoccom}','','{$wfec[0]}','{$wfec[1]}','{$item}','{$wfeccor}','{$debito->campo1}','','','COMPROBANTE FUENTE $wfuente','{$wnat}',{$debito->campo2},'',0,0,0,0,'N','0')";

						$res = odbc_do($conexunix,$q);

						$item++;
					}
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
				echo "<td>TOTAL</td>";
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
	default:  //Pantalla principal de filtros
		if(!isset($conexunix))
			$conexunix='';
		if($conexunix || true){

			//Cuerpo de la pagina
			echo "<table align='center' border=0>";

			//Ingreso de fecha de consulta
			echo '<span class="subtituloPagina2">';
			echo 'Par&aacute;metros de consulta';
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

			if (isset($wfuente)) //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
			{
				$q="SELECT
						carfue, cardes
					FROM
						{$wbasedato}_000040
					WHERE
						carfue != (mid('{$wfuente}',1,instr('{$wfuente}','-')-1))
						AND carcsg = 'on'";

				$res1 = mysql_query($q,$conex);
				$num1 = mysql_num_rows($res1);
			} else {
				$q= "   SELECT carfue, cardes "
				."     FROM ".$wbasedato."_000040 "
				."      where carcsg ='on'  ";

				$res1 = mysql_query($q,$conex);
				$num1 = mysql_num_rows($res1);
			}
			echo "<select name='wfuente' class=seleccionNormal>";

			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				echo "<option value='$row1[0]'>".$row1[0]." - ".$row1[1]."</option>";
			}

			echo "</select></td>";

			echo "<tr><td>";
			echo "<br>";
			echo '<span class="subtituloPagina2">';
			echo 'Par&aacute;metros del comprobante';
			echo "</span>";
			echo "<br>";
			echo "<br>";
			echo "</td></tr>";

			//Fecha del comprobante
			echo "<tr><td class='fila1'>Fecha del comprobante</td>";
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
				echo "<input type='text' name='wdoccom' maxlength=7 value='$wdoccom'>";
			} else {
				echo "<input type='text' name='wdoccom' maxlength=7>";
			}
			echo "</td></tr>";

			//Fuente comprobante
			echo "<tr><td class='fila1'>Fuente</td>";
			echo "<td class='fila2' align='center'>";
			if(isset($wfuecom)){
				echo "<input type='text' name='wfuecom' value='$wfuecom' maxlength='2' onKeyPress='return validarEntradaEntera(event);'>";
			} else {
				echo "<input type='text' name='wfuecom' maxlength='2' onKeyPress='return validarEntradaEntera(event);'>";
			}
			echo "</td></tr>";

			echo "<tr><td><br></td></tr>";

			echo "<input type='HIDDEN' NAME= 'bandera' value='2'>";
			echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

			//Grabacion reemplazo comprobante
			if($wemp_pmla!=06)
			{
				echo "<tr><td class='fila2' colspan=2 align='center'>";
				echo "<input type='checkbox' name='gra'>Grabar comprobante&nbsp;|&nbsp;<input type='checkbox' name='ree'>Reemplazar si existe";
				echo "</td></tr>";
			}

			//Resumido o detallado
			echo "<tr><td class='fila2' colspan=2 align='center'>";
			echo "<input type='radio' id='wtiporep' name='wtiporep' value='d' checked>Detallado&nbsp;|&nbsp;";
			echo "<input type='radio' id='wtiporep' name='wtiporep' value='r'>Resumido";
			echo "</td></tr>";

			echo "<tr><td colspan=2 align='center'>";
			echo "<input type='button' name='comprobante' value='Generar comprobante' onClick='javascript:consultar();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'>";
			echo "</b></td></tr></table></br>";
		}else {
			echo '<span class="subtituloPagina2" align="center">';
			echo 'No se pudo establecer conexi&oacute;n con Unix';
			echo "</span><br><br>";

			terminarEjecucion("Por favor comuniquese con el área de soporte.   Dirección de informática");
		}
		break;
	}
	odbc_close($conexunix);
	odbc_close_all();
}
?>
</body>
</html>