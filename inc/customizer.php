<?php
/**
 * inc/customizer.php — Theme Customizer options
 */
defined( 'ABSPATH' ) || exit;

/* Shared with the customize_register settings below and the wp_head CSS output —
 * kept at file scope so both can read it without depending on customize_register
 * having fired (it only fires inside the Customizer admin/preview, never on
 * normal front-end page loads). */
$mica_colours = [
    'mica_color_orange' => [ 'label' => __( 'Primary (Orange)', 'micaonline' ), 'default' => '#E8590C' ],
    'mica_color_blue'   => [ 'label' => __( 'Secondary (Blue)', 'micaonline' ), 'default' => '#1A4E8A' ],
    'mica_color_yellow' => [ 'label' => __( 'Accent (Yellow)', 'micaonline' ), 'default' => '#F5B800' ],
];

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) use ( $mica_colours ) {

    /* ── Store Information ── */
    $wp_customize->add_section( 'mica_store_info', [
        'title'    => __( 'Store Information', 'micaonline' ),
        'priority' => 30,
    ] );

    $fields = [
        'mica_store_phone'   => __( 'Phone Number', 'micaonline' ),
        'mica_store_email'   => __( 'Email Address', 'micaonline' ),
        'mica_store_hours'   => __( 'Trading Hours', 'micaonline' ),
        'mica_address'   => __( 'Head Office Address', 'micaonline' ),
        'mica_google_map'   => __( 'Google Map Location', 'micaonline' ),
        'mica_utility_bar'   => __( 'Utility Bar Text', 'micaonline' ),
    ];
    foreach ( $fields as $key => $label ) {
        $wp_customize->add_setting( $key, [ 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( $key, [
            'label'   => $label,
            'section' => 'mica_store_info',
            'type'    => 'text',
        ] );
    }

    $wp_customize->add_setting( 'mica_store_hours', [
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post', // allows safe HTML like <br>
    ] );

    /* ── Brand Colours ── */
    $wp_customize->add_section( 'mica_colours', [
        'title'    => __( 'Brand Colours', 'micaonline' ),
        'priority' => 40,
    ] );

    foreach ( $mica_colours as $key => $data ) {
        $wp_customize->add_setting( $key, [
            'default'           => $data['default'],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ] );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $key, [
            'label'   => $data['label'],
            'section' => 'mica_colours',
        ] ) );
    }

    /* ── Hero Slides ── */
    $wp_customize->add_section( 'mica_hero_slides', [
        'title'    => __( 'Homepage Hero Slides', 'micaonline' ),
        'priority' => 45,
        'description' => __( 'Up to 5 slides for the homepage hero slider. Leave a slide\'s title and image both empty to skip it. If no slides are configured, the homepage falls back to the single hero defined in the page content.', 'micaonline' ),
    ] );

    for ( $i = 1; $i <= 5; $i++ ) {
        $wp_customize->add_setting( "mica_slide_{$i}_image", [ 'default' => '', 'sanitize_callback' => 'esc_url_raw' ] );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "mica_slide_{$i}_image", [
            'label'   => sprintf( __( 'Slide %d — Image', 'micaonline' ), $i ),
            'section' => 'mica_hero_slides',
        ] ) );

        $wp_customize->add_setting( "mica_slide_{$i}_title", [ 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( "mica_slide_{$i}_title", [
            'label' => sprintf( __( 'Slide %d — Title', 'micaonline' ), $i ),
            'section' => 'mica_hero_slides', 'type' => 'text',
        ] );

        $wp_customize->add_setting( "mica_slide_{$i}_subtitle", [ 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( "mica_slide_{$i}_subtitle", [
            'label' => sprintf( __( 'Slide %d — Subtitle', 'micaonline' ), $i ),
            'section' => 'mica_hero_slides', 'type' => 'text',
        ] );

        $wp_customize->add_setting( "mica_slide_{$i}_link", [ 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( "mica_slide_{$i}_link", [
            'label' => sprintf( __( 'Slide %d — Button Link', 'micaonline' ), $i ),
            'section' => 'mica_hero_slides', 'type' => 'text',
        ] );

        $wp_customize->add_setting( "mica_slide_{$i}_button_text", [ 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( "mica_slide_{$i}_button_text", [
            'label' => sprintf( __( 'Slide %d — Button Text', 'micaonline' ), $i ),
            'section' => 'mica_hero_slides', 'type' => 'text',
        ] );
    }

    /* ── Promo Stripe ── */
    $wp_customize->add_section( 'mica_promo_stripe', [
        'title'       => __( 'Promo Stripe (Homepage)', 'micaonline' ),
        'priority'    => 47,
        'description' => __( 'Shown in the yellow stripe below the homepage hero slider. Leave a field empty to hide that slot. Type emoji directly into the fields if desired (e.g. "🚚 Free delivery over R500").', 'micaonline' ),
    ] );

    $wp_customize->add_setting( 'mica_promo_stripe_free_delivery', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'mica_promo_stripe_free_delivery', [
        'label'       => __( 'Item 1 — Free Delivery Message', 'micaonline' ),
        'description' => __( 'E.g. "🚚 Free delivery over R500". Also shown on individual product pages when filled in.', 'micaonline' ),
        'section'     => 'mica_promo_stripe',
        'type'        => 'text',
    ] );

    $mica_promo_stripe_defaults = [
        2 => '🔒 Secure checkout with PayFast',
        3 => '📦 Delivery between 2-3 days',
        4 => '✅ 100% South African owned',
    ];
    foreach ( $mica_promo_stripe_defaults as $i => $default_text ) {
        $wp_customize->add_setting( "mica_promo_stripe_{$i}", [ 'default' => $default_text, 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( "mica_promo_stripe_{$i}", [
            'label'   => sprintf( __( 'Item %d', 'micaonline' ), $i ),
            'section' => 'mica_promo_stripe',
            'type'    => 'text',
        ] );
    }

    /* ── Site Background ── */
    $wp_customize->add_section( 'mica_background', [
        'title'    => __( 'Site Background', 'micaonline' ),
        'priority' => 50,
    ] );

    $wp_customize->add_setting( 'mica_bg_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mica_bg_image', [
        'label'   => __( 'Background Image (content area only)', 'micaonline' ),
        'section' => 'mica_background',
    ] ) );

    $wp_customize->add_setting( 'mica_bg_image_opacity', [
        'default'           => 100,
        'sanitize_callback' => 'absint',
    ] );
    $wp_customize->add_control( 'mica_bg_image_opacity', [
        'label'       => __( 'Image Opacity (%)', 'micaonline' ),
        'description' => __( 'Fades the background image itself. 100 = fully visible.', 'micaonline' ),
        'section'     => 'mica_background',
        'type'        => 'range',
        'input_attrs' => [ 'min' => 0, 'max' => 100, 'step' => 5 ],
    ] );

    $wp_customize->add_setting( 'mica_bg_overlay_opacity', [
        'default'           => 35,
        'sanitize_callback' => 'absint',
    ] );
    $wp_customize->add_control( 'mica_bg_overlay_opacity', [
        'label'       => __( 'Overlay Darkness (%)', 'micaonline' ),
        'description' => __( 'Darkens the background image so text stays readable. 0 = no overlay.', 'micaonline' ),
        'section'     => 'mica_background',
        'type'        => 'range',
        'input_attrs' => [ 'min' => 0, 'max' => 90, 'step' => 5 ],
    ] );

    /* ── Floating Promotions Button ── */
    $wp_customize->add_section( 'mica_promo_button', [
        'title'    => __( 'Floating Promotions Button', 'micaonline' ),
        'priority' => 60,
    ] );

    $wp_customize->add_setting( 'mica_promo_btn_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mica_promo_btn_image', [
        'label'       => __( 'Button Image', 'micaonline' ),
        'description' => __( 'Hidden sitewide until an image is uploaded.', 'micaonline' ),
        'section'     => 'mica_promo_button',
    ] ) );

    $wp_customize->add_setting( 'mica_promo_btn_url', [
        'default'           => '/shop/?on_sale=1',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'mica_promo_btn_url', [
        'label'   => __( 'Link URL', 'micaonline' ),
        'section' => 'mica_promo_button',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'mica_promo_btn_text', [
        'default'           => __( 'View Promotion', 'micaonline' ),
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'mica_promo_btn_text', [
        'label'   => __( 'Button Text', 'micaonline' ),
        'section' => 'mica_promo_button',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'mica_promo_btn_bg_color', [
        'default'           => '#FFFFFF',
        'sanitize_callback' => 'sanitize_hex_color',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'mica_promo_btn_bg_color', [
        'label'   => __( 'Background Colour', 'micaonline' ),
        'section' => 'mica_promo_button',
    ] ) );

    $wp_customize->add_setting( 'mica_promo_btn_text_color', [
        'default'           => '#1A1A1A',
        'sanitize_callback' => 'sanitize_hex_color',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'mica_promo_btn_text_color', [
        'label'   => __( 'Text Colour', 'micaonline' ),
        'section' => 'mica_promo_button',
    ] ) );

} );

/* ── Output CSS variables from customizer ──
 * Registered unconditionally (NOT inside customize_register, which only fires
 * inside the Customizer admin/preview) so this actually runs on normal
 * front-end page loads. */
add_action( 'wp_head', function () use ( $mica_colours ) {
    $vars = [];
    $map  = [
        'mica_color_orange' => '--clr-orange',
        'mica_color_blue'   => '--clr-blue',
        'mica_color_yellow' => '--clr-yellow',
    ];
    foreach ( $mica_colours as $key => $data ) {
        $val = get_theme_mod( $key, $data['default'] );
        if ( $val !== $data['default'] ) {
            $vars[] = $map[ $key ] . ':' . esc_attr( $val );
        }
    }
    if ( ! empty( $vars ) ) {
        echo '<style>:root{' . implode( ';', $vars ) . '}</style>' . "\n";
    }
} );

/* ── Output background-image CSS for .site-main ── */
add_action( 'wp_head', function () {
    $bg_image = get_theme_mod( 'mica_bg_image', '' );
    if ( empty( $bg_image ) ) return; // no image set — zero CSS output, layout untouched

    $img_opacity = max( 0, min( 100, (int) get_theme_mod( 'mica_bg_image_opacity', 100 ) ) ) / 100;
    $alpha       = max( 0, min( 90, (int) get_theme_mod( 'mica_bg_overlay_opacity', 35 ) ) ) / 100;

    // Image and dark overlay are separate layers (::before / ::after) at a
    // NEGATIVE z-index, so they paint behind all real content automatically —
    // this deliberately avoids touching any child's position/z-index, since
    // doing so previously demoted fixed-position overlays (e.g. the in-store
    // stock modal) to position:relative, breaking them.
    $css  = '.site-main{position:relative;}';
    $css .= '.site-main::before{content:"";position:absolute;inset:0;background-image:url(' . esc_url( $bg_image ) . ');background-size:cover;background-position:center top;background-attachment:fixed;opacity:' . $img_opacity . ';pointer-events:none;z-index:-1;}';
    $css .= '.site-main::after{content:"";position:absolute;inset:0;background:rgba(0,0,0,' . $alpha . ');pointer-events:none;z-index:-1;}';
    $css .= '@media (max-width:768px){.site-main::before{background-attachment:scroll;}}';
    echo '<style id="mica-bg-style">' . $css . '</style>' . "\n";
} );
