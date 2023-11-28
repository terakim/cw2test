<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

?>

<?php do_action('woocommerce_email_header', $email_heading, $email); ?>

<table id="wysija_container">
    <tbody>
    <tr>
        <td id="wysija_body">
            <div wysija_type="content" class="wysija_block" wysija_position="1" id="anonymous_element_5">
                <div class="wysija_content center" wysija_align="center">
                    <div class="wysija_text" style="width: 562px; float: none; height: auto;">
                        <div class="wysija_editable" style="width:564px;">
                            <?php
                            $metas = MSM_Meta::get_post_meta( $post_id, '_msm_form' );

                            if ( ! empty( $metas ) ) {

                            echo '<table class="application_info">';
                                foreach ( $metas as $meta ) {
                                if ( ! empty( $meta['title'] ) ) {
                                echo '<tr>';
                                    echo '<td class="meta_key">' . $meta['title'] . '</td>';
                                    echo '<td class="meta_value">' . str_replace( "\n", "<br>", ! empty( $meta['label'] ) ? $meta['label'] : $meta['value'] ) . '</td>';
                                    echo '</tr>';
                                }
                                }
                                echo '</table>';
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>

<?php do_action('woocommerce_email_footer', $email); ?>
