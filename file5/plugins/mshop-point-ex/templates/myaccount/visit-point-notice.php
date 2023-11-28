<?php
?>
<style>
    .msps-visit-point-notification {
        background-color: #446084;
        z-index: 999;
        position: fixed;
        width: 100%;
    }

    .msps-visit-point-notification h1 {
        margin: 0 !important;
        color: white;
        text-align: center;
        font-size: 13px;
        padding: 10px;
        border-top: 1px solid white;
        border-bottom: 1px solid white;
    }
</style>
<script>
    setTimeout( function () {
        jQuery( '.msps-visit-point-notification' ).slideUp( 1500 );
    }, 10000 );
</script>
<div class="msps-visit-point-notification">
    <h1><?php echo $message; ?></h1>
</div>