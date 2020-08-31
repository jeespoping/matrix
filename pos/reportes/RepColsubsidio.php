<html>
<head>
<title>REPORTE COLSUBSIDIO</title>

<style type="text/css">
BODY           
{   
    font-family: Verdana;
    font-size: 10pt;
    margin: 0px;
}
</style>

<!-- Funciones Javascript -->
<SCRIPT LANGUAGE="javascript">
	function enviar(){  
		document.forma.submit();		
	}
</SCRIPT>

</head>

<?php
include_once("conex.php");
/*
 * REPORTE Y GENERACION DE ARCHIVO PLANO COLSUBSIDIO
 */
//BS'D=================================================================================================================================
//PROGRAMA: RepColsubsidio.php
//AUTOR: Mauricio Sánchez Castaño.
//TIPO DE SCRIPT: reporte
//RUTA DEL SCRIPT: matrix\IPS\Reportes\RepColsubsidio.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
//+-------------------+------------------------+-----------------------------------------+
//|	   FECHA          |     AUTOR              |   MODIFICACION							 |
//+-------------------+------------------------+-----------------------------------------+
//|  2009-01-13       | Mauricio Sánchez       | creación del script.					 |
//+-------------------+------------------------+-----------------------------------------+
	
//FECHA ULTIMA ACTUALIZACION 	: 2009-01-13

//=================================================================================================================================*/
include_once("root/comun.php");

//Registro de control EN ESTE ORDEN
class control{
	var $tipoRegistro;
	var $numeroRegistrosEnviados;
	var $numeroFacturasEnviadas;	
	var $fechaEnvio;
	var $codigoSucursal;
	var $tipoDocumento;
	var $nitIps;
	var $digitoVerificacion;
}

//Registro de cabecera
class cabecera {
	var $tipoRegistro;
	var $plan;
	var $fechaFactura;
	var $letrasFactura;
	var $numerosFactura;
	var $valorTotalFactura;
	var $periodosDeCarencia;
	var $cuotaModeradora;
	var $copagoTiquete;
	var $tiquetes;
	var $descuento;
	var $conceptoRetencionFuente;
}

//Registro de detalle
class detalle{
	var $tipoRegistro;
	var $numeroRecetario;
	var $tipoIdentificacionAfiliado;
	var $numeroIdentificacionAfiliado;
	var $codigoDelMedicamento;
	var $cantidadEntregada;
	var	$ordenDeServicio;
	var $registroMedico;
	var $codigoDiagnostico;
	var $codigoFarmacia;
	var $costoUnitarioMedicamento;
	var $pluMedicamento;
	var $cuotaModeradora;
	var $fechaDespacho;
	var $valorCobrarRecetario;
	var $clasificacionIngresos;
}

class empresaGenerica{
	var $tipoIdentificacion;
	var $numeroIdentificacion;
	var $digitoVerificacion;
	var $nombre;
}

//Preparacion 
function prepararCampo($valor, $longitudMaxima, $tipo){
	$preparada = "";
	$longitudValor = strlen($valor);
	
	switch ($tipo){
		case 'NUMERICO': //Si es numerico se rellenan ceros a la izquierda
			//Ajuste a la longitud maxima posible de la cadena
			if($longitudValor > $longitudMaxima){
				$valor = substr($valor,0,$longitudMaxima);
				$longitudValor = $longitudMaxima;
			}
				
			$cont1 = 1;
			while($cont1 <= ($longitudMaxima - $longitudValor)){
				$preparada .= "0";
				$cont1++;
			}
			$preparada .= $valor;
			break;			
		case 'CARACTER':  //Justificados a la izquierda
//			Ajuste a la longitud maxima posible de la cadena
			if($longitudValor > $longitudMaxima){
				$valor = substr($valor,0,$longitudMaxima);
				$longitudValor = $longitudMaxima;
			}
				
			$cont1 = 1;
			$preparada = $valor;
			$longitudValor = strlen($preparada);
			
			while($cont1 <= ($longitudMaxima - $longitudValor)){
				$preparada .= " "; //OJO REEMPLAZAR POR ESPACIO
				$cont1++;
			}		
			break;
		case 'FECHA':  //Formato requerido DD/MM/AAAA
			$vecFecha = explode("-",$valor);
			
			$preparada = $vecFecha[2]."/".$vecFecha[1]."/".$vecFecha[0];
			
			break;
	}
//	return $preparada."<br>";  //OJO CON ESTO COMPAE
	return $preparada;		
}

