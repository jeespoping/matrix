<html>
<head>
  	<title>Reporte del saldo de las glosas</title>
</head>
<body  BGCOLOR="FFFFFF">
<font face='arial'>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
//==================================================================================================================================
//PROGRAMA						:Reporte del saldo de las glosas
//AUTOR							:Juan David Londoño
//FECHA CREACION				:2008-04-21
//FECHA ULTIMA ACTUALIZACION 	:2008-04-21
$wactualiz="2008-04-21";
//==================================================================================================================================
//ACTUALIZACIONES
//==================================================================================================================================
// xxxx
//==================================================================================================================================
// xxxx
//==================================================================================================================================


include_once("root/comun.php");

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower($institucion->baseDeDatos);
	$wentidad = $institucion->nombre;

	echo "<form name=rep_Confac action='' method=post>";

	echo "<input type='HIDDEN' NAME='wemp_pmla' value='".$wemp_pmla."'>";

	if (!isset($wfec_f) or !isset($wfec_i) or !isset($empresa))
	   {
	   	$wfecha=date("Y-m-d");// esta es la fecha actual
        echo "<br><br><br>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><font size=5><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></font></td></tr>";
		echo "<tr><td><br></td></tr>";
		echo "<tr><td align=center colspan=2><font size=5>REPORTE DE SALDO DE GLOSAS</font></td></tr>";
		echo "<tr><td td bgcolor=#dddddd align=center colspan=2><b>Empresa:</b> <select name='empresa'>";
		// query para traerme las empresas
        $query =  " SELECT Empcod, Empnit, Empnom
                      FROM ".$wbasedato."_000024 " .
                     "WHERE Empcod = Empres " .
                     "AND Empest='on' " .
                   " ORDER BY 3";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);

      	echo "<option>*-TODAS LAS EMPRESAS</option>";
	       for ($i=1;$i<=$num;$i++)
	        {
	            $row = mysql_fetch_array($err);
	            echo "<option>".$row[0]."-".$row[1]."-".$row[2]."</option>";
	        }
        echo "</select></td></tr>";
        //echo "<input type='hidden' name='codi' value='".$row[0]."'>";
        echo "<tr><td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_i' value=".$wfecha." SIZE=10></td>";
	    echo "<td bgcolor=#dddddd align=center><b>Fecha Final (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_f' value=".$wfecha." SIZE=10></td>";
	    echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'>&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr></table>";
	   }
	else
	   {
	   		$cod=explode('-',$empresa); // para que las busque por el codigo
	   		if ($cod[0] == '*')
	   		{
	   			$cod[0]='%';
	   		}
	   			// este query me trae los datos que necesito que me muestre en pantalla como empresa, documento, nombre, codigo del concepto, nombre del concepto, fecha y telefono
		   		$query =  " SELECT ".$wbasedato."_000021.Fecha_data, Empcod, Empnit, Empnom, Emptem, Fenffa, Fenfac, Rdevca, Rdesfa, (Rdesfa-Rdevca), Fensal
			                  FROM ".$wbasedato."_000018, ".$wbasedato."_000021, ".$wbasedato."_000024
			              	 WHERE ".$wbasedato."_000021.Fecha_data between '".$wfec_i."' and '".$wfec_f."'
			              	   AND Rdefue = 85 " .
			              	"  AND Rdeest = 'on'" .
			              	"  AND Fenffa = Rdeffa " .
			              	"  AND Fenfac = Rdefac " .
			              	"  AND Fencod like '".$cod[0]."'" .
		   					"  AND Fensal > 0 " .
			              	"  AND Empres = Fenres
			              	ORDER BY 1 ";
		        $err = mysql_query($query,$conex);
		        $num = mysql_num_rows($err);
		        //echo mysql_errno() ."=". mysql_error();

		        echo "<center><table border=1>";
			    echo "<tr><td align=center ><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png'></td></tr>";
			    echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>REPORTE DE VENTAS POR EMPRESAS</b></font><br><font size=1 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";
				echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>FECHA INICIAL: <i>".$wfec_i."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$wfec_f."</i></b></font></b></font></td></tr>";
				echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>EMPRESA: ".$empresa."</b></td></tr>";
				echo "</table>";
				echo "<br>";
				echo "<center><table border=0>";

				$tog=0;
				$tos=0;
				$ton=0;
				$toh=0;
				//echo $num;
				if ($num>0)
				{


					echo "<tr bgcolor=#cccccc><td align=center><b>FECHA</b></td><td align=center><b>COD. EMPRESA</b></td><td align=center><b>NIT EMPRESA</b></td><td align=center><b>NOMBRE</b></td><td align=center><b>TIPO EMPRESA</b></td>";
					echo "<td align=center><b>FUENTE FACTURA</b></td><td align=center><b>NUMERO FACTURA</b></td><td align=center><b>VALOR GLOSADO</b></td><td align=center><b>SALDO FACTURA<br>(a la fecha)</b></td><td align=center><b>VAL. NETO</b></td>
						  <td align=center><b>SALDO FACTURA<br>(hoy)</b></td></tr>";
			        for ($i=1;$i<=$num;$i++)
			         {
			            if (is_int ($i/2))
		                $wcf="DDDDDD";
		                else
		                $wcf="CCFFFF";

			            $row = mysql_fetch_array($err);
			            $arr[$i]['fec']=$row[0];
			            $arr[$i]['cod']=$row[1];
			            $arr[$i]['nit']=$row[2];
			            $arr[$i]['nom']=$row[3];
			            $arr[$i]['tip']=$row[4];
			            $arr[$i]['fue']=$row[5];
			            $arr[$i]['fac']=$row[6];
			            $arr[$i]['vlg']=$row[7];
			            $arr[$i]['vls']=$row[8];
			            $arr[$i]['vln']=$row[9];
			            $arr[$i]['sho']=$row[10];

			           $tog=$tog+$arr[$i]['vlg'];
			           $tos=$tos+$arr[$i]['vls'];
			           $ton=$ton+$arr[$i]['vln'];
			           $toh=$toh+$arr[$i]['sho'];

			           echo "<tr bgcolor=".$wcf."><td>".$arr[$i]['fec']."</td><td align=left>".$arr[$i]['cod']."</td><td align=left>".$arr[$i]['nit']."</td><td align=left>".$arr[$i]['nom']."</td><td align=left>".$arr[$i]['tip']."</td>";
					   echo "<td align=left>".$arr[$i]['fue']."</td><td align=left>".$arr[$i]['fac']."</td><td align=right>".number_format($arr[$i]['vlg'],0,'.',',')."</td><td align=right>".number_format($arr[$i]['vls'],0,'.',',')."</td><td align=right>".number_format($arr[$i]['vln'],0,'.',',')."</td>
					   		 <td align=right>".number_format($arr[$i]['sho'],0,'.',',')."</td>";


			         }
		         	 echo "<tr><td align=center bgcolor=#006699 colspan=4><font text color=#FFFFFF><b>NUMERO TOTAL DE GLOSAS: ".$num."</font></td><td align=right bgcolor=#006699 colspan=3><font text color=#FFFFFF><b>TOTALES</font></td>";
					 echo "<td align=right bgcolor=#006699 ><font text color=#FFFFFF><b>".number_format($tog,0,'.',',')."</font></td><td align=right bgcolor=#006699 ><font text color=#FFFFFF><b>".number_format($tos,0,'.',',')."</font></td>";
					 echo "<td align=right bgcolor=#006699 ><font text color=#FFFFFF><b>".number_format($ton,0,'.',',')."</font></td><td align=right bgcolor=#006699 ><font text color=#FFFFFF><b>".number_format($toh,0,'.',',')."</font></td></tr>";
					 echo "</table>";
			         echo"<br>";
			         echo"<br>";
	   			}
	   			else
	   			{
	   				echo "<font text color=#FF0000><b>NO EXISTEN GLOSAS CON ESAS CARACTERISTICAS";
	   				echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
	   			}

		  }
}
liberarConexionBD($conex);
?>
</body>
</html>
