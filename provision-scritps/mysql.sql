CREATE DATABASE IF NOT EXISTS `haijin-persistency`;
CREATE USER 'haijin'@'%' IDENTIFIED BY '123456';
GRANT ALL PRIVILEGES ON `haijin-persistency`.* TO 'haijin'@'%';