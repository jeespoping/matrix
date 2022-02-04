
<head>
  	<title>MATRIX  Medio Magentico para Susualud</title>
</head>

<body onload=ira()>

<script type="text/javascript">
function enter()
{
	document.forms.mmagsusalud.submit();
}
</script>

<?php
include_once("conex.php");

/* **********************************************************
   *     PROGRAMA PARA LA GENERACION DE MEDIO MAGNETICO     *
   *                   DE SUSALUD E.P.S                     *
   **********************************************************/

//==================================================================================================================================
//PROGRAMA                   : Mmag_susalud.php
//AUTOR                      : Juan David Jaramillo R.
  $wautor="Juan D. Jaramillo R.";
//FECHA CREACION             : Junio 2 de 2006
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(2017-01-17)";
//DESCRIPCION
//====================================================================================================================================\\
//Este programa se hace con el objetivo de generar el medio magnetico que se debe enviar a susalud, el cual generara un archivo plano \\
//conformado por un registro de control, un encabezado y un detalle.   Tambien generara un reporte con el detalle de la informaacion  \\
//contenida en el plano.                  																							  \\
//====================================================================================================================================\\


//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES
//2017-01-17: camilo zapata
//            redefinición de programa para construcción de nuevo archivo de sura.                                                                                                                   \\
//========================================================================================================================================\\
//2012-11-30: Jonatan Lopez
//            Evalua que conceptos hacen falta en el listado de factuas seleccionadas.  (linea 1026)                                                                                                                    \\
//========================================================================================================================================\\
//2012-08-23: Camilo Zapata: se modificó el programa para que cuando un usuario autorizado no tenga carpeta asociada en la cual se guarden\\
// 							 los archivos planos el programa la cree. (linea 1158)
//________________________________________________________________________________________________________________________________________\\
//2008-09-24: Se cambio el campo de valor total de la factura para que mostrara el valor total + el valor del coopago    				  \\
//			  Se cambio la manera de imprimir los paquetes para que no imprimiera cada procedimiento sino el paquete completo en una      \\
//      	  sola linea.                                                                                                                 \\
//________________________________________________________________________________________________________________________________________\\
//2007-04-24: Se agrego un cambio para que muestre los conceptos que no han sido relacionados 											  \\
//			  en la tabla clisur_000119(relacion conceptos susalud)                  													  \\
//________________________________________________________________________________________________________________________________________\\
//2007-11-14: Se modifico el programa para que pusiera el valor del copago y la cuota moderadora cuando tenian y para cuando es           \\
//            susalud eps que colocara el codigo cups.                                                                                    \\
//________________________________________________________________________________________________________________________________________\\

$wnitips="890939026";
$wnitdip="1";

$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
$color="#999999";

// Variables Registro de Control
$wnumreg=0;
$wcodsuc="";
$wtipdoc="NI";

// Variables Registro de Cabecera
$wplan=0;
$wfecfac="";
$wletfac="";
$wvtofac=0;
$wpercar=0;
$wcuomod=0;
$wcopago=0;
$wtiquet=0;
$wdescto=0;
$wconret=0;

// Variables Registro de Detalle
$wnumaut=0;
$wtipid="";
$wnumid=0;
$wfecate="";
$wtipate=0;
$wcodate=0;
$wcantre=0;
$wcodsat=0;
$wcansat=0;
$wvlrate=0;
$wcoddia=0;

session_start();
if (!isset($user))
{
	if(!isset($_SESSION['user']))
		session_register("user");
}

if(!isset($_SESSION['user']))
	echo "Error, Usuario NO Registrado";
