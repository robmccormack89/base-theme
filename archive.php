<?php
/**
 * The template for displaying general archive pages
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

// set templates variable as an array
$templates = array('archive.twig', 'base.twig');

// set the context
$context = Theme::context();

// author archives
if (is_author()) {
  if (isset($wp_query->query_vars['author'])) {
  	$author = new User( $wp_query->query_vars['author'] );
  	$context['author'] = $author;
  	$title  = 'Author Archives: ' . $author->name();
  }
  $context['configs']['blog_filters'] = false; // disable blog filters on author archives
  array_unshift($templates, 'author.twig', 'archive.twig');
}

// date archives (D)
elseif (is_day()) {
	$title = _x( 'Archive', 'Archives', 'base-theme' ) . ': ' . get_the_date('D M Y');
  $context['configs']['blog_filters'] = false; // disable blog filters on date archives
}

// date archives (M)
elseif (is_month()) {
	$title = _x( 'Archive', 'Archives', 'base-theme' ) . ': ' . get_the_date('M Y');
  $context['configs']['blog_filters'] = false; // disable blog filters on date archives
}

// date archives (Y)
elseif (is_year()) {
	$title = _x( 'Archive', 'Archives', 'base-theme' ) . ': ' . get_the_date('Y');
  $context['configs']['blog_filters'] = false; // disable blog filters on date archives
}

// tag archives
elseif (is_tag()) {
  $title = single_tag_title('', false);
  array_unshift($templates, 'archive-' . get_query_var('tag') . '.twig');
}

// category archives
elseif (is_category()) {
  $title = single_cat_title( '', false );
  array_unshift($templates, 'archive-' . get_query_var('cat') . '.twig', 'category.twig');
}

// custom taxonomy archives
elseif(is_tax()){
  $context['configs']['blog_filters'] = false; // disable blog filters on custom taxonomy archives
}

// custom post_type archives
elseif (is_post_type_archive()) {
  $context['configs']['blog_filters'] = false; // disable blog filters on post_type archives
  $title = post_type_archive_title( '', false );
	array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );
}

// create the archive object, and fill it. i think this is good practice as it matches singular context format like post.title as archive.title
$context['archive'] = (object) [
  "posts" => $context['posts'],
  "title" => (is_paged()) ? $title . ' - Page ' . get_query_var('paged') : $title,
  "description" => get_the_archive_description(),
  "thumbnail" => [
    "src" => false,
    "alt" => false,
    "caption" => false
  ]
];

// & render the templates with the context
Theme::render($templates, $context);
