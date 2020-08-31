<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA       : generar_planos_furips.php
 AUTOR          : Edwar Jaramillo.
 FECHA CREACION : 23 Noviembre de 2016

 DESCRIPCION: Programa para generar archivos .txt de furips.


 Notas:
 --
 */ $wactualiza = "(Diciembre 22 de 2017)"; /*
 ACTUALIZACIONES:

 * Diciembre 22 2017 Edwar Jaramillo:
    - Se ordenan alfabéticamente las facturas de farmacia, se modifica el order by puesto que estas facturas tienen letras y no estaba ordenando como en las demás facturas
        cuyos códigos son solo numéricos.
    - Para las facturas de farmacia ordenar por letra con que inicia la factura y también numéricamente de menor a mayor, p.e.: a-181161,S-4857,s-10319,s-10373.
        En la publicación anterior estaba ordenando sólamente por la letra con que inicia la factura y no adicionalmente por los número que están después del guión.
    - Para farmacia no se estaba identificando cuales eran los conceptos de medicamentos para que en el archivo plano de furips2 el tipo de concepto pueda ser "1",
        para esto se crea un parámetro en root_51 "furips_medicamentos_farmacia" donde están configurados los conceptos separados por coma que corresponden a medicamentos,
        de igual manera estaba sucediendo al identificar los conceptos de materiales entonces se creó en root_51 el parámetro "furips_otros_servicios_farmacia" donde
        se incluyen separados por comas los conceptos de materiales.
 * Diciembre 20 2017 Edwar Jaramillo:
    - Código de facturas de farmacia que tengan letras ahora quedan en mayúsculas.
 * Diciembre 20 2017 Edwar Jaramillo:
    - La información para el archivo de furips1 no estaba quedando completa, faltaban datos del accidente debído a que se estaba consultando en otras tablas porque
        no teníam fuente "01", para las facturas de farmacia se empezó a hacer la consulta de los datos del accidente como si fueran fuente "01" y poder encontrar
        los datos que estaban quedando en blanco.
    - No se estaban detectando para farmacia los conceptos de inventario para usar el código invima de los materiales y medicamentos, además se agregó una nueva columna en
        la consulta de materiales y medicamentos de farmacia para poder utilizar el código invima en los cargos de inventario.
 * Diciembre 19 2017 Edwar Jaramillo:
    - Nueva opción para generar archivos planos de farmacia seleccionando un checkbox, además de actualizar automáticamente el select de las empresas correspondientes,
        también se crean los archivos planos usando los códigos de conceptos, materiales y medicamentos de los maestros de farmacia.
 * Octubre 02 2017 Edwar Jaramillo:
    - En el campo [29] del furips1 se guarda la placa del carro pero cuando se reemplazaba el guion "-" quedaba un espacio y esto generaba error en la malla validadora. Se corrigió
 * Agosto 29 2017 Edwar Jaramillo:
    - Si hay número de radicado respuesta_glosa=0.
    - LimpiarString quita todos los caracteres no permitidos entre ellos puntos, comas, si este era un número decimal se le quitó el punto,
        se valída si quedó algún espacio y se cambia por punto para evaluar si es decimal o no.
 * Julio 25 2017 Edwar Jaramillo:
    - Se elimina el caracter ";" de las descripciones en furips2.
    - Validación para saber si el estado de cartera es AP para dejar vacío el campo [2] respuesta a glosa en furips1.
    - Se modifican los nombres de las tablas temporales en unix para disminuir el error al máximo sobre la longitud de nombre de tabla en unix.
 * Mayo 09 2017 Edwar Jaramillo:
    - El código del médico en el campo [95] del furips 1 estaba quedando con un espacio entre los caracteres lo que producía una inconsistencia en la malla validadora,
        ya se corrigió para que no pase de nuevo, se debía al momento de sustituir el caracter "/".
 * Abril 10 2017 Edwar Jaramillo:
    - Función "limpiarStringTelefonos" es modificada para validar de una forma diferentes los número de contacto y generar alerta si es un número con formato no válido.
        Se aceptan números de siete o diez digitos como números válidos.
    - Nueva alerta para identificar si en los valores unitarios o tatales del furips2 hay números con decimales, pues en la malla validadora se generará un error
        porque siempre esperá un valor entero.
    - A la función "obligatorioVal" se le agrega un nuevo parámetro que ayuda a realizar nuevas validadiones, en este caso se está usando para validar si hay número decimales o no y generar alerta.
    - El campo 2-respuesta de glosa, del furips1 ahora tiene en cuenta si es una glosa total el valor del campo es cero o vacío en cualquier otro caso.
    - Los campos 28,29 (Marca y placa) se les definió si son obligatorios o no según el campo estado de aseguramiento.
    - [Correción] Cuando se mostraba la alerta de número de teléfono o celular incorrecto, no se estaba mostrando correctamente el número de la factura en la que se generó la alerta.
 * Marzo 24 2017 Edwar Jaramillo:
    - Estaba invertida la lógica para consultar los datos de conductor y propietario.
    - El campo de excedente de póliza se calcula según la cantidad de responsables del paciente que estén en la tabla fasalacc por historia y número de accidente, si hay igual o más
        de tres responsables entonces el excedente de póliza es "1" sino es "0".
 * Marzo 22 2017 Edwar Jaramillo:
    - Nuevo campo para centro de costos, se debe tener en cuenta que en el archivo plano solo se pueden generar los cargos que tengan centro de costo "*"
    - La cantidad del cargo ahora es Cantidad a reclamar Gdecre.
 * Marzo 16 2017 Edwar Jaramillo:
    - Se crea indicador de facturas seleccionadas y sin seleccionar, cuando quedan facturas seleccionadas y se quiere generar el archivo plano se pide confirmación para continuar.
 * Marzo 14 2017 Edwar Jaramillo:
    - Cuando se identifica que el tipo de aseguramiento es 3 o "F" en unix, no se realiza la consulta para datos del conductor ni se llenas los datos del propietario.
    - No se estan ordenando de mayor a menor los números de las facturas porque se estaba interpretando como string y no como número.
    - Cuando se generaba de nuevo un archivo plano con el mismo consecutivo se estaba adicionando el contenido al archivo antes creado, ahora se verifica si el archivo
        de mismo nombre existe entonces se elimina para crearlo nuevamente.
    - Si ayuda no tiene diagnóstico de egreso entonces este será el mismo diagnóstico de ingreso.
 * Marzo 13 2017 Edwar Jaramillo:
    - Si estado_aseguramiento = 5 no debe llenar los campos de departamento y municipio del conductor, deben ser vacíos.
 * Marzo 07 2017 Edwar Jaramillo:
    - El valor unitario se calcula sobre la cantidad glosada y no sobre la cantidad ingresada.
    - Los cargos que tengan valor aceptado igual a valor glosado no se deben mostrar en el archivo porque la totalidad de la glosa fue aceptada.
    - Alerta cuando un cargo de insumo no tiene código invima.
    - Las ayudas diagnósticas si tienen una tabla de egreso, de esa tabla se empieza a leer la fecha, hora y diagnóstico de egreso.
 * Marzo 06 2017 Edwar Jaramillo:
    - Corrección campo diagnóstico de ingreso.
    - Validación del campo cédula de conductor que puede ser un campo null.
    - Correción al leer el campo de municipio del conductor.
    - La cédula del conductor no se lee desde la tabla de datos de propietario-conductor sino de la tabla de detalle del accidente.
    - El caracter guión '-' no se puede reemplezar por un espacio por que en los número como el código de médico queda con un espacio que la malla validadora
        se interpreta como error.
    - Se valída si la factura está asociada a una historia de Ayuda, en ese caso se debe consultar información del paciente en la tabla aymov de unix, pues
        la factura queda asociada a un consecutivo de cargos y no queda con un número de ingreso.
    - Para un cargo de ayudas, el diagnóstico de egreso es el mismo de ingreso, asi como las fechas de ingreso y egreso.
 * Marzo 03 2017 Edwar Jaramillo:
    - Se valída si es un examen para buscar en la tabla inexasoat el código correcto a mostrar según el CUPS del examen y el nombre asociado.
    - Se hizo una corrección cuando se consulta primero en la relación por empresa, si ya encontró código propio en esa tabla de relación entonces no
        intente nuevamente consultar en maestro procedimiento y luego nuevamente en la relación por empresa con un nuevo código CUPS. Si ya encontró código
        propio entonces no intente buscar otro.
    - Hay algunos códigos que son procedimientos, pero no es posible encontrar una relación por empresa para encontrar un código soat, a pesar de ser procedimientos
        (p.e Concepto:0079 Procedimiento: 389103) no se encuentra el código por grupo Qx asociado a código de la clinica, en este caso entonces se debe ir a buscar
        el código correcto para el archivo plano en la tabla de inexasoat. Entonces, si no se encontró código propio entonces se intenta buscar directamente en
        inexasoat con el código del procedimiento.
    - Para el concepto 0037 y en general para todos los conceptos a los que no se les pueda identificar si es de procedimiento o examen, por defecto se busca
        el código del cargo en inexasoat. Este caso funcionó para los códigos de cargos de hospitalización que tienen en código "I" o aplicaciones por ejemplo
        para el código de cargo "935301".
    - Departamento y ciudad se igualan al del paciente si el documento es el mismo.
    - Al valor glosado se le resta el valor aceptado.
    - Medicamentos que no tienen código invima se marcan con "NO_INVIMA" para que los actualicen manualmente.
    - Formato de hora correcto cuando es menor a 10, se debe completar con cero a la izquierda para que la malla validadora no genere error.
 * Marzo 01 2017 Edwar Jaramillo:
    - Cuando los conceptos son compartidos se estaba modificando el nombre a mostrar del cargo pero el código no era el correcto, se estaba colocando el código soat
        para esos conceptos pero la malla validadora no acepta ese código sino el código de la relación conceptoUnx-codigoSoat que en unix se encuentra en la tabla
        "faquicon", el código a mostrar es el que se encuentra en el campo "quiconcod" luego de relacionar el grupo quirúrgico y el concepto del cargo unix.
    - El concepto 0163 tambien debe tener el mismo comportamiento que los conceptos compartidos, pues debe mostrar en el nombre el grupo quirúrgico y el código
        del cargo asociado al código de grupo.
 * Febrero 24 2017 Edwar Jaramillo:
    - Corrección en los saltos de línea de los archivos planos para que todos los editores puedan interpretar el salto de línea.
    - El campo nombre de furips2 está leyendo desde unix un caracter extraño que al momento de ser grabado en el archivo plano se muestra como una coma ","
        se captura ese caracter raro y se ingresa en los caracteres no permitidos para que sea eliminado del texto.
 * Febrero 22 2017 Edwar Jaramillo:
    - Se eliminan nuevos caracteres especiales de los textos.
    - La función que elimina los archivos planos de días anteriores ahora tiene en cuenta cuántos días hacias atrás debe conservar, para dar la posibilidad de
        descargar archivos despues de generados, durante un determinado tiempo, esto con el fin de tener un caché de archivos pero igualmente poder ir eliminando
        y evitar que el directorio de generación de archivos pueda crecer.
    - La busqueda de historias en matrix se limíta a que se haga a partir de ingresos generados en 2017-03-01, de ahí hacia atrás siempre debe buscar la información
        del paciente y accidente en unix.
    - Nuevo mensaje de alertas cuando no se encuentran código de exámenes o procedimientos en los maestros.
    - Solo buscar información de procedimiento en matrix si no se hizo consulta de paciente en unix.
    - Nueva sección y funciones javascript para consultar historial de archivos generados en días anteriores, PENDIENTE DESARROLLAR FUNCIÓN PHP PARA CONSULTAR ARCHIVOS.
    - Correción al leer los datos del accidente porque al leer el número de ingreo principal del accidente no se estaba leyedo el campo correctamente. Adicionalmente
        al momento de leer el detalle del accidente "inaccdet" se quita el filtro de ingreso y se busca solo por el consecutivo de accidente, pues con el número de ingreso
        que llega por parámetros a la función "consultar_datos_accidente_paciente" es el ingreso con el que se generó la factura y puede corresponder a un ingreso de revisión
        y no del accidente inicial, entonces en "inacc" se busca el consecutivo de accidente que es común a todos los ingresos de revisión y con ese consecutivo e historia se
        busca el detalle de accidente.
    - Se permiten borrar archivos temporales creados de día anterior hacia atrás.
    - Se crea nueva función y se termina la funcionalidad para mostrar un listado de todos los archivos planos con consecutivo que fueron creados en días anteriores,
        el límire de recuperación de archivos está dado por la variable "dias_cache_archivos" pues los archivos con más de esos días de creación son borrados.
 * Febrero 21 2017 Edwar Jaramillo:
    - La función "queryEncabezadoFacturas" ahora puede filtrar por número de factura o por un conjunto de ID's de encabezados de facturas separados por comas.
    - Se adiciona campo en encabezado donde se guarda el número de consecutivo de archivo plano.
    - La función "cambiarEstadoFacturasFiltradas" encargada de cambiar a estado Revisada los encabezados de facturas AP-archivoPlano en este momento no está
        operando porque no se está permitiendo desde la interfas consultar facturas ya generadas en un archivo plano. En esos casos se debería modificar el archivo plano directamente.
    - Nueva funcionalidad que permite seleccionar las facturas con las que se quiere generar el archivo plano, anteriormente se generaban con todas las facturas revisadas y no se permitía excluir.
    - Al momento de cambiar el estado de los encabezados de revisado a generado se guarda también el número de consecutivo con el que se generó el archivo plano.
 * Febrero 14 2017 Edwar Jaramillo:
    - Nueva función "obtenerTipoFacUnix" para consultar el tipo de facturación y el nombre de procedimientos y exámente cuando esos códigos no se pueden encontrar en matrix.
    - Cuando el cargo es de grupo quirúrgico solo concatenar la modalidad y el grupo Qx a los conceptos que son compartidos "$tipo_concepto = [C]".
    - Cuando se consulta procedimiento o examen en unix, siempre se trata de encontrar el código propio que se mostrará en el archivo plano.
    - Se eliminan caracteres de los textos como "+,#,*,),(".
    - Según Cristian de glosas, cuando el estado de aseguramiento es "3-Carro fantasma" no deben haber datos de municipio ni departamento del conductor del vehículo.
 * Febrero 13 2017 Edwar Jaramillo:
    - Nuevo procesos para buscar información del paciente y de accidentes en unix, pues hay facturas muy antiguas de las cuales no se encuentra información
        en matrix.
    - En la función "queryEncabezadoFacturas" ahora se incluye la relación con la tabla de detalle 000174 para que no aparezcan los encabezados que en cuyo detalle
        tengan valor glosado todal en cero.
    - Nueva regla que estaba pendiente por desarrollar donde el tipo de vehículo es obligatorio si el estado e aseguramiento es 1,2 o 4.
    - Se crea la función "consultar_diagnostico_medico_egreso" para traer los diagnósticos del paciente, teniendo en cuenta si la información del paciente se está consultando
        en unix o matrix.
 * Enero 25 2017 Edwar Jaramillo:
    - Los campos 98 y 99 de FURIPS1 Siempre deben ir en cero.
 * Enero 17 2017 Edwar Jaramillo:
    - Se quitó el filtro de estado del maestro de artículos porque hay códigos que fueron usados en cargos de facturas y que hoy en día ya son códigos inactivos de artículos.
    - Al la consulta de encabezados de facturas se le adiciona un filtro adicional por estado de cartera para facturas.
    - A partir de este momento los códigos de insumos que se graban al archivo plano se muestran con códigos CUMS.
    - Algunos códigos de procedimientos que se intentan escribir en el archivo plano ya vienen como código soat, en ese caso lo que se hace al consultar ese código soat a que
        código cups corresponde y con ese buscar el grupo quirúrgico.
    - Se formatean los números que conforman los códigos CUMS quitando los ceros a la izquierda a lado y lado del guión ej. 000123-015 => 123-15. el campo de código para el archivo
        plano queda vacío y el tipo de registro queda con tipo=5.

 Reglas-validaciones:
    * FURIPS - Datos de reclamación: FURIPS1
        - Campos que contengan direcciones, tener presente que al archivo plano no pueden viajar comas (,), puntos (.), guiones (-) o slash (/).
        - Campos que contengan telefonos, tener presente que al archivo plano no pueden viajar comas (,), puntos(.), guiones (-) y trabajar la información según lo acordado en la reunión.
        - En el campo 20, Descripción del evento, tener presente que al archivo plano no pueden viajar comas (,), puntos (.), guiones (-) o slash (/).
        - En el campo 32, Número de la póliza, al archivo plano no puede viajar con comas (,), puntos (.), guiones (-) o slash (/).
        - Para los nombres y apellidos, no deben viajar puntos (.).
        - En los casos en que el documento de identidad del lesionado, sea el mismo del conductor y a su vez del propietario, que en los tres registros se tenga la misma información de telefonos y direcciones.
        - Campo 96, Total facturado por amparo de gastos medicos quirurgicos, debe viajar al archivo plano es el valor de la factura antes sin tener en cuenta nota crédito.
        - Campo 97, Total reclamado por amparo de gastos medicos quirurgicos, debe viajar al archivo plano es el valor de glosa a reclamar.
    * FURIPS- Detalle de Factura: FURIPS2
        - Tener presente las siguientes consideraciónes:
        - Campo 4.  Código de Servicio:  Deben ir codigos Soat, para servicios médicos.  (según lo que dice la descripción).
        - Para los códigos CUM, se debe suprimir los ceros(0), que se encuentran a la izquierda y si después del guión(-), también se tienen ceros a la izquierda, también se eliminan. No se puede eliminar el guión (-).  Ejemplo, si se tiene el código CUM 020040898-01; se le deben borrar los ceros y por tanto el código debe quedar 20040898-1.
        - Todo lo que sea materiales, debe traer en blanco este campo.
        - Campo 6. Cantidad de servicios: Se debe netear la cantidad, cuando se tienen positivos y negativos.
        - Campo 7.  Valor unitario: Tener presente que este valor no puede variar al netear cantidades ni valores totales.
        - Campo 8. Valor total facturado:  Se debe netear el valor, cuando se tienen positivos y negativos.
 **/

$fecha_actual = date("Y-m-d");
$hora_actual  = date("H:i:s");
$dir          = '../../planos/ips_furips_glosas'; // Directorio archivos planos






if(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}
elseif(!isset($accion) && !array_key_exists('user',$_SESSION))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
$user_session      = explode('-',$_SESSION['user']);
$user_session      = $user_session[1];

/*****  DICCIONARIO LOCAL *****/
// define('PROCEDIMIENTO'      ,'Procedimiento');

/********************** INICIO DE FUNCIONES *************************/

/**
 * [seguimiento description: Función para uso solo de desarrollo, en ambiente local, crea un archivo de texto donde se imprimen variables y arrays para su seguimiento]
 * @param  [type] $seguir [Cadena de texto a guardar en el archivo, para guardar array recordar usar print_r($al_array, true), puede usar saltos de líea PHP así PHP_EOL ]
 * @return [type]         [description]
 */
function seguimiento($seguir)
{
    $fp = fopen("seguimiento.txt","a+");
    $fec = "[".date("Y-m-d H:i:s")."]";
    fwrite($fp, $fec.PHP_EOL.$seguir);
    fclose($fp);
}

/**
 * [limpiarString: quita multiples espacios y espacios al final del string]
 * @param  [type] $string_ [description]
 * @return [type]          [description]
 */
function limpiarString($string_)
{
    $patrones     = array();
    $patrones[0]  = '/[,]/';
    $patrones[1]  = '/[.]/';
    $patrones[2]  = '/[-]/';
    $patrones[3]  = '/[\/]/';
    $patrones[4]  = "/[']/";
    $patrones[5]  = '/["]/';
    $patrones[6]  = '/[\(]/';
    $patrones[7]  = '/[\)]/';
    $patrones[8]  = '/[\*]/';
    $patrones[9]  = '/[\+]/';
    $patrones[10] = '/[\#]/';
    $patrones[11] = '/[\[]/';
    $patrones[12] = '/[\]]/';
    $patrones[13] = '/[\%]/'; // Algunos código de médico parecen tener un guión '-' al reemplazar se interpreta como un porcentaje '%'
    $patrones[14] = '/[\?]/';
    $patrones[15] = '/[\]/';//Caracter raro que está llegando desde unix
    $patrones[16] = '/[;]/';
    $sustituciones = array();
    $sustituciones[16] = ' ';
    $sustituciones[15] = ' ';
    $sustituciones[14] = ' ';
    $sustituciones[13] = '';
    $sustituciones[12] = ' ';
    $sustituciones[11] = ' ';
    $sustituciones[10] = ' ';
    $sustituciones[9]  = ' ';
    $sustituciones[8]  = ' ';
    $sustituciones[7]  = ' ';
    $sustituciones[6]  = ' ';
    $sustituciones[5]  = ' ';
    $sustituciones[4]  = ' ';
    $sustituciones[3]  = '';
    $sustituciones[2]  = '';
    $sustituciones[1]  = ' ';
    $sustituciones[0]  = ' ';

    $string_ = preg_replace($patrones, $sustituciones, $string_);

    return limpiarStringEspacios($string_);
}

/**
 * [limpiarString: quita multiples espacios y espacios al final del string]
 * @param  [type] $string_ [description]
 * @return [type]          [description]
 */
function limpiarStringEspacios($string_)
{
    return trim(preg_replace('/[ ]+/', ' ', $string_));
}

/**
 * [limpiarString: quita multiples espacios y espacios al final del string]
 * @param  [type] $string_ [description]
 * @return [type]          [description]
 */
function limpiarStringTelefonos($numero_campo,$arr_descArch,&$arr_alert,$tipo_archivo,$index_fact,$string_)
{
    $numero_posible  = limpiarString(preg_replace('/[ ]+/', '', $string_)); // Quita espacios y caracteres especiales.
    $numero_correcto = true;
    if(!numeroCelularFijo($numero_posible))
    {
        $numero_correcto = false;
    }

    if(!$numero_correcto){
        $expl            = explode("-", $string_);
        $numero_posible  = limpiarString($expl[0]);
        $numero_correcto = numeroCelularFijo($numero_posible);
    }

    if(!$numero_correcto){
        $expl            = explode(" ", $string_);
        $numero_posible  = limpiarString(preg_replace('/[ ]+/', '', $expl[0]));
        $numero_correcto = numeroCelularFijo($numero_posible);
    }

    if(!$numero_correcto){
        $expl            = explode(":", $string_);
        $numero_posible  = limpiarString(preg_replace('/[ ]+/', '', $expl[0]));
        $numero_correcto = numeroCelularFijo($numero_posible);
    }

    if(!$numero_correcto){
        $expl            = explode(",", $string_);
        $numero_posible  = limpiarString(preg_replace('/[ ]+/', '', $expl[0]));
        $numero_correcto = numeroCelularFijo($numero_posible);
    }

    if(!$numero_correcto){
        $expl            = explode("/", $string_);
        $numero_posible  = limpiarString(preg_replace('/[ ]+/', '', $expl[0]));
        $numero_correcto = numeroCelularFijo($numero_posible);
    }

    if($numero_correcto){
        $string_ = $numero_posible;
    }else{
        $msj_add = ' <span style="color:#B77300;">[Número incorrecto ('.$numero_posible.')]</span>';
        agregarAlerta($index_fact,$tipo_archivo,$numero_campo,$arr_alert,$arr_descArch,$msj_add);
    }

    return limpiarString($string_);
}

function numeroCelularFijo($numero)
{
    return ($numero == '' || strlen($numero) == 7 || strlen($numero) == 10);
}

/*function reglasFurips1()
{
    $arr_campos_furips1Config[] = array("concepto"       =>"Número de radicado anterior",
                                        "descripcion"    =>"Campo obligatorio en caso de diligenciarse RG (Respuesta a glosa)",
                                        "val_permitidos" =>array(""),
                                        "val_defecto"    =>"",
                                        "obligatorio"    =>false, // obligatorio sin importar si depende_de esta diligenciado
                                        "obligatorio_si" =>false, // obligatorio cuando el depende_de esta diligenciado
                                        "depende_de"     =>2,
                                        "longitud"       =>10); //[1]
}*/

//Constructor de Queries UNIX no se pueden mas de 9 campos para verificar si son nulos o no
function construirQueryUnix( $tablas, $campos_nulos, $campos_todos='', $condicionesWhere='',$defecto_campos_nulos=''){

    $condicionesWhere = trim($condicionesWhere);

    if( $campos_nulos == NULL || $campos_nulos == "" ){
        $campos_nulos = array("");
    }

    if( $tablas == "" ){ //Debe existir al menos una tabla
        return false;
    }

    if(gettype($tablas) == "array"){
        $tablas = implode(",",$tablas);
    }

    $pos = strpos($tablas, ",");
    if( $pos !== false && $condicionesWhere == ""){ //Si hay mas de una tabla, debe mandar condicioneswhere
        return false;
    }

    //Si recibe un string, convertirlo a un array
    if( gettype($campos_nulos) == "string" )
        $campos_nulos = explode(",",$campos_nulos);

    $campos_todos_arr = array();

    //Por cual string se reemplazan los campos nulos en el query
    if( $defecto_campos_nulos == "" ){
        $defecto_campos_nulos = array();
        foreach( $campos_nulos as $posxy=>$valorxy ){
            array_push($defecto_campos_nulos, "''");
        }
    }else{
        if(gettype($defecto_campos_nulos) == "string"){
            $defecto_campos_nulos = explode(",",$defecto_campos_nulos);
        }
        if(  count( $defecto_campos_nulos ) == 1 ){ //Significa que todos los campos nulos van a ser reemplazados con el mismo valor
            $defecto_campos_nulos_aux = array();
            foreach( $campos_nulos as $posxyc=>$valorxyc ){
                array_push($defecto_campos_nulos_aux, $defecto_campos_nulos[0]);
            }
            $defecto_campos_nulos = $defecto_campos_nulos_aux;
        }else if(  count( $defecto_campos_nulos ) != count( $campos_nulos ) ){
            return false;
        }
    }

    if( gettype($campos_todos) == "string" ){
        $campos_todos_arr = explode(",",trim($campos_todos));
    }else if(gettype($campos_todos) == "array"){
        $campos_todos_arr = $campos_todos;
        $campos_todos = implode(",",$campos_todos);
    }
    foreach( $campos_todos_arr as $pos22=>$valor ){ //quitar espacios a cada valor
        $campos_todos_arr[$pos22] = trim($valor);
    }
    foreach( $campos_nulos as $pos221=>$valor1 ){ //quitar espacios a cada valor
        $campos_nulos[$pos221] = trim($valor1);

        //Si el campo nulo no existe en el arreglo de todos los campos, agregarlo al final
        $clavex = array_search(trim($valor1), $campos_todos_arr);
        if( $clavex === false ){
            array_push($campos_todos_arr,trim($valor1));
        }
    }
    //Quitar la palabra and, si las condiciones empiezan asi.
    if( substr($condicionesWhere, 0, 3)  == "AND" || substr($condicionesWhere, 0, 3) == "and" ){
        $condicionesWhere = substr($condicionesWhere, 3);
    }
    $condicionesWhere = str_replace("WHERE", "", $condicionesWhere); //Que no tenga la palabra WHERE
    $condicionesWhere = str_replace("where", "", $condicionesWhere); //Que no tenga la palabra WHERE

    $query = "";

    $bits = count( $campos_nulos );
    if( $bits >= 10 ){ //No pueden haber más de 10 campos nulos
        return false;
    }

    if( $bits == 1 && $campos_nulos[0] == "" ){ //retornar el query normal
        $query = "SELECT ".$campos_todos ." FROM ".$tablas;
        if( $condicionesWhere != "" )
            $query.= " WHERE ".$condicionesWhere;
        return $query;
    }

    $max = (1 << $bits);
    $fila_bits = array();
    for ($i = 0; $i < $max; $i++){
        /*-->decbin Entrega el valor binario del decimal $i,
          -->str_pad Rellena el string hasta una longitud $bits con el caracter 0 por la izquierda:
           EJEMPLO $input = "Alien" str_pad($input, 10, "-=", STR_PAD_LEFT);  // produce "-=-=-Alien", rellena por la izquierda hasta juntar 10 caracteres
          -->str_split Convierte un string (el entregado por str_pad) en un array, asi tengo el arreglo con el codigo binario generado
        */
        $campos_todos_arr_copia = array();
        $campos_todos_arr_copia = $campos_todos_arr;

        $fila_bits = str_split( str_pad(decbin($i), $bits, '0', STR_PAD_LEFT) );
        $select = "SELECT ";
        $where = " WHERE ";
        if( $condicionesWhere != "" )
            $where.= $condicionesWhere." AND ";

        for($pos = 0; $pos < count($fila_bits); $pos++ ){
            if($pos!=0) $where.= " AND ";
            if( $fila_bits[$pos] == 0 ){
                $clave = array_search($campos_nulos[$pos], $campos_todos_arr_copia);
                //if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = "'.' as ".$campos_nulos[$pos];
                if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = $defecto_campos_nulos[$pos]." as ".$campos_nulos[$pos];
                $where.= $campos_nulos[$pos]." IS NULL ";
            }else{
                $where.= $campos_nulos[$pos]." IS NOT NULL ";
            }
        }

        $select.= implode(",",$campos_todos_arr_copia);
        $query.= $select." FROM ".$tablas.$where;
        if( ($i+1) < $max ) $query.= " UNION ";
    }
    return $query;
}

function fechaFurips($fecha)
{
    if($fecha != '')
    {
        $fecha = explode("-", $fecha); // AAAA-MM-DD
        $fecha = $fecha[2].'/'.$fecha[1].'/'.$fecha[0]; // DD/MM/AAA
    }
    return $fecha; // DD/MM/AAA
}
function horaFurips($hora)
{
    $hora = str_replace(".", ":", $hora);
    if($hora != '')
    {
        $hora    = explode(":", $hora); // HH:MM:SS
        $hora[0] = (($hora[0]*1) < 10) ? '0'.($hora[0]*1): $hora[0];
        $hora    = $hora[0].':'.$hora[1]; // HH:MM
    }
    return $hora;
}

