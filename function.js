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
    return false;
}

function render(url):
    //render to container from url
