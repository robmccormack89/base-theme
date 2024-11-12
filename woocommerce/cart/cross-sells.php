<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( $cross_sells ) :
	
foreach ($cross_sells as $cross_sell) {
	$id = $cross_sell->get_id();
	$cross_sell_id_array[] = $id;
}
$context = Timber::context();
$context['cross_sells_title'] = _x( 'You may be interested in&hellip;', 'Cross-sell products: title', 'massey-theme' );
$context['cross_sells'] = Timber::get_posts( $cross_sell_id_array );
Timber::render( '_cross-sells.twig', $context );
	
endif;

wp_reset_postdata();
