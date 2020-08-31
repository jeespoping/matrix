<?php
include_once("conex.php"); if(!isset($consultaAjax)) { ?>
<html>
<head>
<title>Programacion disponibilidad especialidad</title>

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

function guarda_valor(cmedico,cespecialidad,cronda,cfecha,idcheck,wemp_pmla)
{
	//alert (cfecha);
	var parametros = "";
    parametros = "consultaAjax=guardaValor&wespecialidad="+cespecialidad+"&wmedico="+cmedico+"&wronda="+cronda+"&wfechagraba="+cfecha+"&wemp_pmla="+wemp_pmla;
	//alert(parametros);

	try
	{
		var ajax = nuevoAjax();


		ajax.open("POST", "Program_disponibilidad_especialidad.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		//ajax.onreadystatechange = grabar_dato();


		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				//alert( "::"+ajax.responseText +"::");
				if(ajax.responseText !='' )
				{
				    //alert('window.document.form.'+idcheck+'.checked=true');
					//idcheck.checked=false;
					document.getElementById(idcheck).checked=false;
					alert(ajax.responseText);




				//alert("hey");
				}
			}
		}
		if ( !estaEnProceso(ajax) )
		{
			ajax.send(null);
		}
	}catch(e){	}
}

function retornar(wemp,fecha)
{
	location.href = "Program_disponibilidad_especialidad.php?wemp_pmla="+wemp+"&wfecha_f="+fecha;
}

</script>

</head>

<body>

<?php
}
  /******************************************************
   *   		AGENDA DE TURNOS POR ESPECIALIDAD    		*
   ******************************************************/
	/*
	 ********** DESCRIPCIÓN *****************************************************************************
	 * Por medio de la visualizacion de una cuadricula que indica en sus columnas los dias con sus  	*
	 * respectivas fechas y en sus filas las rondas asociadas a cada especialidad, esto con el fin		*
	 * de generar una agenda para saber que Medico esta en un dia particular y que turno esta atendiendo*
	 ****************************************************************************************************
	 * Autor: Felipe Alvarez Sanchez				    *
	 * Fecha creacion: 2012-04-30						*
	 *****************************************************/
	/**
	 * ACTUALIZACIONES
	 * 2017-12-06 Edwar Jaramillo:
	 * 		De la modificación anterior se corrigió que se agrupen los médicos por tipo de documento y cédula pues en movhos_48 aún puede haber
	 * 		dobles registros activos para un mismo médico y eso puede hacer que aparezca repetido en la lista de programación de disponibilidad.
	 * 2017-11-30 Edwar Jaramillo:
	 * 		Se modifica la función encargada de consultar los especialistas que tienen una determinada especialidad,
	 * 		movhos_48 se está actualizando para que solo quede un registro por médico y en movhos_65 quedan todas las especialidades por médico.
	 */
	@session_start();
	 include_once("root/comun.php");
	 

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
	  $wactualiz = " mayo 2 de 2012";
	}
//**********************************************//
//********** P R I N C I P A L *****************//
//**********************************************//
// Si no existe la variabel consultaAjax se pinta el programa, Si existe el guarda lo agendado
if(!isset($consultaAjax))
{
  $titulo = "PROGRAMACION DISPONIBILIDAD ESPECIALIDAD";
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

	$q2=" SELECT Espcod "
		."  FROM ".$wmovhos."_000044 "
		." WHERE Espupd LIKE '%".$user_session."%' ";

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
		 ."   WHERE  Esphdi = 'on' "
		 ."     AND  Espcod IN ('".implode("','",$vectorespecialidades)."') "
		 ."ORDER BY  Espnom ";

		$resesp = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num1 = mysql_num_rows($resesp);


		echo "<tr rowspan='3'>";
		echo "<td align='center' ></td>";
		echo "<tr><td align='center' colspan='2' height='40' ><b> ESPECIALIDAD: </b></td></tr>";
		echo "</tr><tr>";
		echo "<td align='center' colspan='2' ><select name='wespecialidad' id='wespecialidad'>";

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




grabar_dato($conex,$wespecialidad,$wmedico,$wronda,$wfechagraba,$wemp_pmla);
}


//**********************************************//
//*************** FUNCIONES*********************//
//**********************************************//

