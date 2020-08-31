<?php
include_once("conex.php");




include_once("root/comun.php");

if ($operacion=='inicioprograma')
{
	include_once("../procesos/funciones_talhuma.php");
	

	
 
	cargar_hiddens_para_autocomplete();
	return;
}

function cargar_hiddens_para_autocomplete()
{
	// --> CCOS
	global $conex;
	global $wbasedato;
	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

	
	$arr_cco = array();
	
	$q_cco = " SELECT Ccocod AS codigo, Cconom AS nombre 
				 FROM costosyp_000005
				WHERE Ccoest = 'on' 	
				ORDER BY nombre ";
			
	$r_cco = mysql_query($q_cco,$conex) or die("Error en el query: ".$q_cco."<br>Tipo Error:".mysql_error());
	
	while($row_cco = mysql_fetch_array($r_cco))
	{
		$row_cco['nombre'] = str_replace($caracter_ma, $caracter_ok, $row_cco['nombre']);
		$arr_cco[trim($row_cco['codigo'])] = trim($row_cco['nombre']);
	}
	
	echo json_encode($arr_cco);
	
}

?>

<head>
  <title>arbol de relaciones</title>
  
</head>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
		<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
		<!--<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>-->

<script type="text/javascript">

function seleccioncco()
{
	var emp_pmla = $("#wemp_pmla").val();
	var cco = $("#buscador_cco").attr('valor');
	var params = 'consultaarbolrelaciones.php?wemp_pmla='+emp_pmla+'&wcco='+cco+'&wncco='+cco+'&pintatabla=pintar';
			$.get(params, function(data) {
			$('#contenedor').html(data);
			});

}

function cerrarVentana ()
{
 window.close();

}


$(document).ready(function() {

	//alert("hola");
  $.post("../procesos/consultaarbolrelaciones.php",
		{
			consultaAjax 	: '',
			inicial			: 'no',
			operacion		: 'inicioprograma',
			wemp_pmla		: $('#wemp_pmla').val(),
			wtema           : $('#wtema').val(),
			wuse			: $('#wuse').val()
		}
		, function(data) {
			
			cargar_cco (eval('(' + data + ')'));
		});

});

function cargar_cco (ArrayValores)
{
	var ccos	= new Array();
	var index		= -1;
	var tr ;
	var n = 1;
	tr ="";
	var wfc ;
	for (var cod_ccos in ArrayValores)
	{
		index++;
		ccos[index] = {};
		ccos[index].value  = cod_ccos+'-'+ArrayValores[cod_ccos];
		ccos[index].label  = cod_ccos+'-'+ArrayValores[cod_ccos];
		ccos[index].valor  = cod_ccos;
		ccos[index].nombre = ArrayValores[cod_ccos];
	}
	
	$( "#buscador_cco" ).autocomplete({
		
		minLength: 	0,
		source: 	ccos,
		select: 	function( event, ui ){					
			$( "#buscador_cco" ).val(ui.item.label);
			$( "#buscador_cco" ).attr('valor', ui.item.valor);
			seleccioncco();
			return false;
		}
		
		
		
	});	
	
	
}
</script>

<style type="text/css">
    .displ{
        display:block;
    }
    .borderDiv {
        border: 2px solid #2A5DB0;
        padding: 5px;
    }
    .resalto{
        font-weight:bold;
    }
    .parrafo1{
        color: #676767;
        font-family: verdana;
    }
	.ui-autocomplete{
		max-width: 	300px;
		max-height: 150px;
		overflow-y: auto;
		overflow-x: hidden;
		font-size: 	9pt;
	}
</style>

<body>

<?php
  /*********************************************************
   *               VISUALIZACION DE RELACIONES             *
   *                                                       *
   *     				                        		   *
   *********************************************************/
//==================================================================================================================================
//PROGRAMA                   : arbolrelaciones.php
//AUTOR                      : Felipe Alvarez Sanchez
//
//FECHA CREACION             : Julio 12 de 2012
//FECHA ULTIMA ACTUALIZACION :
 
