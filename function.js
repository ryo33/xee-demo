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
    $('form').submit(function(event){
        event.preventDefault();
        var form = $(this);
        var button = form.find('button');
        button.attr('disabled', true);
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            timeout: 3000,
            dataType: "json",
        }).always(function(result){
            button.attr('disabled', false);
        }).done(function(data){
            form[0].reset();
            if($.trim(data.html) !== ""){//not empty
                render(data.html);
            }
        }).fail(function(result){
            alert(error_message);
        });
    });
}

function render_from_url(selector, url){
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
    }).done(function(data){
        render(selector, data.html);
    }).fail(function(data){
        alert(error_message);
    });
}
