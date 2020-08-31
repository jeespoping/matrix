<?php
include_once("conex.php");
/* * * * * * * * * * * * * * * * * * * * *   Modificaciones   * * * * * * * * * * * * * * * * * * * * * * *
 2019-06-28  -Edwin MG.	Se cambian funciones split de php por explode
 2019-06-27  -Edwin MG.	Se valida en javascript que no se pueda ingresar en las notas de las evaluaciones espacios
 2018-07-11  -Juan Felipe Balcero L. Se agrega un filtro de estado en la consulta de los descriptores de las encuestas
			  para prevenir un error al desactivar un item, sin eliminarlo primero del formulario.
 2018-06-12  -Arleyda Insignares C.  Se modifica consulta para contar el numero de evaluaciones programadas y 
              garantizar que el insert a la tabla talhuma_000058, no supere el rango permitido según el
              tipo de evaluación ( 4 evaluaciones para renovación de contrato).
 
 2017-09-02  -Arleyda Insignares C.  Se modifica manejo del formato para grabar en talhuma_000058.
              Se adicionan periodos anteriores pendientes por evaluación.
              Se retira el filtro empresa para la tabla usuarios.
              
 2016-11-11  -Arleyda Insignares C.  Se coloca una excepcion en la Funcion 'grabadato', para que permite
              diligenciar: el contrato si se prorroga sin necesidad de diligenciar observaciones.
              
 2016-10-10  -Arleyda Insignares C.  Se cambia el campo Centro de Costos tipo 'Select' por un 'Autocomplete'
 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


include_once("funciones_talhuma.php");

$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

function restarmes($ano_ano,$mes_periodo, $tipo,$conex,$wbasedato)
{
    $q =  "SELECT Perano, Perper, Perest "
				."  FROM ".$wbasedato."_000009 "
				." WHERE Perfor = '".$tipo."' "
				." ORDER BY (Perano * 1) , (Perper * 1) " ;

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$vecmesper = '';

	$i = 1;
	while ($row = mysql_fetch_array($res))
	{
			if($row['Perano'] <= $ano_ano && $row['Perper'] <= $mes_periodo  )
			{
				$vecmesper = $row['Perano']."-".$row['Perper'];
			}
	}
	return ($vecmesper);
}

function getOptionsUserspropio($wemp_pmla, $conex, $wbasedato, $id_padre, $especifico = '', $scc = '',$wformulario, $wgcompetencia,$wcompetencia,$descriptor)
{
    $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($id_padre))));
    $buscaNombre = strtoupper(strtolower($buscaNombre));

    $q = "  SELECT  Ideuse AS codigo, Ideno1 AS nombre1, Ideno2 AS nombre2, Ideap1 AS apellido1, Ideap2 AS apellido2
            FROM    talhuma_000013
            WHERE   Ideest = 'on'
                    AND (CONCAT (Ideno1,' ',Ideno2,' ',Ideap1,' ',Ideap2) LIKE '%".$buscaNombre."%')
                    OR Ideuse  LIKE '".$buscaNombre."'
            ORDER BY    Ideno1, Ideno2, Ideap1, Ideap2";

    $res = mysql_query($q,$conex);

    $options = '';
    if($especifico == '') { $options = '<option value="" >Seleccione..</option>'; }

    if($especifico != '')
    {
        if(mysql_num_rows($res) > 0)
        {
            $row = mysql_fetch_array($res);
            $n_empleado = strtoupper(strtolower(trim($row['nombre1'].' '.$row['nombre2'].' '.$row['apellido1'].' '.$row['apellido2'])));
            $options = $row['codigo'].' - '.(strtoupper(strtolower($n_empleado)));
            $options = "
                    <div id='div_ckc_user_".$row['codigo']."-".$scc."' class='fila2' style='border-top: 2px solid #ffffff;'>
                        <input type='checkbox' id='wuse_pfls_".$row['codigo']."-".$scc."' name='wuse_pfls_chk[".$row['codigo']."]' value='".$row['codigo']."' checked='checked' onClick='desmarcarRemover(\"wuse_pfls_".$row['codigo']."-".$scc."\",\"div_ckc_user_".$row['codigo']."-".$scc."\",\"div_load_chk_users\",\"".$scc."\",\"".$wformulario."\", \"".$wgcompetencia."\",\"".$wcompetencia."\",\"".$scc."\",\"".$row['codigo']."\");' >&nbsp;".$options."
				   </div>";
        }
        else
        {
            $options = '';
        }
    }
    else
    {
        while($row = mysql_fetch_array($res))
        {
            $n_empleado = strtoupper(strtolower(trim($row['nombre1'].' '.$row['nombre2'].' '.$row['apellido1'].' '.$row['apellido2'])));
            $options .= '<option value="'.$row['codigo'].'" >'.$row['codigo'].' - '.($n_empleado).'</option>';
        }
    }
    return $options;
}

if(isset($consultaAjax) AND $consultaAjax == 'load_chk_usuario') // Cargo seleccionado
{
	echo getOptionsUserspropio($wemp_pmla, $conex, $wbasedato, $id_padre, 'on', $seccion ,$wformulario, $wgcompetencia,$wcompetencia,$descriptor);
		return;
}
if( isset($woperacion) AND $woperacion=='traecomentario')
{
		$q= " SELECT  Comstr, Comtip"
			."  FROM	".$wbasedato."_000036   "
			." WHERE Comuco= '".$wempleado."' "
			."   AND Comucm= '".$wempleado."' "
			."   AND Comucr= '".$wcalificador."' "
			."	 AND Comfor= '".$wformulario."' "
			."   AND Comgco= '".$wcodigcompetencia."' "
			."   AND Comcom= '".$wcodicompetencia."' "
			."   AND Comdes= '".$wcodidescriptor."' "
			."   AND Comper= '".$wperiodo."' "
			."	 AND Comano= '".$wano."' " ;

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrow = mysql_num_rows($res);

	$row =mysql_fetch_array($res);
	$resultado = $row['Comstr'] ."***".$row['Comtip'];

	echo $resultado;
	return;

}

if( isset($woperacion) AND $woperacion=='ConsultarPeriodosant')
{

	$wanoant= $wano-1;

	$qren= 	"SELECT  Arecdo, Arefor,Areper,Areano,Fordes,Forcod "
			." FROM ".$wbasedato."_000058 "
			." Left join ".$wbasedato."_000032 on Arecdo = Mcauco "
			."  		AND Areper  = Mcaper "
			."  		AND Areano  = Mcaano "
			."  		AND Arefor  = Mcafor "
			." Inner join ".$wbasedato."_000002 on Arefor  = Forcod "
			."WHERE Aretem  = '".$wtipo."' "
			."  AND Arecdo  = '".$wempleado."' "
			."  AND Forabr  = 'on' "
			."  AND Fortip  = '".$wtipo."' "
			."  AND ( ( Areper < ".$wperiodo." And Areano = '".$wano."' ) or ( Areano = '".$wanoant."' ) ) "
			."  AND ".$wbasedato."_000002.Forest='on' "
			."  AND isnull(Mcauco) ";
		
	$resren    = mysql_query($qren,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qren." - ".mysql_error());
	$numrowren = mysql_num_rows($resren);

    echo $numrowren;
	return ; 

}
// programa automaticamente   personal en sus respectivas evaluaciones siguientes
if(isset($woperacion) AND $woperacion =='programacionautomatica')
{

if($wtipoevaluacion =='01')
{

	// consulta el tema actual
	$q  = "SELECT Forcod,Fordes,Forper,Formpe,Fortes,Fortco "
		 ."  FROM ".$wbasedato."_000042 "
		 ." WHERE  Forcod ='".$wptema."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	//----------

	$maxperiodos   = $row['Formpe']; // max de veces por año
	$temasiguiente = $row['Fortes']; // tema siguiente
	$periodicidad  = $row['Forper']; // frecuencia con la que re realiza la evaluacion
	$numfil = 0;
	$wnformato ='';

	// Consulta cuantas veces un empleado a realizado cierta evaluacion
	$q = "SELECT Mcafor, COUNT(*) AS cuantos "
		."  FROM ".$wbasedato."_000032, ".$wbasedato."_000002 "
		." WHERE Mcaucr  = '".$wpcalificador."' "
		."   AND Mcauco  = '".$wpempleado."'  "
		."   AND Mcafor  = Forcod "
		."   AND Fortip  = '".$wptema."' ";
		
	$rescon  = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numfil  = mysql_num_rows($rescon);

    // Si no tiene evaluaciones realizadas se tomará el formato de talhuma_000059
    if ($numfil > 0){
        $rowFo     = mysql_fetch_array($rescon);
 	    $cuantos   = $rowFo["cuantos"];  // numero de veces que ha realizado la evaluacion

 	    if ($rowFo['Mcafor'] != '')
            $wnformato = $rowFo['Mcafor'];   // formato realizado   	
    }


    if ($wnformato == ''){

    	$cuantos   = 0;

		$selectformato = " SELECT Mtecar,Mtefor,Mtecon 
						   FROM ".$wbasedato."_000059 
						   WHERE  Mtecar = '*' ";

		$resfo     = mysql_query($selectformato,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$selectformato." - ".mysql_error());
		$rowfor    = mysql_fetch_array($resfo);

		$wnformato = $rowfor['Mtefor'];
    }

	
	// Consulta Cuantas evaluaciones tiene programadas
    $qEval = "SELECT  Arecdr, Arecdo 
                     FROM ".$wbasedato."_000058 
                     WHERE  Arecdo = '".$wempleado."' 
                       And  Aretem = '".$wptema."' ";

    $resEval = mysql_query($qEval,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$selecteval." - ".mysql_error());
    $numEval = mysql_num_rows($resEval); 
		

  if ($periodicidad !='0')
  {
	// si ha realizado menos de los permitidos
	if($maxperiodos > $cuantos )
	{
		//-----Se calcula el nuevo año y nuevo periodo  en el que se va a programar el empleado
		$wnperiodo = ($wperiodo * 1)  + ($periodicidad * 1);

		 if( $wnperiodo > 12)
		 {
			$wnperiodo = $wnperiodo -12;
			$wnano = $wano + 1;
		 }else
		 {
			$wnano = $wano;
		 }
		//------

		//// Acontinuacion instrucciones para crear el periodo nuevo

		// 1- Se mira si el periodo existe
		$queper = "SELECT COUNT(*) AS cuantos "
				 ."  FROM ".$wbasedato."_000009 "
				 ." WHERE  Perfor = '".$wptema."' "
				 ."   AND  Perano = '".$wnano."' "
				 ."   AND  Perper = '".$wnperiodo."' ";

		$resper = mysql_query($queper,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$queper." - ".mysql_error());
		$rowper = mysql_fetch_array($resper);

		$numrowper = $rowper['cuantos']; // existe o no ?

		// Si no existe
		if  ($numrowper == '0')
		{
				// 2- se inserta en la tabla de periodos el nuevo periodo
				$queryinsert = "  INSERT INTO ".$wbasedato."_000009 "
							  ."    (Medico,Fecha_data,Hora_data, Perfor, Perano, Perper , "
							  ."	 Seguridad, perest ) "
							  ."   VALUES "
							  ." 	('".$wbasedato."' ,'".date("Y-m-d")."','".date("H:i:s")."', '".$wptema."' ,'".$wnano."', '".$wnperiodo."' , "
							  ."     'C-".$wbasedato."' , 'off') ";

				$res = mysql_query($queryinsert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insert En '".$wbasedato."'_000009   ): ".$query." - " . mysql_error());
				$wnfecha=date("Y-m-d");

				// 3- se inserta en la tabla 34 las notas nuevas
				$q = "  INSERT INTO ".$wbasedato."_000034 "
					  ."            (Calmax,Calmin,Calmal,Calbue,Calsob,Calano,Calper,Calfor,Fecha_data,Medico,Seguridad)"
					  ."     VALUES ('".$wncalmax."','".$wncalmin."','".$wncalmal."','".$wncalbue."','".$wncalsob."','".$wnano."','".$wnperiodo."','".$wptema."',  '".$wnfecha."','".$wbasedato."','C-".$wbasedato."')";

				$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				// 4-  se inserta en la tabla 48 el ano el periodo y el tema
				$q = "  INSERT INTO ".$wbasedato."_000048 "
					  ."            (Nxtano,Nxtper,Nxttem,Fecha_data,Medico,Seguridad,Nxtgno)"
					  ."     VALUES ('".$wnano."','".$wnperiodo."','".$wptema."',  '".$wnfecha."','".$wbasedato."','C-".$wbasedato."','1')";

				$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		}

		//// Fin de  instrucciones para crear el periodo nuevo
		//----------

		// Programacion  en arbol de  evaluaciones
		if ($maxperiodos > $numEval ){
			$query = "INSERT INTO ".$wbasedato."_000058
						(	Medico, Fecha_data, Hora_data, Arecdr, Arecdo
							, Aretem, Arefor, Areper, Areano,Areest,Seguridad	)
					 VALUES
						( 	'".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$wpcalificador."','".$wpempleado."' ,
							'".$wptema."' , '".$wnformato."' , '".$wnperiodo."' , '".$wnano."' , 'on','C-1".$wbasedato."' )";

			$res = mysql_query($query,$conex) or die("Error: " . mysql_errno() . " - en el query (Insert En '".$wbasedato."'_000058   ): ".$query." - " . mysql_error());
	    }
		//------------
	}
	else
	{	// si se programa un tema siguiente
	// si hay que programar tema siguiente
	//
	if ($temasiguiente	!='' && $temasiguiente	!='0')
	{
		// se consulta el tema siguiente
		$q  = "SELECT Forcod,Fordes,Forper,Formpe,Fortes,Fortco "
			 ."  FROM ".$wbasedato."_000042 "
			 ." WHERE  Forcod ='".$temasiguiente."'";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($res);

		$maxperiodos  = $row['Formpe']; // maximo de veces
		$periodicidad = $row['Forper']; // frecuencia por año


		//  Se consulta el periodo y el año actual
		$q = "SELECT Perano, Perper "
			."  FROM ".$wbasedato."_000009 "
			." WHERE Perfor = '".$temasiguiente."' "
			."   AND Perest = 'on' ";
		$resper = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($resper);


		$wcalculoperiodo =  $row['Perper']; // periodo actual
		$wcalculoano = $row['Perano']; // año actual

		//---- se calcula  el nuevo año y periodo donde se debe programar al empleado
		$wcalculoperiodo = ($wcalculoperiodo * 1)  + ($periodicidad * 1);

		 if( $wcalculoperiodo > 12)
		 {
			$wcalculoperiodo = $wcalculoperiodo -12;
			$wcalculoano = $wcalculoano + 1;
		 }else
			$wcalculoano = $wcalculoano;

		//-------------------------

		// se consulta el cargo  del empleado
		$traecargo = "SELECT  	Ideccg,Idetco "
					."  FROM talhuma_000013 "
					." WHERE Ideuse = '".$wpempleado."' ";

		$rescar = mysql_query($traecargo,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$traecargo." - ".mysql_error());
		$numrowcar = mysql_num_rows($rescar);
		//------------------------------------------

		if ($numrowcar > 0)
		{

			$row =mysql_fetch_array($rescar);
			$wncargo = $row['Ideccg'];
			$wtipcontrato = $row['Idetco'];

			$selectformato = "SELECT  Mtecar,Mtefor,Mtecon "
					."  FROM ".$wbasedato."_000059 "
					." WHERE  Mtecar = '*' "
					."   AND  Mtetem = '".$temasiguiente."' ";
			$resfor = mysql_query($selectformato,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$selectformato." - ".mysql_error());
			$numrowfor = mysql_num_rows($resfor);
			
			//---------------------------------

			if($numrowfor > 0)
			{
				$rowfor =mysql_fetch_array($resfor);
				$wnformato = $rowfor['Mtefor'];

				// Se inserta el empleado en el nuevo periodo con su respetivo formulario y tema
				if ($maxperiodos > $numEval ){
					$query = "INSERT INTO ".$wbasedato."_000058
								(	Medico, Fecha_data, Hora_data, Arecdr, Arecdo
									, Aretem, Arefor, Areper, Areano,Areest,Seguridad	)
							 VALUES
								( 	'".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$wpcalificador."','".$wpempleado."' ,
									'".$temasiguiente."' , '".$wnformato."' , '".$wcalculoperiodo."' , '".$wcalculoano."' , 'on','C-2".$wbasedato."' )";

					$res = mysql_query($query,$conex) or die("Error: " . mysql_errno() . " - en el query (Insert En '".$wbasedato."'_000058   ): ".$query." - " . mysql_error());
			    }
				//-------------------------------------------

				//-----------------------------------------
				// Instrucciones para crear nuevo periodo
				// 1- Se mira si el periodo existe
				$queper = "SELECT COUNT(*) AS cuantos "
						 ."  FROM ".$wbasedato."_000009 "
						 ." WHERE  Perfor = '".$temasiguiente."' "
						 ."   AND  Perano = '".$wcalculoano."' "
						 ."   AND  Perper = '".$wcalculoperiodo."' ";
				$resper = mysql_query($queper,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$queper." - ".mysql_error());
				//$numrowper = mysql_num_rows($resper);

				$rowper = mysql_fetch_array($resper);
				$numrowper = $rowper['cuantos'];
				if ($numrowper == '0')
				{
					// 2 -se inserta en la tabla de periodos el nuevo periodo
					$queryinsert = "  INSERT INTO ".$wbasedato."_000009 "
								  ."    (Medico,Fecha_data,Hora_data, Perfor, Perano, Perper , "
								  ."	 Seguridad, perest ) "
								  ."   VALUES "
								  ." 	('".$wbasedato."' ,'".date("Y-m-d")."','".date("H:i:s")."', '".$temasiguiente."' ,'".$wcalculoano."', '".$wcalculoperiodo."' , "
								  ."     'C-".$wbasedato."' , 'off') ";


					$res = mysql_query($queryinsert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insert En '".$wbasedato."'_000009   ): ".$query." - " . mysql_error());
					$wnfecha=date("Y-m-d");

					//3 -se inserta en la tabla 34 las notas nuevas
					$q = "  INSERT INTO ".$wbasedato."_000034 "
						  ."            (Calmax,Calmin,Calmal,Calbue,Calsob,Calano,Calper,Calfor,Fecha_data,Medico,Seguridad)"
						  ."     VALUES ('".$wncalmax."','".$wncalmin."','".$wncalmal."','".$wncalbue."','".$wncalsob."','".$wcalculoano."','".$wcalculoperiodo."','".$temasiguiente."',  '".$wnfecha."','".$wbasedato."','C-".$wbasedato."')";
					$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					//4-  se inserta en la tabla 48 el ano el periodo y el tema
					$q = "  INSERT INTO ".$wbasedato."_000048 "
						  ."            (Nxtano,Nxtper,Nxttem,Fecha_data,Medico,Seguridad,Nxtgno)"
						  ."     VALUES ('".$wcalculoano."','".$wcalculoperiodo."','".$temasiguiente."',  '".$wnfecha."','".$wbasedato."','C-".$wbasedato."','1')";
					$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				}
			//
			}
		}
	}
  }
}

}
return ;
}

if(isset($woperacion) AND $woperacion=='guardacomentario')
{


	$fecha= date("Y-m-d");
	$hora = date("H:i:s");
	$q= " SELECT  Comstr, Comtip"
			."  FROM	".$wbasedato."_000036   "
			." WHERE Comuco= '".$wempleado."' "
			."   AND Comucm= '".$wempleado."' "
			."   AND Comucr= '".$wcalificador."' "
			."	 AND Comfor= '".$wformulario."' "
			."   AND Comgco= '".$wcodigcompetencia."' "
			."   AND Comcom= '".$wcodicompetencia."' "
			."   AND Comdes= '".$wcodidescriptor."' "
			."   AND Comper= '".$wperiodo."' "
			."	 AND Comano= '".$wano."' " ;

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrow = mysql_num_rows($res);

	if ($numrow==0)
	{
			$q	= 	 " INSERT INTO ".$wbasedato."_000036  (Comuco,Comucm,Comucr,Comfor,Comgco,Comcom,Comdes,Comper,Comano,Comstr,Comtip,Fecha_data,Seguridad,Medico) "
						." VALUES ('".$wempleado."', "
						."         '".$wempleado."' , "
						."         '".$wcalificador."' , "
						."         '".$wformulario."',"
						."         '".$wcodigcompetencia."',"
						."         '".$wcodicompetencia."',"
						."         '".$wcodidescriptor."',"
						."         '".$wperiodo."',"
						."         '".$wano."',"
						."         '".$wcomentario."',"
						."         '".$wtipocomentario."',"
						."         '".$fecha."',"
						."         'C-".$wcalificador."',"
						."		   '".$wbasedato."' ) ";

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	}
	else if($wcomentario=='')
	{

		$q= " DELETE "
			."  FROM	".$wbasedato."_000036   "
			." WHERE Comuco= '".$wempleado."' "
			."   AND Comucm= '".$wempleado."' "
			."   AND Comucr= '".$wcalificador."' "
			."	 AND Comfor= '".$wformulario."' "
			."   AND Comgco= '".$wcodigcompetencia."' "
			."   AND Comcom= '".$wcodicompetencia."' "
			."   AND Comdes= '".$wcodidescriptor."' "
			."   AND Comper= '".$wperiodo."' "
			."	 AND Comano= '".$wano."' " ;
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	}
	else
	{
			$q= "UPDATE ".$wbasedato."_000036   "
			   ."   SET Comstr='".$wcomentario."' ,"
			   ."       Comtip='".$wtipocomentario."' "
			   ." WHERE Comuco= '".$wempleado."' "
			   ."   AND Comucm= '".$wempleado."' "
			   ."   AND Comucr= '".$wcalificador."' "
			   ."	AND Comfor= '".$wformulario."' "
			   ."   AND Comgco= '".$wcodigcompetencia."' "
			   ."   AND Comcom= '".$wcodicompetencia."' "
			   ."   AND Comdes= '".$wcodidescriptor."' "
			   ."   AND Comper= '".$wperiodo."' "
			   ."	AND Comano= '".$wano."' " ;

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	}


return;

}

if (isset($woperacion) AND $woperacion=='traenotasanteriores')
{

	$vector_notas = array();
	$q = 	"  SELECT Evacal , Evades, Destip,Evadat
				 FROM ".$wbasedato."_000007 , ".$wbasedato."_000005
			    WHERE Evaevo = '".$wempleado."'
				  AND Evaano = '".$wano."'
				  AND Evaper = '".$wperiodo."'
				  AND Evafco = '".$wformulario."'
				  AND Evades = Descod";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	while($row =mysql_fetch_array($res))
	{

		if( $row['Destip'] == '07' || $row['Destip'] == '03' ||   $row['Destip'] == '08' )
		{
			$vector_notas[$row['Evades']] =$row['Evadat'];
		}
		else
		{
			$vector_notas[$row['Evades']] =$row['Evacal'];
		}
	}

	echo json_encode($vector_notas);
	return;


}

if (isset($woperacion) AND $woperacion=='traenotascomentariosanteriores')
{

	$vector_notas = array();

		$q= " SELECT  Comstr, Comdes"
			."  FROM	".$wbasedato."_000036   "
			." WHERE Comucm= '".$wempleado."' "
			."	 AND Comfor= '".$wformulario."' "
			."   AND Comper= '".$wperiodo."' "
			."	 AND Comano= '".$wano."' " ;

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	while($row =mysql_fetch_array($res))
	{
		$vector_notas[$row['Comdes']] =$row['Comstr'];

	}

	echo json_encode($vector_notas);
	return;


}

if (isset($woperacion) AND $woperacion=='traenotastiposcomentariosanteriores')
{

	$vector_notas = array();

		$q= " SELECT  Comtip, Comdes"
			."  FROM	".$wbasedato."_000036   "
			." WHERE Comucm= '".$wempleado."' "
			."	 AND Comfor= '".$wformulario."' "
			."   AND Comper= '".$wperiodo."' "
			."	 AND Comano= '".$wano."' " ;

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	while($row =mysql_fetch_array($res))
	{
		$vector_notas[$row['Comdes']] =$row['Comtip'];

	}

	echo json_encode($vector_notas);
	return;


}

// Se utilaza para traer notas anteriores
if(isset($form) && $form == 'load_users' && isset($accion) && $accion == 'load')
{
	$params['tabla']='talhuma_000013';
	$params['campo_estado']="Ideest = 'on'";
	$params['campos'][]='Ideuse';
	$params['campos'][]='ideno1';
	$params['campos'][]='ideno2';
	$params['campos'][]='ideap1';
	$params['campos'][]='ideap2';
	echo getOptions($wemp_pmla, $conex, $wbasedato,$params , $id_padre);
	return;
}
if(isset($wnotaant) && $wnotaant == 'si')
{
	$q = "  SELECT Evafco,Evagco,Evacom,Evades,Evacal,Comstr,Comfco,Destip,Evadat"
		  ."  FROM  ".$wbasedato."_000005 ,".$wbasedato."_000007  LEFT JOIN ".$wbasedato."_000036 ON (Evaevo = Comucm  AND Evaevr = Comucr  AND Comper = '".$wperiodo."' AND Comano = '".$wano."' "
		  ." AND Evafco =  Comfor  AND Evagco =  Comgco AND Evacom =  Comcom AND Evades =  Comdes )"
		  ." WHERE Evaevo ='".$wcalificado."' "
	      ."   AND Evaevr ='".$wcalificador2."' "
          ."   AND Evaper ='".$wperiodo."' "
          ."   AND Evaano ='".$wano."' "
		  ."   AND Descod = Evades" ;



	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$veccalant = '||';
	while($row =mysql_fetch_array($res))
	{
		if ($row['Destip']!='03')
		{
			$veccalant .= "-*-".$row['Evafco']."-".$row['Evagco']."-".$row['Evacom']."-".$row['Evades']."-".$row['Evacal']."-".$row['Comstr']."-".$row['Comfco'];
		}
		else
		{
			$veccalant .= "-*-".$row['Evafco']."-".$row['Evagco']."-".$row['Evacom']."-".$row['Evades']."-".$row['Evadat']."-".$row['Comstr']."-".$row['Comfco'];
		}
	}
	$veccalant = str_replace('||-*-','',$veccalant);

	echo $veccalant;
return;
}

if(isset($wnotaanttotal) && $wnotaanttotal == 'si')
{
	$q = "  SELECT Tottot,Fecha_data"
		  ."  FROM  ".$wbasedato."_000035 "
		  ." WHERE Totcdo ='".$wcalificado."' "
	      ."   AND Totcdr ='".$wcalificador2."' "
          ."   AND Totper ='".$wperiodo."' "
          ."   AND Totano ='".$wano."' " ;



	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);

	$totalanterior = $row['Tottot'];
	$fechaanterior = $totalanterior."||".$row['Fecha_data'];
	echo $fechaanterior;
return;
}

// Se utilaza para agregar un compromiso por parte del evaluado
if(isset($compromisos) && $compromisos == 'eliminar')
{
	$elemento= explode("-",$descriptor);
	$q = " 	   DELETE "
            ."   FROM ".$wbasedato."_000036 "
            . " WHERE Comuco='".$wempleado."'"
			. "   AND Comucr='".$wcalificador."'"
			. "   AND Comfor='".$elemento[0]."'"
			. "   AND Comgco='".$elemento[1]."'"
			. "   AND Comcom='".$elemento[2]."'"
			. "   AND Comdes='".$elemento[3]."'";


	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	return;
}
//----------------------------------------------------------

if(isset($compromisos) && $compromisos == 'Ecompromiso')
{
	$elemento= explode("-",$descriptor);
	$des= $elemento[3];
	$com= $elemento[2];

	$q= " SELECT Desdes "
	 . "    FROM ".$wbasedato."_000005 "
	 . "   WHERE Descod='".$des."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$des1 = mysql_fetch_array($res);

	$q= " SELECT Comdes "
	 . "    FROM ".$wbasedato."_000004 "
	 . "   WHERE Comcod='".$com."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$com1 = mysql_fetch_array($res);

	echo"<div id=Ecompromiso-".$descriptor." width='100%' >
		  <table width='100%' >
			<tr>
				<td class='fila1'  width='219'>".($com1['Comdes'])."</td>
				<td class='fila1'  width='254'>".($des1['Desdes'])."</td>
				<td class='fila1'  align='center'  width='252'><textarea  class='compromisos'  id='ecompromiso-".$descriptor."' onchange='grabadato(this)' rows='6' cols='30'  rows='6' cols='30'></textarea></td>
				<td class='fila1' align='center' width='147'><div id='edatepicker-".$descriptor."'></div></td>
			</tr>
		 </table>
		</div>";
	return;
}
//--------------------------------------------------------------------------

//Se Utiliza para agregar el encabezado de los compromisos del evaluado
if(isset($compromisos) && $compromisos == 'Eencabezado')
{
	$elemento= explode("-",$descriptor);
	$des= $elemento[3];
	$com= $elemento[2];

	$q= " SELECT Desdes "
	 . "    FROM ".$wbasedato."_000005 "
	 . "   WHERE Descod='".$des."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$des1 = mysql_fetch_array($res);

	$q= " SELECT Comdes "
	 . "    FROM ".$wbasedato."_000004 "
	 . "   WHERE Comcod='".$com."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$com1 = mysql_fetch_array($res);
	echo'<br>';
	echo'<div id="EcompromisoEncabezado" width="100%" >
			<table width="100%" >
				<tr class="encabezadoTabla">
					<td colspan="4"><div align="center">1.  COMPROMISOS DEL EVALUADO  <br>(Compromisos espec&iacute;ficos por mejorar por parte del evaluado)</div></td>
				</tr>
				<tr>
					<td align = "Left" width="219" class="fila2">Nombre de la competencia</td>
					<td align = "Left" width="254" class="fila2">Descriptor en el que se compromete a mejorar:</td>
					<td align = "Left" width="252" class="fila2">Con que estrateg&iacute;a, &iquest;como se compromete a  Mejorarlo?</td>
					<td align = "Left" width="147" class="fila2">Fecha(D/M/A) De seguimiento</td>
				</tr>
			</table>
		  </div>
		  <div id="Ecompromiso-'.$descriptor.'" width="100%" >
		  <table width="100%" >
				<tr>
					<td align = "Left" class="fila1"  width="219">'.($com1["Comdes"]).'</td>
					<td align = "Left" class="fila1" width="254">'.($des1["Desdes"]).'</td>
					<td align = "Left" class="fila1"  width="252"><textarea class="compromisos"  id="ecompromiso-'.$descriptor.'" onchange="grabadato(this)" rows="6" cols="30"></textarea></td>
					<td align = "Left" class="fila1"  width="147"><div id="edatepicker-'.$descriptor.'"></td>
				</tr>
			</table>
		  </div> ';

	return;
}
//---------------------------------------------------------------------------------------------------------

//Se utiliza Para Agregar los compromisos por parte del calificado
if(isset($compromisos) && $compromisos == 'Ccompromiso')
{
	$elemento= explode("-",$descriptor);
	$des= $elemento[3];
	$com= $elemento[2];

	$q= " SELECT Desdes "
	 . "    FROM ".$wbasedato."_000005 "
	 . "   WHERE Descod='".$des."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$des1 = mysql_fetch_array($res);

	$q= " SELECT Comdes "
	 . "    FROM ".$wbasedato."_000004 "
	 . "   WHERE Comcod='".$com."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$com1 = mysql_fetch_array($res);

	echo"<div id=Ccompromiso-".$descriptor." width='100%' >
		  <table width='100%'>
			<tr>
				<td align = 'Left' class='fila1' width='219'>".($com1['Comdes'])."</td>
				<td align = 'Left' class='fila1' width='254'>".($des1['Desdes'])."</td>
				<td align = 'Left' class='fila1' align = 'Left' width='252'><textarea id='ccompromiso-".$descriptor."' class='compromisos'  onchange='grabadato(this)' rows='6' cols='30'></textarea></td>
				<td class='fila1' align = 'Left' width='147'><div id='cdatepicker-".$descriptor."'></div></td>
			</tr>
		 </table>
		</div>";
	return;
}
//---------------------------------------------------------------------------------------

// se utiliza para agregar el encabezado de los compromisos del evaluado
if(isset($compromisos) && $compromisos == 'Cencabezado')
{
	$elemento= explode("-",$descriptor);
	$des= $elemento[3];
	$com= $elemento[2];

	$q= " SELECT Desdes "
	 . "    FROM ".$wbasedato."_000005 "
	 . "   WHERE Descod='".$des."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$des1 = mysql_fetch_array($res);

	$q= " SELECT Comdes "
	 . "    FROM ".$wbasedato."_000004 "
	 . "   WHERE Comcod='".$com."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$com1 = mysql_fetch_array($res);

    echo'<br>';

	echo'<div id="CcompromisoEncabezado" width="100%" >
		<table width="100%" >
			<tr class="encabezadoTabla">
				<td colspan="4"><div align="center">2. COMPROMISOS DEL EVALUADOR <br>(Como puede apoyar el logro de las actividades o planes trazados, especifique tiempos y estrategias de seguimiento)</div></td>
			</tr>
			<tr>
				<td align = "Left" width="219" class="fila2">Nombre de la competencia</td>
				<td align = "Left" width="254" class="fila2">Descriptor en el que se compromete a mejorar:</td>
				<td align = "Left" width="252" class="fila2">Con que estrateg&iacute;a, &iquest;como se compromete a  Mejorarlo?</td>
				<td align = "Left" width="147"  class="fila2">Fecha(D/M/A) De seguimiento</td>
			</tr>
		</table>
		</div>
		<div id="Ccompromiso-'.$descriptor.'" width="100%" >
		  <table width="100%" >
				<tr>
					<td align = "Left" class="fila1"  width="219">'.($com1["Comdes"]).'</td>
					<td align = "Left" class="fila1"  width="254">'.($des1["Desdes"]).'</td>
					<td align = "Left" class="fila1"  width="252"><textarea id="ccompromiso-'.$descriptor.'"  class="compromisos" onchange="grabadato(this)" rows="6" cols="30"></textarea></td>
					<td align = "Left" class="fila1"  width="147"><div id="cdatepicker-'.$descriptor.'"></td>
				</tr>
			</table>
		 </div>';
	return;
}

if (isset($wenviofirma))
{

 $q = "SELECT * "
	 ."  FROM usuarios "
	 ." WHERE Codigo Like '%".$wcalificado."' "
	 ."   AND Password ='".$wenviofirma."' ";
	 //."   AND Empresa = '".$wemp_pmla."'";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrow = mysql_num_rows($res);

	echo $numrow;
	return;
}

// cambia estado de la encuesta
if(isset($cambiaestadoencuesta) AND $cambiaestadoencuesta=='si' )
{

	$q	= "  UPDATE  ".$wbasedato."_000049 "
		."	    SET	 Encese = '".$wencuestaestado."' ,"
		." 			 Enccom = '".$wencuestacomentario."' "
		."	  WHERE  Enchis = '".$whistoria."' "
		."      AND  Encing = '".$wingreso."' "
		."      AND  Encenc = '".$wencuesta."' "
		."		AND  Encano = '".$wano."' "
		."      AND  Encese !='cerrado'   " ;
	echo $q;
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	return;

}

if(isset($guardaentablaencuesta) AND $guardaentablaencuesta='si')
{
		$fecha= date("Y-m-d");
		$q	= "  UPDATE  ".$wbasedato."_000049 "
		."	    SET	 Encese = 'cerrado', "
		."           Encfce= '".$fecha."' "
		."	  WHERE  Enchis = '".$whistoria."' "
		."      AND  Encing = '".$wingreso."' "
		."      AND  Encenc = '".$wencuesta."' "
		."		AND  Encano = '".$wano."' "
		."      AND  Encper = '".$wperiodo."' ";
		echo $q;
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		return;
}

if(isset($guardaentablaevaluacion) AND $guardaentablaevaluacion='si')
{
		$fecha= date("Y-m-d");
		$q	= "  UPDATE  ".$wbasedato."_000055 "
		."	    SET	Empeve = 'cerrado', "
		."          Empfec = '".$fecha."' "
		."	  WHERE Empcod = '".$wuse."' "
		."      AND Empeva = '".$wformulario."' "
		." 		AND Empper = '".$wperiodo."' "
		."      AND Empano = '".$wano."' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


		return;
}

//--Si es del tipo 05- Evaluacion de invecla, programa automaticamente a la persona para otra encuesta o evaluacion.
//-- sumandole uno al periodo que acaba de hacer.
if(isset($guarda_empleado_tipo_05) AND $guarda_empleado_tipo_05=='si')
{

	$wfecha	=date("Y-m-d");
	$whora	=date("H:i:s");

	$qres	= "  	 SELECT  Encno1, Encno2, Encap1, Encap2  , Enctem"
				."	   FROM   ".$wbasedato."_000049 "
				."	  WHERE  Enchis = '".$whis."' "
				."      AND  Encing = '".$wing."' "
				."      AND  Encenc = '".$wencuesta."' "
				."		AND  Encano = '".$wano."' "
				."      AND  Encper = '".$wperiodo."' ";

	$resres = mysql_query($qres,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qres." - ".mysql_error());

	if($rowres =mysql_fetch_array($resres))
	{

		$wno1 			= $rowres['Encno1'];
		$wno2 			= $rowres['Encno2'];
		$wap1 			= $rowres['Encap1'];
		$wap2 			= $rowres['Encap2'];
		$wtemainterno	= $rowres['Enctem'];
		$wperiodo		= (($wperiodo * 1) +1);

		$q = " INSERT INTO ".$wbasedato."_000049 "
					   . "            ( Medico			,Fecha_data		,	Hora_data		,	Encced		,		Encenc		,	Enchis		,	Encing		,Encno1				,Encno2			,Encap1			,Encap2				,Enceda			,Encent				,Encdia							,Enctel				,Enchab			,Encafi			,Enccco			,		Encfec				,Encest		,Seguridad				,Encano			,Encper				,Enctem					,Encfpr			,Encpob) "
					   . "      VALUES('".$wbasedato."'	,'".$wfecha."'	,	'".$whora."'	,	'".$wced."'	,	'".$wencuesta."',	'".$whis."'	,	'".$wing."'	,'".$wno1."'		,'".$wno2."'	,'".$wap1."'	,'".$wap2."'		,'".$wedad."'	,'".$wentidad."'	,'".trim($wcodentidad)."'		,'".$wtelefono."'	,'".$whcod."'	,'no'			,'".$wcco."'	,		''					,'on'		,'C-".$wbasedato."'		,'".$wano."'	,'".$wperiodo."'	,'".$wtemainterno."'	,'".$fecha."'	,'".$wpublicoobjetivo."')" ;


		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	}
	return;

}

// Actualiza la fecha de cierre a la fecha que este en el campo de la clase  fechaconcierre
// Esto no lo tienen todos los formatos , si el formato lo tiene y es tipo   05- Evaluacion de invecla cambiaria las fechas de hacer la encuesta , por esta fecha
//--------------------------------------------------------------------------------
if(isset($actualiza_empleado_tipo_05) AND $actualiza_empleado_tipo_05=='si')
{
	// Actualiza la tabla 000049  para que la evalucion quede registrada con fecha de lo que este en el campo de fecha con cierre de evaluacion.
	//---------
	$qres	= "  	 UPDATE  ".$wbasedato."_000049 "
				."      SET  Fecha_data ='".$wfecha."' ,
							 Hora_data  ='".date("H:i:s")."' "
				."	  WHERE  Enchis = '".$whis."' "
				."      AND  Encing = '".$wing."' "
				."      AND  Encenc = '".$wencuesta."' "
				."		AND  Encano = '".$wano."' "
				."      AND  Encper = '".$wperiodo."' ";

	$resres = mysql_query($qres,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qres." - ".mysql_error());
	echo $qres;
	return;
}


if(isset($eliminarseleccionado) AND $eliminarseleccionado=='si')
{
		$q	= "  DELETE  "
			 ."	   FROM ".$wbasedato."_000050"
			 ."   WHERE Percal='".$wcalificador."' "
			 ."     AND perhis='".$wempleado."' "
			 ."     AND pering='".$wingreso."' "
			 ."     AND peresc='".$wcalificacion."' "
			 ."     AND perfor='".$wformulario."' "
			 ."     AND pergco='".$wgrupocom."' "
			 ."     AND percom='".$wcomp."' "
			 ."     AND perdes='".$wdes."' "
			 ."     AND Perper='".$wperiodo."' "
			 ."     AND Perano='".$wano."' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		echo $q;
		return;

}

//----------------------------------------------------------------------------------------------

?>
<div id="evaluacioncompetencias" >
<?php if(!isset($consultaAjax) ||  $consultaAjax=='') { ?>
<html>
<head>
<title>Evaluacion de competencias</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
<script type='text/javascript'>
var celda_ant;

celda_ant="";
celda_ant_clase="";

$(document).ready( function () {
		//fecha
		$('#ui-datepicker-div').hide();
		//
		iluminartr();

		// Cuando el campo con esta clase pierde el focus lanza el llamado a guardar comentario
		$( ".comentario_abierto" ).focusout(function() {
			elemento = jQuery($(this));
			grabanuevocomentario_nuevo(elemento);

		})


		// Cuando el campo con esta clase pierde el focus lanza el llamado a guardar comentario
		$( ".comentario_abierto_select" ).change(function() {
			elemento = jQuery($(this));
			grabanuevocomentario_nuevo_select(elemento);

		})

	   //div flotante
	   var posicion_query = $("#nombreflotante").offset();
	   if(posicion_query != undefined )
	   {
        var margenSuperior_query = 15;
         $(window).scroll(function() {
             if ($(window).scrollTop() > posicion_query.top) {
			 marginTop= $(window).scrollTop() - posicion_query.top + margenSuperior_query
			 $("#nombreflotante").css('margin-top',marginTop);
                 $("#nombreflotante").stop().animate({
                     // marginTop: $(window).scrollTop() - posicion_query.top + margenSuperior_query

                 });
             } else {
                 $("#nombreflotante").stop().animate({
                     marginTop: 0
                 });
             };
         });
	   }

   } );

function iluminartr(){
	$('.ubicacion').each(function(){

		var ele = $(this).find(':checked').length;
		$(this).find('td').eq(0).css({'border':""});
		$(this).find('td').eq(1).css({'border':""});
		if( ele == 0 ){

			// $(this).css({'border':"green"});
			$(this).find('td').eq(0).css({'border':"orange solid 3px","border-right": "0px"});
			$(this).find('td').eq(1).css({'border':"orange solid 3px","border-left": "0px"});


			return false;
		}
	});

	// aqui .... aqui lo de pasar cuando no es check


}

function grabanuevocomentario()
{

	var empleado = document.getElementById('wempleado').value;
	var calificador=document.getElementById('wcalificador').value;
	var emp_pmla = (document.getElementById('wemp_pmla').value);
	var comentario = $('#nombrecomentario').val();
	var tipocomentario = $('#selectcomentario').val();
	var tema = (document.getElementById('wtema').value);
	var codigogcompetencia = $('#Ocodigogcompetencia').val();
	var codigocompetencia = $('#Ocodigocompetencia').val();
	var codigodescriptor = $('#Ocodigodescriptor').val();

	var formulario = $('#wformulario1').val();

	var ano=document.getElementById('wano').value;
	var periodo=document.getElementById('wperiodo').value;


    var params = "evaluacioncompetencias.php?consultaAjax=&woperacion=guardacomentario&wcomentario="+comentario+"&wformulario="+formulario+"&wtipocomentario="+tipocomentario+"&wcodicompetencia="+codigocompetencia+"&wcodigcompetencia="+codigogcompetencia+"&wcodidescriptor="+codigodescriptor+"&wempleado="+empleado+"&wcalificador="+calificador+"&wperiodo="+periodo+"&wano="+ano+"&wemp_pmla="+emp_pmla+"&wtema="+tema;
	$.get(params, function(data) {

	});

}

function seleccionaPeriodo_05()
{

	var empleado 	= 	$("#select_ante_05 option:selected").attr('wempleado');
	var wemp_pmla 	= 	$('#wemp_pmla').val();
	var ano			=	$("#select_ante_05 option:selected").attr('wano');
	var periodo		=	$("#select_ante_05 option:selected").attr('wperiodo');
	var formulario	=	$("#select_ante_05 option:selected").attr('wformulario');
	var tema 		= 	$('#wtema').val();




	$.post("evaluacioncompetencias.php",
		{
			consultaAjax:   	'',
			woperacion:     	'traenotasanteriores',
			wformulario:		formulario,
			wperiodo:			periodo,
			wano: 				ano,
			wemp_pmla: 			wemp_pmla,
			wtema:				tema,
			wempleado:			empleado

		}, function(data){
			$(".contienenotaoant").val('');

			for(var index in  data)
			{
				$("#text_comentario_anterior_"+index).val(data[index])

			}
		},'JSON');

		$.post("evaluacioncompetencias.php",
		{
			consultaAjax:   	'',
			woperacion:     	'traenotascomentariosanteriores',
			wformulario:		formulario,
			wperiodo:			periodo,
			wano: 				ano,
			wemp_pmla: 			wemp_pmla,
			wtema:				tema,
			wempleado:			empleado

		}, function(data){
			$(".contienecomentarioant").text('');
			for(var index in  data)
			{
				$("#textarea_comentario_anterior_"+index).text(data[index])

			}
		},'JSON');

		$.post("evaluacioncompetencias.php",
		{
			consultaAjax:   	'',
			woperacion:     	'traenotastiposcomentariosanteriores',
			wformulario:		formulario,
			wperiodo:			periodo,
			wano: 				ano,
			wemp_pmla: 			wemp_pmla,
			wtema:				tema,
			wempleado:			empleado

		}, function(data){
			//$(".contienecomentarioant").text('');
			for(var index in  data)
			{
				$("#select_tipo_comentario_anterior_"+index).val(data[index]);

			}
		},'JSON');
}


function grabanuevocomentario_nuevo (elemento){



	var empleado 	= 	$('#wempleado').val();
	var calificador	=	$('#wcalificador').val();
	var wemp_pmla 	= 	$('#wemp_pmla').val();
	var ano			=	$('#wano').val();
	var periodo		=	$('#wperiodo').val();
	var tema 		= 	$('#wtema').val();

	$.post("evaluacioncompetencias.php",
		{
			consultaAjax:   '',
			woperacion:     	'guardacomentario',
			wcomentario:		elemento.val(),
			wformulario:		elemento.attr('formulario'),
			wtipocomentario: 	$("#selectcomentario_"+elemento.attr('descriptor')).val(),
			wcodicompetencia: 	elemento.attr('competencia'),
			wcodigcompetencia:  elemento.attr('gcompetencia'),
			wcodidescriptor: 	elemento.attr('descriptor'),
			wempleado: 			empleado,
			wcalificador: 		calificador,
			wperiodo:			periodo,
			wano: 				ano,
			wemp_pmla: 			wemp_pmla,
			wtema:				tema

		}, function(data){

		});



}

function grabanuevocomentario_nuevo_select(){

	var empleado 	= 	$('#wempleado').val();
	var calificador	=	$('#wcalificador').val();
	var wemp_pmla 	= 	$('#wemp_pmla').val();
	var ano			=	$('#wano').val();
	var periodo		=	$('#wperiodo').val();
	var tema 		= 	$('#wtema').val();

	$.post("evaluacioncompetencias.php",
		{
			consultaAjax:   '',
			woperacion:     	'guardacomentario',
			wcomentario:		$("#texareacomentario_"+elemento.attr('descriptor')).val(),
			wformulario:		elemento.attr('formulario'),
			wtipocomentario: 	elemento.val(),
			wcodicompetencia: 	elemento.attr('competencia'),
			wcodigcompetencia:  elemento.attr('gcompetencia'),
			wcodidescriptor: 	elemento.attr('descriptor'),
			wempleado: 			empleado,
			wcalificador: 		calificador,
			wperiodo:			periodo,
			wano: 				ano,
			wemp_pmla: 			wemp_pmla,
			wtema:				tema

		}, function(data){

		});



}


function seleccionaPeriodo(periodoano,usuario)
{

if( $('#wperiodo2').val() =='ninguna')
{



 $("td[id^=tdac]").each(function(){
	$(this).find('div').html('-');
 });

 $('#divenctotalanterior').html('');

 $('#divtotalanterior').html('');


}else
{
var empleado = document.getElementById('wempleado').value;
var periodo = periodoano.value.split("-");
var tema = (document.getElementById('wtema').value);

var emp_pmla = (document.getElementById('wemp_pmla').value);

    var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wano='+periodo[0]+'&wperiodo='+periodo[1]+'&wuse='+usuario+'&wnotaant=si&wcalificador2='+periodo[3]+'-'+periodo[4]+'&wcalificado='+empleado+'&wtema='+tema;

	$.get(params, function(data) {

		var codigototal = data.split('-*-');

			var i = (codigototal.length);
			for (j=0; j<=i; j++)
			{
				if(codigototal[j])
				{
					codigo = codigototal[j].split('-');

					var div = 'div-'+codigo[0]+'-'+codigo[1]+'-'+codigo[2]+'-'+codigo[3];
					$('#'+div).html(codigo[4]);
					if(codigo[5]!='')
					{
						var campo = 'tdd'+codigo[0]+'-'+codigo[1]+'-'+codigo[2]+'-'+codigo[3];
						$('#'+div).append("</br><a onclick='muestracompromiso(\""+codigo[5]+"\", \""+codigo[6]+"-"+codigo[7]+"-"+codigo[8]+"\" ,\""+campo+"\")' style='cursor:pointer; color : orange'>Compromiso</a>");
					}
				}
			}



	});

	var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wano='+periodo[0]+'&wperiodo='+periodo[1]+'&wuse='+usuario+'&wnotaanttotal=si&wcalificador2='+periodo[3]+'-'+periodo[4]+'&wcalificado='+empleado+'&wtema='+tema;

			$.get(params, function(data) {
				var datasplit = data.split('||');
				var fechaanterior = datasplit[1];
				var caltotalanterior = datasplit[0];
				$('#divtotalanterior').html('<b>'+caltotalanterior+'</b>');
				$('#divenctotalanterior').html('<b>TOTAL PERIODO ANTERIOR <br>CIERRE : '+fechaanterior+' <b>');

			});

}
}





function muestraEstado(celda,nombre_encuestado,nombre_encuesta,historiaencuestado,ingresoencuestado,notaencuesta)
{

	//se asigna el nombre al div estado_nombre_encuestado
	$('#estado_nombre_encuestado').text(nombre_encuestado);

	//se asigna el nombre al div estado_nombre_encuesta
	$('#estado_nombre_encuesta').text(nombre_encuesta);

	//se asigna la nota de la encuesta si existe
	$('#mestadoencuesta').val(notaencuesta);

	//asigna historia y ingreso a inputs ocultos
	$('#estado_historia_encuestado').val(historiaencuestado);
	$('#estado_ingreso_encuestado').val(ingresoencuestado);



	//si existe la celda con el id se muestra
	if( $('#'+celda ) )
	{

		$.blockUI({ message: $('#'+celda ),
						css: { left: ( $(window).width() - 600 )/2 +'px',
								top: '200px',
							  width: '600px'
							 }
				  });

	}


}

function muestracompromiso(descripcion,fechacompromiso,campo)
{

$('#compromisoviejo').text(descripcion);
$('#fechavieja').val(fechacompromiso);
$('#descriptorviejo').text($('#'+campo).text());

		$.blockUI({ message: $('#agregarcompromiso'),
						css: { left: ( $(window).width() - 600 )/2 +'px',
								top: '200px',
							  width: '600px'
							 }
				  });



}

function traecompromisos(celda,desccompentencia,desdescriptor,codigogcompetenci,codigocompetencia,codigodescriptor)
{
	var ano=document.getElementById('wano').value;
	var periodo=document.getElementById('wperiodo').value;
	var tema = (document.getElementById('wtema').value);
	var empleado = document.getElementById('wempleado').value;
	var calificador=document.getElementById('wcalificador').value;
	var emp_pmla = (document.getElementById('wemp_pmla').value);
	var formulario = $('#wformulario1').val();
	 //$('td[name^=tdtxt-]')


	var params="evaluacioncompetencias.php?consultaAjax=&woperacion=traecomentario&wformulario="+formulario+"&wcodicompetencia="+codigocompetencia+"&wcodigcompetencia="+codigogcompetenci+"&wcodidescriptor="+codigodescriptor+"&wempleado="+empleado+"&wcalificador="+calificador+"&wperiodo="+periodo+"&wano="+ano+"&wemp_pmla="+emp_pmla+"&wtema="+tema;
						$.get(params, function(data) {

							var resultado = data.split('***');
							fnMostrar3 (celda,desccompentencia,desdescriptor,codigogcompetenci,codigocompetencia,codigodescriptor,resultado[0],resultado[1]);
						});

}


function fnMostrar3(celda,desccompentencia,desdescriptor,codigogcompetenci,codigocompetencia,codigodescriptor,comentario,tipocomentario)
{

	var formulario = $('#wformulario1').val();
	$('#Ocalificacion').text('');

	var posicion ;
	var i=0;
		$('input[name="radio-'+formulario+'-'+codigogcompetenci+'-'+codigocompetencia+'-'+codigodescriptor+'"]').each(
		function(){
		if($(this).attr('checked')=='checked'){
		posicion = i;
		}
		i++;
		});

	var textotd = $('td[name="tdtxt-'+formulario+'-'+codigogcompetenci+'-'+codigocompetencia+'-'+codigodescriptor+'"]').eq(posicion).html();
	if(textotd==null)
	{
		textotd = 'sin respuesta' ;
	}
	$('#Onombredescriptor').text(desdescriptor);
	$('#Onombrecompetencia').text(desccompentencia);
	$('#Ocodigogcompetencia').val(codigogcompetenci);
	$('#Ocodigocompetencia').val(codigocompetencia);
	$('#Ocodigodescriptor').val(codigodescriptor);

	$('#Ocalificacion').text(textotd);

	 $('#nombrecomentario').val(comentario);
	 $('#selectcomentario').val(tipocomentario);

	if( $('#'+celda ) ){

		$.blockUI({ message: $('#'+celda ),
						css: { left: ( $(window).width() - 600 )/2 +'px',
								top: '200px',
							  width: '600px'
							 }
				  });

	}


}

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

// funcion que cambia de estado Encuestas de los pacientes
function grabaCambioEstado()
{

	var emp_pmla = (document.getElementById('wemp_pmla').value);
	var tema = (document.getElementById('wtema').value);

	// trae los datos  del estado y la nota
	var nota_encuesta = $('#mestadoencuesta').val();
	var est_encuesta = $('.selectestadoencuesta').val();

	// trae los datos : Historia, ingreso y nombre dela encuesta
	var historiaencuestado = $('#estado_historia_encuestado').val();
	var ingresoencuestado = $('#estado_ingreso_encuestado').val();
	var encuesta = $('#estado_nombre_encuesta').text();
	var ano=document.getElementById('wano').value;
	var periodo=document.getElementById('wperiodo').value;

	params='evaluacioncompetencias.php?consultaAjax=&cambiaestadoencuesta=si&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wencuestaestado='+est_encuesta+'&wencuestacomentario='+nota_encuesta+'&whistoria='+historiaencuestado+'&wingreso='+ingresoencuestado+'&wencuesta='+encuesta+'&wano='+ano+'&wperiodo='+periodo;

	$.get(params, function(data) {

	});

	$('#notaencuesta'+historiaencuestado+'-'+ingresoencuestado).html(nota_encuesta);

}

//---
// Funcion que valida campo si es numerico que este entre las calificaciones maximas y minimas
//--
function validacampo(campo)
{

var calmax	    = (document.getElementById('wcalmax').value)*1;
var calmin	    = (document.getElementById('wcalmin').value)*1;
var calmal	    = (document.getElementById('wcalmal').value)*1;
var empleado    = document.getElementById('wempleado').value;
var calificador = document.getElementById('wcalificador').value;
var emp_pmla    = (document.getElementById('wemp_pmla').value);
var tema        = (document.getElementById('wtema').value);

	campo.value = $.trim( campo.value );

     if (campo.value.length!=0)
     {
        if(campo.value>calmax)
        {

            alert("califique de "+calmin+" a "+calmax+" cada criterio");
            campo.value="";
            return;

        }
        if(campo.value<calmin)
        {
            alert("califique de "+calmin+" a "+calmax+" cada criterio");
            campo.value="";
            return;
            //campo.id.focus();
        }
        if(isNaN(campo.value))
        {
            alert("Los Datos deben ser numericos");
            campo.value="";
            return;

        }
		if ((campo.value*1) >= calmin && (campo.value*1) <= calmax)
		{

			tabla=document.getElementById("EcompromisoEncabezado");
			var des= campo.id;

			if ((campo.value*1) >= calmin && (campo.value*1) <= calmal)
			{
				 $("#tdc"+campo.id).css({'background-color':'orange'});

				if(tabla==null)
				{
					 var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla=01&compromisos=Eencabezado&descriptor='+des+'&wtema='+tema;
					 $.get(params, function(data) {
					 $('#Ecompromisos').html(data);

					 var picker1="efecha-"+des+"-"+empleado;
					 var picker = $("<input class='compromisos'  size='10' id='"+picker1+"' onchange='grabadato(this)'/>").datepicker({
						monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
						dayNamesMin: ['Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
						nextText: 'Siguiente',
						prevText: 'Anterior',
						closeText: 'Cancelar',
						currentText: 'Hoy',
						changeMonth: true,
						changeYear: true,
						showButtonPanel: false,
						dateFormat: 'yy-mm-dd'
					 });

					$('#edatepicker-'+des).append(picker);

					});

					var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&compromisos=Cencabezado&descriptor='+des+'&wtema='+tema;
					$.get(params, function(data) {
					$('#Ccompromisos').html(data);

					var picker1="cfecha-"+des+"-"+empleado;

					 var picker = $("<input class='compromisos'  size='10' id='"+picker1+"' onchange='grabadato(this)'/>").datepicker({
						monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
						dayNamesMin: ['Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
						nextText: 'Siguiente',
						prevText: 'Anterior',
						closeText: 'Cancelar',
						currentText: 'Hoy',
						changeMonth: true,
						changeYear: true,
						showButtonPanel: false,
						dateFormat: 'yy-mm-dd'
					 });

					$('#cdatepicker-'+des).append(picker);
					});
				}
				else
				{

					var anterior =document.getElementById('valor_foco').value*1;
					if(anterior > calmal || anterior =='' )
					{
						var div = document.getElementById("Ecompromiso-"+des+"-"+empleado);

						if (div==null)
						{
							var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&compromisos=Ecompromiso&descriptor='+des+'&wtema='+tema;
							$.get(params, function(data) {

							$('#Ecompromisos').append(data);
							var picker1="efecha-"+des+"-"+empleado;

							 var picker = $("<input size='10' id='"+picker1+"' class='compromisos'  onchange='grabadato(this)'/>").datepicker({
								monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
								dayNamesMin: ['Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
								nextText: 'Siguiente',
								prevText: 'Anterior',
								closeText: 'Cancelar',
								currentText: 'Hoy',
								changeMonth: true,
								changeYear: true,
								showButtonPanel: false,
								dateFormat: 'yy-mm-dd'
							 });


							$('#edatepicker-'+des).append(picker);

							});

							var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&compromisos=Ccompromiso&descriptor='+des+'&wtema='+tema;
							$.get(params, function(data) {
							$('#Ccompromisos').append(data);

							var picker1="cfecha-"+des+"-"+calificador;
							var picker = $("<input size='10' id='"+picker1+"' onchange='grabadato(this)' />" ).datepicker({
								monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
								dayNamesMin: ['Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
								nextText: 'Siguiente',
								prevText: 'Anterior',
								closeText: 'Cancelar',
								currentText: 'Hoy',
								changeMonth: true,
								changeYear: true,
								showButtonPanel: false,
								dateFormat: 'yy-mm-dd'
							});


							$('#cdatepicker-'+des).append(picker);


							});
						}
					}
				}
		    }

			if (campo.value > calmal )
			{

				var des= campo.id;
				var div = document.getElementById("Ecompromiso-"+des);
				$("#tdc"+campo.id).css({'background-color': ""});

				if (div!=null)
				{

					var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&compromisos=eliminar&descriptor='+des+'&wempleado='+empleado+'&wcalificador='+calificador+'&wtema='+tema;
						$.get(params, function(data) {});


					$("#Ccompromiso-"+des).remove();
					$("#Ecompromiso-"+des).remove();

					if($('div[id^=Ecompromiso-]').length ==0 )
					{
						$("#EcompromisoEncabezado").remove();
						$("#CcompromisoEncabezado").remove();
					}

				}

			}
		}
    }
	if (campo.value.length==0)
	{
		$("#tdd"+campo.id).css({'background-color': ""});
		var des= campo.id;
				var div = document.getElementById("Ecompromiso-"+des);
				$("#tdc"+campo.id).css({'background-color': ""});

				if (div!=null)
				{
					var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&compromisos=eliminar&descriptor='+des+'&wempleado='+empleado+'&wcalificador='+calificador+'&wtema='+tema;
						$.get(params, function(data) {});

					$("#Ccompromiso-"+des).remove();
					$("#Ecompromiso-"+des).remove();

				}
	}

}

//----
//-- Esta validacion solo lo tendra los descriptores tipo 09 que son
//-- los descriptores numericos pero no son tenido en cuenta en los informes
function validacampoNumericoNoTendioEncuentaParaEvaluaciones (campo)
{
	var elemento = jQuery(campo);
	if (/^-?[0-9]+([,\.][0-9]*)?$/.test(elemento.val()))
	{
		 var res = elemento.val().replace(/,/gi, ".");
		 elemento.val(res);

	}
	else
	{
		elemento.val('');
	}

}

function ClicRadioEscala(oculto,campo,id,numeroprueba,ndes,ncom,porcentaje,rotulo)
{
	tomavaloractual2 (oculto);
	document.getElementById(oculto).value=campo.value;
	var elemento = document.getElementById(oculto);
	grabadato(elemento,id,numeroprueba,ndes,ncom,porcentaje,rotulo);

}

function tomavaloractual2(oculto)
{

	document.getElementById('valor_foco').value=document.getElementById(oculto).value ;

}

function grabadato2(elemento,formulario,grupocom,comp,des)
{
	var wemp_pmla = (document.getElementById('wemp_pmla').value);
	var ingreso = $('#wingreso').val();
	var tema = (document.getElementById('wtema').value);
	var tipoevaluacion=document.getElementById('wtipoevaluacion').value;
	var ano=document.getElementById('wano').value;
	var periodo=document.getElementById('wperiodo').value;
	var empleado = document.getElementById('wempleado').value;
	var calificador=document.getElementById('wcalificador').value;
	parametros = "consultaAjax=guardatipoSelect&wcalificacion="+elemento.value+"&wempleado="+empleado+"&wnumprueba=1&wcalificador="+calificador+"&wformulario="+formulario+"&wgrupocom="+grupocom+"&wcomp="+comp+"&wfomurlario="+formulario+"&wperiodo="+periodo+"&wano="+ano+"&wtipoevaluacion="+tipoevaluacion+"&wemp_pmla="+wemp_pmla+"&wtema="+tema+"&wdes="+des+"&wingreso="+ingreso;
	// alert(parametros);
	try
	  {
		var ajax = nuevoAjax();
		ajax.open("POST", "evaluacioncompetencias.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

	  }catch(e){ alert(e) }
}

function grabadato3(elemento,formulario,grupocom,comp,des)
{
	var wemp_pmla = (document.getElementById('wemp_pmla').value);
	var tema = (document.getElementById('wtema').value);
	var tipoevaluacion=document.getElementById('wtipoevaluacion').value;
	var ano=document.getElementById('wano').value;
	var periodo=document.getElementById('wperiodo').value;
	var empleado = document.getElementById('wempleado').value;
	var calificador=document.getElementById('wcalificador').value;
	var calmax	= (document.getElementById('wcalmax').value)*1;

	parametros = "consultaAjax=guardatipotext&wcalificacion="+elemento.value+"&wempleado="+empleado+"&wnumprueba=1&wcalificador="+calificador+"&wformulario="+formulario+"&wgrupocom="+grupocom+"&wcomp="+comp+"&wfomurlario="+formulario+"&wperiodo="+periodo+"&wano="+ano+"&wtipoevaluacion="+tipoevaluacion+"&wemp_pmla="+wemp_pmla+"&wtema="+tema+"&wdes="+des+"&wcalmax="+calmax;
	  try
	  {
		var ajax = nuevoAjax();
		ajax.open("POST", "evaluacioncompetencias.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

	  }catch(e){ alert(e) }
}

function Programacion_Automatica(ptema, pempleado, pcalificador)
{


var wemp_pmla = (document.getElementById('wemp_pmla').value);
var retorna = ptema+" "+pempleado+" "+pcalificador;
var tipoevaluacion=document.getElementById('wtipoevaluacion').value;
var ptipo = document.getElementById('tipo').value;
var ano=document.getElementById('wano').value;
var periodo=document.getElementById('wperiodo').value;

	$.get("../procesos/evaluacioncompetencias.php",
				{
					consultaAjax 	: '',
					woperacion		: 'programacionautomatica',
					wemp_pmla		: $('#wemp_pmla').val(),
					wptema          : ptipo,
					wtema           : $('#wtema').val(),
					wpempleado		: pempleado,
					wpcalificador	: pcalificador,
					wperiodo		: periodo,
					wano			: ano ,
					wtipoevaluacion	: tipoevaluacion


				}
				, function(data) {
				//alert(data);

				});

return;
}
function grabadato(campo,id,numeroprueba,ndes,ncom,porcentaje,rotulo='sindato',centro)
{

var calmax	  = (document.getElementById('wcalmax').value)*1;
var calmin	  = (document.getElementById('wcalmin').value)*1;
var calmal	  = (document.getElementById('wcalmal').value)*1;
var auxcampo  = campo.id.split("-");
var wemp_pmla = (document.getElementById('wemp_pmla').value);
var tema      = (document.getElementById('wtema').value);


	if(campo.id =='cerrarevaluacion')
    {

		validar=id;
		validar=id.split("*");
        var a ='*|';
        var calificaciones ='';
		for(i=1;i<validar.length;i++)
		{
			calificaciones = calificaciones+a+document.getElementById(validar[i]).value;
			a = '*';
			calificaciones=calificaciones.replace("*|", "*");			

			if(document.getElementById(validar[i]).value.length==0)
			{
				if (i==validar.length-1 && document.getElementById(validar[i-1]).value == 5)
                {
                   // Se genera una excepcion: si el contrato se responde 'prorroga si', no se debe diligenciar
                   // el campo de explicación.
               	}
               	else
                {
				   alert("Debe llenar todos los campos");
				   return; 
			    }
			}
		}

		var condicioncompromisos = false;
		$('.compromisos').each(function(){


			if($(this).val().length ==0  )
			{
				alert("Debe llenar todos los campos de compromisos");

				condicioncompromisos = true;
				return false;
			}

		});

		if (condicioncompromisos)
		{
			return;
		}


		var tipoevaluacion=document.getElementById('wtipoevaluacion').value;

		if  (tipoevaluacion=='01')
		{
			firma = fnMostrar2("firmadigital");
		}
		else
		{

			if  (tipoevaluacion=='03')
			{
				alert("Encuesta cerrada satisfactoriamente");
			}
			if  (tipoevaluacion=='04'  || tipoevaluacion=='05' )
			{
				alert("Evaluacion cerrada satisfactoriamente");
			}


			empleado = document.getElementById('wempleado').value;
			calificador=document.getElementById('wcalificador').value;
			ano=document.getElementById('wano').value;
			periodo=document.getElementById('wperiodo').value;
			formulario=document.getElementById('wformulario1').value;

			divtotal="div"+formulario;
			totalevaluacion= (document.getElementById(divtotal).innerHTML)*1;
			//tipoevaluacion=document.getElementById('wtipoevaluacion').value;
			/**/
			if  (tipoevaluacion=='04')
			{
				var numero_pregs_formato = $("td[id^=tdd]").length;

				var valor_total_radios = 0;
				$("input[name^=radio-]:checked").each(function(){
					var valor_radio = $(this).val();
					valor_total_radios+= parseInt( valor_radio );

				});

				var valor_cada_respondida = 100 / numero_pregs_formato;

				var valor_evaluado = valor_cada_respondida * valor_total_radios;
				totalevaluacion = valor_cada_respondida * valor_total_radios;
			}
			var parametros = "";


			parametros = "consultaAjax=guardaevaluacion&wempleado="+empleado+"&wcalificador="+calificador+"&wnumprueba="+numeroprueba+"&wano="+ano+"&wperiodo="+periodo+"&wformulario="+formulario+"&wcaltotal="+totalevaluacion+'&wtipoevaluacion='+tipoevaluacion+'&wcalificacion='+id+'&wcalificaciones='+calificaciones+'&wemp_pmla='+wemp_pmla+'&wtema='+tema;
				  try
				  {
					var ajax = nuevoAjax();
					ajax.open("POST", "evaluacioncompetencias.php",false);
					ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					ajax.send(parametros);
				  }catch(e){ alert(e) }


			var historia = $('#whistoria').val();
			var ingreso = $('#wingreso').val();
			var encuesta = $('#wencuesta').val();
			var wccohospitalario = $('#wccohospitalario').val();

			if  (tipoevaluacion=='03' ||  tipoevaluacion== '05' )
			{
				var ano=document.getElementById('wano').value;
				var periodo=document.getElementById('wperiodo').value;

				var params = 'evaluacioncompetencias.php?wemp_pmla='+wemp_pmla+'&wtema='+tema+'&guardaentablaencuesta=si&whistoria='+historia+'&wingreso='+ingreso+'&wencuesta='+encuesta+'&wano='+ano+'&wperiodo='+periodo;
				$.get(params, function(data) {

				});

				var wfechacierre = ''
				$(".fechaconcierre").each(function(){
					wfechacierre =  $(this).val();
					//alert(wfecha);
				});
				
				//---------------------------
				// si la fecha de cierre es igual a '' significa que no tiene campo de cierre de encuesta , por lo tanto se debe quedar sin atualizar en la tabla 49 su fecha
				// si la fecha tiene valor hace el procedimiento de actualiza_empleado_tipo_05 = si
				if (wfechacierre =='')
				{	
					
				}
				else
				{


					$.post("evaluacioncompetencias.php",
					{
						consultaAjax:   			'',
						wemp_pmla:      			$('#wemp_pmla').val(),
						actualiza_empleado_tipo_05:	'si',
						wced:						$('#whistoria').val(),
						wencuesta:					$('#wencuesta').val(),
						whis: 						$('#whistoria').val(),
						wing: 						$('#wingreso').val() ,
						wedad: 						'0' ,
						wentidad: 					'no aplica',
						wcodentidad: 				'',
						wtelefono: 					'no aplica',
						whcod:						'no aplica',
						wtpa: 						'no',
						wcco: 						$('#wccohospitalario').val(),
						wfecha: 					wfechacierre ,
						wano: 						$('#wano').val(),
						wperiodo: 					$('#wperiodo').val(),
						wpublicoobjetivo: 			'empleado',
						wtema:						$('#wtema').val()
					}, function(data){

					});
				}


				var formulario=document.getElementById('wformulario1').value;
				var tema = (document.getElementById('wtema').value);
				var wuse = (document.getElementById('wuse').value);
				var calmax	= (document.getElementById('wcalmax').value)*1;
				var calmin	= (document.getElementById('wcalmin').value)*1;
				var calmal	= (document.getElementById('wcalmal').value)*1;
				var emp_pmla = (document.getElementById('wemp_pmla').value);
				var calificador=document.getElementById('wcalificador').value;
				var ano = document.getElementById('wano').value;
				var periodo=document.getElementById('wperiodo').value;
				var tipo = document.getElementById('tipo').value;
				//var tipoevaluacion = document.getElementById('wtipoevaluacion').value;
				var ccohospitalario = document.getElementById('wccohospitalario').value;
				var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wformulario='+formulario+'&wano='+ano+'&wperiodo='+periodo+'&westado='+empleado[3]+'&wnumprueba=1&wcalmax='+calmax+'&wcalmin='+calmin+'&wcalmal='+calmal+'&tipo='+tipo+'&wtipoevaluacion='+tipoevaluacion+'&wuse='+wuse+'&wtema='+tema+'&cargaLista=si&wccohospitalario='+centro;


				$.get(params, function(data) {
						  $('#evaluacioncompetencias').html(data);
				}).done(function(){
        
			        var datos = eval('(' + $("#arr_wccohospitalario").val() + ')');
			        var arr_datos = new Array();
			        var index = -1;
			        for (var CodVal in datos)
			        {
			        	index++;
			            arr_datos[index] = {};
			            arr_datos[index].value  = CodVal+'-'+datos[CodVal];
			            arr_datos[index].label  = CodVal+'-'+datos[CodVal];
			            arr_datos[index].codigo = CodVal;
			            arr_datos[index].nombre = CodVal+'-'+datos[CodVal];           
			        }

			        $("#wccohoseleccion").autocomplete({
			                source: arr_datos, minLength : 0,
			                select: function( event, ui ) {
			                            var cod_sel = ui.item.codigo;
			                            var nom_sel = ui.item.nombre;
			                            $("#wccohoseleccion").attr("codigo",cod_sel);
			                            $("#wccohoseleccion").attr("nombre",nom_sel);
			                            $("#wccohospitalario" ).val(ui.item.codigo);  
			                        },
			                close: function( event, ui ) {
			                    
			                }
			        });
			      });


			}
			if  (tipoevaluacion=='04')
			{
				var tema = (document.getElementById('wtema').value);
				var wuse = (document.getElementById('wuse').value);
				var emp_pmla = (document.getElementById('wemp_pmla').value);
				var formulario=document.getElementById('wformulario1').value;
				var ano = document.getElementById('wano').value;
				var periodo=document.getElementById('wperiodo').value;

				var params = 'evaluacioncompetencias.php?wemp_pmla='+wemp_pmla+'&wtema='+tema+'&guardaentablaevaluacion=si&wuse='+wuse+'&wformulario='+formulario+'&wano='+ano+'&wperiodo='+periodo;
				$.get(params, function(data) {

				});



				var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wuse='+wuse+'&wtema='+tema;
				$.get(params, function(data) {
						  $('#evaluacioncompetencias').html(data);
					}).done(function(){
        
			        var datos = eval('(' + $("#arr_wccohospitalario").val() + ')');
			        var arr_datos = new Array();
			        var index = -1;
			        for (var CodVal in datos)
			        {
			        	index++;
			            arr_datos[index] = {};
			            arr_datos[index].value  = CodVal+'-'+datos[CodVal];
			            arr_datos[index].label  = CodVal+'-'+datos[CodVal];
			            arr_datos[index].codigo = CodVal;
			            arr_datos[index].nombre = CodVal+'-'+datos[CodVal];           
			        }

			        $("#wccohoseleccion").autocomplete({
			                source: arr_datos, minLength : 0,
			                select: function( event, ui ) {
			                            var cod_sel = ui.item.codigo;
			                            var nom_sel = ui.item.nombre;
			                            $("#wccohoseleccion").attr("codigo",cod_sel);
			                            $("#wccohoseleccion").attr("nombre",nom_sel);
			                            $("#wccohospitalario" ).val(ui.item.codigo);  
			                        },
			                close: function( event, ui ) {
			                    
			                }
			        });
			      });
			}

			if  (tipoevaluacion== '05' )
			{


				$.post("evaluacioncompetencias.php",
				{
					consultaAjax:   			'',
					wemp_pmla:      			$('#wemp_pmla').val(),
					guarda_empleado_tipo_05:	'si',
					wced:						$('#whistoria').val(),
					wencuesta:					$('#wencuesta').val(),
					whis: 						$('#whistoria').val(),
					wing: 						$('#wingreso').val() ,
					wedad: 						'0' ,
					wentidad: 					'no aplica',
					wcodentidad: 				'',
					wtelefono: 					'no aplica',
					whcod:						'no aplica',
					wtpa: 						'no',
					wcco: 						$('#wccohospitalario').val(),
					wfechaing: 					'' ,
					wano: 						ano,
					wperiodo: 					$('#wperiodo').val(),
					wpublicoobjetivo: 			'empleado',
					wtema:						$('#wtema').val()
				}, function(data){

				});

			}

			return;

		}


    }
    else
    {



		if( auxcampo[0] == 'ecompromiso' || auxcampo[0] == 'ccompromiso' )
		{
			empleado = document.getElementById('wempleado').value;
			calificador=document.getElementById('wcalificador').value;
			ano=document.getElementById('wano').value;
			periodo=document.getElementById('wperiodo').value;
			var parametros = "";
			parametros = "consultaAjax=guardaCompromiso&wid="+campo.id+"&wcalificacion="+campo.value+"&wempleado="+empleado+"&wcalificador="+calificador+"&wperiodo="+periodo+"&wano="+ano+'&wemp_pmla='+wemp_pmla+"&wtema="+tema;

				   try
				  {
					var ajax = nuevoAjax();
					ajax.open("POST", "evaluacioncompetencias.php",false);
					ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					ajax.send(parametros);
					//alert(ajax.responseText);
				  }catch(e){ alert(e) }
		}
		else
		{

			if ( auxcampo[0] == 'efecha' || auxcampo[0] == 'cfecha' )
			{
				empleado = document.getElementById('wempleado').value;
				calificador=document.getElementById('wcalificador').value;
				ano=document.getElementById('wano').value;
				periodo=document.getElementById('wperiodo').value;
				//alert(campo.value);
				var parametros = "";
				parametros = "consultaAjax=guardaFechaCompromiso&wid="+campo.id+"&wcalificacion="+campo.value+"&wempleado="+empleado+"&wcalificador="+calificador+"&wperiodo="+periodo+"&wano="+ano+'&wemp_pmla='+wemp_pmla+"&wtema="+tema;
				   try
					  {
						var ajax = nuevoAjax();
						ajax.open("POST", "evaluacioncompetencias.php",false);
						ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						ajax.send(parametros);
						//alert(ajax.responseText);
					  }catch(e){ alert(e) }


			}
			else
			{
				if(rotulo=='sindato')
				{
				 return;
				}
				empleado = document.getElementById('wempleado').value;
				calificador=document.getElementById('wcalificador').value;
				ano=document.getElementById('wano').value;
				periodo=document.getElementById('wperiodo').value;
				tipoevaluacion=document.getElementById('wtipoevaluacion').value;

				var parametros = "";
				parametros = "consultaAjax=guardaCalificacion&wid="+id+"&wcalificacion="+campo.value+"&wempleado="+empleado+"&wnumprueba=1&wcalificador="+calificador+'&wfo_gc_co_de='+campo.id+'&wperiodo='+periodo+'&wano='+ano+'&wtipoevaluacion='+tipoevaluacion+'&wemp_pmla='+wemp_pmla+'&wtema='+tema+'&wrotulo='+rotulo;

					   try
					  {
						var ajax = nuevoAjax();
						ajax.open("POST", "evaluacioncompetencias.php",false);
						ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						ajax.send(parametros);
						//alert(ajax.responseText);
					  }catch(e){ alert(e) }

				campo1=campo.id.split("-");

				//campo1[0]+'-'+campo1[1]

				//variables que hacen referencias a los divs del formulario (div por competencias, div por grupo competencia
				//y div por formulario

				var div = 'div'+campo1[0]+'-'+campo1[1]+'-'+campo1[2];
				var divgco='div'+campo1[0]+'-'+campo1[1];
				var divtotal='div'+campo1[0];
				//-----------------

				//variables que hacen referencias a los text del formulario (text por competencias)
				var text = 'text'+campo1[0]+'-'+campo1[1]+'-'+campo1[2];

				//-----------------

				//traigo valores anteriores
				//por descriptor
				var valor_anterior2 =document.getElementById('valor_foco').value*1;

				//por grupo de grupo competencias
				var valor_ant_grupcom = document.getElementById(divgco).innerHTML*1

				//por grupo de competencias

				var valor_ant_comp= document.getElementById(div).innerHTML*1;
				//total de evaluacion
				var valor_ant_total = document.getElementById(divtotal).innerHTML*1;
				//---------------------


				var cantidad2 = campo.value*1;

				cantidad2 = cantidad2 - valor_anterior2;


				//actualiza txt por competencia
				var totalconcepto2 = (document.getElementById(text).value)*1;
				totalconcepto2 = totalconcepto2 + cantidad2;
				document.getElementById(text).value =(totalconcepto2);
				//------------

				// si la competencia no tiene la clase nosetieneencuenta haga
				var elementoauxiliar  = jQuery(campo);
				if(!(elementoauxiliar.hasClass("nosetieneencuenta")))
				{

					//calcula el valor porcentual de la competencia
					var totalconcepto = (((((totalconcepto2*1) / ndes) / calmax ) * 100) / ncom) * (porcentaje * 0.01);
					totalconcepto = redondeo2decimales (totalconcepto);
					//------------

					//calcula valor porcental por grupo de competencia
					total_grupcom= valor_ant_grupcom + (totalconcepto - valor_ant_comp );
					var total_grupcom = total_grupcom;
					total_grupcom= redondeo2decimales (total_grupcom);

					//calcula valor porcentual total de la evaluacion
					// total_evaluacionsr =

					//-----------se utiliza
					clasejq = $(campo).attr("class");
					var valorconceptojq = 0;
					var totalConceptojq = 0;
					var j=0;
					$("."+clasejq).each(function(){
						totalConceptojq+=$(this).val()*1;
						j++;
					});

					valorconceptojq = (((((totalConceptojq*1) / j) / calmax ) * 100) / 1) * (porcentaje * 0.01);
					valorconceptojq = redondeo2decimales (valorconceptojq);
					//------
					total_evaluacion = valor_ant_total + ( total_grupcom - valor_ant_grupcom);
					total_evaluacion= redondeo2decimales (total_evaluacion);
					//-----------
					totalgcompetenciajq=0;
					document.getElementById(div).innerHTML =(totalconcepto);
					document.getElementById(divgco).innerHTML =(valorconceptojq);
					$(".resultadogcompetencia").each(function(){
						totalgcompetenciajq+=$(this).text()*1;
					});

					totalgcompetenciajq = redondeo2decimales (totalgcompetenciajq);
					// muestra de resultados en pantalla
					// pone en la div correspondiente el valor porcentual de la competencia
					// alert(totalconcepto);
					//document.getElementById(div).innerHTML =(totalconcepto);

					//pone en la divgci correspondiente el valor porcentual del grupo de competencias
					//--------
					//document.getElementById(divgco).innerHTML =(valorconceptojq);
					//----------
					document.getElementById(divtotal).innerHTML =(totalgcompetenciajq);
					//------------
				}
				else
				{
				   //alert(elementoauxiliar.attr('grupocompetencia'));
				   var grupodecompetencias = elementoauxiliar.attr('grupocompetencia');
				   var formato 		= elementoauxiliar.attr('formato');
				   var competencia 	= elementoauxiliar.attr('competencia');
				   var sumadevalores = 0;
				   $(".nosetieneencuenta").each(function () {
						if($(this).attr('grupocompetencia') == grupodecompetencias)
						{
							sumadevalores = (sumadevalores*1) + ($(this).val()*1);
						}
				   });

				   $("#div"+formato+"-"+grupodecompetencias+"-"+competencia).html(sumadevalores);
				   $("#div"+formato+"-"+grupodecompetencias).html(sumadevalores);

				}

			}
		}
    }

