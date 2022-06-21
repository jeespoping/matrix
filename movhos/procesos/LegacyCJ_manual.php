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
        //$fecha_actual = date('Y/m/d');
       $fecha_inicial='';
       $fecha_final=date('Y/m/d', strtotime(' -1 day' ));
	   
    
	    
		$q= "select *
             from cacar 
             where   carfec >= '".$date1."' and carfec <= '".$date2."' and carfue in (35)   and caranu = 0 ";
     
		/* $q= "select *
             from cacar 
             where carano = '2021' and carmes = '11' and carfec >= '2021/11/01' and carfec <= '2021/11/04' and carfue in (35)   and caranu = 0 ";*/
        
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
    
    function detalle_cajabanco($fuente, $documento){
       conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
 
        $q= "select * 
             from cbmovdet 
             where 
                movdetfue = '".$fuente."' 
                    and movdetdoc = ".$documento." 
                        and movdetanu = 0 
                            and movdetfpa not in ( 'TC','TS','TD')";
							//print_r($q);
							/*   $q= "select * 
             from cbmovdet 
             where 
                movdetfue = '".$fuente."' 
                    and movdetdoc = ".$documento." 
                        and movdetanu = 0 
                            and movdetfpa not in ('TD', 'TC', 'TR', '15', 'PM', 'TS', 'PS')"; */
            
            $res= odbc_do($conex_o, $q);
            
                while($arr =odbc_fetch_array($res))
                {
                    $datos= $arr;
                }

            liberarConexionOdbc($conex_o);
            odbc_close_all();
            return $datos;
    
    }
