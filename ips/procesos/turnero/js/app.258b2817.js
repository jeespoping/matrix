(function(o){function e(e){for(var r,t,u=e[0],c=e[1],s=e[2],l=0,d=[];l<u.length;l++)t=u[l],Object.prototype.hasOwnProperty.call(i,t)&&i[t]&&d.push(i[t][0]),i[t]=0;for(r in c)Object.prototype.hasOwnProperty.call(c,r)&&(o[r]=c[r]);m&&m(e);while(d.length)d.shift()();return a.push.apply(a,s||[]),n()}function n(){for(var o,e=0;e<a.length;e++){for(var n=a[e],r=!0,t=1;t<n.length;t++){var u=n[t];0!==i[u]&&(r=!1)}r&&(a.splice(e--,1),o=c(c.s=n[0]))}return o}var r={},t={app:0},i={app:0},a=[];function u(o){return c.p+"js/"+({about:"about"}[o]||o)+"."+{about:"af1917f0"}[o]+".js"}function c(e){if(r[e])return r[e].exports;var n=r[e]={i:e,l:!1,exports:{}};return o[e].call(n.exports,n,n.exports,c),n.l=!0,n.exports}c.e=function(o){var e=[],n={about:1};t[o]?e.push(t[o]):0!==t[o]&&n[o]&&e.push(t[o]=new Promise((function(e,n){for(var r="css/"+({about:"about"}[o]||o)+"."+{about:"e9e8bb91"}[o]+".css",i=c.p+r,a=document.getElementsByTagName("link"),u=0;u<a.length;u++){var s=a[u],l=s.getAttribute("data-href")||s.getAttribute("href");if("stylesheet"===s.rel&&(l===r||l===i))return e()}var d=document.getElementsByTagName("style");for(u=0;u<d.length;u++){s=d[u],l=s.getAttribute("data-href");if(l===r||l===i)return e()}var m=document.createElement("link");m.rel="stylesheet",m.type="text/css",m.onload=e,m.onerror=function(e){var r=e&&e.target&&e.target.src||i,a=new Error("Loading CSS chunk "+o+" failed.\n("+r+")");a.code="CSS_CHUNK_LOAD_FAILED",a.request=r,delete t[o],m.parentNode.removeChild(m),n(a)},m.href=i;var g=document.getElementsByTagName("head")[0];g.appendChild(m)})).then((function(){t[o]=0})));var r=i[o];if(0!==r)if(r)e.push(r[2]);else{var a=new Promise((function(e,n){r=i[o]=[e,n]}));e.push(r[2]=a);var s,l=document.createElement("script");l.charset="utf-8",l.timeout=120,c.nc&&l.setAttribute("nonce",c.nc),l.src=u(o);var d=new Error;s=function(e){l.onerror=l.onload=null,clearTimeout(m);var n=i[o];if(0!==n){if(n){var r=e&&("load"===e.type?"missing":e.type),t=e&&e.target&&e.target.src;d.message="Loading chunk "+o+" failed.\n("+r+": "+t+")",d.name="ChunkLoadError",d.type=r,d.request=t,n[1](d)}i[o]=void 0}};var m=setTimeout((function(){s({type:"timeout",target:l})}),12e4);l.onerror=l.onload=s,document.head.appendChild(l)}return Promise.all(e)},c.m=o,c.c=r,c.d=function(o,e,n){c.o(o,e)||Object.defineProperty(o,e,{enumerable:!0,get:n})},c.r=function(o){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(o,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(o,"__esModule",{value:!0})},c.t=function(o,e){if(1&e&&(o=c(o)),8&e)return o;if(4&e&&"object"===typeof o&&o&&o.__esModule)return o;var n=Object.create(null);if(c.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:o}),2&e&&"string"!=typeof o)for(var r in o)c.d(n,r,function(e){return o[e]}.bind(null,r));return n},c.n=function(o){var e=o&&o.__esModule?function(){return o["default"]}:function(){return o};return c.d(e,"a",e),e},c.o=function(o,e){return Object.prototype.hasOwnProperty.call(o,e)},c.p="/matrix/ips/procesos/turnero/",c.oe=function(o){throw console.error(o),o};var s=window["webpackJsonp"]=window["webpackJsonp"]||[],l=s.push.bind(s);s.push=e,s=s.slice();for(var d=0;d<s.length;d++)e(s[d]);var m=l;a.push([0,"chunk-vendors"]),n()})({0:function(o,e,n){o.exports=n("56d7")},"034f":function(o,e,n){"use strict";n("85ec")},"149a":function(o,e,n){o.exports=n.p+"img/cedulatrans.b6758273.png"},"26c6":function(o,e,n){o.exports=n.p+"img/tecladotrans.af2cc412.png"},"56d7":function(o,e,n){"use strict";n.r(e);n("e260"),n("e6cf"),n("cca6"),n("a79d");var r=n("2b0e"),t=function(){var o=this,e=o.$createElement,r=o._self._c||e;return r("v-app",{staticClass:"myFont",attrs:{id:"AppAtrilTurnero"}},[r("v-main",{staticClass:"white ma-0 pa-0 "},[r("v-row",{attrs:{align:"center",justify:"center"}},[r("v-col",{attrs:{cols:"12"}},[r("v-card",{attrs:{light:"",color:"withe",height:"800",elevation:"0"}},[r("router-view",{staticClass:"ma-0 pa-0"})],1)],1)],1),r("v-bottom-navigation",{attrs:{fixed:"",height:"100px",dark:"","background-color":"primary"}},[r("v-row",{attrs:{justify:"end"}},[r("v-btn",{attrs:{value:"recent"}},[r("v-img",{attrs:{src:n("9a92"),width:"150px"},on:{click:function(e){return o.IrHome()}}})],1)],1)],1)],1)],1)},i=[],a={data:function(){return{drawer:null}},computed:{ConfigTurnero:function(){return this.$store.state.ConfigTurnero}},mounted:function(){console.log("Bienvenida"),this.$store.dispatch("inicializarTurnero"),document.addEventListener("click",(function(o){var e=document.getElementById("AppAtrilTurnero");document.mozFullScreen||document.webkitFullScreen||(e.mozRequestFullScreen?e.mozRequestFullScreen():e.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT))}),!1)},updated:function(){},methods:{redirect:function(o){this.$router.push(o)},IrHome:function(){this.redirect("/")}}},u=a,c=(n("034f"),n("2877")),s=Object(c["a"])(u,t,i,!1,null,null,null),l=s.exports,d=(n("d3b7"),n("3ca3"),n("ddb0"),n("8c4f"));n("bb51");r["default"].use(d["a"]);var m,g=[{path:"/",name:"Home",component:function(){return Promise.resolve().then(n.bind(null,"bb51"))}},{path:"/about",name:"About",component:function(){return n.e("about").then(n.bind(null,"f820"))}},{path:"/datos",name:"Datos",component:function(){return n.e("about").then(n.bind(null,"4a1e"))}},{path:"/tipoDocumento",name:"TipoDocumento",component:function(){return n.e("about").then(n.bind(null,"48ae"))}},{path:"/ingresoNombre",name:"IngresoNombre",component:function(){return n.e("about").then(n.bind(null,"0b8c"))}},{path:"/ingresoNombreModelo",name:"IngresoNombreModelo",component:function(){return n.e("about").then(n.bind(null,"9a08"))}},{path:"/generacionTurno",name:"GeneracionTurno",component:function(){return n.e("about").then(n.bind(null,"0d08"))}},{path:"/ingresoEdad",name:"IngresoEdad",component:function(){return n.e("about").then(n.bind(null,"6613"))}},{path:"/seleccioncategoria",name:"SeleccionCategoria",component:function(){return n.e("about").then(n.bind(null,"c314"))}},{path:"/seleccionprioridad",name:"SeleccionPrioridad",component:function(){return n.e("about").then(n.bind(null,"0f18"))}}],f=new d["a"]({routes:g}),p=f,h=n("ade3"),C=n("2f62"),T=n("bc3a"),b=n.n(T);r["default"].use(C["a"]);var v=new C["a"].Store({state:{Turno:(m={Turno:"SIN TURNO",TipoIdentificacion:"",NumeroIdentificacion:"",Nombre:"",Categoria:"",Edad:"",TipoEdad:"",Prioridad:"",ValidarExisteTurno:"",YaExisteTurnoHoy:""},Object(h["a"])(m,"Turno",""),Object(h["a"])(m,"FichoTurno",""),Object(h["a"])(m,"Error",""),Object(h["a"])(m,"MensajeError",""),m),ConfigTurnero:{CodigoTurnero:"",NombreTurnero:"",TieneLectorCedula:"",TieneIngresoManual:"",TieneCategorias:"",TienePrioridad:"",ValidarExisteTurno:"",MensajeBienvenida:"<b>Bienvenido</b><br> Cuidamos la vida a cada instante",MensajeLector:"SIN MENSAJE <br> DE LECTOR",MensajeIngresoManual:"SIN MENSAJE <br> DE INGRESO MANUAL",MensajeTiposDocumento:"SIN MENSAJE TIPO DOCUMENTO",MensajeDatosPersonales:"SIN MENSAJE DATOS PERSONALES",MensajeCategorias:"SIN MENSAJE CATEGORIAS",MensajeTurnoGenerado:"",MensajeSinTipoDocumento:"SIN MENSAJE SIN TIPO DOCUMENTO",MensajeSinNumeroDocumento:"SIN MENSAJE SIN NUMERO DOCUMENTO",MensajeSinNombre:"SIN MENSAJE SIN NOMBRE",MensajeSinEdad:"SIN MENSAJE SIN EDAD",MensajeSinTipoEdad:"",MensajeSinCategoria:"SIN MENSAJE SIN CATEGORIA",Error:"",MensajeError:"",TiposDocumento:[{Nombre:"TIPO DOC1",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC2",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC3",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC4",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC5",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"TIPO DOC6",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline"}],Categorias:[{Nombre:"CAT1",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT2",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT3",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT4",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT5",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT6",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline"}],Prioridades:[{Nombre:"P1",Codigo:"006",Color:"white",EsPrioridad:!1,Icono:"mdi-human-pregnant"},{Nombre:"P2",Codigo:"003",Color:"white",EsPrioridad:!0,Icono:"mdi-human-pregnant"},{Nombre:"P3",Codigo:"01",Color:"white",EsPrioridad:!0,Icono:"mdi-human-cane"},{Nombre:"P4",Codigo:"002",Color:"white",EsPrioridad:!0,Icono:"mdi-human-wheelchair"},{Nombre:"P5",Codigo:"005",Color:"white",EsPrioridad:!0,Icono:"mdi-human-male-child"}]},Prueba:"Prueba vuex"},getters:{},mutations:{setConfigTurnero:function(o,e){o.ConfigTurnero=e},changePrueba:function(o,e){o.Prueba=e},seleccionarCategoria:function(o,e){for(var n=0;n<o.ConfigTurnero.Categorias.length;n+=1)console.log(o.ConfigTurnero.Categorias[n].Codigo+"-"+e.Codigo),o.ConfigTurnero.Categorias[n].Codigo==e.Codigo?(o.ConfigTurnero.Categorias[n].Color="white",o.ConfigTurnero.Categorias[n].Clase="m-botonSeleccionado text-h6 headline pt-4",o.Turno.Categoria=e.Codigo):(o.ConfigTurnero.Categorias[n].Clase="m-boton text-h6 headline pt-4",o.ConfigTurnero.Categorias[n].Color="primary")},seleccionarTipoDocumento:function(o,e){for(var n=0;n<o.ConfigTurnero.TiposDocumento.length;n+=1)console.log(o.ConfigTurnero.TiposDocumento[n].Codigo+"-"+e.Codigo),o.ConfigTurnero.TiposDocumento[n].Codigo==e.Codigo?(o.ConfigTurnero.TiposDocumento[n].Color="white",o.ConfigTurnero.TiposDocumento[n].Clase="m-botonSeleccionado text-h6 headline pt-4",o.Turno.TipoIdentificacion=e.Codigo):(o.ConfigTurnero.TiposDocumento[n].Clase="m-boton text-h6 headline pt-4",o.ConfigTurnero.TiposDocumento[n].Color="primary")},seleccionarPrioridad:function(o,e){for(var n=0;n<o.ConfigTurnero.Prioridades.length;n+=1)console.log(o.ConfigTurnero.Prioridades[n].Codigo+"-"+e.Codigo),o.ConfigTurnero.Prioridades[n].Codigo==e.Codigo?(console.log("codigo encontrado"),o.ConfigTurnero.Prioridades[n].Color="primary",o.Turno.Prioridad=e.Codigo,console.log("Prioridad:"+o.Turno.Prioridad),console.log("Prioridad turno:"+o.Turno.Prioridad),console.log(o.Turno)):o.ConfigTurnero.Prioridades[n].Color="white"},changeTurno:function(o,e){o.Turno=e},changeTurnoTurno:function(o,e){o.Turno.Turno=e},changeTurnoTipoIdentificacion:function(o,e){o.Turno.TipoIdentificacion=e},changeTurnoTipoIdentificacionTexto:function(o,e){for(var n=0;n<o.ConfigTurnero.TiposDocumento.length;n+=1)o.ConfigTurnero.TiposDocumento[n].Codigo==e?(o.ConfigTurnero.TiposDocumento[n].Color="white",o.ConfigTurnero.TiposDocumento[n].Clase="m-botonSeleccionado text-h6 headline pt-4",o.Turno.TipoIdentificacion=e):(o.ConfigTurnero.TiposDocumento[n].Clase="m-boton text-h6 headline pt-4",o.ConfigTurnero.TiposDocumento[n].Color="primary")},changeTurnoNumeroIdentificacion:function(o,e){o.Turno.NumeroIdentificacion=e},changeTurnoNombre:function(o,e){o.Turno.Nombre=e},changeTurnoCategoria:function(o,e){o.Turno.Categoria=e},changeTurnoEdad:function(o,e){o.Turno.Edad=e},changeTurnoTipoEdad:function(o,e){o.Turno.TipoEdad=e},changeTurnoPrioridad:function(o,e){o.Turno.Prioridad=e},inicializarTurno:function(o,e){o.Turno.Prioridad="",o.Turno.Edad="",o.Turno.Categoria="",o.Turno.TipoIdentificacion="",o.Turno.NumeroIdentificacion="",o.Turno.Turno="SIN TURNO",o.Turno.Nombre="";for(var n=0;n<o.ConfigTurnero.Prioridades.length;n+=1)o.ConfigTurnero.Prioridades[n].Color="white";for(n=0;n<o.ConfigTurnero.TiposDocumento.length;n+=1)o.ConfigTurnero.TiposDocumento[n].Color="primary",o.ConfigTurnero.TiposDocumento[n].Clase="m-boton text-h6 headline pt-4";for(n=0;n<o.ConfigTurnero.Categorias.length;n+=1)o.ConfigTurnero.Categorias[n].Color="primary",o.ConfigTurnero.Categorias[n].Clase="m-boton text-h6 headline pt-4"}},actions:{changeTurno:function(o,e){setTimeout((function(){o.commit("changeTurno",e)}),2e3)},changePrueba:function(o,e){setTimeout((function(){o.commit("changePrueba",e)}),2e3)},inicializarTurnero:function(o){var e=o.commit;b.a.get("../obtenerconfiguracionturnero.php?wemp_pmla=01&codigoTurnero=09").then((function(o){e("setConfigTurnero",o.data)}))}}}),N=n("ce5b"),x=n.n(N);n("bf40");r["default"].use(x.a);var E=new x.a({theme:{options:{customProperties:!0},themes:{light:{primary:"#00B0CA",secondary:"#bed600",accent:"#FF7F00",error:"#FF0000",info:"#2196F3",success:"#00CB00",warning:"#FFC107"}}}}),y=(n("e792"),n("d5e8"),n("5363"),n("7898")),S=n.n(y);window.document.title;r["default"].use(S.a),r["default"].config.productionTip=!1,new r["default"]({router:p,store:v,vuetify:E,render:function(o){return o(l)}}).$mount("#app")},"5ced":function(o,e,n){},"85ec":function(o,e,n){},"9a92":function(o,e,n){o.exports=n.p+"img/logoaunatrans.1238e842.png"},"9f59":function(o,e,n){o.exports=n.p+"img/corazontrans.3186bead.png"},bb51:function(o,e,n){"use strict";n.r(e);var r=function(){var o=this,e=o.$createElement,r=o._self._c||e;return r("div",{staticClass:"home",attrs:{tabindex:"0",id:"bodyPrincipal"}},[r("v-row",{attrs:{align:"center",justify:"center"}},[r("v-col",{attrs:{cols:"1"}},[r("v-card",{staticClass:"mx-auto rounded-br-xl ",attrs:{dark:"",color:"secondary",height:"120px",elevation:"0"}})],1),r("v-col",{attrs:{cols:"11"}},[r("v-card",{staticClass:"mx-auto rounded-bl-xl",attrs:{dark:"",color:"primary",height:"120px",elevation:"0"}},[r("v-row",{attrs:{align:"center",justify:"center"}},[r("v-col",{attrs:{cols:"1"}}),r("v-col",{staticClass:"pa-0",attrs:{cols:"1"}},[r("div",{staticClass:"pa-0",attrs:{id:"printMe"}},[r("v-img",{attrs:{src:n("9f59"),height:"70px"}})],1)]),r("v-col",{attrs:{cols:"9"}},[r("div",{staticClass:"text-center textoregular  pt-3"},[r("p",{domProps:{innerHTML:o._s(o.ConfigTurnero.MensajeBienvenida)}})])]),r("v-col",{attrs:{cols:"1"}})],1)],1)],1)],1),r("div",{staticStyle:{height:"75px"}}),r("v-row",[r("v-col",{attrs:{cols:"1"}}),r("v-col",{attrs:{cols:"5"}},[r("v-card",{staticClass:"mx-auto rounded-br-xl",attrs:{dark:"",color:"primary",height:"130px",elevation:"0","max-width":"550"}},[r("v-list-item-title",{staticClass:"textoregularmediano pa-7"},[r("p",{domProps:{innerHTML:o._s(o.ConfigTurnero.MensajeLector)}})])],1),r("v-card",{staticClass:"mx-auto rounded-tr-xl mt-4",attrs:{dark:"",color:"secondary",height:"330px",width:"550",elevation:"0"}},[r("v-img",{attrs:{src:n("149a")}})],1)],1),r("v-col",{attrs:{cols:"5"}},[r("v-card",{staticClass:"mx-auto rounded-br-xl",attrs:{dark:"",color:"primary",height:"130px",elevation:"0","max-width":"550"},on:{click:function(e){return o.SeleccionTipoDocumento()}}},[r("v-list-item-title",{staticClass:"textoregularmediano pa-7"},[r("p",{domProps:{innerHTML:o._s(o.ConfigTurnero.MensajeIngresoManual)}})])],1),r("v-card",{staticClass:"mx-auto rounded-tr-xl mt-4 ",attrs:{dark:"",color:"secondary",height:"330px",width:"550",elevation:"0"},on:{click:function(e){return o.SeleccionTipoDocumento()}}},[r("v-img",{attrs:{src:n("26c6")}})],1)],1),r("v-col",{attrs:{cols:"1"}})],1),r("v-row")],1)},t=[],i=n("1da1"),a=(n("96cf"),n("ac1f"),n("1276"),n("8ba4"),n("a9e3"),n("498a"),{created:function(){},data:function(){return{textoLector:""}},computed:{Turno:function(){return this.$store.state.Turno},ConfigTurnero:function(){return this.$store.state.ConfigTurnero}},mounted:function(){this.$store.commit("inicializarTurno","");var o=this;window.addEventListener("keyup",(function(e){if(!(e.keyCode>=48&&e.keyCode<=90||"Tab"==e.key||"Enter"==e.key))return e.cancelBubble=!0,e.returnValue=!1,!1;switch(e.key){case"Tab":return o.textoLector=o.textoLector+"|",!1;case"Enter":var n=o.textoLector.split("|");if(12==n.length||13==n.length){var r=n[1].substr(0,1);if(Number.isInteger(1*r)){o.$store.commit("changeTurnoNumeroIdentificacion",String.trim(n[0]+r));var t=trim(n[1].substr(1,n[1].length));o.$store.commit("changeTurnoNombre",trim(n[3])+" "+trim(n[4])+" "+t+" "+String.trim(n[2]));var i=n[6].substr(1,n[6].length)+n[7].substr(0,1),a=n[7].substr(1,1)+n[8].substr(0,1),u=n[8].substr(1,1)+n[9].substr(0,1);String.trim(i+"-"+a+"-"+u)}else{var c=n[3]+" "+n[4]+" "+n[1]+" "+n[2];c=c.toUpperCase(),o.$store.commit("changeTurnoNumeroIdentificacion",1*n[0]),o.$store.commit("changeTurnoNombre",c),o.$store.commit("changeTurnoTipoIdentificacionTexto","CC"),o.textoLector="";var s=n[6]+"-"+n[7]+"-"+n[8],l=new Date,d=new Date(s),m=l.getFullYear()-d.getFullYear(),g=l.getMonth()-d.getMonth();(g<0||0===g&&l.getDate()<d.getDate())&&m--,o.$store.commit("changeTurnoEdad",m),o.redirect("/IngresoNombreModelo")}o.textoLector=""}else alert("Formato invalido"+n.length+"-"+n[0]);return o.textoLector="",e.cancelBubble=!0,e.returnValue=!1,!1;default:o.textoLector=o.textoLector+e.key;break}}))},methods:{redirect:function(o){this.$router.push(o)},SeleccionCategoria:function(){this.redirect("/SeleccionCategoria")},SeleccionTipoDocumento:function(){this.redirect("/IngresoNombreModelo")},print:function(){return Object(i["a"])(regeneratorRuntime.mark((function o(){return regeneratorRuntime.wrap((function(o){while(1)switch(o.prev=o.next){case 0:case"end":return o.stop()}}),o)})))()}}}),u=a,c=(n("cccb"),n("2877")),s=Object(c["a"])(u,r,t,!1,null,null,null);e["default"]=s.exports},cccb:function(o,e,n){"use strict";n("5ced")}});
//# sourceMappingURL=app.258b2817.js.map