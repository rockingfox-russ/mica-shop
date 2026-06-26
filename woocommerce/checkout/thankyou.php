<?php
/**
 * woocommerce/checkout/thankyou.php
 * Order received (success) and order failed page.
 */
defined( 'ABSPATH' ) || exit;

$is_failed = $order && in_array( $order->get_status(), [ 'failed', 'cancelled' ], true );
?>
<?php if ( $order ) : ?>

        <?php if ( $is_failed ) : ?>
        <!-- ── ORDER FAILED ── -->
        <div class="thankyou-hero thankyou-hero--failed">
            <div class="thankyou-hero-icon">&#10007;</div>
            <h1 class="thankyou-hero-title"><?php esc_html_e( 'Payment unsuccessful', 'micaonline' ); ?></h1>
            <p class="thankyou-hero-sub">
                <?php esc_html_e( 'Unfortunately your order could not be processed. Please try again or contact us for assistance.', 'micaonline' ); ?>
            </p>
        </div>
        <?php else : ?>
        <!-- ── ORDER RECEIVED ── -->
        <div class="thankyou-hero thankyou-hero--success">
            <div class="thankyou-hero-icon">&#10003;</div>
            <h1 class="thankyou-hero-title"><?php esc_html_e( 'Thank you for your order!', 'micaonline' ); ?></h1>
            <p class="thankyou-hero-sub">
                <?php
                printf(
                    /* translators: %s order number */
                    esc_html__( 'Your order #%s has been received and is being processed. A confirmation email is on its way.', 'micaonline' ),
                    '<strong>' . esc_html( $order->get_order_number() ) . '</strong>'
                );
                ?>
            </p>
        </div>
        <?php endif; ?>

        <?php do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

        <!-- ── Order detail cards ── -->
        <div class="thankyou-meta-grid">
            <div class="thankyou-meta-card">
                <div class="thankyou-meta-label"><?php esc_html_e( 'Order number', 'micaonline' ); ?></div>
                <div class="thankyou-meta-value">#<?php echo esc_html( $order->get_order_number() ); ?></div>
            </div>
            <div class="thankyou-meta-card">
                <div class="thankyou-meta-label"><?php esc_html_e( 'Date', 'micaonline' ); ?></div>
                <div class="thankyou-meta-value"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></div>
            </div>
            <div class="thankyou-meta-card">
                <div class="thankyou-meta-label"><?php esc_html_e( 'Status', 'micaonline' ); ?></div>
                <div class="thankyou-meta-value thankyou-status thankyou-status--<?php echo esc_attr( $order->get_status() ); ?>">
                    <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                </div>
            </div>
            <div class="thankyou-meta-card">
                <div class="thankyou-meta-label"><?php esc_html_e( 'Total', 'micaonline' ); ?></div>
                <div class="thankyou-meta-value thankyou-total"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></div>
            </div>
        </div>

        <!-- ── Two-column layout: order details + items ── -->
        <div class="thankyou-layout">

            <!-- Left: addresses + payment -->
            <div>

                <?php if ( ! $is_failed ) : ?>
                <!-- Billing address -->
                <div class="checkout-section">
                    <h2 class="checkout-section-title">
                        <?php esc_html_e( 'Billing details', 'micaonline' ); ?>
                    </h2>
                    <address class="thankyou-address">
                        <?php echo wp_kses_post( $order->get_formatted_billing_address() ?: esc_html__( 'N/A', 'micaonline' ) ); ?>
                    </address>
                    <?php if ( $order->get_billing_email() ) : ?>
                    <p class="thankyou-address-meta">
                        <span class="thankyou-address-icon">&#9993;</span>
                        <?php echo esc_html( $order->get_billing_email() ); ?>
                    </p>
                    <?php endif; ?>
                    <?php if ( $order->get_billing_phone() ) : ?>
                    <p class="thankyou-address-meta">
                        <span class="thankyou-address-icon">&#9990;</span>
                        <?php echo esc_html( $order->get_billing_phone() ); ?>
                    </p>
                    <?php endif; ?>
                </div>

                <?php if ( $order->needs_shipping_address() ) : ?>
                <!-- Shipping address -->
                <div class="checkout-section">
                    <h2 class="checkout-section-title">
                        <?php esc_html_e( 'Shipping address', 'micaonline' ); ?>
                    </h2>
                    <address class="thankyou-address">
                        <?php echo wp_kses_post( $order->get_formatted_shipping_address() ?: esc_html__( 'Same as billing', 'micaonline' ) ); ?>
                    </address>
                </div>
                <?php endif; ?>

                <!-- Payment method -->
                <div class="checkout-section">
                    <h2 class="checkout-section-title">
                        <?php esc_html_e( 'Payment method', 'micaonline' ); ?>
                    </h2>
                    <p class="thankyou-payment-method"><?php echo esc_html( $order->get_payment_method_title() ); ?></p>
                </div>
                <?php endif; ?>

                <!-- CTA buttons -->
                <div class="thankyou-actions">
                    <?php if ( $is_failed ) : ?>
                    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn-primary">
                        <?php esc_html_e( 'Try again', 'micaonline' ); ?>
                    </a>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn-secondary">
                        <?php esc_html_e( 'Continue shopping', 'micaonline' ); ?>
                    </a>
                    <?php else : ?>
                    <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="btn-primary">
                        <?php esc_html_e( 'View my orders', 'micaonline' ); ?>
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn-secondary">
                        <?php esc_html_e( 'Continue shopping', 'micaonline' ); ?>
                    </a>
                    <?php endif; ?>
                </div>

                <?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>

            </div><!-- left column -->

            <!-- Right: order items summary -->
            <div class="oder-summary-block">
                <div class="order-summary">
                    <div class="order-summary-header">
                        <?php esc_html_e( 'Your order', 'micaonline' ); ?>
                    </div>
                    <div class="order-summary-items">
                        <?php foreach ( $order->get_items() as $item ) :
                            /** @var WC_Order_Item_Product $item */
                            $product = $item->get_product();
                        ?>
                        <div class="order-summary-item">
                            <div class="order-summary-item-img">
                                <?php if ( $product ) echo $product->get_image( 'thumbnail' ); ?>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:.8rem;font-weight:600;line-height:1.3;color:var(--clr-text);">
                                    <?php echo esc_html( $item->get_name() ); ?>
                                    <span style="color:var(--clr-text-muted);font-weight:400;"> &times; <?php echo $item->get_quantity(); ?></span>
                                </div>
                            </div>
                            <div style="font-size:.875rem;font-weight:700;color:var(--clr-orange);flex-shrink:0;">
                                <?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="order-summary-totals">
                        <div class="summary-row">
                            <span><?php esc_html_e( 'Subtotal', 'micaonline' ); ?></span>
                            <span><?php echo wp_kses_post( $order->get_subtotal_to_display() ); ?></span>
                        </div>

                        <?php foreach ( $order->get_coupons() as $coupon ) : ?>
                        <div class="summary-row" style="color:var(--clr-success);">
                            <span><?php esc_html_e( 'Coupon:', 'micaonline' ); ?> <?php echo esc_html( $coupon->get_code() ); ?></span>
                            <span>-<?php echo wp_kses_post( wc_price( $coupon->get_discount() ) ); ?></span>
                        </div>
                        <?php endforeach; ?>

                        <?php foreach ( $order->get_fees() as $fee ) : ?>
                        <div class="summary-row">
                            <span><?php echo esc_html( $fee->get_name() ); ?></span>
                            <span><?php echo wp_kses_post( wc_price( $fee->get_total() ) ); ?></span>
                        </div>
                        <?php endforeach; ?>

                        <?php if ( $order->get_shipping_total() > 0 ) : ?>
                        <div class="summary-row">
                            <span><?php esc_html_e( 'Shipping', 'micaonline' ); ?></span>
                            <span><?php echo wp_kses_post( wc_price( $order->get_shipping_total() ) ); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ( wc_tax_enabled() && $order->get_total_tax() > 0 ) : ?>
                        <div class="summary-row">
                            <span><?php esc_html_e( 'Tax', 'micaonline' ); ?></span>
                            <span><?php echo wp_kses_post( wc_price( $order->get_total_tax() ) ); ?></span>
                        </div>
                        <?php endif; ?>

                        <div class="summary-row total">
                            <span><?php esc_html_e( 'Total', 'micaonline' ); ?></span>
                            <span class="amount"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
                        </div>
                    </div>
                </div>
            </div><!-- right column -->

        </div><!-- .thankyou-layout -->

        <?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

    <?php else : ?>

        <!-- ── No order found (direct page visit) ── -->
        <div class="thankyou-hero thankyou-hero--notice">
            <div class="thankyou-hero-icon">&#9432;</div>
            <h1 class="thankyou-hero-title"><?php esc_html_e( 'Order not found', 'micaonline' ); ?></h1>
            <p class="thankyou-hero-sub">
                <?php esc_html_e( 'We could not locate your order. It may have already been processed or the link may have expired.', 'micaonline' ); ?>
            </p>
            <div class="thankyou-actions thankyou-actions--center">
                <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn-primary">
                    <?php esc_html_e( 'Continue shopping', 'micaonline' ); ?>
                </a>
            </div>
        </div>

    <?php endif; ?>
