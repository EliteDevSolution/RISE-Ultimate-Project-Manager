<?php

$quick_filters_dropdown = array(
    array("id" => "", "text" => "- " . lang("quick_filters") . " -"),
    array("id" => "has_open_projects", "text" => lang("has_open_projects")),
    array("id" => "has_completed_projects", "text" => lang("has_completed_projects")),
    array("id" => "has_any_hold_projects", "text" => lang("has_any_hold_projects")),
    array("id" => "has_unpaid_invoices", "text" => lang("has_unpaid_invoices")),
    array("id" => "has_overdue_invoices", "text" => lang("has_overdue_invoices")),
    array("id" => "has_partially_paid_invoices", "text" => lang("has_partially_paid_invoices"))
);
echo json_encode($quick_filters_dropdown);
?>