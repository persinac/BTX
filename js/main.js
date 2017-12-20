/**
 * Created by apersinger on 4/23/2017.
 */

function BuildHomePage(optionalParams) {
    var market = optionalParams.market || "BTC";
    var coin = optionalParams.coin || "ETH";
    var interval = optionalParams.interval || 1;
    var limit = optionalParams.limit || 100;
    var timePeriod = optionalParams.timePeriod || 14;
    BuildSMA(optionalParams);
    BuildRSI(optionalParams);
    BuildStochastics(optionalParams);
    BuildMACD(optionalParams);
    // $.ajax({
    //     type: "GET",
    //     url: "/src/api/get/getCalculatedSMA.php",
    //     dataType: "json",
    //     data: {
    //         market: "BTC"
    //         , coin: "ETH"},
    //     success: function(response) {
    //         console.log(response);
    //         drawBasic(response);
    //         drawBasicRSI(response);
    //         // $("#dyn_content").html(response);
    //     }
    // });
}

function BuildFilters(optionalParams) {
    var market = optionalParams.market || "BTC";
    $.ajax({
        type: "GET",
        url: "/src/buildCoinMarketDropdown.php",
        dataType: "json",
        success: function(response) {
            console.log(response);
            // $("#filters_markets").html(response['markets']);
            if(market == "BTC") {
                $("#filters_coins").html(response['coinMarkets'][1]['coins']);
            } else {
                $("#filters_coins").html(response['coinMarkets'][0]['coins']);
            }

        }
    });
}

function BuildSMA(optionalParams) {
    var market = optionalParams.market || "BTC";
    var coin = optionalParams.coin || "ETH";
    var interval = optionalParams.interval || 1;
    var limit = optionalParams.limit || 100;
    var timePeriod = optionalParams.timePeriod || 14;
    $.ajax({
        type: "GET",
        url: "/src/api/get/getCalculatedSMA.php",
        dataType: "json",
        data: {
            market: market
            , coin: coin
            , interval: interval
            , limit: limit
            , timePeriod: timePeriod
        },
        success: function(response) {
            console.log(response);
            $("#sma_loading").attr('src',"");
            drawBasic(response);
            // $("#dyn_content").html(response);
        }
    });
}

function BuildRSI(optionalParams) {
    var market = optionalParams.market || "BTC";
    var coin = optionalParams.coin || "ETH";
    var interval = optionalParams.interval || 1;
    var limit = optionalParams.limit || 100;
    var timePeriod = optionalParams.timePeriod || 14;
    $.ajax({
        type: "GET",
        url: "/src/api/get/getCalculatedRSI.php",
        dataType: "json",
        data: {
            market: market
            , coin: coin
            , interval: interval
            , limit: limit
            , timePeriod: timePeriod
        },
        success: function(response) {
            console.log(response);
            drawBasicRSI(response);
            // $("#dyn_content_rsi").html(response);
        }
    });
}

function BuildStochastics(optionalParams) {
    var market = optionalParams.market || "BTC";
    var coin = optionalParams.coin || "ETH";
    var interval = optionalParams.interval || 1;
    var limit = optionalParams.limit || 100;
    var timePeriod = optionalParams.timePeriod || 14;
    $.ajax({
        type: "GET",
        url: "/src/api/get/getCalculatedStochastic.php",
        dataType: "json",
        data: {
            market: market
            , coin: coin
            , interval: interval
            , limit: limit
            , timePeriod: timePeriod
        },
        success: function(response) {
            console.log("STOCHASTICS: ");
            console.log(response);
            drawBasicStochastic(response);
            // $("#dyn_content_rsi").html(response);
        }
    });
}

function BuildMACD(optionalParams) {
    var market = optionalParams.market || "BTC";
    var coin = optionalParams.coin || "ETH";
    var interval = optionalParams.interval || 1;
    var limit = optionalParams.limit || 100;
    var timePeriod = optionalParams.timePeriod || 14;
    $.ajax({
        type: "GET",
        url: "/src/api/get/getCalculatedMACD.php",
        dataType: "json",
        data: {
            market: market
            , coin: coin
            , interval: interval
            , limit: limit
            , timePeriod: timePeriod
        },
        success: function(response) {
            console.log("MACD: ");
            console.log(response);
            drawBasicMACD(response);
            // $("#dyn_content_rsi").html(response);
        }
    });
}

function BuildActionsPage() {
    var response = '<div class="upload col-md-3">';
    response += '<button id="sendAdHocEmail" type="button" class="btn btn-primary btn-md margin_5_px">Send Ad Hoc Email</button>';
    response += '<button id="uplNewBlogMthSch" type="button" class="btn btn-primary btn-md margin_5_px">Upload New Blog Month Schedule</button>';
    response += '<button id="uplNewEmpList" type="button" class="btn btn-primary btn-md margin_5_px">Upload New Employee List</button>';
    response += '<button id="setUpServiceAcct" type="button" class="btn btn-primary btn-md margin_5_px">Set up Service Account</button>';
    response += '<button id="displayCronJobList" type="button" class="btn btn-primary btn-md margin_5_px">Display Notification Schedule</button>';
    response += '</div>';
    response += '<div id="selectedAction" class="selectedAction col-md-9">';
    response += '</div>';
    $("#dyn_content").html(response);
    //BuildServiceAccountSetup();
}

