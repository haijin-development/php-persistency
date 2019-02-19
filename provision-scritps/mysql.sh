sudo apt install -y mysql-server
sudo mysql -e "CREATE DATABASE IF NOT EXISTS \`haijin-persistency\`;"
sudo mysql -e "CREATE USER 'haijin'@'%' IDENTIFIED BY '123456';"
sudo mysql -e "GRANT ALL PRIVILEGES ON \`haijin-persistency\`.* TO 'haijin'@'%';"