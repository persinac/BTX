"""
https://developer.twitter.com/en/docs/tweets/search/guides/standard-operators
https://developer.twitter.com/en/docs/tweets/search/overview/standard
https://developer.twitter.com/en/docs/tweets/search/api-reference/get-search-tweets.html
"""

import twitter
import json
api = twitter.Api(consumer_key='GgR4sdcHQall5deCH1UkZ2EVO',
                      consumer_secret='9QDlFbw3G6Mnm3b5QuLv4cTpnfheQ1PTFb4JHgGHx0JW96ykrv',
                      access_token_key='1049430014-rzIZJEuwiGhfCfMZ9Kwdmv8RQKtzUqcg1G2DS2x',
                      access_token_secret='CslWPnRsG3TgM7CdvTd4UmZ6a6tsw5b8w5GSnC22aHyXc')

# print(api.VerifyCredentials())
searchresults = api.GetSearch(raw_query="q=%24eth&since_id=956193850162327554")
print("Length of search results: %s"%(len(searchresults)))
for val in searchresults:
    print(val._json["id"])

