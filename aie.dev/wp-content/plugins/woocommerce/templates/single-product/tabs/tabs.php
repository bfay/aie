<?php
/**
 * Single Product tabs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $tabs ) ) : ?>

	<div class="woocommerce-tabs">
		<section class="tabs">

    <ul class="tab-nav">
        <li class="active"><a href="#">Description</a></li>
        
        <li><a href="#">Data Sheet</a></li>
    </ul>

    <div class="tab-content active">
        <p><?php echo do_shortcode('[wpv-post-body]'); ?></p>
    </div>
   
    <div class="tab-content">
        <?php echo do_shortcode('[wpv-post-field name="spec_sheet"]'); ?>
    </div>

</section>	
</div>

<?php endif; ?>