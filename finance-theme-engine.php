<?php

/**
 * Plugin Name:     Finance Advisor Theme Engine
 * Description:     The Finance Theme Engine plugin
 *
 * Version:         1.3.7
 *
 * GitHub URI:      finance-advisor/
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * financen Finance_Theme_Engine Class.
 *
 * @since 1.0.0
 */
final class Finance_Theme_Engine {

	/**
	 * @var Finance_Theme_Engine The one true Finance_Theme_Engine
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * financen Finance_Theme_Engine Instance.
	 *
	 * Insures that only one instance of Finance_Theme_Engine exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   1.0.0
	 * @static  var array $instance
	 * @uses    Finance_Theme_Engine::setup_constants() Setup the constants needed.
	 * @uses    Finance_Theme_Engine::includes() Include the required files.
	 * @return  object | Finance_Theme_Engine The one true Finance_Theme_Engine
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Finance_Theme_Engine;
			// Methods
			self::$instance->setup_constants();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return  void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'finance-theme-engine' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @return  void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'finance-theme-engine' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function setup_constants() {

		// Plugin version.
		define( 'Finance_Theme_Engine_VERSION', '1.3.7' );

		// DB version.
		define( 'Finance_Theme_Engine_DB_VERSION', '1161' );

		// Plugin Folder Path.
		define( 'Finance_Theme_Engine_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		// Plugin Lib Path.
		define( 'Finance_Theme_Engine_LIB_DIR', Finance_Theme_Engine_PLUGIN_DIR . 'lib/' );

		// Plugin Includes Path.
		define( 'Finance_Theme_Engine_INCLUDES_DIR', Finance_Theme_Engine_PLUGIN_DIR . 'includes/' );

		// Plugin Folder URL.
		define( 'Finance_Theme_Engine_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		// Plugin Root File.
		define( 'Finance_Theme_Engine_PLUGIN_FILE', __FILE__ );

		// Plugin Base Name.
		define( 'Finance_Theme_Engine_BASENAME', dirname( plugin_basename( __FILE__ ) ) );

	}

	/**
	 * Include required files.
	 *
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function hooks() {

		// Includes (Vendor).
		require_once Finance_Theme_Engine_INCLUDES_DIR . 'CMB2/init.php'; // v2.4.2
		require_once Finance_Theme_Engine_INCLUDES_DIR . 'plugin-update-checker/plugin-update-checker.php'; // v4.4

		// Run the updater.
		add_action( 'admin_init', function() {

			// Bail if current user cannot manage plugins.
			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}

			// Setup the updater.
			$updater = Puc_v4_Factory::buildUpdateChecker( 'https://github.com/rickysokhi/finance-advisor/finance-theme-engine/', __FILE__, 'finance-theme-engine' );

			/**
			 * Allow branch and updater object manipulation.
			 * This let's us do beta releases via a branch change,
			 * among other things.
			 */
			$updater->setBranch( apply_filters( 'finace_updater_branch', 'master' ) );

			// Allow tokens to be used to bypass GitHub rate limit.
			if ( defined( 'FINANCE_UPDATER_TOKEN' ) ) {
				$updater->setAuthentication( FINANCE_UPDATER_TOKEN );
			}

