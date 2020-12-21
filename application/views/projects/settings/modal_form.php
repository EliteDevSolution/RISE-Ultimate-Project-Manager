<?php echo form_open(get_uri("projects/save_settings"), array("id" => "project-settings-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix p30">
    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
    <input type="hidden" id="send_a_test_message" name="send_a_test_message" value="" />

    <?php if ($can_edit_timesheet_settings) { ?>
        <div class="form-group">
            <label for="client_can_view_timesheet" class="col-md-5"><?php echo lang('client_can_view_timesheet'); ?></label>
            <div class="col-md-7">
                <?php
                echo form_checkbox("client_can_view_timesheet", "1", get_setting("client_can_view_timesheet") ? true : false, "id='client_can_view_timesheet' class=''");
                ?>
            </div>
        </div>
    <?php } ?>

    <?php if ($can_edit_slack_settings) { ?>
        <div class="form-group">
            <label for="project_enable_slack" class="col-md-5"><?php echo lang('enable_slack'); ?></label>
            <div class="col-md-7">
                <?php
                echo form_checkbox("project_enable_slack", "1", get_setting("project_enable_slack") ? true : false, "id='project_enable_slack' class=''");
                ?>
            </div>
        </div>

        <div id="slack-details-area" class="<?php echo get_setting("project_enable_slack") ? "" : "hide" ?>">

            <div class="form-group">
                <label for="" class=" col-md-12">
                    <?php echo lang("get_the_webhook_url_of_your_app_from_here") . " " . anchor("https://api.slack.com/apps", "Slack Apps", array("target" => "_blank")); ?>
                </label>
            </div>

            <div class="form-group">
                <label for="project_slack_webhook_url" class=" col-md-3"><?php echo lang('slack_webhook_url'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "project_slack_webhook_url",
                        "name" => "project_slack_webhook_url",
                        "value" => get_setting("project_slack_webhook_url"),
                        "class" => "form-control",
                        "placeholder" => lang('slack_webhook_url'),
                        "data-rule-required" => true,
                        "data-msg-required" => lang("field_required")
                    ));
                    ?>
                </div>
            </div>

        </div>
    <?php } ?>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>

    <?php if ($can_edit_slack_settings) { ?>
        <button id="test-slack-btn" type="button" class="btn btn-info <?php echo (get_setting("project_enable_slack") && get_setting("project_slack_webhook_url")) ? "" : "hide"; ?>"><span class="fa fa-check-circle"></span> <?php echo lang('save_and_send_a_test_message'); ?></button>
    <?php } ?>

    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#project-settings-form").appForm({
        });

        //show/hide slack details area
        $("#project_enable_slack").click(function () {
            if ($(this).is(":checked")) {
                $("#slack-details-area").removeClass("hide");
                $("#test-slack-btn").removeClass("hide");
            } else {
                $("#slack-details-area").addClass("hide");
                $("#test-slack-btn").addClass("hide");
            }
        });

        //flag to send a test message
        $("#test-slack-btn").click(function () {
            $("#send_a_test_message").val("1");
            $("#project-settings-form").trigger("submit");
        });
    });
</script>    