# COSC349-ASSGN2 - Device Rental Service

This is the README to get you up to speed on the architecture of the Device Rental interface that is deployed using AWS, in particular 2 EC2 instances and 1 MySQL RDS to store data.

This repository contains information on the architecture and how to interact with it. The files and such contained in here are not what is currently being used in the actual deployment.

## Architecture

We use a 2 EC2 instances, 1 as the Rental Service front end and the other as the dashboard for managing the data within the database, with a MySQL database storing all the data. 
We are using AWS's Elastic IP service for now to keep a persistent IP address attached to each EC2 instance, as they are often stopped and started and the public DNS changes often.

### The IP Addresses
Rental Frontend : 98.82.102.76
Database Management : 44.199.172.29

Putting these into your browser (using HTTP not HTTPS) will take you to the home page for the Rental frontend, and the login screen for the DB management page.

To SSH into the instances, I have not found another solution yet other than to:
    -> Go to the EC2 instance on the AWS Dashboard
    -> Select Connect
    -> Copypaste the command given under "SSH Client", changing "cosc-349.pem" to whatever the filepath is to that file. This file will be supplied to you for access to these instances.
The copied command will look something like the following, just with the IPv4 address in the middle being different.
-> ssh -i cosc-349.pem ec2-user@ec2-52-90-82-54.compute-1.amazonaws.com


To access the database directly, first SSH into the rental frontent VM and copy the following

### Connect to DB from EC2 instance

mysql -h asgn2-db.ccigrfaxhfvy.us-east-1.rds.amazonaws.com -P 3306 -u kerli421 -p

Requires mysql client to be installed on that EC2 instance.

The password is in the report, as i didn't want it in the public readme.

## The database structure
create table Booking (bookingId INT AUTO_INCREMENT, deviceId INT, userId INT, startDate DATE NOT NULL, endDate DATE NOT NULL, PRIMARY KEY (bookingId), FOREIGN KEY (deviceId) REFERENCES Device(deviceId), FOREIGN KEY (userId) REFERENCES Account(userId));

create table Device(deviceId INT AUTO_INCREMENT, deviceName varchar(100) NOT NULL UNIQUE, type varchar(30) NOT NULL, available TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY (deviceId));

CREATE TABLE Account (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    passwordHash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);

The main thing to note here is that the passwords for accounts are hashed server side and stored as the hash in the db.

## Developing the web pages
During development so far, we found using Visual Studio Code, with the "Remote - SSH" extension was the easiest way to develop directly, as it allowed us to use VS Code to directly manipulate files on the EC2 instances, giving us all the benefits of VSCode for development.

## Send files to EC2 instances

scp -i cosc-349.pem ~/temp/SamplePage.php ec2-user@ec2-52-90-82-54.compute-1.amazonaws.com:/var/www/html
