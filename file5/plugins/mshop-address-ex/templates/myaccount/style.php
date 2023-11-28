<?php

if ( ! class_exists( 'PAFW_DC_Shortcodes' ) ) {
    return;
}

$theme = PAFW_DC_Shortcodes::get_theme( $params['theme'] );
?>
<style>
    .pafw-checkout-block.type-b #pafw-dc-address ul li.ui-state-active label::before {
        background-color: <?php echo $theme['color'] ?> !important;
    }

    .pafw-checkout-block.type-b .info-panel .history.selected .items.destinations-checkbox label::before {
        background-color: <?php echo $theme['color'] ?> !important;
    }

    .pafw-checkout-block.type-b .info-panel#destinations .button:not(.destinations-nav .button),
    .pafw-checkout-block.type-b .info-panel#destinations .destinations-nav .button:hover,
    .pafw-checkout-block.type-b .info-panel#destinations .destinations-nav .button[disabled] {
        border: 1px solid <?php echo $theme['color'] ?> !important;
        background-color: <?php echo $theme['color'] ?> !important;
    }

    .pafw-checkout-block.type-b #pafw-dc-address.tab-style-tab ul li.ui-state-active {
        border-top: 3px solid <?php echo $theme['color'] ?>;
    }

    .pafw-checkout-block.type-b #pafw-dc-address.tab-style-button ul li.ui-state-active a {
        background-color: <?php echo $theme['color'] ?> !important;
    }

    .pafw-checkout-block.type-b #pafw-dc-address.list-style-block .info-panel .history::before {
        background-color: <?php echo $theme['color'] ?> !important;
    }

    .pafw-checkout-block.type-b .pafw-icon::before {
        color : <?php echo $theme['color'] ?> !important;
    }

    .pafw-checkout-block.type-b .pafw-icon:hover::before {
        font-weight: bold;
    }

    .pafw-checkout-block.type-b .pafw-icon.set-default::before {
        content:'\0032';
    }

    .pafw-checkout-block.type-b .pafw-icon.edit-destination::before {
        content:'\0036';
    }

    .pafw-checkout-block.type-b .pafw-icon.set-default.default::before {
        content:'\0033';
    }

    .pafw-checkout-block.type-b .pafw-icon.delete-destination::before {
        content:'\0034';
    }

    .pafw-checkout-block.type-b #pafw-dc-address.list-style-table .history.selected {
        background-color: <?php echo $theme['color'] ?>10;
    }
</style>