<?
/**
* banner.backend.php  
* script for all actions with banners
* @package Banners Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once(SITE_PATH.'/modules/mod_banner/banner.defines.php' );

if(!defined("_LANG_ID")) {session_start(); $pg = new PageAdmin();}

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset( $_REQUEST['task'] ) ) $task = 'show';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['display'] ) ) $display = 50;
else $display = $_REQUEST['display'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if(!isset($_REQUEST['asc_desc'])) $asc_desc='asc';
else $asc_desc=$_REQUEST['asc_desc']; 

if( !isset( $_REQUEST['fltr'] ) ) $fltr=NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['fltr2'] ) ) $fltr2=NULL;
else $fltr2 = $_REQUEST['fltr2'];


if( !isset( $_REQUEST['id'] ) ) $id = NULL;
else $id = $_REQUEST['id'];

if( !isset( $_REQUEST['type'] ) ) $type = NULL;
else $type = $_REQUEST['type'];

if( !isset( $_REQUEST['path'] ) ) $path = NULL;
else $path = $_REQUEST['path'];

if( !isset( $_REQUEST['img_bg'] ) ) $img_bg = NULL;
else $img_bg = $_REQUEST['img_bg'];


if( !isset( $_REQUEST['visible'] ) ) $visible = NULL;
else $visible = $_REQUEST['visible'];

if( !isset( $_REQUEST['href'] ) ) $href = NULL;
else $href = $_REQUEST['href'];

if( !isset( $_REQUEST['size'] ) ) $size = NULL;
else $size = $_REQUEST['size'];

if( !isset( $_REQUEST['set'] ) ) $set = NULL;
else $set = $_REQUEST['set'];

if( !isset( $_REQUEST['content'] ) ) $content = NULL;
else $content = $_REQUEST['content'];

if( !isset( $_REQUEST['sdt'] ) ) $sdt = strftime('%Y-%m-%d %H:%M', strtotime('now'));
else $sdt = $_REQUEST['sdt'];

if( !isset( $_REQUEST['edt'] ) ) $edt = strftime('%Y-%m-%d %H:%M', strtotime('+30 days'));
else $edt = $_REQUEST['edt'];

if( !isset( $_REQUEST['cnt_show'] ) ) $cnt_show = NULL;
else $cnt_show = $_REQUEST['cnt_show'];

if( !isset( $_REQUEST['limit_show'] ) ) $limit_show = NULL;
else $limit_show = $_REQUEST['limit_show'];

if( !isset( $_REQUEST['move'] ) ) $move = NULL;
else $move = $_REQUEST['move']; 

if( !isset( $_REQUEST['replace_to'] ) ) $replace_to = NULL;
else $replace_to = $_REQUEST['replace_to']; 

 $Banner = new Banner($pg->logon->user_id, $module);
 $Banner->task = $task;
 $Banner->display = $display;
 $Banner->start = $start;
 $Banner->sort = $sort;
 $Banner->fltr = $fltr;
 $Banner->fltr2 = $fltr2;
 $Banner->asc_desc = $asc_desc; 
 
 $Banner->id = $id;
 $Banner->type = $type;
 $Banner->path = $path;
 $Banner->img_bg = $img_bg;
 $Banner->visible = $visible;
 $Banner->href = $href;
 $Banner->size = $size;
 $Banner->set = $set;
 $Banner->content = $content;
 $Banner->sdt = $sdt;
 $Banner->edt = $edt;
 $Banner->cnt_show = $cnt_show;
 $Banner->limit_show = $limit_show;
 $Banner->move = $move; 

 if( !isset( $_REQUEST['fln'] ) ) $Banner->fln = _LANG_ID;
 else $Banner->fln = $_REQUEST['fln'];

 $Banner->script_ajax = 'module='.$Banner->module.'&display='.$Banner->display.'&start='.$Banner->start.'&sort='.$Banner->sort.'&fltr='.$Banner->fltr;
 $Banner->script = "/admin/index.php?".$Banner->script_ajax;


switch( $task ) {

    case 'show':      
        $Banner->show(); 
        break;

    case 'new':       
        $Banner->edit( NULL, NULL ); 
        break;

    case 'edit':      
        $Banner->edit( $id, NULL ); 
        break;

    case 'save':
        if( !isset($_FILES['filename']) ) $filename = NULL;
        else $filename = $_FILES['filename'];    
        if ( $filename!=null and $_FILES["filename"]["error"]==0 ) {
            $Banner->path = $_FILES["filename"]["name"];
            $uploaddir = $_SERVER['DOCUMENT_ROOT'].'/images/mod_banners/';
            if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
            else @chmod($uploaddir,0777);
            $uploaddir = $uploaddir.$Banner->path; 
            if ( !copy( $_FILES["filename"]["tmp_name"],$uploaddir) ) {
                echo "Ошибка копирования файла<br>";
                $Banner->edit( $id );
                @chmod($uploaddir,0755);
                return false;
            }
            @chmod($uploaddir,0755);
        }  

        if( !isset($_FILES['file_img_bg']) ) $file_img_bg = NULL;
        else $file_img_bg = $_FILES['file_img_bg'];    
        if ( $file_img_bg!=null and $_FILES["file_img_bg"]["error"]==0 ) {
            $Banner->img_bg = $_FILES["file_img_bg"]["name"];
            $uploaddir = $_SERVER['DOCUMENT_ROOT'].'/images/mod_banners/';
            if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
            else @chmod($uploaddir,0777);
            $uploaddir = $uploaddir.$Banner->img_bg; 
            if ( !copy( $_FILES["file_img_bg"]["tmp_name"],$uploaddir) ) {
                echo "Ошибка копирования файла<br>";
                $Banner->edit( $id );
                @chmod($uploaddir,0755);
                return false;
            }
            @chmod($uploaddir,0755);
        } 
        
        if($Banner->set=='0') {
            $Banner->path =  $Banner->content;
        }
        $res = $Banner->save( $id );
        //if($res)  echo "<script>window.location.href='$Banner->script';</script>";
        $Banner->show();
       	break;

    case 'delete':
        if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
  		else $id_del = $_REQUEST['id_del'];
        $del = $Banner->del( $id_del );
        //if ( $del > 0 ) echo "<script>window.alert('Deleted OK! ($del records)');</script>";
        //else $pg->Msg->show_msg('_ERROR_DELETE');
        if( $del==0) $pg->Msg->show_msg('_ERROR_DELETE');
        echo "<script>window.location.href='$Banner->script';</script>";
        break;

    case 'cancel':  
        echo "<script>window.location.href='$Banner->script';</script>";
        break;
        
    case 'up':
        //phpinfo();
        //echo '<br>$Catalog->id_cat='.$Catalog->id_cat;
        $Banner->up(TblModBanner, 'type', $Banner->type);
        $Banner->ShowContentHTML();
        //echo "<script>window.location.href='$Catalog->script';</script>";
        break;
        
    case 'down':
        $Banner->down(TblModBanner, 'type', $Banner->type);
        $Banner->ShowContentHTML();
        //echo "<script>window.location.href='$Catalog->script';</script>";
        break;
        
    case 'replace':
        $Banner->Form->Replace(TblModBanner, 'move', $Banner->id, $replace_to);
        $Banner->ShowContentHTML();
        break;

}

?>
