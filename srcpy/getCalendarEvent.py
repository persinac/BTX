import time
import numpy
import talib
import psycopg2
import sys
import logging
import math
import json
from datetime import datetime
from pprint import pprint
from datetime import timedelta
from config import config

logging.basicConfig(filename='/var/www/html/example.log',level=logging.DEBUG)
logger = logging.getLogger()
handler = logging.StreamHandler()
formatter = logging.Formatter(
        '%(asctime)s %(name)-12s %(levelname)-8s %(message)s')
handler.setFormatter(formatter)
logger.addHandler(handler)
logger.setLevel(logging.DEBUG)

try:
    params = config.config()
    # use our connection values to establish a connection
    conn = psycopg2.connect(
        host=params['pgsql']['host']
        , database=params['pgsql']['database']
        , user=params['pgsql']['user']
        , password=params['pgsql']['password']
    )

    epoch_time = int(time.time())
    currentTime = datetime.now()
    currMinuteTime_nonFormat = currentTime
    currMinuteTimeReplaceSeconds = currMinuteTime_nonFormat.replace(second=0, microsecond=0)

    # convert the times to EPOCH
    epochCurrTime = int(currMinuteTimeReplaceSeconds.timestamp())

    coin = sys.argv[1]
    eventType = sys.argv[2]
    eventDateLow = int(sys.argv[3])
    eventDateHigh = int(sys.argv[4])
    listoftuples = []
    tupleCoin = ("coin","in",coin)
    tupleEventType = ("typeofevent","in",eventType)
    tupleDateLow = ("eventdate",">=",eventDateLow)
    tupleDateHigh = ("eventdate","<=",eventDateHigh)
    listoftuples.append(tupleCoin)
    listoftuples.append(tupleEventType)
    listoftuples.append(tupleDateLow)
    listoftuples.append(tupleDateHigh)
    sqlSelect = "SELECT * from calevents"
    sqlWhere = "WHERE coin = '%s' and eventdate >= %s" % (coin, epoch_time)
    # Now we need to use numpy to order the data
    sqlStmnt = sqlSelect + " " + sqlWhere
    cursor = conn.cursor()
    cursor.execute(sqlStmnt)
    rows = cursor.fetchall()
    if len(rows) > 0:
        # print(str(rows).encode('utf-8'))
        data =[[row[0],
                row[1],
                row[2],
                row[3],
                row[4],
                row[5],
                row[6].encode('ascii', 'ignore').decode('ascii'),
                row[7].encode('ascii', 'ignore').decode('ascii'),
                row[8],
                row[9],
                row[10],
                row[11]
               ] for row in rows]
        print(data)

except Exception as e:
    print("Uh oh, you done fucked up: %s" % (e))