function generarArrArticulos($conex, $wbasedato_movhos)
{
    // --> Obtener maestro de articulos
    $arrayArticulo = array();
    $sqlArt = " SELECT  Artcod, Artgen, Artcum
                FROM    {$wbasedato_movhos}_000026";
                // WHERE   Artest = 'on'";
    $resArt = mysql_query($sqlArt, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlArt):</b><br>".mysql_error());
    while($rowArt = mysql_fetch_array($resArt))
    {
        $arrayArticulo[$rowArt['Artcod']] = array("nombre_gen"=>utf8_encode(trim($rowArt['Artgen'])), "codigo_cum"=>trim($rowArt['Artcum']));
    }
    return $arrayArticulo;
}
function generarArrArticulosEmpresa($conex, $wbasedato_empresa)
{
    // --> Obtener maestro de articulos
    $arrayArticulo = array();
    $sqlArt = " SELECT  Artcod, Artgen, Artcna AS Artcum
                FROM    {$wbasedato_empresa}_000001";
                // WHERE   Artest = 'on'";
    $resArt = mysql_query($sqlArt, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlArt):</b><br>".mysql_error());
    while($rowArt = mysql_fetch_array($resArt))
    {
        $arrayArticulo[$rowArt['Artcod']] = array("nombre_gen"=>utf8_encode(trim($rowArt['Artgen'])), "codigo_cum"=>trim($rowArt['Artcum']));
    }
    return $arrayArticulo;
}

function generarArrProcedimientos($conex, $wbasedato_cliame)
{
    // --> Obtener maestro de procedimientos en matrix
    $arrayProcedimientos = array();
    $sqlPro = " SELECT  Procod, Pronom, Progqx, Propun
                FROM    {$wbasedato_cliame}_000103
                WHERE   Proest = 'on'";
    $resPro = mysql_query($sqlPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPro):</b><br>".mysql_error());
    while($rowPro = mysql_fetch_array($resPro))
    {
        $arrayProcedimientos[$rowPro['Procod']] = array("nombre" => utf8_encode(trim($rowPro['Pronom'])), "Progqx" => $rowPro['Progqx'], "Propun" => $rowPro['Propun']);
    }
    return $arrayProcedimientos;
}


/**
 * [obtener_array_procedimientosEmpresa: Esta función se encarga de adicionar al array de procedimientos generales (000103) los procedimientos que
 * existan en la tabla 000070 para la empresa responsable del paciente, si se va a adicionar un código entonces se verifíca si el código ya existe en el array
 * que llega por parámetros y de ser así entonces actualiza el nombre del procedimiento tal como aparezca para esa empresa en 000070.]
 * @param  [type] $conex              [Conexión a la base de datos]
 * @param  [type] $wemp_pmla          [Código de la empresa de promotora las américas]
 * @param  [type] $wbasedato          [Prefijo de las tablas de la base de datos]
 * @param  [type] $wcod_empresa       [Código de la empresa para la que se van a buscar códigos de procedimientos en la relación procedimientos-empresa 000070]
 * @param  array  $arr_procedimientos [Array de procedimientos, pueden llegar inicialmente los procedimientos existentes en 000103]
 * @return [type]                     [Retorna un array con los procedimientos iniciales que hayan llegado por parámetros más los posibles procedimientos encontrados en la relación procedimiento-empresa]
 */
function obtener_array_procedimientosEmpresa($conex, $wemp_pmla, $wbasedato, $wcod_empresa, $arr_procedimientos = array())
{
    if(!empty($wcod_empresa))
    {
        $sql = "SELECT  Proempcod AS codigo, Proempnom AS nombre, Proemppro AS codigo_personalizado, Proemptfa AS tipo_facturacion
                FROM    {$wbasedato}_000070
                WHERE   Proempemp = '{$wcod_empresa}'
                        AND Proempest = 'on'
                ORDER BY Proempnom";
        $resPro = mysql_query($sql,$conex) or die("Procedimientos por empresa - Error: ".mysql_errno()." ".$qPro." - ".mysql_error());
        while ($row = mysql_fetch_array($resPro))
        {
            $arr_procedimientos[$row['codigo']] = array( "codigo_propio"=>$row['codigo_personalizado'],
                                                    "nombre_propio"=>utf8_encode(limpiarString($row['nombre'])),
                                                    "tipo_facturacion"=>$row['tipo_facturacion']);
        }
    }
    return $arr_procedimientos;
}

function obtener_array_concepto_matrix_unix($conex, $wemp_pmla, $wbasedato)
{
    $arr_conceptos = array();

    $sql = "SELECT  c197.Congen AS concepto_unx, c197.Consim AS concepto_mx, c200.Grutip, c200.Grudes, c200.Gruinv
            FROM    {$wbasedato}_000197 AS c197
                    INNER JOIN
                    {$wbasedato}_000200 AS c200 ON (c197.Consim = c200.Grucod)";
    $resPro = mysql_query($sql,$conex) or die("Procedimientos por empresa - Error: ".mysql_errno()." ".$qPro." - ".mysql_error());
    while ($row = mysql_fetch_array($resPro))
    {
        $arr_conceptos[$row['concepto_unx']] = array(   "concepto_unx"  => $row['concepto_unx'],
                                                        "concepto_mx"   => $row['concepto_mx'],
                                                        "tipo"          => $row['Grutip'],
                                                        "inventario"    => $row['Gruinv'],
                                                        "nombre_mx"     => utf8_encode(limpiarString($row['Grudes'])));
    }
    return $arr_conceptos;
}

function obtener_array_concepto_empresa($conex, $wemp_pmla, $wbasedato)
{
    $arr_conceptos = array();

    $sql = "SELECT  e04.Grucod AS concepto_unx, e04.Grucod AS concepto_mx, e04.Grutip, e04.Grudes, e04.Gruinv
            FROM    {$wbasedato}_000004 AS e04";
    $resPro = mysql_query($sql,$conex) or die("Procedimientos por empresa - Error: ".mysql_errno()." ".$qPro." - ".mysql_error());
    while ($row = mysql_fetch_array($resPro))
    {
        $arr_conceptos[$row['concepto_unx']] = array(   "concepto_unx"  => $row['concepto_unx'],
                                                        "concepto_mx"   => $row['concepto_mx'],
                                                        "tipo"          => $row['Grutip'],
                                                        "inventario"    => $row['Gruinv'],
                                                        "nombre_mx"     => utf8_encode(limpiarString($row['Grudes'])));
    }
    return $arr_conceptos;
}

function queryEncabezadoFacturas($conex, $wbasedato_cliame, $filtrar_fecha, $fecha_inicio_rep, $fecha_final_rep, $wentidad_respuesta, $limite_enc_facturas_plano_furips, $estado_encabezado, $westado_cartera, $wFarmacia = 'off', $limitar=false, $num_factura = '', $filtrar_encabezados = '')
{
    $filtros = '';
    if($num_factura != '')
    {
        $filtros .= "AND c273.Glonfa = '{$num_factura}'";
    }

    if($filtrar_encabezados != '')
    {
        // ID de encabezado de facturas 000273 seperados por comas, para filtrar las facturas seleccionadas que se desean generar en archivo plano.
        $expl_ids = explode(",", $filtrar_encabezados);
        $ids_273 = implode("','", $expl_ids);
        $filtros .= "AND c273.id IN ('{$ids_273}')";
    }

    if($filtrar_fecha == 'on')
    {
        $filtros .= "AND c273.Glofhr BETWEEN '{$fecha_inicio_rep} 00:00:00' AND '{$fecha_final_rep} 23:59:59'";
    }

    if($westado_cartera != '')
    {
        $filtros .= "AND c273.Gloecf = '{$westado_cartera}'";
    }

    $LIMIT = "";
    if($limitar)
    {
        // $LIMIT = "LIMIT   0 , {$limite_enc_facturas_plano_furips}";
    }

    $order_by = "(c273.Glonfa*1)";
    if($wFarmacia == 'on'){
        // Para las facturas de farmacia ordenar por letra con que inicia la factura y también numéricamente de menor a mayor, p.e.: a-181161,S-4857,s-10319,s-10373
        $order_by = "SUBSTRING_INDEX(c273.Glonfa,'-',1), (SUBSTRING_INDEX(c273.Glonfa,'-',-1)*1)";
    }

    // Gloesg = Estado Glosa (GL=glosada, GR=glosa revisada, AN=glosa anulada, AP=En archivo plano)
    // Se hace un inner con el detalle de glosa para saber si por lo menos hay un cargo con respuesta a glosa > 0 para que justifique tener en cuenta
    // la factura para incluirla en el archivo plano, pues los detalles de cargos con respuesta cero '0' no se deben incluir en el archivo plano.
    $sql_Enc_fn = " SELECT  c273.Glofuo as fuente_historia, c273.Glohis as historia, c273.Gloing AS ingreso, c273.id AS consec_reclamacion, c273.Glonfa AS num_factura,
                            c273.Glorad AS num_radicado, c273.Glotot AS glosa_total, c273.Glocar AS consec_archivo, c273.Gloecf AS estado_cartera,
                            c273.Glofar AS farmacia
                    FROM    {$wbasedato_cliame}_000273 AS c273
                            INNER JOIN
                            {$wbasedato_cliame}_000274 AS c274 ON ( c274.Gdeidg = c273.id
                                                                    AND c274.Gdecco = '*'
                                                                    AND (CAST(c274.Gdevgl as SIGNED) - CAST(c274.Gdevac as SIGNED)) > 0
                                                                    AND c274.Gdeest = 'on')
                    WHERE   c273.Gloent = '{$wentidad_respuesta}'
                            AND c273.Gloesg = '{$estado_encabezado}'
                            {$filtros}
                            AND c273.Gloest = 'on'
                    GROUP BY c273.id
                    ORDER BY {$order_by}
                    {$LIMIT}";
                            // AND c273.Glohis = '55193'
    return $sql_Enc_fn;
}

function cambiarEstadoFacturasFiltradas($conex, $wbasedato_cliame, $user_session, &$data, $filtrar_fecha, $fecha_inicio_rep, $fecha_final_rep, $wentidad_respuesta, $limite_enc_facturas_plano_furips, $estado_encabezado, $arr_encabezados_AP, $limitar=false)
{
    $fecha_hora_actual = date("Y-m-d H:i:s");
    $filtros = '';
    if(count($arr_encabezados_AP) > 0)
    {
        $id_encabezados_AP = implode("','", array_keys($arr_encabezados_AP));
        $filtros .= " AND id IN ('".$id_encabezados_AP."')";
    }

    if($filtrar_fecha == 'on')
    {
        $filtros .= " AND Glofhr BETWEEN '{$fecha_inicio_rep} 00:00:00' AND '{$fecha_final_rep} 23:59:59'";
    }

    // $LIMIT = "";
    // if($limitar)
    // {
    //     $LIMIT = "LIMIT   0 , {$limite_enc_facturas_plano_furips}";
    // }

    // Gloesg = Estado Glosa (GL=glosada, GR=glosa revisada, AN=glosa anulada, AP=En archivo plano)
    $sql_Enc_updt = "   UPDATE  {$wbasedato_cliame}_000273
                        SET     Gloesg = '{$estado_encabezado}',
                                Glourg = '{$user_session}',
                                Glofur = '{$fecha_hora_actual}'
                        WHERE   Gloent = '{$wentidad_respuesta}'
                                {$filtros}
                                AND Gloest = 'on'";
                                // AND Gloesg = 'AP'
    if($result_Enc_updt = mysql_query($sql_Enc_updt,$conex))
    { }
    else
    {
        $data['mensaje'] = "No se pudo cambiar de estado los encabezados de facturas para iniciar proceso de generación de archivo.";
        $data['error']   = 1;
        $data['sql']     .= $sql_Enc_updt." > ".mysql_error().PHP_EOL;
    }
}

function eliminarArchivosDiasAnteriores($dir, $dias_cache_archivos_furips)
{
    if(is_dir($dir)){ }
    else { mkdir($dir,0777); }

    $arr_archivos_r = array();
    // Abrir un directorio conocido, y proceder a leer sus contenidos
    /*
        Esta sección se encarga de leer todos los archivos generados para resultados y verifíca si la fecha de creación es menor
        a la fecha actual, si es así entonces elimina esos archivos, esto con el fin de liberar el disco duro de estos archivos no necesarios.
    */
    if (is_dir($dir)) {
        if ($gd = opendir($dir)) {
            while ($archivo = readdir($gd)) {
                //echo "nombre de archivo: $archivo : tipo de archivo: " . filetype($dir . $archivo) . "\n";
                if($archivo != '.' && $archivo != '..'){
                    $arr_archivos_r[] = $dir."/".$archivo;
                }
            }
            closedir($gd);
        }

        foreach ($arr_archivos_r as $key => $archivo) {
            $fecha_creado = date("Ymd", filectime($archivo));
            $fecha_modifi = date("Ymd", filemtime($archivo));

            $fecha_actual = date('Ymd');
            $nuevafecha   = strtotime ('-'.$dias_cache_archivos_furips.' day',strtotime ($fecha_actual));
            $fecha_limite = date ('Ymd',$nuevafecha);

            if($fecha_creado < $fecha_limite || $fecha_modifi < $fecha_limite) { unlink($archivo); }

            // Validar si el archivo  plano es temporal "tmp", estoa archivos si se deben eliminar más frecuentemente para reducir el consumo de espacio.
                                                            //  ../../planos/ips_furips_glosas/FURIPS205001021260122022017-1-20170222095931-tmp.txt
            $exlp_ext1 = explode(".", $archivo);            //  Separa por []][/][/planos/ips_furips_glosas/FURIPS205001021260122022017-1-20170222095931-tmp][txt]
            $exlp_ext2  = $exlp_ext1[count($exlp_ext1)-2];  //  [/planos/ips_furips_glosas/FURIPS205001021260122022017-1-20170222095931-tmp]
            $exlp_ext3  = explode("/",$exlp_ext2);          //  []][planos][ips_furips_glosas][FURIPS205001021260122022017-1-20170222095931-tmp]
            $exlp_ext4   = $exlp_ext3[count($exlp_ext3)-1]; //  [FURIPS205001021260122022017-1-20170222095931-tmp]
            $exlp_ext    = explode("-", $exlp_ext4);        //  [FURIPS205001021260122022017][1][20170222095931][tmp]

            $sufij_tmp = $exlp_ext[count($exlp_ext)-1];     // [tmp]
            $nombreFurips = $exlp_ext[0];                   // [FURIPS205001021260122022017]
            $dir_nombreFurips = $dir.'/'.$archivo;
            // echo $archivo.PHP_EOL;

            if($sufij_tmp == 'tmp')
            {
                // Si la fecha del archivo temporal es menor a la fecha actual entonces se borra para liberar espacio.
                if($fecha_creado < $fecha_actual || $fecha_modifi < $fecha_actual)
                {
                    // echo $archivo.' > '.$fecha_creado.' > '.$fecha_modifi.PHP_EOL;
                    unlink($archivo);
                }
            }
        }
    }
}

function consultarPlanosConsecutivo($dir, $dias_cache_archivos_furips)
{
    if(is_dir($dir)){ }
    else { mkdir($dir,0777); }

    $arr_archivos_r = array();
    // Abrir un directorio conocido, y proceder a leer sus contenidos
    /*
        Esta sección se encarga de leer todos los archivos generados para resultados y verifíca si la fecha de creación es menor
        a la fecha actual, si es así entonces elimina esos archivos, esto con el fin de liberar el disco duro de estos archivos no necesarios.
    */
    if (is_dir($dir)) {
        if ($gd = opendir($dir)) {
            while ($archivo = readdir($gd)) {
                //echo "nombre de archivo: $archivo : tipo de archivo: " . filetype($dir . $archivo) . "\n";
                if($archivo != '.' && $archivo != '..'){

                    $exlp_ext         = explode(".", $archivo);
                    $expl_nom         = explode("-", $exlp_ext[0]);
                    $sufij_tmp        = $expl_nom[count($expl_nom)-1];
                    $nombreFurips     = $exlp_ext[0];
                    $dir_nombreFurips = $dir.'/'.$archivo;
                    $fecha_creado     = date("Y-m-d", filectime($dir_nombreFurips));
                    $fecha_modifi     = date("Y-m-d", filemtime($dir_nombreFurips));
                    // echo $archivo.PHP_EOL;

                    if($sufij_tmp == 'tmp')
                    {
                        //
                    }
                    else
                    {
                        if(!array_key_exists($sufij_tmp, $arr_archivos_r))
                        {
                            $arr_archivos_r[$sufij_tmp] = array();
                        }

                        if(strpos($archivo, "FURIPS1") !== false)
                        {
                            $arr_archivos_r[$sufij_tmp]["furips1"] = array( "nombre"        => $nombreFurips,
                                                                            "url"           => $dir_nombreFurips,
                                                                            "fecha_creado"  => $fecha_creado);
                        }
                        else
                        {
                            $arr_archivos_r[$sufij_tmp]["furips2"] = array( "nombre"        => $nombreFurips,
                                                                            "url"           => $dir_nombreFurips,
                                                                            "fecha_creado"  => $fecha_creado);
                        }

                        // $arr_archivos_r[$sufij_tmp][] = $dir."/".$archivo;
                    }
                }
            }
            closedir($gd);
        }

        ksort($arr_archivos_r); // Ordenar por consecutivo
        // print_r($arr_archivos_r);
    }
    return $arr_archivos_r;
}

function actualizarConsecutivoArchivoPlanoFuripsGlosas($conex, $wemp_pmla)
{
    $sql_Enc_updt = "   UPDATE  root_000051
                        SET     Detval = ((Detval*1) + 1)
                        WHERE   Detemp = '{$wemp_pmla}'
                                AND Detapl = 'consecutivo_planos_furips_glosa'";
    if($result_Enc_updt = mysql_query($sql_Enc_updt,$conex))
    { }
    else
    {
        // $data['mensaje'] = "No se pudo cambiar de estado los encabezados de facturas para iniciar proceso de generación de archivo.";
        // $data['error']   = 1;
        // $data['sql']     .= $sql_Enc_updt." > ".mysql_error().PHP_EOL;
    }
}

function descripcionCamposArchivo($tipo_archivo='')
{
    // if($tipo_archivo == 'enc')
    {
        $arr_descArch = array("enc"=>array(),"dlle"=>array());
        $arr_descArch["enc"][1]   = "Número radicado";
        $arr_descArch["enc"][2]   = "Respuesta a glosa";
        $arr_descArch["enc"][3]   = "[Req] Número factura";
        $arr_descArch["enc"][4]   = "[Req] Consecutivo reclamación";
        $arr_descArch["enc"][5]   = "[Req] Código de habilitación";
        $arr_descArch["enc"][6]   = "[Req] Apellido 1 paciente";
        $arr_descArch["enc"][7]   = "Apellido 2 paciente";
        $arr_descArch["enc"][8]   = "[Req] Nombre 1 paciente";
        $arr_descArch["enc"][9]   = "Nombre 2 paciente";
        $arr_descArch["enc"][10]  = "[Req] Tipo documento paciente";
        $arr_descArch["enc"][11]  = "[Req] Número documento paciente";
        $arr_descArch["enc"][12]  = "Fecha de nacimiento paciente";
        $arr_descArch["enc"][13]  = "Sexo";
        $arr_descArch["enc"][14]  = "Dirección residencia victima";
        $arr_descArch["enc"][15]  = "Código departamento residencia paciente";
        $arr_descArch["enc"][16]  = "Código mpio. residencia paciente";
        $arr_descArch["enc"][17]  = "Teléfono paciente";
        $arr_descArch["enc"][18]  = "Condición de la víctima";
        $arr_descArch["enc"][19]  = "[Req] naturaleza del evento";
        $arr_descArch["enc"][20]  = "Descripción del otro evento";
        $arr_descArch["enc"][21]  = "[Req] Dirección ocurrencia evento";
        $arr_descArch["enc"][22]  = "[Req] Fecha ocurrencia evento";
        $arr_descArch["enc"][23]  = "[Req] Hora ocurrencia evento";
        $arr_descArch["enc"][24]  = "[Req] Codigo depto. evento";
        $arr_descArch["enc"][25]  = "[Req] Codigo mpio. evento";
        $arr_descArch["enc"][26]  = "Zona ocurrencia";
        $arr_descArch["enc"][27]  = "[Req] Estado aseguramiento";
        $arr_descArch["enc"][28]  = "Marca";
        $arr_descArch["enc"][29]  = "Placa";
        $arr_descArch["enc"][30]  = "Tipo vehículo";
        $arr_descArch["enc"][31]  = "Código aseguradora";
        $arr_descArch["enc"][32]  = "Número de póliza SOAT";
        $arr_descArch["enc"][33]  = "Fecha inicio vigencia póliza";
        $arr_descArch["enc"][34]  = "Fecha final vigencia póliza";
        $arr_descArch["enc"][35]  = "Intervención de la autoridad";
        $arr_descArch["enc"][36]  = "Cobro por excedente de póliza";
        $arr_descArch["enc"][37]  = "";
        $arr_descArch["enc"][38]  = "";
        $arr_descArch["enc"][39]  = "";
        $arr_descArch["enc"][40]  = "";
        $arr_descArch["enc"][41]  = "";
        $arr_descArch["enc"][42]  = "";
        $arr_descArch["enc"][43]  = "Tipo documento propietario";
        $arr_descArch["enc"][44]  = "Número documento propietario";
        $arr_descArch["enc"][45]  = "1er. Apellido propietario";
        $arr_descArch["enc"][46]  = "2do. Apellido propietario";
        $arr_descArch["enc"][47]  = "1er. Nombre propietario";
        $arr_descArch["enc"][48]  = "2do. Nombre propietario";
        $arr_descArch["enc"][49]  = "Dir. propietario";
        $arr_descArch["enc"][50]  = "Teléfono residencia propietario";
        $arr_descArch["enc"][51]  = "Código depto. propietario";
        $arr_descArch["enc"][52]  = "Código mpio. propietario";
        $arr_descArch["enc"][53]  = "1er. Apellido conductor";
        $arr_descArch["enc"][54]  = "2do. Apellido conductor";
        $arr_descArch["enc"][55]  = "1er. Nombre conductor";
        $arr_descArch["enc"][56]  = "2do. Nombre conductor";
        $arr_descArch["enc"][57]  = "Tipo documento conductor";
        $arr_descArch["enc"][58]  = "Número documento conductor";
        $arr_descArch["enc"][59]  = "Dirección conductor";
        $arr_descArch["enc"][60]  = "Código depto. conductor";
        $arr_descArch["enc"][61]  = "Código mpio. conductor";
        $arr_descArch["enc"][62]  = "Teléfono conductor";
        $arr_descArch["enc"][63]  = "";
        $arr_descArch["enc"][64]  = "";
        $arr_descArch["enc"][65]  = "";
        $arr_descArch["enc"][66]  = "";
        $arr_descArch["enc"][67]  = "";
        $arr_descArch["enc"][68]  = "";
        $arr_descArch["enc"][69]  = "";
        $arr_descArch["enc"][70]  = "";
        $arr_descArch["enc"][71]  = "";
        $arr_descArch["enc"][72]  = "";
        $arr_descArch["enc"][73]  = "";
        $arr_descArch["enc"][74]  = "";
        $arr_descArch["enc"][75]  = "";
        $arr_descArch["enc"][76]  = "";
        $arr_descArch["enc"][77]  = "";
        $arr_descArch["enc"][78]  = "";
        $arr_descArch["enc"][79]  = "[Req] Fecha ingreso";
        $arr_descArch["enc"][80]  = "Hora ingreso";
        $arr_descArch["enc"][81]  = "[Req] Fecha egreso";
        $arr_descArch["enc"][82]  = "Hora egreso";
        $arr_descArch["enc"][83]  = "[Req] Código diagnóstico principal ingreso";
        $arr_descArch["enc"][84]  = "Código diagnóstico 1 ingreso";
        $arr_descArch["enc"][85]  = "Código diagnóstico 2 ingreso";
        $arr_descArch["enc"][86]  = "[Req] Código diagnóstico principal egreso";
        $arr_descArch["enc"][87]  = "Código diagnóstico 1 egreso";
        $arr_descArch["enc"][88]  = "Código diagnóstico 2 egreso";
        $arr_descArch["enc"][89]  = "[Req] Apellido 1 médico";
        $arr_descArch["enc"][90]  = "Apellido 2 médico";
        $arr_descArch["enc"][91]  = "[Req] Nombre 1 médico";
        $arr_descArch["enc"][92]  = "Nombre 2 médico";
        $arr_descArch["enc"][93]  = "[Req] Tipo documento identidad médico";
        $arr_descArch["enc"][94]  = "[Req] Número documento médico";
        $arr_descArch["enc"][95]  = "[Req] Número regístro médico";
        $arr_descArch["enc"][96]  = "[Req] Total facturado por amparo de gastos médicos quirúrgicos, debe ser mayor a cero";
        $arr_descArch["enc"][97]  = "[Req] Total reclamado por amparo de gastos médicos quirúrgicos, debe ser mayor a cero";
        $arr_descArch["enc"][98]  = "[Req] Total facturado por amparo de gastos de transporte y movilización de la víctima, > 0";
        $arr_descArch["enc"][99]  = "[Req] Total reclamado por amparo de gastos de transporte y movilización de la víctima, > 0";
        $arr_descArch["enc"][100] = "[Req] Total folios";
    }
    // else
    {
        $arr_descArch["dlle"][1] = "[Req] Número de factura";
        $arr_descArch["dlle"][2] = "[Req] Número de consecutivo";
        $arr_descArch["dlle"][3] = "[Req] Tipo de servicio";
        $arr_descArch["dlle"][4] = "[Req] Código de servicio o Código INVIMA";
        $arr_descArch["dlle"][5] = "Descripción del insumo";
        $arr_descArch["dlle"][6] = "[Req] Cantidad de servicios, > 0";
        $arr_descArch["dlle"][7] = "[Req] valor unitario, no separadores de miles, no decimales, > 0";
        $arr_descArch["dlle"][8] = "[Req] valor total facturado, no separadores de miles, no decimales, > 0";
        $arr_descArch["dlle"][9] = "[Req] valor total reclamado al fosyga, no separadores de miles, no decimales, > 0";
    }

    return $arr_descArch;
}

function obligatorioVal($numero_campo,$validacion_add,$arr_descArch,&$arr_alert,$tipo_archivo,$index_fact,$dato_campo,$obligatorio)
{
    if($obligatorio && (($dato_campo == 'NO_INVIMA' || $dato_campo == '') || (is_numeric($dato_campo) && ($dato_campo*1) == 0)))
    {
        agregarAlerta($index_fact,$tipo_archivo,$numero_campo,$arr_alert,$arr_descArch,'');
    }else{
        switch ($validacion_add) {
            case 'I': // validación de número INT (Enteros), no decimales.
                    $impl_dato_campo = explode(" ", $dato_campo);
                    // LimpiarString quita todos los caracteres no permitidos entre ellos puntos, comas, si este era un número decimal se le quitó el punto
                    // se valída si quedó algún espacio y se cambia por punto para evaluar si es decimal o no.
                    $dato_campo = (count($impl_dato_campo) > 1) ? implode(".", $impl_dato_campo): $dato_campo;
                    if(is_numeric($dato_campo) && is_float($dato_campo + 0)){
                        $msj_add = " <br>[NÚMERO DECIMAL NO PERMITIDO]";
                        agregarAlerta($index_fact,$tipo_archivo,$numero_campo,$arr_alert,$arr_descArch,$msj_add);
                    }
                break;

            default:
                # code...
                break;
        }
    }
    return $dato_campo;
}

function agregarAlerta($index_fact,$tipo_archivo,$numero_campo,&$arr_alert,$arr_descArch,$msj_add)
{

    if(!array_key_exists($index_fact, $arr_alert))
    {
        $arr_alert[$index_fact] = array();
    }

    if(!array_key_exists($tipo_archivo, $arr_alert[$index_fact]))
    {
        $arr_alert[$index_fact][$tipo_archivo] = array();
    }

    if(!array_key_exists($numero_campo, $arr_alert[$index_fact][$tipo_archivo]))
    {
        $arr_alert[$index_fact][$tipo_archivo][$numero_campo]=array();
    }
    $arr_alert[$index_fact][$tipo_archivo][$numero_campo] = (($tipo_archivo == 'enc') ? "Encabezado":"Detalle")." > Campo [$numero_campo]-".$arr_descArch[$tipo_archivo][$numero_campo].$msj_add.".";
}

function estadosCarteraFactura($conex, $wbasedato)
{
    $arrayEstadosFacturaAutocomp = array();
    $sqlEstFac = "  SELECT  Esccod, Escnom, Esccau, Escrra AS req_radicado
                    FROM    {$wbasedato}_000279
                    WHERE   Escest = 'on'";
    $resEstFac = mysql_query($sqlEstFac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstFac):</b><br>".mysql_error()." > ".$sqlEstFac);
    while($rowEst = mysql_fetch_array($resEstFac))
    {
        $arrayEstadosFacturaAutocomp[$rowEst['Esccod']] = utf8_encode(trim($rowEst['Escnom']));
    }
    return $arrayEstadosFacturaAutocomp;
}

/**
 * [consultar_tabla_paciente: esta función se encarga de buscar en que tabla de unix está la información básica del paciente, ya sea en inpaco o inpaci,
 *                             para que en las consultas posteriores del proceso tenga en cuenta la tabla correcta de información del paciente.]
 * @param  [type] $conex        [description]
 * @param  [type] $conexUnix    [description]
 * @param  [type] $wemp_pmla    [description]
 * @param  [type] $historia     [description]
 * @param  [type] $ingreso      [description]
 * @param  [type] &$id_paciente [description]
 * @return [type]               [description]
 */
