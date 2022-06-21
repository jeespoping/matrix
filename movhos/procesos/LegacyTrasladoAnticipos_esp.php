<?php

    ini_set('memory_limit', '-1');
    $consultaAjax = '';
    include_once("movhos/otros.php");
    include_once("conex.php");
    include_once("root/comun.php");
    require 'enviarNotificacion.php';
    global $conex_o;
	global $conex;
	global $bd;

	$fecha_actual = date('Y-m-d', strtotime(date('Y-m-d')."- 1 day"));
	$path = getcwd();
	
	
	 function eliminar_letras($str)
     {
			$res = preg_replace('/[A-z]+/', '', $str);
			return $res;
     }
	 
	  function sobrescribir_nit($str){
          $res=mb_substr($str,0,9);
          return $res;
     }
	 
	  function consultar_empresa($nit)
 {
      conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
		$n="$nit";
    $q= "select nitraz 
        from  conit
        where  nitnit='$n'";	
   
        $res= odbc_do($conex_o, $q);
  
        while($arr =odbc_fetch_array($res))
        {
            return $arr['nitraz'];
        }
  
 
  
 }
	
	 function list_cacar($date1,$date2){

        conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
      // $fecha_inicial= date('Y/m/d', strtotime('last Sunday '));
       //$fecha_final=date('Y/m/d', strtotime('next Sunday -1 day' ));
	   //  $fecha_inicial= date('Y/m/d', strtotime('last Sunday '));
       //$fecha_final=date('Y/m/d', strtotime('next Sunday -1 day' ));
		//$q= "select famov.*,cacar.* from famov,cacar where  
			//		  movhis=carhis and movnum=carnum and
				//	  movfue=carfca and movdoc=carfac and carfue=38 and carfec >='".$fecha_inicial."' and carfec <='".$fecha_final."'  ";

		 $q= "select famov.*,cacar.* from famov,cacar where  
              movhis=carhis and movnum=carnum and
              movfue=carfca and movdoc=carfac and carfue=38 and carfec >='".$date1."' and carfec <='".$date2."'  ";
        
       /* $q= "select *
             from cacar 
             where carano = ".date('Y')." and carmes = ".date('m')." and carfec = '".$fecha_actual."' and carfue in (35) and caranu = 0 "; */
   
           $i = 0;
            $res= odbc_do($conex_o, $q);
            
                while($arr =odbc_fetch_array($res))
                {
                  /*  if( $i == 1000){
                    break;
                    }*/
             
                $datos[]= $arr;
                    $i++;
                }

            liberarConexionOdbc($conex_o);
            odbc_close_all();
            return $datos; 
    }
    
 function codigo_sap_nit( $conex,$nit ){
	
	
	$sql = "SELECT Axnsap  from asignacion_nit_clientes    WHERE Axnnit='".$nit."'";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $row = mysql_fetch_array( $res ) ){
		return $row['Axnsap'];
	}
	


}	
function consultar_cuenta_convenio($conex,$tipo,$convenio){
$cuentapsap="";
if($tipo=="E"){	
	$sql = "SELECT Csccsa  from CuentasSapXconvenio    WHERE Csccco='".$convenio."'";	
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		while( $row = mysql_fetch_array( $res ) ){
			$cuentapsap = $row['Csccsa'];//cuenta SAP
		}
}else{
	$cuentapsap="1301250100";
}
return $cuentapsap;
}

    
    function estructura (){
        $date1=$_GET["date1"];
        $date2=$_GET["date2"];
    $datos = list_cacar($date1,$date2);
    $legacy = array();
    foreach($datos as $value){
	
       $reg = array(
                        'fechadoc'=> date_format(date_create($value['carfec']),"Ymd"),
                        'clasedoc'=> "C9",
                        'sociedad'=> 3000,
                        'fechacon'=> date_format(date_create($value['carfec']),"Ymd"),
                        'moneda'=> 'COP',
                        'referencia'=> 'CPA'.$value['movdoc'],
                        'textocab'=> 'ANT APLIC A CPA'.$value['movdoc'],
                        'clavecon'=> 01,
                        'cuenta'=>codigo_sap_nit( $conex,eliminar_letras($value['carced'])),
                        'tercero'=> null,
                        'vacio'=> null,
                        'importe'=> intval($value['carval']),
                        'indimp'=> null,
                        'calimp'=> null,
                        'fechaval'=> date_format(date_create($value['carfec']),"Ymd"), 
                        'asignacion'=>codigo_sap_nit( $conex,eliminar_letras($value['carced'])),
                        'texto'=> 'ANT APLIC A CPA'.$value['movdoc'],
                        'bancopro'=> null,
                        'centrocos'=> null,
                        'orden'=> null,
                        'viapago'=> null,
                        'centro'=> null,
                        'centroben'=> null,
                        'cuentadiver'=>1305050400,
                        'bloquedopag'=> null,
                        'referencia1'=>($value['carind']=="E")?sobrescribir_nit(eliminar_letras($value['carced'])):eliminar_letras($value['carced']),
                        'referencia3'=>consultar_empresa(eliminar_letras($value['carced'])),
                        'tiporete'=> null,
                        'indirete'=> null,
                        'baserete'=> null
                        );
                        $reg2 = $reg;
                        $reg2['clavecon'] = 11;
                        $reg2['cuenta'] =codigo_sap_nit( $conex,eliminar_letras($value['carced']));
                        $reg2['cuentadiver'] =consultar_cuenta_convenio($conex,$value['carind'],$value['carced']);
                        array_push($legacy, $reg,$reg2);
       
    }
    return $legacy;
  
    }
    
   function utf8_converter($array){
    array_walk_recursive($array, function(&$item, $key){
        if(!mb_detect_encoding($item, 'utf-8', true)){
            $item = utf8_encode($item);
        }
    }); 
    return $array;
    }
    function particion_array( $datos, $p ) {
        $cant = count( $datos );
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
    function send_file_to_ftp_server($fileNameV0){
		$remote_fileV0 = 'Interfaz_SAP/CAJA/'.$fileNameV0;
		
		$ftp_server = '10.30.131.22';
		$ftp_user_name = 'isap';
		$ftp_user_pass = 'Porto2021!';

		// set up basic connection
		$conn_id = ftp_connect($ftp_server);

		// login with username and password
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

		// upload a file
		ftp_put($conn_id, $remote_fileV0, $fileNameV0, FTP_ASCII);
		

		// close the connection
		ftp_close($conn_id);
	}

 
  

    try
    {
		//$datos = consultar_cuenta_convenio($conex,"P","805009741LA");
		//print_r($datos);
        $datos = estructura($conexion);
        $datos = utf8_converter( $datos);
        $reg = count($datos);
        $sep = 500000;
        $div = ceil($reg/$sep);
        $array_div = particion_array($datos, $div);
        //print_r($array_div);
        foreach($array_div as $key =>$item1){
            $nombre_fichero='Legacy_TRASLADO ANTICIPO_'.$fecha_actual.'_'.'parte'.($key+1).'.txt';
            $fpV0 = fopen($nombre_fichero, 'w');
                    
            foreach($item1 as $item){
            fputcsv($fpV0, $item, "\t");  
            }
         //   send_file_to_ftp_server($nombre_fichero);
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
    catch (ErrorException $e)
    {
        echo $e->getMessage();
    }
  
  

 
 ?>