<?php
include_once("conex.php");
 /***********************************************************************************************************
 *
 * Programa				   :	Administracion Segunda Clave para Nomina
 * Fecha de Creación :	2016-06-25
 * Autor				     :	Arleyda Insignares Ceballos
 * Descripcion			 :	Administración de Usuarios para Acceder al Reporte de Horas Extras.
 *                      Permite adicionar e inactivar usuarios, agregar y/o modificar segunda clave.
 *
 ***********************************************************************************************************
 *   Modificaciones
 *
 *   2017-11-29 -Arleyda Insignares C. -Se adiciona herramienta Tooltip para mostrar mensajes en el encabezado.
 *
 *   2017-11-23 -Arleyda Insignares C. -Se adiciona proceso para administrar la lista de empleados utilizada
 *                                      por un coordinador, para el reporte de horas extras y recargos.
 *                                     -Utiliza la tabla 'rephor_000006' para grabar el código del coordinador
 *                                      y el empleado. */

 $wactualiz = "2017-11-23";

 if(!isset($_SESSION['user'])){
  	echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
  		<tr><td>Error, inicie nuevamente</td></tr>
  		</table></center>";
  	return;
 }

 header('Content-type: text/html;charset=UTF-8');

  //********************************** Inicio  *************************************************************


	include_once("root/comun.php");

	$conex         = obtenerConexionBD("matrix");
	$wbasedato     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rephor');
	$wbasetalhuma  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
  $wtabcco       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
	$wfecha        = date("Y-m-d");
	$whora         = (string)date("H:i:s");
	$pos           = strpos($user,"-");
	$wusuario      = substr($user,$pos+1,strlen($user));
	$wcodusu       = '';
  $wnomusu       = '';
  $wclausu       = '';
  $usuario_exis  = '';

     // ***************************************    FUNCIONES AJAX    **********************************************

      /* Seleccionar los empleados que tiene asignado el coordinador y en caso de que no
         tenga empleados a cargo mostrar los empleados del respectivo centro de costos */

      if ( isset($_POST["accion"]) && $_POST["accion"] == "ActivarEmpleados" )
      {

          $data = array();

          $arr_empleados = array();

          $arr_emptodos  = array();

          $data['error'] = 0;

          $empact = 'N';

          $q  = " SELECT  A.Ideuse, concat(C.Ideno1,' ',C.Ideno2,' ',C.Ideap1,' ',C.Ideap2) as Nomemp,
                          B.Repemp, B.Repcoo, D.Cconom, D.Ccocod
                    From  ".$wbasetalhuma."_000013 A
                    Inner join  ".$wbasedato."_000006 B
                          on A.Ideuse = B.Repcoo
                    Inner join  ".$wbasetalhuma."_000013 C
                          on B.Repemp = C.Ideuse
                    Inner Join ".$wtabcco." D on D.Ccocod = A.Idecco
                    Where ( substr(A.Ideuse,1,5) = '".substr(trim($wempleado),0,5)."' )
                            And A.Ideest != 'off'
                            And D.Ccoemp = '".$wemp_pmla."'
                  UNION
                    SELECT  A.Ideuse, concat(A.Ideno1,' ',A.Ideno2,' ',A.Ideap1,' ',A.Ideap2) as Nomemp,
                          B.Repemp, B.Repcoo, C.Cconom, C.Ccocod
                    From  ".$wbasetalhuma."_000013 A
                    Left  join  ".$wbasedato."_000006 B
                          on A.Ideuse = B.Repemp
                    Inner Join ".$wtabcco." C on C.Ccocod = A.Idecco
                    Where ( A.Idecco in (".$wcentro.")
                            And isnull(B.Repemp)
                            And C.Ccoemp = '".$wemp_pmla."' )
                    Order by Cconom asc, Repcoo desc , Nomemp asc"  ;

          $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

          $num = mysql_num_rows($res);

          $cont= 0;

          $codigo_anterior  = '';

          while($row = mysql_fetch_assoc($res))
          {

                  if ($row['Cconom'] == $codigo_anterior)
                      $centroc = '';
                  else
                      $centroc = $row['Cconom'];


                  $codigo_anterior = $row['Cconom'];


                  if ( $row['Repcoo'] == null ){

                       if ($row['Ideuse'] != $wempleado){

                           $arr_empleados[] = array( "seleccion_empleado" => 'N',
                                                     "codigo_empleado"    => $row['Ideuse'],
                                                     "nombre_empleado"    => utf8_encode($row['Nomemp']),
                                                     "centro_empleado"    => utf8_encode($row['Cconom']),
                                                     "codcen_empleado"    => utf8_encode($row['Ccocod']) );

                           $cont++;
                       }
                  }

                  else{

                       $empact = 'S';

                       $cont++;

                       $arr_empleados[] = array( "seleccion_empleado" => 'S',
                                                 "codigo_empleado"    => $row['Repemp'],
                                                 "nombre_empleado"    => utf8_encode($row['Nomemp']),
                                                 "centro_empleado"    => utf8_encode($row['Cconom']),
                                                 "codcen_empleado"    => utf8_encode($row['Ccocod']) );
                  }

          }


          $data['accesoempleado'] = $empact ;

          $data['arrtodos'] = $arr_empleados;

          // En caso de no encontrar registro
          if ($cont > 0)
              $data['error'] = 1;

          echo json_encode($data);

          return;

      }


      /* Asignar los empleados seleccionados al coordinador, grabandolos en la tabla rephor_000006*/
      if ( isset($_POST["accion"]) && $_POST["accion"] == "GrabarEmpleados" )
      {

          $wempleados = explode('|', $wstringemp);

          if (!empty($wstringemp)){

              // Borrar todos los empleados asignados al coordinador
              $q = " Delete From  ".$wbasedato."_000006
                           WHERE substr(Repcoo,1,5) = '".$wcoordinador."' ";

              $wcoordinador = $wcoordinador.'-01';

              $rescon = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

              for ($x=1;$x<count($wempleados); $x++)
              {

                   $q = " INSERT INTO ".$wbasedato."_000006
                       (Medico,Fecha_data,Hora_data,Repcoo,Repemp,Repest,Seguridad)
                        VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wcoordinador."','".$wempleados[$x-1]."','on','C-".$wusuario."') ";

                   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
              }

              echo $res;

          }

          return;

      }


      // Verificar si el empleado se encuentra asignado al coordinador
      if ( isset($_POST["accion"]) && $_POST["accion"] == "ConsultarCoordinador" )
      {

         $respuesta = 'N';

         $q  = " SELECT Ideuse, concat(Ideno1,' ',Ideno2,' ',Ideap1,' ',Ideap2) as Nomemp, Repcoo
                 From ".$wbasedato."_000006
                 Inner join ".$wbasetalhuma."_000013
                       on Repcoo = Ideuse
                 Where Repcoo = '".$wcoordinador."'
                   And Repemp = '".$wempleado."'
                   And Ideest != 'off'"  ;

         $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

         $num = mysql_num_rows($res);

         if ($num>0){

             $row = mysql_fetch_assoc($res);

             $respuesta = 'S|'.$row['Nomemp'];
         }

         echo $respuesta;

         return;

      }


       // Consulta Especialidad para luego armar el array con los Docentes correspondientes a ésta.
        if (isset($_POST["accion"]) && $_POST["accion"] == "CambiarUsuarios"){

           $strtipvar = array();

           if ($wopcion=='1')
           {
               $strtipvar = array();
               $q   = " SELECT codigo, descripcion
                       From usuarios A
                       Where activo ='A'";

               $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
               while($row = mysql_fetch_assoc($res))
               {
                      $strtipvar[$row['codigo']] = utf8_encode($row['descripcion']);
               }
           }
           else
           {
               $q = " SELECT A.Ajeucr, A.Ajeccr, concat(B.Ideno1,' ', B.Ideno2, ' ', B.Ideap1, ' ', B.Ideap2) as 'Nomemp'
                    FROM ".$wbasetalhuma."_000008 A
                    Inner join ".$wbasetalhuma."_000013 B
                    on A.Ajeucr = B.Ideuse
                    Group By A.Ajeucr
                    Order by A.Ajeucr";

               $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
               while($row = mysql_fetch_assoc($res))
               {
                     $strtipvar[$row['Ajeucr']] = utf8_encode($row['Nomemp']);
               }

           }

           echo json_encode($strtipvar);
           return;
        }


        //----------------------------------------------------------------------------------------------------------
        if  (isset($_POST["accion"]) && $_POST["accion"] == "ActualizarCentros"){

            $q=" UPDATE ".$wbasedato."_000001
    			       SET Carne_nomina = '".$centro_usu."'
    			       WHERE id = '".$identi_usu."'" ;

    		    $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    		    echo $resp;

            return;
        }


        //----------------------------------------------------------------------------------------------------------

        if  (isset($_POST["accion"]) && $_POST["accion"] == "ActualizarUsuarios"){

            $strtipvar = array();

            $q  =  " SELECT codigo, descripcion"
                  ." From usuarios A "
                  ." where activo ='A'";

            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            while($row = mysql_fetch_assoc($res))
            {
                  $strtipvar[$row['codigo']] = utf8_encode($row['descripcion']);
            }

            return $strtipvar;
        }


        //----------------------------------------------------------------------------------------------------------
		    if (isset($_POST["accion"]) && $_POST["accion"] == "ReiniciarClave"){

            if ($wnuevo_usu =='S')
            {
            	  $q =  " INSERT INTO ".$wbasedato."_000001
	                    (Medico,Fecha_data,Hora_data,Usuario_matrix,Carne_nomina,Seguridad,Clave_nomina)
        					    VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wcod_usu."','".$wcen_usu."','C-".$wusuario."','".$wcla_usu."') ";

        				     $resp = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			      }
            else
            {
  	            $q=" UPDATE ".$wbasedato."_000001
      				       SET Clave_nomina = '".$wcla_usu."'
      				       WHERE id = '".$wide_usu."'" ;
  				      $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			      }

      			echo $resp;
      			return;
      	}


        //----------------------------------------------------------------------------------------------------------

  	    if (isset($_POST["accion"]) && $_POST["accion"] == "InactivarUsuario"){

            $q1 ="DELETE FROM ".$wbasedato."_000001 "
  			       . " WHERE id = '".$widusuario."' ";

     			  $res = mysql_query($q1,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());

            $q2 ="DELETE FROM ".$wbasedato."_000006 "
               . " WHERE substr(Repcoo,1,5) = '".$wcoordinador."' ";

            $res = mysql_query($q2,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());

     			  echo $res;

  		      return;

  	    }


      //----------------------------------------------------------------------------------------------------------
	    if (isset($_POST["accion"]) && $_POST["accion"] == "SeleccionarUsuario"){

        $pos = strpos($codigo_doc, '-');

        if ($pos == false)
        {
  			    $q = " SELECT A.Codigo, A.Descripcion, A.Activo, A.Ccostos, B.Usuario_matrix, B.Carne_nomina, B.Clave_nomina, B.id "
  	          ."   FROM usuarios A Left Join ".$wbasedato."_000001 B on A.Codigo = B.Usuario_matrix "
  	          ."   WHERE A.Codigo = '".$codigo_doc."' ";
            $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        }
        else
        {

           $varusuario1  = substr($codigo_doc,0,strlen($codigo_doc)-3);
           $prefijo      = substr($codigo_doc,5,3);
           $varusuario2  = $prefijo . substr($codigo_doc,0,strlen($codigo_doc)-3);

           $q = " SELECT A.Codigo, A.Descripcion, A.Activo, A.Ccostos, B.Usuario_matrix, B.Carne_nomina, B.Clave_nomina, B.id "
           ."   FROM usuarios A Left Join ".$wbasedato."_000001 B on A.Codigo = B.Usuario_matrix "
           ."   WHERE A.Codigo = '".$varusuario1."' ";
           $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

           $num = mysql_num_rows($res);

           if ($num = 0)
           {
                $q = " SELECT A.Codigo, A.Descripcion, A.Activo, A.Ccostos, B.Usuario_matrix, B.Carne_nomina, B.Clave_nomina, B.id "
                ."   FROM usuarios A Left Join ".$wbasedato."_000001 B on A.Codigo = B.Usuario_matrix "
                ."   WHERE A.Codigo = '".$varusuario1."' ";
                $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
           }

        }

		    $num = mysql_num_rows($res);

        if ($num > 0)
		       {
			       	 $row         = mysql_fetch_assoc($res);
			       	 $vresultado  = $row['Descripcion'] . ";" . $row['Ccostos'] . ";" .
                              $row['Activo'] . ";" . $row['Carne_nomina'] . ";" .
                              $row['Clave_nomina']. ";" . $row['id'];

			       	 $vcentros    = $row['Carne_nomina'];

			       	 if ( $row['Usuario_matrix'] == null )

                   $vresultado  .=  ";N";

			       	 else

			       	 	   $vresultado  .=  ";S";

		       }

		       echo $vresultado;
           return;

        }


        //----------------------------------------------------------------------------------------------------------
        if (isset($_POST["accion"]) && $_POST["accion"] == "SeleccionarCentros"){

            $array_     = unserialize(base64_decode($array_cen));
            $cencos     = explode(',', $centrosel);
            $vresultado = '';
            $varrayres  = array();
            $cont1      = 0;

            foreach ($array_ as $centros => $nombre) {
           		       for($j=0;$j<count($cencos);$j++){
              			      if ($centros == $cencos[$j])
              				    {
              	 			 	     $cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
              		           $cont1++;
              	             $vresultado .= '<tr class="'.$clase.'"><td>'.$centros.'</td><td>'.$nombre.'</td><td align="center"><input type="checkbox" id="chktodos" name="chktodos" value="'.$centros.'" onclick="VerificarCentro(this);"></td></tr>';
              				    }
              			 }
      			}

            $varrayres['resultado'] = $vresultado;
            $varrayres['total']     = $cont1;
            echo json_encode($varrayres);

            return;
        }


      // Consultar los usuarios para el campo autocompletar
	    function consultarUsuarios($wbasedato,$conex,$wemp_pmla){

        $strtipvar = array();
			  $q  = " SELECT codigo, descripcion"
				."   From usuarios A "
				."   where activo ='A'";

  			$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
  			while($row = mysql_fetch_assoc($res))
  			     {
  			 	     $strtipvar[$row['codigo']] = utf8_encode($row['descripcion']);
  			 	   }

  			return $strtipvar;

	    }


      // Consultar todos los Centros de Costos para el campo autocompletar
	    function consultarCentros($wbasetalhuma,$conex,$wemp_pmla){

      $strtipvar = array();

			$q = "  SELECT  Empdes,Emptcc
                    FROM    root_000050
                    WHERE   Empcod = '".$wemp_pmla."'";
            $res = mysql_query($q,$conex);

            if($row = mysql_fetch_array($res))
            {
                $tabla_CCO = $row['Emptcc'];
                switch ($tabla_CCO)
                {
                    case "clisur_000003":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                        FROM    clisur_000003 AS tb1
                                                INNER JOIN
                                                ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Ccodes";
                            break;
                    case "farstore_000003":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                        FROM    farstore_000003 AS tb1
                                                INNER JOIN
                                                ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Ccodes";
                            break;
                    case "costosyp_000005":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                        FROM    costosyp_000005 AS tb1
                                                INNER JOIN
                                                ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Cconom";
                            break;
                    case "uvglobal_000003":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                        FROM    uvglobal_000003 AS tb1
                                                INNER JOIN
                                                ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Ccodes";
                            break;
                    default:
                            $query="    SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                        FROM    costosyp_000005 AS tb1
                                                INNER JOIN
                                                ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Cconom";
                }

            $res = mysql_query($query,$conex);
    				$res = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
    				while($row = mysql_fetch_assoc($res))
    			     {
                 $aux = explode("-",$row['nombre']);
                 if( trim($aux[0]) == "NO USAR" )
                  continue;
    			 	     $strtipvar[$row['codigo']] = $row['nombre'];
    			 	   }
            }

			return $strtipvar;
	    }


     // *****************************************         FIN PHP         ********************************************

	?>
	<html>
	<head>
		<title>Administraci&oacute;n Usuarios - Reporte de Horas</title>
		<meta charset="utf-8">
		<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script type="text/javascript">
		  // Funciones para la Encriptación de claves
		  function hex_sha1(a){return rstr2hex(rstr_sha1(str2rstr_utf8(a)))}
		  function hex_hmac_sha1(a,b){return rstr2hex(rstr_hmac_sha1(str2rstr_utf8(a),str2rstr_utf8(b)))}
		  function sha1_vm_test(){return hex_sha1("abc").toLowerCase()=="a9993e364706816aba3e25717850c26c9cd0d89d"}
		  function rstr_sha1(a){return binb2rstr(binb_sha1(rstr2binb(a),a.length*8))}
		  function rstr_hmac_sha1(c,f){var e=rstr2binb(c);if(e.length>16){e=binb_sha1(e,c.length*8)}var a=Array(16),d=Array(16);for(var b=0;b<16;b++){a[b]=e[b]^909522486;d[b]=e[b]^1549556828}var g=binb_sha1(a.concat(rstr2binb(f)),512+f.length*8);return binb2rstr(binb_sha1(d.concat(g),512+160))}
		  function rstr2hex(c){try{hexcase}catch(g){hexcase=0}var f=hexcase?"0123456789ABCDEF":"0123456789abcdef";var b="";var a;for(var d=0;d<c.length;d++){a=c.charCodeAt(d);b+=f.charAt((a>>>4)&15)+f.charAt(a&15)}return b}
		  function str2rstr_utf8(c){var b="";var d=-1;var a,e;while(++d<c.length){a=c.charCodeAt(d);e=d+1<c.length?c.charCodeAt(d+1):0;if(55296<=a&&a<=56319&&56320<=e&&e<=57343){a=65536+((a&1023)<<10)+(e&1023);d++}if(a<=127){b+=String.fromCharCode(a)}else{if(a<=2047){b+=String.fromCharCode(192|((a>>>6)&31),128|(a&63))}else{if(a<=65535){b+=String.fromCharCode(224|((a>>>12)&15),128|((a>>>6)&63),128|(a&63))}else{if(a<=2097151){b+=String.fromCharCode(240|((a>>>18)&7),128|((a>>>12)&63),128|((a>>>6)&63),128|(a&63))}}}}}return b}
		  function rstr2binb(b){var a=Array(b.length>>2);for(var c=0;c<a.length;c++){a[c]=0}for(var c=0;c<b.length*8;c+=8){a[c>>5]|=(b.charCodeAt(c/8)&255)<<(24-c%32)}return a}
		  function binb2rstr(b){var a="";for(var c=0;c<b.length*32;c+=8){a+=String.fromCharCode((b[c>>5]>>>(24-c%32))&255)}return a}function binb_sha1(v,o){v[o>>5]|=128<<(24-o%32);v[((o+64>>9)<<4)+15]=o;var y=Array(80);var u=1732584193;var s=-271733879;var r=-1732584194;var q=271733878;var p=-1009589776;for(var l=0;l<v.length;l+=16){var n=u;var m=s;var k=r;var h=q;var f=p;for(var g=0;g<80;g++){if(g<16){y[g]=v[l+g]}else{y[g]=bit_rol(y[g-3]^y[g-8]^y[g-14]^y[g-16],1)}var z=safe_add(safe_add(bit_rol(u,5),sha1_ft(g,s,r,q)),safe_add(safe_add(p,y[g]),sha1_kt(g)));p=q;q=r;r=bit_rol(s,30);s=u;u=z}u=safe_add(u,n);s=safe_add(s,m);r=safe_add(r,k);q=safe_add(q,h);p=safe_add(p,f)}return Array(u,s,r,q,p)}
		  function sha1_ft(e,a,g,f){if(e<20){return(a&g)|((~a)&f)}if(e<40){return a^g^f}if(e<60){return(a&g)|(a&f)|(g&f)}return a^g^f}function sha1_kt(a){return(a<20)?1518500249:(a<40)?1859775393:(a<60)?-1894007588:-899497514}
		  function safe_add(a,d){var c=(a&65535)+(d&65535);var b=(a>>16)+(d>>16)+(c>>16);return(b<<16)|(c&65535)}
		  function bit_rol(a,b){return(a<<b)|(a>>>(32-b))};
		  $(document).ready(function(){

			//  *****************************      Asignar busqueda Autocompletar Usuario      ************************
			$('#wcodusu').focus();

			var arr_usu  = eval('(' + $('#arr_usu').val() + ')');
      var usuarios = new Array();
			var index   = -1;
            for (var cod_usu in arr_usu)
            {
                index++;
                usuarios[index]                = {};
                usuarios[index].value          = cod_usu;
                usuarios[index].label          = cod_usu+'-'+arr_usu[cod_usu];
                usuarios[index].codigo         = cod_usu;
            }

            $("#wcodusu").autocomplete({
		           source: usuarios,
               autoFocus: true,
    		       select:   function( event, ui ){
                        var cod_sel = ui.item.codigo;
                        $("#wcodusu").attr("codigo",cod_sel);
                        SeleccionUsuario(cod_sel);
               }
		        });

			//  *****************************      Asignar busqueda Autocompletar Centros de Costos    ************************
			var arr_cen  = eval('(' + $('#arr_cen').val() + ')');
      var centros = new Array();
			var index   = -1;
            for (var cod_cen in arr_cen)
            {
                index++;
                centros[index]                = {};
                centros[index].value          = cod_cen;
                centros[index].label          = cod_cen+'-'+arr_cen[cod_cen];
                centros[index].codigo         = cod_cen;
            }
            $("#wcodcen").autocomplete({
  		          source: centros,
                autoFocus: true,
  		          select:     function( event, ui ){
                      var cod_sel = ui.item.codigo;
                      var nom_sel = ui.item.label;
                      $("#wcodcen").attr("codigo",cod_sel);
                      SeleccionCentro(cod_sel,nom_sel);
                }
		        });

            $('#wcodcen').on({
              focusout: function(e) {
                  if($(this).val().replace(/ /gi, "") == '')
                  {
                      $(this).val("");
                      $(this).attr("codigo","");
                      $(this).attr("label","");
                  }
                  else
                  {
                      $(this).val("");
                      $(this).attr("codigo","");
                      $(this).attr("label","");
                  }
              }
           });


		  	});  // Finalizar Ready()

	      // **************************************    Inicio Funciones Javascript    ************************************************

        // Funcion de grabado, para grabar segunda clave y centros de costos adicionados y relacionar los empleados a un coordinador
        function Actualizar()
        {

             $("#img_bus").show();
             $("#wedicion").val('0');

          	 var wemp_pmla   = $("#wemp_pmla").val();
          	 var identi_usu  = $("#wideusu").val();
          	 var nuevo_usu   = $("#wnueusuario").val();
          	 var codigo_usu  = $("#wcodusu").val();
             var centro_usu  = '';

             // Crear un string con todos los centros de costos
          	 vcentro='';

			       $('#tbllistacentro tbody tr').each(function() {

                vfila1  = $(this.cells[0]).text();
                cadeli  = $(this).find("#chktodos").prop('checked');

                if (cadeli==false)
                   vcentro = vcentro + vfila1  + ',';
                else
                   $(this).remove();

             });


             if (vcentro.length > 0 )
                 centro_usu = vcentro.substring(0, vcentro.length - 1);


             // Si activa el checkbox para resetear la contraseña
          	 if ($('input:checkbox[name=chkclave]:checked').val()=='on'){

            	 	 if ( $("#wclaveusu1").val().length<4 || $("#wclaveusu2").val().length<4 )
            	 	 {
                       alerta('La Contrase&ntilde;a debe tener m&iacute;nimo 4 caracteres');
  				             return;
            	 	 }

  	          	 clave_usu1  =  hex_sha1($("#wclaveusu1").val());
  	             clave_usu2  =  hex_sha1($("#wclaveusu2").val());

        	       if (clave_usu1 != clave_usu2)
        				 {
        				     alerta('La Claves no coinciden favor verificar');
        				     $("#img_bus").hide();
        				     return;
        				 }
        				 else
        				 {
          				   $.post("Nomina_admonusuarios.php",
          				   {
              						consultaAjax : true,
              						accion       : 'ReiniciarClave',
              						wemp_pmla    : wemp_pmla,
              						wcod_usu     : codigo_usu,
              						wide_usu     : identi_usu,
              						wnuevo_usu   : nuevo_usu,
              						wcen_usu     : centro_usu,
              						wcla_usu     : clave_usu1
            				 }, function(respuesta){

                          if (respuesta>0)
            					   	   alerta('La Clave ha sido Grabada');

            				 });
        				 }
			       }


    				 /* Actualizar los campos residente y validacion en Usuario */
    	       $.post("Nomina_admonusuarios.php",
      			 {
        					 consultaAjax :  true,
        	         async        :  false,
        					 accion       :  'ActualizarCentros',
        					 wemp_pmla    :  wemp_pmla,
        					 identi_usu   :  identi_usu,
        					 centro_usu   :  centro_usu
      			 }, function(respuesta){
      			       alerta('La informaci\u00F3n ha sido actualizada');
      			 });


  			     $("#img_bus").hide();
             var wcoordinador  = $("#wcodusu").val();


             /* Concatener los empleados seleccionados en un string */
             var stringemp = '';
             $("input[class^=chkseleccion]").each(function(){

                   if (this.checked){

                      var cadena = $(this).val();
                      var valor  = cadena.split("|");
                      stringemp = stringemp + valor[0] +'|';
                   }

             });


             /* Grabar los empleados seleccionados en rephor_000006 */
             $.post("Nomina_admonusuarios.php",
             {
                   consultaAjax :  true,
                   accion       :  'GrabarEmpleados',
                   async        :  false,
                   wemp_pmla    :  $("#wemp_pmla").val(),
                   wstringemp   :  stringemp,
                   wcoordinador :  wcoordinador

             }, function(respuesta){

                Consultar(wcoordinador);

             });


        }

        // -----------------   Verificar si el Centro de Costos puede ser eliminado   ------------------------
        function VerificarCentro(obj)
        {

             /* Verificar si los empleados pertenecen al centro de costos seleccionado*/
             var strresul = '0';

             $("input[class^=chkseleccion]").each(function(){

                   if (this.checked) {

                      var cadena = $(this).val();
                      var valor  = cadena.split("|");

                      if (valor[1] == obj.value)
                          strresul ='1' ;

                   }
             });

             if (strresul == '1'){
                 jAlert('El Centro de Costos tiene empleados asociados y no ser\u00E1 eliminado')
                 obj.checked = false;
                 return;
             }
        }


        // ----------------------  Eliminar un usuario de Nomina tabla rephor_000001   -----------------------
        function Inactivar()
        {

            var wemp_pmla     = $("#wemp_pmla").val();
          	var widusuario    = $("#wideusu").val();
            var wcoordinador  = $("#wcodusu").val();

          	$.post("Nomina_admonusuarios.php",
				    {
      						consultaAjax : true,
      						accion       : 'InactivarUsuario',
      						wemp_pmla    : wemp_pmla,
      						widusuario   : widusuario,
                  wcoordinador : wcoordinador
					  }, function(respuesta){
      					  if (respuesta>0)
      					  {
      					   		 alerta('El Usuario ha sido Inactivado');
       		          	 $("#tbllistacentro").hide();
       		          	 $("#tbllistacentro tbody ").remove(0);
      					       $("#tblagregarcentro").hide();
      					  }
					  });
        }

        // ********************************    Limpiar los campos del formulario y ocultar las tablas    ************************
        function Iniciar()
        {
          	$("input[type=text]").val('');
          	$("input[type=password]").val('');
          	$("#wactivo").attr('checked',false);
            $("#wclave").attr('checked',false);
          	$("#tbllistacentro").hide();
    	      $("#tblagregarcentro").hide();
    			  $("#tblbotones").hide();
            $("#conempleados").hide();
    			  $('#wcodusu').focus();
        }

      	function CerrarVentana()
        {
          if ($("#wedicion").val() == '1'){
              if (jConfirm("Existen cambios sin grabar, desea salir") == true)
                 window.close();

              else
                 return false;
          }else{
              window.close();
          }

        }


        // *************************    Activar dos campos de texto para digitar la nueva clave   ******************************
        function Activarclave(value)
        {
          	if(document.getElementById('chkclave').checked == true)
          	  $("td.activarclave").show();
            else
              $("td.activarclave").hide();
        }


        // *************************    Cambiar el autocompletar de usuarios segun el campo chktodos   *************************
        function CambiarUsuarios(value)
        {
              $("#wnomusu").val('');
              $("#wcodusu").val('');
              if(document.getElementById('chkcamusu').checked == true)
              {

                  //lLamada ajax para actualizar source del autocomplete de Docentes (x especialidad)
                  $.post("Nomina_admonusuarios.php",
                      {
                          consultaAjax:  true,
                          accion      :  'CambiarUsuarios',
                          wopcion     :  '1',
                          wemp_pmla   :  $("#wemp_pmla").val()
                      }, function(respuesta){

                          $('#arr_usu').val(JSON.stringify(respuesta));
                      },'json').done(function(){

                          iniciarusuarios();
                      });
              }
              else
              {
                  $.post("Nomina_admonusuarios.php",
                     {
                         consultaAjax:  true,
                         accion      :  'CambiarUsuarios',
                         wopcion     :  '2',
                         wemp_pmla   :  $("#wemp_pmla").val()
                     }, function(respuesta){

                         $('#arr_usu').val(JSON.stringify(respuesta));
                     },'json').done(function(){

                         iniciarusuarios();
                     });
              }
          }


          // Llenar el array de docentes
          function iniciarusuarios()
          {

              var arr_usu   = eval('(' + $('#arr_usu').val() + ')');
              var usuarios  = new Array();
              var index     = -1;
              for (var cod_usu in arr_usu)
              {
                  index++;
                  usuarios[index]                = {};
                  usuarios[index].value          = cod_usu;
                  usuarios[index].label          = cod_usu+'-'+arr_usu[cod_usu];
                  usuarios[index].codigo         = cod_usu;
              }

              $("#wcodusu").autocomplete({
              source: usuarios,
              select: function( event, ui ){
                      var cod_sel = ui.item.codigo;
                      $("#wcodusu").attr("codigo",cod_sel);
                      SeleccionUsuario(cod_sel);
              }

              });

          }


          // *****************              Agregar centros de costos a la tabla tbllistacentro      **************************
          function SeleccionCentro(codigocen,nombrecen)
          {

             var wemp_pmla = $("#wemp_pmla").val();
      			 var wcod_usu  = $("#wcodusu").val();
      			 var vencoalu  = '0';
      			 if (codigocen != '')
      			 {
        				 // Seleccionar la lista de Centros de Costos para verificar que no exista
        	 			 $('#tbllistacentro tbody tr').each(function() {
    	                var cadena = $(this.cells[0]).text();

                      if (cadena == codigocen)
    	                   	vencoalu = '1';

        	       });

        	 			 if (vencoalu==0){
          	 			 	 clase = 'fila1';
          	 			 	 codcencos  = nombrecen.split('-');
          	 			 	 vresultado = '<tr class="'+clase+'"><td>'+codcencos[0]+'</td><td>'+codcencos[1]+'</td><td align="center"><input type="checkbox" id="chktodos" name="chktodos" value='+codcencos[0]+' onclick="VerificarCentro(this);"></td></tr>';
          		       $("#tbllistacentro").append(vresultado);
                     $("#wedicion").val('1');
        		     }
        		     else{
        		         alerta('El Centro de Costos ya se encuentra seleccionado');
        		     }
      			 }
    		  }


       // ******** Seleccionar el Usuario según el codigo diligenciado y verificar si tiene usuario en Nomina y clave asignada
		   function SeleccionUsuario(codigodoc)
       {
          	var wemp_pmla   = $("#wemp_pmla").val();
          	var array_cen   = $("#arr_cen2").val();
          	var wnueusuario = '';
          	if (codigodoc != '')
    			  {
        			 	$.post("Nomina_admonusuarios.php",
        				{
          						consultaAjax:   true,
          						accion:         'SeleccionarUsuario',
          						wemp_pmla:      wemp_pmla,
          						codigo_doc:     codigodoc

        				}, function(respuesta){
            				  if (respuesta.length>2)
            				  {
                            var vusuario = respuesta.split(';');
                            $("#wnomusu").val(vusuario[0]);
                            $("#wideusu").val(vusuario[5]);
                            $("#wcentros").val(vusuario[3]);
                            var wcentrosel = vusuario[3];

                            if (vusuario[4].length >= 5)
                               $("#wclave").attr("checked", true);
                            else
                            	 $("#wclave").attr("checked", false);

                            if (vusuario[2] == 'A')
                               $("#wactivo").attr("checked", true);
                            else
                            	 $("#wactivo").attr("checked", false);

                            $("#tbllistacentro").show();
        			              $("#tblagregarcentro").show();
                            $("#conempleados").show();

                            // En caso de que el usuario no tenga acceso en Nomina
                            if (vusuario[6]=='N')
                            {
                            	  jConfirm("El Usuario no existe en Nomina, desea crearlo?","Confirmar", function(resconfir){

                                        if (resconfir == true) {
                	                       	 $("#wnueusuario").val('S');
                	                       	 $("#chkclave").attr("checked", true);
                      								     $("td.activarclave").show();
                	                      }
                                });
                            }

                            //Llenar la tabla de centros de costos asignados al usuario
            							  $.post("Nomina_admonusuarios.php",
            						    {
                								   consultaAjax :  true,
                								   accion       :  'SeleccionarCentros',
                								   wemp_pmla    :  wemp_pmla,
                								   codigo_doc   :  codigodoc,
                								   centrosel    :  wcentrosel,
                								   array_cen    :  array_cen
            							  }, function(respuesta){

                                 $("#tbllistacentro tbody ").remove(0);
                                 $("#conempleados").hide();

                                 if (respuesta.total > 0){
              					   			    $("#tbllistacentro").append(respuesta.resultado);
                                    Consultar(codigodoc);
                                 }

            			          },"json");
            		          }
                      });
            }
        }


        /* Generar el reporte segun filtros seleccionados */
       function Consultar(wcoordinador){

            var stringTr = '';

            // Crear un string con todos los centros de costos
            wcentro='';

            $('#tbllistacentro tbody tr').each(function() {

                vfila1  = $(this.cells[0]).text();
                wcentro = wcentro + vfila1 + ',';
            });

            if (wcentro.length > 0 )
                wcencos = wcentro.substring(0, wcentro.length - 1);

            $.post("Nomina_admonusuarios.php",
              {
                    consultaAjax :  true,
                    accion       :  'ActivarEmpleados',
                    wemp_pmla    :  $("#wemp_pmla").val(),
                    wcentro      :  wcencos,
                    wempleado    :  wcoordinador
              }, function(respuesta){

                  if  (respuesta.error == 0)

                      $("#tblmensajedet").show();

                  else{

                      var fila = "fila1";

                      var cont = 1;

                      var filanom  = "fila1";

                      var centroanterior ='';

                      $('#tbldetalle').empty();

                      if  (respuesta.accesoempleado == 'S'){

                           jQuery.each(respuesta.arrtodos, function(){

                                    if (this.centro_empleado !== centroanterior && centroanterior !== '')

                                        stringTr += "</table></div><br><br>";


                                    if (this.centro_empleado !== centroanterior){

                                        stringTr += "<div class='accordionFiltros' align='center' style='border: 1px' >";

                                        stringTr += "<h1 style='font-size: 11pt;' align='left'>"+ this.centro_empleado +"</h1>";

                                        stringTr += "<table with=100%>";

                                        stringTr +='<thead><tr align="center" class="encabezadoTabla"><td align="center">Seleccionar</td>'
                                                 + '<td align="center">C&oacute;digo</td>'
                                                 + '<td align="center">Nombre empleado</td>'
                                                 + '</tr></thead>';
                                    }


                                    if (this.seleccion_empleado == 'S')
                                          var opcsel = "<input type='checkbox' id='chkseleccion' class='chkseleccion' align='left' onclick='VerificarCoordinador(this);' value='"+this.codigo_empleado+'|'+this.codcen_empleado+"'  checked>";
                                    else
                                          var opcsel = "<input type='checkbox' id='chkseleccion' class='chkseleccion' align='left' onclick='VerificarCoordinador(this);' value='"+this.codigo_empleado+'|'+this.codcen_empleado+"'>  ";

                                    stringTr +=  '<tr class="'+fila+'">'
                                                         + '<td width="100px" align="center">'+opcsel+'</td>'
                                                         + '<td align="center" width="100px">'+this.codigo_empleado+'</td>'
                                                         + '<td width="800px">'+this.nombre_empleado+'</td></tr>';

                                    fila  = fila == "fila1" ? "fila2" : "fila1";

                                    cont++;

                                    centroanterior = this.centro_empleado;


                           });


                           $("#tblmensajedet").hide();

                           $('#tbldetalle').append(stringTr);

                           $("#conempleados").show();

                           // agregar la clase que convierte un div en un formato tipo acordión
                           $(".accordionFiltros").accordion({
                                collapsible: true,
                                heightStyle: "content"
                           });

                      }
                      else
                      {

                         jConfirm("El coordinador tiene Acceso general, desea activarlo por empleado ?","Confirmar", function(resp){

                            if (resp == true)
                            {

                                jQuery.each(respuesta.arrtodos, function(){

                                          if (this.codigo_empleado !== wcoordinador ){

                                              if (this.centro_empleado !== centroanterior && centroanterior !== '')

                                                  stringTr += "</table></div><br><br>";


                                              if (this.centro_empleado !== centroanterior){

                                                  stringTr += "<div class='accordionFiltros' align='center' style='border: 1px' >";

                                                  stringTr += "<h1 style='font-size: 11pt;' align='left'>"+ this.centro_empleado +"</h1>";

                                                  stringTr += "<table>";

                                                  stringTr +='<thead><tr align="center" class="encabezadoTabla"><td align="center">Seleccionar</td>'
                                                           + '<td align="center">C&oacute;digo</td>'
                                                           + '<td align="center">Nombre empleado</td>'
                                                           + '</tr></thead>';
                                              }


                                              var opcsel = "<input type='checkbox' id='chkseleccion' class='chkseleccion' align='left' onclick='VerificarCoordinador(this);' value='"+this.codigo_empleado+'|'+this.codcen_empleado+"'>  ";

                                              stringTr =  stringTr + '<tr class="'+fila+'">'
                                                                   + '<td align="center">'+opcsel+'</td>'
                                                                   + '<td align="center" width="100px">'+this.codigo_empleado+'</td>'
                                                                   + '<td width="500px">'+this.nombre_empleado+'</td></tr>';

                                              fila  = fila == "fila1" ? "fila2" : "fila1";

                                              cont++;

                                              centroanterior = this.centro_empleado;
                                          }

                                });


                                $("#tblmensajedet").hide();

                                $('#tbldetalle').append(stringTr);

                                $("#conempleados").show();

                                // agregar la clase que convierte un div en un formato tipo acordión
                                $(".accordionFiltros").accordion({
                                    collapsible: true,
                                    heightStyle: "content"
                                });

                            }
                        });
                      }

                  }


           },"json");

       }


        function VerificarCoordinador(obj){

          /* Remover y agregar una clase */
          $('#btnGrabar').removeClass('button2');
          $('#btnGrabar').addClass('button');
          $('#btnGrabar').removeAttr('disabled');
          $('#wedicion').val('1');

          var idcampo = $(obj).attr('id');
          var wcoordinador  = $("#wcodusu").val();

          $.post("Nomina_admonusuarios.php",
                {
                    consultaAjax :  true,
                    accion       :  'ConsultarCoordinador',
                    wemp_pmla    :  $("#wemp_pmla").val(),
                    wempleado    :  obj.value,
                    wcoordinador :  wcoordinador
                }, function(resultado){

                   if (resultado == 'S'){
                      jAlert('El empleado se encuentra asignado a otro coordinador');
                      $(idcampo).attr('checked',false);
                   }
                });
        }


        // ********************************  FUNCION Sacar un mensaje de alerta con formato predeterminado  *************
  			function alerta(txt){
  				$("#textoAlerta").text( txt );
  				$.blockUI({ message: $('#msjAlerta') });
  					setTimeout( function(){
  								   $.unblockUI();
  					}, 1800 );
  			}

          // **************************************   Fin Funciones Javascript   ********************************************
	    </script>
      <style type="text/css">

          .button{
            color: #1b2631;
            font-weight: normal;
            font-size: 12,75pt;
            width: 90px; height: 27px;
            background: rgb(199,199,199);
            background: -moz-linear-gradient(top,  rgba(199,199,199,1) 0%, rgba(193,193,193,1) 50%, rgba(184,184,184,1) 51%, rgba(224,224,224,1) 100%);
            background: -webkit-linear-gradient(top,  rgba(199,199,199,1) 0%,rgba(193,193,193,1) 50%,rgba(184,184,184,1) 51%,rgba(224,224,224,1) 100%);
            background: linear-gradient(to bottom,  rgba(199,199,199,1) 0%,rgba(193,193,193,1) 50%,rgba(184,184,184,1) 51%,rgba(224,224,224,1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#c7c7c7', endColorstr='#e0e0e0',GradientType=0 );
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
          }

          .tooltip {
            position: relative;
            display: inline-block;
            opacity: 0.9;
            border-bottom: 1px dotted black;
          }

          .tooltip .tooltiptext {
              visibility: hidden;
              width: 300px;
              background-color: #dedede;
              color: black;
              font-size: 11pt;
              text-align: center;
              border-radius: 6px;
              padding: 5px 0;

              /* Position the tooltip */
              position: absolute;
              z-index: 1;
              top: -10px;
              left: 105%;

          }

          .tooltip:hover .tooltiptext {
              visibility: visible;
          }

          .tooltip:hover .tooltiptext2 {
              visibility: visible;
          }
         </style>
 </head>
	<body>
		<?php
		  echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
		  $wtitulo="ADMINISTRACION DE CLAVE Y USUARIOS PARA REPORTE DE HORAS";
		  encabezado($wtitulo, $wactualiz, 'clinica');
		  $arr_usu  = consultarUsuarios ($wbasedato,$conex,$wemp_pmla);
		  $arr_cen  = consultarCentros  ($wbasetalhuma,$conex,$wemp_pmla);
      $mensaje1 ="<span class='tooltiptext'>-Habilitado: todos los usuarios Matrix <br> -Deshabilitado: Solo Coordinadores</span>";
      $mensaje2 ="<span class='tooltiptext'>-Activo: Tiene usuario Matrix   <br>-Segunda clave: tiene usuario habilitado y clave para el  reporte de horas</span>";
      $mensaje3 ="<span class='tooltiptext'>Modificar la clave que permite ingresar al proceso de reporte de horas</span>";
      $mensaje4 ="<span class='tooltiptext'>Inactivar en el proceso <br> de reporte de horas</span>";
		?>
    <CENTER>
    <table width='800px' style='border: 1px solid blue'>
      <tr class='fila1' style="height:30px;" >
    		<td id='idusuario' class='tooltip'><?=$mensaje1?><b>C&oacute;digo Usuario </b></td><td><input type='checkbox' id='chkcamusu' name='chkcamusu' checked onclick="CambiarUsuarios(this.value)">Todos</td>
    		<td width="15px" class=fila2><input type='text' id='wcodusu' name='wcodusu' size=20  ></td>
        <td class=fila2 ><input type='text' id='wnomusu' name='wnomusu' readonly size=50> </td>
      </tr>
      <tr class='fila1' style="height:30px;">
    		<td id='idactivo' class='tooltip'><?=$mensaje2?><b>Activo en Matrix </b></td><td><input type='checkbox' id='wactivo' name='wactivo' disabled='disabled'></td>
    		<td colspan=2 class=fila2 ><b>Posee Segunda Clave: </b><input type='checkbox' id='wclave' name='wclave' disabled='disabled'></td>
      </tr>
      <tr class='fila1' style="height:30px;">
    		<td id='idclave' class='tooltip'><?=$mensaje3?><b>Cambiar contrase&ntilde;a </b></td><td><input type='checkbox' id='chkclave' name='chkclave' onclick='Activarclave(this.value)'></td>
    		<td colspan=2 style='display:none;' class='activarclave fila2'>Digite Nueva Clave &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='password' id='wclaveusu1' name='wclaveusu1'  size=30 maxlength='10'>
    		<br>Digite nuevamente la Clave <input type='password' id='wclaveusu2' name='wclaveusu2' size=30 maxlength='10'></td>
      </tr>
      <tr class=fila1>
      </tr>
    </table>
    </CENTER>
    <br><br>
    <center>
    <table>
	    <tr>
        <td>&nbsp;&nbsp;<input type='submit' name='Iniciar'    value='Iniciar'    class='button' onclick='Iniciar()'></td>
  	    <td>&nbsp;&nbsp;<input type='submit' name='Actualizar' value='Actualizar' class='button' onclick='Actualizar()'></td>
  	    <td class='tooltip'><?=$mensaje4?>&nbsp;&nbsp;<input type='submit' name='Inactivar' value='Inactivar' class='button' onclick='Inactivar()'></td>
  	    <td>&nbsp;&nbsp;<input type='submit' name='Salir'     value='Salir'     class='button'  onclick='CerrarVentana()'></td>
	    </tr>
	  </table>
      </br></br>
    <table id='tblagregarcentro' name='tblagregarcentro'><tr class=fila2>
  		<td width="402px" align='center'><b>Adicionar Centro de Costos</b></td><td width="400px" align='center'><input type='text' id='wcodcen' name='wcodcen' size='40'></td>
  		</tr>
		</table>
		<table id='tbllistacentro' name='tbllistacentro' class='tbllistacentro' style='border: 1px solid blue;visibility:none;'>
      <thead><tr class=fila1>
      		<td colspan=5 align='center'><b>Lista Centros de Costos asignados al Usuario</b></td>
      		</tr>
      		<tr class=encabezadotabla>
      		<td width="150px" align="center">Codigo</td><td width="550px" align="center">Descripcion</td><td width="100px" align="center">Eliminar</td>
      		</tr>
  		</thead>
  		<tbody>
  		</tbody>
		</table>
		<br></br>
		<table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;display:none;'>
		  <tr><td>No hay usuarios con el codigo ingresado</td></tr>
		</table>
    <center>
    <fieldset id='conempleados' style='display:none;border: 0px;'>
      <br>&nbsp;
      <div width='100%' align=center id="tbldetalle" >
      </div>
      <table id='tblmensajedet' name='tblmensajedet' style='border: 1px solid blue;display:none;'>
          <tr><td class='fila2'>No hay registros para esta consulta</td></tr>
      </table>
    </fieldset>
		<div id='msjAlerta' style='display:none;'>
  		<br><img src='../../images/medical/root/Advertencia.png'/>
  		<br><br><div id='textoAlerta'></div><br><br>
		</div>
		<div style="display:none;" id="img_bus">Actualizando en Matrix.. <img width="13" height="13" border="0" src="../../images/medical/ajax-loader9.gif">
		</div>
		</center>
    <input type="HIDDEN" name="arr_usu"  id="arr_usu" value='<?=json_encode($arr_usu)?>'>
    <input type="HIDDEN" name="arr_cen"  id="arr_cen" value='<?=json_encode($arr_cen)?>'>
    <input type="HIDDEN" name="arr_cen2" id="arr_cen2" value='<?=base64_encode(serialize($arr_cen))?>'>
    <input type="HIDDEN" name="wcentro"  id="wcentro">
    <input type="HIDDEN" name="wideusu"  id="wideusu">
    <input type="HIDDEN" name="wedicion" id="wedicion" value='0'>
    <input type="HIDDEN" name="wnueusuario" id="wnueusuario">
	</body>
	</html>


