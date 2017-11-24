function BuildHomePage() {
    var response = "<h3>Starting...</h3>";
    $("#dyn_content").html(response);
    $.ajax({
        type: "GET",
        url: "/CRUD/general/bittrexAccountInfo.php",
        success: function(response) {
            //console.log(response);
            $("#dyn_content").html(response);
        }
    });
}

function TestPythonCall() {
    var response = "<h3>Starting...</h3>";
    $("#dyn_content").html(response);
    $.ajax({
        type: "GET",
        url: "/py_bittrex/testing2.py",
        success: function(response) {
            console.log(response);
            $("#dyn_content").html(response);
        }
    });
}