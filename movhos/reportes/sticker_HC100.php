<html>
<head>
  <title>MATRIX Sticker De Pacientes</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<script type="text/javascript">
/**
 * Fecha: 2019-10-30
 * Autor: Andres Alvarez
 * Se realiza actualización el formato de tirilla para tipo adulto, se reduce el tamaño.
 *
 * @return void
 */
<!--
	function enter()
	{
		document.forms.sticker_HC100.submit();
	}

//-->
</script>
<?php
include_once("conex.php");
include_once("root/comun.php");

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
	$wactualiz = '11-08-2021';
	encabezado( "STICKER DE PACIENTES", $wactualiz, $institucion->baseDeDatos );
	$key = substr($user,2,strlen($user));
	echo "<form  name='sticker_HC100' action='sticker_HC100.php?wemp_pmla=".$wemp_pmla."' method=post>";
	
	

	if(!isset($wimp) or !isset($wces) or !isset($whis) or !isset($wtip))
	{
		echo "<center><table border=0>";
		//echo "<tr><td align=center colspan=2><b>STICKER DE PACIENTES Ver. 2010-08-11</b></td></tr>";
		if(isset($whis))
			echo "<tr><td bgcolor=#cccccc>Nro. Historia Clinica</td><td bgcolor=#cccccc align=center><input type='TEXT' name='whis' value=".$whis." size=15 maxlength=15></td></tr>";
		else
			echo "<tr><td bgcolor=#cccccc>Nro. Historia Clinica</td><td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=15 maxlength=15></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Condicion Especial</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wces' size=15 maxlength=15></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Tipo de Sticker</td><td bgcolor=#cccccc align=center><input type='RADIO' name=wtip value=0><b>Adultos</b><input type='RADIO' name=wtip value=1><b>Niños</b><input type='RADIO' name=wtip value=2><b>Neonatos</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Impresora</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Impnom, Impnip  from root_000053 where Impest ='on' and Impcbp = 'on' order by Impcod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wimp'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
	}
	else
	{	
		if(!isset($ok))
		{
			$query  = "select pacnac from inpac ";
			$query .= " where pachis = ".$whis;
			$conex_o = odbc_connect('admisiones','','');
			$err_o = odbc_do($conex_o,$query);
			$campos= odbc_num_fields($err_o);
			if(odbc_fetch_row($err_o))
			{
				$odbc=array();
				for($m=1;$m<=$campos;$m++)
				{
					$odbc[$m-1]=odbc_result($err_o,$m);
				}
				$ann=(integer)substr($odbc[0],0,4)*360 +(integer)substr($odbc[0],5,2)*30 + (integer)substr($odbc[0],8,2);
				$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
				$ann1=($aa - $ann)/360;
				$meses=(($aa - $ann) % 360)/30;
				$dias1=(($aa - $ann) % 360) % 30;
				if($ann1 <= 1)
					$wedad = "Ne";
				elseif($ann1 > 1 and $ann1 < 9)
						$wedad = "Ni";
					else
						$wedad = "Ad";
				if($wtip == 0 and $wedad == "Ad")
					$wmsg="ASEGURESE DE TENER INSTALADO EL STICKER DE ADULTOS";
				else
				if($wtip == 0 and $wedad == "Ni")
					$wmsg="ESCOGIO STICKER DE ADULTOS Y POR LA EDAD DEL PACIENTE SE SUGIERE DE NIÑOS. REVISE O IMPRIMA SEGUN SU CRITERIO ";
				else
				if($wtip == 0 and $wedad == "Ne")
					$wmsg="ESCOGIO STICKER DE ADULTOS Y POR LA EDAD DEL PACIENTE SE SUGIERE DE NEONATOS. REVISE O IMPRIMA SEGUN SU CRITERIO ";
				else
				if($wtip == 1 and $wedad == "Ni")
					$wmsg="ASEGURESE DE TENER INSTALADO EL STICKER DE NIÑOS";
				else
				if($wtip == 1 and $wedad == "Ad")
					$wmsg="ESCOGIO STICKER DE NIÑOS Y POR LA EDAD DEL PACIENTE SE SUGIERE DE ADULTOS. REVISE O IMPRIMA SEGUN SU CRITERIO ";
				else
				if($wtip == 1 and $wedad == "Ne")
					$wmsg="ESCOGIO STICKER DE NIÑOS Y POR LA EDAD DEL PACIENTE SE SUGIERE DE NEONATOS. REVISE O IMPRIMA SEGUN SU CRITERIO ";
				else
				if($wtip == 2 and $wedad == "Ne")
					$wmsg="ASEGURESE DE TENER INSTALADO EL STICKER DE NEONATOS";
				else
				if($wtip == 2 and $wedad == "Ad")
					$wmsg="ESCOGIO STICKER DE NEONATOS Y POR LA EDAD DEL PACIENTE SE SUGIERE DE ADULTOS. REVISE O IMPRIMA SEGUN SU CRITERIO ";
				else
				if($wtip == 2 and $wedad == "Ni")
					$wmsg="ESCOGIO STICKER DE NEONATOS Y POR LA EDAD DEL PACIENTE SE SUGIERE DE NIÑOS. REVISE O IMPRIMA SEGUN SU CRITERIO ";
			}
			$ok=1;
			echo "<input type='HIDDEN' name= 'whis' value='".$whis."'>";
			echo "<input type='HIDDEN' name= 'wces' value='".$wces."'>";
			echo "<input type='HIDDEN' name= 'wimp' value='".$wimp."'>";
			echo "<input type='HIDDEN' name= 'wtip' value='".$wtip."'>";
			echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
			echo "<center><table border=0>";
			echo "<tr><td align=center bgcolor=#99CCFF><IMG SRC='/matrix/images/medical/root/Advertencia.png'></td><td bgcolor=#99CCFF><font color=#000000 face='arial'><b>".$wmsg."</b></td></tr>";
			echo "<tr><td align=center bgcolor=#CCCCCC colspan=2><input type='RADIO' name=wtip value=".$wtip." onclick='enter()'><b>Imprimir</b><input type='RADIO' name=wtip value=9 onclick='enter()'><b>Cancelar</b></td></tr>";
			echo "</table></center>";
		}
		else
		{
			$bd = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
			$wbasedato1 = strtolower( $institucion->baseDeDatos );
			$titulo_manilla = $institucion->nombre;

			$manillaAdulto = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'ImagenStikerAdulto' );
			$manillaNino = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'ImagenStikerNino' );

			$fp = fopen($manillaAdulto, "rb");
			$paquete_adulto = fread($fp, filesize($manillaAdulto));
			fclose($fp);

			$fp = fopen($manillaNino, "rb");
			$paquete_boys = fread($fp, filesize($manillaNino));
			fclose($fp);

			$wip=substr($wimp,strpos($wimp,"-")+1);
			
			
			switch ($wtip)
			{
				case 0:
					
					//                  0        1       2       3       4       5       6       7  
					$query  = "select pacced, pactid, pacnum, pacnom, pacap1, pacap2, pacnac, pacsex from inpac ";
					$query .= " where pachis = ".$whis." ";
                    $query .= " AND pacap2 is NOT NULL ";
                    $query .= " UNION " ;
                    $query .= "select pacced, pactid, pacnum, pacnom, pacap1, '' as pacap2, pacnac, pacsex from inpac ";
					$query .= " where pachis = ".$whis." ";
                    $query .= " AND pacap2 is NULL ";
                    
					$conex_o = odbc_connect('admisiones','','');
					$err_o = odbc_do($conex_o,$query);
					$campos= odbc_num_fields($err_o);
					$count=0;
					if(odbc_fetch_row($err_o))
					{
						$count++;
						$odbc=array();
						for($m=1;$m<=$campos;$m++)
						{
							$odbc[$m-1]=odbc_result($err_o,$m);
						}
						$wpac=trim($odbc[3])." ".trim($odbc[4])." ".trim($odbc[5]);
						if($odbc[7] == "M")
							$wsex="MASCULINO";
						else
							$wsex="FEMENINO";
						$wing=$odbc[2];
						$wtdo=$odbc[1];
						$wdoc=$odbc[0];
						$ann=(integer)substr($odbc[6],0,4)*360 +(integer)substr($odbc[6],5,2)*30 + (integer)substr($odbc[6],8,2);
						$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
						$ann1=($aa - $ann)/360;
						$meses=(($aa - $ann) % 360)/30;
						$dias1=(($aa - $ann) % 360) % 30;
						if ($ann1 > 0)
							$wedad=(string)(integer)$ann1." A";	
						elseif($meses > 0)
								$wedad=(string)(integer)$meses." M";
							else
								$wedad=(string)(integer)$dias1." D";

						$paquete = "CT~~CD,~CC^~CT~
							^XA
							~TA000
							~JSN
							^LT0
							^MNW
							^MTT
							^PON
							^PMN
							^LH0,0
							^JMA
							^PR6,6
							~SD15
							^JUS
							^LRN
							^CI27
							^PA0,1,1,0
							^XZ
							^XA
							^MMT
							^PW300
							^LL3300
							^LS0
							".
							$paquete_adulto.
							"^FT239,1204^A0R,33,33^FH\^CI28^FD".$titulo_manilla."^FS^CI27
							^FT198,1204^A0R,33,33^FH\^CI28^FD".$wtdo." : ".$wdoc."^FS^CI27
							^FT157,1204^A0R,33,33^FH\^CI28^FDSEXO :".$wsex."^FS^CI27
							^FT116,1204^A0R,33,33^FH\^CI28^FDHISTORIA:".$whis."^FS^CI27
							^FT75,1204^A0R,33,33^FH\^CI28^FD".$wpac."^FS^CI27
							^FT239,1737^A0R,33,33^FH\^CI28^FD*** ".$wces." ***^FS^CI27
							^FT198,1737^A0R,33,33^FH\^CI28^FDEDAD :".$wedad."^FS^CI27
							^FT157,1737^A0R,33,33^FH\^CI28^FDINGRESO: ".$wing."^FS^CI27
							^BY3,3,219^FT62,2174^BCR,,Y,N
							^FH\^FD>;".$whis."^FS
							^PQ1,,,Y
							^XZ
						";
						//echo $paquete."<br>";
						//break;
						$addr=$wip;
						$fp = fsockopen( $addr,9100, $errno, $errstr, 30);
						if(!$fp) 
						echo "ERROR : "."$errstr ($errno)<br>\n";
						else 
						{
							fputs($fp,$paquete);
							echo "PAQUETE ENVIADO <br>\n";
							
						}
						sleep(2);
						fclose($fp);
					}
					else
						echo "PACIENTE NO EXISTE O NO ESTA ACTIVO<BR>";
				break;
				case 1:
					//                  0        1       2       3       4       5       6       7  
                    $query  = "select pacced, pactid, pacnum, pacnom, pacap1, pacap2, pacnac, pacsex from inpac ";
					$query .= " where pachis = ".$whis." ";
                    $query .= " AND pacap2 is NOT NULL ";
                    $query .= " UNION " ;
                    $query .= "select pacced, pactid, pacnum, pacnom, pacap1, '' as pacap2, pacnac, pacsex from inpac ";
					$query .= " where pachis = ".$whis." ";
                    $query .= " AND pacap2 is NULL ";
                    
					/*$query  = "select pacced, pactid, pacnum, pacnom, pacap1, pacap2, pacnac, pacsex from inpac ";
					$query .= " where pachis = ".$whis;*/
                    
					$conex_o = odbc_connect('admisiones','','');
					$err_o = odbc_do($conex_o,$query);
					$campos= odbc_num_fields($err_o);
					$count=0;
					if(odbc_fetch_row($err_o))
					{
						$count++;
						$odbc=array();
						for($m=1;$m<=$campos;$m++)
						{
							$odbc[$m-1]=odbc_result($err_o,$m);
						}
						$wpac=trim($odbc[3])." ".trim($odbc[4])." ".trim($odbc[5]);
						if($odbc[7] == "M")
							$wsex="MASCULINO";
						else
							$wsex="FEMENINO";
						$wing=$odbc[2];
						$wtdo=$odbc[1];
						$wdoc=$odbc[0];
						$ann=(integer)substr($odbc[6],0,4)*360 +(integer)substr($odbc[6],5,2)*30 + (integer)substr($odbc[6],8,2);
						$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
						$ann1=($aa - $ann)/360;
						$meses=(($aa - $ann) % 360)/30;
						$dias1=(($aa - $ann) % 360) % 30;
						if ($ann1 > 0)
							$wedad=(string)(integer)$ann1." A";	
						elseif($meses > 0)
								$wedad=(string)(integer)$meses." M";
							else
								$wedad=(string)(integer)$dias1." D";

						$paquete = "CT~~CD,~CC^~CT~
							~TA000
							~JSN
							^LT0
							^MNW
							^MTT
							^PON
							^PMN
							^LH0,0
							^JMA
							^PR2,2
							~SD15
							^JUS
							^LRN
							^CI27
							^PA0,1,1,0
							^XZ
							^XA
							^MMT
							^PW300
							^LL2100
							^LS0
							^BY2,3,174^FT77,1386^BCR,,Y,N
							^FH\^FD>;".$whis."^FS
							^FT246,641^A0R,33,33^FH\^CI28^FD".$titulo_manilla."^FS^CI27
							^FT205,641^A0R,33,33^FH\^CI28^FD".$wtdo." : ".$wdoc."^FS^CI27
							^FT164,641^A0R,33,33^FH\^CI28^FDSEXO :".$wsex."^FS^CI27
							^FT123,641^A0R,33,33^FH\^CI28^FDHISTORIA : ".$whis."^FS^CI27
							^FT82,641^A0R,33,33^FH\^CI28^FD".$wpac."^FS^CI27
							"
							.$paquete_boys.
							
							"^FT210,1113^A0R,33,33^FH\^CI28^FD*** ".$wces." ***^FS^CI27
							^FT169,1113^A0R,33,33^FH\^CI28^FDEDAD : ".$wedad."^FS^CI27
							^FT128,1113^A0R,33,33^FH\^CI28^FDINGRESO : ".$wing."^FS^CI27
							^PQ1,,,Y
							^XZ

							";
						//echo $paquete."<br>";
						//break;
						$addr=$wip;
						$fp = fsockopen( $addr,9100	, $errno, $errstr, 30);
						if(!$fp) 
						echo "ERROR : "."$errstr ($errno)<br>\n";
						else 
						{
							fputs($fp,$paquete);
							echo "PAQUETE ENVIADO <br>\n";
							
						}
						sleep(2);
						fclose($fp);
					}
					else
						echo "PACIENTE NO EXISTE O NO ESTA ACTIVO<BR>";
				break;
				case 2:
					//                  0        1       2       3       4       5       6       7  
                    $query  = "select pacced, pactid, pacnum, pacnom, pacap1, pacap2, pacnac, pacsex from inpac ";
					$query .= " where pachis = ".$whis." ";
                    $query .= " AND pacap2 is NOT NULL ";
                    $query .= " UNION " ;
                    $query .= "select pacced, pactid, pacnum, pacnom, pacap1, '' as pacap2, pacnac, pacsex from inpac ";
					$query .= " where pachis = ".$whis." ";
                    $query .= " AND pacap2 is NULL ";
                    
					/*$query  = "select pacced, pactid, pacnum, pacnom, pacap1, pacap2, pacnac, pacsex from inpac ";
					$query .= " where pachis = ".$whis;*/
					$conex_o = odbc_connect('admisiones','','');
					$err_o = odbc_do($conex_o,$query);
					$campos= odbc_num_fields($err_o);
					$count=0;
					if(odbc_fetch_row($err_o))
					{
						$count++;
						$odbc=array();
						for($m=1;$m<=$campos;$m++)
						{
							$odbc[$m-1]=odbc_result($err_o,$m);
						}
						$wpac1=trim($odbc[3]);
						$wpac2=trim($odbc[4])." ".trim($odbc[5]);
						if($odbc[7] == "M")
							$wsex="MAS";
						else
							$wsex="FEM";
						$wing=$odbc[2];
						$wtdo=$odbc[1];
						$wdoc=$odbc[0];
						$long=1720 - ((10 - strlen($whis)) * 10);
						$ann=(integer)substr($odbc[6],0,4)*360 +(integer)substr($odbc[6],5,2)*30 + (integer)substr($odbc[6],8,2);
						$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
						$ann1=($aa - $ann)/360;
						$meses=(($aa - $ann) % 360)/30;
						$dias1=(($aa - $ann) % 360) % 30;
						if ($ann1 > 0)
							$wedad=(string)(integer)$ann1." A";	
						elseif($meses > 0)
								$wedad=(string)(integer)$meses." M";
							else
								$wedad=(string)(integer)$dias1." D";
						$paquete="";
						$paquete=$paquete."CT~~CD,~CC^~CT~".chr(13).chr(10);
						$paquete=$paquete."^XA~TA000~JSN^LT0^MNM^MTD^PON^PMN^LH0,0^JMA^PR2,2~SD21^JUS^LRN^CI0^XZ".chr(13).chr(10);
						$paquete=$paquete."~DG000.GRF,02560,016,".chr(13).chr(10);
						$paquete=$paquete.",::::::::::::::::::::::S02,,::R0H2H020,R0104I4,T0IA8,R010,R0H2,R010,,R010,R0H2,R0H1,O0A80H080,N035701140,N0A22022A2,N040H010,N080080H0H80,R010,R0202280,T0H4,N08008,R010,M0H202A220A,R01004,S0H2H80,R01010,N0A22822A280,N0I7010,N08088,R010,O020022,".chr(13).chr(10);
						$paquete=$paquete."R010,N080J080,N080H01010I015D,N0A0H0I2A0H02AA80,R010K0I740,R02008200AIA8,R010H01005D558,N0IA8A20A802AJA,N0I405004007J74,Q0800A8002AJA,R0H1J05D5D55,R0H2J02AJA,R010J0I7377,N0IA80I080AA22AA80,P010115500545115,O0H2022AA82AA2H2A80,P04010J0767337,".chr(13).chr(10);
						$paquete=$paquete."O0280J080AA22AA80,O010010J0D4511D,O0A0022AA80A2I2A80,N014001177007673H3,N0A0080I080AA22AA80,N0D55010J0H45119,N0I28220080A2I2A,R010J0H67331,T0HA802A22A8,R0I1I0H45119,N0A2282I2H0K2A,N0I70107600667330,N0IA800A2002A22A8,R0H1K045118,R0H2H0A2K28,".chr(13).chr(10);
						$paquete=$paquete."R010K067330,O0A80I02880A22A8,N01DC010050I051,N0A020220J0I2,N0C0H010L072,N0800820AA80H020,R010,M020H0I2,Q0410,N080080I080,R010,N0202822AA88,R010,,R01054,N0A20022A2,R010,O0A0H020080,R010,O0820220080,P0701003,O0HA0,O050010,N0HAH0H2A2,N060H010,".chr(13).chr(10);
						$paquete=$paquete."N080I020880,R010,R020AA80,R010,,R010,R0H20A80,R010,S0H2H80,R01010,R020A280,,:::::::::::::::::::::::::::::::::::^XA".chr(13).chr(10);
						$paquete=$paquete."^MMT".chr(13).chr(10);
						$paquete=$paquete."^PW300".chr(13).chr(10);
						$paquete=$paquete."^LL2300".chr(13).chr(10);
						$paquete=$paquete."^LS0".chr(13).chr(10);
						$paquete=$paquete."^XA".chr(13).chr(10);
						$paquete=$paquete."^CF0,30".chr(13).chr(10);
						$paquete=$paquete."^FO230,1480^A@R^FDCLINICA PORTO AZUL^FS".chr(13).chr(10);
						$paquete=$paquete."^CF0,25".chr(13).chr(10);
						$paquete=$paquete."^FO200,1480^A@R^FD".$wtdo."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO200,1530^A@R^FD".$wdoc."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO175,1480^A@R^FDSEXO : ".$wsex."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO175,1630^A@R^FDEDAD : ".$wedad."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO145,1480^A@R^FDHISTORIA : ".$whis."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO145,".$long."^A@R^FD-".$wing."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO120,1480^A@R^FD*** ".$wces." ***^FS".chr(13).chr(10);
						$paquete=$paquete."^CF0,28".chr(13).chr(10);
						$paquete=$paquete."^FO090,1480^A@R^FD".$wpac1."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO060,1480^A@R^FD".$wpac2."^FS".chr(13).chr(10);
						$paquete=$paquete."^BY3,3,100^FT150,1800^BCR,,Y,N,N,A".chr(13).chr(10);
						$paquete=$paquete."^FD".$whis."^FS".chr(13).chr(10);
						$paquete=$paquete."^FT100,100^XG000.GRF,1,1^FS".chr(13).chr(10);
						$paquete=$paquete."^PQ1,0,1,Y^XZ".chr(13).chr(10);
						$paquete=$paquete."^XA^ID000.GRF^FS^XZ".chr(13).chr(10);

						//echo $paquete."<br>";
						//break;
						$addr=$wip;
						$fp = fsockopen( $addr,9100, $errno, $errstr, 30);
						if(!$fp) 
						echo "ERROR : "."$errstr ($errno)<br>\n";
						else 
						{
							fputs($fp,$paquete);
							echo "PAQUETE ENVIADO <br>\n";
							
						}
						sleep(2);
						fclose($fp);
					}
					else
						echo "PACIENTE NO EXISTE O NO ESTA ACTIVO<BR>";
				break;
				default:
					echo "<input type='HIDDEN' name= 'whis' value='".$whis."'>";
			}
			echo "<table><tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='RETORNAR'></td></tr></table>";
		}
	}
}
?>
</body>
</html>