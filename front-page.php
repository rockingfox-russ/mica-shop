<?php
/**
 * front-page.php — Homepage
 */

defined( 'ABSPATH' ) || exit;
get_header();

$top_cats        = mica_get_categories( 0 );
$featured_args   = mica_build_query_args( [ 'orderby' => 'popularity' ] );
$featured_args['posts_per_page'] = 8;
$featured_query  = new WP_Query( $featured_args );

$sale_args  = mica_build_query_args( [ 'on_sale' => true ] );
$sale_args['posts_per_page'] = 8;
$sale_query = new WP_Query( $sale_args );

$hero_slides = mica_get_hero_slides();

$promo_items = array_filter( [
    get_theme_mod( 'mica_promo_stripe_free_delivery', '' ),
    get_theme_mod( 'mica_promo_stripe_2', '🔒 Secure checkout with PayFast' ),
    get_theme_mod( 'mica_promo_stripe_3', '📦 Delivery between 2-3 days' ),
    get_theme_mod( 'mica_promo_stripe_4', '✅ 100% South African owned' ),
] );
?>

<div class="container" style="padding-top:var(--space-6);padding-bottom:var(--space-16);">

    <!-- Hero Slider -->
    <section class="hero-slider" id="hero-slider" style="margin-bottom:var(--space-8);"
              aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Promotional highlights', 'micaonline' ); ?>">
        <div class="hero-slides-track">
            <?php foreach ( $hero_slides as $i => $slide ) : ?>
            <div class="hero-slide<?php echo $i === 0 ? ' active' : ''; ?>"
                 <?php if ( $slide['image'] ) : ?>style="background-image:url('<?php echo esc_url( $slide['image'] ); ?>');"<?php endif; ?>>
                <div class="hero-content">
                    <h1 class="hero-title"><?php echo wp_kses_post( $slide['title'] ); ?></h1>
                    <?php if ( $slide['subtitle'] ) : ?>
                    <p class="hero-subtitle"><?php echo wp_kses_post( $slide['subtitle'] ); ?></p>
                    <?php endif; ?>
                    <div class="hero-ctas">
                        <a href="<?php echo esc_url( $slide['link'] ); ?>" class="btn btn-primary btn-lg">
                            <?php echo esc_html( $slide['button_text'] ); ?>
                        </a>
                        <a href="#categories" class="btn btn-secondary btn-lg" style="background:rgba(255,255,255,.1);color:#fff;border-color:rgba(255,255,255,.4);">
                            Browse Categories
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ( count( $hero_slides ) > 1 ) : ?>
        <button type="button" class="hero-arrow hero-arrow-prev" aria-label="<?php esc_attr_e( 'Previous slide', 'micaonline' ); ?>"><?php echo mica_icon( 'chevron' ); ?></button>
        <button type="button" class="hero-arrow hero-arrow-next" aria-label="<?php esc_attr_e( 'Next slide', 'micaonline' ); ?>"><?php echo mica_icon( 'chevron' ); ?></button>
        <div class="hero-dots">
            <?php foreach ( $hero_slides as $i => $slide ) : ?>
            <button type="button" class="hero-dot<?php echo $i === 0 ? ' active' : ''; ?>" data-index="<?php echo (int) $i; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'micaonline' ), $i + 1 ) ); ?>"<?php echo $i === 0 ? ' aria-current="true"' : ''; ?>></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="sr-only" aria-live="polite" id="hero-slide-announcer"></div>
    </section>

    <!-- Promo Stripe -->
    <?php if ( ! empty( $promo_items ) ) : ?>
    <div class="promo-stripe" style="border-radius:var(--radius-xl);margin-bottom:var(--space-8);padding:var(--space-3) var(--space-6);">
        <div class="promo-stripe-inner">
            <?php foreach ( $promo_items as $item ) : ?>
            <span class="promo-stripe-item"><?php echo esc_html( $item ); ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Categories -->
    <section id="categories" style="margin-bottom:var(--space-10);">
        <div class="section-header">
            <h2 class="section-title">Browse Categories</h2>
            <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="section-link">
                View all →
            </a>
        </div>
        <div class="category-grid">
            <?php foreach ( $top_cats as $cat ) :
                $thumb_id  = get_term_meta( $cat->term_id, 'thumbnail_id', true );
                $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '';
            ?>
            <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="category-card">
                <div class="category-card-icon">
                    <?php if ( $thumb_url ) : ?>
                        <img src="<?php echo esc_url( $thumb_url ); ?>"
                             alt="<?php echo esc_attr( $cat->name ); ?>"
                             style="object-fit:contain;">
                    <?php else : ?>
                        <?php echo mica_icon( 'store', '' ); ?>
                    <?php endif; ?>
                </div>
                <span class="category-card-name"><?php echo esc_html( $cat->name ); ?></span>
                <span class="category-card-count"><?php echo (int) $cat->count; ?> products</span>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Featured / Popular Products -->
    <?php if ( $featured_query->have_posts() ) : ?>
    <section style="margin-bottom:var(--space-10);">
        <div class="section-header">
            <h2 class="section-title">Popular Products</h2>
            <a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>"
               class="section-link">View all →</a>
        </div>
        <div class="products-grid">
            <?php while ( $featured_query->have_posts() ) :
                $featured_query->the_post();
                $product = wc_get_product( get_the_ID() );
                if ( $product ) mica_part( 'content/product-card', [ 'product' => $product ] );
            endwhile;
            wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- On Sale -->
    <?php if ( $sale_query->have_posts() ) : ?>
    <section style="margin-bottom:var(--space-10);">
        <div class="section-header">
            <h2 class="section-title" style="color:var(--clr-orange);">🔥 On Sale</h2>
            <a href="<?php echo esc_url( add_query_arg( 'on_sale', '1', get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>"
               class="section-link">View all deals →</a>
        </div>
        <div class="products-grid">
            <?php while ( $sale_query->have_posts() ) :
                $sale_query->the_post();
                $product = wc_get_product( get_the_ID() );
                if ( $product ) mica_part( 'content/product-card', [ 'product' => $product ] );
            endwhile;
            wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Trust signals -->
    <section style="margin-bottom:var(--space-10);">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:var(--space-4);">
            <?php
            $trust = [
                // [ 'icon' => '🏪', 'title' => 'Click & Collect',    'desc' => 'Pick up at your nearest Mica store — often same day.' ],
                [ 'icon' => '🔒', 'title' => 'Secure Payments',    'desc' => 'PayFast. transactions encrypted.' ],
                [ 'icon' => '🛠️', 'title' => 'Expert Advice',      'desc' => 'Our staff know hardware. Ask anything in-store.' ],
                [ 'icon' => '↩️', 'title' => 'Easy Returns',       'desc' => '30-day returns. No questions asked on unopened items.' ],
            ];
            foreach ( $trust as $t ) : ?>
            <div class="card" style="padding:var(--space-5);display:flex;gap:var(--space-4);align-items:flex-start;">
                <span style="font-size:28px;flex-shrink:0;"><?php echo $t['icon']; ?></span>
                <div>
                    <strong style="display:block;margin-bottom:4px;font-size:var(--font-size-sm);">
                        <?php echo esc_html( $t['title'] ); ?>
                    </strong>
                    <span class="text-sm text-muted"><?php echo esc_html( $t['desc'] ); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

</div>

<?php get_footer(); ?>
