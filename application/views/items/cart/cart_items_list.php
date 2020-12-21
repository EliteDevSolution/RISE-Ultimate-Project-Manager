<?php if ($items) { ?>
    <div class="rise-cart-body">
        <?php
        foreach ($items as $item) {
            ?>
            <div class='js-item-row pull-left message-row cart-item-row clearfix w100p' data-id='<?php echo $item->id; ?>' data-original_item_id="<?php echo $item->item_id; ?>">
                <?php $this->load->view("items/cart/cart_item_data", array("item" => $item)); ?>
            </div>

        <?php }
        ?>

        <div id="cart-total-section" class="cart-item-row pull-left message-row clearfix w100p cart-total no-border mt5 mb5">
            <?php $this->load->view("items/cart/cart_total_section"); ?>
        </div>
    </div>

    <div class="p10 b-t">
        <?php echo anchor(get_uri("orders/process_order"), "<i class='fa fa-check-circle-o'></i> " . lang("process_order"), array("class" => "btn btn-info col-md-12 col-xs-12 col-sm-12")); ?>
    </div>
    <?php
} else {
    ?>

    <div class="chat-no-messages text-off text-center">
        <i class="fa fa-shopping-basket"></i><br />
        <?php echo lang("no_items_text"); ?>
    </div>

<?php } ?>