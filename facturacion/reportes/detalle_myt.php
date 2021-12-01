<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre e�es y tildes -->
    <title>MATRIX - [FACTURAS PARA MYT]</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <style>
        .alternar:hover{ background-color:#CADCFF;}
    </style>
    <?php
    include_once("conex.php");
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

        include_once("root/comun.php");
		$wemp_pmla=$_REQUEST['wemp_pmla'];
        mysql_select_db("matrix");

        $conex = obtenerConexionBD("matrix");
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexi�n con la BD de Facturaci�n");
        $wactualiz = "1.4 2-junio-2019";
    }
    session_start();
    ?>
</head>

<body>
<?php
encabezado("<font style='font-size: x-large; font-weight: bold'>"."DETALLE FACTURAS PARA MYT"."</font>",$wactualiz,"clinica");
?>
<div style="padding-top:10px" class="panel-body">
    <form class="form-horizontal" role="form" name="mty01" action="detalle_myt.php" method="post">
        <table align="center">
            <tr align="center">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><label>CTC</label></td>

                <td><label>Tutela</label></td>
                <td>&nbsp;</td>
            </tr>
            <tr align="center">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><input type="radio" id="radio" name="radio" value="1" checked /></td>

                <td><input type="radio" id="radio" name="radio" value="2" /></td>
                <td>&nbsp;</td>
            </tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon"><label>FUENTE</label></span>
                        <input id="fte" name="fte" type="text" class="form-control" style="width: 100px" value="">
                    </div>
                </td>
                <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon"><label>FACTURA NUMERO</label></span>
                        <input id="fac" name="fac" type="text" class="form-control" style="width: 150px" value="">
                    </div>
                </td>
                <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon"><label>CONSECUTIVO</label></span>
                        <input id="consec" name="consec" type="text" class="form-control" style="width: 100px" value="">
                    </div>
                </td>
                <td>
                    <div class="col-sm-12 controls">
                        <input type="submit" class="btn btn-info btn-sm" id="bntIr" name="btnIr" title="Generar" value="> > >">
                    </div>
                </td>
                <td><div class="input-group-addon" style="background-color: #ffffff; width: 20px; border: none"></div></td>
            </tr>
        </table>
    </form>
