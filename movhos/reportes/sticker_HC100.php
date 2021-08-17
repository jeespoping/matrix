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
						/*$paquete="";
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
						$paquete=$paquete."^FO180,1400^A@R^FDCLINICA PORTO AZUL^FS".chr(13).chr(10);
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
						$paquete=$paquete."^XA^ID000.GRF^FS^XZ".chr(13).chr(10);*/
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
							^FO21,745^GFA,3153,14688,36,:Z64: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:8F76
							^FT239,1204^A0R,33,33^FH\^CI28^FDCLINICA LAS AMERICAS AUNA^FS^CI27
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
						/*$paquete="";
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
						$paquete=$paquete."^FO220,390^A@R^FDCLINICA PORTO AZUL^FS".chr(13).chr(10);
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
						$paquete=$paquete."^XA^ID000.GRF^FS^XZ".chr(13).chr(10);*/
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
							^FT246,641^A0R,33,33^FH\^CI28^FDCLINICA LAS AMERICAS AUNA^FS^CI27
							^FT205,641^A0R,33,33^FH\^CI28^FD".$wtdo." : ".$wdoc."^FS^CI27
							^FT164,641^A0R,33,33^FH\^CI28^FDSEXO :".$wsex."^FS^CI27
							^FT123,641^A0R,33,33^FH\^CI28^FDHISTORIA : ".$whis."^FS^CI27
							^FT82,641^A0R,33,33^FH\^CI28^FD".$wpac."^FS^CI27
							^FO51,280^GFA,2241,9324,28,:Z64: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:0417
							^FT210,1113^A0R,33,33^FH\^CI28^FD*** ".$wces." ***^FS^CI27
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