-- docker/mysql-init.sql
-- Force the "simpsons" user to use mysql_native_password with the given password.
ALTER USER 'simpsons'@'%' IDENTIFIED WITH mysql_native_password BY 'simpsons';
FLUSH PRIVILEGES;