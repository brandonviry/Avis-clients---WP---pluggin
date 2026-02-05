# Site Avis Clients - WordPress Plugin

Un plugin WordPress professionnel pour la gestion des avis clients avec système de notation, formulaire front-end sécurisé et modération.

**Auteur :** VIRY Brandon
**Site web :** [devweb.viry-brandon.fr](https://devweb.viry-brandon.fr)
**GitHub :** [github.com/brandonviry/Avis-clients---WP---pluggin](https://github.com/brandonviry/Avis-clients---WP---pluggin)

## 🌟 Fonctionnalités

- **Custom Post Type "review"** - Type de contenu dédié pour les avis
- **Système de notation 1-5 étoiles** - Interface visuelle intuitive
- **Formulaire front-end** - Soumission d'avis via AJAX sans rechargement
- **Sécurité complète** - Nonces, sanitization, validation, échappement
- **Protection anti-spam** - Détection automatique et rate limiting
- **Modération** - Validation des avis avant publication
- **Notifications email** - Alertes admin pour nouveaux avis
- **Responsive design** - Adapté à tous les appareils

## 📋 Prérequis

- WordPress 5.8 ou supérieur
- PHP 7.4 ou supérieur
- JavaScript activé

## 🚀 Installation

1. Téléchargez ou clonez ce dépôt dans `/wp-content/plugins/site-avis-clients/`
2. Activez le plugin via le menu 'Extensions' dans WordPress
3. Le Custom Post Type "review" est automatiquement enregistré

## 📖 Utilisation

### Afficher le formulaire d'avis

Ajoutez le shortcode sur n'importe quelle page ou article :

```php
[avis_clients_form]
```

Avec un titre personnalisé :

```php
[avis_clients_form title="Partagez votre expérience"]
```

### Afficher les avis (via code)

```php
// Récupérer les avis
$reviews = SAC_Review_Handler::get_reviews_by_rating(0, 10);

// Récupérer la moyenne
$average = SAC_Review_Handler::get_average_rating();

// Récupérer le nombre total
$total = SAC_Review_Handler::get_total_reviews();

// Récupérer la distribution des notes
$distribution = SAC_Review_Handler::get_rating_distribution();
```

## 🔒 Sécurité

Le plugin suit strictement les standards WordPress et les meilleures pratiques :

### Protection CSRF
- Vérification de nonce pour toutes les soumissions (`wp_verify_nonce`)
- Nonces AJAX avec `wp_create_nonce` et `check_ajax_referer`

### Validation des données
- Validation côté serveur de tous les champs
- Vérification des types et longueurs
- Validation email avec `is_email()`

### Sanitization
- `sanitize_text_field()` pour les champs texte
- `sanitize_email()` pour les emails
- `wp_kses_post()` pour le contenu HTML
- `intval()` pour les nombres

### Échappement
- `esc_html()` pour le texte
- `esc_attr()` pour les attributs
- `esc_url()` pour les URLs

### Anti-spam
- Rate limiting par IP (3 avis/heure)
- Rate limiting par email (1 avis/jour)
- Détection de patterns spam
- Limitation du nombre de liens

### Autres protections
- Vérification des capabilities (`current_user_can`)
- Protection des accès directs (`ABSPATH`)
- Prévention des injections SQL (prepared statements)
- XSS prevention via échappement systématique

## 📁 Structure du plugin

```
site-avis-clients/
├── site-avis-clients.php       # Fichier principal
├── includes/
│   ├── class-sac-review-handler.php   # Gestion des avis
│   └── class-sac-validator.php        # Validation des données
├── templates/
│   └── review-form.php         # Template du formulaire
├── assets/
│   ├── css/
│   │   ├── front.css          # Styles front-end
│   │   └── admin.css          # Styles admin
│   └── js/
│       └── front.js           # JavaScript front-end
├── languages/                  # Fichiers de traduction
├── readme.txt                 # Documentation WordPress
├── README.md                  # Documentation développeur
└── .gitignore
```

## 🎨 Personnalisation

### Surcharge des styles

Ajoutez votre CSS personnalisé dans votre thème :

```css
.sac-review-form-wrapper {
    background: #f5f5f5;
}

.sac-submit-button {
    background: #your-color;
}
```

### Hooks disponibles

```php
// Après soumission d'un avis
add_action('sac_review_submitted', function($post_id, $data) {
    // Votre code
}, 10, 2);
```

## 🔧 Configuration

Les options suivantes sont disponibles (stockées dans `sac_settings`) :

- `require_moderation` - Modération obligatoire (défaut : true)
- `allow_anonymous` - Autoriser les avis anonymes (défaut : false)
- `min_rating` - Note minimale (défaut : 1)
- `max_rating` - Note maximale (défaut : 5)

## 📊 Fonctionnalités de l'administration

- Liste des avis avec colonne de notation
- Meta boxes pour la note et les informations
- Filtre et tri des avis
- Modération en un clic
- Statistiques (à venir)

## 🌐 Internationalisation

Le plugin est prêt pour la traduction :

- Text domain : `site-avis-clients`
- Domain path : `/languages`

## 🐛 Débogage

Pour activer le mode debug WordPress :

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## 📝 Changelog

### Version 1.0.0 (2026-02-05)
- Version initiale
- Custom Post Type "review"
- Système de notation 1-5 étoiles
- Formulaire front-end AJAX
- Validation et sécurité complète
- Protection anti-spam et rate limiting
- Interface d'administration

## 📄 Licence

GPL v2 ou ultérieure - https://www.gnu.org/licenses/gpl-2.0.html

## 👨‍💻 Auteur

**VIRY Brandon**
- Site web : [devweb.viry-brandon.fr](https://devweb.viry-brandon.fr)

Développé en suivant les standards WordPress Codex et les meilleures pratiques de sécurité.

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :

1. Fork le projet sur [GitHub](https://github.com/brandonviry/Avis-clients---WP---pluggin)
2. Créer une branche (`git checkout -b feature/amelioration`)
3. Commit vos changements (`git commit -m 'Ajout d'une fonctionnalité'`)
4. Push vers la branche (`git push origin feature/amelioration`)
5. Ouvrir une Pull Request

## 📞 Support

Pour toute question ou problème, veuillez ouvrir une issue sur [GitHub](https://github.com/brandonviry/Avis-clients---WP---pluggin/issues).

---

**Note** : Ce plugin suit strictement les recommandations du WordPress Plugin Handbook et a été développé avec un focus sur la sécurité, la performance et l'expérience utilisateur.
