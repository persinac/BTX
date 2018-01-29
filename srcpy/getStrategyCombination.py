import time
import numpy
import talib
import psycopg2
import sys
import logging
import math
import json

from datetime import datetime
from itertools import permutations
from pprint import pprint
from datetime import timedelta

from classes import CombinationMapping
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
    sqlSelect = "SELECT DISTINCT indref.id from indreference indref"
    sqlJoin = "JOIN indmeasurementcombinations mc on mc.indicatorid = indref.id"
    sqlWhere = "WHERE isactive = 1"
    sqlOrder = "ORDER BY indref.id"
    sqlStmnt = ("%s %s %s %s")%(sqlSelect,sqlJoin,sqlWhere,sqlOrder)
    cursor = conn.cursor()
    cursor.execute(sqlStmnt)
    rows = cursor.fetchall()
    listOfCombos = []
    if len(rows) > 0:
        x = [row[0] for row in rows]
        buyCombos = list(permutations(x, 3))
        sellCombos = buyCombos
        lengthOfCombo = len(buyCombos)
        ## First - generate list of BUY strat combo strategy objects
        for i in range(lengthOfCombo):
            comboId = i
            for k in buyCombos[i]:
                combo = CombinationMapping.CombinationMapping(
                    -1, comboId, k, 1, 0, datetime.now()
                )
                listOfCombos.append(combo)

        ## Second - generate list of SELL strat combo strategy objects
        for i in range(lengthOfCombo):
            # add to current length so that the combo id continues
            comboId = i + lengthOfCombo
            for k in sellCombos[i]:
                combo = CombinationMapping.CombinationMapping(
                    -1, comboId, k, 0, 1, datetime.now()
                )
                listOfCombos.append(combo)

        # for item in listOfCombos:
        #     print(item.createcommadelimitedvalueforinsert())

except Exception as e:
    print("Uh oh, you done fucked up: %s" % (e))