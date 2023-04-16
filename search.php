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

// set the context
$context = Theme::context();

// set templates variable as an array
$templates = array('search.twig', 'archive.twig', 'base.twig');

// disable blog filters on search archives
$context['configs']['blog_filters'] = false;

// set some context vars
$context['title'] = _x( 'Search results', 'Search: results', 'base-theme' );
$context['description'] = _x( 'You have searched for:', 'Search: results', 'base-theme' ) . ' "' . get_search_query() . '"';
$context['posts'] = new PostQuery();

// create the archive object, and fill it. i think this is good practice as it matches singular context format like post.title as archive.title
$context['archive'] = (object) [
  "posts" => $context['posts'],
  "title" => (is_paged()) ? $context['title'] . ' - Page ' . get_query_var('paged') : $context['title'],
  "description" => $context['description'],
  "thumbnail" => [
    "src" => false,
    "alt" => false,
    "caption" => false
  ]
];

// & render the template with the context
Theme::render($templates, $context);