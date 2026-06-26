<?php
/**
 * inc/woocommerce.php â WooCommerce hooks & theme tweaks
 */

defined( 'ABSPATH' ) || exit;

/* Hide the "Calculate shipping" estimator on the cart page — it quotes
 * against a blank/guessed address, and that stale quote was carrying over
 * to checkout. Customers now only get a shipping quote once on checkout,
 * where their full address is captured. */
add_filter( 'pre_option_woocommerce_enable_shipping_calc', fn() => 'no' );

/* ââ Tell WC this theme supports it ââ */
add_action( 'after_setup_theme', function () {
    add_theme_support( 'woocommerce' );
} );

/* ââ Remove default WC wrappers (we use our own) ââ */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar',             'woocommerce_get_sidebar', 10 );

/* ââ Replace with our wrappers ââ */
add_action( 'woocommerce_before_main_content', function () {
    echo '<div class="container"><div class="shop-layout">';
    // Sidebar rendered by archive-product.php directly
}, 10 );

add_action( 'woocommerce_after_main_content', function () {
    echo '</div></div>'; // .shop-layout + .container
}, 10 );

/* ââ Product columns ââ */
add_filter( 'loop_shop_columns', fn() => 4 );
add_filter( 'loop_shop_per_page', fn() => 24 );

/* ââ Use our product card template ââ */
remove_action( 'woocommerce_before_shop_loop_item',       'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_shop_loop_item_title',        'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title',  'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title',  'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item',        'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item',        'woocommerce_template_loop_add_to_cart', 10 );

add_action( 'woocommerce_before_shop_loop_item', function () {
    mica_part( 'content/product-card', [ 'product' => $GLOBALS['product'] ] );
}, 10 );

/* ââ Remove default breadcrumb (we use our own) ââ */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/* Product search is handled by the Relevanssi plugin (fuzzy/typo-tolerant).
 * Make sure Relevanssi also indexes SKU and barcode custom fields so those
 * still match, like the old custom search query did.
 */
add_filter( 'relevanssi_index_custom_fields', function ( $fields ) {
    $fields   = is_array( $fields ) ? $fields : [];
    $fields[] = '_sku';
    $fields[] = 'wpcf-barcode';
    return array_unique( $fields );
} );

/* Only auto-redirect to a single search match if the search term closely
 * matches the product title — broad/fuzzy/typo matches should still show
 * the search results page instead of jumping straight to a product. */
add_filter( 'relevanssi_redirect', function ( $url, $hits, $q ) {
    if ( ! $url || empty( $hits[0][0] ) ) {
        return $url;
    }
    $title = strtolower( $hits[0][0]->post_title );
    $term  = strtolower( trim( $q ) );
    if ( $term === '' ) {
        return false;
    }
    if ( str_contains( $title, $term ) ) {
        return $url;
    }
    similar_text( $title, $term, $percent );
    return $percent >= 85 ? $url : false;
}, 10, 3 );

/* ââ Related products: limit to 4 ââ */
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
    $args['posts_per_page'] = 4;
    $args['columns']        = 4;
    return $args;
} );

/* ââ My account: custom endpoints ââ */
// add_filter( 'woocommerce_account_menu_items', function ( $items ) {
//     $new = [];
//     foreach ( $items as $key => $label ) {
//         $new[ $key ] = $label;
//         if ( $key === 'orders' ) {
//             $new['click-collect'] = __( 'Collection Orders', 'micaonline' );
//         }
//     }
//     return $new;
// } );

/* ââ Cart fragments (AJAX mini-cart count) ââ */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
    ob_start();
    ?>
    <span class="cart-count" id="cart-count">
        <?php echo WC()->cart->get_cart_contents_count(); ?>
    </span>
    <?php
    $fragments['#cart-count'] = ob_get_clean();
    return $fragments;
} );

/* ââ Hide page.php's "Checkout" h1 on the order-received endpoint ââ */
add_filter( 'the_title', function ( $title, $id = null ) {
    if ( $id && (int) $id === wc_get_page_id( 'checkout' ) && is_wc_endpoint_url( 'order-received' ) ) {
        return '';
    }
    return $title;
}, 10, 2 );

/* ââ Checkout Delivery (untick deliver to another address) ââ */
add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );

