  <?php
  ini_set('max_execution_time', 0);
     $consultaAjax = '';
    include_once("movhos/otros.php");
    include_once("conex.php");
    include_once("root/comun.php");
	require 'enviarNotificacion.php';
    global $conex_o;
	global $conex;
	global $bd;
		



	
 $wmovhos 	= consultarAliasPorAplicacion( $conex, '01', 'movhos' );  
 function array_cco($datos){
	   $unicos = unique_multidim_array($datos, 'cco');
    

    $temp = array();
    $temp2 = array();
    foreach ($unicos as $key2 => $value2) {
       $keys = array_keys(array_column($datos, 'cco'), $value2['cco']);
       foreach ($keys as $key ) {
        
        $temp[$value2['cco']][] = $datos[$key];
       }
      // foreach ($temp as $key => $value) {
       
        $temp2[$value2['cco']] = array_unico(array_column($temp[$value2['cco']], 'nombre'));
        
        
        
   // }//
   
    }
       
    return $temp2;
       
  
 }
  function sobrescribir_nit($str){
          $res=mb_substr($str,0,9);
          return $res;
     }
	 
 
 function consultar_concepto($concepto)
 {
      conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');

    $q= "select connom 
        from facon 
        where  concod=".$concepto." ";	
        
       
        $res= odbc_do($conex_o, $q);
  
        while($arr =odbc_fetch_array($res))
        {
            return $arr['connom'];
        }
  
 
  
 }
 function consultar_nit_convenio($convenio,$tipo_responsable)
 {
      conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
	  if($tipo_responsable=="E"){
				$q= "select empnit from inemp where empcod="."'".$convenio."'";
					echo $q;
					$res= odbc_do($conex_o, $q);
			  
					$arr =odbc_fetch_array($res);
				    
					return $arr['empnit'];
	  }else{
		  return $convenio;
	  }
  
 }
 
 function eliminar_letras($str)
{
    $res = preg_replace('/[A-z]+/', '', $str);
    return $res;
}



 function unique_multidim_array($array, $key) { 
  $temp_array = array(); 
  $i = 0; 
  $key_array = array();           
  foreach( $array as $val ) { 
      if ( ! in_array( $val[$key], $key_array ) ) { 
          $key_array[$i] = $val[$key]; 
          $temp_array[] = $val; // <--- remove the $i
      }
      $i++; 
  }
  return $temp_array; 
  }

 function array_unico($datos){
		$array =$datos;
	
		$valores = array_count_values($array);
		$valores2=array();
		$claves=array_keys($valores);
		$terminal=array();
		foreach ($valores as $valor){
			$valores2[]=$valor;
		}

		foreach ($claves as $key => $clave){
		 $terminal[]="".$clave. ' x'.$valores2[$key];
		 
		}

  
	return $terminal;
   }	
 function cuentasap_empresa( $conex,$convenio ){
	
	
	$sql = "SELECT Csccsa  from CuentasSapXconvenio   WHERE Csccco='".$convenio."'";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $row = mysql_fetch_array( $res ) ){
		return $row['Csccsa'];
	}
	


}	
function send_file_to_ftp_server($nombre_fichero){
		$file = $nombre_fichero;
		$remote_file = 'Interfaz_SAP/FACTURACION/'.$file;
		$ftp_server = '10.30.131.22';
		$ftp_user_name = 'isap';
		$ftp_user_pass = 'Porto2021!';

		// set up basic connection
		$conn_id = ftp_connect($ftp_server);

		// login with username and password
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

		// upload a file
		ftp_put($conn_id, $remote_file, $file, FTP_ASCII);

		// close the connection
		ftp_close($conn_id);
	}
    
  function particion_array( $datos, $p ) {
    $cant = count( $datos );
	echo $cant;
    $partes = floor( $cant / $p );
    $porc = $cant % $p;
    $datos2 = array();
    $cont = 0;
    for ($px = 0; $px < $p; $px++) {
        $incr = ($px < $porc) ? $partes + 1 : $partes;
        $datos2[$px] = array_slice( $datos, $cont, $incr );
        $cont += $incr;
    }
    return $datos2;
    
}
    
