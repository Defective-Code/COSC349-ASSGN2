# COSC349-ASSGN2

## Connect to DB from EC2 instance

mysql -h asgn2-db.ccigrfaxhfvy.us-east-1.rds.amazonaws.com -P 3306 -u kerli421 -p

Requires mysql client to be installed on that EC2 instance.

## Send files to EC2 instances

scp -i cosc-349.pem ~/temp/SamplePage.php ec2-user@ec2-52-90-82-54.compute-1.amazonaws.com:/var/www/html

## SSH into WebServer 
ssh -i cosc-349.pem ec2-user@ec2-52-90-82-54.compute-1.amazonaws.com

## Create table for the two tables
create table Booking (bookingId INT AUTO_INCREMENT, deviceId INT, userId INT, startDate DATE NOT NULL, endDate DATE NOT NULL, PRIMARY KEY (bookingId), FOREIGN KEY (deviceId) REFERENCES Device(deviceId), FOREIGN KEY (userId) REFERENCES Account(userId));

create table Device(deviceId INT AUTO_INCREMENT, deviceName varchar(100) NOT NULL UNIQUE, type varchar(30) NOT NULL, available TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY (deviceId));

CREATE TABLE Account (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    passwordHash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);

ALTER TABLE Account AUTO_INCREMENT=100; 

INSERT INTO Account (userId, username, passwordHash, email) values (0, "admin", "$2y$10$O20945IU/1EgYnUXz6/v1.2vEY4QZeEIe4fCNqYF/qSWUooszdzgO", "admin@gmail.com");


## log into db from webserver ec2 instance
mysql -h asgn2-db.ccigrfaxhfvy.us-east-1.rds.amazonaws.com -P 3306 -u kerli421 -p

## Amazon SNS 
Topic = BookingExpiry

## Amazon Lambda
FunctionName = BookingExpiryLambda

