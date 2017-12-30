<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 10/30/2017
 * Time: 11:45 PM
 *
 * Store the table constants as arrays:
 *      0: Table name
 *      1: Column names for insert new row
 *      2: For standard retrieval / update
 */
define('BTX_TBL_TESTING_LEDGER', 'btxledger');
define('BTX_TBL_COINS_TO_WATCH', 'btxcoinswatch');
define('BTX_TBL_MARKET_HISTORY',
    array(
        'btxmarkethistory',
        "(coin,market,volume,\"value\",\"usdValue\",high,low,\"lastSell\",\"currentBid\",\"openBuyOrders\",\"openSellOrders\",btxtimestamp,\"timestamp\")",
        "(id,coin,market,volume,\"value\",\"usdValue\",high,low,\"lastSell\",\"currentBid\",\"openBuyOrders\",\"openSellOrders\",btxtimestamp,\"timestamp\")"
    )
);
define('BTX_TBL_COIN_MARKET_HISTORY_DETAILS',
    array(
        'btxcoinmarkethistorydetails',
        "(btxid,coin,market,quantity,\"value\",\"usdValue\",total,filltype,ordertype,btxtimestamp)",
        "(id,btxid,coin,market,quantity,\"value\",\"usdValue\",total,filltype,ordertype,btxtimestamp)"
    )
);
define('BTX_TBL_RUNNING_TOTALS', 'btxrunningtotals');
define('BTX_TBL_TRANSACTIONS', 'btxtransactions');
define('BTX_TBL_COIN_HEADER',
    array(
        'btxcoinheader',
        "(coin,market,coinname,mintradesize,txfee,minconfirmation,isactive,btxtimestamp,timestamp,logourl)",
        "(id,coin,market,coinname,mintradesize,txfee,minconfirmation,isactive,btxtimestamp,timestamp,logourl)"
    )
);

define('BTX_TBL_HISTORY',
    array(
        'btxhistory',
        "(coin,market,description,history_ref_key,timestamp)",
        "(id,coin,market,description,history_ref_key,timestamp)"
    )
);

define('BTX_TBL_HISTORY_REF',
    array(
        'btxhistoryref',
        "(type,subtype,name,isactive)",
        "(id,type,subtype,name,isactive)"
    )
);