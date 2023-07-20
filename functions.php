<?php
/**
*
* Rmcc Theme functions and definitions...
*
* @package Rmcc_Theme
*
*/

require get_template_directory() . '/inc/extra/helpers.php';
$configs = require_once(get_template_directory() . '/inc/extra/configs.php');

/* plugin activation

  using tgm-plugin-activation

*/
require_once get_template_directory() . '/inc/lib/plugin-activation.php';

/* composer autoloader of classes

  timber should be included in packages

*/
if (file_exists($composer_autoload = __DIR__.'/vendor/autoload.php')) require_once $composer_autoload;

/* init the main theme class

  Timber should be available via composer autoload

*/
if (class_exists('Timber\Timber')) new Rmcc\Theme;

/* if ACF is available (plugin is installed), do the Theme ACF class 

  ACf should be installed via required plugins

*/
if(class_exists('ACF')) new Rmcc\Fields;

if($configs['live_search']) require get_template_directory() . '/inc/extra/live_search.php';
if($configs['blog_filters']) require get_template_directory() . '/inc/extra/blog_filters.php';
