<div class="panel panel-default">
    <ul id="project-timesheet-tabs" data-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo lang("timesheets"); ?></h4></li>

        <li><a id="timesheet-details-button" role="presentation" href="javascript:;" data-target="#timesheet-details"><?php echo lang("details"); ?></a></li>
        <li><a role="presentation" href="<?php echo_uri("projects/timesheet_summary/" . $project_id); ?>" data-target="#timesheet-summary"><?php echo lang('summary'); ?></a></li>
        <li><a role="presentation" href="<?php echo_uri("projects/timesheet_chart/" . $project_id); ?>" data-target="#timesheet-chart"><?php echo lang('chart'); ?></a></li>

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php
                if ($can_add_log) {
                    echo modal_anchor(get_uri("projects/timelog_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('log_time'), array("class" => "btn btn-default", "title" => lang('log_time'), "data-post-project_id" => $project_id));
                }
                ?>
            </div>
        </div>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="timesheet-details">
            <div class="table-responsive">
                <table id="project-timesheet-table" class="display" width="100%">  
                </table>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="timesheet-summary"></div>
        <div role="tabpanel" class="tab-pane fade" id="timesheet-chart"></div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        var optionVisibility = false;
        if ("<?php echo $this->login_user->user_type; ?>" == "staff") {
            optionVisibility = true;
        }


        var endTimeVisibility = true;
<?php if (get_setting("users_can_input_only_total_hours_instead_of_period")) { ?>
            endTimeVisibility = false;
<?php } ?>

        $("#project-timesheet-table").appTable({
            source: '<?php echo_uri("projects/timesheet_list_data/") ?>',
            filterParams: {project_id: "<?php echo $project_id; ?>"},
            order: [[3, "desc"]],
            filterDropdown: [{name: "user_id", class: "w200", options: <?php echo $project_members_dropdown; ?>}, {name: "task_id", class: "w200", options: <?php echo $tasks_dropdown; ?>}],
            rangeDatepicker: [{startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, showClearButton: true}],
            columns: [
                {title: "<?php echo lang('member') ?>"},
                {visible: false, searchable: false},
                {visible: false, searchable: false},
                {title: "<?php echo lang('task') ?>"},
                {visible: false, searchable: false},
                {title: "<?php echo get_setting("users_can_input_only_total_hours_instead_of_period") ? lang("date") : lang('start_time') ?>", "iDataSort": 4},
                {visible: false, searchable: false},
                {title: "<?php echo lang('end_time') ?>", "iDataSort": 6, visible: endTimeVisibility},
                {title: "<?php echo lang('total') ?>", "class": "text-right"},
                {title: '<i class="fa fa-comment"></i>', "class": "text-center w50"}
<?php echo $custom_field_headers; ?>,
                {visible: optionVisibility, title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ],
            printColumns: combineCustomFieldsColumns([0, 3, 5, 7, 8], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 3, 5, 7, 8], '<?php echo $custom_field_headers; ?>'),
            summation: [{column: 8, dataType: 'time'}]
        });
    }
    );
</script>