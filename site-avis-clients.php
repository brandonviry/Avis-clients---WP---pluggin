<?php
/**
 * Plugin Name: Site Avis Clients
 * Plugin URI: https://github.com/brandonviry/Avis-clients---WP---pluggin
 * Description: Gestion des avis clients avec Custom Post Type, notation et formulaire front-end
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: VIRY Brandon
 * Author URI: https://devweb.viry-brandon.fr
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: site-avis-clients
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/brandonviry/Avis-clients---WP---pluggin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SAC_VERSION', '1.0.0');
define('SAC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SAC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SAC_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class Site_Avis_Clients {

    /**
     * Instance of this class
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_review', array($this, 'save_review_meta'), 10, 2);

        // Front-end hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_front_scripts'));
        add_shortcode('avis_clients_form', array($this, 'render_review_form'));
        add_action('wp_ajax_sac_submit_review', array($this, 'handle_review_submission'));
        add_action('wp_ajax_nopriv_sac_submit_review', array($this, 'handle_review_submission'));

        // Admin hooks
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_filter('manage_review_posts_columns', array($this, 'custom_review_columns'));
        add_action('manage_review_posts_custom_column', array($this, 'custom_review_column_content'), 10, 2);
    }

    /**
     * Load dependencies
     */
    private function load_dependencies() {
        require_once SAC_PLUGIN_DIR . 'includes/class-sac-review-handler.php';
        require_once SAC_PLUGIN_DIR . 'includes/class-sac-validator.php';
        require_once SAC_PLUGIN_DIR . 'includes/class-sac-shortcodes.php';
    }

    /**
     * Activation hook
     */
    public function activate() {
        $this->register_post_type();
        flush_rewrite_rules();

        // Set default options
        if (!get_option('sac_settings')) {
            update_option('sac_settings', array(
                'require_moderation' => true,
                'allow_anonymous' => false,
                'min_rating' => 1,
                'max_rating' => 5
            ));
        }
    }

    /**
     * Deactivation hook
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Load text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('site-avis-clients', false, dirname(SAC_PLUGIN_BASENAME) . '/languages');
    }

    /**
     * Register Custom Post Type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Avis', 'Post Type General Name', 'site-avis-clients'),
            'singular_name'         => _x('Avis', 'Post Type Singular Name', 'site-avis-clients'),
            'menu_name'             => __('Avis Clients', 'site-avis-clients'),
            'name_admin_bar'        => __('Avis', 'site-avis-clients'),
            'archives'              => __('Archives des avis', 'site-avis-clients'),
            'attributes'            => __('Attributs', 'site-avis-clients'),
            'parent_item_colon'     => __('Avis parent:', 'site-avis-clients'),
            'all_items'             => __('Tous les avis', 'site-avis-clients'),
            'add_new_item'          => __('Ajouter un nouvel avis', 'site-avis-clients'),
            'add_new'               => __('Ajouter', 'site-avis-clients'),
            'new_item'              => __('Nouvel avis', 'site-avis-clients'),
            'edit_item'             => __('Modifier l\'avis', 'site-avis-clients'),
            'update_item'           => __('Mettre à jour', 'site-avis-clients'),
            'view_item'             => __('Voir l\'avis', 'site-avis-clients'),
            'view_items'            => __('Voir les avis', 'site-avis-clients'),
            'search_items'          => __('Rechercher un avis', 'site-avis-clients'),
            'not_found'             => __('Aucun avis trouvé', 'site-avis-clients'),
            'not_found_in_trash'    => __('Aucun avis dans la corbeille', 'site-avis-clients'),
        );

        $args = array(
            'label'                 => __('Avis', 'site-avis-clients'),
            'description'           => __('Avis clients', 'site-avis-clients'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'author'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-star-filled',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rewrite'               => array('slug' => 'avis'),
        );

        register_post_type('review', $args);
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'sac_review_rating',
            __('Note', 'site-avis-clients'),
            array($this, 'render_rating_meta_box'),
            'review',
            'side',
            'high'
        );

        add_meta_box(
            'sac_review_info',
            __('Informations', 'site-avis-clients'),
            array($this, 'render_info_meta_box'),
            'review',
            'side',
            'default'
        );
    }

    /**
     * Render rating meta box
     */
    public function render_rating_meta_box($post) {
        wp_nonce_field('sac_save_review_rating', 'sac_review_rating_nonce');

        $rating = get_post_meta($post->ID, '_sac_rating', true);
        $rating = $rating ? intval($rating) : 0;
        ?>
        <p>
            <label for="sac_rating"><?php _e('Note (1-5):', 'site-avis-clients'); ?></label>
            <select name="sac_rating" id="sac_rating" style="width: 100%;">
                <option value=""><?php _e('Sélectionner', 'site-avis-clients'); ?></option>
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <option value="<?php echo esc_attr($i); ?>" <?php selected($rating, $i); ?>>
                        <?php echo esc_html(str_repeat('★', $i)); ?>
                    </option>
                <?php endfor; ?>
            </select>
        </p>
        <?php
    }

    /**
     * Render info meta box
     */
    public function render_info_meta_box($post) {
        $email = get_post_meta($post->ID, '_sac_author_email', true);
        $ip = get_post_meta($post->ID, '_sac_author_ip', true);
        $date = get_the_date('d/m/Y H:i', $post);
        ?>
        <p>
            <strong><?php _e('Email:', 'site-avis-clients'); ?></strong><br>
            <?php echo esc_html($email ? $email : __('Non renseigné', 'site-avis-clients')); ?>
        </p>
        <p>
            <strong><?php _e('Adresse IP:', 'site-avis-clients'); ?></strong><br>
            <?php echo esc_html($ip ? $ip : __('Non enregistrée', 'site-avis-clients')); ?>
        </p>
        <p>
            <strong><?php _e('Date de soumission:', 'site-avis-clients'); ?></strong><br>
            <?php echo esc_html($date); ?>
        </p>
        <?php
    }

    /**
     * Save review meta
     */
    public function save_review_meta($post_id, $post) {
        // Verify nonce
        if (!isset($_POST['sac_review_rating_nonce']) ||
            !wp_verify_nonce($_POST['sac_review_rating_nonce'], 'sac_save_review_rating')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save rating
        if (isset($_POST['sac_rating'])) {
            $rating = intval($_POST['sac_rating']);
            if ($rating >= 1 && $rating <= 5) {
                update_post_meta($post_id, '_sac_rating', $rating);
            }
        }
    }

    /**
     * Enqueue front-end scripts
     */
    public function enqueue_front_scripts() {
        if (is_singular() || is_page()) {
            wp_enqueue_style(
                'sac-front-style',
                SAC_PLUGIN_URL . 'assets/css/front.css',
                array(),
                SAC_VERSION
            );

            wp_enqueue_script(
                'sac-front-script',
                SAC_PLUGIN_URL . 'assets/js/front.js',
                array('jquery'),
                SAC_VERSION,
                true
            );

            wp_localize_script('sac-front-script', 'sacData', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('sac_submit_review'),
                'strings' => array(
                    'error' => __('Une erreur est survenue', 'site-avis-clients'),
                    'success' => __('Merci ! Votre avis a été soumis avec succès.', 'site-avis-clients'),
                    'required' => __('Ce champ est requis', 'site-avis-clients'),
                )
            ));
        }
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ('post.php' === $hook || 'post-new.php' === $hook) {
            global $post_type;
            if ('review' === $post_type) {
                wp_enqueue_style(
                    'sac-admin-style',
                    SAC_PLUGIN_URL . 'assets/css/admin.css',
                    array(),
                    SAC_VERSION
                );
            }
        }
    }

    /**
     * Custom columns for review list
     */
    public function custom_review_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['rating'] = __('Note', 'site-avis-clients');
        $new_columns['author'] = $columns['author'];
        $new_columns['date'] = $columns['date'];

        return $new_columns;
    }

    /**
     * Custom column content
     */
    public function custom_review_column_content($column, $post_id) {
        if ('rating' === $column) {
            $rating = get_post_meta($post_id, '_sac_rating', true);
            if ($rating) {
                echo '<span style="color: #ffa500; font-size: 16px;">';
                echo esc_html(str_repeat('★', intval($rating)));
                echo str_repeat('☆', 5 - intval($rating));
                echo '</span>';
            } else {
                echo '—';
            }
        }
    }

    /**
     * Render review form shortcode
     */
    public function render_review_form($atts) {
        $atts = shortcode_atts(array(
            'title' => __('Laisser un avis', 'site-avis-clients'),
        ), $atts);

        ob_start();
        include SAC_PLUGIN_DIR . 'templates/review-form.php';
        return ob_get_clean();
    }

    /**
     * Handle review submission
     */
    public function handle_review_submission() {
        check_ajax_referer('sac_submit_review', 'nonce');

        $handler = new SAC_Review_Handler();
        $result = $handler->process_submission($_POST);

        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message()
            ));
        } else {
            wp_send_json_success(array(
                'message' => __('Merci ! Votre avis a été soumis avec succès.', 'site-avis-clients'),
                'review_id' => $result
            ));
        }
    }
}

// Initialize plugin
function sac_init() {
    return Site_Avis_Clients::get_instance();
}

add_action('plugins_loaded', 'sac_init');
