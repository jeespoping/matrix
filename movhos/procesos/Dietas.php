<?php
include_once("conex.php");
session_start();

if(!isset($_SESSION['user']))
	echo "error";
else
{
  include_once("root/magenta.php");
  include_once("root/comun.php");
  include_once("movhos/mensajeriaDietas.php");
  include_once("movhos/movhos.inc.php");
  $sesion_usuario = $_SESSION["user"];
  $array_user_aux = explode("-",$sesion_usuario);

// =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz = "2020-09-28"; // Ultima fecha de actualizacion de este programa
// =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

//-----------------------------------------------------------------------------------------------------
//    --> FUNCION QUE GENERA UN QUERY CON TODAS LA POSIBLES COMBINACIONES, BAJO LA MODALIDAD QUE PRIMERO
//        SE BUSCA POR UN VALOR ESPECIFICO Y SI NO POR EL VALOR *
//        Autor:                     Edward jaramillo, Jerson trujillo, Felipe alvarez.
//------------------------------------------------------------------------------------------------------
function generarQueryCombinado($variables, $tabla, $filtro, $filtro_aux)
{
    $matriz         = array();
    $arrayPunteros     = array();

    // --> Obtener el numero de valores a combinar
    $contador    = 0;
    $numValores    = 0;
    foreach($variables as $campo => $valores)
    {
        if($valores['combinar'])
        {
            $arrayPunteros[$numValores]['campo']            = $campo;
            $arrayPunteros[$numValores]['posicionTodos']     = $contador;
            $numValores+= 1;
        }
        $arrayTodos[$contador]['combinable']     = $valores['combinar'];
        $arrayTodos[$contador]['campo']         = $campo;
        $contador++;
    }
    $numCombinaciones     = pow(2, $numValores);
    $numCambio             = $numCombinaciones;

    // --> Genero una matriz con todas las posibles combinaciones
    for($x=0; $x<$numValores; $x++)
    {
        $numCambio         = $numCambio/2;
        $limteCambio    = $numCambio;
        $asignar        = 1;
        for($y=0; $y<$numCombinaciones; $y++)
        {
            if($y<$limteCambio)
                $matriz[$y][$x] = $asignar;
            else
            {
                $limteCambio+=$numCambio;
                $asignar = (($asignar == 1) ? 0 : 1);
                $matriz[$y][$x] = $asignar;
            }
        }
    }

    // --> Aqui se arma el query, dado el numero de combinaciones.
    $union = '';
    $query = '';

    for($x=0; $x<$numCombinaciones ;$x++)
    {
        $query.= $union.'
                SELECT id , '.$x.' as prioridad
                FROM '.$tabla.'
                WHERE ';
        $and = '';

        // --> Primero debo agregar si existen, los filtros que son fijos en el query
        if(array_key_exists(0, $arrayTodos) && !$arrayTodos[0]['combinable'])
        {
            for($z=0; $z<count($arrayTodos); $z++)
            {
                // --> Si no es combinables, es un filtro fijo.
                if(!$arrayTodos[$z]['combinable'])
                {
                    $nomCampo = $arrayTodos[$z]['campo'];
                    // --> Si existe la variable SQL, significa que es un segmento SQL fijo que se debe agregar
                    if(array_key_exists('SQL', $variables[$nomCampo]))
                    {
                        $query.= "
                            {$and} ".$variables[$nomCampo]['valor'];
                    }
                    // --> Se agrega el filtro al query
                    else
                    {
                        $query.= "
                            {$and} ".$nomCampo." $filtro '$filtro_aux".$variables[$nomCampo]['valor']."$filtro_aux'";
                    }
                    $and = ' AND ';
                }
                else
                    break;
            }
        }

        // --> Recorrer cada uno de lo filtros (campos)
        for($y=0; $y<$numValores ;$y++)
        {
            $nomCampo         = $arrayPunteros[$y]['campo'];
            $posicionTodos     = $arrayPunteros[$y]['posicionTodos'];

            if ($matriz[$x][$y] == 1)
                $query.= "
                        {$and} ".$nomCampo." $filtro '$filtro_aux".$variables[$nomCampo]['valor']."$filtro_aux'";
            else
                $query.= "
                        {$and} ".$nomCampo." $filtro '$filtro_aux*$filtro_aux' ";

            // --> Si una posicion mas adelante existe un filtro que va fijo en el query.
            if(array_key_exists($posicionTodos+1, $arrayTodos) && !$arrayTodos[$posicionTodos+1]['combinable'])
            {
                for($z=$posicionTodos+1; $z<count($arrayTodos); $z++)
                {
                    // --> Si no es combinable, es un filtro fijo.
                    if(!$arrayTodos[$z]['combinable'])
                    {
                        $nomCampo = $arrayTodos[$z]['campo'];
                        // --> Si existe la variable SQL, significa que es un segmento SQL fijo que se debe agregar
                        if(array_key_exists('SQL', $variables[$nomCampo]))
                        {
                            $query.= "
                                {$and} ".$variables[$nomCampo]['valor'];
                        }
                        // --> Se agrega el filtro al query
                        else
                        {
                            $query.= "
                                {$and} ".$nomCampo." $filtro '$filtro_aux".$variables[$nomCampo]['valor']."$filtro_aux'";
                        }
                        $and = ' AND ';
                    }
                    else
                        break;
                }
            }

            $and = ' AND ';
        }
        $union = '
                UNION
                ';
    }

    return $query." ORDER BY prioridad";
}

	$IIPP_aux = $IIPP;

	$variables = array();
    $variables['enc.Dipusu']['combinar'] = true;
    $variables['enc.Dipusu']['valor']    = $array_user_aux[1];

    $variables['enc.Dipnip']['combinar'] = true;
    $variables['enc.Dipnip']['valor']    = $IIPP_aux;

    $variables['enc.Dipest']['combinar'] = false;
    $variables['enc.Dipest']['valor']    = "on";

    $sql = generarQueryCombinado($variables, "root_000095 AS enc", "=", "");
	$result = mysql_query($sql, $conex) OR die(mysql_errno()." - ".mysql_error().' > '.$sql);
	$num_rows = mysql_num_rows($result);

	//Se evalua si hay un registro asociado a la ip con la que entra el usuario.
	if($num_rows == 0){


		$IIPP_aux_a = substr( $IIPP, 0, 5 );

		$variables_a = array();
		$variables_a['enc.Dipusu']['combinar'] = true;
		$variables_a['enc.Dipusu']['valor']    = $array_user_aux[1];

		$variables_a['enc.Dipnip']['combinar'] = true;
		$variables_a['enc.Dipnip']['valor']    = $IIPP_aux_a;

		$variables_a['enc.Dipest']['combinar'] = false;
		$variables_a['enc.Dipest']['valor']    = "on";
		//Verifico con los
		$sql_a = generarQueryCombinado($variables_a, "root_000095 AS enc", "LIKE", "%");
		$result_a = mysql_query($sql_a, $conex) OR die(mysql_errno()." - ".mysql_error().' > '.$sql_a);
		$num_rows = mysql_num_rows($result_a);

	}


	if($num_rows == 0){

	 die ("<br>\n<br>\n".
        " <H1>No puede ingresar al programa de Solicitud de Dietas desde un equipo externo a la Clínica Las Américas <br><br> Volver al <a href='http://mx.lasamericas.com.co/matrix/f1.php'> inicio </a>");

	}
/*
//==========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION                                                                                                                              \\
//=========================================================================================================================================\\
//En este programa se registran las diferentes dietas por servicio y pacientes de la clinica.                                              \\
//=========================================================================================================================================\\
//2020-09-28 Edwin MG	  : Se corrige  fecha en la funcion traer_observaciones_dsn
//2020-09-21 Camilo Zapata: Se corrige consultas con fecha debido al cambio de BD (ya no se permite consultas con fecha vacia ej: fecha_data = '' )
//2019-10-28 Camilo Zapata: Ahora el programa se refresca despues de darle click al botón grabar en la modal de patrón SI(Servicio Individual), para que
                            habilite los campos de observaciones e intolerancias inmediatamente. buscar por fecha si es necesario.
//2019-10-23 Camilo Zapata: Modificación en el programa para evitar caracteres erroneos en el nombre del patrón DSN wpatron_dsn_asociado, de tal manera que
                            Como en la modificación anterior ya se garantiza la configuración ut8 en el momento de grabar el registro, no haga ninguna transformación posterior, manteniendo los caracteres tal cual están grabados en la bd.
//2019-10-10 Camilo Zapata: Modificación en la función que hable la modal para DSN, para que calcule su respectivo tamaño sin asignar un tiempo de recarga.
                             y corrección en caracteres, en observaciones y nombres en el dns.(Buscar fecha de ser necesario.)
//2019-10-03 Camilo Zapata: Modificacion para la validación del tamaño de texto permitido en observaciones e intolerancias. ya que son 350 caracteres
                            que se deben repartir entre los dos.
//2019-09-24 Camilo Zapata: Se verifica si un servicio(DSN) para una historia e ingreso ya existe programado para un dia específico,
                            En caso de que ya exista se guardara en el log, la modificación de la dieta, si no existe se almacenará como nuevo pedido.
//2019-09-23 Camilo Zapata: Adición del nombre específico de la zona y la habitación para cada uno de los pacientes de urgencias.
//2019-09-19 Camilo Zapata: Se realiza las modificaciones necesarias para que en los servicios hospitalarios y urgencias se puedan visualizar
                            todos los pacientes que estén en el centro de costos definido y zona( cuando existan ); En caso de ser necesario
                            buscar "wzona".
//Enero 24 de 2019 Edwin MG
//- No se permite modificar observaciones o intolerancias si no hay un patrón seleccionado
//- Las observaciones se traen de acuerdo al último servicio
//=========================================================================================================================================\\
//Diciembre 21 de 2018 Edwin MG
//- SE modifica query que trae las observaciones del último servicio
//=========================================================================================================================================\\
//Diciembre 17 de 2018 Edwin MG
//Se realizan cambios varios:
//	- En la auditoría (movhos 78) al hacer un cambio en observaciones queda la observación anterior y la nueva
//	- Al empezar a escribir en el campo de observaciones el tiempo de refrescar la página se detiene, una vez se deja de escribir pasados 5
//	  segundos las observaciones se graban y 45 segundos despues la página se refresca
//	- Se valida que no se pueda modificar las observaciones y tolerancias en horarios no permitidos
//=========================================================================================================================================\\
//Agosto 30 de 2018 Jonatan
//Se permite la solicitud de dietas a pacientes de cirugia.
//=========================================================================================================================================\\
//Julio 23 de 2018 Jonatan
//Se corrige la solicitud de DSN en horario de adicion por parte de la nutricinista, para que puedan solicitar sin necesidad de eliminar el
//patron diferente a DSN del paciente.
//Si esta en horario normal le avisa a la nutricionista que se cancelara el patron que tiene el paciente diferente a DSN.
//=========================================================================================================================================\\
//Julio 18 de 2018 Jonatan
//Se muestra el rol del ultimo usuario que solicito el patron para el paciente en el tooltip, se permite solicitud de patrones diferentes a DSN
//por parte de las nutricionistas, ademas se restringe el ingreso al servicio para este rol(nutricionista) individual a las nutricionistas.
//=========================================================================================================================================\\
//Mayo 29 de 2018 Jonatan
//Se deja de mostrar la columna de postquirurgico por peticion de Liliana.
//Se filtran los patrones por el tipo de centro de costos con los campos Diehos, Dieurg, Diecir, Dieayu, cuando estan activos alguno de estos
//campos se muestra el patron, inicialmente todos los patrones estan activos en Diehos, Dieurg = HGL LC L SI DSN, Diecir = SI LC L, Dieayu = SI LC L.
//=========================================================================================================================================\\
//Mayo 3 de 2018 Jonatan
//Se agrega auditoria en la actualizacion del registro cuando la enfermera recupera la DSN, en la funcion programar_dsn_enfermeria.
//=========================================================================================================================================\\
//Abril 4 de 2018 Jonatan
//Se corrige la funcion procesar_datos_dsnauto para que registre correctamente el dia de la DSN.
//=========================================================================================================================================\\
//Marzo 5 de 2017 Jonatan
//Se recupera el ultimo esquema solicitado por la nutricionista por parte de la enfermera.
//Si un paciente tiene DSN en dias anteriores, la nutricionista puede recuperar los registros.
//=========================================================================================================================================\\
//Diciembre 1 de 2017 Jonatan
//Se corrige el horario de cancelacion de las DSN para que tome el horario maximo de cancelacion de la tabla movhos_000076 de acuerdo al servicio.
//=========================================================================================================================================\\
//Noviembre 28 de 2017 Jonatan
//Se corrige la cancelacion del patron de DSN para que no sean afectado por cancelacion de otros patrones, solo cancela DSN del dia actual y siguiente
//si el patron seleccionado es DSN, esto por parte de la enfermera jefe.
//=========================================================================================================================================\\
// Agosto 10 de 2017 Jonatan
// Se corrige la consulta para fechas anteriores, para que pueda mostrar los pacientes correctamente.
//=========================================================================================================================================\\
// Abril 28 de 2017 Jonatan
// Se valida que la DSN no pueda ser cancelada en horario de adicion por parte de la enfermera.
//=========================================================================================================================================\\
// Diciembre 5 de 2016 Jessica																											   \\
// Se agrega columna de alertas y alergias para que traiga las ingresadas en movhos_000220.												   \\
//=========================================================================================================================================\\
//Enero 29 de 2015 (Jonatan Lopez)
//Se registraran las observaciones de la nutricionista en la tabla movhos_000078, y si el paciente necesita reprogramar la dieta por parte de la
//enfermera jefe, traera la observacion de ese campo en caso de no tener datos en la tabla movhos_000077 ya que esta es la tabla original desde
//donde se traen lsa observaciones de la nutricionista.
//=========================================================================================================================================\\
//Octubre 01 de 2014 (Jonatan Lopez)
//Se corrige falla al traer las observaciones de la DSN cuando la enfermera solicita la DSN para el dia siguiente, ademas se valida que la
//enfermera no pueda marcar DSN cuando la nutricionista haya cancelado los productos para el paciente en ese servicio.
//=========================================================================================================================================\\
//Agosto 27 de 2014 (Jonatan Lopez)
//Se corrige la cancelacion de dieta segun nutricion por parte de la enfermera; si el paciente tiene pedidos en servicios posteriores al actual,
//hara la cancelacion, y si tiene para el siguiente dia tambien seran cancelados, ademas si la enfermera solicita DSN en la comida, validara si el paciente
//tiene servicios con DSN anteriores y los registrara para el siguiente dia.
//=========================================================================================================================================\\
//Agosto 14 de 2014 (Jonatan Lopez)
//Se corrige el redondeo en la edad para que tenga en cuenta los pacientes de menos de 1 añoy asi se haga correctamente el cobro de la dieta.
//=========================================================================================================================================\\
//Julio 22 de 2014 (Jonatan Lopez)
//Se corrige la asigancion de los patrones de dieta para el servicio asociado, permitiendo que la dieta del servicio ppal si se duplique en el
//servicio asociado correctamente.
//=========================================================================================================================================\\
//Junio 16 de 2014 (Jonatan Lopez)
//Se repara la validacion cuando no hay DSN en el servicio asociado y tambien cuando el servicio de adicion no ha iniciado, ahora se muestran los
//mensaje correspondientes.
//=========================================================================================================================================\\
//Mayo 8 de 2014 (Jonatan Lopez)
//Se agrega validacion de horario de cancelacion de pedido para los pacientes con DSN, no se pueden cancelar patrones despues del horario
//maximo de cancelacion del pedido, ademas se valida que la ip con la que entra el usuario es valida para realizar solicitud de dietas.
//=========================================================================================================================================\\
//Abril 22 de 2014 (Jonatan Lopez)
//Se configura la dieta segun nutricion para que sea solicitada desde el servicio comida, esto permitira tener los registros de DSN desde ese
//servicio en el monitor para realizar analisis por parte de los encargados de la preparacion, esto lo solicita Gloria, ademas si el serivio ya
//esta programado para el dia siguiente y hacen modificaciones, estas modificaciones tambien aplicaran para el dia siguiente.
//=========================================================================================================================================\\
//Abril 10 de 2014 (Jonatan Lopez)
//Se controla que la observacion DSN y el patron asociado a DSN no se pierdan cuando la dieta es cancelada y cambiada por otra.
//=========================================================================================================================================\\
//Enero 20 de 2014 (Jonatan Lopez)
//Se agrega en cada area de texto la validación del máximo de caracteres, esto con el fin de que la información escrita no supere el tamaño de
//la tarjeta de dietas y asi no se pierda información en la impresión.
//=========================================================================================================================================\\
//Enero 14 de 2014 (Jonatan Lopez)
//Se cambia le modo de uso del evento .click() jquery por .trigger('click'), ya que en la nueva version cambio la forma de uso, ademas se corrige
//el agregar estilo al fondo del articulo, para que sea igual la comida con el almuerzo.
//=========================================================================================================================================\\
//Enero 02 de 2014
//Se verifica si el servicio asociado tiene registro en la tabla de movimiento de dietas cuando se cargan las dietas de forma automatica,
//y si asi no permite el registro de la solicitud.
//=========================================================================================================================================\\
//Diciembre 10 de 2013 (Jonatan Lopez)
//Se corrige el registro del patron que se cobra cuando al grupo d epatrones de un paciente se le agrega un servicio individual, quiere decir que
//no tiene patrones cobre el patron individual o si los tiene, siga cobrando el patron principal de la combinacion.
//===========================================================================================================================================\\
//Noviembre 29 de 2013: (Jonatan Lopez)
//Se reemplaza el uso del tag <blink> por una funcion jquery que cumple con la misma funcion, esto para que las alertas del chat
//parpadeen el todos los navegadores, ademas se cambia la validacion de true a checked en el chekbox, cuando la enfermera pide DSN.
//===========================================================================================================================================\\
//Noviembre 07 de 2013 Jonatan Lopez
//Se corrige la funcion programar_dsn_enfermeria, para que no permita el registro de productos si estan activos el dia actual, solo permitira
//registro de productos inactivos del dia actual o anteriores.
//===========================================================================================================================================\\
//Septiembre 19 de 2013
//Se agrega a la funcion procesr_datos_dsnauto la consulta que busca la ultima observacion que ha sido ingresada por la enfermera, para que se
//grabe en cada servicio.
//===========================================================================================================================================\\
//Septiembre 09 de 2013
//Se crea un nuevo campo en movhos_000018 Ubidie, los registros que estén en 'on' en este campo, esas hitorias se listan para la solicitud de dietas
//en urgencias.
//=========================================================================================================================================\\
//Agosto 14 de 2013
//Se valida que el producto ya este registrado cuando la enfermera hace la solicitud DSN.
//=========================================================================================================================================\\
//Julio 29 de 2013
//Se modifica la consulta para traer la observacion DSN, buscara la ultima observacion DSN que tenga el paciente para el servicio solicitado.
//=========================================================================================================================================\\
//Julio 17 de 2013
//Se corrige para que al seleccionar SI y TMO al mismo tiempo si permita sacar eliminar uno de ellos del arreglo cuando sea cancelado.
//=========================================================================================================================================\\
//Julio 17 de 2013
//Se valida que el servicio individual en el ultimo servicio no dañe la DSN del paciente, ademas que la DSN se marque en la primera carga del
//programa.
//=========================================================================================================================================\\
//Julio 12 de 2013
//Se cambia el metodo de busqueda de la fecha para la solicitud DSN de enfermeria, ya se basa en el ultimo registro de la tabla 84 de movhos
//y no en la tabla 77 de movhos.
//=========================================================================================================================================\\
//Julio 11 de 2013
//Se valida que no se pueda pedir DSN en horario de adicion si se le ha cancelado un patron al paciente en horario de adicion.
//=========================================================================================================================================\\
//Julio 09 de 2013
//Se valida si el paciente tenia patron DSN del dia anterior, cuando el ultimo patron fue es diferente a DSN, ya que si permite el registro
//se pierde la DSN.
//=========================================================================================================================================\\
//Julio 03 de 2013
//Se corrigen las observaciones de la dieta segun nutricion solicitada por la enfermera, ya que estaba filtrando por centro de costos y la fecha
//actual del sistema, estos filtros no permitian traer las observaciones de dias anteriores o si el paciente estaba en otra habitacion.
//=========================================================================================================================================\\
//Junio 17 de 2013
//Se quita la validacion de estado la dieta segun nutricion pedida por la enfermera para que tome los ultimos productos registrados.
//=========================================================================================================================================\\
//Mayo 20 de 2013
//Se restringue para que el enfermero no pueda seleccionar productos del patron SI si el paciente tiene DSN activo.
//=========================================================================================================================================\\
//Mayo 17 de 2013
//Se agrega la suma de los costos de los productos cuando la enfermera solcita DSN por primera vez.
//=========================================================================================================================================
//Mayo 15 de 2013
//Se agrega el nombre del paciente en el panel de las dietas y un boton "Salir sin grabar", ademas se guarda siempre la ultima
//nutricionista en el campo movnut de la tabla 77 de movhos.
//=========================================================================================================================================\\
//Mayo 14 de 2013
//Se modifica la consulta de los ultimos productos de DSN basado en el ultimo registro de la 77 activo o inactivo, ademas
//si el paciente tiene un patron en horario normal, la nutricionista puede ingresar a DSN, cancelar el servicio actual y hacer la nueva solicitud.
//=========================================================================================================================================
////Mayo 10 de 2013
//Se hace cambio de esquema de grabacion para las dietas segun nutricion.
////=========================================================================================================================================\\
//Marzo 07 de 2013
//Se hacen las modificaciones necesarias para que las DSN sean funcionales.
//Se valida el patron del servicio asociado, si es igual permite cancelarlo, si es diferente no lo permite.
//==========================================================================================================================================
//Febrero 4 de 2013
//Se cambia el manejo de los productos por categoria, ya que un producto pertenece solamente a una categoria, esto aplica para el
//Servicio Individual y TMO
//=============================================================================================================================================
//Enero 24 de 2013
//Se agrega una validacion que permita que los pacientes menores al parametro LimiteEdadPedidosPos de la root_000051, sean tratados como Prepagados
//y asi se puedan solicitar todos los servicios para el paciente.
//=============================================================================================================================================
//Enero 14 de 2013
//Se cambia el modo en que se toma el tiempo de actualizacion de la mensajeria, antes hacia conexion al script mensajeriaDietas.php por medio de ajax,
//ahora para obtener este dato lo hará en este mismo script.
//=============================================================================================================================================
//Noviembre 20 - Jónatan Lopez
//Se repara el calendario para que funcione en internet explorer, ya que en la misma interfaz no se debe tener la variable del calendario
//oculta y la misma variable asignada al calendario.
//Se optimiza la ventana modal para la solictud de servicios individuales y TMO. Para que la interfaz cargue mas rapido y no tenga que poner
//en oculto todo el esquema de los productos.
//===========================================================================================================================================\\
*/
  $wfecha=date("Y-m-d");
  $whora =(string)date("H:i:s");

  $wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
  $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
  $whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "HCE");
  $wlimite_caracteres_dsn = consultarAliasPorAplicacion($conex, $wemp_pmla, "LimiteCaracteresDSN");
  $wlimite_caracteres_observ = consultarAliasPorAplicacion($conex, $wemp_pmla, "LimiteCaracteresObservDietas");
  $wlimite_carac_patron_asociado = consultarAliasPorAplicacion($conex, $wemp_pmla, "LimiteCaracteresPatronAsociadoDSN");

 if (!isset($consultaAjax))
	{

  encabezado("REGISTRO DE DIETAS",$wactualiz, "clinica");

?>
<head>
  <title>REGISTRO DE DIETAS</title>

  <!--JQUERY-->
	<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" />


	<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../include/root/jquery.blockUI.js"></script>

	<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
	<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>

    <link type="text/css" href="../../../include/root/jatt.css" rel="stylesheet" />
	<script type="text/javascript" src="../../../include/root/jquery.jatt.js"></script>

  <!--Fin JQUERY-->

	<script type="text/javascript" src="../../../include/movhos/mensajeriaDietas.js"></script>

</head>
<style type="text/css">
    .esError{
        border: 1px solid red;
        background-color: lightyellow;
    }

</style>
<script type="text/javascript">

    var color_esq_actual = 'yellow'; // Color para mozilla
	var color_esq_actualie = '#ffff00';  // Color para internet explorer
	var color_ant_sing = 'silver'; // Color para mozilla

    $(document).ready(function(){
        $('#sl_wcco').change(function(){
            buscar_zonas();
        });
    });
    function buscar_zonas(){
        console.log( "entro " );
        var wcco = $('#sl_wcco').val();

        $.ajax({
            url: "Dietas.php",
            type: "POST",
            data:{
                wemp_pmla       : $("#wemp_pmla").val(),
                consultaAjax    : 'filtrarzonas',
                wcco            : wcco
            },
            dataType: "json",
            async: false,
            success:function(data_json) {
                if (data_json.error == 1)
                {
                    jAlert( data_json.mensaje, 'ALERTA' );
                    return;
                }
                else{

                    if(data_json.nro_zonas > 0){
                    $("#tabla_zonas").show();
                    $("#select_zonas").html(data_json.html);
                    }else{

                    $("#tabla_zonas").hide();
                    $("#select_zonas").html("");
                    }

                }
            }

        });

    }
    function trim(myString)
    {
    return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
    }

    //Cambia de color el fondo en los cajones de DSN
    function cambiar_color_td(este,patron, posicion)
    {
        //Si lo selecciona queda amarillo, si lo quita quedara con el color original
        if($(este).is(':checked')) {
            $("#chk"+patron+posicion).css('background-color', '#FAFC7C');
        } else {
            $("#chk"+patron+posicion).css('background-color', '');
            $("#chk"+patron+posicion).removeAttr('bgcolor');

        }
    }

    var regEx = /(^[0]\.{1}[1-9]+$)|(^[1-9]+\.{1}[1-9]+$)|(^[1-9]+$)|(^[0]$)/;

    function validar_cifra(elem)
    {

       var cantidad = $(elem).val();
       if ( regEx.test( cantidad ) && cantidad != '')
            {
                $(elem).removeClass("esError");
            }
        else
            {
                esok = false;
                $(elem).addClass("esError");
            }
    }

	function recuperar_dsn_nutri(wemp_pmla,wbasedato,whis,wing,wpatron_nutricion){


		$.ajax({
					url: "Dietas.php",
					type: "POST",
					data:{
						wemp_pmla			: wemp_pmla,
						consultaAjax 		: 'recuperar_dsn_nutri',
						operacion 			: '',
						wbasedato 			: wbasedato,
						whis				: whis,
						wing				: wing,
						wpatron_nutricion	: wpatron_nutricion,


					},
					dataType: "json",
					async: false,
					success:function(data_json) {

						if (data_json == 1)
						{

						}
						else{

							var array_datos_dsn = data_json.datos_dsn;

							for(num1 in array_datos_dsn){

								for(num2 in array_datos_dsn[num1]){

									$(".td-"+num1+"-"+array_datos_dsn[num1][num2].codigo).css('background-color', '#FAFC7C');
									$(".cajon-"+num1+"-"+array_datos_dsn[num1][num2].codigo).attr('checked', 'checked');
									$(".input-"+num1+"-"+array_datos_dsn[num1][num2].codigo).val(array_datos_dsn[num1][num2].cantidad);
									$(".obser-"+num1).val(array_datos_dsn[num1][num2].observacion);
								}

							}

						}
					}

				});

	}


    function cerraremergente_grabar(elem, f, patron, fila, columna, historia, ingreso, wemp_pmla, basedato, wser, usuario, habitacion, centro_costos, novalidahora, fecha)
    {

        //Verifica cuantos cajones estan seleccionados en un form
       var cuantos =  $( "#form" + patron+fila ).find('input:checkbox:checked').size();
       var id_click = "patron_grid"+fila+"-"+columna;

       //Si no selecciona ningun producto entonces deja el cajon sin seleccionar
       if (novalidahora == 'on')
           {

            var patron_asociado = $("#ptr_dsn_text").val(); //Patron asociado a la dieta segun nutricion, campo de texto.

            if(cuantos > 0)
                {
                    document.getElementById("patron_grid"+fila+"-"+columna).checked=true;
                    id = "cajon"+fila+"-"+columna;
                    cajon = document.getElementById(id);
                    cajon.style.backgroundColor="yellow";
                }
                else
                    {
                        document.getElementById("patron_grid"+fila+"-"+columna).checked=false;
                        id = "cajon"+fila+"-"+columna;
                        cajon = document.getElementById(id);
                        cajon.style.backgroundColor="";
                        $(cajon).removeAttr("bgcolor");
                    }
            //Si no tiene nada esrito en el patron asociado no permite cerrar la ventana modal.
            if (trim(patron_asociado) != '')
                {

                        var separador = '';
                        var string_guardar = '';
                        var string_observ = '';
                        var esok = true;

                        //Se capturan todos los cajones que estan seleccionados y sus id para que la informacion sea enviada por ajax
						$('table[id^=tabla_servicio]').find('input:checkbox:checked').each(function(){

                            var id_chk = $(this).attr("id");
                            var cantidad = ($(this).next("input").length > 0) ? $(this).next("input").val() : '0';

                            //Valida que las cantidades si esten correctas
                            if ( regEx.test( cantidad ) && cantidad != '')
                                {
                                    if($(this).next("input").length > 0)
                                    { $(this).next("input").removeClass("esError"); }
                                }
                            else
                                {
                                    esok = false;
                                    $(this).next("input").addClass("esError");
                                }

                            var valor_neto = $(this).attr("valor_neto");

                            //Esta validacion se da para la casilla de servicio igual
                            if(valor_neto != undefined)
                                {
                                string_guardar = string_guardar+separador+id_chk+'-'+cantidad+'-'+valor_neto;
                                }

                            separador = '*|*';
						});

                        separador = '';

                        $('table[id^=tabla_servicio]').find('textarea').each(function(){

                            var id_observac = $(this).attr("id");
                            var serv = id_observac.split("-");
                            var text_observac = $(this).val();
                            string_observ = string_observ+separador+serv[1]+'=>'+text_observac;
                            separador = '*|*';
						});

                        // --> Obtener cantidad de productos por servicio
                        var cant_product_servi = '';
                        $("[hidden_cant_pat=si]").each(function(){
                            servicio_cat_pro = $(this).attr('servicio');
                            cantidad_product = $(this).val();
                            if (cant_product_servi == '')
                                {
                                    cant_product_servi = servicio_cat_pro+'-'+cantidad_product;
                                }
                             else
                                 {
                                     cant_product_servi+= '|'+servicio_cat_pro+'-'+cantidad_product;
                                 }

                        });

                    //Valida que las cantidades si esten correctas
                    if(!esok) { alert("La cantidad o cantidades marcadas con rojo están erradas."); return; }

                    var prt_asociado = escape(patron_asociado);

                    var parametros = "consultaAjax=procesar_datos_dsn&wemp_pmla="+wemp_pmla+"&wbasedato="+basedato+"&winf_prod="+string_guardar+"&wpatron="+patron+"&whis="+historia+"&wing="+ingreso+"&wser="+wser+"&wusuario="+usuario+"&whab="+habitacion+"&wcco="+centro_costos+"&wfecha_interfaz="+fecha+"&wpatron_asociado="+prt_asociado+"&wobservacion="+string_observ+"&cant_product_servi="+cant_product_servi;

                    try
                    {
                        var ajax = nuevoAjax();
                        ajax.open("POST", "Dietas.php",true);
                        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        ajax.send(parametros);

                        ajax.onreadystatechange=function()
                        {

                            if (ajax.readyState==4  && ajax.status==200)
                            {

									 var x;
									 x = ajax.responseText;

                                     //Desmarca los cajones y quita el fondo amarillo cuando ingresa a dsn y ya hay registros en la 77 para ese servicio.
                                     if( x == 'desmarcar_patron')
                                         {
                                            //Desmarca los cajones y quita el fondo amarillo cuando ingresa a dsn y ya hay registros en la 77 para ese servicio.
                                            $(":checkbox[id^=patron_grid"+fila+"]:checked").each(function(){
                                            var id_chk = $(this).attr("id");
                                            if(id_chk != id_click)
                                            {
                                                $(this).removeAttr("checked");
                                                $(this).parent().parent().removeAttr("bgcolor");
                                            }
                                        });
                                         }

                                      if(x == 'desmarcar_patron_dsn')
                                         {
                                             //Se desmarca el patron DSN
                                            document.getElementById("patron_grid"+fila+"-"+columna).checked=false;
                                            id = "cajon"+fila+"-"+columna;
                                            cajon = document.getElementById(id);
                                            cajon.style.backgroundColor="";
                                            $(cajon).removeAttr("bgcolor");

                                         }

                                }
                            }
                        }catch(e){ alert(e) }

                    $.unblockUI();
					alert("Se ha guardado la DSN para el paciente");
					enter();
                }
              else
                  {
                      alert('Debe asignar patrón a la dieta.');
                      $("#ptr_dsn_text").focus();
                  }


           }
        else
            {
              //Verifica cuantos hay seleccionados, si es mas de uno marca la casilla, sino desmarca la casilla y guarda el cancelado segun sea el caso.
              if(cuantos > 0)
                {
                    document.getElementById("patron_grid"+fila+"-"+columna).checked=true;
                    id = "cajon"+fila+"-"+columna;
                    cajon = document.getElementById(id);
                    cajon.style.backgroundColor="yellow";

                }
                else
                    {
                        document.getElementById("patron_grid"+fila+"-"+columna).checked=false;
                        id = "cajon"+fila+"-"+columna;
                        cajon = document.getElementById(id);
                        cajon.style.backgroundColor="";

                        var cantidad = '0';  // En este caso la cantidad se vuelve cero, para que envie ese numero a la funcion procesar_datos_servind
                        var estado = '0';  // En este caso la cantidad se vuelve cero, para que envie ese numero a la funcion procesar_datos_servind
                        grabar_servindiv(wemp_pmla, basedato, '', fila, columna, patron, historia, ingreso, wser, '', usuario, habitacion, centro_costos, estado, '', '', 'on', '', fecha , '', '');
                    }

                $.unblockUI(); //cierra la ventana emergente.
                enter();//--> refresca la pantalla despues de grabar //-->2019-10-28


            }
    }

    function noenter(e)
        {

        e = e || window.event;
        var key = e.keyCode || e.charCode;
        return key !== 13;
        }

    function ponerMayusculas(dato)
        {
            dato.value=dato.value.toUpperCase();
        }

    function enter()
        {
       document.forms[0].submit();
        }


    var tick;

    function stop()
        {
        clearTimeout(tick);
        }

    function simple_reloj()
    {

        var ut=new Date();
        var h,m,s;
        var time=" ";
        h=ut.getHours();
        m=ut.getMinutes();
        s=ut.getSeconds();
        var ampm = h >= 12 ? 'pm' : 'am';
        if(h >= 12){ h = h - 12;}
        if(s<=9) s="0"+s;
        if(m<=9) m="0"+m;
        if(h<=9) h="0"+h;
        time+=h+":"+m+":"+s+" "+ampm;
        document.getElementById('reloj').innerHTML=time;
        tick=setTimeout("simple_reloj()",1000);
    }

	/************************************************************************************
	 * Actualiza los mensjaes sin leer cuando se actualiza la mensajeria
	 ************************************************************************************/
	function alActualizarMensajeria(){

		var campo = document.getElementById( "sinLeer" );
		campo.innerHTML = mensajeriaSinLeer;
	}

	/**********************************************************************
	 *
	 **********************************************************************/
	function enviandoMensaje(){

		if( document.getElementById('mensajeriaKardex').value != '' ){
			enviarMensaje( document.getElementById( 'mensajeriaKardex' ), document.getElementById( 'mensajeriaPrograma' ).value,document.forms.dietas.centro_costos.value, document.forms.dietas.servicio.value, document.getElementById( "usuario" ).value, document.getElementById( "wbasedato" ).value );

		}
	}

	/**********************************************************************
	 *
	 **********************************************************************/
	function marcarLeido( campo, id ){

		//campo es una tabla que tiene toda la informacion que se muestra
		//Con dos fila
		//La primera fila tiene dos celdas y la segunda 1

	marcandoLeido( document.getElementById( "wbasedato" ).value, id, document.getElementById( "usuario" ).value );

	$('#mensajesdietas tr[id^=fila_'+id+']').find(".blink").each(function(){

		$(this).stop(true);
		$(this).removeClass('blink');

		});
	}

	/************************************************************************************************
	 *
	 * campo
	 ************************************************************************************************/
	function marcarPrioridad( campo ){

		//celda en la que se encuentra el boton de guardar
		var celda = 0;

		//Campo es el checkbox de prioridad
		fila = campo.parentNode.parentNode;	//Busco la fila en la que se encuentra el checkbox

		eval( fila.cells[ celda ].firstChild.href );	//Click en boton guardar
	}
	/************************************************************************************************/

	/****************************************************************************************************
	 *
	 ****************************************************************************************************/
	function mostrarMensajeConfirmarKardex(){
		return;	//Septiembre 19 de 2011, Se deshabilita mostrar el mensaje para, esto por que viene tal cual el dia anterior
		var msjConfirmarKardex = document.getElementById( 'mostrarConfirmarKardex' );

		if(  msjConfirmarKardex && msjConfirmarKardex.value == 'on' ){
			//$.( '#txConfKar' ).blink();
			$.blockUI({ message: $('#msjConfirmarKardex') });
		}
	}

	/*****************************************************************************************************************************
 * Inicializa jquery
 ******************************************************************************************************************************/
    function inicializarJquery(){


        if (browser=="Microsoft Internet Explorer"){
            setInterval( "parpadear()", 500 );
        }

        mostrarMensajeConfirmarKardex();	//Agosto 25 de 2011

        mensajeriaActualizarSinLeer = alActualizarMensajeria;

        consultarHistoricoTextoProcesado( document.getElementById( "wbasedato" ).value, document.getElementById( "wemp_pmla" ).value, document.forms.dietas.centro_costos.value, document.forms.dietas.servicio.value, document.getElementById( 'mensajeriaPrograma' ).value, document.getElementById( 'historicoMensajeria' ) );	//Octubre 11 de 2011

       // mensajeriaTiempoRecarga = consultasAjax( "POST", "../../../include/movhos/mensajeriaDietas.php", "consultaAjax=4&wemp="+document.getElementById( "wemp_pmla" ).value, false );
        mensajeriaTiempoRecarga = document.getElementById( "wtiempo_rec_msg" ).value;
        mensajeriaTiempoRecarga = mensajeriaTiempoRecarga*60000;	//El tiempo que se consulta esta en minutos

        setInterval( "mensajeriaActualizar()", mensajeriaTiempoRecarga );
    }


	/*********************************************************************************************************/



	//Funcion que permite deseleccionar todos los checkbox
	function deseleccionar_todo(patronfila)
		{

			checkboxes = document.getElementById(patronfila).getElementsByTagName( 'input' ); //Array que contiene los checkbox

			for (var x=0; x < checkboxes.length; x++)
            {

			if (checkboxes[x].type == "checkbox")checkboxes[x].checked=0;


				}


		}

	//Vuelve a poner la pagina en el ultimo lugar antes de ser recargada
    window.onload=function(){
    var pos=window.name || 0;
    window.scrollTo(0,pos);

	setInterval(function() {
     
		$('.blink').effect("pulsate", {}, 5000);

	}, 1000);

    }
    window.onunload=function(){
    window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
    }

    //Marca la historia de urgencias que tiene mas de 3 días para hacerle pedidos de alimentacion.
    function funcionhistoriaurgencias(wemp_pmla, basedato, centro_costos)
    {

      var historia = $("#idhistoria").val();
      var ingreso = $("#idingreso").val();

      if(historia == '')// || ingreso == ''
      {
          alert("Escribir la historia");
          return;
      }

      var parametros = "consultaAjax=funcionhistoriaurgencias&wemp_pmla="+wemp_pmla+"&wbasedato="+basedato+"&whis="+historia+"&wing="+ingreso+"&wcco="+centro_costos;


      try
      {
          var ajax = nuevoAjax();
          ajax.open("POST", "Dietas.php",false);
          ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          ajax.send(parametros);
          //alert(ajax.responseText);
          document.forms[0].submit();
      }catch(e){ alert(e) }
    }

	//Funcion para grabar los datos del un servicio individual$wemp_pmla, $wbasedato, $whis, $wing, $whab, $wser, $wtexto
	function grabar_observ_intoler(wemp_pmla, basedato, historia, ingreso, habitacion, servicio, i, j, usuario, obsint, centro_costos, fecha_interfaz)
		{

            if(obsint == 'o')
				var texto = escape(document.getElementById("wobs_"+i+j).value);
			if(obsint == 'i')
				var texto = escape(document.getElementById("wint_"+i+j).value);

			var parametros = "consultaAjax=grabar_observ_intoler&wemp_pmla="+wemp_pmla+"&wbasedato="+basedato+"&whis="+historia+"&wing="+ingreso+"&whab="+habitacion+"&wser="+servicio+"&wtexto="+texto+"&wusuario="+usuario+"&wobsint="+obsint+"&wcco="+centro_costos+"&wfec="+fecha_interfaz;


		  try
		  {
		    var ajax = nuevoAjax();
			ajax.open("POST", "Dietas.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);
			//alert(ajax.responseText);

			if( ajax.responseText == 1 ){
				alert( "Ya existe un servicio posterior. Las observaciones no se reflejarán en los servicios ya creados" );
			}

			}catch(e){ alert(e) }
		}

    //Funcion para grabar los datos del un servicio individual
	function grabar_dsn(wemp_pmla, basedato, codigo, fila, columna, patron, historia, ingreso, servicio, valor_neto, usuario, habitacion, centro_costos, estado, este, posicioninput, cancelo, sercla, campo_texto, fecha_registro, serdsn, fecha_actual, posicion, controldia)
		{


        var checkbox = $("#cajon-"+patron+"-"+codigo+"-"+serdsn).attr("checked"); //Verfico en que estado se encuentra el cajon
        var cantidad = $("#patron"+patron+posicion).val(); //Cantidad de productos
        var patron_asociado = $("#ptr_dsn_text").val(); //Patron asociado a la dieta segun nutricion

        if (campo_texto != 'off' )
            {

                if(cantidad == 0)
                    {
                        alert ('La cantidad de productos debe ser mayor a cero.');
                        return;
                    }

                //Si el cajon no esta seleccionado pero escribe cantidad diferente a uno entonces seleccionara el cajon, si esta seleccionado envia el nuevo dato.
                if (checkbox != false)
                    {

                    var cantidad_prod = $("#patron"+patron+posicion).val();
                    var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                    var media_porcion = document.getElementById("media_porcion"+fila.toString()+"-"+dato_media_porcion).disabled;

                    if (media_porcion == true)
                        {
                        var media_porcion = document.getElementById("media_porcion"+fila.toString()+"-"+dato_media_porcion).disabled=true;

                        }
                        else
                            {
                            var media_porcion = document.getElementById("media_porcion"+fila.toString()+"-"+dato_media_porcion).disabled=false;

                            }

                    var parametros = "consultaAjax=procesar_datos_dsn&wemp_pmla="+wemp_pmla+"&wbasedato="+basedato+"&wcodigo="+codigo+"&wpatron="+patron+"&whis="+historia+"&wing="+ingreso+"&wser="+servicio+"&wvalorneto="+valor_neto+"&wusuario="+usuario+"&whab="+habitacion+"&wcco="+centro_costos+"&westado="+estado+"&wcantidad="+cantidad_prod+"&wsercla="+sercla+"&wguardabitacora=on&wcancelo="+cancelo+"&wfecha_registro="+fecha_registro+"&wserdsn="+serdsn+"&wfecha_actual="+fecha_actual+"&wpatron_asociado="+patron_asociado;
                    }
            }
            else
                {

                 if (serdsn >= servicio)
                     {

                         if (controldia != 'on')
                            {
                                $("#chk"+patron+posicion).css('background-color', 'DeepSkyBlue');
                            }
                            else
                                {
                                   $("#chk"+patron+posicion).css('background-color', 'Silver');
                                }
                     }
                     else
                         {
                           $("#chk"+patron+posicion).css('background-color', 'Silver');
                         }

                         if($("#chk"+patron+posicion).css('background-color') == 'rgb(192, 192, 192)')
                             {
                                //Se crea esta variable para que el control del dia de solicitud se pueda manejar.
                                var controldiainterfaz = 'on';
                             }

                //Captura la cantidad de productos
                var cantidad_prod = $("#patron"+patron+posicion).val();
                var parametros = "consultaAjax=procesar_datos_dsn&wemp_pmla="+wemp_pmla+"&wbasedato="+basedato+"&wcodigo="+codigo+"&wpatron="+patron+"&whis="+historia+"&wing="+ingreso+"&wser="+servicio+"&wvalorneto="+valor_neto+"&wusuario="+usuario+"&whab="+habitacion+"&wcco="+centro_costos+"&westado="+estado+"&wcantidad="+cantidad_prod+"&wsercla="+sercla+"&wguardabitacora=on&wcancelo="+cancelo+"&wfecha_registro="+fecha_registro+"&wserdsn="+serdsn+"&wfecha_actual="+fecha_actual+"&wpatron_asociado="+patron_asociado+"&controldiainterfaz="+controldiainterfaz;
                }


		  try
		  {
		    var ajax = nuevoAjax();
			ajax.open("POST", "Dietas.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

                   // console.log(ajax.responseText);
                    if (ajax.readyState==4  && ajax.status==200)
                        {

                        var x;
                        x = ajax.responseText;

                        if (x == 1)
                            {
                             este.checked=false;
                             alert('El producto seleccionado no tiene costo en la tabla 000082, para el Patrón: '+patron+' ** en la historia: ** '+historia+' **')
                             return;
                            }

                          if (x == 12)
                            {
                            alert('El producto no se puede desactivar porque no pertenece al servicio actual o esta registrado en un dia posterior.');
                            este.checked=true;
                            return false;
                            }


                        if(x == 'noinactiva')
                            {
                             alert ('El producto ya fue enviado y no es posible inactivarlo.');
                             este.checked=true;
                             return;
                            }

                         if (x == 'eliminado')
                             {
                                 $("#chk"+patron+posicion).css('background-color', '#CCFFFF');

                                 return;
                             }
                        }


			}catch(e){ alert(e) }
		}



	//Funcion para grabar los datos del un servicio individual
	function grabar_servindiv(wemp_pmla, basedato, codigo, fila, columna, patron, historia, ingreso, servicio, valor_neto, usuario, habitacion, centro_costos, estado, este, posicioninput, cancelo, campo_texto, fecha_interfaz, clasificacion, posicion)
		{

            if (cancelo != 'on')
                {

                var checkbox = $("#cajon"+patron+posicion).attr("checked"); //Verfico en que estado se encuentra el cajon
                var cantidad = este.parentNode.getElementsByTagName('input')[1].value;

                if (campo_texto != 'off' )
                    {

                        if(cantidad == 0)
                            {
                             alert ('La cantidad de productos debe ser mayor a cero.');
                             return;
                            }

                        if (checkbox != false)
                            {

                            $("#chk"+patron+posicion).css('background-color', 'DeepSkyBlue');

                            var cantidad_prod = $("#input"+patron+codigo).val();
                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                            var media_porcion = document.getElementById("media_porcion"+fila.toString()+"-"+dato_media_porcion).disabled;

                            if (media_porcion == true)
                                {
                                var media_porcion = document.getElementById("media_porcion"+fila.toString()+"-"+dato_media_porcion).disabled=true;

                                }
                                else
                                    {
                                    var media_porcion = document.getElementById("media_porcion"+fila.toString()+"-"+dato_media_porcion).disabled=false;

                                    }

                            var parametros = "consultaAjax=procesar_datos_servind&wemp_pmla="+wemp_pmla+"&wbasedato="+basedato+"&wcodigo="+codigo+"&wpatron="+patron+"&whis="+historia+"&wing="+ingreso+"&wser="+servicio+"&wvalorneto="+valor_neto+"&wusuario="+usuario+"&whab="+habitacion+"&wcco="+centro_costos+"&westado="+estado+"&wcantidad="+cantidad_prod+"&wfecha_interfaz="+fecha_interfaz+"&wclasificacion="+clasificacion;

                            }
                            else
                                {
                                 alert ('Debe seleccionar el producto para que sea grabado.');
                                 return;
                                }
                    }
                    else
                      {
                       $("#chk"+patron+posicion).css('background-color', 'DeepSkyBlue');
                       var cantidad_prod = $("#input"+patron+codigo).val();
                       var parametros = "consultaAjax=procesar_datos_servind&wemp_pmla="+wemp_pmla+"&wbasedato="+basedato+"&wcodigo="+codigo+"&wpatron="+patron+"&whis="+historia+"&wing="+ingreso+"&wser="+servicio+"&wvalorneto="+valor_neto+"&wusuario="+usuario+"&whab="+habitacion+"&wcco="+centro_costos+"&westado="+estado+"&wcantidad="+cantidad_prod+"&wfecha_interfaz="+fecha_interfaz+"&wclasificacion="+clasificacion;
                      }

                 }
              else
                  {
                  cantidad_prod = '0';  // En este caso la cantidad se vuelve cero, para que envie ese numero a la funcion procesar_datos_servind
                  var parametros = "consultaAjax=procesar_datos_servind&wemp_pmla="+wemp_pmla+"&wbasedato="+basedato+"&wcodigo="+codigo+"&wpatron="+patron+"&whis="+historia+"&wing="+ingreso+"&wser="+servicio+"&wvalorneto="+valor_neto+"&wusuario="+usuario+"&whab="+habitacion+"&wcco="+centro_costos+"&westado="+estado+"&wcantidad="+cantidad_prod+"&wfecha_interfaz="+fecha_interfaz+"&wclasificacion="+clasificacion;
                  //console.log(parametros);
                  }


		  try
		  {
		    var ajax = nuevoAjax();
			ajax.open("POST", "Dietas.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);


                    if (ajax.readyState==4  && ajax.status==200)
                        {

                        var x;
                        x = ajax.responseText;

                        if (x == 1)
                            {
                             este.checked=false;
                             alert('El producto seleccionado no tiene costo en la tabla 000082, para el Patrón: '+patron+' ** en la historia: ** '+historia+' **')
                            }

                         if (x == 'inactivo')
                            {
                                //Vuelve el td al color inicial #CCFFFF
                               $("#chk"+patron+posicion).css('background-color', '#CCFFFF');
                            }
                        }


			}catch(e){ alert(e) }
		}


        function confirmar_cancelacion(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, patron_combinable, usuario, modificar, chequeados_final, combinables, media_porcion, control_pos_quirur, wrol_usuario, wpatron_nutricion, wrolnutricion, windicador)
        {
                cajon.bgColor="yellow";
                document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=true;

				// $( "#result" ).html( "That div is <span style='color:" +color + ";'>" + color + "</span>." );

                if (windicador != 'dsn')
                    {
                     confirmar=confirm("¿Esta seguro de realizar esta operación, cancelará el servicio para el paciente y no podrá seleccionar otro patrón o patrones?");
                    }
                    else
                        {
                        confirmar=confirm("                                                                               ! ALERTA ¡ \n ¿Esta seguro de desea cancelar el patron "+patron+", se cancelará la DSN para el paciente.?");
                        }

                if (confirmar)
                    {
                    grabar_datos_sinUI(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, patron_combinable, usuario, modificar, chequeados_final, combinables, media_porcion, control_pos_quirur, wrol_usuario, wpatron_nutricion, wrolnutricion, 'on');
                    enter();
                    }
                    else
                        {
                         cajon.bgColor="yellow";
                        }


        }

     function confirmar_solic_dsn(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, patron_combinable, usuario, modificar, chequeados_final, combinables, media_porcion, control_pos_quirur, wrol_usuario, wpatron_nutricion, wrolnutricion, mensaje)
        {
                cajon.bgColor="yellow";
                document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=true;

                confirmar=confirm(mensaje);

                if (confirmar)
                    {
                    activarModal(wemp_pmla, patron,f,historia, ingreso, servicio, f, c, habitacion, fecha, centro_costos, usuario, nom_pac);
                    }
                    else
                        {
                            cajon.style.backgroundColor="";
                            cajon.bgColor="";
                            document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                        }


        }

	//Funcion para grabar la dieta al seleccionar un checkbox //24 Abril 2012
	function grabar_datos(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, patron_combinable, usuario, modificar, chequeados_final, combinables, media_porcion, control_pos_quirur, wrol_usuario, wpatron_nutricion, wrolnutricion, codDSN, campInput)
		{

          //Verfico en que estado se encuentra el cajon
          var checkbox = $("#patron_grid"+f+"-"+c).is(":checked");

		  //Esta validacion permite identificar si estan deseleccionando el cajon de DSN por parte de enfermeria, si esta activo el cajon
		  //mostrará un mensaje de confirmación para la cancelación del servicio.
          if(checkbox == true)
              {
               var estado = 'on';
              }
             else
                 {
                  var estado = 'off';
                 }

         //**********************************************

          var parametros = "consultaAjax=procesar_datos&wemp_pmla="+wemp_pmla+"&whis="+historia+"&wing="+ingreso+"&wpatron="+patron+"&wcco="+centro_costos+"&wser="+servicio+"&wfec="+fecha+"&whab="+habitacion+"&wpac="+nom_pac+"&wdpa="+doc_pac+"&wtid="+tipo_doc+"&wptr="+proc_trasl+"&wmue="+muerte+"&wedad="+edad+"&walp="+alta_proc+"&wtem="+tipo_empresa+"&west="+dias_estancia+"&wusuario="+usuario+"&wmodificar="+modificar+"&wchequeados="+chequeados_final+"&wcombinables="+combinables+"&wpcomb="+patron_combinable+"&wmedia_porcion="+media_porcion+"&wcontrolposqui="+control_pos_quirur+"&wrol_usuario="+wrol_usuario+"&wpatron_nutricion="+wpatron_nutricion+"&wrolnutricion="+wrolnutricion+"&wseleccionado="+estado+"&codDSN="+codDSN;


		  try
		  {

                try {
					$.blockUI(
						{
							message: $('#msjEspere')
						});
					} catch(e){ }
					var ajax = nuevoAjax();
					ajax.open("POST", "Dietas.php",true);
					ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					ajax.send(parametros);

                    ajax.onreadystatechange=function()
                    {
                            if (ajax.readyState==4  && ajax.status==200)
                            {
                                    // Respuesta ajax para validar algunas acciones, entre ellas la combinacion de patrones, el horario de adicion o cancelacion
									 var x;
									 x = ajax.responseText;
									//console.log(x);
									if (x == 1 || x == 8 || x == 11 || x == 114 )
										{
										 document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
										 id = "cajon"+f+"-"+c;
										 cajon = document.getElementById(id);
										 cajon.style.backgroundColor="";
                                         var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                         var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
										 alert('El Patrón: **'+ patron +'** No tiene costo en la tabla 000079, para el tipo de empresa: ** '+ tipo_empresa +' ** en la historia: ** '+ historia+' **, o la edad del paciente es menor a 6 meses.');
										 }

									 if (x == 2)
										{
										 cajon.style.backgroundColor="";
                                         document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                         var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                         var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
										 alert('El Patrón: **'+ patron +'** no se puede combinar con ningún otro');
										 }


                                    if (x == 3 || x == 73)
										{
										 id = "cajon"+f+"-"+c;
										 cajon = document.getElementById(id);
										 cajon.style.backgroundColor=color_esq_actual;
										 document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=true;
                                         var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                         var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
										 alert('El servicio de la historia seleccionada no puede ser adicionado o modificado porque esta fuera del horario. Favor revisar el inicio de horario de adición o el horario final del pedido en la parte superior de la pantalla.');
										 }


                                    //Este dato resulta de evaluar el arreglo del patron con sus combinaciones, en caso de devolver un 4 emitira un alerta en la que se muestra
                                    //que no se puede combinar, ademas detendra el script php.
									 if (x == 4 || x == 44 || x == 441 )
										{
										 cajon.style.backgroundColor="";
										 document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
										 alert('El Patrón: **'+ patron +'** no se puede combinar con los patrones seleccionados.');
										 }

                                     if (x == 421 )
										{
										 cajon.style.backgroundColor="";
										 document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
										 alert('El patrón principal de los patrones seleccionados no tiene costo en el servicio actual.');
										 }


                                    if (x == 491 || x == 41 )
										{
										 cajon.style.backgroundColor="";
										 document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
										 alert('El Patrón: **'+ patron +'** no se puede combinar con los patrones seleccionados.');
										 }

                                    if (x == 5)
										{
										 id = "cajon"+f+"-"+c;
										 cajon = document.getElementById(id);
										 cajon.style.backgroundColor=color_esq_actual;
										 document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=true;
                                         var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                         var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
										 alert('El servicio ya fue enviado y no es posible cancelarlo.');
										 }


                                      //Mensaje cuando se intenta seleccionar un patron en horario que aun no es de adicion.
                                      if (x == 6)
                                            {
                                            cajon.style.backgroundColor="";
                                            document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                            var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                                            alert('El horario de solicitd de adiciones no ha iniciado, favor revisar los horarios en la parte superior de la pantalla.');
                                            }

										//Mensaje cuando se intenta seleccionar un patron en horario que aun no es de adicion.
                                      if (x == 66)
                                            {
                                            cajon.style.backgroundColor="";
                                            document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                            var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                                            alert('El horario de solicitd de adiciones no ha iniciado, favor revisar los horarios en la parte superior de la pantalla.');
                                            }
                                       //Mensaje cuando se intenta seleccionar un patron no combinable en horario que aun no es de adicion,
                                       //al seleccionar SI o TMO el ajax recibe el codigo numero 7, este numero 7 es evaluado por la funcion grabar datos
                                       //y si x==7 entonces abre la ventana modal, al realizar esta accion tambien se activa la funcion que analiza si se
                                       //pueden realizar adiciones a esa hora y como no es posible entonces el script responde al ajax el codigo numero 6, el cual
                                       //muestra un mensaje diciendo que no es posible solicitar en este horario, por lo tanto al unir el 7 de la modal y el 6
                                       //del mensaje de no solicitud de adicion entonces se deje generar el mensaje de que no se puede solicitar servicio individual
                                       //en ese horario.
                                       if (x == 76)
                                            {
                                            cajon.style.backgroundColor="";
                                            document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                            var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                                            alert('No es posible ingresar a **'+ patron +'** ya que no ha iniciado el horario de adición, favor revisar los horarios en la parte superior de la pantalla.');
                                            }


                                      //Este dato resulta de validar si el patorn que se ha seleccoinado es un patron no combinable, por lo tanto despliega los productos que lo contienen
                                      //entre ellos estan SI, TMO, DSN, el 79 es una combinacion entre mostrar la ventana modal y la validacion de que no puede cancelar el servicio porque ya fue enviado
									  //en este caso es necesario que abra la ventana modal.
                                      if (x == 7 || x == 77 || x == 79)
                                            {
                                            activarModal(wemp_pmla, patron,f,historia, ingreso, servicio, f, c, habitacion, fecha, centro_costos, usuario, nom_pac)
                                            return false;
                                            }

                                       //Este dato lo devuelve la funcion procesar_datos cuando se ha seleccionado un patron no combinable, e intenta selecccionar otro patron combinable para
                                       //la misma historia e ingreso, combina la respuesta de la validacion anterior (x == 7), la cual se refiere al primer patron combinable que se selecciono y
                                       //el (x == 4), el cual se refiere a que no se puede combinar con ningun otro patron.
                                       if (x == 74)
                                            {

                                            cajon.style.backgroundColor="";
                                            document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                            var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                                            alert('El Patrón: **'+ patron +'** no se puede combinar con ningún otro');
                                            return false;
                                            }

                                        //Este dato resulta de la combinacion de validar que el patron no tiene costo en la tabla 79 de movhos para la empresa, historia e ingreso,
                                        //ademas que no se puede combinar con ningun otro patron.
                                       if (x == 14)
                                            {

                                            cajon.style.backgroundColor="";
                                            document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                            var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                                            alert('El Patrón: **'+ patron +'** no se puede combinar con ningún otro');
                                            return false;
                                            }


                                       if (x == 9)
                                            {
                                            //Esta funcion muestra una ventana alertandole al usuario que si selecciona aceptar, se eliminara el servicio para el paciente.
                                            confirmar_cancelacion(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, '', usuario, '', '', '', '', '','', '', '', '');

                                            }

                                       if (x == 'cancelardsn')
                                            {
                                            //Esta funcion muestra una ventana alertandole al usuario que si selecciona aceptar, se eliminara el servicio para el paciente.
                                            confirmar_cancelacion(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, '', usuario, '', '', '', '', '','', '', '', 'dsn');

                                            }

                                        if (x == 91)
                                            {

                                           // confirmar_cancelacion(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, patron_combinable);
                                            cajon.style.backgroundColor="";
                                            document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false ;
                                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                            var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                                            alert('No es posible modificar o solicitar alimentación para el paciente ya que ha pasado el horario normal de pedido o ha cancelado la solicitud tres veces en el horario de adición.');
                                            return false;
                                            }

                                          if (x == 92)
                                            {

                                           // confirmar_cancelacion(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, patron_combinable);
                                            cajon.style.backgroundColor="";
                                            document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false ;
                                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                            var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                                            alert('Canceló el pedido en horario de adicion, solo podrá solicitar servicios individuales o un patrón desde el siguiente servicio.');
                                            return false;
                                            }


                                            if (x == 10)
                                            {
    //										cajon.style.backgroundColor="";
    //										document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                            var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                                            alert('El Patrón: **'+ patron +'** No tiene costo en la tabla 000079, para el tipo de empresa: ** '+ tipo_empresa +' ** en la historia: ** '+ historia+' **, o la edad del paciente es menor a 6 meses.');
                                            return false;
                                            }

                                        if (x == 1020)
                                        {
    //										cajon.style.backgroundColor="";
    //										document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                            var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                                            alert('El Patrón: **'+ patron +'** la edad del paciente es menor a 6 meses.');
                                            return false;
                                        }


                                        if (x == 101)
                                            {
                                            cajon.style.backgroundColor="";
                                            document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                            var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                                            alert('El Patrón: **'+ patron +'** No tiene costo en la tabla 000079, para el tipo de empresa: ** '+ tipo_empresa +' ** en la historia: ** '+ historia+' **, o la edad del paciente es menor a 6 meses.');
                                            return false;
                                            }

                                        if (x == 102)
                                            {
                                            cajon.style.backgroundColor="Yellow";
                                            document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=true;
                                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                            var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                                            alert('Los patrones restantes no se pueden combinar');
                                            return false;
                                            }

                                        if (x == 109)// Este caso se da por la combinacion del resultado de las funciones traer_costo_del_patron_asociado y traer_costo_del_patron, ya que no hay costo para el servicio actual ni para el asociado.
                                            {
                                            cajon.style.backgroundColor="";
                                            document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                            var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                                            var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                                            alert('El patrón no tiene costo para el servicio actual, ni para el servicio asociado.');
                                            return false;
                                            }

                                          if (x == 30)// Este caso se da cuando el usuario intenta seleccionar otro patron que acompañe a un postquirurgico, si no selecciona el cajon postquirurgico primero, se mostrara este mensaje.
                                            {

                                            if(document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked == true)
                                                {
                                                 cajon.style.backgroundColor="";
                                                 document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                                }
                                                else
                                                    {
                                                    cajon.style.backgroundColor="yellow";
                                                    document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=true;
                                                    }

                                            alert('No es posible hacer la combinación ya que no ha seleccionado el paciente como postquirúrgico, favor seleccione el cajón de postquirúrgico y luego seleccione otro patrón o patrones.');
                                            return false;
                                            }

                                           if (x == 31)// Este caso se da por la combinacion del resultado de las funciones traer_costo_del_patron_asociado y traer_costo_del_patron, ya que no hay costo para el servicio actual ni para el asociado.
                                            {

                                            cajon.style.backgroundColor="yellow";
                                            document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=true;

                                            alert('No es posible deseleccionar el patrón ya que no ha deseleccionado el paciente de postquirúrgico.');
                                            return false;
                                            }
                                            //Este caso se da cuando un usuario que no es nutricionista quiere ingresar a DSN.
                                            if (x == 'seleccionardsn')
                                                {

                                                    document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                                    cajon.style.backgroundColor="";
                                                    alert('Su rol no esta autorizado para seleccionar patrón.');
                                                    return false;
                                                }

											//Si una nutricionista marca SI le mostrara este mensaje.
											if (x == 'seleccionarSI')
                                                {

                                                    document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                                    cajon.style.backgroundColor="";
                                                    alert('Su rol no esta autorizado para seleccionar patrón.');
                                                    return false;
                                                }

                                                //Este caso se da cuando un usuario que es nutricionista quiere ingresar a un patron diferente de DSN.
                                            if (x == 'seleccionapatronnodsn_new')
                                                {

                                                    document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                                    cajon.style.backgroundColor="";
                                                    alert('Su rol no esta autorizado para seleccionar este patrón.');
                                                    return false;
                                                }

                                             //Este caso se da cuando un usuario que es nutricionista quiere ingresar a un patron diferente de DSN.
                                             //y el paciente ya tiene patron o patrones seleccionados.
                                            if (x == 'seleccionapatronnodsn_update')
                                                {
                                                    cajon.style.backgroundColor="yellow";
                                                    document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=true;
                                                    alert('Su rol no esta autorizado para seleccionar este patrón.');
                                                    return false;
                                                }

											//Esta validacion se da cuando al paciente no se le pidio DSN por parte de la nutricionista
											//y la enfermera intenta pedir un patron diferente a DSN
											var l = x.split('|'); //Separo el texto que se devuelve por asterisco.
											if (l[0] == 'noDSN')
                                                {
                                                    cajon.style.backgroundColor="";
                                                    document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
                                                    alert(l[1]);
                                                    return false;
                                                }

											//Alerta que muestra la inactivacion de DSN desde el servicio actual en adelante.
											var l = x.split('|'); //Separo el texto que se devuelve por asterisco.
											if (l[1] == 'inactivarDSN')
                                                {
                                                    cajon.style.backgroundColor="yellow";
                                                    alert(l[2]);
                                                    return false;
                                                }



											 var j = x.split('*'); //Separo el texto que se devuelve por asterisco.
											 var k = j[0].split('-'); //Separo la primera posicion que contiene los servicios que no tienen pedido DSN.
											 var servicio_sin_dsn = k.length-1; //Resto uno para que no tenga en cuenta la ultima posicion dle arreglo.
											 var servicios_total = $("#cantidad_servicios").val(); //Traigo la cantidad actual de servicios.

											 //Si la cantidad de servicios es igual a la cantidad de servicios no pedidos muestra este mensaje.
                                             if (servicio_sin_dsn == servicios_total)
                                                {

													if(j[1] == 'nohaydsn'){
														cajon.style.backgroundColor="";
														document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
														alert('El paciente no ha tenido pedidos de Dieta Según Nutrición en días anteriores para este servicio, favor comunicarse con la nutricionista.');
														return false;
													}

                                                }else{

													if(j[1] == 'nohaydsn'){

														alert('                                                                    **** ALERTA **** \n  Se reprogramó la última DSN de este paciente, favor revisar el icono de información.');
														//Si dentro del arreglo de servicios recuparados no esta el servicio actual seleccionado desmarca el cajon.
														if(k.indexOf(servicio) == 0){
															cajon.style.backgroundColor="";
															document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
														}

														enter();
														return false;
													}
												}


                                              if (x == 'alerta_nutricionista')
                                                {
													var mensaje = "¿Esta seguro que desea ingresar al patrón "+patron+"?, cancelará el patrón actual del paciente si solicita productos para este servicio.";
                                                    confirmar_solic_dsn(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, patron_combinable, usuario, modificar, chequeados_final, combinables, media_porcion, control_pos_quirur, wrol_usuario, wpatron_nutricion, wrolnutricion, mensaje)
                                                    return false;
                                                }

											  if (x == 'alerta_nutricionista_adicion')
                                                {
													var mensaje = "¿Esta seguro que desea ingresar al patrón "+patron+"?";
                                                    confirmar_solic_dsn(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, patron_combinable, usuario, modificar, chequeados_final, combinables, media_porcion, control_pos_quirur, wrol_usuario, wpatron_nutricion, wrolnutricion, mensaje)
                                                    return false;
                                                }

												// $wrespuesta = "no_puede_pedir_dsn";
												// $wmensaje = "Este patron ha sido cancelado por ".$wnombre." el dia de ayer, favor comunicarse con la nutricionista.";

											   var mensaje_dsn_cancelado = x.split("|");

											   if(mensaje_dsn_cancelado[0] == 'no_puede_pedir_dsn'){

												document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
												cajon.style.backgroundColor="";
												alert(mensaje_dsn_cancelado[1]);

											   }

									/**
									 * Se busca si la fila tiene algun patron activo
									 * Si la fila tiene algún patrón activo no se deje editar el campo observaciones
									 */

									// $( ".fila1, .fila2" ).click(function(){
										var hayPatronSeleccionado = $( "input[id^=patron]:checkbox:checked", $( campInput ).parent().parent().parent() ).length > 0;

										if( !hayPatronSeleccionado ){
											$( "[id^=wobs_],[id^=wint_]", $( campInput ).parent().parent().parent() ).attr({disabled:true})
										}
										else{
											$( "[id^=wobs_],[id^=wint_]", $( campInput ).parent().parent().parent() ).filter("[disabled_origen!=true]").attr({disabled:false})
										}
									// });


                            }
                            try {
                                    $.unblockUI();
                                } catch(e){ }
                    }


                  }catch(e){	}
		}

	 //Funcion para grabar la dieta al seleccionar un checkbox en caso de ser patron unico pero en hora de adicion  //2 Mayo de 2012
	function grabar_datos_sinUI(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, patron_combinable, usuario, modificar, chequeados_final, combinables, media_porcion, control_pos_quirur, wrol_usuario, wpatron_nutricion, wrolnutricion, wind_cancela_dsn)
		{

          var parametros = "consultaAjax=procesar_datos&wemp_pmla="+wemp_pmla+"&whis="+historia+"&wing="+ingreso+"&wpatron="+patron+"&wcco="+centro_costos+"&wser="+servicio+"&wfec="+fecha+"&whab="+habitacion+"&wpac="+nom_pac+"&wdpa="+doc_pac+"&wtid="+tipo_doc+"&wptr="+proc_trasl+"&wmue="+muerte+"&wedad="+edad+"&walp="+alta_proc+"&wtem="+tipo_empresa+"&west="+dias_estancia+"&wusuario="+usuario+"&wmodificar="+modificar+"&wchequeados="+chequeados_final+"&wcombinables="+combinables+"&wpcomb="+patron_combinable+"&wmedia_porcion="+media_porcion+"&wcontrolposqui="+control_pos_quirur+"&wrol_usuario="+wrol_usuario+"&wpatron_nutricion="+wpatron_nutricion+"&wrolnutricion="+wrolnutricion+"&wconfirmar_canceladsn="+wind_cancela_dsn;

		  try
		  {
		    var ajax = nuevoAjax();
			ajax.open("POST", "Dietas.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			var x = ajax.responseText;

			//La funcion procesar datos devuelve este texto el cual se genero a partir la validacion de la hora de cancelacion del serivico,
			//como estaba fuera de esta hora no puede cancelarlo.
			//(el numero 10 al inicio es porque en esa funcion ingresa primero a la funcion traer_costo_del_patron y retorna ese numero, la funcion continuar
			//ejecutandose y al final)
			if (x == '10nocancelardsn')
				{
				 id = "cajon"+f+"-"+c;
				 cajon = document.getElementById(id);
				 cajon.style.backgroundColor=color_esq_actual;
				 document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=true;
				 var dato_media_porcion = document.getElementById("dato_media_porcion").value;
				 var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
				 alert('El servicio ya fue enviado y no es posible cancelarlo.');
				 return;
				 }
			}catch(e){ alert(e) }
		}


	function cambiar_color(c,f)
	  {

	  var cajon;
	  var id;

	  id = "cajon"+f+"-"+c;
	  cajon = document.getElementById(id);
	  cajon.style.backgroundColor = color_esq_actual;

	  }

    function respuestaUnblock(idElemento, arreglo)
    {

		var temporal="";
		var temporal2="";
		var mensaje="";
		var valores = "";  //Acumulo los values de los checkboxes

		var acumulador = "";

		var setter = new Array();
		var cont1 = 0;

		var elemento = document.getElementById("sel"+idElemento);
        elemento.value = "";

		while(document.getElementById("chk"+idElemento+cont1.toString())){
			temporal2 = document.getElementById("chk"+idElemento+cont1.toString()).checked;
			temporal = arreglo[cont1];

			mensaje += temporal2 ? " on" : " off";

			document.getElementById("chk"+idElemento+cont1.toString()).checked = temporal;

			acumulador += temporal ? "on," : "off,";

			if(temporal)
			   valores+=document.getElementById("chk"+idElemento+cont1.toString()).value+",";

			mensaje += temporal ? " on" : " off";
			mensaje += "<br>";

			cont1++;
		}

		arregloTemporal = arreglo;
		document.getElementById(idElemento).value = acumulador.substring(0,acumulador.length-1);
		elemento.value = valores;

		mensaje+=elemento.value+"<br>";



	}


    //ventanana emergente
    function activarModal(wemp_pmla, patron,f,historia, ingreso, servicio, f, c, habitacion, fecha, cco, usuario, nom_pac)
    {
        $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

        clearInterval(reload);

		$.post("Dietas.php",
				{

                    consultaAjax:   	'mostrar_modal',
					wemp_pmla:      	wemp_pmla,
					wpatron:           	patron,
                    fila:           	f,
                    whis:           	historia,
                    wing:           	ingreso,
                    wser:           	servicio,
                    i:                  f,
                    j:                  c,
                    whab:               habitacion,
                    wfec:               fecha,
                    wcco:               cco,
                    wusuario:           usuario,
                    wnombre_pac:        nom_pac



				}
				,function(data) {
					$.blockUI({ message: data,
							css: {  left: 	'5%',
								    top: 	'1%',
								    width: 	'90%',
                                    height: 'auto'
								 }
					  });
                    validar_maxLength_textarea();//2019-10-10
				}
			);
	}

    //Grabar observacion de cada servicio en el patron DSN cuando se abre la ventana emergente.
    function grabar_observ_dsn(wemp_pmla, wbasedato, whis, wing, whab, wser, wserdsn, wusuario, wcco, wfec, wpatron)
    {


            var patron_asociado = $("#ptr_dsn_text").val(); //Patron asociado a la dieta segun nutricion

            $.post("Dietas.php",
                    {

                        consultaAjax:   'grabar_observ_dsn',
                        wemp_pmla:      wemp_pmla,
                        wbasedato:      wbasedato,
                        whis:           whis,
                        wing:           wing,
                        whab:           whab,
                        wser:           wser,
                        wserdsn:        wserdsn,
                        wpatron:        wpatron,
                        wusuario:       wusuario,
                        wcco:           wcco,
                        wfec:           wfec,
                        wpatron_asoc:   patron_asociado,
                        wtexto:         $("#dsn-"+whis+"-"+wing+"-"+wpatron+"-"+wserdsn+"-"+wcco).val()

                    },function(data_json)
                                {
                                alert(data_json.mensaje);

                                },

                                "json"

                    );

	}

    //Grabar observacion de cada servicio en el patron DSN cuando se abre la ventana emergente.
    function patron_asoc_dsn(wemp_pmla, wbasedato, whis, wing, wser, whab, wusuario, wcco, wfec, wpatron)
    {


            $.post("Dietas.php",
                    {

                        consultaAjax:   'patron_asoc_dsn',
                        wemp_pmla:      wemp_pmla,
                        wbasedato:      wbasedato,
                        whis:           whis,
                        wing:           wing,
                        whab:           whab,
                        wser:           wser,
                        wpatron:        wpatron,
                        wusuario:       wusuario,
                        wcco:           wcco,
                        wfec:           wfec,
                        wtexto:         $("#ptr_dsn_text").val()

                    },function(data_json)
                                {
                                alert(data_json.mensaje);

                                },

                                "json"

                    );

	}




	function cerrarVentana()
	 {
      window.close()
     }

     function cerrarventana_emergente()
     {
        $.unblockUI();
        enter();
     }

    //Funcion para grabar media porcion
	function grabar_media_porcion(wemp_pmla, wbasedato, historia, ingreso, habitacion, servicio, id, este, id_td, fecha_interfaz, centro_costos, usuario)
		{


            var valor = 'off';

            //Si el cajon es seleccionado pinta el td de color amarillo y envia on para activar la media porcion.
            if ($("#"+id).is(':checked'))
            {
                    valor = 'on';
                    cajonmediap = document.getElementById(id_td);
                    cajonmediap.style.backgroundColor = color_esq_actual;

            }

            //Si permanece el color es porque lo quieren deselccionar, entonces cambio el td a color gris y envio off en el valor para que vuelva a la porcion completa.
            if(valor == 'off')
                {
                    cajonmediap = document.getElementById(id_td);

                    if(cajonmediap.style.backgroundColor == 'Yellow')
                        {
                          cajonmediap.style.backgroundColor = '#A9A5A5';
                        }
                        else
                            {
                            cajonmediap.style.backgroundColor = '';
                            }

                }


        var parametros = "consultaAjax=grabar_media_porcion&wemp_pmla="+wemp_pmla+"&wbasedato="+wbasedato+"&whis="+historia+"&wing="+ingreso+"&whab="+habitacion+"&wser="+servicio+"&westado="+valor+"&wfec="+fecha_interfaz+"&wcco="+centro_costos+"&wusuario="+usuario;


		  try
		  {

                try {
			$.blockUI({ message: $('#msjEspere') });
		} catch(e){ }
		    var ajax = nuevoAjax();
			ajax.open("POST", "Dietas.php",true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

                    ajax.onreadystatechange=function()
                    {

                            if (ajax.readyState==4)
                            {


                            }
                            try {
                                    $.unblockUI();
                                } catch(e){ }
                    }


                  }catch(e){	}
		}


     //Funcion para grabar media porcion
	function grabar_posqx(wemp_pmla, wbasedato, historia, ingreso, habitacion, servicio, id, este, id_td, edad, tipo_empresa, usuario, fecha_interfaz, centro_costos, hora_max_modifi, hora_max_cancela, hora_actual)
		{


            var valor = 'off';

            //Si el cajon es seleccionado pinta el td de color amarillo y envia on para activar la media porcion.
            if ($("#"+id).is(':checked'))
            {
                    valor = 'on';
                    cajonposqx = document.getElementById(id_td);
                    cajonposqx.style.backgroundColor = color_esq_actual;
                    var parametros = "consultaAjax=grabar_posqx&wemp_pmla="+wemp_pmla+"&wbasedato="+wbasedato+"&whis="+historia+"&wing="+ingreso+"&whab="+habitacion+"&wser="+servicio+"&westado="+valor+"&wedad="+edad+"&wtipemp="+tipo_empresa+"&wusuario="+usuario+"&wfec="+fecha_interfaz+"&wcco="+centro_costos+"&whora_max_modifi="+hora_max_modifi+"&whora_max_cancela="+hora_max_cancela;
            }

            //Si permanece el color es porque lo quieren deselccionar, entonces cambio el td a color gris y envio off en el valor para que vuelva a la porcion completa.
            if(valor == 'off')
                {
                    cajonposqx = document.getElementById(id_td);
                    document.getElementById(id).checked=true;
                    if (hora_actual > hora_max_cancela)
                        {
                            alert("No es posible cancelar el servicio Postquirúrgico porque ha pasado la hora máxima de cancelación");
                            return false;
                        }
                    //Pregunta primero si desea quitar el posquirurgico.
                    confirmar=confirm("¿Esta seguro de realizar esta operación, cancelará el patrón que ha seleccionado después del patron Postquirúrgico?");

                    if (confirmar)
                        {
                        var parametros = "consultaAjax=grabar_posqx&wemp_pmla="+wemp_pmla+"&wbasedato="+wbasedato+"&whis="+historia+"&wing="+ingreso+"&whab="+habitacion+"&wser="+servicio+"&westado="+valor+"&wedad="+edad+"&wtipemp="+tipo_empresa+"&wusuario="+usuario+"&wfec="+fecha_interfaz+"&wcco="+centro_costos+"&whora_max_modifi="+hora_max_modifi+"&whora_max_cancela="+hora_max_cancela;
                       // enter();
                        }
                        else
                            {
                                cajonposqx.bgColor="yellow";

                            }

                }


		  try
		  {

                try {
			$.blockUI({ message: $('#msjEspere') });
		} catch(e){ }
		    var ajax = nuevoAjax();
			ajax.open("POST", "Dietas.php",true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

                    ajax.onreadystatechange=function()
                    {

                            if (ajax.readyState==4)
                            {
                               var x;
                                x = ajax.responseText;

                                if( x == 'recargar')
                                    {
                                    enter(); //Reinicia la pagina.
                                    }
                            }
                            try {
                                    $.unblockUI();
                                } catch(e){ }
                    }


                  }catch(e){	}
		}


    //Funcion princpal que evalua cada seleccion.
	function combina(wemp_pmla, f, c, patron, adi_ser, historia, ingreso, patron_combinable, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, usuario, controlseranterior, media_porcion, este, posqx, wrol_usuario, wpatron_nutricion, wrolnutricion) //f=fila, c=columna, cod_dieta=patron, adi_ser=servicio adicional (on o off), tipo_dieta=(unica seleccionable=on, seleccion multiple=off)
	{

        var cont1    = 1;

        id = "cajon"+f+"-"+c;
        cajon = document.getElementById(id);
        colorcajon = cajon.bgColor; // Color del cajon cuando el esquema esta grabado en la base de datos
        colorcajonnew = cajon.style.backgroundColor; //Color del cajon cuando se selecciona el checkbox
        var dato_media_porcion = document.getElementById("dato_media_porcion").value;
        var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=false;
		var codDSN = $("#wpatron_nutricion").val();

        if(patron_combinable=='off')
            {
                var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
            }

		if ( colorcajon == color_esq_actual || colorcajonnew == color_esq_actual || colorcajon == color_esq_actualie)
			{

			document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;

			id = "cajon"+f+"-"+c;
			cajon = document.getElementById(id);

			cajon.style.backgroundColor="";
			cajon.bgColor="";
            //Modificar es igual a cero cuando es la primera insercion, 1 es una modificacion.
			var modificar = '1';

            chequeados1 = '';

			combinables = document.getElementById("wcombinacon"+c.toString()).value;

			//Recorro todas las columnas en pantalla para saber cual esta chequeado
			 while(document.getElementById("wcom"+cont1.toString()))
			  {

				if (document.getElementById("patron_grid"+f.toString()+"-"+cont1.toString()).checked==true)
					{
						chequeados1 += ""+document.getElementById("westepatron"+cont1.toString()).value+",";
					}

				cont1++;
				}

			chequeados_final = chequeados1.substring(0, chequeados1.length-1);

			grabar_datos(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, patron_combinable, usuario, modificar, chequeados_final,'','', '', wrol_usuario, wpatron_nutricion, wrolnutricion, codDSN, este);

			controlseranterior = '1'; //Variable que controla el ingreso a la funcion que graba, si esta en 1 no deja que ingrese.

			}

		if (controlseranterior == '0')
			{

			chequeados1 = '';

            if (posqx == 'ok')
                {
                 var c = 1;
                }

			combinables = document.getElementById("wcombinacon"+c.toString()).value;

			//Recorro todas las columnas en pantalla para saber cual esta chequeado
			 while(document.getElementById("wcom"+cont1.toString()))
			  {

				if (document.getElementById("patron_grid"+f.toString()+"-"+cont1.toString()).checked==true)
					{
						chequeados1 += ""+document.getElementById("westepatron"+cont1.toString()).value+",";
					}

				cont1++;
				}

			   chequeados_final = chequeados1.substring(0, chequeados1.length-1);

               if(chequeados_final == '')
                  {
                      var dato_media_porcion = document.getElementById("dato_media_porcion").value;
                      var media_porcion = document.getElementById("media_porcion"+f.toString()+"-"+dato_media_porcion).disabled=true;
                  }
               //Modificar es igual a cero se refiere a que es la primera insercion, cuando es 1 es una modificacion.
			   var modificar='0';

               if(posqx != 'ok')
                   {
                    document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=true;
                   }
               //Se agrega esta validacion para los patrones que no son posquirurgicos, el patron posqx configurado es LC, en la tabla 41 de movhos, campo diepqu.



               switch(posqx)
                    {
                    case 'on':

                               control_pos_quirur = 1;
                    break;

                    case '':
                               control_pos_quirur = '';
                    break;

                    case 'NO APLICA':

                               control_pos_quirur = '';
                    break;

                    case 'ok':
                                control_pos_quirur = 'on';
                                id = "tdcajonposquirur"+f;
                                cajon_posqx = document.getElementById(id);

                                //Evalua si el el usuario hace click por primera vez en el cajon, si es asi pinta el td de amarillo, sino lo vuelve al color inicial.
                                if(cajon_posqx.style.backgroundColor == 'Yellow')
                                    {
                                        cajon_posqx.style.backgroundColor = '';
                                    }
                                else
                                    {
                                    cajon_posqx.style.backgroundColor = 'Yellow';
                                    }
                    break;

                    }


			   grabar_datos(wemp_pmla, historia, ingreso, c, f, patron, centro_costos, servicio, fecha, habitacion, nom_pac, tipo_doc, doc_pac, proc_trasl, muerte, edad, alta_proc, tipo_empresa, dias_estancia, patron_combinable, usuario, modificar, chequeados_final, combinables, '', control_pos_quirur, wrol_usuario, wpatron_nutricion, wrolnutricion, codDSN, este );

               if(posqx != 'ok')
                   {
               		   cambiar_color(c,f);
                   }


			 }

    }


    function evaluarEnvio(fila, patron, f, c, historia, ingreso, wemp_pmla, basedato, servicio, usuario, habitacion, centro_costos, novalidahorario, wfec)
       {

		var mensaje="";
		var idElemento=fila.toString()+patron;

		var cont1 = 0;
		var valores = "";
		var arreglo = document.getElementById(idElemento);
		var setter = new Array();
		arreglo.value = "";

		var elemento = document.getElementById("sel"+idElemento);

		elemento.value="";

		while(document.getElementById("chk"+idElemento+cont1.toString()))         //**** Corresponde a los checkbox de la ventana modal
		  {
			mensaje += idElemento + cont1.toString();
			mensaje += document.getElementById("chk"+idElemento+cont1.toString()).checked ? " on\n\r" : " off\n\r";

			arreglo.value += document.getElementById("chk"+idElemento+cont1.toString()).checked ? "on," : "off,";

			setter[cont1] = document.getElementById("chk"+idElemento+cont1.toString()).checked;

			if(setter[cont1])
			   valores+=document.getElementById("chk"+idElemento+cont1.toString()).value+",";

			cont1++;
		  }

		    var cont = 0;

			var checkboxes = document.getElementById(patron+fila).getElementsByTagName( 'input' ); //Array que contiene los checkbox

			for (var x=0; x < checkboxes.length; x++) {
			if (checkboxes[x].checked && checkboxes[x].type.toLowerCase() == 'checkbox')
				{
				cont = cont + 1;

				}
			}

		  if(cont > 0)
				{
				document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=true;
				}
			else
				{

				document.getElementById("patron_grid"+f.toString()+"-"+c.toString()).checked=false;
				id = "cajon"+f+"-"+c;
				cajon = document.getElementById(id);
				cajon.style.backgroundColor="";
                cajon.bgColor="";

				}

		arreglo.value = arreglo.value.substring(0,arreglo.value.length-1);
		elemento.value = valores.substring(0,valores.length-1);


		$.unblockUI({onUnblock: function(){
				if(arreglo != 'undefined'){
					respuestaUnblock(idElemento,setter);
				}
				}});

       enter();

       }

    //Esta funcion permite seleccionar los mismos productos del almuerzo en la comida, en el patron DSN.
    function marcarservigual(tID, wpatron, wservigual, sercla)
       {

            var divigual = 'tabla_servicio-'+wservigual;
            var tabla_actual = tID.split("-");
            var servicio_origen = tabla_actual[1];

			//Este ciclo inactiva todo lo que haya en el servicio comida, luego en el siguiente ciclo activa lo que este igual en el almuerzo.
			$("#"+tID).find("input:checkbox[id^='"+servicio_origen+"-"+wpatron+"']:checked").each(function(){

                var expl = $(this).attr("id").split("-");
                var id = expl[0]+"-"+expl[1]+"-"+expl[2];


                if($("#"+tID).find("[id^="+id+"]").length > 0)
                    {

                        $("#"+tID).find("input:checkbox[id^="+id+"]").trigger("click");
						$("#"+tID).find("input:checkbox[id^="+id+"]").parent().removeAttr("style"); //Elimina el estilo
						$("#"+tID).find("input:checkbox[id^="+id+"]").parent().removeAttr("bgcolor"); //Elimina le atributo bgcolor.

                    }

                });

            if($("#servicioigual").is(':checked')){
                    //Se busca los cajones que esten seleccionado en el almuerzo (Servicio origen)
                    $("#"+divigual).find("input:checkbox[id^='"+wservigual+"-"+wpatron+"']:checked").each(function(){


                        var expl = $(this).attr("id").split("-");
                        var id = servicio_origen+"-"+expl[1]+"-"+expl[2];

                        //Si encuentra algun cajon seleccionado, lo activa en la servicio destino(comida)
                        if($("#"+tID).find("[id^="+id+"]").length > 0)
                            {

                                var cantidad_origen = $("#cantidad-"+wpatron+"-"+expl[2]+"-"+expl[3]).val(); //Captura la cantidad origen
                                $("#cantidad-"+wpatron+"-"+expl[2]+"-"+sercla).val(cantidad_origen); // igual la cantidad en el destino
                                $("#"+tID).find("input:checkbox[id^="+id+"]").trigger("click"); //Selecciona el cajon
								$("#"+tID).find("input:checkbox[id^="+id+"]").parent().css("background-color", "#FAFC7C"); //Selecciona el cajon

                            }

                            var obs_origen = $("#obs-"+wservigual).val(); //Captura la observacion origen
                            $("#obs-"+servicio_origen).val(obs_origen); // Igual la observacion en el destino

                    });
               }

       }

    //Esta funcion deschekea todos los cajones del servicio y cancela los productos para la his e ing.
    function descheckTodos(tID, wemp_pmla, wbasedato, wserdsn, wser, whis, wing, wfec, whab, wpatron, wusuario, wcco)
        {

            //Busca si en la tabla del servicio hay cajones verificados, en caso de no tener ninguno
            //mostrara una alerta.
            var cuantos =  $( "#" + tID ).find('input:checkbox:checked').size()

            if(cuantos == 0)
                {
                    alert('No hay productos para cancelar.');
                    return;
                }



            //Se envian los datos para inactivar los productos de ese servicio y guardar la auditoria.
            $.post("Dietas.php",
            {

                consultaAjax:   'cancelar_dsn',
                wemp_pmla:      wemp_pmla,
                wbasedato:      wbasedato,
                whis:           whis,
                wing:           wing,
                whab:           whab,
                wser:           wser,
                wserdsn:        wserdsn,
                wpatron:        wpatron,
                wusuario:       wusuario,
                wcco:           wcco,
                wfec:           wfec

            },function(data_json)
                        {

                            if (data_json.error == 1)
                            {
                                alert(data_json.mensaje);
                                return;
                            }
                            else
                            {
                                $(".td"+wpatron+wser).removeAttr('bgcolor');
                                $(".td"+wpatron+wser).css('background-color','');

                                 //Con esta linea se inhabilitan todos los checkbox que esten seleccionados
                                $( "#" + tID + " :checkbox").removeAttr('checked');
                                alert(data_json.mensaje);
                                return;
                            }

                        },

                        "json"

            );

        }

	$(document).ready(function(){

	    inicializarJquery();
        simple_reloj();
        // reload = setTimeout("enter()",150*1000);

		fnc_reload( 150 );
		stop_reload_textarea();

		$( ".fila1, .fila2" ).each(function(){

			var hayPatronSeleccionado = $( "input[id^=patron]:checkbox:checked", this ).length > 0;

			var sinPatronesHabilitados = $( "[id^=patron]:disabled", this ).length == $( "[id^=patron]", this ).length;

			//Si no hay patron seleccionado o no hay ningúnpatron habilitado no se permite escribir en los campos de observación e intolerancia
			if( !hayPatronSeleccionado || sinPatronesHabilitados ){
				$( "[id^=wobs_],[id^=wint_]", this ).attr({disabled:true})
			}
			else{
				$( "[id^=wobs_]:disabled,[id^=wint_]:disabled", this ).attr({disabled_origen:true})
			}
		});

	});

      $(function() {

        $('#info').tooltip({
            delay: 0,
            showURL: false,
			track: true,
            bodyHandler: function() {
                return $("<img/>").attr("src", this.src);
            }
          });
      });


    $(function(){
        $.jatt();
        });

    function soloNumeros(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        // alert(charCode);
         if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 37 && charCode != 39 && charCode != 35 && charCode != 36 && charCode != 46) //37:teclaizquierda 39:tecladerecha 36:teclainicio 38:teclafin 46:suprimir
            return false;

         return true;
    }

	function fnc_reload( tiempo ){
		reload = setTimeout("enter()",tiempo*1000);
	}

	/**************************************************************************************************************
	 * Esta función detiene la ejecución de recargar la página al momento de escribir en un textarea
	 **************************************************************************************************************/
	function stop_reload_textarea(){

		$( ".textareadietas" )
			.on( 'focus', function(){
					try{
						clearTimeout( reload );
						fnc_reload(45);
					}
					catch(e){}
				})
			.on( 'keypress', function(){

					try{
						clearTimeout( reload );
					}
					catch(e){ console.log( "falla en reload" ) }

					try{
						clearTimeout( time_keyup );
						// fnc_reload(30);
					}
					catch(e){ console.log( "falla en keypress" ) }
				})
			.on( 'keyup', function(){

				try{
						clearTimeout( time_keyup );
						// fnc_reload(30);
					}
					catch(e){ console.log( "falla en keyup" ) }

               //2019-09-03
                var historiaAux      = $(this).attr("historia");
                var ingresoAux       = $(this).attr("ingreso");
                //var contenedor       = $(this).parent().parent();
                var caracteresTitulo = 0;
                var limiteCaracteres = $("#wlimite_caracteres_observ").val();

                $(".textareadietas[tipo='observacion'][historia='"+historiaAux+"'][ingreso='"+ingresoAux+"']").each(function(){
                    var textAreaObs      = $(this);
                    var currentLengthObs = $(textAreaObs).val().length;
                    var textAreaInt      = $(".textareadietas[tipo='intolerancia'][historia='"+historiaAux+"'][ingreso='"+ingresoAux+"']")
                    var currentLengthInt = $(textAreaInt).val().length;

                    if( currentLengthInt > 0 && currentLengthObs > 0 ){
                        caracteresTitulo = 50;
                        currentLengthInt = currentLengthInt + caracteresTitulo;
                    }

                    limiteCaracteresObs = limiteCaracteres - currentLengthInt;
                    if( limiteCaracteresObs <= 0 )
                        limiteCaracteresObs = currentLengthObs;
                    $(textAreaObs).attr("maxlength", limiteCaracteresObs );

                    limiteCaracteresInt = limiteCaracteres - currentLengthObs - caracteresTitulo;
                    $(textAreaInt).attr("maxlength", limiteCaracteresInt );

                });



				var ___self = this;

				// console.log(___self)
				time_keyup = setTimeout( function(){
									eval( $( ___self ).eq(0).attr( 'onblur' ) ) ;
									fnc_reload(45);
								}, 5000 );
			});
	}

    function validar_maxLength_textarea(){//2019-10-10

        $( ".textareadietas","form[id^='formDSN']" ).on( 'keyup', function(){
            var historiaAux      = $(this).attr("historia");
            var ingresoAux       = $(this).attr("ingreso");
            //var contenedor       = $(this).parent().parent();
            var caracteresTitulo = 0;
            var limiteCaracteres = $("#wlimite_caracteres_observ").val();

            $(".textareadietas[tipo='observacion'][historia='"+historiaAux+"'][ingreso='"+ingresoAux+"']").each(function(){
                var textAreaObs      = $(this);
                var currentLengthObs = $(textAreaObs).val().length;
                var textAreaInt      = $(".textareadietas[tipo='intolerancia'][historia='"+historiaAux+"'][ingreso='"+ingresoAux+"']")
                var currentLengthInt = $(textAreaInt).val().length;
                if( currentLengthInt > 0 && currentLengthObs > 0 ){
                    caracteresTitulo = 50;
                    currentLengthInt = currentLengthInt + caracteresTitulo;
                }

                limiteCaracteresObs = limiteCaracteres - currentLengthInt;
                if( limiteCaracteresObs <= 0 )
                    limiteCaracteresObs = currentLengthObs;
                $(textAreaObs).attr("maxlength", limiteCaracteresObs );

                limiteCaracteresInt = limiteCaracteres - currentLengthObs - caracteresTitulo;
                $(textAreaInt).attr("maxlength", limiteCaracteresInt );

            });
        });

    }


</script>

<?php
 } // Fin de la validacion para la variable $consultaAjax para que no imprima en la respuesta.
  //==================================================================================================================
  //==================================================================================================================
  //***********************************************  F U N C I O N E S  **********************************************
  //==================================================================================================================
  //==================================================================================================================

  function recuperar_dsn_nutri($wemp_pmla, $wbasedato, $whis, $wing, $wpatron_nutricion){

	  global $conex;

	  $datamensaje = array('mensaje'=>'', 'error'=>0);

	  $wfecha = date("Y-m-d");

	  $wult_datos_dsn = consultar_ult_reg_activo($whis, $wing, "", $wpatron_nutricion);

	  $wult_consecutivo_dsn = $wult_datos_dsn['ultimo_consecutivo_dsn'];
	  $wult_fecha_dsn = $wult_datos_dsn['ultima_fecha_dsn'];
	  $wult_hora_dsn = $wult_datos_dsn['ultima_hora_dsn'];

	  $q1 =   " SELECT Sercod
			     FROM ".$wbasedato."_000076
			    WHERE serest = 'on'";
	  $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());

	  $array_prod_ser_detalle = array();

	  //Recorro todos los servicios
	  while($row = mysql_fetch_array($res1)){

		  $wobsdsn = traer_observaciones_dsn_enfer($whis, $wing, $wfecha, $row['Sercod']);

		  //Busco registros para el ultimo consecutivo.
	     $q_con =  " SELECT detpro, detcos, detcan, detcla "
				  ."   FROM ".$wbasedato."_000084 "
				  ."  WHERE dethis = '".$whis."'"
				  ."    AND deting = '".$wing."'"
				  ."    AND detpat = '".$wpatron_nutricion."'"
				  ."    AND detcon = '".$wult_consecutivo_dsn."'"
				  ."    AND detcon != ''"
				." GROUP BY detpro ";
		 $res_con = mysql_query($q_con,$conex) or die (mysql_errno().$q_con." - ".mysql_error());
		 $num_con = mysql_num_rows($res_con);

		//Si existen registros para el ultimo consecutivo todo los datos de ese consecutivo, sino buscara por la ultima hora de registro, historia e ingreso.
		if($num_con > 0){

			$q = " SELECT detpro, detcos, detcan, detcla "
				  ."   FROM ".$wbasedato."_000084 "
				  ."  WHERE dethis = '".$whis."'"
				  ."    AND deting = '".$wing."'"
				  ."    AND detser = '".$row['Sercod']."'"
				  ."    AND detpat = '".$wpatron_nutricion."'"
				  ."    AND detcon = '".$wult_consecutivo_dsn."'"
				  ."    AND detcon != ''"
				." GROUP BY detpro ";
			 $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
			 $num = mysql_num_rows($res);

		}else{


			$q = " SELECT detpro, detcos, detcan, detcla "
			    ."   FROM ".$wbasedato."_000084 "
			    ."  WHERE dethis = '".$whis."'"
			    ."    AND deting = '".$wing."'"
			    ."    AND detser = '".$row['Sercod']."'"
			    ."    AND detpat = '".$wpatron_nutricion."'"
			   // ."    AND detfec = '".$wult_fecha_dsn."'"
			    ."    AND Hora_data = '".$wult_hora_dsn."'"
				." GROUP BY detpro ";
			  $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
			  $num = mysql_num_rows($res);

		 }

		 //Si hay registros para recuperar crea el arreglo para pintar los productos en la pantalla.
		  if($num > 0){

			  while($row1 = mysql_fetch_array($res))	{

					if(!array_key_exists($row1['detpro'], $array_prod_ser_detalle)){

						$array_prod_ser_detalle[$row['Sercod']][$row1['detpro']] = array('codigo'=>$row1['detpro'], 'cantidad'=>$row1['detcan'], 'observacion'=>utf8_encode($wobsdsn));

					}
				}
		  }
		}

		$datamensaje['datos_dsn'] = $array_prod_ser_detalle;

		echo json_encode($datamensaje);
  }


  //Funcion que registra DSN para la ultima fecha registrada en el servicio, se utiliza en la funcion programar_dsn_enfermeria
  function registrar_dsn_ult_fecha($wemp_pmla, $wbasedato, $whis, $wing, $wser, $wfec, $wcco, $wpatron, $wusuario, $whab){

	  global $conex;
      global $whora;

      $ultimo_nutricionista = buscar_ult_nutricionista($whis, $wing); //Ultimo nutricionista que registro datos para el paciente
      $wdatos_nutrinicionista = explode("-", $ultimo_nutricionista);
      $wusuario_nutri = $wdatos_nutrinicionista[0]; //codigo del nutricionista
      $wsuma = 0;
      $waccion = "PEDIDO";

      //OBSERVACIONES DEL PACIENTE
        //Busco si hay alguna observacion en el ingreso actual del paciente
       $q_obs =  " SELECT MAX(CONCAT(fecha_data,hora_data)),movobs "
                ."   FROM ".$wbasedato."_000077 "
                ."  WHERE movhis  = '".$whis."'"
                ."    AND moving  = '".$wing."'"
                ."    AND movser  = '".$wser."'"
                ."    AND movobs != '' "
                ."  GROUP BY 2 "
                ."  ORDER BY 1 DESC ";
        $res_obs = mysql_query($q_obs,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_obs." - ".mysql_error());
        $row_mov = mysql_fetch_array($res_obs);
        $wobs=trim($row_mov[1]);


        //INTOLERANCIAS DEL PACIENTE
        //Busco si hay alguna intolerancias en el ingreso actual del paciente
        $q_int =  " SELECT MAX(CONCAT(fecha_data,hora_data)),movint "
                ."   FROM ".$wbasedato."_000077 "
                ."  WHERE movhis  = '".$whis."'"
                ."    AND moving  = '".$wing."'"
                ."    AND movser  = '".$wser."'"
                ."    AND movobs != '' "
                ."  GROUP BY 2 "
                ."  ORDER BY 1 DESC ";
        $res_int = mysql_query($q_int,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_int." - ".mysql_error());
        $row_int = mysql_fetch_array($res_int);
        $wint=trim($row_int[1]);

      //Busco los ultimos productos activos solicitados para el servicio, con la ultima fecha activa.
      $q = " SELECT detpro, detcos, detcan, detcla "
	      ."   FROM ".$wbasedato."_000084 "
	      ."  WHERE dethis = '".$whis."'"
          ."    AND deting = '".$wing."'"
          ."    AND detser = '".$wser."'"
          ."    AND detpat = '".$wpatron."'"
          ."    AND detfec = '".$wfec."'"
		." GROUP BY detpro ";
	  $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
      $num = mysql_num_rows($res);

      $wobsdsn = traer_observaciones_dsn_enfer($whis, $wing, $wfec, $wser);
      $wptrasociadodsn = traer_patron_asocia_dsn($whis, $wing, $wfec, $wser);

      //Si hay productos anteriores para el servicio entonces hara los registros.
      if ($num > 0)
      {
           //Grabo los productos encontrados.
       while($row = mysql_fetch_array($res))
            {

			  $wfec_aux = time()+(1*24*60*60); //Suma un dia para que registre los productos para el dia siguiente.
			  $wfec = date('Y-m-d', $wfec_aux); //Formatea dia

			  //Consulta si ya esta registrado el producto para ese servicio.
			  $q_valid =   " SELECT detpro, detcos, detcan, detcla "
						  ."   FROM ".$wbasedato."_000084 "
						  ."  WHERE dethis = '".$whis."'"
						  ."    AND deting = '".$wing."'"
						  ."    AND detser = '".$wser."'"
						  ."    AND detpat = '".$wpatron."'"
						  ."    AND detfec = '".$wfec."'"
						  ."    AND detpro = '".$row['detpro']."'"
						  ."	AND detest = 'on'" //Se agrega esta validacion para que valide si el articulo para el servicio, esta activo el dia de hoy, si es asi no permite el registro. Noviembre 07 de 2013. Jonatan Lopez
						." GROUP BY detpro ";
			  $res_valid = mysql_query($q_valid,$conex) or die (mysql_errno().$q_valid." - ".mysql_error());
			  $num_valid = mysql_num_rows($res_valid);

			if($num_valid == 0)
				{

				   $q = " INSERT INTO ".$wbasedato."_000084 (   Medico       ,   Fecha_data,   Hora_data,   detfec  ,   dethis  ,   deting  ,   detser  ,   detpat     ,   detpro     ,  detcos         , detest, detcan, detcco, detcla,       Seguridad        ) "
					   ."                            VALUES ('".$wbasedato."','".$wfec."','".$whora."','".$wfec."','".$whis."','".$wing."','".$wser."','".$wpatron."','".$row['detpro']."','".$row['detcos']."', 'on'  , '".$row['detcan']."', '".$wcco."','".$row['detcla']."' , 'C-".$wusuario."') ";
				  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

				}

            }

		$wfec_aux = time()+(1*24*60*60); //Suma un dia para que registre los productos para el dia siguiente.
	    $wfec = date('Y-m-d', $wfec_aux); //Formatea dia

       //Cuenta si ya exite el registro, si existe hace una actualizacion, si no hace una insercion.
        $q_mov = " SELECT id "
                ."   FROM ".$wbasedato."_000077 "
                ."  WHERE movfec = '".$wfec."'"
                ."    AND movhab = '".$whab."'"
                ."    AND movhis = '".$whis."'"
                ."    AND moving = '".$wing."'"
                ."    AND movser = '".$wser."'"
                ."    AND movcco = '".$wcco."'"
               ." GROUP BY movhis, moving";
        $res_mov = mysql_query($q_mov,$conex) or die (mysql_errno().$q_mov." - ".mysql_error());
        $row_mov = mysql_fetch_array($res_mov);
		$num_datos = mysql_num_rows($res_mov);

        //Si ya existe el registro lo actualiza, sino lo inserta.
        if ($num_datos == 0)

            {
                //Se suman los costos de los productos de la tabla 84 para insertar esa suma en la tabla 77 de movhos.
                $q2 = " SELECT SUM(detcos) AS suma "
                        ."   FROM ".$wbasedato."_000084 "
                        ."  WHERE dethis = '".$whis."'"
                        ."    AND deting = '".$wing."'"
                        ."    AND detser = '".$wser."'"
                        ."    AND detpat = '".$wpatron."'"
                        ."    AND detcco = '".$wcco."'"
                        ."    AND detfec = '".$wfec."'"
                        ."    AND detest = 'on'";
                $res2 = mysql_query($q2,$conex) or die (mysql_errno().$q2." - ".mysql_error());
                $row2 = mysql_fetch_array($res2);
                $wsuma = $row2['suma'];

                //Registra la dieta
                $q = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  , movdie, movest,  movind, movcco, movpco, movcan, movval, movods, movdsn, movnut, movobs, movint,  Seguridad       ) "
                                        ."     VALUES                       ('".$wbasedato."','".$wfec."','".$whora."','".$wfec."','".$whis."','".$wing."','".$whab."','".$wser."','".$wpatron."', 'on', 'N' ,'".$wcco."','".$wpatron."','1', '". $wsuma."','".$wobsdsn."','".$wptrasociadodsn."','".$wusuario_nutri."','".$wobs."', '".$wint."','C-".$wusuario."') ";
                $res1 = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

            }
        else
            {
                //Sumatoria de costo de los productos
                $q2 = " SELECT SUM(detcos) AS suma "
                        ."   FROM ".$wbasedato."_000084 "
                        ."  WHERE dethis = '".$whis."'"
                        ."    AND deting = '".$wing."'"
                        ."    AND detser = '".$wser."'"
                        ."    AND detpat = '".$wpatron."'"
                        ."    AND detcco = '".$wcco."'"
                        ."    AND detfec = '".$wfec."'"
                        ."    AND detest = 'on'";
                $res2 = mysql_query($q2,$conex) or die (mysql_errno().$q2." - ".mysql_error());
                $row2 = mysql_fetch_array($res2);
                $wsuma = $row2['suma'];

                //Pone en on el registro de la 77 para la his e ing
                $q = " UPDATE ".$wbasedato."_000077 "
                    ."    SET movval   = '".$wsuma."',
                              movest   = 'on',
                           Seguridad   = 'C-".$wusuario."',
                              movnut   = '".$wusuario_nutri."',
                              movdie   = '".$wpatron."',
                              movcan   = '1',
                              movdsn   = '".$wptrasociadodsn."',
							  movods   = '".$wobsdsn."',
							  movpco   = '".$wpatron."'"
                    ."  WHERE movfec = '".$wfec."'"
                    ."    AND movcco = '".$wcco."'"
                    ."    AND movhis = '".$whis."'"
                    ."    AND moving = '".$wing."'"
                    ."    AND movser = '".$wser."'";
                $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


            }

       //Grabo la auditoria
       $q_aud = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad, auddie , audcco ) "
              ."   VALUES ('".$wbasedato."','".$wfec."','".$whora."','".$whis."','".$wing."','".$wser."','".$waccion."','".$wusuario."','C-".$wusuario."', '".$wpatron."', '".$wcco."') ";
       $res_aud = mysql_query($q_aud,$conex) or die (mysql_errno().$q_aud." - ".mysql_error());


  }
 }

//Consulta si tiene servicios para el dia siguiente al actual.
function verificar_dia_sgte($whis, $wing){

	global $conex;
	global $wbasedato;
	global $wfecha;

	$dia_sgte = date("Y-m-d", strtotime("$wfecha +1 day"));

	 //Busco el patron de la historia antes de cancelarlo
	  $q = " SELECT movdie "
		  ."   FROM ".$wbasedato."_000077 "
		  ."  WHERE movfec = '".$dia_sgte."'"
		  ."    AND movhis = '".$whis."'"
		  ."    AND moving = '".$wing."'"
		  ."    and movest = 'on' ";
	  $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num_mov = mysql_num_rows($res_mov);

	  return $num_mov;

	}

    //Consulto los productos por servicio del dia anterior.
    function verificar_prod_dsn($whis, $wing, $wpatron_nutricion, $wcco, $wser, $wayer1)
    {

        global $conex;
        global $wbasedato;

        //Consulto los productos por servicio del dia anterior.
        $q = " SELECT detser, detpat, detpro, detcos, detcan, detcla, Fecha_data"
            ."   FROM ".$wbasedato."_000084 "
            ."  WHERE detfec = '".$wayer1."'"
            ."    AND dethis = '".$whis."'"
            ."    AND deting = '".$wing."'"
            ."    AND detpat = '".$wpatron_nutricion."'"
            ."    AND detcco = '".$wcco."'"
            ."    AND detest = 'on' ";
        $respro = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num_prod = mysql_num_rows($respro);

        return $num_prod;

    }

    //Funcion que trae el servicio que sera igual en productos a otro.
    function traer_serv_igual($wserdsn)
    {
        global $conex;
        global $wbasedato;

      $q = " SELECT serads, sernom "
	      ."     FROM ".$wbasedato."_000076 "
          ."    WHERE sercod='".$wserdsn."'";
	  $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
      $row = mysql_fetch_array($res);

      //Busca el nombre del servicio
      $q_nom = " SELECT sernom "
	      ."     FROM ".$wbasedato."_000076 "
          ."    WHERE sercod='".$row['serads']."'";
	  $res_nom = mysql_query($q_nom,$conex) or die (mysql_errno().$q_nom." - ".mysql_error());
      $row_nom = mysql_fetch_array($res_nom);

      return $row['serads']."-".$row_nom['sernom']; //Devuelve el nombre del servicio y el servicio que sera igual en productos


    }


    //Consulta horarios de servicios para ponerlos en el DSN
    function traer_horario_servicio($wservicio)
    {
        global $conex;
        global $wbasedato;

        $q = " SELECT serhin, serhfi, serhia, serhad "
	      ."     FROM ".$wbasedato."_000076 "
          ."    WHERE sercod='".$wservicio."'";
	  $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
      $row = mysql_fetch_array($res);

      return "<b>Horario <br>Normal</b>:".$row['serhin']."-".$row['serhfi']." <b>Adición:</b>".$row['serhia']."-".$row['serhad'];

    }

    //Consulta horarios de un servicios
    function consultar_horarios_servicio($wservicio)
    {
        global $conex;
        global $wbasedato;

      $q = "   SELECT serhin, serhfi, serhia, serhad, serhca "
            ."     FROM ".$wbasedato."_000076 "
            ."    WHERE sercod='".$wservicio."'";
	  $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
      $row = mysql_fetch_array($res);

      return $row['serhin']."-".$row['serhfi']."-".$row['serhia']."-".$row['serhad']."-".$row['serhca'];

    }


  //Esta funcion actuliza la dieta o patron sociado a DSN, en la ventana modal de DSN.
  function patron_asoc_dsn($wemp_pmla, $wbasedato, $whis, $wing, $wser, $whab, $wusuario, $wcco, $wfec, $wpatron, $wtexto)
  {

      global $conex;
      global $wfecha;

      $wdia_sgte = date("Y-m-d", strtotime("$wfecha+ 1 day"));

      //Actualiza el patron asociado a DSN del dia actual.
      $q1 =  " UPDATE ".$wbasedato."_000077 "
            ."    SET movdsn = '".$wtexto."'"
            ."  WHERE movhab = '".$whab."'"
            ."    AND movhis = '".$whis."'"
            ."    AND moving = '".$wing."'"
            ."    AND movcco = '".$wcco."'"
            ."    AND movdie = '".$wpatron."'"
            ."    AND movfec = '".$wfec."'"
            ."    AND movser+0 >= '".$wser."'"  ;
      $res = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());

      //Actualiza el patron asociado a DSN del dia siguiente, si tiene registros.
      $q1 =  " UPDATE ".$wbasedato."_000077 "
            ."    SET movdsn = '".$wtexto."'"
            ."  WHERE movhab = '".$whab."'"
            ."    AND movhis = '".$whis."'"
            ."    AND moving = '".$wing."'"
            ."    AND movcco = '".$wcco."'"
            ."    AND movdie = '".$wpatron."'"
            ."    AND movfec = '".$wdia_sgte."'"  ;
      $res = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
  }


  //Esta funcion actuliza la dieta o patron sociado a DSN, en la ventana modal de DSN.
  function funcionhistoriaurgencias($wemp_pmla, $wbasedato, $whis, $wing, $wcco)
  {
      global $conex;

      //Marcar historia para pedirle dietas desde urgencias
      $q =  "   UPDATE  ".$wbasedato."_000018
                        SET Ubidie = 'on'
                WHERE   Ubihis = '".$whis."'
                        AND Ubiald <> 'on'
                        AND Ubisac = '".$wcco."'
                LIMIT 1";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  }

  //Funcion que permite registrar los productos de DSN para un paciente si el patron es seleccionado por un usuario que NO es nutricionista.
  function programar_dsn_enfermeria($wemp_pmla, $wbasedato, $whis, $wing, $wser, $wfec, $wcco, $wpatron, $wusuario, $whab, $wser_actual, $wcontrol_fecha, $wult_fecha_dsn, $wult_hora_dsn, $wult_consecutivo_dsn )
  {

      global $conex;
      global $whora;

      $ultimo_nutricionista = buscar_ult_nutricionista($whis, $wing); //Ultimo nutricionista que registro datos para el paciente
      $wdatos_nutrinicionista = explode("-", $ultimo_nutricionista);
      $wusuario_nutri = $wdatos_nutrinicionista[0]; //codigo del nutricionista
      $wsuma = 0;
	  $wser_posteriores = $wser; //Esta variable se usa para compara el servicio actual con el servicio desde donde se programar DSN posteriores.
	  $array_nohaydsn = array();

	  $horarios = consultar_horarios_servicio($wser);
	  $arr_horarios = explode('-', $horarios);
	  $hora_actual = date('H:i:s');
	  $hora_fin_servicio = $arr_horarios[1];

	  $waccion = "PEDIDO";

	  //Se valida la hora final del servicio, si es mayor a la actual el registro sera para el dia siguiente.
	  if(strtotime($hora_actual) > strtotime($hora_fin_servicio)){

			$fecha_cons_reg = date("Y-m-d", strtotime("$wfecha+ 1 day"));

	  }else{

		if($wser*1 >= $wser_actual*1 ){

				$fecha_cons_reg = date("Y-m-d");
			}else{

				$fecha_cons_reg = date("Y-m-d", strtotime("$wfecha+ 1 day"));

			}

		}

      //Traigo la ultima fecha de solicitud para DSN.
      //$wult_fecha_dsn = consultar_ult_reg_activo($whis, $wing, $wser, $wpatron);

      //OBSERVACIONES DEL PACIENTE
        //Busco si hay alguna observacion en el ingreso actual del paciente
       $q_obs =  " SELECT MAX(CONCAT(fecha_data,hora_data)),movobs "
                ."   FROM ".$wbasedato."_000077 "
                ."  WHERE movhis  = '".$whis."'"
                ."    AND moving  = '".$wing."'"
                ."    AND movser  = '".$wser."'"
                ."    AND movobs != '' "
                ."  GROUP BY 2 "
                ."  ORDER BY 1 DESC ";
        $res_obs = mysql_query($q_obs,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_obs." - ".mysql_error());
        $row_mov = mysql_fetch_array($res_obs);
        $wobs=trim($row_mov[1]);


        //INTOLERANCIAS DEL PACIENTE
        //Busco si hay alguna intolerancias en el ingreso actual del paciente
        $q_int =  " SELECT MAX(CONCAT(fecha_data,hora_data)),movint "
                ."   FROM ".$wbasedato."_000077 "
                ."  WHERE movhis  = '".$whis."'"
                ."    AND moving  = '".$wing."'"
                ."    AND movser  = '".$wser."'"
                ."    AND movobs != '' "
                ."  GROUP BY 2 "
                ."  ORDER BY 1 DESC ";
        $res_int = mysql_query($q_int,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_int." - ".mysql_error());
        $row_int = mysql_fetch_array($res_int);
        $wint=trim($row_int[1]);

      //Busco los ultimos productos activos solicitados para el servicio, con la ultima fecha activa.

		 $q_con = " SELECT detpro, detcos, detcan, detcla "
			  ."   FROM ".$wbasedato."_000084 "
			  ."  WHERE dethis = '".$whis."'"
			  ."    AND deting = '".$wing."'"
			  ."    AND detpat = '".$wpatron."'"
			  ."    AND detcon = '".$wult_consecutivo_dsn."'"
			  ."    AND detcon != ''"
			." GROUP BY detpro ";
		 $res_con = mysql_query($q_con,$conex) or die (mysql_errno().$q_con." - ".mysql_error());
		 $num_con = mysql_num_rows($res_con);

		if($num_con > 0){

			$q = " SELECT detpro, detcos, detcan, detcla "
				  ."   FROM ".$wbasedato."_000084 "
				  ."  WHERE dethis = '".$whis."'"
				  ."    AND deting = '".$wing."'"
				  ."    AND detser = '".$wser."'"
				  ."    AND detpat = '".$wpatron."'"
				  ."    AND detcon = '".$wult_consecutivo_dsn."'"
				  ."    AND detcon != ''"
				." GROUP BY detpro ";
			 $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
			 $num = mysql_num_rows($res);

		}else{


			$q = " SELECT detpro, detcos, detcan, detcla "
			    ."   FROM ".$wbasedato."_000084 "
			    ."  WHERE dethis = '".$whis."'"
			    ."    AND deting = '".$wing."'"
			    ."    AND detser = '".$wser."'"
			    ."    AND detpat = '".$wpatron."'"
			   // ."    AND detfec = '".$wult_fecha_dsn."'"
			    ."    AND Hora_data = '".$wult_hora_dsn."'"
				." GROUP BY detpro ";
			  $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
			  $num = mysql_num_rows($res);

		 }

      $wobsdsn = traer_observaciones_dsn_enfer($whis, $wing, $wult_fecha_dsn, $wser);
      $wptrasociadodsn = traer_patron_asocia_dsn($whis, $wing, $wult_fecha_dsn, $wser);

      //Si hay productos anteriores para el servicio entonces hara los registros.
      if ($num > 0)
      {
           //Grabo los productos encontrados.
       while($row = mysql_fetch_array($res))
            {

			  //Consulta si ya esta registrado el producto para ese servicio.
			  $q_valid =   " SELECT detpro, detcos, detcan, detcla "
						  ."   FROM ".$wbasedato."_000084 "
						  ."  WHERE dethis = '".$whis."'"
						  ."    AND deting = '".$wing."'"
						  ."    AND detser = '".$wser."'"
						  ."    AND detpat = '".$wpatron."'"
						  ."    AND detfec = '".$fecha_cons_reg."'"
						  ."    AND detpro = '".$row['detpro']."'"
						  ."	AND detest = 'on'" //Se agrega esta validacion para que valide si el articulo para el servicio, esta activo el dia de hoy, si es asi no permite el registro. Noviembre 07 de 2013. Jonatan Lopez
						." GROUP BY detpro ";
			  $res_valid = mysql_query($q_valid,$conex) or die (mysql_errno().$q_valid." - ".mysql_error());
			  $num_valid = mysql_num_rows($res_valid);

			if($num_valid == 0)
				{

				  $q = " INSERT INTO ".$wbasedato."_000084 (   Medico       ,        Fecha_data   ,   Hora_data,         detfec     ,   dethis  ,   deting  ,   detser  ,   detpat     ,   detpro     ,  detcos         , detest, detcan, detcco, detcla,       Seguridad        ) "
					   ."                            VALUES ('".$wbasedato."','".$fecha_cons_reg."','".$whora."','".$fecha_cons_reg."','".$whis."','".$wing."','".$wser."','".$wpatron."','".$row['detpro']."','".$row['detcos']."', 'on'  , '".$row['detcan']."', '".$wcco."','".$row['detcla']."' , 'C-".$wusuario."') ";
				  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

				}

            }

			//Cuenta si ya exite el registro, si existe hace una actualizacion, si no hace una insercion.
			$q_mov = " SELECT id "
					."   FROM ".$wbasedato."_000077 "
					."  WHERE movfec = '".$fecha_cons_reg."'"
					."    AND movhab = '".$whab."'"
					."    AND movhis = '".$whis."'"
					."    AND moving = '".$wing."'"
					."    AND movser = '".$wser."'"
					."    AND movcco = '".$wcco."'"
				   ." GROUP BY movhis, moving";
			$res_mov = mysql_query($q_mov,$conex) or die (mysql_errno().$q_mov." - ".mysql_error());
			$row_mov = mysql_fetch_array($res_mov);
			$num_datos = mysql_num_rows($res_mov);

        //Si ya existe el registro lo actualiza, sino lo inserta.
        if ($num_datos == 0)

            {
                //Se suman los costos de los productos de la tabla 84 para insertar esa suma en la tabla 77 de movhos.
                $q2 = " SELECT SUM(detcos) AS suma "
                        ."   FROM ".$wbasedato."_000084 "
                        ."  WHERE dethis = '".$whis."'"
                        ."    AND deting = '".$wing."'"
                        ."    AND detser = '".$wser."'"
                        ."    AND detpat = '".$wpatron."'"
                        ."    AND detcco = '".$wcco."'"
                        ."    AND detfec = '".$fecha_cons_reg."'"
                        ."    AND detest = 'on'";
                $res2 = mysql_query($q2,$conex) or die (mysql_errno().$q2." - ".mysql_error());
                $row2 = mysql_fetch_array($res2);
                $wsuma = $row2['suma'];

                //Registra la dieta
                $q = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,      Fecha_data     ,  Hora_data ,          movfec     ,   movhis  ,   moving  ,   movhab  ,   movser  , movdie, movest,  movind, movcco, movpco, movcan, movval, movods, movdsn, movnut, movobs, movint,  Seguridad       ) "
                                        ."     VALUES    ('".$wbasedato."','".$fecha_cons_reg."','".$whora."','".$fecha_cons_reg."','".$whis."','".$wing."','".$whab."','".$wser."','".$wpatron."', 'on', 'N' ,'".$wcco."','".$wpatron."','1', '". $wsuma."','".$wobsdsn."','".$wptrasociadodsn."','".$wusuario_nutri."','".$wobs."', '".$wint."','C-".$wusuario."') ";
                $res1 = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

				 //Grabo la auditoria
			   $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad, auddie , audcco, audoba, audobn ) "
					  ."   VALUES ('".$wbasedato."','".$fecha_cons_reg."','".$whora."','".$whis."','".$wing."','".$wser."','".$waccion."','".$wusuario."','C-".$wusuario."', '".$wpatron."', '".$wcco."', '', '".$wobsdsn."') ";
			   $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

            }
        else
            {
                //Sumatoria de costo de los productos
                $q2 = " SELECT SUM(detcos) AS suma "
                        ."   FROM ".$wbasedato."_000084 "
                        ."  WHERE dethis = '".$whis."'"
                        ."    AND deting = '".$wing."'"
                        ."    AND detser = '".$wser."'"
                        ."    AND detpat = '".$wpatron."'"
                        ."    AND detcco = '".$wcco."'"
                        ."    AND detfec = '".$fecha_cons_reg."'"
                        ."    AND detest = 'on'";
                $res2 = mysql_query($q2,$conex) or die (mysql_errno().$q2." - ".mysql_error());
                $row2 = mysql_fetch_array($res2);
                $wsuma = $row2['suma'];

                //Pone en on el registro de la 77 para la his e ing
                $q = " UPDATE ".$wbasedato."_000077 "
                    ."    SET movval   = '".$wsuma."',
                              movest   = 'on',
                           Seguridad   = 'C-".$wusuario."',
                              movnut   = '".$wusuario_nutri."',
                              movdie   = '".$wpatron."',
                              movcan   = '1',
                              movdsn   = '".$wptrasociadodsn."',
							  movods   = '".$wobsdsn."',
							  movpco   = '".$wpatron."'"
                    ."  WHERE movfec = '".$fecha_cons_reg."'"
                    ."    AND movcco = '".$wcco."'"
                    ."    AND movhis = '".$whis."'"
                    ."    AND moving = '".$wing."'"
                    ."    AND movser = '".$wser."'";
                $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


				 //Se agrega auditoria en la actualizacion del registro cuando la enfermera recupera la DSN el 3 de mayo de 2018 Jonatan
			   $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad, auddie , audcco, audoba, audobn ) "
					  ."   VALUES ('".$wbasedato."','".$fecha_cons_reg."','".$whora."','".$whis."','".$wing."','".$wser."','PEDIDO','".$wusuario."','C-".$wusuario."', '".$wpatron."', '".$wcco."', '', '".$wobsdsn."') ";
			   $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());


            }

      }
      else
      {

		    echo $wser."-";

      }

  }

  function cancelar_dsn($wemp_pmla, $wbasedato, $whis, $wing, $whab, $wser, $wserdsn, $wpatron, $wusuario, $wcco, $wfec )
  {

    global $conex;
	global $whora;

    $datamensaje = array('mensaje'=>'', 'error'=>0);

    $q_ser =     " SELECT sernom "
                ."   FROM ".$wbasedato."_000076 "
                ."  WHERE sercod='".$wserdsn."' ";
    $res_ser = mysql_query( $q_ser, $conex ) or die( mysql_errno()." - Error en el query $q_ser - ".mysql_error() );
    $row_ser = mysql_fetch_array( $res_ser );

    $q_cancelado =   " SELECT serhca "
                    ."   FROM ".$wbasedato."_000076 "
                    ."  WHERE sercod = '".$wserdsn."'";
    $res_cancelado = mysql_query($q_cancelado,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cancelado." - ".mysql_error());
    $row_cancelado = mysql_fetch_array($res_cancelado);

    //Si la hora actual es mayor a la hora de cancelacion, los productos seran registrados para el dia siguiente.
    if ($whora >= $row_cancelado['serhca'])
    {
        $datamensaje['mensaje'] = "No puede cancelar el servicio ya que ha pasado el horario maximo de cancelacion.";
        $datamensaje['error'] = 1;
    }
    else
    {

    //Inactivo los productos de la his e ingreso para el servicio y patron
    $q1 =    " UPDATE ".$wbasedato."_000084 "
            ."    SET detest = 'off'"
            ."  WHERE detfec = '".$wfec."'"
            ."    AND dethis = '".$whis."'"
            ."    AND deting = '".$wing."'"
            ."    AND detcco = '".$wcco."'"
            ."    AND detser = '".$wserdsn."'"
            ."    AND detpat = '".$wpatron."'";
    $res = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());


    //Inactiva el patron en la tabla de dietas para ese servicio
      $q =   "  UPDATE ".$wbasedato."_000077 "
            ."    SET movest = 'off'"
            ."  WHERE movfec = '".$wfec."'"
            ."    AND movhab = '".$whab."'"
            ."    AND movhis = '".$whis."'"
            ."    AND moving = '".$wing."'"
            ."    AND movdie = '".$wpatron."'"
            ."    AND movcco = '".$wcco."'"
            ."    AND movser = '".$wserdsn."'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

      $actualizado = mysql_affected_rows();

        if ($actualizado > 0)
            {

            $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad, auddie , audcco ) "
                                        ."   VALUES ('".$wbasedato."','".$wfec."','".$whora."','".$whis."','".$wing."','".$wserdsn."','CANCELADO','".$wusuario."','C-".$wusuario."', '".$wpatron."', '".$wcco."') ";
            $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
            }

        $datamensaje['mensaje'] = "Se cancelo el ".$row_ser['sernom']." con exito";
        $datamensaje['error'] = 0;
    }

    echo json_encode($datamensaje);
  }


    //Trae el ultimo patron asociado a DSN en la tabla 77
 function traer_patron_asocia_dsn($whis, $wing, $wfecha, $wserdsn)
 {

     global $conex;
     global $wbasedato;

    $q =     " SELECT movdsn "
            ."   FROM ".$wbasedato."_000077 "
            ."  WHERE movhis  = '".$whis."'"
            ."    AND moving  = '".$wing."'"
            ."    AND movdsn  != ''"
           ."ORDER BY id desc";
    $res_dsn = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row_dsn = mysql_fetch_array($res_dsn);

    return $row_dsn['movdsn'];

 }

 //Busca si el patron es individual.
 function consultar_servicio_ind($wpatron)
 {

     global $conex;
     global $wbasedato;

     //Busca si hay observaciones para hoy
     $q =    " SELECT dieind "
            ."   FROM ".$wbasedato."_000041 "
            ."  WHERE diecod = '".$wpatron."'" ;
    $res_ind = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row_ind = mysql_fetch_array($res_ind);

    return $row_ind['dieind'];

 }


 //Trae las observaciones para la modal de dietas segun nutricion.
 function traer_observaciones_dsn($whis, $wing, $wfecha_consulta, $wser, $wcco)
 {

     global $conex;
     global $wbasedato;

     //Busca si hay observaciones para hoy
     $q =    " SELECT movods "
            ."   FROM ".$wbasedato."_000077 "
            ."  WHERE movhis  = '".$whis."'"
            ."    AND moving  = '".$wing."'"
            ."    AND movcco  = '".$wcco."'"
            ."    AND movfec  = '".( empty( $wfecha_consulta ) ? '0000-00-00' : $wfecha_consulta )."'"
            ."    AND movser  = '".$wser."'"
           ." ORDER BY id DESC" ;
    $res_obs = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row_obs = mysql_fetch_array($res_obs);

    return $row_obs['movods'];

 }

 //Trae las observaciones DSN cuando enfermeria la activa
 function traer_observaciones_dsn_enfer($whis, $wing, $wfecha_consulta, $wser)
 {

     global $conex;
     global $wbasedato;
	 global $wemp_pmla;

	$wdatos_rol = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
    $winf_nutricion_dsn = explode("-", $wdatos_rol);
    $wpatronutricion = $winf_nutricion_dsn[1];// Patron DSN


     //Busca si hay observaciones para hoy
    $q =    " SELECT movods "
            ."   FROM ".$wbasedato."_000077 "
            ."  WHERE movhis  = '".$whis."'"
            ."    AND moving  = '".$wing."'"
            ."    AND movfec  <= '".$wfecha_consulta."'"
            ."    AND movser  = '".$wser."'"
			."    AND movods != ''"
           ." ORDER BY id DESC" ;
    $res_obs = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row_obs = mysql_fetch_array($res_obs);

	//Si no hay observaciones en la tabla movhos_000077, buscara en la tabla 78(auditoria) en el campo audods.
	if(trim($row_obs['movods']) == ''){

		$wult_fecha_dsn = consultar_ult_reg_activo($whis, $wing, $wser, $wpatronutricion);
		$wult_fecha = $wult_fecha_dsn['ultima_fecha_dsn'];
		$wult_hora = $wult_fecha_dsn['ultima_hora_dsn'];

		$q =     " SELECT audods "
				."   FROM ".$wbasedato."_000078 "
				."  WHERE audhis  = '".$whis."'"
				."    AND auding  = '".$wing."'"
				."    AND audser  = '".$wser."'"
				."    AND audods != ''"
				." ORDER BY concat(Fecha_data,' ',Hora_data) DESC ";
		$res_obs = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row_obs = mysql_fetch_array($res_obs);

		$wobservacion_dsn = $row_obs['audods'];

	}else{

		$wobservacion_dsn = $row_obs['movods'];
	}


    return $wobservacion_dsn;

 }

  // Esta funcion permite insertar observaciones e intolerancias, ademas de actualizarlas
  function grabar_observ_intoler($wemp_pmla, $wbasedato, $whis, $wing, $whab, $wser, $wtexto, $wusuario, $wobsint, $wcco, $wfec)
	{

	  global $conex;
	  global $wfecha;
	  global $whora;

	  $wtexto = trim( $wtexto );

      $wpatronesantes = consultar_patron_actual($whis, $wing, $wser, $wfec);
	  //Si no hay un patrón seleccionado no se deja modificar

	  $wdatopatrones = explode("-", $wpatronesantes);

	  if( empty( $wdatopatrones[0] ) ){
		  return;
	  }

	  $numSerPosterior = 0;

	  switch($wobsint)
			{
		// La variable $wobsint me dice que tipo de insercion o modificacion es (observacion o intolerancia)
		case 'o':

			  //Verifica si la historia e ingreso tienen datos en la tabla77 de movhos
			  $q = " SELECT movobs "
					  ."   FROM ".$wbasedato."_000077 "
					  ."  WHERE movhis = '".$whis."'"
					  ."    AND moving = '".$wing."'"
					  ."    AND movhab = '".$whab."'"
					  ."    AND movser = '".$wser."'"
					  ."    AND movfec = '".$wfecha."'";
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $row = mysql_fetch_array($res);
              $num = mysql_num_rows($res);

                $q = " SELECT Seraso "
                    ."   FROM ".$wbasedato."_000076"
                    ."  WHERE Sercod = '".$wser."'"
                    ."    AND seraso != ''"
                    ."    AND seraso != '.'"
                    ."    AND seraso != 'NO APLICA'";
                $resaso = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                $rowaso=mysql_fetch_array($resaso);

                $wserv_asociados=explode(",",$rowaso[0]);

			  //Si hay datos solamente debo actualizar el registro

			  //Consulto si hay un servicio posterior al actual
			  $qSerPos = " SELECT fecha_data, Movfec, Movhis, Moving, Movser "
					  ."   FROM ".$wbasedato."_000077 "
					  ."  WHERE movhis = '".$whis."'"
					  ."    AND moving = '".$wing."'"
					  ."    AND movser > '".$wser."'"
					  ."    AND movfec = '".$wfecha."'"
					  ."    AND movser NOT IN('".implode("','",$wserv_asociados)."') ";
			  $resSerPosterior = mysql_query($qSerPos,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
              $numSerPosterior = mysql_num_rows($resSerPosterior);

			  if ($num > 0)
				{

            //Si el texto que recibe esta vacio no inserta nada, si es diferente al que habia pondra el nuevo
			  if ($row['movobs'] != $wtexto)
						{

				 $q = "  UPDATE ".$wbasedato."_000077 "
								  ."    SET movobs = '".$wtexto."'"
								  ."  WHERE movfec = '".$wfecha."'"
								  ."    AND movhab = '".$whab."'"
								  ."    AND movhis = '".$whis."'"
								  ."    AND moving = '".$wing."'"
								  ."    AND movser = '".$wser."'";
				$resenc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                $actualizado = mysql_affected_rows();

                $q1 = "  UPDATE ".$wbasedato."_000077 "
								  ."    SET movobs = '".$wtexto."'"
								  ."  WHERE movfec = '".$wfecha."'"
								  ."    AND movhab = '".$whab."'"
								  ."    AND movhis = '".$whis."'"
								  ."    AND moving = '".$wing."'"
								  ."    AND movser = '".$wserv_asociados[0]."'";
				$resenc1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                $actualizado1 = mysql_affected_rows();

				if ($actualizado == 1)
					{

					$q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad, auddie , audcco, audoba, audobn ) "
												  ."   VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."','MODIFICO OBSERVACION','".$wusuario."','C-".$wusuario."', '".$wdatopatrones[0]."', '".$wcco."' , '".$row['movobs']."', '".$wtexto."' ) ";
					$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
					}


                 if ($actualizado1 == 1)
					{

					$q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad, auddie , audcco, audoba, audobn ) "
												  ."   VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wserv_asociados[0]."','MODIFICO OBSERVACION','".$wusuario."','C-".$wusuario."','".$wdatopatrones[0]."', '".$wcco."' , '".$row['movobs']."', '".$wtexto."' ) ";
					$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

					}

                   }
				   else{
					   $numSerPosterior = 0;
				   }
                }


			  // Si la respuesta del conteo da cero, entonces hara una insercion
			  else if( true )
						{
							$q = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,  movest,  movind,    movobs, movcco,  Seguridad       ) "
								  ."     VALUES                       ('".$wbasedato."','".$wfecha."','".$whora."','".$wfecha."','".$whis."','".$wing."','".$whab."','".$wser."', 'on', 'N' ,'".$wtexto."','".$wcco."', 'C-".$wusuario."') ";
							$res1 = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

							$q2 = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad, auddie , audcco , audoba, audobn ) "
							  ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."','MODIFICO OBSERVACION','".$wusuario."','C-".$wusuario."', '".$wdatopatrones[0]."','".$wcco."', '', '".$wtexto."') ";
							$err = mysql_query($q2,$conex) or die (mysql_errno().$q2." - ".mysql_error());

                             if ($wserv_asociados != '')
                                {
                                    $q1 = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,  movest,  movind,    movobs, movcco,  Seguridad       ) "
                                        ."     VALUES                       ('".$wbasedato."','".$wfecha."','".$whora."','".$wfecha."','".$whis."','".$wing."','".$whab."','".$wserv_asociados[0]."', 'on', 'N' ,'".$wtexto."','".$wcco."', 'C-".$wusuario."') ";
                                    $res = mysql_query($q1,$conex) or die (mysql_errno().$q1." - ".mysql_error());

                                    $q3 = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie , audcco , audoba, audobn ) "
                                    ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wserv_asociados[0]."','MODIFICO OBSERVACION','".$wusuario."','C-".$wusuario."', '".$wdatopatrones[0]."','".$wcco."', '', '".$wtexto."') ";
                                    $err = mysql_query($q3,$conex) or die (mysql_errno().$q3." - ".mysql_error());
                                };
                        }

				break;

			case 'i':
			  //Verifica si la historia e ingreso tienen datos en la tabla77 de movhos
			  $q = " SELECT movint "
				  ."   FROM ".$wbasedato."_000077 "
				  ."  WHERE movhis = '".$whis."'"
				  ."    AND moving = '".$wing."'"
				  ."    AND movhab = '".$whab."'"
				  ."    AND movser = '".$wser."'"
				  ."    AND movfec = '".$wfecha."'";
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $row = mysql_fetch_array($res);
              $num_int = mysql_num_rows($res);


               $q = " SELECT Seraso "
                    ."   FROM ".$wbasedato."_000076"
                    ."  WHERE Sercod = '".$wser."'"
                    ."    AND seraso != ''"
                    ."    AND seraso != '.'"
                    ."    AND seraso != 'NO APLICA'";
                $resaso = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                $rowaso=mysql_fetch_array($resaso);
                $wserv_asociados=explode(",",$rowaso[0]);


			  //Si hay datos solamente debo actualizar el registro
			  if ($num_int > 0)
				{

                   if ($row['movint'] != $wtexto)
						{

                        $q = "  UPDATE ".$wbasedato."_000077 "
                                        ."    SET movint = '".$wtexto."'"
                                        ."  WHERE movfec = '".$wfecha."'"
                                        ."    AND movhab = '".$whab."'"
                                        ."    AND movhis = '".$whis."'"
                                        ."    AND moving = '".$wing."'"
                                        ."    AND movser = '".$wser."'";
                        $resenc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                        $actualizado = mysql_affected_rows();

                        $q1 = "  UPDATE ".$wbasedato."_000077 "
                                        ."    SET movint = '".$wtexto."'"
                                        ."  WHERE movfec = '".$wfecha."'"
                                        ."    AND movhab = '".$whab."'"
                                        ."    AND movhis = '".$whis."'"
                                        ."    AND moving = '".$wing."'"
                                        ."    AND movser = '".$wserv_asociados[0]."'";
                        $resenc1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                        $actualizado1 = mysql_affected_rows();

                        //Si se inserta almenos un caracter insertara registro en la bitacora en la movhos_000078.
                        if ($actualizado == 1)
                            {
                            $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie , audcco, audoba, audobn ) "
                                                                    ."   VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."','MODIFICO INTOLERANCIA','".$wusuario."','C-".$wusuario."', '".$wdatopatrones[0]."','".$wcco."', '".$row['movint']."', '".$wtexto."') ";
                            $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                            }

                        if ($actualizado1 == 1)
                            {
                            $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad, auddie , audcco, audoba, audobn   ) "
                                                        ."   VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wserv_asociados[0]."','MODIFICO OBSERVACION','".$wusuario."','C-".$wusuario."', '".$wdatopatrones[0]."','".$wcco."', '".$row['movint']."', '".$wtexto."') ";
                            $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                            }
                        }

				}


			  else
                    {
                        $q1 = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,  movest,  movind,   movint, movcco,  Seguridad       ) "
                                ."     VALUES                       ('".$wbasedato."','".$wfecha."','".$whora."','".$wfecha."','".$whis."','".$wing."','".$whab."','".$wser."', 'on', 'N' ,'".$wtexto."','".$wcco."', 'C-".$wusuario."') ";
                        $res = mysql_query($q1,$conex) or die (mysql_errno().$q1." - ".mysql_error());

                        $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad, auddie , audcco, audoba, audobn  ) "
                                                        ."   VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."','MODIFICO INTOLERANCIA','".$wusuario."','C-".$wusuario."', '".$wdatopatrones[0]."','".$wcco."', '', '".$wtexto."') ";
                        $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

                        if ($wserv_asociados != '')
                        {
                            $q1 = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,  movest,  movind,   movint, movcco,  Seguridad       ) "
                                    ."     VALUES                       ('".$wbasedato."','".$wfecha."','".$whora."','".$wfecha."','".$whis."','".$wing."','".$whab."','".$wserv_asociados[0]."', 'on', 'N' ,'".$wtexto."','".$wcco."', 'C-".$wusuario."') ";
                            $res = mysql_query($q1,$conex) or die (mysql_errno().$q1." - ".mysql_error());

                            $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad, auddie  , audcco, audoba, audobn ) "
                                                            ."   VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wserv_asociados[0]."','MODIFICO INTOLERANCIA','".$wusuario."','C-".$wusuario."', '".$wdatopatrones[0]."','".$wcco."', '', '".$wtexto."') ";
                            $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                        };
                    }
				break;
			}

		//Devuelvo un uno si existe un servicio posterior
		//Esto para indicarle al usuario que las modficaciones a las observaciones realizadas
		//No afectará a los servicios posteriores ya creados
		return $numSerPosterior > 0 ? '1' : '' ;
	}

  //==================================================================================================================
  //==================================================================================================================


  //==================================================================================================================
  //==================================================================================================================

    //Funcion que maneja el sistema de mensajeria
	function mensajeria()
		{

			global $wemp_pmla;
            global $conex;

            //Esta variable permite identificar el lapso de recarga de los mensajes del chat.
            $wtiemporecarga = consultarAliasPorAplicacion($conex, $wemp_pmla, 'recargaMsgKardex');

            echo "<INPUT type='hidden' id='mensajeriaPrograma' value='dietas'>";
            echo "<INPUT type='hidden' id='tiemporecargamsg' value='$wtiemporecarga'>";

			echo "<table style='width:80%;font-size:10pt' align='center'>";

			echo "<tr><td class='encabezadotabla' align='center' colspan='3'>Mensajer&iacute;a Dietas</td></tr>";

			echo "<tr>";

			//Area para escribir
			echo "<td style='width:45%;' rowspan='2'>";
			// echo "<textarea id='mensajeriaKardex' onKeyPress='return validarEntradaAlfabetica(event);' style='width:100%;height:80px'></textarea>";
			echo "<textarea id='mensajeriaKardex' style='width:100%;height:80px'></textarea>";
			echo "</td>";

			//Boton Enviar mensaje
			echo "<td align='center' style='width:10%'>";
			echo "<input type='button' onClick='enviandoMensaje()' value='Enviar' style='width:100px'>";
			echo "</td>";

			//Mensajes
			echo "<td style='width:45%' rowspan='2'>";
			echo "<div id='historicoMensajeria' style='overflow:auto;font-size:10pt;height:80px'>";
			echo "</div>";
			echo "</td>";

			echo "</tr>";

			echo "<tr>";
			echo "<td align='center'><b>Mensajes sin leer: </b><div id='sinLeer'></div></td>";
			echo "</tr>";

			echo "</table>";
		}

  //==================================================================================================================
  //==================================================================================================================

   function SumaHoras( $time1, $time2 )
      {

        list($hour1, $min1, $sec1) = parteHora($time1);

        list($hour2, $min2, $sec2) = parteHora($time2);

        return date('H:i:s', mktime( (integer) $hour1 + (integer) $hour2, (integer)$min1 + (integer)$min2, (integer)$sec1 + (integer)$sec2));

    }

    function parteHora( $hora )
    {

      $horaSplit = explode(":", $hora);
        if( count($horaSplit) < 3 )
            {
            $horaSplit[2] = 0;
            }

        return $horaSplit;
    }

  //==================================================================================================================
  //==================================================================================================================
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
            $datos['meses'] = $meses;
            $datos['dias'] = $dias;
        }

        return $datos;
    }


   //=================================================================================================================
  //Funcion que verifica que tipo de accion se grabara en la auditoria, ademas devuelve un parametro que determina las acciones.
  function accion_a_grabar($whis, $wing, $whab, $wser, $wpatron, &$westado, $wobservacion, $wintolerancias, $wmodificar)
     {
	  global $wbasedato;
	  global $conex;
	  global $wfec;
	  global $whora;
      global $wfecha;
	  global $wcco;

	  $westado="on";

	  //Primero valido que al paciente no le hallan dado ALTA DEFINITIVA, porque puede ser que la enfermera graba el
	  //registro de Dietas despues de dado el paciente de ALTA y todavia lo tenga en pantalla, y eso lo que haria es
	  //reactivar el pedido de ese paciente que ya se fue.
	  $q = " SELECT COUNT(*) "
	      ."   FROM ".$wbasedato."_000018 "
	      ."  WHERE ubihis  = '".$whis."'"
	      ."    AND ubiing  = '".$wing."'"
	      ."    AND ubiald != 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);

      if ($row[0] > 0)                              //SI esta activo
         {

            //Se evaluan los servicios pedidos para el paciente.
             $q     = " SELECT COUNT(*) "
                    ."   FROM ".$wbasedato."_000078, ".$wbasedato."_000077  "
                    ."  WHERE ".$wbasedato."_000078.Fecha_data = '".$wfecha."'"
                    ."    AND ".$wbasedato."_000078.Fecha_data = ".$wbasedato."_000077.Fecha_data"
                    ."    AND ".$wbasedato."_000078.Hora_data = ".$wbasedato."_000077.Hora_data"
                    ."    AND auding = '".$wing."'"
                    ."    AND audser = '".$wser."'"
                    ."    AND movser = '".$wser."'"
                    ."    AND audcco = '".$wcco."'"
                    ."    AND movhis = '".$whis."'"
                    ."    AND moving = '".$wing."'"
                    ."    AND movest = 'on'";
            $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $row = mysql_fetch_array($res);

          if ($row[0] > 0)                          //Ya tiene servicio
	         {

              if ($wpatron!="")                     //Si hay un patron?
		         {
			      //Busco si tiene el mismo patron activo, si es asi, quiere decir que NO lo modifico
			      $q = " SELECT COUNT(*) "
			          ."   FROM ".$wbasedato."_000077 "
			          ."  WHERE movhis = '".$whis."'"
			          ."    AND moving = '".$wing."'"
			          ."    AND movser = '".$wser."'"
			          ."    AND movdie = '".$wpatron."'"
			          ."    AND movest = 'on' "
			          ."    AND movfec = '".$wfec."'";
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		          $row = mysql_fetch_array($res);
		          if ($row[0] > 0)                     //Ya tiene el servicio y el patron
		             {
			          //Busco si se modificaron las OBSERVACIONES basado en la ultimo registro grabado, no importa que tenga ese ultimo registro en el
		              //campo de observaciones.
		              $q = " SELECT COUNT(*) "
						  ."   FROM ".$wbasedato."_000077, (SELECT fecha_data fec, hora_data hor, MAX(CONCAT(fecha_data,hora_data)) "
						  ."                          FROM ".$wbasedato."_000077 "
						  ."                         WHERE movhis = '".$whis."'"
						  ." 						   AND moving = '".$wing."'"
						  ." 			 			   AND movser = '".$wser."'"
						  ." 						   AND movest = 'on' "
						  ."                    	 GROUP BY 1,2 "
						  ."                         ORDER BY 1 DESC,2 DESC LIMIT 1) obser  "
						  ."  WHERE movhis = '".$whis."'"
						  ."    AND moving = '".$wing."'"
						  ."    AND movser = '".$wser."'"
						  ."    AND movobs = '".trim($wobservacion)."'"
						  ."    AND movest = 'on' "
						  ."    AND fecha_data = obser.fec "
						  ."    AND hora_data  = obser.hor ";
					  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			          $row = mysql_fetch_array($res);

                      if ($row[0] == 0)                 //Ya tiene el servicio, el patron y la observacion, osea no se modifico nada
		                  $waccion = "MODIFICO OBSERVACION";


		              //Busco si se modificaron las INTOLERANCIAS basado en la ultimo registro grabado, no importa que tenga ese ultimo registro en el
		              //campo de intolerancia.
		              $q = " SELECT COUNT(*) "
						  ."   FROM ".$wbasedato."_000077, (SELECT fecha_data fec, hora_data hor, MAX(CONCAT(fecha_data,hora_data)) "
						  ."                          FROM ".$wbasedato."_000077 "
						  ."                         WHERE movhis = '".$whis."'"
						  ." 						   AND moving = '".$wing."'"
						  ." 			 			   AND movser = '".$wser."'"
						  ." 						   AND movest = 'on' "
						  ."                    	 GROUP BY 1,2 "
						  ."                         ORDER BY 1 DESC,2 DESC LIMIT 1) inter  "
						  ."  WHERE movhis = '".$whis."'"
						  ."    AND moving = '".$wing."'"
						  ."    AND movser = '".$wser."'"
						  ."    AND movint = '".trim($wintolerancias)."'"
						  ."    AND movest = 'on' "
						  ."    AND fecha_data = inter.fec "
						  ."    AND hora_data  = inter.hor ";
		              $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			          $row = mysql_fetch_array($res);

			          if ($row[0] == 0)                 //Ya tiene el servicio, el patron y la observacion, osea no se modifico nada
		                 {
			                   $waccion = "MODIFICO INTOLERANCIAS";
		                 }
		             }
		            else                               //Hay servicio pero no hay Patron
		              {
			           //Busco si esta dentro del rango modificacion del pedido
			           $q = " SELECT COUNT(*) "
				           ."   FROM ".$wbasedato."_000076 "
				           ."  WHERE sercod = '".$wser."'"
				           ."    AND serhin <= '".$whora."'"
				           ."    AND serhfi >= '".$whora."'"
				           ."    AND seradi  = 'on' ";
				       $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				       $row = mysql_fetch_array($res);
				       if ($row[0] > 0)
				          {
							//$wmodificar == 2 se refiere a la carga inicial de cada servicio, osea es cuando se carga la informacion de todas las historias al cargar la pagina.

                           if ($wmodificar == '2')
								{
								$waccion = "PEDIDO";
								}
							else
								$waccion = "MODIFICO PEDIDO";     //Quiere decir que se esta modificando

				          }
				         else                                //Esta fuera del rango de modificacion osea que es una adicion
				           {
					        //Busco si esta dentro del rango Adicion
					        $q = " SELECT COUNT(*) "
					            ."   FROM ".$wbasedato."_000076 "
					            ."  WHERE sercod = '".$wser."'"
					            ."    AND serhia <= '".$whora."'"
					            ."    AND serhad >= '".$whora."'"
					            ."    AND seradi  = 'on' ";
				            $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					        $row = mysql_fetch_array($res);
					        if ($row[0] > 0)
					           {
				                $waccion = "MODIFICO ADICION";
			                   }
			                  else
			                     {
				                 // $waccion="off";
								  if ($wmodificar != '2')

                                      echo "3";  //Mensaje javascript en el response text :  "El servicio de la historia seleccionada no puede ser adicionado o modificado porque esta fuera del horario. Favor revisar el inicio de horario de adición o el horario final del pedido en la parte superior de la pantalla"

			                     }
			               }
		              }
	             }
	         }
	       else  //No tenia el servicio
	          {
	           if ($wpatron!="")  //Si hay un patron
		         {
			      //Busco si esta dentro del rango de PEDIDO
			      $q = " SELECT COUNT(*) "
			          ."   FROM ".$wbasedato."_000076 "
			          ."  WHERE sercod = '".$wser."'"
			          ."    AND serhin <= '".$whora."'"
			          ."    AND serhfi >= '".$whora."'"
			          ."    AND seradi  = 'on' ";
		          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			      $row = mysql_fetch_array($res);
			      if ($row[0] > 0)
			         {
		              $waccion = "PEDIDO";
	                 }
	                else
	                   {
		                //Busco si esta dentro del rango de ADICION
				        $q = " SELECT COUNT(*) "
				            ."   FROM ".$wbasedato."_000076 "
				            ."  WHERE sercod = '".$wser."'"
				            ."    AND serhia <= '".$whora."'"
				            ."    AND serhad >= '".$whora."'"
				            ."    AND seradi  = 'on' ";
			            $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				        $row = mysql_fetch_array($res);
				        if ($row[0] > 0)
				          {
			               $waccion = "ADICION";
		                  }
		                 else
		                    {

                                echo "6"; //Mensaje javascript en el response text: El horario de solicitd de adiciones no ha iniciado, favor revisar los horarios en la parte superior de la pantalla.
								$waccion = 'no_ha_iniciado_horario';
                                return $waccion;
		                    }
		               }
			     }
	          	else
	          	  $waccion="off";                      //No se a seleccionado ningun patron y tampoco tenia patron para el servicio.
	          }
          }
         else
            $waccion="off";               //No se hace ninguna accion
       return $waccion;
     }

  //==================================================================================================================
  //==================================================================================================================


  //==================================================================================================================
  //==================================================================================================================
  //Esta funcion devuelve un parametro enabled o disabled que activa e inactiva los cajones, y otras funciones. Identifica si se esta en horario para hacer operaciones.
  function validaciones($whistoria, $wingreso, $whabitacion, $wservicio, $wtransaccion)
     {

      global $wbasedato;
	  global $conex;
	  global $wfec;
	  global $wfecha;
	  global $whabilitado;

	  $whora =(string)date("H:i:s");

	  //Valido que la fecha sea igual a la actual, para poder habilitar el boton de adicion y cancelacion

	  if ($wfec == $wfecha)
	     {
		  switch ($wtransaccion)
		     {
			  case "Consulta":
			      //========================================================================================================\\
			      //** H O R A R I O ***************************************************************************************\\
			      //Aca se hacen cuatro validaciones basadas todas en la hora actual, se valida si se habilitan los cajones \\
			      //de las dietas o no, y sabiendo porque se habilitan, si es por, PEDIDO, MODIFICACION, ADICION O          \\
			      //CANCELACION, para que al momento de grabar se sepa cual es la accion o transaccion que se esta haciendo.\\
			      //********************************************************************************************************\\
			      //Verifico que se pueda actualiazar o no de acuerdo al horario del servicio, si alguna de las horas       \\
			      //limite no se a cumplido entonces dejo el cajon habilitado, pero al GRABAR se debe identificar que accion\\
			      //se esta haciendo PEDIDO, MODIFICACION, ADICION o CANCELACION.                                           \\
			      //========================================================================================================\\
			      //Que este dentro de la HORA INICIAL y FINAL de PEDIDO
			      $q = " SELECT COUNT(*) "
			          ."   FROM ".$wbasedato."_000076 "
			          ."  WHERE sercod = '".$wservicio."'"
			          ."    AND serhin <= '".$whora."'"
			          ."    AND (serhfi >= '".$whora."'"
			          ."     OR  serhia >= '".$whora."'"
			          ."     OR  serhca >= '".$whora."'"
			          ."     OR  serhad >= '".$whora."')"
			          ."    AND seradi  = 'on' ";
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			      $row = mysql_fetch_array($res);
			      if ($row[0] > 0)
			         {
				      $whabilitado="Enabled";
				     }
			        else
			          {
				       $whabilitado="Disabled";
					  }
			      break;

			  case "Grabar":
			      //Valido si todavia esta dentro del rango de tiempo para grabar el servicio
			      //volviendo a llamar esta funcion con el parametro de 'Consulta'
			      validaciones($whistoria, $wingreso, $whabitacion, $wservicio, "Consulta");
			      if ($whabilitado != "Enabled" )
			         {
		              $whabilitado="Disabled";
		             }
		     }
         }
         //Si la fecha seleccionada es anterior el cajon de adicion siempre esta deshabilitado
        else
        {
	       $whabilitado="Disabled";
        }

	  return $whabilitado;
	 }
  //==================================================================================================================
  //==================================================================================================================


  //==================================================================================================================
  //==================================================================================================================
  function determinar_adicion($wser)
     {

	  global $wbasedato;
	  global $conex;
	  global $whora;
	  global $wadi_ser;

	  //Busco que la hora este dentro del rango de inicio del servicio y la hora maxima de modificacion, si esta por fuera
	  //de este rango es porque puede estar dentro del rango de adicion.
	  $q = " SELECT COUNT(*) "
	      ."   FROM ".$wbasedato."_000076 "
	      ."  WHERE serhin <= '".$whora."'"
	      ."    AND serhca >= '".$whora."'"
	      ."    AND sercod  = '".$wser."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);
      if ($row[0] > 0)
         $wadi_ser="off";
        else
           {
            //Busco si esta dentro del rango Adicion
			$q = " SELECT COUNT(*) "
			    ."   FROM ".$wbasedato."_000076 "
			    ."  WHERE sercod  = '".$wser."'"
			    ."    AND serhin <= '".$whora."'"
			    ."    AND serhad >= '".$whora."'"
			    ."    AND seradi  = 'on' ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row = mysql_fetch_array($res);
			if ($row[0] > 0)
			   {
				$wadi_ser="on";
			   }
			  else
			     $wadi_ser="off";
	       }
	   return $wadi_ser;
	 }
  //==================================================================================================================
  //==================================================================================================================


  //==================================================================================================================
  //==================================================================================================================
  function buscar_servicio_anterior()
     {
	  global $wbasedato;
	  global $conex;
	  global $wser_ant;

	  global $wser;

	  //Traigo el orden del servicio actual y el esquema al que pertenece.
	  $q = " SELECT serord, seresq "
	      ."   FROM ".$wbasedato."_000076 "
	      ."  WHERE sercod = '".$wser."'"
	      ."    AND serest = 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);
	  if ($num > 0)
	     {
		  $row = mysql_fetch_array($res);

		  $wordser=$row['serord'];
		  $wesqser=$row['seresq'];

		  if ($wordser > 1)
		     {
			  //Con el Orden traigo el servicio anterior
			  $q = " SELECT sernom "
			      ."   FROM ".$wbasedato."_000076 "
			      ."  WHERE serord = ".($wordser-1)   //Servicio anterior
			      ."    AND seresq = '".$wesqser."'"  //Codigo Esquema
			      ."    AND serest = 'on' ";
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);
			  if ($num > 0)
			     {
				  $row = mysql_fetch_array($res);
				  $wser_ant=$row[0];
				 }
		     }
		    else
		       {
			    //Si entro por aca es porque esta en servicio con orden=1, entonces busco el orden maximo para el esquema y
			    //ese tiene que ser el servicio anterior.
			    $q = " SELECT MAX(serord), sernom "
			        ."   FROM ".$wbasedato."_000076 "
			        ."  WHERE seresq = '".$wesqser."'"  //Codigo Esquema
			        ."    AND serest = 'on' "
			        ."  GROUP BY 2 "
			        ."  ORDER BY 1 DESC LIMIT 1	";
			    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			    $num = mysql_num_rows($res);
			    if ($num > 0)
			       {
				    $row = mysql_fetch_array($res);
				    $wser_ant=$row[1];
				   }
		       }
		 }
	}
  //==================================================================================================================
  //==================================================================================================================


  //==================================================================================================================
  //==================================================================================================================

   //Permite identificar cual fue el ultimo servicio que se le guardo a una his e ing
   function buscar_servicio_anterior_por_historia($whis, $wing)
     {

      global $wbasedato;
	  global $conex;
	  global $wser_ant;


	  //Busco el servicio anterior a partir de la ultima fecha, hora e identificador del registro.
	  $q = " SELECT movser,id "
	      ."   FROM ".$wbasedato."_000077 "
	      ."  WHERE movhis = '".$whis."'"
	      ."    AND moving = '".$wing."'"
	      ."    AND movest = 'on' "
	      ."  ORDER BY Fecha_data DESC, movser DESC "
          ."  LIMIT 1";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);

      if ($num > 0)
	     {
		  $row = mysql_fetch_array($res);
		  $wser_ant=$row['movser'];
		 }
		else
		   $wser_ant="";

        return $wser_ant;

     }

     //==================================================================================================================
  //==================================================================================================================

   //Funcion que trae la observacion nutricional del kardex
  function traer_observ_alimentacion($whis, $wing)
     {

      global $wbasedato;
	  global $conex;
      global $wfecha;


	  $q = " SELECT Kardie"
	      ."   FROM ".$wbasedato."_000053"
	      ."  WHERE karhis = '".$whis."'"
	      ."    AND karing = '".$wing."'"
          ."    AND Fecha_data = '".$wfecha."'"
	      ."    AND karest = 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $row = mysql_fetch_array($res);

      if ($row['Kardie'] == '')
      {
            $dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
            $wayer = date('Y-m-d', $dia); //Formatea dia

            $q = " SELECT Kardie"
                ."   FROM ".$wbasedato."_000053"
                ."  WHERE karhis = '".$whis."'"
                ."    AND karing = '".$wing."'"
                ."    AND Fecha_data = '".$wayer."'"
                ."    AND karest = 'on' ";
            $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $row = mysql_fetch_array($res);
      }


      return $row['Kardie'];
     }

  //==================================================================================================================
  //==================================================================================================================

  //Idenifica cual es el patron que se cobro del ultimo registro.
  function buscar_patronppal_serv_ant($whis, $wing, $wcco)
     {

      global $wbasedato;
	  global $conex;


	  //Busco el ultimo patron que se cobro
	  $q = " SELECT movpco, movser, fecha_data"
	      ."   FROM ".$wbasedato."_000077"
	      ."  WHERE movhis = '".$whis."'"
	      ."    AND moving = '".$wing."'"
          ."    AND movcco = '".$wcco."'"
	      ."    AND movest = 'on'"
        ." ORDER BY 3 DESC, 2 DESC limit 1";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);

      if ($num > 0)
	     {
		  $row = mysql_fetch_array($res);
		  $wptrppal=$row['movpco'];
		 }
		else
		   $wptrppal="";

        return $wptrppal;
     }

   //==================================================================================================================
  //==================================================================================================================



  //==================================================================================================================
  //==================================================================================================================
    function traer_dietas_kardex($whis, $wing, $wfecha)
    {


       global $conex;
       global $wbasedato;

       $q = " SELECT dikcod "
            ."   FROM ".$wbasedato."_000052 "
            ."  WHERE dikhis = '".$whis."'"
            ."    AND diking = '".$wing."'"
            ."    AND dikfec = '".$wfecha."'"
            ."    AND dikest = 'on' ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        if ($wnum == 0)
        {

        $wayer = time()-(1*24*60*60); //Resta un dia
        $wayer1 = date('Y-m-d', $wayer); //Formatea dia

        $q = " SELECT dikcod "
            ."   FROM ".$wbasedato."_000052"
            ."  WHERE dikhis = '".$whis."'"
            ."    AND diking = '".$wing."'"
            ."    AND dikfec = '".$wayer1."'"
            ."    AND dikest = 'on' ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        }

        if ($wnum > 0)
        {
            $wdieta1 = "";
            for ($i=1; $i <= $wnum;$i++)
            {
                $row = mysql_fetch_array($res);
                $wdieta1 .= $row['dikcod']."<br>";

            }

            $wdieta = $wdieta1;
        }
        else
        {
            $wdieta = 'Sin dieta en el kardex';
        }

        return $wdieta;
    }


  //==================================================================================================================
  //==================================================================================================================



  //==================================================================================================================
  //==================================================================================================================
  function buscar_si_hay_servicio_anterior($wser)
     {
	  global $wbasedato;
	  global $conex;
	  global $wfecha;
	  global $wser;
	  global $wser_ant;
	  global $wcco;
	  global $whabilitado;
	  global $wfec;
	  global $wemp_pmla;

	  validaciones('', '', '', $wser, "Consulta");

	  $wdatos_rol_enfermeria = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
	  $winf_nutricion_dsn = explode("-", $wdatos_rol_enfermeria);
	  $wpatron_nutricion = $winf_nutricion_dsn[1]; // Patron asociado a las nutricionistas.

	  if ($whabilitado)
	     {
		  //Primero verifico que no halla movimiento en el dia con el Servicio Actual con el Centro de Costo, porque si lo hay no tengo que buscar
		  //si hay movimiento del servicio anterior.
		  $q = " SELECT COUNT(*) "
		      ."   FROM ".$wbasedato."_000077 a, ".$wbasedato."_000085 b"
		      ."  WHERE movfec = '".$wfec."'"
		      ."    AND movser = '".$wser."'"
		      ."    AND movest = 'on' "
		      ."    AND movfec = b.fecha_data "
		      ."    AND movser = encser "
              ."    AND encest = 'on' "
		      ."    AND enccco = '".trim($wcco)."'";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $row = mysql_fetch_array($res);

		  if ($row[0] == 0)
		     {
			  buscar_servicio_anterior($wser);    //Busco cual es el servicio anterior

			  //Busco si hay algun registro con el servicio anterior activo.
		      $q = " SELECT MAX(movfec) "
		          ."   FROM ".$wbasedato."_000077 "
		          //."  WHERE movfec = '".$wfecha."'"
		          ."  WHERE movser = '".$wser_ant."'"
		          ."    AND movest = 'on' ";
		      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $row = mysql_fetch_array($res);
			  $num = mysql_num_rows($res);
			  if ($num > 0)
			     {
				  return true;
				 }
				else
				   return false;
	         }
	        else
	           return false;
         }
        else
           return false;
	 }
    //==================================================================================================================
  //==================================================================================================================


  //==================================================================================================================
  //==================================================================================================================

  	//Registra los productos de los servicios individuales (SI, TMO)
	function procesar_datos_servind($wemp_pmla, $wbasedato, $wcodigo, $wpatron, $whis, $wing, $wser, $wvalorneto, $wusuario, $whab, $wcco, $westado, $wcantidad, $wfec, $wclasificacion)
     {

        global $conex;
        global $wfecha;
        global $whora;
        global $wusuario;

        //Consulta la informacion de la historia e ingreso para el servicio
        $q = " SELECT movdie, movval, movpam, movpco"
            ."   FROM ".$wbasedato."_000077 "
            ."  WHERE movhis = '".$whis."'"
            ."    AND moving = '".$wing."'"
            ."    AND movfec = '".$wfec."'"
            ."    AND movser = '".$wser."'"
            ."    AND movest = 'on'";
        $res_die = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
        $row_die=mysql_fetch_array($res_die);

        $wpatronactual = $row_die['movdie']; //Patron del servicio actual.

        $whorarioadicional = consultarHorario($wser, $wfec); //Consulta si se esta en horario de adicion.

        if ($whorarioadicional == 'on')
        {
        $waccion = 'ADICION';
        }
        else
        {
           $waccion = accion_a_grabar($whis, $wing, $whab, $wser, $wpatron, $westadoaccion, '', '', '');
        }


        //Trae los rangos de horas para guardar la accion como adicion
        $q1="SELECT serhia, serhad, serpda "
            ."  FROM ".$wbasedato."_000076 "
            ." WHERE sercod = '".$wser."'";
        $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row1 = mysql_fetch_array($res1);

        $whi_desp_adicion = $row1['serhia'];
        $wvhf_desp_adicion = $row1['serhad'];
        $wpatronmerienda = $row1['serpda'];

        //Aqui se evalua si se estan registrando productos en el servicio merienda del dia siguiente.
        if ($wpatronmerienda != '' and $wpatronmerienda != '.' and $wpatronmerienda != 'NO APLICA')
            {

            $wvalidacionhoras = analisishoras($whi_desp_adicion, $wvhf_desp_adicion, $wfec);

            $whora =(string)date("H:i:s");
            $wfechahoy = explode("-",$wfecha);
            $whoraactual = explode(":",$whora);
            $whoraactual1 = mktime($whoraactual[0],$$whoraactual1[1],$$whoraactual1[2],$wfechahoy[1],$wfechahoy[2],$wfechahoy[0]);


            if ($wvalidacionhoras['whoraactual'] >= $wvalidacionhoras['whoraini'] and $whoraactual1 < $wvalidacionhoras['whorafin'])
                {
                    $waccion = 'ADICION';

                }
            }

      //Busco los datos del producto seleccionado
      $q =     " SELECT rpcvac, rpcfec, rpcvan "
		      ."   FROM ".$wbasedato."_000131 "
		      ."  WHERE rpccod = '".$wcodigo."'"
		      ."    AND rpcpat = '".$wpatron."'"
		      ."    AND rpcest = 'on' ";
      $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num_cos = mysql_num_rows($res_cos);

       if ($num_cos > 0 or trim($wcodigo) == '')
            {

            //Busco si la historia para este patron ya tiene grabado o no la opcion
            $q = " SELECT SUM(Detcos) as suma "
                ."   FROM ".$wbasedato."_000084 "
                ."  WHERE detfec = '".( empty( $wfec ) ? '0000-00-00' : $wfec )."'"
                ."    AND dethis = '".$whis."'"
                ."    AND deting = '".$wing."'"
                ."    AND detser = '".$wser."'"
                ."    AND detpat = '".$wpatron."'"
                ."    AND detcla = '".$wclasificacion."'"
                ."    AND detest = 'on' ";
            $resdet = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $rowdes = mysql_fetch_array($resdet);

            $wtotalanterior = $rowdes['suma'];
            $wtotalactual = ($wvalorneto*$wcantidad) + $wtotalanterior;
            $wpreciofinal = ($wvalorneto*$wcantidad);

            //Busco si el producto ha sido seleccionado anteriormente, en caso de ser asi significa que lo quiere eliminar del listado, entonces vuelvo la variable
            //$westado = 2 para que haga los procedimientos del case correspondiente.
            $q = " SELECT detpro, detcan"
                ."   FROM ".$wbasedato."_000084 "
                ."  WHERE detfec = '".( empty( $wfec ) ? '0000-00-00' : $wfec )."'"
                ."    AND dethis = '".$whis."'"
                ."    AND deting = '".$wing."'"
                ."    AND detser = '".$wser."'"
                ."    AND detpat = '".$wpatron."'"
                ."    AND detpro = '".$wcodigo."'"
                ."    AND detcla = '".$wclasificacion."'"
                ."    AND detest = 'on' ";
            $resprod = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $rowprod = mysql_fetch_array($resprod);

            if($rowprod['detpro'] != "")
            {
                if ($rowprod['detcan'] == $wcantidad)
                {
                    $westado = '2'; // Este dato se da cuanto seleccionaron un producto que ya estaba seleccionado y que desean eliminar.
                }
                else
                {
                    $westado = '3';   // Esta dato se da cuanto hay cambio en la cantidad de productos seleccionados.
                }
            }


            switch ($westado){

                   case '0': // En este caso vuelve a estado off los productos de la historia e ingreso seleccionados en la tabla 77 y 84 de movhos

                                //Se consultan los patrones por fecha, his, ing y servicio.
                                $q = " SELECT movdie, movval, movpco"
                                    ."   FROM ".$wbasedato."_000077 "
                                    ."  WHERE movfec = '".$wfec."'"
                                    ."    AND movhis = '".$whis."'"
                                    ."    AND moving = '".$wing."'"
                                    ."    AND movser = '".$wser."'"
                                    ."    and movest = 'on' ";
                                $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                $row_mov=mysql_fetch_array($res_mov);
                                $wpatron1 = $row_mov['movdie']; // Patron actual
                                $wvalor = $row_mov['movval']; // Costo actual
                                $wcuantospatrones = explode(",", $wpatron1);
								$wpco = $row_mov['movpco']; //Patron que se cobra
                                $wcombinable = valida_combinable($wpatron);

                                //Busco si la historia para este patron ya tiene grabado o no la opcion
                                $q = " SELECT SUM(Detcos) as valor "
                                    ."   FROM ".$wbasedato."_000084 "
                                    ."  WHERE detfec = '".$wfec."'"
                                    ."    AND dethis = '".$whis."'"
                                    ."    AND deting = '".$wing."'"
                                    ."    AND detser = '".$wser."'"
                                    ."    AND detcco = '".$wcco."'"
                                    ."    AND detpat = '".$wpatron."'"
                                    ."    AND detcla = '".$wclasificacion."'"
                                    ."    AND detest = 'on' ";
                                $resdet = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                $rowcosto = mysql_fetch_array($resdet);

                                $wtotal = $rowcosto['valor'];
                                $wvalor1 =  $wvalor - $wtotal;

                                if($wcombinable == 'off')
                                    {

                                    //Cuando el sistema esta en horario adicional el paciente ya puede tener un patron seleccionado, por lo tanto se hace este
                                    //procedimiento, ademas analiza que tenga mas de un patron para el paciente con la variable $wcuantospatrones.
                                    if(count($wcuantospatrones) > 1)
                                        {
                                        //En este caso hacemos explode del patron que trae, para luego eliminar el patron de ese arreglo
										$wpatronfinal_array = explode(",",$wpatron1);

										//Se elimina el patron del arreglo.
										foreach($wpatronfinal_array as $key => $value)
											{
											if($value == $wpatron)
												{
												unset($wpatronfinal_array[$key]);
												break;
												}
											}

										$wpatronfinal = implode(",",$wpatronfinal_array);

										$wpatron_ind = consultar_servicio_ind($wpatron);

										if($wpatron_ind == 'on')
											{
											$wpatroncobra = ", movpco = '".$wpco."'";
											}

                                        $q =     " UPDATE ".$wbasedato."_000077 "
                                                ."    SET movdie = '".$wpatronfinal."', movval = '".$wvalor1."' ".$wpatroncobra.""
                                                ."  WHERE movfec = '".$wfec."'"
                                                ."    AND movhis = '".$whis."'"
                                                ."    AND moving = '".$wing."'"
                                                ."    AND movser = '".$wser."'"
                                                ."    AND movdie like '%".$wpatron."%'";
                                        $resenc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                                        }
                                        else
                                        {

                                        //Deja el patron que viene con la seleccion e inactiva la solicitud
                                        $wpatronfinal = $wpatron;

                                        $q =     " UPDATE ".$wbasedato."_000077 "
                                                ."    SET movdie = '".$wpatronfinal."', movval = '".$wvalor1."', movest = 'off'"
                                                ."  WHERE movfec = '".$wfec."'"
                                                ."    AND movhis = '".$whis."'"
                                                ."    AND moving = '".$wing."'"
                                                ."    AND movser = '".$wser."'"
                                                ."    AND movdie like '%".$wpatron."%'";
                                        $resenc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                        }


                                    //Busco todos los servicios individuales para esta historia e ingreso en la fecha actual
                                    $q1 =    " UPDATE ".$wbasedato."_000084 "
                                            ."    SET detest = 'off'"
                                            ."  WHERE detfec = '".$wfec."'"
                                            ."    AND dethis = '".$whis."'"
                                            ."    AND deting = '".$wing."'"
                                            ."    AND detcco = '".$wcco."'"
                                            ."    AND detpat = '".$wpatron."'";
                                    $resenc1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                                    //Consulta de nuevo si tiene patrones
                                    $q = " SELECT movdie, movval "
                                        ."   FROM ".$wbasedato."_000077 "
                                        ."  WHERE movfec = '".$wfec."'"
                                        ."    AND movhis = '".$whis."'"
                                        ."    AND moving = '".$wing."'"
                                        ."    AND movser = '".$wser."'"
                                        ."    and movest = 'on' ";
                                    $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                    $row_mov=mysql_fetch_array($res_mov);
                                    $wpatron1 = $row_mov['movdie']; // Patron actual

                                    //Consulta de nuevo si tiene patrones
                                    $q_off = " SELECT movdie, movval "
                                            ."   FROM ".$wbasedato."_000077 "
                                            ."  WHERE movfec = '".$wfec."'"
                                            ."    AND movhis = '".$whis."'"
                                            ."    AND moving = '".$wing."'"
                                            ."    AND movser = '".$wser."'"
                                            ."    and movest = 'off' ";
                                    $res_mov_off = mysql_query($q_off,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_off." - ".mysql_error());
                                    $row_mov_off =mysql_fetch_array($res_mov_off);
                                    $wpatron_off = $row_mov_off['movdie']; // Patron actual

                                    //Evalua si no tiene patrones y si el valor final es cero, si es asi guarda el cancelado.
                                    if($wtotal == 0 and $wpatron_off != '' and $wpatron1 == '' )
                                        {
                                            //Grabo la bitacora
                                            $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie , audcco ) "
                                                ."   VALUES ('".$wbasedato."','".$wfec."','".$whora."','".$whis."','".$wing."','".$wser."','CANCELADO','".$wusuario."','C-".$wusuario."', '".$wpatronactual."','".$wcco."') ";
                                            $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                                        }
                                    }



                    break;

                    case '1':   //Grabo nuevos productos

                                $q = " INSERT INTO ".$wbasedato."_000084 (   Medico       ,   Fecha_data,   Hora_data,   detfec  ,   dethis  ,   deting  ,   detser  ,   detpat     ,   detpro     ,  detcos         , detest, detcan, detcco, detcla,       Seguridad        ) "
                                    ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wfec."','".$whis."','".$wing."','".$wser."','".$wpatron."','".$wcodigo."',".$wpreciofinal.", 'on'  , '".$wcantidad."', '".$wcco."','".$wclasificacion."' , 'C-".$wusuario."') ";
                                $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

                                //Cuenta si ya exite el registro, si existe hace una actualizacion, si no hace una insercion.
                                $q = " SELECT COUNT(*), movdie, movobs, movint, movnut "
                                    ."   FROM ".$wbasedato."_000077 "
                                    ."  WHERE movfec = '".$wfec."'"
                                    ."    AND movhab = '".$whab."'"
                                    ."    AND movhis = '".$whis."'"
                                    ."    AND moving = '".$wing."'"
                                    ."    AND movser = '".$wser."'"
									."  GROUP BY movhis";
                                $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                                $row = mysql_fetch_array($res);

                                $wobs = $row[2]; // Observaciones
                                $wint = $row[3]; // Intolerancias
                                $wnutricionista = $row[4]; // Nutricionista

                                if ($row[0] > 0)
                                    {

                                        $q = " SELECT movdie, movval, movpqu, movpco"
                                            ."   FROM ".$wbasedato."_000077 "
                                            ."  WHERE movfec = '".$wfec."'"
                                            ."    AND movhab = '".$whab."'"
                                            ."    AND movhis = '".$whis."'"
                                            ."    AND moving = '".$wing."'"
                                            ."    AND movser = '".$wser."'"
                                            ."    AND movest = 'on'";
                                        $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                                        $row = mysql_fetch_array($res);

                                        $wpatronesexisten = explode(',', $row['movdie']);
                                        $wposqx = $row['movpqu']; //Posquirurgico
										$wpco = $row['movpco']; //Patron que se cobra

										//Procesa el array $wpatronesexisten, y le agrega el patron individual que llegue.
                                        if (!in_array($wpatron, $wpatronesexisten))
                                            {
                                            $wpatronesexisten[] = $wpatron;
                                            }

										//Si el paciente ya tiene patrones reasigna el valor de patron por el nuevo arreglo que contiene el patorn individual.
                                        if ($row['movdie'] != '')
                                        {
										//Une de nuevo los patrones por coma.
                                        $wpatronf = implode(',',$wpatronesexisten);
                                        }
                                        else
                                        {
											//Sino deja el valor del patron individual que viene en la funcion.
                                            $wpatronf = $wpatron;
                                        }

                                        $wvalorfinal = ($row[1]+$wpreciofinal); //Valor que tenia, mas el valor de los productos seleccionados.

										//Elimina todo el registro para registrar la nueva informacion.
                                        $q = " DELETE FROM ".$wbasedato."_000077 "
                                            ."  WHERE movfec = '".$wfec."'"
                                            ."    AND movhab = '".$whab."'"
                                            ."    AND movhis = '".$whis."'"
                                            ."    AND moving = '".$wing."'"
                                            ."    AND movser = '".$wser."'";
                                        $res1 = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

										//Si el patron que cobra es vacio, entonces pondra como patron el patron individual seleccionado.(SI,TMO)
                                        if ($wpco == '')
                                        {
                                            $wpco = $wpatron;
                                        }

										//Inserta la informacion en el movimiento de la dieta.
                                        $q1 = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,   movdie     , movind,   movest, movobs, movint, movpco,   movval  , movcco, movcan, Seguridad, movpqu, movnut      ) "
                                             ."      VALUES                       ('".$wbasedato."','".$wfecha."','".$whora."','".$wfec."','".$whis."','".$wing."','".$whab."','".$wser."','".$wpatronf."', 'N'   ,'on', '".$wobs."', '".$wint."', '".$wpco."', '".$wvalorfinal."','".$wcco."','1', 'C-".$wusuario."','".$wposqx."','".$wnutricionista."') ";
                                        $res = mysql_query($q1,$conex) or die (mysql_errno().$q1." - ".mysql_error());


                                    }
                                    else
                                    {

                                        //Consulta que patron tenia la historia e ingreso
                                        $q = " SELECT movdie "
                                            ."   FROM ".$wbasedato."_000077 "
                                            ."  WHERE movfec = '".$wfec."'"
                                            ."    AND movhab = '".$whab."'"
                                            ."    AND movhis = '".$whis."'"
                                            ."    AND moving = '".$wing."'"
                                            ."    AND movser = '".$wser."'"
                                            ."    AND movest = 'on'";
                                        $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                                        $row = mysql_fetch_array($res);

                                       //Borra el registro para que ingresen datos nuevos
                                        $q = " DELETE FROM ".$wbasedato."_000077 "
                                            ."  WHERE movfec = '".$wfec."'"
                                            ."    AND movhab = '".$whab."'"
                                            ."    AND movhis = '".$whis."'"
                                            ."    AND moving = '".$wing."'"
                                            ."    AND movser = '".$wser."'"
                                            ."    AND movest = 'on'";
                                        $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());


                                        //Toma el patron que tenia y le concatena el SI o TMO.
                                        if($row[0] != '')
                                        {
                                          $wpatronf = $row[0].",".$wpatron;
                                        }
                                        else
                                        {
                                           $wpatronf = $wpatron;
                                        }

                                        //Se actualiza el patron con lo que tenia el paciente, si no tenia nada solo pondra el patron seleccionado.
                                        $q = " UPDATE ".$wbasedato."_000077 "
                                            ."    SET movdie = '".$wpatronf."'"
                                            ."  WHERE movfec = '".$wfec."'"
                                            ."    AND movhab = '".$whab."'"
                                            ."    AND movhis = '".$whis."'"
                                            ."    AND moving = '".$wing."'"
                                            ."    AND movser = '".$wser."'";
                                        $resenc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


                                        $q1 = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,   movdie     , movind,   movest, movobs, movint, movpco,   movval  , movcco, movcan, Seguridad       ) "
                                             ."      VALUES                       ('".$wbasedato."','".$wfecha."','".$whora."','".$wfec."','".$whis."','".$wing."','".$whab."','".$wser."','".$wpatron."', 'N'   ,'on', '".$wobs."', '".$wint."', '".$wpatron."', '".$wtotalactual."','".$wcco."','1', 'C-".$wusuario."') ";
                                        $res = mysql_query($q1,$conex) or die (mysql_errno().$q1." - ".mysql_error());
                                    }


                                    //Busco si la historia ya tiene registrado el servicio en la auditoria
                                    $q = " SELECT audhis "
                                        ."   FROM ".$wbasedato."_000078 "
                                        ."  WHERE audhis     = '".$whis."'"
                                        ."    AND auding     = '".$wing."'"
                                        ."    AND audser     = '".$wser."'"
                                        ."    AND audcco     = '".$wcco."'"
                                        ."    AND audacc     = 'PEDIDO'"
                                        ."    AND fecha_data = '".$wfecha."'";
                                    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                    $num_prod = mysql_num_rows($res);

                                    if($num_prod >= 3)
                                        {
                                            //Grabo la bitacora
                                            $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie , audcco  ) "
                                                                    ."   VALUES ('".$wbasedato."','".$wfec."','".$whora."','".$whis."','".$wing."','".$wser."','MODIFICO PEDIDO','".$wusuario."','C-".$wusuario."', '".$wpatron."','".$wcco."') ";
                                            $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                                        }
                                    else
                                        {

                                            //Grabo la bitacora
                                            $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad, auddie , audcco ) "
                                                                    ."   VALUES ('".$wbasedato."','".$wfec."','".$whora."','".$whis."','".$wing."','".$wser."','".$waccion."','".$wusuario."','C-".$wusuario."', '".$wpatron."','".$wcco."') ";
                                            $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                                        }


                    break;

                    case '2':       // Aqui se inactiva un producto
                                    //Busco todos los servicios individuales para esta historia e ingreso en la fecha actual
                                    $q1 =    " UPDATE ".$wbasedato."_000084 "
                                            ."    SET detest = 'off'"
                                            ."  WHERE detfec = '".$wfec."'"
                                            ."    AND dethis = '".$whis."'"
                                            ."    AND deting = '".$wing."'"
                                            ."    AND detpat = '".$wpatron."'"
                                            ."    AND detcco = '".$wcco."'"
                                            ."    AND detcla = '".$wclasificacion."'"
                                            ."    AND detpro = '".$wcodigo."'";
                                    $resenc1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                                    //Consulta el arreglo de patrones que trae
                                    $q = " SELECT movdie  "
                                        ."   FROM ".$wbasedato."_000077 "
                                        ."  WHERE movfec = '".$wfec."'"
                                        ."    AND movhis = '".$whis."'"
                                        ."    AND moving = '".$wing."'"
                                        ."    AND movser = '".$wser."'"
                                        ."    AND movest = 'on'";
                                    $res1 = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                                    $row1 = mysql_fetch_array($res1);

                                    $wpatronestrae = $row1[0];

                                    $q = " SELECT movval  "
                                        ."   FROM ".$wbasedato."_000077 "
                                        ."  WHERE movfec = '".$wfec."'"
                                        ."    AND movdie = '".$wpatronestrae."'"
                                        ."    AND movhis = '".$whis."'"
                                        ."    AND moving = '".$wing."'"
                                        ."    AND movser = '".$wser."'"
                                        ."    AND movest = 'on'";
                                    $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                                    $row = mysql_fetch_array($res);

                                    $wtotalanterior = $row[0];
                                    $wtotalactual = $wtotalanterior - $wvalorneto;

                                    $q = " UPDATE ".$wbasedato."_000077 "
                                            ."    SET movval = '".$wtotalactual."'"
                                            ."  WHERE movfec = '".$wfec."'"
                                            ."    AND movhab = '".$whab."'"
                                            ."    AND movhis = '".$whis."'"
                                            ."    AND moving = '".$wing."'"
                                            ."    AND movdie = '".$wpatronestrae."'"
                                            ."    AND movser = '".$wser."'";
                                    $resenc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                                    echo "inactivo"; //Se devuelve la palabra inactivo para quitarle el color azul al td.

                    break;

                    case '3':       // Aqui se actualiza la cantidad de productos
                                    //Busco todos los servicios individuales para esta historia e ingreso en la fecha actual
                                    $q1 =    " UPDATE ".$wbasedato."_000084 "
                                            ."    SET detcos = '".$wpreciofinal."', detcan = '".$wcantidad."'"
                                            ."  WHERE detfec = '".$wfec."'"
                                            ."    AND dethis = '".$whis."'"
                                            ."    AND deting = '".$wing."'"
                                            ."    AND detpat = '".$wpatron."'"
                                            ."    AND detcco = '".$wcco."'"
                                            ."    AND detcla = '".$wclasificacion."'"
                                            ."    AND detpro = '".$wcodigo."'";
                                    $resenc1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                                     //Busco si la historia para este patron ya tiene grabado o no la opcion
                                    $q =     " SELECT SUM(Detcos) "
                                            ."   FROM ".$wbasedato."_000084 "
                                            ."  WHERE detfec = '".$wfec."'"
                                            ."    AND dethis = '".$whis."'"
                                            ."    AND deting = '".$wing."'"
                                            ."    AND detser = '".$wser."'"
                                            ."    AND detcco = '".$wcco."'"
                                            ."    AND detcla = '".$wclasificacion."'"
                                            ."    AND detpat = '".$wpatron."'"
                                            ."    AND detest = 'on' ";
                                    $resdet = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                    $rowcosto = mysql_fetch_array($resdet);

                                    $wtotal = $rowcosto[0];

                                    $q =     " UPDATE ".$wbasedato."_000077 "
                                            ."    SET movval = '".$wtotal."'"
                                            ."  WHERE movfec = '".$wfec."'"
                                            ."    AND movhab = '".$whab."'"
                                            ."    AND movhis = '".$whis."'"
                                            ."    AND moving = '".$wing."'"
                                            ."    AND movdie LIKE '%".$wpatron."%'"
                                            ."    AND movser = '".$wser."'";
                                    $resenc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    break;


                    }
            }
           elseif($wcodigo != '')
           {
               echo "1"; //Al devolver este numero, el ajax respondera un mensaje javascript el cual indica que no encontro costo del producto en la tabla 82 de movhos
           }

	 }


      //==================================================================================================================
  //==================================================================================================================

     // --> Eliminar e inactivar la informacion actual del paciente en la tabla 77(dietas) y 84(productos) de movhos
     function inactivar_pedidos($whis,$wing,$whab,$wcco,$fecha_consulta,$servicio,$wpatron, $wser_actual)
     {

         global $conex;
         global $wbasedato;
         global $whora;
         global $wusuario;


         //Funcion que consulta que patrones en la his e ing antes de hacer alguna operacion, para insertarlo en la auditoria.
        $wpatronesantes = consultar_patron_actual($whis, $wing, $servicio, $fecha_consulta);
        $wdatopatrones = explode("-", $wpatronesantes);
        $wultimo_patron = $wdatopatrones[0];

         //Borro lo que tenia registrado la historia en el servicio, habitacion y fecha
        $q = " DELETE FROM ".$wbasedato."_000077 "
            ."  WHERE movfec = '".$fecha_consulta."'"
            ."    AND movhis = '".$whis."'"
            ."    AND moving = '".$wing."'"
            ."    AND movhab = '".$whab."'"
            ."    AND movcco = '".$wcco."'"
            ."    AND movser = '".$servicio."'";
        $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
        $wfilas_afectadas = mysql_affected_rows();

        //Si para el servicio actual habian registros que eliminar entonces se devuelve la palabra desmarcar_patron a la funcion cerraremergente_grabar
        //y esto deschekeara los cajones activos y el color de fondo de los cajones chekeados.
        //Si se elimina un registro, se guardara la auditoria del servicio que se elimino, par ael caso de las dietas el registro se elimina y lo reemplaza el registro nuevo.
        if($wfilas_afectadas == '1' and $wultimo_patron != $wpatron and $wultimo_patron != '' )
        {

            $q_aud = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie, audcco ) "
                                    ."   VALUES ('".$wbasedato."','".$fecha_consulta."','".$whora."','".$whis."','".$wing."','".$servicio."','CANCELADO','".$wusuario."','C-".$wusuario."', '".$wultimo_patron."','".$wcco."')";
            $res_aud = mysql_query($q_aud,$conex) or die (mysql_errno().$q_aud." - ".mysql_error());

        }

        //Esta validacion marcara o desmarcara los patrones que tenga un paciente.
        switch ($wfilas_afectadas) {
            case '1':
                        if($servicio == $wser_actual and $wultimo_patron != $wpatron and $wultimo_patron != '' )
                            {
                            echo "desmarcar_patron";
                            }

                break;

            case '0':
                        if($servicio == $wser_actual and $wultimo_patron != $wpatron and $wultimo_patron != '')
                            {
                                echo "desmarcar_patron_dsn";
                            }
                break;


            default:
                break;
        }

        //Busco todos los productos para esta historia e ingreso en la fecha actual y servicio
        $q1 =    " UPDATE ".$wbasedato."_000084 "
                ."    SET detest = 'off'"
                ."  WHERE detfec = '".$fecha_consulta."'"
                ."    AND dethis = '".$whis."'"
                ."    AND deting = '".$wing."'"
                ."    AND detcco = '".$wcco."'"
                ."    AND detser = '".$servicio."'"
                ."    AND detpat = '".$wpatron."'";
        $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
		$wproductos_afectados = mysql_affected_rows();


     }


     //Consultar la composición actual de la dieta DSN en la fecha recibida desde la función procesar_datos_dsn.
     function consultarPedidoAnterior( $fecha_consulta, $whis, $wing, $whab, $wcco, $servicio ){
        global $conex;
        global $wbasedato;

        $pedidoAnterior = array();

        $query = " SELECT detpro
                     FROM {$wbasedato}_000084
                    WHERE Dethis = '$whis'
                      AND Deting = '$wing'
                      AND Detfec = '$fecha_consulta'
                      AND Detser = '$servicio'
                      AND Detest = 'on'
                      AND Detcco = '$wcco'
                    ORDER BY detpro";
        $rs    = mysql_query( $query, $conex );
        while( $row = mysql_fetch_assoc( $rs ) ){
            array_push( $pedidoAnterior, "{$row['detpro']}" );
        }
        return( $pedidoAnterior );
     }
     //Registra la informacion de DSN segun la fecha que sea recibida desde la funcion procesar_datos_dsn.
     function registrar_datos($whis,$wing,$whab,$wcco,$fecha_consulta, $servicio, $wpatron, $cod_producto, $cantidad_producto, $wvalorneto, $wserdsn, $wusuario_registra, $wpatron_asociado, $wobservacion , $wobs, $wint, $wconsec, $modificacion = false )
     {

         global $conex;
         global $wbasedato;
         global $wfecha;
         global $whora;
         global $wusuario;

         $wobserv_servicio = (array_key_exists($servicio,$wobservacion)) ? $wobservacion[$servicio] : '';

         $wvalorneto = $wvalorneto * $cantidad_producto;

           //Grabo los productos
        $q = " INSERT INTO ".$wbasedato."_000084 (   Medico       ,      Fecha_data,     Hora_data   ,        detfec       ,  dethis   ,  deting   ,      detser   ,    detpat    ,       detpro      ,      detcos     , detest,          detcan         ,    detcla    ,   detcco    ,     detcon    ,     Seguridad        ) "
            ."                            VALUES ('".$wbasedato."','".$fecha_consulta."','".$whora."','".$fecha_consulta."','".$whis."','".$wing."','".$servicio."','".$wpatron."','".$cod_producto."','".$wvalorneto."', 'on'  , '".$cantidad_producto."','".$wserdsn."', '".$wcco."' , '".$wconsec."','C-".$wusuario_registra."') ";
        $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

          //Cuenta si ya exite el registro, si existe hace una actualizacion, si no hace una insercion.
        $q = " SELECT COUNT(*) as cuantos "
            ."   FROM ".$wbasedato."_000077 "
            ."  WHERE movfec = '".$fecha_consulta."'"
            ."    AND movhab = '".$whab."'"
            ."    AND movdie = '".$wpatron."'"
            ."    AND movhis = '".$whis."'"
            ."    AND moving = '".$wing."'"
            ."    AND movser = '".$servicio."'"
            ."    AND movcco = '".$wcco."'"
            ."    AND movest = 'on'"
           ."GROUP BY movhis, moving";
        $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
        $row = mysql_fetch_array($res);

        if ($row['cuantos'] == 0)
//
            {
                $q1 = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,   movdie     , movind,   movest, movods, movint, movpco,   movval, movcan ,  movcco, movdsn, movnut, movobs,  Seguridad       ) "
                        ."      VALUES                       ('".$wbasedato."','".$fecha_consulta."','".$whora."','".$fecha_consulta."','".$whis."','".$wing."','".$whab."','".$servicio."','".$wpatron."', 'N'   ,'on', '".$wobserv_servicio."', '".$wint."', '".$wpatron."', '".$wvalorneto."','1', '".$wcco."', '".$wpatron_asociado."', '".$wusuario_registra."','".$wobs."','C-".$wusuario."') ";
                $res = mysql_query($q1,$conex) or die (mysql_errno().$q1." - ".mysql_error());

                 //Grabo la auditoria
                $accionAud = ( !$modificacion ) ? "PEDIDO" : "MODIFICO PEDIDO";
                    $q2 = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie, audcco, audods ) "
                                            ."   VALUES ('".$wbasedato."','".$fecha_consulta."','".$whora."','".$whis."','".$wing."','".$servicio."','$accionAud','".$wusuario_registra."','C-".$wusuario_registra."', '".$wpatron."','".$wcco."','".$wobserv_servicio."') ";
                $res2 = mysql_query($q2,$conex) or die (mysql_errno().$q2." - ".mysql_error());
            }
        else
            {

            //Aqui se suman los productos que estan registrados actualmente
            $q_valor =   " SELECT SUM(detcos) as valor "
                        ."   FROM ".$wbasedato."_000084 "
                        ."  WHERE detfec = '".$fecha_consulta."'"
                        ."    AND dethis = '".$whis."'"
                        ."    AND deting = '".$wing."'"
                        ."    AND detser = '".$servicio."'"
                        ."    AND detpat = '".$wpatron."'"
                        ."    AND detcco = '".$wcco."'"
                        ."    AND detest = 'on'";
            $res_valor = mysql_query($q_valor,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_valor." - ".mysql_error());
            $row_valor = mysql_fetch_array($res_valor);

            $wtotalactual = $row_valor['valor']; //Total de costo para los productos


            //Actualiza el valor total en la tabla 77
             $q =    " UPDATE ".$wbasedato."_000077 "
                    ."    SET movval = '".$wtotalactual."'"
                    ."  WHERE movfec = '".$fecha_consulta."'"
                    ."    AND movhab = '".$whab."'"
                    ."    AND movcco = '".$wcco."'"
                    ."    AND movhis = '".$whis."'"
                    ."    AND moving = '".$wing."'"
                    ."    AND movdie = '".$wpatron."'"
                    ."    AND movser = '".$servicio."'";
            $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


            }

     }

    //Esta funcion elimina o cancela la solicitud de dieta y los produtos.
   function cancelar_solicitudes($whis,$wing,$whab,$wcco,$fecha_consulta, $serv_can, $wpatron, $wser_actual)
     {

         global $conex;
         global $wbasedato;
         global $whora;
         global $wusuario;

		 $wfecha_hoy = date('Y-m-d');

		// --> Consultar si existe solicitud
		$q_existe = " SELECT COUNT(*) as cantidad
						FROM ".$wbasedato."_000077
					   WHERE movfec = '".$fecha_consulta."'
					     AND movhab = '".$whab."'
					     AND movcco = '".$wcco."'
				         AND movhis = '".$whis."'
					     AND moving = '".$wing."'
					     AND movdie = '".$wpatron."'
						 AND movser = '".$serv_can."'";
		$res_existe = mysql_query($q_existe,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_existe." - ".mysql_error());
		$row_existe = mysql_fetch_array($res_existe);

		if($row_existe['cantidad'] <= 0)
		{
			// --> Insertar el registro en la 77, en estado off, el cancelado en la auditoria
            $q1 = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,   movdie     , movind,   movest, movods, movint, movpco,   movval, movcan ,  movcco, movdsn, Seguridad       ) "
                        ."      VALUES                       ('".$wbasedato."','".$fecha_consulta."','".$whora."','".$fecha_consulta."','".$whis."','".$wing."','".$whab."','".$serv_can."','".$wpatron."', 'N'   ,'off', '', '', '".$wpatron."', '0','1', '".$wcco."', '', 'C-".$wusuario."') ";
            $res = mysql_query($q1,$conex) or die (mysql_errno().$q1." - ".mysql_error());

            $q2 = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie, audcco ) "
                                        ."   VALUES ('".$wbasedato."','".$fecha_consulta."','".$whora."','".$whis."','".$wing."','".$serv_can."','CANCELADO','".$wusuario."','C-".$wusuario."', '".$wpatron."','".$wcco."') ";
            $res2 = mysql_query($q2,$conex) or die (mysql_errno().$q2." - ".mysql_error());

		}
		else
		{

			 //Cambia de estado el registro en la tabla 77 de movhos a off
			$q =    " UPDATE ".$wbasedato."_000077 "
					."    SET movest = 'off'"
					."  WHERE movfec = '".$fecha_consulta."'"
					."    AND movhab = '".$whab."'"
					."    AND movcco = '".$wcco."'"
					."    AND movhis = '".$whis."'"
					."    AND moving = '".$wing."'"
					."    AND movdie = '".$wpatron."'"
					."    AND movser = '".$serv_can."'";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			 //Busco todos los productos para esta historia e ingreso en la fecha actual y servicio
			$q1 =    " UPDATE ".$wbasedato."_000084 "
					."    SET detest = 'off'"
					."  WHERE detfec = '".$fecha_consulta."'"
					."    AND dethis = '".$whis."'"
					."    AND deting = '".$wing."'"
					."    AND detcco = '".$wcco."'"
					."    AND detser = '".$serv_can."'"
					."    AND detpat = '".$wpatron."'";
			$res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());

			//Verifico si el registro de cancelado ya existe para la fecha y servicio, si no existe hago el registro de auditoria.
			$q_existe1= " SELECT id
							FROM ".$wbasedato."_000078
						   WHERE Fecha_data = '".$fecha_consulta."'
							 AND audacc = 'CANCELADO'
							 AND audhis = '".$whis."'
							 AND auding = '".$wing."'
							 AND auddie = '".$wpatron."'
							 AND audser = '".$serv_can."'";
			$res_existe1 = mysql_query($q_existe1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_existe1." - ".mysql_error());
			$num_existe1 = mysql_num_rows($res_existe1);

			if($num_existe1 == 0){

			//Grabo la auditoria de cancelado
			$q2 = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie, audcco ) "
									."   VALUES ('".$wbasedato."','".$fecha_consulta."','".$whora."','".$whis."','".$wing."','".$serv_can."','CANCELADO','".$wusuario."','C-".$wusuario."', '".$wpatron."','".$wcco."') ";
			$res2 = mysql_query($q2,$conex) or die (mysql_errno().$q2." - ".mysql_error());

			}

		}

		 //Esta validacion marcara o desmarcara los patrones que tenga un paciente.
		if($serv_can == $wser_actual and $fecha_consulta == $wfecha_hoy)
			{
			echo "desmarcar_patron_dsn";
			}

	 }

  	 //Registra informacion del los productos DSN.
	function procesar_datos_dsn($wemp_pmla, $wbasedato, $winf_prod, $wpatron, $whis, $wing, $wser, $wusuario, $whab, $wcco, $wfecha_interfaz, $wpatron_asociado, $wobservacion, $cant_product_servi)
     {


      global $conex;
      global $wfecha;

      $arr_principal = array();
      $array_observaciones = array();

	  //Incrementar consecutivo de la solicitud
	  $q = " UPDATE ".$wbasedato."_000001
		        SET connum=connum + 1
		      WHERE contip='DSN' ";
	  $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

	  //Consulta el consecutivo
	  $q = "SELECT connum
		      FROM " . $wbasedato . "_000001
		     WHERE contip='DSN' ";
	  $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	  $row = mysql_fetch_array($err);
	  $wconsec = $row['connum'];

      $array_observaciones_exp = explode("*|*", $wobservacion);
      $array_primario = explode("*|*", $winf_prod);

      // --> array para conocer cuantos productos habian por servicio
      $array_can_pro_serv     = explode('|', $cant_product_servi);
      $arr_cantidad_productos = array();

      foreach($array_can_pro_serv as $valores_cant)
      {
          $valores_cant = explode('-', $valores_cant);
          $arr_cantidad_productos[$valores_cant[0]] = $valores_cant[1];
      }

      //Aqui se recorren las observaciones por servicio
      foreach($array_observaciones_exp as $obs_serv => $string_obs )
        {
           $string_obs = explode("=>", $string_obs);
           $array_observaciones[$string_obs[0]] = strtolower(utf8_decode($string_obs[1]));
		   $array_observaciones[$string_obs[0]] = ucfirst($array_observaciones[$string_obs[0]]);
       }



       //Se crea un arreglo con los datos del servicio, productos y sus caracteristicas.
      foreach($array_primario as $valores_primarios)
      {
          $info_valores = explode('-', $valores_primarios);
          $arr_principal[$info_valores[0]][$info_valores[2]]  = array("serv_dsn"=>$info_valores[3],"usuario_registra"=>$info_valores[4], "cantidad"=>$info_valores[5],"valor"=>$info_valores[6]);
      }

	    //OBSERVACIONES DEL PACIENTE
        //Busco si hay alguna observacion en el ingreso actual del paciente
        $q_obs =  " SELECT MAX(CONCAT(fecha_data,hora_data)),movobs "
                ."   FROM ".$wbasedato."_000077 "
                ."  WHERE movhis  = '".$whis."'"
                ."    AND moving  = '".$wing."'"
                ."    AND movser  = '".$wser."'"
                ."    AND movobs != '' "
                ."  GROUP BY 2 "
                ."  ORDER BY 1 DESC ";
        $res_obs = mysql_query($q_obs,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_obs." - ".mysql_error());
        $row_mov = mysql_fetch_array($res_obs);
        $wobs=trim($row_mov[1]);


        //INTOLERANCIAS DEL PACIENTE
        //Busco si hay alguna intolerancias en el ingreso actual del paciente
        $q_int =  " SELECT MAX(CONCAT(fecha_data,hora_data)),movint "
                ."   FROM ".$wbasedato."_000077 "
                ."  WHERE movhis  = '".$whis."'"
                ."    AND moving  = '".$wing."'"
                ."    AND movser  = '".$wser."'"
                ."    AND movobs != '' "
                ."  GROUP BY 2 "
                ."  ORDER BY 1 DESC ";
        $res_int = mysql_query($q_int,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_int." - ".mysql_error());
        $row_int = mysql_fetch_array($res_int);
        $wint=trim($row_int[1]);

		$wservicio_aux = '';

        // --> recorre array principal
        foreach($arr_principal as $servicio => $array_productos )
        {
            $productosActuales   = array();
            $array_productos_aux = $array_productos;
            foreach($array_productos_aux as $cod_producto_aux => $datos_aux ){
                array_push( $productosActuales, "$cod_producto_aux" );
            }
            sort($productosActuales);

            $horarios = consultar_horarios_servicio($servicio);
            $arr_horarios = explode('-', $horarios);
            $hora_actual = date('H:i:s');
            $hora_fin_servicio = $arr_horarios[1];


            //Se valida la hora final del servicio, si es mayor a la actual el registro sera para el dia siguiente.
            if($hora_actual > $hora_fin_servicio){
                $fecha_consulta = date("Y-m-d", strtotime("$wfecha+ 1 day"));
				}
            else{
                $fecha_consulta = $wfecha;
				$wservicio_aux = $servicio;
				}

            //-->  Validar si ya existe servicio programado con las características indicadas, con el propósito de verificar si es una modificación o un pedido nuevo. 2019-09-24
            $q = " SELECT count(*) cantidad
                    FROM {$wbasedato}_000077
                   WHERE movfec = '$fecha_consulta'
                     AND movhis = '$whis'
                     AND moving = '$wing'
                     AND movhab = '$whab'
                     AND movcco = '$wcco'
                     AND movser = '$servicio'";
            $modificacion = false;
            $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
            $rex = mysql_fetch_row( $err );
            if( $rex[0]*1 > 0 ){
                //--> Se hace una comparación de los productos solicitados nuevos con los anteriores
                $pedidoAnterior = consultarPedidoAnterior( $fecha_consulta, $whis, $wing, $whab, $wcco, $servicio );
                $diferencia1    = array_diff( $pedidoAnterior, $productosActuales );
                $diferencia2    = array_diff( $productosActuales, $pedidoAnterior );
                if( count($diferencia1) > 0 or count($diferencia2) > 0 ){
                    $modificacion = true; //--> esto es temporal, hay que verificar que si existan cambios.
                }
            }else{
                $modificacion = false;
            }

            //Aqui se inactivan las solicitudes y los productos.
            inactivar_pedidos($whis,$wing,$whab,$wcco,$fecha_consulta, $servicio, $wpatron, $wser);

            // Aqui se realizan las siguientes acciones
            // --> Hacer pedido en la 77
            // --> Recorre $array_productos, e insertalo en la 84
            // --> Regisro de la auditoria.
            foreach($array_productos as $cod_producto => $datos_producto)
            {

                registrar_datos($whis,$wing,$whab,$wcco,$fecha_consulta, $servicio, $wpatron, $cod_producto, $datos_producto['cantidad'], $datos_producto['valor'], $datos_producto['serv_dsn'], $datos_producto['usuario_registra'], $wpatron_asociado, $array_observaciones, $wobs, $wint, $wconsec, $modificacion);

            }

			//===============================================================================================================
			//En este segmento se verifica si el paciente tiene pedidos automaticos de dsn para el dia siguiente, si es asi
			//cancelará los productos y pondrá los nuevos solicitados, con la fecha del dia siguiente.
			//===============================================================================================================
			$wactivo = consultarservgrabado_dsn($whis, $wing, $wservicio_aux, $wcco, 'dsndesdeservicio');

			if($wservicio_aux != '' and $wactivo == 'on'){

			$wmanana_aux = time()+(1*24*60*60); //Suma un dia
			$wmanana = date('Y-m-d', $wmanana_aux); //Formatea dia

			$fecha_consulta = $wmanana;

			//Aqui se inactivan las solicitudes y los productos.
			inactivar_pedidos($whis,$wing,$whab,$wcco,$fecha_consulta, $wservicio_aux, $wpatron, $wser);

				// Aqui se realizan las siguientes acciones
				// --> Hacer pedido en la 77
				// --> Recorre $array_productos, e insertalo en la 84
				// --> Regisro de la auditoria.
				foreach($array_productos as $cod_producto => $datos_producto)
				{

					registrar_datos($whis,$wing,$whab,$wcco,$fecha_consulta, $wservicio_aux, $wpatron, $cod_producto, $datos_producto['cantidad'], $datos_producto['valor'], $datos_producto['serv_dsn'], $datos_producto['usuario_registra'], $wpatron_asociado, $array_observaciones, $wobs, $wint, $modificacion);

				}

			}
			//=========================================================================================================

        }

        // --> Servicios que cancelaron
        foreach($arr_cantidad_productos as $serv_can => $cant_prod)
        {

            if($cant_prod > 0 && !array_key_exists($serv_can, $arr_principal))
            {

                //Se valida la hora final del servicio, si es mayor a la actual el registro sera cancelado para mañana
                $horarios = consultar_horarios_servicio($serv_can);
                $arr_horarios = explode('-', $horarios);
                $hora_actual = date('H:i:s');
                $hora_fin_servicio = $arr_horarios[4];

                if($hora_actual > $hora_fin_servicio)
                    $fecha_consulta = date("Y-m-d", strtotime("$wfecha+ 1 day"));
                else
                    $fecha_consulta = $wfecha;

                cancelar_solicitudes($whis,$wing,$whab,$wcco,$fecha_consulta, $serv_can, $wpatron, $wser);

				//---------------------------------------------------------------------------------------------------------
				//Si el servicio es cancelado, se verifica si hay registros para el dia siguiente y tambien se cancelan.
				//---------------------------------------------------------------------------------------------------------
				$wactivo = consultarservgrabado_dsn($whis, $wing, $serv_can, $wcco, 'dsndesdeservicio');

				if($wactivo == 'on'){

				$wmanana_aux = time()+(1*24*60*60); //Suma un dia
				$wmanana = date('Y-m-d', $wmanana_aux); //Formatea dia

				$fecha_consulta = $wmanana; //Cambio la variable para el dia siguiente.

				cancelar_solicitudes($whis,$wing,$whab,$wcco,$fecha_consulta, $serv_can, $wpatron, $wser);

				}


            }
        }
     }

  //===============================


   ///Permite la insercion de los productos DSN de forma automatica, o sea, al traerlos del dia anterior.
	function procesar_datos_dsnauto($wemp_pmla, $wbasedato, $whis, $wing, $whab, $wcco, $wpatron_nutricion, $control_solicitud )
     {

	  global $conex;
	  global $whora;
      global $whce;
      global $wusuario;

      $wayerfecha = time()-(1*24*60*60); //Resta un dia
      $wayer1 = date('Y-m-d', $wayerfecha); //Formatea dia
      $wfecha_actual = date("Y-m-d");

      $ultimo_nutricionista = buscar_ult_nutricionista($whis, $wing); //Ultimo nutricionista que registro datos para el paciente
      $wdatos_nutrinicionista = explode("-", $ultimo_nutricionista);
      $wusuario_nutri = $wdatos_nutrinicionista[0]; //codigo del nutricionista

      //Consulto los servicio activos
      $q =   " SELECT sercod "
            ."   FROM ".$wbasedato."_000076 "
            ."  WHERE serest = 'on'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);

      //Recorro cada servicio
	  for ($i=1;$i<=$num;$i++)
	     {
            $row = mysql_fetch_array($res);

            $wservicios = $row['sercod'];

			//Verifico si el servicio esta activo para el dia de hoy
            $wactivo = consultarservgrabado_dsn($whis, $wing, $wservicios, $wcco, $control_solicitud);

			//Si no esta activo y la variable $control_solicitud sea dsndesdeservicio, cambiara la variable de fecha_actual por el dia siguiente y consultara con la variable $wayer1
			//los productos para hoy. Jonatan Lopez 22 Abril de 2014.
			if($wactivo == 'off' and $control_solicitud == 'dsndesdeservicio'){

			$wmanana_aux = time()+(1*24*60*60); //Suma un dia
			$wmanana = date('Y-m-d', $wmanana_aux); //Formatea dia

			$wfecha_actual = $wmanana; //La fecha actual sera la fecha de mañana y la variable $wayer1 sera la fecha de hoy.
			$wayer1 = date('Y-m-d'); //Hoy.

			}

          //Si no hay registros para el dia de hoy, consulto los productos del dia anterior
          if($wactivo == 'off')
            {

            //Consulto los productos por servicio
            $q = " SELECT detser, detpat, detpro, detcos, detcan, detcla, Fecha_data"
                ."   FROM ".$wbasedato."_000084 "
                ."  WHERE detfec = '".$wayer1."'" //Ayer
                ."    AND dethis = '".$whis."'"
                ."    AND deting = '".$wing."'"
                ."    AND detpat = '".$wpatron_nutricion."'"
                ."    AND detcco = '".$wcco."' "
                ."    AND detser = '".$wservicios."' "
                ."    AND detest = 'on' "
				."  GROUP BY detpro"
                ."  ORDER BY detser";
            $respro = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num_prod = mysql_num_rows($respro);

            while ($rowpro = mysql_fetch_array($respro))
                    {

                    $wserdsn = $rowpro['detser']; //Servicio en el que se registra el producto.
                    $wpatrondsn = $rowpro['detpat']; //Patron asociado.
                    $wcodigo = $rowpro['detpro']; //Codigo del producto.
                    $wcantidad = $rowpro['detcan']; //Cantidad.
                    $wsercla = $rowpro['detcla']; //Servicio DSN
                    $wcosto = $rowpro['detcos']; //Servicio DSN

                    $wobservaciones = traer_observaciones_dsn($whis, $wing, $wayer1, $wservicios, $wcco);
                    $wpatron_asoc_dsn = traer_patron_asocia_dsn($whis, $wing, $wayer1, $wservicios, $wcco);

                    //Busco la informacion del producto
                    $q = " SELECT rpcvan, rpcvac, rpcfec "
                        ."   FROM ".$wbasedato."_000131 "
                        ."  WHERE rpccod = '".$wcodigo."'"
                        ."    AND rpcpat = '".$wpatron_nutricion."'"
                        ."    AND rpcest = 'on'";
                    $rescos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $rowcos = mysql_fetch_array($rescos);

                    //Valida el valor actual o el valor anterior
                    if ($wfecha_actual>= $rowcos['rpcfec'])
                        {
                        $wvalorneto=$rowcos['rpcvac'];            //Asigno el valor actual
                        }
                        else
                        {
                        $wvalorneto=$rowcos['rpcvan'];          //Asigno el valor anterior a la fecha de cambio
                        }

                        $wvalorneto = $wvalorneto * $wcantidad;
                        //Si hay productos para el dia anterior hara los registros en la tabla 77 y 84
                        if($num_prod > 0)
                        {


                            $q_prod = " INSERT INTO ".$wbasedato."_000084 (   Medico       ,   Fecha_data,   Hora_data,   detfec  ,   dethis  ,   deting  ,   detser  ,   detpat     ,   detpro     ,  detcos         , detest, detcan , detcla, detcco,        Seguridad        ) "
                                ."                            VALUES ('".$wbasedato."','".$wfecha_actual."','".$whora."','".$wfecha_actual."','".$whis."','".$wing."','".$wservicios."','".$wpatrondsn."','".$wcodigo."','".$wvalorneto."', 'on'  , '".$wcantidad."','".$wsercla."', '".$wcco."' , 'C-".$wusuario."') ";
                            $res_prod = mysql_query($q_prod,$conex) or die (mysql_errno().$q_prod." - ".mysql_error());


                                //Cuenta si ya exite el registro, si existe hace una actualizacion, si no hace una insercion.
                                $q_reg = " SELECT COUNT(*) as cuantos "
                                        ."   FROM ".$wbasedato."_000077 "
                                        ."  WHERE movfec = '".$wfecha_actual."'"
                                        ."    AND movhab = '".$whab."'"
                                        ."    AND movdie = '".$wpatrondsn."'"
                                        ."    AND movhis = '".$whis."'"
                                        ."    AND moving = '".$wing."'"
                                        ."    AND movser = '".$wservicios."'"
                                        ."    AND movcco = '".$wcco."'"
                                        ."    AND movest = 'on'"
                                    ." GROUP BY movhis, moving";
                                $res_reg = mysql_query($q_reg,$conex) or die (mysql_errno().$q_reg." - ".mysql_error());
                                $row_reg = mysql_fetch_array($res_reg);

								//OBSERVACIONES DEL PACIENTE
								//Busco si hay alguna observacion en el ingreso actual del paciente
								$q_obs = " SELECT MAX(CONCAT(fecha_data,hora_data)),movobs "
										."   FROM ".$wbasedato."_000077 "
										."  WHERE movhis  = '".$whis."'"
										."    AND moving  = '".$wing."'"
										."    AND movobs != '' "
										."  GROUP BY movobs "
										."  ORDER BY 1 DESC ";
								$res_obs = mysql_query($q_obs,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								$row_obs = mysql_fetch_array($res_obs);
								$wobs_paciente = trim($row_obs['movobs']);

								//Valida si hay observacion en el ultimo registro para la historia e ingreso, si no es asi, muestra las observaciones del kardex.
								if ($wobs_paciente == '')
								{
									$wobs_paciente = traer_observ_alimentacion($whis, $wing);
								}
								else
								{
									$wobs_paciente = trim($row_obs['movobs']);
								}

                                if ($row_reg['cuantos'] == 0)

                                    {
                                       $q1 = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,   movdie     , movind,   movest, movods, movint, movpco,   movval, movcan,  movaut  ,  movcco, movdsn, movnut, movobs,  Seguridad       ) "
                                             ."      VALUES                       ('".$wbasedato."','".$wfecha_actual."','".$whora."','".$wfecha_actual."','".$whis."','".$wing."','".$whab."','".$wserdsn."','".$wpatrondsn."', 'N'   ,'on', '".$wobservaciones."', '".$wint."', '".$wpatrondsn."', '".$wvalorneto."','1','on', '".$wcco."', '".$wpatron_asoc_dsn."','".$wusuario_nutri."','".$wobs_paciente."' , 'C-".$wusuario."') ";
                                       $res1 = mysql_query($q1,$conex) or die (mysql_errno().$q1." - ".mysql_error());

                                    }
								else
									{

                                    //Aqui se suman los productos que estan registrados actualmente
                                    $q_valor =   " SELECT SUM(detcos) as valor "
                                                ."   FROM ".$wbasedato."_000084 "
                                                ."  WHERE detfec = '".$wfecha_actual."'"
                                                ."    AND dethis = '".$whis."'"
                                                ."    AND deting = '".$wing."'"
                                                ."    AND detser = '".$wservicios."'"
                                                ."    AND detpat = '".$wpatrondsn."'"
                                                ."    AND detcco = '".$wcco."'"
                                                ."    AND detest = 'on'";
                                    $res_valor = mysql_query($q_valor,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_valor." - ".mysql_error());
                                    $row_valor = mysql_fetch_array($res_valor);

                                    $wtotalactual = $row_valor['valor']; //Total de costo para los productos

									 $q2 =    " UPDATE ".$wbasedato."_000077 "
                                            ."    SET movval = '".$wtotalactual."'"
                                            ."  WHERE movfec = '".$wfecha_actual."'"
                                            ."    AND movhab = '".$whab."'"
                                            ."    AND movcco = '".$wcco."'"
                                            ."    AND movhis = '".$whis."'"
                                            ."    AND moving = '".$wing."'"
                                            ."    AND movdie = '".$wpatrondsn."'"
                                            ."    AND movser = '".$wservicios."'";
                                    $res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());


                                    }


                               //Busco si la historia ya tiene registrado el servicio en la auditoria
                               $q_aud =  " SELECT COUNT(*) as cuantos "
										."   FROM ".$wbasedato."_000078 "
										."  WHERE audhis     = '".$whis."'"
										."    AND auding     = '".$wing."'"
										."    AND audser     = '".$wservicios."'"
										."    AND audcco     = '".$wcco."'"
										."    AND fecha_data = '".$wfecha_actual."'"
										."    AND audacc     = 'PEDIDO'";
                                $res_aud = mysql_query($q_aud,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                $row_aud = mysql_fetch_array($res_aud);

                                if ($row_aud['cuantos'] == 0)
                                    {
                                        //Grabo la auditoria
                                        $q_aud1 = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie, audcco, audods ) "
                                                                ."   VALUES ('".$wbasedato."','".$wfecha_actual."','".$whora."','".$whis."','".$wing."','".$wservicios."','PEDIDO','".$wusuario."','C-".$wusuario."', '".$wpatrondsn."','".$wcco."', '".$wobservaciones."') ";
                                        $err = mysql_query($q_aud1,$conex) or die (mysql_errno().$q_aud1." - ".mysql_error());

									}else{

										//Si el paciente tiene una cancelacion del dia anterior y necesitan retomarla, debe permitir hacer el registro de pedido.
										$q_aud = " SELECT COUNT(*) as cuantos "
												."   FROM ".$wbasedato."_000078 "
												."  WHERE audhis     = '".$whis."'"
												."    AND auding     = '".$wing."'"
												."    AND audser     = '".$wservicios."'"
												."    AND audcco     = '".$wcco."'"
												."    AND fecha_data = '".$wfecha_actual."'"
												."    AND audacc     = 'CANCELADO DESDE EL DIA ANTERIOR'";
											$res_aud = mysql_query($q_aud,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
											$row_aud = mysql_fetch_array($res_aud);

											if ($row_aud['cuantos'] > 0){

												//Grabo la auditoria
												$q_aud1 = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie, audcco, audods ) "
																		."   VALUES ('".$wbasedato."','".$wfecha_actual."','".$whora."','".$whis."','".$wing."','".$wservicios."','PEDIDO','".$wusuario."','C-".$wusuario."', '".$wpatrondsn."','".$wcco."', '".$wobservaciones."') ";
												$err = mysql_query($q_aud1,$conex) or die (mysql_errno().$q_aud1." - ".mysql_error());

											}
									}

                        }
                    }
            }
         }
     }


  //==================================================================================================================
  //==================================================================================================================


  //==================================================================================================================
  //==================================================================================================================
  //Esta funcion permite identificar el que patron puede estar habilitado en un rango de horas
  function analisishoras($whorainicial, $whorafinal, $wfec)
	{

		  global $wfecha;
		  global $whora;


		  $wfechahoy = explode("-",$wfec);
		  $whorainicio = explode(":",$whorainicial);
		  $whorainicio1 = mktime($whorainicio[0],$whorainicio[1],$whorainicio[2],$wfechahoy[1],$wfechahoy[2],$wfechahoy[0]);

		  $whorafin = explode(":",$whorafinal);

		  $whorafinal1 = mktime($whorafin[0],$whorafin[1],$whorafin[2],$wfechahoy[1],$wfechahoy[2],$wfechahoy[0]);

		  $whoraactual = explode(":",$whora);
		  $whoraactual1 = mktime($whoraactual[0],$whoraactual[1],$whoraactual[2],$wfechahoy[1],$wfechahoy[2],$wfechahoy[0]);


		  //Esta validacion permite saber si se esta pasando de dia por ejemplo : Hora inicio: (2012-05-15 20:00:00), hora fin: (2012-05-16 04:00:00),
		  //si esto ocurre se le debe sumar un dia a la hora final

		  if($whorainicio1 > $whorafinal1)
			{

			$whorafinal1 = $whorafinal1 + (24 * 60 * 60);

			if($whoraactual1 < $whorainicio1)
				{
				$whorainicio1 = $whorainicio1 - (24 * 60 * 60);
				}
			}

		  // Retorno varios elementos para utilizarlos en las validaciones del patron configurado en la base de datos, tabla movhos_000076, campo Serpda
		  return array('whoraini'=>$whorainicio1, 'whorafin'=>$whorafinal1, 'whoraactual'=>$whoraactual1);

	}

  //==================================================================================================================
  //==================================================================================================================
  //Consulta en que servicio se encuentra el sistema en la hora actual.
    function consultar_servicioactual()
    {

        global $conex;
        global $wbasedato;
        global $whora;

        $q = " SELECT sercod, sernom "
            ."   FROM ".$wbasedato."_000076 "
            ."  WHERE serhin <= '".$whora."'"
            ."    AND serhad >= '".$whora."'"
            ."    AND serest  = 'on' ";
        $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
        $num = mysql_num_rows( $res );

        if ($num > 0)
            {
            $row = mysql_fetch_array( $res );
            $wcodser   = $row[0];

            }
            else
            $wcodser="";

        return $wcodser;

    }


  //==================================================================================================================
  //==================================================================================================================
// Esta funcion permite identificar si el servicio esta grabado para la his e ing en la fecha actual,
// si la respuesta es on entonces no ejecutara funciones de tipo automatico.
  function consultarservgrabado($wser, $whis, $wing)
  {

      global $wbasedato;
	  global $conex;
      global $wfecha;


    $q1= "  SELECT movser"
        ."    FROM ".$wbasedato."_000077 "
        ."   WHERE movest = 'on' "
        ."     AND movfec = '".$wfecha."'"
        ."     AND movhis = '".$whis."'"
        ."     AND moving = '".$wing."'"
        ."     AND movser = '".$wser."'";
    $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res1);

    if ($num > 0)
        {
            $wsergra="on";
        }
    else
        {
            $wsergra="off";
        }

    return $wsergra;
  }


  //==================

  function consulta_patron_posqx($wpatronesantes)
  {

      global $wbasedato;
	  global $conex;


   //Traigo quien es el patron posqx que tenia el paciente.
    $q = " SELECT diecod "
        ."   FROM ".$wbasedato."_000041 "
        ."  WHERE diepqu  = 'on'"
        ."    AND dieest  = 'on'"
        ."    AND diecod  = '".$wpatronesantes."'";
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row = mysql_fetch_array($res);
    $wpatronposqx = $row[0];

    return $wpatronposqx;
  }


  //=======================
 //Verifica si el patron DSN ya esta grabado para una his e ing .
function consultarservgrabado_dsn($whis, $wing, $wser, $wcco, $control_solicitud)
   {

    global $wbasedato;
	global $conex;
    global $wfecha;

	$wfecha_actual = $wfecha;

	$wmanana_aux = time()+(1*24*60*60); //Suma un dia
    $wmanana = date('Y-m-d', $wmanana_aux); //Formatea dia

	if($control_solicitud == 'dsndesdeservicio'){

	 $wfecha_actual = $wmanana;

	}

    $q1= "  SELECT movser "
        ."    FROM ".$wbasedato."_000077  "
        ."   WHERE movfec = '". $wfecha_actual."'"
        ."     AND movhis = '".$whis."'"
        ."     AND moving = '".$wing."'"
        ."     AND movcco = '".$wcco."'"
        ."     AND movest = 'on'" //Debe validar que este activo para que traiga lo del dia anterior.
        ."     AND movser = '".$wser."'";
    $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
    $num = mysql_num_rows($res1);

    if ($num > 0)
        {
            $wsergra="on";
        }
    else
        {
            $wsergra="off";
        }

    return $wsergra;
  }


  //==================================================================================================================
  //==================================================================================================================


  //==================================================================================================================
  //==================================================================================================================
  // Verifica si el sistema se encuentra en horario de adicion.
  function consultarHorario($wser, $wfec)
  {

      global $wbasedato;
	  global $conex;
      global $whora;

    //Consulto si esta en horario de adicion
   $q1 = "  SELECT serhia"
        ."    FROM ".$wbasedato."_000076 "
        ."   WHERE serest = 'on' "
        ."     AND sercod = '".$wser."'";
    $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row1 = mysql_fetch_array($res1);
    $whorafinalser = $row1[0];

    if ($whora > $whorafinalser)
        {

          $wadi_ser="on";

        }
    else
        {
            $wadi_ser="off";

        }

    return $wadi_ser;
  }

  //==================================================================================================================

  //==================================================================================================================


  function consultar_patron_posq($wchequeados)
  {

        global $wbasedato;
        global $conex;

       $q =  " SELECT diepqu"
            ."   FROM ".$wbasedato."_000041"
            ."  WHERE dieest = 'on' "
            ."    AND diecod in '".$wchequeados."'";
        $res_die = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row_die = mysql_fetch_array($res_die);

        return $row_die[0];
  }




  //=================================================================================================================
  //================================================================================================================
  //Consulta el ultimo nutrinionista que registro datos para el paciente
  function buscar_ult_nutricionista($whis, $wing)

   {

      global $conex;
      global $wbasedato;
      global $whce;
      global $wemp_pmla;

      $wdatos_rol = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
      $winf_nutricion_dsn = explode("-", $wdatos_rol);
      $wrolnutricion = $winf_nutricion_dsn[0];// Rol nutricionistas


       //Fecha de ingreso del paciente
        $q_ingreso = " SELECT Fecha_data "
                    ."   FROM ".$wbasedato."_000016 "
                    ."  WHERE inghis = '".$whis."'"
                    ."    AND inging = '".$wing."'";
        $res_ingreso = mysql_query($q_ingreso,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_ingreso." - ".mysql_error());
        $row_ingreso = mysql_fetch_array($res_ingreso);
        $wfecha_ingreso_paciente = $row_ingreso['Fecha_data'];

        //Busco si el registro para la his e ing existe en el servicio actual
        $q = " SELECT a.movnut "
            ."   FROM ".$whce."_000020, ".$wbasedato."_000077 a"
            ."  WHERE movhis = '".$whis."'"
            ."    AND moving = '".$wing."'"
            ."    AND a.Fecha_data >= '".$wfecha_ingreso_paciente."'"
            ."    AND a.movnut = usucod "
            ."    AND usurol = '".$wrolnutricion."'"
            ." ORDER BY a.Fecha_data desc limit 1 ";
        $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row_val=mysql_fetch_array($res_mov);

        $wcodigo_nutri = $row_val['movnut'];

        //Nombre del usuario
        $q_usuario = " SELECT descripcion "
                    ."   FROM usuarios "
                    ."  WHERE codigo = '".$wcodigo_nutri."'";
        $res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuario." - ".mysql_error());
        $row_usuario = mysql_fetch_array($res_usuario);
        $wnombre = $row_usuario['descripcion'];

        return $wcodigo_nutri."-".$wnombre;



  }


  //Funcion que pinta los productos para el patron DSN.
  function composicion_patrondsn($wpatron, $fila, $whis, $wing, $wser, $f, $c, $whab, $wfec)
     {

        global $wbasedato;
        global $conex;
        global $wfecha;
        global $wemp_pmla;
        global $wusuario;
        global $wcco;
        global $wfec;
		global $wlimite_caracteres_dsn;

        //Analizo si el patron no valida horario.
        $wnovalidah = validahorariopatron($wpatron);


         //Busco los productos relacionados con este patron.
        $q = " SELECT clades, claord, prodes, rpcvan, rpcfec, rpcvac, rpccod, rpccla, claser"
            ."   FROM ".$wbasedato."_000082, ".$wbasedato."_000083, ".$wbasedato."_000131 "
            ."  WHERE rpcpat = '".$wpatron."'"
            ."    AND rpccla = clacod "
            ."    AND rpccod = procod"
            ."    AND rpcser != '".$wser."'"
            ."    AND proest = 'on' "
            ."    AND claest = 'on' "
            ."    AND rpcest = 'on' "
            ."  ORDER BY 2, 3 ";

        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0)
            {
            $wvar="";

            $row = mysql_fetch_array($res);
            $word = $row['clades'];

            echo "<tr>";
            $wposicioninput = 2;
            //Cantidad de opciones por Patron
            for ($i=0; $i < $num; $i++)
                {

                $word = $row['claord'];
                $wsercla = $row['rpccla'];
                $wserdsn = $row['claser'];

                //Consulta la ultima fecha de registro en la tabla de movmientos (77 movhos)
                $wfecha_consulta = consultar_ult_reg($whis, $wing, $wserdsn, $wcco, $wpatron);

                $wmismosdatos = traer_serv_igual($wserdsn); //Servicio del que se marcaran los mismos productos (Almuerzo == Comida)
                $wserigual = explode("-", $wmismosdatos); //La funcion trae servicio y nombre del servicio, entonces se separan.

                $idtabla = "tabla_servicio-$wserdsn"; //Identificador de cada servicio en DSN (Div de la tabla).
                echo "<td valign='top' bgcolor='CCFFFF'>";
                echo "<table valign='top' id='tabla_servicio-".$wserdsn."'>";
                echo "<tr><td colspan=3 align=center><font size=2>".traer_horario_servicio($wserdsn)."</font></td></tr>";
                echo "<tr><td colspan=3 align=center><font size=3><b>".$row['clades']."</b></font></td></tr>";//2019-10-10

                //Esta validacion se refiere a que el boton de cancelar pedido solo se mostrara en el servicio en el que se encuentre el sistema.
                if ($wser == $wserdsn)
                    {
                   // echo "<tr><td colspan=3 align=center><input type='button' value='Cancelar Pedido' onclick='descheckTodos(\"".$idtabla."\",\"".$wemp_pmla."\", \"".$wbasedato."\",\"".$wserdsn."\",\"".$wser."\",\"".$whis."\", \"".$wing."\", \"".$wfec."\", \"".$whab."\", \"".$wpatron."\", \"".$wusuario."\", \"".$wcco."\" );'></td></tr>";
                    }
                //Aqui se valida si el servicio debe tener los mismo productos de otro servicio
                if (trim($wserigual[0]) != '' and $wserigual[0] != 'NO APLICA')
                    {
                        echo "<tr><td colspan=3 align=center><input type='checkbox' id='servicioigual' $wchk value='' onclick='marcarservigual(\"".$idtabla."\",\"".$wpatron."\", \"".$wserigual[0]."\", \"".$wsercla."\" );'>Igual al  ".strtolower($wserigual[1])."</td></tr>";
                    }
                $cantidad_patrones_x_servicio = 0;
                while ($i < $num and $word == $row[1])
                    {

                        $wdescripprod = $row[2];

                        if ($wfecha >= $row[4])
                            {
                            $wvalorneto=$row[5];            //Asigno el valor actual
                            }
                            else
                            {
                            $wvalorneto=$row[3];          //Asigno el valor anterior a la fecha de cambio
                            }

                        $wcod = $row['rpccod'];

                        //Busco si esta opcion esta grabada para el paciente en la tabla 000084
                        $q = " SELECT detpro, detcan, Fecha_data "
                            ."   FROM ".$wbasedato."_000084 "
                            ."  WHERE detfec = '".( empty( $wfecha_consulta ) ? '0000-00-00' : $wfecha_consulta )."'"
                            ."    AND dethis = '".$whis."'"
                            ."    AND deting = '".$wing."'"
                            ."    AND detpat = '".$wpatron."'"
                            ."    AND detpro = '".$wcod."'"
                            ."    AND detcco = '".$wcco."'"
                            ."    AND detser = '".$wserdsn."'"
                            ."    AND detest = 'on' "
							."ORDER BY Fecha_data DESC";
                        $respro = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                        $rowpro = mysql_fetch_array($respro);
						$numpro = mysql_num_rows($respro);

						//Variable que determina si se puede poner una observacion en el cajon de obs.
						if($numpro > 0){
							$can_pro++;
						}

                        //Si esta grabada entonces le da atributos al productos en la pantalla
                        if ($rowpro['detpro'] > 0)
                        {
                            $cantidad_patrones_x_servicio++;
                            $wchk = "CHECKED";
                            $wcolor =  '#FAFC7C';
                            $wvar=$wvar.$row['Fecha_data'].",";
                            $wvalue = $rowpro['detcan'];
                            $wfecharegistro = $rowpro['Fecha_data'];
                        }
                        else
                            {
                            $wchk = "UNCHECKED";
                            $wcolor = '';
                            $wvalue = '1';
                            $wfecharegistro = $wfecha;

                            }


                        echo "<tr>";
                        echo "<td align=left class='td".$wpatron."".$wserdsn." td_".$wserdsn."-".$wpatron."-".$wcod."-".$wsercla."-".$wusuario." td-".$wserdsn."-".$wcod."' bgcolor='$wcolor' id='chk".$wpatron.$i."'><INPUT TYPE='checkbox' onkeypress='return noenter(event)' id='".$wserdsn."-".$wpatron."-".$wcod."-".$wsercla."-".$wusuario."' class='cajon-".$wserdsn."-".$wcod."'' valor_neto='".$wvalorneto."' name='checkbox' ".$wchk." onclick='cambiar_color_td(this,\"".$wpatron."\",\"".$i."\");'><input size='3' class='input-".$wserdsn."-".$wcod."' onkeydown='this.value = this.value.replace(/[^0-9|\.]/g, \"\");' onblur='validar_cifra(this);' maxlength='5' value='".$wvalue."' id='cantidad-".$wpatron."-".$wcod."-".$wsercla."' type='text'></input>".$wdescripprod."</td>";//2019-10-10
                        echo "</tr>";

                        $row = mysql_fetch_array($res);

                        $i++;
                        $wposicioninput+=2;

                    }

                    echo "<input type='hidden' servicio = '".$wserdsn."' hidden_cant_pat='si' value=".$cantidad_patrones_x_servicio.">";
                    $i=$i-1;

					if($can_pro > 0){
						//Trae las observaciones que han sido guardadas en la ventana emergente de DSN.
						$wobservaciones = traer_observaciones_dsn($whis, $wing, $wfecha_consulta, $wserdsn, $wcco);

						if (trim($wobservaciones) == '')
						{
							$wfecha_consulta = date("Y-m-d", strtotime("$wfecha+ 1 day"));

							//Trae las observaciones que han sido guardadas en la ventana emergente de DSN.
							$wobservaciones = traer_observaciones_dsn($whis, $wing, $wfecha_consulta, $wserdsn, $wcco);
						}
					}
                    echo "<td><textarea id='obs-".$wserdsn."' maxlength='".$wlimite_caracteres_dsn."' class='textareadietas obser-".$wserdsn."' tipo='observacion' historia='$whis' ingreso='$wing' cols='29' rows='5' >".$wobservaciones."</textarea><div style='display:block; font-size:10px; color:red;'>M&aacuteximo ".$wlimite_caracteres_dsn." car&aacutecteres.</div></td>";//2019-10-10
                    echo "</table>";
                    echo "</td>";

                    $wreadonly = ''; //Se inicializa de nuevo la variable para que las otras areas de texxto puedan ser editadas.

                }

            echo "</tr>";
            echo "<tr>";
            echo "<td align=center colspan=$i>";
            echo "<input type='button' onclick='cerraremergente_grabar(this,\"".$fila."\",\"".$wpatron."\",\"".$f."\",\"".$c."\",\"".$whis."\",\"".$wing."\",\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$wser."\",\"".$wusuario."\",\"".$whab."\",\"".$wcco."\",\"".$wnovalidah."\",\"".$wfec."\");' value='Grabar'><input type='button' onclick='cerrarventana_emergente();' value='Salir sin Grabar'>";

            echo "</td>";
            echo "</tr>";
            }
        }




  //==================================================================================================================
  //Funcion que pinta los productos para SI y TMO.
  function composicion_patron($wpatron, $fila, $whis, $wing, $wser, $f, $c, $whab, $wfec)
     {

        global $wbasedato;
        global $conex;
        global $wfecha;
        global $wemp_pmla;
        global $wusuario;
        global $wcco;


        $whorario_adicional = consultarHorario($wser, $wfec);

	  	// Validacion para los pacientes que ya tienen patron a excepcion de los posquirurgicos.
      	$wpatronesactual = consultar_patron_actual($whis, $wing, $wser, $wfecha);
      	$wdatopatrones = explode("-", $wpatronesactual);

        //Analizo si el patron no valida horario.
        $wnovalidah = validahorariopatron($wpatron);

      	$wdietas = $wdatopatrones[0];
      	$wposquirur = $wdatopatrones[4];

        //Si el paciente tiene asociado un patron e ingrega al servicio individual entonces hara un filtro para solo mostrar los productos que tengan Rpcnad activo (Rpcnad = producto que aparece en horaio normal y en adicion)
      	switch ($wdietas) {

      		case true:
					//Se explota el areglo de dietas que trae por coma.
					$array_dietas = explode(",",$wdietas);
					//Se recorre el arrelo generado para saber si uno de ellos es combinable o no, esto definira si se muestran mas productos.
					foreach($array_dietas as $key => $value)
							{
							$wcombinable = valida_combinable($value);
							//Si encuentra almenos uno en 'on' detiene el ciclo foreach y $wcombinable queda en 'on'
							if($wcombinable == 'on')
								{
								break;
								}
							}

  					if (($wposquirur == '' or $wposquirur == ' ') and $wcombinable != 'off')
						{
							$wquerynormaladicion = " AND Rpcnad = 'on'";
						}

      		break;

      		default:

      		break;
      	}


        if ($whorario_adicional == 'on')
        {
            //Esta consulta mostrara solo los productos activos en horario de adicion
            $q = " SELECT clades, claord, prodes, rpcvan, rpcfec, rpcvac, rpccod, procla"
                ."   FROM ".$wbasedato."_000082, ".$wbasedato."_000083, ".$wbasedato."_000131 "
                ."  WHERE rpcpat = '".$wpatron."'"
                ."    AND procla = clacod "
                ."    AND rpccod = procod"
                ."    AND proest = 'on' "
                ."    AND rpcser != '".$wser."'"
                ."    AND claest = 'on' "
                ."    AND rpcadi = 'on' "
                ." $wquerynormaladicion "  //Se agrega este filtro en caso de que el paciente ya tenga un patron en el servicio
                ."    AND rpcest = 'on' "
                ."  ORDER BY 2, 3 ";
        }
        else
        {
            //Esta consulta mostrara los productos que estan habilitados para horario normal, y mostrara solo algunos si el paciente ya tiene patron.
            $q = " SELECT clades, claord, prodes, rpcvan, rpcfec, rpcvac, rpccod, procla"
                ."   FROM ".$wbasedato."_000082, ".$wbasedato."_000083, ".$wbasedato."_000131 "
                ."  WHERE rpcpat = '".$wpatron."'"
                ."    AND procla = clacod "
                ."    AND rpccod = procod"
                ."    AND rpcser != '".$wser."'"
                ."    AND proest = 'on' "
                ."    AND claest = 'on' "
                ." $wquerynormaladicion " //Se agrega este filtro en caso de que el paciente ya tenga un patron en el servicio
                ."    AND rpcest = 'on' "
                ."  ORDER BY 2, 3 ";
        }

        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0)
            {
            $wvar="";

            $row = mysql_fetch_array($res);
            $word = $row[1];

            echo "<tr>";
            $wposicioninput = 2;
            //Cantidad de opciones por Patron
            for ($i=0; $i < $num; $i++)
                {

                $word = $row[1];

                echo "<td valign='top' bgcolor='CCFFFF'>";
                echo "<table valign='top'>";
                echo "<tr><td colspan=3 align=center><font size=3><b>".$row[0]."</b></font><input type='hidden' id='ptr_dsn_text' name='ptr_dsn_text' value='SI'></td></tr>";

                while ($i < $num and $word == $row[1])
                    {

                        $wdescripprod = $row[2];  // Nombre del producto.
                        if ($wfecha >= $row[4])
                            {
                            $wvalorneto=$row[5];            //Asigno el valor actual
                            }
                            else
                            {
                            $wvalorneto=$row[3];          //Asigno el valor anterior a la fecha de cambio
                            }

                         $wcod = $row[6];
                         $wclasificacion = $row[7];
                        //Busco si esta opcion esta grabada para el paciente en la tabla 000084
                        $q = " SELECT COUNT(*), detcan "
                            ."   FROM ".$wbasedato."_000084 "
                            ."  WHERE detfec = '".$wfec."'"
                            ."    AND dethis = '".$whis."'"
                            ."    AND deting = '".$wing."'"
                            ."    AND detser = '".$wser."'"
                            ."    AND detpat = '".$wpatron."'"
                            ."    AND detpro = '".$row[6]."'"
                            ."    AND detest = 'on' "
							."GROUP BY dethis";
                        $respro = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                        $rowpro = mysql_fetch_array($respro);

                        if ($rowpro[0] > 0)
                        {
                            $wchk = "CHECKED";
                            $wcolor =  'DeepSkyBlue';
                            $wvar=$wvar.$row[2].",";
                            $wvalue = $rowpro[1];
                        }
                        else
                            {
                            $wchk = "UNCHECKED";
                            $wcolor = '';
                            $wvalue = '1';
                            }
                        echo "<tr>";

                        echo "<td align=left bgcolor='$wcolor' id='chk".$wpatron.$i."'><INPUT TYPE='checkbox' id='patron_unico$i$j' name='checkbox' ".$wchk." onClick='grabar_servindiv("."\"".$wemp_pmla."\"".","."\"".$wbasedato."\"".","."\"".$wcod."\"".","."\"".$fila."\"".","."\"".$c."\"".","."\"".$wpatron."\"".","."\"".$whis."\"".","."\"".$wing."\"".","."\"".$wser."\"".","."\"".$wvalorneto."\"".","."\"".$wusuario."\"".","."\"".$whab."\"".","."\"".$wcco."\"".",\"1\",this,\"".$wposicioninput."\",\"off\",\"off\",\"".$wfec."\",\"$wclasificacion\", \"$i\");'><input size='3' maxlength='5' onkeyup='this.value = this.value.replace (/[^1?[0-9]$|^[1-2]0$]/, \"\");'  value='".$wvalue."' id='input".$wpatron."".$wcod."' type='text'></input>".utf8_encode($wdescripprod)."<div id='patron_unico$i$j'></div></td>";
                        echo "</tr>";

                        $row = mysql_fetch_array($res);

                        $i++;
                        $wposicioninput+=2;

                    }

                    $i=$i-1;
                    echo "</table>";
                    echo "</td>";

                }

            echo "</tr>";
            echo "<tr>";
            echo "<td align=center colspan=$i>";

            echo "<input type='button' onClick='cerraremergente_grabar(this,\"".$fila."\"".","."\"".$wpatron."\","."\"".$f."\","."\"".$c."\","."\"".$whis."\","."\"".$wing."\","."\"".$wemp_pmla."\","."\"".$wbasedato."\","."\"".$wser."\","."\"".$wusuario."\","."\"".$whab."\","."\"".$wcco."\","."\"".$wnovalidah."\","."\"".$wfec."\" );' value='Grabar'><input type='button' onclick='cerrarventana_emergente();' value='Salir sin Grabar'>";

            echo "</td>";
            echo "</tr>";
            }
        }
  //==================================================================================================================
  //==================================================================================================================


  //==================================================================================================================
  //==================================================================================================================
  //Funcion que define el div donde se pintaran los productos de los servicios individuales(SI, TMO) y DSN.
  function definir_div($wpatron, $fila, $whis, $wing, $wser, $i, $j, $whab, $wfec, $wcco, $wusuario, $wnombre_pac)
     {

	  global $wbasedato;
	  global $wemp_pmla;
	  global $wusuario;
	  global $wcco;
      global $conex;
      global $wfec;
      global $wfecha;
	  global $wlimite_carac_patron_asociado;

	  $wid_hidden=$fila.$wpatron;
	  $wid_sel="sel".$fila.$wpatron;

      // Analizo si el patron no valida horario
      $wnovalidah = validahorariopatron($wpatron);

      echo "<div align='center' style='cursor:default;background:none repeat scroll 0 0; "
			."position:relative;width:100 %;height:700px;overflow:auto;'><center><br>";
	  echo "<input type='hidden' name='".$wid_hidden."' id='".$wid_hidden."' value='".$wid_hidden."'/>";
	  echo "<input type='hidden' name='".$wid_sel."' id='".$wid_sel."' value='".$wid_sel."'/>";

	  echo "<div id='".$wpatron.$fila."' style='cursor: default' width=300 height=50>";
	  echo "<form id='form".$wpatron.$fila."' method='post' action='#'>";
      echo "<span><b><b class='fila1'><font size=4>".$wnombre_pac."</b></font></b></span><br><br>";
      echo "<span><b>Patr&oacuten: </b>".$wpatron."  <b> Historia: </b> ".$whis." - ".$wing." <b> Habitaci&oacuten: <b class='fila1'><font size=3>".$whab."</font></b> </b></span><br>";


      //Verifica si el patron no valida horario, aplica para el patron DSN.
      if ($wnovalidah == 'on')
        {

        $wpatron_dsn_asociado = traer_patron_asocia_dsn($whis, $wing, $wfecha, '');//Trae el ultimo patron asociado a DSN para una historia e ingreso
        $ultimo_nutricionista = buscar_ult_nutricionista($whis, $wing); //Ultimo nutricionista que registro datos para el paciente
        $wdatos_nutrinicionista = explode("-", $ultimo_nutricionista);
        $wcodigo_nutri = $wdatos_nutrinicionista[0]; //codigo del nutricionista
        $wnombre_nutri = $wdatos_nutrinicionista[1]; //nombre nutricionista

        echo "<br>";
        echo "<span><b>Nutricionista:&nbsp; ".$wnombre_nutri."</b> <br></span>&nbsp;";
        echo "<span>";
        echo "<table style='text-align: left; width: 1100px;' border='0'>
                <tbody>
                    <tr>
                    <td style='width: 400px;'><b>Patr&oacuten:</b><input type='text' id='ptr_dsn_text' size='70' name='ptr_dsn_text' maxlength='".$wlimite_carac_patron_asociado."' value='".strtoupper($wpatron_dsn_asociado)."'><div style='display:inline; font-size:10px; color:red;'>M&aacuteximo ".$wlimite_carac_patron_asociado." car&aacutecteres</div></td>
                    <td style='width: 500px;'><input type='button' onclick='cerraremergente_grabar(this,\"".$fila."\",\"".$wpatron."\",\"".$i."\",\"".$j."\",\"".$whis."\",\"".$wing."\",\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$wser."\",\"".$wusuario."\",\"".$whab."\",\"".$wcco."\",\"".$wnovalidah."\",\"".$wfec."\", \"".$wcodigo_nutri."\");' value='Grabar'><input type='button' onclick='cerrarventana_emergente();' value='Salir sin Grabar'></td>
                    </tr>
                </tbody>
                </table>";
        echo "</span>";
        echo "<br>";

		$wdatos_rol_enfermeria = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
        $winf_nutricion_dsn = explode("-", $wdatos_rol_enfermeria);
        $wpatron_nutricion = $winf_nutricion_dsn[1]; // Patron asociado a las nutricionistas.

		//Validar si el paciente tiene DSN para el dia de hoy o mañana en cualquier servicio, si no es asi pinta el boton para recuperar la dieta.
		$q = " SELECT movdie
                 FROM ".$wbasedato."_000077
                WHERE Fecha_data >= '".date("Y-m-d")."'
				  AND movhis = '".$whis."'
				  AND moving = '".$wing."'
                  AND movdie = '".$wpatron_nutricion."'";
        $res_die = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num_die = mysql_fetch_row($res_die);

			if($num_die == 0){

				//Valida si hay productos solicitados en dias anteriores para el paciente.
				$q_p = " SELECT *
						 FROM ".$wbasedato."_000084
						WHERE Fecha_data <= '".date("Y-m-d")."'
						  AND dethis = '".$whis."'
						  AND deting = '".$wing."'
						  AND detpat = '".$wpatron_nutricion."'
						LIMIT 1";
				$res_p = mysql_query($q_p,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_p." - ".mysql_error());
				$num_p = mysql_fetch_row($res_p);

				if($num_p > 0){

					echo "<span align=center>";
					echo "<table border='0'>
							<tbody>
								<tr>
								<td style='width: 400px;'><input type='button' onclick='recuperar_dsn_nutri(\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$whis."\",\"".$wing."\",\"".$wpatron_nutricion."\")' value='Recuperar DSN anterior'></td>
								</tr>
							</tbody>
							</table>";
					echo "</span>";
					echo "<br>";

				}
			}
        }
        else
        {
            echo "<span>";
            echo "<table style='text-align: left; width: 1100px;' border='0'>
                    <tbody>
                        <tr>
                        <td style='width: 400px;'></td>
                        <td style='width: 500px;'><input type='button' onclick='cerraremergente_grabar(this,\"".$fila."\",\"".$wpatron."\",\"".$i."\",\"".$j."\",\"".$whis."\",\"".$wing."\",\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$wser."\",\"".$wusuario."\",\"".$whab."\",\"".$wcco."\",\"".$wnovalidah."\",\"".$wfec."\", \"".$wcodigo_nutri."\");' value='Grabar'><input type='button' onclick='cerrarventana_emergente();' value='Salir sin Grabar'></td>
                        </tr>
                    </tbody>
                    </table>";
            echo "</span>";
        }

	  echo "<table valign='top'>";

      //Verifica si el patron no valida horario.
      if ($wnovalidah != 'on')
            {
             composicion_patron($wpatron, $fila, $whis, $wing, $wser, $i, $j, $whab, $wfec);
             $wnovalidah = '';
            }
         else
         {
             composicion_patrondsn($wpatron, $fila, $whis, $wing, $wser, $i, $j, $whab, $wfec);
             $wnovalidah = 'on';
         }

	  echo "<input type='HIDDEN' id='fila' name='fila' value='".$i."'>";
	  echo "<input type='HIDDEN' id='columna' name='columna' value='".$j."'>";

	  echo "</table>";
	  echo "</form>";
	  echo "</div>";
      echo "</center></div>";


	 }

	//Valida si el patron es combinable o no.
   function valida_combinable($wpatron)
    {

        global $conex;
        global $wbasedato;


        $q = " SELECT diecbi"
            ."   FROM ".$wbasedato."_000041"
            ."  WHERE dieest = 'on'"
            ."    AND diecod = '".$wpatron."'";
        $res_die = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row_die = mysql_fetch_array($res_die);

        if ($row_die['diecbi'] != 'off')
            {
            $wcombinable = 'on';
            }
        else
            {
            $wcombinable = 'off';
            }

        return $wcombinable;
	}


//=============================================================================================================
     //Funcion que verifica si el patron tiene costo adicional por centro de costos.
     function verifica_cobro_adicional($wcco, $wpatron)
		{

		global $conex;
		global $wbasedato;


		$q =   " SELECT diecad "
			  ."   FROM ".$wbasedato."_000041"
			  ."  WHERE dieccc = '".$wcco."'"
              ."    AND diecod = '".$wpatron."' "
              ."    AND diecad = 'on' "
			  ."    AND dieest = 'on' ";
	    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $row = mysql_fetch_array($res);
        $num = mysql_num_rows($res);

        if ($num == 0)
        {
            //Esta validacion se hace especialmente para el patron LC cuando se combina con otro patron y el paciente es POSTQUIRURGICO.
             $q =    " SELECT diecad "
                    ."   FROM ".$wbasedato."_000041"
                    ."  WHERE dieccc = '*'"
                    ."    AND diecod in ('".$wpatron."') "
                    ."    AND diecad = 'on' "
                    ."    AND diepqu = 'on' "
                    ."    AND dieest = 'on' ";
            $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $row = mysql_fetch_array($res);

        }

        return $row['diecad'];

        }

  //==================================================================================================================
  //==================================================================================================================

    // Costo del patron cuando es de tipo adicional (LOA)
     function costo_adicional_patron($wser, $wtipemp, $wedad_pac, $wpatron_seleccionado)
		{

		global $conex;
		global $wbasedato;

		//Consulto si el paciente es POS
		$tipo_pos = paciente_tipo_pos ($wtipemp, $wser, $wedad_pac);

		//Si el paciente es tipo POS consulto si existe costo correspondiente, especifico por el codigo de la empresa.
		if ($tipo_pos == 'SI')
		{
		  $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
		      ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041 "
		      ."  WHERE costem  LIKE '%".$wtipemp."%'"
		      ."    AND cosedi <= '".($wedad_pac*12)."'"
		      ."    AND cosedf >  '".($wedad_pac*12)."'"
		      ."    AND cosest  = 'on' "
		      ."    AND cosser  = '".$wser."'"
		      ."    AND cospat  = diecod "
              ."    AND diecad  = 'on' "
              ."    AND diecod  = '".$wpatron_seleccionado."'"
             ." GROUP BY cospat "
			 ." ORDER BY cosact DESC";
		  $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num_cos = mysql_num_rows($res_cos);
          $row_cos = mysql_fetch_array($res_cos);

		  if ($num_cos > 0)
			 {
              $wcosto = $row_cos['cosact'];
             }
		}
             else
             {


                $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
                    ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041 "
                    ."  WHERE costem  = '*'"
                    ."    AND cosedi <= '".($wedad_pac*12)."'"
                    ."    AND cosedf >=  '".($wedad_pac*12)."'"
                    ."    AND cosest  = 'on' "
                    ."    AND cosser  = '".$wser."'"
                    ."    AND cospat  = diecod "
                    ."    AND diecod  = '".$wpatron_seleccionado."'"
                    ." GROUP BY cospat "
                    ." ORDER BY cosact DESC";
                $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $num_cos = mysql_num_rows($res_cos);
                $row_cos = mysql_fetch_array($res_cos);

                $wcosto = $row_cos['cosact'];

             }

			return $wcosto;
        }

  //==================================================================================================================
  //==================================================================================================================

        //Funcion que identifica si el patron no valida horario (DSN)
        function validahorariopatron($wpatron)
		{

		global $conex;
		global $wbasedato;



		$q =   " SELECT dienvh "
			  ."   FROM ".$wbasedato."_000041"
			  ."  WHERE diecod = '".$wpatron."'"
			  ."    AND dieest = 'on' ";
	    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $row = mysql_fetch_array($res);

		  if ($row[0] == 'on')
			 {
			  $wvalida = 'on';
			 }
			else
				{
				$wvalida = 'off';
				}

			return $wvalida;
		}

    //==================================================================================================================
  //==================================================================================================================

    //Consulta la ultima fecha en que se haya registrado un patron DSN, pero que sea mayor o igual a la fecha actual.
    function consultar_ult_reg($whis, $wing, $wser, $wcco, $wpatron)
    {

        global $wbasedato;
        global $conex;
        global $wfecha;


        $q1= "  SELECT fecha_data "
            ."    FROM ".$wbasedato."_000077  "
            ."   WHERE movhis = '".$whis."'"
            ."     AND fecha_data >= '".$wfecha."'"
            ."     AND moving = '".$wing."'"
            ."     AND movcco = '".$wcco."'"
            ."     AND movser = '".$wser."'"
            ."     AND movest = 'on'"
            ."     AND movdie = '".$wpatron."'"
           ." ORDER BY fecha_data DESC limit 1";
        $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
        $row1 = mysql_fetch_array($res1);

        return $row1['fecha_data'];
    }


    //Consulta la ultima fecha en que se haya registrado un patron DSN.
    function consultar_ult_reg_activo($whis, $wing, $wser, $wpatron)
    {

        global $wbasedato;
        global $conex;

		$wfecha_hoy = date ("Y-m-d");
		$wfecha_manana = date("Y-m-d", strtotime("$wfecha_hoy+ 1 day")); // Fecha actual mas un dia.
		$array_datos = array();

       $q1= "  SELECT MAX(CONCAT(fecha_data,' ',hora_data)) as ultima_fecha_dsn
                 FROM ".$wbasedato."_000084
                WHERE dethis = '".$whis."'
                  AND deting = '".$wing."'
                  AND detpat = '".$wpatron."'
			      AND detfec <= '".$wfecha_manana."'
             ORDER BY detfec DESC limit 1";
        $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
        $row1 = mysql_fetch_array($res1);

		$wfecha_hora = $row1['ultima_fecha_dsn'];
		$wdatos = explode(" ",$wfecha_hora);
		$wfecha = $wdatos[0];
		$whora = $wdatos[1];

		$q2= "  SELECT Detcon "
            ."    FROM ".$wbasedato."_000084  "
            ."   WHERE dethis = '".$whis."'"
            ."     AND deting = '".$wing."'"
            ."     AND detpat = '".$wpatron."'"
           ." ORDER BY Detcon DESC limit 1";
        $res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
        $row2 = mysql_fetch_array($res2);
		$ultimo_consecutivo = $row2['Detcon'];

		$array_datos = array('ultima_fecha_dsn'=>$wfecha, 'ultima_hora_dsn'=>$whora, 'ultimo_consecutivo_dsn'=>$ultimo_consecutivo);

        return $array_datos;

    }


        //=============================================================================================================


     //VaAlida si el patron DSN esta grabado para una his, ing y hab.
     function valida_dsn_paciente($whis, $wing, $whab)
		{

		global $conex;
		global $wbasedato;
        global $wfecha;

        $wfecha_manana = date("Y-m-d", strtotime("$wfecha+ 1 day")); // Fecha actual mas un dia.

		$q =   " SELECT COUNT(*) "
			  ."   FROM ".$wbasedato."_000077, ".$wbasedato."_000041"
			  ."  WHERE movhis = '".$whis."'"
			  ."    AND moving = '".$wing."'"
			  ."    AND movhab = '".$whab."'"
			  ."    AND movdie = diecod "
			  ."    AND movfec between '".$wfecha."' AND '".$wfecha_manana."'"
			  ."    AND dienvh = 'on'"
			  ."    AND movest = 'on' ";
	    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $row = mysql_fetch_array($res);

		  if ($row[0] > 0)
			 {
			  $wcorresponde = 'on';
			 }
			else
				{
				$wcorresponde = 'off';
				}

			return $wcorresponde;
        }

	//------------------------------------------------------------------------------------------------------------------------------
	//	Funcion que me consulta si el cco graba pedidos de alimentacion de forma automatica, en el momento en que entran al sistema
	//	2012-10-26:		jerson trujillo.
	//------------------------------------------------------------------------------------------------------------------------------
	function cco_graba_automatico($wcco)
	{
		global $conex;
		global $wbasedato;
		$q_cco_aut=	" SELECT Ccosda
						FROM ".$wbasedato."_000011
					   WHERE Ccocod	= '".$wcco."'";
		$res_cco_aut = mysql_query($q_cco_aut,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar si el cco graba el pedido al entrar al programa): ".$q_cco_aut." - ".mysql_error());
		$row_cco_aut = mysql_fetch_array ($res_cco_aut);
		if ($row_cco_aut['Ccosda'] == 'on')
             $wauto = 'on';
		else
             $wauto = 'off';

        return $wauto;
	}
	//--------------------------------------------------------------
	// Fin graba pedido automatico
	//--------------------------------------------------------------

    function validarenfermera($whce, $wbasedato, $user)
    {

      global $conex;

      //Busco que rol tiene el usuario
	  $q =  " SELECT usurol "
           ."   FROM ".$whce."_000020"
           ."  WHERE usucod = '".$user."'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);

      return $row['usurol'];

    }

  //==================================================================================================================
  //==================================================================================================================
  //Funcion que es llamada al cargar la pagina y muestra toda la informacion de los pacientes en el centro de costos seleccionado
  function mostrar()
     {

      global $wbasedato;
	  global $conex;
	  global $wcco;
	  global $wfec;
	  global $wfecha;
	  global $wemp_pmla;
	  global $wser;
	  global $wdietas;
      global $whce;
	  global $whistoria_c;
	  global $wingreso_c;

	  global $num_die;
	  global $num_pac;

	  global $whis;
	  global $wing;
	  global $whab;          //Habitacion
	  global $wedad;          //Edad
	  global $wtem;          //Tipo de empresa
	  global $whabilitado;
	  global $wser_ant;
	  global $wadi_ser;
	  global $wcombinable;
	  global $user;
      global $wlimite_caracteres_observ;
      global $wzona;

	  $wdisabledTextArea = $whabilitado;	//whabilidatos es ENABLED si está en horario de pedido y DISABLED en caso contrario

	  $whora =(string)date("H:i:s");
      $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

	  $color_esq_actual = 'yellow';
	  $color_ant_sing = 'yellow';

      $wdosdiasantes = time()-(2*24*60*60); //Resta dos dias
      $wdosdiasantes1 = date('Y-m-d', $wdosdiasantes); //Formatea dia

      $wundiaantes = time()-(1*24*60*60); //Resta un dia
      $wundiaantes1 = date('Y-m-d', $wundiaantes); //Formatea dia

      $wprog_automatico = cco_graba_automatico($wcco);   //Valida si se carga automatico
	  $wtiempo_recarga_msg = consultarTiempoRecargaMsg( $wemp_pmla ); //Tiempo de recarga para la mensajeria
      $wdatos_rol_enfermeria = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
	  $wtcx = consultarAliasPorAplicacion($conex, $wemp_pmla, "tcx");
      $winf_nutricion_dsn = explode("-", $wdatos_rol_enfermeria);

      $wrolnutricion = $winf_nutricion_dsn[0];// Rol nutricionistas
      $wpatron_nutricion = $winf_nutricion_dsn[1]; // Patron asociado a las nutricionistas.

      $wrol_usuario = validarenfermera($whce, $wbasedato, $wusuario); //Rol del usuario actual

      echo "<input type='hidden' id='wrol_usuario' name='wrol_usuario' value='".$wrol_usuario."'>";
      echo "<input type='hidden' id='wtiempo_rec_msg' name='wtiempo_rec_msg' value='".$wtiempo_recarga_msg."'>";
      echo "<input type='hidden' id='wrolnutricion' name='wrolnutricion' value='".$wrolnutricion."'>";
      echo "<input type='hidden' id='wpatron_nutricion' name='wpatron_nutricion' value='".$wpatron_nutricion."'>";
	  //=======================================================================================================================
      //OJO ESTE PROCEDIMIENTO ES CLAVE PARA EL FUNCIONAMIENTO DEL PROGRAMA
      //=======================================================================================================================
      //Los patrones NO son conbinables solo cuando es en horario de Solicitud normal (Osea entre Hora Inicial de Pedido y
      //Hora final del pedido, pero pasado este tiempo solo es posible hacer combinaciones con patrones individuales como SI y TMO, excepto DSN.
      //
      //Por eso se hace el siguiente procedimiento de determinar el horario en que se esta haciendo la transaccion para definir
      //si los patrones son combinables o no, porque solo se combinan cuando es adición.
      $wadi_ser=determinar_adicion($wser);

      //$wadi_ser='on' Indica que se esta en horario de adiciones para el servicio seleccionado, por lo tanto los patrones se
      //puede combinar, entonces siempre coloco en el campo $row_die[4]=='on' el cual viene de la tabla 000076

	  //Busco caracteristicas del servicio
	  $q = " SELECT seresq, seradi, sertpo, sercap, serhia, serhca "
	      ."   FROM ".$wbasedato."_000076 "
	      ."  WHERE sercod = '".$wser."'"
	      ."    AND serest = 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);

	  if ($num > 0)
	     {
		  $row = mysql_fetch_array($res);

		  $wesq = $row['seresq'];   //Esquema asociado al Servicio
		  $wtpo = $row['sertpo'];   //Tipos de empresa POS
		  $wemp = $row['sercap'];   //Cantidad de empleados (para enviar meriendas)
          $whora_maxima_modificacion = $row['serhia'];
          $whora_maxima_cancelacion = $row['serhca'];
		 }

	  //Busco si ya existe el servicio seleccionado en el dia, si ya existe no doy la posibilidad de traer el servicio anterior
	  //si no existe, traigo la configuracion igual al anterior servicio, para esto coloco $wserant='on'
	  if (buscar_si_hay_servicio_anterior($wser))
	        {
            $wserant="on";
            }
	    else
            {
            $wserant="off";
            }


      //Busco de que tipo de centro de costos es el seleccionado
	  $q =  " SELECT ccohos, ccocir, ccourg, ccoayu "
           ."   FROM ".$wbasedato."_000011"
           ."  WHERE ccocod = '".trim($wcco)."'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);
      $whos = $row['ccohos'];
      $wcir = $row['ccocir']; //esta variable se usa para que los pacientes de cirugia no tengan la fila en azul y los cajones inhabilitados.
      $wurg = $row['ccourg']; //esta variable se usa para que los pacientes de urgencias no tengan la fila en azul y los cajones inhabilitados.
      $wayu = $row['ccoayu']; //esta variable se usa para que los pacientes de urgencias no tengan la fila en azul y los cajones inhabilitados.
        //Se declara una palabra 'Cir' para utilizarla como alias en caso de que el centro de costos sea de Cirugia, esta varialbe se usa en la
        //consulta posterior, ademas se crea una variable $wunion con la consulta a la tabla 77 de movhos para los mismos pacientes pero hayan
        //tenido alimentacion el dia anterior.
        if ($row['ccocir']=='on')
            {

            $whabit = "'Cir'";

            //Pacientes que esten en Urgencias o en cirugia desde dias anteriores y hayan tenido servicio de alimentacion
            $wunion =   "UNION SELECT ' ', ubihis, ubiing, trim(pacno1), trim(pacno2), pacap1, pacap2, pactid, pacced, ubiptr, ubimue, pacnac, "
                        ."        ubialp, ingtip, ROUND(TIMESTAMPDIFF(HOUR,".$wbasedato."_000016.fecha_data,now())/24,0)"
                        ."   FROM root_000036, root_000037, ".$wbasedato."_000018, ".$wbasedato."_000016 "
                        ."  WHERE ubihis = orihis "
                        ."    AND ubiing = oriing "
                        ."    AND oriori = '".$wemp_pmla."'"       //Empresa Origen de la historia,
                        ."    AND oriced = pacced "
                        ."    AND oritid = pactid "
                        ."    AND ubiald != 'on' "			       //Que no este en Alta Definitiva
                        ."    AND ubisac = '".trim($wcco)."'"      //Servicio Actual BETWEEN '06-Jan-1999' AND '10-Jan-1999'
                        ."    AND ".$wbasedato."_000018.Fecha_data BETWEEN '".$wdosdiasantes1."' AND '".$wundiaantes1."'" //Pacientes que hallan tenido atencion por lo menos dos dias antes
                        ."    AND ubihis = inghis "
                        ."    AND ubiing = inging "
                        ."  GROUP BY 1,2,3 "
                        ."  ORDER BY 4,5 ";
            }
            //Se declara una palabra 'Cir' para utilizarla como alias en caso de que el centro de costos sea de Cirugia, esta varialbe se usa en la
            //consulta posterior.


        //Esta validacion se da para los centros de costos que son hospitalarios.
        $wtabla = '';
         if ($row['ccohos']=='on' and $row['ccourg']=='off' and $row['ccocir']=='off')
            {
               $whabit = 'Habcod';    // Nombre de campo que se usa para centros de costos hospitalarios.
               $wtabla = $wbasedato."_000020,";  // Tabla que se usa para los centros de costo hospitalarios.
               $wunion = "  GROUP BY 1,2,3 ORDER BY habcco, habord ,habcod "; //En este caso la variable $wunion tendra este valor ya que no se realizan consultas a dias aneriores.
            }

      //Esta validacion se da cuando el usuario quiere ver registros de dietas de dias anteriores o posteriores
        if($wfecha != $wfec)
        {

            //Si la fecha del servidor es mayor a la fecha a la fecha de la interfaz entonces hace la consulta a la tabla 77 de movhos, verificando los
            //pacientes que tienen dieta segun el filtro de $wfec (fecha de la interfaz)

                $q = " SELECT movhab, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, pactid, pacced, ubiptr, ubimue, pacnac, "
                    ."        ubialp, ingtip, ROUND(TIMESTAMPDIFF(HOUR,".$wbasedato."_000016.fecha_data,now())/24,0) "
                    ." FROM root_000036, root_000037, ".$wbasedato."_000018, ".$wbasedato."_000016, ".$wbasedato."_000077 "
                    ."WHERE movfec = '".$wfec."'"
                    ."  AND movcco = '".trim($wcco)."' "
                    ."  AND movser = '".$wser."' "
                    ."  AND movhis = orihis"
                    ."  AND oriori = '".$wemp_pmla."'"
                    ."  AND oriced = pacced "
                    ."  AND oritid = pactid"
                    ."  AND movhis = ubihis "
                    ."  AND moving = ubiing "
                    ."  AND movhis = inghis "
                    ."  AND moving = inging "
                    ."  GROUP BY 1,2,3"
                    ."  ORDER BY 1 ";

          }
          else
            {

                $tabla_20       = ", {$wbasedato}_000020 ";
                $condicionZona  = " AND Habhis = Ubihis ";
                $condicionZona .= " AND Habing = Ubiing ";
                $condicionZona .= " AND Habest = 'on' ";
                $wzona = ( $wzona == "%" ) ? "" : $wzona;
                if( $wzona != "" and $wzona != "NO APLICA"){
                    $condicionZona .= " AND Habzon = '{$wzona}' ";
                }
			switch(true){

				case ($whos == 'on'):

							//Esta es la consulta principal de los pacientes para el dia de hoy, validando los campos para los centros de costos hospitalarios y cirugia.
							  $q = " SELECT $whabit, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, pactid, pacced, ubiptr, ubimue, pacnac, "
								  ."        ubialp, ingtip, ROUND(TIMESTAMPDIFF(HOUR,".$wbasedato."_000016.fecha_data,now())/24,0) "
								  ."   FROM root_000036, root_000037, ".$wbasedato."_000018, $wtabla ".$wbasedato."_000016 "
								  ."  WHERE ubihis  = orihis "
								  ."    AND ubiing  = oriing "
								  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
								  ."    AND oriced  = pacced "
								  ."    AND oritid  = pactid "
								  ."    AND ubiald != 'on' ";			   //Que no este en Alta Definitiva

								  //Esta validacion se da para los centros de costos hospitalarios ya que usan los campos habhis, habing, ubihis, ubiing, los que no
								  //son hospitalario no usan estos campos.
								  if ($row['ccohos']=='on' and $row['ccourg']=='off' and $row['ccocir']=='off')
									  {
									  $q.="    AND ubihis  = habhis ";
									  $q.="    AND ubiing  = habing ";
									  $q.="    AND habcco  = '".trim($wcco)."'";
									  }

								  //Esta condicion se aplica para los centros de costo de cirugia y urgencias, ya que solo usan los campos ubisac y filtran los pacientes
								  //por la fecha actual.
								  if ($row['ccourg']=='on' or $row['ccocir']=='on' )
									  {
									  $q.= "    AND ubisac = '".trim($wcco)."'"; //Servicio Actual
									  $q.= "    AND ".$wbasedato."_000018.Fecha_data = '".$wfec."'";

									  }


								  $q.="    AND ubihis  = inghis ";
                                  $q.="    AND ubiing  = inging ";
                                  $q.="    {$condicionZona} ";
								  $q.=    $wunion;   //Segmento de consulta cuando el centro de costos que se escoge es urgencias o cirugia.

				break;

				case ($wurg == 'on'):
							//Consulta solo para urgencias
							$q = " SELECT concat(Aredes, '-<br>',Habcpa), ubihis, ubiing, trim(pacno1), trim(pacno2), pacap1, pacap2, pactid, pacced, ubiptr, ubimue, pacnac, "
								."        ubialp, ingtip, ROUND(TIMESTAMPDIFF(HOUR,".$wbasedato."_000016.fecha_data,now())/24,0)"
								."   FROM root_000036, root_000037, ".$wbasedato."_000018, ".$wbasedato."_000016 {$tabla_20}, {$wbasedato}_000169"
								."  WHERE ubihis = orihis "
								."    AND ubiing = oriing "
								."    AND oriori = '".$wemp_pmla."'"       //Empresa Origen de la historia,
								."    AND oriced = pacced "
								."    AND oritid = pactid "
								."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
								."    AND ubisac = '".trim($wcco)."'"      //Servicio Actual
								."    AND ubihis = inghis "
								."    AND ubiing = inging "
								//."	  AND Ubidie = 'on' " //->2019-09-23 Se omite este campo puesto que a partir de la fecha se van a mostrar todos los pacientes de urgencias.
								/*."	  AND ubihis = '".$whistoria_c."'
                                      AND ubiing = '".$wingreso_c."'"*/
                                ."    {$condicionZona}"
                                ."    AND Arecod = Habzon"
								."   GROUP BY 2,3
								    ORDER BY 4,5 ";

				break;

				case ($wayu == 'on'):


						$q = "SELECT 'Ayu', ubihis, ubiing, trim(pacno1), trim(pacno2), pacap1, pacap2, pactid, pacced, ubiptr, ubimue, pacnac, ubialp,
									ingtip, ROUND(TIMESTAMPDIFF(HOUR,".$wbasedato."_000016.fecha_data,now())/24,0)
							   FROM ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000011, root_000037, root_000036,".$wbasedato."_000016
							  WHERE ubiald = 'off'
								AND ubisac = Ccocod
								AND oriori = '".$wemp_pmla."'
								AND ubihis = orihis
								AND ubiing = oriing
								AND oriced = pacced
								AND oritid = pactid
								AND orihis = inghis
								AND oriing = inging
								AND ubimue != 'on'
								AND Ubisac = '".trim($wcco)."'
								AND ubihis = '".$whistoria_c."'
								AND ubiing = '".$wingreso_c."'
						   GROUP BY ubihis, ubiing
						   ORDER BY 4,5";

				break;

				case ($wcir == 'on'):

						$q = "SELECT 'Cir', ubihis, ubiing, trim(pacno1), trim(pacno2), pacap1, pacap2, pactid, pacced, ubiptr, ubimue, pacnac, ubialp, ingtip
								   FROM ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000011, root_000037, root_000036,".$wbasedato."_000016, ".$wtcx."_000011
								  WHERE ubiald = 'off'
									AND ubisac = Ccocod
									AND oriori = '".$wemp_pmla."'
									AND ubihis = orihis
									AND ubiing = oriing
									AND oriced = pacced
									AND oritid = pactid
									AND orihis = inghis
									AND oriing = inging
									AND turhis = inghis
									AND turnin = inging
									AND ubimue != 'on'
									AND ubihis = '".$whistoria_c."'
									AND ubiing = '".$wingreso_c."'
							   GROUP BY ubihis, ubiing";

				break;

				default:
						echo '<br><br><font size="5"><span>Este centro de costos no puede realizar solicitud de alimentación.</span></font>';
						return;
				break;

			}

        }

	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num_pac = mysql_num_rows($res);

	  if ($num_pac > 0)
	     {
		  echo "<input type='HIDDEN' id='num_pac' name='num_pac' value='".$num_pac."'>";

		  //==============================================================================================================
		  // ENCABEZADO DONDE VAN LAS OBSERVACIONES DE ENFERMERIA Y LA CPA
		  //==============================================================================================================
		  //Traigo las observaciones y numero de empleados del ENCABEZADO DEL SERVICIO
	      $q = " SELECT encobe, encobc, enccap "
	          ."   FROM ".$wbasedato."_000085 "
	          ."  WHERE encfec = '".$wfec."'"
	          ."    AND enccco = '".$wcco."'"
	          ."    AND encser = '".$wser."'"
              ."    AND encest = 'on'"    ;
	      $resenc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $rowenc = mysql_fetch_array($resenc);

	      if (trim($rowenc[0]) != "" or trim($rowenc[1]) or trim($rowenc[2]))
	         {
	          $wobserv_enfer = $rowenc[0];
	          $wobserv_cpa   = $rowenc[1];
	          $wcap          = $rowenc[2];
	         }
	        else
	           {
	            $wobserv_enfer="";
	            $wobserv_cpa="";
	            $wcap="";
	           }

	      echo "<tr class='fila2'>";
		  echo "<td colspan=40>";

		  echo "</td>";
	      echo "</tr>";
		  //==============================================================================================================
		   //Se consulta el tipo de centro de costos.
		   $q_control = " SELECT Ccohos, Ccourg, Ccoayu, Ccocir
				            FROM ".$wbasedato."_000011
				           WHERE Ccocod = '".$wcco."' ";
		   $res_control = mysql_query($q_control,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		   $row_control = mysql_fetch_array($res_control);
		   $cco_hos = $row_control['Ccohos'];
		   $cco_urg = $row_control['Ccourg'];
		   $cco_ayu = $row_control['Ccoayu'];
		   $cco_cir = $row_control['Ccocir'];

			//Dependendo del tipo se crea un filtro que luego se le aplica a la consulta de los patrones que se pueden mostrar.
			switch(true)	{

				case ($cco_hos == 'on'):

							$filtro_patrones = "Diehos != 'off'";

				break;

				case ($cco_urg == 'on'):

							$filtro_patrones = "Dieurg = 'on'";

				break;

				case ($cco_ayu == 'on'):

							$filtro_patrones = "Dieayu = 'on'";

				break;

				case ($cco_cir == 'on'):

							$filtro_patrones = "Diecir = 'on'";

				break;


			}

		  //Traigo las DIETAS que existen para colocar la barra de titulo
		  $q = " SELECT diecod, diedes, diecom, dieord, diecbi, diecmb, dienvh, dieind, diepqu
		           FROM ".$wbasedato."_000041, ".$wbasedato."_000076
		          WHERE dieest = 'on'
		            AND dieord > 0
		            AND dieesq = seresq
                    AND dieped != 'on'
                    AND dieccc in ('*','".$wcco."')
				    AND $filtro_patrones
		            AND sercod = '".$wser."'
		        ORDER BY 4 ";
		  $res_die = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $num_die = mysql_num_rows($res_die);

	      echo "<input type='HIDDEN' id='num_die' name='num_die' value='".$num_die."'>";

	      if ($num_die > 0 )
	         {
		      //========================================================================================================
		      //Aca coloco los cajones multiselecion X columna
		      //========================================================================================================
		      validaciones('', '', '', $wser, "Consulta");

	          //=======================================================================================================

	          //========================================================================================================
		      //Aca los titulos de cada columna, basados en el maestro de Dietas o Patrones con JQUERY.
		      //========================================================================================================
	          mysql_data_seek($res_die,0);         //Devuelvo el puntero al primer registro
		      echo "<tr class=encabezadoTabla>";
              echo "<td >Dieta Kardex</td>";
		      echo "<td>Hab</td>";
		      echo "<td>Días</td>";
		      echo "<td>Edad</td>";
		      echo "<td>Historia</td>";
		      echo "<td>Paciente</td>";
              //echo "<td>PosQx</td>"; Se deja de mostrar esta columna por peticion de Liliana 28 Mayo 2018 Jonatan
		      $j=0;
		      for ($i=1;$i<=$num_die;$i++)
		         {
			      $row_die = mysql_fetch_array($res_die);

		          echo "<td><span id='wdie".$i."' class='tooltip { direction: n; width: auto; font-weight:bold; background: #222; color: #ddd; }' title='".$row_die[1]."'>".strtoupper($row_die[0])."</span></td>";
		          $wdietas[$i]=$row_die[1];


		          if ($row_die[4]=="on")
		             {
			          $wdie_unicas[$j]=$row_die[1];
			          $j++;
		             }

		          echo "<input type='HIDDEN' id='wcom$i' value='".$row_die[4]."'>";
				  echo "<input type='HIDDEN' id='wcombinacon$i' value='".$row_die[5]."'>";
				  echo "<input type='HIDDEN' id='westepatron$i' value='".$row_die[0]."'>";
                  echo "<input type='HIDDEN' id='wposquirur$i' value='".$row_die['diepqu']."'>";
	             }
	         }
		  echo "<td>Media porción</td>";
          echo "<td align=center>Diagnóstico</td>";
	      echo "<td align=center>Observaciones <br> de la estancia <div style='display:block; font-size:10px; color:white;'>(Máximo <span id='limite_obs'>".$wlimite_caracteres_observ."</span> carácteres)</div></td>";
		  echo "<td align=center>Intolerancias <div style='display:block; font-size:10px; color:white;'>(Máximo <span id='limite_into'>".$wlimite_caracteres_observ."</span> carácteres)</div></td>";
		  echo "<td align=center>Alergias y alertas</td>";
		  echo "<td>Afinidad</td>";
		  echo "</tr>";

		  for($i=1;$i<=$num_pac;$i++)                             //For por habitacion o paciente
			 {
			  $row = mysql_fetch_array($res);

			  if (is_integer($i/2))
                   $wclass="fila1";
                else
                   $wclass="fila2";

			  $whab[$i] = $row[0];                                       //Habitacion
			  $whis[$i] = $row[1];                                       //Historia
			  $wing[$i] = $row[2];                                       //Ingreso
			  $wpac[$i] = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];   //Paciente
			  $wdpa[$i] = $row[7];                                       //Documento del paciente, sirve para buscar en Magenta
		      $wtid[$i] = $row[8];                                       //Tipo de Identificacion, para Magenta
		      $wptr[$i] = $row[9];                                       //Proceso de traslado
		      $wmue[$i] = $row[10];                                      //Muerte
		      $wnac[$i] = $row[11];                                      //Fecha nacimiento
		      $walp[$i] = $row[12];                                      //Alta en proceso
		      $wtem[$i] = $row[13];                                      //Tipo de Empresa
		      $west[$i] = $row[14];                                      //Dias de Estancia


		      if ($walp[$i]=="on")     //Si esta en proceso de alta Resalto la fila
		         $wclass="fondoAmarillo";


		      //Calculo la edad
		      $wfnac=(integer)substr($wnac[$i],0,4)*365 +(integer)substr($wnac[$i],5,2)*30 + (integer)substr($wnac[$i],8,2);
			  $wfhoy=(integer)date("Y")*365 +(integer)date("m")*30 + (integer)date("d");
			  $wedad[$i]=(($wfhoy - $wfnac)/365);
			  $wedad1[$i]=round((($wfhoy - $wfnac)/365),3);

              $wedad_paciente = calcularAnioMesesDiasTranscurridos($wnac[$i]);

              $wedad_pacienteanos = $wedad_paciente['anios'];
              $wedad_pacientemeses = $wedad_paciente['meses'];
              $wedad_pacientedias = $wedad_paciente['dias'];

              if ($wedad_pacienteanos == 1)
              {
                  $wanios = ' Año ';
              }
              else
              {
                  $wanios = ' Años ';
              }

              if ($wedad_pacientemeses == 1)
              {
                  $wmeses = ' Mes ';
              }
              else
              {
                  $wmeses = ' Meses ';
              }

			  $wedad = $wedad_pacienteanos.$wanios.$wedad_pacientemeses.$wmeses;

		      validaciones($whis[$i], $wing[$i], $whab[$i], $wser, "Consulta");

            //Si la historia esta en proceso de traslado y es de urgencias o cirugia, los cajones siempre estaran activos.
            if ($wptr[$i]=="on" and $wcir != 'on' and $wurg != 'on')
                {
                $whabilitado="Disabled";
                $wclass="colorAzul4";
                }

		      $wdietakardex = traer_dietas_kardex($whis[$i], $wing[$i], $wfec); //Trae la dieta del kardex.

		      echo "<tr class='".$wclass."'>";
              echo "<td bgcolor='#A9A5A5' align=center><b>".$wdietakardex."</b></td>";
		      echo "<td>".$whab[$i]."</td>";
		      echo "<td>".$west[$i]."</td>";
		      echo "<td>".$wedad."</td>";
		      echo "<td>".$whis[$i]."-".$wing[$i]."</td>";
		      echo "<td>".$wpac[$i]."</td>";
		      echo "<input type='HIDDEN' name='whis[".$i."]' value='".$whis[$i]."'>";
		      echo "<input type='HIDDEN' name='wing[".$i."]' value='".$wing[$i]."'>";
		      echo "<input type='HIDDEN' name='whab[".$i."]' value='".$whab[$i]."'>";
		      echo "<input type='HIDDEN' name='wpac[".$i."]' value='".$wpac[$i]."'>";
		      echo "<input type='HIDDEN' name='wedad[".$i."]' value='".$wedad[$i]."'>";
		      echo "<input type='HIDDEN' name='wtem[".$i."]' value='".$wtem[$i]."'>";
		      echo "<input type='HIDDEN' name='west[".$i."]' value='".$west[$i]."'>";



		      ///===============================================

		      $wcolor=$color_esq_actual;

		      if ($wmue[$i]=="on")    //Si la historia esta en proceso de traslado o el paciente murio 'deshabilito' los cajones
		         {
		          $whabilitado="Disabled";
		          $wcolor="blue";
		         }

		      //ATENCION !!!! Si la variable $wserant=='on' es porque se debe traer la configuracion del servicio anterior al actual
		      //entonces cambio el query pero con la variable del servicio anterior.


				if ($wserant=="off")  //Indica que NO se puede traer el SERVICIO ANTERIOR
				{

					//Como el PATRON de alimentacion puede ser mas de uno para un mismo paciente, y este queda grabado en el mismo campo 'movdie'
					//entonces traigo este campo y le hago un explode para recorrerlo buscando el patron que estoy buscando segun el result de $row_die
					$q = " SELECT movdie, MAX(id) as id, movmpo, movcan, movobs, movint, movpqu "
						."   FROM ".$wbasedato."_000077 "
						."  WHERE movfec = '".$wfec."'"
						."    AND movhis = '".$whis[$i]."'"
						."    AND moving = '".$wing[$i]."'"
						."    AND movser = '".$wser."'"           //Servicio Actual
						."    AND movest = 'on' "
						."  GROUP BY 1"
						."    UNION "
						." SELECT movdie, '' AS id, movmpo, movcan, movobs, movint, movpqu "
						."   FROM ".$wbasedato."_000077, ".$wbasedato."_000041"
						."  WHERE movhis = '".$whis[$i]."'"
						."    AND moving = '".$wing[$i]."'"
						."    AND movhab = '".$whab[$i]."'"
						."    AND movdie = diecod "
						."    AND movfec = '".$wfec."'"
						."    AND dienvh = 'on'"
						."    AND movser = '".$wser."'"
						."    AND movest = 'on' "
						."  GROUP BY 1 order by id DESC";

					$wcolor=$color_esq_actual;
					$wcontrolserv = '0';

					$wseraux=$wser;
				}

					else    //Si entra por aca es porque debe traer el SERVICIO ANTERIOR
					{
					   buscar_servicio_anterior_por_historia($whis[$i], $wing[$i]);

						//Esta validacion se da cuando el usuario quiere consultar dato una fecha anterior o posterior.
						if($wfecha != $wfec)
							{

								if ($wfecha > $wfec)
								{

								//Esta consulta se da cuando la interfaz se queda en el mismo dia y el servidor esta en el siguiente dia.
								$q = " SELECT movdie, MAX(id) as id, movmpo, movcan, movobs, movint, movpqu "
									."   FROM ".$wbasedato."_000077 "
									."  WHERE movhis = '".$whis[$i]."'"
									."    AND moving = '".$wing[$i]."'"
									."    AND movhab = '".$whab[$i]."'"
									."    AND movser = '".$wser."'"
									."    AND movest = 'on' "
									."   UNION " //Este union se usa especialmente para DSN, el cual no valida horario.
									." SELECT movdie, '' AS id, movmpo, movcan, movobs, movint, movpqu "
									."   FROM ".$wbasedato."_000077, ".$wbasedato."_000041"
									."  WHERE movhis = '".$whis[$i]."'"
									."    AND moving = '".$wing[$i]."'"
									."    AND movhab = '".$whab[$i]."'"
									."    AND movdie = diecod "
									."    AND dienvh = 'on'"
									."    AND movest = 'on' "
									."  GROUP BY 1 order by id DESC";

									$wseraux=$wser;
								}
								else
								{

								//Esta consulta se da cuando el usuario selecciona una fecha difente a la actual, regularmente es anterior a la actual.
								$q = " SELECT movdie, MAX(id) as id, movmpo, movcan, movobs, movint, movpqu "
									."   FROM ".$wbasedato."_000077 "
									."  WHERE movfec = '".$wfec."'"
									."    AND movhis = '".$whis[$i]."'"
									."    AND moving = '".$wing[$i]."'"
									."    AND movser = '".$wser."'"           //Servicio Actual
									."    AND movest = 'on' "
									." GROUP BY 1"
									."  UNION "
									." SELECT movdie, '' AS id, movmpo, movcan, movobs, movint, movpqu  "
									."   FROM ".$wbasedato."_000077, ".$wbasedato."_000041"
									."  WHERE movhis = '".$whis[$i]."'"
									."    AND moving = '".$wing[$i]."'"
									."    AND movhab = '".$whab[$i]."'"
									."    AND movdie = diecod "
									."    AND movfec = '".$wfec."'"
									."    AND dienvh = 'on'"
									."    AND movest = 'on' "
									."  GROUP BY 1 order by id DESC";

									$wseraux=$wser;
								}

							}
							else
							{

							//Consulta que trae los datos del servicio anterior.
							$q = " SELECT movdie, '' as id, movmpo, movcan, movobs, movint, movpqu, Fecha_data "
								."   FROM ".$wbasedato."_000077 "
								."  WHERE movhis = '".$whis[$i]."'"
								."    AND moving = '".$wing[$i]."'"
								."    AND movhab = '".$whab[$i]."'"
								."    AND movser = '".$wser_ant."'"      //Servicio Anterior
								."    AND movest = 'on' "
								."  UNION " //Este union se usa especialemte para DSN, el cual no valida horario.
								." SELECT movdie, '' as id, movmpo, movcan, movobs, movint, movpqu, '' "
								."   FROM ".$wbasedato."_000077, ".$wbasedato."_000041"
								."  WHERE movhis = '".$whis[$i]."'"
								."    AND moving = '".$wing[$i]."'"
								."    AND movhab = '".$whab[$i]."'"
								."    AND movdie = diecod "
								."    AND dienvh = 'on'"
								."    AND movest = 'on' "
							   ."ORDER BY Fecha_data DESC";

							$wcolor=$color_ant_sing;
							$wcontrolserv = '1';
							$wseraux=$wser;
							}
					  }

			  $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num_mov = mysql_num_rows($res_mov);
			  $row_mov = mysql_fetch_array($res_mov);

			  $wpatrones_inicial= $row_mov['movdie'];
			  $wmedia_porcionbd = $row_mov['movmpo'];
              $wcantidad = $row_mov['movcan'];
              $wobservaciones = $row_mov['movobs'];
              $wintolerancias = $row_mov['movint'];
              $wposquirur = $row_mov['movpqu'];

              $wpatronposquirur = consultar_estado_posqui($whis[$i],$wing[$i], $wser);
              $wtiempoposqx = consultarAliasPorAplicacion($conex, $wemp_pmla, 'TiempoSolicitudPosqx'); //Consulta los minutos despues de los cuales se puede pedir un patron adicional al posqx.
              $wformatohora = "00:".$wtiempoposqx.":00"; // Formatea la hora para que la tome la funcion.
              $wultimoregposqx = ultimoregposqx($whis[$i],$wing[$i], $wser); // Consulta el ultimo registro que tenga disponible la casilla posqx.

              if(empty($wultimoregposqx))
                {
                    $wultimoregposqx = '00:00:00';
                }

              $whoraactivacionposqx = SumaHoras( $wultimoregposqx, $wformatohora ); //Lapso que ha transcurrido entre la seleccion del patron LC.

			  $wchecked='';

              // Acciones del cajon para POSTQUIRURGICO, si hay un 1 en el campo movpqu el cajon se activara, si hay un on, el cajon se activara y se chequeara.
               if ($wposquirur == 1 and $whora >= $whoraactivacionposqx)
                  {

                   //Si esta en horario habil, entonces habilitara.
                   if($whabilitado == 'Enabled')
                            {
                                $whabilitaposqui = 'Enabled';
                                $wbgcolor = "bgcolor='#CCCCCC'";
                            }
                            else
                            {
                              $whabilitaposqui = 'Disabled';
                              $wbgcolor = "bgcolor='#A9A5A5'";
                            }
                   if ($wpatronposquirur == 'on')
                        {
                            $wchecked = 'checked';
                        }
                  }
                  else
                  {
                      //Verifica si es posquirurgico
                    if ($wposquirur == 'on')
                        {

                            if($whabilitado == 'Enabled')
                            {
                                $whabilitaposqui = 'Enabled';

                            }
                            else
                            {
                              $whabilitaposqui = 'Disabled';
                            }
                            $wchecked = 'checked';
                            $wbgcolor = "bgcolor=".$wcolor."";
                        }
                        else
                        {
                            $whabilitaposqui = 'Disabled';
                            $wbgcolor = "bgcolor='#A9A5A5'";
                        }
                  }

              //Postquirurgico

              //echo "<td $wbgcolor align=center id='tdcajonposquirur".$i."'><SPAN id='wcajonposquirurspan".$i."' class='tooltip { direction: n; width: auto; font-weight:bold; background: #222; color: #ddd; }' title='POSTQUIRÚRGICO'><INPUT TYPE='checkbox' id='wcajonposquirur$i' NAME='wcelda[".$i."][".$j."]' $wchecked $whabilitaposqui onClick='grabar_posqx(\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$whis[$i]."\",\"".$wing[$i]."\",\"".$whab[$i]."\",\"".$wser."\",\"wcajonposquirur$i\",this,\"tdcajonposquirur$i\",\"$wedad1[$i]\",\"$wtem[$i]\",\"$wusuario\",\"$wfec\",\"$wcco\",\"$whora_maxima_modificacion\",\"$whora_maxima_cancelacion\", \"$whora\");'><div id='patron_dato$i-$j'></div></SPAN></SPAN></td>";
		      echo "<td id='cajon$i-posqx' style='display: none; cursor: default' </td>";
              $wpatrones = array();

			  if ($num_mov > 0 )
				  $wpatrones=explode(",",$row_mov['movdie']);

			  // Ciclo foreach para crear un arreglo donde los indices sean el codigo del patron, para hacer la posterior comparacion
			  $wpatrones_pac = array();

              foreach($wpatrones as $key => $value)
			  {
				$wpatrones_pac[$value] = $value;

			  }

                //Se reasignan los datos del foreach anterior a la variable $wpatrones.
                $wpatrones = $wpatrones_pac;

                //Vuelvo a recorrer el result set de las dietas y por cada uno busco si el paciente la tiene seleccionada
                mysql_data_seek($res_die,0);   //coloco el puntero en el ** 1er registro ** del result set de dietas

			    //Trae los rangos de horas para habilitar un patron en especifico
			    $q1="SELECT serhida, serhfda, serpda "
				   ."  FROM ".$wbasedato."_000076 "
				   ." WHERE sercod = '".$wser."'";
				$res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$row1 = mysql_fetch_array($res1);

                $whi_desp_adicion = $row1[0];
				$wvhf_desp_adicion = $row1[1];
				$wserviciocod = $row1[2];

			    $whabilitado_aux = $whabilitado;

				//Esta funcion permite identificar cual de los patrones se deja habilitado en un horario determinado
				$wvalidacionhoras = analisishoras($whi_desp_adicion, $wvhf_desp_adicion, $wfec);

                $whora =(string)date("H:i:s");
                $wfechahoy = explode("-",$wfecha);
                $whoraactual = explode(":",$whora);
                $whoraactual1 = mktime($whoraactual[0],$$whoraactual1[1],$$whoraactual1[2],$wfechahoy[1],$wfechahoy[2],$wfechahoy[0]);

				$dato = '';
				$patronesacumulados = '';

               //Este ciclo se refiere a cada uno de los patrones.
				for ($j=1;$j<=$num_die;$j++)
					 {
					  $row_die = mysql_fetch_array($res_die);

                      //Evaluamos cual es el patron que debe quedar activo y en que rango de horas.
					  if ($row_die['diecod'] == $wserviciocod and $wvalidacionhoras['whoraactual'] >= $wvalidacionhoras['whoraini'] and $whoraactual1 < $wvalidacionhoras['whorafin'])
					    {
							$whabilitado = 'Enable';
						}

                     $wservgrabado = consultarservgrabado($wser, $whis[$i], $wing[$i]); //Consulta si el registro esta grabado para el dia de hoy

                    //$row_die[4]  se refiere a que se pueda combinar o no, off = no, on = si, $row_die[7] indica que el patron es individual.
					  if ($row_die[4]=="off" and $row_die[7]=='on')
						 {

                          if ($row_die[6] != 'on') // No valida horario
                            {
                              $wnovalidah = '';
                            }
                            else
                                {
                                  $wnovalidah = 'novalida'; //Variable para validar la respuesta del tooltip para el patron DSN, el cual no tiene restriccion de horario.
                                }

                            $dato = "<a class='tooltip preload {direction:ne;width:250px;} sticky' href='../reportes/rep_dietasservind.php?wemp_pmla=".$wemp_pmla."&wpatron=".$row_die[0]."&whis=".$whis[$i]."&wing=".$wing[$i]."&wser=".$wser."&wfecha=".$wfec."&wnovalidah=".$wnovalidah."&wcco=".$wcco."&consultaAjax='><span id='wprodind".$i."' id='info_servind'><img src='../../images/medical/movhos/info.png' id='info'  onclick='return false;' border='0' height='17' width='15'/></span></a>";

						 }
                         else
                         {
                             $dato = '';
                         }
                      // Cuenta la cantidad de patrones que tiene asociado el paciente
					  if(count($wpatrones) > 0)
						{
                          //Compara que el patron sea igual a la posicion del patron asociado al paciente
                          if (@$wpatrones[$row_die['diecod']])
							 {

                                $wcombinable = $row_die[4];
                                $wcombinable1 = $row_die[4]; // Variable para controlar los servicios no combinables, los cuales no deben ingresan al arreglo de patrones.

                                //Si el patron es combinable la casilla de marcara de color amarillo y el cajon de seleccion se marcara, si no, quedara en el color
                                //normal y la casilla quedara inactiva.

                                if ($wcombinable1 != 'off')
                                        {

                                            $wbgcolor = "bgcolor=".$wcolor."";
                                            $wchecked = 'checked';

                                        }
                                else
                                        {
                                            if ($wservgrabado != 'on' and $whabilitado == 'Enabled')
                                                {
													//Si es servicio individual o TMO no se marcara el cajon, ya que no sera solicitado automaticamente.
													$wbgcolor = '';
													$wchecked = 'unchecked';

                                                }
                                                else
                                                {
                                                    $wbgcolor = "bgcolor=".$wcolor."";
                                                    $wchecked = 'checked';

                                                }
                                        }

								//Si el patron no es combinable y es DSN, se marcará.
								if($row_die['diecod'] == $wpatron_nutricion)
									{
										$wbgcolor = "bgcolor=".$wcolor."";
										$wchecked = 'checked';
									}


                            //Si el centro de costos no programa automaticamente, entonces no se pintaran los cajones.
                           if ($wprog_automatico == 'off' and $wservgrabado == 'off')
                                {
                                $wbgcolor = '';
                                $wchecked = 'unchecked';
                                }

                                // $row_die[4] se refiere al tipo de patron de seleccion unica, si es de seleccion unica el estado es on, viene de la consulta a la tabla movhos_000041

                                echo "<td $wbgcolor align=center id='cajon".$i."-".$j."'><SPAN id='wcelda".$i."-".$j."' class='tooltip { direction: n; width: auto; font-weight:bold; background: #222; color: #ddd; text-align:center;}' title='".$wdietas[$j]."'><INPUT TYPE='checkbox' id='patron_grid$i-$j' NAME='wcelda[".$i."][".$j."]' $wchecked ".$whabilitado." onClick='combina("."\"".$wemp_pmla."\"".","."\"".$i."\"".","."\"".$j."\"".","."\"".$row_die[0]."\"".","."\"".$wadi_ser."\"".","."\"".$whis[$i]."\"".","."\"".$wing[$i]."\"".","."\"".$wcombinable."\"".","."\"".trim($wcco)."\"".","."\"".$wser."\"".","."\"".$wfec."\"".","."\"".$whab[$i]."\"".","."\"".$wpac[$i]."\"".","."\"".$wdpa[$i]."\"".","."\"".$wtid[$i]."\"".","."\"".$wptr[$i]."\"".","."\"".$wmue[$i]."\"".","."\"".$wedad1[$i]."\"".","."\"".$walp[$i]."\"".","."\"".$wtem[$i]."\"".","."\"".$west[$i]."\"".","."\"".$wusuario."\"".",\"0\",\"\",this,\"".trim($row_die['diepqu'])."\", \"$wrol_usuario\", \"$wpatron_nutricion\", \"$wrolnutricion\");'><div id='patron_dato$i-$j'></div></SPAN></SPAN>".$dato."</td>"; //Cajon con patron activo


                                if ($wcombinable1 == 'off' and $row_die[6] != 'on')  //$row_die[6] !='on' quiere decir que no valida horarios de servicio (DSN)
                                    {
                                    $row_die['diecod'] = 'servicio_individual'; //Se le da un valor al SI para que pueda ingresar la informacion a la solicitud automatica.
                                    }

                                $patronesacumulados .= $row_die['diecod'].",";

							 }
							else
							 {
                                  $wcombinable = $row_die[4];
								  echo "<td align=center id='cajon".$i."-".$j."'><SPAN id='wcelda".$i."-".$j."' class='tooltip { direction: n; width: auto; font-weight:bold; background: #222; color: #ddd; }' title='".$wdietas[$j]."'><INPUT TYPE='checkbox' id='patron_grid$i-$j' NAME='wcelda[".$i."][".$j."]' UNCHECKED ".$whabilitado." onClick='combina("."\"".$wemp_pmla."\"".","."\"".$i."\"".","."\"".$j."\"".","."\"".$row_die[0]."\"".","."\"".$wadi_ser."\"".","."\"".$whis[$i]."\"".","."\"".$wing[$i]."\"".","."\"".$wcombinable."\"".","."\"".trim($wcco)."\"".","."\"".$wser."\"".","."\"".$wfec."\"".","."\"".$whab[$i]."\"".","."\"".$wpac[$i]."\"".","."\"".$wdpa[$i]."\"".","."\"".$wtid[$i]."\"".","."\"".$wptr[$i]."\"".","."\"".$wmue[$i]."\"".","."\"".$wedad1[$i]."\"".","."\"".$walp[$i]."\"".","."\"".$wtem[$i]."\"".","."\"".$west[$i]."\"".","."\"".$wusuario."\"".",\"0\",\"\",this,\"".trim($row_die['diepqu'])."\", \"$wrol_usuario\", \"$wpatron_nutricion\", \"$wrolnutricion\");'><div id='patron_dato$i-$j'></div></SPAN>".$dato."</td>"; // Cajon sin patron queda inactivo


							 }
					 }
					 else
					 {

                         $wcombinable = $row_die[4];
						 echo "<td align=center id='cajon".$i."-".$j."'><SPAN id='wcelda".$i."-".$j."' class='tooltip { direction: n; width: auto; font-weight:bold; background: #222; color: #ddd; }' title='".$wdietas[$j]."'><INPUT TYPE='checkbox' id='patron_grid$i-$j' NAME='wcelda[".$i."][".$j."]' UNCHECKED ".$whabilitado." onClick='combina("."\"".$wemp_pmla."\"".","."\"".$i."\"".","."\"".$j."\"".","."\"".$row_die[0]."\"".","."\"".$wadi_ser."\"".","."\"".$whis[$i]."\"".","."\"".$wing[$i]."\"".","."\"".$wcombinable."\"".","."\"".trim($wcco)."\"".","."\"".$wser."\"".","."\"".$wfec."\"".","."\"".$whab[$i]."\"".","."\"".$wpac[$i]."\"".","."\"".$wdpa[$i]."\"".","."\"".$wtid[$i]."\"".","."\"".$wptr[$i]."\"".","."\"".$wmue[$i]."\"".","."\"".$wedad1[$i]."\"".","."\"".$walp[$i]."\"".","."\"".$wtem[$i]."\"".","."\"".$west[$i]."\"".","."\"".$wusuario."\"".",\"0\",\"\",this,\"".trim($row_die['diepqu'])."\", \"$wrol_usuario\", \"$wpatron_nutricion\", \"$wrolnutricion\");'><div id='patron_dato$i-$j'></div></SPAN>".$dato."</td>"; // Sin patrones relacionados
					 }

                     $whabilitado = $whabilitado_aux;

				 }

				//Guarda la informacion de los patrones asociados de los servicios anteriores para cada historia
			  $wpatronfinal1=substr($patronesacumulados,0,strlen($patronesacumulados)-1);
		      $wpatronfinal=explode(",",$wpatronfinal1);
              $wpatronfinal1 = trim($wpatronfinal1,","); //Quito la coma del final
              $wvalidahorario = validahorariopatron($wpatronfinal1);   //Valido si el patron final valida horario.
              $wservgrabado = consultarservgrabado($wser, $whis[$i], $wing[$i]); //Consulta si el registro esta grabado para el dia de hoy

                if ($wservgrabado != 'on' and $wpatronfinal[0] != '' and $whabilitado == 'Enabled')
                    {
					//Esta validacion se da cuando el centro de costos programa de forma automatica, urgencias es uno de los que no programa
					//de forma automatica.
                     if($wprog_automatico == 'on')
                        {

                        $wayerfecha = time()-(1*24*60*60); //Resta un dia
                        $wayer1 = date('Y-m-d', $wayerfecha); //Formatea dia

                        //Verifica si el paciente tiene solicitud dsn del dia anterior, asi haya ingresado por un patron que no es DSN.
                        if($wpatronfinal[0] != 'servicio_individual')
                            {

							//Le quita al arreglo que viene el servicio individual, en caso de que en el ultimo serivicio alla sido solicitado.
							$wpatronfinal1 = str_replace(",servicio_individual","", $wpatronfinal1);

							if($wvalidahorario != 'on'){

								//Esta funcion inserta toda la informacion del ultimo servicio que le programaron al paciente.
								procesar_datos_automatico($wemp_pmla, $whis[$i], $wing[$i], $wpatronfinal1, $wcco, $wser, $wfec, $whab[$i], $wpac[$i], $wdpa[$i], $wtid[$i], $wptr[$i], $wmue[$i], $wedad1[$i], $walp[$i], $wtem[$i], $west[$i], $wusuario, '2', '', '', '', '', 'on', $wservgrabado, $wcantidad, $wmedia_porcionbd, $wobservaciones, $wintolerancias, $wpatron_nutricion);

							//En este caso ingresa cuando el ultimo patron que se solicito sea un servicio individual, ya que el paciente tiene solicitud
							//de ese patron en el dia anterior, por lo tanto debe hacer la solicitud de DSN y no del ultimo patrón, el cual es diferente a DSN,
							//esto se evalua en el parametro $wvalidahorario != 'on'.
							}else{

								//Se comenta esta funcion ya que la solicitud automtica de DSN viene desde el dia anterior, osea, no se pide automatica para el mismo dia, y por eso esta funcion se usa un poco mas abajo.
								//procesar_datos_dsnauto($wemp_pmla, $wbasedato, $whis[$i], $wing[$i], $whab[$i], $wcco, $wpatron_nutricion, '');

								}

                            }


                        }
                        else
                        {
                            grabar_encabezado($wser, $wcap, $wobs_enf);
                        }
                    }

					//Consulta el servicio desde donde se solicita la DSN. Jonatan 22 Abril 2014.
					$wdsnservicioauto = consultarAliasPorAplicacion($conex, $wemp_pmla, 'servicioDSNautomatico');

                    //Aqui ingresa cuando el ultimo patron solicitado sea DSN, el cual no valida horario, ademas verifica en que sericio esta para realizar la solicitud automatica.
                    if ( $wvalidahorario == 'on' and $wser == $wdsnservicioauto and $whabilitado == 'Enabled')
                        {

                            procesar_datos_dsnauto($wemp_pmla, $wbasedato, $whis[$i], $wing[$i], $whab[$i], $wcco, $wpatron_nutricion, 'dsndesdeservicio');

                        }

			  //===== MEDIA PORCION =========
				//En esta parte se muestra si el producto es solicitado en media porcion, y tiene varias validaciones para identificar si esta habilitado, o chequeado.

              $wpatron_combinable = valida_combinable($wpatronfinal[0]);
              $whabilitado = validaciones('', '', '', $wser, "Consulta");


              if ($wmedia_porcionbd == 'on' and $wpatron_combinable == 'on')
                {
                $wchecked = 'checked';
                $wbgcolor = "bgcolor=".$wcolor."";

                if($whabilitado == 'Enabled')
                    {
                    $whabilitado = 'Enabled';
                    }
                else
                    {
                    $whabilitado = 'disabled=""';
                    }

                }
                else
                {
                    if ($wpatronfinal[0] != '' and $wpatron_combinable == 'on' and $whabilitado == 'Enabled')
                        {
                        $whabilitado = 'Enabled';
                        $wchecked = 'unchecked';
                        $wbgcolor = "bgcolor='#A9A5A5'";
                        }
                    else
                        {
                        $whabilitado = 'disabled=""';
                        $wbgcolor = "bgcolor='#A9A5A5'";
                        $wchecked = 'unchecked';
                        }
                }

               //Media porcion
              echo "<input type='hidden' id='dato_media_porcion' name='dato_media_porcion' value='".$j."'>";

              echo "<td align=center $wbgcolor id='media_porciontd$i-$j'><input type='checkbox' $whabilitado $wchecked id='media_porcion$i-$j' name='media_porcion' value='media' onClick='grabar_media_porcion(\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$whis[$i]."\",\"".$wing[$i]."\",\"".$whab[$i]."\",\"".$wser."\",\"media_porcion$i-$j\",this,\"media_porciontd$i-$j\",\"$wfec\",\"$wcco\",\"$wusuario\")'></td>";
			  //=============================

              // DIAGNOSTICO

              $wdiag=traer_diagnostico($whis[$i], $wing[$i], $wfec);
		      $whabilitaobs = '';
              $wobs = array();
              $whabilitaint = '';
              $wobs[$i] = '';

              if ($wdiag=="Sin Diagnostico")    //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
              {
                   $dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
                   $wayer = date('Y-m-d', $dia); //Formatea dia
                   $wdiag=traer_diagnostico($whis[$i], $wing[$i], $wayer);
              }

              echo "<td align=center><TEXTAREA id='diagnostico' readonly>".trim($wdiag)."</TEXTAREA></td>";

              //OBSERVACIONES DEL PACIENTE
	          //Busco si hay alguna observacion en el ingreso actual del paciente
		      $q =  " SELECT MAX(CONCAT(fecha_data,hora_data)),movobs "
		           ."   FROM ".$wbasedato."_000077 "
		           ."  WHERE movhis  = '".$whis[$i]."'"
		           ."    AND moving  = '".$wing[$i]."'"
		           ."    AND movfec  = '".$wfecha."'"       //Solo consulta observaciones del mismo dia
		           //."    AND movest  = 'on' "
                   ."    AND movser  = '".$wser."'"
		           //."    AND movobs != '' "
		           ."  GROUP BY 2 "
		           ."  ORDER BY 1 DESC ";
		      $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      	  $row_mov = mysql_fetch_array($res_mov);

              //Valida si hay observacion en el ultimo registro para la historia e ingreso, si no es asi, muestra las observaciones del kardex.
              if ($row_mov['movobs'] != '')
              {
      		     $wobs[$i]=trim($row_mov[1]);
              }

			  if( $wdisabledTextArea == 'Enabled' ){

				  //Se evalua si el horario actual es mayor al horario de modificacion, si es mayor deshabilita los cajones de insercion de texto en las observaciones e intolerancias.
				  if ($whora > $whora_maxima_modificacion)
				  {
					 //Solo se habilita el cajon de editar observaciones en adicion para cco urgencias
                     $whabilitaobs = "Enabled";
					  /*if ($wurg != 'off')
						  {
							 $whabilitaobs = "Enabled";
						  }
					  else
						  {
							//solo se habilita el cajon de editar observaciones en adicion para cco urgencias
							$whabilitaobs = "Disabled";

						  }*/
				  }
			  }
			  else{
				  $whabilitaobs = "Disabled";
			  }

	      	  echo "<td align=center><TEXTAREA id='wobs_".$i."".$j."' maxlength='".$wlimite_caracteres_observ."' class='textareadietas' historia='{$whis[$i]}' ingreso='$wing[$i]' tipo='observacion' onBlur='grabar_observ_intoler(\"$wemp_pmla\", \"$wbasedato\", \"$whis[$i]\", \"$wing[$i]\", \"$whab[$i]\", \"$wser\", \"$i\", \"$j\", \"$wusuario\", \"o\", \"$wcco\", \"$wfec\")' ".$whabilitaobs.">".trim($wobs[$i])."</TEXTAREA></td>";


	      	  //INTOLERANCIAS
	      	  //Busco si hay alguna Intolerancia en cualquier ingreso del paciente
			  $q =  " SELECT MAX(CONCAT(fecha_data,hora_data)), movint "
		           ."   FROM ".$wbasedato."_000077 "
		           ."  WHERE movhis  = '".$whis[$i]."'"
		           //."    AND moving  = '".$wing[$i]."'"
		           //."    AND movest  = 'on' "
		           ."    AND movint != '' "
		           ."  GROUP BY 2 "
		           ."  ORDER BY 1 DESC ";
		      $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      		  $num_mov = mysql_num_rows($res_mov);

      		  if ($num_mov > 0)
      		    {
	      		 $row_mov = mysql_fetch_array($res_mov);
      		     $wint[$i]=trim($row_mov['movint']);
      		    }
      		   else
      		      $wint[$i]='';

			  if( $wdisabledTextArea == 'Enabled' ){
					//Se evalua si el horario actual es mayor al horario de modificacion, si es mayor deshabilita los cajones de insercion de texto en las observaciones e intolerancias.
				  if ($whora > $whora_maxima_modificacion)
				  {
						//solo se habilita el cajon de editar observaciones en adicion para cco urgencias
						$whabilitaint = "Disabled";

				  }
			  }
			  else{
				  $whabilitaint = "Disabled";
			  }

			echo "<td align=center><TEXTAREA id='wint_".$i."".$j."' maxlength='".$wlimite_caracteres_observ."' class='textareadietas' tipo='intolerancia' historia='{$whis[$i]}' ingreso='$wing[$i]' onBlur='grabar_observ_intoler(\"$wemp_pmla\", \"$wbasedato\", \"$whis[$i]\", \"$wing[$i]\", \"$whab[$i]\", \"$wser\", \"$i\", \"$j\", \"$wusuario\", \"i\", \"$wcco\" , \"$wfec\")' ".$whabilitaint.">".trim($wint[$i])."</TEXTAREA></td>";
			 //echo "<td align=center><TEXTAREA NAME=wint[".$i."] ".$whabilitado.">".trim($wint[$i])."</TEXTAREA></td>";


	      	  //ALERTAS
			  // Consultar alertas en movhos_000220
				$alergiasAnteriores = consultarAlergiaAlertas($whis[$i], $wing[$i]);

				echo "<td align=center><TEXTAREA id='wale_".$i."".$j."'  readonly>".trim($alergiasAnteriores)."</TEXTAREA></td>";


	      	  //======================================================================================================
		      //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
		      $wafin=clienteMagenta($wdpa,$wtid,$wtpa,$wcolorpac);
		      if ($wafin)
			     echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
			    else
			      echo "<td>&nbsp</td>";
			  //======================================================================================================

			  echo "</tr>";

              //Al inicializar esta variable no se confunde con el chekeo del cajon posquirurgico.
              $wchecked = '';

			 }
	     }

         //Este segmento sirve para recargar la pagina almenos una vez despues de haber cargado un servicio de forma automatica.
         $datosesion = $wser.$wcco.$wfec;

        if($_SESSION['datosession'] != $datosesion and $whabilitado_aux == 'Enabled')
            {
                $datosesion = $wser.$wcco.$wfec;
                $_SESSION['datosession']=$datosesion;
                echo "<script>enter();</script>";


            }

     }

  //==================================================================================================================
  //==================================================================================================================


  //==================================================================================================================
  //==================================================================================================================
  //===========================================================================================================
  function consultarZonaActual( $historia_b, $historia_c ){
    global $wbasedato;
    global $conex;

    $query = " SELECT Habzon
                 FROM {$wbasedato}_000020
                WHERE Habhis = '{$historia_b}'
                  AND Habing = '{$historia_c}'
                  AND Habest = 'on'";
    $rs    = mysql_query( $query, $conex );
    $row   = mysql_fetch_row( $rs );
    return( $row['Habzon'] );
  }
  //Traigo los costos de cada Patron
  function traer_costo_del_patron($wpatron, $wtipemp, $wedad_pac, &$res_cos, $wser, &$wcob, &$wsec, &$wcbi, &$wptrcobra, $wmedia_porcion, $wpatron_seleccionado, $wchequeados, $wautomatico, $wpcomb, $wnocobrapatron, $whis, $wing, $wmodificar)
     {

	   global $wbasedato;
	   global $conex;


       //Se analiza cuantos elementos hay el arreglo, si solo hay uno hace un str_replace
       //del patron_seleccionado con un espacio en blanco para que haga la consulta sobre ese patron, si es mayor de un patron, reemplara el seleccionado
       //con nada (nisiquiera vacio), para que haga la consulta sobre los seleccionados, esto quiere decir, sin incluir al seleccionado.
       $wcuantos = explode(',',$wchequeados);
       $wpatron_seleccionado1 = $wpatron_seleccionado; // Esta variable se usa como auxiliar para cuando no hay costo por empresa.
       if(count($wcuantos) == 1)
       {
        $wchequeados = str_replace($wpatron_seleccionado,' ',$wchequeados);
        $wpatron_seleccionado = str_replace("','",'',$wpatron);
       }
       else
       {
        $wchequeados = str_replace($wpatron_seleccionado,'',$wchequeados);
       }

       $wseleccionados = explode(',',$wchequeados);

       $wresultado = array();


       //Recorro cada elemento del arreglo $wseleccionados, por cada uno se genera el siguiente dato "patsec like ('%".$value."%')";
        foreach ($wseleccionados as $key => $value )
        {
            $wresultado[]= "patsec like ('%".$value."%')";
        }

        //Tomo cada elemento del arreglo y lo uno con AND para utilizarlo en la siguiente consulta.
        $warregloconsulta = implode(' AND ',$wresultado);

      //Busco el costo para el tipo de empresa del paciente, Si no existe con este tipo de empresa lo
	  //busco con '*' y retorno el numero de filas encontradas
	  //*-------------------------------------------------------

	   if(count($wcuantos) > 1 and $wmodificar != 1)
        {
			//Consulto si el paciente es POS
			$tipo_pos = paciente_tipo_pos ($wtipemp, $wser, $wedad_pac);

			//Si el paciente es tipo POS consulto si existe costo correspondiente, especifico por el codigo de la empresa.
			if ($tipo_pos == 'SI')
			{
			  $q = " SELECT patppa, patsec "
				  ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
				  ."  WHERE ".$warregloconsulta.""
				  ."    AND patpri = '".$wpatron_seleccionado."'"
				  ."    AND costem  LIKE '%".$wtipemp."%' "
				  ."    AND cosedi <= '".($wedad_pac*12)."'"
				  ."    AND cosedf >=  '".($wedad_pac*12)."'"
				  ."    AND cosest  = 'on' "
				  ."    AND patest  = 'on' "
				  ."    AND diecbi  = 'on' "
				  ."    AND cosser  = '".$wser."'"
				  ."    AND cospat  = diecod "
				  ."    AND cospat  = patpri "
				 ."ORDER BY diesec DESC, cosact DESC";
				$res_ppa = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num_ppa = mysql_num_rows($res_ppa);
			}
			else
			{
              $q =   " SELECT patppa, patsec "
                    ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                    ."  WHERE ".$warregloconsulta.""
                    ."    AND patpri = '".$wpatron_seleccionado."'"
                    ."    AND costem  LIKE '%*%' "
                    ."    AND cosedi <= '".($wedad_pac*12)."'"
                    ."    AND cosedf >=  '".($wedad_pac*12)."'"
                    ."    AND cosest  = 'on' "
                    ."    AND patest  = 'on' "
                    ."    AND diecbi  = 'on' "
                    ."    AND cosser  = '".$wser."'"
                    ."    AND cospat  = diecod "
                    ."    AND cospat  = patpri "
                    ."ORDER BY diesec DESC, cosact DESC";
				$res_ppa = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num_ppa = mysql_num_rows($res_ppa);
			}

          if ($num_ppa > 0)
          {

          $wcontrol = false;

          while($row_ppa = mysql_fetch_array($res_ppa))
			 {

              $wsecundarios = $row_ppa['patsec'];
              $warreglosec = explode(',', $wsecundarios);
             // print_r($warreglosec);
              $wchequeados1 = explode(',', $wchequeados);
             // print_r($wchequeados1);

              $wcuantossec = count($warreglosec);
              $wcuantoschec = count($wchequeados1)-1;   //El menos uno (-1) es para eliminar un elemento del arreglo, ese elemento es una posicion vacia que no es necesaria en el arreglo.

                if($wcuantossec == $wcuantoschec)
                {

                    $wnumero = array();
                    $wnumero = array_diff($wseleccionados, $warreglosec);

                    if (count($wnumero) <= 1)
                    {

                        $wpatron_seleccionado = $row_ppa['patppa'];
                        $wcontrol = true;
                        break;

                    }
                }

             }

             if ($wmodificar != '1' and !$wcontrol)
                        {
                        echo "4"; // Este dato genera un mensaje que no se puede combinar.
                        return;
                        }
          }
           else
          {
              if($wnocobrapatron != 'on' and $wpcomb != 'off')
                {

                echo "4";  //No se puede combinar, ya que no hay patron ppal para la combinacion necesitada.
                $num_cos = 0;
                return $num_cos;
                return;
                }
          }

         }


		//Consulto si el paciente es POS
		$tipo_pos = paciente_tipo_pos ($wtipemp, $wser, $wedad_pac);

		//Si el paciente es tipo POS consulto si existe costo correspondiente, especifico por el codigo de la empresa.
		if ($tipo_pos == 'SI')
		{

         if($wmodificar == 1)
            {


                if(count($wcuantos) == 1)
                {

                    $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
                        ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                        ."  WHERE costem  LIKE '%".$wtipemp."%' "
                        ."    AND cosedi <= '".($wedad_pac*12)."'"
                        ."    AND cosedf >=  '".($wedad_pac*12)."'"
                        ."    AND cosest  = 'on' "
                        ."    AND patest  = 'on' "
                        ."    AND diecbi  = 'on' "
                        ."    AND cosser  = '".$wser."'"
                        ."    AND cospat  = diecod "
                        ."    AND cospat  = patpri "
                        ."    AND patpri  = '".$wchequeados."'"
                        ." GROUP BY cospat "
                        ." ORDER BY cosact DESC";
                    $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $num_cos = mysql_num_rows($res_cos);

                }
                else
                {

                    $wchequeados1 = explode(',', $wchequeados);

                    $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
                        ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                        ."  WHERE costem  LIKE '%".$wtipemp."%' "
                        ."    AND cosedi <= '".($wedad_pac*12)."'"
                        ."    AND cosedf >=  '".($wedad_pac*12)."'"
                        ."    AND cosest  = 'on' "
                        ."    AND patest  = 'on' "
                        ."    AND diecbi  = 'on' "
                        ."    AND cosser  = '".$wser."'"
                        ."    AND cospat  = diecod "
                        ."    AND cospat  = patpri "
                        ."    AND patpri  = '".$wchequeados1[0]."'"
                        ."    AND patsec  = '".$wchequeados1[1]."'"
                        ." GROUP BY cospat "
                        ." ORDER BY cosact DESC";

                    $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $num_cos = mysql_num_rows($res_cos);

                    }

            }
		  else
            {

			if($wnocobrapatron != 'on' and $wpcomb != 'off')
                {

				$q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
					."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
					."  WHERE costem  LIKE '%".$wtipemp."%' "
					."    AND cosedi <= '".($wedad_pac*12)."'"
					."    AND cosedf >=  '".($wedad_pac*12)."'"
					."    AND cosest  = 'on' "
					."    AND patest  = 'on' "
					."    AND cosser  = '".$wser."'"
					."    AND cospat  = diecod "
					."    AND cospat  = patpri "
					."    AND diecbi  = 'on' "
					."    AND patpri  = '".$wpatron_seleccionado."'"
					." GROUP BY cospat "
					." ORDER BY cosact DESC";

				$res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num_cos = mysql_num_rows($res_cos);
				}
           }
        }
         //Si no es tipo POS, entonces busco con asterisco.
        else
	     {

           if(count($wcuantos) > 1 and $wmodificar != 1)
            {

          $q = " SELECT patppa, patsec "
		      ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
		      ."  WHERE ".$warregloconsulta.""
              ."    AND patpri = '".$wpatron_seleccionado1."'"
		      ."    AND costem  = '*' "
		      ."    AND cosedi <= '".($wedad_pac*12)."'"
		      ."    AND cosedf >=  '".($wedad_pac*12)."'"
		      ."    AND cosest  = 'on' "
              ."    AND patest  = 'on' "
		      ."    AND cosser  = '".$wser."'"
		      ."    AND cospat  = diecod "
              ."    AND cospat  = patpri "
			 ."ORDER BY diesec DESC, cosact DESC";
		  $res_ppa = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num_ppa = mysql_num_rows($res_ppa);


          if ($num_ppa > 0)
          {

          $wcontrol = false;
          while($row_ppa = mysql_fetch_array($res_ppa))
			 {

              $wsecundarios = $row_ppa['patsec'];
              $warreglosec = explode(',', $wsecundarios);
             // print_r($warreglosec);
              $wchequeados1 = explode(',', $wchequeados);
             // print_r($wchequeados1);

              $wcuantossec = count($warreglosec);
              $wcuantoschec = count($wchequeados1)-1;   //El menos uno (-1) es para eliminar un elemento del arreglo, ese elemento es una posicion vacia que no es necesaria en el arreglo.

                if($wcuantossec == $wcuantoschec)
                {

                    $wnumero = array();
                    $wnumero = array_diff($wseleccionados, $warreglosec);

                    if (count($wnumero) <= 1)
                    {

                        $wpatron_seleccionado1 = $row_ppa['patppa'];
                        $wcontrol = true;
                        break;

                    }
                }

             }

             if ($wmodificar != '1' and !$wcontrol)
                        {
                        echo "4"; // Este dato genera un mensaje que no se puede combinar.
                        return;
                        }
             }

          else
          {
              if($wnocobrapatron != 'on' and $wpcomb != 'off')
                {

                echo "4";  //No se puede combinar, ya que no hay patron ppal para la combinacion necesitada.
                $num_cos = 0;
                return $num_cos;
                return;
                }
          }

         }

         if($wmodificar == 1)
            {


                if(count($wcuantos) == 1 and $wchequeados != '')
                {

                    $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
                        ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                        ."  WHERE costem  = '*'"
                        ."    AND cosest  = 'on' "
                        ."    AND patest  = 'on' "
                        ."    AND cosser  = '".$wser."'"
                        ."    AND cospat  = diecod "
                        ."    AND cospat  = patpri "
                        ."    AND patpri  = '".$wchequeados."'"
                        ." GROUP BY cospat "
                        ." ORDER BY cosact DESC";
                    $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $num_cos = mysql_num_rows($res_cos);

                    if ($num_cos == 0 and $wpcomb != 'off' and $wnocobrapatron != 'on')
                    {
                        echo "10"; //Muestra un mensaje javascript diciendo que no se encontro costo para el patron en la tabla 79 de movhos (funcion js grabar_datos).

                        if ($num_cos == '')
                            //  $num_cos = 0;
                            return $num_cos;
                        return;
                    }

                    $q = " SELECT count(*) "
                        ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                        ."  WHERE costem  = '*'"
                        ."    AND cosedi <= '".($wedad_pac*12)."'"
                        ."    AND cosedf >=  '".($wedad_pac*12)."'"
                        ."    AND cosest  = 'on' "
                        ."    AND patest  = 'on' "
                        ."    AND cosser  = '".$wser."'"
                        ."    AND cospat  = diecod "
                        ."    AND cospat  = patpri "
                        ."    AND patpri  = '".$wchequeados."'"
                        ." GROUP BY cospat "
                        ." ORDER BY cosact DESC";
                    $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $num_cos = mysql_num_rows($res_cos);

                    if ($num_cos == 0 and $wpcomb != 'off' and $wnocobrapatron != 'on')
                    {
                        echo "1020"; //Muestra un mensaje javascript diciendo que no se encontro costo para el patron en la tabla 79 de movhos (funcion js grabar_datos).

                        if ($num_cos == '')
                            //  $num_cos = 0;
                            return $num_cos;
                        return;
                    }
                }
                else
                {

                    $wchequeados1 = explode(',', $wchequeados);

                    $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
                        ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                        ."  WHERE costem  = '*' "
                        ."    AND cosedi <= '".($wedad_pac*12)."'"
                        ."    AND cosedf >=  '".($wedad_pac*12)."'"
                        ."    AND cosest  = 'on' "
                        ."    AND patest  = 'on' "
                        ."    AND cosser  = '".$wser."'"
                        ."    AND cospat  = diecod "
                        ."    AND cospat  = patpri "
                        ."    AND patpri  = '".$wchequeados1[0]."'"
                        ."    AND patsec  = '".$wchequeados1[1]."'"
                        ." GROUP BY cospat "
                        ." ORDER BY cosact DESC";

                    $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $num_cos = mysql_num_rows($res_cos);

                    if ($num_cos == 0 and $wpcomb != 'off' and $wnocobrapatron != 'on' and $wchequeados != '')
                    {
                        echo "102"; //Muestra un mensaje javascript diciendo que no se encontro costo para el patron en la tabla 79 de movhos (funcion js grabar_datos).

                        if ($num_cos == '')
                            $num_cos = 0;
                            return $num_cos;


                        return;
                    }

                }

            }
		  else
            {

            $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
                ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                ."  WHERE costem  = '*' "
                ."    AND cosedi <= '".($wedad_pac*12)."'"
                ."    AND cosedf >= '".($wedad_pac*12)."'"
                ."    AND cosest  = 'on' "
                ."    AND patest  = 'on' "
                ."    AND cosser  = '".$wser."'"
                ."    AND cospat  = diecod "
                ."    AND cospat  = patpri "
                ."    AND patpri  = '".$wpatron_seleccionado."'"
                ." GROUP BY cospat "
                ." ORDER BY cosact DESC";

            $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num_cos = mysql_num_rows($res_cos);

            if ($num_cos == 0 and $wpcomb != 'off' and $wnocobrapatron != 'on')
            {
                echo "10"; //Muestra un mensaje javascript diciendo que no se encontro costo para el patron en la tabla 79 de movhos (funcion js grabar_datos).

                if ($num_cos == '')
                    //  $num_cos = 0;
                    return $num_cos;


                return;
            }

            $q = " SELECT count(*) "
                        ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                        ."  WHERE costem  = '*'"
                        ."    AND cosedi <= '".($wedad_pac*12)."'"
                        ."    AND cosedf >=  '".($wedad_pac*12)."'"
                        ."    AND cosest  = 'on' "
                        ."    AND patest  = 'on' "
                        ."    AND cosser  = '".$wser."'"
                        ."    AND cospat  = diecod "
                        ."    AND cospat  = patpri "
                        ."    AND patpri  = '".$wchequeados."'"
                        ." GROUP BY cospat "
                        ." ORDER BY cosact DESC";
            $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num_cos = mysql_num_rows($res_cos);

            if ($num_cos == 0 and $wpcomb != 'off' and $wnocobrapatron != 'on')
            {
                echo "1020"; //Muestra un mensaje javascript diciendo que no se encontro costo para el patron en la tabla 79 de movhos (funcion js grabar_datos).

                if ($num_cos == '')
                    //  $num_cos = 0;
                    return $num_cos;
                return;
            }
           }
		 }

       return $num_cos;



     }

   //============================================================
     //==========================================================

     //Traigo los costos del servicio asociado.
  function traer_costo_del_patron_asociado($wpatron, $wtipemp, $wedad_pac, &$res_cos_asoc, $wseraso, $wpatron_seleccionado, $wchequeados, $wautomatico, $wpcomb, $wnocobrapatron, $whis, $wing, $wmodificar, $num_cos)
     {

	   global $wbasedato;
	   global $conex;

       $wpatron_seleccionado1 = $wpatron_seleccionado; // Esta variable se usa como auxiliar para cuando no hay costo por empresa.
       if ($wseraso != '')
       {


       //Se analiza cuantos elementos hay el arreglo, si solo hay uno hace un str_replace
       //del patron_seleccionado con un espacio en blanco para que haga la consulta sobre ese patron, si es mayor de un patron, reemplara el seleccionado
       //con nada (nisiquiera vacio), para que haga la consulta sobre los seleccionados, esto quiere decir, sin incluir al seleccionado.
       $wcuantos = explode(',',$wchequeados);

       if(count($wcuantos) == 1)
       {
        $wchequeados= str_replace($wpatron_seleccionado,' ',$wchequeados);
       }
       else
       {
        $wchequeados= str_replace($wpatron_seleccionado,'',$wchequeados);
       }

       $wseleccionados = explode(',',$wchequeados);
       $wresultado = array();


       //Recorro cada elemento del arreglo $wseleccionados, por cada uno se genera el siguiente dato "patsec like ('%".$value."%')";
        foreach ($wseleccionados as $key => $value )
        {
            $wresultado[]= "patsec like ('%".$value."%')";
        }

        //Tomo cada elemento del arreglo y lo uno con AND para utilizarlo en la siguiente consulta.
        $warregloconsulta = implode(' AND ',$wresultado);
       //Busco el costo para el tipo de empresa del paciente, Si no existe con este tipo de empresa lo
	   //busco con '*' y retorno el numero de filas encontadas
        if(count($wcuantos) > 1)
            {

			//Consulto si el paciente es POS
			$tipo_pos = paciente_tipo_pos ($wtipemp, $wseraso, $wedad_pac);

			//Si el paciente es tipo POS consulto si existe costo correspondiente, especifico por el codigo de la empresa.
			if ($tipo_pos == 'SI')
			{

			$q = " SELECT patppa, patsec "
		      ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
		      ."  WHERE ".$warregloconsulta.""
              ."    AND patpri  = '".$wpatron_seleccionado."'"
		      ."    AND costem  LIKE '%".$wtipemp."%' "
		      ."    AND cosedi <= '".($wedad_pac*12)."'"
		      ."    AND cosedf >=  '".($wedad_pac*12)."'"
		      ."    AND cosest  = 'on' "
              ."    AND patest  = 'on' "
		      ."    AND cosser  = '".$wseraso."'"
		      ."    AND cospat  = diecod "
              ."    AND cospat  = patpri "
			 ."ORDER BY diesec DESC, cosact DESC";
			$res_ppa = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num_ppa = mysql_num_rows($res_ppa);
			}
          else
          {
              $q =   " SELECT patppa, patsec "
                    ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                    ."  WHERE ".$warregloconsulta.""
                    ."    AND patpri = '".$wpatron_seleccionado."'"
                    ."    AND costem  LIKE '%*%' "
                    ."    AND cosedi <= '".($wedad_pac*12)."'"
                    ."    AND cosedf >=  '".($wedad_pac*12)."'"
                    ."    AND cosest  = 'on' "
                    ."    AND patest  = 'on' "
                    ."    AND diecbi  = 'on' "
                    ."    AND cosser  = '".$wseraso."'"
                    ."    AND cospat  = diecod "
                    ."    AND cospat  = patpri "
                    ."ORDER BY diesec DESC, cosact DESC";
            $res_ppa = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num_ppa = mysql_num_rows($res_ppa);
          }

         if ($num_ppa > 0)
         {

          $wcontrol = false;
          while($row_ppa = mysql_fetch_array($res_ppa))
			 {

              $wsecundarios = $row_ppa['patsec'];
              $warreglosec = explode(',', $wsecundarios);
             // print_r($warreglosec);
              $wchequeados1 = explode(',', $wchequeados);
             // print_r($wchequeados1);

              $wcuantossec = count($warreglosec);
              $wcuantoschec = count($wchequeados1)-1;   //El menos uno (-1) es para eliminar un elemento del arreglo, ese elemento es una posicion vacia que no es necesaria en el arreglo.

                if($wcuantossec == $wcuantoschec)
                {

                    $wnumero = array();
                    $wnumero = array_diff($wseleccionados, $warreglosec);

                    if (count($wnumero) <= 1)
                    {

                        $wpatron_seleccionado = $row_ppa['patppa'];
                        $wcontrol = true;
                        break;

                    }
                }

             }

             if ($wmodificar != '1' and !$wcontrol)
                        {
                        echo "4"; // Este dato genera un mensaje que no se puede combinar.
                        return;
                        }
             }


         }

		//Consulto si el paciente es POS
		$tipo_pos = paciente_tipo_pos ($wtipemp, $wseraso, $wedad_pac);

		//Si el paciente es tipo POS consulto si existe costo correspondiente, especifico por el codigo de la empresa.
		if ($tipo_pos == 'SI')
		{

         if($wmodificar == 1)
            {


                if(count($wcuantos) == 1)
                {

                    $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
                        ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                        ."  WHERE costem  LIKE '%".$wtipemp."%' "
                        ."    AND cosedi <= '".($wedad_pac*12)."'"
                        ."    AND cosedf >=  '".($wedad_pac*12)."'"
                        ."    AND cosest  = 'on' "
                        ."    AND patest  = 'on' "
                        ."    AND cosser  = '".$wseraso."'"
                        ."    AND cospat  = diecod "
                        ."    AND cospat  = patpri "
                        ."    AND patpri  = '".$wchequeados."'"
                        ." GROUP BY cospat "
                        ." ORDER BY cosact DESC";
                    $res_cos_asoc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $num_cos_asoc = mysql_num_rows($res_cos_asoc);

                }
                else
                {

                    $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
                        ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                        ."  WHERE costem  LIKE '%".$wtipemp."%' "
                        ."    AND cosedi <= '".($wedad_pac*12)."'"
                        ."    AND cosedf >=  '".($wedad_pac*12)."'"
                        ."    AND cosest  = 'on' "
                        ."    AND patest  = 'on' "
                        ."    AND cosser  = '".$wseraso."'"
                        ."    AND cospat  = diecod "
                        ."    AND cospat  = patpri "
                        ."    AND patpri  = '".$wchequeados1[0]."'"
                        ."    AND patsec  = '".$wchequeados1[1]."'"
                        ." GROUP BY cospat "
                        ." ORDER BY cosact DESC";

                    $res_cos_asoc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $num_cos_asoc = mysql_num_rows($res_cos_asoc);

                    }

            }
		  else
            {

           $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
                ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                ."  WHERE costem  LIKE '%".$wtipemp."%' "
                ."    AND cosedi <= '".($wedad_pac*12)."'"
                ."    AND cosedf >=  '".($wedad_pac*12)."'"
                ."    AND cosest  = 'on' "
                ."    AND patest  = 'on' "
                ."    AND cosser  = '".$wseraso."'"
                ."    AND cospat  = diecod "
                ."    AND cospat  = patpri "
                ."    AND patpri  = '".$wpatron_seleccionado."'"
                ." GROUP BY cospat "
                ." ORDER BY cosact DESC";

            $res_cos_asoc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num_cos_asoc = mysql_num_rows($res_cos_asoc);

           }

		}
      else
        {


           if(count($wcuantos) > 1)
            {

          $q = " SELECT patppa, patsec "
		      ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
		      ."  WHERE ".$warregloconsulta.""
              ."    AND patpri = '".$wpatron_seleccionado1."'"
		      ."    AND costem  = '*' "
		      ."    AND cosedi <= '".($wedad_pac*12)."'"
		      ."    AND cosedf >=  '".($wedad_pac*12)."'"
		      ."    AND cosest  = 'on' "
              ."    AND patest  = 'on' "
		      ."    AND cosser  = '".$wseraso."'"
		      ."    AND cospat  = diecod "
              ."    AND cospat  = patpri "
			 ."ORDER BY diesec DESC, cosact DESC";
		  $res_ppa = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num_ppa = mysql_num_rows($res_ppa);


          if ($num_ppa > 0)
            {

          $wcontrol = false;
          while($row_ppa = mysql_fetch_array($res_ppa))
			 {

              $wsecundarios = $row_ppa['patsec'];
              $warreglosec = explode(',', $wsecundarios);
             // print_r($warreglosec);
              $wchequeados1 = explode(',', $wchequeados);
             // print_r($wchequeados1);

              $wcuantossec = count($warreglosec);
              $wcuantoschec = count($wchequeados1)-1;   //El menos uno (-1) es para eliminar un elemento del arreglo, ese elemento es una posicion vacia que no es necesaria en el arreglo.

                if($wcuantossec == $wcuantoschec)
                {

                    $wnumero = array();
                    $wnumero = array_diff($wseleccionados, $warreglosec);

                    if (count($wnumero) <= 1)
                    {

                        $wpatron_seleccionado = $row_ppa['patppa'];
                        $wcontrol = true;
                        break;

                    }
                }

             }

             if ($wmodificar != '1' and !$wcontrol)
                        {
                        echo "4"; // Este dato genera un mensaje que no se puede combinar.
                        return;
                        }
             }
//          else
//          {
//              if($wnocobrapatron != 'on' and $wpcomb != 'off' and $num_cos != 1)
//                {
//                echo "4";  //No se puede combinar, ya que no hay patron ppal para la combinacion necesitada.
//                $num_cos_asoc = 0;
//                return $num_cos_asoc;
//                return;
//                }
//          }

         }

         if($wmodificar == 1)
            {
             // Esta validacion se da al modificar un patron donde se combinen 2 patron, quedando solamente uno, se tomara ese para el calculo.
            if(count($wcuantos) == 1)
                {

                    $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
                        ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                        ."  WHERE costem  = '*'"
                        ."    AND cosedi <= '".($wedad_pac*12)."'"
                        ."    AND cosedf >=  '".($wedad_pac*12)."'"
                        ."    AND cosest  = 'on' "
                        ."    AND patest  = 'on' "
                        ."    AND cosser  = '".$wseraso."'"
                        ."    AND cospat  = diecod "
                        ."    AND cospat  = patpri "
                        ."    AND patpri  = '".$wchequeados."'"
                        ." GROUP BY cospat "
                        ." ORDER BY cosact DESC";

                }
                else
                {

                    $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
                        ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                        ."  WHERE costem  = '*' "
                        ."    AND cosedi <= '".($wedad_pac*12)."'"
                        ."    AND cosedf >=  '".($wedad_pac*12)."'"
                        ."    AND cosest  = 'on' "
                        ."    AND patest  = 'on' "
                        ."    AND cosser  = '".$wseraso."'"
                        ."    AND cospat  = diecod "
                        ."    AND cospat  = patpri "
                        ."    AND patpri  = '".$wchequeados1[0]."'"
                        ."    AND patsec  = '".$wchequeados1[1]."'"
                        ." GROUP BY cospat "
                        ." ORDER BY cosact DESC";
                }

            }
		  else
            {

            $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
                ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
                ."  WHERE costem  = '*' "
                ."    AND cosedi <= '".($wedad_pac*12)."'"
                ."    AND cosedf >=  '".($wedad_pac*12)."'"
                ."    AND cosest  = 'on' "
                ."    AND patest  = 'on' "
                ."    AND cosser  = '".$wseraso."'"
                ."    AND cospat  = diecod "
                ."    AND cospat  = patpri "
                ."    AND patpri  = '".$wpatron_seleccionado."'"
                ." GROUP BY cospat "
                ." ORDER BY cosact DESC";

            }

         $res_cos_asoc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num_cos_asoc = mysql_num_rows($res_cos_asoc);

         if ($num_cos_asoc == 0 and $wpcomb != 'off' and $wnocobrapatron != 'on' and count($wchequeados) > 1 )
          {
              echo "10"; //Muestra un mensaje javascript diciendo que no se encontro costo para el patron en la tabla 79 de movhos (funcion js grabar_datos).
              return $num_cos_asoc;
              return;
          }
		 }

        return $num_cos_asoc;
       }
       else
       {
            $num_cos_asoc = 0;
            return $num_cos_asoc;
       }

     }


    //--------------------------------------------------------------------------------------
	//	Cosultar si existe costo para el patron, cuando se produce una insercion automatica.
	//	Actualizacion: 2012-12-28, Jerson Trujillo.
	//--------------------------------------------------------------------------------------
	function traer_costo_del_patron_aut($wpatron_seleccionado, $wtipemp, $wedad_pac, &$res_cos, $wser, &$wcob, &$wsec, &$wcbi, &$wptrcobra, $wmedia_porcion, $wpatron_seleccionado11, $wchequeados, $wautomatico, $wpcomb, $wnocobrapatron, $whis, $wing)
    {
		global $wbasedato;
		global $conex;

     	//Consulto si el paciente es POS

		$tipo_pos = paciente_tipo_pos ($wtipemp, $wser, $wedad_pac);

		//Si el paciente es tipo POS consulto si existe costo correspondiente, especifico por el codigo de la empresa.
		if ($tipo_pos == 'SI')
     	{

			$q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
		      ."    FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
		      ."   WHERE costem  LIKE '%".$wtipemp."%'"
		      ."     AND cosedi <= '".($wedad_pac*12)."'"
		      ."     AND cosedf >=  '".($wedad_pac*12)."'"
		      ."     AND cosest  = 'on' "
		      ."     AND cosser  = '".$wser."'"
		      ."     AND cospat  = diecod "
              ."     AND cospat  = patpri "
              ."     AND patpri  = '".$wpatron_seleccionado."'"
             ." GROUP BY cospat "
			 ." ORDER BY cosact DESC";
		  $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num_cos = mysql_num_rows($res_cos);
        }
		else
		{
			$q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
		      ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
		      ."  WHERE costem  = '*' "
		      ."    AND cosedi <= '".($wedad_pac*12)."'"
		      ."    AND cosedf >=  '".($wedad_pac*12)."'"
		      ."    AND cosest  = 'on' "
		      ."    AND cosser  = '".$wser."'"
		      ."    AND cospat  = diecod "
              ."    AND cospat  = patpri "
              ."    AND patpri  = '".$wpatron_seleccionado."'"
             ." GROUP BY cospat "
			 ." ORDER BY cosact DESC";
			$res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num_cos = mysql_num_rows($res_cos);
		}

		if($wnocobrapatron == 'on')
        {
            $num_cos = 0;
        }

		return $num_cos;
	}
	//--------------------------------
	// Fin traer costo
	//--------------------------------


      //Traigo los costos del servicio asociado, ciando es de forma automatica.
  function traer_cos_servaso_auto($wpatron_seleccionado, $wtipemp, $wedad_pac, &$res_cos_asoc, $wseraso, $wpatron, $wchequeados, $wautomatico, $wpcomb, $wnocobrapatron, $whis, $wing)
     {

          global $wbasedato;
          global $conex;

		//Consulto si el paciente es POS

		$tipo_pos = paciente_tipo_pos ($wtipemp, $wseraso, $wedad_pac);

		//Si el paciente es tipo POS consulto si existe costo correspondiente, especifico por el codigo de la empresa.
		if ($tipo_pos == 'SI')
     	{
          $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
		      ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
		      ."  WHERE costem  LIKE '%".$wtipemp."%'"
		      ."    AND cosedi <= '".($wedad_pac*12)."'"
		      ."    AND cosedf >=  '".($wedad_pac*12)."'"
		      ."    AND cosest  = 'on' "
		      ."    AND cosser  = '".$wseraso."'"
		      ."    AND cospat  = diecod "
              ."    AND cospat  = patpri "
              ."    AND patpri  = '".$wpatron_seleccionado."'"
             ." GROUP BY cospat "
			 ." ORDER BY cosact DESC";
		  $res_cos_asoc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num_cos_asoc = mysql_num_rows($res_cos_asoc);
        }
		else
		{
          $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi, cospat "
		      ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041, ".$wbasedato."_000128 "
		      ."  WHERE costem  = '*' "
		      ."    AND cosedi <= '".($wedad_pac*12)."'"
		      ."    AND cosedf >=  '".($wedad_pac*12)."'"
		      ."    AND cosest  = 'on' "
		      ."    AND cosser  = '".$wseraso."'"
		      ."    AND cospat  = diecod "
              ."    AND cospat  = patpri "
              ."    AND patpri  = '".$wpatron_seleccionado."'"
             ." GROUP BY cospat "
			 ." ORDER BY cosact DESC";
		  $res_cos_asoc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num_cos_asoc = mysql_num_rows($res_cos_asoc);
		}

        if($wnocobrapatron == 'on')
        {
            $num_cos_asoc = 0;
        }
		return $num_cos_asoc;
	}

   //=============================
     //=================================


  //Funcion para grabar que el paciente es POSTQUIRURGICO
  function grabar_posqx($wemp_pmla, $wbasedato, $whis, $wing, $whab, $wser, $westado, $wedad, $wtipemp, $wusuario, $wfec, $wcco, $whora_max_modifi, $hora_max_cancela)
     {

        global $wbasedato;
        global $conex;
        global $wfecha;
        global $whora;
        global $wusuario;

        $wpatronesantes = consultar_patron_actual($whis, $wing, $wser, $wfec);
        $wdatopatrones = explode("-", $wpatronesantes);
        $wpatron_posqx = consulta_patron_posqx($wdatopatrones[3]);

          //Busco si el registro para la his e ing existe en el servicio actual
        $q = " SELECT movpqu "
            ."   FROM ".$wbasedato."_000077 "
            ."  WHERE movfec = '".$wfecha."'"
            ."    AND movhis = '".$whis."'"
            ."    AND moving = '".$wing."'"
            ."    AND movser = '".$wser."'"
            ."    AND movest = 'on' ";
        $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row_val=mysql_fetch_array($res_mov);

        //Evalua si ya selecciono el cajon de posqx, si responde on es porque quiere deseleccionar el cajon y volverlo a estado 1.
        if ($westado == 'off')
        {
			//Consulto si el paciente es POS
			$tipo_pos = paciente_tipo_pos ($wtipemp, $wser, $wedad);

			//Si el paciente es tipo POS consulto si existe costo correspondiente, especifico por el codigo de la empresa.
			if ($tipo_pos == 'SI')
			{
                //Consulto el costo del patron
                $q = " SELECT cosact, cosfec, cosant "
                    ."   FROM ".$wbasedato."_000079 "
                    ."  WHERE costem  LIKE '%".$wtipemp."%'"
                    ."    AND cosedi <= '".($wedad*12)."'"
                    ."    AND cosedf >=  '".($wedad*12)."'"
                    ."    AND cosser  = '".$wser."'"
                    ."    AND cospat = '".$wpatron_posqx."'"
                    ."    AND cosest  = 'on' ";
                $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $num_cos = mysql_num_rows($res_cos);
                $row_cos=mysql_fetch_array($res_cos);

			}
			else
			{

                $q = " SELECT cosact, cosfec, cosant "
                    ."   FROM ".$wbasedato."_000079 "
                    ."  WHERE costem  LIKE '*'"
                    ."    AND cosedi <= '".($wedad*12)."'"
                    ."    AND cosedf >=  '".($wedad*12)."'"
                    ."    AND cosser  = '".$wser."'"
                    ."    AND cospat = '".$wpatron_posqx."'"
                    ."    AND cosest  = 'on' ";
                $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $num_cos = mysql_num_rows($res_cos);
                $row_cos=mysql_fetch_array($res_cos);
            }

               $wvalpat = $row_cos['cosact'];

                if ($wvalpat > 0)
					 {
                        if ($wfecha >= $row_cos['cosfec'])
                        {
                            $wvalpat=$row_cos['cosact'];            //Asigno el valor actual
                        }
                        else
                        {
                            $wvalpat=$row_cos['cosant'];     //Asigno el valor anterior a la fecha de cambio
                        }
                     }

                  $q1 =  " UPDATE ".$wbasedato."_000077 "
                        ."    SET movpqu = '1', movval = '".$wvalpat."', movdie = '".$wpatron_posqx."'"
                        ."  WHERE movfec = '".$wfecha."'"
                        ."    AND movhab = '".$whab."'"
                        ."    AND movhis = '".$whis."'"
                        ."    AND moving = '".$wing."'"
                        ."    AND movser = '".$wser."'"
                        ."    AND movest = 'on'"  ;
                $res = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                //Grabo la auditoria
                $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie , audcco ) "
                     ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."','CANCELADO POSTQUIRURGICO','".$wusuario."','C-".$wusuario."', '".$wdatopatrones[0]."','".$wcco."') ";
                $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

               echo "recargar";

        }
        else
        {
                //Actualizo el registtro para sea postquirurgico
                $q1 =  " UPDATE ".$wbasedato."_000077 "
                        ."    SET movpqu = 'on'"
                        ."  WHERE movfec = '".$wfecha."'"
                        ."    AND movhab = '".$whab."'"
                        ."    AND movhis = '".$whis."'"
                        ."    AND moving = '".$wing."'"
                        ."    AND movser = '".$wser."'"
                        ."    AND movest = 'on'"  ;
                $res = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

           }


     }

  //==================================================================================================================
  //==================================================================================================================


  //==================================================================================================================
  //==================================================================================================================
 //Funcion que permite registrar media porcion para el paciente.
  function grabar_media_porcion($wemp_pmla, $wbasedato, $whis, $wing, $whab, $wser, $westado, $wfec, $wcco, $wusuario)

    {

      global $wbasedato;
	  global $conex;
	  global $wfecha;
	  global $whora;
	  global $wusuario;

      $wfecha=date("Y-m-d");
      $whora =(string)date("H:i:s");

      $wpatronesantes = consultar_patron_actual($whis, $wing, $wser, $wfecha);
      $wdatopatrones = explode("-", $wpatronesantes);

      $whorarioadicional = consultarHorario($wser, $wfec);

        if ($whorarioadicional == 'on')
            {
                $waccion = 'MODIFICO ADICION';
            }
            else
            {
                $waccion = 'MODIFICO PEDIDO';
            }

  	  //Busco si el registro para la his e ing existe en el servicio actual
        $q = " SELECT movdie, movval "
            ."   FROM ".$wbasedato."_000077 "
            ."  WHERE movfec = '".$wfecha."'"
            ."    AND movhis = '".$whis."'"
            ."    AND moving = '".$wing."'"
            ."    AND movser = '".$wser."'"
            ."    AND movest = 'on' ";
        $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num_mov = mysql_num_rows($res_mov);
        $row_val=mysql_fetch_array($res_mov);

        if ($num_mov > 0)
        {
            // Si entra por aqui es porque hay que dividir el valor actual del registro por 2 ya que solo se solicita media porcion
            if ($westado == 'on')
                {

                $q = " UPDATE ".$wbasedato."_000077 "
                        ."    SET movval = '".($row_val[1]/2)."', movmpo = 'on', movcan = '0.5' "
                        ."  WHERE movfec = '".$wfecha."'"
                        ."    AND movhis = '".$whis."'"
                        ."    AND moving = '".$wing."'"
                        ."    AND movser = '".$wser."'"
                        ."    and movest = 'on' ";
                $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

                 //Grabo la auditoria
                $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie, audcco  ) "
                     ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."','".$waccion."','".$wusuario."','C-".$wusuario."', '".$wdatopatrones[0]."', '".$wcco."') ";
                $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());


                }
                else // Si entra por aqui es porque hay que multiplicar el valor actual del registro por 2 ya que solo se solicita toda la porcion
                {



                    $q = " UPDATE ".$wbasedato."_000077 "
                        ."    SET movval = '".($row_val[1]*2)."', movmpo = '', movcan = '1' "
                        ."  WHERE movfec = '".$wfecha."'"
                        ."    AND movhis = '".$whis."'"
                        ."    AND moving = '".$wing."'"
                        ."    AND movser = '".$wser."'"
                        ."    and movest = 'on' ";
                    $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

                    //return;

                    //Grabo la auditoria
                    $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie , audcco   ) "
                        ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."','".$waccion."','".$wusuario."','C-".$wusuario."', '".$wdatopatrones[0]."', '".$wcco."') ";
                    $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());




                }

        }
    }
  //==================================================================================================================
  //==================================================================================================================


  //==================================================================================================================
  //==================================================================================================================

  //Graba un registro en la tabla 85 para controlar el registro de servicios por centro de costos.
  function grabar_encabezado($wser, $wcap, $wobs_enf)
     {

      global $wbasedato;
	  global $conex;
	  global $wfecha;
	  global $whora;
	  global $wusuario;
	  global $wcco;

  	  $q = " SELECT COUNT(*) "
	      ."   FROM ".$wbasedato."_000085 "
	      ."  WHERE encfec = '".$wfecha."'"
	      ."    AND enccco = '".$wcco."'"
	      ."    AND encser = '".$wser."'"
          ."    AND encest = 'on'";
	  $res_enc = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	  $row_enc=mysql_fetch_array($res_enc);

	  if ($row_enc[0] > 0)
	     {
		  $q = " UPDATE ".$wbasedato."_000085 "
		      ."    SET encobe = '".$wobs_enf."'"
		      ."  WHERE encfec = '".$wfecha."'"
		      ."    AND enccco = '".$wcco."'"
		      ."    AND encser = '".$wser."'"
              ."    AND encest = 'on'";
		  $resenc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 }
	    else
	       {
		    //Grabo la dieta de cada historia y por cada servicio
	        $q = " INSERT INTO ".$wbasedato."_000085 (   Medico       ,   Fecha_data,   Hora_data,   encfec    ,   enccco  ,   encser  ,   enccap  ,   encusu      ,  encobe       , encobc, encest, Seguridad        ) "
		        ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wfecha."','".$wcco."','".$wser."','0','".$wusuario."','".$wobs_enf."', ''    ,'on', 'C-".$wusuario."') ";
		    $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	       }
	 }

     //==================================================================================================================
    //Funcion que identifica cual fue el ultimo patron
     function patron_anterior($whis, $wing, $wser)
     {

      global $wbasedato;
	  global $conex;
      global $wfecha;


  	  $q = " SELECT movpam "
	      ."   FROM ".$wbasedato."_000077 "
	      ."  WHERE movhis = '".$whis."'"
          ."    AND moving = '".$wing."'"
          ."    AND movser = '".$wser."'"
          ."    AND movfec = '".$wfecha."'"
         ."GROUP BY movhis, moving";
	  $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	  $row=mysql_fetch_array($res);

      return $row[1];


	 }



  //==================================================================================================================

    function ultimoregposqx($whis, $wing, $wser)
     {

      global $wbasedato;
	  global $conex;
      global $wfecha;


  	  $q = " SELECT MAX(id), Hora_data "
	      ."   FROM ".$wbasedato."_000077 "
	      ."  WHERE movhis = '".$whis."'"
          ."    AND moving = '".$wing."'"
          ."    AND movser = '".$wser."'"
          ."    AND movfec = '".$wfecha."'"
          ."    AND movpqu = '1'"
	      ."    AND movest = 'on'"
         ."GROUP BY movhis, moving";
	  $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	  $row_posqx=mysql_fetch_array($res);

      return $row_posqx[1];


	 }




  //==================================================================================================================
  //==================================================================================================================

 function consultar_estado_posqui($whis, $wing, $wser)
     {

      global $wbasedato;
	  global $conex;
      global $wfecha;


  	  $q = " SELECT movpqu "
	      ."   FROM ".$wbasedato."_000077 "
	      ."  WHERE movhis = '".$whis."'"
          ."    AND moving = '".$wing."'"
          ."    AND movser = '".$wser."'"
          ."    AND movfec = '".$wfecha."'"
	      ."    AND movest = 'on'";
	  $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	  $row=mysql_fetch_array($res);

      return $row[0];


	 }



  //=====================================================================================================================
  //=====================================================================================================================

  //Consulta con patrones se puede combinar un patron.
  function consultar_combinables($wpatron)
     {

      global $wbasedato;
	  global $conex;


  	  $q = " SELECT patsec, patppa "
	      ."   FROM ".$wbasedato."_000128 "
	      ."  WHERE patpri = '".$wpatron."'"
	      ."    AND patest = 'on'";
	  $res_com = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	  $row_com=mysql_fetch_array($res_com);

      return $row_com[0];


	 }

     //==================================================================================================================
  //==================================================================================================================

  //Consulta datos sobre solicitudes de servicio actual.
  function consultar_patron_actual($whis, $wing, $wser, $wfec)
     {

      global $wbasedato;
	  global $conex;

  	  $q = " SELECT movdie, movval, movpam, movpco, movpqu"
	      ."   FROM ".$wbasedato."_000077 "
	      ."  WHERE movhis = '".$whis."'"
	      ."    AND moving = '".$wing."'"
          ."    AND movfec = '".$wfec."'"
          ."    AND movser = '".$wser."'"
          ."    AND movest = 'on'";
	  $res_die = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	  $row_die=mysql_fetch_array($res_die);

      return $row_die['movdie']."-".$row_die['movval']."-".$row_die['movpam']."-".$row_die['movpco']."-".$row_die['movpqu'];


	 }
  //==================================================================================================================
  //==================================================================================================================
  //Funcion que valida si el patron se cobra(NVO).
    function nosecobra($wpatron)
     {

      global $wbasedato;
	  global $conex;


  	  $q = " SELECT dienco "
	      ."   FROM ".$wbasedato."_000041 "
	      ."  WHERE diecod in ('".$wpatron."')"
	      ."    AND dieest = 'on'";
	  $res_nsc = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	  $row_nsc =mysql_fetch_array($res_nsc);

      if($row_nsc[0] == 'on')
      {
          $wnosecobra = 'on';
      }
      else
      {
         $wnosecobra = 'off';
      }

      return $wnosecobra;


	 }


     //==================================================================================================================
  //==================================================================================================================

	//--------------------------------------------------
	// Funcion que consulta si el paciente es tipo POS
	//--------------------------------------------------
	function paciente_tipo_pos ($wtpo, $wser_pac, $wedad_pac )
	{
		global $wbasedato;
		global $conex;
        global $wemp_pmla;

        $wedadlimitepos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'LimiteEdadPedidosPos');

		$q = " SELECT COUNT(*)
				 FROM ".$wbasedato."_000076
				WHERE Sertpo LIKE '%".$wtpo."%'
				  AND Sercod = '".$wser_pac."'
				  AND Serest = 'on' ";
		$restpo = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query --> (Consultar si es pos):".$q." - ".mysql_error());
		$rowtpo=mysql_fetch_array($restpo);

        if ($rowtpo[0] > 0)
            {
			$tipo_pos = "SI";
            //Si el paceinte es tipo POS pero tiene menos de la edad limite, sera tratado como Prepago. // Jonatan 24 Enero de 2013.
            if($wedad_pac <= $wedadlimitepos)
            {
            $tipo_pos = "NO";
            }

            }
		else
            {
			$tipo_pos = "NO";
            }



		return $tipo_pos;
	}


  //==================================================================================================================
  //==================================================================================================================
  //Funcion que evalua si el paciente es de tipo POS, si es asi no guardará merienda.
    function evaluar_emp_paciente($wtemp, $wser)
    {
		global $wbasedato;
		global $conex;

		$q = " SELECT serspp "
			."   FROM ".$wbasedato."_000076 "
			."  WHERE sertpo LIKE ('%".$wtemp."%')"
			."    AND sercod = '".$wser."'"
			."    AND serest = 'on'";
		$res_pos = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		$row_pos =mysql_fetch_array($res_pos);

		if($row_pos[0] == '')
		{
			$row_pos[0] = 'on';
		}
		return $row_pos[0];
	}

	  //==================================================================================================================
	  //==================================================================================================================
	 //==================================================================================================================
//Tiempo de recarga para el area de texto de la mensajeria
function consultarTiempoRecargaMsg( $wemp_pmla ){


    global $conex;

    $val = '5';

	$sql = "SELECT Detval
			  FROM root_000051
			 WHERE detemp = '$wemp_pmla'
			   AND detapl = 'recargaMsgKardex'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$num = mysql_num_rows($res);

	if($num > 0)
        {
        $rows = mysql_fetch_array($res);
		$val = $rows[ 'Detval' ];
        }

	return $val;
}



//Funcion que evalua si el paciente es de tipo POS, si es asi no guardará merienda.
    function mensajedelservicio($wser)
     {

      global $wbasedato;
	  global $conex;
      global $whora;



  	  $q = " SELECT serhin, serhfi, serhia, serhad "
	      ."   FROM ".$wbasedato."_000076 "
	      ."  WHERE sercod = '".$wser."'"
          ."    AND serest = 'on'";
	  $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	  $row =mysql_fetch_array($res);

      $whorarioinicial = $row[0];
      $whorariofinal = $row[1];
      $whorariofinaladicion = $row[3];

      if($whora >= $whorarioinicial)
      {

          if($whora <= $whorariofinal)
          {
          $wmensaje = "<span class='blink'>Se encuentra en horario de Pedido</span>";
          }

          elseif ($whora <= $whorariofinaladicion )
          {
          $wmensaje = "<span class='blink'>Se encuentra en horario de adición</span><br><font size=1> Solo podrá cancelar máximo 3 veces lo que pida en este horario <br> y no podrá pedir si cancela patrones pedidos en horario normal excepto NVO, LC y pacientes nuevos.</font>";
          }
          elseif($whora >= $whorariofinaladicion)
          {
              $wmensaje = "<span class='blink'>Se ha cerrado el servicio</span>";
          }


      }




      return $wmensaje;
	 }

	  //==================================================================================================================
	  //==================================================================================================================
	//Funcion principal que maneja todas las acciones cuando el usuario selecciona un patron, controla las inserciones, adiciones, modificaciones y cancelaciones .
	function procesar_datos($wemp_pmla, $whis, $wing, $wpatron, $wcco, $wser, $wfec, $whab, $wpac, $wdpa, $wtid, $wptr, $wmue, $wedad, $walp, $wtem, $west, $wusuario, $wmodificar, $wchequeados, $wcombinables, $wpcomb, $wmedia_porcion, $wautomatico, $wservgrabado, $wcontrolposqui, $wrol_usuario, $wpatron_nutricion, $wrolnutricion, $wseleccionado, $wconfirmar_canceladsn, $codDSN)
    {

          global $wobserv_enfer;
		  global $wobs;
		  global $wint;
		  global $wbasedato;
		  global $conex;
          global $whabilitado;
          global $westado;
		  global $whce;

		  $wfecha=date("Y-m-d");
		  $whora =(string)date("H:i:s");

          //Funcion que consulta que patrones en la his e ing antes de hacer alguna operacion, para insertarlo en la auditoria.
          $wpatronesantes = consultar_patron_actual($whis, $wing, $wser, $wfec);
          $wdatopatrones = explode("-", $wpatronesantes);

          $wcombinables = consultar_combinables($wpatron).",".$wpatron; // Verifica con cuales patrones se puede combinar el patron seleccionado.
          $whorario_adicional = consultarHorario($wser, $wfec);  //Funcion que verifica si se esta en horario adicional.
          $wchequeados1=explode(",",$wchequeados);
		  $wprog_asociado  = cco_graba_automatico($wcco);
		  $wbusca_costo    = "on";     //Por defecto debe buscar el costo.
		  $wencontro_costo = "on";     //Indica si se le hallo el costo al patron, por derfecto indica que SI (on).


		   //Consulto los servicios asociados al servicio actual y hago la insercion,
		  //aunque primero se verifica si el sistema esta en horario adicional, en tal caso no registra patrones relacionados
		  $q = " SELECT Seraso "
			  ."   FROM ".$wbasedato."_000076"
			  ."  WHERE Sercod = '".$wser."'"
              ."    AND seraso != ''"
              ."    AND seraso != '.'"
              ."    AND seraso != 'NO APLICA'";
		  $resaso = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		  $rowaso=mysql_fetch_array($resaso);
		  $wserv_asociados=explode(",",$rowaso[0]);

          $wpos_seractual = evaluar_emp_paciente($wtem, $wser); // Esta variable identifica si el paciente es POS, en caso de serlo no se guardará el servicio de merienda(inicialmente).
          $wpos_serasociado = evaluar_emp_paciente($wtem, $wserv_asociados[0]); // Esta variable identifica si el paciente es POS y para el servicio asociado, en caso de serlo no se guardará el servicio de merienda(inicialmente).

          if($wserv_asociados[0] == '') // Esta validacion evalua que no se guarden datos si no tiene servicio asociado
                $wserv_asociados = array();

		  $whabilitado = validaciones('', '', '', $wser, "Consulta"); //Consulta si esta en el horario de solicitud de pedido, para guardar el encabezado.

          if($wservgrabado != 'on' and $whabilitado=='Enabled' and $wpos_seractual == 'on') //Verifica si el servicio esta grabado en la tabla  de 77 de movhos y si la hora es valida para grabar encabezado.
            {
            //Graba el encabezado del servicio actual
            grabar_encabezado($wser, $wcap, $wobserv_enfer);
            }

		  //Consulto los datos del srvicio.
		  $q1 = "   SELECT serhin, serhfi, sersaa, serhia, serhad, serhca"
			   ."    FROM ".$wbasedato."_000076 "
			   ."   WHERE serest = 'on' "
			   ."     AND sercod = '".$wser."'";
		  $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $row1 = mysql_fetch_array($res1);
		  $whorafinalser = $row1[1];
		  $westado_saa = $row1[2]; //Estado servicio en adicion. (Controla si se pide servicio en horario adicional))
		  $whora_ini_adicion = $row1[3];
		  $whora_max_adicion = $row1[4];
          $whora_max_cancelacion = $row1[5];

		 //La hora actual no debe ser mayor a la hora final del pedido, si es asi, no se guardara el servicio asociado, ademas el servicio asociado despues de adicion (Sersaa) debe estar en off.
		 if ($whora < $whorafinalser or $westado_saa != 'off')
			{
			//Recorro los servicios asociados para que haga la insercion respectiva del encabezado
			 for ($k=0;$k < count($wserv_asociados);$k++)
				{
                 if($wservgrabado !='on' and $whabilitado=='Enabled' and $wpos_serasociado == 'on')
                    {
                    grabar_encabezado($wserv_asociados[$k], $wcap, '');
                    }
				}
			}

        //Esta validacion aplica para el proceso posqx, cuando el usuario selecciona LC, patron que esta marcado como posqx en la tabla 41 de movhos,
        //se guarda un indicar (1) que permite identificar que el paciente es un posible posqx, si el usuario intenta seleccionar otro patron y no han pasado
        //los 30 minutos y ademas no ha seleccionado que el paciente es posqx entonces se mostrara un mensaje diciendo que no pude combinar porque no tiene marcado
        //el cajon posqx, cuando lo seleccione, puede hacer la combinacion necesaria.
        $wtiempoposqx = consultarAliasPorAplicacion($conex, $wemp_pmla, 'TiempoSolicitudPosqx'); //Consulta los minutos despues de los cuales se puede pedir un patron adicional al posqx.
        $wformatohora = "00:".$wtiempoposqx.":00"; // Formatea la hora para que la tome la funcion.
        $wultimoregposqx = ultimoregposqx($whis,$wing, $wser); // Consulta el ultimo registro que tenga disponible la casilla posqx.
        $whoraactivacionposqx = SumaHoras( $wultimoregposqx, $wformatohora );


          //Se evalua la cantidad de acciones con la accion 'CANCELO PEDIDO EN ADICION'. Este estado se da cuando el paciente
        //viene con un patron definido desde el horario normal y se lo cancelan en horario de adicion.
       $q_pedi = " SELECT audacc, Hora_data "
                ."   FROM ".$wbasedato."_000078 "
                ."  WHERE Fecha_data = '".$wfecha."'"
                ."    AND audhis = '".$whis."'"
                ."    AND auding = '".$wing."'"
                ."    AND audser = '".$wser."'"
                ."    AND audcco = '".$wcco."'"
                ."    AND audacc = 'CANCELO PEDIDO EN ADICION'"
              ." ORDER BY Fecha_data, hora_data desc";
        $res_pedido = mysql_query($q_pedi,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_pedi." - ".mysql_error());
        $num_cancelapa= mysql_num_rows($res_pedido);


        //Se evalua la cantidad de acciones para la his e ing con la palabra 'CANCELO ADICION' .Este estado se da cuando el paciente
        //viene sin un patron definido desde el horario normal y se lo cancelan en horario de adicion.
       $q2 = " SELECT audacc, Hora_data "
            ."   FROM ".$wbasedato."_000078 "
            ."  WHERE Fecha_data = '".$wfecha."'"
            ."    AND Hora_data >= '".$whora_ini_adicion."'"
            ."    AND audhis = '".$whis."'"
            ."    AND auding = '".$wing."'"
            ."    AND audser = '".$wser."'"
            ."    AND audcco = '".$wcco."'"
            ."    AND audacc = 'CANCELO ADICION'"
          ." ORDER BY Fecha_data, hora_data desc";
        $res_mov2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
        $num_cancelaa= mysql_num_rows($res_mov2);


        //Consultar la ultima fecha y hora de solicitud para validar si la solicitud inicio despues de la hora de adicion.
        $q = " SELECT MAX(CONCAT(fecha_data,hora_data)), hora_data "
            ."   FROM ".$wbasedato."_000077 "
            ."  WHERE movfec = '".$wfecha."'"
            ."    AND movhis = '".$whis."'"
            ."    AND moving = '".$wing."'"
            ."    AND movser = '".$wser."'"
            ."    and movest = 'on' "
			." GROUP BY movfec, movhis, moving, movser "	//jerson
			." ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row =mysql_fetch_array($res);


        //La variable modificar evalua si se agrega un patron a lo que viene o si se le quiere quitar un patron a lo que hay, 0 (Cero) agrega, (1) modifica.
		switch($wmodificar)
				{

				case '0':  // Opcion para registrar nuevos patrones

                           //Consultamos los datos de la his, ing y servicio para hoy.
						  $q = " SELECT movdie, movpqu, movval, movpco "
							  ."   FROM ".$wbasedato."_000077 "
							  ."  WHERE movfec = '".$wfecha."'"
							  ."    AND movhis = '".$whis."'"
							  ."    AND moving = '".$wing."'"
							  ."    AND movser = '".$wser."'"
							  ."    and movest = 'on' ";
						  $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						  $row_mov=mysql_fetch_array($res_mov);
                          $num_mov= mysql_num_rows($res_mov);

						  $wpatron1 = $row_mov['movdie'];	//Patrones actuales.
						  $wdatopatron= str_replace(",","','",$wpatron1); // Reemplazamos ',' por (,) en la cadena
						  $wpatron2 = "".$wdatopatron."','".$wpatron.""; //Concatenamos los patrones que se tienen con el nuevo patron seleccionado.
                          $wnocobrapatron = nosecobra($wpatron);  //Esta funcion verifica si el patron se cobra o no.
                          $wpqx = $row_mov['movpqu']; // Control del POSTQUIRURGICO
                          $wcostoactual = $row_mov['movval']; // Costo actual del servicio.
                          $wpatronquecobra = $row_mov['movpco'];	//Patron que se cobra.

                          //Si el usuario no es nutricionista puede mover cualquier patron menos DSN
                          // if($wrol_usuario != $wrolnutricion)
                          // {

                            $wpatroneshising = array(); //Inicializamos la variable como arreglo.

                            if ($num_mov > 0)
                                {
                                $wpatroneshising=explode(",",$row_mov['movdie']); // Patrones explotados por la coma.
                                }


                            //Consultar el ultimo registro para la his e ing en la tabla 77, aun si se encuentra inactivo
                                $q_pqu = " SELECT movdie, movpqu "
                                    ."   FROM ".$wbasedato."_000077 "
                                    ."  WHERE movfec = '".$wfecha."'"
                                    ."    AND movhis = '".$whis."'"
                                    ."    AND moving = '".$wing."'"
                                    ."    AND movser = '".$wser."'";
                                $res_pqu = mysql_query($q_pqu,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_pqu." - ".mysql_error());
                                $row_pqu =mysql_fetch_array($res_pqu);
                                $wdatoposquirur = $row_pqu['movpqu'];

                                if ($whora > $whora_ini_adicion )
                                {
                                    $wdatoposquirur = trim($wdatoposquirur); // Se le quitan los espacios para que pueda entrar en la validacion.

                                    switch (trim($wdatoposquirur)) {
                                        case '1':
                                        case 'fueposqx':

                                                if($num_cancelapa >= 3)
                                                {
                                                    echo "91"; //Alerta javascript que muestra el mensaje "No es posible modificar o solicitar alimentación para el paciente ya que a pasado la hora limite"
                                                    return;
                                                }

                                                if($num_cancelaa >= 3)
                                                {
                                                    echo "91"; //Alerta javascript que muestra el mensaje "No es posible modificar o solicitar alimentación para el paciente ya que a pasado la hora limite"
                                                    return;
                                                }

                                            break;

                                        case '':

                                                $wvalidahorario = validahorariopatron($wpatron); // Esta funcion valida si el patron no valida horario, se aplica especialmente al patron DSN.
                                                //Aqui valida si el ultimo patron a sido inactivo en horario de adicion, si es asi muestra un mensaje que no permite seleccionar mas patrones,
                                                //pero podra entrar al servicio individual.
                                                if($num_cancelapa >= 1)
                                                {

                                                    $wpatron_combinable = valida_combinable($wpatron);
                                                    //Si el patron no es combinable se abre la ventana modal, si es combinable mostrara el mensaje de "Cancelo el pedido en horario de adicion, solo podra solicitar servicios individuales."

                                                    if ($wpatron_combinable != 'on')
                                                        {
                                                            //Verifico si el usuario es nutricionista, si es asi puede ingresar al patron
                                                        if($wrol_usuario == $wrolnutricion)
                                                            {
                                                            //$wvalidahorario Aplica para el patron DSN
                                                                if($wvalidahorario == 'on')
                                                                {
                                                                    echo "7";	 //Devuelve este numero a la funcion javascript grabar_datos y abre la ventana modal.
                                                                    return;
                                                                }

                                                            }
                                                            else
                                                            {
                                                                //$wvalidahorario Aplica para el patron DSN
                                                                if($wvalidahorario == 'on')
                                                                {
																	if($num_cancelapa == 0)
																	{

																		$wdatos_rol_enfermeria = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
																		$winf_nutricion_dsn = explode("-", $wdatos_rol_enfermeria);
																		$wrol_nutricion = $winf_nutricion_dsn[0]; // Rol asociado a las nutricionistas.
																		$wpatron_nutricion = $winf_nutricion_dsn[1]; // Patron asociado a las nutricionistas.
																		$wayer = time()-(1*24*60*60); //Resta un dia
																		$wayer1 = date('Y-m-d', $wayer); //Formatea dia

																		//Consulto si el paciente tiene cancelado el patron DSN para el servicio actual.
																		 $q_aud =    " SELECT audacc, audusu "
																					."   FROM ".$wbasedato."_000078 "
																					."  WHERE audhis = '".$whis."'"
																					."    AND auding = '".$wing."'"
																					."    AND audser = '".$wser."'"
																					."    AND audcco = '".$wcco."'"
																					."    AND Fecha_data BETWEEN '".$wayer1."' AND '".$wfecha."'"
																					."    AND auddie = '".$wpatron_nutricion."'"
																					."    AND audacc = 'CANCELADO'"
																					." ORDER BY Fecha_data DESC";
																		$res_aud = mysql_query($q_aud,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_aud." - ".mysql_error());
																		$row_aud = mysql_fetch_array($res_aud);
																		$num_aud = mysql_num_rows($res_aud);
																		$wusuario_cancela = $row_aud['audusu'];

																		//Consulta el rol del usuario que cancela.
																		$rol_cancelacion = validarenfermera($whce, $wbasedato, $wusuario_cancela);

																		//Si el paciente tiene cancelaciones el dia actual y el rol del que canceló es diferente al de nutricionista.
																		if($num_aud > 0){

																			if($rol_cancelacion != $wrol_nutricion){

																				  //A esta parte se llega cuando una enfermera(o) quiere recuperar la DSN para un paciente, se hace una consulta
																				  //a todos los servicios y se aplica la funcion programar_dsn_enfermeria la cual busca los ultimos productos
																				  //solicitados por servicio y se los registra al paciente, teniendo en cuenta los horarios de inicio y fin de cada
																				  //servicio.


																					  $q =   " SELECT sercod "
																							."   FROM ".$wbasedato."_000076 "
																							."  WHERE serest = 'on'";
																					  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
																					  $num = mysql_num_rows($res);

																					  //Traigo la ultima fecha de solicitud para DSN.
																						$wult_fecha_dsn = consultar_ult_reg_activo($whis, $wing, $wser, $wpatron);

																						while($row = mysql_fetch_array($res)){

																							programar_dsn_enfermeria($wemp_pmla, $wbasedato, $whis, $wing, $row['sercod'], $wfec, $wcco, $wpatron, $wusuario, $whab, $wser, "", $wult_fecha_dsn['ultima_fecha_dsn'], $wult_fecha_dsn['ultima_hora_dsn'], $wult_fecha_dsn['ultimo_consecutivo_dsn']);

																						}

																						echo "*nohaydsn";

																			}else{

																			//Nombre del usuario
																			$q_usuario = " SELECT descripcion "
																						."   FROM usuarios "
																						."  WHERE codigo = '".$wusuario_cancela."'";
																			$res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuario." - ".mysql_error());
																			$row_usuario = mysql_fetch_array($res_usuario);
																			$wnombre = $row_usuario['descripcion'];

																			$wrespuesta = "no_puede_pedir_dsn";
																			$wmensaje = "Este patron ha sido cancelado por la nutricionista ".$wnombre.", \nno es posible solicitarlo, favor comunicarse con la nutricionista encargada.";

																			echo $wrespuesta."|".$wmensaje;

																			}
																		}else{

																		  //A esta parte se llega cuando una enfermera(o) quiere recuperar la DSN para un paciente, se hace una consulta
																		  //a todos los servicios y se aplica la funcion programar_dsn_enfermeria la cual busca los ultimos productos
																		  //solicitados por servicio y se los registra al paciente, teniendo en cuenta los horarios de inicio y fin de cada
																		  //servicio.

																			  $q =   " SELECT sercod "
																					."   FROM ".$wbasedato."_000076 "
																					."  WHERE serest = 'on'";
																			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
																			  $num = mysql_num_rows($res);
																			  //Traigo la ultima fecha de solicitud para DSN.
																			  $wult_fecha_dsn = consultar_ult_reg_activo($whis, $wing, $wser, $wpatron);
																				while($row = mysql_fetch_array($res)){

																					programar_dsn_enfermeria($wemp_pmla, $wbasedato, $whis, $wing, $row['sercod'], $wfec, $wcco, $wpatron, $wusuario, $whab, $wser, "", $wult_fecha_dsn['ultima_fecha_dsn'], $wult_fecha_dsn['ultima_hora_dsn'], $wult_fecha_dsn['ultimo_consecutivo_dsn']);

																				}

																				echo "*nohaydsn";

																		}

																		return;
																	}
																	else
																	{
																		echo "92"; //Alerta javascript que muestra el mensaje "Cancelo el pedido en horario de adicion."
																		return false;
																	}
                                                                }
                                                                else
                                                                {
                                                                    echo "7";	 //Devuelve este numero a la funcion javascript grabar_datos y abre la ventana modal.
                                                                    return;
                                                                }
                                                            }
                                                        }
                                                        else
                                                            {
                                                                echo "92"; //Alerta javascript que muestra el mensaje "Cancelo el pedido en horario de adicion."
                                                                return;
                                                            }
                                                }
                                                else
                                                    if($wpatroneshising == '' or $wpatroneshising == ' ')
                                                    {
                                                        return false; //Este caso se da cuando al paciente se le agrega una observacion y se guarda en el servicio ppal y el asociado,
                                                                //pero no se guarda dieta para el paciente, entonces detengo la funcion y permito el registro del patron vacio.
                                                    }

                                                break;


                                        default:
                                            break;
                                    }
                                }
                            //}
                            //Si es nutricionista pero el patron que selecciono es diferente de DSN no lo deja seleccionar, sin es DSN se abre la modal.
                            // else
                                // {

                                 // if ($wpatron != $wpatron_nutricion)
                                 // {
                                    // echo "seleccionapatronnodsn_new";	 //Devuelve esta palabra a la funcion js grabar_datos y mostrara un mensaje diciendo que no puede seleccionar este patron , porque no tiene permisos.
                                    // return;
                                 // }
                                 // else
                                 // {
                                     // if (trim($wpatron1) != '')
                                        // {

                                         // //Este valor indica que el patron no se puede combinar con otro.
                                            // echo "alerta_nutricionista";
                                            // return;

                                        // }
                                        // else
                                        // {
                                           // //Abre la ventana modal para DSN
                                            // echo "7";
                                            // return;

                                        // }

                                 // }
                            // }

				break;


				case '1':   //Valida si se va a modificar la solicitud.
							//Se consultan los patrones anteriores por fecha, his, ing y servicio.
						  $q = " SELECT movdie, movpqu, movval, movpco "
							  ."   FROM ".$wbasedato."_000077 "
							  ."  WHERE movfec = '".$wfecha."'"
							  ."    AND movhis = '".$whis."'"
							  ."    AND moving = '".$wing."'"
							  ."    AND movser = '".$wser."'"
							  ."    and movest = 'on' ";
						  $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						  $row_mov=mysql_fetch_array($res_mov);

						  //En este caso reemplazamos el valor de patron que llega y lo eliminamos del arreglo de patrones.
						  $wpatron1 = $row_mov['movdie'];		// Patron o patrones actuales.
						  $wdatopatron_modifi= str_replace($wpatron,"",$wpatron1); //Se le quita al grupo de patrones el seleccionado.
						  $wdatopatron = str_replace(",","','",$wdatopatron_modifi); // se reeemplaza (,) por ','
						  $wpatron2 = $wdatopatron;  //Nuevo grupo de patrones para la his e ing.
                          $wnocobrapatron = nosecobra($wpatron1);  //Esta funcion verifica si el patron se cobra o no.
                          $wpqx = $row_mov['movpqu']; // Control del POSTQUIRURGICO
                          $wcostoactual = $row_mov['movval']; // Costo actual del servicio.
                          $wpatroneshising=explode(",",$row_mov['movdie']); // Patrones actuales explotados por la coma.
                          $wpatronquecobra = $row_mov['movpco'];	//Patron que se cobra.

						  $wrol_usuario = validarenfermera($whce, $wbasedato, $wusuario); //Rol del usuario actual
						  $wdatos_rol_enfermeria = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
						  $winf_nutricion_dsn = explode("-", $wdatos_rol_enfermeria);
						  $wrol_nutricion = $winf_nutricion_dsn[0]; // Rol asociado a las nutricionistas.
						  $wpatron_nutricion = $winf_nutricion_dsn[1]; // Patron asociado a las nutricionistas.

                          //Si el usuario no es nutricionista puede mover cualquier patron menos DSN.
                          // if($wrol_usuario != $wrolnutricion)
                          // {
                           $wvalidahorario = validahorariopatron($wpatron); // Esta funcion valida si el patron no valida horario, se aplica especialmente al patron DSN.
                           //Si va a modificar el pedido pero ingresa a un patron no combinable entonces abre la modal.
                           if($wpcomb != 'on' and $wvalidahorario != 'on')
                            {
                                echo "7"; // Devuelve este numero como referencia para que la funcion js grabar_datos, abra una ventana modal con los productos.
                            }

                            //Aqui se verifica si es posible cancelar un pedido o modificarlo.
                            if ($whora > $whora_max_cancelacion and $wpcomb != 'off')
                            {
                                echo "5"; //Mensaje javascript que muestra el mensaje "No es posible cancelar el servicio"
                                return;
                            }
                            elseif($whora > $whora_ini_adicion)
                            {
                                if ($row['hora_data'] < $whora_ini_adicion and $wpqx == '' or $wpqx == ' ')
                                {
									if($wrol_usuario != $wrol_nutricion){ //La cancelacion de la DSN no se valida con estaa alerta y no se le debe mostrar a las nutricionistas.

										echo "9"; //Alerta javascript que muestra el mensaje "No es posible solicitar alimentación para el paciente ya a pasado el limite de modificación de pedido"
										return;
									}
                                }

                            }


                          // }
                          // //Si es nutricionista pero quiere seleccionar un patron diferente de DSN no se lo permite, si es DSN se abre la modal.
                          // else
                          // {

                                // if ($wpatron != $wpatron_nutricion)
                                 // {
                                    // echo "seleccionapatronnodsn_update";	 //Devuelve esta palabra a la funcion js grabar_datos y mostrara un mensaje diciendo que no puede seleccionar este patron , porque no tiene permisos.
                                    // return;
                                 // }
                                 // else
                                 // {
                                     // if (trim($wpatron1) != '' and $wpatron != $wpatron_nutricion )
                                        // {

                                         // //Este valor indica que el patron no se puede combinar con otro.
                                            // echo "4";
                                            // return;

                                        // }
                                        // else
                                        // {
                                           // //Abre la ventana modal para DSN
                                            // echo "7";
                                            // return;

                                        // }

                                 // }

                          // }


				break;

				case '2': //Identificar que se debe activar cuando se cargan los servicios del dia anterior en la funcion mostrar()
						  $waux = implode(',',$wpatron);
						  $wpatron2 = str_replace(",","','",$waux);
                          $wnocobrapatron = nosecobra($wpatron2);  //Esta funcion verifica si el patron se cobra o no.

				break;
			}


            //Esta validacion se cuando quieren asociar un patron que es Posquirurgico.
            switch ($wpqx) {
                case '1':
                            if($whora >= $whoraactivacionposqx and $wpcomb != 'off' and $wpatron != $wpatroneshising[0])
                            {
                              echo "30"; //Este dato genera un respuesta javascript con un mensaje diciendo "No es posible hacer la combinación ya que no ha seleccionado el paciente como posquirúrgico"
                              return;
                            }

                break;

                case 'on':

                           //En esta parte del codigo se elimina del grupo de patrones el que se cobra, siendo el que se cobra un patron posqx.
                            $wdatopatron= str_replace("','",",",$wpatron2); // Reemplazamos ',' por (,) en la cadena
                            $wdatopatron1 = explode(",", $wdatopatron); //Separamos la combinacion por la coma.
                            $clave = array_search($wpatronquecobra, $wdatopatron1); //Buscamos el patron que se cobra en el arreglo.
                            unset($wdatopatron1[$clave]);  //Eliminamos el patron que se cobra del arreglo, para que los que queden sean analizados por la funcion de traer_costo_del_patron.
                            $wpatron2 = implode(",", $wdatopatron1);  //Unimos los elementos del arreglo por la coma.


                            // En esta parte del codigo se elimina de los chequeados el patron que se cobra, siendo posqx.
                            $wchequeados_aux=explode(",",$wchequeados);
                            $clave1 = array_search($wpatronquecobra, $wchequeados_aux); //Buscamos el patron que se cobra el el arreglo.
                            unset($wchequeados_aux[$clave1]);  //Eliminamos el patron que se cobra del arreglo.
                            $wchequeados = implode(",", $wchequeados_aux);

                            //Este modificar igual a uno significa que el usuario quiere quitarle un patron a un paciente que
                            //tiene seleccionado el cajon de postquirurgico.
                            if($wmodificar == 1 and $wpcomb != 'off')
                            {
                              echo "31"; //Este dato genera un respuesta javascript con un mensaje diciendo "No es posible deseleccionar el patron ya que no ha deseleccionado el paciente como posquirúrgico."
                              return;
                            }

                break;

                default:

                break;
            }


		  //                              Patron,  Tipo Emp, Edad, Query,     Servicio, Cobro Obligado, Solo se cobra este, Se puede combinar , Patron que se cobra, media porcion, patron seleccionado, patrones selccionados
		 $num_cos=traer_costo_del_patron($wpatron2, $wtem, $wedad, $res_cos, $wser  , $wcob  , $wsec  , $wcbi  ,$wptrcobra, $wmedia_porcion, $wpatron, $wchequeados, $wautomatico, $wpcomb, $wnocobrapatron, $whis, $wing, $wmodificar);

          if ($wserv_asociados[0] != '')
                {

                $num_cos_asociado=traer_costo_del_patron_asociado($wpatron2, $wtem, $wedad, $res_cos_asoc, $wserv_asociados[0], $wpatron, $wchequeados, $wautomatico, $wpcomb, $wnocobrapatron, $whis, $wing, $wmodificar, $num_cos); //Funcion para registrar el precion del servicio asociado

                }
                else
                {
                    $res_cos_asoc = array();
                    $num_cos_asociado = 0;
                }


                 //En este switch evaluo inicialmente si el patron se cobra o no, de acuerdo a la funcion nosecobra($wpatron2), la cual recibe el patron que se ha seleccionado
                //en caso de ser igual a 'on' entonces verifica si lo que quiere hacer el usuario es agregar un nuevo patron, lo q{cual es evaluado por la variable $wmodificar
                //si esa variable $wmodificar es igual a cero (osea que quiere agregar un nuevo patron), evaluo si la cantidad de chequeados es igual a uno, en este caso
                //la variable $num_cos tendra un valor mayor a cero para que se pueda registrar, si la cantidad de chequeados es mayor a cero y el paciente tiene activo el cajon de
                //postquirurgico le asigno a $num_cos un valor mayor a cero para que permita continuar, si no tiene seleccionado el postqx y quiere seleccionar otro patron
                //no permitira continuar y mostrara un mensaje diciendo que no puede combinar los patrones.
                  switch ($wnocobrapatron) {
                      case 'on':

                          switch ($wmodificar) {
                              case '0':
                                        if(count($wchequeados1) == 1)
                                        {
                                            $num_cos=1;
                                        }

                                        if(count($wchequeados1) > 1 and $wpqx != 'on')
                                        {
                                            $num_cos = 0;
                                            echo "4"; //Este valor indica que el patron no se puede combinar con otro.
                                            return;
                                        }

                                        if(count($wchequeados1) > 1 and $wpqx == 'on')
                                        {
                                            $num_cos = 1;
                                        }


                               break;


                              default:
                                  break;
                          }

                      break;


                      default:
                          break;
                  }


          //Si encuentra un registro para el costo del patron ingresa al procedimiento.
          if ($num_cos > 0)
			 {

              $row_cos = @mysql_fetch_array($res_cos);

              $wvalpat= $row_cos[0];

              if ($wserv_asociados[0] == '' and $num_cos_asociado == 0) // Esto quiere decir que no hay servicio asociado.
              {
                $row_cos_asociado = array();
              }
              else
              {
                     $wbusca_costo = 'on';
                     $row_cos_asociado = mysql_fetch_array($res_cos_asoc);
              }

			  $wcbi=$row_cos[5];                      //Indica si es combinable o no con otros patrones en horario de Pedido normal.
			  $wptrcobra=$row_cos[6];                 //Indica el patron que se cobro.

              //Esta reasignacion de variable se da ya que el patron que se cobra en el caso del posqx siempre es con el que inicio la solicitud, esto quiere
              //decir que si al paciente le seleccionaron LC o NVO y luego el cajon de postquirurgico entonces uno de esos patrones sera siempre el que se
              //cobra, en los otros casos el patron que se cobra esta definido en la tabla de combinacion (movhos_000128)
                if ($wpqx == 'on')
                {
                    $wptrcobra = $wpatronquecobra;
                }

              $wnocobrapatron = nosecobra($wpatron2); //Esta funcion esta hecha especialmente para el patron NVO el cual no se cobra.

             //esta validacion se refiere a los patrone sque no se cobran como el NVO y el DSN, como estos patrones necesitan pasar
              //las validaciones entonces se le asigna informacion a algunas variables par que pase las validaciones.
            if ($wnocobrapatron == 'on')
            {
                $wvalpat = '0';
                $wcob = 'on';
                $num_cos_asociado = 1;
                $wcbi = 'on';
                $wbusca_costo = 'off';
                $wptrcobra= $wpatron;
            }

                //Pregunto si debe buscar el costo
			  if ($wbusca_costo=="on")
				 {

                  //En esta parte se valida si el patron seleccionado se cobra adicional o no. (LOA)
                  $wcostoptradic = 0;
                  $wcobraadicional = verifica_cobro_adicional($wcco, $wpatron);

                 //Esta validacion es importante ya que determina si el patron seleciconado tiene costo, en caso de estar conbinado o individual.
				  if ($wvalpat > 0)
					 {
                      //Esta validacion se da para determinar el costo del patron en el dia de hoy o de mañana, ya que puede cambiar para dias posteriores.
					  if ($wfecha >= $row_cos[1])
                      {


                          switch ($wpqx) {
                              case 'on':
                                         //Esta funcion consulta el precio del patron en caso de ser cobrado adicional, sea postquirurgico.
                                        $wcostoptradic = costo_adicional_patron($wser, $wtem, $wedad, $wpatron);  //Costo del patron que se cobrara como adicional.

                              break;

                              case '':
                                         //En este caso se consulta si el patron que han seleccionado se cobra adicional, esto aplica para el patron LOA, el cual se cobra adicional
                                         //para a un centro de costos especifico, por lo tanto el patron que se cobra no es el de la consulta a la tabla 77 de movhos del campo movpco, sino
                                         //el que el usuario a seleccionado.
                                         if ($wcobraadicional == 'on')
                                            {
                                                $wpatronquecobra = $wpatron;
                                                //Esta funcion consulta el precio del patron en caso de ser cobrado adicional, sea postquirurgico.
                                                $wcostoptradic = costo_adicional_patron($wser, $wtem, $wedad, $wpatronquecobra);  //Costo del patron que se cobrara como adicional.


                                            }

                                  break;

                              default:
                                  break;
                          }



                          if ($wmedia_porcion != '1')           //Indica que se cobrara toda la porcion
                            {

                                $wvalpat = $row_cos[0];            //Asigno el valor actual

                                //Cuando el patron es LOA, el cual se cobra adicional entonces $wvalpat es igual al valor que tenia el registro del paciente mas el costo del LOA.
                                if ($wcobraadicional == 'on')
                                    {
                                    $wvalpat = $wcostoactual + $wcostoptradic;
                                    }

                                //Si el paciente tiene activa la casilla de posquirurgico entonces toma el costo que tenia el registro y le suma el nuevo costo del
                                //patron o patrones que seleccione.
                                if ($wpqx == 'on')
                                    {
                                    $wvalpat = $wcostoactual + $row_cos[0];
                                    }


                                //Dato para el servicio asociado
                                $wvalpat_asoc = $row_cos_asociado[0];            //Asigno el valor actual del serv aso.
                            }
                            else                                  // Indica que se cobrara media porcion
                            {
                                $wvalpat=($row_cos[0]/2);            //Asigno el valor actual
                                $wvalpat_asoc=($row_cos_asociado[0]/2);            //Asigno el valor actual
                            }


                      }
                      else
                            {
                                if ($wmedia_porcion != '1')         //Indica que se cobrara toda la porcion
                                {

                                    $wvalpat = $row_cos[0];            //Asigno el valor actual

                                    //Cuando el patron es LOA, el cual se cobra adicional entonces $wvalpat es igual al valor que tenia el registro del paciente mas el costo del LOA.
                                    if ($wcobraadicional == 'on')
                                        {
                                        $wvalpat = $wcostoactual + $wcostoptradic;
                                        }

                                    //Si el paciente tiene activa la casilla de posquirurgico entonces toma el costo que tenia el registro y le suma el nuevo costo del
                                    //patron o patrones que seleccione.
                                    if ($wpqx == 'on')
                                        {
                                        $wvalpat = $wcostoactual + $row_cos[0];
                                        }

                                    $wvalpat_asoc=$row_cos_asociado[2];            //Asigno el valor actual del serv aso.

                                }
                                else                                // Indica que se cobrara media porcion
                                {
                                    $wvalpat=($row_cos[2]/2);
                                    $wvalpat_asoc=($row_cos_asociado[2]/2);            //Asigno el valor actual del serv aso.
                                }

                            }
					 }
				 }


               if ($wmodificar == '') // Esto permite hacer un cambio en el parametro del numero de costos asociados ya que genera un mensaje de que no se encontro costo.
               {
                   if ($num_cos_asociado == 0)
                   {
                    $wnoregistraasociado = 'on'; //Variable que permite identificar si se registran los servicios del horario asociado en caso de tener costo en cero.
                    $num_cos_asociado = 1;
                   }
                   else
                   {
                    $wnoregistraasociado = 'off';
                    $num_cos_asociado = 1;
                   }

               }


			  //===================================================================================================================================
			 }
			else
			   {

				if ($wpcomb != "off")  //Indica que si es combinable con otro patron pero que no encontro costo
				   {
					if ($wpatron2 == "") //Variable final al realizar este proceso = ($wdatopatron = str_replace(",","','",$wdatopatron_modifi); // se reeemplaza (,) por ',')
						{

                        $waccion = 'CANCELADO';
						$wencontro_costo="off";
						}
						else
						{
                            if ($wmodificar != 1)
                            {

                                   echo "1"; //Este 1 devuelve un mensaje diciendo lo siguiente: "El Patrón: **xx** No tiene costo en la tabla 000079, para el tipo de empresa: ** xx ** en la historia: ** xxxxxx **", en la funcion javascript grabar_datos.
                                   return;
                            }
                            else
                                {
                                    $wencontro_costo="on";
                                    return;
                                }
                            }
				   }
				  else  //Indica que el patron no es combinable
					 {

                      // Dejo solo los seleccionados excepto el actual.
                      $wchequeados1 = str_replace($wpatron,'',$wchequeados);

                        //Se quita la coma al final
                      $wchequeados = substr ($wchequeados1, 0, strlen($wchequeados1) - 1);

                      $wpatron_combinable = valida_combinable($wpatron); // Verifica si el patron es combinable (SI, DSN, TMO)
                      $wvalidahorario = validahorariopatron($wpatron); // Esta funcion valida si el patron no valida horario, se aplica especialmente al patron DSN.

                          //Si no hay ningun patron seleccionado ingresa a la ventana modal del patron (SI, DSN, TMO)
	                      if (trim($wchequeados) == '')
	                      {
                              //Se valida si el usuario que ingresa es un(a) nutricionista, en caso de ser asi se abre la modal.
                              if($wrol_usuario == $wrolnutricion)
                              {
                                  //$wvalidahorario Aplica para el patron DSN
                                  if($wvalidahorario == 'on')
                                  {
                                      echo "7";	 //Devuelve este numero a la funcion javascript grabar_datos y abre la ventana modal.

                                  }else{

									  echo "seleccionarSI"; //Si el usuario tiene rol de nutricionista no podra ingresar al Servicio individual
								  }
                              }
                              else
                                  {
                                  //Si el usuario no es nutricionista pero quiere cancelar el servicio DSN, lo puede hacer.
                                  if($wvalidahorario == 'on')
                                  {
                                      //Si el patron DSN se quiere desactivar siendo un usuario que no nutricionista ingresa aqui.
                                      if ($wseleccionado == 'off')
                                        {
                                          //Al devolver esta palabra 'cancelardsn' a la funcion javascript grabar_datos mostrara un mensaje de confirmacion para cancelar el patron DSN, cuando el usuario no es nutricionista,
                                          //en caso de aceptar, la variable $wconfirmar_canceladsn se mantendra en off, si acepta, la variable $wconfirmar_canceladsn sera igual a on y cancelara el servicio actual y el asociado.
										  //ademas si la hora actual es mayor a la hora maxima de cancelacion no permitira cancelar el patron para el paciente.
										  if ($whora > $whora_max_cancelacion)
											{
												echo "5"; //Mensaje javascript que muestra el mensaje "No es posible cancelar el servicio, porque ya fue enviado."
												return;
											}else{

											 echo "cancelardsn";

											}
                                        }
                                        //Si lo quiere activar de nuevo entonces traera los ultimos productos programados para el paciente en el servicio astual y el asociado.
                                        else
                                        {
										if($num_cancelapa > 0)
                                            {
												echo "92"; //Alerta javascript que muestra el mensaje "Cancelo el pedido en horario de adicion."
												return false;
											}
											else
											{

												$wdatos_rol_enfermeria = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
												$winf_nutricion_dsn = explode("-", $wdatos_rol_enfermeria);
												$wrol_nutricion = $winf_nutricion_dsn[0]; // Rol asociado a las nutricionistas.
												$wpatron_nutricion = $winf_nutricion_dsn[1]; // Patron asociado a las nutricionistas.
												$wayer = time()-(1*24*60*60); //Resta un dia
												$wayer1 = date('Y-m-d', $wayer); //Formatea dia

												//Consulto si el paciente tiene cancelado el patron DSN para el servicio actual.
												 $q_aud =    " SELECT audacc, audusu "
															."   FROM ".$wbasedato."_000078 "
															."  WHERE audhis = '".$whis."'"
															."    AND auding = '".$wing."'"
															."    AND audser = '".$wser."'"
															."    AND audcco = '".$wcco."'"
															."    AND Fecha_data BETWEEN '".$wayer1."' AND '".$wfecha."'"
															."    AND auddie = '".$wpatron_nutricion."'"
															."    AND audacc = 'CANCELADO'"
															." ORDER BY Fecha_data DESC";
												$res_aud = mysql_query($q_aud,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_aud." - ".mysql_error());
												$row_aud = mysql_fetch_array($res_aud);
												$num_aud = mysql_num_rows($res_aud);
												$wusuario_cancela = $row_aud['audusu'];

												$rol_cancelacion = validarenfermera($whce, $wbasedato, $wusuario_cancela);

												//Si el paciente tiene cancelaciones el dia actual y el rol del que canceló es diferente al de nutricionista .
												if($num_aud > 0){

													if($rol_cancelacion != $wrol_nutricion){

														  //A esta parte se llega cuando una enfermera(o) quiere recuperar la DSN para un paciente, se hace una consulta
														  //a todos los servicios y se aplica la funcion programar_dsn_enfermeria la cual busca los ultimos productos
														  //solicitados por servicio y se los registra al paciente, teniendo en cuenta los horarios de inicio y fin de cada
														  //servicio.

															  $q =   " SELECT sercod "
																	."   FROM ".$wbasedato."_000076 "
																	."  WHERE serest = 'on'";
															  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
															  $num = mysql_num_rows($res);
															  //Traigo la ultima fecha de solicitud para DSN.
															  $wult_fecha_dsn = consultar_ult_reg_activo($whis, $wing, $wser, $wpatron);
																while($row = mysql_fetch_array($res)){

																	programar_dsn_enfermeria($wemp_pmla, $wbasedato, $whis, $wing, $row['sercod'], $wfec, $wcco, $wpatron, $wusuario, $whab, $wser, "", $wult_fecha_dsn['ultima_fecha_dsn'], $wult_fecha_dsn['ultima_hora_dsn'], $wult_fecha_dsn['ultimo_consecutivo_dsn']);

																}

																echo "*nohaydsn";

													}else{

													//Nombre del usuario
													$q_usuario = " SELECT descripcion "
																."   FROM usuarios "
																."  WHERE codigo = '".$wusuario_cancela."'";
													$res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuario." - ".mysql_error());
													$row_usuario = mysql_fetch_array($res_usuario);
													$wnombre = $row_usuario['descripcion'];

													$wrespuesta = "no_puede_pedir_dsn";
													$wmensaje = "Este patron ha sido cancelado por la nutricionista ".$wnombre.", \nno es posible solicitarlo, favor comunicarse con la nutricionista.";

													echo $wrespuesta."|".$wmensaje;

													}

												}else{

													  //A esta parte se llega cuando una enfermera(o) quiere recuperar la DSN para un paciente, se hace una consulta
													  //a todos los servicios y se aplica la funcion programar_dsn_enfermeria la cual busca los ultimos productos
													  //solicitados por servicio y se los registra al paciente, teniendo en cuenta los horarios de inicio y fin de cada
													  //servicio.

														  $q =   " SELECT sercod "
																."   FROM ".$wbasedato."_000076 "
																."  WHERE serest = 'on'";
														  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
														  $num = mysql_num_rows($res);
														  //Traigo la ultima fecha de solicitud para DSN.
														  $wult_fecha_dsn = consultar_ult_reg_activo($whis, $wing, $wser, $wpatron);
															while($row = mysql_fetch_array($res)){


																programar_dsn_enfermeria($wemp_pmla, $wbasedato, $whis, $wing, $row['sercod'], $wfec, $wcco, $wpatron, $wusuario, $whab, $wser, "", $wult_fecha_dsn['ultima_fecha_dsn'], $wult_fecha_dsn['ultima_hora_dsn'], $wult_fecha_dsn['ultimo_consecutivo_dsn']);


															}

															echo "*nohaydsn";

												}

											return false;

											}
                                        }
                                  }
                                  //Si el patron no es DSN se abre la modal
                                  else
                                      {

                                     echo "7";	 //Devuelve este numero a la funcion javascript grabar_datos y abre la ventana modal.
                                      }
                                  }

	                      }
                          //Si hay patrones chequeados, se hacen otras validaciones como el horario adicional, los posquirurgicos y si se valida horario o no.
	                      else
	                      {
                              //Si el paciente tiene un patron LC o NVO, se le abrira la ventana modal, ademas si se esta en horario de adicion.
	                          if ($whorario_adicional == 'on' and $wvalidahorario != 'on' and $wpqx == '1')
	                          {
                                  // Verifica si el patron es combinable (SI, DSN, TMO)
	                              if ( $wpatron_combinable == 'off')
	                              {
	                                echo "7"; // Abre la ventana modal ingresando a la funcion js grabar_datos opcion 7.
	                              }
	                              else
	                              {
									  if($wrol_usuario == $wrolnutricion)
										 {
										//Este valor indica que el patron no se puede combinar con otro.
										 echo "alerta_nutricionista_adicion";
										 return;

                                        }else{

											echo "2"; //Muestra un mensaje javascript diciendo que no se puede combinar, funcion javascript grabar_datos.
										}
	                              }
	                          }
	                          else
	                          {
                                  // Esta validacion se da especialmente para el patron DSN, el cual no valida horario y no se puede combinar.
                                  if ($wvalidahorario != 'on' and $wpatron1 != $wpatron_nutricion)
                                    {
										if($wrol_usuario == $wrolnutricion)
										  {
											 echo "seleccionarSI"; //Si el usuario tiene rol de nutricionista no podra ingresar al Servicio individual

										  }else{
											  echo "7";  // Abre la ventana modal ingresando a la funcion js grabar_datos opcion 7.
										  }
                                    }
                                    else
                                    {
										if($wrol_usuario == $wrolnutricion)
										 {
											 //Si esta en horario normal le avisa a la nutricionista que se cancelara el patron que tiene el paciente diferente a DSN 24 Julio 2018 Jonatan
											if($whorario_adicional == 'on'){

												 echo "alerta_nutricionista_adicion";
												 return;

											}else{
												//Este valor indica que el patron no se puede combinar con otro.
												 echo "alerta_nutricionista";
												 return;
											}

                                        }else{

											echo "2"; //Muestra un mensaje javascript diciendo que no se puede combinar, funcion javascript grabar_datos.
										}

                                    }
	                          }
                              //Aqui se detiene la funcion ya que esta abierta la ventana modal y en esa se hacen otros procedimientos.
	                          return;
	                      }

					 }
			   }

		  //Busco si la historia ya tenia el servicio grabado y de acuerdo a la dieta encontrada o no determino si la accion
		  //es PEDIDO, MODIFICACION, ADICION o CANCELACION
		  if ($wencontro_costo=="on")
			 {

			  $waccion = accion_a_grabar($whis, $wing, $whab, $wser, $wpatron, $westado, trim($wobs), trim($wint), $wmodificar);
              //Verifica si el paciente tenia marcado un patron posquirurgico.
              if ($wpqx == 'on')
                {
                  //Verifica si se esta en horario adicional
                  $whorarioadicional = consultarHorario($wser, $wfec);

                  if ($whorarioadicional == 'on')
                  {
                   $waccion = "ADICION POSTQUIRURGICO";
                  }
                  else
                  {
                    if(count($wpatroneshising)>1)
                    {
                     $waccion = "MODIFICO POSTQUIRURGICO";
                    }
                    else
                    {
                    $waccion = "PEDIDO POSTQUIRURGICO";
                    }
                  }

                }
			 }
			else
				{
                    //Busco si se esta dentro del rango de cancelacion
		           $q = " SELECT COUNT(*) "
			           ."   FROM ".$wbasedato."_000076 "
			           ."  WHERE sercod = '".$wser."'"
			           ."    AND serhin <= '".$whora."'"
			           ."    AND serhca >= '".$whora."'"
			           ."    AND seradi  = 'on' ";
		           $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			       $row = mysql_fetch_array($res);

			       if ($row[0] > 0)
			          {
				       //Busco si el servicio esta ACTIVO, para no hacer registros de cancelacion innecesarios
				       $q = " SELECT COUNT(*) "
				           ."   FROM ".$wbasedato."_000077 "
				           ."  WHERE movhis = '".$whis."'"
				           ."    AND moving = '".$wing."'"
				           ."    AND movser = '".$wser."'"
				           ."    AND movest = 'on' ";
				       $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			           $row = mysql_fetch_array($res);

                       if ($row[0] > 0)
			              {
                           $waccion = 'CANCELADO';
	                       $westado="off";
                          }

	                  }
	                 else
	                   {

						$wdatos_rol_enfermeria = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
					    $winf_nutricion_dsn = explode("-", $wdatos_rol_enfermeria);
						$wpatron_nutricion = $winf_nutricion_dsn[1]; // Patron asociado a las nutricionistas.

                         //Consulta si el patron es no combinable
                        $wsecombina = valida_combinable($wpatron);

                        if ($wsecombina != 'off'){
                                echo "5"; //Muestra un mensaje javascript que diciendo que no se puede cancelar el pedido.
                                return;
                         }else{

							 if($wpatron == $wpatron_nutricion){

								 echo "nocancelardsn"; //Muestra un mensaje javascript que diciendo que no se puede cancelar el pedido.
                                 return;

							 }

						 }



	                   }


				}


		  //Si la accion == off quiere decir que no se hizo ningun cambio sobre el patron por lo tanto no grabo nada
		  if ($waccion != "off" and trim($waccion) != "" )
			 {

			  if($wmodificar == '2' and $waccion == 'CANCELADO')
				{
                  $waccion= 'PEDIDO';
                }


              //Al ingresar a esta validacion se cancelaran los servicios.
			  if ($waccion=="CANCELADO" and $wpcomb != 'off')
				 {


				  //Busco el patron de la historia antes de cancelarlo
				  $q = " SELECT movdie, movpqu "
					  ."   FROM ".$wbasedato."_000077 "
					  ."  WHERE movfec = '".$wfecha."'"
					  ."    AND movhis = '".$whis."'"
					  ."    AND moving = '".$wing."'"
					  ."    AND movser = '".$wser."'"
					  ."    and movest = 'on' ";
				  $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $num_mov = mysql_num_rows($res_mov);


				  if ($num_mov > 0)
					 {

                      $row_mov=mysql_fetch_array($res_mov);

                       if ($row_mov[1] == 'on')
                        {
                            $wposqx = " POSTQUIRURGICO";
                        }
                        else
                        {
                            $wposqx = "";
                        }

                   $waccion = "CANCELADO"; //Este dato debe mantenerse ya que en el transcurso del script puede cambiar, y este se usa para horario de pedido normal.

                     //Se evaluan los servicios pedidos para el paciente.
                   $q_adi = " SELECT audacc, ".$wbasedato."_000078.Hora_data "
                            ."   FROM ".$wbasedato."_000078, ".$wbasedato."_000077  "
                            ."  WHERE ".$wbasedato."_000078.Fecha_data = '".$wfecha."'"
                            ."    AND ".$wbasedato."_000078.Fecha_data = ".$wbasedato."_000077.Fecha_data"
                            ."    AND ".$wbasedato."_000078.Hora_data = ".$wbasedato."_000077.Hora_data"
                            ."    AND audhis = '".$whis."'"
                            ."    AND auding = '".$wing."'"
                            ."    AND audser = '".$wser."'"
                            ."    AND movser = '".$wser."'"
                            ."    AND audcco = '".$wcco."'"
                            ."    AND audhis = movhis"
                            ."    AND auding = moving"
                            ."    AND movest = 'on'"
                            ."    AND audacc LIKE '%ADICION%'"
                          ." ORDER BY ".$wbasedato."_000078.Fecha_data, ".$wbasedato."_000078.hora_data desc";
                    $res_adi = mysql_query($q_adi,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_pedi." - ".mysql_error());
                    $num_adi= mysql_num_rows($res_adi);

                     //Se evaluan los servicios pedidos para el paciente.
                   $q_pedi = " SELECT audacc, ".$wbasedato."_000078.Hora_data "
                            ."   FROM ".$wbasedato."_000078, ".$wbasedato."_000077  "
                            ."  WHERE ".$wbasedato."_000078.Fecha_data = '".$wfecha."'"
                            ."    AND ".$wbasedato."_000078.Fecha_data = ".$wbasedato."_000077.Fecha_data"
                            ."    AND ".$wbasedato."_000078.Hora_data = ".$wbasedato."_000077.Hora_data"
                            ."    AND audhis = '".$whis."'"
                            ."    AND auding = '".$wing."'"
                            ."    AND audser = '".$wser."'"
                            ."    AND movser = '".$wser."'"
                            ."    AND audcco = '".$wcco."'"
                            ."    AND audhis = movhis"
                            ."    AND auding = moving"
                            ."    AND movest = 'on'"
                            ."    AND audacc LIKE '%PEDIDO%'"
                          ." ORDER BY ".$wbasedato."_000078.Fecha_data, ".$wbasedato."_000078.hora_data desc";
                    $res_pedido = mysql_query($q_pedi,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_pedi." - ".mysql_error());
                    $num_pedidos= mysql_num_rows($res_pedido);


                   //Se evalua si el sistema esta en horario de adicion, si es asi se evalua si el patron con el que viene es posqx, si es asi pero tiene
                    //un pedido antes entonces guarda "CANCELO ADICION", para permitir que seleccione otros patrones. En caso contrario si el usuario no es
                    //posquirurgico pero tiene un pedido previo entonces guardara "CANCELO PEDIDO EN ADICION", si guarda esta accion entonces no puede seleccionar
                    //mas patrones.

                    //Si es horario de adicion y encuentra adiciones para el paciente guardara "CANCELO ADICION".
                    switch ($whorario_adicional) {
                        case 'on':
                                   if ($num_pedidos > 0)
                                   {
                                        //Este estado se guarda cuando un paciente ya tenia patron desde el horario normal, y se lo cancelan en horario de adicion.
                                        $waccion = "CANCELO PEDIDO EN ADICION";

                                        if ($wpqx >= 1)
                                        {
                                            $wqueryadicion = " ,movpqu = '2'";
                                        }
                                   }
                                   else
                                   {
                                    if ($num_adi > 0)
                                    {
                                        //Este estado se guarda cuando un paciente no tenia patron; se lo selccionan, y luego se lo cancelan.
                                        $waccion = "CANCELO ADICION";
                                    }
                                   }
                            break;


                        default:
                            break;
                    }


                    //Se actualiza el servicio con movest=off, osea se cancela el servicio.
                    $q = " UPDATE ".$wbasedato."_000077 "
                        ."    SET movest = 'off' $wqueryadicion "
                        ."  WHERE movfec = '".$wfecha."'"
                        ."    AND movhis = '".$whis."'"
                        ."    AND moving = '".$wing."'"
                        ."    AND movser = '".$wser."'"
                        ."    AND movdie = '".$row_mov[0]."'"
                        ."    and movest = 'on' ";
                    $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

                    //Inactivo los productos de la his e ingreso para el servicio y patron, en caso de ser DSN
                    $q1 =    " UPDATE ".$wbasedato."_000084 "
                            ."    SET detest = 'off'"
                            ."  WHERE detfec = '".$wfec."'"
                            ."    AND dethis = '".$whis."'"
                            ."    AND deting = '".$wing."'"
                            ."    AND detcco = '".$wcco."'"
                            ."    AND detser = '".$wser."'"
                            ."    AND detpat = '".$row_mov[0]."'";
                    $res = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());

                    //Se cancela el servicio actual
                    if ($wpcomb != 'off')
                    {
                        //Grabo la Auditoria
                    $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad  , auddie, audcco) "
                        ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."','".$waccion.$wposqx."','".$wusuario."','C-".$wusuario."', '".$wpatron."', '".$wcco."') ";
                    $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
                    }

				     //Recorro los servicios asociados para que haga la cancelacion respectiva
					 for ($k=0;$k < count($wserv_asociados);$k++)
						{

                        $wptr_asociado = consultar_patron_actual($whis, $wing, $wserv_asociados[$k], $wfec); //Consulta los datos del patron asociado
                        $wdatos_ptr_asoc = explode("-", $wptr_asociado); // Explota la informacion
                        $wptr_serv_asociado = $wdatos_ptr_asoc[0]; // Se declara la informacion del patron del servicio asociado.
                        //Si el patron actual y el patron asociado son iguales entonces permite cancelar el servicio asociado.
                        if ($wptr_serv_asociado == $row_mov[0])
                            {

                            //Inactivo los productos de la his e ingreso para el servicio y patron, en caso de ser DSN
                            $q1 =    " UPDATE ".$wbasedato."_000084 "
                                    ."    SET detest = 'off'"
                                    ."  WHERE detfec = '".$wfec."'"
                                    ."    AND dethis = '".$whis."'"
                                    ."    AND deting = '".$wing."'"
                                    ."    AND detcco = '".$wcco."'"
                                    ."    AND detser = '".$wserv_asociados[$k]."'"
                                    ."    AND detpat = '".$row_mov[0]."'";
                            $res = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());


                            //Primero se verifica si el tipo de patron seleccionado es combinable ya que la audtoria de las cancelaciones de
                            //productos las hace la funcion procesar_datos_servind().
                            if ($wpcomb != 'off')
                                {
                                //Consulta si el servicio asociado se encuentra activo
                                $wservicioactivo = consultarservgrabado($wserv_asociados[$k],$whis, $wing);

                                if($wservicioactivo == 'on')
                                    {

                                //Cancelo los registros del servicio asociado.
                                $q = " UPDATE ".$wbasedato."_000077 "
                                    ."    SET movest = 'off' "
                                    ."  WHERE movfec = '".$wfecha."'"
                                    ."    AND movhis = '".$whis."'"
                                    ."    AND moving = '".$wing."'"
                                    ."    AND movser = '".$wserv_asociados[$k]."'"
                                    ."    and movest = 'on' ";
                                $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());


                                //Grabo la Auditoria de cancelado para el servicio asociado.
                                $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad  , auddie , audcco ) "
                                    ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wserv_asociados[0]."','CANCELADO DESDE SERVICIO ANTERIOR','".$wusuario."','C-".$wusuario."', '".$wpatron."', '".$wcco."') ";
                                $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

                                    }
                                }

                            }
                        }

                     }

					//CANCELACION DE SERVICIOS RELACIONADOS CON DSN, TIENE LA CARACTERISTICA DE ESTAR DELANTE DEL HORARIO ACTUAL Y DEBEN SER CANCELADOS, SI EL PACIENTE CAMBIA DE PATRON

					  $wdatos_rol_enfermeria = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
					  $winf_nutricion_dsn = explode("-", $wdatos_rol_enfermeria);

					  $wrolnutricion = $winf_nutricion_dsn[0];// Rol nutricionistas
					  $wpatron_nutricion = $winf_nutricion_dsn[1]; // Patron asociado a las nutricionistas.

					//Solo hace este procedimiento si la dieta que esta cancelando es segun nutricion // Jonatan 28 de nov de 2017
					if($wpatron == $wpatron_nutricion){

						//Verifico si el dia actual tiene mas servicio solicitados.
						$q_today =   " SELECT movser "
									."   FROM ".$wbasedato."_000077 "
									."  WHERE movfec = '".$wfecha."'"
									."    AND movhis = '".$whis."'"
									."    AND moving = '".$wing."'"
									."    AND movdie = '".$wpatron_nutricion."'"
									."    and movest = 'on' ";
						$res_today = mysql_query($q_today,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_today." - ".mysql_error());

						while($row_today = mysql_fetch_array($res_today)){

							//Solo se eliminan desde el servicios actual en adelante.
							if((int)$row_today['movser'] > (int)$wser){

								//Se actualiza el servicio con movest=off, osea se cancela el servicio.
								$q_today1 =  " UPDATE ".$wbasedato."_000077 "
											."	  SET movest = 'off' "
											."  WHERE movfec = '".$wfecha."'"
											."    AND movhis = '".$whis."'"
											."    AND moving = '".$wing."'"
											."    AND movser = '".$row_today['movser']."'"
											."    AND movdie = '".$wpatron_nutricion."'"
											."    and movest = 'on' ";
								$err_aux1 = mysql_query($q_today1,$conex) or die (mysql_errno().$q_today1." - ".mysql_error());

								//Inactivo los productos de la his e ingreso para el servicio y patron, en caso de ser DSN
								$q_today2 =    " UPDATE ".$wbasedato."_000084 "
											."    SET detest = 'off'"
											."  WHERE detfec = '".$wfecha."'"
											."    AND dethis = '".$whis."'"
											."    AND deting = '".$wing."'"
											."    AND detpat = '".$wpatron_nutricion."'"
											."    AND detser = '".$row_today['movser']."'";
								$res_today2 = mysql_query($q_today2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_today2." - ".mysql_error());


								 //Grabo la Auditoria de cancelado.
								$q_today3 = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad  , auddie , audcco ) "
																	 ."  VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$row_today['movser']."','CANCELADO','".$wusuario."','C-".$wusuario."', '".$wpatron."', '".$wcco."') ";
								$res_today3 = mysql_query($q_today3,$conex) or die (mysql_errno().$q_today3." - ".mysql_error());

								sleep(0.5);

								}

							}

						//===== CANCELACION DE SERVICIO PARA EL DIA SIGUIENTE =============

						//Verifico si al siguiente dia hay servicios solicitados, esto aplica para DSN.
						$validar_dia_sgte = verificar_dia_sgte($whis, $wing);

						//Si hay servicios solicitados, se deben cancelar.
						if($validar_dia_sgte > 0){

						$dia_sgte = date("Y-m-d", strtotime("$wfecha +1 day"));

						$q_next = " SELECT movser "
								."   FROM ".$wbasedato."_000077 "
								."  WHERE movfec = '".$dia_sgte."'"
								."    AND movhis = '".$whis."'"
								."    AND moving = '".$wing."'"
								."    and movest = 'on' ";
						$res_next = mysql_query($q_next,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_next." - ".mysql_error());

						while($row_next = mysql_fetch_array($res_next)){

							//Se actualiza elimina el registro del dia siguiente.
							$q_aux1 = 	 " UPDATE ".$wbasedato."_000077 "
										."	  SET movest = 'off'"
										."  WHERE movfec = '".$dia_sgte."'"
										."    AND movhis = '".$whis."'"
										."    AND moving = '".$wing."'"
										."    AND movser = '".$row_next['movser']."'"
										."    and movest = 'on' ";
							$err_aux1 = mysql_query($q_aux1,$conex) or die (mysql_errno().$q_aux1." - ".mysql_error());

							//Inactivo los productos de la his e ingreso para el servicio y patron, en caso de ser DSN
							$q_aux2 =    " UPDATE ".$wbasedato."_000084 "
										."    SET detest = 'off'"
										."  WHERE detfec = '".$dia_sgte."'"
										."    AND dethis = '".$whis."'"
										."    AND deting = '".$wing."'"
										."    AND detser = '".$row_next['movser']."'";
							$res_aux2 = mysql_query($q_aux2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_aux2." - ".mysql_error());


							 //Grabo la Auditoria de cancelado.
							$q_aux3 = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad  , auddie , audcco ) "
								."                            VALUES ('".$wbasedato."','".$dia_sgte."','".$whora."','".$whis."','".$wing."','".$row_next['movser']."','CANCELADO DESDE EL DIA ANTERIOR','".$wusuario."','C-".$wusuario."', '".$wpatron."', '".$wcco."') ";
							$res_aux3 = mysql_query($q_aux3,$conex) or die (mysql_errno().$q_aux3." - ".mysql_error());


							}
						}
					}

                     $waccion = "CANCELADO"; // Esta variable evita que ingrese a la siguiente validacion ya que esta parte del codigo es para las cancelaciones.

				 }
		// Esta parte de la funcion se usa para registrar nuevos patrones o modificaciones !!!!!IMPORTANTE

			  if ($waccion != "CANCELADO" and $wpcomb != 'off')
				 {

				  //Busco el patron de la historia
				  $q = " SELECT movdie, movobs, movint, movmpo, movpam, movcan "
					  ."   FROM ".$wbasedato."_000077 "
					  ."  WHERE movfec = '".$wfecha."'"
					  ."    AND movhis = '".$whis."'"
					  ."    AND moving = '".$wing."'"
					  ."    AND movser = '".$wser."'"
					  ."    and movest = 'on' ";
				  $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row_mov=mysql_fetch_array($res_mov);
				  $num_mov=mysql_num_rows($res_mov);

                  $wmedia_porcion1 = $row_mov[3];

                  if($wmedia_porcion1 == 'on')
                  {
                      $wcan = '0.5'; //Solicita media porcion
                  }
                  else
                  {
                      $wcan= '1';  //Solicita la porcion entera
                  }

                  $wpatron_anterior = $row_mov[0];


				  switch($wmodificar)
						{
							case '0':
										if ($num_mov > 0)
											{
											$wpatronfinal = $row_mov[0].",".$wpatron;
											}
										else
											{
											$wpatronfinal = $wpatron;
											};

							break;

							case '1':
							case '2':
										$wdatopatron = $wpatron2;
										$wdatopatron= str_replace("','','",",",$wpatron2);
										$wdatopatron2 = str_replace("'","",$wdatopatron);
										$wpatronfinal = $wdatopatron2;
							break;

						}

				  // Reemplazamos ',' por (,) en la cadena $wpatronfinal
				  $wdatopatron= str_replace(",","','",$wpatronfinal);

				   //Busco el codigo y el orden de los patrones
				  $q1 = "SELECT Diecod, Dieord "
					  ."   FROM ".$wbasedato."_000041 "
					  ."  WHERE Diecod in ('".$wdatopatron."')"
					  ."    AND Dieest = 'on' "
					 ."ORDER BY Dieord";
				  $res = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());

				  $wpatronf=array(); //Se inicializa el arreglo

				  // Asigno los valores resultantes de los codigos de las dietas a un arreglo, ordenados por Dieord
				  while ($row = mysql_fetch_array($res))
						{
						$wpatronf[]= $row['Diecod'];
						}

				  // Uno de nuevo los patrones ordenados, para que puedan ser registrados en su respectiva posicion y la tabla sea pintada en orden.
				  $wpatronfinal = implode(",",$wpatronf);

                //OBSERVACIONES DEL PACIENTE
                //Busco si hay alguna observacion en el ingreso actual del paciente
                $q =  " SELECT MAX(CONCAT(fecha_data,hora_data)),movobs, movdie "
                    ."   FROM ".$wbasedato."_000077 "
                    ."  WHERE movhis  = '".$whis."'"
                    ."    AND moving  = '".$wing."'"
                    ."    AND movser  = '".$wser."'"
                    ."    AND movobs != '' "
                    ."  GROUP BY 2 "
                    ."  ORDER BY 1 DESC ";
                $res_obs = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $row_mov = mysql_fetch_array($res_obs);
                $wobs=trim($row_mov[1]);


                //Busco si hay alguna Intolerancia en cualquier ingreso del paciente
                $q =  " SELECT MAX(CONCAT(fecha_data,hora_data)), movint "
                    ."   FROM ".$wbasedato."_000077 "
                    ."  WHERE movhis  = '".$whis."'"
                    ."    AND movint != '' "
                    ."  GROUP BY 2 "
                    ."  ORDER BY 1 DESC ";
                $res_int = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $rowint=mysql_fetch_array($res_int);
                $wint = $rowint[1];


                // Trae el dato que hay en la tabla 77 de movhos con respecto al posqx, si esta vacio utiliza la variable que trae en la funcion.
                $wcontrolposquiaux  = consultar_estado_posqui($whis, $wing, $wser);

                if ($wcontrolposquiaux == 'on' and count($wchequeados1) > 1)
                {
                $wcontrolposqui = $wcontrolposquiaux;

                }

                if($whorario_adicional == 'on')
                {
                    $wcontrolposqui = 'fueposqx'; //Este valor controla si el registro que se esta haciendo viene de un patron posquirurgico.
                }


				 //Traigo las observaciones del DSN del paciente antes de eliminar el registro. Jonatan Lopez 10 Abril de 2014
				 $wobservaciondsn = traer_observaciones_dsn($whis, $wing, date('Y-m-d'), $wser, $wcco);

				 //Traigo el patron asociado a DSN del paciente antes de eliminar el registro. Jonatan Lopez 10 Abril de 2014
				 $wpatronasociadodsn = traer_patron_asocia_dsn($whis, $wing, '','');

				   //Borro lo que tenia registrado la historia en el servicio, habitacion y fecha
				  $q = " DELETE FROM ".$wbasedato."_000077 "
					  ."  WHERE movfec = '".$wfecha."'"
					  ."    AND movhis = '".$whis."'"
					  ."    AND moving = '".$wing."'"
					  ."    AND movhab = '".$whab."'"
					  ."    AND movser = '".$wser."'";
				  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

				  if ($waccion=="CANCELADO")  //Grabo el registro pero con estado 'off'
					 $westado="off";

				//Grabo la dieta de cada historia y por cada servicio en el servicio actual
				  $q = " INSERT INTO ".$wbasedato."_000077 (      Medico    ,  Fecha_data , Hora_data  ,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,        movdie     , movind,   movobs        ,movest , movobc   ,     movval  ,     movint      ,     movpco       ,        movmpo        ,          movpam       ,   movcan  ,         movaut    ,   movcco  ,        movods         ,           movdsn          ,    Seguridad    ,       movpqu      ) "
						  ."      VALUES                   ('".$wbasedato."','".$wfecha."','".$whora."','".$wfec."','".$whis."','".$wing."','".$whab."','".$wser."','".$wpatronfinal."', 'N'   ,'".trim($wobs)."',  'on' ,    ''    , ".$wvalpat.",'".trim($wint)."', '".$wptrcobra."' ,'".$wmedia_porcion1."','".$wpatron_anterior."','".$wcan."', '".$wautomatico."','".$wcco."', '".$wobservaciondsn."', '".$wpatronasociadodsn."' ,'C-".$wusuario."','".$wcontrolposqui."') ";
				  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());


                    //Grabo la Auditoria
				  $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie, audcco  ) "
					  ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."','".$waccion."','".$wusuario."','C-".$wusuario."', '".$wpatronfinal."', '".$wcco."') ";
				  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

				  //Evalua si esta en horario de adicion, si no es asi, entonces $whorarioadicional == 'off', si esta en horario de adicion y el
                  //servicio asociado esta habilitado para ser guardado en adicion, entonces deja la variable $whorarioadicional == 'off', para que
                  //permita el registro, sino la hace on para que no lo permita.

				 if ($whora < $whorafinalser)
					{
					$whorarioadicional = 'off';
					}
                    else
                    {
                       if($westado_saa == 'on')
                            {
                            $whorarioadicional = 'off';
                            }
                       else
                            {
                            $whorarioadicional = 'on';
                            }
                    }


				 //La hora actual no debe ser mayor a la hora final del pedido, si es asi, no se guardara el servicio asociado.
				 if ($whorarioadicional != 'on' and $num_cos_asociado != 0 and $wnoregistraasociado !='on' and $wprog_asociado == 'on')
					{

					 //Recorro los servicios asociados para que haga la insercion respectiva
					 for ($k=0;$k < count($wserv_asociados);$k++)
						{

						//Traigo las observaciones del DSN del paciente antes de eliminar el registro. Jonatan Lopez 10 Abril de 2014
						$wobservaciondsn_aso = traer_observaciones_dsn($whis, $wing, date('Y-m-d'), $wserv_asociados[$k], $wcco);

						//Traigo el patron asociado a DSN del paciente antes de eliminar el registro. Jonatan Lopez 10 Abril de 2014
						$wpatronasociadodsn = traer_patron_asocia_dsn($whis, $wing, '','');

                        if ($wcontrolposqui == 'on' or $wcobraadicional == 'on')
                            {

                                $wdatos_historia = consultar_patron_actual($whis, $wing, $wserv_asociados[$k], $wfec); //Funcion para extraer los ultimos datos del servicio.
                                $wdatos_historia1 = explode("-", $wdatos_historia);  //Como la respuesta es un arreglo se explotan los datos.
                                $wpatronfinal = $wdatos_historia1[0]; // Ultimo patron del servicio asociado.
                                $wvalpat_asoc = $wdatos_historia1[1];  //Valor del patrón asociado
                                $wpatron_anterior = $wdatos_historia1[2]; //Patron anterior del servicio asociado
                                $wcontrolposqui = 1;   // Este control se hace para que el servicio asociado no reciba el parametro de posqx en on ya que es posible que el paciente
                            }                           //no sea posqx en el servicio asociado

								//Elimino el servicio adicional guardado para insertar los nuevos datos
								$q = " DELETE FROM ".$wbasedato."_000077 "
									."  WHERE movfec = '".$wfecha."'"
									."    AND movhis = '".$whis."'"
									."    AND moving = '".$wing."'"
									."    AND movhab = '".$whab."'"
									."    AND movser = '".$wserv_asociados[$k]."'";
								$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

								$q = " INSERT INTO ".$wbasedato."_000077 (      Medico    ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,             movser       ,        movdie     ,  movind  ,     movobs      , movest ,   movobc  ,         movval    ,   movint         ,     movpco      ,          movmpo       ,         movpam        ,   movcan   ,        movaut     ,    movcco ,            movods         ,           movdsn         , Seguridad        ,      movpqu       ) "
									."      VALUES                       ('".$wbasedato."','".$wfecha."','".$whora."','".$wfec."','".$whis."','".$wing."','".$whab."','".$wserv_asociados[$k]."','".$wpatronfinal."',    'N'   ,'".trim($wobs)."',  'on'  ,     ''    ,'".$wvalpat_asoc."','".trim($wint)."' , '".$wptrcobra."', '".$wmedia_porcion1."','".$wpatron_anterior."', '".$wcan."', '".$wautomatico."','".$wcco."', '".$wobservaciondsn_aso."', '".$wpatronasociadodsn."', 'C-".$wusuario."','".$wcontrolposqui."') ";
								$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

								//Grabo la Auditoria
								$q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad    ,      auddie        ,    audcco  , audoba,      audobn       ) "
									."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wserv_asociados[$k]."','PEDIDO','".$wusuario."','C-".$wusuario."', '".$wpatronfinal."', '".$wcco."',   ''  , '".trim($wobs)."' ) ";
								$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());


						}
					}
				 }
			  //=======================================================================================================================================================
			 }// if de la accion

		}
  //==================================================================================================================
  //==================================================================================================================
  //Realiza la insercion de los datos DSN cuando cargan de forma auntomatica.
 function procesar_datos_automatico($wemp_pmla, $whis, $wing, $wpatron, $wcco, $wser, $wfec, $whab, $wpac, $wdpa, $wtid, $wptr, $wmue, $wedad, $walp, $wtem, $west, $wusuario, $wmodificar, $wchequeados, $wcombinables, $wpcomb, $wmedia_porcion, $wautomatico, $wservgrabado, $wcantidad, $wmedia_porcionbd, $wobservaciones, $wintolerancias, $wpatron_nutricion)
		{


		  global $wbasedato;
		  global $conex;
          global $whabilitado;

		  $wfecha=date("Y-m-d");
		  $whora =(string)date("H:i:s");
          $wayer = time()-(1*24*60*60); //Resta un dia
          $wayer1 = date('Y-m-d', $wayer); //Formatea dia

          //Esta consulta valida si se esta en horario de pedido o en horario de adicion.
          $q_accion =    " SELECT COUNT(*) AS cuantos "
                        ."   FROM ".$wbasedato."_000076 "
                        ."  WHERE sercod = '".$wser."'"
                        ."    AND serhia <= '".$whora."'"
                        ."    AND serhad >= '".$whora."'"
                        ."    AND seradi  = 'on' ";
          $res_accion = mysql_query($q_accion,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_accion." - ".mysql_error());
          $row_accion = mysql_fetch_array($res_accion);

          //Segun la respuesta de esta consulta se asocia la accion, Adicion significa que estan cargando las solicitudes en horario de adicion
          //Pedido significa que estan cargando las solicitudes en horario de pedido normal, si se carga en horario de Adicion no se cargara el servicio asociado.
          if ($row_accion['cuantos'] > 0)
                {
                    $waccion = "ADICION";
                }
                else
                {
                    $waccion = "PEDIDO";
                }

		  //Consulto los servicios asociados al servicio actual y hago la insercion,
		  //aunque primero se verifica si el sistema esta en horario adicional, en tal caso no registra el servicio asociado.
		  $q = " SELECT seraso "
			  ."   FROM ".$wbasedato."_000076"
			  ."  WHERE sercod = '".$wser."'"
              ."    AND seraso != ''"
              ."    AND seraso != '.'"
              ."    AND seraso != 'NO APLICA'";
		  $resaso = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		  $rowaso=mysql_fetch_array($resaso);
		  $wserv_asociados=explode(",",$rowaso[0]);

          $wpos_seractual = evaluar_emp_paciente($wtem, $wser); // Esta variable identifica si el paciente es POS, en caso de serlo no se guardará el servicio de merienda(inicialmente).
          $wpos_serasociado = evaluar_emp_paciente($wtem, $wserv_asociados[0]); // Esta variable identifica si el paciente es POS y para el servicio asociado, en caso de serlo no se guardará el servicio de merienda(inicialmente).

          if($wserv_asociados[0] == '') // Esta validacion evalua que no se guarden datos si no tiene servicio asociado
                $wserv_asociados = array();

		  $whabilitado = validaciones('', '', '', $wser, "Consulta"); //Consulta si esta en el horario de solicitud de pedido, para guardar el encabezado.

          if($wservgrabado != 'on' and $whabilitado=='Enabled') //Verifica si el servicio esta grabado en la tabla  de 77 de movhos y si la hora es valida para grabar encabezado.
            {
            //Graba el encabezado del servicio actual
            grabar_encabezado($wser, $wcap, $wobserv_enfer);
            }

		  //Consulto la hora final del pedido para este servicio
		  $q1 = "  SELECT serhin, serhfi, sersaa, serhia, serhad"
			   ."    FROM ".$wbasedato."_000076 "
			   ."   WHERE serest = 'on' "
			   ."     AND sercod = '".$wser."'";
		  $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $row1 = mysql_fetch_array($res1);

		  $whorafinalser = $row1['serhfi'];
		  $westado_saa = $row1['sersaa']; //Estado servicio en adicion. (Controla si se pide servicio en horario adicional))

		 //La hora actual no debe ser mayor a la hora final del pedido, si es asi, no se guardara el servicio asociado, ademas el servicio asociado despues de adicion (Sersaa) debe estar en off.
		 if ($whora < $whorafinalser or $westado_saa != 'off')
			{
			//Recorro los servicios asociados para que haga la insercion respectiva del encabezado
			 for ($k=0;$k < count($wserv_asociados);$k++)
				{
                 if($wservgrabado !='on' and $whabilitado=='Enabled' and $wpos_serasociado == 'on')
                    {
                    grabar_encabezado($wserv_asociados[$k], $wcap, '');
                    }
				}
			}

			$wnocobrapatron = nosecobra($wpatron); //Esta funcion esta hecha especialmente para el patron NVO el cual no se cobra
			$wpatron_seleccionado = buscar_patronppal_serv_ant($whis, $wing, $wcco); //Trae el patron que se cobro en el servicio anterior.

            //Estas dos funciones traen el costo de los patrones de forma automatica.
			$num_cos = traer_costo_del_patron_aut($wpatron_seleccionado, $wtem, $wedad, $res_cos, $wser  , $wcob  , $wsec  , $wcbi   ,$wptrcobra, $wmedia_porcion, '', $wchequeados, $wautomatico, $wpcomb, $wnocobrapatron, $whis, $wing);
			$num_cos_asociado=traer_cos_servaso_auto($wpatron_seleccionado, $wtem, $wedad, $res_cos_asoc, $wserv_asociados[0], $wpatron, $wchequeados, $wautomatico, $wpcomb, $wnocobrapatron, $whis, $wing ); //Funcion para registrar el precion del servicio asociado

          if ($num_cos > 0)
			 {

			  $row_cos = mysql_fetch_array($res_cos);
              $row_cos_asociado = mysql_fetch_array($res_cos_asoc);

              $wvalpat = $row_cos[0];                   //Valor del patron.
			  $wcob=$row_cos[3];                        //Verifica si se cobra o no.
			  $wsec=$row_cos[4];
			  $wcbi=$row_cos[5];                        //Indica si es combinable o no con otros patrones en horario de Pedido normal.
			  $wptrcobra=$row_cos[6];                   //Indica el patron que se cobro.
              $wvalpat_asoc = $row_cos_asociado[0];     //Valor del patron asociado
			  $wcontrolregistro = 'on';                 //Esta variable se utiliza para controlar el registro automatico de las dietas desde un servicio asociado
                                                        //quiere decir desde la media mañana, algo o merienda, ya que algunos patrones no tienen costo en estos servicios.

			 }
           else
             {
               //Estas variables se declaran para patrones que no se cobran como el NVO.
               if ($wnocobrapatron == 'on')
               {
                    $wvalpat = 0;
                    $wptrcobra = $wpatron;
                    $wcontrolregistro = 'on';  //Esta variable se utiliza para controlar el registro automatico de las dietas desde un servicio asociado
                                               //quiere decir desde la media mañana, algo o merienda, ya que algunos patrones no tienen costo en estos servicios.
                    $num_cos_asociado = 1;
               }
               else
               {
                   $wcontrolregistro = 'off';
               }
             }

             //Si el valor del patron es mayor a cero se verifica si hay valores para fechas posteriores a la actual, en caso de existir se deja el mismo valor.
             if ($wvalpat > 0)
					 {
					  if ($wfecha >= $row_cos[1])
                      {

                          $wvalpat=$row_cos[0];            //Asigno el valor actual
                          $wvalpat_asoc=$row_cos_asociado[0];  //Asigno el valor actual

                      }
                      else
                      {
                          $wvalpat=$row_cos[2];     //Asigno el valor anterior a la fecha de cambio
                          $wvalpat_asoc=$row_cos_asociado[2];  //Asigno el valor anterior a la fecha de cambio

					 }
				 }

              //Si la accion existe y el patron tiene costo para el servicio actual, hace los registros.
			  if ($waccion != "" and $wcontrolregistro == 'on')
				 {

                    //OBSERVACIONES DEL PACIENTE
                    //Busco si hay alguna observacion en el ingreso actual del paciente
                    // $q =  " SELECT MAX(CONCAT(fecha_data,hora_data)),movobs, movpqu "
                        // ."   FROM ".$wbasedato."_000077 "
                        // ."  WHERE movhis  = '".$whis."'"
                        // ."    AND moving  = '".$wing."'"
                        // ."  GROUP BY movobs "
                        // ."  ORDER BY 1 DESC, id DESC";

					//OBSERVACIONES DEL PACIENTE
                    //Busco si hay alguna observacion en el ingreso actual del paciente
					// $q = " SELECT CONCAT(fecha_data,hora_data),movobs, movpqu "
                        // ."   FROM ".$wbasedato."_000077 "
                        // ."  WHERE movhis  = '".$whis."'"
                        // ."    AND moving  = '".$wing."'"
                        // ."  ORDER BY id DESC"
                        // ."  LIMIT 1";

					$q = " SELECT fecha_data,movobs, movpqu, id, movfec, movser "
                        ."   FROM ".$wbasedato."_000077 "
                        ."  WHERE movhis  = '".$whis."'"
                        ."    AND moving  = '".$wing."'"
                        ."  ORDER BY fecha_data DESC , movser DESC "
                        ."  LIMIT 1";
                    $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $row_mov = mysql_fetch_array($res_mov);
                    $wobservaciones=trim($row_mov[1]);

                    //Valida si hay observacion en el ultimo registro para la historia e ingreso, si no es asi, muestra las observaciones del kardex.
                    if ($wobservaciones == '')
                    {
                        $wobservaciones = traer_observ_alimentacion($whis, $wing);
                    }
                    else
                    {
                        $wobservaciones = trim($row_mov[1]);
                    }

                    //Consulta si el atron que viene es postquirurgico, si es asi, registro el numero 1 que me indica que el paciente viene con Lc o NVO
                    //para que al pasar el tiempo establecido se le puede seleccionar el posqx.
                    $q1 =    " SELECT diepqu "
                            ."   FROM ".$wbasedato."_000077,".$wbasedato."_000041 "
                            ."  WHERE movhis  = '".$whis."'"
                            ."    AND moving  = '".$wing."'"
                            ."    AND movpco = diecod"
                            ."    AND movpco = '".$wpatron."'"
                            ."    AND movest = 'on'";
                    $res_mov1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
                    $row_mov1 = mysql_fetch_array($res_mov1);
                    $wquirur=$row_mov1[0];

                    //Aqui se verifica si el patron que viene es posquirurgico, en ese caso se debe guardar el numero 1 en el campo movpqu, para que asi se pueda activar el cajon posquirurgico.
                    if ($wquirur == 'on')
                    {
                        $wquirur = '1';
                    }

				    $q_verif = " SELECT movods "
							."   FROM ".$wbasedato."_000077 "
							."  WHERE movhis  = '".$whis."'"
							."    AND moving  = '".$wing."'"
							."    AND movser  = '".$wser."'"
							."    AND Fecha_data  = '".$wfecha."'";
                    $res_verif = mysql_query($q_verif,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_verif." - ".mysql_error());
                    $row_verif = mysql_fetch_array($res_verif);
                    $num_verif = mysql_num_rows($res_verif);

					if($num_verif > 0){

					 $q_verif =  " DELETE FROM ".$wbasedato."_000077 "
								."  WHERE movhis  = '".$whis."'"
								."    AND moving  = '".$wing."'"
								."    AND movser  = '".$wser."'"
								."    AND Fecha_data  = '".$wfecha."'";
                    $res_verif = mysql_query($q_verif,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_verif." - ".mysql_error());

					}

				  //Grabo la dieta de cada historia y por cada servicio en el servicio actual
				  $q = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,   movdie     , movind,   movobs        ,   movest     , movobc,  movval    ,   movint,    movpco,    movmpo,  movpam, movcan,   movaut,    movcco,   Seguridad , movpqu, movods     ) "
						  ."      VALUES                       ('".$wbasedato."','".$wfecha."','".$whora."','".$wfec."','".$whis."','".$wing."','".$whab."','".$wser."','".$wpatron."', 'N'   ,'".trim($wobservaciones)."', 'on', ''    ,'".$wvalpat."','".trim($wintolerancias)."', '".$wptrcobra."' ,'','','1', '".$wautomatico."','".$wcco."' ,'C-".$wusuario."','".$wquirur."', '".$row_verif['movods']."') ";
				  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());


                  //Grabo la Auditoria
				  $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad  , auddie , audcco , audoba, audobn ) "
					  ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."','".$waccion."','".$wusuario."','C-".$wusuario."', '".$wpatron."', '".$wcco."', '', '".trim($wobservaciones)."' ) ";
				  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

                  //******************************************************************************************************************************
                  //De aqui en adelante se procesan los datos del servicio asociado.

                  //Evalua si esta en horario de adicion, si no es asi, entonces $whorarioadicional == 'off', si esta en horario de adicion y el
                  //servicio asociado esta habilitado para ser guardado en adicion, entonces deja la variable $whorarioadicional == 'off', para que
                  //permita el registro, sino la hace lo deja en "on" para que no lo permita.
                  if ($whora < $whorafinalser)
					{
					   $whorarioadicional = 'off';
					}
                    else
                    {
                       if($westado_saa == 'on')
                            {
                            $whorarioadicional = 'off'; // Aqui la variable queda en off ya que el servicio asociado en adicion esta en on, lo que significa que si se permite guardar
                                                        //los patrones del servicio asociado en horario de adicion.
                            }
                       else
                            {
                            $whorarioadicional = 'on';  //Esta variable es "on", porque se esta en horario de adicion y no se permite guardar el servicio asociado.
                            }
                    }

				 //La hora actual no debe ser mayor a la hora final del pedido, si es asi, no se guardara el servicio asociado.
				 if ($whorarioadicional != 'on' and $num_cos_asociado != 0 and $wcontrolregistro == 'on')
					{
					 //Recorro los servicios asociados para que haga la insercion respectiva
					 for ($k=0;$k < count($wserv_asociados);$k++)
						{
						//Consulta el estado del servicio asociado.  02 Enero de 2014 Jonatan
						$q_seraso =    " SELECT movods "
									  ."   FROM ".$wbasedato."_000077"
									  ."  WHERE movhis  = '".$whis."'"
									  ."    AND moving  = '".$wing."'"
									  ."    AND movfec  = '".$wfecha."'"
									  ."    AND movser = '".$wserv_asociados[$k]."'";
						$res_seraso = mysql_query($q_seraso,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_seraso." - ".mysql_error());
						$num_seraso = mysql_num_rows($res_seraso);
						$row_seraso = mysql_fetch_array($res_seraso);

						//Verifica si ya tiene servicio asociado solicitado para el dia actual, si no tiene, permite el registro.
						if($num_seraso == 0){

							$q = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,   movdie     , movind,   movobs        ,   movest     , movobc,  movval    ,   movint,      movpco , movmpo, movpam , movcan, movaut, movcco     , Seguridad , movpqu ) "
								."      VALUES                       ('".$wbasedato."','".$wfecha."','".$whora."','".$wfec."','".$whis."','".$wing."','".$whab."','".$wserv_asociados[$k]."','".$wpatron."', 'N'   ,'".trim($wobservaciones)."','on', ''    ,'".$wvalpat_asoc."','".trim($wintolerancias)."' , '".$wptrcobra."'  , '','".$wpatron_anterior."', '1', '".$wautomatico."','".$wcco."', 'C-".$wusuario."','".$wquirur."') ";
							$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

							//Grabo la Auditoria
							$q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie , audcco , audoba, audobn ) "
								."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wserv_asociados[$k]."','PEDIDO','".$wusuario."','C-".$wusuario."', '".$wpatron."', '".$wcco."' , '', '".trim($wobservaciones)."') ";
							$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

							}else{
							//Si tiene registros en el servicio actual, lo elimina y luego registra.
							$q_verif_aso =   " DELETE FROM ".$wbasedato."_000077 "
											."  WHERE movhis  = '".$whis."'"
											."    AND moving  = '".$wing."'"
											."    AND movser  = '".$wserv_asociados[$k]."'"
											."    AND Fecha_data  = '".$wfecha."'";
							$res_verif_aso = mysql_query($q_verif_aso,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_verif_aso." - ".mysql_error());

							$q = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis  ,   moving  ,   movhab  ,   movser  ,   movdie     , movind,   movobs        ,   movest     , movobc,  movval    ,   movint,      movpco , movmpo, movpam , movcan, movaut, movcco     , Seguridad , movpqu, movods ) "
								."      VALUES                       ('".$wbasedato."','".$wfecha."','".$whora."','".$wfec."','".$whis."','".$wing."','".$whab."','".$wserv_asociados[$k]."','".$wpatron."', 'N'   ,'".trim($wobservaciones)."','on', ''    ,'".$wvalpat_asoc."','".trim($wintolerancias)."' , '".$wptrcobra."'  , '','".$wpatron_anterior."', '1', '".$wautomatico."','".$wcco."', 'C-".$wusuario."','".$wquirur."', '".$row_seraso['movods']."') ";
							$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

							//Grabo la Auditoria
							$q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad , auddie , audcco, audoba, audobn  ) "
								."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wserv_asociados[$k]."','PEDIDO','".$wusuario."','C-".$wusuario."', '".$wpatron."', '".$wcco."' ,'', '".trim($wobservaciones)."') ";
							$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

							}


						}
					}
				 }
			  //=======================================================================================================================================================
		}


function filtrarZonas(){
    global $wemp_pmla, $wcco;
    $datamensaje = array('mensaje'=>'', 'error'=>0, 'html'=>'');

    $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

    $array_cco = explode("-", $wcco);

    $q_sala =      "  SELECT Arecod, Aredes  "
                 . "    FROM ".$wbasedato."_000020, ".$wbasedato."_000169 "
                 ."    WHERE habcco = '".$array_cco[0]."'"
                 ."      AND habzon = Arecod "
                 ." GROUP BY habzon, habcco ";
    $res_sala = mysql_query($q_sala, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_sala." - ".mysql_error());
    $num_salas = mysql_num_rows($res_sala);

    $datamensaje['nro_zonas'] = $num_salas;

    $array_salas = array();

    while( $row_salas = mysql_fetch_assoc($res_sala)) {

        if(!array_key_exists($row_salas['Arecod'], $array_salas )){

            $array_salas[$row_salas['Arecod']] = $row_salas;

        }

    }

    $datamensaje['html'].= "<select id='wzona' name='wzona' >";
    $datamensaje['html'].= "<option value='%'>Todas</option>";

    if(is_array($array_salas)){
        foreach($array_salas as $key => $row_sala){

            $datamensaje['html'].= "<option value='".$row_sala['Arecod']."' $sala_seleccionada>".$row_sala['Aredes']."</option>";
        }
    }

    $datamensaje['html'] .= "</select>";

    return( json_encode($datamensaje) );
}

  //==================================================================================================================
  //==================================================================================================================



  //==================================================================================================================
  //==================================================================================================================
  //************************************* P R O G R A M A    P R I N C I P A L ***************************************
  //==================================================================================================================
  //==================================================================================================================
   //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA
  //===============================================================================================================================================


  global $wusuario;
  global $consultaAjax;
  global $wusuario;
  global $wcco;
  global $wser;

  $color_esq_actual = 'yellow';
  $color_ant_sing = 'silver';

  if (isset($consultaAjax))
		     {

				switch($consultaAjax)
					{

				case 'procesar_datos':
					{
						echo procesar_datos($wemp_pmla, $whis, $wing, $wpatron, $wcco, $wser, $wfec, $whab, $wpac, $wdpa, $wtid, $wptr, $wmue, $wedad, $walp, $wtem, $west, $wusuario, $wmodificar, $wchequeados, $wcombinables, $wpcomb, $wmedia_porcion, $wautomatico, $wservgrabado, $wcontrolposqui, $wrol_usuario, $wpatron_nutricion, $wrolnutricion, $wseleccionado, $wconfirmar_canceladsn, $codDSN, '');
					}
				break;

				case 'procesar_datos_servind':
					{
						echo procesar_datos_servind($wemp_pmla, $wbasedato, $wcodigo, $wpatron, $whis, $wing, $wser, $wvalorneto, $wusuario, $whab, $wcco, $westado, $wcantidad, $wfecha_interfaz, $wclasificacion);
					}
				break;

				case 'grabar_observ_intoler':
					{
						echo grabar_observ_intoler($wemp_pmla, $wbasedato, $whis, $wing, $whab, $wser, $wtexto, $wusuario, $wobsint, $wcco, $wfec);
					}
				break;

                case 'procesar_datos_dsn':
                    {
						echo procesar_datos_dsn($wemp_pmla, $wbasedato, $winf_prod, $wpatron, $whis, $wing, $wser, $wusuario, $whab, $wcco, $wfecha_interfaz,$wpatron_asociado, $wobservacion, $cant_product_servi);
					}
				break;


                case 'grabar_media_porcion':
                    {
                    echo grabar_media_porcion($wemp_pmla, $wbasedato, $whis, $wing, $whab, $wser, $westado, $wfec, $wcco, $wusuario);

                    }

                break;

                 case 'grabar_posqx':
                    {
                    echo grabar_posqx($wemp_pmla, $wbasedato, $whis, $wing, $whab, $wser, $westado, $wedad, $wtipemp, $wusuario, $wfec, $wcco, $whora_max_modifi, $hora_max_cancela);

                    }

                break;


             case 'mostrar_modal':
                    {
                    echo definir_div($wpatron, $fila, $whis, $wing, $wser, $i, $j, $whab, $wfec, $wcco, $wusuario, $wnombre_pac);
                    }
                break;

            case 'grabar_observ_dsn':
                    {
                    echo grabar_observ_dsn($wemp_pmla, $wbasedato, $whis, $wing, $whab, $wser, $wserdsn, $wusuario, $wcco, $wfec, $wtexto, $wpatron, $wpatron_asoc);
                    }
                break;

             case 'cancelar_dsn':
                    {
                    echo cancelar_dsn($wemp_pmla, $wbasedato, $whis, $wing, $whab, $wser, $wserdsn, $wpatron, $wusuario, $wcco, $wfec );
                    }
                break;

            case 'patron_asoc_dsn':

                echo patron_asoc_dsn($wemp_pmla, $wbasedato, $whis, $wing, $wser, $whab, $wusuario, $wcco, $wfec, $wpatron, $wtexto);

                break;

            case 'funcionhistoriaurgencias':

                echo funcionhistoriaurgencias($wemp_pmla, $wbasedato, $whis, $wing, $wcco);

                break;

			case 'recuperar_dsn_nutri':

                echo recuperar_dsn_nutri($wemp_pmla, $wbasedato, $whis, $wing, $wpatron_nutricion);

                break;

            case 'filtrarzonas':

                echo filtrarZonas();

			default : break;
					}
				return;


			 }

  if (!isset($consultaAjax))
	{

  echo "<form name='dietas' id='dietas' action='' method='post' >";
  echo "<input type='hidden' id='wlimite_caracteres_observ' name='wlimite_caracteres_observ' value='".$wlimite_caracteres_observ."'>";

  $wccosto=explode("-",$wcco);
  $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

  echo "<center><table>";

  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

  if (!isset($wcco) or trim($wcco) == "" or !isset($wser) or $wser=="" or !isset($wfec) or $wfec=="")
     {

     //Esta funcion trae los centros de costo hospitalarios, cirugia y urgencias.
     $centrosCostosCCO = consultaCentrosCostos($cco = 'ccohos, ccocir, ccourg');


	  echo "<tr><td align=right class=fila1><b>SELECCIONE LA UNIDAD EN LA QUE SE ENCUENTRA: </b></td>";
	  echo "<td align=center class=fila2><select name='wcco' id='sl_wcco'>";
	  echo "<option>&nbsp</option>";
	 foreach ($centrosCostosCCO as $centroCostos)
            {
                if(isset($wcco) && $$wcco==$centroCostos->codigo."-".$centroCostos->nombre)
                {
                    echo "<option value='".$centroCostos->codigo."-".$centroCostos->nombre."' selected>".$centroCostos->codigo."-".$centroCostos->nombre."</option>";
                }
                else
                {
                    echo "<option value='".$centroCostos->codigo."-".$centroCostos->nombre."'>".$centroCostos->codigo."-".$centroCostos->nombre."</option>";
                }
            }
      echo "</select></td></tr>";
      echo "<tr id='tabla_zonas' style='display:none'><td align=right class=fila1><b>SELECCIONE ZONA: </b></td>";
      echo "<td class=fila2><div id='select_zonas'></div></td></tr>";


      //===============================================================================================================================
      //traigo los registros del Maestro de Servicios
      //===============================================================================================================================
      $q = " SELECT sernom, sercod "
	      ."   FROM ".$wbasedato."_000076 "
	      ."  WHERE serest = 'on' "
		  ."    AND seraso != ''"
          ."    AND seraso != '.'"
          ."    AND seraso != 'NO APLICA'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);


      echo "<tr><td class=fila1 align=right><b>SELECCIONE EL SERVICIO DE ALIMENTACION QUE VA A SOLICITAR: </b></td>";
	  echo "<td align=center class=fila2><select name='wser'>";
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option value=".$row[1].">".$row[0]."</option>";
	     }
      echo "</select></td></tr>";
      //===============================================================================================================================

      echo "<tr>";
      echo "<td align=right  class=fila1><b>Fecha a Registrar: </b></td>";
      echo "<td align=center class=fila2>";

      if(isset($wfec))
	  {
		campoFechaDefecto("wfec", $wfec);
      }
	  else
	  {
		campoFechaDefecto("wfec", date("Y-m-d"));
	  }
      echo "</td>";
      echo "</tr>";

	  echo "<center><tr><td align=center colspan=4 bgcolor=".$color_ant_sing."></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
	  echo "</table>";
     }
    else  //Esta setiado CCO, SER y Fecha
      {
	   if (strpos($wcco,"-") > 0)
	      {
	       $wccosto=explode("-",$wcco);
	       $wcco=$wccosto[0];
          }
         else
           {
            if (strpos($wcco,".") > 0)
	   	       {
		        $wccosto=explode(".",$wcco);
		        $wcco=$wccosto[1];
	           }
	       }

	   $q = " SELECT Aredes "
	       ."   FROM ".$wbasedato."_000169"
	       ."  WHERE Arecod = '".$wzona."'"
	       ."    AND Areest = 'on'";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $row = mysql_fetch_array($res);
	   $wzonanom=$row[0];

       $q = " SELECT Cconom "
           ."   FROM ".$wtabcco
           ."  WHERE ccocod = '".$wcco."'"
           ."    AND ccoemp = '".$wemp_pmla."'";
       $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
       $row = mysql_fetch_array($res);
       $wnomcco=$row[0];



	   if (trim($wnomcco)!="")  //Si hay Cco valido
	     {
		  //====================================================================================================================
		  //*************** G R A B A R ***************
		  //====================================================================================================================



	       if (!isset($consultaAjax))
			{
	       echo "<tr class=titulo>";
		   echo "<td colspan=23 align=center><b>Servicio o Unidad: ".$wnomcco."</b></td>";
		   echo "</tr>";

           if( $wzona != "" and $wzona != "%" and isset($wzona) ){
               echo "<tr class=titulo>";
               echo "<td colspan=23 align=center><b>Zona: ".$wzonanom."</b></td>";
               echo "</tr>";
            }

		   echo "<tr class=titulo>";
		   echo "<td colspan=23 align=center><b>Fecha de Registro: ".$wfec."</b></td>";
		   echo "</tr>";

           //traigo los registros del Maestro de Servicios
	       $q = " SELECT sernom, sercod "
		       ."   FROM ".$wbasedato."_000076 "
		       ."  WHERE serest = 'on' ";
		   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	       $num = mysql_num_rows($res);

		   //Traigo el nombre del servicio para que lo imprima en el seleccionable
		   $q1 = " SELECT sernom "
		       ."   FROM ".$wbasedato."_000076 "
		       ."  WHERE serest = 'on' "
			   ."	 AND sercod = ".$wser."";
		   $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
		   $row1 = mysql_fetch_array($res1);

	       echo "<tr class=seccion1><td colspan=3>&nbsp</td></tr>";
	       echo "<tr class=seccion1><td align=center><b>SELECCIONE EL SERVICIO DE ALIMENTACION QUE VA A SOLICITAR: </b></td>";
		   echo "<td align=center><SELECT name='wser' onchange='enter()'>";

		   if (isset($wser))
		      echo "<OPTION SELECTED value=".$wser.">".$row1[0]."</OPTION>";
		   for ($i=1;$i<=$num;$i++)
		      {
		       $row = mysql_fetch_array($res);
		       echo "<OPTION value=".$row[1].">".$row[0]."</OPTION>";
		      }
	       echo "</SELECT></td>";

	       validaciones('', '', '', $wser, 'Consulta');


	       echo "</tr>";

		   echo "<tr class=seccion1><td colspan=3>&nbsp</td></tr>";
		   echo "<tr>&nbsp</tr>";

		   echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";

		   echo "</table>";

		   if (isset($wser))
		      {
			   $q = " SELECT serhin, serhfi, serhia, serhad, serhca, serhdi, serexp, serhidra, serhfdra "
			       ."   FROM ".$wbasedato."_000076 "
			       ."  WHERE serest = 'on' "
			       ."    AND sercod = '".$wser."'";
		       $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	           $num = mysql_num_rows($res);

	           if ($num>0)
	              {
		           $row = mysql_fetch_array($res);

                  //Convenciones y horarios.

                   echo "<table style='text-align: left; width: 1000px; height: 200px;' border='1' cellpadding='1' cellspacing='1'>
                        <tbody>
                            <tr class=fila1>
                            <td  align='center' style='font-family: Arial; width: 188px;' colspan='4' rowspan='1'><span style='font-weight: bold;'>HORARIO DE PEDIDOS</span></td>
                            <td colspan='1' rowspan='6'>
                            <table style='text-align: left; width: 200px; height: 194px;' border='1' cellpadding='2' cellspacing='2' class=fila2>
                                <tbody>
                                <tr style='font-family: Arial;'>
                                    <td align=center><span style='font-weight: bold;'>CONVENCIONES</span></td>
                                </tr>
                                <tr style='font-family: Arial;'>
                                    <td bgcolor=".$color_ant_sing.">PEDIDO PARA MAÑANA</td>
                                </tr>
                                <tr style='font-family: Arial;'>
                                    <td bgcolor=".$color_esq_actual.">PEDIDO PARA HOY</td>
                                </tr>
                                <tr style='font-family: Arial;' class=fondoAmarillo>
                                    <td>EN PROCESO DE ALTA</td>
                                </tr>
                                <tr style='font-family: Arial;' class=colorAzul4>
                                    <td>PENDIENTE DE RECIBIR</td>
                                </tr>
                                </tbody>
                            </table>
                            </td>
                            </tr>
                            <tr class=fila2>
                            <td style='font-family: Arial; font-weight: bold; text-align: center; width: 203px;'>Hora Inicio</td>
                            <td style='font-family: Arial; font-weight: bold; text-align: center; width: 215px;'>Hora Final</td>
                            <td style='font-family: Arial; font-weight: bold; text-align: center; width: 201px;'>Hora Distribución</td>
                            <td style='font-family: Arial; font-weight: bold; text-align: center; width: 188px;'>Hora Máxima Cancelación</td>
                            </tr>
                            <tr>
                            <td style='font-family: Arial; width: 203px; text-align: center;'>".$row[0]."</td>
                            <td style='font-family: Arial; width: 215px; text-align: center;'>".$row[1]."</td>
                            <td style='font-family: Arial; width: 201px; text-align: center;'>".$row[5]."</td>
                            <td style='font-family: Arial; width: 188px; text-align: center;'>".$row[4]."</td>
                            </tr>
                            <tr class=fila1>
                            <td style='text-align: center; font-family: Arial; width: 188px;' colspan='4' rowspan='1'><span style='font-weight: bold;'>HORARIO DE ADICIONES</span></td>
                            </tr>
                            <tr class=fila2>
                            <td style='font-family: Arial; font-weight: bold; text-align: center; width: 203px;'>Hora Inicio</td>
                            <td style='font-family: Arial; font-weight: bold; text-align: center; width: 215px;'>Hora Final</td>
                            <td style='font-family: Arial; font-weight: bold; text-align: center; width: 201px;'>Hora Inicio Distribución</td>
                            <td style='font-family: Arial; font-weight: bold; text-align: center; width: 188px;'>Hora Final Distribución</td>
                            </tr>
                            <tr>
                            <td style='font-family: Arial; width: 203px; text-align: center;' >".$row[2]."</td>
                            <td style='font-family: Arial; width: 215px; text-align: center;' >".$row[3]."</td>
                            <td style='font-family: Arial; width: 201px; text-align: center;' >".$row[7]."</td>
                            <td style='font-family: Arial; width: 188px; text-align: center;' >".$row[8]."</td>
                            </tr>
                        </tbody>
                        </table>";

	              }
		      }



            $wmensaje = mensajedelservicio($wser);


            echo "<table width='1000'>";
            echo "<tr>";
            echo "<td align=left width='250'><b>Hora Actual:</b><span id='reloj'></span></td>";
            echo "<td align=center><font color='red' size=5>".$wmensaje."</font><td>";
            echo "</tr>";
            echo "<table>";


		   echo "<table>";
		   mensajeria();
		   echo "</table>";

       $q =  "  SELECT  Ccohos, Ccocir, Ccourg
                FROM    ".$wbasedato."_000011
                WHERE   Ccocod  = '".$wcco."'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);
      $es_urgencias = $row['Ccourg'];

      /*if($es_urgencias == 'on')//->2019-09-23 Se omite este campo puesto que a partir de la fecha se van a mostrar todos los pacientes de urgencias.
      {
         echo ' <table align="center">
                    <tr>
                        <td colspan="3" class="encabezadoTabla">Digite el n&uacute;mero de historia del paciente si no aparece en la lista</td>
                    </tr>
                    <tr>
                        <td class="encabezadoTabla">
                            Historia:
                        </td>
                        <td class="fila1">
                            <input type="text" id="idhistoria" name="idhistoria" value="" class="" onKeyPress="return soloNumeros(event);">
                        </td>
                        <td rowspan="2" class="fila2">
                            <input type="button" onclick="funcionhistoriaurgencias(\''.$wemp_pmla.'\',\''.$wbasedato.'\', \''.$wcco.'\');" value="Ok">
                        </td>
                    </tr>
                </table>';
      }*/
		   echo "<table>";

		   $q_ser =  " SELECT sercod "
					."   FROM ".$wbasedato."_000076 "
					."  WHERE serest = 'on'";
		   $res_ser = mysql_query($q_ser,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_ser." - ".mysql_error());
		   $num_ser = mysql_num_rows($res_ser);

           echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
           echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
           echo "<input type='HIDDEN' id='centro_costos' name='centro_costos' value='".trim($wccosto[0])."'>";
           echo "<input type='HIDDEN' id='servicio' name='servicio' value='".$wser."'>";
           echo "<input type='HIDDEN' id='usuario' name='usuario' value='".$wusuario."'>";
           echo "<input type='HIDDEN' id='wfec' name='wfec' value='".$wfec."'>";
           echo "<input type='HIDDEN' id='wcco' name='wcco' value='".$wcco."'>";
           echo "<input type='HIDDEN' id='cantidad_servicios' name='cantidad_servicios' value='".$num_ser."'>";

		   mostrar();
		   echo "</table>";
		   echo "<table>";
		   echo "<tr>";
		   echo "<td align=center bgcolor=".$color_ant_sing."></td>";
		   echo "</tr>";
		   echo "</table>";
		   //echo "<meta http-equiv='refresh' content='50;url=Dietas.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wcco=".$wcco."&wser=".$wser."&wfec=".$wfec."' id='meta-refresh'>";
		   }
		 } //if $wnomcco
	    else
	      {
           ?>
	         <script>
		       alert ("EL CENTRO DE COSTO NO FUE INGRESADO POR CODIGO DE BARRAS");
		     </script>
		   <?php
		  }
	  echo "</table>";
	  echo "<br>";

      echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
      echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
      echo "<input type='HIDDEN' name='wcco' value='".trim($wcco)."'>";
      echo "<input type='HIDDEN' name='wzona' value='".trim($wzona)."'>";


        echo "<tr>";
        echo "<td align=center colspan=7><A href='Dietas.php?wtabcco=".$wtabcco."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."'><b>Retornar</b></A></td>";
        echo "</tr>";

     }  //if cco, ser y fecha

    echo "</form>";
    echo "<div id='msjEspere' name='msjEspere' style='display:none;'>";
    echo "<br /><img src='../../images/medical/ajax-loader5.gif'/><br /><br />Por favor espere un momento ... <br /><br />";
    echo "</div>";
    echo "<br>";

	echo "<table>";
	echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
	echo "</table>";

    } // fin de la validacion para la variable $consultaAjax
} // if de register
?>