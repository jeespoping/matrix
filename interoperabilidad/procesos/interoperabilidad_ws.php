<?php

/************************************************************************************************************
* Programa		:	
* Fecha		:	
* Por			:	
* Descripcion	:
* Condiciones  :
*********************************************************************************************************

Actualizaciones:

==================================================================================================================
Ejemplo de mensaje de recibo
==================================================================================================================
<mensajeXml>
                <notificacionCambioEstadoExterno xmlns="">
                    <encabezado>
                        <de>DINAMICAIPS</de>
                        <para>Medisyn3.0-Portoazul</para>
                        <fechaEnvio>2020-12-11T09:53:56-05:00</fechaEnvio>
                        <fechaOcurrenciaEvento>2020-12-11T09:47:55-05:00</fechaOcurrenciaEvento>
                        <nroOrdenDinamica>6151115</nroOrdenDinamica>
                        <nroRemisionExterno>0000000000371145</nroRemisionExterno>
                        <codigoEstado>EP</codigoEstado>
                        <descripEstado>ENPROCESO</descripEstado>
                        <observaciones/>
                        <codigoSedeExterno>7338</codigoSedeExterno>
                    </encabezado>
                    <detalle>
                        <codServExterno>17-90-360-05</codServExterno>
                        <consecutivoPrestExterno>2</consecutivoPrestExterno>
                        <descrServExterno>IONOGRAMA (CLORO, SODIO, Y POTASIO)</descrServExterno>
                    </detalle>
                    <responsable/>
                    <paciente>
                        <kdni>CC</kdni>
                        <dni>xxxxxxxxxx</dni>
                        <apellidoPaterno>NIGRINIS</apellidoPaterno>
                        <nombre>CLAUDIA MARCELA</nombre>
                        <apellidoMaterno>LINERO</apellidoMaterno>
                        <fNacimiento>1992-09-11T00:00:00-05:00</fNacimiento>
                        <sexo>FEMENINO</sexo>
                        <ubicacion>
                            <direccion>NA </direccion>
                        </ubicacion>
                        <telefono>NA</telefono>
                        <telefono1>NA</telefono1>
                        <numeroHistoria>1082958648</numeroHistoria>
                        <raza>MES</raza>
                    </paciente>
                </notificacionCambioEstadoExterno>
            </mensajeXml>


Ejemplo de mesnsaje de recibo:
==================================================================================================================
<?xml version="1.0" encoding="utf-16"?>
<Orden xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<encabezado>
    
    codigoRemision>366333</codigoRemision>
    <fechaEnvio>2020/11/20 18:01:09</fechaEnvio>
    <isActivada>Si</isActivada>
    <nitCliente>900248882-1</nitCliente>
    <procedencia>MEDISYN3.0-Portoazul</procedencia>
    <tipoServicio>4</tipoServicio>
    <ubicacionAtencion>24</ubicacionAtencion>
    <codDepartamento>08</codDepartamento>
    <clasificacion>H</clasificacion>
    <institucionRemite>17-18</institucionRemite>
</encabezado>
<detalleOrden>
    <codigoServicio>17-90-221-02</codigoServicio>
    <cantidad>1</cantidad>
    <consecutivo>1</consecutivo>
</detalleOrden>
<detalleOrden>
    <codigoServicio>17-90-490-03</codigoServicio>
    <cantidad>1</cantidad>
    <consecutivo>2</consecutivo>
</detalleOrden>
<detalleOrden>
    <codigoServicio>17-90-691-06</codigoServicio>
    <cantidad>1</cantidad>
    <consecutivo>3</consecutivo>
</detalleOrden>
<paciente>
    <kdni>6</kdni>
    <dni>10018868071</dni>
    <apellidoPaterno>DE LA HOZ</apellidoPaterno>
    <nombre>HIJO(A) 1</nombre>
    <apellidoMaterno>TORRES</apellidoMaterno>
    <fNacimiento>2020/11/20 16:39:00</fNacimiento>
    <sexo>F</sexo>
    <ubicacion>
    <direccion>CRA 15 # 7 - 55</direccion>
    </ubicacion>
    <telefono>3223056600</telefono>
    <numeroHistoria>167297</numeroHistoria>
</paciente>
<medico>
    <TipoId>16</TipoId>
    <identificacion>9002488821</identificacion>
    <nombre>MEDICO  INSTITUCIONAL PEDIATRA</nombre>
    <regMedico>1617</regMedico>
    <especialidad>11</especialidad>
</medico>
<parametros>
    <hijos>
    <nombre>nombreEmpresa</nombre>
    <valor>SALUD TOTAL ENTIDAD PROMOTORA DE SALUD R</valor>
    </hijos>
</parametros>
</Orden>



**********************************************************************************************************/
$consultaAjax = '';

include_once("conex.php");
include_once("root/comun.php");
include_once("./../../interoperabilidad/procesos/funcionesGeneralesEnvioHL7.php");

class medicoDTO{
    
};

