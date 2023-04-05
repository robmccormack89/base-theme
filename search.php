<?php

/**
*
* The search
*
* @package Rmcc_Theme
*
*/
 
// namespace & use
namespace Rmcc;
use Timber\PostQuery;
use Timber\Term;
use Timber\Pagination;

// set templates variable as an array
$templates = array('search.twig', 'archive.twig', 'base.twig');

// set the context
$context = Theme::context();

// set some context vars
$context['posts'] = new PostQuery(); // archive posts
$context['configs']['blog_filters'] = false; // disable blog filters on search archives

// set title & description vars
$title = _x( 'Search results', 'Search results', 'base-theme' );
$description = _x( 'You have searched for:', 'Search results', 'base-theme' ) . ' "' . get_search_query() . '"';

// create the archive object, and fill it. i think this is good practice as it matches singular context format like post.title as archive.title
$context['archive'] = (object) [
  "posts" => $context['posts'],
  "title" => (is_paged()) ? $title . ' - Page ' . get_query_var('paged') : $title,
  "description" => $description,
  "thumbnail" => [
    "src" => false,
    "alt" => false,
    "caption" => false
  ]
];

// & render the template with the context
Theme::render($templates, $context);