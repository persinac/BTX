import sys
import os
import logging
import requests
from calendar import timegm
from datetime import datetime
from bs4 import BeautifulSoup

import globals
import TableNames
from connection import PGConnect
from classes import History
from classes import CalendarEvent
from CRUD.create import BTXCreator
from common import Utilities

logging.basicConfig(filename='/var/www/html/example.log',level=logging.DEBUG)
logger = logging.getLogger()
handler = logging.StreamHandler()
formatter = logging.Formatter(
        '%(asctime)s %(name)-12s %(levelname)-8s %(message)s')
handler.setFormatter(formatter)
logger.addHandler(handler)
logger.setLevel(logging.DEBUG)

## Reference data
pgconn = PGConnect.DBase()
currFile = os.path.splitext(__file__)[0]
fileNameFormat = "%Y_%m_%d_%H:%M:%S"
sqlSelect = "SELECT * from %s" % (TableNames.BTX_TBL_HISTORY_REF[0])
sqlWhere = "WHERE name = '%s' and type = '%s'" % (currFile, globals.HISTORY_REF_TYPE_CRON_JOB)
sqlFull = sqlSelect + " " + sqlWhere
historyRefVal = pgconn.query(sqlFull)

historyTuple = []

##
# Container @ idx 0 = banner / ad
# Container @ idx 1 = event data / buttons
# Container @ idx 2 = search bar
##
# [print(event.prettify()) for event in eventcontainer]
##
page = requests.get("https://coinmarketcal.com/")
soup = BeautifulSoup(page.content, 'html.parser')
findexplore = soup.find(id="explore")
eventcontainer = findexplore.find_all(class_="container")
nav = eventcontainer[1].find_all('nav')
pagenums = [int(link['href'][link['href'].index("=") + 1:]) for link in eventcontainer[1].find_all('a', href=True) if "page" in link['href']]
maxpagenum = max(pagenums)
listofurls = ["https://coinmarketcal.com/?page=%s"%(i+1) for i in range(maxpagenum)]

starttime = str(datetime.now())


listofcalevents = []

for url in listofurls:
    page = requests.get(url)
    soup = BeautifulSoup(page.content, 'html.parser')
    findexplore = soup.find(id="explore")
    eventcontainer = findexplore.find_all(class_="container")
    articles = eventcontainer[1].find_all('article')
    for article in articles:
        divtobreakdown = article.find('div')
        headerfordateanddata = divtobreakdown.find('h5')
        event_div = divtobreakdown.find(class_="content-box-info")
        ## We want to get the text from here because the data
        ## from the 'data-*' fields only include the date,
        ## we want to know it's going to happen on the date OR BY the date
        dateofevent = headerfordateanddata.find('strong').get_text()

        isByDate = 0
        if dateofevent.count("By") > 0:
            isByDate = 1
            dateofevent = str.strip(dateofevent[str.index(dateofevent, "By") + len("By"):])

        dateofevent = str.strip(dateofevent)
        added_on = event_div.find('p', class_="added-date").get_text()
        added_on = str.strip(added_on[str.index(added_on, "Added") + len("Added"):])
        added_on = added_on.replace("]", "")

        ## Give us midnight of the date in epoch for storage purposes
        epochdateofevent = int(datetime.strptime(dateofevent, "%d %B %Y").timestamp())
        epochadded_on = int(datetime.strptime(added_on, "%d %B %Y").timestamp())
        added_on = datetime.strptime(added_on, "%d %B %Y").strftime("%Y-%d-%m")
        dateofevent = datetime.strptime(dateofevent, "%d %B %Y").strftime("%Y-%d-%m")

        event_metadata = headerfordateanddata.find('a')
        coin_name = event_metadata['data-coin']
        coinmarketcal_eventid = event_metadata['data-idevent']
        event_title = event_metadata['data-title']
        ## Clean up double and single quotes
        event_title = Utilities.Utilities.removestringoccurences(event_title, '"', "")
        event_title = Utilities.Utilities.removestringoccurences(event_title, "'", "")

        proofofsources = event_div.find_all('a')
        proofofsource = [sourceproof.get('href') for sourceproof in proofofsources if str.strip(sourceproof.get_text()) == "Source"]

        if len(proofofsource) > 0:
            proofofsource = proofofsource[0]
        else:
            proofofsource = ""

        source = "www.coinmarketcal.com"

        event_description = str.strip(event_div.find('p', class_="description").get_text())
        ## Clean up double and single quotes
        event_description = Utilities.Utilities.removestringoccurences(event_description, '"', "")
        event_description = Utilities.Utilities.removestringoccurences(event_description, "'", "")
        event_votes = event_div.find('span', class_="votes").get_text()
        shorthandcoinname = coin_name[coin_name.index("(") + 1:coin_name.index(")")]

        ## check in our DB: if the id from the source exists
        ## source being: coinmarketcal.com
        sqlSelect = "SELECT count(*) from %s" % (TableNames.BTX_TBL_CALENDAR_EVENTS[0])
        sqlWhere = "WHERE idfromsource = '%s'"%(coinmarketcal_eventid)
        sqlFull = sqlSelect + " " + sqlWhere
        idfromsourceexist = pgconn.query(sqlFull)

        if int(idfromsourceexist[0][0]) < 1:
            calEvent = CalendarEvent.CalendarEvent(
                -1, coinmarketcal_eventid, source, shorthandcoinname, epochadded_on,
                epochdateofevent, event_title, event_description, event_votes, 1, isByDate
                , proofofsource
            )
            listofcalevents.append(calEvent)

insertStmtn = BTXCreator.BTXCreator.buildinsertstatement(listofcalevents, TableNames.BTX_TBL_CALENDAR_EVENTS[1], TableNames.BTX_TBL_CALENDAR_EVENTS[0])
numOfInserts = len(listofcalevents)
# print(insertStmtn)
if numOfInserts > 0:
    pgconn.insert(insertStmtn)

endtime = str(datetime.now())
retValToEcho = "Duration: " + starttime + " - " + endtime + " | Inserted " + str(numOfInserts) + " rows into calevents."
# Initialize final BTXHistory obj to capture end of file statement
historyObj = History.History(
    -1
        , "" ## no specific coin
    , "" ## no specific market
    , retValToEcho
    , historyRefVal[0][0]
    , timegm(datetime.utcnow().utctimetuple())
)
historyTuple.append(historyObj)
insertStmtn = BTXCreator.BTXCreator.buildinsertstatement(historyTuple, TableNames.BTX_TBL_HISTORY[1], TableNames.BTX_TBL_HISTORY[0])
pgconn.insert(insertStmtn)