function cambiarEstadoExamen( $conex, $wemp_pmla, $tipoOrden, $nroOrden, $item, $estado, $fecha, $hora, $justificacion, $historia, $ingreso ){
    
    $respuesta = array('message'=>'', 'result'=>array(), 'status'=>'' );
    
    $whce 	 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce " );
    $wmovhos 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos " );
    
    $estadoPorTipoOrden = consultarAliasPorAplicacion( $conex, $wemp_pmla, "permitirCambiarEstadoInteroperabilidadPorTipoOrden" );
        
    $estadoPorTipoOrden = explode( "-", $estadoPorTipoOrden );
    
    $permitirCambiarEstado = in_array( $tipoOrden, $estadoPorTipoOrden ) ? false : true;
    
    $val = true;
    
    //Buscando los estado según el estandar HL7
    $sql = "SELECT *
            FROM ".$wmovhos."_000257
            WHERE Esthl7 = '".$estado."'
            AND Estest = 'on'";
    
    $resEstado = mysql_query($sql, $conex);
    
    if( $resEstado ){
        
        if( $row = mysql_fetch_array($resEstado) ){
            
            //Valido si la orden existe
            $sql = "SELECT *
                    FROM ".$whce."_000028
                    WHERE Dettor = '".$tipoOrden."'
                    AND Detnro = '".$nroOrden."'
                    AND Detite = '".$item."'";
            
            $resExiste = mysql_query( $sql, $conex );
            $num = mysql_num_rows( $resExiste );
            
            if( $num > 0 ){
                
                $rowsOrdenClinica = mysql_fetch_array( $resExiste );
                
                $whereFecha = '';
                if( !empty( $fecha ) ){
                    $whereFecha = ",Detfec = '".$fecha."',Dethci = '".$hora."'";
                }
                
                //Si el estado es cancelado, deje el registro como pendiente de por examen cancelado (Detplc)
                $pendienteLecuraCancelado = $rowsOrdenClinica['Detplc'];
                if( $row['Estplp'] == 'on' ){
                    $pendienteLecuraCancelado = 'on';
                }
                
                //Si no permite cambiar Estado, dejo como estaba
                $estadoOrden = $row['Estepc'];
                if( !$permitirCambiarEstado )
                    $estadoOrden = $rowsOrdenClinica['Detesi'];
                
                //Actuzando el estado de la orden
                $sql = "UPDATE ".$wmovhos."_000159
                        SET Detesi = '".$estadoOrden."',
                            Deteex = '".$estado."',
                            Detjoc = '".mysql_escape_string( $justificacion )."',
                            Detfme = '".date( "Y-m-d" )."',
                            Dethme = '".date( "H:i:s" )."',
                            Detcor = '".$row['Esteco']."',
                            Detplc = '".$pendienteLecuraCancelado."'
                            $whereFecha
                        WHERE Dettor = '".$tipoOrden."'
                        AND Detnro = '".$nroOrden."'
                        AND Detite = '".$item."'";
                
                $res = mysql_query( $sql, $conex );
                
                
                //Actuzando el estado de la orden
                $sql = "UPDATE ".$whce."_000028
                        SET Detesi = '".$estadoOrden."',
                            Deteex = '".$estado."',
                            Detjoc = '".mysql_escape_string( $justificacion )."',
                            Detfme = '".date( "Y-m-d" )."',
                            Dethme = '".date( "H:i:s" )."',
                            Detcor = '".$row['Esteco']."',
                            Detplc = '".$pendienteLecuraCancelado."'
                            $whereFecha
                        WHERE Dettor = '".$tipoOrden."'
                        AND Detnro = '".$nroOrden."'
                        AND Detite = '".$item."'";
                
                $res = mysql_query( $sql, $conex );
                
                if( $res ){
                    
                    registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Cambio de estado externo', $estado."-".$row['Estdes']."-".$row['Estdpa'] );
                    registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Cambio de estado ordenes', $row['Estepc'] );
                    
                    if( !empty($justificacion) ){
                        registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Justificacion asignada', $justificacion );
                    }
                    
                    if( !empty( $fecha ) ){
                        registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Cita asignada', $fecha." ".$hora );
                    }
                    
                    $num = mysql_affected_rows();
                    
                    if( $num === false ){
                        $respuesta['message'] 	= 'No se logró actualizar el estudio';
                        endRoutine( $respuesta, 400 );
                        $val = false;
                    }
                    else{
                        
                        if( strtolower( $row['Estple'] ) == 'on' ){
                            
                            $sql = "UPDATE ".$whce."_000027
                                    SET Ordple = 'on'
                                    WHERE Ordtor = '".$tipoOrden."'
                                    AND Ordnro = '".$nroOrden."'
                                    ";
                            
                            $res = mysql_query( $sql, $conex );
                            
                            $num = mysql_affected_rows();
                    
                            if( $num === false ){
                                $respuesta['message'] = 'No se logra actualizar el estado pendiente de lectura de la orden';
                                endRoutine( $respuesta, 400 );
                                $val = false;
                            }
                            else{
                                registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, 0, 'Cambio de estado', 'Orden pendiente de lectura' );
                            }
                        }
                    }
                }
                else{
                    $respuesta['message'] 	= 'No se encontró el estudio a actualizar';
                    endRoutine( $respuesta, 400 );
                    $val = false;
                }
            }
            else{
                $respuesta['message'] 	= 'El estudio de la orden solicitada no se encuentra';
                endRoutine( $respuesta, 400 );
                $val = false;
            }
        }
        else{
            $respuesta['message'] 	= 'Estado de estudio desconocido';
            endRoutine( $respuesta, 400 );
            $val = false;
        }
    }
    else{
        $respuesta['message'] 	= 'Estado de estudio no encontrado';
        endRoutine( $respuesta, 400 );
        $val = false;
    }
    
    return $val;
}

function marcarOrdenesEnviadas( $conex, $whce, $wbasedato, $tor, $nro ){
    
    $sql = "UPDATE ".$whce."_000028 a
            SET Detenv = 'off', 
                Deteex = 'OWS'
            WHERE Dettor = '".$tor."'
            AND Detnro = '".$nro."'
        ";

    $resEnv	= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
        
    $sql = "UPDATE ".$wbasedato."_000159 a
            SET Detenv = 'off', 
                Deteex = 'OWS'
            WHERE Dettor = '".$tor."'
            AND Detnro = '".$nro."'
        ";

    $resEnv	= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
}

