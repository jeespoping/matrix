<html>
<head>
  <title>ENTREGA Y RECIBO DE PRENDAS A LAVANDERIA</title>
</head>

<script type="text/javascript">

  // Validación para movimientos con factor 1
  function valida_envio(i)
  {
    var inventario, saldo, disponible, cantidad;

    if(isNaN(document.getElementById('wcantidad'+i).value))
    {
        alert(document.getElementById('wcantidad'+i).value+" - no es un valor válido");
        document.getElementById('wcantidad'+i).value = "";
        document.getElementById('wcantidad'+i).focus();
        return false;
    }

    inventario = parseInt(document.getElementById('winventario'+i).value);
    disponible = parseInt(document.getElementById('wdisponible'+i).value);
    saldo = parseInt(document.getElementById('wsaldo'+i).value);
    cantidad = parseInt(document.getElementById('wcantidad'+i).value);
    if((cantidad+saldo+disponible)>inventario)
    {
        alert("La suma de las prendas a enviar, disponibles y en lavandería no puede ser mayor al inventario");
        document.getElementById('wcantidad'+i).value = "";
        document.getElementById('wcantidad'+i).focus();
        return false;
    }

    if((cantidad)<0)
    {
        alert("La cantidad a enviar no puede ser menor que cero");
        document.getElementById('wcantidad'+i).value = "";
        document.getElementById('wcantidad'+i).focus();
        return false;
    }

    return true;
  }

// Validación para movimientos con factor -1
function valida_recibo(i)
  {
    var inventario, saldo, disponible, cantidad;

    if(isNaN(document.getElementById('wcantidad'+i).value))
    {
        alert(document.getElementById('wcantidad'+i).value+" - no es un valor válido");
        document.getElementById('wcantidad'+i).value = "";
        document.getElementById('wcantidad'+i).focus();
        return false;
    }

    saldo = parseInt(document.getElementById('wsaldo'+i).value);
    cantidad = parseInt(document.getElementById('wcantidad'+i).value);

    if(cantidad>saldo)
    {
        alert("La cantidad a recibir no puede ser mayor al saldo en lavandería");
        document.getElementById('wcantidad'+i).value = "";
        document.getElementById('wcantidad'+i).focus();
        return false;
    }

    if(cantidad<0)
    {
        alert("La cantidad a recibir no puede ser menor que cero");
        document.getElementById('wcantidad'+i).value = "";
        document.getElementById('wcantidad'+i).focus();
        return false;
    }

    return true;
  }

// Asigna el total de los pesos ingresados en el campo de "Peso total"
function sumar_pesos(wpeso)
    {
        var pesototal;

        if(isNaN(document.getElementById(wpeso).value))
        {
            alert(document.getElementById(wpeso).value+" - no es un valor válido");
            document.getElementById(wpeso).value = "0";
            document.getElementById(wpeso).focus();
            return false;
        }

        if(document.getElementById(wpeso).value<0)
        {
            alert("El peso no puede ser negativo");
            document.getElementById(wpeso).value = "0";
            document.getElementById(wpeso).focus();
            return false;
        }

        pesototal = parseInt(document.getElementById('wpesoalta').value)+parseInt(document.getElementById('wpesobaja').value)+parseInt(document.getElementById('wpesomojada').value);
        document.getElementById('wpeso').value = pesototal;
    }

// Oculta el mensaje de datos actualizados
function ocultar_msj()
    {
        div = document.getElementById('msjActualiza');
        div.style.display='none';
    }

// Cambio de ronda
function enter_ronda()
    {
        document.getElementById('envio').value='0';
        document.forms.invlav.submit();
    }

// Definir concepto como reproceso
function establecer_reproceso()
    {
        if(document.getElementById('reproceso').checked == true)
        {
            document.getElementById('envio').value='0';
            document.getElementById('wreproceso').value='1';
            document.getElementById('wactualizar').value='0';
            document.getElementById('wrondasel').value='';
            document.getElementById('wcambio_concepto').value='1';
            document.forms.invlav.submit();
        }
        else
        {
            document.getElementById('envio').value='0';
            document.getElementById('wreproceso').value='0';
            document.getElementById('wactualizar').value='0';
            document.getElementById('wrondasel').value='';
            document.getElementById('wcambio_concepto').value='1';
            document.forms.invlav.submit();
        }
    }

// Envio del formulario
function enter()
    {
        if(document.getElementById('wfeclim').value > document.getElementById('wfecmov').value)
        {
            alert("No se pueden hacer movimientos para la fecha seleccionada. seleccione una más reciente");
            //document.getElementById('wcantidad'+i).value = "";
            return false;
        }
        /*
        // MUESTRA MENSAJE DE CONFIRMACIÓN SI SE VA A GRABAR SIN REGISTROS EN LA ÚLTIMA RONDA DEL DÍA ANTERIOR
        // SE COMENTA PORQUE YA HAY UN ALERT AL CARGAR LA PÁGINA Y NO SE CONSIDERA ENCESARIO MAS MENSAJES
        if(document.getElementById('wayer').value == '0')
        {
            if(!confirm("No se han registrados movimientos para la última ronda del día anterior, realmente desea registrar los movimientos para el día actual?"))
                return false;
        }
        */

        document.forms.invlav.submit();
    }

// Envio del formulario
function enter_select()
    {
        document.forms.invlav.submit();
    }

function existe_cambios(cant_inic)
{
    var existe = false;
    var valores = cant_inic.split("-");
    var i = 0;
    while(document.getElementById('wcantidad'+i))
    {
        if(document.getElementById('wcantidad'+i).value!=valores[i])
        {
            existe = true;
        }
        i++;
    }

    return existe;
}

// Vuelve a la página anterior llevando sus parámetros
function retornar(wemp_pmla,f,cant_inic)
    {
        if(cant_inic)
        {
            if(existe_cambios(cant_inic))
            {
                if(!confirm("Se perderán los últimos cambios realizados. ¿Realmente desea salir?"))
                    return false;
            }
        }
        location.href = "stock_lavanderia.php?wemp_pmla="+wemp_pmla+"&f="+f;
    }

