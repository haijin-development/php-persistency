# Create Testuser
CREATE USER 'haijin'@'localhost' IDENTIFIED BY '123456';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX ON *.* TO 'haijin'@'localhost';
CREATE SCHEMA `haijin-persistency`;