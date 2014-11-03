$("title").append(title);
$("#header").append(header);
$("#footer").append(footer);
address = location.href;
if(address.slice(-1) !== "/"){
    address = address + "/";
}

var settings = {};
var id = "";

$.ajax({
    url: address + "system/request.php?request=system/setting" + get_settings(),
    type: "GET",
    dataType: "json"
}).done(function(data){
    settings = data
    $("p").text(settings.login_message);
    id = get("id")
    if(id){
        refresh();
    }else{
        login();
    }
});

