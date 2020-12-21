<div class="panel panel-default">
    <div class="panel-heading no-border">
        <i class="fa fa-life-ring"></i>&nbsp; <?php echo lang('open_tickets'); ?>
    </div>

    <div class="table-responsiv" id="open-tickets-list-widget-table">
        <table id="ticket-table" class="display" cellspacing="0" width="100%">
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        initScrollbar('#open-tickets-list-widget-table', {
            setHeight: 330
        });

        var showOption = true,
                idColumnClass = "w70",
                titleColumnClass = "w200";

        if (isMobile()) {
            showOption = false;
            idColumnClass = "w25p";
            titleColumnClass = "w75p";
        }

        $("#ticket-table").appTable({
            source: '<?php echo_uri("tickets/ticket_list_data_of_client/" . $client_id . "/1") ?>',
            order: [[6, "desc"]],
            displayLength: 30,
            responsive: false, //hide responsive (+) icon
            columns: [
                {title: '<?php echo lang("ticket_id") ?>', "class": idColumnClass},
                {title: '<?php echo lang("title") ?>', "class": titleColumnClass},

                {visible: false, searchable: false},
                {visible: false, searchable: false},
                {title: '<?php echo lang("ticket_type") ?>', "iDataSort": 3, "class": "w70", visible: showOption},
                {visible: false, searchable: false},
                {visible: false, searchable: false},
                {title: '<?php echo lang("last_activity") ?>', "iDataSort": 5, "class": "w70", visible: showOption},
                {title: '<?php echo lang("status") ?>', "class": "w70", visible: showOption}
            ],
            onInitComplete: function () {
                $("#ticket-table_wrapper .datatable-tools").addClass("hide");
            }
        });

    });
</script>