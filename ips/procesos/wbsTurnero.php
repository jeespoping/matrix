<?php
    include_once("conex.php");
    include("root/comun.php");
    ob_end_clean();
    if ($_SERVER['REQUEST_METHOD'] == 'GET')
    {
        $wemp_pmla = $_GET['wemp_pmla'];
        $tema = $_GET['tema'];
        $funcion = $_GET['funcion'];
        if ($funcion == 'listaTurnos')
        {        
            $objTurnos = new listarTurnos($wemp_pmla, $tema);
            header("HTTP/1.1 200 OK");
            // echo Requerido para devolver el resultado
            echo json_encode($objTurnos->arrFilasTur);
        }
        else
        {
            $objAlertas = new listarAlertas($wemp_pmla, $tema);
            header("HTTP/1.1 200 OK");
            echo json_encode($objAlertas->arrAlerta);
        }
	    exit();
    } 
    // En caso de que ninguna de las opciones anteriores se haya ejecutado
    header("HTTP/1.1 400 Bad Request"); 
    exit();
    // Se define la clase que va a contener los 3 campos
    class turno
    {
        public $Turno;
        public $Modulo;
        public $Prioridad;
    }
    class alertas
    {
        public $turnoAlerta;
        public $moduloAlerta;
        public $prioridadAlerta;
    }
    class listarAlertas
    {
        public $wemp_pmla;
        public $tema;   
        public $arrAlerta = Array();
        public function __construct ($wemp_pmla, $tema)
        {
            $conex = obtenerConexionBD("matrix");	
            $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
            // original ojo A.Fecha_data >= '".date("Y-m-d",strtotime("-1 day"))."'
            $sqlAlertas = "SELECT A.Fecha_data, A.Hora_data, Turtur, Puenom, Conpri, Puetem, Turllv
            FROM ".$wbasedato."_000304 AS A INNER JOIN ".$wbasedato."_000301 ON(Turtem = Puetem AND Turven = Puecod)
			INNER JOIN ".$wbasedato."_000299 ON(Turupr = Concod) 
            WHERE A.Fecha_data >= '".date("Y-m-d",strtotime("-1 day"))."'
            AND Turtem = '".$tema."'
            AND Turest = 'on'
            AND Turllv = 'on'";
            $resAlertas = mysql_query($sqlAlertas, $conex ) or die("<b>ERROR EN QUERY MATRIX(sqlAlertas):</b><br>".mysql_error());
            // echo ($sqlAlertas);
            while ($rowAlerta = mysql_fetch_array($resAlertas))
            {
                // --> Solo mostrar turnos de maximo 12 horas atras
                if(strtotime($rowAlerta['Fecha_data']." ".$rowAlerta['Hora_data']) < strtotime('-12 hours'))
				    continue;
                $objAlerta = new alertas();
                $tmpturno = substr($rowAlerta['Turtur'], 7);
                $tmpturno = substr($tmpturno, 0, 2)." ".substr($tmpturno, 2, 5);
                // se llena cada campo del objeto temporal de alerta de la clase alerta
                $objAlerta->turnoAlerta = $tmpturno;
                $objAlerta->moduloAlerta = utf8_encode($rowAlerta["Puenom"]);
                $objAlerta->prioridadAlerta = utf8_encode($rowAlerta["Conpri"]);
                $this->arrAlerta[]= $objAlerta;
            }
        }
    }
    class listarTurnos 
    {	
        public $wemp_pmla;
        public $tema;
        public $arrFilasTur = Array();
        public function __construct ($wemp_pmla, $tema) 
        {
            $conex = obtenerConexionBD("matrix");	
            $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
            // original ojo A.Fecha_data >= '".date("Y-m-d",strtotime("-1 day"))."'
            $sqlTurnos = "SELECT A.Fecha_data, A.Hora_data, Turtur, Conpri, Puenom
            FROM ".$wbasedato."_000304 AS A INNER JOIN ".$wbasedato."_000299 ON(Turupr = Concod)
                   INNER JOIN ".$wbasedato."_000301 ON(Turtem = Puetem AND Turven = Puecod)
            WHERE A.Fecha_data >= '".date("Y-m-d",strtotime("-1 day"))."'
            AND Turtem = '".$tema."'
            AND Turest = 'on'
            AND Turpat = 'on'
            ORDER BY REPLACE(Turtur, '-', '')*1 ASC";
            $resTurnos = mysql_query($sqlTurnos, $conex ) or die("<b>ERROR EN QUERY MATRIX(sqlturnos):</b><br>".mysql_error());
            // echo ($sqlTurnos);
            while ($rowTurnos = mysql_fetch_array($resTurnos))
            {
                // --> Solo mostrar turnos de maximo 12 horas atras
			    if(strtotime($rowTurnos['Fecha_data']." ".$rowTurnos['Hora_data']) < strtotime('-12 hours'))
                     continue;
                // se define un objeto de la clase turno
                // echo ($sqlTurnos);
                $objTurno = new turno();
                $tmpturno = substr($rowTurnos['Turtur'], 7);
                $tmpturno = substr($tmpturno, 0, 2)." ".substr($tmpturno, 2, 5);
                // se llena cada campo del objeto temporal de turno de la clase turno
                $objTurno->Turno = $tmpturno;
                $objTurno->Modulo = utf8_encode($rowTurnos["Puenom"]);
                $objTurno->Prioridad = utf8_encode($rowTurnos["Conpri"]);
                // echo (utf8_encode($rowTurnos["Conpri"])."<BR>");
                // se lleva al arreglo el objeto turno
                $this->arrFilasTur[]= $objTurno;
                // echo ($tmpturno."<BR>");
            }  
             // var_dump($arrFilasTur); 
        }
    }
?>