function concatenarCampos($registro,$tipoRegistro){
	$preparada = "";
			
	switch ($tipoRegistro) 
	{
		case '0':  //Control
			$preparada = $registro->tipoRegistro.$registro->numeroRegistrosEnviados.$registro->numeroFacturasEnviadas.$registro->fechaEnvio.$registro->codigoSucursal.$registro->tipoDocumento.$registro->nitIps.$registro->digitoVerificacion;
		break;
		case '1':  //Cabecera
			$preparada = $registro->tipoRegistro.$registro->plan.$registro->fechaFactura.$registro->letrasFactura.$registro->numerosFactura.$registro->valorTotalFactura.$registro->periodosDeCarencia.$registro->cuotaModeradora.$registro->copagoTiquete.$registro->tiquetes.$registro->descuento.$registro->conceptoRetencionFuente;
		break;
		case '2':  //Detalle
			$preparada = $registro->tipoRegistro.$registro->numeroRecetario.$registro->tipoIdentificacionAfiliado.$registro->numeroIdentificacionAfiliado.$registro->codigoDelMedicamento.$registro->cantidadEntregada.$registro->ordenDeServicio.$registro->registroMedico.$registro->codigoDiagnostico.$registro->codigoFarmacia.$registro->costoUnitarioMedicamento.$registro->pluMedicamento.$registro->cuotaModeradora.$registro->fechaDespacho.$registro->valorCobrarRecetario.$registro->clasificacionIngresos;
		break;
	}
	
	return $preparada;
}

function consultarDatosEmpresaLocal($conex){
	$q = "SELECT 
			SUBSTRING_INDEX(Cfgtdo, '-', 1 ) Cfgtdo, Cfgnit, Cfgnom
		FROM 
			farstore_000049	
		LIMIT 1";
	
	$coleccion = array();
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if($num > 0)
	{
		$info = mysql_fetch_array($res);

		$empresa = new empresaGenerica();
		
		$vecIdentificacion = explode("-",$info['Cfgnit']);
		
		$empresa->tipoIdentificacion 		= $info['Cfgtdo'];
		$empresa->numeroIdentificacion 		= $vecIdentificacion[0];
		$empresa->digitoVerificacion		= $vecIdentificacion[1];
		$empresa->nombre 					= $info['Cfgnom'];
	}
	return $empresa;
}

function extraerLetras($valor){
	$cont1 = 0;
	$retorno = "";
	while($cont1 < strlen($valor)){
		if(!is_numeric($valor[$cont1]) && $valor[$cont1] != "-"){
			$retorno .= $valor[$cont1];	
		}
		$cont1++;
	}
	return $retorno;
}

function extraerNumeros($valor){
	$cont1 = 0;
	$retorno = "";
	while($cont1 < strlen($valor)){
		if(is_numeric($valor[$cont1])){
			$retorno .= $valor[$cont1];
		}	
		$cont1++;
	}
	return $retorno;
}

//Constantes
$tipo_registro_control = "0";
$tipo_registro_cabecera = "1";
$tipo_registro_detalle = "2";

$sucursal_ips_medellin = "1";
$sucursal_ips_cali = "2";
$sucursal_ips_bogota = "3";
$sucursal_ips_barranquilla = "4";

$tipo_plan_pos = "0";
$tipo_plan_prepagada = "1";
$tipo_plan_pac = "2";

$concepto_retencion_fuente = "3";

$clasificacion_ingresos_a = "0";
$clasificacion_ingresos_b = "1";
$clasificacion_ingresos_c = "2";

//Inicio
if(!isset($_SESSION['user'])){
	terminarEjecucion($MSJ_);
}else{
	if(!isset($empresa)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."empresa");		
	}

	if(!isset($user)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."codigo de usuario");		
	}
	
	$conex = obtenerConexionBD("matrix");
	$wbasedato = $empresa;
	
  	$wfecha=date("Y-m-d");
  	$hora = (string)date("H:i:s");
  	$wnomprog="RepColsubsidio.php";  //nombre del reporte
  	
  	$wcf1 = "#41627e";		//Fondo encabezado del Centro de costos
  	$wcf  = "#c2dfff";   	//Fondo procedimientos
  	$wcf2 = "003366";  		//Fondo titulo pantalla de ingreso de parametros
  	$wcf3 = "#659ec6";  	//Fondo encabezado del detalle
  	$wclfg= "003366"; 		//Color letra parametros
  	
  	//Generando salida a archivo
	$nombreArchivo = "./../../planos/pos/".$wfecha."-".(string)date("His")."medio_magnetico.txt";
