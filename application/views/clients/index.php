<div id="page-content" class="p20 clearfix">
    <div class="panel clearfix">
        <ul id="client-tabs" data-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            <li><a id="clients-button" class="active" role="presentation" href="javascript:;" data-target="#clients"><?php echo lang('clients'); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("clients/contacts/"); ?>" data-target="#contacts"><?php echo lang('contacts'); ?></a></li>
            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php if ($can_edit_clients) { ?>
                        <?php echo modal_anchor(get_uri("clients/import_clients_modal_form"), "<i class='fa fa-upload'></i> " . lang('import_clients'), array("class" => "btn btn-default", "title" => lang('import_clients'))); ?>
                        <?php echo modal_anchor(get_uri("clients/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_client'), array("class" => "btn btn-default", "title" => lang('add_client'))); ?>
                    <?php } ?>
                </div>
            </div>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="clients">
                <div class="table-responsive">
                    <table id="client-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="contacts"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    loadClientsTable = function (selector) {
        var showInvoiceInfo = true;
        if (!"<?php echo $show_invoice_info; ?>") {
            showInvoiceInfo = false;
        }
        
        var showOptions = true;
        if (!"<?php echo $can_edit_clients; ?>") {
            showOptions = false;
        }

        $(selector).appTable({
            source: '<?php echo_uri("clients/list_data") ?>',
            filterDropdown: [
                {name: "group_id", class: "w200", options: <?php echo $groups_dropdown; ?>},
                {name: "quick_filter", class: "w200", options: <?php $this->load->view("clients/quick_filters_dropdown"); ?>},
                <?php if($this->login_user->is_admin || get_array_value($this->login_user->permissions, "client") === "all"){ ?>
                    {name: "created_by", class: "w200", options: <?php echo $team_members_dropdown; ?>}
                <?php } ?>
            ],
            columns: [
                {title: "<?php echo lang("id") ?>", "class": "text-center w50"},
                {title: "<?php echo lang("company_name") ?>"},
                {title: "<?php echo lang("primary_contact") ?>"},
                {title: "<?php echo lang("client_groups") ?>"},
                {title: "<?php echo lang("projects") ?>"},
                {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo lang("invoice_value") ?>"},
                {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo lang("payment_received") ?>"},
                {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo lang("due") ?>"}
<?php echo $custom_field_headers; ?>,
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w100", visible: showOptions}
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6], '<?php echo $custom_field_headers; ?>')
        });
    };

    $(document).ready(function () {
        loadClientsTable("#client-table");

        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "contacts") {
                $("[data-target=#contacts]").trigger("click");
            }
        }, 210);
    });
</script>