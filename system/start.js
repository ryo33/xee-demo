$("title").append(title);
$("#header").append(header);
$("#footer").append(footer);
$("button").text(submit_text);
address = location.href;
if(address.slice(-1) !== "/"){
    address = address + "/";
}

var settings = {};

$.ajax({
    url: address + "system/request.php?request=system/setting&settings=" + get_settings(),
    type: "GET",
    dataType: "json"
}).done(function(data){
    settings = data
    $("p").text(settings.login_message);
    var token = get("token")
    if(token){
        refresh();
    }else{
        login();
    }
});

