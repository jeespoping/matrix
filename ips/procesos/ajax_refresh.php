<?php
include_once("conex.php"); //publicacion en matrix
include_once("root/comun.php"); //publicacion en matrix
$conex = obtenerConexionBD("matrix");
$keyword = '%'.$_POST['keyword'].'%';

$sql = mysql_queryV("select * from patol_000024 WHERE (Empnom LIKE '$keyword') OR (Empcod LIKE '$keyword') AND Empest = 'on' ORDER BY Empcod ASC LIMIT 0, 10");
while($rs = mysql_fetch_array($sql))
{
    $country_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs['Empcod'].' - '.$rs['Empnom']);
    echo '<li onclick="set_item(\''.str_replace("'", "\'",$rs['Empcod'].' - '.$rs['Empnom']).'\')">'.$country_name.'</li>';
}



/*
// PDO connect *********
function connect() {
    return new PDO('mysql:host=132.1.18.85;dbname=matrix', 'root', 'q6@nt6m', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}

$pdo = connect();
$keyword = '%'.$_POST['keyword'].'%';
$sql = "select * from patol_000024 WHERE (Empnom LIKE (:keyword)) OR (Empcod LIKE (:keyword)) AND Empest = 'on' ORDER BY Empcod ASC LIMIT 0, 10";
$query = $pdo->prepare($sql);
$query->bindParam(':keyword', $keyword, PDO::PARAM_STR);
$query->execute();
$list = $query->fetchAll();
foreach ($list as $rs) {
    // put in bold the written text
    $country_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs['Empcod'].' - '.$rs['Empnom']);
    // add new option
    echo '<li onclick="set_item(\''.str_replace("'", "\'",$rs['Empcod'].' - '.$rs['Empnom']).'\')">'.$country_name.'</li>';
}
*/
?>