(function(o){function e(e){for(var t,n,l=e[0],u=e[1],s=e[2],c=0,d=[];c<l.length;c++)n=l[c],Object.prototype.hasOwnProperty.call(a,n)&&a[n]&&d.push(a[n][0]),a[n]=0;for(t in u)Object.prototype.hasOwnProperty.call(u,t)&&(o[t]=u[t]);m&&m(e);while(d.length)d.shift()();return i.push.apply(i,s||[]),r()}function r(){for(var o,e=0;e<i.length;e++){for(var r=i[e],t=!0,n=1;n<r.length;n++){var l=r[n];0!==a[l]&&(t=!1)}t&&(i.splice(e--,1),o=u(u.s=r[0]))}return o}var t={},n={app:0},a={app:0},i=[];function l(o){return u.p+"js/"+({about:"about"}[o]||o)+"."+{about:"a6a9485e"}[o]+".js"}function u(e){if(t[e])return t[e].exports;var r=t[e]={i:e,l:!1,exports:{}};return o[e].call(r.exports,r,r.exports,u),r.l=!0,r.exports}u.e=function(o){var e=[],r={about:1};n[o]?e.push(n[o]):0!==n[o]&&r[o]&&e.push(n[o]=new Promise((function(e,r){for(var t="css/"+({about:"about"}[o]||o)+"."+{about:"06102c51"}[o]+".css",a=u.p+t,i=document.getElementsByTagName("link"),l=0;l<i.length;l++){var s=i[l],c=s.getAttribute("data-href")||s.getAttribute("href");if("stylesheet"===s.rel&&(c===t||c===a))return e()}var d=document.getElementsByTagName("style");for(l=0;l<d.length;l++){s=d[l],c=s.getAttribute("data-href");if(c===t||c===a)return e()}var m=document.createElement("link");m.rel="stylesheet",m.type="text/css",m.onload=e,m.onerror=function(e){var t=e&&e.target&&e.target.src||a,i=new Error("Loading CSS chunk "+o+" failed.\n("+t+")");i.code="CSS_CHUNK_LOAD_FAILED",i.request=t,delete n[o],m.parentNode.removeChild(m),r(i)},m.href=a;var C=document.getElementsByTagName("head")[0];C.appendChild(m)})).then((function(){n[o]=0})));var t=a[o];if(0!==t)if(t)e.push(t[2]);else{var i=new Promise((function(e,r){t=a[o]=[e,r]}));e.push(t[2]=i);var s,c=document.createElement("script");c.charset="utf-8",c.timeout=120,u.nc&&c.setAttribute("nonce",u.nc),c.src=l(o);var d=new Error;s=function(e){c.onerror=c.onload=null,clearTimeout(m);var r=a[o];if(0!==r){if(r){var t=e&&("load"===e.type?"missing":e.type),n=e&&e.target&&e.target.src;d.message="Loading chunk "+o+" failed.\n("+t+": "+n+")",d.name="ChunkLoadError",d.type=t,d.request=n,r[1](d)}a[o]=void 0}};var m=setTimeout((function(){s({type:"timeout",target:c})}),12e4);c.onerror=c.onload=s,document.head.appendChild(c)}return Promise.all(e)},u.m=o,u.c=t,u.d=function(o,e,r){u.o(o,e)||Object.defineProperty(o,e,{enumerable:!0,get:r})},u.r=function(o){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(o,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(o,"__esModule",{value:!0})},u.t=function(o,e){if(1&e&&(o=u(o)),8&e)return o;if(4&e&&"object"===typeof o&&o&&o.__esModule)return o;var r=Object.create(null);if(u.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:o}),2&e&&"string"!=typeof o)for(var t in o)u.d(r,t,function(e){return o[e]}.bind(null,t));return r},u.n=function(o){var e=o&&o.__esModule?function(){return o["default"]}:function(){return o};return u.d(e,"a",e),e},u.o=function(o,e){return Object.prototype.hasOwnProperty.call(o,e)},u.p="/matrix/ips/procesos/turnero/",u.oe=function(o){throw console.error(o),o};var s=window["webpackJsonp"]=window["webpackJsonp"]||[],c=s.push.bind(s);s.push=e,s=s.slice();for(var d=0;d<s.length;d++)e(s[d]);var m=c;i.push([0,"chunk-vendors"]),r()})({0:function(o,e,r){o.exports=r("56d7")},"034f":function(o,e,r){"use strict";r("85ec")},"36da":function(o,e,r){"use strict";r("dd2b")},"56d7":function(o,e,r){"use strict";r.r(e);r("e260"),r("e6cf"),r("cca6"),r("a79d");var t=r("2b0e"),n=function(){var o=this,e=o.$createElement,t=o._self._c||e;return t("v-app",{staticClass:"myFont",attrs:{id:"AppAtrilTurnero"}},[t("v-main",{staticClass:"white ma-0 pa-0 "},[t("v-row",{attrs:{align:"center",justify:"center"}},[t("v-col",{attrs:{cols:"12"}},[t("v-card",{attrs:{light:"",color:"white",height:"800px",elevation:"0"}},[t("router-view",{staticClass:"ma-0 pa-0"})],1)],1)],1),"CarteleraUrgencias"!=o.Route.name?t("v-bottom-navigation",{attrs:{fixed:"",height:"100px",dark:"","background-color":"primary"}},[t("v-row",{attrs:{justify:"end"}},[t("v-btn",{attrs:{value:"recent"}},[t("v-img",{attrs:{src:r("9a92"),width:"150px"},on:{click:function(e){return o.IrHome()}}})],1)],1)],1):o._e()],1)],1)},a=[],i={data:function(){return{drawer:null}},computed:{ConfigTurnero:function(){return this.$store.state.ConfigTurnero},Route:function(){return this.$route}},mounted:function(){console.log("Mounted APP"),console.log(this.$route),console.log("Mounted AP Antes de inicializarturnero"),this.$store.dispatch("inicializarTurnero",{wemp_pmla:this.$route.query.wemp_pmla,tema:this.$route.query.tema,portal:this.$route.query.portal}),document.addEventListener("click",(function(o){var e=document.getElementById("AppAtrilTurnero");document.mozFullScreen||document.webkitFullScreen||(e.mozRequestFullScreen?e.mozRequestFullScreen():e.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT))}),!1)},updated:function(){},methods:{redirect:function(o){this.$router.push(o).catch((function(){}))},IrHome:function(){console.log("ir home"),console.log(this.$store.state),this.$router.push({path:"/",query:{wemp_pmla:this.$store.state.ConfigTurnero.wemp_pmla,tema:this.$store.state.ConfigTurnero.CodigoTurnero,portal:this.$store.state.ConfigPortal.CodigoPortal}}).catch((function(){}))}}},l=i,u=(r("034f"),r("2877")),s=Object(u["a"])(l,n,a,!1,null,null,null),c=s.exports,d=(r("d3b7"),r("3ca3"),r("ddb0"),r("8c4f")),m=function(){var o=this,e=o.$createElement,t=o._self._c||e;return t("div",{staticClass:"PortalTurneros"},[t("v-row",{attrs:{align:"center",justify:"center"}},[t("v-col",{attrs:{cols:"1"}},[t("v-card",{staticClass:"mx-auto rounded-br-xl ",attrs:{dark:"",color:"secondary",height:"120px",elevation:"0"}})],1),t("v-col",{attrs:{cols:"11"}},[t("v-card",{staticClass:"mx-auto rounded-bl-xl",attrs:{dark:"",color:"primary",height:"120px",elevation:"0"}},[t("v-row",{attrs:{align:"center",justify:"center"}},[t("v-col",{attrs:{cols:"1"}}),t("v-col",{staticClass:"pa-0",attrs:{cols:"1"}},[t("div",{staticClass:"pa-0",attrs:{id:"printMe"}},[t("v-img",{attrs:{src:r("9f59"),height:"70px"}})],1)]),t("v-col",{attrs:{cols:"9"}},[t("div",{staticClass:"text-center textoaunaPortal  pt-0"},[t("p",{domProps:{innerHTML:o._s(o.ConfigPortal.MensajeBienvenida)}})])]),t("v-col",{attrs:{cols:"1"}})],1)],1)],1)],1),t("div",{staticStyle:{height:"10px"}}),t("v-card",{staticClass:"div-2",attrs:{loight:"",color:"white",flat:""}},[t("v-row",{attrs:{justify:"center"}},[t("v-col",{attrs:{cols:"12"}},[t("div",{staticStyle:{height:"40px"}}),t("v-card",{staticClass:"d-flex align-content-space-around pa-10  justify-space-around flex-wrap",attrs:{height:"80%",flat:"",tile:""}},o._l(o.ConfigPortal.Turneros,(function(e){return t("div",{key:e.CodigoTurnero},[t("v-btn",{staticClass:"m-boton text-h6 headline pt-4",attrs:{height:"150",width:"400",elevation:"8",ligth:"",color:"gray",rounded:""},on:{click:function(r){return o.clickTurnero(e)}}},[t("p",{staticClass:"textoaunapequenoPortal",domProps:{innerHTML:o._s(e.NombreTurnero)}})]),t("div",{staticStyle:{height:"40px"}})],1)})),0),t("div",{staticStyle:{height:"40px"}})],1)],1)],1),t("v-dialog",{attrs:{transition:"dialog-bottom-transition",persistent:"","max-width":"900"},model:{value:o.ErrorIngresoDatos,callback:function(e){o.ErrorIngresoDatos=e},expression:"ErrorIngresoDatos"}},[t("v-card",{attrs:{color:"primary light"}},[t("v-toolbar",{staticClass:"textoauna pa-0",attrs:{color:"secondary",dark:"",elevation:"0"}},[o._v("Error")]),t("v-card-text",[t("div",[t("p",{staticClass:"textoauna  pa-12 text-popup",domProps:{innerHTML:o._s(o.MensajeError)}})])])],1)],1)],1)},C=[],g={data:function(){return{ErrorIngresoDatos:!1,MensajeError:"Sin error"}},computed:{ConfigPortal:function(){return this.$store.state.ConfigPortal}},mounted:function(){return console.log("Portal turneros mounted:"),console.log("route:"),console.log(this.$route),void 0!=this.$route.query.wemp_pmla&&void 0!=this.$route.query.tema||void 0!=this.$route.query.wemp_pmla&&void 0!=this.$route.query.portal?void 0==this.$route.query.portal?(console.log("No Es Portal"),this.redirect("/Home"),void this.$store.dispatch("inicializarTurnero",{wemp_pmla:this.$route.query.wemp_pmla,tema:this.$route.query.tema,portal:this.$route.query.portal})):(console.log("Es Portal"),console.log(this.$store.state.ConfigPortal),void this.$store.dispatch("inicializarTurnero",{wemp_pmla:this.$route.query.wemp_pmla,tema:this.$route.query.tema,portal:this.$route.query.portal})):(this.MensajeError="No se ingresaron los par&aacute;metros WEMP_PMLA (TEMA o PORTAL)",void(this.ErrorIngresoDatos=!0))},methods:{redirect:function(o){this.$router.push(o)},clickTurnero:function(o){this.$store.state.ConfigTurnero=o,console.log(this.$store.state.ConfigTurnero),this.redirect("/Home")}}},h=g,p=(r("36da"),Object(u["a"])(h,m,C,!1,null,"201b0415",null)),b=p.exports;t["default"].use(d["a"]);var f,T=[{path:"/",name:"PortalTurneros",component:b},{path:"/about",name:"About",component:function(){return r.e("about").then(r.bind(null,"f820"))}},{path:"/home",name:"Home",component:function(){return r.e("about").then(r.bind(null,"bb51"))}},{path:"/datos",name:"Datos",component:function(){return r.e("about").then(r.bind(null,"4a1e"))}},{path:"/tipoDocumento",name:"TipoDocumento",component:function(){return r.e("about").then(r.bind(null,"48ae"))}},{path:"/ingresoNombre",name:"IngresoNombre",component:function(){return r.e("about").then(r.bind(null,"0b8c"))}},{path:"/ingresoNombreModelo",name:"IngresoNombreModelo",component:function(){return r.e("about").then(r.bind(null,"9a08"))}},{path:"/generarTurno",name:"GenerarTurno",component:function(){return r.e("about").then(r.bind(null,"9fd8"))}},{path:"/ingresoEdad",name:"IngresoEdad",component:function(){return r.e("about").then(r.bind(null,"6613"))}},{path:"/ingresoIdentificacion/",name:"IngresoIdentificacion",component:function(){return r.e("about").then(r.bind(null,"bc70"))}},{path:"/seleccioncategoria",name:"SeleccionCategoria",component:function(){return r.e("about").then(r.bind(null,"c314"))}},{path:"/seleccionsubcategoria",name:"SeleccionSubcategoria",component:function(){return r.e("about").then(r.bind(null,"30c95"))}},{path:"/seleccionprioridad",name:"SeleccionPrioridad",component:function(){return r.e("about").then(r.bind(null,"0f18"))}},{path:"/carteleraUrgencias",name:"CarteleraUrgencias",component:function(){return r.e("about").then(r.bind(null,"e9a3"))}}],S=new d["a"]({routes:T}),N=S,y=r("ade3"),v=r("2f62"),x=r("bc3a"),P=r.n(x);t["default"].use(v["a"]);var E=new v["a"].Store({state:{Turno:(f={Turno:"SIN TURNO",TipoIdentificacion:"",NumeroIdentificacion:"",Nombre:"",Categoria:"",Subcategoria:"",Subcategorias:Array(),Edad:"",TipoEdad:"",Prioridad:"",ValidarExisteTurno:"",YaExisteTurnoHoy:""},Object(y["a"])(f,"Turno",""),Object(y["a"])(f,"FichoTurno",""),Object(y["a"])(f,"Error",""),Object(y["a"])(f,"MensajeError",""),f),ConfigPortal:{MensajeBienvenida:"BIENVENIDO AL PORTEAL DE TURNEROS",MensajeSeleccion:"Se lecciones el servicio",CodigoPortal:void 0,NombrePortal:"PORTAL SIN NOMBRE",Turneros:Array()},ConfigTurnero:{CodigoTurnero:"99",wemp_pmla:"99",NombreTurnero:"URGENCIAS",TieneLectorCedula:!0,TieneIngresoManual:!0,TieneTipoDocumento:!0,TieneDocumento:!0,TieneNombre:!0,TieneEdad:!0,IngresoPorPasos:!0,TieneCategorias:!0,TieneSubCategorias:!0,TienePrioridad:!0,ValidarExisteTurno:!1,MensajeBienvenida:"<b>Bienvenido</b><br> Cuidamos la vida a cada instante",MensajeLector:"SIN MENSAJE <br> DE LECTOR",MensajeIngresoManual:"SIN MENSAJE <br> DE INGRESO MANUAL",MensajeTiposDocumento:"SIN MENSAJE TIPO DOCUMENTO",MensajeDatosPersonales:"Ingrese su número de documento",MensajeNumeroDocumento:"Ingrese su número de documento**",MensajeNombre:"Ingrese su nombre***",MensajeCategorias:"SIN MENSAJE CATEGORIAS",MensajePrioridades:"SIN MENSAJE PRIORIDADES",MensajeGeneracionTurno:"Retire su turno",MensajeTurnoGenerado:"",MensajeSinTipoDocumento:"SIN MENSAJE SIN TIPO DOCUMENTO",MensajeSinNumeroDocumento:"SIN MENSAJE SIN NUMERO DOCUMENTO",MensajeSinNombre:"SIN MENSAJE SIN NOMBRE",MensajeSinEdad:"SIN MENSAJE SIN EDAD",MensajeSinTipoEdad:"",MensajeSinCategoria:"SIN MENSAJE SIN CATEGORIA",Error:"",MensajeError:"",TiposDocumento:[{Nombre:"Cédula",Codigo:"CC",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"Tarjeta de identidad",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"Registro cívil",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"Cédula de extrajeria",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"Numero unico <br> de identificacion",Codigo:"004",Color:"primary",Clase:"m-boton text-h6 headline"}],Categorias:[{Nombre:"CAT1",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline",Subcategorias:[{Nombre:"CAT1S1",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT1S2",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT1S3",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT1S4",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT1S5",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT1S6",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline"}]},{Nombre:"CAT2",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline",Subcategorias:[{Nombre:"CAT2S1",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT2S2",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT2S3",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT2S4",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT2S5",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT2S6",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline"}]},{Nombre:"CAT3",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline",Subcategorias:[{Nombre:"CAT3S1",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT3S2",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT3S3",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT3S4",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT3S5",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT3S6",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline"}]},{Nombre:"CAT4",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline",Subcategorias:[{Nombre:"CAT4S1",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT4S2",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT4S3",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT4S4",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT4S5",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT4S6",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline"}]},{Nombre:"CAT5",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline",Subcategorias:[{Nombre:"CAT5S5",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT5S5",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT5S5",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT5S5",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT5S5",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT5S5",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline"}]},{Nombre:"CAT6",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline",Subcategorias:[{Nombre:"CAT6S6",Codigo:"006",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT6S6",Codigo:"003",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT6S6",Codigo:"01",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT6S6",Codigo:"002",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT6S6",Codigo:"005",Color:"primary",Clase:"m-boton text-h6 headline"},{Nombre:"CAT6S6",Codigo:"007",Color:"primary",Clase:"m-boton text-h6 headline"}]}],Prioridades:[{Nombre:"P1",Codigo:"006",Color:"white",EsPrioridad:!1,Icono:"mdi-human-pregnant"},{Nombre:"P2",Codigo:"003",Color:"white",EsPrioridad:!0,Icono:"mdi-human-pregnant"},{Nombre:"P3",Codigo:"01",Color:"white",EsPrioridad:!0,Icono:"mdi-human-cane"},{Nombre:"P4",Codigo:"002",Color:"white",EsPrioridad:!0,Icono:"mdi-human-wheelchair"},{Nombre:"P5",Codigo:"005",Color:"white",EsPrioridad:!0,Icono:"mdi-human-male-child"}]},Prueba:"Prueba vuex"},getters:{},mutations:{setConfigPortal:function(o,e){o.ConfigPortal=e,console.log(o.ConfigPortal)},setConfigTurnero:function(o,e){o.ConfigTurnero=e},changePrueba:function(o,e){o.Prueba=e},seleccionarCategoria:function(o,e){for(var r=0;r<o.ConfigTurnero.Categorias.length;r+=1)console.log(o.ConfigTurnero.Categorias[r].Codigo+"-"+e.Codigo),o.ConfigTurnero.Categorias[r].Codigo==e.Codigo?(o.ConfigTurnero.Categorias[r].Color="white",o.ConfigTurnero.Categorias[r].Clase="m-botonSeleccionado text-h6 headline pt-4",o.Turno.Categoria=e.Codigo,o.Turno.Subcategorias=e.Subcategorias):(o.ConfigTurnero.Categorias[r].Clase="m-boton text-h6 headline pt-4",o.ConfigTurnero.Categorias[r].Color="primary")},seleccionarSubcategoria:function(o,e){for(var r=0;r<o.Turno.Subcategorias.length;r+=1)console.log(o.Turno.Subcategorias[r].Codigo+"-"+e.Codigo),o.Turno.Subcategorias[r].Codigo==e.Codigo?(o.Turno.Subcategorias[r].Color="white",o.Turno.Subcategorias[r].Clase="m-botonSeleccionado text-h6 headline pt-4",o.Turno.Subcategoria=e.Codigo):(o.Turno.Subcategorias[r].Clase="m-boton text-h6 headline pt-4",o.Turno.Subcategorias[r].Color="primary")},seleccionarTipoDocumento:function(o,e){var r="XX";r=void 0==e.Codigo?e:e.Codigo;for(var t=0;t<o.ConfigTurnero.TiposDocumento.length;t+=1)console.log(o.ConfigTurnero.TiposDocumento[t].Codigo+"-"+r),o.ConfigTurnero.TiposDocumento[t].Codigo==r?(o.ConfigTurnero.IngresoPorPasos,o.ConfigTurnero.TiposDocumento[t].Color="white",o.ConfigTurnero.TiposDocumento[t].Clase="m-botonSeleccionado text-h6 headline pt-4",o.Turno.TipoIdentificacion=r):(o.ConfigTurnero.IngresoPorPasos,o.ConfigTurnero.TiposDocumento[t].Clase="m-boton text-h6 headline pt-4",o.ConfigTurnero.TiposDocumento[t].Color="primary")},seleccionarPrioridad:function(o,e){for(var r=0;r<o.ConfigTurnero.Prioridades.length;r+=1)console.log(o.ConfigTurnero.Prioridades[r].Codigo+"-"+e.Codigo),o.ConfigTurnero.Prioridades[r].Codigo==e.Codigo?(console.log("codigo encontrado"),o.ConfigTurnero.Prioridades[r].Color="primary",o.Turno.Prioridad=e.Codigo,console.log("Prioridad:"+o.Turno.Prioridad),console.log("Prioridad turno:"+o.Turno.Prioridad),console.log(o.Turno)):o.ConfigTurnero.Prioridades[r].Color="white"},changeTurno:function(o,e){o.Turno=e},changeTurnoTurno:function(o,e){o.Turno.Turno=e},changeTurnoTipoIdentificacion:function(o,e){o.Turno.TipoIdentificacion=e},changeTurnoNumeroIdentificacion:function(o,e){o.Turno.NumeroIdentificacion=e},changeTurnoNombre:function(o,e){o.Turno.Nombre=e},changeTurnoCategoria:function(o,e){o.Turno.Categoria=e},changeTurnoEdad:function(o,e){o.Turno.Edad=e},changeTurnoTipoEdad:function(o,e){o.Turno.TipoEdad=e},changeTurnoPrioridad:function(o,e){o.Turno.Prioridad=e},inicializarTurno:function(o,e){o.Turno.Prioridad="",o.Turno.Edad="",o.Turno.TipoEdad="A",o.Turno.Categoria="",o.Turno.Subcategoria="",o.Turno.Subcategorias=new Array,o.Turno.TipoIdentificacion="",o.Turno.NumeroIdentificacion="",o.Turno.Turno="SIN TURNO",o.Turno.Nombre="";for(var r=0;r<o.ConfigTurnero.Prioridades.length;r+=1)o.ConfigTurnero.Prioridades[r].Color="white";for(r=0;r<o.ConfigTurnero.TiposDocumento.length;r+=1)o.ConfigTurnero.TiposDocumento[r].Color="primary",o.ConfigTurnero.TiposDocumento[r].Clase="m-boton text-h6 headline pt-4";for(r=0;r<o.ConfigTurnero.Categorias.length;r+=1)o.ConfigTurnero.Categorias[r].Color="primary",o.ConfigTurnero.Categorias[r].Clase="m-boton text-h6 headline pt-4"}},actions:{changeTurno:function(o,e){setTimeout((function(){o.commit("changeTurno",e)}),2e3)},changePrueba:function(o,e){setTimeout((function(){o.commit("changePrueba",e)}),2e3)},inicializarTurnero:function(o,e){return console.log("Inicio inicializarTurnero"),void 0!=e.wemp_pmla&&void 0!=e.portal?(console.log("Carga Portal"),void P.a.get("../obtenerconfiguracionPortal.php?wemp_pmla="+e.wemp_pmla+"&codigoPortal="+e.portal).then((function(e){o.commit("setConfigPortal",e.data)}))):void 0!=e.wemp_pmla&&void 0!=e.tema?(console.log("Carga Turnero"),void P.a.get("../obtenerconfiguracionturnero.php?wemp_pmla="+e.wemp_pmla+"&codigoTurnero="+e.tema).then((function(e){o.commit("setConfigTurnero",e.data)}))):(console.log("inicializarTurnero Sin parametros"),void console.log(e))}}}),A=r("ce5b"),w=r.n(A);r("bf40");t["default"].use(w.a);var I=new w.a({theme:{options:{customProperties:!0},themes:{light:{primary:"#00B0CA",secondary:"#bed600",accent:"#FF7F00",error:"#FF0000",info:"#2196F3",success:"#00CB00",warning:"#FFC107"}}}}),M=(r("e792"),r("d5e8"),r("5363"),r("7898")),j=r.n(M);window.document.title;t["default"].use(j.a),t["default"].config.productionTip=!1,new t["default"]({router:N,store:E,vuetify:I,render:function(o){return o(c)}}).$mount("#app")},"85ec":function(o,e,r){},"9a92":function(o,e,r){o.exports=r.p+"img/logoaunatrans.1238e842.png"},"9f59":function(o,e,r){o.exports=r.p+"img/corazontrans.3186bead.png"},dd2b:function(o,e,r){}});
//# sourceMappingURL=app.eae20bcb.js.map