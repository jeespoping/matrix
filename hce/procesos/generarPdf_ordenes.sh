#! /bin/bash
cd /var/www/matrix/hce/procesos/impresion_ordenes
#! cd C:\wamp\www\matrix\hce\procesos\impresion_ordenes
respuesta=xvfb-run html2pdf --load-error-handling ignore  --margin-top 0mm --margin-bottom 0mm --margin-left 0mm --margin-right 0mm --page-size Letter $1.html $1.pdf 2>&1
echo '||||||';
pdfinfo $1.pdf | grep Pages | awk '{print $2}'
	
