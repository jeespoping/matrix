<html>
<head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<style type="text/css">
.enlinea{
               display:inline-block;
               vertical-align: top;
               /display: -moz-inline-stack;/ /* FF2*/
               zoom: 1; /* IE7 (hasLayout)*/
               display: inline; / IE */
       }
	   
 .borderDiv {
        border: 2px solid #2A5DB0;
        padding: 5px;
    }
    </style>
</head>
<body>
<?php
include_once("conex.php");

//**********************************************//
//*************** FUNCIONES*********************//
//**********************************************//

//---------------------------------------------------------------------------------------------------------------------------
//Funcion que llena el vector de dias, en la primera posicion de la matriz [*][1] van las iniciales de los dias
// y en la posicion [*][2] va el numero del dia
function llenaVectorFecha($wfecha_i,$ndias)
{
	global $wmovhos;
    global $wemp_pmla;
	$fecha_i=explode("-",$wfecha_i);

	  for ($i=0;$i<=$ndias;$i++)
	  {
		
		$nueva=mktime(0,0,0,$fecha_i[1],$fecha_i[2],$fecha_i[0])+  $i * 24 * 60 * 60;
		$nueva=date("Y-m-d",$nueva);
		//se llena las iniciales del dia
		switch (date("l",strtotime($nueva)))
		{
		  case "Monday":
		   $nombredia= "Lunes";
		  break;
		  
		  case "Tuesday":
		   $nombredia= "Martes";
		  break;

		  case "Wednesday":
		   $nombredia= "Miercoles";
		  break;
		  
		  case "Thursday":
		   $nombredia= "Jueves";
		  break;
		  
		  case "Friday":
		   $nombredia= "viernes";
		  break;

		  case "Saturday":
		   $nombredia= "Sabado";
		  break;
		  
		  case "Sunday":
		   $nombredia= "Domingo";
		  break;
		  
		}
		
		$nuevafecha[$i][1]= $nombredia;
		
		
		// se llena el numero del dia
		$nuevafecha[$i][2]= date("d",strtotime($nueva));
		$nuevafecha[$i][3]=$nueva;
		
	  }
	  return ($nuevafecha);
}

//-------------------------------------------------------------------------------------------------------------------------



