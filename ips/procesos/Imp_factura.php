<?php
include_once("conex.php");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// !!! ATENCION ¡¡¡ SI SE HACE ALGUN CAMBIO EN ESTE PROGRAMA TAMBIEN SE DEBE DE HACER EN EL PROGRAMA copia_factura.php y VICEVERSA
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



$consultaAjax='';
include_once("root/comun.php");




echo "</table>";
echo "<left><table border=0 width='100'>";

$wtamlet=7;   //Tamaño de letra

//=======================================================================
//ACA TRAIGO EL NOMBRE DEL VENDEDOR
$q = " SELECT venusu, descripcion "
    ."   FROM ".$wbasedato."_000016, usuarios "
    ."  WHERE vennum = '".$wnrovta."'"
    ."    AND venusu = codigo ";
$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
$num = mysql_num_rows($res);

if ($num > 0)
   {
	$row = mysql_fetch_array($res);
	$wcodusu = $row[0];
	$wnomusu = $row[1];
   }
//=======================================================================

//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES
//========================================================================================================================================\\
// ENERO 30 DE 2015 Frederick Aguirre
// ________________________________________________________________________________________________________________________________________\\
// Se modifica el encabezado de la factura agregando el texto: SOMOS AUTORRETENEDORES Res. DIAN. No.10653.  03/12/2014
//
//========================================================================================================================================\\
// MAYO 14 DE 2014 Camilo Zapata
// ________________________________________________________________________________________________________________________________________\\
// Se modifica la validación anterior, para que nunca muestre los puntos.
//________________________________________________________________________________________________________________________________________\\
//========================================================================================================================================\\
// ABRIL 14 DE 2014 Camilo Zapata
// ________________________________________________________________________________________________________________________________________\\
// Se Agrega una validación que consulta que el usuario se haya registrado como cliente antes del 7 de abril del 2014, en caso de que no sea \\
// así, no se muestran los puntos en la impresión.
//________________________________________________________________________________________________________________________________________\\                                                                                                                         \\
//========================================================================================================================================\\
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

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//ACA TRAIGO LOS DATOS NECESARIOS PARA IMPRIMIR LA FACTURA DESDE LA TABLA DE CONFIGURACION
$q = " SELECT cfgnit, cfgnom, cfgtre, cfgtel, cfgdir, cfgfran, cfgfian, cfgffan, cfgran, "
    ."        cfgfcar, cfgfrac, cfgfiac, cfgffac, cfgrac, cfgpin, cfgmai, cfgdom, cfgobs, Cfgret "
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
$wobservaciones=$row[17];
$wretenedores=$row[18];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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

