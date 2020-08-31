<head>
  <title>SISTEMA DE REQUERIMIENTOS</title>
  
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#DDDDDD;font-size:11pt;font-family:Tahoma;}	
    	.texto2{color:#003366;background:#DDDDDD;font-size:9pt;font-family:Tahoma;}
    	.texto4{color:#003366;background:#C0C0C0;font-size:11pt;font-family:Tahoma;}	
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

   function enter1()
   {
   	document.informatica.clareq.options[document.informatica.clareq.selectedIndex].text='';
   	document.informatica.submit();
   }
    </script>
  
</head>

<body >

<?php
include_once("conex.php");

//----------------------------------------------------------funciones de persitencia------------------------------------------------

function consultarUsuario($cco, $req)
{
	global $conex;
	global $wbasedato;


	$q= " SELECT Requso "
	."      FROM ".$wbasedato."_000040  "
	."    WHERE Reqnum = '".$req."' "
	."      AND Reqcco = '".$cco."' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);

	//Si el centro de costos esta vacio, busca los datos para cualquier centro de costos
	//Si tiene un valor busco los datos del usuario para ese centro de costos
	$q= " SELECT Usucod, Usucco, Usuext, Usuema, Usucar, Ususup, Descripcion "
	."       FROM ".$wbasedato."_000039, usuarios "
	."    WHERE Usucod = '".$row['Requso']."' "
	."       AND Usuest = 'on' "
	."       AND Codigo = usucod "
	."       AND Activo = 'A' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);

	//si lo encuentra cargo el vector de usuario
	//si no lo encuentra la función devuelve falso
	if ($num>0)
	{
		$usuario['cod']=$row['Usucod'];
		$usuario['cco']=$row['Usucco'];
		$usuario['ext']=$row['Usuext'];
		$usuario['ema']=$row['Usuema'];
		$usuario['car']=$row['Usucar'];
		$usuario['nom']=$row['Descripcion'];
		$usuario['sup']=$row['Ususup'];

	}
	else
	{
		$usuario=false;
	}
	return $usuario;
}

function consultarClases($cco, $tipo, $clase)
{
	global $conex;
	global $wbasedato;

	if ($clase!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$clases[0]=$clase;
		$cadena="Rctcla != mid('".$clase."',1,instr('".$clase."','-')-1) AND";
		$inicio=1;
	}
	else
	{
		$centros[0]='';
		$cadena='';
		$inicio=0;
	}


	//consulto los conceptos
	$q =  " SELECT Rctcla, Rctesp, Clades "
	."        FROM ".$wbasedato."_000044, ".$wbasedato."_000043 "
	."      WHERE ".$cadena." "
	."            rctcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."        AND rcttip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
	."        AND rctest = 'on' "
	."        AND rctcla = clacod "
	."        AND claest = 'on' ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$clases[$inicio]=$row1['Rctcla'].'-'.$row1['Clades'].'-'.$row1['Rctesp'];
			$inicio++;
		}
	}
	else
	{
		$clases= false;
	}

	return $clases;
}

function consultarRequerimiento($req, $cco, &$tipreq, &$clareq, &$temreq, &$resreq, &$desreq, &$fecap, &$porcen, &$fecen, &$estreq, &$prireq, &$codigo, &$obsreq, &$fecreq, &$recreq, &$ccoreq, $acureq, &$horreq)
{
	global $conex;
	global $wbasedato;

	$q= " SELECT Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Reqfae, Reqcum, Hora_data, Reqfen "
	."       FROM ".$wbasedato."_000040 "
	."    WHERE Reqnum = '".$req."' "
	."       AND Reqcco = '".$cco."' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		$row = mysql_fetch_array($res);

		$q =  " SELECT distinct Usucco as Cconom  "
		."         FROM ".$wbasedato."_000041, ".$wbasedato."_000039 "
		."      WHERE Mtrcco = '".$cco."' "
		."         AND Mtrest='on' "
		."         AND mid(Usucco,1,instr(Usucco,'-')-1)=Mtrcco ";

		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		$ccoreq=$row['Reqcco'].'-'.$row1['Cconom'];
		$numreq=$row['Reqnum'];

		$fecen=$row['Reqfen'];
		if ($fecen=='0000-00-00')
		{
			$fecen='';
		}

		$q= " SELECT Mtrdes "
		."      FROM ".$wbasedato."_000041 "
		."    WHERE Mtrcco = '".$row['Reqcco']."' "
		."      AND Mtrcod = '".$row['Reqtip']."' "
		."      AND Mtrest = 'on' ";

		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		$tipreq=$row['Reqtip'].'-'.$row1['Mtrdes'];
		$fecreq=$row['Reqfec'];
		$horreq=$row['Hora_data'];
		$fecap=$row['Reqfec'];
		$acureq=$row['Reqcum'].'%';
		$porcen=$row['Reqcum'].'%';


		$q= " SELECT Descripcion  "
		."       FROM usuarios "
		."    WHERE Codigo = '".$row['Reqpurs']."' "
		."       AND ACTIVO='A' ";
		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		$resreq=$row['Reqpurs'].'-'.$row1['Descripcion'];

		$q= " SELECT Descripcion  "
		."       FROM usuarios "
		."    WHERE Codigo = '".$row['Requrc']."' "
		."       AND ACTIVO='A' ";
		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		$recreq=$row['Requrc'].'-'.$row1['Descripcion'];

		$desreq=$row['Reqdes'];

		$q =  " SELECT Descripcion "
		."        FROM det_selecciones "
		."      WHERE Medico='".$wbasedato."' "
		."        AND Codigo='16' "
		."        AND Activo = 'A' "
		."        AND Subcodigo = '".$row['Reqpri']."' ";

		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		$prireq=$row['Reqpri'].'-'.$row1['Descripcion'];

		//consulto estado
		$q =  " SELECT Estnom "
		."        FROM ".$wbasedato."_000049 "
		."      WHERE Estest = 'on' "
		."      and Estcod = '".$row['Reqest']."' ";

		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		$estreq=$row['Reqest'].'-'.$row1['Estnom'];

		$q =  " SELECT Clades "
		."        FROM ".$wbasedato."_000043 "
		."      WHERE Claest = 'on' "
		."      and Clacod = '".$row['Reqcla']."' ";
		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		if ($row['Reqcla']!='')
		{
			$clareq=$row['Reqcla'].'-'.$row1['Clades'];
		}
		else
		{
			$clareq='';
		}
	}
}

