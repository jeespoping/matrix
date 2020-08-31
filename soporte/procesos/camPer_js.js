function exportarEx(accion,fecIni,fecFin)
{
    var altura=300; var anchura=800;
    var y=parseInt((window.screen.height/2)-(altura/2));
    var x=parseInt((window.screen.width/2)-(anchura/2));
    window.open("camPer_process.php?accion="+accion.value+'&fecIni='+fecIni.value+'&fecFin='+fecFin.value,
        target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");

}
