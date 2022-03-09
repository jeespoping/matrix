<?php
include_once("conex.php");
include_once("root/comun.php");

$sFiltrarSede = consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
$sCodigoSede = ($sFiltrarSede == 'on') ? consultarsedeFiltro() : '';
echo "<input type='hidden' id='sede' name= 'sede' value='".$selectsede."'>";
?>
<input type="hidden" name="<?php echo $wemp_pmla;?>" value="<?php echo $wemp_pmla;?>" id="wemp_pmla">
<input type="hidden" name="<?php echo $wuso;?>" value="<?php echo $wuso;?>" id="wuso">
<?php if(isset($_GET['caso'])){$caso=$_GET['caso'];?>
<input type="hidden" name="caso" value="<?php echo $caso;?>" id="caso">
<?php }else{ ?>
	<input type="hidden" name="caso" value="pantallaincial" id="caso">
	<?php }?>
<?php
/*
* Filtro sede
*/
if(isset($_GET['selectsede'])  &&  !empty($_GET['selectsede']) ){
	
$sCodigoSede = $_GET['selectsede'];
$sedeDestino = $_GET['selectsede'];
}
// if(isset($_GET['caso'])  &&  !empty($_GET['caso']) ){
// 	$caso = $_GET['caso'];

// }






    /**
     * Lógica de los llamados AJAX del todo el programa
     */
    if(isset($accion))
    {
        


        // $wbasedato       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'root');
		$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
        $wbasedato = "root";

        $data = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'');
        $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

        switch($accion)
        {
            /*
             * Verifica si el paciente indicado en la solicitud mostrada ya tiene un proceso de traslado (Entrega)
             * En caso de ya tener un proceso de entrega, no se permitirá la anulación de la solicitud mostrada.
            */
            case 'verificar_entrega_paciente':

                $sql_verif = "SELECT ".$wmovhos."_000017.Fecha_data, ".$wmovhos."_000017.Hora_data, ubihis, ubiing, ubisac, ubisan, eyrtip"
                    . " FROM ".$wmovhos."_000018, ".$wmovhos."_000017"
                        . " WHERE ubihis = '" . $historia . "'"
                                    . " AND ubihis = Eyrhis"
                                    . " AND Ubiing = Eyring"
                                    . " AND Ubihac = Eyrhde"
                                    . " AND Ubihan = Eyrhor"
                                    . " AND Ubisac = Eyrsde"
                                    . " AND ubialp = 'off'"
                                    . " AND ubiald = 'off'"
                                    . " AND ubiptr = 'on'"
                                    . " -- AND eyrtip = 'Entrega'"
                                    . " AND Eyrest = 'on'"
                            . " ORDER BY ".$wmovhos."_000018.Fecha_data DESC, ".$wmovhos."_000018.Hora_data DESC, "
                                                . " ".$wmovhos."_000017.Fecha_data DESC, ".$wmovhos."_000017.Hora_data DESC;";

                $res = mysql_query($sql_verif, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql_verif . " - " . mysql_error());

                $data["resultado"] = 0;
                if($res)
                {
                    $num = mysql_num_rows($res);
                    $data["resultado"] = $num;
                }
                else
                {
                    $data["error"] = 1;
                    $data["Query_Err"] = "Error: " . mysql_errno() . " - en el query  " . $sql_verif . " - " . mysql_error();
                    $data["mensaje"] = "No se pudo verificar la entrega del paciente.";
                }
            break;

            default :
                $data['mensaje'] = $no_exec_sub;
                $data['error'] = 1;
            break;
        }
        echo json_encode($data);
        return;
    }
?>

