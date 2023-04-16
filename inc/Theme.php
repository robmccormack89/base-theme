<?php

/**
*
* the Theme main class
*
* @package Rmcc_Theme
*
*/

// namespace & use
namespace Rmcc;
use Timber\Timber;
use Twig\Extra\String\StringExtension;

// Define paths to Twig templates
Timber::$dirname = array(
  'views',
  'views/archive',
  'views/single',
  'views/global',
  'views/shortcodes',
);

// set the $autoescape value
Timber::$autoescape = false;

// Define Theme Child Class
class Theme extends Timber {

  public function __construct() {
    parent::__construct();
    
    // get/set configs and logo properties
    global $configs;
    $this->configs = $configs;
    $this->logo_width = '223';
    $this->logo_height = '36';

    // regular theme stuff. calling in the methods below into the wp activation contexts
    add_action('after_setup_theme', array($this, 'theme_supports'));
    add_filter('timber/context', array($this, 'add_to_context'));
    add_filter('timber/twig', array($this, 'add_to_twig'));
    add_action('init', array($this, 'register_post_types'));
    add_action('init', array($this, 'register_taxonomies'));
    add_action('init', array($this, 'register_widget_areas'));
    add_action('init', array($this, 'register_navigation_menus'));
    add_action('enqueue_block_assets', array($this, 'theme_enqueue_assets'));

    // fetch routes
    if($this->configs['blog_filters']) add_action('rest_api_init', 'blog_filters_ajax_restapi_routes');

    // meta galleries
    if($this->configs['meta_galleries']){
      
      // add taxonomies to media library
      add_action('init' , function(){

        // add current taxonomies to media library
        // register_taxonomy_for_object_type('category', 'attachment');
        // register_taxonomy_for_object_type( 'post_tag', 'attachment' );

        // add custom taxonomy ('media_category') to media library post type
        $labels_media_cats = array(
          'name' => _x('Media Categories', 'Custom taxonomy label: plural', 'base-theme'),
          'singular_name' => _x('Media Category', 'Custom taxonomy label: singular', 'base-theme'),
          'search_items' => _x('Search Media Categories', 'Custom taxonomy label: search', 'base-theme'),
          'all_items' => _x('All Media Categories', 'Custom taxonomy label: all', 'base-theme'),
          'parent_item' => _x('Parent Media Category', 'Custom taxonomy label: parent', 'base-theme'),
          'parent_item_colon' => _x('Parent Media Category', 'Custom taxonomy label: parent', 'base-theme') . ':',
          'edit_item' => _x('Edit Media Category', 'Custom taxonomy label: edit', 'base-theme'),
          'update_item' => _x('Update Media Category', 'Custom taxonomy label: update', 'base-theme'),
          'add_new_item' => _x('Add New Media Category', 'Custom taxonomy label: add', 'base-theme'),
          'new_item_name' => _x('New Media Category Name', 'Custom taxonomy label: new', 'base-theme'),
          'menu_name' => _x('Media Category', 'Custom taxonomy label: menu label', 'base-theme'),
        );
        $args_media_cats = array(
          'labels' => $labels_media_cats,
          'hierarchical' => true,
          'query_var' => 'true',
          'rewrite' => 'true',
          'show_admin_column' => 'true',
        );
        register_taxonomy('media_category', 'attachment', $args_media_cats);

        $labels_media_tags = array(
          'name' => _x('Media Tags', 'Custom taxonomy label: plural', 'base-theme'),
          'singular_name' => _x('Media Tag', 'Custom taxonomy label: singular', 'base-theme'),
          'search_items' => _x('Search Media Tags', 'Custom taxonomy label: search', 'base-theme'),
          'all_items' => _x('All Media Tags', 'Custom taxonomy label: all', 'base-theme'),
          'parent_item' => _x('Parent Media Tag', 'Custom taxonomy label: parent', 'base-theme'),
          'parent_item_colon' => _x('Parent Media Tag', 'Custom taxonomy label: parent', 'base-theme') . ':',
          'edit_item' => _x('Edit Media Tag', 'Custom taxonomy label: edit', 'base-theme'),
          'update_item' => _x('Update Media Tag', 'Custom taxonomy label: update', 'base-theme'),
          'add_new_item' => _x('Add New Media Tag', 'Custom taxonomy label: add', 'base-theme'),
          'new_item_name' => _x('New Media Tag Name', 'Custom taxonomy label: new', 'base-theme'),
          'menu_name' => _x('Media Tag', 'Custom taxonomy label: menu label', 'base-theme'),
        );
        $args_media_tags = array(
          'labels' => $labels_media_tags,
          'hierarchical' => false,
          'query_var' => 'true',
          'rewrite' => 'true',
          'show_admin_column' => 'true',
        );
        register_taxonomy('media_tag', 'attachment', $args_media_tags);

      });

      // shortcoded meta galleries
      add_shortcode('meta_gallery', 'meta_gallery');

    }

    // Remove tags support from posts
    if($this->configs['disable_post_tags']){
      add_action('init', function(){
        global $wp_taxonomies;
        unregister_taxonomy_for_object_type('post_tag', 'post');
        unset($wp_taxonomies['post_tag']);
        unregister_taxonomy('post_tag');
      });
    }

    // maintenance mode. when it exists & is not false
    if(array_key_exists('maintenance_mode', $this->configs) && !($this->configs['maintenance_mode'] == false)){

      // when maintenance mode set to true
      if(is_bool($this->configs['maintenance_mode']) && $this->configs['maintenance_mode'] == true){
        add_action('template_redirect', function() {
          if(!is_user_logged_in()){

            // when redirect_all_traffic_to_page is OFF
            if(!(array_key_exists('redirect_all_traffic_to_page', $this->configs)) || (is_bool($this->configs['redirect_all_traffic_to_page']) && $this->configs['redirect_all_traffic_to_page'] == false) || (is_string($this->configs['redirect_all_traffic_to_page']) && $this->configs['redirect_all_traffic_to_page'] == '') ){
              add_filter('template_include', function(){
                if(!is_front_page()){
                  wp_redirect(esc_url_raw(home_url()));
                  exit;
                }
                $templates = array('maintenance.twig');
                if(array_key_exists('maintenance_template', $this->configs)) array_unshift($templates, $this->configs['maintenance_template']);
                $context = Theme::context();
                Theme::render($templates, $context);
              });
            }

            // when redirect_all_traffic_to_page is ON
            if(array_key_exists('redirect_all_traffic_to_page', $this->configs) && (is_int($this->configs['redirect_all_traffic_to_page']) || is_string($this->configs['redirect_all_traffic_to_page']) && $this->configs['redirect_all_traffic_to_page'] != '')){

              if(is_int($this->configs['redirect_all_traffic_to_page'])){
                $_postObj = get_post($this->configs['redirect_all_traffic_to_page']);
                if(isset($_postObj) && $_postObj->post_type == 'page') $postObj = $_postObj;
              } elseif(is_string($this->configs['redirect_all_traffic_to_page'])) {
                $postObj = get_page_by_slug($this->configs['redirect_all_traffic_to_page']);
              }

              if(isset($postObj)){
                $link = get_permalink($postObj);
                if(!(is_page($this->configs['redirect_all_traffic_to_page'])) ){
                  wp_redirect(esc_url_raw($link));
                  exit;
                }
              }

              return;

            }

          }
        });
      }

      // when maintenance mode set to 'all'
      if(is_string($this->configs['maintenance_mode']) && $this->configs['maintenance_mode'] == 'all'){
        add_action('template_redirect', function() {

          // when redirect_all_traffic_to_page is OFF
          if(!(array_key_exists('redirect_all_traffic_to_page', $this->configs)) || (is_bool($this->configs['redirect_all_traffic_to_page']) && $this->configs['redirect_all_traffic_to_page'] == false) || (is_string($this->configs['redirect_all_traffic_to_page']) && $this->configs['redirect_all_traffic_to_page'] == '') ){
            add_filter('template_include', function(){
              if(!is_front_page()){
                wp_redirect(esc_url_raw(home_url()));
                exit;
              }
              $templates = array('maintenance.twig');
              if(array_key_exists('maintenance_template', $this->configs)) array_unshift($templates, $this->configs['maintenance_template']);
              $context = Theme::context();
              Theme::render($templates, $context);
            });
          }

          // when redirect_all_traffic_to_page is ON
          if(array_key_exists('redirect_all_traffic_to_page', $this->configs) && (is_int($this->configs['redirect_all_traffic_to_page']) || is_string($this->configs['redirect_all_traffic_to_page']) && $this->configs['redirect_all_traffic_to_page'] != '')){

            if(is_int($this->configs['redirect_all_traffic_to_page'])){
              $_postObj = get_post($this->configs['redirect_all_traffic_to_page']);
              if(isset($_postObj) && $_postObj->post_type == 'page') $postObj = $_postObj;
            } elseif(is_string($this->configs['redirect_all_traffic_to_page'])) {
              $postObj = get_page_by_slug($this->configs['redirect_all_traffic_to_page']);
            }

            if(isset($postObj)){
              $link = get_permalink($postObj);
              if(!(is_page($this->configs['redirect_all_traffic_to_page'])) ){
                wp_redirect(esc_url_raw($link));
                exit;
              }
            }

            return;

          }

        });
      }

    }


  }

