<?php
include_once("conex.php");
// session_start();
/**
 PROGRAMA                   : buscame.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 28 Mayo de 2012

 DESCRIPCION:
 Búscame, es un reporte que se encarga de buscar e identificar al empleado que se busca mediante su código o número de cédula de ciudadanía.
*/ $wactualiz = "2018-06-12"; /*
 ACTUALIZACIONES:

 - Junio 12 del 2018 - Arleyda Insignares C. : Se modifica consulta utilizando parametro en root_000051: 'contratoperiodo'. 
    Se utilizará unicamente un tipo de contrato (a termino fijo para renovación de contrato).   

 - Enero 10 del 2018 - Arleyda Insignares C. :
    Se adiciona control en el tipo de contrato para adicionar evaluaciones solo para empleados vinculados.
    Se cambia formato fecha para utilizar año con 4 digitos.
 
 - Diciembre 28 del 2017 - Arleyda Insignares C. :
    Se adiciona verificación de la primera evaluación cuando tiene contrato a termino fijo y ya existe en talhuma_000013. 

 - Octubre 24 del 2017 - Arleyda Insignares C. :
    Se modifica proceso para mostrar foto, validando que el empleado haya firmado o no el consentimiento. En caso de no, el sistema solo
    mostrará la foto a los coordinadores del proceso.

 - Septiembre 4 del 2017 - Arleyda Insignares C: 
    Se modifica el proceso de obtención del formato en la creación del empleado cuando no exista en talhuma_000013.
 
 - Mayo 5 del 2017  - Arleyda Insignares C:
    Se cambia ODBC para direccionar a nuevo programa SQL.
 
 - Arleyda Insignares C:
    * Cambio en busqueda para filtrar el centros de costos con el campo ccoemp
 
 - Julio 27 2015 Edwar Jaramillo:
    * Codificación utf8 al crear la respuesta html de la acción "buscar" (load).
 
 - Agosto 13 2013 Edwar Jaramillo:
    *Se modifica proceso ajax "actualizarDesdeUnix" para que se actualice el maestro de cargos de empleados (root_000079)
 
 - Noviembre 08 de 2012 Edwar Jaramillo:
    * Dentro del programa, antes se usaba el código de sesión del usuario logueado pero ahora debe ser de la forma "xxxxx-yy".
        Antes se estaban inicializando índices de varios array con códigos de usuarios de 5 dígitos, se debió modificar para que
        estos indices tengan también concatenados el código de la empresa a la que pertenecen. Este programa también hace búsquedas
        en la base de datos de Unix y se debió considerar que toda consulta a unix se hace concatenando "-01" al final de los códigos
        de usuarios que son leídos desde esa base de datos.
    * Se adicionan dos nuevos filtros de fechas para buscar las personas que ingresaron en determinadas fechas o que se retiraron en determinadas fechas.
 
 - Octubre 10 de 2012
    Edwar Jaramillo     : Documentación de código.

 -  Septiembre 06 de 2012
    Edwar Jaramillo     : Se modifica este archivo para que solamente se encargue de la búsqueda de empleados, ahora este archivo se puede ejecutar
                          totalmente independiente al archivo talento.php, es decir, el formulario de búsqueda ya no está dentro de talento.php como ántes
                          sino que ahora hace parte de buscame.php, y esa es ahora su función, solo buscar empleados. (Mostrar información por empleado o lista de empleados).
 -  Mayo 28 de 2012
    Edwar Jaramillo     : Fecha de la creación del reporte.

**/





include_once("funciones_talhuma.php");
include_once("root/comun.php");

$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

$user_session = explode('-',$_SESSION['user']);
$user_session = $user_session[1];
$user_session_wemp = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);

// $user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;


