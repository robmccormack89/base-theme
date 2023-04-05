<?php

/**
*
* Template Name: Custom Template
* Template Post Type: post, page
*
* @package Rmcc_Theme
*
*/

// namespace & use
namespace Rmcc;
use Timber\Post;

// set the context
$context = Theme::context();

// set some context vars
$context['post'] = new Post(); // the singlular post object
$context['description'] = false; // wont bother setting post.description. keep it null
$context['post']->the_excerpt = $context['post']->post_excerpt ?: false; // set post.the_excerpt instead

// set templates variable as an array (requires $context['post'])
$templates = array(
	'single-' . $context['post']->ID . '.twig',
	'single-' . $context['post']->slug . '.twig',
	'single-' . $context['post']->post_type . '.twig',
	'single.twig',
	'base.twig'
);

// add the custom template/s to the start of the templates array
array_unshift($templates, 'custom-' . $context['post']->post_type . '.twig', 'custom.twig',);

// add new template for password protected singulars (does not work on static front_pages)
if(post_password_required($context['post'])){
	$templates  = array(
		'single-protected.twig'
	);
}

// for private pages/posts, re-route to base.twig with title & description set the same as a 404
if(get_post_status($context['post']->ID) == 'private') {
  $context['title'] = _x( 'Error: Page not found', '404/Error pages', 'base-theme' );
  $context['description'] = _x( 'Sorry, there has been an error locating a resource for your query. Try finding what you want using the search form below.', '404/Error pages', 'base-theme' );
	$templates  = array(
		'base.twig'
	);
}

// & render the template with the context
Theme::render($templates, $context);