function list_facardet($accion,$facturas="",$date1="",$date2=""){
	
	 /*$fecha_actual = date('Y/m/d', strtotime(date('Y/m/d')."- 2 year"));
	  $fecha_actual2= strtotime($fecha_actual);
	  $anio = date("Y", $fecha_actual2);
	    $mes = date("m", $fecha_actual2);*/
    conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
        
  
        /*	$q= "SELECT  * FROM famov, cacar
                    WHERE 
                    movdoc = cardoc AND
                    movfue = carfue AND
                    movano = ".$anio."
                    AND movmes = ".$mes." AND movfec ='$fecha_actual' and movanu=0  ";	*/
$q="";

if($accion =="especifico"){
  $q= "SELECT  * FROM famov, cacar
  WHERE 
  movdoc = cardoc AND
  movfue = carfue AND
  movfue = 20  and  movanu=0 AND movdoc IN($facturas)";
  
}else{
  $q= "SELECT  *  FROM famov, cacar
  WHERE 
  movdoc = cardoc AND
  movfue = carfue AND
  movfue = 20  and  movanu=0 AND 
 movfec  BETWEEN '".$date1."' AND '".$date2."'"; 
}
  

 /*$q= "SELECT  *  FROM famov, cacar
                    WHERE 
                    movdoc = cardoc AND
                    movfue = carfue AND
                    movfue = 20  and  movanu=0 AND 
                   movfec  B '2022/01/31' AND '2022/01/31'"; */
				   
				   
          
          
/*	 $q= "SELECT  * FROM famov, cacar
                    WHERE 
                    movdoc = cardoc AND
                    movfue = carfue AND
                    movfue = 20  and  movanu=0 AND movdoc IN(20111)";*/
					
					
		  
                $i = 0;
                $res= odbc_do($conex_o, $q);
                
                    while($arr =odbc_fetch_array($res))
                    {
                      /* if( $i == 100){
                        break;
                        }*/
                    
                      $datos[]= $arr;
                        $i++;
            }

        liberarConexionOdbc($conex_o);
        odbc_close_all();
        return $datos;
    }
    
    
