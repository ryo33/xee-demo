$("title").append(title);
$("#header").append(header);
$("#footer").append(footer);
$("button").text(submit_text);

//TODO access setting.php and get settings

var token = get("token")
if(token){
    refresh();
}else{
    login();
}
