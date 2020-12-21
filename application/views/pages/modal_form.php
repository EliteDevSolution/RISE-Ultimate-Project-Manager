<?php echo form_open(get_uri("pages/save"), array("id" => "add-page-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
    <div class="form-group">
        <label for="title" class=" col-md-2"><?php echo lang('title'); ?></label>
        <div class=" col-md-10">
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
        <label for="page_content" class=" col-md-2"><?php echo lang('content'); ?></label>
        <div class=" col-md-10">
            <?php
            echo form_textarea(array(
                "id" => "page_content",
                "name" => "content",
                "value" => $model_info->content,
                "class" => "form-control"
            ));
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="slug" class=" col-md-2"><?php echo lang("slug"); ?></label>
        <div class=" col-md-10">
            <?php
            echo form_input(array(
                "id" => "slug",
                "name" => "slug",
                "value" => $model_info->slug,
                "class" => "form-control",
                "placeholder" => get_uri("about") . "/[" . strtolower(lang("slug")) . "]",
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
            ));
            ?>
        </div>            
    </div>
    <div class="form-group">
        <label for="status" class=" col-md-2"><?php echo lang('status'); ?></label>
        <div class="col-md-10">
            <?php
            $status_dropdown = array("active" => lang("active"), "inactive" => lang("inactive"));
            echo form_dropdown("status", $status_dropdown, $model_info->status, "class='select2'");
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="internal_use_only" class=" col-md-4"><?php echo lang('internal_use_only'); ?></label>
        <div class=" col-md-8">
            <?php
            echo form_checkbox("internal_use_only", "1", $model_info->internal_use_only ? true : false, "id='internal_use_only'");
            ?>                       
        </div>
    </div> 
    <div class="<?php echo $model_info->internal_use_only ? "" : "hide"; ?>" id="page-visible-to-area">
        <div class="form-group <?php echo $model_info->visible_to_clients_only ? "hide" : ""; ?>" id="visible_to_team_members_only_area">
            <label for="visible_to_team_members_only" class=" col-md-4"><?php echo lang('visible_to_team_members_only'); ?></label>
            <div class=" col-md-8">
                <?php
                echo form_checkbox("visible_to_team_members_only", "1", $model_info->visible_to_team_members_only ? true : false, "id='visible_to_team_members_only'");
                ?>                       
            </div>
        </div> 
        <div class="form-group <?php echo $model_info->visible_to_team_members_only ? "hide" : ""; ?>" id="visible_to_clients_only_area">
            <label for="visible_to_clients_only" class=" col-md-4"><?php echo lang('visible_to_clients_only'); ?></label>
            <div class=" col-md-8">
                <?php
                echo form_checkbox("visible_to_clients_only", "1", $model_info->visible_to_clients_only ? true : false, "id='visible_to_clients_only'");
                ?>                       
            </div>
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
        $("#add-page-form").appForm({
            beforeAjaxSubmit: function (data) {
                $.each(data, function (index, obj) {
                    if (obj.name === "content") {
                        data[index]["value"] = encodeAjaxPostData(getWYSIWYGEditorHTML("#page_content"));
                    }
                });
            },
            onSuccess: function (result) {
                $("#pages-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        initWYSIWYGEditor("#page_content", {height: 150});
        $("#title").focus();
        $("#add-page-form .select2").select2();

        //show/hide visible to details area
        $("#internal_use_only").click(function () {
            if ($(this).is(":checked")) {
                $("#page-visible-to-area").removeClass("hide");
            } else {
                $("#page-visible-to-area").addClass("hide");
            }
        });

        $("#visible_to_team_members_only").click(function () {
            if ($(this).is(":checked")) {
                $("#visible_to_clients_only_area").addClass("hide");
            } else {
                $("#visible_to_clients_only_area").removeClass("hide");
            }
        });

        $("#visible_to_clients_only").click(function () {
            if ($(this).is(":checked")) {
                $("#visible_to_team_members_only_area").addClass("hide");
            } else {
                $("#visible_to_team_members_only_area").removeClass("hide");
            }
        });
    });
</script>    