function consultarEstudiosPorOrden( $conex, $whce, $wmovhos, $historia, $ingreso ){
    
    $val = [];
    
    $tabla 		= "";
    $c_estado 	= "";
    $campo  	= "";
    
    $sql = "SELECT a.Valtoc, a.Valeoc, a.Valcoc, a.Valtor, a.Valtsw
            FROM ".$wmovhos."_000267 a, ".$whce."_000015 b 
            WHERE a.Valest  = 'on'
            AND a.Valtoc != ''
            AND a.Valeoc != ''
            AND a.Valcoc != ''
            AND b.codigo  = a.Valtor
            AND b.estado  = 'on'
            AND b.tipiws	 = 'on'
            ";
    
    $resOfertas = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    
    while( $rowOferta = mysql_fetch_array( $resOfertas ) ){
        
        $tabla 		= $rowOferta['Valtoc'];	//tabla de cups ofertados
        $c_estado 	= $rowOferta['Valeoc']; //tabla de cups ofertados
        $campo  	= $rowOferta['Valcoc']; //tabla de cups ofertados
        $tipoOrden 	= $rowOferta['Valtor'];	//tabla de cups ofertados
        $tipoServicio = $rowOferta['Valtsw'];	//Tipo de servicio Web service
        
        
        $sql = "SELECT a.Dettor, a.Detnro, a.Detite, a.Detcod, c.Codigo, c.Nombre, b.Codcups, a.id, Detesi, Detlog, Detjus, Deteex, b.Codigo as Codlma, Detusu
                FROM ".$whce."_000027 d, ".$whce."_000028 a, ".$whce."_000047 b, root_000012 c
                WHERE a.Dettor = '".$tipoOrden."'
                AND a.Detcod = b.codigo
                AND a.Detenv = 'on'
                AND a.Detest = 'on'
                AND b.Estado = 'on'
                AND b.Codcups= c.Codigo
                AND d.Ordtor = a.Dettor
                AND d.Ordnro = a.Detnro
                AND d.Ordhis = '".$historia."'
                AND d.Ording = '".$ingreso."'
                UNION
                SELECT a.Dettor, a.Detnro, a.Detite, a.Detcod, c.Codigo, c.Nombre, b.Codcups, a.id, Detesi, Detlog, Detjus, Deteex, b.Codigo as Codlma, Detusu
                FROM ".$whce."_000027 d, ".$whce."_000028 a, ".$whce."_000017 b, root_000012 c
                WHERE a.Dettor = '".$tipoOrden."'
                AND a.Detcod = b.codigo
                AND a.Detest = 'on'
                AND a.Detenv = 'on'
                AND b.Nuevo  = 'on'
                AND b.Estado = 'on'
                AND b.Codcups= c.Codigo
                AND d.Ordtor = a.Dettor
                AND d.Ordnro = a.Detnro
                AND d.Ordhis = '".$historia."'
                AND d.Ording = '".$ingreso."'
                ";
        
        $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
        
        while( $row = mysql_fetch_array( $res ) ){
                        
            //Busco si el cups es ofertado
            $sql = "SELECT *
                    FROM ".$tabla." a
                    WHERE a.".$c_estado." = 'on'
                    AND a.".$campo." = '".$row['Codcups']."'
                    ";
            
            $resOfertas = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
            
            $num = mysql_num_rows( $resOfertas );
            
            //Si está ofertado deja ordenar
            if( $num > 0 ){
                
                $index = $row[ 'Dettor' ]."-".$row[ 'Detnro' ];
                
                if( !isset( $val[ $index ] ) ){
                    $val[ $index ]['medico'] = $row[ 'Detusu' ];
                    $val[ $index ]['items'] = [];
                }
                $val[ $index ]['tipoServicioWS']= $tipoServicio;
                $val[ $index ]['items'][] = [
                                'codigo' 		=> $row[ 'Codcups' ] ,
                                'descripcion' 	=> $row[ 'Nombre' ] ,
                                'item' 			=> $row[ 'Detite' ] ,
                                'estado'		=> $row[ 'Detesi' ] ,
                                'justificacion'	=> $row[ 'Detjus' ] ,
                                'estadoExterno'	=> $row[ 'Deteex' ] ,	//Estado en que se encuentra la cita hl7
                                'medico'		=> $row[ 'Detusu' ] ,	//Estado en que se encuentra la cita hl7
                                
                            ];
            }
            else
            {	
                registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $row['Dettor'], $row['Detnro'], $row['Detite'], 'CUP no ofertado', 'Estudio no ofertado '.$row['Codcups']."-".$row['Nombre']." (".$row['Codlma'].")" );
            }
        }
    }
    
    return $val;
}


