<?php

/* ajax_live_search

*/
function ajax_live_search() {

  // default data for the response. result gets set to 1 when we have a valid query.
  $data = array(
    'result' => 0,
    'response' => ''
  );

  // set some vars & context vars from the query/string
  $query = filter_var($_POST['query'], FILTER_SANITIZE_STRING); // validate the string to remove html or script crap
  $context['query_string_upper'] = ucwords($query); // capitalize the query string. escaped on the other end just in case

  // if the query isnt empty, go ahead & get the data using it
  if (!empty($query)) {

    $data['result'] = 1; // once there is a query, we will set result to 1. coz we will outut the template regardless of data. the template will display the message for no data itself

    global $configs;
    $context['configs'] = $configs;
    
    // get terms data if query is for a term
    if(!empty($configs['live_search_taxes'])){
      foreach ($configs['live_search_taxes'] as $tax) {
        // this check is for when post tags are disabled but post_tag still exists in the config types for blog filters
        if(!($configs['disable_post_tags'] && $tax == 'post_tag')){
          $tax_args = array(
            'fields' => 'all',
            'name__like' => $query,
          );
          $tax_items = get_terms($tax, $tax_args);
          $context['result_items'][$tax] = $tax_items; // result_items.category
        }
      }
    }

    // get posts if the query returns results with s=
    if(!empty($configs['live_search_types'])){
      foreach ($configs['live_search_types'] as $type) {

        // posts matching the search query
        $post_args = array(
          'post_type' => $type,
          'post_status' => 'publish',
          's' => $query,
          'posts_per_page' => -1
        );
        $post_items = new Timber\PostQuery($post_args);
        $context['result_items'][$type] = $post_items; // result_items.category
        
      }
    }

    // get posts within any queried terms 
    if(!empty($configs['live_search_types_in_taxes'])){
      foreach ($configs['live_search_types_in_taxes'] as $type) {
        foreach ($configs['live_search_taxes'] as $tax) {
          if(!($configs['disable_post_tags'] && $tax == 'post_tag')){

            $matching_taxes = get_terms(array(
              'taxonomy' => $tax,
              'fields' => 'slugs',
              'name__like' => $query,
            ));
            $types_in_taxes_args = array(
              'post_type' => $type,
              'posts_per_page' => -1,
              'tax_query' => array(
                'relation' => 'AND',
                array(
                  'taxonomy' => $tax,
                  'field' => 'slug',
                  'terms' => $matching_taxes
                ),
              ),
            );
            $types_in_taxes = new Timber\PostQuery($types_in_taxes_args);
            $context['result_items_within'][$type][$tax] = $types_in_taxes; // result_items_within.post.category

          }
        }
      }
    }

    // compile the data in twig & set
    $response = Timber::compile(array('_live-search-results.twig'), $context);
    $data['response'] = $response;

  }

  // echo the json_encoded compiled twig template, then kill the function
  echo json_encode($data);
  die();

}