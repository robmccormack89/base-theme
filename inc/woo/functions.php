<?php

// ajax routes for search form/s
function adv_live_search_ajax_restapi_routes($server) {
  $server->register_route( 'get_subcats', '/get_subcats', array(
    'methods'  => 'POST',
    'callback' => 'get_subcats',
  ));
  $server->register_route( 'get_submodels', '/get_submodels', array(
    'methods'  => 'POST',
    'callback' => 'get_submodels',
  ));
  $server->register_route( 'get_subcats_adv', '/get_subcats_adv', array(
    'methods'  => 'POST',
    'callback' => 'get_subcats_adv',
  ));
  $server->register_route( 'get_submodels_adv', '/get_submodels_adv', array(
    'methods'  => 'POST',
    'callback' => 'get_submodels_adv',
  ));
}

/* shop filters search form - get the subs */
function get_subcats($req) {
  $context = Timber::context();
	
	$parent_ids = $req['id'];
  $parent_slugs = $req['slug'];
  
  $childs_ids = get_term_children($parent_ids, 'product_cat');
  $context['parent_ids'] = $parent_ids;
  $data = null;
  
  if(!empty($parent_ids)){
    $subs_array = array();
    foreach ($parent_ids as $id) {
      $sub_terms = get_terms(array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'parent'     => $id
      ));
      $subs_array[] = $sub_terms; // data will now a set of one or more indexed arrays, for each checkbox checked. i need these arrays combined into one instead using array_merge(...$subs_array)
    }
  } else {
    return null;
  }
  // $merged_subs_array = array_merge(...$subs_array);
  
  $context['subcat_sel_terms'] = array_merge(...$subs_array);
	if(empty($context['subcat_sel_terms'])) return null;
	$data = Timber::compile(array('_subcats_select.twig'), $context);
  return $data;
  
}
function get_submodels($req) {
  $context = Timber::context();
	
	$parent_ids = $req['id'];
  $parent_slugs = $req['slug'];
  
  $childs_ids = get_term_children($parent_ids, 'product_series');
  $context['parent_ids'] = $parent_ids;
  $data = null;
  
  if(!empty($parent_ids)){
    $subs_array = array();
    foreach ($parent_ids as $id) {
      $sub_terms = get_terms(array(
        'taxonomy'   => 'product_series',
        'hide_empty' => true,
        'parent'     => $id
      ));
      $subs_array[] = $sub_terms; // data will now a set of one or more indexed arrays, for each checkbox checked. i need these arrays combined into one instead using array_merge(...$subs_array)
    }
  } else {
    return null;
  }
  // $merged_subs_array = array_merge(...$subs_array);
  
  $context['submodels_sel_terms'] = array_merge(...$subs_array);
	if(empty($context['submodels_sel_terms'])) return null;
	$data = Timber::compile(array('_submodels_select.twig'), $context);
  return $data;
}

/* advanced search form - get the subs */
function get_subcats_adv($req) {
	$context = Timber::context();
	
	$parent_id = $req['id'];
	$context['subcat_sel_terms'] = get_terms([
	  'taxonomy'    => 'product_cat',
	  'hide_empty'  => true,
	  'parent'      => $parent_id,
	]);
	
	if(empty($context['subcat_sel_terms'])) {
		return null;
	}
	
	$data = Timber::compile(array('_subcats_select_adv.twig'), $context);
  
  // return $req['id'];
  return $data;
}
function get_submodels_adv($req) {
	$context = Timber::context();
	
	$parent_id = $req['id'];
	$context['submodels_sel_terms'] = get_terms([
	  'taxonomy'    => 'product_series',
	  'hide_empty'  => true,
	  'parent'      => $parent_id,
	]);
	
	if(empty($context['submodels_sel_terms'])) {
		return null;
	}
	
	$data = Timber::compile(array('_submodels_select_adv.twig'), $context);
  return $data;
}

/**
*
* these functions are used for actions/filters which are added to the theme via ThemeWoo
* these are used to either add new content or modify existing ceontent within the woo part of the theme
*
*/

// add grid_list to the query_vars
function add_grid_list_query_vars($vars){ 
  $vars[] .= 'grid_list'; 
  return $vars; 
}

// add woo to the sites body class; this could be made conditional if necessary
function add_woocommerce_to_body_classes($classes){
  $stack = $classes;
  array_push($stack, 'woocommerce');
  return $stack;
}

/* Disable pagintion on what would be the shop-collection archives 

*/
function disable_pagination_on_collections($query) {
	if (!is_admin()) {
		if (is_product_cat() || is_product_series()) {
			$query->set('nopaging', true);
		}
	}
}

