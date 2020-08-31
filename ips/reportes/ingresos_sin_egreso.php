<html>
	<head>
		<meta http-equiv="content-Type" content="text/html; charset=iso-8859-1">
		<title>MATRIX</title>
	</head>
	<body>
		<label>
			<div align="right">Creado por: Juan Esteban Lopez A
			</div>
		</label>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
<?php
include_once("conex.php");
include_once("root/comun.php");

if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;
		
	echo"<div align=Center>";
	echo"<input type=image name=imageField src='/matrix/images/medical/pos/logo_".$wbasedato.".png'/>";		
	echo"</div>";
	echo"<p>&nbsp;</p>";
	echo"</body>";
	


/********************************************************
*     	Reporte de ingresos de pacientes				*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte de ingresos de pacientes	 
//AUTOR							:Juan Esteban Lopez Aguirre
//FECHA CREACION				:Septiembre de 2007
//FECHA ULTIMA ACTUALIZACION 	:Octubre 30 de 2007
//DESCRIPCION					:En el reporte se lista todos los ingresos ingresos realizxados en una fecha especifica o en unrango segun sea la
// 								 necesidad del usuario 
//MODIFICACIONES:
//=================================================================================================================================

session_start();
if(!isset($_SESSION['user']))
{
	echo "error";
}
else
{
	

	$Dia = date("d")-1; //Contiene el Dia  
	$Mes = date("m"); //Contiene el Mes
	$Anio = date("Y"); //Contiene el Año
	$i=0;
	
	if ($Dia=="0")
	{
		$Dia=30;
		
	}

	//Codigo para pintar la ingerfaz  con la cual el usuario va a trabajar

	echo "<form name=Ingreso_egresos method=POST action=ingresos_sin_egreso.php>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<table width=736 border=1 align=center bgcolor=#CCCCCC>";
	echo "<tr align=left>";
	echo "<th>Fecha Ingresos : </th>";
	
	if(isset($rbtnConsulta) and $rbtnConsulta=="1")
	{
	    echo "<th align=center>Fecha Inicial :  <input TYPEAPPLICATION=text name=txtFeIni maxlength=10 size=8 value='".$Anio."-".$Mes."-".$Dia."'>  	   Fecha Final :<input TYPEAPPLICATION=text name=txtFeFin maxlength=10 size=8 value='".$Anio."-".$Mes."-".$Dia."'></th>";
							
	}
	else
	{
		echo "<th align=center>Dia :<input TYPEAPPLICATION=text name=txtDia maxlength=2 size=3 value='".$Dia."'></th>";	
		echo "<th align=center> Mes :<input TYPEAPPLICATION=text name=txtMes maxlength=2 size=3 value='".$Mes."'></th>";
		echo "<th align=center> Año :<input TYPEAPPLICATION=text name=txtAnio maxlength=4 size=5 value='".$Anio."'></th>";
		echo "</tr>";
	
	
	}//FIN if($consulta=="1")

	echo "<tr><Th COLSPAN=4><input type=submit name=btnConsultar value=Consultar></th></tr>";	
	
	if(isset($rbtnConsulta) and $rbtnConsulta=="1")
	{
		echo "<tr><Th COLSPAN=4><input type='radio' name=rbtnConsulta value=0 onclick='enter()'>Diario";
		echo "<INPUT TYPE=radio NAME=rbtnConsulta VALUE=1 checked onclick='enter()'>Rango";
	}
	else
	{
		echo "<tr><Th COLSPAN=4><input type='radio' name=rbtnConsulta value=0 onclick='enter()' checked>Diario";
		echo "<INPUT TYPE=radio NAME=rbtnConsulta VALUE=1 onclick='enter()'>Rango";
				
	}//FIN if($consulta=="1")
	
	echo "</th></tr>";
	echo "</table>";
	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
	echo "</form>";

	if (isset($txtDia) and isset($txtMes) and isset($txtAnio))
	{
		ValidarFecha($txtDia,$txtMes,$txtAnio); //Validamos que la fecha ingresada tenga un formato valido
		
	}// FIN IF VALIDAR FECHA
	
	if(isset($rbtnConsulta) and isset($btnConsultar) and $rbtnConsulta=="1" and $btnConsultar=='Consultar')
	{
	   	 $q="SELECT CS100.PACHIS, CS100.PACAP1, CS100.PACAP2, CS100.PACNO1, CS100.PACNO2, CS100.PACTDO, CS100.PACDOC, CS101.FECHA_DATA,
			CS101.HORA_DATA "
			."FROM ".$wbasedato."_000100 CS100, ".$wbasedato."_000101 CS101 "
		  	."WHERE CS100.PACACT = 'ON' AND CS100.PACHIS = CS101.INGHIS AND CS101.FECHA_DATA BETWEEN '".$txtFeIni."' AND '".$txtFeFin."'";

	    $err=AccesoDatos($q, "Q"); // Accede a la base de datos para realizar el query	
		$color="#999999";
		
		
		echo "<table border=0 align=center width=900>";
		echo "<caption>Ingresos De la Fecha: $txtFeIni Hasta : $txtFeFin </caption>";
	  	echo "<tr><td bgcolor=".$color."><font size=3><b>N° Historia</b></font></td>";
		echo "<td bgcolor=".$color."><font size=3><b>Primer Apellido</b></font></td>"; 
		echo "<td bgcolor=".$color."><font size=3><b>Segundo Apellido</b></font></td>";
		echo "<td bgcolor=".$color."><font size=3><b>Primer nombre</b></font></td>";	
		echo "<td bgcolor=".$color."><font size=3><b>Segundo Nombre</b></font></td>";	
		echo "<td bgcolor=".$color."><font size=3><b>Tipo Documento</b></font></td>";
		echo "<td bgcolor=".$color."><font size=3><b>N° Documento</b></font></td>";
		echo "<td bgcolor=".$color."><font size=3><b>Fecha Ingreso</b></font></td>";
		echo "<td bgcolor=".$color."><font size=3><b>Hora Ingreso</b></font></td></tr>"; 
			  	
		$row = mysql_fetch_array($err);
		$num = mysql_num_rows($err);

		for($j = 1;$j<=$num;$j++)
		{
			$r = $i/2;
			if ($r*2 === $i)//Da El fo0rmato de color a los datos mostrados por pantalla
			{
				$color="#CCCCCC";
			}
			else
			{
				$color="#999999";
			}
			 
			    echo "<tr align=left>";
				echo "<td bgcolor=".$color." align=center><font size=2>$row[0]</b></font></td>";	
			  	echo "<td bgcolor=".$color." align=center><font size=2>$row[1]</b></font></td>";	
				echo "<td bgcolor=".$color." align=center><font size=2>$row[2]</b></font></td>";	
				echo "<td bgcolor=".$color." align=center><font size=2>$row[3]</b></font></td>";	
				echo "<td bgcolor=".$color." align=center><font size=2>$row[4]</b></font></td>";			  	
				echo "<td bgcolor=".$color." align=center><font size=2>$row[5]</b></font></td>";	
				echo "<td bgcolor=".$color." align=center><font size=2>$row[6]</b></font></td>";
				echo "<td bgcolor=".$color." align=center><font size=2>$row[7]</b></font></td>";
				echo "<td bgcolor=".$color." align=center><font size=2>$row[8]</b></font></td>";			  		  			   
				echo "</tr>";	
				
				$i+=1;
				
			    $row = mysql_fetch_array($err);		
		}// Fin Del FOR

		echo "</table>";
		echo "</form>";		

	}// FIN if(($consulta=="1" and $txtDiaHasta!=""))

		
	if(isset($rbtnConsulta) and isset($btnConsultar) and $rbtnConsulta=="0" and $btnConsultar=='Consultar')
    {	
    	$feUsuario = $txtAnio."-".$txtMes."-".$txtDia;
    	$q="SELECT CS100.PACHIS, CS100.PACAP1, CS100.PACAP2, CS100.PACNO1, CS100.PACNO2, CS100.PACTDO, CS100.PACDOC, CS101.FECHA_DATA,
			CS101.HORA_DATA "
			."FROM ".$wbasedato."_000100 CS100, ".$wbasedato."_000101 CS101 "
			."WHERE CS100.PACACT = 'ON' AND CS100.PACHIS = CS101.INGHIS AND CS101.FECHA_DATA ='".$feUsuario."'";
  	
			$err=AccesoDatos($q, "Q"); // Accede a la base de datos para realizar el query
			 
			$color="#999999";
			echo "<table border=0 align=center width=900>";
			echo "<caption>Ingresos De la Fecha: $txtDia-$txtMes-$txtAnio</caption>";
	  		echo "<tr><td bgcolor=".$color."><font size=3><b>N° Historia</b></font></td>";
			echo "<td bgcolor=".$color."><font size=3><b>Primer Apellido</b></font></td>"; 
			echo "<td bgcolor=".$color."><font size=3><b>Segundo Apellido</b></font></td>";
			echo "<td bgcolor=".$color."><font size=3><b>Primer nombre</b></font></td>";	
			echo "<td bgcolor=".$color."><font size=3><b>Segundo Nombre</b></font></td>";	
			echo "<td bgcolor=".$color."><font size=3><b>Tipo Documento</b></font></td>";
			echo "<td bgcolor=".$color."><font size=3><b>N° Documento</b></font></td>";
			echo "<td bgcolor=".$color."><font size=3><b>Fecha Ingreso</b></font></td>";
			echo "<td bgcolor=".$color."><font size=3><b>Hora Ingreso</b></font></td></tr>"; 
			  	
			$row = mysql_fetch_array($err);
			$num =mysql_num_rows($err);

		  	for($j = 1;$j<=$num;$j++)
			{
				$r = $i/2;
				if ($r*2 === $i)//Da El fo0rmato de color a los datos mostrados por pantalla
				{
					$color="#CCCCCC";
				}
				else
				{
					$color="#999999";
				}

			    echo "<tr align=left>";
			  	echo "<td bgcolor=".$color." align=center><font size=2>$row[0]</b></font></td>";	
			  	echo "<td bgcolor=".$color." align=center><font size=2>$row[1]</b></font></td>";	
			  	echo "<td bgcolor=".$color." align=center><font size=2>$row[2]</b></font></td>";	
			  	echo "<td bgcolor=".$color." align=center><font size=2>$row[3]</b></font></td>";	
			  	echo "<td bgcolor=".$color." align=center><font size=2>$row[4]</b></font></td>";			  	
			  	echo "<td bgcolor=".$color." align=center><font size=2>$row[5]</b></font></td>";	
				echo "<td bgcolor=".$color." align=center><font size=2>$row[6]</b></font></td>";
				echo "<td bgcolor=".$color." align=center><font size=2>$row[7]</b></font></td>";
				echo "<td bgcolor=".$color." align=center><font size=2>$row[8]</b></font></td>";			  		  			   
				echo "</tr>";	
				$row = mysql_fetch_array($err);
				$i+=1;	

			}//FIN 	for ($j = 1;$j<=$num;$j++)

		echo "</table>";
		echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
		echo "</form>";		
  	}

	
}// FIN IF session_start()
    
//Fin Programa principal 
?>
	
	
<?php

	function ValidarFecha($Dia,$Mes,$Anio)
	{
		
		if (($Dia>"31" or $Mes>"12") or $Anio<="1900")
		{
			echo "<font color=#ff0000><div align=center bgcolor=#ff0000> Error En la fecha Ingresada : $Dia-$Mes-$Anio </div></font>"; 
			exit;		
		}
		else
		{
			return;	
		}//FIN if (($Dia>"31" or $Mes>"12") or $Anio<="1900")
		
	}//function ValidarFecha($Dia,$Mes,$Anio)
	
?>

<script type="text/javascript">
	<!--
	function enter()
	{
		document.forms.Ingreso_egresos.submit();

	}// Fin de la funcion enter 
	//-->
</script>
	
<?php

	function AccesoDatos($q, $Type)
	{
		

		mysql_select_db("Matrix",$conex);
		$err = mysql_query($q, $conex);
		
		    //Condicional para verificar el tipo de consulta
		    if ($Type == "Q")
		    {
		        $num = mysql_num_rows($err);
		        if ($num != 0)
		        {
		            return $err;
   
		        }
		        else
		        {
		            //Pinta una advertencia cuando el documento del medico ingresado no existe en la base de datos
		            echo "<label><div align=center style=font-size:32px>No se encontro informacion </div>";
		            echo "</label>";
					exit;
		        }//if ($num != 0)
		        
		    }// FIN if ($Type == "Q")
		    
		    mysql_close($conex);
	}//FIN function AccesoDatos($q, $Type)

?>
	

</html>
