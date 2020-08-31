<head>
  <title>IMPRIMIR FACTURA UNIX</title>
</head>

<script type="text/javascript">
function enter()
	{
	 document.forms.impfacunix.submit();
	}
</script>

<body>
<?php
include_once("conex.php");
  /***************************************************
   *            IMPRIMIR FACTURA DE UNIX             *
   *				CONEX, FREE => OK				 *
   ***************************************************/

//==================================================================================================================================
//PROGRAMA                   : imp_factura_unix.php
//AUTOR                      : Juan Carlos Hernández M.
//FECHA CREACION             : Agosto 23 de 2011
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="Marzo 14 de 2012";
//DESCRIPCION
//==================================================================================================================================
//Este programa se hace para imprimir las facturas de Unix que se requieran imprimir con aspecto diferente al que sale desde Unix
//==================================================================================================================================

//==================================================================================================================================
//MODIFICACIONES ===================================================================================================================
//==================================================================================================================================
// 2012-03-14 - Se creo la función 'imprimir_factura_detalle' que permite imprimir la factura con todos los conceptos de ésta,
// con el valor cargado a la clínica y el valor cargado a terceros, además de las observaciones y el log del pie de página
//==================================================================================================================================

  function auditoria( $wfac, $wdid,$whis,$wing, $wfec, $wval, $wpla )
    {

	  global $conex;
	  global $wchequeo;
	  global $user;

	  list( $a, $usuario ) = explode( "-", $user );

	  $fecha = date( "Y-m-d" );
	  $hora = date( "H:i:s" );

	  $q = "INSERT INTO ".$wchequeo."_000002 (      Medico   , Fecha_data , Hora_data ,   Impfac  ,  Impdid   ,   Imphis  ,  Imping   ,  Impfec   ,    Impusu    ,  Impval   ,  Imppla   ,   Seguridad    ) "
	      ."                           VALUES('".$wchequeo."','".$fecha."','".$hora."','".$wfac."','".$wdid."','".$whis."','".$wing."','".$wfec."','".$usuario."','".$wval."','".$wpla."','C-".$usuario."') ";

	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

    }

  function mostrar_empresa($wemp_pmla)
    {
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;
	  global $wchequeo;

      //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
	  $q = " SELECT detapl, detval, empdes "
	      ."   FROM root_000050, root_000051 "
	      ."  WHERE empcod = '".$wemp_pmla."'"
	      ."    AND empest = 'on' "
	      ."    AND empcod = detemp ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);

	  if ($num > 0 )
	     {
		  for ($i=1;$i<=$num;$i++)
		     {
		      $row = mysql_fetch_array($res);

		      if ($row[0] == "cenmez")
		         $wcenmez=$row[1];

		      if ($row[0] == "afinidad")
		         $wafinidad=$row[1];

		      if ($row[0] == "movhos")
		         $wbasedato=$row[1];

		      if ($row[0] == "tabcco")
		         $wtabcco=$row[1];

			  if ($row[0] == "Chequeo Ejecutivo")
		         $wchequeo=$row[1];
	         }
	     }
	    else
	       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";

      $winstitucion=$row[2];
	}


  function seleccionarPaquete(&$wpaquete)
    {
	 global $conex;
	 global $wchequeo;


	 //Seleccionar PAQUETE
	  echo "<center><table>";
      $q = " SELECT placod, planom "
          ."   FROM ".$wchequeo."_000001 "
          ."  WHERE plaest = 'on' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);

	  echo "<tr class=fila1><td align=center><font size=30>Seleccione el Paquete: </font></td></tr>";
	  echo "</table>";
	  echo "<br>";
	  echo "<center><table>";
	  echo "<tr><td align=center><select name='wpaquete' size='1' style=' font-size:20px; font-family:Verdana, Arial, Helvetica, sans-serif; height:40px'>";
	  echo "<option>&nbsp</option>";
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";
	}

  function imprimir_factura($wfactura)
    {
	  global $wpaquete;
	  global $conexunix;

	  $q = " SELECT carano, carmes, carfec, carfev, carcep, carpac, carced, carres, carval, carhis, carnum "
	      ."   FROM cacar "
		  ."  WHERE carfue = '20' "
		  ."    AND cardoc = ".$wfactura
		  ."    AND caranu = '0' ";
	  $res = odbc_do($conexunix,$q);

	  while( odbc_fetch_row($res) )
	    {
	     $wano = odbc_result($res,1);
	     $wmes = odbc_result($res,2);
	     $wfec = odbc_result($res,3);	//Fecha
	     $wfev = odbc_result($res,4);	//Fecha de vencimiento
	     $wcep = odbc_result($res,5);
		 $wpac = odbc_result($res,6);	//paciente
	     $wced = odbc_result($res,7);	//Nro de documento
	     $wres = odbc_result($res,8);	//Responsable
	     $wval = odbc_result($res,9);	//Valor factura
		 $whis = odbc_result($res,10);	//Historia clínica
		 $wing = odbc_result($res,11);	//Ingreso historia

		 // $wano = "2011";
	     // $wmes = "09";
	     // $wfec = "2011-09-19";	//Fecha
	     // $wfev = "2011-09-20";	//Fecha de vencimiento
	     // $wcep = "Hmmmm....";
		 // $wpac = "Ediwn Molina Grisales";	//paciente
	     // $wced = "98703683";	//Nro de documento
	     // $wres = "Edwin Molina Grisales";	//Responsable
	     // $wval = "100000";	//Valor factura
		 // $whis = "154862";
		 // $wing = "5";


		 // echo "<br><br><br><br><br><br>";
		 // echo $wres."             ".$wced."                                                                     ".substr($wfec,8,2)."  ".substr($wfec,5,2)."  ".substr($wfec,0,4)."<br><br>";
		 // echo $wpac."                                                                                           ".substr($wfev,8,2)."  ".substr($wfev,5,2)."  ".substr($wfev,0,4)."<br><br>";

		 echo "<br><br>";

		 //1ra fila: Fecha y fecha de venciemiento
		 echo "<table style='width:18.5cm;font-size:10pt;font-family:Courier New'>";
		 echo "<tr style='height:0.8cm'>";
		 echo "<td style='width:12.5cm'></td>";
		 echo "<td style='width:0.8cm' align='right'>".substr($wfec,8,2)."</td>";			//Fecha de ingreso
		 echo "<td style='width:0.8cm' align='right'>".substr($wfec,5,2)."</td>";			//Fecha de ingreso
		 echo "<td style='width:1.4cm' align='right'>".substr($wfec,0,4)."</td>";			//Fecha de ingreso
		 echo "<td style='width:0.8cm' align='right'>".substr($wfev,8,2)."</td>";			//Fecha de salida
		 echo "<td style='width:0.8cm' align='right'>".substr($wfev,5,2)."</td>";			//Fecha de salida
		 echo "<td style='width:1.4cm' align='right'>".substr($wfev,0,4)."</td>";			//Fecha de salida
		 echo "</tr>";
		 echo "</table>";

		 //No se neceista bordes para la tabla al imprimir
		 //2da fila: Responsable, nit, domicilio, telefono
		 echo "<table style='width:18.5cm;font-size:10pt;font-family:Courier New'>";
		 echo "<tr style='height:0.8cm'>";
		 echo "<td style='width:8cm'>".$wres."</td>";
		 echo "<td style='width:3cm'>".$wced."</td>";
		 echo "<td style='width:6cm'></td>";
		 echo "<td style='width:1.5cm'></td>";
		 echo "</tr>";
		 echo "</table>";

		 //Fila del paciente
		 echo "<table style='width:18.5cm;font-size:10pt;font-family:Courier New'>";
		 echo "<tr style='height:0.8cm'>";
		 echo "<td style='width:9.7cm'>".$wpac."</td>";						//Paciente
		 echo "<td style='width:0.8cm' align='right'>".substr($wfec,8,2)."</td>";			//Fecha de ingreso
		 echo "<td style='width:0.8cm' align='right'>".substr($wfec,5,2)."</td>";			//Fecha de ingreso
		 echo "<td style='width:1.4cm' align='right'>".substr($wfec,0,4)."</td>";			//Fecha de ingreso
		 echo "<td style='width:0.8cm' align='right'>".substr($wfec,8,2)."</td>";			//Fecha de salida
		 echo "<td style='width:0.8cm' align='right'>".substr($wfec,5,2)."</td>";			//Fecha de salida
		 echo "<td style='width:1.4cm' align='right'>".substr($wfec,0,4)."</td>";			//Fecha de salida
		 echo "<td style='width:2.8cm' align='center'>".$whis."-".$wing."</td>";			//Estadia
		 echo "</tr>";

		 echo "<tr  style='height:0.8cm'><td></td></tr>";	//Este espacio es el usado por documento de identidad y tipo de atencion
		 echo "<tr  style='height:0.3cm'><td></td></tr>";	//Espacio muerto, aqui va donde dice RESOLUCION DIAN No. 1100....
		 echo "</table>";

		 //Descripcion de pago
		 list( $codigo, $descripcion ) = explode( "-", $wpaquete );
		 echo "<table style='width:18.5cm;font-size:10pt;font-family:Courier New'>";
		 echo "<tr style='height:7.0cm'>";
		 echo "<td style='width:1.5cm' align='center'>".$codigo."</td>";
		 echo "<td style='width:12.5cm'>".$descripcion."</td>";
		 echo "<td style='width:4.5cm' align='right'>".number_format( $wval, 0,'.', ',' )."</td>";
		 echo "</tr>";
		 echo "</table>";

		 echo "<br>";

		 //Forma de pago
		 echo "<table style='width:18.5cm;font-size:10pt;font-family:Courier New'>";
		 echo "<tr style='height:1.3cm'>";
		 echo "<td style='width:11.5cm'><br>".montoescrito( $wval )."</td>";
		 echo "<td style='width:2.5cm' rowspan='3'></td>";
		 echo "<td style='width:4.5cm' rowspan='3' align='right'><br>".number_format( $wval, 0,'.', ',' )."<br><br>".number_format( $wval, 0,'.', ',' )."<br><br><br><br>".number_format( $wval, 0,'.', ',' )."</td>";
		 echo "</tr>";

		 echo "<tr style='height:1.4cm'>";
		 echo "<td></td>";
		 echo "</tr>";

		 echo "<tr style='height:0.4cm'>";
		 echo "<td></td>";
		 echo "</tr>";

		 echo "</table>";

		 auditoria( $wfactura, $wced, $whis,$wing, date("Y-m-d"), $wval, $wpaquete );

		 // return;
		}
	}

  function imprimir_factura_detalle($wfactura,$wtop,$wleft)
    {
	  global $wpaquete;
	  global $conexunix;
	  global $wusuario;

	  $wffa = "20";		// Fuente de facturas
	  $existen_facturas = 'off';		// Indicador que determinado

	  $q = " SELECT carano, carmes, carfec, carfev, carcep, carpac, carced, carres, carval, carhis, carnum, empdir, emptel, empnit, carind "
	      ."   FROM cacar, inemp "
		  ."  WHERE carfue = '".$wffa."' "
		  ."    AND cardoc = '".$wfactura."' "
		  ."	AND carced = empcod "
		  ."    AND caranu = '0' ";
	  $res = odbc_do($conexunix,$q);

	  while( odbc_fetch_row($res) )
	    {
	     $wano = odbc_result($res,1);
	     $wmes = odbc_result($res,2);
	     $wfec = odbc_result($res,3);	//Fecha
	     $wfev = odbc_result($res,4);	//Fecha de vencimiento
	     $wcep = odbc_result($res,5);	//Documento paciente
		 $wpac = odbc_result($res,6);	//Paciente
	     $wced = odbc_result($res,7);	//Nro de documento
	     $wres = odbc_result($res,8);	//Responsable
	     $wval = odbc_result($res,9);	//Valor factura
		 $whis = odbc_result($res,10);	//Historia clinica
		 $wing = odbc_result($res,11);	//Ingreso
		 $wdir = odbc_result($res,12);	//Direccion responsable
		 $wtel = odbc_result($res,13);	//Telefono responsable
		 $wcod = odbc_result($res,14);	//Nit responsable
		 $wind = odbc_result($res,15);	//Indicador empresa o particular

		 $qdiv =  " SELECT nitdig "
				 ."   FROM conit "
				 ."  WHERE nitnit = '".$wcod."' ";
		 $resdiv = odbc_do($conexunix,$qdiv);
		 $wdiv = odbc_result($resdiv,1);	//Digito de verificacion

		 if(!isset($wdiv) || $wdiv=="")
			$wdiv = '0';

		 //echo "<br>";
		 echo "<div style='position: absolute;top:".$wtop."px;left:".$wleft."px'>";

		/*
		 echo "<table align='left'>";
		 echo "<tr style='height:".$wtop."cm'>";
		 echo "<td>&nbsp;</td>";
		 echo "<td>&nbsp;</td>";
		 echo "</tr>";
		 echo "<tr>";
		 echo "<td style='width:".$wtop."cm' valign='top' align='left'>&nbsp;</td>";
		 echo "<td valign='top' align='left'>";
		 */

		 //1ra fila: Fecha y fecha de venciemiento
		 echo "<table style='width:18.5cm;font-size:9pt;font-family:Arial'>";
		 echo "<tr style='height:1.2cm'>";
		 echo "<td style='width:12.7cm' align='center' valign='middle'>No. ".$wfactura."</td>";
		 echo "<td style='width:0.7cm' align='right' valign='middle'>".substr($wfec,8,2)."</td>";			//Fecha
		 echo "<td style='width:0.7cm' align='right' valign='middle'>".substr($wfec,5,2)."</td>";			//Fecha
		 echo "<td style='width:1.3cm' align='right' valign='middle'>".substr($wfec,0,4)."</td>";			//Fecha
		 echo "<td style='width:0.7cm' align='right' valign='middle'>".substr($wfev,8,2)."</td>";			//Fecha de vencimiento
		 echo "<td style='width:0.7cm' align='right' valign='middle'>".substr($wfev,5,2)."</td>";			//Fecha de vencimiento
		 echo "<td style='width:1.3cm' align='right' valign='middle'>".substr($wfev,0,4)."</td>";			//Fecha de vencimiento
		 echo "<td style='width:0.4cm'></td>";																//Espacio vacio
		 echo "</tr>";
		 echo "</table>";

		 //No se neceista bordes para la tabla al imprimir
		 //2da fila: Responsable, nit, domicilio, telefono
		 echo "<table style='width:18.5cm;font-size:10pt;font-family:Arial'>";
		 echo "<tr style='height:0.7cm'>";
		 echo "<td style='width:7.7cm'>".substr($wres,0,34)."</td>";	// Nombre responsable
		 echo "<td style='width:3.1cm'>".trim($wcod)."-".$wdiv."</td>";	// NIT responsable
		 echo "<td style='width:5.7cm' valign='top'>".substr($wdir,0,24)."</td>";	// Domicilio responsable
		 echo "<td style='width:2cm' valign='top'>".substr($wtel,0,8)."</td>";	// Telefono responsable
		 echo "</tr>";
		 echo "</table>";

		 // Encuentro la fecha de ingreso del paciente
		 $query="SELECT pacfec
				   FROM inpac
			      WHERE pachis = '".$whis."'
				    AND pacnum = '".$wing."' ";
		 $err_o = odbc_exec($conexunix,$query);
		 if(odbc_fetch_row($err_o))
		 {
			$wfecing=odbc_result($err_o,1);
		 }
		 else
		 {
			$query="SELECT egring
					  FROM inmegr
					 WHERE egrhis = '".$whis."'
					   AND egrnum = '".$wing."' ";
			$err_1 = odbc_exec($conexunix,$query);
			if (odbc_fetch_row($err_1))
			{
				$wfecing=odbc_result($err_1,1);
			}
			else
			{
				$wfecing=$wfec;
			}
		 }

		 // Si es ayuda diagnóstica encuentro la fecha en aymov
		 $query="SELECT b.movfec
				   FROM famov a, aymov b
				  WHERE a.movfue = '20'
				    AND a.movdoc = '".$wfactura."'
				    AND a.movfuo = b.movfue
					AND a.movhis = b.movdoc
					AND a.movanu = '0'
					AND b.movanu = '0' ";
		 $err_ay = odbc_exec($conexunix,$query);
		 if(odbc_fetch_row($err_ay))
		 {
			$wfecing=odbc_result($err_ay,1);
		 }

		 //Fila del paciente
		 echo "<table style='width:18.5cm;font-size:9pt;font-family:Arial'>";
		 echo "<tr style='height:0.7cm'>";
		 echo "<td style='width:9.9cm'>".substr($wpac,0,42)."</td>";						//Paciente
		 echo "<td style='width:0.7cm' align='right' valign='top'>".substr($wfecing,8,2)."</td>";			//Fecha de ingreso
		 echo "<td style='width:0.7cm' align='right' valign='top'>".substr($wfecing,5,2)."</td>";			//Fecha de ingreso
		 echo "<td style='width:1.3cm' align='right' valign='top'>".substr($wfecing,0,4)."</td>";			//Fecha de ingreso
		 echo "<td style='width:0.7cm' align='right' valign='top'>".substr($wfec,8,2)."</td>";			//Fecha de salida
		 echo "<td style='width:0.7cm' align='right' valign='top'>".substr($wfec,5,2)."</td>";			//Fecha de salida
		 echo "<td style='width:1.3cm' align='right' valign='top'>".substr($wfec,0,4)."</td>";			//Fecha de salida
		 echo "<td style='width:3.2cm' align='center' valign='top'>".$whis."-".$wing."</td>";			//Estadia
		 echo "</tr>";
		 echo "</table>";

		 echo "<table style='width:18.5cm;font-size:9pt;font-family:Arial'>";
		 echo "<tr style='height:0.7cm'>";
		 echo "<td style='width:3cm'>".$wcep."</td>";	//Documento de identidad del paciente
		 echo "<td style='width:15.5cm'>HOSPITALIZADO - PENSIONADO</td>";	//Tipo de atencion
		 echo "</tr>";
		 echo "</table>";

		 echo "<table style='width:18.5cm;font-size:8pt;font-family:Arial'>";
		 echo "<tr style='height:0.8cm'>";
		 echo "<td style='width:10.6cm'></td>";	//Espacio muerto, aqui va donde dice CPTO.    DESCRIPCION
		 echo "<td style='width:2.1cm' align='right' valign='bottom'>CLINICA</td>";	//Encabezado CLINICA
		 echo "<td style='width:2.1cm' align='right' valign='bottom'>TERCEROS</td>";	//Encabezado TERCEROS
		 echo "<td style='width:3.8cm;padding-right:20px' align='right' valign='bottom'>TOTAL</td>";		//Encabezado TOTAL
		 echo "</tr>";
		 echo "</table>";

		 //Descripcion de pago
		  $q = 	 " SELECT movdetcon, movdetval, movdetnit, movdetfue, movdetdoc, connom, movdetvde "
				."   FROM cacar, famovdet, facon "
				."  WHERE carfue = '".$wffa."' "
			    ."    AND cardoc = '".$wfactura."'"
				."    AND carfue = movdetfue "
				."    AND cardoc = movdetdoc "
				."    AND movdetcon = concod "
				."    AND caranu = '0' "
				."    AND movdetanu = '0'  ";
		  $resdes = odbc_do($conexunix,$q);

		  // Inicialización de variables para usar en el ciclo
		  $limite_conceptos = 13;
		  $total_otros_clinica = 0;
		  $total_otros_terceros = 0;
		  $total_otros = 0;
		  $cont = 0;
		  $total_clinica = 0;
		  $total_terceros = 0;
		  $total_descuento = 0;
		  $total = 0;

		  echo "<table style='width:18.5cm;height:6.6cm;font-size:8pt;font-family:Arial'>";
		  while( odbc_fetch_row($resdes) )
			{
			  $wcon = odbc_result($resdes,1);	//Codigo concepto
			  $wval = odbc_result($resdes,2);	//Valor del concepto
			  $wnit = odbc_result($resdes,3);	//NIT
			  $wfue = odbc_result($resdes,4);	//Fuente
			  $wdoc = odbc_result($resdes,5);	//Documento
			  $wcde = odbc_result($resdes,6);	//Descripcion concepto
			  $descuento = odbc_result($resdes,7);	//Valor de descuento

			  //Consulto datos del tercero (Porcentaje y Nombre)
			  $q =   " SELECT connitpor, nitnom "
					."   FROM faconnit, conit "
					."  WHERE connitcon = '$wcon'"
					."    AND connitnit = '$wnit'"
					."    AND connitnit = nitnit";
			  $rescon = odbc_do($conexunix,$q);
			  odbc_fetch_row($rescon);
			  $wpor = odbc_result($rescon,1);	//Porcentaje tercero
			  $wnom = odbc_result($rescon,2);	//Nombre tercero
			  $wtde = $wnit." ".substr($wnom,0,11);
			  if($wpor && $wpor>0)
			  {
				  /*
				  // Se comenta porque en teter el valor del tercero no está discriminado, es igual al total del concepto
				  // entonces para obtener el valor del tercero se hace calculo por medio del porcentaje en faconnit
				  // consulto el valor asignado al tercero
				  $q =   " SELECT terval "
						."   FROM facarfac, facardet, teter "
						."  WHERE carfacfue = '$wfue'"
						."    AND carfacdoc = '$wdoc'"
						."    AND carfacanu = '0'"
						." 	  AND carfacreg = cardetreg "
						."    AND cardetanu = '0'"
						."    AND terfue = cardetfue"
						." 	  AND terdoc = cardetdoc "
						."	  AND tercon = '$wcon' "
						."    AND teranu = '0'";
				  $rester = odbc_do($conexunix,$q);
				  odbc_fetch_row($rester);

				  $valor_tercero = odbc_result($rester,1);
				  $valor_clinica = $wval - $valor_tercero;
				  */

				  // Se obtiene porcentaje asociado a la clínica
				  $porcentaje_tercero = $wpor;
				  $porcentaje_clinica = 100 - $porcentaje_tercero;

				  // Se obtiene el valor del tercero y se redondea
				  $valor_tercero = $wval * ($porcentaje_tercero / 100);
				  $valor_tercero = round($valor_tercero);

				  // Se obtiene el valor de la clinica y se redondea
				  $valor_clinica = $wval * ($porcentaje_clinica / 100);
				  $valor_clinica = round($valor_clinica);


				  if($cont>=$limite_conceptos)
				  {
					 $total_otros_terceros += $valor_tercero;
					 $total_otros_clinica += $valor_clinica;
					 $total_otros += $valor_tercero+$valor_clinica;
				  }
			  }
			  else
			  {
				  $wtde = "";
				  $valor_tercero = 0;
				  $valor_clinica = $wval;
				  if($cont>=$limite_conceptos)
				  {
					 $total_otros_terceros += $valor_tercero;
					 $total_otros_clinica += $valor_clinica;
					 $total_otros += $valor_tercero+$valor_clinica;
				  }
			  }

			 if($cont<$limite_conceptos)
			 {
				 echo "<tr style='height:0.4cm'>";
				 echo "<td style='width:1.2cm' align='center'>".$wcon."</td>";	//Codigo concepto
				 echo "<td style='width:5.5cm' align='left'>".substr($wcde,0,28)."</td>";	//Descripcion concepto
				 echo "<td style='width:3.8cm' align='left'>".$wtde."</td>";	//Descripción tercero
				 echo "<td style='width:2cm' align='right'>".number_format( $valor_clinica, 0,'.', ',' )."</td>";	//Valor clínica
				 echo "<td style='width:2cm' align='right'>".number_format( $valor_tercero, 0,'.', ',' )."</td>";	//Valor tercero
				 echo "<td style='width:4cm;padding-right:20px' align='right'>".number_format( $wval, 0,'.', ',' )."</td>";
				 echo "</tr>";
			 }

			 $total_clinica += $valor_clinica;
			 $total_terceros += $valor_tercero;
			 $total_descuento += $descuento;
			 $total += $wval;
			 $cont++;
			}

			if($cont>=$limite_conceptos)
			{
				echo "<tr style='height:0.4cm'>";
				echo "<td align='center'></td>";
				echo "<td align='left' colspan='2'>OTROS SERVICIOS, Ver Anexo</td>";	//Descripcion concepto
				echo "<td align='right'>".number_format( $total_otros_clinica, 0,'.', ',' )."</td>";
				echo "<td align='right'>".number_format( $total_otros_terceros, 0,'.', ',' )."</td>";
				echo "<td align='right' style='padding-right:20px'>".number_format( $total_otros, 0,'.', ',' )."</td>";
				echo "</tr>";
			}

			echo "<tr style='height:0.7cm'>";
			echo "<td align='center'></td>";
			echo "<td align='left' colspan='2'>TOTAL GENERAL DE LOS SERVICIOS:</td>";	//Descripcion concepto
			echo "<td style='border:1px;border-style:dotted none none none;' align='right'>".number_format( $total_clinica, 0,'.', ',' )."</td>";	//Valor clínica
			echo "<td style='border:1px;border-style:dotted none none none;' align='right'>".number_format( $total_terceros, 0,'.', ',' )."</td>";	//Valor tercero
			echo "<td style='border:1px;border-style:dotted none none none;padding-right:20px' align='right'>".number_format( $total, 0,'.', ',' )."</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan='6' align='center'></td>";
			echo "</tr>";

		  echo "</table>";

		 echo "<br>";

		 //Consulta de copagos o cuota moderadora
		 $q =    " SELECT antfacval "
				."   FROM anantfac "
				."  WHERE antfacffa = '".$wffa."'"
				."    AND antfacdfa = '".$wfactura."'";
		 $resant = odbc_do($conexunix,$q);
		 odbc_fetch_row($resant);
		 $antfacval = odbc_result($resant,1);	//Copago o cuota moderadora

		 if(!isset($antfacval) || !$antfacval)
			$antfacval = 0;

		 if($wind=='P')
		 {
			$ant_exc = $antfacval;
			$cop_cmo_frq = 0;
		 }
		 else
		 {
			$ant_exc = 0;
			$cop_cmo_frq = $antfacval;
		 }

		 $parcial = $total;
		 $subtotal = $total-$total_descuento;
		 $iva = 0;	// IVA siempre es cero ya que en hospitalización no hay cargos que impliquen IVA
		 $total_neto = $subtotal+$iva-$cop_cmo_frq-$ant_exc;

		 //Forma de pago
		 echo "<table style='width:18.5cm;font-size:8pt;font-family:Arial'>";
		 echo "<tr style='height:3.3cm'>";
		 echo "<td style='width:12.5cm' valign='top'><br>".montoescrito( $total )."</td>";
		 echo "<td style='width:2.5cm' rowspan='3'></td>";
		 echo "<td style='width:4.5cm;padding-right:20px' rowspan='3' align='right'><br>".number_format( $total, 0,'.', ',' )."<br>".number_format( $total_descuento, 0,'.', ',' )."<br>".number_format( $subtotal, 0,'.', ',' )."<br>".number_format( $iva, 0,'.', ',' )."<br>".number_format( $ant_exc, 0,'.', ',' )."<br>".number_format( $cop_cmo_frq, 0,'.', ',' )."<br>".number_format( $total_neto, 0,'.', ',' )."</td>";
		 echo "</tr>";
		 echo "</table>";

		 //Consulta de observaciones
		 $q =    " SELECT carobsdes "
				."   FROM cacarobs "
				."  WHERE carobsfue = '".$wffa."'"
				."    AND carobsdoc = '".$wfactura."'";
		 $resobs = odbc_do($conexunix,$q);
		 odbc_fetch_row($resobs);
		 $observacion = odbc_result($resobs,1);	//

		 if(!isset($observacion) || !$observacion)
			$observacion = "";

		 echo "<table style='width:18.5cm;font-size:8pt;font-family:Arial'>";
		 echo "<tr style='height:1.4cm'>";
		 echo "<td style='width:11.2cm'></td>";	//Espacio muerto, aqui va donde dice  ELABORADO POR      RECIBI CONFORME
		 echo "<td style='width:7.3cm' align='left' valign='top'>".$observacion."</td>";		//Observaciones
		 echo "</tr>";
		 echo "</table>";

		 $q =    " SELECT logusu, logter, logfec "
				."   FROM falog "
				."  WHERE logva1 = '".$wffa."'"
				."    AND logva2 = '".$wdoc."'";
		 $reslog = odbc_do($conexunix,$q);
		 if(odbc_fetch_row($reslog))
		 {
			 $logusu = odbc_result($reslog,1);	//Usuario
			 $logter = odbc_result($reslog,2);	//
			 $logfec = odbc_result($reslog,3);	//Fecha
			 $logstrfec = explode(" ",$logfec);
			 $logfec = $logstrfec[0];
			 $loghor = $logstrfec[1];	//Hora
			 $logstrper = explode("-",$logfec);
			 $logper = $logstrper[0]."-".$logstrper[1];	//Periodo
		 }
		 else
		 {
			 $q =    " SELECT logusu, logter, logfec "
					."   FROM aylog "
					."  WHERE logva1 = '".$wffa."'"
					."    AND logva2 = '".$wdoc."'";
			 $reslog = odbc_do($conexunix,$q);
			 if(odbc_fetch_row($reslog))
			 {
				 $logusu = odbc_result($reslog,1);	//Usuario
				 $logter = odbc_result($reslog,2);	//
				 $logfec = odbc_result($reslog,3);	//Fecha
				 $logstrfec = explode(" ",$logfec);
				 $logfec = $logstrfec[0];
				 $loghor = $logstrfec[1];	//Hora
				 $logstrper = explode("-",$logfec);
				 $logper = $logstrper[0]."-".$logstrper[1];	//Periodo
			 }
			 else
			 {
				 $logusu = "";	//Usuario
				 $logter = "";	//
				 $logfec = "";	//Fecha
				 $loghor = "";	//Hora
				 $logper = "";	//Periodo
			 }
		 }

		 echo "<table style='width:18.5cm;font-size:8pt;font-family:Arial'>";
		 echo "<tr style='height:0.8cm'>";
		 echo "<td align='left'>Fecha: ".$logfec." &nbsp; &nbsp; &nbsp; &nbsp; Hora: ".$loghor." &nbsp; &nbsp; &nbsp; &nbsp; Usu.: ".$logusu." &nbsp; &nbsp; &nbsp; &nbsp; Term.: ".$logter." &nbsp; &nbsp; &nbsp; &nbsp; Per.: ".$logper." </td>";		//Pie de pagina de la factura
		 echo "</tr>";
		 echo "</table>";

		 $existen_facturas = 'on';

		 auditoria( $wfactura, $wced, $whis,$wing, date("Y-m-d"), $wval, $wpaquete );

		 // return;
		}
		if($existen_facturas=='off')
			echo "<div align='center'><br>No se encontraron datos para la factura</div>";

		//echo "</td></tr></table>";
		echo "</div>";
	}


