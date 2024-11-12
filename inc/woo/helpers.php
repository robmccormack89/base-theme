<?php

/**
*
* labels for translations (translatable strings) 
*
*/

function product_posttype_labels(){ 
	
	$new_options['singular'] = _x( 'Tractor Part', 'Product post-type label: singular', 'massey-theme' );
	$new_options['plural'] = _x( 'Tractor Parts', 'Product post-type label: plural', 'massey-theme' );
	
	return $new_options;
  
}
function shop_sorting_options_labels(){ 
	
	$new_options['price'] = _x( 'Sort by price: low to high', 'Shop sorting options', 'massey-theme' );
	$new_options['price-desc'] = _x( 'Sort by price: high to low', 'Shop sorting options', 'massey-theme' );
	$new_options['menu_order'] = _x( 'Default sorting', 'Shop sorting options', 'massey-theme' );
	$new_options['popularity'] = _x( 'Sort by popularity', 'Shop sorting options', 'massey-theme' );
	$new_options['rating'] = _x( 'Sort by rating', 'Shop sorting options', 'massey-theme' );
	$new_options['date'] = _x( 'Sort by latest', 'Shop sorting options', 'massey-theme' );
	
	return $new_options;
  
}
function gridlist_labels(){ 
	
	$new_options['grid'] = _x( 'Grid View', 'Grid/List: grid label', 'massey-theme' );
	$new_options['list'] = _x( 'List View', 'Grid/List: list label', 'massey-theme' );
	
	return $new_options;
  
}

/**
*
* Used mainly for either clickthru system or shop filtering
*
*/

/* used for custom filters options 

*/
function gridlist_badge_name() {
	
	$labels = gridlist_labels();
	
	if (get_query_var('grid_list') == 'grid-view') {
    $name = $labels['grid'];
  }
	elseif (get_query_var('grid_list') == 'list-view') {
    $name = $labels['list'];
  }
	
  return $name;
}
function orderby_badge_name() {
	
	$new_options = shop_sorting_options_labels();
	
	if ($_GET['orderby'] == 'price') {
    $name = $new_options['price'];
  }
	elseif ($_GET['orderby'] == 'price-desc') {
    $name = $new_options['price-desc'];
  }
	elseif ($_GET['orderby'] == 'menu_order') {
    $name = $new_options['menu_order'];
  }
	elseif ($_GET['orderby'] == 'popularity') {
    $name = $new_options['popularity'];
  }
	elseif ($_GET['orderby'] == 'rating') {
    $name = $new_options['rating'];
  }
	elseif ($_GET['orderby'] == 'date') {
    $name = $new_options['date'];
  }
	
  return $name;
}

/* Getting the current query var value/s with given key/s 

*/
function current_product_cat_var() {
  if (isset($_GET['product_cat'])) {
    return $_GET['product_cat'];
  }
};
function current_product_series_var() {
  if (isset($_GET['product_series'])) {
    return $_GET['product_series'];
  }
};
function current_product_orderby_var() {
  if (isset($_GET['orderby'])) {
    return $_GET['orderby'];
  }
};
function current_product_gridlist_var() {
  if (isset($_GET['grid_list'])) {
    return $_GET['grid_list'];
  }
};

/* Getting the data for the product taxonomy filters (category & series) 

	& when subs exists, getting them too

*/
function product_cats_for_filters() { 
	// get the product cat filters 
  $cats_args = array(
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
    'orderby' => 'slug',
    'parent' => 0,
  );
  return get_terms($cats_args);
}
function product_cat_has_children($term_id) { 
	// check to see if product cat has children 
  if ( count( get_term_children( $term_id, 'product_cat' ) ) > 0 ) return true;
  return false;
};
function sub_cats_for_filters($term_id) { 
	// get the sub product_cat filters based on parent term_id 
  $subs_cats_args = array(
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
    'orderby' => 'slug',
    'parent' => $term_id,
  );
  return get_terms($subs_cats_args);
}
function product_series_for_filters() { 
	// get the product series filters 
  $cats_args = array(
    'taxonomy' => 'product_series',
    'hide_empty' => true,
    'orderby' => 'slug',
    'parent' => 0,
  );
  return get_terms($cats_args);
}
function product_series_has_children($term_id) { 
	// check to see if product series has children 
  if ( count( get_term_children( $term_id, 'product_series' ) ) > 0 ) return true;
  return false;
};
function sub_series_for_filters($term_id) { 
	// get the sub series filters based on parent term_id 
  $subs_cats_args = array(
    'taxonomy' => 'product_series',
    'hide_empty' => true,
    'orderby' => 'slug',
    'parent' => $term_id,
  );
  return get_terms($subs_cats_args);
}