function crearXMLPorHistoria( $conex, $wemp_pmla, $his, $ing ){
    
    $wmovhos	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
    $whce		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
    $wcliame	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
    
    $paciente = informacionPaciente( $conex, $wemp_pmla, $his, $ing );
    
    $ordenes 	= consultarEstudiosPorOrden( $conex, $whce, $wmovhos, $his, $ing );

    $codigoDepartamentoWS	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoDepartamentoWS" );
    $codigoInstitucionRemiteWS	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoInstitucionRemiteWS" );

    
    //echo "<pre>"; var_dump($paciente); echo "</pre>";
    //echo "<pre>"; var_dump($ordenes); echo "</pre>";
    //echo "<pre>"; var_dump($medico); echo "</pre>";
    $datos = array();
    foreach( $ordenes as $key => $orden ){
        
        list( $tipoOrden, $nroOrden ) = explode( "-", $key );
        
        $medico = informacionMedico( $conex, $wmovhos, $wemp_pmla, $orden['medico'] );
        
        //Creando el documento xml
        $xw = new XMLWriter();
        
        $xw->openMemory();
        
        //$xw->startDocument("1.0");
        
        //comienzo de orden
        //$xw->startElement("orden");
        //$xw->writeAttributeNS('xmlns','', null,'');
        $xw->startElementNS(null, 'orden', '');
        
        // ================================================
        //comienzo de encabezado
        /*
        <encabezado>
            <codigoRemision>366333</codigoRemision>
            <fechaEnvio>2020/11/20 18:01:09</fechaEnvio>
            <isActivada>Si</isActivada>
            <nitCliente>900248882-1</nitCliente>
            <procedencia>MEDISYN3.0-Portoazul</procedencia>
            <tipoServicio>4</tipoServicio>
            <ubicacionAtencion>24</ubicacionAtencion>
            <codDepartamento>08</codDepartamento>
            <clasificacion>H</clasificacion>
            <institucionRemite>17-18</institucionRemite>
        </encabezado>
        */
        // ================================================
        $xw->startElement("encabezado");
        
        //codigoRemision
        $xw->startElement( "codigoRemision" );
        $xw->text( $key );
        $xw->endElement();
        
        //fechaEnvio
        $xw->startElement( "fechaEnvio" );
        $xw->text( date( "Y/m/d H:i:s" ) );
        $xw->endElement();
        
        //isActivada
        $xw->startElement( "isActivada" );
        $xw->text( "Si" );
        $xw->endElement();
        
        //nitCliente
        $xw->startElement( "nitCliente" );
        $xw->text( "900248882-1" );
        $xw->endElement();
        
        //procedencia
        $xw->startElement( "procedencia" );
        $xw->text( "MATRIX" );
        $xw->endElement();
        
        //tipoServicio
        $xw->startElement( "tipoServicio" );
        $xw->text( $orden['tipoServicioWS'] );
        $xw->endElement();
        
        //ubicacionAtencion
        $xw->startElement( "ubicacionAtencion" );
        $xw->text( $paciente["servicioActual"] );
        $xw->endElement();
        
        //codDepartamento
        $xw->startElement( "codDepartamento" );
        $xw->text( $codigoDepartamentoWS );
        $xw->endElement();
        
        //clasificacion
        $xw->startElement( "clasificacion" );
        $xw->text( "H" );
        $xw->endElement();
        
        //institucionRemite
        $xw->startElement( "institucionRemite" );
        $xw->text($codigoInstitucionRemiteWS );
        $xw->endElement();
        
        //Fin elemento encabezado
        $xw->endElement();
        
        // ================================================
        
        
        // ================================================
        //comienzo de paciente
        // ================================================
        $xw->startElement("paciente");
        
        //Tipo de documento
        $xw->startElement("kdni");
        $xw->text( $paciente['tipoDocumento'] );
        $xw->endElement();
        
        //Número de documento
        $xw->startElement("dni");
        $xw->text( $paciente['nroDocumento'] );
        $xw->endElement();
        
        //Primer apellido o apellido paterno
        $xw->startElement("apellidoPaterno");
        $xw->text( $paciente['apellido1'] );
        $xw->endElement();
        
        //Nombes del paciente
        $xw->startElement("nombre");
        $xw->text( $paciente['nombresCompletos'] );
        $xw->endElement();
        
        //Segundo apellido o apellido materno
        $xw->startElement("apellidoMaterno");
        $xw->text( $paciente['apellido2'] );
        $xw->endElement();
        $orgDate = $paciente['fechaNacimiento'];  
        $newDate = date("Y/m/d H:i:s", strtotime($orgDate));  
        //Fecha de nacimiento del paciente
        $xw->startElement("fNacimiento");
        $xw->text( $newDate );
        $xw->endElement();
        
        //Sexo
        $sexo = $paciente['genero'] == 'M' ? 'MASCULINO' : 'FEMENINO' ;
        $xw->startElement("sexo");
        $xw->text( $sexo );
        $xw->endElement();
        
        //Ubicacion
        $xw->startElement("ubicacion");
        $xw->startElement("direccion");
        $xw->text( "NA" );
        $xw->endElement();
        $xw->endElement();
        
        //Telefono
        $xw->startElement( "telefono" );
        $xw->text( $paciente['telefono'] );
        $xw->endElement();
        
        //Telefono1
        $xw->startElement( "telefono1" );
        $xw->text( "NA" );
        $xw->endElement();
        
        //Historia
        $xw->startElement( "numeroHistoria" );
        $xw->text( $his."-".$ing );
        $xw->endElement();
        
        //Raza
        $xw->startElement( "raza" );
        $xw->text( $paciente['raza'] );
        $xw->endElement();
        
        //TIpoPlan
        $xw->startElement( "tipoPlan" );
        $xw->text( $paciente['tipoPlan']);
        $xw->endElement();
        
        
        //Fin elemento paciente
        $xw->endElement();
        
        // ==================================================================
        
        // ================================================
        //comienzo de medico
        /*
        <medico>
            <TipoId>16</TipoId>
            <identificacion>9002488821</identificacion>
            <nombre>MEDICO  INSTITUCIONAL PEDIATRA</nombre>
            <regMedico>1617</regMedico>
            <especialidad>11</especialidad>
        </medico>
        */
        // ================================================
        
        $xw->startElement("medico");
        
        //TipoId
        $xw->startElement( "TipoId" );
        $xw->text( $medico->tipoDocumento );
        $xw->endElement();
        
        //identificacion
        $xw->startElement( "identificacion" );
        $xw->text( $medico->numeroDocumento );
        $xw->endElement();
        
        //nombre
        $xw->startElement( "nombre" );
        $xw->text( trim( trim( $medico->nombre1." ".$medico->nombre2 )." ".trim( $medico->apellido1." ".$medico->apellido2 ) ) );
        $xw->endElement();
        
        //regMedico
        $xw->startElement( "regMedico" );
        $xw->text( $medico->registroMedico );
        $xw->endElement();
        
        //especialidad
        $xw->startElement( "especialidad" );
        $xw->text( $medico->codigoEspecialidad );
        $xw->endElement();
        
        //Fin de medico
        $xw->endElement();
        // ================================================
        
        // ================================================
        //Detalle
        /*
        <detalle>
            <codServExterno>17-90-360-05</codServExterno>
            <consecutivoPrestExterno>2</consecutivoPrestExterno>
            <descrServExterno>IONOGRAMA (CLORO, SODIO, Y POTASIO)</descrServExterno>
        </detalle>
        */
        // ================================================
        
        
        
        foreach( $orden['items'] as $item )
        {
            $xw->startElement("detalleOrden");
            
            //TipoId
            //	$xw->startElement( "codServExterno" );
            $xw->startElement( "codigoServicio" );
            $xw->text( $item['codigo'] );
            $xw->endElement();
            
            //campo cantidad quemado de momemento
            $xw->startElement( "cantidad" );
            $xw->text( "1" );
            $xw->endElement();
            //$xw->startElement( "consecutivoPrestExterno" );
            $xw->startElement( "consecutivo" );
            $xw->text( $item['item'] );
            $xw->endElement();
            
            
            //
            /*
            $xw->startElement( "descrServExterno" );
            $xw->text( $item['descripcion'] );
            $xw->endElement();
            */

            //Fin de detalleOrden
            $xw->endElement();
        }
        // ================================================
        
        //Fin elemento Orden
        $xw->endElement();
        
        //Cerrando Orden
        $xw->endDocument();
        
        $msgXML = $xw->outputMemory();
        
        $msgXML = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
            <Body>
            <insertarOrden xmlns="http://AyudasDiagnosticasLIB/IntegracionClientes">'
            .
            $msgXML
            .
            '</insertarOrden>
            </Body>
            </Envelope>';
        
        
        registrarMsgLogHl7( $conex, $wmovhos, $his, $ing, $paciente['tipoDocumento'], $paciente['nroDocumento'], 'XML Generado', $tipoOrden, $nroOrden, '', $msgXML );
        registrarDetalleLog( $conex, $wmovhos, $his, $ing, $tipoOrden, $nroOrden, '', 'XML Generado', $msgXML );
        //$resultws = conexionws($url, $msgXML);
        
        //marcarOrdenesEnviadas( $conex, $whce, $wmovhos, $tipoOrden, $nroOrden);
        
        $datos[]= array(
        "msgXML" => $msgXML,
        "tipoOrden" => $tipoOrden,
        "nroOrden" => $nroOrden,
        "wmovhos" => $wmovhos,
        "paciente" => $paciente,
        "whce" => $whce
        );
    }
    return $datos;
    
}


