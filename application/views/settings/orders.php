<div id="page-content" class="p20 clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "orders";
            $this->load->view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("settings/save_order_settings"), array("id" => "order-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <div class="panel panel-default">

                <ul data-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                    <li><a  role="presentation" class="active" href="javascript:;" data-target="#order-settings-tab"> <?php echo lang('order_settings'); ?></a></li>
                    <li><a role="presentation" href="<?php echo_uri("order_status"); ?>" data-target="#order-status-settings-tab"><?php echo lang('order_status'); ?></a></li>
                    <div class="tab-title clearfix no-border">
                        <div class="title-button-group">
                            <?php echo modal_anchor(get_uri("order_status/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_order_status'), array("class" => "btn btn-default hide", "title" => lang('add_order_status'), "id" => "order-status-add-btn")); ?>
                        </div>
                    </div>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade" id="order-settings-tab">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="logo" class=" col-md-2"><?php echo lang('order_logo'); ?> (300x100) </label>
                                <div class=" col-md-10">
                                    <div class="pull-left mr15">
                                        <?php
                                        $order_logo = "order_logo";
                                        if (!get_setting($order_logo)) {
                                            $order_logo = "invoice_logo";
                                        }
                                        ?>
                                        <img id="order-logo-preview" src="<?php echo get_file_from_setting($order_logo); ?>" alt="..." />
                                    </div>
                                    <div class="pull-left file-upload btn btn-default btn-xs">
                                        <i class="fa fa-upload"></i> <?php echo lang("upload_and_crop"); ?>
                                        <input id="order_logo_file" class="cropbox-upload upload" name="order_logo_file" type="file" data-height="100" data-width="300" data-preview-container="#order-logo-preview" data-input-field="#order_logo" />
                                    </div>
                                    <div class="mt10 ml10 pull-left">
                                        <?php
                                        echo form_upload(array(
                                            "id" => "order_logo_file_upload",
                                            "name" => "order_logo_file",
                                            "class" => "no-outline hidden-input-file"
                                        ));
                                        ?>
                                        <label for="order_logo_file_upload" class="btn btn-default btn-xs">
                                            <i class="fa fa-upload"></i> <?php echo lang("upload"); ?>
                                        </label>
                                    </div>
                                    <input type="hidden" id="order_logo" name="order_logo" value=""  />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="order_prefix" class=" col-md-2"><?php echo lang('order_prefix'); ?></label>
                                <div class=" col-md-10">
                                    <?php
                                    echo form_input(array(
                                        "id" => "order_prefix",
                                        "name" => "order_prefix",
                                        "value" => get_setting("order_prefix"),
                                        "class" => "form-control",
                                        "placeholder" => strtoupper(lang("order")) . " #"
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="order_color" class=" col-md-2"><?php echo lang('order_color'); ?></label>
                                <div class=" col-md-10">
                                    <?php
                                    echo form_input(array(
                                        "id" => "order_color",
                                        "name" => "order_color",
                                        "value" => get_setting("order_color"),
                                        "class" => "form-control",
                                        "placeholder" => "Ex. #e2e2e2"
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" id="last_order_id" name="last_order_id" value="<?php echo $last_id; ?>" />
                                <label for="initial_number_of_the_order" class="col-md-2"><?php echo lang('initial_number_of_the_order'); ?></label>
                                <div class="col-md-3">
                                    <?php
                                    echo form_input(array(
                                        "id" => "initial_number_of_the_order",
                                        "name" => "initial_number_of_the_order",
                                        "type" => "number",
                                        "value" => $last_id + 1,
                                        "class" => "form-control mini",
                                        "data-rule-greaterThan" => "#last_order_id",
                                        "data-msg-greaterThan" => lang("the_orders_id_must_be_larger_then_last_order_id")
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="order_tax" class=" col-md-2"><?php echo lang('tax'); ?></label>
                                <div class="col-md-10">
                                    <?php
                                    echo form_dropdown("order_tax_id", $taxes_dropdown, array(get_setting('order_tax_id')), "class='select2 tax-select2 mini'");
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="order_tax" class=" col-md-2"><?php echo lang('second_tax'); ?></label>
                                <div class="col-md-10">
                                    <?php
                                    echo form_dropdown("order_tax_id2", $taxes_dropdown, array(get_setting('order_tax_id2')), "class='select2 tax-select2 mini'");
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="order_footer" class="col-md-2"><?php echo lang('order_footer') ?></label>
                                <div class="col-md-10">
                                    <?php
                                    echo form_textarea(array(
                                        "id" => "order_footer",
                                        "name" => "order_footer",
                                        "value" => get_setting('order_footer'),
                                        "class" => "form-control"
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="order-status-settings-tab"></div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php $this->load->view("includes/cropbox"); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#order-settings-form").appForm({
            isModal: false,
            beforeAjaxSubmit: function (data) {
                $.each(data, function (index, obj) {
                    if (obj.name === "order_logo") {
                        var image = replaceAll(":", "~", data[index]["value"]);
                        data[index]["value"] = image;
                    }
                    if (obj.name === "order_footer") {
                        data[index]["value"] = encodeAjaxPostData(getWYSIWYGEditorHTML("#order_footer"));
                    }
                });
            },
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});
                } else {
                    appAlert.error(result.message);
                }

                if ($("#order_logo").val() || result.reload_page) {
                    location.reload();
                }
            }
        });

        $("#order-settings-form .select2").select2();

        initWYSIWYGEditor("#order_footer", {height: 100});

        $(".cropbox-upload").change(function () {
            showCropBox(this);
        });

        //show add order status button
        $("a[data-target='#order-status-settings-tab']").click(function () {
            $("#order-status-add-btn").removeClass("hide");
        });
    });
</script>