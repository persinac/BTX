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
<html lang="en"  >
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head;
        any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Bittrex</title>

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
            <a class="navbar-brand" href="#">Bittrex Project</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li id="home" class="active"><a href="#">Home</a></li>
                <li id="testingPythonCall"><a href="#testingPythonCall">Testing Py Call</a></li>
                <li id="employeeList"><a href="#employeeList">Snapshots</a></li>
                <li id="actions"><a href="#actions">Actions</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container">

    <div class="starter-template">
        <div class="dyn_content" id="dyn_content">

        </div>

    </div>
</div><!-- /.container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/<?php echo BOOTSTRAP_VERSION; ?>/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="js/btx_main.js"></script>
<script>
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
        $("#home a").click(function() {
//            BuildBlogListTable();
            $("#employeeList").removeClass("active");
            $("#home").addClass("active");
            $("#actions").removeClass("active");
            $("#history").removeClass("active");
        });

        $("#testingPythonCall a").click(function() {
//            BuildEmployeeListTable();
            TestPythonCall();
            $("#testingPythonCall").addClass("active");
            $("#home").removeClass("active");
            $("#actions").removeClass("active");
            $("#history").removeClass("active");
        });

        $("#actions a").click(function() {
//            BuildActionsPage();
            $("#employeeList").removeClass("active");
            $("#home").removeClass("active");
            $("#actions").addClass("active");
            $("#history").removeClass("active");
        });

        $("#history a").click(function() {
//            DisplayHistory("dyn_content");
            $("#employeeList").removeClass("active");
            $("#home").removeClass("active");
            $("#actions").removeClass("active");
            $("#history").addClass("active");
        });

        $('#dyn_content').delegate('table#employee tr td a.details', 'click', function() {
            //LoadEmployeeDetails()
        });
        $('#dyn_content').delegate('#sendAdHocEmail', 'click', function() {
            //BuildSendAdHocEmailSection()
        });
        $('#dyn_content').delegate('#uplNewBlogMthSch', 'click', function() {
           // BuildUploadNewBlogMonthDataSection()
        });
        $('#dyn_content').delegate('#uplNewEmpList', 'click', function() {
            //BuildUploadNewEmployeeListSection()
        });
        $('#dyn_content').delegate('#setUpServiceAcct', 'click', function() {
            //BuildServiceAccountSetup()
        });

        $('#dyn_content').delegate('#displayCronJobList', 'click', function() {
            //DisplayCurrentCronJobList()
        });

        $('#dyn_content').delegate('ul li a', 'click', function() {
            //ListSelectorPlaceholder($(this));
        });

    });
    BuildHomePage()
</script>
</body>
</html>
