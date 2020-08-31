<head>
  <title>IMPRIMIR FACTURA UNIX-PARTICULARES</title>
</head>

<script type="text/javascript">
    function enter(e)
    {
        var esIE=(document.all);
        var esNS=(document.layers);
        var tecla=(esIE) ? event.keyCode : e.which;
        if (tecla==13) return true;
        else return false;
    }

    function submit_form()
    {
        document.forms.impfacunip.submit();
    }

    // Se crea esta función javascript para controlar los radio button
    // muchas veces se desea imprimir la factura sin seleccionar ninguna opción
    // si antes se seleccionó una opción se puede desmarcar nuevamente dandole clic.
    var era;
    var previo=null;
    function desSeleccionar(rbutton)
    {
        if(previo && previo != rbutton)
            { previo.era = false; }
        if(rbutton.checked == true && rbutton.era == true)
            { rbutton.checked = false;}
        rbutton.era=rbutton.checked;
        previo=rbutton;
    }

</script>

<body>
<?php
include_once("conex.php");
/***************************************************
*            IMPRIMIR FACTURA DE UNIX             *
*            CONEX, FREE => OK                    *
***************************************************/

//==================================================================================================================================
//PROGRAMA                   : imp_factura_unixp.php
//AUTOR                      : Gustavo Alberto Avendaño Rivera
//FECHA CREACION             : Agosto 24 de 2012
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz=" Octubre 07 de 2013";
//DESCRIPCION
//==================================================================================================================================
//Este programa es con base al programa imp_factura_unix y se necesita para imprimira facturas de particulares
//==================================================================================================================================

