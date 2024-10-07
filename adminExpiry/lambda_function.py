import pymysql
import os
import boto3
from datetime import datetime, timedelta

# Initialize SNS client
sns_client = boto3.client('sns')

# Database connection details from environment variables
db_host = os.getenv('asgn2-db.ccigrfaxhfvy.us-east-1.rds.amazonaws.com')
db_user = os.getenv('kerli421')
db_password = os.getenv('adenantH4$')
db_name = os.getenv('asgn2')

# SNS topic ARN from environment variable
sns_topic_arn = os.getenv('arn:aws:sns:us-east-1:324797061984:BookingExpiry')

def lambda_handler(event, context):
    # Connect to the MySQL database
    connection = pymysql.connect(
        host=db_host,
        user=db_user,
        password=db_password,
        database=db_name
    )
    
    # Calculate tomorrow's date
    tomorrow = (datetime.now() + timedelta(days=1)).date()
    
    try:
        with connection.cursor() as cursor:
            # Query to find borrow entries due tomorrow
            sql = """
            SELECT A.userId, A.username, A.email, B.endDate
            FROM Account A
            JOIN Bookings B ON A.userId = B.userId
            WHERE B.endDate = %s
            """
            cursor.execute(sql, (tomorrow,))
            results = cursor.fetchall()
            
            # Check if any entries are found
            if results:
                # Prepare the notification message
                message = "The following borrow entries are due tomorrow:\n\n"
                for row in results:
                    username = row[1]
                    email = row[2]
                    due_date = row[3].strftime('%Y-%m-%d')
                    message += f"User: {username}, Email: {email}, Due Date: {due_date}\n"

                # Publish the message to SNS
                response = sns_client.publish(
                    TopicArn=sns_topic_arn,
                    Message=message,
                    Subject="Borrow Entries Due Tomorrow",
                )
                
                print(f"Notification sent: {message}")
            else:
                print("No borrow entries due tomorrow.")
                
    except Exception as e:
        print(f"Error: {str(e)}")
    
    finally:
        connection.close()
