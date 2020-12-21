<div class="general-form">
    <div class="modal-body clearfix">
        <div class="form-group">
            <div class="col-md-12">
                <?php
                echo form_textarea(array(
                    "id" => "embedded-code",
                    "name" => "embedded-code",
                    "value" => $embedded,
                    "class" => "form-control",
                    "data-rich-text-editor" => false
                ));
                ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
        <button type="button" id="copy-button" class="btn btn-primary"><span class="fa fa-copy"></span> <?php echo lang('copy'); ?></button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#copy-button").click(function () {
            var copyTextarea = document.querySelector('#embedded-code');
            copyTextarea.focus();
            copyTextarea.select();
            document.execCommand('copy');
        });
    });
</script>