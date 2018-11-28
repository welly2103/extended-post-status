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
        $status = self::get_status();
        if ($post->post_type == 'post' || $post->post_type == 'page') {
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
        foreach ($status AS $single_status) {

            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('select[name="_status"]').append('<option value="<?php echo $single_status->slug; ?>"><?php echo $single_status->name; ?></option>');
                });
            </script>
            <?php
        }
    }

    /**
     * Add the custom post type to backend post quickedit status dropdown
     * 
     * @since    1.0.0
     */
    public function append_post_status_list_quickedit()
    {
        $status = self::get_status();
        foreach ($status AS $single_status) {

            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('select[name="_status"]').append('<option value="<?php echo $single_status->slug; ?>" <?php echo $complete; ?>><?php echo $single_status->name; ?></option>');
                });
            </script>
            <?php
        }
    }

    /**
     * Add status to post list
     * 
     * @global type $post
     * @param type $statuses
     * @return type
     * @since    1.0.0
     */
    public function append_post_status_post_overview($statuses)
    {
        global $post;
        $status = self::get_status();
        foreach ($status AS $single_status) {
            if ($single_status->slug == $post->post_status) {
                return [$single_status->name];
            }
        }
        return $statuses;
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
            $args = [
                'label' => $single_status->name,
                'label_count' => _n_noop($single_status->name . ' <span class="count">(%s)</span>', $single_status->name . ' <span class="count">(%s)</span>'),
            ];
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
        $labels = [
            'name' => _x('Status', 'taxonomy general name', 'extended-post-status'),
            'singular_name' => _x('Status', 'taxonomy singular name', 'extended-post-status'),
            'menu_name' => __('Statuses', 'extended-post-status'),
            'all_items' => __('All statuses', 'extended-post-status'),
            'edit_item' => __('Edit status', 'extended-post-status'),
            'view_item' => __('View status', 'extended-post-status'),
            'update_item' => __('Update status', 'extended-post-status'),
            'add_new_item' => __('Add new status', 'extended-post-status'),
            'parent_item' => __('Parent status', 'extended-post-status'),
            'parent_item_colon' => __('Parent status:', 'extended-post-status'),
            'new_item_name' => __('New status name', 'extended-post-status'),
            'search_items' => __('Search statuses', 'extended-post-status'),
            'popular_items' => __('Popular statuses', 'extended-post-status'),
            'separate_items_with_commas' => __('Separate status with commas', 'extended-post-status'),
            'add_or_remove_items' => __('Add or remove status', 'extended-post-status'),
            'choose_from_most_used' => __('Choose from most used statuses', 'extended-post-status'),
            'not_found' => __('No statuses found', 'extended-post-status'),
            'back_to_items' => __('← Back to status', 'extended-post-status'),
        ];
        $args = [
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
        ];
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
        $fields = [
            'public' => ['label' => __('Public', 'extended-post-status'), 'desc' => __('Posts/Pages with this status are public.', 'extended-post-status')],
            'show_in_admin_all_list' => ['label' => __('Show posts in admin all list', 'extended-post-status'), 'desc' => __('Posts/Pages with this status will listet in all posts/pages overview.', 'extended-post-status')],
            'show_in_admin_status_list' => ['label' => __('Show status in admin status list', 'extended-post-status'), 'desc' => __('Status appears in status list.', 'extended-post-status')],
        ];
        foreach ($fields AS $key => $value) {
            $checked = '';
            if ($term_meta[$key] == 1) {
                $checked = 'checked="checked"';
            }
            $returner .= '
                <tr class="form-field">
                    <th scope="row" valign="top">
                        <label for="term_meta[' . $key . ']"><input type="checkbox" name="term_meta[' . $key . ']" id="term_meta[' . $key . ']" value="1" ' . $checked . ' /> ' . $value['label'] . '</label>
                    </th>
                    <td>
                        <label for="term_meta[' . $key . ']"><p>' . $value['desc'] . '</p></label><br />
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
        $fields = ['public', 'show_in_admin_all_list', 'show_in_admin_status_list'];
        $is_inline_edit = filter_input(INPUT_POST, '_inline_edit');

        /* Reset all custom checkbox fields */
        if (!$is_inline_edit) {
            foreach ($fields AS $field) {
                $term_meta[$field] = 0;
            }
            update_option("taxonomy_term_$term_id", $term_meta);
        }

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
     * Override core field after the update of a status taxonomy
     * Used to check if the slug is longer than 20 chars, because the database
     * field for statuses is limited to 20 chars
     * 
     * 
     * @param type $term_id
     * @param type $taxonomy
     * @since    1.0.2
     */
    public function override_status_taxonomy_core_fields($term_id, $taxonomy)
    {
        if ($taxonomy == 'status') {
            $term = get_term($term_id, $taxonomy);
            $slug = $term->slug;

            /* Cut slug if it is longer than 20 chars */
            if (strlen($slug) > 20) {
                wp_update_term($term_id, $taxonomy, ['slug' => substr($slug, 0, 20)]);
            }
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
        $args = [
            'taxonomy' => 'status',
            'hide_empty' => false,
        ];
        return get_terms($args);
    }

    /**
     * Edit the status taxonomy table
     * 
     * @param type $columns
     * @return type
     * @since    1.0.0
     */
    public function edit_status_taxonomy_columns($columns)
    {
        if (isset($columns['posts'])) {
            unset($columns['posts']);
        }
        $columns['settings'] = __('Settings', 'extended-post-status');
        $columns['count_posts'] = __('Posts', 'extended-post-status');
        $columns['count_pages'] = __('Pages', 'extended-post-status');
        return $columns;
    }

    /**
     * Add content to new created custom column in taxonomy table
     * 
     * @param type $content
     * @param type $column_name
     * @param type $term_id
     * @return string
     * @since    1.0.0
     */
    public function add_status_taxonomy_columns_content($content, $column_name, $term_id)
    {
        $content = '';
        $term = get_term($term_id);
        $term_meta = get_option("taxonomy_term_$term_id");
        if ('settings' == $column_name) {
            if (array_key_exists('public', $term_meta) && $term_meta['public'] == 1) {
                $content .= __('Public', 'extended-post-status') . ', ';
            }
            if (array_key_exists('show_in_admin_all_list', $term_meta) && $term_meta['show_in_admin_all_list'] == 1) {
                $content .= __('Show in admin all list', 'extended-post-status') . ', ';
            }
            if (array_key_exists('show_in_admin_status_list', $term_meta) && $term_meta['show_in_admin_status_list'] == 1) {
                $content .= __('Show in admin status list', 'extended-post-status') . ', ';
            }
            $content = rtrim($content, ', ');
        }
        if ('count_posts' == $column_name) {
            $count = wp_count_posts('post');
            $slug = $term->slug;
            $content .= '<a href="edit.php?post_status=' . $slug . '&post_type=post" target="_self">' . $count->$slug . '</a>';
        }
        if ('count_pages' == $column_name) {
            $count = wp_count_posts('page');
            $slug = $term->slug;
            $content .= '<a href="edit.php?post_status=' . $slug . '&post_type=page" target="_self">' . $count->$slug . '</a>';
        }
        return $content;
    }

    /**
     * Add status meta box to gutenberg editor
     * 
     * @since    1.0.0
     */
    public function add_status_meta_box()
    {
        $screens = ['post', 'page'];
        if (function_exists('register_block_type')) {
            foreach ($screens as $screen) {
                add_meta_box('extended_post_status', __('Status', 'extended-post-status'), ['Extended_Post_Status_Admin', 'status_meta_box_content'], $screen, 'side', 'high');
            }
        }
    }

    /**
     * Add meta box content
     * 
     * @global type $post
     * @since    1.0.0
     */
    public function status_meta_box_content()
    {
        global $post;
        $returner = '';
        $statuses = self::get_all_status_array();
        $returner .= '<select name="post_status">';
        foreach ($statuses AS $key => $value) {
            if ($key == $post->post_status) {
                $returner .= '<option value="' . $key . '" selected="selected">' . $value . '</option>';
            } else {
                $returner .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        $returner .= '</select>';
        echo $returner;
    }

    /**
     * Get array of all statuses
     * 
     * @return type
     * @since    1.0.0
     */
    public function get_all_status_array()
    {
        $statuses = [];
        $core_statuses = get_post_statuses();
        $statuses = $core_statuses;
        $custom_statuses = self::get_status();
        foreach ($custom_statuses AS $status) {
            $statuses[$status->slug] = $status->name;
        }
        return $statuses;
    }

    /**
     * Initialize the view for the overridden query
     * 
     * @global type $pagenow
     * @since    1.0.1
     */
    public function override_admin_post_list_init()
    {
        global $pagenow;
        if ('edit.php' == $pagenow) {
            add_action('parse_query', ['Extended_Post_Status_Admin', 'override_admin_post_list']);
        }
    }

    /**
     * Override the post query
     * 
     * @param type $query
     * @return type
     * @since    1.0.1
     */
    public function override_admin_post_list($query)
    {
        $statuses = self::get_status();
        /* Check if query has no further params */
        if ($query->query == ['post_type' => 'post', 'posts_per_page' => $query->query['posts_per_page']] || $query->query == ['post_type' => 'page', 'posts_per_page' => $query->query['posts_per_page']]) {
            $statuses_show_in_admin_all_list = [];
            $statuses_show_in_admin_all_list[] = 'publish';
            $statuses_show_in_admin_all_list[] = 'draft';
            $statuses_show_in_admin_all_list[] = 'pending';
            foreach ($statuses AS $status) {
                $term_meta = get_option("taxonomy_term_$status->term_id");
                if ($term_meta['show_in_admin_all_list'] == 1) {
                    $statuses_show_in_admin_all_list[] = $status->slug;
                }
            }
            set_query_var('post_status', $statuses_show_in_admin_all_list);
            return;
        }
        return;
    }
}
