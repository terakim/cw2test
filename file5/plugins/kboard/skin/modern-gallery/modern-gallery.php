<?php
/*
Plugin Name: KBoard 모던 갤러리 스킨
Plugin URI: https://www.cosmosfarm.com/wpstore/product/kboard-modern-gallery-skin
Description: KBoard 모던 갤러리 스킨 입니다.
Version: 1.8
Author: 코스모스팜 - Cosmosfarm
Author URI: https://www.cosmosfarm.com/
*/

if(!defined('ABSPATH')) exit;

add_filter('kboard_skin_list', 'kboard_skin_list_modern_gallery', 10, 1);
function kboard_skin_list_modern_gallery($list){

	$skin = new stdClass();
	$skin->dir = dirname(__FILE__);
	$skin->url = plugins_url('', __FILE__);
	$skin->name = basename($skin->dir);

	$list[$skin->name] = $skin;

	return $list;
}
?>