iluminartr();




}


function tomavaloractual(campo)
{

		document.getElementById('valor_foco').value = campo.value;

}

function seleccionaPaciente (nombre,historiaencuestado,ingreso,encuesta,centro,periodo_pro,ano_pro,wper)
{

	var calificador=document.getElementById('wcalificador').value;
	var formulario=document.getElementById('wformulario1').value;
	var tema = (document.getElementById('wtema').value);
	var wuse = (document.getElementById('wuse').value);
	var emp_pmla = (document.getElementById('wemp_pmla').value);
	var tipo = document.getElementById('tipo').value;
	var tipoevaluacion = document.getElementById('wtipoevaluacion').value;
	var calmax	= (document.getElementById('wcalmax').value)*1;
	var calmin	= (document.getElementById('wcalmin').value)*1;
	var calmal	= (document.getElementById('wcalmal').value)*1;
	var ano = document.getElementById('wano').value;
	var periodo=document.getElementById('wperiodo').value;
	var ccohospitalario = centro;

	$("#wano").val(ano_pro);
	$("#wperiodo").val(periodo_pro);
	


	if (tipoevaluacion=='05')
	{
		var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wempleado='+historiaencuestado+'&wcalificador='+calificador+'&wnempleado='+nombre+'&wformulario='+formulario+'&wano='+ano_pro+'&wperiodo='+periodo_pro+'&wuse='+wuse+'&wnumprueba=1&wcalmax='+calmax+'&wcalmin='+calmin+'&wcalmal='+calmal+'&tipo='+tipo+'&wtipoevaluacion='+tipoevaluacion+'&wuse='+wuse+'&wtema='+tema+'&cargaLista=si&wccohospitalario='+ccohospitalario+'&whistoria='+historiaencuestado+'&wingreso='+ingreso+'&wencuesta='+encuesta;


		$.get(params, function(data) {
			  $('#evaluacioncompetencias').html(data);
			  cargar_datapicker();
		}).done(function(){
        
        var datos = eval('(' + $("#arr_wccohospitalario").val() + ')');
        var arr_datos = new Array();
        var index = -1;
        for (var CodVal in datos)
        {
        	index++;
            arr_datos[index] = {};
            arr_datos[index].value  = CodVal+'-'+datos[CodVal];
            arr_datos[index].label  = CodVal+'-'+datos[CodVal];
            arr_datos[index].codigo = CodVal;
            arr_datos[index].nombre = CodVal+'-'+datos[CodVal];           
        }

        $("#wccohoseleccion").autocomplete({
                source: arr_datos, minLength : 0,
                select: function( event, ui ) {
                            var cod_sel = ui.item.codigo;
                            var nom_sel = ui.item.nombre;
                            $("#wccohoseleccion").attr("codigo",cod_sel);
                            $("#wccohoseleccion").attr("nombre",nom_sel);
                            $("#wccohospitalario" ).val(ui.item.codigo);  
                        },
                close: function( event, ui ) {
                    
                }
        });
      });

	}
	else
	{
		
		if(tipoevaluacion =='03')
		{

			$("#wperiodo").val(wper);
			periodo = wper;
		}
		
		// En caso de que el tema pertenezca a guias de enfermeria(10)
		if (tema == '10')
			periodo=periodo_pro;

		var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wempleado='+historiaencuestado+'&wcalificador='+calificador+'&wnempleado='+nombre+'&wformulario='+formulario+'&wano='+ano+'&wperiodo='+periodo+'&wuse='+wuse+'&wnumprueba=1&wcalmax='+calmax+'&wcalmin='+calmin+'&wcalmal='+calmal+'&tipo='+tipo+'&wtipoevaluacion='+tipoevaluacion+'&wuse='+wuse+'&wtema='+tema+'&cargaLista=si&wccohospitalario='+ccohospitalario+'&whistoria='+historiaencuestado+'&wingreso='+ingreso+'&wencuesta='+encuesta;


		$.get(params, function(data) {
			  $('#evaluacioncompetencias').html(data);
			  cargar_datapicker();
		}).done(function(){
        
        var datos = eval('(' + $("#arr_wccohospitalario").val() + ')');
        var arr_datos = new Array();
        var index = -1;
        for (var CodVal in datos)
        {
        	index++;
            arr_datos[index] = {};
            arr_datos[index].value  = CodVal+'-'+datos[CodVal];
            arr_datos[index].label  = CodVal+'-'+datos[CodVal];
            arr_datos[index].codigo = CodVal;
            arr_datos[index].nombre = CodVal+'-'+datos[CodVal];           
        }

        $("#wccohoseleccion").autocomplete({
                source: arr_datos, minLength : 0,
                select: function( event, ui ) {
                            var cod_sel = ui.item.codigo;
                            var nom_sel = ui.item.nombre;
                            $("#wccohoseleccion").attr("codigo",cod_sel);
                            $("#wccohoseleccion").attr("nombre",nom_sel);
                            $("#wccohospitalario" ).val(ui.item.codigo);  
                        },
                close: function( event, ui ) {
                    
                }
        });
      });  
	}

}

