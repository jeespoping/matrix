<?php
include_once("conex.php");
include_once("root/comun.php");
   /**************************************************
    *            CENTRAL DE HABITACIONES             *
    *               CONEX, FREE => OK                *
    **************************************************/

//MODIFICACIONES
//========================================================================================================================================\\
//2021-09-09 (Joel Payares Hdz) : Se modifica programa en el momento donde insertaba el registro en la movhos 25, para que realice una
// actualización del registro existente para la habitación indicada. Evitando así duplicar registros para la misma habitación.
//========================================================================================================================================\\
//2019-01-30(Arleyda Insignares): Migración 2019
//========================================================================================================================================\\
//2017-09-06(Jonatan Lopez) : Se registra el la tabla movhos_000239 las habitaciones que sean marcadas como mantenimiento, ademas si estas
// se marcan disponibles no se tienen en cuenta en la estadistica.
//========================================================================================================================================\\
//2014-12-03(Camilo ZZ):  se modificó el script para que omita las habitaciones que son cubiculos
//========================================================================================================================================\\//2014-02-27 (Camilo ZZ):  se modificó el script para que elimine los registros automaticos de cierre cámas cuando se asigna un empleado
//========================================================================================================================================\\
//========================================================================================================================================\\
//2014-02-27 (Camilo ZZ):  se modificó gran parte de la estructura del programa, para que trabaje con ajax.
//========================================================================================================================================\\
//Se agrega un <OPTION value='-'></option> en el listado de empleados para que no dependa de un registro en blanco en la tabla movhos_000024,
//el guion es necesario para que al ser seleccionada esta opcion sea liberada la habitacion de empleado.
//========================================================================================================================================\\
//========================================================================================================================================\\
//Agosto 08 de 2013
//========================================================================================================================================\\
//Se agrega un <OPTION value='-'></option> en el listado de empleados para que no dependa de un registro en blanco en la tabla movhos_000024,
//el guion es necesario para que al ser seleccionada esta opcion sea liberada la habitacion de empleado.
//========================================================================================================================================\\
//Abril 11 de 2011                                                                                                                        \\
//========================================================================================================================================\\
//Se adiciona el campo de usuario que da disponibilidad a la habitacion en la tabla movhos_000025                                         \\
//========================================================================================================================================\\

session_start();

