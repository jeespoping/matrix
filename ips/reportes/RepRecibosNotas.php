<html>
<head>
  <title>MATRIX - [REPORTE DE RECIBOS DE CAJA, NOTAS DEBITO Y CREDITO]</title>

<script language="javascript">
   function Seleccionar()
   {
   	document.forma.submit();
   }
   /******************************************************************************************************************************
	 *Redirecciona a la pagina inicial
	 ******************************************************************************************************************************/
	function inicio(){
		document.location.href='RepRecibosNotas.php?wbasedato='+document.forms.forma.wbasedato.value;	
	}
	/*****************************************************************************************************************************
	 * Consulta de recibos 
	 ******************************************************************************************************************************/
	function consultar(){
		var wemp_pmla = document.forms.forma.wemp_pmla.value;
		var fini = document.forms.forma.wfecini.value;
		var ffin = document.forms.forma.wfecfin.value;
		var fuente = document.forms.forma.wfue.value;
		var empresa = document.forms.forma.wemp.value;
		
		var tipoReporte = document.forms.forma.vol;

		var mostrarValorCancelar = (document.forms.forma.can && document.forms.forma.can.checked == true) ? true : false;

		var accion = '';

		if(tipoReporte[0].checked){
			accion = 'a';
		}

		if(tipoReporte[1].checked){
			accion = 'a';
		}
		
		var url = 'RepRecibosNotas.php?wemp_pmla='+wemp_pmla+'&waccion='+accion+'&wfecini='+fini+'&wfecfin='+ffin+'&wfue='+fuente+'&wemp='+empresa+'&vol=SI';

		/*
		if(mostrarValorCancelar){
			url += '&vol=SI'; 
		} else {
			url += '&vol=NO';
		}
		*/

//		alert(url);
		document.location.href = url;
	}
</script>
</head>
<body>
<?php
include_once("conex.php");