function list_facturables($fuente,$documento,$fecha,$nit_responsable,$nombre,$tipo){
    conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
    $wmovhos 	= consultarAliasPorAplicacion( $conex, '01', 'movhos' );   
  
	$q= "select cardetcco,cardetcon, cardetcod,sum(carfacval) as total 
       from facardet,facarfac 
       where carfacreg=cardetreg and carfacdoc=".$documento."  group by 1,2,3";	
			
	//echo $q;

/*  $q= "SELECT  * FROM famov, cacar
            WHERE 
            movdoc = cardoc AND
            movfue = carfue AND
            movfue = 21 and movanu=0 ";*/
   
   
        $e = 1;
        $res= odbc_do($conex_o, $q);
        $aux=array();
            while($arr =odbc_fetch_array($res))
            {
                    
                    $reg = array(
                      'fechadoc'=> date_format(date_create($fecha),"Ymd"),
                      'clasedoc'=> "DR",
                      'sociedad'=> 3000,
                      'fechacon'=> date_format(date_create($fecha),"Ymd"),
                      'moneda'=> 'COP',
                      'referencia'=> 'CPA'. $documento,
                      'textocab'=>'CPA'.$documento,
                      'clavecon'=> 50,
                      'cuenta'=> cuentacontable($conex,$arr['cardetcco'],$arr['cardetcon'],$arr['cardetcod'],$wmovhos),
                      'tercero'=> null,
                      'importe'=>intval($arr['total']),
                      'indimp'=> "A0",
                      'calimp'=> null,
                      'fechaval'=> date_format(date_create($fecha),"Ymd"), 
                      'asignacion'=>codigo_sap_nit( $conex,$nit_responsable),
                      'texto'=>consultar_concepto($arr['cardetcon']),
                      'bancopro'=> null,
                      'centrocos'=> homologacionCco( $conex, $wmovhos,$arr['cardetcco']),
                      'orden'=> null,
                      'viapago'=> null,
                      'centro'=> null,
                      'centroben'=> null,
                      'cuentadiver'=> null,
                      'bloquedopag'=> null,
                      'referencia1'=> ($tipo=="E")?sobrescribir_nit(eliminar_letras($nit_responsable)):$nit_responsable,
                      'referencia3'=> $nombre,
                      'tiporete'=> null,
                      'indirete'=> null,
                      'baserete'=> null
                      );
                      $aux[]=$reg;
                      echo '<pre>';
                     // print_r($reg);
                      echo '</pre>';

            }
           return $aux;
        liberarConexionOdbc($conex_o);
        odbc_close_all();
       
	}
  
    //homologaciones
   
    
    
	function total_factura($fuente, $factura){
       conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
 
        $q= "select sum(carfacval) as total_factura from facarfac where carfacfue = '".$fuente."' and carfacdoc = ".$factura." ";
            
            $res= odbc_do($conex_o, $q);
            
                while($arr =odbc_fetch_array($res))
                {
                    $datos= $arr;
                }

            liberarConexionOdbc($conex_o);
            odbc_close_all();
            return $datos['total_factura'];
    
    }
    
   
 function homologacionCco( $conex, $wmovhos, $cco ){
	
	
	$sql = "SELECT Codcco
			  FROM ".$wmovhos."_000296
			 WHERE Codcco like  '%".$cco."%'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $row = mysql_fetch_array( $res ) ){
		return $row['Codcco'];
	}
	


}
function cuenta_descuento( $conex,$cco ){
	
	
	$sql = "SELECT Cxfcue
			 FROM centrocostoXunidadfuncional
			 WHERE Cxfccc like  '%".$cco."%' limit 1 ";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $row = mysql_fetch_array( $res ) ){
		return $row['Cxfcue'];
	}
	


}

 function codigo_sap_nit( $conex,$nit ){
	
	
	$sql = "SELECT Axnsap  from asignacion_nit_clientes    WHERE Axnnit='".$nit."'";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $row = mysql_fetch_array( $res ) ){
		return $row['Axnsap'];
	}
	


}
  function cuentacontable($conex,$cco,$concepto,$cfacturable,$wmovhos)
 {
    	  $facturable=$cfacturable;
		  $sql="";
		    if (ctype_digit( $cfacturable) || $cfacturable =="0" ) {
			$sql = " SELECT Cxcccn AS cuenta_contable FROM {$wmovhos}_000297 WHERE Cxccco={$cco} AND Cxccon={$concepto} AND Cxccod={$cfacturable} limit 1";
			} else {
				  $facturable="'$cfacturable'";
					$sql = " SELECT Cxcccn AS cuenta_contable FROM ".$wmovhos."_000297 WHERE Cxccco=".$cco." AND Cxccon=".$concepto." AND Cxccod="."".trim($facturable)." ";
				}
         // $sql = " SELECT Cxcccn AS cuenta_contable FROM {$wmovhos}_000297 WHERE Cxccco={$cco} AND Cxccon={$concepto} AND Cxccod={$facturable} limit 1";
		 
		
          $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

        while( $row = mysql_fetch_array( $res ) ){
              return $row['cuenta_contable'];
        }

 }	
    //fin
  


 function calcular_retencion_factura($valor,$movfue){
    
	$reg3=$valor;
    $reg4=$valor;
	if($movfue==20 || $movfue==25 || $movfue==21  ){
		$reg3['clavecon'] = 50;
		$reg3['cuenta'] = 2365750502;
		$reg4['clavecon'] = 40;
	    $reg4['cuenta'] = 1355150202;
	}else{
		$reg3['clavecon'] = 40;
		$reg3['cuenta'] = 1355150202;
		$reg4['clavecon'] = 50;
		$reg4['cuenta'] = 2365750502;
	}

    if($movfue !=21){
	$reg3['importe'] = intval($valor['importe']*0.02);
    $reg4['importe'] = intval($valor['importe']*0.02);	
	 $retencion_factura=array($reg3,$reg4);
   }else{
		$reg3['importe'] = intval($valor['importe']*0.019);
		$reg3['calimp']=null;
        $reg4['importe'] = intval($valor['importe']*0.019);	
		$reg4['calimp']=null;
		 $retencion_factura=array($reg3,$reg4);
	}
	
	return $retencion_factura;
	
 }
 
 function consultar_descuento($documento)
 {
      conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');

    $q= "select  sum(carfacdes) as descuento from facarfac where carfacdoc=".$documento."";	
 
       
        $res= odbc_do($conex_o, $q);
  
        while($arr =odbc_fetch_array($res))
        {
            return $arr['descuento'];
        }
  
 
  
 }
 function consultar_valor_descuento($documento,$nit_responsable,$fecha)
 {
	 var_dump($nit_responsable);
      conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
    $wmovhos 	= consultarAliasPorAplicacion( $conex, '01', 'movhos' );   
  
 $q= "select cardetcco,cardetcon, cardetcod,sum(carfacdes) as total 
       from facardet,facarfac 
       where carfacreg=cardetreg and carfacdoc=".$documento."  group by 1,2,3";	
			
	//echo $q;

/*  $q= "SELECT  * FROM famov, cacar
            WHERE 
            movdoc = cardoc AND
            movfue = carfue AND
            movfue = 21 and movanu=0 ";*/
   
   
        $e = 1;
        $res= odbc_do($conex_o, $q);
        $aux=array();
            while($arr =odbc_fetch_array($res))
            {
                 if($arr['total']>0){
                    $reg = array(
                      'fechadoc'=> date_format(date_create($fecha),"Ymd"),
                      'clasedoc'=> "DR",
                      'sociedad'=> 3000,
                      'fechacon'=> date_format(date_create($fecha),"Ymd"),
                      'moneda'=> 'COP',
                      'referencia'=> 'CPA'. $documento,
                      'textocab'=>'CPA'.$documento,
                      'clavecon'=> 40,
                      'cuenta'=>cuenta_descuento($conex,$arr['cardetcco']),
                      'tercero'=> null,
                      'importe'=>intval($arr['total']),
                      'indimp'=> "",
                      'calimp'=> null,
                      'fechaval'=> date_format(date_create($fecha),"Ymd"), 
                      'asignacion'=>codigo_sap_nit( $conex,$nit_responsable),
                      'texto'=>"DESCUENTO",
                      'bancopro'=> null,
                      'centrocos'=> homologacionCco( $conex, $wmovhos,$arr['cardetcco']),
                      'orden'=> null,
                      'viapago'=> null,
                      'centro'=> null,
                      'centroben'=> null,
                      'cuentadiver'=> null,
                      'bloquedopag'=> null,
                      'referencia1'=> null,
                      'referencia3'=> null,
                      'tiporete'=> null,
                      'indirete'=> null,
                      'baserete'=> null
                      );
                      $aux[]=$reg;
                      echo '<pre>';
                     // print_r($reg);
                      echo '</pre>';
				 }
            }
           return $aux;
        liberarConexionOdbc($conex_o);
        odbc_close_all();
  
 }  
			 
