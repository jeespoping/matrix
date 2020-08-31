<HTML>
<HEAD>
<TITLE>REPORTE DIAS DISPOSITIVOS INVECLA</TITLE>
</HEAD>
<BODY>

  <!-- Estas 5 lineas es para que funcione el Calendar al capturar fechas -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>    
    
<?php

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
        
 $key = substr($user,2,strlen($user));
	include_once("conex.php"); 
	mysql_select_db("matrix");
 echo "<form name='repdispositivo' action='rep_dispositivo.php' method=post>";  
 
 if (!isset($wfec1) or $wfec1=='')
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>REPORTE DIAS DISPOSITIVOS DE INVECLA<br></font></b>";   
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
	echo "<center><table border=0>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>REPORTE DIAS DISPOSITIVOS MEDICOS INVECLA</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: rep_dispositivo.php Ver. 2019/09/03<br>AUTOR: Gabriel Agudelo</font></b><br>";
    echo "</table>";

    echo "<br>";
		  
	/*	$querytmp = "CREATE TEMPORARY TABLE IF NOT EXISTS temphce_1
					(INDEX idx(Movhis,Moving))*/
		//Query cateteres			
		$querytmp = "select c.movhis,c.Moving,c.Movdat,c.id 
					  from (select  a.movhis, a.moving, max( id ) id 
							from hce_000422 a
							where a.Fecha_data between '".$wfec1."' and '".$wfec2."' 
							and a.movcon = 12 
                            group by 1, 2 ) a, hce_000422 c
					  where c.id = a.id";
					
		$rs = mysql_query( $querytmp, $conex ) or die( mysql_error() );
	    $num1 = mysql_num_rows($rs);
	
	//Query sondas			
		$querytmp1 = "select c.movhis,c.Moving,c.Movdat,c.id 
					  from (select  a.movhis, a.moving, max( id ) id 
							from hce_000422 a
							where a.Fecha_data between '".$wfec1."' and '".$wfec2."' 
							and a.movcon = 7 
                            group by 1, 2 ) a, hce_000422 c
					  where c.id = a.id";
					
		$rs1 = mysql_query( $querytmp1, $conex ) or die( mysql_error() );
	    $num2 = mysql_num_rows($rs1);
		 
	//eSTE ES PARA LAS SONDAS VESICALES
		$rs10 = mysql_query( $querytmp1, $conex ) or die( mysql_error() );
	    $nums = mysql_num_rows($rs10);
	  // Detalle o titulos de los campos de la tabla
	
		echo "<center><table border=0>";
		echo "<br>";
		echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>CATETERES CENTRALES</font></b><br>";
		echo "</table>";
		
		echo "<table border=1 cellspacing=1 cellpadding=1 align=center size=4>"; 
		echo "<tr>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>HISTORIA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>INGRESO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA INSERCION</b></td>";//0
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ID</b></td>";//1
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TIPO CATETER</b></td>";//2
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2>&nbsp<b>LATERALIDAD</b>&nbsp</td>";//3
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2>&nbsp&nbsp<b>LOCALIZACION</b>&nbsp&nbsp</td>";//4
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>PERSONAL</b></td>";//5
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>PROTEGIDO</b></td>";//6
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>INSTALA</b></td>";//7
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>RETIRA</b></td>";//8
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA RETIRO</b></td>";//9
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DIAS CATETER</b></td>";//10
		/*echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA MINIMA</b></td>";//11
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA MAXIMA</b></td>";//12
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CANTIDAD DIAS</b></td>";//13 */
		echo "</tr>";  
		$contot = 0;
		$contot1 = 0;
		$cont1182=0;
		$cont1183=0;
		$cont1184=0;
		$cont1020=0;
		$cont1187=0;
		$cont1180=0;
		$cont1185=0;
		$cont1186=0;
		$cont1188=0;
		$cont1190=0;
		$cont1189=0;
		$cont1286=0;
		$cont1282=0;
		$cont1281=0;
		$cont1283=0;
		$cont1284=0;
		$cont1285=0;
		$cont1179=0;
		$y = 0;
		for ($i=1;$i<=$num1;$i++)
		{
			
			$row = mysql_fetch_array($rs);
			
			$dato=explode('*',$row[2]);// Esta es la posicion donde viene el Dato que necesitamos y lo separamos por *  
						
			$bandera = 0;//Para manejar primera vez que entra al ciclo por historia
			$dias = 0;
			$cont = 0;
			$aq=0;
			for ($j=1;$j<=$dato[0];$j++)  //El primer dato del arreglo es el que me dice cuantas lineas debo recorrer en el ciclo. 
			{
			  $dato1=explode('|',$dato[$j]); //la seleccion esta en hce_000012 seltab=549 
			  if ($dato1[2] == '01-CVC' or $dato1[2] == '03-Swan Ganz' or $dato1[2] == '04-PICC' or $dato1[2] == '06-Epicutaneo' or $dato1[2] == '07-Umb Ven.' or $dato1[2] == '10-Intr. venoso' or $dato1[2] == '10-Intr ven.' or $dato1[2] == '14-C. Hemodialisis' )//solo para contar este tipo de cateter de acduerdo a la solicitud
				{
				  if (is_int ($y/2))
					  {
						$wcf="F8FBFC";  // color de fondo
					  }
					 else
					  {
						$wcf="DFF8FF"; // color de fondo
					  }
				  echo "<Tr bgcolor=".$wcf.">";
				  echo "<td align=center><font size=3>".$row[0]."</font></td>";
				  echo "<td align=center><font size=3>".$row[1]."</font></td>";
				  echo "<td align=center><font size=3>".$dato1[0]."</font></td>";
				  echo "<td align=center><font size=3>".$dato1[1]."</font></td>";
				  echo "<td align=center><font size=3>".$dato1[2]."</font></td>";
				  echo "<td align=center><font size=3>".$dato1[3]."</font></td>";
				  echo "<td align=center><font size=3>".$dato1[4]."</font></td>";
				  echo "<td align=center><font size=3>".$dato1[5]."</font></td>";
				  echo "<td align=center><font size=3>".$dato1[6]."</font></td>"; 
				  echo "<td align=center><font size=3>".$dato1[7]."</font></td>";
				  echo "<td align=center><font size=3>".$dato1[8]."</font></td>";
				  echo "<td align=center><font size=3>".$dato1[9]."</font></td>";
				  echo "<td align=center><font size=3>".$dato1[10]."</font></td>";
				  if ($bandera == 0)//Primera vez que entra al ciclo por historia asigno los valores fecha maxima y fecha minima
				  {
					  $fecmin = $dato1[0];
					  if ($dato1[8] == 'Seleccione' )
						{
							$fecmax = $wfec2;
						}
					  else
						{
							$fecmax = $dato1[9];
						}
					  
					  $fecmax1 = new DateTime($fecmax);
					  $fecmin1 = new DateTime($fecmin);
					  $dias = $fecmin1->diff($fecmax1);
					  $cont = (int) ($cont + 1 );//se debe sumar 1 al contador ya que se cuenta el dia mismo en que lo instalan
					  $cont = $cont + $dias->days;
					  $bandera = 1;
					  $dias=0;
					}
				  else
				  {
					 
					 if ($fecmax < $dato1[0] )
						{
							
								if ($dato1[8] == 'Seleccione' )
									{
										$fecmax = $wfec2;
									}
								  else
									{
										$fecmax = $dato1[9];
									}
								
								$fecmin1=new DateTime($dato1[0]);
								$fecmax2=new DateTime($fecmax);
								$dias = $fecmin1 ->diff ($fecmax2);
								$cont = $cont + $dias->days;
								$dias = 0;
					
						}
					if ($dato1[8] == 'Seleccione' )
						{
							if($fecmax < $wfec2 )
								{
									$fecmin1=new DateTime($fecmax);
									$fecmax2=new DateTime($wfec2);
									$dias = $fecmin1 ->diff ($fecmax2);
									$cont = $cont + $dias->days;
									$fecmax = $wfec2;
									$dias = 0;
								}
						}
					 
					 if ($dato1[9] > $fecmax )
					  {  
						  $fecmax1=new DateTime($fecmax);
						  $fecmax2=new DateTime($dato1[9]);
						  $dias = $fecmax1 ->diff ($fecmax2);
						  $cont = $cont + $dias->days;
						  $fecmax = $dato1[9];
						  $dias = 0;
					  }  
					  
					  if ($dato1[0] < $fecmin )
					  {  
						  $fecmin1=new DateTime($fecmin);
						  $fecmin2=new DateTime($dato1[0]);
						  $dias = $fecmin1 ->diff ($fecmin2);
						  $cont = $cont + $dias->days;
						  $fecmin = $dato1[0];
						  $dias = 0;
						  
					  }  
					
				  }
				  echo "</tr>";  
				  
					  $y = $y + 1;
					  $aq = 1;//Esta variable controla que entre al ciclo solo si cumple esta 

					//aca voy a contar los cateter por centro de costos
					
						$fecmincco = $dato1[0]; //fecha insercion del dispositivo
						$fecmaxcco = $dato1[9]; //fecha retiro del dispositivo
						if ($fecmincco < $wfec1) //Esto se hace ya que debo contar dia a dia los cateter por centro de costos desde la fecha inicial y no desde el inicio de la implantacion del cateter 
							$fecmincco = $wfec1;
						if ($dato1[8] == 'Seleccione' )
							$fecmaxcco = $wfec2; //Esto es cuando la variable de fecha de retiro viene en blanco
						$querytmp2 = "select Fecha_data,movhis,moving,movdat
								 from hce_000422 
								 where movhis = '".$row[0]."' 
								   and moving = '".$row[1]."'						 
								   and Fecha_data between '".$fecmincco."' and '".$fecmaxcco."' 
								   and Movcon = 4 
								 group by Fecha_data,movhis,moving";

						//echo ". ".$querytmp2;		
						
						$rs2 = mysql_query( $querytmp2, $conex ) or die( mysql_error() );
						$num3 = mysql_num_rows($rs2);
						for ($k=1;$k<=$num3;$k++)
						{
							$row3 = mysql_fetch_array($rs2);
							$dato3=explode('-',$row3[3]);
							if ($dato3[0] == '1182') 
								$cont1182 = $cont1182 + 1;
							if ($dato3[0] == '1183') 
								$cont1183 = $cont1183 + 1;
							if ($dato3[0] == '1184') 
								$cont1184 = $cont1184 + 1;
							if ($dato3[0] == '1020') 
								$cont1020 = $cont1020 + 1;
							if ($dato3[0] == '1187') 
								$cont1187 = $cont1187 + 1;
							if ($dato3[0] == '1185') 
								$cont1185 = $cont1185 + 1;
							if ($dato3[0] == '1180') 
								$cont1180 = $cont1180 + 1;
							if ($dato3[0] == '1186') 
								$cont1186 = $cont1186 + 1;
							if ($dato3[0] == '1188') 
								$cont1188 = $cont1188 + 1;
							if ($dato3[0] == '1190') 
								$cont1190 = $cont1190 + 1;
							if ($dato3[0] == '1189') 
								$cont1189 = $cont1189 + 1;
							if ($dato3[0] == '1286') 
								$cont1286 = $cont1286 + 1;
							if ($dato3[0] == '1282') 
								$cont1282 = $cont1282 + 1;
							if ($dato3[0] == '1281') 
								$cont1281 = $cont1281 + 1;
							if ($dato3[0] == '1283') 
								$cont1283 = $cont1283 + 1;
							if ($dato3[0] == '1284') 
								$cont1284 = $cont1284 + 1;
							if ($dato3[0] == '1285') 
								$cont1285 = $cont1285 + 1;
							if ($dato3[0] == '1179') 
								$cont1179 = $cont1179 + 1;
						}
						$contot = $cont1182 + $cont1183 + $cont1184 + $cont1020 + $cont1187 +  $cont1180 + $cont1185 + $cont1186 + $cont1188 
						  + $cont1190 + $cont1189 + $cont1286 + $cont1282 + $cont1281 + $cont1283 + $cont1284 + $cont1285 + $cont1179 ;

				}
				
			}

		}
	echo "</table>"; 
	
	echo "<center><table border=0>";
	echo "<br>";
	echo "<tr><td align=center bgcolor=#DFF8FF colspan=><b><font text color=#003366 size=4><i>TOTAL DIAS CATETERES CENTRALES <br>POR CENTRO DE COSTOS</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1182 - HOSP. PISO 3 - TORRE 3  = ".$cont1182."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1183 - HOSP. PISO 4 - TORRE 3  = ".$cont1183."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1184 - HOSP. PISO 5 - TORRE 3  = ".$cont1184."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1020 - UCI                     = ".$cont1020."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1187 - UCE - PISO 1 - TORRE 3  = ".$cont1187."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1180 - UCE - PISO 2 - TORRE 3  = ".$cont1180."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1185 - HOSP. PISO 6 - TORRE 3  = ".$cont1185."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1186 - HOSP. PISO 7 - TORRE 3  = ".$cont1186."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1188 - HOSP. PISO 3 -ONCOLOGIA = ".$cont1188."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1190 - NEONATOS                = ".$cont1190."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1189 - TRANS.PROGENITORES HEMA.= ".$cont1189."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1286 - HOSP. CORTA EST- TORRE 3= ".$cont1286."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1282 - UCE -TORRE 4            = ".$cont1282."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1281 - HOSP. PISO 1- TORRE 4   = ".$cont1281."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1283 - HOSP. PISO 3- TORRE 4   = ".$cont1283."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1284 - HOSP. PISO 4- TORRE 4   = ".$cont1284."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1285 - HOSP. PISO 5- TORRE 4   = ".$cont1285."</font></b><br>";
	echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1179 - HOSP. MED. NUCL- TORRE 3= ".$cont1179."</font></b><br>";
	echo "</table>";
	
	
	echo "<center><table border=0>";
	echo "<br>";
	echo "<tr><td align=center bgcolor=#DFF8FF colspan=><b><font text color=#003366 size=4><i>TOTAL DIAS CATETERES CENTRALES</font></b><br>";
	echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>".$contot."</font></b><br>";
	echo "</table>";
	
	//*******************aca va la segunda parte de los datos que piden  con el segundo query VENTILACION MECANICA
	// Detalle o titulos de los campos de la tabla
	
		echo "<center><table border=0>";
		echo "<br>";
		echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>VENTILACION MECANICA</font></b><br>";
		echo "</table>";
		
		echo "<table border=1 cellspacing=1 cellpadding=1 align=center size=4>"; 
		echo "<tr>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>HISTORIA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>INGRESO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA INSTALACION</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ID</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TIPO DISPOSITIVO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2>&nbsp<b>SITIO INSERCION</b>&nbsp</td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2>&nbsp&nbsp<b>UNIDAD QUE INSTALA</b>&nbsp&nbsp</td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>UNIDAD QUE RETIRA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA DE RETIRO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DIAS</b></td>";
		/*echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA MINIMA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA MAXIMA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CANTIDAD DIAS</b></td>";*/
		echo "</tr>";  
		
		$contot = 0;
		$contot1 = 0;
		$cont1182=0;
		$cont1183=0;
		$cont1184=0;
		$cont1020=0;
		$cont1187=0;
		$cont1180=0;
		$cont1185=0;
		$cont1186=0;
		$cont1188=0;
		$cont1190=0;
		$cont1189=0;
		$cont1286=0;
		$cont1282=0;
		$cont1281=0;
		$cont1283=0;
		$cont1284=0;
		$cont1285=0;
		$cont1179=0;
		$y = 0;
		
		
	for ($i=1;$i<=$num2;$i++)
		{
			
			$row = mysql_fetch_array($rs1);
			
			$dato=explode('*',$row[2]);// Esta es la posicion donde viene el Dato que necesitamos y lo separamos por *  
						
			$bandera = 0;//Para manejar primera vez que entra al ciclo por historia
			$dias = 0;
			$cont = 0;
			$aq=0;
			for ($j=1;$j<=$dato[0];$j++)  //El primer dato del arreglo es el que me dice cuantas lineas debo recorrer en el ciclo. 
			{
			  $dato1=explode('|',$dato[$j]); //la seleccion esta en hce_000012 seltab=471 
			  
			  if (  $dato1[2] == '26-TOT' or $dato1[2] == '26-TOT VM' or $dato1[2] == '30-Traqueostomia VM' )//solo para contar estos tipos de acuerdo a la solicitud
				{
				  if (is_int ($y/2))
					  {
						$wcf="F8FBFC";  // color de fondo
					  }
					 else
					  {
						$wcf="DFF8FF"; // color de fondo
					  }
				      echo "<Tr bgcolor=".$wcf.">";
					  echo "<td align=center><font size=3>".$row[0]."</font></td>";
					  echo "<td align=center><font size=3>".$row[1]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[0]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[1]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[2]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[3]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[4]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[5]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[6]."</font></td>"; 
					  echo "<td align=center><font size=3>".$dato1[7]."</font></td>";

				  if ($bandera == 0)//Primera vez que entra al ciclo por historia asigno los valores fecha maxima y fecha minima
				  {
					  $fecmin = $dato1[0];
					  if ($dato1[5] == 'Seleccione' )
						{
							$fecmax = $wfec2;
						}
					  else
						{
							$fecmax = $dato1[6];
						}
					  
					  $fecmax1 = new DateTime($fecmax);
					  $fecmin1 = new DateTime($fecmin);
					  $dias = $fecmin1->diff($fecmax1);
					  $cont = (int) ($cont + 1 );//se debe sumar 1 al contador ya que se cuenta el dia mismo en que lo instalan
					  $cont = $cont + $dias->days;
					  $bandera = 1;
					  $dias=0;
				  }
				  else
				  {
					 
					 if ($fecmax < $dato1[0] )
						{
							
								if ($dato1[5] == 'Seleccione' )
									{
										$fecmax = $wfec2;
									}
								  else
									{
										$fecmax = $dato1[6];
									}
								
								$fecmin1=new DateTime($dato1[0]);
								$fecmax2=new DateTime($fecmax);
								$dias = $fecmin1 ->diff ($fecmax2);
								$cont = $cont + $dias->days;
								$dias = 0;
					
						}
					if ($dato1[5] == 'Seleccione' )
						{
							if($fecmax < $wfec2 )
								{
									$fecmin1=new DateTime($fecmax);
									$fecmax2=new DateTime($wfec2);
									$dias = $fecmin1 ->diff ($fecmax2);
									$cont = $cont + $dias->days;
									$fecmax = $wfec2;
									$dias = 0;
								}
						}
					 
					 if ($dato1[6] > $fecmax )
					  {  
						  $fecmax1=new DateTime($fecmax);
						  $fecmax2=new DateTime($dato1[6]);
						  $dias = $fecmax1 ->diff ($fecmax2);
						  $cont = $cont + $dias->days;
						  $fecmax = $dato1[6];
						  $dias = 0;
					  }  
					  
					  if ($dato1[0] < $fecmin )
					  {  
						  $fecmin1=new DateTime($fecmin);
						  $fecmin2=new DateTime($dato1[0]);
						  $dias = $fecmin1 ->diff ($fecmin2);
						  $cont = $cont + $dias->days;
						  $fecmin = $dato1[0];
						  $dias = 0;
						  
					  }  
					
				  }
				
				  echo "</tr>";  
				  
					  $y = $y + 1;
					  $aq = 1;//Esta variable controla que entre al ciclo solo si cumple esta 
						//aca voy a contar las VENTILACION MECANICA por centro de costos
						
							$fecmincco = $dato1[0]; //fecha insercion del dispositivo
							$fecmaxcco = $dato1[6]; //fecha retiro del dispositivo
							if ($fecmincco < $wfec1) //Esto se hace ya que debo contar dia a dia las VENTILACION MECANICA por centro de costos desde la fecha inicial y no desde el inicio de la implantacion del cateter 
								$fecmincco = $wfec1;
							if ($dato1[5] == 'Seleccione' )
								$fecmaxcco = $wfec2; //Esto es cuando la variable de fecha de retiro viene en blanco
						
							$querytmp2 = "select Fecha_data,movhis,moving,movdat
									 from hce_000422 
									 where movhis = '".$row[0]."' 
									   and moving = '".$row[1]."'						 
									   and Fecha_data between '".$fecmincco."' and '".$fecmaxcco."' 
									   and Movcon = 4 
									 group by Fecha_data,movhis,moving";
									
							$rs2 = mysql_query( $querytmp2, $conex ) or die( mysql_error() );
							$num3 = mysql_num_rows($rs2);
							for ($k=1;$k<=$num3;$k++)
							{
								$row3 = mysql_fetch_array($rs2);
								$dato3=explode('-',$row3[3]);
								if ($dato3[0] == '1182') 
									$cont1182 = $cont1182 + 1;
								if ($dato3[0] == '1183') 
									$cont1183 = $cont1183 + 1;
								if ($dato3[0] == '1184') 
									$cont1184 = $cont1184 + 1;
								if ($dato3[0] == '1020') 
									$cont1020 = $cont1020 + 1;
								if ($dato3[0] == '1187') 
									$cont1187 = $cont1187 + 1;
								if ($dato3[0] == '1185') 
									$cont1185 = $cont1185 + 1;
								if ($dato3[0] == '1180') 
									$cont1180 = $cont1180 + 1;
								if ($dato3[0] == '1186') 
									$cont1186 = $cont1186 + 1;
								if ($dato3[0] == '1188') 
									$cont1188 = $cont1188 + 1;
								if ($dato3[0] == '1190') 
									$cont1190 = $cont1190 + 1;
								if ($dato3[0] == '1189') 
									$cont1189 = $cont1189 + 1;
								if ($dato3[0] == '1286') 
									$cont1286 = $cont1286 + 1;
								if ($dato3[0] == '1282') 
									$cont1282 = $cont1282 + 1;
								if ($dato3[0] == '1281') 
									$cont1281 = $cont1281 + 1;
								if ($dato3[0] == '1283') 
									$cont1283 = $cont1283 + 1;
								if ($dato3[0] == '1284') 
									$cont1284 = $cont1284 + 1;
								if ($dato3[0] == '1285') 
									$cont1285 = $cont1285 + 1;
								if ($dato3[0] == '1179') 
									$cont1179 = $cont1179 + 1;
							}
							$contot = $cont1182 + $cont1183 + $cont1184 + $cont1020 + $cont1187 +  $cont1180 + $cont1185 + $cont1186 + $cont1188 
							  + $cont1190 + $cont1189 + $cont1286 + $cont1282 + $cont1281 + $cont1283 + $cont1284 + $cont1285 + $cont1179 ;
				}
				
				
			}
		
		}
			echo "</table>"; 
			
			echo "<center><table border=0>";
			echo "<br>";
			echo "<tr><td align=center bgcolor=#DFF8FF colspan=><b><font text color=#003366 size=4><i>TOTAL DIAS VENTILACION MECANICA <br>POR CENTRO DE COSTOS</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1182 - HOSP. PISO 3 - TORRE 3  = ".$cont1182."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1183 - HOSP. PISO 4 - TORRE 3  = ".$cont1183."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1184 - HOSP. PISO 5 - TORRE 3  = ".$cont1184."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1020 - UCI                     = ".$cont1020."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1187 - UCE - PISO 1 - TORRE 3  = ".$cont1187."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1180 - UCE - PISO 2 - TORRE 3  = ".$cont1180."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1185 - HOSP. PISO 6 - TORRE 3  = ".$cont1185."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1186 - HOSP. PISO 7 - TORRE 3  = ".$cont1186."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1188 - HOSP. PISO 3 -ONCOLOGIA = ".$cont1188."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1190 - NEONATOS                = ".$cont1190."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1189 - TRANS.PROGENITORES HEMA.= ".$cont1189."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1286 - HOSP. CORTA EST- TORRE 3= ".$cont1286."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1282 - UCE - TORRE 4           = ".$cont1282."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1281 - HOSP. PISO 1- TORRE 4   = ".$cont1281."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1283 - HOSP. PISO 3- TORRE 4   = ".$cont1283."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1284 - HOSP. PISO 4- TORRE 4   = ".$cont1284."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1285 - HOSP. PISO 5- TORRE 4   = ".$cont1285."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1179 - HOSP. MED. NUCL- TORRE 3= ".$cont1179."</font></b><br>";
			echo "</table>";
			
			
			echo "<center><table border=0>";
			echo "<br>";
			echo "<tr><td align=center bgcolor=#DFF8FF colspan=><b><font text color=#003366 size=4><i>TOTAL DIAS VENTILACION MECANICA</font></b><br>";
			echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>".$contot."</font></b><br>";
			echo "</table>";
			
			//*****************ACA VA LA TERCERA PARTE LAS SONDAS VESICALES********************************//
			
			echo "<center><table border=0>";
		echo "<br>";
		echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>SONDAS VESICALES</font></b><br>";
		echo "</table>";
		
		echo "<table border=1 cellspacing=1 cellpadding=1 align=center size=4>"; 
		echo "<tr>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>HISTORIA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>INGRESO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA INSTALACION</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ID</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TIPO DISPOSITIVO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2>&nbsp<b>SITIO INSERCION</b>&nbsp</td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2>&nbsp&nbsp<b>UNIDAD QUE INSTALA</b>&nbsp&nbsp</td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>UNIDAD QUE RETIRA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA DE RETIRO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DIAS</b></td>";
		/*echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA MINIMA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA MAXIMA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CANTIDAD DIAS</b></td>";*/
		echo "</tr>";  
		
		$contot = 0;
		$contot1 = 0;
		$cont1182=0;
		$cont1183=0;
		$cont1184=0;
		$cont1020=0;
		$cont1187=0;
		$cont1180=0;
		$cont1185=0;
		$cont1186=0;
		$cont1188=0;
		$cont1190=0;
		$cont1189=0;
		$cont1286=0;
		$cont1282=0;
		$cont1281=0;
		$cont1283=0;
		$cont1284=0;
		$cont1285=0;
		$cont1179=0;
		$y = 0;
		
		
	for ($i=1;$i<=$nums;$i++)
		{
			
			$row = mysql_fetch_array($rs10);
			
			$dato=explode('*',$row[2]);// Esta es la posicion donde viene el Dato que necesitamos y lo separamos por *  
						
			$bandera = 0;//Para manejar primera vez que entra al ciclo por historia
			$dias = 0;
			$cont = 0;
			$aq=0;
			for ($j=1;$j<=$dato[0];$j++)  //El primer dato del arreglo es el que me dice cuantas lineas debo recorrer en el ciclo. 
			{
			  $dato1=explode('|',$dato[$j]); //la seleccion esta en hce_000012 seltab=471 
			  
			  if ( $dato1[2] == '17-S. Vesical' )//solo para contar estos tipos de acuerdo a la solicitud
				{
				  if (is_int ($y/2))
					  {
						$wcf="F8FBFC";  // color de fondo
					  }
					 else
					  {
						$wcf="DFF8FF"; // color de fondo
					  }
				      echo "<Tr bgcolor=".$wcf.">";
					  echo "<td align=center><font size=3>".$row[0]."</font></td>";
					  echo "<td align=center><font size=3>".$row[1]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[0]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[1]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[2]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[3]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[4]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[5]."</font></td>";
					  echo "<td align=center><font size=3>".$dato1[6]."</font></td>"; 
					  echo "<td align=center><font size=3>".$dato1[7]."</font></td>";

				  if ($bandera == 0)//Primera vez que entra al ciclo por historia asigno los valores fecha maxima y fecha minima
				  {
					  $fecmin = $dato1[0];
					  if ($dato1[5] == 'Seleccione' )
						{
							$fecmax = $wfec2;
						}
					  else
						{
							$fecmax = $dato1[6];
						}
					  
					  $fecmax1 = new DateTime($fecmax);
					  $fecmin1 = new DateTime($fecmin);
					  $dias = $fecmin1->diff($fecmax1);
					  $cont = (int) ($cont + 1 );//se debe sumar 1 al contador ya que se cuenta el dia mismo en que lo instalan
					  $cont = $cont + $dias->days;
					  $bandera = 1;
					  $dias=0;
				  }
				  else
				  {
					 
					 if ($fecmax < $dato1[0] )
						{
							
								if ($dato1[5] == 'Seleccione' )
									{
										$fecmax = $wfec2;
									}
								  else
									{
										$fecmax = $dato1[6];
									}
								
								$fecmin1=new DateTime($dato1[0]);
								$fecmax2=new DateTime($fecmax);
								$dias = $fecmin1 ->diff ($fecmax2);
								$cont = $cont + $dias->days;
								$dias = 0;
					
						}
					if ($dato1[5] == 'Seleccione' )
						{
							if($fecmax < $wfec2 )
								{
									$fecmin1=new DateTime($fecmax);
									$fecmax2=new DateTime($wfec2);
									$dias = $fecmin1 ->diff ($fecmax2);
									$cont = $cont + $dias->days;
									$fecmax = $wfec2;
									$dias = 0;
								}
						}
					 
					 if ($dato1[6] > $fecmax )
					  {  
						  $fecmax1=new DateTime($fecmax);
						  $fecmax2=new DateTime($dato1[6]);
						  $dias = $fecmax1 ->diff ($fecmax2);
						  $cont = $cont + $dias->days;
						  $fecmax = $dato1[6];
						  $dias = 0;
					  }  
					  
					  if ($dato1[0] < $fecmin )
					  {  
						  $fecmin1=new DateTime($fecmin);
						  $fecmin2=new DateTime($dato1[0]);
						  $dias = $fecmin1 ->diff ($fecmin2);
						  $cont = $cont + $dias->days;
						  $fecmin = $dato1[0];
						  $dias = 0;
						  
					  }  
					
				  }
				 /* echo "<td align=center><font size=3>".$fecmin."</font></td>";
				  echo "<td align=center><font size=3>".$fecmax."</font></td>";
				  echo "<td align=center><font size=3>".$cont."</font></td>";*/
				  echo "</tr>";  
				  
					  $y = $y + 1;
					  $aq = 1;//Esta variable controla que entre al ciclo solo si cumple esta 
							
							//aca voy a contar las sondas por centro de costos
							$fecmincco = $dato1[0]; //fecha insercion del dispositivo
							$fecmaxcco = $dato1[6]; //fecha retiro del dispositivo
							if ($fecmincco < $wfec1) //Esto se hace ya que debo contar dia a dia las sondas por centro de costos desde la fecha inicial y no desde el inicio de la implantacion del cateter 
								$fecmincco = $wfec1;
							if ($dato1[5] == 'Seleccione' )
								$fecmaxcco = $wfec2; //Esto es cuando la variable de fecha de retiro viene en blanco
							
							$querytmp2 = "select Fecha_data,movhis,moving,movdat
									 from hce_000422 
									 where movhis = '".$row[0]."' 
									   and moving = '".$row[1]."'						 
									   and Fecha_data between '".$fecmincco."' and '".$fecmaxcco."' 
									   and Movcon = 4 
									 group by Fecha_data,movhis,moving";
							
							$rs2 = mysql_query( $querytmp2, $conex ) or die( mysql_error() );
							$num3 = mysql_num_rows($rs2);
							for ($k=1;$k<=$num3;$k++)
							{
								$row3 = mysql_fetch_array($rs2);
								$dato3=explode('-',$row3[3]);
								if ($dato3[0] == '1182') 
									$cont1182 = $cont1182 + 1;
								if ($dato3[0] == '1183') 
									$cont1183 = $cont1183 + 1;
								if ($dato3[0] == '1184') 
									$cont1184 = $cont1184 + 1;
								if ($dato3[0] == '1020') 
									$cont1020 = $cont1020 + 1;
								if ($dato3[0] == '1187') 
									$cont1187 = $cont1187 + 1;
								if ($dato3[0] == '1185') 
									$cont1185 = $cont1185 + 1;
								if ($dato3[0] == '1180') 
									$cont1180 = $cont1180 + 1;
								if ($dato3[0] == '1186') 
									$cont1186 = $cont1186 + 1;
								if ($dato3[0] == '1188') 
									$cont1188 = $cont1188 + 1;
								if ($dato3[0] == '1190') 
									$cont1190 = $cont1190 + 1;
								if ($dato3[0] == '1189') 
									$cont1189 = $cont1189 + 1;
								if ($dato3[0] == '1286') 
									$cont1286 = $cont1286 + 1;
								if ($dato3[0] == '1282') 
									$cont1282 = $cont1282 + 1;
								if ($dato3[0] == '1281') 
									$cont1281 = $cont1281 + 1;
								if ($dato3[0] == '1283') 
									$cont1283 = $cont1283 + 1;
								if ($dato3[0] == '1284') 
									$cont1284 = $cont1284 + 1;
								if ($dato3[0] == '1285') 
									$cont1285 = $cont1285 + 1;
								if ($dato3[0] == '1179') 
									$cont1179 = $cont1179 + 1;
							}
							$contot = $cont1182 + $cont1183 + $cont1184 + $cont1020 + $cont1187 +  $cont1180 + $cont1185 + $cont1186 + $cont1188 
							  + $cont1190 + $cont1189 + $cont1286 + $cont1282 + $cont1281 + $cont1283 + $cont1284 + $cont1285 + $cont1179 ;

				}
	
			}
					
		}
			echo "</table>"; 
			
			echo "<center><table border=0>";
			echo "<br>";
			echo "<tr><td align=center bgcolor=#DFF8FF colspan=><b><font text color=#003366 size=4><i>TOTAL DIAS SONDAS VESICALES <br>POR CENTRO DE COSTOS</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1182 - HOSP. PISO 3 - TORRE 3  = ".$cont1182."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1183 - HOSP. PISO 4 - TORRE 3  = ".$cont1183."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1184 - HOSP. PISO 5 - TORRE 3  = ".$cont1184."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1020 - UCI                     = ".$cont1020."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1187 - UCE - PISO 1 - TORRE 3  = ".$cont1187."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1180 - UCE - PISO 2 - TORRE 3  = ".$cont1180."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1185 - HOSP. PISO 6 - TORRE 3  = ".$cont1185."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1186 - HOSP. PISO 7 - TORRE 3  = ".$cont1186."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1188 - HOSP. PISO 3 -ONCOLOGIA = ".$cont1188."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1190 - NEONATOS                = ".$cont1190."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1189 - TRANS.PROGENITORES HEMA.= ".$cont1189."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1286 - HOSP. CORTA EST- TORRE 3= ".$cont1286."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1282 - UCE -TORRE 4            = ".$cont1282."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1281 - HOSP. PISO 1- TORRE 4   = ".$cont1281."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1283 - HOSP. PISO 3- TORRE 4   = ".$cont1283."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1284 - HOSP. PISO 4- TORRE 4   = ".$cont1284."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1285 - HOSP. PISO 5- TORRE 4   = ".$cont1285."</font></b><br>";
			echo "<tr><td align=left bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>1179 - HOSP. MED. NUCL- TORRE 3= ".$cont1179."</font></b><br>";
			echo "</table>";
		
			echo "<center><table border=0>";
			echo "<br>";
			echo "<tr><td align=center bgcolor=#DFF8FF colspan=><b><font text color=#003366 size=4><i>TOTAL DIAS SONDAS VESICALES</font></b><br>";
			echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4>".$contot."</font></b><br>";
			echo "</table>";	
		
}   

echo "</BODY>";
echo "</HTML>";	

?>