function consultarResponsables($usuario, $cco, $tipo)
{
	global $conex;
	global $wbasedato;

	$responsables= false;

	//Poner de primero el usuario si este esta activado como responsable
	$q =  " SELECT count(*) "
	."        FROM ".$wbasedato."_000042 "
	."      WHERE Percco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."        AND Pertip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
	."        AND Perest = 'on' "
	."        AND Perres= 'on' ";

	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);
	if ($row1[0]>0)
	{
		$responsables[0]=$usuario;
	}

	//consulto los responsable de esa clase de ese tipo
	$q =  " SELECT Perusu, Descripcion"
	."        FROM ".$wbasedato."_000042, usuarios "
	."      WHERE Percco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."        AND Pertip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
	."        AND Perest = 'on' "
	."        AND Perres= 'on' "
	."        AND Perusu<>  mid('".$usuario."',1,instr('".$usuario."','-')-1) "
	."        AND Perusu= Codigo ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$responsables[$i]=$row1['Perusu'].'-'.$row1['Descripcion'];
		}
	}

	return $responsables;
}


function consultarReceptores($usuario, $cco, $tipo)
{
	global $conex;
	global $wbasedato;

	$receptores= false;

	//Poner de primero el usuario si este esta activado como responsable
	$q =  " SELECT count(*) "
	."        FROM ".$wbasedato."_000042  "
	."      WHERE Percco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."        AND Pertip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
	."        AND Perest = 'on' "
	."        AND Perrec= 'on' ";

	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);
	if ($row1[0]>0)
	{
		$receptores[0]=$usuario;
	}

	//consulto los responsable de esa clase de ese tipo
	$q =  " SELECT Perusu, Descripcion"
	."        FROM ".$wbasedato."_000042, usuarios "
	."      WHERE Percco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."        AND Pertip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
	."        AND Perest = 'on' "
	."        AND Perrec= 'on' "
	."        AND Perusu<>  mid('".$usuario."',1,instr('".$usuario."','-')-1) "
	."        AND Perusu= Codigo ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$receptores[$i]=$row1['Perusu'].'-'.$row1['Descripcion'];
		}
	}

	return $receptores;
}

function consultarPrioridades($prioridad)
{
	global $conex;
	global $wbasedato;


	if ($prioridad!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$prioridades[0]=$prioridad;
		$cadena="Subcodigo != mid('".$prioridad."',1,instr('".$prioridad."','-')-1) AND";
		$inicio=1;
	}
	else
	{
		$prioridades[0]='';
		$cadena='';
		$inicio=0;
	}

	//consulto los conceptos
	$q =  " SELECT Subcodigo, Descripcion "
	."        FROM det_selecciones "
	."      WHERE ".$cadena." "
	."        Medico='".$wbasedato."' "
	."        AND Codigo='16' "
	."        AND Activo = 'A' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$prioridades[$inicio]=$row1['Subcodigo'].'-'.$row1['Descripcion'];
			$inicio++;
		}
	}
	else
	{
		$prioridades= false;
	}

	return $prioridades;
}

function consultarEstados($estado)
{
	global $conex;
	global $wbasedato;

	if ($estado!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$estados[0]=$estado;
		$cadena="Estcod != mid('".$estado."',1,instr('".$estado."','-')-1) AND";
		$inicio=1;
	}
	else
	{
		$estados[0]='';
		$cadena='';
		$inicio=0;
	}

	//consulto los conceptos
	$q =  " SELECT Estcod, Estnom "
	."        FROM ".$wbasedato."_000049 "
	."      WHERE ".$cadena
	."        Estest = 'on' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0; $i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$estados[$inicio]=$row1['Estcod'].'-'.$row1['Estnom'];
			$inicio++;
		}
	}
	else
	{
		$estados= false;
	}

	return $estados;
}

function consultarCerrado($estado)
{
	global $conex;
	global $wbasedato;

	//consulto los conceptos
	$q =  " SELECT Estfin"
	."        FROM ".$wbasedato."_000049 "
	."      WHERE Estcod=mid('".$estado."',1,instr('".$estado."','-')-1)"
	."        and Estest = 'on' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	$row1 = mysql_fetch_array($res1);
	return $row1[0];
}

function consultarTiempos($clase)
{
	global $conex;
	global $wbasedato;

	//primero consulto si es necesario printar los tiempos de desarrollo
	$q =  " SELECT Clatde "
	."        FROM ".$wbasedato."_000043 "
	."      WHERE clacod=mid('".$clase."',1,instr('".$clase."','-')-1) "
	."        AND Claest='on' ";

	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);

	if ($row1[0]=='on')
	{
		//consulto los conceptos
		$q =  " SELECT Subcodigo, Descripcion "
		."        FROM det_selecciones "
		."      WHERE Medico='".$wbasedato."' "
		."        AND Codigo='15' "
		."        AND Activo = 'A' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$tiempos[$i]=$row1['Subcodigo'].'-'.$row1['Descripcion'];
			}
		}
		else
		{
			$tiempos= '';
		}
	}
	else
	{
		$tiempos= '';
	}
	return $tiempos;
}

function consultarPorcentaje($clase)
{
	global $conex;
	global $wbasedato;

	//primero consulto si es necesario printar los tiempos de desarrollo
	$q =  " SELECT Claacu "
	."        FROM ".$wbasedato."_000043 "
	."      WHERE clacod=mid('".$clase."',1,instr('".$clase."','-')-1) "
	."        AND Claest='on' ";

	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);

	return $row1[0];
}

