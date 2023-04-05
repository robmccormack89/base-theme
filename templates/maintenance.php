<?php

/**
*
* Template Name: Maintenance Mode Template
* Template Post Type: page
*
* @package Rmcc_Theme
*
*/

// namespace & use
namespace Rmcc;

$templates = array('maintenance.twig');

// set the context
$context = Theme::context();

// & render the template with the context
Theme::render($templates, $context);