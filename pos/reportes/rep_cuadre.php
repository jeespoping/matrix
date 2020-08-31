<html>
<head>
  <title>REPORTE CUADRE CAJA - POS</title>
</head>
<body>
<?php
include_once("conex.php");
/**
 * REPORTE DEL ESTADO DE UNA CAJA POSTERIOR A UN CUADRE 
 * 
 * Muestra los 4 tipos de saldos (anteriores: positivos y notas crédito, y nuevos: ingresoss poditivos y notas crédito), cada uno
 * con un consolidado por forma de pago y un consolidado total.
 *  * 
 * Funciona como reporte en donde pide como parametros dos fechas para desplegar una lista de cuadres de los cuales el usuario
 * debe elegir uno para ver el estado de la caja antes de que ocurriera ese cuadre, o lo que es lo mismo el estado de la caja despues 
 * del cuadre inmediatamente anterior al elégido.
 * 
 * Si no funciona como reporte, hace parte del cuadre d caja muestra el estado actual de la caja asignada al usuario,es el paso
 * final del cuadre de caja donde despliega el estado en que quedo la caja gracias al cuadre que se acaba de realizar.
 * 
 * @author Ana María Betancur Vargas
 * @created 2005-08-01
 * @version 2005-10-17
 * 
 * @wvar String[4]	$wcco 		Codigo Centro de costos.
 * @wvar String		$wnomcco 	Nombre del centro de costos.
 * @wvar String		$wcaja		Código Caja objeto del cuadre.
 * @wvar String		$wnomcaja	Nombre Caja  objeto del cuadre.
 * @wvar String[2] 	$Ccofre		Fuente Recibos de Caja Centro de costos.
 * @wvar String[2] 	$Ccofnc		Fuente nota Crédito Centro de costos.
 * @wvar String[5]	$usuario		Código del usuario que realiza el cuadre
 * @wvar String[8]	$fecha		Fecha del cuadre.
 * @wvar String[6]	$hora		Hora del cuadre.
 * @wvar Integer	$numCua		Número del cuadre.
 * @wvar Array		$arr		Arreglo donde se almacenan todos los valores a mostrar en pantalla.<br>
 * 								[0]:Código Forma de pago x.<br> 
 * 								[1]:Descripción Forma de pago x.<br> 
 * 								[2]:Valor Saldo anterior para la forma de pago x.<br>
 * 								[3]:Valor ingresos para la forma de pago x.<br> entre el ultimo cuadre de caja y el que se esta evaluando
 * 								[4]:Valor Egreso para la forma de pago x.<br>
 * 								[5]:Valor Saldo Final para la forma de pago x.<br>
 * 								[6]:Valor Saldo anterior de NC para la forma de pago x.<br>
 * 								[7]:Valor ingresos para la forma de pago x entre el ultimo cuadre de caja y el que se esta evaluando.
 * @wvar array		$fps		Arreglo donde se almacenan las formas de pago.<br>			
 * 								[0]:Código de la forma de pago.<br>
 * 								[1]:Descripción de la forma de pago.<br>
 * 
 * @table	00003	SELECT
 * @table	000022	SELECT
 * @table	000023	SELECT
 * @table	000030	SELECT	
 * @table	000037	SELECT
 * @table	usuarios	SELECT 
 * 
 * @modified 2005-10-17 Se inicializan las fechas con la fecha actual.
 * @modified 2005-10-10 Se usa el include de paleta de colores.
 * @modified 2005-10-10 Se cambian las tablas farstore por tipo_tablas.
 */

$wactualiz="2005-10-17";

include_once("paleta.php");

$wautor="Ana Maria Betancur V.";
echo "<p align=right><font size=1><b>Autor: ".$wautor."</b></font></p>";