function cargar_datapicker()
{
	$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		dayNamesMin: ['D','L','M','M','J','V','S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['esp']);
	ponerdatepicker();
}

function ponerdatepicker()
{
	$(".datepicker").datepicker({
						showOn: "button",
						buttonImage: "../../images/medical/root/calendar.gif",
						buttonImageOnly: true
					});


}


function  ConsultarLista()
{

var formulario=document.getElementById('wformulario1').value;
var tema     = (document.getElementById('wtema').value);
var wuse     = (document.getElementById('wuse').value);
var calmax	 = (document.getElementById('wcalmax').value)*1;
var calmin	 = (document.getElementById('wcalmin').value)*1;
var calmal	 = (document.getElementById('wcalmal').value)*1;
var empleado = document.getElementById('wempleado').value;
var emp_pmla = (document.getElementById('wemp_pmla').value);
var calificador=document.getElementById('wcalificador').value;
var ano      = document.getElementById('wano').value;
var periodo  = document.getElementById('wperiodo').value;
var tipo     = document.getElementById('tipo').value;
var tipoevaluacion = document.getElementById('wtipoevaluacion').value;
var ccohospitalario = document.getElementById('wccohospitalario').value;

var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wempleado='+empleado+'&wcalificador='+calificador+'&wformulario='+formulario+'&wano='+ano+'&wperiodo='+periodo+'&westado='+empleado[3]+'&wnumprueba=1&wcalmax='+calmax+'&wcalmin='+calmin+'&wcalmal='+calmal+'&tipo='+tipo+'&wtipoevaluacion='+tipoevaluacion+'&wuse='+wuse+'&wtema='+tema+'&cargaLista=si&wccohospitalario='+ccohospitalario;
$.get(params, function(data) {
		  $('#evaluacioncompetencias').html(data);
	}).done(function(){
        
        var datos = eval('(' + $("#arr_wccohospitalario").val() + ')');
        var arr_datos = new Array();
        var index = -1;
        for (var CodVal in datos)
        {
        	index++;
            arr_datos[index] = {};
            arr_datos[index].value  = CodVal+'-'+datos[CodVal];
            arr_datos[index].label  = CodVal+'-'+datos[CodVal];
            arr_datos[index].codigo = CodVal;
            arr_datos[index].nombre = CodVal+'-'+datos[CodVal];           
        }

        $("#wccohoseleccion").autocomplete({
                source: arr_datos, minLength : 0,
                select: function( event, ui ) {
                            var cod_sel = ui.item.codigo;
                            var nom_sel = ui.item.nombre;
                            $("#wccohoseleccion").attr("codigo",cod_sel);
                            $("#wccohoseleccion").attr("nombre",nom_sel);
                            $("#wccohospitalario" ).val(ui.item.codigo);  
                        },
                close: function( event, ui ) {
                    
                }
        });

        $('#wccohoseleccion').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("codigo","");
                        $(this).attr("nombre","");
                    }
                    else
                    {
                        $(this).val($(this).attr("nombre"));
                    }
                }
            });
    });

}


