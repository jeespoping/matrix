<html>
<head>
  	<title>Reporte utilizaci�n de sondas vesicales</title>
</head>
<body  BGCOLOR="FFFFFF">
<font face='arial'>
<BODY TEXT="#000066">

<?php
include_once("conex.php");
//==================================================================================================================================
//PROGRAMA						:Utilizaci�n de Sondas Vesicales
//AUTOR						    :Nancy Estella Gonz�lez G.
//FECHA CREACION			:2007-12-03
//FECHA ULTIMA ACTUALIZACION 	:2007-12-03
$wactualiz="2007-12-03";
//==================================================================================================================================
//ACTUALIZACIONES
//==================================================================================================================================
// xxxx				 
//==================================================================================================================================
// xxxx
//==================================================================================================================================


session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	
	

	


	echo "<form name=utilizacion_sondas_vesicales action='' method=post>";
	$wbasedato='cominf';


	// ENCABEZADO
	if (!isset ($fecha2))
	{
	  $wfecha=date("Y-m-d");// esta es la fecha actual

    echo "<br><br><br>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><font size=5><img src='/matrix/images/medical/invecla/INVECLA.jpg' WIDTH=100 HEIGHT=80></font></td></tr>";
		echo "<tr><td><br></td></tr>";
		echo "<tr><td align=center colspan=2><font size=4>UTILIZACI�N DE SONDAS VESICALES</font></td></tr>";
		echo "<tr><td colspan=2 bgcolor=#dddddd align=center><b>Servicio:</b> <select name='servicio'>";
		// query para traerme las unidades 
	        $query =  " SELECT Subcodigo, Descripcion" .
	        		      "   FROM det_selecciones" .
	        		      "  WHERE Medico = 'cominf'".
	        		      "    AND Codigo = '040'" .
	        		     "ORDER BY 2";
	        $err = mysql_query($query,$conex);
	        $num = mysql_num_rows($err);
	        echo "<option>*TODOS LOS SERVICIOS</option>";
		      for ($i=1;$i<=$num;$i++)
		        {
		            $row = mysql_fetch_array($err);
		            echo "<option>".$row[0]."-".$row[1]."</option>";
		        }
	        echo "</select></td></tr>";
	        echo "<tr><td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='fecha1' value=$wfecha></td>";
		      echo "<td bgcolor=#dddddd align=center><b>Fecha Final (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='fecha2' value=$wfecha></td>";
		      echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
  }
  

  ///  IMPRESION  
  
  else
  {
     if ($fecha1<='2008-03-01' and $fecha2<='2008-03-31')
     {
        $wbasedato1='cominf';  
     }
     else 
     {
        $wbasedato1='movhos';  
     }
     
    if ($servicio !='*TODOS LOS SERVICIOS' )
		{
		 $vble2="AND substring(".$wbasedato1."_000032.Servicio,1,4) = substring('".$servicio."',1,4)";
		 $vble3="AND substring(".$wbasedato1."_000033.Servicio,1,4) = substring('".$servicio."',1,4)";		 
		}
		else
		{
		  $vble2=" ";
		  $vble3=" ";
		}
    $diasestancia=0; // acumulador de dias estancia

     // C�digo para calcular una fecha auxiliar de tres meses antes de la fecha inicial de consulta.
     // esta fecha me va a servir para consultar los pacientes que ingresaron al servicio y que a la fecha dos de corte 
     // a�n se encuentran hospitalizados           
               
     $fec = explode("-",$fecha1);
     if ($fec[1]==01)
     {
       $fec[0]=$fec[0]-1;
       $fec[1]='10';
     }
     else
     {
       $fec[1]=$fec[1]-3;
     }
    
     if ($fec[1]<=9)
     {
        $fec[1]="0".$fec[1];
     }
     $faux=$fec[0]."-".$fec[1]."-".$fec[2];       
     
     //query para traer todos los pacientes que han ingresado a la instituci�n tres meses antes de la fecha1 de consulta
         
     $con = "SELECT ".$wbasedato1."_000032.Historia_clinica, ".$wbasedato1."_000032.Num_ingreso, ".$wbasedato1."_000032.Servicio, ".$wbasedato1."_000032.Fecha_ing, ".$wbasedato1."_000032.Hora_ing
               FROM ".$wbasedato1."_000032
              WHERE ".$wbasedato1."_000032.Fecha_ing >= '".$faux."' and ".$wbasedato1."_000032.Fecha_ing < '".$fecha1."' ".$vble2."
           ORDER BY 1,2,3,4,5";
     $res = mysql_query($con,$conex);
     $numero = mysql_num_rows($res);                       

     for ($i=1;$i<=$numero;$i++)
     {
       $array = mysql_fetch_array($res);
       $pac[$i][0]= $array['Historia_clinica'];
       $pac[$i][1]= $array['Num_ingreso'];
       $pac[$i][2]= $array['Servicio'];
       $pac[$i][3]= $array['Fecha_ing'];
       $pac[$i][4]= $array['Hora_ing'];   
       $pac[$i][5]='0';
       
       //echo $pac[$i][0].",".$pac[$i][1].",".$pac[$i][2].",".$pac[$i][3].",".$pac[$i][4].",".$pac[$i][5];
       //echo "<br>";
     }     
     
     //query para traer todos los pacientes que han egresado de la instituci�n tres meses antes de la fecha1 de consulta    
     
     $con1 = "SELECT ".$wbasedato1."_000033.Historia_clinica, ".$wbasedato1."_000033.Num_ingreso, ".$wbasedato1."_000033.Servicio, ".$wbasedato1."_000033.Fecha_egre_serv, ".$wbasedato1."_000033.Hora_egr_serv
                FROM ".$wbasedato1."_000033
               WHERE ".$wbasedato1."_000033.Fecha_egre_serv >= '".$faux."' and ".$wbasedato1."_000033.Fecha_egre_serv < '".$fecha1."' ".$vble3." 
            ORDER BY 1,2,3,4,5";
     $res1 = mysql_query($con1,$conex);
     $numero1 = mysql_num_rows($res1);      

     for ($i=1;$i<=$numero1;$i++)
     {
       $array1 = mysql_fetch_array($res1);
       $pac1[$i][0]= $array1['Historia_clinica'];
       $pac1[$i][1]= $array1['Num_ingreso'];
       $pac1[$i][2]= $array1['Servicio'];
       $pac1[$i][3]= $array1['Fecha_egre_serv'];
       $pac1[$i][4]= $array1['Hora_egr_serv'];   
       $pac1[$i][5]='0';
              
       //echo $pac1[$i][0].",".$pac1[$i][1].",".$pac1[$i][2].",".$pac1[$i][3].",".$pac1[$i][4].",".$pac1[$i][5];
       //echo "<br>";     
     } 

     for ($i=1;$i<=$numero;$i++)
     {
         $sw=0;
         $q=1;
         $p=1;
         while ($sw==0 and $p<=$numero1)
         {        
            if ($pac[$i][0]==$pac1[$q][0] and $pac1[$q][5]=='0')
            {
               $sw=1;
               $pac[$i][5]='1';
               $pac1[$q][5]='1';
            }
            else
            {
               $p++;  
            }
            $q++;
         }
        //echo $pac[$i][0].",".$pac[$i][1].",".$pac[$i][2].",".$pac[$i][3].",".$pac[$i][4].",".$pac[$i][5];
        //echo "<br>";  
      }
      
      $p=0;
 
      for ($i=1;$i<=$numero;$i++)
      {
       if ($pac[$i][5]=='0')
       {
         $p++;
         $datos[$p][0]=$pac[$i][0];  //Historia cl�nica
         $datos[$p][1]=$pac[$i][1];  //N�mero de ingreso     
         $datos[$p][2]=$pac[$i][2];  //Servicio            
         $datos[$p][3]=$fecha1;      //Fecha de ingreso
         $datos[$p][4]='00:00:00';   //Hora de ingreso                             
       }
      }          
     
     //query para traer todos los pacientes que han ingresado a la instituci�n entre las fechas de consulta
    
     $query1 = "SELECT ".$wbasedato1."_000032.Historia_clinica, ".$wbasedato1."_000032.Num_ingreso, ".$wbasedato1."_000032.Servicio, ".$wbasedato1."_000032.Fecha_ing, ".$wbasedato1."_000032.Hora_ing
                  FROM ".$wbasedato1."_000032
                 WHERE ".$wbasedato1."_000032.Fecha_ing between '".$fecha1."' and '".$fecha2."' ".$vble2."
              ORDER BY 1,2,3,4,5";
     $err1 = mysql_query($query1,$conex);
     $numreg = mysql_num_rows($err1);        
     
     for ($i=1;$i<=$numreg;$i++)
     {
       $p++;
       $registros = mysql_fetch_array($err1);
       $datos[$p][0]= $registros['Historia_clinica'];
       $datos[$p][1]= $registros['Num_ingreso'];
       $datos[$p][2]= $registros['Servicio'];
       $datos[$p][3]= $registros['Fecha_ing'];
       $datos[$p][4]= $registros['Hora_ing'];       
      
       //echo $datos[$i][0].",".$datos[$i][1].",".$datos[$i][2].",".$datos[$i][3].",".$datos[$i][4];
       //echo "<br>";
     } 

     $numreg = $p;


     //query para traer todos los pacientes que han egresado de la instituci�n entre las fechas de consulta     
     
     $query2 = "SELECT ".$wbasedato1."_000033.Historia_clinica, ".$wbasedato1."_000033.Num_ingreso, ".$wbasedato1."_000033.Servicio, ".$wbasedato1."_000033.Fecha_egre_serv, ".$wbasedato1."_000033.Hora_egr_serv
                  FROM ".$wbasedato1."_000033
                 WHERE ".$wbasedato1."_000033.Fecha_egre_serv between '".$fecha1."' and '".$fecha2."' ".$vble3." 
              ORDER BY 1,2,3,4,5";
     $err2 = mysql_query($query2,$conex);
     $numreg1 = mysql_num_rows($err2);        
     
     //echo "<br>";
     //echo "SEGUNDO QUERY";
     //echo "<br>"; 
     
     
     for ($i=1;$i<=$numreg1;$i++)
     {
       $registros = mysql_fetch_array($err2);
       $datos1[$i][0]= $registros['Historia_clinica'];
       $datos1[$i][1]= $registros['Num_ingreso'];
       $datos1[$i][2]= $registros['Servicio'];
       $datos1[$i][3]= $registros['Fecha_egre_serv'];
       $datos1[$i][4]= $registros['Hora_egr_serv'];   
       $datos1[$i][5]='0';    
       
       //echo $datos1[$i][0].",".$datos1[$i][1].",".$datos1[$i][2].",".$datos1[$i][3].",".$datos1[$i][4].",".$datos1[$i][5];
       //echo "<br>";     
     }    
         
     for ($i=1;$i<=$numreg;$i++)
     {
         $sw=0;
         $q=1;
         $p=1;
         while ($sw==0 and $p<=$numreg1)
         {        
            if ($datos[$i][0]==$datos1[$q][0] and $datos1[$q][5]=='0')
            {
               $datos[$i][5]=$datos1[$q][3];  //fecha de egreso del servicio
               $datos[$i][6]=$datos1[$q][4]; // hora de egreso del servicio
               $sw=1;
               $datos1[$q][5]='1';
            }
            else
            {
               $p++;  
               $datos[$i][5]= $fecha2;  //fecha de egreso del servicio
               $datos[$i][6]= '23:59:59'; // hora de egreso del servicio              
            }
            $q++;
         }
      }

      for ($i=1;$i<=$numreg1;$i++)
      {
         if ($datos1[$i][5]=='0')
         {
            $numreg++;
            $datos[$numreg][0]=$datos1[$i][0];  //Historia cl�nica
            $datos[$numreg][1]=$datos1[$i][1];  //N�mero de ingreso a la cl�nica
            $datos[$numreg][2]=$datos1[$i][2];  //Servicio
            $datos[$numreg][3]=$fecha1;         //Fecha de ingreso al servicio  
            $datos[$numreg][4]='00:00:00';      //Hora de ingreso al servicio
            $datos[$numreg][5]=$datos1[$i][3];  //Fecha de egreso del servicio
            $datos[$numreg][6]=$datos1[$i][4];  //Hora de egreso del servicio
         }
      }
     
        
      // Calculo de los dias estancia
      
      for ($i=1;$i<=$numreg;$i++)
      {              
          $fing = explode("-",$datos[$i][3]);     //Fecha de ingreso 
          $fegr = explode("-",$datos[$i][5]);     //Fecha de egreso 
          $hing = explode(":",$datos[$i][4]);     //Hora de ingreso
          $hegr = explode(":",$datos[$i][6]);     //Hora de egreso
          $seg= mktime($hegr[0],$hegr[1],$hegr[2],$fegr[1],$fegr[2],$fegr[0])- mktime($hing[0],$hing[1],$hing[2],$fing[1],$fing[2],$fing[0]);
          $diasest=round(($seg/86400),1);  
          $datos[$i][7]= abs($diasest);                   //C�lculo de los dias estancia 
          $diasestancia = $diasestancia + $datos[$i][7];  //Acumulador dias estancia
          
          ///echo $datos[$i][0].",".$datos[$i][1].",".$datos[$i][2].",".$datos[$i][3].",".$datos[$i][4].",".$datos[$i][5].",".$datos[$i][6].",".$datos[$i][7];
          //echo "<br>";    
      }

    
    /* Este query es para traerle a los pacientes que estuvieron hospitalizados en el servicio y las fechas
       de consulta las Sondas Vesicales que se les instalaron     
    */
     
    $p=0; 
    for ($i=1;$i<=$numreg;$i++)
    {
       $query3 = "SELECT Historia_clinica, Num_ingreso, Num_sond, Sonda, Fecha_instala, Fecha_retiro
                    FROM ".$wbasedato."_000026
                   WHERE ".$wbasedato."_000026.Historia_clinica = '".$datos[$i][0]."'
                     AND ".$wbasedato."_000026.Num_ingreso = '".$datos[$i][1]."'
                     AND (Sonda = '01-Foley'
                      OR  Sonda = '02-Tienam'
                      OR  Sonda = '03-Cistostomia'
                      OR  Sonda = '04-Otra')
                ORDER BY 1, 3";
       $err3 = mysql_query($query3);
       $nsondas = mysql_num_rows($err3); 
     
       for ($n=1;$n<=$nsondas;$n++)
       {
          $consulta = mysql_fetch_array($err3);

          $p++;
          $sondas[$p][0]= $consulta['Historia_clinica'];
          $sondas[$p][1]= $consulta['Num_ingreso'];
          $sondas[$p][2]= $datos[$i][3]; // Fecha inicial (ingreso) con la que se comparar� para sacar los d�as de sonda
          $sondas[$p][3]= $datos[$i][5]; // Fecha final (egreso) con la que se comparar� para sacar los d�as de sonda        
          $sondas[$p][4]= $consulta['Num_sond'];
          $sondas[$p][5]= $consulta['Sonda'];
          $sondas[$p][6]= $consulta['Fecha_instala'];
          $sondas[$p][7]= $consulta['Fecha_retiro'];
       
          //echo $sondas[$p][0].",".$sondas[$p][1].",".$sondas[$p][2].",".$sondas[$p][3].",".$sondas[$p][4].",".$sondas[$p][5].",".$sondas[$p][6].",".$sondas[$p][7];
          //echo "<br>";
       }  
    } 
    
 
    /* En este ciclo se har� el c�lculo de los d�as de sonda que tuvo el paciente en el periodo de tiempo 
       y la unidad consultada
    */   
    
    for ($i=1;$i<=$p;$i++)
    {
       $finst= explode("-",$sondas[$i][6]);   // Fecha de instalaci�n de la sonda
       $freti= explode("-",$sondas[$i][7]);   // Fecha de retiro de la sonda
       $fecin= explode("-",$sondas[$i][2]);   // Fecha inicial de comparaci�n
       $fefin= explode("-",$sondas[$i][3]);   // Fecha final de comparaci�n
      
       if ((($sondas[$i][6]<$sondas[$i][2] and $sondas[$i][7]<$sondas[$i][2]) or ($sondas[$i][6]>$sondas[$i][3] and $sondas[$i][7]>$sondas[$i][3])) and ($sondas[$i][7]!='0000-00-00'))
       {
         $sondas[$i][8]= -99;
       }
       elseif ($sondas[$i][6]>$sondas[$i][3] and $sondas[$i][7]=='0000-00-00')  
       {
         $sondas[$i][8]= -99;
       }     
       elseif ($sondas[$i][6]>=$sondas[$i][2] and $sondas[$i][7]<= $sondas[$i][3] and $sondas[$i][7]!='0000-00-00')
       {
         $seg= mktime(0,0,0,$freti[1],$freti[2],$freti[0])- mktime(0,0,0,$finst[1],$finst[2],$finst[0]);
         $diassond=round(($seg/86400),1);  
         $sondas[$i][8]= $diassond;
       }    
       elseif ($sondas[$i][6]<$sondas[$i][2] and $sondas[$i][7]<= $sondas[$i][3] and $sondas[$i][7]>= $sondas[$i][2])
       {
         $seg= mktime(0,0,0,$freti[1],$freti[2],$freti[0])- mktime(0,0,0,$fecin[1],$fecin[2],$fecin[0]);    
         $diassond=round(($seg/86400),1);  
         $sondas[$i][8]= $diassond;
       }
       elseif ($sondas[$i][6]>=$sondas[$i][2] and $sondas[$i][7]>$sondas[$i][3])
       {
         $seg= mktime(0,0,0,$fefin[1],$fefin[2],$fefin[0])- mktime(0,0,0,$finst[1],$finst[2],$finst[0]);    
         $diassond=round(($seg/86400),1);  
         $sondas[$i][8]= $diassond;
       }
       elseif ($sondas[$i][6]<$sondas[$i][2] and $sondas[$i][7]>$sondas[$i][3])
       {
         $seg= mktime(0,0,0,$fefin[1],$fefin[2],$fefin[0])- mktime(0,0,0,$fecin[1],$fecin[2],$fecin[0]);
         $diassond=round(($seg/86400),1);  
         $sondas[$i][8]= $diassond;
       }  
       elseif ($sondas[$i][6] == $sondas[$i][7] and $sondas[$i][6]>=$sondas[$i][2] and $sondas[$i][7]<=$sondas[$i][3])
       {
         $seg= mktime(0,0,0,$freti[1],$freti[2],$freti[0])- mktime(0,0,0,$finst[1],$finst[2],$finst[0]); 
         $diassond=round(($seg/86400),1);  
         $sondas[$i][8]= $diassond;
       }   
       elseif ($sondas[$i][6]<$sondas[$i][2] and $sondas[$i][7]=='0000-00-00')
       {
         $seg= mktime(0,0,0,$fefin[1],$fefin[2],$fefin[0])- mktime(0,0,0,$fecin[1],$fecin[2],$fecin[0]);
         $diassond=round(($seg/86400),1);  
         $sondas[$i][8]= $diassond;
       }   
       elseif ($sondas[$i][6]>=$sondas[$i][2] and $sondas[$i][7]=='0000-00-00')
       {
         $seg= mktime(0,0,0,$fefin[1],$fefin[2],$fefin[0])- mktime(0,0,0,$finst[1],$finst[2],$finst[0]);  
         $diassond=round(($seg/86400),1);  
         $sondas[$i][8]= $diassond;
       }            

         //echo $sondas[$i][0].",".$sondas[$i][1].",".$sondas[$i][2].",".$sondas[$i][3].",".$sondas[$i][4].",".$sondas[$i][5].",".$sondas[$i][6].",".$sondas[$i][7];
         //echo "<br>";
    } 
    
    /* Este ciclo es para eliminar los registros de los pacientes que tuvieron sondas pero que est�n por
       fuera del rango de consulta
    */ 
    
    $n=0; 
    $diasuso=0;
    for ($i=1;$i<=$p;$i++)
    {
       if ($sondas[$i][8] >=0 )
       {
          $n++;
          $fila[$n][0]= $sondas[$i][0];   // Historia cl�nica
          $fila[$n][1]= $sondas[$i][1];   // N�mero de ingreso
          $fila[$n][2]= $sondas[$i][4];   // N�mero de sonda
          $fila[$n][3]= $sondas[$i][5];   // Sonda
          $fila[$n][4]= $sondas[$i][6];   // Fecha de instalaci�n
          $fila[$n][5]= $sondas[$i][7];   // Fecha de retiro
          $diassonda[$n][6]= $sondas[$i][8];   // Arreglo para guardar los d�as de sonda en el per�odo de consulta y estancia en la unidad
          $diasuso = $diasuso + $sondas[$i][8]; // acumalador de los dias de sonda vesical
                    
          //echo $fila[$n][0].",".$fila[$n][1].",".$fila[$n][2].",".$fila[$n][3].",".$fila[$n][4].",".$fila[$n][5].",".$diassonda[$n][6];
          //echo "<br>";
       }
    } 

    if ($n > 0)
    {
       $promuso = ($diasuso/$n);             // promedio dias de uso
       $propuso = ($diasuso/$diasestancia);  // proporci�n de uso
       sort($diassonda);
       $posiper25 = (round((25*$n)/100)-1)<0?0:round((25*$n)/100)-1;        // c�lculo para la posici�n del percentil 25
       $percentil25 = $diassonda[$posiper25][6];                            // percentil 25
       $posiper50 = (round((50*$n)/100)-1)<0?0:round((50*$n)/100)-1;        // c�lculo para la posici�n del percentil 50
       $percentil50 = $diassonda[$posiper50][6];                            // percentil 50
       $posiper75 = (round((75*$n)/100)-1)<0?0:round((75*$n)/100)-1;        // c�lculo para la posici�n del percentil 75
       $percentil75 = $diassonda[$posiper75][6];                            // percentil 75
    }
    else
    {
       echo "<h3><b>NO HAY DATOS PARA ESTE INFORME</b></h3>";
       exit;
    }
    
    echo "<center><table border=1 width=500>";// este es el encabezado del resultado
	  echo "<tr><td align=center colspan=1 rowspan=3><font size=3><img src='/matrix/images/medical/invecla/INVECLA.jpg' WIDTH=100 HEIGHT=80></font></td>";
		echo "<td align=center colspan=5 rowspan=1><font size=2><b>REPORTE UTILIZACI�N DE SONDAS VESICALES</b></font></td></tr>";
		echo "<tr><td align=center colspan=5 rowspan=1><font size=2>Servicio: <b>".$servicio."</b></font></td></tr>";
		echo "<tr><td align=center colspan=5 rowspan=1><font size=2>Desde: <b>".$fecha1."</b> hasta: <b>".$fecha2."</b></font></td></tr>";
		echo "<tr><td colspan=6></td></tr>";
    echo "<tr><td colspan=5 rowspan=1 width=380><font size=3>N�mero de sondas vesicales</font></td>";
    echo "<td align=center><b>".number_format($n,0,"",".")."</b>";
    echo "<tr><td colspan=5 rowspan=1 width=380><font size=3>D�as de uso</font></td>";
    echo "<td align=center><b>".number_format($diasuso,0,"",".")."</b></tr>";
    echo "<tr><td colspan=5 rowspan=1 width=380><font size=3>D�as estancia</font></td>";
    echo "<td align=center><b>".number_format($diasestancia,0,"",".")."</b></tr>";
    echo "<tr><td colspan=5 rowspan=1 width=380><font size=3>Promedio d�as de uso  </font><font size=1> (D�as uso / N�mero de sondas)</font></td>";
    echo "<td  align=center><b>".number_format($promuso,1,"",".")."</b></tr>";
    echo "<tr><td colspan=5 rowspan=1 width=380><font size=3>Tasa de uso   </font><font size=1> (D�as uso / D�as estancia)</font></td>";
    echo "<td align=center><b>".number_format($propuso,1,"",".")."</b></tr>";
    echo "<tr><td colspan=5 rowspan=1 width=380><font size=3>Percentil 25 dias de uso</font></td>";
    echo "<td align=center><b>".number_format($percentil25,0,"",".")."</b></tr>";
    echo "<tr><td colspan=5 rowspan=1 width=380><font size=3>Percentil 50 dias de uso</font></td>";
    echo "<td  align=center><b>".number_format($percentil50,0,"",".")."</b></tr>";
    echo "<tr><td colspan=5 rowspan=1 width=380><font size=3>Percentil 75 dias de uso</font></td>";
    echo "<td align=center><b>".number_format($percentil75,0,"",".")."</b></tr></table>";
  
  }
  
}  

  
  

  


