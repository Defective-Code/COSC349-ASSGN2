import json
import boto3
import pymysql  # Ensure you have this library in your Lambda environment

# Initialize SNS client
sns_client = boto3.client('sns')

# Replace with your SNS topic ARN
SNS_TOPIC_ARN = "arn:aws:sns:us-east-1:324797061984:BookingExpiry"

# Database connection settings
DB_HOST = "asgn2-db.ccigrfaxhfvy.us-east-1.rds.amazonaws.com'"
DB_USER = "kerli421"
DB_PASSWORD = "adenantH4$"
DB_NAME = "asgn2"

def lambda_handler(event, context):
    # Connect to the MySQL database
    connection = pymysql.connect(
        host=DB_HOST,
        user=DB_USER,
        password=DB_PASSWORD,
        database=DB_NAME,
        cursorclass=pymysql.cursors.DictCursor
    )

    try:
        with connection.cursor() as cursor:
            # Fetch all user emails from the Account table
            cursor.execute("SELECT email FROM Account")
            results = cursor.fetchall()
            
            for row in results:
                email = row['email']
                try:
                    # Subscribe each email to the SNS topic
                    sns_client.subscribe(
                        TopicArn=SNS_TOPIC_ARN,
                        Protocol='email',
                        Endpoint=email
                    )
                except Exception as e:
                    print(f"Error subscribing {email}: {str(e)}")

        return {
            'statusCode': 200,
            'body': json.dumps('Subscription requests sent to all emails.')
        }
    
    except Exception as e:
        return {
            'statusCode': 500,
            'body': json.dumps(f'Error fetching emails: {str(e)}')
        }
    
    finally:
        connection.close()
