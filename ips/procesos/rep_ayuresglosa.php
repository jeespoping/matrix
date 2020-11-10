<html>
<head>
<title>MATRIX - [INFORME PARA ANALIZAR GLOSAS]</title>


<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_ayuresglosa.php'; 
	}
	
	function enter()
	{
		document.forms.rep_ayuresglosa.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             INFORME PARA ANALIZAR GLOSAS                                              *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver el detalle de una factura en unix.                                                         |
//AUTOR				          :Ing. Gabriel Alonso Agudelo Zapata.                                                                       |
//FECHA CREACION			  :Sept 17 de 2020.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  :Sept 17 de 2020.                                                                                      |
//TABLAS UTILIZADAS   :                                                                                                                    | 
//                                                                                                                                         |
//facarfac  en unix   : Tabla de Facturas x cargo.                                                                                         |
//facardet  en unix   : Tabla de Cargos.                                                                                                   |
//ivdrodet  en unix   : Tabla de detalle de cargos de Medicamentos.                                                                        |
//ivart     en unix   : Tabla de Maestro de Articulos.                                                                                     |
//ivuni     en unix   : Tabla de unidad de medida.                                                                                         |
//                                                                                                                                         |
//==========================================================================================================================================
include_once("root/comun.php");
$conex     = obtenerConexionBD("matrix");
$conex_o   = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
$wactualiz = "1.0 22-Sept-2020";

$empresa='root';

$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();
//Encabezado
encabezado("INFORME PARA ANALIZAR GLOSAS",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{

 //Forma
 echo "<form name='forma' action='rep_ayuresglosa.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($codigo) or $codigo=='' or !isset($fac) or $fac == '' )
  {
  	
  	echo "<form name='rep_ayuresglosa' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

 	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";
 	 	
   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////Codigo CUMS/CUPS
  
   echo "<Tr>";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Codigo CUMS/CUPS<i><br></font><bgcolor='#dddddd' aling=center><input type='input' size=9 maxlength=10 name='codigo'></td>";
   echo "</Tr>";
   
   if (isset($codigo))
   {
    $codigo=$codigo;
   }
   else 
   {
    $codigo='';	
   }
     
   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////Nro Factura
  
   echo "<Tr>";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>FACTURA NUMERO<i><br></font><bgcolor='#dddddd' aling=center><input type='input' name='fac' size=9 maxlength=10 id='fac'></td>";
   echo "</Tr>";
   
   if (isset($fac))
   {
    $fac=$fac;
   }
   else 
   {
    $fac='';	
   }
      
   echo "<tr><td align=center bgcolor=#cccccc colspan=3><input type='submit' value='Generar'></td>"; //submit osea el boton de Generar o Aceptar
   echo "</tr>";
     
  }
 else // Cuando ya estan todos los datos escogidos
  {
	
  		$query_o1="SELECT cardetcod,cardetcon,connom,SUM(carfacval)
					FROM facarfac, facardet,outer IVDRODET,outer facon,cacar 
					WHERE carfacfue = '20'
					AND carfacdoc = '".$fac."'
					AND carfacanu = '0'
					AND carfacfue = carfue
					AND carfacdoc = cardoc
					AND carfacreg = cardetreg
					AND drodetfue = cardetfue
					AND drodetdoc = cardetdoc
					AND drodetite = cardetite
					AND cardetcon = concod 
					GROUP BY 1,2,3 
					ORDER BY cardetcod,cardetcon";
		
		$err_o = odbc_do($conex_o,$query_o1);
				
		echo "<table border=0 cellspacing=3 cellpadding=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
		echo "<tr>";
		echo "<td align=center colspan='1' bgcolor=#DDDDDD><font size='4' text color=#003366><b>CODIGO&nbsp;&nbsp;</b></font></td>";
		echo "<td align=center colspan='1' bgcolor=#DDDDDD><font size='4' text color=#003366><b>FACTURA</b></font></td>";
		echo "</tr>";
		echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>".$codigo."&nbsp;&nbsp;</b></font></td>";
		echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>".$fac."</b></font></td>";
		echo "</table>";
		
		echo "<br>";
		
		echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
		echo "<tr>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>CODIGO&nbsp;&nbsp;</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>CONCEPTO&nbsp;&nbsp;</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>NOMBRE&nbsp;&nbsp;</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>VALOR&nbsp;&nbsp;</b></font></td>";
		echo "</tr>";
		$Num_Filas = 0;
		$totconval=0;
		while (odbc_fetch_row($err_o))
			  {
				$Num_Filas++;
				$cod = odbc_result($err_o,1);//codigo
				$concepto = odbc_result($err_o,2);//concepto
				$connom = odbc_result($err_o,3);//nombre del concepto
				$conval = odbc_result($err_o,4);//valor concepto
				//$conrel = odbc_result($err_o,4);//concepto relacionado
				//$tabla = odbc_result($err_o,5);//tabla a consultar
				echo "<tr>";
				echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cod."&nbsp;&nbsp;</b></font></td>";
				echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$concepto."&nbsp;&nbsp;</b></font></td>";
				echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$connom."&nbsp;&nbsp;</b></font></td>";
				echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$conval."&nbsp;&nbsp;</b></font></td>";
				echo "</tr>";
				$totconval=$totconval + $conval;
			  }
			  echo "<tr>";
			  echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD></td>";
			  echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>VALOR TOTAL FACTURA</b></font></td>";
			  echo "<td align=CENTER bgcolor=#DDDDDD ><font size='2' text color='#003366'><b>".$totconval."</b></font></td>";
			  echo "</tr>";
		echo "</table>";
	
	
		// --> Consultar la facturas agrupadas
			$query_o2="
			SELECT cardetcod,cardetcon,connom,conarc,cardettar,SUM(cardetcan),
					SUM(carfacval),cardetfue,cardetcco,cardetvun,cardetnit,
					drodetart,SUM(drodetcan),carced,carres,egring,egregr 
			 FROM facarfac, facardet,outer IVDRODET,outer facon,cacar,outer inmegr
			 WHERE carfacfue = '20'
			   AND carfacdoc = '".$fac."'
			   AND carfacanu = '0'
			   AND carfacfue = carfue
			   AND carfacdoc = cardoc
			   AND carfacreg = cardetreg
			   AND carhis = egrhis
			   AND carnum = egrnum 
			   AND drodetfue = cardetfue
			   AND drodetdoc = cardetdoc
			   AND drodetite = cardetite
			   AND cardetcon = concod 
			 GROUP BY 1,2,3,4,5,8,9,10,11,12,14,15,16,17
			 ORDER BY cardetcod,cardetcon,conarc ";		   
	
		$err_2 = odbc_do($conex_o,$query_o2);
				
		echo "<br>";
		
		echo "<table border=0 cellspacing=3 cellpadding=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
		echo "<tr>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>CODIGO</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>CONCEPTO</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>NOMBRE</b></font></td>";
		//echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>TABLA</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>TARIFA</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>CANTIDAD</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>VALOR UNITARIO</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>VALOR TOTAL</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>FUENTE</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>CCOSTOS</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>TERCERO</b></font></td>";
		//echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>CANTIDAD</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>NIT</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>RESPONSABLE</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>FECHA INGRESO</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>FECHA EGRESO</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>ARTICULO</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>DESCRIPCION</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>FECHA INICIO CONVENIO</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>VALOR ACTUAL</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>VALOR ANTERIOR</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>FACTURABLE S=SI N=NO P=EXCEDENTE</b></font></td>";
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>ARTICULO REGULADO</b></font></td>";	
		echo "<td align=CENTER colspan='1' bgcolor=#DDDDDD><font size='2' text color=#003366><b>COBRO(C/G/U)</b></font></td>";	
		echo "</tr>";
		$Num_Filas1 = 0;
		$bandera=0;
		$codigo = strtoupper($codigo);
		while (odbc_fetch_row($err_2))
			  {
				$Num_Filas1++;
				$codigo1 = odbc_result($err_2,1);//coodigo
				$conc = odbc_result($err_2,2);//concepto
				$concnom = odbc_result($err_2,3);//nombre del concepto
				$tab = odbc_result($err_2,4);//tabla a consultar
				$tar = odbc_result($err_2,5);//tarifa
				$cant = odbc_result($err_2,6);//cantidad
				$valor = odbc_result($err_2,7);//valor
				$fuente = odbc_result($err_2,8);//fuente
				$ccostos = odbc_result($err_2,9);//ccostos
				$vruni = odbc_result($err_2,10);//valor unitario
				$tercero = odbc_result($err_2,11);//tercero
				$articulo = odbc_result($err_2,12);//articulo
				$cant1 = odbc_result($err_2,13);//cantidad
				$nit = odbc_result($err_2,14);//nit
				$resp = odbc_result($err_2,15);//responsable
				$fing = odbc_result($err_2,16);//fecha ingreso
				$fegr = odbc_result($err_2,17);//fecha egreso
				
				if (trim($codigo) == trim($codigo1) or trim($codigo) == trim($articulo) ) 
				{
					echo "<tr>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$codigo1."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$conc."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$concnom."</b></font></td>";
					//echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$tab."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$tar."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cant."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$vruni."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$valor."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$fuente."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ccostos."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$tercero."</b></font></td>";
					//echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cant1."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$nit."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$resp."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$fing."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$fegr."</b></font></td>";
					echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$articulo."</b></font></td>";
					If ($tab == 'IVARTTAR')
					{
						$query_o3="SELECT arttarvaa,arttarfec,arttarval,artnom 
									 FROM ivarttar,ivart 
									 WHERE arttarcod = '".$codigo."' 
									  and  arttartar = '".$tar."' 
									  and  arttarcod = artcod ";		   
							
								$err_3 = odbc_do($conex_o,$query_o3);
								while (odbc_fetch_row($err_3))
									  {	
										$artvrant = odbc_result($err_3,1);//valor anterior
										$artfec = odbc_result($err_3,2);//fecha inicio contrato
										$artvract = odbc_result($err_3,3);//valor actual
										$artnombre = odbc_result($err_3,4);//nombre del articulo
									  }
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$artnombre."</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$artfec."</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$artvract."</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$artvrant."</b></font></td>";
						
						$query_o7="SELECT empartind
									 FROM faempart
									 WHERE empartart = '".$codigo."' 
									  and  empartemp = '".$nit."' 
									  and  empartcco in ('*','".$ccostos."') ";		  
								$indicador=" ";	
								$err_7 = odbc_do($conex_o,$query_o7);
								while (odbc_fetch_row($err_7))
										  {	
											$indicador = odbc_result($err_7,1);//valor anterior
										  }
								if (empty($indicador) or $indicador == " " ) 
												$indicador='S';
								echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$indicador."</b></font></td>";
						$query_o8="SELECT Regcod	
									 FROM cliame_000270
									 WHERE Regcod = '".$codigo."' 
									  and  Regest = 'on' ";		  
								$err8 = mysql_query($query_o8,$conex);
								$num8 = mysql_num_rows($err8);
								if ($num8 > 0)
								{
									echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>SI</b></font></td>";
								}
								else
								{
									echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NO</b></font></td>";
								}
							echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO</b></font></td>";
					}
					if ($tab == 'INPROTAR')
					{
						$query_o4="SELECT protarvaa,protarfec,protarval,pronom 
									 FROM inprotar,inpro 
									 WHERE protarpro = '".$codigo."' 
									  and  protartar = '".$tar."' 
									  and  protarcon = '".$conc."' 
									  and  protarpro = procod ";		   
							
								$err_4 = odbc_do($conex_o,$query_o4);
								
								if (odbc_fetch_row($err_4))
								{
									//while (odbc_fetch_row($err_4))
									  //{	
								       	$provrant = odbc_result($err_4,1);//valor anterior
										$profec = odbc_result($err_4,2);//fecha inicio contrato
										$provract = odbc_result($err_4,3);//valor actual
										$pronombre = odbc_result($err_4,4);//nombre del procedimiento
									  //}
									echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$pronombre."</b></font></td>";
									echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$profec."</b></font></td>";
									echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$provract."</b></font></td>";
									echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$provrant."</b></font></td>";
									echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NO APLICA</b></font></td>";
									echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NO APLICA</b></font></td>";
									echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO</b></font></td>";
								}
								else
								{
									$q="Select proempane,proempnom,proempuni,proempliq From inproemp  Where  proemppro = '".$codigo."' And proempemp = '".$nit."' ";
									$resultliq = odbc_do($conex_o,$q);                   // Ejecuto el query
									if (odbc_fetch_row($resultliq))
										{
										  $wresultliqane=odbc_result($resultliq,1);
										  $wresultliqdes=odbc_result($resultliq,2);
										  $wresultliquni=odbc_result($resultliq,3);
										  $wresultliqliq=odbc_result($resultliq,4);
										  if ( $wresultliqliq == 'G' )
											{
												$q="Select proqui From inpro Where procod = '".$codigo."' ";
												$resultliqg = odbc_do($conex_o,$q);                   // Ejecuto el query
												if (odbc_fetch_row($resultliqg))
													{
														$wgrupo=odbc_result($resultliqg,1);
														$q="Select quitarvaa,quitarfec,quitarval From inquitar Where quitarqui = '".$wgrupo."' and quitartar = '".$tar."' and quitarcon = '".$conc."' ";
														$tarifa = odbc_do($conex_o,$q); 
														if (odbc_fetch_row($tarifa))
														{
															$wquitarvaa =odbc_result($tarifa,1);
															$wquitarfec =odbc_result($tarifa,2);
															$wquitarval =odbc_result($tarifa,3);
															echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$wresultliqdes."</b></font></td>";
															echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$wquitarfec."</b></font></td>";
															echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$wquitarval."</b></font></td>";
															echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$wquitarvaa."</b></font></td>";
															echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NO APLICA</b></font></td>";
															echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NO APLICA</b></font></td>";
															echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>GRUPO:'".$wgrupo."'</b></font></td>";
														}
													}
											}  
										  else
											{
												if ( $wresultliqliq == 'U' )
												{
													$q="Select unitarvaa,unitarfec,unitarval,(unitarvaa * '".$wresultliquni."' ),(unitarval * '".$wresultliquni."' )  From faunitar Where unitartar = '".$tar."' and unitarcon = '".$conc."' ";
													$tarifa = odbc_do($conex_o,$q); 
													if (odbc_fetch_row($tarifa))
													{
														$wquitarvaa =odbc_result($tarifa,1);
														$wquitarfec =odbc_result($tarifa,2);
														$wquitarval =odbc_result($tarifa,3);
														echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$wresultliqdes."</b></font></td>";
														echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$wquitarfec."</b></font></td>";
														echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>UVR:".$wquitarval."</b></font></td>";
														echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>UVR:".$wquitarvaa."</b></font></td>";
														echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NO APLICA</b></font></td>";
														echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NO APLICA</b></font></td>";
														echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>FACTURADO POR UVR CANTIDAD:'".$wresultliquni."'</b></font></td>";
													}
													
												}	
											}
									  
										}
								}
						
					}
					if ($tab == 'INEXATAR')
					{
						$query_o5="SELECT exatarvaa,exatarfec,exatarval,exanom 
									 FROM inexatar,inexa 
									 WHERE exatarexa = '".$codigo."' 
									  and  exatartar = '".$tar."' 
									  and  exatarcon = '".$conc."' 
									  and  exatarexa = exacod ";		   
							
								$err_5 = odbc_do($conex_o,$query_o5);
								while (odbc_fetch_row($err_5))
									  {	
										$exavrant = odbc_result($err_5,1);//valor anterior
										$exafec = odbc_result($err_5,2);//fecha inicio contrato
										$exavract = odbc_result($err_5,3);//valor actual
										$exanombre = odbc_result($err_5,4);//nombre del Examen
									  }
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$exanombre."</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$exafec."</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$exavract."</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$exavrant."</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NO APLICA</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NO APLICA</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO</b></font></td>";
					}
					if (trim($tab) == 'INTIP')
					{
						$query_o6="SELECT tipvaa,tipfec,tipval,tipdes 
									 FROM intip
									 WHERE tipcod = '".$codigo."' 
									  and  tiptar = '".$tar."' 
									  and  tipcon = '".$conc."' ";		   
							
								$err_6 = odbc_do($conex_o,$query_o6);
								while (odbc_fetch_row($err_6))
									  {	
										$tipant = odbc_result($err_6,1);//valor anterior
										$tipfec = odbc_result($err_6,2);//fecha inicio contrato
										$tipvract = odbc_result($err_6,3);//valor actual
										$tipnombre = odbc_result($err_6,4);//nombre habitacion
									  }
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$tipnombre."</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$tipfec."</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$tipvract."</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$tipant."</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NO APLICA</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NO APLICA</b></font></td>";
						echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO</b></font></td>";
					}
				}
				echo "</tr>";
				
			  }
		echo "</table>";
		echo "<br><br><br>";
	    echo "<center><table>";
	    echo "<tr><td><A HREF='rep_ayuresglosa.php' >Retornar</A></td></tr>";
	    echo "</table>";
		 	 
 }
   
	 
  
}
odbc_close($conex_o);
odbc_close_all();   
?>