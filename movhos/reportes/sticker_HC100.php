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
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form  name='sticker_HC100' action='sticker_HC100.php' method=post>";
	

	

	if(!isset($wimp) or !isset($wces) or !isset($whis) or !isset($wtip))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>STICKER DE PACIENTES Ver. 2010-08-11</b></td></tr>";
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
						$paquete="";
						$paquete=$paquete."CT~~CD,~CC^~CT~".chr(13).chr(10);
						$paquete=$paquete."^XA~TA000~JSN^LT0^MNM^MTD^PON^PMN^LH0,0^JMA^PR2,2~SD21^JUS^LRN^CI0^XZ".chr(13).chr(10);
						$paquete=$paquete."~DG000.GRF,10240,032,".chr(13).chr(10);
						$paquete=$paquete.",:::::::::::::::::::::::::::gJ020P020,,::::::gH02020N020,,gH080A0N080,gG0180,gG0280220202020280,gG010040,gH0802ANA80,gG010051M14,gG02A02AOA020,gG010045H575H564,gH080AOA80,gG01004151515154,gG0280220202020280,gG010040,gH080A0N080,gG010040,gG02A020N020,gG010040,gH080A0,gG010040,gG028020,gG010040,gH08020,gG010040,N020H020M02A0202020,gG010040,gH080A8,gG010040,R0H2M02802A,gG010050,Q0JA80K0802A80,Q01550L01,P02AKAJ02A022A0,P017I740J010I040,P0MAK0802AA00280,O017D515DC0I010011,O02AA0H02AA0H02802AHA0AA0,".chr(13).chr(10);
						$paquete=$paquete."O0560J070L0I5H0740,O0A80J02A0I080AA8A0AA8,O070K0180H01005010H010,N02A0020H0H2I02A02002A02A20,O060L040H010060,O0A0L0280H08028008008,N010M040H018040,N02A0L028002802800200A,N010Q01,O080L028002800A08A8A8,N010M010H010M010,N0280020I028002A02AKA8,N010Q01004545H560,O080L0280H080ALA0,N010Q010051H151,O0A0L020H02802I2,W040H010040,O0280K0A0I08028,O040K010I01,N0H2A020202A0022A02AA02AA0,V0540H0100440H04,P0280H02AA80H080A800AHA8,U0140I010040H05D40,Q020H020J02802002AA2A,gG010J0140,gH080800AA00A,gG010J05,".chr(13).chr(10);
						$paquete=$paquete."L0202020J0I2022A0202AA022,gG010J05,O080L0280H080A02A8008,gG010I014,O0A2M2I02802AHA02AA,W040H010H04540040,O0OA80H0800AA800A8,N0175DKDC0H010H0540,N02ANAI02A02H2020,O0N760H01,O0A8080808A80H080,N010M040H01,O0A0L020H0280,,O0A0Q080,N010Q01,N02A0P02A020J020,gG01,O0A0Q080A0,N010Q01,O0A0P02A02A0V0H20,gG010040,O0A0Q0802AA80S0JA80,N010Q0100550U01550,N02A0P02A02H2HAM020H02ALAJ020,O060P010040440R057I760,O0HAQ0808002A80O0NA80,O050P010J0140P0H5D5D5DC,P02A0N0280I02A2A20L02ANA0,gG010L040N057K75740,".chr(13).chr(10);
						$paquete=$paquete."gH080J080AA0L0PA8,gG010M010L017D55D5D5DC0,N02020L02002A02022A022A0J02APA,gG010O040J057O70,O080M080H080J080H0HA80H0RA80,gG010J010H01540I0H5D5D5D5D5D4,O0OA0202A0I02A2AHA20H02ARA0,O064L460H010M0560J057Q740,O0OA8AA880I02AIAK0TA8,N017FLFC05010J0H1H5L05DD5D5M560,N02ANA02EAA0202AIA020H02ASA8,O0H404J4600610J05740L0S760,O080L02802880AJA80L0TA8,gG010041550M015D5D5D5D5D5D558,O020L020H02802AHA20L02ATA,gG010045440M017S74,gH0802AA0N02ATA,gG0100540O01D5D5H5HDH5D5D5C,W020H02A02A0I020J02ATA80,".chr(13).chr(10);
						$paquete=$paquete."gG010040P0M7607K76,O080L0280H080A0P0NA8ALA80,gG010S0H5D5D5D405D5D5D5,O0A0L020H02A020J020J0NA02AKA80,W060H010S0K747607657I7,O0OA80H080280H08A80I0KA8AA8AA8AIA80,N0140K0160H010R015J505C05415D5D80,N02AKA2AA0H02A02AKA820H0KA02A22A02AIA0,O04004001740H010045I5H4J017I7607607607I7,O080J02A80I080ALA80I0KA0AA8AA8AJA0,U01D0I01005J5150I01D5D5C05405C055D580,T02AA0I02802K2A0I02AJA02A02A02AIA0,U0740I010040O017I7607607607I740,T0HA80J08080J020I02AJA0AA8AA8AJA0,T05E0J010R01D55D405405405I580,".chr(13).chr(10);
						$paquete=$paquete."N0202022AA0I0H2A020J0280H02AJA0AA02A02AIA0,S0570K010R017I7607607607I740,S0HA80K080L0280H02AJA0AA8AA8AJA0,R015C0K010M010I01D5C5C05405C075H5C0,R02AA0K028020I0H2A0H02AHA2A02A02A02A2AA0,R0760U010I01774760760760765740,Q02AA0M0802AKA80H02AJA8AA8AA8AJA0,Q01D80L010040I0150I01D585405405405C1D40,N0202AA0H020H0H2A02AKA02002AHA2A0AA02A02A2AA0,Q0760M010057J740I01770760760760761740,Q0HA80M080ALA80I0HA8AA0AA8AA8AA8AA0,P05D0N010040I010J01D505C05405C07415C0,P02AA0I020H02802A2I2A0I02AA82A02A02A02A0AA0,".chr(13).chr(10);
						$paquete=$paquete."O01740J040H010R01760760760760760740,O02AA0J0280H080A0J0280I0HA8AA0AA8AA8AA8AA0,O07DD5J540H010R015405C0540540560580,N02ANA2002A020J0280H02AHA2A0AA02A02A22A0,W040H010R017607607607607607,O08A8A8A8AA80H080L0280I0HA8AA0AA8AA8AA8AA0,gG010M010I015405C07405C0740580,W020H028020J02A0I0HA02A02A02A02A02A0,gG010040J050J07607607607607607,gH080A8I8HA80I0HA0AA0AA8AA8AA8A80,gG01005K540J05C05405405405405,N020H020H020H0H2A02AKA820202A02A0AA02A02A82A0,gG01004K4L05607607607607606,gH080ALAK02A8AA0AA8AA8AA8280,".chr(13).chr(10);
						$paquete=$paquete."gG010S01405C05405C07404,O0A0L020H028020020M02A02A02A02A02A02,W040H010S01607607607607604,O0A80K0A80H080H02AA80L0A0AA0AA8AA8AA82,N017DLDC0H010H0H5D540K014054054054054,O0OAI02A022AJAK02A22A0AA02A02A82,O0N760H01001540H040K06076076076076,O0OA80H0802AA8A8A0K0A8AA0AA8AA8AA80,gG010050Q0405C07405C074,O0A2M2I02802A2028280J0202A02A02A02A82,gG010070Q04076076076076,O080L0280H080AA00280880I028AA0AA8AA8AA80,gG010040J01040K07405405405C,N020L020H0H2A02800280AA0H0H202A0AA02A02A20,gG010060L040K076076076076,".chr(13).chr(10);
						$paquete=$paquete."gH080A8002808280J0HA0AA8AA8AA80,gG010040J05010K05C05405C078,R0H2M028028002A2A2A0J02A02A02A02A,Q045740N010I0H4H040J076076076070,Q0JA80K08028002AA80A80I0HA0AA8AA8A8,P01FDHDC0J010H0400150N01C054054040,P02AKAJ02A02H202AA002A0H0H2A0AA02A02020,P0H7H457C0I010W060760760,P0MAK080H0A00880N0A0AA8AA8,O01C0I0170I010Y07405C0,O02AA0H02AA0H028020J020N0202A02A0,O060K0140H010040J040P0760640,O0A80J02A0I080AA8A8AA80O0HA8AA0,N0140L040H01005H5D5D50,N02A0020H0H2I02A02AKA820N0H2022020,N010M040H0100404H4540,".chr(13).chr(10);
						$paquete=$paquete."O0A0L0280H080ALA8,N010Q01,N02A0L0280028020I0H2R02,N010Q010M040,O080L0280H080L028,N010Q010M010,N02A20J0H28002A02020H02A0O020,W040H010M070,O080L0280H080L0A8,W040H010M040,O0A0L020H028020J0H2,O040L040H010040J040,O0280K0A0I080A808H8A80A80,O010K05C0H01005K54005,O02A020H0HAI02A02AKA82AA020,P040J040I010045J54004,P0280H02AA80H080ALA80A80,gG01,Q020H020J02802M202,gG01,gH08080,gG01,L020280K020H0H2A02H2HAI020,gG010H057574,O0A0Q0800AJA80,N0170P0100150H01,O0HAP02802AHAH2A0,O04540N0100540I040,O0IAP0802A80H0A8,".chr(13).chr(10);
						$paquete=$paquete."Q01C0M010050,N02022A0M02A02A0I02A20,R0460L010060,R0HA80L080A80J08,Q010150K010040,Q02A2AA0J0280280J0A,U050J010040J010,R0800AA0J080280I028,Q010H01580H010010I0140,R0A002AA2002A02A0022A820,U0H740H010H040H0740,R0A0AIA80H080028002A0,S017D0J01,R0A2AHAJ028002200220,R0H760K01,O0800AHA80J02800AA0,P015DC0L0100154005,N02A2AIA020H0H2A02AA822A020,O0I740M010064500740,O0JAO080AIA0AA8,O05F0O01004004,O0IAO02802A02022A,O060P010040,O0A80P080A8008008,N010Q01,N02A0M02002A02020A022,gG01,O080Q0800800A008,gG010H01004150,".chr(13).chr(10);
						$paquete=$paquete."gG02802ALA,gG01007K740,gH080ALA8,gG010040,N020Q02A02A22A22020,gG010040,gH080A8,gG01,gG02802A202H20,gG010040H0H540,gH0802A80AHA8,gG010J015010,L020S02A02002AA28,gG010J0540,gH080A002A808,gG010J05,gG0280200AA002,gG010J04,gH080A00A8008,gG010I0140010,gG02A02H2A202A20,gG010H0560H040,gH0802AHA02A8,,gG028002A002,,::gJ02020,,::::::::::^XA".chr(13).chr(10);
						$paquete=$paquete."^MMT".chr(13).chr(10);
						$paquete=$paquete."^PW310".chr(13).chr(10);
						$paquete=$paquete."^LL3300".chr(13).chr(10);
						$paquete=$paquete."^LS0".chr(13).chr(10);
						$paquete=$paquete."^XA".chr(13).chr(10);
						$paquete=$paquete."^CF0,30".chr(13).chr(10);
						$paquete=$paquete."^FO180,1400^A@R^FDCLINICA LAS AMERICAS^FS".chr(13).chr(10);
						$paquete=$paquete."^CF0,20".chr(13).chr(10);
						$paquete=$paquete."^FO155,1400^A@R^FD".$wtdo."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO155,1470^A@R^FD".$wdoc."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO130,1400^A@R^FDSEXO : ".$wsex."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO130,1810^A@R^FDEDAD : ".$wedad."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO100,1400^A@R^FDHISTORIA : ".$whis."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO100,1810^A@R^FDINGRESO : ".$wing."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO155,1700^A@R^FD*** ".$wces." ***^FS".chr(13).chr(10);
						$paquete=$paquete."^CF0,30".chr(13).chr(10);
						$paquete=$paquete."^FO060,1400^A@R^FD".$wpac."^FS".chr(13).chr(10);
						$paquete=$paquete."^BY3,3,105^FT108,2100^BCR,,Y,N,N,A".chr(13).chr(10);
						$paquete=$paquete."^FD".$whis."^FS".chr(13).chr(10);
						$paquete=$paquete."^FT16,1380^XG000.GRF,1,1^FS".chr(13).chr(10);
						$paquete=$paquete."^PQ1,0,1,Y^XZ".chr(13).chr(10);
						$paquete=$paquete."^XA^ID000.GRF^FS^XZ".chr(13).chr(10);

						//echo $paquete."<br>";
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
						$paquete="";
						$paquete=$paquete."CT~~CD,~CC^~CT~".chr(13).chr(10);
						$paquete=$paquete."^XA~TA000~JSN^LT0^MNM^MTD^PON^PMN^LH0,0^JMA^PR2,2~SD21^JUS^LRN^CI0^XZ".chr(13).chr(10);
						$paquete=$paquete."~DG000.GRF,10240,032,".chr(13).chr(10);
						$paquete=$paquete.",:::::::::::::::::::::::::::gJ020P020,,::::::gH02020N020,,gH080A0N080,gG0180,gG0280220202020280,gG010040,gH0802ANA80,gG010051M14,gG02A02AOA020,gG010045H575H564,gH080AOA80,gG01004151515154,gG0280220202020280,gG010040,gH080A0N080,gG010040,gG02A020N020,gG010040,gH080A0,gG010040,gG028020,gG010040,gH08020,gG010040,N020H020M02A0202020,gG010040,gH080A8,gG010040,R0H2M02802A,gG010050,Q0JA80K0802A80,Q01550L01,P02AKAJ02A022A0,P017I740J010I040,P0MAK0802AA00280,O017D515DC0I010011,O02AA0H02AA0H02802AHA0AA0,".chr(13).chr(10);
						$paquete=$paquete."O0560J070L0I5H0740,O0A80J02A0I080AA8A0AA8,O070K0180H01005010H010,N02A0020H0H2I02A02002A02A20,O060L040H010060,O0A0L0280H08028008008,N010M040H018040,N02A0L028002802800200A,N010Q01,O080L028002800A08A8A8,N010M010H010M010,N0280020I028002A02AKA8,N010Q01004545H560,O080L0280H080ALA0,N010Q010051H151,O0A0L020H02802I2,W040H010040,O0280K0A0I08028,O040K010I01,N0H2A020202A0022A02AA02AA0,V0540H0100440H04,P0280H02AA80H080A800AHA8,U0140I010040H05D40,Q020H020J02802002AA2A,gG010J0140,gH080800AA00A,gG010J05,".chr(13).chr(10);
						$paquete=$paquete."L0202020J0I2022A0202AA022,gG010J05,O080L0280H080A02A8008,gG010I014,O0A2M2I02802AHA02AA,W040H010H04540040,O0OA80H0800AA800A8,N0175DKDC0H010H0540,N02ANAI02A02H2020,O0N760H01,O0A8080808A80H080,N010M040H01,O0A0L020H0280,,O0A0Q080,N010Q01,N02A0P02A020J020,gG01,O0A0Q080A0,N010Q01,O0A0P02A02A0V0H20,gG010040,O0A0Q0802AA80S0JA80,N010Q0100550U01550,N02A0P02A02H2HAM020H02ALAJ020,O060P010040440R057I760,O0HAQ0808002A80O0NA80,O050P010J0140P0H5D5D5DC,P02A0N0280I02A2A20L02ANA0,gG010L040N057K75740,".chr(13).chr(10);
						$paquete=$paquete."gH080J080AA0L0PA8,gG010M010L017D55D5D5DC0,N02020L02002A02022A022A0J02APA,gG010O040J057O70,O080M080H080J080H0HA80H0RA80,gG010J010H01540I0H5D5D5D5D5D4,O0OA0202A0I02A2AHA20H02ARA0,O064L460H010M0560J057Q740,O0OA8AA880I02AIAK0TA8,N017FLFC05010J0H1H5L05DD5D5M560,N02ANA02EAA0202AIA020H02ASA8,O0H404J4600610J05740L0S760,O080L02802880AJA80L0TA8,gG010041550M015D5D5D5D5D5D558,O020L020H02802AHA20L02ATA,gG010045440M017S74,gH0802AA0N02ATA,gG0100540O01D5D5H5HDH5D5D5C,W020H02A02A0I020J02ATA80,".chr(13).chr(10);
						$paquete=$paquete."gG010040P0M7607K76,O080L0280H080A0P0NA8ALA80,gG010S0H5D5D5D405D5D5D5,O0A0L020H02A020J020J0NA02AKA80,W060H010S0K747607657I7,O0OA80H080280H08A80I0KA8AA8AA8AIA80,N0140K0160H010R015J505C05415D5D80,N02AKA2AA0H02A02AKA820H0KA02A22A02AIA0,O04004001740H010045I5H4J017I7607607607I7,O080J02A80I080ALA80I0KA0AA8AA8AJA0,U01D0I01005J5150I01D5D5C05405C055D580,T02AA0I02802K2A0I02AJA02A02A02AIA0,U0740I010040O017I7607607607I740,T0HA80J08080J020I02AJA0AA8AA8AJA0,T05E0J010R01D55D405405405I580,".chr(13).chr(10);
						$paquete=$paquete."N0202022AA0I0H2A020J0280H02AJA0AA02A02AIA0,S0570K010R017I7607607607I740,S0HA80K080L0280H02AJA0AA8AA8AJA0,R015C0K010M010I01D5C5C05405C075H5C0,R02AA0K028020I0H2A0H02AHA2A02A02A02A2AA0,R0760U010I01774760760760765740,Q02AA0M0802AKA80H02AJA8AA8AA8AJA0,Q01D80L010040I0150I01D585405405405C1D40,N0202AA0H020H0H2A02AKA02002AHA2A0AA02A02A2AA0,Q0760M010057J740I01770760760760761740,Q0HA80M080ALA80I0HA8AA0AA8AA8AA8AA0,P05D0N010040I010J01D505C05405C07415C0,P02AA0I020H02802A2I2A0I02AA82A02A02A02A0AA0,".chr(13).chr(10);
						$paquete=$paquete."O01740J040H010R01760760760760760740,O02AA0J0280H080A0J0280I0HA8AA0AA8AA8AA8AA0,O07DD5J540H010R015405C0540540560580,N02ANA2002A020J0280H02AHA2A0AA02A02A22A0,W040H010R017607607607607607,O08A8A8A8AA80H080L0280I0HA8AA0AA8AA8AA8AA0,gG010M010I015405C07405C0740580,W020H028020J02A0I0HA02A02A02A02A02A0,gG010040J050J07607607607607607,gH080A8I8HA80I0HA0AA0AA8AA8AA8A80,gG01005K540J05C05405405405405,N020H020H020H0H2A02AKA820202A02A0AA02A02A82A0,gG01004K4L05607607607607606,gH080ALAK02A8AA0AA8AA8AA8280,".chr(13).chr(10);
						$paquete=$paquete."gG010S01405C05405C07404,O0A0L020H028020020M02A02A02A02A02A02,W040H010S01607607607607604,O0A80K0A80H080H02AA80L0A0AA0AA8AA8AA82,N017DLDC0H010H0H5D540K014054054054054,O0OAI02A022AJAK02A22A0AA02A02A82,O0N760H01001540H040K06076076076076,O0OA80H0802AA8A8A0K0A8AA0AA8AA8AA80,gG010050Q0405C07405C074,O0A2M2I02802A2028280J0202A02A02A02A82,gG010070Q04076076076076,O080L0280H080AA00280880I028AA0AA8AA8AA80,gG010040J01040K07405405405C,N020L020H0H2A02800280AA0H0H202A0AA02A02A20,gG010060L040K076076076076,".chr(13).chr(10);
						$paquete=$paquete."gH080A8002808280J0HA0AA8AA8AA80,gG010040J05010K05C05405C078,R0H2M028028002A2A2A0J02A02A02A02A,Q045740N010I0H4H040J076076076070,Q0JA80K08028002AA80A80I0HA0AA8AA8A8,P01FDHDC0J010H0400150N01C054054040,P02AKAJ02A02H202AA002A0H0H2A0AA02A02020,P0H7H457C0I010W060760760,P0MAK080H0A00880N0A0AA8AA8,O01C0I0170I010Y07405C0,O02AA0H02AA0H028020J020N0202A02A0,O060K0140H010040J040P0760640,O0A80J02A0I080AA8A8AA80O0HA8AA0,N0140L040H01005H5D5D50,N02A0020H0H2I02A02AKA820N0H2022020,N010M040H0100404H4540,".chr(13).chr(10);
						$paquete=$paquete."O0A0L0280H080ALA8,N010Q01,N02A0L0280028020I0H2R02,N010Q010M040,O080L0280H080L028,N010Q010M010,N02A20J0H28002A02020H02A0O020,W040H010M070,O080L0280H080L0A8,W040H010M040,O0A0L020H028020J0H2,O040L040H010040J040,O0280K0A0I080A808H8A80A80,O010K05C0H01005K54005,O02A020H0HAI02A02AKA82AA020,P040J040I010045J54004,P0280H02AA80H080ALA80A80,gG01,Q020H020J02802M202,gG01,gH08080,gG01,L020280K020H0H2A02H2HAI020,gG010H057574,O0A0Q0800AJA80,N0170P0100150H01,O0HAP02802AHAH2A0,O04540N0100540I040,O0IAP0802A80H0A8,".chr(13).chr(10);
						$paquete=$paquete."Q01C0M010050,N02022A0M02A02A0I02A20,R0460L010060,R0HA80L080A80J08,Q010150K010040,Q02A2AA0J0280280J0A,U050J010040J010,R0800AA0J080280I028,Q010H01580H010010I0140,R0A002AA2002A02A0022A820,U0H740H010H040H0740,R0A0AIA80H080028002A0,S017D0J01,R0A2AHAJ028002200220,R0H760K01,O0800AHA80J02800AA0,P015DC0L0100154005,N02A2AIA020H0H2A02AA822A020,O0I740M010064500740,O0JAO080AIA0AA8,O05F0O01004004,O0IAO02802A02022A,O060P010040,O0A80P080A8008008,N010Q01,N02A0M02002A02020A022,gG01,O080Q0800800A008,gG010H01004150,".chr(13).chr(10);
						$paquete=$paquete."gG02802ALA,gG01007K740,gH080ALA8,gG010040,N020Q02A02A22A22020,gG010040,gH080A8,gG01,gG02802A202H20,gG010040H0H540,gH0802A80AHA8,gG010J015010,L020S02A02002AA28,gG010J0540,gH080A002A808,gG010J05,gG0280200AA002,gG010J04,gH080A00A8008,gG010I0140010,gG02A02H2A202A20,gG010H0560H040,gH0802AHA02A8,,gG028002A002,,::gJ02020,,::::::::::^XA".chr(13).chr(10);
						$paquete=$paquete."^MMT".chr(13).chr(10);
						$paquete=$paquete."^PW300".chr(13).chr(10);
						$paquete=$paquete."^LL2100".chr(13).chr(10);
						$paquete=$paquete."^LS0".chr(13).chr(10);
						$paquete=$paquete."^XA".chr(13).chr(10);
						$paquete=$paquete."^CF0,60".chr(13).chr(10);
						$paquete=$paquete."^FO220,390^A@R^FDCLINICA LAS AMERICAS^FS".chr(13).chr(10);
						$paquete=$paquete."^CF0,30".chr(13).chr(10);
						$paquete=$paquete."^FO180,400^A@R^FD".$wtdo."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO180,470^A@R^FD".$wdoc."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO140,400^A@R^FDSEXO : ".$wsex."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO140,810^A@R^FDEDAD : ".$wedad."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO100,400^A@R^FDHISTORIA : ".$whis."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO100,810^A@R^FDINGRESO : ".$wing."^FS".chr(13).chr(10);
						$paquete=$paquete."^FO180,700^A@R^FD*** ".$wces." ***^FS".chr(13).chr(10);
						$paquete=$paquete."^CF0,40".chr(13).chr(10);
						$paquete=$paquete."^FO050,400^A@R^FD".$wpac."^FS".chr(13).chr(10);
						$paquete=$paquete."^BY4,3,124^FT150,1100^BCR,,Y,N,N,A".chr(13).chr(10);
						$paquete=$paquete."^FD".$whis."^FS".chr(13).chr(10);
						$paquete=$paquete."^FT42,380^XG000.GRF,1,1^FS".chr(13).chr(10);
						$paquete=$paquete."^PQ1,0,1,Y^XZ".chr(13).chr(10);
						$paquete=$paquete."^XA^ID000.GRF^FS^XZ".chr(13).chr(10);

						//echo $paquete."<br>";
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
						$paquete=$paquete."^FO230,1480^A@R^FDCLINICA LAS AMERICAS^FS".chr(13).chr(10);
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