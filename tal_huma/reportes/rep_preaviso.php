<html>
<head>
  	<title>Reporte para preaviso</title>
</head>
<body  BGCOLOR="FFFFFF">
<font face='arial'>
<BODY TEXT="#000066">


<?php
include_once("conex.php");
//==================================================================================================================================
//PROGRAMA						:Reporte para preaviso
//AUTOR							:Juan David Londono
//FECHA CREACION				:2007-06-04
//FECHA ULTIMA ACTUALIZACION 	:2007-06-04
//                             
$wactualiz="2020-03-18";
//==================================================================================================================================
//ACTUALIZACIONES
//==================================================================================================================================
// 2020-03-18 Se modifica el query OR Confpr > '".$fecha."', ya que no genera porque es mayor a la fecha de elaboración
// Ing. Gustavo A. Avendaño Rivera. 			 
//==================================================================================================================================
// xxxx
//==================================================================================================================================



@session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	
	echo "<form name=seg_dispositivos action='' method=post>";
	//$wbasedato='cominf';
	// ENCABEZADO
	if (!isset ($fecha2) or $fecha2=='')
	
	{
	  	echo "<br><br><br>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><font size=5><img src='/matrix/images/medical/root/clinica.JPG' WIDTH=200 HEIGHT=80></font></td></tr>";
		echo "<tr><td><br></td></tr>";
		echo "<tr><td align=center colspan=2><font size=5>REPORTE PARA PREAVISO</font></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><b>TIPO DE CONTRATO:</b> <select name='tipo'>";// este es el query para traer los tipos de contratos
			
		$query= " SELECT Numtip, Nomtip
      		 	    FROM ".$wbasedato."_000001
	         	   WHERE Acttip = 'on'
		      	   ORDER BY 1 ";
	    $err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<option>*-TODOS</option>";
		for ($i=1;$i<=$num;$i++)
        {
            $row = mysql_fetch_array($err);
            echo "<option>".$row[0]."-".$row[1]."</option>";
        }
		echo "</select></td></tr>";
	    echo "<tr><td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='fecha1'></td>";
        echo "<td bgcolor=#dddddd align=center><b>Fecha Final (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='fecha2'></td>";
        echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";

	}
	////////////////////////////////////////////////////////apartir de aca comienza la impresion
	else
	{
	     $fecha=date("Y-m-d");// fecha actual
	     
	     if ($tipo != '*-TODOS')// este es para seleccionar el tipo de contrato
	     {
	     	$todos = "AND Contip = '".$tipo."'";
	     }
	     else
	     {
	     	$todos = " ";
	     }
	     //                 0       1      2     3      4      5      6      7      8      9     10      11    12     13     14    15
	     $query= " SELECT Concon,Connum,Contip,Conpa1,Conpa2,Connit,Conobj,Convai,Convac,Confin,Conffi,Confpr,Conres,Conpro,Conter,id
	      		 	  FROM ".$wbasedato."_000002
		         	 WHERE Confpr between '".$fecha1."' and '".$fecha2."'
			      	   AND Conpro = 'on' 
					   ".$todos."			      	 
			      	 ORDER BY 1 ";
					 
		//echo $query;	
	    $err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		//echo mysql_errno() ."=". mysql_error();
		//echo $num;

		  echo "<center><table border=0>";// este es el encabezado del resultado
		  echo "<tr><td align=center colspan=7><font size=5><img src='/matrix/images/medical/calidad/logo_promo.GIF' WIDTH=300 HEIGHT=80></font></td></tr>";
		  echo "<tr><td><br></td></tr>";
		  echo "<tr><td align=center colspan=7><font size=5>REPORTE PARA PREAVISO</font></td></tr>";
		  echo "<tr><td align=center colspan=7>Desde: <b>".$fecha1."</b> hasta <b>".$fecha2."</b></td></tr>";
		  echo "<tr><td>&nbsp</td></tr>";
		  echo "<tr><td>&nbsp</td></tr></table>";
		  
		  
  	  	 echo "<table border=1>";
		 echo "<tr bgcolor=#dddddd><td align=center><font size=2><b>CONSECUTIVO</b></td><td align=center><font size=2><b>No CONTRATO</b></td><td align=center><font size=2><b>TIPO</b></td><td align=center><font size=2><b>PARTE 1</b></td>";
		 echo "<td align=center><font size=2><b>PARTE 2</b></font></td><td align=center><font size=2><b>NIT</b></font></td><td align=center><font size=2><b>OBJETO</b></font></td><td align=center><font size=2><b>VALOR INDETERMINADO</b></font></td><td align=center><font size=2><b>VALOR</b></font></td>";
		 echo "<td align=center><font size=2><b>FECHA INICIAL</b></font></td><td align=center><font size=2><b>FECHA FINAL</b></font></td><td align=center bgcolor=#FF0000><font color=#FFFFFF size=2><b>FECHA PREAVISO</b></font></td>";
		 echo "<td align=center><font size=2><b>REPONSABLE</b></font></td><td align=center><font size=2><b>PROROGADO</b></font></td><td align=center><font size=2><b>TERMINADO</b></font></td><td align=center><font size=2><b>EDITAR</b></font></td></tr>";
		 
		 $total=0;
		 for ($i=1;$i<=$num;$i++) 
	     {
	  		$row = mysql_fetch_array($err);
		  	if (is_int ($i/2))
		       $wcf="DDDDDD";
		    else
		       $wcf="FFFFFF";
		    
		    if ($row[7]=='off')// para cuando sea booleano poner si o no --> VALOR INDETERMINADO
		    {
		    	$ind='NO';
		    }
		    else
		    {
		    	$ind='SI';
		    }   
		    if ($row[13]=='off')// para cuando sea booleano poner si o no --> PRORROGADO
		    {
		    	$pro='NO';
		    }
		    else
		    {
		    	$pro='SI';
		    } 
		    
		    if ($row[14]=='off')// para cuando sea booleano poner si o no --> TERMINADO
		    {
		    	$ter='NO';
		    }
		    else
		    {
		    	$ter='SI';
		    }     
		  	echo "<tr bgcolor=".$wcf." border=1><td align=left>".$row[0]."</td><td align=left>".$row[1]."</td><td align=left>".$row[2]."</td><td align=left>".$row[3]."</td><td align=left>".$row[4]."</td>";
		  	echo "<td align=left>".$row[5]."</td><td align=left>".$row[6]."</td><td align=right>".$ind."</td><td align=right>".number_format($row[8],0,'.',',')."</td><td align=right>".$row[9]."</td><td align=right>".$row[10]."</td><td align=left>".$row[11]."</td>";
		  	echo "<td align=left>".$row[12]."</td>";
		  	// para el hipervinculo para editar el registro
		  	$hyper="<A HREF='/matrix/det_registro.php?id=".$row[15]."&pos1=talento&pos2=2007-06-04&pos3=11:18:24&pos4=000002&pos5=0&pos6=talento&tipo=P&Valor=&Form=000002-talento-C-CONVENIOS%20Y%20CONTRATOS&call=0&change=0&key=talento&Pagina=1' target='new' >Editar</a>";
		  	echo "<td align=left>".$pro."</td><td align=left>".$ter."</td><td align=left>".$hyper."</td></tr>";
		 }
	     echo "<tr bgcolor=#dddddd><td align=left colspan=16><font size=2><b>TOTAL CONTRATOS: ".$num."</b></td></tr>";
		 
 	}
}
?>
</body>
</html>
