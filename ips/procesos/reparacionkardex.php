<?php
include_once("conex.php");


include_once("root/comun.php");



//$empresa = 'clisur';
$wfecha_inicio  = $wano."-".$wmes."-01";
$wfecha_fin     = $wano."-".$wmes."-31";

function getMonthText($m) {
    switch ($m)
   {
        case 1: $month_text = "Enero"; break;
        case 2: $month_text = "Febrero"; break;
        case 3: $month_text = "Marzo"; break;
        case 4: $month_text = "Abril"; break;
        case 5: $month_text = "Mayo"; break;
        case 6: $month_text = "Junio"; break;
        case 7: $month_text = "Julio"; break;
        case 8: $month_text = "Agosto"; break;
        case 9: $month_text = "Septiembre"; break;
        case 10: $month_text = "Octubre"; break;
        case 11: $month_text = "Noviembre"; break;
        case 12: $month_text = "Diciembre"; break;
        default: $month_text = "NO REGISTRA"; break;
    }
    return ($month_text);
}

function actualizar_kardex($ind,$vdi,$auc,$aca,$aco,$key,$conex,$i,$data,$wccoo,$wccod,$wcont,$wdoct,$werr,$e,$wrg,$wiva,$warning,$wa,&$wupdate)
{
    global $empresa;
    global $conex;
    global $data_json;

    switch ($ind)
    {
        case "1":
            // $query = "lock table ".$empresa."_saldos_finales LOW_PRIORITY WRITE, ".$empresa."_000011 LOW_PRIORITY WRITE, ".$empresa."_000003 LOW_PRIORITY WRITE, ".$empresa."_000008 LOW_PRIORITY WRITE";
            // $err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO KARDEX Y DETALLE DE MOVIMIENTO");

            $query = "select Karexi, Karpro, Karvuc, Karfuc from  ".$empresa."_saldos_finales where Karcod='".$data['cod_producto']."' and Karcco='".$wccoo."'";
            $err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO KARDEX ");
            $num1 = mysql_num_rows($err1);



            if($num1 > 0)
            {
                $row1 = mysql_fetch_array($err1);
                $exi = $row1['Karexi'];
                $tot = $row1['Karpro'] * $exi;
                // ***** Adicion 2007-04-18 *****
                $proa=$row1[1];
                //******
                if($aca == "on")
                {

                   // echo "<br>";
                   // echo "1";
                   // echo "<br>existencias:".$exi;
                   // echo "<br>cantidad:".$data['cantidad'];
                   // echo "<br>cantidad sumada:".$exi=$exi + $data['cantidad'];
                   // echo "<br>total:".$tot;
                   // echo "<br>karpro:".$row1['Karpro'];
                   // echo "<br>concepto:".$data['concepto'];
                   // echo "<br>cco origen:".$wccoo;
                   // echo "<br>fecha:".$data['fecha'];
                   // echo "<br>hora:".$data['hora_mto'];

                    $exi = $exi + $data['cantidad']; // Existencias actuales
                    $pro = $row1['Karpro'];
                }
                if($aco == "on")
                {
                    if($vdi == "on")
                    {
                        // La variable $wccoo contiene el centro de costos origen y la variable $wcont contiene el concepto de inventario
                        // $query = "select Ccoiva from  ".$empresa."_000003 where Ccocod='".$wccoo."' ";
                        // $err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CENTROS DE COSTO: ".mysql_errno().":".mysql_error());
                        // $row1 = mysql_fetch_array($err1);
                        // $query = "select Coniva from  ".$empresa."_000008 where Concod='".$wcont."' ";
                        // $err2 = mysql_query($query,$conex) or die("ERROR CONSULTANDO MAESTRO DE CONCEPTOS: ".mysql_errno().":".mysql_error());
                        // $row2 = mysql_fetch_array($err2);
                        // if($row1[0] == "on" and $row2[0] == "on" )
                        // {
                            //La variable $data[$i][6] contiene el valor del IVA
                            // $tot=$tot + $data[$i][4] + $data[$i][6]; // Valor del iva
                            // $wiva="on";
                        // }
                        // else

                        // echo "<br>total:".$tot;
                            $tot = $tot + $data['totalvto'];

                            // echo "<br>valor digitado:".$data['totalvto'];
                            // echo "<br>total final:".$tot;
                            $wcostoorigen = $data['totalvto'];
                    }
                    else
                    {
                        $query = "select Karexi, Karpro, Karvuc, Karfuc from  ".$empresa."_saldos_finales where Karcod='".$data['cod_producto']."' and Karcco='".$wccod."'";
                        $err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO KARDEX ");
                        $num1 = mysql_num_rows($err1);
                        $row1 = mysql_fetch_array($err1);
                        $tot=$tot + ($row1['Karpro'] * $data['cantidad']);
                        $wcostoorigen = $row1['Karpro'];
                    }
                    if($exi != 0)
                        $pro= $tot / $exi ;
                    else
                        $pro= $tot;
                    // ***** Adicion 2007-04-18 *****
                    if($proa > 0)
                        $calc=abs((($pro / $proa) - 1)* 100);
                    else
                        $calc=0;
                    if($calc > 10)
                    {
                        $data_json['lista_errores'] .= "<br>EL ARTICULO  : ".$data['cod_producto']." EN EL CENTRO DE COSTOS : ".$wccoo." GENERO UN COSTO PROMEDIO : <FONT COLOR=#990000>".number_format((double)$pro,4,'.','')."</FONT> DESFASADO<FONT SIZE=6> 10% </FONT>DEL ANTERIOR : <FONT COLOR=#990000>".number_format((double)$proa,4,'.','')."</FONT> REVISE!!!";
                    }
                    //*****

//                    echo "<br>promedio si afecta costo:".$pro;
//                echo "<br>";
                }
                if($auc == "on")
                {

                    $valuc = $data['totalvto'] / $data['cantidad'];
                    $query =  " update ".$empresa."_saldos_finales set Karexi = ".number_format((double)$exi,4,'.','').",Karpro=".number_format((double)$pro,4,'.','').",Karvuc=".number_format((double)$valuc,2,'.','').",Karfuc='".$data['fecha']."'  where Karcod='".$data['cod_producto']."' and Karcco='".$wccoo."'";
                }
                else
                    $query =  " update ".$empresa."_saldos_finales set Karexi = ".number_format((double)$exi,4,'.','').",Karpro=".number_format((double)$pro,4,'.','')."  where Karcod='".$data['cod_producto']."' and Karcco='".$wccoo."'";

                $err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO KARDEX");

                $wupdate['costo'] = $pro;
                $wupdate['totalvto'] = $data['totalvto'];
                $wupdate['existencias'] = $exi;
                //$wupdate['costoorigen'] = $wcostoorigen;

            }
            else
            {
                $exi=0;
                $tot=0;
                if($aca == "on")
                {
                    $exi= $data['cantidad'];
                    $pro=0;
                }
                if($aco == "on")
                {
                    if($vdi == "on")
                    {
                        // Si el articulo NO tiene registro para ese centro de costos - articulo la validacion del IVA que no existia
                        // se realiza en esta parte del codigo.
                        // La variable $wccoo contiene el centro de costos origen y la variable $wcont contiene el concepto de inventario
                        // $query = "select Ccoiva from  ".$empresa."_000003 where Ccocod='".$wccoo."' ";
                        // $err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CENTROS DE COSTO: ".mysql_errno().":".mysql_error());
                        // $row1 = mysql_fetch_array($err1);
                        // $query = "select Coniva from  ".$empresa."_000008 where Concod='".$wcont."' ";
                        // $err2 = mysql_query($query,$conex) or die("ERROR CONSULTANDO MAESTRO DE CONCEPTOS: ".mysql_errno().":".mysql_error());
                        // $row2 = mysql_fetch_array($err2);
                        // if($row1[0] == "on" and $row2[0] == "on" )
                        // {
                            // La variable $data[$i][6] contiene el valor del IVA
                            // $tot=$data[$i][4] + $data[$i][6];
                            // $wiva="on";
                        // }
                        // else
                            $tot=$data['totalvto'];
                            $wcostoorigen = $data['totalvto'];

                    }
                    else
                    {
                        $query = "select Karexi, Karpro, Karvuc, Karfuc from  ".$empresa."_saldos_finales where Karcod='".$data['cod_producto']."' and Karcco='".$wccod."'";
                        $err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO KARDEX ");
                        $num1 = mysql_num_rows($err1);
                        $row1 = mysql_fetch_array($err1);
                        $tot=($row1['Karpro'] * $data['cantidad']);
                        $wcostoorigen = $row1['Karpro'];
                    }
                    if($exi != 0)
                        $pro= $tot / $exi ;
                    else
                        $pro= $tot;
                }
                $fecha = date("Y-m-d");
                $hora = (string)date("H:i:s");

                if($auc == "on")
                {
                    $valuc=$data['totalvto'] / $data['cantidad'];
                    $query = "insert ".$empresa."_saldos_finales (medico,fecha_data,hora_data, Karcod, Karcco, Karexi, Karpro, Karvuc, Karmax, Karmin, Karpor, Karfuc, Seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$data['cod_producto']."','".$wccoo."',".number_format((double)$exi,4,'.','').",".number_format((double)$pro,4,'.','').",".number_format((double)$valuc,2,'.','').",0,0,0,'".$fecha."','C-".$empresa."')";
                }
                else
                    $query = "insert ".$empresa."_saldos_finales (medico,fecha_data,hora_data, Karcod, Karcco, Karexi, Karpro, Karvuc, Karmax, Karmin, Karpor, Karfuc, Seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$data['cod_producto']."','".$wccoo."',".number_format((double)$exi,4,'.','').",".number_format((double)$pro,4,'.','').",0,0,0,0,'0000-00-00','C-".$empresa."')";
                $err1 = mysql_query($query,$conex) or die("ERROR INICIALIZANDO KARDEX : ".mysql_errno().":".mysql_error());

                // echo "<br>";
               // echo "1";
               // echo "<br>existencias:".$exi;
               // echo "<br>cantidad:".$data['cantidad'];
                // echo "<br>cantidad restada:".$exi=$exi - $data['cantidad'];
                // echo "<br>promedio si afecta costo:".$pro=$row1[1];
               // echo "<br>concepto:".$data['concepto'];
               // echo "<br>cco origen:".$wccoo;
               // echo "<br>fecha:".$data['fecha'];
               // echo "<br>hora:".$data['hora_mto'];
               // echo "<br>";

                $wupdate['costo'] = $pro;
                $wupdate['totalvto'] = $data['totalvto'];
                $wupdate['existencias'] = $exi;
                $wupdate['costoorigen'] = $wcostoorigen;

            }
                // if(strpos($data[$i][2],".") === false)
                    // $data[$i][2]=$data[$i][2].".0";
                // if(strpos($data[$i][3],".") === false)
                    // $data[$i][3]=$data[$i][3].".0";
                // if(strpos($data[$i][4],".") === false)
                    // $data[$i][4]=$data[$i][4].".0";
                // if($aca == "off")
                    // $data[$i][2]=0;
                // if($aco == "off")
                    // $data[$i][4]=0;
            return true;
        break;


        case "-1":
            // $query = "lock table ".$empresa."_saldos_finales LOW_PRIORITY WRITE, ".$empresa."_000011 LOW_PRIORITY WRITE";
            // $err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO KARDEX Y DETALLE DE MOVIMIENTO");
            $query = "select Karexi, Karpro, Karvuc, Karfuc from  ".$empresa."_saldos_finales where Karcod='".$data['cod_producto']."' and Karcco='".$wccoo."'";
            $err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO KARDEX ");
            $num1 = mysql_num_rows($err1);

            if($num1 > 0)
            {
                $row1 = mysql_fetch_array($err1);
                $exi=$row1['Karexi'];
                $tot=$row1['Karpro'] * $exi;
                // ***** Adicion 2007-04-18 *****
                $proa=$row1[1];
                //*****
                if($aca == "on")
                {
//                    $data_json['lista_errores'] .= "<br>";
//                    $data_json['lista_errores'] .= "-1";
//                    $data_json['lista_errores'] .= "<br>existencias:".$exi;
//                    $data_json['lista_errores'] .= "<br>cantidad:".$data['cantidad'];
//                    $data_json['lista_errores'] .= "<br>cantidad restada:".$exi=$exi - $data['cantidad'];
//                    $data_json['lista_errores'] .= "<br>promedio si afecta costo:".$pro=$row1[1];
//                    $data_json['lista_errores'] .= "<br>concepto:".$data['concepto'];
//                    $data_json['lista_errores'] .= "<br>total:".$tot;
//                    $data_json['lista_errores'] .= "<br>karpro:".$row1['Karpro'];
//                    $data_json['lista_errores'] .= "<br>cco origen:".$wccoo;
//                    $data_json['lista_errores'] .= "<br>fecha:".$data['fecha'];
//                    $data_json['lista_errores'] .= "<br>hora:".$data['hora_mto'];
//                    $data_json['lista_errores'] .= "<br>cantidad digitada:".$data['totalvto'];
//                    $data_json['lista_errores'] .= "<br>";
                $exi=$exi - $data['cantidad'];
                $pro=$row1[1];
                }
                if($aco == "on")
                {

                    if($vdi == "on")
                        $tot= $tot - $data['totalvto'];
                    else
                        $tot= $tot - ($row1['Karpro'] * $data['cantidad']);
                    if($exi != 0)
                        $pro= $tot / $exi ;
                    else
                    {
                        $pro= $tot;
                        if($pro == 0)
                            $pro= $row1[1];
                    }
                    // ***** Adicion 2007-04-18 *****
                    if($proa > 0)
                        $calc=abs((($pro / $proa) - 1)* 100);
                    else
                        $calc=0;
                    if($calc > 10)
                    {
                        $wa=$wa+1;
                        $data_json['lista_errores'] .= "<br>EL ARTICULO  : ".$data['cod_producto']." EN EL CENTRO DE COSTOS : ".$wccoo." GENERO UN COSTO PROMEDIO : <FONT COLOR=#990000>".number_format((double)$pro,4,'.','')."</FONT> DESFASADO<FONT SIZE=6> 10% </FONT>DEL ANTERIOR : <FONT COLOR=#990000>".number_format((double)$proa,4,'.','')."</FONT> REVISE!!!";
                    }
                    //*****
                }
                if( $exi >= 0 and $pro >= 0)
                {
                    $query =  " update ".$empresa."_saldos_finales set Karexi = ".number_format((double)$exi,4,'.','').",Karpro=".number_format((double)$pro,4,'.','')."  where Karcod='".$data['cod_producto']."' and Karcco='".$wccoo."'";
                    $err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO KARDEX");

                    $wupdate['costo'] = $pro;
                    $wupdate['totalvto'] = $data['totalvto'];
                    $wupdate['existencias'] = $exi;
                    $wupdate['costoorigen'] = $row1['Karpro'];

                    return true;
                }
                else
                {
                    $data_json['lista_errores'] .= "<br>ERROR NO SE GRABO EL MOVIMIENTO DEL ARTICULO  : ".$data['cod_producto']." GENERA NEGATIVOS EN CANTIDAD O VALOR ---- EXISTENCIAS NEGATIVAS GENERADAS : ".$exi."  PROMEDIO NEGATIVO GENERADO : ".$pro." CENTRO COSTOS : ".$wccoo."  cantidad anterior : ".$data['cantidad']." | ".$data['fecha']." - ".$data['hora_mto']." - ".$data['concepto']."  "; //
                    return false;
                }
            }
            break;
    }
}

