<div class="row">
    <div class="col-md-4 col-sm-4 col-sm-push-8">
        <div class="clearfix">
            <div  class="col-md-12 mb15 task-title-right hide">
                <strong><?php echo $model_info->title; ?></strong>
            </div>

            <div class="media m0 bg-white pl15">
                <div class="media-left">
                    <span class="avatar avatar-sm">
                        <img id="task-assigned-to-avatar" src="<?php echo get_avatar($model_info->assigned_to_avatar); ?>" alt="..." />
                    </span>
                </div>
                <div class="media-body w100p pt5">
                    <div class="media-heading m0">
                        <?php echo get_update_task_info_anchor_data($model_info, "user", $can_edit_tasks, "", $show_assign_to_dropdown); ?>
                    </div>
                    <p> 
                        <span class='label label-light mr5' title='Point'><?php echo get_update_task_info_anchor_data($model_info, "points", $can_edit_tasks); ?></span>

                        <?php
                        echo "<span class='label' style='background:$model_info->status_color; '>" . get_update_task_info_anchor_data($model_info, "status", $can_edit_tasks) . "</span>";
                        ?>
                    </p>
                </div>
            </div>

            <div class="col-md-12 mb15 mt15">
                <strong><?php echo lang('milestone') . ": "; ?></strong> <?php echo get_update_task_info_anchor_data($model_info, "milestone", $can_edit_tasks); ?>
            </div>

            <div class="col-md-12 mb15">
                <strong><?php echo lang('start_date') . ": "; ?></strong> <?php echo get_update_task_info_anchor_data($model_info, "start_date", $can_edit_tasks); ?>
            </div>

            <div class="col-md-12 mb15">
                <strong><?php echo lang('deadline') . ": "; ?></strong> <?php echo get_update_task_info_anchor_data($model_info, "deadline", $can_edit_tasks); ?>
                <?php if ($model_info->deadline_milestone_title) { ?>
                    <span class="help task-deadline-milestone-tooltip" data-toggle="tooltip" title="<?php echo lang('milestone') . ": " . $model_info->deadline_milestone_title; ?>"><i class="fa fa-question-circle"></i></span>
                <?php } ?>
            </div>

            <div class="col-md-12 mb15">
                <strong><?php echo lang('label') . ": " ?></strong><?php echo get_update_task_info_anchor_data($model_info, "labels", $can_edit_tasks, $labels); ?>
            </div>

            <div class="col-md-12 mb15">
                <strong><?php echo lang('collaborators') . ": "; ?> </strong>
                <div class="mt5">
                    <?php echo get_update_task_info_anchor_data($model_info, "collaborators", $can_edit_tasks, $collaborators); ?>
                </div>
            </div>

            <?php if ($model_info->ticket_id != "0") { ?>
                <div class="col-md-12 mb15">
                    <strong><?php echo lang("ticket") . ": "; ?> </strong> <?php echo anchor(get_uri("tickets/view/" . $model_info->ticket_id), get_ticket_id($model_info->ticket_id) . " - " . $model_info->ticket_title); ?>
                </div>
            <?php } ?>

            <?php if ($model_info->recurring_task_id) { ?>
                <div class="col-md-12 mb15">
                    <strong><?php echo lang('created_from') . ": "; ?> </strong> 
                    <?php
                    echo modal_anchor(get_uri("projects/task_view"), lang("task") . " " . $model_info->recurring_task_id, array("title" => lang('task_info') . " #$model_info->recurring_task_id", "data-post-id" => $model_info->recurring_task_id, "data-modal-lg" => "1"));
                    ?>
                </div>
            <?php } ?>

            <!--recurring info-->
            <?php if ($model_info->recurring) { ?>

                <?php
                $recurring_stopped = false;
                $recurring_cycle_class = "";
                if ($model_info->no_of_cycles_completed > 0 && $model_info->no_of_cycles_completed == $model_info->no_of_cycles) {
                    $recurring_stopped = true;
                    $recurring_cycle_class = "text-danger";
                }
                ?>

                <?php
                $cycles = $model_info->no_of_cycles_completed . "/" . $model_info->no_of_cycles;
                if (!$model_info->no_of_cycles) { //if not no of cycles, so it's infinity
                    $cycles = $model_info->no_of_cycles_completed . "/&#8734;";
                }
                ?>

                <div class="col-md-12 mb15">
                    <strong><?php echo lang("repeat_every") . ": "; ?> </strong> <?php echo $model_info->repeat_every . " " . lang("interval_" . $model_info->repeat_type); ?>
                </div>

                <div class="col-md-12 mb15">
                    <strong><?php echo lang("cycles") . ": "; ?> </strong> <span class="<?php echo $recurring_cycle_class; ?>"><?php echo $cycles; ?></span>
                </div>

                <?php if (!$recurring_stopped && (int) $model_info->next_recurring_date) { ?>
                    <div class="col-md-12 mb15">
                        <strong><?php echo lang("next_recurring_date") . ": "; ?> </strong> <?php echo format_to_date($model_info->next_recurring_date, false); ?>
                    </div>
                <?php } ?>

            <?php } ?>

            <div class="col-md-12 mb15">
                <?php
                if ($show_timer) {
                    $this->load->view("projects/tasks/task_timer");
                }
                ?> 
            </div>

            <?php if (get_setting("module_project_timesheet") == "1") { ?>
                <div class="col-md-12 mb15">
                    <strong><?php echo lang("total_time_logged") . ": "; ?></strong>
                    <?php
                    echo ajax_anchor(get_uri("projects/task_timesheet/" . $model_info->id . "/" . $model_info->project_id), $total_task_hours, array("data-real-target" => "#task-timesheet", "class" => "strong"));
                    ?>
                </div>
                <div class="col-md-12 mb15">
                    <div id="task-timesheet"></div>
                </div>

            <?php } ?>

        </div>
    </div>
    <div class="col-md-8 col-sm-8 col-sm-pull-4 mb15">
        <div class="clearfix">
            <div  class="col-md-12 mb15 task-title-left">
                <strong><?php echo $model_info->title; ?></strong>
            </div>

            <?php if ($model_info->description) { ?>
                <div class="col-md-12 mb15">
                    <?php echo nl2br(link_it($model_info->description)); ?>
                </div>
            <?php } ?>

            <div class="col-md-12 mb15">
                <strong><?php echo lang('project') . ": "; ?> </strong> <?php echo anchor(get_uri("projects/view/" . $model_info->project_id), $model_info->project_title); ?>
            </div>

            <?php
            if (count($custom_fields_list)) {
                foreach ($custom_fields_list as $data) {
                    if ($data->value) {
                        ?>
                        <div class="col-md-12 mb15">
                            <strong><?php echo $data->title . ": "; ?> </strong> <?php echo $this->load->view("custom_fields/output_" . $data->field_type, array("value" => $data->value), true); ?>
                        </div>
                        <?php
                    }
                }
            }
            ?>

            <!--checklist-->
            <?php echo form_open(get_uri("projects/save_checklist_item"), array("id" => "checklist_form", "class" => "general-form", "role" => "form")); ?>
            <div class="col-md-12 mb15 b-t">
                <div class="pb10 pt10">
                    <strong><?php echo lang("checklist"); ?></strong>
                </div>
                <input type="hidden" name="task_id" value="<?php echo $task_id; ?>" />
                <div class="checklist-items" id="checklist-items">

                </div>
                <?php if ($can_edit_tasks) { ?>
                    <div class="form-group">
                        <div class="mt5 p0">
                            <?php
                            echo form_input(array(
                                "id" => "checklist-add-item",
                                "name" => "checklist-add-item",
                                "class" => "form-control",
                                "placeholder" => lang('add_item'),
                                "data-rule-required" => true,
                                "data-msg-required" => lang("field_required")
                            ));
                            ?>
                        </div>
                    </div>
                    <div id="checklist-options-panel" class="mb15 p0 hide">
                        <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('add'); ?></button> 
                        <button id="checklist-options-panel-close" type="button" class="btn btn-default"><span class="fa fa-close"></span> <?php echo lang('cancel'); ?></button>
                    </div>
                <?php } ?>
            </div>
            <?php echo form_close(); ?>

            <!--Sub tasks-->
            <?php echo form_open(get_uri("projects/save_sub_task"), array("id" => "sub_task_form", "class" => "general-form", "role" => "form")); ?>
            <div class="col-md-12 mb15 b-t">
                <div class="pb10 pt10">
                    <strong><?php echo lang("sub_tasks"); ?></strong>
                </div>
                <input type="hidden" name="project_id" value="<?php echo $model_info->project_id; ?>" />
                <input type="hidden" name="parent_task_id" value="<?php echo $task_id; ?>" />
                <input type="hidden" name="milestone_id" value="<?php echo $model_info->milestone_id; ?>" />

                <div class="checklist-items" id="sub-tasks">

                </div>
                <?php if ($can_create_tasks) { ?>
                    <div class="form-group">
                        <div class="mt5 col-md-12 p0">
                            <?php
                            echo form_input(array(
                                "id" => "sub-task-title",
                                "name" => "sub-task-title",
                                "class" => "form-control",
                                "placeholder" => lang('create_a_sub_task'),
                                "data-rule-required" => true,
                                "data-msg-required" => lang("field_required")
                            ));
                            ?>
                        </div>
                    </div>
                    <div id="sub-task-options-panel" class="col-md-12 mb15 p0 hide">
                        <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('create'); ?></button> 
                        <button id="sub-task-options-panel-close" type="button" class="btn btn-default"><span class="fa fa-close"></span> <?php echo lang('cancel'); ?></button>
                    </div>
                <?php } ?>
            </div>
            <?php echo form_close(); ?>

            <!--Task dependency-->
            <?php if ($can_edit_tasks) { ?>
                <div class="col-md-12 mb15">
                    <span class="dropdown">
                        <button class="btn btn-default dropdown-toggle btn-border" type="button" data-toggle="dropdown" aria-expanded="true">
                            <i class='fa fa-random'></i> <?php echo lang('add_dependency'); ?>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li role="presentation"><?php echo js_anchor(lang("this_task_blocked_by"), array("class" => "add-dependency-btn", "data-dependency_type" => "blocked_by")); ?></li>
                            <li role="presentation"><?php echo js_anchor(lang("this_task_blocking"), array("class" => "add-dependency-btn", "data-dependency_type" => "blocking")); ?></li>
                        </ul>
                    </span>
                </div>
            <?php } ?>

            <div class="col-md-12 mb15 <?php echo ($blocked_by || $blocking) ? "" : "hide"; ?>" id="dependency-area">
                <div class="pb10 pt10">
                    <strong><?php echo lang("dependency"); ?></strong>
                </div>

                <div class="p10 list-group-item mb15 <?php echo $blocked_by ? "" : "hide"; ?>" id="blocked-by-area">
                    <div class="pb10"><strong><?php echo lang("blocked_by"); ?></strong></div>
                    <div id="blocked-by-tasks"><?php echo $blocked_by; ?></div>
                </div>

                <div class="p10 list-group-item mb15 <?php echo $blocking ? "" : "hide"; ?>" id="blocking-area">
                    <div class="pb10"><strong><?php echo lang("blocking"); ?></strong></div>
                    <div id="blocking-tasks"><?php echo $blocking; ?></div>
                </div>

                <?php echo form_open(get_uri("projects/save_dependency_tasks"), array("id" => "dependency_tasks_form", "class" => "general-form hide", "role" => "form")); ?>

                <input type="hidden" name="task_id" value="<?php echo $task_id; ?>" />

                <div class="form-group mb0">
                    <div class="mt5 col-md-12 p0">
                        <?php
                        echo form_input(array(
                            "id" => "dependency_task",
                            "name" => "dependency_task",
                            "class" => "form-control validate-hidden",
                            "placeholder" => lang('tasks'),
                            "data-rule-required" => true,
                            "data-msg-required" => lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>

                <div class="p0 mt10">
                    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('add'); ?></button> 
                    <button type="button" class="dependency-tasks-close btn btn-default"><span class="fa fa-close"></span> <?php echo lang('cancel'); ?></button>
                </div>

                <?php echo form_close(); ?>
            </div>

            <!--Task comment section-->
            <div class="row clearfix">
                <div class="col-md-12 b-t pt10 list-container">
                    <?php if ($can_comment_on_tasks) { ?>
                        <?php $this->load->view("projects/comments/comment_form"); ?>
                    <?php } ?>
                    <?php $this->load->view("projects/comments/comment_list"); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($this->login_user->user_type === "staff") { ?>
    <div class="box-title"><span ><?php echo lang("activity"); ?></span></div>
    <div class="pl15 pr15 mt15 list-container project-activity-logs-container">
        <?php activity_logs_widget(array("limit" => 20, "offset" => 0, "log_type" => "task", "log_type_id" => $model_info->id)); ?>
    </div>
<?php } ?>