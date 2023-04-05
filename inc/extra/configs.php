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
// $configs['maintenance_template'] = 'coming-soon.twig'; // only applies when redirect_all_traffic_to_page doesnt exist or is set to false. defaults to maintenance.twig downstream

// Global
$configs['animated_preloader'] = true;
$configs['enable_page_excerpts'] = false;
$configs['disable_post_tags'] = false;

// Singular
$configs['enable_post_comments'] = false;
$configs['enable_post_sharing'] = false;
$configs['enable_post_paging'] = false;
$configs['enable_post_author'] = false;

// Archives
$configs['infinite_pagination'] = false;

// Live Search configs
$configs['live_search'] = true;
$configs['live_search_types'] = array('post', 'page');
$configs['live_search_taxes'] = array('category', 'post_tag');
$configs['live_search_types_in_taxes'] = array('post');
$configs['live_search'] = false; // disable

// Blog Filters configs
$configs['blog_filters'] = true;
$configs['blog_filters_properties'] = (object) [
  "types" => array(
    (object) [
      "parentGroupId" => 'post_cat_group',
      "subGroupId" => 'post_subcat_group',
      "subId" => 'post_cat_sub',
      "formQueryKey" => 'category_name',
      "taxKey" => 'category',
      "altQueryKey" => 'cat',
      "currentQueryVar" => ''
    ],
    (object) [
      "formQueryKey" => 'tag',
      "taxKey" => 'post_tag',
      "altQueryKey" => 'tag_id',
      "currentQueryVar" => ''
    ]
  )
];
$configs['blog_filters'] = false; // disable

// Meta gallerys config (uses nanogallery)
$configs['meta_galleries'] = false;

// ACF configs
$configs['acf_local_json'] = false;
$configs['acf_blocks'] = false;
$configs['acf_template_settings'] = false;
$configs['acf_options_page'] = false;

// Theme defaults
$configs['theme_defaults'] = (object) [
  "thumbnail" => [
    "src" => _x( 'https://picsum.photos/1920/1200', 'Theme Featured Image - src', 'base-theme' ),
    "alt" => _x( '', 'Theme Featured Image - alt', 'base-theme' ),
    "caption" => _x( '', 'Theme Featured Image - caption', 'base-theme' )
  ]
];

return $configs;
