<?php

/**
 * banners.defines.php
 * All Definitions for module of banners
 * @package Banners Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 22.12.2010
 * @copyright (c) 2010+ by SEOTM
 */
include_once( SITE_PATH . '/include/defines.php' );
include_once( SITE_PATH . '/modules/mod_banner/banner.class.php' );

define("MOD_BANNER", true);

define("TblModBanner", "mod_banner");
define("TblModBannerSprTxt", "mod_banner_spr_txt");
define("TblModBannerSprTypes", "mod_banner_places");
define("Banners_Img_Path_Small", "/images/mod_banners/");
?>