/* custom demo-store notice. will get added to theme locations via actions 

*/
function add_custom_demo_store_notice() {
    
  if (!is_store_notice_showing()) return;
  
  $notice = get_option('woocommerce_demo_store_notice'); 
  
  if (empty($notice)) $notice = _x( 'This is a demo store for testing purposes — no orders shall be fulfilled.', 'Store notice: default', 'massey-theme' ); 

  echo apply_filters(
    'woocommerce_demo_store', 
    '<div class="woocommerce-store-notice demo_store uk-position-z-index theme-border-top"><div class="store-notice-wrap">'.wp_kses_post($notice).' <a href="#" class="woocommerce-store-notice__dismiss-link">' . esc_html_x( 'Dismiss', 'Store notice: dismiss button', 'massey-theme' ) . ' <i class="fas fa-times"></i></a></div></div>', 
    $notice
  ); 
 
}

/* custom stock quantity markup 

*/
function custom_stock_quantity() {
  global $product; 
  $numleft = $product->get_stock_quantity(); 
  
  if (!$product->managing_stock()) {
    $data = "<span class='uk-text-success'>". _x( 'In stock', 'Stock quantites: in stock', 'massey-theme' ) ."</span>"; 
  }
  
  if (!$product->managing_stock() && !$product->is_in_stock()) {
    $data = "<span class='uk-text-danger'>". _x( 'Out of stock', 'Stock quantites: out of stock', 'massey-theme' ) ."</span>"; 
  }
  
  if ($product->managing_stock()) {
    if($numleft < 3) {
      $data = "<span class='uk-text-warning'>". sprintf( _x( 'Just %1$s left %2$s', 'Stock quantites: low item stock text', 'massey-theme' ), $numleft,  _x( 'In stock', 'Stock quantites: in stock', 'massey-theme' )) ."</span>";
    } else {
      $data = "<span class='uk-text-success'>". sprintf( _x( '%1$s left %2$s', 'Stock quantites: item stock levels okay', 'massey-theme' ), $numleft,  _x( 'In stock', 'Stock quantites: in stock', 'massey-theme' )) ."</span>";
    }
  }
  
  echo $data;
}

/* ajax_live_search 

*/
function __ajax_live_search() { 

  $data = array(
    'result' => 0,
    'response' => ''
  );

  $query = $_POST['query'];
  $query_string_upper = ucwords($query);

  if (!empty($query)) {

    $data['result'] = 1;

    $cat_args = array(
      'fields' => 'all',
      'name__like' => $query, 
    );
    $product_categories = get_terms( 'product_cat', $cat_args );

    $products = wc_get_products( array(
      'status' => 'publish',
      'limit' => -1,
      'search_term' => $query,
      'meta_query' => array(
        array(
          'key' => '_stock_status',
          'value' => 'instock'
        ),
      )
    ));
    
    $matching_terms = get_terms( array(
      'taxonomy' => 'product_cat',
      'fields' => 'slugs',
      'name__like' => $query, 
    ));
    $products_in_cat_args = array(
      'posts_per_page' => -1,
      'post_type' => 'product',
      'orderby' => 'title',
      'meta_query' => array(
        array(
          'key' => '_stock_status',
          'value' => 'instock'
        ),
      ),
      'tax_query' => array(
        'relation' => 'AND',
        array(
          'taxonomy' => 'product_cat',
          'field' => 'slug',
          'terms' => $matching_terms
        ),
      ),
    );
    $products_in_cat = new Timber\PostQuery($products_in_cat_args);
    
    $products_with_sku = wc_get_products( array(
      'status' => 'publish',
      'limit' => -1,
      'sku' => $query
    ));
    
    $context['product_categories'] = $product_categories;
    $context['products'] = $products;
    $context['products_in_cat'] = $products_in_cat;
    $context['products_with_sku'] = $products_with_sku;
    $context['query_string_upper'] = $query_string_upper;
    $response = Timber::compile(array('_live-search-results.twig'), $context);

    $data['response'] = $response;

  }

  echo json_encode($data);

  die();
  
}
function support_search_term_query_var( $query, $query_vars ) { // support 's' -> 'search_term' matching; for ajax_live_search 
  if( empty( $query_vars['search_term'] ) ) {
    return $query;
  }
  $query['s'] = $query_vars['search_term'];
  return $query;
}