function consultarCco($usucod)
{
	global $conex;
	global $wbasedato;

	$q= " SELECT Usucco "
	."       FROM ".$wbasedato."_000039"
	."    WHERE Usucod = '".$usucod."' "
	."       AND Usuest = 'on' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	//si lo encuentra cargo el vector de usuario
	//si no lo encuentra la función devuelve falso
	if ($num>0)
	{
		$row = mysql_fetch_array($res);
		$usucco=$row['Usucco'];
	}
	else
	{
		$usucco=false;
	}
	return $usucco;
}

function consultarTipos($cco, $codigo, $tipo)
{
	global $conex;
	global $wbasedato;

	if ($tipo!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$tipos[0]=$tipo;
		$cadena="Mtrcod != mid('".$tipo."',1,instr('".$tipo."','-')-1) AND";

		$contador=1;
	}
	else
	{
		$cadena='';
		$contador=0;
	}


	$exp=explode('-',$cco);
	$cco=$exp[0];
	if ($cco!='')
	{


		$q =  " SELECT Pertip, Mtrdes, Descripcion, Mtrcod "
		."         FROM ".$wbasedato."_000042, ".$wbasedato."_000041, usuarios "
		."      WHERE ".$cadena." "
		."         Percco= '".$cco."' "
		."         AND Perusu='".$codigo."' "
		."         AND Perest='on' "
		."         AND Pervis='on' "
		."         AND Perrec='on' "
		."         AND Mtrcod=Pertip "
		."         AND Mtrcco=percco "
		."         AND Mtrest='on' "
		."         AND Codigo=Perusu "
		."         AND Activo='A' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$tipos[$contador]=$row1[0].'-'.$row1[1].'-('.$row1[0].')-'.$row1[3];
				$contador++;
			}
		}


		$q =  " SELECT Pertip, Mtrdes "
		."         FROM ".$wbasedato."_000042, ".$wbasedato."_000041 "
		."      WHERE ".$cadena." "
		."         Percco= '".$cco."' "
		."         AND Perusu='".$codigo."' "
		."         AND Perest='on' "
		."         AND Pervis<>'on' "
		."         AND Perrec='on' "
		."         AND Mtrcod=Pertip "
		."         AND Mtrcco=percco "
		."         AND Mtrest='on' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$tipos[$contador]=$row1[0].'-'.$row1[1];
				$contador++;
			}
		}


		$q =  " SELECT Pertip, Mtrdes"
		."         FROM ".$wbasedato."_000042, ".$wbasedato."_000041 "
		."      WHERE ".$cadena." "
		."         Percco= '".$cco."' "
		."         AND Perusu='".$codigo."' "
		."         AND Perest='on' "
		."         AND Perrec<>'on' "
		."         AND Perres='on' "
		."         AND Mtrcod=Pertip "
		."         AND Mtrcco=percco "
		."         AND Mtrest='on' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$tipos[$contador]=$row1[0].'-'.$row1[1];
				$contador++;
			}
		}

		$q =  " SELECT Pertip, Pervis, Mtrdes, Descripcion, Mtrcod "
		."         FROM ".$wbasedato."_000042, ".$wbasedato."_000041, usuarios "
		."      WHERE ".$cadena." "
		."         Percco= '".$cco."' "
		."         AND Perusu<>'".$codigo."' "
		."         AND Perest='on' "
		."         AND Pervis='on' "
		."         AND Mtrcod=Pertip "
		."         AND Mtrcco=percco "
		."         AND Mtrest='on' "
		."         AND Codigo=Perusu "
		."         AND Activo='A' ";
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$tipos[$contador]=$row1[0].'-'.$row1[2].'-('.$row1[3].')-'.$row1[4];;
				$contador++;
			}
		}

		$q =  " SELECT Mtrcod, Mtrdes "
		."         FROM ".$wbasedato."_000041 "
		."      WHERE  ".$cadena." "
		."         Mtrcco= '".$cco."' "
		."         AND Mtrcod NOT IN (SELECT Pertip from ".$wbasedato."_000042 where percco='".$cco."' and Pervis='on' ) "
		."         AND Mtrcod NOT IN (SELECT Pertip from ".$wbasedato."_000042 where percco='".$cco."' and Perusu='".$codigo."' ) "
		."         AND Mtrest='on' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$tipos[$contador]=$row1[0].'-'.$row1[1];
				$contador++;
			}
		}
	}
	else
	{
		$tipos[0]='';
	}

	return $tipos;
}

function almacenarSeguimiento($codigo, $ccoreq, $acureq, $req, $seg, $env, $segnum, $segest)
{
	global $conex;
	global $wbasedato;

	$q= " SELECT count(*) "
	."       FROM ".$wbasedato."_000045 "
	."    WHERE Segreq = '".$req."' "
	."       AND Segcco = mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) ";

	$res = mysql_query($q,$conex);
	$row = mysql_fetch_array($res);
	$segnum=$row[0]+1;


	$q= " INSERT INTO ".$wbasedato."_000045 (          Medico,            Fecha_data,                   Hora_data,              Segfec,                                         Segcco ,     Segreq,        Segnum,        Segpcu,     Segtxt,     Segenv,        Segusu,        Segest,       Seguridad) "
	."                               VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".date('Y-m-d')."', mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1), '".$req."', '".$segnum."', '".$acureq."', '".$seg."', '".$env."', '".$codigo."', '".$segest."', 'C-".$codigo."') ";


	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GUARDAR EL SEGUIMIENTO ".mysql_error());

}

function adecuarFecha($estreq, $fecen)
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT Estfin "
	."         FROM ".$wbasedato."_000049 "
	."      WHERE  Estcod= mid('".$estreq."',1,instr('".$estreq."','-')-1) "
	."         AND Estest='on' ";

	$err = mysql_query($q,$conex) ;

	$row = mysql_fetch_array($err);
	if ($row[0]=='on')
	{
		if ($fecen=='')
		{
			$fecen=date('Y-m-d');
		}
	}
	else
	{
		$fecen='';
	}
	return $fecen;
}

