<?php echo form_open(get_uri("tickets/save_ticket_template"), array("id" => "ticket-template-form", "class" => "general-form", "role" => "form")); ?>

<div class="modal-body clearfix">   
    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

    <div class="form-group">
        <label for="title" class="col-md-3"><?php echo lang('title'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_input(array(
                "id" => "title",
                "name" => "title",
                "value" => $model_info->title,
                "class" => "form-control",
                "placeholder" => lang('title'),
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
            ));
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="description" class="col-md-3"><?php echo lang('description'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_textarea(array(
                "id" => "description",
                "name" => "description",
                "value" => $model_info->description,
                "class" => "form-control",
                "placeholder" => lang('description'),
                "style" => "height: 150px;",
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
                "data-rich-text-editor" => true
            ));
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="ticket_type_id" class=" col-md-3"><?php echo lang('ticket_type'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_dropdown("ticket_type_id", $ticket_types_dropdown, $model_info->ticket_type_id, "class='select2'");
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="private" class="col-md-3"><?php echo lang('private'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_checkbox("private", "1", $model_info->private ? true : false, "id='private'");
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
        $("#ticket-template-form").appForm({
            onSuccess: function (result) {
                $("#ticket-template-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        $("#title").focus();

        $("#ticket-template-form .select2").select2();

    });

</script>