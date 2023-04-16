<?php

function meta_gallery($atts) {
  $context = Timber::context();
  $template = 'meta_gallery.twig';
  $context['gallery'] = null;

  if(is_array($atts)){

    if(array_key_exists('cats', $atts)) {

      // dealing with the input string for the cats. can be one cat e.g "driveways", or comma-separated string e.g "driveways,patios"
      $terms = $atts['cats']; 
			$sep = ',';
			if(strpos($terms, $sep) !== false) {
				$exploded_array = explode($sep, $terms);
				$terms = $exploded_array;
			}

      // get the gallery posts (media attachments)
			$args = array(
				'post_type' => 'attachment',
				'post_mime_type' => 'image', // Only bring back attachments that are images
				'posts_per_page' => -1, // Show us the first three results
				'post_status' => 'inherit', // Attachments default to "inherit", rather than published. Use "inherit" or "any".
				'tax_query' => array(
					array(
						'taxonomy' => 'media_category',
						'field'    => 'slug',
						'terms'    => $terms,
					),
				)
		
			);
			$gallery_posts = \Timber::get_posts($args);

			if(!empty($gallery_posts)){
        $gallery = array();
        $context['gallery'] = (object) [];
				foreach($gallery_posts as $item){
					$img_obj = new \Timber\Image($item->id);
					$gallery[] = $img_obj;
				};
        $context['gallery']->images = $gallery;
			}

    }

    // assign all the other attributes for outputs, but only when we have images for the gallery
    if($context['gallery'] && property_exists($context['gallery'], 'images')){

      if(array_key_exists('id', $atts)) $context['gallery']->id = $atts['id']; 
      if(array_key_exists('container', $atts)) $context['gallery']->container = $atts['container']; 
      if(array_key_exists('layout', $atts)) $context['gallery']->layout = $atts['layout']; 

      // nano settings
      if(array_key_exists('gallerydisplaymode', $atts)) $context['gallery']->gallerydisplaymode = $atts['gallerydisplaymode'];
      if(array_key_exists('gallerymaxrows', $atts)) $context['gallery']->gallerymaxrows = $atts['gallerymaxrows'];
      if(array_key_exists('gallerysorting', $atts)) $context['gallery']->gallerysorting = $atts['gallerysorting'];
      if(array_key_exists('gallerydisplaymorestep', $atts)) $context['gallery']->gallerydisplaymorestep = $atts['gallerydisplaymorestep'];
      if(array_key_exists('thumbnailheight', $atts)) $context['gallery']->thumbnailheight = $atts['thumbnailheight'];
      if(array_key_exists('thumbnailwidth', $atts)) $context['gallery']->thumbnailwidth = $atts['thumbnailwidth'];
      if(array_key_exists('thumbnailalignment', $atts)) $context['gallery']->thumbnailalignment = $atts['thumbnailalignment'];
      if(array_key_exists('thumbnailgutterwidth', $atts)) $context['gallery']->thumbnailgutterwidth = $atts['thumbnailgutterwidth'];
      if(array_key_exists('thumbnailgutterheight', $atts)) $context['gallery']->thumbnailgutterheight = $atts['thumbnailgutterheight'];
    }
    
  }

  $out = Timber::compile($template, $context);
  return $out;
}

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

// Add uk-active class to wordpress'active menu items
function uikit_active_menu_items($classes, $item) {
  if (in_array('current-menu-item', $classes) ){
    $classes[] = 'uk-active ';
  }
  return $classes;
}

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