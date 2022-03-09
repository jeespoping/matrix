<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>CARGO A PACIENTE DESDE CENTRAL</title>
 <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo3{color:#003366;background:#A4E1E8;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo4{color:#FFFFFF;background:green;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo5{color:#FFFFFF;background:red;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#FFDBA8;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}	
    	.texto2{color:#003366;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto3{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto4{color:#003366;background:#f5f5dc;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto5{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	
   </style>
   
    <script type="text/javascript">
   function enter()
   {
   	window.opener.document.producto.submit();
   	window.close();
   }
    </script>
    
</head>
<body>
<?php
include_once("conex.php");
/***
 * @modified Diciembre 14 de 2021 (Juan Rodriguez): Se modifica parámetro wemp_pmla quemado, se rectifica que desde donde se llama al archivo, exista wemp_pmla
 * 
 * @modified Agosto 14 de 2013	(Edwin MG)	Se valida que halla conexión unix en inventario desde matrix, si no hay conexión
 *											con unix se activa la contigencia de dispensación.
 * @modified Febrero 25 de 2013 (Edwin MG). Cambios varios para cuando no hay conexión con UNIX. Entre ellos se registra el movimiento en tabla de paso
 *											y se mira los saldos en matrix y no en UNIX.
 * Modificación.
 * Fecha:		2009-10-01
 * Autor:		Edwin Molina Grisales.
 * Descripcion:	Se agrega función para registrar en el KE.  Si un paciente tiene KE
 * 				y se le devulve un articulo debe descontar en uno la cantidad dispensada.
 */


function descargarCarro2( $cod, $codari, $his, $ing, $cco ){
	
	global $conex;
	global $bd;
	
	$expcco = explode( "-", $cco );
	
	//se hace la devolucion del carro
	//if($tipTrans == 'Anulado')//se debe poner !=C
	$sql = "SELECT
				ccofca
			FROM
			".$bd."_000011
			WHERE
				ccocod = '".$expcco[0]."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$rows = mysql_fetch_array( $res );
	
	
	//aca voy a consultar cuantos elementos estan en el carro y son de la central
	$q = "SELECT Fdenum "
	."        FROM ".$bd."_000002, ".$bd."_000003 "
	."       WHERE Fenhis=".$his." "
	."         AND Fening=".$ing." "
	."         AND Fencco='".$expcco[0]."' "
	."         AND Fenfue='{$rows[0]}' "
	."         AND Fdenum=Fennum "
	."         AND Fdeari='".$codari."' "
	."         AND Fdeart='".$cod."' "
	."         AND Fdedis='on' "
	."         AND Fdeest='on' ";

	$err1 = mysql_query($q,$conex);
	echo mysql_error();
	$num1 = mysql_num_rows($err1);
	
	if($num1 >0)
	{
		$row1=mysql_fetch_array($err1);
		if($num1 >0)
		{
			$q = " UPDATE ".$bd."_000003 "
			."    SET fdedis = 'off', "
			."        fdecad = fdecan "
			."  WHERE Fdenum = ".$row1[0]
			."         AND Fdeari='".$codari."' "
			."         AND Fdedis='on' "
			."         AND Fdeest='on' ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		}
	}
}

/**
 * 
 */

function descargarCarro( $cod, $his, $ing, $cco ){
	
	global $conex;
	global $bd;
	
	$expcco = explode( "-", $cco );
	
	//se hace la devolucion del carro
	//if($tipTrans == 'Anulado')//se debe poner !=C
	$sql = "SELECT
				ccofca
			FROM
			".$bd."_000011
			WHERE
				ccocod = '".$expcco[0]."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$rows = mysql_fetch_array( $res );
	
	//aca voy a consultar cuantos elementos estan en el carro y son de la central
	$q = "SELECT Fdenum "
	."        FROM ".$bd."_000002, ".$bd."_000003 "
	."       WHERE Fenhis=".$his." "
	."         AND Fening=".$ing." "
	."         AND Fencco='".$expcco[0]."' "
	."         AND Fenfue='{$rows[0]}' "
	."         AND Fdenum=Fennum "
	."         AND Fdeari='".$cod."' "
	."         AND Fdedis='on' "
	."         AND Fdeest='on' ";

	$err1 = mysql_query($q,$conex);
	echo mysql_error();
	$num1 = mysql_num_rows($err1);
	
	if($num1 >0)
	{
		$row1=mysql_fetch_array($err1);
		if($num1 >0)
		{
			$q = " UPDATE ".$bd."_000003 "
			."    SET fdedis = 'off', "
			."        fdecad = fdecan "
			."  WHERE Fdenum = ".$row1[0]
			."         AND Fdeari='".$cod."' "
			."         AND Fdedis='on' "
			."         AND Fdeest='on' ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		}
	}
}


/**
 * Registra un articulo al paciente en el kardex electrónico.  Si es una devolucion
 * descuenta en uno la cantidad dispensada, si es un cargo aumenta en uno la cantidad
 * dispensada.
 * 
 * @param $art		Codigo del Aritculo del paciente
 * @param $his		Historia del paciente
 * @param $ing		Ingreso del paciente
 * @return bool		True si es verdaderos
 */
function registrarArticuloKE( $art, $his, $ing, $dev = false ){
	
	global $conex;
	global $bd;
	
	if( !$dev ){
		$sqlid="SELECT 
					max(id) 
				FROM 
				".$bd."_000054 
				WHERE 
					kadart = '$art'
					AND kadcdi > kaddis+0
					AND kadfec = '".date("Y-m-d")."'
					AND kadhis = '$his'  
		       		AND kading = '$ing'
		       		AND kadsus != 'on'
		       		AND kadcon = 'on'
				GROUP BY kadart";
		
		$resid = mysql_query( $sqlid, $conex ) or die(mysql_errno()." - en el query: ".$sqlid." - ".mysql_error());;
		
		if( $row = mysql_fetch_array( $resid ) ){
			$id = $row[0];
		}
		else{
			$row[0] = "";
			return false;
		}
		
		//Actualizando registro con el articulo cargado
		$sql = "UPDATE 
					".$bd."_000054 
		       	SET 
		       		kaddis = kaddis+1,
		       		kadhdi = '".date("H:i:s")."'
		        WHERE 
		        	kadcdi >= kaddis+1  
		       		AND kadart = '$art' 
		       		AND kadhis = '$his'  
		       		AND kading = '$ing'
		       		AND kadfec = '".date("Y-m-d")."'
		       		AND kadsus != 'on'
		       		AND kadcon = 'on'
		       		AND kadori = 'CM' 
		       		AND id = {$row[0]}";
		
		$res = mysql_query( $sql, $conex ) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());
		
		if( $res && mysql_affected_rows() > 0 )
			return true;
		else
			return false;
	}
	else{
		$sqlid="SELECT 
					max(id), kaddis 
				FROM 
				".$bd."_000054 
				WHERE 
					kadart = '$art'
					AND kaddis > 0
					AND kadfec = '".date("Y-m-d")."'
					AND kadhis = '$his'  
		       		AND kading = '$ing'
		       		AND kadcon = 'on'
				GROUP BY kadart";
		
		$resid = mysql_query( $sqlid, $conex ) or die(mysql_errno()." - en el query: ".$sqlid." - ".mysql_error());;
		
		if( $row = mysql_fetch_array( $resid ) ){
			$id = $row[0];
		}
		else{
			$row[0] = "";
			return false;
		}
		
		if( $row[1] > 1 ){
			//Actualizando registro con el articulo cargado
			$sql = "UPDATE 
						".$bd."_000054 
			       	SET 
			       		kaddis = kaddis-1
			        WHERE 
			        	kaddis > 0  
			       		AND kadart = '$art' 
			       		AND kadhis = '$his'  
			       		AND kading = '$ing'
			       		AND kadfec = '".date("Y-m-d")."'
			       		AND kadcon = 'on'
			       		AND kadori = 'CM' 
			       		AND id = {$row[0]}";
		}
		else{
			//Actualizando registro con el articulo cargado
			$sql = "UPDATE 
						".$bd."_000054 
			       	SET 
			       		kaddis = kaddis-1,
			       		kadhdi = '00:00:00'
			        WHERE 
			        	kaddis > 0  
			       		AND kadart = '$art' 
			       		AND kadhis = '$his'  
			       		AND kading = '$ing'
			       		AND kadfec = '".date("Y-m-d")."'
			       		AND kadcon = 'on'
			       		AND kadori = 'CM' 
			       		AND id = {$row[0]}";
		}
		
		$res = mysql_query( $sql, $conex ) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());
		
		if( $res && mysql_affected_rows() > 0 )
			return true;
		else
			return false;
	}
}

/**
* Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts 
* existen dos opciones mandandole el paramentro tipo=C o para=A, asi ese Script realizara una u otra opcion
*/
function pintarTitulo()
{
    echo "<table ALIGN=CENTER width='50%'>"; 
    // echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
    echo "<tr><td class='titulo1'>DEVOLUCIONES CENTRAL DE MEZCLAS</td></tr>";
    echo "<tr><td class='titulo2'>Fecha: " . date('Y-m-d') . "&nbsp Hora: " . (string)date("H:i:s") . "</td></tr></table></br>";
} 

function pintarConfi($cod, $var, $nom, $sub)
{
    echo "<form name='producto3' action='cargosN.php' method=post>";

    echo "<table ALIGN=CENTER width='50%'>";
    echo "<tr><td class='titulo4'>SE HA REALIZADO EL MOVIMIENTO EXITOSAMENTE</td></tr>";
    echo "<tr><td class='titulo2'>ARTICULO: " . $cod . "-" . $nom . "</td></tr>";
    echo "<tr><td class='titulo2'>" . $sub . ": " . $var . "</td></tr>";
} 

function pintarAlerta($mensaje)
{
    echo "<form name='producto3' action='cargosN.php' method=post>";

    echo "<table ALIGN=CENTER width='50%'>";
    echo "<tr><td class='titulo5'>" . $mensaje . "</td></tr>";
} 

function pintarBoton()
{
    echo "<tr><td >&nbsp;</td></tr>";
    echo "<tr><td ALIGN='CENTER' ><INPUT TYPE='button' NAME='ok' VALUE='ACEPTAR' onclick='enter()'></td></tr>";
    echo "</form>";
} 

/**
* De cada isnumo de consulta que presentacion se cargo y si fue cargada o por ajuste de presentacion
* 
* @param vector $inslis lista de insumos del producto cargado
* @param caracter $cco centro de costos que cargo
* @param caracter $destino historia clinica-ingreso
* @param caracter $ingreso ingreso
* @param caracter $tipo si es de cargo o de averia
* @param caracter $presentaciones devuelve vector con presentaciones para cada insumo
* @param caracter $insumo producto que se cargo
* @param caracter $lote lote que se cargo
* @param caracter $documento numero del movimeinto
* @param caracter $concepto concepto del movmiento
* @return boolean si encuentra las presentaciones para el movmiento
*/
function consultarMovimiento($codigo, $historia, $ingreso, $lote, $cco)
{
    global $conex;
    global $wbasedato;
	global $bd;

    $q = " SELECT Mencon, Mendoc "
     . "        FROM " . $wbasedato . "_000006, " . $wbasedato . "_000007, " . $wbasedato . "_000008 "
     . "      WHERE Mencon= Concod "
     . "            and Mencco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
     . "            and Menccd = '" . $historia . "-" . $ingreso . "' "
     . "            and Conind='-1' "
     . "            and Concar = 'on' "
     . "            and Mdecon= Mencon "
     . "            and Mdedoc = Mendoc "
     . "  		    and Mdeart = '" . $codigo . "' "
     . "            and Mdenlo = '" . $lote . "-" . $codigo . "' "
     . "            and Mdeest = 'on' "
     . "            and Menest = 'on' "
     . "        ORDER BY " . $wbasedato . "_000006.id desc";

    $res1 = mysql_query($q, $conex);
    $row1 = mysql_fetch_array($res1);

    $q = " SELECT Mdeart, Mdepre, Mdecan, Mdepaj, Mdecaj, Mdecto "
     . "        FROM " . $wbasedato . "_000006, " . $wbasedato . "_000007, " . $wbasedato . "_000008 "
     . "      WHERE Mencon= Concod "
     . "            and Mencco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
     . "            and Mendan='" . $row1[0] . "-" . $row1[1] . "' "
     . "            and Mdecon = Mencon "
     . "            and Mdedoc = Mendoc "
     . "            and Mdenlo = '" . $lote . "-" . $codigo . "' "
     . "            and Mdeest = 'on' "
     . "            and Menest = 'on' "
     . "        ORDER BY " . $wbasedato . "_000006.id desc";

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);

    if ($num1 > 0)
    {
        for($i = 0;$i < $num1;$i++)
        {
            $row1 = mysql_fetch_array($res1);

            $q = " SELECT Artcom, Unides "
			 . "        FROM  " . $wbasedato . "_000002, ".$bd."_000027 "
             . "      WHERE Artcod = '" . $row1[0] . "' "
             . "            and Artuni=Unicod "
             . "            and Artest='on' ";

            $res2 = mysql_query($q, $conex);
            $row2 = mysql_fetch_array($res2);

            $inslis[$i]['cod'] = $row1[0];
            $inslis[$i]['nom'] = $row2[0];
            $inslis[$i]['pre'] = $row2[1];
            $inslis[$i]['tot'] = $row1[5];

            if ($row1[1] != '')
            {
                $q = " SELECT Artcom "
				 . "        FROM  ".$bd."_000026 "
                 . "      WHERE Artcod = mid('" . $row1[1] . "',1,instr('" . $row1[1] . "','-')-1) "
                 . "            and Artest='on' ";

                $res2 = mysql_query($q, $conex);
                $row2 = mysql_fetch_array($res2);

                $exp = explode('-', $row1[1]);
                $inslis[$i]['prese']['cod'] = $exp[0];
                $inslis[$i]['prese']['nom'] = $row2[0];
                $inslis[$i]['prese']['can'] = $row1[2];

                $q = " SELECT Appcnv"
                 . "        FROM " . $wbasedato . "_000009 "
                 . "      WHERE Apppre = mid('" . $row1[1] . "',1,instr('" . $row1[1] . "','-')-1) "
                 . "            and Appest='on' ";

                $res2 = mysql_query($q, $conex);
                $row2 = mysql_fetch_array($res2);
                $inslis[$i]['prese']['cnv'] = $row2[0];
            } 
            else
            {
                $inslis[$i]['prese']['cod'] = '';
                $inslis[$i]['prese']['nom'] = '';
                $inslis[$i]['prese']['can'] = 0;
                $inslis[$i]['prese']['cnv'] = 1;
            } 

            if ($row1[3] != '')
            {
                $q = " SELECT Artcom"
				 . "        FROM  ".$bd."_000026 "
                 . "      WHERE Artcod = mid('" . $row1[3] . "',1,instr('" . $row1[3] . "','-')-1) "
                 . "            and Artest='on' ";

                $res2 = mysql_query($q, $conex);
                $row2 = mysql_fetch_array($res2);
                $exp = explode('-', $row1[3]);
                $inslis[$i]['aju']['cod'] = $exp[0];
                $inslis[$i]['aju']['nom'] = $row2[0];
                $inslis[$i]['aju']['can'] = $row1[4];

                $q = " SELECT Appcnv"
                 . "        FROM " . $wbasedato . "_000009 "
                 . "      WHERE Apppre = mid('" . $row1[3] . "',1,instr('" . $row1[3] . "','-')-1) "
                 . "            and Appest='on' ";

                $res2 = mysql_query($q, $conex);
                $row2 = mysql_fetch_array($res2);
                $inslis[$i]['aju']['cnv'] = $row2[0];
            } 
            else
            {
                $inslis[$i]['aju']['cod'] = '';
                $inslis[$i]['aju']['nom'] = '';
                $inslis[$i]['aju']['can'] = 0;
                $inslis[$i]['aju']['cnv'] = 1;
            } 
        } 
        return $inslis;
    } 
    else
    {
        return false;
    } 
} 

function devolverAjuste($total, $presentacion, $cantidad, $historia, $cco, $usuario, $ingreso, $ajuste, $cnv, $ajucod)
{
    global $conex;
    global $wbasedato; 
    // echo 'total' . $total;
    // echo 'ajuste' . $ajuste;
    // echo 'cnv' . $cnv;
    if ($ajuste == '')
    {
        $ajuste = 0;
    } 

    if ($total > 0 or $total != '')
    {
        if ($ajuste > 0)
        {
            $q = "   UPDATE " . $wbasedato . "_000010 "
             . "      SET Ajpcan = (Ajpcan + " . $ajuste . ") "
             . "    WHERE Ajphis= '" . $historia . "' "
             . "      AND Ajping= '" . $ingreso . "' "
             . "      AND Ajpart= '" . $ajucod . "' "
             . "      AND Ajpcco= '" . $cco . "' "
             . "      AND Ajpest = 'on' ";

            $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO ACTUALIZAR EL AJUSTE DE PRESENTACION " . mysql_error());
        } 
        $sum = $cantidad * $cnv + $ajuste - $total; 
        // echo 'suma' . $sum;
        if ($sum > 0)
        {
            $q = "   UPDATE " . $wbasedato . "_000010 "
             . "      SET Ajpcan = (Ajpcan - " . $sum . ") "
             . "    WHERE Ajphis= '" . $historia . "' "
             . "      AND Ajping= '" . $ingreso . "' "
             . "      AND Ajpart= '" . $presentacion . "' "
             . "      AND Ajpcco= '" . $cco . "' "
             . "      AND Ajpest = 'on' ";

            $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO ACTUALIZAR EL AJUSTE DE PRESENTACION " . mysql_error());
        } 
    } 
} 
/**
* se graba un encabezado de entrada a matrix, cuando es f es un concepto que refleja la entrada de cargos a unix
* pero que realmente no mueve inventarios
* 
* Devuelve
* 
* @param caracter $codigo concepto de inventario
* @param caracter $consecutivo numero del documento
* 
* Pide
* @param caracter $cco centro de costos de origen
* @param caracter $cco2 centro de coostos destino para averias y historia-ingreso para cargos
* @param caracter $usuario usuario que graba
* @param caracter $tipo A averia o C cargos a paciente
*/
function grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $cco2, $cco, $usuario, $tipo, $anexo)
{
    global $conex;
    global $wbasedato;

    $q = "lock table " . $wbasedato . "_000008 LOW_PRIORITY WRITE";
    $errlock = mysql_query($q, $conex);

    switch ($tipo)
    {
        case 'C':
            $q = "   UPDATE " . $wbasedato . "_000008 "
             . "      SET Concon = (Concon + 1) "
             . "    WHERE Conind = '1' "
             . "      AND Concar = 'on' "
             . "      AND Conest = 'on' ";
            break;

        case 'F':
            $anexo = $codigo . '-' . $consecutivo;
            $q = "   UPDATE " . $wbasedato . "_000008 "
             . "      SET Concon = (Concon + 1) "
             . "    WHERE Conind = '1' "
             . "      AND Conane = 'on' "
             . "      AND Conest = 'on' ";
            break;
    } 

    $res1 = mysql_query($q, $conex);

    switch ($tipo)
    {
        case 'C':
            $q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
             . "    WHERE Conind = '1'"
             . "      AND Concar = 'on' "
             . "      AND Conest = 'on' ";
            break;

        case 'F':
            $q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
             . "    WHERE Conind = '1'"
             . "      AND Conane = 'on' "
             . "      AND Conest = 'on' ";
            break;
    } 

    $res1 = mysql_query($q, $conex);
    $row2 = mysql_fetch_array($res1);
    $codigo = $row2[1];
    $consecutivo = $row2[0];

    $q = " UNLOCK TABLES"; //SE DESBLOQUEA LA TABLA DE FUENTES
    $errunlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

    $q = " INSERT INTO " . $wbasedato . "_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . date('Y') . "', '" . date('m') . "','" . $row2[0] . "', '" . $row2[1] . "' , '" . date('Y-m-d') . "', '" . $cco . "' , '" . $cco2 . "' ,      '" . $anexo . "' , '" . $usuario . "',      '' , 'on', 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE ENTRADA DEL ARTICULO " . mysql_error());
} 
/**
* Graba el detalle de entrada de un producto a Matrix
* 
* @param caracter $codpro codigo del articulo que se va ingresar
* @param caracter $codigo concepto del movimeinto
* @param caracter $consecutivo numero del movimeitno
* @param caracter $usuario codigo del usuario que graba
* @param caracter $prese preentacion de al articulo en caso de ser insumo
* @param caracter $lote lote del articulo en caso de ser producto
* @param caracter $ajupre presentacion de ajuste que tiene
* @param caracter $ajucan cantidad de ajuste
*/
function grabarDetalleEntradaMatrix($codpro, $codigo, $consecutivo, $usuario, $prese, $lote, $ajupre, $ajucan)
{
    global $conex;
    global $wbasedato;
    
    if( empty($ajucan) ){
    	$ajucan = 0;
    }

    $q = " INSERT INTO " . $wbasedato . "_000007 (   Medico        ,            Fecha_data   ,                  Hora_data     ,        Mdecon    ,           Mdedoc      ,       Mdeart    , Mdecan,      Mdenlo       ,           Mdepre  ,          Mdepaj    ,      Mdecaj        , Mdecto, Mdeest,  Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $consecutivo . "','" . $codpro . "',   '1' ,   '" . $lote . "',   '" . $prese . "',   '" . $ajupre . "',   '" . $ajucan . "',  1    , 'on'  , 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL DETALLE DE ENTRADA DE UN ARTICULO " . mysql_error());
} 

