class CalendarEvent:
    def __init__(self, id, idfromsource, source, coin, eventaddedon, eventdate,
                 title, description, validity, typeofevent, byoron, proofofsource):
        self.id = id
        self.idfromsource = idfromsource
        self.source = source
        self.coin = coin
        self.eventaddedon = eventaddedon
        self.eventdate = eventdate
        self.title = title
        self.description = description
        self.validity = validity
        self.typeofevent = typeofevent
        self.byoron = byoron
        self.proofofsource = proofofsource

    def __enter__(self):
        return self

    def createcommadelimitedvalueforinsert(self):
        retVal = ""
        retVal += "'" + self.idfromsource + "'"
        retVal += ", '" + self.source + "'"
        retVal += ", '" + self.coin + "'"
        retVal += ", %s"%(self.eventaddedon)
        retVal += ", %s"%(self.eventdate)
        retVal += ",'" + self.title + "'"
        retVal += ", '" + self.description + "'"
        retVal += ", '" + self.validity + "'"
        retVal += ", %s"%(self.typeofevent)
        retVal += ", %s"%(self.byoron)
        retVal += ", '%s'"%(self.proofofsource)
        return retVal