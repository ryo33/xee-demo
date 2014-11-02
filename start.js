$("title").append(title);
$("#header").append(header);
$("#footer").append(footer);

//TODO access setting.php and get settings

var token = get("token")
if(token){
    refresh();
}else{
    login();
}
