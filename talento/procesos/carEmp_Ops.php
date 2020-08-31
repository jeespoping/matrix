<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GESTION DE TALENTO HUMANO - OPERACIONES</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link href="carEmp_style.css" rel="stylesheet">
    <script src="carEmp_Js.js"></script>
    <?php
    include("conex.php");
    include("root/comun.php");

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
        mysql_select_db("matrix");
        $conex = obtenerConexionBD("matrix");
    }
    include('carEmp_Functions.php'); //publicacion local

    $funcion = $_GET['funcion'];            if($funcion == null){$funcion = $_POST['funcion'];}
    $tbInfoEmpleado = $_GET['tablaMtx'];    $usuarioMtx = $_GET['usuarioMtx'];  if($usuarioMtx == null){$usuarioMtx = $_POST['userMtx'];}
    $userMtx = $_POST['userMtx'];           if($userMtx == null){$userMtx = $_POST['userMatrix']; }
    $fechaActual = date('Y-m-d');           $horaActual = date('H:m:s');
    $idEstud = $_GET['idEstud'];
    if($idEstud == null){$idEstud = $_POST['idEstud'];}
    $idEstudAct = $_POST['idEstudAct'];

    //DATOS ESTUDIOS, INTEGRANTES FAMILIARES, CREDITOS:
    $grado = $_POST['grado'];           $titulo = $_POST['titulo'];         $instit = $_POST['instit'];         $fecTit = $_POST['fecTit'];
    $idiomaNom = $_POST['idiomaNom'];   $idiomaHab = $_POST['idiomaHab'];   $idiomaLee = $_POST['idiomaLee'];   $idiomaEsc = $_POST['idiomaEsc'];
    $queEstud = $_POST['queEstud'];     $durEstud = $_POST['durEstud'];     $intEstud = $_POST['intEstud'];     $nivEstud = $_POST['nivEstud'];
    $horEstud = $_POST['horEstud'];
    $intfNom = $_POST['intfNom'];       $intfApe = $_POST['intfApe'];       $intfGen = $_POST['intfGen'];       $intfPare = $_POST['intfPare'];
    $intfFen = $_POST['intfFen'];       $intfNie = $_POST['intfNie'];       $intfOcu = $_POST['intfOcu'];       $intfVius = $_POST['intfVius'];
    $intfNomcol = $_POST['intfNomcol'];
    $cremot = $_POST['cremot'];         $creent = $_POST['creent'];         $creval = $_POST['creval'];         $crecuo = $_POST['crecuo'];
    $idGruFam = $_POST['idGruFam'];

    //GUARDADO GENERAL:
    //$Idefnc2 = $_POST['Idefnc2'];      $Idegen2 = $_POST['Idegen2'];              $Ideced2 = $_POST['Ideced2'];          $Idepas2 = $_POST['Idepas2'];
    //$Idevis2 = $_POST['Idevis2'];      $estcivUser2 = $_POST['estcivUser2'];      $Idedir2 = $_POST['Idedir2'];          $lunaUser2 = $_POST['lunaUser2'];
    //$estUser2 = $_POST['estUser2'];    $munUser2 = $_POST['munUser2'];            $barUser2 = $_POST['barUser2'];        $telUser2 = $_POST['telUser2'];
    //$celUser2 = $_POST['celUser2'];    $corUser2 = $_POST['corUser2'];            $tisanUser2 = $_POST['tisanUser2'];    $extUser2 = $_POST['extUser2'];
    //$variable2 = $_POST['variable2'];  $Cviviv2 = $_POST['Cviviv2'];              $Cvitvi2 = $_POST['Cvitvi2'];          $Cvitrz2 = $_POST['Cvitrz2'];
    //$Cvilot2 = $_POST['Cvilot2'];      $Cvisvi2 = $_POST['Cvisvi2'];              $chAcue2 = $_POST['chAcue2'];          $chAlca2 = $_POST['chAlca2'];
    //$chAseo2 = $_POST['chAseo2'];      $chEner2 = $_POST['chEner2'];              $chInter2 = $_POST['chInter2'];        $chGas2 = $_POST['chGas2'];
    //$chTele2 = $_POST['chTele2'];      $Cvicre2 = $_POST['credito2'];             $chBici2 = $_POST['chBici2'];          $chBus2 = $_POST['chBus2'];
    //$chCamina2 = $_POST['chCamina2'];  $chPart2 = $_POST['chPart2'];              $chMetro2 = $_POST['chMetro2'];        $chMoto2 = $_POST['chMoto2'];
    //$chOtroT2 = $_POST['chOtroT2'];    $chTaxi2 = $_POST['chTaxi2'];              $chContra2 = $_POST['chContra2'];      $Cviotr2 = $_POST['otroTrans2'];
    //$chTrae2 = $_POST['chTrae2'];      $chComBoca2 = $_POST['chComBoca2'];        $chComOtros2 = $_POST['chComOtros2'];  $chCasa2 = $_POST['chCasa2'];
    //$chOtrosAl2 = $_POST['chOtrosAl2'];$Cvioal2 = $_POST['Cvioal2'];              $chClaInfo2 = $_POST['chClaInfo2'];    $chClaIng2 = $_POST['chClaIng2'];
    //$chGastro2 = $_POST['chGastro2'];  $chConFami2 = $_POST['chConFami2'];        $chConCrePer2 = $_POST['chConCrePer2'];$Cvihbb2 = $_POST['Cvihbb2'];
    //$Otrpar2 = $_POST['Otrpar2'];      $Otrlocker2 = $_POST['Otrlocker2'];        $Otractrec2 = $_POST['Otractrec2'];    $Otractrechor2 = $_POST['Otractrechor2'];
    //$Otractcul2 = $_POST['Otractcul2'];$Otractculhor2 = $_POST['Otractculhor2'];  $chAudit2 = $_POST['chAudit2'];        $chComite2 = $_POST['chComite2'];
    //$chBrigada2 = $_POST['chBrigada2']; $chOtroRol2 = $_POST['chOtroRol2'];        $Otrroles2 = $_POST['otrRol2'];

    //CONSULTAR TABLA DEL USUARIO:
    //$tablaUser = tablaUser($wuse,$conex);
    //if($tablaUser == 'thsoe'){$tablaUser = 'talhuma';}
    //if($tablaUser == 'thidc'){$tablaUser = 'talhuma';}
    //if($tablaUser == 'latalhum'){$tablaUser = 'talhuma';}
    //if($tablaUser == 'cstalhum'){$tablaUser = 'talhuma';}
    //if($tablaUser == 'thhonmed'){$tablaUser = 'talhuma';}
    $tablaUser = 'talhuma';

    //GUARDADO GENERAL:
    $Idefnc = $_GET['Idefnc']; if($Idefnc == null){$Idefnc = $_POST['Idefnc2'];}                    $Idegen = $_GET['Idegen']; if($Idegen == null){$Idegen = $_POST['Idegen2'];}
    $Ideced = $_GET['Ideced']; if($Ideced == null){$Ideced = $_POST['Ideced2'];}                    $Idepas = $_GET['Idepas']; if($Idepas == null){$Idepas = $_POST['Idepas2'];}
    $Idevis = $_GET['Idevis']; if($Idevis == null){$Idevis = $_POST['Idevis2'];}                    $estcivUser = $_GET['estcivUser']; if($estcivUser == null){$estcivUser = $_POST['estcivUser2'];}
    $Idedir = $_GET['Idedir']; if($Idedir == null){$Idedir = $_POST['Idedir2'];}                    $lunaUser = $_GET['lunaUser']; if($lunaUser == null){$lunaUser = $_POST['lunaUser2'];}
    $estUser = $_GET['estUser'];if($estUser == null){$estUser = $_POST['estUser2'];}                $munUser = $_GET['munUser']; if($munUser == null){$munUser = $_POST['munUser2'];}
    $barUser = $_GET['barUser']; if($barUser == null){$barUser = $_POST['barUser2'];}               $telUser = $_GET['telUser']; if($telUser == null){$telUser = $_POST['telUser2'];}
    $celUser = $_GET['celUser']; if($celUser == null){$celUser = $_POST['celUser2'];}               $corUser = $_GET['corUser']; if($corUser == null){$corUser = $_POST['corUser2'];}
    $tisanUser = $_GET['tisanUser']; if($tisanUser == null){$tisanUser = $_POST['tisanUser2'];}     $extUser = $_GET['extUser']; if($extUser == null){$extUser = $_POST['extUser2'];}
    $variable = $_GET['variable']; if($variable == null){$variable = $_POST['variable2'];}          $Cviviv = $_GET['Cviviv']; if($Cviviv == null){$Cviviv = $_POST['Cviviv2'];}
    $Cvitvi = $_GET['Cvitvi']; if($Cvitvi == null){$Cvitvi = $_POST['Cvitvi2'];}                    $Cvitrz = $_GET['Cvitrz']; if($Cvitrz == null){$Cvitrz = $_POST['Cvitrz2'];}
    $Cvilot = $_GET['Cvilot']; if($Cvilot == null){$Cvilot = $_POST['Cvilot2'];}                    $Cvisvi = $_GET['Cvisvi']; if($Cvisvi == null){$Cvisvi = $_POST['Cvisvi2'];}
    $chAcue = $_GET['chAcue']; if($chAcue == null){$chAcue = $_POST['chAcue2'];}                    $chAlca = $_GET['chAlca']; if($chAlca == null){$chAlca = $_POST['chAlca2'];}
    $chAseo = $_GET['chAseo']; if($chAseo == null){$chAseo = $_POST['chAseo2'];}                    $chEner = $_GET['chEner']; if($chEner == null){$chEner = $_POST['chEner2'];}
    $chInter = $_GET['chInter']; if($chInter == null){$chInter = $_POST['chInter2'];}               $chGas = $_GET['chGas']; if($chGas == null){$chGas = $_POST['chGas2'];}
    $chTele = $_GET['chTele']; if($chTele == null){$chTele = $_POST['chTele2'];}                    $Cvicre = $_GET['credito']; if($Cvicre == null){$Cvicre = $_POST['credito2'];}
    $chBici = $_GET['chBici']; if($chBici == null){$chBici = $_POST['chBici2'];}                    $chBus = $_GET['chBus']; if($chBus == null){$chBus = $_POST['chBus2'];}
    $chCamina = $_GET['chCamina']; if($chCamina == null){$chCamina = $_POST['chCamina2'];}          $chPart = $_GET['chPart']; if($chPart == null){$chPart = $_POST['chPart2'];}
    $chMetro = $_GET['chMetro']; if($chMetro == null){$chMetro = $_POST['chMetro2'];}               $chMoto = $_GET['chMoto']; if($chMoto == null){$chMoto = $_POST['chMoto2'];}
    $chOtroT = $_GET['chOtroT']; if($chOtroT == null){$chOtroT = $_POST['chOtroT2'];}               $chTaxi = $_GET['chTaxi']; if($chTaxi == null){$chTaxi = $_POST['chTaxi2'];}
    $chContra = $_GET['chContra']; if($chContra == null){$chContra = $_POST['chContra2'];}          $Cviotr = $_GET['otroTrans']; if($Cviotr == null){$Cviotr = $_POST['otroTrans2'];}
    $chTrae = $_GET['chTrae']; if($chTrae == null){$chTrae = $_POST['chTrae2'];}                    $chComBoca = $_GET['chComBoca']; if($chComBoca == null){$chComBoca = $_POST['chComBoca2'];}
    $chComOtros = $_GET['chComOtros'];if($chComOtros == null){$chComOtros = $_POST['chComOtros2'];} $chCasa = $_GET['chCasa']; if($chCasa == null){$chCasa = $_POST['chCasa2'];}
    $chOtrosAl = $_GET['chOtrosAl']; if($chOtrosAl == null){$chOtrosAl = $_POST['Cvioal2'];}        $Cvioal = $_GET['Cvioal']; if($Cvioal == null){$Cvioal = $_POST['Cvioal2'];}
    $chClaInfo = $_GET['chClaInfo']; if($chClaInfo == null){$chClaInfo = $_POST['chClaInfo2'];}     $chClaIng = $_GET['chClaIng']; if($chClaIng == null){$chClaIng = $_POST['chClaIng2'];}
    $chGastro = $_GET['chGastro']; if($chGastro == null){$chGastro = $_POST['chGastro2'];}          $chConFami = $_GET['chConFami']; if($chConFami == null){$chConFami = $_POST['chConFami2'];}
    $chConCrePer=$_GET['chConCrePer'];if($chConCrePer==null){$chConCrePer=$_POST['chConCrePer2'];}  $Cvihbb = $_GET['Cvihbb']; if($Cvihbb == null){$Cvihbb = $_POST['Cvihbb2'];}
    $Otrpar = $_GET['Otrpar']; if($Otrpar == null){$Otrpar = $_POST['Otrpar2'];}                    $Otrlocker = $_GET['Otrlocker']; if($Otrlocker == null){$Otrlocker = $_POST['Otrlocker2'];}
    $Otractrec = $_GET['Otractrec']; if($Otractrec == null){$Otractrec = $_POST['Otractrec2'];}     $Otractrechor = $_GET['Otractrechor']; if($Otractrechor == null){$Otractrechor = $_POST['Otractrechor2'];}
    $Otractcul = $_GET['Otractcul']; if($Otractcul == null){$Otractcul = $_POST['Otractcul2'];}     $Otractculhor = $_GET['Otractculhor']; if($Otractculhor == null){$Otractculhor = $_POST['Otractculhor2'];}
    $chAudit = $_GET['chAudit']; if($chAudit == null){$chAudit = $_POST['chAudit2'];}               $chComite = $_GET['chComite']; if($chComite == null){$chComite = $_POST['chComite2'];}
    $chBrigada = $_GET['chBrigada']; if($chBrigada == null){$chBrigada = $_POST['chBrigada2'];}     $chOtroRol = $_GET['chOtroRol']; if($chOtroRol == null){$chOtroRol = $_POST['chOtroRol2'];}
    $Otrroles = $_GET['otrRol']; if($Otrroles == null){$Otrroles = $_POST['otrRol2'];}              $horaEdu = $_GET['horaEdu'];
    //COMITES:
    $chCoCon = $_GET['chCoCon'];    $chGesIn = $_GET['chGesIn'];            $chCoDoc = $_GET['chCoDoc'];        $chGesTe = $_GET['chGesTe'];
    $chCoInv = $_GET['chCoInv'];    $chHisTe = $_GET['chHisTe'];            $chCoCom = $_GET['chCoCom'];        $chInfInt = $_GET['chInfInt'];
    $chCopas = $_GET['chCopas'];    $chMedTra = $_GET['chMedTra'];          $chCrede = $_GET['chCrede'];        $chMeCon = $_GET['chMeCon'];
    $chEtiIn = $_GET['chEtiIn'];    $chMoSe = $_GET['chMoSe'];              $chEtiHo = $_GET['chEtiHo'];        $chSePac = $_GET['chSePac'];
    $chEvCal = $_GET['chEvCal'];    $chTransp = $_GET['chTransp'];          $chFarTe = $_GET['chFarTe'];        $chViEpi = $_GET['chViEpi'];
    $chGesAm = $_GET['chGesAm'];
    //USUARIO NUEVO:
    $codUsuario = $_GET['codUsuario'];  $cedula = $_POST['cedula'];
    //$priNom = $_POST['priNom'];     $segNom = $_POST['segNom'];             $priApe = $_POST['priApe'];         $segApe = $_POST['segApe'];
    //$fecNac = $_POST['fecNac'];     $genero = $_POST['genero'];
    $codUsFirst = $_POST['codUsFirst']; if($codUsFirst == null){$codUsFirst = $_POST['codUsFirst2'];}
    //NUEVOS DATOS IDENTIFICACION GENERAL:
    $contEmer = $_GET['contEmer']; if($contEmer == null){$contEmer = $_POST['contEmer2'];}  $usuRaza = $_GET['Ideraz']; if($usuRaza == null){$usuRaza = $_POST['usuRaza2'];}
    $timeDesp = $_GET['timeDesp'];  $turnEmp = $_GET['turnEmp'];
    $telEmer = $_GET['telEmer'];
    if($telEmer == null){$telEmer = $_POST['telEmer'];}
    //NUEVOS DATOS ACTIVIDADES:
    $chTorBol = $_GET['chTorBol'];  $chTorPla = $_GET['chTorPla'];  $chTorVol = $_GET['chTorVol'];  $chTorBal = $_GET['chTorBal'];  $chTorTen = $_GET['chTorTen'];
    $chCamin = $_GET['chCamin'];    $chBaile = $_GET['chBaile'];    $chYoga = $_GET['chYoga'];      $chEnPare = $_GET['chEnPare'];  $chCiclo = $_GET['chCiclo'];
    $chMara = $_GET['chMara'];      $chTarHob = $_GET['chTarHob'];  $chGruTea = $_GET['chGruTea'];  $chArtPla = $_GET['chArtPla'];  $chManual = $_GET['chManual'];
    $chGastro = $_GET['chGastro'];  $chClaIng = $_GET['chClaIng'];  $chConPle = $_GET['chConPle'];  $chTarPic = $_GET['chTarPic'];  $chOtrAct = $_GET['chOtrAct'];
    $actExtra = $_GET['actExtra'];  $otraExtra = $_GET['otraExtra'];$ranSal = $_GET['ranSal'];
    //NUEVOS DATOS FAMILIA - GASTOS:
    $chViArr = $_GET['chViArr'];    $chViPag = $_GET['chViPag'];    $chAlime = $_GET['chAlime'];    $chSerPu = $_GET['chSerPu'];    $chTrpte = $_GET['chTrpte'];
    $chEdPro = $_GET['chEdPro'];    $chEdHij = $_GET['chEdHij'];    $chPaCre = $_GET['chPaCre'];    $chReTli = $_GET['chReTli'];    $chVestu = $_GET['chVestu'];
    $chSalud = $_GET['chSalud'];    $chPaCel = $_GET['chPaCel'];    $chPaTar = $_GET['chPaTar'];    $chCoTec = $_GET['chCoTec'];    $chCuPer = $_GET['chCuPer'];
    $chOtGas = $_GET['chOtGas'];
    $usuGasto = $chViArr.','.$chViPag.','.$chAlime.','.$chSerPu.','.$chTrpte.','.$chEdPro.','.$chEdHij.','.$chPaCre.','.$chReTli.','.$chVestu.','.
                $chSalud.','.$chPaCel.','.$chPaTar.','.$chCoTec.','.$chCuPer.','.$chOtGas;
    if($usuGasto == ',,,,,,,,,,,,,,,'){$usuGasto = $_POST['usuGasto'];}
    //NUEVOS DATOS FAMILIA - SITUACIONES:
    $chDeuSi = $_GET['chDeuSi'];    $chPCohi = $_GET['chPCohi'];    $chDiEco = $_GET['chDiEco'];    $chDeMif = $_GET['chDeMif'];    $chHiEmb = $_GET['chHiEmb'];
    $chSeDiv = $_GET['chSeDiv'];    $chViInt = $_GET['chViInt'];    $chAdLic = $_GET['chAdLic'];    $chMuSer = $_GET['chMuSer'];    $chEnGra = $_GET['chEnGra'];
    $chNiSit = $_GET['chNiSit'];
    $usuSitua = $chDeuSi.','.$chPCohi.','.$chDiEco.','.$chDeMif.','.$chHiEmb.','.$chSeDiv.','.$chViInt.','.$chAdLic.','.$chMuSer.','.$chEnGra.','.$chNiSit;
    if($usuSitua == ',,,,,,,,,,'){$usuSitua = $_POST['usuSitua'];}
    //POSICION EN EL GRUPO FAMILIAR:
    $posFam = $_GET['posFam'];      if($posFam == null){$posFam = $_POST['posFam'];}
    //QUIEN CUIDA HIJOS:
    $chAbuNi = $_GET['chAbuNi'];    $chPaMad = $_GET['chPaMad'];    $chVecin = $_GET['chVecin'];    $chGuIns = $_GET['chGuIns'];    $chEmDom = $_GET['chEmDom'];
    $chUnFam = $_GET['chUnFam'];    $chQuSol = $_GET['chQuSol'];    $chCuOtr = $_GET['chCuOtr'];
    $usuCuHi = $chAbuNi.','.$chPaMad.','.$chVecin.','.$chGuIns.','.$chEmDom.','.$chUnFam.','.$chQuSol.','.$chCuOtr;
    if($usuCuHi == ',,,,,,,'){$usuCuHi = $_POST['usuCuHi'];}
    //SUBSIDIO VIVIENDA:
    $subViv = $_GET['subViv'];  if($subViv == null){$subViv = $_POST['subViv'];}
    //AHORRO PARA VIVIENDA:
    $ahoViv = $_GET['ahoViv'];  $montoHa = $_GET['montoHa'];        if($ahoViv == null){$ahoViv = $_POST['ahoViv'];}                if($montoHa == null){$montoHa = $_POST['montoHa'];}
    //FACTORES DE RIESGO VIVIENDA:
    $chInuVi = $_GET['chInuVi'];    $chContVi = $_GET['chContVi'];  $chRiAmVi = $_GET['chRiAmVi'];  $chRiEsvi = $_GET['chRiEsvi'];  $chRiSaVi = $_GET['chRiSaVi'];
    $chRiPuVi = $_GET['chRiPuVi'];  $chNoFaVi = $_GET['chNoFaVi'];
    $Usfariesvi = $chInuVi.','.$chContVi.','.$chRiAmVi.','.$chRiEsvi.','.$chRiSaVi.','.$chRiPuVi.','.$chNoFaVi;
    if($Usfariesvi == ',,,,,,'){$Usfariesvi = $_POST['Usfariesvi'];}
    //MEJORAMIENTO DE VIVIENDA:
    $chMeEst = $_GET['chMeEst'];    $chMeMue = $_GET['chMeMue'];    $chMeEle = $_GET['chMeEle'];    $chMePis = $_GET['chMePis'];    $chMePar = $_GET['chMePar'];
    $chMeCol = $_GET['chMeCol'];    $chMeHum = $_GET['chMeHum'];    $chMeFac = $_GET['chMeFac'];    $chMeTec = $_GET['chMeTec'];    $chMeBan = $_GET['chMeBan'];
    $chMeCoc = $_GET['chMeCoc'];    $chMeAmp = $_GET['chMeAmp'];    $chMeNot = $_GET['chMeNot'];
    $UsuMejoVi = $chMeEst.','.$chMeMue.','.$chMeEle.','.$chMePis.','.$chMePar.','.$chMeCol.','.$chMeHum.','.$chMeFac.','.$chMeTec.','.$chMeBan.','.
                 $chMeCoc.','.$chMeAmp.','.$chMeNot;
    if($UsuMejoVi == ',,,,,,,,,,,,'){$UsuMejoVi = $_POST['UsuMejoVi'];}
    //PRODUCTOS FINANCIEROS:
    $chPfCah = $_GET['chPfCah'];    $chPfCuc = $_GET['chPfCuc'];    $chPfTac = $_GET['chPfTac'];    $chPfCco = $_GET['chPfCco'];    $chPfChi = $_GET['chPfChi'];
    $chPfCve = $_GET['chPfCve'];    $chPfInv = $_GET['chPfInv'];    $chPfSeg = $_GET['chPfSeg'];    $chPfNin = $_GET['chPfNin'];
    $UsProFi = $chPfCah.','.$chPfCuc.','.$chPfTac.','.$chPfCco.','.$chPfChi.','.$chPfCve.','.$chPfInv.','.$chPfSeg.','.$chPfNin;
    if($UsProFi == ',,,,,,,,'){$UsProFi = $_POST['UsProFi'];}
    //MOTIVO CREDITOS:
    $chMcViv = $_GET['chMcViv'];    $chMcTec = $_GET['chMcTec'];    $chMcMue = $_GET['chMcMue'];    $chMcEle = $_GET['chMcEle'];    $chMcVeh = $_GET['chMcVeh'];
    $chMcSal = $_GET['chMcSal'];    $chMcCir = $_GET['chMcCir'];    $chMcTur = $_GET['chMcTur'];    $chMcLib = $_GET['chMcLib'];    $chMcGah = $_GET['chMcGah'];
    $chMcTac = $_GET['chMcTac'];    $chMcEdp = $_GET['chMcEdp'];    $chMcEdf = $_GET['chMcEdf'];    $chMcCem = $_GET['chMcCem'];    $chMcNin = $_GET['chMcNin'];
    $UsMoCre = $chMcViv.','.$chMcTec.','.$chMcMue.','.$chMcEle.','.$chMcVeh.','.$chMcSal.','.$chMcCir.','.$chMcTur.','.$chMcLib.','.$chMcGah.','.
               $chMcTac.','.$chMcEdp.','.$chMcEdf.','.$chMcCem.','.$chMcNin;
    if($UsMoCre == ',,,,,,,,,,,,,,'){$UsMoCre = $_POST['UsMoCre'];}
    //A QUIEN ACUDE ACCESO CREDITOS:
    $chEpBan = $_GET['chEpBan'];    $chEpFon = $_GET['chEpFon'];    $chEpFmu = $_GET['chEpFmu'];    $chEpPad = $_GET['chEpPad'];    $chEpFam = $_GET['chEpFam'];
    $chEpCal = $_GET['chEpCal'];    $chEpCaj = $_GET['chEpCaj'];    $chEpEla = $_GET['chEpEla'];    $chEpNat = $_GET['chEpNat'];    $chEpOtr = $_GET['chEpOtr'];
    $chEpNin = $_GET['chEpNin'];
    $UsEpAcu = $chEpBan.','.$chEpFon.','.$chEpFmu.','.$chEpPad.','.$chEpFam.','.$chEpCal.','.$chEpCaj.','.$chEpEla.','.$chEpNat.','.$chEpOtr.','.$chEpNin;
    if($UsEpAcu == ',,,,,,,,,,'){$UsEpAcu = $_POST['UsEpAcu'];}
    //LINEAS DE CREDITO DE INTERES:
    $chLcViv = $_GET['chLcViv'];    $chLcVeh = $_GET['chLcVeh'];    $chLcSal = $_GET['chLcSal'];    $chLcCir = $_GET['chLcCir'];    $chLcTur = $_GET['chLcTur'];
    $chLcEdf = $_GET['chLcEdf'];    $chLcEdp = $_GET['chLcEdp'];    $chLcCre = $_GET['chLcCre'];    $chLcMej = $_GET['chLcMej'];    $chLcCro = $_GET['chLcCro'];
    $chLcLib = $_GET['chLcLib'];    $chLcTar = $_GET['chLcTar'];    $chLcNin = $_GET['chLcNin'];
    $UsLcInt = $chLcViv.','.$chLcVeh.','.$chLcSal.','.$chLcCir.','.$chLcTur.','.$chLcEdf.','.$chLcEdp.','.$chLcCre.','.$chLcMej.','.$chLcCro.','.
               $chLcLib.','.$chLcTar.','.$chLcNin;
    if($UsLcInt == ',,,,,,,,,,,,'){$UsLcInt = $_POST['UsLcInt'];}
    //A TRAVES DE QUE INSTITUCIONES AHORRA:
    $chIaInv = $_GET['chIaInv'];   $chIaBan = $_GET['chIaBan'];   $chIaNat = $_GET['chIaNat'];   $chIaCac = $_GET['chIaCac'];   $chIaFem = $_GET['chIaFem'];
    $chIaFmu = $_GET['chIaFmu'];   $chIaFvp = $_GET['chIaFvp'];   $chIaOtr = $_GET['chIaOtr'];   $chIaNin = $_GET['chIaNin'];
    $UsIaAho = $chIaInv.','.$chIaBan.','.$chIaNat.','.$chIaCac.','.$chIaFem.','.$chIaFmu.','.$chIaFvp.','.$chIaOtr.','.$chIaNin;
    if($UsIaAho == ',,,,,,,,'){$UsIaAho = $_POST['UsIaAho'];}
    //NECESIDAD DE FORMACION:
    $chNfCap = $_GET['chNfCap'];    $chNfDes = $_GET['chNfDes'];    $chNfRel = $_GET['chNfRel'];    $chNfMan = $_GET['chNfMan'];    $chNfFin = $_GET['chNfFin'];
    $chNfFor = $_GET['chNfFor'];    $chNfIdi = $_GET['chNfIdi'];    $chNfInf = $_GET['chNfInf'];    $chNfFco = $_GET['chNfFco'];    $chNfOtr = $_GET['chNfOtr'];
    $chNfNot = $_GET['chNfNot'];
    $UsNefor = $chNfCap.','.$chNfDes.','.$chNfRel.','.$chNfMan.','.$chNfFin.','.$chNfFor.','.$chNfIdi.','.$chNfInf.','.$chNfFco.','.$chNfOtr.','.$chNfNot;
    if($UsNefor == ',,,,,,,,,,'){$UsNefor = $_POST['UsNefor'];}
    //HOBBIES:
    $chHoCin = $_GET['chHoCin'];    $chHoDep = $_GET['chHoDep'];    $chHoVid = $_GET['chHoVid'];    $chHoVte = $_GET['chHoVte'];    $chHoNav = $_GET['chHoNav'];
    $chHoIce = $_GET['chHoIce'];    $chHoIpa = $_GET['chHoIpa'];    $chHoIfi = $_GET['chHoIfi'];    $chHoCex = $_GET['chHoCex'];    $chHoDes = $_GET['chHoDes'];
    $chHoJar = $_GET['chHoJar'];    $chHoCon = $_GET['chHoCon'];    $chHoPin = $_GET['chHoPin'];    $chHoEsc = $_GET['chHoEsc'];    $chHoFot = $_GET['chHoFot'];
    $chHoVmu = $_GET['chHoVmu'];    $chHoVbi = $_GET['chHoVbi'];    $chHoEsp = $_GET['chHoEsp'];    $chHoDan = $_GET['chHoDan'];    $chHoTin = $_GET['chHoTin'];
    $chHoCoc = $_GET['chHoCoc'];    $chHoMan = $_GET['chHoMan'];    $chHoOtr = $_GET['chHoOtr'];    $chHoNin = $_GET['chHoNin'];
    $UsHobie = $chHoCin.','.$chHoDep.','.$chHoVid.','.$chHoVte.','.$chHoNav.','.$chHoIce.','.$chHoIpa.','.$chHoIfi.','.$chHoCex.','.$chHoDes.','.$chHoJar.','.
               $chHoCon.','.$chHoPin.','.$chHoEsc.','.$chHoFot.','.$chHoVmu.','.$chHoVbi.','.$chHoEsp.','.$chHoDan.','.$chHoTin.','.$chHoCoc.','.$chHoMan.','.
               $chHoOtr.','.$chHoNin;
    if($UsHobie == ',,,,,,,,,,,,,,,,,,,,,,,'){$UsHobie = $_POST['UsHobie'];}
    //CON QUIEN PASA SU TIEMPO DE ESPARCIMIENTO:
    $chQpHij = $_GET['chQpHij'];    $chQpAmi = $_GET['chQpAmi'];    $chQpMas = $_GET['chQpMas'];    $chQpSol = $_GET['chQpSol'];    $chQpFam = $_GET['chQpFam'];
    $chQpAmo = $_GET['chQpAmo'];    $chQpPar = $_GET['chQpPar'];    $chQpCom = $_GET['chQpCom'];    $chQpOtr = $_GET['chQpOtr'];
    $UsQpTie = $chQpHij.','.$chQpAmi.','.$chQpMas.','.$chQpSol.','.$chQpFam.','.$chQpAmo.','.$chQpPar.','.$chQpCom.','.$chQpOtr;
    if($UsQpTie == ',,,,,,,,'){$UsQpTie = $_POST['UsQpTie'];}
    //QUE HACEN HIJOS EN TIEMPO DE ESPARCIMIENTO:
    $chHhCin = $_GET['chHhCin'];    $chHhDep = $_GET['chHhDep'];    $chHhVid = $_GET['chHhVid'];    $chHhVte = $_GET['chHhVte'];    $chHhNav = $_GET['chHhNav'];
    $chHhIce = $_GET['chHhIce'];    $chHhIpa = $_GET['chHhIpa'];    $chHhIfi = $_GET['chHhIfi'];    $chHhCex = $_GET['chHhCex'];    $chHhDes = $_GET['chHhDes'];
    $chHhJar = $_GET['chHhJar'];    $chHhCon = $_GET['chHhCon'];    $chHhPin = $_GET['chHhPin'];    $chHhEsc = $_GET['chHhEsc'];    $chHhFot = $_GET['chHhFot'];
    $chHhVmu = $_GET['chHhVmu'];    $chHhVbi = $_GET['chHhVbi'];    $chHhEsp = $_GET['chHhEsp'];    $chHhDan = $_GET['chHhDan'];    $chHhTin = $_GET['chHhTin'];
    $chHhCoc = $_GET['chHhCoc'];    $chHhMan = $_GET['chHhMan'];    $chHhOtr = $_GET['chHhOtr'];    $chHhNin = $_GET['chHhNin'];
    $UsHhTli = $chHhCin.','.$chHhDep.','.$chHhVid.','.$chHhVte.','.$chHhNav.','.$chHhIce.','.$chHhIpa.','.$chHhIfi.','.$chHhCex.','.$chHhDes.','.$chHhJar.','.
               $chHhCon.','.$chHhPin.','.$chHhEsc.','.$chHhFot.','.$chHhVmu.','.$chHhVbi.','.$chHhEsp.','.$chHhDan.','.$chHhTin.','.$chHhCoc.','.$chHhMan.','.
               $chHhOtr.','.$chHhNin;
    if($UsHhTli == ',,,,,,,,,,,,,,,,,,,,,,,'){$UsHhTli = $_POST['UsHhTli'];}
    //BARRERAS USO TIEMPO LIBRE:
    $chBuFdi = $_GET['chBuFdi'];    $chBuNcd = $_GET['chBuNcd'];    $chBuDap = $_GET['chBuDap'];    $chBuFmo = $_GET['chBuFmo'];    $chBuNdt = $_GET['chBuNdt'];
    $chBuOtr = $_GET['chBuOtr'];    $chBuNin = $_GET['chBuNin'];
    $UsBuTli = $chBuFdi.','.$chBuNcd.','.$chBuDap.','.$chBuFmo.','.$chBuNdt.','.$chBuOtr.','.$chBuNin;
    if($UsBuTli == ',,,,,,'){$UsBuTli = $_POST['UsBuTli'];}
    //GUARDAR DATOS BASICOS PREVIOS:
    $IdefncP = $_GET['IdefncP'];    $IdegenP = $_GET['IdegenP'];    $IdecedP = $_GET['IdecedP'];
    $existCedula = datUserxEmp($wuse,$conex,24);
    ?>
