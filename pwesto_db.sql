create database pwesto_db;
USE pwesto_db;
UPDATE users SET role = 'admin', status = 'approved' WHERE email = 'carl@gmail.com';