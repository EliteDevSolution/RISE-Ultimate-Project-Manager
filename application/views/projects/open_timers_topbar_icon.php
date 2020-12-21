<li class="hidden-xs">
    <?php echo ajax_anchor(get_uri("projects/show_my_open_timers/"), "&nbsp;", array("class" => "dropdown-toggle animated-clock", "id" => "project-timer-icon", "data-toggle" => "dropdown", "data-real-target" => "#my-open-timers-container")); ?>
    <div class="dropdown-menu aside-xl m0 p0 w300">
        <div id="my-open-timers-container" class="dropdown-details panel bg-white m0">
            <div class="list-group">
                <span class="list-group-item inline-loader p20"></span>                          
            </div>
        </div>
    </div>
</li>