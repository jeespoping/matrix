<html>
<head>
  	<title>Reporte de ventas por empresas</title>
</head>
<body  BGCOLOR="FFFFFF">
<font face='arial'>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
//==================================================================================================================================
//PROGRAMA						:Reporte de ventas por empresas 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:2008-04-08
//FECHA ULTIMA ACTUALIZACION 	:2007-05-04
$wactualiz="2008-04-08";
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
	
	

	


	echo "<form name=rep_Confac action='' method=post>";
	
	if (!isset($wfec_f) or !isset($wfec_i) or !isset($empresa))
	   {
	   	$wfecha=date("Y-m-d");// esta es la fecha actual
        echo "<br><br><br>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><font size=5><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></font></td></tr>";
		echo "<tr><td><br></td></tr>";
		echo "<tr><td align=center colspan=2><font size=5>REPORTE DE VENTAS POR EMPRESAS</font></td></tr>";
		echo "<tr><td td bgcolor=#dddddd align=center colspan=2><b>Empresa:</b> <select name='empresa'>";
		// query para traerme las empresas 
        $query =  " SELECT Empcod, Empnit, Empnom 
                      FROM ".$wbasedato."_000024 " .
                     "WHERE Empcod = Empres " .
                     "AND Empest='on' " .
                   " ORDER BY 3";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);
      
	       for ($i=1;$i<=$num;$i++)
	        {
	            $row = mysql_fetch_array($err);
	            echo "<option>".$row[0]."-".$row[1]."-".$row[2]."</option>";
	        }
        echo "</select></td></tr>";  
        //echo "<input type='hidden' name='codi' value='".$row[0]."'>";
        echo "<tr><td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_i' value=".$wfecha." SIZE=10></td>";
	    echo "<td bgcolor=#dddddd align=center><b>Fecha Final (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_f' value=".$wfecha." SIZE=10></td>";
	    echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	   }
	else
	   {
	   		$cod=explode('-',$empresa); // para que las busque por el codigo
	   		
	   			// este query me trae los datos que necesito que me muestre en pantalla como empresa, documento, nombre, codigo del concepto, nombre del concepto, fecha y telefono
		   		$query =  " SELECT Venfec, Vennum, Venvto, Venffa, Vennfa   
			                  FROM ".$wbasedato."_000016
			              	 WHERE Venfec between '".$wfec_i."' and '".$wfec_f."' 
			              	 AND Vencod='".$cod[0]."' " .
			              	"  AND Venest='on'
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
				$tot=0;
				//echo $num;
				echo "<tr bgcolor=#cccccc><td><b>FECHA</b></td><td><b>No DE VENTA</b></td><td><b>Vl DE VENTA</b></td><td><b>FUENTE FACTURA</b></td><td><b>NUMERO FACTURA</b></td></tr>";
		        for ($i=1;$i<=$num;$i++)
		         {
		            if (is_int ($i/2))
	                $wcf="DDDDDD";
	                else
	                $wcf="CCFFFF";
		            
		            $row = mysql_fetch_array($err);
		            $arr[$i]['fecha']=$row[0];
		            $arr[$i]['numerov']=$row[1];
		            $arr[$i]['valorv']=$row[2];
		            $arr[$i]['fuefac']=$row[3];
		            $arr[$i]['numfac']=$row[4];
		           
		           $tot=$tot+$arr[$i]['valorv'];
		           echo "<tr bgcolor=".$wcf."><td>".$arr[$i]['fecha']."</td><td align=left>".$arr[$i]['numerov']."</td><td align=right>".number_format($arr[$i]['valorv'],0,'.',',')."</td>";
		           
		           //fuente en rojo
		           if ($arr[$i]['fuefac']=='')
		           {
		           	$colorf="bgcolor=#ff0000";
		           }
		           else
		           {
		           	$colorf="";
		           }
		           
		           //factura en rojo
		           if ($arr[$i]['numfac']=='')
		           {
		           	$colorfa="bgcolor=#ff0000";
		           }
		           else
		           {
		           	$colorfa="";
		           }
		           echo "<td align=right ".$colorf.">".$arr[$i]['fuefac']."</td><td align=left ".$colorfa.">".$arr[$i]['numfac']."</td></tr>";
	               
	         	 }
	         	 echo "<tr><td align=center bgcolor=#006699><font text color=#FFFFFF><b>NUMERO TOTAL DE VENTAS: ".$num."</font></td><td align=right bgcolor=#006699><font text color=#FFFFFF><b>TOTAL</font></td>";
				 echo "<td align=right bgcolor=#006699><font text color=#FFFFFF><b>".number_format($tot,0,'.',',')."</font></td><td  bgcolor=#006699 colspan=2>&nbsp</td></tr>";
				 echo "</table>";
		         echo"<br>";
		         echo"<br>";
		        
		  }
}
?>
</body>
</html>