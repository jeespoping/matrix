<?php
include_once("conex.php");
header("Cache-Control: no-store, no-cache, must-revalidate");




include_once("root/magenta.php");
include_once("root/comun.php");

//=========================================================================================================================================\\
//DESCRIPCION                                                                                                                              \\
//=========================================================================================================================================\\
//En este programa muestra los productos asociados a una historia e ingreso en un tooltip del programa de las dietas.                      \\
//=========================================================================================================================================\\
//Octubre 30 de 2020 Edwin MG
//Se valida fecha vacia debido a cambio de BD
//Mayo 15 de 2013
//Se modifica la consulta de los ultimos productos de DSN basado en el ultimo registro de la 77 activo o inactivo.
//=========================================================================================================================================


global $winfo;

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

 //Consulta la ultima fecha en que se haya registrado un patron DSN, pero que sea mayor o igual a la fecha actual.
    function consultar_ult_reg($whis, $wing, $wser, $wcco, $wpatron)
    {

        global $wbasedato;
        global $conex;
        global $wfecha;

        $q1= "  SELECT fecha_data "
            ."    FROM ".$wbasedato."_000077  "
            ."   WHERE movhis = '".$whis."'"
            ."     AND fecha_data >= '".$wfecha."'"
            ."     AND moving = '".$wing."'"
            ."     AND movcco = '".$wcco."'"
            ."     AND movser = '".$wser."'"
            ."     AND movdie = '".$wpatron."'"
            ."     AND movest = 'on'"
           ." ORDER BY fecha_data DESC limit 1";
        $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
        $row1 = mysql_fetch_array($res1);

        return $row1['fecha_data'];
    }

//Consulta horarios de un servicios
 function consultar_horarios_servicio($wservicio)
    {
        global $conex;
        global $wbasedato;

      $q = "   SELECT serhin, serhfi, serhia, serhad "
            ."     FROM ".$wbasedato."_000076 "
            ."    WHERE sercod='".$wservicio."'";
	  $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
      $row = mysql_fetch_array($res);

      return $row['serhin']."-".$row['serhfi']."-".$row['serhia']."-".$row['serhad'];

    }

 function traer_nombre_servicioind($wcod) {

        global $conex;
        global $wbasedato;


        $query =   "SELECT Procod, Prodes"
                . "   FROM ".$wbasedato."_000082"
                . "  WHERE Procod = '".$wcod."'"
                . "    AND Proest = 'on'";

        $res = mysql_query($query, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
        $row = mysql_fetch_array($res);

        return $row['Prodes'];


    }


 function traer_nombre_servicio($wser) {

        global $conex;
        global $wbasedato;


        $query =   "SELECT Sercod, Sernom"
                . "   FROM ".$wbasedato."_000076"
                . "  WHERE Sercod = '".$wser."'"
                . "    AND Serest = 'on'";

        $res = mysql_query($query, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
        $row = mysql_fetch_array($res);

        return $row['Sernom'];


    }


//Valida si es no valida horario, solo aplica para DSN.
if ($wnovalidah != 'novalida')
    {
        //Busco si esta opcion esta grabada para el paciente en la tabla 000084
        $q = " SELECT Detpro, Detser, Detcan "
            ."   FROM ".$wbasedato."_000084 "
            ."  WHERE detfec = '".$wfecha."'"
            ."    AND dethis = '".$whis."'"
            ."    AND deting = '".$wing."'"
            ."    AND detser = '".$wser."'"
            ."    AND detpat = '".$wpatron."'"
            ."    AND detcco = '".$wcco."'"
            ."    AND detest = 'on' "
           ."ORDER BY detser ASC";

        $respro = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num_pro = mysql_num_rows($respro);

        if ($num_pro != 0)
            {
                while ($rowpro = mysql_fetch_array($respro))
                {
                $winfo .= $rowpro['Detcan']." ".utf8_encode(traer_nombre_servicioind($rowpro['Detpro']))."<br>";
                }
                echo $winfo;
            }
        else
        {
            echo "No tiene productos relacionados.";
        }
    }
 else
     {

     //Consulto los servicio activos
      $q =   " SELECT sercod "
            ."   FROM ".$wbasedato."_000076 "
            ."  WHERE serest = 'on'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);

      //Recorro cada servicio
	  for ($i=1;$i<=$num;$i++)
	     {

            $row = mysql_fetch_array($res);

            $wservicio = $row['sercod'];

            $wult_fecha = consultar_ult_reg($whis, $wing, $wservicio, $wcco, $wpatron);

           //Consulto los productos para el paciente dependiendo del servicio y la fecha segun el horario actual.
           $q2 =     " SELECT Detpro, Detser, Detcan, Sernom, detfec"
                    ."   FROM ".$wbasedato."_000084, ".$wbasedato."_000076"
                    ."  WHERE dethis = '".$whis."'"
                    ."    AND deting = '".$wing."'"
                    ."    AND detpat = '".$wpatron."'"
                    ."    AND detfec = '".( !empty( $wult_fecha ) ? $wult_fecha : '0000-00-00' )."'"
                    ."    AND detser = sercod"
                    ."    AND detser = '".$wservicio."'"
                    ."    AND detcco = '".$wcco."'"
                    ."    AND detest = 'on' "
                    ."ORDER BY detser, detfec DESC";
            $respro2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
            $num_pro2 = mysql_num_rows($respro2);
			
            if ($num_pro2 != 0)
                {
                    $arr_resp = array();
                    $winfo = '';
                    //Con la respuesta de la consulta se crea un arreglo con la informacion de ese servicio
                    while ($rowpro = mysql_fetch_array($respro2))
                    {
                        if(!array_key_exists($rowpro['Sernom'], $arr_resp)) { $arr_resp[$rowpro['Sernom']] = array(); }

                        $wservi = $rowpro['Sernom'];
                        $arr_resp[$wservi][] = array('producto'=> utf8_encode(traer_nombre_servicioind($rowpro['Detpro'])),'cantidad'=> $rowpro['Detcan']);

                    }
                    //Se lee el arreglo y crea un modelo de texto con los nombres, las cantidades y el servicio,
                    //donde el servicio sera la clave primaria del arreglo
                    foreach($arr_resp as $key => $value)
                    {
                        $winfo .= utf8_encode($key).'<br/>';
                        foreach($value as $keyP => $valueP)
                        {
                            $winfo .= '&nbsp;&nbsp;&nbsp;'.$valueP['cantidad'].' '.$valueP['producto'].'<br />';
                        }

                    }

            echo $winfo;

            $num_pro++;
              }

       }
       //Si no hay productos para el paciente, se mostrará este mensaje
       if($num_pro == 0)
          {
            echo "No tiene productos relacionados.";
          }
}
?>