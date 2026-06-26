<?php
/**
 * search.php — Search results page
 * Renders product results using the same grid/card markup as the shop archive.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="container">

    <?php mica_breadcrumbs(); ?>

    <div class="shop-layout">

        <!-- Sidebar (desktop) -->
        <?php mica_part( 'shop/filter-sidebar', [
            'scope_category_id' => 0,
            'active_filters'    => [
                'min_price'        => '',
                'max_price'        => '',
                'orderby'          => 'title',
                'on_sale'          => false,
                'in_stock'         => false,
                'out_of_stock'     => false,
                'tags'             => [],
                'attributes'       => [],
                'local_attributes' => [],
            ],
        ] ); ?>

        <!-- Main content -->
        <div class="shop-main" id="shop-main">

            <!-- Page title -->
            <div class="section-header mb-4">
                <div>
                    <h1 class="section-title">
                        <?php printf( esc_html__( 'Search results for "%s"', 'micaonline' ), esc_html( get_search_query() ) ); ?>
                    </h1>
                </div>
            </div>

            <!-- Toolbar: result count -->
            <div class="shop-toolbar">
                <span class="shop-result-count" id="result-count">
                    <?php
                    global $wp_query;
                    printf(
                        _n( '<strong>%d</strong> product', '<strong>%d</strong> products', $wp_query->found_posts, 'micaonline' ),
                        $wp_query->found_posts
                    );
                    ?>
                </span>
            </div>

            <!-- Results grid -->
            <div id="products-container">
                <?php if ( have_posts() ) : ?>
                    <div class="products-grid" id="products-grid">
                        <?php
                        while ( have_posts() ) {
                            the_post();
                            if ( get_post_type() !== 'product' ) {
                                continue;
                            }
                            $product = wc_get_product( get_the_ID() );
                            if ( $product ) {
                                mica_part( 'content/product-card', [ 'product' => $product ] );
                            }
                        }
                        ?>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-wrap" id="pagination-wrap">
                        <?php
                        echo paginate_links( [
                            'base'    => add_query_arg( 'paged', '%#%' ),
                            'format'  => '',
                            'total'   => $wp_query->max_num_pages,
                            'current' => max( 1, get_query_var( 'paged' ) ),
                            'type'    => 'list',
                        ] );
                        ?>
                    </div>

                <?php else : ?>
                    <?php mica_part( 'shop/no-products' ); ?>
                <?php endif; ?>
            </div><!-- #products-container -->

        </div><!-- .shop-main -->
    </div><!-- .shop-layout -->
</div><!-- .container -->

<!-- Mobile filter drawer -->
<div class="filter-drawer-overlay" id="filter-drawer-overlay"></div>
<div class="filter-drawer" id="filter-drawer" aria-label="<?php esc_attr_e( 'Filters', 'micaonline' ); ?>">
    <div class="filter-drawer-header">
        <span class="font-bold"><?php esc_html_e( 'Filter Products', 'micaonline' ); ?></span>
        <button class="btn btn-ghost btn-sm" id="filter-drawer-close">✕</button>
    </div>
</div>

<?php get_footer(); ?>
