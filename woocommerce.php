<?php
 /**
 *
 * The template for making woocommerce work with timber/twig. sets the templates & context for woo's archive & singular views
 *
 * @package Rmcc_Theme
 *
 */
 
// namespace stuff
namespace Rmcc;
use Timber\Post;
use Timber\PostQuery;

// make sure timber is activated first
if (!class_exists('Timber')) {
  echo 'Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>';
  return;
}

// get the main context
$context = Theme::context();

if (is_singular('product')) {
  
  $templates = array('product.twig', 'single.twig', 'base.twig');
  
  // Set post & product objects
  $context['post'] = new Post();
  $context['product'] = wc_get_product($context['post']->ID);
  
  // Get related products
  $related_limit = 12;
  $related_ids = wc_get_related_products($context['post']->id, $related_limit);
  $context['related_products_title'] = apply_filters('woocommerce_product_related_products_heading', _x('Related products', 'Related products: title', 'massey-theme'));
  $context['related_products'] = null;
  if ($related_ids) $context['related_products'] = new PostQuery($related_ids);
  
  // get upsells
  $upsell_ids = $context['post']->_upsell_ids;
  $context['up_sells_title'] = apply_filters('woocommerce_product_upsells_products_heading', _x('You may also like', 'Upsell products: title', 'massey-theme'));
  $context['up_sells'] = null;
  if ($upsell_ids) $context['up_sells'] = new PostQuery($upsell_ids);
  
  wp_reset_postdata();
  
  Theme::render($templates, $context);
  
} else {
  
  // if were not on a singular product anymore, then we must be on a woocommerce archive!
  $context['posts'] = new PostQuery(); // get the main posts object via PostQuery 
  
  // define our archive templates arrays
  $templates = array('shop.twig', 'archive.twig', 'base.twig');
  $tease_template = array('_tease-product.twig');
  
  $products_grid_columns = wc_get_loop_prop('columns'); // gets the woocommerce columns per row setting 
  
  if (get_query_var('grid_list') == 'list-view') { // if grid_list = 'list', set products_grid_columns to 1 & unshit tease_template with the list one 
    $products_grid_columns = 1;
  	array_unshift($tease_template, '_tease-product-wide.twig');
  };
  
  if (is_tax()) { // if any taxonomy archive, set some general term vars & the archive title 
    $queried_object = get_queried_object();
    $term_id = $queried_object->term_id;
    $context['term_slug'] = $queried_object->slug;
    $context['term_id'] = $term_id;
    $context['title'] = single_term_title('', false); // e.g: 'Clothing'
  };
  
  /**
  *
  * Clickthru system functionality...
  * if is strictly a 'product_category' term archive & not an archive with query params like ?product_cat=
  *
  */
  if (is_product_cat()) { // here we do the funny business with the product category archives 
  
    $context['category'] = get_term($term_id, 'product_cat');
  
    $terms = get_terms([ // Get subcategories of the current category  
      'taxonomy'    => 'product_cat',
      'hide_empty'  => true,
      'parent'      => $term_id,
    ]); 
  
    if(!empty($terms)){
  
      foreach($terms as $item) { // loop thru the terms & set thumb data & link
        $item->thumbnail = get_product_term_attachments($item->term_id);
        $item->link = get_term_link($item->term_id);
      };
  
      // if is a strict taxonomy-term archive & 'product_series' is queried in the url (?product_series=)
      // this is where the click-thru system for the product_series switches over to the product-category taxonomy archive with product_series query params
      // in this instance, we will appear to be going thru the product_series routing. but is actually in product_category
      if (is_product_cat() == true && current_product_series_var() == true) {
  
        $terms = null; // start at null. cause an error on shop page. if product_series query is valid, it will return new terms below... 
  
        $current_series_slug = get_query_var('product_series'); // get the value for ?product_series= 
  
        $posts_from_queried_series = get_posts(// get posts (only the ids as an array) using the queried series 
          array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'fields' => 'ids', // return an array of ids
            'tax_query' => array(
              array(
                'taxonomy' => 'product_series',
                'field' => 'slug',
                'terms' => $current_series_slug,
             )
           )
         )
       );
  
        if(!empty($posts_from_queried_series)){
  
          // & finally reset the sub_terms variable by getting the product_cats associated with posts_from_queried_series  
          $terms = get_terms([ // we now have the sub-categories of the current category, that have been assigned to products, that ALSO have the current product_series query vars assigned to them 
            'taxonomy'    => 'product_cat',
            'hide_empty'  => true,
            'parent'      => $term_id,
            'object_ids' => $posts_from_queried_series,
          ]);
  
          if(!empty($terms)){ // if we have terms again, loop thru & set term thumb data & link
            foreach($terms as $item) {
              $item->thumbnail = get_product_term_attachments($item->term_id);
              $item->link = get_term_link($item->term_id) . '?product_series=' . $current_series_slug;
            };
          };
  
        }
  
      }
  
    };
  
  };
  
  /**
  *
  * More clickthru system functionality stuff...
  * if is strictly a 'product_series' term archive & not an archive with query params like ?product_series=
  *
  */
  if (is_product_series()) { // here we do the funny business with the product series archives 
  
    $context['series'] = get_term($term_id, 'product_series');
  
    // Get models of the current series 
    // $context['term_subs']
    $terms = get_terms([
      'taxonomy'    => 'product_series',
      'hide_empty'  => true,
      'parent'      => $term_id,
    ]);
  
    if(!empty($terms)) { // if we have some terms (some models) 

      foreach($terms as $item) { // loop thru & set the term thumb data & link 
        $item->thumbnail = get_product_term_attachments($item->term_id);
        $item->link = get_term_link($item->term_id); // set term link
      };
  
    } else { // if terms (models) is empty(if the current product_series has no more subs to show, then we will need to show product_cats or posts next) 
  
      $cat_ids = array(); // the cat ids for each product will be in an array, otherwise its an empty array
  
      if(!empty($context['posts'])){ // if posts exist for current global query
        foreach($context['posts'] as $item) { // loop thru the posts of the current loop, to get the ids of any product_cats that have been assigned 
  
          $product_cats = get_the_terms($item->get_id(), 'product_cat'); // get the product_cats of the post
  
          if(!empty($product_cats)){ // if we have product_cats in the post
            foreach($product_cats as $product_cat) { // loop thru & save ids as array 
              $cat_ids[] .= $product_cat->term_id;
            }
          }
  
        };
      }
  
      if(!empty($cat_ids)){ // if we have cat_ids from above... 
  
        $terms = get_terms([ // now we get the categories (parent-only) related to products from the given series 
          'taxonomy'    => 'product_cat',
          'hide_empty'  => true,
          'parent' => 0,
          'include' => $cat_ids,
        ]);
  
        if(!empty($terms)){ // if we have new cats... 
          foreach($terms as $item) { // loop thru & set the term thumb data & link 
            $item->thumbnail = get_product_term_attachments($item->term_id);
            $item->link = get_term_link($item->term_id) . '?product_series=' . $context['term_slug'];
          };
        }
  
      }
  
    };
  
  };
  
  if (is_shop()) {
  
    if((!is_product_cat() && !is_product_series()) || (current_product_cat_var() == true || current_product_series_var() == true)) { // if not a cat/series tax term archive or if not a cat query or series query 
      $shop_page_id = wc_get_page_id('shop'); 
      $context['title'] = get_the_title($shop_page_id);  // e.g: 'Shop'
      // $context['description'] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam';
    };
  
  };
  
  // if is product category tax archive
  if (is_product_cat()) {
    $quer_obj = get_queried_object();
    $context['product_cat_obj'] = $quer_obj->slug;
  }
  // if is product cat query var
  elseif (current_product_cat_var() == true) {
    $context['product_cat_obj'] = get_query_var('product_cat');
  };
  
  // if is product series
  if (is_product_series()) {
    $quer_obj = get_queried_object();
    $context['product_series_obj'] = $quer_obj->slug;
  }
  // if is product series query var
  elseif (current_product_series_var() == true ) {
    $context['product_series_obj'] = get_query_var('product_series');
  };
  
  // set contexts of tease_template & tease_term_template
  $context['tease_template'] = $tease_template; 
  
  /**
  *
  * Yet more clickthru system functionality stuff...
  * if terms exist, set the terms context & add the terms collection template to start of templates array
  *
  */
  if(!empty($terms)){
    $context['terms'] = $terms;
    array_unshift($templates, 'shop-collection.twig');
  }
  
  // set grid widths contexts based on products_grid_columns
  if($products_grid_columns == 1) {
    $context['grid_classes'] = 'uk-child-width-1-1';
  }
  elseif($products_grid_columns == 2) {
    $context['grid_classes'] = 'uk-child-width-1-2';
  }
  elseif($products_grid_columns == 3) {
    $context['grid_classes'] = 'uk-child-width-1-2 uk-child-width-1-3@m';
  }
  elseif($products_grid_columns == 4) {
    $context['grid_classes'] = 'uk-child-width-1-2 uk-child-width-1-3@m uk-child-width-1-4@l';
  }
  elseif($products_grid_columns >= 5) {
    $context['grid_classes'] = 'uk-child-width-1-2 uk-child-width-1-3@m uk-child-width-1-4@l uk-child-width-1-'.$products_grid_columns.'@xl';
  }
  
  Theme::render($templates, $context);
}