//	$nombreArchivo = "colsubsidio_medio_magnetico.txt";
  	@unlink($nombreArchivo);
  	
  	echo "<form action='RepColsubsidio.php' method=post name='forma'>";
  	echo "<input type='HIDDEN' NAME= 'empresa' value='".$empresa."'>";

  	//CODIGO ASIGNADO POR COLSUBSIDIO A FARMASTORE
  	$codigoConsulta = "3052CL";
  	
//  if (!isset($wfechainicial) or !isset($wfechafinal) or !isset($codigoConsulta) or !isset($fuente) or !isset($factura))
	if (!isset($resultado))
  	{
  		echo "<center><table border=0>";
  		echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/ips/logo_".$wbasedato.".png' WIDTH=350 HEIGHT=100></td></tr>";
  		echo "<tr><td align=center bgcolor=".$wcf2."><font size=5 text color=#FFFFFF><b>REPORTE COLSUBSIDIO</b></font></td></tr>";

  		//Fuente y factura
  		echo "<tr>";
  		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fuente </font></b>";
  		echo "<INPUT TYPE='text' NAME='wfuente' SIZE=10></td>";
  		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Factura </font></b>";
  		echo "<INPUT TYPE='text' NAME='wfactura' SIZE=10></td>";
  		echo "</tr>";
  		
		//Fecha inicial de consulta	
  		echo "<tr>";
  	 	echo "<td colspan=2 bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha envio </font></b>";
  	 	campoFecha("wfechainicial");
  		echo "</td>";

//  	echo "<input type='hidden' name='codigoConsulta' value='*'>";
  		echo "<input type='hidden' name='resultado' value='*'>";
  		
  		echo "<tr align='center'><td colspan=2 bgcolor=".$wcf.">";
  		echo "<input type='submit' value='Consultar'>";
  		echo "</td></tr>";
    	
  		echo "</table>";
    	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
    	
  	} else {
  		echo "<table border=0 width=100%>";
  		echo "<td><B>Fecha actual:</B> ".date('Y-m-d')."&nbsp;&nbsp;<B>Hora :</B> ".$hora."&nbsp;</td></tr>";
  		echo "<tr><td align=left><B>Programa:</B> ".$wnomprog."</td>";
  		echo "</table>";
  		echo "<table border=0 align=center >";
  		
  		echo "<tr><td><B>REPORTE COLSUBSIDIO</B></td></tr>";
  		
  		echo "</table></br>";

  		echo "<A href='RepColsubsidio.php?empresa=".$empresa."'><center>VOLVER</center></A><br>";
  		echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
  		
  		echo "<input type='HIDDEN' NAME= 'wfechainicial' value='".$wfechainicial."'>";
  		echo "<input type='HIDDEN' NAME= 'wfuente' value='".$wfuente."'>";
  		echo "<input type='HIDDEN' NAME= 'wfactura' value='".$wfactura."'>";

  		//Fuente  		
  		if(empty($wfuente)){
  			$wfuente = '%';
  		}

  		//Factura
  		if($wfactura == ''){
  			$wfactura = '%';
  		}
  		
		$q = "SELECT * FROM (
					SELECT 
						Venfec,
						Venffa,
						Vennfa,
						Vennum,					
						IFNULL((SELECT
							Ripaut
						FROM
							farstore_000063
						WHERE
							Ripvta = Vennum),'') Ripaut,						
						IFNULL((SELECT
							Riptid
						FROM
							farstore_000063
						WHERE
							Ripvta = Vennum),'') Riptid,
						IFNULL((SELECT 
							medreg 
						FROM 
							farstore_000050, farstore_000051 
						WHERE 
							Vmpvta = Vennum 
							AND SUBSTRING_INDEX(Vmpmed, '-', 1 ) = Medcod
						),'') Medreg,					
						IFNULL((SELECT 
							Vmpdia
						FROM 
							farstore_000050, farstore_000051 
						WHERE 
							Vmpvta = Vennum 
							AND SUBSTRING_INDEX(Vmpmed, '-', 1 ) = Medcod
						),'') Vmpdia,
						IFNULL((SELECT 
							Vmpran
						FROM 
							farstore_000050, farstore_000051 
						WHERE 
							Vmpvta = Vennum 
							AND SUBSTRING_INDEX(Vmpmed, '-', 1 ) = Medcod
						),'') Vmpran,
						Vennit,
						Artnom,
						Vdecan,
						Vdepiv,	
						(Vdecan*Vdevun)*(Vdepiv/100) Valor_iva,  				
						Vencmo,
						Artcod,
						Vdevun,
						IFNULL((SELECT Aeman1 FROM farstore_000089 WHERE Aemcem = '$codigoConsulta' AND Aemart = Artcod AND Aemest = 'on'),'') Colsubsidio,
						Venvto,
						Fenfec,
						Fenfac,
						Fenval,
						IFNULL((SELECT Aeman2 FROM farstore_000089 WHERE Aemcem = '$codigoConsulta' AND Aemart = Artcod AND Aemest = 'on'),'') Susalud				
					FROM
						farstore_000016,
						farstore_000017,
						farstore_000018,
						farstore_000001
					WHERE 
						Venffa = '$wfuente'
						AND Vennfa = '$wfactura'
						AND Fenffa = Venffa
						AND Fenfac = Vennfa
						AND Vennum = Vdenum
						AND Vdeart = Artcod
					ORDER BY 1
				) A";
		