function BuildSendAdHocEmailSection() {
    var response = "";
    response += '<input type="submit" value="Send Ad Hoc Email" name="genCronData" onclick="SendAdHocEmail();"></br>';
    response += '<div id="sendMailResponse">';
    response += '</div>';
    $("#selectedAction").html(response);
}

function BuildUploadNewBlogMonthDataSection() {
    var response = "";
    response += '<div class="dropdown">';
    response += '<button id="month_dd_btn" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">';
    response += 'Month ';
    response += '<span class="caret"></span></button>';
    response += '<ul class="dropdown-menu" id="month_dropdown">';
    response += '<li><a href="#" data-month="JAN">January</a></li>';
    response += '<li><a href="#" data-month="FEB">Febuary</a></li>';
    response += '<li><a href="#" data-month="MAR">March</a></li>';
    response += '<li><a href="#" data-month="APR">April</a></li>';
    response += '<li><a href="#" data-month="MAY">May</a></li>';
    response += '<li><a href="#" data-month="JUN">June</a></li>';
    response += '<li><a href="#" data-month="JULY">July</a></li>';
    response += '<li><a href="#" data-month="AUG">August</a></li>';
    response += '<li><a href="#" data-month="SEP">September</a></li>';
    response += '<li><a href="#" data-month="OCT">October</a></li>';
    response += '<li><a href="#" data-month="NOV">November</a></li>';
    response += '<li><a href="#" data-month="DEC">December</a></li>';
    response += '</ul>';
    response += '</div>';
    response += '<div class="dropdown">';
    response += '<button id="year_dd_btn" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">';
    response += 'Year';
    response += '<span class="caret"></span></button>';
    response += '<ul class="dropdown-menu" id="year_dropdown">';
    response += '<li><a href="#" data-year="17">2017</a></li>';
    response += '<li><a href="#" data-year="18">2018</a></li>';
    response += '<li><a href="#" data-year="19">2019</a></li>';
    response += '<li><a href="#" data-year="20">2020</a></li>';
    response += '<li><a href="#" data-year="21">2021</a></li>';
    response += '<li><a href="#" data-year="22">2022</a></li>';
    response += '</ul>';
    response += '</div>';
    response += '<div><input class="margin_10_px" type="file" name="fileToUpload" id="fileToUpload"></div>';
    response += '<div><input class="margin_10_px" type="submit" value="Upload File" name="submit" onclick="UploadFile();"></div>';
    $("#selectedAction").html(response);
}

function BuildUploadNewEmployeeListSection() {
    var response = "";
    response += '<input class="margin_10_px" type="file" name="fileToUpload" id="fileToUpload"></br>';
    response += '<label for="fileType" class="inlinecheckBoxLabel padding_rgt_5_px">';
    response += 'Employee List?'
    response += '</label>';
    response += '<input class="inlinecheckBox" type="checkbox" value="1", name="fileType" id="fileType" checked disabled>';
    response += '<input class="absolutePosition margin_10_px" type="submit" value="Upload File" name="submit" onclick="UploadFile();"></br>';
    $("#selectedAction").html(response);
}
/**
 *
 * @constructor
 *
 *
 */
function BuildServiceAccountSetup() {
    var response = '<div class="margin_10_px">';
    response += '<div class="form-group" id="svcacctipt">';
    response += '<label for="svcacctemail">Service Account Email</label>';
    response += '<input class="form-control margin_5_px" id="svcacctemail" type="text">';
    response += '<label for="svcacctpw">Service Account Password</label>';
    response += '<input class="form-control margin_5_px" id="svcacctpw" type="password">';
    response += '<input type="submit" ' +
            'class="margin_10_px"' +
        'value="Submit Service Acct" ' +
        'name="submitServiceAcct" ' +
        'onclick="SubmitServiceAccount();"></br>';
    response += '</div>';
    response += '<div id="serviceAcctResponse"></div>';
    $("#selectedAction").html(response);
}

function UploadFile() {
    var fileType = 0;
    var month = $("#month_dropdown li a");
    var year = $("#year_dropdown li a");
    var response = "<h3>Uploading...</h3>";
    var fileToUpload = $("#fileToUpload").prop('files')[0];
    if($("#fileType").is(':checked')) {fileType = 1};
    var formData = new FormData();
    formData.append("file",fileToUpload);
    formData.append("fileType", fileType);
    $("#dyn_content").html(response);
    $.ajax({
        type: "POST",
        url: "/CRUD/general/uploadFile.php",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
            //console.log(response);
            $("#dyn_content").html(response);
        }
    });
}

