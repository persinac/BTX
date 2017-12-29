function GetCoinData(optionalParams) {
    var market = optionalParams.market || "BTC";
    var coin = optionalParams.coin || "ETH";
    var response = "<h3>loading...</h3>";
    $.ajax({
        type: "GET",
        url: "/src/api/get/getcurrcoindetailsfrombtx.php",
        data: {
            market: market
            , coin: coin
        },
        success: function(response) {
            var jsonResponse = JSON.parse(response);
            console.log(jsonResponse);
            console.log(jsonResponse.response[1].logourl);
            var tempHTML = '<div></br> ' +
                '<label for="price">Price: </label><span id="price"> '+jsonResponse.response[0].Price+' </span>' +
                '</br><label for="qty">Quantity </label><span id="qty"> '+jsonResponse.response[0].Quantity+' </span>' +
                '</br><label for="timestamp">Low: </label><span id="timestamp"> '+jsonResponse.response[2].high+' </span>' +
                '</br><label for="timestamp">High: </label><span id="timestamp"> '+jsonResponse.response[2].low+' </span>' +
                '</br><label for="timestamp">Time: </label><span id="timestamp"> '+jsonResponse.response[0].TimeStamp+' </span>' +
                '</div>';

            var tempHTML2 = '<div></br> ' +
                '<label for="price">Value: </label><span id="price"> '+jsonResponse.response[2].value+' </span>' +
                '</br><label for="price">USD Value: </label><span id="price"> '+jsonResponse.response[2].usdValue+' </span>' +
                '</br><label for="price">Open Buys: </label><span id="price"> '+jsonResponse.response[2].openBuyOrders+' </span>' +
                '</br><label for="qty">Open Sells </label><span id="qty"> '+jsonResponse.response[2].openSellOrders+' </span>' +
                '</br><label for="qty">Volume </label><span id="qty"> '+jsonResponse.response[2].volume+' </span>' +
                '</div>';
            $("#coin_picture").html('<img id="coin_pic_logo" style="height: 50%;width: 50%" src="'+jsonResponse.response[1].logourl+'" />');
            $("#coin_curr_data").html(tempHTML);
            $("#coin_historical_data").html(tempHTML2);
        }
    });
}