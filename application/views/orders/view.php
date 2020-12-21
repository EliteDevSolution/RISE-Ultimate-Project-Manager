<div id="page-content" class="clearfix">
    <div style="max-width: 1000px; margin: auto;">
        <div class="page-title clearfix mt15">
            <h1><?php echo get_order_id($order_info->id); ?></h1>
            <div class="title-button-group">
                <span class="dropdown inline-block">
                    <button class="btn btn-info dropdown-toggle  mt0 mb0" type="button" data-toggle="dropdown" aria-expanded="true">
                        <i class='fa fa-cogs'></i> <?php echo lang('actions'); ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li role="presentation"><?php echo anchor(get_uri("orders/download_pdf/" . $order_info->id), "<i class='fa fa-download'></i> " . lang('download_pdf'), array("title" => lang('download_pdf'),)); ?> </li>
                        <li role="presentation"><?php echo anchor(get_uri("orders/download_pdf/" . $order_info->id . "/view"), "<i class='fa fa-file-pdf-o'></i> " . lang('view_pdf'), array("title" => lang('view_pdf'), "target" => "_blank")); ?> </li>
                        <li role="presentation"><?php echo anchor(get_uri("orders/preview/" . $order_info->id . "/1"), "<i class='fa fa-search'></i> " . lang('order_preview'), array("title" => lang('order_preview')), array("target" => "_blank")); ?> </li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><?php echo modal_anchor(get_uri("orders/modal_form"), "<i class='fa fa-edit'></i> " . lang('edit_order'), array("title" => lang('edit_order'), "data-post-id" => $order_info->id, "role" => "menuitem", "tabindex" => "-1")); ?> </li>

                        <li role="presentation" class="divider"></li>
                        <?php if ($show_estimate_option) { ?>
                            <li role="presentation"><?php echo modal_anchor(get_uri("estimates/modal_form/"), "<i class='fa fa-file'></i> " . lang('create_estimate'), array("title" => lang("create_estimate"), "data-post-order_id" => $order_info->id)); ?> </li>
                        <?php } ?>
                        <?php if ($show_invoice_option) { ?>
                            <li role="presentation"><?php echo modal_anchor(get_uri("invoices/modal_form/"), "<i class='fa fa-file-text'></i> " . lang('create_invoice'), array("title" => lang("create_invoice"), "data-post-order_id" => $order_info->id)); ?> </li>
                        <?php } ?>

                    </ul>
                </span>
                <?php echo modal_anchor(get_uri("orders/item_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_item'), array("class" => "btn btn-default", "title" => lang('add_item'), "data-post-order_id" => $order_info->id)); ?>
            </div>
        </div>
        <div id="order-status-bar">
            <?php $this->load->view("orders/order_status_bar"); ?>
        </div>
        <div class="mt15">
            <div class="panel panel-default p15 b-t">
                <div class="clearfix p20">
                    <!-- small font size is required to generate the pdf, overwrite that for screen -->
                    <style type="text/css"> .invoice-meta {font-size: 100% !important;}</style>

                    <?php
                    $color = get_setting("order_color");
                    if (!$color) {
                        $color = get_setting("invoice_color");
                    }
                    $style = get_setting("invoice_style");
                    ?>
                    <?php
                    $data = array(
                        "client_info" => $client_info,
                        "color" => $color ? $color : "#2AA384",
                        "order_info" => $order_info
                    );
                    if ($style === "style_2") {
                        $this->load->view('orders/order_parts/header_style_2.php', $data);
                    } else {
                        $this->load->view('orders/order_parts/header_style_1.php', $data);
                    }
                    ?>

                </div>

                <div class="table-responsive mt15 pl15 pr15">
                    <table id="order-item-table" class="display" width="100%">            
                    </table>
                </div>

                <div class="clearfix">
                    <div class="col-sm-8">

                    </div>
                    <div class="pull-right pr15" id="order-total-section">
                        <?php $this->load->view("orders/order_total_section"); ?>
                    </div>
                </div>

                <p class="b-t b-info pt10 m15"><?php echo nl2br($order_info->note); ?></p>

            </div>
        </div>

    </div>
</div>



<script type="text/javascript">
    //RELOAD_VIEW_AFTER_UPDATE = true;
    $(document).ready(function () {
        $("#order-item-table").appTable({
            source: '<?php echo_uri("orders/item_list_data/" . $order_info->id . "/") ?>',
            order: [[0, "asc"]],
            hideTools: true,
            displayLength: 100,
            columns: [
                {visible: false, searchable: false},
                {title: "<?php echo lang("item") ?> ", "bSortable": false},
                {title: "<?php echo lang("quantity") ?>", "class": "text-right w15p", "bSortable": false},
                {title: "<?php echo lang("rate") ?>", "class": "text-right w15p", "bSortable": false},
                {title: "<?php echo lang("total") ?>", "class": "text-right w15p", "bSortable": false},
                {title: "<i class='fa fa-bars'></i>", "class": "text-center option w100", "bSortable": false}
            ],

            onInitComplete: function () {
                //apply sortable
                $("#order-item-table").find("tbody").attr("id", "order-item-table-sortable");
                var $selector = $("#order-item-table-sortable");

                Sortable.create($selector[0], {
                    animation: 150,
                    chosenClass: "sortable-chosen",
                    ghostClass: "sortable-ghost",
                    onUpdate: function (e) {
                        appLoader.show();
                        //prepare sort indexes 
                        var data = "";
                        $.each($selector.find(".item-row"), function (index, ele) {
                            if (data) {
                                data += ",";
                            }

                            data += $(ele).attr("data-id") + "-" + index;
                        });

                        //update sort indexes
                        $.ajax({
                            url: '<?php echo_uri("orders/update_item_sort_values") ?>',
                            type: "POST",
                            data: {sort_values: data},
                            success: function () {
                                appLoader.hide();
                            }
                        });
                    }
                });

            },

            onDeleteSuccess: function (result) {
                $("#order-total-section").html(result.order_total_view);
            },
            onUndoSuccess: function (result) {
                $("#order-total-section").html(result.order_total_view);
            }
        });
    });

</script>

<?php
//required to send email 

load_css(array(
    "assets/js/summernote/summernote.css",
));
load_js(array(
    "assets/js/summernote/summernote.min.js",
));
?>

<?php $this->load->view("orders/update_order_status_script", array("details_view" => true)); ?>