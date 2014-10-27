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
            url: address + "/request.php?request=form&" + form.serialize(),
            type: "GET",
            timeout: 3000,
            dataType: "json",
        }).always(function(data){
            button.attr('disabled', false);
        }).done(function(data){
            form[0].reset();
            process(data);
        }).fail(function(result){
            alert(connect_error + " submit");
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
        alert(connect_error + " render_from_url" + url);
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
    render_from_url(address + "/request.php?request=refresh&token=" + token);
}

function login(){
    render_from_url(address + "/request.php?request=loginform");
    form = $('form');
    form.off();
    form.submit(function(event){
        event.preventDefault();
        var form = $(this);
        var button = form.find('button');
        button.attr('disabled', true);
        $.ajax({
            url: address + "/request.php?request=login&" + form.serialize(),
            type: "GET",
            timeout: 3000,
            dataType: "json",
        }).always(function(data){
            button.attr('disabled', false);
        }).done(function(data){
            form[0].reset();
            if(data.meta.state !== "failure"){
                token = data.order.token;
                refresh();
            }else{
                alert(login_error);
            }
            process(data);
        }).fail(function(result){
            alert(connect_error + " login");
        });
    });
}
