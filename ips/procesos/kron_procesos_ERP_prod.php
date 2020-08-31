<?php
include_once("conex.php");
echo "INICIO script: ".date("Y-m-d H:i:s")."<br>";

/*
 ****************** ACTUALIZACIONES MATRIX-UNIX **************************
 ****************************** DESCRIPCIÓN ***************************************************
 * Este script es solo una copia de las funciones que estaban en el cron hce/procesos/pacientes_unix_matrix de tal forma que puedan ser ejecutadas de manera independiente
 * Sucedió que se estaba presentando un error por un dato nulo en pacientes unix matrix que estaba deteniendo todos los demás procesos que se pasaron a este nuevo cron.
 * LLAMADO PARA LA FUNCION DE ACTUALIZAR MEDICAMENTOS DE MATRIX A UNIX
 * Actualización de cargos de ingresos inactivos
 * Actualizar turnos de urgencias que se hayan borrado.
 *************************************************************************************************
 * Autor derivación: Edwar Jaramillo
 * Fecha creacion: 2016-07-15
 *************************************************************************************************
 * MODIFICACIONES
 *************************************************************************************************
 * 2017-06-28  Edwar Jaramillo:
    Actualización en la función "actualizarMedicamentos" para que mediante un parámetro en root_51 la consulta principal se pueda modificar
    el campo de consulta de fecha y cantidad de días de la consulta hacia atras a partir de la fecha actual.
 * 2017-03-06  Edwar Jaramillo:
    Se obliga a que no se actualice cliame_106 cuando no encontró número de línea, pues pueden ocurrir casos en que no se integren de una vez todos los insumnos
    por tanto el número de línea en unix va a estar vacío, se marcaría ingreso inactivo actualizado en unix y es un error.
 * 2016-07-28  Edwar Jaramillo:
    Se eliminan secciones de código que aun pertenecían al cron pacientes_unix_matrix y que aquí no tienen efecto.
    Modificaciones relacionadas con la actualización de cargos unix para ingresos inactivos:
        - A parte de actualización de número de ingreso en unix por el número de ingreso inactivo (recordar que inicialmente se liquida desde Mx con el ingreso activo seguiente al correcto), se modifican las fechas de los cargos unix por la fecha de la cirugía del ingreso inactivo (menos cuando se cambió de mes, en ese caso quedan minimo con la fecha del ultimo ingreso activo en unix al momento de liquidar el ingreso inactivo), Si entre los dos ingresos (activo-inactivo) en unix hay diferentes responsables, entonces se tiene que actualizar las tarifas (valor unitario, total, excedente), si es el caso en que en matrix era un producto entonces en unix se graba descompuesto en varios cargos, a esos nuevos cargos se les debe calcular la tarifa correspondinte para el responsable del ingreso inactivo, si era un caso NO POS entonces se debe calcular el valor no con la tarifa no pos sino con la tarifa pos.
        - Se valída que para la fuente y documento de la actualización no se haya facturado ningún cargo, si ya se facturo alguno no se debe continuar con la liquidación.
        - Actualización de la tabla unix ivdrodet que antes no se estaba actualizando.
 * 2016-06-16  Edwar Jaramillo:
 *             Actualizar ingreso correcto cargos y RIPS en unix de insumos que desde matrix se grabaron a un ingreso inactivo pero
 *             que el integrador grabó a unix con el ingreso activo en ese momento de la grabación a unix., para este procesos se crearon las funciones
 *             fn> queryCargoUnix
 *             fn> actualizarConsecutivosRipsCargos
 *             fn> consultarCargoInsumoPorLinea
 *             fn> consultaCargosUnix
 *             fn> registroLogErrorCRON
 *             fn> actualizarCargosIngresosInactivosUnix
 *************************************************************************************************
 * 2016-04-11  Jerson Andres Trujillo . Se agrega la funcion recuperarTurnosDeUrgencias().
 *             Que busca los pacientes de urgencias que se le hayan borrado el turno, para volver a asignárselo.
 *************************************************************************************************
 * 2016-01-20  Felipe Alvarez Sanchez . Se modifica la funcion  ActualizarMedicamentos en su flujo ppal , con el fin de optimizar
 *             el procedimiento e ir menos a unix
 *
 *************************************************************************************************
 * 2015-03-25 - Se agrega la funcion ActualizarMedicamentos esto con el fin de actualizar los medicamentos (facturable o no facturable)
 segun lo que se    grabo en matrix debe quedar en unix
 *************************************************************************************************
*/

include_once("root/comun.php");
//include_once("movhos/movhos.inc.php");
//include_once("root/magenta.php");

/********************************************************************************************
****************************** INICIO APLICACIÓN ********************************************
********************************************************************************************/
$wbasedato = "";
$wactualiz = " Julio 28 de 2016 ";

$wuser = "movhos";
$seguridad = "movhos";

//Variable para determinar la empresa
if(!isset($wemp_pmla))
{
    $wemp_pmla = '01';
}

//Conexion base de datos Matrix
$conex = obtenerConexionBD("matrix");
$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
conexionOdbc($conex, $wbasedato, &$conexUnix, 'facturacion');
global $conexUnix;

/*********************************************************************
************** FUNCIONES  **********************
*********************************************************************/

