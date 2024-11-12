<?php
/**
 * The template for displaying general archive pages.
 *
 * @package Rmcc_Theme
 */

// namespace & use
use Timber as Theme;
use Timber\PostQuery;
use Timber\Post;
use Timber\User;
use Timber\Helper;
use Timber\Term;

// set the context
$context = Theme::context();

// set templates variable as an array
$templates = array('base.twig');

// set some context vars
$context['title'] = _x( 'Error: Page not found', '404/Error pages', 'base-theme' );
$context['description'] = _x( 'Sorry, there has been an error locating a resource for your query. Try finding what you want using the search form below.', '404/Error pages', 'base-theme' );

// author archives
if (is_author()) {
  // disable blog filters on author archives
  $context['configs']['blog_filters'] = false;

  // set vars
  if (isset($wp_query->query_vars['author'])) {
  	$author = new User( $wp_query->query_vars['author'] );
  	$context['author'] = $author;
  	$context['title'] = _x('Author: ', 'Archives', 'base-theme') . $author->name();
    $context['description'] = get_the_archive_description();
  }

  // set templates
  array_unshift($templates, 'author.twig', 'archive.twig');
}

// date archives (D)
elseif (is_day()) {
  // disable blog filters on date archives
  $context['configs']['blog_filters'] = false;

  // set vars
	$context['title'] = _x('Day: ', 'Archives', 'base-theme') . get_the_date('l dS \o\f F Y');
  $context['description'] = get_the_archive_description();

  // set templates
  array_unshift($templates, 'day.twig', 'archive.twig');
}

// date archives (M)
elseif (is_month()) {
  // disable blog filters on date archives
  $context['configs']['blog_filters'] = false;

  // set vars
	$context['title'] = _x('Month: ', 'Archives', 'base-theme') . get_the_date('F Y');
  $context['description'] = get_the_archive_description();
  
  // set templates
  array_unshift($templates, 'month.twig', 'archive.twig');
}

// date archives (Y)
elseif (is_year()) {
  // disable blog filters on date archives
  $context['configs']['blog_filters'] = false;

  // set vars
	$context['title'] = _x('Year: ', 'Archives', 'base-theme') . get_the_date('Y');
  $context['description'] = get_the_archive_description();

  // set templates
  array_unshift($templates, 'year.twig', 'archive.twig');
}

// tag archives
elseif (is_tag()) {

  // set vars
  $context['title'] = _x('Tag: ', 'Archives', 'base-theme') . single_tag_title('', false);
  $context['description'] = get_the_archive_description();

  // set templates
  array_unshift($templates, 'archive_' . get_query_var('tag') . '.twig', 'tag.twig', 'archive.twig');
}

// category archives
elseif (is_category()) {

  // set vars
  $context['title'] = single_cat_title('', false);
  $context['description'] = get_the_archive_description();

  // set templates
  array_unshift($templates, 'archive_' . get_query_var('cat') . '.twig', 'category.twig', 'archive.twig');
}

// custom taxonomy archives
elseif(is_tax()){

  // disable blog filters on custom taxonomy archives
  $context['configs']['blog_filters'] = false;

  // set vars
  $context['title'] = single_term_title('', false);
  $context['description'] = get_the_archive_description();

  // set templates
  array_unshift($templates, 'custom_taxonomy.twig', 'archive.twig');
}

// custom post_type archives
elseif (is_post_type_archive()) {

  // disable blog filters on post_type archives. can reset the blog filters settings here if adding in filters for other taxonomies on a custom type etc. Clever?!
  $context['configs']['blog_filters'] = false;

  // set vars
  $context['title'] = post_type_archive_title('', false);
  $context['description'] = get_the_archive_description();

  // set templates
	array_unshift($templates, 'archive_' . get_post_type() . '.twig', 'custom_post_type.twig', 'archive.twig');
}

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

// & render the templates with the context
Theme::render($templates, $context);
