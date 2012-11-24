<?php
/**
 * sys_comments.php
 * script for all actions with control of users comments
 * @package System Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 02.04.2012
 * @copyright (c) 2005+ by SEOTM
 */
if (!defined("SITE_PATH"))
    define("SITE_PATH", $_SERVER['DOCUMENT_ROOT']);
include_once( SITE_PATH . '/admin/include/defines.inc.php' );
include_once( SITE_PATH . '/admin/modules/sys_comments/sys_comments.class.php' );

if (!defined("_LANG_ID"))
    $pg = new PageAdmin();

$module = AntiHacker::AntiHackRequest('module');

//Blocking to execute a script from outside (not Admin-part)
if ( !$pg->logon->isAccessToScript($module)) exit;

/*
//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part)
//============================================================================================
$Msg = new ShowMsg();
$goto = "http://" . NAME_SERVER . "/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if (!defined('BASEPATH')) {
    //$Msg->show_msg( '_NOT_AUTH' );
    //return false;
    ?><script>window.location.href="<?= $goto ?>";</script><?
;
}
$logon = check_init('logon', 'Authorization');
if (!$logon->LoginCheck()) {
    //return false;
    ?><script>window.location.href="<?= $goto ?>";</script><?
;
}
//=============================================================================================
// END
//=============================================================================================
//if (!defined("_LANG_ID")) define("_LANG_ID", DEBUG_LANG);
*/
$Obj = new CommentsCtrl($pg->logon->user_id, $module);

$Obj->task = AntiHacker::AntiHackRequest('task', 'show');
if(empty($Obj->task )) $Obj->task  = 'show';
$Obj->fln = AntiHacker::AntiHackRequest('fln', _LANG_ID);
$Obj->fltr = AntiHacker::AntiHackRequest('fltr');
$Obj->fltr2 = AntiHacker::AntiHackRequest('fltr2');
$Obj->srch = AntiHacker::AntiHackRequest('srch');
$Obj->srch2 = AntiHacker::AntiHackRequest('srch2');
$Obj->sort = AntiHacker::AntiHackRequest('sort');
$Obj->start = AntiHacker::AntiHackRequest('start', 0);
$Obj->display = AntiHacker::AntiHackRequest('display', 50);
$Obj->id = AntiHacker::AntiHackRequest('id');
$Obj->id_module = AntiHacker::AntiHackRequest('id_module');
$Obj->id_item = AntiHacker::AntiHackRequest('id_item');
$Obj->dt = AntiHacker::AntiHackRequest('dt');
$Obj->status = AntiHacker::AntiHackRequest('status');
$Obj->text = AntiHacker::AntiHackRequest('text');
$Obj->id_user = AntiHacker::AntiHackRequest('id_user');
$Obj->name = AntiHacker::AntiHackRequest('name');
$Obj->email = AntiHacker::AntiHackRequest('email');
$Obj->status = AntiHacker::AntiHackRequest('status');


$Obj->script_ajax = "module=$Obj->module&display=$Obj->display&start=$Obj->start&sort=$Obj->sort&fltr=$Obj->fltr&fltr2=$Obj->fltr2&srch=$Obj->srch&srch2=$Obj->srch2";
$Obj->script = "index.php?" . $Obj->script_ajax;
echo '<br>$Obj->task='.$Obj->task;
switch ($Obj->task) {
    case 'show':
        $Obj->show();
        break;
    case 'edit':
        $Obj->edit();
        break;
    case 'new':
        $Obj->edit();
        break;
    case 'ch_stat':
        $Obj->change_stat();
        break;
    case 'save':
        if ($Obj->CheckFields() != NULL) {
            $Obj->edit();
            return false;
        }
        if ($Obj->save()) {
            echo "<script>window.location.href='$Obj->script';</script>";
        }
        break;
    case 'delete':
        if (!isset($_REQUEST['id_del']))
            $id_del = NULL;
        else
            $id_del = $_REQUEST['id_del'];
        if (!empty($id_del)) {
            $del = $Obj->del($id_del);
            if (!$del)
                $pg->Msg->show_msg('_ERROR_DELETE');
        }
        else
            $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        echo "<script>window.location.href='$Obj->script';</script>";
        break;
    case 'cancel':
        echo "<script>window.location.href='$Obj->script';</script>";
        break;
    case 'savedt':
        $res = $Obj->changeDt();
        if ($res)
            echo $Obj->multi['_OK_SAVE'];
        else
            echo $Obj->multi['_NOT_SAVE'];
        break;
}
?>