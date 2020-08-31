<html>
<head>
  	<title>MATRIX Ventas a Empresa sin facturar</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<script type="text/javascript">
	function enter()
	{
	   document.forms.vtasXempresa.submit();
	}
	
</script>

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='vtasXempresa' action='vtasXempresa.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>";
	
	$wfecha=date("Y-m-d"); 
	
	if (!isset($wfec_i) or !isset($wfec_f) or !isset($wemp) or !isset($wpro) or !isset($wcco) or !isset($wcaja))
	   {
		echo "<br><br><br>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></font></td></tr>";
		echo "<tr><td align=center colspan=2><font size=5>RELACION DE VENTAS POR EMPRESA SIN FACTURAR</font></td></tr>";
		echo "<tr>"; 
		if (isset($wfec_i))
		   echo "<td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_i' value=".$wfec_i." SIZE=10></td>";
		  else 
	         echo "<td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_i' value=".$wfecha." SIZE=10></td>";
	    if (isset($wfec_f))     
	       echo "<td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_f' value=".$wfec_f." SIZE=10></td>";
	      else 
	         echo "<td bgcolor=#dddddd align=center><b>Fecha Final (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_f' value=".$wfecha." SIZE=10></td>";
	    echo "</tr>";
        
        //CENTRO DE COSTO
	    $q =  " SELECT ccocod, ccodes "
			 ."   FROM ".$wbasedato."_000003 "
			 ."  ORDER BY 1 ";
				 	 
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
	            echo "<tr><td bgcolor=#cccccc colspan=2>Sucursal: ";
		echo "<select name='wcco' onchange='enter()' >";
		
		if (isset($wcco))
		   echo "<option selected>$wcco</option>";    
		for ($i=1;$i<=$num;$i++)
		   {
		    $row = mysql_fetch_array($res); 
		    echo "<option>".$row[0]."-".$row[1]."</option>";
	       }
		echo "</select></td>";
        
		//EMPRESA
        $query =  " SELECT Empcod, Empnom "
                 ."   FROM ".$wbasedato."_000024 "
                 ."  WHERE empest = 'on' "
                 ."    AND empfac = 'off' "
                 ."  ORDER BY Empcod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td bgcolor=#cccccc colspan=2>Empresa : <select name='wemp'>";
		for ($i=0;$i<$num;$i++)
		   {
			$row = mysql_fetch_array($err); 
			echo "<option>".$row[0]." - ".$row[1]."</option>";
		   }
		echo "</select></td></tr>";
		
		//PROGRAMA
		$query =  " SELECT Procod, Pronom  FROM ".$wbasedato."_000052 where Proest='on'  ORDER BY Procod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td bgcolor=#cccccc colspan=2>Programa : <select name='wpro'>";
		echo "<option>"."* - Todos"."</option>";
		for ($i=0;$i<$num;$i++)
		   {
			$row = mysql_fetch_array($err); 
			echo "<option>".$row[0]." - ".$row[1]."</option>";
		   }
		echo "</select></td></tr>";
		
		//CAJA
		if (isset($wcco))
		   {
			$query =  " SELECT cajcod, cajdes  FROM ".$wbasedato."_000028 where cajcco='".substr($wcco,0,strpos($wcco,"-"))."' ORDER BY cajdes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
	        	
			echo "<tr><td bgcolor=#cccccc colspan=2>Caja : <select name='wcaja'>";
			echo "<option>"."* - Todas"."</option>";
			for ($i=0;$i<$num;$i++)
			   {
				$row = mysql_fetch_array($err); 
				echo "<option>".$row[0]."-".$row[1]."</option>";
			   }
			echo "</select></td></tr>";
	       }	
		
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	   }
	else
	   {
		echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
	    $wccoe = explode("-",$wcco); 
	  
	    echo "<input type='HIDDEN' NAME= 'wtar' value='".$wemp."'>";
	    $wempe = explode("-",$wemp); 
		
	    if ($wpro == "* - Todos")
	       {
		    $wpro="%";
		    $wnompro="* - Todos";
	       } 
		  else 
		     $wnompro=$wpro;
	       
	    if ($wcaja == "* - Todas")
	       $wcajae="%";
	      else
	         $wcajae=explode("-",$wcaja);   
	    
		echo "<table border=0 align=center>";
		echo "<tr><td align=center  colspan=10><IMG SRC='/matrix/images/medical/Pos/logo_".$wbasedato.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=6 face='tahoma'><b>RELACION DE VENTAS POR EMPRESA SIN FACTURAR</font></b></font></td></tr>";
		echo "<tr>";  
        echo "<td bgcolor=#cccccc align=center colspan=5><b><font size=4 face='tahoma'>Fecha Inicial (AAAA-MM-DD): </font></b>".$wfec_i."</td>";
        echo "<td bgcolor=#cccccc align=center colspan=5><b><font size=4 face='tahoma'>Fecha Final (AAAA-MM-DD): </font></b>".$wfec_f."</td>";
        echo "</tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=10><font size=4 face='tahoma'><b>SUCURSAL : ".$wcco."</b></font></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=10><font size=4 face='tahoma'><b>EMPRESA : ".$wemp."</b></font></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=10><font size=4 face='tahoma'><b>PROGRAMA : ".$wnompro."</b></font></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=10><font size=4 face='tahoma'><b>CAJA : ".$wcaja."</b></font></font></td></tr>";
		
		$q = " SELECT Vennum, Venfec, Vennfa, Vennit, venvto, Venviv, Vencmo, Vmpmed, Vmppro "
	        ."   FROM ".$wbasedato."_000016,".$wbasedato."_000024,".$wbasedato."_000050 "
	        ."  WHERE venfec between '".$wfec_i."'"
            ."    AND '".$wfec_f."'"
            ."    AND vencco = '".$wccoe[0]."'"
	        ."    AND vencod = '".$wempe[0]."'"
	        ."    AND vencod = empcod "
	        ."    AND vennum = Vmpvta "
	        ."    AND vencaj like '".$wcajae[0]."'"
	        ."    AND vmppro like '".$wpro."'"
	        ."    AND vennfa = '' "
	        ."    AND vennum not in (SELECT traven FROM ".$wbasedato."_000055) "
	        ."  ORDER BY Venfec, Vennum ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		
		$wtotg=0;
		$wven="";
		$wpac=0;
		$wtot=0;
		$wtiva=0;
		$wtcmo=0;
		
		echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Nro. Formula</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Fecha</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Factura</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Cedula<br>Paciente</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Nombre<br>Paciente</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Programa</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Valor<br>Venta</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Valor<br>Iva</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Valor<br>Cuota<br>Moderadora</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Registro<br>Medico</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		   {
			$row = mysql_fetch_array($err);
			if ($wven != $row[0])
			   {
				$wpac++;
				$wven=$row[0];
				$wtot += $row[4];
				$wtiva += $row[5];
				$wtcmo += $row[6];
			   }
			$wmed=$row[7];
			$wpro=$row[8];
			$query = "select Clinom from ".$wbasedato."_000041  ";
			$query .= " where Clidoc = '".$row[3]."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			  {
				$row1 = mysql_fetch_array($err1);
				$wcli=$row1[0];
			  }
			 else
			    {
				 $wcli="9999";
			    }
			$wtotg += $row[4];
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."</font></td>";	
			if($row[3] != "9999" and $wcli != "9999")	
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wcli."</font></td>";	
			else
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>No Especificado</font></td>";	
			if(substr($wpro,0,1) != "-")	
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wpro."</font></td>";
			else
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>No Especificado</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[4],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[5],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[6],2,'.',',')."</font></td>";	
			if(substr($wmed,0,3) != "NO ")
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wmed."</font></td></tr>";	
			else
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>No Especificado</font></td>";
		   }
		echo "<tr>";
		echo "<td bgcolor=#999999 colspan=6 align=left ><font face='tahoma' size=2><b>VALOR TOTAL : ".number_format((double)($wtot+$wtiva-$wtcmo),0,'.',',')."</b></font></td>";
		echo "<td bgcolor=#999999 colspan=1 align=right><font face='tahoma' size=2><b>".number_format((double)$wtot,0,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#999999 colspan=1 align=right><font face='tahoma' size=2><b>".number_format((double)$wtiva,0,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#999999 colspan=1 align=right><font face='tahoma' size=2><b>".number_format((double)$wtcmo,0,'.',',')."</b></font></td>";
		echo "<td bgcolor=#999999 colspan=1 align=right><font face='tahoma' size=2><b>&nbsp</b></font></td>";
		echo "</tr>";	
		echo "<tr><td bgcolor=#999999 colspan=10><font face='tahoma' size=2><b>REGISTROS TOTALES : ".number_format((double)$num,0,'.',',')."</b></font></td></tr>";	
		echo"</table>";
	   }
}
?>
</body>
</html>