function detalle_caja($fuente, $documento){
       conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
 
        $q= "select * 
             from cbmovdet 
             where 
                movdetfue = '".$fuente."' 
                    and movdetdoc = ".$documento." 
                        and movdetanu = 0 
                            and movdetfpa not in ( 'TC','TS','TD','PM')";
			
  			
							
							/*   $q= "select * 
             from cbmovdet 
             where 
                movdetfue = '".$fuente."' 
                    and movdetdoc = ".$documento." 
                        and movdetanu = 0 
                            and movdetfpa not in ('TD', 'TC', 'TR', '15', 'PM', 'TS', 'PS')"; */
            
            $res= odbc_do($conex_o, $q);
            
                while($arr =odbc_fetch_array($res))
                {
                    $datos[]= $arr;
                }

            liberarConexionOdbc($conex_o);
            odbc_close_all();
            return $datos;
    
    }
    function consultar_cuentas($fpa, $banco, $fuente){
     
       
        if($banco != "512" and $banco != "060" and $banco != "014" and $banco != "071"){
		   $banco= 0;
        }
     
        
		$ret = array();
		$sql = "SELECT *
            FROM hom_tarifas_tc
            WHERE medio_pago = '".trim($fpa)."'
                AND cod_banco= ".$banco."
                AND fuente = '".$fuente."'";
				
		print_r($sql);		
    
		$res = mysql_query($sql, $conex);
		
		if($row = mysql_fetch_assoc($res)){
			$ret = $row;
		}
		
		return $ret;
	}
    
    function estructura (){
        $date1=$_GET["date1"];
        $date2=$_GET["date2"];
    $datos = list_cacar($date1,$date2);
    $legacy = array();
    foreach($datos as $value){
        switch($value['carfue']){
           /* case 30:
                $reg = array(
                'fechadoc'=> date('Ymd'),
                'clasedoc'=> "CG",
                'sociedad'=> 3000,
                'fechacon'=> date('Ymd'),
                'moneda'=> 'COP',
                'referencia'=> $value['cardoc'],
                'textocab'=> $value['carfac'],
                'clavecon'=> 40,
                'cuenta'=>null,
                'tercero'=> null,
                'importe'=> $value['carsal'],
                'indimp'=> null,
                'calimp'=> null,
                'fechaval'=> date('Ymd'), 
                'asignacion'=> null,
                'texto'=> $value['carfac'],
                'bancopro'=> null,
                'centrocos'=> null,
                'orden'=> null,
                'viapago'=> null,
                'centro'=> null,
                'centroben'=> null,
                'cuentadiver'=> null,
                'bloquedopag'=> null,
                'referencia1'=> $value['carced'],
                'referencia3'=> $value['carres'],
                'tiporete'=> null,
                'indirete'=> null,
                'baserete'=> null
                );
                $reg2 = $reg;
                $reg2['clavecon'] = 50;
                array_push($legacy, $reg, $reg2);
            break;*/
            case 35:
                $codigo_banco="";
                $detalle = detalle_cajabanco(trim($value['carfue']),trim($value['cardoc']));
				$detallecaja = detalle_caja($value['carfue'], $value['cardoc']);
                if(empty($detalle) || empty($detallecaja) ){
                    break;
                }
          
				if($detalle['movdetbco']=="000" || $detalle['movdetbco']==""){
					 switch ($detalle['movdetban']) {
							case "06":
							    $codigo_banco="60";
								break;
							case "07":
								 $codigo_banco="071";
								break;
							default:
                              $codigo_banco=$detalle['movdetban'];
							  break;
						}
					
					 
				}else{
					 $codigo_banco=$detalle['movdetbco'];
				}
                $cuentas = consultar_cuentas(trim($detalle['movdetfpa']), trim($codigo_banco),trim($value['carfue']));
				print_r($cuentas);
				$valorcomprobante=0;
				    foreach ($detallecaja as $d){
				
					//	echo 'Hola';
					$reg = array(
                        'fechadoc'=> date_format(date_create($value['carfec']),"Ymd"),
                        'clasedoc'=> "CG",
                        'sociedad'=> 3000,
                        'fechacon'=> date_format(date_create($value['carfec']),"Ymd"),
                        'moneda'=> 'COP',
                        'referencia'=> $value['cardoc'],
                        'textocab'=> $value['cardoc'],
                        'clavecon'=> $cuentas['clave_contable1'],
                        'cuenta'=>$cuentas['cuenta1'],
                        'tercero'=> null,
                        'vacio'=> null,
                        'importe'=> intval($d['movdetval']),
                        'indimp'=> null,
                        'calimp'=> null,
                        'fechaval'=>  date_format(date_create($value['carfec']),"Ymd"), 
                        'asignacion'=> null,
                        'texto'=> $value['cardoc'],
                        'bancopro'=> null,
                        'centrocos'=> null,
                        'orden'=> null,
                        'viapago'=> null,
                        'centro'=> null,
                        'centroben'=> null,
                        'cuentadiver'=> null,
                        'bloquedopag'=> null,
                        'referencia1'=> ($value['carind']=="E")?sobrescribir_nit(eliminar_letras($value['carced'])):eliminar_letras($value['carced']),
                        'referencia3'=> consultar_empresa(eliminar_letras($value['carced'])),
                        'tiporete'=> null,
                        'indirete'=> null,
                        'baserete'=> null
                        );
						$valorcomprobante+=$d['movdetval'];
						array_push($legacy,$reg);
					}						
                    $reg2 = array(
                        'fechadoc'=> date_format(date_create($value['carfec']),"Ymd"),
                        'clasedoc'=> "CG",
                        'sociedad'=> 3000,
                        'fechacon'=> date_format(date_create($value['carfec']),"Ymd"),
                        'moneda'=> 'COP',
                        'referencia'=> $value['cardoc'],
                        'textocab'=> $value['cardoc'],
                        'clavecon'=> $cuentas['clave_contable2'],
                        'cuenta'=>$cuentas['cuenta2'],
                        'tercero'=> null,
                        'vacio'=> null,
                        'importe'=> intval($valorcomprobante),
                        'indimp'=> null,
                        'calimp'=> null,
                        'fechaval'=> date_format(date_create($value['carfec']),"Ymd"), 
                        'asignacion'=> null,
                        'texto'=> $value['cardoc'],
                        'bancopro'=> null,
                        'centrocos'=> null,
                        'orden'=> null,
                        'viapago'=> null,
                        'centro'=> null,
                        'centroben'=> null,
                        'cuentadiver'=> $cuentas['cuenta2'],
                        'bloquedopag'=> null,
                        'referencia1'=>($value['carind']=="E")?sobrescribir_nit(eliminar_letras($value['carced'])):eliminar_letras($value['carced']),
                        'referencia3'=> consultar_empresa(eliminar_letras($value['carced'])),
                        'tiporete'=> null,
                        'indirete'=> null,
                        'baserete'=> null
                        );
                        array_push($legacy,$reg2);
                        
             break;
             case 38:
              
            /*$reg = array(
                'fechadoc'=> date('Ymd'),
                'clasedoc'=> "CG",
                'sociedad'=> 3000,
                'fechacon'=> date('Ymd'),
                'moneda'=> 'COP',
                'referencia'=> $value['cardoc'],
                'textocab'=> $value['cardoc'],
                'clavecon'=> 01,
                'cuenta'=>null,
                'tercero'=> null,
                'importe'=> $value['carsal'],
                'indimp'=> null,
                'calimp'=> null,
                'fechaval'=> date('Ymd'), 
                'asignacion'=> null,
                'texto'=> $value['cardoc'],
                'bancopro'=> null,
                'centrocos'=> null,
                'orden'=> null,
                'viapago'=> null,
                'centro'=> null,
                'centroben'=> null,
                'cuentadiver'=> null,
                'bloquedopag'=> null,
                'referencia1'=> $value['carced'],
                'referencia3'=> $value['carres'],
                'tiporete'=> null,
                'indirete'=> null,
                'baserete'=> null
                );
                $reg2 = $reg;
                $reg2['clavecon'] = 11;
                array_push($legacy, $reg, $reg2);*/
            break;
    
    
        }
       
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
        $datos = estructura($conexion);
        $datos = utf8_converter( $datos);
        $reg = count($datos);
        $sep = 500000;
        $div = ceil($reg/$sep);
        $array_div = particion_array($datos, $div);
        //print_r($array_div);
        foreach($array_div as $key =>$item1){
            $nombre_fichero='Legacy_CJ_'.$fecha_actual.'_'.'parte'.($key+1).'.txt';
            $fpV0 = fopen($nombre_fichero, 'w');
                    
            foreach($item1 as $item){
            fputcsv($fpV0, $item, "\t");  
            }
            send_file_to_ftp_server($nombre_fichero);
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
 

 

   

  
   
