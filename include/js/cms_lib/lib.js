function preload(image)
{var d=document; if(!d.wb_pre) d.wb_pre=new Array();
var l=d.wb_pre.length; d.wb_pre[l]=new Image; d.wb_pre[l].src=image;
}

function over_on(n,ovr)
{var d=document,x; x=d[n];if (!(x) && d.all) x=d.all[n];
if (x){        document.wb_image=x; document.wb_normal=x.src; x.src=ovr; }}

function over_off()
{var x=document.wb_image; if (document.wb_normal) x.src=document.wb_normal;}


function ajaxResponse(responseText) {
    try {
        $response = $.parseJSON(responseText);
        if ($response.err) {
            switch ($response.err) {
                case 'user_login':
                    window.location.href = '/login.html';
                    break;
                case 'msg':
                    if ($response.div_id) {
                        $("#" + $response.div_id).validationEngine('showPrompt', $response.err_cont, 'err', 'topRight', true);
                        $(".parentFormundefined").click(function () {
                            $(this).remove();
                        });
                    } else
                        alert($response.err_cont);
                    break;
                case 'func':
                    if ($response.func) {
                        if ($response.param)
                            actions[$response.func]({param:$response.param});
                        else
                            actions[$response.func]();
                    }
                    break;
            }

            return false;
        }
        if ($response.ok) {
            switch ($response.ok) {
                case 'file':
                    $("#userAvatarTrueId").val($response.file);
                    $("#userEditFormIDAvatar img").attr('src', '/uploads/tmp/' + $response.file);
                    $("#userAvatarDelBtn").fadeTo('fast', 1);
                    break;
                case 'msg':
                    if ($response.div_id)
                        $("#" + $response.div_id).validationEngine('showPrompt', $response.ok_cont, 'pass', 'topRight', true);
                    else
                        alert($response.ok_cont);
                    break;
                case "msg_div":
                    $("#" + $response.div_id).fadeTo('fast', 0, function () {
                        $(this).html($response.ok_cont).fadeTo('fast', 1);
                        if ($response.div_id2 && $response.ok_cont2)
                            $("#" + $response.div_id2).html($response.ok_cont2);
                        if ($response.func)
                            actions[$response.func]();
                    });
                    break;
                case 'func':
                    if ($response.func) {
                        if ($response.param)
                            actions[$response.func]({param:$response.param});
                        else
                            actions[$response.func]();
                    }
                    break;
                case 'return_html':
                    if ($response.return_html)
                        return $response.return_html
                    break;
            }
            return true;
        }
    } catch (e) {
        alert("Возникла ошибка. Попробуйте ещё раз или обратитесь к администрации." + e.message);
    }
}

var actions = {

}