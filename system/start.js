$("title").append(title);
$("#header").append(header);
$("#footer").append(footer);
address = location.href;
if(address.slice(-1) !== "/"){
    address = address + "/";
}
$.ajax({
    url: address + "system/request.php?request=system/setting",
    type: "GET",
    dataType: "json"
}).done(function(data){
    $.each(data, function(key){
        eval(key + " = " + this + ";");
    });
    var token = get("token")
    if(token){
        refresh();
    }else{
        login();
    }
});

