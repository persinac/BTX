# noinspection PyInterpreter
import time
import numpy
import talib
import psycopg2
import sys
import logging
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

def reject_outliers(data, m=2):
    return data[abs(data - numpy.mean(data)) < m * numpy.std(data)]

try:
    params = config.config()
    # use our connection values to establish a connection
    conn = psycopg2.connect(
        host=params['pgsql']['host']
        ,database=params['pgsql']['database']
        , user=params['pgsql']['user']
        , password=params['pgsql']['password']
    )

    epoch_time = int(time.time())
    currentTime = datetime.now()

    # Actually going to calculate 1 minute behind - to allow the
    # other batch jobs to complete / insert data
    previousMinuteTime_nonFormat = currentTime - timedelta(minutes=2)
    previousMinuteReplaceSeconds = previousMinuteTime_nonFormat.replace(second=0, microsecond=0)

    currMinuteTime_nonFormat = currentTime - timedelta(minutes=1)
    currMinuteTimeReplaceSeconds = currMinuteTime_nonFormat.replace(second=0, microsecond=0)

    # convert the times to EPOCH
    epochPrevTime = int(previousMinuteReplaceSeconds.timestamp())
    epochCurrTime = int(currMinuteTimeReplaceSeconds.timestamp())

    ##
    # get unique list of coins and markets
    ##
    sqlSelect = "SELECT coin, market from btxcoinheader"
    sqlGroupBy = "group by coin, market"
    sqlOrderBy = "ORDER BY market, coin"
    sqlStmnt = sqlSelect + " " + sqlGroupBy + " " + sqlOrderBy
    cursor = conn.cursor()
    cursor.execute(sqlStmnt)
    uniqueRows = cursor.fetchall()
    dataTuple = []
    for uniqueRow in uniqueRows:
        currMarket = uniqueRow[1]
        currCoin = uniqueRow[0]

        ##
        # when accessing the rows, use the following Index values that correspond to columns:
        # 0 - id
        # 1 - btxid
        # 2 - quantity
        # 3 - value
        # 4 - total
        # 5 - btxtimestamp
        # 6 - usdValue
        #
        # numpy.argmin() will give us the index
        #
        # test empty result set with btxtimestamp low = 1513039680 / high = 1513039740
        ##

        sqlSelect = "SELECT id, btxid, quantity, value, total, btxtimestamp, \"usdValue\" from btxcoinmarkethistorydetails"
        sqlWhere = "WHERE market = '%s' AND coin = '%s' AND btxtimestamp >= %d AND btxtimestamp < %d " % (currMarket, currCoin, epochPrevTime, epochCurrTime)
        sqlOrderBy = "ORDER BY btxtimestamp ASC" #the first row is going to be the opener / last = closer
        sqlStmnt = sqlSelect + " " + sqlWhere + " " + sqlOrderBy
        cursor = conn.cursor()
        cursor.execute(sqlStmnt)
        rows = cursor.fetchall()
        ## Open-High-Low-Close Candlestick(s)
        ##this inline for each thing is known as: List Comprehensions
        rowsToInsert = ""
        if len(rows) > 0:
            x = numpy.array([row[3] for row in rows])
            #filter any outliers
            if len(x) > 10:
                filtered = numpy.array(reject_outliers(x))
            else:
                filtered = x
            opener = numpy.round(filtered[0], 9)
            closer = numpy.round(filtered[len(filtered) - 1], 9)
            min = numpy.round(filtered.min(), 9)
            max = numpy.round(filtered.max(), 9)

            mytuple = (
                currCoin, currMarket, opener, closer, max, min,
                'BITTREX', epochPrevTime, epochCurrTime, epoch_time
            )
            dataTuple.append(mytuple)
        else:
            sqlWhere = "WHERE market = '%s' AND coin = '%s' " % (currMarket, currCoin)
            sqlSelect = "select t1.* from tkmcandlesticks t1 " \
                        "JOIN (" \
                            "select max(maxid.id) AS maxid from tkmcandlesticks maxid"
            sqlSelect += " " + sqlWhere
            sqlSelect += ") AS t2 on t2.maxid = t1.id"
            sqlStmnt = sqlSelect
            cursor.execute(sqlStmnt)
            secondResult = cursor.fetchall()
            if len(secondResult) > 0:
                addSecondsToLow = secondResult[0][8] + 60
                addSecondsToHigh = secondResult[0][9] + 60
                opener = secondResult[0][3]
                closer = secondResult[0][4]
                max = secondResult[0][5]
                min = secondResult[0][6]
                mytuple = (
                    currCoin, currMarket, opener, closer, max, min,
                    'BITTREX', epochPrevTime, epochCurrTime, epoch_time
                )
                dataTuple.append(mytuple)

    rowsToInsert = ",".join(
        cursor.mogrify('(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)', thing).decode('UTF-8')
        for thing in dataTuple
    )
    if len(rowsToInsert) > 0:
        cursor.execute("""INSERT INTO tkmcandlesticks(
            coin, market, opener, closer, high, low, exchange, timestampintervallow, timestampintervalhigh, createdon)
            VALUES """ + rowsToInsert)
        conn.commit()
    else:
        print(rowsToInsert + " | " + str(len(rowsToInsert)))
except Exception as e:
    print(e)