function SendAdHocEmail() {
    var response = "<h3>Sending...</h3>";
    $("#sendMailResponse").html(response);
    $.ajax({
        type: "POST",
        url: "/CRUD/cron/master.php",
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log(response);
            DisplayHistory("sendMailResponse");
            //$("#sendMailResponse").html(DisplayHistory());
        }
    });
}

function BuildBlogListTable() {
    var response = "<h3>Uploading...</h3>";
    $("#dyn_content").html(response);
    $.ajax({
        type: "GET",
        url: "/CRUD/general/homePageData.php",
        success: function(response) {
            //console.log(response);
            $("#dyn_content").html(response);
        }
    });
}

function BuildEmployeeListTable() {
    var response = "<h3>Uploading...</h3>";
    $("#dyn_content").html(response);
    $.ajax({
        type: "GET",
        url: "/CRUD/general/buildEmployeeListTable.php",
        success: function(response) {
            //console.log(response);
            $("#dyn_content").html(response);
        }
    });
}

function LoadEmployeeDetails() {
    var response = "<h3>Employee Details</h3>";
    $("#containerForTableLinkClicks").html(response);
}

function SubmitServiceAccount() {
    var response = "Submitting";
    var svcAcctEmail = $("#svcacctemail").val();
    var svcAcctPw = $("#svcacctpw").val();
    $.each($(".form-group input"), function( index, value) {
        console.log($(this).attr("id") + " : " + $(this).val());
    })
    var acctDetails = {email: svcAcctEmail, password: svcAcctPw};
    $("#serviceAcctResponse").html(response);
    $.ajax({
        type: "POST",
        url: "/CRUD/general/setServiceAccount.php",
        data: {data: JSON.stringify(acctDetails)},
        success: function(response) {
            console.log(response);
            //$("#sendMailResponse").html(response);
        }
    });
}

function ListSelectorPlaceholder(obj) {
    if(obj.data().month !== undefined) {
        //console.log("Month: " + obj.data().month)
        $("#month_dd_btn").html(obj.text() + ' <span class="caret"></span>');
        $("#month_dd_btn").val(obj.text());
    } else {
        //console.log("Year: " + obj.data().year)
        $("#year_dd_btn").html(obj.text() + ' <span class="caret"></span>');
        $("#year_dd_btn").val(obj.text());
    }
}

function DisplayHistory(domId) {
    $.ajax({
        type: "GET",
        url: "/CRUD/general/historyDisplay.php",
        success: function(response) {
            console.log(response);
            $("#" + domId + "").html(response);
        }
    });
}

function DisplayCurrentCronJobList() {
    $.ajax({
        type: "GET",
        url: "/CRUD/cron/listCronTab.php",
        success: function(response) {
            console.log(response);
        }
    });
}

function drawBasic(myData) {

    var data = new google.visualization.DataTable();
    data.addColumn('timeofday', 'X');
    data.addColumn('number', 'SMA');
    // A column for custom tooltip content
    //data.addColumn({type: 'string', role: 'tooltip'});
    data.addRows(myData);

    var options = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            0:{title: 'SMA'}
        },
        series: {
            0: {targetAxisIndex:0}
        },
        annotations: {
            textStyle: {
                fontName: 'Times-Roman',
                fontSize: 18,
                bold: true,
                italic: true,
                // The color of the text.
                color: '#871b47',
                // The color of the text outline.
                auraColor: '#d799ae',
                // The transparency of the text.
                opacity: 0.8
            }
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('dyn_content_sma'));

    chart.draw(data, options);
}

function drawBasicRSI(myData) {
    var data = new google.visualization.DataTable();
    data.addColumn('timeofday', 'X');
    data.addColumn('number', 'RSI');
    // A column for custom tooltip content
    data.addColumn({type: 'string', role: 'tooltip'});
    data.addRows(myData);

    var options = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            0:{title: 'RSI'}
        },
        series: {
            0: {targetAxisIndex:0}
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('dyn_content_rsi'));

    chart.draw(data, options);
}

function drawBasicStochastic(myData) {
    var data = new google.visualization.DataTable();
    data.addColumn('timeofday', 'X');
    data.addColumn('number', 'SlowK');
    data.addColumn('number', 'SlowD');
    // A column for custom tooltip content
    data.addColumn({type: 'string', role: 'tooltip'});
    data.addRows(myData);

    var options = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            0:{title: 'Value - SlowK'},
            1:{title: 'Value - SlowD'}
        },
        series: {
            0: {targetAxisIndex:0},
            1: {targetAxisIndex:1}
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('dyn_content_stoch'));

    chart.draw(data, options);
}

function drawBasicMACD(myData) {
    var data = new google.visualization.DataTable();
    data.addColumn('timeofday', 'X');
    data.addColumn('number', 'MACD');
    data.addColumn('number', 'Signal');
    // A column for custom tooltip content
    data.addColumn({type: 'string', role: 'tooltip'});
    data.addRows(myData);

    var options = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            0:{title: 'MACD'},
            1:{title: 'Signal'}
        },
        series: {
            0: {targetAxisIndex:0},
            1: {targetAxisIndex:1}
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('dyn_content_macd'));

    chart.draw(data, options);
}