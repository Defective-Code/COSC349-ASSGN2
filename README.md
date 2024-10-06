# COSC349-ASSGN2

## Connect to DB from EC2 instance

mysql -h asgn2-db.ccigrfaxhfvy.us-east-1.rds.amazonaws.com -P 3306 -u kerli421 -p

Requires mysql client to be installed on that EC2 instance.

## Send files to EC2 instances

scp -i cosc-349.pem ~/temp/SamplePage.php ec2-user@ec2-52-90-82-54.compute-1.amazonaws.com:/var/www/html

## SSH into WebServer 
ssh -i cosc-349.pem ec2-user@ec2-52-90-82-54.compute-1.amazonaws.com

## Create table for the two tables
create table Booking (bookingId INT AUTO_INCREMENT, userName varchar(50) NOT NULL, deviceId INT, startDate DATE NOT NULL, endDate DATE NOT NULL, PRIMARY KEY (bookingId), FOREIGN KEY (deviceId) REFERENCES Device(deviceId));

create table Device(deviceId INT AUTO_INCREMENT, deviceName varchar(40) NOT NULL, type varchar(30) NOT NULL, bookingStatus TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY (deviceId));

## log into db from webserver ec2 instance
mysql -h asgn2-db.ccigrfaxhfvy.us-east-1.rds.amazonaws.com -P 3306 -u kerli421 -p

