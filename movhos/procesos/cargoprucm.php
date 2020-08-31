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
    	.texto1{color:#003366;background:#FFDBA8;font-size:7pt;font-family:Tahoma;font-weight:bold;}	
    	.texto2{color:#003366;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto3{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto4{color:#003366;background:#f5f5dc;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto5{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto7{color:#003366;background:green;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	
   </style>
   
    <script type="text/javascript">
   function enter()
   {
	   a = window.opener;
//	   window.opener.producto.submit();
		a.document.producto.submit();
	   window.close();
   }
    </script>
    
</head>
<body>
<?php
include_once("conex.php");
//actualizacion: 2007-11-06 se crea la opcion del carro 

/**
 * Registra un articulo al paciente en el kardex electrónico
 * 
 * @param $art		Codigo del Aritculo del paciente
 * @param $his		Historia del paciente
 * @param $ing		Ingreso del paciente
 * @return bool		True si es verdaderos
 */
function registrarArticuloKE( $art, $his, $ing ){
	
	global $conex;
	global $bd;
	
	$sqlid="SELECT 
				max(id) 
			FROM 
				movhos_000054 
			WHERE 
				kadart = '$art'
				AND kadcdi > kaddis+0
				AND kadfec = '".date("Y-m-d")."'
				AND kadhis = '$his'  
	       		AND kading = '$ing'
			GROUP BY kadart";
	
	$resid = mysql_query( $sqlid, $conex );
	
	if( $row = mysql_fetch_array( $resid ) ){
		$id = $row[0];
	}
	else{
		$row[0] = "";
	}
	
	//Actualizando registro con el articulo cargado
	$sql = "UPDATE 
				movhos_000054 
	       	SET 
	       		kaddis = kaddis+1
	        WHERE 
	        	kadcdi >= kaddis+1 
	       		AND kadart = '$art' 
	       		AND kadhis = '$his'  
	       		AND kading = '$ing' 
	       		AND kadfec = '".date("Y-m-d")."'
	       		AND kadori = 'CM'
	       		AND kadcon = 'on'
	       		AND kadsus != 'on' 
	       		AND id = {$row[0]}";
	
	$res = mysql_query( $sql, $conex );
	
	if( $res && mysql_affected_rows() > 0 )
		return true;
	else
		return false;
}

/**
 * Condiciones para grabar en el KE
 * 
 * @param array $pac		informacion del paciente en el KE
 * @param array $art		informacion del articulo
 * @return 
 * 
 * Nota: Las condiciones son
 * - Tener KE (Si anteriormente tuvo un KE)
 * - Que la cantidad a dispensar (kadcdi) sea mayor a la cantidad dispensada (kaddis) 
 *   para los articulos, aunque hallan duplicados.
 * - Que tenga KE actualizado (Halla KE para el dia de hoy)
 * - Que el KE este confirmado (Que la doctora confirme el KE)
 * - Que el articulo a cargar este confirmado para CM
 */
function condicionesKE( &$pac, $art ){
	
	global $conex;
    global $wbasedato;
    
    $pac['sal'] = false;	//Indica si tiene saldo para poder dispensar
    $pac['art'] = false;	//Indica si el articulo existe para el paciente de ke
    $pac['ke'] = false;
	$pac['con'] = false;
	$pac['act'] = false;
	
	esKE( $pac['his'], $pac['ing'], $pacKE);
	
	$pac['ke'] = $pacKE['ke'];
	$pac['con'] = $pacKE['con'];
	$pac['act'] = $pacKE['keact'];
	
	//El articulo debe tener saldo antes de guardar
	$sql = "SELECT 
				SUM((kadcdi)-(kaddis)) as sal, kadart
			FROM 
				movhos_000054
			WHERE	
				kadhis = '{$pac['his']}' 
				AND kading = '{$pac['ing']}'
				AND kadart = '$art'
				AND kadfec = '".date("Y-m-d")."'
				AND kadcon = 'on'
				AND kadori = 'CM'
				AND kadsus != 'on'
			GROUP BY kadart";
	
	$res = mysql_query( $sql, $conex );

	if ( $rows = mysql_fetch_array( $res ) ){
		if( $rows['sal'] > 0 ){
			$pac['sal'] = true;
		}
		$pac['art'] = true;
	}
}

/**
 * Indica si el paciente se encuentra en Kardex Electronico o no
 * 
 * @param array $his	Paciente al que se le va a cargar los articulos
 * @param array $ing	Ingreso del paciente al que se le va a cargar los articulos
 * @return bool $ke		Devuelve true en caso de se Kardex electronico, en caso contrario false
 */

function esKE( $his, $ing, &$packe ){
	
	global $conex;
    global $wbasedato;
	
	$ke = 0;
	$pac = array();
	$pac['his'] = $his;
	$pac['ing'] = $ing;
	$pac['ke'] = false;
	$pac['con'] = false;
	$pac['act'] = false;
	
	//Busca kardex electronico para el paciente con la fecha mas reciente
	
	$sql = "SELECT 
				*, MAX(a.kadfec) as afd, b.Fecha_data as bfd 
			FROM 
				movhos_000054 a, movhos_000053 b 
	        WHERE 
	        	kadhis = '$his' AND
	        	kading = '$ing' AND 
	        	kadhis = karhis AND
	        	kading = karing AND
	        	a.Kadfec = b.Fecha_data
	        GROUP BY a.Kadfec
	        ORDER BY a.Kadfec DESC";

	$res = mysql_query( $sql, $conex );
	$pac['ke']=false;
	if( mysql_num_rows($res) > 0 ){
		$ke = 1;
		$rows = mysql_fetch_array( $res );
		$pac['ke']=true;			//Indica si tiene kardex o no
		if( $rows['Karcon'] == "on" ){
			$pac['con'] = true;		//Kardex confirmado
		}
		else{
			$pac['con'] = false;
		}
		if( $rows['afd'] == date("Y-m-d") && $rows['bfd'] == date("Y-m-d") ){
			$pac['keact']=true;		//Kardex actualizado
			$ke = 2;
		}
		else{
			$pac['keact']=false;
		}
		
//		$ke = true;
	}
	$packe = $pac;	
	return $ke;
}

/**
* Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts 
* existen dos opciones mandandole el paramentro tipo=C o para=A, asi ese Script realizara una u otra opcion
*/
function pintarTitulo()
{
    echo "<table ALIGN=CENTER width='50%'>"; 
    // echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
    echo "<tr><td class='titulo1'>CARGOS CENTRAL DE MEZCLAS</td></tr>";
    echo "<tr><td class='titulo2'>Fecha: " . date('Y-m-d') . "&nbsp Hora: " . (string)date("H:i:s") . "</td></tr></table></br>";
} 

function pintarConfi($cod, $var, $nom, $sub)
{
    echo "<form name='producto3' action='cargoprucm.php' method=post>";

    echo "<table ALIGN=CENTER width='50%'>";
    echo "<tr><td class='titulo4'>SE HA REALIZADO EL MOVIMIENTO EXITOSAMENTE</td></tr>";
    echo "<tr><td class='titulo2'>ARTICULO: " . $cod . "-" . $nom . "</td></tr>";
    echo "<tr><td class='titulo2'>" . $sub . ": " . $var . "</td></tr>";
} 

function pintarAlerta($mensaje)
{
    echo "<form name='producto3' action='cargoprucm.php' method=post>";

    echo "<table ALIGN=CENTER width='50%'>";
    echo "<tr><td class='titulo5'>" . $mensaje . "</td></tr>";
} 

function pintarBoton()
{
    echo "<tr><td >&nbsp;</td></tr>";
    echo "<tr><td ALIGN='CENTER' ><INPUT TYPE='button' NAME='ok' VALUE='ACEPTAR' onclick='enter()'></td></tr>";
    echo "</form>";
} 

function pintarPreparacion($preparacion, $escogidos, $carro)
{
    echo "<table align='center'>";
    echo "<tr><td colspan='6' class='titulo3' align='center'><b>INSUMOS DE PREPARACION ADICIONALES</b></td></tr>";
    echo "<td colspan='2' class='titulo3' align='center'><b>TIPO DE INSUMOS</b></td>";
    echo "<td colspan='4' class='titulo3' align='center'><b>PRESENTACION</b></td>";

    for ($i = 0; $i < count($preparacion); $i++)
    {
        $tam = count($preparacion[$i])-1;
        echo "<tr><td class='texto1' rowspan:'" . $tam . "' colspan='2' align='center'>" . $preparacion[$i]['nom'] . ": </td>";
        for ($j = 0;$j < $tam;$j++)
        {
            echo "<td class='texto1' colspan='1' align='left'><input type='checkbox' name='escogidos[" . $i . "][" . $j . "]' class='texto3' " . $escogidos[$i][$j] . "></td>";
            echo "<td class='texto1' colspan='3' align='left'>" . $preparacion[$i][$j] . " </td></tr><tr>";
            if ($j + 1 != $tam)
            {
                echo "<td class='texto1' colspan='2' align='center'>&nbsp</td>";
            } 
            echo "<input type='hidden' name='preparacion[" . $i . "][" . $j . "]' value='" . $preparacion[$i][$j] . "'></td>";
        } 
    } 
    echo "</table>";
    echo "<input type='hidden' name='carro' value='".$carro."'></td>";
    echo "<input type='hidden' name='grabar' value='0'></td>";
    echo "</form>";
} 

function pintarInsumos($inslis, $presen, $cod, $cco, $historia, $ingreso, $var, $servicio)
{
    echo "<form name='producto' action='cargoprucm.php' method=post>";
    echo "<table align='center'>";
    echo "<tr><td colspan='8' class='titulo3' align='center'><b>INSUMOS DEL PRODUCTO</b></td></tr>";
    echo "<td colspan='1' class='titulo3' align='center'><b>INSUMO</b></td>";
    echo "<td colspan='1' class='titulo3' align='center'><b>CANTIDAD</b></td>";
    echo "<td colspan='1' class='titulo3' align='center'><b>AJUSTE</b></td>";
    echo "<td colspan='1' class='titulo3' align='center'><b>FALTANTE</b></td>";
    echo "<td colspan='1' class='titulo3' align='center'><b>PRESENTACION</b></td>";
    echo "<td colspan='1' class='titulo4' align='center'><b>CAN. CARGO</b></td>";
    echo "<td colspan='1' class='titulo3' align='center'><b>AJUSTE</b></td>";
    echo "<td colspan='1' class='titulo3' align='center'><b>CAN. AJUSTE</b></td>";

    for ($i = 0; $i < count($inslis); $i++)
    {
        $tam = count($presen[$i]); 
        // echo $tam;
        echo "<tr><td class='texto1' rowspan:'" . $tam . "' colspan='1' align='center'>" . $inslis[$i]['cod'] . "-" . $inslis[$i]['nom'] . " (" . $inslis[$i]['pre'] . " ) </td>";
        echo "<td class='texto1' rowspan:'" . $tam . "' colspan='1' align='center'>" . $inslis[$i]['can'] . " </td>";
        echo "<td class='texto1' rowspan:'" . $tam . "' colspan='1' align='center'>" . $inslis[$i]['aju'] . " </td>";
        echo "<td class='texto1' rowspan:'" . $tam . "' colspan='1' align='center'>" . $inslis[$i]['fal'] . " </td>";

        echo "<input type='hidden' name='inslis[" . $i . "][cod]' value='" . $inslis[$i]['cod'] . "'></td>";
        echo "<input type='hidden' name='inslis[" . $i . "][nom]' value='" . $inslis[$i]['nom'] . "'></td>";
        echo "<input type='hidden' name='inslis[" . $i . "][pre]' value='" . $inslis[$i]['pre'] . "'></td>";
        echo "<input type='hidden' name='inslis[" . $i . "][can]' value='" . $inslis[$i]['can'] . "'></td>";
        echo "<input type='hidden' name='inslis[" . $i . "][aju]' value='" . $inslis[$i]['aju'] . "'></td>";
        echo "<input type='hidden' name='inslis[" . $i . "][fal]' value='" . $inslis[$i]['fal'] . "'></td>";
        for ($j = 0;$j < $tam;$j++)
        {
            if ($tam == 1)
            {
                $faltante = $inslis[$i]['can'] - $presen[$i][$j]['caj'];
                echo "<td class='texto1' colspan='1' align='center'>" . $presen[$i][$j]['nom'] . " </td>";
                echo "<td class='texto7' colspan='1' align='center'><input type='text' name='presen[" . $i . "][" . $j . "][can]' class='texto3' value='" . $faltante . "' size='5'></td>";
                echo "<td class='texto1' colspan='1' align='center'>" . $presen[$i][$j]['aju'] . "</td>";
                echo "<td class='texto1' colspan='1' align='center'><input type='text' name='presen[" . $i . "][" . $j . "][caj]' class='texto3' value='" . $presen[$i][$j]['caj'] . "' size='5'></td></tr><tr>";
            } 
            else
            {
                echo "<td class='texto1' colspan='1' align='center'>" . $presen[$i][$j]['nom'] . " </td>";
                echo "<td class='texto7' colspan='1' align='center'><input type='text' name='presen[" . $i . "][" . $j . "][can]' class='texto3' value='" . $presen[$i][$j]['can'] . "' size='5'></td>";
                echo "<td class='texto1' colspan='1' align='center'>" . $presen[$i][$j]['aju'] . "</td>";
                echo "<td class='texto1' colspan='1' align='center'><input type='text' name='presen[" . $i . "][" . $j . "][caj]' class='texto3' value='" . $presen[$i][$j]['caj'] . "' size='5'></td></tr><tr>";
            } 
            echo "<input type='hidden' name='presen[" . $i . "][" . $j . "][cod]' value='" . $presen[$i][$j]['cod'] . "'></td>";
            echo "<input type='hidden' name='presen[" . $i . "][" . $j . "][nom]' value='" . $presen[$i][$j]['nom'] . "'></td>";
            echo "<input type='hidden' name='presen[" . $i . "][" . $j . "][cnv]' value='" . $presen[$i][$j]['cnv'] . "'></td>";
            echo "<input type='hidden' name='presen[" . $i . "][" . $j . "][aju]' value='" . $presen[$i][$j]['aju'] . "'></td>";

            if ($j + 1 != $tam)
            {
                echo "<td class='texto1' colspan='2' align='center'>&nbsp</td>";
                echo "<td class='texto1' colspan='2' align='center'>&nbsp</td>";
            } 
        } 
    } 
    echo "<tr><td colspan=8 class='titulo3' align='center'><INPUT TYPE='submit' NAME='GRABAR' VALUE='GRABAR' ></td></tr>";
    echo "<input type='hidden' name='cod' value='" . $cod . "'></td>";
    echo "<input type='hidden' name='cco' value='" . $cco . "'></td>";
    echo "<input type='hidden' name='historia' value='" . $historia . "'></td>";
    echo "<input type='hidden' name='ingreso' value='" . $ingreso . "'></td>";
    echo "<input type='hidden' name='var' value='" . $var . "'></td>";
    echo "<input type='hidden' name='servicio' value='" . $servicio . "'></td>";
    echo "</table></br>";
} 
/**
* conusltamos los detalles del producto, sobretodo los insumos que lo componenen ($inslis)
* 
* Pide:
* 
* @param caracter $codigo , codigo del producto
* 
* Retorna:
* @param caracter $via , via de infusion
* @param caracter $tfd , tiempo de infusion en horas
* @param caracter $tfh , tienmpo de infusion en minutos
* @param caracter $tvd , tiempo de vencimiento en hras
* @param caracter $tvh , tiempo de vencimiento en minutos
* @param date $fecha ,  fecha de creacion
* @param vector $inslis , lista de insumos que lo componen
* @param caracter $tippro , tipo de porducto (codigo del tipo-descripcion-codificado o no)
* @param boolean $foto si es fotosensible
* @param boolean $neve si debe conservarse en nevera
*/
function consultarInsumos($codigo, &$inslis)
{
    global $conex;
    global $wbasedato;

    $q = " SELECT Pdeins, Pdecan, Artcom, Artgen, Artuni, Unides "
     . "       FROM " . $wbasedato . "_000003, " . $wbasedato . "_000002, movhos_000027 "
     . "    WHERE  Pdepro = '" . $codigo . "' "
     . "       AND Pdeest = 'on' "
     . "       AND Pdeins= Artcod "
     . "       AND Artuni= Unicod "
     . "       AND Uniest='on' "
     . "    Order by 1 ";

    $res = mysql_query($q, $conex);
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
        for ($i = 0;$i < $num;$i++)
        {
            $row = mysql_fetch_array($res);
            $inslis[$i]['cod'] = $row[0];
            $inslis[$i]['nom'] = str_replace('-', ' ', $row[2]);
            $inslis[$i]['pre'] = $row[4] . '-' . $row[5];
            $inslis[$i]['can'] = $row[1];
        } 
    } 
} 

function consultarPresentaciones(&$insumo, $cco, $historia, $ingreso)
{
    global $conex;
    global $wbasedato;

    $q = " SELECT Apppre, Artcom, Appcnv "
     . "        FROM  " . $wbasedato . "_000009, movhos_000026 "
     . "      WHERE  Appcod='" . $insumo['cod'] . "' "
     . "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
     . "            and Appest='on' "
     . "            and Apppre=Artcod ";

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);
    $insumo['aju'] = 0;
    $cuenta = $insumo['can'];
    if ($num1 > 0)
    {
        for ($i = 0;$i < $num1;$i++)
        {
            $row1 = mysql_fetch_array($res1);
            $presentacion[$i]['cod'] = $row1['Apppre'];
            $presentacion[$i]['nom'] = $row1['Artcom'];
            $presentacion[$i]['cnv'] = $row1['Appcnv']; 
            // consulto el ajuste que hay para la presentación
            $q = " SELECT Ajpart, Ajpcan, Ajpfve, Ajphve, Artcom, Artgen "
             . "        FROM " . $wbasedato . "_000010, " . $wbasedato . "_000009, movhos_000026 "
             . "      WHERE Ajphis= '" . $historia . "' "
             . "            and Ajpest ='on' "
             . "            and Ajping = '" . $ingreso . "' "
             . "            and Ajpcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
             . "  		  and Ajpart = Apppre "
             . "            and Apppre = '" . $row1['Apppre'] . "' "
             . "            and Appest = 'on' "
             . "            and Artcod = Ajpart "
             . "           order by  Ajpfve desc";

            $res2 = mysql_query($q, $conex);
            $num2 = mysql_num_rows($res1);
            if ($num2 > 0)
            {
                $row2 = mysql_fetch_array($res2);
                if ($row2['Ajpfve'] > date('Y-m-d'))
                {
                    $presentacion[$i]['aju'] = $row2['Ajpcan'];
                    $presentacion[$i]['can'] = '';
                    $insumo['aju'] = $insumo['aju'] + $row2['Ajpcan'];

                    if ($cuenta > 0 and $row2['Ajpcan'] > 0)
                    {
                        if ($cuenta > $row2['Ajpcan'])
                        {
                            $presentacion[$i]['caj'] = $row2['Ajpcan'];
                            $cuenta = $cuenta - $presentacion[$i]['caj'];
                        } 
                        else if ($cuenta <= $row2['Ajpcan'])
                        {
                            $presentacion[$i]['caj'] = $cuenta;
                            $cuenta = 0;
                        } 
                    } 
                    else
                    {
                        $presentacion[$i]['caj'] = '';
                    } 
                } 
                else
                {
                    $presentacion[$i]['aju'] = 0;
                    $presentacion[$i]['can'] = '';
                    $presentacion[$i]['caj'] = '';
                } 
            } 
            else
            {
                $presentacion[$i]['aju'] = 0;
                $presentacion[$i]['can'] = '';
                $presentacion[$i]['caj'] = '';
            } 
        } 
    } 
    return $presentacion;
} 

