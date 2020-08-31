<?php
include_once("conex.php");
session_start();

if (!isset($consultaAjax))
{
?>
<head>
  <title>INTERCAMBIO DE HABITACIONES</title>
</head>
<body>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
		<style type="text/css">


				.titulo_grande{
					font-size:14px;
					font-weight:bold;
					text-align: center;
					width: 100%;
				}
				.botona{
					font-size:13px;
					font-family:Verdana,Helvetica;
					font-weight:bold;
					color:white;
					background:#638cb5;
					border:0px;
					width:200px;
					height:30px;
					margin-left: 1%;
				}

				.botona:hover{
					background:#638BD5;

				}

				input:disabled {
					background: #fff;
					color: black;
				}

			    a img{
					border:0;
				}
				input{
					outline: none;
					-moz-border-radius: 5px;
					-webkit-border-radius: 5px;
				    border-radius: 4px;

				}

				input:focus{
					outline: none;
					-moz-border-radius: 5px;
					-webkit-border-radius: 5px;
				    border-radius: 5px;

				    box-shadow: 0 0 32px #C3D9FF;
					-webkit-box-shadow: 0 0 32px #C3D9FF;
					-moz-box-shadow: 0 0 32px #C3D9FF;
				}

				#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
				#tooltip h3, #tooltip div{margin:0; width:auto}

		</style>
<script type="text/javascript">

 function conMayusculas(field)
            {
            field.value = field.value.toUpperCase()
            }

 function enter()
	{
	 document.forms[0].submit();
	}

 function iniciar_cancelacion(wemp_pmla, basedatos, cenmez, user)
        {

        var whaba = 0;
        var whabb = 0;

        var whaba = $("#whaba").val();
        var whabb = $("#whabb").val();

        //Valida si ha digitado la cama A
        if (whaba == '')
            {
                alert('Debe ingresar una habitación A.');
                return;
            }

        //Valida si ha digitado la cama B
        if (whabb == '')
            {
                alert('Debe ingresar una habitación B.');
                return;
            }

                   //Verifica que las habitaciones no sean iguales
        if (whaba == whabb)
            {
                alert('Las habitaciones no deben ser iguales');
                return;
            }


		$.post("intercambio_pacientes.php",
				{
                    consultaAjax:   	'iniciar_cancelacion',
					wemp_pmla:      	wemp_pmla,
                    wbasedato:          basedatos,
					whaba:              whaba,
                    whabb:              whabb,
                    wcenmez:            cenmez,
                    wuser:              user

				}
                ,function(data_json) {

                    if (data_json.error == 1)
                    {
                        alert(data_json.mensaje);
                        enter();
                        return;
                    }
                    else
                    {

                        //Vuelve los campos de habitacion A y B de solo lectura.
                        $('#whaba').attr('readonly', true);
                        $('#whabb').attr('readonly', true);

                        //Oculta el boton de iniciar cancelacion
                        $("#boton_interc").hide("slow");

                        //Oculta el boton de iniciar cancelacion
                        $("#boton_cancelar").hide("slow");

                        //Oculta el boton de cerrar ventana
                        $("#cerrar1").hide("slow");

                        $("#datos_pacientes").html(data_json.table);
                        $("#informacion_pacientes").show('1000');
                    }

            },
            "json"
        );
    }


 function iniciar_intercambio(wemp_pmla, basedatos, cenmez, user)
        {

        var whaba = 0;
        var whabb = 0;

        var whaba = $("#whaba").val();
        var whabb = $("#whabb").val();

        //Valida si ha digitado la cama A
        if (whaba == '')
            {
                alert('Debe ingresar una habitación A.');
                return;
            }

        //Valida si ha digitado la cama B
        if (whabb == '')
            {
                alert('Debe ingresar una habitación B.');
                return;
            }

                   //Verifica que las habitaciones no sean iguales
        if (whaba == whabb)
            {
                alert('Las habitaciones no deben ser iguales');
                return;
            }


		$.post("intercambio_pacientes.php",
				{
                    consultaAjax:   	'iniciar_intercambio',
					wemp_pmla:      	wemp_pmla,
                    wbasedato:          basedatos,
					whaba:              whaba,
                    whabb:              whabb,
                    wcenmez:            cenmez,
                    wuser:              user

				}
                ,function(data_json) {

                    if (data_json.error == 1)
                    {
                        alert(data_json.mensaje);
                        enter();
                        return;
                    }
                    else
                    {

                        //Vuelve los campos de habitacion A y B de solo lectura.
                        $('#whaba').attr('readonly', true);
                        $('#whabb').attr('readonly', true);

                        //Oculta el boton de iniciar cancelacion
                        $("#boton_interc").hide("slow");

                        //Oculta el boton de iniciar cancelacion
                        $("#boton_cancelar").hide("slow");


                        //Oculta el boton de cerrar ventana
                        $("#cerrar1").hide("slow");

                        $("#datos_pacientes").html(data_json.table);
                        $("#informacion_pacientes").show('1000');
                    }

            },
            "json"
        );
    }


function intercambiar(wemp_pmla, basedatos, cenmez, user, whistoriaa, wingresoa, whistoriab, wingresob, wid_solici_a, wid_solici_b, wcco_a, wcco_b, wcencam)
        {

        var whaba = 0;
        var whabb = 0;

        var whaba = $("#whaba").val();
        var whabb = $("#whabb").val();

        //Valida si ha digitado la cama A
        if (whaba == '')
            {
                alert('Debe ingresar una habitación A.');
                return;
            }

        //Valida si ha digitado la cama B
        if (whabb == '')
            {
                alert('Debe ingresar una habitación B.');
                return;
            }

                   //Verifica que las habitaciones no sean iguales
        if (whaba == whabb)
            {
                alert('Las habitaciones no deben ser iguales');
                return;
            }


        //muestra el mensaje de cargando
        $.blockUI({ message: $('#msjEspere') });

		$.post("intercambio_pacientes.php",
				{
                    consultaAjax:   	'intercambiar',
					wemp_pmla:      	wemp_pmla,
                    wbasedato:          basedatos,
					whaba:              whaba,
                    whabb:              whabb,
                    wcenmez:            cenmez,
                    whistoriaa:         whistoriaa,
                    wingresoa:          wingresoa,
                    whistoriab:         whistoriab,
                    wingresob:          wingresob,
                    wid_solici_a:       wid_solici_a,
                    wid_solici_b:       wid_solici_b,
                    wcco_a:             wcco_a,
                    wcco_b:             wcco_b,
                    wuser:              user,
                    wcencam:            wcencam

				}
                ,function(data_json) {

                    if (data_json.error == 1)
                    {
                        alert(data_json.mensaje);
                        $.unblockUI();
                        return;
                    }
                    else
                    {
                        //Oculta el boton de iniciar cancelacion
                        $("#boton_interc").hide("slow");

                        //Oculta la tabla de informacion actual
                        $("#inf_actual").hide("slow");

                        //Oculta el boton de intercambiar par adejar solo el retornar
                        $("#boton_intercambiar").hide("slow");

                        alert("Habitaciones intercambiadas con éxito");
                        //oculta el mensaje de cargando
						$.unblockUI();

                    }

            },
            "json"
        );
    }


    function cancelar_intercambio(wemp_pmla, basedatos, cenmez, user, whistoriaa, wingresoa, whistoriab, wingresob, wid_solici_a, wid_solici_b, wcco_a, wcco_b, wcencam, wid_ent_a, wid_ent_b )
        {

        var whaba = 0;
        var whabb = 0;

        var whaba = $("#whaba").val();
        var whabb = $("#whabb").val();

        //Valida si ha digitado la cama A
        if (whaba == '')
            {
                alert('Debe ingresar una habitación A.');
                return;
            }

        //Valida si ha digitado la cama B
        if (whabb == '')
            {
                alert('Debe ingresar una habitación B.');
                return;
            }

                   //Verifica que las habitaciones no sean iguales
        if (whaba == whabb)
            {
                alert('Las habitaciones no deben ser iguales');
                return;
            }

        //muestra el mensaje de cargando
        $.blockUI({ message: $('#msjEspere') });

		$.post("intercambio_pacientes.php",
				{
                    consultaAjax:   	'cancelar_intercambio',
					wemp_pmla:      	wemp_pmla,
                    wbasedato:          basedatos,
					whaba:              whaba,
                    whabb:              whabb,
                    wcenmez:            cenmez,
                    whistoriaa:         whistoriaa,
                    wingresoa:          wingresoa,
                    whistoriab:         whistoriab,
                    wingresob:          wingresob,
                    wid_solici_a:       wid_solici_a,
                    wid_solici_b:       wid_solici_b,
                    wcco_a:             wcco_a,
                    wcco_b:             wcco_b,
                    wuser:              user,
                    wcencam:            wcencam,
                    wid_ent_a:          wid_ent_a,
                    wid_ent_b:          wid_ent_b

				}
                ,function(data_json) {

                    if (data_json.error == 1)
                    {
                        alert(data_json.mensaje);
                        return;
                    }
                    else
                    {
                        alert(data_json.mensaje);

                        //Oculta el boton de iniciar cancelacion
                        $("#boton_cancel_interc").hide("slow");

                        //Oculta la tabla de informacion actual
                        $("#inf_actual").hide("slow");

                        //oculta el mensaje de cargando
						$.unblockUI();

                    }

            },
            "json"
        );
    }




</script>

<?php
}
/* ****************************************************************
   * PROGRAMA PARA INTERCAMBIAR HABITACIONES DESDES ADMISIONES
   ****************************************************************/

