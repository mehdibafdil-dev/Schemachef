<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/mehdibafdil-dev
 * @since      1.0.0
 *
 * @package    Schemachef
 * @subpackage Schemachef/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Schemachef
 * @subpackage Schemachef/includes
 * @author     Mehdi BAFDIL <mehdibafdil@gmail.com>
 */
class Schemachef
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Schemachef_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	protected $title;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('SCHEMACHEF_VERSION')) {
			$this->version = SCHEMACHEF_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'schemachef';

		$this->title = 'SchemaChef';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Schemachef_Loader. Orchestrates the hooks of the plugin.
	 * - Schemachef_i18n. Defines internationalization functionality.
	 * - Schemachef_Admin. Defines all hooks for the admin area.
	 * - Schemachef_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-schemachef-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-schemachef-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-schemachef-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-schemachef-public.php';

		$this->loader = new Schemachef_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Schemachef_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Schemachef_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Schemachef_Admin($this->get_plugin_name(), $this->get_version(), $this->get_title());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('save_post', $plugin_admin, 'save_recipe_schema_data');
		$this->loader->add_action('the_content', $plugin_admin, 'add_recipe_schema_to_content');
		$this->loader->add_filter('add_meta_boxes', $plugin_admin, 'add_recipe_schema_metabox');
		$this->loader->add_filter('admin_menu', $plugin_admin, 'register_admin_menu');
		$this->loader->add_action('admin_post_save_recipe_options', $plugin_admin, 'handle_recipe_options_form_submission');
		$this->loader->add_action('admin_post_save_recipe_general_settings', $plugin_admin, 'handle_recipe_options_form_submission');

		$this->loader->add_action('wp_ajax_delete_recipe_action', $plugin_admin, 'delete_recipe_callback');
		$this->loader->add_action('wp_ajax_nopriv_delete_recipe_action', $plugin_admin, 'delete_recipe_callback');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Schemachef_Public($this->get_plugin_name(), $this->get_version());
		$plugin_admin = new Schemachef_Admin($this->get_plugin_name(), $this->get_version(), $this->get_title());
		$current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';

		if ($current_page === 'schemachef-admin') {
			$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
			$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_bootstrap');
			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_custom_js');
		}
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Schemachef_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	public function get_title()
	{
		return $this->title;
	}
}