function seleccionaEmpleado(empleado,calificador,ano,periodo,usuario,nprueba,calmax,calmin,calmal,cargo)
{

	var tema     = (document.getElementById('wtema').value);
	var wuse     = (document.getElementById('wuse').value);
	var codtab   = $('#wcodtab').val();
	var emp_pmla = (document.getElementById('wemp_pmla').value);
	var wverifi  = 0;

	empleado=empleado.id.split("|");

	var tipo  = document.getElementById('tipo').value;
	var tipoevaluacion = document.getElementById('wtipoevaluacion').value;
	var cargo = cargo;


	// si los formatos son del tipo 3 = Encuestas a Usuarios registrados (pacientes) , esta seleccion de empleados sirve para desplegar la lista de centro de costos para posteriormente
	// seleccionar pacientes

	if(tipoevaluacion=='03' || tipoevaluacion=='05'  )
	{

		var nombre_encuesta = $(this).text();
		var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wempleado='+empleado[0]+'&wcalificador='+calificador+'&wnempleado='+empleado[1]+'&wformulario='+empleado[2]+'&wano='+ano+'&wperiodo='+periodo+'&westado='+empleado[3]+'&wuse='+usuario+'&wnumprueba='+nprueba+'&wcalmax='+calmax+'&wcalmin='+calmin+'&wcalmal='+calmal+'&tipo='+tipo+'&wtipoevaluacion='+tipoevaluacion+'&wcargo='+cargo+'&wuse='+wuse+'&wtema='+tema+'&cargacco=si&wnombreevaluacion='+nombre_encuesta;


	}
	else if(tipoevaluacion=='04'  )
	{
		var calificador=(document.getElementById('wuse').value);
		var usuario=(document.getElementById('wuse').value);
		var formulario=empleado[2];
		var nprueba='1';
		var calmax='1'
		var calmin='0';
		var calmal='0';
		var empleado=(document.getElementById('wuse').value);
		var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wempleado='+empleado+'&wcalificador='+calificador+'&wnempleado='+empleado+'&wformulario='+formulario+'&westado='+empleado+'&wuse='+usuario+'&wnumprueba='+nprueba+'&wcalmax='+calmax+'&wcalmin='+calmin+'&wcalmal='+calmal+'&tipo='+tipo+'&wtipoevaluacion='+tipoevaluacion+'&wcargo='+cargo+'&wuse='+wuse+'&wtema='+tema+'&wnombreevaluacion='+nombre_encuesta;

		$('#empleadosConocimiento').html('');
	}
	else
	{

        if ( tipoevaluacion=='01' && tipo=='2')
		{
			$.post("evaluacioncompetencias.php",
			{
				consultaAjax:  '',
				woperacion  :  'ConsultarPeriodosant',
				async       :  false,
	            wemp_pmla   :  emp_pmla,
		        wperiodo    :  periodo,
		        wano        :  ano,
	            wtipo       :  tipo, 
	            wtema       :  tema,
	            wempleado   :  empleado[0]

			}, function(data){
				 if (data > 0){
                     alert('Debe diligenciar las evaluaciones anteriores');
                     
				 }
				 else{	
					 var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wempleado='+empleado[0]+'&wcalificador='+calificador+'&wnempleado='+empleado[1]+'&wformulario='+empleado[2]+'&wano='+ano+'&wperiodo='+periodo+'&westado='+empleado[3]+'&wuse='+usuario+'&wnumprueba='+nprueba+'&wcalmax='+calmax+'&wcalmin='+calmin+'&wcalmal='+calmal+'&tipo='+tipo+'&wtipoevaluacion='+tipoevaluacion+'&wcargo='+cargo+'&wuse='+wuse+'&wtema='+tema;

						$.get(params,{wcodtab : codtab}, function(data) {

					          $('#evaluacioncompetencias').html(data);

					    }).done(function(){
					        
					        var datos = eval('(' + $("#arr_wccohospitalario").val() + ')');
					        var arr_datos = new Array();
					        var index = -1;
					        for (var CodVal in datos)
					        {
					        	index++;
					            arr_datos[index] = {};
					            arr_datos[index].value  = CodVal+'-'+datos[CodVal];
					            arr_datos[index].label  = CodVal+'-'+datos[CodVal];
					            arr_datos[index].codigo = CodVal;
					            arr_datos[index].nombre = CodVal+'-'+datos[CodVal];           
					        }

					        $("#wccohoseleccion").autocomplete({
					                source: arr_datos, minLength : 0,
					                select: function( event, ui ) {
					                            var cod_sel = ui.item.codigo;
					                            var nom_sel = ui.item.nombre;
					                            $("#wccohoseleccion").attr("codigo",cod_sel);
					                            $("#wccohoseleccion").attr("nombre",nom_sel);
					                            $("#wccohospitalario" ).val(ui.item.codigo);  
					                        },
					                close: function( event, ui ) {
					                    
					                }
					        });

					    });


			    }

			});
	    }
	    else
		
		var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wempleado='+empleado[0]+'&wcalificador='+calificador+'&wnempleado='+empleado[1]+'&wformulario='+empleado[2]+'&wano='+ano+'&wperiodo='+periodo+'&westado='+empleado[3]+'&wuse='+usuario+'&wnumprueba='+nprueba+'&wcalmax='+calmax+'&wcalmin='+calmin+'&wcalmal='+calmal+'&tipo='+tipo+'&wtipoevaluacion='+tipoevaluacion+'&wcargo='+cargo+'&wuse='+wuse+'&wtema='+tema;

	}


    if (tipoevaluacion=='03' || tipoevaluacion=='04' || tipoevaluacion=='05' || (tipoevaluacion=='01' && tipo !== '2') ){

		$.get(params,{wcodtab : codtab}, function(data) {

	          $('#evaluacioncompetencias').html(data);

	    }).done(function(){
	        
	        var datos = eval('(' + $("#arr_wccohospitalario").val() + ')');
	        var arr_datos = new Array();
	        var index = -1;
	        for (var CodVal in datos)
	        {
	        	index++;
	            arr_datos[index] = {};
	            arr_datos[index].value  = CodVal+'-'+datos[CodVal];
	            arr_datos[index].label  = CodVal+'-'+datos[CodVal];
	            arr_datos[index].codigo = CodVal;
	            arr_datos[index].nombre = CodVal+'-'+datos[CodVal];           
	        }

	        $("#wccohoseleccion").autocomplete({
	                source: arr_datos, minLength : 0,
	                select: function( event, ui ) {
	                            var cod_sel = ui.item.codigo;
	                            var nom_sel = ui.item.nombre;
	                            $("#wccohoseleccion").attr("codigo",cod_sel);
	                            $("#wccohoseleccion").attr("nombre",nom_sel);
	                            $("#wccohospitalario" ).val(ui.item.codigo);  
	                        },
	                close: function( event, ui ) {
	                    
	                }
	        });

	    });

	}

}


function redondear(num)
{
	var original=num;
	if ((original*100%100)>=0.5)
	{
		var result=Math.round(original*100)/100+0.01;
	}
	else
	{
		var result=Math.round(original*100)/100;
	}
	return result;
}


function redondeo2decimales(numero)
{
	var original=parseFloat(numero);
	var result=Math.round(original*100)/100 ;

	return result;
}

function redondeo1decimal(numero)
{
	var original=parseInt(numero);
	var result=Math.round(original*10)/10 ;
	return result;
}

function fnMostrar2( celda ){

		if( $('#'+celda ) ){
			$.blockUI({ message: $('#'+celda ),
							css: { left: ( $(window).width() - 600 )/2 +'px',
								  top: '200px',
								  width: '600px'
								 }
					  });

		}
	}

function grabafirma (elemento)
{
var tema = (document.getElementById('wtema').value);
var valorfirma =document.getElementById(elemento).value;

var calificado = document.getElementById('wempleado').value;
calificado=calificado.split('-');
calificado=calificado[0];

var emp_pmla = (document.getElementById('wemp_pmla').value);

var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wenviofirma='+valorfirma+'&wempleado='+calificado+'&wcalificado='+calificado+'&wtema='+tema;

	$.get(params, function(data) {

	  if(data==0)
	  {
		alert ('clave incorrecta');
	  }
	  else
	  {
	    alert("Evaluacion cerrada satisfactoriamente");
		var ptema = tema;
		var pempleado = document.getElementById('wempleado').value;
		var pcalificador=document.getElementById('wcalificador').value;
		var calculo ='';
		calculo = Programacion_Automatica(ptema , pempleado, pcalificador);

		empleado = document.getElementById('wempleado').value;
		calificador=document.getElementById('wcalificador').value;
		ano=document.getElementById('wano').value;
		periodo=document.getElementById('wperiodo').value;
		formulario=document.getElementById('wformulario1').value;


		divtotal="div"+formulario;
		totalevaluacion= (document.getElementById(divtotal).innerHTML)*1;
		tipoevaluacion=document.getElementById('wtipoevaluacion').value;
		// alert("entro");
		// alert(tipoevaluacion);

		var parametros = "";
        parametros = "consultaAjax=guardaevaluacion&wempleado="+empleado+"&wcalificador="+calificador+"&wnumprueba=1&wano="+ano+"&wperiodo="+periodo+"&wformulario="+formulario+"&wcaltotal="+totalevaluacion+"&wtipoevaluacion="+tipoevaluacion+"&wcalificacion=1&wcalificaciones=1&wemp_pmla="+emp_pmla+"&wtema="+tema;

               try
              {
                var ajax = nuevoAjax();
                ajax.open("POST", "evaluacioncompetencias.php",false);
                ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                ajax.send(parametros);
                //alert(ajax.responseText);
              }catch(e){ alert(e) }

        var calmax	= (document.getElementById('wcalmax').value)*1;
		var calmin	= (document.getElementById('wcalmin').value)*1;
		var calmal	= (document.getElementById('wcalmal').value)*1;
		var tipo = document.getElementById('tipo').value;
        var wnempleado = document.getElementById('wnempleado').value;
		var cargo =  (document.getElementById('wcargo').value);

		var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wuse='+calificador+'&wempleado='+empleado+'&wcalmax='+calmax+'&wcalmin='+calmin+'&wcalmal='+calmal+'&wnumprueba=1&wano='+ano+'&wperiodo='+periodo+'&tipo='+tipo+'&wformulario='+formulario+'&wtipoevaluacion='+tipoevaluacion+'&westado=1&wnempleado='+wnempleado+'&wcalificador='+calificador+'&wcargo='+cargo+'&wtema='+tema;

		$.get(params, function(data) {
			$('#evaluacioncompetencias').html(data);
		});
	  }
    });

}
function  fnRegresar()
{
var tema = (document.getElementById('wtema').value);
var wuse = (document.getElementById('wuse').value);
var emp_pmla = (document.getElementById('wemp_pmla').value);
var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wuse='+wuse+'&wtema='+tema;
	$.get(params, function(data) {
			$('#evaluacioncompetencias').html(data);
		});


}

function SeleccionTema(wcodtema,emp_pmla,wuse,wtema)
{


	var id = wcodtema.id;
	var codtab=$('#wcodtab').val();

	var wcodtema =  wcodtema.id.split('-');
	$('tr[name^=tdc-]').css({'background-color':''});
	$("#"+id).css({'background-color':'yellow'});

	var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wtipoevaluacion='+wcodtema[1]+'&tipo='+wcodtema[0]+'&wuse='+wuse+'&wtema='+wtema+'&wcodtab='+codtab;
	$.get(params, function(data) {
			$('#evaluacioncompetencias').html(data);
		});

}

function cambiarperiodo(wuse,wtipo)
{


	var ano = '';
	var periodo = '' ;
	var periodoano = $('#selcambiaper').val();
	periodoano = periodoano.split('||');
	ano = periodoano[0];
	periodo = periodoano[1];


	var codtab=$('#wcodtab').val();
	var wemp_pmla = $("#wemp_pmla").val();
    var wtema = $("#wtema").val();
	var tipoevaluacion = document.getElementById('wtipoevaluacion').value;


	var params = 'evaluacioncompetencias.php?consultaAjax=&wemp_pmla='+wemp_pmla+'&wtipoevaluacion='+tipoevaluacion+'&tipo='+wtipo+'&wuse='+wuse+'&wtema='+wtema+'&wcodtab='+codtab+'&wperiodo='+periodo+'&wano='+ano;
	$.get(params, function(data) {
			$('#evaluacioncompetencias').html(data);
		});

}


function enterBuscar(ele,hijo,op,form,e)
    {
        tecla = (document.all) ? e.keyCode : e.which;
        if(tecla==13) { $("#"+hijo).focus(); }
        else { return true; }
        return false;
    }

    function cambioImagen(img1, img2)
    {
        $('#'+img1).hide(1000);
        $('#'+img2).show(1000);
    }

	function recargarLista(id_padre, id_hijo, form)
    {
        val = $("#"+id_padre).val();
        if(val != '*')
        {
            $('#'+id_hijo).load(
				"evaluacioncompetencias.php",
				{
					consultaAjax:   '',
					wemp_pmla:  $("#wemp_pmla").val(),
					wtema:      $("#wtema").val(),
					temaselect: $('#temaselect').val(),
					accion:     'load',
					id_padre:   val,
					form:       form
				});
        }
    }

function buscarEnLista(obj_seleccionado, contenedor)
{
	val = $("#"+obj_seleccionado).val();
	encontrado = false;
	$('#'+contenedor+' input[type=checkbox]').each(function() {
		if($(this).val() == val)
		{ encontrado = true; return; }
	});
	return encontrado;
}

function addList(id_padre, id_hijo, form, scc,formulario,gcompetencia,competencia,descriptor)
    {
        // $('#div_req_permisos').hide();
        // $('#div_req_usuarios').hide();
        val = $("#"+id_padre).val();
        if(val == '*')
        {
            $("#"+id_hijo).find('div').each(function() {
                $(this).remove();
            });
        }

        checks = $("#"+id_hijo).find(':checkbox').length;
        if(checks > 0)
        {
            if(buscarEnLista(id_padre, id_hijo) == true) { $("#"+id_padre+" option[value='']").attr("selected",true); return; }
            if(checks == 1)
            {
                if($("#"+id_hijo).find("[id*=pfls_todos]").is(':checked'))
                {
                    $("#"+id_hijo).find("div").remove(); // si existe seleccionado la opción todos, entonces se remueve todo el contenedor de ese checked.
                    //$("#"+id_padre+" option[value='']").attr("selected",true); return;
                }
            }
        }

        $.post("evaluacioncompetencias.php",
            {
                consultaAjax: '',
                wemp_pmla   : $("#wemp_pmla").val(),
                wtema       : $("#wtema").val(),
                temaselect  : $('#temaselect').val(),
                accion      : 'load',
                id_padre    : val,
                consultaAjax : form,
				seccion		: scc,
				wformulario : formulario ,
				wgcompetencia: gcompetencia,
				wcompetencia: competencia,
				wdescriptor:descriptor

            }
            ,function(data) {
                if(checks == 0 && data != '')
                {
                    // $('#div_'+form).remove();
                    // $('#div_'+form).hide();
                }
				//data=data.replace('wuse_pfls','wuse_pfls_'+scc);
                $('#'+id_hijo).append(data);
                $("#"+id_padre+" option[value='']").attr("selected",true);
            }
        );
    }
function desmarcarRemover(obj_seleccionado, contenedor, div_msj,des,formulario,grupocom,comp,des,seleccionado)
{


	var wemp_pmla = (document.getElementById('wemp_pmla').value);
	var ingreso = $('#wingreso').val();
	var tema = (document.getElementById('wtema').value);
	var tipoevaluacion=document.getElementById('wtipoevaluacion').value;
	var ano=document.getElementById('wano').value;
	var periodo=document.getElementById('wperiodo').value;
	var empleado = document.getElementById('wempleado').value;
	var calificador=document.getElementById('wcalificador').value;



	var params = "evaluacioncompetencias.php?consultaAjax=&eliminarseleccionado=si&wcalificacion="+seleccionado+"&wempleado="+empleado+"&wnumprueba=1&wcalificador="+calificador+"&wformulario="+formulario+"&wgrupocom="+grupocom+"&wcomp="+comp+"&wfomurlario="+formulario+"&wperiodo="+periodo+"&wano="+ano+"&wtipoevaluacion="+tipoevaluacion+"&wemp_pmla="+wemp_pmla+"&wtema="+tema+"&wdes="+des+"&wingreso="+ingreso;
	$.get(params, function(data) {

		});



	$('#'+contenedor).remove();

}



</script>

<script type="text/javascript">
    function verSeccion(id){
        $("#"+id).toggle("normal");
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

    #nombreflotante{
        /*position: fixed;
        bottom: 0;
        left: 55%;*/
        position: absolute;
        top:124px;
        left: 5px;
        border: 2px solid #999999;
        background-color: #ffffff;
        color: black;
        font-weight:bold;
        /*height:200px;*/
        /*overflow:scroll;*/
        width:auto;
        /*margin-left: 10px;*/
    }


</style>

</head>

<body>
<?php
}
?>

<?php
/* ------------------------------includes ---------------------------------------------------------------------------------
-------------------------------------------------------------------------------------------------------------------------*/

if(!isset($consultaAjax) || $consultaAjax=='')

