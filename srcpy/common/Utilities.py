from datetime import datetime
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
    def removestringoccurences(haystack, needle):
        retStr = haystack
        pre_strOccurCount = retStr.count(str(needle))
        strOccurCount = pre_strOccurCount
        print(strOccurCount)
        while strOccurCount > 0:
            retStr = str.replace(retStr, ",,", ",")
            strOccurCount = retStr.count(",,")
        post_strOccurCount = retStr.count(",,")
        print("POST %s" % (str(strOccurCount)))
        return retStr
