<?php
$consultaAjax = '';
include_once("movhos/otros.php");
include_once("conex.php");
include_once("root/comun.php");
global $conex_o;
global $conex;
global $bd;


function consultar_empresa($nit,$conex_o){
  $datosemp=[];
  conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
	$q= "select * from INEMP where  empcod=empnit and empnit=".$nit;
	$r= odbc_do($conex_o, $q);
	while($a =odbc_fetch_array($r)){
	    $datosemp=$a;
		break;
	}
    if(count($datosemp)>0 ){
		echo json_encode($datosemp);
    }else{
    echo json_encode(array(
        'error'=>'empresa no existe  o no configurada.'
    ));
   }										
}
function consultar_estadofactura($factura,$conex_o){
  $estado='';
  conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
	$q= "select * from caenc where encfue=20 and encdoc=".$factura;
	$r= odbc_do($conex_o, $q);
	while($a =odbc_fetch_array($r)){
       $estado=$a['encest'];
		break;
	}
    switch ($estado) {
        case 'AP':
           return "AP";
            break;
        case 'RD':
           return "La factura/nota".' '.$factura.' '. "No esta en estado para relacionarse";
            break;
        case 'GL':
           return "La factura/nota".' '.$factura.' '."No esta en estado para relacionarse en envio";
            break;   
        case 'EV':
           return "la factura".'' .$factura.' '."esta enviada en otra relacion de envio.";
            break;       
        default:
            return "El estado".' '.$estado.' '."No ha sido contemplado";
            break;
    }     
}
function validar_responsable($nit_responsable,$nit_digitado){
    $caracteres=strlen($nit_digitado);
    $nit_responsable=substr($nit_responsable, 0,$caracteres);
    if($nit_responsable==$nit_digitado){
        return 1;
    }else{
        return 0;
    }
}
function consultar_factura($factura,$conex_o,$nit_digitado)
{
    $datosfactura;
    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
	$q= "select * from cacar where  carfue=20 and cardoc=".$factura;
	$r= odbc_do($conex_o, $q);
	while($a =odbc_fetch_array($r)){
        $datosfactura=$a;
		break;
	}
    if($datosfactura['carind']=="E"){
       $nit_valido=validar_responsable($datosfactura['carced'],$nit_digitado);
       if($nit_valido==1){
           return $datosfactura;
       }else{
           return 'la factura'.' '.$factura.' '."No corresponde al NIT ingresado";
       }
       
    }else{
        return "La factura".' '.$factura.' '."Pertenece a un particular"; 
    }
}
function consulta_carta( $conex_o,$documento)
{
    $datoscarta;
    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
	$q= "select count(*)as cantidad,sum(b.carsal) as valor,c.estnom as estado,
    d.empnom as empresa
    from caenvdet  a, cacar b,caest c,inemp d where a.envdetfan=b.carfue
    and a.envdetdan=b.cardoc and a.envdetfue=80
    and a.envdetest in('RD','EV') and a.envdetest=c.estcod
     and a.envdetnit=d.empcod and a.envdetdoc=".$documento."
    group by 3,4";
   
	$r= odbc_do($conex_o, $q);
	while($a =odbc_fetch_array($r)){
        $datoscarta=$a;
		break;
	}
    return $datoscarta;
}
function consulta_cartadet( $conex_o,$documento)
{
    $datoscarta=[];
    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
	$q= "select b.cardoc,b.carsal,b.carhis,b.carpac,a.envdetest 
    from caenvdet  a, cacar b where a.envdetfan=b.carfue
    and a.envdetdan=b.cardoc and a.envdetfue=80
    and a.envdetest in('RD','EV') and a.envdetdoc=".$documento;
   
	$r= odbc_do($conex_o, $q);
	while($a =odbc_fetch_array($r)){
        $datoscarta[]=$a;
	}
    return $datoscarta;
}
function consultar_consecutivo_enc($conex_o)
{
    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
	$q= "select fuesec from cafue where fuecod='80'and fuecco='1680'";
   
	$r= odbc_do($conex_o, $q);
	while($a =odbc_fetch_array($r)){
        return $a['fuesec'];
	}
   
}
function consultar_consecutivo2($conex_o,$nit)
{
    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
	$q= "select cseact from cacse where csefue='80'and csecod='$nit'";
	$r= odbc_do($conex_o, $q);
	while($a =odbc_fetch_array($r)){
        return $a['cseact'];
	}
   
}
 function sumar($datos)
{
    $suma=[];
    foreach ($datos as $key => $dato) {
        $suma[$key]=$dato[1];
    }
    return array_sum($suma);	
}
function actualizarestadofactura($datos)
{
    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
    foreach ($datos as $key => $dato) {
       $q="update caenc set encest='EV' where encfue=20 and encdoc={$dato[0]}";
       $r= odbc_do($conex_o, $q);
    }
    	return 'actualizo';
}
function  guardar_enc($conex_o,$datos,$nit)
{
    $n_carta=consultar_consecutivo_enc($conex_o);
    $n_consecutivo=consultar_consecutivo2($conex_o,$nit);
    $n_registros=count($datos);
    $valor=sumar($datos);
    $periodo=date("Ym");
    $fecha=date("Y/m/d");
    $fechahora= date('Y-m-d H:m:s');
    $n_cartasig=(intval($n_carta)+1);
    $n_consecutivosig=(intval($n_consecutivo)+1);
    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
	$q= "INSERT INTO caenvenc(envencfue, envencdoc, envencnit,envenccse,envencper,envencfec,envencfra,envencest,envencuad,envencfen,envencden,envencreg,envencvto,envencanu,envencfad,envencumo,envencfmo) 
                       VALUES (80, {$n_carta},{$nit},{$n_consecutivo},{$periodo},'{$fecha}','{$fecha}','EV','carxnit',80,{$n_carta},{$n_registros},{$valor},0,'{$fechahora}','carxnit','{$fechahora}')";
   $r= odbc_do($conex_o, $q);
   $q= "UPDATE cafue  SET fuesec={$n_cartasig}    WHERE fuecod='80' and fuecco='1680'";
   $r= odbc_do($conex_o, $q);
   $q= "UPDATE cacse SET cseact={$n_consecutivosig}   WHERE csefue='80' and csecod='{$nit}'";
   $r= odbc_do($conex_o, $q);
   $data=[$n_carta,$n_consecutivo,$nit];
   return $data;
	
}
 function guardar_detalle($datos,$encabezado)
{
    list($n_carta, $consecutivo, $nit) = $encabezado;
    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
    $contador=($key+1);
    foreach ($datos as $key => $dato) {
      $q="INSERT INTO caenvdet(envdetfue, envdetdoc, envdetnit,envdetcse,envdetsec,envdetfan,envdetdan,envdetcco,envdettse,envdetval,envdetest) 
             VALUES (80, {$n_carta},{$nit},{$consecutivo},{$contador},20,'{$dato[0]}',9915,'P',{$dato[1]},'EV')";      
       $r= odbc_do($conex_o, $q);  
       insertar_movimiento($dato[0],$n_carta);
    }
    actualizarestadofactura($datos);
    actualizar_cenco($n_carta,$datos);

}
function consultarcartaadicion($carta)
{
    $datosemp;
    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
    $q="select a.envencdoc as carta,a.envencnit as nit, b.empnom as empresa,a.envencest as estado
    from caenvenc as a,inemp as b 
    where a.envencfue='80' and a.envencnit=b.empnit and b.empnit=b.empcod and a.envencdoc=".$carta;
    $r= odbc_do($conex_o, $q);
    while($a =odbc_fetch_array($r)){
	    $datosemp=$a;
		break;
	}
   if(count($datosemp)>0  && $datosemp['estado']=='EV'){
        return json_encode($datosemp);
    }elseif($datosemp['estado']!='EV' && count($datosemp)>0){
        return json_encode(array(
            'error'=>'Carta No se puede modificar, esta en estado'.' '.$datosemp['estado']
        ));
       
    }else{
        return json_encode(array(
            'error'=>'Carta No existe o el Nit no esta registrado correctamente'
        ));
    }
}
function consultarencabezado($carta)
{
    $encabezado;
    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
    $q="select envencdoc as carta , envencnit as nit,envenccse as consecutivo 
    ,envencreg as registros,envencvto as valor 
    from caenvenc 
    where envencfue='80' and envencdoc=".$carta;
    $r= odbc_do($conex_o, $q);
    while($a =odbc_fetch_array($r)){
	    $encabezado=$a;
		break;
	}
    return $encabezado;

}
function guardar_detalleadicion($datos,$encabezado)
{
    $datos_nuevos=intval($encabezado['registros'])+count($datos);
    $valortotal=sumar($datos)+intval($encabezado['valor']);

    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
    foreach ($datos as $key => $dato) {
      $contador=(intval($encabezado['registros'])+$key+1);
      $q="INSERT INTO caenvdet(envdetfue, envdetdoc, envdetnit,envdetcse,envdetsec,envdetfan,envdetdan,envdetcco,envdettse,envdetval,envdetest) 
             VALUES (80, {$encabezado['carta']},{$encabezado['nit']},{$encabezado['consecutivo']},{$contador},20,'{$dato[0]}',9915,'P',{$dato[1]},'EV')";      
     
      $r= odbc_do($conex_o, $q);
      $fechahora= date('Y-m-d H:m:s');
       $q= "UPDATE caenvenc SET envencreg={$datos_nuevos} ,envencvto={$valortotal},envencfmo='{$fechahora}'   WHERE envencfue='80' and envencdoc='{$encabezado['carta']}'";    

     $r= odbc_do($conex_o, $q);  
     insertar_movimiento($dato[0],$encabezado['carta']);
     
    }
    actualizarestadofactura($datos); 
    actualizar_cenco($encabezado['carta'],$datos);

}
function actualizar_cenco($carta,$facturas)
{
    $carta=$carta;
    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
    foreach ($facturas as $key => $factura) {
        $sql="update ahdocact set docactenv='{$carta}' where docactarc='CENCO' and docactcod='20-{$factura[0]}'";
       $r= odbc_do($conex_o, $sql);
    }
    	return 'actualizo';
}

