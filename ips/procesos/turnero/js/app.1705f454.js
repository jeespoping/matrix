(function(e){function o(o){for(var n,r,u=o[0],c=o[1],l=o[2],s=0,d=[];s<u.length;s++)r=u[s],Object.prototype.hasOwnProperty.call(i,r)&&i[r]&&d.push(i[r][0]),i[r]=0;for(n in c)Object.prototype.hasOwnProperty.call(c,n)&&(e[n]=c[n]);m&&m(o);while(d.length)d.shift()();return a.push.apply(a,l||[]),t()}function t(){for(var e,o=0;o<a.length;o++){for(var t=a[o],n=!0,r=1;r<t.length;r++){var u=t[r];0!==i[u]&&(n=!1)}n&&(a.splice(o--,1),e=c(c.s=t[0]))}return e}var n={},r={app:0},i={app:0},a=[];function u(e){return c.p+"js/"+({about:"about"}[e]||e)+"."+{about:"af1917f0"}[e]+".js"}function c(o){if(n[o])return n[o].exports;var t=n[o]={i:o,l:!1,exports:{}};return e[o].call(t.exports,t,t.exports,c),t.l=!0,t.exports}c.e=function(e){var o=[],t={about:1};r[e]?o.push(r[e]):0!==r[e]&&t[e]&&o.push(r[e]=new Promise((function(o,t){for(var n="css/"+({about:"about"}[e]||e)+"."+{about:"e9e8bb91"}[e]+".css",i=c.p+n,a=document.getElementsByTagName("link"),u=0;u<a.length;u++){var l=a[u],s=l.getAttribute("data-href")||l.getAttribute("href");if("stylesheet"===l.rel&&(s===n||s===i))return o()}var d=document.getElementsByTagName("style");for(u=0;u<d.length;u++){l=d[u],s=l.getAttribute("data-href");if(s===n||s===i)return o()}var m=document.createElement("link");m.rel="stylesheet",m.type="text/css",m.onload=o,m.onerror=function(o){var n=o&&o.target&&o.target.src||i,a=new Error("Loading CSS chunk "+e+" failed.\n("+n+")");a.code="CSS_CHUNK_LOAD_FAILED",a.request=n,delete r[e],m.parentNode.removeChild(m),t(a)},m.href=i;var g=document.getElementsByTagName("head")[0];g.appendChild(m)})).then((function(){r[e]=0})));var n=i[e];if(0!==n)if(n)o.push(n[2]);else{var a=new Promise((function(o,t){n=i[e]=[o,t]}));o.push(n[2]=a);var l,s=document.createElement("script");s.charset="utf-8",s.timeout=120,c.nc&&s.setAttribute("nonce",c.nc),s.src=u(e);var d=new Error;l=function(o){s.onerror=s.onload=null,clearTimeout(m);var t=i[e];if(0!==t){if(t){var n=o&&("load"===o.type?"missing":o.type),r=o&&o.target&&o.target.src;d.message="Loading chunk "+e+" failed.\n("+n+": "+r+")",d.name="ChunkLoadError",d.type=n,d.request=r,t[1](d)}i[e]=void 0}};var m=setTimeout((function(){l({type:"timeout",target:s})}),12e4);s.onerror=s.onload=l,document.head.appendChild(s)}return Promise.all(o)},c.m=e,c.c=n,c.d=function(e,o,t){c.o(e,o)||Object.defineProperty(e,o,{enumerable:!0,get:t})},c.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},c.t=function(e,o){if(1&o&&(e=c(e)),8&o)return e;if(4&o&&"object"===typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(c.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&o&&"string"!=typeof e)for(var n in e)c.d(t,n,function(o){return e[o]}.bind(null,n));return t},c.n=function(e){var o=e&&e.__esModule?function(){return e["default"]}:function(){return e};return c.d(o,"a",o),o},c.o=function(e,o){return Object.prototype.hasOwnProperty.call(e,o)},c.p="/matrix/ips/procesos/turnero/",c.oe=function(e){throw console.error(e),e};var l=window["webpackJsonp"]=window["webpackJsonp"]||[],s=l.push.bind(l);l.push=o,l=l.slice();for(var d=0;d<l.length;d++)o(l[d]);var m=s;a.push([0,"chunk-vendors"]),t()})({0:function(e,o,t){e.exports=t("56d7")},"034f":function(e,o,t){"use strict";t("85ec")},"149a":function(e,o,t){e.exports=t.p+"img/cedulatrans.b6758273.png"},"26c6":function(e,o,t){e.exports=t.p+"img/tecladotrans.af2cc412.png"},"56d7":function(e,o,t){"use strict";t.r(o);t("e260"),t("e6cf"),t("cca6"),t("a79d");var n=t("2b0e"),r=function(){var e=this,o=e.$createElement,n=e._self._c||o;return n("v-app",{staticClass:"myFont",attrs:{id:"AppAtrilTurnero"}},[n("v-main",{staticClass:"white ma-0 pa-0 "},[n("v-row",{attrs:{align:"center",justify:"center"}},[n("v-col",{attrs:{cols:"12"}},[n("v-card",{attrs:{light:"",color:"withe",height:"800",elevation:"0"}},[n("router-view",{staticClass:"ma-0 pa-0"})],1)],1)],1),n("v-bottom-navigation",{attrs:{fixed:"",height:"100px",dark:"","background-color":"primary"}},[n("v-row",{attrs:{justify:"end"}},[n("v-btn",{attrs:{value:"recent"}},[n("v-img",{attrs:{src:t("9a92"),width:"150px"},on:{click:function(o){return e.IrHome()}}})],1)],1)],1)],1)],1)},i=[],a={data:function(){return{drawer:null,timer:""}},computed:{ConfigTurnero:function(){return this.$store.state.ConfigTurnero}},mounted:function(){console.log("Bienvenida"),this.$store.dispatch("inicializarTurnero"),this.timer=setInterval(this.PantallaCompleta,1e3),document.addEventListener("mozvisibilitychange",(function(e){console.log("lostfocus");var o=document.getElementById("AppAtrilTurnero");document.mozFullScreen||document.webkitFullScreen||(o.mozRequestFullScreen?o.mozRequestFullScreen():o.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT))}),!1),document.addEventListener("click2",(function(e){var o=document.getElementById("AppAtrilTurnero");document.mozFullScreen||document.webkitFullScreen||(o.mozRequestFullScreen?o.mozRequestFullScreen():o.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT))}),!1)},updated:function(){var e=document.getElementById("AppAtrilTurnero");document.mozFullScreen||document.webkitFullScreen||(e.mozRequestFullScreen?e.mozRequestFullScreen():e.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT))},methods:{redirect:function(e){this.$router.push(e)},IrHome:function(){this.redirect("/")},PantallaCompleta:function(){console.log("pantalla completa");var e=document.getElementById("AppAtrilTurnero");document.mozFullScreen||document.webkitFullScreen||(e.mozRequestFullScreen?e.mozRequestFullScreen():e.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT))}}},u=a,c=(t("034f"),t("2877")),l=Object(c["a"])(u,r,i,!1,null,null,null),s=l.exports,d=(t("d3b7"),t("3ca3"),t("ddb0"),t("8c4f"));t("bb51");n["default"].use(d["a"]);var m,g=[{path:"/",name:"Home",component:function(){return Promise.resolve().then(t.bind(null,"bb51"))}},{path:"/about",name:"About",component:function(){return t.e("about").then(t.bind(null,"f820"))}},{path:"/datos",name:"Datos",component:function(){return t.e("about").then(t.bind(null,"4a1e"))}},{path:"/tipoDocumento",name:"TipoDocumento",component:function(){return t.e("about").then(t.bind(null,"48ae"))}},{path:"/ingresoNombre",name:"IngresoNombre",component:function(){return t.e("about").then(t.bind(null,"0b8c"))}},{path:"/ingresoNombreModelo",name:"IngresoNombreModelo",component:function(){return t.e("about").then(t.bind(null,"9a08"))}},{path:"/generacionTurno",name:"GeneracionTurno",component:function(){return t.e("about").then(t.bind(null,"0d08"))}},{path:"/ingresoEdad",name:"IngresoEdad",component:function(){return t.e("about").then(t.bind(null,"6613"))}},{path:"/seleccioncategoria",name:"SeleccionCategoria",component:function(){return t.e("about").then(t.bind(null,"c314"))}},{path:"/seleccionprioridad",name:"SeleccionPrioridad",component:function(){return t.e("about").then(t.bind(null,"0f18"))}}],f=new d["a"]({routes:g}),p=f,h=t("ade3"),C=t("2f62"),T=t("bc3a"),b=t.n(T);n["default"].use(C["a"]);var v=new C["a"].Store({state:{Turno:(m={Turno:"SIN TURNO",TipoIdentificacion:"",NumeroIdentificacion:"",Nombre:"",Categoria:"",Edad:"",TipoEdad:"",Prioridad:"",ValidarExisteTurno:"",YaExisteTurnoHoy:""},Object(h["a"])(m,"Turno",""),Object(h["a"])(m,"FichoTurno",""),Object(h["a"])(m,"Error",""),Object(h["a"])(m,"MensajeError",""),m),ConfigTurnero:{CodigoTurnero:"",NombreTurnero:"",TieneLectorCedula:"",TieneIngresoManual:"",TieneCategorias:"",TienePrioridad:"",ValidarExisteTurno:"",MensajeBienvenida:"<b>Bienvenido</b><br> Cuidamos la vida a cada instante",MensajeLector:"SIN MENSAJE <br> DE LECTOR",MensajeIngresoManual:"SIN MENSAJE <br> DE INGRESO MANUAL",MensajeTiposDocumento:"SIN MENSAJE TIPO DOCUMENTO",MensajeDatosPersonales:"SIN MENSAJE DATOS PERSONALES",MensajeCategorias:"SIN MENSAJE CATEGORIAS",MensajeTurnoGenerado:"",MensajeSinTipoDocumento:"SIN MENSAJE SIN TIPO DOCUMENTO",MensajeSinNumeroDocumento:"SIN MENSAJE SIN NUMERO DOCUMENTO",MensajeSinNombre:"SIN MENSAJE SIN NOMBRE",MensajeSinEdad:"SIN MENSAJE SIN EDAD",MensajeSinTipoEdad:"",MensajeSinCategoria:"SIN MENSAJE SIN CATEGORIA",Error:"",MensajeError:"",TiposDocumento:[{Nombre:"TIPO DOC1",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC2",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC3",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC4",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC5",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC6",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline"}],Categorias:[{Nombre:"CAT1",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT2",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT3",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT4",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT5",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT6",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline"}],Prioridades:[{Nombre:"P1",Codigo:"006",Color:"white",EsPrioridad:!1,Icono:"mdi-human-pregnant"},{Nombre:"P2",Codigo:"003",Color:"white",EsPrioridad:!0,Icono:"mdi-human-pregnant"},{Nombre:"P3",Codigo:"01",Color:"white",EsPrioridad:!0,Icono:"mdi-human-cane"},{Nombre:"P4",Codigo:"002",Color:"white",EsPrioridad:!0,Icono:"mdi-human-wheelchair"},{Nombre:"P5",Codigo:"005",Color:"white",EsPrioridad:!0,Icono:"mdi-human-male-child"}]},Prueba:"Prueba vuex"},getters:{},mutations:{setConfigTurnero:function(e,o){e.ConfigTurnero=o},changePrueba:function(e,o){e.Prueba=o},seleccionarCategoria:function(e,o){for(var t=0;t<e.ConfigTurnero.Categorias.length;t+=1)console.log(e.ConfigTurnero.Categorias[t].Codigo+"-"+o.Codigo),e.ConfigTurnero.Categorias[t].Codigo==o.Codigo?(e.ConfigTurnero.Categorias[t].Color="white",e.ConfigTurnero.Categorias[t].Clase="m-botonSeleccionado text-h6 headline pt-4",e.Turno.Categoria=o.Codigo):(e.ConfigTurnero.Categorias[t].Clase="m-boton text-h6 headline pt-4",e.ConfigTurnero.Categorias[t].Color="primary")},seleccionarTipoDocumento:function(e,o){for(var t=0;t<e.ConfigTurnero.TiposDocumento.length;t+=1)console.log(e.ConfigTurnero.TiposDocumento[t].Codigo+"-"+o.Codigo),e.ConfigTurnero.TiposDocumento[t].Codigo==o.Codigo?(e.ConfigTurnero.TiposDocumento[t].Color="white",e.ConfigTurnero.TiposDocumento[t].Clase="m-botonSeleccionado text-h6 headline pt-4",e.Turno.TipoIdentificacion=o.Codigo):(e.ConfigTurnero.TiposDocumento[t].Clase="m-boton text-h6 headline pt-4",e.ConfigTurnero.TiposDocumento[t].Color="primary")},seleccionarPrioridad:function(e,o){for(var t=0;t<e.ConfigTurnero.Prioridades.length;t+=1)console.log(e.ConfigTurnero.Prioridades[t].Codigo+"-"+o.Codigo),e.ConfigTurnero.Prioridades[t].Codigo==o.Codigo?(console.log("codigo encontrado"),e.ConfigTurnero.Prioridades[t].Color="primary",e.Turno.Prioridad=o.Codigo,console.log("Prioridad:"+e.Turno.Prioridad),console.log("Prioridad turno:"+e.Turno.Prioridad),console.log(e.Turno)):e.ConfigTurnero.Prioridades[t].Color="white"},changeTurno:function(e,o){e.Turno=o},changeTurnoTurno:function(e,o){e.Turno.Turno=o},changeTurnoTipoIdentificacion:function(e,o){e.Turno.TipoIdentificacion=o},changeTurnoNumeroIdentificacion:function(e,o){e.Turno.NumeroIdentificacion=o},changeTurnoNombre:function(e,o){e.Turno.Nombre=o},changeTurnoCategoria:function(e,o){e.Turno.Categoria=o},changeTurnoEdad:function(e,o){e.Turno.Edad=o},changeTurnoTipoEdad:function(e,o){e.Turno.TipoEdad=o},changeTurnoPrioridad:function(e,o){e.Turno.Prioridad=o},inicializarTurno:function(e,o){e.Turno.Prioridad="",e.Turno.Edad="",e.Turno.Categoria="",e.Turno.TipoIdentificacion="",e.Turno.NumeroIdentificacion="",e.Turno.Turno="SIN TURNO",e.Turno.Nombre="";for(var t=0;t<e.ConfigTurnero.Prioridades.length;t+=1)e.ConfigTurnero.Prioridades[t].Color="white";for(t=0;t<e.ConfigTurnero.TiposDocumento.length;t+=1)e.ConfigTurnero.TiposDocumento[t].Color="primary",e.ConfigTurnero.TiposDocumento[t].Clase="m-boton text-h6 headline pt-4";for(t=0;t<e.ConfigTurnero.Categorias.length;t+=1)e.ConfigTurnero.Categorias[t].Color="primary",e.ConfigTurnero.Categorias[t].Clase="m-boton text-h6 headline pt-4"}},actions:{changeTurno:function(e,o){setTimeout((function(){e.commit("changeTurno",o)}),2e3)},changePrueba:function(e,o){setTimeout((function(){e.commit("changePrueba",o)}),2e3)},inicializarTurnero:function(e){var o=e.commit;b.a.get("../obtenerconfiguracionturnero.php?wemp_pmla=01&codigoTurnero=09").then((function(e){o("setConfigTurnero",e.data)}))}}}),E=t("ce5b"),N=t.n(E);t("bf40");n["default"].use(N.a);var S=new N.a({theme:{options:{customProperties:!0},themes:{light:{primary:"#00B0CA",secondary:"#bed600",accent:"#FF7F00",error:"#FF0000",info:"#2196F3",success:"#00CB00",warning:"#FFC107"}}}}),x=(t("e792"),t("d5e8"),t("5363"),t("7898")),y=t.n(x);window.document.title;n["default"].use(y.a),n["default"].config.productionTip=!1,new n["default"]({router:p,store:v,vuetify:S,render:function(e){return e(s)}}).$mount("#app")},"5ced":function(e,o,t){},"85ec":function(e,o,t){},"9a92":function(e,o,t){e.exports=t.p+"img/logoaunatrans.1238e842.png"},"9f59":function(e,o,t){e.exports=t.p+"img/corazontrans.3186bead.png"},bb51:function(e,o,t){"use strict";t.r(o);var n=function(){var e=this,o=e.$createElement,n=e._self._c||o;return n("div",{staticClass:"home",attrs:{tabindex:"0",id:"bodyPrincipal"}},[n("v-row",{attrs:{align:"center",justify:"center"}},[n("v-col",{attrs:{cols:"1"}},[n("v-card",{staticClass:"mx-auto rounded-br-xl ",attrs:{dark:"",color:"secondary",height:"120px",elevation:"0"}})],1),n("v-col",{attrs:{cols:"11"}},[n("v-card",{staticClass:"mx-auto rounded-bl-xl",attrs:{dark:"",color:"primary",height:"120px",elevation:"0"}},[n("v-row",{attrs:{align:"center",justify:"center"}},[n("v-col",{attrs:{cols:"1"}}),n("v-col",{staticClass:"pa-0",attrs:{cols:"1"}},[n("div",{staticClass:"pa-0",attrs:{id:"printMe"}},[n("v-img",{attrs:{src:t("9f59"),height:"70px"}})],1)]),n("v-col",{attrs:{cols:"9"}},[n("div",{staticClass:"text-center textoregular  pt-3"},[n("p",{domProps:{innerHTML:e._s(e.ConfigTurnero.MensajeBienvenida)}})])]),n("v-col",{attrs:{cols:"1"}})],1)],1)],1)],1),n("div",{staticStyle:{height:"75px"}}),n("v-row",[n("v-col",{attrs:{cols:"1"}}),n("v-col",{attrs:{cols:"5"}},[n("v-card",{staticClass:"mx-auto rounded-br-xl",attrs:{dark:"",color:"primary",height:"130px",elevation:"0","max-width":"550"}},[n("v-list-item-title",{staticClass:"textoregularmediano pa-7"},[n("p",{domProps:{innerHTML:e._s(e.ConfigTurnero.MensajeLector)}})])],1),n("v-card",{staticClass:"mx-auto rounded-tr-xl mt-4",attrs:{dark:"",color:"secondary",height:"330px",width:"550",elevation:"0"}},[n("v-img",{attrs:{src:t("149a")}})],1)],1),n("v-col",{attrs:{cols:"5"}},[n("v-card",{staticClass:"mx-auto rounded-br-xl",attrs:{dark:"",color:"primary",height:"130px",elevation:"0","max-width":"550"},on:{click:function(o){return e.SeleccionTipoDocumento()}}},[n("v-list-item-title",{staticClass:"textoregularmediano pa-7"},[n("p",{domProps:{innerHTML:e._s(e.ConfigTurnero.MensajeIngresoManual)}})])],1),n("v-card",{staticClass:"mx-auto rounded-tr-xl mt-4 ",attrs:{dark:"",color:"secondary",height:"330px",width:"550",elevation:"0"},on:{click:function(o){return e.SeleccionTipoDocumento()}}},[n("v-img",{attrs:{src:t("26c6")}})],1)],1),n("v-col",{attrs:{cols:"1"}})],1),n("v-row")],1)},r=[],i=t("1da1"),a=(t("96cf"),t("ac1f"),t("1276"),t("8ba4"),t("a9e3"),t("498a"),{created:function(){},data:function(){return{textoLector:""}},computed:{Turno:function(){return this.$store.state.Turno},ConfigTurnero:function(){return this.$store.state.ConfigTurnero}},updated:function(){},mounted:function(){this.$store.commit("inicializarTurno","");var e=this;window.addEventListener("keyup",(function(o){if(!(o.keyCode>=48&&o.keyCode<=90||"Tab"==o.key||"Enter"==o.key))return o.cancelBubble=!0,o.returnValue=!1,!1;switch(o.key){case"Tab":return e.textoLector=e.textoLector+"|",!1;case"Enter":var t=e.textoLector.split("|");if(12==t.length&&Number.isInteger(1*t[0])){var n=t[1].substr(0,1);if(Number.isInteger(1*n)){e.$store.commit("changeTurnoNumeroIdentificacion",String.trim(t[0]+n));var r=trim(t[1].substr(1,t[1].length));e.$store.commit("changeTurnoNombre",trim(t[3])+" "+trim(t[4])+" "+r+" "+String.trim(t[2]));var i=t[6].substr(1,t[6].length)+t[7].substr(0,1),a=t[7].substr(1,1)+t[8].substr(0,1),u=t[8].substr(1,1)+t[9].substr(0,1);String.trim(i+"-"+a+"-"+u)}else{var c=t[3]+" "+t[4]+" "+t[1]+" "+t[2];c=c.toUpperCase(),e.$store.commit("changeTurnoNumeroIdentificacion",1*t[0]),e.$store.commit("changeTurnoNombre",c),e.$store.commit("changeTurnoEdad","53"),e.textoLector="",e.redirect("/IngresoNombreModelo")}e.textoLector=""}else alert("Formato invalido"+t.length+"-"+t[0]);return e.textoLector="",o.cancelBubble=!0,o.returnValue=!1,!1;default:e.textoLector=e.textoLector+o.key;break}}));var o=document.getElementById("AppAtrilTurnero");document.mozFullScreen||document.webkitFullScreen||(o.mozRequestFullScreen?o.mozRequestFullScreen():o.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT))},methods:{redirect:function(e){this.$router.push(e)},SeleccionCategoria:function(){this.redirect("/SeleccionCategoria")},SeleccionTipoDocumento:function(){this.redirect("/IngresoNombreModelo")},print:function(){return Object(i["a"])(regeneratorRuntime.mark((function e(){return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:case"end":return e.stop()}}),e)})))()}}}),u=a,c=(t("cccb"),t("2877")),l=Object(c["a"])(u,n,r,!1,null,null,null);o["default"]=l.exports},cccb:function(e,o,t){"use strict";t("5ced")}});
//# sourceMappingURL=app.1705f454.js.map