if(isset($accion) && ($accion == 'recalcular'))
{
    $data_json = array('mensaje'=>'','error'=>0,'html'=>'','lista_errores'=>'');
    // $wfecha_inicio = '2012-09-01';
    // $wfecha_fin = '2012-09-30';
    $salmescompara = explode("-", $wfecha_inicio);
    $salanocalc = $salmescompara[0];
    $salmescalc = $salmescompara[1];
    // $salano = '2012';
    // $salmes = '08';

    // Si no existe Crea tabla para calcular saldos finales.
    $qS="
        CREATE TABLE IF NOT EXISTS ".$empresa."_saldos_finales (
            Medico varchar(8) NOT NULL DEFAULT '',
            Fecha_data date NOT NULL DEFAULT '0000-00-00',
            Hora_data time NOT NULL DEFAULT '00:00:00',
            Karcod varchar(80) NOT NULL DEFAULT '',
            Karcco varchar(80) NOT NULL DEFAULT '',
            Karexi double NOT NULL DEFAULT '0',
            Karpro double NOT NULL DEFAULT '0',
            Karvuc double NOT NULL DEFAULT '0',
            Karmax double NOT NULL DEFAULT '0',
            Karmin double NOT NULL DEFAULT '0',
            Karpor double NOT NULL DEFAULT '0',
            Karfuc date NOT NULL DEFAULT '0000-00-00',
            Seguridad varchar(10) NOT NULL DEFAULT '',
            id bigint(20) NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (id),
            UNIQUE KEY invkarcodccoidx (Karcod(20),Karcco(4))
        );";
    $resCre = mysql_query($qS,$conex) or die ("Error: ".mysql_errno()." - en el query Crear tabla bkp_07: ".$qS." - ".mysql_error());

    // Si no existe Crea tabla para calcular saldos finales.
    $qSb="
        CREATE TABLE IF NOT EXISTS ".$empresa."_000007_backup (
            Medico varchar(8) NOT NULL DEFAULT '',
            Fecha_data date NOT NULL DEFAULT '0000-00-00',
            Hora_data time NOT NULL DEFAULT '00:00:00',
            Karcod varchar(80) NOT NULL DEFAULT '',
            Karcco varchar(80) NOT NULL DEFAULT '',
            Karexi double NOT NULL DEFAULT '0',
            Karpro double NOT NULL DEFAULT '0',
            Karvuc double NOT NULL DEFAULT '0',
            Karmax double NOT NULL DEFAULT '0',
            Karmin double NOT NULL DEFAULT '0',
            Karpor double NOT NULL DEFAULT '0',
            Karfuc date NOT NULL DEFAULT '0000-00-00',
            Seguridad varchar(10) NOT NULL DEFAULT '',
            id BIGINT(20) NOT NULL DEFAULT '0',
            INDEX invkarcodccoidx (Karcod(20),Karcco(4))
        );";
    $resCreB = mysql_query($qSb,$conex) or die ("Error: ".mysql_errno()." - en el query Crear tabla bkp_07 fotos por dia-hora: ".$qSb." - ".mysql_error());

    // Si no existe Crea tabla para guardar backup de los movimientos a modificar.
    $qM="
        CREATE TABLE IF NOT EXISTS ".$empresa."_000011_backup (
            Medico varchar(8) NOT NULL DEFAULT '',
            Fecha_data date NOT NULL DEFAULT '0000-00-00',
            Hora_data time NOT NULL DEFAULT '00:00:00',
            Mdecon varchar(80) NOT NULL DEFAULT '',
            Mdedoc varchar(80) NOT NULL DEFAULT '0',
            Mdeart varchar(80) NOT NULL DEFAULT '',
            Mdecan double NOT NULL DEFAULT '0',
            Mdevto double NOT NULL DEFAULT '0',
            Mdepiv double NOT NULL DEFAULT '0',
            Mdefve date NOT NULL DEFAULT '0000-00-00',
            Mdenlo varchar(80) NOT NULL DEFAULT '',
            Mdeest char(3) NOT NULL DEFAULT '',
            Seguridad varchar(10) NOT NULL DEFAULT '',
            id bigint(20) NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (id),
            UNIQUE KEY invmdecondocartidx (Mdecon(3),Mdedoc(15),Mdeart(8))
        );";
    $resMov = mysql_query($qM,$conex) or die ("Error: ".mysql_errno()." - en el query Crear tabla movimientos bkp_11: ".$qM." - ".mysql_error());

    $fechaBK = date('Y-m-d');
    $horaBK = date('H').':00:00';
    // SI NO HAY UN BACKUP del mes - dia - hora de la 000007 en su tabla de backup entonces cree el backup de ese instante.
    $qhayBk="   SELECT  COUNT(id) AS existen
                FROM    ".$empresa."_000011_backup
                WHERE   Fecha_data = '".$fechaBK."'
                        AND Hora_data = '".$horaBK."'";
    $reshayBk = mysql_query($qhayBk,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qhayBk." - ".mysql_error());
    //$haybk = mysql_num_rows($reshayBk);
    if($reshayBk)
    {
        $rr = mysql_fetch_array($reshayBk);
        if($rr['existen'] <= 0)
        {
            $qBk="  INSERT  INTO ".$empresa."_000007_backup (Medico, Fecha_data, Hora_data, Karcod, Karcco, Karexi, Karpro, Karvuc, Karmax, Karmin, Karpor, Karfuc, Seguridad, id)
                    SELECT  Medico, '".$fechaBK."', '".$horaBK."', Karcod, Karcco, Karexi, Karpro, Karvuc, Karmax, Karmin, Karpor, Karfuc, Seguridad, id
                    FROM    ".$empresa."_000007;";
            $resBk = mysql_query($qBk,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qBk." - ".mysql_error());
        }
    }

    // Borra datos antes calculados en la tabla de saldos finales
    $q="TRUNCATE ".$empresa."_saldos_finales;";
    $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

    // Reinicia la tabla de saldos finales para hacer los calculos
    $q="insert into ".$empresa."_saldos_finales (Medico, Fecha_data, Hora_data, Karcod, Karcco, Karexi, Karpro, Karvuc, Karmax, Karmin, Karpor, Karfuc, Seguridad)
            SELECT
            Medico,Fecha_data,Hora_data,Salcod, Salcco,Salexi,Salpro,Salvuc,Salmax,Salmin,Salpor,Salfuc,Seguridad
            from ".$empresa."_000014 where salano='".$salano."' and salmes = '".$salmes."'; ";
     $res4 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

    // Consultad los movimientos a recalcular.
    $q="SELECT ".$empresa."_000011.id, ".$empresa."_000011.Fecha_data AS fecha_mto, ".$empresa."_000011.Hora_data AS hora_mto, Mdeart, Mendoc ,
                    ".$empresa."_000011.Mdecan, Mdevto, (Mdevto/Mdecan) AS unidad,Mdecon, Mencco, Menccd, Conind, Conaca, Conaco,Conauc,Convdi
               FROM ".$empresa."_000008, ".$empresa."_000010, ".$empresa."_000011
              WHERE Mendoc = Mdedoc
                AND Mencon = Mdecon
                AND Mdecon = Concod
                AND ".$empresa."_000010.Fecha_data = ".$empresa."_000011.Fecha_data
                AND ".$empresa."_000011.Fecha_data BETWEEN '".$wfecha_inicio."' AND '".$wfecha_fin."'
            ORDER BY ".$empresa."_000011.id";
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

    // SI NO HAY UN BACKUP DEL MES A RECALCULAR ENTONCES DEBE CREAR AL BACKUP PARA ESE MES
    $qhayBk="   SELECT  COUNT(id) AS existen
                FROM    ".$empresa."_000011_backup
                WHERE   Fecha_data BETWEEN '".$wfecha_inicio."' AND '".$wfecha_fin."'";
    $reshayBk = mysql_query($qhayBk,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qhayBk." - ".mysql_error());
    $haybk = mysql_num_rows($reshayBk);
    if($haybk > 0)
    {
        $rr = mysql_fetch_array($reshayBk);
        if($rr['existen'] <= 0)
        {
            $qBk="  INSERT  INTO ".$empresa."_000011_backup (Medico, Fecha_data, Hora_data, Mdecon, Mdedoc, Mdeart, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo, Mdeest, Seguridad, id)
                    SELECT  m.Medico, m.Fecha_data, m.Hora_data, m.Mdecon, m.Mdedoc, m.Mdeart, m.Mdecan, m.Mdevto, m.Mdepiv, m.Mdefve, m.Mdenlo, m.Mdeest, m.Seguridad, m.id
                    FROM    ".$empresa."_000011 AS m
                            LEFT JOIN
                            ".$empresa."_000011_backup AS bk ON (m.Fecha_data = bk.Fecha_data
                                        AND m.Hora_data = bk.Hora_data
                                        AND m.Mdedoc = bk.Mdedoc
                                        )
                    WHERE   m.Fecha_data BETWEEN '".$wfecha_inicio."' AND '".$wfecha_fin."'
                            AND bk.Fecha_data IS NULL;";
            $resBk = mysql_query($qBk,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qBk." - ".mysql_error());
        }
    }
    else
    {
        $qBk="   INSERT  INTO ".$empresa."_000011_backup (Medico, Fecha_data, Hora_data, Mdecon, Mdedoc, Mdeart, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo, Mdeest, Seguridad, id)
                    SELECT  m.Medico, m.Fecha_data, m.Hora_data, m.Mdecon, m.Mdedoc, m.Mdeart, m.Mdecan, m.Mdevto, m.Mdepiv, m.Mdefve, m.Mdenlo, m.Mdeest, m.Seguridad, m.id
                    FROM    ".$empresa."_000011 AS m
                            LEFT JOIN
                            ".$empresa."_000011_backup AS bk ON (	m.Fecha_data = bk.Fecha_data
                                        AND m.Hora_data = bk.Hora_data
                                        AND m.Mdedoc = bk.Mdedoc
                                        )
                    WHERE   m.Fecha_data BETWEEN '".$wfecha_inicio."' AND '".$wfecha_fin."'
                            AND bk.Fecha_data IS NULL;";
        $resBk = mysql_query($qBk,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qBk." - ".mysql_error());
    }

    while ($row = mysql_fetch_array($res))
    {
        $wid = $row['id'];
        $wccoo = $row['Mencco'];
        $wccod = $row['Menccd'];
        $wcantidad = $row['Mdecan'];
        $wcodigo_producto = $row['Mdeart'];
        $wconcepto  = $row['Mdecon'];
        $wfecha  = $row['fecha_mto'];
        $whora  = $row['hora_mto'];
        $wunidad = $row['unidad'];
        $wtotal = $row['Mdevto'];
        $ind=$row['Conind'];
        $aca=$row['Conaca'];
        $aco=$row['Conaco'];
        $auc=$row['Conauc'];
        $vdi=$row['Convdi'];
        $wdoct=$row['Mendoc'];
        $key = '03997';
        $wrg=0;
        $wiva="off";
        $wa=-1;
        $data['id']= $wid;
        $data['cantidad']= $wcantidad;
        $data['cod_producto']= $wcodigo_producto;
        $data['centro_costos_origen']= $wccoo;
        $data['unidad']= $wunidad;
        $data['totalvto']= $wtotal;
        $data['fecha']= $wfecha;
        $data['hora_mto']= $whora;
        $data['concepto']= $wconcepto;

        $i = 0;
        $wcont = 0;
        $werr = '';
        $e = '';
        $warning = '';

        if ( $aca == 'on' or $aco == 'on')
        {
            $wupdate = array('costo'=>0,'existencias'=>0);

            switch ($ind)
            {
                case "1":

                    if(actualizar_kardex("1",$vdi,$auc,$aca,$aco,$key,$conex,$i,$data,$wccoo,$wccod,$wcont,$wdoct,$werr,$e,$wrg,$wiva,$warning,$wa,$wupdate))
                    {

                        if ($vdi == 'on') //Valor es digitado
                        {
                            $wcostofinal = $wupdate['totalvto'];
                        }
                        else
                        {
                            $wcostounitario = $wupdate['costo'];
                            $wcostofinal = $wcantidad * $wupdate['costo'];
                        }

                        $q2=" UPDATE ".$empresa."_000011
                                SET Mdevto = '".$wcostofinal."'
                              WHERE id = '".$wid."'";
                        $res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());

                    }
                    break;
                case "-1":
                        if (actualizar_kardex("-1",$vdi,$auc,$aca,$aco,$key,$conex,$i,$data,$wccoo,$wccod,$wcont,$wdoct,$werr,$e,$wrg,$wiva,$warning,$wa,$wupdate))
                        {
                            
                            if ($vdi == 'on') //Valor es digitado
                                {
                                    $wcostofinal = $wupdate['totalvto'];
                                }
                                else
                                {
                                    $wcostounitario = $wupdate['costo'];
                                    $wcostofinal = $wcantidad * $wupdate['costoorigen'];
                                }
                                
                            
                            $q2=" UPDATE ".$empresa."_000011
                                    SET Mdevto = '".$wcostofinal."'
                                  WHERE id = '".$wid."'";
                            $res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
                        }
                        break;

                case "0":
                    if(actualizar_kardex("-1",$vdi,$auc,$aca,$aco,$key,$conex,$i,$data,$wccoo,$wccod,$wcont,$wdoct,$werr,$e,$wrg,$wiva,$warning,$wa,$wupdate))
                    {

                        if (actualizar_kardex("1",$vdi,$auc,$aca,$aco,$key,$conex,$i,$data,$wccod,$wccoo,$wcont,$wdoct,$werr,$e,$wrg,$wiva,$warning,$wa,$wupdate))
                        {
                            // echo "<br>id:".$wid;
                            // echo "<br>costoantes:".$wupdate['costoorigen'];
                            // echo "<br>cantidad:".$wcantidad;
                           $wcostofinal = $wcantidad * $wupdate['costoorigen'];
                           $q2=" UPDATE ".$empresa."_000011
                                    SET Mdevto = '".$wcostofinal."'
                                  WHERE id = '".$wid."'";
                            $res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
                        }
                    }
                    break;
            }
        }
    }

    //Actualizar kardex con los datos de la tabla clisur_saldos_finales

    $q = "  SELECT Medico, Fecha_data, Hora_data, Karcod, Karcco, Karexi, Karpro, Karvuc, Karmax, Karmin, Karpor, Karfuc, Seguridad, id
            FROM   ".$empresa."_saldos_finales";

    $res = mysql_query($q,$conex) or die("Error: " . mysql_errno() . "  - ".$q.mysql_error());

    while ($row = mysql_fetch_array($res))
    {
        $q2 = " UPDATE  ".$empresa."_000007
                SET     Karexi = '".$row['Karexi']."',
                        Karpro = '".$row['Karpro']."',
                        Karvuc = '".$row['Karvuc']."'
               WHERE    Karcod = '".$row['Karcod']."'
                        AND Karcco = '".$row['Karcco']."'";
        mysql_query($q2,$conex) or die("Error: " . mysql_errno() . "  - " . mysql_error());
        //$data_json['html'] .= '<br>'.$row['id'].'| '.utf8_encode($q2);
    }
    
    
   if(!isset($programada) || $programada != 'on')
   { 

    $q2="
        SELECT  h.salcco, s.karcco, h.salexi, s.karexi, h.salcod, s.Karcod, h.salpro, s.karpro
        FROM    ".$empresa."_000014 as h, ".$empresa."_saldos_finales as s
        WHERE   h.salcod = s.karcod
                AND h.salcco = s.Karcco
                AND salano='".$salanocalc."'
                AND salmes='".$salmescalc."'
        ORDER BY s.karexi DESC";
    $res1 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

    $compara = "
        <table border=1>
            <tr>
                <td>Cco 14</td>
                <td>Cco Calculado</td>
                <td>Existen 14</td>
                <td>Existe Calculado</td>
                <td>Codigo 14</td>
                <td>Codigo Calculado</td>
                <td>Promedio 14</td>
                <td>Promedio Calculado</td>
            <tr>";

    while ($row1 = mysql_fetch_array($res1))
    {
        $diferencia = ($row1['salexi'] != $row1['karexi'] || $row1['salpro'] != $row1['karpro']) ? 'style="background-color:#f2f2f2;"': '';
        $difexi = ($row1['salexi'] != $row1['karexi']) ? 'style="background-color:#cccccc;color:darkorange;"': '';
        $difpro = ($row1['salpro'] != $row1['karpro']) ? 'style="background-color:#cccccc;color:darkorange;"': '';

        $compara .=  "
            <tr ".$diferencia.">
                <td>".$row1['salcco']."</td>
                <td>".$row1['karcco']."</td>
                <td ".$difexi.">".$row1['salexi']."</td>
                <td ".$difexi.">".$row1['karexi']."</td>
                <td>".$row1['salcod']."</td>
                <td>".$row1['Karcod']."</td>
                <td ".$difpro.">".$row1['salpro']."</td>
                <td ".$difpro.">".$row1['karpro']."</td>
            </tr>";

  //        $q2=" UPDATE clisur_000007
  //                 SET Karpro = '".$wnuevocospro."'
  //               WHERE Karcod = '".$wcodigo_producto."'
  //                 AND Karcco = '".$wcentro_costos_afectado."'";
  //       mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        // $conteo++;

        //  $q2=" UPDATE clisur_000011
  //                 SET Karexi = ".$row['final']."
  //               WHERE Karcod = '".$row['Karcodaux']."'
  //                 AND Karcco = '".$row['Karccoaux']."'
  //           ";
  //       mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        // $conteo++;


    }
    $compara .= "</table>";

    $data_json['html'] = $compara;
    echo json_encode($data_json);
     }
    return;
  
}
elseif(isset($accion) && ($accion == 'actualizar_000007'))
{
    $data_json = array('mensaje'=>'','error'=>0,'html'=>'');
    $q = "  SELECT Medico, Fecha_data, Hora_data, Karcod, Karcco, Karexi, Karpro, Karvuc, Karmax, Karmin, Karpor, Karfuc, Seguridad, id
            FROM   ".$empresa."_saldos_finales";

    $res = mysql_query($q,$conex) or die("Error: " . mysql_errno() . "  - ".$q.mysql_error());

    while ($row = mysql_fetch_array($res))
    {
        $q2 = " UPDATE  ".$empresa."_000007
                SET     Karexi = '".$row['Karexi']."',
                        Karpro = '".$row['Karpro']."',
                        Karvuc = '".$row['Karvuc']."'
               WHERE    Karcod = '".$row['Karcod']."'
                        AND Karcco = '".$row['Karcco']."'";
        mysql_query($q2,$conex) or die("Error: " . mysql_errno() . "  - " . mysql_error());
        //$data_json['html'] .= '<br>'.$row['id'].'| '.utf8_encode($q2);
    }

    echo json_encode($data_json);
    return;
}
elseif(isset($accion) && ($accion == 'solo_ver_diferencias'))
{
    $data_json = array('mensaje'=>'','error'=>0,'html'=>'');

    if(!isset($opVer)) { $opVer = 'todo'; }

    $salmes = (($salmes*1) < 10) ? '0'.$salmes: $salmes;

    switch($opVer)
    {
        case 'todo':
                    $q2 = "
                        SELECT  h.salcco, s.karcco, h.salexi, s.karexi, h.salcod, s.Karcod, h.salpro, s.karpro
                        FROM    ".$empresa."_000014 as h, ".$empresa."_saldos_finales as s
                        WHERE   h.salcod = s.karcod
                                AND h.salcco = s.Karcco
                                AND salano='".$salano."'
                                AND salmes='".$salmes."'
                        ORDER BY s.karexi DESC";
                    break;
        case 'solo_prom':
                    $q2 = "
                        SELECT  h.salcco, s.karcco, h.salexi, s.karexi, h.salcod, s.Karcod, h.salpro, s.karpro
                        FROM    ".$empresa."_000014 as h, ".$empresa."_saldos_finales as s
                        WHERE   h.salcod = s.karcod
                                AND h.salcco = s.Karcco
                                AND salano='".$salano."'
                                AND salmes='".$salmes."'
                                AND h.salpro <> s.karpro
                        ORDER BY s.karexi DESC";
                    break;
        case 'solo_existencia':
                    $q2 = "
                        SELECT  h.salcco, s.karcco, h.salexi, s.karexi, h.salcod, s.Karcod, h.salpro, s.karpro
                        FROM    ".$empresa."_000014 as h, ".$empresa."_saldos_finales as s
                        WHERE   h.salcod = s.karcod
                                AND h.salcco = s.Karcco
                                AND salano='".$salano."'
                                AND salmes='".$salmes."'
                                AND h.salexi <> s.karexi
                        ORDER BY s.karexi DESC";
                    break;
        case 'prom_o_existencia':
                    $q2 = "
                        SELECT  h.salcco, s.karcco, h.salexi, s.karexi, h.salcod, s.Karcod, h.salpro, s.karpro
                        FROM    ".$empresa."_000014 as h, ".$empresa."_saldos_finales as s
                        WHERE   h.salcod = s.karcod
                                AND h.salcco = s.Karcco
                                AND salano='".$salano."'
                                AND salmes='".$salmes."'
                                AND (
                                        h.salexi <> s.karexi
                                        OR
                                        h.salpro <> s.karpro
                                    )
                        ORDER BY s.karexi DESC";
                    break;
        default:
                $q2 = "
                        SELECT  h.salcco, s.karcco, h.salexi, s.karexi, h.salcod, s.Karcod, h.salpro, s.karpro
                        FROM    ".$empresa."_000014 as h, ".$empresa."_saldos_finales as s
                        WHERE   h.salcod = s.karcod
                                AND h.salcco = s.Karcco
                                AND salano='".$salano."'
                                AND salmes='".$salmes."'
                        ORDER BY s.karexi DESC";
                    break;
    }

    $res1 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

    $cont_difs = 0;
    $compara_concat = '';
    while ($row1 = mysql_fetch_array($res1))
    {
        $diferencia = ($row1['salexi'] != $row1['karexi'] || $row1['salpro'] != $row1['karpro']) ? 'style="background-color:#f2f2f2;"': '';
        $difexi = ($row1['salexi'] != $row1['karexi']) ? 'style="background-color:#cccccc;color:darkorange;"': '';
        $difpro = ($row1['salpro'] != $row1['karpro']) ? 'style="background-color:#cccccc;color:darkorange;"': '';

        $compara_concat .=  "
            <tr ".$diferencia.">
                <td>".$row1['salcco']."</td>
                <td>".$row1['karcco']."</td>
                <td ".$difexi.">".$row1['salexi']."</td>
                <td ".$difexi.">".$row1['karexi']."</td>
                <td>".$row1['salcod']."</td>
                <td>".$row1['Karcod']."</td>
                <td ".$difpro.">".$row1['salpro']."</td>
                <td ".$difpro.">".$row1['karpro']."</td>
            </tr>";

        $cont_difs++;

  //        $q2=" UPDATE clisur_000007
  //                 SET Karpro = '".$wnuevocospro."'
  //               WHERE Karcod = '".$wcodigo_producto."'
  //                 AND Karcco = '".$wcentro_costos_afectado."'";
  //       mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        // $conteo++;

        //  $q2=" UPDATE clisur_000011
  //                 SET Karexi = ".$row['final']."
  //               WHERE Karcod = '".$row['Karcodaux']."'
  //                 AND Karcco = '".$row['Karccoaux']."'
  //           ";
  //       mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        // $conteo++;
    }
    $compara = "
        <table border='1' align='center'>
            <tr>
                <td colspan='8'>Registros: ".$cont_difs."</td>
            </tr>
            <tr>
                <td>Cco 14</td>
                <td>Cco Calculado</td>
                <td>Existen 14</td>
                <td>Existe Calculado</td>
                <td>Codigo 14</td>
                <td>Codigo Calculado</td>
                <td>Promedio 14</td>
                <td>Promedio Calculado</td>
            <tr>
            ".$compara_concat."
        </table>";

    $data_json['html'] = $compara;
    echo json_encode($data_json);
    return;
}
elseif(isset($accion) && ($accion == 'ver_copias_respaldos'))
{
    $data_json = array('mensaje'=>'','error'=>0,'html'=>'');

    // puntos de restauracion de la 000007
    $q="
        SELECT  Fecha_data, Hora_data
        FROM    ".$empresa."_000007_backup
        GROUP BY Fecha_data, Hora_data";
    $res4 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


    $copias_7 = "<table>
                    <tr class='encabezadoTabla'>
                        <td colspan='3'>(clisur_000007_backup) Copias de respaldo de la 000007</td>
                    </tr>";
    while($row = mysql_fetch_array($res4))
    {
        $copias_7 .= "
            <tr class='fila1'>
                <td>
                    <input type='checkbox' id='bkp_siete".$row['Fecha_data'].'|'.$row['Hora_data']."'>
                </td>
                <td>".$row['Fecha_data']."</td>
                <td>".$row['Hora_data']."</td>
            </tr>
        ";
    }
    $copias_7 .= "</table>";
    $data_json['copias_siete'] = $copias_7;

    // puntos de restauracion de la 000011
    $q="
        SELECT  MIN(Fecha_data) AS fecha
        FROM    ".$empresa."_000011_backup
        GROUP BY MONTH(Fecha_data)";
    $res4 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $copias_11 = "<table>
                    <tr class='encabezadoTabla'>
                        <td colspan='3'>(clisur_000011_backup) Copias de respaldo de la 000011</td>
                    </tr>";
    while($row = mysql_fetch_array($res4))
    {
        $explode = explode('-',$row['fecha']);
        $copias_11 .= "
            <tr class='fila1'>
                <td>
                    <input type='checkbox' id='bkp_once".$row['fecha']."'>
                </td>
                <td>Copia</td>
                <td>Mes: ".getMonthText(($explode[1])*1).' '.$explode[0]."</td>
            </tr>
        ";
    }
    $copias_11 .= "</table>";

    $data_json['copias_siete'] = $copias_7;
    $data_json['copias_once'] = $copias_11;

    echo json_encode($data_json);
    return;
}
else
{

    $anios = '';
    for($i=2000;$i <= date("Y");$i++)
    {
        $ck = ($i == date('Y')) ? 'selected="selected"': '';
        $anios .= "
            <option value='".$i."' ".$ck.">".$i."</option>";
    }

    $meses = '';
    for($i=1;$i <= 12;$i++)
    {
        $ck = ($i == (date('m')*1)) ? 'selected="selected"': '';
        $meses .= "
            <option value='".$i."' ".$ck.">".getMonthText($i)."</option>";
    }

    $wfecha_inicio = date('Y-m-d');
    $wfecha_fin = date('Y-m-d');
}


    
?>
<html>
<head>
<title>Reparar saldos y movimientos de inventario</title>
<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />

