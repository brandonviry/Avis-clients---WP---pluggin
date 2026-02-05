<?php
/**
 * Shortcodes class
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SAC_Shortcodes {

    /**
     * Initialize shortcodes
     */
    public static function init() {
        add_shortcode('avis_clients_list', array(__CLASS__, 'display_reviews_list'));
        add_shortcode('avis_clients_stats', array(__CLASS__, 'display_reviews_stats'));
    }

    /**
     * Display reviews list shortcode
     *
     * Usage: [avis_clients_list limit="5" rating="5"]
     */
    public static function display_reviews_list($atts) {
        $atts = shortcode_atts(array(
            'limit'   => 10,
            'rating'  => 0,
            'orderby' => 'date',
            'order'   => 'DESC',
        ), $atts);

        $args = array(
            'post_type'      => 'review',
            'post_status'    => 'publish',
            'posts_per_page' => intval($atts['limit']),
            'orderby'        => sanitize_text_field($atts['orderby']),
            'order'          => sanitize_text_field($atts['order']),
        );

        // Filter by rating if specified
        if (intval($atts['rating']) > 0) {
            $args['meta_query'] = array(
                array(
                    'key'     => '_sac_rating',
                    'value'   => intval($atts['rating']),
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ),
            );
        }

        $reviews = new WP_Query($args);

        ob_start();

        if ($reviews->have_posts()) {
            echo '<div class="sac-reviews-shortcode">';

            while ($reviews->have_posts()) {
                $reviews->the_post();
                $rating = get_post_meta(get_the_ID(), '_sac_rating', true);
                $author_name = get_post_meta(get_the_ID(), '_sac_author_name', true);
                ?>
                <div class="sac-review-card">
                    <div class="sac-review-header-short">
                        <div class="sac-review-stars">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $rating ? '★' : '☆';
                            }
                            ?>
                        </div>
                        <span class="sac-review-author-short"><?php echo esc_html($author_name); ?></span>
                    </div>
                    <h4 class="sac-review-title-short"><?php the_title(); ?></h4>
                    <div class="sac-review-excerpt">
                        <?php echo wp_trim_words(get_the_content(), 30); ?>
                    </div>
                    <time class="sac-review-date-short"><?php echo esc_html(get_the_date()); ?></time>
                </div>
                <?php
            }

            echo '</div>';
        } else {
            echo '<p class="sac-no-reviews-short">' . esc_html__('Aucun avis disponible.', 'site-avis-clients') . '</p>';
        }

        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Display review statistics shortcode
     *
     * Usage: [avis_clients_stats]
     */
    public static function display_reviews_stats($atts) {
        $atts = shortcode_atts(array(
            'show_distribution' => 'yes',
        ), $atts);

        $average = SAC_Review_Handler::get_average_rating();
        $total = SAC_Review_Handler::get_total_reviews();
        $distribution = SAC_Review_Handler::get_rating_distribution();

        ob_start();
        ?>
        <div class="sac-stats-widget">
            <div class="sac-stats-summary">
                <div class="sac-stats-average">
                    <span class="sac-stats-number"><?php echo esc_html(number_format($average, 1)); ?></span>
                    <div class="sac-stats-stars">
                        <?php
                        $full_stars = floor($average);
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $full_stars ? '<span class="star">★</span>' : '<span class="star empty">☆</span>';
                        }
                        ?>
                    </div>
                    <span class="sac-stats-total">
                        <?php printf(_n('%s avis', '%s avis', $total, 'site-avis-clients'), number_format_i18n($total)); ?>
                    </span>
                </div>
            </div>

            <?php if ($atts['show_distribution'] === 'yes' && $total > 0) : ?>
                <div class="sac-stats-distribution">
                    <?php foreach ($distribution as $rating => $count) :
                        $percentage = ($count / $total) * 100;
                    ?>
                        <div class="sac-stats-bar">
                            <span class="sac-stats-label"><?php echo esc_html($rating); ?>★</span>
                            <div class="sac-stats-bar-bg">
                                <div class="sac-stats-bar-fill" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                            </div>
                            <span class="sac-stats-count"><?php echo esc_html($count); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <style>
        .sac-stats-widget {
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }
        .sac-stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
        }
        .sac-stats-stars {
            font-size: 1.5rem;
            color: #ffa500;
            margin: 0.5rem 0;
        }
        .sac-stats-stars .star.empty {
            color: #ddd;
        }
        .sac-stats-total {
            display: block;
            color: #666;
            margin-top: 0.5rem;
        }
        .sac-stats-distribution {
            margin-top: 1.5rem;
        }
        .sac-stats-bar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .sac-stats-label {
            width: 40px;
            text-align: right;
            font-size: 0.9rem;
        }
        .sac-stats-bar-bg {
            flex: 1;
            height: 20px;
            background: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
        }
        .sac-stats-bar-fill {
            height: 100%;
            background: #ffa500;
        }
        .sac-stats-count {
            width: 30px;
            text-align: right;
            font-size: 0.9rem;
        }

        /* Review cards for list shortcode */
        .sac-reviews-shortcode {
            display: grid;
            gap: 1rem;
        }
        .sac-review-card {
            padding: 1rem;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
        }
        .sac-review-header-short {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .sac-review-stars {
            color: #ffa500;
            font-size: 1.1rem;
        }
        .sac-review-author-short {
            font-size: 0.9rem;
            color: #666;
        }
        .sac-review-title-short {
            margin: 0.5rem 0;
            font-size: 1rem;
        }
        .sac-review-excerpt {
            color: #333;
            line-height: 1.5;
            margin-bottom: 0.5rem;
        }
        .sac-review-date-short {
            font-size: 0.85rem;
            color: #999;
        }
        </style>
        <?php
        return ob_get_clean();
    }
}

// Initialize shortcodes
SAC_Shortcodes::init();
