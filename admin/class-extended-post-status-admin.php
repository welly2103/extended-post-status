<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.felixwelberg.de/
 * @since      1.0.0
 *
 * @package    Extended_Post_Status
 * @subpackage Extended_Post_Status/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Extended_Post_Status
 * @subpackage Extended_Post_Status/admin
 * @author     Felix Welberg <felix@welberg.de>
 */
class Extended_Post_Status_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Add the custom post type to backend post status dropdown
     * The trac ticket is still open and there are no new changes until now, so
     * this is just a workaround :(
     * https://core.trac.wordpress.org/ticket/12706
     * 
     * @global type $post
     * @since    1.0.0
     */
    public function append_post_status_list()
    {
        global $post;
        $complete = '';
        $label = '';
        $status = self::get_status();
        if ($post->post_type == 'post') {
            foreach ($status AS $single_status) {
                if ($post->post_status == $single_status->slug) {
                    $complete = ' selected="selected"';

                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery(".misc-pub-section span#post-status-display").append('<span id="post-status-display"><?php echo $single_status->name; ?></span>');
                        });
                    </script>
                    <?php
                }

                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        jQuery('select#post_status').append('<option value="<?php echo $single_status->slug; ?>" <?php echo $complete; ?>><?php echo $single_status->name; ?></option>');
                    });
                </script>
                <?php
            }
        }
    }

    /**
     * Add custom post type
     * 
     * @since    1.0.0
     */
    public function register_post_status()
    {
        $status = self::get_status();
        foreach ($status AS $single_status) {
            $term_meta = get_option("taxonomy_term_$single_status->term_id");
            $args = array(
                'label' => $single_status->name,
                'label_count' => _n_noop($single_status->name . ' <span class="count">(%s)</span>', $single_status->name . ' <span class="count">(%s)</span>'),
            );
            if ($term_meta['public'] == 1) {
                $args['public'] = true;
            } else {
                $args['public'] = false;
            }
            if ($term_meta['show_in_admin_all_list'] == 1) {
                $args['show_in_admin_all_list'] = true;
            } else {
                $args['show_in_admin_all_list'] = false;
            }
            if ($term_meta['show_in_admin_status_list'] == 1) {
                $args['show_in_admin_status_list'] = true;
            } else {
                $args['show_in_admin_status_list'] = false;
            }
            register_post_status($single_status->slug, $args);
        }
    }

    /**
     * Add custom taxonomy
     * 
     * @since    1.0.0
     */
    public function register_status_taxonomy()
    {
        $labels = array(
            'name' => _x('Status', 'taxonomy general name', 'extended-post-status'),
            'singular_name' => _x('Status', 'taxonomy singular name', 'extended-post-status'),
            'menu_name' => __('Status', 'extended-post-status'),
            'all_items' => __('All status', 'extended-post-status'),
            'edit_item' => __('Edit status', 'extended-post-status'),
            'view_item' => __('View status', 'extended-post-status'),
            'update_item' => __('Update status', 'extended-post-status'),
            'add_new_item' => __('Add new status', 'extended-post-status'),
            'parent_item' => __('Parent status', 'extended-post-status'),
            'parent_item_colon' => __('Parent status:', 'extended-post-status'),
            'new_item_name' => __('New Status Name', 'extended-post-status'),
            'search_items' => __('Search status', 'extended-post-status'),
            'popular_items' => __('Popular status', 'extended-post-status'),
            'separate_items_with_commas' => __('Separate status with commas', 'extended-post-status'),
            'add_or_remove_items' => __('Add or remove status', 'extended-post-status'),
            'choose_from_most_used' => __('Choose from most used status', 'extended-post-status'),
            'not_found' => __('No status found', 'extended-post-status'),
            'back_to_items' => __('← Back to status', 'extended-post-status'),
        );
        $args = array(
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
        );
        register_taxonomy('status', 'post', $args);
    }

    /**
     * Manipulate the taxonomy form fields
     * 
     * @param type $tag
     * @since    1.0.0
     */
    public function status_taxonomy_custom_fields($tag)
    {
        $returner = '';
        $t_id = $tag->term_id;
        $term_meta = get_option("taxonomy_term_$t_id");
        $fields = array(
            'public' => array('label' => __('Public', 'extended-post-status'), 'desc' => __('Posts with this status are public', 'extended-post-status')),
            'show_in_admin_all_list' => array('label' => __('Show in admin all list', 'extended-post-status'), 'desc' => __('Status appears in admin all list', 'extended-post-status')),
            'show_in_admin_status_list' => array('label' => __('Show in admin status list', 'extended-post-status'), 'desc' => __('Status appears in status list', 'extended-post-status')),
        );
        foreach ($fields AS $key => $value) {
            $checked = '';
            if ($term_meta[$key] == 1) {
                $checked = 'checked="checked"';
            }
            $returner .= '
                <tr class="form-field">
                    <th scope="row" valign="top">
                        <label for="term_meta[' . $key . ']">' . $value['label'] . '</label>
                    </th>
                    <td>
                        <label for="term_meta[' . $key . ']"><input type="checkbox" name="term_meta[' . $key . ']" id="term_meta[' . $key . ']" value="1" ' . $checked . ' /> ' . $value['desc'] . '</label><br />
                    </td>
                </tr>
            ';
        }
        echo $returner;
    }

    /**
     * Save the manipulated taxonomy form fields
     * 
     * @param type $term_id
     * @since    1.0.0
     */
    public function save_status_taxonomy_custom_fields($term_id)
    {
        $fields = array('public', 'show_in_admin_all_list', 'show_in_admin_status_list');

        /* Reset all fields */
        foreach ($fields AS $field) {
            $term_meta[$field] = 0;
        }
        update_option("taxonomy_term_$term_id", $term_meta);

        /* Update new values */
        if (isset($_POST['term_meta'])) {
            $term_meta = get_option("taxonomy_term_$term_id");
            $cat_keys = array_keys($_POST['term_meta']);
            foreach ($cat_keys as $key) {
                if (isset($_POST['term_meta'][$key])) {
                    $term_meta[$key] = $_POST['term_meta'][$key];
                }
            }
            update_option("taxonomy_term_$term_id", $term_meta);
        }
    }

    /**
     * Returns all status
     * 
     * @return type
     * @since    1.0.0
     */
    public function get_status()
    {
        $args = array(
            'taxonomy' => 'status',
            'hide_empty' => false,
        );
        return get_terms($args);
    }
}
