#! /bin/bash
cd /var/www/matrix/ips/procesos/soportes
respuesta=xvfb-run html2pdf --load-error-handling ignore  --margin-top 0mm --margin-bottom 0mm --margin-left 0mm --margin-right 1mm --page-width 145mm --page-height 94mm $1.html $1.pdf 2>&1
echo '||||||';
pdfinfo $1.pdf | grep Pages | awk '{print $2}'
	
