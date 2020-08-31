//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2020-02-06	Jessica Madrid Mejía	- Se modifica el tamaño del iframe para que los programas dentro de HCE puedan ser visualizados correctamente.
//  2019-11-21	Jessica Madrid Mejía	- Se descomprime(desminifica) el script HCE.min.js 
// 										- Se agrega la función encodeURIComponent() que codifica los caracteres especiales enviados por ajax.
//--------------------------------------------------------------------------------------------------------------------------------------------

var browser = navigator.appName, esIE = !1, hexcase = 0, b64pad = "";
function hex_sha1(a) {
    return rstr2hex(rstr_sha1(str2rstr_utf8(a)))
}
function hex_hmac_sha1(a, b) {
    return rstr2hex(rstr_hmac_sha1(str2rstr_utf8(a), str2rstr_utf8(b)))
}
function sha1_vm_test() {
    return "a9993e364706816aba3e25717850c26c9cd0d89d" == hex_sha1("abc").toLowerCase()
}
function rstr_sha1(a) {
    return binb2rstr(binb_sha1(rstr2binb(a), 8 * a.length))
}
function rstr_hmac_sha1(a, b) {
    var c = rstr2binb(a);
    16 < c.length && (c = binb_sha1(c, 8 * a.length));
    for (var d = Array(16), g = Array(16), f = 0; 16 > f; f++)
        d[f] = c[f] ^ 909522486, g[f] = c[f] ^ 1549556828;
    c = binb_sha1(d.concat(rstr2binb(b)), 512 + 8 * b.length);
    return binb2rstr(binb_sha1(g.concat(c), 672))
}
function rstr2hex(a) {
    try {
        hexcase
    } catch (f) {
        hexcase = 0
    }
    for (var b = hexcase ? "0123456789ABCDEF" : "0123456789abcdef", c = "", d, g = 0; g < a.length; g++)
        d = a.charCodeAt(g), c += b.charAt(d >>> 4 & 15) + b.charAt(d & 15);
    return c
}
function str2rstr_utf8(a) {
    for (var b = "", c = -1, d, g; ++c < a.length; )
        d = a.charCodeAt(c), g = c + 1 < a.length ? a.charCodeAt(c + 1) : 0, 55296 <= d && 56319 >= d && 56320 <= g && 57343 >= g && (d = 65536 + ((d & 1023) << 10) + (g & 1023), c++), 127 >= d ? b += String.fromCharCode(d) : 2047 >= d ? b += String.fromCharCode(192 | d >>> 6 & 31, 128 | d & 63) : 65535 >= d ? b += String.fromCharCode(224 | d >>> 12 & 15, 128 | d >>> 6 & 63, 128 | d & 63) : 2097151 >= d && (b += String.fromCharCode(240 | d >>> 18 & 7, 128 | d >>> 12 & 63, 128 | d >>> 6 & 63, 128 | d & 63));
    return b
}
function rstr2binb(a) {
    for (var b = Array(a.length >> 2), c = 0; c < b.length; c++)
        b[c] = 0;
    for (c = 0; c < 8 * a.length; c += 8)
        b[c >> 5] |= (a.charCodeAt(c / 8) & 255) << 24 - c % 32;
    return b
}
function binb2rstr(a) {
    for (var b = "", c = 0; c < 32 * a.length; c += 8)
        b += String.fromCharCode(a[c >> 5] >>> 24 - c % 32 & 255);
    return b
}
function binb_sha1(a, b) {
    a[b >> 5] |= 128 << 24 - b % 32;
    a[(b + 64 >> 9 << 4) + 15] = b;
    for (var c = Array(80), d = 1732584193, g = -271733879, f = -1732584194, e = 271733878, h = -1009589776, l = 0; l < a.length; l += 16) {
        for (var p = d, k = g, m = f, q = e, u = h, n = 0; 80 > n; n++) {
            c[n] = 16 > n ? a[l + n] : bit_rol(c[n - 3] ^ c[n - 8] ^ c[n - 14] ^ c[n - 16], 1);
            var v = safe_add(safe_add(bit_rol(d, 5), sha1_ft(n, g, f, e)), safe_add(safe_add(h, c[n]), sha1_kt(n)));
            h = e;
            e = f;
            f = bit_rol(g, 30);
            g = d;
            d = v
        }
        d = safe_add(d, p);
        g = safe_add(g, k);
        f = safe_add(f, m);
        e = safe_add(e, q);
        h = safe_add(h, u)
    }
    return [d, g, f, e, h]
}
function sha1_ft(a, b, c, d) {
    return 20 > a ? b & c | ~b & d : 40 > a ? b ^ c ^ d : 60 > a ? b & c | b & d | c & d : b ^ c ^ d
}
function sha1_kt(a) {
    return 20 > a ? 1518500249 : 40 > a ? 1859775393 : 60 > a ? -1894007588 : -899497514
}
function safe_add(a, b) {
    var c = (a & 65535) + (b & 65535);
    return (a >> 16) + (b >> 16) + (c >> 16) << 16 | c & 65535
}
function bit_rol(a, b) {
    return a << b | a >>> 32 - b
}
var clTARD = "#E8EEF7";
function cambiarColor(a, b) {
    a && (a.style.backgroundColor = b)
}
function tecladoCP(a, b) {
    tipo = document.getElementById("HCESEG" + b).value;
    if ("off" == tipo)
        if ("Microsoft" == navigator.appName.substring(0, 9))
            event.ctrlKey && 118 == event.keyCode && (event.returnValue = !1);
        else
            return 118 != a.which || !a.ctrlKey;
    else
        return !0
}
function limpiarHora(a) {
    document.getElementById("MI" + a).value = ""
}
function mostrarHora(a) {
    1 < document.getElementById("MI" + a).value.length && (document.getElementById("H" + a).value = document.getElementById("HO" + a).value + ":" + document.getElementById("MI" + a).value + ":00")
}
function limpiarHoraG(a, b) {
    document.getElementById("MIG" + b + a).value = ""
}
function mostrarHoraG(a, b) {
    1 < document.getElementById("MIG" + b + a).value.length && (document.getElementById("HGRID" + b + a).value = document.getElementById("HOG" + b + a).value + ":" + document.getElementById("MIG" + b + a).value + ":00")
}
function findPosX(a) {
    var b = 0;
    if (a.offsetParent)
        for (; ; ) {
            b += parseInt(a.offsetLeft);
            if (!a.offsetParent)
                break;
            a = a.offsetParent
        }
    else
        a.x && (b += parseInt(a.x));
    return parseInt(b)
}
function minmax(a, b, c, d) {
    if (0 != b || 0 != c)
        parseInt(a.value) < parseInt(b) || parseInt(a.value) > parseInt(c) ? (a.value = "", alert("ESTE CAMPO TIENE UN MINIMO DE " + b + " Y UN MAXIMO DE " + c), a.style.background = "#FF0000") : a.style.background = "1" == d ? "#DDDDDD" : "#FFFFFF"
}
function enter() {
    document.forms.HCE4.submit()
}
function tooltipAlertas(a) {
    $("#ALERT[pos] *").tooltip()
}
function tooltipnotas(a) {
    $("#NOT[pos] *").tooltip()
}
function tooltipGrid(a) {
    $("#GRIDTT[pos] *").tooltip()
}
function vertextogrid(a) {
    msg = document.getElementById(a).value;
    alert(msg)
}
function tooltipGrid(a) {
    $("#GRIDT[pos] *").tooltip()
}
function tooltipIconos(a) {
    $("#ICONOS[pos] *").tooltip()
}
function tooltipUT(a) {
    $("#UT[pos] *").tooltip()
}
function findPosY(a) {
    var b = 0;
    if (a.offsetParent)
        for (; ; ) {
            b += a.offsetTop;
            if (!a.offsetParent)
                break;
            a = a.offsetParent
        }
    else
        a.y && (b += a.y);
    return parseInt(b)
}
function parpadear() {
    for (var a = document.getElementsByTagName("blink"), b = 0; b < a.length; b++)
        a[b].style.visibility = "" == a[b].style.visibility ? "hidden" : ""
}
function posdivs(a, b, c, d, g, f, e) {
    try {
        b = parseInt(b),
        c = parseInt(c),
        a.style.position = "absolute",
        a.style.zIndex = "200",
        a.style.top = parseInt(c + parseInt(findPosY(document.getElementById(e)))).toString() + "px",
        a.style.left = parseInt(b + parseInt(findPosX(document.getElementById(e)))).toString() + "px",
        a.style.width = d + "px",
        a.style.height = g + "px",
        a.style.border = "solid",
        a.innerHTML = "<table><tr><td bgcolor=white><font size=2em><b>" + f + "</b></font></td></tr></table>"
    } catch (h) {
        alert("error " + h)
    }
}
function pintardivs() {
    for (var a = document.getElementsByTagName("img"), b = 0; b < a.length; b++) {
        var c = "";
        if ("mainImage" == a[b].id) {
            varable = document.getElementById("Hgrafica").value;
            if (0 < varable.length)
                for (frag1 = varable.split("^"), document.createElement("div"), i = 1; i < frag1.length; i++) {
                    var d = document.createElement("div");
                    frag2 = frag1[i].split("~");
                    d.id = frag2[0];
                    document.HCE6.appendChild(d);
                    posdivs(d, frag2[1], frag2[2], frag2[3], frag2[4], i, a[b].id);
                    c = c + i + ". " + frag2[5] + "<br>"
                }
            d = document.createElement("div");
            document.HCE6.appendChild(d);
            d.style.position = "absolute";
            d.style.top = parseInt(findPosY(a[b])) + "px";
            d.style.left = parseInt(findPosX(a[b])) + parseInt(a[b].offsetWidth) + 10 + "px";
            d.innerHTML = "<font size=2em>" + c + "</font>"
        }
    }
}
function reescritura(a, b) {
    a.value = b
}
function soloLectura(a) {
    if (a)
        if (a.tagName)
            switch (a.tagName.toLowerCase()) {
            case "select":
                -1 == a.options.selectedIndex && (a.options.selectedIndex = 0);
                if (-1 < a.options.selectedIndex)
                    for (var b = a.options[a.options.selectedIndex].text, c = a.options.length; 0 < c; c--)
                        b != a.options[c - 1].text && a.removeChild(a.options[c - 1]);
                cambiarColor(a, clTARD);
                break;
            case "input":
                switch (a.type.toLowerCase()) {
                case "radio":
                    b = document.getElementsByName(a.name);
                    for (c = 0; c < b.length; c++)
                        0 == b[c].checked && (b[c].disabled = !0), cambiarColor(b[c],
                            clTARD);
                    break;
                case "hidden":
                    break;
                case "submit":
                    break;
                case "image":
                    break;
                case "file":
                    break;
                case "reset":
                    break;
                case "button":
                    break;
                case "password":
                case "text":
                    a.readOnly = !0;
                    break;
                case "checkbox":
                    a.onclick = function () {
                        this.checked = !this.checked
                    };
                    break;
                default:
                    a.readOnly = !0
                }
                cambiarColor(a, clTARD);
                break;
            default:
                a.readOnly = !0,
                cambiarColor(a, clTARD)
            }
        else
            a[0] && soloLectura(a[0])
}
"Microsoft Internet Explorer" == browser && (esIE = !0);
function quitarComponente(a) {
    var b = document.getElementById("selAuto" + a);
    a = document.getElementById("cual" + a);
    var c;
    for (c = b.length - 1; 0 <= c; c--)
        b.options[c].selected && b.remove(c);
    b.options[0] && "on" == a.value && "S" == b.options[0].text.substring(0, 1) && (b.options[0].text = "P" + b.options[0].text.substring(1))
}
function quitarComponenteS(a) {
    a = document.getElementById("XX" + a);
    var b;
    for (b = a.length - 1; 0 <= b; b--)
        a.options[b].selected && a.remove(b)
}
function agregarComponenteS(a) {
    var b = document.getElementById("XX" + a),
    c = 0,
    d = document.createElement("option"),
    g = document.getElementById("XA" + a).value;
    if (0 < b.length)
        for (i = 0; i < b.length; i++)
            0 < g.length && b[i].value.substring(3) == g && (c = 1);
    if (0 < g.length && 0 == c) {
        c = "." + document.getElementById("XA" + a).value;
        eval("var radioR = document.forms.HCE6.XT" + a);
        if (radioR)
            for (j = 0; j < radioR.length; j++)
                if (1 == radioR[j].checked) {
                    c = radioR[j].value + "-" + c;
                    break
                }
        d.setAttribute("value", c);
        "Microsoft Internet Explorer" == navigator.appName ?
        (d.setAttribute("text", c), b.add(d)) : (d.innerHTML = c, b.add(d, null))
    }
}
function mostrarFlotante(a, b, c, d, g) {
    var f = 1,
    e = parent.parent.demograficos.document.all.txtformulario.value;
    c = c + "&wformulario=" + e;
    if ("" != e) {
        for (; parent.parent.document.getElementById("flotanteIframe" + f) && "block" == parent.parent.document.getElementById("flotanteIframe" + f).style.display && 1 > f; )
            f++;
        parent.parent.document.getElementById("flotanteIframe" + f) || (e = parent.parent.document.createElement("div"), e.setAttribute("name", "flotanteIframe" + f), e.setAttribute("id", "flotanteIframe" + f), esIE ? (e.style.setAttribute("background",
                    "#FFFFFF"), e.style.setAttribute("border", "3px solid  #000066"), e.style.setAttribute("position", "absolute"), e.style.setAttribute("z-index", "1500")) : e.setAttribute("style", "background:#FFFFFF;border:3px solid #000066;position:absolute;z-index:1500;"), parent.parent.document.body.appendChild(e), parent.parent.$("#flotanteIframe" + f).draggable());
        e = parent.parent.document.getElementById("flotanteIframe" + f);
        a = "<center><p><b>" + a + "</b></p>" + ("<center><input type='button' value='Ocultar' onClick='javascript:ocultarFlotante(\"" +
                f + "\");'/></center>") + ("<iframe name='" + b + "' src='" + c + "' width='100%' height='" + (parseInt(d, 10) + 40) + "px' width='" + g + "px' frameborder='0'  style='margin-top:0px;margin-left: 0px;' scrolling=no></iframe>");
        e.innerHTML = a;
        e.style.display = "block";
        e.style.width = parseInt(g, 10) + 10 + "px";
        e.style.height = parseInt(d, 10) + 90 + "px";
        e.style.position = "absolute";
        1 == f ? (e.style.left = "10px", e.style.top = "45%") : (e.style.left = e.style.width, e.style.top = "0px")
    } else
        alert("ESCOJA UN FORMULARIO PRIMERO")
}
function activarModalIframe(a, b, c, d, g) {
    "REGISTROS ASOCIADOS" == a && (c = c + "&wformulario=" + parent.parent.demograficos.document.all.txtformulario.value);
    var f = "no",
    e = "no",
    h = "si";
    "-1" == d && (d = "0", h = "no");
    // "0" == d && (f = "si", d = screen.availHeight);
    "0" == d && (f = "si", d = (screen.availHeight-70));
    "0" == g && (e = "si", g = screen.availWidth);
    a = "si" == h ? "<table cellpadding=1 cellspacing=1 width='100%' style='cursor:default'><tr height='10' class='encabezadoTabla'><td ><b>" + a + "</b></td><td align='center'><img src='../../images/medical/HCE/button.gif' title='Cerrar' onclick='javascript:cerrarModal();' style='cursor:hand; cursor: pointer;'></td></tr><tr><td colspan=2 class='textoNormal'>" :
        "<table cellpadding=1 cellspacing=1 width='100%' style='cursor:default'><tr height='10' class='encabezadoTabla'><td ><b>" + a + "</b></td><td align='center'></td></tr><tr><td colspan=2 class='textoNormal'>";
    a = "si" == f && "si" == e ? a + "<iframe name='" + b + "' src='" + c + "' height='" + (parseInt(d, 10) - 70) + "' width='100%' scrolling=yes frameborder='0'></iframe>" : a + "<iframe name='" + b + "' src='" + c + "' width='100%' height='" + (parseInt(d, 10) - 30) + "' width='" + g + "' frameborder='0'></iframe>";
    a += "</td></tr></table>";
    b = window.parent.parent;
    "si" == f && "si" == e ? b.$.blockUI({
        message: a,
        css: {
            width: g + "px",
            left: "0px",
            top: "0px"
        },
        centerX: !1,
        centerY: !1
    }) : b.$.blockUI({
        message: a,
        css: {
            width: g + "px",
            left: "20px",
            top: "20px"
        },
        centerX: !1,
        centerY: !1
    })
}
function cerrarModal() {
    $.unblockUI()
}
function ejecutar(a) {
    window.open(a, "", "fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=1,scrollbars=1,titlebar=0")
}
function recargaIframes(a, b) {
    // debugger;
    parent.frames.principal.location.href = a
}
function limpiarCampo(a) {
    a.value = ""
}
function calendario(a) {
    Zapatec.Calendar.setup({
        weekNumbers: !1,
        showsTime: !1,
        timeFormat: "24",
        electric: !1,
        inputField: "F" + a,
        button: "btn_" + a,
        ifFormat: "%Y-%m-%d",
        daFormat: "%Y/%m/%d"
    })
}
function nuevoAjax() {
    var a = !1;
    try {
        a = new ActiveXObject("Msxml2.XMLHTTP")
    } catch (b) {
        try {
            a = new ActiveXObject("Microsoft.XMLHTTP")
        } catch (c) {
            a = !1
        }
    }
    a || "undefined" == typeof XMLHttpRequest || (a = new XMLHttpRequest);
    return a
}
function estaEnProceso(a) {
    switch (a.readyState) {
    case 1:
        return !0;
    case 2:
        return !0;
    case 3:
        return !0;
    default:
        return !1
    }
}
function toggleDisplay(a) {
    a.style.display = "none" == a.style.display ? "" : "none"
}
function toggleDisplay1(a, b) {
    "none" == a.style.display ? (a.style.display = "", b.src = "/matrix/images/medical/hce/menos.png") : (a.style.display = "none", b.src = "/matrix/images/medical/hce/mas.png")
}
function ajaxalert(a, b, c, d, g, f, e, h, l, p, k, m, q) {
    st = confirm("\u00bfESTA SEGURO DE BORRAR ESTA ALERTA?") ? "empresa=" + c + "&origen=" + d + "&wdbmhos=" + g + "&okA=" + q + "&accion=" + b + "&wcedula=" + e + "&wtipodoc=" + h + "&wformulario=" + f + "&whis=" + l + "&wing=" + p + "&wfecha=" + k + "&whora=" + m : "empresa=" + c + "&origen=" + d + "&wdbmhos=" + g + "&accion=" + b + "&wcedula=" + e + "&wtipodoc=" + h + "&wformulario=" + f + "&whis=" + l + "&wing=" + p + "&wfecha=" + k + "&whora=" + m;
    try {
        ajax = nuevoAjax(),
        ajax.open("POST", "HCE.php", !0),
        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"),
        ajax.send(st),
        ajax.onreadystatechange = function () {
            4 == ajax.readyState && 200 == ajax.status && (document.getElementById(+a).innerHTML = ajax.responseText)
        },
        estaEnProceso(ajax) || ajax.send(null)
    } catch (u) {}
}
function ninguno(a) {
    eval("var radio = document.forms.HCE6." + a);
    for (j = 0; j < radio.length; j++)
        1 == radio[j].checked && (radio[j].checked = !1)
}
function grabagrid(a) {
    pos1 = document.getElementById("WGRIDITEM").value;
    if (0 == pos1) {
        config = document.getElementById("GRID" + a).value;
        segmentos = config.split("*");
        titl = segmentos[0].split("|");
        tipo = segmentos[1].split("|");
        tobl = segmentos[2].split("|");
        data = "";
        wsw = 0;
        msg = "LOS CAMPOS : ";
        for (i = 0; i < tipo.length; i++)
            switch ("^" == tipo[i].substring(0, 1) && (tipo[i] = tipo[i].substring(1)), tipo[i].substring(0, 1)) {
            case "F":
                0 < i && (data += "|");
                "R" == tobl[i] && "" == document.getElementById("FGRID" + a + i).value && (wsw = 1, msg = msg + titl[i] +
                        "/");
                data += document.getElementById("FGRID" + a + i).value;
                break;
            case "H":
                0 < i && (data += "|");
                "R" == tobl[i] && "" == document.getElementById("HGRID" + a + i).value && (wsw = 1, msg = msg + titl[i] + "/");
                data += document.getElementById("HGRID" + a + i).value;
                break;
            case "S":
                0 < i && (data += "|");
                "R" == tobl[i] && "Seleccione" == document.getElementById("SGRID" + a + i).value && (wsw = 1, msg = msg + titl[i] + "/");
                data += document.getElementById("SGRID" + a + i).value;
                break;
            case "T":
                0 < i && (data += "|");
                "R" == tobl[i] && "" == document.getElementById("WGRID" + a + i).value && (wsw =
                        1, msg = msg + titl[i] + "/");
                data += document.getElementById("WGRID" + a + i).value;
                break;
            case "M":
                0 < i && (data += "|");
                "R" == tobl[i] && "" == document.getElementById("MGRID" + a + i).value && (wsw = 1, msg = msg + titl[i] + "/");
                data += document.getElementById("MGRID" + a + i).value;
                break;
            case "N":
                0 < i && (data += "|"),
                "R" == tobl[i] && "" == document.getElementById("TGRID" + a + i).value && (wsw = 1, msg = msg + titl[i] + "/"),
                data += document.getElementById("TGRID" + a + i).value
            }
        if (0 == wsw) {
            valores = document.getElementById("J" + a).value;
            val1 = valores.split("*");
            val1[0] =
                parseInt(val1[0]) + 1;
            document.getElementById("J" + a).value = val1[0].toString();
            for (i = 1; i < val1[0]; i++)
                document.getElementById("J" + a).value = document.getElementById("J" + a).value + "*" + val1[i].toString();
            document.getElementById("J" + a).value = document.getElementById("J" + a).value + "*" + data;
            return !0
        }
        msg += "   !!!SON OBLIGATORIOS Y NO FUERON DILIGENCIADOS!!! ";
        alert(msg);
        return !1
    }
}
function posgrid(a, b) {
    valores = document.getElementById("J" + b).value;
    val1 = valores.split("*");
    for (i = 1; i <= val1[0]; i++)
        i == a && (val2 = val1[i].split("|"));
    document.getElementById("WGRIDITEM").value = a;
    config = document.getElementById("GRID" + b).value;
    segmentos = config.split("*");
    tipo = segmentos[1].split("|");
    data = "";
    for (i = 0; i < tipo.length; i++)
        switch ("^" == tipo[i].substring(0, 1) && (tipo[i] = tipo[i].substring(1)), tipo[i].substring(0, 1)) {
        case "F":
            document.getElementById("FGRID" + b + i).value = val2[i];
            break;
        case "H":
            document.getElementById("HGRID" +
                b + i).value = val2[i];
            break;
        case "S":
            document.getElementById("SGRID" + b + i).value = val2[i];
            break;
        case "T":
            document.getElementById("WGRID" + b + i).value = val2[i];
            break;
        case "M":
            document.getElementById("MGRID" + b + i).value = val2[i];
            break;
        case "N":
            document.getElementById("TGRID" + b + i).value = val2[i]
        }
}
function modificagrid(a) {
    pos1 = document.getElementById("WGRIDITEM").value;
    if (0 < pos1) {
        valores = document.getElementById("J" + a).value;
        val1 = valores.split("*");
        document.getElementById("J" + a).value = val1[0].toString();
        for (j = 1; j <= val1[0]; j++)
            if (j == pos1) {
                config = document.getElementById("GRID" + a).value;
                segmentos = config.split("*");
                titl = segmentos[0].split("|");
                tipo = segmentos[1].split("|");
                tobl = segmentos[2].split("|");
                data = "";
                wsw = 0;
                msg = "LOS CAMPOS : ";
                for (i = 0; i < tipo.length; i++)
                    switch ("^" == tipo[i].substring(0, 1) &&
                        (tipo[i] = tipo[i].substring(1)), tipo[i].substring(0, 1)) {
                    case "F":
                        0 < i && (data += "|");
                        "R" == tobl[i] && "" == document.getElementById("FGRID" + a + i).value && (wsw = 1, msg = msg + titl[i] + "/");
                        data += document.getElementById("FGRID" + a + i).value;
                        break;
                    case "H":
                        0 < i && (data += "|");
                        "R" == tobl[i] && "" == document.getElementById("HGRID" + a + i).value && (wsw = 1, msg = msg + titl[i] + "/");
                        data += document.getElementById("HGRID" + a + i).value;
                        break;
                    case "S":
                        0 < i && (data += "|");
                        "R" == tobl[i] && "Seleccione" == document.getElementById("SGRID" + a + i).value && (wsw = 1,
                            msg = msg + titl[i] + "/");
                        data += document.getElementById("SGRID" + a + i).value;
                        break;
                    case "T":
                        0 < i && (data += "|");
                        "R" == tobl[i] && "" == document.getElementById("WGRID" + a + i).value && (wsw = 1, msg = msg + titl[i] + "/");
                        data += document.getElementById("WGRID" + a + i).value;
                        break;
                    case "M":
                        0 < i && (data += "|");
                        "R" == tobl[i] && "" == document.getElementById("MGRID" + a + i).value && (wsw = 1, msg = msg + titl[i] + "/");
                        data += document.getElementById("MGRID" + a + i).value;
                        break;
                    case "N":
                        0 < i && (data += "|"),
                        "R" == tobl[i] && "" == document.getElementById("TGRID" + a + i).value &&
                        (wsw = 1, msg = msg + titl[i] + "/"),
                        data += document.getElementById("TGRID" + a + i).value
                    }
                0 == wsw ? document.getElementById("J" + a).value = document.getElementById("J" + a).value + "*" + data : (msg += "  !!!SON OBLIGATORIOS Y NO FUERON DILIGENCIADOS!!! ", alert(msg), document.getElementById("J" + a).value = document.getElementById("J" + a).value + "*" + val1[j].toString())
            } else
                document.getElementById("J" + a).value = document.getElementById("J" + a).value + "*" + val1[j].toString();
        return 0 == wsw ? !0 : !1
    }
}
function borragrid(a) {
    pos1 = document.getElementById("WGRIDITEM").value;
    if (0 < pos1)
        for (valores = document.getElementById("J" + a).value, val1 = valores.split("*"), menos = parseInt(val1[0]) - 1, document.getElementById("J" + a).value = menos.toString(), 0 == menos && (document.getElementById("J" + a).value = "0*"), i = 1; i <= val1[0]; i++)
            i != pos1 && (document.getElementById("J" + a).value = document.getElementById("J" + a).value + "*" + val1[i].toString())
}
function limpiagrid(a) {
    var b = new Date,
    c = b.getFullYear() + "-" + b.getMonth() + "-" + b.getDate();
    b = b.getHours() + ":" + b.getMinutes() + ":" + b.getSeconds();
    document.getElementById("WGRIDITEM").value = 0;
    config = document.getElementById("GRID" + a).value;
    segmentos = config.split("*");
    tipo = segmentos[1].split("|");
    data = "";
    for (i = 0; i < tipo.length; i++)
        switch ("^" == tipo[i].substring(0, 1) && (tipo[i] = tipo[i].substring(1)), tipo[i].substring(0, 1)) {
        case "F":
            document.getElementById("FGRID" + a + i).value = c;
            break;
        case "H":
            document.getElementById("HGRID" +
                a + i).value = b;
            break;
        case "S":
            document.getElementById("SGRID" + a + i).value = val2[i];
            break;
        case "T":
            document.getElementById("WGRID" + a + i).value = "";
            break;
        case "M":
            document.getElementById("MGRID" + a + i).value = "";
            break;
        case "N":
            document.getElementById("TGRID" + a + i).value = ""
        }
}
function ajaxtable(a, b, c, d, g, f, e, h, l, p, k, m, q) {
    k = document.getElementById("FI" + q).value;
    m = document.getElementById("FF" + q).value;
    st = "empresa=" + b + "&origen=" + c + "&wdbmhos=" + d + "&accion=" + g + "&wformulario=" + f + "&wcedula=" + e + "&wtipodoc=" + h + "&whis=" + l + "&wing=" + p + "&wfechai=" + k + "&wfechaf=" + m + "&fila=" + a;
    try {
        ajax = nuevoAjax(),
        ajax.open("POST", "HCE.php", !0),
        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"),
        ajax.send(st),
        ajax.onreadystatechange = function () {
            4 == ajax.readyState && 200 == ajax.status &&
            (document.getElementById(+a).innerHTML = ajax.responseText)
        },
        estaEnProceso(ajax) || ajax.send(null)
    } catch (u) {}
}
function reemplazarTodo(a, b, c) {
    for (; -1 != a.toString().indexOf(b); )
        a = a.toString().replace(b, c);
    return a
}
function replaceAll(a, b, c) {
    for (; -1 != a.toString().indexOf(b); )
        a = a.toString().replace(b, c);
    return a
}
function ajaxview(a, b, c, d, g, f, e, h, l, p, k, m, q, u, n, v, z, w, A, B, C, y, D, r) {
    "1" == r && 3 != y && (t = document.getElementById("firma").value, "" != t ? document.getElementById("logook").style.display = "none" : confirm(" Esta seguro de Grabar el Documento SIN FIRMA ELECTRONICA ??") || (r = 0), FED = document.getElementById("FED") && 1 == document.getElementById("FED").checked ? "CHECKED" : "UNCHECKED");
    2 == r && alert("LA INFORMACION HA SIDO RESPALDADA TEMPORALMENTE. PARA ALMACENARLA EN FORMA DEFINITIVA HAGA CLICK EN EL ICONO DE GRABAR.  GRACIAS");
    x = [];
    xs = [];
    xr = [];
    TEXTO = [];
    var t = "";
    Hgrafica = document.getElementById("Hgrafica").value;
    q = document.getElementById("position").value;
    wsex = document.getElementById("wsex").value;
    wedad = document.getElementById("wedad").value;
    frag = f.split("-");
    for (i = 0; i < frag.length; i++)
        if ("FIN" != frag[i])
            switch (frag[i].substring(0, 1)) {
            case "W":
                x[i] = document.getElementById(frag[i]).value;
                x[i] = replaceAll(x[i], "+", "~");
                TEXTO[i] = document.getElementById(frag[i]).value;
                break;
            case "J":
                x[i] = document.getElementById(frag[i]).value;
                x[i] = replaceAll(x[i], "+", "~");
                TEXTO[i] = document.getElementById(frag[i]).value;
                break;
            case "T":
                x[i] = document.getElementById(frag[i]).value;
                break;
            case "H":
                x[i] = document.getElementById(frag[i]).value;
                break;
            case "F":
                x[i] = document.getElementById(frag[i]).value;
                break;
            case "C":
                1 == document.getElementById(frag[i]).checked ? x[i] = "CHECKED" : x[i] = "UNCHECKED";
                break;
            case "S":
                x[i] = document.getElementById(frag[i]).value;
                x[i] = replaceAll(x[i], "+", "~");
                break;
            case "M":
                x[i] = reemplazarTodo(document.getElementById("selAuto" +
                            frag[i].substring(1)).innerHTML, '"', "");
                document.getElementById("TS" + frag[i].substring(1)) && (xs[i] = document.getElementById("TS" + frag[i].substring(1)).value);
                eval("var radioR = document.forms.HCE6.RT" + frag[i].substring(1));
                if (radioR)
                    for (j = 0; j < radioR.length; j++)
                        if (1 == radioR[j].checked) {
                            xr[i] = radioR[j].value;
                            break
                        }
                break;
            case "X":
                x[i] = reemplazarTodo(document.getElementById("XX" + frag[i].substring(1)).innerHTML, '"', "");
                break;
            case "R":
                for (eval("var radio = document.forms.HCE6." + frag[i]), j = 0; j < radio.length; j++)
                    if (1 ==
                        radio[j].checked) {
                        radio[j].value = replaceAll(radio[j].value, "+", "~");
                        x[i] = radio[j].value;
                        break
                    }
            }
    okGrid = "no";
    "1" == r ? r = "CHECKED" : (r = "UNCHECKED", "5" == r && (okGrid = "si"));
    3 != y ? (t = document.getElementById("firma").value, "" != t && "HCE" != t.substring(0, 3) && (t = "HCE " + hex_sha1(document.getElementById("firma").value))) : t = "";
    st = "empresa=" + b + "&origen=" + c + "&wdbmhos=" + d + "&ok=" + r + "&accion=" + g + "&wformulario=" + h + "&wcedula=" + l + "&wtipodoc=" + p + "&whis=" + k + "&wing=" + m + "&wsa=" + e + "&position=" + q + "&wfechaT=" + u + "&whoraT=" + n + "&width=" +
        v + "&num=" + z + "&firma=" + t + "&WSF=" + w + "&wsinfirma=" + A + "&wfechareg=" + B + "&whorareg=" + C + "&WTIPO=" + y + "&Hgrafica=" + Hgrafica + "&wsex=" + wsex + "&wedad=" + wedad + "&okGrid=" + okGrid;
    document.getElementById("FED") && (st = st + "&FED=" + FED);
    document.getElementById("MemoText") && (st = st + "&MemoText=" + document.getElementById("MemoText").value);
    document.getElementById("MemoDrop") && "0-NO APLICA" != document.getElementById("MemoDrop").value && "SELECCIONE" != document.getElementById("MemoDrop").value && (st = st + "&MemoDrop=" + document.getElementById("MemoDrop").value,
        -1 == TEXTO[12].indexOf(document.getElementById("MemoDrop").value) && (TEXTO[12] += document.getElementById("MemoDrop").value + "\n"));
    for (i = 0; i < frag.length - 1; i++)
        // st = st + "&registro[" + parseInt(frag[i].substring(1), 10) + "][0]=" + x[i], xs[i] && (st = st + "&Tselect[" + parseInt(frag[i].substring(1), 10) + "]=" + xs[i]), xr[i] && (st = st + "&RT" + parseInt(frag[i].substring(1), 10) + "=" + xr[i]);
        st=st+"&registro["+parseInt(frag[i].substring(1),10)+"][0]="+encodeURIComponent(x[i]);
		if(xs[i])
			st=st+"&Tselect["+parseInt(frag[i].substring(1),10)+"]="+encodeURIComponent(xs[i]);
		if(xr[i])
			st=st+"&RT"+parseInt(frag[i].substring(1),10)+"="+encodeURIComponent(xr[i]);
	
	try {
        ajax = nuevoAjax(),
        ajax.open("POST", "HCE.php", !0),
        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"),
        ajax.send(st),
        ajax.onreadystatechange =
        function () {
            if (4 == ajax.readyState && 200 == ajax.status) {
                document.getElementById(+a).innerHTML = ajax.responseText;
                w = document.getElementById("WSF").value;
                SF = f.split("-");
                for (c = 0; c < SF.length; c++)
                    if ("W" == SF[c].substring(0, 1) && (VALDIFF = document.getElementById(SF[c]).value, !document.getElementById("GOK") || document.getElementById("GOK") && "2" == document.getElementById("GOK").value) && (VALDIFF != TEXTO[c] && " " != TEXTO[c] && "" != TEXTO[c] && "0*" != TEXTO[c] && "CERRADO" != TEXTO[c] ? document.getElementById(SF[c]).value = TEXTO[c] :
                                document.getElementById(SF[c]).value = VALDIFF), "J" == SF[c].substring(0, 1) && (VALDIFF = document.getElementById(SF[c]).value, !document.getElementById("GOK") || document.getElementById("GOK") && "2" == document.getElementById("GOK").value) && (VALDIFF == TEXTO[c] && " " != TEXTO[c] && "" != TEXTO[c] && "0*" == TEXTO[c] && "CERRADO" != TEXTO[c] ? document.getElementById(SF[c]).value = TEXTO[c] : document.getElementById(SF[c]).value = VALDIFF), "X" == SF[c].substring(0, 1) && (document.getElementById("XX" + SF[c].substring(1)).style.width = document.getElementById("registro[" +
                                    SF[c].substring(1) + "][36]").value), "G" == SF[c].substring(0, 1)) {
                        var b = new Tipmage("mainImage", !0);
                        b.startup();
                        varable = document.getElementById("Hgrafica").value;
                        b.onInsert = function (a, b, c, d, e, f) {
                            document.getElementById("Hgrafica").value = document.getElementById("Hgrafica").value + "^" + parseInt(a) + "~" + b + "~" + c + "~" + d + "~" + e + "~" + f
                        };
                        b.onUpdate = function (a, b, c, d, e, f) {
                            final1 = "";
                            arreglo = document.getElementById("Hgrafica").value;
                            frag1 = arreglo.split("^");
                            for (j = 1; j < frag1.length; j++)
                                frag2 = frag1[j].split("~"), frag2[0] ==
                                a && (frag2[1] = b, frag2[2] = c, frag2[3] = d, frag2[4] = e, frag2[5] = f, frag1[j] = frag2[0] + "~" + frag2[1] + "~" + frag2[2] + "~" + frag2[3] + "~" + frag2[4] + "~" + frag2[5]), final1 += "^" + frag1[j];
                            document.getElementById("Hgrafica").value = final1
                        };
                        b.onDelete = function (a, b, c, d, e, f) {
                            final1 = "";
                            arreglo = document.getElementById("Hgrafica").value;
                            frag1 = arreglo.split("^");
                            for (j = 1; j < frag1.length; j++)
                                frag2 = frag1[j].split("~"), frag2[0] != a && (final1 = final1 + "^" + frag1[j]);
                            document.getElementById("Hgrafica").value = final1
                        }
                    }
                SF = f.split("-");
                SS = D.split("-");
                for (c = 0; c < SF.length; c++)
                    2 == SS[c] && soloLectura(document.getElementById(SF[c]));
                SF = w.split("-");
                for (c = 0; c < SF.length; c++);
                $.mask.definitions.H = "[012]";
                $.mask.definitions.N = "[012345]";
                $.mask.definitions.n = "[0123456789]";
                b = document.getElementsByTagName("input");
                for (var c = 0; c < b.length; c++)
                    "text" == b[c].type && ("F" == b[c].id.substring(0, 1) && buscarSF(b[c].id) && $("#" + b[c].id).datepicker({
                            showOn: "button",
                            buttonImage: "../../images/medical/root/calendar.gif",
                            buttonImageOnly: !0,
                            dateFormat: "yy-mm-dd",
                            changeMonth: !0,
                            changeYear: !0,
                            yearRange: "-90:+10"
                        }), "FGRID" == b[c].id.substring(0, 5) && $("#" + b[c].id).datepicker({
                            showOn: "button",
                            buttonImage: "../../images/medical/root/calendar.gif",
                            buttonImageOnly: !0,
                            dateFormat: "yy-mm-dd",
                            changeMonth: !0,
                            changeYear: !0,
                            yearRange: "-90:+10"
                        }), "H" == b[c].id.substring(0, 1) && $("#" + b[c].id).mask("Hn:Nn:Nn"), "M" == b[c].id.substring(0, 1) && (document.getElementById("selAuto" + b[c].id.substring(1)).style.width = document.getElementById("registro[" + b[c].id.substring(1) + "][36]").value, $("#TooltipT" +
                                b[c].id.substring(1)).tooltip({
                                track: !0,
                                delay: 0,
                                showURL: !1,
                                showBody: " - ",
                                extraClass: "globo",
                                fixPNG: !0,
                                opacity: .95,
                                left: -120
                            }), $("#M" + b[c].id.substring(1)).autocomplete("../../HCE/procesos/autocompletar.php?consulta=" + document.getElementById("query" + b[c].id.substring(1)).value, {
                                max: 100,
                                scroll: !0,
                                scrollHeight: 300,
                                matchContains: !0,
                                width: 500,
                                autoFill: !1,
                                formatResult: function (a, b) {
                                    return b
                                }
                            }).result(function (a, b) {
                                var c = document.getElementById("selAuto" + this.id.substring(1)),
                                d = document.createElement("option");
                                b += ".";
                                if (document.getElementById("SimMul" + this.id.substring(1))) {
                                    var e = document.getElementById("SimMul" + this.id.substring(1)).value,
                                    f = document.getElementById("Tcual" + this.id.substring(1)).value,
                                    g = document.getElementById("TTipe" + this.id.substring(1)).value;
                                    f = f.split(",");
                                    var m = f[0],
                                    k = f[1];
                                    if ("on" == document.getElementById("cual" + this.id.substring(1)).value && 2 < f.length) {
                                        if (2 < f.length && (eval("var radioR = document.forms.HCE6.RT" + this.id.substring(1)), radioR))
                                            for (j = 0; j < radioR.length; j++)
                                                if (1 == radioR[j].checked) {
                                                    b =
                                                        radioR[j].value + "-" + b;
                                                    break
                                                }
                                        "" != m && "" != k && "N" != g && "QN" != g && (b = 0 == c.options.length ? m + "-" + b : k + "-" + b)
                                    }
                                }
                                d.setAttribute("value", b);
                                if ("Microsoft Internet Explorer" == navigator.appName) {
                                    if (0 == c.options.length && "S" == e || "M" == e)
                                        d.setAttribute("text", b), c.add(d)
                                } else if (0 == c.options.length && "S" == e || "M" == e)
                                    d.innerHTML = b, c.add(d, null);
                                $("#M" + this.id.substring(1)).focus();
                                $("#M" + this.id.substring(1)).select()
                            })));
                "CHECKED" != r || 1 != document.getElementById("wswfirma").value && 2 != document.getElementById("wswfirma").value ?
                document.getElementById("logook") && (document.getElementById("logook").style.display = "") : document.getElementById("logook") && (document.getElementById("logook").style.display = "none")
            }
        },
        estaEnProceso(ajax) || ajax.send(null)
    } catch (E) {}
}
function ajaxrecov(a, b, c, d, g, f, e, h, l, p) {
    eval("var radio = document.forms.HCE6.RECOV1");
    for (j = 0; j < radio.length; j++)
        if (1 == radio[j].checked) {
            RECOV = radio[j].value;
            break
        }
    st = "empresa=" + b + "&origen=" + c + "&wdbmhos=" + d + "&accion=" + g + "&wformulario=" + f + "&wcedula=" + e + "&wtipodoc=" + h + "&whis=" + l + "&wing=" + p + "&RECOV=" + RECOV;
    try {
        ajax = nuevoAjax(),
        ajax.open("POST", "HCE.php", !0),
        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"),
        ajax.send(st),
        ajax.onreadystatechange = function () {
            if (4 == ajax.readyState &&
                200 == ajax.status) {
                document.getElementById(+a).innerHTML = ajax.responseText;
                $.mask.definitions.H = "[012]";
                $.mask.definitions.N = "[012345]";
                $.mask.definitions.n = "[0123456789]";
                for (var b = document.getElementsByTagName("input"), c = 0; c < b.length; c++)
                    "text" == b[c].type && ("F" == b[c].id.substring(0, 1) && buscarSF(b[c].id) && $("#" + b[c].id).datepicker({
                            showOn: "button",
                            buttonImage: "../../images/medical/root/calendar.gif",
                            buttonImageOnly: !0,
                            dateFormat: "yy-mm-dd",
                            changeMonth: !0,
                            changeYear: !0,
                            yearRange: "-90:+10"
                        }), "FGRID" ==
                        b[c].id.substring(0, 5) && $("#" + b[c].id).datepicker({
                            showOn: "button",
                            buttonImage: "../../images/medical/root/calendar.gif",
                            buttonImageOnly: !0,
                            dateFormat: "yy-mm-dd",
                            changeMonth: !0,
                            changeYear: !0,
                            yearRange: "-90:+10"
                        }), "H" == b[c].id.substring(0, 1) && $("#" + b[c].id).mask("Hn:Nn:Nn"), "M" == b[c].id.substring(0, 1) && (document.getElementById("selAuto" + b[c].id.substring(1)).style.width = document.getElementById("registro[" + b[c].id.substring(1) + "][36]").value, $("#TooltipT" + b[c].id.substring(1)).tooltip({
                                track: !0,
                                delay: 0,
                                showURL: !1,
                                showBody: " - ",
                                extraClass: "globo",
                                fixPNG: !0,
                                opacity: .95,
                                left: -120
                            }), $("#M" + b[c].id.substring(1)).autocomplete("../../HCE/procesos/autocompletar.php?consulta=" + document.getElementById("query" + b[c].id.substring(1)).value, {
                                max: 100,
                                scroll: !0,
                                scrollHeight: 300,
                                matchContains: !0,
                                width: 500,
                                autoFill: !1,
                                formatResult: function (a, b) {
                                    return b
                                }
                            }).result(function (a, b) {
                                var c = document.getElementById("selAuto" + this.id.substring(1)),
                                d = document.createElement("option");
                                b += ".";
                                if (document.getElementById("SimMul" +
                                        this.id.substring(1))) {
                                    var f = document.getElementById("SimMul" + this.id.substring(1)).value,
                                    e = document.getElementById("Tcual" + this.id.substring(1)).value.split(","),
                                    g = e[0],
                                    h = e[1],
                                    l = document.getElementById("cual" + this.id.substring(1)),
                                    k = document.getElementById("TTipe" + this.id.substring(1)).value;
                                    if ("on" == l.value && 2 < e.length) {
                                        if (2 < e.length && (eval("var radioR = document.forms.HCE6.RT" + this.id.substring(1)), radioR))
                                            for (j = 0; j < radioR.length; j++)
                                                if (1 == radioR[j].checked) {
                                                    b = radioR[j].value + "-" + b;
                                                    break
                                                }
                                        "" != g &&
                                        "" != h && "N" != k && "QN" != k && (b = 0 == c.options.length ? g + "-" + b : h + "-" + b)
                                    }
                                }
                                d.setAttribute("value", b);
                                if ("Microsoft Internet Explorer" == navigator.appName) {
                                    if (0 == c.options.length && "S" == f || "M" == f)
                                        d.setAttribute("text", b), c.add(d)
                                } else if (0 == c.options.length && "S" == f || "M" == f)
                                    d.innerHTML = b, c.add(d, null);
                                $("#M" + this.id.substring(1)).focus();
                                $("#M" + this.id.substring(1)).select()
                            })))
            }
        },
        estaEnProceso(ajax) || ajax.send(null)
    } catch (k) {}
}
function enter1() {
    document.forms.HCE6.submit()
}
function enter2() {
    document.forms.HCE7.submit()
}
function ejecutar(a, b) {
    1 == b ? window.open(a, "", "fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0,width=900,height=425") : window.open(a, "", "fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0,width=900,height=580")
}
function teclado(a) {
    if ("Microsoft" == navigator.appName.substring(0, 9))
        (48 > event.keyCode || 57 < event.keyCode || 13 == event.keyCode) && 46 != event.keyCode && 8 != event.keyCode && (event.returnValue = !1);
    else
        return 48 <= a.which && 57 >= a.which || 46 == a.which || 0 == a.which || 8 == a.which
}
function teclado1() {
    (48 > event.keyCode || 57 < event.keyCode) & 46 != event.keyCode & 13 != event.keyCode && (event.returnValue = !1)
}
function teclado2() {
    (48 > event.keyCode || 57 < event.keyCode) & (65 > event.keyCode || 90 < event.keyCode) & (97 > event.keyCode || 122 < event.keyCode) & 13 != event.keyCode && (event.returnValue = !1)
}
function teclado3() {
    (48 > event.keyCode || 57 < event.keyCode) & (65 > event.keyCode || 90 < event.keyCode) & (97 > event.keyCode || 122 < event.keyCode) & 13 != event.keyCode & 45 != event.keyCode && (event.returnValue = !1)
}
function teclado4() {
    (48 > event.keyCode || 57 < event.keyCode) & (65 > event.keyCode || 90 < event.keyCode) & 32 != event.keyCode & 13 != event.keyCode && (event.returnValue = !1)
}
function teclado5() {
    (48 > event.keyCode || 57 < event.keyCode) & 13 != event.keyCode && (event.returnValue = !1)
}
function teclado6(a) {
    if ("Microsoft" == navigator.appName.substring(0, 9))
        8 != event.keyCode && (event.returnValue = !1);
    else
        return 8 == a.which
}
function tecladonull(a) {
    if ("Microsoft" == navigator.appName.substring(0, 9))
        13 != event.keyCode && (event.returnValue = !1);
    else
        return 13 == a.which
}
function buscarSF(a) {
    WSF = document.getElementById("WSF").value;
    SF = WSF.split("-");
    for (i = 0; i < SF.length; i++)
        if (a == SF[i])
            return !1;
    return !0
}
function init_jquery() {
    $("#accordion").accordion({
        collapsible: !0
    });
    for (var a = 1; document.getElementById("TooltipU" + a); )
        $("#TooltipU" + a).tooltip({
            track: !0,
            delay: 0,
            showURL: !1,
            showBody: " - ",
            extraClass: "globo",
            fixPNG: !0,
            opacity: .95,
            left: -120
        }), a++;
    $.mask.definitions.H = "[012]";
    $.mask.definitions.N = "[012345]";
    $.mask.definitions.n = "[0123456789]";
    a = document.getElementsByTagName("input");
    for (var b = 0; b < a.length; b++)
        "text" == a[b].type && ("F" == a[b].id.substring(0, 1) && buscarSF(a[b].id) && $("#" + a[b].id).datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                buttonImageOnly: !0,
                dateFormat: "yy-mm-dd",
                changeMonth: !0,
                changeYear: !0,
                yearRange: "-90:+10"
            }), "FGRID" == a[b].id.substring(0, 5) && $("#" + a[b].id).datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                buttonImageOnly: !0,
                dateFormat: "yy-mm-dd",
                changeMonth: !0,
                changeYear: !0,
                yearRange: "-90:+10"
            }), "H" == a[b].id.substring(0, 1) && $("#" + a[b].id).mask("Hn:Nn:Nn"), "M" == a[b].id.substring(0, 1) && ($("#TooltipT" + a[b].id.substring(1)).tooltip({
                    track: !0,
                    delay: 0,
                    showURL: !1,
                    showBody: " - ",
                    extraClass: "globo",
                    fixPNG: !0,
                    opacity: .95,
                    left: -120
                }), $("#M" + a[b].id.substring(1)).autocomplete("../../HCE/procesos/autocompletar.php?consulta=" + document.getElementById("query" + a[b].id.substring(1)).value, {
                    max: 100,
                    minChars: 0,
                    scroll: !0,
                    scrollHeight: 300,
                    matchContains: !0,
                    selectFirst: !0,
                    autoFill: !1,
                    formatResult: function (a, b) {
                        return b
                    }
                }).result(function (a, b) {
                    var c = document.getElementById("selAuto" + this.id.substring(1)),
                    d = document.createElement("option");
                    b += ".";
                    if (document.getElementById("SimMul" +
                            this.id.substring(1))) {
                        var e = document.getElementById("SimMul" + this.id.substring(1)).value,
                        h = document.getElementById("Tcual" + this.id.substring(1)).value.split(","),
                        l = h[0],
                        p = h[1],
                        k = document.getElementById("cual" + this.id.substring(1)),
                        m = document.getElementById("TTipe" + this.id.substring(1)).value;
                        if ("on" == k.value && 2 < h.length) {
                            if (2 < h.length && (eval("var radioR = document.forms.HCE6.RT" + this.id.substring(1)), radioR))
                                for (j = 0; j < radioR.length; j++)
                                    if (1 == radioR[j].checked) {
                                        b = radioR[j].value + "-" + b;
                                        break
                                    }
                            "" != l &&
                            "" != p && "N" != m && "QN" != m && (b = 0 == c.options.length ? l + "-" + b : p + "-" + b)
                        }
                    }
                    d.setAttribute("value", b);
                    if ("Microsoft Internet Explorer" == navigator.appName) {
                        if (0 == c.options.length && "S" == e || "M" == e)
                            d.setAttribute("text", b), c.add(d)
                    } else if (0 == c.options.length && "S" == e || "M" == e)
                        d.innerHTML = b, c.add(d, null);
                    $("#M" + this.id.substring(1)).focus();
                    $("#M" + this.id.substring(1)).select()
                })))
}
function init_calendar() {
    for (var a = document.getElementsByTagName("input"), b = 0; b < a.length; b++)
        "text" == a[b].type && "F" == a[b].id.substring(0, 1) && $("#" + a[b].id).datepicker({
            showOn: "button",
            buttonImage: "../../images/medical/root/calendar.gif",
            buttonImageOnly: !0,
            dateFormat: "yy-mm-dd",
            changeMonth: !0,
            changeYear: !0,
            yearRange: "-90:+10"
        })
}
function increaseFontSize() {
    var a = document.getElementById("wtex"),
    b = a.style.fontSize ? parseInt(a.style.fontSize.replace("px", "")) : 12;
    20 > b && (b += 1);
    a.style.fontSize = b + "pt"
}
function decreaseFontSize() {
    var a = document.getElementById("wtex"),
    b = a.style.fontSize ? parseInt(a.style.fontSize.replace("px", "")) : 12;
    7 < b && --b;
    a.style.fontSize = b + "pt"
};