function consultar_tabla_paciente($conex, $conexUnix, $wemp_pmla, $historia, $ingreso, &$id_paciente)
{
    $tabla_paciente_unx = "";
    $query="SELECT  pacced
            FROM    inpaci
            WHERE   pachis = '{$historia}'
                    AND pacnum = '{$ingreso}'";
    $err_o = odbc_do($conexUnix,$query);

    if (odbc_fetch_row($err_o))
    {
        $tabla_paciente_unx = "inpaci";
        $id_paciente["documento"] = odbc_result($err_o,'pacced');
    }
    else
    {
        $query="SELECT  pacced
                FROM    inpac
                WHERE   pachis = '{$historia}'
                        AND pacnum = '{$ingreso}'";
        $err_1 = odbc_do($conexUnix,$query);
        if (odbc_fetch_row($err_1))
        {
            $tabla_paciente_unx = "inpac";
            $id_paciente["documento"] = odbc_result($err_1,'pacced');
        }
        else
        {
            $query="SELECT  pacced, pacing
                    FROM    inpaci
                    WHERE   pachis = '{$historia}'
                    ORDER BY pacing DESC";
            $err_o = odbc_do($conexUnix,$query);

            if (odbc_fetch_row($err_o))
            {
                $tabla_paciente_unx = "inpaci";
                $id_paciente["documento"] = odbc_result($err_o,'pacced');
            }
            else
            {
                $query="SELECT  pacced
                        FROM    inpac
                        WHERE   pachis = '{$historia}'";
                $err_1 = odbc_do($conexUnix,$query);
                if (odbc_fetch_row($err_1))
                {
                    $tabla_paciente_unx = "inpac";
                    $id_paciente["documento"] = odbc_result($err_1,'pacced');
                }else
                {
                    $tabla_paciente_unx = "";
                }
            }
        }
    }

    // echo $tabla_paciente_unx.'>'.$query;
    return $tabla_paciente_unx;
}

function consultar_datos_ingreso_egreso_paciente($conex, $conexUnix, $wemp_pmla, $historia, $ingreso, $id_enc, $tabla_paciente_unx, $arrInfo_historiaAyudas, &$id_paciente, &$paciente)
{
    $datos_encontrados = false;
    if($arrInfo_historiaAyudas["fuente_his"] != '01' && $tabla_paciente_unx == 'aymov' && $arrInfo_historiaAyudas["farmacia"] != 'on')
    {
        // [1] Consulta datos de ingreso y egreso
        $temp_pac = "AY".$id_enc; //nombre de la tabla temporal
        $campos_select_no_null = "movtid,movced,movape,movnom,movmun,movsex,movlug,movdia,movmed,movhis,movegrfeg,movegrheg";
        $campos_tipo_fecha     = array("movnac,movfec");
        $campos_nulos          = "movap2,movtel,movnac,movfec,movhor,movdir,movegrdeg";

        //si no esta hospitalizado Busco en inpaci datos del paciente y en inmgr datos del ingreso
        $selecPac = "   SELECT  {$campos_select_no_null}, {$campos_nulos}
                        FROM    {$tabla_paciente_unx}, aymovegr
                        WHERE   movfuo ='{$arrInfo_historiaAyudas['fuente_his']}'
                                AND movdoo = '{$arrInfo_historiaAyudas['consec_cargo']}'
                                AND movhis = '{$historia}'
                                AND movegrfue = movfuo
                                AND movegrdoc = movdoo
                        into temp {$temp_pac}";
        // echo "<pre>".print_r($select,true)."</pre>";
        if($result_1PAC = odbc_exec($conexUnix,$selecPac ))
        {
            // [2] Se arreglan todos los campos nulos, asignando vacíos y a los campos tipo fecha '1900/01/01'
            arreglarNulosTemp($conex, $conexUnix, $wemp_pmla, $temp_pac, $campos_nulos, $campos_tipo_fecha);
            // [3] Temporal con toda la información y con todos los campos nulos arreglados
            $select = " SELECT  {$campos_select_no_null}, {$campos_nulos}
                        FROM    {$temp_pac}";
            if($result_2pac = odbc_exec($conexUnix,$select))
            {
                if(odbc_fetch_row($result_2pac))
                {
                    // Nombres
                    $pacnom      = trim(odbc_result($result_2pac,'movnom'));
                    $pacnom_expl = explode(" ", $pacnom);
                    $pacnom1     = $pacnom_expl[0];
                    unset($pacnom_expl[0]);
                    $pacnom2     = (count($pacnom_expl) > 0) ? implode(" ", $pacnom_expl): '';

                    // Departamento - minicipio
                    $depmun = trim(odbc_result($result_2pac,'movmun'));
                    $depto = substr($depmun, 0, 2);
                    $munic = substr($depmun, 2);

                    $pacnac = (str_replace("-", "", trim(odbc_result($result_2pac,'movnac'))) == '19000101') ? '' : trim(odbc_result($result_2pac,'movnac'));
                    $egring = (str_replace("-", "", trim(odbc_result($result_2pac,'movfec'))) == '19000101') ? '' : trim(odbc_result($result_2pac,'movfec'));
                    $egregr = (str_replace("-", "", trim(odbc_result($result_2pac,'movegrfeg'))) == '19000101') ? '' : trim(odbc_result($result_2pac,'movegrfeg'));

                    $diag_ingreso = trim(odbc_result($result_2pac,'movdia')); // Código diagnóstico principal ingreso
                    $diag_egreso  = trim(odbc_result($result_2pac,'movegrdeg'));

                    if($diag_egreso == '')
                    {
                        $diag_egreso = $diag_ingreso;
                    }

                    $medico_tratante = "";
                    $egrmed          = trim(odbc_result($result_2pac,'movmed'));
                    if($medico_tratante=='' && $egrmed != '')
                    {
                        $medico_tratante = $egrmed;
                    }

                    $paciente["Pacap1"] = trim(odbc_result($result_2pac,'movape'));
                    $paciente["Pacap2"] = trim(odbc_result($result_2pac,'movap2'));
                    $paciente["Pacno1"] = $pacnom1;
                    $paciente["Pacno2"] = $pacnom2;
                    $paciente["Pactdo"] = trim(odbc_result($result_2pac,'movtid'));
                    $paciente["Pacdoc"] = trim(odbc_result($result_2pac,'movced'));
                    $paciente["Pacfna"] = $pacnac;
                    $paciente["Pacsex"] = trim(odbc_result($result_2pac,'movsex'));
                    $paciente["Pacdir"] = trim(odbc_result($result_2pac,'movdir'));
                    $paciente["Pacdep"] = $depto;
                    $paciente["Paciu"]  = $depto.$munic;
                    $paciente["Pactel"] = trim(odbc_result($result_2pac,'movtel'));
                    $paciente["Ingfei"] = $egring;
                    $paciente["Inghin"] = trim(odbc_result($result_2pac,'movhor'));
                    $paciente["Ingdig"] = $diag_ingreso; // Código diagnóstico principal ingreso
                    $paciente["Ubifad"] = $egregr; // Fecha de egreso
                    $paciente["Ubihad"] = trim(odbc_result($result_2pac,'movegrheg'));
                    $paciente["Diamed"] = $medico_tratante; // Médico diagnóstico de egreso.
                    $paciente["diag_egre_ayuda"] = $diag_egreso; // Diagnóstico egreso ayuda diagnóstica
                    $datos_encontrados  = true;
                }
            }
        }
    }
    elseif($tabla_paciente_unx == 'inpaci')
    {
        // [1] Consulta datos de ingreso y egreso
        $temp_ingre_egre = "IE".$id_enc; //nombre de la tabla temporal

        $campos        = "egring, egrhoi, egregr, egrhoe, egrhis, egrnum, egrmed";
        $campos_nulos  = "egrdia, egrdin";
        $defectoCampos = "'',''";
        $tablas        = "inmegr";
        $where         = "  egrhis='{$historia}'
                            AND egrnum='{$ingreso}'";
        $query_in_egr   = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );
        $query_inegre   = "( $query_in_egr )
                            into temp {$temp_ingre_egre}";
        $err_IE = odbc_exec($conexUnix,$query_inegre);

        // [2] Temporal con datos de egreso y datos del paciente
        $temp_pac = "PA".$id_enc; //nombre de la tabla temporal
        $campos_select_no_null = "pacced,pactid,pacap1,pacnom,pacmun";
        $campos_tipo_fecha     = array("pacnac","egring","egregr");
        $campos_nulos          = "pacap2,pacsex,paclug,pacnac,pacdir,pactel,egring,egrhoi,egregr,egrhoe,egrdia,egrhis,egrnum,egrdin,egrmed,pacinfmed";

        //si no esta hospitalizado Busco en inpaci datos del paciente y en inmgr datos del ingreso
        $selecPac = "   SELECT  {$campos_select_no_null}, {$campos_nulos}
                        FROM    {$tabla_paciente_unx}, OUTER ({$temp_ingre_egre}, OUTER inpacinf)
                        WHERE   pachis ='{$historia}'
                                AND pachis = egrhis
                                AND egrnum = '{$ingreso}'
                                AND egrhis = pacinfhis
                                AND egrnum = pacinfnum
                        into temp {$temp_pac}";
        // echo "<pre>".print_r($select,true)."</pre>";
        if($result_1PAC = odbc_exec($conexUnix,$selecPac ))
        {
            // [3] Se arreglan todos los campos nulos, asignando vacíos y a los campos tipo fecha '1900/01/01'
            arreglarNulosTemp($conex, $conexUnix, $wemp_pmla, $temp_pac, $campos_nulos, $campos_tipo_fecha);
            // [4] Temporal con toda la información y con todos los campos nulos arreglados
            $select = " SELECT  {$campos_select_no_null}, {$campos_nulos}
                        FROM    {$temp_pac}";
            if($result_3pac = odbc_exec($conexUnix,$select))
            {
                if(odbc_fetch_row($result_3pac))
                {
                    // Nombres
                    $pacnom      = trim(odbc_result($result_3pac,'pacnom'));
                    $pacnom_expl = explode(" ", $pacnom);
                    $pacnom1     = $pacnom_expl[0];
                    unset($pacnom_expl[0]);
                    $pacnom2     = (count($pacnom_expl) > 0) ? implode(" ", $pacnom_expl): '';

                    // Departamento - minicipio
                    $depmun = trim(odbc_result($result_3pac,'pacmun'));
                    $depto = substr($depmun, 0, 2);
                    $munic = substr($depmun, 2);

                    $pacnac = (str_replace("-", "", trim(odbc_result($result_3pac,'pacnac'))) == '19000101') ? '' : trim(odbc_result($result_3pac,'pacnac'));
                    $egring = (str_replace("-", "", trim(odbc_result($result_3pac,'egring'))) == '19000101') ? '' : trim(odbc_result($result_3pac,'egring'));
                    $egregr = (str_replace("-", "", trim(odbc_result($result_3pac,'egregr'))) == '19000101') ? '' : trim(odbc_result($result_3pac,'egregr'));

                    $medico_tratante = trim(odbc_result($result_3pac,'pacinfmed'));
                    $egrmed          = trim(odbc_result($result_3pac,'egrmed'));
                    if($medico_tratante=='' && $egrmed != '')
                    {
                        $medico_tratante = $egrmed;
                    }

                    $paciente["Pacap1"] = trim(odbc_result($result_3pac,'pacap1'));
                    $paciente["Pacap2"] = trim(odbc_result($result_3pac,'pacap2'));
                    $paciente["Pacno1"] = $pacnom1;
                    $paciente["Pacno2"] = $pacnom2;
                    $paciente["Pactdo"] = trim(odbc_result($result_3pac,'pactid'));
                    $paciente["Pacdoc"] = trim(odbc_result($result_3pac,'pacced'));
                    $paciente["Pacfna"] = $pacnac;
                    $paciente["Pacsex"] = trim(odbc_result($result_3pac,'pacsex'));
                    $paciente["Pacdir"] = trim(odbc_result($result_3pac,'pacdir'));
                    $paciente["Pacdep"] = $depto;
                    $paciente["Paciu"]  = $depto.$munic;
                    $paciente["Pactel"] = trim(odbc_result($result_3pac,'pactel'));
                    $paciente["Ingfei"] = $egring;
                    $paciente["Inghin"] = trim(odbc_result($result_3pac,'egrhoi'));
                    $paciente["Ingdig"] = trim(odbc_result($result_3pac,'egrdin')); // Código diagnóstico principal ingreso
                    $paciente["Ubifad"] = $egregr;
                    $paciente["Ubihad"] = trim(odbc_result($result_3pac,'egrhoe'));
                    $paciente["Diamed"] = $medico_tratante; // Médico diagnóstico de egreso.
                    $datos_encontrados  = true;
                }
            }
        }
    }
    else
    {
        $select= "  SELECT  pacced, pactid, pacap1, pacap2, pacnom, pacsex, paclug, pacnac, pacdir, pactel, pacmun, pachor,
                            pacdin, pacfec, pacmed, pachis, pacnum
                    FROM    inpac
                    WHERE   pachis='{$historia}'
                            AND pacnum = '{$ingreso}'";
                //." AND pacnum='".trim($ingreso)."' "; 2012/08/17 gustavo

        if($result_4pac = odbc_exec($conexUnix,$select))
        {
            if(odbc_fetch_row($result_4pac))
            {
                // Nombres
                $pacnom      = trim(odbc_result($result_4pac,'pacnom'));
                $pacnom_expl = explode(" ", $pacnom);
                $pacnom1     = $pacnom_expl[0];
                unset($pacnom_expl[0]);
                $pacnom2     = (count($pacnom_expl) > 0) ? implode(" ", $pacnom_expl): '';

                // Departamento - minicipio
                $depmun = trim(odbc_result($result_4pac,'pacmun'));
                $depto = substr($depmun, 0, 2);
                $munic = substr($depmun, 2);

                $pachor = str_replace(".",":",trim(odbc_result($result_4pac,'pachor')));
                $pachor = validarFormatoHoraCorrecto($pachor);

                $paciente["Pacap1"] = trim(odbc_result($result_4pac,'pacap1'));
                $paciente["Pacap2"] = trim(odbc_result($result_4pac,'pacap2'));
                $paciente["Pacno1"] = $pacnom1;
                $paciente["Pacno2"] = $pacnom2;
                $paciente["Pactdo"] = trim(odbc_result($result_4pac,'pactid'));
                $paciente["Pacdoc"] = trim(odbc_result($result_4pac,'pacced'));
                $paciente["Pacfna"] = trim(odbc_result($result_4pac,'pacnac'));
                $paciente["Pacsex"] = trim(odbc_result($result_4pac,'pacsex'));
                $paciente["Pacdir"] = trim(odbc_result($result_4pac,'pacdir'));
                $paciente["Pacdep"] = $depto;
                $paciente["Paciu"]  = $depto.$munic;
                $paciente["Pactel"] = trim(odbc_result($result_4pac,'pactel'));
                $paciente["Ingfei"] = trim(odbc_result($result_4pac,'pacfec'));
                $paciente["Inghin"] = $pachor;
                $paciente["Ingdig"] = trim(odbc_result($result_4pac,'pacdin'));
                $paciente["Diamed"] = trim(odbc_result($result_4pac,'pacmed')); // Médico diagnóstico de egreso.
                $datos_encontrados  = true;
            }
        }
    }
    return $datos_encontrados;
}

/**
 * [arreglarNulosTemp: esta función es una alternativa a "construirQueryUnix" pues en mucho casos la cantidad de campos nulos en el select
 *                      supera los 10 campos y "construirQueryUnix" solo acepta 10, esta nueva función empieza a usar tablas temporales
 *                      y updates sobre campos nulos temporales para arreglar dichos campos evitando que se dañe el programa]
 * @param  [type] $conex             [description]
 * @param  [type] $conexUnix         [description]
 * @param  [type] $wemp_pmla         [description]
 * @param  [type] $temp_accidente    [description]
 * @param  [type] $campos_nulos      [description]
 * @param  [type] $campos_tipo_fecha [description]
 * @return [type]                    [description]
 */
function arreglarNulosTemp($conex, $conexUnix, $wemp_pmla, $temp_accidente, $campos_nulos, $campos_tipo_fecha)
{
    $campos_nulos_expl = explode(",", $campos_nulos);
    foreach($campos_nulos_expl AS $key => $posible_campo_nulo)
    {
        $val_dafault = "";
        if(in_array(trim($posible_campo_nulo), $campos_tipo_fecha))
        {
            $val_dafault = "1900/01/01"; // Los tipo fecha no aceptan valor vacío, un campo tipo fecha con valor vacío lo sigue interpretando como NULL
        }

        $query_upt_null = " UPDATE  {$temp_accidente}
                                    SET {$posible_campo_nulo} = '{$val_dafault}'
                            WHERE   {$posible_campo_nulo} IS NULL";
        // echo $query_acc; return;
        if($result_acc = odbc_exec($conexUnix,$query_upt_null))
        {
        }
        else
        {
            // echo "error";
        }
    }
}

function validarFormatoHoraCorrecto($hora_min)
{
    $expl_hor    = explode(":", $hora_min);
    $expl_hor[0] = (($expl_hor[0]*1) < 10) ? '0'.($expl_hor[0]*1): $expl_hor[0];
    $hora_min    = $expl_hor[0].':'.$expl_hor[1];
    return $hora_min;
}

function consultar_datos_accidente_paciente($conex, $conexUnix, $wemp_pmla, $aseguradoraEstatalFosyga, $historia, $ingreso, $id_enc, $tabla_paciente_unx, $arr_tipoVehiculoUnx, $arrInfo_historiaAyudas, &$id_paciente, &$paciente)
{
    // Consulta datos de accidente
    $catastrofico = false;

    // En esta tabla, todos los ingresos de una historia entán asociados a un ingreso pricipal del accidente, por ejemplo si un paciente tuvo accidente en ingreso 1 y ahora
    // la historia tiene el ingreso ocho para una revisión, ese ingreso ocho debe estar asociado al ingreso principal del accidente 1, El ingreso principal se puede identificar
    // tambien por accind=P
    if($arrInfo_historiaAyudas["fuente_his"] != '01' && $arrInfo_historiaAyudas["farmacia"] != 'on')
    {
        $query_acc = "  SELECT  acchis, accacc
                        FROM    inacc
                        WHERE   acchis = '{$historia}'
                                AND accfuo = '{$arrInfo_historiaAyudas['fuente_his']}'
                                AND accdoo = '{$arrInfo_historiaAyudas['consec_cargo']}'"; // accind=P principal, C=ingresos de revisiones del accidente principal
        if($result_acc = odbc_exec($conexUnix,$query_acc))
        {
            if(odbc_fetch_row($result_acc))
            {
                $id_paciente["historia_accidente"]    = trim(odbc_result($result_acc,"acchis"));
                // $id_paciente["ingreso_accidente"]     = trim(odbc_result($result_acc,"accnum"));
                $id_paciente["consecutivo_accidente"] = trim(odbc_result($result_acc,"accacc"));
            }
        }
    }
    else
    {
        $query_acc = "  SELECT  acchis, accnum, accacc
                        FROM    inacc
                        WHERE   acchis = '{$historia}'
                                AND accnum = '{$ingreso}'"; // accind=P principal, C=ingresos de revisiones del accidente principal
        if($result_acc = odbc_exec($conexUnix,$query_acc))
        {
            if(odbc_fetch_row($result_acc))
            {
                $id_paciente["historia_accidente"]    = trim(odbc_result($result_acc,"acchis"));
                $id_paciente["ingreso_accidente"]     = trim(odbc_result($result_acc,"accnum"));
                $id_paciente["consecutivo_accidente"] = trim(odbc_result($result_acc,"accacc"));
            }
            else
            {
                $catastrofico = true;
                $query_cat = "  SELECT  pacevchis, pacevcnum
                                FROM    inpacevc, inevc, $tabla_paciente_unx
                                WHERE   pacced = '{$id_paciente['documento']}'
                                        AND pacevchis = pachis
                                        AND pacevcevc = evccod
                                ORDER BY pacevcnum DESC";

                if($result_cat = odbc_exec($conexUnix,$query_cat))
                {
                    if(odbc_fetch_row($result_cat))
                    {
                        $id_paciente["historia_accidente"]    = trim(odbc_result($result_cat,"pacevchis"));
                        $id_paciente["ingreso_accidente"]     = trim(odbc_result($result_cat,"pacevcnum"));
                        $id_paciente["consecutivo_accidente"] = "";
                    }
                }
            }
        }
    }

    $temp_accidente = "tm".$id_enc; //nombre de la tabla temporal
    if(!$catastrofico)
    {
        $campos_select_no_null = "  accdethis, accdetnum, accdethor, accdetmun, accdetlug, accdetasn, accdetnom,
                                    accdetocu, accdetzon, accdetase, accdetaut, accdetacc,
                                    accdetfec, accdetres";
        $campos_tipo_fecha     = array("accdetffi", "accdetfin");
        $campos_nulos          = "accdettel,accdetdir,accdetffi,accdetfin,accdetpol,accdetcas,accdettip,accdetpla,accdetmar,accdetmuc,accdetced";

        $query_acc = "  SELECT  {$campos_select_no_null}, {$campos_nulos}
                        FROM    inaccdet
                        WHERE   accdethis = '{$id_paciente['historia_accidente']}'
                                AND accdetacc = '{$id_paciente['consecutivo_accidente']}'
                        into temp {$temp_accidente}";
                                // AND accdetnum = '{$id_paciente['ingreso_accidente']}'

        if($result_acc = odbc_exec($conexUnix,$query_acc))
        {
            arreglarNulosTemp($conex, $conexUnix, $wemp_pmla, $temp_accidente, $campos_nulos, $campos_tipo_fecha);

            $query_acc_tmp = "  SELECT  {$campos_select_no_null}, {$campos_nulos}
                                FROM    {$temp_accidente}";

            if($result_acc_tmp = odbc_exec($conexUnix,$query_acc_tmp))
            {
                if(odbc_fetch_row($result_acc_tmp))
                {
                    // Departamento - minicipio
                    $depmunCond    = trim(odbc_result($result_acc_tmp,'accdetmuc')); // En la gran mayoría este campo es null
                    $depmun        = trim(odbc_result($result_acc_tmp,'accdetmun'));
                    $depto         = substr($depmun, 0, 2);
                    $munic         = substr($depmun, 2);

                    $deptoConduct  = substr($depmun, 0, 2);
                    $municConduct  = substr($depmun, 2);

                    if($depmunCond != '')
                    {
                        $deptoConduct = substr($depmunCond, 0, 2);
                        $municConduct = substr($depmunCond, 2);
                    }

                    $accdetfin     = trim(odbc_result($result_acc_tmp,'accdetfin'));
                    $accdetffi     = trim(odbc_result($result_acc_tmp,'accdetffi'));
                    $tipo_vehiculo = trim(trim(odbc_result($result_acc_tmp,'accdettip')));
                    $accdethor     = str_replace(".",":",trim(odbc_result($result_acc_tmp,'accdethor')));
                    $accdethor     = validarFormatoHoraCorrecto($accdethor);
                    //$cod_TVehiculo = "";

                    // echo $tipo_vehiculo = array_search($tipo_vehiculo, $arr_tipoVehiculoUnx);
                    if(false === $tipo_vehiculo = array_search($tipo_vehiculo, $arr_tipoVehiculoUnx))
                    {
                        $tipo_vehiculo = "";
                    }

                    $paciente["Accdep"]           = $depto;
                    $paciente["Accmun"]           = $depto.$munic;
                    $paciente["codicion_victima"] = trim(odbc_result($result_acc_tmp,'accdetocu'));
                    $paciente["dir_evento"]       = trim(odbc_result($result_acc_tmp,'accdetlug'));
                    $paciente["fecha_evento"]     = trim(odbc_result($result_acc_tmp,'accdetfec'));
                    $paciente["hora_evento"]      = $accdethor;
                    $paciente["Acczon"]           = trim(odbc_result($result_acc_tmp,'accdetzon'));
                    $paciente["Accase"]           = trim(odbc_result($result_acc_tmp,'accdetase'));
                    $paciente["Accmar"]           = trim(odbc_result($result_acc_tmp,'accdetmar'));
                    $paciente["Accpla"]           = trim(odbc_result($result_acc_tmp,'accdetpla'));
                    $paciente["Acctse"]           = $tipo_vehiculo;
                    $paciente["Acccas"]           = trim(odbc_result($result_acc_tmp,'accdetcas'));
                    $paciente["Accpol"]           = trim(odbc_result($result_acc_tmp,'accdetpol'));

                    $paciente["Accvfi"]           = (str_replace("-", "", $accdetfin) == '19000101') ? '' : $accdetfin;
                    $paciente["Accvff"]           = (str_replace("-", "", $accdetffi) == '19000101') ? '' : $accdetffi;
                    $paciente["Accaut"]           = trim(odbc_result($result_acc_tmp,'accdetaut'));
                    $paciente["Acccep"]           = "";
                    $paciente["Acccni"]           = trim(odbc_result($result_acc_tmp,'accdetced')); // Número documento conductor
                    $paciente["Acccdi"]           = trim(odbc_result($result_acc_tmp,'accdetdir')); // Dirección conductor
                    $paciente["Acccdp"]           = $deptoConduct;                                  // Código depto. conductor
                    $paciente["Acccmn"]           = $deptoConduct.$municConduct;                    // Código mpio. conductor
                    $paciente["Accctl"]           = trim(odbc_result($result_acc_tmp,'accdettel')); // Teléfono conductor

                    // Si tipo de aseguramiento unix es vehículo fantasma (3:equivalente en matrix), no debe mostrar datos de propietario ni conductor.
                    if($paciente["Accase"] == 'F')
                    {
                        $paciente["Accmar"] = '';
                        $paciente["Accpla"] = '';
                        $paciente["Acctse"] = '';
                        $paciente["Acccas"] = '';
                        $paciente["Accpol"] = '';
                        $paciente["Accvfi"] = '';
                        $paciente["Accvff"] = '';
                        $paciente["Acccep"] = '';
                        $paciente["Acccni"] = '';
                        $paciente["Acccdi"] = '';
                        $paciente["Accctl"] = '';
                        $paciente["Acccdp"] = '';
                        $paciente["Acccmn"] = '';
                    }
                    else
                    {
                        $query_Res = "  SELECT  count(*) AS cant_responsables
                                        FROM    fasalacc
                                        WHERE   salacchis = '{$id_paciente['historia_accidente']}'
                                                AND salaccacc = '{$id_paciente['consecutivo_accidente']}'";
                        if($result_Res = odbc_exec($conexUnix,$query_Res))
                        {
                            $cant_responsables = odbc_result($result_Res,'cant_responsables');
                            $paciente["Acccep"] = ($cant_responsables >= 3) ? 'S': '';
                        }
                    }
                }
            }
        }

        // Si tipo de aseguramiento unix es vehículo fantasma (3:equivalente en matrix), no debe mostrar datos de propietario ni conductor.
        if($paciente["Accase"] != 'F')
        {
            $temp_propietario = "t".$id_enc; //nombre de la tabla temporal
            $campos_select_no_null = "accprohis,accproacc";
            $campos_tipo_fecha     = array();
            $campos_nulos          = "  accprono1,accprono2,accproap1,accproap2,accprotid,accproide,accprodir,accprodep,accpromun,accprotel,
                                        accprotic,accpronc1,accpronc2,accproac1,accproac2,accproid2";
            //Busco datos del propietario y del conductor
            $query_proTm =  "SELECT  {$campos_select_no_null}, {$campos_nulos}
                            FROM    inaccpro
                            WHERE   accprohis = '{$historia}'
                                    AND accproacc = '{$id_paciente['consecutivo_accidente']}'
                            into temp {$temp_propietario}";
            if($result_proTm = odbc_exec($conexUnix,$query_proTm))
            {
                arreglarNulosTemp($conex, $conexUnix, $wemp_pmla, $temp_propietario, $campos_nulos, $campos_tipo_fecha);

                $query_prop = " SELECT  {$campos_select_no_null}, {$campos_nulos}
                                FROM    {$temp_propietario}";
                if($result_acc = odbc_exec($conexUnix,$query_prop))
                {
                    if(odbc_fetch_row($result_acc))
                    {
                        $depto = trim(odbc_result($result_acc,'accprodep'));

                        $paciente["Acctid"] = trim(odbc_result($result_acc,'accprotid'));
                        $paciente["Accnid"] = trim(odbc_result($result_acc,'accproide'));
                        $paciente["Accap1"] = trim(odbc_result($result_acc,'accproap1'));
                        $paciente["Accap2"] = trim(odbc_result($result_acc,'accproap2'));
                        $paciente["Accno1"] = trim(odbc_result($result_acc,'accprono1'));
                        $paciente["Accno2"] = trim(odbc_result($result_acc,'accprono2'));
                        $paciente["Accpdi"] = trim(odbc_result($result_acc,'accprodir'));
                        $paciente["Acctel"] = trim(odbc_result($result_acc,'accprotel'));
                        $paciente["Accpdp"] = trim(odbc_result($result_acc,'accprodep'));
                        $paciente["Accpmn"] = $depto.trim(odbc_result($result_acc,'accpromun'));
                        $paciente["Accca1"] = trim(odbc_result($result_acc,'accproac1')); // 1 Apellido conductor
                        $paciente["Accca2"] = trim(odbc_result($result_acc,'accproac2')); // 2 Apellido conductor
                        $paciente["Acccn1"] = trim(odbc_result($result_acc,'accpronc1')); // 1 Nombre conductor
                        $paciente["Acccn2"] = trim(odbc_result($result_acc,'accpronc2')); // 2 Nombre conductor
                        $paciente["Acccti"] = trim(odbc_result($result_acc,'accprotic')); // Tipo documento conductor
                        // $paciente["Acccni"] = trim(odbc_result($result_acc,'accproid2')); // Número documento conductor
                    }
                }
            }
        }
    }
    else
    {
        $query_cat = "  SELECT  pacevchis, pacevcnum, evchor, evcmun, evcdir, evczon, evcfec
                        FROM    inpacevc, inevc
                        WHERE   pacevchis = '{$id_paciente['historia_accidente']}'
                                AND pacevcnum = '{$id_paciente['ingreso_accidente']}'
                                AND pacevcevc = evccod
                        ORDER BY evczon DESC";

        if($result_cat = odbc_exec($conexUnix,$query_cat))
        {
            if(odbc_fetch_row($result_cat))
            {
                // Departamento - minicipio
                $depmun = trim(odbc_result($result_cat,'evcmun'));
                $depto = substr($depmun, 0, 2);
                $munic = substr($depmun, 2);
                $evchor = str_replace(".",":",trim(odbc_result($result_cat,'evchor')));
                $evchor = validarFormatoHoraCorrecto($evchor);

                $ase_est = explode("-",$aseguradoraEstatalFosyga);

                $paciente["Accdep"]           = $depto;
                $paciente["Accmun"]           = $depto.$munic;
                $paciente["codicion_victima"] = "";
                $paciente["dir_evento"]       = trim(odbc_result($result_cat,'evcdir'));
                $paciente["fecha_evento"]     = trim(odbc_result($result_cat,'evcfec'));
                $paciente["hora_evento"]      = $evchor;
                $paciente["Acczon"]           = trim(odbc_result($result_cat,'evczon'));
                $paciente["Accase"]           = $ase_est[0];
                $paciente["Accmar"]           = "";
                $paciente["Accpla"]           = "";
                $paciente["Acctse"]           = "";
                $paciente["Acccas"]           = "";
                $paciente["Accpol"]           = "";
                $paciente["Accvfi"]           = "";
                $paciente["Accvff"]           = "";
                $paciente["Accaut"]           = "S";
                $paciente["Acccep"]           = "";
                $paciente["Acccdi"]           = "";
                $paciente["Acccdp"]           = $depto;
                $paciente["Acccmn"]           = $depto.$munic;
                $paciente["Accctl"]           = "";
            }
        }
    }
}

