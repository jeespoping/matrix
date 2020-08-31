<head>
  <title>CARGOS DE CENTRAL DE MEZCLAS</title>
  
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo3{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
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
   	document.producto.accion.value=1;
   	document.producto.submit();
   }

   function enter1()
   {
   	document.producto.submit();
   }

   function enter2(i)
   {
   	document.producto.eliminar.value=i;
   	document.producto.submit();
   }

   function enter3()
   {
   	document.producto.realizar.value='modificar';
   	document.producto.submit();
   }

   function enter4()
   {
   	document.producto.realizar.value='desactivar';
   	document.producto.submit();
   }

   function enters(valor)
   {
   	document.producto.estado.value='inicio';
   	document.producto.submit();
   }

   function enter7()
   {
   	document.producto2.submit();
   }

   function enter8()
   {
   	document.producto4.submit();
   }

   function enter9()
   {
   	document.producto.grabar.value='1';
   	document.producto.submit();
   }

   function hacerFoco()
   {
   	if (document.producto.elements[5].value=='')
   	{
   		document.producto.elements[1].focus();
   	}
   	else
   	{
   		document.producto.elements[14].focus();
   	}
   }

    </script>
  
</head>

<body onload="hacerFoco()">

<?php
include_once("conex.php");
/*========================================================DOCUMENTACION PROGRAMA================================================================================*/
/*

1. AREA DE VERSIONAMIENTO

Nombre del programa:cargos.php
Aplicacion:cenpro
Fecha de creacion: 2007-05-30
Autor: Carolina Castano P
Ultima actualizacion:
2007-07-13   Carolina Castano P  Se permite realiza una secuencia de cargos para varias dosis de un producto
2007-07-06   Carolina Castano P  Publicacion a produccion

2. AREA DE DESCRIPCION:

Este script realiza las historias: Cargar un producto al paciente, devolver un producto cargado al paciente,
cargar un producto a otro centro de costos por averia,  descartar un producto de la central por averia,
devolver un producto cargado a un centro de costos por averia, devolver un producto descartado por averia.

Para ello el programa según la variable tipo enviada al cargar el programa, decide si se realizara un cargo a paciente
o un cargo por averia.  En el primer caso pide la historia clinica del paciente y una vez validada, el producto a ser
cargado.  En el segundo caso se debe escoger el centro de costos al que se cargara la averia y posteriormente el
producto a ser cargado.

3. AREA DE PRECONDICIONES

Al programa se le debe enviar como parametro:

tipo: que cuando es A indica que se realizara un cargo o devolucion por la averia a la central
B indica que se realizara un cargo por averia o devolucion a otro centro de costos
C indica que se realizara un cargo o devolucion a un paciente

4. AREA DE VALIDACIONES

5. AREA DE POSTCONDICIONES

6. AREA DE VARIABLES DE TRABAJO

7. AREA DE TABLAS

*/

/*========================================================FUNCIONES==========================================================================*/

//----------------------------------------------------------funciones de persitencia------------------------------------------------

function anularCargo($cco,$destino,$insumo,$dato,$lote)
{
	global $conex;
	global $wbasedato;

	$q= " SELECT Concod"
	."       FROM ".$wbasedato."_000008  "
	."    WHERE Conind = '-1' "
	."       AND Concar = 'on' "
	."       AND Conest = 'on' ";

	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);
	if ($lote=='on')
	{
		$q =  " SELECT Mencon, Mendoc "
		."        FROM ".$wbasedato."_000006, ".$wbasedato."_000007 "
		."      WHERE Mencco =mid('".$cco."',1,instr('".$cco."','-')-1) "
		."            and Menccd = '".$destino."' "
		."            and Mencon = '".$row1[0]."' "
		."            and Mdecon = Mencon "
		."            and Mdedoc = Mendoc "
		."  		  and Mdeart = '".$insumo."' "
		."            and Mdenlo = '".$dato."-".$insumo."' "
		."            and Mdeest = 'on' "
		."            and Menest = 'on' "
		."        ORDER BY ".$wbasedato."_000006.id desc";
	}
	else
	{
		$q =  " SELECT Mencon, Mendoc "
		."        FROM ".$wbasedato."_000006, ".$wbasedato."_000007 "
		."      WHERE Mencco = mid('".$cco."',1,instr('".$cco."','-')-1) "
		."            and Menccd = '".$destino."' "
		."            and Mencon = '".$row1[0]."' "
		."            and Mdecon = Mencon "
		."            and Mdedoc = Mendoc "
		."  		  and Mdeart = '".$insumo."' "
		."            and Mdepre = '".$dato."' "
		."            and Mdeest = 'on' "
		."            and Menest = 'on' "
		."        ORDER BY ".$wbasedato."_000006.id desc";
	}
	//echo $q;
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		$row1 = mysql_fetch_array($res1);
		$q= "   UPDATE ".$wbasedato."_000006 "
		."      SET Menest = 'off' "
		."    WHERE Mencon= '".$row1[0]."' "
		."      AND Mendoc= '".$row1[1]."' ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ANULAR EL CARGO ".mysql_error());


		$q= "   UPDATE ".$wbasedato."_000006 "
		."      SET Menest = 'off' "
		."    WHERE Mendan= '".$row1[0]."-".$row1[1]."' ";
		//echo $q;
		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ANULAR EL CARGO ".mysql_error());
	}
}


function actualizarAjuste($faltante, $presentacion2, $presentacion1, $cantidad, $historia, $cco, $usuario, $ingreso)
{
	global $conex;
	global $wbasedato;

	$exp=explode('-',$presentacion1);
	$presentacion1=$exp[1].'-'.$exp[0];
	if ($faltante==0)
	{
		$q= "   UPDATE ".$wbasedato."_000010 "
		."      SET Ajpcan = (Ajpcan - ".$cantidad.") "
		."    WHERE Ajphis= '".$historia."' "
		."      AND Ajping= '".$ingreso."' "
		."      AND Ajpart= mid('".$presentacion1."',1,instr('".$presentacion1."','-')-1) "
		."      AND Ajpcco= '".$cco."' "
		."      AND Ajpest = 'on' ";
	}
	else
	{
		$q =  " SELECT Appcnv "
		."        FROM  ".$wbasedato."_000009 "
		."      WHERE Apppre=mid('".$presentacion2."',1,instr('".$presentacion2."','-')-1) "
		."            and Appcco='".$cco."'"
		."            and Appest='on' ";

		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		$multi=ceil($faltante/$row1[0]);
		$cantidad=$multi*$row1[0]-$faltante;

		$q =  " SELECT * "
		."        FROM ".$wbasedato."_000010 "
		."      WHERE Ajphis= '".$historia."' "
		."            and Ajpest ='on' "
		."            and Ajping = '".$ingreso."' "
		."            and Ajpcco = '".$cco."' "
		."  		  and Ajpart = mid('".$presentacion1."',1,instr('".$presentacion1."','-')-1) ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			$esp=explode('-',$presentacion2);
			if (isset($esp[1]))
			{
				$presentacion2=$esp[0];
			}
			$q= "   UPDATE ".$wbasedato."_000010 "
			."      SET Ajpcan = ".$cantidad.", Ajpart = '".$presentacion2."' "
			."    WHERE Ajphis= '".$historia."' "
			."      AND Ajping= '".$ingreso."' "
			."      AND Ajpart= mid('".$presentacion1."',1,instr('".$presentacion1."','-')-1) "
			."      AND Ajpcco= '".$cco."' "
			."      AND Ajpest = 'on' ";
		}
		else
		{

			$q =  " SELECT Arttve "
			."        FROM  ".$wbasedato."_000009, ".$wbasedato."_000002 "
			."      WHERE Apppre=mid('".$presentacion2."',1,instr('".$presentacion2."','-')-1) "
			."            and Appcco='".$cco."' "
			."            and Appest='on' "
			."            and Appcod=Artcod ";


			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);
			$tiempo=mktime(0,0,0,date('m'),date('d'),date('Y'))+($row1[0]*24*60*60);
			$tiempo=date('Y-m-d', $tiempo);
			$q= " INSERT INTO ".$wbasedato."_000010 (   Medico       ,           Fecha_data,                  Hora_data,          Ajphis,          Ajping ,     Ajpcco,   Ajpfec  ,       Ajphor,     Ajpfve,        Ajphve ,                    Ajpart    ,       Ajpcan,  Ajpest, Seguridad) "
			."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$historia."', '".$ingreso."' , '".$cco."', '".date('Y-m-d')."' ,   '".(string)date("H:i:s")."',  '".$tiempo."' , '".(string)date("H:i:s")."',   mid('".$presentacion2."',1,instr('".$presentacion2."','-')-1) , '".$cantidad."', 'on', 'C-".$usuario."') ";

		}

	}

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL AJUSTE DE PRESENTACION ".mysql_error());
}


function devolverAjuste($faltante, $presentacion2, $presentacion1, $cantidad, $historia, $cco, $usuario, $ingreso)
{
	global $conex;
	global $wbasedato;

	if ($faltante==0)
	{
		$q= "   UPDATE ".$wbasedato."_000010 "
		."      SET Ajpcan = (Ajpcan + ".$cantidad.") "
		."    WHERE Ajphis= '".$historia."' "
		."      AND Ajping= '".$ingreso."' "
		."      AND Ajpart= mid('".$presentacion1."',1,instr('".$presentacion1."','-')-1) "
		."      AND Ajpcco= '".$cco."' "
		."      AND Ajpest = 'on' ";
	}
	else
	{
		$q =  " SELECT Appcnv "
		."        FROM  ".$wbasedato."_000009 "
		."      WHERE Apppre=mid('".$presentacion2."',1,instr('".$presentacion2."','-')-1) "
		."            and Appcco='".$cco."'"
		."            and Appest='on' ";

		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		if($faltante==$cantidad)
		{
			$q= "   UPDATE ".$wbasedato."_000010 "
			."      SET Ajpcan = 0 "
			."    WHERE Ajphis= '".$historia."' "
			."      AND Ajping= '".$ingreso."' "
			."      AND Ajpart= mid('".$presentacion2."',1,instr('".$presentacion2."','-')-1) "
			."      AND Ajpcco= '".$cco."' "
			."      AND Ajpest = 'on' ";
		}
		else
		{
			$cantidad=$cantidad-$faltante;

			$q= "   UPDATE ".$wbasedato."_000010 "
			."      SET Ajpcan = '".$cantidad."' ,  Ajpart = '".$presentacion1."'  "
			."    WHERE Ajphis= '".$historia."' "
			."      AND Ajping= '".$ingreso."' "
			."      AND Ajpart= mid('".$presentacion2."',1,instr('".$presentacion2."','-')-1) "
			."      AND Ajpcco= '".$cco."' "
			."      AND Ajpest = 'on' ";
		}
	}

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL AJUSTE DE PRESENTACION ".mysql_error());
}

function BuscarTraslado($parcon, $parcon2,$parcon3, $insfor, $forcon)
{
	global $conex;
	global $wbasedato;

	switch ($forcon)
	{
		case 'Numero de movimiento':
		$exp=explode('-',$parcon);
		if (isset($exp[1]))
		{
			$q= "SELECT Mdecon, Mdedoc, A.Fecha_data "
			."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000008 "
			."   WHERE Mdecon = '".$exp[0]."' "
			."     AND Mdedoc = '".$exp[1]."' "
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND (Concar = 'on' or Conave='on') "
			."     GROUP BY 1, 2, 3 ";
		}
		else
		{
			$q= "SELECT Mdecon, Mdedoc, A.Fecha_data "
			."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000008 "
			."   WHERE Mdedoc = '".$parcon."' "
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND (Concar = 'on' or Conave='on') "
			."     GROUP BY 1, 2, 3 ";
		}
		break;

		case 'Articulo':
		if (!isset ($parcon2) or $parcon2=='')
		{
			$parcon2=date('Y-m').'-01';
		}

		if (!isset ($parcon2) or $parcon2=='')
		{
			$parcon3=date('Y-m-d');
		}

		if ($insfor=='Codigo')
		{
			$q= "SELECT Mdecon, Mdedoc, A.Fecha_data "
			."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000008 "
			."   WHERE Mdeart = '".$parcon."' "
			."     AND A.Fecha_Data between  '".$parcon2."' and '".$parcon3."'"
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND (Concar = 'on' or Conave='on') "
			."     GROUP BY 1, 2, 3 ";
		}
		else if ($insfor=='Nombre comercial')
		{
			$q= "SELECT Mdecon, Mdedoc, A.Fecha_data"
			."     FROM   ".$wbasedato."_000007 A , ".$wbasedato."_000002, ".$wbasedato."_000008 "
			."   WHERE Artcom like '%".$parcon."%' "
			."   WHERE Artest = 'on' "
			."   WHERE Mdeart = Artcod "
			."     AND A.Fecha_Data between  '".$parcon2."' and '".$parcon3."'"
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND (Concar = 'on' or Conave='on') "
			."     GROUP BY 1, 2, 3 ";
		}
		else
		{
			$q= "SELECT Mdecon, Mdedoc, A.Fecha_data"
			."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000002, ".$wbasedato."_000008 "
			."   WHERE Artgen like '%".$parcon."%' "
			."   WHERE Artest = 'on' "
			."   WHERE Mdeart = Artcod "
			."     AND A.Fecha_Data between  '".$parcon2."' and '".$parcon3."'"
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND (Concar = 'on' or Conave='on') "
			."     GROUP BY 1, 2, 3 ";
		}
		break;

		case 'Historia':
		if (!isset ($parcon2) or $parcon2=='')
		{
			$parcon2=date('Y-m').'-01';
		}

		if (!isset ($parcon2) or $parcon2=='')
		{
			$parcon3=date('Y-m-d');
		}

		$q= "SELECT Mdecon, Mdedoc, Menfec "
		."     FROM   ".$wbasedato."_000007, ".$wbasedato."_000006, ".$wbasedato."_000008 "
		."   WHERE (mid(Mencco,1,instr(Mencco,'-')-1) = '".$parcon."' OR mid(Menccd,1,instr(Menccd,'-')-1) = '".$parcon."') "
		."     AND Menfec between  '".$parcon2."' and '".$parcon3."'"
		."     AND Menest = 'on' "
		."     AND Mencon = Mdecon "
		."     AND Mendoc = Mdedoc "
		."     AND Mdeest = 'on' "
		."     AND Mdecon = Concod "
		."     AND Concar = 'on'  "
		."     GROUP BY 1, 2, 3 ";

		break;

		default:
		if (!isset ($parcon2) or $parcon2=='')
		{
			$parcon2=date('Y-m').'-01';
		}

		if (!isset ($parcon2) or $parcon2=='')
		{
			$parcon3=date('Y-m-d');
		}

		if ($insfor=='Codigo')
		{
			$q= "SELECT Mdecon, Mdedoc, Menfec "
			."     FROM  ".$wbasedato."_000007, ".$wbasedato."_000006, ".$wbasedato."_000008 "
			."   WHERE ( Menccd = '".$parcon."' or Mencco = '".$parcon."') "
			."     AND Menfec between  '".$parcon2."' and '".$parcon3."'"
			."     AND Menest = 'on' "
			."     AND Mencon = Mdecon "
			."     AND Mendoc = Mdedoc "
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND Conave = 'on' "
			."     GROUP BY 1, 2, 3 ";
		}
		else if ($insfor=='Nombre')
		{
			$q= "SELECT Mdecon, Mdedoc, Menfec "
			."     FROM   ".$wbasedato."_000007, ".$wbasedato."_000006, movhos_000011, ".$wbasedato."_000008 "
			."   WHERE Cconom like '%".$parcon."%' "
			."     AND Ccoest = 'on' "
			."     AND (Menccd = Ccocod or Mencco = Ccocod) "
			."     AND Menfec between  '".$parcon2."' and '".$parcon3."'"
			."     AND Menest = 'on' "
			."     AND Mencon = Mdecon "
			."     AND Mendoc = Mdedoc "
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND Conave = 'on' "
			."     GROUP BY 1, 2, 3 ";
		}
		break;
	}

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0; $i<$num1; $i++)
		{
			$row1 = mysql_fetch_array($res1);
			$consultas[$i]=$row1[0].'-'.$row1[1].'-('.$row1[2].')';
		}
		return $consultas;
	}
	else
	{
		return '';
	}

}


