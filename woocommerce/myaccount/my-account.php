<?php
/**
 * woocommerce/myaccount/my-account.php
 */
defined( 'ABSPATH' ) || exit;
?>
    <?php mica_breadcrumbs(); ?>

    <?php if ( is_user_logged_in() ) : ?>
        <div class="account-layout">
            <?php do_action( 'woocommerce_account_navigation' ); ?>
            <div class="account-content">
                <?php do_action( 'woocommerce_account_content' ); ?>
            </div>
        </div>
    <?php else : ?>
        <div class="account-auth-wrap">
            <?php do_action( 'woocommerce_account_content' ); ?>
        </div>
    <?php endif; ?>