if( !isset($_SESSION['user'])){
    echo "<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
            [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
        </div>";
    return;
}

header('Content-type: text/html;charset=ISO-8859-1');

$wfecha    = date("Y-m-d");
$wayerfecha = time()-(1*24*60*60); //Resta un dia
$wayer1 = date('Y-m-d', $wayerfecha);
$whora     = (string)date("H:i:s");
$wusuario  = substr($user,(strpos($user,"-")+1),strlen($user));
$wactualiz = "2017-09-06";


// sede

$sFiltrarSede = consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
$sCodigoSede = ($sFiltrarSede == 'on') ? consultarsedeFiltro() : '';



if(isset($_POST['selectsede']) && !empty($_POST['selectsede']) && !empty($sCodigoSede) ){
    $selectsede = $_POST['selectsede'];
}


if( isset( $peticionAjax ) ){

    // Validamos que la habitación siga sin estar disponible( en caso de que haya varios usuarios concurrentes )
    $q = "SELECT habdis
            FROM ".$wbasedato."_000020
           WHERE habcod = '{$whabi}'
             AND habest = 'on' ";
    $err = mysql_query( $q, $conex );
    $row = mysql_fetch_array( $err );
    if( $row[0] == "on" ){
        echo "error-Habitacion Disponible";
        return;
    }

    if( $actualizar ==  "empleado" ){
		
		$q_reg = " SELECT Sgeman 
					 FROM ".$wbasedato."_000024
					WHERE Sgecod = '".$nuevoValor."'";
		$err_reg  = mysql_query($q_reg,$conex) or die (mysql_errno()." - en el query: ".$q_reg." - ".mysql_error());
		$row_reg = mysql_fetch_array($err_reg);
		$sgeman = $row_reg['Sgeman'];
		
        if ( trim($nuevoValor) == "-" ){					
			
            //Borro el movimiento porque se quito el empleado asignado
            $q = " DELETE FROM ".$wbasedato."_000025 "
                ."  WHERE movhdi = '00:00:00' "
                ."    AND movhab = '".$whabi."'";
            $res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        }

        if( trim($nuevoValor) != "-" ){            
			
			$q1 = " SELECT COUNT(*) " //existe movimiento sin responsable asociado a esta habitación.
                 ."   FROM ".$wbasedato."_000025 "
                 ."  WHERE movhab       = '".$whabi."'"
                 ."    AND movhdi       = '00:00:00' "
                 ."    AND TRIM(movemp) = '' "
                 ."    AND movfec  >= '".$wayer1."'";
            $err  = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());
            $row1 = mysql_fetch_array($err);
			
            if( $row1[0] > 0 ){//elimino el movimiento de la habitacion sin empleado

                $q1 = " DELETE FROM ".$wbasedato."_000025 "
                     ."  WHERE movhab       = '".$whabi."'"
                     ."    AND movfec  >= '".$wayer1."' "
                     ."    AND movhdi       = '00:00:00' "
                     ."    AND TRIM(movemp) = '' ";

			   $err = mysql_query( $q1,$conex ) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());
                if (isset($wobservacion[$i])){
                       if (trim($wobservacion[$i]) == "")
                          $wobservacion[$i]="";
                }else
                    $wobservacion[$i]="";

                $select = "
                          SELECT    count(*)
                            FROM    {$wbasedato}_000025
                           WHERE    movhab = '{$whabi}'
                             AND    Movfal = '{$wfecha}'
                             AND    Movhdi = '00:00:00'
                             AND    Movfdi = '0000-00-00'
                    ";
                $rest_select = mysql_query( $select, $conex ) or die (mysql_errno()." - en el query: ".mysql_error());
                $cantidad = mysql_fetch_array( $rest_select );

                if( $cantidad[0] > 0 )
                {
                    /**
                     * Actualizo el registro en la base de datos para la habitación correspondiente
                     * @author Joel David Payares Hernández <joel.payares@lasamericas.com.co>
                     * @since 2021-09-09
                     */
                    $q1 = "
                          UPDATE    {$wbasedato}_000025
                             SET    movemp = '{$nuevoValor}',
                                    movfec = '{$wfecha}',
                                    movhem = '{$whora}'
                           WHERE    movhab = '{$whabi}'
                             AND    Movhdi = '00:00:00'
                             AND    Movfdi = '0000-00-00'
                    ";
                    $err = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());

                    /**
                     * Obtengo el id del ultimo registro actualizado
                     * @author Joel David Payares Hernández <joel.payares@lasamericas.com.co>
                     * @since 2021-09-09
                     */
                    $query_id = "
                          SELECT    id
                            FROM    {$wbasedato}_000025
                           WHERE    movhab = '{$whabi}'
                        ORDER BY    movfec DESC
                           LIMIT    1;
                    ";
                    $err = mysql_query($query_id, $conex) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());
                    $id = mysql_fetch_assoc($err)['id'];
                }
                else
                {
                    $q1 = " INSERT INTO ".$wbasedato."_000025 (   Medico       ,   Fecha_data,   Hora_data,   movhab    ,   movemp       ,   movfec    ,   movhem    ,  movhdi   ,   movobs                    ,   movfal    ,   movhal    , movfdi     , Seguridad        ) "
                        ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whabi."','".$nuevoValor."','".$wfecha."','".$whora."' , '00:00:00','','".$fechaAltaDef."','".$horaAltaDef."','0000-00-00', 'C-".$wusuario."')";
                    $err = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $id = mysql_insert_id();
                }
                
                echo $id."-".$whora; 				
				
				if($sgeman == 'on'){
		
					$q2 = " INSERT INTO ".$wbasedato."_000239 (   Medico       ,   Fecha_data,   Hora_data,   loghab    ,   Logfman    ,   Loghma     ,   logids   , logest, Seguridad        ) "
									 ."                VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whabi."', '".$wfecha."' , '".$whora."' ,  '".$id."' , 'on'  ,'C-".$wusuario."')";
					$err2 = mysql_query($q2,$conex) or die (mysql_errno()." - en el query: ".$q2." - ".mysql_error());
				
				}

            }else{
                    /**
                     * Consulta para contar los registros de la habitación como parametro,
                     * hora disponible = 00:00:00 y empleado != ''
                     */
                    $q1 = " SELECT COUNT(*) "
                         ."   FROM ".$wbasedato."_000025 "
                         ."  WHERE movhab  = '".$whabi."'"
                         ."    AND movhdi  = '00:00:00' "
                         ."    AND movemp != '' ";
                    $err = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());
                    $row1 = mysql_fetch_array($err);

                    if ($row1[0] > 0){
                           $q1 = " UPDATE ".$wbasedato."_000025 "
                                ."    SET movemp  = '".$nuevoValor."',"
                                ."        movfec  = '".$wfecha."',"
                                ."        movhem  = '".$whora."'"
                                ."  WHERE movhab  = '".$whabi."'"
                                ."    AND movhdi  = '00:00:00' "
                                ."    AND movemp != '' ";
                           $err = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());
                            echo "actualizado-".$whora;
							
							$q2 = " SELECT COUNT(*) "
								 ."   FROM ".$wbasedato."_000239 "
								 ."  WHERE Loghab  = '".$whabi."'"
								 ."    AND Logffi  = '0000-00-00' "
								 ."    AND Loghfi  = '00:00:00' ";
							$err2 = mysql_query($q2,$conex) or die (mysql_errno()." - en el query: ".$q2." - ".mysql_error());
							$row2 = mysql_fetch_array($err2);	
							
							if ($row2[0] > 0){
								
								$q2 = " UPDATE ".$wbasedato."_000239 "
									 ."    SET Logffi  = '".$wfecha."', Loghfi  = '".date("H:i:s")."'"
									 ."  WHERE Logids  = '".$id."'
									       AND Logffi = '00:00:00'";
								$err2 = mysql_query($q2,$conex) or die (mysql_errno()." - en el query: ".$q2." - ".mysql_error());
								
							}elseif($sgeman == 'on'){
							
								$q2 = " INSERT INTO ".$wbasedato."_000239 (   Medico       ,   Fecha_data,   Hora_data,   loghab    ,   Logfman    ,   Loghma     ,   logids   , logest, Seguridad        ) "
												."                VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whabi."', '".$wfecha."' , '".$whora."' ,  '".$id."' , 'on'  ,'C-".$wusuario."')";
								$err2 = mysql_query($q2,$conex) or die (mysql_errno()." - en el query: ".$q2." - ".mysql_error());
							}
                    }else{
                        $select = "
                            SELECT    count(*)
                              FROM    {$wbasedato}_000025
                             WHERE    movhab = '{$whabi}'
                               AND    Movfal = '{$wfecha}'
                               AND    Movhdi = '00:00:00'
                               AND    Movfdi = '0000-00-00'
                            ";
                        $rest_select = mysql_query( $select, $conex ) or die (mysql_errno()." - en el query: ".mysql_error());
                        $cantidad = mysql_fetch_array( $rest_select );

                        if( $cantidad[0] > 0 )
                        {
                            /**
                             * Actualizo el registro en la base de datos para la habitación correspondiente
                             * @author Joel David Payares Hernández <joel.payares@lasamericas.com.co>
                             * @since 2021-09-09
                             */
                            $q1 = "
                                    UPDATE    {$wbasedato}_000025
                                    SET    movemp = '{$nuevoValor}',
                                            movfec = '{$wfecha}',
                                            movhem = '{$whora}'
                                    WHERE    movhab = '{$whabi}'
                                    AND    Movhdi = '00:00:00'
                                    AND    Movfdi = '0000-00-00'
                            ";
                            $err = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());

                            /**
                             * Obtengo el id del ultimo registro actualizado
                             * @author Joel David Payares Hernández <joel.payares@lasamericas.com.co>
                             * @since 2021-09-09
                             */
                            $query_id = "
                                    SELECT    id
                                    FROM    {$wbasedato}_000025
                                    WHERE    movhab = '{$whabi}'
                                ORDER BY    movfec DESC
                                    LIMIT    1;
                            ";
                            $err = mysql_query($query_id, $conex) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());
                            $id = mysql_fetch_assoc($err)['id'];
                        }
                        else
                        {
                            $q1 = " INSERT INTO ".$wbasedato."_000025 (   Medico       ,   Fecha_data,   Hora_data,   movhab    ,   movemp       ,   movfec    ,   movhem    ,  movhdi   ,   movobs                    ,   movfal    ,   movhal    , movfdi     , Seguridad        ) "
                                ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whabi."','".$nuevoValor."','".$wfecha."','".$whora."' , '00:00:00','','".$fechaAltaDef."','".$horaAltaDef."','0000-00-00', 'C-".$wusuario."')";
                            $err = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
                            $id = mysql_insert_id();
                        }
                        echo $id."-".$whora;
                            
                        if($sgeman == 'on'){
    
                            $q2 = " INSERT INTO ".$wbasedato."_000239 (   Medico       ,   Fecha_data,   Hora_data,   loghab    ,   Logfman    ,   Loghma     ,   logids   , logest, Seguridad        ) "
                                                ."                VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whabi."', '".$wfecha."' , '".$whora."' ,  '".$id."' , 'on'  ,'C-".$wusuario."')";
                            $err2 = mysql_query($q2,$conex) or die (mysql_errno()." - en el query: ".$q2." - ".mysql_error());
                        
                        }

                    }
            }
        }
    }

    if( $actualizar == "observacion" ){

        //Actualizo el movimiento
         $q = " UPDATE ".$wbasedato."_000025 "
             ."    SET movobs = '".trim(utf8_encode($nuevoValor))."' "
             ."  WHERE movhab = '".$whabi."'
                   AND id     = '".$id."'";
        $res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        echo "actualizado-".$whora;
    }

    if( $actualizar == "disponibilidad" ){
        //Actualizo el movimiento
        $q = " UPDATE ".$wbasedato."_000025 "
         ."    SET movhdi = '".$whora."', "
         ."        movfdi = '".$wfecha."', "
         ."        movedi = '".$wusuario."' "                     //Abril 11 de 2011
         ."  WHERE movhab = '".$whabi."'"
         ."    AND movhdi = '00:00:00' "
         ."    AND id     = '".$id."'";
        $res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

        //Actualizo la habitación colocandola disponible y ya alistada
        $q = " UPDATE ".$wbasedato."_000020 "
         ."    SET habali = 'off', "
         ."        habdis = 'on', "
         ."        habprg = '', "
         ."        habpro = '' "
         ."  WHERE habcod = '".$whabi."'";
        $res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        echo "actualizado-".$whora;
    }