function consultarTraslado($concepto, $consecutivo, $fecha, &$tipo, &$ccos, &$destinos, &$historia, &$estado, &$cco, &$ingreso, &$insumos, &$lotes, &$unidades, &$inslis)
{
	global $conex;
	global $wbasedato;

	$q= "SELECT Mdeart, Mdecan, Mdepre, Mdenlo, Mencco, Menccd, Menest, Artcom, Artgen, Artuni, Unides "
	."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000006, ".$wbasedato."_000002, farstore_000002 "
	."   WHERE Mdecon = '".$concepto."' "
	."     AND Mdedoc = '".$consecutivo."' "
	."     AND Mdecon = Mencon "
	."     AND Mdedoc = Mendoc "
	."     AND Mdeart = Artcod "
	."     AND Artest = 'on' "
	."     AND Unicod = Artuni "
	."     AND Uniest = 'on' ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		$row = mysql_fetch_array($res);

		if ($tipo=='C')
		{
			$exp=explode('-',$row['Mencco']);
			if (isset($exp[1]))
			{
				$historia=$exp[0];
				$ingreso=$exp[1];

				$q= "SELECT Cconom  "
				."     FROM   movhos_000011 "
				."   WHERE Ccocod = '".$row['Menccd']."' "
				."     AND Ccoest = 'on' ";
				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$destinos[0]=$row['Menccd'].'-'.$row1['Cconom'];
				$cco=$row['Menccd'].'-'.$row1['Cconom'];
			}
			else
			{
				$q= "SELECT Cconom  "
				."     FROM   movhos_000011 "
				."   WHERE Ccocod = '".$row['Mencco']."' "
				."     AND Ccoest = 'on' ";
				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$ccos[0]=$row['Mencco'].'-'.$row1['Cconom'];
				$cco=$row['Mencco'].'-'.$row1['Cconom'];

				$exp=explode('-',$row['Menccd']);
				$historia=$exp[0];
				$ingreso=$exp[1];
			}
		}
		else
		{

			$q= "SELECT Cconom  "
			."     FROM   movhos_000011 "
			."   WHERE Ccocod = '".$row['Mencco']."' "
			."     AND Ccoest = 'on' ";
			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$ccos[0]=$row['Mencco'].'-'.$row1['Cconom'];
			$cco=$row['Mencco'].'-'.$row1['Cconom'];
			$q= "SELECT Cconom  "
			."     FROM   movhos_000011 "
			."   WHERE Ccocod = '".$row['Menccd']."' "
			."     AND Ccoest = 'on' ";
			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$destinos[0]=$row['Menccd'].'-'.$row1['Cconom'];

			/*if ($ccos[0]!=$destinos[0])
			{
			$tipo='B';
			}*/

		}

		if ($row['Menest']=='on')
		{
			$estado='Creado';
		}
		else
		{
			$estado='Desactivado';
		}

		$exp=explode('-',$row['Mdenlo']);
		if (isset($exp[1]))
		{
			$insumos[0]['cod']=$exp[1];
			$insumos[0]['lot']='on';
			$lotes[0]=$exp[0];
		}
		else
		{
			$insumos[0]['cod']=$row['Mdeart'];
			$insumos[0]['lot']='off';
			$unidades[0]=$row['Mdepre'];
		}

		$q= "SELECT Artcom, Artgen, Artuni, Unides, Tipcdo "
		."     FROM   ".$wbasedato."_000002, farstore_000002, ".$wbasedato."_000001 "
		."   WHERE Artcod='".$insumos[0]['cod']."' "
		."     AND Unicod = Artuni "
		."     AND Tipcod = Arttip "
		."     AND Tipest = 'on' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		$row1 = mysql_fetch_array($res1);

		$insumos[0]['nom']=str_replace('-',' ',$row1[0]);
		$insumos[0]['gen']=str_replace('-',' ',$row1[1]);
		$insumos[0]['pre']=$row1[2].'-'.$row1[3];
		$insumos[0]['est']='on';

		if ($row1[4]=='on')
		{
			$insumos[0]['cdo']='on';
		}
		else
		{
			$insumos[0]['cdo']='off';
		}

		if ($insumos[0]['lot']=='on')
		{
			$q= " SELECT Pdeins, Pdecan, Artcom, Artgen, Artuni, Unides "
			."       FROM ".$wbasedato."_000003, ".$wbasedato."_000002, farstore_000002 "
			."    WHERE  Pdepro = '".$insumos[0]['cod']."' "
			."       AND Pdeest = 'on' "
			."       AND Pdeins= Artcod "
			."       AND Artuni= Unicod "
			."       AND Uniest='on' "
			."    Order by 1 ";

			$res = mysql_query($q,$conex);
			$num = mysql_num_rows($res);

			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($res);
					$inslis[$i]['cod']=$row[0];
					$inslis[$i]['nom']=str_replace('-',' ',$row[2]);
					$inslis[$i]['gen']=str_replace('-',' ',$row[3]);
					$inslis[$i]['pre']=$row[4].'-'.$row[5];
					$inslis[$i]['can']=$row[1];
					$inslis[$i]['pri']='';
				}
			}
		}
		else
		{
			$inslis[0]['cod']=$insumos[0]['cod'];
			$inslis[0]['nom']=$insumos[0]['nom'];
			$inslis[0]['gen']=$insumos[0]['gen'];
			$inslis[0]['pre']=$insumos[0]['pre'];
			$inslis[0]['can']=1;
			$inslis[0]['lot']=$insumos[0]['lot'];
			$inslis[0]['est']=$insumos[0]['est'];
		}
	}
}

function consultarUnidades($codigo, $cco, $unidad)
{
	global $conex;
	global $wbasedato;

	if ($unidad!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		//consulto los conceptos
		$q =  " SELECT Apppre, Artcom, Artgen, Appcnv, Appexi "
		."        FROM  ".$wbasedato."_000009, movhos_000026 "
		."      WHERE Apppre='".$unidad."' "
		."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
		."            and Appest='on' "
		."            and Apppre=Artcod ";


		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			$row1 = mysql_fetch_array($res1);
			$enteras=floor($row1['Appexi']/$row1['Appcnv']);
			$fracciones=$row1['Appexi']%$row1['Appcnv'];
			$unidades[0]=$row1['Apppre'].'-'.str_replace('-',' ',$row1['Artcom']).'-'.str_replace('-',' ',$row1['Artgen']).'-'.$enteras.'-'.$fracciones;
			$cadena="Apppre != '".$unidad."' AND";
			$inicio=1;
		}
		else
		{
			$cadena='';
			$inicio=0;
		}
	}
	else
	{
		$cadena='';
		$inicio=0;
	}

	//consulto los conceptos
	$q =  " SELECT Apppre, Artcom, Artgen, Appcnv, Appexi "
	."        FROM  ".$wbasedato."_000009, movhos_000026 "
	."      WHERE ".$cadena." "
	."             Appcod='".$codigo."' "
	."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."            and Appest='on' "
	."            and Apppre=Artcod ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);

			$enteras=floor($row1['Appexi']/$row1['Appcnv']);
			$fracciones=$row1['Appexi']%$row1['Appcnv'];
			$unidades[$inicio]=$row1['Apppre'].'-'.str_replace('-',' ',$row1['Artcom']).'-'.str_replace('-',' ',$row1['Artgen']).'-'.$enteras.'-'.$fracciones;
			$inicio++;
		}
		return $unidades;
	}

	if (!isset($unidades[0]))
	{
		return false;
	}
	else
	{
		return $unidades;
	}
}


function consultarConversor($codigo, $cco)
{
	global $conex;
	global $wbasedato;


	//consulto los conceptos
	$q =  " SELECT Appcnv "
	."        FROM  ".$wbasedato."_000009 "
	."      WHERE Apppre='".$codigo."' "
	."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."            and Appest='on' ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		$row1 = mysql_fetch_array($res1);
		return $row1[0];
	}
	else
	{
		return false;
	}

}

function consultarUnidades2($codigo, $cco, $unidad)
{
	global $conex;
	global $wbasedato;


	if ($unidad!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$unidades[0]=$unidad;
		$cadena="Apppre != mid('".$unidad."',1,instr('".$unidad."','-')-1) AND";
		$inicio=1;
	}
	else
	{
		$cadena='';
		$inicio=0;
	}

	//consulto los conceptos
	$q =  " SELECT Apppre, Artcom, Artgen "
	."        FROM  ".$wbasedato."_000009, movhos_000026 "
	."      WHERE ".$cadena." "
	."            Appcod='".$codigo."' "
	."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."            and Appest='on' "
	."            and Apppre=Artcod ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$unidades[$inicio]=$row1['Apppre'].'-'.str_replace('-',' ',$row1['Artcom']).'-'.str_replace('-',' ',$row1['Artgen']);
			$inicio++;
		}
		return $unidades;
	}

	if ($inicio==0)
	{
		return false;
	}
	else
	{
		return $unidades;
	}
}
function consultarDestinos($forbus, $parbus)
{
	global $conex;
	global $wbasedato;

	switch ($forbus)
	{
		case 'codigo':
		$q= " SELECT A.Ccocod, B.Cconom "
		."       FROM movhos_000011 A, costosyp_000005 B "
		."    WHERE A.Ccocod like '%".$parbus."%' "
		."       AND A.Ccoest = 'on' "
		."       AND A.Ccocod = B.Ccocod "
		."    Order by 1 ";
		$inicio=0;

		break;

		case 'nombre':
		$q= " SELECT A.Ccocod, B.Cconom "
		."       FROM movhos_000011 A, costosyp_000005 B "
		."    WHERE B.Cconom like '%".$parbus."%' "
		."       AND A.Ccoest = 'on' "
		."       AND A.Ccocod = B.Ccocod "
		."    Order by 1 ";
		$inicio=0;

		break;
		case 'todos':

		if ($parbus!='')
		{
			$centros[0]=$parbus;
			$cadena=" A.Ccocod <> mid('".$parbus."',1,instr('".$parbus."','-')-1) AND";
			$inicio=1;
		}
		else
		{
			$cadena='';
			$inicio=0;
		}
		$q= " SELECT A.Ccocod, B.Cconom "
		."       FROM movhos_000011 A, costosyp_000005 B "
		."    WHERE ".$cadena." "
		."       A.Ccoest = 'on' "
		."       AND A.Ccocod = B.Ccocod "
		."    Order by 1 ";
		break;
	}

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$centros[$inicio]=$row[0].'-'.$row[1];
			$inicio++;
		}
	}
	else if (!isset($centros[0]))
	{
		$centros=false;
	}
	return $centros;
}
function consultarCentros($cco)
{
	global $conex;
	global $wbasedato;

	if ($cco!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$ccos[0]=$cco;
		$cadena="A.Ccocod != mid('".$cco."',1,instr('".$cco."','-')-1) AND";
		$inicio=1;
	}
	else
	{
		$cadena='';
		$inicio=0;
	}

	//consulto los conceptos
	$q =  " SELECT A.Ccocod as codigo, B.Cconom as nombre"
	."        FROM movhos_000011 A, costosyp_000005 B "
	."      WHERE ".$cadena." "
	."        A.Ccoima = 'on' "
	."        AND A.Ccocod = B.Ccocod "
	."        AND A.Ccoest='on' ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$ccos[$inicio]=$row1['codigo'].'-'.$row1['nombre'];
			$inicio++;
		}

	}
	else if ($inicio==0)
	{
		$ccos='';
	}

	return $ccos;

}

function consultarConsecutivo($tipo, $signo)
{
	global $conex;
	global $wbasedato;

	switch ($tipo)
	{
		case 'A':
		$q= " SELECT Concod, Concon "
		."       FROM ".$wbasedato."_000008  "
		."    WHERE Conind = '".$signo."' "
		."       AND Conave = 'on' "
		."       AND Conest = 'on' ";
		break;

		case 'B':
		$q= " SELECT Concod, Concon "
		."       FROM ".$wbasedato."_000008  "
		."    WHERE Conind = '".$signo."' "
		."       AND Conave = 'on' "
		."       AND Conest = 'on' ";
		break;

		case 'C':
		$q= " SELECT Concod, Concon "
		."       FROM ".$wbasedato."_000008  "
		."    WHERE Conind = '".$signo."' "
		."       AND Concar = 'on' "
		."       AND Conest = 'on' ";
		break;
	}

	$res = mysql_query($q,$conex);
	$row = mysql_fetch_array($res);

	$resultado=$row[0].'-'.($row[1]+1);
	return $resultado;
}