/**********************************************************************
* Actualizar medicamentos y materiales Unix segun reglas de Matrix ( segun Manuales de cirugia)
---Autor: Felipe Alvarez sanchez
---Descripcion:  Esta funcion actualiza los medicamentos cuando se graban por el sistema de facturacion
--               inteligente
**********************************************************************/
function actualizarMedicamentos($wemp_pmla)
{
    global $conex;
    global $conexUnix;
    $fecha1 = time();

    $campo_rango_consulta_ActMed_ERP = consultarAliasPorAplicacion($conex, $wemp_pmla, "campo_rango_consulta_ActMed_ERP");
    $wbasedato                       = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");
    $wbasedato_mov                   = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

    $explode_campo_rango       = explode(":", $campo_rango_consulta_ActMed_ERP);
    $campo_fecha_consulta      = 'Tcarfec';
    $campo_rango_dias_consulta = 20;
    if(count($explode_campo_rango) == 2)
    {
        $campo_fecha_consulta      = trim($explode_campo_rango[0]);
        $campo_rango_dias_consulta = trim($explode_campo_rango[1])*1;
    }

    $fechaant = date( "Y-m-d",time()-3600*(24*$campo_rango_dias_consulta) ); // se le restan 20 dias a la fecha actual

    // se consulta los registros que no esten actualizados en unix (Tcaraun !=on ) y que muevan inventario (Tcardoi !='')
    // ademas de estos se va a la tabla movhos_000158 para mirar si el articulo fue reemplazado por otro o por otros componentes
    // tambien va a la movhos_000003 para ver el estado del cargo.
    // ademas se le agrega en el where   que los registros que traigan sean mayores a fechaant
    // se hizo en una sola consulta optimizar el query y que no fuera muchas veces a consultar estos datos
    /*$sqlppal = "SELECT Tcardoi,Tcarlin,Tcarfac , ".$wbasedato."_000106.id, Tcarprocod ,Fdeubi,Logdoc , Logpro , Fenfue
                  FROM ".$wbasedato."_000106
                  LEFT JOIN  ".$wbasedato_mov."_000158 ON ( Tcardoi = Logdoc AND Tcarlin = Loglin) ,
                             ".$wbasedato_mov."_000003 , ".$wbasedato_mov."_000002
                 WHERE  Tcardoi !=''
                   AND  ".$wbasedato."_000106.Fecha_data  between '2016-07-01' AND  '2016-07-15'
                   AND  Tcardoi = Fdenum
                   AND  Tcarlin = Fdelin
                   AND  Tcardoi = Fennum" ;*/

	 $sqlppal = "SELECT Tcardoi,Tcarlin,Tcarfac , {$wbasedato}_000106.id, Tcarprocod ,Fdeubi,Logdoc , Logpro , Fenfue
                  FROM {$wbasedato}_000106
                  LEFT JOIN  {$wbasedato_mov}_000158 ON ( Tcardoi = Logdoc AND Tcarlin = Loglin) ,
                             {$wbasedato_mov}_000003 , {$wbasedato_mov}_000002
                 WHERE  Tcardoi !=''
                   AND  Tcaraun !='on'
                   AND  {$wbasedato}_000106.{$campo_fecha_consulta} > '{$fechaant}'
                   AND  Tcardoi = Fdenum
                   AND  Tcarlin = Fdelin
                   AND  Tcardoi = Fennum" ;

    $res = mysql_query( $sqlppal, $conex  ) or die( mysql_errno()." - Error en el query $sqlppal - ".mysql_error() );
    $num = mysql_num_rows( $res );

    //Se construye  un array  ($rows)  con el resultado de la consulta anterior, con el fin de que el proceso, continue
    //recorriendose el array y no tener una conexion abierta.
    // Al array se le añade una posicion llamada clave y otra documentounix en este documentounix se guardara luego el ducumento
    // correspondiente en unix
    $clave = 0; // se utiliza para ponerle una clave al vector
    while($row = mysql_fetch_array($res))
    {
        $clave ++;
        $row['clave'] = $clave;
        $row['documentounix'] = ''; // se crea la posicion documentounix que posteriormente sera llenada
        $rows[] = $row;
    }
    //--------------------------------------------------------------

    $contador = 0; // este contador es para llevar un control de las idas a unix. no influye en el programa


    // En este proceso se va a unix por la informacion del documento con matrix.
    // Se optimizo yendo pocas veces a unix utilizando un vector (arr_itdrodoc) , si el documento con la fuente de matrix ya fue consultado en unix
    // se graba en un vector la respuesta. Asi antes de ir a Unix se mira si ya se tiene en el vector, ahorrando idas a unix
    // Ademas de esto la posicion del vector rows[documentounix] se llena con el documento consultado.
    $arr_itdrodoc   = array(); // vector documentos matrix , en su valor trae el documento unix

    // se hace siempre y cuando clave sea diferente de cero
    if($clave !=0)
    {
        // se recorre todo el vector resultado de la consulta ppal (sqlppal)
        foreach($rows as &$row)
        {

            $documentoppal1 =''; // variable auxiliar para ir llenando el vector  arr_itdrodoc

            // se pregunta si la posicion de del array no se encuentra y si cumple esta condicion hace la consulta en unix
            // si no la cumple (que quiere decir que el elemento ya se encuentra no consulta en unix
            if( !isset($arr_itdrodoc[$row['Fenfue']][$row['Tcardoi']]) )
            {
                $arr_itdrodoc[$row['Fenfue']][$row['Tcardoi']] = "";

                // se consulta en drodocdoc el documento con que se grabo a Unix , la tabla ITDRODOC
                // es una tabla puente entre Matrix y Unix  averiguo con los datos de Matrix con que documento y fuente
                // quedo en unix y sigo trabajando con este.
                $sqlu = "SELECT drodocdoc
                           FROM ITDRODOC
                          WHERE drodocnum  = '".$row['Tcardoi']."'
                            AND drodocfue  = '".$row['Fenfue']."'";
                $resu = odbc_do( $conexUnix, $sqlu );


                if( odbc_fetch_row($resu))
                {
                    $documentoppal1 = odbc_result($resu,1); // documentoppal1 se iguala al resultado encontrado
                    $arr_itdrodoc[$row['Fenfue']][$row['Tcardoi']] = $documentoppal1; // se llena la posicion con el documentoppal11
                    $row['documentounix'] = $documentoppal1; // la posicion del vector rows se llena con el documentounix

                }


            }
            else
            {
                // Si no se consulta  se  busca la posicion y se asigna a documentoppal  y  se le asigna a la posicion del vector rows documentounix
                $documentoppal1 = $arr_itdrodoc[$row['Fenfue']][$row['Tcardoi']];
                $row['documentounix'] = $documentoppal1;
            }

        }

        //-----------------------------------------------------------------------------

        // Este proceso se utiliza para crear una consulta  unix por fuente de documentos que haya .
        // Utilizando el vector antes construido arr_itdrodoc
        // Este vector tiene  por cada Fuente , todos los documentos correspondientes a esta, es decir:
        //          hay articulos para actualizar  y entre todos estos articulos hay 2 Fuentes , la 11 y la AP
        //          se hacen dos consultas a unix una donde estan todos los documentos correspondientes a la fuente 11 y en otra
        //          todos los documentos correspondiente a la fuente AP
        // Nota : todo esto se crea para optimizar el proceso.

        $strin_in = array(); // array que se utiliza para construir el IN de la consulta, aqui se guardaran todos los documentos
                             // separados por coma por cada una de las fuentes.

        // se recorre el array arr_itdrodoc por las fuentes que existan
        foreach ( $arr_itdrodoc as $keyfuente => $fuente  )
        {
            // Se recorre el array con su fuente correspondiente por cada uno de los documentos
            foreach($fuente as $key => $valor)
            {
                // En el vector  strin_in  queda como clave la fuente y en su valor todos los documentos
                // separados por coma.
                $strin_in[$keyfuente] = $strin_in[$keyfuente].",'".$valor."'";
            }

        }


        // Acontinuacion se hara un proceso para  hacer las consultas a unix utilizando el vector $strin_in para su debida
        // construccion y los resultados se almacenaran en el vector  arrayresultadosunix .
        //
        $r=0;
        $arrayresultadosunix = array(); // Este vector se crea para almacenar los resultados de las consulta en unix
        // se recorre el vector $strin_in
        foreach($strin_in as $key => $valor)
        {
            $valor=substr($valor,1);
            // consulta donde se agrupan todos los documentos de una fuente determinada. Es decir
            // por la fuente que seria key , se pone el valor del strin_in que son todos los documentos de esa fuente
            // separados por coma
            $selectiv   = " SELECT drodetfac,drodetart,drodetfue,drodetdoc,drodetite
                                          FROM IVDRODET
                                         WHERE drodetfue = '".$key."'
                                           AND drodetdoc IN ( ".$valor.")";

            $resiv = odbc_do( $conexUnix, $selectiv );


            while (odbc_fetch_row($resiv))
            {

                $drodetfac = odbc_result($resiv,1); // se guarda el resultado de si es facturable o no (drodetfac)
                $drodetart = odbc_result($resiv,2); // se guarda el resultado del articulo (drodetart)
                $drodetfue = odbc_result($resiv,3); // se guarda el resultado de la fuente (drodetfue)
                $drodetdoc = odbc_result($resiv,4); // se guarda el resultado del documento (drodetdoc)
                $drodetite = odbc_result($resiv,5); // se guarda el resultado de la linea o ite llamado en unix (drodetite)
                $arrayresultadosunix[$drodetfue][$drodetdoc][$drodetite]['articulo']  = $drodetart; // se llena la posicion del vector arrayresultadosunix[fuente][documento][linea][articulo] con el articulo
                $arrayresultadosunix[$drodetfue][$drodetdoc][$drodetite]['facturable']= $drodetfac; // se llena la posicion del vector arrayresultadosunix[fuente][documento][linea][facturable] con la condicion si o no facturable
            }

        }
    }
    //---------------------------------------------------------------------
    //--------------------------------------------------------------------


    //-----
    //Recorrido ppal , aqui se realizan las operaciones de  actualizar la condicion de facturable o no  de los medicamentos,
    //en las tablas  IVDRODET y FACARDET.
    //Tiene varios flujos
    //1- Medicamento no se parte en varios medicamentos ni se reemplaza por otro , ademas de esto la linea en matrix es igual a la de unix
    //   corresponde a la mayoria de los casos
    //2- Medicamento no se parte en varios medicamentos ni se reemplaza por otro , las lineas en matrix no corresponden a las de unix
    //3- Medicamento se parte en varios medicamentos o se reemplaza por otro , Las lineas corresponden tanto en matrix como en unix
    //4- Medicamento se parte en varios medicamentos o se reemplaza por otro , Las lineas no corresponde
    if($clave !=0)
    {
        foreach($rows as $row)
        {

            //--Se trae el estado, del vector ppal $row['Fdeubi']
            $estado ='';
            $estado =  $row['Fdeubi'];
            //--------------------------

            // el proceso solo sigue si se encuentra en estado UP = procesado a unix , US = Unix sin procesar (esto es porque quedan muchos
            if ($estado =='UP' || $estado =='US')
            {

                //--------------------------------------------------------------
                // valido si tiene regla que divide un articulo entre varios en la tabla movhos_000158
                // a veces algunos medicamentos se parten en varios componentes y pasan a unix divididos ,
                // entonces hay que hacer un analisis particular para estos
                if ($row['Logdoc'] !='')
                {
                    $validacion ='si'; // se llena la variable validacion si validacion vale si , es porque tiene una regla (de reemplazo o divide en componentes)
                    if($row['Logpro'] =='on')
                        $esdereemplazo = 'si'; // esdereemplazo  se define si reemplaza articulo por otro
                    else
                       $esdereemplazo = 'no';

                }
                else
                {
                   $validacion ='no';
                   $esdereemplazo ='';
                }
                //
                //-------------------------------------------
                //--Se trae el estado, del vector ppal $row['Fenfue']
                $fuente ='';
                $fuente = $row['Fenfue'];
                //------------------

                // si hay conexion a unix haga
                if( $conexUnix ){
                    $documentoppal = $row['documentounix'];//Se trae el documentoppal, del vector ppal $row['documentounix']
                    if( $documentoppal !='' )
                    {
                            $i = 0;
                            //1- Medicamento no se parte en varios medicamentos ni se reemplaza por otro , ademas de esto la linea en matrix es igual a la de unix
                            //   corresponde a la mayoria de los casos
                            if($validacion!='si')
                            {

                                $articulo =''; // variable auxiliar donde estara el articulo
                                // Si el arrayresultadosunix[fuente][documentoppal][linea]['articulo'] existe no hace la consulta a unix
                                // por lo general todos estos articulos ya se encuentran en este vector pero por salvedad  se tiene el flujo
                                // del else y se hace la consulta , por si llega a no estar en este array se busque en unix
                                if(isset($arrayresultadosunix[$fuente][$documentoppal][$row['Tcarlin']]['articulo']))
                                {
                                     $articulo = $arrayresultadosunix[$fuente][$documentoppal][$row['Tcarlin']]['articulo'];
                                     $drodetart = $articulo; // la variable drodetart se llena con el articulo
                                     $drodetfac = $arrayresultadosunix[$fuente][$documentoppal][$row['Tcarlin']]['facturable']; // La variable drodetfac se llena con la condicion facturable o no
                                }
                                else
                                {
                                    // hago Consulta para encontrar el articulo y si es facturable o no, esta consulta no se ejecutara si en el vector arrayresultadosunix ya se encuentra
                                    $selectiv   = " SELECT drodetfac , drodetart
                                                      FROM IVDRODET
                                                     WHERE drodetfue = '".$fuente."'
                                                       AND drodetdoc = '".$documentoppal."'
                                                       AND drodetite = '".$row['Tcarlin']."'";

                                    $resiv = odbc_do( $conexUnix, $selectiv );
                                    $drodetfac = odbc_result($resiv,1); // se llena la condicion de facturable si o no
                                    $drodetart = odbc_result($resiv,2); // se llena el articulo.
                                    $contador++;// este contador es utilizado para contar las veces que se va a unix


                                }


                                //--Se comparan los articulo de matrix con los de unix , aveces no coinciden entonces se tiene que buscar cual es el articulo que corresponde
                                //--Las lineas ya no coincidirian en unix y matrix y se iria por el flujo 2(2- Medicamento no se parte en varios medicamentos ni se reemplaza por otro , las lineas en matrix no corresponden a las de unix)
                                $nodiferenteprocedimiento = true;
                                if( $drodetart != $row['Tcarprocod']  )
                                {
                                    $nodiferenteprocedimiento = false;
                                }
                                //-- si los procedimientos coresponden (articulos )
                                if($nodiferenteprocedimiento)
                                {

                                    if($drodetfac != $row['Tcarfac'])
                                    {
                                        // actualizamos el registro de medicamentos en IVDRODET segun lo que dice en cliame_000106
                                        $sqlupdate2 = " UPDATE IVDRODET
                                                          SET drodetfac = '".$row['Tcarfac']."'
                                                        WHERE drodetfue = '".$fuente."'
                                                          AND drodetdoc = '".$documentoppal."'
                                                          AND drodetite = '".$row['Tcarlin']."'";
                                        $resodbc = odbc_do( $conexUnix, $sqlupdate2 );
                                        $number_of_rows = odbc_num_rows($resodbc);
                                        $contador++;
                                        //$row['Tcarfac']
                                    }

                                    // actualizamos el registro de medicamentos en Facardet segun lo que dice en cliame_000106
                                    $sqlupdate = " UPDATE FACARDET
                                                      SET cardetfac = '".$row['Tcarfac']."'
                                                    WHERE cardetfue = '".$fuente."'
                                                      AND cardetdoc = '".$documentoppal."'
                                                      AND cardetite = '".$row['Tcarlin']."'";
                                    odbc_do( $conexUnix, $sqlupdate );


                                    /*
                                    Acontinuacion se hace una validacion de como quedo los registros en
                                    FACARDET e IVDRODET y se mira si corresponde a lo que esta en la tabla
                                    cliame_000106 si esto es igual se cambia el estado de actualizado en unix
                                    en la tabla cliame_000106
                                    Este proceso , se adiciono debido a inconvenientes en el proceso, "estaban quedando articulos con condiciones diferentes  unix a matrix"
                                    se opto por hacer esta consulta antes de cada actualizacion de estado de articulo actualizado en unix en la Tcaraun
                                    */
                                    //* Enero 20 de 2016 se quita esta validacion por eficienci en unix
                                   $selectfacar   = "  SELECT cardetfac,drodetfac
                                                          FROM IVDRODET , FACARDET
                                                         WHERE drodetfue = '".$fuente."'
                                                           AND drodetdoc = '".$documentoppal."'
                                                           AND drodetite = '".$row['Tcarlin']."'
                                                           AND drodetfue = cardetfue
                                                           AND drodetdoc = cardetdoc
                                                           AND drodetite = cardetite ";

                                    $resfacar = odbc_do( $conexUnix, $selectfacar );
                                    $cardetfac = odbc_result($resfacar,1);
                                    $drodetfac = odbc_result($resfacar,2);



                                    // si los registros en facardet e ivdrodet y cliame_000106 son iguales
                                    // actualizo en la tabla cliame_000106  el campo Tcaraun igual a on y asi
                                    // queda por terminada la transaccion

                                    //* Enero 20 de 2016 se quita este if
                                    if($row['Tcarfac'] == $cardetfac && $row['Tcarfac']== $drodetfac)
                                    {
                                        $sql3 = "   UPDATE ".$wbasedato."_000106
                                                      SET Tcaraun = 'on',
														  Tcarlun = '".$row['Tcarlin']."',
														  Tcardun = '".$documentoppal."',
														  Tcarfun = '".$fuente."'
                                                    WHERE  id = '".$row['id']."'";
                                        mysql_query( $sql3, $conex  ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );

                                    }

                                }
                                else
                                {
                                    // hago Consulta para luego validar si los articulos son iguales y si  puedo actualizar o no
                                    $selectiv   = " SELECT drodetite
                                                      FROM IVDRODET
                                                     WHERE drodetfue = '".$fuente."'
                                                       AND drodetdoc =  '".$documentoppal."'
                                                       AND drodetart =  '".$row['Tcarprocod']."'
													   AND drodetite >= '".$row['Tcarlin']."'";

                                    $resiv = odbc_do( $conexUnix, $selectiv );
                                    //$drodetfac = odbc_result($resiv,1);
                                    //$drodetart = odbc_result($resiv,2);
                                    $drodetlinea = odbc_result($resiv,1);


                                    if($drodetlinea=='')
                                    {


											 $sqlppal2 ="SELECT  Tcarlun
														  FROM ".$wbasedato."_000106
														 WHERE  ".$wbasedato."_000106.Tcarfec > '".$fechaant."'
														   AND  Tcardoi 	= '".$row['Tcardoi']."'
														   AND  Tcarprocod 	=  '".$row['Tcarprocod']."' ";
												$reslineas = mysql_query( $sqlppal2, $conex  ) or die( mysql_errno()." - Error en el query $sqlppal - ".mysql_error() );
												$num = mysql_num_rows( $reslineas );
												$aux = "''";
												$u=0;
												while($rowlineas = mysql_fetch_array($reslineas))
												{
													if($u==0)
													{
														$aux ="'".$rowlineas['Tcarlun']."'";
													}
													else
														$aux .=",'".$rowlineas['Tcarlun']."'";

												}

										// hago Consulta para luego validar si los articulos son iguales y si  puedo actualizar o no
										$selectiv   = " SELECT drodetite
														  FROM IVDRODET
														 WHERE drodetfue = '".$fuente."'
														   AND drodetdoc =  '".$documentoppal."'
														   AND drodetart =  '".$row['Tcarprocod']."'
														   AND drodetite NOT IN ( $aux ) ";

										$resiv = odbc_do( $conexUnix, $selectiv );
										//$drodetfac = odbc_result($resiv,1);
										//$drodetart = odbc_result($resiv,2);
										$drodetlinea = odbc_result($resiv,1);
										if($drodetlinea=='')
										{

										}
										else
										{
											//echo "<br> Entro por aqui ".$sqlppal2."-------".$selectiv;
											// actualizamos el registro de medicamentos en IVDRODET segun lo que dice en cliame_000106
                                            $sqlupdate2 = " UPDATE IVDRODET
                                                              SET drodetfac = '".$row['Tcarfac']."'
                                                            WHERE drodetfue = '".$fuente."'
                                                              AND drodetdoc = '".$documentoppal."'
                                                              AND drodetite = '".$drodetlinea."'";
                                            $resodbc = odbc_do( $conexUnix, $sqlupdate2 );
                                            //$number_of_rows = odbc_num_rows($resodbc);

                                            // actualizamos el registro de medicamentos en Facardet segun lo que dice en cliame_000106
                                            $sqlupdate = " UPDATE FACARDET
                                                              SET cardetfac = '".$row['Tcarfac']."'
                                                            WHERE cardetfue = '".$fuente."'
                                                              AND cardetdoc = '".$documentoppal."'
                                                              AND cardetite = '".$drodetlinea."'";

                                            odbc_do( $conexUnix, $sqlupdate );

                                            /*
                                            Acontinuacion se hace una validacion de como quedo los registros en
                                            FACARDET e IVDRODET y se mira si corresponde a lo que esta en la tabla
                                            cliame_000106 si esto es igual se cambia el estado de actualizado en unix
                                            en la tabla cliame_000106
                                            */

                                            $selectfacar   = "  SELECT cardetfac,drodetfac
                                                                  FROM IVDRODET , FACARDET
                                                                 WHERE drodetfue = '".$fuente."'
                                                                   AND drodetdoc = '".$documentoppal."'
                                                                   AND drodetite = '".$drodetlinea."'
                                                                   AND drodetfue = cardetfue
                                                                   AND drodetdoc = cardetdoc
                                                                   AND drodetite = cardetite ";

                                            $resfacar = odbc_do( $conexUnix, $selectfacar );
                                            $cardetfac = odbc_result($resfacar,1);
                                            $drodetfac = odbc_result($resfacar,2);


                                            // si los registros en facardet e ivdrodet y cliame_000106 son iguales
                                            // actualizo en la tabla cliame_000106  el campo Tcaraun igual a on y asi
                                            // queda por terminada la transaccion
                                            if($row['Tcarfac'] == $cardetfac && $row['Tcarfac']== $drodetfac)
                                            {
                                                $sql3 = "   UPDATE ".$wbasedato."_000106
                                                              SET Tcaraun = 'on',
																  Tcarlun = '".$drodetlinea."',
																  Tcardun = '".$documentoppal."',
																  Tcarfun = '".$fuente."'
                                                            WHERE  id = '".$row['id']."'";
                                                mysql_query( $sql3, $conex  ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );

                                            }
										}


									}
                                    else
                                    {
                                            // actualizamos el registro de medicamentos en IVDRODET segun lo que dice en cliame_000106
                                            $sqlupdate2 = " UPDATE IVDRODET
                                                              SET drodetfac = '".$row['Tcarfac']."'
                                                            WHERE drodetfue = '".$fuente."'
                                                              AND drodetdoc = '".$documentoppal."'
                                                              AND drodetite = '".$drodetlinea."'";
                                            $resodbc = odbc_do( $conexUnix, $sqlupdate2 );
                                            //$number_of_rows = odbc_num_rows($resodbc);

                                            // actualizamos el registro de medicamentos en Facardet segun lo que dice en cliame_000106
                                            $sqlupdate = " UPDATE FACARDET
                                                              SET cardetfac = '".$row['Tcarfac']."'
                                                            WHERE cardetfue = '".$fuente."'
                                                              AND cardetdoc = '".$documentoppal."'
                                                              AND cardetite = '".$drodetlinea."'";

                                            odbc_do( $conexUnix, $sqlupdate );

                                            /*
                                            Acontinuacion se hace una validacion de como quedo los registros en
                                            FACARDET e IVDRODET y se mira si corresponde a lo que esta en la tabla
                                            cliame_000106 si esto es igual se cambia el estado de actualizado en unix
                                            en la tabla cliame_000106
                                            */

                                            $selectfacar   = "  SELECT cardetfac,drodetfac
                                                                  FROM IVDRODET , FACARDET
                                                                 WHERE drodetfue = '".$fuente."'
                                                                   AND drodetdoc = '".$documentoppal."'
                                                                   AND drodetite = '".$drodetlinea."'
                                                                   AND drodetfue = cardetfue
                                                                   AND drodetdoc = cardetdoc
                                                                   AND drodetite = cardetite ";

                                            $resfacar = odbc_do( $conexUnix, $selectfacar );
                                            $cardetfac = odbc_result($resfacar,1);
                                            $drodetfac = odbc_result($resfacar,2);


                                            // si los registros en facardet e ivdrodet y cliame_000106 son iguales
                                            // actualizo en la tabla cliame_000106  el campo Tcaraun igual a on y asi
                                            // queda por terminada la transaccion
                                            if($row['Tcarfac'] == $cardetfac && $row['Tcarfac']== $drodetfac)
                                            {
                                                $sql3 = "   UPDATE ".$wbasedato."_000106
                                                              SET Tcaraun = 'on',
																  Tcarlun = '".$drodetlinea."',
																  Tcardun = '".$documentoppal."',
																  Tcarfun = '".$fuente."'
                                                            WHERE  id = '".$row['id']."'";
                                                mysql_query( $sql3, $conex  ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );

                                            }



                                    }

                                }
                            }
                            else
                            {


                                //2- Medicamento no se parte en varios medicamentos ni se reemplaza por otro , las lineas en matrix no corresponden a las de unix
                                if ($esdereemplazo =='si')
                                {
                                    $querycenpro = "SELECT  Pdeins
                                                      FROM  cenpro_000003
                                                     WHERE  Pdepro ='".$row['Tcarprocod']."'";

                                    $resquerycenpro=  mysql_query( $querycenpro, $conex  ) or die( mysql_errno()." - Error en el query $querycenpro - ".mysql_error() );

                                    $p=-1;
                                    $variablereemplazo    = '';
                                    $auxvariablereemplazo = '';
                                    while($rowquerycenpro = mysql_fetch_array($resquerycenpro))
                                    {
                                        $p++;
                                        $auxvariablereemplazo = $auxvariablereemplazo.",".(($row['Tcarlin']*1) + $p);

                                    }

                                    $variablereemplazo = substr($auxvariablereemplazo,1);
                                     //$variablereemplazo = $auxvariablereemplazo;

                                }
                                else
                                {
                                    $variablereemplazo = $row['Tcarlin'];
                                }


                                // Consulto los medicamentos y veo cuales son su reglas y en cuantos
                                // articulos se parte el medicamento
                                $sqlval          = "  SELECT Logdoc,Loglin,Logaor,Logare
                                                        FROM ".$wbasedato_mov."_000158
                                                       WHERE Logdoc = '".$row['Tcardoi']."'
                                                         AND Loglin IN ( ".$variablereemplazo." ) ";

                                $resval =  mysql_query( $sqlval, $conex  ) or die( mysql_errno()." - Error en el query $sqlval - ".mysql_error() );


                                $bandera = true;
                                while($rowval = mysql_fetch_array($resval))
                                {



                                    // hago Consulta para luego validar si los articulos son iguales y si  puedo actualizar o no
                                    $selectiv   = " SELECT drodetfac , drodetart
                                                      FROM IVDRODET
                                                     WHERE drodetfue = '".$fuente."'
                                                       AND drodetdoc = '".$documentoppal."'
                                                       AND drodetite = '".$rowval['Loglin']."'";

                                    $resiv = odbc_do( $conexUnix, $selectiv );
                                    $drodetfac = odbc_result($resiv,1);
                                    $drodetart = odbc_result($resiv,2);

                                    $diferenteprocedimiento = true;
                                    if( $drodetart != $rowval['Logare']  )
                                    {
                                        $diferenteprocedimiento = false;
                                    }


                                    if($diferenteprocedimiento)
                                    {

                                        //echo "<br> articulo en unix ".$drodetart." articulo matrix ".$rowval['Logare'];
                                        //echo "<br>Partido : ".odbc_result($resu,1)."-".$rowval['Loglin'];
                                        // actualizamos el registro de medicamentos en IVDRODET segun lo que dice en cliame_000106
                                        $sqlupdate2 = "    UPDATE IVDRODET
                                                              SET drodetfac = '".$row['Tcarfac']."'
                                                            WHERE drodetfue = '".$fuente."'
                                                              AND drodetdoc = '".$documentoppal."'
                                                              AND drodetite = '".$rowval['Loglin']."'";
                                        odbc_do( $conexUnix, $sqlupdate2 );

                                        // actualizamos el registro de medicamentos en Facardet segun lo que dice en cliame_000106
                                        $sqlupdate = " UPDATE FACARDET
                                                          SET cardetfac = '".$row['Tcarfac']."'
                                                        WHERE cardetfue = '".$fuente."'
                                                          AND cardetdoc = '".$documentoppal."'
                                                          AND cardetite = '".$rowval['Loglin']."'";
                                        odbc_do( $conexUnix, $sqlupdate );


                                        /*
                                        Acontinuacion se hace una validacion de como quedo los registros en
                                        FACARDET e IVDRODET y se mira si corresponde a lo que esta en la tabla
                                        cliame_000106 si esto es igual se cambia el estado de actualizado en unix
                                        en la tabla cliame_000106
                                        */

                                        // se selecciona el estado de facturable o no facturable en facardet
                                        $selectfacar = "   SELECT cardetfac
                                                            FROM FACARDET
                                                            WHERE cardetfue = '".$fuente."'
                                                              AND cardetdoc = '".$documentoppal."'
                                                              AND cardetite = '".$rowval['Loglin']."'";

                                        $resfacar = odbc_do( $conexUnix, $selectfacar );
                                        $cardetfac = odbc_result($resfacar,1);

                                        // se selecciona el estado del registro en ivdrodet facturable o no facturable
                                        $selectiv   = " SELECT drodetfac
                                                          FROM IVDRODET
                                                         WHERE drodetfue = '".$fuente."'
                                                           AND drodetdoc = '".$documentoppal."'
                                                           AND drodetite = '".$rowval['Loglin']."'";

                                        $resiv = odbc_do( $conexUnix, $selectiv );
                                        $drodetfac = odbc_result($resiv,1);




                                        // si alguno de los estados no es igual en la tabla cliame_000106 en Facardet y en Ivdrodet
                                        // la bandera es false y luego no se hace el update en cliame_000106 indicando que se actualizo
                                        // el registro
                                        if($row['Tcarfac'] == $cardetfac && $row['Tcarfac']== $drodetfac)
                                        {
                                            //$bandera=true;
                                        }
                                        else
                                        {
                                            $bandera=false;
                                        }

                                    }
                                    else
                                    {


                                            // hago Consulta para luego validar si los articulos son iguales y si  puedo actualizar o no
                                            $selectiv   = " SELECT drodetite
                                                              FROM IVDRODET
                                                             WHERE drodetfue = '".$fuente."'
                                                               AND drodetdoc = '".$documentoppal."'
                                                               AND drodetart = '".$rowval['Logare']."' ";

                                            $resiv = odbc_do( $conexUnix, $selectiv );
                                            $drodetlinea = odbc_result($resiv,1);


                                            if($drodetlinea=='')
                                            {

                                            }
                                            else
                                            {
                                                //echo "<br>no linea nula articulo en unix ".$drodetart." articulo matrix ".$rowval['Logare'];

                                                //echo "<br>Partido : ".odbc_result($resu,1)."-".$drodetlinea;
                                                // actualizamos el registro de medicamentos en IVDRODET segun lo que dice en cliame_000106
                                                $sqlupdate2 = "    UPDATE IVDRODET
                                                                      SET drodetfac = '".$row['Tcarfac']."'
                                                                    WHERE drodetfue = '".$fuente."'
                                                                      AND drodetdoc = '".$documentoppal."'
                                                                      AND drodetite = '".$drodetlinea."'";
                                                odbc_do( $conexUnix, $sqlupdate2 );

                                                // actualizamos el registro de medicamentos en Facardet segun lo que dice en cliame_000106
                                                $sqlupdate = " UPDATE FACARDET
                                                                  SET cardetfac = '".$row['Tcarfac']."'
                                                                WHERE cardetfue = '".$fuente."'
                                                                  AND cardetdoc = '".$documentoppal."'
                                                                  AND cardetite = '".$drodetlinea."'";
                                                odbc_do( $conexUnix, $sqlupdate );

                                                /*
                                                Acontinuacion se hace una validacion de como quedo los registros en
                                                FACARDET e IVDRODET y se mira si corresponde a lo que esta en la tabla
                                                cliame_000106 si esto es igual se cambia el estado de actualizado en unix
                                                en la tabla cliame_000106
                                                */

                                                // se selecciona el estado de facturable o no facturable en facardet
                                                $selectfacar = "   SELECT cardetfac
                                                                    FROM FACARDET
                                                                    WHERE cardetfue = '".$fuente."'
                                                                    AND cardetdoc = '".$documentoppal."'
                                                                    AND cardetite = '".$drodetlinea."'";

                                                $resfacar = odbc_do( $conexUnix, $selectfacar );
                                                $cardetfac = odbc_result($resfacar,1);

                                                // se selecciona el estado del registro en ivdrodet facturable o no facturable
                                                $selectiv   = " SELECT drodetfac
                                                                  FROM IVDRODET
                                                                 WHERE drodetfue = '".$fuente."'
                                                                   AND drodetdoc = '".$documentoppal."'
                                                                   AND drodetite = '".$drodetlinea."'";

                                                $resiv = odbc_do( $conexUnix, $selectiv );
                                                $drodetfac = odbc_result($resiv,1);

                                                // si alguno de los estados no es igual en la tabla cliame_000106 en Facardet y en Ivdrodet
                                                // la bandera es false y luego no se hace el update en cliame_000106 indicando que se actualizo
                                                // el registro
                                                if($row['Tcarfac'] == $cardetfac && $row['Tcarfac']== $drodetfac)
                                                {
                                                    //$bandera=true;
                                                }
                                                else
                                                {
                                                    $bandera=false;
                                                }
                                            }



                                    }
                                }
                                if($bandera==true)
                                {

                                    // se actualiza el  registro en la tabla cliame_000106 indicando que el registro esta correctamente actualizado
                                    $sql3 = "   UPDATE ".$wbasedato."_000106
                                                   SET Tcaraun = 'on',
													  Tcarlun = '".$drodetlinea."',
													  Tcardun = '".$documentoppal."',
													  Tcarfun = '".$fuente."'
                                                 WHERE  id = '".$row['id']."'";
                                    mysql_query( $sql3, $conex  ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
                                }


                            }

                    }

                }
            }

        }
    }
    $consultasunix = $contador ;
    $fecha2=  time();

    //echo "<br>ConsultasUnix = ".$consultasunix."<br>Nuevo script Tiempo".( ($fecha2*1)-($fecha1*1) );
    //
    //2014-12-29
    // liberarConexionOdbc( $conexUnix );
    // odbc_close_all();
    //-------------------------------------------------------------------------------------------------------

    /*
    Aqui empieza la devolucion se hizo separada para no agregar mas condiciones a la logica de la
    actualizacion del cargo en unix

    */

    // se seleccionan los cargos que mueven inventario que ya esten actualizados en unix en la entrega y
    // que tengan devolucion pero que el estado en unix sea distinto a on, cuando el estado esta en on
    // indica que ya fue actualizado
    $sql = "SELECT Tcardod,Tcarlid,Tcarfac , id ,Tcarprocod
              FROM ".$wbasedato."_000106
             WHERE  Tcardod != ''
               AND  Tcaraud !='on'
               AND  ".$wbasedato."_000106.Fecha_data > ".$fechaant." ";

    // echo $sql ;
    $res = mysql_query( $sql, $conex  ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    $num = mysql_num_rows( $res );


    while($row = mysql_fetch_array($res))
    {

        $wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

        // se consulta en la movhos_000103 el estado de cada registro
        $sql1 = "SELECT  Fdeubi
                   FROM ".$wbasedato_mov."_000003
                  WHERE  Fdenum = '".$row['Tcardod']."'
                    AND  Fdelin = '".$row['Tcarlid']."' ";
        $res1 = mysql_query( $sql1, $conex  ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );

        $estado ='';
        if($row1 = mysql_fetch_array($res1))
        {
            $estado = $row1['Fdeubi'];
        }



        // solo se actualizan registros que tengan estado US y UP (procesado en unix = UP ) (unix sin procesar US)
        if ($estado =='UP' || $estado =='US')
        {


            //--------------------------------------------------------------
            // valido si tiene regla que divide un articulo entre varios
            $sqlvalidacion = "SELECT Logdoc
                                FROM ".$wbasedato."_000106 ,  ".$wbasedato_mov."_000158
                               WHERE Tcardod = '".$row['Tcardod']."'
                                 AND Tcarlid = '".$row['Tcarlid']."'
                                 AND Tcarprocod = '".$row['Tcarprocod']."'
                                 AND Tcardod = Logdoc
                                 AND Tcarlid = Loglin ";
            $resvalidacion = mysql_query( $sqlvalidacion, $conex  ) or die( mysql_errno()." - Error en el query $sqlvalidacion - ".mysql_error() );

            $validacion ='no';
            $facturableaux ='';
            if($rowvalidacion = mysql_fetch_array($resvalidacion))
            {
                $validacion = 'si';
                $facturableaux = $row['Tcarfac'];
            }
            //-------------------------------------------
            //-------------------------------------------

            // se busca la fuente de cada registro
            $sql2 = "   SELECT  Fenfue
                          FROM  ".$wbasedato_mov."_000002
                         WHERE  Fennum  = '".$row['Tcardod']."'";
            $res2 = mysql_query( $sql2, $conex  ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );


            $fuente ='';
            if($row2 = mysql_fetch_array($res2))
            {
                $fuente = $row2['Fenfue'];
            }


            if( $conexUnix ){

                // se consulta en unix el registro con que quedaron grabados los materiales y medicamentos
                $sqlu = "SELECT drodocdoc
                          FROM ITDRODOC
                         WHERE drodocnum  = '".$row['Tcardod']."'
                           AND drodocfue  = '".$fuente."'";
                $resu = odbc_do( $conexUnix, $sqlu );

                if( $resu )
                {
                    $i = 0;
                    while( odbc_fetch_row($resu) )
                    {

                        if($validacion!='si')
                        {
                            // hago Consulta para luego validar si los articulos son iguales y si  puedo actualizar o no
                            $selectiv   = " SELECT drodetfac , drodetart
                                              FROM IVDRODET
                                             WHERE drodetfue = '".$fuente."'
                                               AND drodetdoc = '".odbc_result($resu,1)."'
                                               AND drodetite = '".$row['Tcarlid']."'";

                            $resiv = odbc_do( $conexUnix, $selectiv );
                            $drodetfac = odbc_result($resiv,1);
                            $drodetart = odbc_result($resiv,2);

                            $diferenteprocedimiento = true;
                            if( $drodetart != $row['Tcarprocod']  )
                            {
                                $diferenteprocedimiento = false;
                            }


                            if($diferenteprocedimiento)
                            {
                                // actualizamos el registro de medicamentos en Facardet segun lo que dice en cliame_000106
                                $sqlupdate = " UPDATE FACARDET
                                                  SET cardetfac = '".$row['Tcarfac']."'
                                                WHERE cardetfue = '".$fuente."'
                                                  AND cardetdoc = '".odbc_result($resu,1)."'
                                                  AND cardetite = '".$row['Tcarlid']."'";
                                odbc_do( $conexUnix, $sqlupdate );

                                // actualizamos el registro de medicamentos en IVDRODET segun lo que dice en cliame_000106
                                $sqlupdate2 = " UPDATE IVDRODET
                                                  SET drodetfac = '".$row['Tcarfac']."'
                                                WHERE drodetfue = '".$fuente."'
                                                  AND drodetdoc = '".odbc_result($resu,1)."'
                                                  AND drodetite = '".$row['Tcarlid']."'";
                                odbc_do( $conexUnix, $sqlupdate2 );



                                // Acontinuacion se hace una validacion de como quedo los registros en
                                // FACARDET e IVDRODET y se mira si corresponde a lo que esta en la tabla
                                // cliame_000106 si esto es igual se cambia el estado de actualizado en unix
                                // en la tabla cliame_000106

                                $selectfacar = "       SELECT cardetfac
                                                         FROM FACARDET
                                                        WHERE cardetfue = '".$fuente."'
                                                          AND cardetdoc = '".odbc_result($resu,1)."'
                                                          AND cardetite = '".$row['Tcarlid']."'";

                                $resfacar = odbc_do( $conexUnix, $selectfacar );
                                $cardetfac = odbc_result($resfacar,1);

                                $selectiv   = "     SELECT drodetfac
                                                      FROM IVDRODET
                                                     WHERE drodetfue = '".$fuente."'
                                                       AND drodetdoc = '".odbc_result($resu,1)."'
                                                       AND drodetite = '".$row['Tcarlid']."'";

                                $resiv = odbc_do( $conexUnix, $selectiv );
                                $drodetfac = odbc_result($resiv,1);

                                if($row['Tcarfac'] == $cardetfac && $row['Tcarfac']== $drodetfac)
                                {

                                    $sql3 = "   UPDATE ".$wbasedato."_000106
                                                   SET Tcaraud = 'on'
                                                 WHERE  id = '".$row['id']."'";
                                    mysql_query( $sql3, $conex  ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
                                }

                            }
                            else
                            {

                                // hago Consulta para luego validar si los articulos son iguales y si  puedo actualizar o no
                                $selectiv   = " SELECT drodetite
                                                  FROM IVDRODET
                                                 WHERE drodetfue = '".$fuente."'
                                                   AND drodetdoc = '".odbc_result($resu,1)."'
                                                   AND drodetart = '".$row['Tcarprocod']."' ";

                                $resiv = odbc_do( $conexUnix, $selectiv );
                                //$drodetfac = odbc_result($resiv,1);
                                //$drodetart = odbc_result($resiv,2);
                                $drodetlinea = odbc_result($resiv,1);


                                if($drodetlinea=='')
                                {
                                    //echo "<br>documento : ".odbc_result($resu,1)." -- Linea Original ".$row['Tcarlin']."  Nueva linea ".$drodetlinea;
                                    //echo "<br> El articulo ".$row['Tcarprocod']." no esta en Unix";


                                }
                                else
                                {

                                    // actualizamos el registro de medicamentos en Facardet segun lo que dice en cliame_000106
                                    $sqlupdate = " UPDATE FACARDET
                                                      SET cardetfac = '".$row['Tcarfac']."'
                                                    WHERE cardetfue = '".$fuente."'
                                                      AND cardetdoc = '".odbc_result($resu,1)."'
                                                      AND cardetite = '".$drodetlinea."'";
                                    odbc_do( $conexUnix, $sqlupdate );

                                    // actualizamos el registro de medicamentos en IVDRODET segun lo que dice en cliame_000106
                                    $sqlupdate2 = " UPDATE IVDRODET
                                                      SET drodetfac = '".$row['Tcarfac']."'
                                                    WHERE drodetfue = '".$fuente."'
                                                      AND drodetdoc = '".odbc_result($resu,1)."'
                                                      AND drodetite = '".$drodetlinea."'";
                                    odbc_do( $conexUnix, $sqlupdate2 );



                                    // Acontinuacion se hace una validacion de como quedo los registros en
                                    // FACARDET e IVDRODET y se mira si corresponde a lo que esta en la tabla
                                    // cliame_000106 si esto es igual se cambia el estado de actualizado en unix
                                    // en la tabla cliame_000106

                                    $selectfacar = "       SELECT cardetfac
                                                             FROM FACARDET
                                                            WHERE cardetfue = '".$fuente."'
                                                              AND cardetdoc = '".odbc_result($resu,1)."'
                                                              AND cardetite = '".$drodetlinea."'";

                                    $resfacar = odbc_do( $conexUnix, $selectfacar );
                                    $cardetfac = odbc_result($resfacar,1);

                                    $selectiv   = "     SELECT drodetfac
                                                          FROM IVDRODET
                                                         WHERE drodetfue = '".$fuente."'
                                                           AND drodetdoc = '".odbc_result($resu,1)."'
                                                           AND drodetite = '".$drodetlinea."'";

                                    $resiv = odbc_do( $conexUnix, $selectiv );
                                    $drodetfac = odbc_result($resiv,1);

                                    if($row['Tcarfac'] == $cardetfac && $row['Tcarfac']== $drodetfac)
                                    {

                                        $sql3 = "   UPDATE ".$wbasedato."_000106
                                                       SET Tcaraud = 'on'
                                                     WHERE  id = '".$row['id']."'";
                                        mysql_query( $sql3, $conex  ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
                                    }


                                }

                            }

                        }
                        else
                        {// si tiene una regla donde parte el registro por dos

                            $sqlval          = "  SELECT Logdoc,Loglin,Logaor,Logare
                                                    FROM ".$wbasedato_mov."_000158
                                                   WHERE Logdoc = '".$row['Tcardod']."'
                                                     AND Logaor = '".$row['Tcarprocod']."'";

                            $resval =  mysql_query( $sqlval, $conex  ) or die( mysql_errno()." - Error en el query $sqlval - ".mysql_error() );

                            $bandera=true;
                            while($rowval = mysql_fetch_array($resval))
                            {


                                // hago Consulta para luego validar si los articulos son iguales y si  puedo actualizar o no
                                $selectiv   = " SELECT drodetfac , drodetart
                                                  FROM IVDRODET
                                                 WHERE drodetfue = '".$fuente."'
                                                   AND drodetdoc = '".odbc_result($resu,1)."'
                                                   AND drodetite = '".$rowval['Loglin']."'";

                                $resiv = odbc_do( $conexUnix, $selectiv );
                                $drodetfac = odbc_result($resiv,1);
                                $drodetart = odbc_result($resiv,2);

                                $diferenteprocedimiento = true;
                                if( $drodetart != $rowval['Logare']  )
                                {
                                    $diferenteprocedimiento = false;
                                }

                                if($diferenteprocedimiento)
                                {
                                        $sqlupdate = " UPDATE FACARDET
                                                  SET cardetfac = '".$row['Tcarfac']."'
                                                WHERE cardetfue = '".$fuente."'
                                                  AND cardetdoc = '".odbc_result($resu,1)."'
                                                  AND cardetite = '".$rowval['Loglin']."'";
                                        odbc_do( $conexUnix, $sqlupdate );


                                        $sqlupdate2 = "    UPDATE IVDRODET
                                                              SET drodetfac = '".$row['Tcarfac']."'
                                                            WHERE drodetfue = '".$fuente."'
                                                              AND drodetdoc = '".odbc_result($resu,1)."'
                                                              AND drodetite = '".$rowval['Loglin']."'";
                                        odbc_do( $conexUnix, $sqlupdate2 );


                                        //
                                        // Acontinuacion se hace una validacion de como quedo los registros en
                                        // FACARDET e IVDRODET y se mira si corresponde a lo que esta en la tabla
                                        // cliame_000106 si esto es igual se cambia el estado de actualizado en unix
                                        // en la tabla cliame_000106
                                        //
                                        $selectfacar = "   SELECT cardetfac
                                                             FROM FACARDET
                                                            WHERE cardetfue = '".$fuente."'
                                                              AND cardetdoc = '".odbc_result($resu,1)."'
                                                              AND cardetite = '".$rowval['Loglin']."'";

                                        $resfacar = odbc_do( $conexUnix, $selectfacar );
                                        $cardetfac = odbc_result($resfacar,1);

                                        $selectiv   = "     SELECT drodetfac
                                                              FROM IVDRODET
                                                             WHERE drodetfue = '".$fuente."'
                                                               AND drodetdoc = '".odbc_result($resu,1)."'
                                                               AND drodetite = '".$row['Tcarlid']."'";

                                        $resiv = odbc_do( $conexUnix, $selectiv );
                                        $drodetfac = odbc_result($resiv,1);

                                        // si alguno de los estados no es igual en la tabla cliame_000106 en Facardet y en Ivdrodet
                                        // la bandera es false y luego no se hace el update en cliame_000106 indicando que se actualizo
                                        // el registro
                                        if($row['Tcarfac'] == $cardetfac && $row['Tcarfac']== $drodetfac)
                                        {
                                            //$bandera=true;
                                        }
                                        else
                                        {
                                            $bandera=false;
                                        }

                                }
                                else
                                {
                                        // hago Consulta para luego validar si los articulos son iguales y si  puedo actualizar o no
                                        $selectiv   = " SELECT drodetite
                                                          FROM IVDRODET
                                                         WHERE drodetfue = '".$fuente."'
                                                           AND drodetdoc = '".odbc_result($resu,1)."'
                                                           AND drodetart = '".$rowval['Logare']."' ";

                                        $resiv = odbc_do( $conexUnix, $selectiv );
                                        //$drodetfac = odbc_result($resiv,1);
                                        //$drodetart = odbc_result($resiv,2);
                                        $drodetlinea = odbc_result($resiv,1);
                                        //echo "<br>entro".$selectiv;

                                        if($drodetlinea=='')
                                        {
                                                //echo "<br>linea nula en unix ".$drodetart." articulo matrix ".$rowval['Logare'];


                                        }
                                        else
                                        {
                                                    $sqlupdate = " UPDATE FACARDET
                                                                      SET cardetfac = '".$row['Tcarfac']."'
                                                                    WHERE cardetfue = '".$fuente."'
                                                                      AND cardetdoc = '".odbc_result($resu,1)."'
                                                                      AND cardetite = '".$drodetlinea."'";
                                                    odbc_do( $conexUnix, $sqlupdate );


                                                    $sqlupdate2 = "    UPDATE IVDRODET
                                                                          SET drodetfac = '".$row['Tcarfac']."'
                                                                        WHERE drodetfue = '".$fuente."'
                                                                          AND drodetdoc = '".odbc_result($resu,1)."'
                                                                          AND drodetite = '".$drodetlinea."'";
                                                    odbc_do( $conexUnix, $sqlupdate2 );


                                                    //
                                                    // Acontinuacion se hace una validacion de como quedo los registros en
                                                    // FACARDET e IVDRODET y se mira si corresponde a lo que esta en la tabla
                                                    // cliame_000106 si esto es igual se cambia el estado de actualizado en unix
                                                    // en la tabla cliame_000106
                                                    //
                                                    $selectfacar = "   SELECT cardetfac
                                                                         FROM FACARDET
                                                                        WHERE cardetfue = '".$fuente."'
                                                                          AND cardetdoc = '".odbc_result($resu,1)."'
                                                                          AND cardetite = '".$drodetlinea."'";

                                                    $resfacar = odbc_do( $conexUnix, $selectfacar );
                                                    $cardetfac = odbc_result($resfacar,1);

                                                    $selectiv   = "     SELECT drodetfac
                                                                          FROM IVDRODET
                                                                         WHERE drodetfue = '".$fuente."'
                                                                           AND drodetdoc = '".odbc_result($resu,1)."'
                                                                           AND drodetite = '".$drodetlinea."'";

                                                    $resiv = odbc_do( $conexUnix, $selectiv );
                                                    $drodetfac = odbc_result($resiv,1);

                                                    // si alguno de los estados no es igual en la tabla cliame_000106 en Facardet y en Ivdrodet
                                                    // la bandera es false y luego no se hace el update en cliame_000106 indicando que se actualizo
                                                    // el registro
                                                    if($row['Tcarfac'] == $cardetfac && $row['Tcarfac']== $drodetfac)
                                                    {
                                                        //$bandera=true;
                                                    }
                                                    else
                                                    {
                                                        $bandera=false;
                                                    }

                                        }
                                }



                            }

                            if($bandera==true)
                            {
                                // se actualiza el  registro en la tabla cliame_000106 indicando que el registro esta correctamente actualizado
                                $sql3 = "   UPDATE ".$wbasedato."_000106
                                               SET Tcaraud = 'on'
                                             WHERE  id = '".$row['id']."'";
                                mysql_query( $sql3, $conex  ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
                            }

                        }
                    }

                }

            }
        }

    }
}

//--------------------------------------------------------------------------------------------
// -->  Funcion que busca que turnos de urgencias se hayan perdido, para volver a asignárselo
//      al paciente. Jerson Trujillo 2016-04-11
//--------------------------------------------------------------------------------------------
function recuperarTurnosDeUrgencias()
{
    global $conex;
    global $wemp_pmla;

    $wbasedatoHce       = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
    $wbasedatoCliame    = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
    $wbasedatoMovhos    = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

    // --> Obtener el centro de costos de urgencias.
    $sqlUrg = "
    SELECT Ccocod
      FROM ".$wbasedatoMovhos."_000011
     WHERE Ccourg = 'on'
    ";
    $resUrg = mysql_query($sqlUrg, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUrg):</b><br>".mysql_error());
    if($rowUrg = mysql_fetch_array($resUrg))
        $hayCcoUrg = true;
    else
        $hayCcoUrg = false;

    if($hayCcoUrg)
    {
        // -->  Obtener los pacientes que no tengan turno en la hce_22, pero que por el numero de documento si haya un turno asignado
        //      en la tabla de turnos (movhos_000178), y esto solo lo hago para los registros asignados en la ultima hora.
        $sqlTurnos = "
        SELECT A.id, Atutur
          FROM ".$wbasedatoHce."_000022 AS A INNER JOIN ".$wbasedatoCliame."_000100 AS B ON (A.Mtrhis = B.Pachis )
                                             INNER JOIN ".$wbasedatoMovhos."_000178 AS C ON (B.Pacdoc = C.Atudoc  AND B.Pactdo = C.Atutdo)
                                             INNER JOIN ".$wbasedatoMovhos."_000018 AS D ON (A.Mtrhis = D.Ubihis AND A.Mtring = D.Ubiing)
         WHERE (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(CONCAT(A.Fecha_data, ' ', A.Hora_data))) < 3600
           AND A.Mtrcci = '".trim($rowUrg['Ccocod'])."'
           AND A.Mtrtur = ''
           AND C.Fecha_data = A.Fecha_data
           AND Ubialp != 'on'
           AND Ubiald != 'on' ";

        $resTurnos = mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());
        if($rowTurnos = mysql_fetch_array($resTurnos))
        {
            // --> Asignar nuevamente el turno
            $sqlAsignar = "
            UPDATE ".$wbasedatoHce."_000022
               SET Mtrtur = '".$rowTurnos['Atutur']."'
             WHERE id     = '".$rowTurnos['id']."'
            ";
            mysql_query($sqlAsignar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAsignar):</b><br>".mysql_error());

            // --> Archivo de log
            $tipoEscritura  = ((date('d') == '01') ? 'w+' : 'a+');
            $archivo        = fopen("logRecuperacionDeTurnosUrgencias.txt", $tipoEscritura);
            $log            = PHP_EOL."--> ".date( "Y-m-d" )." ".date( "H:i:s").PHP_EOL.$sqlAsignar;
            fputs($archivo, $log);
            fclose($archivo);
        }
    }

}


/**
 * [queryCargoUnix: Se crea el query que actualizará el ingreso en facardet para que quede el ingreso matrix]
 * @param  [type] $tipo_cargo             [description]
 * @param  [type] $arr_FiltroSql          [description]
 * @param  [type] $fuente_insumo          [description]
 * @param  [type] $drodocdoc              [description]
 * @param  [type] $Tcarlin                [description]
 * @param  [type] $historia_rep           [description]
 * @param  [type] $ingreso_rep            [description]
 * @param  [type] $wingreso_unx           [description]
 * @param  [type] $reg_unix               [description]
 * @param  [type] &$proceso_actualizacion [description]
 * @return [type]                         [description]
 */
function queryCargoUnix($tipo_cargo, $arr_FiltroSql, $fuente_insumo, $drodocdoc, $Tcarlin, $historia_rep, $ingreso_rep, $wingreso_unx, $reg_unix, &$proceso_actualizacion, $row_cargo_mx, $actualiza_valores, $arr_tarifaRecalculada)
{
    $selectfacar = "";
    $set_valores = "";
    // Si los valores matrix-unix no son iguales quiere decir que el valor unix se calculó con una tarifa diferente a matrix, entonces se actualiza el cargo con los valores de matrix
    if($actualiza_valores)
    {
        $valor_unitario = $arr_tarifaRecalculada["valor_unitario"];
        $valor_total    = $arr_tarifaRecalculada["valor_total"];
        $valor_excedente= $arr_tarifaRecalculada["valor_excedente"];
        $set_valores .= " , cardetvun = '{$valor_unitario}'";
        $set_valores .= " , cardettot = '{$valor_total}'";
        $set_valores .= " , cardetvex = '{$valor_excedente}'";
    }

    // Si el código de tarifa de unix era diferente al de matrix entonces se actualiza el cargo en unix con el código de tarifa matrix.
    if($arr_tarifaRecalculada["codigo_tarifa"] != '')
    {
        $codigo_tarifa = $arr_tarifaRecalculada["codigo_tarifa"];
        $set_valores .= " , cardettar = '{$codigo_tarifa}'";
    }

    $selectfacar = "UPDATE  FACARDET
                            SET cardetnum = '{$ingreso_rep}'
                                , cardetfec = '{$row_cargo_mx['fecha_cargo_mx']}'
                                , cardetori = 'IM'
                                $set_valores
                    WHERE   cardetfue = '{$fuente_insumo}'
                            AND cardetdoc = '{$drodocdoc}'
                            AND cardethis = '{$historia_rep}'
                            AND (cardetnum = '{$wingreso_unx}' OR cardetnum = '{$ingreso_rep}')
                            AND cardetlin = '{$Tcarlin}'";
    // echo "<pre>selectfacar:".print_r($selectfacar,true)."</pre><br><br>";
    return $selectfacar;
}

/**
 * [actualizarConsecutivosRipsCargos: Los RIPS debieron quedar con el consecutivo del ingreso activo al momento de grabar el cargo, pero en esta función se actualizan con el consecutivo
 *                                 RIPS del ingreso que se grabó desde matrix.]
 * @param  [type] $conex                       [description]
 * @param  [type] $conexUnix                   [description]
 * @param  [type] $wbasedato                   [description]
 * @param  [type] $arr_parametros              [description]
 * @param  [type] $fuente_insumo               [description]
 * @param  [type] $drodocdoc                   [description]
 * @param  [type] $linea                       [description]
 * @param  [type] $codigo_insumo_mx            [description]
 * @param  [type] $arr_FiltroSql               [description]
 * @param  [type] $historia_rep                [description]
 * @param  [type] $ingreso_rep                 [description]
 * @param  [type] $wingreso_unx                [description]
 * @param  [type] $row                         [description]
 * @param  [type] $arr_DocumentosFuentes       [description]
 * @param  [type] $Tcardoi                     [description]
 * @param  [type] &$data                       [description]
 * @param  [type] &$arr_cargosReporte          [description]
 * @param  [type] &$arr_cargosHisFactNotas     [description]
 * @param  [type] &$arr_consultas_por_historia [description]
 * @param  [type] &$proceso_actualizacion      [description]
 * @param  [type] &$arr_RIPS_msate             [description]
 * @return [type]                              [description]
 */
function actualizarConsecutivosRipsCargos($conex, $conexUnix, $wbasedato, $wemp_pmla, $arr_parametrosExtra, $arr_FiltroSql, $ingreso_rep, $row, $arr_DocumentosFuentes, $Tcardoi, $arr_tarifaRecalculada, &$data, &$arr_cargosReporte, &$arr_cargosHisFactNotas, &$arr_consultas_por_historia, &$proceso_actualizacion, &$arr_RIPS_msate, $actualiza_valores)
{
    foreach ($arr_parametrosExtra as $key => $value) {
        $$key = $value;
    }

    // Consulto los consecutivos de RIPS para los ingresos de la historia en unix
    if(!array_key_exists($historia_rep, $arr_RIPS_msate))
    {
        $arr_RIPS_msate[$historia_rep] = array();

        // Para guardar consecutivo RIPS del ingreso matrix
        if(!array_key_exists($ingreso_rep, $arr_RIPS_msate[$historia_rep]))
        {
            $arr_RIPS_msate[$historia_rep][$ingreso_rep] = array("ateips"=>"", "atedoc"=>"");
        }

        // Para guardar consecutivo RIPS del ingreso unix
        if(!array_key_exists($wingreso_unx, $arr_RIPS_msate[$historia_rep]))
        {
            $arr_RIPS_msate[$historia_rep][$wingreso_unx] = array("ateips"=>"", "atedoc"=>"");
        }

        $sqlMsate= "SELECT  ateips, atedoc, ateing
                    FROM    msate
                    WHERE   atehis = '{$historia_rep}'
                            AND ateing IN ('{$ingreso_rep}', '{$wingreso_unx}')";
        if($resMsate = @odbc_exec($conexUnix, $sqlMsate))
        {
            while (odbc_fetch_row($resMsate))
            {
                $sel_ateips = odbc_result($resMsate,"ateips");
                $sel_atedoc = odbc_result($resMsate,"atedoc");
                $sel_ateing = odbc_result($resMsate,"ateing");

                $arr_RIPS_msate[$historia_rep][$sel_ateing]["ateips"] = $sel_ateips;
                $arr_RIPS_msate[$historia_rep][$sel_ateing]["atedoc"] = $sel_atedoc;
            }
        }
        else
        {
            $proceso_actualizacion = false;
            // echo odbc_errormsg()." > ".$sqlMsate;
            $desc_error = "No se pudo consultar consecutivo RIPS: > ".PHP_EOL.odbc_errormsg();
            registroLogErrorCRON($conex, $wbasedato, $sqlMsate, 'unix', $desc_error);

            // Se eliminan las posiciones de ingreso de arreglo porque si hubo un error no deben quedar seteados los campos de ips y doc de mstate en el array.
            unset($arr_RIPS_msate[$historia_rep][$ingreso_rep]);
            unset($arr_RIPS_msate[$historia_rep][$wingreso_unx]);
        }
    }

    if(array_key_exists($ingreso_rep, $arr_RIPS_msate[$historia_rep]) && array_key_exists($wingreso_unx, $arr_RIPS_msate[$historia_rep])
        && $arr_RIPS_msate[$historia_rep][$ingreso_rep]["ateips"] != '' && $arr_RIPS_msate[$historia_rep][$ingreso_rep]["atedoc"] != ''
        && $arr_RIPS_msate[$historia_rep][$wingreso_unx]["ateips"] != '' && $arr_RIPS_msate[$historia_rep][$wingreso_unx]["atedoc"] != '')
    {
        $ms_ateips_mx = $arr_RIPS_msate[$historia_rep][$ingreso_rep]["ateips"];
        $ms_atedoc_mx = $arr_RIPS_msate[$historia_rep][$ingreso_rep]["atedoc"];//Documento que reemplazará atedoc por el consecutivo correspondiente al ingreso matrix.

        $ms_ateips_unx = $arr_RIPS_msate[$historia_rep][$wingreso_unx]["ateips"];
        $ms_atedoc_unx = $arr_RIPS_msate[$historia_rep][$wingreso_unx]["atedoc"];//Documento que se debe reemplazar por $ms_atedoc_mx del ingreso matrix

        $facturable_mx = $row["facturable_mx"];
        $fecha_cargo_mx= $row['fecha_cargo_mx'];

        $set_valores_msdro = "";
        if($facturable_mx == 'S' && $actualiza_valores)
        {
            $set_valores_msdro .= ", drovun = '".$arr_tarifaRecalculada["valor_unitario"]."'";
            $set_valores_msdro .= ", drotot = '".$arr_tarifaRecalculada["valor_total"]."'";
        }
        elseif($facturable_mx != 'S')
        {
            $set_valores_msdro .= ", drovun = '0'";
            $set_valores_msdro .= ", drotot = '0'";
        }

        // Se modifican los consecutivos de RIPS en el detalle del ingreso unix para que se asigne al detalle el consecutivo RIPS del ingreso matrix.
        // Este query aplica para medicamentos
        $sqlMsdro= "UPDATE  msdro
                    SET     drodoc = '{$ms_atedoc_mx}'
                            , drofec = '{$fecha_cargo_mx}'
                            {$set_valores_msdro}
                    WHERE   droips = '{$ms_ateips_unx}'
                            AND drodoc = '{$ms_atedoc_unx}'
                            AND drofte = '{$fuente_insumo}'
                            AND drodto = '{$drodocdoc}'
                            AND droite = '{$linea_facardet}'";
        if($resivUdt = @odbc_exec($conexUnix, $sqlMsdro))
        {
            //
        }
        else
        {
            $proceso_actualizacion = false;
            // echo odbc_errormsg()." > ".$sqlMsdro;
            $desc_error = "No se pudo actualizar consecutivo RIPS en Msdro: > ".PHP_EOL.odbc_errormsg();
            registroLogErrorCRON($conex, $wbasedato, $sqlMsdro, 'unix', $desc_error);
        }

        $set_valores_msotr = "";
        if($facturable_mx == 'S' && $actualiza_valores)
        {
            $set_valores_msotr .= ", otrvun = '".$arr_tarifaRecalculada["valor_unitario"]."'";
            $set_valores_msotr .= ", otrtot = '".$arr_tarifaRecalculada["valor_total"]."'";
        }
        elseif($facturable_mx != 'S')
        {
            $set_valores_msotr .= ", otrvun = '0'";
            $set_valores_msotr .= ", otrtot = '0'";
        }

        // Se modifican los consecutivos de RIPS en el detalle del ingreso unix para que se asigne al detalle el consecutivo RIPS del ingreso matrix.
        // Este query aplica para materiales
        $sqlMsotr= "UPDATE  msotr
                    SET     otrdoc = '{$ms_atedoc_mx}'
                            , otrfec = '{$fecha_cargo_mx}'
                            $set_valores_msotr
                    WHERE   otrips = '{$ms_ateips_unx}'
                            AND otrdoc = '{$ms_atedoc_unx}'
                            AND otrfte = '{$fuente_insumo}'
                            AND otrdto = '{$drodocdoc}'
                            AND otrite = '{$linea_facardet}'";
        if($resivUdt = @odbc_exec($conexUnix, $sqlMsotr))
        {
            //
        }
        else
        {
            $proceso_actualizacion = false;
            // echo odbc_errormsg()." > ".$sqlMsotr;
            $desc_error = "No se pudo actualizar consecutivo RIPS en Msotr: > ".PHP_EOL.odbc_errormsg();
            registroLogErrorCRON($conex, $wbasedato, $sqlMsotr, 'unix', $desc_error);
        }
    }
    else
    {
        $proceso_actualizacion = false;
        // echo "<br>".utf8_decode("Faltó algún consecutivo RIPS por leer<br>");
        $desc_error = "Falto un consecutivo RIPS por leer o se presento algun problema no especificado al consultar consecutivo para ingresos [({$ingreso_rep}), ({$wingreso_unx})] en msate historia: [{$historia_rep}] ".PHP_EOL;
        registroLogErrorCRON($conex, $wbasedato, "", 'unix', $desc_error);
    }
}


/**
 * [consultarCargoInsumoPorLinea: Por cada cargo (número de línea) se realiza una actualización en facardet para cambiar el número de ingreso activo al momento de grabar el cargo en unix
 *                                 por el número de ingreso con el que se generó el cargo en matrix, si la actualización fue exitosa entonces también se procede a actualizar el cargo en RIPS
 *                                 con el consecutivo de RIPS del ingreso matrix reemplazando el consecutivo RIPS del ingreso unix (se modifica en msdro y msotr, el cargo puede estar en una de las dos tablas)]
 * @param  [type] $conex                       [description]
 * @param  [type] $conexUnix                   [description]
 * @param  [type] $wbasedato                   [description]
 * @param  [type] $arr_parametros              [description]
 * @param  [type] $fuente_insumo               [description]
 * @param  [type] $drodocdoc                   [description]
 * @param  [type] $linea                       [description]
 * @param  [type] $codigo_insumo_mx            [description]
 * @param  [type] $arr_FiltroSql               [description]
 * @param  [type] $historia_rep                [description]
 * @param  [type] $ingreso_rep                 [description]
 * @param  [type] $wingreso_unx                [description]
 * @param  [type] $row                         [description]
 * @param  [type] $arr_DocumentosFuentes       [description]
 * @param  [type] $Tcardoi                     [description]
 * @param  [type] &$data                       [description]
 * @param  [type] &$arr_cargosReporte          [description]
 * @param  [type] &$arr_cargosHisFactNotas     [description]
 * @param  [type] &$arr_consultas_por_historia [description]
 * @param  [type] &$proceso_actualizacion      [description]
 * @param  [type] &$arr_RIPS_msate             [description]
 * @return [type]                              [description]
 */
function consultarCargoInsumoPorLinea($conex, $conexUnix, $wbasedato, $wemp_pmla, $arr_parametros, $fuente_insumo, $drodocdoc, $linea, $codigo_insumo_mx, $arr_FiltroSql, $historia_rep, $ingreso_rep, $wingreso_unx, $row, $arr_DocumentosFuentes, $Tcardoi , &$data, &$arr_cargosReporte, &$arr_cargosHisFactNotas, &$arr_consultas_por_historia, &$proceso_actualizacion, &$arr_RIPS_msate, $linea_nueva_reemplazo, $cantidad)
{
    $historia_ing = $historia_rep.'_'.$ingreso_rep;

    {
        $drodetart = '';
        if(array_key_exists($fuente_insumo, $arr_DocumentosFuentes) && array_key_exists($Tcardoi, $arr_DocumentosFuentes[$fuente_insumo])
        && array_key_exists($drodocdoc, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi]) && array_key_exists($linea, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi][$drodocdoc]))
        {
            $drodetart = $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi][$drodocdoc][$linea];
        }
        // $data["evidencia_error"][] = "DEBUG drodetart: ".$drodetart.' > DEBUG codigo_insumo_mx: '.$codigo_insumo_mx.' > '.PHP_EOL;
        /*
        Se agrega esta nueva validacion  , donde se ve si el articulo en Matrix corresponde al de Unix.

        Explicacion: En Matrix se graba un documento y linea  por cada articulo grabado, Esto mismo se hace en Unix  , Existe
        una tabla en Unix donde hay relacion del documento y linea Matrix con documento y linea Unix  generalmente son distintos los documentos
        pero el numero de linea coincide. Para estar seguros de que el articulo en Matrix corresponda al de Unix se  compara tambien el articulo
        si es el articulo se trabaja con la linea  de matrix porque se sabe que es la misma sino se busca en todo el documento unix la linea que corresponde
        a la linea en matrix

        Si sí corresponde   se consulta el estado de facturable o no en Facardet
        */
        $arr_tarifaRecalculada = array("valor_unitario"=>0, "valor_total"=>0, "valor_excedente"=>0, "codigo_tarifa"=>"");
        $actualiza_valores     = false;
        $arr_parametrosExtra   = array();
        $arr_parametrosExtra["historia_rep"]     = $historia_rep;
        $arr_parametrosExtra["fuente_insumo"]    = $fuente_insumo;
        $arr_parametrosExtra["wingreso_unx"]     = $wingreso_unx;
        $arr_parametrosExtra["ingreso_rep"]      = $ingreso_rep;
        $arr_parametrosExtra["codEmpParticular"] = $arr_parametros["codEmpParticular"];

        if($drodetart == $codigo_insumo_mx)
        {
            $arr_parametrosExtra["drodocdoc"]        = $drodocdoc;
            $arr_parametrosExtra["linea_facardet"]   = $linea;
            $arr_parametrosExtra["codigo_insumo_mx"] = $codigo_insumo_mx;

            $actualiza_valores = calcularTarifa($conex, $conexUnix, $wbasedato, $wemp_pmla, $arr_parametrosExtra, $codigo_insumo_mx, $linea_nueva_reemplazo, $row, $cantidad, $proceso_actualizacion, $arr_tarifaRecalculada);
            $updateFacardet = queryCargoUnix('insumo', $arr_FiltroSql, $fuente_insumo, $drodocdoc, $linea, $historia_rep, $ingreso_rep, $wingreso_unx, '', $proceso_actualizacion, $row, $actualiza_valores, $arr_tarifaRecalculada);

            if($resivUdt = @odbc_exec($conexUnix, $updateFacardet))
            {
                if($proceso_actualizacion)
                {
                    actualizarCargoIvdrodet($conex, $conexUnix, $wbasedato, $wemp_pmla, $arr_parametrosExtra, $actualiza_valores, $arr_tarifaRecalculada, $proceso_actualizacion);
                    actualizarConsecutivosRipsCargos($conex, $conexUnix, $wbasedato, $wemp_pmla, $arr_parametrosExtra, $arr_FiltroSql, $ingreso_rep, $row, $arr_DocumentosFuentes, $Tcardoi, $arr_tarifaRecalculada, $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia, $proceso_actualizacion, $arr_RIPS_msate, $actualiza_valores);
                }
                else
                {
                    // se debió calcular tarifa y algo falló pero ya se debió capturar el error en fn_calcular_Tarifa(...)
                }
            }
            else
            {
                $proceso_actualizacion = false;
                // echo odbc_errormsg()." > ".$updateFacardet;
                $desc_error = "No se pudo actualizar el ingreso en facardet: > ".PHP_EOL.odbc_errormsg();
                registroLogErrorCRON($conex, $wbasedato, $updateFacardet, 'unix', $desc_error);
            }
        }
        else
        {
            // Entra aquí si las líneas de matrix vs Unix no son las mismas
            // Hago una busqueda del articulo y documento y asi hallo la nueva linea

            $selecti2 = "   SELECT  drodetite
                            FROM    IVDRODET
                            WHERE   drodetfue = '{$fuente_insumo}'
                                    AND drodetdoc = '{$drodocdoc}'
                                    AND drodetart = '{$codigo_insumo_mx}'";
            // $data["evidencia_error"][] = "DEBUG selecti2: ".$selecti2.' > '.PHP_EOL;
            // $arr_consultas_por_historia[$historia_ing][] = $selecti2;
            if($resiv = odbc_exec($conexUnix, $selecti2))
            {
                $drodetlinea = odbc_result($resiv,'drodetite');
                $existeprocedimiento = true;
                if($drodetlinea != '')
                {
                    // $linea_nueva_reemplazo = true;
                    $arr_parametrosExtra["drodocdoc"]        = $drodocdoc;
                    $arr_parametrosExtra["linea_facardet"]   = $drodetlinea;
                    $arr_parametrosExtra["codigo_insumo_mx"] = $codigo_insumo_mx;


                    $actualiza_valores = calcularTarifa($conex, $conexUnix, $wbasedato, $wemp_pmla, $arr_parametrosExtra, $codigo_insumo_mx, $linea_nueva_reemplazo, $row, $cantidad, $proceso_actualizacion, $arr_tarifaRecalculada);
                    $updateFacardet = queryCargoUnix('insumo', $arr_FiltroSql, $fuente_insumo, $drodocdoc, $drodetlinea, $historia_rep, $ingreso_rep, $wingreso_unx, '', $proceso_actualizacion, $row, $actualiza_valores, $arr_tarifaRecalculada);

                    if($resivUdt = @odbc_exec($conexUnix, $updateFacardet))
                    {
                        if($proceso_actualizacion)
                        {
                            actualizarCargoIvdrodet($conex, $conexUnix, $wbasedato, $wemp_pmla, $arr_parametrosExtra, $actualiza_valores, $arr_tarifaRecalculada, $proceso_actualizacion);
                            actualizarConsecutivosRipsCargos($conex, $conexUnix, $wbasedato, $wemp_pmla, $arr_parametrosExtra, $arr_FiltroSql, $ingreso_rep, $row, $arr_DocumentosFuentes, $Tcardoi, $arr_tarifaRecalculada, $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia, $proceso_actualizacion, $arr_RIPS_msate, $actualiza_valores);
                        }
                        else
                        {
                            // se debió calcular tarifa y algo falló pero ya se debió capturar el error en fn_calcular_Tarifa(...)
                        }
                    }
                    else
                    {
                        $proceso_actualizacion = false;
                        // echo odbc_errormsg()." > ".$updateFacardet;
                        $desc_error = "No se pudo actualizar el ingreso en facardet: > ".PHP_EOL.odbc_errormsg();
                        registroLogErrorCRON($conex, $wbasedato, $updateFacardet, 'unix', $desc_error);
                    }
                }
                else
                {
                    $proceso_actualizacion = false;
                    // $data["error"] = 1;
                    // $data["mensaje"] = "Problemas al generar el reporte. No existe insumo en Unix";
                    // $data["evidencia_error"][] = "Linea en blanco (unx $drodetart  mx $codigo_insumo_mx, Tcardoi: {$row['Tcardoi']}, Histo: {$row['Tcarhis']}, drodocdoc: $drodocdoc) selecti2: ".$selecti2.' > ';
                    $desc_error = "Linea en blanco (unx $drodetart  mx $codigo_insumo_mx, Tcardoi: {$row['Tcardoi']}, Histo: {$row['Tcarhis']}, drodocdoc: $drodocdoc) > ".PHP_EOL;
                    registroLogErrorCRON($conex, $wbasedato, $selecti2, 'unix', $desc_error);
                }
            }
            else
            {
                // $data["error"] = 1;
                // $data["mensaje"] = "Problemas al generar el reporte";
                // $data["evidencia_error"][] = "selectiv2: ".$selectiv2.' > '.mysql_error();
                $proceso_actualizacion = false;
                $desc_error = "No se pudo consultar nueva linea del insumo en unix: > ".PHP_EOL.odbc_errormsg();
                registroLogErrorCRON($conex, $wbasedato, $selecti2, 'unix', $desc_error);
            }
        }
        //$html3.= "<td>".odbc_result($resu,1)."-".$linea."</td>";
    }
}

/**
 * [consultaCargosUnix: Esta función se encarga de consultar la fuente y el documento del cargo (se adiciona a un array para no tener que consultar cada vez que se lee un cargo)
 *                         se modifica el número de ingreso en las tablas ivdro y facar para la fuente y el documento leído en ITDRODOC, se verifica si el cargo
 *                         cambió de línea o se generaron otros cargos derivados del cargo matrix.]
 * @param  [type] $conex                       [description]
 * @param  [type] $conexUnix                   [description]
 * @param  [type] $wbasedato                   [description]
 * @param  [type] $wbasedato_movhos            [description]
 * @param  [type] $codEmpParticular            [description]
 * @param  [type] $wccos_rep                   [description]
 * @param  [type] $row                         [description]
 * @param  [type] &$data                       [description]
 * @param  [type] &$arr_cargosReporte          [description]
 * @param  [type] &$arr_cargosHisFactNotas     [description]
 * @param  [type] &$Tcardoi_ant                [description]
 * @param  [type] &$fuente_insumo              [description]
 * @param  [type] &$drodocdoc                  [description]
 * @param  [type] &$arr_DocumentosFuentes      [description]
 * @param  [type] &$arr_consultas_por_historia [description]
 * @param  [type] &$proceso_actualizacion      [description]
 * @param  [type] &$arr_RIPS_msate             [description]
 * @return [type]                              [description]
 */
function consultaCargosUnix($conex, $conexUnix, $wbasedato, $wemp_pmla, $wbasedato_movhos, $codEmpParticular, $wccos_rep, $row, &$data, &$arr_cargosReporte, &$arr_cargosHisFactNotas, &$Tcardoi_ant, &$fuente_insumo, &$drodocdoc, &$arr_DocumentosFuentes, &$arr_consultas_por_historia, &$proceso_actualizacion, &$arr_RIPS_msate, &$drodocdoc_facturado)
{
    $Tcarlin      = $row['linea_insumo'];
    $historia_rep = $row['Tcarhis'];
    $ingreso_rep  = $row['Tcaring'];
    $wingreso_unx = $row['wingreso_unx'];
    $reg_unix     = $row['reg_unix'];

    $arr_parametros                         = array();
    $arr_parametros["historia_rep"]         = $historia_rep;
    $arr_parametros["ingreso_rep"]          = $ingreso_rep;
    $arr_parametros["row"]                  = $row;
    $arr_parametros["idx_historia_ing_rep"] = $historia_rep.'_'.$ingreso_rep;
    $arr_parametros["wccos_rep"]            = $wccos_rep;
    $arr_parametros["codEmpParticular"]     = $codEmpParticular;
    $historia_ing                           = $historia_rep.'_'.$ingreso_rep;

    $arr_FiltroSql = array("select"=>"","group"=>"");

    // if($row['invent'] == 'on')
    {
        // Si es de inventario, Tcardoi y fuen_insumo son diferentes a un valor anterior entonces consulte nuevamente en ITDRODOC
        // el número de documento
        $sqlu = '';

        if(!array_key_exists($row['fuen_insumo'], $arr_DocumentosFuentes))
        {
            // echo "<pre>fuen_insumo: ".print_r($row['fuen_insumo'],true)."</pre><br>";
            $arr_DocumentosFuentes[$row['fuen_insumo']] = array();
        }

        // if($row['Tcardoi'] != $Tcardoi_ant || $row['fuen_insumo'] != $fuente_insumo)
        if(!array_key_exists($row['Tcardoi'], $arr_DocumentosFuentes[$row['fuen_insumo']]))
        {
            $Tcardoi_ant   = $row['Tcardoi'];
            $fuente_insumo = $row['fuen_insumo'];

            $sqlu = "   SELECT  drodocdoc, drodetart, drodetite
                        FROM    ITDRODOC, IVDRODET
                        WHERE   drodocfue  = '{$fuente_insumo}'
                                AND drodocnum  = '{$Tcardoi_ant}'
                                AND drodetfue = drodocfue
                                AND drodetdoc = drodocdoc";
            // $data["evidencia_error"][] = "DEBUG sqlu: ".$sqlu.' > '.PHP_EOL;
            // $arr_consultas_por_historia[$historia_ing][] = $sqlu;
            if($resu = @odbc_do($conexUnix, $sqlu))
            {
                while(odbc_fetch_row($resu))
                {
                    $drodocdoc     = odbc_result($resu,"drodocdoc");
                    $drodetite_lin = odbc_result($resu,"drodetite");
                    $drodetart     = odbc_result($resu,"drodetart");
                    if(!array_key_exists($Tcardoi_ant, $arr_DocumentosFuentes[$fuente_insumo]))
                    {
                        $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant] = array();
                    }

                    if(!array_key_exists($drodocdoc, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant]))
                    {
                        $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant][$drodocdoc] = array();

                        // Validar que para la fuente y documento en facardet no se ha facturado ningún cargo, en ese caso no debe continuar el proceso de actualización.
                        $sql_facturados = " SELECT  COUNT(*) AS facturados
                                            FROM    facardet
                                            WHERE   cardetfue = '{$fuente_insumo}'
                                                    AND cardetdoc = '{$drodocdoc}'
                                                    AND cardethis = '{$historia_rep}'
                                                    AND cardetvfa > 0
                                            GROUP BY cardetfue";

                        if($resFacts = @odbc_exec($conexUnix, $sql_facturados))
                        {
                            $nums_facturados = odbc_result($resFacts,"facturados")*1;

                            if($nums_facturados == 0)
                            {
                                $fecha_cargo_mx = $row['fecha_cargo_mx'];
                                // Actualizar el ingreso en encabezado ivdro
                                $updateIvdro = "UPDATE  ivdro
                                                        SET dronum = '{$ingreso_rep}',
                                                            drofec = '{$fecha_cargo_mx}'
                                                WHERE   drofue = '{$fuente_insumo}'
                                                        AND drodoc = '{$drodocdoc}'
                                                        AND drohis = '{$historia_rep}'"; //AND dronum = '{$wingreso_unx}'

                                if($resivUdt = @odbc_exec($conexUnix, $updateIvdro))
                                {
                                    //
                                }
                                else
                                {
                                    $proceso_actualizacion = false;
                                    // echo odbc_errormsg()." > ".$updateIvdro;
                                    $desc_error = "No se pudo actualizar ingreso en ivdro: > ".PHP_EOL.odbc_errormsg();
                                    registroLogErrorCRON($conex, $wbasedato, $updateIvdro, 'unix', $desc_error);
                                }

                                // Actualizar el ingreso en encabezado ivdro
                                $updateFacar = "UPDATE  facar
                                                        SET carnum = '{$ingreso_rep}',
                                                            carfec = '{$fecha_cargo_mx}'
                                                WHERE   carfue = '{$fuente_insumo}'
                                                        AND cardoc = '{$drodocdoc}'
                                                        AND carhis = '{$historia_rep}'"; //AND carnum = '{$wingreso_unx}'

                                if($resivUdt = @odbc_exec($conexUnix, $updateFacar))
                                {
                                    //
                                }
                                else
                                {
                                    $proceso_actualizacion = false;
                                    // echo odbc_errormsg()." > ".$updateFacar;
                                    $desc_error = "No se pudo actualizar ingreso en facar: > ".PHP_EOL.odbc_errormsg();
                                    registroLogErrorCRON($conex, $wbasedato, $updateFacar, 'unix', $desc_error);
                                }

                                $log_fec = date("Y-m-d H:i:s");
                                //Registrar en log proceso de actualización.
                                $insertLog = "INSERT INTO ivlog (logusu, logter, logpro, logope, logde1, logva1, logde2, logva2, logde3, logva3, logreg, logtip, logtab, logfec)
                                                VALUES ('inactivos', 'unix_matrix', 'pac..unix_matrix.php', 'Modifica Ingres/Tarif', 'Fuente', '{$fuente_insumo}', 'Documento', '{$drodocdoc}', 'facCarDet', 'ivDroDet-msDroOt', '{$historia_rep}', 'I', 'ivdro', '{$log_fec}')";
                                if($resivUdt = @odbc_exec($conexUnix, $insertLog))
                                {
                                    //
                                }
                                else
                                {
                                    $proceso_actualizacion = false;
                                    // echo odbc_errormsg()." > ".$insertLog;
                                    $desc_error = "No se pudo insertar registro de actualizacion Log unix: > ".PHP_EOL.odbc_errormsg();
                                    registroLogErrorCRON($conex, $wbasedato, $insertLog, 'unix', utf8_encode($desc_error));
                                }
                            }
                            else
                            {
                                if(!array_key_exists($drodocdoc, $drodocdoc_facturado))
                                {
                                    $proceso_actualizacion = false;
                                    $drodocdoc_facturado[$drodocdoc] = $drodocdoc;
                                    $desc_error = "No se pueden actualizar los cargos en unix porque ya hay cargos facturados para el documento: $drodocdoc.".PHP_EOL;
                                    registroLogErrorCRON($conex, $wbasedato, $sql_facturados, 'unix', $desc_error);
                                }
                            }
                        }
                        else
                        {
                            $proceso_actualizacion = false;
                            // echo odbc_errormsg()." > ".$sql_facturados;
                            $desc_error = "No se pudo verificar si hay cargos facturados: > ".PHP_EOL.odbc_errormsg();
                            registroLogErrorCRON($conex, $wbasedato, $sql_facturados, 'unix', $desc_error);
                        }
                    }

                    if($drodetite_lin != '')
                    {
                        if(!array_key_exists($drodetite_lin, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant][$drodocdoc]))
                        {
                            $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant][$drodocdoc][$drodetite_lin] = ""; //array("drodetart"=>$drodetart);
                        }
                        $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant][$drodocdoc][$drodetite_lin] = $drodetart;
                    }
                    // echo "<pre>Tcardoi_ant: $Tcardoi_ant, drodocdoc: ".print_r($drodocdoc,true)."</pre><br>";
                }
            }
            else
            {
                $proceso_actualizacion = false;
                $drodocdoc = '';
                // $data["error"] = 1;
                // $data["mensaje"] = "Problemas al generar el reporte";
                // $data["evidencia_error"][] = "sqlu: No se pudo ejecutar el query en unix: ".$sqlu.' > '.PHP_EOL.odbc_errormsg();

                $desc_error = "No se puso consultar fuente y documento, ITDRODOC: > ".PHP_EOL.odbc_errormsg();
                registroLogErrorCRON($conex, $wbasedato, $sqlu, 'unix', $desc_error);
            }
        }
        else
        {
            $arr_key_doc = array_keys($arr_DocumentosFuentes[$fuente_insumo][$row['Tcardoi']]);
            $drodocdoc = (array_key_exists(0, $arr_key_doc) && $arr_key_doc[0] != '') ? $arr_key_doc[0] : ''; // Puede que aun no este integrado por inconsistencia
            // $data["evidencia_error"][] = "DEBUG drodocdoc: ".$drodocdoc.' > '.PHP_EOL;
            if(count($arr_key_doc) > 1)
            {
                $data["evidencia_error"][] = 'NO DEBERIA SER MAYOR A 1 > '.print_r($arr_key_doc,true);
                // $desc_error = "No se pudo actualizar ingreso en facar: > ".PHP_EOL.odbc_errormsg();
                // registroLogErrorCRON($conex, $wbasedato, $updateFacar, 'unix', $desc_error);
            }
        }

        if($drodocdoc != '')
        {
            if(!array_key_exists($drodocdoc, $drodocdoc_facturado))
            {
                if($row['Logdoc'] != '')
                {
                    $lineasNuevasOReemplazo = array();
                    if($row['Logpro'] =='on')
                    {
                        $querycenpro = "SELECT  Pdeins
                                        FROM    cenpro_000003
                                        WHERE   Pdepro ='{$row['Tcarprocod']}'";
                        // $data["evidencia_error"][] = "DEBUG querycenpro: ".$querycenpro.' > '.PHP_EOL;
                        // $arr_consultas_por_historia[$historia_ing][] = $querycenpro;
                        if($resquerycenpro =  mysql_query( $querycenpro, $conex  ))
                        {
                            $p = -1;
                            while($rowquerycenpro = mysql_fetch_array($resquerycenpro))
                            {
                                $p++;
                                $lineasNuevasOReemplazo[] = ($row['linea_insumo']*1) + $p;
                            }
                        }
                        else
                        {
                            // $data["error"] = 1;
                            // $data["mensaje"] = "Problemas al generar el reporte";
                            // $data["evidencia_error"][] = "querycenpro: ".$querycenpro.' > '.mysql_error();
                            $proceso_actualizacion = false;
                            $desc_error = "No se pudo consultar codigo insumo en cenpro_000003: > ".PHP_EOL.mysql_error();
                            registroLogErrorCRON($conex, $wbasedato, $querycenpro, 'matrix', $desc_error);
                        }
                    }
                    else
                    {
                        $lineasNuevasOReemplazo[] = $row['linea_insumo'];
                    }

                    $nuevasLineas = implode("','", $lineasNuevasOReemplazo);
                    $sqlval       = "   SELECT  Logdoc,Loglin,Logaor,Logare, Logcae AS cantidad
                                        FROM    {$wbasedato_movhos}_000158
                                        WHERE   Logdoc = '{$row['Tcardoi']}'
                                                AND Loglin IN ('{$nuevasLineas}')
                                                AND Logaor = '{$row['Tcarprocod']}'";
                    // $data["evidencia_error"][] = "DEBUG sqlval: ".$sqlval.' > '.PHP_EOL;
                    // $arr_consultas_por_historia[$historia_ing][] = $sqlval;
                    if($resval = mysql_query( $sqlval, $conex))
                    {
                        while($rowval = mysql_fetch_array($resval))
                        {
                            $linea_nueva_reemplazo = true;
                            consultarCargoInsumoPorLinea($conex, $conexUnix, $wbasedato, $wemp_pmla, $arr_parametros, $fuente_insumo, $drodocdoc, $rowval['Loglin'], $rowval['Logare'], $arr_FiltroSql, $historia_rep, $ingreso_rep, $wingreso_unx, $row, $arr_DocumentosFuentes, $row['Tcardoi'], $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia, $proceso_actualizacion, $arr_RIPS_msate, $linea_nueva_reemplazo, $rowval['cantidad']);
                        }
                    }
                    else
                    {
                        $proceso_actualizacion = false;
                        // $data["error"] = 1;
                        // $data["mensaje"] = "Problemas al generar el reporte";
                        // $data["evidencia_error"][] = "sqlval: ".$sqlval.' > '.mysql_error();
                        $desc_error = "No se pudo consultar nueva línea del insumo: > ".PHP_EOL.mysql_error();
                        registroLogErrorCRON($conex, $wbasedato, $sqlval, 'matrix', $desc_error);
                    }
                }
                else
                {
                    $linea_nueva_reemplazo = false;
                    consultarCargoInsumoPorLinea($conex, $conexUnix, $wbasedato, $wemp_pmla, $arr_parametros, $fuente_insumo, $drodocdoc, $Tcarlin, $row['Tcarprocod'], $arr_FiltroSql, $historia_rep, $ingreso_rep, $wingreso_unx, $row, $arr_DocumentosFuentes, $row['Tcardoi'], $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia, $proceso_actualizacion, $arr_RIPS_msate, $linea_nueva_reemplazo, $row['cantidad']);
                }
            }
        }
        else
        {
            $proceso_actualizacion = false;
            // $data["error"] = 1;
            // $data["mensaje"] = "Problemas al generar el reporte";
            // $data["evidencia_error"][] = "drodocdoc: es un valor vacío, no esta integrado, Tcardoi: {$row['Tcardoi']}, fuen_insumo: {$row['fuen_insumo']}, Tcarprocod: {$row['Tcarprocod']}";
            // $data["evidencia_error"][] = $sqlu;
            $desc_error = "drodocdoc: es un valor vacio, no esta integrado, Tcardoi: {$row['Tcardoi']}, fuen_insumo: {$row['fuen_insumo']}, Tcarprocod: {$row['Tcarprocod']}, id_106: {$row['id_106']} ".PHP_EOL;
            registroLogErrorCRON($conex, $wbasedato, "", 'unix', utf8_encode($desc_error));
        }
    }
}