//---------------------------------------------------------------------------------------------------------------------------
//Funcion que llena el vector de dias, en la primera posicion de la matriz [*][1] van las iniciales de los dias
// y en la posicion [*][2] va el numero del dia
function llenaVectorFecha($wfecha_i,$ndias)
{
global $wmovhos;
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

	$qesp= "SELECT Espnom "
	   ."  FROM  ".$wmovhos."_000044 "
	   ." WHERE  Espcod = '".$wespecialidad."' ";

	$resp = mysql_query($qesp,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qesp." - ".mysql_error());
	$rowsesp = mysql_fetch_array($resp);


	//Codigo que pinta la tabla
	// Empieza el encabezado
	echo "<form name='form' id='form' action='' >";
	echo "<table align='center' cellspacing='2'>";
	echo "<tr class='encabezadoTabla'>";
	echo "<td align='center' rowspan='4'> ESPECIALISTAS ".$rowsesp['Espnom']." </td>";
	echo "<td align='center' rowspan='4'> Turnos </td>";
	echo "<td align='center' colspan='".($ndias + 1 )."' height='21'>Rango de Fechas</td>";
	echo "</tr>";
	echo "<tr class='encabezadoTabla'>";
	//for para la letra inicial del dia
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

	//consulta que trae los nombres de los especialistas segun lo seleccionado

	$qlis ="SELECT  mv48.Medno1, mv48.Medno2, mv48.Medap1, mv48.Medap2, mv48.Meddoc
			FROM    {$wmovhos}_000065 AS mv65
			        INNER JOIN
			        {$wmovhos}_000048 AS mv48 ON (mv65.Esmtdo = mv48.Medtdo AND mv65.Esmndo = mv48.Meddoc AND mv48.Medest = 'on' AND mv48.Medhdi = 'on')
			WHERE   mv65.Esmcod LIKE '%{$wespecialidad}%'
			GROUP BY mv65.Esmtdo, mv65.Esmndo
			ORDER BY mv48.Medno1, mv48.Medno2, mv48.Medap1";

	$reslis = mysql_query($qlis,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlis." - ".mysql_error());


	// en esta variable se almacenan turnos
	$turnos = llenaTurnos($wespecialidad,$conex);
	$numtur = numTurnos($wespecialidad,$conex);

	$festivo = festivos($conex);


	// $j variable para controlar el estilo de cada fila
	$j=1;


// empieza la estructura central de la tabla
	while ($resulSelect1 = mysql_fetch_array($reslis))
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
	   $nomcompleto="".$resulSelect1 ['Medno1']." ".$resulSelect1 ['Medno2']." ".$resulSelect1 ['Medap1']." ".$resulSelect1 ['Medap2']."";
	   echo "<td rowspan='".($numtur + 1 )."' ><b>".$resulSelect1 ['Medno1']." ".$resulSelect1 ['Medno2']." ".$resulSelect1 ['Medap1']." ".$resulSelect1 ['Medap2']."</b> </td>";


	   for ($l=0; $l<$numtur; $l++)
	   {
		 echo "<tr class='".$wcf."' onclick='ilumina(this,\"".$wcf."\")'>";
		 echo "<td align='right' >".$turnos[$l][0]."</td>";//solo sirve por ahora

		 for ($k=0;$k<=$ndias;$k++)
		  {
			$wcf2="";
			if(@$festivo[$vecdia[$k][3]])
			{
				$wcf2="fondoRojo";
			}
			if($vecdia[$k][1]=="Domingo")
			{
				//cambio de color si es domingo
				$wcf2="fondoRojo";
			}
			$auxresulselect1= $resulSelect1 ['Meddoc'];
			$auxrondas = $turnos[$l][1];
			// se hace llamado a la funcion cargadatos para saber si en esta posicion el checkbox debe estar seleccionado o no
			$checkeado = cargardatos($auxresulselect1,$auxrondas,$vecdia[$k][3],$conex,$wespecialidad);

			if ($checkeado==1)
			{
				echo "<td align='center' class='".$wcf2."' title='".$nomcompleto."' ><input type='checkbox' title='".$turnos[$l][0]."' name='".$vecdia[$k][3]."-".$resulSelect1 ['Meddoc']."-".$wespecialidad."-".$turnos[$l][1]."' id='".$vecdia[$k][3]."-".$resulSelect1 ['Meddoc']."-".$wespecialidad."-".$turnos[$l][1]."' value='' onclick='guarda_valor(\"".$resulSelect1 ['Meddoc']."\",\"".$wespecialidad."\",\"".$turnos[$l][1]."\",\"".$vecdia[$k][3]."\",\"".$vecdia[$k][3]."-".$resulSelect1 ['Meddoc']."-".$wespecialidad."-".$turnos[$l][1]."\",\"".$wemp_pmla."\")'  checked /></td>";
			}
			else
			{
				echo "<td align='center' class='".$wcf2."' title='".$nomcompleto."'><input type='checkbox' title='".$turnos[$l][0]."' name='".$vecdia[$k][3]."-".$resulSelect1 ['Meddoc']."-".$wespecialidad."-".$turnos[$l][1]."' id='".$vecdia[$k][3]."-".$resulSelect1 ['Meddoc']."-".$wespecialidad."-".$turnos[$l][1]."' onclick='guarda_valor(\"".$resulSelect1 ['Meddoc']."\",\"".$wespecialidad."\",\"".$turnos[$l][1]."\",\"".$vecdia[$k][3]."\",\"".$vecdia[$k][3]."-".$resulSelect1 ['Meddoc']."-".$wespecialidad."-".$turnos[$l][1]."\",\"".$wemp_pmla."\")'/></td>";
			}
		  }

		  echo "</tr>";
		}
		echo "</tr>";


	}

	echo"<tr class='".$wcf."'><td  colspan='".($ndias + 3 )."' align='center'><input type='button' value='RETORNAR' onclick='retornar(\"".$wemp_pmla."\", \"".$wfecha_f."\")'><input type='button' value='CERRAR' onclick='javascript:window.close();'></td>";
	echo "</table>";
	echo "</form>";
}