//==================================================================================================================================
//MODIFICACIONES ===================================================================================================================
//==================================================================================================================================
// 2012-06-23   :   Se creó la variable $wfecegr que permite mostrar la fecha de salida el paciente
//==================================================================================================================================
// 2012-05-15   :   Se crea función javascript para desmarcar las opciones de 'NO POS', esto porque varias veces se puede elegir imprimir sin
//                  marcar ninguna opción.
//==================================================================================================================================
// 2012-05-09   :   Se adicionó una nueva opción al momento de imprimir la factura, ahora se puede seleccionar entre generar la factura
//                  con conceptos NO POS, o generar la factura NO POS para cirugía con el concepto 'PROCEDIMIENTOS NO POS'.
//                  esto permite que al seleccionar la opción 'NO POS (Cirugía)' se mostrará un solo concepto en la factura con las cifras totalizadas
//==================================================================================================================================
// 2012-04-03 - Se adicionó la opción de impresion de facturas NO POS y la sleccion de impresora, de modo que según la impresora se
// definen los margenes superior e izquierdo de impresion, para esto se crearon las tablas del grupo de facturacion hospitalaria:
// fachos_000001 -  Maestro de conceptos NO POS, si en el formulario se seleccionó NO POS y se encuentra el código del concepto en esta
//                  tabla se toma la descripcion de esta tabla y no la UNIX
// fachos_000002 -  Movimiento impresion de facturas, para grabar la auditoria de las impresiones de facturación
// fachos_000003 -  Configuracion impresoras facturacion, determina que margen superior e izquierda se debe dejar según la impresora seleccionada
//==================================================================================================================================
// 2012-03-14 - Se creo la función 'imprimir_factura_detalle' que permite imprimir la factura con todos los conceptos de ésta,
// con el valor cargado a la clínica y el valor cargado a terceros, además de las observaciones y el log del pie de página
//==================================================================================================================================
// 2013-10-07 - Se creo un query de consulta antes de entrar a inpac para ver si exixte y que no traiga null
//==================================================================================================================================


  function auditoria( $wfac, $wdid,$whis,$wing, $wfec, $wval, $wpla, $wparam )
    {

      global $conex;
      global $wchequeo;
      global $wfacturacion;
      global $user;

      list( $a, $usuario ) = explode( "-", $user );

      $fecha = date( "Y-m-d" );
      $hora = date( "H:i:s" );

      if($wparam!="1")
        $q = "INSERT INTO ".$wchequeo."_000002 (      Medico   , Fecha_data , Hora_data ,   Impfac  ,  Impdid   ,   Imphis  ,  Imping   ,  Impfec   ,    Impusu    ,  Impval   ,  Imppla   ,   Seguridad    ) "
          ."                           VALUES('".$wchequeo."','".$fecha."','".$hora."','".$wfac."','".$wdid."','".$whis."','".$wing."','".$wfec."','".$usuario."','".$wval."','".$wpla."','C-".$usuario."') ";
      else
        $q = "INSERT INTO ".$wfacturacion."_000002 (      Medico   , Fecha_data , Hora_data ,   Impfac  ,  Impdid   ,   Imphis  ,  Imping   ,  Impfec   ,    Impusu    ,  Impval   ,  Imppla   ,   Seguridad    ) "
          ."                           VALUES('".$wfacturacion."','".$fecha."','".$hora."','".$wfac."','".$wdid."','".$whis."','".$wing."','".$wfec."','".$usuario."','".$wval."','".$wpla."','C-".$usuario."') ";

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
      global $wfacturacion;

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

              if ($row[0] == "Facturacion hospitalaria")
                 $wfacturacion=$row[1];
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

  function imprimir_factura($wfactura, $wparam)
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

         echo "<br><br>";

         //1ra fila: Fecha y fecha de venciemiento
         echo "<table style='width:18.5cm;font-size:10pt;font-family:Courier New'>";
         echo "<tr style='height:0.8cm'>";
         echo "<td style='width:12.5cm'></td>";
         echo "<td style='width:0.8cm' align='right'>".substr($wfec,8,2)."</td>";   //Fecha factura
         echo "<td style='width:0.8cm' align='right'>".substr($wfec,5,2)."</td>";   //Fecha factura
         echo "<td style='width:1.4cm' align='right'>".substr($wfec,0,4)."</td>";   //Fecha factura
         echo "<td style='width:0.8cm' align='right'>".substr($wfev,8,2)."</td>";   //Fecha de vecimiento
         echo "<td style='width:0.8cm' align='right'>".substr($wfev,5,2)."</td>";   //Fecha de vecimiento
         echo "<td style='width:1.4cm' align='right'>".substr($wfev,0,4)."</td>";   //Fecha de vecimiento
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
         echo "<td style='width:9.7cm'>".$wpac."</td>";                             //Paciente
         echo "<td style='width:0.8cm' align='right'>".substr($wfec,8,2)."</td>";   //Fecha de ingreso
         echo "<td style='width:0.8cm' align='right'>".substr($wfec,5,2)."</td>";   //Fecha de ingreso
         echo "<td style='width:1.4cm' align='right'>".substr($wfec,0,4)."</td>";   //Fecha de ingreso
         echo "<td style='width:0.8cm' align='right'>".substr($wfec,8,2)."</td>";   //Fecha de salida
         echo "<td style='width:0.8cm' align='right'>".substr($wfec,5,2)."</td>";   //Fecha de salida
         echo "<td style='width:1.4cm' align='right'>".substr($wfec,0,4)."</td>";   //Fecha de salida
         echo "<td style='width:2.8cm' align='center'>".$whis."-".$wing."</td>";    //Estadia
         echo "</tr>";

         echo "<tr  style='height:0.8cm'><td></td></tr>";   //Este espacio es el usado por documento de identidad y tipo de atencion
         echo "<tr  style='height:0.3cm'><td></td></tr>";   //Espacio muerto, aqui va donde dice RESOLUCION DIAN No. 1100....
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

         auditoria( $wfactura, $wced, $whis,$wing, date("Y-m-d"), $wval, $wpaquete, $wparam );

         // return;
        }
    }

