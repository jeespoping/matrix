<html>
<head>
<title>Reporte de los estados de las facturas</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
function enter()
{
    document.forms.Rep_enrade.submit();
}
</script>

<?php
include_once("conex.php");

/********************************************************
*     REPORTE DE LOS ESTADOS DE LAS FACTURAS			*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte de los estados de las facturas	 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:FEBRERO 2008
//FECHA ULTIMA ACTUALIZACION 	:
//DESCRIPCION					:
//	
//MODIFICACIONES:
//****-**-**:						 
//==================================================================================================================================
$wactualiz="Ver. 2008-02-12";

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

	echo "<form name=estado_fac action='' method=post>";
	
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	
if (!isset($fec1) and !isset($fec2))
{	
	echo "<center><table border=1>";
    echo "<tr><td align=center rowspan=2 colspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
    echo "<tr><td align=center colspan=1 bgcolor=#006699><font size=6 text color=#FFFFFF><b>REPORTE DE LOS ESTADOS DE LAS FACTURAS</b></font><br><font size=3 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////FUENTE
	echo "<tr><td align=left bgcolor=#DDDDDD><b><font text color=#003366 size=3> Fuente: <br></font></b><select name='fuente' onchange='enter()'>";
	echo "<option></option>";
    echo "<option>GE-GENERADAS</option>";
    echo "<option>EV-ENVIADAS</option>";
    echo "<option>RD-RADICADAS</option>";
    echo "<option>DV-DEVUELTAS</option>";
    echo "<option>GL-GLOSADAS</option>";
    echo "</select></td>";
   
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////RESPONSABLE
    echo "<td align=left bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3> Responsable: <br></font></b><select name='responsable' onchange='enter()'>";
		
        $query =  " SELECT empcod, empnit, empnom
                FROM ".$wbasedato."_000024 
              	WHERE Empcod=Empres
             	ORDER BY empnom ";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);
        echo "<option></option>";
        echo "<option>*-TODAS LAS EMPRESAS</option>";
        for ($i=1;$i<=$num;$i++)
        {
            $row = mysql_fetch_array($err);
            echo "<option>".$row[0]."-".$row[1]."-".$row[2]."</option>";
        }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////RANGO DE FECHAS
    
    if (isset($fec1))
    $fec1=$fec1;
    else
    $fec1='';

    if (isset($fec2))
    $fec2=$fec2;
    else
    $fec2='';
    
        echo "<tr><td align=center bgcolor=#DDDDDD colspan=3><b><font text color=#003366 size=3> <i>Fecha Inicial&nbsp(AAAA-MM-DD):</font></b><INPUT TYPE='text' NAME='fec1' VALUE='".$fec1."'>
			 <b>Fecha Final&nbsp<i>(AAAA-MM-DD):<INPUT TYPE='text' NAME='fec2' VALUE='".$fec2."'></td></tr>";
  		echo "<tr><td align=center bgcolor=#cccccc colspan=3><input type='submit' value='OK'></td>";                            //submit
        echo "</tr>";
        echo "</table>";
        echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
        echo "<br>";
            
}else{           
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   
	$rela=explode('-',$fuente);
	$cod=explode('-',$responsable);
	echo "<center><table border=1>";
    echo "<tr><td align=center ><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=400 HEIGHT=100></td></tr>";
    echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>REPORTE RELACION DE FACTURAS: ".$rela[1]."</b></font><br><font size=1 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";
	echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td></tr>";
	echo "</table>";
	echo "<br>";
	echo "<center><table border=1>";
	
	if ($responsable=='*-TODAS LAS EMPRESAS')
	{
		$cod[0]='%';
	}
	
	$query =  " SELECT Fenfec, Fenffa, Fenfac, Fenval, Fensal, Empnom
            	  FROM ".$wbasedato."_000018, ".$wbasedato."_000024 
              	 WHERE Fenesf='".$rela[0]."'
              	   AND Fenest='on'
              	   AND Fensal>0
              	   AND Fencod=Empcod
              	   AND Empcod like '".$cod[0]."'
              	   AND Fenfec between '".$fec1."' and '".$fec2."'
              	 ORDER BY Empnom";
    $err = mysql_query($query,$conex);
    //echo mysql_errno() ."=". mysql_error();
    $num = mysql_num_rows($err);
    
   if ($responsable=='*-TODAS LAS EMPRESAS')
	{
		echo "<tr><td bgcolor=#ffcc66 colspan=6 align=left><b>EMPRESA: *-TODAS LAS EMPRESAS</b></td></tr>";
		echo "<tr bgcolor=#ffcc66><td><b>Fecha</td><td><b>Fuente Factura</td><td><b>No de Factura</td><td><b>Valor de la Factura</td><td><b>Saldo de la Factura</td><td><b>Empresa</td></tr>";
	}
   else
   {
   		echo "<tr><td bgcolor=#ffcc66 colspan=5 align=left><b>EMPRESA: ".$cod[1]."-".$cod[2]."</b></td></tr>";
		echo "<tr bgcolor=#ffcc66><td><b>Fecha</td><td><b>Fuente Factura</td><td><b>No de Factura</td><td><b>Valor de la Factura</td><td><b>Saldo de la Factura</td></tr>";
	}
   
	$totv=0;
	$tots=0;
	 
    for ($i=1;$i<=$num;$i++)
	{
		if (is_int ($i/2))
        	$wcf="DDDDDD";
        else
        	$wcf="CCFFFF";
        	
		$row = mysql_fetch_array($err);
		
		$totv=$totv+$row[3];
		$tots=$tots+$row[4];
		
		if ($responsable=='*-TODAS LAS EMPRESAS')
		{
			echo "<tr  bgcolor=".$wcf."><td align=center>".$row[0]."</td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td><td align=right>".number_format($row[3],0,'.',',')."</td><td align=right>".number_format($row[4],0,'.',',')."</td><td align=center>".$row[5]."</td></tr>";
		}
		else
		{
			echo "<tr  bgcolor=".$wcf."><td align=center>".$row[0]."</td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td><td align=right>".number_format($row[3],0,'.',',')."</td><td align=right>".number_format($row[4],0,'.',',')."</td></tr>";
		}
		
	}  
							     	
	if ($responsable=='*-TODAS LAS EMPRESAS')
		{
			echo "<tr bgcolor=#ffcc66><td colspan=2><b>TOTAL DE TODAS LAS EMPRESAS</td><td align=right><b>No Dctos: ".$num."</td><td align=right><b>Vl. Facturas: ".number_format($totv,0,'.',',')."</td><td align=right><b>Vl. Saldos: ".number_format($tots,0,'.',',')."</td><td align=right>&nbsp</td></tr>";
			$hyper="<A HREF='/matrix/ips/reportes/estado_fac.php?wemp_pmla=".$wemp_pmla."'>VOLVER</a>";
			echo "<tr><td  colspan= '6' align=center><font size=3 face='arial' >$hyper</td></tr>";			
		}
		else
		{
			echo "<tr bgcolor=#ffcc66><td colspan=2><b>TOTAL DE ".$cod[2]."</td><td align=right><b>No Dctos: ".$num."</td><td align=right><b>Vl. Facturas: ".number_format($totv,0,'.',',')."</td><td align=right><b>Vl. Saldos: ".number_format($tots,0,'.',',')."</td></tr>";
			$hyper="<A HREF='/matrix/ips/reportes/estado_fac.php?wemp_pmla=".$wemp_pmla."'>VOLVER</a>";
			echo "<tr><td  colspan= '5' align=center><font size=3 face='arial' >$hyper</td></tr>";
		}
		echo "<tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
	}	
	echo "</table>";		
}
liberarConexionBD($conex);
?>