/* ââ Single product: hide WC's qty field; theme custom stepper syncs value on submit ââ */
add_action( 'woocommerce_before_add_to_cart_quantity', function () {
    if ( is_product() ) {
        echo '<div class="wc-qty-hidden" aria-hidden="true" style="display:none;">';
    }
} );
add_action( 'woocommerce_after_add_to_cart_quantity', function () {
    if ( is_product() ) {
        echo '</div>';
    }
} );

/* ââ Checkout: add click & collect field ââ */
// add_action( 'woocommerce_after_checkout_billing_form', function ( $checkout ) {
//     if ( function_exists( 'mica_collect_store_field' ) ) {
//         mica_collect_store_field( $checkout );
//     }
// } );

// Only calculate shipping on cart/checkout pages, not everywhere
add_filter( 'woocommerce_cart_ready_to_calc_shipping', function( $calculate ) {
    return is_checkout() ? $calculate : false;
} );

// Suppress shipping zone notice on product pages — only relevant at cart/checkout
add_action( 'woocommerce_before_single_product', 'wc_clear_notices', 5 );

/* ── Lock default catalog sort to alphabetical ──
 * Without this WooCommerce reads its own session/cookie ordering, causing
 * every visitor to potentially see a different sort order.
 */
add_filter( 'woocommerce_default_catalog_orderby', fn() => 'title' );

add_filter( 'woocommerce_get_catalog_ordering_args', function( $args ) {
    if ( ! isset( $_GET['orderby'] ) ) {
        $args['orderby']  = 'title';
        $args['order']    = 'ASC';
        $args['meta_key'] = '';
    }
    return $args;
} );

/* ── "Currently out of stock online" on single product pages ── */
add_filter( 'woocommerce_get_availability_text', function( $text, $product ) {
    if ( ! $product->is_in_stock() ) {
        return __( 'Currently out of stock online', 'micaonline' );
    }
    if ( $product->managing_stock() && $product->get_stock_quantity() !== null ) {
        return sprintf( __( '%s in stock online', 'micaonline' ), wc_format_stock_quantity_for_display( $product->get_stock_quantity(), $product ) );
    }
    return __( 'In stock online', 'micaonline' );
}, 10, 2 );

/* ── Redirect wp-login.php to WooCommerce My Account page ── */
// This fixes the "account creation is blocked" error — WC handles registration independently.
// Exclude requests that are redirecting back to wp-admin so admin login still works.
add_filter( 'login_url', function( $url, $redirect = '' ) {
    if ( $redirect && strpos( $redirect, admin_url() ) !== false ) {
        return $url;
    }
    if ( function_exists( 'wc_get_page_permalink' ) ) {
        $account_url = wc_get_page_permalink( 'myaccount' );
        if ( $account_url ) return $account_url;
    }
    return $url;
}, 10, 2 );

add_filter( 'register_url', function( $url ) {
    if ( function_exists( 'wc_get_page_permalink' ) ) {
        $account_url = wc_get_page_permalink( 'myaccount' );
        if ( $account_url ) return $account_url;
    }
    return $url;
} );

add_filter( 'http_request_timeout', function( $timeout, $url ) {
    if ( str_contains( $url ?? '', 'thecourierguy' ) || str_contains( $url ?? '', 'tcg' ) ) {
        return 20; // 5 seconds max instead of default 30
    }
    return $timeout;
}, 10, 2 );

/* ── Registration: marketing consent checkbox ──
 * POPIA/GDPR-style explicit opt-in: unchecked by default, internal storage
 * only for now. A future ESP (e.g. Mailchimp) push can hook the same
 * woocommerce_created_customer action later without restructuring this. */
add_action( 'woocommerce_register_form', function () {
    ?>
    <p class="form-row form-row-wide">
        <label for="mica_marketing_consent" class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
            <input type="checkbox"
                   class="woocommerce-form__input woocommerce-form__input-checkbox"
                   name="mica_marketing_consent"
                   id="mica_marketing_consent"
                   value="1" />
            <?php esc_html_e( "Yes, I'd like to receive news and special offers by email.", 'micaonline' ); ?>
        </label>
    </p>
    <?php
} );

add_action( 'woocommerce_created_customer', function ( $customer_id ) {
    $consent = isset( $_POST['mica_marketing_consent'] ) && $_POST['mica_marketing_consent'] === '1' ? 1 : 0;
    update_user_meta( $customer_id, '_marketing_consent', $consent );
} );

