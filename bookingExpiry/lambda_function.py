import pymysql
import os
import boto3
from datetime import datetime, timedelta

# SNS client
sns_client = boto3.client('sns')

# Database connection details
db_host = os.getenv('asgn2-db.ccigrfaxhfvy.us-east-1.rds.amazonaws.com')
db_user = os.getenv('kerli421')
db_password = os.getenv('adenantH4$')
db_name = os.getenv('asgn2')

# SNS topic ARN
sns_topic_arn = os.getenv('arn:aws:sns:us-east-1:324797061984:BookingExpiry')

def lambda_handler(event, context):
    # Connect to the MySQL database
    connection = pymysql.connect(
        host=db_host,
        user=db_user,
        password=db_password,
        database=db_name
    )
    
    # Calculate the date for bookings expiring within 1 day
    tomorrow = (datetime.now() + timedelta(days=1)).date()
    
    try:
        with connection.cursor() as cursor:
            # Find bookings expiring in 1 day
            sql = """
            SELECT A.userId, A.username, A.email, B.endDate
            FROM Account A
            JOIN Bookings B ON A.userId = B.userId
            WHERE B.endDate = %s
            """
            cursor.execute(sql, (tomorrow,))
            results = cursor.fetchall()
            
            # Send SNS notifications for each expiring booking
            for row in results:
                username = row[1]
                email = row[2]
                expiration_date = row[3]
                
                # Prepare the notification message
                message = f"Hello {username},\n\nYour booking is expiring on {expiration_date}. Please take necessary actions if needed."
                subject = "Booking Expiration Notice"
                
                # Publish the message to SNS
                response = sns_client.publish(
                    TopicArn=sns_topic_arn,
                    Message=message,
                    Subject=subject,
                )
                
                print(f"Notification sent to {email}")
                
    except Exception as e:
        print(f"Error: {str(e)}")
    
    finally:
        connection.close()

