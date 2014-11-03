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
            url: address + "system/request.php?request=app/form?id=" + id + "&" + form.serialize(),
            type: "GET",
            timeout: 3000,
            dataType: "json",
        }).always(function(data){
            button.attr('disabled', false);
        }).done(function(data){
            form[0].reset();
            process(data);
        }).fail(function(result){
            alert(settings.connect_error + " submit");
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
        alert(settings.connect_error + " render_from_url" + url);
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
    render_from_url(address + "system/request.php?request=app/refresh&id=" + id);
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
    form.append('<p>' + settings.login_message + '</p><input type="' + (settings.id_type ? 'number' : 'text') + '" name="id" required /><button type="submit">' + submit_text + '</button>');
    form.off();
    form.submit(function(event){
        event.preventDefault();
        var form = $(this);
        var id = form.find('input').val();
        if(id == "admin"){
            admin();
        }else if(id.length !== 0 && (get_number(id) !== false || !settings.id_type)){
            var button = form.find('button');
            button.attr('disabled', true);
            $.ajax({
                url: address + "system/request.php?request=system/login?id=" + id + "&" + form.serialize(),
                type: "GET",
                timeout: 3000,
                dataType: "json",
            }).always(function(data){
                button.attr('disabled', false);
            }).done(function(data){
                form[0].reset();
                if(data.meta.state !== "failure"){
                    id = data.order.id;
                    refresh();
                }else{
                    alert(data.order.alert);
                }
                form.empty();
                process(data);
            }).fail(function(result){
                alert(settings.connect_error);
            });
        }
    });
}

function admin(){

}