  /**
  *
  * theme & twig setups
  *
  */

  public function theme_supports() {

    // usual theme supports
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');
    add_theme_support('post-formats', array(
      'gallery',
      'quote',
      'video',
      'aside',
      'image',
      'link'
    ));
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', array(
      'search-form',
      'comment-form',
      'comment-list',
      'gallery',
      'caption'
    ));
    add_theme_support('custom-logo', array(
      'height' => $this->logo_height,
      'width' => $this->logo_width,
      'flex-width' => true,
      'flex-height' => true
    ));

    // Add excerpts to pages
    if($this->configs['enable_page_excerpts']) add_post_type_support('page', 'excerpt');

    // for translations
    load_theme_textdomain('base-theme', get_template_directory() . '/languages');

    // escaping on some stuff set to wpautop
    remove_filter('term_description', 'wpautop');
    remove_filter('the_content', 'wpautop');
    remove_filter('the_excerpt', 'wpautop');
    remove_filter('widget_text_content', 'wpautop');
    remove_filter('widget_custom_html', 'wpautop' , 10, 3 );

    // svg supports
    add_action('admin_head', 'fix_svg');
    add_filter('wp_check_filetype_and_ext', 'check_filetype', 10, 4);
    add_filter('upload_mimes', 'cc_mime_types');

    // uikit active nav items
    add_filter('nav_menu_css_class', 'uikit_active_menu_items', 10, 2);
    
    // allow icon for yoast breads
    if(yoast_breadcrumb_enabled()) add_filter('wpseo_breadcrumb_separator', 'filter_wpseo_breadcrumb_separator', 10, 1);

    // post comments
    if(!$this->configs['enable_post_comments']) add_filter('comments_array', 'disable_comments_hide_existing_comments', 10, 2);
    if(!$this->configs['enable_post_comments']) add_action('admin_menu', 'disable_comments_admin_menu');
    if(!$this->configs['enable_post_comments']) add_action('admin_init', 'disable_comments_admin_menu_redirect');
    if(!$this->configs['enable_post_comments']) add_action('admin_init', 'disable_comments_dashboard');
    if(!$this->configs['enable_post_comments']) add_action('init', 'disable_comments_admin_bar');

    // allowed html for wp kses post
    add_action('init', function(){
      global $allowedposttags;
      $allowed_atts = array (
        'align'      => array(),
        'class'      => array(),
        'type'       => array(),
        'id'         => array(),
        'dir'        => array(),
        'lang'       => array(),
        'style'      => array(),
        'xml:lang'   => array(),
        'src'        => array(),
        'alt'        => array(),
        'href'       => array(),
        'rel'        => array(),
        'rev'        => array(),
        'target'     => array(),
        'novalidate' => array(),
        'type'       => array(),
        'value'      => array(),
        'name'       => array(),
        'tabindex'   => array(),
        'action'     => array(),
        'method'     => array(),
        'for'        => array(),
        'width'      => array(),
        'height'     => array(),
        'data'       => array(),
        'title'      => array(),
        'uk-icon'      => array(),
        'data-nanogallery2'      => array(),
      );
      $allowedposttags['form'] = $allowed_atts;
      $allowedposttags['label'] = $allowed_atts;
      $allowedposttags['input'] = $allowed_atts;
      $allowedposttags['textarea'] = $allowed_atts;
      $allowedposttags['iframe'] = $allowed_atts;
      $allowedposttags['script'] = $allowed_atts;
      $allowedposttags['style'] = $allowed_atts;
      $allowedposttags['strong'] = $allowed_atts;
      $allowedposttags['small'] = $allowed_atts;
      $allowedposttags['table'] = $allowed_atts;
      $allowedposttags['span'] = $allowed_atts;
      $allowedposttags['abbr'] = $allowed_atts;
      $allowedposttags['code'] = $allowed_atts;
      $allowedposttags['pre'] = $allowed_atts;
      $allowedposttags['div'] = $allowed_atts;
      $allowedposttags['img'] = $allowed_atts;
      $allowedposttags['h1'] = $allowed_atts;
      $allowedposttags['h2'] = $allowed_atts;
      $allowedposttags['h3'] = $allowed_atts;
      $allowedposttags['h4'] = $allowed_atts;
      $allowedposttags['h5'] = $allowed_atts;
      $allowedposttags['h6'] = $allowed_atts;
      $allowedposttags['ol'] = $allowed_atts;
      $allowedposttags['ul'] = $allowed_atts;
      $allowedposttags['li'] = $allowed_atts;
      $allowedposttags['em'] = $allowed_atts;
      $allowedposttags['hr'] = $allowed_atts;
      $allowedposttags['br'] = $allowed_atts;
      $allowedposttags['tr'] = $allowed_atts;
      $allowedposttags['td'] = $allowed_atts;
      $allowedposttags['p'] = $allowed_atts;
      $allowedposttags['a'] = $allowed_atts;
      $allowedposttags['b'] = $allowed_atts;
      $allowedposttags['i'] = $allowed_atts;
    }, 10);

    // Removes sticky posts from main loop. this function fixes issue of duplicate posts on archives
    //see https://wordpress.stackexchange.com/questions/225015/sticky-post-from-page-2-and-on
    add_action('pre_get_posts', function($q){
      // Only target the blog page // Only target the main query
      if ($q->is_home() && $q->is_main_query()) {

        // Remove sticky posts
        $q->set('ignore_sticky_posts', 1);

        // Get the sticky posts array
        $stickies = get_option('sticky_posts');

        // Make sure we have stickies before continuing, else, bail
        if (!$stickies) {
          return;
        }

        // Great, we have stickies, lets continue
        // Lets remove the stickies from the main query
        $q->set('post__not_in', $stickies);

        // Lets add the stickies to page one via the_posts filter
        if ($q->is_paged()) {
          return;
        }

        add_filter('the_posts', function ($posts, $q) use ($stickies) {

          // Make sure we only target the main query
          if (!$q->is_main_query()) {
            return $posts;
          }

          // Get the sticky posts
          $args = [
            'posts_per_page' => count($stickies),
            'post__in'       => $stickies
          ];
          $sticky_posts = get_posts($args);

          // Lets add the sticky posts in front of our normal posts
          $posts = array_merge($sticky_posts, $posts);

          return $posts;

        }, 10, 2);

      }
    });

    //  live search
    // add_filter('woocommerce_product_data_store_cpt_get_products_query', 'support_search_term_query_var', 10, 2);
    if($this->configs['live_search']) add_action('wp_ajax_ajax_live_search', 'ajax_live_search');
    if($this->configs['live_search']) add_action('wp_ajax_nopriv_ajax_live_search', 'ajax_live_search');

  }

  public function theme_enqueue_assets() {

    // theme base scripts  (uikit, lightgallery, fonts-awesome)
    wp_enqueue_script(
      'base-theme',
      get_template_directory_uri() . '/assets/js/base.js',
      '',
      '',
      false
    );

    // theme base css (uikit, lightgallery, fonts-awesome)
    wp_enqueue_style(
      'base-theme',
      get_template_directory_uri() . '/assets/css/base.css'
    );

    // theme stylesheet (theme)
    wp_enqueue_style(
      'base-theme-styles', get_stylesheet_uri()
    );

    // global
    wp_enqueue_script(
      'global',
      get_template_directory_uri() . '/assets/js/global.js',
      '',
      '1.0.0',
      true
    );

    if($this->configs['live_search']){

      // live search
      wp_enqueue_script(
        'live-search',
        get_template_directory_uri() . '/assets/js/srcs/live-search.js',
        array('jquery'),
        '1.0.0',
        true
      );

      // infused with ajax!
      wp_localize_script(
        'live-search',
        'myAjax',
        array(
          'ajaxurl' => admin_url('admin-ajax.php')
        )
      );
      
    }

    // meta galleries resources
    if($this->configs['meta_galleries']){

      // nano galleries css
      wp_enqueue_style(
        'nano-gallery',
        get_template_directory_uri() . '/assets/css/lib/nanogallery2.min.css'
      );
      
      // nano galleries js
      wp_enqueue_script(
        'nano-gallery',
        get_template_directory_uri() . '/assets/js/lib/jquery.nanogallery2.min.js',
        array('jquery'),
        '3.0.5',
        true
      );

    }

  }

  public function register_post_types() {}
  public function register_taxonomies() {}
  public function register_widget_areas() {}

  public function register_navigation_menus() {
    register_nav_menus(array(
      'main_menu' => _x( 'Main Menu', 'Menu locations', 'base-theme' ),
      'iconnav_menu' => _x( 'Iconnav Menu', 'Menu locations', 'base-theme' ),
    ));
  }

  public function add_to_context($context) {

    global $configs;

    // globals for twig
    $context['site'] = new \Timber\Site;
    $context['configs'] = $configs;
    $context['theme_defaults'] = $this->configs['theme_defaults'];

    // wp customizer logo
    $theme_logo_src = wp_get_attachment_image_url(get_theme_mod('custom_logo') , 'full');
    if($theme_logo_src){
      $context['theme']->logo = (object)[];
      $context['theme']->logo->src = $theme_logo_src;
      $context['theme']->logo->alt = '';
      $context['theme']->logo->w = $this->logo_width;
      $context['theme']->logo->h = $this->logo_height;
    }

    // menu register & args
    $main_menu_args = array('depth' => 3);
    $foot_menu_args = array('depth' => 1);

    $context['menu_main'] = new \Timber\Menu('main_menu', $main_menu_args);
    $context['has_menu_main'] = has_nav_menu('main_menu');
    
    $context['menu_iconnav'] = new \Timber\Menu('iconnav_menu', $foot_menu_args);
    $context['has_menu_iconnav'] = has_nav_menu('iconnav_menu');

    // return context
    return $context;

  }
  
  public function add_to_twig($twig) {
    $twig->addExtension(new \Twig_Extension_StringLoader());
    $twig->addExtension(new StringExtension());
		return $twig;
  }

}
