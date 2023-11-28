<?php
?>

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
    }

    table {
        font-size: 1em;
    }

    .ui-widget.ui-widget-content {
        border: 1px solid #dddddd;
    }

    .ui-dialog {
        padding: 4px !important;
        box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .2);
    }

    .ui-dialog .ui-dialog-title {
        margin: 0;
    }

    .ui-draggable, .ui-droppable {
        background-position: top;
    }

    .ui-dialog .ui-dialog-content {
        box-sizing: border-box;
    }

    #progressbar {
        margin-top: 6px;
        border: none;
    }

    .ui-progressbar .ui-progressbar-value {
        margin: 0;
        height: 100%;
        background: #fbbd08;
    }

    button#downloadButton {
        z-index: 1;
        position: relative;
    }

    .progress-label {
        margin: 10px 0px;
        font-weight: normal;
    }

    .ui-dialog .ui-button.ui-dialog-titlebar-close,
    .ui-dialog .ui-button.ui-dialog-titlebar-close:hover {
        width: 20px;
        height: 20px;
        color: transparent;
        top: 50% !important;
        right: 10px;
        outline: none;
        border: 1px solid #c5c5c5;
    }

    .ui-dialog .ui-button.ui-dialog-titlebar-close:active {
        border: 1px solid #cccccc;
        background: #ededed;
    }

    .ui-dialog .ui-button.ui-dialog-titlebar-close span {
        position: absolute;
        display: block;
        top: 7px;
        left: 2px;
        width: 13px;
        height: 3px;
        background: #494949 !important;
        -ms-transform: rotate(45deg);
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);
        margin-top: 0;
        margin-left: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .ui-dialog .ui-button.ui-dialog-titlebar-close span:last-child {
        -ms-transform: rotate(-45deg);
        -webkit-transform: rotate(-45deg);
        transform: rotate(-45deg);
    }

    .ui-draggable .ui-dialog-titlebar {
        border: 1px solid #dddddd;
        background: #f9fafb;
        color: #333333;
        font-weight: bold;
    }

    .ui-dialog .ui-dialog-buttonpane.ui-widget-content {
        margin: 0 1em 0;
        padding: 8px 0 4px;
    }

    .ui-dialog .ui-dialog-buttonpane.ui-widget-content button {
        margin: 0;
        padding: .3em .5em;
        outline: none;
        background: #21ba45;
        border: 1px solid #21ba45;
        color: #ffffff !important;
        font-size: 1em;
        line-height: 1;
    }

    .ui-dialog .ui-dialog-buttonpane.ui-widget-content .ui-button-text-only .ui-button-text {
        padding: 0;
    }

    .ui-dialog .ui-button.ui-dialog-titlebar-close, .ui-dialog .ui-button.ui-dialog-titlebar-close:hover {
        display: none !important;
    }

    div.template_selector {
        font-size: 1em;
        display: flex;
        margin-bottom: 5px;
    }

    div.template_selector select {
        flex: 1;
    }

    div.template_selector input[type=button] {
        font-size: 1em;
        width: 70px;
        margin-left: 10px;
    }
</style>

<div id="msex_download" title="엠샵 다운로더">
    <div class="progress-label">템플릿을 선택하신 후, 다운로드 버튼을 클릭해주세요.</div>
    <div class="template_selector">
        <select name="msex_template">
			<?php foreach ( $templates as $template ) : ?>
                <option value="<?php echo $template->ID; ?>"><?php echo $template->post_title; ?></option>
			<?php endforeach; ?>
        </select>
        <input type="button" class="button button-primary start-download" value="다운로드">
    </div>
    <div class="progress_wrapper" style="display: none;">
        <div id="progressbar"></div>
    </div>
</div>
