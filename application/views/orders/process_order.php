<div id="page-content" class="p20 clearfix">
    <?php if ($cart_items_count) { ?>
        <div class="process-order-preview">
            <div class="panel panel-default">

                <?php echo form_open(get_uri("orders/place_order"), array("id" => "place-order-form", "class" => "general-form", "role" => "form")); ?>

                <div class="page-title clearfix">
                    <h1> <?php echo lang('process_order'); ?></h1>
                </div>

                <div class="p20">
                    
                    <div class="mb20 ml15 mr15"><?php echo lang("process_order_info_message"); ?></div>
                    
                    <div class="m15 pb15 mb30">
                        <div class="table-responsive">
                            <table id="order-item-table" class="display mt0" width="100%">            
                            </table>
                        </div>
                        <div class="clearfix">
                            <div class="col-sm-8">

                            </div>
                            <div class="pull-right" id="order-total-section">
                                <?php $this->load->view("orders/processing_order_total_section"); ?>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($clients_dropdown) && $clients_dropdown) { ?>
                        <div class="form-group mt15 clearfix">
                            <div class="col-md-12">
                                <?php
                                echo form_dropdown("client_id", $clients_dropdown, array(), "class='select2 validate-hidden' id='client_id' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                                ?>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="form-group clearfix">
                        <div class=" col-md-12">
                            <?php
                            echo form_textarea(array(
                                "id" => "order_note",
                                "name" => "order_note",
                                "class" => "form-control",
                                "placeholder" => lang('note'),
                                "data-rich-text-editor" => true
                            ));
                            ?>
                        </div>
                    </div>
                </div>

                <div class="panel-footer clearfix">
                    <button type="submit" class="btn btn-primary pull-right ml10"><span class="fa fa-check-circle"></span> <?php echo lang('place_order'); ?></button>
                    <?php echo anchor(get_uri("items/grid_view"), "<i class='fa fa-plus-circle'></i> " . lang('add_more_items'), array("class" => "btn btn-default pull-right")); ?> 
                </div>

                <?php echo form_close(); ?>

            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function () {
                $("#place-order-form").appForm({
                    isModal: false,
                    onSubmit: function () {
                        appLoader.show();
                        $("#place-order-form").find('[type="submit"]').attr('disabled', 'disabled');
                    },
                    onSuccess: function (result) {
                        appLoader.hide();
                        window.location = result.redirect_to;
                    }
                });

                $("#client_id").select2();

                $("#order-item-table").appTable({
                    source: '<?php echo_uri("orders/item_list_data_of_login_user") ?>',
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

    <?php } else { ?>
        <div class="text-center box" style="height: 400px;">
            <div class="box-content" style="vertical-align: middle"> 
                <span class="fa fa-shopping-basket" style="font-size: 1400%; color:#d8d8d8"></span>
                <div class="mt15"><?php echo lang("no_items_text"); ?></div>
            </div>
        </div>  
    <?php } ?>

</div>