function actualizarEspeciales($ccoreq, $tipreq, $clareq, $especiales, $reqnum, $codigo)
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT Rcttab "
	."         FROM ".$wbasedato."_000044 "
	."      WHERE  Rctcco= mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
	."         AND Rcttip=mid('".$tipreq."',1,instr('".$tipreq."','-')-1) "
	."         AND Rctcla=mid('".$clareq."',1,instr('".$clareq."','-')-1) "
	."         AND Rctest='on' ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA TABLA DE ALMACENAMIENTO DE CAMPOS ESPECIALES ".mysql_error());

	$row = mysql_fetch_array($err);
	$tabnum=$row[0];
	$insert = " UPDATE ".$wbasedato."_".$tabnum." SET ";

	$q =  " SELECT Cesnom, Rlcpos "
	."        FROM ".$wbasedato."_000048, ".$wbasedato."_000046 "
	."      WHERE Rlccla = mid('".$clareq."',1,instr('".$clareq."','-')-1) "
	."        AND Rlctip = mid('".$tipreq."',1,instr('".$tipreq."','-')-1) "
	."        AND Rlccco = mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
	."        AND Rlcest = 'on' "
	."        AND Cescod = Rlccam "
	."        AND Cesest = 'on' "
	."        Order by 2 ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);

	for ($i=0;$i<$num1;$i++)
	{
		$row1 = mysql_fetch_array($res1);
		$nom=strtolower(substr($row1[0],0, 3));
		$insert=$insert. " Esp".$nom." = '".$especiales[$i]['val']."',";
	}

	$insert=$insert. " Seguridad = 'C".$codigo."' WHERE Espreq='".$reqnum."' ";

	$err = mysql_query($insert,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GUARDAR LOS CAMPOS ESPECIALES DEL REQUERIMIENTO ".mysql_error());
}


function actualizarRequerimiento($req, $ccoreq, $tipreq, $clareq, $temreq, $recreq, $resreq, $prireq, $acureq, $estreq, $fecen, $obsreq, $fecap)
{
	global $conex;
	global $wbasedato;

	$q= " UPDATE ".$wbasedato."_000040 "
	. "    SET Reqtip= mid('".$tipreq."',1,instr('".$tipreq."','-')-1), "
	. "        Reqcla= mid('".$clareq."',1,instr('".$clareq."','-')-1), "
	. "        Requrc= mid('".$recreq."',1,instr('".$recreq."','-')-1), "
	. "        Reqpurs= mid('".$resreq."',1,instr('".$resreq."','-')-1), "
	. "        Reqfae= '".$fecap."', "
	. "        Reqest= mid('".$estreq."',1,instr('".$estreq."','-')-1), "
	. "        Reqcum= mid('".$acureq."',1,instr('".$acureq."','%')-1), "
	. "        Reqfen= '".$fecen."', "
	. "        Reqfen= '".$fecen."', "
	. "        Reqobe= '".$obsreq."', "
	. "        Reqtde= mid('".$temreq."',1,instr('".$temreq."','-')-1), "
	. "        Reqpri= mid('".$prireq."',1,instr('".$prireq."','-')-1) "
	."   WHERE Reqcco= mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
	."     AND Reqnum= '".$req."' ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ACTULIZAR EL REQUERIMIENTO ".mysql_error());
}

function consultarEspeciales($clase, $cco, $tipo, $usucco, $req)
{
	global $conex;
	global $wbasedato;


	if ($usucco==$cco)
	{

		$q =  " SELECT Rcttab, Rctesp "
		."         FROM ".$wbasedato."_000044 "
		."      WHERE  Rctcco= mid('".$cco."',1,instr('".$cco."','-')-1) "
		."         AND Rcttip=mid('".$tipo."',1,instr('".$tipo."','-')-1) "
		."         AND Rctcla=mid('".$clase."',1,instr('".$clase."','-')-1) "
		."         AND Rctest='on' ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA TABLA DE ALMACENAMIENTO DE CAMPOS ESPECIALES ".mysql_error());

		$row = mysql_fetch_array($err);
		if ($row[1]=='on')
		{
			$tabnum=$row[0];

			$q =  " SELECT * "
			."        FROM ".$wbasedato."_".$tabnum
			."      WHERE Espreq = '".$req."' ";
			$res3 = mysql_query($q,$conex);
			$row3 = mysql_fetch_array($res3);
			$contador=4;

			//consulto los conceptos
			$q =  " SELECT Rlccam, Rlcpos, Cesnom, Cessel, Cescom "
			."        FROM ".$wbasedato."_000048, ".$wbasedato."_000046 "
			."      WHERE Rlccla = mid('".$clase."',1,instr('".$clase."','-')-1) "
			."        AND Rlctip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
			."        AND Rlccco = mid('".$cco."',1,instr('".$cco."','-')-1) "
			."        AND Rlcest = 'on' "
			."        AND Cescod = Rlccam "
			."        AND Cesest = 'on' "
			."        Order by 2 ";
			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);

			if ($num1>0)
			{
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($res1);
					$especiales[$i]['nombre']=$row1['Rlccam'].'-'.$row1['Cesnom'];
					$especiales[$i]['sel']=$row1['Cessel'];
					if ($especiales[$i]['sel']=='on')
					{
						$especiales[$i][0]=$row3[$contador];

						$exp=explode('-', $row1['Cescom'] );

						$q =  " SELECT Subcodigo, Descripcion "
						."        FROM det_selecciones "
						."      WHERE Medico = '".$exp[0]."' "
						."        AND Codigo = '".$exp[1]."' "
						."        AND Subcodigo <> mid('".$row3[$contador]."',1,instr('".$row3[$contador]."','-')-1) "
						."        AND Activo = 'A' ";

						$res2 = mysql_query($q,$conex);
						$num2 = mysql_num_rows($res2);
						if ($num2>0)
						{
							for ($j=1;$j<=$num2;$j++)
							{
								$row2 = mysql_fetch_array($res2);
								$especiales[$i][$j]=$row2['Subcodigo'].'-'.$row2['Descripcion'];
							}
							$especiales[$i]['num']=$num2+1;
						}
						$contador++;
					}
				}
			}
			else
			{
				$especiales= '';
			}
		}
		else
		{
			$especiales= '';
		}
	}
	else
	{
		$especiales='';

	}


	return $especiales;
}

