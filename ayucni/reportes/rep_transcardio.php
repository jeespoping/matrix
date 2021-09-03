<HTML>
<HEAD>
<TITLE>REPORTE PACIENTES DE TRANSCRIPCION DE CARDIOLOGIA NO INVASIVA</TITLE>
</HEAD>
<BODY>

  <!-- Estas 5 lineas es para que funcione el Calendar al capturar fechas --
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>-->    
    
<?php
include_once("conex.php");
include_once("root/comun.php");

$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
$wactualiz = "2017/07/17";
encabezado( "PACIENTES CARDIOLOGIA NO INVASIVA TRANSCRIPCION", $wactualiz, $institucion->baseDeDatos );

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
        
 $key = substr($user,2,strlen($user));
	

	

 echo "<form name='pacreingpiso' action='rep_transcardio.php?wemp_pmla=".$wemp_pmla."' method=post>"; 
 echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>"; 
 
 if (!isset($wfec1) or $wfec1=='')
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	//echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>PACIENTES CARDIOLOGIA NO INVASIVA TRANSCRIPCION<br></font></b>";   
	echo "</tr>";

	
  if (!isset($wfec1))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec1=date("Y-m-d");
    
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Inicial<br></font></b>";   
   	$cal="calendario('wfec1','1')";
	echo "<input type='TEXT' name='wfec1' size=10 maxlength=10  id='wfec1'  value=".$wfec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

  if (!isset($wfec2))   // Si no esta seteada entonces la inicializo en el ultimo dia del mes actual con formato aaaa-mm-dd
    $wfec2=date("Y-m-d");
  
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Final  <br></font></b>";   
   	$cal="calendario('wfec2','1')";
	echo "<input type='TEXT' name='wfec2' size=10 maxlength=10  id='wfec2' value=".$wfec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
	
   echo "<tr><td align=center bgcolor=#cccccc colspan=4><input type='submit' value='Generar'></td>";          //submit osea el boton de Generar o Aceptar
   echo "</tr>";
   echo "</table>";
   
 }	
 else      // Cuando ya estan todos los datos escogidos
 {

	$wayucni = consultarAliasPorAplicacion($conex, $wemp_pmla, "ayudas_diag");
	$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");

	echo "<center><table border=0>";
    //echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>PACIENTES CARDIOLOGIA NO INVASIVA TRANSCRIPCION</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: rep_transcardio.php Ver. 2017/07/17<br>AUTOR: Gabriel Agudelo</font></b><br>";
    echo "</table>";

    echo "<br>";
	// Abro el archivo
   	   $archivo = fopen("rep_transcardio.txt","w"); 
	  
		$querytmp = "CREATE TEMPORARY TABLE IF NOT EXISTS temptranscardio
					(INDEX idx(Esphis,Ingcem))
					select Esphis,Ingcem,Empnom
					from ".$wayucni."_000006 a left join ".$wcliame."_000101 b on ( a.Esphis = b.Inghis and a.Esping = b.Ingnin) left join ".$wcliame."_000024 c on (b.Ingcem = c.Empcod)
					where   a.Fecha_data between '".$wfec1."' and '".$wfec2."'  
					group by Esphis
					order by a.Espcod,a.Fecha_data ";
		$rs = mysql_query( $querytmp, $conex ) or die( mysql_error() );
	
	 $query="Select a.Fecha_data,MONTH(a.Fecha_data),DAY(a.Fecha_data),Espcod,a.Esphis,Esping,Esptdo,Espdoc,CONCAT(Pacno1,' ',Pacno2,' ',Pacap1,' ',Pacap2 ),Pacfna,TIMESTAMPDIFF(YEAR,Pacfna,a.Fecha_data),Pacsex,Pacdir,Pactel,Ingtpa,c.Ingcem,e.Empnom,Espenf,Enfdes,Espfmo,Esphmo,Espusm,Espfce,Esphce,Espuce,Esplog,Espudg,Espequ,Espest 
			 from   ".$wayucni."_000006 a left join ".$wcliame."_000101 c on ( a.Esphis = c.Inghis and a.Esping = c.Ingnin) left join ".$wcliame."_000100 d on (c.Inghis = d.Pachis ) left join temptranscardio e on (d.Pachis = e.Esphis),".$wayucni."_000001 b 
			 where   a.Fecha_data between '".$wfec1."' and '".$wfec2."' 
			  and  a.Espenf = b.Enfcod  
	 order by a.Espcod,a.Fecha_data";
	 
     $err1 = mysql_query($query,$conex);
     $num1 = mysql_num_rows($err1);
	 echo "<li><A href='rep_transcardio.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
     echo "<br>";
     echo "<li>Registros generados: ".$num1;
	  // Detalle o titulos de los campos de la tabla
	   fwrite($archivo, "FECHA|MES|DIA|CONSECUTIVO DEL EXAMEN|HISTORIA|INGRESO|TIPO DOCUMENTO|DOCUMENTO|PACIENTE|FECHA NACIMIENTO|EDAD|SEXO|DIRECCION|TELEFONO|TIPO PACIENTE|ENTIDAD|DESCRIPCION|COD EXAMEN|EXAMEN|FECHA ULTIMA MOD|HORA ULTIMA MOD|ULTIMO USUARIO MOD|FECHA CIERRE EXAMEN|HORA CIERRE EXAMEN|USUARIO CIERRA EXAMEN|TRANSCRIPCION|UBICACION DIGITAL RESULTADO|EQUIPO|ACTIVO/INACTIVO|MEDICO INTERPRETA|DIAGNOSTICO|SONOGRAFO|POSITIVO(S/N)|CONCLUSIONES|USUARIO MODIFICA|USUARIO TRANSCRIBE|CODIGO INDICACION EXAMEN|DESCRIPCION INDICACION EXAMEN|BIDIMENSIONAL|COD MED REMITE|MEDICO REMITE|" ); 
	   fwrite($archivo, chr(13).chr(10) );   
	
		echo "<table border=1 cellspacing=1 cellpadding=1 align=center size=4>"; 
		echo "<tr>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>MES</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DIA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2>&nbsp<b>CONSECUTIVO DEL EXAMEN</b>&nbsp</td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2>&nbsp&nbsp<b>HISTORIA</b>&nbsp&nbsp</td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>INGRESO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TIPO DOCUMENTO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DOCUMENTO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>PACIENTE</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA NACIMIENTO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>EDAD</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>SEXO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DIRECCION</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TELEFONO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TIPO PACIENTE</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ENTIDAD</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DESCRIPCION</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>COD EXAMEN</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>EXAMEN</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA ULTIMA MOD</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>HORA ULTIMA MOD</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ULTIMO USUARIO MOD</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA CIERRE EXAMEN</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>HORA CIERRE EXAMEN</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>USUARIO CIERRA EXAMEN</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TRANSCRIPCION</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>UBICACION DIGITAL RESULTADO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>EQUIPO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ACTIVO/INACTIVO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>MEDICO INTERPRETA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DIAGNOSTICO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>SONOGRAFO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>POSITIVO(S/N)</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CONCLUSIONES</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>USUARIO MODIFICA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>USUARIO TRANSCRIBE</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CODIGO INDICACION EXAMEN</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DESCRIPCION INDICACION EXAMEN</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>BIDIMENSIONAL</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>COD MED REMITE</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>MEDICO REMITE</b></td>";
		echo "</tr>";  
		for ($i=1;$i<=$num1;$i++)
		{
		 if (is_int ($i/2))
		  {
			$wcf="F8FBFC";  // color de fondo
		  }
		 else
		  {
			$wcf="DFF8FF"; // color de fondo
		  }

			$row1 = mysql_fetch_array($err1);
		
		  if ($row1[17]== 'ECODOPV2')
			{
				$query2 ="select Opcdes,Exadi1,Exatec,Exapos,Exanr3,Exausm,a.seguridad,Exaiex,Descripcion,Exanr2 "
					  ."from ".$wayucni."_000016 a left join root_000011 on (Exaiex = Codigo),".$wayucni."_000005 b " 
					  ."where Exacon =  ".$row1[3]." "
					  ." and  Exatx5 = Opccod  and  Opctbl = 'CARDIOLOGOS' ";
				
				$query3 ="select Exarem,Opcdes "
					  ."from ".$wayucni."_000016 ,".$wayucni."_000005 b " 
					  ."where Exacon =  ".$row1[3]." "
					  ." and  Exarem = Opccod  and  Opctbl = 'MEDREMV2' ";
			}
		  if ($row1[17]== 'ECOESTRV2')
			{
				$query2 ="select Opcdes,Exadi1,Exatec,Exapos,Exanr3,Exausm,a.seguridad,Exaiex,Descripcion,Exanr2 "
					  ."from ".$wayucni."_000017 a left join root_000011 on (Exaiex = Codigo),".$wayucni."_000005 b " 
					  ."where Exacon =  ".$row1[3]." "
					  ." and  Exatx5 = Opccod  and  Opctbl = 'CARDIOLOGOS' ";
					  
				$query3 ="select Exarem,Opcdes "
					  ."from ".$wayucni."_000017 ,".$wayucni."_000005 b " 
					  ."where Exacon =  ".$row1[3]." "
					  ." and  Exarem = Opccod  and  Opctbl = 'MEDREMV2' ";
			}
		  if ($row1[17]== 'ECOPEDV2')
			{
				$query2 ="select Opcdes,Exadi1,Exatec,Exapos,Exanr3,Exausm,a.seguridad,Exaiex,Descripcion,Exanr2 "
					  ."from ".$wayucni."_000018 a left join root_000011 on (Exaiex = Codigo),".$wayucni."_000005 b " 
					  ."where Exacon =  ".$row1[3]." "
					  ." and  Exatx5 = Opccod and  Opctbl = 'CARDIOLOGOS'  ";
					  
				$query3 ="select Exarem,Opcdes "
					  ."from ".$wayucni."_000018 ,".$wayucni."_000005 b " 
					  ."where Exacon =  ".$row1[3]." "
					  ." and  Exarem = Opccod  and  Opctbl = 'MEDREMV2' ";
			}
		  if ($row1[17]== 'ECOTRANSV2')
			{
				$query2 ="select Opcdes,Exadi1,Exatec,Exapos,Exanr3,Exausm,a.seguridad,Exaiex,Descripcion,Exanr2 "
					  ."from ".$wayucni."_000019 a left join root_000011 on (Exaiex = Codigo),".$wayucni."_000005 b " 
					  ."where Exacon =  ".$row1[3]." "
					  ." and  Exatx5 = Opccod and  Opctbl = 'CARDIOLOGOS' ";
					  
				$query3 ="select Exarem,Opcdes "
					  ."from ".$wayucni."_000019 ,".$wayucni."_000005 b " 
					  ."where Exacon =  ".$row1[3]." "
					  ." and  Exarem = Opccod  and  Opctbl = 'MEDREMV2' ";
			}
		  if ($row1[17]== 'ECODIPIV2')
			{
				$query2 ="select Opcdes,Exadi1,Exatec,Exapos,Exanr3,Exausm,a.seguridad,Exaiex,Descripcion,Exanr2 "
					  ."from ".$wayucni."_000020 a left join root_000011 on (Exaiex = Codigo),".$wayucni."_000005 b " 
					  ."where Exacon =  ".$row1[3]." "
					  ." and  Exatx5 = Opccod and  Opctbl = 'CARDIOLOGOS' ";
					  
				$query3 ="select Exarem,Opcdes "
					  ."from ".$wayucni."_000020 ,".$wayucni."_000005 b " 
					  ."where Exacon =  ".$row1[3]." "
					  ." and  Exarem = Opccod  and  Opctbl = 'MEDREMV2' ";
			}
		  if ($row1[17]== 'ECOEJERV2')
			{
				$query2 ="select Opcdes,Exadi1,Exatec,Exapos,Exanr3,Exausm,a.seguridad,Exaiex,Descripcion,Exanr2 "
					  ."from ".$wayucni."_000021 a left join root_000011 on (Exaiex = Codigo),".$wayucni."_000005 b " 
					  ."where Exacon =  ".$row1[3]." "
					  ." and  Exatx5 = Opccod and  Opctbl = 'CARDIOLOGOS' ";
					  
				$query3 ="select Exarem,Opcdes "
					  ."from ".$wayucni."_000021 ,".$wayucni."_000005 b " 
					  ."where Exacon =  ".$row1[3]." "
					  ." and  Exarem = Opccod  and  Opctbl = 'MEDREMV2' ";
			}
			
			$err2 = mysql_query($query2,$conex);
			$row2 = mysql_fetch_array($err2);
			$err3 = mysql_query($query3,$conex);
			$row3 = mysql_fetch_array($err3);
			
		
		  echo "<Tr bgcolor=".$wcf.">";
		  echo "<td align=center><font size=3>".$row1[0]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[1]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[2]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[3]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[4]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[5]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[6]."</font></td>"; 
		  echo "<td align=center><font size=3>".$row1[7]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[8]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[9]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[10]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[11]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[12]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[13]."</font></td>"; 
		  echo "<td align=center><font size=3>".$row1[14]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[15]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[16]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[17]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[18]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[19]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[20]."</font></td>"; 
		  echo "<td align=center><font size=3>".$row1[21]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[22]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[23]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[24]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[25]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[26]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[27]."</font></td>";
		  echo "<td align=center><font size=3>".$row1[28]."</font></td>";
		  echo "<td align=center><font size=3>".$row2[0]."</font></td>";
		  echo "<td align=center><font size=3>".$row2[1]."</font></td>";
		  echo "<td align=center><font size=3>".$row2[2]."</font></td>";
		  echo "<td align=center><font size=3>".$row2[3]."</font></td>";
		  echo "<td align=center><font size=3>".$row2[4]."</font></td>";
		  echo "<td align=center><font size=3>".$row2[5]."</font></td>";
		  echo "<td align=center><font size=3>".$row2[6]."</font></td>";
		  echo "<td align=center><font size=3>".$row2[7]."</font></td>";
		  echo "<td align=center><font size=3>".$row2[8]."</font></td>";
		  echo "<td align=center><font size=3>".$row2[9]."</font></td>";
		  echo "<td align=center><font size=3>".$row3[0]."</font></td>";
		  echo "<td align=center><font size=3>".$row3[1]."</font></td>";
		  echo "</tr>";  
		  $LineaDatos = "";
		  for ($j = 0; $j <= 28; $j++)
		  {
			$row1[$j]= str_replace("|", ' ',$row1[$j]);
			$LineaDatos=$LineaDatos.$row1[$j]."|";
			$lineadatos = str_replace(chr(13).chr(10) , ' ',$lineadatos); 
		    $lineadatos = str_replace("\n", ' ', $lineadatos);
		  }
		  $LineaDatos1 = "";
		  for ($k = 0; $k <= 9; $k++)
		  {
			$row2[$k]= str_replace("|", ' ',$row2[$k]);
			$LineaDatos1=$LineaDatos1.$row2[$k]."|";
			$lineadatos1 = str_replace(chr(13).chr(10) , ' ',$lineadatos1); 
		    $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1);
		  }
		
		fwrite($archivo,$LineaDatos.$LineaDatos1.$row3[0]."|".$row3[1]."|".chr(13).chr(10) );
		  
		}
	echo "</table>"; 
	fclose($archivo);
		echo "<li><A href='rep_transcardio.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
       	echo "<br>";
       	echo "<li>Registros generados: ".$num1;

 }   

echo "</BODY>";
echo "</HTML>";	

?>