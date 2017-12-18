<?php

require_once("src/settings.php");

/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 3/21/2017
 * Time: 1:34 PM
 */
//echo BOOTSTRAP_VERSION;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head;
        any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Blog Scheduler</title>

    <!-- Bootstrap core CSS
    <link href="../../dist/css/bootstrap.min.css" rel="stylesheet">
    -->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug
    <link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
    -->

    <!-- Custom styles for this template -->
    <link href="/css/general.css" rel="stylesheet">
    <link href="/css/main.css" rel="stylesheet">
    <link href="/css/tables.css" rel="stylesheet">
    <link href="/css/actions.css" rel="stylesheet">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/<?php echo BOOTSTRAP_VERSION; ?>/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/<?php echo BOOTSTRAP_VERSION; ?>/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>


    <![endif]-->
</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">BTX</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li id="home" class="active"><a href="#">Home</a></li>
                <li id="employeeList"><a href="#employeeList">Tab2</a></li>
                <li id="actions"><a href="#actions">Actions</a></li>
                <li id="history"><a href="#history">History</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container">

    <div class="starter-template">
        <div id="dyn_content">
            <div class="row">
                <div class="col-lg-4" id="filters_markets">

                </div>
                <div class="col-lg-4" id="filters_coins">

                </div>
                <div class="col-lg-4" id="filters_xyz">

                </div>
            </div>
            <div class="row">
                <div class="col-lg-4" id="refresh">
                    <button type="button" id="refresh_data" class="btn btn-primary margin_10_px">Refresh Data</button>
                </div>

            </div>
        <div class="row">
            <div class="col-lg-6" id="value">

            </div>
            <div class="col-lg-6" id="value2">

            </div>
        </div>
        <div class="row">
            <div class="col-lg-6" id="dyn_content_sma" style="height:300px">
                <img id="sma_loading" src="" />
            </div>
            <div class="col-lg-6" id="dyn_content_rsi" style="height:300px"></div>
        </div>
        <div class="row">
            <div class="col-lg-6" id="dyn_content_stoch" style="height:300px"></div>
            <div class="col-lg-6" id="dyn_content_macd" style="height:300px"></div>
        </div>
        </div>
    </div>
</div><!-- /.container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/<?php echo BOOTSTRAP_VERSION; ?>/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="/js/main.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawBasic);
    google.charts.setOnLoadCallback(drawBasicRSI);
    // Wait for the page to load first
//    window.onload = function() {
//
//        //Get a reference to the link on the page
//        // with an id of "mylink"
//        var a = document.getElementById("mylink");
//        a.onclick = function() {
//
//            // Your code here...
//
//            //If you don't want the link to actually
//            // redirect the browser to another page,
//            // "google.com" in our example here, then
//            // return false at the end of this block.
//            // Note that this also prevents event bubbling,
//            // which is probably what we want here, but won't
//            // always be the case.
//            return false;
//        }
//    }
    $(document).ready(function() {
        $("#dyn_content_sma").html('<img id="sma_loading" src="https://loading.io/spinners/recycle/lg.recycle-spinner.gif" />');
        $("#dyn_content_rsi").html('<img id="sma_loading" src="https://loading.io/spinners/recycle/lg.recycle-spinner.gif" />');
        $("#dyn_content_stoch").html('<img id="sma_loading" src="https://loading.io/spinners/recycle/lg.recycle-spinner.gif" />');
        $("#dyn_content_macd").html('<img id="sma_loading" src="https://loading.io/spinners/recycle/lg.recycle-spinner.gif" />');
        BuildFilters();
        // BuildRSI();
        // BuildSMA();
        // BuildStochastics();
        // BuildMACD();
        BuildHomePage("ETH");
        $("#home a").click(function() {
            // BuildHomePage();
            $("#employeeList").removeClass("active");
            $("#home").addClass("active");
            $("#actions").removeClass("active");
            $("#history").removeClass("active");
        });

        $("#employeeList a").click(function() {
            // BuildEmployeeListTable();
            $("#employeeList").addClass("active");
            $("#home").removeClass("active");
            $("#actions").removeClass("active");
            $("#history").removeClass("active");
        });

        $("#actions a").click(function() {
            // BuildActionsPage();
            $("#employeeList").removeClass("active");
            $("#home").removeClass("active");
            $("#actions").addClass("active");
            $("#history").removeClass("active");
        });

        $("#history a").click(function() {
            // DisplayHistory("dyn_content");
            $("#employeeList").removeClass("active");
            $("#home").removeClass("active");
            $("#actions").removeClass("active");
            $("#history").addClass("active");
        });

        $("#refresh_data").click(function() {
            $("#dyn_content_sma").html('<img id="sma_loading" src="https://loading.io/spinners/recycle/lg.recycle-spinner.gif" />');
            $("#dyn_content_rsi").html('<img id="sma_loading" src="https://loading.io/spinners/recycle/lg.recycle-spinner.gif" />');
            $("#dyn_content_stoch").html('<img id="sma_loading" src="https://loading.io/spinners/recycle/lg.recycle-spinner.gif" />');
            $("#dyn_content_macd").html('<img id="sma_loading" src="https://loading.io/spinners/recycle/lg.recycle-spinner.gif" />');
            BuildHomePage($("#filters_coins").find(":selected").text());
        });

        $('#dyn_content').delegate('table#employee tr td a.details', 'click', function() {
            LoadEmployeeDetails()
        });
        $('#dyn_content').delegate('#sendAdHocEmail', 'click', function() {
            BuildSendAdHocEmailSection()
        });
        $('#dyn_content').delegate('#uplNewBlogMthSch', 'click', function() {
            BuildUploadNewBlogMonthDataSection()
        });
        $('#dyn_content').delegate('#uplNewEmpList', 'click', function() {
            BuildUploadNewEmployeeListSection()
        });
        $('#dyn_content').delegate('#setUpServiceAcct', 'click', function() {
            BuildServiceAccountSetup()
        });

        $('#dyn_content').delegate('#filters_coins option', 'click', function() {
            $("#value").html($(this))
        });

        $('#dyn_content').on('#filters_coins option', 'click', function() {
            $("#value").html($(this))
        });

        $('#dyn_content').on('change', '#filters_coins', function(){
            // $("#value").html($("#filters_coins").find(":selected").text());
            $("#dyn_content_sma").html('<img id="sma_loading" src="https://loading.io/spinners/recycle/lg.recycle-spinner.gif" />');
            $("#dyn_content_rsi").html('<img id="sma_loading" src="https://loading.io/spinners/recycle/lg.recycle-spinner.gif" />');
            $("#dyn_content_stoch").html('<img id="sma_loading" src="https://loading.io/spinners/recycle/lg.recycle-spinner.gif" />');
            $("#dyn_content_macd").html('<img id="sma_loading" src="https://loading.io/spinners/recycle/lg.recycle-spinner.gif" />');
            BuildHomePage($("#filters_coins").find(":selected").text())
        });

        $('#dyn_content').delegate('ul li a', 'click', function() {
            ListSelectorPlaceholder($(this));
        });
        // BuildSMA();

    });

</script>
</body>
</html>
