$("title").append(title);
$("#header").append(header);
$("#footer").append(footer);

var token = get("token")
if(token){
    refresh();
}else{
    login();
}
