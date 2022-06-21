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
		$reg3['cuenta'] = 2365750502;
		$reg4['clavecon'] = 50;
		$reg4['cuenta'] = 1355150202;
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
   function consultar_causal($nc)
 {
      conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
	 $q= "select enccau 
        from  caenc
        where encfue='27' and  encdoc='$nc'";	
   
        $res= odbc_do($conex_o, $q);
  
        while($arr =odbc_fetch_array($res))
        {
            return $arr['enccau'];
        }
  
 
  
 }
  function consulta_unidad( $conex, $cco ){
	
	
	$sql = "SELECT Cxfufu
			  FROM centrocostoXunidadfuncional
			 WHERE Cxfccc like  '%".$cco."%' limit 1";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $row = mysql_fetch_array( $res ) ){
		return $row['Cxfufu'];
	}
	


}
 
 function consultar_cuenta_unidad($causal,$unidad,$conex)
 {
     $sql="";
		print_r($unidad);
			
		switch ($unidad) {
			case 'CONSULTA EXTERNA':
				$sql="SELECT Ncxcon FROM ncXunidadfuncional WHERE Ncxcau = '$causal' AND Ncxfue='27'";
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );				
				while( $row = mysql_fetch_array( $res ) ){
					return $row['Ncxcon'];
				}
				break;
			case 'HOSPITALIZACION':
				$sql="SELECT Ncxhos FROM ncXunidadfuncional WHERE Ncxcau = '$causal' AND Ncxfue='27'";
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );				
				while( $row = mysql_fetch_array( $res ) ){
					return $row['Ncxhos'];
				}
				break;
			case 'CIRUGIA':
			    $sql="SELECT Ncxcir FROM ncXunidadfuncional WHERE Ncxcau = '$causal' AND Ncxfue='27'";
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );				
				while( $row = mysql_fetch_array( $res ) ){
					return $row['Ncxcir'];
				}
				break;
			case 'URGENCIAS':
			     $sql="SELECT Ncxurg FROM ncXunidadfuncional WHERE Ncxcau = '$causal' AND Ncxfue='27'";
				 $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );				
				while( $row = mysql_fetch_array( $res ) ){
					return $row['Ncxurg'];
				}
				break;
				case 'FARMACIA':
			     $sql="SELECT Ncxfar FROM ncXunidadfuncional WHERE Ncxcau = '$causal' AND Ncxfue='27'";
				 $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );				
				while( $row = mysql_fetch_array( $res ) ){
					return $row['Ncxfar'];
				}
				break;	
				case 'APOYO DIAGNOSTICO':
			     $sql="SELECT Ncxapd FROM ncXunidadfuncional WHERE Ncxcau = '$causal' AND Ncxfue='27'";
				 $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );				
				while( $row = mysql_fetch_array( $res ) ){
					return $row['Ncxapd'];
				}
				break;	
			default:
				return 0;
				break;	
		}	
					
			
  
 }
 
 
   function cuenta_nota_credito($fuente,$documento,$valor)
 {
      conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
		$n="$factura";
    $q= "select movdetcco 
        from  famovdet
        where  movdetfue='$fuente' and movdetdoc ='$documento' and movdetval={$valor} ";	
     
        $res= odbc_do($conex_o, $q);
  
        while($arr =odbc_fetch_array($res))
        {
			$sql="SELECT Cxfcnc FROM centrocostoXunidadfuncional WHERE Cxfccc LIKE '%{$arr['movdetcco']}%' AND Cxffue='{$fuente}'";
		//	$sql = "SELECT Axnsap  from asignacion_nit_clientes    WHERE Axnnit='".$nit."'";
			 print_r($sql);
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			
			while( $row = mysql_fetch_array( $res ) ){
				return $row['Cxfcnc'];
			}
		  
        }
  
 
  
 }
 
 
 function  list_notas($valor,$wmovhos){
    conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
   
  
  $q= "select * from famovdet where movdetfue =".$valor['carfue']." and movdetdoc=".$valor['cardoc']." ";	
	
   //  print_r($q);	
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
                       'fechadoc'=> date_format(date_create($valor['carfec']),"Ymd"),
                        'clasedoc'=> "DR",
                        'sociedad'=> 3000,
                        'fechacon'=> date_format(date_create($valor['carfec']),"Ymd"),
                        'moneda'=> 'COP',
                        'referencia'=> 'NC'.$valor['cardoc'],
                        'textocab'=> $valor['cardoc'],
                        'clavecon'=> 40,
                        'cuenta'=>consultar_cuenta_unidad(consultar_causal($valor['cardoc']),consulta_unidad($conex,$arr['movdetcco']),$conex),
                        'tercero'=> null,
                        'vacio'=> null,
                        'importe'=> intval($arr['movdetval']),
                        'indimp'=> null,
                        'calimp'=> null,
                        'fechaval'=> date_format(date_create($valor['carfec']),"Ymd"), 
                        'asignacion'=>codigo_sap_nit( $conex,consultar_nit_convenio($valor['carced'],$valor['carind'])),
                        'texto'=> $valor['cardoc'],
                        'bancopro'=> null,
                        'centrocos'=> homologacionCco( $conex, $wmovhos,$arr['movdetcco']),
                        'orden'=> null,
                        'viapago'=> null,
                        'centro'=> null,
                        'centroben'=> null,
                        'cuentadiver'=> null,
                        'bloquedopag'=> null,
                        'referencia1'=>consultar_nit_convenio($valor['carced'],$valor['carind']),
                        'referencia3'=>consultar_empresa(consultar_nit_convenio($valor['carced'],$valor['carind'])),
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
	
	 function list_cacar($date1,$date2){

        conexionOdbc($conexion,'movhos', $conex_o, 'facturacion');
        //$fecha_actual = date('Y/m/d');
        $fecha_actual = date('Y/m/d', strtotime(date('Y/m/d')."- 17 day"));
        $año = date("Y", strtotime($date1));
        $mes= date("m", strtotime($date1));
		$q= "select *
             from cacar 
             where carano = '".$año."' and carmes = '".$mes."' and carfue in (27) and carfec >= '".$date1."' and carfec <= '".$date2."'  and caranu = 0 ";
		/* $q= "select *
             from cacar 
             where carano = '2021' and carmes = '11' and carfue in (27) and caranu = 0 and cardoc in(2394,2513,2533,2534,2540,2551,2554,2562,2563,2564,2565,2566,2567,2576,
			 2577,2582,2585,2586,2602,2603,2609,2610,2612,2613,2617,2629,2648,2649) ";	 */
			 
        
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
 function cuentasap_empresa( $conex,$convenio ){
	
	
	$sql = "SELECT Csccsa  from CuentasSapXconvenio   WHERE Csccco='".$convenio."'";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $row = mysql_fetch_array( $res ) ){
		return $row['Csccsa'];
	}
	


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

    
    function estructura (){
        $date1=$_GET["date1"];
        $date2=$_GET["date2"];
    $datos = list_cacar($date1,$date2);
	$wmovhos 	= consultarAliasPorAplicacion( $conex, '01', 'movhos' );   
    $legacy = array();
    foreach($datos as $value){
	   $detallesnotas = list_notas($value,$wmovhos);
          foreach ($detallesnotas as $detallenotas) {
         
			array_push($legacy, $detallenotas);
          }
	
       $reg = array(
                        'fechadoc'=> date_format(date_create($value['carfec']),"Ymd"),
                        'clasedoc'=> "DR",
                        'sociedad'=> 3000,
                        'fechacon'=> date_format(date_create($value['carfec']),"Ymd"),
                        'moneda'=> 'COP',
                        'referencia'=> 'NC'.$value['cardoc'],
                        'textocab'=> $value['cardoc'],
                        'clavecon'=> 11,
                        'cuenta'=>codigo_sap_nit( $conex,consultar_nit_convenio($value['carced'],$value['carind'])),
                        'tercero'=> null,
                        'vacio'=> null,
                        'importe'=> intval($value['carval']),
                        'indimp'=> null,
                        'calimp'=> null,
                        'fechaval'=> date_format(date_create($value['carfec']),"Ymd"), 
                        'asignacion'=>codigo_sap_nit( $conex,consultar_nit_convenio($value['carced'],$value['carind'])),
                        'texto'=> $value['cardoc'],
                        'bancopro'=> null,
                        'centrocos'=> null,
                        'orden'=> null,
                        'viapago'=> null,
                        'centro'=> null,
                        'centroben'=> null,
                        'cuentadiver'=> cuentasap_empresa( $conex,$value['carced']),
                        'bloquedopag'=> null,
                        'referencia1'=>eliminar_letras($value['carced']),
                        'referencia3'=>consultar_empresa(consultar_nit_convenio($value['carced'],$value['carind'])),
                        'tiporete'=> null,
                        'indirete'=> null,
                        'baserete'=> null
                        );
                      	if($value['carind']==trim("E")){
							$calcular_retencion_factura=calcular_retencion_factura($reg,$value['carfue']);
							
							$reg3=$calcular_retencion_factura[0];
							$reg4=$calcular_retencion_factura[1];
						  
						 
							  array_push($legacy,$reg,$reg3,$reg4);
				}else{
				
				  array_push($legacy, $reg);
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

 
  

       // print_r();    
	//	print_r(cuenta_nota_credito("27","26","144450"));
       $datos = estructura($conexion);
		print_r($datos);
        $datos = utf8_converter( $datos);
        $reg = count($datos);
        $sep = 500000;
        $div = ceil($reg/$sep);
        $array_div = particion_array($datos, $div);
        //print_r($array_div);
        foreach($array_div as $key =>$item1){
            $nombre_fichero='Legacy_NC_'.$fecha_actual.'_'.'parte'.($key+1).'.txt';
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
  
  
  


 
 ?>
 

 

   

  
   
