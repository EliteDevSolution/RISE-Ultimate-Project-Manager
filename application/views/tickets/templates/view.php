<div class="modal-body clearfix general-form">
    <div class="form-group">
        <div class="col-md-12 notepad-title">
            <strong><?php echo $model_info->title; ?></strong>
            <?php if ($model_info->private) { ?>
                <div class='text-off font-11'>
                    <i class='fa fa-lock text-off mr5'></i> <?php echo lang("private_template"); ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="col-md-12 ">
        <?php echo $model_info->description; ?>
    </div>
</div>

<div class="modal-footer">
    <?php
    if ($model_info->created_by == $this->login_user->id || $this->login_user->is_admin) {
        echo modal_anchor(get_uri("tickets/ticket_template_modal_form/"), "<i class='fa fa-pencil'></i> " . lang('edit'), array("class" => "btn btn-default", "data-post-id" => $model_info->id, "title" => lang('edit_template')));
    }
    ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>