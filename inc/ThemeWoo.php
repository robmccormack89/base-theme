<?php
namespace Rmcc;
use Timber\Timber;

// Define paths to Twig templates
array_push(
  Theme::$dirname, 
  'views/woo', 
  'views/woo/product', 
  'views/woo/shop'
);

// Define Theme Child Class
class ThemeWoo extends Theme {
  
  public function __construct() {
    parent::__construct();
    add_action('after_setup_theme', array($this, 'woo_support'));
    add_filter('timber/twig', array($this, 'add_to_twig'));
    add_filter('timber/context', array($this, 'add_to_context'));
    add_action('init', array($this, 'register_product_taxonomies'));
    add_action('wp_enqueue_scripts', array($this, 'dequeue_enqueue_woo_script_styles'), 99);
    
    $this->add_custom_actions_filters();
    $this->remove_woo_actions();
    
  }
  
  /**
  *
  * Add in custom actions or filter existing content
  *
  */
  
  public function add_custom_actions_filters() {
    
    add_filter('query_vars', 'add_grid_list_query_vars');
    add_filter('body_class', 'add_woocommerce_to_body_classes');
    
    // product post type labels
    add_filter('woocommerce_register_post_type_product', 'filter_product_post_type_labels');
    
    // shop sorting options labels
    add_filter('woocommerce_catalog_orderby', 'filter_shop_sorting_options_labels' );
    
    // cart item remove link icon
    add_filter('woocommerce_cart_item_remove_link', 'filter_cart_remove_link_icon_html', 10, 2);
    
    // custom product tab
    add_filter('woocommerce_product_tabs', 'add_parts_custom_tab');
    
    //  live search
    add_filter('woocommerce_product_data_store_cpt_get_products_query', 'support_search_term_query_var', 10, 2);
    add_action('wp_ajax_ajax_live_search', 'ajax_live_search');
    add_action('wp_ajax_nopriv_ajax_live_search', 'ajax_live_search');
    add_action('wp_ajax_ajax_live_search_mobile', 'ajax_live_search_mobile');
    add_action('wp_ajax_nopriv_ajax_live_search_mobile', 'ajax_live_search_mobile');
    
    // adv search
    add_action('rest_api_init', 'adv_live_search_ajax_restapi_routes');
    
    // sku in product searches
    add_action('pre_get_posts', 'make_sku_work_in_product_searches');
    
    // custom actions
    add_action('theme_before_header', 'add_custom_demo_store_notice');
    // add_action('custom_stock_quantity', 'custom_stock_quantity');
    
    // collections
    add_action('pre_get_posts', 'disable_pagination_on_collections');
    
    // custom result count & subtotal with ajax
    add_filter('woocommerce_add_to_cart_fragments', 'result_count_ajaxify', 10, 1);
    add_filter('woocommerce_add_to_cart_fragments', 'subtotal_ajaxify', 10, 1);
    
  }
  
  /**
  *
  * Remove default woo actions
  *
  */
  
