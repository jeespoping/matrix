<html>
<head>
<title>Visitas magenta</title>

</head>
<body >

	


<?php
include_once("conex.php");

/********************************************************
*    REPORTE DE LISTA DE VISITAS AAA O BBB	*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Portal para Megenta y comunicaciones, control programa AFINIDAD y comentarios y sugerencias
//AUTOR							:Carolina Castaño
//FECHA CREACION				:Febrero 2006
//FECHA ULTIMA ACTUALIZACION 	:07 Febrero de 2006
//DESCRIPCION					:


//VARIABLES:
/* $fecha: Indica la fecha en que se desea realizar un reporte

*/
//==================================================================================================================================

/****************************Funciones************************************************/


/****************************PROGRAMA************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{

	//Abrir conexiones
	

	

	$bd='facturacion';
	include_once("/Magenta/socket2.php");
	//entro a programa cuando la conexión se realizó
	if($conex_o != false)
	{
		include_once("/Magenta/apoyolargo.php");
		

		

//////////////forma para ingresar la fecha de búsqueda////////////////////

		if (!isset ($ano))
		
		{
			ECHO "<table border=0 align=center size=100%>";
	 		ECHO "<tr><td align=center ><A HREF='portalMagenta.php'><font size=10 color='#FF00FF'>LISTA DE VISITAS</font></a></td></tr>";
			ECHO "<tr><td align=center ><A NAME='Arriba'><font size=2 color='#FF00FF'> visitasAnt.php Ver. 07/02/2006</font></a></td></tr>";
			ECHO "</table></br></br></br>";
			
			echo "<table align='center'>";
				echo "<tr><td align=center bgcolor='#ADD8E6'><font size=4>INGRESE POR FAVOR EL RANGO DE FECHAS EN EL CUAL DESEA REALIZAR LA BÚSQUEDA:</font></td>";
			echo "</table></br></br>";

			echo "<fieldset style='border:solid;border-color:#ADD8E6; width=500' align=center></br>";
			
				echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
					echo "<table align='center'>";
						echo "<tr><td align=center bgcolor='#ADD8E6' width='100'><font size=3  face='arial'>Fecha inicial:&nbsp</td>";
						echo "<td align=center bgcolor='#cccccc'><font size=2  face='arial'>Año:</b>&nbsp</td>";
						echo "<td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='text' name='ano'size='2'></td>";
						echo "<td align=center bgcolor='#cccccc'><font size=2  face='arial'>Mes:</b>&nbsp</td>";
						echo "<td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='text' name='mes'size='1'></td>";
						echo "<td align=center bgcolor='#cccccc'><font size=2  face='arial'>Día:</b>&nbsp</td>";
						echo "<td align=center bgcolor='#cccccc'><font size='2'  align=center face='arial'><input type='text' name='dia'size='1'</td></tr>";
			
					echo "</TABLE></br></br>";						
					echo "<table align='center'>";
						echo "<tr><td align=center bgcolor='#ADD8E6'  width='100'><font size=3  face='arial'>Fecha final:&nbsp</td>";
						echo "<td align=center bgcolor='#cccccc'><font size=2  face='arial'>Año:</b>&nbsp</td>";
						echo "<td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='text' name='ano2' size='2'></td>";
						echo "<td align=center bgcolor='#cccccc'><font size=2  face='arial'>Mes:</b>&nbsp</td>";
						echo "<td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='text' name='mes2'size='1'></td>";
						echo "<td align=center bgcolor='#cccccc'><font size=2  face='arial'>Día:</b>&nbsp</td>";
						echo "<td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='text' name='dia2'size='1'></td></tr>";
					echo "</TABLE></br></br>";
			
				echo "<table align='center'>";
						echo "<tr><td align=center colspan=2><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='BUSCAR' ></td></tr>";
				echo "</TABLE>";		
			echo "</form>";
			echo "</fieldset>";				
		
		}else
		{
			

/////////////////////////////////Validación de campos de fecha////////////////////
	
			if ((strlen ($ano) != 4) or (strlen ($mes)!=2) or (strlen ($dia)!=2) or (strlen ($ano2)!= 4) or (strlen ($mes2) != 2) or (strlen ($dia2)!= 2))
			{
				echo '<script language="Javascript">';
				 	echo 'window.location.href=window.location.href;';
					echo 'alert ("El formato para ingresar las fechas es como el siguiente ejemplo: Año:2006  Mes:02  Día:25 ")';
				echo '</script>';
			}
//////////////////////////////////////////////////////////Busqueda principal en db////////////////////////////
			//Busco inicialmente en inpac quienes han venido hoy
			$i=0;
			$fecIni=$ano."/".$mes."/".$dia;
			$fecFin=$ano2."/".$mes2."/".$dia2;
			
			ECHO "<table border=0 align=center size=100%>";
	 		ECHO "<tr><td align=center ><A HREF='portalMagenta.php'><font size=6 color='#FF00FF'>LISTA DE VISITAS: desde $fecIni hasta $fecFin</font></a></td></tr>";
			ECHO "<tr><td align=center ><A NAME='Arriba'><font size=2 color='#FF00FF'> visitasAnt.php Ver. 07/02/2006</font></a></td></tr>";
			ECHO "</table></br></br></br>";
			
			
				$query="select A.pachis, A.pacnum, A.pachor, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, A.pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom  ";
				$query= $query."from inpac A , inpacotr B, Outer inser C ";
				$query= $query."where A.pacfec between'$fecIni' and '$fecFin' and B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
				$query= $query."and C.sercod=A.pacser ";

		
				$err_o = odbc_exec($conex_o,$query);
	
		
				while (odbc_fetch_row ($err_o))
				{
	
				
					$his [$i]= odbc_result($err_o,1);
					$numIng [$i]= odbc_result($err_o,2);
					$ced [$i]= odbc_result($err_o,4);
					$cedTip [$i]= odbc_result($err_o,5);
					$nom [$i]= odbc_result($err_o,7);
					$ape1 [$i]= odbc_result($err_o,8);
					$ape2 [$i]= odbc_result($err_o,9);
					$fecIng [$i]= odbc_result($err_o,10);
					$fecNac [$i]= odbc_result($err_o,11);
					$sex[$i]=odbc_result($err_o,12);-
					$codRes [$i]= odbc_result($err_o,13).'-';
					$bar [$i]= odbc_result($err_o,14);
					$serNom [$i]= odbc_result($err_o,16);
					$horIng [$i]= odbc_result($err_o,3);
					$serAct[$i]= odbc_result($err_o,3);
					$amb[$i]='';
					$fecEgr [$i]= 'Aún ingresado';
					$horEgr [$i]='';
					$fuente [$i]='inpac';
					$i++;
				}
		
				
			
			//búsco en aymov
			$query="select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom   ";
			$query= $query."from aymov A , aymovotr B, Outer inser C  ";
			$query= $query."where movfec between'$fecIni' and '$fecFin' and movtip <> 'I' and movfue <> 'PO' ";
			$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue ";
			$query= $query."and C.sercod=A.movsin ";
		
			$err_o = odbc_exec($conex_o,$query);
		
		
			while (odbc_fetch_row ($err_o))
			{
	
				
					$his [$i]= odbc_result($err_o,1);
					$numIng [$i]= odbc_result($err_o,2);
					$ced [$i]= odbc_result($err_o,5);
					$cedTip [$i]= odbc_result($err_o,6);
					$nom [$i]= odbc_result($err_o,8);
					$ape1 [$i]= odbc_result($err_o,9);
					$ape2 [$i]= odbc_result($err_o,10);
					$fecIng [$i]= odbc_result($err_o,11);
					$fecNac [$i]= odbc_result($err_o,12);
					$sex[$i]=odbc_result($err_o,13);
					$codRes [$i]= odbc_result($err_o,14).'-';
					$bar [$i]= odbc_result($err_o,15);
					$serNom [$i]= odbc_result($err_o,17);
					$horIng [$i]= odbc_result($err_o,3);
					$fecEgr [$i]= $fecIng [$i];
					$horEgr [$i]='';
					$amb[$i]='AMBULATORIO';
					$fuente [$i]='aymov';
					$i++;
				
			}
			echo $i;

			//búsqueda en inpaci
		
			$query="select D.egrhis, D.egrnum, D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, A.pacced, A.pactid, A.pacnom, A.pacap1, A.pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom  ";
			$query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D ";
			$query= $query."WHERE D.egring between'$fecIni' and '$fecFin' ";
			$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
			$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
			$query= $query."and C.sercod=D.egrsin ";
	
		
		
			$err_o = odbc_exec($conex_o,$query);
		
			while (odbc_fetch_row ($err_o))
			{
	
				
				$his [$i]= odbc_result($err_o,1);
				$numIng [$i]= odbc_result($err_o,2);
				$ced [$i]= odbc_result($err_o,10);
				$cedTip [$i]= odbc_result($err_o,11);
				$nom [$i]= odbc_result($err_o,12);
				$ape1 [$i]= odbc_result($err_o,13);
				$ape2 [$i]= odbc_result($err_o,14);
				$fecNac [$i]= odbc_result($err_o,15);
				$sex[$i]=odbc_result($err_o,16);
				$codRes [$i]= odbc_result($err_o,3).'-a';
				$fecIng [$i]= odbc_result($err_o,5);
				$fecEgr [$i]= odbc_result($err_o,8);
				$horIng [$i]= odbc_result($err_o,6);
				$horEgr [$i]= odbc_result($err_o,7);
				$bar [$i]= odbc_result($err_o,17).'-a';
				$serNom [$i]= odbc_result($err_o,18);
				$amb[$i]='AMBULATORIO';
				$fuente [$i]='inpaci';
				$i++;
			}
			$ingresos= $i;
			echo $ingresos;
	
//////////////////////////////////////////////////////////Fin Busqueda principal en db////////////////////////////			

//////////////////////////////////////////////////////////selección de clientes AAA////////////////////////////		
		
			if ($i==0)
			{
				echo '<script language="Javascript">';
				 	echo 'window.location.href=window.location.href;';
					echo 'alert ("No se encontró información en el rango de fechas ingresado, intente nuevamente por favor ")';
				echo '</script>';
			}
		
			$k=0;
			echo "<center><img SRC='\MATRIX\images\medical\Magenta\\AAA.gif' ><center></br></br>";
			
		
			for ($j=0; $j<$ingresos; $j++)
			{
				$zona=Zona($bar[$j]);
				$tipUsu= esAAA ($ced[$j], $cedTip[$j]);

				If ($tipUsu==1)
				{
					$tipUsu='AFIN-AAA-1';
				}else
				{
					$frecVis= calcularFec ($fuente[$j], $ced [$j], $cedTip [$j], $his [$j]);
					$tipUsu=definirTipUsu ($frecVis, $sex[$j], $fecNac[$j], $zona, $codRes[$j], $ced[$j], $cedTip[$j]);
				}
				
				
				if (strcmp ($tipUsu,'AFIN-AAA-1')==0)
				{
					
					$array[$k][0]=$ced[$j].$cedTip[$j];
					$array[$k][1]=$his [$j];	
					$array[$k][2]=$nom [$j].$ape1 [$j].$ape2 [$j];
					$array[$k][3]=$fecIng [$j];
					$array[$k][4]=$serNom [$j];
					$array[$k][5]= $horIng [$j];
					$array[$k][6]=$fecEgr [$j];
					$array[$k][7]=$sex[$j];
					$array[$k][8]=$fecNac [$j];
					$array[$k][9]=$numIng [$j];
					$array[$k][10]=$codRes [$j];
					$array[$k][11]=$horEgr[$j];
					$array[$k][12]=$bar [$j];		
					$array[$k][13]=$ced [$j];		
					$array[$k][14]=$cedTip[$j];
				
					$k++;
				}	
			}
			
			
			if ($k !=0)
			{
				$m=$k-1;
				$array=ordenador (3, $array, $m, 14);
				$array=ordenador2  (5, 3, $array, $m, 14);
				$array=verAct ($array, 14);
				$size1=count($array);
				
				ECHO "<table border=0 align=center size=100%>";
	 			ECHO "<tr><td align=center ><font size=5 color='#FF00FF'>Número de Visitas AAA: $size1</font></td></tr>";
				ECHO "</table></br>";
				
		
				ECHO '<TABLE border=1 align=center>';
						ECHO "<Tr >";
						
						echo "<td  bgcolor='#ADD8E6' align=center>Documento</td>";
						echo "<td  bgcolor='#ADD8E6'align=center>Nº Historia</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Nombre</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Fecha de ingreso</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Unidad de ingreso</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Hora de Ingreso</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Fecha de Egreso</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Actualizado</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Datos completos</td>";
						
						ECHO "</Tr >";
		
		
						pintarTabla ($array, 7);
				echo "</table>";
			}ELSE
			{	echo "<table>";
				echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' >";
				echo "<tr><td colspan='2'><font size=3  face='arial'><b>NINGUNO HA INGRESO DURANTES LAS FECHAS INGRESADAS</td><tr>";
				echo "</table></fieldset>";	
			}
			
//////////////////////////////////////////////////////////Fin selección de clientes AAA////////////////////////////	
//////////////////////////////////////////////////////////selección de clientes BBB////////////////////////////		
		
		
			$k=0;
			echo "</br></br></br></br><center><img SRC='\MATRIX\images\medical\Magenta\\BBB.gif' ><center></br></br>";
			
		
			for ($j=0; $j<$ingresos; $j++)
			{

				$tipUsu=esBBB ($ced[$j], $cedTip[$j]);
			
				if ($tipUsu==1)
				{
				
	   					
					
					
					$array2[$k][0]=$ced [$j].$cedTip [$j];
					$array2[$k][1]=$his [$j];	
					$array2[$k][2]=$nom [$j].$ape1 [$j].$ape2 [$j];
					$array2[$k][3]=$fecIng [$j];
					$array2[$k][4]=$serNom [$j];
					$array2[$k][5]= $horIng [$j];
					$array2[$k][6]=$fecEgr [$j];
					$array2[$k][7]=$sex[$j];
					$array2[$k][8]=$fecNac [$j];
					$array2[$k][9]=$numIng [$j];
					$array2[$k][10]=$codRes [$j];
					$array2[$k][11]=$horEgr [$j];
					$array2[$k][12]=$bar [$j];		
					$array2[$k][13]=$ced [$j];		
					$array2[$k][14]=$cedTip[$j];		
					$k++;
	
				}	
		
			}
		
			
			if ($k !=0)
			{
				$m=$k-1;
				$array2=ordenador (3, $array2, $m, 14);
				$array2=ordenador2  (5, 3, $array2, $m, 14);
				$array2=verAct ($array2, 14);
				
				$size1=count($array2);
				
				ECHO "<table border=0 align=center size=100%>";
	 			ECHO "<tr><td align=center ><font size=5 color='#FF00FF'>Número de Visitas BBB: $size1</font></td></tr>";
				ECHO "</table></br>";
				
		
				ECHO '<TABLE border=1 align=center>';
						ECHO "<Tr >";
						
						echo "<td  bgcolor='#ADD8E6'align=center>Documento</td>";
						echo "<td  bgcolor='#ADD8E6'align=center>Nº Historia</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Nombre</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Fecha de ingreso</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Unidad de ingreso</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Hora de Ingreso</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Fecha de Egreso</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Actualizado</td>";
						echo "<td bgcolor='#ADD8E6'align=center>Datos completos</td>";
						
						ECHO "</Tr >";
						pintarTabla ($array2, 7);
				echo "</table>";
			}ELSE
			{
				//echo "<table>";
				echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' >";
				echo "<tr><td colspan='2'><font size=3  face='arial'><b>NINGUNO HA INGRESO DURANTES LAS FECHAS INGRESADAS</td><tr>";
				echo "</fieldset>";	
			}
			
//////////////////////////////////////////////////////////Fin selección de clientes BBB////////////////////////////	
		}	
	}else
	{
		echo "ERROR : "."$errstr ($errno)<br>\n";
	}
	

	
	//Cerrar conexiones
	include_once("free.php");
}
?>