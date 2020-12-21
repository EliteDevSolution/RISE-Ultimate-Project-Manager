<?php
if (!function_exists("make_project_tabs_data")) {

    function make_project_tabs_data($default_project_tabs = array(), $is_client = false) {
        $project_tab_order = get_setting("project_tab_order");
        $project_tab_order_of_clients = get_setting("project_tab_order_of_clients");
        $custom_project_tabs = array();

        if ($is_client && $project_tab_order_of_clients) {
            //user is client
            $custom_project_tabs = explode(',', $project_tab_order_of_clients);
        } else if (!$is_client && $project_tab_order) {
            //user is team member
            $custom_project_tabs = explode(',', $project_tab_order);
        }

        $final_projects_tabs = array();
        if ($custom_project_tabs) {
            foreach ($custom_project_tabs as $custom_project_tab) {
                if (array_key_exists($custom_project_tab, $default_project_tabs)) {
                    $final_projects_tabs[$custom_project_tab] = get_array_value($default_project_tabs, $custom_project_tab);
                }
            }
        }

        $final_projects_tabs = $final_projects_tabs ? $final_projects_tabs : $default_project_tabs;

        foreach ($final_projects_tabs as $key => $value) {
            echo "<li><a role='presentation' href='" . get_uri($value) . "' data-target='#project-$key-section'>" . lang($key) . "</a></li>";
        }
    }

}
?>

<div id="page-content" class="p20 pb0 clearfix">
    <div class="row">
        <div class="col-md-12">
            <div class="page-title clearfix">
                <h1>
                    <?php if ($project_info->status == "open") { ?>
                        <i class="fa fa-th-large" title="<?php echo lang("open"); ?>"></i>
                    <?php } else if ($project_info->status == "completed") { ?>
                        <i class="fa fa-check-circle" title="<?php echo lang("completed"); ?>"></i>
                    <?php } else if ($project_info->status == "hold") { ?>
                        <i class="fa fa-pause" title="<?php echo lang("hold"); ?>"></i> 
                    <?php } else if ($project_info->status == "canceled") { ?>
                        <i class="fa fa-times" title="<?php echo lang("canceled"); ?>"></i> 
                    <?php } ?>

                    <?php echo $project_info->title; ?>

                    <?php if (!(get_setting("disable_access_favorite_project_option_for_clients") && $this->login_user->user_type == "client")) { ?>
                        <span id="star-mark">
                            <?php
                            if ($is_starred) {
                                $this->load->view('projects/star/starred', array("project_id" => $project_info->id));
                            } else {
                                $this->load->view('projects/star/not_starred', array("project_id" => $project_info->id));
                            }
                            ?>
                        </span>
                    <?php } ?>
                </h1>

                <div class="title-button-group" id="project-timer-box">
                    <?php
                    if ($can_edit_timesheet_settings || $can_edit_slack_settings) {
                        echo modal_anchor(get_uri("projects/settings_modal_form"), "<i class='fa fa fa-cog'></i> " . lang('settings'), array("class" => "btn btn-default", "title" => lang('settings'), "data-post-project_id" => $project_info->id));
                    }
                    ?>
                    <?php if ($show_actions_dropdown) { ?>
                        <span class="dropdown inline-block">
                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="true">
                                <i class='fa fa-cogs'></i> <?php echo lang('actions'); ?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-left" role="menu">
                                <?php if ($project_info->status == "open") { ?>
                                    <li role="presentation"><?php echo ajax_anchor(get_uri("projects/change_status/$project_info->id/completed"), "<i class='fa fa-check-circle'></i> " . lang('mark_project_as_completed'), array("class" => "", "title" => lang('mark_project_as_completed'), "data-reload-on-success" => "1")); ?> </li>
                                    <li role="presentation"><?php echo ajax_anchor(get_uri("projects/change_status/$project_info->id/hold"), "<i class='fa fa-pause'></i> " . lang('mark_project_as_hold'), array("class" => "", "title" => lang('mark_project_as_hold'), "data-reload-on-success" => "1")); ?> </li>
                                    <li role="presentation"><?php echo ajax_anchor(get_uri("projects/change_status/$project_info->id/canceled"), "<i class='fa fa-times'></i> " . lang('mark_project_as_canceled'), array("class" => "", "title" => lang('mark_project_as_canceled'), "data-reload-on-success" => "1")); ?> </li>
                                <?php } ?>
                                <?php if ($project_info->status != "open") { ?>
                                    <li role="presentation"><?php echo ajax_anchor(get_uri("projects/change_status/$project_info->id/open"), "<i class='fa fa-th-large'></i> " . lang('mark_project_as_open'), array("class" => "", "title" => lang('mark_project_as_open'), "data-reload-on-success" => "1")); ?> </li>
                                    <?php
                                }
                                if ($this->login_user->user_type == "staff") {
                                    echo "<li role='presentation'>" . modal_anchor(get_uri("projects/clone_project_modal_form"), "<i class='fa fa-copy'></i> " . lang('clone_project'), array("class" => "", "data-post-id" => $project_info->id, "title" => lang('clone_project'))) . " </li>";
                                }
                                echo "<li role='presentation'>" . modal_anchor(get_uri("projects/modal_form"), "<i class='fa fa-pencil'></i> " . lang('edit_project'), array("class" => "edit", "title" => lang('edit_project'), "data-post-id" => $project_info->id)) . " </li>";
                                ?>

                            </ul>
                        </span>
                    <?php } ?>
                    <?php
                    if ($show_timmer) {
                        $this->load->view("projects/project_timer");
                    }
                    ?>
                </div>
            </div>
            <ul id="project-tabs" data-toggle="ajax-tab" class="nav nav-tabs classic" role="tablist">
                <?php
                if ($this->login_user->user_type === "staff") {
                    //default tab order
                    $project_tabs = array(
                        "overview" => "projects/overview/" . $project_info->id,
                        "tasks_list" => "projects/tasks/" . $project_info->id,
                        "tasks_kanban" => "projects/tasks_kanban/" . $project_info->id,
                    );

                    if ($show_milestone_info) {
                        $project_tabs["milestones"] = "projects/milestones/" . $project_info->id;
                    }

                    if ($show_gantt_info) {
                        $project_tabs["gantt"] = "projects/gantt/" . $project_info->id;
                    }

                    if ($show_note_info) {
                        $project_tabs["notes"] = "projects/notes/" . $project_info->id;
                    }

                    $project_tabs["files"] = "projects/files/" . $project_info->id;
                    $project_tabs["comments"] = "projects/comments/" . $project_info->id;
                    $project_tabs["customer_feedback"] = "projects/customer_feedback/" . $project_info->id;

                    if ($show_timesheet_info) {
                        $project_tabs["timesheets"] = "projects/timesheets/" . $project_info->id;
                    }

                    if ($show_invoice_info) {
                        $project_tabs["invoices"] = "projects/invoices/" . $project_info->id;
                        $project_tabs["payments"] = "projects/payments/" . $project_info->id;
                    }

                    if ($show_expense_info) {
                        $project_tabs["expenses"] = "projects/expenses/" . $project_info->id;
                    }

                    make_project_tabs_data($project_tabs);
                } else {
                    //default tab order
                    $project_tabs = array(
                        "overview" => "projects/overview_for_client/" . $project_info->id
                    );

                    if ($show_tasks) {
                        $project_tabs["tasks_list"] = "projects/tasks/" . $project_info->id;
                        $project_tabs["tasks_kanban"] = "projects/tasks_kanban/" . $project_info->id;
                    }

                    if ($show_files) {
                        $project_tabs["files"] = "projects/files/" . $project_info->id;
                    }

                    $project_tabs["comments"] = "projects/customer_feedback/" . $project_info->id;

                    if ($show_milestone_info) {
                        $project_tabs["milestones"] = "projects/milestones/" . $project_info->id;
                    }

                    if ($show_gantt_info) {
                        $project_tabs["gantt"] = "projects/gantt/" . $project_info->id;
                    }

                    if ($show_timesheet_info) {
                        $project_tabs["timesheets"] = "projects/timesheets/" . $project_info->id;
                    }

                    make_project_tabs_data($project_tabs, true);
                }
                ?>

            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade active" id="project-overview-section" style="min-height: 200px;"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-tasks_list-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-tasks_kanban-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-milestones-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-gantt-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-files-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-comments-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-customer_feedback-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-notes-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-timesheets-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-invoices-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-payments-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-expenses-section"></div>

            </div>
        </div>
    </div>
