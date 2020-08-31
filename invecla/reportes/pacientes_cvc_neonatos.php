
<html>
<head>
  	<title>Reporte utilización de catéteres venosos centrales unidad de Neonatos</title>
</head>
<body  BGCOLOR="FFFFFF">
<font face='arial'>
<BODY TEXT="#000066">

<?php
include_once("conex.php");
//==================================================================================================================================
//PROGRAMA						:Utilización de Catéteres Venosos Centrales
//AUTOR						    :Nancy Estella González G.
//FECHA CREACION			:2008-02-12
//FECHA ULTIMA ACTUALIZACION 	:2008-05-03
$wactualiz="2008-05-03";
//==================================================================================================================================
//ACTUALIZACIONES
/* Se organizó el contador de catéteres para que se incluyan los catéteres que quedan instalados despues de que el
   paciente es trasladado a otra unidad o los que están abiertos a la fecha de corte del informe.
*/   
//==================================================================================================================================
/*
   2008-05-03 Se cambio en las consultas donde se tomaban datos de los formulario cominf_000032 y cominf_000033 por
   movhos_000032 y movhos_000033
*/
//==================================================================================================================================
// 
//==================================================================================================================================


session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	
	

	


	echo "<form name=utilizacion_cateteres_neonatos action='' method=post>";
	$wbasedato='cominf';
	$wbasedato1='movhos';


	// ENCABEZADO
	if (!isset ($fecha2))
	{
	  $wfecha=date("Y-m-d");// esta es la fecha actual

    echo "<br><br><br>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><font size=5><img src='/matrix/images/medical/invecla/INVECLA.jpg' WIDTH=100 HEIGHT=70></font></td></tr>";
		echo "<tr><td><br></td></tr>";
		echo "<tr><td align=center colspan=2><font size=4>UTILIZACIÓN DE CATÉTERES VENOSOS CENTRALES - NEONATOS</font></td></tr>";
	  echo "<tr><td><br></td></tr>";
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
  
     $cpeso0=0;  // Contador pacientes con peso 0 gramos; es decir los que no fueron registrados en el formulario de datos generales del neonato
     $cpeso01=0; // Contador pacientes con peso entre 1 y 750 gramos
     $cpeso1=0;  // Contador pacientes con peso entre 751 y 1000 gramos
     $cpeso2=0;  // Contador pacientes con peso entre 1001 y 1500 gramos
     $cpeso3=0;  // Contador pacientes con peso entre 1501 y 2500 gramos
     $cpeso4=0;  // Contador pacientes con peso mayor a 2501 gramos
     $speso0=0;  // Acumulador de dias cateter para pacientes con peso 0 gramos
     $speso01=0; // Acumulador de dias cateter para pacientes con peso entre 1 y 750 gramos
     $speso1=0;  // Acumulador de dias cateter para pacientes con peso entre 751 y 1000 gramos
     $speso2=0;  // Acumulador de dias cateter para pacientes con peso entre 1001 y 1500 gramos
     $speso3=0;  // Acumulador de dias cateter para pacientes con peso entre 1501 y 2500 gramos
     $speso4=0;  // Acumulador de dias cateter para pacientes con peso mayor a 2501 gramos
     $DEpeso0=0; // Acumulador de dias estancia para pacientes con peso 0 gramos
     $DEpeso01=0;// Acumulador de dias estancia para pacientes con peso entre 1 y 750 gramos
     $DEpeso1=0; // Acumulador de dias estancia para pacientes con peso entre 751 y 1000 gramos
     $DEpeso2=0; // Acumulador de dias estancia para pacientes con peso entre 1001 y 1500 gramos
     $DEpeso3=0; // Acumulador de dias estancia para pacientes con peso entre 1501 y 2500 gramos
     $DEpeso4=0; // Acumulador de dias estancia para pacientes con peso mayor a 2501 gramos
     $diasestancia=0;
      
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
           
     //query para traer todos los pacientes que han ingresado a Neonatos tres meses antes de la fecha1 de consulta     
      
     $query1 = "SELECT ".$wbasedato1."_000032.Historia_clinica, ".$wbasedato1."_000032.Num_ingreso, ".$wbasedato1."_000032.Servicio, ".$wbasedato1."_000032.Fecha_ing, ".$wbasedato1."_000032.Hora_ing
                  FROM ".$wbasedato1."_000032
                 WHERE ".$wbasedato1."_000032.Fecha_ing >= '".$faux."' 
                   AND ".$wbasedato1."_000032.Fecha_ing < '".$fecha1."' 
                   AND substring(".$wbasedato1."_000032.Servicio,1,4) = '1190'
              ORDER BY 1,2,4,5";              
     $err1 = mysql_query($query1,$conex);
     $numero = mysql_num_rows($err1);        
     
     for ($i=1;$i<=$numero;$i++)
     {
       $registros = mysql_fetch_array($err1);
       $pac[$i][0]= $registros['Historia_clinica'];
       $pac[$i][1]= $registros['Num_ingreso'];
       $pac[$i][2]= $registros['Servicio'];
       $pac[$i][3]= $registros['Fecha_ing'];
       $pac[$i][4]= $registros['Hora_ing']; 
       $pac[$i][5]= '0';      
       //echo $pac[$i][0].",".$pac[$i][1].",".$pac[$i][2].",".$pac[$i][3].",".$pac[$i][4].",".$pac[$i][5];
       //echo "<br>";       
     }  
 
 
      //query para traer todos los pacientes que han egresado de Neonatos tres meses antes de la fecha1 de consulta   
            
     $query2= "SELECT ".$wbasedato1."_000033.Historia_clinica, ".$wbasedato1."_000033.Num_ingreso, ".$wbasedato1."_000033.Servicio, ".$wbasedato1."_000033.Fecha_egre_serv,  ".$wbasedato1."_000033.Hora_egr_serv
                 FROM ".$wbasedato1."_000033
                WHERE ".$wbasedato1."_000033.Fecha_egre_serv >= '".$faux."' 
                  AND ".$wbasedato1."_000033.Fecha_egre_serv < '".$fecha1."' 
                  AND substring(".$wbasedato1."_000033.Servicio,1,4) = '1190'
              ORDER BY 1,2,4,5";
     $err2 = mysql_query($query2,$conex);
     $numero1 = mysql_num_rows($err2);
            
     for ($i=1;$i<=$numero1;$i++)
     {
       $array1 = mysql_fetch_array($err2);
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
         $datos[$p][0]=$pac[$i][0];  //Historia clínica
         $datos[$p][1]=$pac[$i][1];  //Número de ingreso     
         $datos[$p][2]=$pac[$i][2];  //Servicio            
         $datos[$p][3]=$fecha1;      //Fecha de ingreso
         $datos[$p][4]='00:00:00';   //Hora de ingreso                             
       }
     }      

     //query para traer todos los pacientes que han ingresado a Neonatos entre las fechas de consulta
     
     $query3 = "SELECT ".$wbasedato1."_000032.Historia_clinica, ".$wbasedato1."_000032.Num_ingreso, ".$wbasedato1."_000032.Servicio, ".$wbasedato1."_000032.Fecha_ing, ".$wbasedato1."_000032.Hora_ing
                  FROM ".$wbasedato1."_000032
                 WHERE ".$wbasedato1."_000032.Fecha_ing >= '".$fecha1."' 
                   AND ".$wbasedato1."_000032.Fecha_ing <= '".$fecha2."' 
                   AND substring(".$wbasedato1."_000032.Servicio,1,4) = '1190'
              ORDER BY 1,2,4,5";    
     $err3 = mysql_query($query3,$conex);
     $numreg = mysql_num_rows($err3);
               
     for ($i=1;$i<=$numreg;$i++)
     {
       $p++;
       $registros = mysql_fetch_array($err3);
       $datos[$p][0]= $registros['Historia_clinica'];
       $datos[$p][1]= $registros['Num_ingreso'];
       $datos[$p][2]= $registros['Servicio'];
       $datos[$p][3]= $registros['Fecha_ing'];
       $datos[$p][4]= $registros['Hora_ing'];       
       //echo $datos[$i][0].",".$datos[$i][1].",".$datos[$i][2].",".$datos[$i][3].",".$datos[$i][4];
       //echo "<br>";
     } 
     
     $numreg = $p;

     //query para traer todos los pacientes que han egresado de Neonatos entre las fechas de consulta
     
     $query4 = "SELECT ".$wbasedato1."_000033.Historia_clinica, ".$wbasedato1."_000033.Num_ingreso, ".$wbasedato1."_000033.Servicio, ".$wbasedato1."_000033.Fecha_egre_serv,  ".$wbasedato1."_000033.Hora_egr_serv
                  FROM ".$wbasedato1."_000033
                 WHERE ".$wbasedato1."_000033.Fecha_egre_serv >= '".$fecha1."' 
                   AND ".$wbasedato1."_000033.Fecha_egre_serv <= '".$fecha2."' 
                   AND substring(".$wbasedato1."_000033.Servicio,1,4) = '1190'
              ORDER BY 1,2,4,5";
     $err4 = mysql_query($query4,$conex);
     $numreg1 = mysql_num_rows($err4);

     for ($i=1;$i<=$numreg1;$i++)
     {
       $registros = mysql_fetch_array($err4);
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
            $datos[$numreg][0]=$datos1[$i][0];  //Historia clínica
            $datos[$numreg][1]=$datos1[$i][1];  //Número de ingreso a la clínica
            $datos[$numreg][2]=$datos1[$i][2];  //Servicio
            $datos[$numreg][3]=$fecha1;         //Fecha de ingreso al servicio  
            $datos[$numreg][4]='00:00:00';      //Hora de ingreso al servicio
            $datos[$numreg][5]=$datos1[$i][3];  //Fecha de egreso del servicio
            $datos[$numreg][6]=$datos1[$i][4];  //Hora de egreso del servicio
         }
      }
      
      /*for ($i=1;$i<=$numreg;$i++)
      {
       //echo $datos[$i][0].",".$datos[$i][1].",".$datos[$i][2].",".$datos[$i][3].",".$datos[$i][4].",".$datos[$i][5].",".$datos[$i][6];
       //echo "<br>";      
      }*/
      

      // Calculo de los dias estancia
      
      for ($i=1;$i<=$numreg;$i++)
      {              
          $fing = explode("-",$datos[$i][3]);     //Fecha de ingreso 
          $fegr = explode("-",$datos[$i][5]);     //Fecha de egreso 
          $hing = explode(":",$datos[$i][4]);     //Hora de ingreso
          $hegr = explode(":",$datos[$i][6]);     //Hora de egreso
          $seg= mktime($hegr[0],$hegr[1],$hegr[2],$fegr[1],$fegr[2],$fegr[0])- mktime($hing[0],$hing[1],$hing[2],$fing[1],$fing[2],$fing[0]);
          $diasest=round(($seg/86400),1);  
          $datos[$i][7]= abs($diasest);                   //Cálculo de los dias estancia 
          $diasestancia = $diasestancia + $datos[$i][7];  //Acumulador dias estancia
          
          //echo $datos[$i][0].",".$datos[$i][1].",".$datos[$i][2].",".$datos[$i][3].",".$datos[$i][4].",".$datos[$i][5].",".$datos[$i][6].",".$datos[$i][7];
          //echo "<br>";    
      }
  
     // Este query es para buscar el peso del neonato en la tabla "Datos generales del Neonato" para luego sumar los dias estancia por cada peso

     for ($i=1;$i<=$numreg;$i++)
     {
        $query5 = "SELECT Peso_nac_gr
                     FROM ".$wbasedato."_000030
                    WHERE ".$wbasedato."_000030.Historia_clinica = '".$datos[$i][0]."' 
                      AND ".$wbasedato."_000030.Num_ingreso = '".$datos[$i][1]."' ";

        $err5 = mysql_query($query5);
        $nreg = mysql_num_rows($err5); 
        
        if ($nreg!=0)
        {
          $peso = mysql_fetch_array($err5);
          $datos[$i][8] = $peso['Peso_nac_gr'];   // Se agrega al registro el peso del neonato
        }
        else  // Esta opción es para los neonatos que no se les hizo registro en el formulario datos del neonato
        {   
          $datos[$i][8] = 0;   // Se le asigna un peso de (0)
        }     
        
        if ($datos[$i][8]==0)
        {
           $DEpeso0=$DEpeso0 + $datos[$i][7];// suma los dias estancia de los pacientes que tienen peso 0 gramos
        }
        elseif ($datos[$i][8]>=1 and $datos[$i][8]<=750)
        {
           $DEpeso01=$DEpeso01 + $datos[$i][7];// suma los dias estancia de los pacientes que tienen peso entre 1 y 1000 gramos
        }        
        elseif ($datos[$i][8]>=751 and $datos[$i][8]<=1000)
        {
           $DEpeso1=$DEpeso1 + $datos[$i][7];// suma los dias estancia de los pacientes que tienen peso entre 1 y 1000 gramos
        }
        elseif ($datos[$i][8]>=1001 and $datos[$i][8]<=1500)
        {
           $DEpeso2=$DEpeso2 + $datos[$i][7];// suma los dias estancia de los pacientes que tienen peso entre 1001 y 1500 gramos
        }
        elseif ($datos[$i][8]>=1501 and $datos[$i][8]<=2500)
        {
           $DEpeso3=$DEpeso3 + $datos[$i][7];// suma los dias estancia de los pacientes que tienen peso entre 1501 y 2500 gramos
        }        
        elseif ($datos[$i][8]>=2501)
        {
           $DEpeso4=$DEpeso4 + $datos[$i][8];// suma los dias estancia de los pacientes que tienen peso mayor a 2501 gramos
        }               
          //echo $datos[$i][0].",".$datos[$i][1].",".$datos[$i][2].",".$datos[$i][3].",".$datos[$i][4].",".$datos[$i][5].",".$datos[$i][6].",".$datos[$i][7].",".$datos[$i][8];
          //echo "<br>";
     }
     
     /* Este query es para traerle a los pacientes que estuvieron hospitalizados en el servicio y las fechas
        de consulta los CVC que se les instalaron     
     */
     
     $p=0; 
     for ($i=1;$i<=$numreg;$i++)
     {
        $query6 = "SELECT Historia_clinica, Num_ingreso, Num_cat, Proteccion, Via, Lumen, Fecha_instala, fecha_retiro
                     FROM ".$wbasedato."_000025
                    WHERE ".$wbasedato."_000025.Historia_clinica = '".$datos[$i][0]."'
                      AND ".$wbasedato."_000025.Num_ingreso = '".$datos[$i][1]."'
                      AND (Tipo_cateter = '01-CVC'
                       OR  Tipo_cateter = '05-CVC 4 fr'
                       OR  Tipo_cateter = '06-Epicutaneo cava'
                       OR  Tipo_cateter = '07-Epicutaneo premicath'
                       OR  Tipo_cateter = '08-Epicutaneo nutrile'
                       OR  Tipo_cateter = '09-Umbilical arterial 35'
                       OR  Tipo_cateter = '10-Umbilical venoso 4'
                       OR  Tipo_cateter = '11-Umbilical venoso 5')  
                 ORDER BY 3";
        $err6 = mysql_query($query6);
        $ncat = mysql_num_rows($err6);  
 
        if ($ncat > 0)
        {
            for ($n=1;$n<=$ncat;$n++)
            {
              $consulta = mysql_fetch_array($err6);

              $p++;
              $cateteres[$p][0]= $consulta['Historia_clinica'];
              $cateteres[$p][1]= $consulta['Num_ingreso'];
              $cateteres[$p][2]= $datos[$i][3]; // Fecha inicial (ingreso) con la que se comparará para sacar los días catéter
              $cateteres[$p][3]= $datos[$i][5]; // Fecha final (egreso) con la que se comparará para sacar los días catéter        
              $cateteres[$p][4]= $consulta['Num_cat'];
              $cateteres[$p][5]= $consulta['Proteccion'];
              $cateteres[$p][6]= $consulta['Via'];
              $cateteres[$p][7]= $consulta['Lumen'];
              $cateteres[$p][8]= $consulta['Fecha_instala'];
              $cateteres[$p][9]= $consulta['fecha_retiro'];
            }  
        } 
    }
    
   
    /* En este ciclo se hará el cálculo de los días catéter que tuvo el paciente en el periodo de tiempo 
       y la unidad consultada
    */   
    
    for ($i=1;$i<=$p;$i++)
    {
      $finst= explode("-",$cateteres[$i][8]);   // Fecha de instalación del catéter
      $freti= explode("-",$cateteres[$i][9]);  // Fecha de retiro del cáteter
      $fecin= explode("-",$cateteres[$i][2]);   // Fecha inicial de comparación
      $fefin= explode("-",$cateteres[$i][3]);   // Fecha final de comparación
      
      if ((($cateteres[$i][8]<$cateteres[$i][2] and $cateteres[$i][9]<$cateteres[$i][2]) or ($cateteres[$i][8]>$cateteres[$i][3] and $cateteres[$i][9]>$cateteres[$i][3])) and ($cateteres[$i][9]!='0000-00-00'))
      {
         $cateteres[$i][10]= -99;
      }
      elseif ($cateteres[$i][8]>$cateteres[$i][3] and $cateteres[$i][9]=='0000-00-00')   
      {
         $cateteres[$i][10]= -99;
      }     
      elseif ($cateteres[$i][8]>=$cateteres[$i][2] and $cateteres[$i][9]<= $cateteres[$i][3] and $cateteres[$i][9]!='0000-00-00')
      {
         $seg= mktime(0,0,0,$freti[1],$freti[2],$freti[0])- mktime(0,0,0,$finst[1],$finst[2],$finst[0]);
         $diascat=round(($seg/86400),1);  
         $cateteres[$i][10]= $diascat;
      }    
      elseif ($cateteres[$i][8]<$cateteres[$i][2] and $cateteres[$i][9]<= $cateteres[$i][3] and $cateteres[$i][9]>= $cateteres[$i][2])
      {
         $seg= mktime(0,0,0,$freti[1],$freti[2],$freti[0])- mktime(0,0,0,$fecin[1],$fecin[2],$fecin[0]);    
         $diascat=round(($seg/86400),1);  
         $cateteres[$i][10]= $diascat;
      }
      elseif ($cateteres[$i][8]>=$cateteres[$i][2] and $cateteres[$i][9]>$cateteres[$i][3])
      {
         $seg= mktime(0,0,0,$fefin[1],$fefin[2],$fefin[0])- mktime(0,0,0,$finst[1],$finst[2],$finst[0]);    
         $diascat=round(($seg/86400),1);  
         $cateteres[$i][10]= $diascat;
      }
      elseif ($cateteres[$i][8]<$cateteres[$i][2] and $cateteres[$i][9]>$cateteres[$i][3])
      {
         $seg= mktime(0,0,0,$fefin[1],$fefin[2],$fefin[0])- mktime(0,0,0,$fecin[1],$fecin[2],$fecin[0]);
         $diascat=round(($seg/86400),1);  
         $cateteres[$i][10]= $diascat;
      }  
      elseif ($cateteres[$i][8] == $cateteres[$i][9] and $cateteres[$i][8]>=$cateteres[$i][2] and $cateteres[$i][9]<=$cateteres[$i][3])
      {
         $seg= mktime(0,0,0,$freti[1],$freti[2],$freti[0])- mktime(0,0,0,$finst[1],$finst[2],$finst[0]); 
         $diascat=round(($seg/86400),1);  
         $cateteres[$i][10]= $diascat;
      }   
      elseif ($cateteres[$i][8]<$cateteres[$i][2] and $cateteres[$i][9]=='0000-00-00')
      {
         $seg= mktime(0,0,0,$fefin[1],$fefin[2],$fefin[0])- mktime(0,0,0,$fecin[1],$fecin[2],$fecin[0]);
         $diascat=round(($seg/86400),1);  
         $cateteres[$i][10]= $diascat;
      }   
      elseif ($cateteres[$i][8]>=$cateteres[$i][2] and $cateteres[$i][9]=='0000-00-00')
      {
         $seg= mktime(0,0,0,$fefin[1],$fefin[2],$fefin[0])- mktime(0,0,0,$finst[1],$finst[2],$finst[0]);  
         $diascat=round(($seg/86400),1);  
         $cateteres[$i][10]= $diascat;
      }   
      
         //echo $cateteres[$i][0].",".$cateteres[$i][1].",".$cateteres[$i][2].",".$cateteres[$i][3].",".$cateteres[$i][4].",".$cateteres[$i][5].",".$cateteres[$i][6].",".$cateteres[$i][7].",".$cateteres[$i][8].",".$cateteres[$i][9].",".$cateteres[$i][10];
         //echo "<br>";
    } 

    /* Este ciclo es para eliminar los registros de los pacientes que tuvieron CVC pero que están por
       fuera del rango de consulta
    */ 
    
    $n=0; 
    for ($i=1;$i<=$p;$i++)
    {
      if ($cateteres[$i][10] >=0 )
      {
          $n++;
          $fila[$n][0]= $cateteres[$i][0];   // Historia clínica
          $fila[$n][1]= $cateteres[$i][1];   // Número de ingreso
          $fila[$n][2]= $cateteres[$i][4];   // Número de catéter
          $fila[$n][3]= $cateteres[$i][5];   // Protección
          $fila[$n][4]= $cateteres[$i][6];   // Vía
          $fila[$n][5]= $cateteres[$i][7];   // Lumen 
          $fila[$n][6]= $cateteres[$i][8];   // Fecha de instalación
          $fila[$n][7]= $cateteres[$i][9];  // Fecha de retiro
          $fila[$n][8]= $cateteres[$i][10];  // Días catéter en el período de consulta y estancia en la unidad
          echo $fila[$n][0].",".$fila[$n][1].",".$fila[$n][2].",".$fila[$n][3].",".$fila[$n][4].",".$fila[$n][5].",".$fila[$n][6].",".$fila[$n][7].",".$fila[$n][8];
          echo "<br>";
      }
    } 
  }
}	

?>
