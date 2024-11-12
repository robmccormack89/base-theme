<?php

/* meta galleries

*/
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