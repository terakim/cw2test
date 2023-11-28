<?php
/*
=====================================================================================
                엠샵 업다운로드 / Copyright 2016 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.6 이상

   우커머스 버전 : WooCommerce 2.6 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 업다운로드 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
   2. 플러그인은 사용권을 구매하는 것이며, 프로그램 저작권에 대한 구매가 아닙니다.

   3. 플러그인을 구입하여 다수의 사이트에 복사하여 사용할 수 없으며, 1개의 라이센스는 1개의 사이트에만 사용할 수 있습니다. 
      이를 위반 시 지적 재산권에 대한 손해 배상 의무를 갖습니다.

   4. 플러그인은 구입 후 1년간 업데이트를 지원합니다.

   5. 플러그인은 워드프레스, 테마, 플러그인과의 호환성에 대한 책임이 없습니다.

   6. 플러그인 설치 후 버전에 관련한 운용 및 관리의 책임은 사이트 당사자에게 있습니다.

   7. 다운로드한 플러그인은 환불되지 않습니다.

=====================================================================================
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSSHelper' ) ) {

    require_once( ABSPATH . 'wp-admin/includes/user.php' );

    Class MSSHelper
    {
        private static $msh_editable_roles = array();
        public static function get_settings($setting, $postid = null)
        {
            $values = array();

            if (!empty($setting['id'])) {
                $value = $postid ? get_post_meta( $postid, $setting['id'], true) : get_option( $setting['id'], isset( $setting['default'] ) ? $setting['default'] : '' );
                if( empty( $value ) && isset( $setting['default'] ) ){
                    $value = $setting['default'];
                }

                $values[$setting['id']] = apply_filters( 'msshelper_get_' . $setting['id'], $value );
            }

            if ( !empty( $setting['elements'] ) && empty( $setting['repeater'] ) ){
                foreach( $setting['elements'] as $element ) {
                    $values = array_merge($values, self::get_settings( $element , $postid ));
                }
            }

            return $values;
        }

        public static function update_settings($setting, $postid = null){
            if( !empty( $setting['id'] ) ){
                if( has_action( 'update_' . $setting['id'] ) ){
                    do_action( 'update_' . $setting['id'] );
                }else{
                    if( !empty( $_REQUEST[ $setting['id'] ] ) ){
                        $postid ? update_post_meta( $postid, $setting['id'], $_REQUEST[ $setting['id'] ] ) : update_option( $setting['id'], $_REQUEST[ $setting['id'] ] );
                    }else{
                        $postid ? delete_post_meta( $postid, $setting['id'] ) : delete_option( $setting['id'] );
                    }
                }
            }

            if( !empty( $setting['elements'] ) && empty( $setting['repeater'] ) ){
                foreach( $setting['elements'] as $element ){
                    self::update_settings( $element, $postid );
                }
            }
        }
        public static function get_editable_roles($filter_name = null)
        {
            if (empty(self::$msh_editable_roles['default'])) {
                self::$msh_editable_roles['default'] = get_editable_roles();
                self::$msh_editable_roles['default']['guest'] = array(
                    'name' => __( '비회원', 'mshop-ownership-verification' )
                );
            }

            if (empty($filter_name)) {
                return self::$msh_editable_roles['default'];
            } else if (!empty(self::$msh_editable_roles[$filter_name])) {
                return self::$msh_editable_roles[$filter_name];
            } else {
                $filters = array_filter(get_option($filter_name, array()), function ($item) {
                    return 'yes' === $item['enabled'];
                });
                $keys = array_flip(array_map(function ($role) {
                    return $role['role'];
                }, $filters));
                self::$msh_editable_roles[$filter_name] = array_intersect_key(self::$msh_editable_roles['default'], $keys);

                return self::$msh_editable_roles[$filter_name];
            }
        }
        public static function get_role_based_rules($option_name, $template, $options = array(), $filter_name = null, $postid = null)
        {
            $editable_roles = self::get_editable_roles($filter_name);
            $editable_roles_key = array_keys($editable_roles);
            $rules = !empty( $option_name ) ? ( $postid ? get_post_meta( $postid, $option_name, true) : get_option( $option_name, array() ) ) : $options;
            if( !is_array( $rules) ){
                $rules = array();
            }
            $rules = array_filter($rules, function ($rule) use ($editable_roles_key) {
                return in_array($rule['role'], $editable_roles_key);
            });
            $rules_key = array_map(function ($rule) {
                return $rule['role'];
            }, $rules);
            $new_roles = array_diff($editable_roles_key, $rules_key);

            foreach ($editable_roles as $key => $value) {
                if (in_array($key, $new_roles)) {
                    $rules[] = array_merge(
                        array(
                            'role' => $key,
                            'name' => $value['name']
                        ),
                        !empty($template) ? $template : array()
                    );
                }
            }

            return $rules;
        }
    }
}
?>