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
from itertools import product
from pprint import pprint
from datetime import timedelta

import TableNames
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
    permutationSize = int(sys.argv[1])
    sqlSelect = "SELECT max(combinationid) from %s "%(TableNames.BTX_TBL_IND_COMBINATION_MAPPING[0])
    sqlStmnt = ("%s") % (sqlSelect)
    cursor = conn.cursor()
    cursor.execute(sqlStmnt)
    maxId = cursor.fetchall()
    if len(maxId) > 0:
        maxId = [a[0] for a in maxId][0]+1
    else:
        maxId = 1
    print(maxId)
    '''
    This is the function definition:
    
    SELECT DISTINCT indref.id from indreference indref
    JOIN indmeasurementcombinations mc on mc.indicatorid = indref.id
    LEFT JOIN (
    select unnest(array_agg(indrefid)) as id from indcombinationmapping
    group by combinationid
    having array_length(array_agg(indrefid), 1) = %s -> permutationSize
    ) icm ON indref.id = icm.id
    WHERE isactive = 1 and icm.id IS NULL
    ORDER BY indref.id;
    
    The above returns all ref ids that are not part of a combination of 3 ref ids
    The idea is that every time a refernce value is added, a creation script will
    be run and will create the new combinations of references. The above view
    will ensure that there aren't any duplicates run.
    
    '''
    sqlSelect = "SELECT test(%s)"%(permutationSize)
    sqlStmnt = ("%s")%(sqlSelect)
    cursor = conn.cursor()
    cursor.execute(sqlStmnt)
    rows = cursor.fetchall()
    listOfCombos = []
    if len(rows) > 0:
        x = [row[0] for row in rows]
        buyCombos = list(permutations(x, permutationSize))
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

        print(len(listOfCombos))
        '''
        Insert listOfCombos into indcombinationmapping
        Below we are extracting the unique combo id to be represented in the strategy results tbl 
        '''
        buycomboids = list(sorted(set([i.combinationid for i in listOfCombos if i.buystrat == 1])))
        sellcomboids = list(sorted(set([i.combinationid for i in listOfCombos if i.sellstrat == 1])))
        print(len(buycomboids))
        print(len(list(sorted(set(buycomboids)))))
        '''
        Build cartesian product of buy and sell comboids
        These products are going to be inserted preemptively into indstrategyresults
        '''
        # for r in product(buycomboids, sellcomboids): print("%s | %s"%(r[0], r[1]))
        # for item in buycomboids:
        #     print(buycomboids)

except Exception as e:
    print("Uh oh, you done fucked up: %s" % (e))