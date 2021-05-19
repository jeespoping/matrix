<?php
include_once("conex.php");
/**
 PROGRAMA                   : consulta.php
 AUTOR                      : --
 FECHA CREACION             : --

 DESCRIPCION:

 ACTUALIZACIONES:
 2015-10-13:
    Jessica Madrid:     * Se modifica la funcion consultarRequerimientos() para traer el centro de costos del usuario solicitante de root_000040 y si no existe lo consulta en root_000039.
 2014-06-25
    Edwar Jaramillo:    * Se comprueba si el usuario que esta logueado es de auditoría, si es así entonces se cambia el nombre del programa en el encabezado.

 2013-10-17
    Edwar Jaramillo:    * Se envía en url de seguimiento el id del registro que identifica el requerimiento en la base de datos.

2013-07-19
    Edwar Jaramillo:    * Se adecúa para que se muestren los requerimientos en estado cerrado o rechazado si no hay satisfacción con la entrega.
                        * Se crea la opción de recibir a satisfacción por parte del usuario que crea el requerimiento, si lo acepta desaparece el requerimiento para ambos de los contrario no.
                        * Si hay nuevos mensajes de seguimiento que el usuario debe leer entonces al lado de la columna estado aparece un ícono intermitente notificando.
                        * Modificación, actualización a las hoja de estilos que actualmente usa todo el sistema.
  2019-10-10
    Andres Alvarez: * Se crea class para aplicarle un color verde a los requirimiento revisados.
                    * Se filtran los requerimiento recibidos para que no aparezcan los requerimientos con estado rechazados.   
                    * Se crea estilo para que se aplique en los requerimientos revisados en el archivo include/root/matrix.css                   

 */
?>
<html>
<head>
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

   function parpadearDiv(ele) {
        //var el = document.getElementById(otro);
        //alert(el.style.visibility);
        if ( $("#"+ele).is(":visible")) {
            $("#"+ele).hide();
        }
        else {
            $("#"+ele).show();
        }
    }
    </script>

</head>

<body >

