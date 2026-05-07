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
?>

<div class="container" style="padding-top:var(--space-6);padding-bottom:var(--space-16);">

    <!-- Hero Banner -->
    <section class="hero-banner" style="margin-bottom:var(--space-8);">
        <div class="hero-content">
            <!-- <span class="hero-eyebrow">🏪 Click &amp; Collect Available</span> -->
            <h1 class="hero-title">
                Everything for<br>
                <span class="accent">your home &amp; beyond</span>
            </h1>
            <p class="hero-subtitle">
                Hardware, tools, garden, paint &amp; more — shop online, collect at your nearest Mica store.
            </p>
            <div class="hero-ctas">
                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"
                   class="btn btn-primary btn-lg">
                    Shop All Products
                </a>
                <a href="#categories" class="btn btn-secondary btn-lg" style="background:rgba(255,255,255,.1);color:#fff;border-color:rgba(255,255,255,.4);">
                    Browse Categories
                </a>
            </div>
        </div>
    </section>

    <!-- Promo Stripe -->
    <div class="promo-stripe" style="border-radius:var(--radius-xl);margin-bottom:var(--space-8);padding:var(--space-3) var(--space-6);">
        <div class="promo-stripe-inner">
            <!-- <span class="promo-stripe-item">🚚 Free click &amp; collect at all stores</span> -->
            <span class="promo-stripe-item">🔒 Secure checkout with PayFast</span>
            <!-- <span class="promo-stripe-item">🛠️ Trade accounts available</span> -->
            <span class="promo-stripe-item">📦 Delivery between 5-7 days</span>
            <span class="promo-stripe-item">✅ 100% South African owned</span>
        </div>
    </div>

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