// Vuelve a la página anterior llevando sus parámetros
function retornar_reproceso(wemp_pmla,f,wlav)
    {
        location.href = "stock_lavanderia.php?wemp_pmla="+wemp_pmla+"&f="+f+"&wlav="+wlav;
    }

// Cierra la ventana
function cerrar_ventana(cant_inic)
    {
        if(cant_inic)
        {
            if(existe_cambios(cant_inic))
            {
                if(!confirm("Se perderán los últimos cambios realizados. ¿Realmente desea salir?"))
                    return false;
            }
        }
        window.close();
    }

</script>
<body>

<?php
include_once("conex.php");
  /******************************************************
   *   ENTREGA Y RECIBO DE PRENDAS A LAVANDERIA         *
   ******************************************************/
    /*
     ********** DESCRIPCIÓN *****************************
     * Permite registrar el envío y recibo de prendas   *
     * a lavanderia.                                    *
     ****************************************************
     * Autor: John M. Cadavid. G.                       *
     * Fecha creacion: 2011-05-14                       *
     * Modificado:                                      *
     ****************************************************************************************************************************************
     * 2013-02-26   Se adicionó la condición if($rowlav['Lavind']!='E' && $rowlav['Lavind']!='S') de modo que si es una lavandería de 		*
     *              entrada o salida se consulten todas las prendas y no se filtren por lavandería									        *
     ****************************************************************************************************************************************
     * 2011-08-29   Cambio en el update de los movimientos en la tabla 000111 ya que estaba comparando con Fecha_data y debe ser con Enlfec *
     *              para que tome la fecha del movimiento, no la del registro. También se adicionó un alert al cargar la página y un        *
     *              confirm cuando no se ha registrado el último movimiento del día anterior - Mario Cadavid                                *
     ****************************************************************************************************************************************
    */

include_once("root/comun.php");

session_start();

//Inicio
//Inicio
if(!isset($_SESSION['user']))
    terminarEjecucion("<div align='center'>Usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar a Matrix.</div>");
