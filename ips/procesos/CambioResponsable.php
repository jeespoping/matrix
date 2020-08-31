<head>
  	<title>MATRIX  Cambio de Identificacion de Paciente en Historia Clinica</title>
</head>

<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	   document.forms.cambioresponsable.submit();
	}
</script>

<?php
include_once("conex.php");

/* ****************************************************************
   * PROGRAMA PARA EL CAMBIO DE RESPONSABLE EN HISTORIAS CLINICAS *
   ****************************************************************/
   
//==================================================================================================================================
//PROGRAMA                   : CambioResponsable.php
//AUTOR                      : Juan David Jaramillo R.
  $wautor="Juan D. Jaramillo R.";
//FECHA CREACION             : Sept. 3 de 2006
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(Version Sept. 03 de 2006)"; 
//DESCRIPCION
//====================================================================================================================================\\
//Este programa se hace con el objetivo de permitir cambiar el tipo de identificacion, identificacion y paciente de un ingreso
//ambulatorio, el cual no requiere documento de historia clinica fisica.   Por lo tanto se maneja una historia virtual 9999999 
//para efectos de validacion en el sistema.                  																							  \\
//====================================================================================================================================\\


//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//X X X X X X X X X  ## DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\  
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\

$wcf= "DDDDDD";   //COLOR DEL FONDO    -- Gris claro
$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
$color="#999999";
$color2="#99CCFF";

$empresa="clisur";

function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}

session_start();
if (!isset($user))
{
	if(!isset($_SESSION['user']))
		session_register("user");
}

if(!isset($_SESSION['user']))
	echo "Error, Usuario NO Registrado";