function consultarInsumos($parbus, $forbus)
{
	global $conex;
	global $wbasedato;

	switch ($forbus)
	{
		case 'Rotulo':
		$q= " SELECT Tippro "
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001 "
		."    WHERE Artcod = '".$parbus."' "
		."       AND Artest = 'on' "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip ";
		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);

		//$exp=explode('-', $parbus);
		//if (isset($exp[1]))
		if (isset($row[0]) and $row[0]=='on')
		{
			$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo "
			."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, farstore_000002 "
			."    WHERE Artcod = '".$parbus."' "
			."       AND Artest = 'on' "
			."       AND Artuni= Unicod "
			."       AND Tipest = 'on' "
			."       AND Tipcod = Arttip "
			."       AND Uniest='on' "
			."    Order by 1 ";
		}
		else
		{
			$q= " SELECT C.Artcod, C.Artcom, C.Artgen, C.Artuni, B.Unides, D.Tippro, D.Tipcdo  "
			."       FROM movhos_000026 A, farstore_000002 B, ".$wbasedato."_000002 C, ".$wbasedato."_000001 D, ".$wbasedato."_000009 E"
			."    WHERE A. Artcod = '".$parbus."' "
			."       AND A. Artest = 'on' "
			."       AND A. Artcod = E.Apppre "
			."       AND E. Appest = 'on' "
			."       AND E. Appcod = C.Artcod "
			."       AND C. Artest = 'on' "
			."       AND C. Artuni= B.Unicod "
			."       AND B.Uniest='on' "
			."       AND D.Tipcod = C.Arttip "
			."       AND D.Tipest = 'on' "
			."    Order by 1 ";
		}
		break;

		case 'Codigo':
		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo "
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, farstore_000002 "
		."    WHERE Artcod like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Artuni= Unicod "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;

		case 'Nombre comercial':

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo "
		."       FROM ".$wbasedato."_000002,  ".$wbasedato."_000001, farstore_000002 "
		."    WHERE Artcom like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;
		case 'Nombre genérico':

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo "
		."       FROM ".$wbasedato."_000002,  ".$wbasedato."_000001, farstore_000002 "
		."    WHERE Artgen like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;
	}

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$productos[$i]['cod']=$row[0];
			$productos[$i]['nom']=str_replace('-',' ',$row[1]);
			$productos[$i]['gen']=str_replace('-',' ',$row[2]);
			$productos[$i]['pre']=$row[3].'-'.$row[4];
			$productos[$i]['est']='on';
			if ($row[5]=='on')
			{
				$productos[$i]['lot']='on';
			}
			else
			{
				$productos[$i]['lot']='off';
			}

			if ($row[6]=='on')
			{
				$productos[$i]['cdo']='on';
			}
			else
			{
				$productos[$i]['cdo']='off';
			}
		}
	}
	else
	{
		$productos=false;
	}
	return $productos;
}

function consultarLotes($parbus, $cco, $lote, $crear, $tipo, $destino)
{
	global $conex;
	global $wbasedato;

	if ($lote!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		if ($tipo=='C' and $crear!='cargar')
		{
			$q= "   SELECT Concod from ".$wbasedato."_000008 "
			."    WHERE Conind = '-1' "
			."      AND Concar = 'on' "
			."      AND Conest = 'on' ";

			$res1 = mysql_query($q,$conex);
			$row2 = mysql_fetch_array($res1);


			$q =  " SELECT distinct Plocod "
			."        FROM ".$wbasedato."_000006, ".$wbasedato."_000007, ".$wbasedato."_000004 "
			."    WHERE   Mencon= '".$row2[0]."' "
			."            and Mencco =mid('".$cco."',1,instr('".$cco."','-')-1) "
			."            and Menccd = '".$destino."' "
			."            and Mdecon = Mencon "
			."            and Mdedoc = Mendoc "
			."  		  and Mdeart = '".$parbus."' "
			."            and Mdeest = 'on' "
			."            and Menest = 'on' "
			."            and Plocod= mid(Mdenlo,1,instr(Mdenlo,'-')-1) "
			."            and mid(Mdenlo,1,instr(Mdenlo,'-')-1)= '".$lote."' "
			."            and Plopro= '".$parbus."' "
			."        ORDER BY ".$wbasedato."_000004.id desc";

			$res = mysql_query($q,$conex);
			$num = mysql_num_rows($res);
			if($num>0)
			{
				$consultas[0]=$lote;
				$cadena="Plocod != '".$lote."' AND";
				$inicio=1;
			}
			else
			{
				$cadena='';
				$inicio=0;
			}
		}
		else
		{
			$consultas[0]=$lote;
			$cadena="Plocod != '".$lote."' AND";
			$inicio=1;
		}
	}
	else
	{
		$cadena='';
		$inicio=0;
	}

	$dias=date('d')-20;
	if ($dias>0)
	{
		$fecha=date('Y').'-'.date('m').'-'.$dias;
	}
	else
	{
		$dias=31+$dias;
		$fecha = mktime(0, 0, 0, date("m")-1, $dias,   date("Y"));
		$fecha=date('Y-m-d', $fecha);
	}

	if ($crear=='cargar')
	{
		$q= " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Ploest "
		."       FROM ".$wbasedato."_000004 "
		."    WHERE ".$cadena." "
		."       Plopro = '".$parbus."' "
		."       AND Plocco = mid('".$cco."',1,instr('".$cco."','-')-1) "
		."       AND Ploest = 'on' "
		."       AND Plosal > 0 "
		."    Order by 1 asc  ";
	}
	else
	{

		if ($tipo=='C')
		{

			$q= "   SELECT Concod from ".$wbasedato."_000008 "
			."    WHERE Conind = '-1' "
			."      AND Concar = 'on' "
			."      AND Conest = 'on' ";

			$res1 = mysql_query($q,$conex);
			$row2 = mysql_fetch_array($res1);


			$q =  " SELECT distinct Plocod "
			."        FROM ".$wbasedato."_000006, ".$wbasedato."_000007, ".$wbasedato."_000004 "
			."    WHERE ".$cadena." "
			."            Mencon= '".$row2[0]."' "
			."            and Mencco =mid('".$cco."',1,instr('".$cco."','-')-1) "
			."            and Menccd = '".$destino."' "
			."            and Mdecon = Mencon "
			."            and Mdedoc = Mendoc "
			."  		  and Mdeart = '".$parbus."' "
			."            and Mdeest = 'on' "
			."            and Menest = 'on' "
			."            and Plocod= mid(Mdenlo,1,instr(Mdenlo,'-')-1) "
			."            and Plopro= '".$parbus."' "
			."        ORDER BY ".$wbasedato."_000004.id desc";
		}
		else
		{
			$q= " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Ploest "
			."       FROM ".$wbasedato."_000004 "
			."    WHERE ".$cadena." "
			."       Plopro = '".$parbus."' "
			."       AND Plocco = mid('".$cco."',1,instr('".$cco."','-')-1) "
			."       AND Ploest = 'on' "
			."       AND fecha_data > '".$fecha."' "
			."    Order by 1 desc  ";
		}
	}

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$consultas[$inicio]=$row['Plocod'];
			$inicio++;
		}
		return $consultas;
	}

	if (!isset($consultas[0]))
	{
		return false;
	}
	else
	{
		return $consultas;
	}
}

function consultarProducto($codigo, &$via, &$tfd, &$tfh, &$tvd, &$tvh, &$fecha, &$inslis, &$tippro, &$foto, &$neve)
{
	global $conex;
	global $wbasedato;

	$q= " SELECT Artvia, Arttin, Arttve, Artfec, Arttip, Artest, Artfot, Artnev, Tipdes, Tipcdo "
	."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001 "
	."    WHERE Artcod = '".$codigo."' "
	."       AND Artest = 'on' "
	."       AND Arttip= Tipcod "
	."    Order by 1 ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		$row = mysql_fetch_array($res);
		$via=$row['Artvia'];
		$tfd=floor($row['Arttin']/24);
		$tfh=$row['Arttin']%24;
		$tvd=floor($row['Arttve']/24);
		$tvh=$row['Arttve']%24;
		$fecha=$row['Artfec'];
		$foto=$row['Artfot'];
		$neve=$row['Artnev'];

		if ($row['Tipcdo']=='on')
		{
			$row['Tipcdo']='CODIFICADO';
		}
		else
		{
			$row['Tipcdo']='NO CODIFICADO';
		}

		$tippro=$row['Arttip'].'-'.$row['Tipdes'].'-'.$row['Tipcdo'];

	}

	$q= " SELECT Pdeins, Pdecan, Artcom, Artgen, Artuni, Unides "
	."       FROM ".$wbasedato."_000003, ".$wbasedato."_000002, farstore_000002 "
	."    WHERE  Pdepro = '".$codigo."' "
	."       AND Pdeest = 'on' "
	."       AND Pdeins= Artcod "
	."       AND Artuni= Unicod "
	."       AND Uniest='on' "
	."    Order by 1 ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);
			$inslis[$i]['cod']=$row[0];
			$inslis[$i]['nom']=str_replace('-',' ',$row[2]);
			$inslis[$i]['gen']=str_replace('-',' ',$row[3]);
			$inslis[$i]['pre']=$row[4].'-'.$row[5];
			$inslis[$i]['can']=$row[1];
			$inslis[$i]['pri']='';
		}
	}
}

function consultarMovimiento(&$inslis, $cco, $destino, $ingreso, $tipo, &$presentaciones, $signo, $insumo, $lote)
{
	global $conex;
	global $wbasedato;

	$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
	."    WHERE Conind = '".$signo."' "
	."      AND Conane = 'on' "
	."      AND Conest = 'on' ";
	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);

	//consulto la fuente segun el tipo
	switch($tipo)
	{
		case 'A':
		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '-1' "
		."      AND Conave = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'B':
		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '-1' "
		."      AND Conave = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'C':
		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '-1' "
		."      AND Concar = 'on' "
		."      AND Conest = 'on' ";
		break;
	}

	$res1 = mysql_query($q,$conex);
	$row2 = mysql_fetch_array($res1);

	if ($tipo!='C')
	{
		$q =  " SELECT Mdepre, Mdepaj, Mdecaj "
		."        FROM ".$wbasedato."_000006, ".$wbasedato."_000007 "
		."      WHERE Mencon= '".$row1[1]."' "
		."            and Mencco =mid('".$cco."',1,instr('".$cco."','-')-1) "
		."            and Menccd = mid('".$destino."',1,instr('".$destino."','-')-1) "
		."            and mid(Mendan,1,instr(Mendan,'-')-1)='".$row2[1]."' "
		."            and Mdecon = Mencon "
		."            and Mdedoc = Mendoc "
		."  		  and Mdeart = '".$inslis['cod']."' "
		."            and Mdenlo = '".$lote."-".$insumo."' "
		."            and Mdeest = 'on' "
		."            and Menest = 'on' "
		."        ORDER BY ".$wbasedato."_000006.id desc";
	}
	else
	{
		$q =  " SELECT Mdepre, Mdepaj, Mdecaj "
		."        FROM ".$wbasedato."_000006, ".$wbasedato."_000007 "
		."      WHERE Mencon= '".$row1[1]."' "
		."            and Mencco = mid('".$cco."',1,instr('".$cco."','-')-1) "
		."            and Menccd = '".$destino."-".$ingreso."' "
		."            and mid(Mendan,1,instr(Mendan,'-')-1)='".$row2[1]."' "
		."            and Mdecon = Mencon "
		."            and Mdedoc = Mendoc "
		."  		  and Mdeart = '".$inslis['cod']."' "
		."            and Mdenlo = '".$lote."-".$insumo."' "
		."            and Mdeest = 'on' "
		."            and Menest = 'on' "
		."        ORDER BY ".$wbasedato."_000006.id desc";
	}

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		$row1 = mysql_fetch_array($res1);
		if ($row1[0]!='')
		{
			$q =  " SELECT Artcom, Artgen "
			."        FROM  movhos_000026 "
			."      WHERE Artcod = mid('".$row1[0]."',1,instr('".$row1[0]."','-')-1) "
			."            and Artest='on' ";

			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1>0)
			{
				$row2 = mysql_fetch_array($res1);
				$inslis['prese']=$row1[0].'-'.$row2[0].'-'.$row2[1];
				$presentaciones[0]=$row1[0];
			}
			else
			{
				return false;
			}
		}
		else
		{
			$inslis['prese']='';
			$inslis['fal']=0;
			$presentaciones[0]='';
		}

		if ($row1[1]!='')
		{
			$q =  " SELECT Artcom, Artgen "
			."        FROM  movhos_000026 "
			."      WHERE Artcod = mid('".$row1[1]."',1,instr('".$row1[1]."','-')-1) "
			."            and Artest='on' ";


			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1>0)
			{
				$row2 = mysql_fetch_array($res1);
				$exp=explode('-',$row1[1]);
				$inslis['aju']=$row1[2].'-'.$exp[0].'-'.$row2[0].'-'.$row2[1];
				//echo $inslis['aju'];
				$inslis['fal']=$inslis['can']-$row1[2];
				//echo $inslis['fal'];
			}
			else
			{
				return false;
			}
		}
		else
		{
			$inslis['aju']='0-0-SIN AJUSTES DE PRESENTACION';
			$inslis['fal']=$inslis['can'];
		}
	}
	else
	{
		return false;
	}
	return true;
}

