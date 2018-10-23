#!/bin/sh

echo "Running setup.py"
python3 /home/pi/repos/felreq/setup.py
echo "setup.py executed successfully"

echo "Running db_init.sql"
mysql -u $1  -p$2 < /home/pi/repos/felreq/db_init.sql
mysql -u $1 -p$2 felstedreq < /home/pi/repos/felreq/db_insert.sql
echo "Initialised and populated database"

echo "Copying PHP directory to /var/www/"
sudo cp -r /home/pi/repos/felreq/main /var/www/
echo "Website Deployed! Visit httpsL//felreq.hopto.org"