// Establece una conexion con webservice de Dinamica
function conexionws($conex, $wemp_pmla, $estructura){
    
    $result="";
    $url = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'urlWebservice' );
        
    try{
        $soap_do = curl_init(); 
        curl_setopt($soap_do, CURLOPT_URL,            $url );   
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10); 
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        10); 
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($soap_do, CURLOPT_POST,           true ); 
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $estructura); 
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($estructura) ));
        $result = curl_exec($soap_do);
        return $result;
    }catch(Exception $e){
        echo "Error en la conexion ". $e;
    }
    
}


function consultaEstado($conex, $wmovhos, $estado){
$script =" 
            SELECT a.Estepc
                FROM ".$wmovhos."_000257 a
                WHERE
                    Esthl7 = '".$estado."' AND
                    Estest = 'on'
    ";	
    $res= mysql_query( $script, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    $res = mysql_fetch_array($res, MYSQL_ASSOC);
    return $res['Estepc'];
}


function cambiarEstadoExamenWs( $conex, $whce, $wmovhos, $tor, $nro, $tipoRespuesta, $wemp_pmla, $item){
    $estado = "";
    $result = false;
    If($tipoRespuesta == 'estado'){
    
        $estado = 'EP';
    }else{
        $estado = 'FWS';
        
    }
    $estadointerno = consultaEstado($conex, $wmovhos, $estado);
        $sql = "";
        $sql .= "UPDATE ".$whce."_000028 a
            SET
                Detesi = '".$estadointerno."',
                Deteex = '".$estado."',
                Detfme = '".date( "Y-m-d" )."',
                Dethme = '".date( "H:i:s" )."'";
        if($estado == 'FWS'){
            $get = http_build_query(array('nroOrden' => $nro,
                            'wemp_pmla' =>$wemp_pmla,
                            'tOrden' => $tor,
                            'item'=> strval($item))
                            );
            $url = "/matrix/interoperabilidad/procesos/visor_resultados.php". "?" .$get;
                            
        $sql .= ",Deturp = '". $url."'";
        }
        
        $sql .=  "
            WHERE Dettor = '".$tor."'
            AND Detnro = '".$nro."' 
            AND Detite = '".$item."'
        ";

    $resEnv	= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    if (mysql_affected_rows() >= 1){
        $result = true;
    }
    
    
            
    $sql = "";
        $sql .= "UPDATE ".$wmovhos."_000159 a
            SET
                Detesi = '".$estadointerno."',
                Deteex = '".$estado."',
                Detfme = '".date( "Y-m-d" )."',
                Dethme = '".date( "H:i:s" )."'";
        if($estado == 'FWS'){
            $get = http_build_query(array('nroOrden' => $nro,
                            'wemp_pmla' =>$wemp_pmla,
                            'tOrden' => $tor,
                            'item'=> $item)
                            );
            $url = "/matrix/interoperabilidad/procesos/visor_resultados.php". "?" .$get;
                            
        $sql .= ",Deturp = '". $url."'";
        }
        
        $sql .=  "
            WHERE Dettor = '".$tor."'
            AND Detnro = '".$nro."'
            AND Detite = '".$item."'
        ";
    $resEnv	= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    if (mysql_affected_rows() >= 1){
        $result = true;
    }
    
    
    return $result;

}


function insertarOrden($conex, $wemp_pmla, $historia, $ingreso){
    //$conexiondinamica = conexionws($conex, $wemp_pmla);
    
    
        $datos= crearXMLPorHistoria( $conex, $wemp_pmla, $historia, $ingreso );
        if(!empty($datos)){
            foreach($datos as $arrXML){
            
            //$msgXML = simplexml_load_string($arrXML['msgXML']);
            
            
        
                $respuesta = conexionws($conex, $wemp_pmla, $arrXML['msgXML']);
                if(!empty($respuesta)){
                    $DOM = new DOMDocument('1.1', 'utf-8');
                    $DOM->loadXML($respuesta);
                    $res = trim($DOM->textContent);
                    $status =  $res;
                    registrarMsgLogHl7( $conex, $arrXML['wmovhos'], $historia, $ingreso, $arrXML['paciente']['tipoDocumento'], $arrXML['paciente']['nroDocumento'], 'XML enviado con respuesta:'.$status, $arrXML['tipoOrden'], $arrXML['nroOrden'], '',$arrXML['msgXML'] );
                    registrarDetalleLog( $conex, $arrXML['wmovhos'], $historia, $ingreso, $arrXML['tipoOrden'], $arrXML['nroOrden'], '', 'XML enviado con respuesta:'.$status, $arrXML['msgXML'] );
                    if( $status == "S"){
                        marcarOrdenesEnviadas( $conex, $arrXML['whce'], $arrXML['wmovhos'],$arrXML['tipoOrden'], $arrXML['nroOrden']);
                        echo "Envio Exitoso.";
                    }
                }
            }
        }else{
                $wmovhos	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
                
                registrarMsgLogHl7( $conex,$wmovhos, $historia, $ingreso, $arrXML['paciente']['tipoDocumento'], $arrXML['paciente']['nroDocumento'], 'Error en la generacion del XML', $arrXML['tipoOrden'], $arrXML['nroOrden'], '',$arrXML['msgXML'] );
                registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $arrXML['tipoOrden'], $arrXML['nroOrden'], '',  'Error en la generacion del XML', $arrXML['msgXML'] );
                echo "No se pudo generar XML porque el examen no esta ofertado";
        }
    
}