function consulta_datos_historia_encabezado_unix($conex, $conexUnix, $wemp_pmla, $aseguradoraEstatalFosyga, $historia, $ingreso, $id_enc, $arr_tipoVehiculoUnx, $arrInfo_historiaAyudas)
{
    $paciente    = array();
    $id_paciente = array("historia"             => $historia,
                        "ingreso"               => $ingreso,
                        "documento"             => "",
                        "historia_accidente"    => "",
                        "ingreso_accidente"     => "",
                        "consecutivo_accidente" => "");
    $tabla_paciente_unx = '';
    if($arrInfo_historiaAyudas["fuente_his"] != '01' && $arrInfo_historiaAyudas["farmacia"] != 'on')
    {
        $tabla_paciente_unx = 'aymov';
    }
    else
    {
        $tabla_paciente_unx = consultar_tabla_paciente($conex, $conexUnix, $wemp_pmla, $historia, $ingreso, $id_paciente);
    }


    if($tabla_paciente_unx != '')
    {
        $paciente = array(  "Pacap1"=>"", "Pacap2"=>"", "Pacno1"=>"", "Pacno2"=>"", "Pactdo"=>"", "Pacdoc"=>"", "Pacfna"=>"", "Pacsex"=>"", "Pacdir"=>"",
                            "Pacdep"=>"", "Paciu" =>"", "Pactel"=>"", "Ingfei"=>"", "Inghin"=>"", "Ingdig"=>"", "Accdep"=>"", "Accmun"=>"", "Acczon"=>"",
                            "Accase"=>"", "Accmar"=>"", "Accpla"=>"", "Acctse"=>"", "Acccas"=>"", "Accpol"=>"", "Accvfi"=>"", "Accvff"=>"", "Accaut"=>"",
                            "Acccep"=>"", "Accsvi"=>"", "Acctid"=>"", "Accnid"=>"", "Accap1"=>"", "Accap2"=>"", "Accno1"=>"", "Accno2"=>"", "Accpdi"=>"",
                            "Acctel"=>"", "Accpdp"=>"", "Accpmn"=>"", "Accca1"=>"", "Accca2"=>"", "Acccn1"=>"", "Acccn2"=>"", "Acccti"=>"", "Acccni"=>"",
                            "Acccdi"=>"", "Acccdp"=>"", "Acccmn"=>"", "Accctl"=>"", "Ubifad"=>"", "Ubihad"=>"", "Diamed"=>"", "diag_egre_ayuda"=>"",
                            "codicion_victima"=>"", "naturaleza_evento"=>"", "desc_otro_evento"=>"", "dir_evento"=>"", "fecha_evento"=>"", "hora_evento"=>"",
                        );
        // Defino el nombre de la tabla temporal para datos de ingreso y egreso
        // $randomnum = rand(1, 1000);
        // Primero se buscan datos de un accidente para el paciente y luego con ese ingreso intentar buscar los demás datos del paciente.
        consultar_datos_accidente_paciente($conex, $conexUnix, $wemp_pmla, $aseguradoraEstatalFosyga, $historia, $ingreso, $id_enc, $tabla_paciente_unx, $arr_tipoVehiculoUnx, $arrInfo_historiaAyudas, $id_paciente, $paciente);
        $datos_encontrados  = consultar_datos_ingreso_egreso_paciente($conex, $conexUnix, $wemp_pmla, $historia, $ingreso, $id_enc, $tabla_paciente_unx, $arrInfo_historiaAyudas, $id_paciente, $paciente);
        if(!$datos_encontrados)
        {
            $paciente = array();
        }
    }
    return $paciente;
}

function consultar_diagnostico_medico_egreso($conex, $conexUnix, $wemp_pmla, $wbasedato_cliame, $wbasedato_movhos, $historia, $ingreso, $consulto_unix, $medico_principal, $arrInfo_historiaAyudas, &$arr_diag_egreso, $paciente)
{
    if(!$consulto_unix)
    {
        // Consulta los diagnósticos de egreso del paciente en matrix.
        $sql_diag = "   SELECT  d109.Diacod, d109.Diatip, d109.Diamed, d109.Diaesm, m48.Medreg,
                                m48.Meddoc, m48.Medtdo, m48.Medno1, m48.Medno2, m48.Medap1, m48.Medap2
                        FROM    {$wbasedato_cliame}_000109 AS d109
                                INNER JOIN
                                {$wbasedato_movhos}_000048 AS m48 ON (m48.Meddoc = d109.Diamed)
                        WHERE   d109.Diahis = '{$historia}'  AND d109.Diaing = '{$ingreso}'
                        ORDER BY d109.Diatip";
        if($result_diag = mysql_query($sql_diag,$conex))
        {
            while($row_diag = mysql_fetch_assoc($result_diag))
            {
                if($row_diag["Diatip"] == 'P')
                {
                    $cod_medico_exp    = explode("-",$row_diag["Medreg"]);
                    $cod_medico_exp[0] = $cod_medico_exp[0]*1;
                    $cod_medico        = implode("", $cod_medico_exp);

                    $arr_diag_egreso["P"]              = $row_diag["Diacod"];
                    $medico_principal["documento"]     = $row_diag["Meddoc"];
                    $medico_principal["especialidad"]  = $row_diag["Diaesm"];
                    $medico_principal["codigo_medico"] = $cod_medico;
                    $medico_principal["codigo"]        = $row_diag["Medreg"];
                    $medico_principal["tipo_doc"]      = $row_diag["Medtdo"];
                    $medico_principal["nombre1"]       = $row_diag["Medno1"];
                    $medico_principal["nombre2"]       = $row_diag["Medno2"];
                    $medico_principal["apll1"]         = $row_diag["Medap1"];
                    $medico_principal["apll2"]         = $row_diag["Medap2"];
                }
                elseif($row_diag["Diatip"] == 'S' && $arr_diag_egreso["S1"] == "")
                {
                    $arr_diag_egreso["S1"] = $row_diag["Diacod"];
                }
                elseif($row_diag["Diatip"] == 'S' && $arr_diag_egreso["S2"] == "")
                {
                    $arr_diag_egreso["S2"] = $row_diag["Diacod"];
                }
            }
        }
        /*else
        {
            echo mysql_error();
        }*/
    }
    else
    {
        $medico_diagnostico = array("Diamed" => "",
                                    "Diaesm" => "",
                                    "Meddoc" => "",
                                    "Medtdo" => "",
                                    "Medreg" => "",
                                    "Medno1" => "",
                                    "Medno2" => "",
                                    "Medap1" => "",
                                    "Medap2" => "",
                                    );

        $campos        = "medesp,medced,medtid";
        $campos_nulos  = "medno1,medno2,medap1,medap2,medreg";
        $defectoCampos = "'','','','',''";
        $tablas        = "inmed";
        $where         = " medcod = '{$paciente['Diamed']}'";
        $query_medico   = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );

        if($result_med = odbc_exec($conexUnix,$query_medico))
        {
            if(odbc_fetch_row($result_med))
            {
                // // $medico_diagnostico["Diamed"] = trim(odbc_result($result_med,'egrmed'));
                // $medico_diagnostico["Diaesm"] = trim(odbc_result($result_med,'medesp'));
                // $medico_diagnostico["Meddoc"] = trim(odbc_result($result_med,'medced'));
                // $medico_diagnostico["Medtdo"] = trim(odbc_result($result_med,'medtid'));
                // $medico_diagnostico["Medreg"] = trim(odbc_result($result_med,'medreg'));
                // $medico_diagnostico["Medno1"] = trim(odbc_result($result_med,'medno1'));
                // $medico_diagnostico["Medno2"] = trim(odbc_result($result_med,'medno2'));
                // $medico_diagnostico["Medap1"] = trim(odbc_result($result_med,'medap1'));
                // $medico_diagnostico["Medap2"] = trim(odbc_result($result_med,'medap2'));

                $medico_principal["documento"]     = trim(odbc_result($result_med,'medced'));
                $medico_principal["especialidad"]  = trim(odbc_result($result_med,'medesp'));
                $medico_principal["codigo_medico"] = $paciente['Diamed'];
                $medico_principal["codigo"]        = trim(odbc_result($result_med,'medreg'));
                $medico_principal["tipo_doc"]      = trim(odbc_result($result_med,'medtid'));
                $medico_principal["nombre1"]       = trim(odbc_result($result_med,'medno1'));
                $medico_principal["nombre2"]       = trim(odbc_result($result_med,'medno2'));
                $medico_principal["apll1"]         = trim(odbc_result($result_med,'medap1'));
                $medico_principal["apll2"]         = trim(odbc_result($result_med,'medap2'));
            }
        }

        if($arrInfo_historiaAyudas['fuente_his'] == '01' ||  $arrInfo_historiaAyudas["farmacia"] == 'on')
        {
            // Si la información del paciente no la encontró en matrix entonces busca los diagnósticos de egreso en unix.
            $query_diag ="  SELECT  mdiadia, mdiatip
                            FROM    inmdia
                            WHERE   mdiahis = '{$historia}'
                                    AND mdianum = '{$ingreso}'";
            if($result_diag = odbc_exec($conexUnix,$query_diag))
            {
                // if(odbc_fetch_row($result_diag))
                // {
                //     $paciente["Diacod"] = trim(odbc_result($result_diag,'mdiadia'));
                //     $paciente["Diatip"] = trim(odbc_result($result_diag,'mdiatip'));
                // }
                while(odbc_fetch_row($result_diag))
                {
                    $row_diag["Diacod"] = trim(odbc_result($result_diag,'mdiadia'));
                    $row_diag["Diatip"] = trim(odbc_result($result_diag,'mdiatip'));
                    if($row_diag["Diatip"] == 'P')
                    {
                        $cod_medico = $paciente["Diamed"];

                        $arr_diag_egreso["P"]              = $row_diag["Diacod"];
                        // $medico_principal["documento"]     = $medico_diagnostico["Meddoc"];
                        // $medico_principal["especialidad"]  = $medico_diagnostico["Diaesm"];
                        // $medico_principal["codigo_medico"] = $cod_medico;
                        // $medico_principal["codigo"]        = $medico_diagnostico["Medreg"];
                        // $medico_principal["tipo_doc"]      = $medico_diagnostico["Medtdo"];
                        // $medico_principal["nombre1"]       = $medico_diagnostico["Medno1"];
                        // $medico_principal["nombre2"]       = $medico_diagnostico["Medno2"];
                        // $medico_principal["apll1"]         = $medico_diagnostico["Medap1"];
                        // $medico_principal["apll2"]         = $medico_diagnostico["Medap2"];
                    }
                    elseif($row_diag["Diatip"] == 'S' && $arr_diag_egreso["S1"] == "")
                    {
                        $arr_diag_egreso["S1"] = $row_diag["Diacod"];
                    }
                    elseif($row_diag["Diatip"] == 'S' && $arr_diag_egreso["S2"] == "")
                    {
                        $arr_diag_egreso["S2"] = $row_diag["Diacod"];
                    }
                }
            }
        }
        else
        {
            $arr_diag_egreso["P"] = $paciente["diag_egre_ayuda"];
        }
    }

    if($medico_principal["codigo"] != '')
    {
        $medico_principal["codigo"] = trim(preg_replace('/[ ]+/', '', limpiarString($medico_principal["codigo"])));
    }

    return $medico_principal;
}

function consultar_tipos_vehiculo_unix($conex, $conexUnix, $wemp_pmla)
{
    $arr_tipoVehiculoUnx = array();
    $query_tipos =" SELECT  ctedetpar, ctedetdes
                    FROM    sictedet
                    WHERE   ctedetcod = 'CLAVEH'";
    if($result_tipo = odbc_exec($conexUnix,$query_tipos))
    {
        while(odbc_fetch_row($result_tipo))
        {
            $cod_tipo = trim(odbc_result($result_tipo,'ctedetpar'));
            $desc_tipo= trim(odbc_result($result_tipo,'ctedetdes'));
            if(!array_key_exists($cod_tipo, $arr_tipoVehiculoUnx))
            {
                $arr_tipoVehiculoUnx[$cod_tipo] = $desc_tipo;
            }
        }
    }
    return $arr_tipoVehiculoUnx;
}

function obtenerTipoFacUnix($conexUnix, $concepto, $procedimiento, $empresa)
{
    $info                    = array();
    $info["tipoLiquidacion"] = '';
    $info["grupoQuirurgico"] = '';
    $info["nombre_pro_exa"]  = '';
    $info["codigo_propio"]   = '';
    $info["codigo_grupo_qx"] = '';
    $info["nombre_grupo_qx"] = '';
    $info["cod_nom_grupo"]   = '';

    // --> Obtener el tipo de liquidacion
    $tipoLiquidacion = '';
    $grupoQuirurgico = '';
    $nombre_pro_exa  = '';
    $codigo_propio   = '';
    $codigo_grupo_qx = '';
    $nombre_grupo_qx = '';
    $existe_cod_nom_grupo = false;

    // --> 1- Obtengo si el concepto se relaciona con procedimientos o examenes.
    $sqlRelCon = "  SELECT  conarc
                    FROM    facon
                    WHERE   concod = '{$concepto}'
                            AND conarc IS NOT NULL
                    UNION
                    SELECT  ''
                    FROM    facon
                    WHERE   concod = '{$concepto}'
                            AND (conarc IS NULL OR conarc = '')";
    $sqlTipLiqXemp = '';
    $sqlTipLiq     = '';
    $resRelCon = odbc_exec($conexUnix, $sqlRelCon);
    if(odbc_fetch_row($resRelCon))
    {
        $prefTbUnx = "";
        $relConcepto = trim(odbc_result($resRelCon, 1));
        switch($relConcepto)
        {
            // --> Buscar el tipo del liquidacion en procedimientos.
            case 'INPROTAR':
            {
                $prefTbUnx = "pro";
                // --> Query para buscar si existe un tipo de liquidacion especifica para la empresa
                $campos        = "proempliq, proempane";
                $campos_nulos  = "proempnom";
                $defectoCampos = "''";
                $tablas        = "inproemp";
                $where         = "  proemppro = '{$procedimiento}'
                                    AND proempemp = '{$empresa}'";
                $sqlTipLiqXemp = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );

                // --> Query para buscar el tipo de liquidacion general del procedimieto
                $campos        = "proliq";
                $campos_nulos  = "proqui,pronom,proane";
                $defectoCampos = "''";
                $tablas        = "inpro";
                $where         = "  procod = '{$procedimiento}'
                                    AND proliq != ''";
                                        // AND proact = 'S'
                                        // AND proact = 'S'
                $sqlTipLiq     = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );

                break;
            }
            // --> Buscar el tipo del liquidacion en examenes.
            case 'INEXATAR':
            {
                $prefTbUnx = "exa";
                // --> Query para buscar si existe un tipo de liquidacion especifica para la empresa
                $campos        = "exaempliq, exaempane";
                $campos_nulos  = "exaempnom";
                $defectoCampos = "''";
                $tablas        = "inexaemp";
                $where         = "  exaempexa = '{$procedimiento}'
                                    AND exaempemp = '{$empresa}'";
                $sqlTipLiqXemp = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );

                // --> Query para buscar el tipo de liquidacion general del examen
                $campos        = "exaliq";
                $campos_nulos  = "exagex,exanom,exaane";
                $defectoCampos = "''";
                $tablas        = "inexa";
                $where         = "  exacod = '{$procedimiento}'
                                    AND exaliq != ''";
                $sqlTipLiq     = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );
                break;
            }
            // -->  Cuando el campo conarc esta vacio, indica que es el grabador quien dijita el valor
            //      de la tarifa, es decir no hay tarifa definida en unix.
            default:
            {
                    // Se modifican los valores de $codigo_propio, $nombre_pro_exa
                    buscarExaSoat($conexUnix, $procedimiento, $codigo_propio, $nombre_pro_exa);
                    $info["nombre_pro_exa"]  = $nombre_pro_exa;
                    $info["codigo_propio"]   = $codigo_propio;
                return $info;
                break;
            }
        }

        // --> Ejecutar query de tipo de liquidacion especifica para la empresa
        $resTipLiqXemp = odbc_exec($conexUnix, $sqlTipLiqXemp);
        if(odbc_fetch_row($resTipLiqXemp))
        {
            $tipoLiquidacion = trim(odbc_result($resTipLiqXemp, 1));

            switch ($tipoLiquidacion) {
                case 'C': $tipoLiquidacion = 'CODIGO';
                    break;
                case 'G': $tipoLiquidacion = 'GQX';
                    break;
                case 'U': $tipoLiquidacion = 'UVR';
                    break;
                default:
                    break;
            }

            $codigo_propio  =  trim(odbc_result($resTipLiqXemp, 2));
            $nombre_pro_exa  = trim(odbc_result($resTipLiqXemp, 3));
        }

        // echo PHP_EOL.$sqlTipLiqXemp;
        // echo PHP_EOL.$sqlTipLiq;
        // --> Ejecutar query de tipo de liquidacion general del examen o procedimiento
        $resTipLiq = odbc_exec($conexUnix, $sqlTipLiq);
        if(odbc_fetch_row($resTipLiq))
        {
            $tipoLiquidacion = (($tipoLiquidacion != '') ? $tipoLiquidacion : trim(odbc_result($resTipLiq, 1)));

            switch ($tipoLiquidacion) {
                case 'C': $tipoLiquidacion = 'CODIGO';
                    break;
                case 'G': $tipoLiquidacion = 'GQX';
                    break;
                case 'U': $tipoLiquidacion = 'UVR';
                    break;
                default:
                    break;
            }

            $grupoQuirurgico = trim(odbc_result($resTipLiq, 2));
            $nombre_pro_exa  = ($nombre_pro_exa == '') ? trim(odbc_result($resTipLiq, 3)) : $nombre_pro_exa;
            $codigo_anexo    = trim(odbc_result($resTipLiq, 4));
            // echo "$codigo_anexo != $procedimiento".PHP_EOL;
            if($prefTbUnx != '' && $codigo_propio == '') // && $codigo_anexo != '' && $codigo_anexo != $procedimiento
            {
                $cod_proc_exa_propio = ($codigo_anexo != '' && $codigo_anexo != $procedimiento) ? $codigo_anexo: $procedimiento;
                $sqlCodPropio  = "  SELECT  {$prefTbUnx}empane
                                    FROM    in{$prefTbUnx}emp
                                    WHERE   {$prefTbUnx}emp{$prefTbUnx} = '{$cod_proc_exa_propio}'
                                            AND {$prefTbUnx}empemp = '{$empresa}'
                                            AND {$prefTbUnx}empane IS NOT NULL";
                $resCodProp = odbc_exec($conexUnix, $sqlCodPropio);
                if(odbc_fetch_row($resCodProp))
                {
                    $codigo_propio = trim(odbc_result($resCodProp, 1));
                }
            }
        }

        // echo PHP_EOL." $concepto . $procedimiento , [$relConcepto] - [$codigo_anexo] - [$codigo_propio] - $tipoLiquidacion - $grupoQuirurgico :"; //C9221

        // echo PHP_EOL.$tipoLiquidacion;
        if($relConcepto == 'INPROTAR' && $tipoLiquidacion == 'GQX' && $grupoQuirurgico != '')
        {
            $campos        = "quiconcod";
            $campos_nulos  = "quicondes";
            $defectoCampos = "''";
            $tablas        = "faquicon";
            $where         = "  quiconqui = '{$grupoQuirurgico}'
                                AND quiconcon = '{$concepto}'";
            $sqlCodGruQx = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );
            // echo PHP_EOL."$concepto . $procedimiento . $sqlCodGruQx";

            $resCodGruQx = odbc_exec($conexUnix, $sqlCodGruQx);
            if(odbc_fetch_row($resCodGruQx))
            {
                $codigo_grupo_qx = trim(odbc_result($resCodGruQx, 1));
                $nombre_grupo_qx = trim(odbc_result($resCodGruQx, 2));
                $existe_cod_nom_grupo = true;
            }
        }
        elseif($relConcepto == 'INEXATAR' || ($relConcepto == 'INPROTAR' && $codigo_propio == ''))
        {
            // Se modifican los valores de $codigo_propio, $nombre_pro_exa
            buscarExaSoat($conexUnix, $procedimiento, $codigo_propio, $nombre_pro_exa);
        }

        $info["tipoLiquidacion"] = $tipoLiquidacion;
        $info["grupoQuirurgico"] = $grupoQuirurgico;
        $info["nombre_pro_exa"]  = $nombre_pro_exa;
        $info["codigo_propio"]   = $codigo_propio;
        $info["codigo_grupo_qx"] = $codigo_grupo_qx;
        $info["nombre_grupo_qx"] = $nombre_grupo_qx;
        $info["cod_nom_grupo"]   = $existe_cod_nom_grupo;
    }
    return $info;
}

function buscarExaSoat($conexUnix, $procedimiento, &$codigo_propio, &$nombre_pro_exa)
{
    $campos        = "exasoatsoa";
    $campos_nulos  = "exasoatnom";
    $defectoCampos = "''";
    $tablas        = "inexasoat";
    $where         = "  exasoatcli = '{$procedimiento}'";
    $sqlCodExaSoat = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );
    // echo PHP_EOL."$concepto . $procedimiento . $sqlCodExaSoat";

    $resCodExaSoat = odbc_exec($conexUnix, $sqlCodExaSoat);
    if(odbc_fetch_row($resCodExaSoat))
    {
        $codigo_propio = trim(odbc_result($resCodExaSoat, 1));
        $nombre_pro_exa = trim(odbc_result($resCodExaSoat, 2));
    }
}

function validar_nombre_concepto_mat_med($cod_concepto, $nombre_concepto)
{
    // En matrix los conceptos 0168 y 0169 en su nombre especifícan que no mueven inventario, por eso se simplifica el nombre
    // solo con materiales o medicamentos para que quede con ese nombre en el archivo plano.
    if($cod_concepto == '0168')
    {
        $nombre_concepto = "MATERIALES";
    }
    elseif($cod_concepto == '0169')
    {
        $nombre_concepto = "MEDICAMENTOS";
    }
    return $nombre_concepto;
}


function validarActualizarHisIng($conex, $conexUnix, $wemp_pmla, $historia, $ingreso, $num_factura, $aseguradoraEstatalFosyga, $arrInfo_historia)
{
    $fuente_his = $arrInfo_historia["fuente_his"];
    // 01: indica que es una historia clinica, en otro caso es un consecutivo de ayuda diagnóstica por ejemplo, y se debe buscar la historia asociada
    if($fuente_his != '01' && $arrInfo_historia["farmacia"] != 'on')
    {
        $campos        = "movhis";
        $campos_nulos  = "movnum";
        $defectoCampos = "0";
        $tablas        = "aymov";
        $where         = "  movfuo = '{$fuente_his}'
                            AND movdoo = '{$historia}'";
        $sqlEncFac = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );
        // echo PHP_EOL."$concepto . $procedimiento . $sqlCodGruQx";

        $resEncFac = odbc_exec($conexUnix, $sqlEncFac);
        if(odbc_fetch_row($resEncFac))
        {
            $arrInfo_historia["historia"] = trim(odbc_result($resEncFac, 'movhis'));
            $arrInfo_historia["ingreso"]  = trim(odbc_result($resEncFac, 'movnum'));
            $arrInfo_historia["ingreso"]  = ($arrInfo_historia["ingreso"] == 0) ? '': $arrInfo_historia["ingreso"];
        }
    }
    return $arrInfo_historia;
}
/********************** FIN DE FUNCIONES *************************/

/**
 * ********************************************************************************************************************************************************
 * Lógica, procesos de los llamados AJAX de todo el programa - INICIO DEL PROGRAMA
 * ********************************************************************************************************************************************************
 */