/**
 * NOMBRE:  REPORTE DE RECIBOS Y NOTAS
 *
 * PROGRAMA: RepRecibosNotas.php
 * TIPO DE SCRIPT: PRINCIPAL
 * //DESCRIPCION:Este reporte presenta la lista de notas debito o notas credito entre dos fechas
 * 
 * HISTORIAL DE ACTAULIZACIONES:
 * 2006-06-20 carolina castano, creacion del script
 * 2006-10-12 carolina castano, cambios de forma, presentación
 * 2009-11-20 MSanchez:  Actualizacion con el comun.php y adicion de campos nuevos
 * 2011-10-19 Mario Cadavid:  Se le adicionó la variable 'vol=SI' a la url cuando se consulta para que siempre 
 * 							  se muestre la fuente y número de la factura en los resultados del reporte, solo se 
 * 							  mostraban cuando cuando se chequeaba "Consultar valor a cancelar"
 * 2013-10-11 Jonatan Lopez: 	Se agrega una columna con el usuario que facturo al final de cada tabla, esto para diferenciar las areas de negocio
 * 								domiciliaria y clinica (requerimiento 2923)
 * 
 * Tablas que utiliza:
 * $wbasedato."_000040: Maestro de Fuentes, select
 * $wbasedato."_000024: select en maestro de empresas
 * $wbasedato."_000020: select en encabezado de cartera
 * $wbasedato."_000021: select en detalle de cartera
 * $wbasedato."_000065: select en detalle por conceptos de facturacion
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
	$wactualiz = " Octubre 11 de 2013";
	$key = substr($user,2,strlen($user));

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower($institucion->baseDeDatos);
	$wentidad = $institucion->nombre;

	//Encabezado
	encabezado("Reporte de recibos, notas debito y credito",$wactualiz,"logo_".$wbasedato);
	
	echo "<form action='RepRecibosNotas.php' method=post name='forma'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	$wfecha=date("Y-m-d");
	
	//Esta funcion identifica el usuario que facturó.
	function usuario_que_factura($wfactura, $wfuentefactura)
	{
	
	global $conex;
	global $wbasedato;
	
	//Busco en la tabla 18 quien realizo la factura.
	$q_usu_fac = " SELECT Seguridad "
				."   FROM  ".$wbasedato."_000018"
				."  WHERE Fenfac = '".$wfactura."'"
				."	  AND Fenffa = '".$wfuentefactura."'";
	$res_usu_fac = mysql_query($q_usu_fac,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuario." - ".mysql_error());
	$row_usu_fac = mysql_fetch_array($res_usu_fac);
	$wusuariofac = $row_usu_fac['Seguridad'];
	$wdatos_usuario = explode("-", $wusuariofac);
	
	$wcodigo_usu = $wdatos_usuario[1];
	
	//Busco el nombre del usuario.
	$q_usuario = " SELECT descripcion "
				."   FROM usuarios "
				."  WHERE codigo = '".$wcodigo_usu."'";
	$res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuario." - ".mysql_error());
	$row_usuario = mysql_fetch_array($res_usuario);
	$wnombre = $row_usuario['descripcion'];
	
	return $wnombre;
	
	}
	
	
	//Estrategia de FC con parámetro waccion
	if(!isset($waccion)){
		$waccion = "";
	}
	
	switch ($waccion){
		case 'a':
			echo "<table align=center width='60%'>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td><B>Fecha: ".$wfecha."</B></td></tr>";
			echo "<tr><td><B>REPORTE DE: ".$wfue."</B></td></tr>";
			echo "<tr><td><tr><td>Fecha inicial: ".$wfecini."</td></tr>";
			echo "<tr><td>Fecha final: ".$wfecfin."</td></tr>";
			echo "<tr><td>Empresa: ".$wemp."</td></tr>";
			echo "<tr><td align=right><A href='RepRecibosNotas.php?wemp_pmla=".$wemp_pmla."&wfecfin=".$wfecfin."&wfecini=".$wfecini."&wfue=".$wfue."&wemp=".$wemp."'>VOLVER</A>&nbsp;|&nbsp;<A href='javascript:void(0);' onClick='javascript:cerrarVentana();'>Cerrar ventana</a></td></tr>";
			echo "</table></br>";
			
			echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
			echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
			echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
			echo "<input type='HIDDEN' NAME= 'wfue' value='".$wfue."'>";
			echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

			$print=explode ('-',$wfue);
			$fuente=$print[0];

			if ($wemp != '% - Todas las empresas') {
				$print=explode('-', $wemp);
				$empCod[0]=trim ($print[0]);
				$empNom[0]=trim ($print[2]);
				$empNit[0]=trim ($print[1]);
				$empTip[0]=trim ($print[2]).'-'.trim ($print[3]);
				$print=substr($empTip[0],5, strlen($empTip[0]));
				$empTip[0]=substr($print,0, (strlen($print)-5));

				$empresa[0]=$empNom[0];
				$num=1;
			} else {
				$q =  "SELECT empcod, empnom, empnit "
				."   FROM ".$wbasedato."_000024 "
				."  WHERE empcod=empres "
				."  ORDER BY 3 desc ,1 ";

				$res = mysql_query($q,$conex);
				$num = mysql_num_rows($res);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($res);
					$empCod[$i]=$row[0];
					$empNom[$i]=$row[1];
					$empNit[$i]=$row[2];
					$empresa[$i]=$row[1];
					$empleado[$i]='off';
				}
				
				$auxNum = $num;
				
				$q = " SELECT SUBSTRING_INDEX( emptem , '-', 1 ) as empcod, SUBSTRING_INDEX( emptem , '-', -1 ) as empnom, SUBSTRING_INDEX( emptem , '-', 1 ) as empnit "
	            . "   FROM " . $wbasedato . "_000024 "
	            . "  WHERE empcod != empres "
	            . "  GROUP BY emptem "
	            . "  ORDER BY empnom ";

				$res = mysql_query($q,$conex) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
				$num = mysql_num_rows($res);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($res);
					$empCod[$auxNum+$i]=$row[0];
					$empNom[$auxNum+$i]=$row[1];
					$empNit[$auxNum+$i]=$row[2];
					$empresa[$auxNum+$i]=$row[1];
					$empleado[$auxNum+$i]='on';
				}
				
				$num = $num + $auxNum; 
			}

			//identifico si el documento es un recibo
			$q= "   SELECT carrec  "
			."     FROM ".$wbasedato."_000040 "
			."    WHERE carfue = '".$fuente."' ";
			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);
			if ($row1[0]=='on')
			{
				$wcarrec=true;
			} else {
				$wcarrec=false;
			}

			//se busca en la tabla 20 y 21 registros con esa fuente, empresa por empresa en un for y entre las fechas escogidas
			$senal=0;
			$wtotal=0;
			for ($i=0;$i<$num;$i++)
			{
				if( $empleado[$i] != 'on' ){
					
					if ($vol=='SI') {
						$q = " SELECT  a.rennum, a.rencod, a.renvca, a.renfec, b.rdefac, b.rdevca, b.rdevco, b.rdeffa, b.rdecon, b.rdecco"
						."    FROM ".$wbasedato."_000020 a, ".$wbasedato."_000021 b "
						."   	WHERE  a.renfec between '".$wfecini."'"
						."     AND '".$wfecfin."'"
						."     AND a.renest = 'on' "
						."     AND a.rencod = '".$empCod[$i]."' "
						."     AND a.renfue = '".$fuente."' "
						."     AND b.rdefue = a.renfue "
						."     AND b.rdenum = a.rennum "
						."     AND b.rdecco = a.rencco 
							   ORDER BY  a.rennum, b.rdefac, a.renfec ";
					} else {
						$q = " SELECT  a.rennum, a.rencod, a.renvca, a.renfec,  b.rdecco, sum(b.rdevca), sum(b.rdevco)"
						."    FROM ".$wbasedato."_000020 a, ".$wbasedato."_000021 b"
						."   	WHERE  a.renfec between '".$wfecini."'"
						."     AND '".$wfecfin."'"
						."     AND a.renest = 'on' "
						."     AND a.rencod = '".$empCod[$i]."' "
						."     AND a.renfue = '".$fuente."' "
						."     AND b.rdefue = a.renfue "
						."     AND b.rdenum = a.rennum "
						."     AND b.rdecco = a.rencco 
						group BY  b.rdenum, b.rdefue, b.rdecco";
					}
				}
				else{
					if ($vol=='SI') {
					    	$q = " SELECT  a.rennum, a.rencod, a.renvca, a.renfec, b.rdefac, b.rdevca, b.rdevco, b.rdeffa, b.rdecon, b.rdecco"
							."    FROM ".$wbasedato."_000020 a, ".$wbasedato."_000021 b, {$wbasedato}_000018 c "
							."   WHERE c.fentip = '{$empCod[$i]}-{$empNom[$i]}' "
							." 	   AND c.fenres != c.fencod "
							." 	   AND c.fencod = a.rencod "
							." 	   AND a.renfec between '".$wfecini."'"
							."     AND '".$wfecfin."'"
							."     AND a.renest = 'on' "
							."     AND a.renfue = '".$fuente."' "
							."     AND b.rdefue = a.renfue "
							."     AND b.rdenum = a.rennum "
							."     AND b.rdecco = a.rencco 
								   ORDER BY  a.rennum, b.rdefac, a.renfec ";
						} else {
							$q = " SELECT  a.rennum, a.rencod, a.renvca, a.renfec,  b.rdecco, sum(b.rdevca), sum(b.rdevco)"
							."    FROM ".$wbasedato."_000020 a, ".$wbasedato."_000021 b, {$wbasedato}_000018 c "
							."   WHERE c.fentip = '{$empCod[$i]}-{$empNom[$i]}' "
							."	   AND c.fenres != c.fencod "
							." 	   AND c.fencod = a.rencod "
							."	   AND a.renfec between '".$wfecini."'"
							."     AND '".$wfecfin."'"
							."     AND a.renest = 'on' "
							."     AND a.renfue = '".$fuente."' "
							."     AND b.rdefue = a.renfue "
							."     AND b.rdenum = a.rennum "
							."     AND b.rdecco = a.rencco 
							group BY  b.rdenum, b.rdefue, b.rdecco";// echo "<br><br>........".$q;
						}
				
				}
			
				$err = mysql_query($q,$conex);
				$num1 = mysql_num_rows($err);

				if ($num1>0)
				{

					echo "<table align=center>";
					//Encabezado tabla
					echo "<tr class='encabezadoTabla'><td colspan=9>Empresa: ".$empCod[$i]."-".$empNom[$i]."</td></tr>";
					
					echo "<tr class='encabezadoTabla' align='center'>";
					echo "<td width=150>Fecha</td>";
					echo "<td width=200>Fuente documento</td>";
					echo "<td>Centro costos</td>";
					echo "<td>Nro. autorizacion</td>";
					echo "<td>Nro. dcto</td>";
					if (!isset ($can)){
						echo "<td>Vlr total dcto</td>";
					} else {
						echo "<td>Vlr a cancelar dcto</td>";
					}
					
					if ($vol=='SI'){
						echo "<td>Fuente factura</td>";
						echo "<td>Nro. factura</td>";
						echo "<td>Usuario que factura</td></tr>";
					} else {
						echo "</tr>";
					}

					$row = mysql_fetch_array($err);
					$wtotemp = 0;

					$clase = "fila2";
					for ($j=0;$j<$num1;$j++)
					{
						if ($clase=="fila1"){
							$clase="fila2";
						} else {
							$clase="fila1";
						}

						if ($vol!='SI')
						{
							$q = " SELECT  b.rdecon "
							."    FROM  ".$wbasedato."_000021 b "
							."   	WHERE  b.rdefue = '".$row[0]."' "
							."     AND b.rdenum = '".$fuente."' "
							."     AND b.rdecco = '".$row[4]."' ";

							$errcon = mysql_query($q,$conex);
							$con = mysql_fetch_array($errcon);
							$row[8]=$con[0];
						}

						if ($row[6]=='0' and $row[8]=='' and $row[5]=='0') {
							if (!isset($can)){
								if ($vol=='SI'){
									$q="select fdevco from ".$wbasedato."_000065 where fdefue='".$fuente."' and fdeest='on' and fdedoc='".$row[0]."' ";
								} else {
									$q="select sum(fdevco) from ".$wbasedato."_000065 where fdefue='".$fuente."' and fdeest='on' and fdedoc='".$row[0]."' ";
								}

								$err2 = mysql_query($q,$conex);
								$y = mysql_num_rows($err2);
								$row2 = mysql_fetch_array($err2);

								$valorReg=$row2[0];
							} else {
								$valorReg=0;
								$y=1;
							}

						} else {
							if ($wcarrec) {
								if ($vol=='SI' and $row[8]!='') {
									//debo consultar el multiplicador del concepto para ponerle signo
									$q="select conmul from ".$wbasedato."_000044 where concod=(mid('".$row[8]."',1,instr('".$row[8]."','-')-1)) and confue='".$fuente."'  ";
									$mulres = mysql_query($q,$conex);
									$mul = mysql_fetch_row($mulres);
									$row[6]=$row[6]*(-1*$mul[0]);
								}

								if ($vol!='SI'){
									//debo consultar la suma del valor de los conceptos que suman en vez de restar al valor total del recibo
									//entonces se consultan y se restan dos veces
									$q = " SELECT  sum(b.rdevco) "
									."    FROM  ".$wbasedato."_000021 b, ".$wbasedato."_000044 c "
									."   	WHERE b.rdefue = '".$fuente."' "
									."     AND b.rdenum = '".$row[0]."' "
									."     AND b.rdecco = '".$row[4]."' "
									."     AND c.concod = (mid(rdecon,1,instr(rdecon,'-')-1)) "
									."     AND c.conmul = 1 "
									."     AND c.confue = rdefue "
									."     AND c.conest = 'on' ";
									$mulres = mysql_query($q,$conex);
									$mul = mysql_fetch_row($mulres);
									$row[6]=$row[6]-$mul[0]*2;
								}
							}

							if (!isset ($can)) {
								$valorReg=$row[5]+$row[6];
							} else {
								$valorReg=$row[5];
							}
							$y=1;
						}

						$ccostos = "";
						$autorizacion = "";
						for ($k=1;$k<=$y;$k++) {
							echo "<td class='$clase' align='center'>".$row[3]."</td>";
							echo "<td class='$clase' align='center'>".$wfue."</td>";
							
							if ($vol=='SI'){
								$ccostos = $row[9];
							} else {
								$ccostos = $row[4];
							}
							echo "<td class='$clase' align='center'>$ccostos</td>";
							
							//Autorizacion
							$qAut = "SELECT rfpaut FROM ".$wbasedato."_000022 WHERE Rfpfue = '$fuente' AND Rfpnum = '$row[0]' AND Rfpcco = '$ccostos' AND rfpaut != 'NO APLICA'";
							$errAut = mysql_query($qAut,$conex);
							$numAut = mysql_num_rows($errAut);
							
							if($resAut = mysql_fetch_row($errAut)){
								$autorizacion = $resAut['0'];
							} else {
								$autorizacion = "";
							}							
							echo "<td class='$clase' align='center'>$autorizacion</td>";								
							

							echo "<td class='$clase' align='center'>".$row[0]."</td>";
							echo "<td class='$clase' align='right'>".number_format($valorReg,0,'.',',')."</td>";

							if ($vol=='SI')
							{
								$usuario_que_factura = usuario_que_factura($row['rdefac'], $row['rdeffa']);
								echo "<td class='$clase' align='center'>".$row[7]."</td>";
								echo "<td class='$clase' align='center'>".$row[4]."</td>";
								echo "<td class='$clase' align='center'>".$usuario_que_factura."</td></tr>";
							} else {
								echo "</tr>";
							}

							$wtotemp = $wtotemp + $valorReg;
							$wtotal=$wtotal + $valorReg;

							if ($y>1)
							{
								$row2 = mysql_fetch_array($err2);
								$valorReg=$row2[0];
							}
						}

						$row = mysql_fetch_array($err);

					}

					echo "<td class=encabezadoTabla colspan=5>TOTAL EMPRESA</td>";
					echo "<td class=encabezadoTabla align='right'>".number_format($wtotemp,0,'.',',')."</font></td>";
					if($vol=='SI')
					{
						echo "<td class=encabezadoTabla>&nbsp;</td>";
						echo "<td class=encabezadoTabla>&nbsp;</td>";
						echo "<td class=encabezadoTabla>&nbsp;</td></tr>";
					}
					else
					{
						echo "</tr>";
					}
				}else
				{
					$senal =$senal+1;

				}
			}

			if ($senal==$num)
			{
				echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningun documento en el rango de fechas seleccionado</td><tr>";

			}

			else if ($num>1)
			{
				echo "<tr class=encabezadoTabla><td colspan=5>TOTAL</td>";
				echo "<td align='right'>".number_format($wtotal,0,'.',',')."</td>";
				if($vol=='SI'){
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td></tr>";
				} else {
					echo "</tr>";
				}
			}
			echo "</table>";
			echo "</br><center><A href='RepRecibosNotas.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wfue=".$wfue."&amp;wemp=".$wemp."&amp;bandera='1'>VOLVER</A></center>";
			echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";

			echo "</form>";
			break;
		default: 
			//Cuerpo de la pagina
			echo "<table align='center' border=0>";

			//Subtitulo de parámetros de consulta
			echo '<span class="subtituloPagina2">';
			echo 'Par&aacute;metros de consulta';
			echo "</span>";
			echo "<br>";
			echo "<br>";

			//Fecha inicial
			echo "<tr><td class='fila1'>Fecha inicial de facturaci&oacute;n</td>";
			echo "<td class='fila2' align='center'>";
			if(isset($wfecini) && !empty($wfecini)){
				campoFechaDefecto("wfecini",$wfecini);
			} else {
				campoFecha("wfecini");
			}
			echo "</td></tr>";

			//Fecha final del comprobante
			echo "<tr><td class='fila1'>Fecha final de facturaci&oacute;n</td>";
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
			
			echo "<select name='wfue' class=seleccionNormal>";
			
			$q= "SELECT carfue, cardes FROM ".$wbasedato."_000040 WHERE carrec='on' or carncr='on' or carndb='on'";

			$res = mysql_query($q,$conex);
			$num = mysql_num_rows($res);

			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($res);
				echo "<option value='$row[0]-$row[1]'>$row[0] - $row[1]</option>";
			}
			echo "</select>";
			echo "</td></tr>";
			
			//Empresa
			echo "<tr><td class='fila1'>Empresa</td>";
			echo "<td class='fila2' align='center'>";
			
			echo "<select name='wemp' class=seleccionNormal style='width:650'>";

			$q = "SELECT 
						empcod, empnom, empnit 
				  FROM 
				  		{$wbasedato}_000024 
				  WHERE 
				  		empcod = empres ORDER BY 2
				  ";

			$res = mysql_query($q,$conex);
			$num = mysql_num_rows($res);

			echo "<option value='% - Todas las empresas'>Todas las empresas</option>";

			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($res);
				$print=explode ('-',$wemp);
				$prin=$print[0].'-'.$print[1];
				if ($prin!=$row[0]."-".$row[1])
				echo "<option value='$row[0]-$row[2]-$row[1]'>".$row[0]."-".$row[2]."-".$row[1]."- <b>[&nbsp;&nbsp;&nbsp;&nbsp;".$row[2]."&nbsp;&nbsp;&nbsp;&nbsp;]</b></option>";

			}
			
			$q = " SELECT SUBSTRING_INDEX( emptem , '-', 1 ) as empcod, SUBSTRING_INDEX( emptem , '-', -1 ) as empnom, SUBSTRING_INDEX( emptem , '-', 1 ) as empnit "
            . "   FROM {$wbasedato}_000024 "
            . "  WHERE empcod != empres "
            . "  GROUP BY emptem "
            . "  ORDER BY empnom ";

			$res = mysql_query($q,$conex);
			$num = mysql_num_rows($res);


			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($res);
				$print=explode ('-',$wemp);
				$prin=$print[0].'-'.$print[1];
				if ($prin!=$row[0]."-".$row[1])
				echo "<option value='EMP-$row[2]-$row[1]'>EMP-".$row[2]."-".$row[1]."- <b>[&nbsp;&nbsp;&nbsp;&nbsp;".$row[2]."&nbsp;&nbsp;&nbsp;&nbsp;]</b></option>";
			}
			
			echo "</select></td></tr>";

			echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

			//Mostrar valor a cancelar
			echo "<tr><td class='fila2' colspan=2 align='center'>";
			echo "<input type='checkbox' name='can'>Mostrar valor a cancelar&nbsp;|&nbsp;";
			echo "<input type='radio' name='vol' value='SI' checked>Detallado&nbsp;|&nbsp;";
			echo "<input type='radio' name='vol' value='NO'>Resumido&nbsp;&nbsp;";
			echo "</td></tr>";
			
			echo "<tr><td class='fila1' colspan=2 align='center'>";
			echo "<input type='button' name='comprobante' value='Consultar' onClick='javascript:consultar();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'>";
			echo "</b></td></tr></table></br>";
		break;
	}
}
liberarConexionBD($conex);
?>
</body>
</html>
