<html>
<head>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css">
     <!-- PNotify -->
    <link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
    <link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.brighttheme.css" rel="stylesheet">

    <script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery-ui.min.js"></script>
    <script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
    <script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
    <script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <!-- PNotify -->
    <script type="text/javascript" src="../../../include/gentelella/vendors/pnotify/dist/pnotify.js" type="text/rocketscript"></script>
    <script type="text/javascript" src="../../../include/gentelella/vendors/pnotify/dist/pnotify.buttons.js" type="text/rocketscript"></script>
    <script type="text/javascript" src="../../../include/gentelella/vendors/pnotify/dist/pnotify.nonblock.js" type="text/rocketscript"></script>

    <title>MATRIX Listas Para Altas</title>
    <!-- UTF-8 is the recommended encoding for your pages -->

    <style type="text/css">
        body{background:white url(portal.gif) transparent center no-repeat scroll;}
        #tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
        #tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
        .tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
        .tipo4{color:#000066;background:#dddddd;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        #tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;}
        .tipo6{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        #tipo7{color:#FFFFFF;background:#000066;font-size:12pt;font-family:Tahoma;font-weight:bold;width:30em;}
        #tipo8{color:#99CCFF;background:#000066;font-size:6pt;font-family:Tahoma;font-weight:bold;}
        #tipo9{color:#660000;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        #tipo10{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        #tipo11{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        #tipo12{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
        #tipo13{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
        #tipo14{color:#FFFFFF;background:#000066;font-size:14pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        #tipo15{color:#000066;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
        #tipo16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        #tipo17{color:#000066;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
        #tipo18{color:#000066;background:#FFCC66;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
        #tipo19{color:#CC0000;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
        #tipo20{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
        #tipo21{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}

        #tipo12A{color:#FF0000;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
        #tipo13A{color:#FF0000;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}


        #tipoG00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
        #tipoG01{color:#FFFFFF;background:#FFFFFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
        #tipoG54{color:#000066;background:#DDDDDD;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
        #tipoG11{color:#FFFFFF;background:#99CCFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
        #tipoG21{color:#FFFFFF;background:#CC3333;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
        #tipoG32{color:#FF0000;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
        #tipoG33{color:#006600;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
        #tipoG34{color:#000066;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
        #tipoG42{color:#FF0000;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
        #tipoG41{color:#FFFFFF;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
        #tipoG44{color:#000066;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}

        #tipoM00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
        #tipoM01{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:3em;}
        #tipoM02{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:3em;}

        .ui-dialog { position: absolute; padding: .2em; width: 300px; overflow: hidden; }
	    .ui-dialog .ui-dialog-titlebar { padding: .5em 1em .3em; position: relative;  }
        .ui-dialog .ui-dialog-title { float: left; margin: .1em 16px .2em 0; } 
        .ui-dialog .ui-dialog-titlebar-close { position: absolute; right: .3em; top: 50%; width: 19px; margin: -10px 0 0 0; padding: 1px; height: 18px; }
        .ui-dialog .ui-dialog-titlebar-close span { display: block; margin: 1px; }
        .ui-dialog .ui-dialog-titlebar-close:hover, .ui-dialog .ui-dialog-titlebar-close:focus { padding: 0; }
        .ui-dialog .ui-dialog-content { position: relative; border: 0; padding: .5em 1em; background: none; overflow: auto; zoom: 1; }
        .ui-dialog .ui-dialog-buttonpane { text-align: right; border-width: 1px 0 0 0; background-image: none; margin: .5em 0 0 0; padding: .3em 1em .5em .4em; }
        .ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset { float: right; }
        .ui-dialog .ui-dialog-buttonpane button { margin: 0 5px; cursor: pointer; }
        .ui-dialog .ui-resizable-se { width: 14px; height: 14px; right: 3px; bottom: 3px; }
        .ui-draggable .ui-dialog-titlebar { cursor: move; }

        .ui-dialog-titlebar{
            background: #C3D9FF none repeat scroll 0% 0%;
        }

        .ui-dialog{
            background: #E8EEF7;
        }

        .ui-dialog-buttonpane{
            text-align: right;
            background: transparent;
        }


        .ui-dialog-buttonpane button{
            padding: 10px;
            margin: 0 5px;
            font-weight: bold;
            color: black;
            background: #C3D9FF;
        }

    </style>
</head>
<body onload=ira() bgcolor=#FFFFFF oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">


<script type="text/javascript">


    // jQuery(document).ready(function($){

    //     $(document).on('click','.dvModalAltas button',function(e){
    //         e.preventDefault();
    //         // 
    //         debugger;
    //         var respuestaUsu = jQuery('.ui-state-default.ui-corner-all.ui-state-hover.ui-state-focus').text();
    //     });


    // });

    function resolverCargo( nhis, ning ){
    //  document.forms.listas.his.value = nhis;
    //  document.forms.listas.ing.value = ning;
    //  document.forms.listas.resolver.value = 2;
        document.forms.listas.submit();
    }

    function enter()
    {
        document.forms.listas.submit();
    }


    function cerrarVentana()
     {
      top.close()
     }


     function addHidden(theForm, key, value) {
        // Create a hidden input element, and append it to the form:
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = key; // 'the key/name of the attribute/field that is sent to the server
        input.value = value;
        theForm.appendChild(input);
    }

    function modalAltas( cmp, estadoCargos, num, posicion ){
        
        if( estadoCargos )
        {

            var msg = "Existen cargos en un estado diferente a Procesado, desea consultar en Unix?";

            
            

            function consultarUnix( resp ){
                var wemp_pmla = $("#wemp_pmla").val();
                var ok = $("#ok").val();
                var wcco = $("#wcco").val();
                // $.post("Listas.php",
                // {
                //     wemp_pmla			: wemp_pmla,
                //     ok                  : ok,
                //     wcco                : wcco,
                //     respuestaUsuario    : resp,
                //     posicion            : posicion,
                //     num                 : num,
                // }
                // ,function(data) {
                //     console.log(data);
                // },"json" );
                debugger;

                var listaForm = document.forms['listas'];
                addHidden(listaForm, 'posicion', posicion);
                addHidden(listaForm, 'num', num);
                // document.querySelector("form").reset();
                // listaFom.submit();
                document.forms.listas.submit();
            }
        
            $( "<div style='color: black;font-size:12pt;height: 250px;' title='CONSULTA EN UNIX' class='dvModalAltas'>"+msg+"</div>" ).dialog({
                width		: 550,
                height		: 100,
                modal		: true,
                resizable	: false,
                buttons	: {
                    "Si": function() {
                            cmp.checked = false;
                            cmp.value = 'on';
                            consultarUnix( true );
                            $( this ).dialog( "close" );
                            $( cmp ).css({display:""});
                        },
                    "No": function() {
                            cmp.checked = true;
                            cmp.value = 'off';
                            consultarUnix( false );
                            $( this ).dialog( "close" );
                            $( cmp ).css({display:"none"});
                        },
                    "Cancelar": function() {
                            cmp.checked = false;
                            cmp.value = '';
                            $( this ).dialog( "close" );
                        },
                },
            });
        
            $( ".dvModalAltas" ).parent().css({
                left: ( $( window ).width() - 550 )/2,
                top : ( $( window ).height() - 100 )/2,
            });
        }
        else
        {
            document.forms.listas.submit();
        }
    }

</script>
<?php
include_once("conex.php");
/**********************************************************************************************************************
       PROGRAMA : listas.php
       Fecha de Liberación : 2007-06-05
       Autor : Ing. Pedro Ortiz Tamayo
       Version Actual : 2011-12-28

       OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite obtener informacion a traves
       de listas de pacientes que estan en proceso de alta (Opcion 99), Pacientes con factura pendientes de pago,
       (Opcion 98) y pacientes en alta administrativa (opcion 97).

       OPCION 99 PACIENTES QUE ESTAN EN PROCESO DE ALTA: El programa valida que el paciente no tenga saldo o devoluciones
           pendientes y que la grabacion de medicamentos en la aplicacion de Servinte haya sido exitosa. Si las condiciones
           anteriores se dan, los datos del paciente son grabados en la tabla 22 (Cuentas a Caja) con las opciones de generacion
           de factura Cuegen en off y cancelacion de factura Cuepag en off.
           En esta opcion la lista muestra los pacientes que aparecen en la tabla 22 con la opcion de generacion de factura
           Cuegen en off y da la opcion de digitar el numero de factura y prender Cuegen para opcion de PACIENTE FACTURADO
           PENDIENTE DE PAGO o paciente sin cuentas pendientes para ALTA ADMINISTRATIVA.
           En esta opcion igualmente se permite digitar observaciones y comentarios por cada paciente en la lista.

       OPCION 98 PACIENTES CON FACTURA PENDIENTES DE PAGO: Esta lista muestra los pacientes que fueron facturados en el piso
           y estan pendientes de pago Opcion (Cuepag en off). Una vez el responsable de la cuenta cancela el indicador Cuepag
           se coloca en on y desaparece de la lista.

       OPCION 97 PACIENTES EN ALTA ADMINISTRATIVA: Esta lista muestra los pacientes que fueron facturados en el piso
           y pagaron en caja con indicador (ubiald en off). Esta lista solamente es informativa. Una vez el paciente se coloca
           en alta definitiva el indicador Ubiald se coloca en on y desaparece de la lista.


       REGISTRO DE MODIFICACIONES :
       .2022-01-05 - Juan Rodriguez: Se agrega model para que usuario decida si ingresar a unix o no, 
                        esto se hace a raiz de las frecuentes caidas de unix.
       .2019-02-19 - Arleyda I.C. Migración realizada
	     .2017-06-08 - Jonatan. Se valida que el paciente no tenga insumos pendientes para aplicar o devolver.
       .2017-02-09 - Arleyda I.C. Se agrega filtro en el campo ccoemp en caso de que el Query utilice la tabla costosyp_000005
       .2013-06-20 - Jonatan Lopez
            Se agrega la validacion de evoluciones pendientes para el paciente asi como se evaluan las glucomentrias, nebulizaciones,
            oxigenos, insumos y transfusiones.
	   .2013-05-02 - Edwin Molina G
            Se quita en el calculo de saldos los aprovechamientos en la consulta SQL, ya no se deben tener en cuenta y por php se hace
			un redondeo del resultado a 3 cifras decimales, esto para evitar negativos con cifras muy pequeñas que puedan ocurrir.
       .2012-11-21 - Jonatan Lopez
            Se agrega la validacion en la columna numero de factura para que verifique si aun tiene pendiente glucometrias, nebulizaciones
            oxigenos, insumos y transfusiones, en caso de ser asi no permitira generar facturacion, de las 7 AM a las 19 PM hara esta validacion,
            entre las 19 PM y las 7 AM el mensaje solo sera informativo y dejara generar factura.

        .2012-07-06 - Viviana Rodas
            Se agrega el include al comun.php, tambien se agrega la funcion consultaCentroCostos() que consulta los centros de costos
            dependiendo de los parametros que le ingresen y la funcion dibujarSelect() dibuja el select con los centros de costos enviados por los parametros, de igual forma se agregaron los estilos.

        .2012-06-22 - Viviana Rodas
            Se agregan las hojas de estilo nuevas de las aplicaciones de matrix.

       .2012-05-17
            Cuando se llama a la funcion ValidacionHistoriaUnix del include validacion_hist.php, la variable correspondiente a la
            conexiòn con unix no estaba seteada. Por tanto se crea la variable, y adicionalmente se incluyen fxValidacionArticulo.php y
            registro_tablas.php para que la funcion ValidacionHistoriaUnix continue correctamente.

       .2011-12-28
            Se modificaron en el programa para incluir el script validacion_hist.php para que el include otros.php no falle en
            determinadas condiciones.

       .2009-03-03
            Se modificaron en el programa todas las consultas a la base de datos que involucren las tablas 36 y 37 de root,
            para hacer el join por documento y tipo de documento. Esto con el proposito de que los querys sean mas eficientes.

       .2008-12-09
            Se modifico el programa para mostrar x la opcion 98 los pacientes que ya pagaron y los que estan pendientes de pago con el fin
            de poder controlar la entrega de paz y salvos a aquellos que no tienen cuentas pendientes. Los pacientes que cancelaron y se les
            entrega el paz y salvo se actualizan el la tabla 22 colocando en 'on' el campo Cuepgr que se agrego a esta tabla.

       .2007-11-22
            Se modifico el programa para actualizar en al tabla 22 (Cueffa, Cuehfa) la fecha y hora del momento de la generacion de la factura.

       .2007-10-22
            Se modificaron los tres querys de las listas para incluir la tabla 20 donde se encuentra el numero de la habitacion.

       .2007-10-20
            Se modifico el programa para validar sumas de saldos mayores a 0.0001.

       .2007-06-05
            Release de Versión Beta.



***********************************************************************************************************************/
include_once("movhos/otros.php");
include_once("movhos/validacion_hist.php");
include_once("movhos/fxValidacionArticulo.php");
include_once("movhos/registro_tablas.php");
include_once("root/comun.php");

if(!empty($respuestaUsuario)){
echo $respuestaUsuario;
}



$empresa = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wbasedato=$empresa;
/**
* --
*/


  
/**
 * Resuelva el caso para los medicamentos cargados despues de facturar
 *
 * @param $his      Historia
 * @param $ing      Ingreso
 * @param $user     Usuario
 * @param $obs      Observaciones
 * @return unknown_type
 */

// FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LOS SOPORTES RESPIRATORIOS //Enero 12/2012 Jonatan Lopez
function traer_evoluciones($wmovhos, $whis, $wing, $wemp_pmla)
    {

        global $conex;
        global $whce;
        $wevoluciones = 0;
        //Extrae el nombre del formulario donde se registran las evoluciones.
        $wform_evoluciones = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormularioEvoluciones');
        $wform_posicion_evo = explode("-", $wform_evoluciones); // Se explota el registro que esta separado por comas, para luego usarlo en el ciclo siguiente.
        $wformulario = $wform_posicion_evo[0];
        $wposicion = $wform_posicion_evo[1];
        // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS EVOLUCIONES GUARD PARA UNA HIST E INGRESO
        $query =     "  SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                        ."FROM ".$wmovhos."_000119 "
                        ."WHERE Glnhis = '".$whis."'
                            AND Glning = '".$wing."'
                            AND Glnind = 'E'
                            AND Glnest = 'on'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_evolucion = $rows['FechaHora'];

        //Consulta todos los especialistas que tienen el campo usures diferente de on, quiere decir los que son profesores y los que on tienen alumnos asignados,
        //hago la relacion de los codigos para extraer la especialidad, el nombre y el codigo de la especialidad.
        $query =    " SELECT usucod, usualu, u.descripcion, espmed.Medesp, nomesp.Espnom"
                    ."  FROM ".$whce."_000020 as usuhce
                        INNER JOIN
                        usuarios as u on (u.codigo = usuhce.Usucod )
                        INNER JOIN
                        ".$wmovhos."_000048 as espmed on (espmed.Meduma = usuhce.Usucod)
                        INNER JOIN
                        ".$wmovhos."_000044 as nomesp on (nomesp.Espcod = SUBSTRING_INDEX(espmed.Medesp, '-', 1))"
                    ." WHERE usures != 'on'";
        $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

        //Se crea por defecto la posicion medico de turno, para asociarlo a los alumnos que tienen mas de un profesor.
        $array_profesores = array(  'medico_turno'=> array(
                                                                'cuantos'=>0,
                                                    'nombre_especialista'=>'MEDICO DE TURNO',
                                                                'cod_esp'=>'medico_turno',
                                                    'nombre_especialidad'=>'MEDICO DE TURNO',
                                                                'alumnos'=>array()
                                                        )
                                    );

        $array_alumnos = array();
        //Al recorrer el resultado de la consulta se crea un arreglo $array_profesores[$row['usucod']][dato] y se agrega al arreglo $array_profesores[$row['usucod']]['alumnos'][],
        //todos los alumnnos asignados a el, solo se agregaran si la posicion $alumno del foreach es diferente de vacio y diferente de punto.
        while($row = mysql_fetch_array($res))
        {
            if(!array_key_exists($row['usucod'], $array_profesores))
            {
                $array_profesores[$row['usucod']] = array();
            }

            $array_profesores[$row['usucod']]['cuantos'] = 0;
            $array_profesores[$row['usucod']]['nombre_especialista'] = $row['descripcion'];
            $array_profesores[$row['usucod']]['cod_esp'] = $row['Medesp'];
            $array_profesores[$row['usucod']]['nombre_especialidad'] = $row['Espnom'];
            $explo_alum = explode(",", $row['usualu']);

            foreach ($explo_alum as $key => $alumno)
                {
                    $array_profesores[$row['usucod']]['alumnos'][] = $alumno;

                    //Solo se agregan los que tengan datos en la posicion $alumno and diferente de punto.
                    if(!empty($alumno) and $alumno != '.')
                        {
                        $array_alumnos[$alumno]['profesor'][] = $row['usucod'];
                        }
                }
        }

        //Consulta todas las  evoluciones que no se han registrado a partir de la ultima fecha y hora de registro
        //en la tabla 119 de movhos para la historia e ingreso y el parametro Glnind = 'E', se trae tambien el nombre, la especialidad y el codigo de la especialidad.
        $query =    " SELECT firusu, usuhce.usualu, COUNT(firusu) as cuantos, u.descripcion, usuhce.usures, ".$whce."_000036.Fecha_data as fechafir, ".$whce."_000036.Hora_data as horafir, "
                            ." ".$whce."_000036.firhis, ".$whce."_000036.firing, ".$whce."_000036.firrol "
                            ."  FROM ".$whce."_000036, ".$whce."_000020 as usuhce
                                INNER JOIN
                                usuarios as u on (u.codigo = usuhce.Usucod )"
                            ." WHERE Firhis = '".$whis."'"
                            ."   AND Firing = '".$wing."'"
                            ."   AND Firpro = '".$wformulario."'"
                            ."   AND Firfir = 'on'"
                            ."   AND firusu = usucod "
                            ."   AND u.Activo = 'A' "
                            ."   AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_evolucion."'"
                            ." GROUP BY firusu"
                            ." HAVING COUNT(*) > 0";
        $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

        while($row1 = mysql_fetch_array($res))
        {
            //Aqui solo permite ingresar si el usuario es residente, osea alumno.
            if($row1['usures'] == 'on')
            {
                //Verifica que en el array_alumnos se encuentre el codigo del alumno.
                if(array_key_exists($row1['firusu'], $array_alumnos))
                {

                    //Si un alumno tiene varios profesores, pondra como especialista la palabra medico de turno.
                    if (count($array_alumnos[$row1['firusu']]['profesor'])>1)
                    {
                        $wevoluciones += $row1['cuantos']*1;
                    }
                    else
                    {
                        $wevoluciones += $row1['cuantos']*1;
                    }


                }

            }
            //Si el usuario no es residente, entonces la informacion se mantendra como viene en el arreglo de profesores.
            else
            {
                $wevoluciones += $row1['cuantos']*1;
            }

        }


        return $wevoluciones;

}


// FUNCION PARA MOSTRAR LOS PENDIENTES DE GLUCOMETER POR COBRAR //09 DIC 2011 Jonatan Lopez
function traer_glucometer($wmovhos, $whis, $wing, $wemp_pmla)
        {

        global $conex;
        global $whce;

        $wglucometrias = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Glucometrias');

        // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS GLUCOMETRIAS GUARD PARA UNA HIST E INGRESO
        $query =     "SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                        ."FROM ".$wmovhos."_000119 "
                        ."WHERE Glnhis = '".$whis."'
                                AND Glning = '".$wing."'
                                AND Glnind = 'G'";

        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $fechamax_gluco ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_glu = $rows[0];


        //CANTIDAD DE GLUCOMETRIAS SIN GUARDAR
        $query = "SELECT COUNT(Firhis) "
                        ."FROM ".$whce."_000036 "
                        ."WHERE Firhis = '".$whis."'"
                        ."  AND Firing = '".$wing."'"
                        ."  AND Firpro = '".$wglucometrias."'"
                        ."  AND Firfir = 'on'"
                        ."  AND CONCAT( Fecha_data, ' ', Hora_data ) > '".$fechamax_glu."'";

        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $cantidadg = $rows[0];
        return $cantidadg;

        }


// FUNCION PARA MOSTRAR LOS PENDIENTES DE GLUCOMETER POR COBRAR //09 DIC 2011 Jonatan Lopez
function traer_nebulizaciones($wmovhos, $whis, $wing, $wemp_pmla, $wcco)
        {

        global $conex;


        $pos_str = explode("-",$wcco);
        $wcco = $pos_str[0];

        // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS NEBULIZACIONES GUARD PARA UNA HIST E INGRESO
        $query =    "SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                        ."FROM ".$wmovhos."_000119 "
                        ."WHERE Glnhis = '".$whis."'
                            AND Glning = '".$wing."'
                            AND Glnind = 'N'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_nebus = $rows[0];

       $query =  " SELECT COUNT(".$wmovhos."_000015.Aplcan) as nebus "
                    ."   FROM ".$wmovhos."_000091, ".$wmovhos."_000015"
                    ."  WHERE Aplart = Arscod "
                    ."    AND Arstip = 'N' "
                    ."    AND Aplhis = '".$whis."'"
                    ."    AND Apling = '".$wing."'"
                    ."    AND Aplcco = '" .$wcco."' "
                    ."    AND Arscco = Aplcco "
                    ."    AND Aplest = 'on'"
                    ."    AND CONCAT( ".$wmovhos."_000015.Fecha_data, ' ', ".$wmovhos."_000015.Hora_data ) > '".$fechamax_nebus."'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$datos_nebus." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $wnebulizaciones = $rows['nebus'];

        return $wnebulizaciones;

    }

    // FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA INSUMOS
function traer_insumos($wmovhos, $whis, $wing, $wemp_pmla)
        {

        global $conex;
        global $whce;
        $wcant_insumos = 0;

        $wforminsumos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormInsumos'); //Extrae el nombre del formulario para extraer los valores a cobrar.
        $wconfinsumos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ConfInsumos'); //Extrae el arreglo con dos numeros, el primero sirve para mostrar el nombre del
                                                                                        //articulo de la tabla hce_000002 y el segundo sirve para extraer la cantidad
                                                                                        //del campo movdat de la tabla hce_000205.
        $wcampos_desc = explode(";", $wconfinsumos); // Se explota el registro que esta separado por comas, para luego usarlo en el ciclo siguiente.
        $wcuantos = count($wcampos_desc);

        // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS GLUCOMETRIAS GUARD PARA UNA HIST E INGRESO
        $query =     "  SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                        ."FROM ".$wmovhos."_000119 "
                        ."WHERE Glnhis = '".$whis."'
                            AND Glning = '".$wing."'
                            AND Glnind = 'I'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_insumos = $rows[0];

        for($i = 0; $i <= ($wcuantos-1); $i++)
        {

            $wnombres_posicion = explode("-", $wcampos_desc[$i]); //Esta posicion se refiere a la cantidad y nombre del insumo, en el formulario 000205 de hce.

            //CANTIDAD DE OXIMETRIAS SIN GUARDAR
            $query =    "SELECT SUM(".$whce."_".$wforminsumos.".movdat)  "
                        ."FROM ".$whce."_000036, ".$whce."_".$wforminsumos
                        ." WHERE Firhis = '".$whis."'"
                        ."  AND Firing = '".$wing."'"
                        ."  AND Firhis = Movhis"
                        ."  AND Firing = Moving"
                        ."  AND Firpro = '".$wforminsumos."'"
                        ."  AND Firfir = 'on'"
                        ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wforminsumos.".Fecha_data"
                        ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wforminsumos.".Hora_data"
                        ."  AND movcon = '".$wnombres_posicion[1]."'" //Esta posicion se refiere a la cantidad, en el formulario 000205 de hce.
                        ."  AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_insumos."'";
        $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $wcant_insumos += $rows[0];

        }

        return $wcant_insumos;

        }


// FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LOS SOPORTES RESPIRATORIOS //Enero 12/2012 Jonatan Lopez
function traer_oximetrias($wmovhos, $whis, $wing, $wemp_pmla)
        {

        global $conex;
        global $whce;

        $woximetrias = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Soporterespiratorio');
        $wcampos = explode("*", $woximetrias);
        $wtablat = $wcampos[0];
        $wcampot = explode("-",$wcampos[1]);	//Si es UCI/UCE el consecutivo para los oxigenos es el 20.
		$consecutivos = implode("','",$wcampot);

        // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS TRANSFUSIONES GUARD PARA UNA HIST E INGRESO
        $query =     "SELECT MAX(CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                        ."FROM ".$wmovhos."_000119 "
                        ."WHERE Glnhis = '".$whis."'
                                AND Glning = '".$wing."'
                                AND Glnind = 'O'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_transf = $rows[0];

        //CANTIDAD DE OXIMETRIAS SIN GUARDAR
      $query =    "SELECT SUM(".$whce."_".$wtablat.".movdat)  "
                    ."FROM ".$whce."_000036, ".$whce."_".$wtablat
                    ." WHERE Firhis = '".$whis."'"
                    ."  AND Firing = '".$wing."'"
                    ."  AND Firhis = Movhis"
                    ."  AND Firing = Moving"
                    ."  AND Firpro = '".$wtablat."'"
                    ."  AND Firfir = 'on'"
                    ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wtablat.".Fecha_data"
                    ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wtablat.".Hora_data"
                    ."  AND movcon in ('".$consecutivos."')"
                    ."  AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_transf."'";
        $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $cantidadoxi = $rows[0];

        if ($cantidadoxi == '')
        {
            $cantidadoxi = 0;
        }

        return $cantidadoxi;

        }

// FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LAS TRANSFUSIONES //Enero 12/2012 Jonatan Lopez
function traer_transfusiones($wmovhos, $whis, $wing, $wemp_pmla)
        {

        global $conex;
        global $whce;

        $wtransfusiones = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Transfusiones');
        $wcampos = explode("-", $wtransfusiones);
        $wtablat = $wcampos[0];
        $wcampot = $wcampos[1];

        // CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS TRANSFUSIONES GUARD PARA UNA HIST E INGRESO
        $query =    "SELECT MAX(CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
                     ."FROM ".$wmovhos."_000119 "
                    ."WHERE Glnhis = '".$whis."'
                        AND Glning = '".$wing."'
                        AND Glnind = 'T'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $fechamax_transf = $rows[0];


        //CANTIDAD DE TRANSFUSIONES SIN GUARDAR
        $query =    "SELECT COUNT(DISTINCT (movdat))  "
                     ."FROM ".$whce."_000036, ".$whce."_".$wtablat
                   ." WHERE Firhis = '".$whis."'"
                    ."  AND Firing = '".$wing."'"
                    ."  AND Firhis = Movhis"
                    ."  AND Firing = Moving"
                    ."  AND Firpro = '".$wtablat."'"
                    ."  AND Firfir = 'on'"
                    ."  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wtablat.".Fecha_data"
                    ."  AND ".$whce."_000036.Hora_data = ".$whce."_".$wtablat.".Hora_data"
                    ."  AND movcon = '".$wcampot."'"
                    ."  AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_transf."'";
        $res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $cantidadtransf = $rows[0];
        return $cantidadtransf;

        }

// Funcion que validar si el paciente tiene saldo de insumos de enfermeria // 22 mayo de 2017
function traer_insumos_enfermeria($wmovhos, $whis, $wing, $wemp_pmla)
        {

        global $conex;

        $query =    "SELECT (SUM(Carcca) - SUM(Carcap) - SUM(Carcde)) AS saldo_insumos "
                     ."FROM ".$wmovhos."_000227 "
                    ."WHERE Carhis = '".$whis."'
                        AND Caring = '".$wing."'
						AND Carcca-Carcap-Carcde > 0
                        AND Carest = 'on'";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
        $rows = mysql_fetch_array($res);
        $saldo_insumos_enf = $rows['saldo_insumos'];
		
		
        return $saldo_insumos_enf;

        }


function resolverCargos( $his, $ing, $user, $obs ){

    if( !empty($his) && !empty($ing) ){

        global $empresa;
        global $conex_o;
        global $bd;
        global $conex;

        if( !empty($obs) ){
            $sql = "UPDATE
                        {$empresa}_000022
                    SET
                        cuegdf = 'off',
                        cueure = '$user',
                        cuehre = '".date("H:i:s")."',
                        cuefre = '".date("Y-m-d")."',
                        cueobs = TRIM( '\n\n' FROM CONCAT(cueobs, '\n\n', '$obs'))
                    WHERE
                        cuegdf = 'on'
                        AND cuehis = '$his'
                        AND cueing = '$ing'
                        AND cuegdf = 'on'
                    ";
        }
        else{
            $sql = "UPDATE
                        {$empresa}_000022
                    SET
                        cuegdf = 'off',
                        cueure = '$user',
                        cuehre = '".date("H:i:s")."',
                        cuefre = '".date("Y-m-d")."'
                    WHERE
                        cuegdf = 'on'
                        AND cuehis = '$his'
                        AND cueing = '$ing'
                        AND cuegdf = 'on'
                    ";
        }

        $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    }

}
/**
 * Esta función verifica si hay cargos con estado diferente a procesado
 * @param string $conex : Conexión
 * @param string $whis : Historia
 * @param string $wnin : Ingreso
 * 
 * @return true Si hay cargos con estado diferente a procesado
 * @return false Si no hay cargos con estado diferente a procesado
 */
function validarCargosDiferenteProcesado($conex,$whis,$wnin)
{
    global $empresa;

    $query = "SELECT * FROM ".$empresa."_000002
	    WHERE Fenhis = '".$whis."'
	    AND Fening = ".$wnin." 
	    AND ( Fenues <> 'P' OR (Fenues='P' and Fecha_data='".date('Y-m-d')."') )
	    AND Fenest =  'on'
	    ORDER BY Fencco, Fecha_data";

    $err=mysql_query($query,$conex);
    $num=@mysql_num_rows($err);

    if($num > 0){

        return '1';
    }
    else{
        return '0';
    }
}

function validar_medins($conex,$whis,$wnin, $wcontrol, $respuestaUsuario)
{
    global $empresa;
    global $conex_o;
    global $bd;

    $pac=array();
    $pac['his']=$whis;
    $pac['ing']=$wnin;
    $pac['permisoAlta']=false;
    $array=array();
    $conex_o=0;
    $bd=$empresa;
    connectOdbc($conex_o, "inventarios");
    actualizacionDetalleRegistros ($pac, $array, $respuestaUsuario );
    $query = "select sum((spamen + spauen) - (spamsa + spausa)) ";	//Mayo 2 de 2013
    $query .= " from ".$empresa."_000004 ";
    $query .= " where spahis = '".$whis."'";
    $query .= "   and spaing = '".$wnin."'";
    $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
    $nums = mysql_num_rows($err);
    if ($nums > 0)
    {
        $row = mysql_fetch_array($err);
        $suma = round( $row[0], 3 );	//Mayo 2 de 2013
    }
    if($suma < 0.0001)
		$suma = 0;

	$query = "select inghis, inging ";
    $query .= " from ".$empresa."_000016,".$empresa."_000018 ";
    $query .= " where inghis = '".$whis."'";
    $query .= "   and inging = '".$wnin."'";
    $query .= "   and inghis = ubihis";
    $query .= "   and inging = ubiing  ";
    $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
    $num = mysql_num_rows($err);

    $alta="false";

    if($pac['permisoAlta'])
        $alta="true";

    if($pac['permisoAlta'] and $num > 0 and $nums > 0 and $suma == 0)
    {
        $query = "lock table ".$empresa."_000022 LOW_PRIORITY WRITE, ".$empresa."_000020 LOW_PRIORITY WRITE ";
        $err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO DE CUENTAS A CAJA : ".mysql_errno().":".mysql_error());
        $fecha = date("Y-m-d");
        $hora = (string)date("H:i:s");

        //Verifica que no tenga cantidades pendientes por grabar en la entrega de turnos secretaria.
        if ($wcontrol == 0)
            {
            //Verificar si el registro ya existe
            $query = "select cuehis, cueing ";
            $query .= " from ".$empresa."_000022 ";
            $query .= " where cuehis = '".$whis."'";
            $query .= "   and cueing = '".$wnin."'";
            $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
            $num = mysql_num_rows($err);

            if ($num == 0 ) //aqui se evalua si el registro ya existe en la tabla 22 de movhos, si ya existe no permite realizar mas inserciones
                {
                $query = "insert ".$empresa."_000022 (medico,fecha_data,hora_data, Cuehis, Cueing, Cuefac, Cuegen, Cuepag, Cuefpa, Cuehpa, Cuecok, Cueobs, Cueffa, Cuehfa, Cuepgr, Seguridad) values ('";
                $query .=  $empresa."','";
                $query .=  $fecha."','";
                $query .=  $hora."','";
                $query .=  $whis."','";
                $query .=  $wnin."',";
                $query .=  "'0','off','off','0000-00-00','00:00:00','on','','0000-00-00','00:00:00','off'";
                $query .=  ",'C-".$empresa."')";
                $err1 = mysql_query($query,$conex) or die("ARCHIVO DE CUENTAS A CAJA : ".mysql_errno().":".mysql_error());

                $query = " UNLOCK TABLES";
                $err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
                }

                $validar_medins=0;

            }
            else
            {
             $validar_medins=4;
            }

         $query = " UNLOCK TABLES";
         $err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());

         return $validar_medins;



    }
    else
    {
        $validar_medins=0;
        if($alta == "false")
            $validar_medins += 1;
        if($num == 0 or $nums == 0 or $suma != 0)
            $validar_medins += 2;
        return $validar_medins;
    }




}

function grabar_factura($conex,$whis,$wnin,$wfac,$wtip,$wobs)
{

    global $empresa;

    if($wfac != "")
    {
        $fecha = date("Y-m-d");
        $hora = (string)date("H:i:s");
        switch ($wtip)
        {
            case 1:
                $query =  " update ".$empresa."_000022 set Cuefac='".$wfac."', Cuegen='on', Cueobs='".$wobs."', Cueffa='".$fecha."', Cuehfa='".$hora."' where Cuehis='".$whis."' and Cueing='".$wnin."'";
            break;
            case 2:
                $query =  " update ".$empresa."_000022 set Cuefpa='".$fecha."', Cuehpa='".$hora."', Cuepag='on', Cuefac='".$wfac."', Cuegen='on', Cueobs='".$wobs."', Cueffa='".$fecha."', Cuehfa='".$hora."' where Cuehis='".$whis."' and Cueing='".$wnin."'";
            break;
            case 3:
                $query =  " update ".$empresa."_000018 set Ubifad='".$fecha."', Ubihad='".$hora."', Ubiald='on' where Ubihis='".$whis."' and Ubiing='".$wnin."'";
                $err = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ALTAS");
                $query =  " update ".$empresa."_000022 set Cuefpa='".$fecha."', Cuehpa='".$hora."', Cuepag='on', Cuefac='".$wfac."', Cuegen='on', Cueobs='".$wobs."', Cueffa='".$fecha."', Cuehfa='".$hora."' where Cuehis='".$whis."' and Cueing='".$wnin."'";
            break;
        }
        $err = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO FACTURA");
    }
}

function grabar_pago($conex,$whis,$wnin,$wfac,$wpag)
{
    global $empresa;
    if($wpag == "off")
    {
        $fecha = date("Y-m-d");
        $hora = (string)date("H:i:s");
        $query =  " update ".$empresa."_000022 set Cuefpa='".$fecha."', Cuehpa='".$hora."', Cuepgr='on', Cuepag='on' where Cuehis='".$whis."' and Cueing='".$wnin."' and Cuefac='".$wfac."'";
    }
    else
        $query =  " update ".$empresa."_000022 set Cuepgr='on', Cuepag='on' where Cuehis='".$whis."' and Cueing='".$wnin."' and Cuefac='".$wfac."'";
    $err = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO PAGOS EN TABLA 22");
}

/********** comienza la aplicacion***********/
$wactualiz = "2022-01-05";

if(!isset($_SESSION['user']))
    echo "error";
else
{
    $key = substr($user,2,strlen($user));
    echo "<form name='listas' action='#' method=post>";

    $empresa = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    $whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HCE');

    echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
    echo "<center><input type='HIDDEN' name= 'wbasedato' value='".$empresa."'>";
    echo "<center><input type='HIDDEN' id='wemp_pmla' name= 'wemp_pmla' value='".$wemp_pmla."'>";
    echo "<input type='HIDDEN' name= 'codemp' value='".$wemp_pmla."'>";
    echo "<input type='HIDDEN' name= 'wlogo' value='".$wlogo."'>";
    echo "<input type='HIDDEN' id = 'ok' name= 'ok' value='".$ok."'>";
    echo "<input type='HIDDEN' id = 'wcco' name= 'wcco' value='".$wcco."'>";

    switch ($ok)
    {
        case 99:
            $wcontrol = '';
            //  if( !isset($resolver) ){
            //      echo "<input type='hidden' name = 'resolver' value='1'>";
            //  }
            //  else{
            //      echo "<input type='hidden' name = 'resolver' value='$resolver'>";
            //  }

            //  if( $resolver == 2 ){
            //      resolverCargos( $his, $ing, $key );
            //  }

            if( isset( $resolver ) ){
                for( $i = 0; $i <= count($wdata); $i++ ){
                    if( !empty($resolver[$i]) ){
                        if( !empty($wobs[$i]) ){
                            resolverCargos( $wdata[$i][0], $wdata[$i][1], $key, $wobs[$i] );
                        }
                        else{
                            resolverCargos( $wdata[$i][0], $wdata[$i][1], $key, '' );
                        }
                    }
                }
            }

            echo "<input type='hidden' name='his' value='0'>";
            echo "<input type='hidden' name='ing' value='0'>";

            echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
            if(!isset($wcco))
            {
                echo "<center><table border=0>";
                encabezado("PACIENTES EN PROCESO DE ALTA", $wactualiz, "clinica");

                $cco="Ccohos";
                $sub="off";
                $tod="";
                $ipod="off";
                //$cco=" ";
                $centrosCostos = consultaCentrosCostos($cco);
                echo "<table align='center' border=0 >";
                $dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);

                echo $dib;
                echo "</table>";

                echo "<tr class='fila1'><td colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
            }
            else
            {
                echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
                // echo "<meta http-equiv='refresh' content='60;url=/matrix/movhos/procesos/listas.php?ok=99&wemp_pmla=".$wemp_pmla."&codemp=".$wemp_pmla."&wcco=".$wcco."&wlogo=".$wlogo."'>";

                encabezado("PACIENTES EN PROCESO DE ALTA", $wactualiz, "clinica");

                echo "<table border=0 align=center id=tipo5>";
                ?>
                <script>
                    function ira(){document.listas.wfecha.focus();}
                </script>
                <?php
                if($wlogo == 1)
                    //echo "<tr><td><IMG SRC='/matrix/images/medical/movhos/logo_".$empresa.".png'></td></tr>";
                echo "<tr><td align=center colspan=4 class='encabezadoTabla'><b>PACIENTES EN PROCESO DE ALTA</td></tr>";
                if (!isset($wfecha))
                    $wfecha=date("Y-m-d");
                if(!isset($whis))
                    $whis="";
                if(!isset($wnin))
                    $wnin="";
                if(!isset($num))
                    $num=0;
                $year = (integer)substr($wfecha,0,4);
                $month = (integer)substr($wfecha,5,2);
                $day = (integer)substr($wfecha,8,2);
                $nomdia=mktime(0,0,0,$month,$day,$year);
                $nomdia = strftime("%w",$nomdia);
                $wsw=0;
                switch ($nomdia)
                {
                    case 0:
                        $diasem = "DOMINGO";
                        break;
                    case 1:
                        $diasem = "LUNES";
                        break;
                    case 2:
                        $diasem = "MARTES";
                        break;
                    case 3:
                        $diasem = "MIERCOLES";
                        break;
                    case 4:
                        $diasem = "JUEVES";
                        break;
                    case 5:
                        $diasem = "VIERNES";
                        break;
                    case 6:
                        $diasem = "SABADO";
                        break;
                }
                echo "<tr><td class='fila1' align=center><b>Fecha :</b></td>";
                echo "<td class='fila2' align=center><b>".$diasem."</b></td>";
                echo "<td class='fila2' colspan=2 align=center valign=center><input type='TEXT' name='wfecha' size=10 maxlength=10 readonly='readonly' value=".$wfecha." class=tipo6></td></tr>";
                echo "</table><br>";
                echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";

                for ($i=0;$i<$num;$i++)
                    if(isset($wf[$i]))
                    {
                        if($wf[$i] == 3)
                            grabar_factura($conex,$wdata[$i][0],$wdata[$i][1],"Urgencias",$wf[$i],$wobs[$i]);
                        else
                            grabar_factura($conex,$wdata[$i][0],$wdata[$i][1],$wfac[$i],$wf[$i],$wobs[$i]);
                    }
                for ($i=0;$i<$num;$i++)



                $wdata=array();


                //                  0       1       2       3       4       5       6       7       8       9      10         11                  12      13      14      15        16         17       18      19      20
                $query = "select Cuehis, Cueing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, costosyp_000005.Cconom, Ingres, Ingnre, Ubialp, Ubiptr, '1' as tipo, Ccourg, Ubihac, cuegdf, cuegen ";
                $query .= " from ".$empresa."_000022,".$empresa."_000018,root_000036,root_000037,costosyp_000005,".$empresa."_000016,".$empresa."_000011 ";
                $query .= " where Cuegen = 'off'  ";
                $query .= " and Cuehis = ubihis  ";
                $query .= " and Cueing = ubiing  ";
                $query .= " and ubiald = 'off'  ";
                $query .= " and ubialp = 'on'  ";
                $query .= " and ubisac = '".substr($wcco,0,strpos($wcco,"-"))."'";
                $query .= " and ubihis = orihis  ";
                $query .= " and ubiing = oriing  ";
                $query .= " and oriori = '".$wemp_pmla."'  ";
                $query .= " and oriced = pacced  ";
                $query .= " and oritid = pactid  ";
                $query .= " and ubisac = costosyp_000005.ccocod  ";
                $query .= " and ubihis = inghis ";
                $query .= " and ubiing = inging  ";
                $query .= " and ubisac = ".$empresa."_000011.Ccocod  ";
                $query .= " and costosyp_000005.ccoemp = '".$wemp_pmla."' ";
                $query .= " UNION ";
                //                  0       1       2       3       4       5       6       7       8       9      10             11                 12      13      14      15      16           17      18        19                  20
                $query .= " select ubihis, ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, costosyp_000005.Cconom, Ingres, Ingnre, Ubialp, Ubiptr, '2' as tipo, Ccourg, Ubihac, 'off' as cuegdf, 'off' as cuegen ";
                $query .= " from ".$empresa."_000018,root_000036,root_000037,costosyp_000005,".$empresa."_000016,".$empresa."_000011 ";
                $query .= " where ubiald = 'off'  ";
                $query .= " and ubialp = 'on'  ";
                $query .= " and ubisac = '".substr($wcco,0,strpos($wcco,"-"))."'";
                $query .= " and ubiing Not in (Select Cueing from ".$empresa."_000022 where Cuehis = ubihis and Cueing = ubiing)  ";
                $query .= " and ubihis = orihis  ";
                $query .= " and ubiing = oriing  ";
                $query .= " and oriori = '".$wemp_pmla."'  ";
                $query .= " and oriced = pacced  ";
                $query .= " and oritid = pactid  ";
                $query .= " and ubisac = costosyp_000005.ccocod  ";
                $query .= " and ubihis = inghis ";
                $query .= " and ubiing = inging  ";
                $query .= " and ubisac = ".$empresa."_000011.Ccocod  ";
                $query .= " and costosyp_000005.ccoemp = '".$wemp_pmla."' ";

                //2010-01-05
                $query .= " UNION ";
                //                  0       1       2       3       4       5       6       7       8       9      10         11                  12      13      14      15        16         17       18      19      20
                $query .= "select Cuehis, Cueing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, costosyp_000005.Cconom, Ingres, Ingnre, Ubialp, Ubiptr, '3' as tipo, Ccourg, Ubihac, cuegdf, cuegen ";
                $query .= " from ".$empresa."_000022,".$empresa."_000018,root_000036,root_000037,costosyp_000005,".$empresa."_000016,".$empresa."_000011 ";
                $query .= " where cuegdf = 'on'  ";
                $query .= " and Cuehis = ubihis  ";
                $query .= " and Cueing = ubiing  ";
                // $query .= " and ubiald = 'off'  ";
                //  $query .= " and ubialp = 'on'  ";
                $query .= " and ubisac = '".substr($wcco,0,strpos($wcco,"-"))."'";
                $query .= " and ubihis = orihis  ";
                $query .= " and ubiing = oriing  ";
                $query .= " and oriori = '".$wemp_pmla."'  ";
                $query .= " and oriced = pacced  ";
                $query .= " and oritid = pactid  ";
                $query .= " and ubisac = costosyp_000005.ccocod  ";
                $query .= " and ubihis = inghis ";
                $query .= " and ubiing = inging  ";
                $query .= " and ubisac = ".$empresa."_000011.Ccocod  ";
                $query .= " and costosyp_000005.ccoemp = '".$wemp_pmla."' ";
                $query .= " order by tipo,Cuehis ";
                //FIN 2010-01-05

                $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
                $num = mysql_num_rows($err);

                if ($num>0)
                {
                    echo "<table border=0 align=center id=tipo5>";
                    echo "<tr class='encabezadoTabla'><td align=center colspan=13>PACIENTES ACTIVOS</td></tr>";
                    echo "<tr class='encabezadoTabla'><td align=center>HISTORIA</td><td align=center>NRO. INGRESO</td><td align=center>NOMBRE</td><td align=center>CODIGO<BR>HABITACION</td><td align=center>SERVICIO</td><td align=center>RESPONSABLE</td><td align=center>DESCRIPCION</td><td align=center>NRO. FACTURA/<br> VALIDACION MED - INS</td><td align=center>OBSERVACIONES</td><td align=center>GRABAR FACTURA/<br>VALIDACION MED - INS/<br>EGRESO URGENCIAS</td></tr>";

                    //Indica si se pinta la fila de cargos despues de facturar
                    $filgen = '';   //nuevo 2009-01-07
                    for ($i=0;$i<$num;$i++)
                    {

                            $row = mysql_fetch_array($err);
                            $wmsginsumos = '';
                            $wmsgtransfusiones = '';
                            $wobservaciones = '';
                            $wmsgglucos = '';
                            $wporfacturar = array();
                            $wmsgnebus = '';
                            $wmsgoxigenos = '';
                            $wobservaciones_textarea = '';
                            $wmsgevoluciones = '';
                            $wcontrol[$i] = 0;
                            $whora = date("H:i:s");
                            $wevoluciones = traer_evoluciones($empresa, $row[0], $row[1], $wemp_pmla); //Trae las glucometrias pendientes por facturar.
                            $wglucomerias = traer_glucometer($empresa, $row[0], $row[1], $wemp_pmla); //Trae las glucometrias pendientes por facturar.
                            $winsumos = traer_insumos($empresa, $row[0], $row[1], $wemp_pmla); //Trae los insumos pendientes por facturar.
                            $wnebulizaciones = traer_nebulizaciones($empresa, $row[0], $row[1], $wemp_pmla, $wcco); //Trae las nebulizaciones pendientes por facturar.
                            $woximetrias = traer_oximetrias($empresa, $row[0], $row[1], $wemp_pmla); //Trae los oxigenos pendientes por facturar.
                            $wtrasfusiones = traer_transfusiones($empresa, $row[0], $row[1], $wemp_pmla); //Trae las transfusiones pendientes por facturar.
                            $winsumos_enfermeria = traer_insumos_enfermeria($empresa, $row[0], $row[1], $wemp_pmla); //Saldo de insumos cargados a los auxiliares.

                            $whorariolimite1 = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HorarioRestrcCargosSecretaria'); //Trae el horario inicial y final para mostrar el cajon de seleccion.
                            $whorariolimite = explode("-", $whorariolimite1);
                            $whoralimiteinicial = $whorariolimite[0]; //Hora inicial para mostrar la validacion como mensaje.
                            $whorariolimitefinal = $whorariolimite[1]; //Hora final para mostrar la validacion como mensaje.
                            $wformatohorainicial = $whoralimiteinicial.":00:00";
                            $wformatohorafinal = $whorariolimitefinal.":00:00";


                            //Validaciones para cada uno de los pendientes por facturar.
                        if ($wglucomerias > 0 )
                        {
                            $wporfacturar[$i] = "-GLUCOMETRIAS";
                            $wmsgglucos = $wporfacturar[$i];
                            $wcontrol[$i] = 1;
                        }

                        if ($winsumos > 0 )
                        {

                            $wporfacturar[$i] = "-INSUMOS";
                            $wmsginsumos = $wporfacturar[$i];
                            $wcontrol[$i] = 1;

                        }

                        if ($wnebulizaciones > 0 )
                        {
                            $wporfacturar[$i] = "-NEBULIZACIONES";
                            $wmsgnebus = $wporfacturar[$i];
                            $wcontrol[$i] = 1;
                        }

                        if($woximetrias > 0 )
                        {

                            $wporfacturar[$i] = "-OXIGENOS";
                            $wmsgoxigenos = $wporfacturar[$i];
                            $wcontrol[$i] = 1;

                        }

                        if ($wtrasfusiones > 0)
                        {
                            $wporfacturar[$i] = "-TRANSFUSIONES";
                            $wmsgtransfusiones = $wporfacturar[$i];
                            $wcontrol[$i] = 1;
                        }

                            //Validaciones para cada uno de los pendientes por facturar.
                        if ($wevoluciones > 0 )
                        {
                            $wporfacturar[$i] = "-EVOLUCIONES";
                            $wmsgevoluciones = $wporfacturar[$i];
                            $wcontrol[$i] = 1;
                        }
                        
                        //Saldo insumos enfermeria auxiliares.
                        if ($winsumos_enfermeria > 0 )
                        {
                            $wporfacturar[$i] = "-APLICACION O DEVOLUCION DE INSUMOS AL BOTIQUIN";
                            $wmsginsumosenferm = $wporfacturar[$i];
                            $wcontrol[$i] = 1;
                        }

                        if($posicion == $i)
                        {
                            $wval[$i] = "on";
                        }


                        //No permite facturar si hay algun dato por facturar desde las 7 AM hasta las 10 PM (root_000051(Detapl)= HorarioRestrcCargosSecretaria)
                        if (($whora > $wformatohorainicial or $whora < $wformatohorafinal) and $wcontrol[$i] > 0 and isset($wval[$i]))
                            {
                                $wporfacturar[$i] = "<hr>FALTA GRABAR ".$wmsgglucos.$wmsginsumos.$wmsgnebus.$wmsgoxigenos.$wmsgtransfusiones.$wmsgevoluciones.$wmsginsumosenferm;
                            }
                            //Muestra el mensaje de validacion de elementos pendientes y permite facturar si hay algun elemento por facturar desde
                            //las 10 PM hasta las 9 AM (root_000051(Detapl)= HorarioRestrcCargosSecretaria)
                        if(($whora < $wformatohorainicial or $whora > $wformatohorafinal) and $wcontrol[$i] > 0 and isset($wval[$i]))
                            {

                                $wporfacturar1 =  str_replace('<hr>','',$wporfacturar[$i]);  //Quita el hr para el horario de la noche.
                                $wobservaciones = "<font color='red'>".$wporfacturar1."</font>"; //Esta variable se imprime en el td de observaciones.
                                $wobservaciones_textarea = $wporfacturar1; // Esta variable se imprime en el textarea de observaciones.
                                $wporfacturar[$i] = '';
                                $wcontrol[$i] = 0;

                            }

                        //Esta variable de control == 0 evita mensajes de arreglos vacios para historias que no tengan nada pendiente por grabar.
                            if ($wcontrol[$i] == 0)
                            {
                                $wporfacturar[$i] = '';
                            }

                        $estadoCargos = validarCargosDiferenteProcesado($conex,$row[0],$row[1]);
                        if(isset($wval[$i])  )
                        {

                            $wres=array();
                            // $respuestaUsuario = $_POST['respuestaUsuario'];

                            $resultado=validar_medins($conex,$row[0],$row[1], $wcontrol[$i], $respuestaUsuario);

                            switch ($resultado)
                            {

                                case 0:
                                    $row[16] = 1;
                                break;

                                case 1:
                                    $wres[$i]="ERRORES EN GRABACION DE INSUMOS LLAMAR URGENTE A SERVICIO FARMACEUTICO";
                                break;
                                case 2:
                                    $wres[$i]="PACIENTE CON SALDO EN MEDICAMENTOS - DEVOLUCION PENDIENTE";
                                break;
                                case 3:
                                    $wres[$i]="ERRORES EN GRABACION DE INSUMOS LLAMAR URGENTE A SERVICIO FARMACEUTICO Y PACIENTE CON SALDO EN MEDICAMENTOS - DEVOLUCION PENDIENTE";
                                break;
                                case 4:
                                    $wres[$i] = '';
                                break;
                            }

                        }

                        $query = "select Habcod ";
                        $query .= " from ".$empresa."_000020 ";
                        $query .= " where Habhis = '".$row[0]."'  ";
                        $query .= " and Habing = '".$row[1]."'  ";
                        $err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
                        $num1 = mysql_num_rows($err1);
                        if($num1 > 0)
                        {
                            $row1 = mysql_fetch_array($err1);
                            $row[18]=$row1[0];
                        }
                        else
                            $row[18]="";
                        if($i % 2 == 0)
                        {
                            $tipo="tipo12";
                            $tipoA="tipo20";
                            $tipoB="tipo12A";
                        }
                        else
                        {
                            $tipo="tipo13";
                            $tipoA="tipo21";
                            $tipoB="tipo13A";
                        }

                        $wdata[$i][0]=$row[0]; //Historia
                        $wdata[$i][1]=$row[1]; //Ingreso

                        $nombre=$row[4]." ".$row[5]." ".$row[6]." ".$row[7];
                        echo "<input type='HIDDEN' name= 'wdata[".$i."][0]' value='".$wdata[$i][0]."'>";
                        echo "<input type='HIDDEN' name= 'wdata[".$i."][1]' value='".$wdata[$i][1]."'>";
                        echo "<input type='HIDDEN' name= 'num' value='".$num."'>";


                        // if($respuestaUsuario === "0" )
                        // {
                        //     echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[18]."</td><td id=".$tipo.">".$row[10]."-".$row[11]."</td><td id=".$tipo.">".$row[12]."</td><td id=".$tipo.">".$row[13]."</td><td id=".$tipo."></td><td id=".$tipoA."><textarea name='wobs[".$i."]' cols=60 rows=3 class=tipo3>$wobservaciones_textarea</textarea></td><td id=".$tipoA."><input type='RADIO' name='wf[".$i."]' value=3 onclick='enter()'>Egresar De Urgencias </td></tr>";
                        
                        // }


                        //Valida si ya puede generar factura y además si todo esta registrado desde la entrega de turno secretaria.
                        if($row[16] == 1 and $wcontrol[$i] == 0)
                            {

                            if($row[17] == "on")
                            {
                                //echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[10]."</td><td id=".$tipo.">".$row[11]."</td><td id=".$tipo.">".$row[12]."</td><td id=".$tipo.">".$row[13]."</td><td id=".$tipo."><input type='TEXT' name='wfac[".$i."]' size=10 maxlength=10 class=tipo6></td><td id=".$tipoA."><textarea name='wobs[".$i."]' cols=60 rows=3 class=tipo3></textarea></td><td id=".$tipoA."><input type='RADIO' name='wf[".$i."]' value=3 onclick='enter()'>Egreso Urgencias </td></tr>";
                                echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[18]."</td><td id=".$tipo.">".$row[10]."-".$row[11]."</td><td id=".$tipo.">".$row[12]."</td><td id=".$tipo.">".$row[13]."</td><td id=".$tipo."></td><td id=".$tipoA."><textarea name='wobs[".$i."]' cols=60 rows=3 class=tipo3>$wobservaciones_textarea</textarea></td><td id=".$tipoA."><input type='RADIO' name='wf[".$i."]' value=3 onclick='enter()'>Egresar De Urgencias </td></tr>";
                            }
                            else
                            {
                                echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[18]."</td><td id=".$tipo.">".$row[10]."-".$row[11]."</td><td id=".$tipo.">".$row[12]."</td><td id=".$tipo.">".$row[13]."</td><td id=".$tipo."><input type='TEXT' name='wfac[".$i."]' size=10 maxlength=10 class=tipo6></td><td id=".$tipoA."><textarea name='wobs[".$i."]' cols=60 rows=3 class=tipo3>$wobservaciones_textarea</textarea></td><td id=".$tipoA."><input type='RADIO' name='wf[".$i."]' value=1 onclick='enter()'>Pagar <input type='RADIO' name='wf[".$i."]' value=2 onclick='enter()'>Sin Pago</td></tr>";
                            }
                        }
                        elseif( $row[16] == 3)
                        {
                            
                            if( $filgen == '' ){
                                echo "<tr align=center><td colspan='10' style='background-color:#FFcc66'>PACIENTES CON CARGOS DESPUES DE FACTURAR</td></tr>";
                                $filgen = 1;
                            }

                            if($row[17] == "on")
                            {
                                //echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[10]."</td><td id=".$tipo.">".$row[11]."</td><td id=".$tipo.">".$row[12]."</td><td id=".$tipo.">".$row[13]."</td><td id=".$tipo."><input type='TEXT' name='wfac[".$i."]' size=10 maxlength=10 class=tipo6></td><td id=".$tipoA."><textarea name='wobs[".$i."]' cols=60 rows=3 class=tipo3></textarea></td><td id=".$tipoA."><input type='RADIO' name='wf[".$i."]' value=3 onclick='enter()'>Egreso Urgencias </td></tr>";
                                echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[18]."</td><td id=".$tipo.">".$row[10]."-".$row[11]."</td><td id=".$tipo.">".$row[12]."</td><td id=".$tipo.">".$row[13]."</td><td id=".$tipo."></td><td id=".$tipoA."><textarea name='wobs[".$i."]' cols=60 rows=3 class=tipo3></textarea></td><td id=".$tipoA."><input type='RADIO' name='wf[".$i."]' value=3 onclick='enter()'>Egresar De Urgencias</td></tr>";
                            }
                            else{

                                if( $row[19] == "on" ){
                                //  $class="style='background-color:#E8eeF7'";
                                    $class="style='background-color:#c3d9FF'";
                                }
                                else{
                                    $class="";
                                }
                                //  <input type='TEXT' name='wfac[".$i."]' size=10 maxlength=10 class=tipo6>
                                echo "<tr $class>
                                <td $class id=".$tipo.">".$row[0]."</td>
                                <td $class id=".$tipo.">".$row[1]."</td>
                                <td $class id=".$tipo.">".$nombre."</td>
                                <td $class id=".$tipo.">".$row[18]."</td>
                                <td $class id=".$tipo.">".$row[10]."-".$row[11]."</td>
                                <td $class id=".$tipo.">".$row[12]."</td>
                                <td $class id=".$tipo.">".$row[13]."</td>
                                <td $class id=".$tipo."></td>
                                <td $class id=".$tipoA."><textarea name='wobs[".$i."]' cols=60 rows=3 class=tipo3></textarea></td>
                                <td $class id=".$tipoA.">";

                                //  if( $row[20] == 'off')
                                    //  echo "<input type='RADIO' name='wf[".$i."]' value=1 onclick='enter()'>Pagar <input type='RADIO' name='wf[".$i."]' value=2 onclick='enter()'>Sin Pago";

                                if( $row[19] == "on" ){
                                    echo "<INPUT type='radio' name='resolver[$i]' onclick='javascript: resolverCargo( {$wdata[$i][0]}, {$wdata[$i][1]});'> Resuelto";
                                }
                                echo "</td></tr>";
                            }
                        }
                        else{
                            if(isset($wres[$i]) and isset($wporfacturar[$i])) //Si estan declaradas las dos variables se mostrará esta opción.
                            {

                                echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[18]."</td><td id=".$tipo.">".$row[10]."-".$row[11]."</td><td id=".$tipo.">".$row[12]."</td><td id=".$tipo.">".$row[13]."</td><td id=".$tipo."><input type='checkbox' name='wval[".$i."]' onclick='modalAltas(this, ".$estadoCargos.", ".$num.", ".$i.")'></td><td id=".$tipo.">".$wobservaciones."</td><td id=".$tipoB.">".$wres[$i]."<br>".$wporfacturar[$i]."</td></tr>";
                            }
                            else{
                                echo "<tr>";
                                echo "<td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[18]."</td><td id=".$tipo.">".$row[10]."-".$row[11]."</td><td id=".$tipo.">".$row[12]."</td><td id=".$tipo.">".$row[13]."</td><td id=".$tipo."><input type='checkbox' name='wval[".$i."]' onclick='modalAltas(this, ".$estadoCargos.", ".$num.", ".$i.")'></td><td id=".$tipo."></td><td id=".$tipo.">VALIDACION PENDIENTE";
                                echo "</td></tr>";
                            }
                        }

                    }
                }
                echo "</table></center>";
                echo "<table border=0 align=center id=tipo5>";
                echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
                if(isset($wcco))
                {
                echo "<tr><td align=center><A HREF='listas.php?ok=99&wemp_pmla=".$wemp_pmla."&wlogo=".$wlogo."'>Retornar</A></td></tr>";
                }
                echo "</table>";
            }
        break;
        case 98:
            echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
            echo "<meta http-equiv='refresh' content='60;url=/matrix/movhos/procesos/listas.php?ok=98&wemp_pmla=".$wemp_pmla."&codemp=".$wemp_pmla."&wlogo=".$wlogo."'>";
            echo "<table border=0 align=center id=tipo5>";
            ?>
            <script>
                function ira(){document.listas.wfecha.focus();}
            </script>
            <?php
            if($wlogo == 1)
                echo "<tr><td align=center colspan=4><IMG SRC='/matrix/images/medical/movhos/logo_".$empresa.".png'></td></tr>";

            echo "<tr><td align=center colspan=4 id=tipo14><b>PACIENTES FACTURADOS PENDIENTES DE PAGO</td></tr>";
            if (!isset($wfecha))
                $wfecha=date("Y-m-d");
            if(!isset($whis))
                $whis="";
            if(!isset($wnin))
                $wnin="";
            if(!isset($num))
                $num=0;
            $year = (integer)substr($wfecha,0,4);
            $month = (integer)substr($wfecha,5,2);
            $day = (integer)substr($wfecha,8,2);
            $nomdia=mktime(0,0,0,$month,$day,$year);
            $nomdia = strftime("%w",$nomdia);
            $wsw=0;
            switch ($nomdia)
            {
                case 0:
                    $diasem = "DOMINGO";
                    break;
                case 1:
                    $diasem = "LUNES";
                    break;
                case 2:
                    $diasem = "MARTES";
                    break;
                case 3:
                    $diasem = "MIERCOLES";
                    break;
                case 4:
                    $diasem = "JUEVES";
                    break;
                case 5:
                    $diasem = "VIERNES";
                    break;
                case 6:
                    $diasem = "SABADO";
                    break;
            }
            echo "<tr><td class='fila1' align=center><b>Fecha :</b></td>";
            echo "<td bgcolor='#cccccc' align=center><b>".$diasem."</b></td>";
            echo "<td bgcolor='#cccccc' colspan=2 align=center valign=center><input type='TEXT' name='wfecha' size=10 maxlength=10 readonly='readonly' value=".$wfecha." class=tipo6></td></tr>";
            echo "</table><br>";
            echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";

            for ($i=0;$i<$num;$i++)
                if(isset($wf[$i]))
                {
                    grabar_pago($conex,$wdata[$i][0],$wdata[$i][1],$wdata[$i][2],$wdata[$i][3]);
                }
            $wdata=array();

            //                  0       1       2       3       4       5       6       7       8       9      10      11      12      13      14      15      16      17      18      19
            $query = "select Cuehis, Cueing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, Cuefac, Cueobs, Ubihac, Cuepag ";
            $query .= " from ".$empresa."_000022,".$empresa."_000018,root_000036,root_000037,costosyp_000005,".$empresa."_000016 ";
            $query .= " where Cuegen = 'on'  ";
            $query .= " and Cuepgr = 'off'  ";
            $query .= " and Cuehis = ubihis  ";
            $query .= " and Cueing = ubiing  ";
            //$query .= " and ubiald = 'off'  ";
            $query .= " and ubihis = orihis  ";
            $query .= " and ubiing = oriing  ";
            $query .= " and oriori = '".$codemp."'  ";
            $query .= " and oriced = pacced  ";
            $query .= " and oritid = pactid  ";
            $query .= " and ubisac = ccocod  ";
            $query .= " and ubihis = inghis ";
            $query .= " and ubiing = inging  ";
            $query .= " and costosyp_000005.ccoemp = '".$wemp_pmla."' ";
            $query .= " order by Ubisac,Pacced ";
            $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
            $num = mysql_num_rows($err);
            if ($num>0)
            {
                echo "<table border=0 align=center id=tipo5>";
                echo "<tr class='encabezadoTabla'><td align=center colspan=14>PACIENTES ACTIVOS</td></tr>";
                echo "<tr class='encabezadoTabla'><td align=center>HISTORIA</td><td align=center>NRO. INGRESO</td><td align=center>TIPO<BR>DOCUMENTO</td><td align=center>IDENTIFICACION</td><td align=center>NOMBRE</td><td align=center>CODIGO<BR>HABITACION</td><td align=center>SERVICIO</td><td align=center>RESPONSABLE</td><td align=center>DESCRIPCION</td><td align=center>NRO. FACTURA</td><td align=center>OBSERVACIONES</td><td align=center>PAGO O<br>PAGARE</td><td align=center>ESTADO</td></tr>";
                for ($i=0;$i<$num;$i++)
                {
                    $row = mysql_fetch_array($err);
                    $query = "select Habcod ";
                    $query .= " from ".$empresa."_000020 ";
                    $query .= " where Habhis = '".$row[0]."'  ";
                    $query .= " and Habing = '".$row[1]."'  ";
                    $err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
                    $num1 = mysql_num_rows($err1);
                    if($num1 > 0)
                    {
                        $row1 = mysql_fetch_array($err1);
                        $row[18]=$row1[0];
                    }
                    else
                        $row[18]="";
                    if($row[19] == "on")
                    {
                        $Pago="NO PAGA";
                        $tipo="tipo15";
                    }
                    else
                        if($i % 2 == 0)
                        {
                            $Pago="";
                            $tipo="tipo12";
                        }
                        else
                        {
                            $Pago="";
                            $tipo="tipo13";
                        }
                    $wdata[$i][0]=$row[0];
                    $wdata[$i][1]=$row[1];
                    $wdata[$i][2]=$row[16];
                    $wdata[$i][3]=$row[19];
                    $nombre=$row[4]." ".$row[5]." ".$row[6]." ".$row[7];
                    echo "<input type='HIDDEN' name= 'wdata[".$i."][0]' value='".$wdata[$i][0]."'>";
                    echo "<input type='HIDDEN' name= 'wdata[".$i."][1]' value='".$wdata[$i][1]."'>";
                    echo "<input type='HIDDEN' name= 'wdata[".$i."][2]' value='".$wdata[$i][2]."'>";
                    echo "<input type='HIDDEN' name= 'wdata[".$i."][3]' value='".$wdata[$i][3]."'>";
                    echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
                    $path="/matrix/movhos/procesos/bitacora.php?ok=0&empresa=".$empresa."&wemp_pmla=".$wemp_pmla."&codemp=".$codemp."&whis=".$row[0]."&wnin=".$row[1]."";
                    echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$row[3]."</td><td id=".$tipo.">".$row[2]."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[18]."</td><td id=".$tipo.">".$row[10]."-".$row[11]."</td><td id=".$tipo.">".$row[12]."</td><td id=".$tipo.">".$row[13]."</td><td id=".$tipo.">".$row[16]."</td><td id=".$tipo.">".$row[17]."</td><td id=".$tipo."><input type='checkbox' name='wf[".$i."]' onclick='enter()'></td><td id=".$tipo.">".$Pago."</td></tr>";
                }
            }
            echo "</table></center>";
            echo "<table border=0 align=center id=tipo5>";
            echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
            if(isset($wcco))
            {
            echo "<tr><td align=center><A HREF='listas.php?ok=98&wemp_pmla=".$wemp_pmla."&wlogo=".$wlogo."'>Retornar</A></td></tr>";
            }
            echo "</table>";
        break;
        case 97:
            echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
            if(!isset($wcco))
            {
                echo "<center><table border=0>";

                encabezado("PACIENTES CON ALTA ADMINISTRATIVA", $wactualiz, "clinica");

                $cco="Ccohos";
                $sub="off";
                $tod="";
                $ipod="off";
                //$cco=" ";
                $centrosCostos = consultaCentrosCostos($cco);
                echo "<table align='center' border=0 >";
                $dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);

                echo $dib;
                echo "</table>";



                echo "</td></tr>";
                echo "<tr class='fila1'><td colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
            }
            else
            {
                echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
                echo "<meta http-equiv='refresh' content='60;url=/matrix/movhos/procesos/listas.php?ok=97&wemp_pmla=".$codemp."&empresa=".$empresa."&codemp=".$codemp."&wcco=".$wcco."&wlogo=".$wlogo."'>";

                encabezado("PACIENTES CON ALTA ADMINISTRATIVA", $wactualiz, "clinica");
                echo "<table border=0 align=center id=tipo5>";
                ?>
                <script>
                    function ira(){document.listas.wfecha.focus();}
                </script>
                <?php
                if($wlogo == 1)
                    //echo "<tr><td align=center colspan=4><IMG SRC='/matrix/images/medical/movhos/logo_".$empresa.".png'></td></tr>";
                echo "<tr><td align=right colspan=4><font size=2>Ver. 2011-12-28 </font></td></tr>";
                echo "<tr><td align=center colspan=4 class='encabezadoTabla'><b>PACIENTES CON ALTA ADMINISTRATIVA</td></tr>";
                if (!isset($wfecha))
                    $wfecha=date("Y-m-d");
                if(!isset($whis))
                    $whis="";
                if(!isset($wnin))
                    $wnin="";
                if(!isset($num))
                    $num=0;
                $year = (integer)substr($wfecha,0,4);
                $month = (integer)substr($wfecha,5,2);
                $day = (integer)substr($wfecha,8,2);
                $nomdia=mktime(0,0,0,$month,$day,$year);
                $nomdia = strftime("%w",$nomdia);
                $wsw=0;
                switch ($nomdia)
                {
                    case 0:
                        $diasem = "DOMINGO";
                        break;
                    case 1:
                        $diasem = "LUNES";
                        break;
                    case 2:
                        $diasem = "MARTES";
                        break;
                    case 3:
                        $diasem = "MIERCOLES";
                        break;
                    case 4:
                        $diasem = "JUEVES";
                        break;
                    case 5:
                        $diasem = "VIERNES";
                        break;
                    case 6:
                        $diasem = "SABADO";
                        break;
                }
                echo "<tr><td class='fila1' align=center><b>Fecha :</b></td>";
                echo "<td class='fila2' align=center><b>".$diasem."</b></td>";
                echo "<td class='fila2' colspan=2 align=center valign=center><input type='TEXT' name='wfecha' size=10 maxlength=10 readonly='readonly' value=".$wfecha." class=tipo6></td></tr>";
                echo "</table><br>";
                echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";

                //                  0       1       2       3       4       5       6       7       8       9      10      11      12      13      14      15      16      17      18
                $query = "select Cuehis, Cueing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, Cuefac, Cueobs, Ubihac ";
                $query .= " from ".$empresa."_000022,".$empresa."_000018,root_000036,root_000037,costosyp_000005,".$empresa."_000016 ";
                $query .= " where Cuepag = 'on'  ";
                $query .= " and Cuehis = ubihis  ";
                $query .= " and Cueing = ubiing  ";
                $query .= " and ubiald = 'off'  ";
                $query .= " and ubisac = '".substr($wcco,0,strpos($wcco,"-"))."'";
                $query .= " and ubihis = orihis  ";
                $query .= " and ubiing = oriing  ";
                $query .= " and oriori = '".$codemp."'  ";
                $query .= " and oriced = pacced  ";
                $query .= " and oritid = pactid  ";
                $query .= " and ubisac = ccocod  ";
                $query .= " and ubihis = inghis ";
                $query .= " and ubiing = inging  ";
                $query .= " and costosyp_000005.ccoemp = '".$wemp_pmla."' ";
                $query .= " order by Ubisac,Pacced ";

                $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
                $num = mysql_num_rows($err);
                if ($num>0)
                {
                    echo "<table border=0 align=center id=tipo5>";
                    echo "<tr class='encabezadoTabla'><td align=center colspan=13>PACIENTES ACTIVOS PARA ALTA INMEDIATA</td></tr>";
                    echo "<tr class='encabezadoTabla'><td align=center>HISTORIA</td><td align=center>NRO. INGRESO</td><td align=center>NOMBRE</td><td align=center>CODIGO<BR>HABITACION</td><td align=center>SERVICIO</td><td align=center>RESPONSABLE</td><td align=center>DESCRIPCION</td><td align=center>NRO. FACTURA</td><td align=center>OBSERVACIONES</td></tr>";
                    for ($i=0;$i<$num;$i++)
                    {
                        $row = mysql_fetch_array($err);
                        $query = "select Habcod ";
                        $query .= " from ".$empresa."_000020 ";
                        $query .= " where Habhis = '".$row[0]."'  ";
                        $query .= " and Habing = '".$row[1]."'  ";
                        $err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
                        $num1 = mysql_num_rows($err1);
                        if($num1 > 0)
                        {
                            $row1 = mysql_fetch_array($err1);
                            $row[18]=$row1[0];
                        }
                        else
                            $row[18]="";
                        if($i % 2 == 0)
                        {
                            $tipo="tipo12";
                        }
                        else
                        {
                            $tipo="tipo13";
                        }
                        $nombre=$row[4]." ".$row[5]." ".$row[6]." ".$row[7];
                        $path="/matrix/movhos/procesos/bitacora.php?ok=0&empresa=".$empresa."&wemp_pmla=".$wemp_pmla."&codemp=".$codemp."&whis=".$row[0]."&wnin=".$row[1]."";
                        echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[18]."</td><td id=".$tipo.">".$row[10]."-".$row[11]."</td><td id=".$tipo.">".$row[12]."</td><td id=".$tipo.">".$row[13]."</td><td id=".$tipo.">".$row[16]."</td><td id=".$tipo.">".$row[17]."</td></tr>";
                    }
                }
                echo "</table></center>";
            }
        break;
    }
    // echo "<table border=0 align=center id=tipo5>";
    // echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    // if(isset($wcco))
    // {
    // echo "<tr><td align=center><A HREF='listas.php?ok=99&wemp_pmla=".$wemp_pmla."&wlogo=".$wlogo."'>Retornar</A></td></tr>";
    // }
    // echo "</table>";


}
?>
</body>
</html>
