<div class="strong">
    <div class="clearfix mb5">
        <div class="pull-left"><?php echo lang("sub_total"); ?></div>
        <div class="pull-right"><?php echo to_currency($order_total_summary->order_subtotal, $order_total_summary->currency_symbol); ?></div>
    </div>

    <?php if ($order_total_summary->tax) { ?>
        <div class="clearfix mb5">
            <div class="pull-left"><?php echo $order_total_summary->tax_name; ?></div>
            <div class="pull-right"><?php echo to_currency($order_total_summary->tax, $order_total_summary->currency_symbol); ?></div>
        </div>
    <?php } ?>
    <?php if ($order_total_summary->tax2) { ?>
        <div class="clearfix mb5">
            <div class="pull-left"><?php echo $order_total_summary->tax_name2; ?></div>
            <div class="pull-right"><?php echo to_currency($order_total_summary->tax2, $order_total_summary->currency_symbol); ?></div>
        </div>
    <?php } ?>

    <div class="clearfix">
        <div class="pull-left"><?php echo lang("total"); ?></div>
        <div class="pull-right cart-total-value" data-value="<?php echo $order_total_summary->order_total; ?>"><?php echo to_currency($order_total_summary->order_total, $order_total_summary->currency_symbol); ?></div>
    </div>
</div>