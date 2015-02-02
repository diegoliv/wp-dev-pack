<?php

/**
 * Theme functions and definitions
 *
 * @package Theme_Name
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Theme_Name{

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   1.0.0
     * @var     string
     */
    const VERSION = '1.0.0';

    /**
     * @since    1.0.0
     * @var      string
     */
    public $theme_slug = '';

    /**
     * @since    1.0.0
     * @var      string
     */
    protected $assets_dir = null;

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     * @var      object
     */
    protected static $instance = null;

    /**
     * @since     1.0.0
     */
    function __construct() {

        $this->assets_dir = get_template_directory_uri() . '/dist/assets/';

        // Add support
        if ( function_exists( 'add_theme_support' ) ) {
            add_theme_support( 'menus' );
            // theme thumbnails
            add_theme_support( 'post-thumbnails' );

            add_image_size( 'mini', 200, 170, true );

            // Localisation Support
            load_theme_textdomain( $this->theme_slug, get_template_directory() . '/languages' );            

        }

        add_action( 'admin_init', array( $this, 'register_menus') );
        add_action( 'widgets_init', array( $this, 'register_sidebar') );

        // Load public-facing style sheet and JavaScript.
        add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
        add_action( 'wp_print_scripts', array( $this, 'do_scripts' ) );
        add_action( 'wp_print_styles', array( $this, 'do_styles' ) );
        add_action( 'admin_init', array( $this, 'editor_styles') );

        // Add Filters
        add_filter( 'avatar_defaults', array( $this, 'blankgravatar') ); // Custom Gravatar in Settings > Discussion
        add_filter( 'body_class', array( $this, 'add_slug_to_body_class' ) ); // Add slug to body class (Starkers build)
        add_filter( 'post_class', array( $this, 'has_thumb_class' ) ); // Add "has-thumbnail" class to post_class if post has thumbnail
        add_filter( 'wp_nav_menu_objects', array( $this, 'add_menu_parent_class' ) ); // Add "has-submenu" class to parent items in menu

        add_filter( 'post_thumbnail_html', array( $this, 'remove_thumbnail_dimensions' ), 10 ); // Remove width and height dynamic attributes to thumbnails
        add_filter( 'image_send_to_editor', array( $this, 'remove_thumbnail_dimensions' ), 10 ); // Remove width and height dynamic attributes to post images
        add_filter( 'the_category', array( $this, 'remove_category_rel_from_category_list' ) ); // Remove invalid rel attribute
        add_filter( 'style_loader_tag', array( $this, 'html5_style_remove' ) ); // Remove 'text/css' from enqueued stylesheet


        // Remove Actions
        remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
        remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
        remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
        remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
        remove_action('wp_head', 'index_rel_link'); // Index link
        remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
        remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
        remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
        remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
        remove_action('wp_head', 'start_post_rel_link', 10, 0);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
        remove_action('wp_head', 'rel_canonical');
        remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function register_styles(){
        // styles
        wp_register_style( 'pure-css', $this->assets_dir . 'css/pure.min.css', array(), self::VERSION );
        wp_register_style( 'main', $this->assets_dir . 'css/main.min.css', array(), self::VERSION );
        wp_register_style( 'web-fonts', 'http://fonts.googleapis.com/css?family=Exo+2:700italic,300italic|Open+Sans:400italic,700italic,300,400,700' );
        wp_register_style( 'web-fonts-logged', 'http://fonts.googleapis.com/css?family=Exo+2:700italic,300italic' );
    }

    public function do_styles(){
        
        if( is_user_logged_in() ){
            wp_enqueue_style( 'web-fonts-logged' );
        } else{
            wp_enqueue_style( 'web-fonts' );
        }

        wp_enqueue_style( 'pure-css' );
        wp_enqueue_style( 'main' );
    }

    public function editor_styles(){
        add_editor_style( $this->assets_dir . 'css/editor-styles.min.css' );
    }

    public function register_scripts(){
        wp_register_script( $this->theme_slug . '-script', $this->assets_dir . 'js/main.js', array( 'jquery' ), self::VERSION, true );                
    }

    public function do_scripts(){

        // check if jquery is enqueued
        if ( !wp_script_is( 'jquery' ) ) {
            wp_enqueue_script( 'jquery' );
        }

        wp_enqueue_script( $this->theme_slug . '-script' );

        if ( !is_admin() ) {
            if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                wp_enqueue_script( 'comment-reply' );
            }
        }

    }


    // Register the menus
    public function register_menus() {
        register_nav_menus( array(
            'header' => __( 'Main Menu', $this->theme_slug ),
            'footer' => __( 'Footer', $this->theme_slug ),
        ) );
    }

    // Register the sidebar
    public function register_sidebar() {
        register_sidebar( array(
            'name'          => __( 'Blog - Sidebar', $this->theme_slug ),
            'id'            => 'theme-sidebar',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title primary header-title">',
            'after_title'   => '</h3>',
        ) );

        register_sidebar( array(
            'name'          => __( 'Footer', $this->theme_slug ),
            'id'            => 'footer',
            'description'   => __( 'Primary links of the footer', $this->theme_slug ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ) );

    }

    // Custom Gravatar in Settings > Discussion
    public function blankgravatar ( $avatar_defaults ) {
        $myavatar = $this->assets_dir . '/img/gravatar.jpg';
        $avatar_defaults[$myavatar] = __( "Custom Gravatar", $this->theme_slug );
        return $avatar_defaults;
    }

    // Add page slug to body class, love this - Credit: Starkers Wordpress Theme
    public function add_slug_to_body_class( $classes ) {
        global $post;
        if ( is_home() ) {
            $key = array_search( 'blog', $classes );
            if ($key > -1) {
                unset( $classes[$key] );
            }
        } elseif ( is_page() ) {
            $classes[] = sanitize_html_class($post->post_name);
        } elseif ( is_singular() ) {
            $classes[] = sanitize_html_class($post->post_name);
        }

        return $classes;
    }

    // add class if post has thumbnail
    function has_thumb_class( $classes ) {
        global $post;
        if( has_post_thumbnail( $post->ID ) ) { $classes[] = 'has-thumbnail'; }

            return $classes;
    }

    // Add class "has-submenu" to parent items of menus
    public function add_menu_parent_class( $items ) {
        
        $parents = array();
        foreach ( $items as $item ) {
            if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
                $parents[] = $item->menu_item_parent;
            }
        }
        
        foreach ( $items as $item ) {
            if ( in_array( $item->ID, $parents ) ) {
                $item->classes[] = 'has-submenu'; 
            }
        }
        
        return $items;    
    }

    // Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
    public function remove_thumbnail_dimensions( $html ) {
        $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
        return $html;
    }

    // Remove invalid rel attribute values in the categorylist
    public function remove_category_rel_from_category_list( $thelist ) {
        return str_replace( 'rel="category tag"', 'rel="tag"', $thelist );
    }

    // Remove 'text/css' from our enqueued stylesheet
    public function html5_style_remove( $tag ) {
        return preg_replace( '~\s+type=["\'][^"\']++["\']~', '', $tag );
    }

}

$theme = new Theme_Name();

require get_template_directory() . '/inc/theme.menu-walker.php'; // Custom Walker Class
require get_template_directory() . '/inc/theme.helpers.php'; // Helper functions