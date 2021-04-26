<?php
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
         * @Name: sIdUnidad
         * @Type: string
         */
        private $sIdUnidad;

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
            //Valores por defecto
            $this->dbConection = $conex;
            $this->iId = null;
            $this->sCodigo = null;
            $this->sNombre = null;
            $this->sDescripcion = null;
            $this->sIdUnidad = null;
            $this->bEnviarNotificacion = null;
            $this->sMensaje = null;
            $this->wemp_pmla = $wemppmla;
            $this->nombreAplicacion = 'radiolog';
            $this->sSeguridad = isset($_SESSION['usera']) ? "C-".$_SESSION['usera'] : null;
        }

        /**
         * Funcion para obtener el id
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getId()
        {
            return $this->iId;
        }

        /**
         * Funcion para setear el id
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: iId
         */
        public function setId($iId)
        {
            $this->iId = $iId;
        }

        /**
         * Funcion para obtener el código
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getCodigo()
        {
            return $this->sCodigo;
        }

        /**
         * Funcion para setear el código
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: sCodigo
         */
        public function setCodigo($sCodigo)
        {
            $this->sCodigo = $sCodigo;
        }

        /**
         * Funcion para obtener el nombre
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getNombre()
        {
            return $this->sNombre;
        }

        /**
         * Funcion para setear el nombre
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: sNombre
         */
        public function setNombre($sNombre)
        {
            $this->sNombre = $sNombre;
        }

        /**
         * Funcion para obtener el descripción
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getDescripcion()
        {
            return $this->sDescripcion;
        }

        /**
         * Funcion para setear la descripción
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: sDescripcion
         */
        public function setDescripcion($sDescripcion)
        {
            $this->sDescripcion = $sDescripcion;
        }

        /**
         * Funcion para obtener el idUnidad
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getIdUnidad()
        {
            return $this->sIdUnidad;
        }

        /**
         * Funcion para setear el idUnidad
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: sIdUnidad
         */
        public function setIdUnidad($sIdUnidad)
        {
            $this->sIdUnidad = $sIdUnidad;
        }

        /**
         * Funcion para obtener el envío de notificación por correo
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getEnviarNotificacion()
        {
            return $this->bEnviarNotificacion;
        }

        /**
         * Funcion para setear el envío de notificación por correo
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: bEnviarNotificacion
         */
        public function setEnviarNotificacion($bEnviarNotificacion)
        {
            $this->bEnviarNotificacion = $bEnviarNotificacion;
        }

        /**
         * Funcion para obtener el código de seguridad
         * @by: sebastian.nevado
         * @date: 2021/04/23
         */
        public function getSeguridad()
        {
            return $this->sSeguridad;
        }

        /**
         * Funcion para setear el envío de notificación por correo
         * @by: sebastian.nevado
         * @date: 2021/04/23
         * @params: sSeguridad
         */
        public function setSeguridad($sSeguridad)
        {
            $this->sSeguridad = $sSeguridad;
        }

        /**
         * Funcion para obtener el mensaje
         * @by: sebastian.nevado
         * @date: 2021/04/21
         */
        public function getMensaje()
        {
            return $this->sMensaje;
        }

        /**
         * Funcion para setear el mensaje
         * @by: sebastian.nevado
         * @date: 2021/04/21
         * @params: sMensaje
         */
        public function setMensaje($sMensaje)
        {
            $this->sMensaje = $sMensaje;
        }

        /**
         * Funcion para verificar integridad de la medida antes de guardar
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
            if(!isset($this->sIdUnidad) || is_null($this->sIdUnidad)  || strlen($this->sIdUnidad)<1)
            {
                $this->sMensaje = 'El campo "Unidad" es obligatorio';
                return false;
            }
            
            return true;
            
        }

        /**
         * Funcion para guardar medida en la base de datos
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
                                    meduni = '".$this->sIdUnidad."', medenc = '".$this->bEnviarNotificacion."', Seguridad = '".$this->sSeguridad."'
                                WHERE id = ".$this->iId;

                $res = mysql_query($sQueryUpdate,$this->dbConection) or die("Error: " . mysql_errno() . " - en el query (Insertar En ".$wbasedato."_000001 por primera vez): " . $sQueryUpdate . " - " . mysql_error());

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
                                    '".$this->sDescripcion."','".$this->sIdUnidad."','".$this->bEnviarNotificacion."','".$this->sSeguridad."')";
                $res = mysql_query($sQueryInsert,$this->dbConection) or die("Error: " . mysql_errno() . " - en el query (Insertar En ".$wbasedato."_000001 por primera vez): " . $sQueryInsert . " - " . mysql_error());

                $this->sMensaje = 'Medida insertada satisfactoriamente';

                $this->iId = mysql_insert_id($this->dbConection);

                return true;
            }
        }

        /**
         * Funcion para cargar la medida de la base de datos
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

                $err1 = mysql_query($sQuery,$this->dbConection);
                $row1 = mysql_fetch_assoc($err1);

                $this->sCodigo = htmlentities($row1['medcod']);
                $this->sNombre = htmlentities($row1['mednom']);
                $this->sDescripcion = htmlentities($row1['meddes']);
                $this->sIdUnidad = htmlentities($row1['meduni']);
                $this->bEnviarNotificacion = htmlentities($row1['medenc']);

                $this->sMensaje = 'Medida cargada satisfactoriamente';

                return true;
            } else {
                
                $this->sMensaje = 'No se envió la identificación de la medida';

                return false;
            }
        }

        /**
         * Funcion para cargar listado de medidas de la base de datos
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
            $resultado_query = mysql_query($sQuery,$this->dbConection);
            $aMedidas = mysqli_fetch_all($resultado_query, MYSQLI_ASSOC);
            
            $this->sMensaje = 'Todos las medidas cargadas satisfactoriamente';

            return $aMedidas;
        }

        /**
         * Funcion para guardar medidaxpersona en la base de datos
         * @by: sebastian.nevado
         * @date: 2021/04/26
         * @return: Boolean
         */
        public function saveMedidaxPersona($sCodigoPersona, $dFechaMedida, $dHoraMedida, $dValorMedida, $iIdMedidaxPersonal = null, $iIdMedida = null)
        {
            //Obtengo el alias por aplicación
            $this->iId = (!is_null($iIdMedida)) ? $iIdMedida : $this->iId;
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

                $res = mysql_query($sQueryUpdate,$this->dbConection) or die("Error: " . mysql_errno() . " - en el query (Insertar En ".$wbasedato."_000002 por primera vez): " . $sQueryUpdate . " - " . mysql_error());

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
                $res = mysql_query($sQueryInsert,$this->dbConection) or die("Error: " . mysql_errno() . " - en el query (Insertar En ".$wbasedato."_000002 por primera vez): " . $sQueryInsert . " - " . mysql_error());

                $this->sMensaje = 'Medida por persona insertada satisfactoriamente';

                $iIdMedidaxPersonal = mysql_insert_id($this->dbConection);

                return true;
            }
        }

        /**
         * Funcion para verificar integridad de la medidaxpersona antes de guardar
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
                $this->sMensaje = 'El campo "Fecha" es obligatorio';
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
                $this->sMensaje = 'El campo "Medida" es obligatorio';
                return false;
            }
            
            return true;
            
        }

    }

?>