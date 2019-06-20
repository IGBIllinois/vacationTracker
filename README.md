# VacationTracker 
Written by Nevo Band

## URL 
https://www-app.igb.illinois.edu/vacation/

## Using Vacation Tracker 
Vacation tracker was written to duplicate the HR leave policies of the University of Illinois

## Installation 

1. Download the data from Github into the directory on the server
```
cd /var/www/vacation/html
sudo git clone https://github.com/IGB-UIUC/vacationTracker.git .
```

2.  Create an alias in apache config file that points to the html folder, and restart Apache.  
```
Alias /vacation /var/www/vacation/html
```

3.  In mysql, create the database, and then run sql/vacation.sql on the mysql server to create the database tables.
From mysql:
```
CREATE DATABASE <databaseName>;
```
From command prompt:
```
mysql -u root -p <databaseName> < sql/vacation.sql
```
4.  Create a user/password on the mysql server which has select/insert/delete/update permissions on the vacationTracker database.
```
CREATE USER '<username>'@'localhost' IDENTIFIED BY '<password>';
GRANT SELECT,INSERT,DELETE,UPDATE ON <databaseName>.* to '<username>'@'localhost';
```
5.  Edit /conf/config.php to reflect your settings.
6.  Run composer to install php dependencies
```
composer install
```
7. Copy necessary javascript files to html folder
```
cp vendor/components html/vendor
cp vendor/mottie html/vendor
```

## Calendar setup

1. Create an admin user (using appropriate values for NETID, FIRST_NAME, LAST_NAME, and EMAIL)
```
INSERT INTO users (
netid, 
first_name, 
last_name, 
group_id, 
supervisor_id, 
auto_approve, 
percent, 
calendar_format, 
block_days, 
start_date, 
auth_key, 
email, 
enabled, 
user_type_id, 
user_perm_id) 
VALUES
('NETID', 'FIRST_NAME', 'LAST_NAME', '', 0, 0, 100, 0, '', NOW(), 0, 'EMAIL', 1, 1, 1);
```
2. Create default Year types, if using UIUC standards (Using appropriate years for 'YYYY')
```
INSERT INTO year_type (`year_type_id`, `name`, description, start_date, end_date, num_periods) 
	VALUES (1, 'Appointment Year', 'Appointment Year', 'YYYY-08-16', 'YYYY-08-15', 12);
INSERT INTO year_type (`year_type_id`, `name`, description, start_date, end_date, num_periods) 
	VALUES (25, 'Fiscal Year', 'Fiscal Year 12 month period', 'YYYY-07-01', 'YYYY-06-30', 1);
```

## Additional Packages included
* JIT: https://github.com/philogb/jit The JavaScript InfoVis Toolkit (used in Tree drawing)
...jit.js
* jscolor: https://github.com/EastDesire/jscolor
...jscolor.js
* excanvas.js: https://github.com/arv/explorercanvas
...excanvas.js
* JQuery Tools: http://jquerytools.github.io/download/
...jquery.tools.min.js

## For more information: 

UIUC Benefit leave chart:
https://humanresources.illinois.edu/assets/docs/AHR/Benefit-Chart-Update.pdf

