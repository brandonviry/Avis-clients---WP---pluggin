<?php
/**
 * Review submission handler
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SAC_Review_Handler {

    /**
     * Process review submission
     *
     * @param array $data
     * @return int|WP_Error Post ID on success, WP_Error on failure
     */
    public function process_submission($data) {
        // Validate data
        $validation = SAC_Validator::validate_review_data($data);
        if (is_wp_error($validation)) {
            return $validation;
        }

        // Sanitize data
        $clean_data = SAC_Validator::sanitize_review_data($data);

        // Check for spam
        if (SAC_Validator::is_spam($clean_data['content'])) {
            return new WP_Error(
                'spam_detected',
                __('Votre avis a été identifié comme spam.', 'site-avis-clients')
            );
        }

        // Get user IP
        $ip = $this->get_user_ip();

        // Check rate limiting
        $rate_check = SAC_Validator::check_rate_limit($ip, $clean_data['email']);
        if (is_wp_error($rate_check)) {
            return $rate_check;
        }

        // Create the review post
        $post_data = array(
            'post_title'   => $clean_data['title'],
            'post_content' => $clean_data['content'],
            'post_type'    => 'review',
            'post_status'  => 'pending', // Require moderation by default
            'post_author'  => 0, // No specific author for front-end submissions
        );

        // Insert the post
        $post_id = wp_insert_post($post_data, true);

        if (is_wp_error($post_id)) {
            return new WP_Error(
                'insert_failed',
                __('Impossible de créer l\'avis. Veuillez réessayer.', 'site-avis-clients')
            );
        }

        // Save meta data
        update_post_meta($post_id, '_sac_rating', $clean_data['rating']);
        update_post_meta($post_id, '_sac_author_name', $clean_data['author']);
        update_post_meta($post_id, '_sac_author_email', $clean_data['email']);
        update_post_meta($post_id, '_sac_author_ip', $ip);
        update_post_meta($post_id, '_sac_submission_date', current_time('mysql'));

        // Update rate limits
        SAC_Validator::update_rate_limit($ip, $clean_data['email']);

        // Send notification email to admin
        $this->send_admin_notification($post_id, $clean_data);

        // Fire action hook for extensions
        do_action('sac_review_submitted', $post_id, $clean_data);

        return $post_id;
    }

    /**
     * Get user IP address
     *
     * @return string
     */
    private function get_user_ip() {
        $ip = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // Validate IP
        $ip = filter_var($ip, FILTER_VALIDATE_IP);

        return $ip ? $ip : '0.0.0.0';
    }

    /**
     * Send notification email to admin
     *
     * @param int $post_id
     * @param array $data
     */
    private function send_admin_notification($post_id, $data) {
        $admin_email = get_option('admin_email');

        $subject = sprintf(
            __('[%s] Nouvel avis en attente de modération', 'site-avis-clients'),
            get_bloginfo('name')
        );

        $rating_stars = str_repeat('★', $data['rating']) . str_repeat('☆', 5 - $data['rating']);

        $message = sprintf(
            __("Un nouvel avis a été soumis sur votre site et est en attente de modération.\n\n" .
               "Titre: %s\n" .
               "Note: %s (%d/5)\n" .
               "Auteur: %s\n" .
               "Email: %s\n\n" .
               "Contenu:\n%s\n\n" .
               "Pour modérer cet avis, rendez-vous sur:\n%s", 'site-avis-clients'),
            $data['title'],
            $rating_stars,
            $data['rating'],
            $data['author'],
            $data['email'],
            $data['content'],
            admin_url('post.php?post=' . $post_id . '&action=edit')
        );

        wp_mail($admin_email, $subject, $message);
    }

    /**
     * Get reviews by rating
     *
     * @param int $rating
     * @param int $limit
     * @return WP_Query
     */
    public static function get_reviews_by_rating($rating = 0, $limit = 10) {
        $args = array(
            'post_type'      => 'review',
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        if ($rating > 0) {
            $args['meta_query'] = array(
                array(
                    'key'     => '_sac_rating',
                    'value'   => $rating,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ),
            );
        }

        return new WP_Query($args);
    }

    /**
     * Get average rating
     *
     * @return float
     */
    public static function get_average_rating() {
        global $wpdb;

        $average = $wpdb->get_var(
            "SELECT AVG(CAST(meta_value AS DECIMAL(10,2)))
             FROM {$wpdb->postmeta}
             INNER JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
             WHERE {$wpdb->postmeta}.meta_key = '_sac_rating'
             AND {$wpdb->posts}.post_type = 'review'
             AND {$wpdb->posts}.post_status = 'publish'"
        );

        return $average ? round(floatval($average), 1) : 0;
    }

    /**
     * Get total reviews count
     *
     * @return int
     */
    public static function get_total_reviews() {
        $count = wp_count_posts('review');
        return isset($count->publish) ? intval($count->publish) : 0;
    }

    /**
     * Get rating distribution
     *
     * @return array
     */
    public static function get_rating_distribution() {
        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT meta_value as rating, COUNT(*) as count
             FROM {$wpdb->postmeta}
             INNER JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
             WHERE {$wpdb->postmeta}.meta_key = '_sac_rating'
             AND {$wpdb->posts}.post_type = 'review'
             AND {$wpdb->posts}.post_status = 'publish'
             GROUP BY meta_value
             ORDER BY meta_value DESC",
            ARRAY_A
        );

        $distribution = array();
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = 0;
        }

        foreach ($results as $result) {
            $rating = intval($result['rating']);
            if ($rating >= 1 && $rating <= 5) {
                $distribution[$rating] = intval($result['count']);
            }
        }

        return $distribution;
    }
}