function imprimir_cum()
{
	global $wbasedato;
	global $conex;

	$q = " SELECT
				Detval
			FROM
				root_000051, root_000050
			WHERE
				Empbda = '".$wbasedato."'
				AND Empcod = Detemp
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

echo "<tr><td colspan=8>&nbsp</td></tr>";


$wempresa=explode("-",$wempresa);

/////////////////////////////////////
if ($wnrofac != "")
   {
	//IMPRESION CON EL LOGO AL PRINCIPIO
	echo "<tr><td align=center colspan=8><img src='/matrix/images/medical/ips/logo_".$wbasedato.".png' WIDTH=510 HEIGHT=200></td></tr>";
	echo "<tr><td colspan=8 align=center><font size=".($wtamlet+1).">".$wnomemppos."</font></td></tr>";
	echo "<tr><td colspan=8 align=center><font size=".$wtamlet.">Nit. : ".$wnit_pos."</font></td></tr>";
	echo "<tr><td colspan=8 align=center><font size=".$wtamlet.">Tel.: ".$wtel_pos." Dir.: ".$wdir_pos."</font></td></tr>";
	echo "<tr><td colspan=8 align=center><font size=".$wtamlet.">".$wtipregiva."</font></td></tr>";
	if( $wretenedores != "" ) echo "<tr><td colspan=8 align=center><font size=".$wtamlet.">".$wretenedores."</font></td></tr>";
	echo "<tr><td colspan=4 align=left><font size=".$wtamlet.">Hora: ".$whora_tempo."</font></td>";
	echo "<td colspan=4 align=right><font size=".$wtamlet.">Fecha Factura: ".$wfecha_tempo."</font></td></tr>";

	echo "<tr><td colspan=8><font size=".$wtamlet.">Factura de Venta Nro: ".$wnrofac."</font></td></tr>";
	if ($wnrorec > 0)
	{
       echo "<tr><td colspan=4 align=left><font size=".$wtamlet.">Recibo Nro: ".$wnrorec."</font></td><td colspan=4 align=right><font size=".$wtamlet.">Venta Nro: ".$wnrovta."</font></td></tr>";
	}
    else
	{
         if($wemp_pmla == '06')
		 {

		 }
		 else
		 {
			echo "<tr><td colspan=8><font size=".$wtamlet.">Venta Nro: ".$wnrovta."</font></td></tr>";
		 }
	}
    //$wempresa=explode("-",$wnomnit);


    /////////////////////////////////////////////////////////////////////////////////////////////////
    //Aca averiguo si el responsable de la cuenta es una empresa de promotora, osea por nomina
    $q = "SELECT temche, temdes "
        ."  FROM ".$wbasedato."_000016, ".$wbasedato."_000029, ".$wbasedato."_000018 "
        ." WHERE vennum = '".$wnrovta."'"
        ."   AND mid(ventcl,1,instr(ventcl,'-')-1) = temcod "
        ."   AND vennfa = fenfac "
        ."   AND venvto = fenval ";
    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0)
	   {
	    $row = mysql_fetch_array($res);
	    if ($row[0] == 'on')
	       echo "<tr><td colspan=8><font size=".$wtamlet.">Tipo de Responsable: ".$row[1]."</font></td></tr>";
       }

    //===============================================================================================
    //===============================================================================================
    //ACA VALIDO QUE EXISTA LA FACTURA SI num=0, NO EXISTE O ESTA DESCUADRADA !!!!!!!!!!!!!!!!!!!!!!!
    //===============================================================================================
    if ($num > 0)
       {
	    echo "<tr><td colspan=8><font size=".$wtamlet.">Responsable: ".$wempresa[2]."</font></td></tr>";
		echo "<tr><td colspan=8><font size=".$wtamlet.">Cédula/Nit: ".$wempresa[1]."</font></td></tr>";

		echo "<tr><td colspan=8><font size=".$wtamlet.">Beneficiario: ".$wnompac."</font></td></tr>";
		echo "<tr><td colspan=8><font size=".$wtamlet.">Cédula/Nit: ".$wdocpac."</font></td></tr>";

		//Si es un domicilio imprimo la dirección y el telefono del cliente
		if ($wtipven=="Domicilio")
		   {
			echo "<tr><td colspan=8><font size=".$wtamlet.">Dirección: ".$wdirpac."</font></td></tr>";
		    echo "<tr><td colspan=8><font size=".$wtamlet.">Telefono: ".$wte1pac."</font></td></tr>";
		   }

		if (isset($wprograma) and ($wprograma != ""))
		   echo "<tr><td colspan=8><font size=".$wtamlet.">Programa: ".$wprograma."</font></td></tr>";

		if (isset($wprestamo) and ($wprestamo != "") and ($wprestamo > 0))
		   echo "<tr><td colspan=8><font size=".$wtamlet.">Prestamo Nro: ".$wprestamo."</font></td></tr>";

		//-Modificacion para que funcionen todas las empresas igual sin tener que quemar 01-PARTICULAR  en los if siguientes
		// if $wtipcli=="01-PARTICULAR" ya que solo estaba funcionando este para farmastore
		$wtipoEmpresaParticular='01-PARTICULAR';
		//----------

		$wtipoEmpresaParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, "EmpParVentas_nue");
		//------------


		if(isset($wlotven) && $wlotven=='on' && $wtipcli!= $wtipoEmpresaParticular)
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

		echo "<tr><td colspan=8>&nbsp;</td></tr>";
		if(imprimir_cum())
			echo "<th width='70' align=left bgcolor=DDDDDD><font size=".$wtamlet.">CUM</font></th>";
		echo "<th width='".$widthart."' align=left bgcolor=DDDDDD".$colspandes."><font size=".$wtamlet.">Descripción</font></th>";
		if(isset($wlotven) && $wlotven=='on' && $wtipcli != $wtipoEmpresaParticular)
		{
			echo "<th width='80' align=center bgcolor=DDDDDD><font size=".$wtamlet."># Lote</font></th>";
			echo "<th width='120' align=center bgcolor=DDDDDD><font size=".$wtamlet.">Fec. Vmto</font></th>";
		}
		echo "<th width='70' align=center bgcolor=DDDDDD><font size=".$wtamlet.">V/U</font></th>";
		echo "<th width='60' align=center bgcolor=DDDDDD><font size=".$wtamlet.">Cant</font></th>";
		echo "<th width='60' align=center bgcolor=DDDDDD><font size=".$wtamlet.">% Iva</font></th>";
		echo "<th width='70' align=rigth bgcolor=DDDDDD><font size=".$wtamlet.">Total</font></th>";


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
				echo "<td align=left width='70'><font size=".$wtamletart.">".$row['artcna']."</font></td>";                             //CUM
		      if ($row[8] > 0)   //Si tiene descuento el articulo
		         echo "<td align=left".$colspandes." width='".$widthart."'><font size=".$wtamletart.">** ".substr($articulo,0,21)."</font></td>";           //Descripcion
		      else
		         echo "<td align=left".$colspandes." width='".$widthart."'><font size=".$wtamletart.">".$articulo."</font></td>";            //Descripcion
		      //On
		      //echo "<td align=left><font size=".$wtamlet.">".substr($row[1],0,21)."</font></td>";            //Descripcion

			  if($row['vdefve']!='0000-00-00')
				$fecven = $row['vdefve'];
			  else
				$fecven = '';
			  if(isset($wlotven) && $wlotven=='on' && $wtipcli != $wtipoEmpresaParticular)
			  {
				echo "<td align=left width='60'><font size=".$wtamletart.">".$row['vdelot']."</font></td>";                             //# Lote
				echo "<td align=center width='80'><font size=".$wtamletart.">".$fecven."</font></td>";                             //Fecha de vencimiento
		      }
			  echo "<td align=right width='70'><font size=".$wtamletart.">".number_format($row[4],0,'.',',')."</font></td>";    //Valor unitario con IVA
		      echo "<td align='center' width='60'><font size=".$wtamletart.">".$row[3]."</font></td>";                             //Cantidad
		      echo "<td align=center width='60'><font size=".$wtamletart.">".$row[5]."</font></td>";                             //% IVA
		      echo "<td align=right width='70'><font size=".$wtamletart.">".number_format(($row[7]),0,'.',',')."</font></td>";  //Valor total con iva
		      echo "</tr>";

		      $wtotvenconiva=$wtotvenconiva+$row[7];
		      $wtotdes=$wtotdes+($row[8] + ($row[3]*round(($row[8]/$row[3])*($row[5]/100))));


		      if ($row[5] == 0)     //Iva cero (0) entonces es excluido
		         $wtotvenexclui=$wtotvenexclui+$row[7];
		     }

		  if ($wtotvenexclui > 0)
		     $wtotvenexclui=$wtotvenexclui-$wtotdes;

		  echo "<tr><td colspan=7>&nbsp</td>";
		  echo "<td align=right>-------------------</td></tr>";

		  echo "<tr><td colspan=7><font size=".$wtamlet."><b>Sub-Total</b></font></td>";
	      echo "<td align=right><font size=".$wtamlet.">".number_format($wtotvenconiva,0,'.',',')."</font></td></tr>";                  //Total venta con IVA

	      echo "<tr><td colspan=7><font size=".$wtamlet."><b>** Descuento: </b></font></td>";
		  echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format(round($wtotdes),0,'.',',')."</font></td></tr>";              //Descuento antes de IVA

	      if ($wtotrec > 0)  //Si tiene recargo lo imprimo
		     {
			  echo "<tr><td colspan=7><font size=".$wtamlet.">Recargo: </font></td>";
		      echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format($wtotrec,0,'.',',')."</font></td></tr>";          //Recargo
	         }

	      echo "<TR><TD colspan=8 align=center>_________________________________________________________________________________________________________</TD></TR>";

	      echo "<tr><td colspan=6><font size=".$wtamlet."><b>Valor Total</b></font></td>";
	      echo "<td align=right colspan=2><font size=".$wtamlet."><b>".number_format(($wtotal),0,'.',',')."</b></font></td></tr>";             //Total a pagar Neto


	      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      // RESUMEN DE IVA
	      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      echo "<tr><td colspan=8>&nbsp</td></tr>";
	      echo "<tr><td colspan=8>&nbsp</td></tr>";
	      echo "<tr>";
	      echo "<th bgcolor=DDDDDD align=CENTER colspan=8><font size=".$wtamlet.">RESUMEN DE IVA</font></th>";
	      echo "</tr>";

	      echo "<th bgcolor=DDDDDD align=LEFT colspan=4><font size=".$wtamlet."><b>Tipo</b></font></th>";
		  echo "<th bgcolor=DDDDDD align=right colspan=2><font size=".$wtamlet."><b>Compra</b></font></th>";
		  echo "<th bgcolor=DDDDDD align=right>&nbsp</th>";
		  echo "<th bgcolor=DDDDDD align=right><font size=".$wtamlet."><b>IVA</b></font>&nbsp</tr>";

		  //GRAVADO
		  echo "<tr>";
	      echo "<td colspan=4><font size=".$wtamlet."><b>Gravado</b></font></td>";
	      $wgravado=$wtotal-$wtotiva-$wtotvenexclui;
	      echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format(($wgravado),0,'.',',')."</font></td>";     //Total Gravado
		  echo "<td align=right>&nbsp</td>";
	      echo "<td align=right><font size=".$wtamlet.">".number_format(($wtotiva),0,'.',',')."</font></td>";      //Total Iva
		  echo "</tr>";

		  //EXCLUIDO
		  echo "<tr>";
	      echo "<td colspan=4><font size=".$wtamlet."><b>Excluido</b></font></td>";
	      echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format($wtotvenexclui,0,'.',',')."</font></td>";  //Total Excluido
		  echo "<td align=right>&nbsp</td>";
	      echo "<td align=right><font size=".$wtamlet.">".number_format(0,0,'.',',')."</font></td>";               //Total Iva
		  echo "</tr>";

		  echo "<TR><TD colspan=8 align=center>_________________________________________________________________________________________________________</TD></TR>";

		  //TOTALES
		  echo "<tr>";
	      echo "<td colspan=4><font size=".$wtamlet."><b>Total</b></font></td>";
	      echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format(($wgravado+$wtotvenexclui),0,'.',',')."</font></td>";  //Total Gravado
		  echo "<td align=right>&nbsp</td>";
	      echo "<td align=right><font size=".$wtamlet.">".number_format(($wtotiva),0,'.',',')."</font></td>";                  //Total Iva
		  echo "</tr>";

		  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		  // $sqlventaobs= " SELECT  Renobs
							// FROM ".$wbasedato."_000021 ,".$wbasedato."_000020
						   // WHERE rdevta = '".$wnumventa."'
							 // AND Rdenum = Rennum ";

		  // $res1 = mysql_query($sqlventaobs,$conex) or die (mysql_errno()." - ".mysql_error());
		  // $num1 = mysql_num_rows($res1);

		  // if ($num > 0)
		  // {
			  // $row1 	= mysql_fetch_array($res1);
			  // $robser	=$row1[0];
		  // }


		  // echo"<tr><td font size=".$wtamlet." align='center'>Observación:</td></tr>";
		  // echo"<tr><td>".$sqlventaobs."</td></tr>";
	      if ($fk > 0)
	        {
		     echo "<tr><td colspan=8>&nbsp</td></tr>";
	         if ($wcuotamod > 0)
		        {
		         echo "<tr><td colspan=7><font size=".$wtamlet.">Cuota Mod. o Franquicia: </font></td>";
		         echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format($wtotcopcmo,0,'.',',')."</font></td></tr>";
		        }

		     echo "<TR><TD colspan=8 align=center>_________________________________________________________________________________________________________</TD></TR>";
		     //FORMA DE PAGO
		     echo "<tr><td colspan=8>&nbsp</td></tr>";
		     echo "<tr><td colspan=8>";
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
			         echo "<td align=left width='40%'><font size=".$wtamlet.">".$wfpa[$i]."</font></td>";
			         echo "<td align=right width='25%'><font size=".$wtamlet.">".$wdocane[$i]."</font></td>";
			         echo "<td align=right width='25%'><font size=".$wtamlet.">".$wobsrec[$i]."</font></td>";
			         echo "<td align=right width='10%'><font size=".$wtamlet.">".number_format($wvalfpa[$i],0,'.',',')."</font></td>";
			         echo "</tr>";
		            }
		        }
			 if (($wvalcam==''))
			 {
				$wvalcam = 0;
			 }
		     echo "<tr><td colspan=3><font size=".$wtamlet.">Cambio: </font></td>";
		     echo "<td align=right><font size=".$wtamlet.">".@number_format($wvalcam,0,'.',',')."</font></td></tr>";
			 echo "</table>";
		     echo "</td></tr>";
		     echo "<tr><td colspan=8>&nbsp</td></tr>";
		    }
		  echo "<TR><TD colspan=8 align=center>_________________________________________________________________________________________________________</TD></TR>";
	      echo "<tr>";
	      echo "<td colspan=6><font size=3>Vend: ".$wcodusu." - ".$wnomusu."</font></td>";
	      echo "<td colspan=2><font size=3>Caja: ".$wcaja."</font></td>";
	      //echo "<td colspan";

	      //Si es un domicilio imprimo el mensajero
	      if ($wtipven=="Domicilio")
		     echo "<tr><td colspan=8><font size=3>Mensajero: ".$wmensajero."</font></td></tr>";


		  if ($wtipcli != $wtipoEmpresaParticular)
		     {
			  echo "<tr><td colspan=8>&nbsp</td></tr>";
			  echo "<tr><td colspan=8>&nbsp</td></tr>";
			  echo "<tr><td colspan=8>&nbsp</td></tr>";
			  echo "<TR><TD colspan=8 align=center>_____________________________________________________</TD></TR>";
		      echo "<tr><td colspan=8 align=center><font size=".$wtamlet.">Firma del Cliente</font></td></tr>";
	         }

	      //Traigo la resolucion con la que salio la factura
	      $q = " SELECT fenrln "
	          ."   FROM ".$wbasedato."_000018 "
	          ."  WHERE fenffa = '".$wfueffa."'"
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

	      echo "<tr><td colspan=8 align=center>&nbsp</td></tr>";
	      echo "<tr><td colspan=8 align=center><font size=".$wtamlet.">".$wresol."</font></td></tr>";
	      echo "<tr><td colspan=8 align=center>&nbsp</td></tr>";
	      echo "<tr><td colspan=8 align=center>&nbsp</td></tr>";

	      /*
	      echo "<tr><td colspan=5 align=center>&nbsp</td></tr>";
	      echo "<tr><td colspan=5 align=center><font size=".$wtamlet.">Resolución Nro: ".$wnrores." del ".$wfecres.", </font></td></tr>";
	      echo "<tr><td colspan=5 align=center><font size=".$wtamlet.">factura ".$wfacini." a la factura ".$wfacfin.".</font></td></tr>";
	      echo "<tr><td colspan=5 align=center><font size=".$wtamlet.">Esta factura de venta se asimila en </font></td></tr>";
	      echo "<tr><td colspan=5 align=center><font size=".$wtamlet.">todos sus efectos a una letra de cambio, Art. 621 y </font></td></tr>";
	      echo "<tr><td colspan=5 align=center><font size=".$wtamlet.">SS, 671 y SS 772, 773, 770 y SS del código de comercio.</font></td></tr>";
	      echo "<tr><td colspan=5 align=center><font size=".$wtamlet.">Factura impresa por computador cumpliendo con los</font></td></tr>";
	      echo "<tr><td colspan=5 align=center><font size=".$wtamlet.">requisitos del Art. 617 del E.T.</font></td></tr>";
	      echo "<tr><td colspan=5>&nbsp</td></tr>";
	      echo "<tr><td colspan=5>&nbsp</td></tr>";
	      */
	      $query = " SELECT count(*)
	         		   FROM {$wbasedato}_000041
	         		  WHERE Clidoc = '{$wdocpac}'
	         		    AND Fecha_data < '2014-04-07'";
	      $rs    = mysql_query( $query, $conex );
	      $row   = mysql_fetch_array( $rs );

		  $wpuntos_cliente = 0;
	      if ( ( $wpuntos_cliente > 0 ) and ( $row[0] > 0 ) )//se agrega la última parte para que nunca muestre los puntos.
	         {
		      $q = "SELECT salsal "
		          ."  FROM ".$wbasedato."_000060 "
		          ." WHERE saldto = '".$wdocpac."'";
		      $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		      $row = mysql_fetch_array($res);


		      if ($row[0] > 0)
		         {
			      echo "<TR><TD colspan=8 align=left>============================================================================================</TD></TR>";
			      echo "<tr><td colspan=8 align=center><font size=".$wtamlet."><b>PROGRAMA  PUNTOS"."</b></font></td></tr>";
			      echo "<TR><TD colspan=8 align=left>============================================================================================</TD></TR>";


			      echo "<br><br>";
			      echo "<tr>";
			      echo "<td colspan = 8 align=center>";
				  if(isset($wobservacion) && $wobservacion!="")
					echo "<font size=".$wtamlet."><b>!!".$wobservacion."¡¡</b></font>";
				  echo "</td>";
			      echo "</tr>";
			      echo "<br><br>";


			      echo "<tr>";
		          //echo "<td>&nbsp</td>";
		          echo "<td colspan=6 align=LEFT><font size=".$wtamlet."><b>Puntos acumulados antes de esta compra..</b></font></td>";
		          echo "<td colspan=2 align=RIGHT><font size=".$wtamlet."><b>".number_format(($row[0]-$wpuntos_cliente),2,'.',',')."</td></tr>";
		          echo "<tr>";
		          //echo "<td>&nbsp</td>";
		          echo "<td colspan=6 align=LEFT><font size=".$wtamlet."><b>Mas Puntos por esta compra........................</b></font></td>";
		          echo "<td colspan=2 align=RIGHT><font size=".$wtamlet."><b>".number_format($wpuntos_cliente,2,'.',',')."</td></tr>";

		          echo "<tr>";
		          //echo "<td>&nbsp</td>";
		          echo "<td colspan=6 align=LEFT><font size=".$wtamlet."><b>Total Puntos acumulados..............................</b></font></td>";
		          echo "<td colspan=2 align=RIGHT><font size=".$wtamlet."><b>".number_format($row[0],2,'.',',')."</td></tr>";

		          echo "<TR><TD colspan=8 align=left>===========================================================================================</TD></TR>";
		          echo "<tr><td colspan=8>&nbsp</td></tr>";
	      		  echo "<tr><td colspan=8>&nbsp</td></tr>";
		          echo "<tr><td colspan=8>&nbsp</td></tr>";
	      		  echo "<tr><td colspan=8>&nbsp</td></tr>";
		         }
	         }

	      echo "<tr>";
	      if ($wpagintern != "" and $wpagintern != "NO APLICA")    //Pagina de Internet
	         echo "<tr><td colspan=8 align=center><font size=".$wtamlet."><b>Visitenos en: ".$wpagintern."</b></font></td></tr>";
	      if ($wemail_pos != "" and $wemail_pos != "NO APLICA")    //Email
	         echo "<tr><td colspan=8 align=center><font size=".$wtamlet."><b>Escribanos a: ".$wemail_pos."</b></font></td></tr>";
	      if ($wteldompos != "" and $wteldompos != "NO APLICA")    //Telefono domicilio
	         echo "<tr><td colspan=8 align=center><font size=".$wtamlet."><b>Para sus domicilios comuniquese al ".$wteldompos."</b></font></td></tr>";
	      echo "<tr>";
	     }
      } //Else num=0 EL DOCUEMNTO NO EXISTE O ESTA DESCUADRADO
     else
        {
	     echo "<tr><td colspan=8>&nbsp</td></tr>";
		 echo "<tr><td colspan=8>&nbsp</td></tr>";
		 echo "<tr><td colspan=8>&nbsp</td></tr>";
	     echo "<tr><td colspan=8><font size=4><B>!!!!! ATENCION !!!!! EL DOCUMENTO NO EXISTE O PRESENTA ALGUN DESCUADRE, REPITA LA VENTA E INFORME DE ESTO A SISTEMAS</B></font></td></tr>";
	     echo "<tr><td colspan=8>&nbsp</td></tr>";
		 echo "<tr><td colspan=8>&nbsp</td></tr>";
		 echo "<tr><td colspan=8>&nbsp</td></tr>";
        }
	echo "<TR><TD colspan=8 align=left><b><font size=".($wtamlet-2).">*** FACTURA ORIGINAL ***</font></b></TD></TR>";

   }
  else  //No se genero factura
     {
	  //if ($wcuotamod > 0)
	  //  {
		 //IMPRESION CON EL LOGO AL PRINCIPIO
		 echo "<tr><td align=center colspan=5><img src='/matrix/images/medical/ips/logo_".$wbasedato.".png' WIDTH=510 HEIGHT=200></td></tr>";
		 echo "<tr><td colspan=8 align=center><font size=6>".$wnomemppos."</font></td></tr>";
		 echo "<tr><td colspan=8 align=center><font size=".$wtamlet.">Nit. : ".$wnit_pos."</font></td></tr>";
		 echo "<tr><td colspan=8 align=center><font size=".$wtamlet.">Tel.: ".$wtel_pos." Dir.: ".$wdir_pos."</font></td></tr>";
		 echo "<tr><td colspan=8 align=center><font size=".$wtamlet.">".$wtipregiva."</font></td></tr>";
		 echo "<tr><td colspan=4 align=left><font size=".$wtamlet.">Hora: ".$whora_tempo."</font></td>";
		 echo "<td colspan=4 align=right><font size=".$wtamlet.">Fecha: ".$wfecha_tempo."</font></td></tr>";

	     if ($wnrorec > 0)
		 {
	        echo "<tr><td colspan=4 align=left><font size=".$wtamlet.">Recibo Nro:".$wnrorec."</font></td><td colspan=4 align=right><font size=".$wtamlet.">Venta Nro:".$wnrovta."</font></td></tr>";
	     }
		 else
		 {
			 if($wemp_pmla == '06')
			 {

			 }
			 else
			 {
				echo "<tr><td colspan=8><font size=".$wtamlet.">Venta Nro: ".$wnrovta."</font></td></tr>";

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

		 if (isset($wprograma) and ($wprograma != ""))
		    echo "<tr><td colspan=8><font size=".$wtamlet.">Programa: ".$wprograma."</font></td></tr>";
		/*
         echo "<tr><td colspan=8>&nbsp</td></tr>";
		 echo "<th align=left bgcolor=DDDDDD colspan=7><font size=".$wtamlet.">Descripción</font></th>";
		 echo "<th align=RIGHT bgcolor=DDDDDD><font size=".$wtamlet.">Cant.</font></th>";

		 //ACA TRAIGO TODA LA VENTA
		 $q = " SELECT vdeart, artnom, unides, vdecan, vdevun, vdepiv, (vdevun*vdecan)*(vdepiv/100), (vdevun*vdecan) "
		     ."   FROM ".$wbasedato."_000017, ".$wbasedato."_000001, ".$wbasedato."_000002 "
		     ."  WHERE vdenum = '".$wnrovta."'"
		     ."    AND vdeart = artcod "
		     ."    AND mid(artuni,1,locate('-',artuni)-1) = unicod ";

		 $res = mysql_query($q,$conex);
		 $num = mysql_num_rows($res);
*/
		// if ($num > 0)

		   /* for ($i=1;$i<=$num;$i++)
		       {
		        $row = mysql_fetch_array($res);

                echo "<tr>";
		        echo "<td align=left colspan=4><font size=".$wtamlet.">".substr($row[1],0,30)."</font></td>";
		        echo "<td align=right colspan=4><font size=".$wtamlet.">".$row[3]."</font></td>";
		        echo "</tr>";
		       }
	        echo "<TR><TD colspan=8 align=center>_________________________________________________________________________________________________________</TD></TR>";

		    if ($fk > 0)
		       {
		        if ($wcuotamod > 0)
			      {
			       echo "<tr><td colspan=7><font size=".$wtamlet.">Cuota Mod. o Franquicia: </font></td>";
			       echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format($wcuotamod,0,'.',',')."</font></td></tr>";
			      }

			    echo "<TR><TD colspan=8 align=center>_________________________________________________________________________________________________________</TD></TR>";
			    //FORMA DE PAGO
			    echo "<tr><td colspan=8>&nbsp</td></tr>";
			    echo "<th bgcolor=DDDDDD align=left colspan=2><font size=".$wtamlet.">F-PAGO</font></th>";
			    echo "<th bgcolor=DDDDDD align=right colspan=3><font size=".$wtamlet.">Doc.Anexo</font></th>";
			    echo "<th bgcolor=DDDDDD align=right colspan=2><font size=".$wtamlet.">Observ.</font></th>";
			    echo "<th bgcolor=DDDDDD align=right><font size=".$wtamlet.">Valor</font></th>";

			    for ($i=1;$i<=$fk;$i++)
			       {
			        echo "<tr>";
			        echo "<td align=left colspan=2><font size=".$wtamlet.">".$wfpa[$i]."</font></td>";
			        echo "<td align=right colspan=3><font size=".$wtamlet.">".$wdocane[$i]."</font></td>";
			        echo "<td align=right colspan=2><font size=".$wtamlet.">".$wobsrec[$i]."</font></td>";
			        echo "<td align=right><font size=".$wtamlet.">".number_format($wvalfpa[$i],0,'.',',')."</font></td>";
			        echo "</tr>";
			       }
			    echo "<tr><td colspan=7><font size=".$wtamlet.">Cambio: </font></td>";
			    echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format($wvalcam,0,'.',',')."</font></td></tr>";
			    echo "<tr><td colspan=8>&nbsp</td></tr>";
			   }
		    echo "<TR><TD colspan=8 align=center>_________________________________________________________________________________________________________</TD></TR>";
		    echo "<tr>";
		    echo "<td colspan=4><font size=3>Vend: ".$wcodusu." - ".$wnomusu."</font></td>";
		    echo "<td colspan=4><font size=3>Caja: ".$wcaja."</font></td>";
		    echo "</tr>";*/

		//-------------------------
		//-------------------------
		//-------------------------

		if(isset($wlotven) && $wlotven=='on' && $wtipcli!=$wtipoEmpresaParticular)
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

		echo "<tr><td colspan=8>&nbsp;</td></tr>";
		if(imprimir_cum())
			echo "<th width='70' align=left bgcolor=DDDDDD><font size=".$wtamlet.">CUM</font></th>";
		echo "<th width='".$widthart."' align=left bgcolor=DDDDDD".$colspandes."><font size=".$wtamlet.">Descripción</font></th>";
		if(isset($wlotven) && $wlotven=='on' && $wtipcli != $wtipoEmpresaParticular)
		{
			echo "<th width='80' align=center bgcolor=DDDDDD><font size=".$wtamlet."># Lote</font></th>";
			echo "<th width='120' align=center bgcolor=DDDDDD><font size=".$wtamlet.">Fec. Vmto</font></th>";
		}
		echo "<th width='70' align=center bgcolor=DDDDDD><font size=".$wtamlet.">V/U</font></th>";
		echo "<th width='60' align=center bgcolor=DDDDDD><font size=".$wtamlet.">Cant</font></th>";
		echo "<th width='60' align=center bgcolor=DDDDDD><font size=".$wtamlet.">% Iva</font></th>";
		echo "<th width='70' align=rigth bgcolor=DDDDDD><font size=".$wtamlet.">Total</font></th>";


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
				echo "<td align=left width='70'><font size=".$wtamletart.">".$row['artcna']."</font></td>";                             //CUM
		      if ($row[8] > 0)   //Si tiene descuento el articulo
		         echo "<td align=left".$colspandes." width='".$widthart."'><font size=".$wtamletart.">** ".substr($articulo,0,21)."</font></td>";           //Descripcion
		      else
		         echo "<td align=left".$colspandes." width='".$widthart."'><font size=".$wtamletart.">".$articulo."</font></td>";            //Descripcion

			  if($row['vdefve']!='0000-00-00')
				$fecven = $row['vdefve'];
			  else
				$fecven = '';
			  if(isset($wlotven) && $wlotven=='on' && $wtipcli != $wtipoEmpresaParticular)
			  {
				echo "<td align=left width='60'><font size=".$wtamletart.">".$row['vdelot']."</font></td>";                             //# Lote
				echo "<td align=center width='80'><font size=".$wtamletart.">".$fecven."</font></td>";                             //Fecha de vencimiento
		      }
			  echo "<td align=right width='70'><font size=".$wtamletart.">".number_format($row[4],0,'.',',')."</font></td>";    //Valor unitario con IVA
		      echo "<td align='center' width='60'><font size=".$wtamletart.">".$row[3]."</font></td>";                             //Cantidad
		      echo "<td align=center width='60'><font size=".$wtamletart.">".$row[5]."</font></td>";                             //% IVA
		      echo "<td align=right width='70'><font size=".$wtamletart.">".number_format(($row[7]),0,'.',',')."</font></td>";  //Valor total con iva
		      echo "</tr>";

		      $wtotvenconiva=$wtotvenconiva+$row[7];
		      $wtotdes=$wtotdes+($row[8] + ($row[3]*round(($row[8]/$row[3])*($row[5]/100))));


		      if ($row[5] == 0)     //Iva cero (0) entonces es excluido
		         $wtotvenexclui=$wtotvenexclui+$row[7];
		     }

		  if ($wtotvenexclui > 0)
		     $wtotvenexclui=$wtotvenexclui-$wtotdes;

		  echo "<tr><td colspan=7>&nbsp</td>";
		  echo "<td align=right>-------------------</td></tr>";

		  echo "<tr><td colspan=7><font size=".$wtamlet."><b>Sub-Total</b></font></td>";
	      echo "<td align=right><font size=".$wtamlet.">".number_format($wtotvenconiva,0,'.',',')."</font></td></tr>";                  //Total venta con IVA

	      echo "<tr><td colspan=7><font size=".$wtamlet."><b>** Descuento: </b></font></td>";
		  echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format(round($wtotdes),0,'.',',')."</font></td></tr>";              //Descuento antes de IVA

	      if ($wtotrec > 0)  //Si tiene recargo lo imprimo
		     {
			  echo "<tr><td colspan=7><font size=".$wtamlet.">Recargo: </font></td>";
		      echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format($wtotrec,0,'.',',')."</font></td></tr>";          //Recargo
	         }

	      echo "<TR><TD colspan=8 align=center>_________________________________________________________________________________________________________</TD></TR>";

	      echo "<tr><td colspan=6><font size=".$wtamlet."><b>Valor Total</b></font></td>";
	      echo "<td align=right colspan=2><font size=".$wtamlet."><b>".number_format(($wtotal),0,'.',',')."</b></font></td></tr>";             //Total a pagar Neto


	      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      // RESUMEN DE IVA
	      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      echo "<tr><td colspan=8>&nbsp</td></tr>";
	      echo "<tr><td colspan=8>&nbsp</td></tr>";
	      echo "<tr>";
	      echo "<th bgcolor=DDDDDD align=CENTER colspan=8><font size=".$wtamlet.">RESUMEN DE IVA</font></th>";
	      echo "</tr>";

	      echo "<th bgcolor=DDDDDD align=LEFT colspan=4><font size=".$wtamlet."><b>Tipo</b></font></th>";
		  echo "<th bgcolor=DDDDDD align=right colspan=2><font size=".$wtamlet."><b>Compra</b></font></th>";
		  echo "<th bgcolor=DDDDDD align=right>&nbsp</th>";
		  echo "<th bgcolor=DDDDDD align=right><font size=".$wtamlet."><b>IVA</b></font>&nbsp</tr>";

		  //GRAVADO
		  echo "<tr>";
	      echo "<td colspan=4><font size=".$wtamlet."><b>Gravado</b></font></td>";
	      $wgravado=$wtotal-$wtotiva-$wtotvenexclui;
	      echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format(($wgravado),0,'.',',')."</font></td>";     //Total Gravado
		  echo "<td align=right>&nbsp</td>";
	      echo "<td align=right><font size=".$wtamlet.">".number_format(($wtotiva),0,'.',',')."</font></td>";      //Total Iva
		  echo "</tr>";

		  //EXCLUIDO
		  echo "<tr>";
	      echo "<td colspan=4><font size=".$wtamlet."><b>Excluido</b></font></td>";
	      echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format($wtotvenexclui,0,'.',',')."</font></td>";  //Total Excluido
		  echo "<td align=right>&nbsp</td>";
	      echo "<td align=right><font size=".$wtamlet.">".number_format(0,0,'.',',')."</font></td>";               //Total Iva
		  echo "</tr>";

		  echo "<TR><TD colspan=8 align=center>_________________________________________________________________________________________________________</TD></TR>";

		  //TOTALES
		  echo "<tr>";
	      echo "<td colspan=4><font size=".$wtamlet."><b>Total</b></font></td>";
	      echo "<td align=right colspan=2><font size=".$wtamlet.">".number_format(($wgravado+$wtotvenexclui),0,'.',',')."</font></td>";  //Total Gravado
		  echo "<td align=right>&nbsp</td>";
	      echo "<td align=right><font size=".$wtamlet.">".number_format(($wtotiva),0,'.',',')."</font></td>";                  //Total Iva
		  echo "</tr>";

		  $sqlventaobs= " SELECT  Renobs
							FROM ".$wbasedato."_000021 ,".$wbasedato."_000020
						   WHERE rdevta = '".$wnrovta."'
							 AND Rdenum = Rennum ";

		  $res1 = mysql_query($sqlventaobs,$conex) or die (mysql_errno()." - ".mysql_error());
		  $num1 = mysql_num_rows($res1);

		  if ($num > 0)
		  {
			  $row1 	= mysql_fetch_array($res1);
			  $robser	=$row1[0];
		  }

		  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		  echo"<br><br><br><br><br><br><tr><td colspan=8  align='center'><font size=".$wtamlet.">Observación:</font></td></tr>";
		  echo"<tr><td colspan=8><font size=".$wtamlet.">".$robser."</font></td></tr>";
	      if ($fk > 0)
	        {
		     echo "<tr><td colspan=8>&nbsp</td></tr>";
	         if ($wcuotamod > 0)
		        {
		         echo "<tr><td colspan=7><font size=".$wtamlet.">Cuota Mod. o Franquicia: </font></td>";
		         echo "<td colspan=1 align=right><font size=".$wtamlet.">".number_format($wtotcopcmo,0,'.',',')."</font></td></tr>";
		        }

		     echo "<TR><TD colspan=8 align=center>_________________________________________________________________________________________________________</TD></TR>";
		     //FORMA DE PAGO

		     echo "<tr><td colspan=8>&nbsp</td></tr>";
		     echo "<tr><td colspan=8>";
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
					 echo "<td align=left width='40%'><font size=".$wtamlet.">".$wfpa[$i]."</font></td>";
					 echo "<td align=right width='25%'><font size=".$wtamlet.">".$wdocane[$i]."</font></td>";
					 echo "<td align=right width='25%'><font size=".$wtamlet.">".$wobsrec[$i]."</font></td>";
					 echo "<td align=right width='10%'><font size=".$wtamlet.">".number_format($wvalfpa[$i],0,'.',',')."</font></td>";
					 echo "</tr>";
					}
		        }
				if($wsaldo > 0)
				{
					echo "<tr><td colspan='3'><br><br></td>";
					echo "<tr><td colspan='3'><font size=".$wtamlet.">Total Entregado:</font></td>";
					echo "<td align=right><font size=".$wtamlet.">".@number_format($wentregado,0,'.',',')."</font></td></tr>";
					echo "<tr><td colspan='3'><font size=".$wtamlet.">Total Abonado:</font></td>";
					echo "<td align=right><font size=".$wtamlet.">".@number_format($wabonado,0,'.',',')."</font></td></tr>";

					if (($wvalcam==''))
					 {
						$wvalcam = 0;
					 }
					 echo "<tr><td colspan=3><font size=".$wtamlet.">Cambio:</font></td>";
					 echo "<td align=right><font size=".$wtamlet.">".@number_format($wvalcam,0,'.',',')."</font></td></tr>";


					echo "<tr><td colspan='3'><font size=".$wtamlet.">Saldo:</font></td>";
					echo "<td align=right><font size=".$wtamlet.">".@number_format($wsaldo,0,'.',',')."</font></td></tr>";

				}
				else
				{
					echo "<tr><td colspan='3'><br><br></td>";
					echo "<tr><td colspan='3'><font size=".$wtamlet.">Total Entregado:</font></td>";
					echo "<td align=right><font size=".$wtamlet.">".@number_format($wentregado,0,'.',',')."</font></td></tr>";
					echo "<tr><td colspan='3'><font size=".$wtamlet.">Total Abonado:</font></td>";
					echo "<td align=right><font size=".$wtamlet.">".@number_format($wabonado,0,'.',',')."</font></td></tr>";
					if (($wvalcam==''))
					 {
						$wvalcam = 0;
					 }
					 echo "<tr><td colspan=3><font size=".$wtamlet.">Cambio:</font></td>";
					 echo "<td align=right><font size=".$wtamlet.">".@number_format($wvalcam,0,'.',',')."</font></td></tr>";

					echo "<tr><td colspan='3'><font size=".$wtamlet.">Saldo:</font></td>";
					echo "<td align=right><font size=".$wtamlet.">".@number_format(0,0,'.',',')."</font></td></tr>";
				}
			 echo "</table>";
		     echo "</td></tr>";
		     echo "<tr><td colspan=8>&nbsp</td></tr>";
		    }
		  echo "<TR><TD colspan=8 align=center>_________________________________________________________________________________________________________</TD></TR>";
	      echo "<tr>";
	      echo "<td colspan=6><font size=3>Vend: ".$wcodusu." - ".$wnomusu."</font></td>";
	      echo "<td colspan=2><font size=3>Caja: ".$wcaja."</font></td>";
	      //echo "<td colspan";

	      //Si es un domicilio imprimo el mensajero
	      if ($wtipven=="Domicilio")
		     echo "<tr><td colspan=8><font size=3>Mensajero: ".$wmensajero."</font></td></tr>";


		  if ($wtipcli != $wtipoEmpresaParticular)
		     {
			  echo "<tr><td colspan=8>&nbsp</td></tr>";
			  echo "<tr><td colspan=8>&nbsp</td></tr>";
			  echo "<tr><td colspan=8>&nbsp</td></tr>";
			  echo "<TR><TD colspan=8 align=center>_____________________________________________________</TD></TR>";
		      echo "<tr><td colspan=8 align=center><font size=".$wtamlet.">Firma del Cliente</font></td></tr>";
	         }

	      //Traigo la resolucion con la que salio la factura
	      $q = " SELECT fenrln "
	          ."   FROM ".$wbasedato."_000018 "
	          ."  WHERE fenffa = '".$wfueffa."'"
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

	      echo "<tr><td colspan=8 align=center>&nbsp</td></tr>";
	      echo "<tr><td colspan=8 align=center><font size=".$wtamlet.">".$wresol."</font></td></tr>";
	      echo "<tr><td colspan=8 align=center>&nbsp</td></tr>";
	      echo "<tr><td colspan=8 align=center>&nbsp</td></tr>";


	      $query = " SELECT count(*)
	         		   FROM {$wbasedato}_000041
	         		  WHERE Clidoc = '{$wdocpac}'
	         		    AND Fecha_data < '2014-04-07'";
	      $rs    = mysql_query( $query, $conex );
	      $row   = mysql_fetch_array( $rs );

		  $wpuntos_cliente = 0;
	      if ( ( $wpuntos_cliente > 0 ) and ( $row[0] > 0 ) )//se agrega la última parte para que nunca muestre los puntos.
	         {
		      $q = "SELECT salsal "
		          ."  FROM ".$wbasedato."_000060 "
		          ." WHERE saldto = '".$wdocpac."'";
		      $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		      $row = mysql_fetch_array($res);


		      if ($row[0] > 0)
		         {
			      echo "<TR><TD colspan=8 align=left>============================================================================================</TD></TR>";
			      echo "<tr><td colspan=8 align=center><font size=".$wtamlet."><b>PROGRAMA  PUNTOS"."</b></font></td></tr>";
			      echo "<TR><TD colspan=8 align=left>============================================================================================</TD></TR>";


			      echo "<br><br>";
			      echo "<tr>";
			      echo "<td colspan = 8 align=center>";
				  if(isset($wobservacion) && $wobservacion!="")
					echo "<font size=".$wtamlet."><b>!!".$wobservacion."¡¡</b></font>";
				  echo "</td>";
			      echo "</tr>";
			      echo "<br><br>";


			      echo "<tr>";
		          //echo "<td>&nbsp</td>";
		          echo "<td colspan=6 align=LEFT><font size=".$wtamlet."><b>Puntos acumulados antes de esta compra..</b></font></td>";
		          echo "<td colspan=2 align=RIGHT><font size=".$wtamlet."><b>".number_format(($row[0]-$wpuntos_cliente),2,'.',',')."</td></tr>";
		          echo "<tr>";
		          //echo "<td>&nbsp</td>";
		          echo "<td colspan=6 align=LEFT><font size=".$wtamlet."><b>Mas Puntos por esta compra........................</b></font></td>";
		          echo "<td colspan=2 align=RIGHT><font size=".$wtamlet."><b>".number_format($wpuntos_cliente,2,'.',',')."</td></tr>";

		          echo "<tr>";
		          //echo "<td>&nbsp</td>";
		          echo "<td colspan=6 align=LEFT><font size=".$wtamlet."><b>Total Puntos acumulados..............................</b></font></td>";
		          echo "<td colspan=2 align=RIGHT><font size=".$wtamlet."><b>".number_format($row[0],2,'.',',')."</td></tr>";

		          echo "<TR><TD colspan=8 align=left>===========================================================================================</TD></TR>";
		          echo "<tr><td colspan=8>&nbsp</td></tr>";
	      		  echo "<tr><td colspan=8>&nbsp</td></tr>";
		          echo "<tr><td colspan=8>&nbsp</td></tr>";
	      		  echo "<tr><td colspan=8>&nbsp</td></tr>";
		         }
	         }



			//-------------------------
			//-------------------------








		     echo "<tr>";
	      if ($wpagintern != "" and $wpagintern != "NO APLICA")    //Pagina de Internet
	         echo "<tr><td colspan=8 align=center><font size=".$wtamlet."><b>Visitenos en: ".$wpagintern."</b></font></td></tr>";
	      if ($wemail_pos != "" and $wemail_pos != "NO APLICA")    //Email
	         echo "<tr><td colspan=8 align=center><font size=".$wtamlet."><b>Escribanos a: ".$wemail_pos."</b></font></td></tr>";
	      if ($wteldompos != "" and $wteldompos != "NO APLICA")    //Telefono domicilio
	         echo "<tr><td colspan=8 align=center><font size=".$wtamlet."><b>Para sus domicilios comuniquese al ".$wteldompos."</b></font></td></tr>";
	      echo "<tr>";
	     }
	   // }
     }

// echo "<TR><TD colspan=8 align=left><b><font size=".($wtamlet-2).">*** FACTURA ORIGINAL ***</font></b></TD></TR>";
echo "</table>";
?>
