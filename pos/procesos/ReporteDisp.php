<html>
<head>
<title>Reporte de disponibilidad de producto Farmastore</title>

<script language="javascript">
      function NewDrop()
      {
       document.seleccion.submit();
      }
  </script>
  
</head>
<body >
<?php
include_once("conex.php");

/********************************************************
*     PROGRAMA FRONT END DE REPORTE DE ACCIDENTES	*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte de disponibilidad de producto Farmastore
//AUTOR							:Carolina Castaño
//FECHA CREACION				:ENERO 2006
//FECHA ULTIMA ACTUALIZACION 	:02 de Mayo de 2006
//DESCRIPCION					:Reporte de disponibilidad de medicamentos por sucursal


//VARIABLES:
//$med=medicamento que se busca, sirve como bandera para el isset
//==================================================================================================================================

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
			//FORMA PARA INGRESAR EL ARTÍCULO 
			//$empresa='farstore';
			

			

			$query ="SELECT Ccocod, Ccodes FROM " .$empresa."_000003 where Ccoest='on'";
	
			$result = mysql_query($query);
			echo "<table border=0 align=center size=100%>";
			echo "<tr><td align=center rowspan=2><img SRC='/MATRIX/images/medical/pos/logo_".$empresa.".png'></td>";// width=150 high=43></td>"; ";
			echo "<td align=center bgcolor='#ADD8E6'><A NAME='Arriba'><font size=5>CONSULTA DE KARDEX</font></a></td></tr>";
						echo "<tr><td align=center bgcolor='#ADD8E6'><A NAME='Arriba'><font size=2> ReporteDisp.php Ver. 02/05/2006</font></a></td></tr>";
			echo "</table></br>";
			
			echo "<table border=0 align=center>";
			echo "<tr><td align=center bgcolor='#ADD8E6'><font size=4>INGRESE POR FAVOR LOS SIGUIENTES DATOS:</font></td>";
			echo "</table>";
			
			echo "<table border=0 align=center>";

			echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
			echo "<tr><td align=center bgcolor='#cccccc'><font size=2  face='arial'>Código inicial:</b>&nbsp</td>";
			echo "<td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='text' name='codIni'></td></tr>";
			echo "<tr><td align=center bgcolor='#cccccc'><font size=2  face='arial'>Código final:</b>&nbsp</td>";
			echo "<td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='text' name='codFin'></td></tr>";
			echo "<tr><td align=center bgcolor='#cccccc'><font size=2  face='arial'>Descripción:</b>&nbsp</td>";
			echo "<td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='text' name='nom'></td></tr>";
			echo "<tr><td align=center bgcolor='#cccccc'><font size=2  face='arial'>Sucursal:</b>&nbsp</td>";
			echo "<input type='hidden' name='estado' value='1'>";
			echo "<td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><select name='centro'>";
			
			$option = "<option selected value='todas'";
			echo $option.">TODAS LAS SUCURSALES</option>";
			
			$i=0;
			While ( $resulta = mysql_fetch_row($result) )
  			{ 
				$option = "<option value='$resulta[0]'";
				echo $option.">$resulta[0] - $resulta[1] </option>";
			
				$i++;
   			} 
   			echo "</select>";
			
   			echo "</td></tr>";
   		
			echo "<tr><td align=center bgcolor='#cccccc' colspan=2><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='BUSCAR' ></td></tr></form></TABLE></br></br>";			
		
   	

		if (isset ($estado))
		
		{
			if ($codFin=='' and $nom=='' and $codIni=='')
			{
				echo "<table border=0 align=center>";
				echo "<tr><td align=center bgcolor='#ADD8E6'><font size=4  face='arial'>Por favor ingrese algún criterio de búsqueda</td></tr></TABLE>";
				$fuera=1;
				
			}
			
			if ($codFin=='' and ($nom!='' or $codIni!=''))
			{
				//Consultar el código y presentación del medicamento a partir del nombre
				if ($nom!='' and $codIni=='')
				$q="SELECT Artcod, Artnom, Artuni FROM " .$empresa."_000001 where Artnom like '%$nom%'";
				
				//Consultar el nombre y presentación del medicamento a partir del código
				if ($nom=='' and $codIni!='')
				$q ="SELECT Artcod, Artnom, Artuni FROM " .$empresa."_000001 where Artcod like '%$codIni%'";
				
				//Consultar el nombre y presentación del medicamento a partir del código y el nombre
				if ($nom!='' and $codIni!='')
				$q ="SELECT Artcod, Artnom, Artuni FROM " .$empresa."_000001 where Artnom like '%$nom%' and Artcod like '%$codIni%'";
				
				
				$result = mysql_query($q);
				$num=mysql_num_rows($result);
			
				if($num >=1)
				{
						echo "<table border=0 align=center>";
						echo "<form NAME='seleccion' ACTION='' METHOD='POST'>";
						echo "<tr><td align=center bgcolor='#ADD8E6'><font size=2  face='arial'>RESULTADO PARA:</b>&nbsp</td>";
						echo "<td align=center bgcolor='#ADD8E6' ><font size='2'  align=center face='arial'><select name='codIni' onChange='NewDrop()'>";
						
						
						$i=0;
					While ( $resulta = mysql_fetch_row($result) )
  					{ 
						$option = "<option value='$resulta[0]'";
						echo $option.">$resulta[0]-$resulta[1]</option>";
						$medCod[$i]=$resulta[0];  
						$medNom[$i]=$resulta[1]; 
						$medPre[$i]=$resulta[2];    
						$i++;
						
   					} 
   					echo "<input type='hidden' name='estado' value='1'>";
   					echo "<input type='hidden' name='centro' value='$centro'>";
   					echo "<input type='hidden' name='nom' value='$nom'>";
   					echo "<input type='hidden' name='codFin' value='$codFin'>";
   					echo "</select>";
   					echo "</td></tr>";
					echo "</form> </TABLE></br>";
					$medicamento=0;			
						
				}
				else
				{
					echo "<table border=0 align=center>";
					echo "<tr><td align=center bgcolor='#ADD8E6'><font size=4  face='arial'>La información ingresada no se encuentra registrada en el sistema, intente nuevamente por favor</td></tr></TABLE>";
					$fuera=1;
				}
			
			}else if ($codFin!='')
			{
				//se ingresa un rango de códigos a buscar
				if ($codIni=='')
				{
					echo "<table border=0 align=center>";
					echo "<tr><td align=center bgcolor='#ADD8E6'><font size=4  face='arial'>Para ingresar un solo código utilice el campo 'código inicial'. </br> Para consultar un rango de códigos debe ingresar un código inicial y un código final. </br> Intente nuevamente por favor</td></tr></TABLE>";
					$fuera=1;
				}else
				{
					
						$query ="SELECT Artcod, Artnom, Artuni FROM " .$empresa."_000001 where Artcod between '$codIni' and '$codFin'";
		
						$result = mysql_query($query);
						$num=mysql_num_rows($result);
						
						$i=0;
						While ( $resulta = mysql_fetch_row($result) )
  						{ 
							$medCod[$i]=$resulta[0];  
							$medNom[$i]=$resulta[1]; 
							$medPre[$i]=$resulta[2];       
							$i++;
   						} 

					if ($i==1)
					{
						echo "<table border=0 align=center>";
						echo "<tr><td align=center bgcolor='#ADD8E6'><font size=4  face='arial'>La información ingresada no se encuentra registrada en el sistema, intente nuevamente por favor</td></tr></TABLE>";
						$fuera=1;
					}
				}
			}
		
	if (!isset ($fuera))
	{
		if ($centro=='todas')
		{
			$query ="SELECT Ccocod, Ccodes FROM " .$empresa."_000003 where Ccoest='on'";
			$result = mysql_query($query);
			$k=0;
			While ( $resulta = mysql_fetch_row($result) )
  			{ 
				$cenCo[$k]=$resulta[0];  
				$cenNo[$k]=$resulta[1]; 
				$k++;
				//echo $cenCo[$k];
   			} 
		}else
		{
			$query ="SELECT Ccodes FROM " .$empresa."_000003 where Ccocod='$centro'";
			 
			$result = mysql_query($query);
			$k=0;
			While ( $resulta = mysql_fetch_row($result) )
  			{ 
				$cenNo[$k]=$resulta[0];
				$cenCo[$k]=$centro;  
				$k++;
				//echo $cenNo[$k];
			}
		}
   	
		

			for($i=0;$i<count($cenCo); $i++)
			{

				echo "<table border=0 align=center>";
				echo "<tr><td align=center bgcolor='#ADD8E6'><font size=4>$cenNo[$i]:</font></td></tr>";
				echo "</table>";
				
				echo "<table border=0 align=center>";
				echo "<tr><td align=center bgcolor='#cccccc'><font size=4>Código:</font></td>";
				echo "<td align=center bgcolor='#cccccc'><font size=4>Descripción:</font></td>";
				echo "<td align=center bgcolor='#cccccc'><font size=4>Presentación:</font></td>";
				echo "<td align=center bgcolor='#cccccc'><font size=4>Cantidad:</font></td>";
				echo "<td align=center bgcolor='#cccccc'><font size=4>Precio:</font></td></tr>";
				
				for($j=0;$j<count($medCod); $j++)
				{
					
					
						$query ="SELECT Karexi FROM " .$empresa."_000007 where Karcod='".$medCod[$j]."' and Karcco='".$cenCo[$i]."'";
						$result = mysql_query($query);
						$num=mysql_num_rows($result);
					
						$resulta = mysql_fetch_row($result);
						if($num >=1)
						{
							$medExi=$resulta[0];  
   						}else
   						{
	   						$medExi=0;
	   					}
	   					
	   					
	   					//$query ="SELECT Mtavac FROM " .$empresa."_000026 where substring (Mtaart,1,8)='".substr($mtaArt,0,8)."' and substring (Mtacco,1,4)='".substr($mtaCco,0,4)."'";
	   					
	   					$query ="SELECT Mtavac FROM " .$empresa."_000026 where mid(mtaart,1,instr(mtaart,'-')-1)='".$medCod[$j]."' and mid(mtacco,1,instr(mtacco,'-')-1)='".$cenCo[$i]."'";
						
	   					//mid(mtaart,1,instr(mtaart,"-")-1)
	   					//echo $query;
	   					
	   					$result = mysql_query($query);
						$num=mysql_num_rows($result);
					
						$resulta = mysql_fetch_row($result);
						if($num >=1)
						{
							$medVal=$resulta[0];  
   						}else
   						{
	   						$medVal=0;
	   					}
	   					if (is_int ($j/2))
	   			
	   					$color='#EOFFFF';
   						else
   				
	   					$color='#cccccc';
   				
	   		
						echo "<tr><td align=center bgcolor='$color'><font size=3>$medCod[$j]</font></td>";
						echo "<td align=left bgcolor='$color''><font size=3>$medNom[$j]</font></td>";
						echo "<td align=left bgcolor='$color''><font size=3>$medPre[$j]</font></td>";
						echo "<td align=center bgcolor='$color''><font size=3>$medExi</font></td>";
						echo "<td align=right bgcolor='$color''><font size=3>$";
						
						$imp=strlen($medVal);
						
						for($p=1;$p<=strlen($medVal); $p++)
						{
							$neg=-$imp;
							echo substr($medVal,$neg,1);
							$imp--;
							if (is_int( (strlen($medVal)-$p)/3) and $medVal!=0 and $imp!=0)
							echo ",";
						
						}
						echo "</font></td></tr>";
						if (isset ($medicamento))
						$j=count ($medCod);
						
					
				}
				
				echo "</table></br>";
			}
			
		
	}
	}	
	include_once("free.php");
}