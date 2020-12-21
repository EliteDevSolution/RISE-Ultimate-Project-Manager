<div class="panel">
    <div class="tab-title clearfix">
        <h4><?php echo lang('expenses'); ?></h4>
        <div class="title-button-group">
            <?php echo modal_anchor(get_uri("expenses/modal_form"), "<i class='fa fa-plus-cirle'></i>" . lang('add_expense'), array("class" => "btn btn-default", "data-post-client_id" => $client_id, "title" => lang('add_expense'))); ?>
        </div>
    </div>
    <div class="table-responsive">
        <table id="expense-table" class="display" width="100%">
        </table>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#expense-table").appTable({
            source: '<?php echo_uri("expenses/expense_list_data_of_client/" . $client_id) ?>',
            order: [[0, "desc"]],
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo lang("date") ?>', "iDataSort": 0},
                {title: '<?php echo lang("category") ?>'},
                {title: '<?php echo lang("title") ?>'},
                {title: '<?php echo lang("description") ?>'},
                {title: '<?php echo lang("files") ?>'},
                {title: '<?php echo lang("amount") ?>', "class": "text-right"},
                {title: '<?php echo lang("tax") ?>', "class": "text-right"},
                {title: '<?php echo lang("second_tax") ?>', "class": "text-right"},
                {title: '<?php echo lang("total") ?>', "class": "text-right"},
                {visible: false, searchable: false}
            ],
            summation: [{column: 6, dataType: 'currency'}, {column: 7, dataType: 'currency'}, {column: 8, dataType: 'currency'}, {column: 9, dataType: 'currency'}]
        });
    });
</script>