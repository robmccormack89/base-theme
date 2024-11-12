<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$orderby_sort = '';
if (isset($_GET['orderby'])) {
  $orderby_sort = $_GET['orderby'];
}

?>
<ul class="uk-nav uk-nav-default uk-margin-remove ajax-ordering uk-panel uk-panel-scrollable">
<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
	<li class="uk-parent uk-position-relative <?php if ($orderby_sort == esc_attr($id)) { echo 'uk-active'; } ?>">
		<a data-link="<?php echo esc_url(add_query_arg(array('orderby' => esc_attr($id)))); ?>" >
			<div 
			class="uk-radio uk-hidden" 
			<?php if ($orderby_sort == esc_attr($id)) { echo 'checked'; } ?>
			></div>
			<?php echo esc_html( $name ); ?>
		</a>
	</li>
<?php endforeach; ?>
</ul>
<a class="uk-link-text uk-text-primary uk-text-small filters-reset-link" data-link="<?php echo esc_url(remove_query_arg(array('orderby'))); ?>"><?php _x( 'Reset', 'Shop filters: reset label', 'massey-theme' ) ?></a>