function estructura (){
  echo 'entro';
 $path = getcwd();
 $facturas="";
 $date1="";
 $date2="";
 if(isset($_GET["facturas"])){
  $facturas=$_GET["facturas"];
}
if(isset($_GET["date1"])){
  $date1=$_GET["date1"];
}
if(isset($_GET["date2"])){
  $date2=$_GET["date2"];
}
$accion=$_GET["accion"];
   $datos = list_facardet($accion,$facturas,$date1,$date2);
    $legacy = array();
	$nit_real="";
	$mostrar="";
	  $c_divergente=0;
    foreach($datos as $value){
         $c_divergente=($value['carind']=="E")?cuentasap_empresa($conex,$value['carced']):1301250100;
		 
	    if($value['movfue']==20 || $value['movfue']==25  ){
            
          $facturables = list_facturables($value['movfue'],$value['cardoc'],$value['movfec'],consultar_nit_convenio($value['carced'],$value['movemp']),$value['carres'],$value['carind']);
          foreach ($facturables as $facturable) {
            array_push($legacy, $facturable);
          }
		     $desc=0;
             $desc=consultar_descuento($value['cardoc']);
			 if($desc>0){
				 $descuentos = consultar_valor_descuento($value['cardoc'],consultar_nit_convenio($value['carced'],$value['movemp']),$value['movfec']);
				  foreach ($descuentos as  $descuento) {
					array_push($legacy,$descuento);
				  }
				
				$total=total_factura($value['movfue'], $value['movdoc']);
				
				 $reg = array(
                'fechadoc'=> date_format(date_create($value['movfec']),"Ymd"),
                'clasedoc'=> "DR",
                'sociedad'=> 3000,
                'fechacon'=> date_format(date_create($value['movfec']),"Ymd"),
                'moneda'=> 'COP',
                'referencia'=> 'CPA'.$value['cardoc'],
                'textocab'=>'CPA'. $value['carfac'],
                'clavecon'=> 01,
                'cuenta'=> codigo_sap_nit( $conex,consultar_nit_convenio($value['carced'],$value['movemp'])) ,
                'tercero'=> null,
                'importe'=>intval($total-$desc),
                'indimp'=> null,
                'calimp'=> null,
                'fechaval'=> date_format(date_create($value['movfec']),"Ymd"), 
                'asignacion'=>codigo_sap_nit( $conex,consultar_nit_convenio($value['carced'],$value['movemp'])) ,
                'texto'=> $value['carfac'],
                'bancopro'=> null,
                'centrocos'=> null,
                'orden'=> null,
                'viapago'=> null,
                'centro'=> null,
                'centroben'=> null,
                'cuentadiver'=> $c_divergente,
                'bloquedopag'=> null,
                'referencia1'=>consultar_nit_convenio($value['carced'],$value['movemp']),
                'referencia3'=> $value['carres'],
                'tiporete'=> null,
                'indirete'=> null,
                'baserete'=> null
                );
				 
				
				if($value['movemp']==trim("E")){
				$calcular_retencion_factura=calcular_retencion_factura($reg,$value['movfue']);
				
				$reg3=$calcular_retencion_factura[0];
				$reg4=$calcular_retencion_factura[1];
              
			 
			      array_push($legacy,$reg,$reg3,$reg4);
				}else{
				
				  array_push($legacy, $reg);
				}
				 
			 }else{
				 $reg = array(
                'fechadoc'=> date_format(date_create($value['movfec']),"Ymd"),
                'clasedoc'=> "DR",
                'sociedad'=> 3000,
                'fechacon'=> date_format(date_create($value['movfec']),"Ymd"),
                'moneda'=> 'COP',
                'referencia'=> 'CPA'.$value['cardoc'],
                'textocab'=>'CPA'. $value['carfac'],
                'clavecon'=> 01,
                'cuenta'=> codigo_sap_nit( $conex,consultar_nit_convenio($value['carced'],$value['movemp'])) ,
                'tercero'=> null,
                'importe'=>intval(total_factura($value['movfue'], $value['movdoc'])),
                'indimp'=> null,
                'calimp'=> null,
                'fechaval'=> date_format(date_create($value['movfec']),"Ymd"), 
                'asignacion'=>codigo_sap_nit( $conex,consultar_nit_convenio($value['carced'],$value['movemp'])) ,
                'texto'=> $value['carfac'],
                'bancopro'=> null,
                'centrocos'=> null,
                'orden'=> null,
                'viapago'=> null,
                'centro'=> null,
                'centroben'=> null,
                'cuentadiver'=>$c_divergente,
                'bloquedopag'=> null,
                'referencia1'=>consultar_nit_convenio($value['carced'],$value['movemp']),
                'referencia3'=> $value['carres'],
                'tiporete'=> null,
                'indirete'=> null,
                'baserete'=> null
                );
				
				if($value['movemp']==trim("E")){
				$calcular_retencion_factura=calcular_retencion_factura($reg,$value['movfue']);
				
				$reg3=$calcular_retencion_factura[0];
				$reg4=$calcular_retencion_factura[1];
              
			 
			      array_push($legacy, $reg,$reg3,$reg4);
				}else{
				  array_push($legacy, $reg);
				}
              //  array_push($legacy, $reg);
			 }
    }
    
        }
		// var_dump(consultar_nit_convenio('89090379091'));
     $datos =$legacy;
     print_r($datos);
		$reg = count($datos);
		$sep = 5000000;
		$div = ceil($reg/$sep);
		$datos2 = particion_array($datos, $div);
		foreach($datos2 as $key =>$d2){
		 $nombre_fichero='Legacy_FAC_'.date("Ymd").'_'.'parte'.($key+1).'.txt';
		 $fpV0 = fopen($nombre_fichero, 'w');
         		
		foreach($d2 as $d){
		   fputcsv($fpV0, $d, "\t");  
		}
		// send_file_to_ftp_server($nombre_fichero);
            global $wemp_pmla;

            $correos =consultarAliasPorAplicacion($conex, $wemp_pmla, "correorsapmanual");
           
            $correos= explode("-", $correos);
            foreach ($correos as $correo) {
                $mail=new enviarNotificacion();   
                $mail->enviarNotificacion($nombre_fichero,$correo);
             }
            unlink($path.'/'.$nombre_fichero);     
			
		}
		
		
		
}
     
  $datos = estructura();
  //$datos = json_encode($datos);
 // header('Content-Type: application/json');
   //$datos1 = estructura();
 //  print_r(consultar_concepto('0072'));

 //cuentacontable($conex,"3504","0616","0")
echo '<pre>';
print_r( $datos1);
echo '</pre>';

 


?>
              
