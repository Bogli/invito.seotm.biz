//var popup = {
//
//    popupObj:undefined,
//
//    show:function (html, elem) {
//        if (!cmspopup.popupObj)
//            cmspopup.popupObj = new cmspopup(html, elem);
//    },
//
//    close:function () {
//        if (!cmspopup.popupObj)
//            cmspopup.popupObj.close()
//    }
//};
//
//function cmspopup(html, elem) {
//    cmspopup.show_modal(html, elem);
//}

cmspopup = {
    coef_top:45,
    coef_left:115,
    obj_top:0,
    obg_left:0,
    html:'',
    popup_obj:undefined,

    show_modal:function (html, elem) {
        if (cmspopup.popup_obj)
            cmspopup.close();
        $(window).resize(cmspopup.popup_positioning);
        $coords = cmspopup.findPos(elem);


        cmspopup.obj_top = $coords[1];
        cmspopup.obg_left = $coords[0];

        $("body").append('' +
            '<div id="popupBodyId" class="popup-body" style="display: none;position: absolute;">' +
            html
            + '</div>');

        //устанавливаем абсолютный отступ
        cmspopup.popup_obj = $("#popupBodyId");
        cmspopup.popup_positioning();

        cmspopup.popup_obj.show(0).fadeTo('fast', 1);
    },

    popup_positioning:function () {
        //размеры попапа

        popup_width = cmspopup.popup_obj.width();
        popup_height = cmspopup.popup_obj.height();
        //ширина ока браузера
        $viewport_width = $(window).width();
        //ширина документа
        $document_width = $(document).width();
        $document_height = $(document).height();

//    alert('$viewport_width='+$viewport_width+'$document_width='+$document_width+'curtop='+$coords[1]+'curleft='+$coords[0]);
        $popup_half = popup_width / 2;
        $document_half = $document_width / 2;
        $left = $document_half - $popup_half - cmspopup.coef_left;

        if ($document_height > (cmspopup.obj_top + cmspopup.coef_top + popup_height)) {
            $top = cmspopup.obj_top + cmspopup.coef_top;
        } else {
            if (cmspopup.obj_top - popup_height - cmspopup.coef_top > 0)
                $top = cmspopup.obj_top - popup_height - cmspopup.coef_top;
            else
                $top = 0;
        }

        cmspopup.popup_obj.css('left', $left);
        cmspopup.popup_obj.css('top', $top);
    },

    show_loader:function () {

    },

    close:function () {
        cmspopup.popup_obj.remove();
    },

    findPos:function (obj) {
        if (obj) {
            var curleft = 0;
            var curtop = 0;
            if (obj.offsetParent) {
                curleft = obj.offsetLeft;
                curtop = obj.offsetTop;
                while (obj = obj.offsetParent) {
                    curleft += obj.offsetLeft;
                    curtop += obj.offsetTop;
                }
            }
            return [curleft, curtop];
        }
    }
}