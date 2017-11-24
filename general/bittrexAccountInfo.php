<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 8/18/2017
 * Time: 8:24 AM
 */
$retval = "";
//$mystring = system('python3 ../../python_bittrex_master/myTestScript.py', $retval);
//$mystring = system('../../py_bittrex/new_script_3.sh', $retval);
$dbconn = pg_connect("host=localhost dbname=bittrex user=bittrex_user password=password123")
or die('Could not connect: ' . pg_last_error());

// Performing SQL query
$query = 'SELECT * FROM btxmarkethistory
    ORDER BY timestamp DESC
    limit 100 ';
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

// Printing results in HTML
echo '<table class="table table-condensed">';
echo '<thead class="blue-grey lighten-4"><tr>
    <th>ID</th>
    <th>Coin</th>
    <th>Market</th>
    <th>Volume</th>
    <th>Value</th>
    <th>USD</th>
    <th>High</th>
    <th>Low</th>
    <th>Last Sell</th>
    <th>Current Bid</th>
    <th>Open Buys</th>
    <th>Open Sells</th>
    <th>BTX TS</th>
    <th>TS</th>
  </tr></thead>';
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    echo "<tr>";
    //foreach ($line as $col_value) {
    //    echo "<td>$col_value</td>";
    //}
    echo '<td>'.$line['id'].'</td>';
    echo '<td>'.$line['coin'].'</td>';
    echo '<td>'.$line['market'].'</td>';
    echo '<td>'.number_format($line['volume'],2).'</td>';
    echo '<td>'.number_format($line['value'],7).'</td>';
    echo '<td> $'.number_format($line['usdValue'],2).'</td>';
    echo '<td>'.number_format($line['high'],7).'</td>';
    echo '<td>'.number_format($line['low'],7).'</td>';
    echo '<td>'.number_format($line['lastSell'],7).'</td>';
    echo '<td>'.number_format($line['currentBid'],7).'</td>';
    echo '<td>'.$line['openBuyOrders'].'</td>';
    echo '<td>'.$line['openSellOrders'].'</td>';
    echo '<td>'.$line['btxTimestamp'].'</td>';
    echo '<td>'.$line['timestamp'].'</td>';
    //var_dump($line);
    echo "</tr>";
}
echo "</table>";

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);
//var_dump($mystring);
/*
$mystring = system('python3 ../../python_bittrex_master/bittrex/myTestScript.py myargs', $retval);
*/