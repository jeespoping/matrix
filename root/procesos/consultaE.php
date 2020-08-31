<head>
  <title>REQUERIMIENTOS</title>

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
   function enter()
   {
   	document.informatica.submit();
   }
    </script>

</head>

<body >

<?php
include_once("conex.php");

//----------------------------------------------------------funciones de persitencia------------------------------------------------

function consultarRequerimientos($codigo, $para)
{
	global $conex;
	global $wbasedato;

	if ($para=='recibidos')
	{
		$q= " SELECT Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Hora_data, Reqtpn, Reqsat, id AS id_req "
		."       FROM ".$wbasedato."_000040 "
		."    WHERE Reqest NOT IN ( SELECT Estcod FROM ".$wbasedato."_000049 WHERE Estfin='on') and Reqtip='01' "
		//."       OR Reqfec > '".date('Y')."-".date('m')."-01') "
		."    ORDER BY 10, 9, 4 desc, 12 desc";
	}
	else if ($para=='enviados')
	{
		$q= " SELECT Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Hora_data, Reqtpn, Reqsat, id AS id_req "
		."       FROM ".$wbasedato."_000040 "
		."    WHERE Requso = '".$codigo."' "
		//."       OR  Reqpurs = '".$codigo."' "
		."       AND Reqest NOT IN ( SELECT Estcod FROM ".$wbasedato."_000049 WHERE Estfin='on') "
		."    ORDER BY 10, 9, 4 desc, 12 desc";
	}

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$requerimientos[$i]['id_req'] = $row['id_req'];
			$requerimientos[$i]['cco']=$row['Reqcco'];
			$requerimientos[$i]['wcodigo_caso'] = $row['Reqtpn'];

			$q =  " SELECT distinct Usucco  "
			."         FROM ".$wbasedato."_000039  "
			."      WHERE mid(Usucco,1,instr(Usucco,'-')-1)='".$row['Reqcco']."' ";

			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$requerimientos[$i]['cconom']=$row1['Usucco'];
			$requerimientos[$i]['num']=$row['Reqnum'];

			$q= " SELECT Mtrdes "
			."      FROM ".$wbasedato."_000041 "
			."    WHERE Mtrcco = '".$row['Reqcco']."' "
			."      AND Mtrcod = '".$row['Reqtip']."' "
			."      AND Mtrest = 'on' ";

			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$requerimientos[$i]['tip']=$row1['Mtrdes'];
			$requerimientos[$i]['id']=$row['Reqtip'];
			$requerimientos[$i]['fec']=$row['Reqfec'];

			$q= " SELECT Descripcion  "
			."       FROM usuarios "
			."    WHERE Codigo = '".$row['Requso']."' "
			."       AND ACTIVO='A' ";

			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$requerimientos[$i]['uso']=$row1['Descripcion'];

			$q= " SELECT Usucco   "
			."       FROM ".$wbasedato."_000039"
			."    WHERE Usucod = '".$row['Requso']."' "
			."       AND Usuest  = 'on' ";

			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$requerimientos[$i]['usocco']=$row1['Usucco'];

			$q= " SELECT Descripcion  "
			."       FROM usuarios "
			."    WHERE Codigo = '".$row['Reqpurs']."' "
			."       AND ACTIVO='A' ";
			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$requerimientos[$i]['urs']=$row1['Descripcion'];

			$requerimientos[$i]['des']=substr($row['Reqdes'], 0,20).'...';

			$q =  " SELECT Descripcion "
			."        FROM det_selecciones "
			."      WHERE Medico='".$wbasedato."' "
			."        AND Codigo='16' "
			."        AND Activo = 'A' "
			."        AND Subcodigo = '".$row['Reqpri']."' ";
			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$requerimientos[$i]['pri']=$row1['Descripcion'];

			//consulto los conceptos
			$q =  " SELECT Estnom, Estcol "
			."        FROM ".$wbasedato."_000049 "
			."      WHERE Estest = 'on' "
			."      and Estcod = '".$row['Reqest']."' ";

			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$requerimientos[$i]['est']=$row1['Estnom'];
			$requerimientos[$i]['col']=$row1['Estcol'];

			$q =  " SELECT Clades "
			."        FROM ".$wbasedato."_000043 "
			."      WHERE Claest = 'on' "
			."      and Clacod = '".$row['Reqcla']."' ";
			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$requerimientos[$i]['cla']=$row1['Clades'];

			// Para buscar si el requerimiento actual tiene pendiente nuevos mensajes de respuesta para leer por parte del que está viendo la lista en determinado momento.
            $q = '';
            $msj_para_creador = '';
            if($row['Requso'] == $codigo)
            {
                $msj_para_creador = 'on';
                $q = "  SELECT  seg.id
                        FROM    ".$wbasedato."_000045 AS seg
                        WHERE   seg.Segtpn = '".$row['Reqtpn']."'
                                AND seg.Segmcr = 'on'";
            }
            elseif($row['Reqpurs'] == $codigo)
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
		$requerimientos='';
	}

	return $requerimientos;
}

//----------------------------------------------------------funciones de presentacion------------------------------------------------

