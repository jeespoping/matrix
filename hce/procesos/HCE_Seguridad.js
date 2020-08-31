document.onkeydown = mykeyhandler; 
function mykeyhandler(event) 
{
	 //keyCode 116 = F5
	 //keyCode 122 = F11
	 //keyCode 8 = Backspace
	 //keyCode 37 = LEFT ROW
	 //keyCode 78 = N
	 //keyCode 39 = RIGHT ROW
	 //keyCode 67 = C
	 //keyCode 86 = V
	 //keyCode 85 = U
	 //keyCode 45 = Insert
		
	
	 event = event || window.event;
	 if (navigator.appName == "Netscape")
	 {
		 var tgt = event.target || event.srcElement;
		 if ((event.ctrlKey && event.which==37) || (event.ctrlKey && event.which==39) ||
			 (event.ctrlKey && event.which==78) || (event.ctrlKey && event.which==67) ||
			 (event.ctrlKey && event.which==86) || (event.ctrlKey && event.which==85) ||
			 (event.ctrlKey && event.which==45) || (event.ctrlKey && event.which==45))
		 {
			 event.cancelBubble = true;
			 event.returnValue = false;
			 alert("Funcion no permitida");
			 return false;
		 }

		if(event.which==18 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
		{
			return false;
		}
		
		if (event.which == 8 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
		{
			return false;
		}
		
		if ((event.which == 116) || (event.which == 122)) 
		{
			return false;
		}
	 }
	 else
	 {
		 var tgt = event.target || event.srcElement;
		 if((event.altKey && event.keyCode==37) || (event.altKey && event.keyCode==39) ||
		 (event.ctrlKey && event.keyCode==78)|| (event.ctrlKey && event.keyCode==67)||
		 (event.ctrlKey && event.keyCode==86)|| (event.ctrlKey && event.keyCode==85)||
		 (event.ctrlKey && event.keyCode==45)|| (event.shiftKey && event.keyCode==45))
		 {
			 event.cancelBubble = true;
			 event.returnValue = false;
			 alert("Funcion no permitida");
			 return false;
		 }

		if(event.keyCode==18 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
		{
			return false;
		}
		
		if (event.keyCode == 8 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
		{
			return false;
		}
		
		if ((event.keyCode == 116) || (event.keyCode == 122)) 
		{
			if (navigator.appName == "Microsoft Internet Explorer")
			{
				window.event.keyCode=0;
			}
			return false;
		}
	}
}

function mouseDown(e) 
{
	var ctrlPressed=0;
	var altPressed=0;
	var shiftPressed=0;
	if (parseInt(navigator.appVersion)>3) 
	{
		if (navigator.appName=="Netscape") 
		{
			var mString =(e.modifiers+32).toString(2).substring(3,6);
			shiftPressed=(mString.charAt(0)=="1");
			ctrlPressed =(mString.charAt(1)=="1");
			altPressed =(mString.charAt(2)=="1");
			self.status="modifiers="+e.modifiers+" ("+mString+")"
		}
		else
		{
			shiftPressed=event.shiftKey;
			altPressed =event.altKey;
			ctrlPressed =event.ctrlKey;
		}
		if (shiftPressed || altPressed || ctrlPressed)
		alert ("Función no permitida");
	}
	return true;
}

if (parseInt(navigator.appVersion)>3) 
{
	document.onmousedown = mouseDown;
	if (navigator.appName=="Netscape")
	document.captureEvents(Event.MOUSEDOWN);
}

var message="";

function clickIE() 
{
	if (document.all)
	{
		(message);
		return false;
	}
}

function clickNS(e) 
{
	if(document.layers||(document.getElementById&&!document.all)) 
	{
		if (e.which==2||e.which==3) 
		{
			(message);return false;
		}
	}
}

if (document.layers)
{
	document.captureEvents(Event.MOUSEDOWN);
	document.onmousedown=clickNS;
}
else
{
	document.onmouseup=clickNS;document.oncontextmenu=clickIE;
}

document.oncontextmenu=new Function("return false");
