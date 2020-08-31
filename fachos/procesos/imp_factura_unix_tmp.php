<head>
  <title>IMPRIMIR FACTURA UNIX</title>
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
        document.forms.impfacunix.submit();
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
//PROGRAMA                   : imp_factura_unix.php
//AUTOR                      : Juan Carlos Hernández M.
//FECHA CREACION             : Agosto 23 de 2011
//FECHA ULTIMA ACTUALIZACION :

 //DESCRIPCION
//==================================================================================================================================
//Este programa se hace para imprimir las facturas de Unix que se requieran imprimir con aspecto diferente al que sale desde Unix
//==================================================================================================================================

//==================================================================================================================================
//MODIFICACIONES ===================================================================================================================
//==================================================================================================================================
/*
==================================================================================================================================
    2012-11-19(Edwar Jaramillo):    Se realizan correcciones a los calculos cuando son facturas PAF, se estaba restando dos veces el valor del concepto 2105
                                    Adicionalmente de actualiza la función montoescrito() puesto que al tratar de imprimir un número mayor a nueve cifras se mostraba un
                                    mensaje informando que no se podía el texto para esa cifra.
==================================================================================================================================
    2012-11-16(Edwar Jaramillo):    Se adiciona una nueva opción de impresión de factura, se denomina facturas PAF,
                                    Para esto se insertó una nueva opción en fachos_00001 con el código "PAF-2105" donde "2105" en este caso corresponde
                                    al código del concepto que no debe ser sumado junto con el resto de conceptos de la factura pero que se debe mostrar
                                    en el subtotal en una fila adicional, también se crea un campo en el formulario para pedir la fuente de la factura.

                                    En la función imprimir_factura_detalle se cambia el primer sql que aparece, solo cuanso es el tipo PAF, esto se hace porque
                                    al seleccionar este tipo se encontró con que la consulta de unix retornaba valores nulos que dañaban el programa al ejecutarlo.
==================================================================================================================================
    2012-08-29(Viviana Rodas):      Se modifico para cuando el paciente sea de ayudas diagnosticas no muestre fecha de salida.
==================================================================================================================================
    2012-08-28(Viviana Rodas):      Se modifico el valor que imprime el montoescrito por el total neto.
                                    El limite de conceptos se cambio de 13 a 10 para evitar que se bajen los otros valores cuando la factura tenga
                                       muchos conceptos.
                                    Se agrego sum(antfacval) en la consulta a la tabla anantfac para que sume los abonos.
                                    Se agrego para las observaciones un count para saber cuantas lineas trae y asi hacer las consultas correspondientes.
==================================================================================================================================
    2012-06-23   :   Se creó la variable $wfecegr que permite mostrar la fecha de salida el paciente
==================================================================================================================================
    2012-05-15   :  Se crea función javascript para desmarcar las opciones de 'NO POS', esto porque varias veces se puede elegir imprimir sin
                    marcar ninguna opción.
==================================================================================================================================
    2012-05-09  :   Se adicionó una nueva opción al momento de imprimir la factura, ahora se puede seleccionar entre generar la factura
                    con conceptos NO POS, o generar la factura NO POS para cirugía con el concepto 'PROCEDIMIENTOS NO POS'.
                    esto permite que al seleccionar la opción 'NO POS (Cirugía)' se mostrará un solo concepto en la factura con las cifras totalizadas
==================================================================================================================================
    2012-04-03 -    Se adicionó la opción de impresion de facturas NO POS y la sleccion de impresora, de modo que según la impresora se
                    definen los margenes superior e izquierdo de impresion, para esto se crearon las tablas del grupo de facturacion hospitalaria:
                    fachos_000001 -  Maestro de conceptos NO POS, si en el formulario se seleccionó NO POS y se encuentra el código del concepto en esta
                                     tabla se toma la descripcion de esta tabla y no la UNIX
                    fachos_000002 -  Movimiento impresion de facturas, para grabar la auditoria de las impresiones de facturación
                    fachos_000003 -  Configuracion impresoras facturacion, determina que margen superior e izquierda se debe dejar según la impresora seleccionada
==================================================================================================================================
    2012-03-14 -    Se creo la función 'imprimir_factura_detalle' que permite imprimir la factura con todos los conceptos de ésta,
                    con el valor cargado a la clínica y el valor cargado a terceros, además de las observaciones y el log del pie de página
==================================================================================================================================
*/

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