function consultarMovimiento2(&$inslis, $cco, $destino, $ingreso, $tipo, &$presentaciones, $insumo, $lote, $documento, $concepto)
{

	global $conex;
	global $wbasedato;

	$q= "   SELECT Conind, Concod from ".$wbasedato."_000008 "
	."    WHERE Concod = '".$concepto."' "
	."      AND Conest = 'on' ";
	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);

	$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
	."    WHERE Conind = -1*'".$row1[0]."' "
	."      AND Conane = 'on' "
	."      AND Conest = 'on' ";
	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);
	$bu1=$row1[0];
	$bu2=$row1[1];

	if ($tipo!='C')
	{
		$q =  " SELECT Mdepre, Mdepaj, Mdecaj "
		."        FROM ".$wbasedato."_000006, ".$wbasedato."_000007 "
		."      WHERE Mencon= '".$row1[1]."' "
		."            and Mencco =mid('".$cco."',1,instr('".$cco."','-')-1) "
		."            and Menccd = mid('".$destino."',1,instr('".$destino."','-')-1) "
		."            and Mendan='".$concepto."-".$documento."' "
		."            and Mdecon = Mencon "
		."            and Mdedoc = Mendoc "
		."  		  and Mdeart = '".$inslis['cod']."' "
		."            and Mdenlo = '".$lote."-".$insumo."' "
		."            and Mdeest = 'on' "
		."            and Menest = 'on' "
		."        ORDER BY ".$wbasedato."_000006.id desc";
	}
	else
	{
		$q =  " SELECT Mdepre, Mdepaj, Mdecaj "
		."        FROM ".$wbasedato."_000006, ".$wbasedato."_000007 "
		."      WHERE Mencon= '".$row1[1]."' "
		."            and Mencco = mid('".$cco."',1,instr('".$cco."','-')-1) "
		."            and Menccd = '".$destino."-".$ingreso."' "
		."            and Mendan='".$concepto."-".$documento."' "
		."            and Mdecon = Mencon "
		."            and Mdedoc = Mendoc "
		."  		  and Mdeart = '".$inslis['cod']."' "
		."            and Mdenlo = '".$lote."-".$insumo."' "
		."            and Mdeest = 'on' "
		."            and Menest = 'on' "
		."        ORDER BY ".$wbasedato."_000006.id desc";
	}

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		$row1 = mysql_fetch_array($res1);
		if ($row1[0]!='')
		{
			$q =  " SELECT Artcom, Artgen "
			."        FROM  movhos_000026 "
			."      WHERE Artcod = mid('".$row1[0]."',1,instr('".$row1[0]."','-')-1) "
			."            and Artest='on' ";

			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1>0)
			{
				$row2 = mysql_fetch_array($res1);
				$inslis['prese']=$row1[0].'-'.$row2[0].'-'.$row2[1];
				$presentaciones[0]=$row1[0];
			}
			else
			{
				return false;
			}
		}
		else
		{
			$inslis['prese']='';
			$inslis['fal']=0;
			$presentaciones[0]='';
		}

		if ($row1[1]!='')
		{
			$q =  " SELECT Artcom, Artgen "
			."        FROM  movhos_000026 "
			."      WHERE Artcod = mid('".$row1[1]."',1,instr('".$row1[1]."','-')-1) "
			."            and Artest='on' ";


			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1>0)
			{
				$row2 = mysql_fetch_array($res1);
				$exp=explode('-',$row1[1]);
				$inslis['aju']=$row1[2].'-'.$exp[0].'-'.$row2[0].'-'.$row2[1];
				$inslis['fal']=$inslis['can']-$row1[2];
			}
			else
			{
				return false;
			}
		}
		else
		{
			$inslis['aju']='0-0-SIN AJUSTES DE PRESENTACION';

			$inslis['fal']=$inslis['can'];
		}
	}
	else
	{
		return false;
	}
	return true;
}

function consultarEscogidos($cco, $historia, $ingreso, $tipo,  $documento, $concepto, $preparacion, &$escogidos)
{

	global $conex;
	global $wbasedato;

	$q= "   SELECT Conind, Concod from ".$wbasedato."_000008 "
	."    WHERE Concod = '".$concepto."' "
	."      AND Conest = 'on' ";
	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);

	$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
	."    WHERE Conind = -1*'".$row1[0]."' "
	."      AND Conane = 'on' "
	."      AND Conest = 'on' ";
	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);
	$bu1=$row1[0];
	$bu2=$row1[1];

	for ($i=0;$i<count($escogidos);$i++)
	{
		for ($j=0;$j<count($escogidos[$i]);$j++)
		{
			$q= "SELECT Mdepaj "
			."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000006 "
			."      WHERE Mencon= '".$bu2."' "
			."            and Mencco =mid('".$cco."',1,instr('".$cco."','-')-1) "
			."            and Menccd = '".$historia."-".$ingreso."' "
			."            and Mendan='".$concepto."-".$documento."' "
			."            and Mdecon = Mencon "
			."            and Mdedoc = Mendoc "
			."            AND Mdepaj = mid('".$preparacion[$i][$j]."',1,instr('".$preparacion[$i][$j]."','-')-1) "
			."            AND Mdeart = '' ";

			$res = mysql_query($q,$conex);
			$num = mysql_num_rows($res);
			if ($num>0)
			{
				$escogidos[$i][$j]='checked';
			}
		}
	}

}


function consultarPreparacion(&$escogidos)
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT Tipcod, Tipdes "
	."        FROM ".$wbasedato."_000001 "
	."      WHERE Tipmmq= 'on' "
	."            and Tipest ='on' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	for ($i=0; $i<$num1; $i++)
	{

		$row = mysql_fetch_array($res1);
		$preparacion [$i]['nom']=$row[1];

		//consulto los conceptos
		$q =  " SELECT Apppre, C.Artcom, Appuni"
		."        FROM ".$wbasedato."_000002 A, ".$wbasedato."_000009 B, movhos_000026 C "
		."      WHERE A.Arttip = '".$row[0]."' "
		."        AND A.Artcod = B.Appcod "
		."        AND A.Artest='on' "
		."        AND B.Apppre=C.Artcod ";

		$res2 = mysql_query($q,$conex);
		$num2 = mysql_num_rows($res2);


		for ($j=0;$j<$num2;$j++)
		{
			$row2 = mysql_fetch_array($res2);
			$preparacion[$i][$j]=$row2[0].'-'.$row2[1].'-'.$row2[2];
			if (!isset($escogidos[$i][$j]))
			{
				$escogidos[$i][$j]='';
			}
			else
			{
				$escogidos[$i][$j]='checked';
			}
		}
	}

	return $preparacion;
}

function consultarAjuste(&$inslis, $cco, $historia, $ingreso)
{
	global $conex;
	global $wbasedato;

	//consulto los conceptos
	$q =  " SELECT Ajpart, Ajpcan, Ajpfve, Ajphve, Artcom, Artgen "
	."        FROM ".$wbasedato."_000010, ".$wbasedato."_000009, movhos_000026 "
	."      WHERE Ajphis= '".$historia."' "
	."            and Ajpest ='on' "
	."            and Ajping = '".$ingreso."' "
	."            and Ajpcco = mid('".$cco."',1,instr('".$cco."','-')-1) "
	."  		  and Ajpart = Apppre "
	."            and Appcod = '".$inslis['cod']."' "
	."            and Appest = 'on' "
	."            and Artcod = Ajpart "
	."        ORDER BY 3, 4 desc";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		$row1 = mysql_fetch_array($res1);
		if (($row1['Ajpfve'] > date('Y-m-d')) or ($row1['Ajpfve'] == date('Y-m-d') and $row1['Ajphve'] <= date("H:i:s")))
		{
			$inslis['aju']=$row1['Ajpcan'].'-'.$row1['Ajpart'].'-'.str_replace('-',' ',$row1['Artcom']).'-'.str_replace('-',' ',$row1['Artgen']);
			$inslis['fal']=$inslis['can']-$row1['Ajpcan'];
			if ($inslis['fal']<0)
			{
				$inslis['fal']=0;
			}
		}
		else
		{
			$inslis['aju']='0-0-AJUSTE DE PRESENTACION VENCIDO';
			$inslis['fal']=$inslis['can'];
		}
	}
	else
	{
		$inslis['aju']='0-0-SIN AJUSTES DE PRESENTACION';
		$inslis['fal']=$inslis['can'];
	}

}

function validarHistoria($cco, $historia, &$ingreso, &$mensaje, &$nombre, &$habitacion)
{
	global $conex;
	global $wbasedato;

	if($historia == '0')
	{
		$q=" SELECT Ccohcr "
		."     FROM movhos_000011 "
		."   WHERE	Ccocod = mid('".$cco."',1,instr('".$cco."','-')-1) "
		."     AND	Ccoest = 'on'";

		$err=mysql_query($q,$conex);
		$row=mysql_fetch_array($err);

		if($row['hcr']=='on')
		{
			$ingreso='';
			$habitacion='';
			$val=true;
		}else
		{
			$mensaje = "ESTE CENTRO DE COSTOS NO PERMITE CARGOS A  LA HISTORIA CERO";
			return (false);
		}
	}
	else if(is_numeric($historia))
	{
		$q = "SELECT Oriing, Pacno1, Pacno2, Pacap1, Pacap2 "
		."      FROM root_000037, root_000036 "
		."     WHERE Orihis = '".$historia."' "
		."       AND Oriori = '01' "
		."       AND Oriced = Pacced ";

		$err=mysql_query($q,$conex);
		$num=mysql_num_rows($err);
		if($num > 0)
		{
			$row=mysql_fetch_array($err);
			$ingreso=$row['Oriing'];
			$nombre=$row['Pacno1'].' '.$row['Pacno2'].' '.$row['Pacap1'].' '.$row['Pacap2'];

			$q = "SELECT * "
			."      FROM movhos_000018 "
			."     WHERE Ubihis = '".$historia."' "
			."       AND Ubiing = '".$ingreso."' "
			."       AND Ubialp <> 'on' "
			."       AND Ubiald <> 'on' ";

			$err1=mysql_query($q,$conex);
			$num1=mysql_num_rows($err1);
			if($num1 > 0 )
			{
				$row=mysql_fetch_array($err1);
				$habitacion=$row['Ubihac'];
				return (true);
			}
			else
			{
				$mensaje = "EL PACIENTE ESTA EN PROCESO DE ALTA";
				return(false);
			}
		}
		else
		{
			$mensaje = "EL PACIENTE NO SE ENCUENTRA ACTIVO";
			return (false);
		}
	}
	else if( !is_numeric($historia))
	{
		$mensaje = "LAS HISTORIAS CLINICAS DEBEN SER NUMERICAS";
		return (false);
	}
	return(true);
}

function validarMatrix($insumo, $cco, $otro, $tipo)
{
	global $conex;
	global $wbasedato;

	$q = " SELECT * FROM ".$wbasedato."_000002 where Artcod='".$insumo['cod']."' and Artest='on' ";

	$res1 = mysql_query($q,$conex);
	$num3 = mysql_num_rows($res1);
	if ($num3>0)
	{
		$q = " SELECT karexi FROM ".$wbasedato."_000005 where karcod='".$insumo['cod']."' and Karcco= mid('".$cco."',1,instr('".$cco."','-')-1) ";
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);

		if ($num1>0)
		{
			$row1 = mysql_fetch_array($res1);
			if ($row1[0]>0)
			{
				if ($insumo['lot']=='on')
				{
					$q= " SELECT Plosal, Plofve, plohve "
					."       FROM ".$wbasedato."_000004 "
					."    WHERE Plopro = '".$insumo['cod']."' "
					."       AND Plocod='".$otro."' "
					."       AND Ploest='on' "
					."       AND Plocco= mid('".$cco."',1,instr('".$cco."','-')-1) ";
				}
				else
				{
					$q =  " SELECT Appexi "
					."        FROM  ".$wbasedato."_000009 "
					."      WHERE Appcod='".$insumo['cod']."' "
					."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
					."            and Appest='on' "
					."            and Apppre=mid('".$otro."',1,instr('".$otro."','-')-1) ";
				}

				$res2 = mysql_query($q,$conex);
				$num2 = mysql_num_rows($res1);
				if ($num2>0)
				{
					$row2 = mysql_fetch_array($res2);
					if ($row2[0]<=0)
					{
						$val=1;
					}
					else if ($tipo=='C' and $insumo['lot']=='on' and ($row2[1]<date('Y-m-d') or ($row2[1]==date('Y-m-d') and $row2[2]<date("H:i:s"))))
					{
						$val=-2;
					}
					else
					{
						$val=2;
					}
				}
				else
				{
					$val=-3;
				}
			}
			else
			{
				$val=0;
			}
		}
		else
		{
			$val=0;
		}
	}
	else
	{
		$val=-1;
	}

	return $val;
}

function grabarEncabezadoSalidaMatrix(&$codigo, &$consecutivo, $cco, $usuario, $cco2, $tipo)
{
	global $conex;
	global $wbasedato;

	$q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
	//$errlock = mysql_query($q,$conex);
	switch($tipo)
	{
		case 'A':
		$anexo='';
		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '-1' "
		."      AND Conave = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'B':
		$anexo='';
		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '-1' "
		."      AND Conave = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'C':
		$anexo='';
		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '-1' "
		."      AND Concar = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'F':
		$anexo=$codigo.'-'.$consecutivo;
		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '-1' "
		."      AND Conane = 'on' "
		."      AND Conest = 'on' ";
		break;
	}

	$res1 = mysql_query($q,$conex);

	switch($tipo)
	{
		case 'A':
		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '-1'"
		."      AND Conave = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'B':
		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '-1'"
		."      AND Conave = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'C':
		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '-1'"
		."      AND Concar = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'F':
		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '-1'"
		."      AND Conane = 'on' "
		."      AND Conest = 'on' ";
		break;
	}

	$res1 = mysql_query($q,$conex);
	$row2 = mysql_fetch_array($res1);
	$codigo=$row2[1];
	$consecutivo=$row2[0];

	$q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
	$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());



	$q= " INSERT INTO ".$wbasedato."_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".date('Y')."', '".date('m')."','".$row2[0]."', '".$row2[1]."' , '".date('Y-m-d')."', mid('".$cco."',1,instr('".$cco."','-')-1) , '".$cco2."' ,       '".$anexo."', '".$usuario."',      '' , 'on', 'C-".$usuario."') ";


	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS ".mysql_error());
}