/* Getting the links for the product filters using add_query_args/remove_query-args; 

	Checks whether current url is an archive for a taxonomy (category & series),
	then when using filters, adds/removes the correct query-args, with the shop base, minus the taxonomy archive path,
	e.g: /product-category/clothing/hoodies -> /shop/?product_cat=hoodies

*/
function add_query_arg_product_cats_for_filters($cat_slug) { 
	// add query arg link for product_cats in filters & escape the url 
  // set the query arg url for product_cat from the product_cat->slug
  $query_arg_product_cats_args = array(
    'product_cat' => $cat_slug,
  );
  $the_url = add_query_arg($query_arg_product_cats_args);
  // parse the url into an array
  $parsed_url = wp_parse_url($the_url);
  // get the page path from the array
  $url_path = $parsed_url['path'];
  // the value we will use to search for in string
  $key_value = 'category'; 
  // find key_value string in the page path
  $found = strpos_recursive($url_path, $key_value);
  // if the 'category' exists in the current page path
  if($found) {
    // before new path
    $new_path = get_permalink(wc_get_page_id('shop'));
    // remove the current url page path from url string & set new path
    $new_path .= str_replace($url_path,'/',$the_url);
  } else {
    // if 'category' doesnt exist in current page url, set the add_query_arg url to the default state abouve
    $new_path = $the_url;
  }
	// remove_query_arg('paged', $new_path);
	$new_path = preg_replace("/page\/.\//", '', $new_path);
  // return the new path
  return esc_url($new_path);
}
function remove_query_arg_product_cats_for_filters() { 
	// add query arg link for product_cats in filters & escape the url 
  // paths
  $current_uri = home_url( add_query_arg( NULL, NULL ) ); // full current url with params
  $current_url_page_path = strtok($_SERVER["REQUEST_URI"], '?'); // with https & host
  // $current_url_page_path = strtok($current_uri, '?'); // without https & host
  $current_url_query_string = $_SERVER['QUERY_STRING']; // isolated query string: key=value
  // shop base url
  $shop_base = get_permalink(wc_get_page_id('shop'));
  // key values
  $query_key_value = 'product_cat';
  $cat_key_value = 'product-category';
  // check keys in paths
  $found_in_query = strpos_recursive($current_url_query_string, $query_key_value);
  $found_in_path = strpos_recursive($current_url_page_path, $cat_key_value);
	$output = $current_uri;
  // if query_string exists & contains product_cat, remove product_cat query value
  if(($found_in_query)&&(!empty($current_url_query_string))) {
    $output = remove_query_arg('product_cat');
  };
  // if page path exists & contains product-category
  if(($found_in_path)&&(!empty($current_url_page_path))) {
    $strp_path = substr_replace($current_url_page_path, '', strrpos($current_url_page_path, '/'), 1);
    $output = str_replace(home_url().$strp_path, rtrim($shop_base, '/'), rtrim($current_uri, '/'));
  };
  // return url with esc
  return esc_url($output);  
}
function add_query_arg_product_series_for_filters($cat_slug) { 
	// add query arg link for product_series in filters & escape the url 
  // set the query arg url for product_cat from the product_cat->slug, removing _pjax
  $query_arg_product_cats_args = array(
    'product_series' => $cat_slug,
  );
  $the_url = add_query_arg($query_arg_product_cats_args);
  // parse the url into an array
  $parsed_url = wp_parse_url($the_url);
  // get the page path from the array
  $url_path = $parsed_url['path'];
  // the value we will use to search for in string
  $key_value = 'product-series-model'; 
  // find key_value string in the page path
  $found = strpos_recursive($url_path, $key_value);
  // if the 'category' exists in the current page path
  if($found) {
    // before new path
    $new_path = get_permalink(wc_get_page_id('shop'));
    // remove the current url page path from url string & set new path
    $new_path .= str_replace($url_path,'/',$the_url);
  } else {
    // if 'category' doesnt exist in current page url, set the add_query_arg url to the default state abouve
    $new_path = $the_url;
  }
	// remove_query_arg('paged', $new_path);
	$new_path = preg_replace("/page\/.\//", '', $new_path);
  // return the new path
  return esc_url($new_path);
}
function remove_query_arg_product_series_for_filters() { 
	// add query arg link for product_series in filters & escape the url 
  // paths
  $current_uri = home_url( add_query_arg( NULL, NULL ) ); // full current url with params
  $current_url_page_path = strtok($_SERVER["REQUEST_URI"], '?'); // with https & host
  // $current_url_page_path = strtok($current_uri, '?'); // without https & host
  $current_url_query_string = $_SERVER['QUERY_STRING']; // isolated query string: key=value
  // shop base url
  $shop_base = get_permalink(wc_get_page_id('shop'));
  // key values
  $query_key_value = 'product_series';
  $cat_key_value = 'product-series-model';
  // check keys in paths
  $found_in_query = strpos_recursive($current_url_query_string, $query_key_value);
  $found_in_path = strpos_recursive($current_url_page_path, $cat_key_value);
	$output = $current_uri;
  // if query_string exists & contains product_cat, remove product_cat query value
  if(($found_in_query)&&(!empty($current_url_query_string))) {
    $output = remove_query_arg('product_series');
  };
  // if page path exists & contains product-category
  if(($found_in_path)&&(!empty($current_url_page_path))) {
		$strp_path = substr_replace($current_url_page_path, '', strrpos($current_url_page_path, '/'), 1);
    $output = str_replace(home_url().$strp_path, rtrim($shop_base, '/'), rtrim($current_uri, '/'));
  };
  // return url with esc
  return esc_url($output);  
}