  public function remove_woo_actions() {
    
    /**
    *
    * Site-wide
    *
    */
    
    remove_action('wp_footer', 'woocommerce_demo_store', 10); // remove demo store notice
    
    /**
    *
    * Woo-genral
    *
    */
    
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10); // remove unneccesary wrapping
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20); // remove woo breads
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10); // remove unneccesary wrapping
    
    /**
    *
    * Shop & teases
    *
    */
    
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20); // remove result count (shop)
    
    remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10); // remove unneccesary link wrapping (tease)
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10); // remove default featured image (tease)
    remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10); // remove title (tease)
    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5); // remove star rating (tease)
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5); // remove unneccesary link wrapping (tease)
    
    remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10); // remove pagination (shop)
    
    /**
    *
    * Single product
    *
    */
    
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 ); // remove upsells
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 ); // remove relateds
    
  }
  
  /**
  *
  * Dequeue woocommerce's default scripts & styles selectively
  * then enqueue our own
  *
  */
  
  public function dequeue_enqueue_woo_script_styles() {
    
    remove_action('wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );
    wp_dequeue_style( 'woocommerce_frontend_styles' );
    wp_dequeue_style( 'woocommerce-general');
    wp_dequeue_style( 'woocommerce-layout' );
    wp_dequeue_style( 'woocommerce-smallscreen' );
    wp_dequeue_style( 'woocommerce_fancybox_styles' );
    wp_dequeue_style( 'woocommerce_chosen_styles' );
    wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
    wp_dequeue_script( 'selectWoo' );
    wp_deregister_script( 'selectWoo' );
    wp_dequeue_script( 'select2' );
    wp_deregister_script( 'select2' );
    
    // jquery
    wp_enqueue_script('jquery');
    
    // global (requires jQuery)
    wp_enqueue_script(
      'globalwoo',
      get_template_directory_uri() . '/assets/js/woo/globalwoo.js',
      'jquery',
      '1.0.0',
      true
    );
   
    // localize global for ajax
    wp_localize_script(
      'globalwoo',
      'myAjax',
      array(
        'ajaxurl' => admin_url('admin-ajax.php')
      )
    );
    
    wp_enqueue_style(
      'globalwoostyles',
      get_template_directory_uri() . '/assets/css/woo.css'
    );
    
  }
  
  /**
  *
  * Add a custom taxonomy (product_series) to woocommerce products
  * Uses translatable strings as labels
  *
  */
  
  public function register_product_taxonomies() {
    
    // series models (for products)
    $labels_series = array(
      'name'                       => _x( 'Series/Models', 'Series/Models: plural name', 'massey-theme' ),
  		'singular_name'              => _x( 'Series/Model', 'Series/Models: singular name', 'massey-theme' ),
  		'menu_name'                  => _x( 'Series/Models', 'Series/Models: plural name', 'massey-theme' ),
  		// 'all_items'                  => __('All Series/Models', 'ned-murphy-tractors-theme'),
  		// 'parent_item'                => __('Parent (Series)', 'ned-murphy-tractors-theme'),
  		// 'parent_item_colon'          => __('Parent (Series):', 'ned-murphy-tractors-theme'),
  		// 'new_item_name'              => __('New Series/Model Name', 'ned-murphy-tractors-theme'),
  		// 'add_new_item'               => __('Add New Series/Model', 'ned-murphy-tractors-theme'),
  		// 'edit_item'                  => __('Edit Series/Model', 'ned-murphy-tractors-theme'),
  		// 'update_item'                => __('Update Series/Model', 'ned-murphy-tractors-theme'),
  		// 'view_item'                  => __('View Series/Model', 'ned-murphy-tractors-theme'),
  		// 'separate_items_with_commas' => __('Separate items with commas', 'ned-murphy-tractors-theme'),
  		// 'add_or_remove_items'        => __('Add or remove Series/Model', 'ned-murphy-tractors-theme'),
  		// 'choose_from_most_used'      => __('Choose from the most used', 'ned-murphy-tractors-theme'),
  		// 'popular_items'              => __('Popular Series/Models', 'ned-murphy-tractors-theme'),
  		// 'search_items'               => __('Search Series/Models', 'ned-murphy-tractors-theme'),
  	);
  	$rewrite_series = array(
  		'slug'                       => _x( 'product-series-model', 'Series/Models: archive slug', 'massey-theme' ),
  		'with_front'                 => true,
  		'hierarchical'               => true,
  	);
  	$args_series = array(
  		'labels'                     => $labels_series,
  		'hierarchical'               => true,
  		'public'                     => true,
  		'show_ui'                    => true,
  		'show_admin_column'          => true,
  		'show_in_nav_menus'          => true,
  		'show_tagcloud'              => true,
  		'rewrite'                    => $rewrite_series,
  		'update_count_callback'      => 'count_product_series',
  		'show_in_rest'               => true,
      'update_count_callback' => '_update_post_term_count',
  	);
  	register_taxonomy('product_series', array('product'), $args_series);
    
  }
  
  /**
  *
  * Regular woo to twig swtuff
  *
  */
  
  public function woo_support() {
    // woo supports
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
  }
  public function add_to_context($context) {
    
    $context['WC'] = WC();
    
    // woo my account endpoints
    $context['dashboard_endpoint'] = wc_get_account_endpoint_url('dashboard');
    $context['address_endpoint'] = wc_get_account_endpoint_url('edit-address');
    $context['edit_endpoint'] = wc_get_account_endpoint_url('edit-account');
    $context['payment_endpoint'] = wc_get_account_endpoint_url('payment-methods');
    $context['lost_endpoint'] = wc_get_account_endpoint_url('lost-password');
    $context['orders_endpoint'] = wc_get_account_endpoint_url('orders');
    $context['logout_endpoint'] = wc_get_account_endpoint_url('customer-logout');
    
    // woo endpoints
    $context['shop_url'] = get_permalink(wc_get_page_id('shop'));
    $context['checkout_url'] = wc_get_checkout_url();
    $context['cart_url'] = wc_get_cart_url();
    
    // woo address fields
    $context['base_address'] = WC()->countries->get_base_address();
    $context['base_address_2'] = WC()->countries->get_base_address_2();
    $context['base_city'] = WC()->countries->get_base_city();
    $context['base_eircode'] = WC()->countries->get_base_postcode();
    $context['base_county'] = WC()->countries->get_base_state();
    $context['base_country'] = WC()->countries->get_base_country();
    
    $context['_get'] = $_GET;
    
    return $context;    
  }
  public function add_to_twig($twig) {
    return $twig;
  }
  

}