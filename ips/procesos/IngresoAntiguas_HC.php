<head>
  	<title>MATRIX  Ingreso Historias Clinicas</title>
</head>

<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--

	function enter()
	{
	   document.forms.IngresoAntiguas_HC.submit();
	}

	function calendario(id,vrl)
	{
		if (vrl == "1")
		{
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:"12",electric:false,inputField:"wfna",button:"trigger1",ifFormat:"%Y-%m-%d",daFormat:"%Y/%m/%d"});
		}
		if (vrl == "2")
		{
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:"12",electric:false,inputField:"wfei",button:"trigger2",ifFormat:"%Y-%m-%d",daFormat:"%Y/%m/%d"});
		}
	}



</script>

<?php
include_once("conex.php");

/* *******************************************************************
   * PROGRAMA PARA REALIZAR INGRESO DE HISTORIAS CLINICAS EXISTENTES *
   *******************************************************************/

//==================================================================================================================================
//PROGRAMA                   : IngresoAntiguas_HC.php
//AUTOR                      : Juan David Jaramillo R.
  $wautor="Juan D. Jaramillo R.";
//FECHA CREACION             : Noviembre 01 de 2006
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(Version Nov. 01 de 2006)";
//DESCRIPCION
//====================================================================================================================================\\
//Este programa permite ingresar de manera manual las historias clinicas existentes en el archivo fisico, las cuales no existen en    \\
//la base de datos de matrix.	        																							  \\
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


$color="#dddddd";
$color1="#000099";
$color2="#006600";
$color3="#cc0000";
$color4="#CC99FF";
$color5="#99CCFF";
$color6="#FF9966";
$color7="#cccccc";
$color8="#999999";
$color9= "DDDDDD";   	//COLOR DEL FONDO    -- Gris claro
$color10="000066";  	//COLOR DEL FONDO 2  -- Azul claro
$color11="FFFFFF"; 		//COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
$color12="003366"; 		//COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro


$empresa="clisur";

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
	echo "<form name='IngresoAntiguas_HC' action='IngresoAntiguas_HC.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";

	$wfecha=date("Y-m-d");
    $hora = (string)date("H:i:s");
	$wfna=$wfecha;

    function ver($chain)
	{
		if(strpos($chain,"-") === false)
			return $chain;
		else
			return substr($chain,0,strpos($chain,"-"));
	}

    echo "<center><table border=0>";
	echo "<tr><td align=center colspan=4><IMG width=230 height=90 SRC='/matrix/images/medical/Citas/logo_citascs.png'></td></tr>";
	echo "<tr><td align=center bgcolor=#000066 colspan=4><font color=#ffffff size=6><b> INGRESO ANTIGUAS HISTORIAS CLINICAS</font><font color=#33CCFF size=4>&nbsp&nbsp&nbsp Ver. 2006-11-01</font></b></font></td></tr>";
	echo "<tr><td align=center bgcolor=#999999 colspan=4><font text color=".$color10." size=2><b>DATOS GENERALES</b></font></td></tr>";

	// Inicio de captura de datos en formulario

	if (isset($ok) and ($ok == 1))
	{
		$whis="";
		$wdoc="";
  		$wap1="";
		$wap2="";
		$wno1="";
		$wno2="";
	}

	?>
	<script>
		function ira(){document.IngresoAntiguas_HC.whis.focus();}
	</script>
	<?php

	echo "<tr>";
	if(!isset($whis))
		$whis='';
	echo "<td align=center bgcolor=".$color.">Historia:<br><input type='TEXT' name='whis' value='".$whis."' size=10 maxlength=10></td>";

	echo "<td bgcolor=".$color." align=center colspan=2> Tipo Documento: ";
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

    if(!isset($wdoc))
		$wdoc="";
	echo "<td bgcolor=".$color." align=center>Documento: <br><input type='TEXT' name='wdoc' value='".$wdoc."' size=15 maxlength=15></td>";
	echo "</tr>";

	echo "<tr>";
	if(!isset($wap1))
		$wap1="";
	else $wap1=strtoupper($wap1);
	echo "<td bgcolor=".$color." align=center>Primer Apellido:<br><input type='TEXT' name='wap1' value='".$wap1."' size=12 maxlength=12></td>";

	if(!isset($wap2))
		$wap2="";
	else $wap2=strtoupper($wap2);
	echo "<td bgcolor=".$color." align=center>Segundo Apellido:<br><input type='TEXT' name='wap2' value='".$wap2."' size=12 maxlength=12></td>";

	if(!isset($wno1))
		$wno1="";
	else $wno1=strtoupper($wno1);
	echo "<td bgcolor=".$color." align=center>Primer Nombre:<br><input type='TEXT' name='wno1' value='".$wno1."' size=10 maxlength=10></td>";

	if(!isset($wno2))
		$wno2="";
	else $wno2=strtoupper($wno2);
	echo "<td bgcolor=".$color." align=center>Segundo Nombre:<br><input type='TEXT' name='wno2' value='".$wno2."' size=10 maxlength=10></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td bgcolor=".$color." align=center>Sexo : <br>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='03' and Selest='on'  order by Selpri";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	echo "<select name='wsex' id=tipo1>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wsex=ver($wsex);
			if($wsex == $row[0])
				echo "<option selected  value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";

	$wact="off";
	echo "<td bgcolor=".$color." align=center colspan=2>Estado : <br><font color=#FF0000 face='tahoma' size=3><b>INACTIVO</b></td>";
	$ann=(integer)substr($wfna,0,4)*360 +(integer)substr($wfna,5,2)*30 + (integer)substr($wfna,8,2);
	$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$weda=(integer)(($aa - $ann)/360);
	$weda=number_format((double)$weda,0,'.','');
	$cal="calendario('wfna','1')";
	echo "<td bgcolor=".$color." align=center>Fecha Nacimiento : <br><input type='TEXT' name='wfna' size=10 maxlength=10  id='wfna' readonly='readonly' value=".$wfna." class=tipo3><button id='trigger1' onclick=".$cal.">...</button>";
	?>
	<script type="text/javascript">//<![CDATA[
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfna',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
	//]]></script>
	<?php
	echo "</tr>";

	echo "<tr><td bgcolor=".$color8." align=center colspan=4><font text color=".$color10." size=1>
		 <input type='radio' name='ok' value=1 onclick='enter()'><b>INICIAR		&nbsp&nbsp&nbsp
       	 <input type='radio' name='ok' value=2 onclick='enter()'><b>GRABAR		&nbsp&nbsp&nbsp</font></td></tr>";
	echo "</table>";

	if (isset($ok) and ($ok == 2))
	{
		if(!isset($wdoc) or ($wdoc == ""))
		{
			?>
			<script>
			alert ("Debe Ingresar una Identificacion");
			function ira(){document.IngresoAntiguas_HC.wdoc.focus();}
			</script>
			<?php
		}
		else
		{
			if(!isset($wap1) or ($wap1 == ""))
			{
				?>
				<script>
				alert ("Debe Ingresar el Primer Apellido");
				function ira(){document.IngresoAntiguas_HC.wap1.focus();}
				</script>
				<?php
			}
			else {
				if(!isset($wap2) or ($wap2 == ""))
				{
					?>
					<script>
					alert ("Debe Ingresar el Segundo Apellido");
					function ira(){document.IngresoAntiguas_HC.wap2.focus();}
					</script>
					<?php
				}
				else {
					if(!isset($wno1) or ($wno1 == ""))
					{
						?>
						<script>
						alert ("Debe Ingresar el Primer Nombre");
						function ira(){document.IngresoAntiguas_HC.wno1.focus();}
						</script>
						<?php
					}
					else {
						if(!isset($wno2) or ($wno2 == ""))
						{
							?>
							<script>
							alert ("Debe Ingresar el Segundo Nombre");
							function ira(){document.IngresoAntiguas_HC.wno2.focus();}
							</script>
							<?php
						}
						else
						{
							if(!isset($whis) or ($whis == ""))
							{
								?>
								<script>
								alert ("Debe Ingresar la Historia Clinica");
								function ira(){document.IngresoAntiguas_HC.whis.focus();}
								</script>
								<?php
							}
							else
							{
								$query= "SELECT pachis ".
									    "  FROM ".$empresa."_000100 ".
		   								" WHERE pachis = '".$whis."'".
		   								"    OR pacdoc = '".$wdoc."'";

								$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    							$num = mysql_num_rows($err);

   								if($num > 0)
								{
									$wvalido='off';
									?>
									<script>
									alert ("El Numero de Historia O Identificacion Ya Existe");
									function ira(){document.IngresoAntiguas_HC.whis.focus();}
									</script>
									<?php
								}
								else $wvalido='on';

								if($wvalido=='on')
								{
									$query=  "INSERT INTO ".$empresa."_000100 (Medico,Fecha_data,Hora_data,Pachis,Pactdo,Pacdoc,Pactat,Pacap1,Pacap2,Pacno1,Pacno2,Pacfna,Pacsex,Pacest, ".
									     " Pacdir,Pactel,Paciu,Pacbar,Pacdep,Paczon,Pactus,Pacofi,Paccea,Pacnoa,Pactea,Pacdia,Pacpaa,Pacact,Seguridad) ".
										 " VALUES ('".$empresa."','".$wfecha."','".$hora."' ,".$whis.",'".$wtdo."','".$wdoc."','C','".$wap1."','".$wap2."','".$wno1."','".$wno2."','".$wfna.
										 "','".$wsex."','C',' ',' ','05001',' ','05','U',4,' ',' ',' ',' ',' ','NINGUNO','off','C-".$user."')";
									$res = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());

									echo "<tr><td colspan=2><font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=".$color." LOOP=-1>LA HISTORIA CLINICA FUE GRABADA!!!!</MARQUEE></FONT></td></tr>";
								}
							}
						}
					}
				}
			}
		}
	}
}

?>