<html>
<head>
  <title>MATRIX</title>
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script type="text/javascript">
var veccoli;
var vecmes = new Array();
$(document).ready(function(){

	veccoli = $("#veccoli").val();
	veccoli = eval( '('+veccoli+')' );

	vecmes["01"] = "Enero";
	vecmes["02"] = "Febrero";
	vecmes["03"] = "Marzo";
	vecmes["04"] = "Abril";
	vecmes["05"] = "Mayo";
	vecmes["06"] = "Junio";
	vecmes["07"] = "Julio";
	vecmes["08"] = "Agosto";
	vecmes["09"] = "Septiembre";
	vecmes["10"] = "Octubre";
	vecmes["11"] = "Noviembre";
	vecmes["12"] = "Diciembre";


});

function actualizarmeses()
{
	var year = $('#selectano').val();
	if( veccoli[year] != undefined ){
		var options = "";
		var arreglo_claves = new Array();
		for( i in veccoli[year] ){
			arreglo_claves.push( i );
		}
		arreglo_claves.sort();
		for( var i=0; i<arreglo_claves.length; i++){
			var j = arreglo_claves[i];
			options+= "<option value='"+j+"'>"+vecmes[j]+"</option>";
		}
		$("#selectmes").html( options );
		actualizarquincenas();
	}

}

function actualizarquincenas()
{
	var year = $('#selectano').val();
	var mes = $('#selectmes').val();
	if( veccoli[year][mes] != undefined ){
		$("input[name=radio1]").attr('disabled', true);
		$("input[name=radio1]").attr('checked', false);
		var chequeada = '';
		for( i in veccoli[year][mes] ){
			$('#quin'+i).attr('disabled', false );
			chequeada = i;
		}
		$('#quin'+chequeada).attr('checked', true );
	}
}

</script>
<body BGCOLOR="">
<?php
include_once("conex.php");