function imprimir_factura_detalle($wfactura, $wparam, $wnopos, $wimpresora)
{
    global $conex;
    global $conexunix;
    global $wusuario;
    global $wfacturacion;

    $wffa = "20";       // Fuente de facturas
    $existen_facturas = 'off';      // Indicador que determinado
    $wpaquete = "";	// Paquete seleccionado. Usado solo para chequeo ejecutivo

    $q = " SELECT carano, carmes, carfec, carfev, carcep, carpac, carced, carres, carval, carhis, carnum, pacdir , pactel, carced, carind "
      ."   FROM cacar, inpaci "
      ."  WHERE carfue = '".$wffa."' "
      ."    AND cardoc = '".$wfactura."' "
      ."    AND carfuo = '01' "
      ."    AND carhis = pachis "
      ."    AND carind = 'P'"
      ."    AND caranu = '0' "
      ." UNION ALL"
      ." SELECT carano, carmes, carfec, carfev, carcep, carpac, carced, carres, carval, carhis, carnum, movdir pacdir , movtel pactel, movcer carced, carind "
      ."   FROM cacar, aymov "
      ."  WHERE carfue = '".$wffa."' "
      ."    AND cardoc = '".$wfactura."' "
      ."    AND carfuo <> '01' "
      ."    AND carfuo = movfue "
      ."    AND carhis = movdoc"
      ."    AND carind = 'P'"
      ."    AND caranu = '0' ";

    $res = odbc_do($conexunix,$q);

	//echo $q."<br>";

    while( odbc_fetch_row($res) )
    {
        $wano = odbc_result($res,1);
        $wmes = odbc_result($res,2);
        $wfec = odbc_result($res,3);    //Fecha
        $wfev = odbc_result($res,4);    //Fecha de vencimiento
        $wcep = odbc_result($res,5);    //Documento paciente
        $wpac = odbc_result($res,6);    //Paciente
        $wced = odbc_result($res,7);    //Nro de documento
        $wres = odbc_result($res,8);    //Responsable
        $wval = odbc_result($res,9);    //Valor factura
        $whis = odbc_result($res,10);   //Historia clinica
        $wing = odbc_result($res,11);   //Ingreso
        $wdir = odbc_result($res,12);   //Direccion responsable
        $wtel = odbc_result($res,13);   //Telefono responsable
        $wcod = odbc_result($res,14);   //Nit responsable
        $wind = odbc_result($res,15);   //Indicador empresa o particular

        $qdiv =  " SELECT nitdig "
             ."   FROM conit "
             ."  WHERE nitnit = '".$wcod."' ";
        $resdiv = odbc_do($conexunix,$qdiv);
        $wdiv = odbc_result($resdiv,1); //Digito de verificacion

        // Si no se encuentra digito de verificación por defecto es cero
        if(!isset($wdiv) || $wdiv=="")
        $wdiv = '0';

        // Busco los espacios a dejar en el encabezado y la izquierda segun la impresora seleccionada
        $q = " SELECT cimtop, cimlef "
          ."   FROM ".$wfacturacion."_000003 "
          ."  WHERE cimnom = '".$wimpresora."'"
          ."    AND cimusu = '".$wusuario."'"
          ."    AND cimest = 'on' ";
        $rescim = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $numcim = mysql_num_rows($rescim);
        $rowcim = mysql_fetch_array($rescim);
        $wtop = $rowcim['cimtop'];
        $wleft = $rowcim['cimlef'];

        //echo "<br>";
        echo "<div style='position: absolute;top:".$wtop."px;left:".$wleft."px'>";

        //1ra fila: Fecha y fecha de venciemiento
        echo "<table style='width:18.5cm;font-size:9pt;font-family:Arial'>";
        echo "<tr style='height:1.2cm'>";
        echo "<td style='width:12.7cm' align='center' valign='middle'>No. ".$wfactura."</td>";
        echo "<td style='width:0.7cm' align='right' valign='middle'>".substr($wfec,8,2)."</td>";            //Fecha
        echo "<td style='width:0.7cm' align='right' valign='middle'>".substr($wfec,5,2)."</td>";            //Fecha
        echo "<td style='width:1.3cm' align='right' valign='middle'>".substr($wfec,0,4)."</td>";            //Fecha
        echo "<td style='width:0.7cm' align='right' valign='middle'>".substr($wfev,8,2)."</td>";            //Fecha de vencimiento
        echo "<td style='width:0.7cm' align='right' valign='middle'>".substr($wfev,5,2)."</td>";            //Fecha de vencimiento
        echo "<td style='width:1.3cm' align='right' valign='middle'>".substr($wfev,0,4)."</td>";            //Fecha de vencimiento
        echo "<td style='width:0.4cm'></td>";                                                               //Espacio vacio
        echo "</tr>";
        echo "</table>";

        //No se neceista bordes para la tabla al imprimir
        //2da fila: Responsable, nit, domicilio, telefono
        echo "<table style='width:18.5cm;font-size:10pt;font-family:Arial'>";
        echo "<tr style='height:0.7cm'>";
        echo "<td style='width:7.7cm'>".substr($wres,0,34)."</td>"; // Nombre responsable
        echo "<td style='width:3.1cm'>".trim($wcod)."-".$wdiv."</td>";  // NIT responsable
        echo "<td style='width:5.7cm' valign='top'>".substr($wdir,0,24)."</td>";    // Domicilio responsable
        echo "<td style='width:2cm' valign='top'>".substr($wtel,0,8)."</td>";   // Telefono responsable
        echo "</tr>";
        echo "</table>";


        // Cuento para ver si hay fecha de ingreso en pacientes activos, si no busco en inactivos.
		$query=" SELECT count(*)
				   FROM inpac
				  WHERE pachis = '".$whis."'
					AND pacnum = '".$wing."' ";

		$rescant = odbc_do($conexunix,$query);

		while( odbc_fetch_row($rescant) )
        {
         $wcant = odbc_result($rescant,1);
		}

        IF ($wcant>0)
		{
         // Encuentro la fecha de ingreso del paciente
         $query=" SELECT pacfec
		 		    FROM inpac
				   WHERE pachis = '".$whis."'
				 	 AND pacnum = '".$wing."' ";
         $err_o = odbc_exec($conexunix,$query);
         if(odbc_fetch_row($err_o))
         {
			$wfecing=odbc_result($err_o,1);
			$wfecegr="          ";
         }
         else
         {
			$query="SELECT egring, egregr
					  FROM inmegr
					 WHERE egrhis = '".$whis."'
					   AND egrnum = '".$wing."' ";
			$err_1 = odbc_exec($conexunix,$query);
			//echo $query."<br>";

			if (odbc_fetch_row($err_1))
			{
				$wfecing=odbc_result($err_1,1);
				$wfecegr=odbc_result($err_1,2);
			}
			else
			{
				$wfecing=$wfec;
				$wfecegr=$wfec;
			}
         }

		}
		ELSE
		{
		   $query="SELECT egring, egregr
				     FROM inmegr
					WHERE egrhis = '".$whis."'
					  AND egrnum = '".$wing."' ";
		   $err_1 = odbc_exec($conexunix,$query);
		   //echo $query."<br>";

		   if (odbc_fetch_row($err_1))
		   {
			 $wfecing=odbc_result($err_1,1);
			 $wfecegr=odbc_result($err_1,2);
		   }
		   else
		   {
			$wfecing=$wfec;
			$wfecegr=$wfec;
		   }
		}

		// Cuento para ver si hay fecha de ingreso en ayudas.
		$query="SELECT b.movfec
               FROM famov a, aymov b
              WHERE a.movfue = '20'
                AND a.movdoc = '".$wfactura."'
                AND a.movfuo = b.movfue
                AND a.movhis = b.movdoc
                AND a.movanu = '0'
                AND b.movanu = '0' ";

		$rescant = odbc_do($conexunix,$query);

		while( odbc_fetch_row($rescant) )
        {
         $wcant = odbc_result($rescant,1);
		}

        IF ($wcant>0)
		{
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
		  //echo $query."<br>";

          if(odbc_fetch_row($err_ay))
          {
			$wfecing=odbc_result($err_ay,1);
			if(!isset($wfecegr))
			{
				$wfecegr=$wfec;
			}
          }
		}

        //Fila del paciente
        echo "<table style='width:18.5cm;font-size:9pt;font-family:Arial'>";
        echo "<tr style='height:0.7cm'>";
        echo "<td style='width:9.9cm'>".substr($wpac,0,42)."</td>";                             //Paciente
        echo "<td style='width:0.7cm' align='right' valign='top'>".substr($wfecing,8,2)."</td>";//Fecha de ingreso
        echo "<td style='width:0.7cm' align='right' valign='top'>".substr($wfecing,5,2)."</td>";//Fecha de ingreso
        echo "<td style='width:1.3cm' align='right' valign='top'>".substr($wfecing,0,4)."</td>";//Fecha de ingreso
        echo "<td style='width:0.7cm' align='right' valign='top'>".substr($wfecegr,8,2)."</td>";   //Fecha de salida
        echo "<td style='width:0.7cm' align='right' valign='top'>".substr($wfecegr,5,2)."</td>";   //Fecha de salida
        echo "<td style='width:1.3cm' align='right' valign='top'>".substr($wfecegr,0,4)."</td>";   //Fecha de salida
        echo "<td style='width:3.2cm' align='center' valign='top'>".$whis."-".$wing."</td>";    //Estadia
        echo "</tr>";
        echo "</table>";

        echo "<table style='width:18.5cm;font-size:9pt;font-family:Arial'>";
        echo "<tr style='height:0.7cm'>";
        echo "<td style='width:3cm'>".$wcep."</td>";                    //Documento de identidad del paciente
        echo "<td style='width:15.5cm'>HOSPITALIZADO - PENSIONADO</td>";//Tipo de atencion
        echo "</tr>";
        echo "</table>";

        echo "<table style='width:18.5cm;font-size:8pt;font-family:Arial'>";
        echo "<tr style='height:0.8cm'>";
        echo "<td style='width:10.6cm'></td>";  //Espacio muerto, aqui va donde dice CPTO.    DESCRIPCION
        echo "<td style='width:2.1cm' align='right' valign='bottom'>CLINICA</td>";  //Encabezado CLINICA
        echo "<td style='width:2.1cm' align='right' valign='bottom'>TERCEROS</td>"; //Encabezado TERCEROS
        echo "<td style='width:3.8cm;padding-right:20px' align='right' valign='bottom'>TOTAL</td>"; //Encabezado TOTAL
        echo "</tr>";
        echo "</table>";

        //Descripcion de pago
        $q = " SELECT movdetcon, movdetval*conmul, movdetnit, movdetfue, movdetdoc, connom, movdetvde "
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
        $cx_no_pos = false;
        $wcxnopos = 'cxnopos';

        /** 2012-05-09
         * Este bloque de código se adiciona para validar y consultar la descripción del concepto cuando se va a imprimir una factura
         * y se seleccionó la opción 'NO POS CIRUGÍA'
         */
        if (isset($wnopos) && $wnopos == '2')
        {
            $qcx = " SELECT concod, condes "
                ."   FROM ".$wfacturacion."_000001 "
                ."  WHERE concod = '".$wcxnopos."'"
                ."    AND conest = 'on' ";
                $rescx = mysql_query($qcx,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcx." - ".mysql_error());
                $numcx = mysql_num_rows($rescx);
                if($numcx>0)
                {
                    $rowcx = mysql_fetch_array($rescx);
                    $wcde = trim($rowcx['condes']);  //Descripcion concepto
                    $cx_no_pos = true;
                }
                else
                {
                    $wcde = 'NO POS';  //Descripcion concepto
                }
        }

        echo "<table style='width:18.5cm;height:6.6cm;font-size:8pt;font-family:Arial'>";
        while( odbc_fetch_row($resdes) )
        {
            $wcon = odbc_result($resdes,1); //Codigo concepto
            $wval = odbc_result($resdes,2); //Valor del concepto
            $wnit = odbc_result($resdes,3); //NIT
            $wfue = odbc_result($resdes,4); //Fuente
            $wdoc = odbc_result($resdes,5); //Documento
            $descuento = odbc_result($resdes,7);	//Valor de descuento

            if (!$cx_no_pos) // Si no se seleccionar ver concepto cirugía NO POS - 2012-05-09
            {
                if($wnopos=='1')
                {
                    $q = " SELECT concod, condes "
                    ."   FROM ".$wfacturacion."_000001 "
                    ."  WHERE concod = '".$wcon."'"
                    ."    AND conest = 'on' ";
                    $resnopos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $numnopos = mysql_num_rows($resnopos);
                    if($numnopos>0)
                    {
                        $rownopos = mysql_fetch_array($resnopos);
                        $wcde = trim($rownopos['condes']);  //Descripción concepto
                    }
                    else
                    {
                        $wcde = trim(odbc_result($resdes,6));   //Descripción concepto
                    }
                }
                else
                {
                    $wcde = trim(odbc_result($resdes,6));   //Descripción concepto
                }
            }

            //Consulto datos del tercero (Porcentaje y Nombre)
            $q =   " SELECT connitpor, nitnom "
            ."   FROM faconnit, conit "
            ."  WHERE connitcon = '$wcon'"
            ."    AND connitnit = '$wnit'"
            ."    AND connitnit = nitnit"
			."   GROUP BY 1,2";
            $rescon = odbc_do($conexunix,$q);
            odbc_fetch_row($rescon);
            $wpor = odbc_result($rescon,1);	//Porcentaje tercero
            $wnom = odbc_result($rescon,2);	//Nombre tercero
            $wtde = $wnit." ".substr($wnom,0,11);
            if($wpor && $wpor>0)
            {

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

            if($cont<$limite_conceptos && !$cx_no_pos)
            {
                echo "<tr style='height:0.4cm'>";
                echo "<td style='width:1.2cm' align='center'>".$wcon."</td>";               //Codigo concepto
                echo "<td style='width:5.5cm' align='left'>".substr($wcde,0,28)."</td>";    //Descripción concepto
                echo "<td style='width:3.8cm' align='left'>".$wtde."</td>";                 //Descripción tercero
                echo "<td style='width:2cm' align='right'>".number_format( $valor_clinica, 0,'.', ',' )."</td>";    //Valor clínica
                echo "<td style='width:2cm' align='right'>".number_format( $valor_tercero, 0,'.', ',' )."</td>";    //Valor tercero
                echo "<td style='width:4cm;padding-right:20px' align='right'>".number_format( $wval, 0,'.', ',' )."</td>";
                echo "</tr>";
            }

            $total_clinica += $valor_clinica;
            $total_terceros += $valor_tercero;
            $total_descuento += $descuento;
            $total += $wval;
            $cont++;
        }

        // Si se seleccionó ver cirugía NO POS, en este bloque de código se muestra un solo concepto y las cifras totalizadas - 2012-05-09
        if ($cx_no_pos)
        {
            echo "<tr style='height:0.4cm'>";
            echo "<td style='width:1.2cm' align='center'>&nbsp;</td>";               //Codigo concepto
            echo "<td style='width:5.5cm' align='left'>".substr($wcde,0,28)."</td>";    //Descripcion concepto
            echo "<td style='width:3.8cm' align='left'>&nbsp;</td>";                 //Descripción tercero
            echo "<td style='width:2cm' align='right'>".number_format( $total_clinica, 0,'.', ',' )."</td>";    //Valor clínica
            echo "<td style='width:2cm' align='right'>".number_format( $total_terceros, 0,'.', ',' )."</td>";    //Valor tercero
            echo "<td style='width:4cm;padding-right:20px' align='right'>".number_format( $total, 0,'.', ',' )."</td>";
            echo "</tr>";
            $cont = 0; // Se reinicia el contador para que no muestre 'OTROS SERVICIOS, Ver Anexo' en la factura. El valor de anexos ya está incluido en el total
        }

        if($cont>=$limite_conceptos)
        {
            echo "<tr style='height:0.4cm'>";
            echo "<td align='center'></td>";
            echo "<td align='left' colspan='2'>OTROS SERVICIOS, Ver Anexo</td>";    //Descripcion concepto
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
        echo "<td style='width:12.5cm' valign='top'><br>".montoescrito( $total_neto )."</td>";
        echo "<td style='width:2.5cm' rowspan='3'></td>";
        echo "<td style='width:4.5cm;padding-right:20px' rowspan='3' align='right'><br>".number_format( $total, 0,'.', ',' )."<br>".number_format( $total_descuento, 0,'.', ',' )."<br>".number_format( $subtotal, 0,'.', ',' )."<br>".number_format( $iva, 0,'.', ',' )."<br>".number_format( $ant_exc, 0,'.', ',' )."<br>".number_format( $cop_cmo_frq, 0,'.', ',' )."<br>".number_format( $total_neto, 0,'.', ',' )."</td>";
        echo "</tr>";
        echo "</table>";

        //Consulta de observaciones
        $q = " SELECT carobsdes "
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
            $logusu = odbc_result($reslog,1);   //Usuario
            $logter = odbc_result($reslog,2);   //
            $logfec = odbc_result($reslog,3);   //Fecha
            $logstrfec = explode(" ",$logfec);
            $logfec = $logstrfec[0];
            $loghor = $logstrfec[1];    //Hora
            $logstrper = explode("-",$logfec);
            $logper = $logstrper[0]."-".$logstrper[1];  //Periodo
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
                $logusu = odbc_result($reslog,1);   //Usuario
                $logter = odbc_result($reslog,2);   //
                $logfec = odbc_result($reslog,3);   //Fecha
                $logstrfec = explode(" ",$logfec);
                $logfec = $logstrfec[0];
                $loghor = $logstrfec[1];    //Hora
                $logstrper = explode("-",$logfec);
                $logper = $logstrper[0]."-".$logstrper[1];  //Periodo
            }
            else
            {
                $logusu = "";   //Usuario
                $logter = "";   //
                $logfec = "";   //Fecha
                $loghor = "";   //Hora
                $logper = "";   //Periodo
            }
        }

        echo "<table style='width:18.5cm;font-size:8pt;font-family:Arial'>";
        echo "<tr style='height:0.8cm'>";
        echo "<td align='left'>Fecha: ".$logfec." &nbsp; &nbsp; &nbsp; &nbsp; Hora: ".$loghor." &nbsp; &nbsp; &nbsp; &nbsp; Usu.: ".$logusu." &nbsp; &nbsp; &nbsp; &nbsp; Term.: ".$logter." &nbsp; &nbsp; &nbsp; &nbsp; Per.: ".$logper." </td>";		//Pie de pagina de la factura
        echo "</tr>";
        echo "</table>";

        $existen_facturas = 'on';

        auditoria( $wfactura, $wced, $whis,$wing, date("Y-m-d"), $wval, $wpaquete, $wparam );

        // return;
    }

	if($existen_facturas=='off')
		echo "<div align='center'><br>No se encontraron datos para la factura o es de EMPRESA</div>";

    echo "</div>";
}