			// Add icons for Dashboard > Updates screen.
			$updater->addResultFilter( function( $info, $response = null ) {
				$info->icons = array(
					'1x' => Finance_Theme_Engine_PLUGIN_URL . 'assets/images/icon-128x128.png',
					'2x' => Finance_Theme_Engine_PLUGIN_URL . 'assets/images/icon-256x256.png',
				);
				return $info;
			});
		});

		/**
		 * Include files after theme is loaded, to mimic being run in a child theme.
		 */
		add_action( 'genesis_setup', function() {

			// Do not load old stuff
			add_filter( 'genesis_load_deprecated', '__return_false' );

			// Add HTML5 markup structure
			add_theme_support( 'html5' );

			// Add title tag support
			add_theme_support( 'title-tag' );

			// Add viewport meta tag for mobile browsers
			add_theme_support( 'genesis-responsive-viewport' );

			add_theme_support( 'genesis-menus', array(
				'primary'      => __( 'Primary Menu', 'finance-theme-engine' ),
				'header_left'  => __( 'Header Left Menu', 'finance-theme-engine' ),
				'header_right' => __( 'Header Right Menu', 'finance-theme-engine' ),
				'secondary'    => __( 'Footer Menu', 'finance-theme-engine' ),
				'mobile'       => __( 'Mobile Menu', 'finance-theme-engine' ),
			) );

			// Add support for structural wraps
			add_theme_support( 'genesis-structural-wraps', array(
				'archive-description',
				'breadcrumb',
				'header',
				'menu-primary',
				'menu-secondary',
				'footer-widgets',
				'footer',
			) );

			// Add Accessibility support
			add_theme_support( 'genesis-accessibility', array(
				'404-page',
				'drop-down-menu',
				'headings',
				'search-form',
				'skip-links',
			) );

			// Add custom logo support
			add_theme_support( 'custom-logo', array(
				'height'        => 120, // Optional size
				'width'         => 240, // Optional size
				'flex-height'   => true,
				'flex-width'    => true,
			) );

			// Add excerpt support for pages
			add_post_type_support( 'page', 'excerpt' );

			/**
			 * Move Genesis child theme style sheet to a later priority
			 * to make sure Finance Theme Engine loads CSS first.
			 */
			remove_action( 'genesis_meta', 'genesis_load_stylesheet' );
			add_action( 'wp_enqueue_scripts', 'genesis_enqueue_financen_stylesheet', 999 );

			// Load default favicon.
			add_filter( 'genesis_pre_load_favicon', 'finance_default_favicon' );
			function finance_default_favicon( $favicon ) {
				return Finance_Theme_Engine_PLUGIN_URL . 'assets/images/favicon.png';
			}

			/**
			 * Create the initial image sizes.
			 * @link http://andrew.hedges.name/experiments/aspect_ratio/
			 */
			$image_sizes = array(
				'banner' => array(
					'width'  => 1600,
					'height' => 533,
					'crop'   => true, // 3x1
				),
				'featured' => array(
					'width'  => 800,
					'height' => 600,
					'crop'   => true, // 4x3 (works better for no sidebar)
				),
				'one-half' => array(
					'width'  => 550,
					'height' => 413,
					'crop'   => true, // 4x3
				),
				'one-third' => array(
					'width'  => 350,
					'height' => 263,
					'crop'   => true, // 4x3
				),
				'one-fourth' => array(
					'width'  => 260,
					'height' => 195,
					'crop'   => true, // 4x3
				),
				'tiny' => array(
					'width'  => 80,
					'height' => 80,
					'crop'   => true, // square
				),
			);

			/**
			 * Filter the image sizes to allow the theme to override.
			 *
			 * // Change the default Finance image sizes
			 * add_filter( 'finance_image_sizes', 'prefix_custom_image_sizes' );
			 * function prefix_custom_image_sizes( $image_sizes ) {
			 *
			 *   // Change one-third image size
			 *   $image_sizes['one-third'] = array(
			 *       'width'  => 350,
			 *       'height' => 350,
			 *       'crop'   => true,
			 *   );
			 *
			 *   // Change one-fourth image size
			 *   $image_sizes['one-fourth'] = array(
			 *       'width'  => 260,
			 *       'height' => 260,
			 *       'crop'   => true,
			 *   );
			 *
			 *   return $image_sizes;
			 *
			 * }
			 *
			 */
			$image_sizes = apply_filters( 'finance_image_sizes', $image_sizes );

			// Loop through and add the image sizes.
			foreach ( $image_sizes as $name => $data ) {
				add_image_size( $name, $data['width'], $data['height'], $data['crop'] );
			}

		}, 15 );

		/**
		 * Load our files after theme setup but at an earlier hook
		 * so devs can override some actions/filters without needing to specific priority.
		 */
		add_action( 'after_setup_theme', function(){

			/**
			 * Deactivate theme and show notice
			 * if Finance Theme Engine is not supported in the child theme.
			 * or if Finance Pro Engine is not supported in the child theme (backwards compatibility).
			 *
			 * @link https://10up.com/blog/2012/wordpress-plug-in-self-deactivation/
			 */
			if ( ! current_theme_supports( 'finance-theme-engine' ) && ! current_theme_supports( 'finance-pro-engine' ) ) {
				add_action( 'admin_init',    array( $this, 'deactivate_plugin' ) );
				add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				return;
			}

			// Lib.
			foreach ( glob( Finance_Theme_Engine_LIB_DIR . '*.php' ) as $file ) { include_once $file; }
			foreach ( glob( Finance_Theme_Engine_LIB_DIR . 'admin/*.php' ) as $file ) { include_once $file; }
			foreach ( glob( Finance_Theme_Engine_LIB_DIR . 'classes/*.php' ) as $file ) { include_once $file; }
			foreach ( glob( Finance_Theme_Engine_LIB_DIR . 'functions/*.php' ) as $file ) { include_once $file; }
			foreach ( glob( Finance_Theme_Engine_LIB_DIR . 'settings/customizer/*.php' ) as $file ) { include_once $file; }
			foreach ( glob( Finance_Theme_Engine_LIB_DIR . 'settings/metaboxes/*.php' ) as $file ) { include_once $file; }
			foreach ( glob( Finance_Theme_Engine_LIB_DIR . 'structure/*.php' ) as $file ) { include_once $file; }

		}, 8 );

	}

	function deactivate_plugin() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	function admin_notices() {
		printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', __( '<strong>Your theme does not support the Finance Theme Engine plugin</strong>. As a result, this plugin has been deactivated.', 'finance-theme-engine' ) );
		// Remove "Plugin activated" notice.
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

}

// This is only here for backwards compatibility with older Finance Pro themes.
final class Finance_Pro_Engine {}

/**
 * The financen function for that returns Finance_Theme_Engine
 *
 * The financen function responsible for returning the one true Finance_Theme_Engine
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $plugin = Finance_Theme_Engine(); ?>
 *
 * @since 1.0.0
 *
 * @return object|Finance_Theme_Engine The one true Finance_Theme_Engine Instance.
 */
function Finance_Theme_Engine() {
	return Finance_Theme_Engine::instance();
}

// Get Finance_Theme_Engine Running.
Finance_Theme_Engine();
