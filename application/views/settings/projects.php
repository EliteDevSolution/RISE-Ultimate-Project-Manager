<div id="page-content" class="p20 clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "projects";
            $this->load->view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="panel panel-default">
                <div class="page-title clearfix">
                    <h4> <?php echo lang('projects'); ?></h4>
                </div>

                <?php echo form_open(get_uri("settings/save_projects_settings"), array("id" => "project-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="project_tab_order" class=" col-md-2"><?php echo lang('set_project_tab_order'); ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "project_tab_order",
                                "name" => "project_tab_order",
                                "value" => get_setting("project_tab_order"),
                                "class" => "form-control",
                                "placeholder" => lang('project_tab_order')
                            ));
                            ?>
                            <span class="mt10 inline-block text-off"><i class="fa fa-info-circle"></i> <?php echo lang("project_tab_order_help_message"); ?></span> 
                        </div>
                    </div>

                </div>

                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#project-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

        $("#project_tab_order").select2({
            multiple: true,
            data: <?php echo ($project_tabs_dropdown); ?>
        });

        $('[data-toggle="tooltip"]').tooltip();
    });
</script>