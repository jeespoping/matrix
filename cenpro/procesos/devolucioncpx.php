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
include_once("root/comun.php");
	$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cenmez' );
	$bd = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
/***
 * Modificación.
 * 
 * Agosto 14 de 2013  (Edwin MG)	Se valida que halla conexión unix en inventario desde matrix, si no hay conexión
 *									con unix se activa la contigencia de dispensación.
 * Febrero 25 de 2013 (Edwin MG). Cambios varios para cuando no hay conexión con UNIX. Entre ellos se registra el movimiento en tabla de paso
 *								  y se mira los saldos en matrix y no en UNIX.
 * Julio 25 de 2011.	Se modifica el programa para que funcione de acuerdo a la nueva dispensacion (cada x horas)
 * 
 * 
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
		
		$sqlid="SELECT 
					id, kaddis, kadcpx 
				FROM 
					".$bd."_000054 
				WHERE 
					kadart = '$art'
					AND kaddis > 0
					AND kadfec = '".date("Y-m-d")."'
					AND kadhis = '$his'  
		       		AND kading = '$ing'
		       		AND kadcon = 'on'
				ORDER BY kadart";
		
		$resid = mysql_query( $sqlid, $conex ) or die(mysql_errno()." - en el query: ".$sqlid." - ".mysql_error());;
		
		if( $row = mysql_fetch_array( $resid ) ){
			$id = $row[0];
		}
		else{
			$row[0] = "";
			return false;
		}
		
		$cpx = crearAplicacionesDevueltasPorHoras( $row[2], 1 );
		
		list( $kadRondaCpx ) = explode( "|",consultarUltimaRondaDispensada( $cpx ) );
		
		if( $row[1] > 1 ){
			//Actualizando registro con el articulo cargado
			$sql = "UPDATE 
						".$bd."_000054 
			       	SET 
			       		kaddis = kaddis-1,
			       		kadcpx = '$cpx',
						kadron = '$kadRondaCpx'
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
			       		kadhdi = '00:00:00',
			       		kadcpx = '$cpx',
						kadron = '$kadRondaCpx'
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
function grabarEncabezadoEntradaMatrix(&$codigo, &$consecutivo, $cco2, $cco, $usuario, $tipo, $anexo)
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
function grabarEncabezadoSalidaMatrix(&$codigo, &$consecutivo, $cco, $usuario, $cco2, $anexo)
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

    $q = " INSERT INTO " . $wbasedato . "_000007 (   Medico        ,   Fecha_data            ,                  Hora_data     ,              Mdecon,              Mdedoc   ,     Mdeart      ,           Mdecan    ,      Mdefve,     Mdenlo          , Mdepre            ,  Mdepaj          , Mdecaj          ,  Mdecto        ,Mdeest,  Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "'  , '" . $consecutivo . "','" . $inscod . "', '" . $cantidad . "' , '0000-00-00',     '" . $lote . "',   '" . $prese . "', '" . $ajupre . "','" . $ajucan . "','" . $total . "', 'on' , 'C-" . $usuario . "') ";

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
    //$wbasedato = 'cenpro';
//    $conex = mysql_connect('localhost', 'root', '')
//    or die("No se ralizo Conexion");
    
    include_once( "conex.php" );
    include_once( "cenpro/cargos.inc.php" );
    
	$desde_CargosPDA = true; //#SEBASTIAN_NEVADO
	$accion_iq = ''; //#SEBASTIAN_NEVADO


    pintarTitulo(); //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts
   // $bd = 'movhos'; 
    // invoco la funcion connectOdbc del inlcude de ana, para saber si unix responde, en caso contrario,
    // este programa no debe usarse
    // include_once("pda/tablas.php");
    include_once("movhos/fxValidacionArticulo.php");
    include_once("movhos/registro_tablas.php");
    include_once("movhos/otros.php");
    include_once("cenpro/funciones.php");
	include_once("ips/funciones_facturacionERP.php"); //#SEBASTIAN_NEVADO
    connectOdbc($conex_o, 'facturacion');
	
	
	
	
	/**********************************************************************
	 * Agosto 14 de 2013
	 **********************************************************************/
	if( !consultarConexionUnix() ){
		$conex_o = 0;
	}
	/**********************************************************************/

    if( true || $conex_o != 0 )
    {
        $tipTrans = 'D'; //segun ana es una transaccion de devolucion
        $emp = '01';
        $aprov = true; //siempre es por aprovechamiento;
        $exp = explode('-', $cco);
        $centro['cod'] = $exp[0];
        $centro['neg'] = false;
        getCco($centro, $tipTrans, '01');
        $pac['his'] = $historia;
        $pac['ing'] = $ingreso;
        $cns = 0;
        $date = date('Y-m-d');
        $art['ini'] = $cod;
        $art['ubi'] = 'US';
        $serv['cod'] = $servicio;
        getCco($serv, $tipTrans, '01');
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
		
			/************************************************************
			 * Con conexión con Unix
			 ************************************************************/
			//echo "<br>conexión con Unix 943<br>"; //##BORRAR_SEBASTIAN_NEVADO
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
						if ($row1[1] == 'on' && $row1[3] != 'on' )
						{
							//echo "<br>Producto codificado 957<br>"; //##BORRAR_SEBASTIAN_NEVADO
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
										 </script >
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
							//echo "<br>No Codificado 1043<br>"; //##BORRAR_SEBASTIAN_NEVADO
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
									//echo "<br>Guardo mvtos 1088<br>"; //##BORRAR_SEBASTIAN_NEVADO
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
											
											/*
											 *Fecha: 2021-06-11
											 *Descripción: se realiza llamado de factura inteligente.
											 *Autor: sebastian.nevado
											*/
											//echo "<br>Llamo función de la facturación inteligente<br>"; //##BORRAR_SEBASTIAN_NEVADO
											$aResultadoFactInteligente = llamarFacturacionInteligente($pac, $centro['cod'], $art['cod'], $inslis[$i]['prese']['nom'], $art['can'], $tipTrans);
											if(!$aResultadoFactInteligente->exito)
											{
												echo $aResultadoFactInteligente->mensaje;
											}
											// FIN MODIFICACION

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
					//echo "<br>Case Default 1194<br>"; //##BORRAR_SEBASTIAN_NEVADO
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
									//echo "<br>Guardo mvts 1223<br>"; //##BORRAR_SEBASTIAN_NEVADO
									Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
									grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $historia . '-' . $ingreso, $centro['cod'], $wusuario, 'C', $dronum);
									grabarDetalleEntradaMatrix($cod, $codigo, $consecutivo, $wusuario, $var, '', '', '');
									sumarArticuloMatrix($cod, $cco, '', $exp[0]);
									anularCargo($cco, $historia . '-' . $ingreso, $cod, $var, '');
									grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $codigo . '-' . $consecutivo);
									grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $var, '', '', '', 1, 1);
									
									/*
									 *Fecha: 2021-06-11
									 *Descripción: se realiza llamado de factura inteligente.
									 *Autor: sebastian.nevado
									*/
									$sNombreArticulo = substr($var, strlen($exp[0]."-"), strlen($var)-1);
									//echo "<br>Llamo función de la facturación inteligente<br>"; //##BORRAR_SEBASTIAN_NEVADO
									$aResultadoFactInteligente = llamarFacturacionInteligente($pac, $centro['cod'], $art['cod'], $sNombreArticulo, $art['can'], $tipTrans);
									if(!$aResultadoFactInteligente->exito)
									{
										echo $aResultadoFactInteligente->mensaje;
									}
									// FIN MODIFICACION
									
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
										 </script >
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
		else{
		
			/************************************************************
			 * Sin conexión con Unix
			 ************************************************************/
		
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
						if ($row1[1] == 'on' && $row1[3] != 'on' )
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
										$validar = registrarDetalleCargo( date('Y-m-d'), $dronum, $drolin, $art, $usu, $error, "000143" );
										
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
										 </script >
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
							//echo "<br>Producto NO codificado 1432<br>"; //##BORRAR_SEBASTIAN_NEVADO
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
											
											/*
											 *Fecha: 2021-06-11
											 *Descripción: se realiza llamado de factura inteligente.
											 *Autor: sebastian.nevado
											*/
											//echo "<br>Llamo función de la facturación inteligente<br>"; //##BORRAR_SEBASTIAN_NEVADO
											$aResultadoFactInteligente = llamarFacturacionInteligente($pac, $centro['cod'], $art['cod'], $inslis[$i]['prese']['nom'], $art['can'], $tipTrans);
											if(!$aResultadoFactInteligente->exito)
											{
												echo $aResultadoFactInteligente->mensaje;
											}
											// FIN MODIFICACION
											

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
					//echo "<br>Case Default 1593<br>"; //##BORRAR_SEBASTIAN_NEVADO
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

									/*
										*Fecha: 2021-06-11
										*Descripción: se realiza llamado de factura inteligente.
										*Autor: sebastian.nevado
									*/
									//echo "<br>Llamo función de la facturación inteligente<br>"; //##BORRAR_SEBASTIAN_NEVADO
									$aResultadoFactInteligente = llamarFacturacionInteligente($pac, $centro['cod'], $art['cod'], $inslis[$i]['prese']['nom'], $art['can'], $tipTrans);
									if(!$aResultadoFactInteligente->exito)
									{
										echo $aResultadoFactInteligente->mensaje;
									}
									// FIN MODIFICACION
									// $res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, &$error);
									// if (!$res)
									// {
										// pintarAlerta('EL ARTICULO NO HA PODIDO SER CARGADO A ITDRO');
										// $art['ubi'] = 'M';
									// } 
									$validar = registrarDetalleCargo( date('Y-m-d'), $dronum, $drolin, $art, $usu, $error, "000143" );
									
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
										 </script >
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

/**
 * Función para hacer el proceso de factura inteligente.
 * @by: sebastian.nevado
 * @date: 2021-06-11
 * @return: array
 */
function CargarCargosErp($conex, $pac, $wmovhos, $wcliame, $art, $tipTrans, $numCargoInv, $linCargoInv, $cCentroCosto )
{
	//echo "<br>CargarCargosErp 4502<br>";//##BORRAR_SEBASTIAN_NEVADO
	//global $pac;
	global $emp;
	//echo "<br>emp: <br>";//##BORRAR_SEBASTIAN_NEVADO
	//print_r($emp); //##BORRAR_SEBASTIAN_NEVADO
	global $wbasedato;
	//echo "<br>wbasedato: <br>";//##BORRAR_SEBASTIAN_NEVADO
	//print_r($wbasedato); //##BORRAR_SEBASTIAN_NEVADO
	global $wusuario;
	//echo "<br>wusuario: <br>";//##BORRAR_SEBASTIAN_NEVADO
	//print_r($wusuario); //##BORRAR_SEBASTIAN_NEVADO
	global $wuse;
	//echo "<br>wuse: <br>"; //##BORRAR_SEBASTIAN_NEVADO
	//print_r($wuse); //##BORRAR_SEBASTIAN_NEVADO
	global $cco;
	//echo "<br>cco: <br>"; //##BORRAR_SEBASTIAN_NEVADO
	//print_r($cco); //##BORRAR_SEBASTIAN_NEVADO
	global $desde_CargosPDA;
	//echo "<br>desde_CargosPDA: <br>"; //##BORRAR_SEBASTIAN_NEVADO
	//print_r($desde_CargosPDA); //##BORRAR_SEBASTIAN_NEVADO
	//echo "<br>pac: <br>"; //##BORRAR_SEBASTIAN_NEVADO
	//print_r($pac); //##BORRAR_SEBASTIAN_NEVADO
	//echo "<br>"; //##BORRAR_SEBASTIAN_NEVADO
	$desde_CargosPDA = true;
	global $accion_iq;
	$accion_iq = '';
	$sql = "SELECT Ccoerp
			  FROM ".$wmovhos."_000011
			 WHERE ccocod = '".$pac['sac']."'
		";
	
	//echo "<br>sql: ".$sql."<br>"; //##BORRAR_SEBASTIAN_NEVADO
	
	$resCco = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$numCco = mysql_num_rows( $resCco );
	$CcoErp = false;
	if( $rowsCco = mysql_fetch_array( $resCco) ){
		$CcoErp = $rowsCco[ 'Ccoerp' ] == 'on' ? true: false;
	}
	
	//Si el cco no maneja cargo ERP o no está activo los cargos ERP no se ejecuta esta acción
	$cargarEnErp = consultarAliasPorAplicacion( $conex, $emp, "cargosPDA_ERP" );
	//echo "<br>cargarEnErp: ".$cargarEnErp."<br>"; //##BORRAR_SEBASTIAN_NEVADO
	//echo "<br>CcoErp: ".$CcoErp."<br>"; //##BORRAR_SEBASTIAN_NEVADO
	if( !$CcoErp || $cargarEnErp != 'on' ){
		//echo "<br>Return abrupto 4526<br>"; //##BORRAR_SEBASTIAN_NEVADO
		return;
	}
	
	$sql = "SELECT *
			  FROM ".$wmovhos."_000016
			 WHERE inghis = '".$pac['his']."'
			   AND inging = '".$pac['ing']."'
		";
	
	$resRes = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$numRes = mysql_num_rows( $resRes );
	if( $rowsRes = mysql_fetch_array( $resRes) ){
		
				
		$sql = "SELECT *
				  FROM ".$wcliame."_000101
				 WHERE Inghis = '".$pac['his']."'
				   AND Ingnin = '".$pac['ing']."'
			";
		
		$resIng = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numIng = mysql_num_rows( $resIng );
	
		if( $rowsIng = mysql_fetch_array( $resIng) ){
		
			
			$codEmpParticular = consultarAliasPorAplicacion($conex, $emp, 'codigoempresaparticular');
		
			if( $rowsIng[ 'Ingtpa' ] == 'P' ){
				$empresa = $codEmpParticular;
			}
			else{
				$empresa = $rowsIng[ 'Ingcem' ];
			}
			
			$sql = "SELECT *
					  FROM ".$wcliame."_000024
					 WHERE empcod = '".$empresa."'
					";
		
			$resEmp = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$numEmp = mysql_num_rows( $resEmp );
			
			if( $rowsEmp = mysql_fetch_array( $resEmp ) ){
		
				//Información de empresa
				$wcodemp 	  = $rowsEmp[ 'Empcod' ];
				$wnomemp 	  = $rowsEmp[ 'Empnom' ];
				$tipoEmpresa  = $rowsEmp[ 'Emptem' ];
				$nitEmpresa   = $rowsEmp[ 'Empnit' ];
				$wtar		  = $rowsEmp[ 'Emptar' ];
			
				//Información del paciente
				$tipoPaciente = $rowsIng[ 'Ingcla' ];
				$tipoIngreso  = $rowsIng[ 'Ingtin' ];
				$wser		  = $rowsIng[ 'Ingsei' ];
				$wfecing	  = $rowsIng[ 'Ingfei' ];
				
				//Consulta información de pacientes
				$infoPacienteCargos = consultarNombresPaciente( $conex, $pac['his'], $emp );
				
				//Conceptos de grabación
				$wcodcon = consultarAliasPorAplicacion( $conex, $emp, "concepto_medicamentos_mueven_inv" );
				if( esMMQServicioFarmaceutico($art['cod']) )
					$wcodcon = consultarAliasPorAplicacion( $conex, $emp, "concepto_materiales_mueven_inv" );
				
				$wnomcon = consultarNombreConceptos( $conex, $wcliame, $wcodcon );
				
				$wexidev = 0;
				
				$wcantidad = $art['can'];
				
				$wfecha=date("Y-m-d");		
				$whora = date("H:i:s");
				
				//Reemplazo las variables necesarias para la función validar_y_grabar_cargo
				$auxWbasedato = $wbasedato;
				$wbasedato = $wcliame;
				$wuse = $wusuario;
				
				//$dosProc = datos_desde_procedimiento(codigoArticulo, codigoConcepto, wccogra    , ccoActualPac, wcodemp , wfeccar, '', '*', 'on', false, '', fecha  , hora  , '*', '*');
				$datosProc = datos_desde_procedimiento( $art['cod']  , $wcodcon      , $cCentroCosto, $pac['sac'] , $wcodemp, $wfecha, '', '*', 'on', false, '', $wfecha, $whora, '*', '*');
				
				$wvaltar = $datosProc[ 'wvaltar' ];
				
				$wdevol = 'off';
				if( $tipTrans != 'C' )
					$wdevol  = 'on';
				
				$datos=array();
				$datos['whistoria']		=$pac['his']; // $whistoria;
				$datos['wing']			=$pac['ing']; // $wing;
				$datos['wno1']			=$infoPacienteCargos['Pacno1']; // $wno1;
				$datos['wno2']			=$infoPacienteCargos['Pacno2']; // $wno2;
				$datos['wap1']			=$infoPacienteCargos['Pacap1'];
				$datos['wap2']			=$infoPacienteCargos['Pacap2'];
				$datos['wdoc']			=$pac['doc']; // $wdoc;
				$datos['wcodemp']		=$wcodemp;	//				--> cliame_000024
				$datos['wnomemp']		=$wnomemp;	//			--> cliame_000024
				$datos['tipoEmpresa']	=$tipoEmpresa;	//			--> cliame_000024
				$datos['nitEmpresa']	=$nitEmpresa;	//			--> cliame_000024
				$datos['tipoPaciente']	=$tipoPaciente;	//		--> cliame_000101 Ingcla
				$datos['tipoIngreso']	=$tipoIngreso;	//		--> cliame_000101 Ingtin
				$datos['wser']			=$wser;			//		--> cliame_000101 Ingsei
				$datos['wfecing']		=$wfecing;		//		--> cliame_000101 Ingfei
				$datos['wtar']			=$wtar;			//		--> cliame_000024
				$datos['wcodcon']		=$wcodcon;		//		--> Codigo del concepto (0626 = materiales, 0616 = medicamentos)
				$datos['wnomcon']		=$wnomcon;		//		--> Nombre del concepto Cliame 200
				$datos['wprocod']		=$art['cod']; // $wprocod;				--> Codigo del articulo o del medicamento
				$datos['wpronom']		=$art['nom'];// $wpronom;				--> Nombre del articulo Artcom
				$datos['wcodter']		=''; // $wcodter;				--> ''
				$datos['wnomter']		=''; //$wnomter;				--> ''
				$datos['wporter']		=''; // $wporter;				--> ''
				$datos['grupoMedico']	=''; // $grupoMedico;			--> ''
				$datos['wterunix']		=''; // $wterunix;				--> ''
				$datos['wcantidad']		=$wcantidad; //$wcantidad;			--> cantidad
				$datos['wvaltar']		=$wvaltar;	//			--> valor PENDIENTE FUNCION
				$datos['wrecexc']		='R'; // $wrecexc;				--> 'R'
				$datos['wfacturable']	='S'; // $wfacturable;			--> 'S'
				$datos['wcco']			=$cCentroCosto;	// $wcco;					--> Centro de costos graba
				$datos['wccogra']		=$cCentroCosto;// $wccogra;				--> cco paciente
				$datos['wfeccar']		=$wfecha; // $wfeccar;				--> Fecha del cargo
				$datos['whora_cargo']	=$whora; // $whora_cargo.':00';	-->	Hora del cargo
				$datos['wconinv']		='on'; //$wconinv;				--> 'on'
				$datos['wconabo']		=''; //$wconabo;				--> ''
				$datos['wdevol']		=$wdevol; // $wdevol;				--> 'off'
				$datos['waprovecha']	='off'; // $waprovecha;			--> 'off'
				$datos['wconmvto']		=''; //$wconmvto;				--> ''
				//$datos['wexiste']		=$wexiste;				--> cantidad existente PENDIENTE FUNCION
				$datos['wexiste']		=$datosProc[ 'wexiste' ];	//				--> cantidad existente PENDIENTE FUNCION
				$datos['wbod']			='off'; //$wbod;					--> 'off'
				$datos['wconser']		='H'; //$wconser;				--> 'H'
				//$datos['wtipfac']		=$wtipfac;				--> tipo facturacion PENDIENTE FUNCION
				$datos['wtipfac']		="CODIGO";	//			--> tipo facturacion PENDIENTE FUNCION
				$datos['wexidev']		=$wexidev;	//			--> 0 
				$datos['wfecha']		=$wfecha;	//				--> fecha act
				$datos['whora']			=$whora;	//			--> hora act
				$datos['nomCajero']		=''; //$nomCajero;			--> ''
				$datos['cobraHonorarios']		= ''; // $cobraHonorarios;			--> ''
				$datos['wespecialidad']			= '*';
				$datos['wgraba_varios_terceros']= ''; // $wgraba_varios_terceros;		''
				$datos['wcodcedula']			= ''; // $wcodcedula;					''
				$datos['estaEnTurno']			= ''; // $estaEnTurno;					''
				$datos['tipoCuadroTurno']		= ''; // $tipoCuadroTurno;				''
				$datos['ccoActualPac']			= $pac['sac']; //$ccoActualPac;				--> Centro de costos actual del paciente	
				$datos['codHomologar']			= ''; // $codHomologar;				--> ''	
				$datos['validarCondicMedic']	= true;	//						--> FALSE
				$datos['estadoMonitor']			= '';
				$datos['respuesta_array']			= 'on';
				$datos['numCargoInv']			= $numCargoInv;
				$datos['linCargoInv']			= $linCargoInv;
				
				//Esto es nuevo
				$datos['desde_CargosPDA']			= true;

				//$codEmpParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
				$codEmpParticular = consultarAliasPorAplicacion($conex, $emp, 'codigoempresaparticular');

				// --> Si la empresa es particular esto se graba como excedente
				if($wcodemp == $codEmpParticular)
					$datos['wrecexc'] = 'R';	//Septiembre 11 de 2017

				// --> Valor excedente
				if($datos['wrecexc'] == 'E')
					$datos['wvaltarExce'] = round($wcantidad*$wvaltar);
				// --> Valor reconocido
				else
					$datos['wvaltarReco'] = round($wcantidad*$wvaltar);
				
				//Llamo la función de cargos de CARGOS DE ERP
				//echo "<br>validar_y_grabar_cargo 4697<br>"; //##BORRAR_SEBASTIAN_NEVADO
				$respuesta = validar_y_grabar_cargo($datos, false);
				//print_r( $respuesta ); //##BORRAR_SEBASTIAN_NEVADO
				
				
				//echo "<h1>"; var_dump( $respuesta ); echo "</h1>";
				//Dejo las variables como estaban
				$wbasedato = $auxWbasedato;
			}
			//else{ echo "<h1>empresa</h1>" ;}
		}
		//else{ echo "<h1>ingreso cliame</h1>" ;}
	}
	//else{ echo "<h1>ingreso movhos</h1>" ;}
	
}

/**
 * Se realiza llamado de factura inteligente.
 * @by: sebastian.nevado
 * @date: 2021-06-11
 * @return: object
 */
function llamarFacturacionInteligente($pac, $cCentroCosto, $sCodigo, $sNombre, $dCantidad, $tipTrans, $numCargoInv = '', $linCargoInv = '')
{
	//echo "<br>Inicia la facturación inteligente<br>"; //##BORRAR_SEBASTIAN_NEVADO
	global $wemp_pmla;
	global $conex;

	//Obtengo el alias por aplicación y defino parámetros
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	//$numCargoInv = '';
	//$linCargoInv = '';
	$pac['sac'] = consultarCcoPaciente($conex, $pac['his'], $pac['ing']);
	//echo "<br>cCentroCosto:".$cCentroCosto."<br>"; //##BORRAR_SEBASTIAN_NEVADO

	//Llamo facturación inteligente
	$artFactInteligente = array();
	// $artFactInteligente['cod'] = $presen[$i][$j]['cod'];
	// $artFactInteligente['nom'] = $row1[0];
	// $artFactInteligente['can'] = $can;
	
	$artFactInteligente['cod'] = $sCodigo;
	$artFactInteligente['nom'] = $sNombre;
	$artFactInteligente['can'] = $dCantidad;
	//print_r($artFactInteligente); //##BORRAR_SEBASTIAN_NEVADO
	CargarCargosErp($conex, $pac, $wmovhos, $wcliame, $artFactInteligente, $tipTrans, $numCargoInv, $linCargoInv, $cCentroCosto);
	//echo "<br>Finaliza la facturación inteligente<br>"; //##BORRAR_SEBASTIAN_NEVADO

	$aResultado = new stdClass();
	$aResultado->exito = true;
	$aResultado->mensaje = '';

	//Fin facturación inteligente
	return $aResultado;
}

/**
 * Se obtiene el nombre del paciente
 * @by: sebastian.nevado
 * @date: 2021-06-11
 * @return: array
 */
function consultarNombresPaciente( $conex, $his, $emp ){

	$val = false;

	$sql = "SELECT *
			  FROM root_000036, root_000037
			 WHERE orihis = '".$his."'
			   AND pacced = oriced
			   AND pactid = oritid
			   AND oriori = '".$emp."'
		";
		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows;
	}
	
	return $val;
}

