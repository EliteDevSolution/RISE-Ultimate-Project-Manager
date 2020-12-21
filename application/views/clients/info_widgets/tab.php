<?php
$panel = "";
$icon = "";
$value = "";
$link = "";

$view_type = "client_dashboard";
if ($this->login_user->user_type == "staff") {
    $view_type = "";
}

if (!is_object($client_info)) {
    $client_info = new stdClass();
}


if ($tab == "projects") {
    $panel = "panel-sky";
    $icon = "fa-th-large";
    if (property_exists($client_info, "total_projects")) {
        $value = to_decimal_format($client_info->total_projects);
    }
    if ($view_type == "client_dashboard") {
        $link = get_uri('projects/index');
    } else {
        $link = get_uri('clients/view/' . $client_info->id . '/projects');
    }
} else if ($tab == "invoice_value") {
    $panel = "panel-primary";
    $icon = "fa-file-text";
    if (property_exists($client_info, "invoice_value")) {
        $value = to_currency($client_info->invoice_value, $client_info->currency_symbol);
    }
    if ($view_type == "client_dashboard") {
        $link = get_uri('invoices/index');
    } else {
        $link = get_uri('clients/view/' . $client_info->id . '/invoices');
    }
} else if ($tab == "payments") {
    $panel = "panel-success";
    $icon = "fa-check-square";
    if (property_exists($client_info, "payment_received")) {
        $value = to_currency($client_info->payment_received, $client_info->currency_symbol);
    }
    if ($view_type == "client_dashboard") {
        $link = get_uri('invoice_payments/index');
    } else {
        $link = get_uri('clients/view/' . $client_info->id . '/payments');
    }
} else if ($tab == "due") {
    $panel = "panel-coral";
    $icon = "fa-money";
    if (property_exists($client_info, "invoice_value")) {
        $value = to_currency(ignor_minor_value($client_info->invoice_value - $client_info->payment_received), $client_info->currency_symbol);
    }
    if ($view_type == "client_dashboard") {
        $link = get_uri('invoices/index');
    } else {
        $link = get_uri('clients/view/' . $client_info->id . '/invoices');
    }
}
?>

<div class="panel <?php echo $panel ?>">
    <a href="<?php echo $link; ?>" class="white-link">
        <div class="panel-body ">
            <div class="widget-icon">
                <i class="fa <?php echo $icon; ?>"></i>
            </div>
            <div class="widget-details">
                <h1><?php echo $value; ?></h1>
                <?php echo lang($tab); ?>
            </div>
        </div>
    </a>
</div>