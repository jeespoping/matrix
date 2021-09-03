<HTML>
<HEAD>
<TITLE>Procesos Mensuales de Facturas</TITLE>
	<style type="text/css">
		.tipodrop{color:#000000;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:normal;width:60em;text-align:left;height:2em;}
 	</style>
</HEAD>
<BODY>

 <!-- Loading Calendar JavaScript files -->  <!-- Loading Theme file(s) --
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>    --> 
  
<?php
include_once("conex.php");
include_once("root/comun.php");
$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
$wactualiz = "2015-12-01";
encabezado( "Reporte General de Facturas", $wactualiz, $institucion->baseDeDatos );
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

function UltimoDia($anho,$mes)
{ 
   if (((fmod($anho,4)==0) and (fmod($anho,100)!=0)) or (fmod($anho,400)==0)) { 
       $dias_febrero = 29; 
   } else { 
       $dias_febrero = 28; 
   }
    
   switch($mes) { 
       case "01": return 31; break; 
       case "02": return $dias_febrero; break; 
       case "03": return 31; break; 
       case "04": return 30; break; 
       case "05": return 31; break; 
       case "06": return 30; break; 
       case "07": return 31; break; 
       case "08": return 31; break; 
       case "09": return 30; break; 
       case "10": return 31; break; 
       case "11": return 30; break; 
       case "12": return 31; break; 
   } 
} 



mysql_select_db("matrix") or die("No se selecciono la base de datos");    

//Forma
echo "<form name='rep_facmensual' action='rep_facmensual.php' method=post>"; 
echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>"; 
 
 if (!isset($wfec1) or !isset($wfec2))
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Procesos Mensuales de Facturas<br></font></b>";   
	echo "</tr>";

	
  if (!isset($wfec1))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec1 = date("Y-m-d");
   
    echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Inicial<br></font></b>";   
   	$cal="calendario('wfec1','1')";
	echo "<input type='TEXT' name='wfec1' size=10 maxlength=10  id='wfec1'  value=".$wfec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

  if (!isset($wfec2))   // Si no esta seteada entonces la inicializo en la misma Inicial
    $wfec2=$wfec1;
  
    echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Final<br></font></b>";   
   	$cal="calendario('wfec2','1')";
	echo "<input type='TEXT' name='wfec2' size=10 maxlength=10  id='wfec2' readonly='readonly' value=".$wfec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
	echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2><B>Tercero que presta el Servicio:</B><br></font></b><select name='pp' id='searchinput'>";

    $query = "SELECT Nit,Nombre  FROM tesorhon_000001 Group by Nit Order By Codigo"; 
           
    $err3 = mysql_query($query,$conex);
    $num3 = mysql_num_rows($err3);
    $tpp=$pp;
   
    if (!isset($pp))
    { 
     echo "<option>%%%-Todos</option>";
    }
    else 
    {
     echo "<option>".$tpp[0]."-".$tpp[1]."</option>";
    } 
   
    for ($i=1;$i<=$num3;$i++)
	 {
	 $row3 = mysql_fetch_array($err3);
	 echo "<option>".$row3[0]."-".$row3[1]."</option>";
	 }
	echo "<option></option>";
    echo "</select></td></tr>";
	
 
/****/
 
   echo "<tr><td align=center bgcolor=#cccccc colspan=4><input type='submit' id='searchsubmit' value='Generar'></td>";          //submit o sea el boton de Generar o Aceptar
   echo "</tr>";
   echo "</table>";
   
 }	
 else      // Cuando ya estan todos los datos escogidos     **********************************************************************
 {

	$tpp=explode('-',$pp);
	echo "<center><table border=0>";
    //echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>Reporte General de Facturas</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    //echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: rep_facmensual.php Ver. 2015/12/01<br>AUTOR: Gabriel Agudelo</font></b><br>";
    echo "</table>";

    echo "<br>";
    echo "<table border=0>";
    echo "<tr>";
    //echo "<td colspan=1 align=center bgcolor=#DDDDDD></td>";	
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Fecha<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Codigo<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Nit<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Nombre<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Concepto<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Factura<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Valor<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Ccostos1<b></td>";	
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Valor<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Ccostos2<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Valor<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Ccostos3<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Valor<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Ccostos4<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Valor<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Ccostos5<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Valor<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Ingreso Terceros<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Nombre AFC<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Valor<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Porcentaje<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Nombre PV<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Valor<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Porcentaje<b></td>";
	echo "</tr>"; 
	
	
 
    $wtotgrl = 0;   //0	     1      2      3          4            5          6     7    8    9   10    11   12   13  14   15   16     17      18   19    20     21     22
	$query="SELECT Fecha,Tercero,Factura,Valor,Ingreso_tercero,Observaciones,Cco1,Por1,Cco2,Por2,Cco3,Por3,Cco4,Por4,Cco5,Por5,Afc1,Vlr_afc,Por_afc,Pv1,Vlr_pv,Por_pv,Grupo1 "
          ." FROM tesorhon_000002 "
		  ." WHERE Fecha BETWEEN '".$wfec1."' AND '".$wfec2."'"
		  ."   AND Tercero  LIKE '%".$tpp[0]."-%'" 
		  ." Order by Fecha";
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
	
   if ($nroreg > 0)
   { 
    $totfac=0; 
    $k = 1;
    While ($k <= $nroreg)
    {        
        $registro = mysql_fetch_row($resultado);  	 // Leo 1er registro
       // color de la fila
          if (is_int ($k/2))  // Cuando la variable $i es para coloca este color
            $wcf="DDDDDD";  
          else
            $wcf="CCFFFF";
		$cod=explode('-',$registro[1]); 	
		$reg1=explode('-',$registro[6]); 
		$reg2=explode('-',$registro[8]);
		$reg3=explode('-',$registro[10]);
		$reg4=explode('-',$registro[12]);
		$reg5=explode('-',$registro[14]);
		echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro[0]."</td>";     
		echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$cod[0]."</td>";     
		echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$cod[1]."</td>"; 
		echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$cod[2]."</td>"; 
		echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$cod[3]."-".$cod[4]."</td>"; 
		echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro[2]."</td>";  
		echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro[3]."</td>";  
		$totfac= $totfac + $registro[3];
		if ($registro[7] == 0)
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>LIQ.ESPECIAL</td>";  
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>LIQ.ESPECIAL</td>";  
			}
		else
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$reg1[0]."</td>";  
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".round($registro[3]*($registro[7]/100),0)."</td>";  
			}
		if ($registro[9] == 0)
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";  
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";  
			}
		else
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$reg2[0]."</td>";  
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".round($registro[3]*($registro[9]/100),0)."</td>";
			}
		if ($registro[11] == 0)
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";  
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";  
			}
		else
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$reg3[0]."</td>";  
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".round($registro[3]*($registro[11]/100),0)."</td>";
			}
       if ($registro[13] == 0)
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";  
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";  
			}
		else
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$reg4[0]."</td>";  
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".round($registro[3]*($registro[13]/100),0)."</td>";
			}
		if ($registro[15] == 0)
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";  
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";  
			}
		else
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$reg5[0]."</td>";  
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".round($registro[3]*($registro[15]/100),0)."</td>";
			}
		echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro[4]."</td>";
		if ($registro[16] == 'NO APLICA')
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";
			}
		else
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro[16]."</td>";
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro[17]."</td>";
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".round($registro[3]*($registro[18]/100),0)."</td>";
			}
		if ($registro[19] == 'NO APLICA')
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>---</td>";
			}
		else
			{
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro[19]."</td>";
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro[20]."</td>";
				echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".round($registro[3]*($registro[21]/100),0)."</td>";
			}
		echo "</tr>";
		$k=$k+1;
   }  
   
  }
  echo "<br><tr></table>";
  echo "<table border=0>";
  echo "<td colspan=21 align=center bgcolor='".$wcf."'><font text color=#003366 size=3><b>TOTAL FACTURAS:</b></td>";
  echo "<td colspan=2 align=LEFT   bgcolor='".$wcf."'><font text color=#003366 size=3>".number_format($totfac,0,'.',',')."</td>";
  echo "</tr></table>";
  $query1="SELECT Codigo,Nit,Nombre,Concepto,Grupo "
          ." FROM tesorhon_000001 "
		  ." WHERE Activo = 'on' and Codigo NOT IN (SELECT SUBSTR(Tercero,1,INSTR(Tercero,'-')-1)  from tesorhon_000002 where Fecha BETWEEN '".$wfec1."' AND '".$wfec2."') " 
		  ." Order by Grupo";

   $resultado1 = mysql_query($query1);
   $nroreg1 = mysql_num_rows($resultado1);
   if ($nroreg1 > 0)
	{	
		$k1 = 1;
		echo "<table border=0>";
		echo "<br><tr>";
		echo "<td colspan=5 align=center bgcolor='".$wcf."'><font text color=#003366 size=3><b>TERCEROS QUE NO HAN TRAIDO FACTURAS EN EL PERIODO SELECCIONADO: </b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>CODIGO<b></td>";
		echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>NIT<b></td>";
		echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>NOMBRE<b></td>";
		echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>CONCEPTO<b></td>";
		echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>GRUPO<b></td>";
		echo "</tr>";
		While ($k1 <= $nroreg1)
		{        
			$registro1 = mysql_fetch_row($resultado1);  	 // Leo 1er registro
		   // color de la fila
			  if (is_int ($k1/2))  // Cuando la variable $i es para coloca este color
				$wcf="DDDDDD";  
			  else
				$wcf="CCFFFF";
			echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro1[0]."</td>";     
			echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro1[1]."</td>";  
			echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro1[2]."</td>"; 
			echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro1[3]."</td>"; 
			echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro1[4]."</td>"; 
			echo "</tr>";
		$k1=$k1+1;		
		}
	
	}
  
  

  echo "</tr>";
  echo "</table>"; 
 }  
echo "</Form>"; 
echo "</BODY>";
echo "</HTML>";	

?>
