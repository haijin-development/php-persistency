sudo apt install -y postgresql postgresql-contrib

sudo su -c "psql -f src/php-persistency/provision-scritps/postgres.sql" -s /bin/sh postgres