//-------------------------------------------------------------------------------------------------------------------------
// Funcion que Dibuja la tabla, segun la especialidad seleccionada y el rango de fechas seleccionado
function PintaTabla($conex,$wespecialidad,$wfecha_i,$wfecha_f)
{

	global $wmovhos;
    global $wemp_pmla;
	// las fechas inicial y final se llevan a segundos, luego se restan estas y el resultado se vuelve a pasar a dias, esto con el
	// fin de calcular el numero de dias y asi entrar al ciclo las veces que sean necesarias
	$fecha_i=explode("-",$wfecha_i);
	$fecha_f=explode("-",$wfecha_f);
	$ndias = mktime(0,0,0,$fecha_f[1],$fecha_f[2],$fecha_f[0]) - mktime(0,0,0,$fecha_i[1],$fecha_i[2],$fecha_i[0]) ;
	$ndias = $ndias/(60 * 60 * 24);


	// Se llena el $vecdia segun los dias seleccionados. En la primera posicion de la matriz [*][1] van las iniciales de los dias
	// y en la posicion [*][2] va el numero del dia
	$vecdia = llenaVectorFecha($wfecha_i,$ndias);

	$qesp= "SELECT Turtur "   
	   ."  FROM  ".$wmovhos."_000124 "
	   ." WHERE  Turtur = '".$wespecialidad."' ";

	$resp = mysql_query($qesp,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qesp." - ".mysql_error());
	$rowsesp = mysql_fetch_array($resp);   

	$qespnom= "SELECT Espnom "   
	   ."  FROM  ".$wmovhos."_000044 "
	   ." WHERE  Espcod = '".$wespecialidad."' ";

	$respnom = mysql_query($qespnom,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qespnom." - ".mysql_error());
	$rowsespnom = mysql_fetch_array($respnom);      
	

	$nombre_especialidad = $rowsespnom['Espnom'];
	//Codigo que pinta la tabla
	// Empieza el encabezado  
	echo "<table style='display : none' align='center' cellspacing='2'>";	
	echo "<tr class='encabezadoTabla'>";
	echo "<td align='center' rowspan='4'> HORARIO " .$nombre_especialidad. " </td>";
	echo "<td align='center' colspan='".($ndias + 1 )."' height='21'>Rango de Fechas</td>";	
	echo "</tr>";
	echo "<tr class='encabezadoTabla'>";

	$auxmes = date("n",strtotime($vecdia[0][3]));
	$mes=0;
	$meses = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	for ($i=0;$i<=$ndias;$i++)
	 {
	   if (date("n",strtotime($vecdia[$i][3]))!= $auxmes){
	   
		   echo "<td align='center' colspan='".($mes)."'> ".$meses[$auxmes]." </td>";
		   $mes=0;
	   }
	   $auxmes=(date("n",strtotime($vecdia[$i][3])));
	   $mes++;
	 }
	echo "<td align='center' colspan='".($mes)."'> ".$meses[$auxmes]." </td>";
	echo "</tr>";
	echo "<tr class='encabezadoTabla'>";

	//for para la letra inicial del dia
	for ($i=0;$i<=$ndias;$i++)
	 {
	   echo "<td align='center'> ".$vecdia[$i][1]." </td>";
	 }
	echo "</tr>";
	echo "<tr class='encabezadoTabla'>";

	//for para el numero del dia
	for ($i=0;$i<=$ndias;$i++)
	 {
	   echo "<td align='center'> ".$vecdia[$i][2]." </td>";
	 }
	echo "</tr>";

	//termina encabezado


	$qron = " SELECT Turnom, Turcod
			  FROM ".$wmovhos."_000124, ".$wmovhos."_000126
			  WHERE Turtur = Turcod
			  AND Turesp = ".$wespecialidad." "; 
			  
	$resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
	$numron = mysql_num_rows($resron);



	// $j variable para controlar el estilo de cada fila
	$j=1;	  
	$rondas=llenaTurnos($wespecialidad,$conex);
	$numron=numTurnos($wespecialidad,$conex);
	//print_r($rondas);
	$festivo = festivos($conex);
	

	// empieza la estructura central de la tabla
	for ($f=0;$f<$numron;$f++) 
	{


	  // este if es para saber que fondo corresponde a la fila
	   if (is_int ($j/2))
	   {
		 $wcf="fila1";  // color de fondo de la fila
	   }
	   else
	   { 
		$wcf="fila2"; // color de fondo de la fila
	   }

	  
	   
	  
	   $j++;  
	   echo "<tr class='".$wcf."' onclick='ilumina(this,\"".$wcf."\")'>";
	   echo "<td class='encabezadoTabla' ><b>".$rondas[$f][0]."</b> </td>";
	  
	   for($i=0;$i<=$ndias;$i++){
	   
	   $wcf2="";
	   // if(@$festivo[$vecdia[$i][3]])
	   //{
		//	$wcf2="fondoRojo";
	   //} 
	   if($vecdia[$i][1]=="Domingo")
	   {
            //cambio de color si es domingo
			$wcf2="fondoRojo";
			
	   }
	   
	   $q =  "SELECT Medno1, Medno2, Medap1, Medap2,Medtel,Medcel,Medbip 
				FROM ".$wmovhos."_000125, ".$wmovhos."_000065, ".$wmovhos."_000048 
			   WHERE Agefec ='".$vecdia[$i][3]."'
				 AND Agetur ='".$rondas[$f][1]."' 
				 AND Ageces ='".$wespecialidad."' 
				 AND Esmndo = Agecme    
				 AND Esmcod = Ageces
				 AND Esmest ='on'
				 AND Meddoc = Esmndo    
				 AND Medtdo = Esmtdo    
				 AND Medest = 'on'
			GROUP BY Agecme;";

	   $qres = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $qnum = mysql_num_rows($qres); 
	   if ($qnum != 0)
	   {
		 echo "<td align='Justify' class='".$wcf2."' nowrap='nowrap'>";
		 for($m=0;$m<$qnum;$m++)
		 {
			 $qrow = mysql_fetch_array($qres);
			 
			 echo "".$qrow['Medno1']." ".$qrow['Medno2']." ".$qrow['Medap1']." ".$qrow['Medap2']."";//solo sirve por ahora
			 echo "<br>";
			 if ($qrow['Medtel']!='')
			 {
				echo "Tel: ".$qrow['Medtel']."";
				echo "<br>";
			 }
			 if ($qrow['Medcel']!='')
			 {
				echo "Cel: ".$qrow['Medcel']."";
				echo "<br>";
			 }
			 if($qrow['Medbip']!='')
			 {
				echo "Bip:".$qrow['Medbip']."";
				echo "<br>";
			 }	
		}
	    echo "</td>";
	   }
		else
	   {
		echo "<td align='right' class='".$wcf2."'></td>";
	   }
	   }
	   echo "</tr>";
	   

	}					

	echo"<tr ><td  colspan='".($ndias + 2)."' align='center'><input type='button' value='RETORNAR' onclick='retornar(\"".$wemp_pmla."\", \"".$wfecha_f."\")'><input type='button' value='CERRAR' onclick='javascript:window.close();'></td></tr>";
	echo "</table>";
	// tabla con el titulo
	
	
	echo "<br><br>";
	// tabla con el reporte
	// echo"<table id='treporte'>";
	// echo"<tr><td valign='top' width='600'>";
	//echo"<table align='right' ><tr><td><input type='button' value='RETORNAR' onclick='retornar(\"".$wemp_pmla."\", \"".$wfecha_f."\")'><input type='button' value='CERRAR' onclick='javascript:window.close();'><td></tr></table>";
	echo "<br>";
	echo "<br>";
	echo "<table align='center' ><tr><td align='center'><b>CUADRO DE TURNOS DE ".$nombre_especialidad."";
	//
	// tabla con el titulo

	echo "<br> de ".$fecha_i[0]."-".$fecha_i[1]."-".$fecha_i[2]." Hasta el  ".$fecha_f[0]."-".$fecha_f[1]."-".$fecha_f[2]."</b></td></tr>";
	echo"<tr align='center'><td><input type='button' value='RETORNAR' onclick='retornar(\"".$wemp_pmla."\", \"".$wfecha_f."\")'><input type='button' value='CERRAR' onclick='javascript:window.close();'><td></tr></table>";
	//
	
	$q= "  SELECT Medno1, Medno2, Medap1, Medap2 ,Meddoc  ,Medhdi,Medtel,Medbip,Medcel "
	   ."    FROM movhos_000048 "		
	   ."   WHERE Medesp LIKE '%".$wespecialidad."%' "
	   ."     AND Medest = 'on'  "
	   ."     AND Medhdi = 'on' "
	   ."ORDER BY Medhdi Desc ,Medno1, Medno2, Medap1 " ;
	   
	$res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	echo "<br>";
	echo "<br>";
	echo "<table align='center' width='300'><tr><td>";
	echo "<div  class='borderdiv'>";
	echo "<table id='table_medicos' width='600' align='center'>";
    echo "<tr><td align ='left' ><b>Ver Medico</b></td><td  align ='left'><b>Ver Telefonos</b></td></tr>";
	$k=0;
	echo "<tr><td  align ='left'><Select onchange='ilumina2(this)'>";
	echo "<option value='todo' >Todos</option>";

	while ($row = mysql_fetch_array($res)) 
	{
		echo "<option value='".$row['Meddoc']."' >".$row['Medno1']." ".$row['Medno2']." ".$row['Medap1']." ".$row['Medap2']."</option>";
	}
	echo "</select></td><td  align ='left'><input type='checkbox' id='checver'  value='on'  checked  onclick='checkverno(this)'></td></tr>";
	echo"</table></div></tr><td></table>";
	echo "<br>";
	echo "<br>";
	
	echo "<table align='center'  width='600'>";
	echo "<tr align='center' class='encabezadoTabla'>";
	echo "<td nowrap='nowrap' ></td>";

	for ($j=0;$j<(count($rondas) );$j++)
	{
		
		echo "<td>".$rondas[$j][0]."</td>";
		
	}
	echo "</tr>";
	for ($i=0;$i<=$ndias;$i++)
	{
		echo "<tr >";
		echo "<td class='encabezadoTabla' align='left' nowrap='nowrap'> ".$vecdia[$i][1]." - ".$vecdia[$i][2]." </td>";
		
			if (is_int ($i/2))
			    {
				 $wcf="fila1";  // color de fondo de la fila
			    }
			    else
			    { 
				 $wcf="fila2"; // color de fondo de la fila
			    }
			for ($s=0;$s<$j;$s++)
			{
				 
			

				 
				 $wcf2=$wcf;
				 if($vecdia[$i][1]=="Domingo")
				 {
						//cambio de color si es domingo
						$wcf2="fondoRojo";
				 }
				  
				   $q =  "SELECT Medno1, Medno2, Medap1, Medap2,Medtel,Medcel,Medbip,Meddoc 
						   FROM ".$wmovhos."_000125, ".$wmovhos."_000065, ".$wmovhos."_000048 
						   WHERE Agefec ='".$vecdia[$i][3]."'
						   AND Agetur ='".$rondas[$s][1]."' 
						   AND Ageces ='".$wespecialidad."'
						   AND Esmndo = Agecme    
						   AND Esmcod = Ageces
						   AND Esmest ='on'
						   AND Meddoc = Esmndo    
						   AND Medtdo = Esmtdo    
						   AND Medest = 'on'
					  GROUP BY Agecme;  ";
	
				   $qres = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				   $qnum = mysql_num_rows($qres);

					if ($qnum != 0)
					{
					// $qrow = mysql_fetch_array($qres);
					 $td = "<td align='Justify' class='".$wcf2." replaceclass' nowrap='nowrap' >";
					 $clases = '';
						while($qrow = mysql_fetch_array($qres))
						{
							
							 $td .= "<div nombremed  class='clas".$qrow['Meddoc']."' >";
							 $td .= "".$qrow['Medno1']." ".$qrow['Medno2']." ".$qrow['Medap1']." ".$qrow['Medap2']."";//solo sirve por ahora
							 $td .= "<br>";
							 $td .= "<div class='telefono'>";
							 if ($qrow['Medtel']!='')
							 {
								$td .= "Tel: ".$qrow['Medtel']."";
								$td .= "<br>";
							 }
							 if ($qrow['Medcel']!='')
							 {
								$td .= "Cel: ".$qrow['Medcel']."";
								$td .= "<br>";
							 }
							 if($qrow['Medbip']!='')
							 {
								$td .= "Bip:".$qrow['Medbip']."";
								$td .= "<br>";
							 }
							 $td .= "</div>";							 
							 $td .= "</div>";
							
						}
						
						echo $td;
					 echo "</td>";
				    
					}
					else
				    {
						echo "<td align='right' class='".$wcf2."'></td>";
				    }
				 
			}
		
		echo "</tr>";
	}
	
	echo "</table>";
	
	//----------
}