if(isset($accion) && isset($form)) // se debe diferenciar por los dos o por otro diferente a $accion puesto que desde talento.php ya esta seteado $accion
{
    // Hay momentos en los que se cierra la sesion por tiempo de espera, en este caso no se debería dejar modificar la caracterización
    // puesto que no hay sesion iniciada por el usuario y no es posible obtener el codigo del usuario. Cualquie modificación a la caracterización
    // no tendría registro de quién lo hizo.
    // if(!isset($_SESSION['user']) && !isset($accion))
    // {
        // $msj = '[?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.';
        // $data = array('error'=>1,'mensaje'=>$msj);
        // echo json_encode($data);
        // return;
    // }

    if(isset($accion) && $accion == 'validar_admin'){
        $data = array("error"=>0, "mensaje"=>"", "esAdmin"=>"off","usuario_logueado"=>$user_session_wemp);
        $permisoAdmin = consultarSiEsAdmin($conex, $wemp_pmla, $wtema, $wcodtab, $user_session);
        // print_r($permisoAdmin);
        if(count($permisoAdmin) > 0 && $permisoAdmin['esAdmin'] == 'on'){
            $data['esAdmin'] = $permisoAdmin['esAdmin'];
        }
        echo json_encode($data);
        return;
    }
    elseif(isset($accion) && $accion == 'actualizarDesdeUnix')
    {

        $wcontratos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'contratoperiodo');

        $data = array('error'=>0,'mensaje'=>'');

        $empleadoUnix = array();
        $cont_actualizados = 0;
        $cont_nuevos = 0;
        $cont_actualizados_eps = 0;
        $cont_nuevos_eps = 0;
        $cont_nuevos_cargos = 0;
        $cont_cargos_empleados = 0;
		$cont_nuevas_relaciones = 0;
		$cco_sin_coordina = '';
        
        // lee todos los usuario que estan en la base de datos de UNIX
        $odbc       = nominaEmpresa($wemp_pmla, $conex, $wbasedato);
        $conexunix = odbc_connect('queryx7','','') or die("No se ralizo Conexion con Oracle");

        // posibles valores nulos => Nombre 2, Apellido 2, Direccion, Fecha de retiro
        $q = "  SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, '' AS nombre2, perap1 AS apellido1, '' AS apellido2
                        , perfna AS f_nacimiento
                        , '' AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , to_date('0001-01-01', 'YYYY-MM-DD') AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NULL
                        AND perap2 IS NULL
                        AND perdir IS NULL
                        AND perret IS NULL

                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, '' AS nombre2, perap1 AS apellido1, '' AS apellido2
                        , perfna AS f_nacimiento
                        , '' AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , perret AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NULL
                        AND perap2 IS NULL
                        AND perdir IS NULL
                        AND perret IS NOT NULL

                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, '' AS nombre2, perap1 AS apellido1, '' AS apellido2
                        , perfna AS f_nacimiento
                        , perdir AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , to_date('0001-01-01', 'YYYY-MM-DD') AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NULL
                        AND perap2 IS NULL
                        AND perdir IS NOT NULL
                        AND perret IS NULL

                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, '' AS nombre2, perap1 AS apellido1, '' AS apellido2
                        , perfna AS f_nacimiento
                        , perdir AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , perret AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NULL
                        AND perap2 IS NULL
                        AND perdir IS NOT NULL
                        AND perret IS NOT NULL

                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, '' AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                        , perfna AS f_nacimiento
                        , '' AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , to_date('0001-01-01', 'YYYY-MM-DD') AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NULL
                        AND perap2 IS NOT NULL
                        AND perdir IS NULL
                        AND perret IS NULL

                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, '' AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                        , perfna AS f_nacimiento
                        , '' AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , perret AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NULL
                        AND perap2 IS NOT NULL
                        AND perdir IS NULL
                        AND perret IS NOT NULL

                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, '' AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                        , perfna AS f_nacimiento
                        , perdir AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , to_date('0001-01-01', 'YYYY-MM-DD') AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NULL
                        AND perap2 IS NOT NULL
                        AND perdir IS NOT NULL
                        AND perret IS NULL

                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, '' AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                        , perfna AS f_nacimiento
                        , perdir AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , perret AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NULL
                        AND perap2 IS NOT NULL
                        AND perdir IS NOT NULL
                        AND perret IS NOT NULL
                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, '' AS apellido2
                        , perfna AS f_nacimiento
                        , '' AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , to_date('0001-01-01', 'YYYY-MM-DD') AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NOT NULL
                        AND perap2 IS NULL
                        AND perdir IS NULL
                        AND perret IS NULL
                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, '' AS apellido2
                        , perfna AS f_nacimiento
                        , '' AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , perret AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NOT NULL
                        AND perap2 IS NULL
                        AND perdir IS NULL
                        AND perret IS NOT NULL
                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, '' AS apellido2
                        , perfna AS f_nacimiento
                        , perdir AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , to_date('0001-01-01', 'YYYY-MM-DD') AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NOT NULL
                        AND perap2 IS NULL
                        AND perdir IS NOT NULL
                        AND perret IS NULL
                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, '' AS apellido2
                        , perfna AS f_nacimiento
                        , perdir AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , perret AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NOT NULL
                        AND perap2 IS NULL
                        AND perdir IS NOT NULL
                        AND perret IS NOT NULL
                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                        , perfna AS f_nacimiento
                        , '' AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , to_date('0001-01-01', 'YYYY-MM-DD') AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NOT NULL
                        AND perap2 IS NOT NULL
                        AND perdir IS NULL
                        AND perret IS NULL
                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                        , perfna AS f_nacimiento
                        , '' AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , perret AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NOT NULL
                        AND perap2 IS NOT NULL
                        AND perdir IS NULL
                        AND perret IS NOT NULL
                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                        , perfna AS f_nacimiento
                        , perdir AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , to_date('0001-01-01', 'YYYY-MM-DD') AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NOT NULL
                        AND perap2 IS NOT NULL
                        AND perdir IS NOT NULL
                        AND perret IS NULL
                UNION

                SELECT  percod AS codigo
                        , perced AS cedula
                        , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                        , perfna AS f_nacimiento
                        , perdir AS direccion
                        , persex AS sex
                        , peretr AS estado
                        , percco AS ccosto
                        , perofi AS cod_cargo
                        , perfin AS f_ingreso
                        , perret AS f_retiro
                        , percot AS tipo_contrato
                FROM    noper
                WHERE   perno2 IS NOT NULL
                        AND perap2 IS NOT NULL
                        AND perdir IS NOT NULL
                        AND perret IS NOT NULL";

        $res = odbc_exec($conexunix,$q);//or die( odbc_error()." Query talento.php - $q - ".odbc_errormsg() );
        if(!$res) { $data['error'] = 1; $data['mensaje'] = 'No se pudo actualizar. Se produjo un inconveniente en la conexion.'; }
        else
        {
            while (odbc_fetch_row($res))
            {
                $codigo_un = odbc_result($res,'codigo').'-'.$wemp_pmla;

                $empleadoUnix[$codigo_un] = array(
                                        'wcodigo'   =>$codigo_un,
                                        'wced'      =>str_replace('.','',trim(odbc_result($res,'cedula'))),
                                        'wnombre1'  =>trim(odbc_result($res,'nombre1')),
                                        'wnombre2'  =>trim(odbc_result($res,'nombre2')),
                                        'wapellido1'=>trim(odbc_result($res,'apellido1')),
                                        'wapellido2'=>trim(odbc_result($res,'apellido2')),
                                        'wf_nace'   =>trim(odbc_result($res,'f_nacimiento')),
                                        //'telefono'  =>trim(odbc_result($res,'telefono')),
                                        'direccion' =>trim(odbc_result($res,'direccion')),
                                        'sex'       =>trim(odbc_result($res,'sex')),
                                        'westado'   =>trim(odbc_result($res,'estado')),
                                        'wccosto'   =>trim(odbc_result($res,'ccosto')),
                                        'wcodcargo' =>trim(odbc_result($res,'cod_cargo')),
                                        'wf_ingreso'=>trim(odbc_result($res,'f_ingreso')),
                                        'f_retiro'  =>trim(odbc_result($res,'f_retiro')),
                                        'tipo_contrato' => trim(odbc_result($res, 'tipo_contrato'))
                                    );
            }

            // Consulta solo los numeros telefonicos, validando que sea o no sea null y evitar que falle la consulta a unix.
           /* $qtelef = " SELECT  percod AS codigo
                                , 0 AS telefono
                        FROM    noper
                        WHERE   pertel IS NULL

                        UNION

                        SELECT  percod AS codigo
                                , pertel AS telefono
                        FROM    noper
                        WHERE   pertel IS NOT NULL";
			
            $resTelef = odbc_exec($conexunix,$qtelef)or die( odbc_error()." Query talento.php - $qtelef - ".odbc_errormsg() );

            while (odbc_fetch_row($resTelef))
            {
                $codigo_unx = odbc_result($resTelef,'codigo').'-'.$wemp_pmla;
                $empleadoUnix[$codigo_unx]['telefono'] = trim(odbc_result($resTelef,'telefono'));
            }*/

            // Lee todos los usuarios que estan en la base de datos Matrix - talhuma_000013
            $q = "  SELECT  Ideuse AS codigo, Ideced AS cedula, Ideest AS estado
                    FROM    ".$wbasedato."_000013";
            $res = mysql_query($q,$conex);

            $empleadoMatrix = array();
            while ($row = mysql_fetch_array($res))
            {
                $empleadoMatrix[$row['codigo']] = array(
                                        'wcodigo'   =>$row['codigo'],
                                        'wced'      =>$row['cedula'],
                                        'westado'   =>$row['estado']
                                    );
            }

            // $fp = fopen('insertados_de_unix.txt',"w+");
            if (count($empleadoUnix) >= 1)
            {

                $temaevaluacion    = '';
                $formatoevaluacion = '';

                $qtevaluacion= " SELECT Mtefor, Mtetem "
                              ."   FROM ".$wbasedato."_000059 "
                              ."  WHERE Mtecon = '2' ";

                $resteval = mysql_query($qtevaluacion,$conex) or die("Error: " . mysql_errno() . " - en el query (Select En '".$wbasedato."'_000059 por primera vez - generado desde actualizar matrix desde talento.php): " . $qtevaluacion . " - " . mysql_error());

                $rowteval = mysql_fetch_array($resteval);

                $temaevaluacion    = $rowteval['Mtetem'] ;
                $formatoevaluacion = $rowteval['Mtefor'] ;

                /* Busca si tiene email y extensión asociada en la tabla de requerimientos */
                $qUsu= "SELECT  Usucod, Usuext, Usuema
                        FROM    root_000039";
                $rUsu = mysql_query($qUsu,$conex);

                $usuExtEml = array();
                while ($row = mysql_fetch_array($rUsu))
                {
                    $row['Usucod'] = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $row['Usucod']);
                    $usuExtEml[$row['Usucod']] = array('Usuext' => $row['Usuext'],'Usuema' => $row['Usuema']);
                }

                foreach($empleadoUnix as $cod_use => $value)
                {

                    $unix = $empleadoUnix[$cod_use];
                    $usu = array('Usuext'=>'','Usuema'=>'');
                    $f_retiro = trim($unix['f_retiro']);

                    if ($f_retiro == '0001-01-01 00:00:00') { $f_retiro = ''; }

                    if (array_key_exists($cod_use,$usuExtEml)) { $usu = $usuExtEml[$cod_use]; }

                    if (array_key_exists($cod_use,$empleadoMatrix))
                    {
                        
                        $wperano = substr(date("Ymd",strtotime($unix['wf_ingreso'])),0,4);  
                        
                        $updEstado = (trim($unix['westado']) == 'A') ? 'on': 'off';

                        $update = " UPDATE  ".$wbasedato."_000013 SET
                                                Idecco = '".trim($unix['wccosto'])."',
                                                Ideccg = '".trim($unix['wcodcargo'])."',
                                                Ideest = '".$updEstado."',
                                                Idefre = '".$f_retiro."',
                                                Idetco = '".trim($unix['tipo_contrato'])."',
                                                Idefin = '".trim($unix['wf_ingreso'])."'
                                        WHERE   Ideuse = '".trim($unix['wcodigo'])."' ";

                        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$update." - ".mysql_error());
                        $cont_actualizados++;

                        // Verificar si tiene la primera evaluacion y la fecha de ingreso es del presente año                       

                        $anohoy = date("Y");

                        
                        if ($wperano == $anohoy && (trim($unix['tipo_contrato']) == trim($wcontratos) ) ){
                           
                            //Verificar que tenga la primera evaluación, en caso de que no, deberá ser ingresada
                            $selecteval = "SELECT  Arecdr, Arecdo 
                                             FROM ".$wbasedato."_000058 
                                             WHERE  Arecdo = '".trim($unix['wcodigo'])."' ";

                            $resfor = mysql_query($selecteval,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$selecteval." - ".mysql_error());

                            $numrowfor = mysql_num_rows($resfor);
                
                            //-----------------------------------

                            if( $numrowfor == 0){

                                // $query que trae coordinador
                                $query = " SELECT DISTINCT(Ajeucr) AS coordinador
                                           FROM ".$wbasedato."_000008 
                                           WHERE Ajeccc LIKE '%".$unix['wccosto']."%' 
                                             AND Ajecoo = 'on' ";

                                $res = mysql_query($query,$conex) or die("Error: " . mysql_errno() . " - en el query (select En '".$wbasedato."'_000008 '".$unix['wccosto']."' buscando coordinador ): ".$query." - " . mysql_error());

                                $num = mysql_num_rows($res);

                                if ($num > 0)
                                {
                                    $row = mysql_fetch_array($res);

                                    $ucordinador = $row['coordinador'];
                                    
                                    // Verificar si posee relacion con su respectivo jefe                                    
                                    $selectrel = "SELECT  Ajeuco
                                                     FROM ".$wbasedato."_000008 
                                                     WHERE  Ajeuco = '".trim($unix['wcodigo'])."' ";

                                    $resrel = mysql_query($selectrel,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$selectrel." - ".mysql_error());

                                    $numrowrel = mysql_num_rows($resrel);

                                    if( $numrowrel == 0){

                                        // query que relaciona a una persona nueva con su respectivo coordinador de unidad
                                        $quey = "INSERT INTO ".$wbasedato."_000008
                                                    (   Medico, Fecha_data, Hora_data, Ajeucr, Ajeccr, Ajeuco, Ajecco,Forest,Seguridad)
                                                 VALUES
                                                    (   '".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$ucordinador."','".$unix['wccosto']."','".$unix['wcodigo']."','".$unix['wccosto']."','".$updEstado."','C-kron')";

                                        $res = mysql_query($quey,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En '".$wbasedato."'_000008 por primera vez - generado desde actualizar matrix desde talento.php): " . $quey . " - " . mysql_error());
                                        
                                        $cont_nuevas_relaciones = $cont_nuevas_relaciones + 1;

                                    }

                                    $fecingunix = $unix['wf_ingreso'];
                                    $wdia       = date("d",strtotime($unix['wf_ingreso']));
                                    $wperiodo   = date("m",strtotime($unix['wf_ingreso']));

                                    if ($wdia <= 10 )
                                        $wperiodo = $wperiodo - 1;

                                        $q  = "SELECT Forcod,Fordes,Forper,Formpe,Fortes,Fortco "
                                             ."  FROM ".$wbasedato."_000042 "
                                             ." WHERE  Forcod ='".$temaevaluacion."' ";

                                        $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                                        $row = mysql_fetch_array($res);

                                        $maxperiodos   = $row['Formpe'];
                                        $temasiguiente = $row['Fortes'];
                                        $periodicidad  = $row['Forper'];

                                        $wnperiodo = ($wperiodo * 1)  + ($periodicidad * 1);

                                        if ( $wnperiodo > 12){

                                             $wnperiodo = $wnperiodo -12;
                                             $wnano = $wperano + 1;
                                        }else
                                             $wnano = $wperano;
                                        

                                        $query = "INSERT INTO ".$wbasedato."_000058
                                                    (   Medico, Fecha_data, Hora_data, Arecdr, Arecdo, Aretem, Arefor, Areper, Areano, Areest, Seguridad )
                                                 VALUES
                                                    (   '".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$ucordinador."','".$unix['wcodigo']."' ,
                                                        '".$temaevaluacion."' , '".$formatoevaluacion."' , '".$wnperiodo."' , '".$wnano."' , 'on', 'C-kron' )";

                                        $res = mysql_query($query,$conex) or die("Error: " . mysql_errno() . " - en el query (Insert En '".$wbasedato."'_000058   ): ".$query." - " . mysql_error());


                                }

                            }
                           
                        }
                        

                    }
                    else
                    {
                        $istEstado = (trim($unix['westado']) == 'A') ? 'on': 'off';

                        $tipo_contrato=trim($unix['tipo_contrato']);

                        $insert = " INSERT INTO ".$wbasedato."_000013
                                        (   Medico, Fecha_data, Hora_data, Idefnc, Idegen,
                                            Ideno1, Ideno2, Ideap1, Ideap2, Idecco ,Ideccg,
                                            Ideced, Idedir, Idetel, Ideeml, Ideext, Idefin,
                                            Idefre, Ideuse, Ideest, Idetco, Idecer, Seguridad)
                                    VALUES
                                        (   '".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$unix['wf_nace']."','".$unix['sex']."',
                                            '".$unix['wnombre1']."','".$unix['wnombre2']."','".$unix['wapellido1']."','".$unix['wapellido2']."',
                                            '".$unix['wccosto']."','".$unix['wcodcargo']."',
                                            '".$unix['wced']."','".$unix['direccion']."','".$unix['telefono']."','".$usu['Usuema']."','".$usu['Usuext']."','".$unix['wf_ingreso']."','".$f_retiro."','".$unix['wcodigo']."','".$istEstado."','".$tipo_contrato."', 'on' ,'C-".$user_session."')";

                        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En '".$wbasedato."'_000013 por primera vez - generado desde actualizar matrix desde talento.php): " . $insert . " - " . mysql_error());
                        $cont_nuevos++;

						$ucordinador ='';

                        // $query que trae coordinador
                        $query = "SELECT DISTINCT(Ajeucr) AS coordinador "
                                ."  FROM ".$wbasedato."_000008 "
                                ." WHERE Ajeccc LIKE '%".$unix['wccosto']."%' "
                                ."   AND Ajecoo = 'on' ";


                        $res = mysql_query($query,$conex) or die("Error: " . mysql_errno() . " - en el query (select En '".$wbasedato."'_000008 '".$unix['wccosto']."' buscando coordinador ): ".$query." - " . mysql_error());

						$num = mysql_num_rows($res);

						if($num > 0)
						{
    							$row = mysql_fetch_array($res);
    							$ucordinador = $row['coordinador'];

    							// query que relaciona a una persona nueva con su respectivo coordinador de unidad
    							$quey = "INSERT INTO ".$wbasedato."_000008
    										(   Medico, Fecha_data, Hora_data, Ajeucr, Ajeuco, Forest)
    									 VALUES
    										(   '".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$ucordinador."','".$unix['wcodigo']."','".$istEstado."' )";
    							$res = mysql_query($quey,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En '".$wbasedato."'_000008 por primera vez - generado desde actualizar matrix desde talento.php): " . $quey . " - " . mysql_error());
    							
                                $cont_nuevas_relaciones = $cont_nuevas_relaciones +1;

                                if ( trim($tipo_contrato) == trim($wcontratos) ){

                                    $fecingunix = $unix['wf_ingreso'];
                                    $wdia       = date("d",strtotime($unix['wf_ingreso']));
                                    $wperiodo   = date("m",strtotime($unix['wf_ingreso']));
                                    //$wperano  = date("y",strtotime($unix['wf_ingreso']));   
                                    $wperano    = substr(date("Ymd",strtotime($unix['wf_ingreso'])),0,4);

                                    if ($wdia <= 10 )
                                        $wperiodo = $wperiodo - 1;    

    								$q  = "SELECT Forcod,Fordes,Forper,Formpe,Fortes,Fortco "
    									 ."  FROM ".$wbasedato."_000042 "
    									 ." WHERE  Forcod ='".$temaevaluacion."' ";
    								$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


    								$row = mysql_fetch_array($res);

    								$maxperiodos   = $row['Formpe'];
    								$temasiguiente = $row['Fortes'];
    								$periodicidad  = $row['Forper'];

    								$wnperiodo = ($wperiodo * 1)  + ($periodicidad * 1);

    								 if( $wnperiodo > 12)
    								 {
    									 $wnperiodo = $wnperiodo -12;
    									 $wnano = $wperano + 1;
    								 }else
    								 
    									 $wnano = $wperano;
    								 

    								$query = "INSERT INTO ".$wbasedato."_000058
    											(	Medico, Fecha_data, Hora_data, Arecdr, Arecdo, Aretem, Arefor, Areper, Areano, Areest, Seguridad )
    										 VALUES
    											( 	'".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$ucordinador."','".$unix['wcodigo']."' ,
    												'".$temaevaluacion."' , '".$formatoevaluacion."' , '".$wnperiodo."' , '".$wnano."' , 'on', 'C-kron' )";

    								$res = mysql_query($query,$conex) or die("Error: " . mysql_errno() . " - en el query (Insert En '".$wbasedato."'_000058   ): ".$query." - " . mysql_error());

                                }
							

						}
						else
						{
							$cco_sin_coordina = $cco_sin_coordina."-".$unix['wccosto'];

						}


                        // fwrite($fp, $insert.PHP_EOL.PHP_EOL);
                    }
                }
            }

            /* ACTUALIZAR EPS's */
            // Lee todas las eps's que estan en la base de datos Matrix - root_000073
            $q = "  SELECT  Epscod AS codigo, Epsnit AS nit, Epsest AS estado
                    FROM    root_000073";
            $res = mysql_query($q,$conex);

            $epsMatrix = array();
            while ($row = mysql_fetch_array($res))
            {
                $epsMatrix[$row['codigo']] = array(
                                        'codigo'   =>$row['codigo'],
                                        'nit'      =>$row['nit'],
                                        'estado'   =>$row['estado']
                                    );
            }

            $queryEPS = "
                    SELECT  foncod AS codigo, fonnom AS nombre, fonnit AS nit, fonact AS estado, fondir AS direccion, fontel AS telefono
                    FROM    nofon
                    WHERE   fondir IS NOT NULL
                            AND fontel IS NOT NULL
                            AND fonnit IS NOT NULL
                            AND fonnit <> ''

                    UNION

                    SELECT  foncod AS codigo, fonnom AS nombre, fonnit AS nit, fonact AS estado, '' AS direccion, '' AS telefono
                    FROM    nofon
                    WHERE   fondir IS NULL
                            AND fontel IS NULL
                            AND fonnit IS NOT NULL
                            AND fonnit <> ''

                    UNION

                    SELECT  foncod AS codigo, fonnom AS nombre, fonnit AS nit, fonact AS estado, fondir AS direccion, '' AS telefono
                    FROM    nofon
                    WHERE   fondir IS NOT NULL
                            AND fontel IS NULL
                            AND fonnit IS NOT NULL
                            AND fonnit <> ''

                    UNION

                    SELECT  foncod AS codigo, fonnom AS nombre, fonnit AS nit, fonact AS estado, '' AS direccion, fontel AS telefono
                    FROM    nofon
                    WHERE   fondir IS NULL
                            AND fontel IS NOT NULL
                            AND fonnit IS NOT NULL
                            AND fonnit <> ''";
            $resEPS = odbc_exec($conexunix,$queryEPS)or die( odbc_error()." (consulta EPS) Query talento.php - $queryEPS - ".odbc_errormsg() );

            $epsUnix = array();
            while (odbc_fetch_row($resEPS))
            {
                $epsUnix[odbc_result($resEPS,'codigo')] = array(
                                        'nombre'    => odbc_result($resEPS,'nombre'),
                                        'nit'       => trim(odbc_result($resEPS,'nit')),
                                        'estado'    => trim(odbc_result($resEPS,'estado')),
                                        'direccion' => trim(odbc_result($resEPS,'direccion')),
                                        'telefono'  => trim(odbc_result($resEPS,'telefono'))
                                    );
            }

            foreach($epsUnix as $cod_eps => $value)
            {
                $unix_ep = $epsUnix[$cod_eps];
                if(array_key_exists($cod_eps,$epsMatrix))
                {
                    $updEstado = (trim($unix_ep['estado']) == 'S') ? 'on': 'off';
                    $update = " UPDATE  root_000073 SET
                                        Epsnom = '".trim($unix_ep['nombre'])."',
                                        Epstel = '".trim($unix_ep['telefono'])."',
                                        Epsest = '".$updEstado."',
                                        Epsdir = '".trim($unix_ep['direccion'])."'
                                WHERE   Epscod = '".$cod_eps."'";
                    $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query (actualizar EPS's): ".$update." - ".mysql_error());
                    $cont_actualizados_eps++;
                }
                else
                {
                    $istEstado = (trim($unix_ep['estado']) == 'S') ? 'on': 'off';

                    $insert = " INSERT INTO root_000073
                                    (   Medico, Fecha_data, Hora_data, Epscod, Epsnom,
                                        Epstel, Epsdir, Epsnit, Epsest, Seguridad)
                                VALUES
                                    (   'root','".date("Y-m-d")."','".date("H:i:s")."','".$cod_eps."','".$unix_ep['nombre']."',
                                        '".$unix_ep['telefono']."','".$unix_ep['direccion']."','".$unix_ep['nit']."','".$istEstado."','C-".$user_session."')";
                    $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En root_000073 por primera vez - generado desde actualizar matrix desde talento.php): " . $insert . " - " . mysql_error());
                    $cont_nuevos_eps++;

                }
            }

            /* ACTUALIZAR CARGOS DE EMPLEADOS */
            // Lee todos los cargos que estan en la base de datos Matrix - root_000079
            $q = "  SELECT  Carcod AS codigo, Cardes AS nombre, Carest AS estado
                    FROM    root_000079";
            $res = mysql_query($q,$conex);

            $cargMatrix = array();
            while ($row = mysql_fetch_array($res))
            {
                $cargMatrix[trim($row['codigo'])] = array(
                                        'nombre' =>$row['nombre'],
                                        'estado' =>$row['estado']);
            }

            $queryCar = "   SELECT  oficod AS codigo, ofinom AS nombre, ofiact AS estado
                            FROM    noofi
                            WHERE   ofiact IS NOT NULL";
            $resCar = odbc_exec($conexunix,$queryCar)or die( odbc_error()." (consulta Cargos empleados) Query talento.php -<pre> $queryCar </pre>- ".odbc_errormsg() );

            $cargUnix = array();
            while (odbc_fetch_row($resCar))
            {
                $cod_cargoux = trim(odbc_result($resCar,'codigo'));
                $updEstado = (trim(odbc_result($resCar,'estado') == 'S') ? 'on': 'off');
                $cargUnix[$cod_cargoux] = array(
                                        'nombre'    => trim(odbc_result($resCar,'nombre')),
                                        'estado'    => $updEstado);
            }

            foreach($cargUnix as $cod_carg => $value)
            {
                $unix_carg = $cargUnix[$cod_carg];
                if(array_key_exists($cod_carg,$cargMatrix) && ($cargMatrix[$cod_carg]["estado"] != $cargUnix[$cod_carg]["estado"] || $cargMatrix[$cod_carg]["nombre"] != $unix_carg['nombre']))
                {
                    $update = " UPDATE  root_000079 SET
                                        Cardes = '".trim($unix_carg['nombre'])."',
                                        Carest = '".trim($unix_carg['estado'])."'
                                WHERE   Carcod = '".$cod_carg."'";
                    $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query (actualizar cargos empleados): <pre>".$update."</pre> - ".mysql_error());
                    $cont_cargos_empleados++;
                }
                elseif(!array_key_exists($cod_carg,$cargMatrix))
                {
                    $estEstado = $unix_carg['estado'];

                    $insert = " INSERT INTO root_000079
                                    (   Medico, Fecha_data, Hora_data, Carcod, Cardes,
                                        Carley, Carfor, Carest, Seguridad)
                                VALUES
                                    (   'root','".date("Y-m-d")."','".date("H:i:s")."','".$cod_carg."','".$unix_carg['nombre']."',
                                        'off','','".$estEstado."','C-".$user_session."')";
                    $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En root_000079 por primera vez - generado desde actualizar matrix desde buscame.php): <pre>".$insert." </pre>- ".mysql_error());
                    $cont_nuevos_cargos++;
                    // fwrite($fp, $insert.PHP_EOL.PHP_EOL);
                }
            }
        }

    // fclose($fp);
        $data['actualizados']                  = $cont_actualizados;
        $data['insertados']                    = $cont_nuevos;
        $data['actualizados_eps']              = $cont_actualizados_eps;
        $data['insertados_eps']                = $cont_nuevos_eps;
        $data['insertados_cargos']             = $cont_nuevos_cargos;
        $data['actualizados_cargos']           = $cont_cargos_empleados;
        $data['insertados_arbol_relacion']     = $cont_nuevas_relaciones;
        $data['insertados_cco_sincoordinador'] = $cco_sin_coordina;
        $data['contratos']                     = $wcontratos;

        echo json_encode($data);

		odbc_close($conexunix);

		odbc_close_all();

        return;

    }
    elseif(isset($accion) && $accion == 'recarga')
    {

        if(isset($form) && $form == 'load_costo')
        {
            $q = "  SELECT  Empdes,Emptcc
                    FROM    root_000050
                    WHERE   Empcod = '".$wemp_pmla."'";
            $res = mysql_query($q,$conex);

            $options = '<option value="" >Seleccione..</option>';
            if($row = mysql_fetch_array($res))
            {
                $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($id_padre))));
                $buscaNombre = strtoupper(strtolower($buscaNombre));
                $tabla_CCO = $row['Emptcc'];

                switch ($tabla_CCO)
                {
                    case "clisur_000003":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                        FROM    clisur_000003 AS tb1
                                                INNER JOIN
                                                ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        WHERE   tb1.Ccodes LIKE '%".trim($buscaNombre)."%'
                                                OR tb1.Ccocod LIKE '%".trim($buscaNombre)."%'
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Ccodes";
                            break;
                    case "farstore_000003":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                        FROM    farstore_000003 AS tb1
                                                INNER JOIN
                                                ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        WHERE   tb1.Ccodes LIKE '%".trim($buscaNombre)."%'
                                                OR tb1.Ccocod LIKE '%".trim($buscaNombre)."%'
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Ccodes";
                            break;
                    case "costosyp_000005":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                        FROM    costosyp_000005 AS tb1
                                                INNER JOIN
                                                ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        WHERE   tb1.Ccoemp = '".$wemp_pmla."' AND
                                                tb1.Cconom LIKE '%".trim($buscaNombre)."%'
                                                OR tb1.Ccocod LIKE '%".trim($buscaNombre)."%'
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Cconom";                            
                            break;
                    case "uvglobal_000003":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                        FROM    uvglobal_000003 AS tb1
                                                INNER JOIN
                                                ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        WHERE   tb1.Ccodes LIKE '%".trim($buscaNombre)."%'
                                                OR tb1.Ccocod LIKE '%".trim($buscaNombre)."%'
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Ccodes";
                            break;
                    default:
                            $query="    SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                        FROM    costosyp_000005 AS tb1
                                                INNER JOIN
                                                ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        WHERE   tb1.Ccoemp = '".$wemp_pmla."' AND 
                                                tb1.Cconom LIKE '%".trim($buscaNombre)."%'
                                                OR tb1.Ccocod LIKE '%".trim($buscaNombre)."%'
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Cconom";
                }
                
                $res = mysql_query($query,$conex);

                while($row = mysql_fetch_array($res))
                {
                    $options .= '<option value="'.$row['codigo'].'" >'.$row['codigo'].' - '.utf8_encode(strtoupper(strtolower($row['nombre']))).'</option>';
                }
            }
            echo $options;
        }
        elseif(isset($form) && $form == 'load_cargos')
        {
            $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($id_padre))));
            $buscaNombre = strtoupper(strtolower($buscaNombre));

            $q = "  SELECT  Carcod AS cod_cargo, Cardes AS nom_cargo
                    FROM    root_000079
                    WHERE   Cardes LIKE '%".$buscaNombre."%'
                            OR Carcod LIKE '".$buscaNombre."'
                    ORDER BY Cardes";

            $res = mysql_query($q,$conex);

            $optionsCar = '<option value="" >Seleccione..</option>';

            while($row = mysql_fetch_array($res))
            {
                $optionsCar .= '<option value="'.$row['cod_cargo'].'" >'.$row['cod_cargo'].' - '.utf8_encode(strtoupper(strtolower($row['nom_cargo']))).'</option>';
            }
            echo $optionsCar;
        }
        //return;
    }
    elseif($accion == 'update')
    {
        if(isset($form) && $form == 'default')
        {

            $update = " UPDATE  ".$wbasedato."_".$wtabla." SET
                                $campo = '".utf8_decode($value)."'
                        WHERE   Ideuse = '$id_registro'";
            $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Actualizar registros : ".$update." - ".mysql_error());
            $data = array('id_registro'=>$id_registro,'error'=>0);
            echo json_encode($data);
        }
    }
    elseif($accion == 'load')
    {
        if(isset($form) && $form == 'buscar')
        {
            $lista_enc = array();
            // echo '<pre>';print_r($_POST);echo '</pre>';

            $data = array(
                            'error'=>0,
                            'mensaje'=>'',
                            'html_info_datos'=>'',
                            'html_lista_varios'=>'',
                            'html_navegar'=>'',
                            'wuse'=>'',
                            'wuse_listado'=>'',
                            'foto'=>'',
                            'nombre'=>'[?] NO SE ENCONTRARON DATOS',
                            'encontrados'=>0);

            $foto = '<img class="imagen" src="'.getFoto($conex,$wemp_pmla,$wbasedato,$user_session,'').'"/>'; // se envía cédula vacía para que no encuentre la foto y muestre silueta.

            $vacio = false;
            if($wced == '' && $wcodigo == '' && $wnombre1 == '' && $wnombre2 == '' && $wapellido1 == '' && $wapellido2 == '' && $wccostos == '' && $wccargo == '' && $wfechaingreso == '' && $wfecharetiro == '' && $wingresoretiro == '') // && $wnombrecc == ''
            {
                $wced           = '*';
                $wcodigo        = '*';
                $wnombre1       = '+';
                $wnombre2       = '+';
                $wapellido1     = '+';
                $wapellido2     = '+';
                $wccostos       = '+';
                $wccargo        = '+';
                $wfechaingreso  = '+';
                $wfecharetiro   = '+';
                $wingresoretiro = '*';
                $vacio = true;
            }

            /**
                Los parámetros de búsqueda que se evalúan en el condicional anterior se usarán para buscar coincidencias en la base de datos de matrix (talhuma_000013 por ejemplo)
                y si no se encuentra ningúna coincidencia entonces hace de nuevo una busqueda en UNIX, si lo encuentra en UNIX entonces lee toda la información
                del empleado y la inserta en la tabla talhuma_000013 para luego leerla directamente y no tenerla que leer desde UNIX.
            */
            $empleado = array();
            $empleado = buscarEnTalhuma($conex,$wemp_pmla,$wbasedato,$user_session,$wcodigo,$wced,$wnombre1,$wnombre2,$wapellido1,$wapellido2,$wccostos,$wccargo,$wsexo,$westado,$wfechaingreso,$wfecharetiro,$wingresoretiro); // busca primero los datos del empleado en matrix, si no encuentra datos busca en unix e inserta en matrix
            if( count($empleado) <= 0)
            {
                // echo 'Consulta en unix';
                //echo "$wced == '*' && $wcodigo == '*' && $wnombre1 == '+' && $wnombre2 == '+' && $wapellido1 == '+' && $wapellido2 == '+' && $wccostos == '' && $wccargo == '+'";
                if(!$vacio && !(( strlen($wcodigo) > 5)))
                {
                    $empleado = buscarEnUnix($conex,$wemp_pmla,$wbasedato,$user_session,$wcodigo,$wced,$wnombre1,$wnombre2,$wapellido1,$wapellido2,$wccostos,$wccargo,$wsexo,$westado,$wfechaingreso,$wfecharetiro,$wingresoretiro);
                }
            }

            /**
                Si el usuario que está generando la busqueda está configurado como administrador entonces puede ver toda la información disponible por este programa.
            */
            $permisoAdmin = consultarSiEsAdmin($conex, $wemp_pmla, $wtema, $wcodtab, $user_session);
            // echo '<pre>';print_r($permisoAdmin); echo '</pre>';

            /**
                Para todos los empleados solo esta permitido ver sus nombres, centro de costos, cargo, extensión.
                a no ser que sea un administrador que lo puede ver todo.
            */
            $disabled = 'disabled';
            $comtIni = " <!-- ";
            $comtClose = " --> ";
            $rowspan = "3";
            if(count($permisoAdmin) > 0 && $permisoAdmin['esAdmin'] == 'on') // si el que esta consultando tiene permiso de administrador para buscame como administrador le deja ver todos los datos
            {
                // $disabled = "";
                $comtIni = "";
                $comtClose = "";
                $rowspan = "9";
            }

            // Si se encontró exactamente un solo empleado en la busqueda se muestra de una vez la información, sino se arma una tabla con todos los encontrados
            if (count($empleado) >= 1)
            {
                $data['encontrados'] = count($empleado);
                if(count($empleado) == 1) // PARA MOSTRAR LA INFORMACIÓN DE SOLO UNA PERSONA
                {
                    $datos = '';
                    $wuse_encontrado = '';
                    foreach($empleado as $key => $inf)
                    {
                        if($wcodigo == $user_session_wemp)
                        {
                            $disabled = "";
                            // $comtIni = "";
                            // $comtClose = "";
                            // $rowspan = "9";
                        }

                        $wcodigo    = $inf['wcodigo'];
                        $wuse_encontrado = $inf['wcodigo'];
                        $wced       = $inf['wced'];
                        $wccosto    = $inf['wccosto'];
                        $wf_ingreso = $inf['wf_ingreso'];
                        $wnombre1   = $inf['wnombre1'];
                        $wnombre2   = $inf['wnombre2'];
                        $wapellido1 = $inf['wapellido1'];
                        $wapellido2 = $inf['wapellido2'];
                        $westado    = $inf['westado'];
                        $wnom_cco   = $inf['wnom_cco'];
                        $wf_nace    = $inf['wf_nace'];
                        $cargo      = $inf['cargo'];
                        $edad       = $inf['edad'];
                        $f_retiro   = $inf['f_retiro'];
                        $ext        = $inf['extension'];
                        $mail       = $inf['email'];
                        $genero     = $inf['sex'];

                        $msj_t      = ($westado == 'A' || $westado == 'on')? 'Hace': 'Labor&oacute;';
                        $msj_retiro = ($westado == 'A' || $westado == 'on')? '': ', '.$f_retiro;
                        $westado    = ($westado == 'A' || $westado == 'on')? 'Activo': 'Retirado';
                        $wf_nace    = explode("-", $wf_nace);

                        $t_laborado = calcularAnioMesesDiasTranscurridos($wf_ingreso,trim($f_retiro));

                        $n_empleado = trim($wnombre1.' '.$wnombre2.' '.$wapellido1.' '.$wapellido2);

                        //$foto = '';
                        $genero = ($genero == '') ? 'M':$genero;
                        $varadmin = $permisoAdmin['esAdmin'];

                        // Quitar Filtro con root_000082
                        //$foto = '<img class="imagen" src="'.getFoto($conex,$wemp_pmla,$wbasedato,$user_session,'',$genero).'"/>';

                        //if(count($permisoAdmin) > 0 && $permisoAdmin['esAdmin'] == 'on')
                        //{
                            $foto = '<img class="imagen" src="'.getFoto($conex,$wemp_pmla,$wbasedato,$user_session,$wced,$genero,$varadmin).'"/>';
                        //}

                        $wcodigo_ex = explode('-',$wcodigo);
                        $wcodigoVr = $wcodigo_ex[0];
                        $wemp = $wcodigo_ex[1];

                        $datos = '
                        <table border="0" cellspacing="0" cellpadding="1" style="width:100%">
                            <tr>
                                <td class="tbold disminuir padding_info">&nbsp;Cargo actual</td>
                                <td class="alng padding_info">'.utf8_encode(trim($cargo)).'</td>
                            </tr>'.$comtIni.'
                            <tr>
                                <td class="tbold disminuir padding_info" >&nbsp;C&oacute;digo</td>
                                <td class="alng padding_info">'.$wcodigoVr.' ['.$wemp.']</td>
                            </tr>'.$comtClose.''.$comtIni.'
                            <tr>
                                <td class="tbold disminuir padding_info" >&nbsp;Documento</td>
                                <td class="alng padding_info">'.$wced.'</td>
                            </tr>'.$comtClose.'
                            <tr>
                                <td class="tbold disminuir padding_info" >&nbsp;Centro costo</td>
                                <td class="alng padding_info">['.$wccosto.'] '.utf8_encode($wnom_cco).'</td>
                            </tr>'.$comtIni.'
                            <tr>
                                <td class="tbold disminuir padding_info" >&nbsp;Fecha ingreso</td>
                                <td class="alng padding_info">'.$wf_ingreso.',
                                            <font style="font-weight:bold">'.$msj_t.' '.$t_laborado['anios'].' A&ntilde;os '.$t_laborado['meses'].' meses '.$t_laborado['dias'].' d&iacute;as.</font>
                                </td>
                            </tr>'.$comtClose.''.$comtIni.'
                            <tr>
                                <td class="tbold disminuir padding_info" >&nbsp;Estado</td>
                                <td class="tbold alng padding_info">'.$westado.$msj_retiro.'</td>
                            </tr>'.$comtClose.''.$comtIni.'
                            <tr>
                                <td class="tbold disminuir padding_info" >&nbsp;Fecha cumplea&ntilde;os</td>
                                <td class="alng padding_info">'.getMonthText($wf_nace[1]*1).' '.(($wf_nace[2]=='00' || $wf_nace[2]=='') ? '': $wf_nace[2]).'</td>
                            </tr>'.$comtClose.'
                            <tr>
                                <td class="tbold disminuir padding_info" >&nbsp;Extensi&oacute;n</td>
                                <td class="alng padding_info">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td>
                                                <input type="hidden" name="wextensionAnt" id="wextensionAnt" value="'.$ext.'" />
                                                <input '.$disabled.' size="25" type="text" id="wextension" name="wextension" value="'.$ext.'" rel="000013" in="Ideext" onBlur="opGrabar(\'wextensionAnt\', \'wextension\', \'img_graba_ext\');">
                                            </td>
                                            <td width="60px;">
                                                <div align="left" id="img_graba_ext" style="display:none;">
                                                    <span style="cursor: pointer;" onClick="blurCampoBuscame(\'wextension\',\'wuse_tal\',\'default\',\'\',\'\',\'\'); ocultarElemnto(\'img_graba_ext\'); actualizaAnt(\'wextension\');" ><img title="Guardar la extensi&oacute;n modificada" width="11" height="11" src="../../images/medical/cambiar1.png" /><img title="Guardar la extensi&oacute;n modificada" width="13" height="13" src="../../images/medical/root/grabar16.png" /></span>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>'.$comtIni.'
                            <tr>
                                <td class="tbold disminuir padding_info" >&nbsp;E-mail</td>
                                <td class="alng padding_info">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td>
                                                <input type="hidden" name="wcorreoAnt" id="wcorreoAnt" value="'.$mail.'" />
                                                <input '.$disabled.' size="25" type="text" id="wcorreo" name="wcorreo" value="'.$mail.'" rel="000013" in="Ideeml" onBlur="opGrabar(\'wcorreoAnt\', \'wcorreo\', \'img_graba_mail\');">
                                            </td>
                                            <td width="60px;">
                                                <div align="left" id="img_graba_mail" style="display:none;">
                                                    <span style="cursor: pointer;" onClick="blurCampoBuscame(\'wcorreo\',\'wuse_tal\',\'default\',\'\',\'\',\'\'); ocultarElemnto(\'img_graba_mail\'); actualizaAnt(\'wcorreo\');" ><img title="Guardar direcci&oacute;n e-mail modificada" width="11" height="11" src="../../images/medical/cambiar1.png" /><img title="Guardar direcci&oacute;n e-mail modificada" width="13" height="13" src="../../images/medical/root/grabar16.png" /></span>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>'.$comtClose.'
                        </table>';
                    }
                    $data['html_info_datos'] = $datos;
                    $data['wuse'] = $wuse_encontrado;
                    $data['nombre'] = $n_empleado;
                }
            }
            else
            {
                $no_encontrado = '
                                    <div align="center" style="font-weigth:bold; font-size:16pt;color:#999999;">
                                        <br />
                                        <br />
                                        <br />
                                        [?] No se encontraron datos..
                                        <br />
                                    </div>';
                $data['html_info_datos'] = $no_encontrado;
            }

            if(isset($wuse_listado) && $wuse_listado != '' && count($empleado) < 1)
            {
                $empleado = buscarEnTalhuma($conex,$wemp_pmla,$wbasedato,$user_session,$wuse_listado,'','','','','','','','','','','','');
                // echo '<pre>';print_r($empleado);echo '</pre>';
            }

            if(count($empleado) > 1) // PARA MOSTRAR UNA LISTA DE TODAS LAS PERSONAS ENCONTRADAS
            {
                $listado =
                        '  <br />
                        <div style="text-align: center;font-weight:bold;"> SE ENCONTR&Oacute; M&Aacute;S DE UN RESULTADO, SELECCIONE UNO DE ELLOS.</div>
                        <br />
                        <br />
                        <div style="align: center;">
                            <table width="1180px;" style="text-align: left; font-size:9pt;" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td><font style="font-weight:bold;">Encontrados:</font>&nbsp;'.count($empleado).'</td>
                                </tr>
                            </table>
                        </div>
                        <br />
                        <table width="1180px;" style="text-align: left;" border="0" cellpadding="0" cellspacing="0">

                        <tr class="encabezadoTabla">
                            <th align="center">&nbsp;</th>
                            <th align="center" width="85">C&oacute;digo</th>
                            <th align="center">Nombres</th>
                            <th align="center">Apellidos</th>
                            <th align="center">Estado</th>
                            <th align="center">Centro costo</th>
                            <th align="center">Cargo</th>
                            <th align="center">Fecha ingreso</th>
                            <th align="center">Fecha retiro</th>
                        </tr>';
                $fl = 0;

                foreach($empleado as $key => $datos)
                {
                    $list[] = $datos['wcodigo'];
                    $wclass = ($fl % 2 == 0) ? "fila1" : "fila2";
                    $fl++;

                    $westado = $datos['westado'];
                    $westado = ($westado == 'A' || $westado == 'on')? '<font style="color:#006600;">Activo</font>': '<font style="color:#990000;">Retirado</font>';

                    $wcodigo_ex = explode('-',$datos['wcodigo']);
                    $wcodigoVr = $wcodigo_ex[0];
                    $wemp = $wcodigo_ex[1];

                    $wfretiro = ($datos['f_retiro'] == '0000-00-00') ? 'N/A': $datos['f_retiro'];

                    $listado .=
                        '
                        <tr style="cursor:pointer;" class="'.$wclass.'" id="'.$datos['wcodigo'].'" onclick="ocultarBusqueda(); generarBusqueda(\''.$datos['wcodigo'].'\',\'navegacion\');">
                            <td><font style="font-weight:bold;">&nbsp;'.$fl.'&nbsp;&nbsp;</font></td>
                            <td>'.$wcodigoVr.' ['.$wemp.']</td>
                            <td>'.utf8_encode(trim($datos['wnombre1'].' '.$datos['wnombre2'])).'</td>
                            <td>'.utf8_encode(trim($datos['wapellido1'].' '.$datos['wapellido2'])).'</td>
                            <td style="font-weight:bold;">'.$westado.'&nbsp;</td>
                            <td>['.$datos['wccosto'].'] '.utf8_encode(strtoupper($datos['wnom_cco'])).'</td>
                            <td>['.$datos['wcodcargo'].'] '.utf8_encode($datos['cargo']).'</td>
                            <td align="center">'.$datos['wf_ingreso'].'</td>
                            <td align="center">'.$wfretiro.'</td>
                        </tr>';
                }
                $listado .= '</table>';

                $lista_enc = $list;
                $listado .=  '
                        <tr><td colspan="7" class="encabezadoTabla">&nbsp;</td></tr>';
                $data['html_lista_varios'] = $listado;
            }

            if((isset($tipo_llamado) && $tipo_llamado == 'navegacion') || (count($lista_enc) > 0))
            {
                if(count($lista_enc) > 0) { $wuse_listado = implode('|',$lista_enc); }
                $data['wuse_listado'] = $wuse_listado;
                $listado = explode('|',$wuse_listado);

                $total_pos = count($listado);
                $pos_actual = array_search( $wcodigo, $listado );

                $mouse = ' onmouseover="activarBoton(this,\'on\');" onmouseout="activarBoton(this,\'off\');" ';

                $actionPrimero  = (array_key_exists(0,$listado))
                                    ? $mouse.'style="font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;" onclick="verNavegacion(); generarBusqueda(\''.$listado[0].'\',\'navegacion\')'
                                    : '';
                $actionAnt      = (array_key_exists(($pos_actual-1),$listado))
                                    ? $mouse.' class="encabezadoTabla" style="font_size:8pt; width:20px; cursor:pointer;" onclick="verNavegacion(); generarBusqueda(\''.$listado[($pos_actual-1)].'\',\'navegacion\')'
                                    : 'style="background-color: #999999;"';
                $actionSig      = (array_key_exists(($pos_actual+1),$listado))
                                    ? $mouse.'class="encabezadoTabla" style="font_size:8pt; width:20px; cursor:pointer;" onclick="verNavegacion(); generarBusqueda(\''.$listado[($pos_actual+1)].'\',\'navegacion\')'
                                    : 'style="background-color: #999999;"';
                $actionUltimo   = (array_key_exists((count($listado)-1),$listado))
                                    ? $mouse.'style="font_size:8pt; width:20px; cursor:pointer; border-left: 4px #ffffff solid;" onclick="verNavegacion(); generarBusqueda(\''.$listado[(count($listado)-1)].'\',\'navegacion\')'
                                    : '';
                $navegar = '
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr class="fila1">
                                <td id="prim" align="left" class="encabezadoTabla" title="Primero" '.$actionPrimero.'">
                                    <font style="font-weight:bold">&nbsp;<<&nbsp;</font>
                                </td>
                                <td id="ant" align="left" title="Anterior" '.$actionAnt.'">
                                    <font style="font-weight:bold">&nbsp;<&nbsp;</font>
                                </td>
                                <td align="left" style="font_size:8pt; width:40px;"><font>&nbsp;Ant.</font></td>
                                <td align="center" style="font_size:4pt; width:100px;">
                                    <font style="font-weight:bold">
                                        '.($pos_actual+1).' de <a title="Ver toda la lista" href="#" onclick="verListaEncontrados();generarBusqueda(\'\', \'navegacion\');" style="color:#2A5DB0;">['.$total_pos.']</a>
                                    </font>
                                </td>
                                <td align="right" style="font_size:8pt; width:40px;"><font>Sig.</font></td>
                                <td id="sig" align="center" title="Siguiente" '.$actionSig.'">
                                    <font style="font-weight:bold">&nbsp;>&nbsp;</font>
                                </td>
                                <td id="ult" align="center" class="encabezadoTabla" title="&Uacute;ltimo" '.$actionUltimo.'">
                                    <font style="font-weight:bold">&nbsp;>>&nbsp;</font>
                                </td>
                            </tr>
                        </table>';

                $data['html_navegar'] = $navegar;
            }

            // $fp = fopen('buffer.txt',"w+");
            // fwrite($fp, print_r($data,true));
            // fclose($fp);
            $data['foto'] = $foto;
            echo json_encode($data);
        }
    }
    return;
}