// Esta funcion se encarga de grabar el codigo de medico, el codigo de la especialidad, el codigo de la ronda y la fecha
// antes de esto hace un select en la posicion de interes para ver si tiene que insertar o eliminar
function grabar_dato($conex,$wespecialidad,$wmedico,$wronda,$wfechagraba)
{

global $user;
$cod = explode('-',$user);
global $wmovhos;
    //consulta que trae el numero de disponibilidad por especialidad
	$qconnesp ="SELECT Espndi "
	         ."   FROM ".$wmovhos."_000044 "
		     ."  WHERE Espcod = '".$wespecialidad."' ";



	$res_connesp = mysql_query($qconnesp,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qconnesp." - ".mysql_error());
	$num_connesp = mysql_num_rows($res_connesp);
	$row_connesp = mysql_fetch_array($res_connesp);
	//variable con el numero de disponibilidades por especialidad
	$numdisp= $row_connesp['Espndi'];


	//
	$qconnesp = "SELECT Agecme,Ageces,Agetur,Agefec "
	          ."   FROM ".$wmovhos."_000125 "
		      ."  WHERE Ageces = '".$wespecialidad."' "
		      ."    AND Agetur = '".$wronda."' "
		      ."    AND Agefec = '".$wfechagraba."' ";

	$res_connesp = mysql_query($qconnesp,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qconnesp." - ".mysql_error());
	//variable con el numero de disponibilidades por especialidad que ya han sido guardadas
	$num_connesp = mysql_num_rows($res_connesp);



	$qcon = "SELECT Agecme,Ageces,Agetur,Agefec "
	      ."   FROM ".$wmovhos."_000125 "
		  ."  WHERE Agecme = '".$wmedico."' "
		  ."    AND Ageces = '".$wespecialidad."' "
		  ."    AND Agetur = '".$wronda."' "
		  ."    AND Agefec = '".$wfechagraba."' ";

	$res_consulta = mysql_query($qcon,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcon." - ".mysql_error());
	$num_consulta = mysql_num_rows($res_consulta);


	if($num_consulta==0 )
	{
	  //agrego


	  if($num_connesp<$numdisp)
	  {
		 $fecha= date("Y-m-d");
		 $hora = date("H:i:s");
	     $qgra = " INSERT INTO ".$wmovhos."_000125  (Agecme,Ageces,Agetur,Agefec,Fecha_data,Hora_data,Seguridad) "
	           ."       VALUES ('".$wmedico."','".$wespecialidad."','".$wronda."', '".$wfechagraba."','".$fecha."','".$hora."','".$cod[1]."') ";

	     $res_graba = mysql_query($qgra,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qgra." - ".mysql_error());

		$qgra= str_replace("'","",$qgra);

		 $qlog = "INSERT INTO ".$wmovhos."_000154 "
				."          ( Logtip , "
				."			  Logmov , "
				."			  Logusu , "
				."			  Logdes , "
				."			  Logest , "
				."			  Seguridad, "
				."			  Fecha_data, "
				."			  Hora_data, "
				."    		  Medico )"
				."   	VALUES "
				."			( 'Programacion Disponibilidad' ,"
				."			  'Inserccion' ,"
				."			  '".$cod[1]."' ,"
				." 			  '".$qgra."' , "
				."			  'on' , "
				."			  'C-".$wmovhos."',"
				."			  '".$fecha."', "
				."			  '".$hora."' ,"
				."			  '".$wmovhos."' )";

				$res_grabalog = mysql_query($qlog,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlog." - ".mysql_error());


	  }
	  else
	 {
	     echo "ya fue asignado esta disponibilidad a otro Medico";
	 }

	}
	else
    {
	  $qbor = " DELETE FROM ".$wmovhos."_000125  "
	         ."  WHERE Agecme = '".$wmedico."' "
		     ."    AND Ageces = '".$wespecialidad."' "
		     ."    AND Agetur = '".$wronda."' "
		     ."    AND Agefec = '".$wfechagraba."' ";

	  $res_borra = mysql_query($qbor,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qbor." - ".mysql_error());

		$qbor= str_replace("'","",$qbor);
	    $fecha= date("Y-m-d");
	    $hora = date("H:i:s");

		 $qlog = "INSERT INTO ".$wmovhos."_000154 "
				."          ( Logtip , "
				."			  Logmov , "
				."			  Logusu , "
				."			  Logdes , "
				."			  Logest , "
				."			  Seguridad, "
				."			  Fecha_data, "
				."			  Hora_data, "
				."    		  Medico )"
				."   	VALUES "
				."			( 'Programacion Disponibilidad' ,"
				."			  'Borrado' ,"
				."			  '".$cod[1]."' ,"
				." 			  '".$qbor."' , "
				."			  'on' , "
				."			  'C-".$wmovhos."',"
				."			  '".$fecha."', "
				."			  '".$hora."' ,"
				."			  '".$wmovhos."' )";

				$res_grabalog = mysql_query($qlog,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlog." - ".mysql_error());



	}

}

