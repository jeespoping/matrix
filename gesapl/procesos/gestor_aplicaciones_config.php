<?php
include_once("conex.php");
/**
    El include a este archivo debe hacerce inmediatamente despues de "
"
*/
define("HOST_MATRIX",$_SERVER['HTTP_HOST']);// ruta raiz host.
define("RAIZ_MATRIX",$_SERVER['HTTP_HOST'].'/matrix');// ruta raiz de matrix con host.
$URL_ACTUAL = 'NO URL ACTUAL';
$URL_REFERIDA = 'NO URL REFERIDA';
$URL_AUTOLLAMADO = 'NO URL AUTO';

function subUrlTab($cod_sub_tab)
{
    global $conex;
    $sub_url = '';
    if($cod_sub_tab != '')
    {
        $sqlST = "  SELECT  Tabcod, Taburl
                    FROM    root_000080
                    WHERE   Tabcod = '".$cod_sub_tab."'";
        $resST = mysql_query($sqlST,$conex) or die("Error: " . mysql_errno() . " - en el query consultar informacion de pestaña: ".$sqlST." - ".mysql_error());
        $row = mysql_fetch_array($resST);
        $sub_url = $row['Taburl'];
    }
    // $sub_url = "procesos/";
    return $sub_url;
}

if(array_key_exists('SCRIPT_NAME',$_SERVER) && array_key_exists('HTTP_REFERER',$_SERVER))
{
    $REFERIDA = $_SERVER['HTTP_REFERER'];// ruta raiz de matrix con host.
    $URL_ACTUAL = $_SERVER['SCRIPT_NAME'];// ruta raiz de matrix con host.

    $raiz = "://".HOST_MATRIX;
    //echo '>>'.strpos($REFERIDA,$raiz);
    if(strpos($REFERIDA,$raiz) !== false)
    {
        $pos = strpos($REFERIDA,$raiz);
        $URL_REFERIDA = substr($REFERIDA,$pos+strlen($raiz));
    }

    if ($URL_ACTUAL != $URL_REFERIDA)
    {
		$URL_AUTOLLAMADO = $URL_ACTUAL;
        $explodeREF = explode('/',$URL_REFERIDA);
        $explodeACT = explode('/',$URL_ACTUAL);
        $diferencia = array();
        // print_r($explodeREF);
        $excluir = (count($explodeREF) > 1 && strtolower($explodeREF[1]) == 'matrix') ? 'matrix/': 'include/';
        foreach($explodeREF as $key => $dir)
        {

            //echo $explodeREF[$key] .'--'. $explodeACT[$key].'<<<<br>';
            if($explodeREF[$key] != $explodeACT[$key])
            {
                break;
            }
            $diferencia[$key] = $explodeREF[$key];
        }

        $retroceder = '';
        for($i=0 ; $i<count($diferencia) ; $i++)
        {
            $retroceder .= '../';
        }
        $wroot_group = (!isset($wroot_group)) ? '': $wroot_group;
        $URL_ACTUAL = str_replace($excluir,'',$retroceder.$wroot_group);
        $wcodtab = (!isset($wcodtab)) ? '': $wcodtab;
        //$URL_AUTOLLAMADO = str_replace($excluir,'',$retroceder.$wroot_group).subUrlTab($wcodtab);
    }
}

// $wfunciones = (isset($wfunciones) && $wfunciones != '') ? "../".$wfunciones: RAIZ_MATRIX.'/gesapl/procesos';
// define("FUNCIONES_GESTION",$wfunciones);// ruta raiz de matrix.
global $URL_ACTUAL,$URL_AUTOLLAMADO;

// echo HOST_MATRIX.'<br>';
// echo RAIZ_MATRIX.'<br>---------<br>';
// echo '<div align="left"><pre>';
// echo $URL_REFERIDA.'<br>';
// echo $URL_ACTUAL.'<br>';
// echo $URL_AUTOLLAMADO.'<br>';
// echo '</pre></div>';




?>