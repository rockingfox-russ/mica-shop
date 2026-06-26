<?php
/**
 * inc/cron-safety-net.php — Reliability net for WooCommerce's scheduled-sales cron.
 * DISABLE_WP_CRON is set in wp-config.php and the site sits behind Cloudflare's
 * page cache, so neither real cron nor the pseudo-cron-on-page-load fallback can
 * be relied on. This calls WooCommerce's own wc_scheduled_sales() — the exact
 * function its cron event would call — from normal frontend traffic, rate-limited
 * via a transient so it runs at most once every ~15 minutes.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'template_redirect', function () {
    if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
        return;
    }
    if ( ! function_exists( 'wc_scheduled_sales' ) ) {
        return;
    }

    $lock = 'mica_wc_scheduled_sales_lock';
    if ( get_transient( $lock ) ) {
        return;
    }
    set_transient( $lock, 1, 15 * MINUTE_IN_SECONDS );

    wc_scheduled_sales();
} );
