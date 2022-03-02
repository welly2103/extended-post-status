<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.felixwelberg.de/
 * @since      1.0.0
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
 * @author     Felix Welberg <felix@welberg.de>
 */
class Extended_Post_Status
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @var      Extended_Post_Status_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

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
        if (defined('EXTENDED_POST_STATUS_VERSION')) {
            $this->version = EXTENDED_POST_STATUS_VERSION;
        } else {
            $this->version = '1.0.19';
        }
        $this->plugin_name = 'extended-post-status';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Extended_Post_Status_Loader. Orchestrates the hooks of the plugin.
     * - Extended_Post_Status_i18n. Defines internationalization functionality.
     * - Extended_Post_Status_Admin. Defines all hooks for the admin area.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     */
    private function load_dependencies()
    {
        require_once plugin_dir_path(__DIR__) . 'includes/class-extended-post-status-loader.php';
        require_once plugin_dir_path(__DIR__) . 'includes/class-extended-post-status-i18n.php';
        require_once plugin_dir_path(__DIR__) . 'admin/class-extended-post-status-admin.php';

        $this->loader = new Extended_Post_Status_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Extended_Post_Status_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     */
    private function set_locale()
    {
        $plugin_i18n = new Extended_Post_Status_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Extended_Post_Status_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('init', $plugin_admin, 'register_status_taxonomy');
        $this->loader->add_action('init', $plugin_admin, 'register_post_status');
        $this->loader->add_action('admin_init', $plugin_admin, 'override_admin_post_list_init');
        $this->loader->add_action('admin_init', $plugin_admin, 'settings_init');
        $this->loader->add_action('admin_init', $plugin_admin, 'admin_redirects');
        $this->loader->add_action('admin_menu', $plugin_admin, 'admin_menu');
        $this->loader->add_action('admin_footer-post.php', $plugin_admin, 'append_post_status_list');
        $this->loader->add_action('admin_footer-post-new.php', $plugin_admin, 'append_post_status_list');
        $this->loader->add_action('admin_footer-edit.php', $plugin_admin, 'append_post_status_list_quickedit');
        $this->loader->add_action('admin_print_footer_scripts', $plugin_admin, 'change_publish_button_gutenberg');
        $this->loader->add_action('display_post_states', $plugin_admin, 'append_post_status_post_overview');
        $this->loader->add_action('status_add_form_fields', $plugin_admin, 'status_taxonomy_custom_fields', 10, 2);
        $this->loader->add_action('created_status', $plugin_admin, 'save_status_taxonomy_custom_fields', 10, 2);
        $this->loader->add_action('status_edit_form_fields', $plugin_admin, 'status_taxonomy_custom_fields', 10, 2);
        $this->loader->add_action('edited_status', $plugin_admin, 'save_status_taxonomy_custom_fields', 10, 2);
        $this->loader->add_action('manage_edit-status_columns', $plugin_admin, 'edit_status_taxonomy_columns');
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_status_meta_box');
        $this->loader->add_action('enqueue_block_editor_assets', $plugin_admin, 'remove_publishing_sidebar_gutenberg');

        $this->loader->add_filter('parent_file', $plugin_admin, 'parent_file');
        $this->loader->add_filter('submenu_file', $plugin_admin, 'submenu_file');
        $this->loader->add_filter('wp_update_term_data', $plugin_admin, 'override_status_taxonomy_on_save', 10, 4);
        $this->loader->add_filter('manage_status_custom_column', $plugin_admin, 'add_status_taxonomy_columns_content', 10, 3);
        $this->loader->add_filter('wp_insert_post_data', $plugin_admin, 'wp_insert_post_data', 99, 2);
        $this->loader->add_filter('gettext', $plugin_admin, 'gettext_override', 10, 3);
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
     * @return    Extended_Post_Status_Loader    Orchestrates the hooks of the plugin.
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
}
