<?php
/**
 * @link              http://www.felixwelberg.de/
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Extended Post Status
 * Description:       Add new post status types.
 * Version:           1.0.19
 * Author:            Felix Welberg
 * Author URI:        http://www.felixwelberg.de/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       extended-post-status
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

__('Extended Post Status', 'extended-post-status');
__('Add new post status types.', 'extended-post-status');

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('EXTENDED_POST_STATUS_VERSION', '1.0.19');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-extended-post-status-activator.php
 */
function activate_extended_post_status()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-extended-post-status-activator.php';
    Extended_Post_Status_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-extended-post-status-deactivator.php
 */
function deactivate_extended_post_status()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-extended-post-status-deactivator.php';
    Extended_Post_Status_Deactivator::deactivate();
}
register_activation_hook(__FILE__, 'activate_extended_post_status');
register_deactivation_hook(__FILE__, 'deactivate_extended_post_status');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-extended-post-status.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_extended_post_status()
{
    $plugin = new Extended_Post_Status();
    $plugin->run();
}
run_extended_post_status();
