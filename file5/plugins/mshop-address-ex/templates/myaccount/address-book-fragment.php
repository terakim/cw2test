<?php
?>

<?php foreach ( $destinations['address'] as $key => $destination ) : ?>
    <div class="history" data-key="<?php echo $key; ?>" data-address="<?php echo esc_html( json_encode( $destination['address'] ) ); ?>">
        <div class="items destinations-info">
            <?php echo 'billing' == $address_type ? MSADDR_Address_Book::get_formatted_billing_address( $destination['address'] ) : MSADDR_Address_Book::get_formatted_address( $destination['address'] ); ?>
            <?php
            $custom_values = MSADDR_Address_Book::get_custom_values( $address_type, $destination['address'] );
            if ( ! empty( $custom_values ) ) {
                ?>
                <div class="shipping-info">
                    <p class="custom-fields"><span><?php echo implode( '</span><span>', $custom_values ); ?></></p>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="items destinations-edit">
            <div class="pafw-icon set-default <?php echo $destination['default'] ? 'default' : ''; ?>"></div>
            <div class="pafw-icon edit-destination"></div>
            <div class="pafw-icon delete-destination"></div>
        </div>
    </div>
<?php endforeach; ?>