/**
* Se graba el encabezado del movmiento
* 
* Retorna
* 
* @param caracter $codigo concepto del movmiento
* @param caracter $consecutivo numero del movmiento
* Pide
* @param caracter $cco centro de costos (codigo-descripcion)
* @param caracter $usuario codigo de usuario que realiza la operacion
* @param caracter $cco2 si es averia el centro de costos al que va, si es cargo historia-ingreso
* @param caracter $tipo C cargo A o Averia, segun eso se busca en concepto para el movmiento
*/
function grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $usuario, $cco2, $anexo)
{
    global $conex;
    global $wbasedato;

    $q = "lock table " . $wbasedato . "_000008 LOW_PRIORITY WRITE"; 
    // $errlock = mysql_query($q,$conex);
    $q = "   UPDATE " . $wbasedato . "_000008 "
     . "      SET Concon = (Concon + 1) "
     . "    WHERE Conind = '-1' "
     . "      AND Conane = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);

    $q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '-1'"
     . "      AND Conane = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);
    $row2 = mysql_fetch_array($res1);
    $codigo = $row2[1];
    $consecutivo = $row2[0];

    $q = " UNLOCK TABLES"; //SE DESBLOQUEA LA TABLA DE FUENTES
    $errunlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

    $q = " INSERT INTO " . $wbasedato . "_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . date('Y') . "', '" . date('m') . "','" . $row2[0] . "', '" . $row2[1] . "' , '" . date('Y-m-d') . "', mid('" . $cco . "',1,instr('" . $cco . "','-')-1) , '" . $cco2 . "' ,       '" . $anexo . "', '" . $usuario . "',      '' , 'on', 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS " . mysql_error());
} 
/**
* Grabamos el movimiento de salida de matrix
* 
* @param caracter $inscod codigo del insumo o producto
* @param caracter $codigo concepto del movmiento
* @param caracter $consecutivo numero del movimeinto
* @param caracter $usuario codigo del usuario que graba
* @param caracter $prese presentacion que se graba
* @param caracter $lote lote que se descuenta
* @param caracter $ajupre la presentacion si haya ajuste de presentacion
* @param caracter $ajucan en que cantidad el ajuste
* @param numerico $cantidad cantidad que se descarta
*/
function grabarDetalleSalidaMatrix($inscod, $codigo, $consecutivo, $usuario, $prese, $lote, $ajupre, $ajucan, $cantidad, $total)
{
    global $conex;
    global $wbasedato;
    
    if( empty($ajucan) ){
    	$ajucan = 0;
    }
	
	if( empty($cantidad) ){
    	$cantidad = 0;
    }

    $q = " INSERT INTO " . $wbasedato . "_000007 (   Medico        ,   Fecha_data            ,                  Hora_data     ,              Mdecon,              Mdedoc   ,     Mdeart      ,           Mdecan    ,      Mdefve ,    Mdenlo      , Mdepre            ,  Mdepaj          , Mdecaj          ,  Mdecto        ,Mdeest,  Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "'  , '" . $consecutivo . "','" . $inscod . "', '" . $cantidad . "' , '0000-00-00', '" . $lote . "',   '" . $prese . "', '" . $ajupre . "','" . $ajucan . "','" . $total . "', 'on' , 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN ARTICULO " . mysql_error());
} 