return;
}
?>
<html lang="es-ES">
<head>    
    <title>CENTRAL DE HABITACIONES</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">

        // Selecciona sede
        jQuery(document).ready(function($){

            $(document).on('change','#selectsede',function(e){
                e.preventDefault();
                var selectsede =  $("#selectsede").val();
                var wbasedato =  $("#wbasedato").val();
                var wemp_pmla =  $("#wemp_pmla").val();
                $("#wselectsede").val(selectsede);
                selectorSede(wbasedato,wemp_pmla,selectsede);
            });
        });

        function selectorSede(wbasedato,wemp_pmla,selectsede){
                   $("#wselectsede").val(selectsede);
                   $('#central').submit();

        }

        function actualizarMovimiento( obj, habitacion, fechaAltaDef, horaAltaDef ){

          wconsulta  = $("#wconsulta").val();
          selectsede = $("#wselectsede").val();
          habitacion = $.trim(habitacion);
          wemp_pmla = $("#wemp_pmla").val();
          if( wconsulta == "S" ){
			alert("No puede realizar esta accion el sistema esta en solo lectura.");
			location.reload();
			return;  
		  }
            

          setTimeout( function(){
              obj        = jQuery(obj);
              actualizar = obj.attr('actualizar');
              nuevoValor = obj.val();
              wbasedato  = $("#wbasedato").val();
              id         = $("#id_registro_"+habitacion).val();
              $.ajax({

                  url: "central_de_habitaciones.php?wbasedato="+wbasedato+"&wemp_pmla="+wemp_pmla+"&selectsede="+selectsede,
                  type: "POST",
                  data: {
                          peticionAjax: "actualizarMovimiento",
                            nuevoValor: nuevoValor,
                            actualizar: actualizar,
                                 whabi: habitacion,
                          fechaAltaDef: fechaAltaDef,
                           horaAltaDef: horaAltaDef,
                                    id: id
                        },
                  success: function(data)
                  {

                      data = data.split("-")
                      if( data[0] == "error" ){
                        alert( data[1] );
                        window.location="central_de_habitaciones.php?wbasedato="+wbasedato+"&wemp_pmla="+wemp_pmla+"&wconsulta="+wconsulta+"&selectsede="+selectsede;
                      }
                      if( data[0] != "actualizado" ){
                        if( actualizar == "empleado" ){
                          $("#registro_"+habitacion).val( data[0] );
                          if( nuevoValor != "-" ){
                            $("#wobservacion_"+habitacion).removeAttr( "disabled" );
                            $("#disponible_"+habitacion).removeAttr( "disabled" );
                            $("#id_registro_"+habitacion).val( data[0] );
                            $("#hora_asignado_"+habitacion).html( "<b>"+data[1]+"</b>" );

                          }else{
                            $("#wobservacion_"+habitacion).attr( "disabled","disabled" );
                            $("#wobservacion_"+habitacion).val( "" );
                            $("#disponible_"+habitacion).attr( "disabled",true );
                            $("#id_registro_"+habitacion).val( "" );
                            $("#hora_asignado_"+habitacion).html( "" );
                          }
                        }
                      }else{
                        if( actualizar == "empleado" ){
                             $("#hora_asignado_"+habitacion).html( "<b>"+data[1]+"</b>" );
                        }
                        if( actualizar == "disponibilidad" ){
                            obj.parent().parent().remove();
                        }
                      }
                  }

              }), 500 });
        }
    </script>
