class CombinationMapping:
    def __init__(self, id, combinationid, idrefid, buystrat, sellstrat, createdon):
        self.id = id
        self.combinationid = combinationid
        self.idrefid = idrefid
        self.buystrat = buystrat
        self.sellstrat = sellstrat
        self.createdon = createdon

    def __enter__(self):
        return self

    def createcommadelimitedvalueforinsert(self):
        retVal = "%s,%s,%s,%s,'%s'"%(
            self.combinationid,self.idrefid
            ,self.buystrat,self.sellstrat
            ,self.createdon
        )
        return retVal