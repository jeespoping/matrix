<html>
<head>
  <title>Reporte de Articulos Aplicados X Paciente</title>
</head>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.rep_artaplixpac.submit();
	}
	
	function cerrarVentana()
	{
		window.close()		  
    }
	
	function valida_campo()
	{
		if(document.getElementById("whis2").value == "") 		
			alert ("Ingrese el número de la historia");
	}	
	
	 
</script>


<?php
include_once("conex.php");

/**
* REPORTE DE ARTICULOS APLICADOS X PACIENTE	                                                   *
*/
// ===========================================================================================================================================
// PROGRAMA				      :Reporte para saber los articulos aplicados por paciente.                                                      |
// AUTOR				      :Ing. Gustavo Alberto Avendano Rivera.                                                                         |
// FECHA CREACION			  :Octubre 3 DE 2007.                                                                                            |
// FECHA ULTIMA ACTUALIZACION :06 de Diciembre de 2011                                                                                       |
// DESCRIPCION			      :Este reporte sirve para ver por centro de costos-habitacio y paciente que saldo tiene pendiente de aplicar.   |
//                                                                                                                                           |
// TABLAS UTILIZADAS :                                                                                                                       |
// root_000050       : Tabla de Empresas para escojer empresa y esta traer un campo para saber que centros de costos escojer.                |
// costosyp_000005   : Tabla de Centros de costos de Clinica las Americas, Laboratorio de Patologia, y Laboratorio Medico.                   |
// clisur_000003     : Tabla de Centros de costos de clinica del sur.                                                                        |
// farstore_000003   : Tabla de Centros de costos de farmastore.                                                                             |
// root_000041       : Tabla de Tipos de requerimientos.                                                                                     |
// root_000042       : Tabla de Responsables por centro de costos.                                                                           |
// usuarios          : Tabla de Usuarios con su codigo y descripcion.                                                                        |
// root_000040       : Tabla de Requerimientos.                                                                                              |
// root_000043       : Tabla de Clases.                                                                                                      |
// root_000049       : Tabla de Estados.    
//
// ==========================================================================================================================================

// ==========================================================================================================================================
// M O D I F I C A C I O N E S 	 
// ==========================================================================================================================================
// Diciembre 23 de 2020: Edwin MG
// Se hacen modificaciones varias para servicio domiciliario
// ===========================================================================================================================================
// Julio 12 de 2018: Juan Felipe Balcero
// Se agrega la opción de generar la hoja filtrando por el tipo de medicamento requerido, ya sean todos, sólo quimioterapia, nutricion parenteral, 
// no esteril o dosis adaptada.
// ===========================================================================================================================================
// Febrero 6 de 2018: Jonatan
// Se cambia el titulo MEDICAMENTOS APLICADOS X PACIENTE por ADMINISTRACION DE MEDICAMENTOS(HOJA DE MEDICAMENTOS).
// ==========================================================================================================================================
// Julio 19 de 2017 :  Felipe Alvarez :
// Se hacen pequeños cambios para que el programa tenga un filtro adicional, (externo e interno) si es escogido el interno , se traen cuatro columnas mas
// si se escoge externo muestra la tabla de detalle de medicamento  mas resumida. Ademas de esto se trae mas informacion del paciente (habitacion, nombre completo)
// ==========================================================================================================================================

// ==========================================================================================================================================
// Diciembre 7 de 2011 :   Ing. Luis Haroldo Zapata Arismendy
// ==========================================================================================================================================
// Se agrega el campo frecuencia con el fin de que el usuario tenga conocimiento de la periodicidad 
// de los medicamentos.
// ==========================================================================================================================================
// Diciembre 12 de 2011 :   Ing. Santiago Rivera Botero
// ==========================================================================================================================================
// Se adiciona un if que va desde la linea 359 hasta 362 para preguntar si el formulario fue llamado o no desde su origen 
// Se adiciona un if que va desde la linea 370 hasta 373 para preguntar si el formulario fue llamado o no desde su origen para imprimir el boton cerrar                                                                                       
// Febrero 06 de 2012 : 
// ==========================================================================================================================================
// Se Adicionan un reporte de los pacientes activos en cama para consultar los medicamentos con la historia e ingreso
// Se elimina los campos Ingreso y Medicamento con el fin que el usuario busque los ingresos del paciente y despues los medicamentos respectivos al ingreso
// Febrero 07 de 2012 : 
// ==========================================================================================================================================
// Se ordenan el listado de pacientes por habitación cuando el usuario selecciona un centro de costos
// Se filtra el query que reporta los insumos del paciente para que solo presente los medicamentos aplicados al paciente (tablas de movhos: la 15,26 y 29 tablas de cenpro: 01 y 02)
//================================================================================================================================================
//Junio 20 de 2012
//se agregan las funciones consultaCentroCostos y dibujarSelect la cual lista los centros de costos de un grupo seleccionado en orden alfabetico y la otra dibuja el select con los centros de costos. Viviana Rodas
//================================================================================================================================================