<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>

<script type="text/javascript">
    function recalcular()
    {
        $('#div_resultado_errores').hide();
        $('#div_resultado_errores').html('');
        $('#div_resultado_comparacion').html('<div align="center">Recalculando mes, el proceso se esta ejecutando, por favor espere un momento..</div>');
        $.post("reparacionkardex.php",
            {
                consultaAjax: '',
                empresa         : $("#empresa").val(),
                accion          : 'recalcular',
                wano            : $('#wano').val(),
                wmes            : $('#wmes').val(),
                salano          : $('#salano').val(),
                salmes          : $('#salmes').val()
            }
            ,function(data_json) {
                $('#div_resultado_errores').html(data_json.lista_errores);
                $('#div_resultado_comparacion').html(data_json.html);
                $('#forma_comparar1').attr('checked','checked');
            },
            "json"
        );
    }

    function verCopias()
    {
        $.post("reparacionkardex.php",
            {
                consultaAjax: '',
                empresa         : $("#empresa").val(),
                accion          : 'ver_copias_respaldos'
            }
            ,function(data_json) {
                $('#div_000007_copiados').html(data_json.copias_siete);
                $('#div_000011_copiados').html(data_json.copias_once);
            },
            "json"
        );
    }

    function verComparaciones(formaVer)
    {
        $.post("reparacionkardex.php",
            {
                consultaAjax: '',
                empresa         : $("#empresa").val(),
                opVer           : formaVer,
                accion          : 'solo_ver_diferencias',
                salano          : $('#salano_ver').val(),
                salmes          : $('#salmes_ver').val()
            }
            ,function(data_json) {
                if(data_json.error == 1)
                {
                    alert('No se pudo consultar, se genero algun error.');
                }
                else
                {
                    $('#div_resultado_comparacion').html(data_json.html);
                }
            },
            "json"
        );
    }

    function actualizarKardex()
    {
        $.post("reparacionkardex.php",
            {
                consultaAjax: '',
                empresa         : $("#empresa").val(),
                accion          : 'actualizar_000007'
            }
            ,function(data_json) {
                if(data_json.error == 1)
                {
                    alert('No se pudo consultar, se genero algun error.');
                }
                else
                {
                    $('#div_actualizacion_kardex').html('Se actualizó..');
                    // $('#div_actualizacion_kardex').html(data_json.html);
                }
            },
            "json"
        );
    }

    function verSeccion(id){
        $("#"+id).toggle("normal");
    }
