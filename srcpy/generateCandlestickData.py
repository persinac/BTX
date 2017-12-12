# noinspection PyInterpreter
import time
import numpy
import talib
import psycopg2
from datetime import datetime
from pprint import pprint
from datetime import timedelta
from config import config

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
    print(str(previousMinuteReplaceSeconds) + " | " + str(epochPrevTime)
          + " Curr: " + str(currMinuteTimeReplaceSeconds) + " | " + str(epochCurrTime))


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
    #
    #
    ##
    sqlSelect = "SELECT id, btxid, quantity, value, total, btxtimestamp, \"usdValue\" from btxcoinmarkethistorydetails"
    sqlWhere = "WHERE coin = '%s' AND btxtimestamp >= %d AND btxtimestamp < %d " % ("ETH", epochPrevTime, epochCurrTime)
    sqlOrderBy = "ORDER BY btxtimestamp ASC" #the first row is going to be the opener / last = closer
    sqlStmnt = sqlSelect + " " + sqlWhere + " " + sqlOrderBy
    cursor = conn.cursor()
    cursor.execute(sqlStmnt)
    rows = cursor.fetchall()
    ## Open-High-Low-Close Candlestick(s)
    opener = rows[0][3]
    closer = rows[len(rows)-1][3]
    ##this inline for each thing is known as: List Comprehensions
    x = numpy.array([row[3] for row in rows])
    #filter any outliers
    filtered = numpy.array(reject_outliers(x))
    print(opener)
    print(closer)
    print(filtered.min())
    print(filtered.max())
except Exception as e:
    print(e)