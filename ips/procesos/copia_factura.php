<head>
  <title>REPORTE HORAS DE DESARROLLO</title>
<style type="text/css">
  .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
  .tipo3V:hover {color: #000066; background: #999999;}
</style>
<style type="text/css">
    /* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
    .ui-datepicker {font-size:12px;}

    /* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
    .ui-datepicker-cover {
        display: none; /*sorry for IE5*/
        display/**/: block; /*sorry for IE5*/
        position: absolute; /*must have*/
        z-index: -1; /*must have*/
        filter: mask(); /*must have*/
        top: -4px; /*must have*/
        left: -4px; /*must have*/
        width: 100px; /*must have*/
        height: 100px; /*must have*/
    }
</style>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script>
    $.datepicker.regional['esp'] = {
        closeText: 'Cerrar',
        prevText: 'Antes',
        nextText: 'Despues',
        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
        'Jul','Ago','Sep','Oct','Nov','Dic'],
        dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
        dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
        dayNamesMin: ['D','L','M','M','J','V','S'],
        weekHeader: 'Sem.',
        dateFormat: 'yy-mm-dd',
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['esp']);
</script>
<script type="text/javascript">
    $( document ).ready( function(){

        $(".inputFechas").datepicker({
             showOn: "button",
             buttonImage: "../../images/medical/root/calendar.gif",
             buttonImageOnly: true,
             changeYear:true,
             reverseYearRange: true,
             changeMonth: true,
             maxDate: $("#wfechahoy").val()
        });
    } );
</script>
</head>
<?php
include_once("conex.php");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// !!! ATENCION ¡¡¡ SI SE HACE ALGUN CAMBIO EN ESTE PROGRAMA TAMBIEN SE DEBE DE HACER EN EL PROGRAMA imp_factura.php y VICEVERSA
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//

//			      or die("No se ralizo Conexion");

include_once("root/comun.php");



$wfecha=date("Y-m-d");
$hora = (string)date("H:i:s");
$wcf="DDDDDD";                //COLOR DEL FONDO    -- Gris claro
$wcf2="006699";               //COLOR DEL FONDO 2  -- Azul claro
$wclfa="FFFFFF";              //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
$wclfg="003366";              //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
$wactualiz  = "2014-04-16";


echo "<form name='copia_factura' action='copia_factura.php' method=post>";

echo "<center><table border=2 width=100>";


$wtamlet=7;   //Tamaño de letra

$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));

if(isset($wemp_pmla) && $wemp_pmla!="")
{
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$wbasedato = $institucion->baseDeDatos;
}
else
{
	$q = " SELECT
				Empcod
			FROM
				root_000050
			WHERE
				Empbda = '".$wbasedato."'
				AND empest = 'on';";

	$res_emp = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num_emp = mysql_num_rows($res_emp);

	if ($num_emp > 0)
	{
		$rs_emp = mysql_fetch_array($res_emp);
		$wemp_pmla = $rs_emp['Empcod'];;
	}
}

function imprimir_cum()
{
	global $wemp_pmla;
	global $conex;

	$q = " SELECT
				Detval
			FROM
				root_000051
			WHERE
				Detemp = '".$wemp_pmla."'
				AND Detapl = 'imprimeCUM';";

	$res_imp = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num_imp = mysql_num_rows($res_imp);

	if ($num_imp > 0)
	{
		$rs_imp = mysql_fetch_array($res_imp);
		if($rs_imp['Detval']=='on')
			return true;
		else
			return false;
	}
	else
		return true;
}

echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>";

//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES
//========================================================================================================================================\\
// Abril 13 DE 2016 Felipe Alvarez
// ________________________________________________________________________________________________________________________________________\\
// Se modifica el programa para que permita buscar por ventas sin factura y por ventas con factura , para facilitar la busqueda en uvglobal ,
// estos cambios solo aplican para uvglobal , las otras empresas siguen con lo mismo 
//
//                                                                                                                      \\
//========================================================================================================================================\\
//========================================================================================================================================\\
// ENERO 30 DE 2015 Frederick Aguirre
// ________________________________________________________________________________________________________________________________________\\
// Se modifica el encabezado de la factura agregando el texto: SOMOS AUTORRETENEDORES Res. DIAN. No.10653.  03/12/2014
//                                                                                                                      \\
//========================================================================================================================================\\
// Abril 16 DE 2014 Camilo
// ________________________________________________________________________________________________________________________________________\\
// Se modificaon los estilos del programa, para que corresponda con los colores y estilos matrix
//________________________________________________________________________________________________________________________________________\\
// OCTUBRE 21 DE 2013 Jonatan
// ________________________________________________________________________________________________________________________________________\\
// Se mueve una columna a a derecha el resultado del IVA para que no se confunda con el valor de la compra.
//________________________________________________________________________________________________________________________________________\\
// J U L I O  6  DE  2012:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
// Se adicionó la función imprimir_cum que permite saber si en la empresa actual se imprime la columna de código CUM o no
// para esto se agregaron varios condicionales if(imprimir_cum()) que indica las acciones que se ejecutan cuando imprime CUM
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// J U N I O  8  DE  2012:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
// Se adicionaron los campos Artcna, Vdelot	y Vdefve para mostrar el código CUN, número del lote y la fecha de vencimiento de cada artículo
// Los dos últimos campos mencionados se muestran cuando el parámetro wlotven = "on" y $wtipcli != "01-PARTICULAR"
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// J U N I O  15  DE  2012:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
// Se debió establecer ancho de tabla y celdas al igual que un tamaño mas pequeño de letra en el detalle de la venta cuando ésta debe
// mostrar "# de lote" y "Fecha de vencimiento", todo esto para que la impresión de la factura no pase el ancho del papel
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// J U N I O  20  DE  2012:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
// Se creó la función prepararCadena que permite verificar que el detalle de los artículos no exceda la longitud debida por cada renglón
// para que la impresión de la factura no se desborde
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
//________________________________________________________________________________________________________________________________________\\

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////
//ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
  $q =  " SELECT cjecco, cjecaj, cjetin "
       ."   FROM ".$wbasedato."_000030 "
       ."  WHERE cjeusu = '".$wusuario."'"
       ."    AND cjeest = 'on' ";

  $res = mysql_query($q,$conex);
  $num = mysql_num_rows($res);
  if ($num > 0)
     {
      $row = mysql_fetch_array($res);

      $pos = strpos($row[0],"-");
      $wcco = substr($row[0],0,$pos);
      $wnomcco = substr($row[0],$pos+1,strlen($row[0]));

      $pos = strpos($row[1],"-");
      $wcaja = substr($row[1],0,$pos);
      $wnomcaj = substr($row[1],$pos+1,strlen($row[1]));
     }
///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

	// Funcion para preparar la cadena de descripción del articulo de modo que no haya
	// una palabra mas larga de 13 caracteres pues se sale del rango de impresión
	function prepararCadena($cadena,$wordsize)
	{
		$strcad = explode(" ",$cadena);
		for($i=0;$i<count($strcad);$i++)
		{
			$numlet = strlen($strcad[$i]);
			if($numlet>$wordsize)
			{
				$nombre = $strcad[$i];
				$strcad[$i] = substr($nombre, 0, $wordsize-1)." ".substr($nombre, $wordsize-1, $wordsize-1);
			}
		}
		$cadena_ok = "";
		for($i=0;$i<count($strcad);$i++)
		{
			$cadena_ok .= $strcad[$i]." ";
		}
		return $cadena_ok;
	}

    // Defino si la empresa actual requiere facturar con número de lote y fecha de vencimiento
	$q =   "SELECT Detval "
		."    FROM root_000051 "
		."   WHERE Detemp = '".$wemp_pmla."' "
		."     AND Detapl = 'incluye_lote_y_vencimiento' ";
	$res_lot_ven = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$row_lot_ven = mysql_fetch_array($res_lot_ven);
	if($row_lot_ven && $row_lot_ven['Detval']=='on')
	{
		$wlotven = 'on';
	}
	else
	{
		$wlotven = 'off';
	}

echo "<input type='HIDDEN' name= 'wlotven' value='".$wlotven."'>";

