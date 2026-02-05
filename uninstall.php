<?php
/**
 * Uninstall script
 * Fired when the plugin is uninstalled
 */

// Exit if accessed directly or not via WordPress uninstall
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Option to keep or delete data on uninstall
$delete_data = get_option('sac_delete_data_on_uninstall', false);

if ($delete_data) {
    // Delete all reviews
    $reviews = get_posts(array(
        'post_type'      => 'review',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    ));

    foreach ($reviews as $review) {
        // Delete all post meta
        $post_metas = get_post_meta($review->ID);
        foreach ($post_metas as $key => $value) {
            delete_post_meta($review->ID, $key);
        }

        // Delete the post
        wp_delete_post($review->ID, true);
    }

    // Delete plugin options
    delete_option('sac_settings');
    delete_option('sac_version');
    delete_option('sac_delete_data_on_uninstall');

    // Clear transients
    global $wpdb;

    $wpdb->query(
        "DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '_transient_sac_%'
         OR option_name LIKE '_transient_timeout_sac_%'"
    );

    // Flush rewrite rules
    flush_rewrite_rules();
}
