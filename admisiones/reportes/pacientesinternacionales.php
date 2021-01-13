<?php
include_once("conex.php");
 /**********************************************************************************************************
 *
 * Programa				: pacientesinternacionales.php
 * Fecha de Creación 	: 2017-01-12
 * Autor				: Arleyda Insignares Ceballos
 * Descripcion			: Reporte Pacientes Internacionales: son aquellos cuya País de residencia es 
 *                        diferente a Colombia.
 *                      
 *                     - Tener en cuenta dos parametros en la tabla root_000051:
 *                    
 *                     1.codigopaisinternacional: Contiene dos códigos, el primer código es del país Colombia,
 *                       para que en el informe de pacientes internacionales se realice el respectivo filtro.
 *                       El segundo es el código del Departamento Antioquia, para que el Reporte 'Nacional' 
 *                       filtre todos los pacientes con Departamento de Residencia distinto a Antioquia.
 *                      
 *                     2.documentosinternacional: parametro para visualizar los tipos de documento de 
 *                       pacientes extranjeros, como por ejemplo, Pasaporte y Cédula de Extranjería. 
 *                       Se utiliza para la opción 'Adicionales' que se refiere a los pacientes que aparecen 
 *                       con Pais de Residencia Colombia pero con Tipo de Documento Extranjero.
 *                      
 *                      
 ***************************************   Modificaciones  *************************************************
 * 2017-05-08  -Arleyda Insignares C. -Se adiciona filtro, rango de fecha según facturación Unix.
 * 
 * 2017-01-25  -Arleyda Insignares C. -Se adiciona informe de Pacientes Nacionales: aquellos cuyo Departamento
 *                                     de residencia es diferente a Antioquia.
 *                                    -En el Reporte internacional se coloca opción para adicionar pacientes 
 *                                     Residentes en Colombia pero con tipo de Documento Pasaporte o Cédula 
 *                                     de Extranjería.
 **********************************************************************************************************/
 
 $wactualiz = "2017-05-18";

 
 $superglobals = array($_FILES,$_SESSION,$_REQUEST);

 foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
 {
    foreach ($valueSuperglobals as $variable => $dato)
    {
        $$variable = $dato; 
    }
 }


 if(!isset($_SESSION['user'])){
	 echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>Error, inicie nuevamente</td></tr>
		</table></center>";
	 return;
 }

 header('Content-type: text/html;charset=ISO-8859-1');

  //***********************************   Inicio  ***********************************************************

  

  include_once("root/comun.php");

  $wbasedato     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
  $wbasemovhos   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

  $wfecha        = date("Y-m-d");
  $whora         = (string)date("H:i:s");
  $pos           = strpos($user,"-");
  $wusuario      = substr($user,$pos+1,strlen($user));

  // ***************************************    FUNCIONES AJAX  Y PHP  **********************************************

   
      if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarDetalle"){

          $wpaises   = unserialize(base64_decode($arr_pais));

          $respuesta = array();

          //Consultar datos Unix
          $conexunix = odbc_connect('facturacion','informix','sco') or die("No se realizo Conexion con el Unix");

          if ($wtipinfo == 'n'){
			  
                // * * * * * * * * * * * * * * REPORTE NACIONAL  * * * * * * * * * * * * * * *

                //Condicion para la consulta
                $condicion  = ' A.Pacdeh != "'.$wcodigode.'" and A.Pacpah = "'.$wcodigopa.'" ';

                // filtrar por servicio en caso de ser seleccionado por el usuario
                if (!is_null($wservicio) && $wservicio != '')
                   $condicion .= ' and H.Ccocod = "'.$wservicio.'"';
                
                //Seleccionar tabla para realizar filtro de fecha
				switch ($wtipo) {
						
						case 'I': // * * * * * * * * * RANGO DE FECHA INGRESO * * * * * * * * * 
						{
									
								$q1 = " CREATE TEMPORARY TABLE T1 (select * From ".$wbasedato."_000101 A
										Where A.Ingfei >= '".$wfechaini."' AND A.Ingfei <= '".$wfechafin."')";

								$res = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());

								$q2  = "SELECT  A.Pacpan, A.Pacpah,A.Pactel,A.Pacdir,A.Id,A.Pacdeh,
												A.Pacap1, A.Pacap2,A.Pactdo,A.Pacdoc,A.Pacsex, A.Pachis,
												concat (A.Pacno1,' ',A.Pacno2) as Nompac,A.Pacfna, D.Ingnin,
												D.Ingfei, D.Ingsei, D.Ingdig, E.Egrfee, E.Egrmei, G.Espnom,
												concat(F.Medno1,' ',F.Medno2,' ',F.Medap1,' ',F.Medap2) as nommedi,
												D.Inghis, H.Cconom, I.Descripcion,U.Descripcion as Nomusuario,   
												R.Descripcion as Nomdepar
										FROM T1 D Inner join ".$wbasedato."_000100 A on A.Pachis = D.Inghis
												Inner join root_000011 I on I.Codigo = D.Ingdig
												Inner join ".$wbasedato."_000108 E on E.Egrhis = A.Pachis and E.Egring = D. Ingnin
												Inner Join ".$wbasemovhos."_000048 F on D.Ingmei = F.Meddoc
												Inner Join ".$wbasemovhos."_000044 G on F.Medesp = G.Espcod
												Inner Join ".$wbasemovhos."_000011 H on H.Ccocod = D.Ingsei
												Inner Join usuarios U on U.Codigo = D.Ingusu 
												Left Join root_000002 R on R.Codigo = A.Pacdeh
										WHERE ".$condicion . " 
										GROUP BY D.Inghis,D.Ingnin,D.Ingsei,A.Pacdoc,D.Ingmei,D.Ingfei,D.Ingdig 
										ORDER BY Nompac";
										
								$res = mysql_query($q2,$conex) or die (mysql_errno()." - en el query: ".$q2." - ".mysql_error());	

								break;								
						}
						case 'E':  // * * * * * * * * * RANGO DE FECHA EGRESO * * * * * * * * * 
						{
                 				
								$q1 = " CREATE TEMPORARY TABLE T1 (select * From ".$wbasedato."_000108 A
										WHERE A.Egrfee >= '".$wfechaini."' AND A.Egrfee <= '".$wfechafin."')";

								$res = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());

								$q2  = "SELECT A.Pacpan, A.Pacpah,A.Pactel,A.Pacdir,A.Id,
												A.Pacap1,A.Pacap2,A.Pactdo,A.Pacdoc,A.Pacsex, A.Pachis,
												concat (A.Pacno1,' ',A.Pacno2) as Nompac,A.Pacfna, D.Ingnin,
												D.Ingfei, D.Ingsei, D.Ingdig, E.Egrfee, E.Egrmei, G.Espnom,
												concat(F.Medno1,' ',F.Medno2,' ',F.Medap1,' ',F.Medap2) as nommedi,
												D.Inghis, H.Cconom, I.Descripcion, U.Descripcion as Nomusuario,
												R.Descripcion as Nomdepar
										FROM T1 E Inner join ".$wbasedato."_000101 D on E.Egrhis = D.Inghis and E.Egring = D. Ingnin
												Inner join ".$wbasedato."_000100 A on A.Pachis = D.Inghis
												Inner join root_000011 I on I.Codigo = D.Ingdig
												Inner Join ".$wbasemovhos."_000048 F on E.Egrmei = F.Meddoc
												Inner Join ".$wbasemovhos."_000044 G on F.Medesp = G.Espcod
												Inner Join ".$wbasemovhos."_000011 H on H.Ccocod = D.Ingsei
												Inner Join usuarios U on U.Codigo = D.Ingusu 
												Left Join root_000002 R on R.Codigo = A.Pacdeh
										WHERE ".$condicion. " 
										GROUP BY E.Egrhis,E.Egring,D.Ingsei,A.Pacdoc,E.Egrmei,D.Ingfei,D.Ingdig 
										ORDER BY Nompac";   
										
								$res = mysql_query($q2,$conex) or die (mysql_errno()." - en el query: ".$q2." - ".mysql_error());
								
								break;																
						}						
						case 'F': // CONSULTAR DATOS UNIX
						{
							   							   
							   $campos        = "F.movemp, F.movcer, F.movres, F.movhis, F.movnum, F.movfec, 
							                     C.cardoc, C.carval, D.encusu, E.idenom, E.ideap1";
							   $campos_nulos  = "ideap2";
							   $defectoCampos = "''";
							   $tablas        = "famov F, cacar C, caenc D, siide E, inpaci P";
							   $where         = "F.movfec >= '".$wfechaini."' 
												 and F.movfec <= '".$wfechafin."'
												 and F.movfuo = '01'
												 and F.movfue in ('20','22')
												 and F.movhis = P.pachis												 
												 and P.pacmun not matches '05*'
												 and P.pacmun not matches '01*'
												 and F.movfue = C.carfue
												 and F.movdoc = C.cardoc
												 and C.carfue = D.encfue
												 and C.cardoc = D.encdoc
												 and E.idecod = D.encusu 												 
												 and F.movanu = '0' ";											 

							   $qUnix         =  construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );			   
			   
							   $resodbc = odbc_do($conexunix,$qUnix);
							   
						}

             } // Fin switch
		  }    // Fin Paciente nacional
          else{ 
                // * * * * * * * * * * * * * * REPORTE INTERNACIONAL * * * * * * * * * * * * * * *
				
                // Condicion para la consulta con parametro de la tabla root_000051
                // wadicion : Adicionar pacientes que tengan tipo de documento 'Cedula de Extranjería o Pasaporte'
                
                $exp         = explode(",",strtoupper($wdocumenrep));
                $wdocumenrep = implode("','",$exp);
                
				if ($wadicion == 'on'){
                   $condicion  = "(A.Pacpah != '{$wcodigopa}' or A.Pactdo in ('{$wdocumenrep}'))";
                }
                else{
                   $condicion  = 'A.Pacpah != "'.$wcodigopa.'"';
                }

                // filtrar por servicio en caso de ser seleccionado por el usuario
                if (!is_null($wservicio) && $wservicio != '')
                   $condicion .= ' and H.Ccocod = "'.$wservicio.'"';
                

                // filtrar por pais en caso de ser seleccionado por el usuario
                if (!is_null($wpais) && $wpais != '')
                   $condicion .= ' and A.Pacpah = "'.$wpais.'"';
                
                //Seleccionar tabla para realizar filtro de fecha
				switch ($wtipo) {
						
						case 'I': // * * * * * * * * * RANGO DE FECHA INGRESO * * * * * * * * * 
						{

							  $q1  = " CREATE TEMPORARY TABLE T1 (select * From ".$wbasedato."_000101 A
									  Where A.Ingfei >= '".$wfechaini."' AND A.Ingfei <= '".$wfechafin."')";

							  $res = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());

							  $q2  = "SELECT  A.Pacpan, A.Pacpah,A.Pactel,A.Pacdir,A.Id,
											  A.Pacap1, A.Pacap2,A.Pactdo,A.Pacdoc,A.Pacsex, A.Pachis,
											  concat (A.Pacno1,' ',A.Pacno2) as Nompac,A.Pacfna, D.Ingnin,
											  D.Ingfei, D.Ingsei, D.Ingdig, E.Egrfee, E.Egrmei, G.Espnom,
											  concat(F.Medno1,' ',F.Medno2,' ',F.Medap1,' ',F.Medap2) as nommedi,
											  D.Inghis, H.Cconom, I.Descripcion,U.Descripcion as Nomusuario,
											  R.Descripcion as Nomdepar
									  FROM T1 D Inner join ".$wbasedato."_000100 A on A.Pachis = D.Inghis
											  Inner join root_000011 I on I.Codigo = D.Ingdig
											  Inner join ".$wbasedato."_000108 E on E.Egrhis = A.Pachis and E.Egring = D.Ingnin
											  Inner Join ".$wbasemovhos."_000048 F on D.Ingmei = F.Meddoc
											  Inner Join ".$wbasemovhos."_000044 G on F.Medesp = G.Espcod
											  Inner Join ".$wbasemovhos."_000011 H on H.Ccocod = D.Ingsei
											  Inner Join usuarios U on U.Codigo = D.Ingusu 
											  Left  Join root_000002 R on R.Codigo = A.Pacdeh
									  WHERE ".$condicion. " 
									  GROUP BY D.Inghis,D.Ingnin,D.Ingsei,A.Pacdoc,D.Ingmei,D.Ingfei,D.Ingdig 
									  ORDER BY Nompac";     
									  
							  $res = mysql_query($q2,$conex) or die (mysql_errno()." - en el query: ".$q2." - ".mysql_error());		  
							  
							  break;
							  
						}
						case 'E':  // * * * * * * * * * RANGO DE FECHA EGRESO * * * * * * * * * 
						{

								$q1 = " CREATE TEMPORARY TABLE T1 (select * From ".$wbasedato."_000108 A
										WHERE A.Egrfee >= '".$wfechaini."' AND A.Egrfee <= '".$wfechafin."')";

								$res = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q1." - ".mysql_error());

								$q2  = "SELECT  A.Pacpan, A.Pacpah,A.Pactel,A.Pacdir,A.Id,
												A.Pacap1,A.Pacap2,A.Pactdo,A.Pacdoc,A.Pacsex, A.Pachis,
												concat (A.Pacno1,' ',A.Pacno2) as Nompac,A.Pacfna, D.Ingnin,
												D.Ingfei, D.Ingsei, D.Ingdig, E.Egrfee, E.Egrmei, G.Espnom,
												concat(F.Medno1,' ',F.Medno2,' ',F.Medap1,' ',F.Medap2) as nommedi,
												D.Inghis, H.Cconom, I.Descripcion,U.Descripcion as Nomusuario,
												R.Descripcion as Nomdepar
										FROM T1 E Inner join ".$wbasedato."_000101 D on E.Egrhis = D.Inghis and E.Egring = D.Ingnin
												Inner join ".$wbasedato."_000100 A on A.Pachis = D.Inghis
												Inner join root_000011 I on I.Codigo = D.Ingdig
												Inner Join ".$wbasemovhos."_000048 F on E.Egrmei = F.Meddoc
												Inner Join ".$wbasemovhos."_000044 G on F.Medesp = G.Espcod
												Inner Join ".$wbasemovhos."_000011 H on H.Ccocod = D.Ingsei
												Inner Join usuarios U on U.Codigo = D.Ingusu 
												Left Join root_000002 R on R.Codigo = A.Pacdeh
										WHERE ".$condicion. " 
										GROUP BY E.Egrhis,E.Egring,D.Ingsei,A.Pacdoc,E.Egrmei,D.Ingfei,D.Ingdig 
										ORDER BY Nompac";
										
								$res = mysql_query($q2,$conex) or die (mysql_errno()." - en el query: ".$q2." - ".mysql_error());
								
								break;
								
						}
						case 'F':
						{
							
							   // Consultar Datos Unix.
							   $campos        = " F.movemp, F.movcer, F.movres, F.movhis, F.movnum, F.movfec, 
							                      C.cardoc, C.carval, D.encusu, E.idenom, E.ideap1";
							   $campos_nulos  = " ideap2";
							   $defectoCampos = "''";
							   $tablas        = " famov F, cacar C, caenc D, siide E, inpaci P ";
							   $where         = " F.movfec >= '".$wfechaini."' 
												  and F.movfec <= '".$wfechafin."'
												  and F.movfuo = '01'
												  and F.movfue in ('20','22')	
												  and F.movhis = P.pachis	
												  and (P.pacmun matches '01*' or (P.pacmun not matches '01*' and P.pactid in ('{$wdocumenrep}') ))
												  and F.movfue = C.carfue
												  and F.movdoc = C.cardoc
												  and C.carfue = D.encfue
												  and C.cardoc = D.encdoc												  
												  and E.idecod = D.encusu 												  
												  and F.movanu = '0' ";								

							   $qUnix         =  construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );
	   
							   $resodbc   = odbc_do($conexunix,$qUnix);
						}
						
                } // Fin switch

          } // Fin pacientes internacionales


          // * * * * * * * * * * * * *  CONSTRUCCION TABLA DETALLE CONSULTA  * * * * * * * * * * * * *
          
          $cont1     = 0;
          $valtotal  = 0;
          $titulo = "<thead><tr class='encabezadoTabla'>
	                      <td align='center'> NOMBRE LUGAR DE NACIMIENTO </td>
	                      <td align='center'> NOMBRE PAIS DE RESIDENCIA  </td>
	                      <td align='center'> NOMBRE DEPARTAMENTO RESIDENCIA </td>
	                      <td align='center'> TELEFONO            </td>
	                      <td align='center'> DIRECCION           </td>
	                      <td align='center'> PRIMER APELLIDO     </td>
	                      <td align='center'> SEGUNDO APELLIDO    </td>
	                      <td align='center'> NOMBRE              </td>
	                      <td align='center'> EDAD                </td>
	                      <td align='center'> ID                  </td>
	                      <td align='center'> NUMERO ID           </td>
	                      <td align='center'> SEXO                </td>
	                      <td align='center'> HISTORIA CLINICA    </td>
	                      <td align='center'> NUMERO DE INGRESO   </td>
	                      <td align='center'> USUARIO QUE INGRESO </td>
	                      <td align='center'> FECHA INGRESO       </td>
	                      <td align='center'> FECHA DE EGRESO     </td>
	                      <td align='center'> FECHA DE FACTURACION</td>
	                      <td align='center'> SERVICIO DE INGRESO </td>
	                      <td align='center'> CODIGO DX           </td>
	                      <td align='center'> DIAGNOSTICO         </td>
	                      <td align='center'> ESPECIALIDAD        </td>
	                      <td align='center'> CODIGO MEDICO       </td>
	                      <td align='center'> NOMBRE MEDICO       </td>
	                      <td align='center'> FACTURA             </td>
	                      <td align='center'> EMPRESA             </td>
	                      <td align='center'> NIT RESPONSABLE     </td>
	                      <td align='center'> NOMBRE RESPONSABLE  </td>
	                      <td align='center'> VALOR FACTURADO     </td>
	                      <td align='center'> USUARIO QUE FACTURA </td>
	                      <td align='center'> NOMBRE FUNCIONARIO  </td>
	                      <td align='center'> PRIMER APELLIDO     </td>
	                      <td align='center'> SEGUNDO APELLIDO    </td>
                    </tr></thead>";

          $titulo1 = "<thead><tr>
	                      <th align='center'>Pais_de_Nacimiento</th>
	                      <th align='center'>Pais_de_Residencia</th>
	                      <th align='center'>Departamento_de_Residencia</th>
	                      <th align='center'>Telefono</th>
	                      <th align='center'>Direccion</th>
	                      <th align='center'>Primer_Apellido</th>
	                      <th align='center'>Segundo_Apellido</th>
	                      <th align='center'>Nombre</th>
	                      <th align='center'>Edad</th>
	                      <th align='center'>Id</th>
	                      <th align='center'>Numero_Id</th>
	                      <th align='center'>Sexo</th>
	                      <th align='center'>Historia_Clinica</th>
	                      <th align='center'>Numero_de_Ingreso</th>
	                      <th align='center'>Usuario_que_ingreso</th>
	                      <th align='center'>Fecha_de_Ingreso</th>
	                      <th align='center'>Fecha_de_Egreso</th>
	                      <th align='center'>Fecha_de_Facturacion</th>
	                      <th align='center'>Servicio_de_Ingreso</th>
	                      <th align='center'>Codigo_dx</th>
	                      <th align='center'>Diagnostico</th>
	                      <th align='center'>Especialidad</th>
	                      <th align='center'>Codigo_Medico</th>
	                      <th align='center'>Nombre_Medico</th>
	                      <th align='center'>Factura</th>
	                      <th align='center'>Empresa</th>
	                      <th align='center'>Nit_Responsable</th>
	                      <th align='center'>Nombre_Responsable</th>
	                      <th align='center'>Valor_Facturado</th>
	                      <th align='center'>Usuario_que_factura</th>
	                      <th align='center'>Nombre_Funcionario</th>
	                      <th align='center'>Primer_Apellido</th>
	                      <th align='center'>Segundo_Apellido</th>
                      </tr></thead>";
          $data_excel .= '<tbody>';

          
          if  ($wtipo == 'F'){ // ´* * * INGRESO POR FACTURACION UNIX

	               // CREAR LA TABLA TEMPORAL UNIX
	          	   $maketemp = "
						    CREATE TEMPORARY TABLE TEMP_UNIX (
							      movemp VARCHAR(50),
							      movcer VARCHAR(50),
							      movres VARCHAR(200),
							      movhis VARCHAR(20),
							      movnum VARCHAR(10),
							      movfec VARCHAR(10),
							      cardoc VARCHAR(10),
							      carval DECIMAL(12,2),
							      encusu VARCHAR(15),
							      idenom VARCHAR(50),
							      ideap1 VARCHAR(50),
							      ideap2 VARCHAR(50),
							      INDEX hisnum_key (movhis, movnum)
						    )
						   "; 

  			   	   mysql_query($maketemp, $conex) or die (mysql_errno()." - en el query: ".$maketemp." - ".mysql_error());	

				   $inserttemp = "
						    INSERT INTO TEMP_UNIX
							      (movemp,movcer,movres,movhis,movnum,movfec,cardoc,carval,encusu,idenom,ideap1,ideap2) values 
							";   
		  	      
			  	    // recorrer tabla unix para grabar en tabla temporal   
	                while( odbc_fetch_row($resodbc) ){
	                     
	                     $vmovemp = utf8_encode(trim(odbc_result($resodbc,'movemp')));
	                     $vmovcer = utf8_encode(trim(odbc_result($resodbc,'movcer')));
	                     $vmovres = utf8_encode(trim(odbc_result($resodbc,'movres')));
	                     $vmovhis = utf8_encode(trim(odbc_result($resodbc,'movhis')));
	                     $vmovnum = utf8_encode(trim(odbc_result($resodbc,'movnum')));
	                     $vmovfec = utf8_encode(trim(odbc_result($resodbc,'movfec')));
	                     $vcardoc = utf8_encode(trim(odbc_result($resodbc,'cardoc')));
	                     $vcarval = utf8_encode(trim(odbc_result($resodbc,'carval')));
	                     $vencusu = utf8_encode(trim(odbc_result($resodbc,'encusu')));
	                     $videnom = utf8_encode(trim(odbc_result($resodbc,'idenom')));
	                     $videap1 = utf8_encode(trim(odbc_result($resodbc,'ideap1')));
	                     $videap2 = utf8_encode(trim(odbc_result($resodbc,'ideap2')));

	               	     $inserttemp .= "('".$vmovemp ."','". $vmovcer ."','". $vmovres ."','". $vmovhis ."','". $vmovnum ."','". $vmovfec ."','". $vcardoc ."',". $vcarval .",'". $vencusu ."','". $videnom ."','". $videap1 ."','". $videap2 ."'),";  
					   				   
				    }

					$inserttemp = substr ($inserttemp, 0, strlen($inserttemp) - 1);

				    $resmat = mysql_query($inserttemp,$conex) or die (mysql_errno()." - en el query: ".$inserttemp." - ".mysql_error());

			   		$q2  =  "SELECT   X.movemp, X.movcer, X.movres, X.movhis, X.movnum, X.movfec, 
						              X.cardoc, X.carval, X.encusu, X.idenom, X.ideap1, X.ideap2,
						              A.Pacpah, A.Pactel, A.Pacdir, A.Id,A.Pacap1,A.Pacap2,
									  A.Pacpan, A.Pactdo, A.Pacdoc, A.Pacsex, A.Pachis,A.Pacfna,
									  concat (A.Pacno1,' ',A.Pacno2) as Nompac, D.Ingnin,D.Ingfei, 
									  D.Ingsei, D.Ingdig, D.Inghis, D.Ingmei, H.Cconom, 								  
									  U.Descripcion as Nomusuario, R.Descripcion as Nomdepar
							 FROM     TEMP_UNIX X
						              Inner join ".$wbasedato."_000101 D on D.Inghis = X.movhis and D.Ingnin = X.movnum
									  Inner join ".$wbasedato."_000100 A on A.Pachis = D.Inghis
									  Inner Join ".$wbasemovhos."_000011 H on H.Ccocod = D.Ingsei
									  Inner Join usuarios U on U.Codigo = D.Ingusu 
									  Left Join root_000002 R on R.Codigo = A.Pacdeh
							 WHERE ".$condicion."
							 GROUP BY D.Inghis,D.Ingnin,D.Ingsei,A.Pacdoc,D.Ingmei,D.Ingfei,D.Ingdig 
							 ORDER BY Nompac";	
										  
				    $resmat = mysql_query($q2,$conex) or die (mysql_errno()." - en el query: ".$q2." - ".mysql_error());	            	   

					while($row = mysql_fetch_assoc($resmat))
				    {				     	 

                          //Consultar datos del egreso
                          $qEgreso = "SELECT Egrfee, Egrmei
 								      FROM ".$wbasedato."_000108 
 								      WHERE Egrhis='".$row["Inghis"]."' 
 								        AND Egring='".$row["Ingnin"]."'";


  						  $resEgreso = mysql_query($qEgreso,$conex) or die (mysql_errno()." - en el query: ".$qEgreso." - ".mysql_error());

  						  $rowEgreso = mysql_fetch_assoc($resEgreso);


  						  //Consultar diagnóstico		  
  						  $qDiagnos = "SELECT Descripcion
 								       FROM root_000011 
 								       WHERE Codigo='".$row["Ingdig"]."'";


  						  $resDiagnos = mysql_query($qDiagnos,$conex) or die (mysql_errno()." - en el query: ".$qDiagnos." - ".mysql_error());

  						  $rowDiagnos = mysql_fetch_assoc($resDiagnos);


  						  //Consultar Nombre médico  						  
						  $qMedico = "SELECT concat(F.Medno1,' ',F.Medno2,' ',F.Medap1,' ',F.Medap2) as nommedi,
						                     G.Espnom
 								       FROM ".$wbasemovhos."_000048 F
 								       Inner Join ".$wbasemovhos."_000044 G on F.Medesp = G.Espcod
 								       WHERE F.Meddoc='".$row["Ingmei"]."'";

  						  $resMedico = mysql_query($qMedico,$conex) or die (mysql_errno()." - en el query: ".$qMedico." - ".mysql_error());

  						  $rowMedico = mysql_fetch_assoc($resMedico);  



				     	  $vedad = edad($row["Pacfna"]);
					      $Paisn = (array_key_exists($row['Pacpan'],$wpaises)) ? $wpaises[$row['Pacpan']]:'vacio';
					      $Paisr = (array_key_exists($row['Pacpah'],$wpaises)) ? $wpaises[$row['Pacpah']]:'vacio';

					      $cont1 % 2 == 0 ? $fondo = "fila1" : $fondo = "fila2";
						  $cont1 ++;
						  $Colum = 1;

						  $data .= "<tr class='".$fondo."' onclick='ilumina(this,\"".$fondo."\")'>";
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($Paisn).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($Paisr).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Nomdepar"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Pactel"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Pacdir"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Pacap1"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Pacap2"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Nompac"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$vedad.'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Pactdo"].'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Pacdoc"].'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Pacsex"].'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Inghis"].'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Ingnin"].'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Nomusuario"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Ingfei"].'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$rowEgreso["Egrfee"].'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["movfec"].'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Cconom"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Ingdig"].'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($rowDiagnos["Descripcion"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($rowMedico["Espnom"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$rowEgreso["Egrmei"].'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($rowMedico["nommedi"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["cardoc"].'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["movemp"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["movcer"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["movres"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["carval"].'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["encusu"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["idenom"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["ideap1"]).'</td>';
						  $Colum ++;
						  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["ideap2"]).'</td>';
						  $Colum ++;
						  $data .= '<td style="display:none;">'.$row["Id"].'</td>';
						  $Colum ++;
						  $data .= '</tr>';

					   
	  					  $data_excel .= '<tr>
											   <td >'.utf8_encode($Paisn).'</td>
											   <td >'.utf8_encode($Paisr).'</td>
											   <td >'.utf8_encode($row["Nomdepar"]).'</td>
											   <td >'.utf8_encode($row["Pactel"]).'</td>
											   <td >'.utf8_encode(codificar($row["Pacdir"])).'</td>
											   <td >'.utf8_encode($row["Pacap1"]).'</td>
											   <td >'.utf8_encode($row["Pacap2"]).'</td>
											   <td >'.utf8_encode($row["Nompac"]).'</td>
											   <td >'.$vedad.'</td>
											   <td >'.$row["Pactdo"].'</td>
											   <td >'.$row["Pacdoc"].'</td>
											   <td >'.$row["Pacsex"].'</td>
											   <td >'.$row["Inghis"].'</td>
											   <td >'.$row["Ingnin"].'</td>
											   <td >'.utf8_encode($row["Nomusuario"]).'</td>
											   <td >'.$row["Ingfei"].'</td>
											   <td >'.$rowEgreso["Egrfee"].'</td>
											   <td >'.$row["movfec"].'</td>
											   <td >'.utf8_encode($row["Cconom"]).'</td>
											   <td >'.$row["Ingdig"].'</td>
											   <td >'.utf8_encode($rowDiagnos["Descripcion"]).'</td>
											   <td >'.utf8_encode($rowMedico["Espnom"]).'</td>
											   <td >'.$rowEgreso["Egrmei"].'</td>
											   <td >'.utf8_encode($rowMedico["nommedi"]).'</td>
											   <td >'.utf8_encode($row["cardoc"]).'</td>
											   <td >'.utf8_encode($row["movemp"]).'</td>
											   <td >'.utf8_encode($row["movcer"]).'</td>
											   <td >'.utf8_encode($row["movres"]).'</td>
											   <td >'.round($row["carval"]).'</td>
											   <td >'.utf8_encode($row["encusu"]).'</td>
											   <td >'.utf8_encode($row["idenom"]).'</td>
											   <td >'.utf8_encode($row["ideap1"]).'</td>
											   <td >'.utf8_encode($row["ideap2"]).'</td>
										  </tr>';

						  $valtotal = $valtotal + $row["carval"];

					  }
          }
		  else{ // Consultar datos desde matrix
		  
				while($row = mysql_fetch_assoc($res))
				{
					// Consultar Datos Unix.
					$campos        = " F.movemp, F.movcer, F.movres, F.movfec,C.cardoc,
					                   C.carval, D.encusu, E.idenom, E.ideap1 ";
					$campos_nulos  = " ideap2";
					$defectoCampos = " ''";
					$tablas        = " famov F, cacar C, caenc D, siide E ";
					$where         = " F.movhis = '".$row["Inghis"]."'
									   and F.movnum = '".$row["Ingnin"]."'
									   and F.movfuo = '01'
									   and F.movfue in ('20','22')
									   and F.movfue = C.carfue
									   and F.movdoc = C.cardoc
									   and C.carfue = D.encfue
									   and C.cardoc = D.encdoc
									   and E.idecod = D.encusu 
									   and F.movanu = '0' ";

					$qUnix         = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );

					$resodbc = odbc_do($conexunix,$qUnix);
					$totodbc = odbc_num_rows($resodbc);

					$vedad = edad($row["Pacfna"]);
					$Paisn = (array_key_exists($row['Pacpan'],$wpaises)) ? $wpaises[$row['Pacpan']]:'vacio';
					$Paisr = (array_key_exists($row['Pacpah'],$wpaises)) ? $wpaises[$row['Pacpah']]:'vacio';

					while( odbc_fetch_row($resodbc) ){

							  $cont1 % 2 == 0 ? $fondo = "fila1" : $fondo = "fila2";
							  $cont1 ++;
							  $Colum = 1;

							  $data .= "<tr class='".$fondo."' onclick='ilumina(this,\"".$fondo."\")'>";
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($Paisn).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($Paisr).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Nomdepar"]).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.
								utf8_encode($row["Pactel"]).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Pacdir"]).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Pacap1"]).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Pacap2"]).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Nompac"]).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$vedad.'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Pactdo"].'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Pacdoc"].'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Pacsex"].'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Inghis"].'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Ingnin"].'</td>';
							  $Colum ++;							  
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Nomusuario"]).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Ingfei"].'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Egrfee"].'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.trim(odbc_result($resodbc,'movfec')).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Cconom"]).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Ingdig"].'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Descripcion"]).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["Espnom"]).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.$row["Egrmei"].'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode($row["nommedi"]).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode(trim(odbc_result($resodbc,'cardoc'))).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode(trim(odbc_result($resodbc,'movemp'))).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode(trim(odbc_result($resodbc,'movcer'))).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode(trim(odbc_result($resodbc,'movres'))).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.number_format(trim(odbc_result($resodbc,'carval'))).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode(trim(odbc_result($resodbc,'encusu'))).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode(trim(odbc_result($resodbc,'idenom'))).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode(trim(odbc_result($resodbc,'ideap1'))).'</td>';
							  $Colum ++;
							  $data .= '<td align="center" class="col'.$Colum.'" Onclick ="iluminacolumna(this,\'col'.$Colum.'\');" nowrap >'.utf8_encode(trim(odbc_result($resodbc,'ideap2'))).'</td>';
							  $Colum ++;
							  $data .= '<td style="display:none;">'.$row["Id"].'</td>';
							  $Colum ++;
							  $data .= '</tr>';



							  $data_excel .= '<tr>
										   <td >'.utf8_encode($Paisn).'</td>
										   <td >'.utf8_encode($Paisr).'</td>
										   <td >'.utf8_encode($row["Nomdepar"]).'</td>
										   <td >'.utf8_encode($row["Pactel"]).'</td>
										   <td >'.utf8_encode(codificar($row["Pacdir"])).'</td>
										   <td >'.utf8_encode($row["Pacap1"]).'</td>
										   <td >'.utf8_encode($row["Pacap2"]).'</td>
										   <td >'.utf8_encode($row["Nompac"]).'</td>
										   <td >'.$vedad.'</td>
										   <td >'.$row["Pactdo"].'</td>
										   <td >'.$row["Pacdoc"].'</td>
										   <td >'.$row["Pacsex"].'</td>
										   <td >'.$row["Inghis"].'</td>
										   <td >'.$row["Ingnin"].'</td>
										   <td >'.utf8_encode($row["Nomusuario"]).'</td>
										   <td >'.$row["Ingfei"].'</td>
										   <td >'.$row["Egrfee"].'</td>
										   <td >'.trim(odbc_result($resodbc,'movfec')).'</td>
										   <td >'.utf8_encode($row["Cconom"]).'</td>
										   <td >'.$row["Ingdig"].'</td>
										   <td >'.utf8_encode($row["Descripcion"]).'</td>
										   <td >'.utf8_encode($row["Espnom"]).'</td>
										   <td >'.$row["Egrmei"].'</td>
										   <td >'.utf8_encode($row["nommedi"]).'</td>
										   <td >'.utf8_encode(trim(odbc_result($resodbc,'cardoc'))).'</td>
										   <td >'.utf8_encode(trim(odbc_result($resodbc,'movemp'))).'</td>
										   <td >'.utf8_encode(trim(odbc_result($resodbc,'movcer'))).'</td>
										   <td >'.utf8_encode(trim(odbc_result($resodbc,'movres'))).'</td>
										   <td >'.round(trim(odbc_result($resodbc,'carval'))).'</td>
										   <td >'.utf8_encode(trim(odbc_result($resodbc,'encusu'))).'</td>
										   <td >'.utf8_encode(trim(odbc_result($resodbc,'idenom'))).'</td>
										   <td >'.utf8_encode(trim(odbc_result($resodbc,'ideap1'))).'</td>
										   <td >'.utf8_encode(trim(odbc_result($resodbc,'ideap2'))).'</td>
									  </tr>';

									  $valtotal = $valtotal + odbc_result($resodbc,'carval');
					}// fin while


            } // Fin while datos Matrix

          } // fin else

          $conten =$data_excel;

          $data .= '<tr class=encabezadotabla><td colspan=28 align="left">TOTAL PACIENTES:  '.$cont1.'</td>
                    <td>'.number_format($valtotal).'</td><td colspan=4 align="left"></td></tr>';
          
          $data_excel .= '</tbody>';
          
          if ($cont1>0){

              $respuesta['titulo1'] = $titulo.$data;
              $respuesta['titulo2'] = $titulo1.$data_excel;
          }
          else{
              $respuesta['titulo1'] = 'N';
              $respuesta['titulo2'] = 'N';
          }

          //var_dump($respuesta);
          echo json_encode($respuesta);
          return;
      }
	  

      function edad($fecha){

          $fecha = str_replace("/","-",$fecha);
          $fecha = date('Y/m/d',strtotime($fecha));
          $hoy = date('Y/m/d');
          $edad = $hoy - $fecha;
          return $edad;

      }
	  

      function codificar($concepto_dec){

        return str_replace("#","nro",$concepto_dec);
      }


      function decodificar($concepto_cod){

        return str_replace("__",".",$concepto_cod);
      }


      //Constructor de Queries UNIX no se pueden mas de 9 campos para verificar si son nulos o no
      function construirQueryUnix( $tablas, $campos_nulos, $campos_todos='', $condicionesWhere='',$defecto_campos_nulos='')
      {

          $condicionesWhere = trim($condicionesWhere);

          if( $campos_nulos == NULL || $campos_nulos == "" ){
              $campos_nulos = array("");
          }

          if( $tablas == "" ){ //Debe existir al menos una tabla
              return false;
          }

          if(gettype($tablas) == "array"){
              $tablas = implode(",",$tablas);
          }

          $pos = strpos($tablas, ",");
          if( $pos !== false && $condicionesWhere == ""){ //Si hay mas de una tabla, debe mandar condicioneswhere
              return false;
          }

          //Si recibe un string, convertirlo a un array
          if( gettype($campos_nulos) == "string" )
              $campos_nulos = explode(",",$campos_nulos);

          $campos_todos_arr = array();

          //Por cual string se reemplazan los campos nulos en el query
          if( $defecto_campos_nulos == "" ){
              $defecto_campos_nulos = array();
              foreach( $campos_nulos as $posxy=>$valorxy ){
                  array_push($defecto_campos_nulos, "''");
              }
          }else{
              if(gettype($defecto_campos_nulos) == "string"){
                  $defecto_campos_nulos = explode(",",$defecto_campos_nulos);
              }
              if(  count( $defecto_campos_nulos ) == 1 ){ //Significa que todos los campos nulos van a ser reemplazados con el mismo valor
                  $defecto_campos_nulos_aux = array();
                  foreach( $campos_nulos as $posxyc=>$valorxyc ){
                      array_push($defecto_campos_nulos_aux, $defecto_campos_nulos[0]);
                  }
                  $defecto_campos_nulos = $defecto_campos_nulos_aux;
              }else if(  count( $defecto_campos_nulos ) != count( $campos_nulos ) ){
                  return false;
              }
          }

          if( gettype($campos_todos) == "string" ){
              $campos_todos_arr = explode(",",trim($campos_todos));
          }else if(gettype($campos_todos) == "array"){
              $campos_todos_arr = $campos_todos;
              $campos_todos = implode(",",$campos_todos);
          }
          foreach( $campos_todos_arr as $pos22=>$valor ){ //quitar espacios a cada valor
              $campos_todos_arr[$pos22] = trim($valor);
          }
          foreach( $campos_nulos as $pos221=>$valor1 ){ //quitar espacios a cada valor
              $campos_nulos[$pos221] = trim($valor1);

              //Si el campo nulo no existe en el arreglo de todos los campos, agregarlo al final
              $clavex = array_search(trim($valor1), $campos_todos_arr);
              if( $clavex === false ){
                  array_push($campos_todos_arr,trim($valor1));
              }
          }
          //Quitar la palabra and, si las condiciones empiezan asi.
          if( substr($condicionesWhere, 0, 3)  == "AND" || substr($condicionesWhere, 0, 3) == "and" ){
              $condicionesWhere = substr($condicionesWhere, 3);
          }
          $condicionesWhere = str_replace("WHERE", "", $condicionesWhere); //Que no tenga la palabra WHERE
          $condicionesWhere = str_replace("where", "", $condicionesWhere); //Que no tenga la palabra WHERE

          $query = "";

          $bits = count( $campos_nulos );
          if( $bits >= 10 ){ //No pueden haber más de 10 campos nulos
              return false;
          }

          if( $bits == 1 && $campos_nulos[0] == "" ){ //retornar el query normal
              $query = "SELECT ".$campos_todos ." FROM ".$tablas;
              if( $condicionesWhere != "" )
                  $query.= " WHERE ".$condicionesWhere;
              return $query;
          }

          $max = (1 << $bits);
          $fila_bits = array();
          for ($i = 0; $i < $max; $i++){
              /*-->decbin Entrega el valor binario del decimal $i,
                -->str_pad Rellena el string hasta una longitud $bits con el caracter 0 por la izquierda:
                 EJEMPLO $input = "Alien" str_pad($input, 10, "-=", STR_PAD_LEFT);  // produce "-=-=-Alien", rellena por la izquierda hasta juntar 10 caracteres
                -->str_split Convierte un string (el entregado por str_pad) en un array, asi tengo el arreglo con el codigo binario generado
              */
              $campos_todos_arr_copia = array();
              $campos_todos_arr_copia = $campos_todos_arr;

              $fila_bits = str_split( str_pad(decbin($i), $bits, '0', STR_PAD_LEFT) );
              $select = "SELECT ";
              $where = " WHERE ";
              if( $condicionesWhere != "" )
                  $where.= $condicionesWhere." AND ";

              for($pos = 0; $pos < count($fila_bits); $pos++ ){
                  if($pos!=0) $where.= " AND ";
                  if( $fila_bits[$pos] == 0 ){
                      $clave = array_search($campos_nulos[$pos], $campos_todos_arr_copia);
                      //if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = "'.' as ".$campos_nulos[$pos];
                      if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = $defecto_campos_nulos[$pos]." as ".$campos_nulos[$pos];
                      $where.= $campos_nulos[$pos]." IS NULL ";
                  }else{
                      $where.= $campos_nulos[$pos]." IS NOT NULL ";
                  }
              }

              $select.= implode(",",$campos_todos_arr_copia);
              $query.= $select." FROM ".$tablas.$where;
              if( ($i+1) < $max ) $query.= " UNION ";
          }
          return $query;
      }

      function timequery(){
		   static $querytime_begin;
		   list($usec, $sec) = explode(' ',microtime());
		    
		       if(!isset($querytime_begin))
		      {   
		         $querytime_begin= ((float)$usec + (float)$sec);
		      }
		      else
		      {
		         $querytime = (((float)$usec + (float)$sec)) - $querytime_begin;
		         echo sprintf('<br />La consulta tardó %01.5f segundos.- <br />', $querytime);
		      }
	  }


      function consultarPaises($wbasedato,$conex,$wemp_pmla){

        $strtipvar = array();
        $q  = " SELECT Paicod, Painom"
             ."  From root_000077 ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        while($row = mysql_fetch_assoc($res))
             {
               $strtipvar[$row['Paicod']] = $row['Painom'];
             }
        return $strtipvar;
     }


     function consultarServicios($wbasedato,$wbasemovhos,$conex,$wemp_pmla){

        $strtipvar = array();
        $q  = " SELECT Ccocod, Cconom
                From ".$wbasemovhos."_000011 
                Where Ccoing = 'on' ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        while($row = mysql_fetch_assoc($res))
             {
               $strtipvar[$row['Ccocod']] = utf8_encode($row['Cconom']);
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

                $res = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
                while($row = mysql_fetch_assoc($res))
                   {
                     $strtipvar[$row['codigo']] = $row['nombre'];
                 }
            }

      return $strtipvar;
      }

     // *****************************************         FIN PHP         ********************************************
  ?>
  <html>
  <head>
    <title>Reporte Pacientes Internacionales</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" /></script>
    <script type="text/javascript" src="../../../include/root/jqueryalert.js"></script>
    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />    
    <script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
    <script type="text/javascript">

      var celda_ant="";
      var celda_ant_clase="";
      
      $(document).ready(function(){

          $("#txtfecini,#txtfecfin").datepicker({
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
              yearSuffix: '',
              showOn: "button",
              buttonImage: "../../images/medical/root/calendar.gif",
              buttonImageOnly: true,
          });

          $('#optpais').multiselect({
                     numberDisplayed: 1,
                     selectedList:1,
                     multiple:false
          }).multiselectfilter();


          $('#optservicio').multiselect({
                     numberDisplayed: 1,
                     selectedList:1,
                     multiple:false
          }).multiselectfilter();

          $('input#id_search_pacientes').quicksearch('table#tbldetalle tbody tr');

          $(".mostrartooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });


      }); // Finalizar Ready()


        // Función para exportar la tabla 'tblexcel'
        function Exportar(){
     
            //Creamos un Elemento Temporal en forma de enlace
            var tmpElemento = document.createElement('a');
            var data_type = 'data:application/vnd.ms-excel'; //Formato anterior xls

            // Obtenemos la información de la tabla
            var tabla_div = document.getElementById('tblexcel');
            var tabla_html = tabla_div.outerHTML.replace(/ /g, '%20');
            
            tmpElemento.href = data_type + ', ' + tabla_html;
            //Asignamos el nombre a nuestro EXCEL
            tmpElemento.download = 'pacienteinternacional.xls';
            // Simulamos el click al elemento creado para descargarlo
            tmpElemento.click();

         }

         
         function Consultar(){

          // Validar que el rango de fecha esté diligenciado
          if ( $("#txtfecini").val() == '' || $("#txtfecfin").val() == '' ) 
             {
                jAlert('Falta diligenciar Rango de fecha');
                return;
             }

          var wemp_pmla = $("#wemp_pmla").val();
          if ($("#optpais").val() == null)
              var wpais     = '';
          else
              var wpais     = $("#optpais").val(); 


          if ($("#optservicio").val() == null)
              var wservicio = '';
          else
              var wservicio = $("#optservicio").val();
          
          var wfechaini    =  $("#txtfecini").val();
          var wfechafin    =  $("#txtfecfin").val();          
          var arr_pais     =  $("#arr_pais").val();
          var wcodigopa    =  $("#wcodigopais").val();
          var wcodigode    =  $("#wcodigodepar").val();
          var wdocumenrep  =  $("#wdocumenrep").val();
          var wtipo        =  $("input:radio[name=radtipo]:checked").val();
          var wtipinfo     =  $("input:radio[name=radinforme]:checked").val();
          var wadicion     =  $('input:checkbox[name=chkinternacional]:checked').val();

          // Activar div que muestra el tiempo de proceso
          document.getElementById("divcargando").style.display   = "";

          $.post("pacientesinternacionales.php",
               {
                consultaAjax:  true,
                accion   :     'ConsultarDetalle',
                wemp_pmla:     wemp_pmla,
                wpais    :     wpais.toString(),
                wservicio:     wservicio.toString(),
                wfechaini:     wfechaini,
                wfechafin:     wfechafin,
                wtipo    :     wtipo,
                wtipinfo :     wtipinfo,
                wadicion :     wadicion,
                arr_pais :     arr_pais,
                wcodigopa:     wcodigopa,
                wcodigode:     wcodigode,
                wdocumenrep:   wdocumenrep
               }, function(data){

                  // En caso de no encontrar registros
                  if (data.titulo1 == 'N'){

                    $("#tbldetalle").hide();
                    $("#tblbuscar").hide();
                    $("#tblmensaje").show();
                  } 
                  // Mostrar tabla con registros consultados
                  else{
                    
                    $("#tblmensaje").hide();                    
                    $("#tbldetalle thead ").remove(0);
                    $("#tbldetalle tbody ").remove(0);
                    $("#tblbuscar").show();
                    $("#tbldetalle").show();
                    $("#tbldetalle").append(data.titulo1);
                    $('input#id_search_pacientes').quicksearch('table#tbldetalle tbody tr');
                    $("#tblexcel thead ").remove(0);
                    $("#tblexcel tbody ").remove(0);
                    $("#tblexcel").append(data.titulo2);
                  }

                  // Ocultar div que muestra el tiempo de proceso
                  document.getElementById("divcargando").style.display    = "none";

               },"json");

               
       }

      
      function Mostrarinternacional(valor){

        if (valor == 'i')
            $(".verinter").show();
        else
            $(".verinter").hide();

        $("#tbldetalle thead ").remove(0);
        $("#tbldetalle tbody ").remove(0);
        $("#tbldetalle").hide();
        $("#tblbuscar").hide();

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

      // ******************************** FUNCION Ilumina toda la columna donde se ubique el mouse 
      function iluminacolumna(celda,columna)
      {
         $("td.fondoAmarillo").removeClass('fondoAmarillo');
         $("."+columna).addClass("fondoAmarillo");       
      }

       // Funcion para restringir permitiendo digitar solo numeros
       function justNumbers(e)
       {
         var keynum = window.event ? window.event.keyCode : e.which;

         if ((keynum == 8) || (keynum == 46) || (keynum == 0))
              return true;

          return /\d/.test(String.fromCharCode(keynum));
       }


        function mostrarTooltip( celda ){
          if( !celda.tieneTooltip ){
            $( "*", celda ).tooltip();
            celda.tieneTooltip = 1;
          }
        }

       // ****** FUNCION Sacar un mensaje de alerta con formato predeterminado
       function alerta(txt){
         $("#textoAlerta").text( txt );
         $.blockUI({ message: $('#msjAlerta') });
           setTimeout( function(){
                   $.unblockUI();
                }, 1800 );
       }

       function cerrarVentana()
       {
          if(confirm("Esta seguro de salir?") == true){
            window.close();}
          else
            return false;
       }

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

        .button:hover {background-color: #3e8e41}

        .button:active {
           background-color: rgb(169,169,169);
           box-shadow: 0 5px #666;
           transform: translateY(4px);
         }

        .ui-multiselect { height:20px; overflow-x:hidden; padding:2px 0 2px 4px; text-align:left;font-size: 10pt;     } 
          
         BODY {
            font-family: verdana;
            font-size: 10pt;
            width: auto;
            height:auto;
         }

         .tbldetalle tr {
            height: 50px;
         }


    </style>
    </head>
    <body >
      <?php
        echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
        $wtitulo  = "REPORTE PACIENTES INTERNACIONALES";
        encabezado($wtitulo, $wactualiz, 'clinica');
        $arr_pais      = consultarPaises ($wbasedato,$conex,$wemp_pmla);
        $arr_servicio  = consultarServicios ($wbasedato,$wbasemovhos,$conex,$wemp_pmla);
        $codigosrep    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigopaisinternacional');
        $documentosrep = consultarAliasPorAplicacion($conex, $wemp_pmla, 'documentosinternacional');
        list($codigopais, $codigodepar)  = explode(',', $codigosrep);
        $mensaje="<a title='Adiciona a la consulta Paciente Nacional con Cedula de Extranjer&iacute;a o Pasaporte'>";
      ?>
      <table align='center' width='550px' >
          <tr  height='30px' width='50px'>
            <td class='fila1'><b>Tipo</b></td>
            <td class='fila2' align='left'>&nbsp;
            <input type='Radio' id='radtipo' name='radtipo' value='I' checked >Ingreso
            &nbsp;&nbsp;<input type='Radio' id='radtipo' name='radtipo' value='E' >Egreso
			&nbsp;&nbsp;<input type='Radio' id='radtipo' name='radtipo' value='F' >Facturaci&oacute;n
            </td>
          </tr>
          <tr height='30px'>
            <td class='fila1'><b>Fecha</b></td>
            <td class='fila2'>&nbsp;&nbsp;
            Inicial <input type='text' id='txtfecini' name='txtfecini' size='15' readonly>
            &nbsp;Final <input type='text' id='txtfecfin' name='txtfecfin' size='15' readonly>
            </td>
          </tr>
          <tr><td class='fila1'><b>Informe</b></td>
            <td  class='fila2'><input type="radio" id="radinforme" name="radinforme" value="i" checked onclick='Mostrarinternacional(this.value);'> Internacional
            <input type="radio" id="radinforme" name="radinforme" value="n" onclick='Mostrarinternacional(this.value);'> Nacional 
            </td>
          </tr>
          <tr height='30px'><td class='fila1'><b>Servicio</b></td>
            <td class='fila2'>&nbsp;&nbsp;<select id='optservicio' name='optservicio' multiple='multiple' style='width: 100px;'>
            <?php
              echo '<option ></option>';
              foreach( $arr_servicio as $key => $val){
                echo '<option value="' . $key .'">'.$val.'</option>';
              }
            ?>
            </select>
            </td>
          </tr>
          <tr class='fila1 verinter' height='30px' ><td class='fila1'><b>Pa&iacute;s de Residencia</b></td>
            <td class='fila2'>&nbsp;&nbsp;<select id='optpais' name='optpais' multiple='multiple' style='width: 100px;'>
            <?php
              echo '<option ></option>';
              foreach( $arr_pais as $key => $val){
                echo '<option value="' . $key .'">'.$val.'</option>';
              }
            ?>
            </select>
           </td>
          </tr>
          <tr class='fila1 verinter' height='30px' ><td class='fila1' onMouseover='mostrarTooltip(this);'><?=$mensaje?><b>Adicionales</b></td><td class='fila2 mostrartooltip' onMouseover='mostrarTooltip(this);'><?=$mensaje?><input type="checkbox" id="chkinternacional" name="chkinternacional" > x Documento</td></tr>
      </table>
      </br>
      <center><table>
        <tr>
        <td>&nbsp;&nbsp;<input type='button' id='btnConsultar' name='btnConsultar' class='button' value='Consultar' onclick='Consultar()'></td>
        <td>&nbsp;&nbsp;<input type='button' id='btnExportar'  name='btnExportar'  class='button' value='Exportar'  onclick='Exportar()'></td>
        <td>&nbsp;&nbsp;<input type='button' id='btnSalir'     name='btnSalir'     class='button' value='Salir'     onclick='cerrarVentana()'></td>
        </tr>
      </table>
      <div id="divcargando" name="divcargando" style='display:none;' ><center><img width="26" height="26" border="0" src="../../images/medical/ajax-loader9.gif"></center></div>
      <br>
      <div>
      <table align='left' style='display:none;' id='tblbuscar' name='tblbuscar'>
        <tr><td></td><td class='fila1'>Filtrar listado:&nbsp;&nbsp;<input id="id_search_pacientes" type="text" value="" size="20" name="id_search_pacientes" placeholder="Buscar en listado">&nbsp;&nbsp;</td></tr>
      </table>
      </div>
      <br>&nbsp;
      <center>
      <table width='1600px' id='tbldetalle' name='tbldetalle' class='tbldetalle' style='border: 1px solid blue' >
      </table>
      </center>
      <table id='tblexcel' name='tblexcel' style='display:none;'>
      </table>
      <table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;display:none;'>
        <tr><td class='fila2'>No hay registros para esta consulta</td></tr>
      </table>
      <input type="HIDDEN" name="arr_pais"     id="arr_pais"     value='<?=base64_encode(serialize($arr_pais))?>'>
      <input type="HIDDEN" name="wscroll"      id="wscroll"      value='0'>
      <input type="HIDDEN" name="wdocumenrep"  id="wdocumenrep"  value='<?=$documentosrep?>'>
      <input type="HIDDEN" name="wcodigopais"  id="wcodigopais"  value='<?=$codigopais?>'>
      <input type="HIDDEN" name="wcodigodepar" id="wcodigodepar" value='<?=$codigodepar?>'>
    </body>
    </html>