function insertar_movimiento($factura,$carta)
{
    $movimiento;
    conexionOdbc($conexion,'movhos',$conex_o, 'facturacion');
    $q="select * from caestmov where estmovfue=20 and estmovedo='AP' and estmovdoc=".$factura;
    $r= odbc_do($conex_o, $q);
    while($a =odbc_fetch_array($r)){
	    $movimiento=$a;
		break;
	}
    $periodo=strval(date('Ym'));
    $modificacion=strval(date('Y-m-d H:i:s'));
    $q= "INSERT INTO caestmov(estmovfue, estmovdoc, estmovper,estmovsec,estmovind,estmovedo,estmovfmo,estmovdmo,estmovemo,estmovusu,estmovfec) 
                       VALUES ({$movimiento['estmovfue']}, {$movimiento['estmovdoc']},'{$periodo}',2,'{$movimiento['estmovind']}','EV',80,{$carta},'EV','{$movimiento['estmovusu']}','{$modificacion}')";
   $r= odbc_do($conex_o, $q);
    return 'inserto';
    
}


if ($_POST) {
    if (isset($_POST['accion'])) {

        $accion = $_POST['accion'];
		$nit = $_POST['nit'];
        $factura = $_POST['factura'];
        if ($accion) {
            switch ($accion) {
                case 'uno':
                    {
						consultar_empresa($nit,$conex_o);
                    }
                    break;

                case 'dos':
                    {
                       
                        $estado =consultar_estadofactura($factura,$conex_o);
                        if($estado=="AP"){
                             $datosfactura=consultar_factura($factura,$conex_o,$nit);
                             if(is_array($datosfactura)){
                                 echo json_encode($datosfactura);
                             }else{
                                 echo json_encode(array("error"=>$datosfactura));
                             }
                        }else{
                            echo json_encode(array("error"=>$estado));
                        }
                        

                    }
                    break;

                    case 'tres':
                        {
                          $datos=$_POST['datos'];
                          $encabezado=guardar_enc($conex_o,$datos,$nit);
                          guardar_detalle($datos,$encabezado);
                          return print_r(json_encode($encabezado[0]));
                        }
                        break;    
                    case 'cuatro':
                         {
                              $codigo=$_POST['codigo'];
                              $datoscarta=consulta_carta( $conex_o,$codigo);
                              if(is_array($datoscarta)){
                                echo json_encode($datoscarta);
                            }else{
                                echo json_encode(array("error"=>'Carta no existe'));
                            }
                             
                            }
                            break;    
                    case 'cinco':
                                {
                                     $codigo=$_POST['codigo'];
                                     $datoscarta=consulta_cartadet( $conex_o,$codigo);
                                     if(is_array($datoscarta)){
                                       echo json_encode($datoscarta);
                                   }else{
                                       echo json_encode(array("error"=>'Carta no existe'));
                                   }
                                    
                                   }
                                   break;    
                    case 'seis':
                                    {
                                      $carta=$_POST['carta'];
                                      print_r(consultarcartaadicion($carta));
                                        
                                     }
                                       break;  
                    case 'siete':
                                        {
                                          $carta=$_POST['carta'];
                                          $datos=$_POST['datos'];
                                          $encabezado=consultarencabezado($carta);
                                          guardar_detalleadicion($datos,$encabezado);
                                            
                                         }
                                           break; 
                     case 'ocho':
                                            {
                                             
                                              insertar_movimiento(50,20);
                                                
                                             }
                                               break;                                                             
                default :
                {
                    header("HTTP/1.1 500 Internal Server Error");
                    break;
                }
            }
        } else {
            header("HTTP/1.1 500 Internal Server Error");
        }

    } else {
        header("HTTP/1.1 500 Internal Server Error");
    }
}