if(isset($accion))
{
    $data = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    switch($accion)
    {
        case 'validar_archivos_generados':
                $data["seleccionar_facts_html"] = '';
                $data["buscar_facturas_ap"]     = '';
                $columnas_facturas = 2;
                $arr_seleccion_facturas = array();
                $estado_encabezado = 'GR';
                $sql_Enc_GR = queryEncabezadoFacturas($conex, $wbasedato_cliame, $filtrar_fecha, $fecha_inicio_rep, $fecha_final_rep, $wentidad_respuesta, $limite_enc_facturas_plano_furips, $estado_encabezado, $westado_cartera, $wFarmacia, false);
                $facturas_GR = 0;
                $facturas_AP = 0;

                if($result_Enc_GR = mysql_query($sql_Enc_GR,$conex))
                {
                    $facturas_GR = mysql_num_rows($result_Enc_GR);
                    while ($row = mysql_fetch_assoc($result_Enc_GR))
                    {
                        $id_273      = $row["consec_reclamacion"];
                        $num_factura = $row["num_factura"];
                        if(!array_key_exists($id_273, $arr_seleccion_facturas))
                        {
                            $arr_seleccion_facturas[$id_273] = $num_factura;
                        }
                    }

                    if($operacion == 'listar_facturas')
                    {
                        //Mostrar facturas en columnas para seleccionar las que van a archivo plano.
                        $arr_columnas = array_chunk($arr_seleccion_facturas, $columnas_facturas, true);
                        // print_r($arr_columnas);
                        $tr_facts = "";
                        $contTr = 0;
                        foreach ($arr_columnas as $key => $arr_info_fac)
                        {
                            $contTr++;
                            $cssTr = ($contTr % 2 == 0) ? 'fila1': 'fila2';
                            $tr_facts .= '  <tr class="'.$cssTr.'">';
                            foreach ($arr_info_fac as $id_fact => $n_factura)
                            {
                                                    // <!-- <input type="checkbox" id="ckfact_'.$id_fact.'" value="'.$id_fact.'" factura="'.$n_factura.'" estado_enc="'.$estado_encabezado.'">'.$n_factura.' -->
                                $tr_facts .= '  <td style="text-align:center;">
                                                    <span class="button-checkbox">
                                                        <button type="button" class="btn" data-color="primary" id="ckfact_'.$id_fact.'" value="'.$id_fact.'" factura="'.$n_factura.'" estado_enc="'.$estado_encabezado.'" onclick="seleccion_click_facturas(this);">'.trim($n_factura).'</button>
                                                        <input type="checkbox" class="hidden">
                                                    </span>
                                                </td>';
                                if(count($arr_info_fac) < $columnas_facturas)
                                {
                                    $cols_vacio = $columnas_facturas - count($arr_info_fac);
                                    $tr_facts .= '<td colspan="'.$cols_vacio.'">&nbsp;</td>';
                                }
                            }
                            $tr_facts .= '  </tr>';
                        }
                        $data["seleccionar_facts_html"] = '
                                                    <table align="center">
                                                        <tr class="encabezadoTabla">
                                                            <td colspan="2">Seleccione las facturas para crear el archivo plano</td>
                                                        </tr>
                                                        '.$tr_facts.'
                                                    </table>'; //256px

                        $data["buscar_facturas_ap"] = '
                                                    <div style="width:100%; height: 300px; overflow:auto;background-color:#FFFFCC;">
                                                    <table align="center" id="tabla_seleccion_facturas_ap">
                                                        <tr class="encabezadoTabla">
                                                            <td>Seleccione las facturas para crear el archivo plano</td>
                                                        </tr>
                                                        <tr class="encabezadoTabla tr_buscar_facturas_ap" id="">
                                                            <td ><input type="text" value="" id="buscar_facturas_ap" placeholder="Número factura" onkeypress="enterBuscar(event,this);"> <input type="button" value="Buscar" id="btn_buscar_facturas_ap" onclick="generarArchivosPlanos(\'buscar_facturas_ap\');"></td>
                                                        </tr>
                                                    </table>
                                                    </div>';
                    }

                    $sql_Enc_AP = queryEncabezadoFacturas($conex, $wbasedato_cliame, $filtrar_fecha, $fecha_inicio_rep, $fecha_final_rep, $wentidad_respuesta, $limite_enc_facturas_plano_furips, 'AP', $westado_cartera, $wFarmacia, false);
                    if($result_Enc_AP = mysql_query($sql_Enc_AP,$conex))
                    {
                        $facturas_AP = mysql_num_rows($result_Enc_AP);
                    }
                }
                else
                {
                    $data["error"]   = 1;
                    $data["mensaje"] = "Problemas al consultar la lista de facturas";
                    $data["sql_err"] = mysql_error().' > '.$sql_Enc_GR;
                }

                $data["facturas_GR"] = $facturas_GR;
                $data["facturas_AP"] = $facturas_AP;
            break;
        case 'generar_archivos_planos':
                $facturas_seleccionadas = json_decode(str_replace("\\", '',  $facturas_seleccionadas), TRUE);
                $arr_encabezados_AP     = json_decode(str_replace("\\", '',  $arr_encabezados_AP), TRUE);

                $filtrar_encabezadoskey = array_keys($facturas_seleccionadas);
                $filtrar_encabezados    = implode(",", $filtrar_encabezadoskey);
                // echo print_r($facturas_seleccionadas);
                // echo print_r($filtrar_encabezados);
                // return;

                $consultaAjax = '';
                $limite_enc_facturas_plano_furips = $limite_enc_facturas_plano_furips*1;
                include_once("root/comun.php");
                eliminarArchivosDiasAnteriores($dir, $dias_cache_archivos_furips);
                $nombreFurips_link = '';
                $arr_err           = array("error_msj"=>array());
                $arr_alert         = array();
                $arr_his_ing_nogen = array(); // Facturas no generadas en archivo plano plano por historia e ingreso.
                $arr_descArch      = descripcionCamposArchivo();

                $arr_ids_generados            = array();
                $arr_campos_furipsEnc         = array();
                $arr_campos_furipsDll         = array();
                $arr_campos_furipsEncConfig   = array();
                $arr_no_generadas_err         = array(); // Facturas no generadas porque no se encontró información de historia e ingreso.
                $data["html_no_generadas"]    = '';
                $nombreFurips1                = "";
                $nombreFurips2                = "";
                $data["facturas_encontradas"] = 0;

                $arrayArticulo       = array();
                $arrayConceptosUnxMx = array();
                $furips_medicamentos = array();
                $furips_otros_servicios = array();
                if($wFarmacia == 'on'){
                    $arrayArticulo          = generarArrArticulosEmpresa($conex, $wbasedato_empresa);
                    $arrayConceptosUnxMx    = obtener_array_concepto_empresa($conex, $wemp_pmla, $wbasedato_empresa);
                    $furips_medicamentos    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'furips_medicamentos_farmacia');
                    $furips_otros_servicios = consultarAliasPorAplicacion($conex, $wemp_pmla, 'furips_otros_servicios_farmacia');
                } else {
                    $arrayArticulo          = generarArrArticulos($conex, $wbasedato_movhos);
                    $arrayConceptosUnxMx    = obtener_array_concepto_matrix_unix($conex, $wemp_pmla, $wbasedato_cliame);
                    $furips_medicamentos    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'furips_medicamentos');
                    $furips_otros_servicios = consultarAliasPorAplicacion($conex, $wemp_pmla, 'furips_otros_servicios');
                }
                $arrayProcedimientos          = generarArrProcedimientos($conex, $wbasedato_cliame);
                $arrayProcedimientosEmpresa   = obtener_array_procedimientosEmpresa($conex, $wemp_pmla, $wbasedato_cliame, $wentidad_respuesta);
                $concepto_medicamentos        = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_medicamentos_mueven_inv');
                $concepto_materiales          = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_materiales_mueven_inv');
                $aseguradoraEstatalFosyga     = $wentidad_respuesta; //consultarAliasPorAplicacion($conex, $wemp_pmla, 'aseguradoraEstatalFosyga');
                $data["faltantes_por_generar"]= 0;
                $data["pendientes_html"]      = "";
                $data["notificaciones_html"]  = "";
                $conectado_a_unix             = false;

                $furips_medicamentos          = explode(",", $furips_medicamentos);
                $furips_otros_servicios       = explode(",", $furips_otros_servicios);

                $arr_codicion_victima = array(""=>"","O"=>3,"P"=>2,"C"=>1,"I"=>4);

                // Matrix => "A":ASEGURADO, "N":NO ASEGURADO, "F":FANTASMA, "V":VEHICULO EN FUGA
                // FURIPS => 1:Asegurado 2:No asegurado 3:Vehículo fantasma 4:Póliza falsa 5:Vehículo en fuga
                $arr_aseguramiento = array("A"=>1,"N"=>2,"F"=>3,"X"=>4,"V"=>5,""=>"");

                // Matrix => "1":PARTICULAR, "2":PUBLICO, "3":OFICIAL, "4":DE EMERGENCIA, "5":DIPLOMATICO O CONSULAR, "6":TRANSPORTE MASIVO, "7":ESCOLAR,
                // FURIPS => 3 = Particular, 4 = Público, 5 = Oficial, 6 = De emergencia, 7 = Diplomático o consular, 8 = Transporte Masivo, 9 = Escolar
                $arr_tipoVehiculo = array(  "1"=>3, "2"=>4, "3"=>5, "4"=>6, "5"=>7, "6"=>8, "7"=>9, ""=>"");
                $arr_tipoVehiculoUnx = array();

                // Matrix => "CC":CEDULA DE CIUDADANIA, "CE":CEDULA DE EXTRANJERIA, "PA":PASAPORTE, "TI":TARJETA DE IDENTIDAD, "RC":REGISTRO CIVIL, "NI":NIT, "MS":MENOR SIN IDENTIFICACION, "AS":ADULTO SIN IDENTIFICACION, "NU":NRO UNICO DE IDENTIFICAC.,
                // FURIPS => CC:Cédula de ciudadanía., CE:Cédula de extranjería., PA:Pasaporte., TI:Tarjeta de identidad, RC:Registro Civil, NI:Número de identificación tributaria, CD:Carnet Diplomatico,
                $arr_tipoDocumentosAccPro = array("CC"=>"CC", "CE"=>"CE", "PA"=>"PA", "TI"=>"TI", "RC"=>"RC", "NI"=>"NI");

                // if($cambiar_estado_encabezados == 'on')
                if(count($arr_encabezados_AP) > 0)
                {
                    // Cambiar el estado de AP:Archivo plano a GR:Glosa revisada, para que nuevamente se puedan los archivos.
                    cambiarEstadoFacturasFiltradas($conex, $wbasedato_cliame, $user_session, $data, $filtrar_fecha, $fecha_inicio_rep, $fecha_final_rep, $wentidad_respuesta, $limite_enc_facturas_plano_furips, 'GR', $arr_encabezados_AP, 'false');
                    if($data["error"]*1 == 1)
                    {
                        break;
                    }
                }

                // $sql_Enc = "SELECT  '' AS idg_enc, '4370089' AS num_factura, '575424' as historia, '3' AS ingreso"; // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
                $sql_Enc = queryEncabezadoFacturas($conex, $wbasedato_cliame, $filtrar_fecha, $fecha_inicio_rep, $fecha_final_rep, $wentidad_respuesta, $limite_enc_facturas_plano_furips, 'GR', $westado_cartera, $wFarmacia, true, '', $filtrar_encabezados);
                if($result_Enc = mysql_query($sql_Enc,$conex))
                {
                    $conexUnix = '';
                    // Si el paciente no fue encontrado en matrix se intenta buscar en unix.
                    if(!$conectado_a_unix && $hay_unix)
                    {
                        if($conexUnix = @odbc_connect('facturacion','informix','sco'))
                        {
                            $conectado_a_unix = true;
                            $arr_tipoVehiculoUnx = consultar_tipos_vehiculo_unix($conex, $conexUnix, $wemp_pmla);
                        }
                        else
                        {
                            $data['mensaje'] = "Problemas en la conexión a unix para consultar la información del paciente.";
                            $data['error']   = 1;
                            // $data['sql']    .= $sql_pac." > ".mysql_error().PHP_EOL;
                            echo json_encode($data);
                            return;
                        }
                    }

                    $naturaleza_evento = $wnaturaleza_evento;            // [PENDIENTE] Naturaleza del evento

                    $num_facturas  = mysql_num_rows($result_Enc);
                    $num_facturas_no_generadas = 0;
                    while ($row_enc = mysql_fetch_assoc($result_Enc))
                    {
                        $num_radicado       = $row_enc["num_radicado"]; //(!isset($num_radicado)) ? 1000 : ($num_radicado++); // Número de radicado
                        $num_factura        = $row_enc["num_factura"];  //(!isset($num_factura)) ? 1 : ($num_factura++);      // Número de factura
                        $consec_reclamacion = $row_enc["consec_reclamacion"];
                        $fuente_historia    = $row_enc["fuente_historia"];
                        $estado_cartera     = $row_enc["estado_cartera"];
                        $farmacia           = $row_enc["farmacia"];
                        $id_enc             = $consec_reclamacion;
                        $respuesta_glosa    = "";             // Respuesta a glosa, 0 si es glosa total, en otro caso es vacío

                        $glosa_total     = $row_enc["glosa_total"];
                        if($glosa_total == 'on')
                        {
                            $respuesta_glosa = "0";
                        }

                        if($num_radicado != '')
                        {
                            $respuesta_glosa = "0";
                        }

                        if($estado_cartera == 'AP')
                        {
                            $respuesta_glosa = "";
                        }
                        // elseif($glosa_total != 'on' && $num_radicado != '')
                        // {
                        //     $respuesta_glosa = "1";
                        // }

                        // $index_fact   = $num_radicado.'_'.$num_factura;
                        $index_fact   = $id_enc;


                        $historia           = $row_enc["historia"];
                        $ingreso            = $row_enc["ingreso"];
                        $paciente_en_matrix = true;
                        $paciente           = array();
                        $consulto_unix      = false;
                        $fecha_limite       = $fecha_actual; // '2017-03-01'; // Por ahora se esta consultando todos los datos en unix pues en matrix no está toda la información
                                                            // de la factura, paciente, accidente o ayuda diagnóstica.

                        // ********************************************************************************************************************************************
                        // ******************************                 FURIPS1 ENCABEZADO DE FACTURA                                ********************************
                        // ********************************************************************************************************************************************
                        $sql_pac = "SELECT  c100.Pacap1, c100.Pacap2, c100.Pacno1, c100.Pacno2, c100.Pactdo, c100.Pacdoc, c100.Pacfna, c100.Pacsex, c100.Pacdir, c100.Pacdep, c100.Paciu, c100.Pactel,
                                            c101.Ingfei, c101.Inghin, c101.Ingdig,
                                            c148.Accdep, c148.Accmun, c148.Acccon AS codicion_victima, '' AS naturaleza_evento, '' AS desc_otro_evento, c148.Accdir AS dir_evento, c148.Accfec AS fecha_evento, c148.Acchor AS hora_evento,
                                            c148.Acczon, c148.Accase, c148.Accmar, c148.Accpla, c148.Acctse, c148.Acccas, c148.Accpol, c148.Accvfi, c148.Accvff, c148.Accaut, c148.Acccep, '' AS Accsvi,
                                            c148.Acctid, c148.Accnid, c148.Accap1, c148.Accap2, c148.Accno1, c148.Accno2, c148.Accpdi, c148.Acctel, c148.Accpdp, c148.Accpmn, c148.Accca1, c148.Accca2, c148.Acccn1, c148.Acccn2,
                                            c148.Acccti, c148.Acccni, c148.Acccdi, c148.Acccdp, c148.Acccmn, c148.Accctl,
                                            mv18.Ubifad, mv18.Ubihad
                                    FROM    {$wbasedato_cliame}_000100 AS c100
                                            INNER JOIN
                                            {$wbasedato_cliame}_000101 AS c101 ON (c101.Inghis = c100.Pachis AND c101.Ingnin = '{$ingreso}')
                                            LEFT JOIN
                                            {$wbasedato_cliame}_000148 AS c148 ON (c148.Acchis = c101.Inghis AND c148.Accing = c101.Ingnin)
                                            INNER JOIN
                                            {$wbasedato_movhos}_000018 AS mv18 ON (mv18.Ubihis = c101.Inghis AND mv18.Ubiing = c101.Ingnin)
                                    WHERE   c100.Pachis = '{$historia}'
                                            AND c101.Ingfei > '{$fecha_limite}'";
                        if($result_pac = mysql_query($sql_pac,$conex))
                        {
                            if(mysql_num_rows($result_pac) > 0)
                            {
                                $paciente = mysql_fetch_assoc($result_pac);
                                $consulto_unix = false;
                            }
                            // elseif($hay_unix && $conectado_a_unix)
                            if($hay_unix && $conectado_a_unix)
                            {
                                // echo "<pre>".print_r($paciente,true)."</pre>";
                                $consulto_unix = true;
                                $arrInfo_historiaAyudas = array();
                                $arrInfo_historiaAyudas["consec_cargo"] = $historia; // consecutivo de cargo generado en ayuda diagnóstica
                                $arrInfo_historiaAyudas["historia"]     = $historia;
                                $arrInfo_historiaAyudas["ingreso"]      = $ingreso;
                                $arrInfo_historiaAyudas["fuente_his"]   = $fuente_historia;
                                $arrInfo_historiaAyudas["farmacia"]     = $farmacia;

                                // Actualizar historia-ingreso si este campo no es 01
                                if($fuente_historia != '01' && $farmacia != 'on')
                                {
                                    $arrInfo_historiaAyudas = validarActualizarHisIng($conex, $conexUnix, $wemp_pmla, $historia, $ingreso, $num_factura, $aseguradoraEstatalFosyga, $arrInfo_historiaAyudas);
                                    $historia = $arrInfo_historiaAyudas["historia"];
                                    $ingreso  = $arrInfo_historiaAyudas["ingreso"];
                                }
                                // EN CASO DE NO ENCONTRAR LA INFORMACIÓN DEL PACIENTE EN MATRIX, SE CONECTA A UNIX Y
                                // SE CONSULTA TODA LA INFORMACIÓN DEL PACIENTE, ACCIDENTE,PARA EL ENCABEZADO POR HISTORIA E INGRESO
                                $paciente = consulta_datos_historia_encabezado_unix($conex, $conexUnix, $wemp_pmla, $aseguradoraEstatalFosyga, $historia, $ingreso, $id_enc, $arr_tipoVehiculoUnx, $arrInfo_historiaAyudas);
                            }
                        }
                        else
                        {
                            $data['mensaje'] = "No se pudo consultar historia-ingreso del paciente en Matrix";
                            $data['error']   = 1;
                            $data['sql']     .= $sql_pac." > ".mysql_error().PHP_EOL;
                        }

                        // if($result_pac = mysql_query($sql_pac,$conex))
                        {
                            // if($paciente = mysql_fetch_assoc($result_pac)) // && $historia != '366054'
                            if(count($paciente) > 0)
                            {
                                if(!array_key_exists($index_fact, $arr_campos_furipsEnc))
                                {
                                    $arr_campos_furipsEnc[$index_fact] = array();
                                }

                                $Pactdo = $paciente["Pactdo"];
                                $Pacdoc = $paciente["Pacdoc"];
                                $Pacdir = $paciente["Pacdir"];
                                $Pactel = $paciente["Pactel"];
                                $Pacdep = $paciente["Pacdep"];
                                $Paciu  = $paciente["Paciu"];
                                if(($Pactdo == 'AS' || $Pactdo == 'MS') && $naturaleza_evento == '01')
                                {
                                    $Pacdoc = limpiarString($paciente["Accdep"].$paciente["Accmun"])."NN".$historia;
                                }
                                elseif(($Pactdo == 'AS' || $Pactdo == 'MS') && $naturaleza_evento != '01')
                                {
                                    $Pacdoc = limpiarString($paciente["Pacdep"].$paciente["Paciu"])."NN".$historia;
                                }

                                $num_factura_archivo = strtoupper(str_replace(" ","",limpiarString($num_factura)));

                                // 1. Datos de la reclamación.
                                $arr_campos_furipsEnc[$index_fact][1]   = substr(limpiarString($num_radicado), 0, 10);                                                                                       // número radicado
                                $arr_campos_furipsEnc[$index_fact][2]   = substr(limpiarString($respuesta_glosa), 0, 1);                                                                                     // respuesta a glosa
                                $arr_campos_furipsEnc[$index_fact][3]   = obligatorioVal(3,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr($num_factura_archivo, 0, 20),true);// [Rq] número factura
                                $arr_campos_furipsEnc[$index_fact][4]   = obligatorioVal(4,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($consec_reclamacion), 0, 12),true);           // [Rq] consecutivo reclamación
                                                                        // 2. Datos del prestador de servicios de salud.
                                $arr_campos_furipsEnc[$index_fact][5]   = obligatorioVal(5,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($codigo_habilitacion), 0, 12),true);          // [Rq] código de habitación
                                                                        // 3. Datos de la víctima del evento catastrófico o accidente de tránsito.
                                $arr_campos_furipsEnc[$index_fact][6]   = obligatorioVal(6,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($paciente["Pacap1"]), 0, 20), true);          // [Rq] apellido 1
                                $arr_campos_furipsEnc[$index_fact][7]   = substr(limpiarString($paciente["Pacap2"]), 0, 30);                                                                                 // apellido 2
                                $arr_campos_furipsEnc[$index_fact][8]   = obligatorioVal(8,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($paciente["Pacno1"]), 0, 20),true);           // [Rq] nombre 1
                                $arr_campos_furipsEnc[$index_fact][9]   = substr(limpiarString($paciente["Pacno2"]), 0, 30);                                                                                 // nombre 2
                                $arr_campos_furipsEnc[$index_fact][10]  = obligatorioVal(10,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($Pactdo), 0, 2),true);                       // [Rq] tipo documento
                                $arr_campos_furipsEnc[$index_fact][11]  = obligatorioVal(11,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($Pacdoc), 0, 16),true);                      // [Rq] número documento
                                $arr_campos_furipsEnc[$index_fact][12]  = substr(fechaFurips($paciente["Pacfna"]), 0, 10);                                                                                   // fecha de nacimiento
                                $arr_campos_furipsEnc[$index_fact][13]  = substr(limpiarString($paciente["Pacsex"]), 0, 1);                                                                                  // sexo
                                $arr_campos_furipsEnc[$index_fact][14]  = substr(limpiarString($Pacdir), 0, 40);                                                                                             // dirección residencia victima
                                $arr_campos_furipsEnc[$index_fact][15]  = substr(limpiarString($Pacdep), 0, 2);                                                                                              // código departamento residencia paciente
                                $arr_campos_furipsEnc[$index_fact][16]  = substr(limpiarString($Paciu), -3);                                                                                                 // código mpio. residencia paciente
                                $arr_campos_furipsEnc[$index_fact][17]  = substr(limpiarStringTelefonos(17,$arr_descArch,$arr_alert,'enc',$num_factura,$Pactel), 0, 10);                                      // teléfono paciente
                                $arr_campos_furipsEnc[$index_fact][18]  = substr($arr_codicion_victima[limpiarString($paciente["codicion_victima"])], 0, 1);                                                 // condición de la víctima
                                                                        // 4. Datos del sitio donde ocurrió el evento catastrófico o el accidente de tránsito.
                                $arr_campos_furipsEnc[$index_fact][19]  = obligatorioVal(19,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($naturaleza_evento), 0, 2),true);            // [Rq] naturaleza del evento
                                $arr_campos_furipsEnc[$index_fact][20]  = substr(limpiarString($paciente["desc_otro_evento"]), 0, 25);                                                                       // [PENDIENTE] Descripción del otro evento
                                $arr_campos_furipsEnc[$index_fact][21]  = obligatorioVal(21,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($paciente["dir_evento"]), 0, 40),true);      // [Rq] Dirección ocurrencia evento
                                $arr_campos_furipsEnc[$index_fact][22]  = obligatorioVal(22,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(fechaFurips($paciente["fecha_evento"]), 0, 10),true);      // [Rq] Fecha ocurrencia evento
                                $arr_campos_furipsEnc[$index_fact][23]  = obligatorioVal(23,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(horaFurips($paciente["hora_evento"]), 0, 5),true);         // [Rq] Hora ocurrencia evento
                                $arr_campos_furipsEnc[$index_fact][24]  = obligatorioVal(24,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($paciente["Accdep"]), 0, 2),true);           // [Rq] Codigo depto. evento
                                $arr_campos_furipsEnc[$index_fact][25]  = obligatorioVal(25,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($paciente["Accmun"]), -3),true);             // [Rq] Codigo mpio. evento
                                $arr_campos_furipsEnc[$index_fact][26]  = substr(limpiarString($paciente["Acczon"]), 0, 1);                                                                                  // Zona ocurrencia

                                $estado_aseguramiento = $arr_aseguramiento[limpiarString($paciente["Accase"])];
                                $req_tipo_vehi = (in_array($estado_aseguramiento, array(1,2,4))) ? true : false;
                                $arr_campos_furipsEnc[$index_fact][27]  = obligatorioVal(27,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr($estado_aseguramiento, 0, 1),true);                           // [Rq] Estado aseguramiento

                                if($estado_aseguramiento =='5')
                                {
                                    // Cuando el estado de aseguramiento sea 3 "Carro fantasma" o 5 "en fuga", no debe traer datos de propietario, conductor.
                                    $paciente["Acccdp"] = "";
                                    $paciente["Acccmn"] = "";
                                }

                                $req_marca = ($estado_aseguramiento == 1 || $estado_aseguramiento == 2 || $estado_aseguramiento == 5) ? true : false;
                                $req_placa = ($estado_aseguramiento == 1 || $estado_aseguramiento == 2 || $estado_aseguramiento == 4 || $estado_aseguramiento == 5) ? true : false;

                                $placa_auto = trim(preg_replace('/[ ]+/', '', limpiarString($paciente["Accpla"])));// La placa no debe tener espacios intermedios.

                                $arr_campos_furipsEnc[$index_fact][28]  = obligatorioVal(28,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($paciente["Accmar"]), 0, 15),$req_marca);       // Marca
                                $arr_campos_furipsEnc[$index_fact][29]  = obligatorioVal(29,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($placa_auto), 0, 10),$req_placa);       // Placa

                                $Acctse = limpiarString($paciente["Acctse"]);
                                $tipo_vehiculo = "";
                                if($consulto_unix && array_key_exists($Acctse, $arr_tipoVehiculoUnx)) // Si los datos son de unix, el tipo que viene de unix ya es correcto
                                {
                                    $tipo_vehiculo = $Acctse;
                                }
                                elseif(array_key_exists($Acctse, $arr_tipoVehiculo)) // Si el tipo es de matrix, se debe cambiar a un código equivalente a la norma de furips
                                {
                                    $tipo_vehiculo = $arr_tipoVehiculo[$Acctse];
                                }
                                elseif($req_tipo_vehi) // Si no se encontró en ninguno de los dos arreglos de tipos, matrix y unix, pero es obligatorio que deba ir un tipo entonces se usa uno por defecto
                                {
                                    $tipo_vehiculo = '3';
                                }

                                $arr_campos_furipsEnc[$index_fact][30]  = obligatorioVal(30,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr($tipo_vehiculo, 0, 1),$req_tipo_vehi);                       // Tipo vehículo

                                $Acccas = "";
                                $Accpol = "";
                                if($estado_aseguramiento == '1')
                                {
                                    $Acccas = $paciente["Acccas"];
                                    $Accpol = $paciente["Accpol"];
                                }

                                $arr_campos_furipsEnc[$index_fact][31]  = substr(limpiarString($Acccas), 0, 6);                                                                                             // Código aseguradora
                                $arr_campos_furipsEnc[$index_fact][32]  = substr(limpiarString($Accpol), 0, 20);                                                                                            // Número de póliza SOAT
                                $arr_campos_furipsEnc[$index_fact][33]  = substr(fechaFurips($paciente["Accvfi"]), 0, 10);                                                                                  // Fecha inicio vigencia póliza
                                $arr_campos_furipsEnc[$index_fact][34]  = substr(fechaFurips($paciente["Accvff"]), 0, 10);                                                                                  // Fecha final vigencia póliza
                                $arr_campos_furipsEnc[$index_fact][35]  = substr((($paciente["Accaut"] == 'on' || $paciente["Accaut"] == 'S') ? 1: 0), 0, 1);                                               // Intervención de la autoridad
                                $arr_campos_furipsEnc[$index_fact][36]  = substr((($paciente["Acccep"] == 'on' || $paciente["Acccep"] == 'S') ? 1: 0), 0, 1);                                               // Cobro por excedente de póliza
                                                                        // 5. Otro vehículos involucrados
                                $arr_campos_furipsEnc[$index_fact][37]  = substr(limpiarString(""), 0, 10);                                                                                                 //
                                $arr_campos_furipsEnc[$index_fact][38]  = substr(limpiarString(""), 0, 2);                                                                                                  //
                                $arr_campos_furipsEnc[$index_fact][39]  = substr(limpiarString(""), 0, 16);                                                                                                 //
                                $arr_campos_furipsEnc[$index_fact][40]  = substr(limpiarString(""), 0, 10);                                                                                                 //
                                $arr_campos_furipsEnc[$index_fact][41]  = substr(limpiarString(""), 0, 2);                                                                                                  //
                                $arr_campos_furipsEnc[$index_fact][42]  = substr(limpiarString(""), 0, 16);                                                                                                 //

                                $Acctid = $paciente["Acctid"];
                                $Accnid = $paciente["Accnid"];
                                $Accpdi = $paciente["Accpdi"];
                                $Acctel = $paciente["Acctel"];
                                $Accpdp = $paciente["Accpdp"];
                                $Accpmn = $paciente["Accpmn"];
                                // Comparar documento de propietario y víctima, si son iguales entonces usar misma dirección, teléfono, documento de víctima
                                if($Pacdoc == $Accnid)
                                {
                                    $Acctid = $Pactdo;
                                    $Accnid = $Pacdoc;
                                    $Accpdi = $Pacdir;
                                    $Acctel = $Pactel;
                                    $Accpdp = $Pacdep;
                                    $Accpmn = $Paciu;
                                }
                                // $arr_tipoDocumentosAccPro  // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< VALIDAR SI EL TIPO DE DOCUMENTO EXISTE O NO ENTRE LOS TIPOS VÁLIDOS PARA EL ARCHIVO PLANO por ejemplo MS, AS
                                                                        // 6. Datos del propietario del vehículo.
                                $arr_campos_furipsEnc[$index_fact][43]  = substr(limpiarString($Acctid), 0, 2);                                                         // Tipo documento propietario
                                $arr_campos_furipsEnc[$index_fact][44]  = substr(limpiarString($Accnid), 0, 16);                                                        // Número documento propietario
                                $arr_campos_furipsEnc[$index_fact][45]  = substr(limpiarString($paciente["Accap1"]), 0, 40);                                            // 1 Apellido propietario
                                $arr_campos_furipsEnc[$index_fact][46]  = substr(limpiarString($paciente["Accap2"]), 0, 30);                                            // 2 Apellido propietario
                                $arr_campos_furipsEnc[$index_fact][47]  = substr(limpiarString($paciente["Accno1"]), 0, 20);                                            // 1 Nombre propietario
                                $arr_campos_furipsEnc[$index_fact][48]  = substr(limpiarString($paciente["Accno2"]), 0, 30);                                            // 2 Nombre propietario
                                $arr_campos_furipsEnc[$index_fact][49]  = substr(limpiarString($Accpdi), 0, 40);                                                        // Dir. propietario
                                $arr_campos_furipsEnc[$index_fact][50]  = substr(limpiarStringTelefonos(50,$arr_descArch,$arr_alert,'enc',$num_factura,$Acctel), 0, 10); // Teléfono residencia propietario
                                $arr_campos_furipsEnc[$index_fact][51]  = substr(limpiarString($Accpdp), 0, 2);                                                         // Código depto. propietario
                                $arr_campos_furipsEnc[$index_fact][52]  = substr(limpiarString($Accpmn), -3);                                                           // Código mpio. propietario
                                                                        // 7. Datos del conductor involucrado en el accidente de tránsito.
                                $arr_campos_furipsEnc[$index_fact][53]  = substr(limpiarString($paciente["Accca1"]), 0, 20);                                            // 1 Apellido conductor
                                $arr_campos_furipsEnc[$index_fact][54]  = substr(limpiarString($paciente["Accca2"]), 0, 30);                                            // 2 Apellido conductor
                                $arr_campos_furipsEnc[$index_fact][55]  = substr(limpiarString($paciente["Acccn1"]), 0, 20);                                            // 1 Nombre conductor
                                $arr_campos_furipsEnc[$index_fact][56]  = substr(limpiarString($paciente["Acccn2"]), 0, 30);                                            // 2 Nombre conductor

                                $Acccti = $paciente["Acccti"];
                                $Acccni = $paciente["Acccni"];
                                $Acccdi = $paciente["Acccdi"];
                                $Accctl = $paciente["Accctl"];
                                $Acccdp = $paciente["Acccdp"];
                                $Acccmn = $paciente["Acccmn"];

                                if(($Acccti == 'AS' || $Acccti == 'MS') && $naturaleza_evento == '01')
                                {
                                    $Acccni = limpiarString($paciente["Accdep"].$paciente["Accmun"])."NN".$historia;
                                }
                                elseif(($Acccti == 'AS' || $Acccti == 'MS') && $naturaleza_evento != '01')
                                {
                                    $Acccni = limpiarString($paciente["Pacdep"].$paciente["Paciu"])."NN".$historia;
                                }

                                // Comparar documento de conductor y víctima, si son iguales entonces usar misma dirección, teléfono, documento de víctima
                                if($Pacdoc == $Acccni)
                                {
                                    $Acccti = $Pactdo;
                                    $Acccni = $Pacdoc;
                                    $Acccdi = $Pacdir;
                                    $Accctl = $Pactel;
                                    $Acccdp = $Pacdep;
                                    $Acccmn = $Paciu;
                                }

                                // if($Acccti == 'AS');

                                // $arr_tipoDocumentosAccPro  // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< VALIDAR SI EL TIPO DE DOCUMENTO EXISTE O NO ENTRE LOS TIPOS VÁLIDOS PARA EL ARCHIVO PLANO por ejemplo MS, AS
                                $arr_campos_furipsEnc[$index_fact][57]  = substr(limpiarString($Acccti), 0, 2);                                                                                             // Tipo documento conductor
                                // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Si es AS generar un número de acuerdo como se explica en el manual.
                                $arr_campos_furipsEnc[$index_fact][58]  = substr(limpiarString($Acccni), 0, 16);                                                                                            // Número documento conductor
                                $arr_campos_furipsEnc[$index_fact][59]  = substr(limpiarString($Acccdi), 0, 40);                                                                                            // Dirección conductor
                                $arr_campos_furipsEnc[$index_fact][60]  = substr(limpiarString($Acccdp), 0, 2);                                                                                             // Código depto. conductor
                                $arr_campos_furipsEnc[$index_fact][61]  = substr(limpiarString($Acccmn), -3);                                                                                               // Código mpio. conductor
                                $arr_campos_furipsEnc[$index_fact][62]  = substr(limpiarStringTelefonos(62,$arr_descArch,$arr_alert,'enc',$num_factura,$Accctl), 0, 10);                                                                                   // Teléfono conductor
                                                                        // 8. Datos de remisión (La información se convierte en obligatoria siempre y cuando exista remisión de la víctima).
                                $arr_campos_furipsEnc[$index_fact][63]  = substr(limpiarString(""), 0, 1);                                                                                                  // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][64]  = substr(limpiarString(""), 0, 10);                                                                                                 // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][65]  = substr(limpiarString(""), 0, 5);                                                                                                  // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][66]  = substr(limpiarString(""), 0, 12);                                                                                                 // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][67]  = substr(limpiarString(""), 0, 60);                                                                                                 // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][68]  = substr(limpiarString(""), 0, 30);                                                                                                 // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][69]  = substr(limpiarString(""), 0, 10);                                                                                                 // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][70]  = substr(limpiarString(""), 0, 5);                                                                                                  // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][71]  = substr(limpiarString(""), 0, 12);                                                                                                 // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][72]  = substr(limpiarString(""), 0, 60);                                                                                                 // NO NECESARIO
                                                                        // 9. Transporte y movilización de la víctima (La información se convierte en obligatoria siempre y cuando exista movilización de víctima).
                                $arr_campos_furipsEnc[$index_fact][73]  = substr(limpiarString(""), 0, 30);                                                                                                 // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][74]  = substr(limpiarString(""), 0, 10);                                                                                                 // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][75]  = substr(limpiarString(""), 0, 40);                                                                                                 // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][76]  = substr(limpiarString(""), 0, 40);                                                                                                 // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][77]  = substr(limpiarString(""), 0, 1);                                                                                                  // NO NECESARIO
                                $arr_campos_furipsEnc[$index_fact][78]  = substr(limpiarString(""), 0, 1);                                                                                                  // NO NECESARIO
                                                                        // 10. Certificación de la atención medica de la víctima como prueba del accidente o evento.
                                $arr_campos_furipsEnc[$index_fact][79]  = obligatorioVal(79,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(fechaFurips($paciente["Ingfei"]), 0, 10),true);           // [Rq] Fecha ingreso
                                $arr_campos_furipsEnc[$index_fact][80]  = substr(horaFurips($paciente["Inghin"]), 0, 5);                                                                                    // Hora ingreso
                                $arr_campos_furipsEnc[$index_fact][81]  = obligatorioVal(81,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(fechaFurips($paciente["Ubifad"]), 0, 10),true);           // [Rq] Fecha egreso
                                $arr_campos_furipsEnc[$index_fact][82]  = substr(horaFurips($paciente["Ubihad"]), 0, 5);                                                                                    // Hora egreso
                                $arr_campos_furipsEnc[$index_fact][83]  = obligatorioVal(83,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($paciente["Ingdig"]), 0, 4),true);          // [Rq] Código diagnóstico principal ingreso
                                $arr_campos_furipsEnc[$index_fact][84]  = substr(limpiarString(""), 0, 4);                                                                                                  // Código diagnóstico 1 ingreso
                                $arr_campos_furipsEnc[$index_fact][85]  = substr(limpiarString(""), 0, 4);                                                                                                  // Código diagnóstico 2 ingreso

                                $arr_diag_egreso  = array("P"=>"","S1"=>"","S2"=>"");
                                $medico_principal = array(  "codigo_medico" => "",
                                                            "codigo"        => "",
                                                            "especialidad"  => "",
                                                            "tipo_doc"      => "",
                                                            "documento"     => "",
                                                            "nombre1"       => "",
                                                            "nombre2"       => "",
                                                            "apll1"         => "",
                                                            "apll2"         => "");
                                $medico_principal = consultar_diagnostico_medico_egreso($conex, $conexUnix, $wemp_pmla, $wbasedato_cliame, $wbasedato_movhos, $historia, $ingreso, $consulto_unix, $medico_principal, $arrInfo_historiaAyudas, $arr_diag_egreso, $paciente);

                                $arr_campos_furipsEnc[$index_fact][86]  = obligatorioVal(86,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($arr_diag_egreso["P"]), 0, 4),true);                // [Rq] Código diagnóstico principal egreso
                                $arr_campos_furipsEnc[$index_fact][87]  = substr(limpiarString($arr_diag_egreso["S1"]), 0, 4);                                                                                      // Código diagnóstico 1 egreso
                                $arr_campos_furipsEnc[$index_fact][88]  = substr(limpiarString($arr_diag_egreso["S2"]), 0, 4);                                                                                      // Código diagnóstico 2 egreso
                                                                        // 11. Datos del médico o profesional de la salud tratante.
                                $arr_campos_furipsEnc[$index_fact][89]  = obligatorioVal(89,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($medico_principal["apll1"]), 0, 20),true);          // [Rq] Apellido 1 médico
                                $arr_campos_furipsEnc[$index_fact][90]  = substr(limpiarString($medico_principal["apll2"]), 0, 30);             // Apellido 2 médico
                                $arr_campos_furipsEnc[$index_fact][91]  = obligatorioVal(91,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($medico_principal["nombre1"]), 0, 20),true);        // [Rq] Nombre 1 médico
                                $arr_campos_furipsEnc[$index_fact][92]  = substr(limpiarString($medico_principal["nombre2"]), 0, 30);           // Nombre 2 médico
                                $arr_campos_furipsEnc[$index_fact][93]  = obligatorioVal(93,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($medico_principal["tipo_doc"]), 0, 2),true);        // [Rq] Tipo documento identidad médico
                                $arr_campos_furipsEnc[$index_fact][94]  = obligatorioVal(94,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($medico_principal["documento"]), 0, 16),true);      // [Rq] Número documento médico
                                $arr_campos_furipsEnc[$index_fact][95]  = obligatorioVal(95,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($medico_principal["codigo"]), 0, 16),true);         // [Rq] Número regístro médico

                                // ********************************************************************************************************************************************
                                // ******************************                    FURIPS2 DETALLE DE FACTURA                                ********************************
                                // ********************************************************************************************************************************************
                                // No mostrar los cargos que el valor aceptado de la glosa sea cero
                                $sql_Dll = "SELECT  c274.id AS idg_dlle, c274.Gdecon AS cod_concepto, c274.Gdecre AS cantidad_glosada, c274.Gdecod AS cum_cups,
                                                    ((c274.Gdevgl - c274.Gdevac)/c274.Gdecre) AS valor_unitario, (c274.Gdevgl - c274.Gdevac) AS total_glosado, (c274.Gdevgl - c274.Gdevac) AS total_fosyga, c274.id AS idDetalle
                                            FROM    {$wbasedato_cliame}_000274 AS c274
                                            WHERE   c274.Gdeidg = '{$id_enc}'
                                                    AND (CAST(c274.Gdevgl as SIGNED) - CAST(c274.Gdevac as SIGNED)) > 0
                                                    AND c274.Gdeest = 'on'";

                                $contador_folios_dll = $num_facturas;
                                $total_glosado       = 0;
                                $total_fosyga        = 0;
                                $existe_detalle      = false;
                                if($result_Dll = mysql_query($sql_Dll,$conex))
                                {
                                    while ($row_dll = mysql_fetch_assoc($result_Dll))
                                    {
                                        $existe_detalle     = true;
                                        $nombre_cargo       = "";
                                        $codigo_cum         = "";
                                        $tipo_concepto      = "";
                                        $concepto_inv       = "";
                                        $nombre_concepto    = "";
                                        $grupoQx            = "";
                                        $idDetalle          = $row_dll["idDetalle"];
                                        $cod_concepto       = $row_dll["cod_concepto"];
                                        $cod_concepto_unx   = $row_dll["cod_concepto"];
                                        $codigo_cargo       = $row_dll["cum_cups"];
                                        $codigo_cargoArch   = $codigo_cargo;
                                        $index_fact_detalle = $index_fact;
                                        $falta_invima       = false;

                                        //Convertir concepto unix a concepto matrix
                                        if(array_key_exists($cod_concepto, $arrayConceptosUnxMx))
                                        {
                                            $cod_concepto    = $arrayConceptosUnxMx[$cod_concepto]["concepto_mx"];
                                            $tipo_concepto   = $arrayConceptosUnxMx[$cod_concepto]["tipo"];
                                            $concepto_inv    = $arrayConceptosUnxMx[$cod_concepto]["inventario"];
                                            $nombre_concepto = $arrayConceptosUnxMx[$cod_concepto]["nombre_mx"];
                                        }

                                        if(!array_key_exists($index_fact_detalle, $arr_campos_furipsDll))
                                        {
                                            $arr_campos_furipsDll[$index_fact_detalle] = array();
                                        }

                                        if(!array_key_exists($idDetalle, $arr_campos_furipsDll[$index_fact_detalle]))
                                        {
                                            $arr_campos_furipsDll[$index_fact_detalle][$idDetalle] = array();
                                        }

                                        if(($wFarmacia == 'on' && $concepto_inv == 'on' )
                                            || ($cod_concepto == $concepto_medicamentos || $cod_concepto == $concepto_materiales))
                                        {
                                            $nombre_cargo     = (array_key_exists($codigo_cargo, $arrayArticulo)) ? $arrayArticulo[$codigo_cargo]["nombre_gen"]:"";
                                            $codigo_cum       = (array_key_exists($codigo_cargo, $arrayArticulo)) ? $arrayArticulo[$codigo_cargo]["codigo_cum"]:"";

                                            if($nombre_cargo == 'NO APLICA' || $nombre_cargo == '.'){
                                                $nombre_cargo = '';
                                            }

                                            // Se intentan eliminar los ceros a la izquierda de una serie de números que componen el código CUM
                                            if($codigo_cum != '')
                                            {
                                                $expl_codigo_cum = explode("-", $codigo_cum);
                                                foreach ($expl_codigo_cum as $key => $value)
                                                {
                                                    $expl_codigo_cum[$key] = (is_numeric($value)) ? ($value*1): $value;
                                                }
                                                $codigo_cum = implode("-", $expl_codigo_cum);
                                            }
                                            // SI ES MEDICAMENTO BUSCAR EL CODIGO INVIMA PARA COLOCARLO EN EL CAMPO CÓDIGO DEL ARCHIVO
                                            $codigo_cargoArch = ($codigo_cum != '') ? $codigo_cum: '';

                                            if($cod_concepto == $concepto_medicamentos && $codigo_cargoArch == '')
                                            {
                                                $codigo_cargoArch = 'NO_INVIMA';
                                                $falta_invima     = true;
                                            }
                                        }
                                        else
                                        {
                                            $nombre_cargo = "";
                                            $codigo_cargo_unx = $codigo_cargo;
                                            $codigo_cargo_cups= "";
                                            // Si el código del procedimiento está en el maestro de procedimientos, el procedimiento está en el array de relación empresas procedimiento
                                            // y además es grupo quirúrgico entonces usar el código SOAT como código de procedimiento, el nombre debe ser el nombre de concepto con el grupo Qx.
                                            if(!array_key_exists($codigo_cargo, $arrayProcedimientos))
                                            {
                                                // Consultar si el código del procedimiento se encuentra directamente como código propio (Código SOAT) y homologarlo a un CUPS.
                                                // para que más adelante se pueda encontrar el tipo de facturación.
                                                $sqlCodHomo = " SELECT  c70.Proempnom, c103.Pronom, c70.Proemptfa AS tipoFac, c70.Proemppun AS numUvr, c103.Procod AS cod_cups
                                                                FROM    {$wbasedato_cliame}_000070 AS c70
                                                                        INNER JOIN
                                                                        {$wbasedato_cliame}_000103 AS c103 ON(c70.Proempcod = c103.Procod)
                                                                WHERE   c70.Proemppro  = '{$codigo_cargo}'
                                                                        AND c70.Proempemp = '{$wentidad_respuesta}'
                                                                        AND c70.Proempest = 'on'";

                                                if($resCodHomo = mysql_query($sqlCodHomo, $conex))
                                                {
                                                    while($rowCodHomo = mysql_fetch_array($resCodHomo))
                                                    {
                                                        $codigo_cargo = $rowCodHomo['cod_cups'];
                                                    }
                                                }
                                                else
                                                {
                                                    echo "Error al intentar consultar código CUPS: ".mysql_error().PHP_EOL.$sqlCodHomo;
                                                    exit();
                                                }
                                            }

                                            // Sigue para verificar el tipo de facturación
                                            if(!$consulto_unix && array_key_exists($codigo_cargo, $arrayProcedimientos))
                                            {
                                                $nombre_cargo = $arrayProcedimientos[$codigo_cargo]["nombre"];
                                                if(($tipo_concepto == 'C') && array_key_exists($codigo_cargo, $arrayProcedimientosEmpresa)
                                                    && $arrayProcedimientosEmpresa[$codigo_cargo]["tipo_facturacion"] == 'GQX') //$naturaleza_evento == '01'
                                                {
                                                    $nombre_concepto = validar_nombre_concepto_mat_med($cod_concepto, $nombre_concepto);

                                                    $grupoQx          = $arrayProcedimientos[$codigo_cargo]["Progqx"];
                                                    $nombre_cargo     = substr(limpiarString($nombre_concepto), 0, 31).' GRUPO '.$grupoQx;
                                                    $codigo_cargoArch = $arrayProcedimientosEmpresa[$codigo_cargo]["codigo_propio"];
                                                }
                                                else
                                                {
                                                    $codigo_cargoArch = (array_key_exists($codigo_cargo,$arrayProcedimientosEmpresa)) ? $arrayProcedimientosEmpresa[$codigo_cargo]["codigo_propio"]: $codigo_cargoArch;
                                                }
                                            }
                                            else
                                            {
                                                // Si finalmente no encontró el procedimiento en matrix entonces hace la busqueda en unix.
                                                if($consulto_unix)
                                                {
                                                    $arr_tipo_fac_unix = obtenerTipoFacUnix($conexUnix, $cod_concepto_unx, $codigo_cargo_unx, $wentidad_respuesta);
                                                    $nombre_cargo      = $arr_tipo_fac_unix["nombre_pro_exa"];

                                                    if(($tipo_concepto == 'C' || $arr_tipo_fac_unix['cod_nom_grupo']) && $arr_tipo_fac_unix['tipoLiquidacion'] == 'GQX')
                                                    {
                                                        $nombre_concepto = validar_nombre_concepto_mat_med($codigo_cargo_unx, $nombre_concepto);

                                                        // $grupoQx          = $arr_tipo_fac_unix["grupoQuirurgico"];
                                                        // $nombre_cargo     = substr(limpiarString($nombre_concepto), 0, 31).' GRUPO '.$grupoQx;
                                                        $codigo_cargoArch = ($arr_tipo_fac_unix["codigo_propio"] != '') ? $arr_tipo_fac_unix["codigo_propio"] : $codigo_cargoArch;
                                                        if($arr_tipo_fac_unix['codigo_grupo_qx'] != '')
                                                        {
                                                            $codigo_cargoArch = $arr_tipo_fac_unix['codigo_grupo_qx'];
                                                            $nombre_cargo     = $arr_tipo_fac_unix['nombre_grupo_qx'];
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $codigo_cargoArch = ($arr_tipo_fac_unix["codigo_propio"] != '') ? $arr_tipo_fac_unix["codigo_propio"] : $codigo_cargoArch;
                                                    }
                                                }
                                                else
                                                {
                                                    // seguimiento("arr_campos_furipsDll:".print_r("??",true));
                                                    $arr_err["error_msj"][] = "Código de procedimiento [$codigo_cargo] no existe en el maestro, (concepto: $cod_concepto)";
                                                }
                                            }
                                        }

                                        $cantidad_glosada_dll = limpiarString($row_dll["cantidad_glosada"]);
                                        $valor_unitario_dll   = limpiarString($row_dll["valor_unitario"]);
                                        $total_glosado_dll    = limpiarString($row_dll["total_glosado"]);
                                        $total_fosyga_dll     = limpiarString($row_dll["total_fosyga"]);

                                        $tipo_servicio = ($codigo_cargoArch == '' || $codigo_cargoArch == '0') ? '5' : '2'; // Lo que no tenga código debe ser por defecto tipo servicio 5
                                        if($codigo_cargoArch != '' && in_array($cod_concepto, $furips_medicamentos))
                                        {
                                            $tipo_servicio = '1';
                                        }
                                        elseif(in_array($cod_concepto, $furips_otros_servicios))
                                        {
                                            $tipo_servicio = '5';
                                        }

                                        $req_codigo = true;
                                        if($tipo_servicio == '5')
                                        {
                                            // $codigo_cargoArch = "";
                                            $req_codigo = false;
                                        }
                                        else
                                        {
                                            // $nombre_cargo = "";
                                        }

                                        if($falta_invima)
                                        {
                                            $req_codigo = true;
                                        }

                                                                              // 1. Datos de la reclamación.
                                        $arr_campos_furipsDll[$index_fact_detalle][$idDetalle][1] = obligatorioVal(1,'',$arr_descArch,$arr_alert,'dlle',$num_factura,substr($num_factura_archivo, 0, 20),true);                            // [Rq] Número de factura
                                        $arr_campos_furipsDll[$index_fact_detalle][$idDetalle][2] = obligatorioVal(2,'',$arr_descArch,$arr_alert,'dlle',$num_factura,substr($consec_reclamacion, 0, 12),true);                             // [Rq] Número de consecutivo
                                                                              // 2. Factura o Cuenta de cobro.
                                        $arr_campos_furipsDll[$index_fact_detalle][$idDetalle][3] = obligatorioVal(3,'',$arr_descArch,$arr_alert,'dlle',$num_factura,substr(limpiarString($tipo_servicio), 0, 1),true);                    // [PENDIENTE] [Rq] Tipo de servicio

                                        $arr_campos_furipsDll[$index_fact_detalle][$idDetalle][4] = obligatorioVal(4,'',$arr_descArch,$arr_alert,'dlle',$num_factura,substr($codigo_cargoArch, 0, 15),$req_codigo);                        // [PENDIENTE] [Rq] Código de servicio
                                        $arr_campos_furipsDll[$index_fact_detalle][$idDetalle][5] = substr(limpiarString($nombre_cargo), 0, 40);                                                                                           // [PENDIENTE] Descripción del insumo
                                        $arr_campos_furipsDll[$index_fact_detalle][$idDetalle][6] = obligatorioVal(6,'',$arr_descArch,$arr_alert,'dlle',$num_factura,substr(limpiarString($cantidad_glosada_dll), 0, 15),true);            // [PENDIENTE] [Rq] Cantidad de servicios, > 0
                                        $arr_campos_furipsDll[$index_fact_detalle][$idDetalle][7] = obligatorioVal(7,'I',$arr_descArch,$arr_alert,'dlle',$num_factura,substr(limpiarString($valor_unitario_dll), 0, 15),true);             // [PENDIENTE] [Rq] valor unitario, no separadores de miles, no decimales, > 0
                                        $arr_campos_furipsDll[$index_fact_detalle][$idDetalle][8] = obligatorioVal(8,'I',$arr_descArch,$arr_alert,'dlle',$num_factura,substr(limpiarString($total_glosado_dll), 0, 15),true);              // [PENDIENTE] [Rq] valor total facturado, no separadores de miles, no decimales, > 0
                                        $arr_campos_furipsDll[$index_fact_detalle][$idDetalle][9] = obligatorioVal(9,'I',$arr_descArch,$arr_alert,'dlle',$num_factura,substr(limpiarString($total_fosyga_dll), 0, 15),true);               // [PENDIENTE] [Rq] valor total reclamado al fosyga, no separadores de miles, no decimales, > 0

                                        if(array_key_exists($num_factura, $arr_alert))
                                        {
                                            if(!array_key_exists($num_factura, $arr_his_ing_nogen))
                                            {
                                                $arr_his_ing_nogen[$num_factura] = array("historia"=>$historia,"ingreso"=>$ingreso);
                                            }
                                        }

                                        // seguimiento("arr_campos_furipsDll:".print_r($arr_campos_furipsDll[$index_fact_detalle][$idDetalle],true));
                                        $total_glosado = $total_glosado+$total_glosado_dll;
                                        $total_fosyga  = $total_fosyga+$total_fosyga_dll;
                                    }
                                }
                                else
                                {
                                    $data['mensaje'] = "No se puedo realizar la consulta detalle";
                                    $data['error']   = 1;
                                    $data['sql']     .= $sql_Dll." > ".mysql_error().PHP_EOL;
                                }
                                                                        // 12. Amparos que reclama.
                                $arr_campos_furipsEnc[$index_fact][96]  = obligatorioVal(96,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($total_glosado), 0, 15),true);                                             // [PENDIENTE] [Rq] Total facturado por amparo de gastos médicos quirúrgicos, debe ser mayor a cero
                                $arr_campos_furipsEnc[$index_fact][97]  = obligatorioVal(97,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString($total_fosyga), 0, 15),true);                                              // [PENDIENTE] [Rq] Total reclamado por amparo de gastos médicos quirúrgicos, debe ser mayor a cero
                                $arr_campos_furipsEnc[$index_fact][98]  = obligatorioVal(98,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString("0"), 0, 15),false);                                                       // [PENDIENTE] [Rq] Total facturado por amparo de gastos de transporte y movilización de la víctima, > 0
                                $arr_campos_furipsEnc[$index_fact][99]  = obligatorioVal(99,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr(limpiarString("0"), 0, 15),false);                                                       // [PENDIENTE] [Rq] Total reclamado por amparo de gastos de transporte y movilización de la víctima, > 0
                                                                        // 13 Folios.
                                $arr_campos_furipsEnc[$index_fact][100] = obligatorioVal(100,'',$arr_descArch,$arr_alert,'enc',$num_factura,substr($contador_folios_dll, 0, 3),true);                                                      // [PENDIENTE] [Rq] Total folios >> EN TODOS DEBE SER LA CANTIDAD DE FACTURAS GENERADAS EN EL ARCHIVO FURIPS1
                            }
                            else
                            {
                                $num_facturas_no_generadas++;
                                $idx_his_ing = $historia.'_'.$ingreso;
                                if(!array_key_exists($idx_his_ing,$arr_no_generadas_err))
                                {
                                    $arr_no_generadas_err[$idx_his_ing] = array();
                                }

                                $arr_no_generadas_err[$idx_his_ing][] = $num_factura;
                            }
                        }
                        // else
                        // {
                        //     $data['mensaje'] = "No se pudo consultar historia-ingreso del paciente en Matrix";
                        //     $data['error']   = 1;
                        //     $data['sql']     .= $sql_pac." > ".mysql_error().PHP_EOL;
                        // }
                    }

                    if($conectado_a_unix)
                    {
                        odbc_close($conexUnix);
                        odbc_close_all();
                    }

                    if($num_facturas_no_generadas > 0)
                    {
                        foreach ($arr_campos_furipsEnc as &$value) {
                            $value[100] = $value[100] - $num_facturas_no_generadas;
                        }
                    }

                    $arr_archivos_generados = array();
                    // $consecutivo_archivo_plano    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'consecutivo_planos_furips_glosa');
                    $nombreFurips1Prefijo = "FURIPS1".$codigo_habilitacion.date("dmY");
                    $nombreFurips2Prefijo = "FURIPS2".$codigo_habilitacion.date("dmY");
                    $arr_actualiza_estado_consec = array();

                    // $dir_nombreFurips1 = $dir.'/'.$nombreFurips1;
                    // $dir_nombreFurips2 = $dir.'/'.$nombreFurips2;
                    if(count($arr_campos_furipsEnc) > 0)
                    {
                        // echo "<pre>".print_r($arr_campos_furipsEnc,true)."</pre>";
                        // echo "<pre>".print_r($arr_campos_furipsDll,true)."</pre>";
                        $data["facturas_encontradas"] = count($arr_campos_furipsEnc);

                        // $consecutivos_generados_arch = array();
                        $facturas_archivo            = 0;
                        $contador_archivos           = 1;
                        $consecutivo_archivo_plano   = ($generar_consecutivo_archivo == 'on') ? consultarAliasPorAplicacion($conex, $wemp_pmla, 'consecutivo_planos_furips_glosa') : $contador_archivos.'-'.date("YmdHis").'-tmp'; // Se lee el nuevo consecutivo de archivo
                        if($generar_consecutivo_archivo == 'on')
                        {
                            actualizarConsecutivoArchivoPlanoFuripsGlosas($conex, $wemp_pmla);
                        }

                        $nombreFurips1 = $nombreFurips1Prefijo.'-'.$consecutivo_archivo_plano;
                        $nombreFurips2 = $nombreFurips2Prefijo.'-'.$consecutivo_archivo_plano;

                        $dir_nombreFurips1 = $dir.'/'.$nombreFurips1.'.txt';
                        $dir_nombreFurips2 = $dir.'/'.$nombreFurips2.'.txt';

                        if(file_exists($dir_nombreFurips1))
                        {
                            unlink($dir_nombreFurips1);
                        }

                        if(file_exists($dir_nombreFurips2))
                        {
                            unlink($dir_nombreFurips2);
                        }

                        $arr_archivos_generados[$consecutivo_archivo_plano] = array("furips1"=>array("nombre"=>$nombreFurips1,"url"=>$dir_nombreFurips1),
                                                                                    "furips2"=>array("nombre"=>$nombreFurips2,"url"=>$dir_nombreFurips2));

                        $fpEnc = fopen($dir_nombreFurips1,"a+");
                        $fpDll = fopen($dir_nombreFurips2,"a+");

                        foreach ($arr_campos_furipsEnc as $idx_enc_factura => $arr_encabezado)
                        {
                            // Si el número de facturas guardadas en el archivo llegan a ser iguales al límite máximo de facturas posibles por archivo,
                            // entonces se cierra el archivo y se abre uno nuevo con un nuevo número de consecutivo
                            // echo "$facturas_archivo == $limite_enc_facturas_plano_furips";
                            if($facturas_archivo == $limite_enc_facturas_plano_furips)
                            {
                                fclose($fpEnc);
                                fclose($fpDll);
                                $facturas_archivo = 0;
                                if($generar_consecutivo_archivo == 'on')
                                {
                                    actualizarConsecutivoArchivoPlanoFuripsGlosas($conex, $wemp_pmla);
                                }

                                $consecutivo_archivo_plano   = ($generar_consecutivo_archivo == 'on') ? consultarAliasPorAplicacion($conex, $wemp_pmla, 'consecutivo_planos_furips_glosa') : $contador_archivos.'-'.date("YmdHis").'-tmp'; // Se lee el nuevo consecutivo de archivo

                                $nombreFurips1 = $nombreFurips1Prefijo.'-'.$consecutivo_archivo_plano;
                                $nombreFurips2 = $nombreFurips2Prefijo.'-'.$consecutivo_archivo_plano;

                                $dir_nombreFurips1 = $dir.'/'.$nombreFurips1.'.txt';
                                $dir_nombreFurips2 = $dir.'/'.$nombreFurips2.'.txt';

                                $arr_archivos_generados[$consecutivo_archivo_plano] = array("furips1"=>array("nombre"=>$nombreFurips1,"url"=>$dir_nombreFurips1),
                                                                                            "furips2"=>array("nombre"=>$nombreFurips2,"url"=>$dir_nombreFurips2));

                                $fpEnc = fopen($dir_nombreFurips1,"a+");
                                $fpDll = fopen($dir_nombreFurips2,"a+");
                            }

                            //Originalmente en este campo se guardó el id del encabezado de glosa de la factura, pero se debe actualizar
                            //para que en el archivo plano en ese campo quede el número de consecutivo de generación de archivo, más adelante también
                            //se actualiza el campo [2] del detalle con este mismo número de consecutivo.
                            // $consecutivos_generados_arch[] = $arr_encabezado[4];
                            $arr_encabezado[4]             = $consecutivo_archivo_plano;
                            $linea_encabezado              = implode(",", $arr_encabezado);
                            fwrite($fpEnc, $linea_encabezado."\r\n");

                            foreach ($arr_campos_furipsDll[$idx_enc_factura] as $id_detalle => $arr_detalle)
                            {
                                $arr_detalle[2] = $consecutivo_archivo_plano;
                                $linea_detalle = implode(",", $arr_detalle);
                                fwrite($fpDll, $linea_detalle."\r\n");
                            }

                            if($generar_consecutivo_archivo == 'on' && !in_array($idx_enc_factura, $arr_ids_generados))
                            {
                                $arr_ids_generados[] = $idx_enc_factura;
                            }

                            if(!array_key_exists($consecutivo_archivo_plano, $arr_actualiza_estado_consec))
                            {
                                $arr_actualiza_estado_consec[$consecutivo_archivo_plano] = array();
                            }
                            // echo $consecutivo_archivo_plano.PHP_EOL;
                            $arr_actualiza_estado_consec[$consecutivo_archivo_plano][] = $idx_enc_factura;
                            // print_r($arr_actualiza_estado_consec);
                            // fclose($fpDll);

                            $facturas_archivo++;
                            $contador_archivos++;
                        }

                        if($fpEnc === true)
                        {
                            fclose($fpEnc);
                        }
                        if($fpEnc === true)
                        {
                            fclose($fpDll);
                        }

                        /*if(file_exists($dir_nombreFurips2))
                        {
                            unlink($dir_nombreFurips2);
                        }
                        $fpDll = fopen($dir_nombreFurips2,"a+");
                        foreach ($arr_campos_furipsDll as $num_radicado_num_factura => $arr_ids_detalle)
                        {
                            foreach ($arr_ids_detalle as $id_detalle => $arr_detalle)
                            {
                                $linea_detalle = implode(",", $arr_detalle);
                                fwrite($fpDll, $linea_detalle."\r\n");
                            }
                        }
                        fclose($fpDll);*/

                        if(count($arr_actualiza_estado_consec) > 0)
                        {
                            $fecha_hora_actual = $fecha_actual.' '.$hora_actual;
                            // $ids_enc_generados = implode("','", $consecutivos_generados_arch);

                            $pendientes_generar = false;
                            if($generar_consecutivo_archivo == 'on')
                            {
                                foreach ($arr_actualiza_estado_consec as $consec_archivo => $arr_idx_enc_factura)
                                {
                                    $ids_enc_generados = implode("','", $arr_idx_enc_factura);
                                    $sql_UdtEnc = "UPDATE {$wbasedato_cliame}_000273 SET Gloesg='AP', Glocar='{$consec_archivo}', Glouga = '{$user_session}', Glofga = '{$fecha_hora_actual}' WHERE id IN ('{$ids_enc_generados}')";
                                    if($result_UdtEnc = mysql_query($sql_UdtEnc,$conex))
                                    {
                                        $pendientes_generar = true;
                                    }
                                }
                            }
                            else
                            {
                                $pendientes_generar = true;
                            }

                            if($pendientes_generar)
                            {
                                // Se vuelve a ajecutar el query inicial para saber cuántas facturas faltan por generar. pero sin LIMIT
                                $sql_EncCount = queryEncabezadoFacturas($conex, $wbasedato_cliame, $filtrar_fecha, $fecha_inicio_rep, $fecha_final_rep, $wentidad_respuesta, $limite_enc_facturas_plano_furips, 'GR', $westado_cartera, $wFarmacia, false, '', $filtrar_encabezados);
                                if($result_EncCount = mysql_query($sql_EncCount,$conex))
                                {
                                    $data["faltantes_por_generar"] = mysql_num_rows($result_EncCount);
                                    if($data["faltantes_por_generar"]*1 > 0)
                                    {
                                        $data["pendientes_html"] = '<tr class="fila2">
                                                                        <td>Facturas generadas ['.$data["facturas_encontradas"].']</td>
                                                                    </tr>
                                                                    <tr class="fila2">
                                                                        <td>
                                                                            Quedan pendientes ['.$data["faltantes_por_generar"].'] facturas por generar.
                                                                        </td>
                                                                    </tr>';
                                    }
                                    else
                                    {
                                        $data["pendientes_html"] = '<tr class="fila2">
                                                                        <td>
                                                                            Todas las facturas fueron generadas en el archivo
                                                                        </td>
                                                                    </tr>';
                                    }
                                }
                            }
                        }

                        foreach ($arr_archivos_generados as $consecutivo_archivo => $arr_furips)
                        {
                            $nombreFurips_link .= ' <div class="fila1" onmouseover="trOver(this);" onmouseout="trOut(this);" style=""><a href="'.$arr_furips["furips1"]["url"].'" download>'.$arr_furips["furips1"]["nombre"].'</a></div>
                                                    <div class="fila2" onmouseover="trOver(this);" onmouseout="trOut(this);" style=""><a href="'.$arr_furips["furips2"]["url"].'" download>'.$arr_furips["furips2"]["nombre"].'</a></div>
                                                    <hr>';
                        }
                    }
                    else
                    {
                        //
                    }

                    $html_alertas_tr = "";
                    if(count($arr_alert) > 0)
                    {
                        // echo "<pre>".print_r($arr_alert,true)."</pre>";
                        $cont=0;
                        foreach ($arr_alert as $cod_factura => $arr_alertas_tipo) {
                            $cont++;
                            $cssTb = ($cont%2 == 0) ? 'fila1': 'fila2';
                            $arr_alertas_enc  = (array_key_exists("enc", $arr_alertas_tipo)) ? $arr_alertas_tipo["enc"] : array();
                            $arr_alertas_dlle = (array_key_exists("dlle", $arr_alertas_tipo)) ? $arr_alertas_tipo["dlle"] : array();
                            $historia = (array_key_exists($cod_factura, $arr_his_ing_nogen)) ? $arr_his_ing_nogen[$cod_factura]["historia"]: "";
                            $ingreso = (array_key_exists($cod_factura, $arr_his_ing_nogen)) ? $arr_his_ing_nogen[$cod_factura]["ingreso"]: "";

                            $his_ing_fac = "";
                            if($historia != '')
                            {
                                $his_ing_fac = " [{$historia}-{$ingreso}]";
                            }

                            // $tot_alertas = count($arr_alertas_enc)+count($arr_alertas_dlle);

                            $html_alert_enc = "";
                            if(count($arr_alertas_enc)>0)
                            {
                                if(count($arr_alertas_enc) > 3)
                                {
                                    $html_alert_enc = "<ul><li>".implode("</li><li>", array_slice($arr_alertas_enc, 0 ,3)).'...<span style="font-weight:bold;color:blue;cursor:pointer;" onmouseover="trOver(this);" onmouseout="trOut(this);" onclick="ver_mas_alertas(\'alerta_enc_'.$cod_factura.'\')">Ver más...</span></li></ul>';
                                    $html_alert_enc .= '<div id="alerta_enc_'.$cod_factura.'" style="display:none;"><ul><li>'.implode("</li><li>", $arr_alertas_enc).'</li></ul></div>';
                                }
                                else
                                {
                                    $html_alert_enc = '<div id="alerta_enc_'.$cod_factura.'" style=""><ul><li>'.implode("</li><li>", $arr_alertas_enc).'</li></ul></div>';
                                }

                                $html_alert_enc = '<div style="background-color: #ffffcc">Encabezado FURIPS-1:</div>'.$html_alert_enc;
                            }

                            $html_alert_dlle = "";
                            if(count($arr_alertas_dlle)>0)
                            {
                                if(count($arr_alertas_dlle) > 3)
                                {
                                    $html_alert_dlle = "<ul><li>".implode("</li><li>", array_slice($arr_alertas_dlle, 0 ,3)).'...<span style="font-weight:bold;color:blue;cursor:pointer;" onmouseover="trOver(this);" onmouseout="trOut(this);" onclick="ver_mas_alertas(\'alerta_dlle_'.$cod_factura.'\')">Ver más...</span></li></ul>';
                                    $html_alert_dlle .= '<div id="alerta_dlle_'.$cod_factura.'" style="display:none;"><ul><li>'.implode("</li><li>", $arr_alertas_dlle).'</li></ul></div>';
                                }
                                else
                                {
                                    $html_alert_dlle = '<div id="alerta_dlle_'.$cod_factura.'" style=""><ul><li>'.implode("</li><li>", $arr_alertas_dlle).'</li></ul></div>';
                                }

                                $html_alert_dlle = '<div style="background-color: #ffffcc">Detalle FURIPS-2:</div>'.$html_alert_dlle;
                            }

                            $html_alertas_tr .= '  <tr class="'.$cssTb.'">
                                                        <td>'.$cod_factura.$his_ing_fac.'</td>
                                                        <td>'.$html_alert_enc.$html_alert_dlle.'</td>
                                                    </tr>';
                        }

                        $html_alertas_tr = '  <tr class="fila2" >
                                                <td>
                                                    Alertas <span style="font-weight:bold; color:red;cursor:pointer;" onclick="fnModalInconsistencias(\'div_alertas\');"  onmouseover="trOver(this);" onmouseout="trOut(this);">[clic para ver]</span>
                                                    <div id="div_alertas" style="display:none;">
                                                        <table width="700" align="center">
                                                            <tr class="encabezadoTabla">
                                                                <td colspan="2">Listado de campos por factura que en archivo de encabezado o detalle están vacíos y son obligatorios.</td>
                                                            </tr>
                                                            <tr class="encabezadoTabla">
                                                                <td>Número de factura</td>
                                                                <td>Campos obligatorios archivos planos, FURIPS-1 y FURIPS-2</td>
                                                            </tr>
                                                            '.$html_alertas_tr.'
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>';
                    }

                    if(count($arr_no_generadas_err) > 0)
                    {
                        $cont=0;
                        $html_no_generadas_tr = "";
                        foreach ($arr_no_generadas_err as $his_ing => $arr_facts) {
                            $cont++;
                            $cssTb = ($cont%2 == 0) ? 'fila1': 'fila2';
                            $expl_his_ing = explode("_", $his_ing);
                            $historia_no_generada = $expl_his_ing[0];
                            $ingreso_no_generado = $expl_his_ing[1];
                            $html_no_generadas_tr .= '  <tr class="'.$cssTb.'">
                                                                <td>'.$historia_no_generada.'-'.$ingreso_no_generado.'</td>
                                                                <td>'.implode(", ", $arr_facts).'</td>
                                                            </tr>';
                        }
                        $data["html_no_generadas"] = '  <tr class="fila2" id="tr_inconsistencias">
                                                            <td>
                                                                Inconsistencias <span style="font-weight:bold; color:red;cursor:pointer;" onclick="fnModalInconsistencias(\'div_inconsistencias\');"  onmouseover="trOver(this);" onmouseout="trOut(this);">[clic para ver]</span>
                                                                <div id="div_inconsistencias" style="display:none;">
                                                                    <table width="700" align="center">
                                                                        <tr class="encabezadoTabla">
                                                                            <td colspan="2">Historias-ingresos, facturadas no generadas en el archivo plano por admisión incompleta o inexistente.</td>
                                                                        </tr>
                                                                        <tr class="encabezadoTabla">
                                                                            <td>Historia-ingreso</td>
                                                                            <td>Facturas no generadas en archivo plano.</td>
                                                                        </tr>
                                                                        '.$html_no_generadas_tr.'
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                    }

                    $html_error_codigos = '';
                    if(count($arr_err["error_msj"]) > 0)
                    {
                        $li_srr = '<ul>';
                        foreach ($arr_err["error_msj"] as $key => $value_err)
                        {
                            $li_srr .= '<li>'.$value_err.'</li>';
                        }
                        $li_srr .= '</ul>';

                        $html_error_codigos = ' <tr class="fila2" id="tr_inconsistencias">
                                                    <td>
                                                        Códigos no encontrados en el maestro <span style="font-weight:bold; color:red;cursor:pointer;" onclick="fnModalInconsistencias(\'div_error_codigos\');"  onmouseover="trOver(this);" onmouseout="trOut(this);">[clic para ver]</span>
                                                        <div id="div_error_codigos" style="display:none;">
                                                            <table width="700" align="center">
                                                                <tr class="">
                                                                    <td>
                                                                        '.$li_srr.'
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>';
                    }

                    if($data["pendientes_html"] != '' || $data["html_no_generadas"] != '' || $html_alertas_tr != '' || $html_error_codigos != '')
                    {
                        $data["notificaciones_html"] = '<br><br>
                                                        <div style="text-align:left;font-weight:bold; background-color: #ffffcc;">
                                                            <table width="80%" id="tbl_descargables_msjs" align="center">
                                                                <tr class="encabezadoTabla">
                                                                    <td colspan="2" style="text-align: center;">Notificaciones</td>
                                                                </tr>
                                                                '.$data["pendientes_html"].'
                                                                '.$data["html_no_generadas"].'
                                                                '.$html_error_codigos.'
                                                                '.$html_alertas_tr.'
                                                            </table>
                                                        </div>';
                    }
                }
                else
                {
                    $data['mensaje'] = "No se puedo realizar la consulta";
                    $data['error']   = 1;
                    $data['sql']     .= $sql_Enc." > ".mysql_error().PHP_EOL;
                }

                $data["arr_ids_generados"] = $arr_ids_generados;
                $data["html"]              = $nombreFurips_link;
                $data["arr_err"]           = $arr_err;
            break;
        case 'ajax_buscar_factura_ap':
                $arr_facturas_listadas = json_decode(str_replace("\\", '',  $arr_facturas_listadas), TRUE);
                $estado_encabezado = 'AP';
                $sql_Enc_AP = queryEncabezadoFacturas($conex, $wbasedato_cliame, $filtrar_fecha, $fecha_inicio_rep, $fecha_final_rep, $wentidad_respuesta, $limite_enc_facturas_plano_furips, $estado_encabezado, $westado_cartera, $wFarmacia, false, $num_factura_buscar);
                $tr_facts = "";
                if($result_Enc_AP = mysql_query($sql_Enc_AP,$conex))
                {
                    //$facturas_GR = mysql_num_rows($result_Enc_AP);
                    while ($row = mysql_fetch_assoc($result_Enc_AP))
                    {
                        $id_273         = $row["consec_reclamacion"];
                        $num_factura    = $row["num_factura"];
                        $consec_archivo = $row["consec_archivo"]; // Número de consecutivo de archivo plano en que fue generado la última vez.
                        if(!array_key_exists($id_273, $arr_facturas_listadas))
                        {
                                                    // <input type="checkbox" id="ckfact_'.$id_273.'" value="'.$id_273.'" factura="'.$num_factura.'" estado_enc="'.$estado_encabezado.'" >'.$num_factura.' <span style="font-size:8pt;" >[Generada en archivo: #'.$consec_archivo.']</span>
                            $tr_facts    = ' <tr class="fila2">
                                                <td style="text-align:left;">
                                                    <span class="button-checkbox">
                                                        <button type="button" class="btn" data-color="primary" id="ckfact_'.$id_273.'" value="'.$id_273.'" factura="'.$num_factura.'" estado_enc="'.$estado_encabezado.'" onclick="seleccion_click_facturas(this);">'.trim($num_factura).' [Generada en archivo: #'.$consec_archivo.']</button>
                                                        <input type="checkbox" class="hidden" />
                                                    </span>
                                                </td>
                                            </tr>';
                        }
                    }
                }
                if($tr_facts != '')
                {
                    $data["html"] = $tr_facts;
                }
                else
                {
                    $data["error"] = 1;
                    $data["mensaje"] = "No se encontró un número de factura con los filtros que ha seleccionado o ya está en la lista";
                }
            break;
        case 'historial_ajax_archivos':
                $nombreFurips_link = '';
                $arr_histo_archivos = consultarPlanosConsecutivo($dir, $dias_cache_archivos_furips);
                if(count($arr_histo_archivos) > 0)
                {
                    $nombreFurips_link .= ' <div style="background-color:#FFFFCC;text-align:center;">Los archivos con más de '.$dias_cache_archivos_furips.' días de creados son borrados para liberar espacio en el sistema.</div>
                                            <hr>';
                    foreach ($arr_histo_archivos as $consecutivo_archivo => $arr_furips)
                    {
                        $nombreFurips_link .= ' <div class="fila1" onmouseover="trOver(this);" onmouseout="trOut(this);" style="">[Creado: '.$arr_furips["furips1"]["fecha_creado"].'] <a href="'.$arr_furips["furips1"]["url"].'" download>'.$arr_furips["furips1"]["nombre"].'</a></div>
                                                <div class="fila2" onmouseover="trOver(this);" onmouseout="trOut(this);" style="">[Creado: '.$arr_furips["furips2"]["fecha_creado"].'] <a href="'.$arr_furips["furips2"]["url"].'" download>'.$arr_furips["furips2"]["nombre"].'</a></div>
                                                <hr>';
                    }
                }
                if($nombreFurips_link == '')
                {
                    $nombreFurips_link = '<div class="fila1" onmouseover="trOver(this);" onmouseout="trOut(this);" style="font-weight:bold;text-align:center;" >No se encontraron archivos generados.</div>';
                }
                $data["html"] = $nombreFurips_link;
            break;
        case 'descargar_archivo':
               header("Content-type: text/plain");
               header("Content-Disposition: attachment; filename=".$url_archivo);
               exit();
            break;
        case 'consultar_entidades_farmacia':
                $arr_entidades_empresa = array();
                $sql_ent = "    SELECT  c24.Empcod, c24.Empnom
                                FROM    {$wbasedato_cliame}_000273 AS c273
                                        INNER JOIN
                                        {$wbasedato_empresa}_000024 AS c24 ON (c24.Empcod = c273.Gloent)
                                WHERE   c273.Gloest = 'on'
                                GROUP BY c24.Empcod, c24.Empnom
                                ORDER BY c24.Empnom";
                if($result_ent = mysql_query($sql_ent,$conex))
                {
                    while($row_ent = mysql_fetch_assoc($result_ent))
                    {
                        $arr_entidades_empresa[$row_ent["Empcod"]] = utf8_encode($row_ent["Empnom"]);
                    }
                } else {
                    $data["error"] = 1;
                    $data["mensaje"] = "Error consultando las empresas";
                }

                $data["arr_entidades_empresa"] = $arr_entidades_empresa;
                $data["sql_ent"] = $sql_ent;
            break;
        default :
            $data['mensaje'] = $no_exec_sub;
            $data['error'] = 1;
            break;
    }
    echo json_encode($data);
    return;
}
$consultaAjax = '';
include_once("root/comun.php");

