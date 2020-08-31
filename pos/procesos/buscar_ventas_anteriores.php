<script type="text/javascript">
	function cerrarVentana()
	 {
      window.close()		  
     } 
     
    self.focus() 
</script>

<?php
include_once("conex.php");

 

			      or die("No se ralizo Conexion");
 


 echo "<center><table border=1>";
 echo "<tr>";
 echo "<td align=center colspan=3 bgcolor=006699><font size=4 text color=#FFFFFF><b>LISTA CRONOLOGICA DE ENTREGA DE MEDICAMENTOS</b></font></td>";
 echo "</tr>";
 
 $q = " SELECT venfec, vdecan, DATEDIFF('".$wfecha."',venfec) "
     ."   FROM ".$wbasedato."_000016, ".$wbasedato."_000017 "
     ."  WHERE vennit = '".$wdocpac."'" 
     ."    AND vennum = vdenum "
     ."    AND venest = 'on' "
     ."    AND vdeart = '".$wart."'"
     ."  ORDER BY 1 desc ";
 $res = mysql_query($q,$conex);
 $num = mysql_num_rows($res);
 if ($num > 0)      
    {
     echo "<th bgcolor=ffcc66>FECHA ULT. <br>COMPRA</th>";
     echo "<th bgcolor=ffcc66>CANTIDAD</th>";
     echo "<th bgcolor=ffcc66>DIAS</th>";
        
     for ($i=1;$i<=$num;$i++)
        {
	     if (is_integer($i/2))
            $wcolor="33FFFF";
           else
              $wcolor="99FFFF";    
	        
	     $row = mysql_fetch_array($res);    
	     
	     echo "<tr>";
	     echo "<td align=center bgcolor=".$wcolor.">".$row[0]."</td>";
	     echo "<td align=center bgcolor=".$wcolor.">".$row[1]."</td>";
	     echo "<td align=center bgcolor=".$wcolor.">".$row[2]."</td>";
	     echo "</tr>";
	    }
	}
  echo "</table>";  
  
  echo "<br><br>";
  echo "<table align=center>";
  echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";
  
?>
