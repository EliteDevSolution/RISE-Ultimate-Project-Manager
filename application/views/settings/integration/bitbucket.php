<div class="panel panel-default no-border clearfix mb0">

    <?php echo form_open(get_uri("settings/save_bitbucket_settings"), array("id" => "bitbucket-form", "class" => "general-form dashed-row", "role" => "form")); ?>

    <div class="panel-body">

        <div class="form-group">
            <label for="enable_bitbucket_commit_logs_in_tasks" class="col-md-3"><?php echo lang('enable_bitbucket_commit_logs_in_tasks'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_checkbox("enable_bitbucket_commit_logs_in_tasks", get_setting("enable_bitbucket_commit_logs_in_tasks"), get_setting("enable_bitbucket_commit_logs_in_tasks") ? true : false, "id='enable_bitbucket_commit_logs_in_tasks'");
                ?>                       
            </div>
        </div>

        <div class="bitbucket-details-area <?php echo get_setting("enable_bitbucket_commit_logs_in_tasks") ? "" : "hide" ?>">
            <div class="form-group">
                <label for="" class=" col-md-12">
                    <?php echo lang("add_webhook_in_your_repository_at") . " " . anchor("https://www.bitbucket.org", "Bitbucket", array("target" => "_blank")); ?>
                </label>
            </div>

            <div class="form-group clearfix">
                <label for="webhook_listener_link" class=" col-md-3"><?php echo lang('webhook_listener_link'); ?></label>
                <div class=" col-md-9">
                    <!--Don't add space between this spans. It'll make problem on copying code-->
                    <span id="webhook_listener_link"><?php echo get_uri("webhooks_listener/bitbucket") . "/" . get_setting("enable_bitbucket_commit_logs_in_tasks"); ?></span><span id="reset-key" class="fa fa-refresh p10 ml15 clickable"></span>
                </div>
            </div>

            <div class="form-group clearfix">
                <div class="col-md-12">
                    <i class="fa fa-warning text-warning"></i>
                    <span><?php echo lang("bitbucket_info_text"); ?></span>
                </div>
            </div>
        </div>

    </div>

    <div class="panel-footer">
        <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
    </div>
    <?php echo form_close(); ?>
</div>


<script type="text/javascript">
    $(document).ready(function () {

        var url = "<?php echo get_uri('webhooks_listener/bitbucket'); ?>",
                $enableBitbucket = $("#enable_bitbucket_commit_logs_in_tasks"),
                $bitbucketDetailsArea = $(".bitbucket-details-area");

        //for security purpose, add random string at the end of webhook listener link
        var setUrl = function () {
            var randomString = getRandomAlphabet(20);
            $("#enable_bitbucket_commit_logs_in_tasks").val(randomString);
            $("#webhook_listener_link").html(url + "/" + randomString);
        };

        $("#bitbucket-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

        //show/hide bitbucket details area
        $enableBitbucket.click(function () {
            if ($(this).is(":checked")) {
                $bitbucketDetailsArea.removeClass("hide");
            } else {
                $bitbucketDetailsArea.addClass("hide");
            }
        });

        //prepare url at first time
        if (!$enableBitbucket.val()) {
            setUrl();
        }

        //reset url
        $("#reset-key").click(function () {
            setUrl();
        });

    });
</script>