</div>


<?php
//if we get any task parameter, we'll show the task details modal automatically
$preview_task_id = get_array_value($_GET, 'task');
if ($preview_task_id) {
    echo modal_anchor(get_uri("projects/task_view"), "", array("id" => "preview_task_link", "title" => lang('task_info') . " #$preview_task_id", "data-post-id" => $preview_task_id, "data-modal-lg" => "1"));
}
?>

<?php
load_css(array(
    "assets/js/gantt-chart/frappe-gantt.css",
));
load_js(array(
    "assets/js/gantt-chart/frappe-gantt.js",
));
?>

<script type="text/javascript">
    RELOAD_PROJECT_VIEW_AFTER_UPDATE = true;

    $(document).ready(function () {
        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "comment") {
                $("[data-target='#project-comments-section']").trigger("click");
            } else if (tab === "customer_feedback") {
                $("[data-target='#project-customer_feedback-section']").trigger("click");
            } else if (tab === "files") {
                $("[data-target='#project-files-section']").trigger("click");
            } else if (tab === "gantt") {
                $("[data-target='#project-gantt-section']").trigger("click");
            } else if (tab === "tasks") {
                $("[data-target='#project-tasks_list-section']").trigger("click");
            } else if (tab === "tasks_kanban") {
                $("[data-target='#project-tasks_kanban-section']").trigger("click");
            } else if (tab === "milestones") {
                $("[data-target='#project-milestones-section']").trigger("click");
            }
        }, 210);


        //open task details modal automatically 

        if ($("#preview_task_link").length) {
            $("#preview_task_link").trigger("click");
        }

    });
</script>

<?php $this->load->view("projects/tasks/batch_update/batch_update_script"); ?>