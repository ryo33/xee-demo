var address = $(location).attr("href");
address = address.replace(new RegExp(app_id + "\/?$"), "");

var settings = {};
var small_settings = {};
var id = "";
var nextpage = "refresh";
var current_loop = 0;

$.ajax({
    url: address + "system/request.php?app_id=" + app_id + "&request=system/setting",
    type: "GET",
    dataType: "json"
}).done(function(data){
    $("title").append(title);
    $("#header").append(header);
    $("#footer").append(footer);
    settings = data;
    login();
    $("form").children("p").text(settings.login_message.value);
});
