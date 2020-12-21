<?php

if ($disable_timer) {
    $start_timer = js_anchor("<i class='fa fa fa-clock-o'></i> " . lang('start_timer'), array('title' => lang('start_timer'), "class" => "btn btn-info", "disabled" => "true", "data-action-url" => get_uri("projects/timer/" . $project_id . "/start"), "data-reload-on-success" => "1", "data-post-task_id" => $model_info->id));
} else {
    $start_timer = ajax_anchor(get_uri("projects/timer/" . $project_id . "/start"), "<i class='fa fa fa-clock-o'></i> " . lang('start_timer'), array("class" => "btn btn-info", "title" => lang('start_timer'), "data-post-task_id" => $model_info->id, "data-real-target" => "#start-timer-btn-$model_info->id", "data-post-task_timer" => true));
}

$stop_timer = modal_anchor(get_uri("projects/stop_timer_modal_form/" . $project_id), "<i class='fa fa fa-clock-o'></i> " . lang('stop_timer'), array("class" => "btn btn-danger", "title" => lang('stop_timer'), "data-post-task_id" => $model_info->id));

if ($timer_status === "open") {
    echo $stop_timer;
} else {
    echo "<span id='start-timer-btn-$model_info->id'>" . $start_timer . "</span>";
}
?>