function sumarArticuloMatrix($inscod, $cco, $lote, $dato)
{
    global $conex;
    global $wbasedato;

    if ($lote != '')
    {
        $q = "   UPDATE " . $wbasedato . "_000005 "
         . "      SET karexi = karexi + 1 "
         . "    WHERE Karcod = '" . $inscod . "' "
         . "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

        $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO SUMAR EL ARTICULO " . mysql_error());

        $q = "   UPDATE " . $wbasedato . "_000004 "
         . "      SET Plosal = Plosal+1 "
         . "    WHERE Plocod =  '" . $dato . "' "
         . "      AND Plopro ='" . $inscod . "' "
         . "      AND Ploest ='on' "
         . "      AND Plocco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
    } 
    else
    {
        $q = " SELECT Appcnv "
         . "      FROM " . $wbasedato . "_000009 "
         . "    WHERE Appcod =  '" . $inscod . "' "
         . "      AND Apppre='" . $dato . "' "
         . "      AND Appest ='on' "
         . "      AND Appcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

        $res1 = mysql_query($q, $conex);
        $row2 = mysql_fetch_array($res1);

        $q = "   UPDATE " . $wbasedato . "_000005 "
         . "      SET karexi = karexi + (1*" . $row2[0] . ") "
         . "    WHERE Karcod = '" . $inscod . "' "
         . "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

        $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO SUMAR EL ARTICULO " . mysql_error());

        $q = "   UPDATE " . $wbasedato . "_000009 "
         . "      SET Appexi = Appexi+ 1*Appcnv "
         . "    WHERE Appcod =  '" . $inscod . "' "
         . "      AND Apppre='" . $dato . "' "
         . "      AND Appest ='on' "
         . "      AND Appcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
    } 
    $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO SUMAR UN INSUMO " . mysql_error());
} 