function calcularTarifa($conex_fn, $conexUnix, $wbasedato_fn, $wemp_pmla_fn, $arr_parametrosExtra, $codigo_insumo_mx, $linea_nueva_reemplazo, $row, $wcantidad, &$proceso_actualizacion, &$arr_tarifaRecalculada)
{
    foreach ($arr_parametrosExtra as $key => $value) {
        $$key = $value;
    }

    $conex     = $conex_fn;
    $wemp_pmla = $wemp_pmla_fn;
    $wbasedato = $wbasedato_fn;

    $actualiza_valores = false;

    global $conex;
    global $wemp_pmla;
    global $wbasedato;
    $wbasedato = $wbasedato_fn;

    if($linea_nueva_reemplazo || $row['cargo_nopos'] == 'on')
    {
        include_once("ips/funciones_facturacionERP.php");
        //Consultar tarifa
        $cod_empresa = $row['responsable_cargo'];
        $msj_tipo_insumo = "(PRODUCTO->Equivalente)";
        if($row['cargo_nopos'] == 'on')
        {
            $msj_tipo_insumo = "(Insumo->NO POS)";
            $cod_empresa = $row['responsable_pos'];
        }

        $arr_valor_cobro_insumo = datos_desde_procedimiento($codigo_insumo_mx, $row['concepto_cargo'], $row['cco_cargo'], '', $cod_empresa, $row['fecha_cargo_mx'], "");
        if($arr_valor_cobro_insumo['error'] == 1)
        {
            $proceso_actualizacion = false;
            $desc_error = "No hay tarifa de insumo {$msj_tipo_insumo}, Tcardoi: {$row['Tcardoi']}, fuen_insumo: {$row['fuen_insumo']}, linea_unix: {$linea_facardet}, codigo_insumo_mx: {$codigo_insumo_mx}, id_106_producto: {$row['id_106']} ".PHP_EOL;
            registroLogErrorCRON($conex, $wbasedato, "", 'matrix', utf8_encode($desc_error));
        }
        else
        {
            $wrecexc     = "";
            $wfacturable = "";
            if($cod_empresa != $codEmpParticular) // No se valida si es no pos porque en la condición if($row['cargo_nopos'] == 'on') ya se esta modificando a la empresa correcta cuando es NO POS
            {
                $condicion = CondicionMedicamento($codigo_insumo_mx, $cod_empresa, $row['cco_cargo']);
                switch($condicion)
                {
                    //  P --> va para excedente (no lo cubre)
                    //  N --> no facturable
                    //  C --> lo cubre la entidad
                    case "EXCEDENTE" :
                        $wrecexc = "E";
                    break;
                    case "NOFACTURABLE" :
                        $wfacturable = "N";
                    break;
                }
            }

            // --> Valor excedente
            $valor_excedente = 0;
            if($wrecexc == 'E')
            { $valor_excedente = round($wcantidad*($arr_valor_cobro_insumo['wvaltar']*1)); }
            else{
                // --> Valor reconocido
                    // $datosGrabarCargos['wvaltarReco'] = round($wcantidad*($valor_final_simulado*1));
            }

            $actualiza_valores = true;
            $arr_tarifaRecalculada["valor_unitario"] = $arr_valor_cobro_insumo['wvaltar']*1;
            $arr_tarifaRecalculada["valor_total"]    = round($wcantidad*($arr_valor_cobro_insumo['wvaltar']*1));
            $arr_tarifaRecalculada["valor_excedente"]= $valor_excedente;
        }
    }
    else
    {
        $cod_empresa = $row['responsable_cargo'];

        $valor_unitario_unx = 0;
        $codigo_tarifa_unx  = '';
        $selectfacar = "SELECT  cardetvun, cardettar
                        FROM    FACARDET
                        WHERE   cardetfue = '{$fuente_insumo}'
                                AND cardetdoc = '{$drodocdoc}'
                                AND cardethis = '{$historia_rep}'
                                AND (cardetnum = '{$wingreso_unx}' OR cardetnum = '{$ingreso_rep}')
                                AND cardetlin = '{$linea_facardet}'";
        if($resCargo = @odbc_exec($conexUnix, $selectfacar))
        {
            while (odbc_fetch_row($resCargo))
            {
                $valor_unitario_unx = odbc_result($resCargo,"cardetvun")*1;
                $codigo_tarifa_unx  = odbc_result($resCargo,"cardettar");
            }

            if($valor_unitario_unx != ($row["valor_unitario"]*1))
            {
                $actualiza_valores = true;
                $arr_tarifaRecalculada["valor_unitario"] = $row["valor_unitario"]*1;
                $arr_tarifaRecalculada["valor_total"]    = $row["valor_total_cargo"]*1;
                $arr_tarifaRecalculada["valor_excedente"]= $row["valor_excedente"]*1;
            }

            if($row["tarifa_cargo_mx"] != $codigo_tarifa_unx)
            {
                $arr_tarifaRecalculada["codigo_tarifa"] = $row["tarifa_cargo_mx"];
            }
        }
        else
        {
            $proceso_actualizacion = false;
            $desc_error = "No se pudo consultar el cargo en unix para saber el valor unitario: > ".PHP_EOL.odbc_errormsg();
            registroLogErrorCRON($conex, $wbasedato, $selectfacar, 'unix', $desc_error);
        }
    }
    return $actualiza_valores;
}