/* Make SKU work in product searches 

*/
function make_sku_work_in_product_searches($query) {
	// conditions - change the post type clause if you're not searching woocommerce or 'product' post type
	if (is_admin() || !$query->is_main_query() || !$query->is_search() || !get_query_var('post_type') == 'product') {
		return;
	}
	add_filter('posts_join', 'az_search_join');
	add_filter('posts_where', 'az_search_where');
	add_filter('posts_groupby', 'az_search_groupby');

}
function az_search_join($join) {
	global $wpdb;
	$join .= " LEFT JOIN $wpdb->postmeta gm ON (" . $wpdb->posts . ".ID = gm.post_id AND gm.meta_key='_sku')"; // change to your meta key if not woo
	return $join;
}
function az_search_where($where) {
	global $wpdb;
	$where = preg_replace("/\(\s*{$wpdb->posts}.post_title\s+LIKE\s*(\'[^\']+\')\s*\)/", "({$wpdb->posts}.post_title LIKE $1) OR (gm.meta_value LIKE $1)", $where);
	return $where;
}
function az_search_groupby($groupby) {
	global $wpdb;
	$mygroupby = "{$wpdb->posts}.ID";
	if (preg_match("/$mygroupby/", $groupby)) {
		// grouping we need is already there
		return $groupby;
	}
	if (!strlen(trim($groupby))) {
		// groupby was empty, use ours
		return $mygroupby;
	}
	// wasn't empty, append ours
	return $groupby . ", " . $mygroupby;
}
function get_product_by_sku( $sku ) { // get a product by a given sku. is this being used anywhere??? 
  global $wpdb;

  $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );

  if ( $product_id ) return new WC_Product( $product_id );

  return null;
}

/* custom subtotal & result count markup with ajax 

*/
function result_count_ajaxify( $fragments ) { 
  $fragments['span.header-cart-count'] = '<span class="header-cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
  return $fragments;
}
function subtotal_ajaxify( $fragments ) { 
  $fragments['span.subtotal-cart'] = '<span class="subtotal-cart">' . WC()->cart->get_cart_subtotal() . '</span>';
  return $fragments;
}

/* filters the icon for remove links on cart 

*/
function filter_cart_remove_link_icon_html( $sprintf, $cart_item_key ) {
  if ( is_admin() && ! defined( 'DOING_AJAX' ) )
  return $sprintf;
  $sprintf = str_replace('&times;', '<i class="fas fa-times"></i>', $sprintf);
  return $sprintf;
};

/* Product post type labels - strings for translations defined in helpers

*/
function get_product_post_type_labels($single,$plural){
  $arr = array(
    'name' => $plural,
    'singular_name' => $single,
    'menu_name' => $plural,
    'add_new' => 'Add '.$single,
    'add_new_item' => 'Add New '.$single,
    'edit' => 'Edit',
    'edit_item' => 'Edit '.$single,
    'new_item' => 'New '.$single,
    'view' => 'View '.$plural,
    'view_item' => 'View '.$single,
    'search_items' => 'Search '.$plural,
    'not_found' => 'No '.$plural.' Found',
    'not_found_in_trash' => 'No '.$plural.' Found in Trash',
    'parent' => 'Parent '.$single
  );
  return $arr;
}
function filter_product_post_type_labels( $args ){
  $new_labels = product_posttype_labels();
  $labels = get_product_post_type_labels( 
    $new_labels['singular'], 
    $new_labels['plural']
  );
  $args['labels'] = $labels;
  return $args;
}

/* sorting options labels - defined in helpers

*/
function filter_shop_sorting_options_labels( $options ){ 
  
  $new_options = shop_sorting_options_labels();
	
	$options['price'] = $new_options['price'];
	$options['price-desc'] = $new_options['price-desc'];
	$options['menu_order'] = $new_options['menu_order'];
	$options['popularity'] = $new_options['popularity'];
	$options['rating'] = $new_options['rating'];
	$options['date'] = $new_options['date'];
	
	return $options;
  
}

/* Custom product tab 

*/
function add_parts_custom_tab( $tabs ) {
	$tabs['parts_custom_tab'] = array(
		'title'    => _x('Parts information', 'Custom product tab', 'massey-theme'),
		'callback' => 'parts_custom_tab_content', // the function name, which is on line 15
		'priority' => 5,
	);
	return $tabs;
}
function parts_custom_tab_content( $slug, $tab ) {
  
  global $product;
  
  $context['post'] = Timber::get_post();
  $product = wc_get_product( $context['post']->ID );
  $context['product'] = $product;
  
  wp_reset_postdata();
  
  Timber::render( '_data-tab.twig', $context );
  
}