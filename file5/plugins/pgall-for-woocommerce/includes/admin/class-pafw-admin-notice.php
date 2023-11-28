<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PAFW_Admin_Notice' ) ) :
	class PAFW_Admin_Notice {

		protected static $slug;
		protected static $version;
		protected static $dismiss_args;
		public static function init( $slug, $version ) {
			self::$slug    = $slug;
			self::$version = $version;

			self::$dismiss_args = $slug . '-dismiss';


			add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
			add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		}

		static function admin_init() {
			if ( ! empty( $_REQUEST[ self::$dismiss_args ] ) ) {
				$transient = get_transient( self::$slug . '-notice-' . wc_clean( $_REQUEST[ self::$dismiss_args ] ) );
				if ( empty( $transient ) ) {
					set_transient( self::$slug . '-notice-' . wc_clean( $_REQUEST[ self::$dismiss_args ] ), 'yes', MONTH_IN_SECONDS );
				}
			}
		}

		static function get_options( $options, $key, $default = '' ) {
			return isset( $options[ $key ] ) ? $options[ $key ] : $default;
		}

		static function convert_smart_quotes( $string ) {
			$quotes = array(
				"\xC2\xAB"     => '"', // « (U+00AB) in UTF-8
				"\xC2\xBB"     => '"', // » (U+00BB) in UTF-8
				"\xE2\x80\x98" => "'", // ‘ (U+2018) in UTF-8
				"\xE2\x80\x99" => "'", // ’ (U+2019) in UTF-8
				"\xE2\x80\x9A" => "'", // ‚ (U+201A) in UTF-8
				"\xE2\x80\x9B" => "'", // ‛ (U+201B) in UTF-8
				"\xE2\x80\x9C" => '"', // “ (U+201C) in UTF-8
				"\xE2\x80\x9D" => '"', // ” (U+201D) in UTF-8
				"\xE2\x80\x9E" => '"', // „ (U+201E) in UTF-8
				"\xE2\x80\x9F" => '"', // ‟ (U+201F) in UTF-8
				"\xE2\x80\xB9" => "'", // ‹ (U+2039) in UTF-8
				"\xE2\x80\xBA" => "'", // › (U+203A) in UTF-8
			);

			return strtr( $string, $quotes );
		}

		static function admin_notices() {
			try {
				$admin_notices = self::get_admin_notices();

				if ( ! empty( $admin_notices ) && is_array( $admin_notices ) ) {

					foreach ( $admin_notices as $admin_notice ) {
						if ( is_array( $admin_notice ) && isset( $admin_notice['id'] ) ) {
							$transient = get_transient( self::$slug . '-notice-' . $admin_notice['id'] );

							if ( empty( $transient ) ) {
								$excerpt = pafw_get( $admin_notice, 'excerpt', array() );

								$options = strip_tags( html_entity_decode( pafw_get( $excerpt, 'rendered' ) ) );
								$options = json_decode( self::convert_smart_quotes( $options ), true );

								$version = pafw_get( $options, 'version' );
								if ( empty( $version ) || version_compare( self::$version, $version, "<" ) ) {
									?>
                                    <div class="notice is-dismissible notice-<?php echo self::get_options( $options, 'type', 'success' ); ?>">
										<?php
										$content = pafw_get( $admin_notice, 'content', array() );
										echo pafw_get( $content, 'rendered' );
										?>
										<?php if ( 'no' != self::get_options( $options, 'dismiss', 'yes' ) ) : ?>
                                            <a href="<?php echo add_query_arg( self::$dismiss_args, $admin_notice['id'] ); ?>" class="button" style="margin-bottom: 10px;">더보지않기</a>
										<?php endif; ?>
                                    </div>
									<?php
								}
							}
						}
					}
				}
			} catch ( Exception $e ) {

			}
		}

		static function load_admin_notices() {
			$after = date( 'Y-m-d\TH:i:s', strtotime( '-7 days', strtotime( current_time( 'mysql' ) ) ) );

			$url = "https://www.codemshop.com/wp-json/wp/v2/posts?per_page=5&filter[category_name]=" . self::$slug . "-notification&after=" . $after;

			$response = wp_remote_get( $url, array(
					'timeout'     => 10,
					'redirection' => 5,
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$admin_notices = json_decode( $response['body'], true );
				set_transient( self::$slug . '-notices', $admin_notices, DAY_IN_SECONDS );

				return $admin_notices;
			}
		}

		static function get_admin_notices() {
			$admin_notices = get_transient( self::$slug . '-notices' );

			if ( $admin_notices === false ) {
				$admin_notices = self::load_admin_notices();
			}

			return $admin_notices;
		}
	}

endif;