<?php
/**
 * Template for displaying reviews
 * This is an example template - copy to your theme and customize
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get reviews
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$args = array(
    'post_type'      => 'review',
    'post_status'    => 'publish',
    'posts_per_page' => 10,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
);

// Filter by rating if specified
if (isset($_GET['rating']) && intval($_GET['rating']) > 0) {
    $args['meta_query'] = array(
        array(
            'key'     => '_sac_rating',
            'value'   => intval($_GET['rating']),
            'compare' => '=',
            'type'    => 'NUMERIC',
        ),
    );
}

$reviews_query = new WP_Query($args);

// Get statistics
$average_rating = SAC_Review_Handler::get_average_rating();
$total_reviews = SAC_Review_Handler::get_total_reviews();
$distribution = SAC_Review_Handler::get_rating_distribution();
?>

<div class="sac-reviews-container">

    <!-- Statistics Summary -->
    <div class="sac-reviews-summary">
        <div class="sac-summary-rating">
            <div class="sac-average-score"><?php echo esc_html(number_format($average_rating, 1)); ?></div>
            <div class="sac-average-stars">
                <?php
                $full_stars = floor($average_rating);
                $half_star = ($average_rating - $full_stars) >= 0.5;

                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $full_stars) {
                        echo '<span class="star filled">★</span>';
                    } elseif ($i == ($full_stars + 1) && $half_star) {
                        echo '<span class="star half">★</span>';
                    } else {
                        echo '<span class="star empty">☆</span>';
                    }
                }
                ?>
            </div>
            <div class="sac-total-reviews">
                <?php printf(_n('%s avis', '%s avis', $total_reviews, 'site-avis-clients'), number_format_i18n($total_reviews)); ?>
            </div>
        </div>

        <div class="sac-rating-distribution">
            <?php foreach ($distribution as $rating => $count) :
                $percentage = $total_reviews > 0 ? ($count / $total_reviews) * 100 : 0;
            ?>
                <div class="sac-distribution-row">
                    <a href="<?php echo esc_url(add_query_arg('rating', $rating)); ?>" class="sac-rating-filter">
                        <?php echo esc_html($rating); ?> <span class="star">★</span>
                    </a>
                    <div class="sac-distribution-bar">
                        <div class="sac-distribution-fill" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                    </div>
                    <span class="sac-distribution-count"><?php echo esc_html($count); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Filter -->
    <?php if (isset($_GET['rating'])) : ?>
        <div class="sac-active-filter">
            <?php printf(__('Filtré par : %d étoiles', 'site-avis-clients'), intval($_GET['rating'])); ?>
            <a href="<?php echo esc_url(remove_query_arg('rating')); ?>" class="sac-clear-filter">
                <?php _e('Supprimer le filtre', 'site-avis-clients'); ?>
            </a>
        </div>
    <?php endif; ?>

    <!-- Reviews List -->
    <?php if ($reviews_query->have_posts()) : ?>
        <div class="sac-reviews-list">
            <?php while ($reviews_query->have_posts()) : $reviews_query->the_post();
                $rating = get_post_meta(get_the_ID(), '_sac_rating', true);
                $author_name = get_post_meta(get_the_ID(), '_sac_author_name', true);
                $author_email = get_post_meta(get_the_ID(), '_sac_author_email', true);

                // Generate Gravatar
                $gravatar = get_avatar($author_email, 60);
            ?>
                <article class="sac-review-item">
                    <div class="sac-review-header">
                        <div class="sac-review-author">
                            <div class="sac-author-avatar">
                                <?php echo $gravatar; ?>
                            </div>
                            <div class="sac-author-info">
                                <h3 class="sac-author-name"><?php echo esc_html($author_name); ?></h3>
                                <time class="sac-review-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo esc_html(get_the_date()); ?>
                                </time>
                            </div>
                        </div>
                        <div class="sac-review-rating">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $rating ? '<span class="star filled">★</span>' : '<span class="star empty">☆</span>';
                            }
                            ?>
                        </div>
                    </div>

                    <div class="sac-review-content">
                        <h4 class="sac-review-title"><?php the_title(); ?></h4>
                        <div class="sac-review-text">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php
        $pagination = paginate_links(array(
            'total'     => $reviews_query->max_num_pages,
            'current'   => $paged,
            'prev_text' => __('&laquo; Précédent', 'site-avis-clients'),
            'next_text' => __('Suivant &raquo;', 'site-avis-clients'),
            'type'      => 'list',
        ));

        if ($pagination) {
            echo '<nav class="sac-pagination">' . $pagination . '</nav>';
        }
        ?>

    <?php else : ?>
        <div class="sac-no-reviews">
            <p><?php _e('Aucun avis pour le moment. Soyez le premier à laisser un avis !', 'site-avis-clients'); ?></p>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</div>

<style>
/* Basic styles for review display - customize in your theme */
.sac-reviews-container {
    max-width: 1000px;
    margin: 2rem auto;
}

.sac-reviews-summary {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 2rem;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.sac-summary-rating {
    text-align: center;
}

.sac-average-score {
    font-size: 3rem;
    font-weight: bold;
    color: #333;
}

.sac-average-stars {
    font-size: 1.5rem;
    color: #ffa500;
    margin: 0.5rem 0;
}

.sac-total-reviews {
    color: #666;
    font-size: 0.95rem;
}

.sac-distribution-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.sac-rating-filter {
    width: 60px;
    text-decoration: none;
    color: #333;
}

.sac-distribution-bar {
    flex: 1;
    height: 20px;
    background: #e0e0e0;
    border-radius: 3px;
    overflow: hidden;
}

.sac-distribution-fill {
    height: 100%;
    background: #ffa500;
}

.sac-distribution-count {
    width: 40px;
    text-align: right;
    color: #666;
}

.sac-review-item {
    padding: 1.5rem;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.sac-review-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.sac-review-author {
    display: flex;
    gap: 1rem;
}

.sac-author-avatar img {
    border-radius: 50%;
}

.sac-author-name {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.sac-review-date {
    color: #666;
    font-size: 0.85rem;
}

.sac-review-rating .star {
    color: #ffa500;
    font-size: 1.2rem;
}

.sac-review-title {
    font-size: 1.1rem;
    margin: 0 0 0.5rem;
}

.sac-review-text {
    color: #333;
    line-height: 1.6;
}

.sac-no-reviews {
    text-align: center;
    padding: 3rem;
    background: #f8f9fa;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .sac-reviews-summary {
        grid-template-columns: 1fr;
    }
}
</style>
