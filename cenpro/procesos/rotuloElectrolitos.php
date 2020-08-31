<head>
  <title>ROTULO MEDICAMENTO DE ALTO RIESGO (Electrolitos)</title>

  <style type="text/css">
        .texto1{font-size:7px; font-family:Verdana, Arial, Helvetica, sans-serif; height:5px;}
        
   </style>
  
 <script type="text/javascript">
    function enter()
    {
    	document.forms.electrolitos.submit();
    }

 </script>
<head >
<body >

<?php
include_once("conex.php");

// //style='font-size:8px; font-family:Verdana, Arial, Helvetica, sans-serif; height:5px'

/*****************************************************************************************************************************************************************************
 * Por: Juan C. Hernández M.
 * 
 * Modificaciones:
 *  
 * Agosto 1 de 2011	(JuanC)
 *****************************************************************************************************************************************************************************/




////////////////////////////////////////////////////PROGRAMA/////////////////////////////////////////////////////////

if (!isset($user))
{
	if(!isset($_SESSION['user']))
	session_register("user");
}

if(!isset($_SESSION['user']))
{
	echo "error";
}
else
{
    include_once("root/comun.php");
    
	$conex = obtenerConexionBD("matrix");
	
	//consulto los datos del usuario de la sesion
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user)); //extraigo el codigo del usuario
	
	if (strpos($user,"-") > 0)
	   $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 

	$usuario = consultarUsuario($conex,$wusuario);   
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');


	echo "<form name='electrolitos' action='rotuloElectrolitos.php' method=post>";
	
	
	echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' name='user' value='".$user."'>";
	echo "<input type='HIDDEN' name='whis' value='".$whis."'>";
	echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";
	echo "<input type='HIDDEN' name='wlote' value='".$wlote."'>";
	echo "<input type='HIDDEN' name='warticulo' value='".$warticulo."'>";
	
	//Traigo el paciente y la habitacion
	$q = " SELECT pacno1, pacno2, pacap1, pacap2, ubihac "
	    ."   FROM root_000036, root_000037, ".$wmovhos."_000018 "
		."  WHERE orihis = '".$whis."'"
		."    AND oriori = '".$wemp_pmla."'"
		."    AND oriced = pacced "
		."    AND orihis = ubihis "
		."    AND oriing = ubiing ";
	$respac = mysql_query($q, $conex) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
    $rowpac = mysql_fetch_array($respac);
	
	//Traigo la fraccion o dosis
	$q = " SELECT deffra, deffru "
	    ."   FROM ".$wmovhos."_000059 "
		."  WHERE defart = '".$warticulo."'"
		."    AND defcco = '".$wcco."'";
	$resfra = mysql_query($q, $conex) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
    $rowfra = mysql_fetch_array($resfra);
	
	//Traigo datos generales del medicamento y del lote
	$q = " SELECT artcom, artvia, arttin, artfot, plofcr, plofve, descripcion "
	    ."   FROM ".$wcenmez."_000002, ".$wcenmez."_000004, usuarios "
		."  WHERE plopro = '".$warticulo."'"
		."    AND plocod = '".$wlote."'"
		."    AND plopro = artcod "
		."    AND ploela = codigo ";
	$reslot = mysql_query($q, $conex) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
    $rowlot = mysql_fetch_array($reslot);
	
	$wletra=1;
	
	echo "<left><table>";
	echo "<th align=center><font size=1><b>MEDICAMENTO DE ALTO RIESGO</b></font></th>";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr class=texto1>";
	echo "<td>HISTORIA CLINICA: <b>".$whis."</b></td>";
	echo "</tr>";
	echo "<tr class=texto1>";
	echo "<td>PACIENTE: <b>".$rowpac[0]." ".$rowpac[1]." ".$rowpac[2]." ".$rowpac[3]." "."</b></td>";
	echo "</tr>";
	echo "<tr class=texto1>";
	echo "<td>HABITACION: <b>".$rowpac[4]."</b></td>";
	echo "</tr>";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr class=texto1>";
	echo "<td>MEDICAMENTO: <br><b>".substr($rowlot[0],0,40)."</b></td>";
	echo "</tr>";
	
	echo "<tr class=texto1>";
	echo "<td><b>".substr($rowlot[0],41,strlen($rowlot[0]))."</b></td>";
	echo "</tr>";
	
	echo "<tr class=texto1>";
	echo "<td>DOSIS: <b>".$rowfra[0]." ".$rowfra[1]."</b></td>";
	echo "</tr>";
	
	$wvehiculo=explode("-",$rowlot[1]);
	echo "<tr class=texto1>";
	echo "<td>VOL Y VEH.DILUCION: <b>".trim($wvehiculo[1])."</b></td>";
	echo "</tr>";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr class=texto1>";
	echo "<td>Fecha de preparación: <b>".$rowlot[4]."</b></td>";
	echo "</tr>";
	echo "<tr class=texto1>";
	echo "<td>Fecha de vencimiento: <b>".$rowlot[5]."</b></td>";
	echo "</tr>";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr class=texto1>";
	echo "<td>Fecha de Instalación:___________________________</td>";
	echo "</tr>";
	echo "<tr></tr>";
	echo "<tr></tr>";
    echo "<tr class=texto1>";
	echo "<td>Hora de Instalación: ___________________________</td>";
	echo "</tr>";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr class=texto1>";
	echo "<td>Tiempo de Infusión:  ___________________________</td>";
	echo "</tr>";
	echo "<tr></tr>";
	echo "<tr class=texto1>";
	if ($rowlot[2] == 'on' )
		echo "<td>Proteger de la luz: <b>Si</b></td>";
	  else
         echo "<td>Proteger de la luz: <b>No</b></td>";
	echo "</tr>";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr class=texto1>";
	echo "<td>Preparó: <br><b>".$rowlot[6]."</b></td>";
	echo "</tr>";
	
	//Seleccionar REVISO
	$q = " SELECT codigo, descripcion "
		."   FROM usuarios "
		."  WHERE Ccostos = '".trim($wcco)."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);

	if (isset($wreviso))
	   {
	    $wreviso1=explode("-",$wreviso);
	    echo "<tr class=texto1>";
		echo "<td>Reviso: <br><b>".trim($wreviso1[1])."</b></td>";
		echo "</tr>";
	   }
	   else
	      {
			echo "<tr class=texto1>";
			echo "<td>Reviso: ";
			echo "<select name='wreviso' onchange='enter()'>";
			  
			echo "<option selected></option>";	
			for ($j=1;$j<=$num;$j++)
			   {
				$row = mysql_fetch_array($res); 
				echo "<option>".$row[0]." - ".$row[1]."</option>";
			   }
			echo "</select></td></tr>";
		  }
	echo "</table></center>";	  
}
?>


</body >
</html >