/* Checks to see if we are on a taxonomy archive; as opposed to query var archive 

*/
function is_product_series() { 
	// check if is product-series via uri paramaters 
  $current_url_page_path = strtok($_SERVER["REQUEST_URI"], '?'); 
  $cat_key_value = 'product-series-model';
  $found_in_path = strpos_recursive($current_url_page_path, $cat_key_value);
  if($found_in_path) {
    return true;
  };
}
function is_product_cat() { 
	// check if is product-cat via uri paramaters 
  $current_url_page_path = strtok($_SERVER["REQUEST_URI"], '?'); 
  $cat_key_value = 'product-category';
  $found_in_path = strpos_recursive($current_url_page_path, $cat_key_value);
  if($found_in_path) {
    return true;
  };
}

/* Get the parent terms ONLY from a given taxonomy 

*/
function get_parent_product_terms($tax = 'product_cat') { 
	$terms = get_terms([
	  'taxonomy'    => $tax,
	  'hide_empty'  => true,
	  'parent' => 0,
	]);
	return $terms;
}

/* Getting the thumb attachments of a product term, like 'Series ABC' 

	product_series uses acf field & product_cat uses built-in meta field
	this function will be called when getting the thumbnail attachment of a given term	

*/
function get_product_term_attachments($term_id) {
	
	$thumb_src = null;
  $thumb_alt = null;
  
  // we will only ever be using this function to get term attachments for product_cats or product_series
  // in each case we must target different term meta: thumbnail_id & series_thumbnail
  
  // if the term_id exists in product_cats, we will get the thumbnail_id
  if (term_exists($term_id, 'product_cat')) {
    
    $thumbnail_id = get_term_meta($term_id, 'thumbnail_id', true);
    
    if($thumbnail_id){
      $thumb_src = wp_get_attachment_url($thumbnail_id);
      $thumb_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
    }
    
    // when no thumbnail_id (when no thumbnail is set, we will default using this)
    if(!$thumbnail_id) {
      
      // if no thumb set, get the whole term first
      $term = get_term_by('id', $term_id, 'product_cat');
      
      // if term has parent, 
      if ($term->parent > 0) {
        $parent = get_term_by('id', $term->parent, 'product_cat'); // set the parent term
        $parent_thumbnail_id = get_term_meta($parent->term_id, 'thumbnail_id', true); // get the thumbnail id from that instead
        $thumb_src = wp_get_attachment_url($parent_thumbnail_id);
        $thumb_alt = get_post_meta($parent_thumbnail_id, '_wp_attachment_image_alt', true);
      }
    }
    
  } 
  
  // if the term_id exists instead in product_series, we will get the series_thumbnail
  if (term_exists($term_id, 'product_series')) {
    
    $thumbnail_id = get_term_meta($term_id, 'series_thumbnail', true);
    
    if($thumbnail_id){
      $thumb_src = wp_get_attachment_url($thumbnail_id);
      $thumb_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
    }
    
    // when no thumbnail_id (when no thumbnail is set, we will default using this)
    if(!$thumbnail_id){
      
      $term = get_term_by('id', $term_id, 'product_series');
      
      if ($term->parent > 0) {
        $parent = get_term_by('id', $term->parent, 'product_series');
        $parent_thumbnail_id = get_term_meta($parent->term_id, 'series_thumbnail', true);
        $thumb_src = wp_get_attachment_url($parent_thumbnail_id);
        $thumb_alt = get_post_meta($parent_thumbnail_id, '_wp_attachment_image_alt', true);
      }
      
    }
    
  }
  
  $data['src'] = $thumb_src;
  $data['alt'] = $thumb_alt;
  
  return $data;
  
}

/**
*
* Generalized helper function/s
*
*/

function strpos_recursive($haystack, $needle, $offset = 0, &$results = array()) { 
	// helper function: finding strings; see below 
  $offset = strpos($haystack, $needle, $offset);
  if($offset === false) {
    return $results;           
  } else {
    $results[] = $offset;
    return strpos_recursive($haystack, $needle, ($offset + 1), $results);
  }
}

/**
*
* DEFUNct/REDUNDANT
*
*/

function __product_cat_has_children($term_id) { 
  if ( count( get_term_children( $term_id, 'product_cat' ) ) > 0 ) {
    return true;
  } else {
    return false;
  };
};
function __product_series_has_children($term_id) { 
  if ( count( get_term_children( $term_id, 'product_series' ) ) > 0 ) {
    return true;
  } else {
    return false;
  };
};