<html>
<head>
<title>IMPRESION</title>
<STYLE TYPE="text/css">
<!--
BODY {font-family:courier new}
//-->
</STYLE>

</head>

<body>
<?php
include_once("conex.php");
//////////////////////////////////////////////
//				CONTRO DE CAMBIOS
//////////////////////////////////////////////
//2009-11-03 Se quita toda la relacion que tenga con la tabla 100 ya que el nombre del usuario se encuentra en la factura.

//2006-11-22 Se cambio la manera de preguntar por el responsable

//2006-12-14 Se agregaron los campos obligatorios

//2007-04-26 Se puso la impresion de glosas y tambien para que mostrara los documentos anulados de todas las fuentes

//////////////////////////////////////////////



session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	include_once("root/montoescrito.php");
	




/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//DATOS NECESARIOS PARA IMPRIMIR LA FACTURA DESDE LA TABLA DE CONFIGURACION
$query = " SELECT cfgnit, cfgnom, cfgtre, cfgtel, cfgdir, cfgpin, cfgmai, cfgdom "
	    ."  FROM ".$empresa."_000049 ";

$res = mysql_query($query,$conex) or die ("Este es el error");
$row = mysql_fetch_array($res);

$wnit_pos  =$row[0];
$wnomemppos=$row[1];
$wtipregiva=$row[2];
$wtel_pos  =$row[3];
$wdir_pos  =$row[4];
$wpagintern=$row[5];
$wemail_pos=$row[6];
$wteldompos=$row[7];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$fue=explode('-',$fuente);

		////////////////////////////////////////////////////////////////////////////para envio
        $query =  " SELECT carfue, cardes
                FROM ".$empresa."_000040
              	WHERE Carenv='on'
             	ORDER BY carfue ";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);

        for ($i=1;$i<=$num;$i++)
        {
            $row1 = mysql_fetch_array($err);
        }
		////////////////////////////////////////////////////////////////////////////para radicacion
        $query =  " SELECT carfue, cardes
                FROM ".$empresa."_000040
              	WHERE Carrad='on'
             	ORDER BY carfue ";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);

        for ($i=1;$i<=$num;$i++)
        {
            $rowr = mysql_fetch_array($err);
        }

        ////////////////////////////////////////////////////////////////////////////para devolucion
        $query =  " SELECT carfue, cardes
                FROM ".$empresa."_000040
              	WHERE Cardev='on'
             	ORDER BY carfue ";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);

        for ($i=1;$i<=$num;$i++)
        {
            $rowd = mysql_fetch_array($err);
        }

        ////////////////////////////////////////////////////////////////////////////para glosas
        $query =  " SELECT carfue, cardes
	                  FROM ".$empresa."_000040
	              	 WHERE Carglo='on'
	              ORDER BY carfue ";
        $err = mysql_query($query,$conex);
        $numgl = mysql_num_rows($err);

        for ($i=1;$i<=$numgl;$i++)
        {
            $rowgl = mysql_fetch_array($err);
        }


 $query = " SELECT fecha_data, rencod, rennom, renvca, rencaj, renusu, rencco, renobs, renest
		 	  FROM ".$empresa."_000020
			 WHERE renfue =". $fue[0]."
			   AND rennum = ".$dcto;

 $res = mysql_query($query,$conex) or die ("Este es el error1");
 $num = mysql_num_rows($res);
	if ($num > 0)
	   {
		    $row = mysql_fetch_array($res);

 		    $wfecdoc = $row[0];
 		    $wcodemp = $row[1];
 		    $wnomemp = $row[2];
 		    $wvaldoc = $row[3];
 		    $wcajdoc = $row[4];
 		    $wusudoc = $row[5];
 		    $wccodoc = $row[6];
 		    $wobser = $row[7];
 		    $westado = $row[8];
 	 }

 //$nom=explode('-',$wnomemp);


 $query = " SELECT Empdir, Empdiv, Empnom, Empnit
		 FROM ".$empresa."_000024
		 WHERE Empcod ='".$wcodemp."'";

 $res = mysql_query($query,$conex) or die ("Este es el error2");
 $num = mysql_num_rows($res);
 $dir = mysql_fetch_array($res);

 if ($westado == 'off')// para que ponga anulado cuando el documento ha sido anulado
 {
 	echo "<center><h1><font size=5><b>DOCUMENTO ANULADO</b></font></h1></center>";

 }

 echo "<center><table border=0 align=center WIDTH=750 HEIGHT=200>";

    //IMPRESION CON EL LOGO AL PRINCIPIO
	echo "<tr><td align=left rowspan=4><img src='/matrix/images/medical/pos/logo_".$empresa.".png' WIDTH=317 HEIGHT=100></td></tr>";
	echo "<tr><td WIDTH=25% colspan=1><font size=2><b>".$fue[1]."</b></font></td></tr>";
	echo "<tr><td colspan=1><font size=2><b>Nro: ".$dcto."</b></font></td></tr>";
	echo "<tr><td colspan=1><font size=2><b>Fecha: ".$wfecdoc."</b></font></td></tr>";
	echo "<tr><td align=center colspan=2><font size=2><b>".$wnomemppos."</b></font></td></tr>";
	echo "<tr><td align=center colspan=2><font size=2><b>Nit. : ".$wnit_pos."</b></font></td></tr>";
	echo "<tr><td>&nbsp</td></tr>";

	if($fue[0]==$row1[0])
	{
		if (($dir[1] != '') and ($dir[1] != 'N') )
		{
			echo "<tr><td align=left colspan=2><font size=2><b>Señores: ".trim($dir[3])."-".$dir[1]." -".$dir[2]."<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp".$dir[0]."
				 <br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspENVIGADO</b></font></td></tr>";
		}
		else
		{
			echo "<tr><td align=left colspan=2><font size=2><b>Señores:  ".$dir[3]."-".$dir[2]."<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp".$dir[0]."
		    <br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspENVIGADO</b></font></td></tr>";

		}
		echo "<tr><td colspan=2><font size=2><br><br><br>ADJUNTAMOS A ESTA RELACION DE ".$fue[1]." LOS DOCUMENTOS, ENTRE FACTURAS Y NOTAS, QUE CORRESPONDEN A SERVICIOS PRESTADOS A PACIENTES CON RESPONSABILIDAD
			 POR PARTE DE USTEDES Y QUE DETALLAMOS A CONTINUACION:</td></tr>";
	}
	else
	{
		echo  "<tr><td><b>Empresa:</b> ".$dir[3]."-".$dir[2]."</td></tr>";
	}
	echo "<tr><td colspan=2><br><br><b><hr></b></td></tr>";
	echo "<tr><td align=center colspan=5><font size=2><b>D E T A L L E&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspD E&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspF A C T U R A S</td></tr>";
	echo "<tr><td colspan=2><b><hr></b></td></tr>";
	echo "</table>";


	echo "<left><table border=0 WIDTH=750 >";


	///////////////////////////////////////////////////query para traer el numero de campos obligatotrios por empresa

				$query =  " SELECT Cobnpa, Cobnta
				             FROM ".$empresa."_000121
				             WHERE Cobest='on'
				             AND Cobcod='".$wcodemp."'";
	            $err = mysql_query($query,$conex);
	            $numco = mysql_num_rows($err);

	            echo "<input type='hidden' name='numco' value='".$numco."'>";

	            if ($numco>0)
	               {
	               	echo "<tr ><td align=center><font size=2><b>Documento</td>";

	               	for ($l=1;$l<=$numco;$l++)
		                {
		               	$rowco = mysql_fetch_array($err);
		               	$arr[$l]['co']=$rowco[1];


		               	echo "<input type='hidden' name='arr[".$l."][co]' value='".$arr[$l]['co']."'>";

		               	echo "<td align=center><font size=2><b>&nbsp".$rowco[0]."</td>";
		                }

	               	echo "<td align=center><font size=2>&nbsp&nbsp&nbsp&nbsp<b>Fecha&nbsp&nbsp&nbsp&nbsp</td><td align=center><font size=2><b>Nombre</td>
		  					<td align=center><font size=2><b>Valor Factura</td><td align=center><font size=2><b>Vr Cop, CMO<br> y Abonos<br></td><td align=center><font size=2><b>Valor Neto</td></tr>";

	               }
	               else if ($fuente==$rowgl[0]."-".$rowgl[1])
	               {
	               		echo "<tr ><td align=center><font size=2><b>Documento</td><td align=center><font size=2>&nbsp&nbsp&nbsp&nbsp<b>Fecha&nbsp&nbsp&nbsp&nbsp</td><td align=center><font size=2><b>Nombre</td>
	  					<td align=center><font size=2><b>Saldo Factura&nbsp</td><td align=center><font size=2><b>Valor Glosado&nbsp</td></tr>";

	               }
	               else
	               {
               			echo "<tr ><td align=center><font size=2><b>Documento</td><td align=center><font size=2>&nbsp&nbsp&nbsp&nbsp<b>Fecha&nbsp&nbsp&nbsp&nbsp</td><td align=center><font size=2><b>Nombre</td>
	  					<td align=center><font size=2><b>Valor Factura</td><td align=center><font size=2><b>Vr Cop, CMO<br> y Abonos<br></td><td align=center><font size=2><b>Valor Neto</td></tr>";
	               }

	$query =  " SELECT Rdefac, Rdevca, a.id, Rdesfa, b.fenfec, b.id idfactura
				FROM ".$empresa."_000021 a, ".$empresa."_000018 b
				WHERE Rdefue='".$fue[0]."'
				AND Rdenum='".$dcto."'
				AND fenffa = rdeffa
				AND fenfac = rdefac
				ORDER BY fenfec asc, idfactura asc";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);

	//////////////////para los totales
	$totvf=0;
	$totve=0;

	for ($i=1;$i<=$num;$i++)
	{
		$dat = mysql_fetch_array($err);
		$fan[$i]['nf']=$dat[0];

		/////////////////pal nombre
		//2009-11-03
		$query =  " SELECT Fennpa
					FROM ".$empresa."_000018, ".$empresa."_000021
					WHERE Rdefac='".$fan[$i]['nf']."'
					AND Rdefac=Fenfac";
		$err1 = mysql_query($query,$conex);
		$row5 = mysql_fetch_array($err1);
		//echo mysql_errno() ."=". mysql_error();
		$nomb=$row5[0];

		$query =  " SELECT Fecha_data, Fencop, Fencmo, Fenabo
				FROM ".$empresa."_000018
				WHERE Fenfac='".$fan[$i]['nf']."'";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		$fec = mysql_fetch_array($err1);
		$valext=$fec[1]+$fec[2]+$fec[3];

		$nom=explode('-',$dat[0]);
		$fan[$i]['fd']=$nom[0];
		$fan[$i]['nd']=$nom[1];

		$query =  " SELECT Fecha_data
				FROM ".$empresa."_000021
				WHERE Rdefue='".$fan[$i]['fd']."'
				AND Rdenum='".$fan[$i]['nd']."'";
		$err2 = mysql_query($query,$conex);
		$num2 = mysql_num_rows($err2);
		$fec1 = mysql_fetch_array($err2);

		$valfac=$dat[1]+$valext;///////////////////////////////////////////valor de la factura

		$totve=$totve+$valext;////////////////total valores extras


		if ($fuente==$rowgl[0]."-".$rowgl[1])
		{
			$totvf=$totvf+$dat[3];////////////////total valores de los saldos de las facturas
		}
		else
		{
			$totvf=$totvf+$valfac;////////////////total valores factura
		}

		echo "<tr><td align=left><font size=2>".$dat[0]."</td>";

		//////////////////////////////////////////este es el for para traer los campos obligatorios
		 for ($l=1;$l<=$numco;$l++)
		{

        	$query =  " SELECT ".$arr[$l]['co']."
			             FROM ".$empresa."_000101, ".$empresa."_000018
			             WHERE Fenhis=Inghis
			             AND Fening=Ingnin
			             AND Fenfac='".$fan[$i]['nf']."'
			             AND Fenest='on'";

           $err1 = mysql_query($query,$conex);
       	   $numdo = mysql_num_rows($err1);
            //echo mysql_errno() ."=". mysql_error();

            $rowdo = mysql_fetch_array($err1);
           	$arr[$l][$i]=$rowdo[0];

           	echo "<td align=center><font size=2>".$arr[$l][$i]."</td>";


		}

		// esto es para las glosas
		if ($fuente==$rowgl[0]."-".$rowgl[1])
		{
			echo "<td align=left><font size=2>".$fec[0]."".$fec1[0]."</td><td align=left ><font size=2>".$nomb."</td><td align=right><font size=2>".number_format($dat[3],0,'.',',')."</td><td align=right><font size=2>".number_format($dat[1],0,'.',',')."</td></font></tr>";

		}
		else
		{
			echo "<td align=left><font size=2>".$fec[0]."".$fec1[0]."</td><td align=left ><font size=2>".$nomb."</td><td align=right><font size=2>".number_format($valfac,0,'.',',')."</td><td align=right><font size=2>".number_format($valext,0,'.',',')."</td><td align=right><font size=2>".number_format($dat[1],0,'.',',')."</td></font></tr>";
		}

	}
	echo "<tr><td><hr></td></tr>";
	echo "<tr><td align=left><font size=2><b>Valor Total</td><td></td><td></td>";

	for ($l=1;$l<=$numco;$l++)
	{

		echo "<td></td>";
	}

	// esto es pa glosas
	if ($fuente==$rowgl[0]."-".$rowgl[1])
	{
		echo "<td align=right><font size=2><b>".number_format($totvf,0,'.',',')."</td><td align=right><font size=2><b>".number_format($wvaldoc,0,'.',',')."</td></tr>";
	}
	else
	{
		echo "<td align=right><font size=2><b>".number_format($totvf,0,'.',',')."</td><td align=right><font size=2><b>".number_format($totve,0,'.',',')."</td><td align=right><font size=2><b>".number_format($wvaldoc,0,'.',',')."</td></tr>";
	}

	echo "</table>";
	echo "<br>";

	echo "<center><table border=0 WIDTH=600 >";
	echo "<tr ><td><font size=2><b>Numero de Documentos:</b>  ".$num."</td><td align=right><font size=2><b>Valor Total:    ".number_format($wvaldoc,0,'.',',')."</td></tr>";
	echo "<tr ><td colspan=2><font size=2><b>Valor en Letras:</b>  ".montoescrito($wvaldoc)."</td></tr>";
	echo "</table>";
	echo "<br>";
	echo "<br>";
	echo "<br>";

	if ($wobser !='')
	{
	echo "<center><table border=0 WIDTH=600 >";
	echo "<tr ><td><font size=2><b>Observacion:</b> ".$wobser."</td></tr >";
	echo "</table>";
	}
	echo "<br>";
	$hora=date("h:m:s");
	$caj=explode('-',$wcajdoc);

	//////////////////////para las causas, solo aplica para las devoluciones
	if($fue[0]==$rowd[0])
	{
		echo "<center><table border=0 WIDTH=600 >";
		echo "<tr ><td><font size=2><b>Causa de devoluvion:</b></td></tr >";
		$query =  " SELECT Caucod, Caunom
				FROM ".$empresa."_000072, ".$empresa."_000071
				WHERE Caucod=Doccau
				AND Docest='on'
				AND Docfue='".$fue[0]."'
				AND Docnum='".$dcto."'";
		$err = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err);
		//echo $num;
		for ($i=1;$i<=$num1;$i++)
		{
			$causa = mysql_fetch_array($err);
			echo "<tr ><td><font size=2>".$causa[0]." - ".$causa[1]."</td></tr >";

		}
		echo "</table>";
		echo "<br>";
	}

	echo "<center><table border=0 WIDTH=750>";
	if($fue[0]==$row1[0])
	{
		echo "<tr><td colspan=5><font size=2>Por su atención, muchas gracias<br><br><br>Codialmente,<br><br><br><b>DEPTO. DE CARTERA</td></tr>";
	}


	echo "<tr><td  colspan=5><br><br><br><b><hr></b></td></tr>";
	echo "<tr><td><font size=2>FECHA: ".$wfecdoc."</td><td><font size=2>HORA: ".$hora."</td><td><font size=2>USUARIO: ".$key."</td><td><font size=2>CAJA: ".$caj[0]."</td><td><font size=2>SUCURSAL: ".$wccodoc."</td></tr>";
	echo "<tr><td  colspan=5><b><hr></b></td></tr>";

}
?>
</body>
</html>