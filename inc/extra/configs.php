<?php

/**
*
* Global theme configs to enable & disable various shit
*
* @package Rmcc_Theme
*
*/

// $configs['maintenance_mode'] = 'all';
// // $configs['redirect_all_traffic_to_page'] = 2;
// $configs['maintenance_template'] = 'coming_soon.twig'; // only applies when redirect_all_traffic_to_page doesnt exist or is set to false. defaults to maintenance.twig downstream

// Global
$configs['animated_preloader'] = true;
$configs['enable_page_excerpts'] = true;
$configs['disable_post_tags'] = false;

// Singular
$configs['enable_post_comments'] = true;
$configs['enable_post_sharing'] = true;
$configs['enable_post_paging'] = true;
$configs['enable_post_author'] = true;

// Archives
$configs['infinite_pagination'] = true;

// Live Search configs
$configs['live_search'] = true;
// $configs['live_search_types'] = array('post', 'page');
$configs['live_search_types'] = array('product', 'page');
// $configs['live_search_taxes'] = array('category', 'post_tag');
$configs['live_search_taxes'] = array('product_cat', 'product_tag');
$configs['live_search_types_in_taxes'] = array('product');
// $configs['live_search'] = false; // disable

// Blog Filters configs
$configs['blog_filters'] = true;
$configs['blog_filters_properties'] = (object) [
  "types" => array(
    // (object) [
    //   "parentGroupId" => 'post_cat_group',
    //   "subGroupId" => 'post_subcat_group',
    //   "subId" => 'post_cat_sub',
    //   "formQueryKey" => 'category_name',
    //   "taxKey" => 'category',
    //   "altQueryKey" => 'cat',
    //   "currentQueryVar" => ''
    // ],
    (object) [
      "parentGroupId" => 'product_cat_group',
      "subGroupId" => 'product_subcat_group',
      "subId" => 'product_cat_sub',
      "formQueryKey" => 'product_cat',
      "taxKey" => 'product_cat',
      "altQueryKey" => 'product_cat',
      "currentQueryVar" => ''
    ],
    (object) [
      "formQueryKey" => 'product_tag',
      "taxKey" => 'product_tag',
      "altQueryKey" => 'product_tag',
      "currentQueryVar" => ''
    ]
  )
];
// $configs['blog_filters'] = false; // disable

// Meta gallerys config (uses nanogallery)
$configs['meta_galleries'] = true;

// Theme defaults
$configs['theme_defaults'] = (object) [
  "thumbnail" => [
    "src" => _x( 'https://picsum.photos/1920/1200', 'Theme Featured Image - src', 'base-theme' ),
    "alt" => _x( 'Alt', 'Theme Featured Image - alt', 'base-theme' ),
    "caption" => _x( 'Caption', 'Theme Featured Image - caption', 'base-theme' )
  ]
];

return $configs;
