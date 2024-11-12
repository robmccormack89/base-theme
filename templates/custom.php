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

/*
Set 
The
Base
Stuff
Same
As
404
*/

// set templates variable as an array. set as base.twig to start, in case anything goes wrong (wp wants display an archive, but our conditionals below dont cover it) we will modify this within conditionals below for diffrent contexts etc...
$templates = array('base.twig');

// set the context
$context = Theme::context();

// set some context vars. set 404 title & desc to start, in case anything goes wrong. we will modify these within conditionals below for diffrent contexts etc...
$context['title'] = _x( 'Error: Page not found', '404/Error pages', 'base-theme' );
$context['description'] = _x( 'Sorry, there has been an error locating a resource for your query. Try finding what you want using the search form below.', '404/Error pages', 'base-theme' );

/*
Set
The
Contextual
Stuff
with
Conditionals
*/

// set some context vars
$context['post'] = new Post(); // the singlular post object
$context['post']->the_excerpt = $context['post']->post_excerpt ?: false; // set post.the_excerpt instead

// if not a privated post (privated posts will appear as 404s due to configs above)
if(get_post_status($context['post']->ID) != 'private') {

	// good housekeeping. we will use post.title & post.post_excerpt in actual templates
	$context['title'] = $context['post']->title;
	$context['description'] = $context['post']->post_excerpt ?: false;

	// twig templates for singles
	array_unshift($templates, 'single-' . $context['post']->ID . '.twig', 'single-' . $context['post']->slug . '.twig', 'single-' . $context['post']->post_type . '.twig', 'single.twig');

	// add the custom template/s to the start of the templates array
	array_unshift($templates, 'custom-' . $context['post']->post_type . '.twig', 'custom.twig',);

	// add new template for password protected singulars (does not work on static front_pages)
	if(post_password_required($context['post'])) array_unshift($templates, 'single_protected.twig');

}

/*
finally
we
render
templates
and
context
*/

// & render the template with the context
Theme::render($templates, $context);