/**
* Muestra en pantalla las formas de pago 
* 
* 
* @return void
* @param Integer $fps dice el numero de formas de pago
* @param Array $arr arreglo con las formas de pago y el valor
* 
*/
function Pantalla($fps,$arr) {

	global $AmarClar;	//COLOR DEL FONDO 3 PARA RESALTAR -- Amarillo quemado claro
	global $AguaOsc;	//COLOR DEL FONDO 5 -- Aguamarina Oscuro
	global $AzulText; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

	echo "<br><br><table border='1' >";
	$tit[0]="SALDO ANTERIOR";
	$tit[1]="INGRESOS";
	$tit[2]="EGRESOS";
	$tit[3]="NUEVO SALDO";
	$vueltas=0;
	do{
		$totP=0;
		echo "<tr><td colspan=2 bgcolor='$AguaOsc' align='center' ><b><font face='arial' color=".$AzulText." size=6 >".$tit[$vueltas]."</B></td>";
		for($i=0;$i<$fps;$i++){
			echo "<tr><td class='lAM'><font face='arial' color=".$AzulText." size=6 >".$arr[$i][0]."-".$arr[$i][1]."</td>";
			echo "<td class='lAM'  align='right'><font face='arial' color=".$AzulText." size=6 >$ ".number_format($arr[$i][$vueltas+2],"2",".",",")."</td><tr>";
			$totP=$totP+$arr[$i][$vueltas+2];
		}
		$vueltas=$vueltas+1;
		echo "<tr bgcolor='$AmarClar'><td bgcolor='$AmarClar'><b><font face='arial' color=".$AzulText." size=6 >TOTAL</td>";
		echo "<td  align='right'><b><font face='arial' color='".$AzulText."' size=6 >$ ".number_format($totP,"2",".",",")."</td>";
		//$totT=$totT+$totP;
	}while($vueltas<4);
}