function pintarVersion()
{
	$wautor="Carolina Castaño P.";
	$wversion="2007-04-17";
	echo "<table align='right'>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
	echo "</tr>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br></br>" ;
}

function pintarTitulo($wacutaliza)
{
	echo encabezado("<div class='titulopagina2'>SISTEMA DE REQUERIMIENTOS</div>", $wacutaliza, 'clinica');
	echo "<form name='informatica' action='consultaE.php' method=post>";
	echo "<table ALIGN=CENTER width='50%'>";
	//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	// echo "<tr><td class='titulo1'>SISTEMA DE REQUERIMIENTOS</td></tr>";
	echo "<tr><td class='titulo2'>Fecha: ".date('Y-m-d')."&nbsp Hora: ".(string)date("H:i:s")."</td></tr></table></br>";
}

function pintarAlert2($mensaje)
{
	echo "</br></table>";
	echo"<CENTER>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>".$mensaje."</td></tr>";
	echo "</table>";
}

function pintarRequerimientos($requerimientos, $para)
{
	$fila='enter()';
	$fila2='enter()';
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr>";
	echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;NUMERO&nbsp;</b><a onclick='".$fila2."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
	echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;FECHA</b><a onclick='".$fila2."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
	if($para=='recibidos')
	{
		echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;SOLICITANTE</b><a onclick='".$fila2."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
	}
	echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;UNIDAD</b><a onclick='".$fila2."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
	echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;TIPO</b><a onclick='".$fila2."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
	if($para=='recibidos')
	{
		echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;CLASE</b><a onclick='".$fila2."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
	}
	echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;DESCRIPCION</b><a onclick='".$fila2."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
	if($para=='recibidos')
	{
		echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;RESPONSABLE</b><a onclick='".$fila2."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
		echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;PRIORIDAD</b><a onclick='".$fila2."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
	}
	echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><b>&nbsp;ESTADO</b><a onclick='".$fila2."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></td>";
	echo "</tr>";


	for ($i=0;$i<count($requerimientos);$i++)
	{
		if (is_int($i/2))
		{
			$class='fila1';
		}
		else
		{
			$class='fila2';
		}
		echo "<tr>";
		echo "<td class='".$class."' align='center' ><a href='seguimiento.php?wcodigo_caso=".$requerimientos[$i]['wcodigo_caso']."&cco=".$requerimientos[$i]['cco']."&req=".$requerimientos[$i]['num']."&id_req=".$requerimientos[$i]['id_req']."&id=".$requerimientos[$i]['id']."&ids_segs_pte=".$requerimientos[$i]['mensajes_seguimiento']."&msj_para_creador=".$requerimientos[$i]['msj_para_creador']."' target='new' width='80%' >".$requerimientos[$i]['cco']."-".$requerimientos[$i]['num']."</a></td>";
		echo "<td class='".$class."' align='center' >".$requerimientos[$i]['fec']."</td>";
		if($para=='recibidos')
		{
			echo "<td class='".$class."' align='center' >".$requerimientos[$i]['uso']."</td>";

			echo "<td class='".$class."' align='center' >".$requerimientos[$i]['usocco']."</td>";
		}
		else
		{
			echo "<td class='".$class."' align='center' >".$requerimientos[$i]['cconom']."</td>";
		}
		echo "<td class='".$class."' align='center' >".$requerimientos[$i]['tip']."</td>";
		if($para=='recibidos')
		{
			echo "<td class='".$class."' align='center' >".$requerimientos[$i]['cla']."</td>";
		}
		echo "<td class='".$class."' >".$requerimientos[$i]['des']."</td>";
		if($para=='recibidos')
		{
			echo "<td class='".$class."' align='center'>".$requerimientos[$i]['urs']."</td>";
			echo "<td class='".$class."' align='center'>".$requerimientos[$i]['pri']."</td>";
		}
		echo "<td bgcolor='".$requerimientos[$i]['col']."' align='center'>".$requerimientos[$i]['est']."</td>";

		echo "</tr>";
	}
	echo "</table>";
}
/*=========================================================PROGRAMA==========================================================================*/
session_start();

if (!isset($user))
{
	if(!isset($_SESSION['user']))
	session_register("user");
}

if(!isset($_SESSION['user']))
echo "error";
else
{
	$wacutaliza = "2013-10-22";
	if (!isset ($para))
	{
		$para='recibidos';
	}
	$wbasedato='root';
	

	


	include_once("root/comun.php");

	// pintarVersion();
	pintarTitulo($wacutaliza);

	//consulto los datos del usuario de la sesion
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user));

	$requerimientos=consultarRequerimientos($wusuario, $para);
	if (is_array($requerimientos))
	{
		pintarRequerimientos($requerimientos, $para);
	}
	else
	{
		pintarAlert2('NO TIENE REQUERIMIENTOS PENDIENTES');
	}

	echo "<meta http-equiv='refresh' content='40;url=consultaE.php?para=".$para."'>";

}
/*===========================================================================================================================================*/
?>


</body >
</html >
