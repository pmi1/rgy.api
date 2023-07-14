#!/usr/bin/env bash

# DEV
HOSTNAME_DEV="localhost"
USER_DEV="rguys"
PASS_DEV="root"
DB_DEV="rguys"

# PROD
HOSTNAME_PROD="localhost"
USER_PROD="rguys_db_user"
PASS_PROD="1J7b9S4t"
DB_PROD="rguys"

DATE=$(date +"%d-%b-%Y")


BACKUP_PATH="/var/tmp"

sshpass -p "Ms_>swh,3-(B" ssh -o StrictHostKeyChecking=no root@185.76.146.153 << ENDHERE
mysqldump --user=$USER_PROD --password=$PASS_PROD --host=$HOSTNAME_PROD $DB_PROD > $BACKUP_PATH/$DB_PROD-$DATE.sql
ENDHERE

cd /var/tmp
scp -i ~/.ssh/id_rsa root@185.76.146.153:$BACKUP_PATH/$DB_PROD-$DATE.sql $BACKUP_PATH/$DB_PROD-$DATE.sql 2>&1

mysql --user=$USER_DEV --password=$PASS_DEV $DB_DEV < $BACKUP_PATH/$DB_PROD-$DATE.sql

#rm $BACKUP_PATH/$DB_DEV-$DATE.sql

sshpass -p "Ms_>swh,3-(B" ssh -o StrictHostKeyChecking=no root@185.76.146.153 << ENDHERE
rm $BACKUP_PATH/$DB_PROD-$DATE.sql
ENDHERE

cd /var/www/api.r-guys.ru  2>&1
php artisan migrate  2>&1
php artisan user:convert_phone  2>&1