function consultarSeguimientos($req, $cco, $usucco)
{
	global $conex;
	global $wbasedato;

	if ($usucco==$cco)
	{
		$q= " SELECT Segfec, Segnum, Segpcu, Segtxt, Segusu, Segest, Descripcion, Hora_data  "
		."       FROM ".$wbasedato."_000045, usuarios "
		."    WHERE Segreq = '".$req."' "
		."       AND Segcco=(mid('".$cco."',1,instr('".$cco."','-')-1) "
		."       AND Segusu=Codigo "
		."       AND Activo='A' "
		."    ORDER BY 1 ";
	}
	else
	{
		$q= " SELECT Segfec, Segnum, Segpcu, Segtxt, Segusu, Segest, Descripcion, Hora_data  "
		."       FROM ".$wbasedato."_000045, usuarios "
		."    WHERE Segreq = '".$req."' "
		."       AND Segcco='".$cco."' "
		."       AND Segenv='on' "
		."       AND Segusu=Codigo "
		."       AND Activo='A' "
		."    ORDER BY 1 ";
	}

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$seguimientos[$i]['fec']=$row['Segfec'];
			$seguimientos[$i]['hor']=$row['Hora_data'];
			$seguimientos[$i]['num']=$row['Segnum'];
			$seguimientos[$i]['acu']=$row['Segpcu'];
			$seguimientos[$i]['txt']=$row['Segtxt'];
			$seguimientos[$i]['est']=$row['Segest'];
			$seguimientos[$i]['usu']=$row['Segusu'].'-'.$row['Descripcion'];
		}
	}
	else
	{
		$seguimientos='';
	}

	return $seguimientos;
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

function pintarTitulo()
{
	echo "<form name='informatica' action='seguimiento.php' method=post>";
	echo "<table ALIGN=CENTER width='50%'>";
	//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	echo "<tr><td class='titulo1'>SISTEMA DE REQUERIMIENTOS</td></tr>";
	echo "<tr><td class='titulo2'>Fecha: ".date('Y-m-d')."&nbsp Hora: ".(string)date("H:i:s")."</td></tr></table></br>";
}

function pintarUsuario($usuario)
{
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan=3 class='texto1' align='center'><b>Informacion del Usuario</b></td></tr>";

	echo "<tr><td class='texto2'>Codigo: ".$usuario['cod']."</td>";
	echo "<td class='texto2'>Nombre: ".$usuario['nom']."</td></tr>";

	echo "<tr><td class='texto2'>Centro de costos: ".$usuario['cco']."</td>";
	echo "<td class='texto2'>Cargo: ".$usuario['car']."</td></tr>";
	echo "<tr><td class='texto2'>Email: ".$usuario['ema']."</td>";
	echo "<td class='texto2'>Extension: ".$usuario['ext']."</td></tr>";
	echo "</table></br>";
}

function pintarAlert1($mensaje)
{
	echo '<script language="Javascript">';
	echo 'alert ("'.$mensaje.'")';
	echo '</script>';
}

function pintarAlert2($mensaje)
{
	echo "</table>";
	echo"<CENTER>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>".$mensaje."</td></tr>";
	echo "</table>";
}

