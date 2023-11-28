<?php
class MSPS_Comment {
	public static function set_earn_point( $comment_id, $amount = 0 ) {
		if ( $amount > 0 ) {
			update_comment_meta( $comment_id, '_mshop_point_post_amount', $amount );
		} else {
			delete_comment_meta( $comment_id, '_mshop_point_post_amount' );
		}
	}

	public static function get_earn_point( $comment_id ) {
		$earn_point = get_comment_meta( $comment_id, '_mshop_point_post_amount', true );

		return is_null( $earn_point ) ? 0 : $earn_point;
	}

	public static function is_earn_processed( $comment_id ) {
		return 'yes' == get_comment_meta( $comment_id, '_mshop_point_post_processed', true );
	}
	public static function set_earn_processed( $comment_id, $flag ) {
		update_comment_meta( $comment_id, '_mshop_point_post_processed', $flag ? 'yes' : 'no' );
	}
	public static function earn_point( $comment ) {
		if ( ! self::is_earn_processed( $comment->comment_ID ) ) {
			$user_id = $comment->user_id;
			$post_id = $comment->comment_post_ID;

			if ( ! empty( $user_id ) && intval( $user_id ) > 0 ) {
				$user = get_user_by( 'id', $user_id );

				if ( $user instanceof WP_User ) {
					$user_role = mshop_point_get_user_role( $user_id );
					if ( MSPS_Post_Manager::is_valid_user( $user_role ) ) {

						$point = MSPS_Post_Manager::get_expected_comment_point( $post_id, $user_role, $comment );

						if ( ! empty( $point ) && floatval( $point ) > 0 ) {
							$mshop_user   = new MSPS_User( $user );
							$prev_point   = $mshop_user->get_point();
							$remain_point = $mshop_user->earn_point( $point );

							self::set_earn_point( $comment->comment_ID, $point );
							self::set_earn_processed( $comment->comment_ID, true );

							$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

							MSPS_Log::add_log( $user_id, msps_get_wallet_id( 'free_point', null, $current_language ), 'earn', 'comment', $point, $remain_point, 'completed', $comment->comment_ID );
						}
					}

				}
			}
		}
	}
	public static function deduct_point( $comment ) {
		if ( self::is_earn_processed( $comment->comment_ID ) ) {
			$user_id = $comment->user_id;
			$post_id = $comment->comment_post_ID;

			if ( ! empty( $user_id ) && intval( $user_id ) > 0 ) {
				$user = get_user_by( 'id', $user_id );

				if ( $user instanceof WP_User ) {
					$user_role = mshop_point_get_user_role( $user_id );

					$point = self::get_earn_point( $comment->comment_ID );

					$mshop_user   = new MSPS_User( $user );
					$prev_point   = $mshop_user->get_point();
					$remain_point = $mshop_user->deduct_point( $point );

					self::set_earn_processed( $comment->comment_ID, false );

					$current_language = apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() );

					MSPS_Log::add_log( $user_id, msps_get_wallet_id( 'free_point', null, $current_language ), 'deduct', 'comment', - 1 * $point, $remain_point, 'completed', $comment->comment_ID );
				}
			}
		}
	}
	public static function wp_insert_comment( $id, $comment ) {
		if ( 'approved' == wp_get_comment_status( $comment ) ) {
			$user_role = mshop_point_get_user_role( $comment->user_id );

			if ( ! empty( $user_role ) &&
			     MSPS_Post_Manager::is_valid_user( $user_role ) &&
			     MSPS_Post_Manager::is_applicable( $comment->comment_post_ID ) &&
			     MSPS_Post_Manager::can_earn_point( $comment->user_id, $comment->comment_post_ID, 'comment' ) ) {
				self::earn_point( $comment );
			}
		}
	}
	public static function wp_set_comment_status( $id, $comment_status ) {
		$comment = get_comment( $id );

		switch ( $comment_status ) {
			case 'approve':
			case '1':
				$user_role = mshop_point_get_user_role( $comment->user_id );

				if ( ! empty( $user_role ) &&
				     MSPS_Post_Manager::is_valid_user( $user_role ) &&
				     MSPS_Post_Manager::is_applicable( $comment->comment_post_ID ) &&
				     MSPS_Post_Manager::can_earn_point( $comment->user_id, $comment->comment_post_ID, 'comment' ) ) {
					self::earn_point( $comment );
				}
				break;
			case 'unapprove':
			case 'hold':
			case 'spam':
				self::deduct_point( $comment );
				break;
			default:
				break;
		}

	}
}