/**
    Esta función se encarga de buscar la foto o silueta para la persona encontrada, como en linux hay diferencia en encontrar una extensión con
    mayúsculas o minúsculas entonces se valídan las convinaciones entre la extensión .jpg variando entre mayúsculas y minúsculas

    @param $conex       : NO SE USA EN EL MOMENTO.
    @param $wemp_pmla   : NO SE USA EN EL MOMENTO.
    @param $wbasedato   : NO SE USA EN EL MOMENTO.
    @param $user_session: NO SE USA EN EL MOMENTO.
    @param $wcedula     : Si existe un número de cedula indica que se debe buscar la foto con este nombre, si no existe se muestra silueta en base al sexo.
    @param $sex         : Indica si es hombre o mujer para poder mostrar las silueta adecuada.

    @return string: nombre de la foto que se va a mostrar.
 */
function getFoto($conex,$wemp_pmla,$wbasedato,$user_session,$wcedula = 'not_foto',$sex='M',$varadmin='off')
{
    $extensiones_img = array(   '.jpg','.Jpg','.jPg','.jpG','.JPg','.JpG','.JPG','.jPG',
                                '.png','.Png','.pNg','.pnG','.PNg','.PnG','.PNG','.pNG');

    $wruta_fotos = "../../images/medical/tal_huma/";
    $wfoto = "silueta".$sex.".png";

    $wfoto_em    = '';
    $ext_arch    = '';
    $mostrarfoto = 's';

    if ($varadmin !== 'on'){

            $q = "  SELECT  Ideuse AS codigo, Ideced AS cedula, Ideest AS estado, Ideafb
                            FROM   ".$wbasedato."_000013
                            WHERE  Ideced = '".$wcedula."'" ;

            $res = mysql_query($q,$conex);

            $num = mysql_num_rows($res);

            if( $num > 0){
            
               $row = mysql_fetch_assoc($res);

               if ( $row['Ideafb'] == 'off')
                   
                   $mostrarfoto ='n';
                    
            }
    }

    // $permisoAdmin = consultarSiEsAdmin($conex, $wemp_pmla, '01', '01', $user_session);
    // if(count($permisoAdmin) > 0 && $permisoAdmin['esAdmin'] == 'on') // si el que esta consultando tiene permiso de administrador para buscame como administrador le deja ver todos los datos
    // {
        // comentado para que no aparezca foto a nadie, determinación temporal.
        foreach($extensiones_img as $key => $value)
        {
            $ext_arch = $wruta_fotos.trim($wcedula).$value;

            // echo "<!-- Foto encontrada: $ext_arch -->";
            if (file_exists($ext_arch))
            {
                $wfoto_em = $ext_arch;
                break;
            }
        }
    // }

    if ($wfoto_em == '' || $mostrarfoto == 'n')
    {
        $wfoto_em = $wruta_fotos.$wfoto;
    }

    return $wfoto_em;
}
include_once("root/comun.php");