function imprimir_factura_detalle($wfactura, $wparam, $wnopos, $wimpresora, $wffa)
{
    global $conex;
    global $conexunix;
    global $wusuario;
    global $wfacturacion;

    //$wffa = "20";       // Fuente de facturas    // Se pone entre comentarios porque antes estaba quemado pero ahora se pide desde el formulario - 2012-11-16
    $existen_facturas = 'off';      // Indicador que determinado
    $wpaquete = "";	// Paquete seleccionado. Usado solo para chequeo ejecutivo

    $q = " SELECT carano, carmes, carfec, carfev, carcep, carpac, carced, carres, carval, carhis, carnum, empdir, emptel, empnit, carind "
          ."   FROM cacar, inemp "
          ."  WHERE carfue = '".$wffa."' "
          ."    AND cardoc = '".$wfactura."' "
          ."    AND carced = empcod "
          ."    AND caranu = '0' ";

    // 2012-11-16
    // Si es del tipo PAF entonces no se requiere carcep ni carhis, porque se va a mostrar es una sumatoria, se debe entonces devolver '0' en esos dos campos
    // para evitar valores nulos que dañan el programa.
    // El siguiente query se crea porque para las facturas tipo PAF se estaba detectando valores nulos que hacían fallar el programa
    if (isset($wnopos) && $wnopos == '3')
    {
        $q = "
                SELECT  carano, carmes, carfec, carfev, carcep, carpac, carced, carres, carval, carhis, carnum, empdir, emptel, empnit, carind
                FROM    cacar, inemp
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
                        AND caranu = '0'
                        AND carcep IS NOT NULL
                        AND carhis IS NOT NULL
                        AND carnum IS NOT NULL

                UNION

                SELECT  carano, carmes, carfec, carfev, carcep, carpac, carced, carres, carval, carhis, 0 AS carnum, empdir, emptel, empnit, carind
                FROM    cacar, inemp
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
                        AND caranu = '0'
                        AND carcep IS NOT NULL
                        AND carhis IS NOT NULL
                        AND carnum IS NULL
                UNION

                SELECT  carano, carmes, carfec, carfev, carcep, carpac, carced, carres, carval, 0 AS carhis, carnum, empdir, emptel, empnit, carind
                FROM    cacar, inemp
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
                        AND caranu = '0'
                        AND carcep IS NOT NULL
                        AND carhis IS NULL
                        AND carnum IS NOT NULL
                UNION

                SELECT  carano, carmes, carfec, carfev, carcep, carpac, carced, carres, carval, 0 AS carhis, 0 AS carnum, empdir, emptel, empnit, carind
                FROM    cacar, inemp
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
                        AND caranu = '0'
                        AND carcep IS NOT NULL
                        AND carhis IS NULL
                        AND carnum IS NULL
                UNION

                SELECT  carano, carmes, carfec, carfev, '.' AS carcep, carpac, carced, carres, carval, carhis, carnum, empdir, emptel, empnit, carind
                FROM    cacar, inemp
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
                        AND caranu = '0'
                        AND carcep IS NULL
                        AND carhis IS NOT NULL
                        AND carnum IS NOT NULL
                UNION

                SELECT  carano, carmes, carfec, carfev, '.' AS carcep, carpac, carced, carres, carval, carhis, 0 AS carnum, empdir, emptel, empnit, carind
                FROM    cacar, inemp
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
                        AND caranu = '0'
                        AND carcep IS NULL
                        AND carhis IS NOT NULL
                        AND carnum IS NULL
                UNION

                SELECT  carano, carmes, carfec, carfev, '.' AS carcep, carpac, carced, carres, carval, 0 AS carhis, carnum, empdir, emptel, empnit, carind
                FROM    cacar, inemp
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
                        AND caranu = '0'
                        AND carcep IS NULL
                        AND carhis IS NULL
                        AND carnum IS NOT NULL
                UNION

                SELECT  carano, carmes, carfec, carfev, '.' AS carcep, carpac, carced, carres, carval, 0 AS carhis, 0 AS carnum, empdir, emptel, empnit, carind
                FROM    cacar, inemp
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
                        AND caranu = '0'
                        AND carcep IS NULL
                        AND carhis IS NULL
                        AND carnum IS NULL
                ";
    }
    // echo '<pre>';print_r($q);echo '</pre>';
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

        // Busco los espacios a dejar en el encabezado y la izquierda según la impresora seleccionada
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

        if ($wnopos == '3')// 2012-11-16 se busca el nombre real y no el nombre del contrato al que pertenece el nit
        {
            $qNIT = "   SELECT  nitnit, nitnom
                        FROM    conit
                        WHERE   nitnit = '".trim($wcod)."'";
            $resNIT = odbc_do($conexunix,$qNIT);
            odbc_fetch_row($resNIT);
            $wres = odbc_result($resNIT,2);
        }


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

        $wfecing='0000-00-00';
        $wfecegr='0000-00-00';
        // 2012-11-16 en muchas ocaciones se hacen consultas para historias con tipo 0, pero generan datos nulos y dañan el programa, por defecto se pone la fecha en 0000-00-00
        //
        if($whis && trim($whis)!='' && $whis!='0')
		{
			if ($wnopos != '3')
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
		}
		else
		{
			$wfecing="";
			$wfecegr="";
			$whis="";
			$wing="";
		}

        // Si es ayuda diagnóstica encuentro la fecha en aymov
        // 2012-11-16 antes estaba quemado a.movfue = '20' y se cambió por a.movfue = '".$wffa."'
        $query="SELECT b.movfec
               FROM famov a, aymov b
              WHERE a.movfue = '".$wffa."'
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
            $wfecegr="";
        }

        //Fila del paciente
        echo "<table style='width:18.5cm;font-size:9pt;font-family:Arial'>";
        echo "<tr style='height:0.7cm'>";
        echo "<td style='width:9.9cm'>".substr($wpac,0,42)."</td>";                             //Paciente
        echo "<td style='width:0.7cm' align='right' valign='top'>".substr($wfecing,8,2)."</td>";//Fecha de ingreso
        echo "<td style='width:0.7cm' align='right' valign='top'>".substr($wfecing,5,2)."</td>";//Fecha de ingreso
        echo "<td style='width:1.3cm' align='right' valign='top'>".substr($wfecing,0,4)."</td>";//Fecha de ingreso
        //si es de ayudas diagnosticas no muestra fecha de salida
        if(odbc_fetch_row($err_ay))
            {
                echo "<td style='width:0.7cm' align='right' valign='top'>".$wfecegr."</td>";   //Fecha de salida
                echo "<td style='width:0.7cm' align='right' valign='top'>".$wfecegr."</td>";   //Fecha de salida
                echo "<td style='width:1.3cm' align='right' valign='top'>".$wfecegr."</td>";   //Fecha de salida
            }
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
        $limite_conceptos = 10;
        $total_otros_clinica = 0;
        $total_otros_terceros = 0;
        $total_otros = 0;
        $cont = 0;
        $total_clinica = 0;
        $total_terceros = 0;
        $total_descuento = 0;
        $total_clinica_paf = 0;
        $total_terceros_paf = 0;
        $total_descuento_paf = 0;
        $total_desc_paf = 0;
        $cod_paf_con = 0;
        $total = 0;
        $cx_no_pos = false;
        $hay_paf = false;
        $wcxnopos = 'cxnopos';
        $wf_paf = 'PAF-%';

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

        /** 2012-11-16
         * Este bloque de código se adiciona para validar y consultar la descripción del concepto COPAGO
         *
         */
        if (isset($wnopos) && $wnopos == '3')
        {
            $qcx = " SELECT concod, condes "
                ."   FROM ".$wfacturacion."_000001 "
                ."  WHERE concod LIKE '".$wf_paf."'"
                ."    AND conest = 'on' ";
                $rescx = mysql_query($qcx,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcx." - ".mysql_error());
                $numcx = mysql_num_rows($rescx);
                if($numcx>0)
                {
                    $rowcx = mysql_fetch_array($rescx);
                    $wcde = trim($rowcx['condes']);  //Descripcion concepto
                    $cod_paf_exp = trim($rowcx['concod']);  //Código de concepto tipo PAF
                    $cod_paf_exp = explode('-',$cod_paf_exp);
                    $cod_paf_con = $cod_paf_exp[1];
                    $cx_no_pos = true;
                }
                else
                {
                    $wcde = 'PAF';  //Descripcion concepto
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
            $descuento = odbc_result($resdes,7);    //Valor de descuento

            if (!$cx_no_pos) // Si no se selecciona ver concepto cirugía NO POS - 2012-05-09
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
                        $wcde = trim($rownopos['condes']);  //Descripcion concepto
                    }
                    else
                    {
                        $wcde = trim(odbc_result($resdes,6));   //Descripcion concepto
                    }
                }
                else
                {
                    $wcde = trim(odbc_result($resdes,6));   //Descripcion concepto
                }
            }

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
                $valor_tercero = round($valor_tercero,5);

                // Se obtiene el valor de la clinica y se redondea
                $valor_clinica = $wval * ($porcentaje_clinica / 100);
                $valor_clinica = round($valor_clinica,5);


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

            if($cont<$limite_conceptos && !$cx_no_pos) //********************************************************************
            {
                echo "<tr style='height:0.4cm'>";
                echo "<td style='width:1.2cm' align='center'>".$wcon."</td>";               //Codigo concepto
                echo "<td style='width:5.5cm' align='left'>".substr($wcde,0,28)."</td>";    //Descripcion concepto
                echo "<td style='width:3.8cm' align='left'>".$wtde."</td>";                 //Descripción tercero
                echo "<td style='width:2cm' align='right'>".number_format( $valor_clinica, 0,'.', ',' )."</td>";    //Valor clínica
                echo "<td style='width:2cm' align='right'>".number_format( $valor_tercero, 0,'.', ',' )."</td>";    //Valor tercero
                echo "<td style='width:4cm;padding-right:20px' align='right'>".number_format( $wval, 0,'.', ',' )."</td>";
                echo "</tr>";
            }

            // if($wcon != $cod_paf_con)
            {
                $total_clinica += $valor_clinica;
                $total_terceros += $valor_tercero;
                $total_descuento += $descuento;
            }
            $total += $wval;

            if($wcon == $cod_paf_con) // 2012-11-16 para sumar todo lo que es del concepto tipo PAF
            {
                $total_clinica_paf += $valor_clinica;
                $total_terceros_paf += $valor_tercero;
                $total_descuento_paf += $descuento;
                $total_desc_paf += ($wval < 0) ? ($wval*(-1)) : $wval;
                $hay_paf = true;

                if($wval < 0)           { $total +=  $wval*(-1); }
                if($valor_clinica < 0)  { $total_clinica +=  $valor_clinica*(-1); }
                if($valor_tercero < 0)  { $total_terceros +=  $valor_tercero*(-1); }
                if($descuento < 0)      { $total_descuento +=  $descuento*(-1); }
            }
            $cont++;
        }

        // Si se seleccionó ver cirugía NO POS, en este bloque de código se muestra un solo concepto y las cifras totalizadas - 2012-05-09
        if ($cx_no_pos)
        {
            // if (isset($wnopos) && $wnopos == '2')
            {
                echo "<tr style='height:0.4cm'>";
                echo "<td style='width:1.2cm' align='center'>&nbsp;</td>";               //Codigo concepto
                echo "<td style='width:5.5cm' align='left'>".substr($wcde,0,28)."</td>";    //Descripcion concepto
                echo "<td style='width:3.8cm' align='left'>&nbsp;</td>";                 //Descripción tercero
                echo "<td style='width:2cm' align='right'>".number_format( $total_clinica, 0,'.', ',' )."</td>";    //Valor clínica
                echo "<td style='width:2cm' align='right'>".number_format( $total_terceros, 0,'.', ',' )."</td>";    //Valor tercero
                echo "<td style='width:4cm;padding-right:20px' align='right'>".number_format( $total, 0,'.', ',' )."</td>";
                echo "</tr>";
            }

            if(isset($wnopos) && $wnopos == '3' && $hay_paf)  // 2012-11-16 para mostrar todo lo que es del concepto tipo PAF
            {
                $q = "  SELECT  concod,connom
                        FROM    facon
                        WHERE   concod = '".$cod_paf_con."'";
                $resDescPaf = odbc_do($conexunix,$q);

                $desconPaf = odbc_result($resDescPaf,2);

                $total_desc_paf = ($total_desc_paf < 0) ? $total_desc_paf: ($total_desc_paf*(-1));

                echo "<tr style='height:0.4cm'>";
                echo "<td style='width:1.2cm' align='center'>".$cod_paf_con."</td>";               //Codigo concepto
                echo "<td style='width:5.5cm' align='left'>".substr($desconPaf,0,28)."</td>";    //Descripcion concepto
                echo "<td style='width:3.8cm' align='left'>&nbsp;</td>";                 //Descripción tercero
                echo "<td style='width:2cm' align='right'>".number_format( $total_clinica_paf, 0,'.', ',' )."</td>";    //Valor clínica
                echo "<td style='width:2cm' align='right'>".number_format( $total_terceros_paf, 0,'.', ',' )."</td>";    //Valor tercero
                echo "<td style='width:4cm;padding-right:20px' align='right'>".number_format( $total_desc_paf, 0,'.', ',' )."</td>";
                echo "</tr>";

                // Como lo que es tipo PAF se va a relacionar en una fila aparte entonces lo que se sumó en rotales paf se le resta a la sumatoria de todos los conceptos
                $total_clinica = ($total_clinica_paf < 0) ? $total_clinica + $total_clinica_paf : $total_clinica - $total_clinica_paf;
                $total_terceros = ($total_terceros_paf < 0) ? $total_terceros + $total_terceros_paf : $total_terceros - $total_terceros_paf;
                $total = ($total_desc_paf < 0) ? $total + $total_desc_paf : $total - $total_desc_paf; // para el primer caso debe ser (+) porque $total_desc_paf tiene un valor negativo.
            }
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

        //se hace count para saber si tiene resultados los copagos o cuota moderadora
        $q =    " SELECT count(*) "
            ."   FROM anantfac "
            ."  WHERE antfacffa = '".$wffa."'"
            ."    AND antfacdfa = '".$wfactura."'";
        $resant1 = odbc_do($conexunix,$q);
        $antfacval1 = odbc_result($resant1,1);

        if ($antfacval1>0)
        {
            //Consulta de copagos o cuota moderadora
            $q =    " SELECT SUM(antfacval) "
                ."   FROM anantfac "
                ."  WHERE antfacffa = '".$wffa."'"
                ."    AND antfacdfa = '".$wfactura."'";
            $resant = odbc_do($conexunix,$q);
            odbc_fetch_row($resant);
            $antfacval = odbc_result($resant,1);	//Copago o cuota moderadora
        }
        else
        {
            $antfacval=0;
        }

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
        $iva = 0;   // IVA siempre es cero ya que en hospitalización no hay cargos que impliquen IVA
        $total_neto = $subtotal+$iva-$cop_cmo_frq-$ant_exc;

        //Forma de pago
        echo "<table style='width:18.5cm;font-size:8pt;font-family:Arial'>";
        echo "<tr style='height:3.3cm'>";
        echo "<td style='width:12.5cm' valign='top'><br>".montoescrito( $total_neto )."</td>";  //***********************************************
        echo "<td style='width:2.5cm' rowspan='3'></td>";
        echo "<td style='width:4.5cm;padding-right:20px' rowspan='3' align='right'><br>".number_format( $total, 0,'.', ',' )."<br>".number_format( $total_descuento, 0,'.', ',' )."<br>".number_format( $subtotal, 0,'.', ',' )."<br>".number_format( $iva, 0,'.', ',' )."<br>".number_format( $ant_exc, 0,'.', ',' )."<br>".number_format( $cop_cmo_frq, 0,'.', ',' )."<br>".number_format( $total_neto, 0,'.', ',' )."</td>";
        echo "</tr>";
        echo "</table>";

        $observacionFinal = "";
        //Consulta para saber cuantas lineas de observaciones tiene la factura

        $query =" SELECT COUNT(*) "
               ." FROM cacarobs "
               ." WHERE carobsfue = '".$wffa."'"
               ." AND carobsdoc = '".$wfactura."' ";

        $res = odbc_do($conexunix,$query);
        $lineas= odbc_result($res,1);
        //echo $lineas;

        if($lineas==0)
        {
            $observacionFinal = "";
        }
        else if ($lineas==1)
        {
            //Consulta de observaciones
             $q =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='1'";
            $resobs = odbc_do($conexunix,$q);
            odbc_fetch_row($resobs);

            $observacion = odbc_result($resobs,1);
        }
        else if ($lineas==2)
        {
            //Consulta de observaciones
             $q =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='1'";
            $resobs = odbc_do($conexunix,$q);
            odbc_fetch_row($resobs);

            //Consulta de observaciones
             $q1 =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='2'";
            $resobs1 = odbc_do($conexunix,$q1);
            odbc_fetch_row($resobs1);

            $observacion = odbc_result($resobs,1)." ".odbc_result($resobs1,1);
        }
        else
        {
            //Consulta de observaciones
             $q =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='1'";
            $resobs = odbc_do($conexunix,$q);
            odbc_fetch_row($resobs);

            //Consulta de observaciones
             $q1 =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='2'";
            $resobs1 = odbc_do($conexunix,$q1);
            odbc_fetch_row($resobs1);

            //Consulta de observaciones
             $q2 =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='3'";
            $resobs2 = odbc_do($conexunix,$q2);
            odbc_fetch_row($resobs2);

            $observacion = odbc_result($resobs,1)." ".odbc_result($resobs1,1)." ".odbc_result($resobs2,1);
        }


        $numCaracteres = strlen($observacion);

        if ($numCaracteres > 150)
        {
            $observacionFinal = substr( $observacion, 0, 150 );
        }
        else
        {
            $observacionFinal=$observacion;
        }

        //*************************************************************************************

        // if(!isset($observacion) || !$observacion)
        // $observacionFinal = "";

        echo "<table style='width:18.5cm;font-size:8pt;font-family:Arial'>";
        echo "<tr style='height:1.4cm'>";
        echo "<td style='width:11.2cm'></td>";	//Espacio muerto, aqui va donde dice  ELABORADO POR      RECIBI CONFORME
        //echo "<td style='width:7.3cm' align='left' valign='top'><font size='1'>".$observacion."</font></td>";		//Observaciones
        echo "<td style='width:7.3cm' align='left' valign='top'><font size='1'>".$observacionFinal."</font></td>";
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
        echo "<div align='center'><br>No se encontraron datos para la factura</div>";

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
    $wactualiz=" Noviembre 16 de 2012 ";                // Aca se coloca la ultima fecha de actualizacion de este programa //
                                                        // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

    echo "<form name=impfacunix action='' method=post>";

    echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

    if(!isset($wparam))
    { $wparam = "0"; }

    mostrar_empresa($wemp_pmla);

    if ( !isset($wenvia))
    {
        encabezado("Imprimir Factura Unix",$wactualiz, "clinica");

        if($wparam!="1")
        seleccionarPaquete(&$wpaq);

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
        echo "  <tr>
                    <td height='31'><b>Fuente de Factura:</b></td>
                    <td><input type='text' name='wffa' id='wffa' size='3' maxlength='4' value='20'></td>
                </tr>
                <tr>
                    <td height='31'><b>Nro de Factura:</b></td>
                    <td><input type='text' name='wfactura' id='wfactura' size='15'></td>
                </tr>";
        if($wparam=="1")
        {
            echo "
            <tr>
                <td colspan='2'>
                    <table>
                        <tr style='font-size:10pt;'>
                            <td height='31' align='center'><input type='radio' name='wnopos' value='1' onclick='desSeleccionar(this);'> NO POS (Otros)</td>
                            <td height='31' align='center'><input type='radio' name='wnopos' value='2' onclick='desSeleccionar(this);'> NO POS (Cirug&iacute;a)</td>
                            <td height='31' align='center'><input type='radio' name='wnopos' value='3' onclick='desSeleccionar(this);'> Factura (PAF)</td>
                        </tr>
                    </table>
                </td>
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
        if(isset($wffa))
        {
            $wffa = $wffa;
        }
        else
        {
            $wffa = '20';
        }

        echo "<input type='HIDDEN' name='wfactura' value='".$wfactura."'>";
        if(isset($wimpresora))
        echo "<input type='HIDDEN' name='wimpresora' value='".$wimpresora."'>";

        echo "<input type='HIDDEN' name='wnopos' value='".$wnopos."'>";
        echo "<input type='HIDDEN' name='wffa' value='".$wffa."'>";

        //On
        // echo $wfactura."<br>";

        if($wparam!="1")
        {
            imprimir_factura($wfactura, $wparam);
        }
        else
        {
            // Se limpia la fuente de factura de caracteres especiales que puedan romper el query por este campo
            $wffa = str_replace('"','',str_replace("'","",trim($wffa)));
            $wffa = str_replace('\\','',str_replace("/","",$wffa));

            imprimir_factura_detalle($wfactura, $wparam, $wnopos, $wimpresora, $wffa);
        }
    }
	odbc_close($conexunix);
	odbc_close_all();
	
} // if de register
?>