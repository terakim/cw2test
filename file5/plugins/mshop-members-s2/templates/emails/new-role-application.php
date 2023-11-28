<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$post = get_post( $post_id );

$user         = get_user_by( 'id', get_post_meta( $post_id, 'user_id', true ) );
$roles        = apply_filters( 'msm_get_roles', array() );
$current_role = get_post_meta( $post_id, 'current_role', true );
$current_role = ! empty( $roles[ $current_role ] ) ? $roles[ $current_role ] : '';
$request_role = get_post_meta( $post_id, 'request_role', true );
$request_role = ! empty( $roles[ $request_role ] ) ? $roles[ $request_role ] : '';

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php

$metas = MSM_Meta::get_post_meta( $post_id, '_msm_form' );

if ( ! empty( $metas ) ) {
	?>
	<div class="featured-box align-left msmu">
		<div class="box-content">
			<h3><?php echo apply_filters( 'msmu_view_request_title', __( '등급변경 요청..', 'mshop-members-s2' ) ); ?> <span class="request-date">( <?php echo $post->post_date; ?> )</span></h3>
			<table cellpadding="0" cellspacing="0" style="width: 100%;">
				<tbody>
				<tr>
					<td style="text-align:left;width:100px;border-top-color:#eee;border-top-width:1px;border-top-style:solid;border-bottom-color:#eee;border-bottom-width:1px;border-bottom-style:solid;border-right-color:#eee;border-right-width:1px;border-right-style:solid;background:#f7f7f7;padding:10px;">기존역할</td>
					<td style="text-align:left;border-top-color:#eee;border-top-width:1px;border-top-style:solid;border-bottom:1px solid #eee;padding:10px;"><?php echo $current_role; ?></td>
				</tr>
				<tr>
					<td style="text-align:left;width:100px;border-bottom-color:#eee;border-bottom-width:1px;border-bottom-style:solid;border-right-color:#eee;border-right-width:1px;border-right-style:solid;background:#f7f7f7;padding:10px;">요청역할</td>
					<td style="text-align:left;border-bottom:1px solid #eee;padding:10px;"><?php echo $request_role; ?></td>
				</tr>
				<?php
				foreach ( $metas as $meta ) {
					echo '<tr>';
					echo '<td style="text-align:left;width:100px;border-bottom-color:#eee;border-bottom-width:1px;border-bottom-style:solid;border-right-color:#eee;border-right-width:1px;border-right-style:solid;background:#f7f7f7;padding:10px;">' . $meta['title'] . '</td>';
					echo '<td style="text-align:left;border-bottom:1px solid #eee;padding:10px;">' . str_replace( "\n", "<br>", ! empty( $meta['label'] ) ? $meta['label'] : ( is_string( $meta['value'] ) ? $meta['value'] : '' ) ) . '</td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}
?>

<?php do_action( 'msm_email_new_role_application', $post_id ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