if(!isset($_SESSION['user']))
echo "error";
else{

	if(!isset($cuadre)) {
		/*NO SE HA ESCOGIDO EL CUADRE AL CUAL SE LE VA A MOSTRAR EL REPORTE*/

		echo '<form action="" method="POST">';
		echo "<center><table border width='390'>";
		echo "<tr><td align=center colspan='2' ><img src='/matrix/images/medical/POS/logo_".$tipo_tablas.".png' WIDTH=388 HEIGHT=70></td></tr>";
		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>REPORTE CUADRE DE CAJA </b></font></td></tr>";


		if(!isset($fecha1)){
			/*Pide los rangos de fecha entre los cuales se encuentra el cuadre deseado*/
			echo "</table><BR><BR><center><table border width='390'>";
			echo "<tr><td align=left bgcolor=".$AzulClar."><font size=3 color='#FFFFFF'><b>FECHA DE INICIO </b></font></td>";
			echo "<td align=center  bgcolor=".$AzulClar."><input type='text' name='fecha1' value='".date('Y-m-d')."' size='9'></font></td>";
			echo "<tr><td align=left  bgcolor=".$AzulClar."><font size=3 color='#FFFFFF'><b>FECHA DE FIN </b></font></td>";
			echo "<td align=center  bgcolor=".$AzulClar."><input type='text' name='fecha2' value='".date('Y-m-d')."' size='9'></font></td>";

		}else{
			

			


			//ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
			$q =  " SELECT cjecco, cjecaj   "
			."   FROM ".$tipo_tablas."_000030 "
			."  WHERE cjeusu = '".substr($user,2)."'"
			."    AND cjeest = 'on' ";
			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0) {

				$row = mysql_fetch_array($err);

				$pos = strpos($row[0],"-");
				$wcco = substr($row[0],0,$pos);						//Código del centro de costos
				$wnomcco = substr($row[0],$pos+1,strlen($row[0]));	//Nombre del centro de costos
				echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
				echo "<input type='HIDDEN' name= 'wnomcco' value='".$wnomcco."'>";

				$wcaja=$row[1];
				echo "<input type='HIDDEN' name= 'wcaja' value='".$wcaja."'>";
			}



			echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>REPORTE CUADRE DE CAJA </b></font></td></tr>";
			echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>$wcaja </b></font></td></tr>";

			echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=2 text color=#FFFFFF><b>DESDE $fecha1 HASTA $fecha2</b></font></td></tr>";

			echo "</table><BR><BR><center><table border width='390'>";

			$pos=explode("-",$wcaja);
			$wcaja = $pos[0];
			$wnomcaj = $pos[1];

			/*Muestra los cuadres realizados entre el rango de fecha escogido*/
			$q="SELECT Fecha_data,Hora_data,Cdecua "
			."FROM ".$tipo_tablas."_000037 "
			."WHERE	Cdeest='on' "
			."AND	Cdecco='$wcco' "
			."AND	Cdecaj='".$wcaja."' "
			."AND	Fecha_data between '$fecha1' and '$fecha2'"
			."group by Cdecua";
			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0) {
				for($i=0;$i<$num;$i++){
					$row = mysql_fetch_array($err);
					echo "<tr><td align=center ><input type='radio' name='cuadre' value='".$row[2]."'></td><td><font size=3 text color=#003366>Cuadre # ".$row[2].". Realizado el ".$row[0]." a las ".$row[1]."</font></td></tr>";
				}
			}
		}
		echo "<input type='hidden' name='tipo_tablas' value='$tipo_tablas'>";
		echo "</table><br><br><table align='center' border='0'><tr><td align='center'><input type='submit' name='aceptar' value='ACEPTAR'></td></tr></table>";
		ECHO "</FORM>";


	}
	else{
		/**
	 	 * AQUI EMPIEZA EL PROGRAMA
	 	 */
		

		


		echo "<center><table border >";
		echo "<tr><td align=center colspan='2' ><img src='/matrix/images/medical/POS/logo_".$tipo_tablas.".png' WIDTH=532 HEIGHT=105></td></tr>";
		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=6 text color=#FFFFFF><b>REPORTE CUADRE DE CAJA </b></font></td></tr>";
		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=6 text color=#FFFFFF><b>$wcaja</b></font></td></tr>";
		echo "</table>";

		echo "<input type='hidden' name='wcaja' value='".$wcaja."'>";
		$pos=explode("-",$wcaja);
		$wcaja = $pos[0];
		$wnomcaj = $pos[1];

		if(!isset($usuario)){

			$q="SELECT Seguridad,Fecha_data,Hora_data "
			."FROM ".$tipo_tablas."_000037 "
			."WHERE	Cdecua='".$cuadre."' "
			."AND	Cdecaj='".$wcaja."' ";
			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);
			if($num > 0){
				$row=mysql_fetch_row($err);
				$ini=explode("-",$row[0]);
				$usuario=$ini[1];
				$fecha=$row[1];
				$hora=$row[2];
			}
		}

		$q="SELECT Descripcion "
		."FROM usuarios "
		."WHERE Codigo='$usuario'";
		$err=mysql_query($q,$conex);
		$num=mysql_num_rows($err);
		if($num > 0){
			$row=mysql_fetch_row($err);
			$usuario=$row[0];
		}


		$q="SELECT Ccofrc, Ccofnc "
		."FROM ".$tipo_tablas."_000003 "
		."WHERE Ccocod	= '".$wcco."' "
		."AND Ccoest	= 'on'";
		$err1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($err1);
		if ($num1 > 0)
		{
			$row1=mysql_fetch_row($err1);
			//$Ccoffp=$row1[0];
			$Ccofre=$row1[0];
			$Ccofnc=$row1[1];
		}


		echo "<BR><BR><center><table border >";
		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=6 text color=#FFFFFF><b>CUADRE DE CAJA  # $cuadre</b></font></td></tr>";
		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=5 text color=#FFFFFF><b>REALIZADO POR: $usuario </b></font></td></tr>";
		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=6 text color=#FFFFFF><b>$fecha $hora </b></font></td></tr>";
		echo "</table>";



		/*Inicializar el Array arr donde:
		$arr[x][0]= Código Forma de pago
		$arr[x][1]= Descripción Forma de pago
		$arr[x][2]= Valor Saldo anterior para la forma de pago x
		$arr[x][3]= Valor ingresos para la forma de pago x entre el ultimo cuadre de caja y el que se esta evaluando
		$arr[x][4]= Valor Egreso para la forma de pago x
		$arr[x][5]= Valor Saldo Final para la forma de pago x
		$arr[x][6]= Valor Saldo anterior de NC para la forma de pago x
		$arr[x][7]= Valor ingresos para la forma de pago x entre el ultimo cuadre de caja y el que se esta evaluando
		Se inicializan los primeros dos datos con la info de la base de datos y el resto en '0'
		*/
		$q="select Fpacod,Fpades "
		."FROM ".$tipo_tablas."_000023 "
		."WHERE Fpaest = 'on' "
		."ORDER BY Fpacod";
		$err = mysql_query($q,$conex);
		$fps = mysql_num_rows($err);
		if ($fps > 0)
		{
			for($i=0;$i<$fps;$i++){
				$row=mysql_fetch_row($err);
				$arr[$i][0]=$row[0];
				$arr[$i][1]=$row[1];
				for($j=2;$j<=5;$j++){
					$arr[$i][$j]=0;
				}
			}
		}

		/*Saldo de Cuadre de caja anterior DE VENTAS*/
		$q="SELECT SUM(Cdevrf), Cdefpa, Fpades "
		."FROM ".$tipo_tablas."_000037, ".$tipo_tablas."_000023 "
		."WHERE	Cdecua	=	'".($cuadre-1)."' "	//Pertenece al cuadre de caja anterior
		."and 	Cdecaj	=	'".$wcaja."' "
		."and	Cdecco	=	'".$wcco."' "
		."and	Cdefue	=	'".$Ccofre."' "
		."and	Cdevrf 	<>	0 "	//quedo un valor pendiente
		."and 	Fpacod	=	Cdefpa "	//El código de la forma de pago correponde al de el detalle del CuC
		."GROUP BY Cdefpa "
		."ORDER BY Cdefpa ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$p=0;
			for($i=0;$i<$num;$i++) {
				$row=mysql_fetch_row($err);
				$j=$p;
				$ok='';
				do{
					if($row[1] == $arr[$j][0]){
						$arr[$j][2]=$row[0]; //saldo pendiente de cuadre de caja anterior
						$ok='on';
						$p++;
					}
					$j++;
				}while(($ok != 'on') and ($j < $fps));
			}
		}



		/*Saldo de Cuadre de caja anterior DE NC*/
		$q="SELECT SUM(Cdevrf), Cdefpa, Fpades "
		."FROM ".$tipo_tablas."_000037, ".$tipo_tablas."_000023 "
		."WHERE	Cdecua	=	'".($cuadre-1)."' "	//Pertenece al cuadre de caja anterior
		."and 	Cdecaj	=	'".$wcaja."' "
		."and	Cdecco	=	'".$wcco."' "
		."and	Cdefue	=	'".$Ccofnc."' "
		."and	Cdevrf 	<>	0 "	//quedo un valor pendiente
		."and 	Fpacod	=	Cdefpa "	//El código de la forma de pago correponde al de el detalle del CuC
		."GROUP BY Cdefpa "
		."ORDER BY Cdefpa ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$p=0;
			for($i=0;$i<$num;$i++) {
				$row=mysql_fetch_row($err);
				$j=$p;
				$ok='';
				do{
					if($row[1] == $arr[$j][0]){
						$arr[$j][2]=$arr[$j][2]-$row[0]; //saldo pendiente de cuadre de caja anterior
						$ok='on';
						$p++;
					}
					$j++;
				}while(($ok != 'on') and ($j < $fps));
			}
		}



		/*INGRESOS VENTAS: Recibos que no tengan asociado ningun registro en la tabla de detalle de Cuadre de caja(".$tipo_tablas."_0000037) */
		$table="table_".date("dis");
		$q= "Create table  IF NOT EXISTS ".$table." as "
		."SELECT * "
		."FROM ".$tipo_tablas."_000037 "
		."WHERE	Cdecaj = '".$wcaja."' "
		."and	Cdecco = '".$wcco."' "
		."and	Cdecua = '".$cuadre."' "
		."and	Cdefue	=	'".$Ccofre."' "
		."and	Cdeest = 'on' "
		."ORDER BY Cdefue,Cdenum ";
		$err = mysql_query($q,$conex);

		$table1="table1_".date("dis");
		$q= "Create table  IF NOT EXISTS ".$table1." as "
		."SELECT Cdenum,Cdefue,Cdefpa,Sum(Cdevrf) "
		."FROM $table "
		."WHERE	NOT EXISTS (SELECT	* "
		."					FROM	".$tipo_tablas."_000037 "
		."					WHERE 	".$tipo_tablas."_000037.Cdeest = 'on' "
		."					and 	".$tipo_tablas."_000037.Cdecaj = $table.Cdecaj "
		."					and 	".$tipo_tablas."_000037.Cdecco = $table.Cdecco "
		."					and 	".$tipo_tablas."_000037.Cdefue = $table.Cdefue "
		."					and 	".$tipo_tablas."_000037.Cdenum = $table.Cdenum "
		."					and 	".$tipo_tablas."_000037.Cdecua = '".($cuadre-1)."') "
		."GROUP BY Cdefue,Cdenum,Cdefpa "
		."ORDER BY Cdefpa ";
		$err = mysql_query($q,$conex);

		$query = "DROP table ".$table;
		$err = mysql_query($query,$conex);

		$q="SELECT Rfpfpa,SUM(Rfpvfp) FROM $table1,".$tipo_tablas."_000022 "
		."WHERE Rfpfpa = Cdefpa "
		."and	Rfpfue = Cdefue "
		."and	Rfpnum = Cdenum "
		."and	Rfpcco = '".$wcco."' "
		."GROUP BY Rfpfpa ";
		$err=mysql_query($q,$conex);
		$num=mysql_num_rows($err);
		$p=0;
		if ($num > 0)
		{
			for($i=0;$i<$num;$i++) {
				$row=mysql_fetch_row($err);
				$j=$p;
				$ok='';
				do{
					if($row[0] == $arr[$j][0]){
						$arr[$j][3]=$row[1]; //Valor de ingreso para la forma de pago $arr[$j][0]
						$ok='on';
						$p++;
					}
					$j++;
				}while(($ok != 'on') and ($j < $fps));
			}
		}
		$query = "DROP table ".$table1;
		$err = mysql_query($query,$conex);


		/*INGRESOS NOTA CRÉDITO: Recibos que no tengan asociado ningun registro en la tabla de detalle de Cuadre de caja(".$tipo_tablas."_0000037) y su fuente sea de nota crédito*/
		$table="table_".date("dis");
		$q= "Create table  IF NOT EXISTS ".$table." as "
		."SELECT * "
		."FROM ".$tipo_tablas."_000037 "
		."WHERE	Cdecaj = '".$wcaja."' "
		."and	Cdecco = '".$wcco."' "
		."and	Cdecua = '".$cuadre."' "
		."and	Cdefue	=	'".$Ccofnc."' "
		."and	Cdeest = 'on' "
		."ORDER BY Cdefue,Cdenum ";
		$err = mysql_query($q,$conex);

		$table1="table1_".date("dis");
		$q= "Create table  IF NOT EXISTS ".$table1." as "
		."SELECT Cdenum,Cdefue,Cdefpa,Sum(Cdevrf) "
		."FROM $table "
		."WHERE	NOT EXISTS (SELECT	* "
		."					FROM	".$tipo_tablas."_000037 "
		."					WHERE 	".$tipo_tablas."_000037.Cdeest = 'on' "
		."					and 	".$tipo_tablas."_000037.Cdecaj = $table.Cdecaj "
		."					and 	".$tipo_tablas."_000037.Cdecco = $table.Cdecco "
		."					and 	".$tipo_tablas."_000037.Cdefue = $table.Cdefue "
		."					and 	".$tipo_tablas."_000037.Cdenum = $table.Cdenum "
		."					and 	".$tipo_tablas."_000037.Cdecua = '".($cuadre-1)."') "
		."GROUP BY Cdefue,Cdenum,Cdefpa "
		."ORDER BY Cdefpa ";
		$err = mysql_query($q,$conex);

		$query = "DROP table ".$table;
		$err = mysql_query($query,$conex);

		$q="SELECT Rfpfpa,SUM(Rfpvfp) FROM $table1,".$tipo_tablas."_000022 "
		."WHERE Rfpfpa = Cdefpa "
		."and	Rfpfue = Cdefue "
		."and	Rfpnum = Cdenum "
		."and	Rfpcco = '".$wcco."' "
		."GROUP BY Rfpfpa ";
		$err=mysql_query($q,$conex);
		$num=mysql_num_rows($err);
		$p=0;
		
		if ($num > 0)
		{
			for($i=0;$i<$num;$i++) {
				$row=mysql_fetch_row($err);
				$j=$p;
				$ok='';
				do{
					if($row[0] == $arr[$j][0]){
						$arr[$j][3]=$arr[$j][3]-$row[1]; //Valor de ingreso para la forma de pago $arr[$j][0]
						$ok='on';
						$p++;
					}
					$j++;
				}while(($ok != 'on') and ($j < $fps));
			}
		}
		$query = "DROP table ".$table1;
		$err = mysql_query($query,$conex);

		/*BUSCAR EL REMANENTE DEL CUADRE DE CAJA*/
		$salant=0;
		$ccant="";
		$q="SELECT SUM(Cdevrf), Cdefpa, Fpades "
		."FROM ".$tipo_tablas."_000037, ".$tipo_tablas."_000023 "
		."WHERE	Cdecua	=	'".$cuadre."' "	//Pertenece al cuadre de caja anterior
		."and 	Cdecaj	=	'".$wcaja."' "
		."and	Cdecco	=	'".$wcco."' "
		."and	Cdefue	=	'".$Ccofre."' "
		."and	Cdevrf 	<>	0 "	//quedo un valor pendiente
		."and 	Fpacod	=	Cdefpa "	//El código de la forma de pago correponde al de el detalle del CuC
		."GROUP BY Cdefpa "
		."ORDER BY Cdefpa ";
	
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$p=0;
			for($i=0;$i<$num;$i++) {
				$row=mysql_fetch_row($err);
				$j=$p;
				$ok='';
				do{
					if($row[1] == $arr[$j][0]){
						$arr[$j][5]=$row[0];
						$ok='on';
						$p++;
					}
					$j++;
				}while(($ok != 'on') and ($j < $fps));
			}
		}

		/*BUSCAR EL REMANENTE DEL CUADRE DE CAJA NC*/
		$salant=0;
		$ccant="";
		$q="SELECT SUM(Cdevrf), Cdefpa, Fpades "
		."FROM ".$tipo_tablas."_000037, ".$tipo_tablas."_000023 "
		."WHERE	Cdecua	=	'".$cuadre."' "	//Pertenece al cuadre de caja anterior
		."and 	Cdecaj	=	'".$wcaja."' "
		."and	Cdecco	=	'".$wcco."' "
		."and	Cdefue	=	'".$Ccofnc."' "
		."and	Cdevrf 	<>	0 "	//quedo un valor pendiente
		."and 	Fpacod	=	Cdefpa "	//El código de la forma de pago correponde al de el detalle del CuC
		."GROUP BY Cdefpa "
		."ORDER BY Cdefpa ";
		
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$p=0;
			for($i=0;$i<$num;$i++) {
				$row=mysql_fetch_row($err);
				$j=$p;
				$ok='';
				do{
					if($row[1] == $arr[$j][0]){
						$arr[$j][5]=$arr[$j][5]-$row[0];
						$ok='on';
						$p++;
					}
					$j++;
				}while(($ok != 'on') and ($j < $fps));
			}
		}

		/*Generar los egresos
		Saldo anterior+ Ingresos- Saldo nuevo*/
		for($i=0;$i<$fps;$i++){
			$arr[$i][4]=$arr[$i][2]+$arr[$i][3]-$arr[$i][5];
		}
		Pantalla($fps,&$arr);
	}
}
?>
</body>
</html>