function guardarResultadoWs($conex,$wmovhos, $resultado){
    
    $q = "INSERT INTO ".$wmovhos."_000288
                (Medico, Fecha_data, Hora_data, Rldtio, Rldnuo, Rldite, Rldxml, Rldhis, Rlding, Seguridad)
            VALUES
                ('movhos','".date("Y-m-d")."','".date("H:i:s")."','".$resultado['Rldtio']."','".$resultado['Rldnuo']."','".$resultado['Rldite']."','".utf8_encode($resultado['Rldxml'])."','".$resultado['Rldhis']."','".$resultado['Rlding']."','C-movhos' )";

    $res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

function obtenerPacienteXml($xml, $tipoRespuesta){
    $resultado = array();
    
    if($tipoRespuesta == 'estado'){
        
        $paciente = $xml->notificacionCambioEstadoExterno->paciente;
        list( $tipoOrden, $numOrden  ) =  explode('-',$xml->notificacionCambioEstadoExterno->encabezado->codigoSedeExterno);
        
    }elseif($tipoRespuesta == 'resultado'){
        
        $paciente = $xml->resultado->paciente;
        list( $tipoOrden, $numOrden  ) =  explode('-',$xml->resultado->infoGeneral->numeroOrden);
    }
    
    $infoPaciente= $xml;
    
    return array(
                'paciente'=> $paciente, 
                'orden' => array(
                    'tipoOrden' => $tipoOrden,
                    'numOrden' => $numOrden
                )
    );
}

function analizarxml($valorRespuesta){

        $xml = simplexml_load_string($valorRespuesta);
        if(isset($xml->resultados)){
            return "resultados";
        }if(isset($xml->notificacionCambioEstadoExterno)){
            return "estado";
        }else{
            return "No se pudo identificar";
        }
}

//$server = new SoapServer("dinamicaws.wsdl" , array('cache_wsdl' => WSDL_CACHE_NONE));



function WSRecepcion_De_Resultados($valorRespuesta){
    
    $tipoRespuesta = analizarxml($valorRespuesta);
    

    if((isset($tipoRespuesta)) && !(empty($tipoRespuesta))){
            $wemp_pmla= $GLOBALS['wemp_pmla'];
            
                    if((isset($valorRespuesta)) && !(empty($valorRespuesta))){
                            if((isset($wemp_pmla)) && !(empty($wemp_pmla))){
                                $wmovhos	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
                                $whce		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
                                $wcliame	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
                                $xml = simplexml_load_string($valorRespuesta);
                            
                                    if($tipoRespuesta == "resultados"){
                                                list( $tipoOrden, $nroOrden ) =  explode('-',$xml->resultados->infoGeneral->numeroOrden);
                                                list( $historia, $ingreso  ) =  explode('-',$xml->resultados->paciente->numeroHistoria);
                                                $item = $xml->resultados->resultado->examen->consecutivo;
                                                
                                                $respuesta = array(
                                                                        'Rldtio'=> $tipoOrden,
                                                                        'Rldnuo'=> $nroOrden,
                                                                        'Rldite' => $item,
                                                                        'Rldxml' => utf8_encode($valorRespuesta),
                                                                        'Rldhis' => $historia,
                                                                        'Rlding' => $ingreso
                                                                        );
                                                guardarResultadoWs($conex,$wmovhos, $respuesta);
                                                $result = cambiarEstadoExamenWs( $conex, $whce, $wmovhos, $tipoOrden, $nroOrden, $tipoRespuesta, $wemp_pmla, strval($item));
                                                if($result ){
                                                    $datos = obtenerPacienteXml($xml, $tipoRespuesta );	
                                                    registrarMsgLogHl7( $conex,$wmovhos, $historia, $ingreso, $datos['paciente']->kdni, $datos['paciente']->dni, 'Se ha cambiado el estado  a Finalizado', $datos['orden']['tipoOrden'], $datos['orden']['numOrden'] ,'',$xml->asXML());
                                                    registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso,$datos['orden']['tipoOrden'], $datos['orden']['numOrden'] , '',  'Se ha cambiado el estado a Finalizado', $xml->asXML() );
                                                    
                                                        $resultado = array( "codigo"=> 200,
                                                                        "msgError"=> "",
                                                                        "status"=> "S");
                                                
                                                    return  json_encode($resultado);
                                                
                                                }else{
                                                    $resultado = array( "codigo"=> 201,
                                                                        "msgError"=> "La orden no exite",
                                                                        "status"=> "N");
                                                    
                                                        return  json_encode($resultado);
                                                    
                                                }
                                        
                                    }elseif($tipoRespuesta == "estado"){
                                            list( $tipoOrden, $nroOrden ) =  explode('-',$xml->notificacionCambioEstadoExterno->encabezado->nroRemisionExterno);
                                            $item = $xml->notificacionCambioEstadoExterno->detalle->consecutivoPrestExterno;
                                            $result = cambiarEstadoExamenWs( $conex, $whce, $wmovhos, $tipoOrden, $nroOrden, $tipoRespuesta, $wemp_pmla, $item);
                                            if($result ){
                                                $datos = obtenerPacienteXml($xml, $tipoRespuesta );
                                                    list( $historia, $ingreso  ) =  explode('-',$datos['paciente']->numeroHistoria);	
                                                    registrarMsgLogHl7( $conex,$wmovhos, $historia, $ingreso, $datos['paciente']->kdni, $datos['paciente']->dni, 'Se ha cambiado el estado en proceso',$tipoOrden, $nroOrden ,'',$xml->asXML());
                                                    registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso,$tipoOrden, $nroOrden , '',  'Se ha cambiado el estado en proceso', $xml->asXML() );
                                                
                                                    $resultado = array( "codigo"=> 200,
                                                                        "msgError"=> "",
                                                                        "status"=> "S");
                                                
                                                    return  json_encode($resultado);
                                                
                                            }else{
                                                $resultado = array( "codigo"=> 201,
                                                                    "msgError"=> "La orden no exite",
                                                                    "status"=> "N");
                                                
                                                    return  json_encode($resultado);
                                                
                                            }
                                            
                                    }
                        }
                    }	
                }
}


