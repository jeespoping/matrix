#!/bin/bash
cant=16
cxn=$(/bin/netstat -pan | /bin/grep -c '1541')
	if [ "$cxn" -ge "$cant" ]; then
#   	 echo "mas de $cant conexiones odbc"
	/usr/sbin/service apache2 reload
	fi
