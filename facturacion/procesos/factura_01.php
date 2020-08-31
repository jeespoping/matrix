<?php
//==========================================================================================================================================
//PROGRAMA				      : ENVIO DE CORREO PARA FACTURAS QUE NO PASARON AL CEN FINANCIERO                                      |
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2019-03-11.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2019-03-11.                                                                                             |
//DESCRIPCION			      : ENVIO DE CORREO PARA FACTURAS QUE NO PASARON AL CEN FINANCIERO                                      |.        |
//                                                                                                                                          |
//TABLAS UTILIZADAS SON CORRESPONDIENTES A LAS DE FACUTRACION DE UNIX:                                                                                                                       |
//
//  
// 
//                                                                                                                                     |
//==========================================================================================================================================



  include_once("conex.php");
  include_once("root/comun.php");
  $conex_o = odbc_connect('informix','','')  or die("No se realizo conexion con la BD de Facturacion");

//CALCULAR FECHA ACTUAL Y RESTAR UN DIA
$fecha = date('Y-m-d');
$nuevafecha = strtotime ( '-1 day' , strtotime ( $fecha ) ) ;
$nuevafecha = date ( 'Ymd' , $nuevafecha );
//echo 'La fecha es:'.$nuevafecha;

//CONSULTAR SI HAY DATOS EN LAS TABLAS Y CONTAR:
					$conteo_buscarfac = "select movfue,movdoc,movfec,movanu
										from famov
										where movfec between '$nuevafecha' and '$nuevafecha'
										order by 1
										into temp tmpfamov";
										odbc_do($conex_o, $conteo_buscarfac);
					$conteo_buscarfac3 = "select movfue,movdoc,movfec,fue,carfca fca,carfac fac
										from tmpfamov,cacar,outer amefacele
										where movfue in ('25','27')
										   and movanu='0'
										   and movfue=fue
										   and movdoc=doc
										   and movfue=carfue
										   and movdoc=cardoc
										   into temp tmpnota1";
										odbc_do($conex_o, $conteo_buscarfac3);	
					$conteo_buscarfac5 = "select movfue,movdoc,movfec,fue
										   from tmpnota1,cacar
										   where fca=carfue
										   and fac=cardoc
										   and carfec>='20190110'
										   and cardoc>5000000
										   and fue is null
										   into temp tmpnota2";
										odbc_do($conex_o, $conteo_buscarfac5);	
					$conteo_buscarfac7 = "select movfue,movdoc,movfec,fue
										  from tmpfamov,outer amefacele
										   where movfue in ('20','22')
										   and movdoc>5000000
										   and movfec between '$nuevafecha' and '$nuevafecha'
										   and movanu='0'
										   and movfue=fue
										   and movdoc=doc
										   into temp tmpnota3";
										odbc_do($conex_o, $conteo_buscarfac7);					   					   					
					$conteo_buscarfac9 = "select * from tmpnota2
										   union all
										   select * from tmpnota3
										   into temp tmpbusca";
										odbc_do($conex_o, $conteo_buscarfac9);						   
					$conteo_buscarfac11 = "select movfue,movdoc,movfec,fue,carval,carcco
										   from tmpbusca,cacar
										   where fue is null
										   and movfue=carfue
										   and movdoc=cardoc
										   order by 3,1,2
										   into temp tmpbusca1";
										odbc_do($conex_o, $conteo_buscarfac11);					
					$conteo_buscarfac13 = "select count(*) cant
										   from tmpbusca1";
					$dato_conteo = odbc_do($conex_o, $conteo_buscarfac13);




