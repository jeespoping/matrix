(function(e){function o(o){for(var t,n,u=o[0],c=o[1],s=o[2],l=0,d=[];l<u.length;l++)n=u[l],Object.prototype.hasOwnProperty.call(i,n)&&i[n]&&d.push(i[n][0]),i[n]=0;for(t in c)Object.prototype.hasOwnProperty.call(c,t)&&(e[t]=c[t]);m&&m(o);while(d.length)d.shift()();return a.push.apply(a,s||[]),r()}function r(){for(var e,o=0;o<a.length;o++){for(var r=a[o],t=!0,n=1;n<r.length;n++){var u=r[n];0!==i[u]&&(t=!1)}t&&(a.splice(o--,1),e=c(c.s=r[0]))}return e}var t={},n={app:0},i={app:0},a=[];function u(e){return c.p+"js/"+({about:"about"}[e]||e)+"."+{about:"af1917f0"}[e]+".js"}function c(o){if(t[o])return t[o].exports;var r=t[o]={i:o,l:!1,exports:{}};return e[o].call(r.exports,r,r.exports,c),r.l=!0,r.exports}c.e=function(e){var o=[],r={about:1};n[e]?o.push(n[e]):0!==n[e]&&r[e]&&o.push(n[e]=new Promise((function(o,r){for(var t="css/"+({about:"about"}[e]||e)+"."+{about:"e9e8bb91"}[e]+".css",i=c.p+t,a=document.getElementsByTagName("link"),u=0;u<a.length;u++){var s=a[u],l=s.getAttribute("data-href")||s.getAttribute("href");if("stylesheet"===s.rel&&(l===t||l===i))return o()}var d=document.getElementsByTagName("style");for(u=0;u<d.length;u++){s=d[u],l=s.getAttribute("data-href");if(l===t||l===i)return o()}var m=document.createElement("link");m.rel="stylesheet",m.type="text/css",m.onload=o,m.onerror=function(o){var t=o&&o.target&&o.target.src||i,a=new Error("Loading CSS chunk "+e+" failed.\n("+t+")");a.code="CSS_CHUNK_LOAD_FAILED",a.request=t,delete n[e],m.parentNode.removeChild(m),r(a)},m.href=i;var g=document.getElementsByTagName("head")[0];g.appendChild(m)})).then((function(){n[e]=0})));var t=i[e];if(0!==t)if(t)o.push(t[2]);else{var a=new Promise((function(o,r){t=i[e]=[o,r]}));o.push(t[2]=a);var s,l=document.createElement("script");l.charset="utf-8",l.timeout=120,c.nc&&l.setAttribute("nonce",c.nc),l.src=u(e);var d=new Error;s=function(o){l.onerror=l.onload=null,clearTimeout(m);var r=i[e];if(0!==r){if(r){var t=o&&("load"===o.type?"missing":o.type),n=o&&o.target&&o.target.src;d.message="Loading chunk "+e+" failed.\n("+t+": "+n+")",d.name="ChunkLoadError",d.type=t,d.request=n,r[1](d)}i[e]=void 0}};var m=setTimeout((function(){s({type:"timeout",target:l})}),12e4);l.onerror=l.onload=s,document.head.appendChild(l)}return Promise.all(o)},c.m=e,c.c=t,c.d=function(e,o,r){c.o(e,o)||Object.defineProperty(e,o,{enumerable:!0,get:r})},c.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},c.t=function(e,o){if(1&o&&(e=c(e)),8&o)return e;if(4&o&&"object"===typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(c.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&o&&"string"!=typeof e)for(var t in e)c.d(r,t,function(o){return e[o]}.bind(null,t));return r},c.n=function(e){var o=e&&e.__esModule?function(){return e["default"]}:function(){return e};return c.d(o,"a",o),o},c.o=function(e,o){return Object.prototype.hasOwnProperty.call(e,o)},c.p="/matrix/ips/procesos/turnero/",c.oe=function(e){throw console.error(e),e};var s=window["webpackJsonp"]=window["webpackJsonp"]||[],l=s.push.bind(s);s.push=o,s=s.slice();for(var d=0;d<s.length;d++)o(s[d]);var m=l;a.push([0,"chunk-vendors"]),r()})({0:function(e,o,r){e.exports=r("56d7")},"034f":function(e,o,r){"use strict";r("85ec")},"149a":function(e,o,r){e.exports=r.p+"img/cedulatrans.b6758273.png"},"26c6":function(e,o,r){e.exports=r.p+"img/tecladotrans.af2cc412.png"},"56d7":function(e,o,r){"use strict";r.r(o);r("e260"),r("e6cf"),r("cca6"),r("a79d");var t=r("2b0e"),n=function(){var e=this,o=e.$createElement,t=e._self._c||o;return t("v-app",{staticClass:"myFont",attrs:{id:"AppAtrilTurnero"}},[t("v-main",{staticClass:"white ma-0 pa-0 "},[t("v-row",{attrs:{align:"center",justify:"center"}},[t("v-col",{attrs:{cols:"12"}},[t("v-card",{attrs:{light:"",color:"withe",height:"800",elevation:"0"}},[t("router-view",{staticClass:"ma-0 pa-0"})],1)],1)],1),t("v-bottom-navigation",{attrs:{fixed:"",height:"100px",dark:"","background-color":"primary"}},[t("v-row",{attrs:{justify:"end"}},[t("v-btn",{attrs:{value:"recent"}},[t("v-img",{attrs:{src:r("9a92"),width:"150px"},on:{click:function(o){return e.IrHome()}}})],1)],1)],1)],1)],1)},i=[],a={data:function(){return{drawer:null,timer:""}},computed:{ConfigTurnero:function(){return this.$store.state.ConfigTurnero}},mounted:function(){console.log("Bienvenida"),this.$store.dispatch("inicializarTurnero"),document.addEventListener("click",(function(e){var o=document.getElementById("AppAtrilTurnero");document.mozFullScreen||document.webkitFullScreen||(o.mozRequestFullScreen?o.mozRequestFullScreen():o.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT))}),!1)},updated:function(){var e=document.getElementById("AppAtrilTurnero");document.mozFullScreen||document.webkitFullScreen||(e.mozRequestFullScreen?e.mozRequestFullScreen():e.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT))},methods:{redirect:function(e){this.$router.push(e)},IrHome:function(){this.redirect("/")},PantallaCompleta:function(){console.log("pantalla completa");var e=document.getElementById("AppAtrilTurnero");document.mozFullScreen||document.webkitFullScreen||(e.mozRequestFullScreen?e.mozRequestFullScreen():e.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT))}}},u=a,c=(r("034f"),r("2877")),s=Object(c["a"])(u,n,i,!1,null,null,null),l=s.exports,d=(r("d3b7"),r("3ca3"),r("ddb0"),r("8c4f"));r("bb51");t["default"].use(d["a"]);var m,g=[{path:"/",name:"Home",component:function(){return Promise.resolve().then(r.bind(null,"bb51"))}},{path:"/about",name:"About",component:function(){return r.e("about").then(r.bind(null,"f820"))}},{path:"/datos",name:"Datos",component:function(){return r.e("about").then(r.bind(null,"4a1e"))}},{path:"/tipoDocumento",name:"TipoDocumento",component:function(){return r.e("about").then(r.bind(null,"48ae"))}},{path:"/ingresoNombre",name:"IngresoNombre",component:function(){return r.e("about").then(r.bind(null,"0b8c"))}},{path:"/ingresoNombreModelo",name:"IngresoNombreModelo",component:function(){return r.e("about").then(r.bind(null,"9a08"))}},{path:"/generacionTurno",name:"GeneracionTurno",component:function(){return r.e("about").then(r.bind(null,"0d08"))}},{path:"/ingresoEdad",name:"IngresoEdad",component:function(){return r.e("about").then(r.bind(null,"6613"))}},{path:"/seleccioncategoria",name:"SeleccionCategoria",component:function(){return r.e("about").then(r.bind(null,"c314"))}},{path:"/seleccionprioridad",name:"SeleccionPrioridad",component:function(){return r.e("about").then(r.bind(null,"0f18"))}}],f=new d["a"]({routes:g}),p=f,h=r("ade3"),C=r("2f62"),T=r("bc3a"),b=r.n(T);t["default"].use(C["a"]);var v=new C["a"].Store({state:{Turno:(m={Turno:"SIN TURNO",TipoIdentificacion:"",NumeroIdentificacion:"",Nombre:"",Categoria:"",Edad:"",TipoEdad:"",Prioridad:"",ValidarExisteTurno:"",YaExisteTurnoHoy:""},Object(h["a"])(m,"Turno",""),Object(h["a"])(m,"FichoTurno",""),Object(h["a"])(m,"Error",""),Object(h["a"])(m,"MensajeError",""),m),ConfigTurnero:{CodigoTurnero:"",NombreTurnero:"",TieneLectorCedula:"",TieneIngresoManual:"",TieneCategorias:"",TienePrioridad:"",ValidarExisteTurno:"",MensajeBienvenida:"<b>Bienvenido</b><br> Cuidamos la vida a cada instante",MensajeLector:"SIN MENSAJE <br> DE LECTOR",MensajeIngresoManual:"SIN MENSAJE <br> DE INGRESO MANUAL",MensajeTiposDocumento:"SIN MENSAJE TIPO DOCUMENTO",MensajeDatosPersonales:"SIN MENSAJE DATOS PERSONALES",MensajeCategorias:"SIN MENSAJE CATEGORIAS",MensajeTurnoGenerado:"",MensajeSinTipoDocumento:"SIN MENSAJE SIN TIPO DOCUMENTO",MensajeSinNumeroDocumento:"SIN MENSAJE SIN NUMERO DOCUMENTO",MensajeSinNombre:"SIN MENSAJE SIN NOMBRE",MensajeSinEdad:"SIN MENSAJE SIN EDAD",MensajeSinTipoEdad:"",MensajeSinCategoria:"SIN MENSAJE SIN CATEGORIA",Error:"",MensajeError:"",TiposDocumento:[{Nombre:"TIPO DOC1",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC2",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC3",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC4",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC5",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC6",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline"}],Categorias:[{Nombre:"CAT1",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT2",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT3",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT4",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT5",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT6",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline"}],Prioridades:[{Nombre:"P1",Codigo:"006",Color:"white",EsPrioridad:!1,Icono:"mdi-human-pregnant"},{Nombre:"P2",Codigo:"003",Color:"white",EsPrioridad:!0,Icono:"mdi-human-pregnant"},{Nombre:"P3",Codigo:"01",Color:"white",EsPrioridad:!0,Icono:"mdi-human-cane"},{Nombre:"P4",Codigo:"002",Color:"white",EsPrioridad:!0,Icono:"mdi-human-wheelchair"},{Nombre:"P5",Codigo:"005",Color:"white",EsPrioridad:!0,Icono:"mdi-human-male-child"}]},Prueba:"Prueba vuex"},getters:{},mutations:{setConfigTurnero:function(e,o){e.ConfigTurnero=o},changePrueba:function(e,o){e.Prueba=o},seleccionarCategoria:function(e,o){for(var r=0;r<e.ConfigTurnero.Categorias.length;r+=1)console.log(e.ConfigTurnero.Categorias[r].Codigo+"-"+o.Codigo),e.ConfigTurnero.Categorias[r].Codigo==o.Codigo?(e.ConfigTurnero.Categorias[r].Color="white",e.ConfigTurnero.Categorias[r].Clase="m-botonSeleccionado text-h6 headline pt-4",e.Turno.Categoria=o.Codigo):(e.ConfigTurnero.Categorias[r].Clase="m-boton text-h6 headline pt-4",e.ConfigTurnero.Categorias[r].Color="primary")},seleccionarTipoDocumento:function(e,o){for(var r=0;r<e.ConfigTurnero.TiposDocumento.length;r+=1)console.log(e.ConfigTurnero.TiposDocumento[r].Codigo+"-"+o.Codigo),e.ConfigTurnero.TiposDocumento[r].Codigo==o.Codigo?(e.ConfigTurnero.TiposDocumento[r].Color="white",e.ConfigTurnero.TiposDocumento[r].Clase="m-botonSeleccionado text-h6 headline pt-4",e.Turno.TipoIdentificacion=o.Codigo):(e.ConfigTurnero.TiposDocumento[r].Clase="m-boton text-h6 headline pt-4",e.ConfigTurnero.TiposDocumento[r].Color="primary")},seleccionarPrioridad:function(e,o){for(var r=0;r<e.ConfigTurnero.Prioridades.length;r+=1)console.log(e.ConfigTurnero.Prioridades[r].Codigo+"-"+o.Codigo),e.ConfigTurnero.Prioridades[r].Codigo==o.Codigo?(console.log("codigo encontrado"),e.ConfigTurnero.Prioridades[r].Color="primary",e.Turno.Prioridad=o.Codigo,console.log("Prioridad:"+e.Turno.Prioridad),console.log("Prioridad turno:"+e.Turno.Prioridad),console.log(e.Turno)):e.ConfigTurnero.Prioridades[r].Color="white"},changeTurno:function(e,o){e.Turno=o},changeTurnoTurno:function(e,o){e.Turno.Turno=o},changeTurnoTipoIdentificacion:function(e,o){e.Turno.TipoIdentificacion=o},changeTurnoNumeroIdentificacion:function(e,o){e.Turno.NumeroIdentificacion=o},changeTurnoNombre:function(e,o){e.Turno.Nombre=o},changeTurnoCategoria:function(e,o){e.Turno.Categoria=o},changeTurnoEdad:function(e,o){e.Turno.Edad=o},changeTurnoTipoEdad:function(e,o){e.Turno.TipoEdad=o},changeTurnoPrioridad:function(e,o){e.Turno.Prioridad=o},inicializarTurno:function(e,o){e.Turno.Prioridad="",e.Turno.Edad="",e.Turno.Categoria="",e.Turno.TipoIdentificacion="",e.Turno.NumeroIdentificacion="",e.Turno.Turno="SIN TURNO",e.Turno.Nombre="";for(var r=0;r<e.ConfigTurnero.Prioridades.length;r+=1)e.ConfigTurnero.Prioridades[r].Color="white";for(r=0;r<e.ConfigTurnero.TiposDocumento.length;r+=1)e.ConfigTurnero.TiposDocumento[r].Color="primary",e.ConfigTurnero.TiposDocumento[r].Clase="m-boton text-h6 headline pt-4";for(r=0;r<e.ConfigTurnero.Categorias.length;r+=1)e.ConfigTurnero.Categorias[r].Color="primary",e.ConfigTurnero.Categorias[r].Clase="m-boton text-h6 headline pt-4"}},actions:{changeTurno:function(e,o){setTimeout((function(){e.commit("changeTurno",o)}),2e3)},changePrueba:function(e,o){setTimeout((function(){e.commit("changePrueba",o)}),2e3)},inicializarTurnero:function(e){var o=e.commit;b.a.get("../obtenerconfiguracionturnero.php?wemp_pmla=01&codigoTurnero=09").then((function(e){o("setConfigTurnero",e.data)}))}}}),N=r("ce5b"),E=r.n(N);r("bf40");t["default"].use(E.a);var S=new E.a({theme:{options:{customProperties:!0},themes:{light:{primary:"#00B0CA",secondary:"#bed600",accent:"#FF7F00",error:"#FF0000",info:"#2196F3",success:"#00CB00",warning:"#FFC107"}}}}),x=(r("e792"),r("d5e8"),r("5363"),r("7898")),y=r.n(x);window.document.title;t["default"].use(y.a),t["default"].config.productionTip=!1,new t["default"]({router:p,store:v,vuetify:S,render:function(e){return e(l)}}).$mount("#app")},"5ced":function(e,o,r){},"85ec":function(e,o,r){},"9a92":function(e,o,r){e.exports=r.p+"img/logoaunatrans.1238e842.png"},"9f59":function(e,o,r){e.exports=r.p+"img/corazontrans.3186bead.png"},bb51:function(e,o,r){"use strict";r.r(o);var t=function(){var e=this,o=e.$createElement,t=e._self._c||o;return t("div",{staticClass:"home",attrs:{tabindex:"0",id:"bodyPrincipal"}},[t("v-row",{attrs:{align:"center",justify:"center"}},[t("v-col",{attrs:{cols:"1"}},[t("v-card",{staticClass:"mx-auto rounded-br-xl ",attrs:{dark:"",color:"secondary",height:"120px",elevation:"0"}})],1),t("v-col",{attrs:{cols:"11"}},[t("v-card",{staticClass:"mx-auto rounded-bl-xl",attrs:{dark:"",color:"primary",height:"120px",elevation:"0"}},[t("v-row",{attrs:{align:"center",justify:"center"}},[t("v-col",{attrs:{cols:"1"}}),t("v-col",{staticClass:"pa-0",attrs:{cols:"1"}},[t("div",{staticClass:"pa-0",attrs:{id:"printMe"}},[t("v-img",{attrs:{src:r("9f59"),height:"70px"}})],1)]),t("v-col",{attrs:{cols:"9"}},[t("div",{staticClass:"text-center textoregular  pt-3"},[t("p",{domProps:{innerHTML:e._s(e.ConfigTurnero.MensajeBienvenida)}})])]),t("v-col",{attrs:{cols:"1"}})],1)],1)],1)],1),t("div",{staticStyle:{height:"75px"}}),t("v-row",[t("v-col",{attrs:{cols:"1"}}),t("v-col",{attrs:{cols:"5"}},[t("v-card",{staticClass:"mx-auto rounded-br-xl",attrs:{dark:"",color:"primary",height:"130px",elevation:"0","max-width":"550"}},[t("v-list-item-title",{staticClass:"textoregularmediano pa-7"},[t("p",{domProps:{innerHTML:e._s(e.ConfigTurnero.MensajeLector)}})])],1),t("v-card",{staticClass:"mx-auto rounded-tr-xl mt-4",attrs:{dark:"",color:"secondary",height:"330px",width:"550",elevation:"0"}},[t("v-img",{attrs:{src:r("149a")}})],1)],1),t("v-col",{attrs:{cols:"5"}},[t("v-card",{staticClass:"mx-auto rounded-br-xl",attrs:{dark:"",color:"primary",height:"130px",elevation:"0","max-width":"550"},on:{click:function(o){return e.SeleccionTipoDocumento()}}},[t("v-list-item-title",{staticClass:"textoregularmediano pa-7"},[t("p",{domProps:{innerHTML:e._s(e.ConfigTurnero.MensajeIngresoManual)}})])],1),t("v-card",{staticClass:"mx-auto rounded-tr-xl mt-4 ",attrs:{dark:"",color:"secondary",height:"330px",width:"550",elevation:"0"},on:{click:function(o){return e.SeleccionTipoDocumento()}}},[t("v-img",{attrs:{src:r("26c6")}})],1)],1),t("v-col",{attrs:{cols:"1"}})],1),t("v-row")],1)},n=[],i=r("1da1"),a=(r("96cf"),r("ac1f"),r("1276"),r("8ba4"),r("a9e3"),r("498a"),{created:function(){},data:function(){return{textoLector:""}},computed:{Turno:function(){return this.$store.state.Turno},ConfigTurnero:function(){return this.$store.state.ConfigTurnero}},updated:function(){},mounted:function(){this.$store.commit("inicializarTurno","");var e=this;window.addEventListener("keyup",(function(o){if(!(o.keyCode>=48&&o.keyCode<=90||"Tab"==o.key||"Enter"==o.key))return o.cancelBubble=!0,o.returnValue=!1,!1;switch(o.key){case"Tab":return e.textoLector=e.textoLector+"|",!1;case"Enter":var r=e.textoLector.split("|");if(12!=r.length&&13!=r.length||!Number.isInteger(1*r[0]))alert("Formato invalido"+r.length+"-"+r[0]);else{var t=r[1].substr(0,1);if(Number.isInteger(1*t)){e.$store.commit("changeTurnoNumeroIdentificacion",String.trim(r[0]+t));var n=trim(r[1].substr(1,r[1].length));e.$store.commit("changeTurnoNombre",trim(r[3])+" "+trim(r[4])+" "+n+" "+String.trim(r[2]));var i=r[6].substr(1,r[6].length)+r[7].substr(0,1),a=r[7].substr(1,1)+r[8].substr(0,1),u=r[8].substr(1,1)+r[9].substr(0,1);String.trim(i+"-"+a+"-"+u)}else{var c=r[3]+" "+r[4]+" "+r[1]+" "+r[2];c=c.toUpperCase(),e.$store.commit("changeTurnoNumeroIdentificacion",1*r[0]),e.$store.commit("changeTurnoNombre",c),e.$store.commit("changeTurnoEdad","53"),e.textoLector="";var s=String.trim(r[6]+"-"+r[7]+"-"+r[8]);console.log("FecNac:"+s),e.redirect("/IngresoNombreModelo")}e.textoLector=""}return e.textoLector="",o.cancelBubble=!0,o.returnValue=!1,!1;default:e.textoLector=e.textoLector+o.key;break}}));var o=document.getElementById("AppAtrilTurnero");document.mozFullScreen||document.webkitFullScreen||(o.mozRequestFullScreen?o.mozRequestFullScreen():o.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT))},methods:{redirect:function(e){this.$router.push(e)},SeleccionCategoria:function(){this.redirect("/SeleccionCategoria")},SeleccionTipoDocumento:function(){this.redirect("/IngresoNombreModelo")},print:function(){return Object(i["a"])(regeneratorRuntime.mark((function e(){return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:case"end":return e.stop()}}),e)})))()}}}),u=a,c=(r("cccb"),r("2877")),s=Object(c["a"])(u,t,n,!1,null,null,null);o["default"]=s.exports},cccb:function(e,o,r){"use strict";r("5ced")}});
//# sourceMappingURL=app.554cdb45.js.map