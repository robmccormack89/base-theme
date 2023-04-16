<?php

// Getting the data for the blog filters & checking stuff
function terms_for_filters($get_terms_tax_key = 'category', $parent_only = true){
  $terms_args = array(
    'taxonomy' => $get_terms_tax_key,
    'orderby' => 'slug',
		'hide_empty' => true
  );
	if($parent_only){
		$terms_args['parent'] = 0;
	}
  return get_terms($terms_args);
}
function is_term_active($id, $q_var){

	if(is_array($q_var)){
		if(in_array($id, $q_var)){
			return true;
		}
	}
	
	elseif(is_string($q_var)){
		if(str_contains($q_var, strval($id))){
			return true;
		}
	}

	// redundant?
	elseif(is_int($q_var)){
		if($id == $q_var){
			return true;
		}
	}

	return false;

}
function does_term_have_children($term_id, $tax_key = 'category') {
	// check to see if 'product_cat' has children
  if (count(get_term_children($term_id, $tax_key)) > 0) return true;
  return false;
}
function is_term_parent_of_active($id, $q_vars, $tax_key){
	$active_id = get_query_var($q_vars); // an id
	$active_term = get_term_by('id', $active_id, $tax_key);
	if(term_is_ancestor_of($id, $active_term, $tax_key)){
		return true;
	}
	return false;
}
function get_parent_term_slug_from_child($child, $tax = 'category'){
	$parent_id = ($child) ? $child->parent : null;
	if($parent_id){
		$parent_term = get_term($parent_id, $tax);
		$parent_slug = $parent_term->slug;
		return $parent_slug;
	}
	return '';
}
// set conditionals for shop filters functionality on child term archives
function is_child_term_archive($tax_key = null) {
	$queried_object = get_queried_object();
  if($queried_object->parent) {
		if($queried_object->parent != '0'){
			if($tax_key){
				$parent_term = get_term($queried_object->parent);
				if($parent_term->taxonomy == $tax_key) return true;
				return false;
			}
			return true;
		}
		return false;
  }
  return false;
}

// subterms for filters (rest api & fetch)
function blog_filters_ajax_restapi_routes($server) {
	global $configs;
	foreach($configs['blog_filters_properties']->types as $item){
		// this check is for when post tags are disabled but post_tag still exists in the config types for blog filters
		if(!($configs['disable_post_tags'] && $item->taxKey == 'post_tag')){
			if(property_exists($item, 'parentGroupId')){
				$server->register_route($item->parentGroupId, '/'.$item->parentGroupId, array(
					'methods'  => 'POST',
					'callback' => 'get_subterms_for_filters',
				));
			}
		}
	}
}
function get_subterms_for_filters($req) {
	
  $context = Timber::context();

	$parent_ids = $req['id'];
  $parent_slugs = $req['slug'];
  $q_vars = $req['q_vars'];
	$tax_key = $req['tax_key'];
	$q_key = $req['q_key'];

  $childs_ids = get_term_children($parent_ids, $tax_key);
  $context['parent_ids'] = $parent_ids;

  if(!empty($parent_ids)){
    $subs_array = array();
    foreach ($parent_ids as $id) {
      $sub_terms = get_terms(array(
        'taxonomy'   => $tax_key,
        'hide_empty' => true,
        'parent'     => $id
      ));
      $subs_array[] = $sub_terms;
    }
  }

  $context['q_vars'] = $q_vars;
	$context['tax_key'] = $tax_key;
	$context['q_key'] = $q_key;
  $context['sub_terms'] = array_merge(...$subs_array);
	if(empty($context['sub_terms'])) return;
	$data = Timber::compile(array('archive_parts/filters/_sub_terms.twig'), $context);
  return $data;

}

// old / unused helpers
function __get_parent_term_id_from_child($child, $tax = 'category'){
	$parent_id = ($child) ? $child->parent : null;
	if($parent_id){
		// $parent_term = get_term($parent_id, $tax);
		// $parent_slug = $parent_term->slug;
		return $parent_id;
		// return $parent_slug;
	}
	return '';
}
function __get_values_from_array_using_key($array, $key = 'term_id'){
	if($array && count($array) > 0) {
		$new_arr = array();
		foreach($array as $item){
			if($item->$key){
				$new_arr[] = $item->$key;
			} else {
				return null;
			}
		}
		return $new_arr;
	}
	return null;
}
function __get_parents_of_terms_no_repeat($terms){
	if($terms && count($terms) > 0) {
		$parents = array();
		foreach($terms as $term){
			if(!($term->parent == '0')){
				$parents[] = get_term($term->parent);
			}
		}
		// removes duplicates in multi-dimensional array  - https://stackoverflow.com/questions/307674/how-to-remove-duplicate-values-from-a-multi-dimensional-array-in-php
		return array_map("unserialize", array_unique(array_map("serialize", $parents)));
	}
}
function __get_children_terms_from_terms_list($terms){
	if($terms && count($terms) > 0) {
		$arr = array();
		foreach($terms as $term){
			if(!($term->parent == '0')){
				$arr[] = $term;
			}
		}
		return $arr;
	}
}
function __if_terms_contains_child($terms, $tax_key = 'category') {
	if($terms && count($terms) > 0) {
		foreach($terms as $term){
			if(!($term->parent == '0')){
				return true;
			}
		}
	}
	return false;
}