function llenaTurnos ($wespecialidad,$conex)
{
	
	global $wmovhos;
    global $wemp_pmla;
	$qron = " SELECT Turnom, Turcod "
			."  FROM ".$wmovhos."_000124, ".$wmovhos."_000126 "
			." WHERE Turtur = Turcod "
			."   AND Turesp = '".$wespecialidad."' "
			."  ORDER BY Turord"; 
			  
	$resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
	$numron = mysql_num_rows($resron);
	//$rowlron = mysql_fetch_array($resron);

	if ($numron == 0){
	 $qron = " SELECT Turnom, Turcod "
			."  FROM ".$wmovhos."_000124, ".$wmovhos."_000126 "
			." WHERE Turtur = Turcod "
			."   AND Turesp = '*' "
			."  ORDER BY Turord"; 
			  
	 $resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
	 $numron = mysql_num_rows($resron);
	 
	 for($f=0;$f<$numron;$f++)
	  {
		$rowron = mysql_fetch_array($resron);
		//en las posiciones [*][0] se almacena el nombre
		$rondas[$f][0] = $rowron['Turnom'] ;
		//en las posiciones [*][0] se almacena el codigo
		$rondas[$f][1] = $rowron['Turcod'] ;
	  }

	}
	else
	{
	  for($f=0;$f<$numron;$f++)
	  {
		$rowron = mysql_fetch_array($resron);
		//en las posiciones [*][0] se almacena el nombre
		$rondas[$f][0] = $rowron['Turnom'] ;
		//en las posiciones [*][0] se almacena el codigo
		$rondas[$f][1] = $rowron['Turcod'] ;
	  }

	}
	return($rondas);
}

