<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.felixwelberg.de/
 * @since      1.0.0
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @author     Felix Welberg <felix@welberg.de>
 */
class Extended_Post_Status_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
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
        $post_types = get_post_types();
        $status = self::get_status();
        if (in_array($post->post_type, $post_types)) {
            foreach ($status as $single_status) {
                $term_meta = get_option("taxonomy_term_$single_status->term_id");
                $complete = '';
                $hidden = 0;
                if (array_key_exists('hide_in_drop_down', $term_meta) && $term_meta['hide_in_drop_down'] == 1) {
                    $hidden = 1;
                }
                if ($post->post_status == $single_status->slug) {
                    $complete = ' selected="selected"'; ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery(".misc-pub-section span#post-status-display").append('<span id="post-status-display"><?php echo $single_status->name; ?></span>');
                        });
                    </script>
                    <?php
                }
                if ($hidden == 0 || $post->post_status == $single_status->slug) {
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
        foreach ($status as $single_status) {
            $term_meta = get_option("taxonomy_term_$single_status->term_id");
            $hidden = 0;
            if (array_key_exists('hide_in_drop_down', $term_meta) && $term_meta['hide_in_drop_down'] == 1) {
                $hidden = 1;
            }
            if ($hidden == 0) {
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        jQuery('select[name="_status"]').append('<option value="<?php echo $single_status->slug; ?>"><?php echo $single_status->name; ?></option>');
                    });
                </script>
                <?php
            }
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
        foreach ($status as $single_status) {
            $term_meta = get_option("taxonomy_term_$single_status->term_id");
            $hidden = 0;
            if (array_key_exists('hide_in_drop_down', $term_meta) && $term_meta['hide_in_drop_down'] == 1) {
                $hidden = 1;
            } ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('#bulk-edit select[name="_status"]').append('<option value="<?php echo $single_status->slug; ?>" class="hidden-<?php echo $hidden; ?>"><?php echo $single_status->name; ?></option>');
                    jQuery('.quick-edit-row select[name="_status"]').append('<option value="<?php echo $single_status->slug; ?>" class="hidden-<?php echo $hidden; ?>"><?php echo $single_status->name; ?></option>');
                });
            </script>
        <?php
        } ?>
        <script type="text/javascript">
            jQuery('#the-list').bind('DOMSubtreeModified', postListUpdated);

            function postListUpdated() {
                // Wait for the quick-edit dom to change
                setTimeout(function () {
                    var post_quickedit_tr_id = jQuery('.inline-editor').attr('id');
                    if (post_quickedit_tr_id) {
                        var post_edit_tr = post_quickedit_tr_id.replace("edit", "post");
                        jQuery('.quick-edit-row select[name="_status"] option').each(function () {
                            jQuery(this).show();
                            if (jQuery(this).hasClass('hidden-1') && !jQuery('#' + post_edit_tr).hasClass('status-' + jQuery(this).val())) {
                                jQuery(this).hide();
                            }
                        });
                    }
                    jQuery('#bulk-edit select[name="_status"] option').each(function () {
                        jQuery(this).show();
                        if (jQuery(this).hasClass('hidden-1')) {
                            jQuery(this).hide();
                        }
                    });
                }, 100);
            }
        </script>
        <?php
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
        if ($post) {
            foreach ($status as $single_status) {
                if ($single_status->slug == $post->post_status) {
                    return [$single_status->name];
                }
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
        foreach ($status as $single_status) {
            $term_meta = get_option("taxonomy_term_$single_status->term_id");
            $args = [
                'label' => $single_status->name,
                'label_count' => _n_noop($single_status->name . ' <span class="count">(%s)</span>', $single_status->name . ' <span class="count">(%s)</span>'),
            ];
            if ((array_key_exists('public', $term_meta) && $term_meta['public'] == 1) || current_user_can('edit_posts')) {
                $args['public'] = true;
            } else {
                $args['public'] = false;
            }
            if (array_key_exists('show_in_admin_all_list', $term_meta) && $term_meta['show_in_admin_all_list'] == 1) {
                $args['show_in_admin_all_list'] = true;
            } else {
                $args['show_in_admin_all_list'] = false;
            }
            if (array_key_exists('show_in_admin_status_list', $term_meta) && $term_meta['show_in_admin_status_list'] == 1) {
                $args['show_in_admin_status_list'] = true;
            } else {
                $args['show_in_admin_status_list'] = false;
            }
            if (array_key_exists('hide_in_drop_down', $term_meta) && $term_meta['hide_in_drop_down'] == 1) {
                $args['hide_in_drop_down'] = true;
            } else {
                $args['hide_in_drop_down'] = false;
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
            'show_in_menu' => false,
            'meta_box_cb' => false,
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
        $term_meta = false;
        if (is_object($tag)) {
            $t_id = $tag->term_id;
            $term_meta = get_option("taxonomy_term_$t_id");
        }
        $fields = [
            'public' => ['label' => __('Public', 'extended-post-status'), 'desc' => __('Posts/Pages with this status are public.', 'extended-post-status')],
            'show_in_admin_all_list' => ['label' => __('Show posts in admin "All" list', 'extended-post-status'), 'desc' => __('Posts/Pages with this status will be listed in all posts/pages overview.', 'extended-post-status')],
            'show_in_admin_status_list' => ['label' => __('Show status in admin status list', 'extended-post-status'), 'desc' => __('Status appears in status list.', 'extended-post-status')],
            'hide_in_drop_down' => ['label' => __('Hide status in admin drop downs', 'extended-post-status'), 'desc' => __('Status is not selectable in the admin dropdowns.', 'extended-post-status')],
        ];
        foreach ($fields as $key => $value) {
            $checked = '';
            if ($term_meta && $term_meta[$key] == 1) {
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
        $fields = ['public', 'show_in_admin_all_list', 'show_in_admin_status_list', 'hide_in_drop_down'];
        $is_inline_edit = filter_input(INPUT_POST, '_inline_edit');

        /* Reset all custom checkbox fields */
        if (!$is_inline_edit) {
            foreach ($fields as $field) {
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
     * @param type $data
     * @param type $term_id
     * @param type $taxonomy
     * @param type $args
     * @return type
     * @since    1.0.2
     */
    public function override_status_taxonomy_on_save($data, $term_id, $taxonomy, $args)
    {
        if ($taxonomy == 'status') {
            $slug = $data['slug'];

            /* Cut slug if it is longer than 20 chars */
            if (strlen($slug) > 20) {
                $data['slug'] = substr($slug, 0, 20);
            }
        }
        return $data;
    }

    /**
     * Returns all status
     *
     * @return type
     * @since    1.0.0
     */
    public static function get_status()
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
                $content .= __('Show in admin "All" list', 'extended-post-status') . ', ';
            }
            if (array_key_exists('show_in_admin_status_list', $term_meta) && $term_meta['show_in_admin_status_list'] == 1) {
                $content .= __('Show in admin status list', 'extended-post-status') . ', ';
            }
            if (array_key_exists('hide_in_drop_down', $term_meta) && $term_meta['hide_in_drop_down'] == 1) {
                $content .= __('Hide in admin drop downs', 'extended-post-status') . ', ';
            }
            $content = rtrim($content, ', ');
        }
        if ('count_posts' == $column_name) {
            $count = wp_count_posts('post');
            $slug = $term->slug;
            $count_posts = 0;
            if (property_exists($count, $slug)) {
                $count_posts = $count->$slug;
            }
            $content .= '<a href="edit.php?post_status=' . $slug . '&post_type=post" target="_self">' . $count_posts . '</a>';
        }
        if ('count_pages' == $column_name) {
            $count = wp_count_posts('page');
            $slug = $term->slug;
            $count_pages = 0;
            if (property_exists($count, $slug)) {
                $count_pages = $count->$slug;
            }
            $content .= '<a href="edit.php?post_status=' . $slug . '&post_type=page" target="_self">' . $count_pages . '</a>';
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
        $is_block_editor = get_current_screen()->is_block_editor();
        if ($is_block_editor) {
            add_meta_box('extended_post_status', __('Status', 'extended-post-status'), ['Extended_Post_Status_Admin', 'status_meta_box_content'], null, 'side', 'high');
        }
    }

    /**
     * Add meta box content
     *
     * @global type $post
     * @since    1.0.0
     */
    public static function status_meta_box_content()
    {
        global $post;
        $returner = '';
        $statuses = self::get_all_status_array();
        $returner .= '<select name="post_status_">';
        $returner .= '<option value="none">' . __('- Select status -', 'extended-post-status') . '</option>';
        foreach ($statuses as $key => $value) {
            $term = get_term_by('slug', $key, 'status');
            if ($term) {
                $term_meta = get_option("taxonomy_term_$term->term_id");
            }
            $hidden = 0;
            if ($term && array_key_exists('hide_in_drop_down', $term_meta) && $term_meta['hide_in_drop_down'] == 1) {
                $hidden = 1;
            }
            if ($key == $post->post_status) {
                $returner .= '<option value="' . $key . '" selected="selected">' . $value . '</option>';
            } else {
                if ($hidden == 0) {
                    $returner .= '<option value="' . $key . '">' . $value . '</option>';
                }
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
    public static function get_all_status_array()
    {
        $statuses = [];
        $core_statuses = get_post_statuses();
        $statuses = $core_statuses;
        $custom_statuses = self::get_status();
        foreach ($custom_statuses as $status) {
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
    public static function override_admin_post_list($query)
    {
        $statuses = self::get_status();
        /* Check if query has no further params */
        if ((array_key_exists('post_status', $query->query) && empty($query->query['post_status']))) {
            $statuses_show_in_admin_all_list = self::get_all_post_statuses();
            foreach ($statuses as $status) {
                $term_meta = get_option("taxonomy_term_$status->term_id");
                if (!in_array($status->slug, $statuses_show_in_admin_all_list)) {
                    if ($term_meta['show_in_admin_all_list'] == 1) {
                        $statuses_show_in_admin_all_list[] = $status->slug;
                    }
                } else {
                    if ($term_meta['show_in_admin_all_list'] != 1) {
                        if (($key = array_search($status->slug, $statuses_show_in_admin_all_list)) !== false) {
                            unset($statuses_show_in_admin_all_list[$key]);
                        }
                    }
                }
            }

            set_query_var('post_status', array_values($statuses_show_in_admin_all_list));
            return;
        }
        return;
    }

    /**
     * Get all available post statuses
     *
     * @return array
     * @since    1.0.17
     */
    private static function get_all_post_statuses()
    {
        global $wpdb;
        $query = $wpdb->get_results("SELECT DISTINCT $wpdb->posts.post_status as post_status FROM $wpdb->posts WHERE post_status NOT IN ('auto-draft', 'trash', 'inherit')");
        return wp_list_pluck($query, 'post_status');
    }

    /**
     * Init additional settings page params
     *
     * @since    1.0.4
     */
    public function settings_init()
    {
        register_setting(
            'writing',
            'extended-post-status-add-extra-admin-menu-item',
            ['Extended_Post_Status_Admin', 'settings_sanitize']
        );
        add_settings_section(
            'extended-post-status-settings',
            __('Extended Post Status', 'extended-post-status'),
            ['Extended_Post_Status_Admin', 'settings_section_description'],
            'writing'
        );
        add_settings_field(
            'extended-post-status-add-extra-admin-menu-item',
            '<label for="extended-post-status-add-extra-admin-menu-item">' . __('Move status to main admin menu.', 'extended-post-status') . '</label>',
            ['Extended_Post_Status_Admin', 'settings_extra_admin_menu_item_field'],
            'writing',
            'extended-post-status-settings'
        );
    }

    /**
     * Sanitize setting page input
     *
     * @param type $input
     * @return type
     * @since    1.0.4
     */
    public static function settings_sanitize($input)
    {
        return isset($input) ? true : false;
    }

    /**
     * Add description to settings page section
     *
     * @since    1.0.4
     */
    public static function settings_section_description()
    {
        echo __('Settings for post status handling.', 'extended-post-status');
    }

    /**
     * Add settings section fields
     *
     * @since    1.0.4
     */
    public static function settings_extra_admin_menu_item_field()
    {
        $returner = '
            <input id="extended-post-status-add-extra-admin-menu-item" type="checkbox" value="1" name="extended-post-status-add-extra-admin-menu-item"' . checked(get_option('extended-post-status-add-extra-admin-menu-item', false), true, false) . '>
        ';
        echo $returner;
    }

    /**
     * Add admin menu items
     *
     * @since    1.0.4
     */
    public function admin_menu()
    {
        if (get_option('extended-post-status-add-extra-admin-menu-item', false)) {
            add_menu_page(__('Extended Post Status', 'extended-post-status'), __('Extended Post Status', 'extended-post-status'), 'publish_posts', 'extended-post-status-taxonomy', ['Extended_Post_Status_Admin', 'admin_menu_link_extended_post_status_taxonomy'], 'dashicons-post-status');
        } else {
            add_submenu_page('options-general.php', __('Extended Post Status', 'extended-post-status'), __('Extended Post Status', 'extended-post-status'), 'publish_posts', 'extended-post-status-taxonomy', ['Extended_Post_Status_Admin', 'admin_menu_link_extended_post_status_taxonomy']);
        }
    }

    /**
     * Redirects in admin context
     * - Redirect the main admin menu status page to original taxonomy page
     *
     * @global type $pagenow
     * @since    1.0.4
     */
    public function admin_redirects()
    {
        global $pagenow;
        if (($pagenow == 'admin.php' || $pagenow == 'options-general.php') && filter_input(INPUT_GET, 'page') == 'extended-post-status-taxonomy') {
            wp_redirect(admin_url('edit-tags.php?taxonomy=status'), 301);
            exit;
        }
    }

    /**
     * Parent file settings
     * - Used to fake the status page in main admin menu
     *
     * @param string $parent_file
     * @return string
     * @since    1.0.4
     */
    public function parent_file($parent_file)
    {
        if (get_current_screen()->taxonomy == 'status') {
            if (get_option('extended-post-status-add-extra-admin-menu-item', false)) {
                $parent_file = 'extended-post-status-taxonomy';
            } else {
                $parent_file = 'options-general.php';
            }
        }
        return $parent_file;
    }

    /**
     * Submenu file settings
     * - Used to fake the status page in admin submenu
     *
     * @param string $submenu_file
     * @return string
     * @since    1.0.8
     */
    public function submenu_file($submenu_file)
    {
        if (get_current_screen()->taxonomy == 'status') {
            $submenu_file = 'extended-post-status-taxonomy';
        }
        return $submenu_file;
    }

    /**
     * Override the core status field with the custom status field
     * - If the post is getting trashed, don't do this!
     * - If the post is a planned post for the future, don't do this!
     * - If no custom status is set (equals 'none'), set post status to draft
     *
     * @param type $data
     * @param type $postarr
     * @return type
     * @since    1.0.13
     */
    public function wp_insert_post_data($data, $postarr)
    {
        if (array_key_exists('post_status_', $postarr) && $data['post_status'] != 'trash' && $data['post_status'] != 'future') {
            $data['post_status'] = $postarr['post_status_'];
        }
        if ($data['post_status'] == 'none') {
            $data['post_status'] = 'draft';
        }
        return $data;
    }

    /**
     * Override the text on the Gutenberg publish button
     * - This is done to prevent confusion while publishing or saving a post
     *
     * @since    1.0.18
     */
    public function change_publish_button_gutenberg()
    {
        if (wp_script_is('wp-i18n')) {
            ?>
            <script type="text/javascript">
                wp.i18n.setLocaleData({'Publish': ['<?php echo __('Save'); ?>']});
            </script>
            <?php
        }
    }

    /**
     * Remove the "two click" publishing sidebar
     * - See: https://github.com/WordPress/gutenberg/issues/9077#issuecomment-458309231
     *
     * @since    1.0.18
     */
    public function remove_publishing_sidebar_gutenberg()
    {
        wp_enqueue_script('disablePublishSidebar', plugin_dir_url(__DIR__) . 'admin/js/disablePublishSidebar.js', ['jquery']);
    }

    /**
     * Override gettext snippets
     *
     * @param type $translated
     * @param type $original
     * @param type $domain
     * @return type
     * @since    1.0.18
     */
    public function gettext_override($translated, $original, $domain)
    {
        if ($original == 'Post published.') {
            $translated = __('Post saved.');
        }
        return $translated;
    }
}
