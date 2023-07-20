<?php

/**
*
* The default template for displaying all singlular items (posts, pages & cpt singles)
*
* @package Rmcc_Theme
*
*/

// namespace & use
namespace Rmcc;
use Timber\Post;
use Timber\Image;

global $configs;

// set the context
$context = Theme::context();

// set templates variable as an array
$templates = array('base.twig');

// set some context vars
$context['title'] = _x( 'Error: Page not found', '404/Error pages', 'base-theme' );
$context['description'] = _x( 'Sorry, there has been an error locating a resource for your query. Try finding what you want using the search form below.', '404/Error pages', 'base-theme' );
$context['post'] = new Post(); // the singlular post object

// if not a privated post
if(get_post_status($context['post']->ID) != 'private') {

	$context['title'] = $context['post']->title; // just good housekeeping
	$context['description'] = $context['post']->post_excerpt ?: false; // just good housekeeping

	// add new template for password protected singulars (does not work on static front_pages)
	if(post_password_required($context['post'])){
		array_unshift($templates, 'single_protected.twig');
	} else {
		array_unshift($templates, 'single-' . $context['post']->ID . '.twig', 'single-' . $context['post']->slug . '.twig', 'single-' . $context['post']->post_type . '.twig', 'single.twig');

		if($context['post']->slug == 'contact' || $context['post']->slug == 'contact-us') {
			array_unshift($templates, 'contact.twig');
		}

	}

}

// & render the template with the context
Theme::render($templates, $context);
