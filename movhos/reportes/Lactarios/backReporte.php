<?php 

/**DESCRIPCIÓN 27 DE OCTUBRE DEL 2021 
 * Este archivo se basa en la creación de lo que es el backend del reporte, es en donde creamos la consulta 
 * para consultar los datos y mostrarlos.
*/

$consultaAjax = '';

include_once("conex.php");
include_once("root/comun.php");


$wactualiz="Abril 08 de 2022"; 

 if(!isset($_SESSION['user'])){
	  echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>Error, inicie nuevamente</td></tr>
		</table></center>";
	  return;
 }
/*
* Filtro sede
*/
 $selectsede = '';
 if(isset($_POST['selectsede']) && !empty($_POST['selectsede'])){
   $selectsede = $_POST['selectsede'];
 }

 function get_data($fecha_ini, $fecha_fin,$selectsede){
   if(empty($selectsede)){
     
     $q = "SELECT 
            CONCAT(Entart, '-', Entdes)Entart, Enthis, Enting, CONCAT(Pacno1 ,' ',Pacno2 ,' ' ,Pacap1, ' ',Pacap2 ) Entnom, Entcan, CONCAT(Entusu,' - ',a.Descripcion) Entusu, CONCAT(Enture,' - ',b.Descripcion) Enture, e.Fecha_data Entfec, e.Hora_data Enthor, if(Entest = 'on', '<a class=entregado >Entregrado</a>', '<a class=cancelado>Cancelado</a>') as Entest
          FROM
            movhos_000298 e
          JOIN cliame_000100 ON Enthis = Pachis
          JOIN usuarios a ON Entusu = a.Codigo 
          JOIN usuarios b ON Enture = b.Codigo 
          WHERE e.Fecha_data between '{$fecha_ini}' AND '{$fecha_fin}'
          ORDER BY e.Fecha_data desc, e.Hora_data desc;"; 
    }
    else {
      $q = "SELECT
            CONCAT(Entart, '-', Entdes)Entart, Enthis, Enting, CONCAT(Pacno1 ,' ',Pacno2 ,' ' ,Pacap1, ' ',Pacap2 ) Entnom, Entcan, CONCAT(Entusu,' - ',a.Descripcion) Entusu, CONCAT(Enture,' - ',b.Descripcion) Enture, e.Fecha_data Entfec, e.Hora_data Enthor, if(Entest = 'on', '<a class=entregado >Entregrado</a>', '<a class=cancelado>Cancelado</a>') as Entest
          FROM
            movhos_000298 e
          JOIN cliame_000100 ON Enthis = Pachis
          JOIN usuarios a ON Entusu = a.Codigo 
          JOIN usuarios b ON Enture = b.Codigo 
          JOIN movhos_000020 m20 ON m20.Habhis = e.Enthis 	
          JOIN movhos_000011 m11 ON m11.Ccocod = m20.Habcco
          WHERE e.Fecha_data between '{$fecha_ini}' AND '{$fecha_fin}'
          AND  m11.Ccosed = '".$selectsede."'
          ORDER BY e.Fecha_data desc, e.Hora_data desc;";  
    }

    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $datos = array();
    while ($row = mysql_fetch_array($res)) {
        $datos[]= $row;
        
    }
    return $datos;
}


 switch ($_POST['accion']) {
     case 'consultar':
        $res = get_data($_POST['fecha_ini'], $_POST['fecha_fin'],$selectsede);
        header('Content-Type: application/json; charset=utf-8');
        $array = utf8ize2(array('datos' => $res, 'status' => true ));
        echo json_encode($array);
         break;
     
     default:
         break;
 }


?>