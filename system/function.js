function get(key){
    return $.cookie(key);
}

function set(key, value){
    $.cookie(key, value);
}

function remove(key){
    $.removeCookie(key)
}

function clear(){
    localStorage.clear();
}

function is_set(value){
    if(typeof value == 'object'){
        return true;
    }
    return false;
}

function render(selector, data){
    var container = $(selector);
    if(container.html() != data){
        container.empty();
        container.append(data);
    }
}

function render_by_array(data){
    $.each(data, function(key){
        render("#" + key, this);
    });
}

function set_wait(wait, loop){
    if(wait !== 0){
        sleep(wait).done(function(){
            refresh(loop)
        });
    }
}

function render_logout(){
    if(!$("#footer").children("button").get(0)){
        $("#footer").append("<button id=\"logout\">" + settings.logout.value + "</button>");
        $("#logout").click(function(){
            redirect();
        });
    }
}

function redirect(){
    $(location).attr("href", "/" + app_id);
}

function process(data, loop){
    render_logout();
    if(data.order.state && data.order.state >= 2){
        nextpage = "result";
    }
    if(data.html){
        render_by_array(data.html);
    }
    if(data.order.alert){
        alert(data.order.alert);
    }
    if(data.order.next){
        nextpage = data.order.next;
    }
    if(get_number(data.order.wait) != 0){
        set_wait(data.order.wait, loop);
    }
    submit();
}

function submit(){
    form = $('form');
    form.off();
    form.submit(function(event){
        event.preventDefault();
        var form = $(this);
        var button = form.find('button');
        button.attr('disabled', true);
        $.ajax({
            url: address + "system/request.php?app_id=" + app_id + "&request=app/" + app_id + "/form&id=" + id + "&" + form.serialize() + serialize_settings(),
            type: "GET", cache: false,
            timeout: settings.timeout.value,
            dataType: "json"
        }).always(function(data){
            button.attr('disabled', false);
        }).done(function(data){
            form[0].reset();
            nextpage = 'refresh';
            if(data.order.next){
                nextpage = data.order.next;
            }
            refresh(++current_loop);
        }).fail(connect_error);
    });
}

function render_from_url(url, loop){
    $.ajax({
        type: "GET", cache: false,
        url: url,
        dataType: "json" }).done(function(data){
        process(data, loop);
    }).fail(connect_error);
}

function sleep(ms){
    var d = new $.Deferred;
    setTimeout(function(){
        d.resolve(ms);
    }, ms);
    return d.promise();
};

function refresh(loop){
    if(loop == current_loop){
        render_from_url(address + "system/request.php?app_id=" + app_id + "&request=app/" + app_id + "/" + nextpage + "&id=" + id + serialize_settings(), loop);
    }
}

function get_number(text){
    if(text && /^\d*$/.test(text)){
        return parseInt(text);
    }else{
        return false;
    }
}

function login(){
    form = $('form');
    form.empty();
    form.append('<p>' + settings.login_message.value + '</p><input type="' + (settings.id_type.value ? 'number' : 'text') + '" name="id" required /><button type="submit">' + settings.submit.value + '</button>');
    form.off();
    form.submit(function(event){
        event.preventDefault();
        var form = $(this);
        id = check_data(form.find('input').val());
        if(id === settings.admin.value){
            admin();
        }else if(id.length !== 0 && (get_number(id) !== false || !settings.id_type.value)){
            var button = form.find('button');
            button.attr('disabled', true);
            $.ajax({
                url: address + "system/request.php?app_id=" + app_id + "&request=system/login&" + form.serialize() + serialize_settings(),
                type: "GET", cache: false,
                timeout: settings.timeout.value,
                dataType: "json"
            }).always(function(data){
                button.attr('disabled', false);
            }).done(function(data){
                form[0].reset();
                if(data.meta.state !== "failure"){
                    id = data.order.id;
                    form.empty();
                    check_game();
                }else{
                    alert(data.order.alert);
                }
            }).fail(connect_error);
        }
    });
}

