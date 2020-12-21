<?php
$panel = "";
$link = "";
if ($status == "new") {
    $panel = "orange";
    $link = get_uri("tickets/index");
} else if ($status == "open") {
    $panel = "coral";
    $link = get_uri("tickets/index");
} else if ($status == "closed") {
    $panel = "success";
    $link = get_uri("tickets/index/closed");
}
?>

<div class="panel panel-<?php echo $panel; ?>">
    <a href="<?php echo $link; ?>" class="white-link">
        <div class="panel-body">
            <div class="widget-icon">
                <i class="fa fa-support"></i>
            </div>
            <div class="widget-details">
                <h1><?php echo $total_tickets; ?></h1>
                <?php echo lang($status . "_tickets"); ?>
            </div>
        </div>
    </a>
</div>