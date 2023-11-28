<?php

if (!class_exists('Restrict_Elementor_Addon_Logged_In_User')) {

  class Restrict_Elementor_Addon_Logged_In_User {
    function __construct() {
      add_filter('restrict_for_elementor_show_to_main_options', array($this, 'show_to_main_options'));
      add_filter('restrict_for_elementor_should_render_logged_in_users', array($this, 'should_render'), 10, 2);
    }

    function show_to_main_options($array){
      $array['logged_in_users'] = __('Logged in users', 'restrict-for-elementor');
      return $array;
    }

    function should_render($should_render, $settings){
      if ( !empty( $settings['restrict_for_elementor_show_to'] ) ) {

        $action = (!isset($settings['restrict_for_elementor_action']) || (isset($settings['restrict_for_elementor_action']) && $settings['restrict_for_elementor_action'] !== 'yes')) ? 'hide' : 'show';

        if($settings['restrict_for_elementor_show_to'] == 'logged_in_users'){
          if(is_user_logged_in()){
            $should_render = ($action == 'show') ? true : false;
          }else{
            $should_render = ($action == 'show') ? false : true;
          }
        }
      }
      return $should_render;
    }

  }
  new Restrict_Elementor_Addon_Logged_In_User();
}

?>