function cargardatos($auxresulselect1,$auxrondas,$vecdia,$conex,$wespecialidad)
{

   global $wmovhos;
   $qmedron = "	SELECT Agecme,Ageces,Agetur,Agefec "
			 ."   FROM ".$wmovhos."_000125  "
			 ."  WHERE Agecme = '".$auxresulselect1."' "
			 ."    AND Agetur = '".$auxrondas."' "
			 ."    AND Ageces = '".$wespecialidad."' "
			 ."    AND Agefec = '".$vecdia."' ";

   $resmedron = mysql_query($qmedron ,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qmedron." - ".mysql_error());
   $nummedron= mysql_num_rows($resmedron);

   if ($nummedron!=0)
	 {
	   $esta=1;
	 }
   else
     {
	   $esta=0;
	 }
   return($esta);

}

function llenaTurnos ($wespecialidad,$conex)
{
	global $wmovhos;
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
  $qron ="  SELECT Turnom, Turcod "
          ."   FROM ".$wmovhos."_000124, ".$wmovhos."_000126 "
          ."  WHERE Turtur = Turcod "
		  ."    AND Turesp = ".$wespecialidad." "
		  ."  ORDER BY Turord";

   $resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
   $numron = mysql_num_rows($resron);



   if ($numron==0)
   {
      $qron = " SELECT Turnom, Turcod "
              ."  FROM ".$wmovhos."_000124, ".$wmovhos."_000126 "
              ." WHERE Turtur = Turcod "
		      ."   AND Turesp = '*' "
		      ." ORDER BY Turord";

       $resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
       $numron = mysql_num_rows($resron);

    }

	return($numron);

}

function festivos($conex)
{
   global $wmovhos;
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
<?php if(!isset($consultaAjax)) { ?>
</body>
</html>
<?php } ?>