// Función que consulta el nombre del medicamento en las tablas movhos_000026 o cenpro_000002 
function ConsultaNomMedica($codigo)
{
	global $conex;
	global $wbasedato;
	global $wcenmez;
	
	$q= "SELECT Artcom
		   FROM ".$wbasedato."_000026
		  WHERE Artcod = '".$codigo."'";
		  
	$res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query: $q -".mysql_error() );
	$num = mysql_num_rows($res);
	
	if($num >0)
	{
		$row = mysql_fetch_array($res);
		return $row["Artcom"];
	}
	else
	{
		$q= "SELECT Artcom
			   FROM ".$wcenmez."_000002
		      WHERE Artcod = '".$codigo."'";
	
		$res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query: $q -".mysql_error() );
		$row = mysql_fetch_array($res);
		return $row["Artcom"];
	}
}

session_start();
if (!isset($_SESSION['user']))
{
    echo "error";
} 
else
{
    

	include_once("root/comun.php");
    

    
	$wactualiz="2018-07-12";
	$entro='no';
	if($tipoarticulo=='Ambos' or $tipoarticulo=='' )
	{
		 encabezado("DISPOSITIVOS Y MEDICAMENTOS APLICADOS X PACIENTE ",$wactualiz, "clinica");
		 $entro='si';
	}
	if($tipoarticulo=='Dispositivos Medicos')
	{
		 encabezado("DISPOSITIVOS APLICADOS X PACIENTE ",$wactualiz, "clinica");
		  $entro='si';
	}
	if($tipoarticulo=='Medicamentos')
	{
		 encabezado("ADMINISTRACION DE MEDICAMENTOS(HOJA DE MEDICAMENTOS)",$wactualiz, "clinica");
		  $entro='si';
	}
  
	if($entro=='no')
	{
		 encabezado("DISPOSITIVOS Y MEDICAMENTOS APLICADOS X PACIENTE ",$wactualiz, "clinica");
	}// encabezado("CONSULTA DE INSUMOS APLICADOS X PACIENTE ".$tipoarticulo."",$wactualiz, "clinica");
	
	//consultamos la base de datos de la empresa correspondiente
	 $q = " SELECT detapl, detval "
        ."    FROM root_000050, root_000051 "
        ."   WHERE empcod = '".$wemp."'"
        ."     AND empest = 'on' "
        ."     AND empcod = detemp ";
		
     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
     $num = mysql_num_rows($res); 
  
     if($num > 0 )
        {
	     for($i=1;$i<=$num;$i++)
	        {   
	         $row = mysql_fetch_array($res);
	      
	         if($row[0] == "cenmez")
	            $wcenmez=$row[1];
	         
	         if($row[0] == "afinidad")
	            $wafinidad=$row[1];
	         
	         if($row[0] == "movhos")
	            $wbasedato=$row[1];
			  
			 if(strtoupper($row[0]) == "HCE")
	            $whce=$row[1];
	         
	         if($row[0] == "tabcco")
	            $wtabcco=$row[1];
				
			 if($row[0] == "camilleros")
	            $wcencam=$row[1];
			 
            }  
        }
        else
		    { 
             echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
			 
	        }
	
	if (!isset($whis) and !isset($wing) and !isset($warticulo))
    {
		echo "<form name='rep_artaplixpac4' action='' method=post>";
		//Selecionamos los centros de costos hospitalarios
		//*********************llamado a las funciones que listan los centros de costos y la que dibuja el select************************
		$cco="Ccohos";
		$sub="on";
		$tod="";
		//$cco=" ";
		$ipod="off";
		$centrosCostos = consultaCentrosCostos($cco);
		echo "<table align='center' border=0 >";
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "wcco0");
		echo "<input type='hidden' name='wemp' value='".$wemp."'>";			
		echo $dib; 
		echo "</table>";
					
					
	//********************************fin llamado a funciones en comun.php				
		
		//buscamos las historias que se encuentran activos en cama en la tabla de movhos_000020	
		if(isset($wcco0) )
		//if(isset($wcco0) and $wcco0!="todos")
		{
			$wcco = explode("-",$wcco0);
			
			$tablaHabitaciones 	= consultarTablaHabitaciones( $conex, $wbasedato, $wcco[0] );
			
			$q ="SELECT A.Habcod,A.Habhis,A.Habing,Pacno1,Pacno2,Pacap1,Pacap2
				   FROM	".$tablaHabitaciones." A,".$wbasedato."_000018 B, root_000037 C, root_000036 D 
				  WHERE A.Habest = 'on'
				    AND A.Habhis = B.Ubihis 
					AND A.Habing = B.Ubiing
					AND A.Habhis = C.Orihis
					AND A.Habing = C.Oriing
					AND C.Oriori = '".$wemp."'
					AND A.Habcco = '".$wcco[0]."'
					AND B.Ubiald <> 'on'
					AND C.Oriced = D.Pacced
					AND C.Oritid = D.Pactid
				  ORDER BY A.Habord, A.Habcod ";  //se agrega el campo de orden
						
			$res = mysql_query($q, $conex);						
			$num = mysql_num_rows($res);
			
			if($num>0)
			{
				echo "<br/>";
				echo "<table border=0 align='center'>";
				echo "<tr class=encabezadoTabla>";
				echo "<td align=center >HABITACIÓN</td>";
				echo "<td align=center >HISTORIA</td>";
				echo "<td align=center>INGRESO</td>";
				echo "<td align=center>PACIENTE</td>";
				echo "<td align=center colspan='2'>TIPO DE MEDICAMENTO</td>";
				echo "</tr>";
				for($i=0;$i<$num;$i++)
				{
					if($i % 2 == 0)
						$wclass="fila1";
					else
						$wclass="fila2";
					
					$row = mysql_fetch_array($res);
					echo "<form name='rep_artaplixpac2' action='rep_artaplixpac.php' method=post>";
					echo "<tr>";
					echo "<td class=".$wclass." align=center>".$row["Habcod"]."</td>";
					echo "<td class=".$wclass." align=center>".$row["Habhis"]."</td>";
					echo "<td class=".$wclass." align=center>".$row["Habing"]."</td>";
					$wnombres = $row["Pacno1"]." ".$row["Pacno2"]." ".$row["Pacap1"]." ".$row["Pacap2"];
					$wespacio = " ";
					$wremplazo = "%20"; 
					$wnombres2 = str_replace($wespacio,$wremplazo,$wnombres);	
					echo "<td class=".$wclass." align=left>".$wnombres." </td>";
					echo "<td class=".$wclass." align=center><select name='wtipo'>";
					echo "<option value='%-Todos'>Todos</option>";
					echo "<option value='Q-Citostáticos y Coadyuvantes'>Citostáticos y Coadyuvantes</option>";
					echo "<option value='DA-Dosis Adaptada'>Dosis Adaptada</option>";
					echo "<option value='NU-Nutricion Parenteral'>Nutrición parenteral</option>";
					echo "<option value='NE-No Esteril'>No esteril</option>";
					echo "<option value='OT-Generales'>Generales</option>";
					echo "</select></td>";
					echo "<input type='hidden' name='wemp' value='".$wemp."'>";
                    echo "<input type='hidden' name='whis' value='".$row["Habhis"]."'>";
                    echo "<input type='hidden' name='wing' value='".$row["Habing"]."'>";
					echo "<input type='hidden' name='whab' value='".$row["Habcod"]."'>";
					echo "<input type='hidden' name='wnombres' value='".$wnombres."'>";
					echo "<input type='hidden' name='wcco0' value='".$wcco[0]."'>";
					echo "<td align=center class=".$wclass."><input type='submit' value='Ver'></td>";
					echo "</tr>";
					echo "</form>";
					
				}
				echo "</table>";	
			}
			else
			{
				echo "<br/>";
				echo "<center class='textoMedio'>NO HAY PACIENTES EN CAMA</center>";
			}
			echo "<br/>";
		}
		
		echo "<br/>";
		echo '<table align=center>';
		echo "<tr class=seccion1>";
		if(isset($whis2))
			$wvalor = $whis2;
		else 
			$wvalor = "";
		echo "</form>";
		echo "<form name='rep_artaplixpac' action='rep_artaplixpac.php' method=post>";
		echo "<td><b>Historia</b><br><center><INPUT TYPE='text' NAME='whis2' ID='whis2' value='".$wvalor."' SIZE=10></td></tr>";
		echo "<input type='hidden' name='wemp' value='".$wemp."'>";
		echo "<input type='hidden' name='wcco0' value='".$wcco0."'>";
		echo "</table>";
		echo "<input type='HIDDEN' name='wingreso' id='wingreso' value='1'>";
		
		//preguntamos si se inserto la historia para contultar todos los ingresos  
		if( isset($whis2) and $whis2!="" )
		{
			$q = "SELECT A.Ubihis,A.Ubiing,A.Fecha_data,Pacno1,Pacno2,Pacap1,Pacap2 
					FROM ".$wbasedato."_000018 A,root_000036 B,root_000037 C 
				   WHERE A.Ubihis = '".trim($whis2)."' 
				     AND A.Ubihis = C.Orihis
					 AND B.Pacced = C.Oriced 
					 AND B.Pactid = C.Oritid
				   GROUP BY A.Ubihis,A.Ubiing ";
			
			$res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query: $q -".mysql_error() );
			$num = mysql_num_rows($res);
			if ($num>0)
			{	
				echo "<table align=center>";
				echo "<tr class=encabezadoTabla>";
				echo "<td align=center> FECHA </td> ";
				echo "<td align=center> HISTORIA </td> ";
				echo "<td align=center> INGRESO </td> ";
				echo "<td align=center> PACIENTE </td> ";
				echo "<td align=center colspan='2'>TIPO DE MEDICAMENTO</td>";
				echo "</tr>";
				for($i=0;$i<$num;$i++)
				{
					if($i % 2 == 0)
						$wclass="fila1";
					else
						$wclass="fila2";
					
					$row = mysql_fetch_array($res);
					
					echo "<tr >";
					echo "<td class=".$wclass." align=center> ".$row["Fecha_data"]." </td> ";
					echo "<td class=".$wclass." align=center> ".$row["Ubihis"]." </td> ";
					echo "<td class=".$wclass." align=center> ".$row["Ubiing"]." </td> ";
					echo "<td class=".$wclass." align=left> " 	.$row["Pacno1"]." ".$row["Pacno2"]." ".$row["Pacap1"]." ".$row["Pacap2"]. " </td> ";
					echo "<form name='rep_artaplixpac3' action='rep_artaplixpac.php' method=post>";
					echo "<td class=".$wclass." align=center><select name='wtipo'>";
					echo "<option value='%-Todos'>Todos</option>";
					echo "<option value='Q-Citostáticos y Coadyuvantes'>Citostáticos y Coadyuvantes</option>";
					echo "<option value='DA-Dosis Adaptada'>Dosis Adaptada</option>";
					echo "<option value='NU-Nutricion Parenteral'>Nutrición parenteral</option>";
					echo "<option value='NE-No Esteril'>No esteril</option>";
					echo "<option value='OT-Generales'>Generales</option>";
					echo "</select></td>";
					echo "<input type='hidden' name='wemp' value='".$wemp."'>";
                    echo "<input type='hidden' name='whis' value='".$row["Ubihis"]."'>";
                    echo "<input type='hidden' name='wing' value='".$row["Ubiing"]."'>";
					echo "<td align=center class=".$wclass."><input type='submit' value='Ver'></td>";
					echo "</form>";
					echo "</tr>";
	
				}
				echo "</table>";
			}
			else
			{
				echo "<br/>";
				echo "<center class='textoMedio'>NO SE ENCONTRÓ HISTORIA</center>";
			}		
		}
		echo "<br/>";		
		echo "<center><input type='submit' value='Generar' onclick='valida_campo()'></center>";	
		echo "</form>";   
    }
	else if( isset($whis) and  isset($wing) and !isset($warticulo) )
	{
		if(!isset($wnombres))
		{
			$q1 = "SELECT Pacno1,Pacno2,Pacap1,Pacap2
					 FROM root_000036,root_000037
					WHERE Orihis = '".$whis."'
					  AND Oriori = '".$wemp."'	
					  AND Pactid = Oritid
					  AND Pacced = Oriced";
					  
			$res1 = mysql_query( $q1, $conex ) or die( mysql_errno()." - Error en el query: $q -".mysql_error() );
			$row1 = mysql_fetch_array($res1);
			$wnombres = $row1["Pacno1"]." ".$row1["Pacno2"]." ".$row1["Pacap1"]." ".$row1["Pacap2"];
		}
		//Se añade un filtro para el tipo de medicamento
		$wtipo1 = explode('-',$wtipo);
        $and_arktip = '';
		$and_tiptpr = '';
        
        switch ($wtipo1[0]) {
            case 'Q':
                $and_arktip = 'AND Arktip = "Q" ';
                $and_tiptpr = 'AND Tiptpr = "QT" ';
                break;
            case 'DA':
                $and_arktip = 'AND Arktip = "DA" ';
                $and_tiptpr = "AND Tiptpr IN ('DA','DD','DS') ";
                break;
            case 'NU':
                $and_arktip = 'AND Arktip = "NU" ';
                $and_tiptpr = "AND Tiptpr = 'NU' ";
                break;
            case 'NE':
                $and_arktip = 'AND Arktip = "NE" ';
                $and_tiptpr = "AND Tiptpr = 'NE' ";
                break;
            case 'OT':
                $and_arktip = "AND ( arktip IS NULL OR Arktip <> 'Q')";
                $and_tiptpr = "AND Tiptpr NOT IN ('NE','NU','QT','DA','DS','DD') ";
                break;
            default:
                # code...
                break;
		}
		
		//Buscamos los articulos que se le aplicaron al paciente y que sean medicamentos con la historia e ingreso en la tabla movhos_000015 		   
		$q = "CREATE TEMPORARY TABLE articulos
			 ( INDEX cod_idx( Historia(20),Ingreso(10) ) )
			 (  SELECT A.Aplart as Codigo ,A.Aplhis as Historia,A.Apling as Ingreso
				  FROM ".$wbasedato."_000015 A LEFT JOIN ".$wbasedato."_000068 ON A.Aplart = Arkcod, ".$wbasedato."_000026 B, ".$wbasedato."_000029 C".$wbasedato68." 
			     WHERE A.Aplhis = '".$whis."'
				   AND A.Apling = '".$wing."'	
				   AND A.Aplest <> 'off' 
				   AND A.Aplart = B.Artcod
				   $and_arktip 
				   AND mid( B.Artgru,1,instr( B.Artgru,'-')- 1) = C.Gjugru )
			  UNION
			 (  SELECT A.Aplart as Codigo ,A.Aplhis as Historia,A.Apling as Ingreso
				  FROM ".$wbasedato."_000015 A, ".$wcenmez."_000002 B, ".$wcenmez."_000001 C 
				 WHERE A.Aplhis = '".$whis."'
				   AND A.Apling = '".$wing."'
				   AND A.Aplest <> 'off'
				   AND A.Aplart = B.Artcod 
				   AND B.Arttip = C.Tipcod
				   $and_tiptpr 
				   AND C.Tipcdo = 'off' )";		   
				 		
		$res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query: $q -".mysql_error() );

		$q = "SELECT *
				FROM articulos 
			   GROUP BY Codigo";
			
		$res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query: $q -".mysql_error() );
		$num = mysql_num_rows($res);
		
		echo "<table align=center>";
		echo "<tr class=encabezadoTabla>";
		
		if(isset($whab))
			$whab1= "<b>HABITACIÓN : </b>".$whab;
		else
			$whab1= "";
		
		if($whab1!="")
		{
			echo "<td align=center>";
			echo $whab1;
			echo "</td>";
		}
		if($whab1!="")
			$wcolspan = 2;
		else
			$wcolspan = 0;
		echo "<td align=center>";
		echo "<b>HISTORIA : </b>".$whis;
		echo "</td>";
		echo "<td align='center'>";
		echo "<b>INGRESO : </b>".$wing;
		echo "</td></tr></table>";
		echo "<form action='rep_artaplixpac.php' name='rep_artaplixpac' method=post>";
		echo "<table align=center><tr class=encabezadoTabla><td align=center>";
		echo "<b>PACIENTE : </b>".$wnombres;
		echo "</td>";
		echo "<td align=center>";
		echo "<b>TIPO DE MEDICAMENTOS : </b><select name='wtipo' onchange='enter()'>";
		echo "<option value='".$wtipo."'checked>".$wtipo1[1]."</option>";
		echo "<option value='%-Todos'>Todos</option>";
		echo "<option value='Q-Citostáticos y Coadyuvantes'>Citostáticos y Coadyuvantes</option>";
		echo "<option value='DA-Dosis Adaptada'>Dosis Adaptada</option>";
		echo "<option value='NU-Nutricion Parenteral'>Nutrición parenteral</option>";
		echo "<option value='NE-No Esteril'>No esteril</option>";
		echo "<option value='OT-Generales'>Generales</option>";
		echo "</select>";
		echo "</td>";
		echo "<input type='hidden' name='wemp' value='".$wemp."'>";
		echo "<input type='hidden' name='whis' value='".$whis."'>";
		echo "<input type='hidden' name='wing' value='".$wing."'>";
		echo "<input type='hidden' name='wnombres' value='".$wnombres."'>";
		echo "<input type='hidden' name='whab' value='".$whab."'>";
		echo "<input type='hidden' name='wcco0' value='".$wcco0."'>";
		echo "</tr>";
		echo "</table>";
		echo "</form>";
		//echo "<br>";
		echo "<table align=center >";
		
		if($num>0)
		{
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center>CÓDIGO</td>";
			echo "<td align=center >MEDICAMENTO</td>";
			echo "<td > </td>";
			echo "</tr>";
		
			for($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$wclass="fila1";
				else
					$wclass="fila2";
				 
				$row = mysql_fetch_array($res);
				echo "<tr>";
				echo "<td class=".$wclass." align=center>".$row["Codigo"]."</td>";
				$medicamento = ConsultaNomMedica($row["Codigo"]);
				echo "<td class=".$wclass.">".$medicamento."</td>";
				
				if(isset($whab))
					$path = "rep_artaplixpac.php?wemp=".$wemp."&whis=".$whis."&wing=".$wing."&warticulo=".$row["Codigo"]."&whab=".$whab."&wcco0=".$wcco0."&wtipo=".$wtipo;
				else
					$path = "rep_artaplixpac.php?wemp=".$wemp."&whis=".$whis."&wing=".$wing."&warticulo=".$row["Codigo"]."&wtipo=".$wtipo;
				echo "<td class=".$wclass." align=center ><A href=".$path."><b>Ver</b></A> </td>";
				echo "<tr/>";
			}
			
		}
		else
		{	
			echo "<tr><td align=center class=textoMedio colspan = '3'>NO HAY MEDICAMENTOS APLICADOS PARA ESTE PACIENTE</td></tr>";
		}
		if(isset($whab))
			echo "<tr><td align=center colspan=9><A href='rep_artaplixpac.php?wemp=".$wemp."&wcco0=".$wcco0."' id='searchsubmit'> Retornar</A></td></tr>";
		else
			echo "<tr><td align=center colspan=9><A href='rep_artaplixpac.php?wemp=".$wemp."&whis2=".$whis	."' id='searchsubmit'> Retornar</A></td></tr>";
		echo "</table>";
	}
    else if( isset($whis) and isset($wing) and isset($warticulo) )
    {
		echo '<div id="page" align="center">';
        echo '<div id="feature" class="box-orange" align="center">';
           



		if(isset($wpaciente))
		{
			echo "<table><tr><td class='fila1'>Historia:</td><td class='fila2'>".$whis."-".$wing."</td><td class='fila1'>Servicio:</td><td class='fila2'>".$nomservicio."</td><td class='fila1'>Cama:</td><td class='fila2'>".$whabitacion."</td></tr>
				  <tr><td class='fila1'>Paciente:</td><td class='fila2'>".$wpaciente."</td><td class='fila1'>Fecha Ingreso:</td><td class='fila2'>".$wfinal."</td><td class='fila1' >Fecha Egreso:</td><td class='fila2'>".$wfegreso."</td></tr>
				  </table>";
		}
		
        //Aca busco si el codigo es de un proveedor y traigo el codigo propio
        $q= " SELECT artcod "
           ."   FROM ".$wbasedato."_000009 "
           ."  WHERE artcba = '".$warticulo."'";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);  
        if($num > 0)
        {
			$row = mysql_fetch_array($res);
            $warticulo=$row[0];
        } 
        
		/*
        $q = " SELECT aplfec, aplron, aplcco, aplcan, aplusu, aplapl "
           . "   FROM ".$wbasedato."_000018,".$wbasedato."_000015,".$wbasedato."_000020 "
           . "  WHERE habcod = '".$whab."'"
           . "    AND ubihis = habhis "
           . "    AND ubiing = habing " 
           . "    AND ubihis = aplhis "
           . "    AND ubiing = apling "
           . "    AND aplart = '".$warticulo."'"
		   . "    AND ubihis = '".$whis."'"
		   . "    AND ubiing = '".$wing."'"
           . "    AND aplest = 'on' "
           . "  ORDER BY 1 desc, 2 desc, 3, 4 ";
		*/
		$q = " SELECT aplfec, aplron, aplcco, aplcan, aplusu, aplapl "
           . "   FROM ".$wbasedato."_000018,".$wbasedato."_000015 "
           . "  WHERE ubihis = aplhis "
           . "    AND ubiing = apling "
           . "    AND aplart = '".$warticulo."'"
		   . "    AND ubihis = '".$whis."'"
		   . "    AND ubiing = '".$wing."'"
           . "    AND aplest = 'on' "
           . "  ORDER BY 1 desc, 2 desc, 3, 4 ";
        $res = mysql_query($q, $conex) or die("ERROR EN QUERY");
        $wnum = mysql_num_rows($res); 
        echo '<table align=center>';

		/*
        $q = " SELECT habhis, habing "
            ."   FROM ".$wbasedato."_000020 "
            ."  WHERE habcod = '".$whab."'"
            ."    AND habest = 'on' ";
        $reshis = mysql_query($q, $conex) or die("ERROR EN QUERY");
		$rowhis = mysql_fetch_array($reshis);    
		
		$whis=$rowhis[0];
		$wing=$rowhis[1];
        */
		
        //TRAIGO EL NOMBRE DEL PACIENTE
        //==============================================================================
        $q = " SELECT pacno1, pacno2, pacap1, pacap2, orihis, oriing "
            ."   FROM root_000036, root_000037 "
            ."  WHERE oriori = '".$wemp."'"
            ."    AND orihis = '".$whis."'"
           // ."    AND oriing = '".$wing."'"
            ."    AND oriced = pacced ";
        $respac = mysql_query($q, $conex) or die("ERROR EN QUERY");
		$rowpac = mysql_fetch_array($respac);
        $pacie = $rowpac[0]." ".$rowpac[1]." ".$rowpac[2]." ".$rowpac[3];
        //==============================================================================

       // echo "<tr class=encabezadoTabla>";
        //echo "<th align=center colspan=1><b>HAB: ".$whab."</b></th>";
        //echo "<th align=center colspan=2><b>HISTORIA : ".$whis." - ".$wing."</b></th>";
        //echo "<th align=center colspan=8><b>PACIENTE : ".$pacie."</b></th>";
        //echo "</tr>";
        //echo "<br>";
        
        //ACA TRAIGO EL NOMBRE DEL ARTICULO, BUSCO 1RO EN MOVHOS Y LUEGO EN CENMEZ
        //==============================================================================
        $q = " SELECT artcom "
            ."   FROM ".$wbasedato."_000026 "
            ."  WHERE artcod = '".$warticulo."'";
        $resart = mysql_query($q, $conex) or die("ERROR EN QUERY");
        $wnumart = mysql_num_rows($resart); 
        if($wnumart > 0)
        {
	        $rowart = mysql_fetch_array($resart);   
            $wnomart = $rowart[0];
        }
        else
        {
			$q = " SELECT artcom "
				."   FROM ".$wcenmez."_000002 "
				."  WHERE artcod = '".$warticulo."'";
			$resart = mysql_query($q, $conex) or die("ERROR EN QUERY");
			$wnumart = mysql_num_rows($resart); 
			if($wnumart > 0)
			{
				$rowart = mysql_fetch_array($resart);
				$wnomart=$rowart[0];
			}
		}         
        //==============================================================================
        
		echo "<br>";
		     
        echo "<tr class=seccion1>";
        echo "<td align=center colspan=9><font size=4><b>ARTICULO: ".$warticulo." : ".$wnomart."</b></font></td>";
        echo "</tr>";
        
        echo "<br>";
        
		if($externointerno=='Ext')
		{
			$propiedad ='Style="display:none "';
		}
		else
		{
			$propiedad ='';
		}
		
		
        echo "<tr class=encabezadoTabla>";
        echo "<th align=center><b>FECHA</b></th>";
        echo "<th align=center><b>HORA</b></th>";
        echo "<th align=center><b>C.COSTO</b></th>";
        echo "<th align=center><b>CANTIDAD <BR> APLICADA</b></th>";
		echo "<th align=center><b>FRECUENCIA</b></th>";
        echo "<th ".$propiedad." >&nbsp</th>";
        echo "<th ".$propiedad." align=center><b>USUARIO QUE REGISTRO</b></th>";
        echo "<th ".$propiedad." >&nbsp</th>";
        echo "<th ".$propiedad." align=center><b>USUARIO QUE APLICO AL PACIENTE</b></th>";
		
        echo "</tr>";
        
        $wtot=0;
        for($i=1;$i<=$wnum;$i++)
        {
			if(is_int ($i / 2))
			{
				$wclass = "fila1";
			} 
			else
			{
				$wclass = "fila2"; 
			} 

			$row = mysql_fetch_array($res);
            
			echo "<tr class=".$wclass.">";
			echo "<td align=center>".$row[0]."</td>";                                  //Fecha
			echo "<td align=right>".$row[1]."</td>";                                   //Hora
			echo "<td align=center>".$row[2]."</td>";                                  //Ccosto
			echo "<td align=center>".number_format($row[3],2,'.',',')."</td>";  //Cantidad
			
			//=========================================================
			//Buscar la frecuencia en que se aplica el medicamento
			$q= " SELECT perequ "
			   ."   FROM ".$wbasedato."_000054,".$wbasedato."_000043 "
			   ."  WHERE ".$wbasedato."_000054.kadper=".$wbasedato."_000043.percod "
			   ."	  AND ".$wbasedato."_000054.kadhis='$whis' "
			   ."	  AND ".$wbasedato."_000054.kading='$wing' "
			   ."	  AND ".$wbasedato."_000054.kadart='$warticulo' "
			   ."	  AND ".$wbasedato."_000054.kadfec='".$row['aplfec']."'"; 
			$resfre = mysql_query($q, $conex) or die("ERROR EN QUERY $q");
            $rowfre= mysql_num_rows($resfre);
			 
			if($rowfre==0)
			{
				echo "<td align=center> </td>";
			}
			else
			{
				$rowfre = mysql_fetch_array($resfre);
				echo "<td align=center>Cada ".$rowfre['perequ']." horas</td>";			//Frecuencia
			}
             
            //=========================================================
            $q=" SELECT descripcion "
              ."   FROM usuarios "
              ."  WHERE codigo = '".$row[4]."'";
            $resusu = mysql_query($q, $conex) or die("ERROR EN QUERY");
            $rowusu = mysql_fetch_array($resusu);
             
            echo "<td ".$propiedad." >&nbsp</td>";  
            echo "<td align=left ".$propiedad.">".$row[4]." ".$rowusu[0]."</td>";                     //Registro
            //=========================================================
             
            //=========================================================
            $q=" SELECT descripcion "
              ."   FROM usuarios "
              ."  WHERE codigo = '".$row[5]."'";
            $resusu = mysql_query($q, $conex) or die("ERROR EN QUERY");
            $rowusu = mysql_fetch_array($resusu);
             
            echo "<td ".$propiedad." >&nbsp</td>";
            echo "<td align=left ".$propiedad.">".$row[5]." ".$rowusu[0]."</td>";                     //Aplico
            echo "</tr>";
            $wtot=$wtot+$row[3];
        } // fin del for
           
        echo "<tr class=encabezadoTabla>";
        echo "<th colspan=3>Total </th>";
        echo "<th colspan=1 align=center><b>".number_format($wtot,2,'.',',')."</b></th>";
        echo "<th colspan=5>&nbsp</th>";
        echo "</tr>";
	
		if(isset($ret))
			echo "<tr><td align=center colspan=9><A href='Hoja_medicamentos_auditores.php?wemp_pmla=".$wemp."&whis=".$whis."&wing=".$wing."&wfil=".$wfil ."&externointerno=".$externointerno."&wtipo=".$wtipo."' id='searchsubmit'> Retornar</A></font></td></tr>";
        else
		{
			if(isset($whab))
				echo "<tr><td align=center colspan=9><A href='rep_artaplixpac.php?wemp=".$wemp."&whis=".$whis."&wing=".$wing."&whab=".$whab."&wcco0=".$wcco0."&wfil=".$wfil ."&wtipo=".$wtipo."' id='searchsubmit'> Retornar </A></font></td></tr>";
			else
				echo "<tr><td align=center colspan=9><A href='rep_artaplixpac.php?wemp=".$wemp."&whis=".$whis."&whis2=".$whis."&wing=".$wing."&wfil=".$wfil ."&wtipo=".$wtipo."' id='searchsubmit'> Retornar </A></font></td></tr>";			                         
		}	 
    } // cierre del else donde empieza la impresión
        
    echo "</table>"; // cierra la tabla o cuadricula de la impresión
    echo "<center><table>"; 
	if(!isset($ret))
		echo "<tr ><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
	echo "</table></center>";	
} 

?>
