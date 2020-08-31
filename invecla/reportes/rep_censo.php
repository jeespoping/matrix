<html>
<head>
<title>Reporte para control del censo</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<?php
include_once("conex.php");

/********************************************************
*     REPORTE PARA EL CONTROL DEL CENSO DIARIO			*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte para el control del censo diario
//AUTOR							:Juan David Londoño
//FECHA CREACION				:ENERO 2007
//FECHA ULTIMA ACTUALIZACION 	:11 de Enero de 2007
//DESCRIPCION					:Este reporte sirve para llevar el control del censo diario
//
//==================================================================================================================================
$wactualiz="Ver. 2007-01-11";


session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$empresa='cominf';

	

	


	echo "<form name=rep_censo action='' method=post>";
  
if (!isset($fec2))
{
	echo "<center><table border=1>";
    echo "<tr><td align=center colspan=2 bgcolor=#006699><font size=6 text color=#FFFFFF><b> REPORTE PARA EL CONTROL DEL CENSO DIARIO</b></font><br><font size=3 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// seleccion para ingreso, egreso
        echo "<tr><td align=CENTER bgcolor=#DDDDDD><b><font text color=#003366 size=3> Tipo: <br></font></b><select name='tipo' >";

        echo "<option></option>";
        echo "<option>01-INGRESO</option>";
        echo "<option>02-EGRESO</option>";
        echo "</select></td>";
        
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// seleccion para el servicio
        echo "<td align=CENTER bgcolor=#DDDDDD><b><font text color=#003366 size=3> Servicio: <br></font></b><select name='serv' >";

        $query =  " SELECT subcodigo, descripcion
                FROM det_selecciones
              	WHERE medico='cominf'
              	AND codigo='087'";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);
        echo "<option></option>";
        echo "<option>9999-TODOS LOS SERVICIOS</option>";
        for ($i=1;$i<=$num;$i++)
        {
            $row = mysql_fetch_array($err);
            echo "<option>".$row[0]."-".$row[1]."</option>";
        }
        echo "</select></td></tr>";
      

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


}else{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION

	echo "<center><table border=1>";
    echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=6><b>REPORTE PARA EL CONTROL DEL CENSO DIARIO</b></font><br><font size=1 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";
	echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>SERVICIO: <i>".$serv."</i></td></tr>";
	echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td></tr>";
	echo "</table>";
	echo "<br>";
	
	
	/*$query="CREATE  TEMPORARY  TABLE  IF  NOT  EXISTS tempo6 AS  
			SELECT Fdefue AS fue, Fdedoc AS doc 
			 FROM clisur_000065 
			WHERE Fecha_data BETWEEN  '2007-01-01' AND  '2007-07-16' 
			  AND Fdecco =  '1300' 
			  AND Fdevco > 0
			  AND fdefue IN (  '19',  '20' )
			  AND Fdeest ='on' 
			GROUP  BY 1 , 2";
	$err1 = mysql_query($query,$conex);
	$query="SELECT Fenfec, fenhis, fening, fenres, Empnom, Fdecon, Grudes, Fdevco
		    FROM clisur_000018, clisur_000065, tempo6, clisur_000004, clisur_000024
		   WHERE Fdefue = fue
		   	 AND fenres = Empcod
		     AND Fdedoc = doc
		     AND Fdevco>0
		     AND Fenffa = Fdefue
		     AND Fenfac = Fdedoc
		     AND Fdeest ='on' 
		     AND Grucod = Fdecon
		   ORDER BY 1, 2";*/
	
	// nota debito	   
	/*$query="SELECT Fenfec, fenhis, fening, fenres, Empnom, Fdecon, Grudes, Fdevco
		    FROM clisur_000018, clisur_000065, clisur_000004, clisur_000024, clisur_000021
		   WHERE Fdeest ='on'
		   	 AND fenres = Empcod
		     AND Fenffa = Rdeffa
		     AND Fenfac = Rdefac
		     AND clisur_000065.Fecha_data BETWEEN  '2007-01-01' AND  '2007-07-16'
		     AND Fdefue = '25'
		     AND Fdevco > 0 
		     AND Grucod = Fdecon 
		     AND Fdefue = Rdefue
		     AND Fdedoc = Rdenum 
		   ORDER BY 1, 2";	   
	/*$query="SELECT Tcarfec, Tcarhis, Tcaring, Tcarres, Tcarconcod, Grudes, Tcarvto este es el viejo
			FROM clisur_000106, clisur_000004
			WHERE Tcarhis in (select Tcarhis from clisur_000106 where Tcarser='1300')
			AND Tcarvto >0 
			AND Tcarfec between '2007-01-01' and '2007-06-16' 
			AND Grucod=Tcarconcod";*/
			//echo $query;
	/*$erre = mysql_query($query,$conex);
	$nume = mysql_num_rows($erre);
	//echo mysql_errno() ."=". mysql_error();
	$total=0;
	echo "<center><table border=1>";
	for ($i=1;$i<=$nume;$i++)
		    {
		    	 $row = mysql_fetch_array($erre);
		    	 echo "<tr>";
		    	 echo "<td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7];
		    	 echo "</tr>";
		    	$total=$total+$row[7];
		    }
	      echo "<tr><td colspan=8 align=right>TOTAL:".$total."</td></tr>";*/
	
	$tip=explode('-',$tipo);
	/////////////////////////////para el ultimo campo de la fila
	if ($tipo=="01-INGRESO")
			{ 
				$tip1="PROCEDENCIA";
			}else if ($tipo=="02-EGRESO")
				{
					$tip1="MOTIVO DE EGRESO";
				}
	echo "<center><table border=1>";
	/////////////////////////////////////////////////para cuando son todos los servicios
	if ($serv=="9999-TODOS LOS SERVICIOS"){
		
		echo "<tr><td align=center bgcolor=#006699><font text color=#FFFFFF><b>FECHA ".$tip[1]."</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>HISTORIA CLINICA</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>Nº DE INGRESO</b></font></td>" .
				 "<td align=center bgcolor=#006699><font text color=#FFFFFF><b>NOMBRE</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>".$tip1."</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>SERVICIO</b></font></td></tr>";
	
		
	}else{  
		echo "<tr><td align=center bgcolor=#006699><font text color=#FFFFFF><b>FECHA ".$tip[1]."</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>HISTORIA CLINICA</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>Nº DE INGRESO</b></font></td>" .
				 "<td align=center bgcolor=#006699><font text color=#FFFFFF><b>NOMBRE</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>".$tip1."</b></font></td></tr>";
		}
		if ($tipo=="01-INGRESO")
		{ 
			
		/////////////////////////////////////////////////para cuando son todos los servicios
	if ($serv=="9999-TODOS LOS SERVICIOS"){
		
		$query =  " SELECT Fecha_ing, ".$empresa."_000032.Historia_clinica, Num_ingreso, Nombres, Primer_apellido, Segundo_apellido, Procedencia, Servicio
	                FROM ".$empresa."_000032, ".$empresa."_000016 
	              	WHERE Fecha_ing between '".$fec1."' and '".$fec2."' 
	              	AND ".$empresa."_000032.Historia_clinica = ".$empresa."_000016.Historia_clinica	
	              	ORDER BY Servicio";
	        $err = mysql_query($query,$conex);
	        $num = mysql_num_rows($err);
	        //echo mysql_errno() ."=". mysql_error();
	        
	}else{
		
	
		$query =  " SELECT Fecha_ing, ".$empresa."_000032.Historia_clinica, Num_ingreso, Nombres, Primer_apellido, Segundo_apellido, Procedencia
	                FROM ".$empresa."_000032, ".$empresa."_000016 
	              	WHERE Fecha_ing between '".$fec1."' and '".$fec2."'
	              	AND Servicio='".$serv."' 
	              	AND ".$empresa."_000032.Historia_clinica = ".$empresa."_000016.Historia_clinica	";
	        $err = mysql_query($query,$conex);
	        $num = mysql_num_rows($err);
	        //echo mysql_errno() ."=". mysql_error();
	}
			
		
	        
		}else if ($tipo=="02-EGRESO")
			{ 
			
			/////////////////////////////////////////////////para cuando son todos los servicios
	if ($serv=="9999-TODOS LOS SERVICIOS"){
		
		$query =  " SELECT Fecha_egre_serv, ".$empresa."_000033.Historia_clinica, Num_ingreso, Nombres, Primer_apellido, Segundo_apellido, Tipo_egre_serv, Servicio
		                FROM ".$empresa."_000033, ".$empresa."_000016 
		              	WHERE Fecha_egre_serv between '".$fec1."' and '".$fec2."'
		              	AND ".$empresa."_000033.Historia_clinica = ".$empresa."_000016.Historia_clinica	
		        		ORDER BY Servicio";
		        $err = mysql_query($query,$conex);
		        $num = mysql_num_rows($err);
		        //echo mysql_errno() ."=". mysql_error();
	}else{	
			$query =  " SELECT Fecha_egre_serv, ".$empresa."_000033.Historia_clinica, Num_ingreso, Nombres, Primer_apellido, Segundo_apellido, Tipo_egre_serv
		                FROM ".$empresa."_000033, ".$empresa."_000016 
		              	WHERE Fecha_egre_serv between '".$fec1."' and '".$fec2."'
		              	AND Servicio='".$serv."' 
		              	AND ".$empresa."_000033.Historia_clinica = ".$empresa."_000016.Historia_clinica	";
		        $err = mysql_query($query,$conex);
		        $num = mysql_num_rows($err);
		        //echo mysql_errno() ."=". mysql_error();
		}
			}
		for ($i=1;$i<=$num;$i++)
		    {
		    	if (is_int ($i/2))
                    $wcf="DDDDDD";
                    else
                    $wcf="CCFFFF";
                
                $row = mysql_fetch_array($err);
                  /////////////////////////////////////////////////para cuando son todos los servicios
	if ($serv=="9999-TODOS LOS SERVICIOS")
	{
		echo "<tr  bgcolor=".$wcf."><td align=center>".$row[0]."</td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td><td align=center>".$row[3]." ".$row[4]." ".$row[5]."</td><td align=center>".$row[6]."</td><td align=center>".$row[7]."</td></tr>";
		
	}else{  
		
         echo "<tr  bgcolor=".$wcf."><td align=center>".$row[0]."</td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td><td align=center>".$row[3]." ".$row[4]." ".$row[5]."</td><td align=center>".$row[6]."</td></tr>";
		}   
		    }
		    
		if ($serv=="9999-TODOS LOS SERVICIOS")
		{   
			$clp=3;
		}else{
			$clp=2;
		}
		echo "<tr><td align=left colspan=3 bgcolor=#006699><font text color=#FFFFFF><b>NUMERO TOTAL DE PACIENTES: </b></font></td><td align=right colspan=".$clp." bgcolor=#006699><font text color=#FFFFFF><b>".$num."</b></font></td></tr>";
			
	}
}
?>



























