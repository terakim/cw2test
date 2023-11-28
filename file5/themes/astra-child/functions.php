<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

add_filter('kboard_content_get_thumbnail_size','my_kboard_content_get_thumbnail_size', 10, 2);
function my_kboard_content_get_thumbnail_size($size, $content){
	$board = $content->getBoard();
	if($board->id == '6'){ // 실제 게시판 id로 적용해주세요.
		$size = array('width'=>'300', 'height'=>'450');
	}
	return $size;
}


add_filter('show_admin_bar', 'my_show_admin_bar');
function my_show_admin_bar() {
	if(current_user_can('activate_plugins')){
		return true;
	}
	return false;
}