function numTurnos($wespecialidad,$conex)
{

	
	global $wmovhos;
    global $wemp_pmla;
	$qron = " SELECT Turnom, Turcod "
			."  FROM ".$wmovhos."_000124, ".$wmovhos."_000126 "
			." WHERE Turtur = Turcod "
			."   AND Turesp = ".$wespecialidad." "
			."  ORDER BY Turord"; 
			  
	$resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
	$numron = mysql_num_rows($resron);
	//$rowlron = mysql_fetch_array($resron);

	if ($numron==0)
	{
		$qron = " SELECT Turnom, Turcod "
				."  FROM ".$wmovhos."_000124, ".$wmovhos."_000126 "
				." WHERE Turtur = Turcod "
				."  AND Turesp = '*' "
				." ORDER BY Turord"; 
				  
		$resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
		$numron = mysql_num_rows($resron);

	}



	return($numron);

}

function festivos($conex)
{
   
   global $wmovhos;
    global $wemp_pmla;
   $q ="SELECT Fecha FROM root_000063";
   $qres = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $qnum = mysql_num_rows($qres);
   $festivo = array();
   
   for($f=0;$f<$qnum;$f++)
   {
      $qrow = mysql_fetch_array($qres);
	  //$festivo = array( $qrow['Fecha'] => $qrow['Fecha']);
	  $festivo[ $qrow['Fecha'] ] = $qrow['Fecha'];
	  
   }
   return($festivo);
}
?>

