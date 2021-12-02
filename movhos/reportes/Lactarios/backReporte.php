<?php 
$consultaAjax = '';

include_once("conex.php");
include_once("root/comun.php");


$wactualiz = "2018-08-13";

 if(!isset($_SESSION['user'])){
	  echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>Error, inicie nuevamente</td></tr>
		</table></center>";
	  return;
 }

 function get_data($fecha_ini, $fecha_fin){
    $q = "SELECT 
            CONCAT(Entart, '-', Entdes)Entart, Enthis, Enting, CONCAT(Pacno1 ,' ',Pacno2 ,' ' ,Pacap1, ' ',Pacap2 ) Entnom, Entcan, CONCAT(Entusu,' - ',a.Descripcion) Entusu, CONCAT(Enture,' - ',b.Descripcion) Enture, e.Fecha_data Entfec, e.Hora_data Enthor, if(Entest = 'on', '<a class=entregado >Entregrado</a>', '<a class=cancelado>Cancelado</a>') as Entest
          FROM
            movhos_000298 e
          JOIN cliame_000100 ON Enthis = Pachis
          JOIN usuarios a ON Entusu = a.Codigo 
          JOIN usuarios b ON Enture = b.Codigo 
          WHERE e.Fecha_data between '{$fecha_ini}' AND '{$fecha_fin}'
          ORDER BY e.Fecha_data desc, e.Hora_data desc;"; 
    //echo $q;
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $datos = array();
    while ($row = mysql_fetch_array($res)) {
        $datos[]= $row;
        
    }
    return $datos;
}
 switch ($_POST['accion']) {
     case 'consultar':
        $res = get_data($_POST['fecha_ini'], $_POST['fecha_fin']);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('datos' => $res,
                                'status' => true ));
         break;
     
     default:
         break;
 }


?>