if (!isset($wfecini) or !isset($wfecfin))
   {
	/*echo "<center><table border=2 width=100>";
	echo "<tr align='center'>";
    echo "<tr><td align='center' rowspan=2 colspan=2><img src='/matrix/images/medical/ips/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
    echo "<tr><td align='center' colspan=2 bgcolor=".$wcf2."><font size=6 color=#FFFFFF><b>REIMPRESION DE FACTURAS</b></font></td></tr>";
    echo "<tr>";
    echo "<td bgcolor=".$wcf." colspan=2><b><font color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecha." SIZE=10></td>";
    echo "<td bgcolor=".$wcf." colspan=2><b><font color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecha." SIZE=10></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<input type='HIDDEN' name= 'wcaja' value='".$wcaja."'>";
    echo "<td align='center' colspan=4 bgcolor=#cccccc><input type='submit' value='OK'></td>";                                         //submit
    echo "</tr>";*/
    encabezado("REIMPRESION DE FACTURAS",$wactualiz, $wbasedato);
    echo "<center><table width='500'>";
    echo "<tr class='encabezadotabla'>";
    echo "<td colspan='4' align='center'> RANGO DE FECHAS </td>";
    echo "</tr>";
    echo "<tr class='fila1'>";
    echo "<td align='left'> Fecha inicial: </td><td> <input class='inputFechas' type='text' size='10' id='wfecini' name='wfecini' value='{$wfecha}'> </td>";
    echo "<td align='left'> Fecha final:   </td><td> <input class='inputFechas' type='text' size='10'id='wfecfin' name='wfecfin' value='{$wfecha}'> </td>";
    echo "</tr>";
    echo "<input type='HIDDEN' name= 'wcaja' value='".$wcaja."'>";
    echo "<td align='center' colspan=4 bgcolor=#cccccc><input type='submit' value='OK'></td>";                                         //submit
    echo "</tr>";
   }
  else
     {
	  if (!isset($wnrovta) or !isset($wfuefac) or !isset($wnrofac))
	     {
		  
			// Abril 13 DE 2016 Felipe Alvarez
			// ________________________________________________________________________________________________________________________________________\\
			// Se modifica el programa para que permita buscar por ventas sin factura y por ventas con factura , para facilitar la busqueda en uvglobal ,
			// estos cambios solo aplican para uvglobal , las otras empresas siguen con lo mismo 
		  if($wemp_pmla == '06' )
			{
		
		
			  $q = "SELECT vennum, venffa, vennfa, venvto, ventve, vennmo "
              ."  FROM ".$wbasedato."_000016 "
              ." WHERE venfec between '".$wfecini."' AND '".$wfecfin."'"
              ."   AND vencaj = '".$wcaja."'"
              ."   AND venest = 'on' "
			  ."   AND Vennfa = '' ";
			}
			else
			{
				$q = "SELECT vennum, venffa, vennfa, venvto, ventve, vennmo "
				  ."  FROM ".$wbasedato."_000016 "
				  ." WHERE venfec between '".$wfecini."' AND '".$wfecfin."'"
				  ."   AND vencaj = '".$wcaja."'"
				  ."   AND venest = 'on'  ";
			}
		 
		  
          $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
          $num = mysql_num_rows($res);

          if ($num > 0)
             {
              encabezado("REIMPRESION DE FACTURAS",$wactualiz, $wbasedato);
              echo "<br><center><table border=2 width=100>";
              if($wemp_pmla == '06')
			  {
				echo"<tr><td align='left' class='fila1' colspan='7'><b>VENTAS REALIZADAS </td></tr>";
			  }
			  echo "<tr>";
              echo "<td align='left' class='fila1' colspan='7'><b><font>NRO CAJA: </font></b>".$wcaja."</td>";
              echo "</tr>";
              echo "<tr class='fila1'>";
              echo "<td align='left' colspan='3'><b>RANGO DE FECHAS:</b> </td>";
              echo "<td align='center' colspan='6'><b> ".$wfecini." hasta ".$wfecfin."</b></td></tr>";
              //echo "<tr><td align='center' colspan='7' class='encabezadotabla'><font size=4 color=#FFFFFF><b>DOCUMENTOS DEL PERIODO</b></font></td></tr>";
              echo "<th class='encabezadotabla' nowrap='nowrap'>Mvto Nro</th>";
	          echo "<th class='encabezadotabla'>Venta Nro</th>";
	          echo "<th class='encabezadotabla'>Fuente Fra</th>";
	          echo "<th class='encabezadotabla' nowrap>Factura Nro</th>";
	          echo "<th class='encabezadotabla'>Valor Venta</th>";
	          echo "<th class='encabezadotabla' colspan='2' align=left>Tipo Venta</th>";

	          $wtotalven=0;
              for ($i=1;$i<=$num;$i++)
                  {
	               $row = mysql_fetch_array($res);
                   ( is_int( $i/2 ) ) ? $wclass = "fila1" : $wclass = "fila2";
	               echo "<tr class='$wclass'>";
	               echo "<td align='center'>".$row[5]."</td>";
	               echo "<td align='center'>".$row[0]."</td>";
	               echo "<td align='center'>".$row[1]."</td>";
	               echo "<td align='center'>".$row[2]."</td>";
	               echo "<td align=right>".number_format(($row[3]),0,'.',',')."</td>";
	               echo "<td>".$row[4]."</td>";

				   // se hace un cambio para uvglobal, por que el programa de copia de factura viene imprimiendo bien para las demas empresa
				   // se hace para conservar la impresion de antes y modificar especificamente para uvglobal

				   if($wemp_pmla == '06' and  $row[2] == '')
				   {
						echo "<td align='center'><font size=3><A href='copia_factura.php?wnrovta=".$row[0]."&amp;wfuefac=".$row[1]."&amp;wnrofac=".$row[2]."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wemp_pmla=".$wemp_pmla."&amp;wlotven=".$wlotven."&esventa=si' target='_blank'> Imprimir Recibo</A></font></td>";
				   }
				   else
				   {
						echo "<td align='center'><font size=3><A href='copia_factura.php?wnrovta=".$row[0]."&amp;wfuefac=".$row[1]."&amp;wnrofac=".$row[2]."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wemp_pmla=".$wemp_pmla."&amp;wlotven=".$wlotven."' target='_blank'> Seleccionar</A></font></td>";
				  												 //imp_factura.php?wnrofac=&wfueffa= &fk=2&wvalcam=200000&wnrorec=129246&wnrovta=LA-25585&wemp=196&wempresa=196-805000601-ALIADOS EN SALUD S.A&wdocpac=1128271403&wnompac=Felipe&wnompac=SIN DATO&wte1pac=22222&wmensajero=&wtipven=Directa&wfecha_tempo=2015-10-01&whora_tempo=09:36:48&wcco=001&wcaja=03&wusuario=03150&wcuotamod=0&wempfac=off&wtipcli=EP-EPS&wbasedato=uvglobal&wpuntos_cliente=&wprograma=&wprestamo=0&wfpa[1]=11 - EFECTIVO&wdocane[1]=&wobsrec[1]=&wvalfpa[1]=100000.00&wfpa[2]=11 - EFECTIVO&wdocane[2]=&wobsrec[2]=&wvalfpa[2]=0.00&wlotven=off&wemp_pmla=06&wsaldo=23500&wentregado=300000&wabonado=100000
						//echo "<td align='center'><font size=3><A href='imp_factura.php?wnrofac=".$row[2]."&wfueffa=".$row[1]."&fk=&wvalcam=&wnrorec=&wnrovta=&wemp=&wempresa=&wdocpac=&wnompac=&wnompac=&wte1pac=&wmensajero=&wtipven=Directa&wfecha_tempo=&whora_tempo=&wcco=001&wcaja=&wusuario=&wcuotamod=&wempfac=off&wtipcli=&wbasedato=&wpuntos_cliente=&wprograma=&wprestamo=0&wfpa[1]=11 - EFECTIVO&wdocane[1]=&wobsrec[1]=&wvalfpa[1]=100000.00&wfpa[2]=11 - EFECTIVO&wdocane[2]=&wobsrec[2]=&wvalfpa[2]=0.00&wlotven=off&wemp_pmla=06&wsaldo=23500&wentregado=300000&wabonado=100000' target='_blank'> Imprimir Recibo venta</A></font></td>";
	               }
				   echo "</tr>";

	               $wtotalven=$wtotalven+$row[3];
                  }
               echo "<th bgcolor=".$wcf." colspan=4 align=right>Total Ventas</th>";
               echo "<th bgcolor=".$wcf." align=right>".number_format($wtotalven,2,'.',',')."</th>";
               echo "<th bgcolor=".$wcf." colspan=2 align=right></th>";

             }
            else
               {
	            echo "<input type='HIDDEN' name= 'wcaja' value='".$wcaja."'>";
                echo "<br><br><br>";
                echo "<tr><td align='center' colspan=4 bgcolor=".$wcf2."><font size=6 color=#FFFFFF><b>NO EXISTE MOVIMIENTO PARA ESTA CAJA EN EL PERIODO SELECCIONADO</b></font></td></tr>";
                echo "<td align='center' colspan=4 bgcolor=#cccccc><input type='submit' value='OK'></td>";                                         //submit
               }
          echo "</table><br><br><br>";
		
		// Abril 13 DE 2016 Felipe Alvarez
		// ________________________________________________________________________________________________________________________________________\\
		// Se modifica el programa para que permita buscar por ventas sin factura y por ventas con factura , para facilitar la busqueda en uvglobal ,
		// estos cambios solo aplican para uvglobal , las otras empresas siguen con lo mismo 
		if($wemp_pmla == '06' )
		{
		
			 $q = "SELECT vennum, venffa, vennfa, venvto, ventve, vennmo "
              ."  FROM ".$wbasedato."_000016 , ".$wbasedato."_000018  "
              ." WHERE Fenfec between '".$wfecini."' AND '".$wfecfin."'"
              ."   AND Vencco = '".$wcco."'"
              ."   AND venest = 'on' "
			  ."   AND Vennfa != ''  "
			  ."   AND Vennfa = Fenfac ";
          $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
          $num = mysql_num_rows($res);

          if ($num > 0)
             {
             // encabezado("REIMPRESION DE FACTURAS",$wactualiz, $wbasedato);
              echo "<br><center><table border=2 width=100>";
              
			  
			
				echo"<tr><td align='left' class='fila1' colspan='7'><b>FACTURAS REALIZADAS </td></tr>";
			  
			  echo "<tr>";
              
			  
			  
			  echo "
					<td align='left' class='fila1' colspan='7'><b><font>NRO CAJA: </font></b>".$wcaja."</td>";
              echo "</tr>";
              echo "<tr class='fila1'>";
              echo "<td align='left' colspan='3'><b>RANGO DE FECHAS:</b> </td>";
              echo "<td align='center' colspan='6'><b> ".$wfecini." hasta ".$wfecfin."</b></td></tr>";
              //echo "<tr><td align='center' colspan='7' class='encabezadotabla'><font size=4 color=#FFFFFF><b>DOCUMENTOS DEL PERIODO</b></font></td></tr>";
              echo "<th class='encabezadotabla' nowrap='nowrap'>Mvto Nro</th>";
	          echo "<th class='encabezadotabla'>Venta Nro</th>";
	          echo "<th class='encabezadotabla'>Fuente Fra</th>";
	          echo "<th class='encabezadotabla' nowrap>Factura Nro</th>";
	          echo "<th class='encabezadotabla'>Valor Venta</th>";
	          echo "<th class='encabezadotabla' colspan='2' align=left>Tipo Venta</th>";

	          $wtotalven=0;
              for ($i=1;$i<=$num;$i++)
                  {
	               $row = mysql_fetch_array($res);
                   ( is_int( $i/2 ) ) ? $wclass = "fila1" : $wclass = "fila2";
	               echo "<tr class='$wclass'>";
	               echo "<td align='center'>".$row[5]."</td>";
	               echo "<td align='center'>".$row[0]."</td>";
	               echo "<td align='center'>".$row[1]."</td>";
	               echo "<td align='center'>".$row[2]."</td>";
	               echo "<td align=right>".number_format(($row[3]),0,'.',',')."</td>";
	               echo "<td>".$row[4]."</td>";

				   // se hace un cambio para uvglobal, por que el programa de copia de factura viene imprimiendo bien para las demas empresa
				   // se hace para conservar la impresion de antes y modificar especificamente para uvglobal

				   if($wemp_pmla == '06' and  $row[2] == '')
				   {
						echo "<td align='center'><font size=3><A href='copia_factura.php?wnrovta=".$row[0]."&amp;wfuefac=".$row[1]."&amp;wnrofac=".$row[2]."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wemp_pmla=".$wemp_pmla."&amp;wlotven=".$wlotven."&esventa=si' target='_blank'> Imprimir Recibo</A></font></td>";
				   }
				   else
				   {
						echo "<td align='center'><font size=3><A href='copia_factura.php?wnrovta=".$row[0]."&amp;wfuefac=".$row[1]."&amp;wnrofac=".$row[2]."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wemp_pmla=".$wemp_pmla."&amp;wlotven=".$wlotven."' target='_blank'> Seleccionar</A></font></td>";
				  												 //imp_factura.php?wnrofac=&wfueffa= &fk=2&wvalcam=200000&wnrorec=129246&wnrovta=LA-25585&wemp=196&wempresa=196-805000601-ALIADOS EN SALUD S.A&wdocpac=1128271403&wnompac=Felipe&wnompac=SIN DATO&wte1pac=22222&wmensajero=&wtipven=Directa&wfecha_tempo=2015-10-01&whora_tempo=09:36:48&wcco=001&wcaja=03&wusuario=03150&wcuotamod=0&wempfac=off&wtipcli=EP-EPS&wbasedato=uvglobal&wpuntos_cliente=&wprograma=&wprestamo=0&wfpa[1]=11 - EFECTIVO&wdocane[1]=&wobsrec[1]=&wvalfpa[1]=100000.00&wfpa[2]=11 - EFECTIVO&wdocane[2]=&wobsrec[2]=&wvalfpa[2]=0.00&wlotven=off&wemp_pmla=06&wsaldo=23500&wentregado=300000&wabonado=100000
						//echo "<td align='center'><font size=3><A href='imp_factura.php?wnrofac=".$row[2]."&wfueffa=".$row[1]."&fk=&wvalcam=&wnrorec=&wnrovta=&wemp=&wempresa=&wdocpac=&wnompac=&wnompac=&wte1pac=&wmensajero=&wtipven=Directa&wfecha_tempo=&whora_tempo=&wcco=001&wcaja=&wusuario=&wcuotamod=&wempfac=off&wtipcli=&wbasedato=&wpuntos_cliente=&wprograma=&wprestamo=0&wfpa[1]=11 - EFECTIVO&wdocane[1]=&wobsrec[1]=&wvalfpa[1]=100000.00&wfpa[2]=11 - EFECTIVO&wdocane[2]=&wobsrec[2]=&wvalfpa[2]=0.00&wlotven=off&wemp_pmla=06&wsaldo=23500&wentregado=300000&wabonado=100000' target='_blank'> Imprimir Recibo venta</A></font></td>";
	               }
				   echo "</tr>";

	               $wtotalven=$wtotalven+$row[3];
                  }
               echo "<th bgcolor=".$wcf." colspan=4 align=right>Total Ventas</th>";
               echo "<th bgcolor=".$wcf." align=right>".number_format($wtotalven,2,'.',',')."</th>";
               echo "<th bgcolor=".$wcf." colspan=2 align=right></th>";

             }
            else
               {
	            echo "<input type='HIDDEN' name= 'wcaja' value='".$wcaja."'>";
                echo "<br><br><br>";
                echo "<tr><td align='center' colspan=4 bgcolor=".$wcf2."><font size=6 color=#FFFFFF><b>NO EXISTE MOVIMIENTO PARA ESTA CAJA EN EL PERIODO SELECCIONADO</b></font></td></tr>";
                echo "<td align='center' colspan=4 bgcolor=#cccccc><input type='submit' value='OK'></td>";                                         //submit
               }
          echo "</table>";
		  
		}
		  
         }
       else
          {
	        //=======================================================================
	        //ACA ESTA SELECCIONADA LA FACTURA A IMPRIMIR
	        //=======================================================================
	        echo "</table>";
	        echo "<left><table border=0 width='100'>";


	        //ACA TRAIGO SI EL RESPONSABLE DE LA FACTURA ES UNA EMPRESA Y SI SI ENTONCES VERIFICO QUE SI LA EMPRESA SE FACTURA EN BLOQUE.
	        if ($wnrofac != "")
	           {
	            $q = "    SELECT empfac "
	                ."      FROM ".$wbasedato."_000018, ".$wbasedato."_000024 "
	                ."     WHERE fenfac = '".$wnrofac."'"
	                ."       AND fencod = empcod "
	                ."       AND empfac = 'off' ";
	            $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	            $num = mysql_num_rows($res);
	            if ($num > 0)
	               {
	                $row = mysql_fetch_array($res);
	                $wfacablocada=$row[0];
                   }
                  else
                     $wfacablocada="";
               }
              else
                 $wfacablocada="";

	        //ACA TRAIGO LA FACTURA, EL RECIBO Y LA VENTA
			$q = "         SELECT rdenum, ".$wbasedato."_000016.fecha_data, ".$wbasedato."_000016.hora_data, clidoc, clinom, clite1, clidir, climai, empcod, empnit, empnom, venusu, descripcion, ventve, vencmo, vencaj, venmsj, ventcl ";
			if ($wnrofac != "") //Para verificar que la factura si exista y no imprima un documento descuadrado
			   $q = $q."     FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000021, ".$wbasedato."_000041, ".$wbasedato."_000018 ";
			  else
			     $q = $q."   FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000021, ".$wbasedato."_000041 ";
			$q = $q."       WHERE vennum = '".$wnrovta."'";
			$q = $q."         AND venusu = codigo ";
			$q = $q."         AND vennum = rdevta ";
			$q = $q."         AND vennit = clidoc ";
			$q = $q."         AND vencod = empcod ";
			$q = $q."         AND ventcl = emptem ";
			if ($wnrofac != "")
			   {
			    $q = $q."     AND vennfa = fenfac ";
                $q = $q."     AND venvto = fenval ";
               }
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);

			if ($num > 0)
			   {
				$row = mysql_fetch_array($res);
				$wnrorec      = $row[0];
				$wfecha_tempo = $row[1];
				$whora_tempo  = $row[2];
				$wdocpac      = $row[3];
				$wnompac      = $row[4];
				$wtelpac      = $row[5];
				$wdirpac      = $row[6];
				$wmaipac      = $row[7];
				$wnomnit      = $row[8]."-".$row[9]."-".$row[10];
				$wcodusu      = $row[11];
				$wnomusu      = $row[12];
				$wtipven      = $row[13];
				$wcuotamod    = $row[14];
				$wcaja        = $row[15];
				$wmensajero   = $row[16];
				$wtipcli      = $row[17];
			   }
			  else
			     {
				  //ACA TRAIGO EL RECIBO Y LA VENTA
				  $q = "          SELECT rdenum, ".$wbasedato."_000016.fecha_data, ".$wbasedato."_000016.hora_data, clidoc, clinom, clite1, clidir, climai, empcod, empnit, empnom, venusu, descripcion, ventve, vencmo, vencaj, venmsj, ventcl ";
				  if ($wnrofac != "")
				      $q = $q."     FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000041, ".$wbasedato."_000021, ".$wbasedato."_000018 ";
				     else
				        $q = $q."   FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000041, ".$wbasedato."_000021 ";
				  $q = $q."        WHERE vennum = '".$wnrovta."'";
				  $q = $q."          AND vennum = rdevta ";
				  $q = $q."          AND venusu = codigo ";
				  $q = $q."          AND vennit = clidoc ";
				  $q = $q."          AND vencod = empcod ";
				  $q = $q."          AND ventcl = emptem ";
				  if ($wnrofac != "")
				     {
				      $q = $q."      AND vennfa = fenfac ";
	                  $q = $q."      AND venvto = fenval ";
	                 }
				  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				  $num = mysql_num_rows($res);

				  if ($num > 0)
				     {
					  $row = mysql_fetch_array($res);
					  $wnrorec      = $row[0];
	                  $wfecha_tempo = $row[1];
					  $whora_tempo  = $row[2];
					  $wdocpac      = $row[3];
					  $wnompac      = $row[4];
					  $wtelpac      = $row[5];
					  $wdirpac      = $row[6];
					  $wmaipac      = $row[7];
					  $wnomnit      = $row[8]."-".$row[9]."-".$row[10];
					  $wcodusu      = $row[11];
					  $wnomusu      = $row[12];
					  $wtipven      = $row[13];
					  $wcuotamod    = $row[14];
					  $wcaja        = $row[15];
					  $wmensajero   = $row[16];
					  $wtipcli      = $row[17];
				     }
				    else
				       {
					    //ACA TRAIGO LA VENTA
						$q = "       SELECT 0, ".$wbasedato."_000016.fecha_data, ".$wbasedato."_000016.hora_data, clidoc, clinom, clite1, clidir, climai, empcod, empnit, empnom, venusu, descripcion, ventve, vencmo, vencaj, venmsj, ventcl ";
						if ($wnrofac != "" and $wfacablocada=="on")
						   $q=$q."     FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000041, ".$wbasedato."_000018 ";
						  else
						     $q=$q."   FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000041 ";
						$q=$q."       WHERE vennum = '".$wnrovta."'";
						$q=$q."         AND venusu = codigo ";
						$q=$q."         AND vennit = clidoc ";
						$q=$q."         AND vencod = empcod ";
						$q=$q."         AND ventcl = emptem ";
						//if ($wnrofac != "" and $wtipfac == "01-PARTICULAR")   or $wfacablocada=="off"
						if ($wnrofac != "" and $wfacablocada=="on")
					       {
					        $q = $q."   AND vennfa = fenfac ";
		                    $q = $q."   AND venvto = fenval ";
		                    //$q = $q."   AND fentip = '01-PARTICULAR'";
		                   }

		                $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						$num = mysql_num_rows($res);

						if ($num > 0)
						   {
						    $row = mysql_fetch_array($res);
						    $wnrorec      = $row[0];
			                $wfecha_tempo = $row[1];
						    $whora_tempo  = $row[2];
						    $wdocpac      = $row[3];
						    $wnompac      = $row[4];
						    $wtelpac      = $row[5];
						    $wdirpac      = $row[6];
						    $wmaipac      = $row[7];
						    $wnomnit      = $row[8]."-".$row[9]."-".$row[10];
						    $wcodusu      = $row[11];
						    $wnomusu      = $row[12];
						    $wtipven      = $row[13];
						    $wcuotamod    = $row[14];
						    $wcaja        = $row[15];
						    $wmensajero   = $row[16];
						    $wtipcli      = $row[17];
						   }
						  else
						     {
							  //ACA TRAIGO LA VENTA
							  $q = "          SELECT 0, ".$wbasedato."_000016.fecha_data, ".$wbasedato."_000016.hora_data, clidoc, clinom, clite1, clidir, climai, empcod, empnit, empnom, venusu, descripcion, ventve, vencmo, vencaj, venmsj, ventcl ";
							  if ($wnrofac != "" or $wfacablocada=="off")
							      $q = $q."     FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000041, ".$wbasedato."_000018 ";
							     else
							        $q = $q."   FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000041 ";
							  $q = $q."        WHERE vennum = '".$wnrovta."'";
							  $q = $q."          AND venusu = codigo ";
							  $q = $q."          AND clidoc = '9999' ";
							  $q = $q."          AND vencod = empcod ";
							  $q = $q."          AND ventcl = emptem ";
							  if ($wnrofac != "" or $wfacablocada=="off")
							     {
							      $q = $q."      AND vennfa = fenfac ";
				                  $q = $q."      AND venvto = fenval ";
				                 }
							  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							  $num = mysql_num_rows($res);

							  if ($num > 0)
							     {
							      $row = mysql_fetch_array($res);
							      $wnrorec      = $row[0];
					              $wfecha_tempo = $row[1];
								  $whora_tempo  = $row[2];
								  $wdocpac      = $row[3];
								  $wnompac      = $row[4];
								  $wtelpac      = $row[5];
								  $wdirpac      = $row[6];
								  $wmaipac      = $row[7];
								  $wnomnit      = $row[8]."-".$row[9]."-".$row[10];
								  $wcodusu      = $row[11];
								  $wnomusu      = $row[12];
								  $wtipven      = $row[13];
								  $wcuotamod    = $row[14];
								  $wcaja        = $row[15];
								  $wmensajero   = $row[16];
								  $wtipcli      = $row[17];
								 }
							 }
					   }
				 }
			//=======================================================================

			echo "<tr><td colspan=8>&nbsp</td></tr>";

			//============================================================================================================================
			//============================================================================================================================
			//SI num=0 INDICA QUE EL DOCUMENTO NO EXISTE O ESTA DESCUADRADO !!!!!!!!!!!! NO EXISTE O ESTA DESCUADRADO !!!!!!!!!!!!!
			//============================================================================================================================
			if ($num == 0)
			   {
				echo "<tr><td colspan=8>&nbsp</td></tr>";
				echo "<tr><td colspan=8>&nbsp</td></tr>";
				echo "<tr><td colspan=8>&nbsp</td></tr>";
			    echo "<tr><td colspan=8><font size=".$wtamlet."><B>!!!!! ATENCION !!!!! EL DOCUMENTO NO EXISTE O PRESENTA ALGUN DESCUADRE</B></font></td></tr>";
			    echo "<tr><td colspan=8><font size=".$wtamlet."><B>DEBE REALIZARLE NOTA CREDITO A ESTE DOCUMENTO Y VOLVER A REALIZAR LA VENTA</B></font></td></tr>";
			    echo "<tr><td colspan=8>&nbsp</td></tr>";
				echo "<tr><td colspan=8>&nbsp</td></tr>";
				echo "<tr><td colspan=8>&nbsp</td></tr>";
		       }
			  else
			   {

				$pos = strpos($wnomnit,"-");
				$wemp = substr($wnomnit,0,$pos-1);

				$pos1 = strpos($wnomnit,"-",$pos+1);
				$wnitemp = substr($wnomnit,$pos+1,$pos1-3);

				$wnomemp = substr($wnomnit,$pos1+1,strlen($wnomnit));

				$wempresa=explode("-",$wnomnit);


	            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//ACA TRAIGO LOS DATOS NECESARIOS PARA IMPRIMIR LA FACTURA DESDE LA TABLA DE CONFIGURACION
				$q = " SELECT cfgnit, cfgnom, cfgtre, cfgtel, cfgdir, cfgfran, cfgfian, cfgffan, cfgran, cfgfcar, cfgfrac, cfgfiac, cfgffac, cfgrac, cfgpin, cfgmai, cfgdom, Cfgret "
				    ."   FROM ".$wbasedato."_000049 "
				    ."  WHERE cfgcco = '".$wcco."'";

				$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$row = mysql_fetch_array($res);

				$wnit_pos  =$row[0];
				$wnomemppos=$row[1];
				$wtipregiva=$row[2];
				$wtel_pos  =$row[3];
				$wdir_pos  =$row[4];
				if ($row[9] > $wfecha_tempo)
				   {
				    $wnrores=$row[8];  //Nro Resolucion Anterior
				    $wfecres=$row[5];  //Fecha Resolucion Anterior
				    $wfacini=$row[6];  //Factura Inicial Anterior
				    $wfacfin=$row[7];  //Factura Final Anterior
				   }
				  else
				     {
					  $wnrores=$row[13];  //Nro Resolucion Actual
					  $wfecres=$row[10];  //Fecha Resolucion Actual
					  $wfacini=$row[11];  //Factura Inicial Anterior
					  $wfacfin=$row[12];  //Factura Final Anterior
				     }
				$wpagintern=$row[14];
				$wemail_pos=$row[15];
				$wteldompos=$row[16];
				$wretenedores=$row[17];
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


				/////////////////////////////////////
				if ($wnrofac != "")
				   {
					//IMPRESION CON EL LOGO AL PRINCIPIO
					echo "<tr><td align='center' colspan=8><img src='/matrix/images/medical/ips/logo_".$wbasedato.".png' WIDTH=510 HEIGHT=200></td></tr>";
					echo "<tr><td colspan=8 align='center'><font size=".$wtamlet.">".$wnomemppos."</font></td></tr>";
					echo "<tr><td colspan=8 align='center'><font size=".$wtamlet.">Nit. : ".$wnit_pos."</font></td></tr>";
					echo "<tr><td colspan=8 align='center'><font size=".$wtamlet.">Tel. : ".$wtel_pos." Dir.: ".$wdir_pos."</font></td></tr>";
					echo "<tr><td colspan=8 align='center'><font size=".$wtamlet.">".$wtipregiva."</font></td></tr>";
					if( $wretenedores != "" ) echo "<tr><td colspan=8 align=center><font size=".$wtamlet.">".$wretenedores."</font></td></tr>";
					echo "<tr><td colspan=4 align=left><font size=".$wtamlet.">Hora: ".$whora_tempo."</font></td>";
					$q5 = "SELECT Fenfec FROM ".$wbasedato."_000018 WHERE  Fenfac = '".$wnrofac."' ";
					
					$res5 = mysql_query($q5,$conex) or die (mysql_errno()." - ".mysql_error());
					$num5 = mysql_num_rows($res5);

					if ($num5 > 0)
					   {
					    $row5 = mysql_fetch_array($res5);
					    $wfecha_tempo = $row5[0] ; 
					       
				       }

					
					echo "<td colspan=4 align=right><font size=".$wtamlet.">Fecha : ".$wfecha_tempo."</font></td></tr>";

					echo "<tr><td colspan=8><font size=".$wtamlet.">Factura de Venta Nro: ".$wnrofac."</font></td></tr>";
					if (isset($nocopia))
					    { }
					else{

					if ($wnrorec > 0)
					{
				      if($wemp_pmla!='06')
					  {
							echo "<tr><td colspan=8><font size=".$wtamlet.">Recibo Nro: ".$wnrorec."     Venta Nro: ".$wnrovta."</font></td></tr>";
					  }
					}
					else
					{
				         if($wemp_pmla!='06')
						{
						 echo "<tr><td colspan=8><font size=".$wtamlet.">Venta Nro: ".$wnrovta."</font></td></tr>";
						}
					}
					}
				    /////////////////////////////////////////////////////////////////////////////////////////////////
				    //Aca averiguo si el responsable de la cuenta es una empresa de promotora, osea por nomina
				    $q = "SELECT temche, temdes "
				        ."  FROM ".$wbasedato."_000016, ".$wbasedato."_000029 "
				        ." WHERE vennum = '".$wnrovta."'"
				        ."   AND mid(ventcl,1,instr(ventcl,'-')-1) = temcod ";
				    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$num = mysql_num_rows($res);

					if ($num > 0)
					   {
					    $row = mysql_fetch_array($res);
					    if ($row[0] == 'on')
					       echo "<tr><td colspan=8><font size=".$wtamlet.">Tipo de Responsable: ".$row[1]."</font></td></tr>";
				       }

				    echo "<tr><td colspan=8><font size=".$wtamlet.">Responsable: ".$wempresa[2]."</font></td></tr>";
					echo "<tr><td colspan=8><font size=".$wtamlet.">Cédula/Nit: ".$wempresa[1]."</font></td></tr>";

					echo "<tr><td colspan=8><font size=".$wtamlet.">Beneficiario: ".$wnompac."</font></td></tr>";
					echo "<tr><td colspan=8><font size=".$wtamlet.">Cédula/Nit: ".$wdocpac."</font></td></tr>";

					$q = "SELECT vmppro "
					    ."  FROM ".$wbasedato."_000050 "
					    ." WHERE vmpvta = '".$wnrovta."'";
					$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$num = mysql_num_rows($res);

					if ($num > 0)
					   {
						$row = mysql_fetch_array($res);
					    $wprograma=$row[0];

					    echo "<tr><td colspan=8><font size=".$wtamlet.">Programa: ".$wprograma."</font></td></tr>";
				       }

					//Si es un domicilio imprimo la dirección y el telefono del cliente
					if ($wtipven=="Domicilio")
					   {
						echo "<tr><td colspan=8><font size=".$wtamlet.">Dirección: ".$wdirpac."</font></td></tr>";
					    echo "<tr><td colspan=8><font size=".$wtamlet.">Telefono: ".$wtelpac."</font></td></tr>";
					   }

					if(isset($wlotven) && $wlotven=='on' && $wtipcli!="01-PARTICULAR")
					{
						if(imprimir_cum())
							$colspandes = '';
						else
							$colspandes = '2';
						$wtamletart = 6;
						$widthart = '80';
					}
					else
					{
						if(imprimir_cum())
							$colspandes = ' colspan=3';
						else
							$colspandes = ' colspan=4';

						$wtamletart = 7;

						if(imprimir_cum())
							$widthart = '170';
						else
							$widthart = '45%';
					}

					echo "<tr><td colspan=8>&nbsp</td></tr>";
					if(imprimir_cum())
						echo "<th width='70' align=left bgcolor=DDDDDD><font size=".$wtamlet.">CUM</font></th>";
					echo "<th width='".$widthart."' align=left bgcolor=DDDDDD".$colspandes."><font size=".$wtamlet.">Descripción</font></th>";
					if(isset($wlotven) && $wlotven=='on' && $wtipcli!="01-PARTICULAR")
					{
						echo "<th width='80' align=left bgcolor=DDDDDD><font size=".$wtamlet."># Lote</font></th>";
						echo "<th width='120' align=left bgcolor=DDDDDD><font size=".$wtamlet.">Fec. Vmto</font></th>";
					}
					echo "<th width='70' align='center' bgcolor=DDDDDD><font size=".$wtamlet.">V/U</font></th>";
					echo "<th width='60' align='center' bgcolor=DDDDDD><font size=".$wtamlet.">Cant</font></th>";
					echo "<th width='60' align='center' bgcolor=DDDDDD><font size=".$wtamlet.">% Iva</font></th>";
					echo "<th align=rigth width='70' bgcolor=DDDDDD><font size=".$wtamlet.">Total</font></th>";

					//ACA TRAIGO EL ENCABEZADO DE LA VENTA
					$q = " SELECT venvto, venviv, vencop, vencmo, vendes, venrec "
					    ."   FROM ".$wbasedato."_000016 "
					    ."  WHERE vennum = '".$wnrovta."'";
					$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$num = mysql_num_rows($res);

					if ($num > 0)
					  {
					   $row = mysql_fetch_array($res);
					   $wtotal=$row[0];
					   $wtotiva=$row[1];
					   $wtotcopcmo=$row[2]+$row[3];
					   $wtotdes=$row[4];
					   $wtotrec=$row[5];
					  }

					//ACA TRAIGO TODO EL DETALLE DE LA VENTA
					$q = " SELECT vdeart, artnom, unides, vdecan, vdevun, vdepiv, (vdevun*vdecan)*(vdepiv/100), (vdevun*vdecan), vdedes, artcna, vdelot, vdefve "
					    ."   FROM ".$wbasedato."_000017, ".$wbasedato."_000001, ".$wbasedato."_000002 "
					    ."  WHERE vdenum = '".$wnrovta."'"
						."    AND vdeart = artcod "
					    ."    AND mid(artuni,1,locate('-',artuni)-1) = unicod ";

					$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$num = mysql_num_rows($res);

					if ($num > 0)
					 {
				      $wtotvenexclui=0;
					  $wtotvenconiva=0;
					  $wtotdes=0;
					  for ($i=1;$i<=$num;$i++)
					     {
					      $row = mysql_fetch_array($res);

						  $wordsize = 17;
						  if(imprimir_cum())
							$wordsize = 14;

						  $articulo = prepararCadena($row[1],$wordsize);

					      echo "<tr>";
	  					  if(imprimir_cum())
							echo "<td width='70' align=left><font size=".$wtamletart.">".$row['artcna']."</font></td>";                             //CUM

						  if ($row[8] > 0)   //Si tiene descuento el articulo
					         echo "<td align=left".$colspandes." width='".$widthart."'><font size=".$wtamletart.">** ".substr($articulo,0,21)."</font></td>";           //Descripcion
					        else
					           echo "<td align=left".$colspandes." width='".$widthart."'><font size=".$wtamletart.">".$articulo."</font></td>";            //Descripcion
					           //On
					           //echo "<td align=left><font size=".$wtamletart.">".substr($row[1],0,21)."</font></td>";            //Descripcion

						  if($row['vdefve']!='0000-00-00')
							$fecven = $row['vdefve'];
						  else
							$fecven = '';
						  if(isset($wlotven) && $wlotven=='on' && $wtipcli!="01-PARTICULAR")
						  {
							echo "<td align=left width='60'><font size=".$wtamletart.">".$row['vdelot']."</font></td>";                             //# Lote
							echo "<td align='center' width='80'><font size=".$wtamletart.">".$fecven."</font></td>";                             //Fecha de vencimiento
						  }

					      echo "<td width='70' align=right><font size=".$wtamletart.">".number_format($row[4],0,'.',',')."</font></td>";    //Valor unitario con IVA
					      echo "<td width='60' align='center'><font size=".$wtamletart.">".$row[3]."</font></td>";                             //Cantidad
					      echo "<td width='60' align='center'><font size=".$wtamletart.">".$row[5]."</font></td>";                             //% IVA
					      echo "<td width='70' align=right><font size=".$wtamletart.">".number_format(($row[7]),0,'.',',')."</font></td>";  //Valor total con iva
					      echo "</tr>";

					      $wtotvenconiva=$wtotvenconiva+$row[7];
					      $wtotdes=$wtotdes+($row[8] + round(($row[8]*($row[5]/100))));


					      if ($row[5] == 0)     //Iva cero (0) entonces es excuido
					         $wtotvenexclui=$wtotvenexclui+$row[7];
					     }

					  if ($wtotvenexclui > 0)
					     $wtotvenexclui=$wtotvenexclui-$wtotdes;


					  echo "<tr><td colspan=7>&nbsp</td>";
					  echo "<td align=right>-------------------</td></tr>";

					  echo "<tr><td colspan=6><font size=".$wtamlet."><b>Sub-Total</b></font></td>";
				      echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format($wtotvenconiva,0,'.',',')."</font></td></tr>";                  //Total venta con IVA

				      echo "<tr><td colspan=7><font size=".$wtamlet."><b>** Descuento: </b></font></td>";
					  echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format(round($wtotdes),0,'.',',')."</font></td></tr>";       //Descuento antes de IVA

				      if ($wtotrec > 0)  //Si tiene recargo lo imprimo
					     {
						  echo "<tr><td colspan=7><font size=".$wtamlet.">Recargo: </font></td>";
					      echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format($wtotrec,0,'.',',')."</font></td></tr>";          //Recargo
				         }

				      echo "<TR><TD colspan=8 align='center'>_________________________________________________________________________________________________________</TD></TR>";

				      echo "<tr><td colspan=6><font size=".$wtamlet."><b>Valor Total</b></font></td>";
				      echo "<td align=right colspan=2><font size=".$wtamlet."><b>".number_format(($wtotal),0,'.',',')."</b></font></td></tr>";      //Total a pagar Neto


					//-------------




 					  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				      // RESUMEN DE IVA
				      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				      echo "<tr><td colspan=8>&nbsp</td></tr>";
				      echo "<tr><td colspan=8>&nbsp</td></tr>";
				      echo "<tr>";
				      echo "<th bgcolor=DDDDDD align='CENTER' colspan=8><font size=".$wtamlet.">RESUMEN DE IVA</font></th>";
				      echo "</tr>";

				      echo "<th bgcolor=DDDDDD align=LEFT colspan=4><font size=".$wtamlet."><b>Tipo</b></font></th>";
					  echo "<th bgcolor=DDDDDD align=right colspan=2><font size=".$wtamlet."><b>Compra</b></font></th>";
					  echo "<th bgcolor=DDDDDD>&nbsp</th>";
					  echo "<th bgcolor=DDDDDD align=right><font size=".$wtamlet."><b>IVA</b></font></th>";

					  //GRAVADO
					  echo "<tr>";
				      echo "<td colspan=4><font size=".$wtamlet."><b>Gravado</b></font></td>";
				      $wgravado=$wtotal-$wtotiva-$wtotvenexclui;
				      echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format(($wgravado),0,'.',',')."</font></td>";  //Total Gravado
					  echo "<td align=right>&nbsp</td>";
				      echo "<td align=right><font size=".$wtamlet.">".number_format(($wtotiva),0,'.',',')."</font></td>";                         //Total Iva
					  echo "</tr>";

					  //EXCLUIDO
					  echo "<tr>";
				      echo "<td colspan=4><font size=".$wtamlet."><b>Excluido</b></font></td>";
				      echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format($wtotvenexclui,0,'.',',')."</font></td>";  //Total Excluido
					  echo "<td align=right>&nbsp</td>";  //Total Gravado
				      echo "<td align=right><font size=".$wtamlet.">".number_format(0,0,'.',',')."</font></td>";               //Total Iva
					  echo "</tr>";

					  echo "<TR><TD colspan=8 align='center'>_________________________________________________________________________________________________________</TD></TR>";

					  //TOTALES
					  echo "<tr>";
				      echo "<td colspan=4><font size=".$wtamlet."><b>Total</b></font></td>";
				      echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format(($wgravado+$wtotvenexclui),0,'.',',')."</font></td>";  //Total Gravado
					  echo "<td align=right>&nbsp</td>";  //Total Gravado
				      echo "<td align=right><font size=".$wtamlet.">".number_format(($wtotiva),0,'.',',')."</font></td>";                         //Total Iva
					  echo "</tr>";

					  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

					  //Aca la cantidad de formas de pago que tiene el recibo de caja
				      $q = " SELECT count(*) "
					      ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022 "
					      ."  WHERE rdenum = ".$wnrorec
					      ."    AND rdevta = '".$wnrovta."'"
					      ."    AND rdefue = rfpfue "
					      ."    AND rdenum = rfpnum "
					      ."    AND rdeest = 'on' "
					      ."    AND rfpest = 'on' "
					      ."    AND rdecco = '".$wcco."'"
					      ."    AND rdecco = rfpcco ";
					  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					  $num = mysql_num_rows($res);
					  $row = mysql_fetch_array($res);
					  $fk=$row[0];

					   if (isset($nocopia))
					   { $fk=0;}

					  if ($fk > 0)
					     {
						  //ACA TRAIGO LOS DATOS NECESARIOS PARA IMPRIMIR LAS FORMAS DE PAGO
						  $q = " SELECT rfpfpa, rfpdan, rfpobs, rfpvfp, fpades "
					          ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022, ".$wbasedato."_000023 "
					          ."  WHERE rdenum = ".$wnrorec
					          ."    AND rdevta = '".$wnrovta."'"
					          ."    AND rdefue = rfpfue "
					          ."    AND rdenum = rfpnum "
					          ."    AND rdeest = 'on' "
					          ."    AND rfpest = 'on' "
					          ."    AND rfpfpa = fpacod "
					          ."    AND rdecco = '".$wcco."'"
					          ."    AND rdecco = rfpcco ";

					      $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					      $num = mysql_num_rows($res);

					      for ($i=1;$i<=$num;$i++)
						      {
							   $row = mysql_fetch_array($res);

							   $wfpa[$i] = $row[0];
							   $wdocane[$i] = $row[1];
							   $wobsrec[$i] = $row[2];
							   $wvalfpa[$i] = $row[3];
							   $wfpades[$i] = $row[4];
						      }
					     }

				      if ($fk > 0)
				        {
						 echo "<tr><td colspan=8>&nbsp</td></tr>";
				         if ($wcuotamod > 0)
					        {
					         echo "<tr><td colspan=7><font size=".$wtamlet.">Cuota Mod. o Franquicia: </font></td>";
					         echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format($wtotcopcmo,0,'.',',')."</font></td></tr>";
					        }

					     echo "<TR><TD colspan=8 align='center'>_________________________________________________________________________________________________________</TD></TR>";
					     //FORMA DE PAGO
					     echo "<tr><td colspan=8>&nbsp</td></tr>";
						 echo "<tr><td colspan=8>";

						 if($wemp_pmla!='06')
						 {
						 echo "<table width='100%'>";
					     echo "<th bgcolor=DDDDDD align=left width='40%'><font size=".$wtamlet.">FORMA DE PAGO</font></th>";
					     echo "<th bgcolor=DDDDDD align=right width='25%'><font size=".$wtamlet.">Doc.Anexo</font></th>";
					     echo "<th bgcolor=DDDDDD align=right width='25%'><font size=".$wtamlet.">Observ.</font></th>";
					     echo "<th bgcolor=DDDDDD align=right width='10%'><font size=".$wtamlet.">Valor</font></th>";

					     for ($i=1;$i<=$fk;$i++)
					        {
						     if ($wvalfpa[$i] > 0)
						        {
						         echo "<tr>";
						         echo "<td align=left width='40%'><font size=".($wtamlet-1).">".$wfpa[$i]." - ".$wfpades[$i]."</font></td>";
						         echo "<td align=right width='25%'><font size=".$wtamlet.">".$wdocane[$i]."</font></td>";
						         echo "<td align=right width='25%'><font size=".$wtamlet.">".$wobsrec[$i]."</font></td>";
						         echo "<td align=right width='10%'><font size=".$wtamlet.">".number_format($wvalfpa[$i],0,'.',',')."</font></td>";
						         echo "</tr>";
					            }
					        }
						 echo "</table>";
					    }
						 echo "</td></tr>";
					     echo "<tr><td colspan=8>&nbsp</td></tr>";
					    }
					  echo "<TR><TD colspan=8 align='center'>_________________________________________________________________________________________________________</TD></TR>";
				      echo "<tr>";
				      echo "<td colspan=6><font size=3>Vend: ".$wcodusu." - ".$wnomusu."</font></td>";
				      echo "<td colspan=2><font size=3>Caja: ".$wcaja."</font></td>";

				      //Si es un domicilio imprimo el mensajero
				      if ($wtipven=="Domicilio")
					     echo "<tr><td colspan=8><font size=3>Mensajero: ".$wmensajero."</font></td></tr>";

					  if ($wtipcli != "01-PARTICULAR")
		                 {
			              echo "<tr><td colspan=8>&nbsp</td></tr>";
			              echo "<tr><td colspan=8>&nbsp</td></tr>";
			              echo "<tr><td colspan=8>&nbsp</td></tr>";
			              echo "<TR><TD colspan=8 align='center'>_____________________________________________________</TD></TR>";
		                  echo "<tr><td colspan=8 align='center'><font size=".$wtamlet.">Firma del Cliente</font></td></tr>";
	                     }

	                  //Traigo la resolucion con la que salio la factura
				      $q = " SELECT fenrln "
				          ."   FROM ".$wbasedato."_000018 "
				          ."  WHERE fenffa = '".$wfuefac."'"
				          ."    AND fenfac = '".$wnrofac."'";
				      $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					  $num = mysql_num_rows($res);

					  if ($num > 0)
					     {
					      $row = mysql_fetch_array($res);
					      $wresol=$row[0];
				         }
				        else
				           $wresol="";

				      echo "<tr><td colspan=8 align='center'>&nbsp</td></tr>";
				      echo "<tr><td colspan=8 align='center'><font size=".$wtamlet.">".$wresol."</font></td></tr>";
				      echo "<tr><td colspan=8 align='center'>&nbsp</td></tr>";
				      echo "<tr><td colspan=8 align='center'>&nbsp</td></tr>";


				      /*
				      echo "<tr><td colspan=5>&nbsp</td></tr>";
				      echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">Resolución Nro: ".$wnrores." del ".$wfecres.", </font></td></tr>";
				      echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">factura ".$wfacini." a la factura ".$wfacfin.".</font></td></tr>";
				      echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">Esta factura de venta se asimila en </font></td></tr>";
				      echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">todos sus efectos a una letra de cambio, Art. 621 y </font></td></tr>";
				      echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">SS, 671 y SS 772, 773, 770 y SS del código de comercio.</font></td></tr>";
				      echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">Factura impresa por computador cumpliendo con los</font></td></tr>";
				      echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">requisitos del Art. 617 del E.T.</font></td></tr>";
				      echo "<tr><td colspan=5 align='center'>&nbsp</td></tr>";
				      echo "<tr><td colspan=5 align='center'>&nbsp</td></tr>";
				      */


				      echo "<tr>";
				      if ($wpagintern != "" and $wpagintern != "NO APLICA")    //Pagina de Internet
				         echo "<tr><td colspan=8 align='center'><font size=".$wtamlet."><b>Visitenos en: ".$wpagintern."</b></font></td></tr>";
				      if ($wemail_pos != "" and $wemail_pos != "NO APLICA")    //Email
				         echo "<tr><td colspan=8 align='center'><font size=".$wtamlet."><b>Escribanos a: ".$wemail_pos."</b></font></td></tr>";
				      if ($wteldompos != "" and $wteldompos != "NO APLICA")    //Telefono domicilio
				         echo "<tr><td colspan=8 align='center'><font size=".$wtamlet."><b>Para sus domicilios comuniquese al ".$wteldompos."</b></font></td></tr>";
				      echo "<tr>";
				     }
				   }
				  else  //No se genero factura
				     {

					  //echo "esta";
					  //if ($wcuotamod > 0)
					  //  {
						 //IMPRESION CON EL LOGO AL PRINCIPIO
						 echo "<tr><td align='center' colspan=8><img src='/matrix/images/medical/ips/logo_".$wbasedato.".png' WIDTH=510 HEIGHT=200></td></tr>";
						 echo "<tr><td colspan=8 align='center'><font size=6>".$wnomemppos."</font></td></tr>";
						 echo "<tr><td colspan=8 align='center'><font size=".$wtamlet.">Nit. : ".$wnit_pos."</font></td></tr>";
						 echo "<tr><td colspan=8 align='center'><font size=".$wtamlet.">Tel.: ".$wtel_pos." Dir.: ".$wdir_pos."</font></td></tr>";
						 echo "<tr><td colspan=8 align='center'><font size=".$wtamlet.">".$wtipregiva."</font></td></tr>";
						 echo "<tr><td colspan=6 align=left><font size=".$wtamlet.">Hora: ".$whora_tempo."</font></td>";
						 echo "<td colspan=2 align=right><font size=".$wtamlet.">Fecha : ".$wfecha_tempo."</font></td></tr>";
						if (isset($nocopia))
					    { }
						else{
					     if ($wnrorec > 0)
						  {

					       // echo "<tr><td colspan=7><font size=".$wtamlet.">Recibo Nro: ".$wnrorec."     Venta Nro: ".$wnrovta."</font></td></tr>";
					        echo "<tr><td colspan=7><font size=".$wtamlet.">Venta Nro: ".$wnrovta."</font></td></tr>";
						  }
						  else
						  {
					          echo "<tr><td colspan=7><font size=".$wtamlet.">Venta Nro: ".$wnrovta."</font></td></tr>";
						  }
						}
					     /////////////////////////////////////////////////////////////////////////////////////////////////
					     //Aca averiguo si el responsable de la cuenta es una empresa de promotora, osea por nomina
					     $q = "SELECT temche, temdes "
					         ."  FROM ".$wbasedato."_000016, ".$wbasedato."_000029 "
					         ." WHERE vennum = '".$wnrovta."'"
					         ."   AND mid(ventcl,1,instr(ventcl,'-')-1) = temcod ";
					     $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						 $num = mysql_num_rows($res);
						 if ($num > 0)
						    {
						     $row = mysql_fetch_array($res);
						     if ($row[0] == 'on')
						        echo "<tr><td colspan=8><font size=".$wtamlet.">Tipo de Responsable: ".$row[1]."</font></td></tr>";
					        }

						 echo "<tr><td colspan=8><font size=".$wtamlet.">Responsable: ".$wempresa[2]."</font></td></tr>";
						 echo "<tr><td colspan=8><font size=".$wtamlet.">Cédula/Nit: ".$wempresa[1]."</font></td></tr>";

						 echo "<tr><td colspan=8><font size=".$wtamlet.">Beneficiario: ".$wnompac."</font></td></tr>";
						 echo "<tr><td colspan=8><font size=".$wtamlet.">Cédula/Nit: ".$wdocpac."</font></td></tr>";

						 //AVERIGUO SI EL CLIENTE ESTA AFILIADO A ALGUN PROGRAMA
						 $q = "SELECT vmppro "
			                 ."  FROM ".$wbasedato."_000050 "
						     ." WHERE vmpvta = '".$wnrovta."'";
						 $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						 $num = mysql_num_rows($res);
						  if ($num > 0)
						     {
							  $row = mysql_fetch_array($res);
						      $wprograma=$row[0];
							  if($wemp_pmla !='06')
							  {
								echo "<tr><td colspan=7><font size=".$wtamlet.">Programa: ".$wprograma."</font></td></tr>";
					          }
							 }



                        if($wemp_pmla=='06')
						{
								if(isset($wlotven) && $wlotven=='on' && $wtipcli!="01-PARTICULAR")
								{
									if(imprimir_cum())
										$colspandes = '';
									else
										$colspandes = '2';
									$wtamletart = 6;
									$widthart = '80';
								}
								else
								{
									if(imprimir_cum())
										$colspandes = ' colspan=3';
									else
										$colspandes = ' colspan=4';

									$wtamletart = 7;

									if(imprimir_cum())
										$widthart = '170';
									else
										$widthart = '45%';
								}

								echo "<tr><td colspan=8>&nbsp</td></tr>";
								if(imprimir_cum())
									echo "<th width='70' align=left bgcolor=DDDDDD><font size=".$wtamlet.">CUM</font></th>";
								echo "<th width='".$widthart."' align=left bgcolor=DDDDDD".$colspandes."><font size=".$wtamlet.">Descripción</font></th>";
								if(isset($wlotven) && $wlotven=='on' && $wtipcli!="01-PARTICULAR")
								{
									echo "<th width='80' align=left bgcolor=DDDDDD><font size=".$wtamlet."># Lote</font></th>";
									echo "<th width='120' align=left bgcolor=DDDDDD><font size=".$wtamlet.">Fec. Vmto</font></th>";
								}

								echo "<th width='70' align='center' bgcolor=DDDDDD><font size=".$wtamlet.">V/U</font></th>";
								echo "<th width='60' align='center' bgcolor=DDDDDD><font size=".$wtamlet.">Cant</font></th>";
								echo "<th width='60' align='center' bgcolor=DDDDDD><font size=".$wtamlet.">% Iva</font></th>";
								echo "<th align=rigth width='70' bgcolor=DDDDDD><font size=".$wtamlet.">Total</font></th>";


								//ACA TRAIGO EL ENCABEZADO DE LA VENTA
								$q = " SELECT venvto, venviv, vencop, vencmo, vendes, venrec "
									."   FROM ".$wbasedato."_000016 "
									."  WHERE vennum = '".$wnrovta."'";
								$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
								$num = mysql_num_rows($res);

								if ($num > 0)
								  {
								   $row = mysql_fetch_array($res);
								   $wtotal=$row[0];
								   $wtotiva=$row[1];
								   $wtotcopcmo=$row[2]+$row[3];
								   $wtotdes=$row[4];
								   $wtotrec=$row[5];
								  }

								//ACA TRAIGO TODO EL DETALLE DE LA VENTA
								$q = " SELECT vdeart, artnom, unides, vdecan, vdevun, vdepiv, (vdevun*vdecan)*(vdepiv/100), (vdevun*vdecan), vdedes, artcna, vdelot, vdefve "
									."   FROM ".$wbasedato."_000017, ".$wbasedato."_000001, ".$wbasedato."_000002 "
									."  WHERE vdenum = '".$wnrovta."'"
									."    AND vdeart = artcod "
									."    AND mid(artuni,1,locate('-',artuni)-1) = unicod ";

								$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
								$num = mysql_num_rows($res);

								if ($num > 0)
								 {
								  $wtotvenexclui=0;
								  $wtotvenconiva=0;
								  $wtotdes=0;
								  for ($i=1;$i<=$num;$i++)
									 {
									  $row = mysql_fetch_array($res);

									  $wordsize = 17;
									  if(imprimir_cum())
										$wordsize = 14;

									  $articulo = prepararCadena($row[1],$wordsize);

									  echo "<tr>";
									  if(imprimir_cum())
										echo "<td width='70' align=left><font size=".$wtamletart.">".$row['artcna']."</font></td>";                             //CUM

									  if ($row[8] > 0)   //Si tiene descuento el articulo
										 echo "<td align=left".$colspandes." width='".$widthart."'><font size=".$wtamletart.">** ".substr($articulo,0,21)."</font></td>";           //Descripcion
										else
										   echo "<td align=left".$colspandes." width='".$widthart."'><font size=".$wtamletart.">".$articulo."</font></td>";            //Descripcion
										   //On
										   //echo "<td align=left><font size=".$wtamletart.">".substr($row[1],0,21)."</font></td>";            //Descripcion

									  if($row['vdefve']!='0000-00-00')
										$fecven = $row['vdefve'];
									  else
										$fecven = '';
									  if(isset($wlotven) && $wlotven=='on' && $wtipcli!="01-PARTICULAR")
									  {
										echo "<td align=left width='60'><font size=".$wtamletart.">".$row['vdelot']."</font></td>";                             //# Lote
										echo "<td align='center' width='80'><font size=".$wtamletart.">".$fecven."</font></td>";                             //Fecha de vencimiento
									  }

									  echo "<td width='70' align=right><font size=".$wtamletart.">".number_format($row[4],0,'.',',')."</font></td>";    //Valor unitario con IVA
									  echo "<td width='60' align='center'><font size=".$wtamletart.">".$row[3]."</font></td>";                             //Cantidad
									  echo "<td width='60' align='center'><font size=".$wtamletart.">".$row[5]."</font></td>";                             //% IVA
									  echo "<td width='70' align=right><font size=".$wtamletart.">".number_format(($row[7]),0,'.',',')."</font></td>";  //Valor total con iva
									  echo "</tr>";

									  $wtotvenconiva=$wtotvenconiva+$row[7];
									  $wtotdes=$wtotdes+($row[8] + round(($row[8]*($row[5]/100))));


									  if ($row[5] == 0)     //Iva cero (0) entonces es excuido
										 $wtotvenexclui=$wtotvenexclui+$row[7];
									 }

								  if ($wtotvenexclui > 0)
									 $wtotvenexclui=$wtotvenexclui-$wtotdes;


								  echo "<tr><td colspan=7>&nbsp</td>";
								  echo "<td align=right>-------------------</td></tr>";

								  echo "<tr><td colspan=6><font size=".$wtamlet."><b>Sub-Total</b></font></td>";
								  echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format($wtotvenconiva,0,'.',',')."</font></td></tr>";                  //Total venta con IVA

								  echo "<tr><td colspan=7><font size=".$wtamlet."><b>** Descuento: </b></font></td>";
								  echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format(round($wtotdes),0,'.',',')."</font></td></tr>";       //Descuento antes de IVA

								  if ($wtotrec > 0)  //Si tiene recargo lo imprimo
									 {
									  echo "<tr><td colspan=7><font size=".$wtamlet.">Recargo: </font></td>";
									  echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format($wtotrec,0,'.',',')."</font></td></tr>";          //Recargo
									 }

								  echo "<TR><TD colspan=8 align='center'>_________________________________________________________________________________________________________</TD></TR>";

								  echo "<tr><td colspan=6><font size=".$wtamlet."><b>Valor Total</b></font></td>";
								  echo "<td align=right colspan=2><font size=".$wtamlet."><b>".number_format(($wtotal),0,'.',',')."</b></font></td></tr>";      //Total a pagar Neto


								//-------------




								  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
								  // RESUMEN DE IVA
								  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
								  /*
								  echo "<tr><td colspan=8>&nbsp</td></tr>";
								  echo "<tr><td colspan=8>&nbsp</td></tr>";
								  echo "<tr>";
								  echo "<th bgcolor=DDDDDD align='CENTER' colspan=8><font size=".$wtamlet.">RESUMEN DE IVA</font></th>";
								  echo "</tr>";

								  echo "<th bgcolor=DDDDDD align=LEFT colspan=4><font size=".$wtamlet."><b>Tipo</b></font></th>";
								  echo "<th bgcolor=DDDDDD align=right colspan=2><font size=".$wtamlet."><b>Compra</b></font></th>";
								  echo "<th bgcolor=DDDDDD>&nbsp</th>";
								  echo "<th bgcolor=DDDDDD align=right><font size=".$wtamlet."><b>IVA</b></font></th>";

								  //GRAVADO
								  echo "<tr>";
								  echo "<td colspan=4><font size=".$wtamlet."><b>Gravado</b></font></td>";
								  $wgravado=$wtotal-$wtotiva-$wtotvenexclui;
								  echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format(($wgravado),0,'.',',')."</font></td>";  //Total Gravado
								  echo "<td align=right>&nbsp</td>";
								  echo "<td align=right><font size=".$wtamlet.">".number_format(($wtotiva),0,'.',',')."</font></td>";                         //Total Iva
								  echo "</tr>";

								  //EXCLUIDO
								  echo "<tr>";
								  echo "<td colspan=4><font size=".$wtamlet."><b>Excluido</b></font></td>";
								  echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format($wtotvenexclui,0,'.',',')."</font></td>";  //Total Excluido
								  echo "<td align=right>&nbsp</td>";  //Total Gravado
								  echo "<td align=right><font size=".$wtamlet.">".number_format(0,0,'.',',')."</font></td>";               //Total Iva
								  echo "</tr>";

								  echo "<TR><TD colspan=8 align='center'>_________________________________________________________________________________________________________</TD></TR>";

								  //TOTALES
								  echo "<tr>";
								  echo "<td colspan=4><font size=".$wtamlet."><b>Total</b></font></td>";
								  echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format(($wgravado+$wtotvenexclui),0,'.',',')."</font></td>";  //Total Gravado
								  echo "<td align=right>&nbsp</td>";  //Total Gravado
								  echo "<td align=right><font size=".$wtamlet.">".number_format(($wtotiva),0,'.',',')."</font></td>";                         //Total Iva
								  echo "</tr>";
								  */
								  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

								  //Aca la cantidad de formas de pago que tiene el recibo de caja
								  $q = " SELECT count(*) "
									  ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022 "
									  ."  WHERE rdevta = '".$wnrovta."'"
									  ."    AND rdefue = rfpfue "
									  ."    AND rdenum = rfpnum "
									  ."    AND rdeest = 'on' "
									  ."    AND rfpest = 'on' "
									  ."    AND rdecco = '".$wcco."'"
									  ."    AND rdecco = rfpcco ";
								  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
								  $num = mysql_num_rows($res);
								  $row = mysql_fetch_array($res);
								  $fk=$row[0];

								   if (isset($nocopia))
								   { $fk=0;}

								  if ($fk > 0)
									 {
									  //ACA TRAIGO LOS DATOS NECESARIOS PARA IMPRIMIR LAS FORMAS DE PAGO
									  // $q = " SELECT rfpfpa, rfpdan, rfpobs, rfpvfp, fpades "
										  // ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022, ".$wbasedato."_000023 "
										  // ."  WHERE rdenum = ".$wnrorec
										  // ."    AND rdevta = '".$wnrovta."'"
										  // ."    AND rdefue = rfpfue "
										  // ."    AND rdenum = rfpnum "
										  // ."    AND rdeest = 'on' "
										  // ."    AND rfpest = 'on' "
										  // ."    AND rfpfpa = fpacod "
										  // ."    AND rdecco = '".$wcco."'"
										  // ."    AND rdecco = rfpcco ";
									   $q = " SELECT rfpfpa, rfpdan, rfpobs, rfpvfp, fpades "
										  ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022, ".$wbasedato."_000023 "
										  ."  WHERE rdevta = '".$wnrovta."'"
										  ."    AND rdefue = rfpfue "
										  ."    AND rdenum = rfpnum "
										  ."    AND rdeest = 'on' "
										  ."    AND rfpest = 'on' "
										  ."    AND rfpfpa = fpacod "
										  ."    AND rdecco = '".$wcco."'"
										  ."    AND rdecco = rfpcco ";

									  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
									  $num = mysql_num_rows($res);

									  for ($i=1;$i<=$num;$i++)
										  {
										   $row = mysql_fetch_array($res);

										   $wfpa[$i] = $row[0];
										   $wdocane[$i] = $row[1];
										   $wobsrec[$i] = $row[2];
										   $wvalfpa[$i] = $row[3];
										   $wfpades[$i] = $row[4];
										  }
									 }

								  if ($fk > 0)
									{
									 echo "<tr><td colspan=8>&nbsp</td></tr>";
									 if ($wcuotamod > 0)
										{
										 echo "<tr><td colspan=7><font size=".$wtamlet.">Cuota Mod. o Franquicia: </font></td>";
										 echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format($wtotcopcmo,0,'.',',')."</font></td></tr>";
										}

									 echo "<TR><TD colspan=8 align='center'>_________________________________________________________________________________________________________</TD></TR>";
									 //FORMA DE PAGO
									 echo "<tr><td colspan=8>&nbsp</td></tr>";
									 echo "<tr><td colspan=8>";
									 echo "<table width='100%'>";
									 echo "<th bgcolor=DDDDDD align=left width='40%'><font size=".$wtamlet.">FORMA DE PAGO</font></th>";
									 echo "<th bgcolor=DDDDDD align=right width='25%'><font size=".$wtamlet.">Doc.Anexo</font></th>";
									 echo "<th bgcolor=DDDDDD align=right width='25%'><font size=".$wtamlet.">Observ.</font></th>";
									 echo "<th bgcolor=DDDDDD align=right width='10%'><font size=".$wtamlet.">Valor</font></th>";
									 $totalabonos = 0;
									 for ($i=1;$i<=$fk;$i++)
										{
										 if ($wvalfpa[$i] > 0)
											{
											 echo "<tr>";
											 echo "<td align=left width='40%'><font size=".($wtamlet-1).">".$wfpa[$i]." - ".$wfpades[$i]."</font></td>";
											 echo "<td align=right width='25%'><font size=".$wtamlet.">".$wdocane[$i]."</font></td>";
											 echo "<td align=right width='25%'><font size=".$wtamlet.">".$wobsrec[$i]."</font></td>";
											 echo "<td align=right width='10%'><font size=".$wtamlet.">".number_format($wvalfpa[$i],0,'.',',')."</font></td>";
											 echo "</tr>";
											 $totalabonos = $totalabonos + $wvalfpa[$i];
											}
										}
									 echo "</table>";
									 echo "</td></tr>";
									 echo "<tr><td colspan=8>&nbsp</td></tr>";
									 echo "<tr><td colspan=8></td></tr>";
									 //echo "<tr><td colspan=7><font size=".($wtamlet-1).">Total Entregado:</font></td><td align=right ><font size=".($wtamlet-1).">".number_format($wabentregado,0,'.',',').".00</font></td></tr>";
									 echo "<tr><td colspan=7><font size=".($wtamlet).">Total Abonado:</font></td><td align=right><font size=".($wtamlet).">".number_format($totalabonos,0,'.',',')."</font></td></tr>";
									 //echo "<tr><td colspan=7 ><font size=".($wtamlet-1).">Cambio:</font></td><td align=right><font size=".($wtamlet-1).">".$wabcambio."</font></td></tr>";
									 echo "<tr><td colspan=7><font size=".($wtamlet).">Saldo:</font></td><td align=right ><font size=".($wtamlet).">".number_format(($wtotal - $totalabonos),0,'.',',')."</font></td></tr>";

									}
								  echo "<TR><TD colspan=8 align='center'>_________________________________________________________________________________________________________</TD></TR>";
								  echo "<tr>";
								  echo "<td colspan=6><font size=3>Vend: ".$wcodusu." - ".$wnomusu."</font></td>";
								  echo "<td colspan=2><font size=3>Caja: ".$wcaja."</font></td>";

								  //Si es un domicilio imprimo el mensajero
								  if ($wtipven=="Domicilio")
									 echo "<tr><td colspan=8><font size=3>Mensajero: ".$wmensajero."</font></td></tr>";

								  if ($wtipcli != "01-PARTICULAR")
									 {
									  echo "<tr><td colspan=8>&nbsp</td></tr>";
									  echo "<tr><td colspan=8>&nbsp</td></tr>";
									  echo "<tr><td colspan=8>&nbsp</td></tr>";
									  echo "<TR><TD colspan=8 align='center'>_____________________________________________________</TD></TR>";
									  echo "<tr><td colspan=8 align='center'><font size=".$wtamlet.">Firma del Cliente</font></td></tr>";
									 }

								  //Traigo la resolucion con la que salio la factura
								  $q = " SELECT fenrln "
									  ."   FROM ".$wbasedato."_000018 "
									  ."  WHERE fenffa = '".$wfuefac."'"
									  ."    AND fenfac = '".$wnrofac."'";
								  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
								  $num = mysql_num_rows($res);

								  if ($num > 0)
									 {
									  $row = mysql_fetch_array($res);
									  $wresol=$row[0];
									 }
									else
									   $wresol="";

								  echo "<tr><td colspan=8 align='center'>&nbsp</td></tr>";
								  echo "<tr><td colspan=8 align='center'><font size=".$wtamlet.">".$wresol."</font></td></tr>";
								  echo "<tr><td colspan=8 align='center'>&nbsp</td></tr>";
								  echo "<tr><td colspan=8 align='center'>&nbsp</td></tr>";


								  /*
								  echo "<tr><td colspan=5>&nbsp</td></tr>";
								  echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">Resolución Nro: ".$wnrores." del ".$wfecres.", </font></td></tr>";
								  echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">factura ".$wfacini." a la factura ".$wfacfin.".</font></td></tr>";
								  echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">Esta factura de venta se asimila en </font></td></tr>";
								  echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">todos sus efectos a una letra de cambio, Art. 621 y </font></td></tr>";
								  echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">SS, 671 y SS 772, 773, 770 y SS del código de comercio.</font></td></tr>";
								  echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">Factura impresa por computador cumpliendo con los</font></td></tr>";
								  echo "<tr><td colspan=5 align='center'><font size=".$wtamlet.">requisitos del Art. 617 del E.T.</font></td></tr>";
								  echo "<tr><td colspan=5 align='center'>&nbsp</td></tr>";
								  echo "<tr><td colspan=5 align='center'>&nbsp</td></tr>";
								  */


								  echo "<tr>";
								  if ($wpagintern != "" and $wpagintern != "NO APLICA")    //Pagina de Internet
									 echo "<tr><td colspan=8 align='center'><font size=".$wtamlet."><b>Visitenos en: ".$wpagintern."</b></font></td></tr>";
								  if ($wemail_pos != "" and $wemail_pos != "NO APLICA")    //Email
									 echo "<tr><td colspan=8 align='center'><font size=".$wtamlet."><b>Escribanos a: ".$wemail_pos."</b></font></td></tr>";
								  if ($wteldompos != "" and $wteldompos != "NO APLICA")    //Telefono domicilio
									 echo "<tr><td colspan=8 align='center'><font size=".$wtamlet."><b>Para sus domicilios comuniquese al ".$wteldompos."</b></font></td></tr>";
								  echo "<tr>";
								 }

						}
						else
						{

						    echo "<tr><td colspan=8>&nbsp</td></tr>";
							echo "<th align=left bgcolor=DDDDDD colspan=7><font size=".$wtamlet.">Descripción</font></th>";
							echo "<th align=RIGHT bgcolor=DDDDDD><font size=".$wtamlet.">Cant.</font></th>";

						 //ACA TRAIGO TODA LA VENTA
						 //$q = " SELECT vdeart, artnom, unides, vdecan, vdevun, vdepiv, (vdevun*vdecan)*(vdepiv/100), (vdevun*vdecan)+((vdevun*vdecan)*(vdepiv/100)) "
						 $q = " SELECT vdeart, artnom, unides, vdecan, vdevun, vdepiv, (vdevun*vdecan)*(vdepiv/100), (vdevun*vdecan) "
						     ."   FROM ".$wbasedato."_000017, ".$wbasedato."_000001, ".$wbasedato."_000002 "
						     ."  WHERE vdenum = '".$wnrovta."'"
						     ."    AND vdeart = artcod "
						     ."    AND mid(artuni,1,locate('-',artuni)-1) = unicod ";

						 $res = mysql_query($q,$conex);
						 $num = mysql_num_rows($res);

						 if ($num > 0)
						   {
						    for ($i=1;$i<=$num;$i++)
						       {
						        $row = mysql_fetch_array($res);

				                echo "<tr>";
						        echo "<td align=left colspan=5><font size=".$wtamlet.">".substr($row[1],0,30)."</font></td>";
						        echo "<td align=right colspan=3><font size=".$wtamlet.">".$row[3]."</font></td>";
						        echo "</tr>";
						       }
					        echo "<TR><TD colspan=8>_________________________________________________________________________________________________________</TD></TR>";

					        //Aca la cantidad de formas de pago que tiene el recibo de caja
					        $q = " SELECT count(*) "
						        ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022 "
						        ."  WHERE rdenum = ".$wnrorec
						        ."    AND rdevta = '".$wnrovta."'"
						        ."    AND rdefue = rfpfue "
						        ."    AND rdenum = rfpnum "
						        ."    AND rdeest = 'on' "
						        ."    AND rfpest = 'on' "
						        ."    AND rdecco = '".$wcco."'"
					            ."    AND rdecco = rfpcco ";
						    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						    $num = mysql_num_rows($res);
						    $row = mysql_fetch_array($res);
						    $fk=$row[0];

						    if ($fk > 0)
						       {
							    //ACA TRAIGO LOS DATOS NECESARIOS PARA IMPRIMIR LAS FORMAS DE PAGO
						        $q = " SELECT rfpfpa, rfpdan, rfpobs, rfpvfp, fpades "
					                ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022, ".$wbasedato."_000023 "
					                ."  WHERE rdenum = ".$wnrorec
					                ."    AND rdevta = '".$wnrovta."'"
					                ."    AND rdefue = rfpfue "
					                ."    AND rdenum = rfpnum "
					                ."    AND rdeest = 'on' "
					                ."    AND rfpest = 'on' "
					                ."    AND rfpfpa = fpacod "
					                ."    AND rdecco = '".$wcco."'"
					                ."    AND rdecco = rfpcco ";

					            $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					            $num = mysql_num_rows($res);

						        for ($i=1;$i<=$fk;$i++)
						          {
							       $row = mysql_fetch_array($res);

	                               $wfpa[$i] = $row[0];
							       $wdocane[$i] = $row[1];
							       $wobsrec[$i] = $row[2];
							       $wvalfpa[$i] = $row[3];
							       $wfpades[$i] = $row[4];
						          }

					            if ($wcuotamod > 0)
							      {
							       echo "<tr><td colspan=7><font size=".$wtamlet.">Cuota Mod. o Franquicia: </font></td>";
							       echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format($wcuotamod,0,'.',',')."</font></td></tr>";
							      }

							    //FORMA DE PAGO
							    echo "<tr><td colspan=8>&nbsp</td></tr>";
							    echo "<th bgcolor=DDDDDD align=left colspan=3><font size=".$wtamlet.">FORMA DE PAGO</font></th>";
							    echo "<th bgcolor=DDDDDD align=right colspan=2><font size=".$wtamlet.">Doc.Anexo</font></th>";
							    echo "<th bgcolor=DDDDDD align=right colspan=2><font size=".$wtamlet.">Observ.</font></th>";
							    echo "<th bgcolor=DDDDDD align=right><font size=".$wtamlet.">Valor</font></th>";

							    for ($i=1;$i<=$fk;$i++)
							       {
							        echo "<tr>";
							        echo "<td align=left colspan=3><font size=".($wtamlet-1).">".$wfpa[$i]."</font></td>";
							        echo "<td align=right colspan=2><font size=".$wtamlet.">".$wdocane[$i]."</font></td>";
							        echo "<td align=right colspan=2><font size=".$wtamlet.">".$wobsrec[$i]."</font></td>";
							        echo "<td align=right><font size=".$wtamlet.">".number_format($wvalfpa[$i],0,'.',',')."</font></td>";
							        echo "</tr>";
							       }
							    //echo "<tr><td colspan=3><font size=".$wtamlet.">Cambio: </font></td>";
							    //echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format($wvalcam,2,'.',',')."</font></td></tr>";
							    echo "<tr><td colspan=8>&nbsp</td></tr>";
							   }
						    echo "<TR><TD colspan=8>_________________________________________________________________________________________________________</TD></TR>";
						    echo "<tr>";
						    echo "<td colspan=8><font size=3>Vend: ".$wcodusu." - ".$wnomusu."</font></td>";
						    echo "<td colspan=8><font size=3>Caja: ".$wcaja."</font></td>";
						    echo "</tr>";

						    echo "<tr>";
				            if ($wpagintern != "" and $wpagintern != "NO APLICA")        //Pagina de Internet
						       echo "<tr><td colspan=8 align='center'><font size=".$wtamlet."><b>Visitenos en: ".$wpagintern."</b></font></td></tr>";
						    if ($wemail_pos != "" and $wemail_pos != "NO APLICA")  //Email
						       echo "<tr><td colspan=8 align='center'><font size=".$wtamlet."><b>Escribanos a: ".$wemail_pos."</b></font></td></tr>";
						    if ($wteldompos != "" and $wteldompos != "NO APLICA")        //Telefono domicilio
			       			   echo "<tr><td colspan=8 align='center'><font size=".$wtamlet."><b>Para sus domicilios comuniquese al ".$wteldompos."</b></font></td></tr>";
				            echo "</tr>";
						   }
					    //}
						}
				     }
				if(isset($nocopia) )
				{
				}
				else
				{
					if ($esventa=='si')
					{

					}
					else
					{
						echo "<TR><TD colspan=8 align=left><b><font size=".($wtamlet-2).">*** COPIA DE FACTURA ***</font></b></TD></TR>";
					}
				}
			 } //Fin del else de num=0, que indica que no existe el documento o esta descuadrado
          }
      }
echo "</table>";
echo "</form>";
?>
