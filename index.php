<?php
/**
 * The main blog template file
 *
 * @package Rmcc_Theme
*/

// namespace & use
namespace Rmcc;
use Timber\PostQuery;
use Timber\Post;

// set the context
$context = Theme::context();

// set templates variable as an array
$templates = array('base.twig');

// set some context vars
$context['title'] = _x( 'Error: Page not found', '404/Error pages', 'base-theme' );
$context['description'] = _x( 'Sorry, there has been an error locating a resource for your query. Try finding what you want using the search form below.', '404/Error pages', 'base-theme' );

if(is_home()) array_unshift($templates, 'archive.twig'); // if is the main blog posts page, front_page or not. static front_page's will go thru the singular.php route instead

// set the title for the blog page
$post = new Post();
$context['title'] = get_the_title($post->id); // title of page itself
$context['description'] = $post->post_excerpt ?: false; // description of page itself (post_excerpt)

if(is_home() && is_front_page()) $context['title'] = get_bloginfo('name'); // site title if blog is homepage
if(is_home() && is_front_page()) $context['description'] = get_bloginfo('description'); // site description if blog is homepage

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