function actualizarCargoIvdrodet($conex, $conexUnix, $wbasedato, $wemp_pmla, $arr_parametrosExtra, $actualiza_valores, $arr_tarifaRecalculada, &$proceso_actualizacion)
{
    foreach ($arr_parametrosExtra as $key => $value) {
        $$key = $value;
    }

    if($actualiza_valores)
    {
        $valor_unitario = $arr_tarifaRecalculada["valor_unitario"];
        $valor_total    = $arr_tarifaRecalculada["valor_total"];

        $sqlIvdrodet = "UPDATE  ivdrodet
                                SET drodetpre = '{$valor_unitario}',
                                    drodettot = '{$valor_total}'
                        WHERE   drodetfue = '{$fuente_insumo}'
                                AND drodetdoc = '{$drodocdoc}'
                                AND drodetite = '{$linea_facardet}'
                                AND drodetart = '{$codigo_insumo_mx}'";
        if($resivUdt = @odbc_exec($conexUnix, $sqlIvdrodet))
        {
            //
        }
        else
        {
            $proceso_actualizacion = false;
            // echo odbc_errormsg()." > ".$sqlMsdro;
            $desc_error = "No se pudo actualizar ivdrodet: > ".PHP_EOL.odbc_errormsg();
            registroLogErrorCRON($conex, $wbasedato, $sqlIvdrodet, 'unix', $desc_error);
        }
    }
}