/**
 * Consulta el nombre del concepto de acuerdo a su codigo
 * @by: sebastian.nevado
 * @date: 2021-06-11
 * @return: array
 */
function consultarNombreConceptos( $conex, $wcliame, $con ){

	$val = false;

	$sql = "SELECT *
			  FROM ".$wcliame."_000200
			 WHERE Grucod = '".$con."'
		";
		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[ 'Grudes' ];
	}
	
	return $val;
}

/**
 * Dice si un articulo es Material Medico Quirurgico por medio de servicio farmacéutico
 *
 * @param $art
 * @return unknown_type
 * @author: sebastian.nevado
 * @date: 2021/06/25
 *
 * Nota: SE considera material medico quirurgico si el grupo del
 * articulo no se encuentra en la taba 66 o pertenezca al grupo E00 o V00.
 * Ya no se considera MMQ los articulos del grupo V00
 */
function esMMQServicioFarmaceutico( $art ){

	global $conex;
	global $bd;
	
	echo "<br>esMMQ CargoCPX<br>"; //##BORRAR_SEBASTIAN_NEVADO

	$esmmq = false;

	$sql = "SELECT
				artcom, artgen, artgru, melgru, meltip
			FROM
				{$bd}_000026 LEFT OUTER JOIN {$bd}_000066
				ON melgru = SUBSTRING_INDEX( artgru, '-', 1 )
			WHERE
				artcod = '$art'
			";

	$res = mysql_query( $sql, $conex );

	if( $rows = mysql_fetch_array( $res ) ){
		if( (empty( $rows['melgru'] ) || $rows['melgru'] == 'E00' ) && !empty($rows['artcom']) ){
			$esmmq = true;
		}
		else{
			$esmmq = false;
		}
	}

	return $esmmq;
}

/**
 * Consulta el nombre del concepto de acuerdo a su codigo
 * @by: sebastian.nevado
 * @date: 2021-06-22
 * @return: array
 */
function consultarCcoPaciente( $conex, $sHistoria, $sIngreso ){
	
	global $wemp_pmla;
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$sQuery = "SELECT Ubisac
			FROM ".$wmovhos."_000018 
			WHERE Ubihis = ? AND Ubiing = ?
			ORDER BY id DESC
			LIMIT 1";
	
	//Preparo y envío los parámetros
	$sentencia = mysqli_prepare($conex, $sQuery);
	mysqli_stmt_bind_param($sentencia, "ss", $sHistoria, $sIngreso );
	mysqli_stmt_execute($sentencia);

	mysqli_stmt_bind_result($sentencia, $iCCo);
	mysqli_stmt_fetch($sentencia);
	
	$bResultado = isset($iCCo) ? $iCCo : null;

	return $bResultado;
}

?>
</body>
</html>