else
{	   
	echo "<form name='CambioResponsable' action='CambioResponsable.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	
	$wfecha=date("Y-m-d");   
    $hora = (string)date("H:i:s");	              

    echo "<center><table border=0>";
	echo "<tr><td colspan= 8 align=center><IMG width=300 height=100 SRC='/matrix/images/medical/Citas/logo_citascs.png'></td>";
	echo "<tr><td><br></td></tr>";
    echo "<tr><td colspan= 8 align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>CAMBIO DE IDENTIFICACION DE PACIENTE</b></font></td></tr>";

    // Inicio de captura de datos en formulario
    ?>	    
	<script>
		function ira(){document.CambioResponsable.wdochis.focus();}
	</script>
	<?php
								
    echo "<tr>";
    if(!isset($wdochis))
		$wdochis='9999999';
	echo "<td align=center colspan=8 bgcolor=#cccccc><b>Historia a Cambiar:&nbsp&nbsp</b><input type='TEXT' name='wdochis' value='".$wdochis."' size=30 maxlength=11></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td bgcolor=#cccccc align=center colspan=3>Tipo Documento: ";
		$query = "SELECT selcod, seldes  from ".$empresa."_000105 where seltip='01' and selest='on'  order by Selpri";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<select name='wtdo' id=tipo1>";
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wtdo=ver($wtdo);
				if($wtdo == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select>";
	echo "</td>";
    
	if(!isset($wdocid))
		$wdocid='';
	echo "<td bgcolor=#cccccc colspan=1>Documento:</td>";
	echo "<td bgcolor=#cccccc colspan=4><input type='TEXT' name='wdocid' value='".$wdocid."' size=30 maxlength=11></td>";
	echo "</tr>";
	
	echo "<tr>";
	if(!isset($wap1)) 
		$wap1="";
	else $wap1=strtoupper($wap1);
	echo "<td bgcolor=#cccccc>Primer Apellido:</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='wap1' value='".$wap1."' size=15 maxlength=12></td>";
		
	if(!isset($wap2))
		$wap2="";
	else $wap2=strtoupper($wap2);
	echo "<td bgcolor=#cccccc>Segundo Apellido:</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='wap2' value='".$wap2."' size=15 maxlength=12></td>";
	
	if(!isset($wnom1))
		$wnom1="";
	else $wnom1=strtoupper($wnom1);
	echo "<td bgcolor=#cccccc>Primer Nombre:</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='wnom1' value='".$wnom1."' size=15 maxlength=10></td>";
	
	if(!isset($wnom2))
		$wnom2="";
	else $wnom2=strtoupper($wnom2);
	echo "<td bgcolor=#cccccc>Segundo Nombre:</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='wnom2' value='".$wnom2."' size=15 maxlength=10></td>";
	echo "</tr>";
	
	echo "<tr><td bgcolor=".$wcf2." align=center colspan=8><font text color=".$wclfa." size=3>
			  <input type='radio' name='wcon' value=1><b>Grabar	 &nbsp&nbsp&nbsp</font></td></tr>";
	
	echo "<tr><td align=center bgcolor=#cccccc colspan=8><input type='submit' value='OK'></td></tr>"; 
	echo "</table>";

	if (isset($wcon) and ($wcon == 1))
	{
		$valido= 'off';
		
		if(!isset($wdocid) or ($wdocid == ""))
		{
			?>	    
			<script>
			alert ("Debe Ingresar una Identificacion");
			function ira(){document.CambioResponsable.wdocid.focus();}
			</script>
			<?php
		}
		else {		
			if(!isset($wap1) or ($wap1 == ""))
			{
				?>	    
				<script>
				alert ("Debe Ingresar el Primer Apellido");
				function ira(){document.CambioResponsable.wap1.focus();}
				</script>
				<?php
			}
			else {
				if(!isset($wap2) or ($wap2 == ""))
				{
					?>	    
					<script>
					alert ("Debe Ingresar el Segundo Apellido");
					function ira(){document.CambioResponsable.wap2.focus();}
					</script>
					<?php
				}
				else {
					if(!isset($wnom1) or ($wnom1 == ""))
					{
						?>	    
						<script>
						alert ("Debe Ingresar el Primer Nombre");
						function ira(){document.CambioResponsable.wnom1.focus();}
						</script>
						<?php
					}
					else {
						if(!isset($wnom2) or ($wnom2 == ""))
						{
							?>	    
							<script>
							alert ("Debe Ingresar el Segundo Nombre");
							function ira(){document.CambioResponsable.wnom2.focus();}
							</script>
							<?php
						}
						else 
						{
							if(!isset($wdochis) or ($wdochis == ""))
							{
								?>	    
								<script>
								alert ("Debe Ingresar una Identificacion");
								function ira(){document.CambioResponsable.wdochis.focus();}
								</script>
								<?php
							} else {
								$query = "SELECT sum(tcarvto), sum(tcarfex), sum(tcarfre) ".  
	 	    	     					 "  FROM ".$empresa."_000106 ".
		        	 			    	 " WHERE tcarhis = ".$wdochis.
		        	 			  	     "   AND tcaring = 1 ".
		        	 			     	 "   AND tcarfac = 'S' ".
		        	 				 	 "   AND tcarest = 'on'";

		        	 			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    							$num = mysql_num_rows($err);
    
    							if ($num > 0)
	    						{
	    							for ($i=1;$i<=$num;$i++)
	        						{   
	        			        		$row = mysql_fetch_array($err);
	        							$wvaltot=$row[0];
	        							$wvalrec=$row[1]+$row[2];
	    							
	        							if($wvaltot==$wvalrec)
	        							{
		        							$valido='on';
	        							}
	        						}
	        					}
	    					}
		
	    					if($valido=='on')
							{		        	 				 
								$query =  "update ".$empresa."_000100 set pactdo='".$wtdo."',pacdoc='".$wdocid."',pacap1='".$wap1."',pacap2='".$wap2."',pacno1='".$wnom1."',pacno2='".$wnom2."' where pachis=".$wdochis;
								$res = mysql_query($query,$conex) or die("ERROR AL ACTUALIZAR EL ENCABEZADO DE LA HISTORIA: ".mysql_errno().":".mysql_error());
		
								if($wdochis==9999999)
								{
									$query =  "update ".$empresa."_000101 set ingfei='".$wfecha."',inghin='".$hora."' where inghis=9999999 and ingnin=1";
									$res1 = mysql_query($query,$conex) or die("ERROR AL ACTUALIZAR EL DETALLE DE LA HISTORIA: ".mysql_errno().":".mysql_error());
								}					
								echo "<tr><td colspan=2><font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=".$wcf." LOOP=-1>LA HISTORIA CLINICA FUE ACTULIZADA!!!!</MARQUEE></FONT></td></tr>";
							}
							else {
								$wmen="LA HISTORIA '9999999' TIENE CARGOS O ABONOS SIN FACTURAR... NO PUEDE ACTUALIZARSE!!!!";
								echo "<br><br><center><table border=0 aling=center>";
								echo "<tr><td bgcolor=".$color2."><IMG SRC='/matrix/images/medical/root/malo.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color2."><font color=#000000 face='tahoma'><b>".$wmen."</b></font></td></tr>";
								echo "</table><br><br></center>";
							}
						}
					}
				}
			}
		}	
	}
}	
	             
?>