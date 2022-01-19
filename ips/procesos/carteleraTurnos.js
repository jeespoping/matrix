var audio = new Audio('../../images/medical/root/alertaMensaje.mp3');
audio.autoplay=true;
new Vue({
	
    el: '#app',
    vuetify: new Vuetify(),
    data () {
        return {
          turnos:[],
		  turnospantalla:[],
		  alerta: [],
		  turnosAlerta: [],
		  lineactual: 0,
		  timer: '',
		  timerAlerta: '',
		  llamadoTurno: false,
		  parametros: '',
		  wemp_pmla: '', 
		  tema: '',  
		  tipoTur: '',
		  solucionCitas: '',
		  wsTurnero: ''
        }
    },
    methods: 
    {
        sonidoAlerta()
		{
			audio.play();
		},
		
		CalcularAltura(objTurno)
        {
        // if Original para validar si hay prioridad se modifdica para validar si es endoscopia o urgencias 
		//if (objTurno.Prioridad == "on")
        if (this.tipoTur == 'ENDOSCOPIA')		
			return 120;
        else if (this.tipoTur == 'URGENCIAS')
			return 180;
		else
          return 70;
        },
		async cargarPagina()
		{
			this.turnospantalla = Array();
			var lineainicial = this.lineactual;
			var lineactualpantalla = this.lineactual;
			// this.turnospantalla = this.turnos;
			// return;
						
			// console.log('Carga pagina');
			// console.log('TipoTurnero: ' + this.tipoTur)
			while ((this.lineactual < this.turnos.length) && (lineactualpantalla < lineainicial + 12))
			{
				//console.log("while arreglo");
				
				this.turnospantalla[this.turnospantalla.length] = this.turnos[this.lineactual];
				// console.log("turno actual" + this.turnos[this.lineactual]);
				// console.log("linea actual: " +lineactualpantalla);
				// console.log("linea inicial: " + lineainicial);
				//console.log("este es el turno en pantalla: ");
				//console.log(this.turnospantalla[this.turnospantalla.length-1]);
				//console.log("este es el turno original: ");
				//console.log(this.turnos[this.lineactual]);
				// SE DEJA COMENTADA POR DECISION DE AUNA DE NO MOSTRAR LA PRIORIDAD
				// if (this.turnos[this.lineactual].Prioridad == 'on') 
				// {
				// 	//console.log("entro a prioridad");
				// 	lineactualpantalla++;
				// }	
				if ((tipoTur == 'ENDOSCOPIA') | (tipoTur == 'URGENCIAS'))
				{
					lineactualpantalla++;
					if (tipoTur == 'URGENCIAS')
					{
						lineactualpantalla++;
					}
				}
				this.lineactual++;
				lineactualpantalla++;				
			}
			if (this.lineactual == this.turnos.length) 
			{
				this.lineactual = 0;
				//var res = await fetch('http://10.17.2.35/matrix/admisiones/procesos/wbsturnero.php?wemp_pmla=01&tema=01&funcion=listaTurnos',
				this.parametros="wemp_pmla=0"+wemp_pmla+"&tema=0"+tema+"&funcion=listaTurnos&solucionCitas="+solucionCitas+"&tipoTur="+tipoTur;
				this.turnos = await (await fetch(this.wsTurnero + this.parametros)).json();
				//console.log('leyo turnos');
				//console.log('datos recibidos')
				//console.log(this.turnos);
				this.parametros="wemp_pmla=0"+wemp_pmla+"&tema=0"+tema+"&funcion=listaAlertas&solucionCitas="+solucionCitas+"&tipoTur="+tipoTur;
				//res = await fetch('http://10.17.2.35/matrix/admisiones/procesos/wbsturnero.php?wemp_pmla=01&tema=01&funcion=listaAlertas',
				this.turnosAlerta = await (await fetch(this.wsTurnero + this.parametros)).json();
				//console.log('Alertas recibidas')
				//console.log(this.turnosAlerta);
			}
			//console.log("estos son los turnos de la pantalla");
			//console.log(this.turnospantalla);
		},
		 async llamarTurno() 
		 {
			this.turnosAlerta = Array();
			// console.log("control llamar turno")
			// console.log(this.parametros);
			// var res = await fetch('http://10.17.2.35/matrix/admisiones/procesos/wbsturnero.php?wemp_pmla=01&tema=01&funcion=listaAlertas',
			this.parametros="wemp_pmla=0"+wemp_pmla+"&tema=0"+tema+"&funcion=listaAlertas&solucionCitas="+solucionCitas+"&tipoTur="+tipoTur;
			this.turnosAlerta = await (await fetch(this.wsTurnero + this.parametros)).json();
				if (this.turnosAlerta.length > 0) 
				{
				// console.log(this.turnosAlerta);
				this.llamadoTurno = true;
				//console.log('entra alerta');
				audio.play();
				clearInterval(this.timerAlerta);
				this.timerAlerta = setInterval(this.cerrarTurno, 10000);
				//console.log('sale de la alerta');
				}
		 },
		cerrarTurno() 
		{
			this.llamadoTurno = false;
			clearInterval(this.timerAlerta);
			this.timerAlerta = setInterval(this.llamarTurno, 4000);
			//console.log('cierra alerta');
		}
	},
	
	 mounted: async function () 
	{
		this.wemp_pmla=wemp_pmla;
		this.tema=tema;
		this.tipoTur=tipoTur;
		this.solucionCitas=solucionCitas;
		this.wsTurnero=wsTurnero;
		this.parametros="wemp_pmla=0"+wemp_pmla+"&tema=0"+tema+"&funcion=listaTurnos&solucionCitas="+solucionCitas+"&tipoTur="+tipoTur;
		this.turnos = await (await fetch(this.wsTurnero + this.parametros)).json();
		//console.log('datos recibidos');
		//console.log(this.turnos);
		this.timer = setInterval(this.cargarPagina, 10000);
		this.timerAlerta = setInterval(this.llamarTurno, 4000);
		//console.log('timer inicializado');		  
	}	
	})
	
    
	
	
        

