<?php
/*
Plugin Name: KBoard 디스커버 FAQ 스킨
Plugin URI: https://www.cosmosfarm.com/wpstore/product/kboard-discover-faq-skin
Description: KBoard 디스커버 FAQ 스킨입니다.
Version: 1.4
Author: 코스모스팜 - Cosmosfarm
Author URI: https://www.cosmosfarm.com/
*/

if(!defined('ABSPATH')) exit;

add_filter('kboard_skin_list', 'kboard_skin_list_discover_faq', 10, 1);
function kboard_skin_list_discover_faq($list){
	
	$skin = new stdClass();
	$skin->dir = dirname(__FILE__);
	$skin->url = plugins_url('', __FILE__);
	$skin->name = basename($skin->dir);
	
	$list[$skin->name] = $skin;
	
	return $list;
}