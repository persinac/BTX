import sys
import logging

class BTXCreator:

    @staticmethod
    def buildinsertstatement(listofinsert, valueList, tableName):
        retVal = "INSERT INTO %s %s VALUES " % (tableName, valueList)
        for i in range(len(listofinsert)):
            if i > 0:
                retVal += ","
            retVal += "("
            retVal += listofinsert[i].createcommadelimitedvalueforinsert()
            retVal += ")"
        return retVal

    @staticmethod
    def buildvaluesforinsertstatement(listofinsert):
        retVal = ""
        for i in range(len(listofinsert)):
            value = listofinsert[i].createcommadelimitedvalueforinsert()
            if i > 0:
                retVal += ","
            retVal += "("
            retVal += listofinsert[i].createcommadelimitedvalueforinsert()
            retVal += ")"
        return retVal

    @staticmethod
    def buildmaininsertstatement(insertstmnt, valueList, tableName):
        cleanUp = str(insertstmnt).strip()[:4]
        if cleanUp.count(",") > 0:
            cleanUp = insertstmnt[1:]
        else:
            cleanUp = str(insertstmnt).strip()

        retVal = "INSERT INTO %s %s VALUES %s" % (tableName, valueList, cleanUp)
        return retVal

    ## ListOfArgs = listof tuples
    ## Tuple -> [i][0] = field for where clause
    ## Tuple -> [i][1] = operator
    ## Tuple -> [i][2] = value
    @staticmethod
    def buildwhereclause(listofargs):
        retval = ""
        for arg in listofargs:
            if arg[1] == "in":
                ""