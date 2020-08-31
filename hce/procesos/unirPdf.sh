#!/bin/bash
cd /var/www/matrix/hce/reportes/cenimp
pdftk $1 cat output $2
echo "termino"
