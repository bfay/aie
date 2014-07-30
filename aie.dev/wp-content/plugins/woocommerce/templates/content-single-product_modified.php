<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="row">
		<div class="eight columns">
	

	<div class="row">
		<div class="product-image four columns">
		<img src="<?php echo do_shortcode('[wpv-post-field name="product_image"]'); ?>"/>
		<h5>Product Number: <?php echo do_shortcode('[wpv-post-field name="product_number"]'); ?></h5>
		</div>
		<div class="product-title eight columns">
			<h2><?php echo do_shortcode('[wpv-post-title]'); ?></h2>
			<h6><?php echo do_shortcode('[wpv-post-field name="subtitle"]'); ?></h6>
			<p><?php echo do_shortcode('[wpv-post-field name="product_highlights"]'); ?></p>
		</div>
	</div>
		</div> <!-- end eight columns -->
		
		


	<?php
		/**
		 * woocommerce_after_single_product_summary hook
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #product-<?php the_ID(); ?> -->

<div class="four columns">
<?php do_action( 'woocommerce_after_single_product' ); ?>
</div> <!-- end four columns -->