<?php
// ----------------------------------------------------------funciones de persitencia------------------------------------------------
function consultarRequerimientos($codigo, $para, $orden, $orden2, $wusuario)
{
    global $conex;
    global $wbasedato;
	global $wcostosyp;
	global $wmovhos;

    if ($para == 'recibidos')
    {
        $q = "  SELECT  Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Hora_data, Descripcion, Reqtpn, Reqsat, r40.id AS id_req, Reqccs 
                FROM    " . $wbasedato . "_000040 AS r40, usuarios
                WHERE   (Requrc = '" . $codigo . "'
                        OR  Reqpurs = '" . $codigo . "')
                        AND
                            (
                                Reqsat = 'off'
                                OR
                                Reqest NOT IN ( SELECT Estcod FROM " . $wbasedato . "_000049 WHERE Estfin='on')
                            )
                        AND Codigo = Reqpurs AND Reqest <> 6
                ORDER   BY ".$orden2." ".$orden.", 10, 9, 4 desc, 12 desc";

       
          
		// $q = "  SELECT  Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Hora_data, Descripcion, Reqtpn, Reqsat, r40.id AS id_req, Reqccs 
                // FROM    " . $wbasedato . "_000040 AS r40, usuarios
                // WHERE   (Requrc = '" . $codigo . "'
                        // OR  Reqpurs = '" . $codigo . "')
                        // AND
                            // (
                                // Reqsat = 'off'
                                // OR
                                // Reqest NOT IN ( SELECT Estcod FROM " . $wbasedato . "_000049 WHERE Estfin='on')
                            // )
                        // AND Codigo = Reqpurs
                        // AND ACTIVO='A'
                // ORDER   BY ".$orden2." ".$orden.", 10, 9, 4 desc, 12 desc";
        // ."       OR Reqfec > '".date('Y')."-".date('m')."-01') "
    }
    else if ($para == 'enviados')
    {
        /*
        Esta consulta permite traer los requerimientos no terminados y los terminados que tienen pendientes mensajes por leer.

        $q = "  SELECT  Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Hora_data, Descripcion, Reqtpn, Reqsat, r40.id AS id_req
                FROM    " . $wbasedato . "_000040 AS r40, usuarios
                WHERE   Requso = '" . $codigo . "'
                        AND Reqest NOT IN ( SELECT Estcod FROM " . $wbasedato . "_000049 WHERE Estfin='on')
                        AND Codigo = Reqpurs
                        AND ACTIVO='A'
                UNION ALL

                (
                    SELECT  Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, r40.Hora_data, Descripcion, r40.Reqtpn, Reqsat, r40.id AS id_req
                    FROM    root_000040 AS r40
                            INNER JOIN
                            root_000045 AS seg ON (seg.Segtpn = r40.Reqtpn AND seg.Segmcr = 'on'),
                            usuarios
                    WHERE   Requso = '".$codigo."'
                            AND Reqest IN ( SELECT Estcod FROM root_000049 WHERE Estfin='on')
                            AND Codigo = Reqpurs
                            AND ACTIVO='A'
                    GROUP BY Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, r40.Hora_data
                )
                ORDER BY ".$orden2." ".$orden.", 10, 9, 4 desc, 12 desc";*/

        $q = "  SELECT  Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Hora_data, Descripcion, Reqtpn, Reqsat, r40.id AS id_req, Reqccs 
                FROM    " . $wbasedato . "_000040 AS r40, usuarios
                WHERE   Requso = '" . $codigo . "'
                        AND (
                                Reqsat = 'off'
                                OR
                                Reqest NOT IN ( SELECT Estcod FROM " . $wbasedato . "_000049 WHERE Estfin='on')
                            )
                        AND Codigo = Reqpurs
               ORDER BY ".$orden2." ".$orden.", 10, 9, 4 desc, 12 desc";
		
		 // $q = "  SELECT  Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Hora_data, Descripcion, Reqtpn, Reqsat, r40.id AS id_req, Reqccs 
                // FROM    " . $wbasedato . "_000040 AS r40, usuarios
                // WHERE   Requso = '" . $codigo . "'
                        // AND (
                                // Reqsat = 'off'
                                // OR
                                // Reqest NOT IN ( SELECT Estcod FROM " . $wbasedato . "_000049 WHERE Estfin='on')
                            // )
                        // AND Codigo = Reqpurs
                        // AND ACTIVO='A'
                // ORDER BY ".$orden2." ".$orden.", 10, 9, 4 desc, 12 desc";
        // ."       OR  Reqpurs = '".$codigo."' "
    }
// echo "<pre>"; print_r($q); echo "</pre>";
    $res = mysql_query($q, $conex);
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
        for ($i = 0;$i < $num;$i++)
        {
            $row = mysql_fetch_array($res);

            $requerimientos[$i]['id_req'] = $row['id_req'];
            $requerimientos[$i]['cco'] = $row['Reqcco'];
            $requerimientos[$i]['wcodigo_caso'] = $row['Reqtpn'];
			
			if($row['Reqccs']=="")
			{
				$q = " SELECT distinct Usucco  "
				 . "         FROM " . $wbasedato . "_000039  "
				 . "      WHERE mid(Usucco,1,instr(Usucco,'-')-1)='" . $row['Reqcco'] . "' ";

				$res1 = mysql_query($q, $conex);
				$row1 = mysql_fetch_array($res1);

				$requerimientos[$i]['cconom'] = $row1['Usucco'];
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

					$requerimientos[$i]['cconom'] = $row['Reqccs']."-".strtoupper($row1['Cconom']);
				}
				else
				{
					$q2 = " SELECT Cconom 
							FROM ".$wcostosyp."_000005 
						   WHERE Ccocod='".$ccosto[1]."'
							 AND Ccoest='on';";

					$res2 = mysql_query($q2, $conex);
					$row2 = mysql_fetch_array($res2);

					$requerimientos[$i]['cconom'] = $row['Reqccs']."-".strtoupper($row2['Cconom']);
				}
			}	
            
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

            $requerimientos[$i]['urs'] = $row['Descripcion'];

			if($row['Reqccs']=="")
			{
				 $q = " SELECT Usucco   "
				 . "       FROM " . $wbasedato . "_000039"
				 . "    WHERE Usucod = '" . $row['Requso'] . "' "
				 . " ORDER BY Usuest DESC 
						LIMIT 1";

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
			
           

            $q= " SELECT Descripcion  "
			."       FROM usuarios "
			."    WHERE Codigo = '".$row['Requso']."' ";
			// ."       AND ACTIVO='A' ";

			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$requerimientos[$i]['uso']=$row1['Descripcion'];

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

            // Para buscar si el requerimiento actual tiene pendiente nuevos mensajes de respuesta para leer por parte del que está viendo la lista en determinado momento.
            $q = '';
            $msj_para_creador = '';
            if($row['Requso'] == $wusuario)
            {
                $msj_para_creador = 'on';
                $q = "  SELECT  seg.id
                        FROM    ".$wbasedato."_000045 AS seg
                        WHERE   seg.Segtpn = '".$row['Reqtpn']."'
                                AND seg.Segmcr = 'on'";
            }
            elseif($row['Reqpurs'] == $wusuario)
            {
                $q = "  SELECT  seg.id
                        FROM    ".$wbasedato."_000045 AS seg
                        WHERE   seg.Segtpn = '".$row['Reqtpn']."'
                                AND seg.Segmen = 'on'";
            }

            $ids_segs_msjs = '';
            if(!empty($q))
            {
                $res1 = mysql_query($q, $conex);
                $arr_ids = array();
                while ($rw_msj = mysql_fetch_array($res1)) {
                    $arr_ids[] = $rw_msj['id'];
                }
                $ids_segs_msjs = implode(",", $arr_ids);
            }
            $requerimientos[$i]['mensajes_seguimiento'] = $ids_segs_msjs;
            $requerimientos[$i]['msj_para_creador'] = $msj_para_creador;
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
    $wversion = "2019-10-17";
    echo "<table align='right'>" ;
    echo "<tr>" ;
    echo "<td><font color=\"#D02090\" size='2'>Autor: " . $wautor . "</font></td>";
    echo "</tr>" ;
    echo "<tr>" ;
    echo "<td><font color=\"#D02090\" size='2'>Version: " . $wversion . "</font></td>" ;
    echo "</tr>" ;
    echo "</table></br></br></br>" ;
}

function pintarTitulo($para,$wacutaliza, $titulo_requerimientos)
{
    global $wemp_pmla;
    echo encabezado("<div class='titulopagina2'>".$titulo_requerimientos."</div>", $wacutaliza, 'clinica');
    echo "<form name='informatica' action='consulta.php?wemp_pmla=".$wemp_pmla."' method=post>";
    echo "<table ALIGN=CENTER width='50%'>";
    // echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
    //echo "<tr><td class='titulo1'>SISTEMA DE REQUERIMIENTOS</td></tr>";
    echo "<tr><td class='titulo2'>Fecha: " . date('Y-m-d') . "&nbsp Hora: " . (string)date("H:i:s") . "</td></tr></table></br>";

    echo "<table ALIGN=CENTER width='96%' >";
    // echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
    echo "<tr><td class='texto5' width='20%'><a href='informatica.php?wemp_pmla=".$wemp_pmla."'>INGRESO DE REQUERIMIENTO</a></td>";
    if ($para == 'recibidos')
    {
        echo "<a href='consulta.php?wemp_pmla=".$wemp_pmla."&para=recibidos'><td class='encabezadoTabla' width='20%'>REQUERIMIENTOS RECIBIDOS</td></a>";
        echo "<td class='texto5' width='20%'><a href='consulta.php?wemp_pmla=".$wemp_pmla."&para=enviados'>REQUERIMIENTOS ENVIADOS</a></td>";
    }
    else
    {
        echo "<td class='texto5' width='20%'><a href='consulta.php?wemp_pmla=".$wemp_pmla."&para=recibidos'>REQUERIMIENTOS RECIBIDOS</a></td>";
        echo "<a href='consulta.php?wemp_pmla=".$wemp_pmla."&para=enviados'><td class='encabezadoTabla' width='20%'>REQUERIMIENTOS ENVIADOS</td></a>";
    }
    echo "<td class='texto5' width='20%'><a href='enviado.php?wemp_pmla=".$wemp_pmla."'>REQUERIMIENTOS ANT.</a></td></tr>";
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

function pintarRequerimientos($requerimientos, $para, $orden, $orden2)
{
    global $wemp_pmla;
    echo "<table border=0 ALIGN=CENTER width=96%>";
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
        $url_href = "seguimiento.php?wemp_pmla=".$wemp_pmla."&wcodigo_caso=".$requerimientos[$i]['wcodigo_caso']."&cco=" . $requerimientos[$i]['cco'] . "&req=" . $requerimientos[$i]['num'] . "&id_req=" . $requerimientos[$i]['id_req']."&id=" .$requerimientos[$i]['id']."&ids_segs_pte=".$requerimientos[$i]['mensajes_seguimiento']."&msj_para_creador=".$requerimientos[$i]['msj_para_creador'];
        echo "<tr>";
        echo "<td class='" . $class . "' align='center' ><a href='".$url_href."' target='new' width='80%' class='numero'>" . $requerimientos[$i]['cco'] . "-" . $requerimientos[$i]['num'] . "</a></td>";
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

        $msj_seguimiento_nuevo = '<div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>';
        if($requerimientos[$i]['mensajes_seguimiento'] != '')
        {
            $msj_seguimiento_nuevo = "
                    <script>setInterval('parpadearDiv(\"req_msj_".$requerimientos[$i]['id_req']."\")',900);</script>
                    <div id=\"req_msj_".$requerimientos[$i]['id_req']."\" style=\"\">
                        <a href='".$url_href."' target='new' width='80%' >
                            <img align=\"middle\" class=\"\" border=\"0\" width=\"18\" heigth=\"18\" src=\"../../images/medical/root/americassu.PNG\" title=\"\" >
                        </a>
                    </div>";
        }

        echo "  <td bgcolor='" . $requerimientos[$i]['col'] . "' align='center' >
                    <table style='width:100%'>
                        <tr>
                            <td nowrap='nowrap'>".$requerimientos[$i]['est']."</td>
                            <td>".$msj_seguimiento_nuevo."</td>
                        </tr>
                    </table>
                </td>";

        echo "</tr>";
    }
    echo "</table>";
    echo "<input type='hidden' name='para' value='" . $para. "'></td>";
    echo "<input type='hidden' name='orden' value='" . $orden . "'></td>";
    echo "<input type='hidden' name='orden2' value='" . $orden2 . "'></td>";
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

/**
* =========================================================PROGRAMA==========================================================================
*/
//session_start();

if (!isset($user))
{
    if (!isset($_SESSION["user"]))
        session_register("user");
}

if (!isset($_SESSION["user"]))
    echo "error";
else
{
    $wacutaliza = "2014-06-25";
    if (!isset ($para))
    {
        $para = 'recibidos';
    }
    $wbasedato = 'root';
    

    


    include_once("root/comun.php");
	$wemp_pmla=$_REQUEST['wemp_pmla'];
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcostosyp = consultarAliasPorAplicacion($conex, $wemp_pmla, "COSTOS");

    $cco_auditoria_corporativa_clinica = consultarAliasPorAplicacion($conex, $wemp_pmla, 'centro_costo_auditoria_corporativa');
    $auditoria_corporativa_titulos     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'auditoria_corporativa_titulos');
    $titulo_requerimientos             = "SISTEMA DE REQUERIMIENTOS";

    $cco_user = centroCostoUsuario($conex, $user);

    if(isset($cco_user) && $cco_user == $cco_auditoria_corporativa_clinica)
    {
      $titulo_requerimientos = $auditoria_corporativa_titulos;
    }

    //pintarVersion();
    pintarTitulo($para,$wacutaliza, $titulo_requerimientos);
    // consulto los datos del usuario de la sesion
    $pos = strpos($user, "-");
    $wusuario = substr($user, $pos + 1, strlen($user));

    if (!isset($orden))
    {
        $orden = 'asc';
    }

    if (!isset($orden2))
    {
        $orden2 = 10;
    }

    $requerimientos = consultarRequerimientos($wusuario, $para, $orden, $orden2, $wusuario);
    if (is_array($requerimientos))
    {
        pintarRequerimientos($requerimientos, $para, $orden, $orden2);
    }
    else
    {
        pintarAlert2('NO TIENE REQUERIMIENTOS PENDIENTES');
    }

    echo "<meta http-equiv='refresh' content='40;url=consulta.php?wemp_pmla=".$wemp_pmla."&para=".$para."&orden=".$orden."&orden2=".$orden2."'>";
}
/**
* ===========================================================================================================================================
*/
?>
<br>
<table align='center'>
    <tr><td align='center' colspan=9><input type='button' value='Cerrar Ventana' onclick='window.close();'></td></tr>
</table>
</body >
</html >