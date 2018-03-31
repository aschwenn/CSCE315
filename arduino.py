import serial 
import datetime
import time

try:
    import MySQLdb
except: 
    import pip
    pip.main(['install', 'mysqlclient'])   
    import MySQLdb

# Name: takeAverage
# PreCondition: Arduino must be connected to laptop with ultrasonic 
#               connected to Port 13 (Trigger) and Port 11 (Echo).
# PostCondition: Returns the calculated average reading of the ultrasonic
#                sensor.
def takeAverage(arduinoSerialData):
    #dataPoints is the number of data points taken for the average
    dataPoints = 100
    count = 0
    sum = 0
    while(count < dataPoints):
        if(arduinoSerialData.inWaiting() > 0):
            myData = str(arduinoSerialData.readline())[2:6]
            try:
                myData = float(myData) / 2.54 * 2
                sum += myData
                count += 1
            except:
                print("Discarding Value", myData)
    return sum/count  


# Name: connectToDatabase
# PreCondition: Must receive correct hostname, database name, username, and password for
#               the database.
# PostCondition: Returns a database connection if successful and false otherwise.  
def connectToDatabase(hostname, database, username, password):
    try:
        return MySQLdb.connect(host = hostname, user = username, passwd = password, db = database)
    except:
        return False


# Name: insertIntoDatabse
# PreCondition: A database connection must be established. For the first time this
#               function is called, a time at least 1 second before the current time
#               must be passed in. For every other time, the last time a database insertion 
#               occured must be passed in.
# PostCondition: Returns the calculated average reading of the ultrasonic
def insertIntoDatabase(conn, oldTime):
    day = datetime.datetime.now().strftime("%A")
    newTime = str(datetime.datetime.now().time())[0:8]
    
    #Old time and current time converted into floats to compare them
    oldTimeNum = float(oldTime[6:8]) + 60 * float(oldTime[3:5]) + 3600 * float(oldTime[0:2])   
    newTimeNum = float(newTime[6:8]) + 60 * float(newTime[3:5]) + 3600 * float(newTime[0:2])
    
    if(abs(newTimeNum - oldTimeNum) > 0.5):
        curs = conn.cursor()
        sql = "INSERT INTO `XXXXXX`.`XXXXXX` (`ID`, `Timestamp`, `Time`, `DayOfWeek`) VALUES (NULL, CURRENT_TIMESTAMP, '" + newTime + "', '" + day + "')";
        curs.execute(sql)
        conn.commit()
        print("New entry inserted")
        return newTime
    else:
        return oldTime
        
        
def main():
    #connects program to the Arduino
    arduinoSerialData = serial.Serial('COM3', 9600)   
    
    oldTime = str(datetime.datetime.now().time())[0:8]
    print("Program Starting...")
    
    average = takeAverage(arduinoSerialData)
    print("Average:", average) 
    
    #input arguments are hostname, username, password, and database name
    db = connectToDatabase('XXXXXX', 'XXXXXX', 'XXXXXX', 'XXXXXX')
    while(not db):
        time.sleep(.1)
    print("Connected")
        
    while True:
        if(arduinoSerialData.inWaiting() > 0):
            myData = str(arduinoSerialData.readline())[2:6]
            try:
                myData = float(myData) / 2.54 * 2
                if(myData <= average/2.0):
                    oldTime = insertIntoDatabase(db, oldTime)
            except:
                print("Discarding Value", myData)            
        
main()