function check_game(){
    render_logout();
    $.ajax({
        url: address + "system/request.php?app_id=" + app_id + "&id=" + id + "&request=system/admin&action=ask" + serialize_settings(),
        type: "GET", cache: false,
        timeout: settings.timeout.value,
        dataType: "json" }).done(function(data){
        if(check_data(data.order.state) >= 2){
            nextpage = "result";
            refresh(++current_loop);
        }else if(check_data(data.order.state) !== 1){
            $("#main").text(settings.waiting.value);
            sleep(5000).done(check_game);
        }else if(!data.order.isjoin){
            $("#main").text(settings.wait_group.value);
            sleep(5000).done(check_game);
        }else{
            refresh(++current_loop);
        }
    }).fail(connect_error);
}

function serialize(array){
    result = "";
    for(key in array){
        result += "&" + key + "=" + array[key];
    }
    return result
}

function serialize_settings(){
    result = "";
    for(key in settings){
        result += "&" + key + "=" + settings[key].value;
    }
    return result
}

function admin(){
    var form = $("#form");
    form.empty();
    form.append("<p>Enter Password</p><input type=\"text\" /><button type=\"submit\">" + settings.submit.value + "</button>");
    form.off();
    form.submit(function(event){
        event.preventDefault();
        form = $(this);
        if(check_data(form.find("input").val()) === settings.admin_password.value){
            form.find("button").attr('disabled', true);
            form.empty();
            form.off();
            $.each(settings, function(key, setting){
                form.append("<p><label><input type=\"" + (setting.type ? "number" : "text") + "\" id=\"" + key + "\" name=\"" + key + "\" value=\"" + setting.value + "\" />" + setting.desc + "</label></p>");
            });
            form.append("<button type=\"submit\">" + settings.submit.value + "</button>");
            form.submit(function(event){
                event.preventDefault();
                var form = $(this);
                var new_settings = form.serialize().split("&");
                var result = "&settings=";
                for(i in new_settings){
                    var setting = new_settings[i].split("=");
                    new_settings[i] = setting[0] + ":" + String(setting[1]);
                    settings[setting[0]].value = setting[1];
                }
                $.ajax({
                    url: address + "system/request.php?app_id=" + app_id + "&request=system/admin&action=change" + serialize_settings() + "&settings=" + new_settings.join(","),
                    type: "GET", cache: false,
                    timeout: settings.timeout.value,
                    dataType: "json" }).done(function(data){
                    alert("Changed Settings");
                }).fail(connect_error);
            });
            admin_refresh();
        }else{
            login();
        }
    });
}

function admin_refresh(){
    render_logout();
    $.ajax({
        url: address + "system/request.php?app_id=" + app_id + "&request=system/admin&action=getstate" + serialize_settings(),
        type: "GET", cache: false,
        timeout: settings.timeout.value,
        dataType: "json" }).done(function(data){
        var main = $("#main");
        main.empty();
        main.off();
        main.append("<h3>Admin Page</h3><div id=\"adminpage\"></div");
        if("html" in data){
            render_by_array(data.html);
        }
        if(check_data(data.order.state) === 0 || check_data(data.order.state) === 1){
            main.append("<button id=\"end\">Terminate The Game</button>");
            main.children("button").click(function(){
                $.ajax({
                    url: address + "system/request.php?app_id=" + app_id + "&request=system/admin&action=end" + serialize_settings(),
                    type: "GET", cache: false,
                    timeout: settings.timeout.value,
                    dataType: "json" }).done(function(data){
                    alert("Terminated The Game");
                }).fail(connect_error);
            });
        }else{
            main.append("<button id=\"end\">Start A Game</button>");
            main.children("button").click(function(){
                $.ajax({
                    url: address + "system/request.php?app_id=" + app_id + "&request=system/admin&action=start" + serialize_settings(),
                    type: "GET", cache: false,
                    timeout: settings.timeout.value,
                    dataType: "json" }).done(function(data){
                    alert("Started A Game");
                }).fail(connect_error);
            });
        }
        sleep(1000).done(admin_refresh);
    }).fail(connect_error);
}

function check_data(data){
    if(/^[0-9０-９]+$/.test(data)){
        return Number(data);
    }
    return data;
}

function connect_error(){
    alert(settings.connect_error.value);
}