/**
* Se anula un cargo de terminado por devolucion, poniendo en encabezado en off
* 
* @param caracter $cco centro de costos origen del cargo
* @param caracter $destino centro de costos destino del cargo
* @param caracter $insumo articulo
* @param caracter $dato presentacion si es insumo y lote si es producto
* @param caracter $lote si tiene lote
*/
function anularCargo($cco, $destino, $insumo, $dato, $lote)
{
    global $conex;
    global $wbasedato;

    $q = " SELECT Concod"
     . "       FROM " . $wbasedato . "_000008  "
     . "    WHERE Conind = '-1' "
     . "       AND Concar = 'on' "
     . "       AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);
    $row1 = mysql_fetch_array($res1);
    if ($lote == 'on')
    {
        $q = " SELECT Mencon, Mendoc "
         . "        FROM " . $wbasedato . "_000006, " . $wbasedato . "_000007 "
         . "      WHERE Mencco =mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
         . "            and Menccd = '" . $destino . "' "
         . "            and Mencon = '" . $row1[0] . "' "
         . "            and Mdecon = Mencon "
         . "            and Mdedoc = Mendoc "
         . "  		  and Mdeart = '" . $insumo . "' "
         . "            and Mdenlo = '" . $dato . "-" . $insumo . "' "
         . "            and Mdeest = 'on' "
         . "            and Menest = 'on' "
         . "        ORDER BY " . $wbasedato . "_000006.id desc";
    } 
    else
    {
        $q = " SELECT Mencon, Mendoc "
         . "        FROM " . $wbasedato . "_000006, " . $wbasedato . "_000007 "
         . "      WHERE Mencco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
         . "            and Menccd = '" . $destino . "' "
         . "            and Mencon = '" . $row1[0] . "' "
         . "            and Mdecon = Mencon "
         . "            and Mdedoc = Mendoc "
         . "  		  and Mdeart = '" . $insumo . "' "
         . "            and Mdepre = '" . $dato . "' "
         . "            and Mdeest = 'on' "
         . "            and Menest = 'on' "
         . "        ORDER BY " . $wbasedato . "_000006.id desc";
    } 

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);
    if ($num1 > 0)
    {
        $row1 = mysql_fetch_array($res1);
        $q = "   UPDATE " . $wbasedato . "_000006 "
         . "      SET Menest = 'off' "
         . "    WHERE Mencon= '" . $row1[0] . "' "
         . "      AND Mendoc= '" . $row1[1] . "' ";
        $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO ANULAR EL CARGO " . mysql_error());

        $q = "   UPDATE " . $wbasedato . "_000006 "
         . "      SET Menest = 'off' "
         . "    WHERE Mendan= '" . $row1[0] . "-" . $row1[1] . "' "; 
        // echo $q;
        $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO ANULAR EL CARGO " . mysql_error());
    } 
} 
/**
* ****************************************************PROGRAMA*************************************************************************
*/

