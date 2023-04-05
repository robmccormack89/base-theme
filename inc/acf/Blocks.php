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
    // add_action('enqueue_block_assets', array($this, 'enqueue_google_fonts'));
    // add_filter('style_loader_tag', array($this, 'preconnects_filter'), 10, 2);
  }

  /*
  Register blocks & enqueue scripts
  */

  public function register_blocks() {

    if(!function_exists('acf_register_block')) return;

    acf_register_block(array( // Advanced search section

      // *required
      'name' => 'advanced_search_section',
      'title' => 'Advanced search section',

      // the callback function
      'render_callback' => array($this, 'advanced_search_section_render_callback'),

      // what block settings does this block allow
      'supports' => array(
        'align' => array('full', 'wide', 'center', ''),
        'align_text' => false,
        'align_content' => false,
        'full_height' => false,
        // 'mode' => false,
        'jsx' => false
      ),

      // the defaults for various block settings
      'align' => '',
      // 'align_content' => 'center',

      // category & icon
      'category' => 'design',
      'icon' => 'align-pull-right',

      // keywords by which to search for the block
      'keywords' => array('advanced', 'search', 'ajax', 'rmcc'),

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
        'jsx' => true
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

  }
  public function acf_blocks_editor_scripts() {

    // swiper
    // wp_enqueue_style(
    //   'swiper',
    //   get_template_directory_uri() . '/assets/css/lib/swiper-bundle.min.css'
    // );
    // wp_enqueue_script(
    //   'swiper-js',
    //   get_template_directory_uri() . '/assets/js/lib/swiper-bundle.min.js',
    //   '',
    //   '1.0.0',
    //   false
    // );

    // blocks (lightgallery)
    // wp_enqueue_style(
    //   'rmcc-blocks',
    //   get_template_directory_uri() . '/inc/acf/blocks/assets/css/blocks.css'
    // );
    // wp_enqueue_script(
    //   'rmcc-blocks',
    //   get_template_directory_uri() . '/inc/acf/blocks/assets/js/blocks.js',
    //   '',
    //   '1.0.0',
    //   false
    // );

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

    // style.css
    wp_enqueue_style(
      'base-theme-styles', get_stylesheet_uri()
    );

    // adv search js
    // wp_enqueue_script(
    //   'rmcc-advanced-search',
    //   get_template_directory_uri() . '/assets/js/woo/advanced-search.js',
    //   '',
    //   '1.0.0',
    //   true
    // );

    // preconnects
    // wp_enqueue_style('picsum-preconnect', 'https://picsum.photos', '', null);
    // wp_enqueue_style('lorem-picsum-preconnect', 'https://i.picsum.photos', '', null);
    // wp_enqueue_style('picsum-prefetch', 'https://picsum.photos', '', null);
    // wp_enqueue_style('lorem-picsum-prefetch', 'https://i.picsum.photos', '', null);

  }
  public function enqueue_google_fonts() {
    // wp_enqueue_style( 'rmcc-google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Sarabun:wght@400;700;800&display=swap', array(), null );
    // preconnects
    // wp_enqueue_style('picsum-preconnect', 'https://picsum.photos', '', null);
    // wp_enqueue_style('lorem-picsum-preconnect', 'https://i.picsum.photos', '', null);
    // wp_enqueue_style('picsum-prefetch', 'https://picsum.photos', '', null);
    // wp_enqueue_style('lorem-picsum-prefetch', 'https://i.picsum.photos', '', null);
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

  public function cover_section_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;

    Timber::render('cover-section.twig', $context);
  }
  public function advanced_search_section_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;

    Timber::render('advanced-search-section.twig', $context);
  }

}
