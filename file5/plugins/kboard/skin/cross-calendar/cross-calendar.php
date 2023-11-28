<?php
/*
Plugin Name: KBoard 크로스 캘린더 스킨
Plugin URI: https://www.cosmosfarm.com/wpstore/product/kboard-cross-calendar-skin
Description: KBoard 크로스 캘린더 스킨입니다.
Version: 2.5
Author: 코스모스팜 - Cosmosfarm
Author URI: https://www.cosmosfarm.com/
*/

if(!defined('ABSPATH')) exit;

add_filter('kboard_skin_list', 'kboard_skin_list_cross_calendar', 10, 1);
function kboard_skin_list_cross_calendar($list){
	
	$skin = new stdClass();
	$skin->dir = dirname(__FILE__);
	$skin->url = plugins_url('', __FILE__);
	$skin->name = basename($skin->dir);
	
	$list[$skin->name] = $skin;
	
	return $list;
}