while(odbc_fetch_row($dato_conteo))
{
    $numDatos = odbc_result($dato_conteo, 1);
	echo 'registros'.$numDatos;
	/*echo 'registros'.$numDatos;
	$Contenido2 .= '<h4 style="font-size: 2em;color: black; margin: 0;padding: 5px 10px; width: 700px; ">' . 'NO SE ENCONTRO REGISTRO HOY' . '</h4>';	
			 	 $Emaildesti   =  'informatica.clinica@lasamericas.com.co';         
                 $Nomdestino   =  'Facturacion';
                 $ArrayOrigen  = array('email'    => 'informatica.clinica@lasamericas.com.co',
                                      'password' => 'pmla2902',
                                      'from'     => '',
                                      'fromName' => 'Facturas Que No Pasaron');

                 $ArrayDestino = array($Emaildesti);
                 $wasunto      = 'No se encontro registro';
				 sendToEmail(utf8_decode($wasunto),utf8_decode($Contenido2),utf8_decode($Contenido2),$ArrayOrigen,$ArrayDestino);
				 echo 'se envio email 2';*/
}				 
    if($numDatos > 0)
    {
				//echo 'LA ACCION SI ENTRO POR ACA'.$numDatos;
		$query_consultamovimiento = "select movfue,movdoc,movfec,movanu
							from famov
							where movfec between '$nuevafecha' and '$nuevafecha'
							order by 1
							into temp tmpfamov_c2";
							odbc_do($conex_o, $query_consultamovimiento);
		//echo 'Hola mundo';
		$query_consultamovimiento3 = "select movfue,movdoc,movfec,fue,carfca fca,carfac fac
							from tmpfamov_c2,cacar,outer amefacele
							where movfue in ('25','27')
							   and movanu='0'
							   and movfue=fue
							   and movdoc=doc
							   and movfue=carfue
							   and movdoc=cardoc
							   into temp tmpnota1_c2";
						   odbc_do($conex_o, $query_consultamovimiento3);					   
		$query_consultamovimiento5 = "select movfue,movdoc,movfec,fue
							   from tmpnota1_c2,cacar
							   where fca=carfue
							   and fac=cardoc
							   and carfec>='20190110'
							   and cardoc>5000000
							   and fue is null
							   into temp tmpnota2_c2";
							odbc_do($conex_o, $query_consultamovimiento5);					   					   
		$query_consultamovimiento7 = "select movfue,movdoc,movfec,fue
							  from tmpfamov_c2,outer amefacele
							   where movfue in ('20','22')
							   and movdoc>5000000
							   and movfec between '$nuevafecha' and '$nuevafecha'
							   and movanu='0'
							   and movfue=fue
							   and movdoc=doc
							   into temp tmpnota3_c2";
							odbc_do($conex_o, $query_consultamovimiento7);					   					   					
		$query_consultamovimiento9 = "select * from tmpnota2_c2
							   union all
							   select * from tmpnota3_c2
							   into temp tmpbusca_c2";
							odbc_do($conex_o, $query_consultamovimiento9);						   
		$query_consultamovimiento11 = "select movfue,movdoc,movfec,fue,carval,carcco
							   from tmpbusca_c2,cacar
							   where fue is null
							   and movfue=carfue
							   and movdoc=cardoc
							   order by 3,1,2";
		$dato_movimiento = odbc_do($conex_o, $query_consultamovimiento11);
			//if($dato_movimiento)
			//{				
				//Construir el contenido del Email
				$Contenido .= '<h4 style="margin: 0;">Facturas que no han pasado al CEN FINANCIERO:</h4>';
				$Contenido  .= "<table border='1'; style='text-align:center;'>";
				$Contenido .= '<tr>';
				$Contenido .= '<td>';
				$Contenido .= '<h4 style="margin: 0; width: 100px; ">FUENTE</h4>';
				$Contenido .= '</td>';
				$Contenido .= '<td>';
				$Contenido .= '<h4 style="margin: 0; width: 100px; ">DOCUMENTO</h4>';
				$Contenido .= '</td>';
				$Contenido .= '<td>';
				$Contenido .= '<h4 style="margin: 0; width: 100px; ">FECHA</h4>';
				$Contenido .= '</td>';
				$Contenido .= '<td>';
				$Contenido .= '<h4 style="margin: 0; width: 100px; ">VALOR</h4>';
				$Contenido .= '</td>';
				$Contenido .= '<td>';
				$Contenido .= '<h4 style="margin: 0; width: 100px; ">CENTRO DE COSTOS</h4>';
				$Contenido .= '</td>';
				$Contenido .= '</tr>';
			   
				while(odbc_fetch_row($dato_movimiento))
				{
					
					$movfue = odbc_result($dato_movimiento, 1);
					$movdoc = odbc_result($dato_movimiento, 2);
					$movfec = odbc_result($dato_movimiento, 3);
					//$fue = odbc_result($dato_movimiento, 4);
					$carval = odbc_result($dato_movimiento, 5);
					$carcco = odbc_result($dato_movimiento, 6);
					$Contenido .= '<tr>';
					$Contenido .= '<td>';
					$Contenido .= '<h3 margin: 0;width: 100px; ">' . $movfue . '</h3>';
					$Contenido .= '</td>';
					$Contenido .= '<td>';
					$Contenido .= '<h3 margin: 0;width: 100px; ">' . $movdoc . '</h3>';
					$Contenido .= '</td>';
					$Contenido .= '<td>';
					$Contenido .= '<h3 margin: 0;width: 100px; ">' . $movfec . '</h3>';
					$Contenido .= '</td>';
					$Contenido .= '<td>';
					$Contenido .= '<h3 margin: 0;width: 100px; ">' . $carval . '</h3>';
					$Contenido .= '</td>';
					$Contenido .= '<td>';
					$Contenido .= '<h3 margin: 0;width: 100px; ">' . $carcco . '</h3>';
					$Contenido .= '</td>';
				}
					$Contenido .= '</tr>';
					$Contenido .= '</table>';
						 $Emaildesti   =  'informatica.clinica@lasamericas.com.co';
						 $Emaildesti2   =  'facturacion@lasamericas.com.co';
						 $Emaildesti3   =  'gustavo.avendano@lasamericas.com.co';
						 $Nomdestino   =  'Facturacion';
						 $ArrayOrigen  = array('email'    => 'informatica.clinica@lasamericas.com.co',
											  'password' => 'Informatica2020',
											  'from'     => '',
											  'fromName' => 'Informe De Facturas Que No Pasaron');
		
						 $ArrayDestino = array($Emaildesti,$Emaildesti2,$Emaildesti3);
						 $wasunto      = 'Informe De Facturas Que No Pasaron';
		
						 sendToEmail(utf8_decode($wasunto),utf8_decode($Contenido),utf8_decode($Contenido),$ArrayOrigen,$ArrayDestino);
						echo 'se envio email correcto';
				//}
			
		}
	

?>