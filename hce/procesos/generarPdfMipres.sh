#! /bin/bash
cd /var/www/matrix/hce/procesos/mipres
respuesta=xvfb-run html2pdf --load-error-handling ignore  --margin-top 11mm --margin-bottom 11mm --margin-left 11mm --margin-right 11mm --page-size Letter $1.html $1.pdf 2>&1
echo '||||||';
pdfinfo $1.pdf | grep Pages | awk '{print $2}'
	
