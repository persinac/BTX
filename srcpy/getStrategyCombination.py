import time
import numpy
import talib
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
from classes import StrategyResults
from config import config
from connection import PGConnect
from calendar import timegm
from datetime import datetime
from classes import History
from CRUD.create import BTXCreator

logging.basicConfig(filename='/var/www/html/example.log',level=logging.DEBUG)
logger = logging.getLogger()
handler = logging.StreamHandler()
formatter = logging.Formatter(
        '%(asctime)s %(name)-12s %(levelname)-8s %(message)s')
handler.setFormatter(formatter)
logger.addHandler(handler)
logger.setLevel(logging.DEBUG)

try:
    pgconn = PGConnect.DBase()
    permutationSize = int(sys.argv[1])
    sqlSelect = "SELECT max(combinationid) from %s "%(TableNames.BTX_TBL_IND_COMBINATION_MAPPING[0])
    sqlStmnt = ("%s") % (sqlSelect)
    maxId = pgconn.query(sqlStmnt)
    if len(maxId) > 0:
        maxId = [a[0] for a in maxId][0]
        if maxId == None:
            maxId = 1
        else:
            maxId += 1
    else:
        maxId = 1
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
    rows = pgconn.query(sqlStmnt)
    listOfCombos = []
    if len(rows) > 0:
        x = [row[0] for row in rows]
        buyCombos = list(permutations(x, permutationSize))
        sellCombos = buyCombos
        lengthOfCombo = len(buyCombos)
        ## First - generate list of BUY strat combo strategy objects
        for i in range(lengthOfCombo):
            comboId = i + maxId
            for k in buyCombos[i]:
                combo = CombinationMapping.CombinationMapping(
                    -1, comboId, k, 1, 0, datetime.now()
                )
                listOfCombos.append(combo)

        ## increment maxId
        maxId += len(buyCombos)
        # print("mid maxid: %s" % (maxId))
        ## Second - generate list of SELL strat combo strategy objects
        for i in range(lengthOfCombo):
            # add to maxid so that the id continues
            comboId = i + maxId
            for k in sellCombos[i]:
                combo = CombinationMapping.CombinationMapping(
                    -1, comboId, k, 0, 1, datetime.now()
                )
                listOfCombos.append(combo)

        insertStmtn = BTXCreator.BTXCreator.buildinsertstatement(listOfCombos,
                                                                 TableNames.BTX_TBL_IND_COMBINATION_MAPPING[1],
                                                                 TableNames.BTX_TBL_IND_COMBINATION_MAPPING[0])
        numOfInserts = len(listOfCombos)
        if numOfInserts > 0:
            # Insert listOfCombos into indcombinationmapping
            pgconn.insert(insertStmtn)
        '''
        Below we are extracting the unique combo id to be represented in the strategy results tbl 
        '''
        buycomboids = list(sorted(set([i.combinationid for i in listOfCombos if i.buystrat == 1])))
        sellcomboids = list(sorted(set([i.combinationid for i in listOfCombos if i.sellstrat == 1])))
        '''
        Build cartesian product of buy and sell comboids for each ticker value
        These products are going to be inserted preemptively into indstrategyresults
        '''
        tickerVals = [5, 30]
        cartProduct = product(buycomboids, sellcomboids)
        listOfResultsToInsert = []
        for r in cartProduct:
            for ticker in tickerVals:
                '''
                r[0] = buy strat
                r[1] = sell strat
                '''
                newResult = StrategyResults.StrategyResults(
                    -1, r[0], r[1], ticker, "None","None",0,0,0,0,0,0,0,0,datetime.now(),datetime.now()
                )
                listOfResultsToInsert.append(newResult)

        '''
        If the insert list contains > 1500 items - break it down
        '''
        while len(listOfResultsToInsert) > 1500:
            itemsToInsert = listOfResultsToInsert[:1500]
            listOfResultsToInsert = listOfResultsToInsert[1500:]
            print("Perform insert...")
            print(len(listOfResultsToInsert))
        '''
        insertStmtn = BTXCreator.BTXCreator.buildinsertstatement(listOfResultsToInsert,
                                                                 TableNames.BTX_TBL_IND_STRATEGY_RESULTS[1],
                                                                 TableNames.BTX_TBL_IND_STRATEGY_RESULTS[0])
        numOfInserts = len(listOfResultsToInsert)
        # print(insertStmtn)
        if numOfInserts > 0:
            # Insert listOfCombos into indcombinationmapping
            pgconn.insert(insertStmtn)
        '''

except Exception as e:
    print("Uh oh, you done fucked up: %s" % (e))