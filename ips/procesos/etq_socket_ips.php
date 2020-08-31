<html>
<head>
  <title>IMPRESION DE ETIQUETAS DE CODIGOS DE BARRAS</title>
<script type="text/javascript">
// Vuelve a la página anterior llevando sus parámetros
function retornar(wemp_pmla,wartn)
{
	location.href = "etq_socket_ips.php?wemp_pmla="+wemp_pmla+"&wartn_env="+wartn;
}
</script>	
</head>
<body>
<?php
include_once("conex.php");
  /******************************************************
   *  IMPRESION DE ETIQUETAS DE CODIGOS DE BARRAS  		*
   ******************************************************/
	/*
	 * Modificado: 										*****************************************************************
	 * 2012-02-13 - Se adaptaron los estilos actuales de matrix y se camboó el uso del parámetro "empresa" por 			*
	 * "wemp_pmla", además se corrigió algunos query's donde estaba quemado farstore									*
	 * Mario Cadavid																									*
	 ********************************************************************************************************************
	*/
   

session_start();

// Si el usuario no está registrado muestra el mensaje de error
if(!isset($_SESSION['user']))
	echo "error";
else	// Si el usuario está registrado inicia el programa
{	            
 	
  include_once("root/comun.php");
  

  
  // Obtengo los datos del usuario
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
 
  // Aca se coloca la ultima fecha de actualización
  $wactualiz = " Feb. 13 de 2012";
                                                   
  echo "<br>";				
  echo "<br>";

          
  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // Obtengo los datos de la empresa
  $empresa = consultarInstitucionPorCodigo($conex, $wemp_pmla);

  $wbasedato = $empresa->baseDeDatos;
  $wentidad = $empresa->nombre;

  // Obtener titulo de la página con base en el concepto
  $titulo = "IMPRESION DE ETIQUETAS DE CODIGOS DE BARRAS";
	
  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz,"logo_".$wbasedato);  

  
  	echo "<form action='etq_socket_ips.php' method='post'>";
  	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
  	if(!isset($wcod) or !isset($wlot) or !isset($wfev) or !isset($wetq) or !isset($wip) or !isset($wartn))
	{
		echo "<center><table border=0>";
		if(!isset($wartn))
	  	{
	  		if(!isset($wartn_env))
				$wartn_env = "";
			echo "<tr><td colspan=2 class='fila1'><b>Descripci&oacute;n del art&iacute;culo</b></td></tr>";
			echo "<tr><td colspan=2 class='fila2' align=center><input type='text' name='wartn' value='".$wartn_env."' size='40' maxlength='40'></td></tr>";
		}
		else
		{
			echo "<input type='hidden' name= 'wartn' value='".$wartn."'>";
			echo "<tr><td class=fila1 align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td class=fila1 align=center colspan=2>GENERACION DE STIKERS DE CODIGOS DE BARRAS</td></tr>";
			echo "<tr><td class=fila2>C&oacute;digo del Producto</td><td class=fila2 align=center>";
			
			if(isset($wartn) and $wartn != "")
			{
				$query = "	SELECT Artcod, Artnom 
							  FROM ".$wbasedato."_000001 
							 WHERE Artcod = '".$wartn."' 
						  ORDER BY Artnom";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num == 0)
				{
					$query = " SELECT Artcod, Artnom 
								 FROM ".$wbasedato."_000001 
								WHERE Artnom like '%".$wartn."%' 
							 ORDER BY Artnom ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
				}
				if ($num>0)
				{
					echo "<select name='wcod'>";
					echo "<option selected>0-SELECCIONE</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($wart == $row[0]."-".$row[1])
							echo "<option selected>".$row[0]."-".$row[1]."</option>";
						else
							echo "<option>".$row[0]."-".$row[1]."</option>";
					}
				}
			}
			echo "</select>";
			echo "</td></tr>";
			echo "<tr><td class=fila2>Nro. de Lote</td>";
			echo "<td class=fila2><input type='TEXT' name='wlot' size=20 maxlength=20></td></tr>";
			echo "<tr><td class=fila2>Fecha de Vencimiento</td>";
			echo "<td class=fila2><input type='TEXT' name='wfev' size=10 maxlength=10></td></tr>";
			echo "<tr><td class=fila2>N&uacute;mero de Etiquetas</td>";
			echo "<td class=fila2><input type='TEXT' name='wetq' size=6 maxlength=6></td></tr>";	
			echo "<tr><td class=fila2>N&uacute;mero de IP</td>";
			echo "<td class=fila2><input type='TEXT' name='wip' size=15 maxlength=15></td></tr>";
		}
		echo "<tr>";
		$colspan = "";
		if(isset($wartn))
		{
			echo "<td align=center><br /><input type='button' onclick='retornar(\"$wemp_pmla\",\"$wartn\")' value='Retornar'></td>";
			$colspan = " colspan='2'";
		}
		echo "<td ".$colspan." align=center><br /><input type='submit' value='Aceptar'></td></tr></table>";
	}
	else
	{
		$wcod=substr($wcod,0,strpos($wcod,"-"));
		$query  = "	SELECT Artnom  ";
		$query .= "   FROM ".$wbasedato."_000001 ";
		$query .= "	 WHERE Artcod = '".$wcod."' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);

		echo "<center>";
		if ($num>0)
		{
			$row = mysql_fetch_array($err);
			$wnom1=substr($row[0],0,23);
			$wnom2=substr($row[0],24);
			echo $wnom1." - ".$wnom2."<br>";
			$longcb=strlen($wcod);
			$paquete="";
			$paquete=$paquete."N".chr(13).chr(10);
			$paquete=$paquete."FK".chr(34)."CENPRO".chr(34).chr(13).chr(10);
			$paquete=$paquete."FS".chr(34)."CENPRO".chr(34).chr(13).chr(10);; 
			$paquete=$paquete."V00,".$longcb.",L,".chr(34)."CODIGO".chr(34).chr(13).chr(10);
			$paquete=$paquete."V01,23,L,".chr(34)."LOTE".chr(34).chr(13).chr(10);
			$paquete=$paquete."V02,23,L,".chr(34)."FECVEN".chr(34).chr(13).chr(10);
			$paquete=$paquete."V03,23,L,".chr(34)."NOMBRE1".chr(34).chr(13).chr(10);
			$paquete=$paquete."V04,23,L,".chr(34)."NOMBRE2".chr(34).chr(13).chr(10);
			$paquete=$paquete."q650".chr(13).chr(10);
			$paquete=$paquete."S3".chr(13).chr(10);
			$paquete=$paquete."D4".chr(13).chr(10);
			$paquete=$paquete."ZT".chr(13).chr(10);
			$paquete=$paquete."TTh:m".chr(13).chr(10);
			$paquete=$paquete."TDy2.mn.dd".chr(13).chr(10);
			$paquete=$paquete."B230,10,0,1,2,5,70,N,V00".chr(13).chr(10);
			$paquete=$paquete."A220,85,0,2,1,1,N,V00".chr(13).chr(10);
			$paquete=$paquete."A220,105,0,1,1,1,N,V01".chr(13).chr(10);
			$paquete=$paquete."A220,125,0,1,1,1,N,V02".chr(13).chr(10);
			$paquete=$paquete."A220,145,0,1,1,1,N,V03".chr(13).chr(10);
			$paquete=$paquete."A220,165,0,1,1,1,N,V04".chr(13).chr(10);
			$paquete=$paquete."FE".chr(13).chr(10);
			$paquete=$paquete.".".chr(13).chr(10);
			$paquete=$paquete."FR".chr(34)."CENPRO".chr(34).chr(13).chr(10);
			$paquete=$paquete."?".chr(13).chr(10);
			$paquete=$paquete.$wcod.chr(13).chr(10);
			$paquete=$paquete."LOTE: ".$wlot.chr(13).chr(10);
			$paquete=$paquete."F.V.: ".$wfev.chr(13).chr(10);
			$paquete=$paquete.$wnom1.chr(13).chr(10);
			$paquete=$paquete.$wnom2.chr(13).chr(10);
			$paquete=$paquete."P".$wetq.chr(13).chr(10);
			$paquete=$paquete.".".chr(13).chr(10);
			$addr=$wip;

			$fp = fsockopen( $addr,9100, $errno, $errstr, 30);
			if(!$fp) 
				echo "ERROR : "."$errstr ($errno)<br>\n";
			else 
			{
				fputs($fp,$paquete);
				#echo "PAQUETE ENVIADO $errstr ($errno)<br>\n";
				echo "PAQUETE ENVIADO <br>\n";
				fclose($fp);
			}

			sleep(5);
		}
		else
			echo "CODIGO DE ARTICULO NO EXISTE !!!! <BR>";

		echo "<br /><input type='button' onclick='retornar(\"$wemp_pmla\",\"\")' value='Retornar'>";
		echo "</center>";
	}
}
?>
</body>
</html>