{
	echo "<input type='hidden' name='wcodtab' id='wcodtab' value='".$wcodtab."'>";
	include_once("root/comun.php");
	$user_session = explode('-',$_SESSION['user']);
	$user_session = $user_session[1];
	$user_session = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);
	$wusuario = $user_session;
	global $wbasedato;
	global $wuse;

	// parametro que viene desde la URL, indica la base de datos que vendra de la tabla de temas
	// dato quemado
	// parametro que viene desde la URL, indica el tipo de evaluacion, por ahora se van a manejar tres tipos:
	// 01 - - Evaluacion de competencias. (un empleado calificando otro )
	// 02 - - Encuesta Anonima. (un empleado encuestando a un usuario anonimo)
	// 03 - - Encuesta Usuario Registrado. (un empleado calificando a un paciente)
	// dato quemado
	// $wtipoevaluacion = '01';
	// De esta tabla se escoje el tema de los formatos a evaluar

	$q =  " SELECT Forcod, Fordes, 	Fortip FROM ".$wbasedato."_000042 ";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrowcom = mysql_num_rows($res);

	if($numrowcom==0)
	{
	}
	{
	  if (!isset($tipo))
	  {
		// Tabla de temas de evaluaciones
		echo '<table  style= "border: #2A5DB0 2px solid;  "  whidth="400">
				<tr class="encabezadoTabla"   style="cursor:pointer; border-color:#666666;  border-width:1px;">
					<td>
						TEMAS
					</td>
				</tr>';
		$k=0;
		while ($row =mysql_fetch_array($res))
		{
				if (is_int ($k/2))
				   {
					$wcf="fila1";  // color de fondo de la fila
				   }
				else
				   {
					$wcf="fila2"; // color de fondo de la fila
				   }

				echo"<tr style='cursor:pointer;' class='".$wcf."' id = '".$row['Forcod']."-".$row['Fortip']."' name = 'tdc-".$row['Forcod']."' onclick='SeleccionTema(this,\"".$wemp_pmla."\",\"".$wuse."\",\"".$wtema."\")'>
						<td>".($row['Fordes'])."</td>
					</tr>";
				$k++;

		}
		echo '</table><br><br>';
		//---------------------------------------------
	  }
	  else
	  {


		 $mes_periodo = date("m") * 1;
		 $ano_ano	= date("Y") * 1;
		 $mesactual = restarmes($ano_ano,$mes_periodo, $tipo,$conex,$wbasedato);
		 $mesactual = explode("-", $mesactual);

		 echo "<table align='right'><tr><td style='cursor:pointer; color:white; background:#666666; border-color:#666666;  border-width:1px;' onClick='fnRegresar()'><< Regresar </td></tr></table>";
		 $q =  " SELECT Forcod, Fordes, 	Fortip "
			  ." FROM ".$wbasedato."_000042 "
			  ." WHERE Forcod='".$tipo."'" ;

	     $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $row =mysql_fetch_array($res);

		 $qq =  "SELECT Perano, Perper, Perest "
				."  FROM ".$wbasedato."_000009 "
				." WHERE Perfor = '".$tipo."' ";

		 $resq = mysql_query($qq,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qq." - ".mysql_error());
	     echo "<table><tr ><td  style='text-align: center; font-weight: bold; font-size:15pt;'>".($row['Fordes'])."</td></tr>
		 ";
		 if($wtipoevaluacion=='01' )
		 {

		 if($tipo!='01')
		 {
		 echo"<tr><td align='center'><b>PERIODO</b>";
		 echo"<select id='selcambiaper' onchange=cambiarperiodo('".$wuse."','".$tipo."') >";
		 while($rowq = mysql_fetch_array($resq))
		 {
			if(!isset($wperiodo))
			{
				// if ( (($rowq["Perper"] * 1) <=($mesactual[1] * 1) && ($rowq['Perano'] * 1) <= ($mesactual[0] * 1 )) || (($rowq['Perano'] * 1) <= ($mesactual[0] * 1 )))
				// {
				if ( ($rowq["Perper"] * 1) == ($mesactual[1] * 1)  && ($rowq['Perano'] * 1) == ($mesactual[0] * 1) )
				 {
					echo "<option selected value='".$rowq['Perano']."||".$rowq['Perper']."'>".$rowq['Perano']."-".$rowq['Perper']."</option>";
					$ano_ano = $rowq['Perano'];
					$mes_periodo =  $rowq['Perper'];
				 }
				else
					echo "<option value='".$rowq['Perano']."||".$rowq['Perper']."'>".$rowq['Perano']."-".$rowq['Perper']."</option>";
				// }
			}
			else
			{
				// if ( (($rowq["Perper"] * 1) <= $mes_periodo  && ($rowq['Perano'] * 1) <= $ano_ano) || (($rowq['Perano'] * 1) <= ($mesactual[0] * 1 )) )
				// {
					if ( $rowq["Perper"] == $wperiodo  && $rowq['Perano'] == $wano  )
						echo "<option selected value='".$rowq['Perano']."||".$rowq['Perper']."'>".$rowq['Perano']."-".$rowq['Perper']."</option>";
					else
						echo "<option value='".$rowq['Perano']."||".$rowq['Perper']."'>".$rowq['Perano']."-".$rowq['Perper']."</option>";
				// }
			}
		 }
		 echo "</select>";

		 echo "</td></tr>";
		 }
		 }
		 echo "</table>";


	  }
	}

	if(isset ($wtipoevaluacion))
	{

		// perfiles de usuario
		// tipo de evaluacion 01
		if ($wtipoevaluacion == '01')
		{

			$usariocalificador = $user_session;
			$Perfilver = verRelaciones($wuse,$user_session,$wbasedato);

			// if ( strlen($wusuario) > 5)
			// {
				// $borrar = strlen($wusuario) - 5;
				// $wusuario = substr($wusuario,$borrar , 5);

			// }

			if ($wuse != $wusuario && $Perfilver == "no")
			{
				echo $wuse."Use<br>";
				echo $wusuario."usuario<br>";
				echo $Perfilver."perfil<br>";
				echo "NO TIENE PERMISO PARA VER ESTE PERFIL";
				return;
			}

		}


		if ($wtipoevaluacion == '02')
		{
			$usuariocalificador = $user_session;
			$encuestaAnonima = "si";
			$wcalificador=$user_session;

		}

		if ($wtipoevaluacion == '03' or  $wtipoevaluacion=='05' )
		{
			$usuariocalificador = $user_session;
			$encuestaAnonima = "si";
			$wcalificador=$user_session;

		}

	/*-----------------------------------------------------------------------------------------------------------------------*/
		if(!isset($wperiodo))
		{
		if ($wtipoevaluacion != '01')
		{

			//se selecciona el periodo segun el tema
			$q=	" 	SELECT Perano,Perper "
				."    FROM ".$wbasedato."_000009 "
				."   WHERE Perest='on'"
				."     AND Perfor='".$tipo."'";
			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row =mysql_fetch_array($res);

			$wano=$row[0];
			$wperiodo=$row[1];
		}
		else
		{
			//se selecciona el periodo segun el tema
			if($tipo=='01')
			{
				$q=	" 	SELECT Perano,Perper "
					."    FROM ".$wbasedato."_000009 "
					."   WHERE Perest='on'"
					."     AND Perfor='".$tipo."'";
				$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$row =mysql_fetch_array($res);

			}
			else
			{
				$q=	" 	SELECT Perano,Perper "
					."    FROM ".$wbasedato."_000009 "
					."   WHERE Perfor='".$tipo."'"
					."    AND  Perper = '".$mes_periodo."' "
					."    AND  Perano = '".$ano_ano."' ";
			}

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row =mysql_fetch_array($res);

			$wano    =$row[0];
			$wperiodo=$row[1];

		}

		}


/*		$q	="SELECT  Calmax,Calmin,Calmal "
			."  FROM ".$wbasedato."_000034"
			." WHERE Calest = 'on' "
			."   AND Calano = '".$wano."' "
			."   AND Calfor = '".$tipo."' "
			."   AND Calper = '".$wperiodo."'";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row =mysql_fetch_array($res);

		$wcalmax=$row['Calmax'];
		$wcalmin=$row['Calmin'];
		$wcalmal=$row['Calmal'];*/

		$wcalmax = 5;
		$wcalmin = 1;
		$wcalmal = 3;

		$q  ="SELECT  Notval "
			."  FROM ".$wbasedato."_000047"
			." WHERE Notcar = '02'  "
			."   AND Nottde = '01' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row =mysql_fetch_array($res);

		$wcalmin=$row['Notval'];

		$q  ="SELECT  Notval "
			."  FROM ".$wbasedato."_000047"
			." WHERE Notcar = '01'  "
			."   AND Nottde = '01' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row =mysql_fetch_array($res);

		$wcalmax=$row['Notval'];

		$q  ="SELECT  Notval "
			."  FROM ".$wbasedato."_000047"
			." WHERE Notcar = '03'  "
			."   AND Nottde ='01' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row =mysql_fetch_array($res);

		$wcalmal=$row['Notval'];


		if ($wtipoevaluacion == '05')
		{
			$q  ="SELECT  Notval "
			."  FROM ".$wbasedato."_000047, ".$wbasedato."_000048"
			." WHERE Notcar = '02'  "
			."   AND Notgru = Nxtgno"
			."   AND Nxttem = '".$tipo."'  ";

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row =mysql_fetch_array($res);

			$wcalmin=$row['Notval'];

			$q  ="SELECT  Notval "
				."  FROM ".$wbasedato."_000047, ".$wbasedato."_000048"
				." WHERE Notcar = '01'  "
				."   AND Notgru = Nxtgno"
				."   AND Nxttem = '".$tipo."'  ";

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row =mysql_fetch_array($res);

			$wcalmax=$row['Notval'];

			$q  ="SELECT  Notval "
				."  FROM ".$wbasedato."_000047, ".$wbasedato."_000048"
				." WHERE Notcar = '03'  "
				."   AND Notgru = Nxtgno"
				."   AND Nxttem = '".$tipo."'  ";

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row =mysql_fetch_array($res);

			$wcalmal=$row['Notval'];


		}

/*		$q	="SELECT  Mcaest,Mcaper,Mcaano "
				."  FROM ".$wbasedato."_000032"
				." WHERE Mcaucr= '".$wuse."' "
				."   AND Mcauco= '".$wempleado."' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numrowcom = mysql_num_rows($res);*/


		$q	="    SELECT  Mcaest,Mcaper,Mcaano "
				."  FROM ".$wbasedato."_000032 "
				." WHERE Mcaucr= '".$wuse."' "
				."   AND Mcaest= 'on' "
				."   AND Mcaano= '".$wano."' "
				."   AND Mcaper= '".$wperiodo."' "
				."   AND Mcauco= '".$wempleado."' ";

		$res1 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row =mysql_fetch_array($res1);

		$wnumprueba=1;

		$q=	 " 	SELECT Forfor, Forgco, Forcom, Fordes "
			."    FROM ".$wbasedato."_000006, ".$wbasedato."_000005 "
			." 	 WHERE Forfor= '".$wformulario."' "
			."   AND Descod = Fordes "
			."   AND Desest != 'off' "
			."ORDER BY forogc,Forgco,foroco,Forcom,Forord , ".$wbasedato."_000006.Fordes";


		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numrow1 = mysql_num_rows($res);
		
		$i = 0;
		$ig = 0;
		$ic = 0;

		$auxGcompetencia = 0;
		$auxCompetencia = 0;
		$auxDescriptor= 0;


		// se llena los vectores: arr_gcompetencia, arr_competencia, arr_descriptor y arr_conTotal
		while ($row =mysql_fetch_array($res))
		{
		//echo "auxgcompetencia:  ".$auxGcompetencia."      array".$i.":  ".$arr_gcompetencia['Forgco'];
			if($auxGcompetencia != $row['Forgco'] )
			{
				$arr_gcompetencia[$ig] = $row['Forgco'];
				$ig++;
			}
			if($auxCompetencia != $row['Forcom'] )
			{

				$arr_competencia[$ic] = $row['Forcom'];
				$ic++;
			}


			$arr_descriptor[$i] = $row['Fordes'];
			$arr_conTotal[$row['Forfor']][$row['Forgco']][$row['Forcom']][] = $row['Fordes'];

			$auxGcompetencia=$row['Forgco'];
			$auxCompetencia=$row['Forcom'];
			$auxDescriptor=$row['Fordes'];
			$i++;
		}
		$rowspan[]=0;
		for($j=0;$j<count($arr_gcompetencia);$j++)
		{
			//echo count($arr_conTotal["".$arr_gcompetencia[$i].""]["".$arr_competencia[$i].""]);

			$numcompetencias[$j] = count($arr_conTotal["".$wformulario.""]["".$arr_gcompetencia[$j].""]);
			$numdescriptoresxcomp[$j] = 0;
			$numdescriptores[$j] = 0;

			for($i=0;$i<$numrow1;$i++)

			{

				$numdescriptores[$j] = $numdescriptores[$j]  + count($arr_conTotal["".$wformulario.""]["".$arr_gcompetencia[$j].""]["".$arr_competencia[$i].""]);
				$numdescriptoresxcomp[$j]=count($arr_conTotal["".$wformulario.""]["".$arr_gcompetencia[$j].""]["".$arr_competencia[$i].""]);

			}

		}
		

		$q=	" 	SELECT ".$wbasedato."_000002.Fordes, Gcodes, Comdes, Desdes,Gcopes,Gcoapl "
			."    FROM ".$wbasedato."_000006,".$wbasedato."_000002,".$wbasedato."_000003,".$wbasedato."_000004,".$wbasedato."_000005 "
			." 	 WHERE Forfor= '".$wformulario."' "
			."	   AND Forfor = Forcod "
			."	   AND Forgco = Gcocod "
			."	   AND Forcom = Comcod "
			."	   AND Desest != 'off' "
			."	   AND ".$wbasedato."_000006.Fordes = Descod "
			."	ORDER BY forogc,Forgco,foroco,Forcom,Forord , ".$wbasedato."_000006.Fordes";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		$i = 0;
		$ig = 0;
		$ic = 0;

		$auxGcompetencia = "";
		$auxCompetencia = "";
		$auxDescriptor= "";

		// se llena los vectores: arr_gcompetencia, arr_competencia, arr_descriptor y arr_conTotal
		while ($row =mysql_fetch_array($res))
		{
			//echo "auxgcompetencia:  ".$auxGcompetencia."      array".$i.":  ".$arr_gcompetencia['Forgco'];
			if($auxGcompetencia != $row['Gcodes'] )
			{
				$Narr_gcompetencia[$ig][0] = $row['Gcodes']; // descripcion
				$Narr_gcompetencia[$ig][1] = $row['Gcopes']; // porcentaje
				$Narr_gcompetencia[$ig][2] = $row['Gcoapl']; // aplica o no porcentaje
				$ig++;
			}
			if($auxCompetencia != $row['Comdes'] )
			{

				$Narr_competencia[$ic] = $row['Comdes'];
				$ic++;

			}


			$Narr_descriptor[$i] = $row['Desdes'];
			$Narr_conTotal[$row['Fordes']][$row['Gcodes']][$row['Comdes']][] = $row['Desdes'];

			$auxGcompetencia=$row['Gcodes'];
			$auxCompetencia=$row['Comdes'];
			$auxDescriptor=$row['Desdes'];
			$i++;
			$nomformulario = $row['Fordes'];
		}


		$q=	"  SELECT  SUM(Evacal), Evafor, Forcom"
			."   FROM ".$wbasedato."_000006,".$wbasedato."_000007"
			." 	WHERE ".$wbasedato."_000006.id = Evafor "
			."    AND Evaevo = '".$wempleado."' "
			."    AND Evaevr = '".$wcalificador."' "
			."    AND Evaano= '".$wano."'"
			."    AND Evaper= '".$wperiodo."'"
			."	  AND Evafco= Forfor"
			."    AND Evafco='".$wformulario."'"
			." GROUP BY Forcom ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numrow = mysql_num_rows($res);

		while($row =mysql_fetch_array($res))
		{

			$arr_calificacionesxcom[$row['Forcom']] = $row['SUM(Evacal)'] ;

		}

		$q=	"  SELECT  Evacal, Evadat, Evafor , CONCAT(Evafco,'-', Evagco,'-', Evacom,'-', Evades) AS indice"
			."   FROM ".$wbasedato."_000006,".$wbasedato."_000007"
			." 	WHERE Evaevo = '".$wempleado."' "
			."    AND Evaevr = '".$wcalificador."' "
			."    AND Evaano= '".$wano."'"
			."    AND Evaper= '".$wperiodo."'"
			."    AND Evafco = Forfor"
			."    AND Evagco = Forgco"
			."    AND Evacom = Forcom"
			."    AND Evades = Fordes"
			." ORDER BY forogc,Forgco,foroco,Forcom,Forord , ".$wbasedato."_000006.Fordes DESC ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numrow = mysql_num_rows($res);

		while($row =mysql_fetch_array($res))
		{
			if($wtipoevaluacion!='04')
			{
				$arr_calificaciones[$row['indice']] = $row['Evacal'] ;
			}
			else
			{
				$arr_calificaciones[$row['indice']] = $row['Evadat'] ;
			}
		}


		echo "<input type='hidden' name='wtema' id='wtema' value='".$wtema."'>";
		echo "<input type='hidden' name='wuse' id='wuse' value='".$wuse."'>";

		echo "<input type='hidden' name='valor_foco' id='valor_foco' value=''>";
		echo "<input type='hidden' name='wempleado' id='wempleado' value='".$wempleado."' >";
		echo "<input type='hidden' name='wformulario1' id='wformulario1'  value='".$wformulario."'>";
		echo "<input type='hidden' id='wnempleado' value='".$wnempleado."' >";
		echo "<input type='hidden' name='wcalificador' id='wcalificador' value='".$wcalificador."'>";
		echo "<input type='hidden' name='wano' id='wano' value='".$wano."'>";
		echo "<input type='hidden' name='wcargo' id='wcargo' value='".$wcargo."'>";
		echo "<input type='hidden' name='wperiodo' id='wperiodo' value='".$wperiodo."'>";
		echo "<input type='hidden' name='wcalmax' id='wcalmax' value='".$wcalmax."'>";
		echo "<input type='hidden' name='wcalmal' id='wcalmal' value='".$wcalmal."'>";
		echo "<input type='hidden' name='wcalmin' id='wcalmin' value='".$wcalmin."'>";
		echo "<input type='hidden' name='wtipoevaluacion' id='wtipoevaluacion' value='".$wtipoevaluacion."'>";
		echo "<input type='hidden' name='tipo' id='tipo' value='".$tipo."'>";
		echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
		echo "<input type='hidden' name='wnombreevaluacion' id='wnombreevaluacion' value='".$wwnombreevaluacion."'>";
		echo "<input type='hidden' name='whistoria' id='whistoria' value='".$whistoria."'>";
		echo "<input type='hidden' name='wingreso' id='wingreso' value='".$wingreso."'>";
		echo "<input type='hidden' name='wencuesta' id='wencuesta' value='".$wencuesta."'>";

		// cambie esto
		$wanoant= $wano-1;

		$qren= 	"SELECT  Arecdo, Arefor,Areper,Areano,Ideno1,Ideno2,Ideap1,Ideap2,Cconom,Fordes,Forcod,Ideccg,Ccocod"
				." FROM ".$wbasedato."_000058, costosyp_000005,".$wbasedato."_000002,talhuma_000013 "
				."WHERE Arecdr  ='".$wuse."'"
				."  AND Arecdo  = Ideuse "
				."  AND Idecco  = Ccocod "
				."  AND Arefor  = Forcod "
				."  AND Aretem  = '".$tipo."' "
				."  AND Forabr  = 'on' "
				."  AND Fortip  = '".$tipo."' "
				."  AND ( ( Areper <= '".$wperiodo."' And Areano = '".$wano."' ) or ( Areano = '".$wanoant."' ) ) "
				."  AND Ccoemp  = '".$wemp_pmla."' "
				."  AND ".$wbasedato."_000002.Forest='on' "
				."  AND Ideest = 'on'"
				." ORDER BY Cconom,Ideno1,Ideno2,Ideap1,Ideap2,Areano,ABS(Areper) ";
		
		$consulta  = $qren;
		$resren    = mysql_query($qren,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qren." - ".mysql_error());
		$numrowren = mysql_num_rows($resren);

		//echo '<pre>';print_r($qren);echo '</pre>';

		$q= 	"SELECT  Arecdo, Arefor,Areper,Areano,Ideno1,Ideno2,Ideap1,Ideap2,Cconom,Fordes,Forcod,Ideccg,Ccocod"
				." FROM ".$wbasedato."_000058, costosyp_000005,".$wbasedato."_000002,talhuma_000013 "
				."WHERE Arecdr ='".$wuse."'"
				."  AND Arecdo = Ideuse "
				."  AND Idecco = Ccocod "
				."  AND Arefor = Forcod "
				."  AND Forabr = 'on' "
				."  AND Fortip = '".$tipo."' "
				."  AND Areper = '".$wperiodo."'"
				."  AND Areano = '".$wano."' "
				."  AND Ccoemp = '".$wemp_pmla."' "
				."  AND ".$wbasedato."_000002.Forest='on' "
				."  AND Ideest = 'on'"
				." ORDER BY Cconom,Ideno1,Ideno2,Ideap1,Ideap2 ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numrowcom = mysql_num_rows($res);

		// tabla principal: Esta tabla contiene todos los tipos de formularios independiente del tipo de formato que se dibuje.
		echo '<table width="100%" >';
		// ---------------------------------

		// si la evaluacion es del tipo 02 (encuesta-anonima) pinta
		if ($wtipoevaluacion=='02' )
		{
			// Propia Evaluacion
			echo '<tr><td colspan=5>';


			// Empleados para evaluar encabezado
			echo		'<div id="ref_tbingen" align="center">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td>&nbsp;</td>
								</tr>
							</table>
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td align="Left"><a href="#null"  onclick="verSeccion(\'div_empleados\')">ENCUESTAS EN EL PERIODO:'.$wano.'-'.$wperiodo.'</a></td>
								</tr>
							</table>
						</div>';

			echo	'</td></tr><tr><td>';
			//-----------------------------------

			//Empleados a evaluar Datos
			echo'<div id="div_empleados" align="right" class="borderDiv displ">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr class="encabezadoTabla">
							<td align="Left" >
								Nombre de la encuesta
							</td>
						</tr>';
			$r=0;

			// el Fortip = tipo de formulario, siempre debe ser encuesta anonima
			$qencuesta =  "  SELECT ".$wbasedato."_000002.Fordes, ".$wbasedato."_000002.Forcod  "
						 ."    FROM ".$wbasedato."_000002  , ".$wbasedato."_000042 "
						 ."   WHERE ".$wbasedato."_000042.Forcod= ".$wbasedato."_000002.Fortip "
						 ."     AND ".$wbasedato."_000042.Fortip = '02' "
						 ."     AND ".$wbasedato."_000002.Forabr ='on' "
						 ."     AND ".$wbasedato."_000002.Fortip ='".$tipo."'";


			$resencuesta = mysql_query($qencuesta,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qencuesta." - ".mysql_error());


			while ($rowencuesta =mysql_fetch_array($resencuesta))
			{
					if (is_int ($r/2))
					{
						$wcf="fila1";  // color de fondo de la fila
					}
					else
					{
						$wcf="fila2"; // color de fondo de la fila
					}

				echo "<tr  align='left' style='cursor:pointer;' id='".$usariocalificador."|".$rowencuesta["Fordes"]."|".$rowencuesta["Forcod"]."' onclick='seleccionaEmpleado(this,\"".$usariocalificador."\",\"".$wano."\",\"".$wperiodo."\",\"".$usariocalificador."\",\"".$wnumprueba."\",\"".$wcalmax."\",\"".$wcalmin."\",\"".$wcalmal."\"); ilumina(this,\"".$wcf."\")' class='".$wcf."'>
						<td align='left'>
							".$rowencuesta["Fordes"]."
						</td>
					  </tr>";

				$r++;

			}
			echo '</table></div>';
			echo '</td></tr>';

		}

		// si la evaluacion es del tipo 03 (encuesta-con usario registrado) pinta

		if ($wtipoevaluacion=='04'  )
		{
			$qevaluacion = "SELECT Empcod ,".$wbasedato."_000002.Fordes, ".$wbasedato."_000002.Forcod, Fecfin,Fecffi,Fechin,Fechfi  "
						 . "  FROM  ".$wbasedato."_000055 ,".$wbasedato."_000002 Left join ".$wbasedato."_000056 ON  ".$wbasedato."_000002.Forcod = Fecfor"
						 . " WHERE Empcod ='".$wuse."' "
						 . "   AND Empeva = ".$wbasedato."_000002.Forcod"
						 . "   AND ".$wbasedato."_000002.Fortip = '".$tipo."' ";


			$resevaluacion  = mysql_query($qevaluacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qevaluacion." - ".mysql_error());
			$rowevaluacion = mysql_fetch_array($resevaluacion);

			$fechaevaluacion  =date("Y-m-d");
			$horaevaluacion = date("H:i:s");

			$westadoevaluacion='disabled';



			if(strtotime($fechaevaluacion.' '.$horaevaluacion) >= strtotime($rowevaluacion['Fecfin'].' '.$rowevaluacion['Fechin']))
			{
				if( strtotime($rowevaluacion['Fecffi'].' '.$rowevaluacion['Fechfi'])>=strtotime($fechaevaluacion.' '.$horaevaluacion))
				{
					$westadoevaluacion='cerrada';


				}

			}


			$qestadodesemp = "SELECT Count(*)  as cuantos
								FROM ".$wbasedato."_000032
							   WHERE Mcauco = '".$wuse."'
								 AND Mcaucr = '".$wuse."'
								 AND Mcaper = '".$wperiodo."'
								 AND Mcaano = '".$wano."'
								 AND Mcafor = '".$rowevaluacion['Forcod']."'";
			$resestadodesemp  = mysql_query($qestadodesemp,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qestadodesemp." - ".mysql_error());
			$rowestadodesemp = mysql_fetch_array($resestadodesemp);

			if($rowestadodesemp['cuantos'] > 0)
			{
				$westadoevaluacion='ya';
			}

			if( empty($rowevaluacion['Fecffi'])  ){
				$rowevaluacion['Fecffi'] = 'No programada';
				$rowevaluacion['Fecfin'] = 'No programada';
				$rowevaluacion['Fechfi'] = 'NA';
				$rowevaluacion['Fechin'] = 'NA';
				$westadoevaluacion = 'disabled';
			}



			echo '<tr aling="center"><td>';

			echo "<table id='empleadosConocimiento'  align = 'center' >";
			echo "<tr ".$westadoevaluacion." ><td align='center' colspan='4' class='encabezadoTabla'>Evaluaci&oacute;n Pendiente</td></tr>";
			echo "<tr ><td class='fila1' colspan='4' align='center' >".$rowevaluacion['Fordes']."</td></tr>";
			echo "<tr ><td colspan='2' class='encabezadoTabla'>Fecha de apertura</td><td colspan='2' class='encabezadoTabla'>Fecha de cierre</td></tr>";
			echo "<tr >";
			echo "<td class='fila1'  >".$rowevaluacion['Fecfin']."</td>";
			echo "<td class='fila1'  >".$rowevaluacion['Fechin']."</td>";
			echo "<td class='fila1'  >".$rowevaluacion['Fecffi']."</td>";
			echo "<td class='fila1'  >".$rowevaluacion['Fechfi']."</td>";
			echo"</tr>";
			echo "<tr>";
			echo"<td colspan = '4'  class='fila1' align='center' >";
			if($westadoevaluacion=='cerrada')
			{
				echo"<a  id='vinculo|".$rowevaluacion['Empcod']."|".$rowevaluacion['Forcod']."' onclick='seleccionaEmpleado(this)' style='cursor: pointer; font-size: 14pt'>realizar</a>";
			}
			else
			{
				if($westadoevaluacion=='ya')
					echo"<a   id='vinculo".$rowevaluacion['Empcod']."-".$rowevaluacion['Forcod']."' style='cursor: pointer; font-size: 14pt' >Evaluacion ya cerrada</a>";
				else
					echo"<a   id='vinculo".$rowevaluacion['Empcod']."-".$rowevaluacion['Forcod']."' style='cursor: pointer; font-size: 14pt' >En espera de activacion</a>";
			}
			echo"</td>";
			echo"</tr>";
			echo "</table>";

			echo	'</td></tr>';

			//-----------------------------------


		}

		if ($wtipoevaluacion=='03' OR $wtipoevaluacion=='05'   )
		{
			// Propia Evaluacion
			echo '<tr><td>';


			// Empleados para evaluar encabezado
			echo		'<div id="ref_tbingen" align="center">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td>&nbsp;</td>
								</tr>
							</table>
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td><a href="#null"  onclick="verSeccion(\'div_empleados\')">ENCUESTAS EN EL PERIODO:'.$wano.'-'.$wperiodo.'</a></td>
								</tr>
							</table>
						</div>';

			echo	'</td></tr>
					 <tr><td>';
			//-----------------------------------

			//Empleados a evaluar Datos
			echo'<div id="div_empleados" align="right" class="borderDiv displ">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr style="cursor:pointer;" class="encabezadoTabla">
							<td align="left" >
								Nombre de la encuesta
							</td>
						</tr>';
			$r=0;

			// el Fortip = 03  tipo de formato, siempre debe ser encuesta usuario registrado
			// esta consulta saca la lista de los formatos tipo 03
			$qencuesta =  "  SELECT ".$wbasedato."_000002.Fordes, ".$wbasedato."_000002.Forcod  "
						."     FROM ".$wbasedato."_000002  , ".$wbasedato."_000042 "
						."    WHERE ".$wbasedato."_000042.Forcod= ".$wbasedato."_000002.Fortip "
						."      AND ".$wbasedato."_000042.Fortip = '".$wtipoevaluacion."'";

			$resencuesta  = mysql_query($qencuesta,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qencuesta." - ".mysql_error());

			// numero de encuestas encontradas
			$numencuestas = mysql_num_rows($resencuesta);

			//si no encuentra nada
			if($numencuesta<0)
			{
			 echo '<tr class="fila1"><td colspan="4"> No hay encuestas para mostrar</td></tr>';
			}

			while ($rowencuesta = mysql_fetch_array($resencuesta))
			{
					if (is_int ($r/2))
					{
						$wcf ="fila1";  // color de fondo de la fila
					}
					else
					{
						$wcf ="fila2"; // color de fondo de la fila
					}

				echo "<tr style='cursor:pointer;' id='".$usariocalificador."|".$rowencuesta["Fordes"]."|".$rowencuesta["Forcod"]."' onclick='seleccionaEmpleado(this,\"".$usariocalificador."\",\"".$wano."\",\"".$wperiodo."\",\"".$usariocalificador."\",\"".$wnumprueba."\",\"".$wcalmax."\",\"".$wcalmin."\",\"".$wcalmal."\"); ilumina(this,\"".$wcf."\")' class='".$wcf."'>
					  <td align='left'>
					  ".$rowencuesta["Fordes"]." </td>
					  </tr>";
					  //
				$r++;

			}
			echo'	  </table>
					  </div>';
			echo'</tr></td>';
		}

		if ($wtipoevaluacion=='01')
		{
		  // Propia Evaluacion
			echo '<tr  width="100%" ><td>
					<div id="propiaevaluacion" align="center"  width="100%"  >
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td>&nbsp;</td>
								</tr>
							</table>
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr align = "Left "td width="100%">
									<td align = "Left" width="100%"><a href="#null"  onclick="verSeccion(\'div_Propiaevaluacion\')">EVALUACI&Oacute;N DE COMPETENCIAS:'.$wano.'-'.$wperiodo.'</a></td>
								</tr>
							</table>
						</div>
				 </td></tr>
				 <tr><td>
					   <div id="div_Propiaevaluacion" align="right" class="borderDiv displ">
						<table width="100%" border="0" cellspacing="1" cellpadding="1">
							<tr class="encabezadoTabla">
								<td align = "left" >
									Nombre Calificador
								</td>
								<td align = "left" >
									Centro de Costos 
								</td>
								<td align = "left">
									Evaluacion
								</td>
								<td align="Left">
								   Periodo
							    </td>
								<td align = "left">
									Estado
								</td>
							</tr>';
			// trae el nombre del calificador de la evaluacion y deja previsualizar la calificacion que le hicieron
			// $q2=	    "   SELECT  Ajeuco, Ajecco, Ajefor,Ideno1,Ideno2,Ideap1,Ideap2,Cconom,Fordes,Forcod,Ideccg,Ajeucr"
					   // ."     FROM ".$wbasedato."_000008, costosyp_000005,".$wbasedato."_000002,talhuma_000013"
					   // ."    WHERE Ajeuco ='".$wuse."'"
					   // ."      AND Ideuse=Ajeucr "
					   // ."      AND Ccocod=Idecco"
					   // ."      AND Ajefor=Forcod "
					   // ."      AND Fortip='".$tipo."' "
					   // ."      AND Forabr='on' "
					   // ."      AND ".$wbasedato."_000002.Forest='on' "
					   // ." ORDER BY Cconom,Ideno1,Ideno2,Ideap1,Ideap2 ";

			$q2=	    " SELECT Arecdo, Ccocod,Arefor,Ideno1,Ideno2,Ideap1,Ideap2,Cconom,Fordes,Forcod,Ideccg,Arecdr,Areper,Areano
							FROM talhuma_000058, costosyp_000005,talhuma_000002,talhuma_000013
						   WHERE Arecdo ='".$wuse."'
							 AND Ideuse=Arecdr
							 AND Ccocod=Idecco
							 AND Arefor=Forcod
							 AND Forabr='on'
							 AND talhuma_000002.Forest='on'
							 AND Fortip = '".$tipo."'
							 AND Areano = '".$wano."'
							 AND Areper = '".$wperiodo."'
							 AND Ccoemp = '".$wemp_pmla."'
							 AND Aretem = '".$tipo."'							
					    ORDER BY Cconom,Ideno1,Ideno2,Ideap1,Ideap2 ";

			$res2 = mysql_query($q2,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
			$num2 = mysql_num_rows($res2);

			$q3= 		"SELECT Ideno1,Ideno2,Ideap1,Ideap2"
					    ."     FROM talhuma_000013"
					    ."    WHERE Ideuse ='".$wuse."'";

			$res3 = mysql_query($q3,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q3." - ".mysql_error());
			$num3 = mysql_num_rows($res3);

			if($num2==0)
			{
				echo"<tr align = 'left' class='fila1'><td colspan='5'>Usted no tiene asignado una evaluaci&oacute;n o ningun calificador </td></tr>";
			}
			else
			{
				$row2=mysql_fetch_array($res2);
				$row3=mysql_fetch_array($res3);
				echo "<tr  id='".$row2['Ajeuco']."|".$row3['Ideno1']." ".$row3['Ideno2']." ".$row3['Ideap1']." ".$row3['Ideap2']."|".$row2['Forcod']."|".$num2."' onclick='seleccionaEmpleado(this,\"".$row2['Ajeucr']."\",\"".$wano."\",\"".$wperiodo."\",\"".$wuse."\",\"".$wnumprueba."\",\"".$wcalmax."\",\"".$wcalmin."\",\"".$wcalmal."\"); ilumina(this,\"".$wcf."\")' class='fila1' style='cursor:pointer;'><td>".$row2['Ideno1']." ".$row2['Ideno2']." ".$row2['Ideap1']." ".$row2['Ideap2']."
					  </td><td>".$row2['Cconom']."</td><td>".$row2['Fordes']." </td><td>".$wano.'-'.$wperiodo." </td>";


				if($row2['Mcano']=='')
				{
					$estadopropio="Pendiente";
				}
				else
				{
					$estadopropio="Evaluado";
				}

				echo "<td>".$estadopropio."
					</td></tr>";
			}

			echo  '</table>
				   </div>
				   </td></tr>
				   <tr><td>';


			// Empleados para evaluar encabezado
			echo		'<div id="ref_tbingen" align="center">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td>&nbsp;</td>
								</tr>
							</table>
							<table  align = "Left" width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr align = "Left">
									<td><a href="#null"  onclick="verSeccion(\'div_empleados\')">EMPLEADOS A EVALUAR EN EL PERIODO:'.$wano.'-'.$wperiodo.' Y PERIODOS ANTERIORES</a></td>
								</tr>
							</table>
						</div>';

			echo	'<tr><td>
					 <tr><td>';
			//-----------------------------------

			//Empleados a evaluar Datos
			echo'<div id="div_empleados" align="right" class="borderDiv displ">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr class="encabezadoTabla">
							<td align="Left" >
								Nombre
							</td>
							<td align="Left">
								Centro de Costos
							</td>
							<td align="Left">
								Evaluacion
							</td>
							<td align="Left">
								Periodo
							</td>
							<td align="Left">
								Estado
							</td>
						</tr>';
			$r=0;

			if ($tipo=='2'){



				while ($row =mysql_fetch_array($resren))
				{

						if (is_int ($r/2))
							$wcf="fila1";  // color de fondo de la fila
						else
							$wcf="fila2"; // color de fondo de la fila

	 
						$q1	="SELECT  Mcaest "
							."  FROM ".$wbasedato."_000009, ".$wbasedato."_000032"
							." WHERE Perano= Mcaano "
							."   AND Perper= Mcaper "
							."   AND Perfor= '".$tipo."' "
							//."   AND Perest= 'on'"
							."   AND Mcaucr= '".$wuse."' "
							."   AND Mcauco = '".$row['Arecdo']."' "
							."   AND Mcafor = '".$row['Forcod']."' "
							."   AND Perano = '".$row['Areano']."' "
							."   AND Perper = '".$row['Areper']."' ";

							//echo '<pre>';print_r($qren);echo '</pre>';
						
						$res1 = mysql_query($q1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$numrowcom1 = mysql_num_rows($res1);
						
						if  ( $numrowcom1 > 0 && ( ( (int)$row['Areano'] < (int)$wano ) || ( (int)$row['Areper'] < (int)$wperiodo && $row['Areano'] = $wano )) )
							{ 


					         }
						else{
								if  ($row['Arefor'] == '')
								{
	
									echo"<tr  id='".$row['Arecdo']."|".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."|".$row['Forcod']."|".$numrowcom1."' onclick='seleccionaEmpleado(this,\"".$wuse."\",\"".$row['Areano']."\",\"".$row['Areper']."\",\"".$wuse."\",\"".$wnumprueba."\",\"".$wcalmax."\",\"".$wcalmin."\",\"".$wcalmal."\",\"".$row['Ideccg']."\"); ilumina(this,\"".$wcf."\",\"".$row['Ideccg']."\")' class='".$wcf."' style='cursor:pointer;'>
									 <td align='Left' >
										".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."
									 </td>
									 <td align='Left' >
										".$row['Cconom']."
									 </td>
									 <td align='Left' >
										".$row['Fordes']."
									 </td>
									 <td align='Left' >
										".$row['Areano'].'-'.$row['Areper']."
									 </td>
									 <td align='Left' >";

									 if ( $numrowcom1 > 0 )
										  echo"<font style='color: green'>Evaluado</font>";
									 else
										  echo"<font style='color: red'>Pendiente</font>";

								} 
								else
								{
									if ( (int)$row['Areper'] <= (int)$wperiodo || (int) $row['Areano'] < (int)$wano )
									{

										echo"<tr  id='".$row['Arecdo']."|".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."|".$row['Forcod']."|".$numrowcom1."' onclick='seleccionaEmpleado(this,\"".$wuse."\",\"".$row['Areano']."\",\"".$row['Areper']."\",\"".$wuse."\",\"".$wnumprueba."\",\"".$wcalmax."\",\"".$wcalmin."\",\"".$wcalmal."\",\"".$row['Ideccg']."\"); ilumina(this,\"".$wcf."\")' class='".$wcf."' style='cursor:pointer;'>
										 <td align='Left' >
											".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."
										 </td>
										 <td align='Left' >
											".$row['Cconom']."
										 </td>
										 <td align='Left' >
											".$row['Fordes']." 
										 </td>
										 <td align='Left' >
											".$row['Areano'].'-'.$row['Areper']."
										 </td>
										 <td  align='Left' >";

										 $valestado = 'S';

										if ( $numrowcom1 > 0 )
											 echo"<font style='color: green'>Evaluado</font>";
										else
											 echo"<font style='color: red'>Pendiente</font>";
									} 
								}


								echo "</td>
									</tr>";
								$r++;
						}

				}// fin while

		    } // fin $tipo='2'
		  	else{

		  		while ($row =mysql_fetch_array($res))
				{
					if (is_int ($r/2))
						$wcf="fila1";  // color de fondo de la fila
					else
						$wcf="fila2"; // color de fondo de la fila

			       //$tipo='01';
					$q	="SELECT  Mcaest "
						."  FROM ".$wbasedato."_000009, ".$wbasedato."_000032"
						." WHERE Perano= Mcaano "
						."   AND Perper= Mcaper"
						."   AND Perfor= '".$tipo."' "
						//."   AND Perest= 'on'"
						."   AND Mcaucr= '".$wuse."' "
						."   AND Mcauco= '".$row[0]."' "
						."   AND Mcafor= '".$row['Forcod']."' "
						."   AND Perano = '".$wano."' "
						."   AND Perper = '".$wperiodo."' ";
					//cambio
					$consultaestado= $q;
					$res1 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$numrowcom1 = mysql_num_rows($res1);

					 if  ($row['Arefor']=='')
					 {
						echo"<tr  id='".$row[0]."|".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."|".$row['Forcod']."|".$numrowcom1."' onclick='seleccionaEmpleado(this,\"".$wuse."\",\"".$wano."\",\"".$wperiodo."\",\"".$wuse."\",\"".$wnumprueba."\",\"".$wcalmax."\",\"".$wcalmin."\",\"".$wcalmal."\",\"".$row['Ideccg']."\"); ilumina(this,\"".$wcf."\",\"".$row['Ideccg']."\")' class='".$wcf."' style='cursor:pointer;'>
						 <td align='Left' >
							".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."
						 </td>
						 <td align='Left' >
							".$row['Cconom']."
						 </td>
						 <td align='Left' >
							".($row['Fordes'])."
						 </td>
						 <td align='Left' >
							".$row['Areano'].'-'.$row['Areper']."
						 </td>
						 <td align='Left' >";

						 if ($numrowcom1 > 0)
							echo"<font style='color: green'>Evaluado</font>";

						 else
							echo"<font style='color: red'>Pendiente</font>";

					} 
					else
					{
							echo"<tr  id='".$row[0]."|".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."|".$row['Forcod']."|".$numrowcom1."' onclick='seleccionaEmpleado(this,\"".$wuse."\",\"".$wano."\",\"".$wperiodo."\",\"".$wuse."\",\"".$wnumprueba."\",\"".$wcalmax."\",\"".$wcalmin."\",\"".$wcalmal."\",\"".$row['Ideccg']."\"); ilumina(this,\"".$wcf."\")' class='".$wcf."' style='cursor:pointer;'>
							 <td align='Left' >
								".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."
							 </td>
							 <td align='Left' >
								".$row['Cconom']."
							 </td>
							 <td align='Left' >
								".$row['Fordes']."
							 </td>
							 <td align='Left' >
							    ".$row['Areano'].'-'.$row['Areper']."
						     </td>
							 <td  align='Left' >";

							 if ($numrowcom1 > 0)
								echo"<font style='color: green'>Evaluado</font>";

							 else
								echo"<font style='color: red'>Pendiente</font>";
					}


					echo "</td>
						</tr>";
					$r++;

				}
				echo' </table>
					  </div>';

				echo'</tr></td>';
		// ------------------------------------------
		    }
		echo'<tr><td colspan=5>';
		}

		if( isset($cargacco) AND $cargacco=='si' OR ($cargaLista=='si') )
		{

			$basedatoscco = consultarAliasPorAplicacion($conex, $wbasedato, 'centrocostos');
			// de la 49

            // Se cambiar carga de centro de costos a uno tipo array para el campo autocompletar
            $arraycentro = array(); 

			if ($wtipoevaluacion=='05')
			{
				if ($basedatoscco == 'costosyp_000005'){
					$q =" SELECT  DISTINCT(Enccco) AS Ccocod, Cconom "
					   ."  FROM  ".$wbasedato."_000049 , ".$basedatoscco." "
					   ." WHERE Encenc = '".$wformulario."' "
					   ."   AND Enccco = Ccocod"
					   ."   AND Ccoemp = '".$wemp_pmla."'"
					   ."   AND Encese = 'pendiente' ";
				}
				else{
					$q =" SELECT  DISTINCT(Enccco) AS Ccocod, Cconom "
					   ."  FROM  ".$wbasedato."_000049 , ".$basedatoscco." "
					   ." WHERE Encenc = '".$wformulario."' "
					   ."   AND Enccco = Ccocod"
					   ."   AND Encese = 'pendiente' ";

				}

			}
			else
			{
				if ($basedatoscco == 'costosyp_000005'){
					$q = " SELECT DISTINCT(Enccco) AS Ccocod, Cconom "
				       . "   FROM ".$wbasedato."_000049 , ".$basedatoscco."  "
				       . "  WHERE Encper = '".$wperiodo."'"
				       . "    AND Encano = '".$wano."' "
				       . "	  AND Enccco = Ccocod "
				       . "    AND Encenc = '".$wformulario."'"
				       . "    AND Ccoemp = '".$wemp_pmla."'"
				       ."     AND Encese = 'pendiente' ";
			    }
			   else{
				   
				   if($wbasedato == 'encumage')
				   {
					   $q = " SELECT DISTINCT(Enccco) AS Ccocod, Cconom "
						   . "   FROM ".$wbasedato."_000049 , ".$basedatoscco."  "
						   . "	  WHERE Enccco = Ccocod "
						   . "    AND Encenc = '".$wformulario."'"
						   ."     AND Encese = 'pendiente' ";
					   
				   }
				   else
				   {
						$q = " SELECT DISTINCT(Enccco) AS Ccocod, Cconom "
						   . "   FROM ".$wbasedato."_000049 , ".$basedatoscco."  "
						   . "  WHERE Encper = '".$wperiodo."'"
						   . "    AND Encano = '".$wano."' "
						   . "	  AND Enccco = Ccocod "
						   . "    AND Encenc = '".$wformulario."'"
						   ."     AND Encese = 'pendiente' ";
					   
					
				   }
			   		
			   }

			}

			$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
            while ($row = mysql_fetch_assoc($res))
                  {
                     $arraycentro[$row['Ccocod']] = ($row['Cconom']);
                  }


			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numrowcom = mysql_num_rows($res);

			//-----------------------------
			echo "<tr><td colspan=5>";
			echo'<div id="div_ccomedico" align="right" class="borderDiv displ">';
			if ($numrowcom == 0 )
			{
				echo "<table align='center'><tr><td>No hay datos para mostrar</td></tr></table>";
			}
			else
			{
				echo "<table align='center' id='tabccoclinicos'>";

				echo "<tr align='left' class='fila1'>";
				echo "<td align='left'><b>Centro de Costos</b>";
				echo "</td>";
				echo "<td align='left'>";
				echo '<input type="hidden" id="arr_wccohospitalario" value=\''.json_encode($arraycentro).'\' >';
                echo '<input type="text" id="wccohoseleccion" codigo="" nombre="" size=80>';
                echo '<input type="hidden" id="wccohospitalario" name="wccohospitalario" size=80>';

				// 2016- 10 -10. Arleyda Insignares C. Se cambia el campo Centro de Costos por un autocompletar
				
				/*echo "<select id='wccohospitalario' >";
				while($row =mysql_fetch_array($res))
				{
					if ($wccohospitalario == $row['Ccocod'])
					{
						echo "<option selected value='".$row['Ccocod']."'>".$row['Cconom']."</option>";
					}
					else
					{
					  echo "<option  value='".$row['Ccocod']."'>".$row['Cconom']."</option>";
					}
				}
				echo "<select>";*/
			}
			echo "</td>";

			echo "</tr>";
			echo "<tr align='center'>";
			echo "<td align='center' colspan='2'><input type='button' Value='Buscar' onClick='ConsultarLista()'/></td>";
			echo "</tr>";

			echo "</table>";
			echo "</div>";
			// fin  segundo tr
			echo "<div id='CambiarEstado' class='fila2' align='middle'  style='display:none;width:100%;cursor:default' >";
					echo "<br><br>";
					echo "<input type='hidden' id='estado_historia_encuestado'></input>";
					echo "<input type='hidden' id='estado_ingreso_encuestado'></input>";
					echo "<table style='border:#2A5DB0 1px solid'>";

					echo "<tr class='encabezadoTabla'>
							<td align='Center' colspan='2'>ESTADO
							</td>
						  </tr>";
					echo"<tr class='fila1'>
							<td align='Left' >
								<b>Nombre<b>
							</td>
							<td align='Left' >
								<div id='estado_nombre_encuestado'>

								</div>
							</td>
						</tr>";
					echo"<tr class='fila1'>
							<td align='Left' >
								<b>Encuesta<b>
							</td>
							<td align='Left' >
								<div id='estado_nombre_encuesta'>

								</div>
							</td>
						</tr>";
					echo"<tr class='fila1'>
							<td align='Left' >
								<b>Estado Actual<b>
							</td>
							<td align='Left' >
								<select class = 'selectestadoencuesta'>
									<option value ='pendiente'>Pendiente</option>
									<option value ='rechazado'>Rechazado</option>
								</select>
							</td>
						</tr>";
					echo"<tr class='fila1'>
							<td align='Left' >
								<b>Nota:<b>
							</td>
							<td align='Left' >
								<textarea id='mestadoencuesta' rows='4'  cols='30'></textarea>
							</td>
						</tr>";
					echo"<tr class='fila2' align='center' >
							<td colspan='2' align='center' >
								<input type = 'button' value='Grabar' onClick='grabaCambioEstado(); $.unblockUI(); ' style='width:100'>
								<input type = 'button' value='Cancelar' onClick='$.unblockUI();' style='width:100'>
							</td>
							<td>
						</tr>";

			echo"</table><br><br></div>";
			echo "</td></tr>";



		}
		echo'</td></tr><tr><td colspan=5>';

		if(isset($cargaLista) AND $cargaLista=='si')
		{

			if($wbasedato=="encumage")
			{
				$q= "SELECT Encced,Encenc,Enchis,Encing,Encno1,Encno2,Encap1,Encap2,Enceda,Encent,Encdia,Enctel,Encfec,Enchab ,Encese,Enccom,Encper"
			   ."  FROM  ".$wbasedato."_000049 "
			   ." WHERE Encenc = '".$wformulario."' "
			   ."   AND Enccco = '".$wccohospitalario."'"
			   ."   AND Encese = 'pendiente'"
			   ."   AND (Encpob = '' OR Encpob ='paciente') ";
				
			}
			else
			{
				$q= "SELECT Encced,Encenc,Enchis,Encing,Encno1,Encno2,Encap1,Encap2,Enceda,Encent,Encdia,Enctel,Encfec,Enchab ,Encese,Enccom,Encper"
			   ."  FROM  ".$wbasedato."_000049 "
			   ." WHERE Encenc = '".$wformulario."' "
			   ."   AND Enccco = '".$wccohospitalario."'"
			   ."   AND Encese = 'pendiente'"
			   ."   AND (Encpob = '' OR Encpob ='paciente') "
			   ."   AND  Encano = '".$wano."' "
			   ."   AND  Encper = '".$wperiodo."' ";
				
			}
		

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			echo'<div id="div_ccomedico" align="Left" class="borderDiv displ">';

			if($num != 0)
			{
				echo "<b>Lista Pacientes</b>";
				echo "<table id='tablista' align='center'>";

				echo "<tr align='Left' class='encabezadoTabla'>";
				echo " <td >Nombre</td>";
				echo " <td>N. Documento</td>";
				echo " <td>Historia</td>";
				echo " <td>Ingreso</td>";
				echo " <td>Habitacion</td>";
				echo " <td>telefono</td>";
				echo " <td>edad</td>";
				echo " <td>Nota</td>";
				echo  "<td>Estado</td>";

				$i=0;
				while ($row =mysql_fetch_array($res))
				{
					if (($i%2)==0)
					{
					$wcf="fila1";  // color de fondo de la fila
					}
					else
					{
					$wcf="fila2"; // color de fondo de la fila
					}
					echo"<tr  class='".$wcf."' >";
					echo"<td ><a style='cursor:pointer;' onClick='seleccionaPaciente(\"".$row['Encno1']." ".$row['Encno2']." ".$row['Encap1']." ".$row['Encap2']."\",\"".$row['Enchis']."\",\"".$row['Encing']."\",\"".$row['Encenc']."\",\"".$wccohospitalario."\" ,  \"\" , \"\" ,  \"".$row['Encper']."\");'>".$row['Encno1']." ".$row['Encno2']." ".$row['Encap1']." ".$row['Encap2']."  </a></td>";
					echo"<td>".$row['Encced']."</td>";
					echo"<td>".$row['Enchis']."</td>";
					echo"<td>".$row['Encing']."</td>";
					echo"<td>".$row['Enchab']."</td>";
					echo"<td>".$row['Enctel']."</td>";
					echo"<td>".$row['Enceda']."</td>";
					echo"<td id='notaencuesta".$row['Enchis']."-".$row['Encing']."'>".$row['Enccom']."</td>";
					echo"<td><a onclick='muestraEstado( \"CambiarEstado\",\"".$row['Encno1']." ".$row['Encno2']." ".$row['Encap1']." ".$row['Encap2']."\",\"".$row['Encenc']."\",\"".$row['Enchis']."\",\"".$row['Encing']."\",\"".$row['Enccom']."\")' style='cursor:pointer; font-size:8pt; color: green; float: right;'>".$row['Encese']."</a></td>";
					echo"</tr>";
					$i++;

				}
				echo "</table>";
			}
			else
			{
/*              Para chequear en caso de que no aparezca tabla de empleados o autocompletar centro de costos
				echo " wformulario ".$wformulario;
			    echo " wccohospitalario ".$wccohospitalario;
			    echo " wano ".$wano;
			    echo " wperiodo ".$wperiodo;*/
	        }

			echo "<br>";
			echo "<br>";
			echo "<br>";


			if ($wtipoevaluacion=='05')
			{
					$q= "SELECT Encced,Encenc,Enchis,Encing,Encno1,Encno2,Encap1,Encap2,Enceda,Encent,Encdia,Enctel,Encfec,Enchab ,Encese,Enccom , Encper, Encano"
					   ."  FROM  ".$wbasedato."_000049 "
					   ." WHERE Encenc = '".$wformulario."' "
					   ."   AND Enccco = '".$wccohospitalario."'"
					   ."   AND Encese = 'pendiente'"
					   ."   AND ( Encpob ='empleado') ";
			}
			else
			{

				$q= "SELECT Encced,Encenc,Enchis,Encing,Encno1,Encno2,Encap1,Encap2,Enceda,Encent,Encdia,Enctel,Encfec,Enchab ,Encese,Enccom,Encper,Encano"
					   ."  FROM  ".$wbasedato."_000049 "
					   ." WHERE Encenc = '".$wformulario."' "
					   ."   AND Enccco = '".$wccohospitalario."'"
					   ."   AND Encese = 'pendiente'"
					   ."   AND ( Encpob ='empleado') "
					   ."   AND  Encano = '".$wano."' "
					   ."   AND  Encper = '".$wperiodo."' ";



			}

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);

			if($num != 0)
			{
				echo "<b>Lista Empleados</b>";
				echo "<table id='tablista' align='center'>";

				echo "<tr align='Left' class='encabezadoTabla'>";
				echo " <td >Nombre</td>";
				echo " <td>Codigo</td>";
				echo " <td>Cargo</td>";
				echo " <td>Nota</td>";
				echo  "<td>Estado</td>";

				$i=0;
				while ($row =mysql_fetch_array($res))
				{
					if (($i%2)==0)
					{
					$wcf="fila1";  // color de fondo de la fila
					}
					else
					{
					$wcf="fila2"; // color de fondo de la fila
					}



					echo"<tr  class='".$wcf."' >";

					if ($wtipoevaluacion=='05')
					{
						echo"<td ><a style='cursor:pointer;' onClick='seleccionaPaciente(\"".$row['Encno1']." ".$row['Encno2']." ".$row['Encap1']." ".$row['Encap2']."\",\"".$row['Enchis']."\",\"".$row['Encing']."\",\"".$row['Encenc']."\",\"".$wccohospitalario."\",\"".$row['Encper']."\",\"".$row['Encano']."\" ,  \"\");'>".$row['Encno1']." ".$row['Encno2']." ".$row['Encap1']." ".$row['Encap2']." </a></td>";
					}
					else
					{
						echo"<td ><a style='cursor:pointer;' onClick='seleccionaPaciente(\"".$row['Encno1']." ".$row['Encno2']." ".$row['Encap1']." ".$row['Encap2']."\",\"".$row['Enchis']."\",\"".$row['Encing']."\",\"".$row['Encenc']."\",\"".$wccohospitalario."\",\"".$row['Encper']."\",\"".$row['Encano']."\" ,  \"\");'>".$row['Encno1']." ".$row['Encno2']." ".$row['Encap1']." ".$row['Encap2']." </a></td>";

					}

					echo"<td>".$row['Enchis']."</td>";

					$querycargo = "SELECT  Cardes
										FROM talhuma_000013 , root_000079
										WHERE Ideuse = '".$row['Enchis']."'
										AND Ideccg = Carcod";

					$rescargo = mysql_query($querycargo,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querycargo." - ".mysql_error());
					$rowcargo =mysql_fetch_array($rescargo);


					echo"<td>".$rowcargo['Cardes']."</td>";

					echo"<td id='notaencuesta".$row['Enchis']."-".$row['Encing']."'>".$row['Enccom']."</td>";
					echo"<td><a onclick='muestraEstado( \"CambiarEstado\",\"".$row['Encno1']." ".$row['Encno2']." ".$row['Encap1']." ".$row['Encap2']."\",\"".$row['Encenc']."\",\"".$row['Enchis']."\",\"".$row['Encing']."\",\"".$row['Enccom']."\")' style='cursor:pointer; font-size:8pt; color: green; float: right;'>".$row['Encese']."</a></td>";
					echo"</tr>";
					$i++;

				}
				echo "</table>";
			}
			echo '</div >';

		}


		echo"</td></tr><tr><td colspan=5>";

		if( isset($wempleado) AND $wempleado!='' AND $wformulario !='' )
			{
				if ($wtipoevaluacion=='01')
				{

				  $q = "SELECT * "
					 . "  FROM ".$wbasedato."_000032 "
					 . " WHERE Mcauco = '".$wempleado."' "
					 . "   AND Mcaucr = '".$wcalificador."' "
					 . "   AND Mcatfo = '01' "
					 . "   AND Mcafor = '".$wformulario."' "
					 . "   AND Mcaano = '".$wano."' "
					 . "   AND Mcaper = '".$wperiodo."' " ;

				  $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				  $num = mysql_num_rows($res);

				  if($num != 0)
				  {
					$westado='1';
				  }
				  else
				  {
					$westado='2';
				  }
				//echo "<table><tr><td>hola".$q."</td></tr></table>";
				}


				// si el formato de evaluacion es 03 (encuesta Usuario registrado) se averigua si la persona ya hizo la evaluacion
				if ($wtipoevaluacion=='03'  OR $wtipoevaluacion=='05' )
				{

				  $q = "SELECT * "
					 . "  FROM ".$wbasedato."_000032 "
					 . " WHERE Mcauco = ".$wempleado." "
					 . "   AND Mcatfo = '".$wtipoevaluacion."' "
					 . "   AND Mcafor = '".$wformulario."' "
					 . "   AND Mcaano = '".$wano."' "
					 . "   AND Mcaper = '".$wperiodo."' " ;

				  $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				  $num = mysql_num_rows($res);

				  if($num != 0)
				  {
					$westado='1';
				  }
				  else
				  {
					$westado='2';
				  }

				}

				if ($wtipoevaluacion=='04')
				{

				  $q = "SELECT * "
					 . "  FROM ".$wbasedato."_000032 "
					 . " WHERE Mcauco = ".$wempleado." "
					 . "   AND Mcatfo = '04' "
					 . "   AND Mcafor = '".$wformulario."' "
					 . "   AND Mcaano = '".$wano."' "
					 . "   AND Mcaper = '".$wperiodo."' " ;


				  $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				  $num = mysql_num_rows($res);

				  if($num != 0)
				  {
					$westado='1';
				  }
				  else
				  {
					$westado='2';
				  }

				}
				//--------------------------------------


				echo'<br><br>
					<div id="ref_tbingen" align="center">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">

							<tr align="center">
								<td class="encabezadoTabla" align="center" >

									<div id="nombreflotante" style="text-align: left; font-weight: bold; font-size:10pt;">
									<table align="center">
									<tr>
										<td align="center">';
										if($wtipoevaluacion=='01' OR $wtipoevaluacion=='04' )
										{
										 echo'<span>Evaluado:</span><span style = "font: italic bold 12px arial, sans-serif;"><br>'.str_replace(' ','<br>', ucwords(strtolower(trim($wnempleado)))).'</span>';
										}
										else
										{
										 echo'<span>Encuestado:</span><span style = "font: italic bold 12px arial, sans-serif;"><br>'.str_replace(' ','<br>', ucwords(strtolower(trim($wnempleado)))).'</span>';
										}

										echo'</td>
										</tr>
									</table>
									</div>
								</td>
							</tr>
						</table>
						<br>';
						if ($wtipoevaluacion!='04')
						{
							echo'<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td align="Left"><a href="#null" onclick="javascript:verSeccion(\'div_infgeneral\');" >INFORMACI&Oacute;N GENERAL</a></td>
								</tr>
							</table>';
						}
					echo'</div>';


				//echo   '</tr></td><tr><td>';

				$q= 	 "SELECT Fordes "
						."  FROM ".$wbasedato."_000002 "
						." WHERE Forcod ='".$wformulario."'";

				$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$row =mysql_fetch_array($res);

				if ($wtipoevaluacion!='04')
				{
					echo"<div id='div_infgeneral' align='center' class='borderDiv displ'>
							<table width='100%' >";

					// si es del tipo evaluacion 01 (evaluacion interna) se pinta una fila con el nombre del empleado a evaluar
					if ($wtipoevaluacion=='01')
					{
						echo "   <tr align='center' class='fila2'>
									<td align='Left' width='180' colspan='1' class='encabezadoTabla'>Nombre del Evaluado:</td>
									<td align='Left' >".$wnempleado."</td>
								  </tr>";
					}
					//------------------------------

						echo	"<tr align='center' class='fila1' >
									<td align='Left' width='180' colspan='1' class='encabezadoTabla'>Periodo:</td>
									<td align='Left' align='Left' >".$wano."-".$wperiodo."</td>
								  </tr>
								  <tr>
									<td>
									</td>
								  </tr>
								  <tr class='fila2'>
									<td align='Left' colspan='5'><p><strong>INSTRUCCIONES</strong></p>
									  <p>Teniendo en cuenta el material de soporte o de evidencia necesario para    evaluar las competencias, califique los descriptores determinados para cada    competencia:</p>
									  <p>-Antes de iniciar la calificaci&oacute;n en el formato asegurese que sea el    indicado para el cargo que va a evaluar.</p>
									  <p>- Lea detenidamente la definici&oacute;n de cada competencia.</p>
									  <p><strong>- CALIFIQUE DE ".$wcalmin." A ".$wcalmax." CADA CRITERIO:</strong></p>
									  <p>1= No se Evidencia, 2= Insatisfactorio, 3= Aceptable, 4 = Bueno, 5 = Sobresaliente. Recuerde que tiene flexibilidad en la calificaci&oacute;n agregando decimales    a la misma, ejemplo 3.5 / 4.8, etc.</p>
									  <p><br />
									NOTA: Autom&aacute;ticamente el sistema dar&aacute; el resultado de la nota de acuerdo al    peso que cada variable tiene asignada.</p></td>
								  </tr>
							</table>";
					echo'</div>';

				}
				// mirar periodos anteriores
				$q =" SELECT Mcaano, Mcaper, Ideno1, Ideno2, Ideap1, Ideap2,Mcaucr
						FROM ".$wbasedato."_000032, ".$wbasedato."_000009, talhuma_000013
					   WHERE Mcauco ='".$wempleado."'
						 AND Mcafor ='".$wformulario."'
						 AND Mcaano = Perano
						 AND Mcaper = Perper
						 AND Perest = 'off'
						 AND Mcaucr = Ideuse
						 AND Perfor = '".$tipo."'
					ORDER BY Mcaano, Mcaper, Ideno1,Ideno2,Ideap1,Ideap2";


				$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());



				echo"</tr></td>
					 <tr ><td colspan=5>
							<div id='ref_tbeva' align='left'>
								<br />
								<table width='100%' border='0' cellspacing='0' cellpadding='0'>
									<tr>
										<td width='100%'><a href='#null'  onclick='verSeccion(\"div_evaluacion\")'><font style='text-transform: uppercase;'>EVALUACI&Oacute;N: ".($row['Fordes'])."</font></a></td>
									</tr>
								</table>
							</div>";


				echo '<div id="div_evaluacion" align="center" class="borderDiv displ">';

				if ($wtipoevaluacion=='01')
				{
					echo'<table align="right" width="200" >
								<tr class="encabezadoTabla">
									<td width="300" align="Center"><b>OTRO PERIODO :</b>
										<select  name="wperiodo2" id="wperiodo2" onchange="seleccionaPeriodo(this,\''.$wuse.'\')" >';
									echo	  "<option value='ninguna' selected>---Seleccione----</option>";
									While($row = mysql_fetch_array($res))
												{

													if(isset($wperiodo) AND ($wperiodo==$row['Mcaper'] AND $wano==$row['Mcaano']))
														{
																echo	  "<option value='".$row['Mcaano']."-".$row['Mcaper']."-".$row['Perest']."-".$row['Mcaucr']."' selected>".$row['Mcaano']."-".$row['Mcaper']."-".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</option>";
														}
													else
														{
																echo	  "<option value='".$row['Mcaano']."-".$row['Mcaper']."-".$row['Perest']."-".$row['Mcaucr']."' >".$row['Mcaano']."-".$row['Mcaper']."-".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</option>";
														}
												}
									echo" </select>
									</td>
								</tr>
							</table>";
				}

				if ($wtipoevaluacion=='05')
				{

					$q_anterior =" SELECT encper, encfce,encano
									FROM ".$wbasedato."_000049
								   WHERE Enchis = '".$wempleado."'
									 AND Encenc	= '".$wformulario."'
									 AND Encese = 'cerrado' ";


					$res_anterior = mysql_query($q_anterior,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q_anterior." - ".mysql_error());



					echo'<table align="right" width="200" >
								<tr class="encabezadoTabla">';

									echo'<td width="300" align="Center"><b>Ver calificaciones anteriores:</b>
										<select  id="select_ante_05" name="wperiodo_05" id="wperiodo_05" onchange="seleccionaPeriodo_05()" >';
									echo	  "<option value='ninguna' selected>---Seleccione----</option>";
									While($row_anterior = mysql_fetch_array($res_anterior))
									{


											echo "<option value='".$row_anterior['encano']."-".$row_anterior['encper']."-".$wempleado."-".$wformulario."' wano='".$row_anterior['encano']."' wperiodo ='".$row_anterior['encper']."' wempleado='".$wempleado."' wformulario='".$wformulario."' >".$row_anterior['encfce']."-".$row_anterior['encper']."</option>";

									}
									echo" </select>
									</td>
								</tr>
							</table>";
				}
				echo 	"<table width='100%' align='center'>";

				$i=0;
				$c=0;

				//While de grupo de competencias
				While($i < count($arr_gcompetencia))
				{
					//echo"<div class='competencia'>";
					echo"<tr ><td>&nbsp;<td></tr>

						<tr align='Left' width='100%'  class='encabezadoTabla'>";

							if ($wtipoevaluacion=='05')
							{
								echo"<td width='100%' align='Left' colspan='6'><div align='center'><font style='text-transform: uppercase;'>VALORACI&Oacute;N DE  COMPETENCIAS ".($Narr_gcompetencia[$i][0])."</font></div></td>";
							}
							else
							{
								if ($wtipoevaluacion=='01')
								{
									echo"<td width='100%' align='Left' colspan='5'><div align='center'><font style='text-transform: uppercase;'>VALORACI&Oacute;N DE  COMPETENCIAS ".($Narr_gcompetencia[$i][0])."</font></div></td>";
								}
								else
								{
									echo"<td width='100%' align='Left' colspan='5'><div align='center'><font style='text-transform: uppercase;'>".($Narr_gcompetencia[$i][0])."</font></div></td>";
								}
							}

					echo"</tr>";
					echo"<tr class='fila2' >
							<td width='120' rowspan='".(($numcompetencias[$i] + $numdescriptores[$i]) + 1) ."'>";

							if($wtipoevaluacion=='01')
							{
								if($Narr_gcompetencia[$i][2] =='off')
								{
									echo"<div align='center' title='No aplica'>
											<strong >NA</strong>
										</div>";
								}
								else
								{
									echo"<div align='center'>
											<strong>".$Narr_gcompetencia[$i][1]."%</strong>
										</div>";
								}
							}

					echo"</td><td class='encabezadoTabla' width='100%'><div align='center'><strong>NIVEL DE EJECUCI&Oacute;N</strong></div></td>";


							if ( $wtipoevaluacion=='05' )
							{
								echo "<td  class='encabezadoTabla' width='10%'><strong>C ANT</strong></td>
									  <td  class='encabezadoTabla' width='10%'><strong><center>COM ANT</center></strong></td>
									  <td  class='encabezadoTabla' width='10%'><strong>CALIFICACI&Oacute;N</strong></td>
									  <td  class='encabezadoTabla' width='10%'><strong><center>COMENTARIO</center></strong></td>";
								$colspan_a = 5;
							}
							else
							{

								if ($wtipoevaluacion=='01'  )
								{
									echo"	<td class='encabezadoTabla' width='50'><strong>C.ANT</strong></td>
											<td class='encabezadoTabla' width='122'><strong>CALIFICACI&Oacute;N</strong></td>
											<td class='encabezadoTabla' width='88'><strong>PUNTAJE</strong></td>";

									$colspan_a = 4;

								}
								else
								{
									echo"<td colspan='3' class='encabezadoTabla' width='122'><strong>CALIFICACI&Oacute;N</strong></td>";
									$colspan_a = 4;
								}
							}


					echo"</tr>";
					$j=0;
					$q=	" 	SELECT  DISTINCT (Comcod),Comdes,Comsig "
							."  FROM ".$wbasedato."_000006,".$wbasedato."_000004 "
							." 	WHERE Forfor= '".$wformulario."' AND Forgco = '".$arr_gcompetencia[$i]."'"
							."	  AND Forcom = Comcod "
							."	ORDER BY foroco,Forcom";

					$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$numrowcom = mysql_num_rows($res);

					// While que establece las competencias con sus respectivos descriptores

					while ($row =mysql_fetch_array($res))
					{
							//Encabezado de la competencia
							echo"<tr class='fila2' style='Background: #999999; Color: #FFFFFF' >
									<td align= 'left' colspan='".$colspan_a."'><strong>".($row['Comdes']).": </strong>".($row['Comsig'])." </td>
								 </tr>";
							//---------------------------

							$k=0;
							$controlTotal=1;


							$q=	" 	SELECT  Descod,Desdes,".$wbasedato."_000006.id "
								."  FROM ".$wbasedato."_000006,".$wbasedato."_000005"
								." 	WHERE Forfor= '".$wformulario."' AND Forgco = '".$arr_gcompetencia[$i]."' AND Forcom = '".$row['Comcod']."'"
								."	  AND Fordes = Descod "
								." 	  AND Desest != 'off' "
								."	ORDER BY forogc,Forgco,foroco,Forcom,Forord , ".$wbasedato."_000006.Fordes DESC";

							$res1 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$numrow = mysql_num_rows($res1);

							//While de los descriptores

							while($row1 =mysql_fetch_array($res1))
							{
									//Estilo alternado de los tr
									if (is_int ($k/2))
									   {
										$wcf="fila1";  // color de fondo de la fila
									   }
									else
									   {
										$wcf="fila2"; // color de fondo de la fila
									   }
									echo"<tr class='".$wcf."  ubicacion' >";
									//------------------------------------

									//si la evaluacion esta cerrada o no, Habilita los inputs o no
									if ($westado=='1')
									   {
										$habilitado="disabled";  // color de fondo de la fila
									   }
									else
									   {
										$habilitado=""; // color de fondo de la fila
									   }
									//------------------------------------

									//Si la calificacion del descripor es menor a dos pone en naranja el estilo de este
									//Tambien se guarda en un vector los compromisos que se deben pintar luego en la seccion de compromisos
									if($arr_calificaciones[$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']]<= $wcalmal AND $arr_calificaciones[$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']] > 0 )
									{

										if($wtipoevaluacion==01)
										{
										$estilo= 'background-color:orange;';
										}

										//vector compromisos [0][$c] id de la div que lo contendra;
										$vectorcompromisos[0][$c]=$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod'];
										//vector compromisos[1][$c] codigo del decriptor;
										$vectorcompromisos[1][$c]=($row1['Descod']);
										//vector compromisos [2][$c] descripcion del decriptor
										$vectorcompromisos[2][$c]=($row1['Desdes']);
										//vector compromisos [2][$c] descripcion de la competencia
										$vectorcompromisos[3][$c]=($row['Comdes']);

										$c++;
										//-------------------------------------
									}
									else
									{
										$estilo="";
									}
									//------------------------------------
									// td con competencia y con la calificacion de esta
									$ancho=600;
									echo"<td align='Left'  id='tdd".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' width='".$ancho."' align='justify' >".($row1['Desdes'])."</td>";

									// Trae el tipo de descriptor que es
									$qtipodescriptor = "SELECT Destip ,Desngr "
													 . "  FROM ".$wbasedato."_000005 "
													 . " WHERE Descod = '".$row1['Descod']."'";



									$restipodescriptor = mysql_query($qtipodescriptor,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtipodescriptor." - ".mysql_error());
									$rowtipodescriptor = mysql_fetch_array($restipodescriptor);


										if ($wtipoevaluacion=='01')
										{

										 echo"<td  id='tdac".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' align='center' style=''><div id='div-".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' >-</div></td>";

										}
										if ($wtipoevaluacion=='05')
										{

											if( $rowtipodescriptor['Destip']=='07' || $rowtipodescriptor['Destip']=='08')
											{
												echo"	<td align='center'>
															<input size='8' maxlength='6'  disabled='disabled'   class='contienenotaant' id='text_comentario_anterior_".$row1['Descod']."' type='text' >
														</td>";
											}
											else if($rowtipodescriptor['Destip']=='03' )
											{

													/*echo"	<td align='center'>
															<input size='8' maxlength='6'   disabled='disabled' style='text-align:center'  class='contienenotaant' id='text_comentario_anterior_".$row1['Descod']."' type='text' >
															</td>";*/

													echo"<td align='center'><textarea  disabled='disabled' style='text-align:center'  class='contienenotaant' id='text_comentario_anterior_".$row1['Descod']."' type='text' ></textarea></td>";



											}
											else
											{
												echo"	<td align='center'>
															<input size='5' maxlength='3'  disabled='disabled' style='text-align:center'  class='contienenotaant' id='text_comentario_anterior_".$row1['Descod']."' type='text' >
														</td>";
											}
											echo "<td>
														<table><tr align='Left' >
															<td>Tipo:</td>
															<td><select disabled='disabled'  id='select_tipo_comentario_anterior_".$row1['Descod']."' >";
																echo"<option value='1'>Hallazgo</option>";
																echo"<option value='2'>No conformidad</option>";
																echo"<option value='3'>Sugerencia</option>";
																echo "</select></td>
															</tr>
															<tr>
																<td colspan='2' ><textarea  class='contienecomentarioant'  id='textarea_comentario_anterior_".$row1['Descod']."' readonly disabled='disabled' rows='6'  cols='25' ></textarea></td>
															</tr>
														</table>
													</td>";


										}

										if ($wtipoevaluacion=='01' or $wtipoevaluacion=='05' )
										{
											$rowspantd=1;
										}
										else
										{
											$rowspantd=3;

										}

									echo"<td colspan='".$rowspantd."' id='tdc".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' align='center' style='".$estilo."'>";

									// aqui maneja comentario
									//$manejacomentario ='si';
									// Pinta el tipo de descriptor
									if ($rowtipodescriptor['Destip']=='01' )
									{
										//-- tipo de descriptor numerico
										echo"<input ".$habilitado."  size='5' maxlength='3' class ='valores-".$i."' style='text-align:center' type='text' name='text' id='".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' onchange='validacampo(this)' onblur='grabadato(this,\"".$row1['id']."\",\"".$wnumprueba."\",\"".$numrow."\",\"".$numrowcom."\",\"".$Narr_gcompetencia[$i][1]."\",\"valor\")' onFocus='tomavaloractual(this)' value='".$arr_calificaciones["".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod'].""]."'>";
									}
									else if ($rowtipodescriptor['Destip']=='02')
									{
										// tipo de descriptor booleano
										if ($arr_calificaciones["".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod'].""] == $wcalmin)
										{
											$seleccionadomin = 'checked';
										}

										if ($arr_calificaciones["".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod'].""] == $wcalmax)
										{
											$seleccionadomax = 'checked';
										}
										echo "<input ".$habilitado."  size='5' maxlength='3' style='text-align:center' type='hidden' name='text' id='".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' onchange='validacampo(this)' onblur='grabadato(this,\"".$row1['id']."\",\"".$wnumprueba."\",\"".$numrow."\",\"".$numrowcom."\",\"".$Narr_gcompetencia[$i][1]."\",\"valor\")' onFocus='tomavaloractual(this)' value='".$arr_calificaciones["".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod'].""]."'>";
										echo"<input  ".$habilitado." type='radio' name='radio-".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' value='".$wcalmax."' onClick='ClicRadioEscala(\"".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."\",this ,\"".$row1['id']."\",\"".$wnumprueba."\",\"".$numrow."\",\"".$numrowcom."\",\"".$Narr_gcompetencia[$i][1]."\")' ".$seleccionadomax." />SI<input type='radio' name='radio-".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' value='".$wcalmin."'  onClick='ClicRadioEscala(\"".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."\",this ,\"".$row1['id']."\",\"".$wnumprueba."\",\"".$numrow."\",\"".$numrowcom."\",\"".$Narr_gcompetencia[$i][1]."\")' ".$seleccionadomin."  ".$habilitado." /> NO";

									}
									else if ($rowtipodescriptor['Destip']=='03')
									{
										//-- Selector tipo texto.
										$qtext     =      "  SELECT  Evadat"
														 ."    FROM ".$wbasedato."_000007"
														 ."   WHERE Evafco = '".$wformulario."' "
														 ."	    AND Evagco = '".$arr_gcompetencia[$i]."' "
														 ."     AND Evacom = '".$row['Comcod']."'"
														 ."	    AND Evades = '".$row1['Descod']."' "
														 ."     AND Evaevo ='".$wempleado."' "
														 ."     AND Evaevr = '".$wcalificador."'"
														 ."     AND Evaano ='".$wano."'	"
														 ."     AND Evaper ='".$wperiodo."'";

										$restext= mysql_query($qtext,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtext." - ".mysql_error());
										$rowtext =mysql_fetch_array($restext);


										echo"<textarea ".$habilitado." class ='valores-".$i." tipotexto'  type='text' name='text' id='".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."'  onchange='grabadato3(this,\"".$wformulario."\",\"".$arr_gcompetencia[$i]."\", \"".$row['Comcod']."\",\"".$row1['Descod']."\")'  rows='4'  cols='15'>".$rowtext['Evadat']."</textarea>";

									}
									else if ($rowtipodescriptor['Destip']=='04' )
									{
										//-- Tipo de descriptor Escala
										//-- Toma sus datos de la tabla 000049
										$vectorescala = array();
										$qescala = "  SELECT 	Notcod, Notdes, Notval, Notima, Notcar  "
										         . "    FROM  ".$wbasedato."_000047 "
												 . "   WHERE  Notgru = '".$rowtipodescriptor['Desngr']."' "
												 . "ORDER BY  Notord ";

										$resescala = mysql_query($qescala,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qescala." - ".mysql_error());

										while($rowescala =mysql_fetch_array($resescala))
										{
											$vectorescala[$rowescala['Notcod']]= array ('nombre'=> $rowescala['Notdes'],'valor'=> $rowescala['Notval'],'ruta'=> $rowescala['Notima'],'caracteristica'=> $rowescala['Notcar']);
										}

										echo "<input ".$habilitado."  size='5' maxlength='3' style='text-align:center' type='hidden' name='text' id='".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' onchange='validacampo(this)' onblur='grabadato(this,\"".$row1['id']."\",\"".$wnumprueba."\",\"".$numrow."\",\"".$numrowcom."\",\"".$Narr_gcompetencia[$i][1]."\")' onFocus='tomavaloractual(this)' value='".$arr_calificaciones["".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod'].""]."'>";

										echo "<table><tr>";
										$e=0;
										foreach ($vectorescala as $key => $valor )
										{
										  if($e%2==0)
										 	$color='blue';
										  else
											$color='black';

										  if($valor['ruta']=='')
										  {
											 echo "<td name='tdtxt-".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' align='center' style='font-size:7.5pt  ; color: ".$color.";' >".$valor['nombre']."</td>";
										  }
										  else
										  {
											  echo "<td name='tdtxt-".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' >";
											  echo "<img width='32' height='33' src='".$valor['ruta']."' />";
											  echo "</td>";
										  }
										  $e++;
										}
										echo "</tr>";
										echo "<tr>";
										$t=1;
										foreach ($vectorescala as $key => $valor )
										{

										 if ($arr_calificaciones["".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod'].""] == $valor['valor'])
											{

											  $seleccionado = 'checked';
											}
											else
											{
												$seleccionado = '';
											}
										  echo "<td align='center'>";
										  echo "<input  ".$habilitado." type='radio' name='radio-".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' value='".$valor['valor']."'  onClick='ClicRadioEscala(\"".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."\",this ,\"".$row1['id']."\",\"".$wnumprueba."\",\"".$numrow."\",\"".$numrowcom."\",\"".$Narr_gcompetencia[$i][1]."\",\"".$valor['nombre']."\")' ".$seleccionado." >";
										  echo "</td>";
										  $t++;

										}

										echo "</tr></table>";
										echo "<a onclick='traecompromisos( \"agregarcomentario\" , \"".($row['Comdes'])."\",\"".($row1['Desdes'])."\",\"".$arr_gcompetencia[$i]."\",\"".$row['Comcod']."\",\"".$row1['Descod']."\")' style='cursor:pointer; color: gray; float: right;'>comentario</a>";
									}
									else if ($rowtipodescriptor['Destip']=='05')
									{
										//-- Campo tipo seleccion
										//----------------------
										$qseleccionado =     "  SELECT  Peresc,Ideno1,Ideno2,Ideap1,Ideap2"
															 ."    FROM ".$wbasedato."_000050, talhuma_000013"
															 ."   WHERE Perfor = '".$wformulario."' "
															 ."	    AND Pergco = '".$arr_gcompetencia[$i]."' "
															 ."     AND Percom = '".$row['Comcod']."'"
															 ."	    AND Perdes = '".$row1['Descod']."' "
															 ."     AND Perhis ='".$wempleado."' "
															 ."  	AND Pering = '".$wingreso."' "
															 ."     AND Percal = '".$wcalificador."'"
															 ."     AND Perano ='".$wano."'	"
															 ."     AND Perper ='".$wperiodo."'"
															 ."     AND Peresc = Ideuse"
															 ."		AND Ideuse = 'on' ";


										$resseleccionado= mysql_query($qseleccionado,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qseleccionado." - ".mysql_error());


										$querySelect = " SELECT Destab, Descam, Descon"
													  ."    FROM ".$wbasedato."_000005 "
													  ."   WHERE  Descod = '".$row1['Descod']."' ";

										$resquerySelect= mysql_query($querySelect,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querySelect." - ".mysql_error());
										$rowquerySelect =mysql_fetch_array($resquerySelect);

										$params = array();
										$params['tabla']='talhuma_000013';
										$params['campo_estado']="Ideest = 'on'";
										$params['campos'][]='Ideuse';
										$params['campos'][]='ideno1';
										$params['campos'][]='ideno2';
										$params['campos'][]='ideap1';
										$params['campos'][]='ideap2';


										$buscado = '';


										// $resquerySelect= mysql_query($querySelect,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querySelect." - ".mysql_error());
										echo"Buscar:<input id='wnomuse_pfls-".$row1['Descod']."' name='wnomuse_pfls-".$row1['Descod']."' value='' size='20' onkeypress='return enterBuscar(\"wnomuse_pfls-".$row1['Descod']."\",\"select-".$row1['Descod']."\",\"user\",\"load_users\",event);' onBlur='recargarLista(\"wnomuse_pfls-".$row1['Descod']."\",\"select-".$row1['Descod']."\",\"load_users\");' />";
										echo"<br>";
										//echo"<SELECT style='width:300px' id='select-".$row1['Descod']."' onchange='grabadato2(this,\"".$wformulario."\",\"".$arr_gcompetencia[$i]."\", \"".$row['Comcod']."\",\"".$row1['Descod']."\")' >";
										echo"<SELECT style='width:300px' id='select-".$row1['Descod']."' onChange='addList(\"select-".$row1['Descod']."\",\"div_adds_usuarios-".$row1['Descod']."\",\"load_chk_usuario\",\"".$row1['Descod']."\",\"".$wformulario."\",\"".$arr_gcompetencia[$i]."\", \"".$row['Comcod']."\",\"".$row1['Descod']."\"); grabadato2(this,\"".$wformulario."\",\"".$arr_gcompetencia[$i]."\", \"".$row['Comcod']."\",\"".$row1['Descod']."\");'>";
										echo getOptions($wemp_pmla, $conex, $wbasedato,$params , $buscado);

										echo"</SELECT>";

										echo "<div align='left' id='div_adds_usuarios-".$row1['Descod']."'  >";
										while($rowseleccionado =mysql_fetch_array($resseleccionado))
										{
											echo"<div id='div_ckc_user_".$rowseleccionado['Peresc']."-".$row1['Descod']."' class='fila2' style='border-top: 2px solid #ffffff;'>";
											echo"<input id='wuse_pfls_".$rowseleccionado['Peresc']."-".$row1['Descod']."' type='checkbox' onclick='desmarcarRemover(\"wuse_pfls_".$rowseleccionado['Peresc']."-".$row1['Descod']."\",\"div_ckc_user_".$rowseleccionado['Peresc']."-".$row1['Descod']."\",\"div_load_chk_users\",\"".$row1['Descod']."\",\"".$wformulario."\",\"".$arr_gcompetencia[$i]."\", \"".$row['Comcod']."\",\"".$row1['Descod']."\",\"".$rowseleccionado['Peresc']."\");' checked='checked' value='".$rowseleccionado['Peresc']."' name='wuse_pfls_chk[".$rowseleccionado['Peresc']."]'>";
											echo $rowseleccionado['Peresc']." - ".$rowseleccionado['Ideno1']." ".$rowseleccionado['Ideno2']." ".$rowseleccionado['Ideap1']." ".$rowseleccionado['Ideap2'];
											echo"</div>";
										}
										echo "</div>";
										echo "<input ".$habilitado."  size='5' maxlength='3' style='text-align:center' type='hidden' name='text' id='".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' value='noobligatorio'  >";


									}
									else if ($rowtipodescriptor['Destip']=='06')
									{
										//-- Campo tipo Seleccion Multiple
										//-- Trae sus valores de la tabla 000047
										$vectorescala = array();
										$qescala = "  SELECT 	Notcod, Notdes, Notval, Notima, Notcar  "
										         . "    FROM  ".$wbasedato."_000047 "
												 . "   WHERE  Notgru = '".$rowtipodescriptor['Desngr']."' "
												 . "ORDER BY  Notord ";

										$resescala = mysql_query($qescala,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qescala." - ".mysql_error());

										while($rowescala =mysql_fetch_array($resescala))
										{
											$vectorescala[$rowescala['Notcod']]= array ('nombre'=> $rowescala['Notdes'],'valor'=> $rowescala['Notval'],'ruta'=> $rowescala['Notima'],'caracteristica'=> $rowescala['Notcar'],'codigo'=> $rowescala['Notcod']);
										}

										echo "<input ".$habilitado."  size='5' maxlength='3' style='text-align:center' type='hidden' name='text' id='".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' onchange='validacampo(this)' onblur='grabadato(this,\"".$row1['id']."\",\"".$wnumprueba."\",\"".$numrow."\",\"".$numrowcom."\",\"".$Narr_gcompetencia[$i][1]."\")' onFocus='tomavaloractual(this)' value='".$arr_calificaciones["".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod'].""]."'>";

										echo "<table align='center'>";
										$e=0;
										foreach ($vectorescala as $key => $valor )
										{
										  if($e%2==0)
										 	$color='blue';
										  else
											$color='black';

											 if ($arr_calificaciones["".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod'].""] == $valor['codigo'])
											{

											  $seleccionado = 'checked';
											}
											else
											{
												$seleccionado = '';
											}

										  if($valor['ruta']=='')
										  {
											 echo "<tr ><td  align='left' style='font-size:7.5pt  ; color: ".$color.";'  >".$valor['codigo']."</td><td align='left' name='tdtxt-".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' align='center' style='font-size:7.5pt  ; color: ".$color.";' >".($valor['nombre'])."</td>";
											 echo "<td align='left'>";
										     echo "<input  ".$habilitado." type='radio' name='radio-".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' value='".$valor['valor']."'  onClick='ClicRadioEscala(\"".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."\",this ,\"".$row1['id']."\",\"".$wnumprueba."\",\"".$numrow."\",\"".$numrowcom."\",\"".$Narr_gcompetencia[$i][1]."\",\"".$valor['codigo']."\")' ".$seleccionado." >";
											 echo "</td>";
											 echo"</tr>";
										  }
										  else
										  {
											  echo "<tr><td align='left'  style='font-size:7.5pt  ; color: ".$color.";' >".($valor['codigo'])."</td><td align='left' name='tdtxt-".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' >";
											  echo "<img width='32' height='33' src='".$valor['ruta']."' />";
											  echo "</td>";
											  echo "<td align='left'>";
											  echo "<input  ".$habilitado." type='radio' name='radio-".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' value='".$valor['valor']."'  onClick='ClicRadioEscala(\"".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."\",this ,\"".$row1['id']."\",\"".$wnumprueba."\",\"".$numrow."\",\"".$numrowcom."\",\"".$Narr_gcompetencia[$i][1]."\",\"".$valor['codigo']."\")' ".$seleccionado." >";
											  echo "</td>";
											  echo "</tr>";
										  }
										  $e++;
										}

										echo "</table>";


									}
									else if ($rowtipodescriptor['Destip']=='07')
									{
										//-- Tipo de descriptor fecha con cierre , esto es que de esta fecha se tomara
										//-- el cierre de la encuensta o evaluacion.
										$qtext     =      "  SELECT  Evadat"
														 ."    FROM ".$wbasedato."_000007"
														 ."   WHERE Evafco = '".$wformulario."' "
														 ."	    AND Evagco = '".$arr_gcompetencia[$i]."' "
														 ."     AND Evacom = '".$row['Comcod']."'"
														 ."	    AND Evades = '".$row1['Descod']."' "
														 ."     AND Evaevo ='".$wempleado."' "
														 ."     AND Evaevr = '".$wcalificador."'"
														 ."     AND Evaano ='".$wano."'	"
														 ."     AND Evaper ='".$wperiodo."'";

										$restext= mysql_query($qtext,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtext." - ".mysql_error());
										$rowtext =mysql_fetch_array($restext);


										echo"<input ".$habilitado." class ='valores-".$i." datepicker fechaconcierre'  readonly  type='text' name='text' id='".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."'  onchange='grabadato3(this,\"".$wformulario."\",\"".$arr_gcompetencia[$i]."\", \"".$row['Comcod']."\",\"".$row1['Descod']."\")' value='".$rowtext['Evadat']."' size='10' maxlength='10' />";

									}
									else if ($rowtipodescriptor['Destip']=='08')
									{
										//-- Tipo de descriptor fecha
										$qtext     =      "  SELECT  Evadat"
														 ."    FROM ".$wbasedato."_000007"
														 ."   WHERE Evafco = '".$wformulario."' "
														 ."	    AND Evagco = '".$arr_gcompetencia[$i]."' "
														 ."     AND Evacom = '".$row['Comcod']."'"
														 ."	    AND Evades = '".$row1['Descod']."' "
														 ."     AND Evaevo ='".$wempleado."' "
														 ."     AND Evaevr = '".$wcalificador."'"
														 ."     AND Evaano ='".$wano."'	"
														 ."     AND Evaper ='".$wperiodo."'";

										$restext= mysql_query($qtext,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtext." - ".mysql_error());
										$rowtext =mysql_fetch_array($restext);


										echo"<input ".$habilitado." class ='valores-".$i." datepicker '  readonly  type='text' name='text' id='".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."'  onchange='grabadato3(this,\"".$wformulario."\",\"".$arr_gcompetencia[$i]."\", \"".$row['Comcod']."\",\"".$row1['Descod']."\")' value='".$rowtext['Evadat']."' size='10' maxlength='10' />";

									}
									else if ($rowtipodescriptor['Destip']=='09')
									{
										//-- tipo de descriptor numerico no tenido encuenta para evaluaciones ni encuestas
										echo"<input ".$habilitado."  size='5' maxlength='3' class ='valores-".$i." nosetieneencuenta' formato='".$wformulario."'  grupocompetencia='".$arr_gcompetencia[$i]."' competencia='".$row['Comcod']."' descriptor='".$row1['Descod']."' style='text-align:center' type='text' name='text' id='".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' onchange='validacampoNumericoNoTendioEncuentaParaEvaluaciones(this)' onblur='grabadato(this,\"".$row1['id']."\",\"".$wnumprueba."\",\"".$numrow."\",\"".$numrowcom."\",\"".$Narr_gcompetencia[$i][1]."\",\"valor\")' onFocus='tomavaloractual(this)' value='".$arr_calificaciones["".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod'].""]."'>";

									}

									//--------------------------------------

									echo "</td>";
									// aqui


									if ($wtipoevaluacion=='05')
									{

										$comentarialfrente ='si';//echo"<td  id='tdac".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' align='center' style=''><div id='div-".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' >-</div></td>";

									}
									else
									{

										$comentarialfrente ='no';
									}
									if ($comentarialfrente =='si')
									{
										$comentario = '';

										$select_comentario = "SELECT Comstr, Comtip
																FROM ".$wbasedato."_000036
															   WHERE Comfor= '".$wformulario."'
															     AND Comgco= '".$arr_gcompetencia[$i]."'
																 AND Comcom= '".$row['Comcod']."'
															     AND Comdes= '".$row1['Descod']."'
															     AND Comper= '".$wperiodo."'
																 AND Comano= '".$wano."' 
																 AND Comucr ='".$wcalificador."'
																 AND Comucm ='".$wempleado."'" ;
																 
																 
																 


										$res_comentario = mysql_query($select_comentario,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$select_comentario." - ".mysql_error());
										if($row_comentario = mysql_fetch_array($res_comentario))
										{
											$comentario 	= $row_comentario['Comstr'];
											$tipocomentario = $row_comentario['Comtip'];

										}


										//id='".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."'
										echo "<td><table><tr class='".$wcf."' align='Left' >
															<td>Tipo:</td>
															<td><select   id='selectcomentario_".$row1['Descod']."' class='comentario_abierto_select' descriptor='".$row1['Descod']."'  formulario='".$wformulario."'  gcompetencia='".$arr_gcompetencia[$i]."' competencia='".$row['Comcod']."' >";
																if($wtipoevaluacion=='05')
																{
																	echo "<option value='1' ";
																		if($tipocomentario == '1')
																			echo 'selected';
																	echo ">Hallazgo</option>";
																	echo "<option value='2'  ";
																		if($tipocomentario == '2')
																			echo 'selected';
																	echo ">No conformidad</option>";
																	echo "<option value='3'  ";
																		if($tipocomentario == '3')
																			echo 'selected';
																	echo ">Felicitacion</option>";
																}
																else
																{
																	echo "<option value='1' ";
																		if($tipocomentario == '1')
																			echo 'selected';
																	echo ">Queja</option>";
																	echo "<option value='2'  ";
																		if($tipocomentario == '2')
																			echo 'selected';
																	echo ">Sugerencia</option>";
																	echo "<option value='3'  ";
																		if($tipocomentario == '3')
																			echo 'selected';
																	echo ">Felicitacion</option>";
																}
																echo "</select></td>
															</tr>
															<tr>
																<td colspan='2'><textarea  id='texareacomentario_".$row1['Descod']."' descriptor='".$row1['Descod']."'  formulario='".$wformulario."'  gcompetencia='".$arr_gcompetencia[$i]."' competencia='".$row['Comcod']."' rows='6'  cols='25' class='comentario_abierto'>".$comentario."</textarea></td>
															</tr>
												   </table>
											  </td>";
									}

									//------------------------------------------------
									//Este vector de campos se arma con el objetivo de que se almacenen en el, todos los campos de descriptores existentes, con el fin
									//de mandar esta variable y poder saber si todos los campos estan llenos.
									$vectorcampos= $vectorcampos."*".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."";

									// La varible controlTotal se utiliza par poder generar el td que contiene el total de la competencia con su
									// respectivo valor y su rowspan correspondiente

										if($controlTotal==1)
										{
												//Calculo del porcentaje del grupo de competencia
												$valorgcompetencia =$valorgcompetencia  + $arr_calificacionesxcom[$row['Comcod']]*1;

												$numerodedescriptores = $numerodedescriptores + $numrow;

												if($Narr_gcompetencia[$i][2] =='on')
												{
													@$porcentajegco = ((((((($arr_calificacionesxcom[$row['Comcod']]*1)/$numrow)) /$wcalmax ) * 100))/ $numrowcom ) * (0.01* $Narr_gcompetencia[$i][1])  ;
													@$porcentajegco =redondear_dos_decimal($porcentajegco);
												}
												else
												{
													@$porcentajegco = $arr_calificacionesxcom[$row['Comcod']]*1;
													@$porcentajegco =redondear_dos_decimal($porcentajegco);
												}
												//-----------------------------------------------
												if ($wtipoevaluacion=='01')
												{
													//td donde se establece el total de competencia
													echo "<td  class='fila2' rowspan='".$numrow."'><div align='center' id='div".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."'>".$porcentajegco."</div>
														  <input type='hidden' name='calificacion' id='text".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."'  value='".($arr_calificacionesxcom[$row['Comcod']]* 1 )."'>
														  </td>";
												}
												else
												{

													echo "<div style= 'display: none;' align='center' id='div".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."'>".$porcentajegco."</div>
														  <input type='hidden' name='calificacion' id='text".$wformulario."-".$arr_gcompetencia[$i]."-".$row['Comcod']."'  value='".($arr_calificacionesxcom[$row['Comcod']]* 1 )."'>";
												}

												//------------------------------------------------

												$controlTotal=0;
										}

									echo "</tr>";


								$k++;
							}
							//----------------------------------------------
							if($Narr_gcompetencia[$i][2] =='on')
							{
								$totalgco2 = (((((($valorgcompetencia*1)/ $numerodedescriptores)/$wcalmax )* 100 ))/ 1) * ($Narr_gcompetencia[$i][1] *0.01);
								$totalgco2 = redondear_dos_decimal ($totalgco2);
								$totalgco=($totalgco ) + (( $porcentajegco ));
								$totalgco= redondear_dos_decimal($totalgco);
							}
							else
							{
								$totalgco2 =  $valorgcompetencia;
								$totalgco2 =  redondear_dos_decimal ($totalgco2);
							}

							$j++;

					}
					//---------------------------------

				   if($Narr_gcompetencia[$i][2] =='on')
				   {
					 $total=$total + $totalgco2;
				     $total= redondear_dos_decimal($total);
				   }

				if ($wtipoevaluacion!='01')
				{
					$display = "display:none;";
				}

					echo  "<tr style='".$display."'>
							<td colspan='3'>&nbsp;</td>
					<td align='right'>TOTAL</td>";
					if($Narr_gcompetencia[$i][2]=='on')
					{
						echo "<td><div class='resultadogcompetencia' id='div".$wformulario."-".$arr_gcompetencia[$i]."' align='center'>".$totalgco2."</div></td>";
					}
					else
					{
						echo "<td><div  id='div".$wformulario."-".$arr_gcompetencia[$i]."' align='center'>".$totalgco2."</div></td>";

					}
					echo"</tr>";
					$valorgcompetencia=0;
					$numerodedescriptores=0;

					$i++;
					$totalgco=0;

				}
				echo"<tr><td align='center'><div id='divfechacierreanterior'></div></td><td align='right'><div id='divenctotalanterior'></div></td><td align='center'><div id='divtotalanterior'></div></td><td></td><td></td></tr>";
				echo"</table><br><br><br><br>";

				echo'<div>';


				// aqui , deberia ser un parametro en el formato indicando que maneja compromisos
				if ($wtipoevaluacion=='01')
				{

					$q=  " SELECT SUM( Tottot ) / COUNT( * ) AS Promedio ,COUNT( * )"
						."   FROM ".$wbasedato."_000035 , talhuma_000013, ".$wbasedato."_000002 "
						."  WHERE Totper = '".$wperiodo."' "
						."    AND Totano = '".$wano."' "
						."    AND Ideuse = Totcdo "
						."    AND Forcod  = Totcod "
						."    AND Fortip = '".$tipo."' "
						."    AND Ideccg = '".$wcargo."' ";


					$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row =mysql_fetch_array($res);

					echo '<table align="center" width="300">
							  <tr><td class="encabezadoTabla">CALIFICACION TOTAL DEL EVALUADO</td><td class="fila1"><div id="div'.$wformulario.'" align="left">'.$total.'</div></td></tr>
							  <tr><td class="encabezadoTabla">PROMEDIO DEL CARGO</td><td class="fila2"><div align="left">'.(round($row[0] * 100) / 100).'</ div></td></tr>
							  <tr><td class="encabezadoTabla">PERSONAS EVALUADAS</td><td class="fila1"><div align="left">'.$row[1].'</ div></td></tr>
						  </table><br><br>
						  <table align="center" width="300">
							  <tr align= center">';
					echo "<td colspan='4' ><div align='center'><input id='cerrarevaluacion' ".$habilitado." type='button' value='Cerrar Evaluacion' onclick='grabadato(this,\"".$vectorcampos."\",\"".$wnumprueba."\")'></div></td>
						  </tr>
						  </table>";

					echo"<div align='middle' style='display:none;width:100%;cursor:default;' id='firmadigital' name='firmadigital'>";
					echo"<br><br><table class='fila1' style='border: black 1px solid'>";
					echo"<tr class='encabezadoTabla' ><td align='center' ><b>FIRMA DEL EVALUADO<b></td></tr>";
					echo"<tr class='fila1'><td align='center' ><input id='textfirma' type='PASSWORD' size='20'></td></tr>";
					echo"<tr><td>ingrese su clave de matrix para poder cerrar esta evaluacion</td></tr>";
					echo "<tr align='center' class='fila1'><td><INPUT TYPE='button' value='Grabar' onClick='grabafirma(\"textfirma\"); $.unblockUI()' style='width:100'/><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'></td></tr>";
					echo"</table><br><br>";
					echo "</div>";


					echo "</tr></td></table>";

					echo'<br>';
					//-----------------------------------------
					// Div del titulo de compromisos
					echo'<div id="ref_compromisos" align="center" width="100%">
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr align="Left" >
											<td align="Left"><a href="#null" onclick="javascript:verSeccion(\'div_compromisos\');" >PLAN DE MEJORAMIENTO Y DESARROLLO</a></td>
										</tr>
									</table>
								</div>';

					//-------------------------------------

					//-------------------------------------


					// vector compromisos desde tabla compromisos
					$q  = "SELECT Comfor, Comgco, Comcom, Comdes"
						. "  FROM ".$wbasedato."_000036 "
						. " WHERE Comuco = '".$wempleado."' "
						. "   AND Comucr = '".$wcalificador."' "
						. "   AND Comper = '".$wperiodo."' "
						. "   AND Comano = '".$wano."' "
						. "   AND Comfor = '".$wformulario."' ";


					$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$j=0;
					while($row =mysql_fetch_array($res))
					{

						$vectorcompromisostabla[0][$j]= $row['Comfor']."-".$row['Comgco']."-".$row['Comcom']."-".$row['Comdes'];
						$j++;
					}
					// Div del contenido de los compromisos


					echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td>
									<div id="div_compromisos" align="center" class="borderDiv displ" width="100%">
										<div id="Ecompromisos">';

					if (count($vectorcompromisos[0])!=0)
					{
						echo'<br>';
						echo'<div id="EcompromisoEncabezado" width="100%" >
								<table width="100%" >
									<tr class="encabezadoTabla">
										<td colspan="4"><div align="center">1.  COMPROMISOS DEL EVALUADO  <br>(Compromisos espec&iacute;ficos por mejorar por parte del evaluado)</div></td>
									</tr>
									<tr>
										<td align="Left" width="219" class="fila2"><b>Nombre de la competencia</b></td>
										<td align="Left" width="254" class="fila2"><b>Descriptor en el que se compromete a mejorar:</b></td>
										<td align="Left" width="252" class="fila2"><b>Con que estrateg&iacute;a, &iquest;como se compromete a  Mejorarlo?</b></td>
										<td align="Left" width="147" class="fila2"><b>Fecha(D/M/A) De seguimiento</b></td>
									</tr>
								</table>
							</div>';

						$i=0;

						while($i<count($vectorcompromisos[0]))
						{

							$form1 = explode("-",$vectorcompromisos[0][$i]);

							$q= "SELECT Comstr, Comfco "
							  . "  FROM ".$wbasedato."_000036"
							  . " WHERE Comucm = '".$wempleado."' "
							  . "   AND Comuco = '".$wempleado."'"
							  . "   AND Comucr = '".$wcalificador."' "
							  ."	AND Comfor= '".$form1[0]."' "
							  ."    AND Comgco= '".$form1[1]."' "
							  ."    AND Comcom= '".$form1[2]."' "
							  ."    AND Comdes= '".$form1[3]."' "
							  ."    AND Comper ='".$wperiodo."' "
							  ."    AND Comano = '".$wano."' ";

							$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$row =mysql_fetch_array($res);



							echo"<div id=Ecompromiso-".$vectorcompromisos[0][$i]." width='100%' >
								  <table width='100%' >
									<tr>
										<td align='Left' class='fila1' width='219'>".$vectorcompromisos[3][$i]."</td>
										<td align='Left' class='fila1' width='254'>".$vectorcompromisos[2][$i]."</td>
										<td align='Left' class='fila1' width='252'><textarea ".$habilitado." id='ecompromiso-".$vectorcompromisos[0][$i]."' class='compromisos' onchange='grabadato(this)' rows='6'  cols='30'>".$row[0]."</textarea></td>
										<td align='center' class='fila1' width='147'><input ".$habilitado."  class='compromisos'  type='text' size='10'  id='efecha-".$vectorcompromisos[0][$i]."-".$wempleado."'  value='".$row['Comfco']."' rel='4' onchange='grabadato(this)' /></td>
									</tr>
								 </table>
								</div>";
							$i++;
						}

					}

					echo'</div>
							<div id="Ccompromisos">';

					if (count($vectorcompromisos)!=0)
					{
						echo'<br>';
						echo'<div id="CcompromisoEncabezado" width="100%" >
								<table width="100%" >
									<tr class="encabezadoTabla">
										<td colspan="4"><div align="center">2. COMPROMISOS DEL EVALUADOR <br>(Como puede apoyar el logro de las actividades o planes trazados, especifique tiempos y estrategias de seguimiento)</div></td>
									</tr>
									<tr>
										<td align="Left" width="219" class="fila2"><b>Nombre de la competencia</b></td>
										<td align="Left" width="254" class="fila2"><b>Descriptor en el que se compromete a mejorar:</b></td>
										<td align="Left" width="252" class="fila2"><b>Con que estrateg&iacute;a, &iquest;como se compromete a  Mejorarlo?</b></td>
										<td align="Left" width="147" class="fila2"><b>Fecha(D/M/A) De seguimiento</b></td>
									</tr>
								</table>
							 </div>';

							$i=0;


						while($i<count($vectorcompromisos[0]))
						{

							$form1 = explode("-",$vectorcompromisos[0][$i]);


							$q= "SELECT Comstr, Comfco "
							  . "  FROM ".$wbasedato."_000036"
							  . " WHERE Comucm = '".$wcalificador."' "
							  . "   AND Comuco = '".$wempleado."'"
							  . "   AND Comucr = '".$wcalificador."' "
							  ."	AND Comfor= '".$form1[0]."' "
							  ."    AND Comgco= '".$form1[1]."' "
							  ."    AND Comcom= '".$form1[2]."' "
							  ."    AND Comdes= '".$form1[3]."' "
							  ."    AND Comper ='".$wperiodo."' "
							  ."    AND Comano = '".$wano."' ";

							$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$row =mysql_fetch_array($res);

							echo"<div id=Ccompromiso-".$vectorcompromisos[0][$i]." width='100%' >
								  <table width='100%' >
									<tr>
										<td  align='Left' class='fila1' width='219'>".$vectorcompromisos[3][$i]."</td>
										<td  align='Left' class='fila1' width='254'>".$vectorcompromisos[2][$i]."</td>
										<td  align='Left' class='fila1' width='252'><textarea ".$habilitado." id='ccompromiso-".$vectorcompromisos[0][$i]."' onchange='grabadato(this)' rows='6' cols='30'>".$row['Comstr']."</textarea></td>
										<td align='center' class='fila1' width='147'><input ".$habilitado." size='10' type='text' id='cfecha-".$vectorcompromisos[0][$i]."-".$wcalificador."'  class='compromisos'  rel='4' onchange='grabadato(this)' value='".$row['Comfco']."' /></td>
									</tr>
								 </table>
								</div>";
							$i++;
						}

					}

					echo'	</div>
							</div>
							</td>
							</tr>
							</table>';

				}
				if($wtipoevaluacion=='03' OR $wtipoevaluacion=='02' OR  $wtipoevaluacion=='05' )
				{
                    $sinpara ='';
					echo '<table  width="100%">';
					echo'<tr style = "'.$display.'" >
							<td colspan="2">&nbsp;</td>
							<td class="encabezadoTabla" width="172"><strong>PUNTAJE TOTAL:</strong></td>
							<td class="encabezadoTabla" width="197" ><div id="div'.$wformulario.'" align="center">'.$total.'</div></td>
						  </tr>
						  <tr align= center">';
					echo   "<td colspan='4' ><div align='center'><input id='cerrarevaluacion' ".$habilitado." type='button' value='Cerrar Encuesta' onclick='grabadato(this,\"".$vectorcampos."\",\"".$wnumprueba."\",\"".$sinpara."\",\"".$sinpara."\",\"".$sinpara."\",\"".$sinpara."\",\"".$wccohospitalario."\"); ' /></div></td>
						  </tr>

					</table>";

				}
				if($wtipoevaluacion=='04' )
				{
                    $sinpara ='';
					echo '<table  width="100%">';
					echo'<tr style = "'.$display.'" >
							<td colspan="2">&nbsp;</td>
							<td class="encabezadoTabla" width="172"><strong>PUNTAJE TOTAL:</strong></td>
							<td class="encabezadoTabla" width="197" ><div id="div'.$wformulario.'" align="center">'.$total.'</div></td>
						  </tr>
						  <tr align= center">';
					echo   "<td colspan='4' ><div align='center'><input id='cerrarevaluacion' ".$habilitado." type='button' value='Cerrar Evaluacion' onclick='grabadato(this,\"".$vectorcampos."\",\"".$wnumprueba."\",\"".$sinpara."\",\"".$sinpara."\",\"".$sinpara."\",\"".$sinpara."\",\"".$wccohospitalario."\"); ' /></div></td>
						  </tr>

					</table>";

				}
				echo "<div id='agregarcompromiso' class='fila2' align='middle'  style='display:none;width:100%;cursor:default' >";
					echo "<br><br><input type='hidden' id='Ocodigogcompetencia' /><input type='hidden' id='Ocodigocompetencia' /><input type='hidden' id='Ocodigodescriptor' />
							<table >
									<tr class='encabezadoTabla'>
										<td colspan='4'><div align='center'>1.  COMPROMISOS DEL EVALUADO  <br>(Compromisos espec&iacute;ficos por mejorar por parte del evaluado)</div></td>
									</tr>
									<tr>
										<td align='Left' width='354' class='fila2'><b>Descriptor en el que se compromete a mejorar:</b></td>
										<td align='Left' width='352' class='fila2'><b>Con que estrateg&iacute;a, &iquest;como se compromete a  Mejorarlo?</b></td>
										<td align='Left' width='147' class='fila2'><b>Fecha(D/M/A) De seguimiento</b></td>
									</tr>
									<tr>
										<td align='Left' class='fila1'  id='descriptorviejo' width='354'></td>
										<td align='Left' class='fila1' width='352'><textarea ".$habilitado." id='compromisoviejo' class='' onchange='grabadato(this)' rows='6'  cols='30'></textarea></td>
										<td align='center' class='fila1' width='147'><input ".$habilitado."  class=' '  type='text' size='10'  id='fechavieja'  value='' rel='4' onchange='grabadato(this)' /></td>
									</tr>
									<tr class='fila2' align='center' >
									<td colspan='4' align='center' >
									<INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'>
									</td>
								<td>
							</tr>
						</table><br><br></div>";
				if ($wtipoevaluacion=='03' OR  $wtipoevaluacion=='05' )
				{

//----------------------------------------- Div comentario
					echo "<div id='agregarcomentario' class='fila2' align='middle'  style='display:none;width:100%;cursor:default' >";
					echo "<br><br><input type='hidden' id='Ocodigogcompetencia' /><input type='hidden' id='Ocodigocompetencia' /><input type='hidden' id='Ocodigodescriptor' />
							<table style='border:#2A5DB0 1px solid'>
								<tr class='encabezadoTabla'>
									<td align='Center' colspan='2'>COMENTARIO
									</td>
								</tr>
								<tr class='fila1'>
									<td align='Left' ><b>Competencia:<b></td><td align='Left' ><div id='Onombrecompetencia'></div>

									</td>
								</tr>

								<tr class='fila1'>
									<td align='Left' ><b>Pregunta:<b></td><td align='Left' ><div id='Onombredescriptor'></div>
									</td>
								</tr>
								<tr class='fila1'>
									<td align='Left' ><b>Respuesta:<b></td><td align='Left' ><div id='Ocalificacion'></div>
									</td>
								</tr>
								<tr class='fila1' align='Left' >
									<td>Tipo:
									</td>
									<td><select   id='selectcomentario'  >";
									if($wtipoevaluacion=='05')
									{
										echo"<option value='1'>Hallazgo</option>";
										echo"<option value='2'>No conformidad</option>";
										echo"<option value='3'>Felicitacion</option>";
									}
									else
									{
										echo"<option value='1'>Queja</option>";
										echo"<option value='2'>Sugerencia</option>";
										echo"<option value='3'>Felicitacion</option>";
									}


								echo "</select></td>
								</tr>

							<tr class='fila1' align='Left'>
								<td>Comentario:
								</td>
								<td align='center'>
									<textarea   id='nombrecomentario'  rows='6' cols='30' ></textarea>
								</td>
							</tr>
							<tr class='fila2' align='center' >
								<td colspan='2' align='center' >
									<INPUT TYPE='button' value='Grabar' onClick='grabanuevocomentario(); $.unblockUI()' style='width:100'>
									<INPUT TYPE='button' value='Cancelar' onClick='$.unblockUI();' style='width:100'>
								</td>
								<td>
							</tr>
						</table><br><br></div>";
				}
				// -------------------------------------
				echo "<script>";
				$i=0;
				while($i<count($vectorcompromisos[0]))
				{
					echo "$('#cfecha-".$vectorcompromisos[0][$i]."-".$wcalificador."').datepicker({
								monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
								dayNamesMin: [ 'Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
								nextText: 'Siguiente',
								prevText: 'Anterior',
								closeText: 'Cancelar',
								currentText: 'Hoy',
								changeMonth: true,
								changeYear: true,
								showButtonPanel: false,
								dateFormat: 'yy-mm-dd'});";

					echo "$('#efecha-".$vectorcompromisos[0][$i]."-".$wempleado."').datepicker({
								monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
								dayNamesMin: ['Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
								nextText: 'Siguiente',
								prevText: 'Anterior',
								closeText: 'Cancelar',
								currentText: 'Hoy',
								changeMonth: true,
								changeYear: true,
								showButtonPanel: false,
								dateFormat: 'yy-mm-dd'});";
					$i++;
				}
				echo"</script>";

		}

	}
}


if (isset($consultaAjax) &&  $consultaAjax!='')
{
    if ($consultaAjax == "guardaCalificacion")
    {
      guardaCalificacion($wid,$wcalificacion,$wempleado,$wnumprueba,$wcalificador,$wfo_gc_co_de,$wperiodo,$wano,$wtipoevaluacion,$wrotulo);
    }
    if ($consultaAjax =="guardaevaluacion")
    {
      guardaEvaluacion($wcalificador,$wempleado,$wperiodo,$wano,$wnumprueba,$wformulario,$wcaltotal,$wtipoevaluacion,$wcalificacion,$wcalificaciones);
    }
	if ($consultaAjax =="guardaCompromiso")
	{
	  guardaCompromiso($wcalificador,$wempleado,$wperiodo,$wano,$wcalificacion,$wid);
	}
	if ($consultaAjax =="guardaFechaCompromiso")
	{
	  guardaFechaCompromiso($wcalificador,$wempleado,$wperiodo,$wano,$wcalificacion,$wid);
	}
	if ($consultaAjax =="guardatipoSelect")
	{
	  guardatipoSelect($wcalificador,$wempleado,$wperiodo,$wano,$wcalificacion,$wformulario,$wcomp,$wdes,$wgrupocom,$wingreso);

	}
	if ($consultaAjax =="guardatipotext")
	{
	   guardatipotext($wcalificador,$wempleado,$wperiodo,$wano,$wcalificacion,$wformulario,$wcomp,$wdes,$wgrupocom,$wcalmax);
	}


}


function  guardatipotext($wcalificador,$wempleado,$wperiodo,$wano,$wcalificacion,$wformulario,$wcomp,$wdes,$wgrupocom,$wcalmax)
{
	global $wbasedato;
	global $conex;
	$wnumprueba = 1;
	$fecha =date("Y-m-d");
	$hora = date("H:i:s");

	$q = 		"  DELETE "
				."   FROM ".$wbasedato."_000007 "
				. " WHERE Evaevo = '".$wempleado."'"
				. "   AND Evaevr = '".$wcalificador."'"
				. "   AND Evafco = '".$wformulario."' "
				. "   AND Evagco = '".$wgrupocom."' "
				. "   AND Evacom = '".$wcomp."'"
				. "   AND Evades = '".$wdes."'"
				. "   AND Evaper = '".$wperiodo."'"
				. "   AND Evaano = '".$wano."'" ;

	$res = mysql_query($q,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$fecha =date("Y-m-d");
	$q	= 	 " INSERT INTO ".$wbasedato."_000007  (Evacal,Evaevo,Evafco,Evagco,Evacom,Evades,Evanup,Evaevr,Evafec,Evadat,Evaper,Evaano,Seguridad,Fecha_data,Hora_data,Medico) "
						." VALUES ('".$wcalmax."', "
						."         '".$wempleado."' , "
						."         '".$wformulario."' , "
						."         '".$wgrupocom."' , "
						."         '".$wcomp."' , "
						."         '".$wdes."' , "
						."         '".$wnumprueba."' , "
						."         '".$wcalificador."',"
						."         '".$fecha."',"
						."         '".$wcalificacion."' ,"
						."         '".$wperiodo."',"
						."         '".$wano."',"
						."		   'C-".$wcalificador."',"
						."		   '".$fecha."',"
						."		   '".$hora."',"
						."         '".$wbasedato."')";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo $q;

}

function guardatipoSelect($wcalificador,$wempleado,$wperiodo,$wano,$wcalificacion,$wformulario,$wcomp,$wdes,$wgrupocom,$wingreso)
{
	global $wbasedato;
	global $conex;
	$wnumprueba = 1;

	// $q = 		"  DELETE "
				// ."   FROM ".$wbasedato."_000050 "
				// . " WHERE Evaevo = '".$wempleado."'"
				// . "   AND Evaevr = '".$wcalificador."'"
				// . "   AND Evafco = '".$wformulario."' "
				// . "   AND Evagco = '".$wgrupocom."' "
				// . "   AND Evacom = '".$wcomp."'"
				// . "   AND Evades = '".$wdes."'"
				// . "   AND Evaper = '".$wperiodo."'"
				// . "   AND Evaano = '".$wano."'" ;

	// $res = mysql_query($q,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$fecha =date("Y-m-d");
	$hora = date("H:i:s");
	$q	= 	 " INSERT INTO ".$wbasedato."_000050  (Percal,perhis,pering,peresc,perfor,pergco,percom,perdes,Perper,Perano,Seguridad,Medico,Fecha_data,Hora_data) "
						." VALUES ('".$wcalificador."', "
						."         '".$wempleado."' , "
						."         '".$wingreso."' , "
						."         '".$wcalificacion."' , "
						."         '".$wformulario."' , "
						."         '".$wgrupocom."' , "
						."         '".$wcomp."',"
						."         '".$wdes."' ,"
						. "   	   '".$wperiodo."',"
						. "  	   '".$wano."',"
						."		   'C-".$wcalificador."',"
						."          '".$wbasedato."', "
						."			'".$fecha."',"
						."			'".$hora."')";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


}

function guardaCompromiso($wcalificador,$wempleado,$wperiodo,$wano,$wcalificacion,$wid)
{

	

    

	$fecha =date("Y-m-d");
	global $wbasedato;
	global $conex;

	$form = explode("-",$wid);
	if($form[0]=='ccompromiso')
	{
		$usuariocomprometido=$wcalificador;
	}
	else
	{
		$usuariocomprometido=$wempleado;
	}
	$q  =	" SELECT * "
			."  FROM	".$wbasedato."_000036   "
			." WHERE Comuco= '".$wempleado."' "
			."   AND Comucm= '".$usuariocomprometido."' "
			."   AND Comucr= '".$wcalificador."' "
			."	 AND Comfor= '".$form[1]."' "
			."   AND Comgco= '".$form[2]."' "
			."   AND Comcom= '".$form[3]."' "
			."   AND Comdes= '".$form[4]."' "
			."   AND Comper= '".$wperiodo."' "
			."	 AND Comano= '".$wano."' " ;



	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrow = mysql_num_rows($res);

	if ($numrow==0)
	{
		$q	= 	 " INSERT INTO ".$wbasedato."_000036  (Comuco,Comucm,Comucr,Comfor,Comgco,Comcom,Comdes,Comper,Comano,Comstr,Fecha_data,Seguridad,Medico) "
                    ." VALUES ('".$wempleado."', "
					."         '".$usuariocomprometido."' , "
                    ."         '".$wcalificador."' , "
					."         '".$form[1]."',"
					."         '".$form[2]."',"
					."         '".$form[3]."',"
					."         '".$form[4]."',"
					."         '".$wperiodo."',"
					."         '".$wano."',"
					."         '".$wcalificacion."',"
					."         '".$fecha."',"
                    ."         'C-".$wcalificador."' ,"
					."         '".$wbasedato."') ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	}
	else
	{
		$q  =	"UPDATE ".$wbasedato."_000036   "
			   ."   SET Comstr='".$wcalificacion."' "
			   ." WHERE Comuco= '".$wempleado."'"
			   ."   AND  Comucm= '".$usuariocomprometido."' "
			   ."   AND  Comucr= '".$wcalificador."' "
			   ."   AND  Comfor= '".$form[1]."' "
			   ."   AND  Comgco= '".$form[2]."' "
			   ."   AND  Comcom= '".$form[3]."' "
			   ."   AND  Comdes= '".$form[4]."' "
			   ."   AND  Comper= '".$wperiodo."' "
			   ."   AND  Comano= '".$wano."' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	}

}

function guardaFechaCompromiso($wcalificador,$wempleado,$wperiodo,$wano,$wcalificacion,$wid)
{

	//En la variable calificacion estara la fecha del compromiso
	

    

	$fecha =date("Y-m-d");
	global $wbasedato;
	global $conex;

	$form = explode("-",$wid);
	if($form[0]=='cfecha')
	{
		$usuariocomprometido=$wcalificador;
	}
	else
	{
		$usuariocomprometido=$wempleado;
	}

	$q  =	" SELECT * "
			."  FROM	".$wbasedato."_000036   "
			." WHERE Comuco= '".$wempleado."' "
			."   AND Comucm= '".$usuariocomprometido."' "
			."   AND Comucr= '".$wcalificador."' "
			."	 AND Comfor= '".$form[1]."' "
			."   AND Comgco= '".$form[2]."' "
			."   AND Comcom= '".$form[3]."' "
			."   AND Comdes= '".$form[4]."' "
			."   AND Comper= '".$wperiodo."' "
			."	 AND Comano= '".$wano."' " ;

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrow = mysql_num_rows($res);

	if($numrow==0)
	{
		$q	= 	 " INSERT INTO ".$wbasedato."_000036  (Comuco,Comucm,Comucr,Comfor,Comgco,Comcom,Comdes,Comper,Comano,Comfco,Fecha_data,Seguridad,Medico) "
                    ." VALUES ('".$wempleado."', "
					."         '".$usuariocomprometido."' , "
                    ."         '".$wcalificador."' , "
					."         '".$form[1]."',"
					."         '".$form[2]."',"
					."         '".$form[3]."',"
					."         '".$form[4]."',"
					."         '".$wperiodo."',"
					."         '".$wano."',"
					."         '".$wcalificacion."',"
					."         '".$fecha."',"
                    ."         'C-".$wcalificador."' ,"
					."		   '".$wbasedato."') ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());



	}
	else
	{
		$q  =	"UPDATE ".$wbasedato."_000036   "
			   ."   SET Comfco='".$wcalificacion."' "
			   ." WHERE Comuco= '".$wempleado."'"
			   ."   AND  Comucr= '".$wcalificador."' "
			   ."   AND Comucm= '".$usuariocomprometido."' "
			   ."   AND  Comfor= '".$form[1]."' "
			   ."   AND  Comgco= '".$form[2]."' "
			   ."   AND  Comcom= '".$form[3]."' "
			   ."   AND  Comdes= '".$form[4]."' "
			   ."   AND  Comper= '".$wperiodo."' "
			   ."   AND  Comano= '".$wano."' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	}

}


function guardaCalificacion($wid,$wcalificacion,$wempleado,$wnumprueba,$wcalificador,$wfo_gc_co_de,$wperiodo,$wano,$wtipoevaluacion,$wrotulo)
{

    global $wbasedato;
	global $conex;
	$fecha= date("Y-m-d");
	$hora = date("H:i:s");
	$element= explode("-",$wfo_gc_co_de);

	if($wtipoevaluacion=='02')
	{

	}
	if($wtipoevaluacion=='01' OR $wtipoevaluacion=='03' OR $wtipoevaluacion=='04' OR $wtipoevaluacion=='05'  )
	{

		if($wcalificacion!="")
		{

			$q =	" SELECT COUNT(Evafor) "
					."  FROM ".$wbasedato."_000007 "
					." WHERE Evafor = '".$wid."' "
					."   AND Evaevo = '".$wempleado."'"
					."   AND Evaevr = '".$wcalificador."'"
					."   AND Evanup = '".$wnumprueba."' "
					."   AND Evaano = '".$wano."' "
					."   AND Evaper = '".$wperiodo."' ";

			 $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			 $row = mysql_fetch_array($res);
			 
			 
			/* $q="SELECT encper
				  FROM ".$wbasedato."_000049 
				 WHERE Enchis='".$element[0]."'  
				 ORDER BY  encper 
				 LIMIT 1 ";
			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			if ($row=mysql_fetch_array($res))
			{
				
				$wperiodo= ($row['encper']*1) + 1;
				
			}*/
			
			
			if($row[0]==0)
			{

				echo "Inserto";

				$q	= 	 " INSERT INTO ".$wbasedato."_000007  (Evaevo,Evafor,Evafco,Evagco,Evacom,Evades,Evanup,Evaevr,Evafec,Evacal,Evaper,Evaano,Evadat,Medico,Fecha_data,Hora_data,Seguridad) "
						." VALUES ('".$wempleado."', "
						."         '".$wid."' , "
						."         '".$element[0]."' , "
						."         '".$element[1]."' , "
						."         '".$element[2]."' , "
						."         '".$element[3]."' , "
						."         '".$wnumprueba."' , "
						."         '".$wcalificador."',"
						."         '".$fecha."',"
						."         '".$wcalificacion."' ,"
						."         '".$wperiodo."',"
						."         '".$wano."',"
						."         '".$wrotulo."',"
						."         '".$wbasedato."',"
						."         '".$fecha."',"
						."         '".$hora."',"
						."		   'C-".$wcalificador."')";

				
				echo $q;

				$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			}
			else
			{
				echo "modifico";
				 $q = "UPDATE ".$wbasedato."_000007 "
					. "   SET Evacal= '".$wcalificacion."', "
					. "       Evadat= '".$wrotulo."' "
					. " WHERE Evafor = '".$wid."' "
					. "   AND Evaevo = '".$wempleado."'"
					."    AND Evaevr = '".$wcalificador."'"
					. "   AND Evanup = '".$wnumprueba."' "
					."    AND Evaano = '".$wano."' "
					."    AND Evaper = '".$wperiodo."' ";

				 echo $q;
				 $res = mysql_query($q,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


			}
		}
		else
		{
			echo "elimino";
			$q = " DELETE "
				."   FROM ".$wbasedato."_000007 "
				. " WHERE Evafor = '".$wid."' "
				. "   AND Evaevo = '".$wempleado."'"
				. "   AND Evaevr = '".$wcalificador."'"
				. "   AND Evanup = '".$wnumprueba."' "
				. "   AND Evaper = '".$wperiodo."' "
				. "   AND Evaano = '".$wano."' ";

			$res = mysql_query($q,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	echo $q;
		}

	}

}

function guardaEvaluacion($wcalificador,$wempleado,$wperiodo,$wano,$wnumprueba,$wformulario,$wcaltotal,$wtipoevaluacion,$wcalificacion,$wcalificaciones)
{
	global $wbasedato;
	global $conex;
	$fecha= date("Y-m-d");
	$hora = date("H:i:s");
	if($wtipoevaluacion=='01' OR $wtipoevaluacion=='03' OR $wtipoevaluacion=='04' OR  $wtipoevaluacion=='05'  )
	{

		$q	= 	 " INSERT INTO ".$wbasedato."_000032  (Mcaano,Mcaper,Mcaucr,Mcanpu,Mcafor,Mcauco,Mcatfo,Medico,Fecha_data,Hora_data,Seguridad) "
				." VALUES ('".$wano."', "
				."         '".$wperiodo."' , "
				."         '".$wcalificador."' , "
				."         '".$wnumprueba."' , "
				."         '".$wformulario."' , "
				."         '".$wempleado."' , "
				."         '".$wtipoevaluacion."' ,"
				."		   '".$wbasedato."',"
				."		   '".date('Y-m-d')."', "
				."         '".$hora."' , "
				."		   'C-".$wcalificador."') ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		$q	= 	 " INSERT INTO ".$wbasedato."_000035  (Totcdr,Totcdo,Totano,Totper,Tottip,Totcod,Tottot,Medico,Fecha_data,Hora_data,Seguridad) "
				." VALUES ('".$wcalificador."', "
				."         '".$wempleado."' , "
				."         '".$wano."' , "
				."         '".$wperiodo."' , "
				."         'formulario' , "
				."         '".$wformulario."' , "
				."         '".$wcaltotal."' ,"
				."         '".$wbasedato."' ,"
				."		   '".date('Y-m-d')."', "
				."		   '".$hora."' ,"
				."         'C-".$wcalificador."' ) ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		echo $q;
		// if($wtipoevaluacion=='03')
		// {
			// $q	= "  UPDATE  ".$wbasedato."_000049 "
			// ."	    SET	 Encese = '".$wencuestaestado."' ,"
			// ." 			 Enccom = '".$wencuestacomentario."' "
			// ."	  WHERE  Enchis = ".$whistoria." "
			// ."      AND  Encing = ".$wingreso." "
			// ."      AND  Encenc = ".$wencuesta." ";

			// $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			// return;
		// }

	}
	if($wtipoevaluacion=='02'  )
	{
		$q  =    " SELECT COUNT(Mcauco)    "
		        ."   FROM ".$wbasedato."_000032  "
				."  WHERE Mcatfo = 02     ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row =mysql_fetch_array($res);

		$wempleado="anonimo".$row[0];

		$q	= 	 " INSERT INTO ".$wbasedato."_000032  (Mcaano,Mcaper,Mcaucr,Mcanpu,Mcafor,Mcauco,Mcatfo) "
				." VALUES ('".$wano."', "
				."         '".$wperiodo."' , "
				."         '".$wcalificador."' , "
				."         '".$wnumprueba."' , "
				."         '".$wformulario."' , "
				."         '".$wempleado."' , "
				."         '".$wtipoevaluacion."' ) ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		$q	= 	 " INSERT INTO ".$wbasedato."_000035  (Totcdr,Totcdo,Totano,Totper,Tottip,Totcod,Tottot) "
				." VALUES ('".$wcalificador."', "
				."         '".$wempleado."' , "
				."         '".$wano."' , "
				."         '".$wperiodo."' , "
				."         'formulario' , "
				."         '".$wformulario."' , "
				."         '".$wcaltotal."' ) ";


		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$wauxcal = explode("*",$wcalificacion);
		$wauxcals = explode("*",$wcalificaciones);
		$auxiliar = '';


		for($i=0; $i<count($wauxcal); $i++)
		{
			 $wcalificacion = $wauxcal[$i];
			 $element = explode("-",$wcalificacion);

			 $wcalificaciones = $wauxcals[$i];;


			if ($wcalificacion!="")
			{
				echo "Inserto";

				$q	= 	 " INSERT INTO ".$wbasedato."_000007  (Evaevo,Evafor,Evafco,Evagco,Evacom,Evades,Evanup,Evaevr,Evafec,Evacal,Evaper,Evaano,Seguridad) "
						." VALUES ('".$wempleado."', "
						."         '".$wid."' , "
						."         '".$element[0]."' , "
						."         '".$element[1]."' , "
						."         '".$element[2]."' , "
						."         '".$element[3]."' , "
						."         '46' , "
						."         '".$wcalificador."',"
						."         '".$fecha."',"
						."         '".$wcalificaciones."' ,"
						."         '".$wperiodo."',"
						."         '".$wano."',"
						."		   'C-".$wcalificador."')";

				$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				// $auxiliar = $auxiliar."----".$q;
			}

		}
		// echo $auxiliar ;

	}
}

function redondear_dos_decimal($valor)
{
	$float_redondeado=round($valor * 100) / 100;
    return $float_redondeado;
}

function verRelaciones ($UserBuscado,$UserLogueado,$wbasedato)
{




global $wtema;
global $wcodtab;
global $wemp_pmla;

$permisos = consultarSiEsAdmin($conex, $wemp_pmla, $wtema, $wcodtab, $_SESSION['user']);

 if ($UserBuscado == $UserLogueado)
 {
  $perfilver="si";
 }
 else if($permisos['esAdmin']=='on' )
 {
  $perfilver="si";

 }
 else
 {

  $q =  " SELECT * "
       ."   FROM ".$wbasedato."_000008 "
	   ."  WHERE Ajeucr = '".$UserLogueado."' "
	   ."    AND Ajeuco = '".$UserBuscado."' ";

   $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $numresul = mysql_num_rows($res);

   if ($numresul >  0)
   {
     $perfilver="si";
   }
   else
   {
     $perfilver="no";
   }
 }
 return $perfilver;
}
?>
</body>
</html>
</div>