<?php

if ( ! empty( $point_info['user_id'] ) ) {
    $user = get_user_by( 'id', $point_info['user_id'] );
} else {
    $user = get_user_by( 'login', $point_info['user_login'] );
}

$msps_user = new MSPS_User( $user );

?>
<tr class="upload-point-item" data-point_data="<?php echo esc_attr( json_encode( $point_info ) ); ?>">
    <td class="status">준비</td>
    <td>
        <?php echo sprintf( '<a href="%s">#%d</a>, %s', get_edit_user_link( $user->ID ), $user->ID, $user->display_name ); ?>
    </td>
    <td><?php echo msps_get_wallet_name( $msps_user, msps_get( $point_info, 'wallet_id', 'free_point' ) ); ?></td>
    <td><?php echo $point_info['action']; ?></td>
    <td><?php echo $point_info['point']; ?></td>
    <?php if ( has_filter( 'wpml_object_id' ) ) : ?>
        <td><?php echo msps_get( $point_info, 'lang', apply_filters( 'msps_get_current_language', mshop_wpml_get_current_language() ) ); ?></td>
    <?php endif; ?>
    <td><?php echo $point_info['message']; ?></td>
</tr>