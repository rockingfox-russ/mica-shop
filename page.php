<?php
/**
 * page.php — Generic page template
 */
get_header();
while ( have_posts() ) : the_post();

    // Cart, Checkout, and My Account are WP Pages with WC shortcodes —
    // their own templates already render breadcrumbs + heading, so skip
    // the generic wrapper here to avoid double breadcrumbs/titles.
    $is_wc_chrome_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );

    if ( $is_wc_chrome_page ) : ?>
        <?php the_content(); ?>
    <?php else : ?>
        <div class="container" style="padding-top:var(--space-8);padding-bottom:var(--space-12);">
            <?php mica_breadcrumbs(); ?>
            <article>
                <h1 style="font-size:var(--font-size-4xl);margin-bottom:var(--space-6);"><?php the_title(); ?></h1>
                <div class="entry-content" style="line-height:1.75;color:var(--clr-text-muted);">
                    <?php the_content(); ?>
                </div>
            </article>
        </div>
    <?php endif;

endwhile;
get_footer();