function descontarArticuloMatrix($inscod, $cco, $lote, $dato)
{
	global $conex;
	global $wbasedato;

	global $conex;
	global $wbasedato;

	if ($lote!='')
	{

		$q= "   UPDATE ".$wbasedato."_000005 "
		."      SET karexi = karexi - 1 "
		."    WHERE Karcod = '".$inscod."' "
		."      AND karcco = mid('".$cco."',1,instr('".$cco."','-')-1) ";


		$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR EL ARTICULO ".mysql_error());

		$q= "   UPDATE ".$wbasedato."_000004 "
		."      SET Plosal = Plosal-1 "
		."    WHERE Plocod =  '".$dato."' "
		."      AND Plopro ='".$inscod."' "
		."      AND Ploest ='on' "
		."      AND Plocco = mid('".$cco."',1,instr('".$cco."','-')-1) ";
	}
	else
	{
		$q= " SELECT Appcnv "
		."      FROM ".$wbasedato."_000009 "
		."    WHERE Appcod =  '".$inscod."' "
		."      AND Apppre='".$dato."' "
		."      AND Appest ='on' "
		."      AND Appcco = mid('".$cco."',1,instr('".$cco."','-')-1) ";

		$res1 = mysql_query($q,$conex);
		$row2 = mysql_fetch_array($res1);

		$q= "   UPDATE ".$wbasedato."_000005 "
		."      SET karexi = karexi - (1*".$row2[0].") "
		."    WHERE Karcod = '".$inscod."' "
		."      AND karcco = mid('".$cco."',1,instr('".$cco."','-')-1) ";

		$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR EL ARTICULO ".mysql_error());

		$q= "   UPDATE ".$wbasedato."_000009 "
		."      SET Appexi = Appexi- 1*Appcnv "
		."    WHERE Appcod =  '".$inscod."' "
		."      AND Apppre='".$dato."' "
		."      AND Appest ='on' "
		."      AND Appcco = mid('".$cco."',1,instr('".$cco."','-')-1) ";

	}

	$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());
}

function sumarArticuloMatrix($inscod, $cco, $lote, $dato)
{
	global $conex;
	global $wbasedato;

	if ($lote!='')
	{

		$q= "   UPDATE ".$wbasedato."_000005 "
		."      SET karexi = karexi + 1 "
		."    WHERE Karcod = '".$inscod."' "
		."      AND karcco = mid('".$cco."',1,instr('".$cco."','-')-1) ";


		$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO SUMAR EL ARTICULO ".mysql_error());

		$q= "   UPDATE ".$wbasedato."_000004 "
		."      SET Plosal = Plosal+1 "
		."    WHERE Plocod =  '".$dato."' "
		."      AND Plopro ='".$inscod."' "
		."      AND Ploest ='on' "
		."      AND Plocco = mid('".$cco."',1,instr('".$cco."','-')-1) ";

	}
	else
	{
		$q= " SELECT Appcnv "
		."      FROM ".$wbasedato."_000009 "
		."    WHERE Appcod =  '".$inscod."' "
		."      AND Apppre='".$prese."' "
		."      AND Appest ='on' "
		."      AND Appcco = mid('".$cco."',1,instr('".$cco."','-')-1) ";

		$res1 = mysql_query($q,$conex);
		$row2 = mysql_fetch_array($res1);

		$q= "   UPDATE ".$wbasedato."_000005 "
		."      SET karexi = karexi + (1*".$row2[0].") "
		."    WHERE Karcod = '".$inscod."' "
		."      AND karcco = mid('".$cco."',1,instr('".$cco."','-')-1) ";

		$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO SUMAR EL ARTICULO ".mysql_error());

		$q= "   UPDATE ".$wbasedato."_000009 "
		."      SET Appexi = Appexi+ 1*Appcnv "
		."    WHERE Appcod =  '".$inscod."' "
		."      AND Apppre='".$prese."' "
		."      AND Appest ='on' "
		."      AND Appcco = mid('".$cco."',1,instr('".$cco."','-')-1) ";

	}
	$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO SUMAR UN INSUMO ".mysql_error());
}

function grabarEncabezadoEntradaMatrix(&$codigo, &$consecutivo, $cco, $cco2, $usuario, $tipo)
{

	global $conex;
	global $wbasedato;

	$q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
	$errlock = mysql_query($q,$conex);

	switch($tipo)
	{
		case 'A':
		$anexo='';
		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '1' "
		."      AND Conave = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'B':
		$anexo='';
		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '1' "
		."      AND Conave = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'C':
		$anexo='';
		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '1' "
		."      AND Concar = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'F':
		$anexo=$codigo.'-'.$consecutivo;
		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '1' "
		."      AND Conane = 'on' "
		."      AND Conest = 'on' ";
		break;
	}

	$res1 = mysql_query($q,$conex);

	switch($tipo)
	{
		case 'A':
		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '1'"
		."      AND Conave = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'B':
		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '1'"
		."      AND Conave = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'C':
		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '1'"
		."      AND Concar = 'on' "
		."      AND Conest = 'on' ";
		break;

		case 'F':
		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '1'"
		."      AND Conane = 'on' "
		."      AND Conest = 'on' ";
		break;
	}

	$res1 = mysql_query($q,$conex);
	$row2 = mysql_fetch_array($res1);
	$codigo=$row2[1];
	$consecutivo=$row2[0];

	$q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
	$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());



	$q= " INSERT INTO ".$wbasedato."_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".date('Y')."', '".date('m')."','".$row2[0]."', '".$row2[1]."' , '".date('Y-m-d')."', '".$cco."' , '".$cco2."' ,      '".$anexo."' , '".$usuario."',      '' , 'on', 'C-".$usuario."') ";


	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE ENTRADA DEL ARTICULO ".mysql_error());

}

function grabarDetalleEntradaMatrix($codpro, $codigo, $consecutivo, $usuario, $prese, $lote, $ajupre, $ajucan)
{
	global $conex;
	global $wbasedato;

	$q= " INSERT INTO ".$wbasedato."_000007 (   Medico       ,            Fecha_data,                  Hora_data,        Mdecon,           Mdedoc ,       Mdeart , Mdecan, Mdefve,    Mdenlo,           Mdepre,          Mdepaj,      Mdecaj, Mdeest,  Seguridad) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$codpro."', '1' ,     '',  '".$lote."',   '".$prese."',   '".$ajupre."',   '".$ajucan."', 'on', 'C-".$usuario."') ";


	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL DETALLE DE ENTRADA DE UN ARTICULO ".mysql_error());

}

function grabarDetalleSalidaMatrix($inscod, $codigo, $consecutivo, $usuario, $prese, $lote, $ajupre, $ajucan)
{
	global $conex;
	global $wbasedato;

	$q= " INSERT INTO ".$wbasedato."_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,             Mdecan ,      Mdefve, Mdenlo, Mdepre,  Mdepaj, Mdecaj, Mdeest,  Seguridad) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$inscod."', '1' , '',     '".$lote."',   '".$prese."', '".$ajupre."','".$ajucan."','on', 'C-".$usuario."') ";


	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN ARTICULO ".mysql_error());

}



//----------------------------------------------------------funciones de presentacion------------------------------------------------

/**
 * Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a los scripts consulta.php, seguimiento.php
 * para consulta.php existen dos opciones mandandole el paramentro para=recibidos o para=enviados, asi ese Script consultara
 * uno u otro tipo de requerimiento
 * 
 * Adicionalmente esta funcione se encarga de abrir la forma del Script que se llama informatica
 *
 * No necesita ningun parametro ni devuelve
 */
function pintarTitulo()
{
	echo "<table ALIGN=CENTER width='50%'>";
	//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	echo "<tr><td class='titulo1'>DEVOLUCIONES CENTRAL DE MEZCLAS</td></tr>";
	echo "<tr><td class='titulo2'>Fecha: ".date('Y-m-d')."&nbsp Hora: ".(string)date("H:i:s")."</td></tr></table></br>";
}


function pintarBusqueda($consultas, $forcon, $tipo)
{
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<form name='producto2' action='cargos2.php' method=post>";
	echo "<tr><td class='titulo3' colspan='3' align='center'>Consulta: ";
	echo "<select name='forcon' class='texto5' onchange='enter7()'>";
	echo "<option>".$forcon."</option>";
	if ($forcon!='Numero de movimiento')
	echo "<option>Numero de movimiento</option>";
	if ($tipo!='C')
	{
		if ($forcon!='Centro de costos destino')
		echo "<option>Centro de costos destino</option>";
	}
	else
	{
		if ($forcon!='Historia')
		echo "<option>Historia</option>";
	}
	if ($forcon!='Articulo')
	echo "<option>Articulo</option>";
	echo "</select>";

	switch ($forcon)
	{
		case '':
		echo "&nbsp";
		break;

		case 'Numero de movimiento':
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		break;

		case 'Articulo':
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de ".$forcon.": ";
		echo "<select name='insfor' class='texto5'>";
		echo "<option>Codigo</option>";
		echo "<option>Nombre comercial</option>";
		echo "<option>Nombre genérico</option>";
		echo "</select>";
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp; ";
		echo "&nbsp; Fecha inicial:<input type='TEXT' name='parcon2' value='' size=10 class='texto5'>&nbsp;Fecha final:<input type='TEXT' name='parcon3' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'>";
		break;

		case 'Historia':
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de ".$forcon.": ";
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp; ";
		echo "&nbsp; Fecha inicial:<input type='TEXT' name='parcon2' value='' size=10 class='texto5'>&nbsp;Fecha final:<input type='TEXT' name='parcon3' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'>";
		break;

		default:
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de ".$forcon.": ";
		echo "<select name='insfor' class='texto5'>";
		echo "<option>Codigo</option>";
		echo "<option>Nombre</option>";
		echo "</select>";
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp; ";
		echo "&nbsp; Fecha inicial:<input type='TEXT' name='parcon2' value='' size=10 class='texto5'>&nbsp;Fecha final:<input type='TEXT' name='parcon3' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'>";
		break;


	}

	echo "&nbsp; Resultados: <select name='consulta' class='texto5' onchange='enter7()'>";
	if ($consultas[0]['cod']!='')
	{
		for ($i=0;$i<count($consultas);$i++)
		{
			echo "<option>".$consultas[$i]."</option>";
		}
	}
	else
	{
		echo "<option value=''></option>";
	}
	echo "</select>";
	echo "</td></tr>";
	echo "<input type='hidden' name='tipo' value='".$tipo."'>";
	echo "</form>";
}