session_start();

if (!isset($user))
	{
	 if(!isset($_SESSION['user']))
		session_register("user");
	}

if(!isset($_SESSION['user']))
	echo "error";
else
{
  

  include_once("root/comun.php");
  include_once("root/montoescrito.php");

  


  //@$conexunix = odbc_pconnect('informix','informix','sco') or die("No se ralizo Conexion con el Unix");	//2012-02-29
  @$conexunix = odbc_connect('facturacion','informix','sco') or die("No se ralizo Conexion con el Unix");

  $pos = strpos($user,"-");
		       $wusuario = substr($user,$pos+1,strlen($user));


  		                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(2011-08-24)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

  echo "<form name='impfacunix' action='imp_factura_unix_conf.php' method=post>";

  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

  mostrar_empresa($wemp_pmla);

  if ( !isset($wenvia))
     {
	  encabezado("Imprimir Factura Unix",$wactualiz, "clinica");

	 if(!isset($wparam))
		$wparam = "0";

	 if($wparam!="1")
		seleccionarPaquete(&$wpaq);

	  echo "<br><br>";
	  echo "<center><table>";
	  echo "<tr class=subtituloPagina><td align=right>Espacio superior: ";
	  echo "<input type='text' name='wtop' size='15'>";
	  echo "</td></tr>";
	  echo "<tr class=subtituloPagina><td align=right>Espacio izquierdo: ";
	  echo "<input type='text' name='wleft' size='15'>";
	  echo "</td></tr>";
	  echo "<tr class=subtituloPagina><td align=right>Nro de Factura: ";
	  echo "<input type='text' name='wfactura' size='15'>";
      echo "<input type='HIDDEN' name='wparam' value='".$wparam."'>";
      echo "<input type='HIDDEN' name='wenvia' value='1'>";
	  echo "</td></tr>";
	  echo "<tr class=subtituloPagina><td align=right>";
	  echo "<input type='submit' name='benviar' value='Enviar'>";
	  echo "</td></tr>";
	 }
	else
       {
	    echo "<input type='HIDDEN' name='wfactura' value='".$wfactura."'>";

	   //On
	   // echo $wfactura."<br>";

        if($wparam!="1")
			imprimir_factura($wfactura);
		else
			imprimir_factura_detalle($wfactura,$wtop,$wleft);

       }
	odbc_close($conexunix);
	odbc_close_all();
} // if de register
?>
