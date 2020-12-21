<?php echo form_open(get_uri("team_members/save_recently_meaning"), array("id" => "recently-meaning-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix p30">
    <div class="form-group">
        <label for="recently_meaning" class="col-md-4"><?php echo lang('recently_meaning'); ?></label>
        <div class="col-md-8">
            <?php
            $recently_meaning = get_setting("user_" . $this->login_user->id . "_recently_meaning");
            echo form_dropdown("recently_meaning", $recently_meaning_dropdown, $recently_meaning ? $recently_meaning : "1_days", "class='select2'");
            ?>
        </div>
    </div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#recently-meaning-form").appForm();
        $("#recently-meaning-form .select2").select2();

        $('#ajaxModal').on('hidden.bs.modal', function () {
            location.reload();
        });
    });
</script>    