function consultarOrdenesWS($conex,$wmovhos, $resultado){
    
    $q = "INSERT INTO ".$wmovhos."_000288
                (Medico, Fecha_data, Hora_data, Rldtio, Rldnuo, Rldite, Rldxml, Rldhis, Rlding, Seguridad)
            VALUES
                ('movhos','".date("Y-m-d")."','".date("H:i:s")."','".$resultado['Rldtio']."','".$resultado['Rldnuo']."','".$resultado['Rldite']."','".$resultado['Rldxml']."','".$resultado['Rldhis']."','".$resultado['Rlding']."','C-movhos' )";

    $res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}


function ListOrdenesWs($conex, $wmovhos, $whce, $torden, $historia, $ingreso , $desde, $hasta){
$script =" 
            SELECT  h.dettor,  h.detnro, h.detcod, h.detesi, h.deteex ,c.descripcion, h.fecha_data fecha_orden, h.hora_data hora_orden , rldxml
            FROM ".$whce."_000028 h 
            JOIN ".$whce."_000027 r ON r.ordtor = h.dettor and r.ordnro = h.detnro
            LEFT JOIN ".$wmovhos."_000288 m ON m.Rldnuo = h.Detnro
            JOIN ".$whce."_000047 c ON h.detcod = c.Codigo
            WHERE h.dettor = '".$torden."'  AND r.Ordhis = '".$historia."' AND r.Ording = '".$ingreso."' AND  h.fecha_data BETWEEN '".$desde."' AND '".$hasta."'
            group by h.detnro

    ";	
    
    $res= mysql_query( $script, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    $r=array();
    while ($row = mysql_fetch_assoc($res)) {
    $r[] = $row;
    }
    
    return $r;
}

function infoOrdenNro($conex, $wmovhos, $whce, $nroOrden, $torden, $item){
$script =" 
            SELECT  h.dettor,  h.detnro, h.detcod, r.Ordhis, r.Ording, h.detesi, h.deteex, h.detusu, h.detjus,c.descripcion, h.fecha_data fecha_orden, h.hora_data hora_orden, m.rldxml, m.fecha_data fecha_resultado, m.hora_data hora_resultado   
            FROM ".$whce."_000028 h 
            JOIN ".$whce."_000027 r ON r.ordtor = h.dettor and r.ordnro = h.detnro
            LEFT JOIN ".$wmovhos."_000288 m ON m.Rldnuo = h.Detnro AND h.Detite = m.Rldite
            JOIN ".$whce."_000047 c ON h.detcod = c.Codigo
            WHERE  h.dettor = '".$torden."'  AND  h.detnro = '".$nroOrden."' AND h.Detite = '".$item."'
            group by h.detnro

    ";	
    $res= mysql_query( $script, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    $res = mysql_fetch_assoc($res);
    return $res;
}

function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string ($d)) {
        return utf8_encode($d);
    }
    return $d;
}