</head>
<?php


    encabezado("CENTRAL DE HABITACIONES",$wactualiz, "clinica",true);

    /** esto se hace siempre al ingresar **/
    //Esto lo hago para que al mostrar los datos de la central y solo es de consulta salga como ReadOnly, sin poder modificar nada
   ($wconsulta=="S") ? $whabi="Readonly" : $whabi="";

    /** eliminamos los movimientos que no tienen empleado asociado **/
    if($wconsulta!="S"){
        $q1 = " DELETE FROM ".$wbasedato."_000025 "
             ."  WHERE movfec   = '".$wfecha."'"
             ."    AND movhdi       = '00:00:00' "
             ."    AND TRIM(movemp) = '' ";
        $err = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());
    }

    //===================================================================================================================================================
    // QUERY PRINCIPAL
    //===================================================================================================================================================
    // ACA TRAIGO TODAS LAS HABITACIONES PARA ALISTAR
    //===================================================================================================================================================


    /** Filtro Sede */
    $wselectsede = '';
    $sedeTabla = '';
    $joinSede = '';
    if(isset($_POST['selectsede']) && !empty($_POST['selectsede']) ){
        $wselectsede = "Habcco = Ccocod";
        $joinSede = "INNER JOIN ".$wbasedato."_000011 on Habcco = Ccocod";
        $whereselectsede = " AND  Ccosed =  '".$_POST['selectsede']."'"; 

        $q = "  SELECT Habcod habitacion, '', '' observacion, Habfal fechaAltaDef, Habhal horaAltaDef, Habprg, '' id, '' horaAsignado "
        ."    FROM ".$wbasedato."_000020 "
       .$joinSede
       ."   WHERE habali = 'on' "
       ."     AND habest = 'on' "
       .$whereselectsede
       ."   UNION  ALL"
       ."  SELECT movhab habitacion, movemp, movobs observacion, Fecha_data fechaAltaDef, Hora_data horaAltaDef, '', id, movhem horaAsignado "
       ."    FROM ".$wbasedato."_000025 "
       ."   WHERE movhdi = '00:00:00' "
       ."   ORDER BY 4, 5, 1 ";
    }
    else {

        /**  **///---> se omiten los cubiculos habcub
        /**
         * Se cambia en la consulta fecha y hora de alta por fecha data y hora data para obtener el tiempo de llegada
         */
        $q = "  SELECT Habcod habitacion, '', '' observacion, Habfal fechaAltaDef, Habhal horaAltaDef, Habprg, '' id, '' horaAsignado "
             ."    FROM ".$wbasedato."_000020 "
            ."   WHERE habali = 'on' "
            ."     AND habest = 'on' "
            ."     AND habcod not in ( SELECT movhab "
            ."                           FROM ".$wbasedato."_000025 "
            ."                          WHERE movhdi = '00:00:00' ) "
            ."     AND habcub != 'on' "
            ."   UNION "
            ."  SELECT movhab habitacion, movemp, movobs observacion, Fecha_data fechaAltaDef, Hora_data horaAltaDef, '', id, movhem horaAsignado "
            ."    FROM ".$wbasedato."_000025 "
            ."   WHERE movhdi = '00:00:00' "
            ."   ORDER BY 4, 5, 1 ";
    }

    $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res);