$wbasedato_HCE       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wbasedato_movhos    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wbasedato_cliame    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$codigo_habilitacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigo_habilitacion');
$limite_enc_facturas_plano_furips = consultarAliasPorAplicacion($conex, $wemp_pmla, 'limite_enc_facturas_plano_furips');
$dias_cache_archivos_furips       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'dias_cache_archivos_furips');

$arr_entidades_empresa = array();
$arr_entidades = array();
$sql_ent = "   SELECT  c24.Empcod, c24.Empnom
                FROM    {$wbasedato_cliame}_000273 AS c273
                        INNER JOIN
                        {$wbasedato_cliame}_000024 AS c24 ON (c24.Empcod = c273.Gloent)
                WHERE   c273.Gloest = 'on'
                ORDER BY c24.Empnom";
if($result_ent = mysql_query($sql_ent,$conex))
{
    while($row_ent = mysql_fetch_assoc($result_ent))
    {
        $arr_entidades[$row_ent["Empcod"]] = utf8_encode($row_ent["Empnom"]);
    }
}

$arr_naturaleza_evento = array(
                                "01" => "Accidente de tránsito",
                                "02" => "Sismo",
                                "03" => "Maremoto",
                                "04" => "Erupción volcánica",
                                "05" => "Deslizamiento de tierra",
                                "06" => "Inundación",
                                "07" => "Avalancha",
                                "08" => "Incendio natural",
                                "09" => "Explosión terrorista",
                                "10" => "Incendio terrorista",
                                "11" => "Combate",
                                "12" => "Ataques a Municipios",
                                "13" => "Masacre",
                                "14" => "Desplazados",
                                "15" => "Mina antipersonal",
                                "16" => "Huracán",
                                "17" => "Otro",
                                "25" => "Rayo",
                                "26" => "Vendaval",
                                "27" => "Tornado",
                            );

