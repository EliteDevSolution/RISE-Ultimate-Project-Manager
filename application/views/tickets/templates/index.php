<div id="page-content" class="p20 pb0 clearfix">

    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo lang("tickets"); ?></h4></li>

        <?php $this->load->view("tickets/index", array("active_tab" => "ticket_templates")); ?>

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("tickets/ticket_template_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_template'), array("class" => "btn btn-default", "title" => lang('add_template'))); ?>
            </div>
        </div>

    </ul>

    <div class="panel panel-default">
        <div class="table-responsive">
            <table id="ticket-template-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#ticket-template-table").appTable({
            source: '<?php echo_uri("tickets/ticket_template_list_data") ?>',
            order: [[0, 'desc']],
            columns: [
                {title: '<?php echo lang("title"); ?>', "class": "w300"},
                {title: '<?php echo lang("description") ?>'},
                {title: '<?php echo lang("category") ?>', "class": "w150"},
                {title: '<?php echo lang("private") ?>', "class": "w100"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>