from datetime import datetime
import numpy

class Utilities:
    @staticmethod
    def calculateusdvalue(usdtBTCVal, value):
        retVal = 0.00
        retVal = usdtBTCVal * value
        return retVal

    @staticmethod
    def parsebtxtimestamp(t):
        strippedMSIdx = str(t).find(".")
        parsedTime = ""
        if strippedMSIdx > 0:
            substrTime = str(t)[:strippedMSIdx]
            parsedTime = datetime.strptime(substrTime, "%Y-%m-%dT%H:%M:%S")
        else:
            parsedTime = datetime.strptime(t, "%Y-%m-%dT%H:%M:%S")
        return parsedTime

    @staticmethod
    def removestringoccurences(haystack, needle, replacewith):
        retStr = haystack
        pre_strOccurCount = retStr.count(str(needle))
        strOccurCount = pre_strOccurCount
        while strOccurCount > 0:
            retStr = str.replace(retStr, needle, replacewith)
            strOccurCount = retStr.count(needle)
        post_strOccurCount = retStr.count(needle)
        return retStr


    @staticmethod
    def calculatemingivenarange(initialData, indirectSort, interval):
        intervalCalc = numpy.array([])
        min = 99999
        idxAtMin = 0
        bucket = 0
        for j in range(len(indirectSort)):
            modResult = j % interval
            idxForData = indirectSort[j]
            print("MODRESULT: %s" % (modResult))
            ## Indicates a new bucket
            if modResult == 0:
                bucket += 1
                idxAtMin = indirectSort[j]
                min = initialData[idxAtMin][1]
            elif modResult != 0:
                if initialData[idxForData][1] < min:
                    idxAtMin = indirectSort[j]
                    min = initialData[idxAtMin][1]
            print("CURRENT BUCKET: %s" % (bucket))
            print("Value J: %s | idxAtMin: %s" % (j, idxAtMin))
            print("Current MIN: %s | Current Initial Data: %s" % (min, initialData[idxForData][1]))
            ## End bucket - add min to numpy array
            if modResult == (interval - 1):
                print("FINAL DATA END OF BUCKET")
                print("MIN: %s" % (min))
                print("IDX For Min Value: %s" % (idxAtMin))
                print("_________________________")
                intervalCalc = numpy.append(intervalCalc, [min, initialData[idxAtMin][2]])
        return intervalCalc