//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//     Programa usado para visualizar de manera clara las relaciones calificador-calificado en cada uno de los centros de costo           \\        
//========================================================================================================================================\\
//========================================================================================================================================\\
//                                                                           \\
//========================================================================================================================================\\



$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
if (isset($pintatabla))
{
	$query= "SELECT * "
		  . "  FROM  ".$wbasedato."_000013 "
		  ."  WHERE  Idecco = '".$wcco."' "
		  . "	AND  Ideest = 'on' ";
	$res = mysql_query($query,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
	$numr = mysql_num_rows($res);
}
else 
{
$numr =1;
}
if ($numr <= 0)
{
	
	echo "<table align='center'><tr class='fila1'><td>El Centro de costos no tiene Personal asignado
	</td></tr></table>";
}
else
{
	if (!isset($pintatabla))
	{
		echo '<input type="hidden" id="wcco" name="wcco" value="'.$wcco.'" />';
		echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';

		if(!isset($wcco)){ $wcco="";}


		
		echo"<table align='center' width='400' >
				  <tr >
						<td nowrap=nowrap class='encabezadoTabla'>
							Seleccione el Centro de Costos:
						</td>
						<td nowrap=nowrap class='fila2'>
							<input type='text'  id='buscador_cco' size='40' >
							<img width='12'  border='0' height='12' title='Busque el nombre o parte del nombre del centro de costo' src='../../images/medical/HCE/lupa.PNG'>
						</td>
				  </tr>
			 </table>
			 <table>
				<tr>
					<td>&nbsp;<td>
				</tr>
			 </table>
			<div id='contenedor'></div>";
	}
	if (isset($pintatabla) and ($pintatabla=='pintar'))
	{
		
		$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
		$q = "   SELECT Ajeucr, Ajeuco,Ajefor, Ideno1, Ideno2, Ideap1, Ideap2,Cconom "
		   . "     FROM ".$wbasedato."_000008, ".$wbasedato."_000013, costosyp_000005"
		   . "    WHERE Ajeccr = '".$wcco."' "
		   . "      AND (Ajeuco = Ideuse) "	   
		   . "      AND (Idecco = Ccocod ) "
		   . "      AND Ideest != 'off'	"	   
		   . " ORDER BY Ajeucr,Ajecco,Ideno1, Ideno2, Ideap1, Ideap2 ";
	 
			
		$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numrowcom = mysql_num_rows($res);
		
		if($numrowcom !=0)
		{
			$personas=1;
			echo'<table align="center" width="1000" >
					<tr class="encabezadoTabla">
						<td width="280" style="font-size:11pt;text-align:center" >Jefe</td>
						<td width="280" style="font-size:11pt;text-align:center" >Subordinado</td>
						<td width="200" style="font-size:11pt;text-align:center">Centro de Costos</td>
					</tr>';
			$r=0;
			$i=0;
			$k=0;
			$vectorCalificados=array();
			$vectorFormularios=array();
			$aux="";
			
			While($row = mysql_fetch_array($res))
			{
				
					if($aux!=$row['Ajeucr'])
					{
						$vectorCalificadores[$k]=$row['Ajeucr'];
						
						$q = "SELECT  Ideno1, Ideno2, Ideap1, Ideap2   "
						   . "  FROM ".$wbasedato."_000013 "
						   . " WHERE Ideuse= '".$vectorCalificadores[$k]."' "; 
						
						$res1=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$row1 = mysql_fetch_array($res1);
						
						$vectorNcalificadores[$k]= $row1['Ideno1']." ".$row1['Ideno2']." ".$row1['Ideap1']." ".$row1['Ideap2'];
						$k++;
						$aux=$row['Ajeucr'];
						$i=0;
					}
					$vectorCalificados[$row['Ajeucr']][$i] = $row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2'];
					$vectorFormularios[$row['Ajeucr']][$i] = $row['Fordes'];
					$vectorCco[$row['Ajeucr']][$i] = $row['Cconom'];
				$i++;
			}
			
			
			
			mysql_data_seek($res,0); 
			$row = mysql_fetch_array($res);
			$tabppales = count($vectorCalificadores);

			$j=0;
			$r=1;
			while($j<$tabppales)
			{
			
			echo"<table align='center' width='1000' class='borderDiv' >";     
				if (is_int ($r/2))
				{
					$wcf="fila1";  // color de fondo de la fila
				}
				else
				{
					$wcf="fila2"; // color de fondo de la fila
				}
			
				$numcalificados= count($vectorCalificados[$vectorCalificadores[$j]]);
				
				echo'	<tr>
							<td width="280" class='.$wcf.'  rowspan='.$numcalificados.'><b>'.$vectorNcalificadores[$j].'</b></td>';
				$m=0;			
				
				while($m<$numcalificados)
					{
					  
					  if (is_int ($m/2))
					  {
						$wcf="fila1";  // color de fondo de la fila
					  }
					  else
					  {
						$wcf="fila2"; // color de fondo de la fila
					  }
					  echo' <td width="280" class='.$wcf.' >'.$vectorCalificados[$vectorCalificadores[$j]][$m].'</td>
							<td width="200" class='.$wcf.' >'.$vectorCco[$vectorCalificadores[$j]][$m].'</td>
							
						</tr>
						<tr >';
					  $m++;
					
					}
			echo	   '</tr>';		
				$j++;
				$r++;
			echo"</table><br>";
			
			}
		}

		
		
		$q = "     SELECT Ideno1, Ideno2, Ideap1, Ideap2,Ajecco "
		   . "	     FROM ".$wbasedato."_000013  "
		   . "  LEFT JOIN ".$wbasedato."_000008  "
		   . "         ON  Ideuse = Ajeuco"
		   . "      WHERE Idecco='".$wcco."' "
		   . "        AND Ideest='on' "
		   . "        AND (Ajecco IS NULL )";

		$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		

		$i=0;
		$nocalificado=array();
		$nnocalificado=array();
		$ningunarelacion=array();
		While($row = mysql_fetch_array($res))
		{
			$nocalificado[$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']]=$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2'];
			$nnocalificado[$i]=$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2'];
			$i++;
		}
		
		
		$i=0;
		

		

		$q = "     SELECT Ideno1, Ideno2, Ideap1, Ideap2,Ajeccr,Ideuse "
		   . "	     FROM ".$wbasedato."_000013  "
		   . "  LEFT JOIN ".$wbasedato."_000008  "
		   . "         ON  Ideuse = Ajeucr"
		   . "      WHERE Idecco='".$wcco."' "
		   . "        AND Ideest='on' "
		   . "        AND (Ajeccr IS NULL)" ;
		  
		 

		$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());




		$i=0;
		$nocalificador=array();
		$nnocalificador=array();
		While($row = mysql_fetch_array($res))
		{
			$nocalificador[$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']]=$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2'];
			$nnocalificador[$i]=$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2'];
			$i++;
		}


		$i=0;
		$a=0;
		while ( $i<count($nnocalificador))
		{
		if(array_key_exists($nnocalificador[$i],$nocalificado))
		{
		$ningunarelacion[$a]=$nnocalificador[$i];
		$a++;
		}

		$i++;
		}
		
        
		if (count($ningunarelacion)==0)
		{
		
		}
		else
		{
			
			
			echo"<table align='center' width='1000'  >
			<tr>
			<td>";
			echo"<b>FALTANTES (No es calificador, ni califica a alguien)<b> 
			</td>
			</tr>";
			echo "</table>";
			
			
			  
			echo"<table align='center' width='1000' class='borderDiv' >";

			$i=0;
			
			while($i<count($ningunarelacion))
			{
				echo"<tr class='fila1'>";
				echo"<td>".$ningunarelacion[$i]."</td>";

				echo"</tr>";   

				$i++;
			}
			echo "</table>";
			
			
			echo "<br><br>";
			
			

		
		}

	}
}
echo "<br><br>";
?>
</body>