<div id="page-content" class="p20 clearfix">
    <div class="panel clearfix">
        <ul id="order-tabs" data-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo lang('orders'); ?></h4></li>
            <li><a id="monthly-order-button" class="active" role="presentation" href="javascript:;" data-target="#monthly-orders"><?php echo lang("monthly"); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("orders/yearly/"); ?>" data-target="#yearly-orders"><?php echo lang('yearly'); ?></a></li>

            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php echo js_anchor("<i class='fa fa-plus-circle'></i> " . lang('add_order'), array("class" => "btn btn-default", "id" => "add-order-btn")); ?>           
                </div>
            </div>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="monthly-orders">
                <div class="table-responsive">
                    <table id="monthly-order-table" class="display" cellspacing="0" width="100%">   
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="yearly-orders"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    loadOrdersTable = function (selector, dateRange) {
        $(selector).appTable({
            source: '<?php echo_uri("orders/list_data") ?>',
            order: [[0, "desc"]],
            dateRangeType: dateRange,
            filterDropdown: [{name: "status_id", class: "w150", options: <?php $this->load->view("orders/order_statuses_dropdown"); ?>}],
            columns: [
                {title: "<?php echo lang("order") ?> ", "class": "w15p"},
                {title: "<?php echo lang("client") ?>"},
                {visible: false, searchable: false},
                {title: "<?php echo lang("order_date") ?>", "iDataSort": 2, "class": "w20p"},
                {title: "<?php echo lang("amount") ?>", "class": "text-right w20p"},
                {title: "<?php echo lang("status") ?>", "class": "text-center"}
<?php echo $custom_field_headers; ?>,
                {title: "<i class='fa fa-bars'></i>", "class": "text-center option w100"}
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 3, 4, 5], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 3, 4, 5], '<?php echo $custom_field_headers; ?>'),
            summation: [{column: 4, dataType: 'currency', currencySymbol: AppHelper.settings.currencySymbol}]
        });
    };

    $(document).ready(function () {
        loadOrdersTable("#monthly-order-table", "monthly");

        $("#add-order-btn").click(function () {
            window.location.href = "<?php echo get_uri("items/grid_view"); ?>";
        });
    });

</script>

<?php $this->load->view("orders/update_order_status_script"); ?>