var submit_text = "送信";

var fm_settings = [
    ["people", "ゲーム開始人数"],
    ["group", "グループの人数"],
    ["connect_error", "接続エラー時のメッセージ"],
    ["login_error", "ログインエラー時のメッセージ"],
    ["login_message", "ログインフォームに表示される説明"]
];

function get_settings(){
    var result = [];
    $.each(fm_settings, function(){
        result.push(this[0]);
    });
    $.each(app_settings, function(){
        result.push(this[0]);
    });
    return result.join(",");
}
