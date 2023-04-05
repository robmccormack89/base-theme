<?php

/**
*
* The 404 template
*
* @package Rmcc_Theme
*
*/

// namespace & use
namespace Rmcc;

// set templates variable as an array
$templates = array('404.twig', 'base.twig');

// set the context
$context = Theme::context();

// set some context vars
$context['title'] = _x( 'Error: Page not found', '404/Error pages', 'base-theme' );
$context['description'] = _x( 'Sorry, there has been an error locating a resource for your query. Try finding what you want using the search form below.', '404/Error pages', 'base-theme' );

// & render the template with the context
Theme::render($templates, $context);
