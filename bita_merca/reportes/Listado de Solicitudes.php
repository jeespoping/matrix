<html>
<head>
<title>BITACORA</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
 session_start();
 if(!session_is_registered("user"))
 echo "error";
 else
 { 
  $key = substr($user,2,strlen($user));
  include("conex.php");
  mysql_select_db("matrix");
  echo "<form action='listado de Solicitudes.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  echo "<input type='HIDDEN' NAME= 'fechact' value='".$wfecha."'>";
  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  
  echo "<center><table border=2>";
  if (!isset($wfecini) or !isset($wfecfin))
   {
   	echo "<tr>";  
    echo "<td bgcolor=".$wcf." align=center colspan=3><b><font text color=".$wclfg." size=5>Reporte de Solicitudes </font><font size=2>Ver. 2008-02-08</font></b></td>";
    echo "</tr>";
	echo "<tr>";         
    echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Estado: </font></b><br><select name='estado'>";//////////////seleccionar el estado
    echo "<option>*TODOS</option>";
	 $query =  " SELECT nomest
	             FROM bitamer_000002 
	             ORDER BY nomest ";
	 $err = mysql_query($query,$conex);
	 $num = mysql_num_rows($err);

	 for ($i=1;$i<=$num;$i++)
	        {
	            $row = mysql_fetch_array($err);
	            echo "<option>".$row[0]."</option>";
	        }
	        echo "</select></td>"; 
       
    echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Unidad que solicita: </font></b><br><select name='unidad'>";/////////////////////seleccionar la unidad
    echo "<option>*TODAS</option>";
    $query =  " SELECT Unidad
	             FROM bitamer_000005 
	             ORDER BY Unidad ";
	 $err = mysql_query($query,$conex);
	 $num = mysql_num_rows($err);

	 for ($i=1;$i<=$num;$i++)
	        {
	            $row = mysql_fetch_array($err);
	            echo "<option>".$row[0]."</option>";
	        }
	        echo "</select></td>"; 
	        
    echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Quien soluciona: </font></b><br><select name='soluciona'>";/////////////////////seleccionar quien lo soluciona
    echo "<option>*TODOS</option>";   
     $query =  " SELECT Soluciona
	             FROM bitamer_000004 
	             ORDER BY Soluciona ";
	 $err = mysql_query($query,$conex);
	 $num = mysql_num_rows($err);

	 for ($i=1;$i<=$num;$i++)
	        {
	            $row = mysql_fetch_array($err);
	            echo "<option>".$row[0]."</option>";
	        }
	        echo "</select></td>"; 
	       
    echo "</tr>";
    echo "<tr>";  
    echo "<td bgcolor=".$wcf." align=center colspan=3><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecha." SIZE=10>
    		&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<b>Fecha Final (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecha." SIZE=10></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=center bgcolor=#cccccc colspan=3><input type='submit' value='OK'></td>";                                         //submit
    echo "</tr>";
   }
  else 
     {
      ///////////////////////////////////////////////////////////////////////
	  // 			ACA COMIENZA LA IMPRESION DEL REPORTE 
	  ///////////////////////////////////////////////////////////////////////
 	
 	
	  $fecact=date("Y-m-d"); /////////////////////esta es la fecha actual para hacer el calculo de la semaforizacion
 	  $segact=mktime(0,0,0,date('m'),date('d'),date('Y'));
 	  
	  
 
	  echo "<br>";
	  echo "<center><table border=0>";
	  //echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=340 HEIGHT=100></td></tr>";
      echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE SOLICITUDES</b></font></td></tr>";
	  echo "<tr>";  
      echo "<td bgcolor=".$wcf." align=left><b><font >Desde</b> <i>".$wfecini."</i> <b>Hasta</b> <i>".$wfecfin."</i></font></td></tr>";
      echo "<td bgcolor=".$wcf." align=left><b><font >Unidad que solicita:</b> <i>".$unidad."</i></font></td></tr>";
      echo "<td bgcolor=".$wcf." align=left><b><font >Quien soluciona:</b> <i>".$soluciona."</i></font></td></tr>";
      echo "<td bgcolor=".$wcf." align=left><b><font >Estado:</b> <i>".$estado."</i></font></td></tr>";
      echo "</table>";
      
     $tot=0;///////////////////////////////////aca inicializo el contador del total de los registros
     $totver=0;// inicializo el contador de semaforos en verde
     $totama=0;// inicializo el contador de semaforos en amarillo
     $totred=0;// inicializo el contador de semaforos en rojo
      ////////////////////////////////////////////////////////////////////aca vienen las unidades
      if ($unidad=='*TODAS'){

      	if ($estado !='*TODOS' and $soluciona !='*TODOS'){
      		
      		$q = "SELECT DISTINCT Unidad_que_solicita
		      	FROM ".$wbasedato."_000001 
	          	WHERE fecha between '".$wfecini."'
	          	AND '".$wfecfin."'
	          	AND Estado = '".$estado."'
	          	AND Quien_soluciono = '".$soluciona."'
	          	ORDER BY 1";
      		
      		}else if ($soluciona !='*TODOS'){
      	 	
      	 	$q = "SELECT DISTINCT Unidad_que_solicita
		      	FROM ".$wbasedato."_000001 
	          	WHERE fecha between '".$wfecini."'
	          	AND '".$wfecfin."'
	          	AND Quien_soluciono = '".$soluciona."'
	          	ORDER BY 1";
      	 }
      		
      		else if ($estado !='*TODOS'){
      		
      		$q = "SELECT DISTINCT Unidad_que_solicita
		      	FROM ".$wbasedato."_000001 
	          	WHERE fecha between '".$wfecini."'
	          	AND '".$wfecfin."'
	          	AND Estado = '".$estado."'
	          	ORDER BY 1";
      		
      		}else{
      	
		  $q = "SELECT DISTINCT Unidad_que_solicita
		      	FROM ".$wbasedato."_000001 
	          	WHERE fecha between '".$wfecini."'
	          	AND '".$wfecfin."'
	          	ORDER BY 1";
      		} 
      }else
      {
      	
	      	$q = "SELECT DISTINCT Unidad_que_solicita
			      	FROM ".$wbasedato."_000001 
		          	WHERE fecha between '".$wfecini."'
		          	AND '".$wfecfin."'
		          	AND Unidad_que_solicita = '".$unidad."'
		          	ORDER BY 1 ";
      }
       
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($err);
	
	  echo "<br>";
	  echo "<table border=1>";

	  for ($i=0;$i<$num;$i++)
	     {
	      $row = mysql_fetch_array($err);
	      $arr[$i]['uni']=$row[0];
	      
	      if ($i !=0)
	      echo "<tr><td colspan=8>&nbsp</td></tr>";
	      echo "<tr><td bgcolor=#666666   colspan=8><font size=3 color=#FFFFFF><b>UNIDAD: ".$row[0]."</b></font></td></tr>"; 
	      
	      ////////////////////////////////////////////////////////////////////aca vienen los estados
	      if ($estado=='*TODOS'){
      	
		  $q = "SELECT DISTINCT Estado
			      	FROM ".$wbasedato."_000001 
		          	WHERE fecha between '".$wfecini."'
		          	AND '".$wfecfin."'
		          	AND Unidad_que_solicita = '". $arr[$i]['uni']."'
		          	ORDER BY 1 ";
		  
		      }else if($estado!='*TODOS'){
		      	
			      	$q = "SELECT DISTINCT Estado
					      	FROM ".$wbasedato."_000001 
				          	WHERE fecha between '".$wfecini."'
				          	AND '".$wfecfin."'
				          	AND Unidad_que_solicita = '". $arr[$i]['uni']."'
				          	AND Estado = '". $estado."'
				          	ORDER BY 1 ";
		      }
		      	$errest = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  			$numest = mysql_num_rows($errest);
	  			
	 
	  			$totuni=0;///////////////////////////////////aca inicializo el contador del total de las unidades
	  			
			      for ($j=0;$j<$numest;$j++)
				     {
				      $row = mysql_fetch_array($errest);
				      $arr[$j]['est']=$row[0];
				      
				       
				     
			   						////////////////////////////////////////////////////////////////////aca vienen los registros	
								
			   						if ($soluciona=='*TODOS')
			   						{
			   						$q = "SELECT Consecutivo, Fecha, Quien_solicita, Fecha_de_solucion, Quien_soluciono,Estado, id, Fecha_De_Solucion
									      	FROM ".$wbasedato."_000001 
								          	WHERE fecha between '".$wfecini."'
								          	AND '".$wfecfin."'
								          	AND Unidad_que_solicita ='".$arr[$i]['uni']."'
								          	AND Estado ='".$arr[$j]['est']."'
								          	ORDER BY 1 ";
			   						}else {
			   							$q = "SELECT Consecutivo, Fecha, Quien_solicita, Fecha_de_solucion, Quien_soluciono, Estado, id, Fecha_De_Solucion
									      	FROM ".$wbasedato."_000001 
								          	WHERE fecha between '".$wfecini."'
								          	AND '".$wfecfin."'
								          	AND Unidad_que_solicita ='".$arr[$i]['uni']."'
								          	AND Estado ='".$arr[$j]['est']."'
								          	AND Quien_soluciono='".$soluciona."'
								          	ORDER BY 1 ";
			   						}
			   						
								     $erreg = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				  					 $numreg = mysql_num_rows($erreg);
				  					
				  					 if ($row[0] !='' and $numreg !=0)
				  					 {
				  					  echo "<tr><td bgcolor=#909090     colspan=8><font size=3 color=#FFFFFF><b>ESTADO: ".$row[0]."</b></font></td></tr>"; 
				  					  
				  					  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>SEMAFORIZACION</font></th>";
									  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>NUMERO DE DIAS</font></th>";
									  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>CONSECUTIVO</font></th>";
									  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FECHA </font></th>";
									  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>PERSONA QUE SOLICITO</font></th>";
									  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FECHA DE SOLUCION</font></th>";
									  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>QUIEN SOLUCIONA</font></th>";
									  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>REGISTRO</font></th>"; 
							     	
									  for ($l=0;$l<$numreg;$l++)
									     {
									     
									     		
									     if (is_int ($l/2))
						                 $wcf="F1F1F0 ";
						                 else
						                 $wcf="FFFFFF";	
						                 
									      $row = mysql_fetch_array($erreg);
									      $arr[$l]['reg']=$row[0];
									      $arr[$l]['fec']=$row[1];
									      $arr[$l]['est']=$row[5];
									      $arr[$l]['fsl']=$row[7];
									      
									      //////////////////////////para contar los dias cuando esta solucionado
									      
									      $fec=explode('-',$row[1]);/////////////////////////////fecha inicial
									      $fsl=explode('-',$row[7]);/////////////////////////////fecha de solucion
									      
									      $segfec=mktime(0,0,0,$fec[1],$fec[2],$fec[0]);
									      
									      //$segact=mktime(0,0,0,date('m'),date('d'),date('Y'));
									      $segsol=mktime(0,0,0,$fsl[1],$fsl[2],$fsl[0]);
									      //$totseg=$segact-$segfec;
									      $totseg=$segsol-$segfec;
									      
									      
									      ///////////////////////////////////////para cuando estan pendientes de solucion Ver. 2007-08-23
									       if( $arr[$l]['est']!='SOLUCIONADO')
										      {
										      	$segact=mktime(0,0,0,date('m'),date('d'),date('Y'));
										      	$totseg=$segact-$segfec;
										      }
									      
									      $totday=$totseg/86400;
									     
									      $arr[$l]['day']=$totday;
									      
									     
											      if ($arr[$l]['day'] < 1)
											      {
												      $color="#00CC00";/////////////////verde
												      
											      }else if(($arr[$l]['day'] >= 1))
											      	{
											      		$color="#FFFF00";/////////////////amarillo
											      		
											      		if(($arr[$l]['day'] <= 5))
												      	{
												      		$color="#FFFF00";/////////////////amarillo
												      		
												      	}
											      	else if(($arr[$l]['day'] > 5))
												      	{
												      		$color="#FF0000";/////////////////rojo
												      		
												      	}
									     			}
										      
									      //echo $totseg."=".$segact."-".$segfec;
									      $hyper="<A HREF='/matrix/det_registro.php?id=".$row[6]."&pos1=".$wbasedato."&pos2=2006-10-31&pos3=10:25:32&pos4=000001&pos5=0&pos6=".$wbasedato."&tipo=P&Valor=&Form=000001-".$wbasedato."-C-BITACORA&call=0&change=0&key=".$wbasedato."&Pagina=1'target='new'>Ver</a>";
									      echo "<tr><td bgcolor=".$color."><font size=3>&nbsp</font></td>";
									      echo "<td bgcolor=".$wcf."><font size=3>".$arr[$l]['day']."</font></td>";
									      echo "<td bgcolor=".$wcf."><font size=3>".$row[0]."</font></td>";
									      echo "<td bgcolor=".$wcf."><font size=3>".$row[1]."</font></td>";
									      echo "<td bgcolor=".$wcf."><font size=3>".$row[2]."</font></td>";
									      echo "<td bgcolor=".$wcf."><font size=3>".$row[3]."</font></td>";
									      echo "<td bgcolor=".$wcf."><font size=3>".$row[4]."</font></td>";
									      echo "<td bgcolor=".$wcf."><font size=3>".$hyper."</font></td></tr>";
									      
									      if ($color=='#00CC00')// contador verde
									     	{
									     		$totver=$totver+1;
									     	}
							     		  if ($color=='#FFFF00')// contador amarillo
									     	{
									     		$totama=$totama+1;
									     	}
								     	  if ($color=='#FF0000')// contador rojo
									     	{
									     		$totred=$totred+1;
									     	}
									      
									    }
									     
				  					 
				  					 }
						$totuni=$totuni+$numreg;
						$tot=$tot+$numreg; 
						 
						if ($numreg !=0)	     
				     	echo "<tr><td bgcolor=#909090   colspan=3><font size=2 color=#FFFFFF><b>TOTAL ".$arr[$j]['est']."</td><td bgcolor=#909090  colspan=5><font size=2 color=#FFFFFF><b> ".$numreg."</font></td></tr>";
				     	
				     	
				     }
				  
			    
				echo "<tr><td bgcolor=#666666 colspan=3><font size=2 color=#FFFFFF><b>TOTAL ".$arr[$i]['uni']."<b></td><td bgcolor=#666666 colspan=5><font size=2 color=#FFFFFF><b> ".$totuni."</font></td></tr>";      
	     		
	     }
	     
	  $porcv=((100*$totver)/$tot); // porcentaje verde
      $porca=((100*$totama)/$tot); // porcentaje amarillo
      $porcr=((100*$totred)/$tot); // porcentaje rojo
	
	  $totco=$totver+$totama+$totred;
	  $porto=$porcv+$porca+$porcr;     
	  echo "<tr><td bgcolor=#FFFFFF colspan=8><font size=3><b>&nbsp<b></font></td></tr>";   
	  echo "<tr><td bgcolor=#666666 colspan=8><font size=3 color=#FFFFFF><b>TOTAL GENERAL<b></font></td></tr>";
	  echo "<tr><td bgcolor=#666666 colspan=8><font size=3 color=#FFFFFF><b>TOTAL DE SEMAFORIZACION<b></font></td></tr>";
	  echo "<tr><td bgcolor=#909090 colspan=3><font size=2 color=#FFFFFF><b>TOTAL VERDE<b></td><td bgcolor=#00CC00 colspan=1 align=center><font size=2 color=#FFFFFF ><b> ".$totver."</td><td bgcolor=#909090 colspan=4><font size=2 color=#FFFFFF><b> ".number_format($porcv,2,'.',',')."%</font></td></tr>";
	  echo "<tr><td bgcolor=#909090 colspan=3><font size=2 color=#FFFFFF><b>TOTAL AMARILLO<b></td><td bgcolor=#FFFF00 colspan=1 align=center><font size=2 color=#FFFFFF><b> ".$totama."</td><td bgcolor=#909090 colspan=4><font size=2 color=#FFFFFF><b> ".number_format($porca,2,'.',',')."%</font></td></tr>";
	  echo "<tr><td bgcolor=#909090 colspan=3><font size=2 color=#FFFFFF><b>TOTAL ROJO<b></td><td bgcolor=#FF0000 colspan=1 align=center><font size=2 color=#FFFFFF><b> ".$totred."</td><td bgcolor=#909090 colspan=4><font size=2 color=#FFFFFF><b> ".number_format($porcr,2,'.',',')."%</font></td></tr>";
	  echo "<tr><td bgcolor=#909090 colspan=3><font size=2 color=#FFFFFF><b>TOTAL <b></td><td bgcolor=#909090 colspan=1 align=center><font size=2 color=#FFFFFF><b> ".$totco."</td><td bgcolor=#909090 colspan=4><font size=2 color=#FFFFFF><b> ".number_format($porto,2,'.',',')."%</font></td></tr>";
	  echo "<tr><td bgcolor=#FFFFFF colspan=8><font size=3><b>&nbsp<b></font></td></tr>"; 
	     
	  if ($soluciona=='*TODOS' and $estado=='*-TODOS' AND $unidad=='*-TODAS')
		{	  
			$q = "SELECT  DISTINCT Estado
					FROM ".$wbasedato."_000001 
					WHERE fecha between '".$wfecini."'
					AND '".$wfecfin."'";
					
					echo "todos1";
			
		}else {
			
			  if ($soluciona=='*TODOS')
			  {
			  	$soluciona='%';
			  }
			  if ($estado=='*TODOS')
			  {
			  	$estado='%';
			  }
			  if ($unidad=='*TODAS')
			  {
			  	$unidad='%';
			  }
			  $q = "SELECT DISTINCT Estado
					FROM ".$wbasedato."_000001 
					WHERE fecha between '".$wfecini."'
					AND '".$wfecfin."'
					AND Quien_soluciono like '".$soluciona."' 
					AND Unidad_que_solicita like '".$unidad."' 
					AND Estado like '".$estado."'";
			  }
			   						
	   $erreg = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	   
	   $nr = mysql_num_rows($erreg);
		
	 for ($k=0;$k<$nr;$k++)
		{
			$row = mysql_fetch_array($erreg);
		
			if ($soluciona=='*TODOS' and $estado=='*-TODOS' AND $unidad=='*-TODAS')
			{	  
				$q = "SELECT  *
						FROM ".$wbasedato."_000001 
						WHERE fecha between '".$wfecini."'
						AND '".$wfecfin."'
						AND Estado='".$row[0]."'";
				
			}else {
					 if ($soluciona=='*TODOS')
					  {
					  	$soluciona='%';
					  }
					  if ($estado=='*TODOS')
					  {
					  	$estado='%';
					  }
					  if ($unidad=='*TODAS')
					  {
					  	$unidad='%';
					  }
					 
					 $q = "SELECT *
							FROM ".$wbasedato."_000001 
							WHERE fecha between '".$wfecini."'
							AND '".$wfecfin."'
							AND Quien_soluciono like '".$soluciona."'
							AND Unidad_que_solicita like '".$unidad."' 
							AND Estado like '".$estado."'
							AND Estado='".$row[0]."'";
				}
				
			 $erret = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	   
	  		 $nest = mysql_num_rows($erret);
			$porc=((100*$nest)/$tot); 
	  		echo "<tr><td bgcolor=#909090 colspan=3><font size=2 color=#FFFFFF><b>TOTAL ".$row[0]."<b></td><td bgcolor=#909090 colspan=1><font size=2 color=#FFFFFF><b> ".$nest."</td><td bgcolor=#909090 colspan=4><font size=2 color=#FFFFFF><b> ".number_format($porc,2,'.',',')."%</font></td></tr>";
		}
		
	  echo "<tr><td bgcolor=#666666 colspan=3><font size=3 color=#FFFFFF><b>TOTAL SOLICITUDES</td><td bgcolor=#666666 colspan=1><font size=3 color=#FFFFFF><b> ".$tot." </td><td bgcolor=#666666 colspan=4><font size=3 color=#FFFFFF><b> 100.00%</font></td></tr>";	
	  echo "</table>";
	  
     } 
}
?>
</body>
</html>