//==================================================================================================================================
//PROGRAMA                   : intercambio_pacientes.php
//AUTOR                      : Jonatan Lopez Aguirre.
//FECHA CREACION             : Feb. 20 de 2013
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="Febrero 20 de 2013";
//DESCRIPCION
//====================================================================================================================================\\
//                																							  \\
//====================================================================================================================================\\



if(!isset($_SESSION['user']))
	echo "Error, Usuario NO Registrado";
else
{

include_once("root/comun.php");
include_once("movhos/movhos.inc.php");





$wbasedatos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Camilleros');

// Se incializan variables de fecha hora y usuario
if (strpos($user, "-") > 0)
    $wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
    else
        $wuser=$user;


function datosPaciente($whab)
    {

    global $conex;
    global $wemp_pmla;
    global $wbasedatos;

    //Consultamos inicialmente si el paciente tiene historia relacionada en la tabla 20 de movhos (Habitacion A)
    $q_hab_a =  " SELECT habhis, habing, habcod"
                ."   FROM ".$wbasedatos."_000020"
                ."  WHERE habcod = '".$whab."'";
    $res_hab_a = mysql_query($q_hab_a,$conex)or die(mysql_errno().":".mysql_error());
    $row_hab_a = mysql_fetch_array($res_hab_a);
    $whistoria = $row_hab_a['habhis'];
    $wingreso = $row_hab_a['habing'];

    $query_info = "
        SELECT  datos_paciente.Pactid ,pacientes_id.Oriced,"
            ."  datos_paciente.Pacno1, datos_paciente.Pacno2,"
            ."  datos_paciente.Pacap1, datos_paciente.Pacap2"
        ."  FROM  root_000037 as pacientes_id, root_000036 as datos_paciente"
        ." WHERE  pacientes_id.Orihis = '".$whistoria."'"
        ."   AND  pacientes_id.Oriing = '".$wingreso."'"
        ."   AND  pacientes_id.Oriori = '".$wemp_pmla."'"
        ."   AND 	pacientes_id.Oriced = datos_paciente.Pacced"
        ."   AND 	Oritid = Pactid";
    $res_info = mysql_query($query_info, $conex);
    $row_datos = mysql_fetch_array($res_info);

    $datos = array('primer_nombre'=>$row_datos['Pacno1'], 'segundo_nombre'=>$row_datos['Pacno2'], 'primer_apellido'=>$row_datos['Pacap1'],
                    'segundo_apellido'=>$row_datos['Pacap2'],'doc'=>$row_datos['Oriced'],'tipodoc'=>$row_datos['Pactid'], 'historia'=>$whistoria, 'ingreso'=>$wingreso);
    return $datos;
}



//Verifica si un centro de costos es de cirugia o de urgencias
function es_cirugia_o_urgencias($wcco)
{
	global $wbasedato;
	global $conex;

	$q = " SELECT count(*) "
		."   FROM ".$wbasedato."_000011 "
		."  WHERE ccocod = '".$wcco."' "
		."    AND (ccocir = 'on' "
		."     OR ccourg = 'on') ";
	$rescir = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$rowcir = mysql_fetch_array($rescir);

	if($rowcir[0]>0)
		return true;
	else
		return false;
}


//Esta funcion verifica que medicamentos tiene grabados el paciente en la ultimas dos rondas
function validarEntregaRecibo( $conex, $wbasedato, $cco, $hab, $his, $ing, $fecha, $rondaInicial, $totalRondas, $proceso )
   {

	global $whora_par_actual;

	$whora_par_actual = $rondaInicial;

	$val = true;

	if( $proceso == "Ent" ){	//entrega de paciente

		if( !es_cirugia_o_urgencias( $cco ) ){

			$rondaInicialUnix = strtotime( $fecha." ".$rondaInicial.":00:00" );

			//recorro el rango de rondas por aplicar
			for( $ronda = $rondaInicialUnix; $ronda < $rondaInicialUnix + $totalRondas*2*3600; $ronda += 2*3600 ){

				if( $val ){

					$habitacionesFaltantes = '';		//indica que habitaciones hay sin aplicar

					$whora_par_actual = date( "H", $ronda );

					//Valido que no falten aplicaciones para la ronda de entrega de pacientes
					$haySinAplicar = estaAplicadoCcoPorRonda( $cco, date( "Y-m-d", $ronda ), date( "H", $ronda )*1, $habitacionesFaltantes );

					if( !$haySinAplicar && !empty($habitacionesFaltantes) ){

						//Si hay habitaciones sin aplicar busco si se encuentra la habitacion del paciente
						//$habitacionesFaltantes es un array cuyo valor tiene las habitaciones sin aplicar
						for( $i = 0; $i < count($habitacionesFaltantes); $i++ ){

							//Si se encuentra la habitacion del paciente significa que no debe hacerse la entrega
							if( strtoupper( $hab ) == strtoupper( $habitacionesFaltantes[$i] ) ){
								$val = false;
								break;
							}
						}
					}
				}
			}
		}

		unset( $whora_par_actual );
	}

	unset( $whora_par_actual );

	return $val;
}


// Detalle de los articulos que tiene grabado el paciente
function Detalle_ent_rec($wtip, $whis, $wing, $wbasedatos, &$res_medic)
    {


	global $conex;
	global $wnum_art;

	// ================================================================================================
	// Aca traigo los articulos del paciente que tienen saldo, osea que falta Aplicarselos
	if($wtip=='NoApl')
	{
		$q = " SELECT spaart, ROUND(sum(spauen-spausa),2) as suma "
		. "   FROM " . $wbasedatos . "_000004 "
		. "  WHERE spahis                            = '" . $whis . "'"
		. "    AND spaing                            = '" . $wing . "'"
		. "    AND ROUND((spauen-spausa),4) > 0 "
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";

	}
	else
	{

		$q = " SELECT Eyrnum, Fecha_data, Hora_data "
		. "   FROM " . $wbasedatos . "_000017 "
		. "  WHERE eyrhis                            = '" . $whis . "'"
		. "    AND eyring                            = '" . $wing . "'"
		. "    AND eyrtip= 'Entrega' "
		. "  ORDER BY 2 desc, 3 desc";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		$q = " SELECT Detart, sum(Detcan) as suma "
		. "   FROM " . $wbasedatos . "_000019 "
		. "  WHERE detnum                        = '" . $row['Eyrnum']  . "'"
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";

	}
	$res_medic = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num_medic = mysql_num_rows($res_medic);

	if ($num_medic >= 1)
	{
		$wnum_art = $num_medic;
    }


}

function cancelar_entrega($wbasedato, $wid_ent )
{

    global $conex;

    $q_ent_rec = " UPDATE ".$wbasedato."_000017 "
                ."    SET Eyrest = 'off' "
                ."  WHERE Eyrnum = '".$wid_ent."'";
    $res_ent_rec = mysql_query($q_ent_rec, $conex) or die (mysql_errno() . $q_ent_rec . " - " . mysql_error());

    //Cambia de estado los articulos entregados
    $q_art = " UPDATE ".$wbasedato."_000019 "
            ."    SET Detest = 'off' "
            ."  WHERE Detnum = '".$wid_ent."'";
    $res_arti = mysql_query($q_art, $conex) or die (mysql_errno() . $q_art . " - " . mysql_error());

}



//Esta funcion hace la grabacion de articulos en la tabla 17 y 19 de movhos (articulos del paciente)
function grabar_entrega($wbasedato, $whis, $wing, $wcco_origen, $wcco_destino, $whab_origen, $whab_destino, $wid)
{

    global $conex;
    global $wccoapl;
    global $wnum_art;
    global $wcan_art;
    global $wuser;
    global $woriapl;

    $went_rec = 'Ent';  // Se declara esta variable ya que estamos simulando una entrega.

    $wfecha = date("Y-m-d");
    $whora  = date("H:i:s");

    $q = "lock table " . $wbasedato . "_000001 LOW_PRIORITY WRITE";
    $err = mysql_query($q, $conex);

    //Generamos el consecutivo
     $q = " UPDATE " . $wbasedato . "_000001 "
        . "    SET connum=connum + 1 "
        . "  WHERE contip='entyrec' ";
    $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

    $q = "SELECT connum "
    . "  FROM " . $wbasedato . "_000001 "
    . " WHERE contip='entyrec' ";
    $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
    $row = mysql_fetch_array($err);
    $wconsec = $row['connum'];

    $q = " UNLOCK TABLES";
    $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

    //Se registra la entrega del paciente.
    $q = " INSERT INTO " . $wbasedato . "_000017 (   Medico       ,   Fecha_data,   Hora_data,   Eyrnum     ,   Eyrhis  ,   Eyring  ,   Eyrsor  ,   Eyrsde         ,   Eyrhor  ,   Eyrhde         ,   Eyrtip   , Eyrest, Eyrids, Eyrint, Seguridad     ) "
        ."                                VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $whora . "','" . $wconsec . "','" . $whis . "','" . $wing . "','" . $wcco_destino . "','" . $wcco_origen. "','" . $whab_origen . "','" . $whab_destino . "','Entrega', 'on', '" . $wid . "' , 'on'  , 'C-" . $wuser . "')";
    $err = mysql_query($q, $conex) or die (mysql_errno().$q." - ".mysql_error());

    //busca si el centro de costos aplica
    $q = " SELECT ccoapl "
        ."   FROM ".$wbasedato."_000011 "
        ."  WHERE ccocod = '".$wcco_origen."'";
    $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res);

    if ($num > 0)
        {

        $row = mysql_fetch_array($res);

        if ($row['ccoapl'] == "on")
            {
                $wccoapl = "on";
            }
            else
                {
                $wccoapl = "off";
                }
        }
    else
        {
        $wccoapl = "off";
        }

    if ($wccoapl == "off")
        {
            Detalle_ent_rec('NoApl', $whis, $wing, $wbasedato, $res_medic, $num_art); //En esta funcion se muestran todos los articulos que tiene saldo
        }
        else
            {
                Detalle_ent_rec('Apl', $whis, $wing, $wbasedato, $res_medic, $num_art); //En esta funcion se muestran todos los articulos que se entregaron a otro servicio
            }



        // El $num_art viene de la funcion Detalle_ent_rec
        // Aca se graba el detalle del movimiento si lo hay
        if (isset($wnum_art))
        {
            $j = 0;
            if (!isset($wnum_art))
                {
                    $wcan_art = $j;
                }
            else
                {
                    $wcan_art = $wnum_art;
                }

                // Averiguo que clase de centro de costos es el destino
                // Traigo el INDICADOR de si el centro de costo es hospitalario o No
                $q = " SELECT ccoapl "
                    ."   FROM ".$wbasedato."_000011 "
                    ."  WHERE ccocod = '".$wcco_destino."'";
                $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $num = mysql_num_rows($res);

                if ($num > 0)
                    {

                    $row = mysql_fetch_array($res);

                    if ($row['ccoapl'] == "on")
                        {
                            $wdesapl = "on";
                        }
                        else
                            {
                            $wdesapl = "off";
                            }
                    }
                else
                    {
                    $wdesapl = "off";
                    }


                //busca si el centro de costos aplica
                $q = " SELECT ccoapl "
                    ."   FROM ".$wbasedato."_000011 "
                    ."  WHERE ccocod = '".$wcco_origen."'";
                $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $num = mysql_num_rows($res);

                if ($num > 0)
                    {

                    $row = mysql_fetch_array($res);

                    if ($row['ccoapl'] == "on")
                        {
                            $woriapl = "on";
                        }
                        else
                            {
                            $woriapl = "off";
                            }
                    }
                else
                    {
                    $woriapl = "off";
                    }

            //Hago el registro de medicamentos del paciente en la tabla 19 de  movhos, la variable $res_medic viene de la funcion Detalle_ent_rec.
            while ($row_medicamentos = mysql_fetch_array($res_medic))
                {

                $q = " INSERT INTO ".$wbasedato."_000019 (   Medico       ,   Fecha_data,   Hora_data,   Detnum     ,   Detart             ,  Detcan            , Detest, Seguridad     ) "
                    ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wconsec."','".$row_medicamentos[0]."','".$row_medicamentos['suma']."', 'on'  , 'C-".$wuser."')";
                $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

                $wctr = $row_medicamentos['suma'];     //Cantidad a trasladar

                if ($went_rec == "Ent") // ******* ENTREGA *******
                {
                    if($wdesapl=='off' and $woriapl=='on')
                    {
                        // =========================================================================================================================================
                        // Aca hago el traslado de los saldos de la tabla 000030 a la 000004, si el centro de costo aplica automaticamente cuando se factura. Ej:UCI
                        // =========================================================================================================================================
                        $q = " SELECT spluen, splusa, splaen, splasa, splcco, Ccopap "
                            ."   FROM ".$wbasedato."_000030, ".$wbasedato."_000011 "
                            ."  WHERE splhis = '".$whis."'"
                            ."    AND spling = '".$wing."'"
                            ."    AND splart = '".$row_medicamentos[0]."'"
                            ."    AND (splcco = '".$wcco_origen."'"      //2 de Mayo de 2008
                            ."     OR  splcco = ccocod "                 //2 de Mayo de 2008
                            ."    AND  ccotra = 'on') "                  //2 de Mayo de 2008
                            ."    Order by 6";
                        $rest = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
                        $num = mysql_num_rows($rest);

                        for ($j = 1;$j <= $num;$j++)
                        {
                            $row = mysql_fetch_array($rest);

                            $wuen = $row['spluen']; //Unix entradas
                            $wusa = $row['splusa']; //Unix salidas
                            $waen = $row['splaen']; //Aprovechamientos entradas
                            $wasa = $row['splasa']; //Aprovechamientos salidas
                            $wscc = $row['splcco']; //centro de costos que grabo

                            if(($wuen-$wusa)>0)
                            {
                                if(($wuen-$wusa)<$wctr)
                                    {
                                    $wcta=$wuen-$wusa;
                                    $wctr=$wctr-$wcta;
                                    }
                                    else
                                    {
                                    $wcta=$wctr;
                                    $wctr=0;
                                    }

                                if($wctr<=0)
                                    {
                                    $j=$num+1;
                                    }

                                if (($wuen-$wusa-$waen+$wasa) >= $wcta) // La cantidad en la 000030 es mayor a lo que se va a trasladar
                                    {
                                    $q=  " SELECT id "
                                        ."   FROM ".$wbasedato."_000004 "
                                        ."  WHERE Spahis = '".$whis."' "
                                        ."    AND Spaing = '".$wing."' "
                                        ."    AND Spacco = '".$wscc."' "
                                        ."    AND Spaart = '".$row_medicamentos[0]."' ";
                                    $errs = mysql_query($q,$conex);
                                    $nums = mysql_num_rows($errs);

                                    if ($nums > 0)
                                        {
                                        $q = " UPDATE ".$wbasedato."_000004 "
                                            ."    SET spauen = spauen+ ".$wcta
                                            ."  WHERE spahis = '".$whis."'"
                                            ."    AND spaing = '".$wing."'"
                                            ."    AND spacco = '".$wscc."'"
                                            ."    AND spaart = '".$row_medicamentos[0]."'";
                                        $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                                        }
                                        else
                                        {
                                            $q=  " INSERT INTO ".$wbasedato."_000004 (   medico       ,    Fecha_data      ,    Hora_data       ,    Spahis  ,    Spaing ,    Spacco  ,    Spaart              ,   Spauen , Spausa, Spaaen, Spaasa, Seguridad     ) "
                                                ."                            VALUES ('".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."',  ".$wing.", '".$wscc."', '".$row_medicamentos[0]."', ".$wcta.", 0     , 0     , 0     , 'A-".$wuser."') ";

                                            $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                        }

                                    $q = " UPDATE ".$wbasedato."_000030 "
                                        ."    SET spluen = spluen-".$wcta
                                        ."  WHERE splhis = '".$whis."'"
                                        ."    AND spling = '".$wing."'"
                                        ."    AND splcco = '".$wscc."'"
                                        ."    AND splart = '".$row_medicamentos[0]. "'";
                                    $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                                    AnularAplicacion($whis, $wing, $wcco_origen, strtoupper($row_medicamentos[0]), $wartnom, $wcta, $wuser, 'off');

                                    }

                                if (($wuen - $wusa - $waen) < $wcta) // La cantidad en la 000030 es menor a lo que se va a trasladar
                                {
                                    $q= " SELECT id "
                                    ."      FROM ".$wbasedato."_000004 "
                                    ."     WHERE Spahis	= '".$whis."' "
                                    ."       AND Spaing	= '".$wing."' "
                                    ."       AND Spacco	= '".$wscc."' "
                                    ."       AND Spaart	= '".$row_medicamentos[0]."' ";
                                    $errs = mysql_query($q,$conex);
                                    $nums = mysql_num_rows($errs);

                                    if ($nums > 0)
                                        {
                                        $q = " UPDATE ".$wbasedato."_000004 "
                                            ."    SET spauen = spauen+".$wcta.", "
                                            ."        spaaen = spaaen+".($wcta - ($wuen - $wusa - $waen+ $wasa))
                                            ."  WHERE spahis = '".$whis."'"
                                            ."    AND spaing = '".$wing."'"
                                            ."    AND spacco = '".$wscc."'"
                                            ."    AND spaart = '".$row_medicamentos[0]."'";
                                        $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                        }
                                    else
                                        {
                                        $q=  " INSERT INTO ".$wbasedato."_000004 (    medico,          Fecha_data,           Hora_data,            Spahis,          Spaing,             Spacco,            Spaart,   Spauen,   Spausa,   Spaaen,  Spaasa,         Seguridad) "
                                            ."                            VALUES ( '".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."',  ".$wing.", '".$wscc."', '".$row_medicamentos[0]."', ".$wcta.", 0, ".($wcta - ($wuen - $wusa - $waen + $wasa)).", 0, 'A-".$wuser."')";

                                        $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                                        }


                                    $q = " UPDATE ".$wbasedato."_000030 "
                                        ."    SET spluen = spluen-".$wcta.", "
                                        ."        splaen = splaen-".($wcta - ($wuen - $wusa - $waen + $wasa))
                                        ."  WHERE splhis = '".$whis."'"
                                        ."    AND spling = '".$wing."'"
                                        ."    AND splcco = '".$wscc."'"
                                        ."    AND splart = '".$row_medicamentos[0]."'";
                                    $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

                                    if (($wuen - $wusa - $waen + $wasa)>0)
                                    {
                                        AnularAplicacion($whis, $wing, $wcco_origen, strtoupper($row_medicamentos[0]), $wartnom, ($wuen - $wusa - $waen + $wasa), $wuser, 'off');
                                    }

                                    if (($wcta - ($wuen - $wusa - $waen + $wasa))>0)
                                    {
                                        AnularAplicacion($whis, $wing, $wcco_origen, strtoupper($row_medicamentos[0]), $wartnom, ($wcta - ($wuen - $wusa - $waen + $wasa)), $wuser, 'on');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }	// Fin grabación de detalle


}


//Buscar el centro de costos de la habitacion
function centro_costos_hab($whab)
{

    global $conex;
    global $wbasedatos;

    $q = " SELECT habcco "
        ."   FROM ".$wbasedatos."_000020"
        ."  WHERE habcod = '".$whab."'";
    $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
    $row = mysql_fetch_array($res);

    return $row['habcco'];
}


//Funcion que actualiza la solicitud a realizada en la tabla 10 de cencam, y actualiza el identificador con la fecha y hora de llegada
//si no la tiene, ademas de la fecha y hora de cumplimiento si no la tiene.
function actualizarregistros($wcencam, $wid_solici, $whistoria, $whab)
{

    global $conex;

    $wfecha = date("Y-m-d");
    $whora  = date("H:i:s");

    //La solicitud se cambia a realizado en la tabla 10 de cencam.
    $q=  " UPDATE ".$wcencam."_000010 "
        ."    SET Acarea = 'on' "
        ."  WHERE Acaids ='".$wid_solici."'"
        ."    AND Acaest = 'on'";
    mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

    //Se consultan los datos para la solicitud
    $sql = "SELECT Fecha_llegada, Hora_llegada, Fecha_Cumplimiento, Hora_cumplimiento
			  FROM ".$wcencam."_000003
			 WHERE id = '".$wid_solici."'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$row = mysql_fetch_array($res);

    $wfecha_llegada = $row['Fecha_llegada'];
    $wfecha_cumplimiento = $row['Fecha_Cumplimiento'];

    //Aqui actualizamos la fecha de llegada si no tiene.
    if ($wfecha_llegada == '0000-00-00')
    {
    $q=  " UPDATE ".$wcencam."_000003 "
        ."    SET Fecha_llegada = '".$wfecha."', Hora_llegada = '".$whora."', hab_asignada = '".$whab."' "
        ."  WHERE id ='".$wid_solici."'"
        ."    AND Anulada = 'No'";
    mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
    }

    //Aqui actualizamos la fecha de cumplimiento si no tiene.
    if ($wfecha_cumplimiento == '0000-00-00')
    {
   $q=  " UPDATE ".$wcencam."_000003 "
        ."    SET Fecha_cumplimiento = '".$wfecha."', Hora_cumplimiento = '".$whora."', hab_asignada = '".$whab."' "
        ."  WHERE id ='".$wid_solici."'"
        ."    AND Anulada = 'No'";
    mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
    }


}

//Esta es la cancelar el intercambio de pacientes
function iniciar_cancelacion($wemp_pmla, $wbasedatos, $whaba, $whabb, $wcenmez, $wuser)
    {

    global $conex;

    $datamensaje = array('mensaje'=>'', 'error'=>0);

    $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Camilleros');
    $wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

    //Consultamos inicialmente si el paciente tiene historia relacionada en la tabla 20 de movhos (Habitacion A)
    $q_hab_a =  " SELECT habhis, habing, habcod"
                ."   FROM ".$wbasedatos."_000020"
                ."  WHERE habcod = '".$whaba."'";
    $res_hab_a = mysql_query($q_hab_a,$conex)or die(mysql_errno().":".mysql_error());
    $row_hab_a = mysql_fetch_array($res_hab_a);
    $whistoriaa = $row_hab_a['habhis'];
    $wingresoa = $row_hab_a['habing'];
    $wcco_a = centro_costos_hab($whaba);

    //Si no tiene historia, entonces el muestra una alerta diciendo que la habitacion no se encuentra ocupada.
    if (trim($whistoriaa) == '')
    {
        $datamensaje['mensaje'] = "La habitacion A ".$whaba." no se encuentra ocupada.";
        $datamensaje['error'] = 1;
    }
    else
    {
       //Consultamos inicialmente si el paciente tiene historia relacionada en la tabla 20 de movhos (Habitacion B)
        $q_hab_b =   " SELECT habhis, habing, habcod "
                    ."   FROM ".$wbasedatos."_000020"
                    ."  WHERE habcod = '".$whabb."'";
        $res_hab_b = mysql_query($q_hab_b,$conex)or die(mysql_errno().":".mysql_error());
        $row_hab_b = mysql_fetch_array($res_hab_b);
        $whistoriab = $row_hab_b['habhis'];
        $wingresob = $row_hab_b['habing'];
        $wcco_b = centro_costos_hab($whabb);

        //Si no tiene historia, entonces el muestra una alerta diciendo que la habitacion no se encuentra ocupada.
        if(trim($whistoriab) == '')
        {
            $datamensaje['mensaje'] = "La habitacion B ".$whabb." no se encuentra ocupada.";
            $datamensaje['error'] = 1;

        }
        else
        {
            //Se verifica si la historia e ingreso asociada a la habitacion A está en proceso de traslado y tiene intercambio reciente,
            //si es asi se valida la habitacion b, sino muestra mensaje.
            $q_soli_a =  " SELECT ubiptr, eyrint, eyrids, eyrnum "
                        ."   FROM ".$wbasedatos."_000017, ".$wbasedatos."_000018 "
                        ."  WHERE ubihis = eyrhis "
                        ."    AND ubiing = eyring "
                        ."	  AND ubihis = '".$whistoriaa."'"
                        ."	  AND ubiing = '".$wingresoa."'"
                        ."    AND eyrest != 'off'"
                        ."    AND ubiald != 'on'"
                      ." ORDER BY ".$wbasedatos."_000017.Fecha_data DESC "    ;
            $res_soli_a = mysql_query($q_soli_a,$conex)or die(mysql_errno().":".mysql_error());
            $row_soli_a = mysql_fetch_array($res_soli_a);
            $wptr_a = $row_soli_a['ubiptr'];  //En proceso de traslado
            $wintercambio_a = $row_soli_a['eyrint']; //Estado del ultimo registro en la tabla 17 de movhos
            $wid_solici_a = $row_soli_a['eyrids']; //Id la solicitud de cama
            $wid_ent_a = $row_soli_a['eyrnum']; //Id entrega del paciente

            //Verifica que el paciente este en proceso de traslado, osea que no hay sido recibido.
            if($wptr_a != 'on')
            {

                $datamensaje['mensaje'] = "El paciente de la habitacion ".$whaba." ya fue recibido, no puede realizar la cancelacion.";
                $datamensaje['error'] = 1;

            }
            else
            {

                if ($wintercambio_a != 'on')
                {
                     $datamensaje['mensaje'] = "El ultimo movimiento de la ".$whaba." no ha sido un intercambios.";
                     $datamensaje['error'] = 1;
                }
                else
                {


                    //Se verifica si la historia e ingreso asociada a la habitacion B está en proceso de traslado y tiene intercambio reciente,
                    //si es asi se valida la habitacion b, sino muestra mensaje.
                    $q_soli_a =  " SELECT ubiptr, eyrint, eyrids, eyrnum "
                                ."   FROM ".$wbasedatos."_000017, ".$wbasedatos."_000018 "
                                ."  WHERE ubihis = eyrhis "
                                ."    AND ubiing = eyring "
                                ."	  AND ubihis = '".$whistoriab."'"
                                ."	  AND ubiing = '".$wingresob."'"
                                ."    AND eyrest != 'off'"
                                ."    AND ubiald != 'on'"
                              ." ORDER BY ".$wbasedatos."_000017.Fecha_data DESC "    ;
                    $res_soli_a = mysql_query($q_soli_a,$conex)or die(mysql_errno().":".mysql_error());
                    $row_soli_a = mysql_fetch_array($res_soli_a);
                    $wptr_b = $row_soli_a['ubiptr'];  //En proceso de traslado
                    $wintercambio_b = $row_soli_a['eyrint']; //Estado del ultimo registro en la tabla 17 de movhos
                    $wid_solici_b = $row_soli_a['eyrids']; //Id la solicitud de cama
                    $wid_ent_b = $row_soli_a['eyrnum']; //Id entrega del paciente

                    //Si no hay registros mostrara un mensaje diciendo que "No hay solicitud de cama para la habitacion B"
                   if($wptr_b != 'on')
                    {

                        $datamensaje['mensaje'] = "El paciente de la habitacion ".$whabb." ya fue recibido, no puede realizar la cancelacion.";
                        $datamensaje['error'] = 1;

                    }
                    else
                    {
                         // Verifica si tiene intercambio reciente.
                         if ($wintercambio_b != 'on')
                            {
                                $datamensaje['mensaje'] = "El ultimo movimiento de la ".$whabb." no ha sido un intercambios.";
                                $datamensaje['error'] = 1;
                            }
                            else
                                {

                                //Esta funcion trae la informacion del paciente para mostrarla antes de intercambiar las habitaciones.
                                $datospacientea = datosPaciente($whaba);
                                $datospacienteb = datosPaciente($whabb);

                                $texto_html .="<table id='inf_actual' style='width: auto;' border='0' align='center' cellpadding='0' cellspacing='0'>";
                                $texto_html .= "<tr>";
                                $texto_html .= "<td colspan=5 align='center' class='encabezadotabla'>INFORMACION ACTUAL DE LAS HABITACIONES</td>";
                                $texto_html .= "</tr>";
                                $texto_html .= "<td colspan=5 align='center'></td>";
                                $texto_html .= "</tr>";
                                $texto_html .= "<td colspan='2'>";
                                $texto_html .= "<table width=100% style='float:left'>
                                                    <tr>
                                                        <td colspan=3 class='titulo_grande' align='center'><b>HABITACION ".$whaba."<b></td>
                                                    </tr>
                                                    <tr>
                                                        <td  class='encabezadotabla'></td>
                                                        <td  class='encabezadotabla'>Documento</td>
                                                        <td   class='encabezadotabla'>Nombre</td>
                                                    </tr>
                                                    <tr>
                                                        <td  class='encabezadotabla'>Paciente</td>
                                                        <td class='fila2' id='documento1' >".$datospacientea['tipodoc'].". ".$datospacientea['doc']."</td>
                                                        <td  class='fila2' id='primer_nombre1'>".$datospacientea['primer_nombre']." ".$datospacientea['segundo_nombre']." ".$datospacientea['primer_apellido']." ".$datospacientea['segundo_apellido']."</td>
                                                    </tr>
                                                    <tr>
                                                        <td  class='encabezadotabla'>Historia</td>
                                                        <td  colspan=2 style='text-align:center' class='fila2' id='tit_historia1' ><b>".$datospacientea['historia']." - ".$datospacientea['ingreso']."</b></td>
                                                    </tr>
                                                </table>";
                                    $texto_html .= "</td>";
                                    $texto_html .= "<td style='width: 15px;'></td> ";
                                    $texto_html .= "<td colspan='2'>";
                                    $texto_html .= "<table width=100% style='float:left' >
                                                    <tr>
                                                        <td colspan=3 class='titulo_grande' align='center'><b>HABITACION ".$whabb."<b></td>
                                                    </tr>
                                                    <tr>
                                                        <td  class='encabezadotabla'></td>
                                                        <td  class='encabezadotabla'>Documento</td>
                                                        <td  class='encabezadotabla'>Nombre</td>
                                                    </tr>
                                                    <tr>
                                                        <td  class='encabezadotabla'>Paciente</td>
                                                        <td  class='fila2' id='documento2'>".$datospacienteb['tipodoc'].". ".$datospacienteb['doc']."</td>
                                                        <td  class='fila2' id='primer_nombre2'>".$datospacienteb['primer_nombre']." ".$datospacienteb['segundo_nombre']." ".$datospacienteb['primer_apellido']." ".$datospacienteb['segundo_apellido']."</td>
                                                    </tr>
                                                    <tr>
                                                        <td  class='encabezadotabla'>Historia</td>
                                                        <td  colspan=2 style='text-align:center' class='fila2' id='tit_historia2' ><b>".$datospacienteb['historia']." - ".$datospacienteb['ingreso']."<b></td>
                                                    </tr>
                                                </table>";
                                    $texto_html .= "</td>";
                                    $texto_html .= "</tr>";
                                    $texto_html .="</table>";
                                    $texto_html .="<br><br><br><br>";

                                    $texto_html .="<table id='inf_actual' style='width: auto;' border='0' align='center' cellpadding='0' cellspacing='0' id='inf_final'>";
                                    $texto_html .= "<tr>";
                                    $texto_html .= "<td colspan=5 align='center' class='encabezadotabla'>RESULTADO AL HACER EL INTERCAMBIO</td>";
                                    $texto_html .= "</tr>";
                                    $texto_html .= "<tr>";
                                    $texto_html .= "<td colspan='2'>";
                                    $texto_html .= "<table width=100% style='float:left'>
                                                        <tr>
                                                            <td colspan=3 class='titulo_grande' align='center'><b>HABITACION ".$whaba."<b></td>
                                                        </tr>
                                                        <tr>
                                                            <td  class='encabezadotabla'></td>
                                                            <td  class='encabezadotabla'>Documento</td>
                                                            <td   class='encabezadotabla'>Nombre</td>
                                                        </tr>
                                                        <tr>
                                                            <td  class='encabezadotabla'>Paciente</td>
                                                            <td class='fila2' id='documento1' >".$datospacienteb['tipodoc'].". ".$datospacienteb['doc']."</td>
                                                            <td  class='fila2' id='primer_nombre1'>".$datospacienteb['primer_nombre']." ".$datospacienteb['segundo_nombre']." ".$datospacienteb['primer_apellido']." ".$datospacienteb['segundo_apellido']."</td>
                                                        </tr>
                                                        <tr>
                                                            <td  class='encabezadotabla'>Historia</td>
                                                            <td  colspan=2 style='text-align:center' class='fila2' id='tit_historia1' ><b>".$datospacienteb['historia']." - ".$datospacienteb['ingreso']."</b></td>
                                                        </tr>
                                                    </table>";
                                        $texto_html .= "</td>";
                                        $texto_html .= "<td style='width: 15px;'></td> ";
                                        $texto_html .= "<td colspan='2'>";
                                        $texto_html .= "
                                                    <table width=100% style='float:left' >
                                                        <tr>
                                                            <td colspan=3 class='titulo_grande' align='center'><b>HABITACION ".$whabb."<b></td>
                                                        </tr>
                                                        <tr>
                                                            <td  class='encabezadotabla'></td>
                                                            <td  class='encabezadotabla'>Documento</td>
                                                            <td  class='encabezadotabla'>Nombre</td>
                                                        </tr>
                                                        <tr>
                                                            <td  class='encabezadotabla'>Paciente</td>
                                                            <td  class='fila2' id='documento2'>".$datospacientea['tipodoc'].". ".$datospacientea['doc']."</td>
                                                            <td  class='fila2' id='primer_nombre2'>".$datospacientea['primer_nombre']." ".$datospacientea['segundo_nombre']." ".$datospacientea['primer_apellido']." ".$datospacientea['segundo_apellido']."</td>
                                                        </tr>
                                                        <tr>
                                                            <td  class='encabezadotabla'>Historia</td>
                                                            <td  colspan=2 style='text-align:center' class='fila2' id='tit_historia2' ><b>".$datospacientea['historia']." - ".$datospacientea['ingreso']."</b></td>
                                                        </tr>
                                                    </table>";
                                        $texto_html .= "</td>";
                                        $texto_html .= "</tr>";
                                        $texto_html .="</table>";
                                        $texto_html .= "<br>";
                                        $texto_html .= "<input type='button' id='boton_cancel_interc' onclick='cancelar_intercambio(\"$wemp_pmla\", \"$wbasedatos\" , \"$wcenmez\" , \"$wuser\" , \"$whistoriaa\", \"$wingresoa\", \"$whistoriab\", \"$wingresob\", \"$wid_solici_a\", \"$wid_solici_b\", \"$wcco_a\", \"$wcco_b\" , \"$wcencam\", \"$wid_ent_a\", \"$wid_ent_b\")' value='Aceptar' class='botona'><input type=button onclick='enter();' value='Retornar' class='botona'></input>";
                                    }
                    }
                }
            }
        }


    }

        $datamensaje['table'] = $texto_html;

        echo json_encode($datamensaje);

    }


//Esta es la funcion principal, la cual valida varios registros que debe tener el paciente para poder realiar el intercambio
function iniciar_intercambio($wemp_pmla, $wbasedatos, $whaba, $whabb, $wcenmez, $wuser)
    {

    global $conex;

    $datamensaje = array('mensaje'=>'', 'error'=>0);

    $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Camilleros');
    $wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

    //Consultamos inicialmente si el paciente tiene historia relacionada en la tabla 20 de movhos (Habitacion A)
    $q_hab_a =  " SELECT habhis, habing, habcod"
                ."   FROM ".$wbasedatos."_000020"
                ."  WHERE habcod = '".$whaba."'";
    $res_hab_a = mysql_query($q_hab_a,$conex)or die(mysql_errno().":".mysql_error());
    $row_hab_a = mysql_fetch_array($res_hab_a);
    $whistoriaa = $row_hab_a['habhis'];
    $wingresoa = $row_hab_a['habing'];
    $wcco_a = centro_costos_hab($whaba);

    //Si no tiene historia, entonces el muestra una alerta diciendo que la habitacion no se encuentra ocupada.
    if (trim($whistoriaa) == '')
    {
        $datamensaje['mensaje'] = "La habitacion A ".$whaba." no se encuentra ocupada.";
        $datamensaje['error'] = 1;
    }
    else
    {
       //Consultamos inicialmente si el paciente tiene historia relacionada en la tabla 20 de movhos (Habitacion B)
        $q_hab_b =   " SELECT habhis, habing, habcod "
                    ."   FROM ".$wbasedatos."_000020"
                    ."  WHERE habcod = '".$whabb."'";
        $res_hab_b = mysql_query($q_hab_b,$conex)or die(mysql_errno().":".mysql_error());
        $row_hab_b = mysql_fetch_array($res_hab_b);
        $whistoriab = $row_hab_b['habhis'];
        $wingresob = $row_hab_b['habing'];
        $wcco_b = centro_costos_hab($whabb);

        //Si no tiene historia, entonces el muestra una alerta diciendo que la habitacion no se encuentra ocupada.
        if(trim($whistoriab) == '')
        {
            $datamensaje['mensaje'] = "La habitacion B ".$whabb." no se encuentra ocupada.";
            $datamensaje['error'] = 1;

        }
        else
        {
            //Aqui se verifica que la habitacion A tenga solicitud de cama.
            $q_soli_a =  " SELECT historia, id "
                        ."   FROM ".$wcencam."_000003 "
                        ."  WHERE historia = '".$whistoriaa."'"
                        ."    AND Fecha_llegada = '0000-00-00'"
                        ."	  AND Hora_llegada = '00:00:00'"
                        ."    AND Fecha_cumplimiento = '0000-00-00'"
                        ."    AND Hora_cumplimiento = '00:00:00'"
                        ."    AND Anulada = 'No'"
                        ."    AND Central = '".$wcentral_camas."'";
            $res_soli_a = mysql_query($q_soli_a,$conex)or die(mysql_errno().":".mysql_error());
            $num_soli_a = mysql_num_rows($res_soli_a);
            $row_soli_a = mysql_fetch_array($res_soli_a);
            $wid_solici_a = $row_soli_a['id'];

            //Si no hay registros mostrara un mensaje diciendo que "No hay solicitud de cama para la habitacion A"
            if($num_soli_a == 0)
            {

                $datamensaje['mensaje'] = "No hay solicitud de cama para la habitacion ".$whaba."";
                $datamensaje['error'] = 1;

            }
            else
                {
                    //Aqui se verifica que la habitacion A tenga solicitud de cama.
                    $q_soli_b =  " SELECT historia, id "
                                ."   FROM ".$wcencam."_000003 "
                                ."  WHERE historia = '".$whistoriab."'"
                                ."    AND Fecha_llegada = '0000-00-00'"
                                ."	  AND Hora_llegada = '00:00:00'"
                                ."    AND Fecha_cumplimiento = '0000-00-00'"
                                ."    AND Hora_cumplimiento = '00:00:00'"
                                ."    AND Anulada = 'No'"
                                ."    AND Central = '".$wcentral_camas."'";
                    $res_soli_b = mysql_query($q_soli_b,$conex)or die(mysql_errno().":".mysql_error());
                    $num_soli_b = mysql_num_rows($res_soli_b);
                    $row_soli_b = mysql_fetch_array($res_soli_b);

                    $wid_solici_b = $row_soli_b['id'];

                    //Si no hay registros mostrara un mensaje diciendo que "No hay solicitud de cama para la habitacion B"
                    if($num_soli_b == 0)
                    {
                        $datamensaje['mensaje'] = "No hay solicitud de cama para la habitacion ".$whabb."";
                        $datamensaje['error'] = 1;
                    }
                    else
                    {
                        //Esta funcion trae la informacion del paciente para mostrarla antes de intercambiar las habitaciones.
                        $datospacientea = datosPaciente($whaba);
                        $datospacienteb = datosPaciente($whabb);

                        $texto_html .="<table id='inf_actual' style='width: auto;' border='0' align='center' cellpadding='0' cellspacing='0'>";
                        $texto_html .= "<tr>";
                        $texto_html .= "<td colspan=5 align='center' class='encabezadotabla'>INFORMACION ACTUAL DE LAS HABITACIONES</td>";
                        $texto_html .= "</tr>";
                        $texto_html .= "<td colspan=5 align='center'></td>";
                        $texto_html .= "</tr>";
                        $texto_html .= "<td colspan='2'>";
                        $texto_html .= "<table width=100% style='float:left'>
                                            <tr>
                                                <td colspan=3 class='titulo_grande' align='center'><b>HABITACION ".$whaba."<b></td>
                                            </tr>
                                            <tr>
                                                <td  class='encabezadotabla'></td>
                                                <td  class='encabezadotabla'>Documento</td>
                                                <td   class='encabezadotabla'>Nombre</td>
                                            </tr>
                                            <tr>
                                                <td  class='encabezadotabla'>Paciente</td>
                                                <td class='fila2' id='documento1' >".$datospacientea['tipodoc'].". ".$datospacientea['doc']."</td>
                                                <td  class='fila2' id='primer_nombre1'>".$datospacientea['primer_nombre']." ".$datospacientea['segundo_nombre']." ".$datospacientea['primer_apellido']." ".$datospacientea['segundo_apellido']."</td>
                                            </tr>
                                            <tr>
                                                <td  class='encabezadotabla'>Historia</td>
                                                <td  colspan=2 style='text-align:center' class='fila2' id='tit_historia1' ><b>".$datospacientea['historia']." - ".$datospacientea['ingreso']."</b></td>
                                            </tr>
                                        </table>";
                            $texto_html .= "</td>";
                            $texto_html .= "<td style='width: 15px;'></td> ";
                            $texto_html .= "<td colspan='2'>";
                            $texto_html .= "<table width=100% style='float:left' >
                                            <tr>
                                                <td colspan=3 class='titulo_grande' align='center'><b>HABITACION ".$whabb."<b></td>
                                            </tr>
                                            <tr>
                                                <td  class='encabezadotabla'></td>
                                                <td  class='encabezadotabla'>Documento</td>
                                                <td  class='encabezadotabla'>Nombre</td>
                                            </tr>
                                            <tr>
                                                <td  class='encabezadotabla'>Paciente</td>
                                                <td  class='fila2' id='documento2'>".$datospacienteb['tipodoc'].". ".$datospacienteb['doc']."</td>
                                                <td  class='fila2' id='primer_nombre2'>".$datospacienteb['primer_nombre']." ".$datospacienteb['segundo_nombre']." ".$datospacienteb['primer_apellido']." ".$datospacienteb['segundo_apellido']."</td>
                                            </tr>
                                            <tr>
                                                <td  class='encabezadotabla'>Historia</td>
                                                <td  colspan=2 style='text-align:center' class='fila2' id='tit_historia2' ><b>".$datospacienteb['historia']." - ".$datospacienteb['ingreso']."<b></td>
                                            </tr>
                                        </table>";
                            $texto_html .= "</td>";
                            $texto_html .= "</tr>";
                            $texto_html .="</table>";
                            $texto_html .="<br><br><br><br>";

                            $texto_html .="<table id='inf_resultado' style='width: auto;' border='0' align='center' cellpadding='0' cellspacing='0'>";
                            $texto_html .= "<tr>";
                            $texto_html .= "<td colspan=5 align='center' class='encabezadotabla'>RESULTADO AL REALIZAR EL INTERCAMBIO</td>";
                            $texto_html .= "</tr>";
                            $texto_html .= "<tr>";
                            $texto_html .= "<td colspan='2'>";
                            $texto_html .= "<table width=100% style='float:left'>
                                                <tr>
                                                    <td colspan=3 class='titulo_grande' align='center'><b>HABITACION ".$whaba."<b></td>
                                                </tr>
                                                <tr>
                                                    <td  class='encabezadotabla'></td>
                                                    <td  class='encabezadotabla'>Documento</td>
                                                    <td   class='encabezadotabla'>Nombre</td>
                                                </tr>
                                                <tr>
                                                    <td  class='encabezadotabla'>Paciente</td>
                                                    <td class='fila2' id='documento1' >".$datospacienteb['tipodoc'].". ".$datospacienteb['doc']."</td>
                                                    <td  class='fila2' id='primer_nombre1'>".$datospacienteb['primer_nombre']." ".$datospacienteb['segundo_nombre']." ".$datospacienteb['primer_apellido']." ".$datospacienteb['segundo_apellido']."</td>
                                                </tr>
                                                <tr>
                                                    <td  class='encabezadotabla'>Historia</td>
                                                    <td  colspan=2 style='text-align:center' class='fila2' id='tit_historia1' ><b>".$datospacienteb['historia']." - ".$datospacienteb['ingreso']."</b></td>
                                                </tr>
                                            </table>";
                                $texto_html .= "</td>";
                                $texto_html .= "<td style='width: 15px;'></td> ";
                                $texto_html .= "<td colspan='2'>";
                                $texto_html .= "
                                            <table width=100% style='float:left' >
                                                <tr>
                                                    <td colspan=3 class='titulo_grande' align='center'><b>HABITACION ".$whabb."<b></td>
                                                </tr>
                                                <tr>
                                                    <td  class='encabezadotabla'></td>
                                                    <td  class='encabezadotabla'>Documento</td>
                                                    <td  class='encabezadotabla'>Nombre</td>
                                                </tr>
                                                <tr>
                                                    <td  class='encabezadotabla'>Paciente</td>
                                                    <td  class='fila2' id='documento2'>".$datospacientea['tipodoc'].". ".$datospacientea['doc']."</td>
                                                    <td  class='fila2' id='primer_nombre2'>".$datospacientea['primer_nombre']." ".$datospacientea['segundo_nombre']." ".$datospacientea['primer_apellido']." ".$datospacientea['segundo_apellido']."</td>
                                                </tr>
                                                <tr>
                                                    <td  class='encabezadotabla'>Historia</td>
                                                    <td  colspan=2 style='text-align:center' class='fila2' id='tit_historia2' ><b>".$datospacientea['historia']." - ".$datospacientea['ingreso']."</b></td>
                                                </tr>
                                            </table>";
                                $texto_html .= "</td>";
                                $texto_html .= "</tr>";
                                $texto_html .="</table>";
                                $texto_html .= "<br>";
                                $texto_html .= "<input type='button' id='boton_intercambiar' onclick='intercambiar(\"$wemp_pmla\", \"$wbasedatos\" , \"$wcenmez\" , \"$wuser\" , \"$whistoriaa\", \"$wingresoa\", \"$whistoriab\", \"$wingresob\", \"$wid_solici_a\", \"$wid_solici_b\", \"$wcco_a\", \"$wcco_b\" , \"$wcencam\")' value='Aceptar' class='botona'><input type=button onclick='enter();' value='Retornar' class='botona'></input>";


                }
            }
        }


    }

        $datamensaje['table'] = $texto_html;

        echo json_encode($datamensaje);

    }


//Esta es la funcion principal, la cual valida varios registros que debe tener el paciente para poder realiar el intercambio
function intercambiar($wemp_pmla, $wbasedatos, $whaba, $whabb, $wcenmez, $wuser, $whistoriaa, $wingresoa, $whistoriab, $wingresob, $wid_solici_a, $wid_solici_b, $wcco_a,$wcco_b, $wcencam)
    {

    global $conex;
    global $wemp_pmla;

    $datamensaje = array('mensaje'=>'', 'error'=>0);

    /****************************************************************************************
        * Agosto 24 de 2012 (Fecha de creacion tomada del script Ent_y_Rec_Pac.php)
        * Valido que no falten aplicaciones de las ultimas rondas por aplicar pra poder realizar
        * al entrega del paciente
        ****************************************************************************************/
    //total de rondas que se quieren comprobar
    $totalRondas = 2;

    //A la fecha actual le resto total de rondas horas
    $fechaComprobar = time()-($totalRondas-1)*2*3600;			//fecha y hora a comprobar la aplicacion en formato Unix

    //busco la ronda actual
    $rondaInicial = floor( date( "H", $fechaComprobar )/2 )*2;

    //Valido que no falten aplicaciones para la ultimas rondas
    $procesaro = validarEntregaRecibo( $conex, $wbasedatos, $wcco_a, $whaba, $whistoriaa, $wingresoa, date( "Y-m-d", $fechaComprobar ), $rondaInicial, $totalRondas, 'Ent' );

    //Si la variable anterior responde falso se mostrara un mensaje diciendo Faltan medicamentos por aplicar en las ultimas dos rondas para la habitacion A
    if($procesaro)
    {


        //total de rondas que se quieren comprobar
        $totalRondas = 2;

        //A la fecha actual le resto total de rondas horas
        $fechaComprobar = time()-($totalRondas-1)*2*3600;			//fecha y hora a comprobar la aplicacion en formato Unix

        //busco la ronda actual
        $rondaInicial = floor( date( "H", $fechaComprobar )/2 )*2;

        //Valido que no falten aplicaciones para la ultimas rondas
        $procesard = validarEntregaRecibo( $conex, $wbasedatos, $wcco_b, $whabb, $whistoriab, $wingresob, date( "Y-m-d", $fechaComprobar ), $rondaInicial, $totalRondas, 'Ent' );

        //Si la variable anterior responde falso se mostrara un mensaje diciendo Faltan medicamentos por aplicar en las ultimas dos rondas para la habitacion B
        if ($procesard)
            {

                //Se graban los articulos de cada una de las habitaciones (Habitacion A)
                grabar_entrega($wbasedatos, $whistoriaa, $wingresoa, $wcco_a, $wcco_b, $whaba, $whabb, $wid_solici_a);

                //Se graban los articulos de cada una de las habitaciones (Habitacion B)
                grabar_entrega($wbasedatos, $whistoriab, $wingresob, $wcco_b, $wcco_a, $whabb, $whaba, $wid_solici_b);

                //La habitacion A se actualiza con los datos de la habitacion B
                $q_a_pac =       " UPDATE ".$wbasedatos."_000018 "
                                        ."    SET ubisac = '".$wcco_b."',
                                            ubisan = '".$wcco_a."',
                                            ubihac = '".$whabb."',
                                            ubihan = '".$whaba."',
                                            ubiptr = 'on'"
                                        ."  WHERE ubihis = '".$whistoriaa."'"
                                        ."    AND ubiing = '".$wingresoa."'"
                                        ."    AND ubiald != 'on'";
                $res_a_pac = mysql_query($q_a_pac,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_a_pac." - ".mysql_error());

                //La habitacion A se actualiza con los datos de la habitacion B en la tabla 20 de movhos
                $q_a_hab =      " UPDATE ".$wbasedatos."_000020 "
                                    ."    SET habhis = '".$whistoriaa."',
                                              habing = '".$wingresoa."'"
                                    ."  WHERE habhis = '".$whistoriab."'"
                                    ."    AND habing = '".$wingresob."'"
                                    ."    AND habcod = '".$whabb."'";
                $res_a_hab = mysql_query($q_a_hab,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_a_hab." - ".mysql_error());

                //La habitacion B se actualiza con los datos de la habitacion A
                $q_b =     " UPDATE ".$wbasedatos."_000018 "
                                ."    SET ubisac = '".$wcco_a."',
                                          ubisan = '".$wcco_b."',
                                          ubihac = '".$whaba."',
                                          ubihan = '".$whabb."',
                                          ubiptr = 'on'"
                                ."  WHERE ubihis = '".$whistoriab."'"
                                ."    AND ubiing = '".$wingresob."'"
                                ."    AND ubiald != 'on'";
                $res_b = mysql_query($q_b,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_b." - ".mysql_error());

                //La habitacion B se actualiza con los datos de la habitacion A en la tabla 20 de movhos
                $q_a_hab =      " UPDATE ".$wbasedatos."_000020 "
                                    ."    SET habhis = '".$whistoriab."',
                                              habing = '".$wingresob."'"
                                    ."  WHERE habhis = '".$whistoriaa."'"
                                    ."    AND habing = '".$wingresoa."'"
                                    ."    AND habcod = '".$whaba."'";
                $res_a_hab = mysql_query($q_a_hab,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_a_hab." - ".mysql_error());

                //Aqui se guarda la llegada y el cumplimiento de las solicitudes para que no se sigan viendo el listado de solicitudes en la central de camas.
                actualizarregistros($wcencam, $wid_solici_a, $whistoriaa, $whabb);
                actualizarregistros($wcencam, $wid_solici_b, $whistoriab, $whaba);

            }
            else
            {
                $datamensaje['mensaje'] = "Faltan medicamentos por aplicar en las ultimas dos rondas para la habitacion ".$whabb."";
                $datamensaje['error'] = 1;
            }

    }
    else
    {
        $datamensaje['mensaje'] = "Faltan medicamentos por aplicar en las ultimas dos rondas para la habitacion ".$whaba."";
        $datamensaje['error'] = 1;
    }



    echo json_encode($datamensaje);


    }

function registro_auditoria_cambios_traslados($consecutivoTraslado, $fechaRegistro, $horaRegistro, $anulacion, $fechaAnulacion, $horaAnulacion, $modificacion, $fechaModificacion, $horaModificacion, $tipoCambio, $usuario)
{
	global $wbasedato;
	global $conex;

	$q="INSERT INTO
			".$wbasedato."_000039 (medico, Fecha_data, Hora_data, Aunnum, Aunanu, Aunfan, Aunhan, Aunmod, Aunfmo, Aunhmo, Aunacc, Seguridad)
		VALUES
			('movhos','".$fechaRegistro."','".$horaRegistro."','".$consecutivoTraslado."','".$anulacion."','".$fechaAnulacion."','".$horaAnulacion."','".$modificacion."','".$fechaModificacion."','".$horaModificacion."','".$tipoCambio."', 'A-".$usuario."');";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

    //Esta es la funcion principal, la cual valida varios registros que debe tener el paciente para poder realiar el intercambio
function cancelar_intercambio($wemp_pmla, $wbasedatos, $whaba, $whabb, $wcenmez, $wuser, $whistoriaa, $wingresoa, $whistoriab, $wingresob, $wid_solici_a, $wid_solici_b, $wcco_a,$wcco_b, $wcencam, $wid_ent_a, $wid_ent_b)
    {

    global $conex;
    global $wemp_pmla;

    $datamensaje = array('mensaje'=>'', 'error'=>0);

    //Se cancelan los articulos de la habitacion A
    cancelar_entrega($wbasedatos, $wid_ent_a);

    //Se cancelan los articulos de la habitacion B
    cancelar_entrega($wbasedatos, $wid_ent_b);

    //La habitacion A se actualiza con los datos de la habitacion B
    $q_a_pac =      "UPDATE ".$wbasedatos."_000018 "
                   ."   SET ubisac = '".$wcco_b."',
                            ubisan = '".$wcco_a."',
                            ubihac = '".$whabb."',
                            ubihan = '".$whaba."',
                            ubiptr = 'off'"
                   ." WHERE ubihis = '".$whistoriaa."'"
                   ."   AND ubiing = '".$wingresoa."'"
                   ."   AND ubiald != 'on'";
    $res_a_pac = mysql_query($q_a_pac,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_a_pac." - ".mysql_error());

    //La habitacion A se actualiza con los datos de la habitacion B en la tabla 20 de movhos
    $q_a_hab =   " UPDATE ".$wbasedatos."_000020 "
                ."    SET habhis = '".$whistoriaa."',
                          habing = '".$wingresoa."'"
                ."  WHERE habhis = '".$whistoriab."'"
                ."    AND habing = '".$wingresob."'"
                ."    AND habcod = '".$whabb."'";
    $res_a_hab = mysql_query($q_a_hab,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_a_hab." - ".mysql_error());

    //La habitacion B se actualiza con los datos de la habitacion A
     $q_b =   " UPDATE ".$wbasedatos."_000018 "
                ."    SET ubisac = '".$wcco_a."',
                          ubisan = '".$wcco_b."',
                          ubihac = '".$whaba."',
                          ubihan = '".$whabb."',
                          ubiptr = 'off'"
                ."  WHERE ubihis = '".$whistoriab."'"
                ."    AND ubiing = '".$wingresob."'"
                ."    AND ubiald != 'on'";
    $res_b = mysql_query($q_b,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_b." - ".mysql_error());

    //La habitacion B se actualiza con los datos de la habitacion A en la tabla 20 de movhos
    $q_a_hab =      " UPDATE ".$wbasedatos."_000020 "
                        ."    SET habhis = '".$whistoriab."',
                                  habing = '".$wingresob."'"
                        ."  WHERE habhis = '".$whistoriaa."'"
                        ."    AND habing = '".$wingresoa."'"
                        ."    AND habcod = '".$whaba."'";
    $res_a_hab = mysql_query($q_a_hab,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_a_hab." - ".mysql_error());

    //Audita de anulación de entrega
	registro_auditoria_cambios_traslados($wid_ent_a, date("Y-m-d"), (string)date("H:i:s"), "on", date("Y-m-d"), (string)date("H:i:s"), "off", "0-0-0", "0:0:0", "Anulacion", $wuser);
    registro_auditoria_cambios_traslados($wid_ent_b, date("Y-m-d"), (string)date("H:i:s"), "on", date("Y-m-d"), (string)date("H:i:s"), "off", "0-0-0", "0:0:0", "Anulacion", $wuser);

    $datamensaje['mensaje'] = "Se cancelo el intercambio con exito";
    $datamensaje['error'] = 0;

    echo json_encode($datamensaje);


    }

//Este segmento interactua con los llamados ajax

//Si la variable $consultaAjax tiene datos entonces busca la funcion que trae la variable.
if (isset($consultaAjax))
            {
            switch($consultaAjax)
                {

                    case 'intercambiar':
                        {
                            echo intercambiar($wemp_pmla, $wbasedatos, $whaba, $whabb, $wcenmez, $wuser, $whistoriaa, $wingresoa, $whistoriab, $wingresob, $wid_solici_a, $wid_solici_b, $wcco_a,$wcco_b, $wcencam);
                        }
                    break;

                    case 'iniciar_intercambio':
                        {
                            echo iniciar_intercambio($wemp_pmla, $wbasedatos, $whaba, $whabb, $wcenmez, $wuser);
                        }
                    break;

                    case 'iniciar_cancelacion':
                        {
                            echo iniciar_cancelacion($wemp_pmla, $wbasedatos, $whaba, $whabb, $wcenmez, $wuser);
                        }
                    break;

                    case 'cancelar_intercambio':
                        {
                            echo cancelar_intercambio($wemp_pmla, $wbasedatos, $whaba, $whabb, $wcenmez, $wuser, $whistoriaa, $wingresoa, $whistoriab, $wingresob, $wid_solici_a, $wid_solici_b, $wcco_a,$wcco_b, $wcencam, $wid_ent_a, $wid_ent_b);
                        }
                    break;

                    default : break;
                }
            return;
            }


  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L
  //===========================================================================================================================================
  //===========================================================================================================================================

	echo "<form name='intercambiopacientes' id='intercambiopacientes' action=''>";
	echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

	encabezado("INTERCAMBIO DE HABITACIONES", $wactualiz, "clinica");

    echo "<br><br>";
    echo "<table style='width: auto;' border='0' align='center' cellpadding='0' cellspacing='0'>
		  <tbody>
			<tr>
			  <td align='center'>
			  <table style='text-align: center; width: 500px; ' border='0'>
				<tbody>
				  <tr>
					<td style='width: auto;'></td>
					<td style='text-align: center;' colspan='2' rowspan='1' class=encabezadoTabla>Habitación A</td>
					<td style='width: auto;'></td>
					<td style='text-align: center;' colspan='2' rowspan='1' class=encabezadoTabla>Habitación B</td>
					<td></td>
				  </tr>
				  <tr>
					<td style='width: auto;'></td>
					<td style='text-align: center;' colspan='2' rowspan='1'><input name='whaba' id='whaba' style='text-align:center' onKeyUp='conMayusculas(this)'>";
			echo "
					<td></td>
					<td colspan='2' rowspan='1' style='text-align: center;'><input name='whabb' id='whabb' style='text-align:center' onKeyUp='conMayusculas(this)'>";

			echo "<td style='width: auto;'></td>
				  </tr>
            <tr>
            <th><br></th>

            </tr>
				  <tr>
					<td style='width: auto;'></td>
					<td style='text-align: center;' colspan='5' rowspan='1'>
					<input type='button' id='boton_interc' onclick='iniciar_intercambio(\"$wemp_pmla\", \"$wbasedatos\" , \"$wcenmez\" , \"$wuser\")' value='Iniciar Intercambio' class='botona'><input type='button' id='boton_cancelar' onclick='iniciar_cancelacion(\"$wemp_pmla\", \"$wbasedatos\" , \"$wcenmez\" , \"$wuser\")' value='Iniciar Cancelación' class='botona'></td>
				  </tr>
				</tbody>
			  </table>
			  </td>
			</tr>
		  </tbody>
		</table>
        <br><br>
        <table style='width: auto;' border='0' align='center' cellpadding='0' cellspacing='0'>
        <tr><td align=center colspan=9><input type='button' id='cerrar1' value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>
        </table>
        <br>
        <div align=center style='display:none' id='informacion_pacientes'>
        <div align=center id='datos_pacientes'></div>
		<div id='msjEspere' style='display:none;'>
		<br>
		<img src='../../images/medical/ajax-loader5.gif'/>
		<br><br> Por favor espere un momento ... <br><br>
		</div>
        </div>
		</form>";
}
?>