</div>
<?php
if(!isset($fte) or $fte=='' or !isset($fac) or $fac == '')
{
    ?>
    <br>
    <br>
    <div align="center">
        <label>Ingrese Fuente y Factura para generar la consulta</label>
    </div>
    <?php
}
else
{
    $valorRadio = $_POST['radio'];
    $consecutivo = $_POST['consec'];

    $query_o1="SELECT '' numcosec,'' numconrec,'' numtiprad,'' numradant,'EPS010' codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,
                        ateideno2,pacarsafi,'' nivcta,'' numacta,'' fechaact,'' fecsolmed,'' indperirec,'' mesper,'' anoper,'' numentre,movfue,
                        movdoc,cardetfec,mdiadia,'' porsema,'800067065' nitprov,conarc,'PROMOTORA MEDICA LAS AMERICAS S.A.' nomprov,
                        cardetcon,cardetcod,cardetcan,cardetvun,cardettot * conmul val1,0 vlrctamod,carfacval * conmul val2,cardetfue,cardetdoc,cardetite,cardetreg"
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
        ."	INTO temp tmpmty";
    $err_o = odbc_do($conex_o,$query_o1);

    //ECHO "QUERY 1 : ",$query_o1;

    $query_o2="SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                    numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                    artcum codi,artnom nomcodi,'MD' sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
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
                '' codi,artnom nomcodi,'MD' sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
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
                '' codi,artnom nomcodi,'MD' sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
       	        cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,ivart"
        ."	WHERE cardetcod <> '0'"
        ."	AND cardetcod=artcod"
        ."	AND cardetcon='0171'"
        ."	AND artcum is null"
        ."	UNION ALL "
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                artcum codi,artnom nomcodi,'MD' sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
       	        cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,ivart"
        ."	WHERE cardetcod <> '0'"
        ."	AND cardetcod=artcod"
        ."	AND cardetcon='0171'"
        ."	AND artcum is not null"

        ."	UNION ALL "
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                cardetcod codi,pronom nomcodi,'PD' sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,inpro"
        ."	WHERE cardetcon='0172'"
        ."    and cardetcod <> '0'"
        ."	AND cardetcod=procod"
        ."	UNION ALL "
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                cardetcod codi,connom nomcodi,'PD' sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,facon"
        ."	WHERE cardetcon='0172'"
        ."    and cardetcod = '0'"
        ."	AND cardetcon=concod"
        ."	UNION ALL "
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                artcum codi,artnom nomcodi,'IN' sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
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
                cardetcod codi,pronom nomcodi,'PD' sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,inpro"
        ."	WHERE conarc='INPROTAR'"
        ."	AND cardetcod=procod"
        ."	UNION ALL "
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                cardetcod codi,exanom nomcodi,'PD' sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."	FROM tmpmty,inexa"
        ."	WHERE conarc='INEXATAR'"
        ."	AND cardetcod=exacod"
        //******NUEVO BLOQUE********
        ."  UNION ALL "
        ."  SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                cardetcod codi,' ' nomcodi,'PD' sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."  FROM tmpmty"
        ."  WHERE conarc=''"
        ."	AND cardetcon='0037'"
        ."  UNION ALL "
        ."  SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                cardetcod codi,' ' nomcodi,'PD' sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg"
        ."  FROM tmpmty"
        ."  WHERE conarc is null"
        ."	AND cardetcon='0037'"
        //."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41"
        //***********************
        ."	INTO temp tmpmty19";

    $err_o = odbc_do($conex_o,$query_o2);

    //ECHO "QUERY 2: ",$query_o2;

    $query_o33="SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                    numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                    codi,nomcodi,sigla,cardetcon,cardetcod,(cardetcan * -1) cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,cardetdoc,cardetite,cardetreg reg"
        ."	FROM tmpmty19"
        ."  WHERE val1<0"
        ."  UNION ALL "
        ."	SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movfue,movdoc,cardetfec,mdiadia,porsema,nitprov,nomprov,
                codi,nomcodi,sigla,cardetcon,cardetcod,cardetcan,cardetvun,val1,vlrctamod,val2,cardetfue,
                cardetdoc,cardetite,cardetreg reg"
        ."	FROM tmpmty19"
        ."  WHERE val1>=0"
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

    $query_o51="SELECT numcosec,numconrec,numtiprad,numradant,codent,ateidetii,ateideide,ateideap1,ateideap2,ateideno1,ateideno2,pacarsafi,nivcta,
                tmpmty4.rowid sec1,numacta,fechaact,fecsolmed,indperirec,mesper,anoper,numentre,movdoc,detfec,rfe,mdiadia,porsema,nitprov,
                nomprov,codi,nomcodi,sigla,can,cardetvun,tot,vlrctamod,facval,tmpmty4.rowid sec2"
        ." FROM tmpmty4";

    $err_o3 = odbc_do($conex_o,$query_o51);

    //ECHO "QUERY 51: ",$query_o51;

	$Num_Filas = 0;

    if($valorRadio == 1)
    {
        ?>
        <div align="center">
            <label>CTC</label>
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
                    <th class="hidden-xs">N�. ENVIO</th>
                    <!--<th>CONSECUTIVO</th>-->
                    <th>N�. COBRO</th>
                    <th>TIPO ID</th>
                    <th>N�. ID</th>
                    <th>1ER APELLIDO</th>
                    <th>2DO APELLIDO</th>
                    <th>1ER NOMBRE</th>
                    <th>2DO NOMBRE</th>
                    <!--
                    <th>FECHA NACIMIENTO</th>
                    <th>EDAD</th>
                    -->
                    <th>NIVEL SISBEN</th>
                    <th>TIPO COBRO</th>
                    <th>N�. FACTURA</th>
                    <th>FECHA EMISION DE LA FACTURA</th>
                    <th>NIT. PROVEEDOR</th>
                    <th>NOMBRE PROVEEDOR</th>
                    <th>FECHA PRESTACION DEL SERVICIO</th>
                    <th>SERVICIO FACTURADO</th>
                    <th>TIPO DE SERVICIO FACTURADO</th>
                    <th>CODIGO DIAGNOSTICO</th>
                    <th>DESCRIPCION DX</th>
                    <th>CODIGO MEDICAMENTO (CUM) /PROCEDIMIENTO (CUPS)</th>
                    <th>NOMBRE MEDICAMENTO /PROCEDIMIENTO</th>
                    <th>CANTIDAD</th>
                    <th>VALOR UNITARIO</th>
                    <th>VALOR FACTURA</th>
                    <th>VALOR MEDICAMENTO O SERVICIO NO POS S</th>
                    <th>CODIGO MEDICAMENTO HOMOLOGO EN EL POS</th>
                    <th>NOMBRE MEDICAMENTO HOMOLOGO EN EL POS</th>
                    <th>VALOR MEDICAMENTO HOMOLOGO EN EL POS</th>
                    <th>VALOR CUOTA DE RECUPERACION</th>
                    <th>VALOR COBRO</th>
                </tr>
                </thead>
                <?php
include_once("conex.php");
include_once("root/comun.php");
$wemp_pmla=$_REQUEST['wemp_pmla'];
				$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
                while (odbc_fetch_row($err_o3))
                {
                    $Num_Filas++;
                    $codent = odbc_result($err_o3, 5);//codigo entidad administradora
                    $tip = odbc_result($err_o3, 6);//tipo documento
                    $cep = odbc_result($err_o3, 7);//cedula paciente
                    $ape1pac = odbc_result($err_o3, 8);//apellido1 paciente
                    $ape2pac = odbc_result($err_o3, 9);//apellido2 paciente
                    if($ape2pac == ''){$ape2pac = 'N';}
                    $nom1pac = odbc_result($err_o3, 10);//nombre 1 paciente
                    if(odbc_result($err_o3, 11) == '' or odbc_result($err_o3, 11) == '               ' or odbc_result($err_o3, 11) == null){$nom2pac = 'N';}
                    else{$nom2pac = odbc_result($err_o3, 11);}//nombre 2 paciente
                    //$nom2pac = odbc_result($err_o3, 11);//nombre 2 paciente
                    //if($nom2pac == ''){$nom2pac = 'N';}
                    $sitiser = odbc_result($err_o3, 31);//Tipo de cobro
                    $numfaci = odbc_result($err_o3, 22);//Numero factura comprende item recobrado
                    $nitproo = odbc_result($err_o3, 27);//NIT proveedor medicamento
                    $nomproo = odbc_result($err_o3, 28);//Nombre proveedor medicamento
                    $fechaPrestacion = odbc_result($err_o3, 23);
                    $fecpres = date("d/m/Y", strtotime($fechaPrestacion));//Fecha prestacion del servicio
                    $nommese = odbc_result($err_o3, 30);//Servicio facturado

                    $queryArticulo = "SELECT artuni,artcod"
                        ." FROM ivart"
                        ." WHERE artnom like '$nommese'";
                    $datoArticulo = odbc_do($conex_o, $queryArticulo);
                    $unidadArticulo = odbc_result($datoArticulo, 1);
                    $codigoArticulo = odbc_result($datoArticulo, 2);
                    switch($unidadArticulo)
                    {
                        case 'FR': $unidadArticulo = 'MEDICAMENTO'; break;
                        case 'TA': $unidadArticulo = 'MEDICAMENTO'; break;
                        case 'UN': $unidadArticulo = 'UNIDAD'; break;
                        case 'PA': $unidadArticulo = 'INSUMO'; break;
                        case 'PQ': $unidadArticulo = 'INSUMO'; break;
                        case 'BO': $unidadArticulo = 'MEDICAMENTO'; break;
                        case 'AM': $unidadArticulo = 'MEDICAMENTO'; break;
                        case 'ML': $unidadArticulo = 'MEDICAMENTO'; break;
                        case 'TB': $unidadArticulo = 'MEDICAMENTO'; break;
                        case 'JR': $unidadArticulo = 'INSUMO'; break;
                    }

                    if($sitiser == 'IN'){$codimed = '';}
                    else{$codimed = odbc_result($err_o3, 29);}
                    if($codimed == null or $codimed == '                  '){$codimed = $codigoArticulo;}//Codigo medicamento, servicios medicos o prestaciones de salud suministrado

                    $codxcie = odbc_result($err_o3, 25);//Codigo diagnostico segun clasificacion internacional de enfermedades vigente
                    $cansumi = odbc_result($err_o3, 32);//Cantidad suministrada item
                    $valuser = odbc_result($err_o3, 33);//Valor unitario del servicio suministrado
                    $valtose = odbc_result($err_o3, 34);//Valor total servicio suministrado
                    $valfire = odbc_result($err_o3, 36);//Valor final recobrado

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
                    //$valcumo = odbc_result($err_o3, 35);//Valor cuota moderadora o copago

                    $query = mysql_queryV("SELECT a.Pacfna FROM ".$wcliame."_000100 a WHERE a.Pacdoc = '$cep'");
                    while($dato = mysql_fetch_array($query))
                    {
                        $fechaN = $dato[0];
                        $fechaNac=date("Y/m/d",strtotime($fechaN));

                        $dia = date("d");   $mes = date("m");   $ano = date("Y");

                        $dianaz = date("d",strtotime($fechaNac));
                        $mesnaz = date("m",strtotime($fechaNac));
                        $anonaz = date("Y",strtotime($fechaNac));

                        if(($mesnaz == $mes) && ($dianaz > $dia))
                        {
                            $ano = ($ano - 1);
                        }
                        if($mesnaz > $mes)
                        {
                            $ano = ($ano - 1);
                        }

                        $edad = ($ano - $anonaz);
                    }

                    $queryDX = "SELECT dianom"
                        ." FROM india"
                        ." WHERE diacod = '$codxcie'";
                    $datoDX = odbc_do($conex_o, $queryDX);
                    $descripcionDX = odbc_result($datoDX, 1);

                    ?>
                    <tbody>
                    <tr id="rowDATOS" class="alternar">
                        <td>&nbsp;</td> <!-- N�mero de Env�o-->
                        <!--<td><?php echo $consecutivo ?></td>--> <!-- Consecutivo-->
                        <td><?php echo $numfaci ?> </td> <!-- N�mero de Cobro, es el mismo numero de Factura-->
                        <td><?php echo $tip ?></td> <!-- tipo documento paciente-->
                        <td><?php echo $cep ?></td> <!-- numero documento paciente-->
                        <td><?php echo $ape1pac ?></td> <!-- apellido 1 paciente-->
                        <td><?php echo $ape2pac ?></td> <!-- apellido 2 paciente-->
                        <td><?php echo $nom1pac ?></td> <!-- nombre 1 paciente-->
                        <td><?php echo $nom2pac ?></td> <!-- nombre 2 paciente-->
                        <!--<td><?php echo $fechaNac ?></td>--> <!-- Fecha Nacimiento -->
                        <!--<td><?php echo $edad ?></td>--> <!-- Edad -->
                        <td>NA</td> <!-- Nivel SISBEN, 1 por defecto -->
                        <!--<td><?php// echo $sitiser ?></td>--> <!-- Tipo cobro -->
                        <td>CTC</td> <!-- Tipo cobro -->
                        <td><?php echo $numfaci ?></td> <!-- N�mero de la factura que comprende el item recobrado -->
                        <td></td> <!-- FECHA DE LA FACTURA -->
                        <td><?php echo $nitproo ?></td> <!-- NIT proveedor medicamento -->
                        <td><?php echo $nomproo ?></td> <!-- Nombre proveedor medicamento -->
                        <td><?php echo $fecpres ?></td> <!-- Fecha prestacion del servicio -->
                        <td><?php echo $nommese ?></td> <!-- Servicio facturado -->
                        <td><?php echo $unidadArticulo ?></td> <!-- Tipo de servicio facturado -->
                        <td><?php echo $codxcie ?></td> <!-- Codigo diagnostico segun clasificacion internacional de enfermedades vigente -->
                        <td><?php echo $descripcionDX ?></td> <!-- Descripcion DX -->
                        <td><?php echo $codimed ?></td> <!-- Codigo medicamento, servicios medicos o prestaciones de salud suministrado -->
                        <td><?php echo $nommese ?></td> <!-- Nombre del medicamento, servicios medicos o prestaciones de salud suministrado 2 -->
                        <td><?php echo intval($cansumi) ?></td> <!-- Cantidad suministrada item -->
                        <td><?php echo $valuser ?></td> <!-- Valor unitario del servicio suministrado -->
                        <td><?php echo $valtose ?></td> <!-- Valor total servicio suministrado -->
                        <td><?php echo $valfire ?></td> <!-- Valor final recobrado -->
                        <td>&nbsp;</td> <!-- Codigo medicamento homologo en el pos -->
                        <td>&nbsp;</td> <!-- Nombre medicamento homologo en el pos -->
                        <td>&nbsp;</td> <!-- Valor medicamento homologo en el pos -->
                        <td><?php echo $valcumo ?></td> <!-- Valor cuota moderadora o copago -->
                        <td><?php echo $valfire ?></td> <!-- Valor final recobrado -->
                    </tr>
                    </tbody>
                    <?php
                }
                ?>
            </table>
        </div>
        <?php
    }
    elseif($valorRadio == 2)
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
                    <th class="hidden-xs">N�mero consecutivo interno para radicaciones de la entidad reclamante</th>
                    <th>N�mero consecutivo del recobro</th>
                    <th>Tipo de radicaci�n</th>
                    <th>N�mero de radicaci�n anterior</th>
                    <th>C�digo de la entidad administradora de planes de beneficio</th>
                    <th>Tipo de documento de identidad</th>
                    <th>N�mero de documento de identidad</th>
                    <th>Primer apellido del afiliado</th>
                    <th>Segundo apellido del afiliado</th>
                    <th>Primer nombre del afiliado</th>
                    <th>Segundo nombre del afiliado</th>
                    <th>Tipo de afiliado</th>
                    <th>Nivel de la cuota moderadora</th>
                    <th>Nivel de la cuota de recuperaci�n</th>
                    <th>N�mero del item</th>
                    <th>N�mero del fallo</th>
                    <th>Fecha del fallo</th>
                    <th>N�mero de autoridad judicial</th>
                    <th>Tipo de autoridad judicial</th>
                    <th>Municipio de ubicacion autoridad judicial</th>
                    <th>Codigo causa del recobro</th>
                    <th>Fecha solicitud del m�dico</th>
                    <th>Indicador de periodicidad del recobro</th>
                    <th>Mes del periodo suministrado</th>
                    <th>A�o del periodo suministrado</th>
                    <th>N�mero de entrega de lo ordenado por el fallo de tutela</th>
                    <th>N�mero de la factura que comprende el item recobrado</th>
                    <th>Fecha de la prestaci�n del servicio</th>
                    <th>Fecha de radicaci�n de la factura ante la entidad administradora de los planes de beneficios</th>
                    <th>C�digo de diagn�stico seg�n la clasificaci�n internacional de enfermedades vigente</th>
                    <th>Porcentaje de semanas</th>
                    <th>NIT del proveedor del medicamento</th>
                    <th>Nombre del proveedor del medicamento</th>
                    <th>C�digo del medicamento, servicios m�dicos o prestaciones de salud suministrado</th>
                    <th>Nombre del medicamento, servicios m�dicos o prestaciones de salud suministrado</th>
                    <th>Sigla del tipo de servicio de salud prestado</th>
                    <th>Cantidad suministrada item</th>
                    <th>Valor unitario del servicio suministrado</th>
                    <th>Valor total del servicio suministrado</th>
                    <th>Valor cuota moderadora o copago</th>
                    <th>Valor final recobrado</th>
                    <th>N�mero del item</th>
                    <th>Nombre del medicamento, servicios medicos o prestaciones de salud suministrado</th>
                    <th>Presentacion del medicamento, servicios medicos o prestaciones de salud suministrado</th>
                    <th>Unidades diarias del medicamento, servicio medico o prestaci�n de salud</th>
                    <th>Cantidad d�as que dura el servicio</th>
                    <th>Cantidad total suministrada del medicamento, servicio medico o prestaci�n de salud</th>
                    <th>Valor unitario del servicio suministrado por item</th>
                    <th>Valor total del servicio suministrado por item</th>
                    <th>C�digo del medicamento, servicios m�dicos o prestaciones de salud similar o que sustituye</th>
                    <th>Nombre del medicamento, servicios m�dicos o prestaciones de salud suministrado similar o que sustituye</th>
                    <th>Unidades diarias del medicamento, servicio m�dico o prestaci�n de salud que sustituye o similar</th>
                    <th>Cantidad d�as que dura el servicio del numeral 46</th>
                    <th>Cantidad total del medicamento, servicio m�dico o prestaci�n de salud que sustituye o similar</th>
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
                    <th>No de radicaci�n recobro anterior (formato MYT-02)</th>
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
                    if(odbc_result($err_o, 11) == '' or odbc_result($err_o, 11) == '               ' or odbc_result($err_o, 11) == null){$nom2pac = 'N';}
                    else{$nom2pac = odbc_result($err_o, 11);}//nombre 2 paciente
                    //$nom2pac = odbc_result($err_o, 11);//nombre 2 paciente
                    $tipoafi = odbc_result($err_o, 12);//Tipo de afiliado
                    $nicuota = odbc_result($err_o, 13);//Nivel de la cuota moderadora
                    $numitem = odbc_result($err_o, 14);//Numero del item
                    $numacta = odbc_result($err_o, 15);//Numero acta comite tecnico cientifico
                    $fecacta = odbc_result($err_o, 16);//Fecha acta del comite tecnico cientifico
                    $fecsolm = odbc_result($err_o, 17);//Fecha solicitud medico
                    $inperre = odbc_result($err_o, 18);//Indicador de periodicidad del recobro
                    $mespesu = odbc_result($err_o, 19);//Mes periodo suministrado
                    $anpersu = odbc_result($err_o, 20);//A�o periodo suministrado
                    $numenac = odbc_result($err_o, 21);//Numero entrega acta ctc para el periodo
                    $numfaci = odbc_result($err_o, 22);//Numero factura comprende item recobrado
                    $fecpres = odbc_result($err_o, 23);//Fecha prestacion del servicio
                    $fecrafa = odbc_result($err_o, 24);//Fecha radicacion factura ante entidad administradora planes de beneficios
                    $codxcie = odbc_result($err_o, 25);//Codigo diagnostico segun clasificacion internacional de enfermedades vigente
                    $porsema = odbc_result($err_o, 26);//Porcentaje de semanas
                    $nitproo = odbc_result($err_o, 27);//NIT proveedor medicamento
                    $nomproo = odbc_result($err_o, 28);//Nombre proveedor medicamento
                    $sitiser = odbc_result($err_o, 31);//Sigla tipo servicio de salud prestado
                    if($sitiser == 'IN'){$codimed = '';}
                    else{$codimed = odbc_result($err_o, 29);}//Codigo medicamento, servicios medicos o prestaciones de salud suministrado
                    $nommese = odbc_result($err_o, 30);//Nombre medicamento, servicios medicos o prestaciones de salud suministrado
                    $cansumi = odbc_result($err_o, 32);//Cantidad suministrada item
                    $valuser = odbc_result($err_o, 33);//Valor unitario del servicio suministrado
                    $valtose = odbc_result($err_o, 34);//Valor total servicio suministrado
                    $valcumo = odbc_result($err_o, 35);//Valor cuota moderadora o copago
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
                        <td><?php echo $numitem ?></td> <!-- N�mero del item -->
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><?php echo $fecsolm ?></td> <!-- Fecha solicitud medico -->
                        <td><?php echo $inperre ?></td> <!-- Indicador de periodicidad del recobro -->
                        <td><?php echo $mespesu ?></td> <!-- Mes periodo suministrado -->
                        <td><?php echo $anpersu ?></td> <!-- A�o periodo suministrado -->
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
?>
</body>
</html>