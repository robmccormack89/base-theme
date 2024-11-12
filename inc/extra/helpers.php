<?php

/*
Breadcrumb
Stuff
Theme.php
*/

// check if yoast_breadcrumbs are enabled
function yoast_breadcrumb_enabled() {
  if(class_exists('WPSEO_Options')){
    if(WPSEO_Options::get('breadcrumbs-enable', false)){
      return true;
    }
  }
  return false;
}

// Yoast breadcrumbs - customize the sep icon
function filter_wpseo_breadcrumb_separator($this_options_breadcrumbs_sep) {
  return '<i uk-icon="icon: chevron-right; ratio: .6"></i>';
}

/*
SVG
Stuff
Theme.php
*/

// add svg support
function check_filetype($data, $file, $filename, $mimes) {
  global $wp_version;
  if ($wp_version !== '4.7.1') {
    return $data;
  }
  $filetype = wp_check_filetype($filename, $mimes);
  return [
    'ext'             => $filetype['ext'],
    'type'            => $filetype['type'],
    'proper_filename' => $data['proper_filename']
  ];
}
function cc_mime_types( $mimes ){
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
function fix_svg() {
  echo '<style type="text/css"> .attachment-266x266, .thumbnail img { width: 100%!important; height: auto!important; } </style>';
}

/*
Disabling Comments
Stuff
Theme.php
*/

// Disable Wordpress comments from backend & posts etc
function disable_comments_hide_existing_comments($comments) {
  $comments = array();
  return $comments;
}
function disable_comments_admin_menu() {
  remove_menu_page('edit-comments.php');
}
function disable_comments_admin_menu_redirect() {
  global $pagenow;
  if ($pagenow === 'edit-comments.php') {
    wp_redirect(admin_url()); exit;
  }
}
function disable_comments_dashboard() {
  remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
function disable_comments_admin_bar() {
  if (is_admin_bar_showing()) {
    remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
  }
}

/*
Menus
Stuff
Theme.php
*/

// Add uk-active class to wordpress'active menu items
function uikit_active_menu_items($classes, $item) {
  if (in_array('current-menu-item', $classes) ){
    $classes[] = 'uk-active ';
  }
  return $classes;
}

/*
Utility
Stuff
*/

// get a post (or page) object using the slug
function get_page_by_slug($slug, $post_type = 'page', $unique = true){

  $args = array(
    'pagename' => $slug,
    'post_type' => $post_type,
    'post_status' => 'publish',
    'posts_per_page' => 1
  );

  $the_posts = new WP_Query($args);

  if($the_posts->posts) {
    if($unique) return $the_posts->posts[0]; // the first!
    return $the_posts->posts;
  }
  
  return false;

}