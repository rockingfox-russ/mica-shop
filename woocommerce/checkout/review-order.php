<?php
/**
 * woocommerce/checkout/review-order.php
 * Custom-styled order review, but kept in the markup WooCommerce's
 * checkout AJAX expects (.woocommerce-checkout-review-order-table) so that
 * switching shipping methods actually refreshes the displayed totals.
 *
 * @see WC_AJAX::update_order_review() — replies with
 *      fragments['.woocommerce-checkout-review-order-table'], and
 *      wc-checkout.js does $('.woocommerce-checkout-review-order-table').replaceWith(...).
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-checkout-review-order-table">

    <div class="order-summary-items">
        <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
            $p = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            if ( ! $p || ! $p->exists() || $cart_item['quantity'] <= 0 ) continue;
            if ( ! apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) continue;
        ?>
        <div class="order-summary-item">
            <div class="order-summary-item-img">
                <?php echo $p->get_image( 'thumbnail' ); ?>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:.8rem;font-weight:600;line-height:1.3;color:var(--clr-text);">
                    <?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $p->get_name(), $cart_item, $cart_item_key ) ); ?>
                    <span style="color:var(--clr-text-muted);font-weight:400;"> &times; <?php echo esc_html( $cart_item['quantity'] ); ?></span>
                </div>
            </div>
            <div style="font-size:.875rem;font-weight:700;color:var(--clr-orange);flex-shrink:0;">
                <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $p, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="order-summary-totals">
        <?php do_action( 'woocommerce_review_order_before_cart_contents' ); ?>

        <div class="summary-row">
            <span><?php esc_html_e( 'Subtotal', 'micaonline' ); ?></span>
            <span><?php echo WC()->cart->get_cart_subtotal(); ?></span>
        </div>

        <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
        <div class="summary-row" style="color:var(--clr-success);">
            <span><?php esc_html_e( 'Coupon:', 'micaonline' ); ?> <?php echo esc_html( $code ); ?></span>
            <span>-<?php wc_cart_totals_coupon_html( $coupon ); ?></span>
        </div>
        <?php endforeach; ?>

        <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
        <div class="summary-row">
            <span><?php echo esc_html( $fee->name ); ?></span>
            <span><?php wc_cart_totals_fee_html( $fee ); ?></span>
        </div>
        <?php endforeach; ?>

        <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
            <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
        <div class="summary-row shipping-row">
            <?php wc_cart_totals_shipping_html(); ?>
        </div>
            <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
        <?php endif; ?>

        <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
        <div class="summary-row">
            <span><?php esc_html_e( 'Tax', 'micaonline' ); ?></span>
            <span><?php wc_cart_totals_taxes_total_html(); ?></span>
        </div>
        <?php endif; ?>

        <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

        <div class="summary-row total">
            <span><?php esc_html_e( 'Total', 'micaonline' ); ?></span>
            <span class="amount"><?php wc_cart_totals_order_total_html(); ?></span>
        </div>

        <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
    </div>

</div>
