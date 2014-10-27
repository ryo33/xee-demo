$("title").append(title);
$("#header").append(header);
$("#footer").append(footer);

render_from_url("#container", address + "/request.php?request=start");
form();

var token = get("token")
if(token !== null){
    refresh();
}else{
    login();
}
