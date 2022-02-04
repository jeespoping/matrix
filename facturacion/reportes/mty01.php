<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <title>MATRIX - [FACTURAS PARA MYT]</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /> <!--Estilo para el calendario-->
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
    <style>
        .alternar:hover{ background-color:#CADCFF;}
    </style>
    <script>
        $(function() {
            $( "#datepicker1" ).datepicker();
        });
    </script>
    <?php
    /** 
     * Modificaciones:
     * 2022 Daniel CB: Se modifica variables quemadas (nit y nombre de empresa)
    */
    if(!isset($_SESSION['user']))
    {
        ?>
        <div align="center">
            <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
        </div>
        <?php
        return;
    }
    else
    {
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        include("conex.php");
        include("root/comun.php");
        mysql_select_db("matrix");

        $conex = obtenerConexionBD("matrix");
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
        $wactualiz = "2022-02-01";
    }
    session_start();
    ?>
</head>

<body>
<?php
encabezado("<font style='font-size: x-large; font-weight: bold'>"."FACTURAS PARA MYT"."</font>",$wactualiz,"clinica");
?>
<div style="padding-top:10px" class="panel-body">
    <form class="form-horizontal" role="form" name="mty01" action="mty01.php" method="post">
        <table align="center" border="0">
            <tr style="text-align: center">
                <td colspan="6">
                    <label for="radio">&ensp;&ensp;CTC</label>
                    &ensp;&ensp;&ensp;
                    <label>Tutela</label>
                </td>
            </tr>
            <tr style="text-align: center">
                <td colspan="6">
                    <input type="radio" id="radio" name="radio" value="1" checked />
                    &ensp;&ensp;&ensp;&ensp;
                    <input type="radio" id="radio" name="radio" value="2" />
                </td>
            </tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr style="text-align: center">
                <td>
                    <div class="input-group">
                        <span class="input-group-addon"><label for="tipoImpresion">TIPO DE IMPRESION:</label></span>
                        <select id="tipoImpresion" name="tipoImpresion" class="form-control">
                            <option>DETALLADA</option>
                            <option>RESUMIDA</option>
                        </select>
                    </div>
                </td>
                <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon" style="width: 217px"><label for="fte">FUENTE:</label></span>
                        <input id="fte" name="fte" type="text" class="form-control" style="width: 100px" value="">
                    </div>
                </td>
                <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon"><label for="fac">FACTURA NUMERO:</label></span>
                        <input id="fac" name="fac" type="text" class="form-control" style="width: 150px" value="">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="input-group" style="margin-top: 10px">
                        <span class="input-group-addon"><label for="numConsRadicado">N° CONS. RADICADO:</label></span>
                        <input id="numConsRadicado" name="numConsRadicado" type="text" class="form-control" style="width: 100px" value="">
                    </div>
                </td>
                <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                <td>
                    <div class="input-group" style="margin-top: 10px">
                        <span class="input-group-addon" style="width: 217px"><label for="numConsRecobro">N° CONS. RECOBRO:</label></span>
                        <input id="numConsRecobro" name="numConsRecobro" type="text" class="form-control" style="width: 100px" value="">
                    </div>
                </td>
                <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                <td>
                    <div class="input-group" style="margin-top: 10px">
                        <span class="input-group-addon" style="width: 180px"><label for="numeroActa">N° ACTA:</label></span>
                        <input id="numeroActa" name="numeroActa" type="text" class="form-control" style="width: 100px" value="">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="input-group" style="margin-top: 10px">
                        <span class="input-group-addon" style="width: 195px"><label for="datepicker1">FECHA ACTA:</label></span>
                        <input id="datepicker1" name="fechaActa" type="text" class="form-control-sm" style="width: 150px; border-color: #EEEEEE">
                    </div>
                    <!--
                    <div class="input-group" style="margin-top: 10px">
                        <span class="input-group-addon" style="width: 195px"><label for="fechaActa">FECHA ACTA:</label></span>
                        <input id="fechaActa" name="fechaActa" type="date" class="form-control-sm" style="width: 150px; border-color: #EEEEEE">
                    </div>
                    -->
                </td>
                <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                <td>
                    <div class="input-group" style="margin-top: 10px">
                        <span class="input-group-addon"><label for="porcSemanas">PORCENTAJE SEMANAS:</label></span>
                        <input id="porcSemanas" name="porcSemanas" type="text" class="form-control" style="width: 100px" value="">
                    </div>
                </td>
                <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                <td>
                    <div class="col-sm-12 controls" style="margin-top: 10px; margin-left: 60px">
                        <input type="submit" class="btn btn-info btn-sm" id="btnIr" name="btnIr" title="Generar" value="> > >">
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>
<?php
if(!isset($fte) or $fte=='' or !isset($fac) or $fac == '')
{
    ?>
    <br><br>
    <div align="center">
        <label>Ingrese Fuente y Factura para generar la consulta</label>
    </div>
    <?php
}
else
{
    $fechaAc = $_POST['fechaActa'];
    list($anioAct,$mesAct,$diaAct) = explode("-",$fechaAc);

    $valorRadio = $_POST['radio'];                      $tipoImpresion = $_POST['tipoImpresion'];
    $numConsRadicado = $_POST['numConsRadicado'];       $numConsRecobro = $_POST['numConsRecobro'];
    $numeroActa = $_POST['numeroActa'];                 $fechaActa = date("d/m/Y", strtotime($fechaAc));
    $porcSemanas = $_POST['porcSemanas'];
    $wemp_pmla = $_REQUEST['wemp_pmla'];
    $wbasedato1 = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    $wnit = $wbasedato1->nit;
    $wnombre = $wbasedato1->nombre;

    $wemp_pmla = $_REQUEST['wemp_pmla'];
    $wbasedato1 = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    $wnit = $wbasedato1->nit;
    $wnombre = $wbasedato1->nombre;

    $query_o1="SELECT '' numcosec,'' numconrec,'' numtiprad,'' numradant,'EPS010' codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,
                        ateideno2,pacarsafi,'' nivcta,'' numacta,'' fechaact,'' fecsolmed,'' indperirec,'' mesper,'' anoper,'' numentre,movfue,
                        movdoc,cardetfec,mdiadia,'' porsema,'".$wnit."' nitprov,conarc,'".$wnombre."' nomprov,
                        cardetcon,cardetcod,cardetcan * conmul can,cardetvun * conmul vun,cardettot * conmul val1,0 vlrctamod,carfacval * conmul val2,cardetfue,cardetdoc,cardetite,cardetreg"
        ."	FROM famov,facarfac,facardet,msate,msateide,inmegr,inmdia,facon,inpacars"
        ."	WHERE movfue='$fte'"
        ."	and movdoc=$fac"
        ."	and movfue=carfacfue"
        ."	and movdoc=carfacdoc"
        ."	and carfacreg=cardetreg"
        ."	and cardethis=atehis"
        ."	and cardetnum=ateing"
        ."	and ateips=ateideips"
        ."	and atedoc=ateidedoc"
        ."	and cardethis=egrhis"
        ."	and cardetnum=egrnum"
        ."	and egrhis=mdiahis"
        ."	and egrnum=mdianum"
        ."	and mdiatip='P'"
        ."	and cardetcon=concod"
        ."  and egrhis=pacarshis"
        ."  and egrnum=pacarsnum"
        ."	INTO temp tmpmty119";
    $err_o = odbc_do($conex_o,$query_o1);

    //ECHO "QUERY 1 : ",$query_o1;

    $query_o11="SELECT  numcosec, numconrec, numtiprad, numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,
                        ateideno2,pacarsafi, nivcta, numacta, fechaact, fecsolmed, indperirec, mesper, anoper, numentre,movfue,
                        movdoc,cardetfec,mdiadia, porsema,nitprov,conarc,nomprov,cardetcon,cardetcod,(can*-1) can,vun,val1,vlrctamod,
                        val2,cardetfue,cardetdoc,cardetite,cardetreg"
        ."  FROM tmpmty119"
        ."  WHERE val1 < 0"
        ."  and can > 0"
        ."  UNION ALL"
        ."  SELECT  numcosec, numconrec, numtiprad, numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,
                        ateideno2,pacarsafi, nivcta, numacta, fechaact, fecsolmed, indperirec, mesper, anoper, numentre,movfue,
                        movdoc,cardetfec,mdiadia, porsema,nitprov,conarc,nomprov,cardetcon,cardetcod,can,vun,val1,vlrctamod,
                        val2,cardetfue,cardetdoc,cardetite,cardetreg"
        ."  FROM tmpmty119"
        ."  WHERE val1 >= 0"
        ."  UNION ALL"
        ."  SELECT  numcosec, numconrec, numtiprad, numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,
                        ateideno2,pacarsafi, nivcta, numacta, fechaact, fecsolmed, indperirec, mesper, anoper, numentre,movfue,
                        movdoc,cardetfec,mdiadia, porsema,nitprov,conarc,nomprov,cardetcon,cardetcod,can,vun,val1,vlrctamod,
                        val2,cardetfue,cardetdoc,cardetite,cardetreg"
        ."  FROM tmpmty119"
        ."  WHERE val1 < 0"
        ."  and can < 0"
        ."  INTO temp tmpmty";
    $err_o11 = odbc_do($conex_o, $query_o11);


    $query_o2="SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                    numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                    artcum codi,artnom nomcodi,'MD' sigla,cardetcon,cardetcod,can cardetcan,vun cardetvun,val1,vlrctamod,val2,cardetfue,
                    cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,ivdrodet,ivart"
        ."	WHERE conarc='IVARTTAR'"
        ."	AND cardetfue=drodetfue"
        ."	AND cardetdoc=drodetdoc"
        ."	AND cardetite=drodetite"
        ."	AND drodetart=artcod"
        ."	AND cardetcon='0616'"
        ."	AND artcum is not null"
        ."	UNION ALL"
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                '' codi,artnom nomcodi,'MD' sigla,cardetcon,cardetcod,can cardetcan,vun cardetvun,val1,vlrctamod,val2,cardetfue,
       	        cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,ivdrodet,ivart"
        ."	WHERE conarc='IVARTTAR'"
        ."	AND cardetfue=drodetfue"
        ."	AND cardetdoc=drodetdoc"
        ."	AND cardetite=drodetite"
        ."	AND drodetart=artcod"
        ."	AND cardetcon='0616'"
        ."	AND artcum is null"

        ."	UNION ALL"
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                '' codi,artnom nomcodi,'MD' sigla,cardetcon,cardetcod,can cardetcan,vun cardetvun,val1,vlrctamod,val2,cardetfue,
       	        cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,ivart"
        ."	WHERE cardetcod <> '0'"
        ."	AND cardetcod=artcod"
        ."	AND cardetcon='0171'"
        ."	AND artcum is null"
        ."	UNION ALL "
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                artcum codi,artnom nomcodi,'MD' sigla,cardetcon,cardetcod,can cardetcan,vun cardetvun,val1,vlrctamod,val2,cardetfue,
       	        cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,ivart"
        ."	WHERE cardetcod <> '0'"
        ."	AND cardetcod=artcod"
        ."	AND cardetcon='0171'"
        ."	AND artcum is not null"

        ."	UNION ALL "
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                cardetcod codi,pronom nomcodi,'PD' sigla,cardetcon,cardetcod,can cardetcan,vun cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,inpro"
        ."	WHERE cardetcon='0172'"
        ."    and cardetcod <> '0'"
        ."	AND cardetcod=procod"
        ."	UNION ALL "
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                cardetcod codi,connom nomcodi,'PD' sigla,cardetcon,cardetcod,can cardetcan,vun cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,facon"
        ."	WHERE cardetcon='0172'"
        ."    and cardetcod = '0'"
        ."	AND cardetcon=concod"
        ."	UNION ALL "
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                artcum codi,artnom nomcodi,'IN' sigla,cardetcon,cardetcod,can cardetcan,vun cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,ivdrodet,ivart"
        ."	WHERE conarc='IVARTTAR'"
        ."	AND cardetfue=drodetfue"
        ."	AND cardetdoc=drodetdoc"
        ."	AND cardetite=drodetite"
        ."	AND drodetart=artcod"
        ."	AND cardetcon='0626'"
        ."	UNION ALL "
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                cardetcod codi,pronom nomcodi,'PD' sigla,cardetcon,cardetcod,can cardetcan,vun cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,inpro"
        ."	WHERE conarc='INPROTAR'"
        ."	AND cardetcod=procod"
        ."	UNION ALL "
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                cardetcod codi,exanom nomcodi,'PD' sigla,cardetcon,cardetcod,can cardetcan,vun cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,inexa"
        ."	WHERE conarc='INEXATAR'"
        ."	AND cardetcod=exacod"
        //******NUEVO BLOQUE********
        ."  UNION ALL "
        ."  SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                cardetcod codi,' ' nomcodi,'PD' sigla,cardetcon,cardetcod,can cardetcan,vun cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."  FROM tmpmty"
        ."  WHERE conarc=''"
        ."	AND cardetcon='0037'"
        ."  UNION ALL "
        ."  SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                cardetcod codi,' ' nomcodi,'PD' sigla,cardetcon,cardetcod,can cardetcan,vun cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."  FROM tmpmty"
        ."  WHERE conarc is null"
        ."	AND cardetcon='0037'"
        //***********************
        ."	INTO temp tmpmty19";

    $err_o = odbc_do($conex_o,$query_o2);

    //ECHO "QUERY 2: ",$query_o2;

    $query_o33="SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                codi,nomcodi,sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg reg"
        ."	FROM tmpmty19"
        ."  INTO temp tmpmty1";

    $err_o33 = odbc_do($conex_o,$query_o33);

    //ECHO "QUERY 3:" ,$query_o33;

    $query_s1="SELECT COUNT(*) cant"
        ."  FROM tmpmty1,outer caenvdet"
        ."  WHERE movfue=envdetfan"
        ."  and movdoc=envdetdan"
        ."  into temp tmpcant";

    $err_s1 = odbc_do($conex_o,$query_s1);


    $query_s10="SELECT cant"
        ."  FROM tmpcant";

    $err_s10 = odbc_do($conex_o,$query_s10);
    odbc_fetch_row($err_s10);
    $cant = odbc_result($err_s10, 1);

    if($cant > 0)
    {
        $cant = 0;
        $query_s2 = "SELECT COUNT(*) cant"
            . "  FROM tmpmty1,caenvdet,caenvenc"
            . "  WHERE movfue=envdetfan"
            . "  and movdoc=envdetdan"
            . "  and envdetfue=envencfue"
            . "  and envdetdoc=envencdoc"
            . "  and envencanu='0'"
            . "  into temp tmpcant1";

        $err_s2 = odbc_do($conex_o, $query_s2);

        $query_s11 = "SELECT cant"
            . "  FROM tmpcant1";

        $err_s11 = odbc_do($conex_o, $query_s11);
        odbc_fetch_row($err_s11);
        $cant = odbc_result($err_s11, 1);
    }

    if($cant > 0)
    {
        $query_s3="SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
            numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movdoc,cardetfec,envdetfue,envdetrfe,mdiadia,
            porsema,nitprov,nomprov,codi,nomcodi,sigla,cardetcan,cardetvun,val1,vlrctamod,val2,reg"
            ."  FROM tmpmty1,caenvdet,caenvenc"
            ."  WHERE movfue=envdetfan"
            ."  and movdoc=envdetdan"
            ."  and envdetfue=envencfue"
            ."  and envdetdoc=envencdoc"
            ."  and envencanu='0'"
            ."  and envdetfue='80'"
            ."  into temp tmpmty2";

        $err_o1 = odbc_do($conex_o,$query_s3);

        //ECHO "query S3: (CANT > 0): ",$query_s3;
    }
    else
    {
        $query_s3="SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1, ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
            numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movdoc,cardetfec,'' envdetfue,'' envdetrfe,mdiadia,
            porsema,nitprov,nomprov,codi,nomcodi,sigla,cardetcan,cardetvun,val1,vlrctamod,val2,reg"
            ."  FROM tmpmty1"
            ."  into temp tmpmty2";

        $err_o1 = odbc_do($conex_o,$query_s3);
    }

    /*
    $queryPrueba ="SELECT COUNT(*) cant"
        . "  FROM tmpmty2";
    $err_Prueba = odbc_do($conex_o,$queryPrueba);
    $conteo = odbc_result($err_Prueba, 1);
    */

    $query_o4="SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movdoc,cardetfec,max(envdetrfe) rfe,mdiadia,porsema,nitprov,
                nomprov,codi,nomcodi,sigla,cardetcan,cardetvun,val1,vlrctamod,val2,reg"
        ."  FROM tmpmty2"
        ."  WHERE envdetrfe <> ''"
        ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,24,25,26,27,28,29,30,31,32,33,34,35,36"
        ."  UNION ALL "
        ."  SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                  numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movdoc,cardetfec,envdetrfe rfe,mdiadia,porsema,nitprov,
                  nomprov,codi,nomcodi,sigla,cardetcan,cardetvun,val1,vlrctamod,val2,reg"
        ."  FROM tmpmty2"
        ."  WHERE envdetrfe = ''"
        ."  UNION ALL "
        ."  SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                  numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movdoc,cardetfec,envdetrfe rfe,mdiadia,porsema,nitprov,
                  nomprov,codi,nomcodi,sigla,cardetcan,cardetvun,val1,vlrctamod,val2,reg"
        ."  FROM tmpmty2"
        ."  WHERE envdetrfe is null "
        ."  ORDER BY 22,28,29"
        ."  INTO temp tmpmty31";

    $err_o2 = odbc_do($conex_o,$query_o4);

    /*
    $queryPrueba2 ="SELECT COUNT(*) cant"
        . "  FROM tmpmty31";
    $err_Prueba2 = odbc_do($conex_o,$queryPrueba2);
    $conteo2 = odbc_result($err_Prueba2, 1);
    */

    $query_o5="SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movdoc,min(cardetfec) detfec,rfe,mdiadia,porsema,nitprov,
                nomprov,codi,nomcodi,sigla,SUM(cardetcan) can,cardetvun,SUM(val1) tot,vlrctamod,sum(val2) facval"
        ." FROM tmpmty31"
	    ." GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,23,24,25,26,27,28,29,30,32,34"
        ." ORDER BY 22,28,29"
        ." into temp tmpmty4";

    $err_o21 = odbc_do($conex_o,$query_o5);

    //ECHO "QUERY 5: ",$query_o5;

    //$query_o51="SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
    //            tmpmty4.rowid sec1,numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movdoc,detfec,rfe,mdiadia,porsema,nitprov,
    //            nomprov,codi,nomcodi,sigla,can,cardetvun,tot,vlrctamod,facval,tmpmty4.rowid sec2"
    //    ." FROM tmpmty4";

    $query_o51="SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                tmpmty4.rowid sec1,numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movdoc,detfec,rfe,mdiadia,porsema,nitprov,
                nomprov,codi,nomcodi,sigla,can,cardetvun,tot,vlrctamod,facval,tmpmty4.rowid sec2"
        ." FROM tmpmty4";

    $err_o3 = odbc_do($conex_o,$query_o51);

        //ECHO "QUERY 51: ",$query_o51;

	$Num_Filas = 0;

    $queryFechaFactura="SELECT movfec"
        ."  FROM famov"
        ."  WHERE movdoc = $fac";
    $datoFechaFactura = odbc_do($conex_o,$queryFechaFactura);

    if($tipoImpresion == 'DETALLADA')
    {
        if($valorRadio == 1) //CTC
        {
            ?>
            <div align="center">
                <label>CTC - Detalle</label>
                <br>
                <label>FUENTE : </label><label><?php echo $fte ?></label>
                <br>
                <label>FACTURA : </label><label><?php echo $fac ?></label>
            </div>
            <br>
            <div class="panel-body">
                <table class="table table-bordered table-list">
                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                    <tr>
                        <th class="hidden-xs">Número consecutivo interno para radicaciones de la entidad reclamante</th>
                        <th>Número consecutivo del recobro</th>
                        <th>Número de tipo de radicación</th>
                        <th>Número de radicación anterior</th>
                        <th>Código de la entidad administradora de planes de beneficio</th>
                        <th>Tipo de documento de identidad</th>
                        <th>Número de documento de identidad</th>
                        <th>Primer apellido del afiliado</th>
                        <th>Segundo apellido del afiliado</th>
                        <th>Primer nombre del afiliado</th>
                        <th>Segundo nombre del afiliado</th>
                        <th>Tipo de afiliado</th>
                        <th>Nivel de la cuota moderadora</th>
                        <th>Número del item</th>
                        <th>Número del acta del comité técnico científico</th>
                        <th>Fecha del acta del comité técnico científico</th>
                        <th>Fecha solicitud del médico</th>
                        <th>Indicador de periodicidad del recobro</th>
                        <th>Mes del periodo suministrado</th>
                        <th>Año del periodo suministrado</th>
                        <th>Número de entrega del acta del ctc para el periodo</th>
                        <th>Número de la factura que comprende el item recobrado</th>
                        <th>Fecha de la prestación del servicio</th>
                        <!--<th>Fecha de Factura</th>--> <!--SE ELIMINA ESTA COLUMNA POR REQUERIMIENTO DE SOR ANGELA: 2020-01-30-->
                        <th>Fecha de radicación de la factura ante la entidad administradora de los planes de beneficios</th>
                        <th>Código de diagnóstico según la clasificación internacional de enfermedades vigente</th>
                        <th>Porcentaje de semanas</th>
                        <th>NIT del proveedor del medicamento</th>
                        <th>Nombre del proveedor del medicamento</th>
                        <th>Código del medicamento, servicios médicos o prestaciones de salud suministrado</th>
                        <th>Nombre del medicamento, servicios médicos o prestaciones de salud suministrado</th>
                        <th>Sigla del tipo de servicio de salud prestado</th>
                        <th>Cantidad suministrada item</th>
                        <th>Valor unitario del servicio suministrado</th>
                        <th>Valor total del servicio suministrado</th>
                        <th>Valor cuota moderadora o copago</th>
                        <th>Valor final recobrado</th>
                        <th>Número del item</th>
                        <th>Nombre del medicamento, servicios medicos o prestaciones de salud suministrado</th>
                        <th>Presentacion del medicamento, servicios medicos o prestaciones de salud suministrado</th>
                        <th>Unidades diarias del medicamento, servicio medico o prestación de salud</th>
                        <th>Cantidad días que dura el servicio</th>
                        <th>Cantidad total suministrada del medicamento, servicio medico o prestación de salud</th>
                        <th>Valor unitario del servicio suministrado por item</th>
                        <th>Valor total del servicio suministrado por item</th>
                        <th>Código del medicamento, servicios médicos o prestaciones de salud similar o que sustituye</th>
                        <th>Nombre del medicamento, servicios médicos o prestaciones de salud suministrado similar o que sustituye</th>
                        <th>Unidades diarias del medicamento, servicio médico o prestación de salud que sustituye o similar</th>
                        <th>Cantidad días que dura el servicio del numeral 46</th>
                        <th>Cantidad total del medicamento, servicio médico o prestación de salud que sustituye o similar</th>
                        <th>Valor unitario del servicio que sustituye o similar</th>
                        <th>Valor total del servicio que sustituye o similar</th>
                        <th>Cantidad de Actas de CTC</th>
                        <th>Cantidad de folios correspondientes al número de actas de CTC</th>
                        <th>Cantidad de facturas</th>
                        <th>Cantidad de folios correspondientes a las facturas</th>
                        <th>Cantidad de formulas médicas</th>
                        <th>Cantidad de folios correspondientes al número de formulas médicas</th>
                        <th>Cantidad de documentos anexos al recobro</th>
                        <th>Cantidad de folios que evidencien la entrega del medicamento</th>
                        <th>Cantidad total de documentos</th>
                        <th>Cantidad total de folios</th>
                        <th>Número de radicación del formato MTY-01</th>
                        <th>NIT Proveedor Prest</th>
                        <th>Nombre Proveedor Prest</th>
                        <th>Num Factura Proveedor Prest</th>
                        <th>Cod Med Proveedor Prest</th>
                        <th>Nombre Proveedor Prest</th>
                        <th>Vlr Unit Proveedor Prest</th>
                        <th>Vlr Total Proveedor Prest</th>
                        <th>Factura tiene constancia de pago</th>
                    </tr>
                    </thead>
                    <?php
                    while (odbc_fetch_row($err_o3))
                    {
                        $Num_Filas++;

                        $fechaFac = odbc_result($datoFechaFactura, 1);
                        $fechaFactura = date("d/m/Y", strtotime($fechaFac));
                        $codent = odbc_result($err_o3, 5);//codigo entidad administradora
                        $tip = odbc_result($err_o3, 6);//tipo documento
                        $cep = odbc_result($err_o3, 7);//cedula paciente
                        $ape1pac = odbc_result($err_o3, 8);//apellido1 paciente
                        $ape2pac = odbc_result($err_o3, 9);//apellido2 paciente
                        $nom1pac = odbc_result($err_o3, 10);//nombre 1 paciente
                        if(odbc_result($err_o3, 11) == '' or odbc_result($err_o3, 11) == '               ' or odbc_result($err_o3, 11) == null){$nom2pac = 'N';}
                        else{$nom2pac = odbc_result($err_o3, 11);}//nombre 2 paciente
                        if(odbc_result($err_o3, 12) == ''){$tipoafi = 'B';}
                        else{$tipoafi = odbc_result($err_o3, 12);} //Tipo de afiliado
                        if(odbc_result($err_o3, 13) == ''){$nicuota = 'E';}
                        else{ $nicuota = odbc_result($err_o3, 13);}//Nivel de la cuota moderadora
                        $numitem = odbc_result($err_o3, 14);//Numero del item
                        $numacta = odbc_result($err_o3, 15);//Numero acta comite tecnico cientifico
                        $fecacta = odbc_result($err_o3, 16);//Fecha acta del comite tecnico cientifico
                        $fecsolm = odbc_result($err_o3, 17);//Fecha solicitud medico
                        $inperre = odbc_result($err_o3, 18);//Indicador de periodicidad del recobro
                        $mespesu = odbc_result($err_o3, 19);//Mes periodo suministrado
                        $anpersu = odbc_result($err_o3, 20);//Año periodo suministrado
                        $numenac = odbc_result($err_o3, 21);//Numero entrega acta ctc para el periodo
                        $numfaci = odbc_result($err_o3, 22);//Numero factura comprende item recobrado
                        $fechaPrestacion = odbc_result($err_o3, 23);
                        $fecpres = date("d/m/Y", strtotime($fechaPrestacion));//Fecha prestacion del servicio
                        $fecrafa = date("d/m/Y");//Fecha radicacion factura ante entidad administradora planes de beneficios
                        $codxcie = odbc_result($err_o3, 25);//Codigo diagnostico segun clasificacion internacional de enfermedades vigente
                        $porsema = odbc_result($err_o3, 26);//Porcentaje de semanas
                        $nitproo = odbc_result($err_o3, 27);//NIT proveedor medicamento
                        $nomproo = odbc_result($err_o3, 28);//Nombre proveedor medicamento
                        $sitiser = odbc_result($err_o3, 31);//Sigla tipo servicio de salud prestado

                        $nommese = odbc_result($err_o3, 30);//Nombre medicamento, servicios medicos o prestaciones de salud suministrado
                        $queryArticulo = "SELECT artuni,artcod"
                            ." FROM ivart"
                            ." WHERE artnom like '$nommese'";
                        $datoArticulo = odbc_do($conex_o, $queryArticulo);
                        $unidadArticulo = odbc_result($datoArticulo, 1);
                        $codigoArticulo = odbc_result($datoArticulo, 2);
                        switch($unidadArticulo)
                        {
                            case 'FR': $unidadArticulo = 'FRASCO'; break;
                            case 'TA': $unidadArticulo = 'TARRO'; break;
                            case 'UN': $unidadArticulo = 'UNIDAD'; break;
                            case 'PA': $unidadArticulo = 'PARES'; break;
                            case 'PQ': $unidadArticulo = 'PAQUETE'; break;
                            case 'BO': $unidadArticulo = 'BOLSA'; break;
                            case 'AM': $unidadArticulo = 'AMPOLLAS'; break;
                            case 'ML': $unidadArticulo = 'MILILITRO'; break;
                            case 'TB': $unidadArticulo = 'TABLETA'; break;
                            case 'JR': $unidadArticulo = 'JERINGA'; break;
                        }

                        if($sitiser == 'IN'){$codimed = '';}
                        else{$codimed = odbc_result($err_o3, 29);}
                        if($codimed == null or $codimed == '                  '){$codimed = $codigoArticulo;}//Codigo medicamento, servicios medicos o prestaciones de salud suministrado

                        $cansumi = odbc_result($err_o3, 32);//Cantidad suministrada item
                        $valuser = odbc_result($err_o3, 33);//Valor unitario del servicio suministrado
                        $valtose = odbc_result($err_o3, 34);//Valor total servicio suministrado

                        //VALIDAR SI EL PACIENTE TIENE COPAGO:
                        $queryCuota = "SELECT COUNT(*)"
                            ." FROM anantfac"
                            ." WHERE antfacffa = '$fte'"
                            ." AND antfacdfa = $fac"
                            ." AND antfacanu = '0'";
                        $datoCuota = odbc_do($conex_o, $queryCuota);
                        $valorCuota = odbc_result($datoCuota, 1);

                        //SI SI TIENE COPAGO, CONSULTAR ANANTFAC:
                        if($valorCuota > 0)
                        {
                            //$datoCuota = odbc_do($conex_o, $queryCuota);
                            $queryCuotaA = "SELECT SUM(antfacval)"
                                ." FROM anantfac"
                                ." WHERE antfacffa = '$fte'"
                                ." AND antfacdfa = $fac"
                                ." AND antfacanu = '0'";

                            $datoCuotaA = odbc_do($conex_o, $queryCuotaA);
                            $valcumo = odbc_result($datoCuotaA, 1);//Valor cuota moderadora o copago
                        }
                        //SI NO, LLEVAR '0' A ESE CAMPO:
                        else
                        {
                            $valcumo = '0';
                        }

                        $valfire = odbc_result($err_o3, 36);//Valor final recobrado
                        $numite2 = odbc_result($err_o3, 37);//Numero del item 2
                        ?>
                        <tbody>
                        <tr id="rowDATOS" class="alternar">
                            <td><?php echo $numConsRadicado ?></td> <!-- Numero consecutivo interno para radicaciones de la entidad reclamante -->
                            <td><?php echo $numConsRecobro ?></td> <!-- Numero consecutivo del recobro -->
                            <td>1</td> <!-- Numero de tipo de radicacion -->
                            <td>0</td> <!-- Numero de radicacion anterior -->
                            <td><?php echo $codent ?></td>
                            <td><?php echo $tip ?></td>
                            <td><?php echo $cep ?></td>
                            <td><?php echo $ape1pac ?></td>
                            <td><?php echo $ape2pac ?></td>
                            <td><?php echo $nom1pac ?></td>
                            <td><?php echo $nom2pac ?></td>
                            <td>B</td> <!-- Tipo afiliado -->
                            <td><?php echo $nicuota ?></td> <!-- Nivel de la cuota moderadora -->
                            <td><?php echo $numitem ?></td>
                            <td><?php echo $numeroActa ?></td> <!-- Numero del acta -->
                            <td><?php echo $fechaActa ?></td> <!-- Fecha acta del comite tecnico cientifico -->
                            <td><?php echo $fechaActa ?></td> <!-- Fecha solicitud medico -->
                            <td>N</td> <!-- Indicador de periodicidad -->
                            <td><?php echo $mesAct ?></td> <!-- Mes periodo suministrado -->
                            <td><?php echo $anioAct ?></td> <!-- Año periodo suministrado -->
                            <td>1</td> <!-- Numero entrega acta ctc para el periodo -->
                            <td><?php echo $numfaci ?></td>
                            <td><?php echo $fecpres ?></td> <!-- Fecha prestacion del servicio -->
                            <!--<td><?php// echo $fechaFactura ?></td>--><!-- Fecha de Factura -->
                            <td><?php echo $fecrafa ?></td> <!-- Fecha radicacion factura ante entidad administradora planes de beneficios -->
                            <td><?php echo $codxcie ?></td> <!-- Codigo diagnostico segun clasificacion internacional de enfermedades vigente -->
                            <td><?php echo $porcSemanas ?></td> <!-- Porcentaje de semanas -->
                            <td><?php echo $nitproo ?></td> <!-- NIT proveedor medicamento -->
                            <td><?php echo $nomproo ?></td> <!-- Nombre proveedor medicamento -->
                            <td><?php echo $codimed ?></td> <!-- Codigo medicamento, servicios medicos o prestaciones de salud suministrado -->
                            <td><?php echo $nommese ?></td> <!-- Nombre medicamento, servicios medicos o prestaciones de salud suministrado -->
                            <td><?php echo $sitiser ?></td> <!-- Sigla tipo servicio de salud prestado -->
                            <td><?php echo intval($cansumi) ?></td> <!-- Cantidad suministrada item -->
                            <td><?php echo $valuser ?></td> <!-- Valor unitario del servicio suministrado -->
                            <td><?php echo $valtose ?></td> <!-- Valor total servicio suministrado -->
                            <td><?php echo $valcumo ?></td> <!-- Valor cuota moderadora o copago -->
                            <td><?php echo $valfire ?></td> <!-- Valor final recobrado -->
                            <td><?php echo $numite2 ?></td> <!-- Numero del item 2 -->
                            <td><?php echo $nommese ?></td> <!-- Nombre del medicamento, servicios medicos o prestaciones de salud suministrado 2 -->
                            <td><?php echo $unidadArticulo ?></td> <!-- Presentacion del medicamento, servicios medicos o prestaciones de salud suministrado -->
                            <td>&nbsp;</td> <!-- Unidades diarias del medicamento, servicio medico o prestación de salud -->
                            <td>&nbsp;</td> <!-- Cantidad días que dura el servicio -->
                            <td><?php echo intval($cansumi) ?></td> <!-- Cantidad total suministrada del medicamento, servicio medico o prestación de salud -->
                            <td><?php echo $valuser ?></td> <!-- Valor unitario del servicio suministrado por item -->
                            <td><?php echo $valtose ?></td> <!-- Valor total del servicio suministrado por item -->
                            <td>&nbsp;</td> <!-- Código del medicamento, servicios médicos o prestaciones de salud similar o que sustituye -->
                            <td>&nbsp;</td> <!-- Nombre del medicamento, servicios médicos o prestaciones de salud suministrado similar o que sustituye -->
                            <td>&nbsp;</td> <!-- Unidades diarias del medicamento, servicio médico o prestación de salud que sustituye o similar -->
                            <td>&nbsp;</td> <!-- Cantidad días que dura el servicio del numeral 46 -->
                            <td>&nbsp;</td> <!-- Cantidad total del medicamento, servicio médico o prestación de salud que sustituye o similar -->
                            <td>&nbsp;</td> <!-- Valor unitario del servicio que sustituye o similar -->
                            <td>&nbsp;</td> <!-- Valor total del servicio que sustituye o similar -->
                            <td>1</td> <!-- Cantidad de Actas de CTC -->
                            <td>&nbsp;</td> <!-- Cantidad de folios correspondientes al número de actas de CTC -->
                            <td>1</td> <!-- Cantidad de facturas -->
                            <td>3</td> <!-- Cantidad de folios correspondientes a las facturas -->
                            <td>&nbsp;</td> <!-- Cantidad de formulas médicas -->
                            <td>&nbsp;</td> <!-- Cantidad de folios correspondientes al número de formulas médicas -->
                            <td>&nbsp;</td> <!-- Cantidad de documentos anexos al recobro -->
                            <td>&nbsp;</td> <!-- Cantidad de folios que evidencien la entrega del medicamento -->
                            <td>&nbsp;</td> <!-- Cantidad total de documentos -->
                            <td>&nbsp;</td> <!-- Cantidad total de folios -->
                            <td>&nbsp;</td> <!-- Número de radicación del formato MTY-01 -->
                            <td>&nbsp;</td> <!-- NIT Proveedor Prest -->
                            <td>&nbsp;</td> <!-- Nombre Proveedor Prest -->
                            <td>&nbsp;</td> <!-- Num Factura Proveedor Prest -->
                            <td>&nbsp;</td> <!-- Cod Med Proveedor Prest -->
                            <td>&nbsp;</td> <!-- Nombre Proveedor Prest -->
                            <td>&nbsp;</td> <!-- Vlr Unit Proveedor Prest -->
                            <td>&nbsp;</td> <!-- Vlr Total Proveedor Prest -->
                            <td>1</td> <!-- Factura tiene constancia de pago -->
                        </tr>
                        </tbody>
                        <?php
                    }
                    ?>
                </table>
            </div>
            <?php
        }
        elseif($valorRadio == 2) //TUTELA
        {
            ?>
            <div align="center">
                <label>TUTELA</label>
                <br>
                <label>FUENTE : </label><label><?php echo $fte ?></label>
                <br>
                <label>FACTURA : </label><label><?php echo $fac ?></label>
            </div>
            <br>
            <div class="panel-body">
                <table class="table table-bordered table-list">
                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                    <tr>
                        <th class="hidden-xs">Número consecutivo interno para radicaciones de la entidad reclamante</th>
                        <th>Número consecutivo del recobro</th>
                        <th>Tipo de radicación</th>
                        <th>Número de radicación anterior</th>
                        <th>Código de la entidad administradora de planes de beneficio</th>
                        <th>Tipo de documento de identidad</th>
                        <th>Número de documento de identidad</th>
                        <th>Primer apellido del afiliado</th>
                        <th>Segundo apellido del afiliado</th>
                        <th>Primer nombre del afiliado</th>
                        <th>Segundo nombre del afiliado</th>
                        <th>Tipo de afiliado</th>
                        <th>Nivel de la cuota moderadora</th>
                        <th>Nivel de la cuota de recuperación</th>
                        <th>Número del item</th>
                        <th>Número del fallo</th>
                        <th>Fecha del fallo</th>
                        <th>Número de autoridad judicial</th>
                        <th>Tipo de autoridad judicial</th>
                        <th>Municipio de ubicacion autoridad judicial</th>
                        <th>Codigo causa del recobro</th>
                        <th>Fecha solicitud del médico</th>
                        <th>Indicador de periodicidad del recobro</th>
                        <th>Mes del periodo suministrado</th>
                        <th>Año del periodo suministrado</th>
                        <th>Número de entrega de lo ordenado por el fallo de tutela</th>
                        <th>Número de la factura que comprende el item recobrado</th>
                        <th>Fecha de la prestación del servicio</th>
                        <th>Fecha de radicación de la factura ante la entidad administradora de los planes de beneficios</th>
                        <th>Código de diagnóstico según la clasificación internacional de enfermedades vigente</th>
                        <th>Porcentaje de semanas</th>
                        <th>NIT del proveedor del medicamento</th>
                        <th>Nombre del proveedor del medicamento</th>
                        <th>Código del medicamento, servicios médicos o prestaciones de salud suministrado</th>
                        <th>Nombre del medicamento, servicios médicos o prestaciones de salud suministrado</th>
                        <th>Sigla del tipo de servicio de salud prestado</th>
                        <th>Cantidad suministrada item</th>
                        <th>Valor unitario del servicio suministrado</th>
                        <th>Valor total del servicio suministrado</th>
                        <th>Valor cuota moderadora o copago</th>
                        <th>Valor final recobrado</th>
                        <th>Número del item</th>
                        <th>Nombre del medicamento, servicios medicos o prestaciones de salud suministrado</th>
                        <th>Presentacion del medicamento, servicios medicos o prestaciones de salud suministrado</th>
                        <th>Unidades diarias del medicamento, servicio medico o prestación de salud</th>
                        <th>Cantidad días que dura el servicio</th>
                        <th>Cantidad total suministrada del medicamento, servicio medico o prestación de salud</th>
                        <th>Valor unitario del servicio suministrado por item</th>
                        <th>Valor total del servicio suministrado por item</th>
                        <th>Código del medicamento, servicios médicos o prestaciones de salud similar o que sustituye</th>
                        <th>Nombre del medicamento, servicios médicos o prestaciones de salud suministrado similar o que sustituye</th>
                        <th>Unidades diarias del medicamento, servicio médico o prestación de salud que sustituye o similar</th>
                        <th>Cantidad días que dura el servicio del numeral 46</th>
                        <th>Cantidad total del medicamento, servicio médico o prestación de salud que sustituye o similar</th>
                        <th>Valor unitario del servicio que sustituye o similar</th>
                        <th>Valor total del servicio que sustituye o similar</th>
                        <th>Cantidad de fallos de tutela</th>
                        <th>Cantidad de folios correspondientes a los fallos de tutela</th>
                        <th>Cantidad de facturas</th>
                        <th>Cantidad de folios correspondientes a las facturas</th>
                        <th>Cantidad de documentos anexos al recobro</th>
                        <th>Cantidad de folios que evidencien la entrega del medicamento</th>
                        <th>Cantidad total de documentos</th>
                        <th>Cantidad total de folios</th>
                        <th>No de radicación recobro anterior (formato MYT-02)</th>
                        <th>NIT Proveedor Prest</th>
                        <th>Nombre Proveedor Prest</th>
                        <th>Num Factura Proveedor Prest</th>
                        <th>Cod Med Proveedor Prest</th>
                        <th>Nombre Med Proveedor Prest</th>
                        <th>Vlr Unit Proveedor Prest</th>
                        <th>Vlr Total Proveedor Prest</th>
                        <th>Factura tiene constancia de pago</th>
                    </tr>
                    </thead>
                    <?php
                    while (odbc_fetch_row($err_o))
                    {
                        $Num_Filas++;

                        $codent = odbc_result($err_o, 5);//codigo entidad administradora
                        $tip = odbc_result($err_o, 6);//tipo documento
                        $cep = odbc_result($err_o, 7);//cedula paciente
                        $ape1pac = odbc_result($err_o, 8);//apellido1 paciente
                        $ape2pac = odbc_result($err_o, 9);//apellido2 paciente
                        $nom1pac = odbc_result($err_o, 10);//nombre 1 paciente
                        if(odbc_result($err_o, 11) == ''){$nom2pac = 'N';}
                        else{$nom2pac = odbc_result($err_o, 11);} //nombre 2 paciente
                        if(odbc_result($err_o, 12) == ''){$tipoafi = 'B';}
                        else{$tipoafi = odbc_result($err_o, 12);} //Tipo de afiliado
                        if(odbc_result($err_o, 13) == ''){$nicuota = 'E';}
                        else{ $nicuota = odbc_result($err_o, 13);}//Nivel de la cuota moderadora
                        $numitem = odbc_result($err_o, 14);//Numero del item
                        $numacta = odbc_result($err_o, 15);//Numero acta comite tecnico cientifico
                        $fecacta = odbc_result($err_o, 16);//Fecha acta del comite tecnico cientifico
                        $fecsolm = odbc_result($err_o, 17);//Fecha solicitud medico
                        $inperre = odbc_result($err_o, 18);//Indicador de periodicidad del recobro
                        $mespesu = odbc_result($err_o, 19);//Mes periodo suministrado
                        $anpersu = odbc_result($err_o, 20);//Año periodo suministrado
                        $numenac = odbc_result($err_o, 21);//Numero entrega acta ctc para el periodo
                        $numfaci = odbc_result($err_o, 22);//Numero factura comprende item recobrado
                        $fechaPrestacion = odbc_result($err_o, 23);
                        $fecpres = date("d/m/Y", strtotime($fechaPrestacion));//Fecha prestacion del servicio
                        $fecrafa = date("d/m/Y");//Fecha radicacion factura ante entidad administradora planes de beneficios
                        $codxcie = odbc_result($err_o, 25);//Codigo diagnostico segun clasificacion internacional de enfermedades vigente
                        $porsema = odbc_result($err_o, 26);//Porcentaje de semanas
                        $nitproo = odbc_result($err_o, 27);//NIT proveedor medicamento
                        $nomproo = odbc_result($err_o, 28);//Nombre proveedor medicamento
                        $sitiser = odbc_result($err_o, 31);//Sigla tipo servicio de salud prestado
                        $nommese = odbc_result($err_o, 30);//Nombre medicamento, servicios medicos o prestaciones de salud suministrado

                        $queryArticulo = "SELECT artuni,artcod"
                            ." FROM ivart"
                            ." WHERE artnom like '$nommese'";
                        $datoArticulo = odbc_do($conex_o, $queryArticulo);
                        $unidadArticulo = odbc_result($datoArticulo, 1);
                        $codigoArticulo = odbc_result($datoArticulo, 2);
                        switch($unidadArticulo)
                        {
                            case 'FR': $unidadArticulo = 'FRASCO'; break;
                            case 'TA': $unidadArticulo = 'TARRO'; break;
                            case 'UN': $unidadArticulo = 'UNIDAD'; break;
                            case 'PA': $unidadArticulo = 'PARES'; break;
                            case 'PQ': $unidadArticulo = 'PAQUETE'; break;
                            case 'BO': $unidadArticulo = 'BOLSA'; break;
                            case 'AM': $unidadArticulo = 'AMPOLLAS'; break;
                            case 'ML': $unidadArticulo = 'MILILITRO'; break;
                            case 'TB': $unidadArticulo = 'TABLETA'; break;
                            case 'JR': $unidadArticulo = 'JERINGA'; break;
                        }

                        if($sitiser == 'IN'){$codimed = '';}
                        else{$codimed = odbc_result($err_o, 29);}
                        if($codimed == null or $codimed == '                  '){$codimed = $codigoArticulo;}//Codigo medicamento, servicios medicos o prestaciones de salud suministrado

                        $cansumi = odbc_result($err_o, 32);//Cantidad suministrada item
                        $valuser = odbc_result($err_o, 33);//Valor unitario del servicio suministrado
                        $valtose = odbc_result($err_o, 34);//Valor total servicio suministrado

                        //VALIDAR SI EL PACIENTE TIENE COPAGO:
                        $queryCuota = "SELECT COUNT(*)"
                            ." FROM anantfac"
                            ." WHERE antfacffa = '$fte'"
                            ." AND antfacdfa = $fac"
                            ." AND antfacanu = '0'";
                        $datoCuota = odbc_do($conex_o, $queryCuota);
                        $valorCuota = odbc_result($datoCuota, 1);

                        //SI SI TIENE COPAGO, CONSULTAR ANANTFAC:
                        if($valorCuota > 0)
                        {
                            //$datoCuota = odbc_do($conex_o, $queryCuota);
                            $queryCuotaA = "SELECT SUM(antfacval)"
                                ." FROM anantfac"
                                ." WHERE antfacffa = '$fte'"
                                ." AND antfacdfa = $fac"
                                ." AND antfacanu = '0'";

                            $datoCuotaA = odbc_do($conex_o, $queryCuotaA);
                            $valcumo = odbc_result($datoCuotaA, 1);//Valor cuota moderadora o copago
                        }
                        //SI NO, LLEVAR '0' A ESE CAMPO:
                        else
                        {
                            $valcumo = '0';
                        }

                        //$valcumo = odbc_result($err_o, 35);//Valor cuota moderadora o copago
                        $valfire = odbc_result($err_o, 36);//Valor final recobrado
                        $numite2 = odbc_result($err_o, 37);//Numero del item 2
                        ?>
                        <tbody>
                        <tr id="rowDATOS" class="alternar">
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><?php echo $codent ?></td>
                            <td><?php echo $tip ?></td>
                            <td><?php echo $cep ?></td>
                            <td><?php echo $ape1pac ?></td>
                            <td><?php echo $ape2pac ?></td>
                            <td><?php echo $nom1pac ?></td>
                            <td><?php echo $nom2pac ?></td>
                            <td><?php echo $tipoafi ?></td>
                            <td><?php echo $nicuota ?></td>
                            <td>&nbsp;</td>
                            <td><?php echo $numitem ?></td> <!-- Número del item -->
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><?php echo $fecsolm ?></td> <!-- Fecha solicitud medico -->
                            <td><?php echo $inperre ?></td> <!-- Indicador de periodicidad del recobro -->
                            <td><?php echo $mespesu ?></td> <!-- Mes periodo suministrado -->
                            <td><?php echo $anpersu ?></td> <!-- Año periodo suministrado -->
                            <td>&nbsp;</td>
                            <td><?php echo $numfaci ?></td> <!-- Numero factura comprende item recobrado -->
                            <td><?php echo $fecpres ?></td> <!-- Fecha prestacion del servicio -->
                            <td><?php echo $fecrafa ?></td> <!-- Fecha radicacion factura ante entidad administradora planes de beneficios -->
                            <td><?php echo $codxcie ?></td> <!-- Codigo diagnostico segun clasificacion internacional de enfermedades vigente -->
                            <td>&nbsp;</td>
                            <td><?php echo $nitproo ?></td> <!-- NIT proveedor medicamento -->
                            <td><?php echo $nomproo ?></td> <!-- Nombre proveedor medicamento -->
                            <td><?php echo $codimed ?></td> <!-- Codigo medicamento, servicios medicos o prestaciones de salud suministrado -->
                            <td><?php echo $nommese ?></td> <!-- Nombre medicamento, servicios medicos o prestaciones de salud suministrado -->
                            <td><?php echo $sitiser ?></td> <!-- Sigla tipo servicio de salud prestado -->
                            <td><?php echo intval($cansumi) ?></td> <!-- Cantidad suministrada item -->
                            <td><?php echo $valuser ?></td> <!-- Valor unitario del servicio suministrado -->
                            <td><?php echo $valtose ?></td> <!-- Valor total servicio suministrado -->
                            <td><?php echo $valcumo ?></td> <!-- Valor cuota moderadora o copago -->
                            <td><?php echo $valfire ?></td> <!-- Valor final recobrado -->
                            <td><?php echo $numite2 ?></td> <!-- Numero del item 2 -->
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        </tbody>
                        <?php
                    }
                    ?>
                </table>
            </div>
            <?php
        }
    }
    else //RESUMIDA
    {
        if($valorRadio == 1) //CTC
        {
            ?>
            <div align="center">
                <label>CTC - Resumen</label>
                <br>
                <label>FUENTE : </label><label><? echo $fte ?></label>
                <br>
                <label>FACTURA : </label><label><? echo $fac ?></label>
            </div>
            <br>
            <div class="panel-body">
                <table class="table table-bordered table-list">
                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                    <tr>
                        <th class="hidden-xs">Número consecutivo interno para radicaciones de la entidad reclamante</th>
                        <th>Número consecutivo del recobro</th>
                        <th>Número de tipo de radicación</th>
                        <th>Número de radicación anterior</th>
                        <th>Código de la entidad administradora de planes de beneficio</th>
                        <th>Tipo de documento de identidad</th>
                        <th>Número de documento de identidad</th>
                        <th>Primer apellido del afiliado</th>
                        <th>Segundo apellido del afiliado</th>
                        <th>Primer nombre del afiliado</th>
                        <th>Segundo nombre del afiliado</th>
                        <th>Tipo de afiliado</th>
                        <th>Nivel de la cuota moderadora</th>
                        <th>Número del item</th>
                        <th>Número del acta del comité técnico científico</th>
                        <th>Fecha del acta del comité técnico científico</th>
                        <th>Fecha solicitud del médico</th>
                        <th>Indicador de periodicidad del recobro</th>
                        <th>Mes del periodo suministrado</th>
                        <th>Año del periodo suministrado</th>
                        <th>Número de entrega del acta del ctc para el periodo</th>
                        <th>Número de la factura que comprende el item recobrado</th>
                        <th>Fecha de la prestación del servicio</th>
                        <th>Fecha de Factura</th>
                        <th>Fecha de radicación de la factura ante la entidad administradora de los planes de beneficios</th>
                        <th>Código de diagnóstico según la clasificación internacional de enfermedades vigente</th>
                        <th>Porcentaje de semanas</th>
                        <th>NIT del proveedor del medicamento</th>
                        <th>Nombre del proveedor del medicamento</th>
                        <th>Código del medicamento, servicios médicos o prestaciones de salud suministrado</th>
                        <th>Nombre del medicamento, servicios médicos o prestaciones de salud suministrado</th>
                        <th>Sigla del tipo de servicio de salud prestado</th>
                        <th>Cantidad suministrada item</th>
                        <th>Valor unitario del servicio suministrado</th>
                        <th>Valor total del servicio suministrado</th>
                        <th>Valor cuota moderadora o copago</th>
                        <th>Valor final recobrado</th>
                        <th>Número del item</th>
                        <th>Nombre del medicamento, servicios medicos o prestaciones de salud suministrado</th>
                        <th>Presentacion del medicamento, servicios medicos o prestaciones de salud suministrado</th>
                        <th>Unidades diarias del medicamento, servicio medico o prestación de salud</th>
                        <th>Cantidad días que dura el servicio</th>
                        <th>Cantidad total suministrada del medicamento, servicio medico o prestación de salud</th>
                        <th>Valor unitario del servicio suministrado por item</th>
                        <th>Valor total del servicio suministrado por item</th>
                        <th>Código del medicamento, servicios médicos o prestaciones de salud similar o que sustituye</th>
                        <th>Nombre del medicamento, servicios médicos o prestaciones de salud suministrado similar o que sustituye</th>
                        <th>Unidades diarias del medicamento, servicio médico o prestación de salud que sustituye o similar</th>
                        <th>Cantidad días que dura el servicio del numeral 46</th>
                        <th>Cantidad total del medicamento, servicio médico o prestación de salud que sustituye o similar</th>
                        <th>Valor unitario del servicio que sustituye o similar</th>
                        <th>Valor total del servicio que sustituye o similar</th>
                        <th>Cantidad de Actas de CTC</th>
                        <th>Cantidad de folios correspondientes al número de actas de CTC</th>
                        <th>Cantidad de facturas</th>
                        <th>Cantidad de folios correspondientes a las facturas</th>
                        <th>Cantidad de formulas médicas</th>
                        <th>Cantidad de folios correspondientes al número de formulas médicas</th>
                        <th>Cantidad de documentos anexos al recobro</th>
                        <th>Cantidad de folios que evidencien la entrega del medicamento</th>
                        <th>Cantidad total de documentos</th>
                        <th>Cantidad total de folios</th>
                        <th>Número de radicación del formato MTY-01</th>
                        <th>NIT Proveedor Prest</th>
                        <th>Nombre Proveedor Prest</th>
                        <th>Num Factura Proveedor Prest</th>
                        <th>Cod Med Proveedor Prest</th>
                        <th>Nombre Proveedor Prest</th>
                        <th>Vlr Unit Proveedor Prest</th>
                        <th>Vlr Total Proveedor Prest</th>
                        <th>Factura tiene constancia de pago</th>
                    </tr>
                    </thead>
                    <?php
                    while (odbc_fetch_row($err_o3))
                    {
                        $Num_Filas++;

                        $querySumaTotal = "SELECT sum(tot)"
                            . "  FROM tmpmty4"
                            . "  WHERE movdoc = $fac"
							. "    AND tot>0";
                        $datoSumaTotal = odbc_do($conex_o, $querySumaTotal);
						
						$querySumaTotal1 = "SELECT sum(tot)"
                            . "  FROM tmpmty4"
                            . "  WHERE movdoc = $fac";
					    $datoSumaTotal1 = odbc_do($conex_o, $querySumaTotal1);

                        $fechaFac = odbc_result($datoFechaFactura, 1);
                        $fechaFactura = date("d/m/Y", strtotime($fechaFac));
                        $codent = odbc_result($err_o3, 5);//codigo entidad administradora
                        $tip = odbc_result($err_o3, 6);//tipo documento
                        $cep = odbc_result($err_o3, 7);//cedula paciente
                        $ape1pac = odbc_result($err_o3, 8);//apellido1 paciente
                        $ape2pac = odbc_result($err_o3, 9);//apellido2 paciente
                        $nom1pac = odbc_result($err_o3, 10);//nombre 1 paciente
                        if (odbc_result($err_o3, 11) == '') {$nom2pac = 'N';} else {$nom2pac = odbc_result($err_o3, 11);} //nombre 2 paciente
                        if (odbc_result($err_o3, 12) == '') {$tipoafi = 'B';} else {$tipoafi = odbc_result($err_o3, 12);} //Tipo de afiliado
                        if (odbc_result($err_o3, 13) == '') {$nicuota = 'E';} else {$nicuota = odbc_result($err_o3, 13);}//Nivel de la cuota moderadora
                        $numitem = odbc_result($err_o3, 14);//Numero del item
                        $numacta = odbc_result($err_o3, 15);//Numero acta comite tecnico cientifico
                        $fecacta = odbc_result($err_o3, 16);//Fecha acta del comite tecnico cientifico
                        $fecsolm = odbc_result($err_o3, 17);//Fecha solicitud medico
                        $inperre = odbc_result($err_o3, 18);//Indicador de periodicidad del recobro
                        $mespesu = odbc_result($err_o3, 19);//Mes periodo suministrado
                        $anpersu = odbc_result($err_o3, 20);//Año periodo suministrado
                        $numenac = odbc_result($err_o3, 21);//Numero entrega acta ctc para el periodo
                        $numfaci = odbc_result($err_o3, 22);//Numero factura comprende item recobrado
                        $fechaPrestacion = odbc_result($err_o3, 23);
                        $fecpres = date("d/m/Y", strtotime($fechaPrestacion));//Fecha prestacion del servicio
                        $fecrafa = date("d/m/Y");//Fecha radicacion factura ante entidad administradora planes de beneficios
                        $codxcie = odbc_result($err_o3, 25);//Codigo diagnostico segun clasificacion internacional de enfermedades vigente
                        $porsema = odbc_result($err_o3, 26);//Porcentaje de semanas
                        $nitproo = odbc_result($err_o3, 27);//NIT proveedor medicamento
                        $nomproo = odbc_result($err_o3, 28);//Nombre proveedor medicamento
                        $sitiser = odbc_result($err_o3, 31);//Sigla tipo servicio de salud prestado
                        if ($sitiser == 'IN') {$codimed = '';} else {$codimed = odbc_result($err_o3, 29);}//Codigo medicamento, servicios medicos o prestaciones de salud suministrado
                        $nommese = odbc_result($err_o3, 30);//Nombre medicamento, servicios medicos o prestaciones de salud suministrado
                        $cansumi = odbc_result($err_o3, 32);//Cantidad suministrada item
                        $valuser = odbc_result($datoSumaTotal, 1);//Valor unitario del servicio suministrado
                        //$valuser = odbc_result($err_o3, 33);//Valor unitario del servicio suministrado
                        $valtose = odbc_result($datoSumaTotal, 1);//Valor total servicio suministrado
                        //$valtose = odbc_result($err_o3, 34);//Valor total servicio suministrado
                        $valcumo = odbc_result($err_o3, 35);//Valor cuota moderadora o copago
                        $valfire = odbc_result($datoSumaTotal1, 1);//Valor final recobrado
                        //$valfire = odbc_result($err_o3, 36);//Valor final recobrado
                        $numite2 = odbc_result($err_o3, 37);//Numero del item 2
                    }
                    ?>
                    <tbody>
                    <tr id="rowDATOS" class="alternar">
                        <td><?php echo $numConsRadicado ?></td> <!-- Numero consecutivo interno para radicaciones de la entidad reclamante -->
                        <td><?php echo $numConsRecobro ?></td> <!-- Numero consecutivo del recobro -->
                        <td>1</td> <!-- Numero de tipo de radicacion -->
                        <td>0</td> <!-- Numero de radicacion anterior -->
                        <td><?php echo $codent ?></td>
                        <td><?php echo $tip ?></td>
                        <td><?php echo $cep ?></td>
                        <td><?php echo $ape1pac ?></td>
                        <td><?php echo $ape2pac ?></td>
                        <td><?php echo $nom1pac ?></td>
                        <td><?php echo $nom2pac ?></td>
                        <td>B</td> <!-- Tipo afiliado -->
                        <td><?php echo $nicuota ?></td> <!-- Nivel de la cuota moderadora -->
                        <td>1</td> <!-- Numero del item -->
                        <td><?php echo $numeroActa ?></td> <!-- Numero del acta -->
                        <td><?php echo $fechaActa ?></td> <!-- Fecha acta del comite tecnico cientifico -->
                        <td><?php echo $fechaActa ?></td> <!-- Fecha solicitud medico -->
                        <td>N</td> <!-- Indicador de periodicidad -->
                        <td><?php echo $mesAct ?></td> <!-- Mes periodo suministrado -->
                        <td><?php echo $anioAct ?></td> <!-- Año periodo suministrado -->
                        <td>1</td> <!-- Numero entrega acta ctc para el periodo -->
                        <td><?php echo $numfaci ?></td>
                        <td><?php echo $fecpres ?></td> <!-- Fecha prestacion del servicio -->
                        <td><?php echo $fechaFactura ?></td><!-- Fecha de Factura -->
                        <td><?php echo $fecrafa ?></td> <!-- Fecha radicacion factura ante entidad administradora planes de beneficios -->
                        <td><?php echo $codxcie ?></td> <!-- Codigo diagnostico segun clasificacion internacional de enfermedades vigente -->
                        <td><?php echo $porcSemanas ?></td> <!-- Porcentaje de semanas -->
                        <td><?php echo $nitproo ?></td> <!-- NIT proveedor medicamento -->
                        <td><?php echo $nomproo ?></td> <!-- Nombre proveedor medicamento -->
                        <td> </td> <!-- Codigo medicamento, servicios medicos o prestaciones de salud suministrado -->
                        <td>Procedimientos No POS</td> <!-- Nombre medicamento, servicios medicos o prestaciones de salud suministrado -->
                        <td><?php echo $sitiser ?></td> <!-- Sigla tipo servicio de salud prestado -->
                        <td>1</td> <!-- Cantidad suministrada item -->
                        <td><?php echo $valuser ?></td> <!-- Valor unitario del servicio suministrado -->
                        <td><?php echo $valtose ?></td> <!-- Valor total servicio suministrado -->
                        <td><?php echo $valcumo ?></td> <!-- Valor cuota moderadora o copago -->
                        <td><?php echo $valfire ?></td> <!-- Valor final recobrado -->
                        <td><?php echo $numite2 ?></td> <!-- Numero del item 2 -->
                        <td><?php echo $nommese ?></td> <!-- Nombre del medicamento, servicios medicos o prestaciones de salud suministrado 2 -->
                        <td>&nbsp;</td> <!-- Presentacion del medicamento, servicios medicos o prestaciones de salud suministrado -->
                        <td>&nbsp;</td> <!-- Unidades diarias del medicamento, servicio medico o prestación de salud -->
                        <td>&nbsp;</td> <!-- Cantidad días que dura el servicio -->
                        <td>&nbsp;</td> <!-- Cantidad total suministrada del medicamento, servicio medico o prestación de salud -->
                        <td>&nbsp;</td> <!-- Valor unitario del servicio suministrado por item -->
                        <td>&nbsp;</td> <!-- Valor total del servicio suministrado por item -->
                        <td>&nbsp;</td> <!-- Código del medicamento, servicios médicos o prestaciones de salud similar o que sustituye -->
                        <td>&nbsp;</td> <!-- Nombre del medicamento, servicios médicos o prestaciones de salud suministrado similar o que sustituye -->
                        <td>&nbsp;</td> <!-- Unidades diarias del medicamento, servicio médico o prestación de salud que sustituye o similar -->
                        <td>&nbsp;</td> <!-- Cantidad días que dura el servicio del numeral 46 -->
                        <td>&nbsp;</td> <!-- Cantidad total del medicamento, servicio médico o prestación de salud que sustituye o similar -->
                        <td>&nbsp;</td> <!-- Valor unitario del servicio que sustituye o similar -->
                        <td>&nbsp;</td> <!-- Valor total del servicio que sustituye o similar -->
                        <td>&nbsp;</td> <!-- Cantidad de Actas de CTC -->
                        <td>&nbsp;</td> <!-- Cantidad de folios correspondientes al número de actas de CTC -->
                        <td>&nbsp;</td> <!-- Cantidad de facturas -->
                        <td>&nbsp;</td> <!-- Cantidad de folios correspondientes a las facturas -->
                        <td>&nbsp;</td> <!-- Cantidad de formulas médicas -->
                        <td>&nbsp;</td> <!-- Cantidad de folios correspondientes al número de formulas médicas -->
                        <td>&nbsp;</td> <!-- Cantidad de documentos anexos al recobro -->
                        <td>&nbsp;</td> <!-- Cantidad de folios que evidencien la entrega del medicamento -->
                        <td>&nbsp;</td> <!-- Cantidad total de documentos -->
                        <td>&nbsp;</td> <!-- Cantidad total de folios -->
                        <td>&nbsp;</td> <!-- Número de radicación del formato MTY-01 -->
                        <td>&nbsp;</td> <!-- NIT Proveedor Prest -->
                        <td>&nbsp;</td> <!-- Nombre Proveedor Prest -->
                        <td>&nbsp;</td> <!-- Num Factura Proveedor Prest -->
                        <td>&nbsp;</td> <!-- Cod Med Proveedor Prest -->
                        <td>&nbsp;</td> <!-- Nombre Proveedor Prest -->
                        <td>&nbsp;</td> <!-- Vlr Unit Proveedor Prest -->
                        <td>&nbsp;</td> <!-- Vlr Total Proveedor Prest -->
                        <td>&nbsp;</td> <!-- Factura tiene constancia de pago -->
                    </tr>
					<?php

                    // VERIFICAMOS SI HAY NULOS:
                    $queryNulos = "SELECT COUNT(*) cant"
                        . " FROM tmpmty4"
                        . " WHERE tot < 0";

                    $datoNulos = odbc_do($conex_o, $queryNulos);

                    while(odbc_fetch_row($datoNulos))
                    {
                        $conteo = odbc_result($datoNulos, 1);

                        // SI NO HAY NULOS:
                        if($conteo != 0)
                        {
                            $querySumaTotal2 = "SELECT sum(tot*-1)"
                                . "  FROM tmpmty4"
                                . "  WHERE movdoc = $fac"
                                . "  AND tot < 0";
                            $datoSumaTotal2 = odbc_do($conex_o, $querySumaTotal2);

                            $valuse = odbc_result($datoSumaTotal2, 1);//Valor unitario del servicio suministrado
                            $valuser = str_replace('.00000000', '.00', $valuse);
                            $valtos = odbc_result($datoSumaTotal2, 1);//Valor total servicio suministrado
                            $valtose = str_replace('.00000000', '.00', $valtos);
                            $valfir = odbc_result($datoSumaTotal2, 1);//Valor final recobrado
                            $valfire = str_replace('.00000000', '.00', $valfir);
                            ?>
                            <tr id="rowDATOS" class="alternar">
                                <td><?php echo $numConsRadicado ?></td> <!-- Numero consecutivo interno para radicaciones de la entidad reclamante -->
                                <td><?php echo $numConsRecobro ?></td> <!-- Numero consecutivo del recobro -->
                                <td>1</td> <!-- Numero de tipo de radicacion -->
                                <td>0</td> <!-- Numero de radicacion anterior -->
                                <td><?php echo $codent ?></td>
                                <td><?php echo $tip ?></td>
                                <td><?php echo $cep ?></td>
                                <td><?php echo $ape1pac ?></td>
                                <td><?php echo $ape2pac ?></td>
                                <td><?php echo $nom1pac ?></td>
                                <td><?php echo $nom2pac ?></td>
                                <td>B</td> <!-- Tipo afiliado -->
                                <td><?php echo $nicuota ?></td> <!-- Nivel de la cuota moderadora -->
                                <td>1</td> <!-- Numero del item -->
                                <td><?php echo $numeroActa ?></td> <!-- Numero del acta -->
                                <td><?php echo $fechaActa ?></td> <!-- Fecha acta del comite tecnico cientifico -->
                                <td><?php echo $fechaActa ?></td> <!-- Fecha solicitud medico -->
                                <td>N</td> <!-- Indicador de periodicidad -->
                                <td><?php echo $mesAct ?></td> <!-- Mes periodo suministrado -->
                                <td><?php echo $anioAct ?></td> <!-- Año periodo suministrado -->
                                <td>1</td> <!-- Numero entrega acta ctc para el periodo -->
                                <td><?php echo $numfaci ?></td>
                                <td><?php echo $fecpres ?></td> <!-- Fecha prestacion del servicio -->
                                <td><?php echo $fechaFactura ?></td><!-- Fecha de Factura -->
                                <td><?php echo $fecrafa ?></td> <!-- Fecha radicacion factura ante entidad administradora planes de beneficios -->
                                <td><?php echo $codxcie ?></td> <!-- Codigo diagnostico segun clasificacion internacional de enfermedades vigente -->
                                <td><?php echo $porcSemanas ?></td> <!-- Porcentaje de semanas -->
                                <td><?php echo $nitproo ?></td> <!-- NIT proveedor medicamento -->
                                <td><?php echo $nomproo ?></td> <!-- Nombre proveedor medicamento -->
                                <td> </td> <!-- Codigo medicamento, servicios medicos o prestaciones de salud suministrado -->
                                <td>Procedimientos No POS</td> <!-- Nombre medicamento, servicios medicos o prestaciones de salud suministrado -->
                                <td><?php echo $sitiser ?></td> <!-- Sigla tipo servicio de salud prestado -->
                                <td>1</td> <!-- Cantidad suministrada item -->
                                <td><?php echo $valuser ?></td> <!-- Valor unitario del servicio suministrado -->
                                <td><?php echo $valtose ?></td> <!-- Valor total servicio suministrado -->
                                <td><?php echo $valcumo ?></td> <!-- Valor cuota moderadora o copago -->
                                <td><?php echo $valfire ?></td> <!-- Valor final recobrado -->
                                <td><?php echo $numite2 ?></td> <!-- Numero del item 2 -->
                                <td><?php echo $nommese ?></td> <!-- Nombre del medicamento, servicios medicos o prestaciones de salud suministrado 2 -->
                                <td>&nbsp;</td> <!-- Presentacion del medicamento, servicios medicos o prestaciones de salud suministrado -->
                                <td>&nbsp;</td> <!-- Unidades diarias del medicamento, servicio medico o prestación de salud -->
                                <td>&nbsp;</td> <!-- Cantidad días que dura el servicio -->
                                <td>&nbsp;</td> <!-- Cantidad total suministrada del medicamento, servicio medico o prestación de salud -->
                                <td>&nbsp;</td> <!-- Valor unitario del servicio suministrado por item -->
                                <td>&nbsp;</td> <!-- Valor total del servicio suministrado por item -->
                                <td>&nbsp;</td> <!-- Código del medicamento, servicios médicos o prestaciones de salud similar o que sustituye -->
                                <td>&nbsp;</td> <!-- Nombre del medicamento, servicios médicos o prestaciones de salud suministrado similar o que sustituye -->
                                <td>&nbsp;</td> <!-- Unidades diarias del medicamento, servicio médico o prestación de salud que sustituye o similar -->
                                <td>&nbsp;</td> <!-- Cantidad días que dura el servicio del numeral 46 -->
                                <td>&nbsp;</td> <!-- Cantidad total del medicamento, servicio médico o prestación de salud que sustituye o similar -->
                                <td>&nbsp;</td> <!-- Valor unitario del servicio que sustituye o similar -->
                                <td>&nbsp;</td> <!-- Valor total del servicio que sustituye o similar -->
                                <td>&nbsp;</td> <!-- Cantidad de Actas de CTC -->
                                <td>&nbsp;</td> <!-- Cantidad de folios correspondientes al número de actas de CTC -->
                                <td>&nbsp;</td> <!-- Cantidad de facturas -->
                                <td>&nbsp;</td> <!-- Cantidad de folios correspondientes a las facturas -->
                                <td>&nbsp;</td> <!-- Cantidad de formulas médicas -->
                                <td>&nbsp;</td> <!-- Cantidad de folios correspondientes al número de formulas médicas -->
                                <td>&nbsp;</td> <!-- Cantidad de documentos anexos al recobro -->
                                <td>&nbsp;</td> <!-- Cantidad de folios que evidencien la entrega del medicamento -->
                                <td>&nbsp;</td> <!-- Cantidad total de documentos -->
                                <td>&nbsp;</td> <!-- Cantidad total de folios -->
                                <td>&nbsp;</td> <!-- Número de radicación del formato MTY-01 -->
                                <td>&nbsp;</td> <!-- NIT Proveedor Prest -->
                                <td>&nbsp;</td> <!-- Nombre Proveedor Prest -->
                                <td>&nbsp;</td> <!-- Num Factura Proveedor Prest -->
                                <td>&nbsp;</td> <!-- Cod Med Proveedor Prest -->
                                <td>&nbsp;</td> <!-- Nombre Proveedor Prest -->
                                <td>&nbsp;</td> <!-- Vlr Unit Proveedor Prest -->
                                <td>&nbsp;</td> <!-- Vlr Total Proveedor Prest -->
                                <td>&nbsp;</td> <!-- Factura tiene constancia de pago -->
                            </tr>
                            <?php
                        }
                    }
					?>
                    </tbody>
                </table>
            </div>
            <?php
        }
        elseif($valorRadio == 2) //TUTELA
        {

        }
    }
}
?>
</body>
</html>