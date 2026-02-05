<?php
/**
 * Review form template
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="sac-review-form-wrapper" id="sac-review-form">
    <h3 class="sac-form-title"><?php echo esc_html($atts['title']); ?></h3>

    <form id="sac-review-submission-form" class="sac-form" method="post">
        <div class="sac-form-messages"></div>

        <div class="sac-form-group">
            <label for="sac-review-author" class="sac-label">
                <?php _e('Votre nom', 'site-avis-clients'); ?> <span class="required">*</span>
            </label>
            <input
                type="text"
                id="sac-review-author"
                name="review_author"
                class="sac-input"
                required
                maxlength="100"
                placeholder="<?php esc_attr_e('Entrez votre nom', 'site-avis-clients'); ?>"
            />
        </div>

        <div class="sac-form-group">
            <label for="sac-review-email" class="sac-label">
                <?php _e('Votre email', 'site-avis-clients'); ?> <span class="required">*</span>
            </label>
            <input
                type="email"
                id="sac-review-email"
                name="review_email"
                class="sac-input"
                required
                placeholder="<?php esc_attr_e('votre@email.com', 'site-avis-clients'); ?>"
            />
            <small class="sac-help-text">
                <?php _e('Votre email ne sera pas publié', 'site-avis-clients'); ?>
            </small>
        </div>

        <div class="sac-form-group">
            <label for="sac-review-title" class="sac-label">
                <?php _e('Titre de votre avis', 'site-avis-clients'); ?> <span class="required">*</span>
            </label>
            <input
                type="text"
                id="sac-review-title"
                name="review_title"
                class="sac-input"
                required
                maxlength="200"
                placeholder="<?php esc_attr_e('Résumez votre expérience en quelques mots', 'site-avis-clients'); ?>"
            />
        </div>

        <div class="sac-form-group">
            <label class="sac-label">
                <?php _e('Votre note', 'site-avis-clients'); ?> <span class="required">*</span>
            </label>
            <div class="sac-rating-input" id="sac-rating-input">
                <input type="hidden" id="sac-review-rating" name="review_rating" value="0" required />
                <div class="sac-stars">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <span class="sac-star" data-rating="<?php echo esc_attr($i); ?>">☆</span>
                    <?php endfor; ?>
                </div>
                <span class="sac-rating-text"></span>
            </div>
        </div>

        <div class="sac-form-group">
            <label for="sac-review-content" class="sac-label">
                <?php _e('Votre avis', 'site-avis-clients'); ?> <span class="required">*</span>
            </label>
            <textarea
                id="sac-review-content"
                name="review_content"
                class="sac-textarea"
                rows="6"
                required
                maxlength="5000"
                placeholder="<?php esc_attr_e('Partagez votre expérience avec nous...', 'site-avis-clients'); ?>"
            ></textarea>
            <small class="sac-help-text">
                <?php _e('Minimum 10 caractères', 'site-avis-clients'); ?>
            </small>
        </div>

        <div class="sac-form-group">
            <button type="submit" class="sac-submit-button">
                <span class="sac-button-text"><?php _e('Envoyer mon avis', 'site-avis-clients'); ?></span>
                <span class="sac-button-loader" style="display: none;">
                    <?php _e('Envoi en cours...', 'site-avis-clients'); ?>
                </span>
            </button>
        </div>

        <p class="sac-privacy-notice">
            <?php _e('Vos données personnelles seront utilisées uniquement pour la gestion de votre avis et ne seront pas partagées avec des tiers.', 'site-avis-clients'); ?>
        </p>
    </form>
</div>