/**
 * [registroLogErrorCRON: función para registrar errores]
 * @param  [type] $conex               [Conexión a base de datos matrix]
 * @param  [type] $wbasedato           [Prefijo base de datos]
 * @param  [type] $nombre_script_error [Nombres del script donde se capturó el error]
 * @param  [type] $usuario_login       [Posible usuario logueado durante el error]
 * @param  [type] $sql_error           [Script sql que está generando el error]
 * @param  [type] $origen_tipo_error   [Origen donde se generó el error, matrix - unix]
 * @param  [type] $descripcion_error   [Descripción más exacta del error]
 * @return [type]                      [null]
 */
function registroLogErrorCRON($conex, $wbasedato, $sql_error, $origen_tipo_error, $descripcion_error, $nombre_script_error = '')
{
    $user_session = 'auto';
    if(array_key_exists('user',$_SESSION))
    {
        $user_session      = explode('-',$_SESSION['user']);
        $user_session      = $user_session[1];
    }

    $fecha         = date("Y-m-d");
    $hora          = date("H:i:s");

    if($nombre_script_error == '' && (array_key_exists('SCRIPT_NAME',$_SERVER)))
    {
        $nombre_script_error = $_SERVER['SCRIPT_NAME'];
    }

    $sql = "INSERT INTO root_000112
                    (Medico, Fecha_data, Hora_data,
                     Logpro, Loguse, Logsql, Logtip,
                    Logdes, Logrev, Logest, Seguridad)
            VALUES
                    ('{$wbasedato}', '{$fecha}', '{$hora}',
                    '{$nombre_script_error}', '{$user_session}', '".addslashes($sql_error)."', '{$origen_tipo_error}',
                    '".addslashes(utf8_decode($descripcion_error))."', 'off', 'on', 'C-{$user_session}') ";

    if($result = mysql_query($sql, $conex))
    {
        //
    }
    else
    {
        // echo "<pre>".mysql_error()." ".$sql."</pre>";
    }
}

