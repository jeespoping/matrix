<?php
include_once("conex.php");
include_once("root/comun.php");
include_once("citas/funcionesAgendaCitas.php");
$wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;
$this->dbConection = obtenerConexionBD("matrix");
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
     * @Name: sMensaje
     * @Type: date
     */
    private $sMensaje;

    /**
     * Constructor de la clase
     */
    public function __construct()
    {
        //Valores por defecto
        $this->dbConection = obtenerConexionBD("matrix");
        $this->iId = null;
        $this->sCodigo = null;
        $this->sNombre = null;
        $this->sDescripcion = null;
        $this->sIdUnidad = null;
        $this->bEnviarNotificacion = null;
        $this->sMensaje = null;
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
    public function getEnviarConfirmacion()
    {
        return $this->bEnviarNotificacion;
    }

    /**
     * Funcion para setear el envío de notificación por correo
     * @by: sebastian.nevado
     * @date: 2021/04/21
     * @params: bEnviarNotificacion
     */
    public function setEnviarConfirmacion($bEnviarNotificacion)
    {
        $this->bEnviarNotificacion = $bEnviarNotificacion;
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
            $this->sMensaje = 'El campo "Código" es obligatorio';
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
            $this->sMensaje = 'El campo "Apellidos" es obligatorio';
            return false;
        }

        //Valido descripcion
        if(!isset($this->sDescripcion) || is_null($this->sDescripcion)  || strlen($this->sDescripcion)<1)
        {
            $this->sMensaje = 'El campo "Descripción" es obligatorio';
            return false;
        }

        //Valido Enviar Notificación
        if(!isset($this->bEnviarNotificacion) || is_null($this->bEnviarNotificacion)  || strlen($this->bEnviarNotificacion)<1)
        {
            $this->sMensaje = 'El campo "Enviar Notificación" es obligatorio';
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
                                meduni = '".$this->sIdUnidad."', medenc = '".$this->bEnviarNotificacion."', Seguridad = 'C-".$wbasedato."'
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
                                '".$this->sDescripcion."','".$this->sIdUnidad."','".$this->bEnviarNotificacion."','C-".$wbasedato."')";
            $res = mysql_query($sQueryInsert,$this->dbConection) or die("Error: " . mysql_errno() . " - en el query (Insertar En ".$wbasedato."_000001 por primera vez): " . $sQueryInsert . " - " . mysql_error());

            $this->sMensaje = 'Medida insertada satisfactoriamente';

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

}