$options_naturaleza_ev = array();
foreach ($arr_naturaleza_evento as $key => $value) {
    $sltd = ($key == '01') ?'selected="selected"' : '';
    $options_naturaleza_ev .= '<option value="'.$key.'" '.$sltd.'>'.$key.'-'.utf8_decode($value).'</option>';
}

$arr_estadoCartera = estadosCarteraFactura($conex, $wbasedato_cliame);

?>
<html lang="es-ES">
<head>
    <meta charset="UTF-8" />
    <title>Generar archivo plano FURIPS</title>

    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>

    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>

    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

    <script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
    <link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>

    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
    <script src="../../../include/root/toJson.js" type="text/javascript"></script>
    <link rel='stylesheet' href='../../../include/root/matrix.css'/>

    <script type="text/javascript">
        function mensajeFailAlert(mensaje, xhr, textStatus, errorThrown)
        {
            var msj_extra = '';
            msj_extra = (mensaje != '') ? "<br>"+mensaje: mensaje;
            jAlert($("#failJquery").val()+msj_extra, "Mensaje");
            $("#div_error_interno").html(xhr.responseText);
            // console.log(xhr);
            // jAlert("error interno: "+xhr.responseText, "Mensaje"); console.log("error");
            fnModalLoading_Cerrar();
            $(".bloquear_todo").removeAttr("disabled");
        }

        $(function(){
            // --> Activar Acordeones
            $("#accordionFiltros").accordion({
                heightStyle: "content"
            });

            $('.campo_autocomplete').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("codigo","");
                        $(this).attr("nombre","");
                    }
                    else
                    {
                        $(this).val($(this).attr("nombre"));
                    }
                }
            });

            $("#fecha_inicio_rep").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                buttonImageOnly: true,
                maxDate:"+0D",
                onSelect: function (date) {
                    var dt1 = $('#fecha_inicio_rep').datepicker('getDate');
                    var dt2 = $('#fecha_final_rep').datepicker('getDate');
                    if (dt1 > dt2) {
                        $('#fecha_final_rep').datepicker('setDate', dt1);
                    }
                    $('#fecha_final_rep').datepicker('option', 'minDate', dt1);
                }
            });

            $("#fecha_final_rep").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                buttonImageOnly: true,
                maxDate:"+0D",
                onClose: function () {
                    var dt1 = $('#fecha_inicio_rep').datepicker('getDate');
                    var dt2 = $('#fecha_final_rep').datepicker('getDate');
                    //check to prevent a user from entering a date below date of dt1
                    if (dt2 < dt1) {
                        var minDate = $('#fecha_final_rep').datepicker('option', 'minDate');
                        $('#fecha_final_rep').datepicker('setDate', minDate);
                    }
                }
            });
        });

        $(document).ready( function ()
        {
            crearAutocomplete('arr_entidades', 'wentidad_respuesta','','',0);
            crearAutocomplete('arr_westado_cartera', 'westado_cartera','','',0);
            // generarArchivosPlanos();
            var btn_cerrar_examen = $("#a_ver_historico_archivos");
            btn_cerrar_examen.click(function(){
                consultarHistorialArchivos();
            });
        });

        function generarArchivosPlanos(operacion)
        {
            $("#td_archivos_notificaciones").html("");
            $("div[id^=alerta_enc_").remove();
            $("div[id^=alerta_dlle_").remove();
            $("div[id^=div_inconsistencias").remove();
            $("#tr_inconsistencias").remove();
            $("#div_alertas").remove();
            $("#td_archivos").html("");
            $("#tbl_descargables").hide();

            if(operacion == 'listar_facturas')
            {
                $("#div_facts_seleccion").html("");
                $("#div_facts_seleccion").hide();
                $("#tr_contador_checks").hide();
            }

            $(".campoRequerido").removeClass("campoRequerido");
            var filtrar_fecha      = ($("#chk_filtar_fecha").is(":checked")) ? 'on': 'off';
            var wentidad_respuesta = $("#wentidad_respuesta").attr("codigo");
            var westado_cartera    = $("#westado_cartera").attr("codigo");
            var wnaturaleza_evento = $("#wnaturaleza_evento").val();
            var validacion_ok      = true;
            var generar_consecutivo_archivo = ($("#generar_consecutivo_archivo").is(":checked")) ? 'on': 'off';

            if(wentidad_respuesta == '')
            {
                validacion_ok = false;
                $("#wentidad_respuesta").addClass('campoRequerido');
                jAlert("Debe ingresar una entidad", "Mensaje");
            }

            /*if(westado_cartera == '')
            {
                validacion_ok = false;
                $("#westado_cartera").addClass('campoRequerido');
                jAlert("Debe ingresar el estado de cartera", "Mensaje");
            }*/

            if(validacion_ok)
            {
                var obJson                    = parametrosComunes();
                obJson['accion']              = 'validar_archivos_generados';
                obJson['codigo_habilitacion'] = $("#codigo_habilitacion").val();
                obJson['fecha_inicio_rep']    = $("#fecha_inicio_rep").val();
                obJson['fecha_final_rep']     = $("#fecha_final_rep").val();
                obJson['wentidad_respuesta']  = wentidad_respuesta;
                obJson['westado_cartera']     = westado_cartera;
                obJson['wnaturaleza_evento']  = wnaturaleza_evento;
                obJson['filtrar_fecha']       = filtrar_fecha;
                obJson['operacion']           = operacion;
                obJson['num_factura_buscar']  = '';
                obJson['limite_enc_facturas_plano_furips'] = $("#limite_enc_facturas_plano_furips").val();
                obJson['generar_consecutivo_archivo']      = generar_consecutivo_archivo;
                obJson['wFarmacia']                        = ($("#wFarmacia").is(":checked")) ? 'on':'off';

                if(operacion == 'listar_facturas')
                {
                    $(".bloquear_todo").attr("disabled","disabled");
                    fnModalLoading();
                    $.post("generar_planos_furips.php", obJson,
                        function(data){
                            if(data.error == 1)
                            {
                                fnModalLoading_Cerrar();
                                jAlert(data.mensaje, "Mensaje");
                                $(".bloquear_todo").removeAttr("disabled");
                            }
                            else
                            {
                                fnModalLoading_Cerrar();
                                $(".bloquear_todo").removeAttr("disabled");
                                // $("#tbl_descargables").show();
                                if(data.facturas_GR > 0  || (data.facturas_GR == 0 && data.facturas_AP == 0)) // generar_consecutivo_archivo == 'off' ||
                                {
                                    $("#div_facts_seleccion").html(data.seleccionar_facts_html);
                                    $("#div_facts_seleccion").show();
                                    $("#tr_contador_checks").show();
                                }
                                else if(data.facturas_GR == 0 && data.facturas_AP > 0)
                                {
                                    // Cuando ya han sido generados todos los cargos al archivo plano y se quiere confirmar que se generen todos los registro al archivo desde el inicio.
                                    var msj_anular = "Todas las facturas ya fueron generadas en el archivo plano. <br><br>Ingrese una por una las facturas que desea generar nuevamente. <br><br>Tenga en cuenta que se devolveran al estado revisadas y se generarán con un nuevo consecutivo de archivo.";
                                    msj_anular = "Todas las facturas ya fueron generadas en el archivo plano.";
                                    jAlert(msj_anular,"Mensaje");
                                    /*jConfirm(msj_anular, "Confirmar", function(r)
                                    {
                                        if (r == true)
                                        {
                                            //ajaxGenerarPlanos(obJson, 'on');
                                            // Mostrar El campo para agregar facturas en estado AP que se quieren generar de nuevo.
                                            $("#div_facts_seleccion").html(data.buscar_facturas_ap);
                                            $("#div_facts_seleccion").show();
                                        }
                                    });*/
                                }
                            }
                            return data;
                    },"json").done(function(data){
                        iniciarcheck();
                        seleccion_click_facturas(undefined);
                    }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
                }
                else if (operacion == 'buscar_facturas_ap')
                {
                    var buscar_facturas_ap = $("#buscar_facturas_ap").val();
                    if(buscar_facturas_ap.replace(/ /gi,"") != '')
                    {
                        var arr_facturas_listadas = new Object();
                        $("#tabla_seleccion_facturas_ap").find(":button.active[id^=ckfact_]").each(function(){
                            var elem = $(this);
                            arr_facturas_listadas[elem.val()]            = new Object();
                            arr_facturas_listadas[elem.val()].factura    = elem.attr("factura");
                            arr_facturas_listadas[elem.val()].estado_enc = elem.attr("estado_enc");
                        });

                        obJson['accion']                = 'ajax_buscar_factura_ap';
                        obJson['num_factura_buscar']    = buscar_facturas_ap;
                        obJson['arr_facturas_listadas'] = JSON.stringify(arr_facturas_listadas);
                        $(".bloquear_todo").attr("disabled","disabled");
                        fnModalLoading();

                        $.post("generar_planos_furips.php", obJson,
                            function(data){
                                if(data.error == 1)
                                {
                                    fnModalLoading_Cerrar();
                                    jAlert(data.mensaje, "Mensaje");
                                    $(".bloquear_todo").removeAttr("disabled");
                                }
                                else
                                {
                                    $("#tabla_seleccion_facturas_ap").find('.tr_buscar_facturas_ap').parent().append(data.html);
                                    $("#buscar_facturas_ap").val("");
                                }
                                // return data;
                        },"json").done(function(data){
                            fnModalLoading_Cerrar();
                            $(".bloquear_todo").removeAttr("disabled");
                            // $("#div_facts_seleccion").find("input:checkbox").triggerHandler('change');
                            iniciarcheck();
                            $("#div_facts_seleccion").find(":button:disabled[id^=ckfact_]").removeClass("active");
                            seleccion_click_facturas(undefined);
                        }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
                    }
                    else
                    {
                        jAlert("Escriba un número de factura para buscar","Mensaje");
                    }
                }
                else
                {
                    var existe_seleccion = $("#div_facts_seleccion").find(":button.active[id^=ckfact_]").length; // Object.keys(arr_facturas_generar).length;
                    // console.log("existe_seleccion:"+existe_seleccion);
                    if(existe_seleccion > 0)
                    {
                        var sin_seleccionar = $("#div_facts_seleccion :button[id^=ckfact_]").not('.active').length;
                        if(sin_seleccionar > 0)
                        {
                            jConfirm("Han quedado facturas sin seleccionar, quiere continuar?", "Confirmar", function(r)
                            {
                                if (r == true)
                                {
                                    ajaxGenerarPlanos(obJson, 'off');
                                }
                            });
                        }
                        else
                        {
                            ajaxGenerarPlanos(obJson, 'off');
                        }
                    }
                    else
                    {
                        jAlert("Seleccione las facturas de la lista que ser&aacute;n generadas en el archivo plano.", "Mensaje");
                    }

                    // return;

                    /*$.post("generar_planos_furips.php", obJson,
                        function(data){
                            if(data.error == 1)
                            {
                                fnModalLoading_Cerrar();
                                jAlert(data.mensaje, "Mensaje");
                                $(".bloquear_todo").removeAttr("disabled");
                            }
                            else
                            {
                                fnModalLoading_Cerrar();
                                $(".bloquear_todo").removeAttr("disabled");
                                $("#tbl_descargables").show();
                            }
                            return data;
                    },"json").done(function(data){

                        if(generar_consecutivo_archivo == 'off' || data.facturas_GR > 0  || (data.facturas_GR == 0 && data.facturas_AP == 0))
                        {
                            // Cuando por lo menos hay un cargo sin generarse al archivo plano
                            ajaxGenerarPlanos(obJson, 'off');
                        }
                        else if(data.facturas_GR == 0 && data.facturas_AP > 0)
                        {
                            // Cuando ya han sido generados todos los cargos al archivo plano y se quiere confirmar que se generen todos los registro al archivo desde el inicio.
                            var msj_anular = "Todas las facturas ya fueron generadas en el archivo plano. <br><br>Desea iniciar este proceso nuevamente?";
                            jConfirm(msj_anular, "Confirmar", function(r)
                            {
                                if (r == true)
                                {
                                    ajaxGenerarPlanos(obJson, 'on');
                                }
                            });
                        }
                    }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });*/
                }
            }
        }

        function ajaxGenerarPlanos(obJson, cambiar_estado_encabezados)
        {
            $(".bloquear_todo").attr("disabled","disabled");
            fnModalLoading();

            var arr_facturas_generar = new Object();
            var arr_encabezados_AP   = new Object();
            $("#div_facts_seleccion").find(":button.active[id^=ckfact_]").each(function(){
                var elem       = $(this);
                var estado_enc = elem.attr("estado_enc");
                var factura    = elem.attr("factura");
                var id_enc_173 = elem.val();
                arr_facturas_generar[id_enc_173]            = new Object();
                arr_facturas_generar[id_enc_173].factura    = factura;
                arr_facturas_generar[id_enc_173].estado_enc = estado_enc;

                if((jQuery.inArray(id_enc_173, arr_encabezados_AP)) == -1) {
                    arr_encabezados_AP[id_enc_173] = factura;
                }
            });

            // $("#buscar_")
            obJson['facturas_seleccionadas'] = JSON.stringify(arr_facturas_generar);
            obJson['arr_encabezados_AP']     = JSON.stringify(arr_encabezados_AP);  // Los id de encabezado que queden en este array, cuando inicie el proceso de generación de archivo,
                                                                                    // se van a cambiar a estado GR y cuando se seleccione "generar consecutivo" se cambiarán nuevamente a AP

            // Generar archivos planos
            obJson['accion']                     = 'generar_archivos_planos';
            obJson['wbasedato_empresa']          = 'farpmla';
            obJson['wFarmacia']                  = ($("#wFarmacia").is(":checked")) ? 'on':'off';
            obJson['cambiar_estado_encabezados'] = cambiar_estado_encabezados;

            // $(".bloquear_todo").attr("disabled","disabled");
            // fnModalLoading();

            $.post("generar_planos_furips.php", obJson,
                function(data){
                    if(data.error == 1)
                    {
                        fnModalLoading_Cerrar();
                        jAlert(data.mensaje, "Mensaje");
                        $(".bloquear_todo").removeAttr("disabled");
                    }
                    else
                    {
                        if(data.facturas_encontradas > 0)
                        {
                            $("#td_archivos").append(data.html);
                            // console.log(data.arr_ids_generados.length);
                            if(data.arr_ids_generados.length > 0)
                            {
                                var fn_arr_ids_generados = new Array();
                                fn_arr_ids_generados = data.arr_ids_generados;
                                // console.log(fn_arr_ids_generados);
                                for (var idxGenerado in fn_arr_ids_generados)
                                {
                                    var fn_id_273 = fn_arr_ids_generados[idxGenerado];
                                    // console.log(fn_id_273);
                                    // $("#div_facts_seleccion").find("#ckfact_"+fn_id_273).removeAttr("checked");
                                    $("#div_facts_seleccion").find("#ckfact_"+fn_id_273).removeClass("active");
                                    $("#div_facts_seleccion").find("#ckfact_"+fn_id_273).attr("disabled","disabled");
                                    // $("#div_facts_seleccion").find("#ckfact_"+fn_id_273).parent().css({"font-weight":"bold","color":"green"});
                                }
                            }
                            // if(data.arr_ids_generados.length)
                            // if(data.notificaciones_html != '')
                            // {
                            //     $("#td_archivos").append(data.notificaciones_html);
                            // }
                        }
                        else
                        {
                            var spn = '<div style="text-align:center;font-weight:bold; background-color: #e0e0e0;">No se encontraron facturas revisadas para generar archivo plano o con valores aceptados mayores a cero.</div>';
                            $("#td_archivos").append(spn);
                        }

                        if(data.notificaciones_html != '')
                        {
                            $("#td_archivos_notificaciones").html(data.notificaciones_html);
                        }
                        fnModalLoading_Cerrar();
                        $(".bloquear_todo").removeAttr("disabled");
                        $("#tbl_descargables").show();
                    }
                    return data;
            },"json").done(function(data){
                // iniciarcheck();
            }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
        }

        function seleccion_click_facturas(btn)
        {
            // El evento click se ejecuta más rápido que la asignación o no de la clase active, por tanto se debe asumir
            // que si al dar click el botón no tiene la clase active es porque con el click actual se va a seleccionar o en caso contrario
            // si al dar click y tiene la clase active es porque el click actual va a desmarcar.
            var seleccionadas   = $("#div_facts_seleccion :button.active[id^=ckfact_]").length;
            var sin_seleccionar = $("#div_facts_seleccion :button[id^=ckfact_]").not('.active').length;
            // console.log(btn);
            if(typeof btn !== 'undefined')
            {
                if($(btn).hasClass("active"))
                {
                    // console.log("Activo");
                    seleccionadas--;
                    sin_seleccionar++;
                }
                else
                {
                    // console.log("Inactivo");
                    seleccionadas++;
                    sin_seleccionar--;
                }
            }

            $("#fact_active").html(seleccionadas);
            $("#fact_noactive").html(sin_seleccionar);

            // console.log("Chequeadas: "+seleccionadas);
            // console.log("Sin chequear: "+sin_seleccionar);
        }


        function consultarHistorialArchivos(url_archivo)
        {
            var obJson            = parametrosComunes();
            obJson['accion']      = 'historial_ajax_archivos';
            obJson['url_archivo'] = 'url_archivo';
            $(".bloquear_todo").attr("disabled","disabled");
            fnModalLoading();

            $.post("generar_planos_furips.php", obJson,
                function(data){
                    if(data.error == 1)
                    {
                        fnModalLoading_Cerrar();
                        jAlert(data.mensaje, "Mensaje");
                        $(".bloquear_todo").removeAttr("disabled");
                    }
                    else
                    {
                        $("#div_historial_archivos").html(data.html);
                        fnModalHistoricoArchivos("div_historial_archivos");
                    }
                    return data;
            },"json").done(function(data){
                fnModalLoading_Cerrar();
                $(".bloquear_todo").removeAttr("disabled");
            }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
        }

        function descargaArchivo(url_archivo)
        {
            return;
                var obJson            = parametrosComunes();
                obJson['accion']      = 'descargar_archivo';
                obJson['url_archivo'] = 'url_archivo';
                $(".bloquear_todo").attr("disabled","disabled");
                fnModalLoading();

                $.post("generar_planos_furips.php", obJson,
                    function(data){
                        // if(data.error == 1)
                        // {
                        //     fnModalLoading_Cerrar();
                        //     jAlert(data.mensaje, "Mensaje");
                        //     $(".bloquear_todo").removeAttr("disabled");
                        // }
                        // else
                        // {
                        //     //
                        // }
                        // return data;
                },"json").done(function(data){
                    fnModalLoading_Cerrar();
                    $(".bloquear_todo").removeAttr("disabled");
                }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
        }


        function fnModalInconsistencias(div)
        {
            $("#"+div).dialog({
                "closeOnEscape": false,
                show: {
                    effect: "blind",
                    duration: 100
                },
                hide: {
                    effect: "blind",
                    duration: 100
                },
                height: 500,
                // maxHeight: 400,
                width:  'auto',//800,
                buttons: {
                    "Cerrar": function() {
                      $( this ).dialog( "close" );
                    }},
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Inconsistencias con datos de admisi&oacute;n de paciente.",
                beforeClose: function( event, ui ) {
                    // $(".bloquear_todo").removeAttr("disabled");
                },
                create: function() {
                   $(this).closest('.ui-dialog').on('keydown', function(ev) {
                       if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                           $("#"+div).dialog('close');
                       }
                   });
                }
            }).on("dialogopen", function( event, ui ) {
                //
            });
        }

        function fnModalHistoricoArchivos(div)
        {
            var dias_cache_archivos = $("#dias_cache_archivos_furips").val();
            $("#"+div).dialog({
                "closeOnEscape": false,
                show: {
                    effect: "blind",
                    duration: 100
                },
                hide: {
                    effect: "blind",
                    duration: 100
                },
                height: 500,
                // maxHeight: 400,
                width:  '450',//'auto',
                buttons: {
                    "Cerrar": function() {
                      $( this ).dialog( "close" );
                    }},
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Hist&oacute;rico de archivos generados &uacute;ltimos "+dias_cache_archivos+" d&iacute;as",
                beforeClose: function( event, ui ) {
                    // $(".bloquear_todo").removeAttr("disabled");
                },
                create: function() {
                   $(this).closest('.ui-dialog').on('keydown', function(ev) {
                       if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                           $("#"+div).dialog('close');
                       }
                   });
                }
            }).on("dialogopen", function( event, ui ) {
                //
            });
        }

        function ver_mas_alertas(id_alertas)
        {
            $("#"+id_alertas).dialog({
                "closeOnEscape": false,
                show: {
                    effect: "blind",
                    duration: 100
                },
                hide: {
                    effect: "blind",
                    duration: 100
                },
                height: 500,
                // maxHeight: 400,
                width:  'auto',//800,
                buttons: {
                    "Regresar": function() {
                      $( this ).dialog( "close" );
                    }},
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Alerta de datos.",
                beforeClose: function( event, ui ) {
                    // $(".bloquear_todo").removeAttr("disabled");
                },
                create: function() {
                   $(this).closest('.ui-dialog').on('keydown', function(ev) {
                       if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                           $("#"+id_alertas).dialog('close');
                       }
                   });
                }
            }).on("dialogopen", function( event, ui ) {
                //
            });
        }

        function enterBuscar(evt,ths)
        {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if(charCode == 13)
            {
                $("#btn_buscar_facturas_ap").trigger("onclick");
                $(ths).focus();
            }
        }

        function validarRequeridos(contenedor)
        {
            var vacioR = true;
            $("#"+contenedor).find(".requerido").each(
                function(){
                    $(this).removeClass('campoRequerido');
                    var valor = $(this).val();

                    if(valor.replace(/ /gi,"") == '')
                    {
                        $(this).addClass('campoRequerido');
                        vacioR = false;
                    }
                }
            );
            return vacioR;
        }

        /**
         * [parametrosComunes: Genera un json con las variables más comunes que se deben enviar en los llamados ajax, evitando tener que crear los mismos parámetros de envío
         *                     en cada llamado ajax de forma manual.]
         * @return {[type]} [description]
         */
        function parametrosComunes()
        {
            var obJson                           = {};
            obJson['wemp_pmla']                  = $("#wemp_pmla").val();
            obJson['wbasedato_HCE']              = $("#wbasedato_HCE").val();
            obJson['wbasedato_movhos']           = $("#wbasedato_movhos").val();
            obJson['wbasedato_cliame']           = $("#wbasedato_cliame").val();
            obJson['dias_cache_archivos_furips'] = $("#dias_cache_archivos_furips").val();
            obJson['consultaAjax']               = '';
            return obJson;
        }


        /**
         * [crearAutocomplete: Inicializa las listas seleccionables en los campos que se definene como autocompletar]
         * @param  {[type]} accion                 [Acción o comportamiento especial que debe asumir la función dado el valor que llega en este parámetro]
         * @param  {[type]} arr_opciones_seleccion [Array de las opciones que debe desplegar el campo autocompletar]
         * @param  {[type]} campo_autocomplete     [ID html del campo que será y se iniciará como autocomplete]
         * @param  {[type]} codigo_default         [Código por defecto con el que podría iniciar el autocomplete]
         * @param  {[type]} nombre_default         [Nombre por defecto con el que podría iniciar el autocomplete]
         * @param  {[type]} limite_buscar          [Límite mínimo de caracteres con el que debería empezar a funcionar el autocomplete]
         * @return {[type]}                        [description]
         */
        function crearAutocomplete(arr_opciones_seleccion, campo_autocomplete, codigo_default, nombre_default, limite_buscar)
        {
            $("#"+campo_autocomplete).val(nombre_default);
            $("#"+campo_autocomplete).attr("codigo",codigo_default);
            $("#"+campo_autocomplete).attr("nombre",nombre_default);

            arr_datos = new Array();
            //var datos = arr_wempresp;//eval( $("#arr_wempresp").val() );
            var datos = eval('(' + $("#"+arr_opciones_seleccion).val() + ')');
            var index = -1;
            for (var CodVal in datos)
            {
                index++;
                arr_datos[index] = {};
                arr_datos[index].value  = CodVal+'-'+datos[CodVal];
                arr_datos[index].label  = CodVal+'-'+datos[CodVal];
                arr_datos[index].codigo = CodVal;
                arr_datos[index].nombre = CodVal+'-'+datos[CodVal];
            }

            // console.log(arr_datos);
            if($("#"+campo_autocomplete).length > 0)
            {
                $("#"+campo_autocomplete).autocomplete({
                        source: arr_datos, minLength : limite_buscar,
                        select: function( event, ui ) {
                                    // Lee el valor seleccionado en el autocompletar y lee solo el código y lo adiciona a otro campo de solo código.
                                    var cod_sel = ui.item.codigo;
                                    var nom_sel = ui.item.nombre;
                                    $("#"+campo_autocomplete).attr("codigo",cod_sel);
                                    $("#"+campo_autocomplete).attr("nombre",nom_sel);
                                    if($(this).attr("tipo_dato") == 'MultiSeleccion')
                                    {
                                        agregarElementoLista(this,$(this).attr("wcod_campo"));
                                    }
                                    // cargarConceptosPorProcedimientos(cod_sel);
                                },
                        close: function( event, ui ) {
                            if($(this).attr("tipo_dato") == 'MultiSeleccion')
                            {
                                $(this).val("");
                                $(this).attr("codigo","");
                                $(this).attr("nombre","");
                            }
                        }
                });
            }
            else if($("."+campo_autocomplete).length > 0)
            {
                $("."+campo_autocomplete).autocomplete({
                        source: arr_datos, minLength : limite_buscar,
                        select: function( event, ui ) {
                                    var cod_sel = ui.item.codigo;
                                    var nom_sel = ui.item.nombre;
                                    var id_el = $(this).attr("id");
                                    $("#"+id_el).attr("codigo",cod_sel);
                                    $("#"+id_el).attr("nombre",nom_sel);
                                    if($(this).attr("tipo_dato") == 'MultiSeleccion')
                                    {
                                        agregarElementoLista(this,$(this).attr("wcod_campo"));
                                    }
                                },
                        close: function( event, ui ) {
                            if($(this).attr("tipo_dato") == 'MultiSeleccion')
                            {
                                $(this).val("");
                                $(this).attr("codigo","");
                                $(this).attr("nombre","");
                            }
                        }
                });
            }
        }

        /**
         * [fnModalLoading: Es función se encarga de mostrar una ventana modal cada vez que se hace un llamado ajax con el fin de bloquear la página web hasta que se
         *                    se genere una respuesta y evitar que el usuario genere más eventos (click) sin terminar la petición anterior y evitar problemas
         *                    en la veracidad de datos]
         * @return {[type]} [description]
         */
        function fnModalLoading()
        {
            $( "#div_loading" ).dialog({
                show: {
                    effect: "blind",
                    duration: 100
                },
                hide: {
                    effect: "blind",
                    duration: 100
                },
                height: 'auto',
                // maxHeight: 600,
                width:  'auto',//800,
                // buttons: {
                //     "Cerrar": function() {
                //       $( this ).dialog( "close" );
                //     }},
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Consultando ...",
                beforeClose: function( event, ui ) {
                    //
                },
                create: function() {
                   $(this).closest('.ui-dialog').on('keydown', function(ev) {
                       if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                           $( "#div_loading" ).dialog('close');
                       }
                   });
                },
                "closeOnEscape": false,
                "closeX": false
            }).on("dialogopen", function( event, ui ) {
                //
            });
        }

        /**
         * [fnModalLoading_Cerrar: complemento a la función fnModalLoading, esta se encarga de cerrar la ventana modal]
         * @return {[type]} [description]
         */
        function fnModalLoading_Cerrar()
        {
            if($("#div_loading").is(":visible"))
            {
                $("#div_loading").dialog('close');
            }
        }

        function verOcultarLista(id_elem)
        {
            if($("#"+id_elem).is(":visible"))
            {
                $("#"+id_elem).hide(300);
            }
            else
            {
                $("#"+id_elem).show(300);
            }
        }

        function trOver(grupo)
        {
            $(grupo).addClass('classOver');
        }

        function trOut(grupo)
        {
            $(grupo).removeClass('classOver');
        }


        $.datepicker.regional['esp'] = {
            closeText: 'Cerrar',
            prevText: 'Antes',
            nextText: 'Despues',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
            dayNamesMin: ['D','L','M','M','J','V','S'],
            weekHeader: 'Sem.',
            dateFormat: 'yy-mm-dd',
            yearSuffix: '',
            changeYear: true,
            changeMonth: true,
            yearRange: '-100:+0'
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);

        function cerrarVentanaPpal()
        {
            window.close();
        }

        function iniciarcheck(){
            $('.button-checkbox').each(function () {

                // Settings
                var $widget = $(this),
                    $button = $widget.find('button'),
                    $checkbox = $widget.find('input:checkbox'),
                    color = $button.data('color'),
                    settings = {
                        on: {
                            icon: 'glyphicon glyphicon-check'
                        },
                        off: {
                            icon: 'glyphicon glyphicon-unchecked'
                        }
                    };

                // Event Handlers
                $button.on('click', function () {
                    $checkbox.prop('checked', !$checkbox.is(':checked'));
                    $checkbox.triggerHandler('change');
                    updateDisplay();
                });
                $checkbox.on('change', function () {
                    updateDisplay();
                });

                // Actions
                function updateDisplay() {
                    var isChecked = $checkbox.is(':checked');

                    // Set the button's state
                    $button.data('state', (isChecked) ? "on" : "off");

                    // Set the button's icon
                    $button.find('.state-icon')
                        .removeClass()
                        .addClass('state-icon ' + settings[$button.data('state')].icon);

                    // Update the button's color
                    if (isChecked) {
                        $button
                            .removeClass('btn-default')
                            .addClass('btn-' + color + ' active');
                    }
                    else {
                        $button
                            .removeClass('btn-' + color + ' active')
                            .addClass('btn-default');
                    }
                }

                // Initialization
                function init() {

                    updateDisplay();

                    // Inject the icon if applicable
                    if ($button.find('.state-icon').length == 0) {
                        $button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
                    }
                }
                init();
            });
        }

        function cargarEntidadesFarmacia(check){
            if($(check).is(":checked")){
                var obJson                  = parametrosComunes();
                obJson['accion']            = 'consultar_entidades_farmacia';
                obJson['wbasedato_empresa'] = 'farpmla';
                $(".bloquear_todo").attr("disabled","disabled");
                fnModalLoading();

                $.post("generar_planos_furips.php", obJson,
                    function(data){
                        if(data.error == 1)
                        {
                            fnModalLoading_Cerrar();
                            jAlert(data.mensaje, "Mensaje");
                            $(".bloquear_todo").removeAttr("disabled");
                        }
                        else
                        {
                            $("#arr_entidades_empresa").val(JSON.stringify(data.arr_entidades_empresa));
                        }
                        return data;
                },"json").done(function(data){
                    crearAutocomplete('arr_entidades_empresa', 'wentidad_respuesta','','',0);
                    fnModalLoading_Cerrar();
                    $(".bloquear_todo").removeAttr("disabled");
                }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
            } else {
                crearAutocomplete('arr_entidades', 'wentidad_respuesta','','',0);
            }
        }
    </script>

    <style type="text/css">
        /* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
        .ui-datepicker {font-size:12px;}
        /* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
        .ui-datepicker-cover {
            display: none; /*sorry for IE5*/
            display/**/: block; /*sorry for IE5*/
            position: absolute; /*must have*/
            z-index: -1; /*must have*/
            filter: mask(); /*must have*/
            top: -4px; /*must have*/
            left: -4px; /*must have*/
            width: 200px; /*must have*/
            height: 200px; /*must have*/
        }
        .titulopagina2
        {
            border-bottom-width: 1px;
            /*border-color: <?=$bordemenu?>;*/
            border-left-width: 1px;
            border-top-width: 1px;
            font-family: verdana;
            font-size: 18pt;
            font-weight: bold;
            height: 30px;
            margin: 2pt;
            overflow: hidden;
            text-transform: uppercase;
        }
        .bordeRojo{
            border:         1px solid red;
        }
        .bordeRed{
            border-radius:  4px;
            border:         1px solid #AFAFAF;
        }
        .bordeRed2{
            border-radius:  4px;
            border:         1px solid #2779AA;
        }
        .fila1
        {
            background-color:   #C3D9FF;
            color:              #000000;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }
        .fila2
        {
            background-color:   #E8EEF7;
            color:              #000000;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }
        .encabezadoTabla {
            background-color:   #2a5db0;
            color:              #ffffff;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }
        fieldset{
            border: 2px solid #e0e0e0;
        }
        legend{
            border: 2px solid #e0e0e0;
            border-top: 0px;
            font-family: Verdana;
            background-color: #e6e6e6;
            font-size: 11pt;
        }
        button{
            font-family:    verdana;
            font-weight:    bold;
            font-size:      10pt;
            cursor:         pointer;
        }
        .campoRequerido{
            border: 1px orange solid;
            background-color:lightyellow;
        }
        .classOver{
            background-color: #CCCCCC;
        }

        /* BOOTSTRAP */
        .container {
            width: 1170px;
        }
        .container {
            width: 970px;
        }
        .container {
            width: 750px;
        }
        .container {
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }
        .btn-primary:active, .btn-primary.active, .open .dropdown-toggle.btn-primary {
            background-image: none;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .open .dropdown-toggle.btn-primary {
            color: #fff;
            background-color: #3276b1;
            border-color: #285e8e;
        }
        .btn:active, .btn.active {
            background-image: none;
            outline: 0;
            -webkit-box-shadow: inset 0 3px 5px rgba(0,0,0,0.125);
            box-shadow: inset 0 3px 5px rgba(0,0,0,0.125);
        }
        .btn-primary {
            color: #fff;
            background-color: #428bca;
            border-color: #357ebd;
        }
        .btn {
            display: inline-block;
            padding: 1px 6px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: normal;
            line-height: 1.428571429;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            cursor: pointer;
            background-image: none;
            border: 1px solid transparent;
            border-radius: 4px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            -o-user-select: none;
            user-select: none;
        }
        input, button, select, textarea {
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
        }
        button, html input[type="button"], input[type="reset"], input[type="submit"] {
            cursor: pointer;
            -webkit-appearance: button;
        }
        button, select {
            text-transform: none;
        }
        button, input {
            line-height: normal;
        }
        button, input, select, textarea {
            margin: 0;
            font-family: inherit;
            font-size: 100%;
        }
        input[type="radio"], input[type="checkbox"] {
            margin: 4px 0 0;
            margin-top: 1px \9;
            line-height: normal;
        }
        input[type="checkbox"], input[type="radio"] {
            padding: 0;
            box-sizing: border-box;
        }
        .hidden {
            display: none !important;
            visibility: hidden !important;
        }
        input, button, select, textarea {
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
        }
        button, input {
            line-height: normal;
        }
        button, input, select, textarea {
            margin: 0;
            font-family: inherit;
            font-size: 100%;
        }

        .btn-primary.disabled, .btn-primary:disabled {
            background-color: #0275d8;
            border-color: #0275d8;
        }
        .btn-primary:hover {
            color: #fff;
            background-color: #025aa5;
            border-color: #01549b;
        }
        .btn.disabled, .btn:disabled {
            cursor: not-allowed;
            opacity: .65;
        }
        .btn-default:hover, .btn-default:focus, .btn-default:active, .btn-default.active, .open .dropdown-toggle.btn-default {
            color: #333;
            background-color: #ebebeb;
            border-color: #adadad;
        }
        .btn:hover, .btn:focus {
            color: #333;
            text-decoration: none;
        }
        .btn-default {
            color: #333;
            background-color: #fff;
            border-color: #ccc;
        }
        /* BOOTSTRAP */

        .ui-autocomplete {
            max-height: 150px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: auto;
            font-size:  9pt;
        }
    </style>
</head>
<body>
<input type='hidden' name='wemp_pmla' id='wemp_pmla' value="<?=$wemp_pmla?>">
<input type='hidden' name='get_whistoria' id='get_whistoria' value="<?=$whis?>">
<input type='hidden' name='get_wingreso' id='get_wingreso' value="<?=$wing?>">
<input type='hidden' name='wbasedato_HCE' id='wbasedato_HCE' value="<?=$wbasedato_HCE?>">
<input type='hidden' name='wbasedato_movhos' id='wbasedato_movhos' value="<?=$wbasedato_movhos?>">
<input type='hidden' name='wbasedato_cliame' id='wbasedato_cliame' value="<?=$wbasedato_cliame?>">
<input type='hidden' name='codigo_habilitacion' id='codigo_habilitacion' value="<?=$codigo_habilitacion?>">
<input type='hidden' name='limite_enc_facturas_plano_furips' id='limite_enc_facturas_plano_furips' value="<?=$limite_enc_facturas_plano_furips?>">
<input type='hidden' name='dias_cache_archivos_furips' id='dias_cache_archivos_furips' value="<?=$dias_cache_archivos_furips?>">
<input type="hidden" name="arr_entidades" id="arr_entidades" value='<?=json_encode($arr_entidades)?>'>
<input type="hidden" name="arr_entidades_empresa" id="arr_entidades_empresa" value='{}'>
<input type='hidden' mane='arr_westado_cartera' id='arr_westado_cartera' value='<?=json_encode($arr_estadoCartera)?>'>
<?php
    encabezado("<div class='titulopagina2'>Generar archivo plano FURIPS</div>", $wactualiza, "clinica");
?>
<div id='accordionFiltros' align='center'>
    <h1 style='font-size: 11pt;' align='left'>&nbsp;&nbsp;&nbsp;Filtros / Descarga</h1>
    <div class='ui-tabs ui-widget ui-widget-content ui-corner-all'>
        <fieldset align='center' style='padding:15px;margin:15px'>
            <legend class='fieldset'></legend>
            <table width=''>
                <tr>
                    <td class='encabezadoTabla'>Filtrar fecha repuesta <input type="checkbox" id="chk_filtar_fecha" onclick="verOcultarLista('tbl_filtro_fecha')"></td>
                    <td class='fila1'>
                        <table width='100%' id="tbl_filtro_fecha" style="display: none;">
                            <tr>
                                <td class='fila2'>Respuesta glosa desde:</td>
                                <td class='fila1'><input type='text' id='fecha_inicio_rep' size="10" placeholder='Debe ingresar dato' value="<?=date("Y-m-d")?>" disabled="disabled"></td>
                                <td class='fila2'>Respuesta glosa hasta:</td>
                                <td class='fila1'><input type='text' id='fecha_final_rep' size="10" placeholder='Debe ingresar dato' value="<?=date("Y-m-d")?>" disabled="disabled"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class='encabezadoTabla'>Facturas de farmacia</td>
                    <td class='fila1'><input type='checkbox' id='wFarmacia' class="" value="" onclick="cargarEntidadesFarmacia(this);"></td>
                </tr>
                <tr>
                    <td class='encabezadoTabla'>Entidad</td>
                    <td class='fila1'><input type='text' id='wentidad_respuesta' class="campo_autocomplete requerido" size="40"></td>
                </tr>
                <tr>
                    <td class='encabezadoTabla'>Estado cartera factura</td>
                    <td class='fila1'><input type='text' id='westado_cartera' class="campo_autocomplete requerido" size="40"></td>
                </tr>
                <tr>
                    <td class='encabezadoTabla'>Naturaleza evento</td>
                    <td class='fila1'>
                        <select id="wnaturaleza_evento" class="requerido" ><?=$options_naturaleza_ev?></select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class='fila1' style="text-align: center;">
                        <button style="font-size:9pt" onclick="generarArchivosPlanos('listar_facturas');" class="bloquear_todo">Consultar facturas</button>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' style="text-align: center">
                        <div style="width:100%; height: 300px; overflow:auto;background-color:#FFFFCC;display: none;" id="div_facts_seleccion">
                    </td>
                </tr>
                <tr id="tr_contador_checks" style="display: none;">
                    <td colspan='2' style="text-align: center;font-size:0.7em;background-color: #FFFFCC;">
                        <span style="font-weight: bold;">Sin seleccionar</span>
                        <span id="fact_noactive" style="background-color: #fff;border-color: #01549b;color: #000;border-radius:  4px;padding: 0.1em;font-weight: bold;padding-right: 1em;padding-left: 1em;border: solid 0.1em #ccc;">
                            0
                        </span>
                        <span id="fact_active" style="background-color: #025aa5;border-color: #01549b;color: #fff;border-radius:  4px;padding: 0.1em;font-weight: bold;padding-right: 1em;padding-left: 1em;border: solid 0.1em #025aa5;">
                            0
                        </span>
                        <span style="font-weight: bold;">Seleccionadas</span>
                    </td>
                </tr>
                <!-- <tr>
                    <td colspan='2' style="text-align: center" >
                        &nbsp;
                    </td>
                </tr> -->
                <tr>
                    <td colspan='2' style="text-align: center">
                        <button style="font-size:9pt" onclick="generarArchivosPlanos('');" class="bloquear_todo">Generar archivos planos</button> [<span style="font-size:9pt;">Generar consecutivo</span><input type="checkbox" id="generar_consecutivo_archivo" name="" value="on">]
                        <br>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' style="text-align: center;">
                        <div style="text-align: center; width: 100%;">
                            <table width='100%' id="tbl_descargables" align="center">
                                <tr>
                                    <td align='center' class="encabezadoTabla">Archivos decargables</td>
                                </tr>
                                <tr>
                                    <td align='left' id="td_archivos"></td>
                                </tr>
                                <tr>
                                    <td align='left' id="td_archivos_notificaciones"></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </fieldset>
        <a href="javascript:" id="a_ver_historico_archivos">Hist&oacute;rico de archivos generados &uacute;ltimos <?=$dias_cache_archivos_furips?> d&iacute;as</a>
    </div>
</div>
<div id="div_loading" style="display:none;"><img width="15" height="15" src="../../images/medical/ajax-loader5.gif" /> Consultando datos, espere un momento por favor...</div>
<input type='hidden' name='failJquery' id='failJquery' value='El programa termin&oacute; de ejecutarse pero con algunos inconvenientes <br>(El proceso no se complet&oacute; correctamente)' >
<div style="display: none;" id="div_historial_archivos"></div>
<div style="text-align: center;">
    <br><input value="Cerrar Ventana" onclick="cerrarVentanaPpal();" type="button">
</div>
</body>
</html>