/**
* Se consultan aquellos insumos tipo material quirurgico en el maestro de tipo de articulos
* ya que estos pueden ser cargados al paciente sin necesidad de pertenecer al lote
* 
* @param unknown_type $escogidos , se incializan todos los insumos como checkbox vacio
* @return unknown $preparacion, lista de insumos
*/
function consultarPreparacion(&$escogidos)
{
    global $conex;
    global $wbasedato;

    $q = " SELECT Tipcod, Tipdes "
     . "        FROM " . $wbasedato . "_000001 "
     . "      WHERE Tipmmq= 'on' "
     . "            and Tipest ='on' ";

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);
    for ($i = 0; $i < $num1; $i++)
    {
        $row = mysql_fetch_array($res1);
        $preparacion [$i]['nom'] = $row[1]; 
        // consulto los conceptos
        $q = " SELECT Apppre, C.Artcom, Appuni, Appcod"
         . "        FROM " . $wbasedato . "_000002 A, " . $wbasedato . "_000009 B, movhos_000026 C "
         . "      WHERE A.Arttip = '" . $row[0] . "' "
         . "        AND A.Artcod = B.Appcod "
         . "        AND A.Artest='on' "
         . "        AND B.Apppre=C.Artcod ";

        $res2 = mysql_query($q, $conex);
        $num2 = mysql_num_rows($res2);

        for ($j = 0;$j < $num2;$j++)
        {
            $row2 = mysql_fetch_array($res2);
            $preparacion[$i][$j] = $row2[0] . '-' . $row2[1] . '-' . $row2[2] . '-' . $row2[3];
            if (!isset($escogidos[$i][$j]))
            {
                $escogidos[$i][$j] = '';
            } 
            else
            {
                $escogidos[$i][$j] = 'checked';
            } 
        } 
    } 

    return $preparacion;
} 

