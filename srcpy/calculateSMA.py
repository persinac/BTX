import time

try:
    import numpy
    import talib
    import psycopg2
    from datetime import datetime
    from pprint import pprint
    from datetime import timedelta

    close = numpy.random.random(100)
    output = talib.SMA(close)
    print(output)
except Exception as e:
    print("Uh oh, you done fucked up")
    print(e)