/*se le envia para pintar en html un vector de usuario ($usuario) con las siguientes caracteristicas:
*  ['cod']=codigo del usuario
*  ['cco']=primer centro de costos encontrado para el usuario (podrian haber mas, luego se muestran)
*  ['ext']=extension del usuario
*  ['ema']=email
*  ['car']=cargo
*  ['nom']=nombre
*  ['sup']= en on quiere decir que debe permitir ingresar otros usuarios
*
* Adicionalmente se le manda la lista de usuarios ($personas) por si el usuario puede solicitar por otro
*/
function pintarFormulario($estado, $ccos, $numtra, $tipo, $historia, $destinos, $fecha, $ingreso, $nombre, $habitacion, $crear, $numcan)
{
	echo "<form name='producto3' action='cargos2.php' method=post>";
	echo "<input type='hidden' name='tipo' value='".$tipo."'>";
	echo "<tr><td colspan=3 class='titulo3' align='center'><INPUT TYPE='submit' NAME='NUEVO' VALUE='Nuevo' class='texto5' ></td></tr>";
	echo "</table></form>";

	echo "<form name='producto' action='cargos2.php' method=post>";
	echo "<table border=0 ALIGN=CENTER width=90%>";
	if ($tipo=='C')
	{
		echo "<tr><td colspan=3 class='titulo3' align='center'><b>Informacion general del cargo</b></td></tr>";
		echo "<tr><td class='texto1' colspan='2' align='center'>Centro de costos de origen: ";
	}
	else
	{
		echo "<tr><td colspan=3 class='titulo3' align='center'><b>Informacion general de la avería</b></td></tr>";
		echo "<tr><td class='texto1' colspan='3' align='center'>Centro de costos de origen: ";
	}

	echo "<select name='cco' class='texto5' onchange='enter8()'>";
	if ($ccos[0]!='')
	{
		for ($i=0;$i<count($ccos);$i++)
		{
			echo "<option >".$ccos[$i]."</option>";
		}
	}
	else
	{
		echo "<option value=''></option>";
	}
	echo "</select>";
	echo "</td>";

	if ($tipo=='C')
	{
		echo "<td class='texto1' colspan='1' align='center'>Numero de historia: <input type='TEXT' name='historia' value='".$historia."' size=10 class='texto5'> ";
	}
	else
	{
		echo "</tr><tr><td class='texto1' colspan='3' align='center'>Centro de costos destino: ";
		echo "<select name='fordes' class='texto5'>";
		echo "<option>codigo</option>";
		echo "<option>nombre</option>";
		echo "</select><input type='TEXT' name='pardes' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter1()' class='texto5'> ";
		echo "<select name='ccoDes' class='texto5' onchange='enter1()'>";
		if ($destinos[0]!='')
		{
			for ($i=0;$i<count($destinos);$i++)
			{
				echo "<option >".$destinos[$i]."</option>";
			}
		}
		else
		{
			echo "<option value=''></option>";
		}
		echo "</select>";
	}

	echo "</td></tr>";

	if ($tipo=='C')
	{
		echo "<tr><td class='texto2' colspan='1' align='left'>Numero de Ingreso: <input type='TEXT' name='ingreso' value='".$ingreso."' readonly='readonly' class='texto2' size='5'></td>";
		echo "<td class='texto2' colspan='1' align='left'>Habitacion: <input type='TEXT' name='habitacion' value='".$habitacion."' readonly='readonly' class='texto2' size='5'></td>";
		echo "<td class='texto2' colspan='1' align='left'>Nombre: <input type='TEXT' name='nombre' value='".$nombre."' readonly='readonly' class='texto2' size='40'></td></tr>";
	}

	echo "<tr><td class='texto2' colspan='1' align='left'>Numero de Movimiento: <input type='TEXT' name='numtra' value='".$numtra."' readonly='readonly' class='texto2' size='10'></td>";
	echo "<td class='texto2' colspan='2' align='left'>Fecha: <input type='TEXT' name='fecha' value='".$fecha."' readonly='readonly' class='texto2' size='10'></td></tr>";


	if ((isset($historia) and $historia!='') or $tipo!='C')
	{
		switch($estado)
		{
			case 'inicio':
			if ($crear=='cargar')
			{
				echo "<tr><td colspan=3 class='titulo3' align='center'><input type='radio' name='crear' class='titulo3' value='devolver' onclick='enter1()'>Devolver&nbsp;&nbsp;&nbsp;<input type='button' name='graba'  value='Grabar' onclick='enter9()'></td></tr>";
			}
			else
			{
				echo "<tr><td colspan=3 class='titulo3' align='center'><input type='radio' name='crear' class='titulo3' value='devolver' checked onclick='enter1()'>Devolver&nbsp;&nbsp;&nbsp;<input type='button' name='graba'  value='Grabar' onclick='enter9()'></td></tr>";
			}
			break;
			case 'creado':
			echo "<tr><td colspan=3 class='titulo3' align='center'>SE HA REALIZADO EL CARGO EXITOSAMENTE";
			if ($numcan!='' and $numcan>0)
			{
				echo "&nbsp;<INPUT TYPE='button' NAME='siguiente' VALUE='SIGUIENTE >>' onclick='enters(".$numcan.")' class='texto5'></td></tr>";
				echo "<input type='hidden' name='crear' value='cargar'></td>";
			}
			else
			{
				echo "</td></tr>";
			}

			break;
			case 'devuelto':
			echo "<tr><td colspan=3 class='titulo3' align='center'>SE HA DEVUELTO EL CARGO EXITOSAMENTE";
			if ($numcan!='' and $numcan>0)
			{
				echo "&nbsp;<INPUT TYPE='button' NAME='siguiente' VALUE='SIGUIENTE >>' onclick='enters(".$numcan.")' class='texto5'></td></tr>";
				echo "<input type='hidden' name='crear' value='devolver'></td>";
			}
			else
			{
				echo "</td></tr>";
			}
			break;
			case 'Creado':
			echo "<tr><td colspan=3 class='titulo3' align='center'>CARGO ACTIVO</td></tr>";
			break;
			case 'Desactivado':
			echo "<tr><td colspan=3 class='titulo3' align='center'>CARGO ANULADO</td></tr>";
			break;
		}
	}
	else
	{
		switch($estado)
		{
			case 'inicio':
			if ($crear=='cargar')
			{
				echo "<tr><td colspan=3 class='titulo3' align='center'><input type='radio' name='crear' class='titulo3' value='devolver'>Devolver&nbsp;&nbsp;&nbsp;<input type='submit' name='graba'  value='Enviar'></td></tr>";
			}
			else
			{
				echo "<tr><td colspan=3 class='titulo3' align='center'><input type='radio' name='crear' class='titulo3' value='devolver' checked >Devolver&nbsp;&nbsp;&nbsp;<input type='submit' name='graba'  value='Enviar'></td></tr>";
			}
			break;
			case 'creado':
			echo "<tr><td colspan=3 class='titulo3' align='center'>SE HA REALIZADO EL CARGO EXITOSAMENTE</td></tr>";
			break;
			case 'devuelto':
			echo "<tr><td colspan=3 class='titulo3' align='center'>DE HA DEVUELTO EL CARGO EXITOSAMENTE &nbsp;&nbsp;<a href='cen_Mez.php'>INICIAR</a> </td></tr>";
			break;
			case 'Creado':
			echo "<tr><td colspan=3 class='titulo3' align='center'>CARGO ACTIVO &nbsp;&nbsp;<a href='lotes.php?parcon=".$productos[0]['cod']."&forcon=Codigo del Producto&pintar=1'>/CREAR LOTE</a>&nbsp;&nbsp;<a href='#' onclick='enter3()'>/MODIFICAR</a>&nbsp;&nbsp;<a href='#' onclick='enter4()'>/DESACTIVAR</a></td></tr>";
			break;
			case 'Desactivado':
			echo "<tr><td colspan=3 class='titulo3' align='center'>CARGO ANULADO &nbsp;&nbsp;<a href='cen_Mez.php'>INICIAR</a> </td></tr>";
			break;
		}
	}
	echo "<input type='hidden' name='estado' value='".$estado."'></td>";
	echo "<input type='hidden' name='tipo' value='".$tipo."'></td>";
	echo "<input type='hidden' name='tvh' value='0'></td>";
	echo "<input type='hidden' name='grabar' value='0'></td>";
	echo "</table></br>";
}