?>
<body width=100%>
    <!-- <form name='central' action='central_de_habitaciones.php' method='post' id="central"> -->
    <form name='central' action='#' method='post' id="central">

        <input type='HIDDEN' name='wbasedato' id='wbasedato' value='<?php echo $wbasedato ?>'>
        <input type='HIDDEN' name='wconsulta' id='wconsulta' value='<?php echo $wconsulta ?>'>
        <input type='HIDDEN' name='selectsede' id='wselectsede' value='<?php echo $selectsede ?>'>
        <input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='<?php echo $wemp_pmla ?>'>

        <center><table>
            <tr class='encabezadotabla'>
                <th>Id</th>
                <th>Prioridad</th>
                <th>Fecha y hora libre<br> para aseo</th>
                <th>Habitacion</th>
                <th>Empleado Asignado</th>
                <th>Hora Asignado</th>
                <th>Observaci&oacute;n</th>
                <th>Disponible</th>
            </tr>
            <?php
                $i = 0;
                while( $row = mysql_fetch_array( $res ) ){

                    $whabitacion  = "";
                    $whoraAsign   = "";
                    $wobservacion = "";
                    $wdisponible  = "";
                    $wcolor       = "";

                    //Si la habitacion estaba programada cambio el color del fondo a rojo
                    if ($row[5]=="on"){
                        $wcolor = "FF9966";
                        $waviso = "Urgente";
                    }else
                        $waviso = "";

                    ( is_int( $i/2) ) ? $wclass = "fila1" : $wclass = "fila2";

                    //================================================================================================================
                    //Busco si la habitacion pertenece a un servicio que tiene personal de aseo asignado para colocarle color amarillo
                    //================================================================================================================
                    $q = " SELECT ccoase "
                        ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000011 "
                        ."  WHERE habcod = '".$row[0]."'"
                        ."    AND habcco = ccocod ";
                    $res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $row1 = mysql_fetch_array($res1);
                    if ($row1[0] == "on")
                       $wcolor="FFFF99";
                    //================================================================================================================

                   /**construcción del select de empleados**/
                   if (trim($row[1])!=""){

                        if ($row[1]!="")
                           $wcodigo[0] = $row[1];
                          else
                             $wcodigo = explode("-",$wempleado[$i]);

                        //Este query se utiliza mas abajo
                        $q = " SELECT '', '', '0' as tip"
                            ."   FROM ".$wbasedato."_000024 "
                            ."  WHERE sgeest = 'on' "
                            ."    AND id = 1 "
                            ."  UNION "
                            ." SELECT sgecod, sgenom, '1' as tip "
                            ."   FROM ".$wbasedato."_000024 "
                            ."  WHERE sgecod = '".$wcodigo[0]."'"
                            ."    AND sgeest = 'on' "
                            ."  UNION "
                            ." SELECT sgecod, sgenom, '2' as tip"
                            ."   FROM ".$wbasedato."_000024 "
                            ."  WHERE sgecod != '".$wcodigo[0]."'"
                            ."    AND sgeest  = 'on' "
                            ."  ORDER BY 3,2 ";
                        }else{
                            $q= "  SELECT '', '' "
                               ."    FROM ".$wbasedato."_000024 "
                               ."   WHERE sgeest = 'on' "
                               ."     AND id = 1 "
                               ."   UNION "
                               ."  SELECT sgecod, sgenom "
                               ."    FROM ".$wbasedato."_000024 "
                               ."   WHERE sgeest = 'on' "
                               ."   ORDER BY 2 ";
                    }
                    $ressge = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $numsge = mysql_num_rows($ressge);
                    $wempleado = "<select name='wempleado[".$i."]' actualizar='empleado' onchange='actualizarMovimiento( this, \"".$row['habitacion']."\", \"".$row['fechaAltaDef']."\", \"".$row['horaAltaDef']."\" )' ".$whabi.">";
                        //Por aca entra cuando se acaba de abrir la ventana del programa y ya habian empleados asiganados a habitaciones.
                        if ( trim($row[1]) != "" ){  //Traigo el nombre del empleado
                            $q1= " SELECT sgecod, sgenom "
                                ."    FROM ".$wbasedato."_000024 "
                                ."   WHERE sgecod = '".$row[1]."'";
                            $ressge1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
                            $rowsge1 = mysql_fetch_array($ressge1);

                            $wempleado .= "<option value='$rowsge1[0]' selected>".$rowsge1[0]." - ".$rowsge1[1]." </option>";    //Coloco el dato que esta grabado en la tabla 000025
                        }

                        //Se agrega esta opcion para que no dependa de un registro en blanco en la tabla movhos_000024,
                        //debe tener valor "-" para que pueda liberar la cama de empleado (Jonatan 01 agosto 2013)
                        $wempleado .= "<option value='-'></option>";

                        for($j=0;$j<$numsge;$j++){
                            $rowsge = mysql_fetch_array($ressge);
                            if(trim($rowsge[0])!="")
                              $wempleado .= "<option value='$rowsge[0]'>".$rowsge[0]."-".utf8_encode($rowsge[1])." </option>";
                        }
                    $wempleado .= "</select>";

                    ?>
                    <tr class='<?php echo $wclass ?>'>

                        <td><input type='text' id='registro_<?php echo trim($row['habitacion']) ?>' value='<?php echo $row['id'] ?>' readonly='readonly' size='8' disabled='disabled'></td>
                        <td align='center'><b><?php echo $waviso ?></b></td></td>
                        <td align='center'><?php echo $row['fechaAltaDef'] ?> <br><?php echo $row['horaAltaDef'] ?></td>
                        <td align='center'><?php echo $row['habitacion'] ?></td>
                        <td><?php echo $wempleado ?></td>
                        <td align='center' id='hora_asignado_<?php echo trim($row['habitacion']) ?>'><b><?php echo $row['horaAsignado'] ?></b></td>
                        <td bgcolor='<?php echo $wcolor?>' >
                            <?php
                            if (trim($row[6])!="" and trim($row[1])!="-" and trim($row[1])!="")   //Indica que solo ingreso si tiene id y empleado asignado
                               echo "<textarea id='wobservacion_".trim($row['habitacion'])."' rows='2' cols='40' ".$whabi."  actualizar='observacion' onkeypress='actualizarMovimiento( this, \"".trim($row['habitacion'])."\", \"".$row['fechaAltaDef']."\", \"".$row['horaAltaDef']."\" )'>".utf8_decode($row['observacion'])." </textarea>";
                              else
                                 echo "<textarea id='wobservacion_".trim($row['habitacion'])."' rows='2' cols='40' actualizar='observacion' disabled onkeypress='actualizarMovimiento( this, \"".trim($row['habitacion'])."\", \"".$row['fechaAltaDef']."\", \"".$row['horaAltaDef']."\" )'>".utf8_decode($row['observacion'])." </textarea>";
                            ?>
                        </td>
                        <td align='center'  bgcolor='<?php echo $wcolor?>' >
                            <?php
                                if (trim($row[6])!="" and trim($row[1])!="-" and trim($row[1])!=""){   //Indica que solo ingreso si tiene id y empleado asignado
                                   echo "<INPUT TYPE='checkbox' id='disponible_".trim($row['habitacion'])."' actualizar='disponibilidad' onclick='actualizarMovimiento( this, \"".trim($row['habitacion'])."\", \"".$row['fechaAltaDef']."\", \"".$row['horaAltaDef']."\" )' ".$whabi."></td>";
                                   echo "<input type='hidden' id='id_registro_".trim($row['habitacion'])."' value='{$row['id']}'>";
                                  }else{
                                     echo "<INPUT TYPE='checkbox' id='disponible_".trim($row['habitacion'])."' actualizar='disponibilidad' disabled onclick='actualizarMovimiento( this, \"".trim($row['habitacion'])."\", \"".$row['fechaAltaDef']."\", \"".$row['horaAltaDef']."\" )'>";
                                     echo "<input type='hidden' id='id_registro_".trim($row['habitacion'])."' value='{$row['id']}'>";
                                  }
                             ?>
                        </td>
                      </tr>
                    <?php
                    $i++;
                }
            ?>
        <tr><td bgcolor="99CCCC" align="center" colspan="9"><input type="submit" value="OK"></td></tr>
        </table></center>

        <br>
        <table border=1 align=center>
        <caption bgcolor=#ffcc66><b>Convenciones</b></caption>
        <tr>
            <td colspan='3' bgcolor='FF9966'><font size='2' color='000000'>&nbsp; Habitaci&oacute;n programada</font></td>
            <td colspan='3' bgcolor='FFFF99'><font size='2' color='000000'>&nbsp; Con personal de Aseo</font></td>
            <td colspan='3' class='fila1'><font size='2' color='000000'>&nbsp; Atenci&oacute;n Normal</font></td>
            <td colspan='3' class='fila2'><font size='2' color='000000'>&nbsp; Atenci&oacute;n Normal</font></td>
        </tr>
        </table>
        </form>

        <meta http-equiv='refresh' content='60;url=central_de_habitaciones.php?wbasedato=<?php echo $wbasedato ?>&wemp_pmla=<?php echo $wemp_pmla ?>&wconsulta=<?php echo $wconsulta ?>&selectsede=<?php echo $selectsede ?>'>
        <br>
        <center><table>
        <tr><td align='center' colspan='9'><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>
        </table></center>

    </form>
</body>
</html>
