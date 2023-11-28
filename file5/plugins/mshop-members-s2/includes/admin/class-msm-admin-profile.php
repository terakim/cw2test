<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'MSM_Admin_Profile' ) ) :

	class MSM_Admin_Profile {
		public static function add_form_tag() {
			echo 'enctype="multipart/form-data"';
		}
		public static function add_members_fields( $user ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$fields = get_option( 'msm_user_fields', array() );

            if ( ! empty( $fields ) ) :
                wp_enqueue_style( 'msm-semantic-css', MSM()->plugin_url() . '/assets/vendor/semantic/semantic.min.css', array(), MSM_VERSION );
                wp_enqueue_script( 'semantic-ui', MSM()->plugin_url() . '/assets/vendor/semantic/semantic.min.js', array( 'jquery', 'jquery-ui-core', 'underscore' ), MSM_VERSION );
                ?>

                <h2><?php echo __( '멤버스 필드', 'mshop-members-s2' ) ?></h2>
                <table class="form-table">
                    <?php foreach ( $fields as $field ) :
                        if ( 'yes' != $field['enabled'] || empty( $field['key'] ) || empty( $field['title'] ) ) {
                            continue;
                        }

                        $value = get_user_meta( $user->ID, $field['key'], true );
                        ?>
                        <tr>
                            <th>
                                <label for="<?php echo esc_attr( $field['key'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
                            </th>
                            <td>
                                <?php if ( 'file' == $field['type'] ) :
                                    echo sprintf( '<input type="file" name="%s[]" %s>', $field['key'], 'yes' == $field['multiple'] ? 'multiple' : '' );

                                    $label = get_user_meta( $user->ID, $field['key'] . '_label', true );
                                    if ( ! empty( $label ) ) {
                                        echo sprintf( __( '<p>파일 다운로드 : %s</p>', 'mshop-members-s2' ), implode( ', ', explode( '<br>', $label ) ) );
                                    }

                                    if ( is_array( get_user_meta( $user->ID, $field['key'], true ) ) ) {
                                        $images = array_column( get_user_meta( $user->ID, $field['key'], true ), 'image' );
                                        if ( ! empty( $images ) ) {
                                            echo sprintf( '<ul class="image-preview">%s</ul>', implode( '', $images ) );
                                        }
                                    }
                                    ?>
                                <?php elseif ( 'textarea' == $field['type'] ) : ?>
                                    <textarea name="<?php echo esc_attr( $field['key'] ); ?>" id="<?php echo esc_attr( $field['key'] ); ?>" rows="5"><?php echo ! empty( $value ) ? esc_html( $value ) : ''; ?></textarea>
                                <?php elseif ( 'select' == $field['type'] ) : ?>
                                    <script>
                                        jQuery( document ).ready( function ( $ ) {
                                            var id = '<?php _e( $field['key'] ); ?>';

                                            $( '#' + id ).dropdown( {
                                                onChange: function ( value, text, $selectedItem ) {
                                                    $( 'input[name=' + id + ']' ).val( value );
                                                },
                                                fullTextSearch : true
                                            } );
                                        } );
                                    </script>
                                    <div id="<?php echo esc_attr( $field['key'] ); ?>" class="ui fluid <?php _e( 'yes' == $field['multiple'] ? 'multiple' : '' ); ?> search selection dropdown">
                                        <input type="hidden" name="<?php echo esc_attr( $field['key'] ); ?>" value="<?php echo $value; ?>">
                                        <i class="dropdown icon"></i>
                                        <div class="default text"><?php _e( '옵션을 선택하세요.', 'mshop-members-s2' ) ?></div>
                                        <div class="menu">
                                            <?php
                                            $load_field = MSM_Fields::load_fields();
                                            $idx        = array_search( $field['fields'], array_column( $load_field, 'slug' ) );
                                            $options    = $load_field[$idx];

                                            foreach ( $options['values'] as $option ) {
                                                echo '<div class="item" data-value="' . $option['slug'] . '">' . $option['name'] . '</div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php elseif ( 'radio' == $field['type'] || 'checkbox' == $field['type'] ) : ?>
                                    <?php
                                    $load_field = MSM_Fields::load_fields();
                                    $idx        = array_search( $field['fields'], array_column( $load_field, 'slug' ) );
                                    $options    = $load_field[$idx];

                                    foreach ( $options['values'] as $option ) : ?>
                                        <?php echo sprintf( '<p><input type="%s" name="%s" id="%s" value="%s" %s/> %s</p>', $field['type'], esc_attr( $field['key'] ), esc_attr( $field['key'] ), $option['slug'], $value == $option['slug'] ? 'checked' : '', $option['name'] ); ?>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <input type="text" name="<?php echo esc_attr( $field['key'] ); ?>" id="<?php echo esc_attr( $field['key'] ); ?>" value="<?php echo ! empty( $value ) ? esc_html( $value ) : ''; ?>"/>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif;

			$user_status    = array(
				'1' => __( '탈퇴', 'mshop-members-s2' ),
				'2' => __( '휴면', 'mshop-members-s2' ),
				'0' => __( '정상', 'mshop-members-s2' )
			);
			$current_status = get_user_meta( $user->ID, 'is_unsubscribed', true );
			if ( empty( $current_status ) ) {
				$current_status = 0;
			}

			?>
            <h2><?php echo __( '회원상태', 'mshop-members-s2' ) ?></h2>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="is_unsubscribed"><?php _e( '회원상태', 'mshop-members-s2' ); ?></label>
                    </th>
                    <td>
                        <select name="is_unsubscribed">
							<?php foreach ( $user_status as $key => $label ) : ?>
                                <option value="<?php echo $key; ?>" <?php echo $current_status == $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
							<?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
			<?php
		}
		public static function save_members_fields( $user_id ) {

            $fields = get_option( 'msm_user_fields', array() );

			foreach ( $fields as $field ) {
                if ( ! empty( $field['key'] ) ) {
                    if ( 'file' == $field['type'] ) {
                        $files = msm_get( $_FILES, $field['key'] );

                        if ( ! empty( $files ) ) {
                            $file_count = count( array_filter( $files['name'] ) );

                            if ( $file_count > 0 ) {
                                $upload_dir = MSM_Meta::get_upload_dir( array( 'type' => 'user', 'id' => $user_id ) );
                                $metas      = array();
                                $labels     = array();

                                for ( $i = 0; $i < $file_count; $i++ ) {
                                    $file_name = $files['name'][ $i ];

                                    if ( apply_filters( 'msm_url_encode_to_upload_filename', true ) ) {
                                        $file_name = urlencode( $file_name );
                                    }

                                    $destination = $upload_dir . basename( $file_name );

                                    if ( move_uploaded_file( $files['tmp_name'][ $i ], $destination ) ) {
                                        $meta_key = uniqid();

                                        $metas[ $meta_key ] = array(
                                            'field_key' => $field['key'],
                                            'filename'  => $destination
                                        );

                                        if ( wp_getimagesize( $destination ) ) {
                                            $dir  = wp_upload_dir();
                                            $link = sprintf( '%s/mshop_members/user/%s/%s', $dir['baseurl'], $user_id, urlencode( basename( $file_name ) ) );
                                            $metas[ $meta_key ] = array_merge( $metas[ $meta_key ], array(
                                                'image' => sprintf( '<li data-title="%s"><a href="%s" target="_blank"><img src="%s"></a></li>', urldecode( $file_name ), $link, $link )
                                            ) );
                                        }

                                        $url      = sprintf( '%s/?msm_file_download=%d&key=%s&type=%s&meta_name=%s', site_url(), $user_id, $meta_key, 'user', $field['key'] );
                                        $labels[] = '<a href="' . $url . '">' . urldecode( $file_name ) . '</a>';
                                    } else {
                                        throw new Exception( __( '파일 업로드중 오류가 발생했습니다.', 'mshop-members-s2' ) );
                                    }
                                }

                                update_user_meta( $user_id, $field['key'], $metas );
                                update_user_meta( $user_id, $field['key'] . '_label', implode( '<br>', $labels ) );
                            }
                        }
                    } else {
                        update_user_meta( $user_id, $field['key'], $_POST[ $field['key'] ] );
                    }
                }
			}

			if ( isset( $_POST['is_unsubscribed'] ) ) {
				update_user_meta( $user_id, 'is_unsubscribed', $_POST['is_unsubscribed'] );
			}
		}
	}

endif;
