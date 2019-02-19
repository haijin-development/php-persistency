sudo apt install -y postgresql postgresql-contrib

sudo su -c "psql -c \"CREATE USER haijin WITH LOGIN PASSWORD '123456';\"" -s /bin/sh postgres
sudo su -c "psql -c \"CREATE DATABASE \\\"haijin-persistency\\\"\"" -s /bin/sh postgres
sudo su -c "psql -c \"GRANT ALL PRIVILEGES ON DATABASE \\\"haijin-persistency\\\" TO haijin;\"" -s /bin/sh postgres