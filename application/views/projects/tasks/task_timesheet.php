<?php foreach ($task_timesheet as $timesheet) { ?>
    <div class="media">
        <div class="media-left">
            <span class="avatar avatar-xs">
                <img src="<?php echo get_avatar($timesheet->logged_by_avatar); ?>" alt="..." />
            </span>
        </div>
        <div class="media-body">
            <div class="pull-left">
                <div><?php echo format_to_date($timesheet->start_time); ?></div>
                <?php if (!$timesheet->hours) { ?>
                    <small><?php echo format_to_time($timesheet->start_time) . " to " . format_to_time($timesheet->end_time); ?></small>
                <?php } ?>
            </div>
            <div class="pull-right">
                <strong><?php echo convert_seconds_to_time_format($timesheet->hours ? (round(($timesheet->hours * 60), 0) * 60) : abs(strtotime($timesheet->end_time) - strtotime($timesheet->start_time))) ?></strong>
            </div>
        </div>
    </div>
<?php } ?>
