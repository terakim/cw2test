<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MSADDR_Admin_Post_types' ) ) {
	class MSADDR_Admin_Post_types {

		public static function init() {
			add_filter( 'manage_users_columns', __CLASS__ . '::manage_users_columns', 100 );
			add_filter( 'manage_users_custom_column', __CLASS__ . '::manage_users_custom_column', 10, 3 );

			add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );
		}

		public static function admin_enqueue_scripts() {
			wp_enqueue_script( 'jquery-blockui', MSADDR()->plugin_url() . '/assets/js/blockui/jquery.blockUI.js', array (), MSADDR()->version );
			wp_enqueue_script( 'msaddr-book', MSADDR()->plugin_url() . '/assets/js/admin/address-book.js', array (), MSADDR()->version );
			wp_localize_script( 'msaddr-book', '_msaddr', array (
				'ajaxurl'     => admin_url( 'admin-ajax.php', 'relative' ),
				'slug'        => MSADDR_AJAX_PREFIX,
				'_ajax_nonce' => wp_create_nonce( 'delete_address_item' )
			) );

			wp_enqueue_style( 'msaddr-book', MSADDR()->plugin_url() . '/assets/css/admin/admin.css', array (), MSADDR()->version );
		}

		public static function manage_users_custom_column( $value, $column_name, $userid ) {
			if ( 'address_book' == $column_name ) {
				$addresses = get_user_meta( $userid, '_msaddr_shipping_history', true );

				if ( ! empty( $addresses ) ) {
					ob_start();
					?>
                    <table class="msaddr_address_book">
						<?php foreach ( $addresses as $key => $value ) : ?>
                            <tr>
                                <td><?php echo MSADDR_Address_Book::get_formatted_address( $value ); ?></td>
                                <td><input type="button" class="button delete-address-item" data-key="<?php echo $key; ?>" data-user_id="<?php echo $userid; ?>" value="<?php _e( '삭제', 'mshop-address-ex' ); ?>"></td>
                            </tr>
						<?php endforeach; ?>
                    </table>
					<?php

					$value = ob_get_clean();
				}
			}

			return $value;
		}

		public static function manage_users_columns( $users_columns ) {
			if ( msaddr_enabled() && MSADDR_Address_Book::is_enabled() ) {
				$columns = apply_filters( 'msm_users_columns', array (
					'address_book' => __( '배송지목록', 'mshop-address-ex' ),
				) );

				$users_columns = array_merge( $users_columns, $columns );
			}

			return $users_columns;
		}
	}

	MSADDR_Admin_Post_types::init();
}