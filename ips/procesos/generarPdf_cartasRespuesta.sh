#! /bin/bash
cd /var/www/matrix/ips/procesos/soportes
respuesta=xvfb-run html2pdf --load-error-handling ignore  --margin-top 45mm --margin-bottom 25mm --margin-left 25mm --margin-right 25mm --page-width 215mm --page-height 279mm $1.html $1.pdf 2>&1
echo '||||||';
pdfinfo $1.pdf | grep Pages | awk '{print $2}'
	
