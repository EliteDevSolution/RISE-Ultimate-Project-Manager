<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-user"></i>&nbsp; <?php echo lang("active_members_on_projects"); ?>
    </div>
    <div class="panel-body active-team-members-list p0" id="active_members_on_projects">
        <?php
        foreach ($users_info as $user_info) {
            ?>
            <div class = "message-row">
                <div class = "media-left">
                    <span class = "avatar avatar-xs">
                        <img alt = "..." src = "<?php echo get_avatar($user_info->image); ?>">
                    </span>
                </div>

                <div class="media-body">
                    <div class="media-heading clearfix">
                        <strong class="pull-left">
                            <?php
                            echo get_team_member_profile_link($user_info->id, $user_info->member_name)
                            ?>
                        </strong>
                    </div>
                    <div>
                        <?php
                        $project_list = explode(",", $user_info->projects_list);
                        foreach ($project_list as $project) {
                            $project_timer = explode("--::--", $project);
                            $in_time = "<span class='text-off'>" . "<i class='fa fa-clock-o'></i>" . " ";
                            $in_time .= format_to_relative_time($project_timer[2]);
                            $in_time .= "</span>";
                            echo "<div class='clearfix row'>";
                            echo "<div class='col-md-7 col-sm-7'>";
                            echo "<small class='text-off block pull-left'>" . anchor(get_uri("projects/view/" . $project_timer[0]), $project_timer[1]) . "</small>";
                            echo "</div>";
                            echo "<div  class='col-md-5 col-sm-5'>";
                            echo "<span class='pull-right'>" . $in_time . "</span>";
                            echo "</div>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

    </div>
</div>

<script>
    $(document).ready(function () {
        initScrollbar('#active_members_on_projects', {
            setHeight: 330
        });
    });
</script>