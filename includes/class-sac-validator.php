<?php
/**
 * Validator class for review data
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SAC_Validator {

    /**
     * Validate review submission data
     *
     * @param array $data
     * @return true|WP_Error
     */
    public static function validate_review_data($data) {
        // Validate title
        if (empty($data['review_title'])) {
            return new WP_Error('missing_title', __('Le titre est requis.', 'site-avis-clients'));
        }

        $title = sanitize_text_field($data['review_title']);
        if (strlen($title) < 3) {
            return new WP_Error('title_too_short', __('Le titre doit contenir au moins 3 caractères.', 'site-avis-clients'));
        }

        if (strlen($title) > 200) {
            return new WP_Error('title_too_long', __('Le titre ne doit pas dépasser 200 caractères.', 'site-avis-clients'));
        }

        // Validate content
        if (empty($data['review_content'])) {
            return new WP_Error('missing_content', __('Le contenu est requis.', 'site-avis-clients'));
        }

        $content = wp_kses_post($data['review_content']);
        if (strlen($content) < 10) {
            return new WP_Error('content_too_short', __('Le contenu doit contenir au moins 10 caractères.', 'site-avis-clients'));
        }

        if (strlen($content) > 5000) {
            return new WP_Error('content_too_long', __('Le contenu ne doit pas dépasser 5000 caractères.', 'site-avis-clients'));
        }

        // Validate rating
        if (empty($data['review_rating'])) {
            return new WP_Error('missing_rating', __('La note est requise.', 'site-avis-clients'));
        }

        $rating = intval($data['review_rating']);
        if ($rating < 1 || $rating > 5) {
            return new WP_Error('invalid_rating', __('La note doit être comprise entre 1 et 5.', 'site-avis-clients'));
        }

        // Validate author name
        if (empty($data['review_author'])) {
            return new WP_Error('missing_author', __('Le nom est requis.', 'site-avis-clients'));
        }

        $author = sanitize_text_field($data['review_author']);
        if (strlen($author) < 2) {
            return new WP_Error('author_too_short', __('Le nom doit contenir au moins 2 caractères.', 'site-avis-clients'));
        }

        if (strlen($author) > 100) {
            return new WP_Error('author_too_long', __('Le nom ne doit pas dépasser 100 caractères.', 'site-avis-clients'));
        }

        // Validate email
        if (empty($data['review_email'])) {
            return new WP_Error('missing_email', __('L\'email est requis.', 'site-avis-clients'));
        }

        $email = sanitize_email($data['review_email']);
        if (!is_email($email)) {
            return new WP_Error('invalid_email', __('L\'adresse email n\'est pas valide.', 'site-avis-clients'));
        }

        return true;
    }

    /**
     * Check for spam patterns
     *
     * @param string $content
     * @return bool
     */
    public static function is_spam($content) {
        // Check for excessive links
        $link_count = substr_count(strtolower($content), 'http');
        if ($link_count > 3) {
            return true;
        }

        // Check for excessive capitalization
        $uppercase_ratio = 0;
        $letters = preg_replace('/[^a-zA-Z]/', '', $content);
        if (strlen($letters) > 0) {
            $uppercase = preg_replace('/[^A-Z]/', '', $content);
            $uppercase_ratio = strlen($uppercase) / strlen($letters);
        }

        if ($uppercase_ratio > 0.5 && strlen($letters) > 10) {
            return true;
        }

        // Common spam keywords
        $spam_keywords = array('casino', 'viagra', 'cialis', 'forex', 'crypto', 'bitcoin');
        $content_lower = strtolower($content);
        foreach ($spam_keywords as $keyword) {
            if (strpos($content_lower, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check rate limiting
     *
     * @param string $ip
     * @param string $email
     * @return true|WP_Error
     */
    public static function check_rate_limit($ip, $email) {
        // Check IP-based rate limiting (max 3 reviews per hour)
        $recent_reviews = get_transient('sac_ip_' . md5($ip));
        if ($recent_reviews && $recent_reviews >= 3) {
            return new WP_Error(
                'rate_limit_ip',
                __('Vous avez soumis trop d\'avis récemment. Veuillez réessayer plus tard.', 'site-avis-clients')
            );
        }

        // Check email-based rate limiting (max 1 review per day)
        $email_check = get_transient('sac_email_' . md5($email));
        if ($email_check) {
            return new WP_Error(
                'rate_limit_email',
                __('Vous avez déjà soumis un avis récemment. Veuillez attendre avant d\'en soumettre un autre.', 'site-avis-clients')
            );
        }

        return true;
    }

    /**
     * Update rate limit counters
     *
     * @param string $ip
     * @param string $email
     */
    public static function update_rate_limit($ip, $email) {
        // Update IP counter
        $ip_key = 'sac_ip_' . md5($ip);
        $count = get_transient($ip_key);
        $count = $count ? intval($count) + 1 : 1;
        set_transient($ip_key, $count, HOUR_IN_SECONDS);

        // Update email counter
        $email_key = 'sac_email_' . md5($email);
        set_transient($email_key, 1, DAY_IN_SECONDS);
    }

    /**
     * Sanitize review data
     *
     * @param array $data
     * @return array
     */
    public static function sanitize_review_data($data) {
        return array(
            'title' => sanitize_text_field($data['review_title']),
            'content' => wp_kses_post($data['review_content']),
            'rating' => intval($data['review_rating']),
            'author' => sanitize_text_field($data['review_author']),
            'email' => sanitize_email($data['review_email']),
        );
    }
}