<html>
<head>
<title>AGENDA DE ESPECIALISTAS URGENCIAS</title>

<script type="text/javascript">

var celda_ant;

celda_ant="";
celda_ant_clase="";

function ilumina(celda,clase){
	if (celda_ant=="")
	{
		celda_ant = celda;
		celda_ant_clase = clase;
	}
	celda_ant.className = celda_ant_clase;
	celda.className = 'fondoAmarillo';
	celda_ant = celda;
	celda_ant_clase = clase;
}
function checkverno(ele)
{
		ele = jQuery(ele);
		if(ele.is(':checked')){
			$('.telefono').show();
		}else
		{
			$('.telefono').hide();
		}
		
}

function ilumina2(ele)
{
	ele = jQuery(ele);
	
	//alert(ele.val());
	//$('#treporte .fondoAmarillo').removeClass('fondoAmarillo');
	$('#treporte tbody td div').css('background-color','');

	//var clase = ele.attr("class");
	//clases = clase.split(" ");
	if(ele.val()=='todo')
	{
	$('[nombremed]').show();
	}else
	{
		$('[nombremed]').not('.clas'+ele.val()).hide(); 
					//$('.'+clases[i]).addClass('fondoAmarillo');
					//$('.clas'+ele.val()).css('background-color','#FFFFCC');
		$('.clas'+ele.val()).show();
	}	
}


function valida_envio(form)
{
	//validacion de fechas
	if(document.getElementById('wfecha_f').value < document.getElementById('wfecha_i').value ) 
	{
	   alert("La fecha final debe ser mayor o igual a la fecha inicial");
	   return false;
	}
	
	
}


// Esta funcion llena el parametro ConsultaAjax, recibe y luego manda los parametros: codigo de medico, codigo de especialidad, codigo de fonda 
// y la fecha(años, mes, dia) utilizando ajax



function retornar(wemp,fecha)
{
	location.href = "Consul_disponibilidad_especialidad.php?wemp_pmla="+wemp+"&wfecha_f="+fecha;
}

</script>
  
</head>

<body>

<?php
  /******************************************************
   *   		MOVIMIENTOS DE PACIENTES POR ESTUDIO   		*
   ******************************************************/
	/*
	 ********** DESCRIPCIÓN *****************************************************************************
	 * Muestra una cuadrícula con la lista de conceptos (filas) y frecuencias (columnas) del estudio 	*
	 * de modo que se pueda grabar la cantidad de frecuencias por concepto y paciente y también se 		*
	 * pueda cheqear si se ha realizado ese concepto en esa frecuencia para determinado paciente		*
	 ***************************************************************************************************/
	 /*
	 ********** MODIFICACIONES *******************************************************************************************************
	 * 2019-09-16	Jessica Madrid Mejía 	- Se modifican los queries que obtienen los médicos con turnos para que la relación con 
	 * 										  especialidad se consulte en movhos_000065 y no en movhos_000048.
	 *********************************************************************************************************************************/
	 include_once("root/comun.php");
  

	@session_start();
	 $wemp_pmla;
	 $wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	
