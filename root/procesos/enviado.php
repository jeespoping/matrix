<?php
include_once("conex.php");
/**
 PROGRAMA                   : enviado.php
 AUTOR                      : --
 FECHA CREACION             : --

 DESCRIPCION:

 ACTUALIZACIONES:

  * 25 de febrero de 2022 - Sebastian Alvarez Barona - Se realiza filtro por sede a la información que nos arroja el sistema, esta información se filtra de acuerdo a la sede 80 o sur.

 2015-10-13:
    Jessica Madrid:     * Se modifica la funcion consultarRequerimientos() para traer el centro de costos del usuario solicitante de root_000040 y si no existe lo consulta en root_000039.
 2013-10-17
    Edwar Jaramillo:    * Se envía en url de seguimiento el id del registro que identifica el requerimiento en la base de datos.

  2013-07-19
    Edwar Jaramillo:    * Modificación, actualización a las hoja de estilos que actualmente usa todo el sistema.

 */
 ?>
 <html>
<head><input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
  <title>REQUERIMIENTOS</title>

  <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>

  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#DDDDDD;font-size:11pt;font-family:Tahoma;}
    	.texto2{color:#003366;background:#DDDDDD;font-size:9pt;font-family:Tahoma;}
    	.texto4{color:#003366;background:#C0C0C0;font-size:9pt;font-family:Tahoma;}
    	.texto3{color:#003366;background:#C0C0C0;font-size:9pt;font-family:Tahoma;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
      	.texto5{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
      	.texto7{background:#FFFFFF;font-size:9pt;font-family:Arial;}
      	.texto8{background:#DDDDDD;font-size:9pt;font-family:Arial;}
   </style>

   <script type="text/javascript">
   function enter(val)
   {
   	document.informatica.orden.value='desc';
   	document.informatica.orden2.value=val;
   	document.informatica.submit();
   }

   function enter2(val)
   {
   	document.informatica.orden.value='asc';
   	document.informatica.orden2.value=val;
   	document.informatica.submit();
   }

    $(document).on('change','#selectsede',function(){
        window.location.href = "enviado.php?wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val();
    });

    </script>

</head>

<body >

<?php
// ----------------------------------------------------------funciones de persitencia------------------------------------------------
function consultarRequerimientos($codigo, $confec1, $confec2, $para, $orden, $orden2, $sCodigoSede = NULL, $TablaValidacionSede = NULL)
{
    global $conex;
    global $wbasedato;
    global $wmovhos;
    global $wcostosyp;
    global $wemp_pmla;

    
    $sFiltroSede='';
   
    if(isset($wemp_pmla) && !empty($wemp_pmla))
	{
		$estadosede=consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
   
		if($estadosede=='on')
		{
			$codigoSede = (is_null($sCodigoSede)) ? consultarsedeFiltro() : $sCodigoSede;
			$sFiltroSede = (isset($codigoSede) && ($codigoSede !='')) ? " AND Ccosed = '{$codigoSede}' " : "";
		}
	}

    if ($para == 'recibidos')
    {
        if($estadosede == 'off' || empty($TablaValidacionSede) || !isset($TablaValidacionSede))
        {
            $q = " SELECT r40.Reqcco, r40.Reqnum, r40.Reqtip, r40.Reqfec, r40.Requso, r40.Requrc, r40.Reqdes, r40.Reqpurs, r40.Reqpri, r40.Reqest, r40.Reqcla, Hora_data, Descripcion, r40.id AS id_req, Reqccs  "
            . "       FROM " . $wbasedato . "_000040 AS r40, usuarios "
            . "    WHERE (r40.Requrc = '" . $codigo . "' "
            . "       OR  r40.Reqpurs = '" . $codigo . "') "
            . "       AND r40.Reqest IN ( SELECT Estcod FROM " . $wbasedato . "_000049 WHERE Estfin='on') "
            . "       AND r40.Reqfec between '" . $confec1 . "' and '" . $confec2 . "' "
           // ."       OR r40.Reqfec > '".date('Y')."-".date('m')."-01') "
            . "       AND Codigo = r40.Reqpurs "
            // . "       AND ACTIVO='A' "
           . "    ORDER BY " . $orden2 . " " . $orden . ", 10, 9, 4 desc, 12 desc ";
        }else{
            $q = " SELECT r40.Reqcco, r40.Reqnum, r40.Reqtip, r40.Reqfec, r40.Requso, r40.Requrc, r40.Reqdes, r40.Reqpurs, r40.Reqpri, r40.Reqest, r40.Reqcla, r40.Hora_data, Descripcion, r40.id AS id_req, Reqccs, m11.Cconom  "
            . "       FROM " . $wbasedato . "_000040 AS r40, usuarios, ".$TablaValidacionSede." m11 "
            . "    WHERE (r40.Requrc = '" . $codigo . "' "
            . "       OR  r40.Reqpurs = '" . $codigo . "') "
            . "       AND r40.Reqest IN ( SELECT Estcod FROM " . $wbasedato . "_000049 WHERE Estfin='on') "
            . "       AND r40.Reqfec between '" . $confec1 . "' and '" . $confec2 . "' "
           // ."       OR r40.Reqfec > '".date('Y')."-".date('m')."-01') "
            . "       AND Codigo = r40.Reqpurs "
            ."        AND (mid(Reqccs,(instr(Reqccs,')') + 1),length(Reqccs)) = Ccocod {$sFiltroSede}) "
            // . "       AND ACTIVO='A' "
           . "    ORDER BY " . $orden2 . " " . $orden . ", 10, 9, 4 desc, 12 desc ";
        }

    }
    else if ($para == 'enviados')
    {

        if ($estadosede == 'off' || empty($TablaValidacionSede) || !isset($TablaValidacionSede))
        {
            
            $q = " SELECT r40.Reqcco, r40.Reqnum, r40.Reqtip, r40.Reqfec, r40.Requso, r40.Requrc, r40.Reqdes, r40.Reqpurs, r40.Reqpri, r40.Reqest, r40.Reqcla, Hora_data, Descripcion, r40.id AS id_req  "
            . "       FROM " . $wbasedato . "_000040 AS r40, usuarios " 
            . "    WHERE r40.Requso = '" . $codigo . "' "
            . "       AND r40.Reqest IN ( SELECT Estcod FROM " . $wbasedato . "_000049 WHERE Estfin='on') "
            . "       AND r40.Reqfec between '" . $confec1 . "' and '" . $confec2 . "' "
            . "       AND Codigo = r40.Reqpurs "
            // . "       AND ACTIVO='A' "
            . "    ORDER BY " . $orden2 . " " . $orden . ", 10, 9, 4 desc , 12 desc ";
        }else{
            
            $q = " SELECT r40.Reqcco, r40.Reqnum, r40.Reqtip, r40.Reqfec, r40.Requso, r40.Requrc, r40.Reqdes, r40.Reqpurs, r40.Reqpri, r40.Reqest, r40.Reqcla, r40.Hora_data, Descripcion, r40.id AS id_req, m11.Cconom  "
            . "       FROM " . $wbasedato . "_000040 AS r40, usuarios, ".$TablaValidacionSede." m11 " 
            . "    WHERE r40.Requso = '" . $codigo . "' "
            . "       AND r40.Reqest IN ( SELECT Estcod FROM " . $wbasedato . "_000049 WHERE Estfin='on') "
            . "       AND r40.Reqfec between '" . $confec1 . "' and '" . $confec2 . "' "
            . "       AND Codigo = r40.Reqpurs "
            . " AND (mid(Reqccs,(instr(Reqccs,')') + 1),length(Reqccs)) = Ccocod {$sFiltroSede}) "
            // . "       AND ACTIVO='A' "
            . "    ORDER BY " . $orden2 . " " . $orden . ", 10, 9, 4 desc , 12 desc ";
        }
    }

    $res = mysql_query($q, $conex);
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
        for ($i = 0;$i < $num;$i++)
        {
            $row = mysql_fetch_array($res);

            $requerimientos[$i]['cco'] = $row['Reqcco'];
            $requerimientos[$i]['id_req'] = $row['id_req'];

           $q = " SELECT distinct Usucco  "
			 . "         FROM " . $wbasedato . "_000039  "
			 . "      WHERE mid(Usucco,1,instr(Usucco,'-')-1)='" . $row['Reqcco'] . "' ";

			$res1 = mysql_query($q, $conex);
			$row1 = mysql_fetch_array($res1);

			$requerimientos[$i]['cconom'] = $row1['Usucco'];
			
			$requerimientos[$i]['num'] = $row['Reqnum'];

            $q = " SELECT Mtrdes "
             . "      FROM " . $wbasedato . "_000041 "
             . "    WHERE Mtrcco = '" . $row['Reqcco'] . "' "
             . "      AND Mtrcod = '" . $row['Reqtip'] . "' "
             . "      AND Mtrest = 'on' ";

            $res1 = mysql_query($q, $conex);
            $row1 = mysql_fetch_array($res1);

            $requerimientos[$i]['tip'] = $row1['Mtrdes'];
            $requerimientos[$i]['id'] = $row['Reqtip'];
            $requerimientos[$i]['fec'] = $row['Reqfec'];

            $q = " SELECT Descripcion  "
             . "       FROM usuarios "
             . "    WHERE Codigo = '" . $row['Requso'] . "' ";
             // . "       AND ACTIVO='A' ";

            $res1 = mysql_query($q, $conex);
            $row1 = mysql_fetch_array($res1);

            $requerimientos[$i]['uso'] = $row1['Descripcion'];
			
			if($row['Reqccs']=="")
			{
				 $q = " SELECT Usucco   "
				 . "      FROM " . $wbasedato . "_000039"
				 . "     WHERE Usucod = '" . $row['Requso'] . "' "
				 // . "       AND Usuest  = 'on' ";
				 . "  ORDER BY Usuest DESC 
						 LIMIT 1 ";

				$res1 = mysql_query($q, $conex);
				$row1 = mysql_fetch_array($res1);

				$requerimientos[$i]['usocco'] = $row1['Usucco'];
			}
			else
			{
				$ccosto=explode(")",$row['Reqccs']);
				$q = " SELECT Cconom 
						FROM ".$wmovhos."_000011 
					   WHERE Ccocod='".$ccosto[1]."'
						 AND Ccoest='on';";

				$res1 = mysql_query($q, $conex);
				$num1 = mysql_num_rows($res1);
				
				if($num1>0)
				{
					$row1 = mysql_fetch_array($res1);

					$requerimientos[$i]['usocco'] = $row['Reqccs']."-".strtoupper($row1['Cconom']);
				}
				else
				{
					$q2 = " SELECT Cconom 
							FROM ".$wcostosyp."_000005 
						   WHERE Ccocod='".$ccosto[1]."'
							 AND Ccoest='on';";

					$res2 = mysql_query($q2, $conex);
					$row2 = mysql_fetch_array($res2);

					$requerimientos[$i]['usocco'] = $row['Reqccs']."-".strtoupper($row2['Cconom']);
				}
			}
			
            $requerimientos[$i]['urs'] = $row['Descripcion'];

            $requerimientos[$i]['des'] = substr($row['Reqdes'], 0, 20) . '...';

            $q = " SELECT Descripcion "
             . "        FROM det_selecciones "
             . "      WHERE Medico='" . $wbasedato . "' "
             . "        AND Codigo='16' "
             . "        AND Activo = 'A' "
             . "        AND Subcodigo = '" . $row['Reqpri'] . "' ";
            $res1 = mysql_query($q, $conex);
            $row1 = mysql_fetch_array($res1);

            $requerimientos[$i]['pri'] = $row1['Descripcion'];
            // consulto los conceptos
            $q = " SELECT Estnom, Estcol "
             . "        FROM " . $wbasedato . "_000049 "
             . "      WHERE Estest = 'on' "
             . "      and Estcod = '" . $row['Reqest'] . "' ";

            $res1 = mysql_query($q, $conex);
            $row1 = mysql_fetch_array($res1);

            $requerimientos[$i]['est'] = $row1['Estnom'];
            $requerimientos[$i]['col'] = $row1['Estcol'];

            $q = " SELECT Clades "
             . "        FROM " . $wbasedato . "_000043 "
             . "      WHERE Claest = 'on' "
             . "      and Clacod = '" . $row['Reqcla'] . "' ";
            $res1 = mysql_query($q, $conex);
            $row1 = mysql_fetch_array($res1);

            $requerimientos[$i]['cla'] = $row1['Clades'];
        }
    }
    else
    {
        $requerimientos = '';
    }

    return $requerimientos;
}
// ----------------------------------------------------------funciones de presentacion------------------------------------------------
function pintarVersion()
{
    $wautor = "Carolina Castaño P.";
    $wversion = "2007-04-17";
    echo "<table align='right'>" ;
    echo "<tr>" ;
    echo "<td><font color=\"#D02090\" size='2'>Autor: " . $wautor . "</font></td>";
    echo "</tr>" ;
    echo "<tr>" ;
    echo "<td><font color=\"#D02090\" size='2'>Version: " . $wversion . "</font></td>" ;
    echo "</tr>" ;
    echo "</table></br></br></br>" ;
}


function pintarTitulo($wacutaliza, $TablaValidacionSede = '')
{
    global $wemp_pmla;
    global $selectsede;

    $estadosede=consultarAliasPorAplicacion($conexion, $wemp_pmla, "filtrarSede");
    $sFiltroSede="";
    $codigoSede = '';
    if($estadosede=='on')
    {	  
        $codigoSede = (isset($selectsede)) ? $selectsede : consultarsedeFiltro();
        $sFiltroSede = (isset($codigoSede) && ($codigoSede != '')) ? " AND Ccosed = '{$codigoSede}' " : "";
    }

    $sUrlCodigoSede = ($estadosede=='on') ? '&selectsede='.$codigoSede : '';

    $incluirFiltroSede = ($TablaValidacionSede == '') ? FALSE : TRUE;

    echo encabezado("<div class='titulopagina2'>SISTEMA DE REQUERIMIENTOS</div>", $wacutaliza, 'clinica', $incluirFiltroSede);
    echo "<form name='informatica' action='enviado.php?wemp_pmla=".$wemp_pmla."&selectsede=".$selectsede."' method=post>";
    echo "<table ALIGN=CENTER width='50%'>";
    // echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
    //echo "<tr><td class='titulo1'>SISTEMA DE REQUERIMIENTOS</td></tr>";
    echo "<tr><td class='titulo2'>Fecha: " . date('Y-m-d') . "&nbsp Hora: " . (string)date("H:i:s") . "</td></tr></table></br>";

    echo "<table ALIGN=CENTER width='90%' >";
    // echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
    echo "<tr><td class='texto5' width='20%'><a href='informatica.php?wemp_pmla=".$wemp_pmla.$sUrlCodigoSede."'>INGRESO DE REQUERIMIENTO</a></td>";
    echo "<td class='texto5' width='20%'><a href='consulta.php?wemp_pmla=".$wemp_pmla.$sUrlCodigoSede."&para=recibidos'>REQUERIMIENTOS RECIBIDOS</a></td>";
    echo "<td class='texto5' width='20%'><a href='consulta.php?wemp_pmla=".$wemp_pmla.$sUrlCodigoSede."&para=enviados'>REQUERIMIENTOS ENVIADOS</a></td></a>";
    echo "<a href='enviado.php?wemp_pmla=".$wemp_pmla."'><td class='encabezadoTabla' width='20%'>REQUERIMIENTOS ANT.</td></tr></a>";
    echo "<tr class='fila1'><td class='' >&nbsp;</td>";
    echo "<td class='' >&nbsp;</td>";
    echo "<td class='' >&nbsp;</td>";
    echo "<td class='' >&nbsp;</td>";
    echo "<td class='' >&nbsp;</td></tr></table>";
}

function pintarAlert2($mensaje)
{
    echo "</br></table>";
    echo"<CENTER>";
    echo "<table align='center' border=0 bordercolor=#000080 width=700>";
    echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>" . $mensaje . "</td></tr>";
    echo "</table>";
}

function pintarFormulario($confec1, $confec2, $para)
{
    // echo "</br><center><font color='#00008B'>INGRESE POR FAVOR EL RANGO DE FECHAS Y LOS REQUERIMIENTOS QUE DESEA CONSULTAR:</font></center></BR>";
    // Busqueda de comentario entre dos fechas
    // echo "<fieldset style='border:solid;border-color:#00008B; width=700' align=center></br>";
    echo "</BR><table align='center'>";
    echo "<tr class='encabezadoTabla'>";
    echo "<td align='center'><font size=3  face='arial' color=''>FECHA INICIAL:</font><input type='text' name='confec1' value='" . $confec1 . "' maxLength=10 size=10></td>";
    // echo "<td align=center >&nbsp;</td>";
    echo "<td align='center'><font size=3  face='arial' color=''>FECHA FINAL:</font><input type='text' name='confec2' value='" . $confec2 . "' maxLength=10 size=10></td>";
    echo "</tr>";
    // echo "<tr><td align=center colspan='3'>&nbsp;</td></tr>";
    if ($para == 'enviados')
    {
        $otro = 'recibidos';
    }
    else
    {
        $otro = 'enviados';
    }
    echo "<tr class='encabezadoTabla'><td align=center colspan='3'><font size=3  face='arial' color=''>BUSCAR REQUERIMIENTOS: </font><select name='para'><option>" . $para . "</option><option>" . $otro . "</option></select></td></tr>";
    echo "<tr><td align=center colspan='3'><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='BUSCAR' ></td></tr>";
    echo "</TABLE>";
    // echo "</fieldset>";
}

function pintarRequerimientos($requerimientos, $para, $orden, $orden2)
{
    global $wemp_pmla;
    echo "<table border=0 ALIGN=CENTER width=90%>";
    echo "<tr class='encabezadoTabla'>";
    echo "<td class='' align='center' height='50'><a onclick='enter(2)'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;NUMERO&nbsp;</b><a onclick='enter2(2)'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
    echo "<td class='' align='center' height='50'><a onclick='enter(4)'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;FECHA</b><a onclick='enter2(4)'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
    if ($para == 'recibidos')
    {
        echo "<td class='' align='center' height='50'><a onclick='enter(5)'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;SOLICITANTE</b><a onclick='enter2(5)'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
    }
    echo "<td class='' align='center' height='50'><a onclick='enter(5)'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;UNIDAD</b><a onclick='enter2(5)'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
    echo "<td class='' align='center' height='50'><a onclick='enter(3)'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;TIPO</b><a onclick='enter2(3)'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
    if ($para == 'recibidos')
    {
        echo "<td class='' align='center' height='50'><a onclick='enter(11)'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;CLASE</b><a onclick='enter2(11)'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
    }
    echo "<td class='' align='center' height='50'><a onclick='enter(7)'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;DESCRIPCION</b><a onclick='enter2(7)'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
    if ($para == 'recibidos')
    {
        echo "<td class='' align='center' height='50'><a onclick='enter(13)'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;RESPONSABLE</b><a onclick='enter2(13)'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
        echo "<td class='' align='center' height='50'><a onclick='enter(9)'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;PRIORIDAD</b><a onclick='enter2(9)'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
    }
    echo "<td class='' align='center' height='50'><a onclick='enter(10)'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;ESTADO</b><a onclick='enter2(10)'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
    echo "</tr>";

    for ($i = 0;$i < count($requerimientos);$i++)
    {
        if (is_int($i / 2))
        {
            $class = 'fila1';
        }
        else
        {
            $class = 'fila2';
        }
        echo "<tr>";
        echo "<td class='" . $class . "' align='center' ><a href='seguimiento.php?wemp_pmla=".$wemp_pmla."&cco=" . $requerimientos[$i]['cco'] . "&id_req=" . $requerimientos[$i]['id_req'] . "&req=" . $requerimientos[$i]['num'] . "&id=" . $requerimientos[$i]['id'] . "' target='new' width='80%' >" . $requerimientos[$i]['cco'] . "-" . $requerimientos[$i]['num'] . "</a></td>";
        echo "<td class='" . $class . "' align='center' >" . $requerimientos[$i]['fec'] . "</td>";
        if ($para == 'recibidos')
        {
            echo "<td class='" . $class . "' align='center' >" . $requerimientos[$i]['uso'] . "</td>";

            echo "<td class='" . $class . "' align='center' >" . $requerimientos[$i]['usocco'] . "</td>";
        }
        else
        {
            echo "<td class='" . $class . "' align='center' >" . $requerimientos[$i]['cconom'] . "</td>";
        }
        echo "<td class='" . $class . "' align='center' >" . $requerimientos[$i]['tip'] . "</td>";
        if ($para == 'recibidos')
        {
            echo "<td class='" . $class . "' align='center' >" . $requerimientos[$i]['cla'] . "</td>";
        }
        echo "<td class='" . $class . "' >" . $requerimientos[$i]['des'] . "</td>";
        if ($para == 'recibidos')
        {
            echo "<td class='" . $class . "' align='center'>" . $requerimientos[$i]['urs'] . "</td>";
            echo "<td class='" . $class . "' align='center'>" . $requerimientos[$i]['pri'] . "</td>";
        }
        echo "<td bgcolor='" . $requerimientos[$i]['col'] . "' align='center'>" . $requerimientos[$i]['est'] . "</td>";

        echo "</tr>";
    }
    echo "</table>";
    echo "<input type='hidden' name='orden' value='" . $orden . "'></td>";
    echo "<input type='hidden' name='orden2' value='" . $orden2 . "'></td>";
    echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='" . $wemp_pmla. "'></td>";
}

/**
 * [centroCostoUsuario Consultar el centro de costo del usuario que está en el programa, esto valída por ejemplo si el usuario es del centro de costo de auditoría,
 * si es así entonces cambia el título del programa]
 * @return [type] [description]
 */
function centroCostoUsuario($conex, $codigo_use)
{
    $exp = explode("-", $codigo_use);
    $usuario_codigo = $exp[1];
    $cco = "";
    $q = "  SELECT  Ccostos AS cco_user, Empresa
            FROM    usuarios
            WHERE   Codigo = '{$usuario_codigo}'";
                    // AND ACTIVO='A'";
    $result = mysql_query($q, $conex);
    if(mysql_num_rows($result) > 0)
    {
        $row = mysql_fetch_array($result);
        $cco = "({$row['Empresa']}){$row['cco_user']}";
    }
    return $cco;
}

function obtenerTablaValidacionSede($cco_user)
{
    global $conex;
    global $wemp_pmla;

    $bdMovhosCco = '';

	if(isset($wemp_pmla) && !empty($wemp_pmla))
	{
		$estadosede=consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
   
		if($estadosede=='on')
		{
			$QuerySede = "SELECT Mtrtvs FROM root_000041 WHERE Mtrcco = '".$cco_user."'";

            $resSede = mysql_query($QuerySede, $conex);
            if(mysql_num_rows($resSede) > 0)
            {
                $row = mysql_fetch_array($resSede);
                $tvs = $row['Mtrtvs'];
            }

            if ($tvs != '' && !is_null($tvs)) 
            {
                $ccostoSede=explode(";",$tvs);

                foreach ($ccostoSede as $empresa) {
                    $empresacco=explode(",",$empresa);
                    if ($empresacco[0] == 'wemp_pmla='.$wemp_pmla) {

                        $bdMovhosCco = $empresacco[1];
                        
                    }
                }
            }

		}
	}
   return $bdMovhosCco;

}
/**
* =========================================================PROGRAMA==========================================================================
*/
//session_start();
include_once("root/comun.php");
$wemp_pmla=$_REQUEST['wemp_pmla'];
$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wcostosyp = consultarAliasPorAplicacion($conex, $wemp_pmla, "COSTOS");

if (!isset($user))
{
    if (!isset($_SESSION["user"]))
        session_register("user");
}

if (!isset($_SESSION["user"]))
    echo "error";
else
{
    $wacutaliza = "25 de febrero de 2022";
    $wbasedato = 'root';
    
    $cco_user = centroCostoUsuario($conex, $user);
    

    $TablaValidacionSede = obtenerTablaValidacionSede($cco_user);


    //pintarVersion();
    pintarTitulo($wacutaliza, $TablaValidacionSede);
    // consulto los datos del usuario de la sesion
    $pos = strpos($user, "-");
    $wusuario = substr($user, $pos + 1, strlen($user));

    if (!isset($confec1))
    {
        $confec1 = date('Y') . '-' . date('m') . '-01';
        $confec2 = date('Y') . '-' . date('m') . '-' . date('d');
        $para = 'enviados';
    }

    if (!isset($orden))
    {
        $orden = 'asc';
    }

    if (!isset($orden2))
    {
        $orden2 = 10;
    }

    pintarFormulario($confec1, $confec2, $para);
    $requerimientos = consultarRequerimientos($wusuario, $confec1, $confec2, $para, $orden, $orden2, $selectsede, $TablaValidacionSede);
    if (is_array($requerimientos))
    {
        pintarRequerimientos($requerimientos, $para, $orden, $orden2);
    }
    else
    {
        pintarAlert2('NO TIENE REQUERIMIENTOS PENDIENTES');
    }

    echo "<input type='hidden' id='sede' name= 'sede' value='".$selectsede."'>";
    echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='" . $wemp_pmla. "'></td>";
}
/**
* ===========================================================================================================================================
*/
?>
<br>
<br>
<table align='center'>
    <tr><td align='center' colspan=9><input type='button' value='Cerrar Ventana' onclick='window.close();'></td></tr>
</table>
</body >
</html >