<?php echo form_open(get_uri("expenses/save"), array("id" => "expense-form", "class" => "general-form", "role" => "form")); ?>
<div id="expense-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <div class=" form-group">
            <label for="expense_date" class=" col-md-3"><?php echo lang('date_of_expense'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_input(array(
                    "id" => "expense_date",
                    "name" => "expense_date",
                    "value" => $model_info->expense_date ? $model_info->expense_date : get_my_local_time("Y-m-d"),
                    "class" => "form-control recurring_element",
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                ));
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="category_id" class=" col-md-3"><?php echo lang('category'); ?></label>
            <div class=" col-md-9">
                <?php
                echo form_dropdown("category_id", $categories_dropdown, $model_info->category_id, "class='select2 validate-hidden' id='category_id' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="title" class=" col-md-3"><?php echo lang('amount'); ?></label>
            <div class=" col-md-9">
                <?php
                echo form_input(array(
                    "id" => "amount",
                    "name" => "amount",
                    "value" => $model_info->amount ? to_decimal_format($model_info->amount) : "",
                    "class" => "form-control",
                    "placeholder" => lang('amount'),
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                ));
                ?>
            </div>
        </div>
        <div class=" form-group">
            <label for="title" class=" col-md-3"><?php echo lang('title'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_input(array(
                    "id" => "title",
                    "name" => "title",
                    "value" => $model_info->title,
                    "class" => "form-control",
                    "placeholder" => lang("title")
                ));
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="description" class=" col-md-3"><?php echo lang('description'); ?></label>
            <div class=" col-md-9">
                <?php
                echo form_textarea(array(
                    "id" => "description",
                    "name" => "description",
                    "value" => $model_info->description ? $model_info->description : "",
                    "class" => "form-control",
                    "placeholder" => lang('description'),
                    "data-rich-text-editor" => true
                ));
                ?>

            </div>
        </div>

        <?php if ($this->login_user->is_admin || $can_access_clients && $can_access_expenses) { ?>

            <?php if ($client_id) { ?>
                <input type="hidden" name="expense_client_id" value="<?php echo $client_id; ?>" />
            <?php } else { ?>
                <div class="form-group">
                    <label for="expense_client_id" class="col-md-3"><?php echo lang('client'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_dropdown("expense_client_id", $clients_dropdown, $model_info->client_id, "class='select2' id='expense_client_id'");
                        ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

        <div class="form-group">
            <label for="expense_project_id" class=" col-md-3"><?php echo lang('project'); ?></label>
            <div class=" col-md-9">
                <?php
                echo form_dropdown("expense_project_id", $projects_dropdown, $model_info->project_id, "class='select2 validate-hidden' id='expense_project_id'");
                ?>
            </div>
        </div>

        <div class="form-group">
            <label for="expense_user_id" class=" col-md-3"><?php echo lang('team_member'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown("expense_user_id", $members_dropdown, $model_info->user_id, "class='select2 validate-hidden' id='expense_user_id'");
                ?>
            </div>
        </div>

        <div class="form-group">
            <label for="tax_id" class=" col-md-3"><?php echo lang('tax'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown("tax_id", $taxes_dropdown, array($model_info->tax_id), "class='select2'");
                ?>
            </div>
        </div>

        <div class="form-group">
            <label for="tax_id" class=" col-md-3"><?php echo lang('second_tax'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown("tax_id2", $taxes_dropdown, array($model_info->tax_id2), "class='select2'");
                ?>
            </div>
        </div>

        <div class="form-group">
            <label for="expense_recurring" class=" col-md-3"><?php echo lang('recurring'); ?>  <span class="help" data-toggle="tooltip" title="<?php echo lang('cron_job_required'); ?>"><i class="fa fa-question-circle"></i></span></label>
            <div class=" col-md-9">
                <?php
                echo form_checkbox("recurring", "1", $model_info->recurring ? true : false, "id='expense_recurring'");
                ?>                       
            </div>
        </div>    
        <div id="recurring_fields" class="<?php if (!$model_info->recurring) echo "hide"; ?>"> 
            <div class="form-group">
                <label for="repeat_every" class=" col-md-3"><?php echo lang('repeat_every'); ?></label>
                <div class="col-md-4">
                    <?php
                    echo form_input(array(
                        "id" => "repeat_every",
                        "name" => "repeat_every",
                        "type" => "number",
                        "value" => $model_info->repeat_every ? $model_info->repeat_every : 1,
                        "min" => 1,
                        "class" => "form-control recurring_element",
                        "placeholder" => lang('repeat_every'),
                        "data-rule-required" => true,
                        "data-msg-required" => lang("field_required")
                    ));
                    ?>
                </div>
                <div class="col-md-5">
                    <?php
                    echo form_dropdown(
                            "repeat_type", array(
                        "days" => lang("interval_days"),
                        "weeks" => lang("interval_weeks"),
                        "months" => lang("interval_months"),
                        "years" => lang("interval_years"),
                            ), $model_info->repeat_type ? $model_info->repeat_type : "months", "class='select2 recurring_element' id='repeat_type'"
                    );
                    ?>
                </div>
            </div>    

            <div class="form-group">
                <label for="no_of_cycles" class=" col-md-3"><?php echo lang('cycles'); ?></label>
                <div class="col-md-4">
                    <?php
                    echo form_input(array(
                        "id" => "no_of_cycles",
                        "name" => "no_of_cycles",
                        "type" => "number",
                        "min" => 1,
                        "value" => $model_info->no_of_cycles ? $model_info->no_of_cycles : "",
                        "class" => "form-control",
                        "placeholder" => lang('cycles')
                    ));
                    ?>
                </div>
                <div class="col-md-5 mt5">
                    <span class="help" data-toggle="tooltip" title="<?php echo lang('recurring_cycle_instructions'); ?>"><i class="fa fa-question-circle"></i></span>
                </div>
            </div>  



            <div class = "form-group hide" id = "next_recurring_date_container" >
                <label for = "next_recurring_date" class = " col-md-3"><?php echo lang('next_recurring_date'); ?>  </label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "next_recurring_date",
                        "name" => "next_recurring_date",
                        "class" => "form-control",
                        "placeholder" => lang('next_recurring_date'),
                        "autocomplete" => "off",
                        "data-rule-required" => true,
                        "data-msg-required" => lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

        </div> 

        <div class="form-group">
            <label class=" col-md-3"></label>
            <div class="col-md-9">
                <?php
                $this->load->view("includes/file_list", array("files" => $model_info->files));
                ?>
            </div>
        </div>

        <?php $this->load->view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-3", "field_column" => " col-md-9")); ?> 

        <?php $this->load->view("includes/dropzone_preview"); ?>    

    </div>

    <div class="modal-footer">
        <div class="row">
            <button class="btn btn-default upload-file-button pull-left btn-sm round" type="button" style="color:#7988a2"><i class='fa fa-camera'></i> <?php echo lang("upload_file"); ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
            <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {


        var uploadUrl = "<?php echo get_uri("expenses/upload_file"); ?>";
        var validationUrl = "<?php echo get_uri("expenses/validate_expense_file"); ?>";

        var dropzone = attachDropzoneWithForm("#expense-dropzone", uploadUrl, validationUrl);

        $("#expense-form").appForm({
            onSuccess: function (result) {
                if (typeof $EXPENSE_TABLE !== 'undefined') {
                    $EXPENSE_TABLE.appTable({newData: result.data, dataId: result.id});
                } else {
                    location.reload();
                }
            },
            onAjaxSuccess: function (result) {
                if (!result.success && result.next_recurring_date_error) {
                    $("#next_recurring_date").val(result.next_recurring_date_value);
                    $("#next_recurring_date_container").removeClass("hide");

                    $("#expense-form").data("validator").showErrors({
                        "next_recurring_date": result.next_recurring_date_error
                    });
                }
            }
        });

        setDatePicker("#expense_date");

        $("#expense-form .select2").select2();
        
        $('[data-toggle="tooltip"]').tooltip();
        
        //show/hide recurring fields
        $("#expense_recurring").click(function () {
            if ($(this).is(":checked")) {
                $("#recurring_fields").removeClass("hide");
            } else {
                $("#recurring_fields").addClass("hide");
            }
        });
        
        setDatePicker("#next_recurring_date", {
            startDate: moment().add(1, 'days').format("YYYY-MM-DD") //set min date = tomorrow
        });

    });
</script>