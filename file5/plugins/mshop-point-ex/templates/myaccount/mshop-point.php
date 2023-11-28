<?php

wp_dequeue_style( 'jquery-ui' );

$tabs = apply_filters( 'msps_myaccount_tabs', array (
	'point'      => __( '포인트', 'mshop-point-ex' ),
	'point-logs' => __( '포인트 로그', 'mshop-point-ex' )
) );

$idx = 0;

?>
    <div class="msps-myaccount">
        <ul style="display: flex;">
			<?php foreach ( $tabs as $key => $label ) : ?>
                <li style="flex: 1;">
                    <a href="#<?php echo $key; ?>"><span><?php echo $label; ?></span></a>
                </li>
			<?php endforeach; ?>
        </ul>

        <div class="contents">
			<?php foreach ( $tabs as $key => $label ) : ?>
                <div id="<?php echo $key; ?>" style="<?php echo $idx++ == 0 ? '' : 'display: none;'; ?>">
					<?php do_action( 'msps-tab-' . $key ) ?>
                </div>
			<?php endforeach; ?>
        </div>
    </div>

<?php

