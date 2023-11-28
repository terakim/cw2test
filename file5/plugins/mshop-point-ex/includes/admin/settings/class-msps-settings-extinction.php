<?php

/*
=====================================================================================
                엠샵 프리미엄 포인트 / Copyright 2014-2015 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.3.1 이상

   우커머스 버전 : WooCommerce 3.0 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 프리미엄 포인트 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
   2. 플러그인은 사용권을 구매하는 것이며, 프로그램 저작권에 대한 구매가 아닙니다.

   3. 플러그인을 구입하여 다수의 사이트에 복사하여 사용할 수 없으며, 1개의 라이센스는 1개의 사이트에만 사용할 수 있습니다. 
      이를 위반 시 지적 재산권에 대한 손해 배상 의무를 갖습니다.

   4. 플러그인은 구입 후 1년간 업데이트를 지원합니다.

   5. 플러그인은 워드프레스, 테마, 플러그인과의 호환성에 대한 책임이 없습니다.

   6. 플러그인 설치 후 버전에 관련한 운용 및 관리의 책임은 사이트 당사자에게 있습니다.

   7. 다운로드한 플러그인은 환불되지 않습니다.

=====================================================================================
*/


if( ! defined('ABSPATH')) {
    exit;
}

if( ! class_exists('MSPS_Settings_Extinction')) {

    class MSPS_Settings_Extinction {

        static function get_wallets() {
            $wallets = array();

            $point_wallets = new MSPS_Point_Wallet(get_current_user_id());

            $wallet_items = $point_wallets->load_wallet_items();

            foreach($wallet_items as $wallet_item) {
                $wallets[$wallet_item->get_id()] = $wallet_item->label;
            }

            return $wallets;
        }

        static function update_settings() {
            include_once MSPS()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
            $_REQUEST = array_merge($_REQUEST, json_decode(stripslashes($_REQUEST['values']), true));

            MSSHelper::update_settings(self::get_setting_fields());

            if('yes' == $_REQUEST['msps_use_extinction']) {
                MSPS_Extinction::maybe_register_scheduled_action();
            } else {
                MSPS_Extinction::maybe_deregister_scheduled_action();
            }

            wp_send_json_success();
        }

        static function get_setting_fields() {
            $fields = array(
                'type'     => 'Page',
                'title'    => __('포인트 소멸 설정', 'mshop-point-ex'),
                'class'    => '',
                'elements' => array(
                    array(
                        'type'     => 'Section',
                        'title'    => __('기본 설정', 'mshop-point-ex'),
                        'elements' => array(
                            array(
                                'id'        => 'msps_use_extinction',
                                'title'     => __('포인트 소멸 기능 사용', 'mshop-point-ex'),
                                'className' => '',
                                'type'      => 'Toggle',
                                'default'   => 'no',
                            )
                        )
                    ),
                    array(
                        'type'     => 'Section',
                        'title'    => __('포인트 소멸 설정', 'mshop-point-ex'),
                        'showIf'   => array('msps_use_extinction' => 'yes'),
                        'elements' => array(
                            array(
                                "id"        => "msps_extinction_term",
                                "title"     => __("포인트 유효기간", 'mshop-point-ex'),
                                "className" => "",
                                "type"      => "LabeledInput",
                                "label"     => __("일", 'mshop-point-ex'),
                                "value"     => "yes",
                                "inputType" => "number",
                                "valueType" => "unsigned int",
                                "default"   => "365",
                                'desc2'     => __('<div class="desc2">포인트 적립 후 유효기간 내에 사용하지 않은 포인트는 자동으로 소멸처리됩니다.</div>', 'mshop-point-ex')
                            ),
                            array(
                                'id'        => 'msps_extinction_wallet_ids',
                                'title'     => __('포인트 소멸대상', 'mshop-point-ex'),
                                'className' => '',
                                'type'      => 'Select',
                                'multiple'  => true,
                                'default'   => 'monthly',
                                'options'   => self::get_wallets()
                            ),
                            array(
                                'id'        => 'msps_extinction_running_period',
                                'title'     => __('포인트 소멸처리 실행주기', 'mshop-point-ex'),
                                'className' => '',
                                'type'      => 'Select',
                                'default'   => 'monthly',
                                'options'   => array(
//                                    'daily'   => __('매일', 'mshop-point-ex'),
                                    'monthly' => __('매달', 'mshop-point-ex'),
                                    'year'    => __('매년', 'mshop-point-ex')
                                ),
                                'desc2'     => __('<div class="desc2">포인트 소멸 처리가 동작되는 주기를 지정합니다.</div>', 'mshop-point-ex')
                            ),
                            array(
                                "id"        => "msps_extinction_running_day",
                                "title"     => __("매달 지정된 날짜에 실행", 'mshop-point-ex'),
                                'showIf'    => array('msps_extinction_running_period' => 'monthly'),
                                "className" => "",
                                "type"      => "LabeledInput",
                                "label"     => __("일", 'mshop-point-ex'),
                                "value"     => "yes",
                                "inputType" => "number",
                                "valueType" => "unsigned int",
                                "default"   => "1",
                                'desc2'     => __('<div class="desc2">매달 지정된 날짜에 포인트 소멸 처리 기능이 실행됩니다.</div>', 'mshop-point-ex')
                            ),
                            array(
                                "id"          => "msps_extinction_running_date",
                                "title"       => __("매년 지정된 날짜에 실행", 'mshop-point-ex'),
                                'showIf'      => array('msps_extinction_running_period' => 'year'),
                                "className"   => "",
                                "type"        => "Text",
                                "placeholder" => "MM-DD",
                                "label"       => __("일", 'mshop-point-ex'),
                                "value"       => "yes",
                                "valueType"   => "unsigned int",
                                "default"     => "01-01",
                                'desc2'       => __('<div class="desc2">매년 지정된 날짜에 포인트 소멸 처리 기능이 실행됩니다.</div>', 'mshop-point-ex')
                            )
                        )
                    ),
                )
            );

            if(class_exists('MSSMS_Manager')) {
                $fields['elements'][] = array(
                    'type'     => 'Section',
                    'title'    => __('소멸 알림 설정', 'mshop-point-ex'),
                    'showIf'   => array(
                        array('msps_use_extinction' => 'yes'),
                        array('msps_extinction_running_period' => 'monthly,year')
                    ),
                    'elements' => array(
                        array(
                            "id"        => "msps_use_extinction_notification",
                            "title"     => __("포인트 소멸 알림 기능 사용", 'mshop-point-ex'),
                            'className' => '',
                            'type'      => 'Toggle',
                            'default'   => 'no',
                            'desc2'     => __('<div class="desc2">고객에게 소멸되는 포인트에 대한 알림을 발송합니다.</div>', 'mshop-point-ex')
                        ),
                        array(
                            "id"        => "msps_extinction_notification_term",
                            "title"     => __("소멸 안내일", 'mshop-point-ex'),
                            'showIf'    => array('msps_use_extinction_notification' => 'yes'),
                            "className" => "",
                            "type"      => "LabeledInput",
                            "label"     => __("일 이전", 'mshop-point-ex'),
                            "value"     => "yes",
                            "inputType" => "number",
                            "valueType" => "unsigned int",
                            "default"   => "15",
                            'desc2'     => __('<div class="desc2">포인트가 소멸되기 며칠 전에 안내 문자를 발송할 것인지 지정합니다.</div>', 'mshop-point-ex')
                        ),
                        array(
                            "id"        => "msps_extinction_notification_minimum_point",
                            "title"     => __("최소 포인트", 'mshop-point-ex'),
                            'showIf'    => array('msps_use_extinction_notification' => 'yes'),
                            "className" => "",
                            "type"      => "LabeledInput",
                            "label"     => __("포인트", 'mshop-point-ex'),
                            "value"     => "yes",
                            "inputType" => "number",
                            "valueType" => "unsigned int",
                            "default"   => "1000",
                            'desc2'     => __('<div class="desc2">소멸될 포인트가 지정된 포인트보다 많은 사용자에게만 소멸 안내문자가 발송됩니다.</div>', 'mshop-point-ex')
                        ),
                        array(
                            "id"        => "msps_use_extinction_notification_sms",
                            "title"     => __("문자(SMS/LMS) 발송", 'mshop-point-ex'),
                            'showIf'    => array('msps_use_extinction_notification' => 'yes'),
                            'className' => '',
                            'type'      => 'Toggle',
                            'default'   => 'no',
                        ),
                        array(
                            'id'      => 'msps_extinction_notification_sms_content',
                            'showIf'  => array(
                                array('msps_use_extinction_notification' => 'yes'),
                                array('msps_use_extinction_notification_sms' => 'yes')
                            ),
                            'title'   => __('문자(SMS/LMS) 내용', 'mshop-point-ex'),
                            'default' => __("{고객명}님이 보유중인 {소멸예정포인트} 포인트가 {소멸예정일}에 소멸됩니다.", 'mshop-point-ex'),
                            'type'    => 'TextArea',
                            'desc2'   => __('기본문구는 "{고객명}님이 보유중인 {소멸예정포인트} 포인트가 {소멸예정일}에 소멸됩니다." 입니다.', 'mshop-point-ex')
                        ),
                        array(
                            "id"        => "msps_use_extinction_notification_alimtalk",
                            "title"     => __("알림톡 발송", 'mshop-point-ex'),
                            'showIf'    => array('msps_use_extinction_notification' => 'yes'),
                            'className' => '',
                            'type'      => 'Toggle',
                            'default'   => 'no',
                        ),
                        array(
                            "id"          => "msps_extinction_notification_alimtalk_template_code",
                            'showIf'      => array(array('msps_use_extinction_notification' => 'yes'), array('msps_use_extinction_notification_alimtalk' => 'yes')),
                            "title"       => __("알림톡 템플릿", 'mshop-point-ex'),
                            "className"   => "",
                            "type"        => "Select",
                            "placeholder" => "알림톡 템플릿을 선택하세요.",
                            "options"     => MSSMS_Settings_Alimtalk_Send::get_templates()
                        ),
                        array(
                            "id"        => "msps_extinction_notification_alimtalk_resend_method",
                            'showIf'    => array(
                                array('msps_use_extinction_notification' => 'yes'),
                                array('msps_use_extinction_notification_alimtalk' => 'yes')
                            ),
                            "title"     => __("문자 대체 발송", 'mshop-point-ex'),
                            "className" => "",
                            "type"      => "Select",
                            "default"   => "alimtalk",
                            "options"   => array(
                                'none'     => __("사용안함", 'mshop-point-ex'),
                                'alimtalk' => __("알림톡 내용전달", 'mshop-point-ex')
                            )
                        ),
                    )
                );
            } else {
                $fields['elements'][] = array(
                    'type'     => 'Section',
                    'title'    => '포인트 소멸 알림 기능 사용 안내',
                    'elements' => array(
                        array(
                            'id'       => 'mssms_requirement_guide',
                            'type'     => 'Label',
                            'readonly' => 'yes',
                            'default'  => '',
                            'desc2'    => __('<div class="desc2">포인트 소멸 알림 기능을 이용하시려면 "<a target="_blank" href="https://www.codemshop.com/shop/sms_out/">엠샵 문자 알림톡 자동발송 플러그인</a>"이 설치되어 있어야 합니다.</div>', 'mshop-point-ex'),
                        )
                    )
                );
            }

            $fields['elements'][] = array(
                'type'     => 'Section',
                'title'    => __('도구', 'mshop-point-ex'),
                'elements' => array(
                    array(
                        'id'             => 'msps_extinction_clear_scheduled_action',
                        'title'          => '예약작업 초기화',
                        'label'          => '실행',
                        'iconClass'      => 'icon settings',
                        'className'      => '',
                        'type'           => 'Button',
                        'default'        => '',
                        'actionType'     => 'ajax',
                        'confirmMessage' => __('등록된 포인트 소멸 및 소멸알림 예약작업을 모두 삭제하시겠습니까?', 'mshop-point-ex'),
                        'ajaxurl'        => admin_url('admin-ajax.php'),
                        'action'         => msps_ajax_command( 'clear_scheduled_action' ),
                        "desc"           => __('등록된 포인트 소멸 및 소멸알림 예약작업을 모두 삭제합니다.', 'mshop-point-ex'),
                    ),
                )
            );

            return $fields;
        }


        static function enqueue_scripts() {
            wp_enqueue_style('mshop-setting-manager', MSPS()->plugin_url() . '/includes/admin/setting-manager/css/setting-manager.min.css');
            wp_enqueue_script('mshop-setting-manager', MSPS()->plugin_url() . '/includes/admin/setting-manager/js/setting-manager.min.js', array('jquery', 'jquery-ui-core', 'underscore'));
        }
        public static function output() {
            include_once MSPS()->plugin_path() . '/includes/admin/setting-manager/mshop-setting-helper.php';
            $settings = self::get_setting_fields();

            self::enqueue_scripts();

            wp_localize_script('mshop-setting-manager', 'mshop_setting_manager', array(
                'element'  => 'mshop-setting-wrapper',
                'ajaxurl'  => admin_url('admin-ajax.php'),
                'action'   => msps_ajax_command( 'update_msps_extinction_settings' ),
                'settings' => $settings,
            ));

            if(MSPS_Extinction::enabled()) {
                $next_schedule = MSPS_Extinction::get_next_schedule();
                $next_notification_schedule = MSPS_Extinction_Notification::get_next_schedule();

                if( ! empty($next_schedule)) {
                    ?>
                    <div class="notice notice-info">
                        <p style="padding: 10px;">
                        <p><?php echo sprintf(__('포인트 소멸 예약작업이 등록되어 있습니다. 다음 실행 예정 시간은 %s 입니다.', 'mshop-point-ex'), $next_schedule); ?></p>
                        <?php if( ! empty($next_notification_schedule)) : ?>
                            <p><?php echo sprintf(__('포인트 소멸 알림 예약작업이 등록되어 있습니다. 다음 실행 예정 시간은 %s 입니다.', 'mshop-point-ex'), $next_notification_schedule); ?></p>
                        <?php endif; ?>
                        <a href="/wp-admin/admin.php?page=wc-status&tab=action-scheduler&status=pending&s=msps_point_extinction&action=-1&pahed=1&action2=-1"
                           target="_blank" class="button" style="margin-left: 10px;">예약작업 확인하기</a>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="notice notice-error">
                        <p style="padding: 10px;"><?php echo __('포인트 소멸 기능이 활성화되어 있지만, 포인트 소멸 처리를 위한 예약작업이 등록되지 않았습니다. 포인트 소멸 설정을 확인하신 후, 저장 버튼을 클릭해주세요.', 'mshop-point-ex'); ?></p>
                    </div>
                    <?php
                }
            }

            ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery(this).trigger('mshop-setting-manager', ['mshop-setting-wrapper', '100', <?php echo json_encode(MSSHelper::get_settings($settings)); ?>, null]);
                });
            </script>

            <div id="mshop-setting-wrapper"></div>
            <?php
        }
    }
}