/**
* Se valida que haya existencia de lo que se va a cargar, si es un producto, que haya cantidad en el lote
* si es un insumo, que haya existencia de la presentacion
* 
* @param vector $insumo caracteriticas del producto o insumo que se va a cargar
* @param caracter $cco centro de costos (codig-descripcion)
* @param caracter $otro lote para producto, presentacion para insumo
* @param caracter $tipo si es cargo o averia
* @return numerico $val  segun el error en la validacion retorna un numero
*/
function validarMatrix($cod, $cco, $otro, $tip, &$mensaje)
{
    global $conex;
    global $wbasedato;

    $q = " SELECT karexi FROM " . $wbasedato . "_000005 where karcod='" . $cod . "' and Karcco= mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);

    if ($num1 > 0)
    {
        $row1 = mysql_fetch_array($res1);
        if ($row1[0] > 0)
        {
            if ($tip == 'on')
            {
                $q = " SELECT Plosal, Plofve, plohve "
                 . "       FROM " . $wbasedato . "_000004 "
                 . "    WHERE Plopro = '" . $cod . "' "
                 . "       AND Plocod='" . $otro . "' "
                 . "       AND Ploest='on' "
                 . "       AND Plocco= mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
            } 
            else
            {
                $q = " SELECT Appexi "
                 . "        FROM  " . $wbasedato . "_000009 "
                 . "      WHERE Appcod='" . $cod . "' "
                 . "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
                 . "            and Appest='on' "
                 . "            and Apppre=mid('" . $otro . "',1,instr('" . $otro . "','-')-1)";
            } 

            $res2 = mysql_query($q, $conex);
            $num2 = mysql_num_rows($res1);
            if ($num2 > 0)
            {
                $row2 = mysql_fetch_array($res2);
                if ($row2[0] <= 0)
                {
                    $mensaje = 'SIN EXISTENCIAS ESPECIFICAS';
                    return false;
                } 
                else if ($tip == 'on' and $row2[1] < date('Y-m-d'))
                {
                    $mensaje = 'PRODUCTO VENCIDO';
                    return false;
                } 
                else
                {
                    return true;
                } 
            } 
            else
            {
                $mensaje = 'SIN EXISTENCIAS ESPECIFICAS';
                return false;
            } 
        } 
        else
        {
            $mensaje = 'SIN EXISTENCIAS EN EL KARDEX DE INVENTARIOS';
            return false;
        } 
    } 
    else
    {
        $mensaje = 'SIN EXISTENCIAS EN EL KARDEX DE INVENTARIOS';
        return false;
    } 
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
     . "      AND Concar = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);

    $q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '-1'"
     . "      AND Concar = 'on' "
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
function grabarEncabezadoEntradaMatrix(&$codigo, &$consecutivo, $cco, $cco2, $usuario)
{
    global $conex;
    global $wbasedato;

    $q = "lock table " . $wbasedato . "_000008 LOW_PRIORITY WRITE";
    $errlock = mysql_query($q, $conex);

    $anexo = $codigo . '-' . $consecutivo;
    $q = "   UPDATE " . $wbasedato . "_000008 "
     . "      SET Concon = (Concon + 1) "
     . "    WHERE Conind = '1' "
     . "      AND Conane = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);

    $q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '1'"
     . "      AND Conane = 'on' "
     . "      AND Conest = 'on' ";

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

    if( empty($ajucan) ) {
    	$ajucan = 0;
    }
    $q = " INSERT INTO " . $wbasedato . "_000007 (   Medico        ,   Fecha_data            ,     Hora_data                  ,   Mdecon         ,      Mdedoc            ,     Mdeart     ,             Mdecan  ,      Mdefve  ,  Mdenlo        , Mdepre            ,  Mdepaj          , Mdecaj          ,  Mdecto        , Mdeest ,  Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $consecutivo . "','" . $inscod . "', '" . $cantidad . "' , '0000-00-00' , '" . $lote . "',   '" . $prese . "', '" . $ajupre . "','" . $ajucan . "','" . $total . "',  'on'  , 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN ARTICULO " . mysql_error());
} 

/**
* Elimina un articulo determinado del inventario
* 
* @param caracter $inscod codigo del articulo
* @param caracter $cco centro de costos (codigo-descripcion)
* @param caracter $lote es vacio si no tiene lote
* @param caracter $dato numero del lote si tienen lote o presentacion si es un insumo
*/
function descontarArticuloMatrix($inscod, $cco, $lote, $dato)
{
    global $conex;
    global $wbasedato;

    global $conex;
    global $wbasedato;

    if ($lote != '')
    {
        $q = "   UPDATE " . $wbasedato . "_000005 "
         . "      SET karexi = karexi - 1 "
         . "    WHERE Karcod = '" . $inscod . "' "
         . "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

        $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR EL ARTICULO " . mysql_error());

        $q = "   UPDATE " . $wbasedato . "_000004 "
         . "      SET Plosal = Plosal-1 "
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
         . "      SET karexi = karexi - (1*" . $row2[0] . ") "
         . "    WHERE Karcod = '" . $inscod . "' "
         . "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

        $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR EL ARTICULO1 " . mysql_error());

        $q = "   UPDATE " . $wbasedato . "_000009 "
         . "      SET Appexi = Appexi- 1*Appcnv "
         . "    WHERE Appcod =  '" . $inscod . "' "
         . "      AND Apppre='" . $dato . "' "
         . "      AND Appest ='on' "
         . "      AND Appcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
    } 

    $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR UN INSUMO " . mysql_error());
} 

/**
* Se actualiza el ajuste que hay para un insumo
* 
* @param numerico $faltante ,  cantidad que falta de lo que se necsita de la dosis
* @param caracter $presentacion2 presentacion a cargar
* @param caracter $presentacion1 presentacion que habia antes en saldo
* @param numerico $cantidad cantidad para el producto
* @param caracter $historia numero de historia
* @param caracter $cco centro de costos (codigo-descripcion)
* @param caracter $usuario codigo del usuario que graba
* @param caracter $ingreso numero del ingreso
*/
function actualizarAjuste($presentacion, $ajuste, $cantidad, $historia, $cco, $usuario, $ingreso, $cnv, $total)
{
    global $conex;
    global $wbasedato;

    if ($ajuste > 0)
    {
        $q = "   UPDATE " . $wbasedato . "_000010 "
         . "      SET Ajpcan = (Ajpcan - " . $ajuste . ") "
         . "    WHERE Ajphis= '" . $historia . "' "
         . "      AND Ajping= '" . $ingreso . "' "
         . "      AND Ajpart= '" . $presentacion . "' "
         . "      AND Ajpcco= '" . $cco . "' "
         . "      AND Ajpest = 'on' ";

        $res1 = mysql_query($q, $conex);
    } 

    if ($cantidad > 0)
    {
        $saldo = $cantidad * $cnv + $ajuste - $total;

        $q = " SELECT Arttve "
         . "        FROM  " . $wbasedato . "_000009, " . $wbasedato . "_000002 "
         . "      WHERE Apppre='" . $presentacion . "' "
         . "            and Appcco='" . $cco . "' "
         . "            and Appest='on' "
         . "            and Appcod=Artcod ";

        $res1 = mysql_query($q, $conex);
        $row1 = mysql_fetch_array($res1);
        $tiempo = mktime(0, 0, 0, date('m'), date('d'), date('Y')) + ($row1[0] * 24 * 60 * 60);
        $tiempo = date('Y-m-d', $tiempo);

        if ($saldo > 0)
        {
            $q = " SELECT * "
             . "        FROM " . $wbasedato . "_000010 "
             . "      WHERE Ajphis= '" . $historia . "' "
             . "            and Ajpest ='on' "
             . "            and Ajping = '" . $ingreso . "' "
             . "            and Ajpcco = '" . $cco . "' "
             . "  		  and Ajpart ='" . $presentacion . "'";

            $res1 = mysql_query($q, $conex);
            $num1 = mysql_num_rows($res1);
            if ($num1 > 0)
            {
                $q = "   UPDATE " . $wbasedato . "_000010 "
                 . "      SET Ajpcan = " . $saldo . ", "
                 . "          Ajpfve = '" . $tiempo . "' "
                 . "    WHERE Ajphis= '" . $historia . "' "
                 . "      AND Ajping= '" . $ingreso . "' "
                 . "      AND Ajpart= '" . $presentacion . "'"
                 . "      AND Ajpcco= '" . $cco . "' "
                 . "      AND Ajpest = 'on' ";
            } 
            else
            {
                $q = " INSERT INTO " . $wbasedato . "_000010 (   Medico       ,           Fecha_data,                  Hora_data,          Ajphis,          Ajping ,     Ajpcco,   Ajpfec  ,       Ajphor,     Ajpfve,        Ajphve ,                    Ajpart    ,       Ajpcan,  Ajpest, Seguridad) "
                 . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $historia . "', '" . $ingreso . "' , '" . $cco . "', '" . date('Y-m-d') . "' ,   '" . (string)date("H:i:s") . "',  '" . $tiempo . "' , '" . (string)date("H:i:s") . "',   '" . $presentacion . "', '" . $saldo . "', 'on', 'C-" . $usuario . "') ";
            } 

            $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO ACTUALIZAR EL AJUSTE DE PRESENTACION " . mysql_error());
        } 
        // echo $saldo;
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
    $wbasedato = 'cenpro';
    

    


    pintarTitulo(); //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts
    $bd = 'movhos'; 
    // invoco la funcion connectOdbc del inlcude de ana, para saber si unix responde, en caso contrario,
    // este programa no debe usarse
    // include_once("pda/tablas.php");
    include_once("movhos/fxValidacionArticulo.php");
    include_once("movhos/registro_tablas.php");
    include_once("movhos/otros.php");
    include_once("CENPRO/funciones.php");
    connectOdbc(&$conex_o, 'facturacion');

    if ($conex_o != 0)
    {
        $tipTrans = 'C'; //segun ana es una transaccion de cargo
        $aprov = true; //siempre es por aprovechamiento;
        $exp = explode('-', $cco);
        $centro['cod'] = $exp[0];
        $centro['neg'] = false;
        getCco(&$centro, $tipTrans, '01');
        $pac['his'] = $historia;
        $pac['ing'] = $ingreso;
        $cns = 0;
        $date = date('Y-m-d');
        $art['ini'] = $cod;
        $art['ubi'] = 'US';
        $serv['cod'] = $servicio;
        $art['ser'] = $servicio;
        getCco(&$serv, $tipTrans, '01');
        if(!$serv['apl'])
		{
			$art['dis'] = $carro;
		}
		else 
		{
			$art['dis'] = 'off';
		}
        $ronApl = date("G:i - A"); 
        // consulto los datos del usuario de la sesion
        $pos = strpos($user, "-");
        $wusuario = substr($user, $pos + 1, strlen($user)); //extraigo el codigo del usuario        
        $usu = $wusuario; 
        // consulto los centros de costos que se administran con esta aplicacion
        // estos se cargan en un select llamado ccos.
        // consultamos si el producto es codificado o no
        $q = "SELECT Artcom, Tipcdo, Tippro "
         . "     FROM   " . $wbasedato . "_000002, movhos_000027, " . $wbasedato . "_000001 "
         . "   WHERE Artcod='" . $cod . "' "
         . "     AND Unicod = Artuni "
         . "     AND Tipcod = Arttip "
         . "     AND Tipest = 'on' ";

        $res1 = mysql_query($q, $conex);
        $num1 = mysql_num_rows($res1);
        $row1 = mysql_fetch_array($res1);

        switch ($row1[2])
        {
            case 'on':

                if (!isset($var) or $var == '')
                {
                    pintarAlerta('DEBE SELECCIONAR EL LOTE QUE VA A CARGAR');
                    pintarBoton();
                } 
                else
                {
                    $art['lot'] = $var;
                    $val = validarMatrix($cod, $cco, $var, 'on', &$mensaje);
                    if (!$val)
                    {
                        pintarAlerta($mensaje);
                        pintarBoton();
                    } 
                    else
                    {
                        if ($row1[1] == 'on')
                        {//1
                            $art['cod'] = $cod;
                            $art['neg'] = false;
                            $art['can'] = 1;

                            $res = ArticuloExiste (&$art, &$error);
                            if ($res)
                            {//2
                                $res = TarifaSaldo($art, $centro, $tipTrans, $aprov, &$error);
                                if ($res)
                                {//3 
                                	//Busco la información del paciente con Kardex Electronico
//                                	esKE( $pac['his'], $pac['ing'], $packe );
									$packe = $pac;
                                	condicionesKE( $packe, $cod );
                                	if( !$packe['ke'] || ( $packe['ke'] && $packe['act'] ) )
                                	{
                                		if( !$packe['ke'] || ( $packe['ke'] && $packe['art'] ) )
                                		{
                                			if( !$packe['ke'] || ( $packe['ke'] && $packe['con'] ) )
                                			{
		                                		if( !$packe['ke'] || ( $packe['ke'] && $packe['sal'] ) )
		                                		{
		                                			$val = false;
				                                    // grabo el encabezado del movimiento
				                                    $dronum = '';			                      
				                                    Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, &$date, &$cns, &$dronum, &$drolin, true, $usu, &$error);
				                                    grabarEncabezadoSalidaMatrix(&$codigo, &$consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $dronum);
				                                    $numtra = $codigo . '-' . $consecutivo; //actualizamos el numero del movimeinto real
				                                    $dato = $var . "-" . $cod;
				                                    grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '', 1, 1);
				                                    descontarArticuloMatrix($cod, $cco, 'on', $var);
				                                    grabarEncabezadoEntradaMatrix(&$codigo, &$consecutivo, $exp[0], $historia . '-' . $ingreso, $wusuario);
				                                    grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '', $art['can'], $art['can']);
				                                    $res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, &$error);
				                                    if (!$res)
				                                    {
				                                        pintarAlerta('EL ARTICULO NO HA PODIDO SER CARGADO A ITDRO');
				                                        $art['ubi'] = 'M';
				                                    } 
				                                    registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, &$error);
				
				                                    if (!$centro['apl'] and $serv['apl'])
				                                    {
				                                        $centro['apl'] = true;
				                                    } 
				
				                                    if (!$centro['apl'] and !$serv['apl'])
				                                    {
				                                        $val = registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, &$error);
				                                    } 
				                                    else
				                                    {
				                                        $val = registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, &$error);
				                                        $trans['num'] = $dronum;
				                                        if ($drolin == 1)
				                                        {
				                                            $trans['lin'] = 1;
				                                        } 
				                                        else
				                                        {
				                                            $trans['lin'] = '';
				                                        } 
				                                        $val = registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, &$error);
				                                    } 
				                                    pintarConfi($cod, $var, $row1[0], 'LOTE');
				                                    
				                                    if( $packe['ke'] && $packe['act'] && $packe['con'] && $packe['sal'] ){
				                                    	registrarArticuloKE( $art['cod'], $pac['his'], $pac['ing']);
				                                    }
				                                    pintarBoton();
				
				                                    ?>
													<script>
				                                        window . opener . producto . submit();
				                                        window . close();
				                                     </script >
				                                    <?php
		                                		}
		                                		else{
		                                			pintarAlerta('EL ARTICULO YA FUE DISPENSADO EN EL KE1');
	                                    			pintarBoton();
		                                		}
	                                		}
	                                		else{
	                                			pintarAlerta('EL KARDEX ELECTRONICO NO SE HA CONFIRMADO1');
	                                    		pintarBoton();
	                                		}
                                		}
                                		else{
                                			pintarAlerta('EL ARTICULO NO HA SIDO CARGADO AL PACIENTE1');
                                    		pintarBoton();
                                		}
                                	}
                                	else{
                                		pintarAlerta('NO TIENE KARDEX ELECTRONICO ACTUALIZADO1');
                                    	pintarBoton();
                                	}
                                }//fin3 
                                else
                                {
                                    pintarAlerta('EL ARTICULO A CARGAR NO TIENE TARIFA EN UNIX');
                                    pintarBoton();
                                } 
                            }//fin2 
                            else
                            {
                                pintarAlerta('EL ARTICULO A CARGAR NO EXISTE EN UNIX');
                                pintarBoton();
                            } 
                        }//fin1 
                        else
                        {
                            if (!isset($grabar))
                            { 
                                // consulto la lista de insumos que componen el producto
                                consultarInsumos($cod, &$inslis); 
                                // para cada insumo consultamos las presentaciones y su ajuste
                                for ($i = 0; $i < count($inslis); $i++)
                                {
                                    $presen[$i] = consultarPresentaciones(&$inslis[$i], $cco, $historia, $ingreso);
                                    $inslis[$i]['fal'] = $inslis[$i]['can'] - $inslis[$i]['aju'];
                                    if ($inslis[$i]['fal'] < 0)
                                    {
                                        $inslis[$i]['fal'] = 0;
                                    } 
                                } 
                                pintarInsumos($inslis, $presen , $cod, $cco, $historia, $ingreso, $var, $servicio); 
                                // aca voy a investigar la lista de insumos que se pueden cargar con el producto
                                $preparacion = consultarPreparacion(&$escogidos);
                                pintarPreparacion($preparacion, $escogidos, $carro);
                            } 
                            else
                            {
                                $val = true;
                                $mensaje = ''; 
                                // validamos que la suma utilizada por las fracciones sea igual al faltante
                                for ($i = 0; $i < count($inslis); $i++)
                                {
                                    $faltante = 0;
                                    $cantidad = 0;
                                    for ($j = 0; $j < count($presen[$i]); $j++)
                                    {
                                        if ($presen[$i][$j]['caj'] != '' and $presen[$i][$j]['caj'] > $presen[$i][$j]['aju'])
                                        {
                                            $mensaje = $mensaje . ' Las cantidades ingresadas para el ajuste de ' . $presen[$i][$j]['cod'] . ' son mayores al saldo de ajuste.';
                                            $val = false;
                                        } 
                                        if ($presen[$i][$j]['can'] != '')
                                        {
                                            $faltante = $faltante + $presen[$i][$j]['can'];
                                        } 

                                        if ($presen[$i][$j]['aju'] > 0 and $presen[$i][$j]['can'] > 0 and $presen[$i][$j]['caj'] != $presen[$i][$j]['caj'])
                                        {
                                            $mensaje = $mensaje . 'Antes de agregar cantidad de la presentación ' . $presen[$i][$j]['cod'] . ' debe consumir el saldo de ajuste.';
                                            $val = false;
                                        } 

                                        $cantidad = $cantidad + $presen[$i][$j]['can'] + $presen[$i][$j]['caj'];
                                    } 

                                    if (round($faltante, 4) < round($inslis[$i]['fal'], 4))
                                    {
                                        $mensaje = $mensaje . ' Las cantidades a cargar del insumo ' . $inslis[$i]['cod'] . ' son inferiores al faltante.';
                                        $val = false;
                                    } 

                                    if (round($cantidad, 4) < round($inslis[$i]['can'], 4))
                                    {
                                        $mensaje = $mensaje . ' Las cantidades ingresadas del insumo ' . $inslis[$i]['cod'] . ' son inferiores a la cantidad en el Producto.';
                                        $val = false;
                                    } 

                                    if (round($faltante, 4) > round($inslis[$i]['fal'], 4))
                                    {
                                        $mensaje = $mensaje . ' Las cantidades a cargar del insumo ' . $inslis[$i]['cod'] . ' son superiores al faltante.';
                                        $val = false;
                                    } 

                                    if (round($cantidad, 4) > round($inslis[$i]['can'], 4))
                                    {
                                        $mensaje = $mensaje . ' Las cantidades ingresadas del insumo ' . $inslis[$i]['cod'] . ' son superiores a la cantidad en el Producto.';
                                        $val = false;
                                    } 
                                } 

                                if ($val)
                                {
                                    $art['neg'] = false;

                                    for ($i = 0; $i < count($inslis); $i++)
                                    {
                                        for ($j = 0; $j < count($presen[$i]); $j++)
                                        {
                                            if ($presen[$i][$j]['can'] > 0)
                                            {
                                                $art['cod'] = $presen[$i][$j]['cod'];
                                                $art['can'] = $presen[$i][$j]['can'];
                                                $res = ArticuloExiste (&$art, &$error);
                                                if ($res)
                                                {
                                                    $res = TarifaSaldo($art, $centro, $tipTrans, $aprov, &$error);
                                                    if (!$res)
                                                    {
	                                                    $fin = 1;
                                                        pintarAlert1('El articulo' . $presen[$i][$j]['cod'] . 'no  tiene saldo unix');
                                                    } 
                                                } 
                                                else
                                                {
	                                                $fin = 1;
                                                    pintarAlert1('El articulo' . $presen[$i][$j]['cod'] . 'no existe e unix');
                                                } 
                                            } 
                                        } 
                                    } 
                                    if (!isset($fin))
                                    {
                                    	//Busco la información del paciente con Kardex Electronico
                                    	//esKE( $pac['his'], $pac['ing'], $packe );
                                    	$packe = $pac;
                                    	condicionesKE( $packe, $cod );
                                    	if( !$packe['ke'] || ( $packe['ke'] && $packe['act'] ) )
                                    	{
                                    		if( !$packe['ke'] || ( $packe['ke'] && $packe['con'] ) )
                                    		{
                                    			if( !$packe['ke'] || ( $packe['ke'] && $packe['art'] ) )
                                    			{
	                                    			if( !$packe['ke'] || ( $packe['ke'] && $packe['sal'] ) )
	                                    			{
				                                        // grabo el encabezado del movimiento
				                                        $dronum = '';
				                                        Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, &$date, &$cns, &$dronum, &$drolin, true, $usu, &$error);
				                                        $ind = 1;
				                                        grabarEncabezadoSalidaMatrix(&$codigo, &$consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $dronum);
				                                        $numtra = $codigo . '-' . $consecutivo; //actualizamos el numero del movimeinto real
				                                        $dato = $var . "-" . $cod;
				                                        grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '', 1, 1);
				                                        descontarArticuloMatrix($cod, $cco, 'on', $var);
				                                        grabarEncabezadoEntradaMatrix(&$codigo, &$consecutivo, $exp[0], $historia . '-' . $ingreso, $wusuario);
				                                        for ($i = 0; $i < count($inslis); $i++)
				                                        {
				                                            for ($j = 0; $j < count($presen[$i]); $j++)
				                                            {
				                                                if ($presen[$i][$j]['can'] > 0 or $presen[$i][$j]['caj'] > 0)
				                                                {
				                                                    if ($presen[$i][$j]['can'] > 0 and $presen[$i][$j]['caj'] > 0)
				                                                    {
				                                                        $can = ceil($presen[$i][$j]['can'] / $presen[$i][$j]['cnv']);
				                                                        $tot = $presen[$i][$j]['can'] + $presen[$i][$j]['caj'];
				                                                        grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $presen[$i][$j]['cod'] . '-' . $presen[$i][$j]['nom'], $dato, $presen[$i][$j]['cod'] . '-' . $presen[$i][$j]['nom'], $presen[$i][$j]['caj'], $can, $presen[$i][$j]['can'] + $presen[$i][$j]['caj']);
				                                                    } 
				                                                    else if ($presen[$i][$j]['can'] > 0)
				                                                    {
				                                                        $can = ceil($presen[$i][$j]['can'] / $presen[$i][$j]['cnv']);
				
				                                                        $tot = $presen[$i][$j]['can'];
				                                                        grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $presen[$i][$j]['cod'] . '-' . $presen[$i][$j]['nom'], $dato, '', 0, $can, $presen[$i][$j]['can']);
				                                                    } 
				                                                    else if ($presen[$i][$j]['caj'] > 0)
				                                                    {
				                                                        $can = 0;
				                                                        $tot = $presen[$i][$j]['caj'];
				                                                        grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, $presen[$i][$j]['cod'] . '-' . $presen[$i][$j]['nom'], $presen[$i][$j]['caj'], 0, $presen[$i][$j]['caj']);
				                                                    } 
				                                                    actualizarAjuste($presen[$i][$j]['cod'], $presen[$i][$j]['caj'], $can, $historia, $centro['cod'], $wusuario, $ingreso, $presen[$i][$j]['cnv'], $tot);
				
				                                                    if ($presen[$i][$j]['can'] > 0)
				                                                    {
				                                                        $art['cod'] = $presen[$i][$j]['cod'];
				                                                        $art['can'] = $can;
				
				                                                        if ($ind == 1)
				                                                        {
				                                                            $ind = 0;
				                                                        } 
				                                                        else
				                                                        {
				                                                            Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, &$date, &$cns, &$dronum, &$drolin, false, $usu, &$error);
				                                                        } 
				                                                        $res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, &$error);
				                                                        if (!$res)
				                                                        {
				                                                            pintarAlerta('EL ARTICULO ' . $presen[$i][$j]['cod'] . ' NO HA PODIDO SER CARGADO A ITDRO');
				                                                        } 
				                                                        registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, &$error);
				                                                    } 
				                                                } 
				                                            } 
				                                        } 
				                                        // cargamos los insumos de preparacion
				                                        for ($i = 0; $i < count($preparacion); $i++)
				                                        {
				                                            $tam = count($preparacion[$i]);
				                                            for ($j = 0; $j < $tam; $j++)
				                                            {
				                                                if (isset($escogidos[$i][$j]) and $escogidos[$i][$j] != '')
				                                                {
				                                                    $exp = explode('-', $preparacion[$i][$j]);
				                                                    $art['cod'] = $exp[0];
				                                                    $art['can'] = $exp[2];
				                                                    grabarDetalleSalidaMatrix($exp[3], $codigo, $consecutivo, $wusuario, $exp[0], '', '', 0, 1, $exp[2]);
				                                                    Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, &$date, &$cns, &$dronum, &$drolin, false, $usu, &$error);
				                                                    $res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, &$error);
				                                                    if (!$res)
				                                                    {
				                                                        pintarAlerta('EL ARTICULO ' . $art['can'] . ' NO HA PODIDO SER CARGADO A ITDRO');
				                                                        $art['ubi'] = 'M';
				                                                    } 
				                                                    registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, &$error);
				                                                } 
				                                            } 
				                                        } 
				                                        // grabamos ahora el saldo de producto
				                                        $art['cod'] = $cod;
				                                        $art['can'] = 1; 
				                                        $art['nom'] = $row1[0];
				                                        // cambiamos en la siguiente version
				                                        if (!$centro['apl'] and $serv['apl'])
				                                        {
				                                            $centro['apl'] = true;
				                                        } 
				
				                                        if (!$centro['apl'] and !$serv['apl'])
				                                        {
				                                            $val = registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, &$error);
				                                        } 
				                                        else
				                                        {
				                                            $val = registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, &$error);
				                                            $trans['num'] = $dronum;
				                                            if ( $drolin == 1 )
				                                            {
				                                                $trans['lin'] = 1;
				                                            } 
				                                            else
				                                            {
				                                                $trans['lin'] = '';
				                                            } 
				                                           $val =  registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, &$error);
				                                        }
				                                        
				                                        //Si todo esta bien, dispensa en KE
					                                    if( $packe['ke'] && $packe['act'] && $packe['con'] && $packe['sal'] && $val ){
					                                    	registrarArticuloKE( $art['cod'], $pac['his'], $pac['ing']);
								                        }
								                        pintarConfi($cod, $var, $row1[0], 'LOTE');
								                        pintarBoton();
								                    //Mensajes del KE
	                                    			}
	                                    			else{
	                                    				pintarAlerta('EL ARTICULO YA FUE DISPENSADO EN EL KE2');
	                                    				pintarBoton();
	                                    			}
                                    			}
	                                    		else{
	                                    			pintarAlerta('EL ARTICULO NO FUE CARGADO AL PACIENTE2');
	                                    			pintarBoton();
	                                    		}
                                    		}
                                    		else{
                                    			pintarAlerta('EL KARDEX ELECTRONICO NO SE HA CONFIRMADO2');
                                    			pintarBoton();
                                    		}
                                    	}
                                    	else{
                                    		pintarAlerta('NO TIENE KARDEX ELECTRONICO ACTUALIZADO2');
                                    		pintarBoton();
                                    	}
                                    } 
                                    else
                                    {
                                        pintarInsumos($inslis, $presen , $cod, $cco, $historia, $ingreso, $var, $servicio);
                                        $preparacion = consultarPreparacion(&$escogidos);
                                        pintarPreparacion($preparacion, $escogidos, $carro);
                                    } 
                                } 
                                else
                                {
                                    pintarAlert1($mensaje);
                                    pintarInsumos($inslis, $presen , $cod, $cco, $historia, $ingreso, $var, $servicio);
                                    $preparacion = consultarPreparacion(&$escogidos);
                                    pintarPreparacion($preparacion, $escogidos, $carro);
                                } 
                            } 
                        } 
                    } 
                } 
                break;
            default:

                if (!isset($var) or $var == '')
                {
                    pintarAlerta('DEBE SELECCIONAR LA PRESENTACION  QUE VA A CARGAR');
                    pintarBoton();
                } 
                else
                {
	                $var=urldecode($var);
                    $val = validarMatrix($cod, $cco, $var, 'off', &$mensaje);
                    if (!$val)
                    {
                        pintarAlerta($mensaje);
                    } 
                    else
                    {
                        $art['lot'] = '';
                        $exp = explode('-', $var);
                        $art['cod'] = $exp[0];
                        $art['neg'] = false;
                        $art['can'] = 1;
                        $res = ArticuloExiste (&$art, &$error);
                        if ($res)
                        {
                            $res = TarifaSaldo($art, $centro, $tipTrans, $aprov, &$error);
                            if ($res)
                            {//3
                            	//Busco la información del paciente con Kardex Electronico
                            	//esKE( $pac['his'], $pac['ing'], $packe );
                            	$packe = $pac;
                            	condicionesKE( $packe, $art['cod'] );
                            	condicionesKE( $packe, $cod );
                            	if( !$packe['ke'] || ( $packe['ke'] && $packe['act'] ) )
                            	{
                            		if( !$packe['ke'] || ( $packe['ke'] && $packe['con'] ) )
                            		{
                            			if( !$packe['ke'] || ( $packe['ke'] && $packe['art'] ) )
                            			{
	                            			if( !$packe['ke'] || ( $packe['ke'] && $packe['sal'] ) )
	                            			{
	                            				$val = false;
				                                // grabo el encabezado del movimiento
				                                $dronum = '';
				                                Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, &$date, &$cns, &$dronum, &$drolin, true, $usu, &$error);
				                                grabarEncabezadoSalidaMatrix(&$codigo, &$consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $dronum);
				                                $numtra = $codigo . '-' . $consecutivo; //actualizamos el numero del movimeinto real
				                                grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $var, '', '', '', 1, 1);
				                                descontarArticuloMatrix($cod, $cco, '', $art['cod']);
				                                grabarEncabezadoEntradaMatrix(&$codigo, &$consecutivo, $centro['cod'], $historia . '-' . $ingreso, $wusuario);
				                                grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $var, '', '', '', 1, 1);
				                                $res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, &$error);
				                                if (!$res)
				                                {
				                                    pintarAlerta('EL ARTICULO NO HA PODIDO SER CARGADO A ITDRO');
				                                    $art['ubi'] = 'M';
				                                } 
				                                registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, &$error); 
				                                // cambiamos en la siguiente version
				                                if (!$centro['apl'] and $serv['apl'])
				                                {
				                                    $centro['apl'] = true;
				                                } 
				
				                                if (!$centro['apl'] and !$serv['apl'])
				                                {
				                                    $val = registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, &$error);
				                                } 
				                                else
				                                {
				                                    $val = registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, &$error);
				                                    $trans['num'] = $dronum;
				                                    if ($drolin == 1)
				                                    {
				                                        $trans['lin'] = 1;
				                                    } 
				                                    else
				                                    {
				                                        $trans['lin'] = '';
				                                    } 
				                                    $val = registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, &$error);
				                                } 
				                                pintarConfi($cod, $var, $row1[0], 'PRESENTACION');
				                                if( $packe['ke'] && $packe['act'] && $packe['con'] && $packe['sal'] ){
				                                	registrarArticuloKE( $cod, $pac['his'], $pac['ing']);
				                                }
				                                pintarBoton();
				
				                                ?>
													<script>
				                                        window . opener . producto . submit();
				                                        window . close();
				                                     </script >
				                                    <?php
	                            			}
	                            			else{
	                            				pintarAlerta('EL ARTICULO YA FUE DISPENSADO EN EL KE3');
	                            				pintarBoton();
	                            			}
                            			}
                            			else{
                            				pintarAlerta('EL ARTICULO NO HA SIDO CARGADO AL PACIENTE3');
	                            			pintarBoton();
                            			}
                            		}
                            		else{
                            			pintarAlerta('EL KARDEX ELECTRONICO NO SE HA CONFIRMADO3');
                            			pintarBoton();
                            		}
                            	}
                            	else{
                            		pintarAlerta('NO TIENE KARDEX ELECTRONICO ACTUALIZADO3');
                            		pintarBoton();
                            	}
                            }//fin 3 
                            else
                            {
                                pintarAlerta('EL ARTICULO A CARGAR NO TIENE TARIFA EN UNIX');
                                pintarBoton();
                            } 
                        } 
                        else
                        {
                            pintarAlerta('EL ARTICULO A CARGAR NO EXISTE EN UNIX');
                            pintarBoton();
                        } 
                    } 
                } 
        } // switch
    } 
} 

?>
</body>
</html>