else    // Si el usuario está registrado inicia el programa
{

  


  // Obtengo los datos del usuario
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));

  // Aca se coloca la ultima fecha de actualización
  $wactualiz = " Febrero 26 de 2013";

  echo "<br>";
  echo "<br>";

  //**********************************************//
  //********** F U N C I O N E S *****************//
  //**********************************************//

  // Consulta los datos de las aplicaciones
  function datos_empresa($wemp_pmla)
    {
      global $user;
      global $conex;
      global $wbasedato;
      global $wtabcco;
      global $winstitucion;

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

              if ($row[0] == "movhos")
                 $wbasedato=$row[1];

              if ($row[0] == "tabcco")
                 $wtabcco=$row[1];

             }
         }
        else
           echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";

      $winstitucion=$row[2];

    }

  // Define si la hora actual se encuentra entre los rangos de horas
  // asignados en las rondas. Se basa en tabla 000102
  function rango_hora ()
    {
        global $conex;
        global $whora;
        global $wbasedato;
        global $codigo_concepto;
        global $ronda;
        global $codigo_ronda;
        //global $orden_ronda;
        //global $orden_ronda_select;
        global $jornada;

        $rango = false;

        // Seleccion de ronda atoumaticamente
        $q = " SELECT Roncod, Ronhin, Ronhfi, Ronnom, Roncon, Ronjor "
            ."   FROM ".$wbasedato."_000102 "
            ."  WHERE Roncon = '".$codigo_concepto."'"
            ."    AND Ronest = 'on' ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        for($i=0;$i<$num;$i++)
        {
            $row = mysql_fetch_array($res);
            if($whora>$row['Ronhin'] && $whora<$row['Ronhfi'])
              {
                $rango = true;
                $ronda = $row['Ronnom'];
                $codigo_ronda = $row['Roncod'];
                //$orden_ronda = $row['Ronord'];
                //$orden_ronda_select = $row['Ronord']; // Solo para usar en el select de recorridos ya que la variable anterior cambia y en el select se necesita el orden según la hora que el que da esta función
                $jornada = $row['Ronjor'];
              }
        }

        return $rango;
    }

  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // En PHP5 $HTTP_POST_VARS no funciona o no esta activa, en PHP5 se usa $_POST, para evitar cambiar
  // todas las líneas en que aparezca $HTTP_POST_VARS en este script, se le asigna $_POST a $HTTP_POST_VARS
  // Esta actualización se hace el 10-Abr-2012
  $HTTP_POST_VARS = $_POST;

  // Obtengo los datos de la empresa
  datos_empresa($wemp_pmla);
  $rowsal=NULL;
  // Se define el formulario principal de la página
  echo "<form name='invlav' action='stock_lavanderia.php' method=post>";

  // Asignación de fecha y hora actual
  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");

  // Asigno valores iniciales para fecha y hora
  if(!isset($wfecmov) || $wfecmov=="")
    $wfecmov = $wfecha;

  if($f=='e')
  {
    $factor = '1';
    $excluido = '';
  }
  elseif($f=='s')
  {
    $factor = '-1';
    $excluido = " AND Lavind <> 'S' AND Lavind <> 'E' ";
  }
  else
  {
    $factor = '0';
    $excluido = '';
  }

  // Definición de campos ocultos con los valores de las variables a tener en cuenta en el programa
  echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='hidden' name='f' value='".$f."'>";
  echo "<input type='hidden' name='wseguridad' id='wseguridad' value='".$wusuario."'>";

    $check_activo = '';
    if(isset($wreproceso) && $wreproceso=='1')
        $check_activo = ' checked';
    else
        $wreproceso = '0';

    if(isset($wreproceso) && $wreproceso=='1')
        // Obtengo los datos del concepto actual
        $qcon =  " SELECT Concod, Condes, Conpes, Tcocod, Tconom, Tcoafe, Tcofac, Ronnom "
                ."   FROM ".$wbasedato."_000100, ".$wbasedato."_000101, ".$wbasedato."_000102 "
                ."  WHERE Rontip = 'Externa'"
                ."    AND Ronhin <= '".$whora."' "
                ."    AND Ronhfi > '".$whora."' "
                ."    AND Ronest = 'on' "
                ."    AND Roncon = Concod "
                ."    AND Conrep = 'on' "
                ."    AND Conest = 'on' "
                ."    AND Contco = Tcocod "
                ."    AND Tcofac = '".$factor."' "
                ."    AND Tcoest = 'on'";
    else
        // Obtengo los datos del concepto actual
        $qcon =  " SELECT Concod, Condes, Conpes, Tcocod, Tconom, Tcoafe, Tcofac, Ronnom "
                ."   FROM ".$wbasedato."_000100, ".$wbasedato."_000101, ".$wbasedato."_000102 "
                ."  WHERE Rontip = 'Externa'"
                ."    AND Ronhin <= '".$whora."' "
                ."    AND Ronhfi > '".$whora."' "
                ."    AND Ronest = 'on' "
                ."    AND Roncon = Concod "
                ."    AND Conest = 'on' "
                ."    AND Contco = Tcocod "
                ."    AND Tcofac = '".$factor."' "
                ."    AND Tcoest = 'on'";

    $rescon = mysql_query($qcon,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcon." - ".mysql_error());
    $rowcon = mysql_fetch_array($rescon);

    // Defino las variables principales del programa
    $codigo_tipo_concepto = $rowcon['Tcocod'];
    $tipo_concepto = $rowcon['Tconom'];
    $codigo_concepto = $rowcon['Concod'];
    $concepto = $rowcon['Condes'];
    $afecta_cantidad = $rowcon['Tcoafe'];
    $factor = $rowcon['Tcofac'];
    $requiere_peso = $rowcon['Conpes'];

    $achoTabla = "width='710px'";

    // Obtener titulo de la página con base en el concepto
    $titulo = $concepto;

    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz, "clinica");


  // Si no se ha enviado datos muestre el formulario de selección de lavanderia
  if (!isset($wlav))
    {

      // Consulta de las lavanderias
      $q = " SELECT Lavcod, Lavnom "
          ."   FROM ".$wbasedato."_000108 "
          ."  WHERE Lavest = 'on' "
          .$excluido." ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);

      // Campos select de lavanderías
      echo "<table align='center'>";
      echo "<tr class=fila1><td align='center'> &nbsp; Seleccione la Lavandería :  &nbsp; </td></tr>";
      echo "</table>";
      echo "<br>";
      echo "<table align='center'>";
      echo "<tr><td align='center'><select name='wlav' onchange='enter_select()'>";
      echo "<option>&nbsp</option>";
      for ($i=1;$i<=$num;$i++)
         {
          $row = mysql_fetch_array($res);
          echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table><br><br>";

      // Botón Cerrar Ventana
      echo "<table align='center'>";
      echo "<tr><td align='center'><input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
      echo "</table>";
    }
    else    // ACA INICIA LA IMPRESION DEL INVENTARIO POR LA LAVANDERIA SELECCIONADA
    {

        // Obtengo código de la lavanderia
        $lav = explode(" - ",$wlav);
        $wlav_cod = $lav['0'];

        // Si se ha enviado el formulario de grabación
        if (isset($envio) && $envio=='1')
        {
            $i=0;

            if(isset($wrondasel) && $wrondasel!='')
            {
                $strRonda = explode(' - ',$wrondasel);
                $wronda = $strRonda['0'];
            }

            if($wactualizar>0)
            {
                // Se actualizan los datos de encabezado de movimiento en la tabla 110
                $q = "  UPDATE ".$wbasedato."_000110
                          SET Medico='Movhos', Fecha_data='$wfecha', Hora_data='$whora', Enlfec='".$wfecmov."', Enlhor='".$whormov."', Enlpal='".$wpesoalta."', Enlpba='".$wpesobaja."', Enlpmo='".$wpesomojada."', Enlest='on', Seguridad='C-".$wseguridad."'
                        WHERE Enlfec = '".$wfecha."'
                          AND Enllav = '".$wlav_cod."'
                          AND Enlron = '".$wronda."'
                          AND Enlcon = '".$codigo_concepto."' ";
                $err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            }
            else
            {
                // Se ingresan los datos de encabezado de movimiento en la tabla 110
                $q = "INSERT INTO ".$wbasedato."_000110
                                  (Medico, Fecha_data, Hora_data, Enlfec, Enlhor, Enlcon, Enllav, Enlron, Enlpal, Enlpba, Enlpmo, Enlest, Seguridad)
                           VALUES
                                  ('Movhos','$wfecha','$whora','$wfecmov','$whormov','".$codigo_concepto."', '$wlav_cod', '".$wronda."', '".$wpesoalta."', '".$wpesobaja."', '".$wpesomojada."', 'on', 'C-".$wseguridad."' )";
                $err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            }

            // Ciclo de grabación de datos en las tablas
            // de encabezados y movimientos (000110 y 000111)

            do
            {
                
                // Defino las variables a usar en la grabación
                $prenda = $HTTP_POST_VARS['wprenda'.$i];
                $inventario = $HTTP_POST_VARS['winventario'.$i];
                $disponible = $HTTP_POST_VARS['wdisponible'.$i];
                $saldo = $HTTP_POST_VARS['wsaldo'.$i];
                $saldoactual = $HTTP_POST_VARS['wsaldoactual'.$i];
                $cantidad_anterior = $HTTP_POST_VARS['wcantidad_anterior'.$i];
                $cantidad = $HTTP_POST_VARS['wcantidad'.$i];

                if($saldo=='') $saldo = 0;
                if($saldoactual=='') $saldoactual = 0;
                if($cantidad=='') $cantidad = 0;

                //echo "<br>Actual. ".$saldoactual." - Cant. ".$cantidad." - Cant Ant. ".$cantidad_anterior;
                // Si el concepto actual afecta cantidad
                if($afecta_cantidad=='on')
                {
					$saldoactual = (int)$saldoactual;
					$cantidad = (int)$cantidad;
					$cantidad_anterior = (int)$cantidad_anterior;
					
					if($factor=='1')
                    {
                        $calculo_saldo = $saldoactual + ($cantidad-$cantidad_anterior);
                    }
                    elseif($factor=='-1')
                    {
                        $calculo_saldo = $saldoactual - ($cantidad-$cantidad_anterior);
                    }
                    else
                    {
                        $calculo_saldo = $saldoactual;
                    }

                    // Consulta de las lavanderias
                    $qspl = " SELECT Splsal
                             FROM ".$wbasedato."_000109
                            WHERE Splpre = '".$HTTP_POST_VARS['wprenda'.$i]."'
                              AND Spllav = '".$wlav_cod."' ";
                    $resspl = mysql_query($qspl,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qspl." - ".mysql_error());
                    $numspl = mysql_num_rows($resspl);

                    // Si existen datos de esta prenda para la lavandería actualice
                    // Sino inserte
                    if($numspl>0)
                    {
                        // Se actualiza el valor del saldo en la tabla 109
                        $q = "  UPDATE ".$wbasedato."_000109
                                   SET Splsal = ".$calculo_saldo."
                                 WHERE Splpre = '".$HTTP_POST_VARS['wprenda'.$i]."'
                                   AND Spllav = '".$wlav_cod."' ";

                        $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    }
                    else
                    {
                        // Se ingresa el registro en la tabla 109
                        $q = "INSERT INTO ".$wbasedato."_000109
                                          (Medico, Fecha_data, Hora_data, Splpre, Spllav, Splsal, Splest, Seguridad)
                                   VALUES
                                          ('Movhos','$wfecha','$whora', '".$HTTP_POST_VARS['wprenda'.$i]."', '".$wlav_cod."', ".$calculo_saldo.", 'on', 'C-".$wseguridad."' )";
                        $err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    }

                    // Query para obtener la lavanderia actual
                    $qlav =  " SELECT Lavcod, Lavnom, Lavind "
                            ."   FROM ".$wbasedato."_000108 "
                            ."  WHERE Lavcod = '".$wlav_cod."' ";
                    $reslav = mysql_query($qlav,$conex) or die  ("Error: ".mysql_errno()." - en el query: ".$qlav." - ".mysql_error());
                    $rowlav = mysql_fetch_array($reslav);

                    if($factor=='-1')
                    {
                        // Se actualiza Existencia disponible (Preedi) en  tabla 103
                        $q = "  UPDATE ".$wbasedato."_000103
                                   SET Preedi = Preedi+".($cantidad-$cantidad_anterior)."
                                 WHERE Precod = '".$HTTP_POST_VARS['wprenda'.$i]."' ";
                        $err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    }
                    elseif($factor=='1' && $rowlav['Lavind']=='E')
                    {
                        // Se actualiza Existencia disponible (Preedi) en  tabla 103
                        $q = "  UPDATE ".$wbasedato."_000103
                                   SET Preedi = Preedi+".($cantidad-$cantidad_anterior).", Preinv = Preinv+".($cantidad-$cantidad_anterior)."
                                 WHERE Precod = '".$HTTP_POST_VARS['wprenda'.$i]."' ";
                        $err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    }
                    elseif($factor=='1' && $rowlav['Lavind']=='S')
                    {
                        // Se actualiza Existencia disponible (Preedi) en  tabla 103
                        $q = "  UPDATE ".$wbasedato."_000103
                                   SET Preinv = Preinv-".($cantidad-$cantidad_anterior)."
                                 WHERE Precod = '".$HTTP_POST_VARS['wprenda'.$i]."' ";
                        $err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    }

                    // Variable que me indicará si se actualizó el movimiento
                    // Sino se debe insertar
                    $actualizo = '';

                    // Si se debe actualizar el movimiento
                    if($wactualizar>0)
                    {
                        // Se ingresan los datos del movimiento en la tabla 111
                        $q = "UPDATE ".$wbasedato."_000111
                                 SET Medico='Movhos', Hora_data='$whora', Molcan=".$cantidad.", Molsal=".$saldo.", Molest='on', Seguridad='C-".$wseguridad."'
                               WHERE Fecha_data = '".$wfecmov."'
                                 AND Mollav = '".$wlav_cod."'
                                 AND Molron = '".$wronda."'
                                 AND Molpre = '".$HTTP_POST_VARS['wprenda'.$i]."'
                                 AND Molcon = '".$codigo_concepto."' ";
                        $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                        if(mysql_affected_rows()>0) $actualizo = '1';
                    }

                    //echo "Actualizar: ".$wactualizar." - Actualizo: ".$actualizo."<br>";
                    // si se debe ingresar un movimiento nuevo
                    if($wactualizar==0 || $actualizo=='')
                    {
                        // Se ingresan los datos del movimiento en la tabla 111
                        $q = "INSERT INTO ".$wbasedato."_000111
                                          (Medico, Fecha_data, Hora_data, Molpre, Molcon, Mollav, Molron, Molcan, Molsal, Molest, Seguridad)
                                   VALUES
                                          ('Movhos','$wfecmov','$whora', '".$HTTP_POST_VARS['wprenda'.$i]."', '".$codigo_concepto."', '$wlav_cod', '".$wronda."', ".$cantidad.", ".$saldo.", 'on', 'C-".$wseguridad."' )";
                        $err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    }

                }
                else        // Si el concepto actual no afecta cantidad
                {
                    if($wactualizar>0)
                    {
                        // Se actualizan los datos del movimiento en la tabla 111
                        $q = "UPDATE ".$wbasedato."_000111
                                 SET (Medico='Movhos', Hora_data='$whora', Molcan=".$cantidad.", Molsal=".$saldo.", Molest='on', Seguridad='C-".$wseguridad."')
                               WHERE Fecha_data = '".$wfecmov."'
                                 AND Enllav = '".$wlav_cod."'
                                 AND Enlron = '".$wronda."'
                                 AND Enlpre = '".$HTTP_POST_VARS['wprenda'.$i]."'
                                 AND Enlcon = '".$codigo_concepto."' ";
                        $err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    }
                    else
                    {
                        // Se ingresan los datos del movimiento en la tabla 111
                        $q = "INSERT INTO ".$wbasedato."_000111
                                          (Medico, Fecha_data, Hora_data, Molpre, Molcon, Mollav, Molron, Molcan, Molsal, Molest, Seguridad)
                                   VALUES
                                          ('Movhos','$wfecmov','$whora', '".$HTTP_POST_VARS['wprenda'.$i]."', '".$codigo_concepto."', '$wlav_cod', '".$wronda."', ".$cantidad.", ".$saldo.", 'on', 'C-".$wseguridad."' )";
                        $err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    }
                }

                $i++;

            } while(isset($HTTP_POST_VARS['wcantidad'.$i]));

            // Se imprime el mensaje con los resultados de la grabación
            echo "<div align='center' id='msjActualiza' style='color:#2A5DB0;'><b>Se han actualizado los datos correctamente</b></div><br><br>";

        }

        // Si el rango de hora se encuentra entre las rondas definidas
        // muestre el formulario de movimiento de inventario
        if(rango_hora())
        {
            if(isset($wrondasel) && $wrondasel!='' && $wcambio_concepto!='1')
            {
                $strRonda = explode(' - ',$wrondasel);
                $ronda = $strRonda['1'];
                $codigo_ronda = $strRonda['0'];

                // Query para obtener la ronda actual
                $qron =  " SELECT Roncod, Ronnom, Ronhin, Ronhfi "
                        ."   FROM ".$wbasedato."_000102 "
                        ."  WHERE Roncod = '".$codigo_ronda."' "
                        ."    AND Ronest = 'on' ";
                $resron = mysql_query($qron,$conex) or die  ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
                $rowron = mysql_fetch_array($resron);

                //$orden_ronda = $rowron['Ronord'];

                echo "<input type='hidden' name='wrondasel' id='wrondasel' value='".$wrondasel."'>";
            }

            /*if(!isset($orden_ronda))
                $orden_ronda = 0;
            */

			// 2013-02-26
			// Query para obtener la lavanderia actual
			$qlav =  " SELECT Lavcod, Lavnom, Lavind "
					."   FROM ".$wbasedato."_000108 "
					."  WHERE Lavcod = '".$wlav_cod."' ";
			$reslav = mysql_query($qlav,$conex) or die  ("Error: ".mysql_errno()." - en el query: ".$qlav." - ".mysql_error());
			$rowlav = mysql_fetch_array($reslav);
			
            // Query para obtener la lista de prendas
			if($rowlav['Lavind']!='E' && $rowlav['Lavind']!='S')
			{
				$q = " SELECT Precod, Predes, Prepes, Preinv, Preedi "
					."   FROM ".$wbasedato."_000103 "
					."  WHERE Prelav = '".$wlav_cod."' "
					."    AND Preest = 'on'"
					."  ORDER BY Predes, Precod ";
			}
			else
			{
				$q = " SELECT Precod, Predes, Prepes, Preinv, Preedi "
					."   FROM ".$wbasedato."_000103 "
					."  WHERE Preest = 'on'"
					."  ORDER BY Predes, Precod ";
			}
            $res = mysql_query($q,$conex) or die  ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num = mysql_num_rows($res);
            $strCantInic = '';

            // Query para saber si ya se ha actualizado los datos
            // de la lavanderia en la ronda actual y por el concepto actual
            $qact = "  SELECT a.Fecha_data fecha, a.Hora_data hora, Enlfec, Enlhor, Enlcon, Enllav, Enlron, Enlpal, Roncon, Ronjor, Enlpba, Enlpmo "
                    ."   FROM ".$wbasedato."_000110 a, ".$wbasedato."_000102 b "
                    ."  WHERE Enlfec = '".$wfecmov."' "
                    ."    AND Enllav = '".$wlav_cod."' "
                    ."    AND Enlron = '".$codigo_ronda."' "
                    ."    AND Enlron = Roncod "
                    ."    AND Roncon = '".$codigo_concepto."' "
                    ."    AND Ronest = 'on' ";
            $resact = mysql_query($qact,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qact." - ".mysql_error());
            $rowact = mysql_fetch_array($resact);
            $wactualizar = mysql_num_rows($resact);
            //echo "Wactualizar es igual a: ".$wactualizar."<br>";

            // Bloqueo de campos si ya se han realizado movimientos
            // por otros conceptos
            if($factor=='1')
                $factor_diferente = '-1';
            elseif($factor=='-1')
                $factor_diferente = '1';
            else
                $factor_diferente = '0';

            $jornada_select = $rowact['Ronjor'];
            $estado = '';
            $mostrar = 'on';
            $msjBloqueo = "";
            if($wactualizar>0 && (!isset($envio) || $envio=='0'))
            {
                // Seleccion de ronda de otros conceptos
                $qotr = "  SELECT  a.Hora_data hora, Enlhor, Tcofac "
                        ."   FROM ".$wbasedato."_000110 a, ".$wbasedato."_000102, ".$wbasedato."_000101, ".$wbasedato."_000100 "
                        ."  WHERE Tcofac = '".$factor_diferente."'"
                        ."    AND Tcoest = 'on' "
                        ."    AND Tcocod = Contco "
                        ."    AND Conest = 'on' "
                        ."    AND Concod = Roncon "
                        ."    AND Rontip = 'Externa' "
                        ."    AND Ronest = 'on' "
                        ."    AND Enlron = Roncod "
                        ."    AND Enlcon = Concod "
                        ."    AND Enllav = '".$wlav_cod."' "
                        ."    AND Ronjor = '".$jornada_select."' "
                        ."    AND Enlfec = '".$wfecmov."' "
                        ."    AND Enlest = 'on' ";
                $resotr = mysql_query($qotr,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qotr." - ".mysql_error());
                $numotr = mysql_num_rows($resotr);
                $rowotr = mysql_fetch_array($resotr);
                //echo $qotr;

                if($numotr>0 && $rowotr['hora']>$rowact['hora'])
                {
                    $estado = " readonly='readonly'";
                    $mostrar = "off";
                    // Se imprime el mensaje avisando por que no se pueden efectuar cambios
                    if($msjBloqueo=="")
                        $msjBloqueo = "<div align='center' style='color:#2A5DB0;'><b>Estos valores no se pueden cambiar debido a que ya se han realizado otros movimientos con base en éste</b></div><br><br>";
                }
            }


            if(!isset($whormov) || $whormov=="")
                $whormov = $whora;

            // Bloqueo de campos si ya se han actualizado movimientos posteriores
            $qpos = "  SELECT  Enlhor, Tcofac "
                    ."   FROM ".$wbasedato."_000110, ".$wbasedato."_000102, ".$wbasedato."_000101, ".$wbasedato."_000100 "
                    ."  WHERE Tcofac = '".$factor."'"
                    ."    AND Tcoest = 'on' "
                    ."    AND Tcocod = Contco "
                    ."    AND Conest = 'on' "
                    ."    AND Concod = Roncon "
                    ."    AND Rontip = 'Externa' "
                    ."    AND Ronest = 'on' "
                    ."    AND Enlron = Roncod "
                    ."    AND Enlcon = Concod "
                    ."    AND Concod = '".$codigo_concepto."' "
                    ."    AND Enllav = '".$wlav_cod."' "
                    ."    AND Enlhor > '".$whormov."' "
                    ."    AND Enlfec = '".$wfecmov."' "
                    ."    AND Enlest = 'on' ";
            $respos = mysql_query($qpos,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qpos." - ".mysql_error());
            $numpos = mysql_num_rows($respos);

            if($numpos>0)
            {
                $estado = " readonly='readonly'";
                $mostrar = "off";
                if($msjBloqueo=="")
                    $msjBloqueo = "<div align='center' style='color:#2A5DB0;'><b>Estos valores no se pueden cambiar debido a que ya se han realizado movimientos en un recorrido posterior a éste</b></div><br><br>";
                // Se imprime el mensaje avisando por que no se pueden efectuar cambios
            }

            echo $msjBloqueo;


            // Establece última fecha permitida para movimientos
            $qult = "  SELECT  Enlfec "
                    ."   FROM ".$wbasedato."_000110, ".$wbasedato."_000102, ".$wbasedato."_000101, ".$wbasedato."_000100 "
                    ."  WHERE Tcofac = '".$factor."'"
                    ."    AND Tcoest = 'on' "
                    ."    AND Tcocod = Contco "
                    ."    AND Conest = 'on' "
                    ."    AND Concod = Roncon "
                    ."    AND Rontip = 'Externa' "
                    ."    AND Ronest = 'on' "
                    ."    AND Enlron = Roncod "
                    ."    AND Enlcon = Concod "
                    ."    AND Concod = '".$codigo_concepto."' "
                    ."    AND Enllav = '".$wlav_cod."' "
                    ."    AND Enlest = 'on' "
                    ."  ORDER BY Enlfec DESC "
                    ."  LIMIT 0,1 ";
            $result = mysql_query($qult,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qult." - ".mysql_error());
            $rowult = mysql_fetch_array($result);
            $numult = mysql_num_rows($result);

            if($numult>0)
            {
                echo "<input type='hidden' name='wfeclim' id='wfeclim' value='".$rowult['Enlfec']."'>";
            }
            else
            {
                echo "<input type='hidden' name='wfeclim' id='wfeclim' value='2011-05-01'>";
            }


            echo "<input type='hidden' name='wlav' value='".$wlav."'>";
            echo "<input type='hidden' name='wronda' value='".$codigo_ronda."'>";
            echo "<input type='hidden' name='wactualizar' id='wactualizar' value='".$wactualizar."'>";

          echo "<table align='center' ".$achoTabla.">";

          // Se muestran los datos a consultar como encabezado
          echo "<tr class='titulo'>";
          echo "<td align='center' colspan='3'><b>Lavandería: ".$wlav."</b></td>";
          echo "</tr>";
          echo "<tr class='titulo'>";
          echo "<td align='center' colspan='3'><b>Recorrido: ".$codigo_ronda." - ".$ronda."</b></td>";
          echo "</tr>";

          if(isset($wfecmov) && $wfecmov < date("Y-m-d"))
          {
              $qron = " SELECT Roncod, Ronnom "
                     ."   FROM ".$wbasedato."_000102 "
                     ."  WHERE Roncon = '".$codigo_concepto."'"
                     ."    AND Ronest = 'on' ";
              $resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
              $numron = mysql_num_rows($resron);
          }
          else
          {
              $qron = " SELECT Roncod, Ronnom "
                     ."   FROM ".$wbasedato."_000102 "
                     ."  WHERE Roncon = '".$codigo_concepto."'"
                     ."    AND Ronhin <= '".$whora."' "
                     ."    AND Ronest = 'on' ";
              $resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
              $numron = mysql_num_rows($resron);
          }

          // Solicitud de ronda, fecha y hora
          echo "<tr height='34'>";
          echo "<td align='center' colspan='3'>";
          echo "<table width='100%' border='0' bordercolor='#ffffff' cellspacing='1' cellpadding='0'><tr><td class='fila2' align='left' colspan='2'> &nbsp; Establecer recorrido:<br> &nbsp; ";
          echo "<select name='wrondasel' id='wrondasel' onchange='enter_ronda()'> ";
          for ($i=1;$i<=$numron;$i++)
             {
              $rowron = mysql_fetch_array($resron);
              if($codigo_ronda==$rowron[0])
                echo "<option selected>".$rowron[0]." - ".$rowron[1]."</option>";
              else
                echo "<option>".$rowron[0]." - ".$rowron[1]."</option>";
             }
          echo "</select>";
          echo "</td><td class='fila2' align='left' colspan='2' nowrap> &nbsp; Fecha:<br> &nbsp; ";
          //if($mostrar!="off")
            campoFechaDefecto("wfecmov",$wfecmov); echo "<input type='button' value='Ir a fecha' name='ir' onclick='enter_ronda()'>";
          /*else
            echo "<input type='text' size='11' name='wfecmov' id='wfecmov' value='".$wfecmov."'".$estado.">";
          */
          echo " &nbsp; </td><td class='fila2' align='left' nowrap> &nbsp; Hora:<br> &nbsp; <input type='text' size='7' name='whormov' id='whormov' onKeyDown='ocultar_msj()' value='".$whormov."'> &nbsp;&nbsp; </td></tr>";

            // Query para obtener la lavanderia actual
            $qlav =  " SELECT Lavcod, Lavnom, Lavind "
                    ."   FROM ".$wbasedato."_000108 "
                    ."  WHERE Lavcod = '".$wlav_cod."' ";
            $reslav = mysql_query($qlav,$conex) or die  ("Error: ".mysql_errno()." - en el query: ".$qlav." - ".mysql_error());
            $rowlav = mysql_fetch_array($reslav);

          if($requiere_peso=='on' && $rowlav['Lavind']!='E' && $rowlav['Lavind']!='S')
          {
              if(isset($rowact['Enlpal']) && $rowact['Enlpal']!='')
                $wpesoalta = $rowact['Enlpal'];
              else
                $wpesoalta = 0;

              if(isset($rowact['Enlpba']) && $rowact['Enlpba']!='')
                $wpesobaja = $rowact['Enlpba'];
              else
                $wpesobaja = 0;

              if(isset($rowact['Enlpmo']) && $rowact['Enlpmo']!='')
                $wpesomojada = $rowact['Enlpmo'];
              else
                $wpesomojada = 0;


              $totalpeso = $rowact['Enlpba']+$rowact['Enlpal']+$rowact['Enlpmo'];
              // Solicitud de peso de las prendas
              echo "<tr><td class='fila2' align='left'> &nbsp; Alta suciedad:<br> &nbsp; <input type='text' size='7' style='font-size:11px;' name='wpesoalta' id='wpesoalta' onKeyDown='ocultar_msj()'  onBlur='sumar_pesos(\"wpesoalta\")' value='".$wpesoalta."'".$estado."> Kg</td><td class='fila2' align='left'> &nbsp; Baja suciedad:<br> &nbsp; <input type='text' size='7' style='font-size:11px;' name='wpesobaja' id='wpesobaja' onKeyDown='ocultar_msj()'  onBlur='sumar_pesos(\"wpesobaja\")' value='".$wpesobaja."'".$estado."> Kg</td><td class='fila2' align='left'> &nbsp; Mojada:<br> &nbsp; <input type='text' size='7' style='font-size:11px;' name='wpesomojada' id='wpesomojada' onKeyDown='ocultar_msj()'  onBlur='sumar_pesos(\"wpesomojada\")' value='".$wpesomojada."'".$estado."> Kg</td><td class='fila2' align='left'> &nbsp; Total peso:<br> &nbsp; <input type='text' size='7' style='font-size:11px;' name='wpeso' id='wpeso' value='".$totalpeso."'".$estado." readonly='readonly'> Kg</td>";
              echo "<td class='fila2' align='left'> &nbsp; ";
              if($factor=='1')
              {
                echo "<input type='checkbox' onclick='javascript:establecer_reproceso();' name='reproceso' id='reproceso' value='1' ".$check_activo."> Reproceso &nbsp; ";
              }
              echo "</td></tr>";
          }
          else
          {
            if($factor=='1' && $rowlav['Lavind']!='E' && $rowlav['Lavind']!='S')
            {
                echo "<tr><td class='fila2' align='right' colspan='5' height='27'> &nbsp; <input type='checkbox' onclick='javascript:establecer_reproceso();' name='reproceso' id='reproceso' value='1' ".$check_activo."> Reproceso &nbsp; </td></tr>";
            }
            echo "<input type='hidden' name='wpesoalta' id='wpesoalta' value='0'>";
            echo "<input type='hidden' name='wpesobaja' id='wpesobaja' value='0'>";
            echo "<input type='hidden' name='wpesomojada' id='wpesomojada' value='0'>";
          }
          echo "</table></td></tr>";

          // Encabezado de la tabla de inventario de lavanderia
          echo "<tr class=encabezadoTabla>";
          echo "<th width='70%'>Prenda</th>";
          echo "<th width='10%'>&nbsp;Saldo&nbsp;</th>";
          if($factor=='1')
          {
            echo "<th width='10%' nowrap>&nbsp;Envio&nbsp;</th>";
          }
          else
          {
            echo "<th width='10%' nowrap>&nbsp;Recibo&nbsp;</th>";
          }
          echo "</tr>";

            // Ciclo para mostrar el inventario de la lavanderia
            for($i=0;$i<$num;$i++)
            {
                $row = mysql_fetch_array($res);

                // Definición del estilo para las filas
                if (is_integer($i/2))
                  $wclass="fila1";
                else
                  $wclass="fila2";

                // Obtengo el saldo actual de la lavanderia
                $qsal =  " SELECT Splsal "
                        ."   FROM ".$wbasedato."_000109 "
                        ."  WHERE Spllav = '".$wlav_cod."'"
                        ."    AND Splpre = '".$row['Precod']."'"
                        ."    AND Splest = 'on' ";
                $ressal = mysql_query($qsal,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qsal." - ".mysql_error());
                $rowsal = mysql_fetch_array($ressal);

                if($wactualizar>0)
                {
                    // Consulto los datos ingresados para esta ronda
                    $qcan =  " SELECT Molcan, Molsal "
                            ."   FROM ".$wbasedato."_000111 a, ".$wbasedato."_000110 b "
                            ."  WHERE Molpre = '".$row['Precod']."' "
                            ."    AND Mollav = '".$wlav_cod."' "
                            ."    AND Molron = '".$codigo_ronda."' "
                            ."    AND Molcon = '".$codigo_concepto."' "
                            ."    AND a.Fecha_data = '".$wfecmov."' "
                            ."    AND Mollav = Enllav "
                            ."    AND Molron = Enlron "
                            ."    AND Molcon = Enlcon "
                            ."  GROUP BY Molcan, Molsal "
                            ."  ORDER BY a.Hora_data DESC, a.id DESC";
                        //echo $qcan;
                    $rescan = mysql_query($qcan,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcan." - ".mysql_error());
                    $numcan = mysql_num_rows($rescan);
                    $rowcan = mysql_fetch_array($rescan);
                    //echo "<br>".$qcan."<br>";

                    if($numcan>0)
                    {
                        $cantidad_inicial = $rowcan['Molcan'];
                        $saldo = $rowcan['Molsal'];
                        //echo $row['Precod']." - ".$qcan."<br>";
                    }
                    else
                    {
                        $cantidad_inicial = "";
                        $saldo = $rowsal['Splsal'];
                        //echo $row['Precod']." - ".$qsal."<br>";
                    }
                }
                else
                {
                    $cantidad_inicial = "";
                    $saldo = $rowsal['Splsal'];
                }

                if($saldo=='')
                {
                    $saldo = '0';
                }

                // Defino el sado actual de la prenda en lavandería
                $saldoactual = $rowsal['Splsal'];
                if($saldoactual=='')
                {
                    $saldoactual = '0';
                }

               // Filas donde se muestran los datos del inventario de la lavanderia
               echo "<tr class=".$wclass.">";
               echo "<td align='left'>&nbsp;".$row['Precod']." - ".$row['Predes']."</td>";
               echo "<td align='center'>".$saldo."</td>";
               if($factor=='1')
               {
                   echo "<td align='center'><input type='text' onKeyDown='ocultar_msj()' name='wcantidad".$i."' id='wcantidad".$i."' size='11' onblur='valida_envio(".$i.")' value='".$cantidad_inicial."'".$estado."></td>";
               }
               else
               {
                   echo "<td align='center'><input type='text' onKeyDown='ocultar_msj()' name='wcantidad".$i."' id='wcantidad".$i."' size='11' onblur='valida_recibo(".$i.")' value='".$cantidad_inicial."'".$estado."></td>";
               }

               // Asigno datos a la cadena de cantidad inicial
               $strCantInic .= $cantidad_inicial."-";

               echo "</tr>";

               // Se definen variables a usar por cada inventario en el envio del formulario
               echo "<input type='hidden' name='wcantidad_anterior".$i."' id='wcantidad_anterior".$i."' value='".$cantidad_inicial."'>";
               echo "<input type='hidden' name='wprenda".$i."' id='wprenda".$i."' value='".$row['Precod']."'>";
               echo "<input type='hidden' name='winventario".$i."' id='winventario".$i."' value='".$row['Preinv']."'>";
               echo "<input type='hidden' name='wdisponible".$i."' id='wdisponible".$i."' value='".$row['Preedi']."'>";
               echo "<input type='hidden' name='wsaldo".$i."' id='wsaldo".$i."' value='".$saldo."'>";
               echo "<input type='hidden' name='wsaldoactual".$i."' id='wsaldoactual".$i."' value='".$saldoactual."'>";
            }

            // Consulto la última ronda para el concepto actual
            $qron =  " SELECT Roncod "
                    ."   FROM ".$wbasedato."_000102 "
                    ."  WHERE Roncon = '".$codigo_concepto."' "
                    ."    AND Rontip = 'Externa' "
                    ."    AND Ronest = 'on' "
                    ."  ORDER BY Ronhfi DESC ";
                //echo $qcan;
            $resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
            $rowron = mysql_fetch_array($resron);
            $ultima_ronda = $rowron['Roncod'];

            $ayer = strtotime($wfecmov);
            $ayer = $ayer-86400;
            $ayer = date("Y-m-d",$ayer);
            //$ayer = date("Y-m-d",time()-86400);
            // Consulto si hay registros para la última ronda del día anterior
            $qayer =  " SELECT Enlfec "
                    ."   FROM ".$wbasedato."_000110 b "
                    ."  WHERE Enllav = '".$wlav_cod."' "
                    ."    AND Enlcon  = '".$codigo_concepto."' "
                    ."    AND Enlron = '".$ultima_ronda."' "
                    ."    AND Enlfec = '".$ayer."' ";
            //echo $qcan;
            $resayer = mysql_query($qayer,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qayer." - ".mysql_error());
            $numayer = mysql_num_rows($resayer);

            // Consulto si hay registros para el día actual
            $qhoy =  " SELECT Enlfec "
                    ."   FROM ".$wbasedato."_000110 "
                    ."  WHERE Enllav = '".$wlav_cod."' "
                    ."    AND Enlcon  = '".$codigo_concepto."' "
                    ."    AND Enlfec = '".$wfecmov."' ";
                //echo $qcan;
            $reshoy = mysql_query($qhoy,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qhoy." - ".mysql_error());
            $numhoy = mysql_num_rows($reshoy);

            if($numayer>0 || $numhoy>0)
            {
                echo "<input type='hidden' name='wayer' id='wayer' value='1'>";
            }
            else
            {
                echo "<input type='hidden' name='wayer' id='wayer' value='0'>";
                echo "<script> alert('¡ATENCIÓN! No se han registrados movimientos para la última ronda del día anterior, recuerde cambiar la fecha si va a registrarlos'); </script>";
            }

            echo "<input type='hidden' name='wreproceso' id='wreproceso' value='".$wreproceso."'>";
            echo "<input type='hidden' name='wcambio_concepto' id='wcambio_concepto' value='0'>";
            echo "<input type='hidden' name='envio' id='envio' value='1'>";

            // Espacio entre filas
            echo "<tr><td align='center' colspan='3' height='37'>&nbsp;</td></tr>";

            // Botones Retornar, Cerrar Ventana y Grabar
            echo "<tr><td align='left'>";
            echo "<input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$f\",\"$strCantInic\")'> &nbsp; &nbsp; &nbsp; &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana(\"$strCantInic\")'></td><td align='right' colspan='3'>";

            if($mostrar!="off")
            {
                echo "<input type='button' name='grabar' value='Grabar' onclick='enter()'>";
            }
            echo "</td></tr>";
            echo "</table>";
        }
        else        // Si el rango de hora no se encuentra entre las rondas definidas
        {
            // Muestra mensaje de rango de hora no corresponde con rondas existentes
            echo "<br><br>";
            echo "<table align='center'>";
            echo "<tr><td align='center'><b>La hora actual no corresponde con ninguna ronda existente para realizar la operación</b><br><br><br></td></tr>";

            // Botones Retornar y Cerrar Ventana
            echo "<tr><td align='center'><input type=button value='Retornar' onclick='retornar_reproceso(\"$wemp_pmla\",\"$f\",\"$wlav\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
            echo "</table>";
        }
    }

  echo "<br>";
  echo "</form>";

}

?>
</body>
</html>
