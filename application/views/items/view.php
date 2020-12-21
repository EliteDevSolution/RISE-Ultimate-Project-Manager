<div class="modal-body clearfix general-form">

    <?php
    if ($model_info->files) {
        $files = @unserialize($model_info->files);
        if (count($files)) {
            if (!$this->login_user->is_admin) {
                ?>
                <div class="col-md-12 mt15">
                    <?php
                    if ($files) {
                        $total_files = count($files);
                        $this->load->view("includes/timeline_preview", array("files" => $files));
                    }
                    ?>
                </div>
                <?php
            }
        }
    }
    ?>

    <div class="clearfix">
        <div class="col-md-12">
            <strong class="font-18"><?php echo $model_info->title; ?></strong>
            <?php if ($model_info->show_in_client_portal && $this->login_user->is_admin && get_setting("module_order")) { ?>
                <span class="ml5 text-off font-11" data-toggle="tooltip" data-placement="right" title="<?php echo lang('showing_in_client_portal'); ?>"><i class="fa fa-shopping-basket"></i></span>
            <?php } ?>
        </div>
    </div>

    <div class="col-md-12 mb15">
        <span class="label item-rate-badge font-18 strong"><?php echo to_currency($model_info->rate, $client_info->currency_symbol); ?></span> <?php echo $model_info->unit_type ? "/" . $model_info->unit_type : ""; ?>
    </div>

    <div class="col-md-12 mb15">
        <?php echo $model_info->description ? nl2br(link_it($model_info->description)) : "-"; ?>
    </div>

    <?php
    if ($model_info->files) {
        $files = @unserialize($model_info->files);
        if (count($files)) {
            if ($this->login_user->is_admin && get_setting("module_order")) {
                ?>
                <div class="col-md-12 mt15">
                    <div class="mb15 text-off"><i class="fa fa-question-circle"></i> <?php echo lang("item_image_sorting_help_message"); ?></div>
                    <?php $this->load->view("includes/sortable_file_list", array("files" => $model_info->files, "action_url" => get_uri("items/save_files_sort"), "id" => $model_info->id)); ?>
                </div>
                <?php
            }
        }
    }
    ?>

</div>

<div class="modal-footer">
    <?php
    if ($this->login_user->user_type == "staff") {
        echo modal_anchor(get_uri("items/modal_form"), "<i class='fa fa-pencil'></i> " . lang('edit_item'), array("class" => "btn btn-default", "data-post-id" => $model_info->id, "title" => lang('edit_item')));
    }
    ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <?php
    //show add to cart button on client portal
    if ($this->login_user->user_type == "client" && !$model_info->added_to_cart) {
        echo js_anchor("<i class='fa fa-shopping-basket'></i> " . lang("add_to_cart"), array("class" => "btn btn-info item-add-to-cart-btn", "data-item_id" => $model_info->id));
    }
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>