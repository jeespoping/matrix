function openOps(funcion,tablaMtx,usuarioMtx,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,chAlca,chAseo,chEner,chInter,chGas,chTele,credito,chBici,chBus,chCamina,
                 chPart,chMetro,chMoto,chOtroT,chTaxi,chContra,otroTrans,chTrae,chComBoca,chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,
                 chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,barUser,telUser,celUser,corUser,tisanUser,
                 extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,chMedTra,chCrede,chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,
                 chFarTe,chViEpi,chGesAm,contEmer,Ideraz,timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,
                 chTarHob,chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,telEmer,chViArr,chViPag,chAlime,
                 chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,chSalud,chPaCel,chPaTar,chCoTec,chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,chDeMif,chHiEmb,chSeDiv,
                 chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,chAbuNi,chPaMad,chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,
                 chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,chNoFaVi,chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,
                 chPfCah,chPfCuc,chPfTac,chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,
                 chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,chLcViv,chLcVeh,chLcSal,
                 chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,chIaCac,chIaFem,chIaFmu,chIaFvp,chIaOtr,chIaNin,
                 chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,chHoIfi,
                 chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,chQpAmi,chQpMas,
                 chQpSol,chQpFam,chQpAmo,chQpPar,chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,chHhJar,chHhCon,chHhPin,
                 chHhEsc,chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,chBuNdt,chBuOtr,chBuNin)
{
    // definimos la anchura y altura de la ventana
    var altura=314; var anchura=800;
    // calculamos la posicion x e y para centrar la ventana
    var y=parseInt((window.screen.height/2)-(altura/2));
    var x=parseInt((window.screen.width/2)-(anchura/2));
    // mostramos la ventana centrada

    window.open("carEmp_Ops.php?funcion="+funcion.value+'&tablaMtx='+tablaMtx.value+'&usuarioMtx='+usuarioMtx.value+'&Cviviv='+tenVivi.value+
        '&Cvitvi='+tipVivi.value+'&Cvitrz='+tienTerr.value+'&Cvilot='+tienLote.value+'&Cvisvi='+estVivi.value+'&chAcue='+chAcue.value+
        '&chAlca='+chAlca.value+'&chAseo='+chAseo.value+'&chEner='+chEner.value+'&chInter='+chInter.value+'&chGas='+chGas.value+
        '&chTele='+chTele.value+'&credito='+credito.value+'&chBici='+chBici.value+'&chBus='+chBus.value+'&chCamina='+chCamina.value+
        '&chPart='+chPart.value+'&chMetro='+chMetro.value+'&chMoto='+chMoto.value+'&chOtroT='+chOtroT.value+'&chTaxi='+chTaxi.value+
        '&chContra='+chContra.value+'&otroTrans='+otroTrans.value+'&chTrae='+chTrae.value+'&chComBoca='+chComBoca.value+
        '&chComOtros='+chComOtros.value+'&chCasa='+chCasa.value+'&chOtrosAl='+chOtrosAl.value+'&Cvioal='+otroTalmu.value+'&Cvihbb='+hobbie.value+
        '&Otrpar='+lugparq.value+'&Otrlocker='+locker.value+'&chAudit='+chAudit.value+'&chComite='+chComite.value+'&chBrigada='+chBrigada.value+
        '&chOtroRol='+chOtroRol.value+'&otrRol='+cualRol.value+'&Idefnc='+Idefnc.value+'&Idegen='+Idegen.value+'&Ideced='+Ideced.value+
        '&Idepas='+Idepas.value+'&Idevis='+Idevis.value+'&estcivUser='+estcivUser.value+'&Idedir='+Idedir.value+'&lunaUser='+lunaUser.value+
        '&estUser='+estUser.value+'&munUser='+munUser.value+'&barUser='+barUser.value+'&telUser='+telUser.value+'&celUser='+celUser.value+
        '&corUser='+corUser.value+'&tisanUser='+tisanUser.value+'&extUser='+extUser.value+'&chCoCon='+chCoCon.value+'&chGesIn='+chGesIn.value+
        '&chCoDoc='+chCoDoc.value+'&chGesTe='+chGesTe.value+'&chCoInv='+chCoInv.value+'&chHisTe='+chHisTe.value+'&chCoCom='+chCoCom.value+
        '&chInfInt='+chInfInt.value+'&chCopas='+chCopas.value+'&chMedTra='+chMedTra.value+'&chCrede='+chCrede.value+'&chMeCon='+chMeCon.value+
        '&chEtiIn='+chEtiIn.value+'&chMoSe='+chMoSe.value+'&chEtiHo='+chEtiHo.value+'&chSePac='+chSePac.value+'&chEvCal='+chEvCal.value+
        '&chTransp='+chTransp.value+'&chFarTe='+chFarTe.value+'&chViEpi='+chViEpi.value+'&chGesAm='+chGesAm.value+'&contEmer='+contEmer.value+
        '&Ideraz='+Ideraz.value+'&timeDesp='+timeDesp.value+'&turnEmp='+turnEmp.value+'&chTorBol='+chTorBol.value+'&chTorPla='+chTorPla.value+
        '&chTorVol='+chTorVol.value+'&chTorBal='+chTorBal.value+'&chTorTen='+chTorTen.value+'&chCamin='+chCamin.value+'&chBaile='+chBaile.value+
        '&chYoga='+chYoga.value+'&chEnPare='+chEnPare.value+'&chCiclo='+chCiclo.value+'&chMara='+chMara.value+'&chTarHob='+chTarHob.value+
        '&chGruTea='+chGruTea.value+'&chArtPla='+chArtPla.value+'&chManual='+chManual.value+'&chGastro='+chGastro.value+'&chClaIng='+chClaIng.value+
        '&chConPle='+chConPle.value+'&chTarPic='+chTarPic.value+'&chOtrAct='+chOtrAct.value+'&Otractrechor='+diaActi.value+'&Otractcul='+horActi.value+
        '&actExtra='+actExtra.value+'&otraExtra='+otraExtra.value+'&ranSal='+ranSal.value+'&telEmer='+telEmer.value+'&chViArr='+chViArr.value+'&chViPag='+chViPag.value+'&chAlime='+chAlime.value+'&chSerPu='+chSerPu.value+'&chTrpte='+chTrpte.value+
'&chEdPro='+chEdPro.value+'&chEdHij='+chEdHij.value+'&chPaCre='+chPaCre.value+'&chReTli='+chReTli.value+'&chVestu='+chVestu.value+'&chSalud='+chSalud.value+
'&chPaCel='+chPaCel.value+'&chPaTar='+chPaTar.value+'&chCoTec='+chCoTec.value+'&chCuPer='+chCuPer.value+'&chOtGas='+chOtGas.value+'&chDeuSi='+chDeuSi.value+
'&chPCohi='+chPCohi.value+'&chDiEco='+chDiEco.value+'&chDeMif='+chDeMif.value+'&chHiEmb='+chHiEmb.value+'&chSeDiv='+chSeDiv.value+'&chViInt='+chViInt.value+
'&chAdLic='+chAdLic.value+'&chMuSer='+chMuSer.value+'&chEnGra='+chEnGra.value+'&chNiSit='+chNiSit.value+'&posFam='+posFam.value+'&chAbuNi='+chAbuNi.value+
'&chPaMad='+chPaMad.value+'&chVecin='+chVecin.value+'&chGuIns='+chGuIns.value+'&chEmDom='+chEmDom.value+'&chUnFam='+chUnFam.value+'&chQuSol='+chQuSol.value+
'&chCuOtr='+chCuOtr.value+'&subViv='+subViv.value+'&ahoViv='+ahoViv.value+'&montoHa='+montoHa.value+'&chInuVi='+chInuVi.value+'&chContVi='+chContVi.value+
'&chRiAmVi='+chRiAmVi.value+'&chRiEsvi='+chRiEsvi.value+'&chRiSaVi='+chRiSaVi.value+'&chRiPuVi='+chRiPuVi.value+'&chNoFaVi='+chNoFaVi.value+
'&chMeEst='+chMeEst.value+'&chMeMue='+chMeMue.value+'&chMeEle='+chMeEle.value+'&chMePis='+chMePis.value+'&chMePar='+chMePar.value+'&chMeCol='+chMeCol.value+
'&chMeHum='+chMeHum.value+'&chMeFac='+chMeFac.value+'&chMeTec='+chMeTec.value+'&chMeBan='+chMeBan.value+'&chMeCoc='+chMeCoc.value+'&chMeAmp='+chMeAmp.value+
'&chMeNot='+chMeNot.value+'&chPfCah='+chPfCah.value+'&chPfCuc='+chPfCuc.value+'&chPfTac='+chPfTac.value+'&chPfCco='+chPfCco.value+'&chPfChi='+chPfChi.value+
'&chPfCve='+chPfCve.value+'&chPfInv='+chPfInv.value+'&chPfSeg='+chPfSeg.value+'&chPfNin='+chPfNin.value+'&chMcViv='+chMcViv.value+'&chMcTec='+chMcTec.value+
'&chMcMue='+chMcMue.value+'&chMcEle='+chMcEle.value+'&chMcVeh='+chMcVeh.value+'&chMcSal='+chMcSal.value+'&chMcCir='+chMcCir.value+'&chMcTur='+chMcTur.value+
'&chMcLib='+chMcLib.value+'&chMcGah='+chMcGah.value+'&chMcTac='+chMcTac.value+'&chMcEdp='+chMcEdp.value+'&chMcEdf='+chMcEdf.value+'&chMcCem='+chMcCem.value+
'&chMcNin='+chMcNin.value+'&chEpBan='+chEpBan.value+'&chEpFon='+chEpFon.value+'&chEpFmu='+chEpFmu.value+'&chEpPad='+chEpPad.value+'&chEpFam='+chEpFam.value+
'&chEpCal='+chEpCal.value+'&chEpCaj='+chEpCaj.value+'&chEpEla='+chEpEla.value+'&chEpNat='+chEpNat.value+'&chEpOtr='+chEpOtr.value+'&chEpNin='+chEpNin.value+
'&chLcViv='+chLcViv.value+'&chLcVeh='+chLcVeh.value+'&chLcSal='+chLcSal.value+'&chLcCir='+chLcCir.value+'&chLcTur='+chLcTur.value+'&chLcEdf='+chLcEdf.value+
'&chLcEdp='+chLcEdp.value+'&chLcCre='+chLcCre.value+'&chLcMej='+chLcMej.value+'&chLcCro='+chLcCro.value+'&chLcLib='+chLcLib.value+'&chLcTar='+chLcTar.value+
'&chLcNin='+chLcNin.value+'&chIaInv='+chIaInv.value+'&chIaBan='+chIaBan.value+'&chIaNat='+chIaNat.value+'&chIaCac='+chIaCac.value+'&chIaFem='+chIaFem.value+
'&chIaFmu='+chIaFmu.value+'&chIaFvp='+chIaFvp.value+'&chIaOtr='+chIaOtr.value+'&chIaNin='+chIaNin.value+'&chNfCap='+chNfCap.value+'&chNfDes='+chNfDes.value+
'&chNfRel='+chNfRel.value+'&chNfMan='+chNfMan.value+'&chNfFin='+chNfFin.value+'&chNfFor='+chNfFor.value+'&chNfIdi='+chNfIdi.value+'&chNfInf='+chNfInf.value+
'&chNfFco='+chNfFco.value+'&chNfOtr='+chNfOtr.value+'&chNfNot='+chNfNot.value+'&chHoCin='+chHoCin.value+'&chHoDep='+chHoDep.value+'&chHoVid='+chHoVid.value+
'&chHoVte='+chHoVte.value+'&chHoNav='+chHoNav.value+'&chHoIce='+chHoIce.value+'&chHoIpa='+chHoIpa.value+'&chHoIfi='+chHoIfi.value+'&chHoCex='+chHoCex.value+
'&chHoDes='+chHoDes.value+'&chHoJar='+chHoJar.value+'&chHoCon='+chHoCon.value+'&chHoPin='+chHoPin.value+'&chHoEsc='+chHoEsc.value+'&chHoFot='+chHoFot.value+
'&chHoVmu='+chHoVmu.value+'&chHoVbi='+chHoVbi.value+'&chHoEsp='+chHoEsp.value+'&chHoDan='+chHoDan.value+'&chHoTin='+chHoTin.value+'&chHoCoc='+chHoCoc.value+
'&chHoMan='+chHoMan.value+'&chHoOtr='+chHoOtr.value+'&chHoNin='+chHoNin.value+'&chQpHij='+chQpHij.value+'&chQpAmi='+chQpAmi.value+'&chQpMas='+chQpMas.value+
'&chQpSol='+chQpSol.value+'&chQpFam='+chQpFam.value+'&chQpAmo='+chQpAmo.value+'&chQpPar='+chQpPar.value+'&chQpCom='+chQpCom.value+'&chQpOtr='+chQpOtr.value+
'&chHhCin='+chHhCin.value+'&chHhDep='+chHhDep.value+'&chHhVid='+chHhVid.value+'&chHhVte='+chHhVte.value+'&chHhNav='+chHhNav.value+'&chHhIce='+chHhIce.value+
'&chHhIpa='+chHhIpa.value+'&chHhIfi='+chHhIfi.value+'&chHhCex='+chHhCex.value+'&chHhDes='+chHhDes.value+'&chHhJar='+chHhJar.value+'&chHhCon='+chHhCon.value+
'&chHhPin='+chHhPin.value+'&chHhEsc='+chHhEsc.value+'&chHhFot='+chHhFot.value+'&chHhVmu='+chHhVmu.value+'&chHhVbi='+chHhVbi.value+'&chHhEsp='+chHhEsp.value+
'&chHhDan='+chHhDan.value+'&chHhTin='+chHhTin.value+'&chHhCoc='+chHhCoc.value+'&chHhMan='+chHhMan.value+'&chHhOtr='+chHhOtr.value+'&chHhNin='+chHhNin.value+
'&chBuFdi='+chBuFdi.value+'&chBuNcd='+chBuNcd.value+'&chBuDap='+chBuDap.value+'&chBuFmo='+chBuFmo.value+'&chBuNdt='+chBuNdt.value+'&chBuOtr='+chBuOtr.value+
'&chBuNin='+chBuNin.value,
        target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=no,directories=no,resizable=no");
}

function openOps2(funcion,tablaMtx,usuarioMtx,idEstud,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,chAlca,chAseo,chEner,chInter,chGas,chTele,credito,chBici,chBus,
                  chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,chContra,otroTrans,chTrae,chComBoca,chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,
                  chComite,chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,barUser,telUser,celUser,corUser,
                  tisanUser,extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,chMedTra,chCrede,chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,
                  chEvCal,chTransp,chFarTe,chViEpi,chGesAm,contEmer,Ideraz,timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,
                  chCiclo,chMara,chTarHob,chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,telEmer,
                  chViArr,chViPag,chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,chSalud,chPaCel,chPaTar,chCoTec,chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,
                  chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,chAbuNi,chPaMad,chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,
                  montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,chNoFaVi,chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,
                  chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,
                  chMcTur,chMcLib,chMcGah,chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,
                  chLcViv,chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,chIaCac,chIaFem,chIaFmu,
                  chIaFvp,chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,
                  chHoIce,chHoIpa,chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,
                  chQpHij,chQpAmi,chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,
                  chHhJar,chHhCon,chHhPin,chHhEsc,chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,chBuNdt,
                  chBuOtr,chBuNin)
{
    // definimos la anchura y altura de la ventana
    var altura=314;
    var anchura=800;
    // calculamos la posicion x e y para centrar la ventana
    var y=parseInt((window.screen.height/2)-(altura/2));
    var x=parseInt((window.screen.width/2)-(anchura/2));
    // mostramos la ventana centrada

    window.open("carEmp_Ops.php?funcion="+funcion.value+'&tablaMtx='+tablaMtx.value+'&usuarioMtx='+usuarioMtx.value+'&idEstud='+idEstud+'&Cviviv='+tenVivi.value+
        '&Cvitvi='+tipVivi.value+'&Cvitrz='+tienTerr.value+'&Cvilot='+tienLote.value+'&Cvisvi='+estVivi.value+'&chAcue='+chAcue.value+'&chAlca='+chAlca.value+
        '&chAseo='+chAseo.value+'&chEner='+chEner.value+'&chInter='+chInter.value+'&chGas='+chGas.value+'&chTele='+chTele.value+'&credito='+credito.value+
        '&chBici='+chBici.value+'&chBus='+chBus.value+'&chCamina='+chCamina.value+'&chPart='+chPart.value+'&chMetro='+chMetro.value+'&chMoto='+chMoto.value+
        '&chOtroT='+chOtroT.value+'&chTaxi='+chTaxi.value+'&chContra='+chContra.value+'&otroTrans='+otroTrans.value+'&chTrae='+chTrae.value+'&chComBoca='+chComBoca.value+
        '&chComOtros='+chComOtros.value+'&chCasa='+chCasa.value+'&chOtrosAl='+chOtrosAl.value+'&Cvioal='+otroTalmu.value+'&Cvihbb='+hobbie.value+'&Otrpar='+lugparq.value+
        '&Otrlocker='+locker.value+'&chAudit='+chAudit.value+'&chComite='+chComite.value+'&chBrigada='+chBrigada.value+'&chOtroRol='+chOtroRol.value+'&otrRol='+cualRol.value+
        '&Idefnc='+Idefnc.value+'&Idegen='+Idegen.value+'&Ideced='+Ideced.value+'&Idepas='+Idepas.value+'&Idevis='+Idevis.value+'&estcivUser='+estcivUser.value+
        '&Idedir='+Idedir.value+'&lunaUser='+lunaUser.value+'&estUser='+estUser.value+'&munUser='+munUser.value+'&barUser='+barUser.value+'&telUser='+telUser.value+
        '&celUser='+celUser.value+'&corUser='+corUser.value+'&tisanUser='+tisanUser.value+'&extUser='+extUser.value+'&chCoCon='+chCoCon.value+'&chGesIn='+chGesIn.value+
        '&chCoDoc='+chCoDoc.value+'&chGesTe='+chGesTe.value+'&chCoInv='+chCoInv.value+'&chHisTe='+chHisTe.value+'&chCoCom='+chCoCom.value+'&chInfInt='+chInfInt.value+
        '&chCopas='+chCopas.value+'&chMedTra='+chMedTra.value+'&chCrede='+chCrede.value+'&chMeCon='+chMeCon.value+'&chEtiIn='+chEtiIn.value+'&chMoSe='+chMoSe.value+
        '&chEtiHo='+chEtiHo.value+'&chSePac='+chSePac.value+'&chEvCal='+chEvCal.value+'&chTransp='+chTransp.value+'&chFarTe='+chFarTe.value+'&chViEpi='+chViEpi.value+
        '&chGesAm='+chGesAm.value+'&contEmer='+contEmer.value+'&Ideraz='+Ideraz.value+'&timeDesp='+timeDesp.value+'&turnEmp='+turnEmp.value+'&chTorBol='+chTorBol.value+
        '&chTorPla='+chTorPla.value+'&chTorVol='+chTorVol.value+'&chTorBal='+chTorBal.value+'&chTorTen='+chTorTen.value+'&chCamin='+chCamin.value+'&chBaile='+chBaile.value+
        '&chYoga='+chYoga.value+'&chEnPare='+chEnPare.value+'&chCiclo='+chCiclo.value+'&chMara='+chMara.value+'&chTarHob='+chTarHob.value+'&chGruTea='+chGruTea.value+
        '&chArtPla='+chArtPla.value+'&chManual='+chManual.value+'&chGastro='+chGastro.value+'&chClaIng='+chClaIng.value+'&chConPle='+chConPle.value+'&chTarPic='+chTarPic.value+
        '&chOtrAct='+chOtrAct.value+'&Otractrechor='+diaActi.value+'&Otractcul='+horActi.value+'&actExtra='+actExtra.value+'&otraExtra='+otraExtra.value+'&ranSal='+ranSal.value+
        '&telEmer='+telEmer.value+'&chViArr='+chViArr.value+'&chViPag='+chViPag.value+'&chAlime='+chAlime.value+'&chSerPu='+chSerPu.value+'&chTrpte='+chTrpte.value+
        '&chEdPro='+chEdPro.value+'&chEdHij='+chEdHij.value+'&chPaCre='+chPaCre.value+'&chReTli='+chReTli.value+'&chVestu='+chVestu.value+'&chSalud='+chSalud.value+
        '&chPaCel='+chPaCel.value+'&chPaTar='+chPaTar.value+'&chCoTec='+chCoTec.value+'&chCuPer='+chCuPer.value+'&chOtGas='+chOtGas.value+'&chDeuSi='+chDeuSi.value+
        '&chPCohi='+chPCohi.value+'&chDiEco='+chDiEco.value+'&chDeMif='+chDeMif.value+'&chHiEmb='+chHiEmb.value+'&chSeDiv='+chSeDiv.value+'&chViInt='+chViInt.value+
        '&chAdLic='+chAdLic.value+'&chMuSer='+chMuSer.value+'&chEnGra='+chEnGra.value+'&chNiSit='+chNiSit.value+'&posFam='+posFam.value+'&chAbuNi='+chAbuNi.value+
        '&chPaMad='+chPaMad.value+'&chVecin='+chVecin.value+'&chGuIns='+chGuIns.value+'&chEmDom='+chEmDom.value+'&chUnFam='+chUnFam.value+'&chQuSol='+chQuSol.value+
        '&chCuOtr='+chCuOtr.value+'&subViv='+subViv.value+'&ahoViv='+ahoViv.value+'&montoHa='+montoHa.value+'&chInuVi='+chInuVi.value+'&chContVi='+chContVi.value+
        '&chRiAmVi='+chRiAmVi.value+'&chRiEsvi='+chRiEsvi.value+'&chRiSaVi='+chRiSaVi.value+'&chRiPuVi='+chRiPuVi.value+'&chNoFaVi='+chNoFaVi.value+
        '&chMeEst='+chMeEst.value+'&chMeMue='+chMeMue.value+'&chMeEle='+chMeEle.value+'&chMePis='+chMePis.value+'&chMePar='+chMePar.value+'&chMeCol='+chMeCol.value+
        '&chMeHum='+chMeHum.value+'&chMeFac='+chMeFac.value+'&chMeTec='+chMeTec.value+'&chMeBan='+chMeBan.value+'&chMeCoc='+chMeCoc.value+'&chMeAmp='+chMeAmp.value+
        '&chMeNot='+chMeNot.value+'&chPfCah='+chPfCah.value+'&chPfCuc='+chPfCuc.value+'&chPfTac='+chPfTac.value+'&chPfCco='+chPfCco.value+'&chPfChi='+chPfChi.value+
        '&chPfCve='+chPfCve.value+'&chPfInv='+chPfInv.value+'&chPfSeg='+chPfSeg.value+'&chPfNin='+chPfNin.value+'&chMcViv='+chMcViv.value+'&chMcTec='+chMcTec.value+
        '&chMcMue='+chMcMue.value+'&chMcEle='+chMcEle.value+'&chMcVeh='+chMcVeh.value+'&chMcSal='+chMcSal.value+'&chMcCir='+chMcCir.value+'&chMcTur='+chMcTur.value+
        '&chMcLib='+chMcLib.value+'&chMcGah='+chMcGah.value+'&chMcTac='+chMcTac.value+'&chMcEdp='+chMcEdp.value+'&chMcEdf='+chMcEdf.value+'&chMcCem='+chMcCem.value+
        '&chMcNin='+chMcNin.value+'&chEpBan='+chEpBan.value+'&chEpFon='+chEpFon.value+'&chEpFmu='+chEpFmu.value+'&chEpPad='+chEpPad.value+'&chEpFam='+chEpFam.value+
        '&chEpCal='+chEpCal.value+'&chEpCaj='+chEpCaj.value+'&chEpEla='+chEpEla.value+'&chEpNat='+chEpNat.value+'&chEpOtr='+chEpOtr.value+'&chEpNin='+chEpNin.value+
        '&chLcViv='+chLcViv.value+'&chLcVeh='+chLcVeh.value+'&chLcSal='+chLcSal.value+'&chLcCir='+chLcCir.value+'&chLcTur='+chLcTur.value+'&chLcEdf='+chLcEdf.value+
        '&chLcEdp='+chLcEdp.value+'&chLcCre='+chLcCre.value+'&chLcMej='+chLcMej.value+'&chLcCro='+chLcCro.value+'&chLcLib='+chLcLib.value+'&chLcTar='+chLcTar.value+
        '&chLcNin='+chLcNin.value+'&chIaInv='+chIaInv.value+'&chIaBan='+chIaBan.value+'&chIaNat='+chIaNat.value+'&chIaCac='+chIaCac.value+'&chIaFem='+chIaFem.value+
        '&chIaFmu='+chIaFmu.value+'&chIaFvp='+chIaFvp.value+'&chIaOtr='+chIaOtr.value+'&chIaNin='+chIaNin.value+'&chNfCap='+chNfCap.value+'&chNfDes='+chNfDes.value+
        '&chNfRel='+chNfRel.value+'&chNfMan='+chNfMan.value+'&chNfFin='+chNfFin.value+'&chNfFor='+chNfFor.value+'&chNfIdi='+chNfIdi.value+'&chNfInf='+chNfInf.value+
        '&chNfFco='+chNfFco.value+'&chNfOtr='+chNfOtr.value+'&chNfNot='+chNfNot.value+'&chHoCin='+chHoCin.value+'&chHoDep='+chHoDep.value+'&chHoVid='+chHoVid.value+
        '&chHoVte='+chHoVte.value+'&chHoNav='+chHoNav.value+'&chHoIce='+chHoIce.value+'&chHoIpa='+chHoIpa.value+'&chHoIfi='+chHoIfi.value+'&chHoCex='+chHoCex.value+
        '&chHoDes='+chHoDes.value+'&chHoJar='+chHoJar.value+'&chHoCon='+chHoCon.value+'&chHoPin='+chHoPin.value+'&chHoEsc='+chHoEsc.value+'&chHoFot='+chHoFot.value+
        '&chHoVmu='+chHoVmu.value+'&chHoVbi='+chHoVbi.value+'&chHoEsp='+chHoEsp.value+'&chHoDan='+chHoDan.value+'&chHoTin='+chHoTin.value+'&chHoCoc='+chHoCoc.value+
        '&chHoMan='+chHoMan.value+'&chHoOtr='+chHoOtr.value+'&chHoNin='+chHoNin.value+'&chQpHij='+chQpHij.value+'&chQpAmi='+chQpAmi.value+'&chQpMas='+chQpMas.value+
        '&chQpSol='+chQpSol.value+'&chQpFam='+chQpFam.value+'&chQpAmo='+chQpAmo.value+'&chQpPar='+chQpPar.value+'&chQpCom='+chQpCom.value+'&chQpOtr='+chQpOtr.value+
        '&chHhCin='+chHhCin.value+'&chHhDep='+chHhDep.value+'&chHhVid='+chHhVid.value+'&chHhVte='+chHhVte.value+'&chHhNav='+chHhNav.value+'&chHhIce='+chHhIce.value+
        '&chHhIpa='+chHhIpa.value+'&chHhIfi='+chHhIfi.value+'&chHhCex='+chHhCex.value+'&chHhDes='+chHhDes.value+'&chHhJar='+chHhJar.value+'&chHhCon='+chHhCon.value+
        '&chHhPin='+chHhPin.value+'&chHhEsc='+chHhEsc.value+'&chHhFot='+chHhFot.value+'&chHhVmu='+chHhVmu.value+'&chHhVbi='+chHhVbi.value+'&chHhEsp='+chHhEsp.value+
        '&chHhDan='+chHhDan.value+'&chHhTin='+chHhTin.value+'&chHhCoc='+chHhCoc.value+'&chHhMan='+chHhMan.value+'&chHhOtr='+chHhOtr.value+'&chHhNin='+chHhNin.value+
        '&chBuFdi='+chBuFdi.value+'&chBuNcd='+chBuNcd.value+'&chBuDap='+chBuDap.value+'&chBuFmo='+chBuFmo.value+'&chBuNdt='+chBuNdt.value+'&chBuOtr='+chBuOtr.value+
        '&chBuNin='+chBuNin.value,
        target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=no,directories=no,resizable=no");
}

function saveDatFam(funcion,tablaMtx,usuarioMtx,variable)
{
    // definimos la anchura y altura de la ventana
    var altura=10;  var anchura=10;
    // calculamos la posicion x e y para centrar la ventana
    var y=parseInt((window.screen.height/2)-(altura/2));
    var x=parseInt((window.screen.width/2)-(anchura/2));
    // mostramos la ventana centrada

    window.open("carEmp_Ops.php?funcion="+funcion+'&tablaMtx='+tablaMtx.value+'&usuarioMtx='+usuarioMtx.value+'&variable='+variable,
    target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=no,directories=no,resizable=no");
}

function saveVivi(funcion,tablaMtx,usuarioMtx,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,chAlca,chAseo,chEner,chInter,chGas,chTele,credito,chBici,chBus,chCamina,
                  chPart,chMetro,chMoto,chOtroT,chTaxi,chContra,otroTrans,chTrae,chComBoca,chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,
                  chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,barUser,telUser,celUser,corUser,tisanUser,
                  extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,chMedTra,chCrede,chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,
                  chTransp,chFarTe,chViEpi,chGesAm,contEmer,Ideraz,timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,
                  chMara,chTarHob,chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,telEmer,chViArr,
                  chViPag,chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,chSalud,chPaCel,chPaTar,chCoTec,chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,chDeMif,
                  chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,chAbuNi,chPaMad,chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,
                  chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,chNoFaVi,chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,
                  chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,
                  chMcLib,chMcGah,chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,chLcViv,
                  chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,chIaCac,chIaFem,chIaFmu,chIaFvp,
                  chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,
                  chHoIpa,chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,
                  chQpAmi,chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,chHhJar,
                  chHhCon,chHhPin,chHhEsc,chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,chBuNdt,chBuOtr,
                  chBuNin)
{
    // definimos la anchura y altura de la ventana
    var altura=314;  var anchura=800;
    // calculamos la posicion x e y para centrar la ventana
    var y=parseInt((window.screen.height/2)-(altura/2));
    var x=parseInt((window.screen.width/2)-(anchura/2));
    // mostramos la ventana centrada

    window.open("carEmp_Ops.php?funcion="+funcion.value+'&tablaMtx='+tablaMtx.value+'&usuarioMtx='+usuarioMtx.value+'&Cviviv='+tenVivi.value+'&Cvitvi='+tipVivi.value+
        '&Cvitrz='+tienTerr.value+'&Cvilot='+tienLote.value+'&Cvisvi='+estVivi.value+'&chAcue='+chAcue.value+'&chAlca='+chAlca.value+'&chAseo='+chAseo.value+
        '&chEner='+chEner.value+'&chInter='+chInter.value+'&chGas='+chGas.value+'&chTele='+chTele.value+'&credito='+credito.value+'&chBici='+chBici.value+
        '&chBus='+chBus.value+'&chCamina='+chCamina.value+'&chPart='+chPart.value+'&chMetro='+chMetro.value+'&chMoto='+chMoto.value+'&chOtroT='+chOtroT.value+
        '&chTaxi='+chTaxi.value+'&chContra='+chContra.value+'&otroTrans='+otroTrans.value+'&chTrae='+chTrae.value+'&chComBoca='+chComBoca.value+
        '&chComOtros='+chComOtros.value+'&chCasa='+chCasa.value+'&chOtrosAl='+chOtrosAl.value+'&Cvioal='+otroTalmu.value+'&Cvihbb='+hobbie.value+
        '&Otrpar='+lugparq.value+'&Otrlocker='+locker.value+'&chAudit='+chAudit.value+'&chComite='+chComite.value+'&chBrigada='+chBrigada.value+
        '&chOtroRol='+chOtroRol.value+'&otrRol='+cualRol.value+'&Idefnc='+Idefnc.value+'&Idegen='+Idegen.value+'&Ideced='+Ideced.value+'&Idepas='+Idepas.value+
        '&Idevis='+Idevis.value+'&estcivUser='+estcivUser.value+'&Idedir='+Idedir.value+'&lunaUser='+lunaUser.value+'&estUser='+estUser.value+'&munUser='+munUser.value+
        '&barUser='+barUser.value+'&telUser='+telUser.value+'&celUser='+celUser.value+'&corUser='+corUser.value+'&tisanUser='+tisanUser.value+'&extUser='+extUser.value+
        '&chCoCon='+chCoCon.value+'&chGesIn='+chGesIn.value+'&chCoDoc='+chCoDoc.value+'&chGesTe='+chGesTe.value+'&chCoInv='+chCoInv.value+'&chHisTe='+chHisTe.value+
        '&chCoCom='+chCoCom.value+'&chInfInt='+chInfInt.value+'&chCopas='+chCopas.value+'&chMedTra='+chMedTra.value+'&chCrede='+chCrede.value+'&chMeCon='+chMeCon.value+
        '&chEtiIn='+chEtiIn.value+'&chMoSe='+chMoSe.value+'&chEtiHo='+chEtiHo.value+'&chSePac='+chSePac.value+'&chEvCal='+chEvCal.value+'&chTransp='+chTransp.value+
        '&chFarTe='+chFarTe.value+'&chViEpi='+chViEpi.value+'&chGesAm='+chGesAm.value+'&contEmer='+contEmer.value+'&Ideraz='+Ideraz.value+'&timeDesp='+timeDesp.value+
        '&turnEmp='+turnEmp.value+'&chTorBol='+chTorBol.value+'&chTorPla='+chTorPla.value+'&chTorVol='+chTorVol.value+'&chTorBal='+chTorBal.value+
        '&chTorTen='+chTorTen.value+'&chCamin='+chCamin.value+'&chBaile='+chBaile.value+'&chYoga='+chYoga.value+'&chEnPare='+chEnPare.value+'&chCiclo='+chCiclo.value+
        '&chMara='+chMara.value+'&chTarHob='+chTarHob.value+'&chGruTea='+chGruTea.value+'&chArtPla='+chArtPla.value+'&chManual='+chManual.value+
        '&chGastro='+chGastro.value+'&chClaIng='+chClaIng.value+'&chConPle='+chConPle.value+'&chTarPic='+chTarPic.value+'&chOtrAct='+chOtrAct.value+
        '&Otractrechor='+diaActi.value+'&Otractcul='+horActi.value+'&actExtra='+actExtra.value+'&otraExtra='+otraExtra.value+'&ranSal='+ranSal.value+
        '&telEmer='+telEmer.value+'&chViArr='+chViArr.value+'&chViPag='+chViPag.value+'&chAlime='+chAlime.value+'&chSerPu='+chSerPu.value+'&chTrpte='+chTrpte.value+
        '&chEdPro='+chEdPro.value+'&chEdHij='+chEdHij.value+'&chPaCre='+chPaCre.value+'&chReTli='+chReTli.value+'&chVestu='+chVestu.value+'&chSalud='+chSalud.value+
        '&chPaCel='+chPaCel.value+'&chPaTar='+chPaTar.value+'&chCoTec='+chCoTec.value+'&chCuPer='+chCuPer.value+'&chOtGas='+chOtGas.value+'&chDeuSi='+chDeuSi.value+
        '&chPCohi='+chPCohi.value+'&chDiEco='+chDiEco.value+'&chDeMif='+chDeMif.value+'&chHiEmb='+chHiEmb.value+'&chSeDiv='+chSeDiv.value+'&chViInt='+chViInt.value+
        '&chAdLic='+chAdLic.value+'&chMuSer='+chMuSer.value+'&chEnGra='+chEnGra.value+'&chNiSit='+chNiSit.value+'&posFam='+posFam.value+'&chAbuNi='+chAbuNi.value+
        '&chPaMad='+chPaMad.value+'&chVecin='+chVecin.value+'&chGuIns='+chGuIns.value+'&chEmDom='+chEmDom.value+'&chUnFam='+chUnFam.value+'&chQuSol='+chQuSol.value+
        '&chCuOtr='+chCuOtr.value+'&subViv='+subViv.value+'&ahoViv='+ahoViv.value+'&montoHa='+montoHa.value+'&chInuVi='+chInuVi.value+'&chContVi='+chContVi.value+
        '&chRiAmVi='+chRiAmVi.value+'&chRiEsvi='+chRiEsvi.value+'&chRiSaVi='+chRiSaVi.value+'&chRiPuVi='+chRiPuVi.value+'&chNoFaVi='+chNoFaVi.value+
        '&chMeEst='+chMeEst.value+'&chMeMue='+chMeMue.value+'&chMeEle='+chMeEle.value+'&chMePis='+chMePis.value+'&chMePar='+chMePar.value+'&chMeCol='+chMeCol.value+
        '&chMeHum='+chMeHum.value+'&chMeFac='+chMeFac.value+'&chMeTec='+chMeTec.value+'&chMeBan='+chMeBan.value+'&chMeCoc='+chMeCoc.value+'&chMeAmp='+chMeAmp.value+
        '&chMeNot='+chMeNot.value+'&chPfCah='+chPfCah.value+'&chPfCuc='+chPfCuc.value+'&chPfTac='+chPfTac.value+'&chPfCco='+chPfCco.value+'&chPfChi='+chPfChi.value+
        '&chPfCve='+chPfCve.value+'&chPfInv='+chPfInv.value+'&chPfSeg='+chPfSeg.value+'&chPfNin='+chPfNin.value+'&chMcViv='+chMcViv.value+'&chMcTec='+chMcTec.value+
        '&chMcMue='+chMcMue.value+'&chMcEle='+chMcEle.value+'&chMcVeh='+chMcVeh.value+'&chMcSal='+chMcSal.value+'&chMcCir='+chMcCir.value+'&chMcTur='+chMcTur.value+
        '&chMcLib='+chMcLib.value+'&chMcGah='+chMcGah.value+'&chMcTac='+chMcTac.value+'&chMcEdp='+chMcEdp.value+'&chMcEdf='+chMcEdf.value+'&chMcCem='+chMcCem.value+
        '&chMcNin='+chMcNin.value+'&chEpBan='+chEpBan.value+'&chEpFon='+chEpFon.value+'&chEpFmu='+chEpFmu.value+'&chEpPad='+chEpPad.value+'&chEpFam='+chEpFam.value+
        '&chEpCal='+chEpCal.value+'&chEpCaj='+chEpCaj.value+'&chEpEla='+chEpEla.value+'&chEpNat='+chEpNat.value+'&chEpOtr='+chEpOtr.value+'&chEpNin='+chEpNin.value+
        '&chLcViv='+chLcViv.value+'&chLcVeh='+chLcVeh.value+'&chLcSal='+chLcSal.value+'&chLcCir='+chLcCir.value+'&chLcTur='+chLcTur.value+'&chLcEdf='+chLcEdf.value+
        '&chLcEdp='+chLcEdp.value+'&chLcCre='+chLcCre.value+'&chLcMej='+chLcMej.value+'&chLcCro='+chLcCro.value+'&chLcLib='+chLcLib.value+'&chLcTar='+chLcTar.value+
        '&chLcNin='+chLcNin.value+'&chIaInv='+chIaInv.value+'&chIaBan='+chIaBan.value+'&chIaNat='+chIaNat.value+'&chIaCac='+chIaCac.value+'&chIaFem='+chIaFem.value+
        '&chIaFmu='+chIaFmu.value+'&chIaFvp='+chIaFvp.value+'&chIaOtr='+chIaOtr.value+'&chIaNin='+chIaNin.value+'&chNfCap='+chNfCap.value+'&chNfDes='+chNfDes.value+
        '&chNfRel='+chNfRel.value+'&chNfMan='+chNfMan.value+'&chNfFin='+chNfFin.value+'&chNfFor='+chNfFor.value+'&chNfIdi='+chNfIdi.value+'&chNfInf='+chNfInf.value+
        '&chNfFco='+chNfFco.value+'&chNfOtr='+chNfOtr.value+'&chNfNot='+chNfNot.value+'&chHoCin='+chHoCin.value+'&chHoDep='+chHoDep.value+'&chHoVid='+chHoVid.value+
        '&chHoVte='+chHoVte.value+'&chHoNav='+chHoNav.value+'&chHoIce='+chHoIce.value+'&chHoIpa='+chHoIpa.value+'&chHoIfi='+chHoIfi.value+'&chHoCex='+chHoCex.value+
        '&chHoDes='+chHoDes.value+'&chHoJar='+chHoJar.value+'&chHoCon='+chHoCon.value+'&chHoPin='+chHoPin.value+'&chHoEsc='+chHoEsc.value+'&chHoFot='+chHoFot.value+
        '&chHoVmu='+chHoVmu.value+'&chHoVbi='+chHoVbi.value+'&chHoEsp='+chHoEsp.value+'&chHoDan='+chHoDan.value+'&chHoTin='+chHoTin.value+'&chHoCoc='+chHoCoc.value+
        '&chHoMan='+chHoMan.value+'&chHoOtr='+chHoOtr.value+'&chHoNin='+chHoNin.value+'&chQpHij='+chQpHij.value+'&chQpAmi='+chQpAmi.value+'&chQpMas='+chQpMas.value+
        '&chQpSol='+chQpSol.value+'&chQpFam='+chQpFam.value+'&chQpAmo='+chQpAmo.value+'&chQpPar='+chQpPar.value+'&chQpCom='+chQpCom.value+'&chQpOtr='+chQpOtr.value+
        '&chHhCin='+chHhCin.value+'&chHhDep='+chHhDep.value+'&chHhVid='+chHhVid.value+'&chHhVte='+chHhVte.value+'&chHhNav='+chHhNav.value+'&chHhIce='+chHhIce.value+
        '&chHhIpa='+chHhIpa.value+'&chHhIfi='+chHhIfi.value+'&chHhCex='+chHhCex.value+'&chHhDes='+chHhDes.value+'&chHhJar='+chHhJar.value+'&chHhCon='+chHhCon.value+
        '&chHhPin='+chHhPin.value+'&chHhEsc='+chHhEsc.value+'&chHhFot='+chHhFot.value+'&chHhVmu='+chHhVmu.value+'&chHhVbi='+chHhVbi.value+'&chHhEsp='+chHhEsp.value+
        '&chHhDan='+chHhDan.value+'&chHhTin='+chHhTin.value+'&chHhCoc='+chHhCoc.value+'&chHhMan='+chHhMan.value+'&chHhOtr='+chHhOtr.value+'&chHhNin='+chHhNin.value+
        '&chBuFdi='+chBuFdi.value+'&chBuNcd='+chBuNcd.value+'&chBuDap='+chBuDap.value+'&chBuFmo='+chBuFmo.value+'&chBuNdt='+chBuNdt.value+'&chBuOtr='+chBuOtr.value+
        '&chBuNin='+chBuNin.value,
        target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=no,directories=no,resizable=no");
}

function chValues(valor,target)
{
    if(valor === true)
    {
        document.getElementById(target).readOnly = false;
    }
    if(valor === false)
    {
        document.getElementById(target).readOnly = true;
        document.getElementById(target).value = '';
    }
}

function basicData(funcion,codUsuario)
{
    // tamaño de la ventana
    var altura=314;  var anchura=800;
    // calculamos la posicion x e y para centrar la ventana
    var y=parseInt((window.screen.height/2)-(altura/2));
    var x=parseInt((window.screen.width/2)-(anchura/2));
    // mostramos la ventana centrada

    window.open("carEmp_Ops.php?funcion="+funcion+'&codUsuario='+codUsuario.value,
        target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=no,directories=no,resizable=no")

}

function contOp(campo)
{
    var op1 = document.getElementById(campo).value;
    if(op1 == 'on'){document.getElementById(campo).value = 'off'}
    if(op1 == 'off'){document.getElementById(campo).value = 'on'}
}

function sumActi(campo)
{
    if(document.getElementById('chTorBol').value == 'on'){opt1 = 1} else{opt1 = 0}  if(document.getElementById('chTorPla').value == 'on'){opt2 = 1} else{opt2 = 0}
    if(document.getElementById('chTorVol').value == 'on'){opt3 = 1} else{opt3 = 0}  if(document.getElementById('chTorBal').value == 'on'){opt4 = 1} else{opt4 = 0}
    if(document.getElementById('chTorTen').value == 'on'){opt5 = 1} else{opt5 = 0}  if(document.getElementById('chCamin').value == 'on'){opt6 = 1} else{opt6 = 0}
    if(document.getElementById('chBaile').value == 'on'){opt7 = 1} else{opt7 = 0}   if(document.getElementById('chYoga').value == 'on'){opt8 = 1} else{opt8 = 0}
    if(document.getElementById('chEnPare').value == 'on'){opt9 = 1} else{opt9 = 0}  if(document.getElementById('chCiclo').value == 'on'){opt10 = 1} else{opt10 = 0}
    if(document.getElementById('chMara').value == 'on'){opt11 = 1} else{opt11 = 0}  if(document.getElementById('chTarHob').value == 'on'){opt12 = 1} else{opt12 = 0}
    if(document.getElementById('chGruTea').value == 'on'){opt13 = 1} else{opt13 = 0}if(document.getElementById('chArtPla').value == 'on'){opt14 = 1} else{opt14 = 0}
    if(document.getElementById('chManual').value == 'on'){opt15 = 1} else{opt15 = 0}if(document.getElementById('chGastro').value == 'on'){opt16 = 1} else{opt16 = 0}
    if(document.getElementById('chClaIng').value == 'on'){opt17 = 1} else{opt17 = 0}if(document.getElementById('chConPle').value == 'on'){opt18 = 1} else{opt18 = 0}
    if(document.getElementById('chTarPic').value == 'on'){opt19 = 1} else{opt19 = 0}if(document.getElementById('chOtrAct').value == 'on'){opt20 = 1} else{opt20 = 0}

    var totActi = opt1+opt2+opt3+opt4+opt5+opt6+opt7+opt8+opt9+opt10+opt11+opt12+opt13+opt14+opt15+opt16+opt17+opt18+opt19+opt20;

    if(totActi > 3)
    {
        alert('SOLO PUEDE SELECCIONAR 3 OPCIONES');
        document.getElementById(campo).checked = false;
        document.getElementById(campo).value = 'off';
    }
}

function sumActi3(campo)
{
    if(document.getElementById('chEpBan').value == 'on'){opt1 = 1} else{opt1 = 0}   if(document.getElementById('chEpFon').value == 'on'){opt2 = 1} else{opt2 = 0}
    if(document.getElementById('chEpFmu').value == 'on'){opt3 = 1} else{opt3 = 0}   if(document.getElementById('chEpPad').value == 'on'){opt4 = 1} else{opt4 = 0}
    if(document.getElementById('chEpFam').value == 'on'){opt5 = 1} else{opt5 = 0}   if(document.getElementById('chEpCal').value == 'on'){opt6 = 1} else{opt6 = 0}
    if(document.getElementById('chEpCaj').value == 'on'){opt7 = 1} else{opt7 = 0}   if(document.getElementById('chEpEla').value == 'on'){opt8 = 1} else{opt8 = 0}
    if(document.getElementById('chEpNat').value == 'on'){opt9 = 1} else{opt9 = 0}   if(document.getElementById('chEpOtr').value == 'on'){opt10 = 1} else{opt10 = 0}

    var totActi = opt1+opt2+opt3+opt4+opt5+opt6+opt7+opt8+opt9+opt10;

    if(totActi > 3)
    {
        alert('SOLO PUEDE SELECCIONAR 3 OPCIONES');
        document.getElementById(campo).checked = false;
        document.getElementById(campo).value = 'off';
    }
}

function sumActi4(campo)
{
    if(document.getElementById('chLcViv').value == 'on'){opt1 = 1} else{opt1 = 0}   if(document.getElementById('chLcVeh').value == 'on'){opt2 = 1} else{opt2 = 0}
    if(document.getElementById('chLcSal').value == 'on'){opt3 = 1} else{opt3 = 0}   if(document.getElementById('chLcCir').value == 'on'){opt4 = 1} else{opt4 = 0}
    if(document.getElementById('chLcTur').value == 'on'){opt5 = 1} else{opt5 = 0}   if(document.getElementById('chLcEdf').value == 'on'){opt6 = 1} else{opt6 = 0}
    if(document.getElementById('chLcEdp').value == 'on'){opt7 = 1} else{opt7 = 0}   if(document.getElementById('chLcCre').value == 'on'){opt8 = 1} else{opt8 = 0}
    if(document.getElementById('chLcMej').value == 'on'){opt9 = 1} else{opt9 = 0}   if(document.getElementById('chLcCro').value == 'on'){opt10 = 1} else{opt10 = 0}
    if(document.getElementById('chLcLib').value == 'on'){opt11 = 1} else{opt11 = 0}   if(document.getElementById('chLcTar').value == 'on'){opt12 = 1} else{opt12 = 0}

    var totActi = opt1+opt2+opt3+opt4+opt5+opt6+opt7+opt8+opt9+opt10+opt11+opt12;

    if(totActi > 2)
    {
        alert('SOLO PUEDE SELECCIONAR 2 OPCIONES');
        document.getElementById(campo).checked = false;
        document.getElementById(campo).value = 'off';
    }
}

function sumActi5(campo)
{
    if(document.getElementById('chHoCin').value == 'on'){opt1 = 1} else{opt1 = 0}   if(document.getElementById('chHoDep').value == 'on'){opt2 = 1} else{opt2 = 0}
    if(document.getElementById('chHoVid').value == 'on'){opt3 = 1} else{opt3 = 0}   if(document.getElementById('chHoVte').value == 'on'){opt4 = 1} else{opt4 = 0}
    if(document.getElementById('chHoNav').value == 'on'){opt5 = 1} else{opt5 = 0}   if(document.getElementById('chHoIce').value == 'on'){opt6 = 1} else{opt6 = 0}
    if(document.getElementById('chHoIpa').value == 'on'){opt7 = 1} else{opt7 = 0}   if(document.getElementById('chHoIfi').value == 'on'){opt8 = 1} else{opt8 = 0}
    if(document.getElementById('chHoCex').value == 'on'){opt9 = 1} else{opt9 = 0}   if(document.getElementById('chHoDes').value == 'on'){opt10 = 1} else{opt10 = 0}
    if(document.getElementById('chHoJar').value == 'on'){opt11 = 1} else{opt11 = 0} if(document.getElementById('chHoCon').value == 'on'){opt12 = 1} else{opt12 = 0}
    if(document.getElementById('chHoPin').value == 'on'){opt13 = 1} else{opt13 = 0} if(document.getElementById('chHoEsc').value == 'on'){opt14 = 1} else{opt14 = 0}
    if(document.getElementById('chHoFot').value == 'on'){opt15 = 1} else{opt15 = 0} if(document.getElementById('chHoVmu').value == 'on'){opt16 = 1} else{opt16 = 0}
    if(document.getElementById('chHoVbi').value == 'on'){opt17 = 1} else{opt17 = 0} if(document.getElementById('chHoEsp').value == 'on'){opt18 = 1} else{opt18 = 0}
    if(document.getElementById('chHoDan').value == 'on'){opt19 = 1} else{opt19 = 0} if(document.getElementById('chHoTin').value == 'on'){opt20 = 1} else{opt20 = 0}
    if(document.getElementById('chHoCoc').value == 'on'){opt21 = 1} else{opt21 = 0} if(document.getElementById('chHoMan').value == 'on'){opt22 = 1} else{opt22 = 0}
    if(document.getElementById('chHoOtr').value == 'on'){opt23 = 1} else{opt23 = 0}

    var totActi = opt1+opt2+opt3+opt4+opt5+opt6+opt7+opt8+opt9+opt10+opt11+opt12+opt13+opt14+opt15+opt16+opt17+opt18+opt19+opt20+opt21+opt22+opt23;

    if(totActi > 5)
    {
        alert('SOLO PUEDE SELECCIONAR 5 OPCIONES');
        document.getElementById(campo).checked = false;
        document.getElementById(campo).value = 'off';
    }
}

function sumActi6(campo)
{
    if(document.getElementById('chQpHij').value == 'on'){opt1 = 1} else{opt1 = 0}   if(document.getElementById('chQpAmi').value == 'on'){opt2 = 1} else{opt2 = 0}
    if(document.getElementById('chQpMas').value == 'on'){opt3 = 1} else{opt3 = 0}   if(document.getElementById('chQpSol').value == 'on'){opt4 = 1} else{opt4 = 0}
    if(document.getElementById('chQpFam').value == 'on'){opt5 = 1} else{opt5 = 0}   if(document.getElementById('chQpAmo').value == 'on'){opt6 = 1} else{opt6 = 0}
    if(document.getElementById('chQpPar').value == 'on'){opt7 = 1} else{opt7 = 0}   if(document.getElementById('chQpCom').value == 'on'){opt8 = 1} else{opt8 = 0}
    if(document.getElementById('chQpOtr').value == 'on'){opt9 = 1} else{opt9 = 0}

    var totActi = opt1+opt2+opt3+opt4+opt5+opt6+opt7+opt8+opt9;

    if(totActi > 3)
    {
        alert('SOLO PUEDE SELECCIONAR 3 OPCIONES');
        document.getElementById(campo).checked = false;
        document.getElementById(campo).value = 'off';
    }
}

function sumActi7(campo)
{
    if(document.getElementById('chHhCin').value == 'on'){opt1 = 1} else{opt1 = 0}   if(document.getElementById('chHhDep').value == 'on'){opt2 = 1} else{opt2 = 0}
    if(document.getElementById('chHhVid').value == 'on'){opt3 = 1} else{opt3 = 0}   if(document.getElementById('chHhVte').value == 'on'){opt4 = 1} else{opt4 = 0}
    if(document.getElementById('chHhNav').value == 'on'){opt5 = 1} else{opt5 = 0}   if(document.getElementById('chHhIce').value == 'on'){opt6 = 1} else{opt6 = 0}
    if(document.getElementById('chHhIpa').value == 'on'){opt7 = 1} else{opt7 = 0}   if(document.getElementById('chHhIfi').value == 'on'){opt8 = 1} else{opt8 = 0}
    if(document.getElementById('chHhCex').value == 'on'){opt9 = 1} else{opt9 = 0}   if(document.getElementById('chHhDes').value == 'on'){opt10 = 1} else{opt10 = 0}
    if(document.getElementById('chHhJar').value == 'on'){opt11 = 1} else{opt11 = 0} if(document.getElementById('chHhCon').value == 'on'){opt12 = 1} else{opt12 = 0}
    if(document.getElementById('chHhPin').value == 'on'){opt13 = 1} else{opt13 = 0} if(document.getElementById('chHhEsc').value == 'on'){opt14 = 1} else{opt14 = 0}
    if(document.getElementById('chHhFot').value == 'on'){opt15 = 1} else{opt15 = 0} if(document.getElementById('chHhVmu').value == 'on'){opt16 = 1} else{opt16 = 0}
    if(document.getElementById('chHhVbi').value == 'on'){opt17 = 1} else{opt17 = 0} if(document.getElementById('chHhEsp').value == 'on'){opt18 = 1} else{opt18 = 0}
    if(document.getElementById('chHhDan').value == 'on'){opt19 = 1} else{opt19 = 0} if(document.getElementById('chHhTin').value == 'on'){opt20 = 1} else{opt20 = 0}
    if(document.getElementById('chHhCoc').value == 'on'){opt21 = 1} else{opt21 = 0} if(document.getElementById('chHhMan').value == 'on'){opt22 = 1} else{opt22 = 0}
    if(document.getElementById('chHhOtr').value == 'on'){opt23 = 1} else{opt23 = 0}

    var totActi = opt1+opt2+opt3+opt4+opt5+opt6+opt7+opt8+opt9+opt10+opt11+opt12+opt13+opt14+opt15+opt16+opt17+opt18+opt19+opt20+opt21+opt22+opt23;

    if(totActi > 5)
    {
        alert('SOLO PUEDE SELECCIONAR 5 OPCIONES');
        document.getElementById(campo).checked = false;
        document.getElementById(campo).value = 'off';
    }
}

function sumActi8(campo)
{
    if(document.getElementById('chBuFdi').value == 'on'){opt1 = 1} else{opt1 = 0}   if(document.getElementById('chBuNcd').value == 'on'){opt2 = 1} else{opt2 = 0}
    if(document.getElementById('chBuDap').value == 'on'){opt3 = 1} else{opt3 = 0}   if(document.getElementById('chBuFmo').value == 'on'){opt4 = 1} else{opt4 = 0}
    if(document.getElementById('chBuNdt').value == 'on'){opt5 = 1} else{opt5 = 0}   if(document.getElementById('chBuOtr').value == 'on'){opt6 = 1} else{opt6 = 0}

    var totActi = opt1+opt2+opt3+opt4+opt5+opt6;

    if(totActi > 3)
    {
        alert('SOLO PUEDE SELECCIONAR 3 OPCIONES');
        document.getElementById(campo).checked = false;
        document.getElementById(campo).value = 'off';
    }
}

function uncheck1()
{
    var val1 = document.getElementById('chMeNot').value;
    if(val1 == 'on')
    {
        document.getElementById('chMeEst').value = 'off';   document.getElementById('chMeEst').checked = false;
        document.getElementById('chMeMue').value = 'off';   document.getElementById('chMeMue').checked = false;
        document.getElementById('chMeEle').value = 'off';   document.getElementById('chMeEle').checked = false;
        document.getElementById('chMePis').value = 'off';   document.getElementById('chMePis').checked = false;
        document.getElementById('chMePar').value = 'off';   document.getElementById('chMePar').checked = false;
        document.getElementById('chMeCol').value = 'off';   document.getElementById('chMeCol').checked = false;
        document.getElementById('chMeHum').value = 'off';   document.getElementById('chMeHum').checked = false;
        document.getElementById('chMeFac').value = 'off';   document.getElementById('chMeFac').checked = false;
        document.getElementById('chMeTec').value = 'off';   document.getElementById('chMeTec').checked = false;
        document.getElementById('chMeBan').value = 'off';   document.getElementById('chMeBan').checked = false;
        document.getElementById('chMeCoc').value = 'off';   document.getElementById('chMeCoc').checked = false;
        document.getElementById('chMeAmp').value = 'off';   document.getElementById('chMeAmp').checked = false;
    }
}

function uncheck2(campo)
{
    document.getElementById(campo).value = 'off';   document.getElementById(campo).checked = false;
}

function uncheck3()
{
    var val1 = document.getElementById('chNoFaVi').value;
    if(val1 == 'on')
    {
        document.getElementById('chInuVi').value = 'off';   document.getElementById('chInuVi').checked = false;
        document.getElementById('chContVi').value = 'off';  document.getElementById('chContVi').checked = false;
        document.getElementById('chRiAmVi').value = 'off';  document.getElementById('chRiAmVi').checked = false;
        document.getElementById('chRiEsvi').value = 'off';  document.getElementById('chRiEsvi').checked = false;
        document.getElementById('chRiSaVi').value = 'off';  document.getElementById('chRiSaVi').checked = false;
        document.getElementById('chRiPuVi').value = 'off';  document.getElementById('chRiPuVi').checked = false;
    }
}

function uncheck4()
{
    var val1 = document.getElementById('chPfNin').value;
    if(val1 == 'on')
    {
        document.getElementById('chPfCah').value = 'off';   document.getElementById('chPfCah').checked = false;
        document.getElementById('chPfCuc').value = 'off';   document.getElementById('chPfCuc').checked = false;
        document.getElementById('chPfTac').value = 'off';   document.getElementById('chPfTac').checked = false;
        document.getElementById('chPfCco').value = 'off';   document.getElementById('chPfCco').checked = false;
        document.getElementById('chPfChi').value = 'off';   document.getElementById('chPfChi').checked = false;
        document.getElementById('chPfCve').value = 'off';   document.getElementById('chPfCve').checked = false;
        document.getElementById('chPfInv').value = 'off';   document.getElementById('chPfInv').checked = false;
        document.getElementById('chPfSeg').value = 'off';   document.getElementById('chPfSeg').checked = false;
    }
}

function uncheck5()
{
    var val1 = document.getElementById('chMcNin').value;
    if(val1 == 'on')
    {
        document.getElementById('chMcViv').value = 'off';   document.getElementById('chMcViv').checked = false;
        document.getElementById('chMcTec').value = 'off';   document.getElementById('chMcTec').checked = false;
        document.getElementById('chMcMue').value = 'off';   document.getElementById('chMcMue').checked = false;
        document.getElementById('chMcEle').value = 'off';   document.getElementById('chMcEle').checked = false;
        document.getElementById('chMcVeh').value = 'off';   document.getElementById('chMcVeh').checked = false;
        document.getElementById('chMcSal').value = 'off';   document.getElementById('chMcSal').checked = false;
        document.getElementById('chMcCir').value = 'off';   document.getElementById('chMcCir').checked = false;
        document.getElementById('chMcTur').value = 'off';   document.getElementById('chMcTur').checked = false;
        document.getElementById('chMcLib').value = 'off';   document.getElementById('chMcLib').checked = false;
        document.getElementById('chMcGah').value = 'off';   document.getElementById('chMcGah').checked = false;
        document.getElementById('chMcTac').value = 'off';   document.getElementById('chMcTac').checked = false;
        document.getElementById('chMcEdp').value = 'off';   document.getElementById('chMcEdp').checked = false;
        document.getElementById('chMcEdf').value = 'off';   document.getElementById('chMcEdf').checked = false;
        document.getElementById('chMcCem').value = 'off';   document.getElementById('chMcCem').checked = false;
    }
}

function uncheck6()
{
    var val1 = document.getElementById('chEpNin').value;
    if(val1 == 'on')
    {
        document.getElementById('chEpBan').value = 'off';   document.getElementById('chEpBan').checked = false;
        document.getElementById('chEpFon').value = 'off';   document.getElementById('chEpFon').checked = false;
        document.getElementById('chEpFmu').value = 'off';   document.getElementById('chEpFmu').checked = false;
        document.getElementById('chEpPad').value = 'off';   document.getElementById('chEpPad').checked = false;
        document.getElementById('chEpFam').value = 'off';   document.getElementById('chEpFam').checked = false;
        document.getElementById('chEpCal').value = 'off';   document.getElementById('chEpCal').checked = false;
        document.getElementById('chEpCaj').value = 'off';   document.getElementById('chEpCaj').checked = false;
        document.getElementById('chEpEla').value = 'off';   document.getElementById('chEpEla').checked = false;
        document.getElementById('chEpNat').value = 'off';   document.getElementById('chEpNat').checked = false;
        document.getElementById('chEpOtr').value = 'off';   document.getElementById('chEpOtr').checked = false;
    }
}

function uncheck7()
{
    var val1 = document.getElementById('chLcNin').value;
    if(val1 == 'on')
    {
        document.getElementById('chLcViv').value = 'off';   document.getElementById('chLcViv').checked = false;
        document.getElementById('chLcVeh').value = 'off';   document.getElementById('chLcVeh').checked = false;
        document.getElementById('chLcSal').value = 'off';   document.getElementById('chLcSal').checked = false;
        document.getElementById('chLcCir').value = 'off';   document.getElementById('chLcCir').checked = false;
        document.getElementById('chLcTur').value = 'off';   document.getElementById('chLcTur').checked = false;
        document.getElementById('chLcEdf').value = 'off';   document.getElementById('chLcEdf').checked = false;
        document.getElementById('chLcEdp').value = 'off';   document.getElementById('chLcEdp').checked = false;
        document.getElementById('chLcCre').value = 'off';   document.getElementById('chLcCre').checked = false;
        document.getElementById('chLcMej').value = 'off';   document.getElementById('chLcMej').checked = false;
        document.getElementById('chLcCro').value = 'off';   document.getElementById('chLcCro').checked = false;
        document.getElementById('chLcLib').value = 'off';   document.getElementById('chLcLib').checked = false;
        document.getElementById('chLcTar').value = 'off';   document.getElementById('chLcTar').checked = false;
    }
}

function uncheck8()
{
    var val1 = document.getElementById('chIaNin').value;
    if(val1 == 'on')
    {
        document.getElementById('chIaInv').value = 'off';   document.getElementById('chIaInv').checked = false;
        document.getElementById('chIaBan').value = 'off';   document.getElementById('chIaBan').checked = false;
        document.getElementById('chIaNat').value = 'off';   document.getElementById('chIaNat').checked = false;
        document.getElementById('chIaCac').value = 'off';   document.getElementById('chIaCac').checked = false;
        document.getElementById('chIaFem').value = 'off';   document.getElementById('chIaFem').checked = false;
        document.getElementById('chIaFmu').value = 'off';   document.getElementById('chIaFmu').checked = false;
        document.getElementById('chIaFvp').value = 'off';   document.getElementById('chIaFvp').checked = false;
        document.getElementById('chIaOtr').value = 'off';   document.getElementById('chIaOtr').checked = false;
    }
}

function uncheck9()
{
    var val1 = document.getElementById('chNfNot').value;
    if(val1 == 'on')
    {
        document.getElementById('chNfCap').value = 'off';   document.getElementById('chNfCap').checked = false;
        document.getElementById('chNfDes').value = 'off';   document.getElementById('chNfDes').checked = false;
        document.getElementById('chNfRel').value = 'off';   document.getElementById('chNfRel').checked = false;
        document.getElementById('chNfMan').value = 'off';   document.getElementById('chNfMan').checked = false;
        document.getElementById('chNfFin').value = 'off';   document.getElementById('chNfFin').checked = false;
        document.getElementById('chNfFor').value = 'off';   document.getElementById('chNfFor').checked = false;
        document.getElementById('chNfIdi').value = 'off';   document.getElementById('chNfIdi').checked = false;
        document.getElementById('chNfInf').value = 'off';   document.getElementById('chNfInf').checked = false;
        document.getElementById('chNfFco').value = 'off';   document.getElementById('chNfFco').checked = false;
        document.getElementById('chNfOtr').value = 'off';   document.getElementById('chNfOtr').checked = false;
    }
}

function uncheck10()
{
    var val1 = document.getElementById('chHoNin').value;
    if(val1 == 'on')
    {
        document.getElementById('chHoCin').value = 'off';   document.getElementById('chHoCin').checked = false;
        document.getElementById('chHoDep').value = 'off';   document.getElementById('chHoDep').checked = false;
        document.getElementById('chHoVid').value = 'off';   document.getElementById('chHoVid').checked = false;
        document.getElementById('chHoVte').value = 'off';   document.getElementById('chHoVte').checked = false;
        document.getElementById('chHoNav').value = 'off';   document.getElementById('chHoNav').checked = false;
        document.getElementById('chHoIce').value = 'off';   document.getElementById('chHoIce').checked = false;
        document.getElementById('chHoIpa').value = 'off';   document.getElementById('chHoIpa').checked = false;
        document.getElementById('chHoIfi').value = 'off';   document.getElementById('chHoIfi').checked = false;
        document.getElementById('chHoCex').value = 'off';   document.getElementById('chHoCex').checked = false;
        document.getElementById('chHoDes').value = 'off';   document.getElementById('chHoDes').checked = false;
        document.getElementById('chHoJar').value = 'off';   document.getElementById('chHoJar').checked = false;
        document.getElementById('chHoCon').value = 'off';   document.getElementById('chHoCon').checked = false;
        document.getElementById('chHoPin').value = 'off';   document.getElementById('chHoPin').checked = false;
        document.getElementById('chHoEsc').value = 'off';   document.getElementById('chHoEsc').checked = false;
        document.getElementById('chHoFot').value = 'off';   document.getElementById('chHoFot').checked = false;
        document.getElementById('chHoVmu').value = 'off';   document.getElementById('chHoVmu').checked = false;
        document.getElementById('chHoVbi').value = 'off';   document.getElementById('chHoVbi').checked = false;
        document.getElementById('chHoEsp').value = 'off';   document.getElementById('chHoEsp').checked = false;
        document.getElementById('chHoDan').value = 'off';   document.getElementById('chHoDan').checked = false;
        document.getElementById('chHoTin').value = 'off';   document.getElementById('chHoTin').checked = false;
        document.getElementById('chHoCoc').value = 'off';   document.getElementById('chHoCoc').checked = false;
        document.getElementById('chHoMan').value = 'off';   document.getElementById('chHoMan').checked = false;
        document.getElementById('chHoOtr').value = 'off';   document.getElementById('chHoOtr').checked = false;
    }
}

function uncheck11()
{
    var val1 = document.getElementById('chHhNin').value;
    if(val1 == 'on')
    {
        document.getElementById('chHhCin').value = 'off';   document.getElementById('chHhCin').checked = false;
        document.getElementById('chHhDep').value = 'off';   document.getElementById('chHhDep').checked = false;
        document.getElementById('chHhVid').value = 'off';   document.getElementById('chHhVid').checked = false;
        document.getElementById('chHhVte').value = 'off';   document.getElementById('chHhVte').checked = false;
        document.getElementById('chHhNav').value = 'off';   document.getElementById('chHhNav').checked = false;
        document.getElementById('chHhIce').value = 'off';   document.getElementById('chHhIce').checked = false;
        document.getElementById('chHhIpa').value = 'off';   document.getElementById('chHhIpa').checked = false;
        document.getElementById('chHhIfi').value = 'off';   document.getElementById('chHhIfi').checked = false;
        document.getElementById('chHhCex').value = 'off';   document.getElementById('chHhCex').checked = false;
        document.getElementById('chHhDes').value = 'off';   document.getElementById('chHhDes').checked = false;
        document.getElementById('chHhJar').value = 'off';   document.getElementById('chHhJar').checked = false;
        document.getElementById('chHhCon').value = 'off';   document.getElementById('chHhCon').checked = false;
        document.getElementById('chHhPin').value = 'off';   document.getElementById('chHhPin').checked = false;
        document.getElementById('chHhEsc').value = 'off';   document.getElementById('chHhEsc').checked = false;
        document.getElementById('chHhFot').value = 'off';   document.getElementById('chHhFot').checked = false;
        document.getElementById('chHhVmu').value = 'off';   document.getElementById('chHhVmu').checked = false;
        document.getElementById('chHhVbi').value = 'off';   document.getElementById('chHhVbi').checked = false;
        document.getElementById('chHhEsp').value = 'off';   document.getElementById('chHhEsp').checked = false;
        document.getElementById('chHhDan').value = 'off';   document.getElementById('chHhDan').checked = false;
        document.getElementById('chHhTin').value = 'off';   document.getElementById('chHhTin').checked = false;
        document.getElementById('chHhCoc').value = 'off';   document.getElementById('chHhCoc').checked = false;
        document.getElementById('chHhMan').value = 'off';   document.getElementById('chHhMan').checked = false;
        document.getElementById('chHhOtr').value = 'off';   document.getElementById('chHhOtr').checked = false;
    }
}

function uncheck12()
{
    var val1 = document.getElementById('chBuNin').value;
    if(val1 == 'on')
    {
        document.getElementById('chBuFdi').value = 'off';   document.getElementById('chBuFdi').checked = false;
        document.getElementById('chBuNcd').value = 'off';   document.getElementById('chBuNcd').checked = false;
        document.getElementById('chBuDap').value = 'off';   document.getElementById('chBuDap').checked = false;
        document.getElementById('chBuFmo').value = 'off';   document.getElementById('chBuFmo').checked = false;
        document.getElementById('chBuNdt').value = 'off';   document.getElementById('chBuNdt').checked = false;
        document.getElementById('chBuOtr').value = 'off';   document.getElementById('chBuOtr').checked = false;
    }
}


function sumActi2(campo)
{
    if(document.getElementById('chViArr').value == 'on'){opt1 = 1} else{opt1 = 0}   if(document.getElementById('chViPag').value == 'on'){opt2 = 1} else{opt2 = 0}
    if(document.getElementById('chAlime').value == 'on'){opt3 = 1} else{opt3 = 0}   if(document.getElementById('chSerPu').value == 'on'){opt4 = 1} else{opt4 = 0}
    if(document.getElementById('chTrpte').value == 'on'){opt5 = 1} else{opt5 = 0}   if(document.getElementById('chEdPro').value == 'on'){opt6 = 1} else{opt6 = 0}
    if(document.getElementById('chEdHij').value == 'on'){opt7 = 1} else{opt7 = 0}   if(document.getElementById('chPaCre').value == 'on'){opt8 = 1} else{opt8 = 0}
    if(document.getElementById('chReTli').value == 'on'){opt9 = 1} else{opt9 = 0}   if(document.getElementById('chVestu').value == 'on'){opt10 = 1} else{opt10 = 0}
    if(document.getElementById('chSalud').value == 'on'){opt11 = 1} else{opt11 = 0} if(document.getElementById('chPaCel').value == 'on'){opt12 = 1} else{opt12 = 0}
    if(document.getElementById('chPaTar').value == 'on'){opt13 = 1} else{opt13 = 0} if(document.getElementById('chCoTec').value == 'on'){opt14 = 1} else{opt14 = 0}
    if(document.getElementById('chCuPer').value == 'on'){opt15 = 1} else{opt15 = 0} if(document.getElementById('chOtGas').value == 'on'){opt16 = 1} else{opt16 = 0}

    var totActi = opt1+opt2+opt3+opt4+opt5+opt6+opt7+opt8+opt9+opt10+opt11+opt12+opt13+opt14+opt15+opt16;

    if(totActi > 5)
    {
        alert('SOLO PUEDE SELECCIONAR 5 OPCIONES');
        document.getElementById(campo).checked = false;
        document.getElementById(campo).value = 'off';
    }
}

function activarCampo()
{
    valor = document.getElementById('actExtra').value;

    if(valor == 5){document.getElementById('otraExtra').readOnly = false}
    if(valor != 5){document.getElementById('otraExtra').readOnly = true; document.getElementById('otraExtra').value = ''}
}

function saveBasic(funcion,tablaMtx,usuarioMtx,IdefncP,IdegenP,IdecedP)
{
    // definimos la anchura y altura de la ventana
    var altura=314;  var anchura=800;
    // calculamos la posicion x e y para centrar la ventana
    var y=parseInt((window.screen.height/2)-(altura/2));
    var x=parseInt((window.screen.width/2)-(anchura/2));
    // mostramos la ventana centrada

    val1 = IdefncP.value;   val2 = IdegenP.value; val3 = IdecedP;

    if(val1 != '' && val2 != '' && val3 != '')
    {
        window.open("carEmp_Ops.php?funcion="+funcion.value+'&tablaMtx='+tablaMtx.value+'&usuarioMtx='+usuarioMtx.value+'&IdefncP='+IdefncP.value+'&IdegenP='+IdegenP.value+
            '&IdecedP='+IdecedP.value,
            target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=no,directories=no,resizable=no");
    }

}