//		echo $q;	
	
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		
		//Variables acumuladoras
		$acumIva = 0;
		$acumValorUnitario = 0;
		$acumCuotaModeradora = 0;
		$acumCobro = 0;
		
		if($num > 0){
			//Variables de control
			$cdVenta = "";
			$cdGru = "";
			$auxNombreCco = "";
			$auxNombreGru = "";
			$resultado = "";
			
			$cont1 = 0;
			
			echo "<table border=0 align=center>";
			
			//Encabezados de columna
			echo "<tr>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>FECHA</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>FACTURA</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>NRO VENTA</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>NRO FORMULA</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>REGISTRO MEDICO</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>DOCUM_ID PACIENTE</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>CODIGO COLSUBSIDIO</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>IDENT_DETALLE</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>CANT</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>VR IVA</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>VR UNIT</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>CUOTA MOD</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>VR COBRO</b></font></td>";
			echo "</tr>";
			
			//Consulta de informacion de farmastore
			$datosEmpresaLocal = consultarDatosEmpresaLocal($conex);
			
			//Preparación de registro de control.  44 Caracteres en total
			$reg1 = new control();
			
			$reg1->tipoRegistro 					= 	prepararCampo($tipo_registro_control,1,'NUMERICO');
			$reg1->numeroRegistrosEnviados 			= 	prepararCampo($num+1,6,'NUMERICO');										//$num + 1 
			$reg1->numeroFacturasEnviadas 			= 	prepararCampo($num,6,'NUMERICO');										//$num NO POR QUE DETALLE > NUMERO FACTURAS			
			$reg1->fechaEnvio 						= 	prepararCampo($wfechainicial,10,'FECHA');								//Fecha de envio se pide.
			$reg1->codigoSucursal					=	prepararCampo($sucursal_ips_medellin,3,'NUMERICO');						//Siempre medellin
			$reg1->tipoDocumento					=	prepararCampo($datosEmpresaLocal->tipoIdentificacion,2,'CARACTER');  	//Tipo de documento farmastore
			$reg1->nitIps							=	prepararCampo($datosEmpresaLocal->numeroIdentificacion,15,'NUMERICO'); 	//Nit de farmastore
			$reg1->digitoVerificacion				=	prepararCampo($datosEmpresaLocal->digitoVerificacion,1,'NUMERICO');		//digito verificacion farmastore
			
			//Concatenacion de los campos del registro de control
			$resultado .= concatenarCampos($reg1,$tipo_registro_control).chr(13).chr(10);
					
			while($cont1 < $num){
				
				$cont1 % 2 == 0 ? $color = $wcf : $color = '';
				
				//El valor de la cuota moderadora y el valor del cobro total aparece una sola vez en el reporte
				if(isset($facturaActual)){
					$cdVenta = $facturaActual['Vennum'];	
				} else {
					$cdVenta = '';
				}
				
				$facturaActual = mysql_fetch_array($err);

				$campo1 = $facturaActual['Venfec'];
				$campo2 = $facturaActual['Venffa'];
				$campo3 = $facturaActual['Vennfa'];
				$campo4 = $facturaActual['Vennum'];
				$campo1 = $facturaActual['Fenfac'];// WARNING::::		
				
				if(isset($facturaActual['Ripaut'])){
					$campo5 = $facturaActual['Ripaut'];
				} else {
					$campo5 = "";
				}
				
				if(isset($facturaActual['Medreg'])){
					$campo6 = $facturaActual['Medreg'];	
				} else {
					$campo6 = "";
				}
								
				$campo7 = $facturaActual['Vennit'];
				$campo8 = $facturaActual['Artnom'];
				$campo9 = $facturaActual['Vdecan'];
//				$campo10 = $facturaActual['Vdepiv'];
				$campo10 = $facturaActual['Valor_iva'];
				$campo11 = $facturaActual['Vdevun'];
				$campo12 = $facturaActual['Vencmo'];
				$campo13 = $facturaActual['Venvto'];
				
				if(isset($facturaActual['Colsubsidio'])){
					$campo14 = $facturaActual['Colsubsidio'];
				} else {
					$campo14 = "";
				}
				
				//Acumuladores
				$acumIva += $campo10;
				$acumValorUnitario += $campo11;
				
				//Poblar celdas
				echo "<tr>";
				echo "<td align=left bgcolor=".$color."><font text size=2>".$facturaActual['Venfec']."</font></td>";
				echo "<td align=left bgcolor=".$color."><font text size=2>".$campo3."</font></td>";
				echo "<td align=left bgcolor=".$color."><font text size=2>".$campo4."</font></td>";
				echo "<td align=left bgcolor=".$color."><font text size=2>".$campo5."</font></td>";
				echo "<td align=left bgcolor=".$color."><font text size=2>".$campo6."</font></td>";
				echo "<td align=left bgcolor=".$color."><font text size=2>".$campo7."</font></td>";
				echo "<td align=left bgcolor=".$color."><font text size=2>".$campo14."</font></td>";
				echo "<td align=left bgcolor=".$color."><font text size=2>".$campo8."</font></td>";
				echo "<td align=left bgcolor=".$color."><font text size=2>".$campo9."</font></td>";
				echo "<td align=left bgcolor=".$color."><font text size=2>".$campo10."</font></td>";
				echo "<td align=left bgcolor=".$color."><font text size=2>".$campo11."</font></td>";
				
				if($cdVenta != $campo4){
					echo "<td align=left bgcolor=".$color."><font text size=2>".$campo12."</font></td>";
					echo "<td align=left bgcolor=".$color."><font text size=2>".$campo13."</font></td>";	
					
					$acumCuotaModeradora += $campo12;
					$acumCobro += $campo13;
				} else {
					echo "<td align=left bgcolor=".$color."><font text size=2>0</font></td>";
					echo "<td align=left bgcolor=".$color."><font text size=2>0</font></td>";
				}
				
				if($cont1 < 1){
					//Preparación de registro de cabecera.  91 Caracteres en total
					$reg2 = new cabecera();

					$reg2->tipoRegistro 					= 	prepararCampo($tipo_registro_cabecera,1,'NUMERICO');
					$reg2->plan 							= 	prepararCampo($tipo_plan_pos,1,'NUMERICO');	  								//Siempre POS
					$reg2->fechaFactura					 	= 	prepararCampo($facturaActual['Fenfec'],10,'FECHA');    						//Fenfec
					$reg2->letrasFactura 					= 	prepararCampo(extraerLetras($facturaActual['Fenfac']),6,'CARACTER');		//Fenfac (Caracteres) 
					$reg2->numerosFactura					=	prepararCampo(extraerNumeros($facturaActual['Fenfac']),10,'NUMERICO');		//Fenfac (numeros)
					$reg2->valorTotalFactura				=	prepararCampo($facturaActual['Fenval'],12,'NUMERICO');						//Fenval
					$reg2->periodosDeCarencia				=	prepararCampo('0',10,'NUMERICO'); 											//Siempre 10 ceros 
					$reg2->cuotaModeradora					=	prepararCampo('0',10,'NUMERICO'); 											//Siempre 10 ceros 
					$reg2->copagoTiquete					=	prepararCampo('0',10,'NUMERICO'); 											//Siempre 10 ceros 
					$reg2->tiquetes							=	prepararCampo('0',10,'NUMERICO');											//Siempre 10 ceros 
					$reg2->descuento						=	prepararCampo('0',10,'NUMERICO'); 											//Siempre 10 ceros 
					$reg2->conceptoRetencionFuente			=	prepararCampo($concepto_retencion_fuente,1,'NUMERICO');		//Siempre 3

					//Concatenacion de los campos del registro de control
					$resultado .= concatenarCampos($reg2,$tipo_registro_cabecera).chr(13).chr(10);					
				}
				
				//Preparación de registro de cabecera.  84 Caracteres en total
				$reg3 = new detalle();
				
				$reg3->tipoRegistro 					= 	prepararCampo($tipo_registro_detalle,1,'NUMERICO');			
				$reg3->numeroRecetario					= 	prepararCampo($facturaActual['Ripaut'],15,'NUMERICO');		//Ripaut
				$reg3->tipoIdentificacionAfiliado		= 	prepararCampo($facturaActual['Riptid'],2,'CARACTER');		//Riptip
				$reg3->numeroIdentificacionAfiliado 	= 	prepararCampo($facturaActual['Vennit'],12,'NUMERICO');		//Vennit
				$reg3->codigoDelMedicamento				=	prepararCampo($facturaActual['Susalud'],6,'NUMERICO');		//Aeman2
				$reg3->cantidadEntregada				=	prepararCampo($facturaActual['Vdecan'],5,'NUMERICO');		//Vdecan
				$reg3->ordenDeServicio					=	prepararCampo('0',12,'NUMERICO');							//12 ceros
				$reg3->registroMedico					=	prepararCampo($facturaActual['Medreg'],12,'NUMERICO');		//Medreg
				$reg3->codigoDiagnostico				=	prepararCampo($facturaActual['Vmpdia'],7,'CARACTER');		//Vmpdia
				$reg3->codigoFarmacia					=	prepararCampo($codigoConsulta,5,'NUMERICO');				//TODO: Codigo suministrado por ellos a farmastore?
				$reg3->costoUnitarioMedicamento			=	prepararCampo($facturaActual['Vdevun'],12,'NUMERICO');		//Vdevun
				$reg3->pluMedicamento					=	prepararCampo($facturaActual['Colsubsidio'],12,'NUMERICO');		//Aeman1
				
				if($cdVenta != $campo4){
					$reg3->cuotaModeradora				=	prepararCampo($facturaActual['Vencmo'],12,'NUMERICO');		//Vencmo
					$reg3->valorCobrarRecetario			=	prepararCampo($facturaActual['Venvto'],12,'NUMERICO');		//Venvto	
				} else {
					$reg3->cuotaModeradora				=	prepararCampo('0',12,'NUMERICO');
					$reg3->valorCobrarRecetario			=	prepararCampo('0',12,'NUMERICO');
				}
				$reg3->fechaDespacho					=	prepararCampo($facturaActual['Venfec'],10,'FECHA');			//Venfec				
				$reg3->clasificacionIngresos			=	prepararCampo($facturaActual['Vmpran'],1,'NUMERICO');		//Vmpran
							
				//Concatenacion de los campos del registro de control
				$resultado .= concatenarCampos($reg3,$tipo_registro_detalle).chr(13).chr(10);
				
				//Prueba de salida RESULTADO.
//				echo $resultado;
				
				$cont1++;
			}
			
			$archivo = fopen($nombreArchivo, 'w');

			$exito = true;
			if(file_exists($nombreArchivo)){				
				fwrite($archivo, $resultado);				
			} else {
				$exito = false;	
			}
			fclose($archivo);
			
			if($exito){
				echo "<h4><a href='./../../planos/pos/$nombreArchivo' target='_blank'>Ver archivo plano</a></h4>";	
			}else {
				echo "El archivo no pudo ser generado.";
			}
			
			echo "<tr>";
			echo "<td align=center bgcolor=".$wcf1." colspan=9><font text color=#efffff size=2><b>&nbsp;</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>".$acumIva."</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>".$acumValorUnitario."</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>".$acumCuotaModeradora."</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff size=2><b>".$acumCobro."</b></font></td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<A href='RepColsubsidio.php?empresa=".$empresa."'><center>VOLVER</center></A><br>";
			echo "<center><input type=button value='Cerrar ventana' onclick='javascript:window.close();'><center>";
		} else {
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>No se encontraron documentos con los criterios especificados</td><tr>";
		}		
  	}
}
liberarConexionBD($conex);
?>
</html>