if( $_POST ){
        if(isset($_POST['accion'])){
            $accion = $_POST['accion'];
            
            if($accion ){
                switch( $accion ){
                        case 'insertarOrden':{
                                $historia 	= $_POST['historia'];
                                $ingreso 	= $_POST['ingreso'];
                                $wemp_pmla 	= $wemp_pmla;
                                insertarOrden( $conex, $wemp_pmla, $historia, $ingreso);
                        }break;
                        
                        case 'resutaldoWS':{                   
                        
                                //print_r($_POST);
                                
                                global $wemp_pmla;
                                if(isset($_POST['resultXML'])){
                                    $result = WSRecepcion_De_Resultados(utf8_encode($_POST['resultXML']));
                                    header('Content-Type: application/json');
                                    echo $result;
                                }else{
                                    header("HTTP/1.1 500 Internal Server Error");
                                }
                        
                            
                                
                        }break;
                        
                        default :{
                        header("HTTP/1.1 500 Internal Server Error");
                        break;}
                    }
                }else{
                    header("HTTP/1.1 500 Internal Server Error");
                }

        }else{
            header("HTTP/1.1 500 Internal Server Error");
        }
}elseif( $_GET['accion']){
    $accion = $_GET['accion'];

    switch( $accion ){
    
            case 'consultarOrdenes':{
                    //print_r($_GET);
                    $historia 	= $_GET['historia'];
                    $ingreso 	= $_GET['ingreso'];
                    $desde = $_GET['desde'];
                    $hasta = $_GET['hasta'];
                    $torden = $_GET['torden'];
                    $wemp_pmla = $_GET['wemp_pmla'];
                    $wmovhos	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
                    $whce		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
                    $wcliame	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
                    $datos = ListOrdenesWs($conex, $wmovhos, $whce, $torden, $historia, $ingreso , $desde, $hasta);
                    $i = 0;
                    
                    foreach ($datos as $clave => $valor){
                        if(isset($valor['rldxml'] ) && !empty($valor['rldxml'])){
                            $orden = $valor['detnro']; 
                            $get = http_build_query(array('nroOrden' => $orden,
                            'wemp_pmla' =>$wemp_pmla,
                            'tOrden' => $torden));
                            $datos[$clave]['url'] = "http://10.17.2.35/matrix/interoperabilidad/procesos/visor_resultados.php". "?" .$get;
                        }
                        
                    }
                    print_r($datos);
                    
                
            }break;
            case 'consultarOrden':{
                
                 
                    $nroOrden = $_GET['nroOrden'];
                    $torden = $_GET['tOrden'];
                    $wemp_pmla = $_GET['wemp_pmla'];
                    $item = $_GET['item'];
                    $wmovhos	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
                    $whce		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
                    $wcliame	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
                    $datos = infoOrdenNro($conex, $wmovhos, $whce, $nroOrden, $torden, $item);
                    $paciente = informacionPaciente( $conex, $wemp_pmla, $datos['Ordhis'], $datos['Ording'] );
                    $medico = informacionMedicoArray( $conex, $wmovhos, $wemp_pmla, $datos['detusu']);
                    $xml = simplexml_load_string(utf8_decode(utf8_decode(utf8_decode($datos['rldxml']))));
                    $fecha= strval($xml->resultados->infoGeneral->fechaServicio);       
                    
                    header('Content-Type: application/json; charset=utf-8');
                    $str = str_replace(':{}',':""',json_encode($xml->resultados->resultado));
                    $str = str_replace(':{"0":" "}',':""',$str);
                    $str = str_replace(':{"0":"\n"}',':""',$str);
                    

                            echo json_encode(array('paciente' =>$paciente,
                                    'medico' => $medico,
                                    'resultado' => array(
                                            "fechaOrden" => date("Y-m-d H:m:s" ,strtotime($datos['fecha_orden'].$datos['hora_data'])),
                                            "fechaRes"=> date("Y-m-d H:m:s" , strtotime($xml->resultados->resultado->examen->fechaValidacion)),
                                            "info"=> json_decode($str)
                                            )
                                    ), true
                                    );							
                    
            }break;
            
            default : break;
    }
}else{
header("HTTP/1.1 500 Internal Server Error");}