<html>
<head>
  <title>SOLICITUD SERVICIO DE CAMILLERO</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script type="text/javascript">

    jQuery(document).ready(function($){

       $('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

		/* 
		* Filtro sede
		*/
		
		if(!localStorage.getItem('step')){
		//	localStorage.setItem('step',1);
		}
		$('input[value="Enviar Solicitud"]').click(function(e){
			//localStorage.setItem('step',1);
		});



		var selectorSede = document.getElementById("selectsede");

		if(selectorSede !== null)
		{
			selectorSede.addEventListener('change', () => {

				var queryString = window.location.search;
				var urlParams = new URLSearchParams(queryString);

				var anuncioParam = urlParams.get('selectsede');
				var anuncioParamCaso = urlParams.get('caso');

				if(anuncioParam != $('#selectsede').val() && (anuncioParamCaso != 'pantallasolicitud') ){
					// event.stopPropagation();
					location.href = "solicitud_camillero.php?wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val()+"&wuso="+$('#wuso').val()+"&caso=pantallainicial";
				}
				else {
					$('#wdestino').val('');
					$('#solicitud_camillero').attr('action',"solicitud_camillero.php?wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val()+"&wuso="+$('#wuso').val()+"&caso=pantallainicial");
					$('#solicitud_camillero').submit();

				}
			
			});
		}

    });  

	function cambioSede(sede,event)  {
		var wemp_pmla = $('#wemp_pmla').val();
		var wuso = $('#wuso').val();
		var caso = $('#caso').val();



		var queryString = window.location.search;
    var urlParams = new URLSearchParams(queryString);

	var anuncioParam = urlParams.get('selectSede');
	

			location.href = 'solicitud_camillero.php?wemp_pmla='+wemp_pmla+'&wuso='+wuso+'&selectsede='+sede+'&caso=pantallasinicial';
			// location.href = 'solicitud_camillero.php?wemp_pmla='+wemp_pmla+'&wuso='+wuso+'&selectsede='+sede+'&caso='+caso;
		
		/*switch (localStorage.getItem('step')) {
			case "1":
			location.href = 'solicitud_camillero.php?wemp_pmla='+wemp_pmla+'&wuso='+wuso+'&selectsede='+sede;
				
			break;

			case "2":
			event.stopPropagation();
				$.ajax({
					url: '#',
					method: 'GET',
					data: {
						wemp_pmla: wemp_pmla,
						wuso:wuso,
						selectSede: sede
					}
				});

				


			break;

			default:
			location.href = 'solicitud_camillero.php?wemp_pmla='+wemp_pmla+'&wuso='+wuso+'&selectsede=""';
				break;
		}*/


		// localStorage.setItem('step',1);
		//localStorage.removeItem('step');

		location.href = 'solicitud_camillero.php?wemp_pmla='+wemp_pmla+'&wuso='+wuso+'&selectsede='+sede+'&caso=pantallasinicial';

	}

	function cerrarVentana()
	{
       window.close()
    }

	function volver(wemp_pmla, wuso)
	{
	  location.href = 'solicitud_camillero.php?wemp_pmla='+wemp_pmla+'&wuso='+wuso+'&selectsede='+'&caso=pantallainicial';
	}

    function destinoautomatico()
	{
	//Permite que al seleccionar en el motivo SOLICITUD DE CAMA, se seleccione automaticamente ADMISIONES en el destino.

    if ($("#wmotivo").val() == $("#wmotivo_solicitud").val())
        {
          var unidaddestino = $("#wunidestsolcamas").val();
          $("#wdestino").val(unidaddestino);
          $('#wcamillero').attr('disabled','');

        }
        else
        {
            $("#wdestino").val('');
            $("#wcamillero").val('');
            $('#wcamillero').attr('disabled','disabled');
        }

	}

    function verificar_dato()
    {
        if ($('#wcamillero').val()=='' && $("#wmotivo").val() == $("#wmotivo_solicitud").val())
            {
            jAlert('DEBE SELECIONAR UN TIPO DE CAMA.', 'Alerta');
            }
            else
                {
                    $("form:solicitud_camillero").submit();
				//	localStorage.setItem('step',1);
                }
    }

    /**
     * Esta función realiza el llamado hacia PHP para verificar si la solicitud que se muestra se
     * puede anular, siempre y cuando no se haya realizado la entrega del paciente.
     * Recibe la historia del paciente para verificar con esta la consulta.
     */
    function verificarEntregaPaciente(historia)
    {
        $.post("solicitud_camillero.php",
            {
                consultaAjax            : '',
                wemp_pmla               : $('#wemp_pmla').val(),
                accion                  : 'verificar_entrega_paciente',
                historia                : historia
            }, function(data) {
                // --> data.error indica si hay un error  en el llamado de la funcion
                if(data.error == 1)
                {
                    jAlert("Error: " + data.mensaje, "Alerta");
                }
                else
                {
                    if(data.resultado > 0) {
                        $('#chkSol_' + historia).attr('checked',false);
                        $('[id$="' + historia + '"]').attr('disabled',true);
                        jAlert("El paciente ya se encuentra entregado.No se puede anular la solicitud.\nPrimero debe anular la entrega del paciente para poder anular la solicitud.\nPara anular la entrega debe ir a la opción MODIFICAR TRASLADOS - ANULACION ENTREGA PACIENTES ubicada en MOVIMIENTO HOSPITALARIO.", "Alerta");
                    }
                }
            }, 'json');
    }

</script>
<style> 
.title {
    display:none;
    font-size:12px;
    height:70px;
    width:160px;
    padding:25px;
    color:#fff;
}
</style>
<?php
  /***************************************************
	*	      SOLICITUD SERVICIO DE CAMILLERO        *
	*	           DESDE CUALQUIER UNIDAD            *
	*				CONEX, FREE => OK				 *
	**************************************************/

//==================================================================================================================================
//PROGRAMA                   : solicitud_camillero.php
//AUTOR                      : Juan Carlos Hernández M.
//FECHA CREACION             : Marzo 17 de 2005
//FECHA ULTIMA ACTUALIZACION :

//DESCRIPCION
//==================================================================================================================================
//Este un programa estilo demonio, porque se refresca automaticamente la pantalla con la instrucción <meta>, el objetivo de este es
//registrar todas las solicitudes de camillero que se hagan a la central de camilleros, para esto se debe digitar el motivo del
//servicio, una observación (opcional), la habitación (opcional) y el destino del servicio. Una vez registrado el servicio se despliega
//las solicitudes hechas y que no han sido atendidas, es decir, en las que el camillero no ha llegado, ademas muestra, cuando y que
//camillero se le asigno al servicio. Una vez sea atendido el servicio y este evento sea registrado en la central, la solitud sale }
//del listado de servicios solicitados por el piso o unidad.
//El refresh se ejecuta cada 30 segundos, y si pasadas dos horas, no ha sido atendido el servicio este sale de la lista de pendientes y
//queda como un servicio no atendido.
//En la lista de pendientes solo aparecen los servicios de dia actual.

// A C T U A L I Z A C I O N E S
// ==========================================================================================================================================
// Enero 21 del 2022 : Esteban Villa 
// Filtro por sede.
// ==========================================================================================================================================
// Abril 3 de 2017 : Arleyda Insignares Ceballos 
// Se retira de la consulta los registros anulados.
// ==========================================================================================================================================
// Marzo 27 de 2017 : Arleyda Insignares Ceballos
// Se habilitan en la consulta de Solicitudes pendientes, los registros anulados con un Tooltip que muestra los datos de anulación (Codigo y
// nombre del usuario Matrix, fecha y hora de anulación).
// ==========================================================================================================================================
// Octubre 20 de 2015: Eimer Castro
// Se crea la función verificarEntregaPaciente(historia) para verificar si a la solicitud de traslado de un paciente ya se le efectuó el
// proceso de entrega y por ende no permitir la anulación de la solicitud si así fue. la consulta se realiza en las tablas movhos_000017
// y movhos_000018. Tambieén se cambian los alert por jAlert.
// Octubre 09 de 2013
// Se cambia la consulta de la tabla cencam_000002, por la tabla cencam_000007 ya que esta contiene los tipos de cama en una sola tabla y asi se
// genera mejor control.
// ==========================================================================================================================================
// Marzo 16 de 2013: Jonatan Lopez
// Se agregan campos en la tabla cencam_000003 para registrar el usuario que anula, la fecha y la hora, funcion marcarAnular.
//==========================================================================================================================================
//Diciembre 12 de 2012 Jonatan
//Se deja como de solo lectura el dato de tipo cama asignado para motivos diferentes a solicitud de cama, cuando seleccione solicitud de cama
//se activa la opcion de tipo de cama y este sera obligatorio para este caso.
//=========================================================================================================================================\\
//Diciembre 10 de 2012 Jonatan
//Se valida que no se hagan 2 solicitudes de cama para el mismo paciente, y se agrega el desplegable de camillero que es obligatorio cuando el
//motivo es solicitud de cama.
//=========================================================================================================================================\\
//Noviembre 30 de 2012 : Jonatan Lopez                                                                                                                        \\
//=========================================================================================================================================\\
//Se agregan detalles del paciente como Edad, Genero, responsable en la columna, Habitacion, Cedula o Historia, ademas se agrega una nueva
//columna llamada habitacion asignada, en la cual aparecera la habitacion que se asgino desde la central de camilleros, para todos los pacientes
//que se les solicite cama deben seleccionar la habitacion, la cedula o la historia activa del paciente. Al seleccionar solicitud de cama en el
//motivo se maracara automaticamente en el destino Admisiones.
//=====================================================================================================================================
//=========================================================================================================================================\\
//Marzo 14 de 2012                                                                                                                         \\
//=========================================================================================================================================\\
//Se adiciona la validacion para traer la Central correspondiente al motivo de la solicitud, según el centro de costo y el tipo de central
//asociado al motivo de solicitud configurados en la tabla  000004 y 000009 de cencam.
//=====================================================================================================================================
// Marzo 7 de 2011
//=====================================================================================================================================
// Se agregan la funcion consultaraliasporaplicacion para no quemar cencam y ninguna otra empresa.
//=====================================================================================================================================
// Octubre 4 de 2011
//=====================================================================================================================================
// Se adiciona la columna de fecha de solicitud, porque ya se hacén solicitudes de camas que pueden durar mas de un día
//=====================================================================================================================================
// Agosto 13 de 2010
//=====================================================================================================================================
// Se adiciona el nombre del paciente cuando se coloca la habitacion en la solicitud, peticion de Jaime de logistica clinica.
//=====================================================================================================================================
// Marzo 19 de 2010
//=====================================================================================================================================
// Se hace modificacion para que se pueden realizar movimientos de centrales nuevas como Documentos Externos y Mensajeria Externa
// para que a las unidades internas a la clinica no les aprezcan los motivos de Internos, es decir, ya hay motivos de solicitud
// Internos y Externos, entonces, solo pueden ver los correspondientes a la opcion por la que ingreso el usuario, para esto se creo una
// variable de Inicio, la cual esta en la opcion de Matrix y se llama $wuso, la cual puede tener las siguientes opciones:
//  I: Solo son de uso Interno.
//  E: Son de uso externo.
//  IE: Son motivos que se utilizan Internamente pero que se originan Externamente.
//  A : Pueder ser usados Interna o Externamente.
// Para este cambio se modificaron las tablas 000001 y 000004, a ambas se les adiciono el campo Uso.
//=====================================================================================================================================
// Enero 25 de 2008
//=====================================================================================================================================
// Se modifica el programa para que no puedan hacer solicitudes de "traslado de muertos" con el motivo de "traslado a sala de transicion"
// para esto se utiliza el parametro de exigir la habitacion en el motivo de "traslado a sala de transicion" y con esto valido si el motivo
// es relacionado con muerte y si si se despliega un mensaje que informa que se debe registrar la muerte en el software de altas y este
// a su vez hace el pedido automatico del camillero.
//
//=====================================================================================================================================
// Diciembre 19 de 2007
//=====================================================================================================================================
// Se modifica el programa para que no puedan hacer solicitudes de "paciente de alta" con el motivo de "traslado interno de pacientes"
// para esto se utiliza el parametro de exigir la habitacion en el motivo de "traslado" y "paciente de alta" y con esto valido si la
// historia esta en proceso y si lo esta no deja pedir la solicitud porque el paciente esta de alta.
//
//=====================================================================================================================================
// Diciembre 5 de 2007
//=====================================================================================================================================
// Se hace la modificacion de validar los de habitacion y observacion si el maestro de motivos lo tiene para metrizado, es decir si en
// el maestro de motivos se exije la habitacion o la observacion se valida que sea digitada.
//=====================================================================================================================================
//
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

    include_once("root/magenta.php");
    include_once("root/comun.php");

    $conex = obtenerConexionBD("matrix");

    

  	// =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
    $wactualiz="2022-01-21";                                 // Aca se coloca la ultima fecha de actualizacion de este programa //
	// =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //


  	$key = substr($user,2,strlen($user));

	if (strpos($user,"-") > 0)
          $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

    $wfecha=date("Y-m-d");
	$hora = (string)date("H:i:s");

	$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
    $wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');
	$wafinidad = consultarAliasPorAplicacion($conex, $wemp_pmla, 'afinidad');
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');


  function registrarAsignacion($wid)
    {

    global $conex;
    global $wcencam;
    global $wfecha;
    global $wusuario;   

    $whora = date("H:i:s");

    $q =     "  SELECT Acaids "
            ."    FROM ".$wcencam."_000010"
            ."   WHERE Acaids   = '".$wid."'"
            ."     AND Acaest   = 'on'";
    $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
    $num = mysql_num_rows($res);

    //Si el id ya tiene registro en on en la tabla 10 de cencam, no hara registro de datos.
    if ($num == 0)
        {
        $q =  " INSERT INTO ".$wcencam."_000010(   Medico       ,   Fecha_data,   Hora_data,   Acaids,   Acaest,   Acarea, Seguridad     ) "
                                . "    VALUES('".$wcencam."','".$wfecha."','".$whora."','".$wid."'        ,'on'   ,'off' , 'C-" . $wusuario . "')";
        $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
        }

    }

    // Funcion que permite extraer la edad del paciente en años, meses y dias.
  function calcularAnioMesesDiasTranscurridos($fecha_inicio, $fecha_fin = '')
    {
        $datos = array('anios'=>0,'meses'=>0,'dias'=>0);

        if($fecha_inicio != '' && $fecha_inicio != '0000-00-00')
        {
            $fecha_de_nacimiento = $fecha_inicio;

            $fecha_actual = date ("Y-m-d");
            if($fecha_fin != '' && $fecha_fin != '0000-00-00')
            {
                $fecha_actual = $fecha_fin;
            }
            // echo "<br>Fecha final: $fecha_actual";
            // echo "<br>Fecha inicio: $fecha_de_nacimiento";

            // separamos en partes las fechas
            $array_nacimiento = explode ( "-", $fecha_de_nacimiento );
            $array_actual = explode ( "-", $fecha_actual );

            $anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos años
            $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses
            $dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos días

            //ajuste de posible negativo en $días
            if ($dias < 0)
            {
                --$meses;

                //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual
                switch ($array_actual[1]) {
                    case 1:     $dias_mes_anterior=31; break;
                    case 2:     $dias_mes_anterior=31; break;
                    case 3:
                            if (checkdate(2,29,$array_actual[0]))
                            {
                                $dias_mes_anterior=29; break;
                            } else {
                                $dias_mes_anterior=28; break;
                            }
                    case 4:     $dias_mes_anterior=31; break;
                    case 5:     $dias_mes_anterior=30; break;
                    case 6:     $dias_mes_anterior=31; break;
                    case 7:     $dias_mes_anterior=30; break;
                    case 8:     $dias_mes_anterior=31; break;
                    case 9:     $dias_mes_anterior=31; break;
                    case 10:     $dias_mes_anterior=30; break;
                    case 11:     $dias_mes_anterior=31; break;
                    case 12:     $dias_mes_anterior=30; break;
                }
                $dias=$dias + $dias_mes_anterior;
            }

            //ajuste de posible negativo en $meses
            if ($meses < 0)
            {
                --$anos;
                $meses=$meses + 12;
            }
            //echo "<br>Tu edad es: $anos años con $meses meses y $dias días";
            $datos['anios'] = $anos;

        }

        return $datos;
    }


   function traerresponsable($whis, $tipo_consulta)
    {

         global $conex;
         global $wmovhos;
         global $wemp_pmla;

         switch ($tipo_consulta) {
              case 'habitacion':

                                $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pactid, pacced, ingnre"
                                    ."   FROM ".$wmovhos."_000020, root_000036, root_000037, ".$wmovhos."_000018, ".$wmovhos."_000016"
                                    ."  WHERE Habcod = '".$whis."'"
                                    ."    AND habhis  = Inghis"
                                    ."    AND habing  = Inging"
                                    ."    AND habhis  = orihis "
                                    ."    AND habing  = oriing "
                                    ."    AND oriori  = '".$wemp_pmla."'" // Empresa Origen de la historia,
                                    ."    AND oriced  = pacced "
                                    ."    AND oritid  = pactid "
                                    ."    AND habhis  = ubihis "
                                    ."  GROUP BY 1, 2, 3, 4, 5, 6, 7, 8 "
                                    ."  ORDER BY Habord, Habcod ";
                                $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                                $row = mysql_fetch_array($res);


                 break;

              case 'historia':

                                $q = " SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, ingnre"
                                    ."   FROM root_000036, root_000037, ".$wmovhos."_000018, ".$wmovhos."_000016"
                                    ."  WHERE Inghis = '".$whis."'"
                                    ."    AND ubihis  = Inghis"
                                    ."    AND ubiing  = Inging"
                                    ."    AND ubihis  = orihis "
                                    ."    AND ubiing  = oriing "
                                    ."    AND oriori  = '".$wemp_pmla."'" // Empresa Origen de la historia,
                                    ."    AND oriced  = pacced "
                                    ."    AND oritid  = pactid "
                                    ."  GROUP BY 1, 2, 3, 4, 5, 6, 7 "
                                    ."  ORDER BY Inghis, Inging ";
                                $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                                $row = mysql_fetch_array($res);

                 break;

              case 'cedula':

                                $q = " SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, ingnre"
                                    ."   FROM root_000036, root_000037, ".$wmovhos."_000016"
                                    ."  WHERE Oriced = '".$whis."'"
                                    ."    AND oriori  = '".$wemp_pmla."'" // Empresa Origen de la historia,
                                    ."    AND oriced  = pacced "
                                    ."    AND oritid  = pactid "
                                    ."    AND inghis  = orihis "
                                    ."    AND inging  = oriing "
                                    ."  GROUP BY 1, 2, 3, 4, 5, 6, 7 ";
                                $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                                $row = mysql_fetch_array($res);


                 break;
             default:
                 break;
         }


        $wresponsable = $row['ingnre'];

        return $wresponsable;
    }

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// ACA SE COLOCA EL HORARIO DE ATENCION EN CENTRAL DE CAMILLEROS POR MATRIX
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$whora_atencion="LAS 24 HORAS DE LUNES A DOMINGO";
	$WMENSAJE = "*** POR FAVOR HAGA SU SOLICITUD CON LA MAYOR CLARIDAD POSIBLE ***";
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo "<form id='solicitud_camillero' action='solicitud_camillero.php?wemp_pmla=".$wemp_pmla."&wuso=".$wuso."&selectsede=".$sCodigoSede."&caso=pantallasolicitud' method=post>";

    $wunidestsolcamas1 = consultarAliasPorAplicacion($conex, $wemp_pmla, 'UnidadDestinoSolCamas');
    $wunidestsolcamas_dato = explode("-", $wunidestsolcamas1);
    $wmotivodb = $wunidestsolcamas_dato[0];
    $wdestinodb = $wunidestsolcamas_dato[1];

    //TRAIGO LOS MOTIVOS DE LLAMADO
    $q = "  SELECT Descripcion "
        ."    FROM ".$wcencam."_000001"
        ."   WHERE Estado = 'on' "
        ."     AND (Uso   = '".$wuso."'"
        ."      OR  Uso   = 'A') "       //Indica que el uso puede ser (I)nterno o (E)xterno
        ."     AND id = ".$wmotivodb."";
    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
    $row = mysql_fetch_array($res);
    $wmotivo_solicitud = $row['Descripcion'];

    //Traigo el nombre del destino que se marcara automaticamente.


	if(!empty($sCodigoSede)){
		$q =     " SELECT c.nombre "
		."   FROM ".$wcencam."_000004 c"
		."  WHERE c.Estado = 'on' "
		."    AND (c.Uso   = '".$wuso."'"
		."     OR  c.Uso   = 'A') "      //A: Indica que puede ser Interno o Externo
		."    AND c.id = ".$wdestinodb.""
		." OR c.codigoSede = '".$sCodigoSede."'";
	}
	else {

		$q =     " SELECT nombre "
		."   FROM ".$wcencam."_000004"
		."  WHERE Estado = 'on' "
		."    AND (Uso   = '".$wuso."'"
		."     OR  Uso   = 'A') "      //A: Indica que puede ser Interno o Externo
		."    AND id = ".$wdestinodb."";
	}
    
	$res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($res);
    $wunidestsolcamas = $row['nombre'];

	echo "<input type='HIDDEN' id='wmotivo_solicitud' name='wmotivo_solicitud' value='".$wmotivo_solicitud."'>";
    echo "<input type='HIDDEN' id='wunidestsolcamas' value='".$wunidestsolcamas."'>";
	echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."' id='wemp_pmla'>";
	echo "<input type='HIDDEN' name='wuso' value='".$wuso."'>";
	
	echo '<input type="hidden" name="caso" value="pantallasolicitud" id="caso">';

	//Entro aca si no se ha seleccionado el servicio origen
    if (!isset($wser) or trim($wser) == "" or trim($wser) == " " or strlen($wser) == 1 )
       {


        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //ACA TRAIGO LOS DESTINOS DIGITADOS EN LA TABLA DE MATRIX
		// Si codigo sede existe filtra destinos
		if(!empty($sCodigoSede)){
			$q =  " SELECT c.nombre, c.cco "
			."   FROM ".$wcencam."_000004 c" 
			."  WHERE c.Estado = 'on' "
			."		AND c.codigoSede = '".$sCodigoSede."'"
			."    AND (c.Uso   = '".$wuso."'"
			."     OR  c.Uso   = 'A') "
			."  ORDER BY 1 "; 

		}
		else {

			$q =  " SELECT nombre, cco "
				 ."   FROM ".$wcencam."_000004"
				 ."  WHERE Estado = 'on' "
				 ."    AND (Uso   = '".$wuso."'"
				 ."     OR  Uso   = 'A') "      //A: Indica que puede ser Interno o Externo
				 ."  ORDER BY 1 ";
		}
		$res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
	    $num = mysql_num_rows($res);

	    echo "<br>";
	    echo "<br>";
	    echo "<br>";

	    encabezado("SOLICITUD SERVICIO DE CAMILLERO y/o SERVICIOS ",$wactualiz, "clinica",TRUE);

	    echo "<center><table border=1>";

		echo "<tr class=encabezadoTabla><td align=center>SELECCIONE EL SERVICIO EN EL QUE SE ENCUENTRA: </td></tr>";
	    echo "<tr><td align=center><select name='wser'>";

	    echo "<option>&nbsp</option>";
		for ($i=1;$i<=$num;$i++)
		    {
			 $row = mysql_fetch_array($res);
	         echo "<option>".$row[0]."</option>";
            }
		echo "</select></td></tr>";

		echo "<center><tr><td align=center colspan=6 bgcolor=#cccccc></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
		echo "</table>";
       }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      else
         {
	        //Ya esta seleccionado el servicio origen
	        echo "<input type='HIDDEN' NAME= 'wser' value='".$wser."'>";

	        //Entro aca si no se ha seleccionado el motivo o el destino de la solicitud
            if (!isset($wmotivo) or !isset($wdestino) or (strpos($wdestino,"-") == 0))
		       {

			    encabezado("SOLICITUD SERVICIO DE CAMILLERO y/o SERVICIOS ",$wactualiz, "clinica",TRUE);

			    echo "<center><table border=1>";
		        echo "<tr class=fila1><td align=center colspan=7><font size=4><b>SERVICIO EN EL QUE SE ENCUENTRA: </FONT><U><I><FONT SIZE=4> ".$wser." </b></font></I></U></td></tr>";
		        echo "<tr class=fila2><td align=center colspan=5><font size=2><b>HORARIO DE ATENCION POR MATRIX: ".$whora_atencion."</b></font></td></tr>";
		        echo "<tr class=fila1><td align=center colspan=5><font size=2><b>".$WMENSAJE."</b></font></td></tr>";

		        echo "<tr class=encabezadoTabla>";
		        echo "<th><font size=3><b>Motivo</b></font></th>";
		        echo "<th><font size=3><b>Observaciones</b></font></th>";
		        echo "<th><font size=3><b>Habitación<br>Cédula o<br>Historia</b></font></th>";
                echo "<th><font size=3><b>Tipo de cama<br>solicitada</b></font></th>";
		        echo "<th><font size=3><b>Destino</b></font></th>";
		        echo "</tr>";

		        //TRAIGO LOS MOTIVOS DE LLAMADO
				$q = "  SELECT Descripcion "
				    ."    FROM ".$wcencam."_000001"
				    ."   WHERE Estado = 'on' "
				    ."     AND (Uso   = '".$wuso."'"
				    ."      OR  Uso   = 'A') "       //Indica que el uso puede ser (I)nterno o (E)xterno
				    ."   ORDER BY Descripcion ";
				$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());

				echo "<tr class=fila2>";
				echo "<td>";
				echo "<select name='wmotivo' id='wmotivo' onchange='destinoautomatico();'>";
                if (isset($wmotivo))
                {
				    echo "<option>".$wmotivo."</option>";
                }
                else
                {
                    echo "<option>&nbsp</option>";
                }
				for($i=0;$i<$num;$i++)
    			{
    				$row = mysql_fetch_array($res);
    			    echo "<option>".$row[0]."</option>";
    			}
				echo "</select></td>";
		        //echo "<td bgcolor=#99FFCC><b>Observación :</b><INPUT TYPE='text' NAME='wobservacion' SIZE=80></td>";
		        echo "<td><TEXTAREA NAME='wobservacion' ROWS='3' COLS='20'></TEXTAREA></td>";

		        ////echo "<td bgcolor=#cccccc><b>Habitacion :</b><INPUT TYPE='text' NAME='whab' SIZE=5></td>";

		        //============================================================================
		        //TRAIGO LAS HABITACIONES ACTIVAS DEL SERVICIO DE DONDE SE HACE LA SOLICITUD
		        //============================================================================
		        $q =  " SELECT habcod, habhis "
					 ."   FROM ".$wmovhos."_000020, ".$wcencam."_000004 "
					 ."  WHERE habcco = mid(cco,1,instr(cco,'-')-1) "
					 ."    AND nombre = '".$wser."'"
					 ."    AND habest = 'on' "
					 ."    AND habdis = 'off' "
					 ."    AND habali = 'off' "
					 ."    AND habcub != 'on' "
					 ."  ORDER BY 1 ";
			    $res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
			    $num = mysql_num_rows($res);
			    if ($num > 0)
			       {
				    echo "<td>";
				    echo "<select name='whab' id='whab'>";
					echo "<option></option>";
				    for ($i=1;$i<=$num;$i++)
				        {
					     $row = mysql_fetch_array($res);
					     echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."</option>";
				        }
					echo "</select></td>";
			       }
			      else
				    {
    					$checked_his="";
    					$checked_hab = "";
    					$checked_doc = "";
    					if( $tipobusqueda == "historia"){
    						$checked_his=" checked ";
    					}else if( $tipobusqueda == "habitacion"){
    						$checked_hab = " checked ";
    					}else if( $tipobusqueda == "dcoidentidad"){
    						$checked_doc = " checked ";
    					}else{
    						$checked_his=" checked ";
    					}
    			         echo "<td>";
    					 echo "<input type='radio' name='tipobusqueda' value='historia' $checked_his><b>Historia</b><br>";
    					 echo "<input type='radio' name='tipobusqueda' value='habitacion' $checked_hab><b>Habitacion</b><br>";
    					 echo "<input type='radio' name='tipobusqueda' value='dcoidentidad' $checked_doc><b>Dcto Ident</b><br>";
    					 echo "<INPUT TYPE='text' NAME='whab' id='whab' SIZE=8></td>";
					}
				//============================================================================

                  $wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');
                //Camllero - Ubicación
				 // $q = "  SELECT Codigo, Nombre, 2 AS Tip "
                    // ."    FROM ".$wcencam."_000002 "
                    // ."   WHERE Unidad != 'INACTIVO' "
                    // ."     AND central = '".$wcentral_camas."'"
                    // ."   ORDER BY Tip, Nombre " ;

			   //Se cambia la tabla 2 por la tabla 7 de cencam ya que esta contiene los tipos de cama detallados y separados de los otros tipos.//09 oct 2013 Jonatan
				$q = "  SELECT tipcod, tipdes, 2 AS Tip "
                    ."    FROM ".$wcencam."_000007 "
                    ."   WHERE tipcen = '".$wcentral_camas."'"
                    ."   ORDER BY Tipcod, Tipdes " ;
                $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
                $numcam = mysql_num_rows($rescam) or die (mysql_errno()." - ".mysql_error());

                $whabilitado = 'disabled'; //Por defecto se encuentra inhabilitado el seleccionador de cama.
                //Si el motivo es solicitud de cama entonces activara el seleccionador de cama.
                if ($wmotivo == $wmotivo_solicitud )
                {
                    $whabilitado = 'enabled';
                }

                echo "<td align='center' bgcolor=".$wcolor.">";
                echo "<SELECT id='wcamillero' name='wcamillero' style='width:12em' ".$whabilitado.">";
                if (trim($row[6]) == "")      //Si no viene ningún dato desde el registro de la tabla, muestro un nulo
                if (isset($wcamillero))
                    {
                        echo "<option value='".$wcamillero."'>".$wcamillero."</option>";
                    }
                else
                    {
                        echo "<option></option>";
                    }
                for($j=0;$j<$numcam;$j++)
                {
                    $rowcam = mysql_fetch_array($rescam);
                    echo "<option value='".$rowcam[0]." - ".$rowcam[1]."'>".$rowcam[0]." - ".$rowcam[1]."</option>";
                }
                echo "<option></option>";
                echo "</SELECT></td>";

				//============================================================================
				//ACA TRAIGO LOS DESTINOS DIGITADOS EN LA TABLA DE MATRIX
				//============================================================================
				// Si codigo sede existe filtra destinos
				if(!empty($sCodigoSede)){
						
					$q =  " SELECT c.nombre "
					."   FROM ".$wcencam."_000004 c"
					."  WHERE c.Estado = 'on' "
					."    AND (c.Uso   = '".$wuso."'"
					."     OR  c.Uso   = 'A') "
					."	AND c.codigoSede = '".$sCodigoSede."'"
					."  ORDER BY 1 ";

				}
				else {

					
					$q =  " SELECT nombre "
					."   FROM ".$wcencam."_000004 "
					."  WHERE Estado = 'on' "
					."    AND (Uso   = '".$wuso."'"
					."     OR  Uso   = 'A') "
					."  ORDER BY 1 ";
				}
				$res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
			    $num = mysql_num_rows($res);

				echo "<td>";
			    echo "<select name='wdestino' id='wdestino'>";

                if (isset($wdestino))
                    {
                        echo "<option value='".$wdestino."'>".$wdestino."</option>";
                    }
                else
                    {
                        echo "<option>&nbsp</option>";
                    }

				for ($i=1;$i<=$num;$i++)
				    {
    					$row = mysql_fetch_array($res);
    			        echo "<option value='".$row[0]."'>".$row[0]."</option>";
		            }
				echo "</select></td></tr>";
				//============================================================================

				echo "<center><tr class=fila1><td align=center colspan=6><b> !!ATENCION!! LAS SOLICITUDES PARA 'PACIENTE DE ALTA' EN UNIDADES HOSPITALARIAS SOLO SE PODRAN HACER DESDE EL SISTEMA DE ALTAS</b></td></tr></center>";

				echo "<center><tr><td align=center colspan=6 bgcolor=#cccccc></b><input type='button' value='Enviar Solicitud' onclick='verificar_dato();'><input type='button' value='Retornar' onclick='volver(\"$wemp_pmla\", \"$wuso\")'></b></td></tr></center>";
				echo "</table>";

				echo "<br>";
				echo "<HR align=center></hr>";

				/////////////////////////////////////////////////
				$q = "  SELECT Motivo, Observacion, Destino, Solicito, Camillero, A.Hora_data, Hora_respuesta, Habitacion, A.Id, Observ_central, A.Fecha_data, A.Hab_asignada, Historia, Anulada,Usu_anula,Fecha_anula,Hora_anula,Descripcion as Nomusu"
				    ."  FROM ".$wcencam."_000003 A "
                    ."        Inner join ".$wcencam."_000006 B on Central = codcen "
                    ."        Left join usuarios C on Usu_anula = codigo "
				    ."  WHERE Hora_Cumplimiento = '00:00:00' " 
                    ."        AND Anulada       = 'No' "
                    ."        AND Origen        = '".$wser."' "
				    ."        AND Hora_llegada  = '00:00:00' "
					."        AND TIMESTAMPDIFF(HOUR,CONCAT(A.Fecha_data,' ',A.Hora_data),CONCAT('".$wfecha."',' ','".$hora."')) <= cenvig ";

                // Se retira condicion para incluir anulacion
                // ."  AND Anulada in ('No','Si') "  
                
				$res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
			    $num = mysql_num_rows($res);

			    echo "<br>";
			    echo "<center><table border=0>";
			    echo "<tr class=fila1><td align=center colspan=11><font size=2><b>SOLICITUDES PENDIENTES REGISTRADAS EN LAS ÚLTIMAS 24 HORAS</b></font></td></tr>";
		        echo "<tr class=fila2><td align=left colspan=11><font size=2><b>Hora: ".$hora."</b></font></td></tr>";

			    echo "<tr class=encabezadoTabla>";
			    echo "<th><font size=2>MOTIVO</font></th>";
			    echo "<th><font size=2>HABITACION,<br>CEDULA O<br>HISTORIA</font></th>";
			    echo "<th><font size=2>OBSERVACION</font></th>";
		        echo "<th><font size=2>DESTINO</font></th>";
		        echo "<th><font size=2>SOLICITADO POR</font></th>";
		        echo "<th><font size=2>CAMILLERO/<BR>TIPO HABITACION</font></th>";
                echo "<th><font size=2>HABIT. ASIGNADA</font></th>";
				echo "<th><font size=2>FECHA SOLICITUD</font></th>";
		        echo "<th><font size=2>HORA SOLICITUD</font></th>";
		        echo "<th><font size=2>RESPUESTA CENTRAL</font></th>";
		        echo "<th><font size=2>OBSERVACION DE LA CENTRAL</font></th>";
		        echo "<th><font size=2>ANULAR</font></th>";
		        echo "</tr>";

			    for ($i=1;$i<=$num;$i++)
			        {
/*
                     $vartitle   = '';
                     $habilitado = ''; 
                     $checkedanu = ''; */

				     if  (is_integer($i/2))
				         $wcolor = "99ccff";
				     else
				         $wcolor = "dddddd";

				     $row = mysql_fetch_array($res);
                     
                     // 2017-04-03 Arleyda Insignares C. -Se retira consulta de registros anulados
                     // Verificar anulación para determinar el color
                     /*if ( strtoupper($row['Anulada'])=='SI' ){
                          $wcolor = "fcf3cf"; 
                          $vartitle   = "Anulado por ".$row['Usu_anula']."-".$row['Nomusu']."<br>Fecha ".$row['Fecha_anula']."</br>Hora ".$row['Hora_anula'];
                          $habilitado = 'disabled'; 
                          $checkedanu = ' checked ';
                     }*/

                     echo "<tr >";
				     echo "<td bgcolor=".$wcolor."><font size=1>".$row[0]."</font></td>";                   // Motivo
				     echo "<td bgcolor=".$wcolor."><font size=1>".$row[7]."</font></td>";                   // Habitacion
				     echo "<td bgcolor=".$wcolor."><font size=1>".$row[1]."</font></td>";                   // Observacion
			         echo "<td bgcolor=".$wcolor."><font size=1>".$row[2]."</font></td>";                   // Destino
			         //==================================================================
			         $q = "  SELECT Descripcion "
			             ."    FROM usuarios "
			             ."   WHERE Codigo = '".$row[3]."'"
			             ."     AND Activo = 'A' ";

				     $res2 = mysql_query($q) or die(mysql_errno().":".mysql_error());

				     $row1 = mysql_fetch_array($res2);

				     if ($row1[0] == "")
				        $wnomusu = $row[3];                                                                 // Sin Nombre de Solicitado por
				     else
				        $wnomusu = $row1[0];                                                                // Solicitado por

				     //==================================================================
				     echo "<td bgcolor=".$wcolor."><font size=1>".$wnomusu."</font></td>";
				     if ($row[4] == "")
				        echo "<td bgcolor=".$wcolor."><font size=1>&nbsp</font></td>";                      // Sin Camillero
				     else
			            echo "<td bgcolor=".$wcolor."><font size=1>".$row[4]."</font></td>";               // Camillero

                     if ($row[4] == "")
				        echo "<td bgcolor=".$wcolor."><font size=1>&nbsp</font></td>";                      // Sin habitacion
				     else
			            echo "<td bgcolor=".$wcolor."><font size=1>".$row[11]."</font></td>";               // Con habitacion


					 echo "<td bgcolor=".$wcolor." align=center><font size=1>".$row[10]."</font></td>";     // Fecha de solicitud
			         echo "<td bgcolor=".$wcolor." align=center><font size=1>".$row[5]."</font></td>";      // Hora de solicitud
			         if ($row[6] == "")
			            echo "<td bgcolor=".$wcolor."><font size=1>&nbsp</font></td>";                      // Sin Hora respuesta de la Central
			         else
			               echo "<td bgcolor=".$wcolor."><font size=1>".$row[6]."</font></td>";             // Hora respuesta de la Central
			         if ($row[9] == "")
					    echo "<td bgcolor=".$wcolor."><font size=1>&nbsp</font></td>";                      // Sin Observacion de la Central
					 else
					      if (isset($wobscc[$i]))
					         echo "<td align=left bgcolor=".$wcolor.">".$wobscc[$i]."</td>";                // Observacion de la central camilleros
					      else
			                   echo "<td align=left bgcolor=".$wcolor.">".$row[9]."</td>";                  // Observacion de la central camilleros
			         //////////
                     // Se hace un llamado a la función verificarEntregaPaciente(historia) al momento de dar clic sobre el checkbox de este registro.
			         echo "<td align=center bgcolor=".$wcolor."><font size=1><INPUT TYPE=CHECKBOX NAME='wanulada[".$i."]' ".$habilitado." ".$checkedanu." onclick='verificarEntregaPaciente(".$row[12].")' id='chkSol_".$row[12]."'></font></td>";
			         
                     if (isset($wanulada[$i]))
			            {
    			            $q = "   UPDATE ".$wcencam."_000003 "
    		                     ."      SET Anulada = 'Si', Usu_anula='".$wusuario."', Fecha_anula = '".$wfecha."', Hora_anula = '".$hora."'"
    		                     ."    WHERE Id = ".$row[8];
    	                    $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

                            //Se cancela la solicitud en la tabla 10 de cencam.
                            $q_aca = "   UPDATE ".$wcencam."_000010 "
                                    ."      SET Acaest = 'off'"
                                    ."    WHERE Acaids = ".$row[8];
                            $resaca = mysql_query($q_aca,$conex) or die (mysql_errno()." - ".mysql_error());

                            //Consulta la cama asociada al id que se actualizara para cambiarle el estado habpro a off.
                            $q = "  SELECT Hab_asignada "
                                ."    FROM ".$wcencam."_000003"
                                ."   WHERE Id  = ".$row[8];
                            $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
                            $row = mysql_fetch_array($res);
                            $whabitacion = $row['Hab_asignada'];

                            //La habitacion se pone en proceso de ocupacion igua a off ya que el usuario
                            //selecciono habitacion vacio, pero el paciente ya tiene historia asignada.
                            $q =  " UPDATE ".$wmovhos."_000020 "
                                . "    SET Habpro = 'off'"
                                . "  WHERE Habcod = '".$whabitacion."'";
                            $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

    	                     //echo "<td align=center bgcolor=".$wcolor."><font size=1><INPUT TYPE=CHECKBOX NAME='wanulada[".$i."]' CHECKED></font></td>";
    	                     echo "<meta http-equiv='refresh' content='0;url=solicitud_camillero.php?wser=".$wser."&wemp_pmla=".$wemp_pmla."&wuso=".$wuso."&selectsede=".$sCodigoSede."&caso=".$caso."'>";
	                    }
			         //////////
			         echo "<td align=center bgcolor=#cccccc></b><input type='submit' value='OK' id='btnOK_".$row[12]."' ".$habilitado."></b></td>";
			         echo "</tr>";
		            }
		        echo "</center></table>";

		        echo "<br>";
				echo "<HR align=center></hr>";
				echo "<center><table border=0>";
				echo "<tr class=fila2>";
				echo "<td><font size=1><b>PARA ANULAR UNA SOLICITUD, SELECCIONE EL CAJON CORRESPONDIENTE Y LUEGO DE CLICK SOBRE EL BOTON [OK] </b></font></td>";
				echo "</tr>";
				echo "</center></table>";
		        /////////////////////////////////////////////////

			   }

			   /*********************************
			   * TODOS LOS PARAMETROS ESTAN SET *
			   *********************************/

			   //Refresh es el tiempo en que se demora en refrescar la pantalla
			   $wrefresh = "'420";  //SIETE MINUTOS
               //=====================================================================================================================================
			   //=====================================================================================================================================
			   // En este procedimiento evaluo si el motivo tiene o pide campos obligatorios dependiendo de si el centro de costo es hospitalario o no
			   //=====================================================================================================================================
				//
					if( ($tipobusqueda == 'habitacion' && isset($tipobusqueda)) OR (!isset($tipobusqueda)) )
					{
					   if ($whab != "")     //Habitacion
					   {
							$wdatohab = explode("-",$whab);
							$whab = $wdatohab[0];
							$whis = $wdatohab[1];

							$q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex,orihis"
								."   FROM root_000036, root_000037, ".$wmovhos."_000020 "
								."  WHERE habcod = '".$whab."'"           //Como Habitacion
								."    AND habhis = orihis "
								."    AND habing = oriing "
								."    AND oriori = '".$wemp_pmla."'"
								."    AND oriced = pacced "
								."    AND oritid = pactid ";
							$reshab = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
							$rowhab = mysql_fetch_array($reshab);

							$whis = $rowhab['orihis'];

						}
					}

					if( $tipobusqueda == 'historia' )
					{
					 if ($whab != "")     //Habitacion
					   {
							 //Busco si lo digitado es la historia y con ese dato traigo el nombre del paciente
							 //si no busco si es la cedula y con el dato busco por cedula
							 $q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex "
								."   FROM root_000036, root_000037 "
								."  WHERE orihis = '".$whab."'"       //Como Historia
								."    AND oriori = '".$wemp_pmla."'"
								."    AND oriced = pacced "
								."    AND oritid = pactid ";
							$reshab = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
							$rowhab = mysql_fetch_array($reshab);

							$whis = $whab; // En este caso se guardara la historia para el paciente en al tabla 3 de cencam.


						}
					}

					if( $tipobusqueda == 'dcoidentidad' )
					{
						if ($whab != "")     //Habitacion
						{

							 //Busco si lo digitado es la historia y con ese dato traigo el nombre del paciente
							 //si no busco si es la cedula y con el dato busco por cedula
							 $q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex,Orihis "
								."   FROM root_000036, root_000037 "
								."  WHERE oriced = '".$whab."'"    //Como Cedula o Documento
								."    AND oriori = '".$wemp_pmla."'"
								."    AND oriced = pacced "
								."    AND oritid = pactid ";

							$reshab = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
							$rowhab = mysql_fetch_array($reshab);

							$whis = $rowhab['Orihis'];

						}
					}

					//
			   //Aca evaluo si el servicio es hospitalario
			   $q = " SELECT ccohos, ccourg "                                                              // Diciembre 5 de 2007
			       ."   FROM ".$wcencam."_000004, ".$wmovhos."_000011 "
			       ."  WHERE ccocod = mid(cco,1,instr(cco,'-')-1) "
			       ."    AND nombre = '".$wser."'"
			       ."    AND Estado = 'on' ";
			   $res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
               $res_urg = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
			   $row = mysql_fetch_array($res);
               $row_urgen = mysql_fetch_array($res_urg);
			   $wval="S";



			   if ($row[0] == 'on' and isset($wmotivo))                                            // Diciembre 5 de 2007
			      {

				   $q = " SELECT dato_obligatorio, texto_pantalla "
				       ."   FROM ".$wcencam."_000001 "
				       ."  WHERE Descripcion = '".$wmotivo."'";
				   $res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
				   $row = mysql_fetch_array($res);

				   if ($row[0]=="Habitacion" or $row[0]=="Observaciones" or $row[0]=="Historia")     // Diciembre 5 de 2007
                      {
				      if (($row[0] =="Habitacion" && !isset($tipobusqueda)) || ($row[0] =="Habitacion" && isset($tipobusqueda) && $tipobusqueda=='habitacion')   )                                                                         // Diciembre 5 de 2007
				         {
					      if ((trim($whab)!="" and trim($whab)!=" " and strlen(trim($whab)) > 2) or $row[1]=="Muerte")    // Diciembre 19 de 2007
				            {
							 $q = " SELECT COUNT(*) "
				                 ."   FROM ".$wmovhos."_000020, ".$wmovhos."_000018 "
				                 ."  WHERE habcod = '".$whab."'"
				                 ."    AND habhis = ubihis "
				                 ."    AND habing = ubiing "
				                 ."    AND ubialp = 'on' ";
				             $res1 = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
				             $row1 = mysql_fetch_array($res1);
				             if ($row1[0] > 0)
				                {
				                 ?>
						           <script>
						              jAlert ("!!! LA HABITACION ESTA EN PROCESO DE ALTA, DEBE SOLICITAR ESTE SERVICIO DESDE EL [SISTEMA DE ALTAS] COLOCANDO EL ALTA DEFINITIVA ¡¡¡", "Alerta");
						           </script>
						         <?php
						         $wval="N";
					            }
					           else
					              {
						           if ($row[1]=="Muerte")
						              {
					                   ?>
							            <script>
							              jAlert ("!!!! DEBE REGISTRAR LA MUERTE DESDE EL SISTEMA DE ALTAS Y ESTE HARA LA SOLICITUD AUTOMATICA DEL CAMILLERO ¡¡¡¡", "Alerta");
							            </script>
							           <?php
							           $wval="N";
						              }
						             else
						               {
							            if ($row[1] == "Altas")
							               {
						                    ?>
								             <script>
								               jAlert ("!!! LA HABITACION ** NO ** ESTA EN PROCESO DE ALTA, DEBE SOLICITAR ESTE SERVICIO DESDE EL [SISTEMA DE ALTAS] COLOCANDO EL ALTA EN PROCESO Y LUEGO ALTA DEFINITIVA ¡¡¡", "Alerta");
								             </script>
								            <?php
								            $wval="N";
							               }
						               }
					              }
			                }
			               else
			                  {
			                   ?>
					            <script>
					              jAlert ("DEBE DIGITAR LA HABITACION ", "Alerta");
					            </script>
					           <?php
                               echo "<input type='HIDDEN' id='wmotivo_solicitud' name='wmotivo_solicitud' value='".$wmotivo_solicitud."'>";
					           $wval="N";
				              }
				       }


                      if ($row[0]=="Historia" )
				         {

					      if (isset($whab) and $whab!="")
				            {
				             $wval="S";
			                }
			               else
			                  {
			                   ?>
					            <script>
					              jAlert ("DEBE INGRESAR LA HABITACION, CEDULA O HISTORIA", "Alerta");
					            </script>
					           <?php
                               echo "<input type='HIDDEN' id='wmotivo_solicitud' name='wmotivo_solicitud' value='".$wmotivo_solicitud."'>";
					           $wval="N";
				              }
			             }


				      if ($row[0]=="Observacion")                                              // Diciembre 5 de 2007
				         {
					      if (isset($wobservacion) and $wobservacion!="")
				            {
				             $wval="S";
			                }
			               else
			                  {
			                   ?>
					            <script>
					              jAlert ("DEBE DIGITAR EL NOMBRE DEL PACIENTE EN EL CAMPO DE OBSERVACIONES", "Alerta");
					            </script>
					           <?php
                               echo "<input type='HIDDEN' id='wmotivo_solicitud' name='wmotivo_solicitud' value='".$wmotivo_solicitud."'>";
					           $wval="N";
				              }
			             }
                      }


                        //==================================================================================================
                         //Consulta si ya hay una solicitud de cama activa para el paciente.
                        $wdatohab = explode("-",$whab);
                        $whistoria = $wdatohab[1];

                        //Verifica si el cco es de urgencias.
                        if ($row_urgen['ccourg'] == 'on')
                        {
                          $whistoria = $wdatohab[0];  //Toma este dato ya que se escribe la historia
                        }


                        $wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

                        //Traigo el Tipo de Central
                        $q = " SELECT Tip_central "
                            ."   FROM ".$wcencam."_000001 "
                            ."  WHERE Descripcion = '".$wmotivo."'"
                            ."    AND Estado = 'on' ";
                        $restce = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
                        $rowcen = mysql_fetch_array($restce);
                        $wtipcen = $rowcen[0];

                        //Si el tipo de central el camas, entonces entrara a validar si la historia ya tiene solicitud.
					if ($wtipcen == $wcentral_camas)
					{
					//no hospitalario
							$q2 =        "  SELECT Motivo, Observacion, Destino, Solicito, Camillero, A.Hora_data, Hora_respuesta, Habitacion, A.Id, observ_central, A.Fecha_data ,Origen"
										."    FROM ".$wcencam."_000003 A"
										//."   WHERE Origen = '".$wser."'"
										."   WHERE Fecha_llegada     = '0000-00-00' "
										."     AND Fecha_cumplimiento = '0000-00-00' "
										."     AND Hora_llegada      = '00:00:00' "
										."     AND Hora_Cumplimiento = '00:00:00' "
										."     AND Anulada           = 'No' "
										."     AND Historia          != ''"
										."     AND Historia          = '".$whis."'"
										."     AND Central           = '".$wcentral_camas."'";
							$res2 = mysql_query($q2,$conex);
							$numsolicitudes = mysql_num_rows($res2);
							$row3 = mysql_fetch_array($res2);
                        if ($numsolicitudes > 0)
                        {
                            echo
                            "<script>
								jAlert ('!!! HAY UNA SOLICITUD DE CAMA ACTIVA PARA ESTE PACIENTE  HECHA EN : ".$row3['Origen']." ¡¡¡', 'Alerta');
                            </script>";
                        $wval="N";
                        }

                    }
                      //==================================================================================================

		          }
		         else
		            if (isset($wmotivo))                                                       // Diciembre 5 de 2007
		               {
 			            $q = " SELECT dato_obligatorio, texto_pantalla "
					        ."   FROM ".$wcencam."_000001 "
					        ."  WHERE Descripcion = '".$wmotivo."'";
					    $res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
					    $row = mysql_fetch_array($res);

					    if ($row[0]=="Observacion")
				         {
					      if (isset($wobservacion) and $wobservacion!="")
				            {
				             $wval="S";
			                }
			               else
			                  {
			                   ?>
					            <script>
					              jAlert ("DEBE DIGITAR EL NOMBRE DEL PACIENTE ", "Alerta");
					            </script>
					           <?php
					           $wval="N";
				              }
			             }

                         if ($row[0]=="Historia")
				         {
					      if (isset($whab) and $whab!="")
				            {
				             $wval="S";
			                }
			               else
			                  {
			                   ?>
					            <script>
					              jAlert ("DEBE DIGITAR LA HABITACION, LA HISTORIA, O EL DOCUMENTOS DE IDENTIDAD.", "Alerta");
					            </script>
					           <?php
					           $wval="N";
				              }
			             }

			             //Si es obligatorio pero es un motivo de ALTA, No obliga si el servicio NO es hospitalario
			             if (strtoupper($row[0])=="HABITACION" and strtoupper($row[1]) != "ALTAS")
					        {
						     if (isset($whab) and $whab!="")
					           {
					            $wval="S";
				               }
				              else
				                 {
				                  ?>
						           <script>
						             jAlert ("DEBE DIGITAR LA HABITACION, LA HISTORIA, O EL DOCUMENTOS DE IDENTIDAD.", "Alerta");
						           </script>
						          <?php
						          $wval="N";
					             }
				            }

                            //Consulta si ya hay una solicitud de cama activa para el paciente.
                            $wdatohab = explode("-",$whab);
                            $whistoria = $wdatohab[0]; //La historia fue escrita.
                            $wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

                            //Traigo el Tipo de Central
                            $q = " SELECT Tip_central "
                                ."   FROM ".$wcencam."_000001 "
                                ."  WHERE Descripcion = '".$wmotivo."'"
                                ."    AND Estado = 'on' ";
                            $restce = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
                            $rowcen = mysql_fetch_array($restce);
                            $wtipcen = $rowcen[0];
                            //Si el tipo de central el camas, entonces entrara a validar si la historia ya tiene solicitud.
                            if ($wtipcen == $wcentral_camas)
                            {

                            $q2 =        "  SELECT Motivo, Observacion, Destino, Solicito, Camillero, A.Hora_data, Hora_respuesta, Habitacion, A.Id, observ_central, A.Fecha_data,Origen "
                                        ."    FROM ".$wcencam."_000003 A"
                                      //  ."   WHERE Origen = '".$wser."'"
                                        ."     WHERE Hora_llegada      = '00:00:00' "
                                        ."     AND Hora_Cumplimiento = '00:00:00' "
                                        ."     AND Anulada           = 'No' "
                                        ."     AND Historia          != ''"
                                        ."     AND Historia          = '".$whis."'"
                                        ."     AND Central           = '".$wcentral_camas."'";
                            $res2 = mysql_query($q2,$conex);
                            $numsolicitudes = mysql_num_rows($res2);
							$row3 = mysql_fetch_array($res2);


								echo "<script>jAlert('hola :".$whis.": ".$wcencam."', 'Alerta');</script>";

                            if ($numsolicitudes > 0)
                                {
                                  echo
								"<script>
									jAlert ('!!! HAY UNA SOLICITUD DE CAMA ACTIVA PARA ESTE PACIENTE  HECHA EN :  ".$row3['Origen']." ¡¡¡', 'Alerta');
								</script>";
                                $wval="N";
                                }
                            }
			           }

                        //==================================================================================

		       //=====================================================================================================================================
			   //=====================================================================================================================================

			                                                                                                   // Diciembre 5 de 2007
			   if (isset($wmotivo) and isset($wdestino) and (strlen($wmotivo) > 1) and (strlen($wdestino) > 1) and $wval=="S")
			      {
				    $wrefresh = "'0";
				    /*
				    /////
				    //Aca evaluo si la solicitud ya habia sido enviada
				    $q = "  SELECT count(*) AS can "
				        ."    FROM ".$wcencam."_000003 "
				        ."   WHERE Origen            = '".$wser."'"
				        ."     AND Destino           = '".$wdestino."'"
				        ."     AND Motivo            = '".$wmotivo."'"
				        ."     AND Habitacion        = '".$whab."'"
				        ."     AND Hora_llegada      = '00:00:00' "
				        ."     AND Hora_Cumplimiento = '00:00:00' "
				        ."     AND Anulada           = 'No' "
				        ."     AND Fecha_data        = '".$wfecha."'"
				        ."     AND MINUTE(str_to_date('".$hora."','%H:%i:%s'))-MINUTE(str_to_date(Hora_data,'%H:%i:%s')) <= 10 "; //Pasados 10 Minutos
				    $res = mysql_query($q,$conex);
				    $num = mysql_num_rows($res);
				    $row = mysql_fetch_array($res);
				    $wcan=$row[0];

				    //Si es mayor a cero es porque la solicitud ya habia sido registrada
				    if ($wcan > 0)
				       {
					    $wrefresh = "'7";
					    echo "<br>";
				        echo "<center><table border=0>";
				        echo "<td bgcolor=#330099><font size=1 color=FFFFFF><b><blink> !ATENCION¡ ESTA SOLICITUD YA HABIA SIDO REGISTRADA Y ESTA EN PROCESO</blink></b></font></td>";
				        echo "</center></table>";
			           }
				      else */
				       {
					    //=======================================
					    //Traigo el Centro de Costo

					   $q = " SELECT Cco "
					        ."   FROM ".$wcencam."_000004 "
					        ."  WHERE Nombre = '".$wser."'"
							."    AND Estado = 'on' ";
                        $rescco = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
					    $rowcco = mysql_fetch_array($rescco);
					    $wcco1 = explode("-",$rowcco[0]);

						if ($wcco1[0]=='NO APLICA' OR $wcco1[0]=='')
							{
							$wcco1[0]='*';
							}

						//=======================================
					    //Traigo el Tipo de Central
                        $q = " SELECT Tip_central "
					        ."   FROM ".$wcencam."_000001 "
					        ."  WHERE Descripcion = '".$wmotivo."'"
							."    AND Estado = 'on' ";
                        $restce = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
					    $rowcen = mysql_fetch_array($restce);
					    $wtipcen = $rowcen[0];
                        //=======================================

						//=============================================================================
					    //Traigo la Central asignada para el Centro de Costos según el Tipo de Central
					    $q = " SELECT Rcccen "
					        ."   FROM ".$wcencam."_000009 "
					        ."  WHERE Rcccco = '".$wcco1[0]."'"
							."    AND Rcctic = '".$wtipcen."'";
					    $rescen = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
					    $rowcen = mysql_fetch_array($rescen);
					    $wcentral=$rowcen[0];

						if ($wcentral == FALSE)
							{

							$q = " SELECT Rcccen "
								."   FROM ".$wcencam."_000009 "
								."  WHERE Rcccco = '*'"
								."    AND Rcctic = '".$wtipcen."'";
							$rescen = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
							$rowcen = mysql_fetch_array($rescen);
							$wcentral=$rowcen[0];
							}
						else
							{
							$wcentral=$wcentral;
							}

                        //=============================================================================


					    //Ago 13 de 2010
					    //Si se coloco habitacion, traigo el nombre del paciente

					if( ($tipobusqueda == 'habitacion' && isset($tipobusqueda)) OR (!isset($tipobusqueda)) )
					{
					   if ($whab != "")     //Habitacion
					   {
							$wdatohab = explode("-",$whab);
							$whab = $wdatohab[0];
							$whis = $wdatohab[1];

							$q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex,orihis"
								."   FROM root_000036, root_000037, ".$wmovhos."_000020 "
								."  WHERE habcod = '".$whab."'"           //Como Habitacion
								."    AND habhis = orihis "
								."    AND habing = oriing "
								."    AND oriori = '".$wemp_pmla."'"
								."    AND oriced = pacced "
								."    AND oritid = pactid ";
							$reshab = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
							$rowhab = mysql_fetch_array($reshab);

							$whis = $rowhab['orihis'];

							$numhab = mysql_num_rows($reshab);
							$wedad = calcularAnioMesesDiasTranscurridos($rowhab[4], $fecha_fin = '');

							$wresponsable = traerresponsable($whab, 'habitacion');

							switch ($rowhab[5]) {
								case 'M':
										$wgenero = "Masculino";
								break;

								case 'F':
										$wgenero = "Femenino";
								break;


								default:
									break;
							}

							if ($numhab > 0)
								$whab="<b>Hab: ".$whab."</b><br>Pac: ".$rowhab[0]." ".$rowhab[1]." ".$rowhab[2]." ".$rowhab[3]."<br>Edad:".$wedad['anios']."<br>Genero:".$wgenero."<br>Responsable:".$wresponsable;
							else
							    $whab="<b>".$whab."</b><br>El dato No existe en la Base de Datos";

						}
					}

					if( $tipobusqueda == 'historia' )
					{
					 if ($whab != "")     //Habitacion
					   {
							 //Busco si lo digitado es la historia y con ese dato traigo el nombre del paciente
							 //si no busco si es la cedula y con el dato busco por cedula
							 $q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex "
								."   FROM root_000036, root_000037 "
								."  WHERE orihis = '".$whab."'"       //Como Historia
								."    AND oriori = '".$wemp_pmla."'"
								."    AND oriced = pacced "
								."    AND oritid = pactid ";
							$reshab = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
							$rowhab = mysql_fetch_array($reshab);
							$numhab = mysql_num_rows($reshab);
							$whis = $whab; // En este caso se guardara la historia para el paciente en al tabla 3 de cencam.
							$wedad = calcularAnioMesesDiasTranscurridos($rowhab[4], $fecha_fin = '');
							$wresponsable = traerresponsable($whis,'historia');

						  switch ($rowhab[5]) {
							case 'M':
									$wgenero = "Masculino";
							break;

							case 'F':
									$wgenero = "Femenino";
							break;


							default:
								break;
							}
							if ($numhab > 0)
							   $whab="<b>Historia: ".$whab."</b><br>Pac: ".$rowhab[0]." ".$rowhab[1]." ".$rowhab[2]." ".$rowhab[3]."<br>Edad:".$wedad['anios']."<br>Genero:".$wgenero."<br>Responsable:".$wresponsable;
							else
								$whab="<b>".$whab."</b><br>El dato No existe en la Base de Datos";
						}
					}

					if( $tipobusqueda == 'dcoidentidad' )
					{
						if ($whab != "")     //Habitacion
						{

							 //Busco si lo digitado es la historia y con ese dato traigo el nombre del paciente
							 //si no busco si es la cedula y con el dato busco por cedula
							 $q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex,Orihis "
								."   FROM root_000036, root_000037 "
								."  WHERE oriced = '".$whab."'"    //Como Cedula o Documento
								."    AND oriori = '".$wemp_pmla."'"
								."    AND oriced = pacced "
								."    AND oritid = pactid ";

							$reshab = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
							$rowhab = mysql_fetch_array($reshab);
							$numhab = mysql_num_rows($reshab);
							$whis = $rowhab['Orihis'];
							$wedad = calcularAnioMesesDiasTranscurridos($rowhab[4], $fecha_fin = '');
							$wresponsable = traerresponsable($whis,'cedula');

							switch ($rowhab[5]) {
								case 'M':
										$wgenero = "Masculino";
								break;

								case 'F':
										$wgenero = "Femenino";
								break;


								default:
									break;
							}

							if ($numhab > 0)
							{
							   $whab="<b>Cédula: ".$whab."</b><br>Pac: ".$rowhab[0]." ".$rowhab[1]." ".$rowhab[2]." ".$rowhab[3]."<br>Edad:".$wedad['anios']."<br>Genero:".$wgenero."<br>Responsable:".$wresponsable;
							}
							else
							{
								$whab="<b>".$whab."</b><br>El dato No existe en la Base de Datos";
								$whis = ''; // Como no hay datos para el paciente la historia debe ir vacia en la tabla 3 de cencam
							}
						}
					}



						$q = "  INSERT INTO ".$wcencam."_000003 (   Medico  ,   Fecha_data,    Hora_data,      Origen  ,   Motivo     ,    Habitacion,   Observacion     ,    Destino     ,   Solicito    ,   Ccosto  , Camillero, Hora_respuesta, Hora_llegada, Hora_Cumplimiento, Anulada, Observ_central,    Central     ,    Historia,  Seguridad,SedeDestino) "
					        ."                           VALUES ('".$wcencam."','".$wfecha."','".$hora."'  ,'".$wser."','".$wmotivo."', '".$whab."','".$wobservacion."', '".$wdestino."','".$wusuario."','".$wser."', '".$wcamillero."'       , ''            , ''          , ''               , 'No'   , ''            , '".$wcentral."', '".$whis."', 'C-".$wusuario."','".$sedeDestino."')";
					    
						
						$res2 = mysql_query($q,$conex) or die(mysql_errno().":".mysql_error());
                        $wid=mysql_insert_id(); // Ultimo id insertado.

                        $wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

                        if ($wcentral == $wcentral_camas)
                            {
                                registrarAsignacion($wid); //Registrar en la tabla cencam_000010 la solicitud.
                            }

					    $q = "  SELECT Motivo, Observacion, Destino, Solicito, Camillero, A.Hora_data, Hora_respuesta, Habitacion, A.Id, observ_central, A.Fecha_data "
					        ."    FROM ".$wcencam."_000003 A, ".$wcencam."_000006 B"
					        ."   WHERE Origen = '".$wser."'"
					        ."     AND Hora_llegada      = '00:00:00' "
					        ."     AND Hora_Cumplimiento = '00:00:00' "
					        ."     AND Anulada           = 'No' "
					        ."     AND Central           = codcen "
							."     AND TIMESTAMPDIFF(HOUR,CONCAT(A.Fecha_data,' ',A.Hora_data),CONCAT('".$wfecha."',' ','".$hora."')) <= cenvig ";

						$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					    $num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());

					    echo "<br>";
					    echo "<br>";
					    echo "<center><table border=0>";
					    echo "<tr><td align=center bgcolor=#fffffff colspan=9><font size=4 text color=#CC0000><b>SOLICITUDES PENDIENTES REGISTRADAS EN LAS ÚLTIMAS 24 HORAS</b></font></td></tr>";
			            echo "<tr><td align=left bgcolor=#fffffff colspan=9><font size=2 text color=#CC0000><b>Hora: ".$hora."</b></font></td></tr>";

					    echo "<tr>";
					    echo "<th bgcolor=#ffcc66><font size=1>MOTIVO</font></th>";
					    echo "<th bgcolor=#ffcc66><font size=1>HABITACION</font></th>";
					    echo "<th bgcolor=#ffcc66><font size=1>OBSERVACION</font></th>";
				        echo "<th bgcolor=#ffcc66><font size=1>DESTINO</font></th>";
				        echo "<th bgcolor=#ffcc66><font size=1>SOLICITADO POR</font></th>";
				        echo "<th bgcolor=#ffcc66><font size=1>CAMILLERO/<BR>HABITACION</font></th>";
						echo "<th bgcolor=#ffcc66><font size=1>FECHA SOLICITUD</font></th>";
				        echo "<th bgcolor=#ffcc66><font size=1>HORA SOLICITUD</font></th>";
				        echo "<th bgcolor=#ffcc66><font size=1>RESPUESTA CENTRAL</font></th>";
				        echo "<th bgcolor=#ffcc66><font size=1>OBSERVACION DE LA CENTRAL</font></th>";
				        echo "<th bgcolor=#ffcc66><font size=1>ANULAR</font></th>";
				        echo "</tr>";

					    for ($i=1;$i<=$num;$i++)
					        {
						     if (is_integer($i/2))
						         $wcolor = "99ccff";
						        else
						           $wcolor = "dddddd";

						     $row = mysql_fetch_array($res);
						     echo "<tr>";
						     echo "<td bgcolor=".$wcolor."><font size=1>".$row[0]."</font></td>";                   // Motivo
						     echo "<td bgcolor=".$wcolor."><font size=1>".$row[7]."</font></td>";                   // Habitacion
						     echo "<td bgcolor=".$wcolor."><font size=1>".$row[1]."</font></td>";                   // Observacion
					         echo "<td bgcolor=".$wcolor."><font size=1>".$row[2]."</font></td>";                   // Destino
					         //===============================================================
					         $q = "  SELECT Descripcion "
					             ."    FROM usuarios "
					             ."   WHERE Codigo = '".$row[3]."'"
					             ."     AND Activo = 'A' ";
						     $res2 = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
						     $row1 = mysql_fetch_array($res2);
						     if ($row1[0] == "")
						        $wnomusu = $row[3];                                                                 // Sin Nombre de Solicitado por
						       else
						          $wnomusu = $row1[0];                                                              // Solicitado por
						     //===============================================================
						     echo "<td bgcolor=".$wcolor."><font size=1>".$wnomusu."</font></td>";
						     if ($row[4] == "")
						        echo "<td bgcolor=".$wcolor."><font size=1>&nbsp</font></td>";                      // Sin Camillero
						       else
					             echo "<td bgcolor=".$wcolor."><font size=1>".$row[4]."</font></td>";               // Camillero
							 echo "<td bgcolor=".$wcolor." align=center><font size=1>".$row[10]."</font></td>";     // Fecha de solicitud
					         echo "<td bgcolor=".$wcolor." align=center><font size=1>".$row[5]."</font></td>";      // Hora de solicitud
					         if ($row[6] == "")
					            echo "<td bgcolor=".$wcolor."><font size=1>&nbsp</font></td>";                      // Sin Hora respuesta de la Central
					           else
					               echo "<td bgcolor=".$wcolor."><font size=1>".$row[6]."</font></td>";             // Hora respuesta de la Central

					         if ($row[9] == "")
					            echo "<td bgcolor=".$wcolor."><font size=1>&nbsp</font></td>";                      // Sin Observacion de la Central
					           else
					             if (isset($wobscc[$i]))
					                echo "<td align=left bgcolor=".$wcolor.">".$wobscc[$i]."</td>";                 // Observacion de la central camilleros
					               else
			                          echo "<td align=left bgcolor=".$wcolor.">".$row[9]."</td>";                   // Observacion de la central camilleros
					         ///////
					         echo "<td align=center bgcolor=".$wcolor."><font size=1><INPUT TYPE=CHECKBOX NAME='wanulada[".$i."]'></font></td>";


					         if (isset($wanulada[$i]))
					            {
					             $q = "   UPDATE ".$wcencam."_000003 "
			                         ."      SET Anulada = 'Si', "
			                         ."          Solicito = '".$wusuario."'"
			                         ."    WHERE Id = ".$row[8];
			                     $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

                                 //Se cancela la solicitud en la tabla 10 de cencam.
                                 $q_aca = "   UPDATE ".$wcencam."_000010 "
			                         ."      SET Acaest = 'off'"
			                         ."    WHERE Id = ".$row[8];
			                     $resaca = mysql_query($q_aca,$conex) or die (mysql_errno()." - ".mysql_error());

                                //Consulta la cama asociada al id que se actualizara para cambiarle el estado habpro a off.
                                $q = "  SELECT Hab_asignada "
                                    ."    FROM ".$wcencam."_000003"
                                    ."   WHERE id     = '".$row[8]."'";
                                $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
                                $row = mysql_fetch_array($res);

                                $whabitacion = $row['Hab_asignada'];

                                 //La habitacion se pone en proceso de ocupacion igua a off ya que el usuario
                                //selecciono habitacion vacio, pero el paciente ya tiene historia asignada.
                                $q =  " UPDATE ".$wmovhos."_000020 "
                                    . "    SET Habpro = 'off'"
                                    . "  WHERE Habcod = '".$whabitacion."'";
                                $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());


			                     echo "<meta http-equiv='refresh' content='0;url=solicitud_camillero.php?wser=".$wser."&wemp_pmla=".$wemp_pmla."&wuso=".$wuso."&selectsede=".$sCodigoSede."&caso=".$caso."'>";
			                    }
			                 ///////
			                 echo "<td align=center bgcolor=#cccccc></b><input type='submit' value='OK'></b></td>";
					         echo "</tr>";
				            }
				        echo "</center></table>";
				       }
			        unset ($wmotivo);
				    unset ($wdestino);
			       }

			 echo "<meta http-equiv='refresh' content=".$wrefresh.";url=solicitud_camillero.php?wser=".$wser."&wemp_pmla=".$wemp_pmla."&wuso=".$wuso."&selectsede=".$sCodigoSede."&caso=".$caso."'>";
		 }
     echo "<tr></tr>";
     echo "<tr></tr>";
     echo "<tr></tr>";
     echo "<tr><td align=center colspan=13><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";

     echo "</form>";
}
include_once("free.php");
?>