// Inicia la sessión del usuario
if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
// Si el usuario no está registrado muestra el mensaje de error
if(!isset($_SESSION['user']))
	echo "error";
else	// Si el usuario está registrado inicia el programa
{	            
 	
 
  
  // Obtengo los datos del usuario
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
 
  // Aca se coloca la ultima fecha de actualización
  $wactualiz = " 2019-09-16";
                                                   
  echo "<br>";				
  echo "<br>";
} 

//**********************************************//
//********** P R I N C I P A L *****************//
//**********************************************//

// Si no existe la variabel consultaAjax se pinta el programa, Si existe el guarda lo agendado
if(!isset($consultaAjax))
{
  $titulo = "CONSULTA DISPONIBILIDAD ESPECIALIDAD";
  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz, "clinica"); 

  // si existe especialidad ya cargada se pinta la tabla que representa la agenda

 if (!isset($wespecialidad))
 {
		// se inicializan las variables de las fechas inicial y final
	   if(!isset($wfecha_f ))
	   {
		  
		  $wfecha_f = date("Y-m-d");
	   }
		   
	   if(!isset($wfecha_i ) )
	   {
	      $wfecha_i = date("Y-m-d");
		
	   }


		// formulario de consulta, el usuario debe seleccionar la especialidad fecha fin y fecha inicio, y asi se distribuye la agenda
		// por especialidad.
			
		echo "<form name='form' id='form' action='' method='post' onSubmit='return valida_envio(this);'>";
			echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';
 
		
		$user_session = explode('-',$_SESSION['user']);
		$user_session = $user_session[1];
		
		$user_session = explode('-',$_SESSION['user']);
	    $user_session = $user_session[1];
	
	$q2=" SELECT Espcod "
		."  FROM ".$wmovhos."_000044 "
	//	." WHERE Espupd LIKE '%".$user_session."%' ";
		." WHERE  Esphdi = 'on'";
		
		$res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
		$vectorespecialidades = array();
		$t=0;
		while($row2 = mysql_fetch_array($res2))
		{
			$vectorespecialidades[$t] = $row2['Espcod'];
			$t++;
		}
		if($t==0)
		{
			echo "<br><br><br><br>";
			echo "<table align='center' ><tr><td><b>No tiene asignada niguna Especialidad</b></td></tr></table>";
		}
		else
		{
		
			echo "<table align='center' cellspacing='5' class='fila2'>";
			$q= "SELECT  Espcod, Espnom "
			 ."    FROM  ".$wmovhos."_000044 "
			 ."   WHERE  Esphdi = 'on'"
			 //."   WHERE  Espcod IN ('".implode("','",$vectorespecialidades)."') "
			 ."ORDER BY  Espnom ";
			
			$resesp = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num1 = mysql_num_rows($resesp);

			
		   
			echo "<tr><td align='center' colspan='2' height='40' ><b> ESPECIALIDAD: </b></td></tr>";
			echo "<tr>";
			echo "<td  colspan='2' align='center' ><select name='wespecialidad' id='wespecialidad'>";
		  
			for($f=0;$f<$num1;$f++)
			{
			  $rowesp = mysql_fetch_array($resesp);
			  echo "<option value = '".$rowesp[0]."'>".$rowesp[1]."</option>";
			}
		  
			echo "</select></td><td></td></tr>";
			echo "<tr><td align='center' colspan='2' height='40' ><b> Rango de Fechas: </b></td></tr>";
			echo "<tr>";
			echo "<td align=right><b>Fecha Inicial: </b>";
			campofechaDefecto("wfecha_i",$wfecha_i);
			echo "</td>";
			echo "<td align=right><b>Fecha final: </b>";
			campofechaDefecto("wfecha_f",$wfecha_f);
			echo "</td>";
			echo "</tr>";
			echo"<tr><td  colspan='2' align=center  height='40'><input type='submit' value='ACEPTAR'><input type='button' value='CERRAR' onclick='javascript:window.close();'></td></tr></form>";
			echo "</table>";
		}
		echo "</form>";
	  // termina formulario de consulta.
 }
  else
 {
 	PintaTabla($conex,$wespecialidad,$wfecha_i,$wfecha_f);
 }

}
else
{
  

  

  grabar_dato($conex,$wespecialidad,$wmedico,$wronda,$wfechagraba);  
} 






?>

</body>
</html>