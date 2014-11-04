function get(key){
    return $.cookie(key);
}

function set(key, value){
    $.cookie(key, value);
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
    container.empty();
    container.append(data);
}

function render_by_array(data){
    $.each(data, function(key){
        render("#" + key, this);
    });
}

function set_wait(wait){
    if(wait !== 0){
        sleep(wait).done(refresh);
    }
}

function process(data){
    render_by_array(data.html);
    if(data.order.alert){
        alert(data.order.alert);
    }
    set_wait(data.order.wait);
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
            url: address + "system/request.php?app_id=" + app_id + "&request=app/" + app_id + "/form?id=" + id + "&" + form.serialize() + serialize_settings(),
            type: "GET",
            timeout: 3000,
            dataType: "json",
        }).always(function(data){
            button.attr('disabled', false);
        }).done(function(data){
            form[0].reset();
            process(data);
        }).fail(function(result){
            alert(settings.connect_error.value + " submit");
        });
    });
}

function render_from_url(url){
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
    }).done(function(data){
        process(data);
    }).fail(function(data){
        alert(settings.connect_error.value);
    });
}

function sleep(ms){
    var d = new $.Deferred;
    setTimeout(function(){
        d.resolve(ms);
    }, ms);
    return d.promise();
};

function refresh(){
    render_from_url(address + "system/request.php?app_id=" + app_id + "&request=app/" + app_id + "/refresh&id=" + id + serialize_settings());
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
    form.append('<p>' + settings.login_message.value + '</p><input type="' + (settings.id_type.value ? 'number' : 'text') + '" name="id" required /><button type="submit">' + submit_text + '</button>');
    form.off();
    form.submit(function(event){
        event.preventDefault();
        var form = $(this);
        var id = form.find('input').val();
        if(id == "admin"){
            admin();
        }else if(id.length !== 0 && (get_number(id) !== false || !settings.id_type.value)){
            var button = form.find('button');
            button.attr('disabled', true);
            $.ajax({
                url: address + "system/request.php?app_id=" + app_id + "&request=system/login&" + form.serialize() + serialize_settings(),
                type: "GET",
                timeout: 3000,
                dataType: "json",
            }).always(function(data){
                button.attr('disabled', false);
            }).done(function(data){
                form[0].reset();
                if(data.meta.state !== "failure"){
                    id = data.order.id;
                    set('id', id);
                    refresh();
                    form.empty();
                }else{
                    alert(data.order.alert);
                }
            }).fail(function(result){
                alert(settings.connect_error.value);
            });
        }
    });
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
    $.each(settings, function(){
        
    });
}

function change_settings(){

}
