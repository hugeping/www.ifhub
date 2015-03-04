rm -f /home/peter/backups/*.tar.gz
rm -f /home/peter/backups/*.sql
mysqldump -u ifhub -pXXXXXX ifhub > /home/peter/backups/ifhub-`date +%d.%m.%y`.sql
tar czf /home/peter/backups/ifhub-`date +%d.%m.%y`.tar.gz --exclude='tmp' --exclude='cache' /home/peter/www/ifhub.ru
tar czf /home/peter/backups/conf-`date +%d.%m.%y`.tar.gz /etc/apache2/