function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		

		

		include_once("root/comun.php");

		
		
		$key = substr($user,2,strlen($user));
		$cedula=$key;
		if(strlen($cedula) == 7)
			$cedula=substr($cedula,2);
			//putenv("INFORMIXDIR=/informixcsdk");
            //putenv("ODBCINI=/etc/odbc.ini");
		//----------------------------------------------------------------------------------
		// --> 2014-09-24: (Jonatan Lopez) 	Se controla el inicio y fin de la segunda quincena, la cual debe ir siempre del dia 16 al 30 y 
		//									en año bisiesto imprimir correctamente la segunda quincena de febrero.
		//----------------------------------------------------------------------------------
		//	--> 2014-03-13: Esto se hace para unificar la colilla de pago en un solo script
		//		Jerson trujillo.
		//----------------------------------------------------------------------------------
		$odbcNomina = consultarAliasPorAplicacion($conex, $wemp_pmla, 'odbc_nomina');
		$conexUnix 	= odbc_connect($odbcNomina,'informix','sco') or die("No se pudo lograr conexion");

		$permiteVercolilla = consultarAliasPorAplicacion($conex, $wemp_pmla, 'permiteVerColilla');
				
				
		if($permiteVercolilla=="N")
		{
			echo "</br></br></br><center>No puede ver su colilla de pago en estos momentos, esta en proceso de liquidacion.</center>";
		}
		else
		{
			echo "<form action='000001_rep3.php' method=post>";
			echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
			if(!isset($cedula) or !isset($ano) or !isset($mes))
			{
				$qrow ='';
				$q=	" SELECT  Detval "
					."  FROM root_000051"
					." WHERE Detapl='conceptoscolillapago' ";

				$qres = mysql_query($q,$conex);
				if(mysql_num_rows($qres) > 0)
				{
					$qrow2 = mysql_fetch_array($qres);
					$qrow = $qrow2['Detval'];
				}


				$qrow = str_replace(",", "','", $qrow);
				$qrow = "('".$qrow."')";

				$query = " SELECT pagano ,pagmes ,pagnpm  "
						 ." FROM nopag "
						 ." WHERE pagcod = '".$cedula."' "
						 ." AND pagcon IN ".$qrow ." "
						 ." AND pagche  IS NOT NULL "
						 ." GROUP BY pagano ,pagmes,pagnpm  "
						 ." ORDER BY pagano , pagmes ";

				//echo $query3;

				$err = odbc_do($conexUnix,$query);
				$campos= odbc_num_fields($err);
				$vectorcolillas=array();

				$anoactual = date("Y");
				$mesactual = date("m");
				$quincenaactual = '';

				while(odbc_fetch_row($err) )
				{
					$vectorcolillas[odbc_result($err,1)][odbc_result($err,2)][odbc_result($err,3)]='s';
					$anoactual = odbc_result($err,1);
					$mesactual = odbc_result($err,2);

				}

				echo "<input type='hidden' id='veccoli' value='".json_encode($vectorcolillas)."'>";

				$anoi=$anoactual ;
				$mesi = $mesactual;

				$vecmes1["01"] = "Enero";
				$vecmes1["02"] = "Febrero";
				$vecmes1["03"] = "Marzo";
				$vecmes1["04"] = "Abril";
				$vecmes1["05"] = "Mayo";
				$vecmes1["06"] = "Junio";
				$vecmes1["07"] = "Julio";
				$vecmes1["08"] = "Agosto";
				$vecmes1["09"] = "Septiembre";
				$vecmes1["10"] = "Octubre";
				$vecmes1["11"] = "Noviembre";
				$vecmes1["12"] = "Diciembre";
				echo "<table></table>";

				$titulo='COLILLA DE PAGO';
				$wactualiz="2013-06-24";
				encabezado($titulo,$wactualiz, "clinica");

				
				
				$year=(integer)substr(date("Y-m-d"),0,4);
				$day=substr(date("Y-m-d"),5,2);
				echo "<center><table >";
				echo"<tr class='fila1'><td><b>Año<b></td><td><select id='selectano' name='ano' onchange='actualizarmeses()'>";
				foreach ($vectorcolillas as $clave => $valor) {
						if($anoi == $clave )
							echo "<option selected value=".$clave.">".$clave."</option>";
						else
							echo "<option value=".$clave.">".$clave."</option>";
					}
				echo "</select></td></tr>";

				echo "<tr class='fila1'><td><b>Mes<b></td><td><select id='selectmes' name='mes' onchange='actualizarquincenas()'>";
				foreach ($vectorcolillas[''.$anoi.''] as $clave => $valor) {
					if($mesactual== $clave )
						echo "<option selected value=".$clave.">".$vecmes1[''.$clave.'']."</option>";
					else
						echo "<option  value=".$clave.">".$vecmes1[''.$clave.'']."</option>";

					}
				echo "</select></td></tr>";


				$quincenahabilitada	 = array();
				$quincenahabilitada[1]='disabled';
				$quincenahabilitada[2]='disabled';
				$quinchequeada = '';
				foreach($vectorcolillas[''.$anoi.''][''.$mesi.''] as $indice => $valores)
				{
					 $quincenahabilitada[$indice] = "";
					 $quinchequeada = $indice;

				}
				$vecquinchequeada= array();
				$vecquinchequeada[$quinchequeada]='CHECKED';

				echo "<tr class='fila1'><td align=center><INPUT TYPE = 'Radio' id='quin1' NAME = 'radio1' ".$quincenahabilitada['1']." VALUE = 1 ".$vecquinchequeada['1']."><b> Primera Quincena <b>";
				echo "</td><td><INPUT id='quin2' TYPE = 'Radio' ".$quincenahabilitada['2']." NAME = 'radio1' VALUE = 2 ".$vecquinchequeada['2']."><b> Segunda Quincena<b></td></tr>";
				echo "<tr class='fila2'><td colspan='2'align=center><input type='submit' value='IR'></td></tr></table>";
				echo "<input type='HIDDEN' name= 'cedula' value='".$cedula."'>";
			}
			else
			{
				$query = "	  SELECT percod,perap1,perap2,perno1,perno2,percco,cconom,perced,perofi,ofinom "
						 ." 	FROM noper,cocco,noofi Where percod = '".$cedula."'"
						 ."  	 AND peretr ='A'"
						 ."  	 AND percco = ccocod and perofi = oficod "
						 ." GROUP BY  percod,perap1,perap2,perno1,perno2,percco,cconom,perced,perofi,ofinom ";
				$err = odbc_do($conexUnix,$query);
				$campos= odbc_num_fields($err);
				if (odbc_fetch_row($err))
				{
					$row=array();
					for($i=1;$i<=$campos;$i++)
					{
						$row[$i-1]=odbc_result($err,$i);
					}
					$meses=array();
					$meses[0]=30;
					if(bisiesto($ano))
						$meses[1]=29;
					else
						$meses[1]=28;
					$meses[2]=30;
					$meses[3]=30;
					$meses[4]=30;
					$meses[5]=30;
					$meses[6]=30;
					$meses[7]=30;
					$meses[8]=30;
					$meses[9]=30;
					$meses[10]=30;
					$meses[11]=30;
					
					$qui = ((integer)$mes * 2);
					if ($radio1 == 1)
						$qui = $qui-1;
					if ($qui < '10')
						$qui = "0".$qui;
					if($qui % 2 == 0)
					{
						$last=$meses[$mes - 1];
						$begin="16";
					}
					else
					{
						$last=15;
						$begin="01";
					}
					$nombre=$row[3]." ".$row[4]." ".$row[1]." ".$row[2];
					echo "<table border=1>";
					echo "<tr><td colspan=5 align=center><IMG SRC='/MATRIX/images/medical/nomina/banner.jpg'></td>";
					echo "<tr><td rowspan=4 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg'></td>";
					echo "<td colspan=4 align=center><font size=5>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
					echo "<tr><td colspan=4  align=center><font size=4>NIT : 800.067.065-9</font><td></tr>";
					echo "<tr><td colspan=4  align=center><font size=4>NOMINA Y PRESTACIONES SOCIALES</font><td></tr>";
					echo "<tr><td colspan=4 align=center><b>COLILLA DE PAGO</b></td></tr>";
					echo "<tr><td colspan=5 align=center>PERIODO : ".$ano."/".$mes." QUINCENA : ".$qui." DESDE : ".$ano."-".$mes."-".$begin." HASTA : ".$ano."-".$mes."-".$last."</td></tr>";
					echo "<tr><td bgcolor=#cccccc colspan=5 align=center>EMPLEADO : ".$row[0]."-".$nombre." CEDULA : ".$row[7]." OFICIO : ".$row[8]."-".$row[9]."</td></tr>";
					echo "<tr><td colspan=5 align=center>CENTRO DE COSTOS : ".$row[5]."-".$row[6]."</td></tr>";
					echo "<tr><td><b>Codigo Cpto</b></td><td><b>Nombre Cpto</b></td><td align=right><b>Horas</b></td><td align=right><b>Valor</b></td><td align=center><b>C. Costos</b></td></tr>";
					$query = "SELECT pagcon,connom,paghor,pagval,pagcco "
							."  FROM nopag,nocon "
							." WHERE pagcod = '".$row[0]."'"
							."   AND pagano = '".$ano."'"
							."   AND pagmes = '".$mes."'"
							."   AND pagtip = 'Q'"
							."   AND pagsec = '".$qui."'"
							."   AND pagcon = concod "
							." ORDER by pagcon";

					$err1 = odbc_do($conexUnix,$query);
					$campos1 = odbc_num_fields($err1);
					if (odbc_fetch_row($err1))
					{
						$wswp=0;
						$wswd=0;
						$total_p=0;
						$total_d=0;
						$total_h=0;
						do
						{
							$row1=array();
							for($i=1;$i<=$campos1;$i++)
							{
								$row1[$i-1]=odbc_result($err1,$i);
							}
							switch (substr($row1[0],0,1))
							{
								case 0:
									if($wswp==0)
									{
										echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>PAGOS</b></td></tr>";
										$wswp=1;
									}
									$total_p=$total_p+$row1[3];
									$total_h=$total_h+$row1[2];
									echo "<tr><td>".$row1[0]."</td><td>".$row1[1]."</td><td align=right>".$row1[2]."</td><td align=right>".number_format((double)$row1[3],2,'.',',')."</td><td align=center>".$row1[4]."</td></tr>";
									break;
								default:
									if($wswd==0)
									{
										echo "<tr><td bgcolor=#ffffcc colspan=2><b>TOTAL HORAS : </b></td><td bgcolor=#ffffcc align=right><b>".$total_h."</b></td><td bgcolor=#99ccff><b>TOTAL PAGOS</b></td><td  bgcolor=#99ccff align=right><b>".number_format((double)$total_p,2,'.',',')."</b></td></tr>";
										echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>DEDUCCIONES</b></td></tr>";
										$wswd=1;
									}
									$total_d=$total_d+$row1[3];
									echo "<tr><td>".$row1[0]."</td><td colspan=2>".$row1[1]."</td><td align=right>".number_format((double)$row1[3],2,'.',',')."</td><td align=center>".$row1[4]."</td></tr>";
									break;
							}
						}
						while (odbc_fetch_row($err1));

						$neto=$total_p-$total_d;
						echo "<tr><td bgcolor=#ccccff colspan=3><b>TOTAL DEDUCCIONES</b></td><td colspan=2 bgcolor=#ccccff align=right><b>".number_format((double)$total_d,2,'.',',')."</b></td></tr>";
						echo "<tr><td colspan=3 bgcolor=#ccffcc><b>NETO PAGADO</b></td><td colspan=2 bgcolor=#ccffcc align=right><b>".number_format((double)$neto,2,'.',',')."</b></td></tr>";
						echo "</table>";

						//-------------------------------------------------------------------------------------
						// --> 	Mostrar mensaje de consignacion de cesantias
						// 		Jerson Trujillo, 2014-02-25
						//-------------------------------------------------------------------------------------
						$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'nomina');
						// --> Consultar si existe una consigancion de cesantias para el año y la quincena seleccionada
						$qConsig = "SELECT Csefon, Cseval, Csefco
									  FROM ".$wbasedato."_000009
									 WHERE Cseano = '".$ano."'
									   AND Csequi = '".$qui."'
									   AND Csecem = '".$row[0]."'
									   AND Cseest = 'on'
						";
						$rConsig = mysql_query($qConsig, $conex) or die("Error en el query: ".$qConsig."<br>Tipo Error:".mysql_error());
						if($rowConsig = mysql_fetch_array($rConsig))
						{
							// --> Obtener nombre de la empresa asociada al empleado
							$qEmp = " SELECT Empdes
										FROM root_000050
									   WHERE Empcod = '".$wemp_pmla."'
							";
							$rEmp = mysql_query($qEmp, $conex) or die("Error en el query: ".$qEmp."<br>Tipo Error:".mysql_error());
							if($rowEmp = mysql_fetch_array($rEmp))
								$empresa = $rowEmp['Empdes'];
							else
								$empresa = "---";

							// --> Mostrar mensaje informando la consignacion
							echo "
							<table>
								<tr>
									<td align='center'>&nbsp;&nbsp;&nbsp;
										<div class='fondoAmarillo' style='font-size	: 11pt;font-family: verdana;width:900px;border: 1px solid #2A5DB0;padding: 5px;' align='center'>
											<img width='15' height='15' src='../../images/medical/root/info.png' />
											<b>NOTA:</b> Nos permitimos informarle que el ".$rowConsig['Csefco']." ".$empresa." consignó la suma de <b>$".number_format($rowConsig['Cseval'], 0, ',', '.')."</b>,
											<br>por concepto de cesantías del año ".($ano-1)." en el Fondo de Cesantías <b>".$rowConsig['Csefon']."</b>.
										</div>
										<br>
									</td>
								</tr>
							</table>
							";
						}
					}
					else
					{
						echo "</table>";
						echo "<center><table border=0 aling=center>";
						echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
						echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE DETALLE DE PAGOS !!!!</MARQUEE></FONT>";
						echo "<br><br>";
					}
				}
				else
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE EL EMPLEADO !!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
			}
		}
		
		
		odbc_close($conexUnix);
		odbc_close_all();
	}
?>
</body>
</html>