session_start();

if (!isset($user))
{
    if (!isset($_SESSION['user']))
        session_register("user");
} 

if (!isset($_SESSION['user']))
    echo "error";
else
{
    // $wbasedato = 'cenpro';
//    $conex = mysql_connect('localhost', 'root', '')
//    or die("No se ralizo Conexion");
	$wemp_pmla = $_REQUEST['wemp_pmla'];
    
	include_once( "cenpro/cargos.inc.php" );	//2013-08-14
    include_once( "conex.php" );
	include_once("root/comun.php");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	$bd = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
    

    pintarTitulo(); //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts
    //$bd = 'movhos'; 
    // invoco la funcion connectOdbc del inlcude de ana, para saber si unix responde, en caso contrario,
    // este programa no debe usarse
    // include_once("pda/tablas.php");
    include_once("movhos/fxValidacionArticulo.php");
    include_once("movhos/registro_tablas.php");
    include_once("movhos/otros.php");
    include_once("cenpro/funciones.php");
    connectOdbc($conex_o, 'facturacion');
	
	/**********************************************************************
	 * Agosto 14 de 2013
	 **********************************************************************/
	if( !consultarConexionUnix() ){
		$conex_o = 0;
	}
	/**********************************************************************/

    if (true || $conex_o != 0)
    {
        $tipTrans = 'D'; //segun ana es una transaccion de devolucion
        //$emp = '01';
        $aprov = true; //siempre es por aprovechamiento;
        $exp = explode('-', $cco);
        $centro['cod'] = $exp[0];
        $centro['neg'] = false;
        getCco($centro, $tipTrans, $wemp_pmla);
        $pac['his'] = $historia;
        $pac['ing'] = $ingreso;
        $cns = 0;
        $date = date('Y-m-d');
        $art['ini'] = $cod;
        $art['ubi'] = 'US';
        $serv['cod'] = $servicio;
        getCco($serv, $tipTrans, $wemp_pmla);
        if (isset($serv['apl']) and $serv['apl'])
        {
            $centro['apl'] = true;
        } 
        else
        {
            $serv['apl'] = false;
        } 
        $ronApl = date("G:i - A"); 
        // consulto los datos del usuario de la sesion
        $pos = strpos($user, "-");
        $wusuario = substr($user, $pos + 1, strlen($user)); //extraigo el codigo del usuario  
        $usu = $wusuario; 
        // consulto los centros de costos que se administran con esta aplicacion
        // estos se cargan en un select llamado ccos.
        // consultamos si el producto es codificado o no
        $q = "SELECT Artcom, Tipcdo, Tippro, Arttnc "
		 . "     FROM   " . $wbasedato . "_000002, ".$bd."_000027, " . $wbasedato . "_000001 "
         . "   WHERE Artcod='" . $cod . "' "
         . "     AND Unicod = Artuni "
         . "     AND Tipcod = Arttip "
         . "     AND Tipest = 'on' ";

        $res1 = mysql_query($q, $conex);
        $num1 = mysql_num_rows($res1);
        $row1 = mysql_fetch_array($res1);

		if( $conex_o != 0 ){
			
			/********************************************************
			 * Con conexion a Unix
			 ********************************************************/
		
			switch ($row1[2])
			{
				case 'on':
					if (!isset($var) or $var == '')
					{
						pintarAlerta('DEBE SELECCIONAR EL LOTE QUE VA A DEVOLVER');
						pintarBoton();
					} 
					else
					{
						$art['lot'] = $var;
						if ($row1[1] == 'on' && $row1[3] != 'on' )	//Si es codificado y diferente de tratar como no codificado
						{
							$art['cod'] = $cod;
							$art['neg'] = false;
							$art['can'] = 1;
							$res = ArticuloExiste ($art, $error); 
							// echo $cod;
							if ($res)
							{
								$res = TarifaSaldo($art, $centro, $tipTrans, $aprov, $error);
								if ($res)
								{
									$centro['apl'] = false;
									$res = validacionDevolucion($centro, $pac, $art, $aprov, false, $error);
									if (!$res)
									{
										$centro['apl'] = true;
										$res = validacionDevolucion($centro, $pac, $art, $aprov, false, $error);
									} 

									if ($res)
									{
										Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
										grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $historia . '-' . $ingreso, $centro['cod'], $wusuario, 'C', $dronum);
										$dato = $var . "-" . $cod;
										grabarDetalleEntradaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '');
										sumarArticuloMatrix($cod, $cco, 'on', $var);
										anularCargo($cco, $historia . '-' . $ingreso, $cod, $var, 'on');
										grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $codigo . '-' . $consecutivo);
										grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $var . '-' . $cod, '', '', 1, 1);
										$res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, $error);
										if (!$res)
										{
											pintarAlerta('EL ARTICULO NO HA PODIDO SER CARGADO A ITDRO');
											$art['ubi'] = 'M';
										} 
										registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error);

										if (!$centro['apl'])
										{
											registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
										} 
										else
										{
											registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
											$trans['num'] = $dronum;
											if ($drolin == 1)
											{
												$trans['lin'] = 1;
											} 
											else
											{
												$trans['lin'] = '';
											} 
											registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, $error);
										}

										descargarCarro2( $art['cod'], $art['ini'], $pac['his'], $pac['ing'], $cco );
										registrarArticuloKE( $art['cod'], $pac['his'], $pac['ing'], true );
										pintarConfi($cod, $var, $row1[0], 'LOTE');
										?>
										<script>
											window . opener . document . producto . submit();
											window . close();
										 </script>
										<?php
									} 
									else
									{
										pintarAlerta('EL PACIENTE NO TIENE SALDO DEL ARTICULO');
										pintarBoton();
									} 
								} 
								else
								{
									pintarAlerta('EL ARTICULO A DEVOLVER NO TIENE TARIFA EN UNIX');
									pintarBoton();
								} 
							} 
							else
							{
								pintarAlerta('EL ARTICULO A DEVOLVER NO EXISTE EN UNIX');
								pintarBoton();
							} 
						} 
						else
						{ 
							// consulto los articulos a devolver
							$inslis = consultarMovimiento($cod, $historia, $ingreso, $var, $cco);
							$art['neg'] = false;
							$art['cod'] = $cod;
							$art['can'] = 1;

							$centro['apl'] = false;
							$res = validacionDevolucion($centro, $pac, $art, $aprov, false, $error);
							if (!$res)
							{
								$centro['apl'] = true;
								$res = validacionDevolucion($centro, $pac, $art, $aprov, false, $error);
							} 

							if ($res)
							{
								for ($i = 0; $i < count($inslis);$i++)
								{
									if ($inslis[$i]['prese']['can'] > 0)
									{
										$art['cod'] = $inslis[$i]['prese']['cod'];
										$art['can'] = $inslis[$i]['prese']['can'];
										$res = ArticuloExiste ($art, $error);
										if ($res)
										{
											$res = TarifaSaldo($art, $centro, $tipTrans, $aprov, $error);
											if ($res)
											{
												$fin = 1;
											} 
											else
											{
												pintarAlert1('El articulo' . $inslis[$i]['prese']['cod'] . ' no tiene saldo en Unix');
											} 
										} 
										else
										{
											pintarAlert1('No existe el articulo' . $inslis[$i]['prese']['cod'] . 'en Unix');
										} 
									} 
								} 

								if (isset($fin))
								{
									Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
									$ind = 1;
									grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $historia . '-' . $ingreso, $centro['cod'], $wusuario, 'C', $dronum);
									$dato = $var . "-" . $cod;
									grabarDetalleEntradaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '');
									sumarArticuloMatrix($cod, $cco, 'on', $var);
									anularCargo($cco, $historia . '-' . $ingreso, $cod, $var, 'on');
									grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $codigo . '-' . $consecutivo);
									for ($i = 0; $i < count($inslis); $i++)
									{
										if ($inslis[$i]['prese']['can'] > 0 and $inslis[$i]['aju']['can'] > 0) // No hay ajuste de presentacion
										{
											grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $inslis[$i]['prese']['cod'] . '-' . $inslis[$i]['prese']['nom'], $var . '-' . $cod, $inslis[$i]['aju']['cod'] . '-' . $inslis[$i]['aju']['nom'], $inslis[$i]['aju']['can'], $inslis[$i]['prese']['can'], $inslis[$i]['tot']);
											devolverAjuste($inslis[$i]['tot'], $inslis[$i]['prese']['cod'], $inslis[$i]['prese']['can'], $historia, $centro['cod'], $wusuario, $ingreso, $inslis[$i]['aju']['can'], $inslis[$i]['prese']['cnv'], $inslis[$i]['aju']['cod']);
										} 
										else if ($inslis[$i]['prese']['can'] > 0) // Todo se descuenta del ajuste, no se escoge presentacion nueva
										{
											grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $inslis[$i]['prese']['cod'] . '-' . $inslis[$i]['prese']['nom'], $var . '-' . $cod, '', '', $inslis[$i]['prese']['can'], $inslis[$i]['tot']);
											devolverAjuste($inslis[$i]['tot'], $inslis[$i]['prese']['cod'], $inslis[$i]['prese']['can'], $historia, $centro['cod'], $wusuario, $ingreso, $inslis[$i]['aju']['can'], $inslis[$i]['prese']['cnv'], $inslis[$i]['aju']['cod']);
										} 
										else if ($inslis[$i]['aju']['can'] > 0)
										{
											grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, '', $var . '-' . $cod, $inslis[$i]['aju']['cod'] . '-' . $inslis[$i]['aju']['nom'], $inslis[$i]['aju']['can'], '', $inslis[$i]['tot']);
											devolverAjuste($inslis[$i]['tot'], $inslis[$i]['prese']['cod'], $inslis[$i]['prese']['can'], $historia, $centro['cod'], $wusuario, $ingreso, $inslis[$i]['aju']['can'], $inslis[$i]['aju']['cnv'], $inslis[$i]['aju']['cod']);
										} 
										// se organiza de nuevo el ajuste anterior
										if ($inslis[$i]['prese']['can'] > 0)
										{
											$art['cod'] = $inslis[$i]['prese']['cod'];
											$art['can'] = $inslis[$i]['prese']['can'];
											if ($ind == 1)
											{
												$ind = 0;
											} 
											else
											{
												Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, false, $usu, $error);
											} 

											$res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, $error);
											if (!$res)
											{
												pintarAlerta('EL ARTICULO ' . $presen[$i][$j]['cod'] . ' NO HA PODIDO SER CARGADO A ITDRO');
												$art['ubi'] = 'M';
											} 
											registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error);
										} 
									} 
									// grabamos ahora el saldo de producto
									$art['cod'] = $cod;
									$art['can'] = 1; 
									$art['nom'] = $row1[0];

									if (!$centro['apl'])
									{
										registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
									} 
									else
									{
										registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
										$trans['num'] = $dronum;
										if ($drolin == 1)
										{
											$trans['lin'] = 1;
										} 
										else
										{
											$trans['lin'] = '';
										} 
										registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, $error);
									}

									descargarCarro( $art['cod'], $pac['his'], $pac['ing'], $cco );
									registrarArticuloKE( $art['cod'], $pac['his'], $pac['ing'], true );
									pintarConfi($cod, $var, $row1[0], 'LOTE');
									pintarBoton();
								} 
								else
								{
									pintarAlerta('NO SE HA REALIZADO AL DEVOLUCION');
									pintarBoton();
								} 
							} 
							else
							{
								pintarAlerta('EL PACIENTE NO TIENE SALDO DEL ARTICULO');
								pintarBoton();
							} 
						} 
					} 
					break;
				default:

					if (!isset($var) or $var == '')
					{
						pintarAlerta('DEBE SELECCIONAR LA PRESENTACION  QUE VA A DEVOLVER');
						pintarBoton();
					} 
					else
					{
						$art['lot'] = '';
						$exp = explode('-', $var);
						$art['cod'] = $exp[0];
						$art['neg'] = false;
						$art['can'] = 1;
						$res = ArticuloExiste ($art, $error);
						if ($res)
						{
							$res = true or TarifaSaldo($art, $centro, $tipTrans, $aprov, $error);
							if ($res)
							{
								$centro['apl'] = false;
								$res = validacionDevolucion($centro, $pac, $art, $aprov, false, $error);
								if (!$res)
								{
									$centro['apl'] = true;
									$res = validacionDevolucion($centro, $pac, $art, $aprov, false, $error);
								} 

								if ($res)
								{
									Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
									grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $historia . '-' . $ingreso, $centro['cod'], $wusuario, 'C', $dronum);
									grabarDetalleEntradaMatrix($cod, $codigo, $consecutivo, $wusuario, $var, '', '', '');
									sumarArticuloMatrix($cod, $cco, '', $exp[0]);
									anularCargo($cco, $historia . '-' . $ingreso, $cod, $var, '');
									grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $codigo . '-' . $consecutivo);
									grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $var, '', '', '', 1, 1);
									$res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, $error);
									if (!$res)
									{
										pintarAlerta('EL ARTICULO NO HA PODIDO SER CARGADO A ITDRO');
										$art['ubi'] = 'M';
									} 
									registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error); 

									if (!$centro['apl'])
									{
										registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
									} 
									else
									{
										registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
										$trans['num'] = $dronum;
										if ($drolin == 1)
										{
											$trans['lin'] = 1;
										} 
										else
										{
											$trans['lin'] = '';
										} 
										registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, $error);
									}

									descargarCarro2( $art['cod'], $art['ini'], $pac['his'], $pac['ing'], $cco );
									registrarArticuloKE( $cod, $pac['his'], $pac['ing'], true );	
									pintarConfi($cod, $var, $row1[0], 'LOTE');
									?>
										<script>
											window . opener . document . producto . submit();
											window . close();
										 </script>
									<?php
								} 
								else
								{
									pintarAlerta('EL PACIENTE NO TIENE SALDO DEL ARTICULO');
									pintarBoton();
								} 
							} 
							else
							{
								pintarAlerta('EL ARTICULO A DEVOLVER NO TIENE TARIFA EN UNIX');
								pintarBoton();
							} 
						} 
						else
						{
							pintarAlerta('EL ARTICULO A DEVOLVER NO EXISTE EN UNIX');
							pintarBoton();
						} 
					} 
			} // switch
		
		}
		else
		{
		
			/********************************************************
			 * Sin conexion a Unix
			 ********************************************************/
			 
			switch ($row1[2])
			{
				case 'on':
					if (!isset($var) or $var == '')
					{
						pintarAlerta('DEBE SELECCIONAR EL LOTE QUE VA A DEVOLVER');
						pintarBoton();
					} 
					else
					{
						$art['lot'] = $var;
						if ($row1[1] == 'on' && $row1[1] != 'on' )
						{
							$art['cod'] = $cod;
							$art['neg'] = false;
							$art['can'] = 1;
							$res = ArticuloExiste ($art, $error); 
							// echo $cod;
							if ($res)
							{
								$res = TarifaSaldoMatrix($art, $centro, $tipTrans, $aprov, $error);
								if ($res)
								{
									$centro['apl'] = false;
									$res = validacionDevolucion($centro, $pac, $art, $aprov, false, $error);
									if (!$res)
									{
										$centro['apl'] = true;
										$res = validacionDevolucion($centro, $pac, $art, $aprov, false, $error);
									} 

									if ($res)
									{
										Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
										grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $historia . '-' . $ingreso, $centro['cod'], $wusuario, 'C', $dronum);
										$dato = $var . "-" . $cod;
										grabarDetalleEntradaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '');
										sumarArticuloMatrix($cod, $cco, 'on', $var);
										anularCargo($cco, $historia . '-' . $ingreso, $cod, $var, 'on');
										grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $codigo . '-' . $consecutivo);
										grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $var . '-' . $cod, '', '', 1, 1);
										// $res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, &$error);
										// if (!$res)
										// {
											// pintarAlerta('EL ARTICULO NO HA PODIDO SER CARGADO A ITDRO');
											// $art['ubi'] = 'M';
										// } 
										$validar = registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error, "000143" );
										
										if( $validar ){
											/***************************************************************************
											 * Enero 2 de 2013
											 *
											 * Si hubo un registro en el detalle, entonces debo mover la tabla de saldos
											 ***************************************************************************/
											realizarMovimientoSaldos( $conex, $bd, $tipTrans, $centro[ 'cod' ], $art[ 'cod' ], $art[ 'can' ] );
											/***************************************************************************/
										}

										if (!$centro['apl'])
										{
											registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
										} 
										else
										{
											registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
											$trans['num'] = $dronum;
											if ($drolin == 1)
											{
												$trans['lin'] = 1;
											} 
											else
											{
												$trans['lin'] = '';
											} 
											registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, $error);
										}

										descargarCarro2( $art['cod'], $art['ini'], $pac['his'], $pac['ing'], $cco );
										registrarArticuloKE( $art['cod'], $pac['his'], $pac['ing'], true );
										pintarConfi($cod, $var, $row1[0], 'LOTE');
										?>
										<script>
											window . opener . document . producto . submit();
											window . close();
										 </script>
										<?php
									} 
									else
									{
										pintarAlerta('EL PACIENTE NO TIENE SALDO DEL ARTICULO');
										pintarBoton();
									} 
								} 
								else
								{
									pintarAlerta('EL ARTICULO A DEVOLVER NO TIENE TARIFA EN UNIX');
									pintarBoton();
								} 
							} 
							else
							{
								pintarAlerta('EL ARTICULO A DEVOLVER NO EXISTE EN UNIX');
								pintarBoton();
							} 
						} 
						else
						{ 
							// consulto los articulos a devolver
							$inslis = consultarMovimiento($cod, $historia, $ingreso, $var, $cco);
							$art['neg'] = false;
							$art['cod'] = $cod;
							$art['can'] = 1;

							$centro['apl'] = false;
							$res = validacionDevolucion($centro, $pac, $art, $aprov, false, $error);
							if (!$res)
							{
								$centro['apl'] = true;
								$res = validacionDevolucion($centro, $pac, $art, $aprov, false, $error);
							} 

							if ($res)
							{
								for ($i = 0; $i < count($inslis);$i++)
								{
									if ($inslis[$i]['prese']['can'] > 0)
									{
										$art['cod'] = $inslis[$i]['prese']['cod'];
										$art['can'] = $inslis[$i]['prese']['can'];
										$res = ArticuloExiste ($art, $error);
										if ($res)
										{
											$res = TarifaSaldoMatrix($art, $centro, $tipTrans, $aprov, $error);
											if ($res)
											{
												$fin = 1;
											} 
											else
											{
												pintarAlert1('El articulo' . $inslis[$i]['prese']['cod'] . ' no tiene saldo en Unix');
											} 
										} 
										else
										{
											pintarAlert1('No existe el articulo' . $inslis[$i]['prese']['cod'] . 'en Unix');
										} 
									} 
								} 

								if (isset($fin))
								{
									Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
									$ind = 1;
									grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $historia . '-' . $ingreso, $centro['cod'], $wusuario, 'C', $dronum);
									$dato = $var . "-" . $cod;
									grabarDetalleEntradaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '');
									sumarArticuloMatrix($cod, $cco, 'on', $var);
									anularCargo($cco, $historia . '-' . $ingreso, $cod, $var, 'on');
									grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $codigo . '-' . $consecutivo);
									for ($i = 0; $i < count($inslis); $i++)
									{
										if ($inslis[$i]['prese']['can'] > 0 and $inslis[$i]['aju']['can'] > 0) // No hay ajuste de presentacion
										{
											grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $inslis[$i]['prese']['cod'] . '-' . $inslis[$i]['prese']['nom'], $var . '-' . $cod, $inslis[$i]['aju']['cod'] . '-' . $inslis[$i]['aju']['nom'], $inslis[$i]['aju']['can'], $inslis[$i]['prese']['can'], $inslis[$i]['tot']);
											devolverAjuste($inslis[$i]['tot'], $inslis[$i]['prese']['cod'], $inslis[$i]['prese']['can'], $historia, $centro['cod'], $wusuario, $ingreso, $inslis[$i]['aju']['can'], $inslis[$i]['prese']['cnv'], $inslis[$i]['aju']['cod']);
										} 
										else if ($inslis[$i]['prese']['can'] > 0) // Todo se descuenta del ajuste, no se escoge presentacion nueva
										{
											grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $inslis[$i]['prese']['cod'] . '-' . $inslis[$i]['prese']['nom'], $var . '-' . $cod, '', '', $inslis[$i]['prese']['can'], $inslis[$i]['tot']);
											devolverAjuste($inslis[$i]['tot'], $inslis[$i]['prese']['cod'], $inslis[$i]['prese']['can'], $historia, $centro['cod'], $wusuario, $ingreso, $inslis[$i]['aju']['can'], $inslis[$i]['prese']['cnv'], $inslis[$i]['aju']['cod']);
										} 
										else if ($inslis[$i]['aju']['can'] > 0)
										{
											grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, '', $var . '-' . $cod, $inslis[$i]['aju']['cod'] . '-' . $inslis[$i]['aju']['nom'], $inslis[$i]['aju']['can'], '', $inslis[$i]['tot']);
											devolverAjuste($inslis[$i]['tot'], $inslis[$i]['prese']['cod'], $inslis[$i]['prese']['can'], $historia, $centro['cod'], $wusuario, $ingreso, $inslis[$i]['aju']['can'], $inslis[$i]['aju']['cnv'], $inslis[$i]['aju']['cod']);
										} 
										// se organiza de nuevo el ajuste anterior
										if ($inslis[$i]['prese']['can'] > 0)
										{
											$art['cod'] = $inslis[$i]['prese']['cod'];
											$art['can'] = $inslis[$i]['prese']['can'];
											if ($ind == 1)
											{
												$ind = 0;
											} 
											else
											{
												Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, false, $usu, $error);
											} 

											// $res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, &$error);
											// if (!$res)
											// {
												// pintarAlerta('EL ARTICULO ' . $presen[$i][$j]['cod'] . ' NO HA PODIDO SER CARGADO A ITDRO');
												// $art['ubi'] = 'M';
											// } 
											$validar = registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error, "000143" );
											
											if( $validar ){
												/***************************************************************************
												 * Enero 2 de 2013
												 *
												 * Si hubo un registro en el detalle, entonces debo mover la tabla de saldos
												 ***************************************************************************/
												realizarMovimientoSaldos( $conex, $bd, $tipTrans, $centro[ 'cod' ], $art[ 'cod' ], $art[ 'can' ] );
												/***************************************************************************/
											}
										} 
									} 
									// grabamos ahora el saldo de producto
									$art['cod'] = $cod;
									$art['can'] = 1; 
									$art['nom'] = $row1[0];

									if (!$centro['apl'])
									{
										registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
									} 
									else
									{
										registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
										$trans['num'] = $dronum;
										if ($drolin == 1)
										{
											$trans['lin'] = 1;
										} 
										else
										{
											$trans['lin'] = '';
										} 
										registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, $error);
									}

									descargarCarro( $art['cod'], $pac['his'], $pac['ing'], $cco );
									registrarArticuloKE( $art['cod'], $pac['his'], $pac['ing'], true );
									pintarConfi($cod, $var, $row1[0], 'LOTE');
									pintarBoton();
								} 
								else
								{
									pintarAlerta('NO SE HA REALIZADO AL DEVOLUCION');
									pintarBoton();
								} 
							} 
							else
							{
								pintarAlerta('EL PACIENTE NO TIENE SALDO DEL ARTICULO');
								pintarBoton();
							} 
						} 
					} 
					break;
				default:

					if (!isset($var) or $var == '')
					{
						pintarAlerta('DEBE SELECCIONAR LA PRESENTACION  QUE VA A DEVOLVER');
						pintarBoton();
					} 
					else
					{
						$art['lot'] = '';
						$exp = explode('-', $var);
						$art['cod'] = $exp[0];
						$art['neg'] = false;
						$art['can'] = 1;
						$res = ArticuloExiste ($art, $error);
						if ($res)
						{
							$res = true or TarifaSaldoMatrix($art, $centro, $tipTrans, $aprov, $error);
							if ($res)
							{
								$centro['apl'] = false;
								$res = validacionDevolucion($centro, $pac, $art, $aprov, false, $error);
								if (!$res)
								{
									$centro['apl'] = true;
									$res = validacionDevolucion($centro, $pac, $art, $aprov, false, $error);
								} 

								if ($res)
								{
									Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
									grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $historia . '-' . $ingreso, $centro['cod'], $wusuario, 'C', $dronum);
									grabarDetalleEntradaMatrix($cod, $codigo, $consecutivo, $wusuario, $var, '', '', '');
									sumarArticuloMatrix($cod, $cco, '', $exp[0]);
									anularCargo($cco, $historia . '-' . $ingreso, $cod, $var, '');
									grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $codigo . '-' . $consecutivo);
									grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $var, '', '', '', 1, 1);
									// $res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, &$error);
									// if (!$res)
									// {
										// pintarAlerta('EL ARTICULO NO HA PODIDO SER CARGADO A ITDRO');
										// $art['ubi'] = 'M';
									// } 
									$validar = registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error, "000143" );
									
									if( $validar ){
										/***************************************************************************
										 * Enero 2 de 2013
										 *
										 * Si hubo un registro en el detalle, entonces debo mover la tabla de saldos
										 ***************************************************************************/
										realizarMovimientoSaldos( $conex, $bd, $tipTrans, $centro[ 'cod' ], $art[ 'cod' ], $art[ 'can' ] );
										/***************************************************************************/
									}

									if (!$centro['apl'])
									{
										registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
									} 
									else
									{
										registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
										$trans['num'] = $dronum;
										if ($drolin == 1)
										{
											$trans['lin'] = 1;
										} 
										else
										{
											$trans['lin'] = '';
										} 
										registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, $error);
									}

									descargarCarro2( $art['cod'], $art['ini'], $pac['his'], $pac['ing'], $cco );
									registrarArticuloKE( $cod, $pac['his'], $pac['ing'], true );	
									pintarConfi($cod, $var, $row1[0], 'LOTE');
									?>
										<script>
											window . opener . document . producto . submit();
											window . close();
										 </script>
									<?php
								} 
								else
								{
									pintarAlerta('EL PACIENTE NO TIENE SALDO DEL ARTICULO');
									pintarBoton();
								} 
							} 
							else
							{
								pintarAlerta('EL ARTICULO A DEVOLVER NO TIENE TARIFA EN UNIX');
								pintarBoton();
							} 
						} 
						else
						{
							pintarAlerta('EL ARTICULO A DEVOLVER NO EXISTE EN UNIX');
							pintarBoton();
						} 
					} 
			} // switch
		
		}
	} 
} 

?>
</body>
</html>