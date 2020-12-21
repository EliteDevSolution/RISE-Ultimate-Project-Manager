<div id="page-content" class="clearfix p20">

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1> <?php echo lang('orders'); ?></h1>
            <div class="title-button-group">
                <?php echo anchor(get_uri("items/grid_view"), "<i class='fa fa-plus-circle'></i> " . lang('add_order'), array("class" => "btn btn-default", "id" => "add-order-btn")); ?> 
            </div>
        </div>
        <div class="table-responsive">
            <table id="order-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        var currencySymbol = "<?php echo $client_info->currency_symbol; ?>";
        $("#order-table").appTable({
            source: '<?php echo_uri("orders/order_list_data_of_client/" . $client_id) ?>',
            order: [[0, "desc"]],
            columns: [
                {title: "<?php echo lang("order") ?>", "class": "w20p"},
                {visible: false, searchable: false},
                {visible: false, searchable: false},
                {title: "<?php echo lang("order_date") ?>", "iDataSort": 2, "class": "w20p"},
                {title: "<?php echo lang("amount") ?>", "class": "text-right w20p"},
                {title: "<?php echo lang("status") ?>", "class": "text-center w20p"}
<?php echo $custom_field_headers; ?>,
                {visible: false}
            ],
            summation: [{column: 4, dataType: 'currency', currencySymbol: currencySymbol}]
        });
    });
</script>