/**
 * [actualizarCargosIngresosInactivosUnix: [APLICA PARA INSUMOS] Cuando se graban cargos a ingresos inactivos en unix, en matrix los cargos quedan con el ingreso inactivo y en unix
 *                                         quedan los cargos con el ingreso activo en el momento de la grabación, esta función se encarga de actualizar en unix
 *                                         el ingreso a los cargos que quedaron con el ingreso activo en unix al momento de la grabación por el ingreso con que realmente quedó
 *                                         el cargo en matrix, es decir, el ingreso que para unix es el inactivo. También se actualizan los RIPS con el consecutivo que
 *                                         corresponde al ingreso inactivo.]
 * @param  [type] $conex     [description]
 * @param  [type] $conexUnix [description]
 * @param  [type] $wemp_pmla [description]
 * @param  [type] $hay_unix  [description]
 * @return [type]            [description]
 */
function actualizarCargosIngresosInactivosUnix($conex, $conexUnix, $wemp_pmla, $hay_unix)
{
    // echo ($hay_unix) ? "hay_unix: $hay_unix <br>":'NO <br>';
    $codEmpParticular              = consultarAliasPorAplicacion($conex, $wemp_pmla, "codigoempresaparticular");
    $wbasedato                     = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");
    $wbasedato_movhos              = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    $rango_dias_modificar_ing_unix = consultarAliasPorAplicacion($conex, $wemp_pmla, "erp_rango_dias_modificar_ing_unix");
    $rango_dias_borrar_log         = consultarAliasPorAplicacion($conex, $wemp_pmla, "erp_rango_dias_borrar_log")*1;
    $rango_dias_modificar_ing_unix = ($rango_dias_modificar_ing_unix*1 > 0) ? ($rango_dias_modificar_ing_unix*1): 20;
    $fechaant                      = date( "Y-m-d",time()-3600*(24*$rango_dias_modificar_ing_unix) ); // se le restan 20 dias a la fecha actual
    $permitir_historia_ingreso_inactivo = consultarAliasPorAplicacion($conex, $wemp_pmla, "erp_permitir_historia_ingreso_inactivo");

    // Se le restan 45 dias a la fecha actual y a partir de esa fecha eliminar todo el historial hacia atrás para evitar acumular registros inecesarios.
    $fechaDelLog = date( "Y-m-d",time()-3600*(24*$rango_dias_borrar_log) );
    $sql = "DELETE FROM root_000112 WHERE Fecha_data < '{$fechaDelLog}'";
    if($result = mysql_query($sql, $conex))
    {
        //
    }

    if($permitir_historia_ingreso_inactivo == 'on')
    {
        $validacionUnixCorrecto = false;
        if($hay_unix && $conexUnix != '')
        {
            $validacionUnixCorrecto = true;
        }

        /*if($hay_unix)
        {
            if($conexUnix = @odbc_connect('facturacion','informix','sco'))
            {
                //
            }
            else
            {
                $validacionUnixCorrecto = false;
            }
        }
        else
        {
            $validacionUnixCorrecto = false;
        }*/

        $data              = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'',"evidencia_error" => array());
        $wccos_rep         = array();
        $arr_cargosReporte = array();

        $arr_consultas_por_historia = array();
        $arr_RIPS_msate = array();
        if($validacionUnixCorrecto)
        {
            // [1] Registro de insumos grabados fuente 11 Tcarfac
            // [2] Registro de insumos devueltos fuente 12
            $sql = "SELECT  c106.Tcarhis, c106.Tcaring, c106.Tcariux AS wingreso_unx, c106.Tcardoi, c106.Tcarlin AS linea_insumo, c106.Tcarfac AS facturable_mx, c106.id AS id_106, c106.Tcarprocod,
                            c106.Tcarfec AS fecha_cargo_mx, c106.Tcarfum AS fec_cargo_unx, c106.Tcartar AS tarifa_cargo_mx, c106.Tcarnps AS cargo_nopos, c106.Tcartrp AS tarifa_pos,
                            c106.Tcarvun AS valor_unitario, c106.Tcarrec AS reconocido_exced, c106.Tcarvto AS valor_total_cargo, c106.Tcarvex AS valor_excedente,
                            c106.Tcaraun AS actualizado_unx, c106.Tcarconcod AS concepto_cargo, c106.Tcarser AS cco_cargo, c106.Tcarres AS responsable_cargo, c106.Tcarrps AS responsable_pos,
                            c106.Tcarcan AS cantidad, mv3.Fdeubi, mv58.Logdoc , mv58.Logpro , mv2.Fenfue AS fuen_insumo, c107.Audrcu AS reg_unix
                    FROM    {$wbasedato}_000106 AS c106
                            INNER JOIN
                            {$wbasedato}_000107 AS c107 ON (c107.Audreg = c106.id AND c107.Audhis = c106.Tcarhis AND c107.Auding = c106.Tcaring)
                            INNER JOIN
                            {$wbasedato_movhos}_000003 AS mv3 ON (c106.Tcardoi = mv3.Fdenum AND c106.Tcarlin = mv3.Fdelin)
                            INNER JOIN
                            {$wbasedato_movhos}_000002 AS mv2 ON (c106.Tcardoi = mv2.Fennum)
                            LEFT JOIN
                            {$wbasedato_movhos}_000158 AS mv58 ON ( c106.Tcardoi = mv58.Logdoc AND c106.Tcarlin = mv58.Loglin)
                    WHERE   c106.Tcarfec > '{$fechaant}'
                            AND c106.Tcardoi <> ''
                            AND c106.Tcardev <> 'on'
                            AND c106.Tcarmiu = 'on'
                            AND c106.Tcarium = 'off'
                            AND c106.Tcarest = 'on'

                    UNION

                    SELECT  c106.Tcarhis, c106.Tcaring, c106.Tcariux AS wingreso_unx, c106.Tcardod AS Tcardoi, c106.Tcarlid AS linea_insumo, c106.Tcarfac AS facturable_mx, c106.id AS id_106, c106.Tcarprocod,
                            c106.Tcarfec AS fecha_cargo_mx, c106.Tcarfum AS fec_cargo_unx, c106.Tcartar AS tarifa_cargo_mx, c106.Tcarnps AS cargo_nopos, c106.Tcartrp AS tarifa_pos,
                            c106.Tcarvun AS valor_unitario, c106.Tcarrec AS reconocido_exced, c106.Tcarvto AS valor_total_cargo, c106.Tcarvex AS valor_excedente,
                            c106.Tcaraud AS actualizado_unx, c106.Tcarconcod AS concepto_cargo, c106.Tcarser AS cco_cargo, c106.Tcarres AS responsable_cargo, c106.Tcarrps AS responsable_pos,
                            c106.Tcarcan AS cantidad, mv3.Fdeubi, mv58.Logdoc , mv58.Logpro , mv2.Fenfue AS fuen_insumo, c107.Audrcu AS reg_unix
                    FROM    {$wbasedato}_000106 AS c106
                            INNER JOIN
                            {$wbasedato}_000107 AS c107 ON (c107.Audreg = c106.id AND c107.Audhis = c106.Tcarhis AND c107.Auding = c106.Tcaring)
                            INNER JOIN
                            {$wbasedato_movhos}_000003 AS mv3 ON (c106.Tcardod = mv3.Fdenum AND c106.Tcarlid = mv3.Fdelin)
                            INNER JOIN
                            {$wbasedato_movhos}_000002 AS mv2 ON (c106.Tcardod = mv2.Fennum)
                            LEFT JOIN
                            {$wbasedato_movhos}_000158 AS mv58 ON ( c106.Tcardod = mv58.Logdoc AND c106.Tcarlid = mv58.Loglin)
                    WHERE   c106.Tcarfec > '{$fechaant}'
                            AND c106.Tcardod <> ''
                            AND c106.Tcardev = 'on'
                            AND c106.Tcarmiu = 'on'
                            AND c106.Tcarium = 'off'
                            AND c106.Tcarest = 'on'";
            // echo "<pre>".print_r($sql,true)."</pre>";
            $arr_consultas_por_historia["principal"] = $sql;

            $arr_cargosHisFactNotas = array();
            $arr_DocumentosFuentes  = array();

            if($result = mysql_query($sql, $conex))
            {
                $Tcardoi_ant           = "";
                $fuente_insumo         = "";
                $drodocdoc             = "";
                $drodocdoc_facturado   = array();

                $arr_excluirHistorias = array();
                while ($row = mysql_fetch_assoc($result))
                {
                    $historia_rep = $row['Tcarhis'];
                    $ingreso_rep  = $row['Tcaring'];
                    $wingreso_unx = $row['wingreso_unx'];
                    $id_106       = $row['id_106'];
                    $idx_historia_ing_rep = $historia_rep.'_'.$ingreso_rep;
                    // if(!array_key_exists($idx_historia_ing_rep, $arr_excluirHistorias))
                    {
                        $proceso_actualizacion = true;
                        consultaCargosUnix($conex, $conexUnix, $wbasedato, $wemp_pmla, $wbasedato_movhos, $codEmpParticular, $wccos_rep, $row, $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $Tcardoi_ant, $fuente_insumo, $drodocdoc, $arr_DocumentosFuentes, $arr_consultas_por_historia, $proceso_actualizacion, $arr_RIPS_msate, $drodocdoc_facturado);

                        // Si no se presentó ningún error interno entonces actualice en la tabla de cargos matrix como cargo actualizado en unix con el ingreso inactivo.
                        // pero si en actualización falló algún proceso interno entonces no marcar el cargo matrix como actualizado en unix para que se tenga la oportunidad de
                        // ejecutar todo el proceso para ese insumo e intentar ejecutar correctamente todo el proceso, por ejemplo puede suceder que una tabla unix se encuentre
                        // temporalmente bloqueada y no haya permitido realizar el proceso de actualización completo.
                        $fec_hor_reg = date("Y-m-d H:i:s");
                        if($proceso_actualizacion)
                        {
                            // Actualizar cliame_106
                            $sql_Updt106 = "UPDATE {$wbasedato}_000106 SET Tcarium = 'on', Tcarfim = '{$fec_hor_reg}' WHERE id = '{$id_106}'";
                            if($result106 = mysql_query($sql_Updt106, $conex))
                            {
                                //
                            }
                            else
                            {
                                // echo mysql_error()." > ".$sql_Updt106;
                                $desc_error = "Error actualizando cargo insumo con ingreso inactivo en unix ".PHP_EOL.mysql_error();
                                registroLogErrorCRON($conex, $wbasedato, $sql_Updt106, 'matrix', utf8_encode($desc_error));
                            }
                        }
                        else
                        {
                            $desc_error = "Se genero algun error interno en el proceso de actualizacion del cargo matrix en unix, intentando modificar al ingreso inactivo [historia_rep: $historia_rep], [ingreso_matrix: $ingreso_rep], [wingreso_unix: $wingreso_unx], [id_106: $id_106]".PHP_EOL;
                            registroLogErrorCRON($conex, $wbasedato, "", 'matrix', utf8_encode($desc_error));
                        }
                    }
                }
            }
            else
            {
                $desc_error = "Error consultando insumos con ingreso modificable en unix. ".PHP_EOL.mysql_error();
                registroLogErrorCRON($conex, $wbasedato, $sql, 'matrix', utf8_encode($desc_error));
            }
        }
        else
        {
            echo "No UNIX";
        }

        // echo "<pre>".print_r($data,true)."</pre><br>";
        // echo "<pre>arr_cargosReporte: ".print_r($arr_cargosReporte,true)."</pre><br>";
        // echo "<pre>arr_cargosHisFactNotas: ".print_r($arr_cargosHisFactNotas,true)."</pre><br>";
        // echo "<pre>arr_DocumentosFuentes: ".print_r($arr_DocumentosFuentes,true)."</pre><br>";
    }
    else
    {
        echo "permitir_historia_ingreso_inactivo : INACTIVO EN ROOT_51";
    }
}


    // LLAMADO PRINCIPAL A LA FUNCIÓN QUE HARÁ EL ESCAN DE LOS PACIENTES
    //agendaPacientesUrgencias($wbasedato,$wbasedatohce,$wemp_pmla,$seguridad);
    // LLAMADO PARA LA FUNCION DE ACTUALIZAR MEDICAMENTOS DE MATRIX A UNIX
    actualizarMedicamentos($wemp_pmla);
    echo "FIN Proceso actualizarMedicamentos________________: ".date("Y-m-d H:i:s")."<br>";

    /**
     * Actualizar ingreso correcto cargos y RIPS en unix de insumos que desde matrix se grabaron a un ingreso inactivo pero que el integrador grabó a unix con el ingreso activo en ese momento
     * de la grabación a unix.
     * >> Edwar Jaramillo 2016-06-16
     */
    actualizarCargosIngresosInactivosUnix($conex, $conexUnix, $wemp_pmla, $hay_unix);
    echo "FIN Proceso actualizarCargosIngresosInactivosUnix_: ".date("Y-m-d H:i:s")."<br>";

    // -->  Actualizar turnos de urgencias que se hayan borrado.
    //      Jerson Trujillo 2016-04-11
    recuperarTurnosDeUrgencias();
    echo "FIN Proceso recuperarTurnosDeUrgencias____________: ".date("Y-m-d H:i:s")."<br>";


//Liberacion de conexion Matrix
liberarConexionBD($conex);

//Liberacion de conexion Unix
liberarConexionOdbc($conexUnix);
odbc_close_all();

echo "FINAL EJECUCION: ".date("Y-m-d H:i:s")."<br>";
?>