else
{
	echo "<form name='mmagsusalud' action='Mmag_susalud.php' method=post>";




	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";

	$pos = strpos($user,"-");
    $wusuario = substr($user,$pos+1,strlen($user));

    $wfecha=date("Y-m-d");
    $hora = (string)date("H:i:s");

    function validar3($chain)
	{
		// Funcion que permite validar la estructura de una fecha
		$fecha="/^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$/";
		if(preg_match($fecha,$chain,$occur))
		{
			if($occur[2] < 0 or $occur[2] > 12)
				return false;
			if(($occur[3] < 0 or $occur[3] > 31) or
		  	($occur[2] == 4 and  $occur[3] > 30) or
		  	($occur[2] == 6 and  $occur[3] > 30) or
		  	($occur[2] == 9 and  $occur[3] > 30) or
		  	($occur[2] == 11 and $occur[3] > 30) or
		  	($occur[2] == 2 and  $occur[3] > 29 and bisiesto($occur[1])) or
		  	($occur[2] == 2 and  $occur[3] > 28 and !bisiesto($occur[1])))
				return false;
			return true;
		}
		else
			return false;
	}


	function add_ceros($numero,$ceros)
	{
		$order_diez = explode(".",$numero);
		$dif_diez = $ceros - strlen($order_diez[0]);

		for($m=0;$m<$dif_diez;$m++)
		{
        	@$insertar_ceros .= 0;
		}
		if(isset($insert_ceros))
			return $insertar_ceros .= $numero;
		else
		{
			@$insertar_ceros .= "";
			return $insertar_ceros .= $numero;
		}
	}

	function ffec_dmy($fecha)
	{
		$yyyy=intval(substr($fecha,0,4));
		$mm  =intval(substr($fecha,5,2));
		$mm  =add_ceros($mm,2);
	 	$dd  =intval(substr($fecha,8,2));
	 	//$dd  =add_ceros($dd,2);
		$fecha=$dd."/".$mm."/".$yyyy;

		return $fecha;
	}

    echo "<center><table border=0>";
	echo "<tr><td colspan= 8 align=center><IMG width=300 height=100 SRC='/matrix/images/medical/Citas/logo_citascs.png'></td>";
	echo "<tr><td><br></td></tr>";
    echo "<tr><td colspan= 8 align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>GENERACION MEDIO MAGNETICO PARA SUSALUD</b></font></td></tr>";

    // Inicio de captura de datos en formulario

    echo "<tr><td bgcolor=#cccccc>Codigo:</td>";
    if (!isset($wcodigo))
	{
		echo "<td colspan=8 align=left bgcolor=#cccccc><select name='wcodigo' onchange='enter()' ondblclick='enter()'>";

        $query= "SELECT empcod, empnom ".
		        "  FROM ".$empresa."_000024 ".
		   		" WHERE empnit = '800088702'
		   		  ORDER BY empnom";

		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    	$num = mysql_num_rows($err);

    	echo "<option></option>";
    	for ($i=1;$i<=$num;$i++)
        {
            $row = mysql_fetch_array($err);
            echo "<option>".$row[0]."-".$row[1]."</option>";
        }
        echo "</select></td>";
    }
    else
    {
    	echo "<td colspan=8 align=left bgcolor=#cccccc><select name='wcodigo' onchange='enter()' ondblclick='enter()'>";
        echo "<option>".$wcodigo."</option>";

		$query= "SELECT empcod, empnom ".
		        "  FROM ".$empresa."_000024 ".
		   		" WHERE empnit = '800088702' ".
		   		"   AND empcod !=  (mid('".$wcodigo."',1,instr('".$wcodigo."','-')-1)) ".
		   		" ORDER BY empnom";

        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);

        for ($i=1;$i<=$num;$i++)
        {
            $row = mysql_fetch_array($err);
            echo "<option>".$row[0]."-".$row[1]."</option>";
		}
        echo "</select></td>";
    }
	echo "</tr>";

	if (isset($wcodigo))
	{
		$wcodemp=explode("-",$wcodigo);
		$wcodigo=$wcodemp[0];

		$query= "SELECT emptem ".
	        	"  FROM ".$empresa."_000024 ".
	   			" WHERE empcod = '".$wcodigo."'";

		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    	$num = mysql_num_rows($err);

    	if($num > 0)
		{
			$row = mysql_fetch_array($err);
			$wtemp=explode("-",$row[0]);

			if($wtemp[0]=="10")
				$wplan=0;
			if($wtemp[0]=="11")
				$wplan=1;

            $wplan = 0;
		}
	}


	//////////////////////////////////////// Sucursal IPS
    echo "<tr>";
    echo "<td colspan=1 bgcolor=#cccccc>Sucursal IPS:<br><font size=2>(1)Med / (2)Cali / (3)Bog / (4)Barranq</font></td>";
    if (!isset($wips))
    {
    	$wips="1";
   		$wdesips="MEDELLIN";

   		echo "<td bgcolor=#cccccc><input type='TEXT' name='wips' value='".$wips."' size=2 maxlength=1 onchange='enter()'></td>";
    	echo "<td colspan=3 bgcolor=#cccccc><font size=3><b>".$wdesips."</b></font></td>";
    }
    else
    {
    	echo "<td bgcolor=#cccccc><input type='TEXT' name='wips' value='".$wips."' size=2 maxlength=1 onchange='enter()'></td>";

    	switch($wips)
		{
			case "1":
			$wdesips= "MEDELLIN";
			break;
			case "2":
			$wdesips= "CALI";
			break;
			case "3":
			$wdesips= "BOGOTA";
			break;
			case "4":
			$wdesips= "BARRANQUILLA";
			break;
			default:
			$wdesips= "NO VALIDO";
		}
		echo "<td colspan=3 bgcolor=#cccccc><font size=3><b>".$wdesips."</b></font></td>";
    }

	echo "<td bgcolor=#cccccc>Tipo de Documento:</td>";
	echo "<td colspan=2 bgcolor=#cccccc>Nit IPS: ".$wnitips."-".$wnitdip."</td>";
	echo "</tr>";

	echo "<tr>";
	if(!isset($wfueini))
		$wfueini="";
	echo "<td bgcolor=#cccccc>Fuente Inicial:</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='wfueini' value='".$wfueini."' size=2 maxlength=2></td>";

	if(!isset($wfuefin))
		$wfuefin="";
	echo "<td bgcolor=#cccccc>Final:</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='wfuefin' value='".$wfuefin."' size=2 maxlength=2></td>";

	if(!isset($wdocini))
		$wdocini="";
	echo "<td bgcolor=#cccccc>Documento Inicial:</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='wdocini' value='".$wdocini."' size=18 maxlength=10></td>";

	if(!isset($wdocfin))
		$wdocfin="";
	echo "<td bgcolor=#cccccc>Final:</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='wdocfin' value='".$wdocfin."' size=18 maxlength=10></td>";
	echo "</tr>";

	echo "<tr>";
	if(!isset($wffacin))
		$wffacin= $wfecha;
	echo "<td bgcolor=#cccccc>Fecha de Factura Inicial <font size=2>&nbsp (AAAA-MM-DD):</font></td>";
	echo "<td colspan=4 bgcolor=#cccccc><input type='TEXT' name='wffacin' value='".$wffacin."' size=30 maxlength=10 onchange='enter'()></td>";

	if(!isset($wffacfi))
		$wffacfi= $wfecha;
	echo "<td bgcolor=#cccccc>Final:</td>";
	echo "<td colspan=2 bgcolor=#cccccc><input type='TEXT' name='wffacfi' value='".$wffacfi."' size=30 maxlength=10 onchange='enter'()></td>";
	echo "</tr>";

	if(!isset($wnumenv))
		$wnumenv=0;
	echo "<tr><td bgcolor=#cccccc>Numero del Envio:</td>";
	echo "<td colspan=7 bgcolor=#cccccc><input type='TEXT' name='wnumenv' value='".$wnumenv."' size=35 maxlength=12></td></tr>";

	echo "<tr>";
	echo "<td bgcolor=#cccccc>Desea Generar el Medio Magnetico en Disco:</td>";
    echo "<td colspan=3 bgcolor=#cccccc align=left><b></b><select name='wgenmmg' onchange='enter()' ondblclick='enter()'>";
    if (isset($wgenmmg))
    {
    	if($wgenmmg== "SI")
    	{	echo "<option selected>SI</option>";
    		echo "<option>NO</option>";
    	}
    	else
    	{
    		echo "<option>SI</option>";
    		echo "<option selected>NO</option>";
    	}
    }
    else
    {
    	echo "<option>SI</option>";
    	echo "<option selected>NO</option>";
    }
   	echo "</select></td>";

	if(!isset($wfecenv))
		$wfecenv=$wfecha;
	echo "<td bgcolor=#cccccc>Fecha de Envio:</td>";
	echo "<td colspan=3 bgcolor=#cccccc><input type='TEXT' name='wfecenv' value='".$wfecenv."' size=18 maxlength=10 onchange='enter()'></td>";
	echo "</tr>";

	if ((!isset($wcon)) || ($wcon == 1))
	{
		$wnummmg=0;
		echo "<tr><td bgcolor=#cccccc>Consecutivo del Medio Magnetico:</td>";
		echo "<td colspan=7 bgcolor=#cccccc>".$wnummmg."</td>";
	}
	else
	{
		if ($wcon == 2)
		{
			if(!isset($wnummmg))
			$wnummmg=0;
			echo "<tr><td bgcolor=#cccccc>Consecutivo del Medio Magnetico:</td>";
			echo "<td colspan=7 bgcolor=#cccccc><input type='TEXT' name='wnummmg' value='".$wnummmg."' size=35 maxlength=12></td></tr>";
		}
		else
		{
			if(!isset($wnummmg))
			$wnummmg=0;
			echo "<tr>";
			echo "<td bgcolor=#cccccc>Consecutivo del Medio Magnetico:</td>";
			echo "<td colspan=3 bgcolor=#cccccc><input type='TEXT' name='wnummmg' value='".$wnummmg."' size=35 maxlength=12></td>";

			echo "<td bgcolor=#cccccc>Desea Anular el Medio Magnetico:</td>";
    		echo "<td colspan=3 bgcolor=#cccccc align=left><b></b><select name='wanummg' onchange='enter()' ondblclick='enter()'>";
    		if (isset($wanummg))
    		{
    			if($wanummg== "SI")
    			{	echo "<option selected>SI</option>";
    				echo "<option>NO</option>";
    			}
    			else
    			{
    				echo "<option>SI</option>";
    				echo "<option selected>NO</option>";
    			}
    		}
    		else
    		{
    			echo "<option>SI</option>";
    			echo "<option selected>NO</option>";
    		}
   			echo "</select></td>";
   			echo "</tr>";
		}
	}

	if(!isset($wcon))
	{
		echo "<tr><td bgcolor=".$wcf2." align=center colspan=8><font text color=".$wclfa." size=3>
				<input type='radio' name='wcon' value=1><b>Grabar	 &nbsp&nbsp&nbsp
        		<input type='radio' name='wcon' value=2><b>Consultar &nbsp&nbsp&nbsp
				<input type='radio' name='wcon' value=3><b>Anular	 &nbsp&nbsp&nbsp</font></td></tr>";
	}
	else
	{
		if ($wcon==1)
		{
			$chk1='checked';
			$chk2='';
			$chk3='';
		}
		else
		{
			if ($wcon==2)
			{
				$chk2="checked";
				$chk1='';
				$chk3='';
			}
			else
			{
				$chk3="checked";
				$chk1='';
				$chk2='';
			}
		}
		echo "<tr><td bgcolor=".$wcf2." align=center colspan=8><font text color=".$wclfa." size=3>
				<input type='radio' name='wcon' value=1 $chk1><b>Grabar		&nbsp&nbsp&nbsp
        		<input type='radio' name='wcon' value=2 $chk2><b>Consultar  &nbsp&nbsp&nbsp
				<input type='radio' name='wcon' value=3 $chk3><b>Anular 	&nbsp&nbsp&nbsp</font></td></tr>";
	}
	echo "<tr><td align=center bgcolor=#cccccc colspan=8><input type='submit' value='OK'></td></tr>";
	echo "</table>";

	if(!validar3($wffacin))
	{
		?>
			<script>
			alert ("Ingrese o Verifique el Formato de la Fecha Inicial");
			function ira(){document.mmagsusalud.wffacin.focus();}
			</script>
		<?php
	}

	if(!validar3($wffacfi))
	{
		?>
			<script>
			alert ("Ingrese o Verifique el Formato de la Fecha Final");
			function ira(){document.mmagsusalud.wffacin.focus();}
			</script>
		<?php
	}

	if(!validar3($wfecenv))
	{
		?>
			<script>
			alert ("Ingrese o Verifique el Formato de la Fecha de Envio");
			function ira(){document.mmagsusalud.wfecenv.focus();}
			</script>
		<?php
	}

	if(isset($wcon))
	{
		if(!isset($wcodigo) or ($wcodigo == ""))
		{
		?>
			<script>
			alert ("Debe Ingresar un Codigo de Empresa Valido");
			function ira(){document.mmagsusalud.wcodigo.focus();}
			</script>
		<?php
		}

		if(($wips < 1) or ($wips > 4))
		{
		?>
			<script>
			alert ("Debe Ingresar una Surcursal de Ips Valida");
			function ira(){document.mmagsusalud.wips.focus();}
			</script>
		<?php
		}

		if(!isset($wfueini) or ($wfueini == "") or
		!isset($wfuefin) or ($wfuefin == ""))
		{
			$wfueini="00";
			$wfuefin="99";
		}

		if(!isset($wdocini) or ($wdocini == "") or
		!isset($wdocfin) or ($wdocfin == ""))
		{
			$wdocini="AAAAAAA";
			$wdocfin="ZZZZZZZ";
		}

		if(!isset($wffacin) or !isset($wffacfi) or
		($wffacin == "") or ($wffacfi == ""))
		{
			$wffacin="1980-01-01";
			$wffacfi=$wfecha;
		}
	}

    $query = " SELECT empcod
                 FROM root_000050
                WHERE empbda='$empresa'";
    $rs    = mysql_query($query,$conex);
    $row   = mysql_fetch_array( $rs );
    $wemp_pmla = $row['empcod'];

    $query = " SELECT empnit
                 FROM root_000050
                WHERE empcod='$wemp_pmla'";
    $rs    = mysql_query($query,$conex);
    $row   = mysql_fetch_array( $rs );

    $wnitipf = $row['empnit'];

	if (($wips >= 1) && ($wips <= 4))
	{
	if (isset($wcon) and ($wcon == 1)) // Si es Grabacion de Medio Magnetico
	{
		if(!isset($wnumenv) or ($wnumenv == "") or ($wnumenv == 0))
		{
			$query = "SELECT fenffa, fenfac, fenfec, fencod, fentip, fenval, fendes, fenhis, fening ".   // reemplazar por fenres
	 	    	     "  FROM ".$empresa."_000018 ".
		        	 " WHERE fencod =  '".$wcodigo."'".
		        	 "   AND fenffa >= '".$wfueini."'"." AND fenffa <= '".$wfuefin."'".
		        	 "   AND fenfac >= '".$wdocini."'"." AND fenfac <= '".$wdocfin."'".
		        	 "   AND fenfec >= '".$wffacin."'"." AND fenfec <= '".$wffacfi."'".
		        	 "   AND fenest= 'on' ";
		}
		else
		{
		    $query = "SELECT fenffa, fenfac, fenfec, fencod, fentip, fenval, fendes, fenhis, fening ".	 // reemplazar por fenres
	 	             "  FROM ".$empresa."_000020, ".$empresa."_000021, ".$empresa."_000018 ".
		         	 " WHERE rencod  = '".$wcodigo."'".
		         	 "   AND renfue  = '80'".
		             "   AND rennum  = '".$wnumenv."'".
		             "   AND renest  = 'on'".
		             "   AND rdeest = 'on'".
	                 "   AND rdefue = renfue".
	                 "   AND rdenum = rennum".
	                 "   AND fenffa = rdeffa".
	                 "   AND fenfac = rdefac";
	    }

		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    	$num = mysql_num_rows($err);

		if ($num > 0)
	    {
	    	if (isset($todas) )
				$chk='checked';
			else $chk="";

	       	echo "<table border=0 align=center width=60%>";
	        echo "<br>";
	      	//echo "<tr><td colspan=13>&nbsp</td></tr>";
	  	    echo "<tr><td align=center colspan=7 bgcolor=FFCC66><font size=5><b>D E T A L L E &nbsp&nbsp D E &nbsp&nbsp F A C T U R A S</b></font></td></tr>";
	  	    echo "<tr><td bgcolor=#ffcc66 colspan=7 align=right><input type='checkbox' name='todas' onclick='enter()'>Seleccionar Todas</td></tr>";
	  	    echo "<tr>";
		    echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Fuente</font></th>";
		    echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Factura</font></th>";
		    echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Fecha</font></th>";
			echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Codigo</font></th>";
			echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Responsable</font></th>";
			echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Gen</font></th>";
			echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Est</th></font>";
			echo "</tr>";

			$wtotfac = 0;	// Almacena numero de facturas posibles a enviar

			// Ciclo para recorrer todas las facturas encotradas
			for ($i=1;$i<=$num;$i++)
	        {
	        	$valido ='off';			// Determina si la factura es valida para enviar en el medio magnetico a realizar

	        	$row = mysql_fetch_array($err);
	        	$wfuefac=$row[0];
	        	$wdocfac=$row[1];

	        	$query = "SELECT magssaedo  ".
	 	       	     	"  FROM ".$empresa."_000067 ".
		      	     	" WHERE magssafue = '".$wfuefac."'".
		           	 	"   AND magssadoc = '".$wdocfac."'".
		           	 	"   AND magssaedo = 'on'";

				$res1 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    			$num1 = mysql_num_rows($res1);

    			if ($num1 > 0)
    	 		{
	    			$row1 = mysql_fetch_array($res1);
	    			$valido= 'off';
    	 		}
	         	else
		        	$valido= 'on';

	         	if ($valido == 'on')   // Factura es valida para enviar
	         	{
		        	if ($i%2==0)
	            	   	$wcolor="#cccccc";
	            	else
		               	$wcolor="#999999";

		            if (!isset($todas) )
					{
						if (isset($windgen[$wtotfac]))
							$chk='checked';
						else
							$chk="";
					}
	                echo "<tr>";
				    echo "<td align=right  bgcolor='".$wcolor."'><font size=2><b>".$row[0]."</b></font></td>";                                  //Fuente de la factura
				    echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$row[1]."</font></td>";                                         //Documento de la factura
			    	echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$row[2]."</font></td>";                                         //Fecha
			     	echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$row[3]."</font></td>";                                         //Codigo del Responable
			     	echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$row[4]."</font></td>";                                         //Descrip. del Responsable
					echo "<td align=center bgcolor='".$wcolor."'><font size=2><input type='Checkbox' name='windgen[".$wtotfac."]' $chk></font></td>"; //Indicador de Generar
					echo "<td align=left   bgcolor='".$wcolor."'><font size=2></font></td>";													//Estado de la Factura
	             	echo "</tr>";

			     	$facturas[$wtotfac]['fuente'] =$row[0];
					$facturas[$wtotfac]['factura']=$row[1];
					$facturas[$wtotfac]['numconp']=0;
					$facturas[$wtotfac]['fecfac']=$row[2];
					$facturas[$wtotfac]['valfac']=$row[5];
					$facturas[$wtotfac]['desfac']=$row[6];
					$facturas[$wtotfac]['histor']=$row[7];
					$facturas[$wtotfac]['ingres']=$row[8];

					$wtotfac++;
			    }
			}
			$wtotfac--;

			echo "<tr><td colspan=7 bgcolor=".$wcf2.">&nbsp&nbsp</td></tr>";
			echo "</table>";

			//------------------------------------------------  Generacion del Archivo Plano --------------------------------------------

			if ($wgenmmg== "SI")
			{
				// Se graba el consecutivo de Envio
	        	$query = "SELECT max(magssacon) ".
	 	       	     	 "  FROM ".$empresa."_000067 ";

				$res4 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    			$num4 = mysql_num_rows($res4);

    			if ($num4 > 0)
    			{
    				$row4 = mysql_fetch_array($res4);
		    		$wconenv=$row4[0];
		    		$wconenv++;
    			}
    			else	$wconenv=1;

    			// Creacion del Archivo Plano
    			//chdir ("../..");
				$ruta="../../planos/ips/".$user."/";
          		$dh=opendir($ruta);

           		if(readdir($dh) == false)
		    	{
	         		mkdir($ruta,0777);
		    	}

		    	for ($j=1; $j<=2; $j++)
			 	{
		    	if (file_exists($ruta."890939026-1_".$wconenv.".txt"))
			     		unlink ($ruta."890939026-1_".$wconenv.".txt");
		     	}
		     	$file=fopen ($ruta."890939026-1_".$wconenv.".txt","w+");

		  		$wtotenv=0;				// Total de Registros a Enviar.
		     	$wfacenv=0;				// Total Facturas a Enviar.

		  		for($f=0;$f<=$wtotfac;$f++)
				{
					if (isset($windgen[$f]))
					{
						$wfacenv++;

						$query = "SELECT count(*) ".
	 	         				 	 "  FROM ".$empresa."_000066 a, ".$empresa."_000106 b, ".$empresa."_000119 c ".
		         				 	 " WHERE a.rcfffa ='".$facturas[$f]['fuente']."'".
			         			 	 "   AND a.rcffac ='".$facturas[$f]['factura']."'".
			         			 	 "   AND b.id = a.rcfreg ".
			         			 	 "   AND c.relconpro = b.tcarconcod";

						/*echo "<input type='hidden' name='facturas[".$f."][fuente]' value='".$facturas[$f]['fuente']."'>";
						echo "<input type='hidden' name='facturas[".$f."][factura]' value='".$facturas[$f]['factura']."'>";*/
						$res5 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    					$num5 = mysql_num_rows($res5);

    					if($num5 > 0)
						{
							$row5 = mysql_fetch_array($res5);
			    			$wtotenv=($wtotenv+$row5[0]);
			    			$facturas[$f]['numconp']=$row5[0];
			    		}
					}
				}

				if($wfacenv > 0)
				{
					// Inicializo Variables Registro de Control, Cabecera y Detalle
					$wregcon=0;
					$wregcab=1;
					$wregdet=2;
					$valido1 ='off';
					/*$wtotenv=add_ceros($wtotenv,6);
					$wfacenv=add_ceros($wfacenv,6);*/
					$wfecenf=date("d/m/Y");
					/*$wcodsuc=add_ceros($wips,3);*/
					$wtipdoc="NI";
					/*$wnitipf=add_ceros($wnitips,15);*/

					fwrite ($file,$wregcon.",");    	       	   //Registro de Control
	         		fwrite ($file,$wtotenv.","); 	               //Numero de Reg. Enviados
		     		fwrite ($file,$wfacenv.",");   		           //Numero de Fac. Enviadas
		   			fwrite ($file,$wfecenf.",");               	   //Fecha del Envio
		   			/*fwrite ($file,$wcodsuc.",");*/fwrite ($file,"1,");        	       //Codigo de la Sucursal (IPS)
		   			fwrite ($file,$wtipdoc.",");   			       //Tipo de Documento
		   			fwrite ($file,$wnitipf.",");				   //Numero del Documento
					fwrite ($file,$wnitdip);				   //Digito de Verificacion
					fwrite ($file,chr(13).chr(10));			   //Salto de Linea

					// Ciclo para Recorrer las Facturas Seleccionadas
					for ($f=0;$f<=$wtotfac;$f++)
					{
						if (isset($windgen[$f]))
						{
							$wvalido1 ='off';

							// Variables Registro Cabecera
							$wfecfaf=ffec_dmy($facturas[$f]['fecfac']);
							$wfactura=explode("-",$facturas[$f]['factura']);
					    	$wletfac=$wfactura[0];
					    	$longitud= strlen(ltrim($wletfac));
					    	$longitud=(6-$longitud);
					    	for ($i=1;$i<=$longitud;$i++)
					    	{
                                //$wletfac=$wletfac." ";
						    	$wletfac=$wletfac;
					    	}

					    	//query para traer los copagos y las cuotas moderadoras 2007-11-14

					    	$query = "SELECT Fencop, Fencmo ".
	 	         				 	 "  FROM ".$empresa."_000018 ".
		         				 	 " WHERE Fenffa ='".$facturas[$f]['fuente']."'".
			         			 	 "   AND Fenfac ='".$facturas[$f]['factura']."'";
					    	$res_copcuo = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    						$num_copcuo = mysql_num_rows($res_copcuo);

    						if ($num_copcuo>0)
    						{
    							$row_copcu = mysql_fetch_array($res_copcuo);
    							$cop=$row_copcu[0];
    							$cumo=$row_copcu[1];
    						}

					    	$wnumfac=$wfactura[1];
							/*$wnumfac=add_ceros($wnumfac,10);
							$wvaltfa=add_ceros($facturas[$f]['valfac'],12);
							$wpercar=add_ceros(0,10);
							$wcuomod=add_ceros($cumo,10);
							$wcopago=add_ceros($cop,10);
							$wtiquet=add_ceros(0,10);   						// buscar en el maestro de concepto el cpto tipo franquicia.
							$wdestfa=add_ceros($facturas[$f]['desfac'],10);*/
							$wnumfac = $wnumfac;
                            $wvaltfa = $facturas[$f]['valfac']*1;
                            $wpercar = 0;
                            $wcuomod = $cumo;
                            $wcopago = $cop*1;
                            $wtiquet = 0;                           // buscar en el maestro de concepto el cpto tipo franquicia.
                            $wdestfa = $facturas[$f]['desfac'];
                            $wconret = 0;

							//2008-09-24
                            $wvaltfa=$facturas[$f]['valfac']*1;
                            $wdestfa=$facturas[$f]['desfac']*1;
							$wcoptfa=$wcopago+$wvaltfa;
						  	//$wcoptfa=add_ceros($wcoptfa,10);// este se cambia para ponerle 2 ceros de mas

						  	// 2008-10-30
							/*$wcoptfa=add_ceros($wcoptfa,12);*/

							fwrite ($file,$wregcab.",");    	       	   //Registro de Cabecera
		   					fwrite ($file,$wplan.",");					   //Plan
		   					fwrite ($file,$wfecfaf.",");	 			   //Fecha de la Factura
		   					fwrite ($file,$wletfac.",");				   //Letras de la Factura
		   					fwrite ($file,$wnumfac.",");   				   //Numero de la Factura
                            //fwrite ($file,$wvaltfa.",");                 //Valor de la Factura

		   					//2008-09-24
		   					fwrite ($file,$wcoptfa.",");                 //Valor de la Factura + el Valor del coopago
                            fwrite ($file,$wpercar.",");                   //Periodos de Carencia
                            fwrite ($file,$wcuomod.",");                   //Cuota Moderadora
                            fwrite ($file,$wcopago.",");                   //Copagos
                            fwrite ($file,$wtiquet.",");                   //Tiquetes
                            fwrite ($file,$wdestfa.",");                   //Descto de la Factura
                            fwrite ($file,'1');                        //Cpto de Ret. Fte.
                            fwrite ($file,chr(13).chr(10));            //Salto de Linea




							// Variables Registro de Detalle
							$wnumaut=0;					// numero de autorizacion
							$wtipid="";					// tipo de identificacion
							$wnumid=0;					// numero de indentif.
							$wfecate="";				// fecha de atencion
							$wtipate=0;					// tipo de atencion
							$wcoddia=0;					// diagnostico

							$query = "SELECT ingord, pactdo, pacdoc, ingfei, ingtin ".
	 	         				 	 "  FROM ".$empresa."_000100, ".$empresa."_000101 ".
		         				 	 " WHERE pachis = '".$facturas[$f]['histor']."'".
		         				 	 "   AND ingnin = '".$facturas[$f]['ingres']."'".
	             				 	 "   AND inghis = pachis";

							$err2 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    						$num2 = mysql_num_rows($err2);

    						if($num2 > 0)
							{
								$row2 = mysql_fetch_array($err2);
		     					//$wnumaut=add_ceros($row2[0],8);
                                $wnumaut=$row2[0];
		     					$wtipid =$row2[1];
                                //$wnumid =add_ceros($row2[2],12);
		     					$wnumid =$row2[2];
                                $wfecate=ffec_dmy($row2[3]);
		     					$wfecate==add_ceros($wfecate,6);
		     					$wtipate=$row2[4];

		     					if($wtipate == "U")
									$wtipate= "0";
								else
								{
									if($wtipate == "A")
										$wtipate= "1";
									else
									{
										if($wtipate == "H")
											$wtipate= "9";
										else
											$wtipate= "0";
									}
								}
							}

							$query = "SELECT diacod ".
	 	         					 "  FROM ".$empresa."_000109 ".
		         					 " WHERE diahis ='".$facturas[$f]['histor']."'".
			         				 "   AND diaing ='".$facturas[$f]['ingres']."'".
			         				 "   AND diatip = 'P' ";

							$err3 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    						$num3 = mysql_num_rows($err3);

    						if($num3 > 0)
							{
								$row3 = mysql_fetch_array($err3);
                                //$wcoddia=add_ceros($row3[0],7);
			     				$wcoddia=$row3[0];
			     			}

							// Detalle de cargos ...
							// query para ver si es paquete 2008-09-24
			     			$query = "SELECT c.relconsus,b.tcarcan,b.tcarvto,b.tcarprocod, b.Tcartfa".
	 	         				 	 "  FROM ".$empresa."_000066 a, ".$empresa."_000106 b, ".$empresa."_000119 c ".
		         				 	 " WHERE a.rcfffa ='".$facturas[$f]['fuente']."'".
			         			 	 "   AND a.rcffac ='".$facturas[$f]['factura']."'".
			         			 	 "   AND b.id = a.rcfreg ".
			         			 	 "   AND c.relconpro = b.tcarconcod";

							$resp = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    						$rowp = mysql_fetch_array($resp);


		   				    $query = "SELECT c.relconsus,b.tcarcan,b.tcarvto,b.tcarprocod, b.Tcartfa".
	 	         				 	 "  FROM ".$empresa."_000066 a, ".$empresa."_000106 b, ".$empresa."_000119 c ".
		         				 	 " WHERE a.rcfffa ='".$facturas[$f]['fuente']."'".
			         			 	 "   AND a.rcffac ='".$facturas[$f]['factura']."'".
			         			 	 "   AND b.id = a.rcfreg ".
			         			 	 "   AND c.relconpro = b.tcarconcod";
							$res6 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    						$num6 = mysql_num_rows($res6);

    						if ($rowp[4]=='PAQUETE')// 2008-09-24
    						{
    							$num6=1;
    						}

                            if($num6 > 0)
							{
								for ($z=1;$z<=$num6;$z++)
								{
									$wcodate=0;					// codigo de la atencion - 5050114 prepagada
									$wcantre=0;					// cantidad realizada
									$wsubate=0;					// codigo de subatencion -
									$wcansat=0;					// cantidad realizada
									$wvlrate=0;					// valor de la atencion

									$row6 = mysql_fetch_array($res6);
                                    /*if( $row6['Tcartfa'] == "CODIGO" || $row6['Tcartfa'] == "ABONO" ){
                                        continue;
                                    }*/

									if ($wcodigo='800088702EPS')// para poner el codigo cups cuando es eps 2007-11-14
									{
										$query = "SELECT Procup".
				 	         				 	 "  FROM ".$empresa."_000103 ".
					         				 	 " WHERE Procod ='".$row6[3]."'";

										$res_cup = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			    						$num_cup = mysql_num_rows($res_cup);

			    						if ($num_cup>0)
			    						{
			    							$row_cup = mysql_fetch_array($res_cup);
			    							$cup=explode('-',$row_cup[0]);


			    							$wcodate=$cup[0];


			    						/*$query = "SELECT Proemppro ".
				 	         				 	 "  FROM ".$empresa."_000070, ".$empresa."_000115, ".$empresa."_000106, ".$empresa."_000018 ".
					         				 	 " WHERE Proempcod = Movpaqcod ".
					         				 	 "  AND Movpaqreg=".$empresa."_000106.id ".
												 "	AND Tcarhis = Fenhis ".
					         				 	 "	AND Tcaring = Fening ".
					         				 	 "	AND Fening=Tcaring ".
					         				 	  "	AND Fenffa='".$facturas[$f]['fuente']."'".
					         				 	 "	AND Fenfac='".$facturas[$f]['factura']."'";
										$res_pac = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
				    					$num_pac = mysql_num_rows($res_pac);
				    					$row_pac = mysql_fetch_array($res_pac);
			    						$wcodate=$row_pac[0];

				    					echo $query."--------".$wcodate;
				    					echo "<bR>";
				    					echo $row6[3];
				    					echo "<br>";*/



										/*if (strlen($row6[3])>7)
										{
											$wcodate=substr($row6[3],1);
										}
										else
										{
											$wcodate=add_ceros($row6[3],7);// este es para cuando es menor a 7 caracteres
										}*/

										}
			    					}
									else
									{
										$wcodate=5050114;
									}


                                    //$wcantre=add_ceros(1,3);
									$wcantre=1;
                                    //$wsubate=add_ceros($row6[0],7);
									$wsubate=$row6[0];
									settype($row6[1],"integer");
                                    //$wcansat=add_ceros($row6[1],3);
									$wcansat=$row6[1];
                                    $wcantre=$wcansat;//--> se agrego para que concida este dato con el el datro de cantidad

									// para los paquetes
									if ($row6[4]=='PAQUETE')// 2008-09-24
									{
										$wvlrate=$wcoptfa;
									}
									else
									{
                                        //$wvlrate=add_ceros($row6[2],12);
										$wvlrate=$row6[2];
									}

                                    /*if( $row6['Tcartfa'] == "ABONO" ){
                                        $wvlrate = 0;
                                    }*/
									//$wcodate=add_ceros($wcodate,7);

									fwrite ($file,$wregdet.",");    	       	   //Registro de Detalle
		   							fwrite ($file,$wnumfac.",");			       //Numero de la Factura
		   							fwrite ($file,$wnumaut.",");    	       	   //Numero de Autorizacion
		   							fwrite ($file,$wtipid.","); 	   	       	   //Tipo de Identificacion
		   							fwrite ($file,$wnumid.",");   	 	       	   //Numero de Identificacion
		   							fwrite ($file,$wfecate.",");    	       	   //Fecha de la Atencion
                                    //fwrite ($file,$wtipate.",");                   //Tipo de la Atencion
		   							fwrite ($file,"1,");    	       	   //Tipo de la Atencion
                                    //fwrite ($file,$wcodate.",");                   //Codigo de la Atencion
				   					fwrite ($file,$wcodate.",");	  	       	   //Codigo de la Atencion
									fwrite ($file,$wcantre.",");    		   	   //Cantidad Realizada
									fwrite ($file,$wsubate.",");				   //Codigo de la Subatencion
									fwrite ($file,$wcansat.",");       			   //Cantidad realizada
									fwrite ($file,$wvlrate.",");       			   //Valor total a cobrar por cpto de la atencion
									fwrite ($file,$wcoddia);     	   		   //Codigo del Diagnostico
									fwrite ($file,chr(13).chr(10));			   //Salto de Linea

		   							$wvalido1 = 'on';
								}
							}
		   				}
					}

					if ($wvalido1 == 'on')
	        		{

	        			for ($f=0;$f<=$wtotfac;$f++)
						{
							if (isset($windgen[$f]))
							{
		    					$q= " INSERT INTO ".$empresa."_000067 (Medico,Fecha_data,Hora_data,magssacon,magssafue,magssadoc,magssafec,magssacer,magssareg,magssaedo,Seguridad) ".
					       			" VALUES ('".$empresa."','".$wfecha."','".$hora."' ,".$wconenv.",'".$facturas[$f]['fuente']."','".$facturas[$f]['factura']."','".$wfecenv."','".$wcodigo."',".$facturas[$f]['numconp'].",'on','C-".$user."')";
					  	        $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							}
						}

						echo "<table border=0 width=60%>";
				       	echo "<tr><td width=6% bgcolor=#FFFFFF align=left><font size=2><A HREF='../reportes/r002-mmag-susalud.php?pos2=".$wconenv."&amp;wnit=".$wcodigo."&amp;wsuc=".$wips."&amp;wdsuc=".$wdesips."&amp;wnitips=".$wnitips."&amp;wdnit=".$wnitdip."&amp;empresa=".$empresa."' target='_blank'>Imprimir</font></td>";

				       	$ruta  = "/var/www/matrix/planos/ips/".$user."/";
			       		$d = dir("../../planos/ips/".$user."/");
			       		$files =$d->read();
                        //echo "edb-->".$files;
						echo "<tr><td bgcolor=#FFFFFF align=left><font size=2><A href='../../planos/ips/".$user."/890939026-1_".$wconenv.".txt' target='_blank'>890939026-1_".$wconenv.".txt &nbsp;&nbsp;&nbsp; Haga Click Para Descargar el Medio Magnetico</A></font></td></tr>";
						$d->close();


						echo "<tr><td colspan=2><font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=".$wcf." LOOP=-1>EL MEDIO MAGNETICO FUE GENERADO!!!!</MARQUEE></FONT></td></tr>";
						echo "<br><br>";
						echo "</table>";
	        		}
					else
					{
						echo "<table align=center border=0>";
                        echo "<tr></tr>";
						echo "<td align=center size=2><b>Relacion de Conceptos faltantes por factura</b></td>";
						// modificacion: 2007-04-24
						for($f=0;$f<=$wtotfac;$f++)
						{
							// este query trae los conceptos que no se encuentran relacionados
						    $query = "SELECT b.Tcarconcod ,b.Tcarconnom, Rcffac ".
	 	         				 	 "  FROM ".$empresa."_000066 a, ".$empresa."_000106 b".
		         				 	 " WHERE a.rcfffa ='".$facturas[$f]['fuente']."'".
			         			 	 "   AND a.rcffac ='".$facturas[$f]['factura']."'".
			         			 	 "   AND b.id = a.rcfreg ".
                                   "GROUP BY b.Tcarconcod ";
							$res = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());

                            //Modificacion 2012-11-29 Jonatan Lopez - Evalua que conceptos hacen falta en el listado de historias seleccionadas.
                            while($row = mysql_fetch_array($res))
                            {
                               $query1 = "SELECT Relconpro ".
                                        "  FROM ".$empresa."_000119".
                                        " WHERE Relconpro ='".$row['Tcarconcod']."'".
                                        "   AND Relconest = 'on' ";
                                $res1 = mysql_query($query1,$conex) or die (mysql_errno().":".mysql_error());
                                $row1 = mysql_fetch_array($res1);

                                //Si el concepto no esta en la tabla de conceptos, se mostrara el mensaje.
                                if ($row1['Relconpro'] == '')
                                {
                                    echo "<tr><td align=left><font size=2><b>El concepto: ".$row[0]."-".$row[1]." para la factura: ".$row[2]." no esta relacionado en la Tabla 000119</b></font></td></tr>";
                                }
                            }
	   					}


    						//
				//echo $wtotfac;
	 					echo "<tr><td align=right><font size=2><b>Se Presento Inconsistencias al Generar el Archivo... Este No fue Generado</b></font></td></tr>";
   						echo "</table>";
					}
				}
				fclose ($file);
        	}
        }
		else
	   	{
	 		echo "<table align=right border=0>";
	 		echo "<tr><td align=right><font size=2><b>No Se Encontraron Registros...</b></font></td></tr>";
   			echo "</table>";
	    }
	}
	else 	// SI ES CONSULTA O ANULAR
	{
		if (isset($wcon) and (($wcon == 2) || ($wcon == 3)))
		{
			if (($wnummmg == 0) || ($wnummmg==''))
			{
			   $query =" SELECT magssacon, magssafue, magssadoc, magssafec, magssacer, magssaedo ".
	 	        	    "  FROM ".$empresa."_000067 ".
	 	            	" WHERE magssafue >= '".$wfueini."'"." AND magssafue <= '".$wfuefin."'".
		        		"   AND magssadoc >= '".$wdocini."'"." AND magssadoc <= '".$wdocfin."'".
		        		"   AND magssafec >= '".$wffacin."'"." AND magssafec <= '".$wffacfi."'";
			}
			else
			{
				$query =" SELECT magssacon, magssafue, magssadoc, magssafec, magssacer, magssaedo ".
	 	        	    "  FROM ".$empresa."_000067 ".
		      	 		" WHERE magssacon= '".$wnummmg."'";
			}

			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    		$num = mysql_num_rows($err);

    		if ($num > 0)
	    	{
	    		echo "<table border=0 align=center width=60%>";
	        	echo "<br>";
	      		echo "<tr><td align=center colspan=7 bgcolor=FFCC66><font size=5><b>D E T A L L E &nbsp&nbsp D E &nbsp&nbsp F A C T U R A S</b></font></td></tr>";
	  	    	echo "<tr>";
		    	echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Consec. MMag</font></th>";
	  	    	echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Fuente</font></th>";
		    	echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Factura</font></th>";
		    	echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Fecha</font></th>";
				echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Codigo</font></th>";
				echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Responsable</font></th>";
				echo "<th bgcolor=".$wcf2."><font size=3 text color= #FFFFFF>Estado</th></font>";
				echo "</tr>";

				// Ciclo para recorrer todas las facturas encotradas
				for ($i=1;$i<=$num;$i++)
	        	{
		           	$row = mysql_fetch_array($err);

		           	$query = "SELECT empnom ".
	 		         		 "  FROM ".$empresa."_000024 ".
		    	     		 " WHERE empcod ='".$row[4]."'";

					$res1 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    				$num1 = mysql_num_rows($res1);

    				if($num1 > 0)
					{
						$row1 = mysql_fetch_array($res1);
				    	$wres = $row1[0];
				    }

	           		if ($i%2==0)
		               	$wcolor="#cccccc";
		            else
			           	$wcolor="#999999";

		    	    echo "<tr>";
					echo "<td align=center  bgcolor='".$wcolor."'><font size=2><b>".$row[0]."</b></font></td>";                                  // Csc. MMag
			        echo "<td align=left  bgcolor='".$wcolor."'><font size=2>".$row[1]."</font></td>";                                 //Fuente de la factura
				    echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$row[2]."</font></td>";                                //Documento de la factura
			    	echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$row[3]."</font></td>";                                //Fecha
		    	 	echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$row[4]."</font></td>";                                //Codigo del Responable
			     	echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$wres."</font></td>";                                  //Descrip. del Responsable
					echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$row[5]."</font></td>";		     	                   //Estado de la Factura
	    	       	echo "</tr>";
				}

				if ($wcon == 3)
				{
					if ((isset($wanummg)) && ($wanummg == "SI"))
					{
						for ($i=0;$i<$num;$i++)
						{
					        if (!mysql_data_seek ($err,$i))
					        {
            					printf ("No se puede seleccionar la fila %d\n", $i);
            					continue;
        					}

        					if(!($row = mysql_fetch_array ($err)))
            					continue;

        					$query =  " update ".$empresa."_000067 set magssaedo = 'off' where magssacon= '".$row[0]."'";
							$res1 = mysql_query($query,$conex) or die("ERROR AL ANULAR EL MEDIO MAGNETICO: ".mysql_errno().":".mysql_error());
	   					}
	   					echo "<table align=right border=0>";
	 					echo "<tr><td align=right><font size=2><b>Los Registros Fueron Anulados...</b></font></td></tr>";
   						echo "</table>";
					}
				}
			}
		}

		$ruta  = "../../planos/ips/".$user."/";
		if(is_dir($ruta)===true)
		{
			$d = dir("../../planos/ips/".$user."/");
			$files =$d->read();
		}else
			{
				mkdir($ruta, 0777);
				$d = dir("../../planos/ips/".$user."/");
				$files =$d->read();
			}
		echo "<tr><td bgcolor=#FFFFFF align=left><font size=2><A href='../../planos/ips/".$user."/' target='_blank'> Haga Click Para Ver Listado de Medios Magnetico</A></font></td></tr>";
		$d->close();

	}
   }
}

?>

