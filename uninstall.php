<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://www.felixwelberg.de/
 * @since      1.0.0
 */
// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Reset all posts with a custom status to status draft, so the posts don't
 * get lost and still appear in the backend.
 *
 * @since    1.0.16
 */
// reregister taxonomy (plugin is already deactivated, so the taxonomy is no
// longer available!)
register_taxonomy('status', 'post');
$args = [
    'taxonomy' => 'status',
    'hide_empty' => false,
];
$custom_status = get_terms($args); //

if (!empty($custom_status)) {
    global $wpdb;

    $status_list = [];
    foreach ($custom_status as $status) {
        $status_list[] = esc_sql($status->name);
    }
    $results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'posts WHERE post_status IN("' . implode('","', $status_list) . '")');
    if (count($results) > 0) {
        foreach ($results as $result) {
            wp_update_post([
                'ID' => $result->ID,
                'post_status' => 'draft'
            ]);
        }
    }
}
