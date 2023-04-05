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

// set templates variable as an array
$templates = array('base.twig');
if(is_home()) array_unshift($templates, 'archive.twig'); // if is the main blog posts page, front_page or not. static front_page's will go thru the singular.php route instead

// set the context
$context = Theme::context();

// set the title for the blog page
$post = new Post();
$title = get_the_title($post->id); // title of page itself
if(is_home() && is_front_page()) $title = get_bloginfo('name'); // site title if blog is homepage

// global $configs;
// if(is_bool($configs['maintenance_mode']) && $configs['maintenance_mode'] == true || is_string($configs['maintenance_mode']) && $configs['maintenance_mode'] == 'all'){
//   array_unshift($templates, 'maintenance.twig');
// }

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

// & render the template with the context
Theme::render($templates, $context);
