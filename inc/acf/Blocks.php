<?php

namespace Rmcc;
use Timber\Timber;
use Timber\Post;
use Timber\PostQuery;

array_unshift(
  Timber::$dirname,
  'views/blocks',
);

class Blocks {

  public function __construct() {
    add_action('acf/init', array($this, 'register_blocks'));
    add_action('enqueue_block_assets', array($this, 'acf_blocks_editor_scripts')); // use 'enqueue_block_editor_assets' for backend-only
    add_action('enqueue_block_assets', array($this, 'enqueue_google_fonts'));

    // add_filter('style_loader_tag', array($this, 'preconnects_filter'), 10, 2);
  }

  /*
  Register blocks & enqueue scripts
  */

  public function register_blocks() {

    // old

    // acf_register_block(array( // Advanced search section

    //   // *required
    //   'name' => 'advanced_search_section',
    //   'title' => 'Advanced search section',

    //   // the callback function
    //   'render_callback' => array($this, 'advanced_search_section_render_callback'),

    //   // what block settings does this block allow
    //   'supports' => array(
    //     'align' => array('full', 'wide', 'center', ''),
    //     'align_text' => false,
    //     'align_content' => false,
    //     'full_height' => false,
    //     // 'mode' => false,
    //     'jsx' => false
    //   ),

    //   // the defaults for various block settings
    //   'align' => '',
    //   // 'align_content' => 'center',

    //   // category & icon
    //   'category' => 'design',
    //   'icon' => 'align-pull-right',

    //   // keywords by which to search for the block
    //   'keywords' => array('advanced', 'search', 'ajax', 'rmcc'),

    // ));

    // acf_register_block(array( // cover section

    //   // *required
    //   'name' => 'cover_section',
    //   'title' => 'Cover section',

    //   // the callback function
    //   'render_callback' => array($this, 'cover_section_render_callback'),

    //   // what block settings does this block allow
    //   'supports' => array(
    //     'align' => array('full', 'wide', 'center', ''),
    //     'align_text' => false,
    //     'align_content' => false,
    //     'full_height' => true,
    //     // 'mode' => false,
    //     'jsx' => true
    //   ),

    //   // the defaults for various block settings
    //   'align' => 'full',
    //   'full_height' => false,
    //   // 'align_content' => '',
    //   'mode' => 'preview',

    //   // category & icon
    //   'category' => 'design',
    //   'icon' => 'cover-image',

    //   // keywords by which to search for the block
    //   'keywords' => array('cover', 'section', 'rmcc'),

    // ));

    // acf_register_block(array( // icon block 
    
    //   // *required
    //   'name' => 'icon_block_section',
    //   'title' => 'Icon Block',
    
    //   // the callback function
    //   'render_callback' => array($this, 'icon_block_section_render_callback'),
    
    //   // what block settings does this block allow
    //   'supports' => array(
    //     'align' => false, 
    //     'align_text' => true,
    //     'align_content' => true,
    //     'full_height' => false,
    //     // 'mode' => false,
    //     'jsx' => true
    //   ),
    
    //   // the defaults for various block settings
    //   // 'align' => 'full',
    //   // 'full_height' => false,
    //   'align_text' => 'left',
    //   'align_content' => 'center',
    //   'mode' => 'preview',
    
    //   // category & icon
    //   'category' => 'design',
    //   'icon' => 'cover-image',
    
    //   // keywords by which to search for the block
    //   'keywords' => array('icon', 'block', 'section', 'rmcc'),
    
    // ));

    if(!function_exists('acf_register_block')) return;
    
    acf_register_block(array( // content grid section 
    
      // *required
      'name' => 'content_grid_section',
      'title' => 'Content Grid section',
    
      // the callback function
      'render_callback' => array($this, 'content_grid_section_render_callback'),
    
      // what block settings does this block allow
      'supports' => array(
        'align' => array('full', 'wide', 'center', ''), 
        'align_text' => false,
        'align_content' => false,
        'full_height' => false,
        // 'mode' => false,
        // 'jsx' => true
      ),
    
      // the defaults for various block settings
      'align' => '',
      // 'full_height' => false,
      // 'align_content' => '',
      'mode' => 'preview',
    
      // category & icon
      'category' => 'design',
      'icon' => 'cover-image',
    
      // keywords by which to search for the block
      'keywords' => array('content', 'grid', 'section', 'rmcc'),
    
    ));
    
    acf_register_block(array( // cover section 
    
      // *required
      'name' => 'cover_section',
      'title' => 'Cover section',
    
      // the callback function
      'render_callback' => array($this, 'cover_section_render_callback'),
    
      // what block settings does this block allow
      'supports' => array(
        'align' => array('full', 'wide', 'center', ''), 
        'align_text' => false,
        'align_content' => false,
        'full_height' => true,
        // 'mode' => false,
        'jsx' => true,
        'color'           => [
          'background' => true,
          'gradients'  => true,
          'text'       => false,
        ],
      ),
    
      // the defaults for various block settings
      'align' => 'full',
      'full_height' => false,
      // 'align_content' => '',
      'mode' => 'preview',
    
      // category & icon
      'category' => 'design',
      'icon' => 'cover-image',
    
      // keywords by which to search for the block
      'keywords' => array('cover', 'section', 'rmcc'),
    
    ));
    
    acf_register_block(array( // Testimonials 
    
      // *required
      'name' => 'testimonial',
      'title' => 'Testimonial',
    
      // the callback function
      'render_callback' => array($this, 'testimonial_render_callback'),

      // what block settings does this block allow
      'supports' => array(
        // 'align' => array('center'), 
        'align' => false, 
        'align_text' => true, 
        'align_content' => false, 
        'full_height' => false, 
        // 'mode' => 'preview',
        'mode' => false,
        'jsx' => true
      ),
    
      // the defaults for various block settings
      // 'align' => 'center', 
      'align_text' => 'center', 
      'mode' => 'preview',
    
      // category & icon
      'category' => 'design',
      'icon' => 'editor-quote',
    
      // keywords by which to search for the block
      'keywords' => array('testimonials', 'rating', 'section', 'rmcc'),
    
    ));
    
    acf_register_block(array( // swiper script 
    
      // *required
      'name' => 'swiper_script ',
      'title' => 'Swiper Script',
    
      // the callback function
      'render_callback' => array($this, 'swiper_script_render_callback'),
    
      // what block settings does this block allow
      'supports' => array(
        'align' => false, 
        'align_text' => false,
        'align_content' => false,
        'full_height' => false,
        // 'mode' => false,
      ),
    
      // the defaults for various block settings
      'mode' => 'preview',
    
      // category & icon
      'category' => 'design',
      'icon' => 'cover-image',
    
      // keywords by which to search for the block
      'keywords' => array('cover', 'section', 'rmcc'),
    
    ));

  }
  public function acf_blocks_editor_scripts() {

    // gutenberg editor styles
    if(is_admin()){
      // base
      wp_enqueue_style(
        'base-theme',
        get_template_directory_uri() . '/assets/css/base.css'
      );
      wp_enqueue_script(
        'base-theme',
        get_template_directory_uri() . '/assets/js/base.js',
        '',
        '',
        false
      );
      // theme stylesheet (theme)
      wp_enqueue_style(
        'base-theme-styles', get_stylesheet_uri()
      );
      wp_enqueue_style(
        'admin-editor-theme',
        get_template_directory_uri() . '/assets/css/admin-editor.css'
      );
    }
    
    // swiper (frontend only)
    if(!is_admin()){
      wp_enqueue_style(
        'swiper',
        // get_template_directory_uri() . '/assets/css/lib/swiper-bundle.min.css'
        'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css'
      );
      wp_enqueue_script(
        'swiper-js',
        // get_template_directory_uri() . '/assets/js/lib/swiper-bundle.min.js',
        'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js',
        '',
        '1.0.0',
        false
      );
      wp_enqueue_style(
        'frontend-editor-theme',
        get_template_directory_uri() . '/assets/css/frontend.css'
      );
    }

  }
  public function enqueue_google_fonts() {
    // wp_enqueue_style( 'rmcc-google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Sarabun:wght@400;700;800&display=swap', array(), null );
    // preconnects
    // wp_enqueue_style('picsum-preconnect', 'https://picsum.photos', '', null);
    // wp_enqueue_style('lorem-picsum-preconnect', 'https://i.picsum.photos', '', null);
    // wp_enqueue_style('picsum-prefetch', 'https://picsum.photos', '', null);
    // wp_enqueue_style('lorem-picsum-prefetch', 'https://i.picsum.photos', '', null);

    // wp_enqueue_style( 'rmcc-google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Sarabun:wght@400;700;800&display=swap', array(), null );
  }
  public function preconnects_filter($html, $handle) {
    if ($handle === 'picsum-preconnect') {
      return str_replace("rel='stylesheet'",
        "rel='preconnect'", $html);
    }
    if ($handle === 'lorem-picsum-preconnect') {
      return str_replace("rel='stylesheet'",
        "rel='preconnect'", $html);
    }
    if ($handle === 'picsum-prefetch') {
      return str_replace("rel='stylesheet'",
        "rel='dns-prefetch'", $html);
    }
    if ($handle === 'lorem-picsum-prefetch') {
      return str_replace("rel='stylesheet'",
        "rel='dns-prefetch'", $html);
    }
    return $html;
  }

  /*
  Sections (with inners)
  */
  
  public function content_grid_section_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
    
    $service_types = array(
     'taxonomy' => 'service-type',
     'hide_empty' => false,
     'orderby' => 'slug',
     'order' => 'DESC',
    );
    $context['service_types'] = get_terms($service_types);
    
    $services = array(
     'post_type' => 'services-repairs',
     'post_status' => 'publish'
    );
    $context['services'] = new \Timber\PostQuery($services);
    
    Timber::render('content-grid-section.twig', $context);
  }
  
  public function swiper_script_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
    
    Timber::render('swiper-script.twig', $context);
  }
  
  public function cover_section_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
    
    Timber::render('cover-section.twig', $context);
  }
  
  public function testimonial_render_callback($block, $content = '', $is_preview = false) { 
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
    
    Timber::render('testimonial.twig', $context);
  }

  /*
  OLD
  */

  public function _icon_block_section_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
    
    Timber::render('icon-block.twig', $context);
  }

  public function __cover_section_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;

    Timber::render('cover-section.twig', $context);
  }
  public function __advanced_search_section_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;

    Timber::render('advanced-search-section.twig', $context);
  }

}