function pintarInsumos($insumos, $inslis, $unidades, $lotes, $presentaciones, $tipo, $preparacion, $escogidos, $numcan)
{
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan='6' class='titulo3' align='center'><b>DETALLE DEL ARTICULO</b></td></tr>";
	echo "<tr><td class='texto1' colspan='6' align='center'>Dosis por cargar: <input type='TEXT' name='numcan' value='".$numcan."' size=10 class='texto5'></td></tr>";

	echo "<tr><td class='texto1' colspan='6' align='center'>Buscar Articulo por: ";
	echo "<select name='forbus2' class='texto5'>";
	echo "<option>Rotulo</option>";
	echo "<option>Codigo</option>";
	echo "<option>Nombre comercial</option>";
	echo "<option>Nombre generico</option>";
	echo "</select><input type='TEXT' name='parbus2' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Buscar'  class='texto5'></td> ";
	echo "<tr><td class='texto1' colspan='4' align='center'>Articulo: <select name='insumo' class='texto5' onchange='enter1()'>";
	if ($insumos!='')
	{
		for ($i=0;$i<count($insumos);$i++)
		{
			echo "<option value='".$insumos[$i]['cod']."-".$insumos[$i]['nom']."-".$insumos[$i]['gen']."-".$insumos[$i]['pre']."-".$insumos[$i]['lot']."-".$insumos[$i]['est']."'>".$insumos[$i]['cod']."-".$insumos[$i]['nom']."</option>";
		}
		echo "<input type='hidden' name='insumos[0]['lot']' value='".$insumos[0]['lot']."'></td>";
		echo "<input type='hidden' name='insumos[0]['cdo']' value='".$insumos[0]['cdo']."'></td>";
	}
	else
	{
		echo "<option ></option>";
	}
	echo "</select></td>";
	if ($insumos[0]['lot']=='on')
	{
		echo "<td class='texto1' colspan='2' align='center'>Lote: <select name='lote' class='texto5' onchange='enter1()'>";
		if (is_array($lotes))
		{
			for ($i=0;$i<count($lotes);$i++)
			{
				echo "<option>".$lotes[$i]."</option>";
			}
		}
		else
		{
			echo "<option ></option>";
		}
		echo "</select></td></tr>";
		$ind=0;
	}
	else if (is_array($unidades))
	{
		echo "<td class='texto1' colspan='2' align='center'>Presentacion: <select name='prese' class='texto5' onchange='enter1()'>";
		if ($unidades[0]!='')
		{
			for ($i=0;$i<count($unidades);$i++)
			{
				echo "<option>".$unidades[$i]."</option>";
			}
		}
		else
		{
			echo "<option ></option>";
		}
		echo "</select></td></tr>";
		$ind=0;
	}
	else
	{
		echo "<td class='texto1' colspan='2' align='center'>&nbsp;</td></tr>";
	}

	echo "<tr><td colspan='6' class='titulo3' align='center'>&nbsp</td></tr>";


	if ($inslis!='')
	{
		echo "<tr><td class='texto2' colspan='1' align='center'>Articulo</td>";
		echo "<td class='texto2' colspan='1' align='center'>Cantidad</td>";
		if ($tipo=='C')
		{
			echo "<td class='texto2' colspan='1' align='center'>Ajuste</td>";
			echo "<td class='texto2' colspan='1' align='center'>presentacion</td>";
			echo "<td class='texto2' colspan='1' align='center'>Faltante</td>";
		}
		else
		{
			echo "<td class='texto2' colspan='1' align='center'>&nbsp;</td>";
			echo "<td class='texto2' colspan='1' align='center'>&nbsp;</td>";
			echo "<td class='texto2' colspan='1' align='center'>&nbsp;</td>";
		}
		echo "<td class='texto2' colspan='1' align='center'>Presentaciones</td></tr>";

		for ($i=0;$i<count($inslis);$i++)
		{
			if (is_int($i/2))
			{
				$class='texto3';
			}
			else
			{
				$class='texto4';
			}
			echo "<input type='hidden' name='inslis[".$i."][cod]' value=".$inslis[$i]['cod']."><input type='hidden' name='inslis[".$i."][nom]' value=".$inslis[$i]['nom']."><input type='hidden' name='inslis[".$i."][gen]' value=".$inslis[$i]['gen'].">";
			if ($insumos[0]['lot']=='on' and $insumos[0]['cdo']=='off' and $tipo!='A')
			{
				echo "<tr><td class='".$class."' colspan='1' align='center'>".$inslis[$i]['cod']."-".$inslis[$i]['nom']."</td>";
				echo "<td class='".$class."' colspan='1' align='center'>".$inslis[$i]['can']." ".$inslis[$i]['pre']."</td>";
				if ($tipo=='C')
				{
					$exp=explode('-',$inslis[$i]['aju']);

					echo "<td class='".$class."' colspan='1' align='center'>".$exp[0]." ".$inslis[$i]['pre']."</td>";
					if (isset($exp[2]) and isset($exp[3]))
					{
						echo "<td class='".$class."' colspan='1' align='center'>".$exp[1]."-".$exp[2]."-".$exp[3]."</td>";
					}
					else
					{
						echo "<td class='".$class."' colspan='1' align='center'>".$exp[0]."-".$exp[2]."</td>";
					}
					echo "<td class='".$class."' colspan='1' align='center'>".$inslis[$i]['fal']."</td>";
				}
				else
				{
					echo "<td class='".$class."' colspan='1' align='center'>&nbsp;</td>";
					echo "<td class='".$class."' colspan='1' align='center'>&nbsp;</td>";
					echo "<td class='".$class."' colspan='1' align='center'>&nbsp;</td>";
				}

				echo "<td class='".$class."' colspan='1' align='center'><select name='inslis[".$i."][prese]' class='texto5'>";
				if ($presentaciones[$i][0]!='')
				{
					for ($j=0;$j<count($presentaciones[$i]);$j++)
					{
						echo "<option>".$presentaciones[$i][$j]."</option>";
					}
				}
				else
				{
					echo "<option ></option>";
				}
				echo "</select></td></tr>";

			}
			else
			{
				echo "<tr><td class='texto2' colspan='1' align='center'>&nbsp;</td>";
				echo "<td class='texto2' colspan='1' align='center'>&nbsp;</td>";
				echo "<td class='texto2' colspan='1' align='center'>&nbsp;</td>";
				echo "<td class='texto2' colspan='1' align='center'>&nbsp;</td>";
				echo "<td class='texto2' colspan='1' align='center'>&nbsp;</td>";
				echo "<td class='texto2' colspan='1' align='center'>&nbsp;</td></tr>";
			}
		}

		echo "</table></br>";
		echo "<table border=1 ALIGN=CENTER width=90%>";
		if ($tipo=='C')
		{

			echo "<tr><td colspan='6' class='titulo3' align='center'><b>INSUMOS DE PREPARACION ADICIONALES</b></td></tr>";
			echo "<td colspan='2' class='titulo3' align='center'><b>TIPO DE INSUMOS</b></td>";
			echo "<td colspan='3' class='titulo3' align='center'><b>PRESENTACION</b></td>";
			echo "<td colspan='1' class='titulo3' align='center'><b>SELECCIONAR</b></td></tr>";

			for ($i=0; $i<count($preparacion); $i++)
			{
				$tam=count($preparacion[$i])-1;
				echo "<tr><td class='texto1' rowspan:'".$tam."' colspan='2' align='center'>".$preparacion[$i]['nom'].": </td>";
				for ($j=0;$j<$tam;$j++)
				{

					echo "<td class='texto1' colspan='3' align='center'>".$preparacion[$i][$j]." </td>";
					echo "<td class='texto1' colspan='1' align='center'><input type='checkbox' name='escogidos[".$i."][".$j."]' class='texto3' ".$escogidos[$i][$j]."></td></tr><tr>";
					if ($j+1!=$tam)
					{
						echo "<td class='texto1' colspan='2' align='center'>&nbsp</td>";
					}
				}

			}
		}
	}


	echo "<input type='hidden' name='accion' value='0'></td>";
	echo "<input type='hidden' name='realizar' value='0'></td>";
	echo "<input type='hidden' name='eliminar' value='0'></td>";
	echo "</table></br></form>";
}
/*===========================================================================================================================================*/
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
	$wbasedato='cenpro';
	

	or die("No se ralizo Conexion");
	


	//pintarVersion(); //Escribe en el programa el autor y la version del Script.
	pintarTitulo();  //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts
	$bd='movhos';
	//invoco la funcion connectOdbc del inlcude de ana, para saber si unix responde, en caso contrario,
	//este programa no debe usarse
	//include_once("pda/tablas.php");
	include_once("movhos/fxValidacionArticulo.php");
	include_once("movhos/registro_tablas.php");
	include_once("movhos/otros.php");
	include_once("CENPRO/funciones.php");
	connectOdbc(&$conex_o, 'facturacion');

	if ($conex_o!=0)
	{
		//consulto los datos del usuario de la sesion
		$pos = strpos($user,"-");
		$wusuario = substr($user,$pos+1,strlen($user)); //extraigo el codigo del usuario

		//consulto los centros de costos que se administran con esta aplicacion
		//estos se cargan en un select llamado cco.
		if (isset($cco))
		{
			$ccos=consultarCentros($cco);
		}
		else
		{
			$ccos=consultarCentros('');
		}

		$preparacion=consultarPreparacion(&$escogidos);
		//dependiendo del tipo de accion a realizar (se manda al invocar el programa)

		//si tipo es A o B, es decir es una averia, se consultan los centros de costos a los cuales podria cargar la averia
		// como un centro de costos destino
		if ($tipo=='A' or $tipo=='B')
		{
			//se consultan los centros de costos de acuerdo a los parametros de busqueda ingresados por el usuario
			//fordes dice si se busca por codigo, nombre del centro de costos
			//pardes que debe buscarse
			//el resultado de centro de costos se guarda en la variable ccoDes
			if (isset($pardes) and $pardes!='')
			{
				$destinos=consultarDestinos($fordes, $pardes);
			}
			else
			{
				if (isset($ccoDes))
				{
					$destinos=consultarDestinos('todos', $ccoDes);
				}
				else
				{
					$destinos=consultarDestinos('todos', '');
				}
			}

			/*if ($ccos[0]==$destinos[0])
			{
			$tipo='A';
			}
			else
			{
			$tipo='B';
			}*/
			//se consulta el consecutivo para el movimiento de inventario
			If(!isset($crear) or $crear=='cargar')
			{
				$numtra=consultarConsecutivo($tipo, -1);
			}
			else
			{
				$numtra=consultarConsecutivo($tipo, 1);
			}
		}
		else
		{
			//si el tipo es un cargo a paciente se pregunta la historia del paciente
			//y se valida que esta historia exita.
			if ((isset($historia) and $historia!=''))
			{
				$val=validarHistoria($cco, $historia, &$ingreso, &$mensaje, &$nombre, &$habitacion);
				if ($val)
				{
					If(!isset($crear) or $crear=='cargar')
					{
						$numtra=consultarConsecutivo($tipo, -1);
					}
					else
					{
						$numtra=consultarConsecutivo($tipo, 1);
					}
				}
				else
				{
					pintarAlert1($mensaje);
					$numtra='';
					$historia='';
					$ingreso='';
					$nombre='';
					$habitacion='';
				}
			}
			else
			{
				$numtra='';
				$historia='';
				$ingreso='';
				$nombre='';
				$habitacion='';
			}
		}

		if (!isset($estado))
		{
			//para saber que mensaje desplegar al pintar el formulario
			$estado='inicio';
		}

		if (isset ($parcon) and $parcon!='')
		{
			if (!isset ($insfor))
			{
				$insfor='';
			}
			if (!isset ($parcon2))
			{
				$parcon2='';
			}
			if (!isset ($parcon3))
			{
				$parcon3='';
			}
			$consultas=BuscarTraslado($parcon, $parcon2,$parcon3, $insfor, $forcon);
			$consulta=$consultas[0];
		}


		//si esta setiado consulta se busca un cargo determinado
		if (isset($consulta) and $consulta!='')
		{
			$exp=explode('-',$consulta);
			$numtra=$exp[0].'-'.$exp[1];
			$exp2=explode('(',$consulta);
			$fecha=substr($exp2[1],0, (strlen($exp2[1])-1));
			consultarTraslado($exp[0], $exp[1], $fecha, &$tipo, &$ccos, &$destinos, &$historia, &$estado, &$cco, &$ingreso, &$insumos, &$lotes, &$unidades, &$inslis);
			if ($insumos[0]['lot']=='on' and $insumos[0]['cdo']=='off' and $tipo!='A')
			{
				for ($i=0;$i<count($inslis);$i++)
				{
					if ($tipo=='C')
					{
						$res=consultarMovimiento2(&$inslis[$i], $cco, $historia, $ingreso, $tipo, &$presentaciones[$i], $insumos[0]['cod'], $lotes[0], $exp[1],  $exp[0]);
					}
					else
					{
						$res=consultarMovimiento2(&$inslis[$i], $cco, $destinos[0], '', $tipo, &$presentaciones[$i], $insumos[0]['cod'], $lotes[0], $exp[1],  $exp[0]);
					}

					if (!$res)
					{
						pintarAlert1('No se encuentra un movimiento realizado con las carcteristicas ingresadas');
						$insumos='';
						unset($inslis);
						$inslis='';
					}
				}
			}

			if ($tipo=='C')
			{
				consultarEscogidos($cco, $historia, $ingreso, $tipo, $exp[1],  $exp[0], $preparacion, &$escogidos);
			}
		}

		if (!isset($forcon))
		{
			$forcon='';
		}
		if (!isset($consultas))
		{
			$consultas='';
		}

		//se pinta html de formulario para buscar un movimiento determinado
		pintarBusqueda($consultas,$forcon, $tipo);

		if (isset($parbus2) and $parbus2!='')
		{
			//se desetean las variable por si se ha cambiado el producto
			//no queden cargados los insumos que lo conforman

			if (isset($insumos))
			{
				unset($insumos);
			}

			if (isset($inslis))
			{
				unset($inslis);
			}
		}
		//si ya tenemos un insumo seleccionado, llenamos las variables de busqueda para volver a buscar el dato
		if (isset($insumo) and $insumo!='' and (!isset($accion) or $accion!=1) and (!isset($parbus2) or $parbus2=='') and (!isset($consulta) or $consulta==''))
		{
			$parbus2=explode('-',$insumo);
			$parbus2=$parbus2[0];
			$forbus2='Codigo';
		}

		if (isset($parbus2) and $parbus2!='')
		{
			//consultamos el insumo, el nombre y si es un insumo producto codificado o no codificado
			$insumos=consultarInsumos($parbus2, $forbus2);
			//si es un prducto
			if ($insumos[0]['lot']=='on')
			{
				if (!isset($lote))
				{
					$exp=explode('-', $parbus2);
				}
				else
				{
					$exp[1]=$lote;
				}

				if (!isset($crear))
				{
					$crear='cargar';
				}

				if($tipo=='C')
				{
					$nueva=$historia.'-'.$ingreso;
				}
				else
				{
					$nueva='';
				}

				if (isset($exp[1]))
				{
					$lotes=consultarLotes($insumos[0]['cod'], $ccos[0],$exp[1], $crear, $tipo, $nueva);
				}
				else
				{
					$lotes=consultarLotes($insumos[0]['cod'], $ccos[0], '', $crear, $tipo, $nueva);
				}
				$unidades='';
			}
			else
			{
				if ($forbus2=='rotulo')
				{
					$unidades=consultarUnidades($insumos[0]['cod'], $ccos[0], $parbus2);
				}
				else
				{
					$unidades=consultarUnidades($insumos[0]['cod'], $ccos[0], '');
				}
			}
		}

		if (isset($insumos) and $insumos[0]!=''  and (!isset($consulta) or $consulta==''))
		{
			if ($insumos[0]['lot']=='on' and $insumos[0]['cdo']=='off')
			{
				consultarProducto($insumos[0]['cod'], &$via, &$tfd, &$tfh, &$tvd, &$tvh, &$fecha, &$inslis, &$tippro, &$foto, &$neve, &$conpro, &$nompro, &$genpro, &$presentacion);
				for ($i=0;$i<count($inslis);$i++)
				{
					if (!isset($crear) or $crear=='cargar')
					{
						if ($tipo=='C')
						{
							consultarAjuste($inslis[$i], $cco, $historia, $ingreso);
						}
						if (isset ($inslis[$i]['prese']))
						{
							$presentaciones[$i]=consultarUnidades2($inslis[$i]['cod'], $cco, $inslis[$i]['prese']);
						}
						else
						{
							$presentaciones[$i]=consultarUnidades2($inslis[$i]['cod'], $cco, '');
						}
					}
					else
					{
						if ($tipo=='C')
						{
							$res=consultarMovimiento(&$inslis[$i], $cco, $historia, $ingreso, $tipo, &$presentaciones[$i], 1, $insumos[0]['cod'], $lotes[0]);
						}
						else if ($tipo=='B')
						{
							$res=consultarMovimiento(&$inslis[$i], $cco, $destinos[0], '', $tipo, &$presentaciones[$i], 1, $insumos[0]['cod'], $lotes[0]);
						}
						else
						{
							$res=true;
						}
						if (!$res)
						{
							pintarAlert1('No se encuentra un movimiento realizado con las carcteristicas ingresadas');
							$insumos='';
							unset($inslis);
							$inslis='';
						}

					}
				}

			}
			else
			{
				$inslis[0]['cod']=$insumos[0]['cod'];
				$inslis[0]['nom']=$insumos[0]['nom'];
				$inslis[0]['gen']=$insumos[0]['gen'];
				$inslis[0]['pre']=$insumos[0]['pre'];
				$inslis[0]['can']=1;
				$inslis[0]['lot']=$insumos[0]['lot'];
				$inslis[0]['est']=$insumos[0]['est'];
			}

			if (!isset($prese))
			{
				$prese='';
			}

			if (!isset($lote))
			{
				$lote='';
			}

		}
		else if ( !isset($consulta) or $consulta=='')
		{
			$insumos='';
		}


		if (!isset ($unidades))
		{
			$unidades='';
		}
		if (!isset ($presentaciones))
		{
			$presentaciones='';
		}

		if (!isset($lotes))
		{
			$lotes='';
		}
		if (!isset($inslis))
		{
			$inslis='';
		}

		if (isset($grabar) and $grabar=='1')
		{
			if (isset($numtra) and $numtra!='')
			{
				if (!isset($inslis) or $inslis[0]['cod']=='')
				{
					pintarAlert1('Debe ingresar al menos un articulo para realizar el traslado');
				}
				else
				{

					if (isset ($lote) and $lote!='')
					{
						$val=validarMatrix($insumos[0], $cco, $lote, $tipo);
					}
					else
					{
						$val=validarMatrix($insumos[0], $cco, $prese, $tipo);
					}
					//echo $val;
					//validciones de unix
					if ($tipo=='C' or $tipo=='B')
					{
						//validaciones de articulos en Unix
						for ($i=0; $i<count($inslis); $i++)
						{
							if ($insumos[0]['lot']=='on')
							{
								if ($insumos[0]['cdo']=='on')
								{
									$art['cod']=$insumos[0]['cod'];
									$art['can']=1;
								}
								else
								{
									$exp=explode('-',$inslis[$i]['prese']);
									$art['cod']=$exp[0];
									$cnv=consultarConversor($art['cod'], $cco);

									if ($tipo!='C')
									{
										$art['can']=ceil($inslis[$i]['can']/$cnv);
									}
									else
									{
										if ($inslis[$i]['fal']>0) //Hay que destapar una nueva presentacion
										{
											$art['can']=ceil($inslis[$i]['fal']/$cnv);
										}
										else
										{
											$pasa=1;
										}
									}
								}
							}
							else
							{
								$exp=explode('-',$prese);
								$art['cod']=$exp[0];
								$art['can']=1;
							}

							$exp=explode('-',$cco);
							$centro['cod']=$exp[0];
							$aprov=false;
							$art['neg']=false; //no maneja articulos especiales
							$centro['neg']=false;

							if (!isset($pasa))
							{
								$res=ArticuloExiste (&$art, &$error );
								if ($res)
								{
									$centro['neg']=true;
									$aprov=true;
									if ($tipo=='C')
									{
										if($crear=='cargar')
										{
											$tipTrans='C';
										}
										else
										{
											$tipTrans='D';
										}

										$res2=TarifaSaldo($art, $centro, $tipTrans, $aprov, &$error);
										if (!$res2)
										{
											$val=-5;
										}
									}
									else
									{
										$tipTrans='C';//el traslado se mandara C a la funcion
										$exp=explode('-',$ccoDes);
										$centro2['cod']=$exp[0];
										if ($crear=='cargar')
										{
											$centro2['neg']=true;
										}
										else
										{
											$centro2['neg']=false;
										}

										$centro['neg']=true;
										$res2=TarifaSaldo($art, $centro, $tipTrans, $aprov, &$error);
										if (!$res2)
										{
											$val=-5;
										}
										else
										{
											$res3=TarifaSaldo($art, $centro2, $tipTrans, $aprov, &$error);
											if (!$res3)
											{
												$val=-5;
											}
										}

									}

								}
								else
								{
									$val=-4;
								}

							}
							else
							{
								unset($pasa);
							}
						}
					}

					if ($crear=='cargar')
					{
						//validamos los articulos de presentacion
						if ($tipo=='C')
						{
							for ($i=0; $i<count($preparacion); $i++)
							{
								$tam=count($preparacion[$i])-1;
								for ($j=0; $j<$tam; $j++)
								{
									if ($escogidos[$i][$j]!='')
									{
										$exp=explode('-',$preparacion[$i][$j]);
										$art['cod']=$exp[0];
										$art['can']=1;

										$exp=explode('-',$cco);
										$centro['cod']=$exp[0];
										$art['neg']=false; //no maneja articulos especiales
										$res=ArticuloExiste (&$art, &$error );
										if ($res)
										{
											$centro['neg']=true;
											$aprov=true;
											$tipTrans='C';
											$res2=TarifaSaldo($art, $centro, $tipTrans, $aprov, &$error);
											if (!$res2)
											{
												$val=-5;
											}
										}
										else
										{
											$val=-4;
										}
									}
								}
							}
						}
						///fin validacion
						switch ($val)
						{
							case 2:
							if ($tipo!='C')
							{
								$exp=explode('-',$ccoDes);
								$cco2=$exp[0];
							}
							else
							{
								$cco2=$historia.'-'.$ingreso;
							}
							grabarEncabezadoSalidaMatrix(&$codigo, &$consecutivo, $cco, $wusuario, $cco2, $tipo);
							$numtra=$codigo.'-'.$consecutivo;
							if ($insumos[0]['lot']=='on')
							{
								$dato=$lote."-".$insumos[0]['cod'];
								grabarDetalleSalidaMatrix($insumos[0]['cod'], $codigo, $consecutivo, $wusuario, '', $dato, '', '');
								descontarArticuloMatrix($insumos[0]['cod'], $cco, $insumos[0]['lot'], $lote);
							}
							else
							{
								grabarDetalleSalidaMatrix($insumos[0]['cod'], $codigo, $consecutivo, $wusuario, $prese, '', '', '');
								descontarArticuloMatrix($insumos[0]['cod'], $cco, $insumos[0]['lot'], $prese);
							}
							if (isset($inslis) and is_array($inslis))
							{
								$exp=explode('-',$cco);
								grabarEncabezadoEntradaMatrix(&$codigo, &$consecutivo, $exp[0],  $cco2, $wusuario, 'F');

								$emp='01';
								$tipTrans='C';
								unset($cco);
								$cco['cod']=$exp[0];
								getCco(&$cco, $tipTrans, '01');

								for ($i=0; $i<count($inslis); $i++)
								{
									$grabar=1;
									if ($insumos[0]['lot']=='on')
									{
										if ($insumos[0]['cdo']=='on' or $tipo=='A')
										{
											$art['cod']=$insumos[0]['cod'];
											grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, '', $dato, '', '');
											$art['can']=1;
										}
										else
										{
											$ins=explode('-', $inslis[$i]['prese']);
											$art['cod']=$ins[0];

											if ($tipo!='C')
											{
												grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $inslis[$i]['prese'], $dato, '', '');
											}
											else
											{
												if ($inslis[$i]['fal']==$inslis[$i]['can']) //No hay ajuste de presentacion
												{
													grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $inslis[$i]['prese'], $dato, '', '');
													$cnv=consultarConversor($art['cod'], $cco['cod'].'-');
													$art['can']=ceil($inslis[$i]['fal']/$cnv);
												}
												else if ($inslis[$i]['fal']==0) //Todo se descuenta del ajuste, no se escoge presentacion nueva
												{
													$aju=explode('-', $inslis[$i]['aju']);
													$aju2=$aju[1].'-'.$aju[2].'-'.$aju[3];
													grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, '', $dato, $aju2, $inslis[$i]['can']);
													$grabar=0;
												}
												else //parte de saca de ajuste parte de nueva presentacion
												{
													$aju=explode('-', $inslis[$i]['aju']);
													$aju2=$aju[1].'-'.$aju[2].'-'.$aju[3];
													grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $inslis[$i]['prese'], $dato, $aju2, $inslis[$i]['can']-$inslis[$i]['fal']);
													$cnv=consultarConversor($art['cod'], $cco['cod'].'-');
													$art['can']=ceil($inslis[$i]['fal']/$cnv);
												}
												actualizarAjuste($inslis[$i]['fal'], $inslis[$i]['prese'], $inslis[$i]['aju'], $inslis[$i]['can'], $historia, $cco['cod'], $wusuario, $ingreso);
											}
										}
									}
									else
									{
										$exp=explode('-',$prese);
										$art['cod']=$exp[0];
										grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $prese, '', '', '');
										$art['can']=1;
									}

									if($tipo=='C' and $grabar==1)
									{
										$art['ini']=$insumos[0]['cod'];
										$art['ubi']='M';
										if (!isset($dronum))
										{
											$dronum='';
											$cns=0;
											$date=date('Y-m-d');
											$pac['his']=$historia;
											$pac['ing']=$ingreso;
											$aprov=true;
											$usu['codM']=$wusuario;
											Numeracion($pac, $cco['fap'],$tipTrans, $aprov, $cco, &$date, &$cns, &$dronum, &$drolin, true, $usu, &$error);

										}
										else
										{
											Numeracion($pac, $cco['fap'],$tipTrans, $aprov, $cco, &$date, &$cns, &$dronum, &$drolin, false, $usu, &$error);
										}
										registrarItdro($dronum, $drolin, $cco['fap'], date('Y-m-d'), $cco, $pac, $art, &$error);
										registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, &$error);
									}
									else if ($tipo=='B')
									{
										if ($i==0)
										{
											connectOdbc(&$conex_o, 'inventarios');
											grabarEncabezadoUnix($cco['cod'], $centro2['cod'], &$concepto2, &$fuente2, &$documento2);
										}
										$present=consultarPresentacion($art['cod']);
										grabarDetalleUnix($centro['cod'], $centro2['cod'], $art['cod'], $art['can'], $present, $concepto2, $fuente2,  $documento2, $i);


									}

								}
								//grabamos los insumos de preparacion
								if ($tipo=='C')
								{
									for ($i=0; $i<count($escogidos); $i++)
									{
										$tam=count($preparacion[$i])-1;
										for ($j=0; $j<$tam; $j++)
										{
											if ($escogidos[$i][$j]!='')
											{
												$exp=explode('-',$preparacion[$i][$j]);
												$art['cod']=$exp[0];
												$art['can']=$exp[2];
												grabarDetalleSalidaMatrix('', $codigo, $consecutivo, $wusuario, '', '', $exp[0], $exp[2]);
												$art['ini']=$insumos[0]['cod'];
												$art['ubi']='M';
												Numeracion($pac, $cco['fap'],$tipTrans, $aprov, $cco, &$date, &$cns, &$dronum, &$drolin, false, $usu, &$error);
												registrarItdro($dronum, $drolin, $cco['fap'], date('Y-m-d'), $cco, $pac, $art, &$error);
												registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, &$error);
											}
										}
									}
								}
								//fin de grabacion de insumos de preparacion
								$art['cod']=$insumos[0]['cod'];
								$art['can']=1;
								$aprov=true;
								if($tipo=='C')
								{
									$cco['apl']=true;
									$pac['his']=$historia;
									$pac['ing']=$ingreso;
									$usu['codM']=$wusuario;

									registrarSaldos($pac, $art, $cco, $aprov, date('Y-m-d'), $usu, '+', false, $tipTrans, &$error);
								}
							}
							$estado='creado';
							$numcan=$numcan-1;
							break;

							case 1:
							if ($insumos[0]['lot']=='on')
							{
								pintarAlert1('Sin existencias del lote seleccionado');
							}
							else
							{
								pintarAlert1('Sin existencias de la presentacion seleccionada');
							}
							break;

							case 0:
							pintarAlert1('Sin existencias del producto');
							break;

							case -1:
							pintarAlert1('Verifique la existencia del articulo, en los maestros de la Central ');
							break;

							case -2:
							pintarAlert1('Se ha vencido la fecha del lote seleccionado');
							break;

							case -3:
							if ($insumo[0]['lot']=='on')
							{
								pintarAlert1('El lote seleccionado no ha sido creado');
							}
							else
							{
								pintarAlert1('La presentacion seleccionada no existe en el maestro');
							}
							break;

							case -5:
							pintarAlert1('Articulos sin tarifa en Unix');
							break;

							case -4:
							pintarAlert1('El articulo no existe en Unix');
							break;
						}
						//echo 'me meto mal';

					}
					else
					{
						if ($tipo=='C')
						{
							$pro['cod']=$insumos[0]['cod'];
							$pro['can']=1;
							$aprov=true;
							$pac['his']=$historia;
							$pac['ing']=$ingreso;
							//esto debe quitarse despues
							$exp=explode('-',$cco);
							$cen['cod']=$exp[0];
							$cen['apl']=true;
							$res=validacionDevolucion($cen, $pac, $pro, $aprov, &$error);
							if(!$res)
							{
								$val=-6;
							}
						}

						switch ($val)
						{
							case -1:
							//echo 'hola';
							pintarAlert1('Verifique la existencia del articulo, en los maestros de la Central ');
							break;

							case -3:
							if ($insumo[0]['lot']=='on')
							{
								pintarAlert1('El lote seleccionado no ha sido creado');
							}
							else
							{
								pintarAlert1('La presentacion seleccionada no existe en el maestro');
							}
							break;

							case -4:
							pintarAlert1('El articulo no existe en Unix');
							break;

							case -6:
							pintarAlert1('NO EXITE PRODUCTO SIN APLICAR PARA EL PACIENTE');
							break;

							default:

							if ($tipo!='C')
							{
								$exp=explode('-',$ccoDes);
								$cco2=$exp[0];
							}
							else
							{
								$cco2=$historia.'-'.$ingreso;
							}

							$exp=explode('-',$cco);
							grabarEncabezadoEntradaMatrix(&$codigo, &$consecutivo, $cco2, $exp[0], $wusuario, $tipo);
							$numtra=$codigo.'-'.$consecutivo;
							if ($insumos[0]['lot']=='on')
							{
								$dato=$lote."-".$insumos[0]['cod'];
								grabarDetalleEntradaMatrix($insumos[0]['cod'], $codigo, $consecutivo, $wusuario, '', $dato, '', '');
								sumarArticuloMatrix($insumos[0]['cod'], $cco, $insumos[0]['lot'], $lote);
								anularCargo($cco, $cco2,$insumos[0]['cod'],$lote, $insumos[0]['lot']);
							}
							else
							{
								grabarDetalleEntradaMatrix($insumos[0]['cod'], $codigo, $consecutivo, $wusuario, $prese, '', '', '');
								sumarArticuloMatrix($insumos[0]['cod'], $cco, $insumos[0]['lot'], $prese);
								anularCargo($cco, $cco2,$insumos[0]['cod'],$prese, $insumos[0]['lot']);
							}

							if (isset($inslis) and is_array($inslis))
							{
								grabarEncabezadoSalidaMatrix(&$codigo, &$consecutivo, $cco,  $wusuario, $cco2,  'F');

								$emp='01';
								$tipTrans='D';
								unset($cco);
								$cco['cod']=$exp[0];
								getCco(&$cco, $tipTrans, '01');

								for ($i=0; $i<count($inslis); $i++)
								{
									$grabar=1;

									if ($insumos[0]['lot']=='on')
									{
										if ($insumos[0]['cdo']=='on' or $tipo=='A')
										{
											$art['cod']=$insumos[0]['cod'];
											grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, '', $dato, '', '');
											$art['can']=1;
										}
										else
										{
											$ins=explode('-', $inslis[$i]['prese']);
											$art['cod']=$ins[0];

											if ($tipo!='C')
											{
												grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $inslis[$i]['prese'], $dato, '', '');
											}
											else
											{
												if ($inslis[$i]['fal']==$inslis[$i]['can']) //No hay ajuste de presentacion
												{
													grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $inslis[$i]['prese'], $dato, '', '');
													$cnv=consultarConversor($art['cod'], $cco['cod'].'-');
													$art['can']=ceil($inslis[$i]['fal']/$cnv);
													$presentacion2=$inslis[$i]['aju'];
													//echo 'hola1';
												}
												else if ($inslis[$i]['fal']==0) //Todo se descuenta del ajuste, no se escoge presentacion nueva
												{
													$exp=explode('-',$inslis[$i]['aju']);
													$presentacion2=$exp[1].'-'.$exp[2].'-'.$exp[3];
													grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, '', $dato, $presentacion2, $inslis[$i]['can']);
													$grabar=0;
													//echo 'hola2';
												}
												else //parte de saca de ajuste parte de nueva presentacion
												{
													$exp=explode('-',$inslis[$i]['aju']);
													$presentacion2=$exp[1].'-'.$exp[2].'-'.$exp[3];
													grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $inslis[$i]['prese'], $dato, $presentacion2, $inslis[$i]['can']-$inslis[$i]['fal']);
													$cnv=consultarConversor($art['cod'], $cco['cod'].'-');
													$art['can']=ceil($inslis[$i]['fal']/$cnv);
													//echo 'hola3';
												}
												devolverAjuste($inslis[$i]['fal'], $inslis[$i]['prese'], $presentacion2, $inslis[$i]['can'], $historia, $cco['cod'], $wusuario, $ingreso);
											}
										}
									}
									else
									{
										$exp=explode('-',$prese);
										$art['cod']=$exp[0];
										grabarDetalleSalidaMatrix($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $prese, '', '', '');
										$art['can']=1;
									}

									if($tipo=='C' and $grabar==1)
									{
										$art['ini']=$insumos[0]['cod'];
										$art['ubi']='M';
										if (!isset($dronum))
										{
											$cns=0;
											$dronum='';
											$date=date('Y-m-d');
											$pac['his']=$historia;
											$pac['ing']=$ingreso;
											$aprov=true;
											$usu['codM']=$wusuario;
											Numeracion($pac, $cco['fap'],$tipTrans, $aprov, $cco, &$date, $cns, &$dronum, &$drolin, true, $usu, &$error);
										}
										else
										{
											Numeracion($pac, $cco['fap'],$tipTrans, $aprov, $cco, &$date, $cns, &$dronum, &$drolin, false, $usu, &$error);
										}
										registrarItdro($dronum, $drolin, $cco['fap'], date('Y-m-d'), $cco, $pac, $art, &$error);
										registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, &$error);
									}
									else if ($tipo=='B')
									{
										if ($i==0)
										{
											connectOdbc(&$conex_o, 'inventarios');
											grabarEncabezadoUnix($centro2['cod'], $cco['cod'], &$concepto2, &$fuente2, &$documento2);
										}
										$present=consultarPresentacion($art['cod']);
										grabarDetalleUnix($centro2['cod'], $centro['cod'], $art['cod'], $art['can'], $present, $concepto2, $fuente2,  $documento2, $i);
									}

								}
								$art['cod']=$insumos[0]['cod'];
								$art['can']=1;
								$aprov=true;
								if($tipo=='C')
								{
									$cco['apl']=true;
									$pac['his']=$historia;
									$pac['ing']=$ingreso;
									$usu['codM']=$wusuario;
									registrarSaldos($pac, $art, $cco, $aprov, date('Y-m-d'), $usu, '+', false, $tipTrans, &$error);
								}
							}
							$estado='devuelto';
							$numcan=$numcan-1;
							break;
						}
					}
				}
			}

		}

		if (!isset ($fecha))
		{
			$fecha=date('Y-m-d');
		}

		if (!isset($crear))
		{
			$crear='cargar';
		}

		if(!isset($numcan))
		{
			$numcan='';
		}

		if ($tipo!='C')
		{
			pintarFormulario($estado, $ccos, $numtra, $tipo, '', $destinos, $fecha, '', '', '', $crear, '');
		}
		else
		{
			pintarFormulario($estado, $ccos, $numtra, $tipo, $historia, '', $fecha, $ingreso, $nombre, $habitacion, $crear, $numcan);
		}

		if ((isset ($historia) and $historia!='') or $tipo!='C')
		{
			pintarInsumos($insumos,$inslis, $unidades, $lotes, $presentaciones, $tipo, $preparacion, $escogidos, $numcan);
		}
	}
	else
	{
		pintarAlert2('EN ESTE MOMENTO NO ES POSIBLE CONECTARSE CON UNIX PARA REALIZAR EL CARGO, POR FAVOR INGRESE MAS TARDE');
	}
}
/*===========================================================================================================================================*/

?>


</body >
</html >
