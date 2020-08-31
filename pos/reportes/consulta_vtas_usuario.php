<html>
<head>
  	<title>MATRIX Consulta de Formulas por Documento de Identificacion</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
//==================================================================================================================================
//PROGRAMA						:Reporte de consulta de ventas 
//AUTOR							:Juan Carlos Hernandez
//FECHA CREACION				:...
//FECHA ULTIMA ACTUALIZACION 	:26 de Marzo de 2007
//
//==================================================================================================================================
//ACTUALIZACIONES
//==================================================================================================================================
//2007-03-26: Juan David Londoño, se puso para consultar tambien las ventas por empresa								 
//==================================================================================================================================
//2007-05-03: Juan David Londoño, se coloco para que sacara errores cuando no se ha escogido el tipo de documento o entidad.	
//								  se hizo un subquery para cuando es tipo empresa con la tabla 000024 pa que traiga el nit de 
//								  todos los codigos.
//==================================================================================================================================


session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='consulta_vtas_usuario' action='consulta_vtas_usuario.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>";
	
	$wfecha=date("Y-m-d"); 
	
	if (!isset($wdoc))
	   {
		echo "<br><br><br>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><font size=5><b>FARMA STORE S.A.<b></font></td></tr>";
		echo "<tr><td align=center colspan=2><font size=5>CONSULTA DE FORMULAS POR USUARIO</font></td></tr>";
		echo "</tr>";  
	    echo "<tr><td bgcolor=#dddddd align=center colspan=2><INPUT TYPE='text' NAME='wdoc' >";
	    echo "<br><b>Documento de Identidad <input type='radio' name='tip' value='doc'></b>&nbsp&nbsp&nbsp&nbsp&nbsp";
		echo "<b>Nit de la Empresa</b> <input type='radio' name='tip' value='nit'></td></tr>";
        echo "<tr><td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_i' value=".$wfecha." SIZE=10></td>";
	    echo "<td bgcolor=#dddddd align=center><b>Fecha Final (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_f' value=".$wfecha." SIZE=10></td>";
	    echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	   }
	else
	   {
	   
		if ($wdoc != "9999" )
		   {	
			
		// 2007-05-03		
	   	if (isset ($tip) and $tip=='nit')// para cuando es empresa
	   	{
	   		$q = " SELECT Empnom, Empcod "   
			    ."   FROM ".$wbasedato."_000024 "
			    ."  WHERE Empnit = '".$wdoc."'";
	   	}
	   
	   else // para cuando es particular
   		{
   		$q = " SELECT clinom "   
		    ."   FROM ".$wbasedato."_000041 "
		    ."  WHERE clidoc = '".$wdoc."'";
   		}
	   		
	   		$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);
		   if ($num > 0)
			   {
			    $row = mysql_fetch_array($err);
			    $wnom=$row[0];
		       } 
			  else
			     $wnom="NO EXISTEN DATOS";     
		    
			echo "<table border=0 align=center>";
			echo "<tr><td align=center  colspan=6><IMG SRC='/matrix/images/medical/Pos/logo_".$wbasedato.".png'></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=6 face='tahoma'><b>FORMULAS POR USUARIO</font></b></font></td></tr>";
			echo "<tr><td align=center bgcolor=#cccccc colspan=10><font size=4 face='tahoma'><b>Documento : ".$wdoc." *** ".$wnom." ***</b></font></font></td></tr>";
		// 2007-05-03
		if (isset ($tip) and $tip=='nit')// para cuando es empresa
		{	
			$q = " SELECT Vennum, Venfec, Vennfa, venvto, Vencmo, Venusu "
				        ."   FROM ".$wbasedato."_000016"
				        ."  WHERE venest = 'on' "
				        ."    AND Vencod in (select Empcod from ".$wbasedato."_000024 where Empnit = '".$wdoc."')"
				         ."	  AND venfec between '".$wfec_i."' and '".$wfec_f."'"
				       ."ORDER BY Venfec, Vennum ";
		}else
		{
			$q = " SELECT Vennum, Venfec, Vennfa, venvto, Vencmo, Venusu "
				        ."   FROM ".$wbasedato."_000016"
				        ."  WHERE venest = 'on' "
				        ."    AND Vennit ='".$wdoc."'"
				         ."	  AND venfec between '".$wfec_i."' and '".$wfec_f."'"
				       ."ORDER BY Venfec, Vennum ";
		}
	  	$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
			//echo mysql_errno() ."=". mysql_error();
	   		$i=0;
	   		$row = mysql_fetch_array($err);
			$wvtatotal=0;
			while ($i < $num)
			   {
			   	
			  
				echo "<tr>";
			    echo "<td bgcolor=CCFFFF>&nbsp</td>";	
			    echo "<td bgcolor=CCFFFF>&nbsp</td>";	
			    echo "<td bgcolor=CCFFFF>&nbsp</td>";	
			    echo "<td bgcolor=CCFFFF>&nbsp</td>";	
			    echo "<td bgcolor=CCFFFF>&nbsp</td>";	
			    echo "<td bgcolor=CCFFFF>&nbsp</td>";
			    echo "</tr>";
				
				$wventa=$row[0];	  
				  
				echo "<tr>";
				echo "<td bgcolor=999999><font face='tahoma' size=2>Fecha: ".$row[1]."</font></td>";	
				echo "<td bgcolor=999999><b><font face='tahoma' size=2>Venta Nro: ".$row[0]."</font></b></td>";	
				echo "<td bgcolor=999999><font face='tahoma' size=2>Factura: ".$row[2]."</font></td>";	
				echo "<td bgcolor=999999><font face='tahoma' size=2>Cuota Mod.: ".number_format($row[4],0,'.',',')."</font></td>";	
				echo "<td bgcolor=999999 COLSPAN=2><b><font face='tahoma' size=2>Valor venta: ".number_format($row[3],0,'.',',')."</font></b></td>";
				echo "</tr>";
				
				$q = " SELECT Descripcion "
				    ."   FROM usuarios "
				    ."  WHERE codigo = '".$row[5]."'";
				$err1 = mysql_query($q,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 > 0)
				   {
				    $row1 = mysql_fetch_array($err1);
				    $wnomusu=$row1[0];
			       } 
				  else
				     $wnomusu="Codigo de usuario ya no existe"; 
				    
				echo "<tr>";
				echo "<td bgcolor=999999 COLSPAN=6><b><font face='tahoma' size=2>Vendedor: (".$row[5].") Nombre: ".$wnomusu."</font></b></td>";
				echo "</tr>";
				
				$wvtatotal=$wvtatotal+$row[3];
				while (($wventa == $row[0]) and ($i < $num))
				     {
					  $color="#CCCCCC";
					      
					  $q = "SELECT vdeart, artnom, artuni, vdevun, vdecan, (((vdevun-vdedes)*vdecan)*(1+(vdepiv/100))) "
					      ."  FROM ".$wbasedato."_000016,".$wbasedato."_000017,".$wbasedato."_000001 "
					      ." WHERE vennum = '".$row[0]."'"
					      ."   AND vennum = vdenum "
					      ."   AND vdeart = artcod "
					      ."   AND vdeest = 'on' ";
					  $errdet = mysql_query($q,$conex);
			          $numdet = mysql_num_rows($errdet);
			    
			          echo "<tr>";
			          echo "<th bgcolor=9999FF><font face='tahoma' size=2>Codigo</font></th>";
			          echo "<th bgcolor=9999FF><font face='tahoma' size=2>Descripcion</font></th>";
			          echo "<th bgcolor=9999FF><font face='tahoma' size=2>Presentación</font></th>";
			          echo "<th bgcolor=9999FF><font face='tahoma' size=2>Valor Unitario</font></th>";
			          echo "<th bgcolor=9999FF><font face='tahoma' size=2>Cantidad</font></th>";
			          echo "<th bgcolor=9999FF><font face='tahoma' size=2>Valor Total</font></th>";
			          echo "</tr>";
			          
					  $j=0;
					  while ($j < $numdet)    
					       {
						    $rowdet = mysql_fetch_array($errdet);
						       
						    echo "<tr>";
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$rowdet[0]."</font></td>";	
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$rowdet[1]."</font></td>";	
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$rowdet[2]."</font></td>";	
							echo "<td align=right bgcolor=".$color."><font face='tahoma' size=2>".number_format($rowdet[3],0,'.',',')."</font></td>";	
							echo "<td align=right bgcolor=".$color."><font face='tahoma' size=2>".number_format($rowdet[4],0,'.',',')."</font></td>";	
							echo "<td align=right bgcolor=".$color."><font face='tahoma' size=2>".number_format($rowdet[5],0,'.',',')."</font></td>";
							echo "</tr>";   
							$j++;
					       } 
					  $i++; 
					  
					  $row = mysql_fetch_array($err);    
					 }     
			   }
			echo "<tr>";
			echo "<td bgcolor=#999999 colspan=3><font face='tahoma' size=2><b>CANTIDAD DE VENTAS : ".$num."</b></font></td>";
			echo "<td bgcolor=#999999 colspan=3><font face='tahoma' size=2><b>POR UN VALOR TOTAL DE : ".number_format($wvtatotal,0,'.',',')."</b></font></td>";
			echo "</tr>";	
			echo"</table>";
		   }	
	   }
}
?>
</body>
</html>