session_start();

if (!isset($user))
{
    if(!isset($_SESSION['user']))
        session_register("user");
}

if(!isset($_SESSION['user']))
{
	echo "error";
}
else
{
    

    include_once("root/comun.php");
    include_once("root/montoescrito.php");

    


    //@$conexunix = odbc_pconnect('informix','informix','sco') or die("No se ralizo Conexion con el Unix");	//2012-02-29
    @$conexunix = odbc_connect('facturacion','informix','sco') or die("No se ralizo Conexion con el Unix");

    $pos = strpos($user,"-");
    $wusuario = substr($user,$pos+1,strlen($user));


                                                        // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
    $wactualiz=" Octubre 07 de 2013 ";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
                                                        // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

    echo "<form name=impfacunip action='imp_factura_unixp.php' method=post>";

    echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

    if(!isset($wparam))
    { $wparam = "0"; }

    mostrar_empresa($wemp_pmla);

    if ( !isset($wenvia))
    {
        encabezado("Imprimir Factura Unix",$wactualiz, "clinica");

        if($wparam!="1")
        seleccionarPaquete($wpaq);

        echo "<br>";
        echo "<center><table border='0'>";

        if($wparam=="1")
        {
            // Consulta de impresora asociada al usuario actual
            $q =   " SELECT cimnom "
            ."   FROM ".$wfacturacion."_000003 "
            ."  WHERE cimusu = '".$wusuario."' "
            ."	  AND cimest = 'on' ";
            //echo $q."<br>";
            $res_impusu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num_impusu = mysql_num_rows($res_impusu);
            $row_impusu = mysql_fetch_array($res_impusu);
            $impusu = $row_impusu[0];

            // Consulta de impresoras
            $q =   " SELECT cimnom "
            ."   FROM ".$wfacturacion."_000003 "
            ."  WHERE cimest = 'on' "
            ."	GROUP BY cimnom ";
            $res_imps = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num_imps = mysql_num_rows($res_imps);

            // Selección de impresora
            echo "<tr><td height='31'><b> Impresora </b></td>";

            // Campo select de impresoras
            echo "<td>";
            echo "<select name='wimpresora' id='wimpresora'>";
            for ($i=1;$i<=$num_imps;$i++)
            {
                $row_imps = mysql_fetch_array($res_imps);
                if(isset($impusu) && $impusu != $row_imps[0])
                {
					echo "<option value='".$row_imps[0]."'>".$row_imps[0]."</option>";
				}
                else
                {
					echo "<option value='".$row_imps[0]."' selected>".$row_imps[0]."</option>";
				}
            }
            echo "</select></td></tr>";
        }
        echo "<tr><td height='31'><b>Nro de Factura:</b></td> ";
        echo "<td><input type='text' name='wfactura' id='wfactura' size='15'></td></tr>";
        if($wparam=="1")
        {
            echo "  <tr>
            <td height='31' align='center'><input type='radio' name='wnopos' value='1' onclick='desSeleccionar(this);'> NO POS (Otros)</td>
            <td height='31' align='center'><input type='radio' name='wnopos' value='2' onclick='desSeleccionar(this);'> NO POS (Cirug&iacute;a)</td>
            </tr>";
        }

        echo "<tr><td height='37' valign='bottom' colspan='2' align='center'><input type='submit' name='imprimir' value='imprimir'></td></tr>";
        echo "<input type='HIDDEN' name='wparam' value='".$wparam."'>";
        echo "<input type='HIDDEN' name='wenvia' value='1'>";

    }
    else
    {
        if(isset($wnopos))
        {
			$wnopos = $wnopos;
		}
        else
        {
			$wnopos = '0';
		}

        echo "<input type='HIDDEN' name='wfactura' value='".$wfactura."'>";
        if(isset($wimpresora))
        echo "<input type='HIDDEN' name='wimpresora' value='".$wimpresora."'>";
        echo "<input type='HIDDEN' name='wnopos' value='".$wnopos."'>";

        //On
        // echo $wfactura."<br>";

        if($wparam!="1")
        {
			imprimir_factura($wfactura, $wparam);
		}
        else
		{
			imprimir_factura_detalle($wfactura, $wparam, $wnopos, $wimpresora );
		}
    }
	
	odbc_close($conexunix);
	odbc_close_all();
	
} // if de register
?>
