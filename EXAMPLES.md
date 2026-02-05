# Exemples de code - Site Avis Clients

Ce fichier contient des exemples pratiques pour les développeurs souhaitant étendre ou personnaliser le plugin.

**Auteur :** VIRY Brandon - [devweb.viry-brandon.fr](https://devweb.viry-brandon.fr)
**GitHub :** [github.com/brandonviry/Avis-clients---WP---pluggin](https://github.com/brandonviry/Avis-clients---WP---pluggin)

## 📋 Table des matières

1. [Hooks et filtres](#hooks-et-filtres)
2. [Affichage personnalisé](#affichage-personnalisé)
3. [Récupération des données](#récupération-des-données)
4. [Widgets personnalisés](#widgets-personnalisés)
5. [Intégration WooCommerce](#intégration-woocommerce)
6. [API REST](#api-rest)

---

## Hooks et filtres

### Action après soumission d'un avis

```php
/**
 * Envoyer un email personnalisé au client après soumission
 */
add_action('sac_review_submitted', function($post_id, $data) {
    $to = $data['email'];
    $subject = 'Merci pour votre avis !';
    $message = sprintf(
        "Bonjour %s,\n\nMerci d'avoir pris le temps de nous laisser un avis.\n\nVotre avis sera examiné par notre équipe et publié prochainement.",
        $data['author']
    );

    wp_mail($to, $subject, $message);
}, 10, 2);
```

### Modifier les critères de spam

```php
/**
 * Ajouter vos propres mots-clés spam
 */
add_filter('sac_spam_keywords', function($keywords) {
    $custom_keywords = array('mot1', 'mot2', 'mot3');
    return array_merge($keywords, $custom_keywords);
});
```

### Modifier les limites de rate limiting

```php
/**
 * Augmenter la limite d'avis par jour
 */
add_filter('sac_email_rate_limit_duration', function($duration) {
    return DAY_IN_SECONDS * 7; // 1 avis par semaine au lieu d'un par jour
});

add_filter('sac_ip_rate_limit_duration', function($duration) {
    return HOUR_IN_SECONDS * 6; // 6 heures au lieu d'1 heure
});

add_filter('sac_ip_rate_limit_count', function($count) {
    return 5; // 5 avis au lieu de 3
});
```

### Modifier le statut par défaut des avis

```php
/**
 * Publier automatiquement les avis (désactiver la modération)
 */
add_filter('sac_review_default_status', function($status) {
    return 'publish'; // 'pending' par défaut
});
```

### Personnaliser l'email de notification admin

```php
/**
 * Personnaliser le contenu de l'email admin
 */
add_filter('sac_admin_notification_message', function($message, $post_id, $data) {
    $custom_message = sprintf(
        "Nouvel avis reçu !\n\n" .
        "De : %s (%s)\n" .
        "Note : %d/5\n\n" .
        "Titre : %s\n\n" .
        "Contenu :\n%s\n\n" .
        "Lien : %s",
        $data['author'],
        $data['email'],
        $data['rating'],
        $data['title'],
        $data['content'],
        admin_url('post.php?post=' . $post_id . '&action=edit')
    );

    return $custom_message;
}, 10, 3);
```

---

## Affichage personnalisé

### Créer un template personnalisé pour les avis

```php
<?php
/**
 * Template : single-review.php
 * Placez ce fichier dans votre thème
 */

get_header();

while (have_posts()) : the_post();
    $rating = get_post_meta(get_the_ID(), '_sac_rating', true);
    $author_name = get_post_meta(get_the_ID(), '_sac_author_name', true);
    $author_email = get_post_meta(get_the_ID(), '_sac_author_email', true);
    ?>

    <article class="review-single">
        <header class="review-header">
            <h1><?php the_title(); ?></h1>

            <div class="review-meta">
                <div class="review-rating">
                    <?php echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating); ?>
                    <span class="rating-number"><?php echo $rating; ?>/5</span>
                </div>

                <div class="review-author">
                    <?php echo get_avatar($author_email, 48); ?>
                    <span><?php echo esc_html($author_name); ?></span>
                </div>

                <time datetime="<?php echo get_the_date('c'); ?>">
                    <?php echo get_the_date(); ?>
                </time>
            </div>
        </header>

        <div class="review-content">
            <?php the_content(); ?>
        </div>
    </article>

    <?php
endwhile;

get_footer();
?>
```

### Widget sidebar avec les derniers avis

```php
<?php
/**
 * Ajouter dans functions.php
 */
function display_latest_reviews_widget() {
    $reviews = SAC_Review_Handler::get_reviews_by_rating(0, 3);

    if (!$reviews->have_posts()) {
        return;
    }
    ?>
    <div class="widget widget-latest-reviews">
        <h3 class="widget-title">Derniers avis</h3>
        <ul class="reviews-list">
            <?php while ($reviews->have_posts()) : $reviews->the_post();
                $rating = get_post_meta(get_the_ID(), '_sac_rating', true);
                ?>
                <li class="review-item">
                    <div class="review-stars">
                        <?php echo str_repeat('★', $rating); ?>
                    </div>
                    <h4 class="review-title">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h4>
                    <div class="review-excerpt">
                        <?php echo wp_trim_words(get_the_content(), 15); ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
        <?php wp_reset_postdata(); ?>
    </div>
    <?php
}

// Utiliser dans votre sidebar
add_action('sidebar', 'display_latest_reviews_widget');
?>
```

---

## Récupération des données

### Obtenir tous les avis d'une note spécifique

```php
// Récupérer tous les avis 5 étoiles
$five_star_reviews = SAC_Review_Handler::get_reviews_by_rating(5, -1);

if ($five_star_reviews->have_posts()) {
    while ($five_star_reviews->have_posts()) {
        $five_star_reviews->the_post();
        // Afficher l'avis
    }
    wp_reset_postdata();
}
```

### Obtenir les statistiques

```php
// Note moyenne
$average = SAC_Review_Handler::get_average_rating();
echo "Note moyenne : " . number_format($average, 1) . "/5";

// Nombre total d'avis
$total = SAC_Review_Handler::get_total_reviews();
echo "Total : " . $total . " avis";

// Distribution des notes
$distribution = SAC_Review_Handler::get_rating_distribution();
foreach ($distribution as $rating => $count) {
    echo $rating . " étoiles : " . $count . " avis<br>";
}
```

### Requête personnalisée

```php
// Récupérer les avis d'un auteur spécifique par email
$args = array(
    'post_type' => 'review',
    'post_status' => 'publish',
    'meta_query' => array(
        array(
            'key' => '_sac_author_email',
            'value' => 'user@example.com',
            'compare' => '='
        )
    )
);

$user_reviews = new WP_Query($args);
```

### Récupérer les avis récents (dernière semaine)

```php
$args = array(
    'post_type' => 'review',
    'post_status' => 'publish',
    'date_query' => array(
        array(
            'after' => '1 week ago'
        )
    ),
    'posts_per_page' => -1
);

$recent_reviews = new WP_Query($args);
echo "Avis cette semaine : " . $recent_reviews->found_posts;
```

---

## Widgets personnalisés

### Widget WordPress classique

```php
<?php
/**
 * Widget pour afficher les statistiques
 * Ajouter dans functions.php
 */
class SAC_Stats_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'sac_stats_widget',
            'Statistiques Avis Clients',
            array('description' => 'Affiche les statistiques des avis')
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . esc_html($instance['title']) . $args['after_title'];
        }

        $average = SAC_Review_Handler::get_average_rating();
        $total = SAC_Review_Handler::get_total_reviews();

        echo '<div class="sac-widget-stats">';
        echo '<div class="stat-average">' . number_format($average, 1) . '/5</div>';
        echo '<div class="stat-total">' . $total . ' avis</div>';
        echo '</div>';

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Nos avis';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Titre :</label>
            <input class="widefat"
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ?
            sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

// Enregistrer le widget
add_action('widgets_init', function() {
    register_widget('SAC_Stats_Widget');
});
?>
```

---

## Intégration WooCommerce

### Lier les avis aux produits

```php
/**
 * Ajouter un champ produit dans le formulaire
 */
add_action('sac_review_form_fields', function() {
    if (function_exists('WC')) {
        $products = wc_get_products(array('limit' => -1));
        ?>
        <div class="sac-form-group">
            <label for="sac-product">Produit concerné (optionnel)</label>
            <select name="review_product_id" id="sac-product">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($products as $product) : ?>
                    <option value="<?php echo $product->get_id(); ?>">
                        <?php echo esc_html($product->get_name()); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php
    }
});

/**
 * Sauvegarder l'ID du produit
 */
add_action('sac_review_submitted', function($post_id, $data) {
    if (!empty($_POST['review_product_id'])) {
        update_post_meta($post_id, '_sac_product_id', intval($_POST['review_product_id']));
    }
}, 10, 2);
```

### Afficher les avis sur la page produit

```php
/**
 * Afficher les avis liés au produit
 */
add_action('woocommerce_after_single_product_summary', function() {
    global $product;

    $args = array(
        'post_type' => 'review',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_sac_product_id',
                'value' => $product->get_id(),
                'compare' => '='
            )
        )
    );

    $product_reviews = new WP_Query($args);

    if ($product_reviews->have_posts()) {
        echo '<div class="product-reviews-section">';
        echo '<h2>Avis clients</h2>';

        while ($product_reviews->have_posts()) {
            $product_reviews->the_post();
            // Afficher l'avis
        }

        wp_reset_postdata();
        echo '</div>';
    }
}, 15);
```

---

## API REST

### Créer un endpoint REST personnalisé

```php
/**
 * Ajouter un endpoint pour récupérer les avis via API
 */
add_action('rest_api_init', function() {
    register_rest_route('sac/v1', '/reviews', array(
        'methods' => 'GET',
        'callback' => 'sac_get_reviews_api',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('sac/v1', '/stats', array(
        'methods' => 'GET',
        'callback' => 'sac_get_stats_api',
        'permission_callback' => '__return_true'
    ));
});

function sac_get_reviews_api($request) {
    $rating = $request->get_param('rating');
    $limit = $request->get_param('limit') ?: 10;

    $reviews = SAC_Review_Handler::get_reviews_by_rating($rating ?: 0, $limit);
    $data = array();

    if ($reviews->have_posts()) {
        while ($reviews->have_posts()) {
            $reviews->the_post();
            $data[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'content' => get_the_content(),
                'rating' => get_post_meta(get_the_ID(), '_sac_rating', true),
                'author' => get_post_meta(get_the_ID(), '_sac_author_name', true),
                'date' => get_the_date('c')
            );
        }
        wp_reset_postdata();
    }

    return rest_ensure_response($data);
}

function sac_get_stats_api() {
    $data = array(
        'average' => SAC_Review_Handler::get_average_rating(),
        'total' => SAC_Review_Handler::get_total_reviews(),
        'distribution' => SAC_Review_Handler::get_rating_distribution()
    );

    return rest_ensure_response($data);
}

// Utilisation :
// GET /wp-json/sac/v1/reviews
// GET /wp-json/sac/v1/reviews?rating=5&limit=5
// GET /wp-json/sac/v1/stats
```

---

## Autres exemples utiles

### Exporter les avis en CSV

```php
function sac_export_reviews_csv() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $reviews = get_posts(array(
        'post_type' => 'review',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=avis-clients-export.csv');

    $output = fopen('php://output', 'w');

    // En-têtes
    fputcsv($output, array('ID', 'Titre', 'Note', 'Auteur', 'Email', 'Date', 'Contenu'));

    foreach ($reviews as $review) {
        fputcsv($output, array(
            $review->ID,
            $review->post_title,
            get_post_meta($review->ID, '_sac_rating', true),
            get_post_meta($review->ID, '_sac_author_name', true),
            get_post_meta($review->ID, '_sac_author_email', true),
            $review->post_date,
            strip_tags($review->post_content)
        ));
    }

    fclose($output);
    exit;
}

// Ajouter un bouton dans l'admin
add_action('admin_menu', function() {
    add_submenu_page(
        'edit.php?post_type=review',
        'Exporter',
        'Exporter CSV',
        'manage_options',
        'sac-export',
        'sac_export_reviews_csv'
    );
});
```

### Badge "Client vérifié"

```php
/**
 * Afficher un badge pour les clients vérifiés (WooCommerce)
 */
add_filter('sac_review_author_name', function($name, $post_id) {
    $email = get_post_meta($post_id, '_sac_author_email', true);

    if (function_exists('wc_customer_bought_product') && wc_customer_bought_product($email, 0, 0)) {
        return $name . ' <span class="verified-badge">✓ Client vérifié</span>';
    }

    return $name;
}, 10, 2);
```

---

Ces exemples vous permettent d'étendre et personnaliser le plugin selon vos besoins spécifiques.