function pintarRequerimiento($usucco, $req, $cco, $tipos, $clases, $tiempos, $responsables, &$desreq, $fecap, &$porcen, &$fecen, $estados, $prioridades, &$codigo, &$obsreq, &$fecreq, $recreq, &$ccoreq, $porcentaje, $wusuario, $receptores, $seguimientos, $especiales, $horreq, $cerrado)
{
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan=2 class='texto4' align='center'><b>Requerimiento ".$cco."-".$req." (".$fecreq."-".$horreq.")</b></td></tr>";

	echo "<tr><td class='texto3'  align='center'>Centro de costos: ".$ccoreq."</td>";

	$exp=explode('-',$recreq);
	if ($usucco==$ccoreq and $wusuario==$exp[0])
	{
		echo "<td class='texto3' align='center'>Tipo de requerimiento:";
		echo "<select name='tipreq' onchange='enter1()'>";
		for ($i=0;$i<count($tipos);$i++)
		{
			$exp=explode('-',$tipos[$i]);
			$contador=0;
			if (isset($exp[3]))
			{
				for ($j=0;$j<$i;$j++)
				{
					if ($tipos[$i]==$tipos[$j])
					{
						$contador++;
					}
				}
				$tipos[$i]=$exp[0].'-'.$exp[1];
			}
			if ($contador==0)
			{
				echo "<option>".$tipos[$i]."</option>";
			}
		}
		echo "</select></td></tr>";

		echo "<tr><td class='texto3' align='center' colspan='2'>Descripcion:</br><textarea cols='80' rows='4' readonly='readonly'> ".$desreq."</textarea></td></tr>";

		if (count($tiempos)>1)
		{

			echo "<tr><td class='texto2' align='center'>Clase de requerimiento:";
			echo "<select name='clareq' onchange='enter()'>";
			for ($i=0;$i<count($clases);$i++)
			{
				$exp=explode('-',$clases[$i]);
				echo "<option>".$exp[0]."-".$exp[1]."</option>";
			}
			echo "</select></td>";

			echo "<td class='texto2' align='center'>Tiempo de desarrollo:";
			echo "<select name='temreq' onchange='enter()'>";
			for ($i=0;$i<count($tiempos);$i++)
			{
				echo "<option>".$tiempos[$i]."</option>";
			}
			echo "</select></td></tr>";
		}
		else
		{
			echo "<tr><td class='texto2'colspan='2' align='center'>Clase de requerimiento:";
			echo "<select name='clareq' onchange='enter()'>";
			for ($i=0;$i<count($clases);$i++)
			{
				$exp=explode('-',$clases[$i]);
				echo "<option>".$exp[0]."-".$exp[1]."</option>";
			}
			echo "</select></td></tr>";
		}

		echo "<tr><td class='texto2' align='center'> Receptor:";
		echo "<select name='recreq' >";

		for ($i=0;$i<count($receptores);$i++)
		{
			echo "<option>".$receptores[$i]."</option>";
		}
		echo "</select></td>";

		echo "<td class='texto2' align='center'> Responsable:";
		echo "<select name='resreq' >";
		for ($i=0;$i<count($responsables);$i++)
		{
			echo "<option>".$responsables[$i]."</option>";
		}
		echo "</select></td></tr>";

		echo "<tr><td class='texto2' align='center'>Prioridad:";
		echo "<select name='prireq' >";
		for ($i=0;$i<count($prioridades);$i++)
		{
			echo "<option>".$prioridades[$i]."</option>";
		}
		echo "</select></td>";
		echo "<td class='texto2' align='center'>Fecha aproximada de atención: <input type='TEXT' name='fecap' value='".$fecap."' maxLength=10 size=10></td></tr>";


		if (is_array($especiales))
		{
			$par=2;
			for ($i=0;$i<count($especiales);$i++)
			{
				$exp=explode('-',$especiales[$i]['nombre']);
				if ($especiales[$i]['sel']=='on')
				{
					if (is_int($par/2))
					{
						echo "<tr>";
					}

					echo "<td align=center class='texto2'>".$exp[1].": <select name='especiales[".$i."][val]'>";
					for ($j=0;$j<$especiales[$i]['num'];$j++)
					{
						echo "<option>".$especiales[$i][$j]."</option>";
					}
					echo "</select></td>";

					if (!is_int($par/2))
					{
						echo "</tr>";
					}
					$par++;
				}
				else
				{
					if (is_int($par/2))
					{
						echo "<tr>";
					}

					echo "<td class='texto2' align='center'>".$exp[1].": <input type='TEXT' name='especiales[".$i."][val]' value='' size=10></td>";

					if (!is_int($par/2))
					{
						echo "</tr>";
					}
					$par++;
				}
			}
			if (!is_int($par/2))
			{
				echo "<td class='texto2' align='center'>&nbsp;</td></tr>";

			}
		}

		if (is_array($seguimientos))
		{
			for ($i=0;$i<count($seguimientos);$i++)
			{

				echo "<tr><td class='texto3'  align=center colspan='2' class=code>Seguimiento ".$seguimientos[$i]['num'].":  </br><textarea cols='80' rows='4' readonly='readonly'>";
				echo "Fecha: ".$seguimientos[$i]['fec']." \n";
				echo "Hora: ".$seguimientos[$i]['hor']." \n";
				echo "De: ".$seguimientos[$i]['usu']."  \n";
				echo "Mensaje: ".$seguimientos[$i]['txt']."  \n";
				echo "Porcentaje de cumplimientos: ".$seguimientos[$i]['acu'].'%'."  \n";
				echo "Estado de requerimiento: ".$seguimientos[$i]['est']."</textarea></td></tr>";
			}
		}

		if ($cerrado!='on')
		{
			echo "<tr><td class='texto2' align='center' colspan='2'>Nuevo Seguimiento:  </br><textarea name='seg' cols='80' rows='4'></textarea></td></tr>";
		}
		if($porcentaje=='on')
		{
			echo "<tr><td class='texto2' align='center'>Porcentaje de cumplimiento: <input type='TEXT' name='porcen' value='".$porcen."' size=10></td>";
			$colspan=1;
		}
		else
		{
			$colspan=2;
			echo "<tr>";
		}
		echo "<td class='texto2' align='center' colspan='".$colspan."'> Estado:";
		echo "<select name='estreq' >";
		for ($i=0;$i<count($estados);$i++)
		{
			echo "<option>".$estados[$i]."</option>";
		}
		echo "</select></td></tr>";
		echo "<tr><td class='texto3' align='center'>Fecha de entrega: <input type='TEXT' name='fecen' value='".$fecen."' maxLength=10 size=10></td>";
		echo "<td class='texto3' align='center'>observacion:  </br><textarea name='obsreq' cols='40' rows='4'></textarea></td></tr>";

	}
	else if ($usucco==$ccoreq)
	{
		echo "<td class='texto3' align='center'>Tipo de requerimiento:";
		echo "<select name='tipreq' onchange='enter1()'>";
		for ($i=0;$i<count($tipos);$i++)
		{
			$exp=explode('-',$tipos[$i]);
			$contador=0;
			if (isset($exp[3]))
			{
				for ($j=0;$j<$i;$j++)
				{
					if ($tipos[$i]==$tipos[$j])
					{
						$contador++;
					}
				}
				$tipos[$i]=$exp[0].'-'.$exp[1];
			}
			if ($contador==0)
			{
				echo "<option>".$tipos[$i]."</option>";
			}
		}
		echo "</select></td></tr>";

		echo "<tr><td class='texto3' align='center' colspan='2'>Descripcion:</br><textarea cols='80' rows='4' readonly='readonly'> ".$desreq."</textarea></td></tr>";

		if (count($tiempos)>1)
		{

			echo "<tr><td class='texto2' align='center'>Clase de requerimiento:";
			echo "<select name='clareq' onchange='enter()'>";
			for ($i=0;$i<count($clases);$i++)
			{
				$exp=explode('-',$clases[$i]);
				echo "<option>".$exp[0]."-".$exp[1]."</option>";
			}
			echo "</select></td>";

			echo "<td class='texto2' align='center'>Tiempo de desarrollo:";
			echo "<select name='temreq' onchange='enter()'>";
			for ($i=0;$i<count($tiempos);$i++)
			{
				echo "<option>".$tiempos[$i]."</option>";
			}
			echo "</select></td></tr>";
		}
		else
		{
			echo "<tr><td class='texto2'colspan='2' align='center'>Clase de requerimiento:";
			echo "<select name='clareq' onchange='enter()'>";
			for ($i=0;$i<count($clases);$i++)
			{
				$exp=explode('-',$clases[$i]);
				echo "<option>".$exp[0]."-".$exp[1]."</option>";
			}
			echo "</select></td></tr>";
		}

		echo "<tr><td class='texto2' align='center'> Receptor:";
		echo "<select name='recreq' >";

		for ($i=0;$i<count($receptores);$i++)
		{
			echo "<option>".$receptores[$i]."</option>";
		}
		echo "</select></td>";

		echo "<td class='texto2' align='center'> Responsable:";
		echo "<select name='resreq' >";
		for ($i=0;$i<count($responsables);$i++)
		{
			echo "<option>".$responsables[$i]."</option>";
		}
		echo "</select></td></tr>";

		echo "<tr><td class='texto2' align='center'>Prioridad:";
		echo "<select name='prireq' >";
		for ($i=0;$i<count($prioridades);$i++)
		{
			echo "<option>".$prioridades[$i]."</option>";
		}
		echo "</select></td>";
		echo "<td class='texto2' align='center'>Fecha aproximada de atención: <input type='TEXT' name='fecap' value='".$fecap."' maxLength=10 size=10></td></tr>";


		if (is_array($especiales))
		{
			$par=2;
			for ($i=0;$i<count($especiales);$i++)
			{
				$exp=explode('-',$especiales[$i]['nombre']);
				if ($especiales[$i]['sel']=='on')
				{
					if (is_int($par/2))
					{
						echo "<tr>";
					}

					echo "<td align=center class='texto2'>".$exp[1].": <select name='".$especiales[$i]['nombre']."'>";
					for ($j=0;$j<$especiales[$i]['num'];$j++)
					{
						echo "<option>".$especiales[$i][$j]."</option>";
					}
					echo "</select></td>";

					if (!is_int($par/2))
					{
						echo "</tr>";
					}
					$par++;
				}
				else
				{
					if (is_int($par/2))
					{
						echo "<tr>";
					}

					echo "<td class='texto2' align='center'>".$exp[1].": <input type='TEXT' name='".$especiales[$i]['nombre']."' value='' size=10></td>";

					if (!is_int($par/2))
					{
						echo "</tr>";
					}
					$par++;
				}
			}
			if (!is_int($par/2))
			{
				echo "<td class='texto2' align='center'>&nbsp;</td></tr>";

			}
		}

		if (is_array($seguimientos))
		{
			for ($i=0;$i<count($seguimientos);$i++)
			{

				echo "<tr><td class='texto3'  align=center colspan='2' class=code>Seguimiento ".$seguimientos[$i]['num'].":  </br><textarea cols='80' rows='4' readonly='readonly'>";
				echo "Fecha: ".$seguimientos[$i]['fec']." \n";
				echo "De: ".$seguimientos[$i]['usu']."  \n";
				echo "Mensaje: ".$seguimientos[$i]['txt']."  \n";
				echo "Porcentaje de cumplimientos: ".$seguimientos[$i]['acu'].'%'."  \n";
				echo "Estado de requerimiento: ".$seguimientos[$i]['est']."</textarea></td></tr>";
			}
		}

		if ($cerrado!='on')
		{
			echo "<tr><td class='texto2' align='center' colspan='2'>Nuevo Seguimiento:  </br><textarea name='seg' cols='80' rows='4'></textarea></td></tr>";
		}
		if($porcentaje=='on')
		{
			echo "<tr><td class='texto2' align='center'>Porcentaje de cumplimiento: <input type='TEXT' name='porcen' value='".$porcen."' size=10></td>";
			$colspan=1;
		}
		else
		{
			$colspan=2;
			echo "<tr>";
		}
		echo "<td class='texto2' align='center' colspan='".$colspan."'> Estado:";
		echo "<select name='estreq' >";
		for ($i=0;$i<count($estados);$i++)
		{
			echo "<option>".$estados[$i]."</option>";
		}
		echo "</select></td></tr>";
		echo "<tr><td class='texto3' align='center'>Fecha de entrega: <input type='TEXT' name='fecen' value='".$fecen."' maxLength=10 size=10></td>";
		echo "<td class='texto3' align='center'>observacion:  </br><textarea name='obsreq' cols='40' rows='4'></textarea></td></tr>";
	}
	else
	{
		echo "<td class='texto3' align='center'>Tipo de requerimiento: ".$tipos[0]." </td></tr>";
		echo "<tr><td class='texto3' align='center' colspan='2'>Descripcion: </br><textarea name='desreq' cols='80' rows='4'>".$desreq."</textarea></td></tr>";
		if (is_array($seguimientos))
		{
			for ($i=0;$i<count($seguimientos);$i++)
			{

				echo "<tr><td class='texto3'  align=center colspan='2' class=code>Seguimiento ".$seguimientos[$i]['num'].":  </br><textarea cols='80' rows='4' readonly='readonly'>";
				echo "Fecha: ".$seguimientos[$i]['fec']." \n";
				echo "De: ".$seguimientos[$i]['usu']."  \n";
				echo "Mensaje: ".$seguimientos[$i]['txt']."  \n";
				if($porcen=='on')
				{
					echo "Porcentaje de cumplimientos: ".$seguimientos[$i]['acu'].'%'."  \n";
				}
				echo "Estado de requerimiento: ".$seguimientos[$i]['est']."</textarea></td></tr>";
			}
		}
	}

	echo "</table>";

	echo "<input type='HIDDEN' name= 'desreq' value='".$desreq."'>";
	echo "<input type='HIDDEN' name= 'fecreq' value='".$fecreq."'>";
	echo "<input type='HIDDEN' name= 'horreq' value='".$horreq."'>";
}


