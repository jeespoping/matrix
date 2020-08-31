<html>
<head>
  	<title>Reporte de bonos</title>
</head>
<body  BGCOLOR="FFFFFF">
<font face='arial'>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
//==================================================================================================================================
//PROGRAMA						:Reporte de bonos 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:2008-05-15
//FECHA ULTIMA ACTUALIZACION 	:
$wactualiz="2008-05-15";
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
	
	

	


	echo "<form name=rep_bonos action='' method=post>";
	
	if (!isset($wfec_f) or !isset($wfec_i) or !isset($bono))
	   {
	   	$wfecha=date("Y-m-d");// esta es la fecha actual
        echo "<br><br><br>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><font size=5><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></font></td></tr>";
		echo "<tr><td><br></td></tr>";
		echo "<tr><td align=center colspan=2><font size=5>REPORTE DE BONOS</font></td></tr>";
		echo "<tr><td td bgcolor=#dddddd align=center colspan=2><b>Tipo de Bono:</b> <select name='bono'>";
		// query para traerme los bonos 
        $query =  " SELECT distinct Venbon 
                      FROM ".$wbasedato."_000016 " .
                     "WHERE Venbon not in ('NO APLICA - NO APLICA', '') " .
                  " ORDER BY 1";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);
      	
      	echo "<option>*-TODOS LOS BONOS</option>";
	       for ($i=1;$i<=$num;$i++)
	        {
	            $row = mysql_fetch_array($err);
	            echo "<option>".$row[0]."</option>";
	        }
        echo "</select></td></tr>";  
        //echo "<input type='hidden' name='codi' value='".$row[0]."'>";
        echo "<tr><td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_i' value=".$wfecha." SIZE=10></td>";
	    echo "<td bgcolor=#dddddd align=center><b>Fecha Final (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_f' value=".$wfecha." SIZE=10></td>";
	    echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	   }
	else
	   {
	   		if ($bono=='*-TODOS LOS BONOS')
	   		{
	   			$bono='%';
	   		}
	   		
	   			// este query me trae los datos de las ventas que se hicieron con esos bonos
		   		$query =  " SELECT Venbon, count(*), sum(Venvto), sum(Vendes)    
			                  FROM ".$wbasedato."_000016 
			                 WHERE Venfec between '".$wfec_i."' and '".$wfec_f."' 
			              	   AND Venbon like '".$bono."' " .
			              	 " AND Venbon not in ('NO APLICA - NO APLICA', '') " .
			                 " AND Venest = 'on'" .
			               " GROUP BY 1 
			                 ORDER BY 1 ";
		        $err = mysql_query($query,$conex);
		        $num = mysql_num_rows($err);
		        //echo $query;
		        //echo mysql_errno() ."=". mysql_error();
		        
		        echo "<center><table border=1>";
			    echo "<tr><td align=center ><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png'></td></tr>";
			    echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>REPORTE DE BONOS</b></font><br><font size=1 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";
				echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>FECHA INICIAL: <i>".$wfec_i."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$wfec_f."</i></b></font></b></font></td></tr>";
				
				if ($bono=='%')
				{
					$bono='*-TODOS LOS BONOS';
				}
				echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>TIPO DE BONO: ".$bono."</b></td></tr>";
				echo "</table>";
				echo "<br>";
				echo "<center><table border=0>";
				
				$totc=0;
				$totv=0;
		        $totd=0;
				echo "<tr bgcolor=#cccccc><td><b>TIPO DE BONO</b></td><td><b>CANTIDAD</b></td><td><b>VALOR VENTAS</b></td><td><b>VALOR DESCUENTOS</b></td></tr>";
		        for ($i=1;$i<=$num;$i++)
		         {
		            if (is_int ($i/2))
	                $wcf="DDDDDD";
	                else
	                $wcf="CCFFFF";
		            
		            $row = mysql_fetch_array($err);
		            
		            $arr[$i]['bono']=$row[0];
		            $arr[$i]['cantidad']=$row[1];
		            $arr[$i]['valven']=$row[2];
		            $arr[$i]['valdes']=$row[3];
		            
		            
		          $totc=$totc+$arr[$i]['cantidad'];
		          $totv=$totv+$arr[$i]['valven'];
		          $totd=$totd+$arr[$i]['valdes'];
		           if ($arr[$i]['bono'] != '' and $arr[$i]['bono'] != 'NO APLICA - NO APLICA')// para que solo imprima las que tienen bonos
		           {
		           		echo "<tr bgcolor=".$wcf."><td>".$arr[$i]['bono']."</td><td align=right>".$arr[$i]['cantidad']."</td><td align=right>".number_format($arr[$i]['valven'],0,'.',',')."</td><td align=right>".number_format($arr[$i]['valdes'],0,'.',',')."</td></tr>";
	               }
		           
	         	 }
	         	 echo "<tr><td align=center bgcolor=#006699><font text color=#FFFFFF><b>TOTAL DE BONOS: ".$num."</font></td><td align=right bgcolor=#006699><font text color=#FFFFFF><b>".$totc."</font></td>";
				 echo "<td align=right bgcolor=#006699><font text color=#FFFFFF><b>".number_format($totv,0,'.',',')."</font></td><td  bgcolor=#006699 align=right><font text color=#FFFFFF><b>".number_format($totd,0,'.',',')."</td></tr>";
				 echo "</table>";
		         echo"<br>";
		         echo"<br>";
		        
		  }
}
?>
</body>
</html>