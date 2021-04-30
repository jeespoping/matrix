<?php
    /** Se inicializa el bufer de salida de php **/
    ob_start();
    include_once("conex.php");
    
    include_once("root/comun.php");

    /***
     * Creo una clase para medida
     * @by: sebastian.nevado
     * @date: 2021/04/21
     ***/
    class Medida 
    {

        //Variable para la conexión a la base de datos
        private $dbConection;

        //Atributos de la clase Medida
        
        /**
         * @Name: iId
         * @Type: biginteger
         */
        private $iId;

        /**
         * @Name: sCodigo
         * @Type: string
         */
        private $sCodigo;

        /**
         * @Name: sNombre
         * @Type: string
         */
        private $sNombre;

        /**
         * @Name: sDescripcion
         * @Type: string
         */
        private $sDescripcion;

        /**
         * @Name: iIdUnidad
         * @Type: integer
         */
        private $iIdUnidad;

        /**
         * @Name: bEnviarNotificacion
         * @Type: boolean
         */
        private $bEnviarNotificacion;

        /**
         * @Name: sSeguridad
         * @Type: string
         */
        private $sSeguridad;

        /**
         * @Name: sMensaje
         * @Type: string
         */
        private $sMensaje;

        /**
         * @Name: wemp_pmla
         * @Type: string
         */
        private $wemp_pmla;

        /**
         * @Name: nombreAplicacion
         * @Type: string
         */
        private $nombreAplicacion;

        /**
         * Constructor de la clase
         */
        public function __construct($wemppmla = null)
        {
            global $conex;
            //Valores por defecto
            $this->dbConection = $conex;
            $this->iId = null;
            $this->sCodigo = null;
            $this->sNombre = null;
            $this->sDescripcion = null;
            $this->iIdUnidad = null;
            $this->bEnviarNotificacion = null;
            $this->sMensaje = null;
            $this->wemp_pmla = $wemppmla;
            $this->nombreAplicacion = 'radiolog';
            $this->sSeguridad = isset($_SESSION['usera']) ? "C-".$_SESSION['usera'] : null;
        }

        /**
         * Función para obtener el id
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getId()
        {
            return $this->iId;
        }

        /**
         * Función para setear el id
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: iId
         */
        public function setId($iId)
        {
            $this->iId = $iId;
        }

        /**
         * Función para obtener el código
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getCodigo()
        {
            return $this->sCodigo;
        }

        /**
         * Función para setear el código
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: sCodigo
         */
        public function setCodigo($sCodigo)
        {
            $this->sCodigo = $sCodigo;
        }

        /**
         * Función para obtener el nombre
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getNombre()
        {
            return $this->sNombre;
        }

        /**
         * Función para setear el nombre
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: sNombre
         */
        public function setNombre($sNombre)
        {
            $this->sNombre = $sNombre;
        }

        /**
         * Función para obtener el descripción
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getDescripcion()
        {
            return $this->sDescripcion;
        }

        /**
         * Función para setear la descripción
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: sDescripcion
         */
        public function setDescripcion($sDescripcion)
        {
            $this->sDescripcion = $sDescripcion;
        }

        /**
         * Función para obtener el idUnidad
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getIdUnidad()
        {
            return $this->iIdUnidad;
        }

        /**
         * Función para setear el idUnidad
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: iIdUnidad
         */
        public function setIdUnidad($iIdUnidad)
        {
            $this->iIdUnidad = $iIdUnidad;
        }

        /**
         * Función para obtener el envío de notificación por correo
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getEnviarNotificacion()
        {
            return $this->bEnviarNotificacion;
        }

        /**
         * Función para setear el envío de notificación por correo
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: bEnviarNotificacion
         */
        public function setEnviarNotificacion($bEnviarNotificacion)
        {
            $this->bEnviarNotificacion = $bEnviarNotificacion;
        }

        /**
         * Función para obtener el código de seguridad
         * @by: sebastian.nevado
         * @date: 2021/04/23
         */
        public function getSeguridad()
        {
            return $this->sSeguridad;
        }

        /**
         * Función para setear el envío de notificación por correo
         * @by: sebastian.nevado
         * @date: 2021/04/23
         * @params: sSeguridad
         */
        public function setSeguridad($sSeguridad)
        {
            $this->sSeguridad = $sSeguridad;
        }

        /**
         * Función para obtener el mensaje
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getMensaje()
        {
            return $this->sMensaje;
        }

        /**
         * Función para setear el mensaje
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: sMensaje
         */
        public function setMensaje($sMensaje)
        {
            $this->sMensaje = $sMensaje;
        }

        /**
         * Función para verificar integridad de la medida antes de guardar
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @return: Boolean
         */
        public function verificarIntegridad()
        {
            //Valido código
            if(is_null($this->sCodigo) || is_null($this->sCodigo) || strlen($this->sCodigo)<1)
            {
                $this->sMensaje = 'El campo "C&oacute;digo" es obligatorio';
                return false;
            }

            //Valido nombre
            if(!isset($this->sNombre) || is_null($this->sNombre)  || strlen($this->sNombre)<1)
            {
                $this->sMensaje = 'El campo "Nombre" es obligatorio';
                return false;
            }

            //Valido unidad
            if(!isset($this->iIdUnidad) || is_null($this->iIdUnidad)  || strlen($this->iIdUnidad)<1)
            {
                $this->sMensaje = 'El campo "Unidad" es obligatorio';
                return false;
            }
            
            return true;
            
        }

        /**
         * Función para guardar medida en la base de datos
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @return: Boolean
         */
        public function save()
        {
            //Obtengo el alias por aplicación
            $wbasedato = consultarAliasPorAplicacion($this->dbConection, $this->wemp_pmla, $this->nombreAplicacion);

            //Verifico la integridad        
            if(!$this->verificarIntegridad())
            {
                return false;
            }

            //Si tiene el id seteado, es update
            if(isset($this->iId))
            {
                //Actualizo en base de datos
                $sQueryUpdate = " UPDATE ".$wbasedato."_000001
                                SET Medico = '".$wbasedato."', Fecha_data = '".date("Y-m-d")."', Hora_data = '".date("H:i:s")."',
                                    medcod = '".$this->sCodigo."', mednom = '".$this->sNombre."', meddes = '".$this->sDescripcion."', 
                                    meduni = ".$this->iIdUnidad.", medenc = '".$this->bEnviarNotificacion."', Seguridad = '".$this->sSeguridad."'
                                WHERE id = ".$this->iId;

                $res = mysqli_query($this->dbConection, $sQueryUpdate) or die("Error: " . mysql_errno() . " - en el query (Insertar En ".$wbasedato."_000001 por primera vez): " . $sQueryUpdate . " - " . mysql_error());

                $this->sMensaje = 'Medida actualizada satisfactoriamente';

                return true;
            }
            else
            {
                //Inserto en base de datos
                $sQueryInsert = " INSERT INTO ".$wbasedato."_000001
                                (   Medico, Fecha_data, Hora_data, medcod, mednom,
                                    meddes, meduni, medenc, Seguridad)
                            VALUES
                                (   '".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$this->sCodigo."','".$this->sNombre."',
                                    '".$this->sDescripcion."',".$this->iIdUnidad.",'".$this->bEnviarNotificacion."','".$this->sSeguridad."')";
                $res = mysqli_query($this->dbConection, $sQueryInsert) or die("Error: " . mysql_errno() . " - en el query (Insertar En ".$wbasedato."_000001 por primera vez): " . $sQueryInsert . " - " . mysql_error());

                $this->sMensaje = 'Medida insertada satisfactoriamente';

                $this->iId = mysql_insert_id($this->dbConection);

                return true;
            }
        }

        /**
         * Función para cargar la medida de la base de datos
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @return: Boolean
         */
        public function load()
        {
            //Obtengo el alias por aplicación
            $wbasedato = consultarAliasPorAplicacion($this->dbConection, $this->wemp_pmla, $this->nombreAplicacion);

            //Valido que tenga el id seteado
            if(isset($this->iId))
            {
                //Cargo de base de datos
                $sQuery = "SELECT medcod, mednom, meddes, meduni, medenc
                            FROM ".$wbasedato."_000001"."
                            WHERE id = ".$this->iId;

                $err1 = mysqli_query($this->dbConection, $sQuery);
                $row1 = mysql_fetch_assoc($err1);

                $this->sCodigo = htmlentities($row1['medcod']);
                $this->sNombre = htmlentities($row1['mednom']);
                $this->sDescripcion = htmlentities($row1['meddes']);
                $this->iIdUnidad = htmlentities($row1['meduni']);
                $this->bEnviarNotificacion = (htmlentities($row1['medenc']) == 1) ? true : false;

                $this->sMensaje = 'Medida cargada satisfactoriamente';

                return true;
            } else {
                
                $this->sMensaje = 'No se envió la identificación de la medida';

                return false;
            }
        }

        /**
         * Función para cargar listado de medidas de la base de datos
         * @by: sebastian.nevado
         * @date: 2021/04/22
         * @return: array
         */
        public function getAll()
        {
            //Obtengo el alias por aplicación
            $wbasedato = consultarAliasPorAplicacion($this->dbConection, $this->wemp_pmla, $this->nombreAplicacion);

            //Cargo de base de datos
            $sQuery = "SELECT id, medcod AS codigo, mednom AS nombre, meddes AS descripcion, meduni AS unidad, medenc AS enviarnotificacion,
                                CASE 
                                    WHEN medenc = 0 THEN 'No'
                                    ELSE 'Si'
                                END enviarnotificaciontexto
                        FROM ".$wbasedato."_000001
                        ORDER BY id";

            //Uno a uno
            $resultado_query = mysqli_query($this->dbConection, $sQuery);
            $aMedidas = mysqli_fetch_all($resultado_query, MYSQLI_ASSOC);
            
            $this->sMensaje = 'Todos las medidas cargadas satisfactoriamente';

            return $aMedidas;
        }

        /**
         * Función para guardar medidaxpersona en la base de datos
         * @by: sebastian.nevado
         * @date: 2021/04/26
         * @return: Boolean
         */
        public function saveMedidaxPersona($sCodigoPersona, $dFechaMedida, $dHoraMedida, $dValorMedida, $iIdMedidaxPersonal = null, $iIdMedida = null)
        {
            //Cargo la medida
            $this->iId = (!is_null($iIdMedida)) ? $iIdMedida : $this->iId;
            $this->load();

            //Obtengo el alias por aplicación
            $wbasedato = consultarAliasPorAplicacion($this->dbConection, $this->wemp_pmla, $this->nombreAplicacion);

            //Verifico la integridad        
            if(!$this->verificarIntegridadMedidaxPersonal($sCodigoPersona, $dFechaMedida, $dHoraMedida, $dValorMedida, $iIdMedidaxPersonal))
            {
                return false;
            }

            //Si tiene el id de medidaxpersonal seteado, es update
            if(isset($iIdMedidaxPersonal))
            {
                //Actualizo en base de datos
                $sQueryUpdate = " UPDATE ".$wbasedato."_000002
                                SET Medico = '".$wbasedato."', Fecha_data = '".date("Y-m-d")."', Hora_data = '".date("H:i:s")."',
                                    mdpimp = '".$this->iId."', mdpcpm = '".$sCodigoPersona."', mdpcpr = '".$_SESSION['codigo']."', 
                                    mdpvme = '".$dValorMedida."', mdpfme = '".$dFechaMedida."', mdphme = '".$dHoraMedida."', 
                                    Seguridad = '".$this->sSeguridad."'
                                WHERE id = ".$iIdMedidaxPersonal;

                $res = mysqli_query($this->dbConection, $sQueryUpdate) or die("Error: " . mysql_errno() . " - en el query (Insertar En ".$wbasedato."_000002 por primera vez): " . $sQueryUpdate . " - " . mysql_error());

                $this->sMensaje = 'Medida por persona actualizada satisfactoriamente';

                return true;
            }
            else
            {
                //Inserto en base de datos
                $sQueryInsert = " INSERT INTO ".$wbasedato."_000002
                                (   Medico, Fecha_data, Hora_data, 
                                    mdpimp, mdpcpm, mdpcpr, mdpvme, 
                                    mdpfme, mdphme, Seguridad)
                            VALUES
                                (   '".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".
                                    $this->iId."','".$sCodigoPersona."','".$_SESSION['usera']."','".$dValorMedida."','".
                                    $dFechaMedida."','".$dHoraMedida."','".$this->sSeguridad."')";
                $res = mysqli_query($this->dbConection, $sQueryInsert) or die("Error: " . mysql_errno() . " - en el query (Insertar En ".$wbasedato."_000002 por primera vez): " . $sQueryInsert . " - " . mysql_error());

                $this->sMensaje = 'Medida por persona insertada satisfactoriamente';

                $iIdMedidaxPersonal = mysql_insert_id($this->dbConection);

                //Se envía notificación por correo si la medida está configurada de esa manera
                if($this->bEnviarNotificacion)
                {
                    $bResultadoNotificación = $this->enviarNotificacion($sCodigoPersona, $dFechaMedida, $dHoraMedida, $dValorMedida, $iIdMedidaxPersonal);
                }

                return true;
            }
        }

        /**
         * Función para verificar integridad de la medidaxpersona antes de guardar
         * @by: sebastian.nevado
         * @date: 2021/04/26
         * @return: Boolean
         */
        public function verificarIntegridadMedidaxPersonal($sCodigoPersona, $dFechaMedida, $dHoraMedida, $dValorMedida, $iIdMedidaxPersonal)
        {
            //Valido código
            if(is_null($sCodigoPersona) || is_null($sCodigoPersona) || strlen($sCodigoPersona)<1)
            {
                $this->sMensaje = 'El campo "C&oacute;digo de Persona" es obligatorio';
                return false;
            }

            //Valido fecha
            if(!isset($dFechaMedida) || is_null($dFechaMedida)  || strlen($dFechaMedida)<1)
            {
                $this->sMensaje = 'El campo "Fecha" es obligatorio';
                return false;
            }

            //Valido hora
            if(!isset($dHoraMedida) || is_null($dHoraMedida)  || strlen($dHoraMedida)<1)
            {
                $this->sMensaje = 'El campo "Hora" es obligatorio';
                return false;
            }

            //Valido Valor medida
            if(!isset($dValorMedida) || is_null($dValorMedida)  || strlen($dValorMedida)<1)
            {
                $this->sMensaje = 'El campo "Valor medida" es obligatorio';
                return false;
            }

            //Valido id medida
            if(!isset($this->iId) || is_null($this->iId)  || strlen($this->iId)<1)
            {
                $this->sMensaje = 'El campo "C&oacute;digo Medida" es obligatorio';
                return false;
            }

            //Valido fecha ingresada vs fecha actual
            $dFechaHoraMedida = DateTime::createFromFormat('Y-m-d H:i', $dFechaMedida . ' ' . $dHoraMedida);
            $dFechaHoraActual = DateTime::createFromFormat('Y-m-d H:i', date('Y-m-d H:i'));
            $sFechaHoraActual = date('Y-m-d H:i');
            if($dFechaHoraMedida > $dFechaHoraActual)
            {
                $this->sMensaje = 'La Fecha y Hora ingresada ('.$dFechaMedida . ' ' . $dHoraMedida.') no pueden ser superior a la fecha y hora actual ('.$sFechaHoraActual.')';
                return false;
            }
            
            return true;
            
        }

        /**
         * Función para cargar listado de usuarios para registrar medidas de la base de datos
         * @by: sebastian.nevado
         * @date: 2021/04/22
         * @return: array
         */
        public function getUsuariosMedidas($sTipoBusqueda = null, $sValorBusqueda = null, $sCodigoCentroCosto = null)
        {
            $aPersonas = array();

            //Defino si busco por documento, código o nombre y construyo el where de la consulta
            $sBusqueda = '';
            if (($sTipoBusqueda == 'documento') && ($sValorBusqueda != ''))
            {
                $sBusqueda = " AND Documento LIKE '".$sValorBusqueda."%' ";
            }
            elseif (($sTipoBusqueda == 'codigo') && ($sValorBusqueda != ''))
            {
                $sBusqueda = " AND Codigo LIKE '".$sValorBusqueda."%' ";
            }
            elseif (($sTipoBusqueda == 'nombre') && ($sValorBusqueda != ''))
            {
                $sBusqueda = " AND Descripcion LIKE '%".$sValorBusqueda."%' ";
            }

            $sBusquedaCentroCosto = "";
            if(isset($sCodigoCentroCosto))
            {
                //Cargo de base de datos
                $sQuery = "SELECT codigo, descripcion AS nombre, grupo, empresa, activo, documento, email
                FROM usuarios 
                WHERE activo = 'A' AND Ccostos = '".$sCodigoCentroCosto."'".
                $sBusqueda .
                " ORDER BY codigo ";

                //Uno a uno
                $resultado_query = mysqli_query($this->dbConection, $sQuery);
                $aPersonas = mysqli_fetch_all($resultado_query, MYSQLI_ASSOC);

                $this->sMensaje = 'Todos los usuarios cargados satisfactoriamente';
            }
            else
            {
                $this->sMensaje = 'No se seleccionó centro de costo';
            }

            return $aPersonas;
        }

        /**
         * Función para enviar notificación por correo cuando se ingrese una nueva medida
         * @by: sebastian.nevado
         * @date: 2021/04/28
         * @return: array
         */
        public function enviarNotificacion($sCodigoPersona, $dFechaMedida, $dHoraMedida, $dValorMedida, $iIdMedidaxPersonal)
        {
            //Obtengo el alias por aplicación
            $wbasedato = consultarAliasPorAplicacion($this->dbConection, $this->wemp_pmla, $this->nombreAplicacion);

            //Valido que se envíe código de la persona
            if(isset($sCodigoPersona) && !is_null($sCodigoPersona))
            {
                //Cargo de base de datos
                $sQuery = "SELECT Descripcion AS nombre, Documento, Email
                            FROM usuarios 
                            WHERE codigo = ".$sCodigoPersona;

                $err1 = mysqli_query($this->dbConection, $sQuery);
                $row1 = mysql_fetch_assoc($err1);

                $sCorreoPersona = $row1['Email'];
                $sNombrePersona = ucwords(strtolower($row1['nombre']));
                $sDocumentoPersona = $row1['Documento'];
                $aDestinatario = array($sCorreoPersona."-".$sNombrePersona);
                $sUnidadMedida = $this->getNombreUnidad($this->iIdUnidad);

                //Valido si la persona tiene correo
                if(isset($sCorreoPersona) && !is_null(isset($sCorreoPersona)) && ($sCorreoPersona != ''))
                {
                    //Construyo el correo
                    $asunto = "Atencion...";
                    $mensaje = "Señor/a ".$sNombrePersona."
                    </br>
                    Se ha realizado un registro de la medida ".$this->sNombre." al código de usuario ".$sCodigoPersona.", vinculado a esta dirección de correo electrónico en el sistema Matrix. Por favor, ingrese al sistema y valide la información.";
                    $altbody = "";
                    
                    $email        		= consultarAliasPorAplicacion( $this->dbConection, $this->wemp_pmla, "emailEnviosTI");
                    $email        		= explode("--", $email );
                    $wremitente			= array( 'email'	=> $email[0],
                                                'password' => $email[1],
                                                'from' 	=> $email[0],
                                                'fromName' => "",
                                        );
                    
                    //Se envía el correo
                    //$respuesta = sendToEmail($asunto, $mensaje, $altbody, $wremitente, $aDestinatario);
                    $respuesta = array();
                    $respuesta['Error']=true;
                    
                    //Si envío correo, se guarda en la base de datos
                    if($respuesta['Error'])
                    {
                        $this->sMensaje .= "Correo enviado";
                        
                        //Guardo en base de datos el envío de correo
                        $sQueryInsert = "INSERT INTO ".$wbasedato."_000003 
                                            (Medico, Fecha_data, Hora_data,
                                            nmpimp, nmpcme, nmpnme, 
                                            nmpvme, nmpcum, nmpume, 
                                            nmpcpm, nmpnpm, nmpcep, 
                                            nmpcop, nmpfme, nmphme, 
                                            nmpfen, nmphen, Seguridad)
                                        VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', ".
                                                    $iIdMedidaxPersonal.", '".$this->sCodigo."', '".$this->sNombre."', ".
                                                    $dValorMedida.", ".$this->iIdUnidad.", '".$sUnidadMedida."', '".
                                                    $sCodigoPersona."', '".$sNombrePersona."', '".$sDocumentoPersona."', '".
                                                    $sCorreoPersona. "', '".$dFechaMedida."', '".$dHoraMedida."', '".
                                                    date("Y-m-d")."', '".date("H:i:s")."', '".$this->sSeguridad."')";

                        //Guardo en base de datos el envío de correo
                        $res = mysqli_query($this->dbConection, $sQueryInsert) or die("Error: " . mysql_errno() . " - en el query (Insertar En ".$wbasedato."_000005 por primera vez): " . $sQueryInsert . " - " . mysql_error());

                        $this->sMensaje .= 'Envío de notificación por correo guardado satisfactoriamente';

                        return true;
                    }
                    else
                    {
                        $this->sMensaje .= "No se pudo enviar el correo";
                        return false;
                    }
                }
                else
                {
                    $this->sMensaje .= "No se puede enviar la notificación por correo, porque la persona no tiene correo electrónico registrado";
                    return false;
                }
            }
            else
            {
                $this->sMensaje = "No se puede enviar la notificación por correo, porque no se envío el código de la persona";
                return false;
            }
        }

        /**
         * Función para obtener unidad de la medida
         * @by: sebastian.nevado
         * @date: 2021/04/28
         * @return: string
         */
        public function getNombreUnidad($iIdUnidad)
        {
            $sQuery = "SELECT unides
                            FROM mipres_000017
                            WHERE id = ".$iIdUnidad;

            $err1 = mysqli_query($this->dbConection, $sQuery);
            $row1 = mysql_fetch_assoc($err1);

            $sNombreUnidad = htmlentities($row1['unides']);
            return $sNombreUnidad;
        }

        /**
         * Función para obtener las personas habilitadas para medidas
         * @by: sebastian.nevado
         * @date: 2021/04/29
         * @return: string
         */
        public function getCentrosCosto()
        {
            //Obtengo el alias por aplicación
            $wbasedato = consultarAliasPorAplicacion($this->dbConection, $this->wemp_pmla, 'movhos');

            $sQuery = "SELECT Ccocod AS codigo, Cconom AS nombre
                        FROM ".$wbasedato."_000011
                        ORDER BY codigo";

            //Uno a uno
            $resultado_query = mysqli_query($this->dbConection, $sQuery);
            $aCentrosCosto = mysqli_fetch_all($resultado_query, MYSQLI_ASSOC);
            
            $this->sMensaje = 'Todos los centros de costo cargados satisfactoriamente';

            return $aCentrosCosto;
        }

        /**
         * Función para obtener el listado de personas con medidas
         * @by: sebastian.nevado
         * @date: 2021/04/30
         * @return: string
         */
        public function getAllMedidasxPersona($sTipoBusqueda = null, $sValorBusqueda = null, $iIdMedida = null, $bGuardarBusqueda = false)
        {
            //Obtengo el alias por aplicación
            $wbasedato = consultarAliasPorAplicacion($this->dbConection, $this->wemp_pmla, $this->nombreAplicacion);

            //Defino si busco por documento, código o nombre y construyo el where de la consulta
            $sBusqueda = '';
            if (($sTipoBusqueda == 'documento') && ($sValorBusqueda != ''))
            {
                $sBusqueda .= " AND u.Documento = '".$sValorBusqueda."' ";
            }
            elseif (($sTipoBusqueda == 'codigo') && ($sValorBusqueda != ''))
            {
                $sBusqueda .= " AND u.Codigo = '".$sValorBusqueda."' ";
            }
            
            //Si busco una medida en específico, filtro la medida
            if (isset($iIdMedida))
            {
                $sBusqueda .= " AND m.id = ".$iIdMedida." ";
            }

            $sQuery = "SELECT m.id AS idmedida, mxp.id AS idmedidapersona, 
                                mxp.mdpfme AS fechamedida, mxp.mdphme AS horamedida,
                                CONCAT(mxp.mdpfme, ' ', DATE_FORMAT(mxp.mdphme, '%H:%i')) AS fechahoramedida, 
                                CONCAT_WS('-', m.medcod, mednom) AS medida, 
                                u.documento, u.codigo AS codigousuario, CONCAT(u.codigo, ' - ', u.descripcion) as nombreusuario, FORMAT(mxp.mdpvme, 2) AS valor, 
                                CONCAT(ur.codigo, ' - ', ur.descripcion) AS personaregistro, CONCAT(mxp.Fecha_data, ' ', DATE_FORMAT(mxp.Hora_data, '%H:%i')) AS fecharegistro
                        FROM ".$wbasedato."_000002 mxp
                        INNER JOIN ".$wbasedato."_000001 m ON (mxp.mdpimp = m.id)
                        LEFT JOIN usuarios u ON (mxp.mdpcpm = u.Codigo)
                        LEFT JOIN usuarios ur ON (SUBSTRING(mxp.Seguridad, LOCATE('-', mxp.Seguridad)+1, LENGTH(mxp.Seguridad)) = ur.Codigo)
                        WHERE 1=1 ".$sBusqueda."
                        ORDER BY fechahoramedida DESC";

            //Uno a uno
            $resultado_query = mysqli_query($this->dbConection, $sQuery);
            $aMedidasxPersona = mysqli_fetch_all($resultado_query, MYSQLI_ASSOC);

            if($bGuardarBusqueda)
            {
                $bResultadoGuardarBusquedas = $this->saveBusqueda($sTipoBusqueda, $sValorBusqueda, $iIdMedida, $aMedidasxPersona);
            }
            
            $this->sMensaje = 'Todos las medidas por persona cargadas satisfactoriamente';

            return $aMedidasxPersona;
        }

        /**
         * Función para guardar búsqueda realizada
         * @by: sebastian.nevado
         * @date: 2021/04/30
         * @return: boolean
         */
        public function saveBusqueda($sTipoBusqueda = null, $sValorBusqueda = null, $iIdMedida = null, $aMedidasxPersona = array())
        {
            //Obtengo el alias por aplicación
            $wbasedato = consultarAliasPorAplicacion($this->dbConection, $this->wemp_pmla, $this->nombreAplicacion);
            $sCodigoSolicitante = isset($_SESSION['usera']) ? $_SESSION['usera'] : null;

            //Recorro los resultados de búsqueda y los guardo como evidencia que la persona los observó
            foreach ($aMedidasxPersona as $oMedidaxPersona)
            {
                $sQueryInsert = "INSERT INTO ".$wbasedato."_000004
                                        (Medico, Fecha_data, Hora_data, 
                                        cmpccc, cmptbc, cmpcpb, 
                                        cmpimc, cmpimp, 
                                        cmpvme, cmpume, 
                                        cmpfme, cmphme, 
                                        cmpcpc, Seguridad)
                                    VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".
                                            $sValorBusqueda."', '".$sTipoBusqueda."', '".$oMedidaxPersona['codigousuario']."', ".
                                            $oMedidaxPersona['idmedida'].", ".$oMedidaxPersona['idmedidapersona'].", ".
                                            $oMedidaxPersona['valor'].", '".$oMedidaxPersona['unidadmedida']."', '".
                                            $oMedidaxPersona['fechamedida']."', '".$oMedidaxPersona['horamedida']."', '".
                                            $sCodigoSolicitante."', '".$this->sSeguridad."')";
                
                //Guardo en base de datos el envío de correo
                $res = mysqli_query($this->dbConection, $sQueryInsert) or die("Error: " . mysql_errno() . " - en el query (Insertar En ".$wbasedato."_000004 por primera vez): " . $sQueryInsert . " - " . mysql_error());
            }

            //Guardo en base de datos el envío de correo
            $this->sMensaje = 'Todos los resultados de búsqueda de medidas por persona guardadas satisfactoriamente';

            return true;
        }

    }

?>