function pintarBoton($cco, $req, $ccoreq)
{
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<TR><td class='texto3' colspan=5 >&nbsp;</td></tr>";
	echo "<td class='texto3' colspan=5 align='center'><input type='checkbox' name='enviar'>ENVIAR&nbsp/&nbsp;";
	echo "<input type='checkbox' name='grabar'>GRABAR&nbsp;";
	echo "<input type='HIDDEN' name= 'cco' value='".$cco."'>";
	echo "<input type='HIDDEN' name= 'req' value='".$req."'>";
	echo "<input type='HIDDEN' name= 'ccoreq' value='".$ccoreq."'>";
	echo "<INPUT TYPE='submit' NAME='ok' VALUE='OK'></td></tr>";
	echo "</table></br></br>";
	echo "</form>";

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
	$wbasedato='root';
	

	or die("No se ralizo Conexion");
	


	pintarVersion();
	pintarTitulo();

	//consulto los datos del usuario de la sesion
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user));

	$usucco=consultarCco($wusuario);
	$usuario=consultarUsuario($cco, $req);
	pintarUsuario($usuario);

	if ((!isset($enviar) and !isset($grabar)) or (isset($enviar) and !isset($seg)) or (isset($enviar) and $seg=='')) //primera vez que se ingresa al programa o tiene el valor de ser con botones de ingreso de datos
	{
		if ((isset($enviar) and !isset($seg)) or (isset($enviar) and $seg==''))
		{
			pintarAlert1('Para enviar un seguimiento debe ingresar alguna descripcion');
		}

		if (!isset($ccoreq))
		{
			consultarRequerimiento($req, $cco, &$tipreq, &$clareq, &$temreq, &$resreq, &$desreq, &$fecap, &$porcen, &$fecen, &$estreq, &$prireq, &$codigo, &$obsreq, &$fecreq, &$recreq, &$ccoreq, &$acureq, &$horreq);
		}
		$tipos=consultarTipos($ccoreq, $wusuario, $tipreq);
		$clases=consultarClases($ccoreq, $tipos[0], $clareq);
		$receptores=consultarReceptores($recreq, $ccoreq, $tipos[0], $clases[0]);
		$responsables=consultarResponsables($resreq, $ccoreq, $tipos[0], $clases[0]);
		$prioridades=consultarPrioridades($prireq);
		$estados=consultarEstados($estreq);
		$tiempos=consultarTiempos($clases[0]);
		$porcentaje=consultarPorcentaje($clases[0]);
		$seguimientos=consultarSeguimientos($req, $cco, $usucco);
		$especiales=consultarEspeciales($clases[0], $ccoreq, $tipos[0], $usucco, $req);
		$cerrado=consultarCerrado($estados[0]);
		pintarRequerimiento($usucco, $req, $cco, $tipos, $clases, $tiempos, $responsables, $desreq, $fecap, &$porcen, &$fecen, $estados, $prioridades, &$codigo, &$obsreq, &$fecreq, $recreq, &$ccoreq,  $porcentaje, $wusuario, $receptores, $seguimientos, $especiales, $horreq, $cerrado);
		if ($usucco==$ccoreq and $cerrado!='on')
		{
			pintarBoton($cco, $req, $ccoreq);
		}
	}
	else
	{
		$segnum=0;
		if (isset($grabar))
		{
			pintarAlert2('Se han grabado los cambios realizados');
		}

		if (isset($enviar))
		{
			$enviar='on';
		}
		else
		{
			$enviar='off';
		}

		if (!isset ($temreq))
		{
			$temreq='';
		}

		if (!isset ($porcen))
		{
			$porcen='';
		}
		else
		{
			$porcen=$porcen.'%';
		}
		almacenarSeguimiento($wusuario, $ccoreq, $porcen, $req, $seg, $enviar, $segnum, $estreq);
		$fecen=adecuarFecha($estreq, $fecen);
		actualizarRequerimiento($req, $ccoreq, $tipreq, $clareq, $temreq, $recreq, $resreq, $prireq, $porcen, $estreq, $fecen, $obsreq, $fecap);
		if (isset($especiales))
		{
			actualizarEspeciales($ccoreq, $tipreq, $clareq, $especiales, $req, $wusuario);
		}
		$tipos=consultarTipos($ccoreq, $wusuario, $tipreq);
		$clases=consultarClases($ccoreq, $tipos[0], $clareq);
		$receptores=consultarReceptores($recreq, $ccoreq, $tipos[0], $clases[0]);
		$responsables=consultarResponsables($resreq, $ccoreq, $tipos[0], $clases[0]);
		$prioridades=consultarPrioridades($prireq);
		$estados=consultarEstados($estreq);
		$tiempos=consultarTiempos($clases[0]);
		$porcentaje=consultarPorcentaje($clases[0]);
		$seguimientos=consultarSeguimientos($req, $cco, $usucco);
		$especiales=consultarEspeciales($clases[0], $ccoreq, $tipos[0], $usucco, $req);
		$cerrado=consultarCerrado($estados[0]);
		pintarRequerimiento($usucco, $req, $cco, $tipos, $clases, $tiempos, $responsables, $desreq, $fecap, &$porcen, &$fecen, $estados, $prioridades, &$codigo, &$obsreq, &$fecreq, $recreq, &$ccoreq, $porcentaje, $wusuario, $receptores, $seguimientos, $especiales, $horreq, $cerrado);
		if ($usucco==$ccoreq and $cerrado!='on')
		{
			pintarBoton($cco, $req, $ccoreq);
		}
	}
}
/*===========================================================================================================================================*/
?>


</body >
</html >