$centro_costos = getOptionsCostos($wemp_pmla, $conex, $wbasedato, '', '', 'off');
$cargos = getOptionsCargos($wemp_pmla, $conex, $wbasedato, '', '', 'off');

/*****************************************************************************************************************************************/


?>
<html>
<head>
    <script type="text/javascript">
    url_add_params = addUrlCamposCompartidosTalento();

    $(document).ready( function () {
        consultarEsAdmin();
        $('#div_navegar').hide();
        $('#div_varios_encontrados').show();
        $('#div_marco_info_empleado').hide();
    } );

    function consultarEsAdmin(){
        $.post("buscame.php?"+url_add_params,
            {
                consultaAjax:   '',
                accion:         'validar_admin',
                form:           ''
            }
            ,function(data) {
                if(data.error == 1)
                {
                    alert(data.mensaje);
                }
                else
                {
                    if(data.esAdmin != 'on'){
                        $('#wuse_tal').val(data.usuario_logueado);
                    }
                }
            }, "json"
        ).done(function(){
            conservarBusqueda();
        });
    }

    function conservarBusqueda(){
        tipo_busqueda = '';
        if( $('#wuse_listado').val() != '')
        {
            tipo_busqueda = 'navegacion';
        }

        generarBusqueda($('#wuse_tal').val(), tipo_busqueda);
    }

    function setWuse()
    {
        $('#wuse_listado').val('');
        $('#wuse_tal').val($('#wcodigo').val());
    }

    function verListaEncontrados()
    {
        $('#div_navegar').hide();
        $('#div_varios_encontrados').show();
        $('#div_marco_info_empleado').hide();
        $('#wuse_tal').val('');
    }

    function verNavegacion()
    {
        $('#div_navegar').show();
    }

    function generarBusqueda(coduse, tipoLlamado)
    {
        var codigoBuscar = $('#wuse_tal').val();
        if(coduse != ''){ codigoBuscar = coduse; }

        cco = '';
        ccg = '';
        gen = '';
        est = '';
        wced = '';
        wnombre1 = '';
        wnombre2 = '';
        wapellido1 = '';
        wapellido2 = '';
        wfechaingreso = '';
        wfecharetiro = '';
        wingresoretiro = '';
        var validacion = true;

        if(tipoLlamado != 'navegacion')
        {
            cco = $("#wccostos").val();
            ccg = $("#wccargo").val();
            gen = $('[name=wsexo]:checked').val();
            est = $('[name=westado]:checked').val();
            wced = $('#wced').val();
            wnombre1 = $('#wnombre1').val();
            wnombre2 = $('#wnombre2').val();
            wapellido1 = $('#wapellido1').val();
            wapellido2 = $('#wapellido2').val();

            wingresoretiroCHK = $('input:radio[name=wingresoretiro]:checked').attr('id');
            wingresoretiro = $('#'+wingresoretiroCHK).val();
            if(wingresoretiro == '')
            {
                resetFechasInOut();
            }
            else
            {
                wfechaingreso = $('#wfechaingreso').val();
                wfecharetiro = $('#wfecharetiro').val();
                if(wfechaingreso == '' || wfecharetiro == '')
                {
                    alert('Debe seleccionar una fecha inicial y una fecha final');
                    validacion = false;
                }
            }
        }

        if(validacion == true)
        {
            $.post("buscame.php?"+url_add_params,
                {
                    //wemp_pmla   : $('#wemp_pmla').val(),
                    //wtema       : $('#wtema').val(),
                    consultaAjax: '',
                    accion      : 'load',
                    form        : 'buscar',
                    wcodigo     : codigoBuscar,
                    wccostos    : cco,
                    wccargo     : ccg,
                    wced        : wced,
                    wnombre1    : wnombre1,
                    wnombre2    : wnombre2,
                    wapellido1  : wapellido1,
                    wapellido2  : wapellido2,
                    wsexo       : gen,
                    westado     : est,
                    tipo_llamado: tipoLlamado,
                    wfechaingreso   : wfechaingreso,
                    wfecharetiro    : wfecharetiro,
                    wingresoretiro  : wingresoretiro,
                    wuse_listado: $('#wuse_listado').val()
                }
                ,function(data) {

                    $('#cont_buscar').hide("slow");
                    $('#div_varios_encontrados').hide();
                    $('#div_marco_info_empleado').hide();
                    if(tipoLlamado == '') { $('#div_navegar').html('&nbsp;'); }

                    if(data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        if(data.encontrados == '1' || data.encontrados == '0')
                        {
                            if(data.encontrados == 1) { $('#wuse_tal').val(data.wuse); }
                            $('#wuse_listado').val('');
                            $('#div_marco_info_empleado').show();
                            $('#div_info_datos').html(data.html_info_datos);
                            $('#nombre_emp').html(data.nombre);
                            $('#div_foto_empleado').html(data.foto);

                            $('#div_navegar').html(data.html_navegar);
                            if(data.wuse_listado != '')
                            {
                                $('#wuse_listado').val(data.wuse_listado);
                                $('#div_navegar').show();
                                $('#div_varios_encontrados').html(data.html_lista_varios);
                            }

                            if(data.encontrados == 0 && data.wuse == '' && data.wuse_listado != '')
                            {
                                verListaEncontrados();
                            }
                        }
                        else
                        {
                            $('#wuse_listado').val(data.wuse_listado);
                            $('#div_varios_encontrados').show();
                            $('#div_varios_encontrados').html(data.html_lista_varios);

                            $('#div_navegar').hide();
                            $('#div_navegar').html(data.html_navegar);
                        }
                    }
                },
                "json"
            );
        }
    }

    function ocultarBusqueda()
    {
        $('#div_varios_encontrados').hide();
        $('#cont_buscar').hide("slow");
    }

    function recargarLista(id_padre, id_hijo, form)
    {
        val = $("#"+id_padre.id).val();
        url_add_params = addUrlCamposCompartidosTalento();
        $('#'+id_hijo).load(    "buscame.php?"+url_add_params,
                                {
                                    consultaAjax:   '',
                                    //wemp_pmla:  $("#wemp_pmla").val(),
                                    //wtema:      $("#wtema").val(),
                                    accion:     'recarga',
                                    id_padre:   val,
                                    form:       form
                                });
    }

    function cambioImagen(img1, img2)
    {
        $('#'+img1).hide(1000);
        $('#'+img2).show(1000);
    }

    function verBuscar(id){    /* Esconde o despliega los filtros para buscar nuevamente */
        $("#"+id).toggle("slow");
    }

    function ocultarBuscar(id) /* Oculta los filtros de busqueda luego de presionar el boton buscar */
    {
        $('#'+id).hide("slow");
    }

    /**
     * description...
     *
     * @return unknown
     */
    function enterBuscar(ele,hijo,op,form,e)
    {
        tecla = (document.all) ? e.keyCode : e.which;
        if(tecla==13) {
            setTimeout(function(){
                $(".calendar").hide();
            },20);
            $("#"+hijo).focus();
        }
        else { return true; }
        return false;
    }

    function enterBuscarOk(e)
    {
        tecla = (document.all) ? e.keyCode : e.which;
        if(tecla==13) {
            setTimeout(function(){
                $(".calendar").hide();
            },20);
            setWuse();
            generarBusqueda('', '');
        }
    }

    function blurCampoBuscame(campo,id_reg,form,borra_seccion,DFiltro,DValor)
    {
        idc = campo;
        val = $("#"+idc).val();

        url_add_params = addUrlCamposCompartidosTalento();

        $.post("buscame.php?"+url_add_params,
            {
                //wemp_pmla:      $('#wemp_pmla').val(),
                //wtema:          $('#wtema').val(),
                id_registro:    $("#"+id_reg).val(),
                wtabla:         $("#"+idc).attr("rel"),
                value:          val,
                consultaAjax:   '',
                accion:         'update',
                form:           form,
                campo:          $("#"+idc).attr("in"),
                id_campo:       idc,
                delSeccion:     borra_seccion,
                delFiltro:      DFiltro,
                delValor:       $('#'+DValor).val()
            }
            ,function(data) {
                if(data.error == 1)
                {
                    alert(data.mensaje);
                }
                else
                {
                    //$("#"+id_reg).val(data.id_registro);
                }
            },
            "json"
        );
    }

    function opGrabar(anterior, actual, img)
    {
        ant = $("#"+anterior).val();
        act = $("#"+actual).val();

        if(act != ant)
        {
            $("#"+img).show('slow');
        }
        else
        {
            $("#"+img).hide('slow');
        }
    }

    function ocultarElemnto(elemento)
    {
        $("#"+elemento).hide(1000);
    }

    function actualizaAnt(actual)
    {
        act = $("#"+actual).val();
        $("#"+actual+'Ant').val(act);
    }

    function activarBoton(elemento,estado)
    {
        if(estado == 'on')
        {
            $("#"+elemento.id).addClass('btnActivo');
        }
        else
        {
            $("#"+elemento.id).removeClass('btnActivo');
        }
    }

    /**
        Esta función se encarga de desencadenar el proceso para migrar o actualizar datos de empleados desde UNIX hacia la base de datos de talhuma
    */
    function actualizarMatrix(u)
    {
        url_add_params = addUrlCamposCompartidosTalento();
        $("#img_esp").hide(500);
        $("#img_bus").show('slow');
        $.post("buscame.php?"+url_add_params,
            {
                // wemp_pmla:      $('#wemp_pmla').val(),
                // wtema:          $('#wtema').val(),
                consultaAjax:   '',
                accion:         'actualizarDesdeUnix',
                form:           '',
                us:             u
            }
            ,function(data) {
                if(data.error == 1)
                {
                    alert(data.mensaje)
                    $("#img_det").show(1500);
                    $("#img_bus").hide(1000);
                }
                else
                {
                    $("#img_fin").show(2000);
                    $("#img_bus").hide(1500, function() {
                            var msj =   'Empleados - Actualizo: '+data.actualizados+'\n'
                                        +'Empleados - Inserto: '+data.insertados+'\n\n'
                                        +'EPS\'s - Actualizo: '+data.actualizados_eps+'\n'
                                        +'EPS\'s - Inserto: '+data.insertados_eps+'\n'
                                        +'Cargos - Actualizo: '+data.actualizados_cargos+'\n'
                                        +'Cargos - Inserto: '+data.insertados_cargos+'\n'
                                        +'Contratos: '+data.contratos+'\n'
                                        +'Insertados arbol de relacion: '+data.insertados_arbol_relacion+'\n'
                                        +'Cco sin coordinador : '+data.insertados_cco_sincoordinador;
                            alert(msj);
                    });
                }
            },
            "json"
        );
    }

    function seleccionaInactivo()
    {
        $('input:radio[id=westadoR]').attr('checked','checked');
        $('#std_inactivo').css({'color':'orange','font-weight':'bold'});
        $('#std_inactivo').hide();
        $('#std_inactivo').show(2000,function() {
                                    $('#std_inactivo').hide();
                                    $('#std_inactivo').show(1000,function() {
                                                                $('#std_inactivo').css({'color':'','font-weight':''});
                                                            }
                                    );
                                }
        );
    }

    function resetFechasInOut()
    {
        $('#wfechaingreso').val('');
        $('#wfecharetiro').val('');
    }
    </script>

    <script language="Javascript">
        Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfechaingreso',button:'btnwfechaingreso',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
    </script>
    <script language="Javascript">
        Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecharetiro',button:'btnwfecharetiro',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
    </script>

    <style type="text/css">
        A   {text-decoration: none;color: #000066;}
        .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px }
        .tipo3V:hover {color: #000066; background: #999999;}

        .brdtop {
            border-top-style: solid; border-top-width: 2px;
            border-color: #2A5BD0;
        }
        .brdleft{
            border-left-style: solid; border-left-width: 2px;
            border-color: #2A5BD0;
        }
        .brdright{
            border-right-style: solid; border-right-width: 2px;
            border-color: #2A5BD0;
        }
        .brdbottom{
            border-bottom-style: solid; border-bottom-width: 2px;
            border-color: #2A5BD0;
        }

        .alto{
            height: 140px;
        }

        .vr
        {
            display:inline;
            height:50px;
            width:1px;
            border:1px inset;
            /*margin:5px*/
            border-color: #2A5BD0;
        }

        .bgGris1{
            background-color:#F6F6F6;
        }

        .tbold{
            font-weight:bold;
            text-align:left;
        }
        .alng{
            text-align:left;
        }
        .img_fondo{
            background: url('../../images/medical/tal_huma/fondo.png');
            background-repeat: no-repeat;
        }
        .disminuir{
            font-size:11pt;
        }
        .imagen { width: 250px; height: auto;}
        .btnActivo { background-color: #0033ff; }
        .padding_info{
            padding-bottom: 4px;
        }
    </style>
</head>
<body>
<input type="hidden" id="wuse_listado" name="wuse_listado" value="<?php echo ((!empty($wuse_listado))? $wuse_listado: ''); ?>" >
<div id="actualiza" class="version" style="text-align:right;" >Subversi&oacute;n: <?php echo $wactualiz; ?></div>
<table>
    <tr>
        <td>
            <div id="contenedor_programa_buscame" align="center">
                <div align="center" id="seccion_buscar">
                    <form method="post" action="" name="empleados_compania" id="empleados_compania">
                    <table width="226px;" border="0">
                               
                        <tr>
                        <td align="center" class="fila2">
                                    <a onClick="javascript:verBuscar('cont_buscar');" href="#">&nbsp;[<img height="12" border="0" src="../../images/medical/HCE/lupa.PNG">]Buscar..
                                    <img width="10 " height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif">
                                    </a>
                        </td>
                        </tr>
                                       
                        <tr>
                            <td align="center">
                                <div align="center" style="border:2px solid #E4E4E4; display:none;" id="cont_buscar">
                                    <input type="hidden" value="demo" id="wformulario" name="wformulario">
                                    <br>
                                     <?php
                                        //Inactivar el buscar. 
                                        $permisoAdmin = consultarSiEsAdmin($conex, $wemp_pmla, $wtema, $wcodtab, $user_session);
                                        if(count($permisoAdmin) > 0 && $permisoAdmin['esAdmin'] == 'on') 
                                        // si el que esta consultando tiene permiso de administrador para buscame como administrador le deja ver todos los datos
                                        {
                                      ?>
                                            <div align="center" style="display:block;" id="paraAdmin">
                                                <br>
                                                <div id="cont_actualizar" align="center" style="font-size:10px;font-family:Verdana, Arial, Helvetica, sans-serif;width:274px;" class="fila2">Actualizar Matrix<br />[Activo solo para Administradores], <span style="font-weight:bold; cursor:pointer; color:#FF9900; border-bottom: 1px solid #FF9900;" onClick="verElementoTal('ver_opcion');">Ver</span></div>

                                                <table width="650px" border="0">
                                                    <tr>
                                                        <td align="center">
                                                            <div align="center" style="display:none;" id="ver_opcion">
                                                                <table>
                                                                    <tr class="fila1">
                                                                        <td align="center" style="font-weight:bold;">Actualizar registros en Matrix</td>
                                                                        <td align="center"><input type="button" value="Aceptar" onClick="actualizarMatrix('<?=$user_session?>'); return false;" id="aceptar"></td>
                                                                    </tr>
                                                                    <tr class="fila2">
                                                                        <td align="center" class="parrafoTal">[?] Tenga en cuenta que esta opci&oacute;n puede tardar varios minutos.</td>
                                                                        <td width="250px;" align="left" style="padding-left: 5px;">
                                                                            <div style="display:inline;font-weight:bold;">
                                                                                <div id="img_esp">Aceptar para inicar..</div>
                                                                                <div style="display:none;" id="img_bus">Actualizando en Matrix.. <img width="13" height="13" border="0" src="../../images/medical/ajax-loader9.gif"></div>
                                                                                <div style="display:none;" id="img_fin">Termin&oacute; actualizaci&oacute;n <img width="13" height="13" border="0" src="../../images/medical/root/grabar.png"></div>
                                                                                <div style="display:none;" id="img_det">Proceso detenido <!-- <img width='13' height='13' border='0' src='../../images/medical/root/root/borrar.png' /> -->
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="center" class="fila1" colspan="2">
                                                                            <span style="font-weight:bold; cursor:pointer; color:#FF9900; border-bottom: 1px solid #FF9900;" onClick="ocultarElementoTal('ver_opcion');">Cerrar opci&oacute;n</span>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                     <?php
                                    }
                                    ?>
                                    <br />
                                    <table width="400px" border="0">
                                        <tr>
                                            <td align="center" class="encabezadoTabla"><font size="4">C&oacute;digo</font></td>
                                            <td align="center" class="encabezadoTabla"><font size="4">C&eacute;dula</font></td>
                                            </tr>
                                        <tr>
                                            <td align="center" class="fila2"><input type="text" value="" id="wcodigo" name="wcodigo" size="15" onKeyPress="enterBuscarOk(event);"></td>
                                            <td align="center" class="fila2"><input type="text" value="" id="wced" name="wced" size="15" onKeyPress="enterBuscarOk(event);"></td>
                                            </tr>
                                    </table>

                                    <br />

                                    <table width="800px" border="0">
                                        <tr class="encabezadoTabla">
                                            <td align="center" colspan="6">FILTROS ADICIONALES</td>
                                        </tr>
                                        <tr class="fila2 tbold">
                                            <td align="left" class="encabezadoTabla">Primer nombre</td>
                                            <td align="center"><input type="text" value="" id="wnombre1" name="wnombre1" size="15" onKeyPress="enterBuscarOk(event);"></td>
                                            <td align="left" class="encabezadoTabla">Nombre Centro Costo</td>
                                            <td align="center">
                                                <table cellspacing="0" cellpadding="0" border="0">
                                                    <tr>
                                                        <td>
                                                            <img width="12 " height="12" border="0" src="../../images/medical/HCE/lupa.PNG" title="Busque el nombre o parte del nombre del centro de costo">
                                                        </td>
                                                        <td>
                                                            <input type="text" onBlur="recargarLista(this,'wccostos','load_costo');cambioImagen('ccload','ccsel');" onFocus="cambioImagen('ccsel','ccload');" onKeyPress="return enterBuscar(this,'wccostos','','load_costo',event);" value="" id="wnombrecc" name="wnombrecc" size="53">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr class="fila1 tbold">
                                            <td align="left" class="encabezadoTabla">Segundo nombre</td>
                                            <td align="center"><input type="text" value="" id="wnombre2" name="wnombre2" size="15" onKeyPress="enterBuscarOk(event);"></td>
                                            <td align="left" class="encabezadoTabla">C&oacute;digo Centro Costo</td>
                                            <td align="center">
                                                <table cellspacing="0" cellpadding="0" border="0">
                                                    <tr>
                                                        <td>
                                                            <div id="ccsel"><img width="10 " height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif" title="Seleccione un centro de costos"></div>
                                                            <div style="display:none;" id="ccload"><img width="10 " height="10" border="0" src="../../images/medical/ajax-loader9.gif"></div>
                                                        </td>
                                                        <td>
                                                            <select style="width:345px;" id="wccostos" name="wccostos">
                                                                <?php echo $centro_costos; ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr class="fila2 tbold">
                                            <td align="left" class="encabezadoTabla">Primer apellido</td>
                                            <td align="center"><input type="text" value="" id="wapellido1" name="wapellido1" size="15" onKeyPress="enterBuscarOk(event);"></td>
                                            <td align="left" class="encabezadoTabla">Nombre cargo</td>
                                            <td align="center">
                                                <table cellspacing="0" cellpadding="0" border="0">
                                                    <tr>
                                                        <td>
                                                            <img width="12 " height="12" border="0" src="../../images/medical/HCE/lupa.PNG" title="Busque el nombre o parte del nombre del cargo">
                                                        </td>
                                                        <td>
                                                            <input type="text" onBlur="recargarLista(this,'wccargo','load_cargos');cambioImagen('cgoload','cgosel');" onFocus="cambioImagen('cgosel','cgoload');" onKeyPress="return enterBuscar(this,'wccargo','','load_cargos',event);" value="" id="wnombrecargo" name="wnombrecargo" size="53">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr class="fila1 tbold">
                                            <td align="left" class="encabezadoTabla">Segundo apellido</td>
                                            <td align="center"><input type="text" value="" id="wapellido2" name="wapellido2" size="15" onKeyPress="enterBuscarOk(event);"></td>
                                            <td align="left" class="encabezadoTabla">C&oacute;digo cargo</td>
                                            <td align="center">
                                                <table cellspacing="0" cellpadding="0" border="0">
                                                    <tr>
                                                        <td>
                                                            <div id="cgosel"><img width="10 " height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif" title="Seleccione un cargo"></div>
                                                            <div style="display:none;" id="cgoload"><img width="10 " height="10" border="0" src="../../images/medical/ajax-loader9.gif"></div>
                                                        </td>
                                                        <td>
                                                            <select style="width:345px;" id="wccargo" name="wccargo">
                                                                <?php echo $cargos; ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr class="fila2">
                                            <td align="left" class="encabezadoTabla tbold">G&eacute;nero</td>
                                            <td align="left" class="tbold">
                                                <input type="radio" value="F" id="wsexoF" name="wsexo">Femenino<br>
                                                <input type="radio" value="M" id="wsexoM" name="wsexo">Masculino<br>
                                                <input type="radio" checked="checked" value="*" id="wsexoA" name="wsexo">Fem. y Masc.
                                            </td>
                                            <td align="left" class="encabezadoTabla tbold">Estado</td>
                                            <td align="left" class="tbold">
                                                <input type="radio" checked="checked" value="A" id="westadoA" name="westado">Activo<br>
                                                <input type="radio" value="R" id="westadoR" name="westado"><span id='std_inactivo'>Inactivo</span><br>
                                                <input type="radio" value="*" id="westadoAR" name="westado">Ambos
                                            </td>
                                        </tr>
                                        <tr class="fila1 tbold">
                                            <td align="center" class="" colspan='4'>
                                                <table><tr><td style='border:#f2f2f2 solid 1px;'>
                                                    <input type="radio" checked="checked" value="" id="wningunoinout" name="wingresoretiro" onclick="resetFechasInOut();"> Niguno
                                                    <input type="radio" value="in" id="wingreso" name="wingresoretiro"> Ingreso
                                                    <input type="radio" value="out" id="wretiro" name="wingresoretiro" onclick="seleccionaInactivo();"> Retiro&nbsp;&nbsp;&nbsp;&nbsp;
                                                    Fecha inicial <input type="text" value="" id="wfechaingreso" name="wfechaingreso" size="9" onKeyPress="enterBuscarOk(event);">
                                                    &nbsp;<button id='btnwfechaingreso' style='height:21px;width: 21px;'>..</button>
                                                    Fecha final <input type="text" value="" id="wfecharetiro" name="wfecharetiro" size="9" onKeyPress="enterBuscarOk(event);">
                                                    &nbsp;<button id='btnwfecharetiro' style='height:21px;width: 21px;'>..</button>
                                                </td></tr></table>
                                            </td>
                                        </tr>
                                        <tr class="fila2">
                                            <input type="hidden" value="" name="buscar" id="buscar">
                                            <td align="center" colspan="4"><input type="button" value="Buscar.." onClick="javascript:ocultarBuscar('cont_buscar'); setWuse(); generarBusqueda('','');" name="find" id="find"></td>
                                        </tr>
                                    </table>
                                    <input type="hidden" value="03636" name="wlistabuscame" id="wlistabuscame">
                                    <input type="hidden" value="" name="varios" id="varios">
                                    <input type="hidden" value="1" name="wpestanas" id="wpestanas">
                                </div>
                            </td>
                        </tr>
                    </table>
                    </form>
                    <br>
                    <div id="div_varios_encontrados">
                    </div>
                </div>

                <br />

                <div align="center" id="div_navegar">
                </div>

                <br />
                <div align="center" id="div_marco_info_empleado">
                    <table align="center" cellspacing="0" cellpadding="0" border="0" style="text-align: left; width: 950px;">
                        <tr>
                            <td width="150px;" align="center" class="encabezadoTabla">Empleado</td>
                            <td class="">&nbsp;</td>
                            <td class="">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <table border="0" align="center" cellpadding="0" cellspacing="0" style="text-align: left; width: 950px;">
                        <tr>
                            <td class="brdtop brdleft bgGris1">&nbsp;</td>
                            <td class="brdtop bgGris1">&nbsp;</td>
                            <td class="brdtop brdright bgGris1">&nbsp;</td>
                        </tr>
                        <tr class="">
                            <td width="20px" class="brdleft bgGris1">&nbsp;</td>
                            <td width="950px;" align="center" class="encabezadoTabla">
                                <div id="nombre_emp" style="text-align: center; font-weight: bold; font-size:22pt;">&nbsp;</div>
                            </td>
                            <td width="25px" class="brdright bgGris1">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="brdleft bgGris1" colspan="1">&nbsp;</td>
                            <td width="" align="center" class="fila2">
                                <table width="950" height="515" cellspacing="0" cellpadding="0" border="0" class="img_fondo">
                                    <tr>
                                        <td width="116">&nbsp;</td>
                                        <td width="119">&nbsp;</td>
                                        <td width="19">&nbsp;</td>
                                        <td width="129">&nbsp;</td>
                                        <td align="center" rowspan="3" colspan="2"><img width="120" height="57" src="../../images/medical/root/clinica.jpg"></td>
                                    </tr>
                                    <tr>
                                        <td align="center" colspan="4"><font size="6" style="font-weigth:bold;">Clinica las Am&eacute;ricas</font></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td width="160">&nbsp;</td>
                                        <td width="155">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td align="center" colspan="2">
                                            <div id="div_foto_empleado"><img src="../../images/medical/tal_huma/siluetaM.png" class="imagen"></div>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td colspan="3" class="disminuir" valign="top">
                                            <div id="div_info_datos">&nbsp;</div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="brdright bgGris1">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="20px" class="brdleft bgGris1">&nbsp;</td>
                            <td width="950px;" align="center" class="encabezadoTabla">&nbsp;</td>
                            <td width="25px" class="brdright bgGris1">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="brdleft bgGris1">&nbsp;</td>
                            <td align="center" class="bgGris1" colspan="1">&nbsp;</td>
                            <td class="brdright bgGris1">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="brdleft brdbottom bgGris1">&nbsp;</td>
                            <td class="brdbottom bgGris1">&nbsp;</td>
                            <td class="brdbottom brdright bgGris1">&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
    </tr>
</table>
</body>
</html>