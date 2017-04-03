rankings = 0;
$(document).ready(function() {
    
    $('#devproLogin')
        .bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                
                devproUsername: {
                    validators: {
                        notEmpty: {
                            message: 'The Username is required and can\'t be empty'
                        },
                    }
                },
        
                devproPassword: {
                    validators: {
                        notEmpty: {
                            message: 'The password is required and can\'t be empty'
                        }
                    }
                }
            }
        })
        .on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();
            
            
            
            //get the action-url of the form
            var actionurl = "http://158.69.116.140/web-devpro/Engine/Api/getjson.php";

            //do your own request an handle the results
             $.ajax({
                    url: actionurl,
                    type: 'post',
                    dataType: 'json',
                    data: $("#devproLogin").serialize(),
                    success: function(data) {
                               console.log(data);
                               
                               //
                               if(data.login == "failed, accept checkbox!")
                               {
                                   $("#wronglogin").text("accept checkbox first!");
                               }
                               //
                               if(data.login == "failed")
                               {
                                   $("#wronglogin").text("Wrong Username/Password combination, login try: " + data.loginTry);
                               }
                               if(data.responseLogin == "wrongcontent")
                               {
                                   $("#wronglogin").text("illegal input detected!, login try: " + data.loginTry);
                               }
                               if(data.login == "ok")
                               {
                                    // close modal
                                    $("#loginModal").hide();
                                    // replace login Button with Username and logout button, and show Dashboard link in Nav
                                    $("#devproLoginBtn").replaceWith("<button id=\"devproLoginBtn\" type=\"button\" onclick=\"setLogout()\" class=\"btn btn-warning\">Logout</button>");
                                    // Show Username
                                    $("#navbar").append("<p class=\"navbar-text navbar-right\">" + data.username + "</p>");
                                    // show Dashboar Link
                                    $("#devproDashboard").replaceWith("<li id=\"devproDashboard\"><a href=\"http://158.69.116.140/web-devpro/index.php?site=Dashboard\">Dashboard</a></li>");
                                }
                               // todo: wenn login = ok schliese Modal und zeige
                               // Namen, logout Button und Dashboard an.
                               // failed = zeige failed in Modal an und lade Bild
                               // Zahlen generator
                               
                             }
                });

        });
});

function getUsername()
{
    
}

function setLogout()
{
     console.log("go logout");
     var actionurl = "http://158.69.116.140/web-devpro/Engine/Api/getjson.php";
     $.ajax({
                    url: actionurl,
                    type: 'post',
                    dataType: 'json',
                    data: { devproLogout: "logout" },
                    success: function(data) {
                               console.log(data);
                               if(data.logoutresponse == true)
                               {
                                    //$("#devproLoginBtn").replaceWith("<button id=\"devproLoginBtn\" type=\"button\" data-toggle=\"modal\" data-target=\"#loginModal\" class=\"btn btn-warning\">Sign In</button>");
                                    //$("#devproDashboard").replaceWith("<li id=\"devproDashboard\" role=\"presentation\" class=\"disabled\"><a href=\"#\">Dashboard</a></li>");
                                    //$(".navbar-text").remove();
                                    window.location ="http://158.69.116.140/web-devpro/";
                                }                            
                             }
                });
}

function getSessionStatus()
{
    var actionurl = "http://158.69.116.140/web-devpro/Engine/Api/getjson.php";
     $.ajax({
                    url: actionurl,
                    type: 'post',
                    dataType: 'json',
                    data: { getSessionStatus: "getdata" },
                    success: function(data) {
                               console.log(data);
                               if(data.getSessionStatus == true)
                               {
                                   $("#navbar").append("<p class=\"navbar-text navbar-right\">" + data.username + "</p>");
                                   $("#navbar").append("<p id=\"devpointsP\" class=\"navbar-text navbar-right\">Devpoints: " + tdevpoints + "</p>");
                                   $("#devproLoginNavbar").append("<button id=\"devproLoginBtn\" type=\"button\" onclick=\"setLogout()\" class=\"btn btn-warning\">Logout</button>");
                                   $("#devproDashboard").replaceWith("<li id=\"devproDashboard\"><a href=\"http://158.69.116.140/web-devpro/index.php?site=Dashboard\">Dashboard</a></li>");
                                }
                               else
                               {
                                   $("#devproLoginNavbar").append("<button id=\"devproLoginBtn\" type=\"button\" data-toggle=\"modal\" data-target=\"#loginModal\" class=\"btn btn-warning\">Sign In</button>");
                               }
                             }
                });
}

/*
 * show Single Rankings
 */
$(document).ready(function(){
   
    $("#devproShowSingleRankings").submit(function(e) {
        console.log("POST show Single Rankings");
        //prevent Default functionality
        e.preventDefault();
        
         var url = "http://158.69.116.140/web-devpro/Engine/Api/getjson.php";
         var data = $("#devproShowSingleRankings").serialize();

         $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: data,
                success: function(data) {
                        console.log(data);
                            var counter = data.showSingleRankings.length;
                            var index;
                            $("#singleRankingTable").replaceWith("<table id=\"singleRankingTable\" class=\"table\"><tbody><tr><th>Pos</th><th>Player</th><th>SingleElo</th></tr></tbody></table>");
                            
                            for(index = 0; index < counter; ++index)
                            {
                                console.log(data.showSingleRankings[index]);
                                $('#singleRankingTable > tbody').append("<tr><td>" + (index + 1) + "</td><td>" + data.showSingleRankings[index].Username + "</td><td>" + data.showSingleRankings[index].SingleElo + "</td><tr>");
                            }
                        }
            });

    });
});  




/*
 * show Match Rankings
 */
$(document).ready(function(){
   
    $("#devproShowMatchRankings").submit(function(e) {
        console.log("POST show Match Rankings");
        //prevent Default functionality
        e.preventDefault();
        
         var url = "http://158.69.116.140/web-devpro/Engine/Api/getjson.php";
         var data = $("#devproShowMatchRankings").serialize();

         $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: data,
                success: function(data) {
                        console.log(data);
                            var counter = data.showMatchRankings.length;
                            var index;
                            $("#singleRankingTable").replaceWith("<table id=\"singleRankingTable\" class=\"table\"><tbody><tr><th>Pos</th><th>Player</th><th>SingleElo</th></tr></tbody></table>");
                            
                            for(index = 0; index < counter; ++index)
                            {
                                console.log(data.showMatchRankings[index]);
                                $('#singleRankingTable > tbody').append("<tr><td>" + (index + 1) + "</td><td>" + data.showMatchRankings[index].Username + "</td><td>" + data.showMatchRankings[index].Elo + "</td><tr>");
                            }
                        }
            });

    });
});   



function getDevpoints()
{
    console.log("send getDevpoints");

    var url = "http://158.69.116.140/web-devpro/Engine/Api/getjson.php";
    $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: 'getdevpoints=getdevpoints',
                success: function(data) {
                        console.log(data);
                        tdevpoints = data.devpoints;
                        $("#devpointsP").replaceWith("<p id=\"devpointsP\" class=\"navbar-text navbar-right\">Devpoints: " + data.devpoints + "</p>");
                        }
            });
}


getDevpoints();