# Create Testuser
CREATE USER 'haijin'@'localhost' IDENTIFIED BY 'haijin';
GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP ON *.* TO 'haijin'@'localhost';