</script>

<style type="text/css">
    .menuP{
        cursor:pointer;
    }
</style>

</head>
<body>
    <table align='center'>
        <tr>
            <td align='center'>
                <div>
                    <table align='center'>
                        <tr class='encabezadoTabla'>
                            <td class='menuP'>Opciones Recalcular</td>
                            <td class='menuP'>Opciones Retroceder</td>
                        </tr>
                    </table>
                </div>
                <br />
                <div id='div_recalcular'>
                    <div style='background-color:#cccccc;'>Datos para recalcular mes</div>
                    <form id='f_arreglo' name='f_arreglo' acction='repacionkardex.php' method='post'>
                        <table align='center'>
                            <tr class='encabezadoTabla'>
                                <td colspan='4' align='center'><h2>Ajuste de saldos (costos,existencias)</h2></td>
                            </tr>
                            <INPUT type='hidden' id='empresa' value='<?php echo $empresa;?>'>
                            <tr class='fila1'>
                                <td colspan='2'>Fecha para ajustar: </td>
                                <td>                                    
                                    <select id='wano' name='wano'>
                                        <?=$anios?>
                                    </select>
                                 <!--   Desde: <?php campofechaDefecto("wfecha_inicio",$wfecha_inicio); ?> -->
                                </td>
                                <td>                                  
                                    <select id='wmes' name='wmes'>
                                            <?=$meses?>
                                        </select>
                                 <!--   Desde:   Hasta: <?php campofechaDefecto("wfecha_fin",$wfecha_fin); ?> -->
                                </td>
                            </tr>
                            <tr class='fila2'>
                                <td colspan='2'>Datos iniciales a partir de:</td>
                                <td align='center'>
                                     A&ntilde;o <select id='salano' name='salano'>
                                                    <?=$anios?>
                                                </select>
                                </td>
                                <td align='center'>
                                    Mes <select id='salmes' name='salmes'>
                                            <?=$meses?>
                                        </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan='4' align='center'><button type="button" id='btn_calcular' onclick='recalcular();'>Recalcular movimientos y saldos</button></td>
                            </tr>
                        </table>
                    </form>

                    <div id='div_ver_err' onclick='verSeccion("div_resultado_errores");' style='color:#999999;' align='left'>Ver Errores o Alertas <span style='font-size:8pt;cursor:pointer;'>(Ver)</span></div>
                    <div id='div_resultado_errores' style='display:none;text-align:left;font-size:8pt;'></div>
                    <br />
                    <div id='div_ver_err' onclick='verSeccion("div_resultado_comparacion");' style='color:#999999;' align='left'>Resultado de comparaci&oacute;n luego de recalcular <span style='font-size:8pt;cursor:pointer;'>(Ver)</span></div>
                    <div>
                        <table align='center'>
                            <tr class='encabezadoTabla'>
                                <td class='menuP' style='font-size:8pt;'>
                                    Comparar saldos finales (clisur_000007 y calculos para clisur_000007). Saldo final:
                                    A&ntilde;o <select id='salano_ver' name='salano_ver'>
                                                    <?=$anios?>
                                                </select>
                                    Mes <select id='salmes_ver' name='salmes_ver'>
                                            <?=$meses?>
                                        </select>
                                </td>
                            </tr>
                            <tr class='encabezadoTabla'>
                                <td class='menuP' style='font-size:8pt;'>
                                    [Ver todo<input type='radio' id='forma_comparar1' name='forma_comparar' value='todo' onclick='verComparaciones("todo")'>]
                                    [Diferencias solo promedio<input type='radio' id='forma_comparar2' name='forma_comparar' value='solo_prom' onclick='verComparaciones("solo_prom")'>]
                                    [Diferencias solo existencias<input type='radio' id='forma_comparar3' name='forma_comparar' value='solo_existencia' onclick='verComparaciones("solo_existencia")'>]
                                    [Diferencias promedio o existencia<input type='radio' id='forma_comparar4' name='forma_comparar' value='prom_o_existencia' onclick='verComparaciones("prom_o_existencia")'>]
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id='div_resultado_comparacion' style='text-align:left;'></div>
                    <br />
                    <br />
                    <!--
                        <div id='div_actualizacion_kardex1' style='text-align:left;background-color:#f2f2f2;'>
                            Actualizar kardex (clisur_000007) con saldos calculados (clisur_saldos_finales) <button type="button" id='btn_actualizar' onclick='actualizarKardex();'>Actualizar</button>
                        </div>
                        <div id='div_actualizacion_kardex' style='text-align:left;'></div>
                    -->
                </div>
                <br />
                <br />
                <div id='div_devolver_proceso'>
                    <div style='background-color:#cccccc;' onclick='verCopias();'>Datos de backup (ver)</div>

                    <div id='div_000007_copiados'></div>
                    <div id='div_000011_copiados'></div>
                </div>

            </td>
        </tr>
    </table>
</body>
</html>