</head>

<body>
<div class="container mainOps">
    <?php
    if($funcion == 'addEstudio')
    {
        ?>
        <h4 class="labelTitulo">EDUCACION - ADICIONAR ESTUDIO</h4>
        <div id="divDatEst" class="input-group">
            <form method="post" action="carEmp_Ops.php">
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="grado">Grado Escolar:</label></span>
                    <select id="grado" name="grado" class="form-control form-sm" style="width: 530px">
                        <?php
                        $queryEscolar = "select Scodes,Scocod from talento_000007 WHERE Scoest = 'on' ORDER BY Scodes ASC ";
                        $comQryEscolar = mysql_query($queryEscolar, $conex) or die (mysql_errno()." - en el query: ".$queryEscolar." - ".mysql_error());
                        while($datoEscolar = mysql_fetch_assoc($comQryEscolar))
                        {
                            $gradoEscolar = $datoEscolar['Scocod'].'-'.$datoEscolar['Scodes'];
                            ?><option><?php echo $gradoEscolar ?></option><?php
                        }

                        if($grado != null){?><option selected><?php echo $grado ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="titulo">Titulo Obtenido:</label></span>
                    <input type="text" id="titulo" name="titulo" class="form-control form-sm" style="width: 530px"
                           value="<?php if($titulo != null){echo $titulo;} ?>" list="listTit">
                    <datalist id="listTit">
                        <?php
                        $queryInst = "select Nompro from talento_000005 WHERE Activo = 'on' ORDER BY Nompro ASC ";
                        $comQryInst = mysql_query($queryInst, $conex) or die (mysql_errno()." - en el query: ".$queryInst." - ".mysql_error());
                        while($datoInst = mysql_fetch_assoc($comQryInst))
                        {
                            $nomProfesion = $datoInst['Nompro'];
                            ?><option><?php echo $nomProfesion ?></option><?php
                        }
                        if($nomProfesion != null){?><option selected><?php echo $nomProfesion ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </datalist>

                    <!--
                    <select id="titulo" name="titulo" class="form-control form-sm" style="width: 530px">
                        <?php
                        $queryInst = "select Nompro from talento_000005 WHERE Activo = 'on' ORDER BY Nompro ASC ";
                        $comQryInst = mysql_query($queryInst, $conex) or die (mysql_errno()." - en el query: ".$queryInst." - ".mysql_error());
                        while($datoInst = mysql_fetch_assoc($comQryInst))
                        {
                            $nomProf = $datoInst['Nompro'];
                            ?><option><?php echo $nomProf ?></option><?php
                        }
                        if($nomProfesion != null){?><option selected><?php echo $nomProfesion ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                    -->
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="instit">Nombre de la Institucion:</label></span>
                    <input type="text" id="instit" name="instit" class="form-control form-sm" style="width: 530px">
                    <!--
                    <select id="instit" name="instit" class="form-control form-sm" style="width: 530px">
                        <?php
                        $queryInst = "select Nomu from talento_000004 WHERE Activo = 'on' ORDER BY Nomu ASC ";
                        $comQryInst = mysql_query($queryInst, $conex) or die (mysql_errno()." - en el query: ".$queryInst." - ".mysql_error());
                        while($datoInst = mysql_fetch_assoc($comQryInst))
                        {
                            $nomInstitucion = $datoInst['Nomu'];
                            ?><option><?php echo $nomInstitucion ?></option><?php
                        }
                        if($instit != null){?><option selected><?php echo $instit ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                    -->
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="fecTit">Fecha:</label></span>
                    <input type="date" id="fecTit" name="fecTit" class="form-control form-sm inpEst" style="width: 150px"
                           value="<?php if($fecTit != null){echo $fecTit;} ?>" >
                </div>

                <div id="divAddEst2" align="center">
                    <input type="hidden" id="funcion" name="funcion" value="insertEstudio">
                    <input type="hidden" id="tablaUser" name="tablaUser" value="<?php echo $tbInfoEmpleado?>">  <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $usuarioMtx ?>">
                    <input type="hidden" name="Idefnc2" value="<?php echo $Idefnc ?>">              <input type="hidden" name="Idegen2" value="<?php echo $Idegen ?>">
                    <input type="hidden" name="Ideced2" value="<?php echo $Ideced ?>">              <input type="hidden" name="Idepas2" value="<?php echo $Idepas ?>">
                    <input type="hidden" name="Idevis2" value="<?php echo $Idevis ?>">              <input type="hidden" name="estcivUser2" value="<?php echo $estcivUser ?>">
                    <input type="hidden" name="Idedir2" value="<?php echo $Idedir ?>">              <input type="hidden" name="lunaUser2" value="<?php echo $lunaUser ?>">
                    <input type="hidden" name="estUser2" value="<?php echo $estUser ?>">            <input type="hidden" name="munUser2" value="<?php echo $munUser ?>">
                    <input type="hidden" name="barUser2" value="<?php echo $barUser ?>">            <input type="hidden" name="telUser2" value="<?php echo $telUser ?>">
                    <input type="hidden" name="celUser2" value="<?php echo $celUser ?>">            <input type="hidden" name="corUser2" value="<?php echo $corUser ?>">
                    <input type="hidden" name="tisanUser2" value="<?php echo $tisanUser ?>">        <input type="hidden" name="extUser2" value="<?php echo $extUser ?>">
                    <input type="hidden" name="Cviviv2" value="<?php echo $Cviviv ?>">              <input type="hidden" name="Cvitvi2" value="<?php echo $Cvitvi ?>">
                    <input type="hidden" name="Cvitrz2" value="<?php echo $Cvitrz ?>">              <input type="hidden" name="Cvilot2" value="<?php echo $Cvilot ?>">
                    <input type="hidden" name="Cvisvi2" value="<?php echo $Cvisvi ?>">              <input type="hidden" name="chAcue2" value="<?php echo $chAcue ?>">
                    <input type="hidden" name="chAlca2" value="<?php echo $chAlca ?>">              <input type="hidden" name="chAseo2" value="<?php echo $chAseo ?>">
                    <input type="hidden" name="chEner2" value="<?php echo $chEner ?>">              <input type="hidden" name="chInter2" value="<?php echo $chInter ?>">
                    <input type="hidden" name="chGas2" value="<?php echo $chGas ?>">                <input type="hidden" name="chTele2" value="<?php echo $chTele ?>">
                    <input type="hidden" name="credito2" value="<?php echo $Cvicre ?>">             <input type="hidden" name="chBici2" value="<?php echo $chBici ?>">
                    <input type="hidden" name="chBus2" value="<?php echo $chBus ?>">                <input type="hidden" name="chCamina2" value="<?php echo $chCamina ?>">
                    <input type="hidden" name="chPart2" value="<?php echo $chPart ?>">              <input type="hidden" name="chMetro2" value="<?php echo $chMetro ?>">
                    <input type="hidden" name="chMoto2" value="<?php echo $chMoto ?>">              <input type="hidden" name="chOtroT2" value="<?php echo $chOtroT ?>">
                    <input type="hidden" name="chTaxi2" value="<?php echo $chTaxi ?>">              <input type="hidden" name="chContra2" value="<?php echo $chContra ?>">
                    <input type="hidden" name="otroTrans2" value="<?php echo $Cviotr ?>">           <input type="hidden" name="chTrae2" value="<?php echo $chTrae ?>">
                    <input type="hidden" name="chComBoca2" value="<?php echo $chComBoca ?>">        <input type="hidden" name="chComOtros2" value="<?php echo $chComOtros ?>">
                    <input type="hidden" name="chCasa2" value="<?php echo $chCasa ?>">              <input type="hidden" name="chOtrosAl2" value="<?php echo $chOtrosAl ?>">
                    <input type="hidden" name="Cvioal2" value="<?php echo $Cvioal ?>">              <input type="hidden" name="chClaInfo2" value="<?php echo $chClaInfo ?>">
                    <input type="hidden" name="chClaIng2" value="<?php echo $chClaIng ?>">          <input type="hidden" name="chGastro2" value="<?php echo $chGastro ?>">
                    <input type="hidden" name="chConFami2" value="<?php echo $chConFami ?>">        <input type="hidden" name="chConCrePer2" value="<?php echo $chConCrePer ?>">
                    <input type="hidden" name="Cvihbb2" value="<?php echo $Cvihbb ?>">              <input type="hidden" name="Otrpar2" value="<?php echo $Otrpar ?>">
                    <input type="hidden" name="Otrlocker2" value="<?php echo $Otrlocker ?>">        <input type="hidden" name="Otractrec2" value="<?php echo $Otractrec ?>">
                    <input type="hidden" name="Otractrechor2" value="<?php echo $Otractrechor ?>">  <input type="hidden" name="Otractcul2" value="<?php echo $Otractcul ?>">
                    <input type="hidden" name="Otractculhor2" value="<?php echo $Otractculhor ?>">  <input type="hidden" name="chAudit2" value="<?php echo $chAudit ?>">
                    <input type="hidden" name="chComite2" value="<?php echo $chComite ?>">          <input type="hidden" name="chBrigada2" value="<?php echo $chBrigada ?>">
                    <input type="hidden" name="chOtroRol2" value="<?php echo $chOtroRol ?>">        <input type="hidden" name="otrRol2" value="<?php echo $Otrroles ?>">
                    <input type="hidden" name="contEmer2" value="<?php echo $contEmer ?>">          <input type="hidden" name="usuRaza2" value="<?php echo $usuRaza ?>">

                    <input type="hidden" name="telEmer" value="<?php echo $telEmer ?>">             <input type="hidden" name="usuGasto" value="<?php echo $usuGasto ?>">
                    <input type="hidden" name="usuSitua" value="<?php echo $usuSitua ?>">           <input type="hidden" name="posFam" value="<?php echo $posFam ?>">
                    <input type="hidden" name="usuCuHi" value="<?php echo $usuCuHi ?>">             <input type="hidden" name="subViv" value="<?php echo $subViv ?>">
                    <input type="hidden" name="ahoViv" value="<?php echo $ahoViv ?>">               <input type="hidden" name="montoHa" value="<?php echo $montoHa ?>">
                    <input type="hidden" name="Usfariesvi" value="<?php echo $Usfariesvi ?>">       <input type="hidden" name="UsuMejoVi" value="<?php echo $UsuMejoVi ?>">
                    <input type="hidden" name="UsProFi" value="<?php echo $UsProFi ?>">             <input type="hidden" name="UsMoCre" value="<?php echo $UsMoCre ?>">
                    <input type="hidden" name="UsEpAcu" value="<?php echo $UsEpAcu ?>">             <input type="hidden" name="UsLcInt" value="<?php echo $UsLcInt ?>">
                    <input type="hidden" name="UsIaAho" value="<?php echo $UsIaAho ?>">             <input type="hidden" name="UsNefor" value="<?php echo $UsNefor ?>">
                    <input type="hidden" name="UsHobie" value="<?php echo $UsHobie ?>">             <input type="hidden" name="UsQpTie" value="<?php echo $UsQpTie ?>">
                    <input type="hidden" name="UsHhTli" value="<?php echo $UsHhTli ?>">             <input type="hidden" name="UsBuTli" value="<?php echo $UsBuTli ?>">
                    <button type="submit" class="btn btn-default btn-lg">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true" style="width: 50px;"></span> Guardar
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    if($funcion == 'modEstudio')
    {
        //QUERY PARA SELECCIONAR LOS REGISTROS DE EDUCACION DEL USUARIO:
        $queryEduUser = "select * from".' '."$tbInfoEmpleado"."_000014 WHERE id = '$idEstud'";
        $commitQryEduUser = mysql_query($queryEduUser, $conex) or die (mysql_errno()." - en el query: ".$queryEduUser." - ".mysql_error());
        $datoEduUser = mysql_fetch_array($commitQryEduUser);
        $Edugrd = $datoEduUser['Edugrd'];   //$gradoEsc = datoEscolar($Edugrd,$conex);
        $Edutit = $datoEduUser['Edutit'];   $Eduins = $datoEduUser['Eduins'];
        $Eduani = $datoEduUser['Eduani'];
        ?>
        <h4 class="labelTitulo">EDUCACION - MODIFICAR ESTUDIO</h4>
        <div id="divDatEst" class="input-group">
            <form method="post" action="carEmp_Ops.php">
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="grado">Grado Escolar:</label></span>
                    <select id="grado" name="grado" class="form-control form-sm" style="width: 530px">
                        <?php
                        $queryEscolar = "select Scodes,Scocod from talento_000007 WHERE Scoest = 'on' ORDER BY Scodes ASC ";
                        $comQryEscolar = mysql_query($queryEscolar, $conex) or die (mysql_errno()." - en el query: ".$queryEscolar." - ".mysql_error());
                        while($datoEscolar = mysql_fetch_assoc($comQryEscolar))
                        {
                            $gradoEscolar = $datoEscolar['Scodes'];
                            ?><option><?php echo $gradoEscolar ?></option><?php
                        }
                        if($Edugrd != null){?><option selected><?php echo $Edugrd ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="titulo">Titulo Obtenido:</label></span>
                    <input type="text" id="titulo" name="titulo" class="form-control form-sm" style="width: 530px"
                           value="<?php if($Edutit != null){echo $Edutit;} ?>" list="listTit">
                    <datalist id="listTit">
                        <?php
                        $queryInst = "select Nompro from talento_000005 WHERE Activo = 'on' ORDER BY Nompro ASC ";
                        $comQryInst = mysql_query($queryInst, $conex) or die (mysql_errno()." - en el query: ".$queryInst." - ".mysql_error());
                        while($datoInst = mysql_fetch_assoc($comQryInst))
                        {
                            $nomProfesion = $datoInst['Nompro'];
                            ?><option><?php echo $nomProfesion ?></option><?php
                        }
                        if($Edutit != null){?><option selected><?php echo $Edutit ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </datalist>
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="instit">Nombre de la Institucion:</label></span>
                    <?php
                    if($Eduins != null)
                    {
                        ?>
                        <input type="text" id="instit" name="instit" class="form-control form-sm" style="width: 530px" value="<?php echo $Eduins ?>">
                        <?php
                    }
                    else
                    {
                        ?>
                        <input type="text" id="instit" name="instit" class="form-control form-sm" style="width: 530px">
                        <?php
                    }
                    ?>
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="fecTit">Fecha:</label></span>
                    <input type="date" id="fecTit" name="fecTit" class="form-control form-sm inpEst" style="width: 150px"
                           value="<?php if($Eduani != null){echo $Eduani;} ?>" >
                </div>

                <div id="divAddEst2" align="center">
                    <input type="hidden" id="funcion" name="funcion" value="updateEstudio">
                    <input type="hidden" id="tablaUser" name="tablaUser" value="<?php echo $tbInfoEmpleado?>">  <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $usuarioMtx ?>">
                    <input type="hidden" name="idEstud" value="<?php echo $idEstud ?>">
                    <input type="hidden" name="Idefnc2" value="<?php echo $Idefnc ?>">              <input type="hidden" name="Idegen2" value="<?php echo $Idegen ?>">
                    <input type="hidden" name="Ideced2" value="<?php echo $Ideced ?>">              <input type="hidden" name="Idepas2" value="<?php echo $Idepas ?>">
                    <input type="hidden" name="Idevis2" value="<?php echo $Idevis ?>">              <input type="hidden" name="estcivUser2" value="<?php echo $estcivUser ?>">
                    <input type="hidden" name="Idedir2" value="<?php echo $Idedir ?>">              <input type="hidden" name="lunaUser2" value="<?php echo $lunaUser ?>">
                    <input type="hidden" name="estUser2" value="<?php echo $estUser ?>">            <input type="hidden" name="munUser2" value="<?php echo $munUser ?>">
                    <input type="hidden" name="barUser2" value="<?php echo $barUser ?>">            <input type="hidden" name="telUser2" value="<?php echo $telUser ?>">
                    <input type="hidden" name="celUser2" value="<?php echo $celUser ?>">            <input type="hidden" name="corUser2" value="<?php echo $corUser ?>">
                    <input type="hidden" name="tisanUser2" value="<?php echo $tisanUser ?>">        <input type="hidden" name="extUser2" value="<?php echo $extUser ?>">
                    <input type="hidden" name="Cviviv2" value="<?php echo $Cviviv ?>">              <input type="hidden" name="Cvitvi2" value="<?php echo $Cvitvi ?>">
                    <input type="hidden" name="Cvitrz2" value="<?php echo $Cvitrz ?>">              <input type="hidden" name="Cvilot2" value="<?php echo $Cvilot ?>">
                    <input type="hidden" name="Cvisvi2" value="<?php echo $Cvisvi ?>">              <input type="hidden" name="chAcue2" value="<?php echo $chAcue ?>">
                    <input type="hidden" name="chAlca2" value="<?php echo $chAlca ?>">              <input type="hidden" name="chAseo2" value="<?php echo $chAseo ?>">
                    <input type="hidden" name="chEner2" value="<?php echo $chEner ?>">              <input type="hidden" name="chInter2" value="<?php echo $chInter ?>">
                    <input type="hidden" name="chGas2" value="<?php echo $chGas ?>">                <input type="hidden" name="chTele2" value="<?php echo $chTele ?>">
                    <input type="hidden" name="credito2" value="<?php echo $Cvicre ?>">             <input type="hidden" name="chBici2" value="<?php echo $chBici ?>">
                    <input type="hidden" name="chBus2" value="<?php echo $chBus ?>">                <input type="hidden" name="chCamina2" value="<?php echo $chCamina ?>">
                    <input type="hidden" name="chPart2" value="<?php echo $chPart ?>">              <input type="hidden" name="chMetro2" value="<?php echo $chMetro ?>">
                    <input type="hidden" name="chMoto2" value="<?php echo $chMoto ?>">              <input type="hidden" name="chOtroT2" value="<?php echo $chOtroT ?>">
                    <input type="hidden" name="chTaxi2" value="<?php echo $chTaxi ?>">              <input type="hidden" name="chContra2" value="<?php echo $chContra ?>">
                    <input type="hidden" name="otroTrans2" value="<?php echo $Cviotr ?>">           <input type="hidden" name="chTrae2" value="<?php echo $chTrae ?>">
                    <input type="hidden" name="chComBoca2" value="<?php echo $chComBoca ?>">        <input type="hidden" name="chComOtros2" value="<?php echo $chComOtros ?>">
                    <input type="hidden" name="chCasa2" value="<?php echo $chCasa ?>">              <input type="hidden" name="chOtrosAl2" value="<?php echo $chOtrosAl ?>">
                    <input type="hidden" name="Cvioal2" value="<?php echo $Cvioal ?>">              <input type="hidden" name="chClaInfo2" value="<?php echo $chClaInfo ?>">
                    <input type="hidden" name="chClaIng2" value="<?php echo $chClaIng ?>">          <input type="hidden" name="chGastro2" value="<?php echo $chGastro ?>">
                    <input type="hidden" name="chConFami2" value="<?php echo $chConFami ?>">        <input type="hidden" name="chConCrePer2" value="<?php echo $chConCrePer ?>">
                    <input type="hidden" name="Cvihbb2" value="<?php echo $Cvihbb ?>">              <input type="hidden" name="Otrpar2" value="<?php echo $Otrpar ?>">
                    <input type="hidden" name="Otrlocker2" value="<?php echo $Otrlocker ?>">        <input type="hidden" name="Otractrec2" value="<?php echo $Otractrec ?>">
                    <input type="hidden" name="Otractrechor2" value="<?php echo $Otractrechor ?>">  <input type="hidden" name="Otractcul2" value="<?php echo $Otractcul ?>">
                    <input type="hidden" name="Otractculhor2" value="<?php echo $Otractculhor ?>">  <input type="hidden" name="chAudit2" value="<?php echo $chAudit ?>">
                    <input type="hidden" name="chComite2" value="<?php echo $chComite ?>">          <input type="hidden" name="chBrigada2" value="<?php echo $chBrigada ?>">
                    <input type="hidden" name="chOtroRol2" value="<?php echo $chOtroRol ?>">        <input type="hidden" name="otrRol2" value="<?php echo $Otrroles ?>">
                    <input type="hidden" name="contEmer2" value="<?php echo $contEmer ?>">          <input type="hidden" name="usuRaza2" value="<?php echo $usuRaza ?>">

                    <input type="hidden" name="telEmer" value="<?php echo $telEmer ?>">             <input type="hidden" name="usuGasto" value="<?php echo $usuGasto ?>">
                    <input type="hidden" name="usuSitua" value="<?php echo $usuSitua ?>">           <input type="hidden" name="posFam" value="<?php echo $posFam ?>">
                    <input type="hidden" name="usuCuHi" value="<?php echo $usuCuHi ?>">             <input type="hidden" name="subViv" value="<?php echo $subViv ?>">
                    <input type="hidden" name="ahoViv" value="<?php echo $ahoViv ?>">               <input type="hidden" name="montoHa" value="<?php echo $montoHa ?>">
                    <input type="hidden" name="Usfariesvi" value="<?php echo $Usfariesvi ?>">       <input type="hidden" name="UsuMejoVi" value="<?php echo $UsuMejoVi ?>">
                    <input type="hidden" name="UsProFi" value="<?php echo $UsProFi ?>">             <input type="hidden" name="UsMoCre" value="<?php echo $UsMoCre ?>">
                    <input type="hidden" name="UsEpAcu" value="<?php echo $UsEpAcu ?>">             <input type="hidden" name="UsLcInt" value="<?php echo $UsLcInt ?>">
                    <input type="hidden" name="UsIaAho" value="<?php echo $UsIaAho ?>">             <input type="hidden" name="UsNefor" value="<?php echo $UsNefor ?>">
                    <input type="hidden" name="UsHobie" value="<?php echo $UsHobie ?>">             <input type="hidden" name="UsQpTie" value="<?php echo $UsQpTie ?>">
                    <input type="hidden" name="UsHhTli" value="<?php echo $UsHhTli ?>">             <input type="hidden" name="UsBuTli" value="<?php echo $UsBuTli ?>">
                    <button type="submit" class="btn btn-default btn-lg">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true" style="width: 50px;"></span> Guardar
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    if($funcion == 'insertEstudio')
    {
        if($grado != null and $titulo != null and $instit != null and $fecTit != null)
        {
            $pieces = explode("-",$grado);  $codGrado = $pieces[0]; $desGrado = $pieces[1];

            //DATOS DE ESTUDIOS
            $qryInsEst = "insert into".' '."$tablaUser"."_000014
                          values('talhuma','$fechaActual','$horaActual','$grado','$titulo','$instit','$fecTit','$userMtx','on','C-$wuse','')";
            $comQryInsEst = mysql_query($qryInsEst, $conex) or die (mysql_errno()." - en el query: ".$qryInsEst." - ".mysql_error());

            //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_24(Condiciones de vida y vivienda):
            saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
                $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
                $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
                $Cvioal,$wuse);

            //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_60(Respuesta Otras Preguntas Caracterizacion):
            saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
                $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
                $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
                $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
                $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse);

            //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:
            saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,$tisanUser,
                $wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$cedula);

            //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
            saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
                $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
                $UsBuTli);

            obtenerOpenTab($conex,$wuse,$fechaActual,2);

            if($comQryInsEst)
            {
                ?>
                <div id="divAddEst2" align="center" style="margin-top: 80px">
                    <input type="hidden" id="funcion" name="funcion" value="insertEstudio">
                    <button type="submit" class="btn btn-default btn-lg"
                            onclick="window.opener.location.reload(); window.close();">
                        <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Guardado
                    </button>
                </div>
                <?php
            }
            else
            {
                ?>
                <div id="divAddEst2" align="center">
                    <h3>No se pudo guardar el registro</h3><h3>por favor verifique</h3>
                </div>

                <form method="post" action="carEmp_Ops.php">
                    <input type="hidden" id="grado" name="grado" value="<?php echo $grado ?>">      <input type="hidden" id="titulo" name="titulo" value="<?php echo $titulo ?>">
                    <input type="hidden" id="instit" name="instit" value="<?php echo $instit ?>">   <input type="hidden" id="fecTit" name="fecTit" value="<?php echo $fecTit ?>">
                    <input type="hidden" id="funcion" name="funcion" value="addEstudio">

                    <div id="divAddEst2" align="center">
                        <button type="submit" class="btn btn-warning btn-lg" onclick="openOps(funcion)">
                            <span class="glyphicon glyphicon-warning-sign" aria-hidden="true" style="width: 50px;"></span> Aceptar
                        </button>
                    </div>
                </form>
                <?php
            }
        }
        else
        {
            ?>
            <div id="divAddEst2" align="center">
                <h3>Todos los campos son obligatorios</h3><h3>por favor verifique</h3>
            </div>

            <form method="post" action="carEmp_Ops.php">
                <input type="hidden" id="grado" name="grado" value="<?php echo $grado ?>">              <input type="hidden" id="titulo" name="titulo" value="<?php echo $titulo ?>">
                <input type="hidden" id="instit" name="instit" value="<?php echo $instit ?>">           <input type="hidden" id="fecTit" name="fecTit" value="<?php echo $fecTit ?>">
                <input type="hidden" id="userMatrix" name="userMatrix" value="<?php echo $userMtx ?>">
                <input type="hidden" id="funcion" name="funcion" value="addEstudio">

                <div id="divAddEst2" align="center">
                    <button type="submit" class="btn btn-warning btn-lg" onclick="openOps(funcion)">
                        <span class="glyphicon glyphicon-warning-sign" aria-hidden="true" style="width: 50px;"></span> Aceptar
                    </button>
                </div>
            </form>
            <?php
        }
    }

    if($funcion == 'supEstudio')
    {
        $qryDelEstudio = "delete from ".' '."$tablaUser"."_000014 where id = '$idEstud'";
        $comQryDelEst = mysql_query($qryDelEstudio, $conex) or die (mysql_errno()." - en el query: ".$qryDelEstudio." - ".mysql_error());

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_24(Condiciones de vida y vivienda):
        saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
            $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
            $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
            $Cvioal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_60(Respuesta Otras Preguntas Caracterizacion):
        saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
            $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
            $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
            $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
            $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:
        saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,$tisanUser,
            $wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$cedula);

        //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
        saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
            $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
            $UsBuTli);

        obtenerOpenTab($conex,$wuse,$fechaActual,2);

        ?>
        <div id="divAddEst2" align="center" style="margin-top: 80px">
            <button type="submit" class="btn btn-default btn-lg" onclick="window.opener.location.reload(); window.close()">
                <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Eliminado
            </button>
        </div>
        <?php
    }

    if($funcion == 'updateEstudio')
    {
        //$pieces = explode("-",$grado);  $codGrado = $pieces[0]; $desGrado = $pieces[1];

        $qryDelEstudio = "update".' '."$tablaUser"."_000014
                            set Edugrd = '$grado',Edutit = '$titulo',Eduins = '$instit',Eduani = '$fecTit'
                          where id = '$idEstud'";
        $comQryDelEst = mysql_query($qryDelEstudio, $conex) or die (mysql_errno()." - en el query: ".$qryDelEstudio." - ".mysql_error());

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_24(Condiciones de vida y vivienda):
        saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
            $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
            $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
            $Cvioal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_60(Respuesta Otras Preguntas Caracterizacion):
        saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
            $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
            $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
            $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
            $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:
        saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,$tisanUser,
            $wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$cedula);

        //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
        saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
            $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
            $UsBuTli);

        obtenerOpenTab($conex,$wuse,$fechaActual,2);

        ?>
        <div id="divAddEst2" align="center" style="margin-top: 80px">
            <button type="submit" class="btn btn-default btn-lg" onclick="window.opener.location.reload(); window.close()">
                <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Actualizado
            </button>
        </div>
        <?php
    }

    if($funcion == 'supCredito')
    {
        $qryDelCredito = "delete from ".' '."$tablaUser"."_000025 where id = '$idEstud'";
        $comQryDelCred = mysql_query($qryDelCredito, $conex) or die (mysql_errno()." - en el query: ".$qryDelCredito." - ".mysql_error());

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_24(Condiciones de vida y vivienda):
        saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
            $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
            $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
            $Cvioal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_60(Respuesta Otras Preguntas Caracterizacion):
        saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
            $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
            $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
            $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
            $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:
        saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,$tisanUser,
            $wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$cedula);

        //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
        saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
            $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
            $UsBuTli);

        obtenerOpenTab($conex,$wuse,$fechaActual,4);

        ?>
        <div id="divAddEst2" align="center" style="margin-top: 80px">
            <input type="hidden" id="funcion" name="funcion" value="insertCredito">
            <button type="submit" class="btn btn-default btn-lg"
                    onclick="window.opener.location.reload(); window.close()">
                <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Eliminado
            </button>
        </div>
        <?php
    }

    if($funcion == 'addIdioma')
    {
        ?>
        <h4 class="labelTitulo">IDIOMA - ADICIONAR IDIOMA</h4>
        <div id="divDatEst" class="input-group">
            <form method="post" action="carEmp_Ops.php">
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="idiomaNom">NOMBRE IDIOMA:</label></span>
                    <input type="text" id="idiomaNom" name="idiomaNom" class="form-control form-sm inpEst" style="width: 530px"
                           value="<?php if($idiomaNom != null){echo $idiomaNom;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="idiomaHab">Lo habla:</label></span>
                    <select id="idiomaHab" name="idiomaHab" class="form-control form-sm inpEst" style="width: 200px">
                        <option>SI</option>
                        <option>NO</option>
                        <?php
                        if($idiomaHab != null){?><option selected><?php echo $idiomaHab ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="idiomaLee">Lo lee:</label></span>
                    <select id="idiomaLee" name="idiomaLee" class="form-control form-sm" style="width: 200px">
                        <option>SI</option>
                        <option>NO</option>
                        <?php
                        if($idiomaLee != null){?><option selected><?php echo $idiomaLee ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="idiomaEsc">Lo escribe:</label></span>
                    <select id="idiomaEsc" name="idiomaEsc" class="form-control form-sm inpEst" style="width: 200px">
                        <option>SI</option>
                        <option>NO</option>
                        <?php
                        if($idiomaEsc != null){?><option selected><?php echo $idiomaEsc ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                </div>

                <div id="divAddEst2" align="center">
                    <input type="hidden" id="funcion" name="funcion" value="insertIdioma">
                    <input type="hidden" id="tablaUser" name="tablaUser" value="<?php echo $tbInfoEmpleado?>">  <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $usuarioMtx ?>">
                    <input type="hidden" name="Idefnc2" value="<?php echo $Idefnc ?>">              <input type="hidden" name="Idegen2" value="<?php echo $Idegen ?>">
                    <input type="hidden" name="Ideced2" value="<?php echo $Ideced ?>">              <input type="hidden" name="Idepas2" value="<?php echo $Idepas ?>">
                    <input type="hidden" name="Idevis2" value="<?php echo $Idevis ?>">              <input type="hidden" name="estcivUser2" value="<?php echo $estcivUser ?>">
                    <input type="hidden" name="Idedir2" value="<?php echo $Idedir ?>">              <input type="hidden" name="lunaUser2" value="<?php echo $lunaUser ?>">
                    <input type="hidden" name="estUser2" value="<?php echo $estUser ?>">            <input type="hidden" name="munUser2" value="<?php echo $munUser ?>">
                    <input type="hidden" name="barUser2" value="<?php echo $barUser ?>">            <input type="hidden" name="telUser2" value="<?php echo $telUser ?>">
                    <input type="hidden" name="celUser2" value="<?php echo $celUser ?>">            <input type="hidden" name="corUser2" value="<?php echo $corUser ?>">
                    <input type="hidden" name="tisanUser2" value="<?php echo $tisanUser ?>">        <input type="hidden" name="extUser2" value="<?php echo $extUser ?>">
                    <input type="hidden" name="Cviviv2" value="<?php echo $Cviviv ?>">              <input type="hidden" name="Cvitvi2" value="<?php echo $Cvitvi ?>">
                    <input type="hidden" name="Cvitrz2" value="<?php echo $Cvitrz ?>">              <input type="hidden" name="Cvilot2" value="<?php echo $Cvilot ?>">
                    <input type="hidden" name="Cvisvi2" value="<?php echo $Cvisvi ?>">              <input type="hidden" name="chAcue2" value="<?php echo $chAcue ?>">
                    <input type="hidden" name="chAlca2" value="<?php echo $chAlca ?>">              <input type="hidden" name="chAseo2" value="<?php echo $chAseo ?>">
                    <input type="hidden" name="chEner2" value="<?php echo $chEner ?>">              <input type="hidden" name="chInter2" value="<?php echo $chInter ?>">
                    <input type="hidden" name="chGas2" value="<?php echo $chGas ?>">                <input type="hidden" name="chTele2" value="<?php echo $chTele ?>">
                    <input type="hidden" name="credito2" value="<?php echo $Cvicre ?>">             <input type="hidden" name="chBici2" value="<?php echo $chBici ?>">
                    <input type="hidden" name="chBus2" value="<?php echo $chBus ?>">                <input type="hidden" name="chCamina2" value="<?php echo $chCamina ?>">
                    <input type="hidden" name="chPart2" value="<?php echo $chPart ?>">              <input type="hidden" name="chMetro2" value="<?php echo $chMetro ?>">
                    <input type="hidden" name="chMoto2" value="<?php echo $chMoto ?>">              <input type="hidden" name="chOtroT2" value="<?php echo $chOtroT ?>">
                    <input type="hidden" name="chTaxi2" value="<?php echo $chTaxi ?>">              <input type="hidden" name="chContra2" value="<?php echo $chContra ?>">
                    <input type="hidden" name="otroTrans2" value="<?php echo $Cviotr ?>">           <input type="hidden" name="chTrae2" value="<?php echo $chTrae ?>">
                    <input type="hidden" name="chComBoca2" value="<?php echo $chComBoca ?>">        <input type="hidden" name="chComOtros2" value="<?php echo $chComOtros ?>">
                    <input type="hidden" name="chCasa2" value="<?php echo $chCasa ?>">              <input type="hidden" name="chOtrosAl2" value="<?php echo $chOtrosAl ?>">
                    <input type="hidden" name="Cvioal2" value="<?php echo $Cvioal ?>">              <input type="hidden" name="chClaInfo2" value="<?php echo $chClaInfo ?>">
                    <input type="hidden" name="chClaIng2" value="<?php echo $chClaIng ?>">          <input type="hidden" name="chGastro2" value="<?php echo $chGastro ?>">
                    <input type="hidden" name="chConFami2" value="<?php echo $chConFami ?>">        <input type="hidden" name="chConCrePer2" value="<?php echo $chConCrePer ?>">
                    <input type="hidden" name="Cvihbb2" value="<?php echo $Cvihbb ?>">              <input type="hidden" name="Otrpar2" value="<?php echo $Otrpar ?>">
                    <input type="hidden" name="Otrlocker2" value="<?php echo $Otrlocker ?>">        <input type="hidden" name="Otractrec2" value="<?php echo $Otractrec ?>">
                    <input type="hidden" name="Otractrechor2" value="<?php echo $Otractrechor ?>">  <input type="hidden" name="Otractcul2" value="<?php echo $Otractcul ?>">
                    <input type="hidden" name="Otractculhor2" value="<?php echo $Otractculhor ?>">  <input type="hidden" name="chAudit2" value="<?php echo $chAudit ?>">
                    <input type="hidden" name="chComite2" value="<?php echo $chComite ?>">          <input type="hidden" name="chBrigada2" value="<?php echo $chBrigada ?>">
                    <input type="hidden" name="chOtroRol2" value="<?php echo $chOtroRol ?>">        <input type="hidden" name="otrRol2" value="<?php echo $Otrroles ?>">
                    <input type="hidden" name="contEmer2" value="<?php echo $contEmer ?>">          <input type="hidden" name="usuRaza2" value="<?php echo $usuRaza ?>">

                    <input type="hidden" name="telEmer" value="<?php echo $telEmer ?>">             <input type="hidden" name="usuGasto" value="<?php echo $usuGasto ?>">
                    <input type="hidden" name="usuSitua" value="<?php echo $usuSitua ?>">           <input type="hidden" name="posFam" value="<?php echo $posFam ?>">
                    <input type="hidden" name="usuCuHi" value="<?php echo $usuCuHi ?>">             <input type="hidden" name="subViv" value="<?php echo $subViv ?>">
                    <input type="hidden" name="ahoViv" value="<?php echo $ahoViv ?>">               <input type="hidden" name="montoHa" value="<?php echo $montoHa ?>">
                    <input type="hidden" name="Usfariesvi" value="<?php echo $Usfariesvi ?>">       <input type="hidden" name="UsuMejoVi" value="<?php echo $UsuMejoVi ?>">
                    <input type="hidden" name="UsProFi" value="<?php echo $UsProFi ?>">             <input type="hidden" name="UsMoCre" value="<?php echo $UsMoCre ?>">
                    <input type="hidden" name="UsEpAcu" value="<?php echo $UsEpAcu ?>">             <input type="hidden" name="UsLcInt" value="<?php echo $UsLcInt ?>">
                    <input type="hidden" name="UsIaAho" value="<?php echo $UsIaAho ?>">             <input type="hidden" name="UsNefor" value="<?php echo $UsNefor ?>">
                    <input type="hidden" name="UsHobie" value="<?php echo $UsHobie ?>">             <input type="hidden" name="UsQpTie" value="<?php echo $UsQpTie ?>">
                    <input type="hidden" name="UsHhTli" value="<?php echo $UsHhTli ?>">             <input type="hidden" name="UsBuTli" value="<?php echo $UsBuTli ?>">
                    <button type="submit" class="btn btn-default btn-lg">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true" style="width: 50px;"></span> Guardar
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    if($funcion == 'insertIdioma')
    {
        if($idiomaNom != null and $idiomaHab != null and $idiomaLee != null and $idiomaEsc != null)
        {
            if($idiomaHab == 'SI'){$idiomaHab = 'on';} else{$idiomaHab = 'off';}
            if($idiomaLee == 'SI'){$idiomaLee = 'on';} else{$idiomaLee = 'off';}
            if($idiomaEsc == 'SI'){$idiomaEsc = 'on';} else{$idiomaEsc = 'off';}

            $queryInsIdioma = "insert into".' '."$tablaUser"."_000015
                          values('talhuma','$fechaActual','$horaActual','$idiomaNom','$idiomaHab','$idiomaLee','$idiomaEsc','$userMtx','on','C-$wuse','')";
            $comQryInsIdioma = mysql_query($queryInsIdioma, $conex) or die (mysql_errno()." - en el query: ".$queryInsIdioma." - ".mysql_error());

            //obtenerOpenTab($conex,$wuse,$fechaActual,2);

            //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:

            $query1 = "select id from".' '."$tablaUser"."_000013 where Ideuse = '$userMtx'";
            $commit1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
            $datoEmpl = mysql_fetch_assoc($commit1);
            $idEmpl = $datoEmpl['id'];

            if($idEmpl == null)
            {
                $queryIns1 = "insert into".' '."$tablaUser"."_000013
                          values('talhuma','$fechaActual','$horaActual','$Idepas','$Idevis','','$estcivUser','$lunaUser','$munUser','$barUser',
                                 '$estUser','$tisanUser','',
                                 'on','C-$wuse','')";
                $comQryIns1 = mysql_query($queryIns1, $conex) or die (mysql_errno()." - en el query: ".$queryIns1." - ".mysql_error());
            }
            else
            {
                //OBTENER CODIGO DEL MUNICIPIO:
                $QueryMuni = "select Codigo from root_000006 WHERE Nombre LIKE '$munUser2'";
                $commMuni = mysql_query($QueryMuni, $conex) or die (mysql_errno()." - en el query: ".$QueryMuni." - ".mysql_error());
                $datoMuni = mysql_fetch_assoc($commMuni);
                $codMuni2 = $datoMuni['Codigo'];

                //OBTENER CODIGO DEL BARRIO:
                $QueryBarr = "select Barcod from root_000034 WHERE Bardes LIKE '$barUser2' AND Barmun = '$codMuni2'";
                $commBarr = mysql_query($QueryBarr, $conex) or die (mysql_errno()." - en el query: ".$QueryBarr." - ".mysql_error());
                $datoBarr = mysql_fetch_assoc($commBarr);
                $codBarr2 = $datoBarr['Barcod'];

                //OBTENER ESTADO CIVIL:
                $QueryEstCiv = "select Scvcod from root_000065 WHERE Scvdes LIKE '$estcivUser2'";
                $commEstCiv = mysql_query($QueryEstCiv, $conex) or die (mysql_errno()." - en el query: ".$QueryBarr." - ".mysql_error());
                $datoEstCiv = mysql_fetch_assoc($commEstCiv);
                $codEstCiv2 = $datoEstCiv['Scvcod'];

                if($Idegen2 == 'Masculino'){$Idegen2 = 'M';} else{$Idegen2 = 'F';}

                //INFORMACION GENERAL
                $queryUpd1 = "update".' '."$tablaUser"."_000013 set Fecha_data = '$fechaActual',Hora_data = '$horaActual',Idepas = '$Idepas2',
                            Idevis = '$Idevis2',Idepvi = '',Ideesc = '$codEstCiv2',Ideinc = '$lunaUser2',Idempo = '$codMuni2',Idebrr = '$codBarr2',
                            Idestt = '$estUser2',Idesrh = '$tisanUser2',Idefnc = '$Idefnc2',Idegen = '$Idegen2',Idedir = '$Idedir2',Idetel = '$telUser2',
                            Idecel = '$celUser2',Ideeml = '$corUser2',Ideext = '$extUser2'
                          where Ideuse = '$userMtx'";
                $comUpd1 = mysql_query($queryUpd1, $conex) or die (mysql_errno()." - en el query: ".$queryUpd1." - ".mysql_error());
            }

            //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
            saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
                $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
                $UsBuTli);

            obtenerOpenTab($conex,$wuse,$fechaActual,2);

            if($comQryInsIdioma)
            {
                ?>
                <div id="divAddEst2" align="center" style="margin-top: 80px">
                    <input type="hidden" id="funcion" name="funcion" value="insertIdioma">
                    <button type="submit" class="btn btn-default btn-lg"
                            onclick="window.opener.location.reload(); window.close();">
                        <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Guardado
                    </button>
                </div>
                <?php
            }
            else
            {
                ?>
                <div id="divAddEst2" align="center">
                    <h3>No se pudo guardar el registro</h3><h3>por favor verifique</h3>
                </div>

                <form method="post" action="carEmp_Ops.php">
                    <input type="hidden" id="idiomaNom" name="idiomaNom" value="<?php echo $idiomaNom ?>">
                    <input type="hidden" id="idiomaHab" name="idiomaHab" value="<?php echo $idiomaHab ?>">
                    <input type="hidden" id="idiomaLee" name="idiomaLee" value="<?php echo $idiomaLee ?>">
                    <input type="hidden" id="idiomaEsc" name="idiomaEsc" value="<?php echo $idiomaEsc ?>">
                    <input type="hidden" id="userMatrix" name="userMatrix" value="<?php echo $userMtx ?>">
                    <input type="hidden" id="funcion" name="funcion" value="addIdioma">

                    <div id="divAddEst2" align="center">
                        <button type="submit" class="btn btn-warning btn-lg" onclick="openOps(funcion)">
                            <span class="glyphicon glyphicon-warning-sign" aria-hidden="true" style="width: 50px;"></span> Aceptar
                        </button>
                    </div>
                </form>
                <?php
            }

        }
        else
        {
            ?>
            <div id="divAddEst2" align="center">
                <h3>Todos los campos son obligatorios</h3><h3>por favor verifique</h3>
            </div>

            <form method="post" action="carEmp_Ops.php">
                <input type="hidden" id="idiomaNom" name="idiomaNom" value="<?php echo $idiomaNom ?>">
                <input type="hidden" id="idiomaHab" name="idiomaHab" value="<?php echo $idiomaHab ?>">
                <input type="hidden" id="idiomaLee" name="idiomaLee" value="<?php echo $idiomaLee ?>">
                <input type="hidden" id="idiomaEsc" name="idiomaEsc" value="<?php echo $idiomaEsc ?>">
                <input type="hidden" id="userMatrix" name="userMatrix" value="<?php echo $userMtx ?>">
                <input type="hidden" id="funcion" name="funcion" value="addIdioma">

                <div id="divAddEst2" align="center">
                    <button type="submit" class="btn btn-warning btn-lg" onclick="openOps(funcion)">
                        <span class="glyphicon glyphicon-warning-sign" aria-hidden="true" style="width: 50px;"></span> Aceptar
                    </button>
                </div>
            </form>
            <?php
        }

    }

    if($funcion == 'supIdioma')
    {
        $queryDelIdio = "delete from ".' '."$tablaUser"."_000015 where id = '$idEstud'";
        $comQryDelIdio = mysql_query($queryDelIdio, $conex) or die (mysql_errno()." - en el query: ".$queryDelIdio." - ".mysql_error());

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_24(Condiciones de vida y vivienda):
        saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
            $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
            $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
            $Cvioal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_60(Respuesta Otras Preguntas Caracterizacion):
        saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
            $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
            $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
            $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
            $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:
        saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,$tisanUser,
            $wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$cedula);

        //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
        saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
            $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
            $UsBuTli);

        obtenerOpenTab($conex,$wuse,$fechaActual,2);

        ?>
        <div id="divAddEst2" align="center" style="margin-top: 80px">
            <button type="submit" class="btn btn-default btn-lg" onclick="window.close();window.opener.location.reload()">
                <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Eliminado
            </button>
        </div>
        <?php
    }

    if($funcion == 'addEstActual')
    {
        ?>
        <h4 class="labelTitulo">ESTUDIO ACTUAL - ADICIONAR ESTUDIO</h4>
        <div id="divDatEst" class="input-group">
            <form method="post" action="carEmp_Ops.php">
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="queEstud">QUÉ ESTUDIA:</label></span>
                    <input type="text" id="queEstud" name="queEstud" class="form-control form-sm inpEst" style="width: 530px"
                           value="<?php if($queEstud != null){echo $queEstud;} ?>">
                    <!--
                    <datalist id="listProf">
                        <?php
                        $qryProf = "select descProf from equipos_000020 ORDER BY descProf ASC";
                        $comProf = mysql_query($qryProf, $conex) or die (mysql_errno()." - en el query: ".$qryProf." - ".mysql_error());
                        while($datProf = mysql_fetch_assoc($comProf))
                        {
                            $descripcionP = $datProf['descProf'];
                            ?><option value="<?php echo $descripcionP  ?>"><?php
                        }
                        ?>
                    </datalist>
                    -->
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="durEstud">DURACION:</label></span>
                    <input type="text" id="durEstud" name="durEstud" class="form-control form-sm inpEst" style="width: 300px"
                           value="<?php if($durEstud != null){echo $durEstud;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="intEstud">INSTITUCION EDUCATIVA:</label></span>
                    <input type="text" id="intEstud" name="intEstud" class="form-control form-sm" style="width: 530px"
                           value="<?php if($intEstud != null){echo $intEstud;} ?>" >
                    <!--
                    <select id="intEstud" name="intEstud" class="form-control form-sm" style="width: 530px">
                        <?php
                        $queryInst = "select Nomu from talento_000004 WHERE Activo = 'on' ORDER BY Nomu ASC ";
                        $comQryInst = mysql_query($queryInst, $conex) or die (mysql_errno()." - en el query: ".$queryInst." - ".mysql_error());
                        while($datoInst = mysql_fetch_assoc($comQryInst))
                        {
                            $intEstud = $datoInst['Nomu'];
                            ?><option><?php echo $intEstud ?></option><?php
                        }
                        if($intEstud != null){?><option selected><?php echo $intEstud ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                    -->
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="nivEstud">NIVEL ACTUAL:</label></span>
                    <input type="text" id="nivEstud" name="nivEstud" class="form-control form-sm inpEst" style="width: 300px"
                           value="<?php if($nivEstud != null){echo $nivEstud;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="horEstud">HORARIO:</label></span>
                    <input type="text" id="horEstud" name="horEstud" class="form-control form-sm inpEst" style="width: 300px"
                           value="<?php if($horEstud != null){echo $horEstud;} ?>" >
                </div>

                <div id="divAddEst2" align="center" style="margin-top: 10px">
                    <input type="hidden" id="funcion" name="funcion" value="insertEstActual">
                    <input type="hidden" id="tablaUser" name="tablaUser" value="<?php echo $tbInfoEmpleado?>">  <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $usuarioMtx ?>">
                    <input type="hidden" name="Idefnc2" value="<?php echo $Idefnc ?>">              <input type="hidden" name="Idegen2" value="<?php echo $Idegen ?>">
                    <input type="hidden" name="Ideced2" value="<?php echo $Ideced ?>">              <input type="hidden" name="Idepas2" value="<?php echo $Idepas ?>">
                    <input type="hidden" name="Idevis2" value="<?php echo $Idevis ?>">              <input type="hidden" name="estcivUser2" value="<?php echo $estcivUser ?>">
                    <input type="hidden" name="Idedir2" value="<?php echo $Idedir ?>">              <input type="hidden" name="lunaUser2" value="<?php echo $lunaUser ?>">
                    <input type="hidden" name="estUser2" value="<?php echo $estUser ?>">            <input type="hidden" name="munUser2" value="<?php echo $munUser ?>">
                    <input type="hidden" name="barUser2" value="<?php echo $barUser ?>">            <input type="hidden" name="telUser2" value="<?php echo $telUser ?>">
                    <input type="hidden" name="celUser2" value="<?php echo $celUser ?>">            <input type="hidden" name="corUser2" value="<?php echo $corUser ?>">
                    <input type="hidden" name="tisanUser2" value="<?php echo $tisanUser ?>">        <input type="hidden" name="extUser2" value="<?php echo $extUser ?>">
                    <input type="hidden" name="Cviviv2" value="<?php echo $Cviviv ?>">              <input type="hidden" name="Cvitvi2" value="<?php echo $Cvitvi ?>">
                    <input type="hidden" name="Cvitrz2" value="<?php echo $Cvitrz ?>">              <input type="hidden" name="Cvilot2" value="<?php echo $Cvilot ?>">
                    <input type="hidden" name="Cvisvi2" value="<?php echo $Cvisvi ?>">              <input type="hidden" name="chAcue2" value="<?php echo $chAcue ?>">
                    <input type="hidden" name="chAlca2" value="<?php echo $chAlca ?>">              <input type="hidden" name="chAseo2" value="<?php echo $chAseo ?>">
                    <input type="hidden" name="chEner2" value="<?php echo $chEner ?>">              <input type="hidden" name="chInter2" value="<?php echo $chInter ?>">
                    <input type="hidden" name="chGas2" value="<?php echo $chGas ?>">                <input type="hidden" name="chTele2" value="<?php echo $chTele ?>">
                    <input type="hidden" name="credito2" value="<?php echo $Cvicre ?>">             <input type="hidden" name="chBici2" value="<?php echo $chBici ?>">
                    <input type="hidden" name="chBus2" value="<?php echo $chBus ?>">                <input type="hidden" name="chCamina2" value="<?php echo $chCamina ?>">
                    <input type="hidden" name="chPart2" value="<?php echo $chPart ?>">              <input type="hidden" name="chMetro2" value="<?php echo $chMetro ?>">
                    <input type="hidden" name="chMoto2" value="<?php echo $chMoto ?>">              <input type="hidden" name="chOtroT2" value="<?php echo $chOtroT ?>">
                    <input type="hidden" name="chTaxi2" value="<?php echo $chTaxi ?>">              <input type="hidden" name="chContra2" value="<?php echo $chContra ?>">
                    <input type="hidden" name="otroTrans2" value="<?php echo $Cviotr ?>">           <input type="hidden" name="chTrae2" value="<?php echo $chTrae ?>">
                    <input type="hidden" name="chComBoca2" value="<?php echo $chComBoca ?>">        <input type="hidden" name="chComOtros2" value="<?php echo $chComOtros ?>">
                    <input type="hidden" name="chCasa2" value="<?php echo $chCasa ?>">              <input type="hidden" name="chOtrosAl2" value="<?php echo $chOtrosAl ?>">
                    <input type="hidden" name="Cvioal2" value="<?php echo $Cvioal ?>">              <input type="hidden" name="chClaInfo2" value="<?php echo $chClaInfo ?>">
                    <input type="hidden" name="chClaIng2" value="<?php echo $chClaIng ?>">          <input type="hidden" name="chGastro2" value="<?php echo $chGastro ?>">
                    <input type="hidden" name="chConFami2" value="<?php echo $chConFami ?>">        <input type="hidden" name="chConCrePer2" value="<?php echo $chConCrePer ?>">
                    <input type="hidden" name="Cvihbb2" value="<?php echo $Cvihbb ?>">              <input type="hidden" name="Otrpar2" value="<?php echo $Otrpar ?>">
                    <input type="hidden" name="Otrlocker2" value="<?php echo $Otrlocker ?>">        <input type="hidden" name="Otractrec2" value="<?php echo $Otractrec ?>">
                    <input type="hidden" name="Otractrechor2" value="<?php echo $Otractrechor ?>">  <input type="hidden" name="Otractcul2" value="<?php echo $Otractcul ?>">
                    <input type="hidden" name="Otractculhor2" value="<?php echo $Otractculhor ?>">  <input type="hidden" name="chAudit2" value="<?php echo $chAudit ?>">
                    <input type="hidden" name="chComite2" value="<?php echo $chComite ?>">          <input type="hidden" name="chBrigada2" value="<?php echo $chBrigada ?>">
                    <input type="hidden" name="chOtroRol2" value="<?php echo $chOtroRol ?>">        <input type="hidden" name="otrRol2" value="<?php echo $Otrroles ?>">
                    <input type="hidden" name="contEmer2" value="<?php echo $contEmer ?>">          <input type="hidden" name="usuRaza2" value="<?php echo $usuRaza ?>">

                    <input type="hidden" name="telEmer" value="<?php echo $telEmer ?>">             <input type="hidden" name="usuGasto" value="<?php echo $usuGasto ?>">
                    <input type="hidden" name="usuSitua" value="<?php echo $usuSitua ?>">           <input type="hidden" name="posFam" value="<?php echo $posFam ?>">
                    <input type="hidden" name="usuCuHi" value="<?php echo $usuCuHi ?>">             <input type="hidden" name="subViv" value="<?php echo $subViv ?>">
                    <input type="hidden" name="ahoViv" value="<?php echo $ahoViv ?>">               <input type="hidden" name="montoHa" value="<?php echo $montoHa ?>">
                    <input type="hidden" name="Usfariesvi" value="<?php echo $Usfariesvi ?>">       <input type="hidden" name="UsuMejoVi" value="<?php echo $UsuMejoVi ?>">
                    <input type="hidden" name="UsProFi" value="<?php echo $UsProFi ?>">             <input type="hidden" name="UsMoCre" value="<?php echo $UsMoCre ?>">
                    <input type="hidden" name="UsEpAcu" value="<?php echo $UsEpAcu ?>">             <input type="hidden" name="UsLcInt" value="<?php echo $UsLcInt ?>">
                    <input type="hidden" name="UsIaAho" value="<?php echo $UsIaAho ?>">             <input type="hidden" name="UsNefor" value="<?php echo $UsNefor ?>">
                    <input type="hidden" name="UsHobie" value="<?php echo $UsHobie ?>">             <input type="hidden" name="UsQpTie" value="<?php echo $UsQpTie ?>">
                    <input type="hidden" name="UsHhTli" value="<?php echo $UsHhTli ?>">             <input type="hidden" name="UsBuTli" value="<?php echo $UsBuTli ?>">
                    <button type="submit" class="btn btn-default btn-lg">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true" style="width: 50px;"></span> Guardar
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    if($funcion == 'modEstActual')
    {
        //QUERY PARA SELECCIONAR LOS ESTUDIOS ACTUALES DEL USUARIO:
        $queryEstUser = "select * from".' '."$tbInfoEmpleado"."_000016 WHERE id = '$idEstud'";
        $commitQryEstu = mysql_query($queryEstUser, $conex) or die (mysql_errno()." - en el query: ".$queryEstUser." - ".mysql_error());
        $datoEstudio = mysql_fetch_array($commitQryEstu);
        $qEstu = $datoEstudio['Nesdes'];    $durEstu = $datoEstudio['Nesdur'];  $instEstu = $datoEstudio['Nesins'];
        $nivEstu = $datoEstudio['Nesniv'];  $horEstu = $datoEstudio['Neshor'];
        ?>
        <h4 class="labelTitulo">ESTUDIO ACTUAL - MODIFICAR ESTUDIO</h4>
        <div id="divDatEst" class="input-group">
            <form method="post" action="carEmp_Ops.php">
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="queEstud">QUÉ ESTUDIA:</label></span>
                    <input type="text" id="queEstud" name="queEstud" class="form-control form-sm inpEst" style="width: 530px"
                           value="<?php if($qEstu != null){echo $qEstu;} ?>">
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="durEstud">DURACION:</label></span>
                    <input type="text" id="durEstud" name="durEstud" class="form-control form-sm inpEst" style="width: 300px"
                           value="<?php if($durEstu != null){echo $durEstu;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="intEstud">INSTITUCION EDUCATIVA:</label></span>
                    <input type="text" id="intEstud" name="intEstud" class="form-control form-sm" style="width: 530px"
                           value="<?php if($instEstu != null){echo $instEstu;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="nivEstud">NIVEL ACTUAL:</label></span>
                    <input type="text" id="nivEstud" name="nivEstud" class="form-control form-sm inpEst" style="width: 300px"
                           value="<?php if($nivEstu != null){echo $nivEstu;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="horEstud">HORARIO:</label></span>
                    <input type="text" id="horEstud" name="horEstud" class="form-control form-sm inpEst" style="width: 300px"
                           value="<?php if($horEstu != null){echo $horEstu;} ?>" >
                </div>

                <div id="divAddEst2" align="center" style="margin-top: 10px">
                    <input type="hidden" id="funcion" name="funcion" value="updateEstActual">
                    <input type="hidden" id="tablaUser" name="tablaUser" value="<?php echo $tbInfoEmpleado?>">  <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $usuarioMtx ?>">
                    <input type="hidden" name="idEstudAct" value="<?php echo $idEstud ?>">
                    <input type="hidden" name="Idefnc2" value="<?php echo $Idefnc ?>">              <input type="hidden" name="Idegen2" value="<?php echo $Idegen ?>">
                    <input type="hidden" name="Ideced2" value="<?php echo $Ideced ?>">              <input type="hidden" name="Idepas2" value="<?php echo $Idepas ?>">
                    <input type="hidden" name="Idevis2" value="<?php echo $Idevis ?>">              <input type="hidden" name="estcivUser2" value="<?php echo $estcivUser ?>">
                    <input type="hidden" name="Idedir2" value="<?php echo $Idedir ?>">              <input type="hidden" name="lunaUser2" value="<?php echo $lunaUser ?>">
                    <input type="hidden" name="estUser2" value="<?php echo $estUser ?>">            <input type="hidden" name="munUser2" value="<?php echo $munUser ?>">
                    <input type="hidden" name="barUser2" value="<?php echo $barUser ?>">            <input type="hidden" name="telUser2" value="<?php echo $telUser ?>">
                    <input type="hidden" name="celUser2" value="<?php echo $celUser ?>">            <input type="hidden" name="corUser2" value="<?php echo $corUser ?>">
                    <input type="hidden" name="tisanUser2" value="<?php echo $tisanUser ?>">        <input type="hidden" name="extUser2" value="<?php echo $extUser ?>">
                    <input type="hidden" name="Cviviv2" value="<?php echo $Cviviv ?>">              <input type="hidden" name="Cvitvi2" value="<?php echo $Cvitvi ?>">
                    <input type="hidden" name="Cvitrz2" value="<?php echo $Cvitrz ?>">              <input type="hidden" name="Cvilot2" value="<?php echo $Cvilot ?>">
                    <input type="hidden" name="Cvisvi2" value="<?php echo $Cvisvi ?>">              <input type="hidden" name="chAcue2" value="<?php echo $chAcue ?>">
                    <input type="hidden" name="chAlca2" value="<?php echo $chAlca ?>">              <input type="hidden" name="chAseo2" value="<?php echo $chAseo ?>">
                    <input type="hidden" name="chEner2" value="<?php echo $chEner ?>">              <input type="hidden" name="chInter2" value="<?php echo $chInter ?>">
                    <input type="hidden" name="chGas2" value="<?php echo $chGas ?>">                <input type="hidden" name="chTele2" value="<?php echo $chTele ?>">
                    <input type="hidden" name="credito2" value="<?php echo $Cvicre ?>">             <input type="hidden" name="chBici2" value="<?php echo $chBici ?>">
                    <input type="hidden" name="chBus2" value="<?php echo $chBus ?>">                <input type="hidden" name="chCamina2" value="<?php echo $chCamina ?>">
                    <input type="hidden" name="chPart2" value="<?php echo $chPart ?>">              <input type="hidden" name="chMetro2" value="<?php echo $chMetro ?>">
                    <input type="hidden" name="chMoto2" value="<?php echo $chMoto ?>">              <input type="hidden" name="chOtroT2" value="<?php echo $chOtroT ?>">
                    <input type="hidden" name="chTaxi2" value="<?php echo $chTaxi ?>">              <input type="hidden" name="chContra2" value="<?php echo $chContra ?>">
                    <input type="hidden" name="otroTrans2" value="<?php echo $Cviotr ?>">           <input type="hidden" name="chTrae2" value="<?php echo $chTrae ?>">
                    <input type="hidden" name="chComBoca2" value="<?php echo $chComBoca ?>">        <input type="hidden" name="chComOtros2" value="<?php echo $chComOtros ?>">
                    <input type="hidden" name="chCasa2" value="<?php echo $chCasa ?>">              <input type="hidden" name="chOtrosAl2" value="<?php echo $chOtrosAl ?>">
                    <input type="hidden" name="Cvioal2" value="<?php echo $Cvioal ?>">              <input type="hidden" name="chClaInfo2" value="<?php echo $chClaInfo ?>">
                    <input type="hidden" name="chClaIng2" value="<?php echo $chClaIng ?>">          <input type="hidden" name="chGastro2" value="<?php echo $chGastro ?>">
                    <input type="hidden" name="chConFami2" value="<?php echo $chConFami ?>">        <input type="hidden" name="chConCrePer2" value="<?php echo $chConCrePer ?>">
                    <input type="hidden" name="Cvihbb2" value="<?php echo $Cvihbb ?>">              <input type="hidden" name="Otrpar2" value="<?php echo $Otrpar ?>">
                    <input type="hidden" name="Otrlocker2" value="<?php echo $Otrlocker ?>">        <input type="hidden" name="Otractrec2" value="<?php echo $Otractrec ?>">
                    <input type="hidden" name="Otractrechor2" value="<?php echo $Otractrechor ?>">  <input type="hidden" name="Otractcul2" value="<?php echo $Otractcul ?>">
                    <input type="hidden" name="Otractculhor2" value="<?php echo $Otractculhor ?>">  <input type="hidden" name="chAudit2" value="<?php echo $chAudit ?>">
                    <input type="hidden" name="chComite2" value="<?php echo $chComite ?>">          <input type="hidden" name="chBrigada2" value="<?php echo $chBrigada ?>">
                    <input type="hidden" name="chOtroRol2" value="<?php echo $chOtroRol ?>">        <input type="hidden" name="otrRol2" value="<?php echo $Otrroles ?>">
                    <input type="hidden" name="contEmer2" value="<?php echo $contEmer ?>">          <input type="hidden" name="usuRaza2" value="<?php echo $usuRaza ?>">

                    <input type="hidden" name="telEmer" value="<?php echo $telEmer ?>">             <input type="hidden" name="usuGasto" value="<?php echo $usuGasto ?>">
                    <input type="hidden" name="usuSitua" value="<?php echo $usuSitua ?>">           <input type="hidden" name="posFam" value="<?php echo $posFam ?>">
                    <input type="hidden" name="usuCuHi" value="<?php echo $usuCuHi ?>">             <input type="hidden" name="subViv" value="<?php echo $subViv ?>">
                    <input type="hidden" name="ahoViv" value="<?php echo $ahoViv ?>">               <input type="hidden" name="montoHa" value="<?php echo $montoHa ?>">
                    <input type="hidden" name="Usfariesvi" value="<?php echo $Usfariesvi ?>">       <input type="hidden" name="UsuMejoVi" value="<?php echo $UsuMejoVi ?>">
                    <input type="hidden" name="UsProFi" value="<?php echo $UsProFi ?>">             <input type="hidden" name="UsMoCre" value="<?php echo $UsMoCre ?>">
                    <input type="hidden" name="UsEpAcu" value="<?php echo $UsEpAcu ?>">             <input type="hidden" name="UsLcInt" value="<?php echo $UsLcInt ?>">
                    <input type="hidden" name="UsIaAho" value="<?php echo $UsIaAho ?>">             <input type="hidden" name="UsNefor" value="<?php echo $UsNefor ?>">
                    <input type="hidden" name="UsHobie" value="<?php echo $UsHobie ?>">             <input type="hidden" name="UsQpTie" value="<?php echo $UsQpTie ?>">
                    <input type="hidden" name="UsHhTli" value="<?php echo $UsHhTli ?>">             <input type="hidden" name="UsBuTli" value="<?php echo $UsBuTli ?>">
                    <button type="submit" class="btn btn-default btn-lg">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true" style="width: 50px;"></span> Guardar
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    if($funcion == 'insertEstActual')
    {
        if($queEstud != null and $durEstud != null and $intEstud != null and $nivEstud != null and $horEstud != null)
        {
            $queryInsEstAc = "insert into".' '."$tablaUser"."_000016
                          values('talhuma','$fechaActual','$horaActual','$queEstud','$durEstud','$intEstud','$nivEstud','$horEstud','$userMtx','on','C-$wuse','')";
            $comQryInsEstAc = mysql_query($queryInsEstAc, $conex) or die (mysql_errno()." - en el query: ".$queryInsEstAc." - ".mysql_error());

            //obtenerOpenTab($conex,$wuse,$fechaActual,2);

            if($comQryInsEstAc)
            {
                ?>
                <div id="divAddEst2" align="center" style="margin-top: 80px">
                    <input type="hidden" id="funcion" name="funcion" value="insertEstActual">
                    <button type="submit" class="btn btn-default btn-lg"
                            onclick="window.opener.location.reload(); window.close();">
                        <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Guardado
                    </button>
                </div>
                <?php
            }
            else
            {
                ?>
                <div id="divAddEst2" align="center">
                    <h3>No se pudo guardar el registro</h3><h3>por favor verifique</h3>
                </div>

                <form method="post" action="carEmp_Ops.php">
                    <input type="hidden" id="queEstud" name="queEstud" value="<?php echo $queEstud ?>">
                    <input type="hidden" id="durEstud" name="durEstud" value="<?php echo $durEstud ?>">
                    <input type="hidden" id="intEstud" name="intEstud" value="<?php echo $intEstud ?>">
                    <input type="hidden" id="nivEstud" name="nivEstud" value="<?php echo $nivEstud ?>">
                    <input type="hidden" id="horEstud" name="horEstud" value="<?php echo $horEstud ?>">
                    <input type="hidden" id="userMatrix" name="userMatrix" value="<?php echo $userMtx ?>">
                    <input type="hidden" id="funcion" name="funcion" value="addEstActual">

                    <div id="divAddEst2" align="center">
                        <button type="submit" class="btn btn-warning btn-lg" onclick="openOps(funcion)">
                            <span class="glyphicon glyphicon-warning-sign" aria-hidden="true" style="width: 50px;"></span> Aceptar
                        </button>
                    </div>
                </form>
                <?php
            }

            //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:

            $query1 = "select id from".' '."$tablaUser"."_000013 where Ideuse = '$userMtx'";
            $commit1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
            $datoEmpl = mysql_fetch_assoc($commit1);
            $idEmpl = $datoEmpl['id'];

            if($idEmpl == null)
            {
                $queryIns1 = "insert into".' '."$tablaUser"."_000013
                          values('talhuma','$fechaActual','$horaActual','$Idepas','$Idevis','','$estcivUser','$lunaUser','$munUser','$barUser',
                                 '$estUser','$tisanUser','',
                                 'on','C-$wuse','')";
                $comQryIns1 = mysql_query($queryIns1, $conex) or die (mysql_errno()." - en el query: ".$queryIns1." - ".mysql_error());
            }
            else
            {
                //OBTENER CODIGO DEL MUNICIPIO:
                $QueryMuni = "select Codigo from root_000006 WHERE Nombre LIKE '$munUser2'";
                $commMuni = mysql_query($QueryMuni, $conex) or die (mysql_errno()." - en el query: ".$QueryMuni." - ".mysql_error());
                $datoMuni = mysql_fetch_assoc($commMuni);
                $codMuni2 = $datoMuni['Codigo'];

                //OBTENER CODIGO DEL BARRIO:
                $QueryBarr = "select Barcod from root_000034 WHERE Bardes LIKE '$barUser2' AND Barmun = '$codMuni2'";
                $commBarr = mysql_query($QueryBarr, $conex) or die (mysql_errno()." - en el query: ".$QueryBarr." - ".mysql_error());
                $datoBarr = mysql_fetch_assoc($commBarr);
                $codBarr2 = $datoBarr['Barcod'];

                //OBTENER ESTADO CIVIL:
                $QueryEstCiv = "select Scvcod from root_000065 WHERE Scvdes LIKE '$estcivUser2'";
                $commEstCiv = mysql_query($QueryEstCiv, $conex) or die (mysql_errno()." - en el query: ".$QueryBarr." - ".mysql_error());
                $datoEstCiv = mysql_fetch_assoc($commEstCiv);
                $codEstCiv2 = $datoEstCiv['Scvcod'];

                if($Idegen2 == 'Masculino'){$Idegen2 = 'M';} else{$Idegen2 = 'F';}

                //INFORMACION GENERAL
                $queryUpd1 = "update".' '."$tablaUser"."_000013 set Fecha_data = '$fechaActual',Hora_data = '$horaActual',Idepas = '$Idepas2',
                            Idevis = '$Idevis2',Idepvi = '',Ideesc = '$codEstCiv2',Ideinc = '$lunaUser2',Idempo = '$codMuni2',Idebrr = '$codBarr2',
                            Idestt = '$estUser2',Idesrh = '$tisanUser2',Idefnc = '$Idefnc2',Idegen = '$Idegen2',Idedir = '$Idedir2',Idetel = '$telUser2',
                            Idecel = '$celUser2',Ideeml = '$corUser2',Ideext = '$extUser2'
                          where Ideuse = '$userMtx'";
                $comUpd1 = mysql_query($queryUpd1, $conex) or die (mysql_errno()." - en el query: ".$queryUpd1." - ".mysql_error());
            }

            //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
            $query06 = "select id from talento_000006 where Usucod = '$wuse'";
            $commit06 = mysql_query($query06, $conex) or die (mysql_errno()." - en el query: ".$query06." - ".mysql_error());
            $datoEmp06 = mysql_fetch_assoc($commit06);
            $idEmp06 = $datoEmp06['id'];

            if($idEmp06 == null)
            {
                $queryIns6 = "insert into talento_000006 VALUES('talento','$fechaActual','$horaActual','$wuse','$contEmer','$usuRaza','C-$wuse','')";
                $comIns6 = mysql_query($queryIns6, $conex) or die (mysql_errno()." - en el query: ".$queryIns6." - ".mysql_error());
            }
            else
            {
                $queryUpd06 = "update talento_000006 set Fecha_data = '$fechaActual',Hora_data = '$horaActual',
                                  Usuemer = '$contEmer',Usuraza = '$usuRaza'
                           WHERE Usucod = '$wuse'";
                $comUpd06 = mysql_query($queryUpd06, $conex) or die (mysql_errno()." - en el query: ".$queryUpd06." - ".mysql_error());
            }

            obtenerOpenTab($conex,$wuse,$fechaActual,2);

        }
        else
        {
            ?>
            <div id="divAddEst2" align="center">
                <h3>Todos los campos son obligatorios</h3><h3>por favor verifique</h3>
            </div>

            <form method="post" action="carEmp_Ops.php">
                <input type="hidden" id="queEstud" name="queEstud" value="<?php echo $queEstud ?>">
                <input type="hidden" id="durEstud" name="durEstud" value="<?php echo $durEstud ?>">
                <input type="hidden" id="intEstud" name="intEstud" value="<?php echo $intEstud ?>">
                <input type="hidden" id="nivEstud" name="nivEstud" value="<?php echo $nivEstud ?>">
                <input type="hidden" id="horEstud" name="horEstud" value="<?php echo $horEstud ?>">
                <input type="hidden" id="userMatrix" name="userMatrix" value="<?php echo $userMtx ?>">
                <input type="hidden" id="funcion" name="funcion" value="addEstActual">

                <div id="divAddEst2" align="center">
                    <button type="submit" class="btn btn-warning btn-lg" onclick="openOps(funcion)">
                        <span class="glyphicon glyphicon-warning-sign" aria-hidden="true" style="width: 50px;"></span> Aceptar
                    </button>
                </div>
            </form>
            <?php
        }
    }

    if($funcion == 'supEstActual')
    {
        $queryDelEstAc = "delete from ".' '."$tablaUser"."_000016 where id = '$idEstud'";
        $comQryDelEstAc = mysql_query($queryDelEstAc, $conex) or die (mysql_errno()." - en el query: ".$queryDelEstAc." - ".mysql_error());

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_24(Condiciones de vida y vivienda):
        saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
            $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
            $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
            $Cvioal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_60(Respuesta Otras Preguntas Caracterizacion):
        saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
            $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
            $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
            $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
            $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:
        saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,$tisanUser,
            $wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$cedula);

        //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
        saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
            $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
            $UsBuTli);

        obtenerOpenTab($conex,$wuse,$fechaActual,2);

        ?>
        <div id="divAddEst2" align="center" style="margin-top: 80px">
            <button type="submit" class="btn btn-default btn-lg" onclick="window.opener.location.reload(); window.close()">
                <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Eliminado
            </button>
        </div>
        <?php
    }

    if($funcion == 'updateEstActual')
    {
        $queryDelEstAc = "update".' '."$tablaUser"."_000016
                            set Nesdes = '$queEstud', Nesdur = '$durEstud', Nesins = '$intEstud', Nesniv = '$nivEstud',
                                Neshor = '$horEstud'
                          where id = '$idEstudAct'";
        $comQryDelEstAc = mysql_query($queryDelEstAc, $conex) or die (mysql_errno()." - en el query: ".$queryDelEstAc." - ".mysql_error());

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_24(Condiciones de vida y vivienda):
        saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
            $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
            $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
            $Cvioal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_60(Respuesta Otras Preguntas Caracterizacion):
        saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
            $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
            $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
            $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
            $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:
        saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,$tisanUser,
            $wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$cedula);

        //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
        saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
            $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
            $UsBuTli);

        obtenerOpenTab($conex,$wuse,$fechaActual,2);

        ?>
        <div id="divAddEst2" align="center" style="margin-top: 80px">
            <button type="submit" class="btn btn-default btn-lg" onclick="window.opener.location.reload(); window.close()">
                <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Actualizado
            </button>
        </div>
        <?php
    }

    if($funcion == 'addCredito')
    {
        ?>
        <h4 class="labelTitulo">ADICIONAR CREDITO</h4>
        <div id="divDatEst" class="input-group">
            <form method="post" action="carEmp_Ops.php">
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="cremot">MOTIVO:</label></span>
                    <input type="text" id="cremot" name="cremot" class="form-control form-sm inpEst" style="width: 530px"
                           value="<?php if($cremot != null){echo $cremot;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="creent">ENTIDAD:</label></span>
                    <input type="text" id="creent" name="creent" class="form-control form-sm inpEst" style="width: 530px"
                           value="<?php if($creent != null){echo $creent;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="creval">VALOR TOTAL CREDITO:</label></span>
                    <input type="text" id="creval" name="creval" class="form-control form-sm" style="width: 200px"
                           value="<?php if($creval != null){echo $creval;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="crecuo">CUOTA MENSUAL:</label></span>
                    <input type="text" id="crecuo" name="crecuo" class="form-control form-sm inpEst" style="width: 200px"
                           value="<?php if($crecuo != null){echo $crecuo;} ?>" >
                </div>

                <div id="divAddEst2" align="center" style="margin-top: 20px">
                    <input type="hidden" id="funcion" name="funcion" value="insertCredito">
                    <input type="hidden" id="tablaUser" name="tablaUser" value="<?php echo $tbInfoEmpleado?>">  <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $usuarioMtx ?>">
                    <input type="hidden" name="Idefnc2" value="<?php echo $Idefnc ?>">              <input type="hidden" name="Idegen2" value="<?php echo $Idegen ?>">
                    <input type="hidden" name="Ideced2" value="<?php echo $Ideced ?>">              <input type="hidden" name="Idepas2" value="<?php echo $Idepas ?>">
                    <input type="hidden" name="Idevis2" value="<?php echo $Idevis ?>">              <input type="hidden" name="estcivUser2" value="<?php echo $estcivUser ?>">
                    <input type="hidden" name="Idedir2" value="<?php echo $Idedir ?>">              <input type="hidden" name="lunaUser2" value="<?php echo $lunaUser ?>">
                    <input type="hidden" name="estUser2" value="<?php echo $estUser ?>">            <input type="hidden" name="munUser2" value="<?php echo $munUser ?>">
                    <input type="hidden" name="barUser2" value="<?php echo $barUser ?>">            <input type="hidden" name="telUser2" value="<?php echo $telUser ?>">
                    <input type="hidden" name="celUser2" value="<?php echo $celUser ?>">            <input type="hidden" name="corUser2" value="<?php echo $corUser ?>">
                    <input type="hidden" name="tisanUser2" value="<?php echo $tisanUser ?>">        <input type="hidden" name="extUser2" value="<?php echo $extUser ?>">
                    <input type="hidden" name="Cviviv2" value="<?php echo $Cviviv ?>">              <input type="hidden" name="Cvitvi2" value="<?php echo $Cvitvi ?>">
                    <input type="hidden" name="Cvitrz2" value="<?php echo $Cvitrz ?>">              <input type="hidden" name="Cvilot2" value="<?php echo $Cvilot ?>">
                    <input type="hidden" name="Cvisvi2" value="<?php echo $Cvisvi ?>">              <input type="hidden" name="chAcue2" value="<?php echo $chAcue ?>">
                    <input type="hidden" name="chAlca2" value="<?php echo $chAlca ?>">              <input type="hidden" name="chAseo2" value="<?php echo $chAseo ?>">
                    <input type="hidden" name="chEner2" value="<?php echo $chEner ?>">              <input type="hidden" name="chInter2" value="<?php echo $chInter ?>">
                    <input type="hidden" name="chGas2" value="<?php echo $chGas ?>">                <input type="hidden" name="chTele2" value="<?php echo $chTele ?>">
                    <input type="hidden" name="credito2" value="<?php echo $Cvicre ?>">             <input type="hidden" name="chBici2" value="<?php echo $chBici ?>">
                    <input type="hidden" name="chBus2" value="<?php echo $chBus ?>">                <input type="hidden" name="chCamina2" value="<?php echo $chCamina ?>">
                    <input type="hidden" name="chPart2" value="<?php echo $chPart ?>">              <input type="hidden" name="chMetro2" value="<?php echo $chMetro ?>">
                    <input type="hidden" name="chMoto2" value="<?php echo $chMoto ?>">              <input type="hidden" name="chOtroT2" value="<?php echo $chOtroT ?>">
                    <input type="hidden" name="chTaxi2" value="<?php echo $chTaxi ?>">              <input type="hidden" name="chContra2" value="<?php echo $chContra ?>">
                    <input type="hidden" name="otroTrans2" value="<?php echo $Cviotr ?>">           <input type="hidden" name="chTrae2" value="<?php echo $chTrae ?>">
                    <input type="hidden" name="chComBoca2" value="<?php echo $chComBoca ?>">        <input type="hidden" name="chComOtros2" value="<?php echo $chComOtros ?>">
                    <input type="hidden" name="chCasa2" value="<?php echo $chCasa ?>">              <input type="hidden" name="chOtrosAl2" value="<?php echo $chOtrosAl ?>">
                    <input type="hidden" name="Cvioal2" value="<?php echo $Cvioal ?>">              <input type="hidden" name="chClaInfo2" value="<?php echo $chClaInfo ?>">
                    <input type="hidden" name="chClaIng2" value="<?php echo $chClaIng ?>">          <input type="hidden" name="chGastro2" value="<?php echo $chGastro ?>">
                    <input type="hidden" name="chConFami2" value="<?php echo $chConFami ?>">        <input type="hidden" name="chConCrePer2" value="<?php echo $chConCrePer ?>">
                    <input type="hidden" name="Cvihbb2" value="<?php echo $Cvihbb ?>">              <input type="hidden" name="Otrpar2" value="<?php echo $Otrpar ?>">
                    <input type="hidden" name="Otrlocker2" value="<?php echo $Otrlocker ?>">        <input type="hidden" name="Otractrec2" value="<?php echo $Otractrec ?>">
                    <input type="hidden" name="Otractrechor2" value="<?php echo $Otractrechor ?>">  <input type="hidden" name="Otractcul2" value="<?php echo $Otractcul ?>">
                    <input type="hidden" name="Otractculhor2" value="<?php echo $Otractculhor ?>">  <input type="hidden" name="chAudit2" value="<?php echo $chAudit ?>">
                    <input type="hidden" name="chComite2" value="<?php echo $chComite ?>">          <input type="hidden" name="chBrigada2" value="<?php echo $chBrigada ?>">
                    <input type="hidden" name="chOtroRol2" value="<?php echo $chOtroRol ?>">        <input type="hidden" name="otrRol2" value="<?php echo $Otrroles ?>">
                    <input type="hidden" name="contEmer2" value="<?php echo $contEmer ?>">          <input type="hidden" name="usuRaza2" value="<?php echo $usuRaza ?>">

                    <input type="hidden" name="telEmer" value="<?php echo $telEmer ?>">             <input type="hidden" name="usuGasto" value="<?php echo $usuGasto ?>">
                    <input type="hidden" name="usuSitua" value="<?php echo $usuSitua ?>">           <input type="hidden" name="posFam" value="<?php echo $posFam ?>">
                    <input type="hidden" name="usuCuHi" value="<?php echo $usuCuHi ?>">             <input type="hidden" name="subViv" value="<?php echo $subViv ?>">
                    <input type="hidden" name="ahoViv" value="<?php echo $ahoViv ?>">               <input type="hidden" name="montoHa" value="<?php echo $montoHa ?>">
                    <input type="hidden" name="Usfariesvi" value="<?php echo $Usfariesvi ?>">       <input type="hidden" name="UsuMejoVi" value="<?php echo $UsuMejoVi ?>">
                    <input type="hidden" name="UsProFi" value="<?php echo $UsProFi ?>">             <input type="hidden" name="UsMoCre" value="<?php echo $UsMoCre ?>">
                    <input type="hidden" name="UsEpAcu" value="<?php echo $UsEpAcu ?>">             <input type="hidden" name="UsLcInt" value="<?php echo $UsLcInt ?>">
                    <input type="hidden" name="UsIaAho" value="<?php echo $UsIaAho ?>">             <input type="hidden" name="UsNefor" value="<?php echo $UsNefor ?>">
                    <input type="hidden" name="UsHobie" value="<?php echo $UsHobie ?>">             <input type="hidden" name="UsQpTie" value="<?php echo $UsQpTie ?>">
                    <input type="hidden" name="UsHhTli" value="<?php echo $UsHhTli ?>">             <input type="hidden" name="UsBuTli" value="<?php echo $UsBuTli ?>">
                    <button type="submit" class="btn btn-default btn-lg">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true" style="width: 50px;"></span> Guardar
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    if($funcion == 'insertCredito')
    {
        if($cremot != null and $creent != null and $creval != null and $crecuo != null)
        {
            $queryInsCred = "insert into".' '."$tablaUser"."_000025
                          values('talhuma','$fechaActual','$horaActual','$cremot','$creent','$creval','$crecuo','$userMtx','on','C-$wuse','')";
            $comQryInsCred = mysql_query($queryInsCred, $conex) or die (mysql_errno()." - en el query: ".$queryInsCred." - ".mysql_error());

            //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_24(Condiciones de vida y vivienda):
            saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
                $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
                $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
                $Cvioal,$wuse);

            //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_60(Respuesta Otras Preguntas Caracterizacion):
            saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
                $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
                $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
                $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
                $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse);

            //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:
            saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,$tisanUser,
                $wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$cedula);

            //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
            saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
                $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
                $UsBuTli);

            obtenerOpenTab($conex,$wuse,$fechaActual,4);

            if($comQryInsCred)
            {
                ?>
                <div id="divAddEst2" align="center" style="margin-top: 80px">
                    <input type="hidden" id="funcion" name="funcion" value="insertCredito">
                    <button type="submit" class="btn btn-default btn-lg"
                            onclick="window.opener.location.reload(); window.close();">
                        <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Guardado
                    </button>
                </div>
                <?php
            }
            else
            {
                ?>
                <div id="divAddEst2" align="center">
                    <h3>No se pudo guardar el registro</h3><h3>por favor verifique</h3>
                </div>

                <form method="post" action="carEmp_Ops.php">
                    <input type="hidden" id="cremot" name="cremot" value="<?php echo $cremot ?>">
                    <input type="hidden" id="creent" name="creent" value="<?php echo $creent ?>">
                    <input type="hidden" id="creval" name="creval" value="<?php echo $creval ?>">
                    <input type="hidden" id="crecuo" name="crecuo" value="<?php echo $crecuo ?>">
                    <input type="hidden" id="userMatrix" name="userMatrix" value="<?php echo $userMtx ?>">
                    <input type="hidden" id="funcion" name="funcion" value="addCredito">

                    <div id="divAddEst2" align="center">
                        <button type="submit" class="btn btn-warning btn-lg" onclick="openOps(funcion)">
                            <span class="glyphicon glyphicon-warning-sign" aria-hidden="true" style="width: 50px;"></span> Aceptar
                        </button>
                    </div>
                </form>
                <?php
            }

        }
        else
        {
            ?>
            <div id="divAddEst2" align="center">
                <h3>Todos los campos son obligatorios</h3><h3>por favor verifique</h3>
            </div>

            <form method="post" action="carEmp_Ops.php">
                <input type="hidden" id="cremot" name="cremot" value="<?php echo $cremot ?>">
                <input type="hidden" id="creent" name="creent" value="<?php echo $creent ?>">
                <input type="hidden" id="creval" name="creval" value="<?php echo $creval ?>">
                <input type="hidden" id="crecuo" name="crecuo" value="<?php echo $crecuo ?>">
                <input type="hidden" id="userMatrix" name="userMatrix" value="<?php echo $userMtx ?>">
                <input type="hidden" id="funcion" name="funcion" value="addCredito">

                <div id="divAddEst2" align="center">
                    <button type="submit" class="btn btn-warning btn-lg" onclick="openOps(funcion)">
                        <span class="glyphicon glyphicon-warning-sign" aria-hidden="true" style="width: 50px;"></span> Aceptar
                    </button>
                </div>
            </form>
            <?php
        }
    }

    if($funcion == 'addIntFam')
    {
        ?>
        <h4 class="labelTitulo" style="margin-bottom: 3px">INTEGRANTE FAMILIAR - ADICIONAR INTEGRANTE</h4>
        <div id="divDatEst" class="input-group">
            <form method="post" action="carEmp_Ops.php">
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="intfNom">NOMBRES:</label></span>
                    <input type="text" id="intfNom" name="intfNom" class="form-control form-sm inpEst" style="width: 530px"
                           value="<?php if($intfNom != null){echo $intfNom;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="intfApe">APELLIDOS:</label></span>
                    <input type="text" id="intfApe" name="intfApe" class="form-control form-sm inpEst" style="width: 530px"
                           value="<?php if($intfApe != null){echo $intfApe;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="intfGen">GENERO:</label></span>
                    <select id="intfGen" name="intfGen" class="form-control form-sm" style="width: 160px">
                        <option>Femenino</option>
                        <option>Masculino</option>
                        <?php
                        if($intfGen != null){?><option selected><?php echo $intfGen ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>

                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 10px"></span>

                    <span class="input-group-addon input-sm"><label for="intfPare">PARENTESCO:</label></span>
                    <select id="intfPare" name="intfPare" class="form-control form-sm inpEst" style="width: 150px">
                        <?php
                        $query = "select Parcod,Pardes from root_000067 WHERE Parest = 'on'";
                        $commit = mysql_query($query, $conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
                        while($dato = mysql_fetch_assoc($commit))
                        {
                            $codParentesco = $dato['Parcod'];   $parentesco = $dato['Pardes'];
                            ?><option><?php echo $codParentesco.'-'.$parentesco ?></option><?php
                        }
                        if($intfPare != null){?><option selected><?php echo $intfPare ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="intfFen">FECHA NACIMIENTO:</label></span>
                    <input type="date" id="intfFen" name="intfFen" class="form-control form-sm" style="width: 160px"
                           value="<?php if($intfFen != null){echo $intfFen;} ?>" >

                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 10px"></span>

                    <span class="input-group-addon input-sm"><label for="intfNie">NIVEL EDUCATIVO:</label></span>
                    <select id="intfNie" name="intfNie" class="form-control form-sm inpEst" style="width: 150px">
                        <?php
                        $queryEscolar = "select Scodes,Scocod from root_000066 WHERE Scoest = 'on' AND Scoley = 'off' ORDER BY Scodes ASC ";
                        $comQryEscolar = mysql_query($queryEscolar, $conex) or die (mysql_errno()." - en el query: ".$queryEscolar." - ".mysql_error());
                        while($datoEscolar = mysql_fetch_assoc($comQryEscolar))
                        {
                            $gradoEscolar = $datoEscolar['Scocod'].'-'.$datoEscolar['Scodes'];
                            ?><option><?php echo $gradoEscolar ?></option><?php
                        }
                        if($intfNie != null){?><option selected disabled><?php echo $intfNie ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="intfOcu">OCUPACION:</label></span>
                    <select id="intfOcu" name="intfOcu" class="form-control form-sm" style="width: 160px">
                        <?php
                        $query = "select Ocucod,Ocudes from root_000078 WHERE Ocuest = 'on'";
                        $commit = mysql_query($query, $conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
                        while($dato = mysql_fetch_assoc($commit))
                        {
                            $codOcu = $dato['Ocucod']; $ocupacion = $dato['Ocudes'];
                            ?><option><?php echo $codOcu.'-'.$ocupacion ?></option><?php
                        }
                        if($intfOcu != null){?><option selected disabled><?php echo $intfOcu ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>

                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 10px"></span>

                    <span class="input-group-addon input-sm"><label for="intfVius">VIVE CON USTED:</label></span>
                    <select id="intfVius" name="intfVius" class="form-control form-sm inpEst" style="width: 150px">
                        <option>SI</option>
                        <option>NO</option>
                        <?php
                        if($intfVius != null){?><option selected disabled><?php echo $intfVius ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                </div>

                <div id="divAddEst2" align="center" style="margin-top: 3px">
                    <input type="hidden" id="funcion" name="funcion" value="insertIntFam">
                    <input type="hidden" id="tablaUser" name="tablaUser" value="<?php echo $tbInfoEmpleado?>">  <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $usuarioMtx ?>">
                    <input type="hidden" name="Idefnc2" value="<?php echo $Idefnc ?>">              <input type="hidden" name="Idegen2" value="<?php echo $Idegen ?>">
                    <input type="hidden" name="Ideced2" value="<?php echo $Ideced ?>">              <input type="hidden" name="Idepas2" value="<?php echo $Idepas ?>">
                    <input type="hidden" name="Idevis2" value="<?php echo $Idevis ?>">              <input type="hidden" name="estcivUser2" value="<?php echo $estcivUser ?>">
                    <input type="hidden" name="Idedir2" value="<?php echo $Idedir ?>">              <input type="hidden" name="lunaUser2" value="<?php echo $lunaUser ?>">
                    <input type="hidden" name="estUser2" value="<?php echo $estUser ?>">            <input type="hidden" name="munUser2" value="<?php echo $munUser ?>">
                    <input type="hidden" name="barUser2" value="<?php echo $barUser ?>">            <input type="hidden" name="telUser2" value="<?php echo $telUser ?>">
                    <input type="hidden" name="celUser2" value="<?php echo $celUser ?>">            <input type="hidden" name="corUser2" value="<?php echo $corUser ?>">
                    <input type="hidden" name="tisanUser2" value="<?php echo $tisanUser ?>">        <input type="hidden" name="extUser2" value="<?php echo $extUser ?>">
                    <input type="hidden" name="Cviviv2" value="<?php echo $Cviviv ?>">              <input type="hidden" name="Cvitvi2" value="<?php echo $Cvitvi ?>">
                    <input type="hidden" name="Cvitrz2" value="<?php echo $Cvitrz ?>">              <input type="hidden" name="Cvilot2" value="<?php echo $Cvilot ?>">
                    <input type="hidden" name="Cvisvi2" value="<?php echo $Cvisvi ?>">              <input type="hidden" name="chAcue2" value="<?php echo $chAcue ?>">
                    <input type="hidden" name="chAlca2" value="<?php echo $chAlca ?>">              <input type="hidden" name="chAseo2" value="<?php echo $chAseo ?>">
                    <input type="hidden" name="chEner2" value="<?php echo $chEner ?>">              <input type="hidden" name="chInter2" value="<?php echo $chInter ?>">
                    <input type="hidden" name="chGas2" value="<?php echo $chGas ?>">                <input type="hidden" name="chTele2" value="<?php echo $chTele ?>">
                    <input type="hidden" name="credito2" value="<?php echo $Cvicre ?>">             <input type="hidden" name="chBici2" value="<?php echo $chBici ?>">
                    <input type="hidden" name="chBus2" value="<?php echo $chBus ?>">                <input type="hidden" name="chCamina2" value="<?php echo $chCamina ?>">
                    <input type="hidden" name="chPart2" value="<?php echo $chPart ?>">              <input type="hidden" name="chMetro2" value="<?php echo $chMetro ?>">
                    <input type="hidden" name="chMoto2" value="<?php echo $chMoto ?>">              <input type="hidden" name="chOtroT2" value="<?php echo $chOtroT ?>">
                    <input type="hidden" name="chTaxi2" value="<?php echo $chTaxi ?>">              <input type="hidden" name="chContra2" value="<?php echo $chContra ?>">
                    <input type="hidden" name="otroTrans2" value="<?php echo $Cviotr ?>">           <input type="hidden" name="chTrae2" value="<?php echo $chTrae ?>">
                    <input type="hidden" name="chComBoca2" value="<?php echo $chComBoca ?>">        <input type="hidden" name="chComOtros2" value="<?php echo $chComOtros ?>">
                    <input type="hidden" name="chCasa2" value="<?php echo $chCasa ?>">              <input type="hidden" name="chOtrosAl2" value="<?php echo $chOtrosAl ?>">
                    <input type="hidden" name="Cvioal2" value="<?php echo $Cvioal ?>">              <input type="hidden" name="chClaInfo2" value="<?php echo $chClaInfo ?>">
                    <input type="hidden" name="chClaIng2" value="<?php echo $chClaIng ?>">          <input type="hidden" name="chGastro2" value="<?php echo $chGastro ?>">
                    <input type="hidden" name="chConFami2" value="<?php echo $chConFami ?>">        <input type="hidden" name="chConCrePer2" value="<?php echo $chConCrePer ?>">
                    <input type="hidden" name="Cvihbb2" value="<?php echo $Cvihbb ?>">              <input type="hidden" name="Otrpar2" value="<?php echo $Otrpar ?>">
                    <input type="hidden" name="Otrlocker2" value="<?php echo $Otrlocker ?>">        <input type="hidden" name="Otractrec2" value="<?php echo $Otractrec ?>">
                    <input type="hidden" name="Otractrechor2" value="<?php echo $Otractrechor ?>">  <input type="hidden" name="Otractcul2" value="<?php echo $Otractcul ?>">
                    <input type="hidden" name="Otractculhor2" value="<?php echo $Otractculhor ?>">  <input type="hidden" name="chAudit2" value="<?php echo $chAudit ?>">
                    <input type="hidden" name="chComite2" value="<?php echo $chComite ?>">          <input type="hidden" name="chBrigada2" value="<?php echo $chBrigada ?>">
                    <input type="hidden" name="chOtroRol2" value="<?php echo $chOtroRol ?>">        <input type="hidden" name="otrRol2" value="<?php echo $Otrroles ?>">
                    <input type="hidden" name="contEmer2" value="<?php echo $contEmer ?>">          <input type="hidden" name="usuRaza2" value="<?php echo $usuRaza ?>">

                    <input type="hidden" name="telEmer" value="<?php echo $telEmer ?>">             <input type="hidden" name="usuGasto" value="<?php echo $usuGasto ?>">
                    <input type="hidden" name="usuSitua" value="<?php echo $usuSitua ?>">           <input type="hidden" name="posFam" value="<?php echo $posFam ?>">
                    <input type="hidden" name="usuCuHi" value="<?php echo $usuCuHi ?>">             <input type="hidden" name="subViv" value="<?php echo $subViv ?>">
                    <input type="hidden" name="ahoViv" value="<?php echo $ahoViv ?>">               <input type="hidden" name="montoHa" value="<?php echo $montoHa ?>">
                    <input type="hidden" name="Usfariesvi" value="<?php echo $Usfariesvi ?>">       <input type="hidden" name="UsuMejoVi" value="<?php echo $UsuMejoVi ?>">
                    <input type="hidden" name="UsProFi" value="<?php echo $UsProFi ?>">             <input type="hidden" name="UsMoCre" value="<?php echo $UsMoCre ?>">
                    <input type="hidden" name="UsEpAcu" value="<?php echo $UsEpAcu ?>">             <input type="hidden" name="UsLcInt" value="<?php echo $UsLcInt ?>">
                    <input type="hidden" name="UsIaAho" value="<?php echo $UsIaAho ?>">             <input type="hidden" name="UsNefor" value="<?php echo $UsNefor ?>">
                    <input type="hidden" name="UsHobie" value="<?php echo $UsHobie ?>">             <input type="hidden" name="UsQpTie" value="<?php echo $UsQpTie ?>">
                    <input type="hidden" name="UsHhTli" value="<?php echo $UsHhTli ?>">             <input type="hidden" name="UsBuTli" value="<?php echo $UsBuTli ?>">
                    <button type="submit" class="btn btn-default btn-lg">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true" style="width: 50px;"></span> Guardar
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    if($funcion == 'modIntFam')
    {
        //QUERY PARA SELECCIONAR LOS DATOS FAMILIARES DEL USUARIO:
        $queryGruFam = "select * from".' '."$tbInfoEmpleado"."_000021 WHERE id = '$idEstud'";
        $commitGruFam = mysql_query($queryGruFam, $conex) or die (mysql_errno()." - en el query: ".$queryGruFam." - ".mysql_error());
        $datoGruFam = mysql_fetch_array($commitGruFam);
        $grunom = $datoGruFam['Grunom'];    $gruape = $datoGruFam['Gruape'];    $grugen = $datoGruFam['Grugen'];
        $grupar = $datoGruFam['Grupar'];    $grufna = $datoGruFam['Grufna'];    $gruesc = $datoGruFam['Gruesc'];
        $gruocu = $datoGruFam['Gruocu'];    $grucom = $datoGruFam['Grucom'];    $gruart = $datoGruFam['Gruart'];
        $idGruFam = $datoGruFam['id'];
        //$grupar = obtenerParentesco($conex,$grupar);    //$gruesc = datoEscolar($gruesc,$conex);
        //$gruocu = obtenerOcupacion($gruocu,$conex);     //if($grucom == 'on'){$grucom = 'SI';} else{$grucom = 'NO';}
        //if($grugen == 'F'){$grugen = 'Femenino';}       else{$grugen = 'Masculino';}

        ?>
        <h4 class="labelTitulo" style="margin-bottom: 3px">INTEGRANTE FAMILIAR - MODIFICAR INTEGRANTE</h4>
        <div id="divDatEst" class="input-group">
            <form method="post" action="carEmp_Ops.php">
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="intfNom">NOMBRES:</label></span>
                    <input type="text" id="intfNom" name="intfNom" class="form-control form-sm inpEst" style="width: 530px"
                           value="<?php if($grunom != null){echo $grunom;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="intfApe">APELLIDOS:</label></span>
                    <input type="text" id="intfApe" name="intfApe" class="form-control form-sm inpEst" style="width: 530px"
                           value="<?php if($gruape != null){echo $gruape;} ?>" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="intfGen">GENERO:</label></span>
                    <select id="intfGen" name="intfGen" class="form-control form-sm" style="width: 160px">
                        <option>Femenino</option>
                        <option>Masculino</option>
                        <?php
                        if($grugen != null){?><option selected><?php echo $grugen ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>

                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 10px"></span>

                    <span class="input-group-addon input-sm"><label for="intfPare">PARENTESCO:</label></span>
                    <select id="intfPare" name="intfPare" class="form-control form-sm inpEst" style="width: 150px">
                        <?php
                        $query = "select Parcod,Pardes from root_000067 WHERE Parest = 'on'";
                        $commit = mysql_query($query, $conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
                        while($dato = mysql_fetch_assoc($commit))
                        {
                            $codParentesco = $dato['Parcod'];   $parentesco = $dato['Pardes'];
                            ?><option><?php echo $codParentesco.'-'.$parentesco ?></option><?php
                        }
                        if($grupar != null){?><option selected><?php echo $grupar ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="intfFen">FECHA NACIMIENTO:</label></span>
                    <input type="date" id="intfFen" name="intfFen" class="form-control form-sm" style="width: 160px"
                           value="<?php if($grufna != null){echo $grufna;} ?>" >

                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 10px"></span>

                    <span class="input-group-addon input-sm"><label for="intfNie">NIVEL EDUCATIVO:</label></span>
                    <select id="intfNie" name="intfNie" class="form-control form-sm inpEst" style="width: 150px">
                        <?php
                        $queryEscolar = "select Scodes,Scocod from root_000066 WHERE Scoest = 'on' AND Scoley = 'off' ORDER BY Scodes ASC ";
                        $comQryEscolar = mysql_query($queryEscolar, $conex) or die (mysql_errno()." - en el query: ".$queryEscolar." - ".mysql_error());
                        while($datoEscolar = mysql_fetch_assoc($comQryEscolar))
                        {
                            $gradoEscolar = $datoEscolar['Scocod'].'-'.$datoEscolar['Scodes'];
                            ?><option><?php echo $gradoEscolar ?></option><?php
                        }
                        if($gruesc != null){?><option selected><?php echo $gruesc ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="intfOcu">OCUPACION:</label></span>
                    <select id="intfOcu" name="intfOcu" class="form-control form-sm" style="width: 160px">
                        <?php
                        $query = "select Ocucod,Ocudes from root_000078 WHERE Ocuest = 'on'";
                        $commit = mysql_query($query, $conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
                        while($dato = mysql_fetch_assoc($commit))
                        {
                            $codOcu = $dato['Ocucod']; $ocupacion = $dato['Ocudes'];
                            ?><option><?php echo $codOcu.'-'.$ocupacion ?></option><?php
                        }
                        if($gruocu != null){?><option selected><?php echo $gruocu ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>

                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 10px"></span>

                    <span class="input-group-addon input-sm"><label for="intfVius">VIVE CON USTED:</label></span>
                    <select id="intfVius" name="intfVius" class="form-control form-sm inpEst" style="width: 150px">
                        <option>SI</option>
                        <option>NO</option>
                        <?php
                        if($grucom != null){?><option selected><?php echo $grucom ?></option><?php }
                        else{?><option selected disabled>Seleccione...</option><?php }
                        ?>
                    </select>
                </div>

                <div id="divAddEst2" align="center" style="margin-top: 3px">
                    <input type="hidden" id="funcion" name="funcion" value="updateIntFam">
                    <input type="hidden" id="tablaUser" name="tablaUser" value="<?php echo $tbInfoEmpleado?>">  <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $usuarioMtx ?>">
                    <input type="hidden" name="Idefnc2" value="<?php echo $Idefnc ?>">              <input type="hidden" name="Idegen2" value="<?php echo $Idegen ?>">
                    <input type="hidden" name="Ideced2" value="<?php echo $Ideced ?>">              <input type="hidden" name="Idepas2" value="<?php echo $Idepas ?>">
                    <input type="hidden" name="Idevis2" value="<?php echo $Idevis ?>">              <input type="hidden" name="estcivUser2" value="<?php echo $estcivUser ?>">
                    <input type="hidden" name="Idedir2" value="<?php echo $Idedir ?>">              <input type="hidden" name="lunaUser2" value="<?php echo $lunaUser ?>">
                    <input type="hidden" name="estUser2" value="<?php echo $estUser ?>">            <input type="hidden" name="munUser2" value="<?php echo $munUser ?>">
                    <input type="hidden" name="barUser2" value="<?php echo $barUser ?>">            <input type="hidden" name="telUser2" value="<?php echo $telUser ?>">
                    <input type="hidden" name="celUser2" value="<?php echo $celUser ?>">            <input type="hidden" name="corUser2" value="<?php echo $corUser ?>">
                    <input type="hidden" name="tisanUser2" value="<?php echo $tisanUser ?>">        <input type="hidden" name="extUser2" value="<?php echo $extUser ?>">
                    <input type="hidden" name="Cviviv2" value="<?php echo $Cviviv ?>">              <input type="hidden" name="Cvitvi2" value="<?php echo $Cvitvi ?>">
                    <input type="hidden" name="Cvitrz2" value="<?php echo $Cvitrz ?>">              <input type="hidden" name="Cvilot2" value="<?php echo $Cvilot ?>">
                    <input type="hidden" name="Cvisvi2" value="<?php echo $Cvisvi ?>">              <input type="hidden" name="chAcue2" value="<?php echo $chAcue ?>">
                    <input type="hidden" name="chAlca2" value="<?php echo $chAlca ?>">              <input type="hidden" name="chAseo2" value="<?php echo $chAseo ?>">
                    <input type="hidden" name="chEner2" value="<?php echo $chEner ?>">              <input type="hidden" name="chInter2" value="<?php echo $chInter ?>">
                    <input type="hidden" name="chGas2" value="<?php echo $chGas ?>">                <input type="hidden" name="chTele2" value="<?php echo $chTele ?>">
                    <input type="hidden" name="credito2" value="<?php echo $Cvicre ?>">             <input type="hidden" name="chBici2" value="<?php echo $chBici ?>">
                    <input type="hidden" name="chBus2" value="<?php echo $chBus ?>">                <input type="hidden" name="chCamina2" value="<?php echo $chCamina ?>">
                    <input type="hidden" name="chPart2" value="<?php echo $chPart ?>">              <input type="hidden" name="chMetro2" value="<?php echo $chMetro ?>">
                    <input type="hidden" name="chMoto2" value="<?php echo $chMoto ?>">              <input type="hidden" name="chOtroT2" value="<?php echo $chOtroT ?>">
                    <input type="hidden" name="chTaxi2" value="<?php echo $chTaxi ?>">              <input type="hidden" name="chContra2" value="<?php echo $chContra ?>">
                    <input type="hidden" name="otroTrans2" value="<?php echo $Cviotr ?>">           <input type="hidden" name="chTrae2" value="<?php echo $chTrae ?>">
                    <input type="hidden" name="chComBoca2" value="<?php echo $chComBoca ?>">        <input type="hidden" name="chComOtros2" value="<?php echo $chComOtros ?>">
                    <input type="hidden" name="chCasa2" value="<?php echo $chCasa ?>">              <input type="hidden" name="chOtrosAl2" value="<?php echo $chOtrosAl ?>">
                    <input type="hidden" name="Cvioal2" value="<?php echo $Cvioal ?>">              <input type="hidden" name="chClaInfo2" value="<?php echo $chClaInfo ?>">
                    <input type="hidden" name="chClaIng2" value="<?php echo $chClaIng ?>">          <input type="hidden" name="chGastro2" value="<?php echo $chGastro ?>">
                    <input type="hidden" name="chConFami2" value="<?php echo $chConFami ?>">        <input type="hidden" name="chConCrePer2" value="<?php echo $chConCrePer ?>">
                    <input type="hidden" name="Cvihbb2" value="<?php echo $Cvihbb ?>">              <input type="hidden" name="Otrpar2" value="<?php echo $Otrpar ?>">
                    <input type="hidden" name="Otrlocker2" value="<?php echo $Otrlocker ?>">        <input type="hidden" name="Otractrec2" value="<?php echo $Otractrec ?>">
                    <input type="hidden" name="Otractrechor2" value="<?php echo $Otractrechor ?>">  <input type="hidden" name="Otractcul2" value="<?php echo $Otractcul ?>">
                    <input type="hidden" name="Otractculhor2" value="<?php echo $Otractculhor ?>">  <input type="hidden" name="chAudit2" value="<?php echo $chAudit ?>">
                    <input type="hidden" name="chComite2" value="<?php echo $chComite ?>">          <input type="hidden" name="chBrigada2" value="<?php echo $chBrigada ?>">
                    <input type="hidden" name="chOtroRol2" value="<?php echo $chOtroRol ?>">        <input type="hidden" name="otrRol2" value="<?php echo $Otrroles ?>">
                    <input type="hidden" name="contEmer2" value="<?php echo $contEmer ?>">          <input type="hidden" name="usuRaza2" value="<?php echo $usuRaza ?>">
                    <input type="hidden" name="idGruFam" value="<?php echo $idGruFam ?>">
                    <input type="hidden" name="telEmer" value="<?php echo $telEmer ?>">             <input type="hidden" name="usuGasto" value="<?php echo $usuGasto ?>">
                    <input type="hidden" name="usuSitua" value="<?php echo $usuSitua ?>">           <input type="hidden" name="posFam" value="<?php echo $posFam ?>">
                    <input type="hidden" name="usuCuHi" value="<?php echo $usuCuHi ?>">             <input type="hidden" name="subViv" value="<?php echo $subViv ?>">
                    <input type="hidden" name="ahoViv" value="<?php echo $ahoViv ?>">               <input type="hidden" name="montoHa" value="<?php echo $montoHa ?>">
                    <input type="hidden" name="Usfariesvi" value="<?php echo $Usfariesvi ?>">       <input type="hidden" name="UsuMejoVi" value="<?php echo $UsuMejoVi ?>">
                    <input type="hidden" name="UsProFi" value="<?php echo $UsProFi ?>">             <input type="hidden" name="UsMoCre" value="<?php echo $UsMoCre ?>">
                    <input type="hidden" name="UsEpAcu" value="<?php echo $UsEpAcu ?>">             <input type="hidden" name="UsLcInt" value="<?php echo $UsLcInt ?>">
                    <input type="hidden" name="UsIaAho" value="<?php echo $UsIaAho ?>">             <input type="hidden" name="UsNefor" value="<?php echo $UsNefor ?>">
                    <input type="hidden" name="UsHobie" value="<?php echo $UsHobie ?>">             <input type="hidden" name="UsQpTie" value="<?php echo $UsQpTie ?>">
                    <input type="hidden" name="UsHhTli" value="<?php echo $UsHhTli ?>">             <input type="hidden" name="UsBuTli" value="<?php echo $UsBuTli ?>">
                    <button type="submit" class="btn btn-default btn-lg">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true" style="width: 50px;"></span> Guardar
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    if($funcion == 'insertIntFam')
    {
        if($intfNom != null and $intfApe != null and $intfGen != null and $intfPare != null and $intfFen != null and $intfNie != null and $intfOcu != null and $intfVius != null)
        {
            $qryInsIntFam = "insert into".' '."$tablaUser"."_000021
                             values('talhuma','$fechaActual','$horaActual','$intfNom','$intfApe','$intfGen','$intfPare','$intfFen','$intfNie','$intfOcu','$intfVius','','$userMtx','on','C-$wuse','')";
            $comQryInsIntFam = mysql_query($qryInsIntFam, $conex) or die (mysql_errno()." - en el query: ".$qryInsIntFam." - ".mysql_error());

            //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_24(Condiciones de vida y vivienda):
            saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
                $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
                $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
                $Cvioal,$wuse);

            //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_60(Respuesta Otras Preguntas Caracterizacion):
            saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
                $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
                $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
                $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
                $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse);

            //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:
            saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,$tisanUser,
                $wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$cedula);

            //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
            saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
                $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
                $UsBuTli);

            obtenerOpenTab($conex,$wuse,$fechaActual,3);

            if($comQryInsIntFam)
            {
                ?>
                <div id="divAddEst2" align="center" style="margin-top: 80px">
                    <input type="hidden" id="funcion" name="funcion" value="insertIntFam">
                    <button type="submit" class="btn btn-default btn-lg"
                            onclick="window.opener.location.reload(); window.close();">
                        <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Guardado
                    </button>
                </div>
                <?php
            }
            else
            {
                ?>
                <div id="divAddEst2" align="center">
                    <h3>No se pudo guardar el registro</h3><h3>por favor verifique</h3>
                </div>

                <form method="post" action="carEmp_Ops.php">
                    <input type="hidden" id="intfNom" name="intfNom" value="<?php echo $intfNom ?>">
                    <input type="hidden" id="intfApe" name="intfApe" value="<?php echo $intfApe ?>">
                    <input type="hidden" id="intfGen" name="intfGen" value="<?php echo $intfGen ?>">
                    <input type="hidden" id="intfPare" name="intfPare" value="<?php echo $intfPare ?>">
                    <input type="hidden" id="intfFen" name="intfFen" value="<?php echo $intfFen ?>">
                    <input type="hidden" id="intfNie" name="intfNie" value="<?php echo $intfNie ?>">
                    <input type="hidden" id="intfOcu" name="intfOcu" value="<?php echo $intfOcu ?>">
                    <input type="hidden" id="intfVius" name="intfVius" value="<?php echo $intfVius ?>">
                    <input type="hidden" id="intfNomcol" name="intfNomcol" value="<?php echo $intfNomcol ?>">
                    <input type="hidden" id="funcion" name="funcion" value="addIntFam">

                    <div id="divAddEst2" align="center">
                        <button type="submit" class="btn btn-warning btn-lg" onclick="openOps(funcion)">
                            <span class="glyphicon glyphicon-warning-sign" aria-hidden="true" style="width: 50px;"></span> Aceptar
                        </button>
                    </div>
                </form>
                <?php
            }
        }
        else
        {
            ?>
            <div id="divAddEst2" align="center">
                <h3>Todos los campos son obligatorios</h3><h3>por favor verifique</h3>
            </div>

            <form method="post" action="carEmp_Ops.php">
                <input type="hidden" id="intfNom" name="intfNom" value="<?php echo $intfNom ?>">
                <input type="hidden" id="intfApe" name="intfApe" value="<?php echo $intfApe ?>">
                <input type="hidden" id="intfGen" name="intfGen" value="<?php echo $intfGen ?>">
                <input type="hidden" id="intfPare" name="intfPare" value="<?php echo $intfPare ?>">
                <input type="hidden" id="intfFen" name="intfFen" value="<?php echo $intfFen ?>">
                <input type="hidden" id="intfNie" name="intfNie" value="<?php echo $intfNie ?>">
                <input type="hidden" id="intfOcu" name="intfOcu" value="<?php echo $intfOcu ?>">
                <input type="hidden" id="intfVius" name="intfVius" value="<?php echo $intfVius ?>">
                <input type="hidden" id="intfNomcol" name="intfNomcol" value="<?php echo $intfNomcol ?>">
                <input type="hidden" id="userMatrix" name="userMatrix" value="<?php echo $userMtx ?>">
                <input type="hidden" id="funcion" name="funcion" value="addIntFam">

                <div id="divAddEst2" align="center">
                    <button type="submit" class="btn btn-warning btn-lg" onclick="openOps(funcion)">
                        <span class="glyphicon glyphicon-warning-sign" aria-hidden="true" style="width: 50px;"></span> Aceptar
                    </button>
                </div>
            </form>
            <?php
        }
    }

    if($funcion == 'updateIntFam')
    {
        $queryDelEstAc = "update".' '."$tablaUser"."_000021
                            set Grunom = '$intfNom', Gruape = '$intfApe', Grugen = '$intfGen', Grupar = '$intfPare',
                                Grufna = '$intfFen', Gruesc = '$intfNie', Gruocu = '$intfOcu', Grucom = '$intfVius'
                          where id = '$idGruFam'";
        $comQryDelEstAc = mysql_query($queryDelEstAc, $conex) or die (mysql_errno()." - en el query: ".$queryDelEstAc." - ".mysql_error());

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_24(Condiciones de vida y vivienda):
        saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
            $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
            $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
            $Cvioal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_60(Respuesta Otras Preguntas Caracterizacion):
        saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
            $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
            $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
            $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
            $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:
        saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,$tisanUser,
            $wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$cedula);

        //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
        saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
            $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
            $UsBuTli);

        obtenerOpenTab($conex,$wuse,$fechaActual,3);

        ?>
        <div id="divAddEst2" align="center" style="margin-top: 80px">
            <button type="submit" class="btn btn-default btn-lg" onclick="window.opener.location.reload(); window.close()">
                <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Actualizado
            </button>
        </div>
        <?php
    }

    if($funcion == 'supIntFam')
    {
        $queryDelEstAc = "delete from ".' '."$tablaUser"."_000021 where id = '$idEstud'";
        $comQryDelEstAc = mysql_query($queryDelEstAc, $conex) or die (mysql_errno()." - en el query: ".$queryDelEstAc." - ".mysql_error());

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_24(Condiciones de vida y vivienda):
        saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
            $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
            $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
            $Cvioal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_60(Respuesta Otras Preguntas Caracterizacion):
        saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
            $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
            $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
            $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
            $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:
        saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,$tisanUser,
            $wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$cedula);

        //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
        saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
            $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
            $UsBuTli);

        obtenerOpenTab($conex,$wuse,$fechaActual,3);

        ?>
        <div id="divAddEst2" align="center" style="margin-top: 80px">
            <button type="submit" class="btn btn-default btn-lg" onclick="window.opener.location.reload();window.close()">
                <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Eliminado
            </button>
        </div>
        <?php
    }

    if($funcion == 'saveVivienda')
    {
        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_24(Condiciones de vida y vivienda):
        saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
                   $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
                   $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
                   $Cvioal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA_60(Respuesta Otras Preguntas Caracterizacion):
        saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
                   $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
                   $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
                   $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
                   $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse);

        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:
        saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,$tisanUser,
                   $wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$existCedula);

        //GUARDAR DATOS ADICIONALES INFORMACION GENERAL:
        saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
                   $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
                   $UsBuTli);

        //INSERTAR EN TABLA DE REGISTRO DE DILIGENCIAMIENTO:
        saveTal_08($conex,$fechaActual,$horaActual,$wuse,$existCedula);

        obtenerOpenTab($conex,$wuse,$fechaActual,1);

        ?>
        <div id="divAddEst2" align="center" style="margin-top: 80px">
            <input type="hidden" id="funcion" name="funcion" value="insertCredito">
            <button type="submit" class="btn btn-default btn-lg"
                    onclick="window.opener.location.reload(); window.close()">
                <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Información Actualizada Correctamente
            </button>
        </div>
        <?php
    }

    if($funcion == 'saveFam')
    {
        $QuerySel1 = "select id from".' '."$tablaUser"."_000019 where Famuse = '$usuarioMtx'";
        $commSel1 = mysql_query($QuerySel1, $conex) or die (mysql_errno()." - en el query: ".$QuerySel1." - ".mysql_error());
        $dato1 = mysql_fetch_assoc($commSel1);
        $idTal19 = $dato1['id'];

        if($idTal19 == null)
        {
            $queryInsT19 = "insert into".' '."$tablaUser"."_000019
                            values('talhuma','$fechaActual','$horaActual','$variable','','','','','','','','','$usuarioMtx','on','C-$wuse','')";
            $comQryInsT19 = mysql_query($queryInsT19, $conex) or die (mysql_errno()." - en el query: ".$queryInsT19." - ".mysql_error());
        }
        else
        {
            $queryUpdT19 = "update ".' '."$tablaUser"."_000019 set Famaco = '$variable' where Famuse = '$usuarioMtx'";
            $commUpdT19 = mysql_query($queryUpdT19, $conex) or die (mysql_errno()." - en el query: ".$queryUpdT19." - ".mysql_error());
        }



        //obtenerOpenTab($conex,$wuse,$fechaActual,3);
        ?><script>window.close()</script><?php
    }

    if($funcion == 'saveFam2')
    {
        $QuerySel1 = "select id from".' '."$tablaUser"."_000019 where Famuse = '$usuarioMtx'";
        $commSel1 = mysql_query($QuerySel1, $conex) or die (mysql_errno()." - en el query: ".$QuerySel1." - ".mysql_error());
        $dato1 = mysql_fetch_assoc($commSel1);
        $idTal19 = $dato1['id'];

        if($idTal19 == null)
        {
            $queryInsT19 = "insert into".' '."$tablaUser"."_000019
                            values('talhuma','$fechaActual','$horaActual','','$variable','','','','','','','','$usuarioMtx','on','C-$wuse','')";
            $comQryInsT19 = mysql_query($queryInsT19, $conex) or die (mysql_errno()." - en el query: ".$queryInsT19." - ".mysql_error());
        }
        else
        {
            $queryUpdT19 = "update".' '."$tablaUser"."_000019 set Famcab = '$variable' where Famuse = '$usuarioMtx'";
            $commUpdT19 = mysql_query($queryUpdT19, $conex) or die (mysql_errno()." - en el query: ".$queryUpdT19." - ".mysql_error());
        }

        //obtenerOpenTab($conex,$wuse,$fechaActual,3);
        ?>
            <script>window.close()</script>
        <?php
    }

    if($funcion == 'saveFam3')
    {
        $QuerySel1 = "select id from".' '."$tablaUser"."_000019 where Famuse = '$usuarioMtx'";
        $commSel1 = mysql_query($QuerySel1, $conex) or die (mysql_errno()." - en el query: ".$QuerySel1." - ".mysql_error());
        $dato1 = mysql_fetch_assoc($commSel1);
        $idTal19 = $dato1['id'];

        if($idTal19 == null)
        {
            $queryInsT19 = "insert into".' '."$tablaUser"."_000019
                            values('talhuma','$fechaActual','$horaActual','','','$variable','','','','','','','$usuarioMtx','on','C-$wuse','')";
            $comQryInsT19 = mysql_query($queryInsT19, $conex) or die (mysql_errno()." - en el query: ".$queryInsT19." - ".mysql_error());
        }
        else
        {
            $queryUpdT19 = "update".' '."$tablaUser"."_000019 set Fammac = '$variable' where Famuse = '$usuarioMtx'";
            $commUpdT19 = mysql_query($queryUpdT19, $conex) or die (mysql_errno()." - en el query: ".$queryUpdT19." - ".mysql_error());
        }

        //obtenerOpenTab($conex,$wuse,$fechaActual,3);
        ?><script>window.close()</script><?php
    }

    if($funcion == 'saveFam4')
    {
        $QuerySel1 = "select id from".' '."$tablaUser"."_000019 where Famuse = '$usuarioMtx'";
        $commSel1 = mysql_query($QuerySel1, $conex) or die (mysql_errno()." - en el query: ".$QuerySel1." - ".mysql_error());
        $dato1 = mysql_fetch_assoc($commSel1);
        $idTal19 = $dato1['id'];

        if($idTal19 == null)
        {
            $queryInsT19 = "insert into".' '."$tablaUser"."_000019
                            values('talhuma','$fechaActual','$horaActual','','','','$variable','','','','','','$usuarioMtx','on','C-$wuse','')";
            $comQryInsT19 = mysql_query($queryInsT19, $conex) or die (mysql_errno()." - en el query: ".$queryInsT19." - ".mysql_error());
        }
        else
        {
            $queryUpdT19 = "update".' '."$tablaUser"."_000019 set Famaac = '$variable' where Famuse = '$usuarioMtx'";
            $commUpdT19 = mysql_query($queryUpdT19, $conex) or die (mysql_errno()." - en el query: ".$queryUpdT19." - ".mysql_error());
        }

        //obtenerOpenTab($conex,$wuse,$fechaActual,3);
        ?><script>window.close()</script><?php
    }

    if($funcion == 'saveFam5')
    {
        if($variable == 'SI'){$variable = 'on';}
        else{$variable = 'off';}

        $QuerySel1 = "select id from".' '."$tablaUser"."_000019 where Famuse = '$usuarioMtx'";
        $commSel1 = mysql_query($QuerySel1, $conex) or die (mysql_errno()." - en el query: ".$QuerySel1." - ".mysql_error());
        $dato1 = mysql_fetch_assoc($commSel1);
        $idTal19 = $dato1['id'];

        if($idTal19 == null)
        {
            $queryInsT19 = "insert into".' '."$tablaUser"."_000019
                            values('talhuma','$fechaActual','$horaActual','','','','','$variable','','','','','$usuarioMtx','on','C-$wuse','')";
            $comQryInsT19 = mysql_query($queryInsT19, $conex) or die (mysql_errno()." - en el query: ".$queryInsT19." - ".mysql_error());
        }
        else
        {
            $queryUpdT19 = "update".' '."$tablaUser"."_000019 set Famtpd = '$variable' where Famuse = '$usuarioMtx'";
            $commUpdT19 = mysql_query($queryUpdT19, $conex) or die (mysql_errno()." - en el query: ".$queryUpdT19." - ".mysql_error());
        }

        //obtenerOpenTab($conex,$wuse,$fechaActual,3);
        ?><script>window.close()</script><?php
    }

    if($funcion == 'saveFam6')
    {
        $QuerySel1 = "select id from".' '."$tablaUser"."_000019 where Famuse = '$usuarioMtx'";
        $commSel1 = mysql_query($QuerySel1, $conex) or die (mysql_errno()." - en el query: ".$QuerySel1." - ".mysql_error());
        $dato1 = mysql_fetch_assoc($commSel1);
        $idTal19 = $dato1['id'];

        if($idTal19 == null)
        {
            $queryInsT19 = "insert into".' '."$tablaUser"."_000019
                            values('talhuma','$fechaActual','$horaActual','','','','','','','$variable','','','$usuarioMtx','on','C-$wuse','')";
            $comQryInsT19 = mysql_query($queryInsT19, $conex) or die (mysql_errno()." - en el query: ".$queryInsT19." - ".mysql_error());
        }
        else
        {
            $queryUpdT19 = "update".' '."$tablaUser"."_000019 set Famtms = '$variable' where Famuse = '$usuarioMtx'";
            $commUpdT19 = mysql_query($queryUpdT19, $conex) or die (mysql_errno()." - en el query: ".$queryUpdT19." - ".mysql_error());
        }

        //obtenerOpenTab($conex,$wuse,$fechaActual,3);
        ?><script>window.close()</script><?php
    }

    if($funcion == 'saveSal')
    {
        $cadena = explode("-", $variable);
        $codEps = $cadena[0];

        $QuerySel1 = "select id from".' '."$tablaUser"."_000013 where Ideuse = '$usuarioMtx'";
        $commSel1 = mysql_query($QuerySel1, $conex) or die (mysql_errno()." - en el query: ".$QuerySel1." - ".mysql_error());
        $dato1 = mysql_fetch_assoc($commSel1);
        $idTal19 = $dato1['id'];

        if($idTal19 == null)
        {
            $queryInsT19 = "insert into".' '."$tablaUser"."_000013
                            values('talhuma','$fechaActual','$horaActual','','','','','','','','','','','','','','','','','$codEps','','','',
                            '','','','','','','','','','','','','','','$usuarioMtx','','','','','','','','','','','','','C-$wuse','')";
            $comQryInsT19 = mysql_query($queryInsT19, $conex) or die (mysql_errno()." - en el query: ".$queryInsT19." - ".mysql_error());
        }
        else
        {
            $queryUpdT19 = "update".' '."$tablaUser"."_000013 set Ideeps = '$codEps' where Ideuse = '$usuarioMtx'";
            $commUpdT19 = mysql_query($queryUpdT19, $conex) or die (mysql_errno()." - en el query: ".$queryUpdT19." - ".mysql_error());
        }

        //obtenerOpenTab($conex,$wuse,$fechaActual,3);
        ?><script>window.close()</script><?php
    }

    if($funcion == 'saveSal2')
    {
        $QuerySel1 = "select id from".' '."$tablaUser"."_000013 where Ideuse = '$usuarioMtx'";
        $commSel1 = mysql_query($QuerySel1, $conex) or die (mysql_errno()." - en el query: ".$QuerySel1." - ".mysql_error());
        $dato1 = mysql_fetch_assoc($commSel1);
        $idTal19 = $dato1['id'];

        if($idTal19 == null)
        {
            $queryInsT19 = "insert into".' '."$tablaUser"."_000013
                            values('talhuma','$fechaActual','$horaActual','','','','','','','','','','','','','','','','','','$variable','','',
                            '','','','','','','','','','','','','','','$usuarioMtx','','','','','','','','','','','','','C-$wuse','')";
            $comQryInsT19 = mysql_query($queryInsT19, $conex) or die (mysql_errno()." - en el query: ".$queryInsT19." - ".mysql_error());
        }
        else
        {
            $queryUpdT19 = "update".' '."$tablaUser"."_000013 set Idescs = '$variable' where Ideuse = '$usuarioMtx'";
            $commUpdT19 = mysql_query($queryUpdT19, $conex) or die (mysql_errno()." - en el query: ".$queryUpdT19." - ".mysql_error());
        }

        //obtenerOpenTab($conex,$wuse,$fechaActual,3);
        ?><script>window.close()</script><?php
    }

    if($funcion == 'addNewUser')
    {
        ?>
        <h4 class="labelTitulo">REGISTRAR DATOS BASICOS DEL EMPLEADO</h4>
        <div id="divDatEst" class="input-group">
            <form method="post" action="carEmp_Ops.php">
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="priNom">Primer Nombre:</label></span>
                    <input type="text" id="priNom" name="priNom" class="form-control form-sm inpEst" style="width: 150px" >

                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 10px"></span>

                    <span class="input-group-addon input-sm"><label for="segNom">Segundo Nombre:</label></span>
                    <input type="text" id="segNom" name="segNom" class="form-control form-sm inpEst" style="width: 150px" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="priApe">Primer Apellido:</label></span>
                    <input type="text" id="priApe" name="priApe" class="form-control form-sm inpEst" style="width: 150px" >

                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 10px"></span>

                    <span class="input-group-addon input-sm"><label for="segApe">Segundo Apellido:</label></span>
                    <input type="text" id="segApe" name="segApe" class="form-control form-sm inpEst" style="width: 150px" >
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="fecNac">Fecha de Nacimiento:</label></span>
                    <input type="date" id="fecNac" name="fecNac" class="form-control form-sm inpEst" style="width: 150px" >

                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 10px"></span>

                    <span class="input-group-addon input-sm"><label for="genero">Genero:</label></span>
                    <select id="genero" name="genero" class="form-control form-sm inpEst" style="width: 150px">
                        <option>MASCULINO</option>
                        <option>FEMENINO</option>
                        <option selected disabled>Seleccione...</option>
                    </select>
                </div>
                <div class="input-group datEstudio">
                    <span class="input-group-addon input-sm"><label for="cedula">Cédula:</label></span>
                    <input type="text" id="cedula" name="cedula" class="form-control form-sm inpEst" style="width: 370px" >
                </div>
                <div id="divAddEst2" align="center">
                    <input type="hidden" id="funcion" name="funcion" value="insertEmple">
                    <input type="hidden" id="codUsFirst" name="codUsFirst" value="<?php echo $codUsuario ?>">
                    <button type="submit" class="btn btn-default btn-lg">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true" style="width: 50px;"></span> Guardar
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    if($funcion == 'insertEmple')
    {
        echo 'USUARIO SIN PROCESAR='.$codUsFirst;

        if($cedula != null)
        {

        }
        else
        {
            ?>
            <div id="divAddEst2" align="center">
                <h3>Todos los campos son obligatorios</h3><h3>por favor verifique</h3>
            </div>

            <form method="post" action="carEmp_Ops.php">
                <input type="hidden" id="codUsFirst2" name="codUsFirst2" value="<?php echo $codUsFirst ?>">
                <input type="hidden" id="funcion" name="funcion" value="addNewUser">

                <div id="divAddEst2" align="center">
                    <button type="submit" class="btn btn-warning btn-lg" onclick="openOps(funcion)">
                        <span class="glyphicon glyphicon-warning-sign" aria-hidden="true" style="width: 50px;"></span> Aceptar
                    </button>
                </div>
            </form>
            <?php
        }

    }

    if($funcion == 'saveBasic')
    {
        if($IdefncP != null and $IdegenP != null and $IdecedP != null)
        {
            if($tablaUser == 'talhuma')
            {
                $queryInsT19 = "insert into".' '."$tablaUser"."_000013
                            values('talhuma','$fechaActual','$horaActual','','','','','','','','','','','','','','','','','','','$IdefncP','$IdegenP','$IdecedP',
                            '','','','','','','','','','','','','','$usuarioMtx','on','','','','','','','','','','','','C-$wuse','')";
                $comQryInsT19 = mysql_query($queryInsT19, $conex) or die (mysql_errno()." - en el query: ".$queryInsT19." - ".mysql_error());
            }

            if($comQryInsT19)
            {
                //INSERTAR EN TABLA DE REGISTRO DE DILIGENCIAMIENTO:
                saveTal_08($conex,$fechaActual,$horaActual,$wuse,$IdecedP);
                ?>
                <div id="divAddEst2" align="center" style="margin-top: 80px">
                    <input type="hidden" id="funcion" name="funcion" value="insertCredito">
                    <button type="submit" class="btn btn-default btn-lg"
                            onclick="window.opener.location.reload(); window.close()">
                        <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Información Actualizada Correctamente
                    </button>
                </div>
                <?php
            }
            else
            {
                ?>
                <div id="divAddEst2" align="center" style="margin-top: 80px">
                    <input type="hidden" id="funcion" name="funcion" value="insertCredito">
                    <button type="submit" class="btn btn-default btn-lg"
                            onclick="window.opener.location.reload(); window.close()">
                        <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> No se pudo insertar la informacion
                    </button>
                </div>
                <?php
            }
        }
    }

    /*
    if($funcion == 'saveOtros')
    {
        if($chAudit == 'on'){$chAudit = '01';} else{$chAudit = '00';}
        if($chComite == 'on'){$chComite = '02';} else{$chComite = '00';}
        if($chBrigada == 'on'){$chBrigada = '03';} else{$chBrigada = '00';}
        if($chOtroRol == 'on'){$chOtroRol = '04';} else{$chOtroRol = '00';}
        $Otrrol = $chAudit.','.$chComite.','.$chBrigada.','.$chOtroRol; //ROLES EMPLEADO

        if($Otrlocker == 'SI'){$Otrlocker = 'on';} else{$Otrlocker = 'off';}

        $QuerySel1 = "select id from talhuma_000060 where Otruse = '$usuarioMtx'";
        $commSel1 = mysql_query($QuerySel1, $conex) or die (mysql_errno()." - en el query: ".$QuerySel1." - ".mysql_error());
        $dato1 = mysql_fetch_assoc($commSel1);
        $idTal19 = $dato1['id'];

        if($idTal19 == null)
        {
            $queryInsT19 = "insert into talhuma_000060
                            values('talhuma','$fechaActual','$horaActual','$usuarioMtx','$Otrpar','$Otrlocker','$Otractrec','$Otractrechor',
                            '$Otractcul','$Otractculhor','$Otrrol','$Otrroles','C-$wuse','')";
            $comQryInsT19 = mysql_query($queryInsT19, $conex) or die (mysql_errno()." - en el query: ".$queryInsT19." - ".mysql_error());

            obtenerOpenTab($conex,$wuse,$fechaActual,1);

            ?><script>window.close()</script><?php
        }
        else
        {
            $queryUpdT19 = "update talhuma_000060 set Otrpar='$Otrpar',Otrlocker='$Otrlocker',Otractrec='$Otractrec',Otractrechor='$Otractrechor',
                                   Otractcul='$Otractcul',Otractculhor='$Otractculhor',Otrrol='$Otrrol',Otrroles='$Otrroles'
                            where Otruse = '$usuarioMtx'";
            $commUpdT19 = mysql_query($queryUpdT19, $conex) or die (mysql_errno()." - en el query: ".$queryUpdT19." - ".mysql_error());

            obtenerOpenTab($conex,$wuse,$fechaActual,1);

            ?>
            <div id="divAddEst2" align="center" style="margin-top: 80px">
                <input type="hidden" id="funcion" name="funcion" value="insertCredito">
                <button type="submit" class="btn btn-default btn-lg"
                        onclick="window.opener.location.reload(); window.close()">
                    <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Información Actualizada Correctamente
                </button>
            </div>
            <?php


        }
    }

    if($funcion == 'saveGeneral')
    {
        //CONSULTAR SI YA EXISTE REGISTRO DEL USUARIO EN TALHUMA:

        $query1 = "select id from".' '."$tablaUser"."_000013 where Ideuse = '$usuarioMtx'";
        $commit1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
        $datoEmpl = mysql_fetch_assoc($commit1);
        $idEmpl = $datoEmpl['id'];

        if($idEmpl == null)
        {
            $queryIns1 = "insert into".' '."$tablaUser"."_000013
                          values('talhuma','$fechaActual','$horaActual','$Idepas','$Idevis','','$estcivUser','$lunaUser','$munUser','$barUser',
                                 '$estUser','$tisanUser','',
                                 'on','C-$wuse','')";
            $comQryIns1 = mysql_query($queryIns1, $conex) or die (mysql_errno()." - en el query: ".$queryIns1." - ".mysql_error());


            obtenerOpenTab($conex,$wuse,$fechaActual,1);

            if($comQryIns1)
            {
                ?>
                <div id="divAddEst2" align="center" style="margin-top: 80px">
                    <input type="hidden" id="funcion" name="funcion" value="insertCredito">
                    <button type="submit" class="btn btn-default btn-lg"
                            onclick="window.opener.location.reload(); window.close();">
                        <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Registro Guardado
                    </button>
                </div>
                <?php
            }
            else
            {
                ?>
                <div id="divAddEst2" align="center">
                    <h3>No se pudo guardar el registro</h3><h3>por favor verifique</h3>
                </div>

                <form method="post" action="carEmp_Ops.php">
                    <input type="hidden" id="cremot" name="cremot" value="<?php echo $cremot ?>">
                    <input type="hidden" id="creent" name="creent" value="<?php echo $creent ?>">
                    <input type="hidden" id="creval" name="creval" value="<?php echo $creval ?>">
                    <input type="hidden" id="crecuo" name="crecuo" value="<?php echo $crecuo ?>">
                    <input type="hidden" id="userMatrix" name="userMatrix" value="<?php echo $userMtx ?>">
                    <input type="hidden" id="funcion" name="funcion" value="addCredito">

                    <div id="divAddEst2" align="center">
                        <button type="submit" class="btn btn-warning btn-lg" onclick="openOps(funcion)">
                            <span class="glyphicon glyphicon-warning-sign" aria-hidden="true" style="width: 50px;"></span> Aceptar
                        </button>
                    </div>
                </form>
                <?php
            }

        }
        else
        {
            //OBTENER CODIGO DEL MUNICIPIO:
            $QueryMuni = "select Codigo from root_000006 WHERE Nombre LIKE '$munUser'";
            $commMuni = mysql_query($QueryMuni, $conex) or die (mysql_errno()." - en el query: ".$QueryMuni." - ".mysql_error());
            $datoMuni = mysql_fetch_assoc($commMuni);
            $codMuni = $datoMuni['Codigo'];

            //OBTENER CODIGO DEL BARRIO:
            $QueryBarr = "select Barcod from root_000034 WHERE Bardes LIKE '$barUser' AND Barmun = '$codMuni'";
            $commBarr = mysql_query($QueryBarr, $conex) or die (mysql_errno()." - en el query: ".$QueryBarr." - ".mysql_error());
            $datoBarr = mysql_fetch_assoc($commBarr);
            $codBarr = $datoBarr['Barcod'];

            //OBTENER ESTADO CIVIL:
            $QueryEstCiv = "select Scvcod from root_000065 WHERE Scvdes LIKE '$estcivUser'";
            $commEstCiv = mysql_query($QueryEstCiv, $conex) or die (mysql_errno()." - en el query: ".$QueryBarr." - ".mysql_error());
            $datoEstCiv = mysql_fetch_assoc($commEstCiv);
            $codEstCiv = $datoEstCiv['Scvcod'];

            if($Idegen == 'Masculino'){$Idegen = 'M';} else{$Idegen = 'F';}

            //INFORMACION GENERAL
            $queryUpd1 = "update".' '."$tablaUser"."_000013 set Fecha_data = '$fechaActual',Hora_data = '$horaActual',Idepas = '$Idepas',
                            Idevis = '$Idevis',Idepvi = '',Ideesc = '$codEstCiv',Ideinc = '$lunaUser',Idempo = '$codMuni',Idebrr = '$codBarr',
                            Idestt = '$estUser',Idesrh = '$tisanUser',Idefnc = '$Idefnc',Idegen = '$Idegen',Idedir = '$Idedir',Idetel = '$telUser',
                            Idecel = '$celUser',Ideeml = '$corUser',Ideext = '$extUser'
                          where Ideuse = '$usuarioMtx'";
            $comUpd1 = mysql_query($queryUpd1, $conex) or die (mysql_errno()." - en el query: ".$queryUpd1." - ".mysql_error());

            //obtenerOpenTab($conex,$wuse,$fechaActual,1);
            //echo 'EL QUERY:'.$queryUpd1;

            /*
            //INFORMACION FAMILIAR
            $Famaco = '0'.$Famaco;

            $queryUpd2 = "update".' '."$tablaUser"."_000019 set Famaco = '$Famaco'
                          where Famuse = '$usuarioMtx'";
            $comUpd2 = mysql_query($queryUpd2, $conex) or die (mysql_errno()." - en el query: ".$queryUpd2." - ".mysql_error());

            echo '<br>'.'EL QUERY 2:'.$queryUpd2;

            ?>
            <!--
            <div id="divAddEst2" align="center" style="margin-top: 80px">
                <input type="hidden" id="funcion" name="funcion" value="insertCredito">
                <button type="submit" class="btn btn-default btn-lg"
                        onclick="window.opener.location.reload(); /*window.close();">
                    <span class="glyphicon glyphicon-ok" aria-hidden="true" style="width: 50px;"></span> Información Actualizada Correctamente
                </button>
            </div>-->
            <?php
        }
    }*/
    ?>
</div>
</body>
</html>