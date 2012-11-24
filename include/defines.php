<?php
error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set("memory_limit","32M");
date_default_timezone_set('Europe/Kiev');

if (!defined("SITE_PATH"))          define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
if (!defined("NAME_SERVER"))        define( "NAME_SERVER", $_SERVER['SERVER_NAME'] );

if (!defined("SEOCMS_DEBUGNAME"))   define( "SEOCMS_DEBUGNAME", "SEOCMS_make_debug" );

if (!defined("MAKE_DEBUG")){
    if( isset($_REQUEST['make_debug']) ){
        define( "MAKE_DEBUG", intval($_REQUEST['make_debug']) );
    }
    elseif( isset($_COOKIE[SEOCMS_DEBUGNAME]) ){
        define( "MAKE_DEBUG", $_COOKIE[SEOCMS_DEBUGNAME] );
    }
    else define( "MAKE_DEBUG", "1" );
}

define('USE_CACHE', false);
if (!defined("CATALOG_TRASLIT"))         define( "CATALOG_TRASLIT", "0" );//Каталог без /catalog/

if (!defined("DEBUG_LANG"))         define( "DEBUG_LANG", "3" );
if (!defined("USE_TAGS"))           define( "USE_TAGS", "0" );
if (!defined("USE_COMMENTS"))       define( "USE_COMMENTS", "0" );
if (!defined("DEBUG_CURR"))         define( "DEBUG_CURR", "1" ); // debug currency = 1 (USD)
if (!defined("DISCOUNT"))           define( "DISCOUNT", "0.5" ); // default user discount
if (!defined("META_TITLE"))         define( "META_TITLE", "SEOCMS" );
if (!defined("META_DESCRIPTION"))   define( "META_DESCRIPTION", "" );
if (!defined("META_KEYWORDS"))      define( "META_KEYWORDS", "" );

include_once( SITE_PATH.'/admin/include/defines.inc.php' );

include_once( SITE_PATH.'/sys/classes/view.php' );
include_once( SITE_PATH.'/sys/classes/arr.php' );
include_once( SITE_PATH.'/sys/classes/date.php' );
include_once( SITE_PATH.'/sys/classes/utf8.php' );
include_once( SITE_PATH.'/sys/classes/url.php' );
include_once( SITE_PATH.'/sys/classes/html.php' );
include_once( SITE_PATH.'/sys/classes/form.php' );

include_once( SITE_PATH.'/include/classes/PageUser.class.php' );
include_once( SITE_PATH.'/include/classes/FrontForm.class.php' );
include_once( SITE_PATH.'/include/classes/FrontSpr.class.php' );
include_once( SITE_PATH.'/include/classes/UserAuthorize.class.php' );
include_once( SITE_PATH.'/include/classes/FrontTags.class.php' );
include_once( SITE_PATH.'/include/classes/FrontComments.class.php' );
include_once( SITE_PATH.'/sys/classes/mail/Mail.class.php' );

include_once( SITE_PATH.'/modules/mod_banner/banner.defines.php' );
include_once( SITE_PATH.'/modules/mod_pages/pages.defines.php' );
?>