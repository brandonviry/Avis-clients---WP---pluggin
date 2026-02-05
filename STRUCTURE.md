# Structure du Plugin Site Avis Clients

**Auteur :** VIRY Brandon
**Site web :** https://devweb.viry-brandon.fr
**GitHub :** https://github.com/brandonviry/Avis-clients---WP---pluggin

## 📁 Architecture des fichiers

```
site-avis-clients/
│
├── 📄 site-avis-clients.php          # Fichier principal du plugin
├── 📄 uninstall.php                  # Script de désinstallation
├── 📄 README.md                      # Documentation développeur
├── 📄 readme.txt                     # Documentation WordPress.org
├── 📄 INSTALLATION.md                # Guide d'installation
├── 📄 EXAMPLES.md                    # Exemples de code
├── 📄 STRUCTURE.md                   # Ce fichier
├── 📄 .gitignore                     # Fichiers à ignorer dans Git
│
├── 📁 includes/                      # Classes PHP principales
│   ├── class-sac-review-handler.php  # Gestion des avis
│   ├── class-sac-validator.php       # Validation des données
│   └── class-sac-shortcodes.php      # Shortcodes du plugin
│
├── 📁 templates/                     # Templates d'affichage
│   ├── review-form.php               # Formulaire de soumission
│   └── review-display.php            # Affichage des avis (exemple)
│
├── 📁 assets/                        # Ressources front-end
│   ├── css/
│   │   ├── front.css                 # Styles front-end
│   │   └── admin.css                 # Styles administration
│   └── js/
│       └── front.js                  # JavaScript front-end
│
└── 📁 languages/                     # Fichiers de traduction
    └── site-avis-clients-fr_FR.po    # Traduction française
```

## 🏗️ Architecture du code

### Fichier principal : `site-avis-clients.php`

**Responsabilités :**
- Déclaration des métadonnées du plugin
- Définition des constantes
- Classe principale `Site_Avis_Clients` (singleton)
- Enregistrement des hooks WordPress
- Chargement des dépendances

**Hooks principaux :**
```php
register_activation_hook()    // Activation du plugin
register_deactivation_hook()  // Désactivation du plugin
add_action('init')            // Enregistrement du CPT
add_action('add_meta_boxes')  // Meta boxes admin
add_action('wp_ajax_*')       // Handlers AJAX
add_shortcode()               // Shortcodes
```

### Classes incluses

#### `SAC_Review_Handler` (includes/class-sac-review-handler.php)

**Responsabilités :**
- Traitement des soumissions d'avis
- Création des posts de type "review"
- Gestion des métadonnées
- Récupération des statistiques
- Envoi des notifications email

**Méthodes publiques :**
```php
process_submission($data)              // Traiter une soumission
get_reviews_by_rating($rating, $limit) // Récupérer les avis
get_average_rating()                   // Moyenne des notes
get_total_reviews()                    // Nombre total
get_rating_distribution()              // Distribution des notes
```

#### `SAC_Validator` (includes/class-sac-validator.php)

**Responsabilités :**
- Validation des données de formulaire
- Détection de spam
- Rate limiting
- Sanitization des données

**Méthodes publiques :**
```php
validate_review_data($data)     // Valider les données
is_spam($content)               // Détecter le spam
check_rate_limit($ip, $email)   // Vérifier les limites
update_rate_limit($ip, $email)  // Mettre à jour les compteurs
sanitize_review_data($data)     // Nettoyer les données
```

#### `SAC_Shortcodes` (includes/class-sac-shortcodes.php)

**Responsabilités :**
- Enregistrement des shortcodes
- Rendu des templates de shortcode

**Shortcodes disponibles :**
```php
[avis_clients_form]      // Formulaire de soumission
[avis_clients_list]      // Liste des avis
[avis_clients_stats]     // Statistiques
```

## 🗄️ Base de données

### Custom Post Type : `review`

**Champs de base (wp_posts) :**
- `ID` - Identifiant unique
- `post_title` - Titre de l'avis
- `post_content` - Contenu de l'avis
- `post_status` - Statut (pending, publish, etc.)
- `post_date` - Date de soumission
- `post_author` - Auteur (0 pour front-end)

**Meta données (wp_postmeta) :**
- `_sac_rating` - Note (1-5)
- `_sac_author_name` - Nom de l'auteur
- `_sac_author_email` - Email de l'auteur
- `_sac_author_ip` - Adresse IP
- `_sac_submission_date` - Date de soumission

### Options WordPress (wp_options)

- `sac_settings` - Configuration du plugin
  ```php
  array(
      'require_moderation' => true,
      'allow_anonymous' => false,
      'min_rating' => 1,
      'max_rating' => 5
  )
  ```

### Transients (cache temporaire)

- `sac_ip_{md5_hash}` - Compteur rate limit par IP (1h)
- `sac_email_{md5_hash}` - Compteur rate limit par email (24h)

## 🔄 Flux de traitement

### 1. Soumission d'un avis

```
Utilisateur remplit le formulaire
         ↓
JavaScript valide (front.js)
         ↓
Envoi AJAX vers admin-ajax.php
         ↓
Vérification du nonce (sécurité)
         ↓
SAC_Validator::validate_review_data()
         ↓
SAC_Validator::is_spam()
         ↓
SAC_Validator::check_rate_limit()
         ↓
SAC_Review_Handler::process_submission()
         ↓
wp_insert_post() + update_post_meta()
         ↓
Envoi notification email admin
         ↓
Mise à jour rate limit
         ↓
Réponse JSON success/error
```

### 2. Affichage du formulaire

```
Page contient [avis_clients_form]
         ↓
do_shortcode() exécute le shortcode
         ↓
Site_Avis_Clients::render_review_form()
         ↓
Inclusion template review-form.php
         ↓
Enqueue CSS et JS (wp_enqueue_*)
         ↓
Localisation des strings JS
         ↓
Affichage du formulaire HTML
```

### 3. Modération admin

```
Admin accède à Avis Clients
         ↓
Liste des avis (status: pending)
         ↓
Clic sur "Modifier"
         ↓
Affichage meta boxes (note, infos)
         ↓
Changement status → publish
         ↓
save_post_review hook
         ↓
Sauvegarde des métadonnées
         ↓
Avis visible en front-end
```

## 🔒 Sécurité - Checklist

### ✅ Protection CSRF
- [x] Nonces sur tous les formulaires
- [x] `wp_verify_nonce()` avant traitement
- [x] `check_ajax_referer()` pour AJAX

### ✅ Validation
- [x] Validation côté serveur de tous les champs
- [x] Vérification des types de données
- [x] Limites de longueur imposées
- [x] Email validé avec `is_email()`

### ✅ Sanitization
- [x] `sanitize_text_field()` pour texte
- [x] `sanitize_email()` pour emails
- [x] `wp_kses_post()` pour HTML
- [x] `intval()` pour nombres

### ✅ Échappement
- [x] `esc_html()` pour affichage texte
- [x] `esc_attr()` pour attributs HTML
- [x] `esc_url()` pour URLs
- [x] Pas de `echo` direct de données utilisateur

### ✅ Capabilities
- [x] Vérification `current_user_can()` pour admin
- [x] Limitations d'accès aux fonctions sensibles

### ✅ Anti-spam
- [x] Rate limiting par IP et email
- [x] Détection de patterns spam
- [x] Modération par défaut

### ✅ Protection des fichiers
- [x] `if (!defined('ABSPATH'))` dans tous les fichiers
- [x] Pas d'accès direct aux templates

### ✅ Base de données
- [x] Utilisation de `$wpdb->prepare()` si nécessaire
- [x] Pas de requêtes SQL directes non préparées

## 🎨 Personnalisation

### Hooks disponibles pour développeurs

**Actions :**
```php
do_action('sac_review_submitted', $post_id, $data)
do_action('sac_before_review_insert', $data)
do_action('sac_after_review_insert', $post_id)
```

**Filtres :**
```php
apply_filters('sac_review_default_status', 'pending')
apply_filters('sac_spam_keywords', $keywords)
apply_filters('sac_ip_rate_limit_duration', HOUR_IN_SECONDS)
apply_filters('sac_email_rate_limit_duration', DAY_IN_SECONDS)
apply_filters('sac_admin_notification_message', $message, $post_id, $data)
```

### Classes CSS principales

**Formulaire :**
- `.sac-review-form-wrapper` - Container principal
- `.sac-form-group` - Groupe de champ
- `.sac-input` / `.sac-textarea` - Champs de saisie
- `.sac-rating-input` - Container de notation
- `.sac-star` - Étoile individuelle
- `.sac-submit-button` - Bouton d'envoi

**Messages :**
- `.sac-form-messages` - Container messages
- `.sac-form-messages.success` - Message succès
- `.sac-form-messages.error` - Message erreur

**Affichage :**
- `.sac-reviews-container` - Container liste avis
- `.sac-review-item` - Avis individuel
- `.sac-stats-widget` - Widget statistiques

## 📊 Performances

### Optimisations implémentées

1. **Cache avec Transients**
   - Rate limiting stocké en transients
   - Expiration automatique
   - Pas de requêtes répétées

2. **Requêtes optimisées**
   - Utilisation de WP_Query avec limites
   - Index sur meta_key pour rapidité
   - Pas de requêtes dans les boucles

3. **Assets conditionnels**
   - CSS/JS chargés uniquement si nécessaire
   - Pas de chargement sur toutes les pages

4. **Lazy loading**
   - Shortcodes rendus à la demande
   - Pas de traitement inutile

## 🧪 Tests recommandés

### Tests fonctionnels
- [ ] Soumission formulaire avec données valides
- [ ] Validation des champs requis
- [ ] Détection de spam
- [ ] Rate limiting IP
- [ ] Rate limiting email
- [ ] Notification email admin
- [ ] Modération et publication
- [ ] Affichage front-end

### Tests de sécurité
- [ ] Tentative CSRF
- [ ] Injection XSS
- [ ] Injection SQL
- [ ] Upload de fichiers malveillants
- [ ] Spam massif
- [ ] Accès direct aux fichiers

### Tests de compatibilité
- [ ] WordPress 5.8+
- [ ] PHP 7.4+
- [ ] Thèmes populaires
- [ ] Plugins de cache
- [ ] Plugins de sécurité

## 📝 Conventions de code

### Standards WordPress
- Respect du WordPress Coding Standards
- Documentation PHPDoc pour toutes les fonctions
- Indentation : 4 espaces
- Accolades sur nouvelle ligne pour les fonctions

### Nommage
- Classes : `SAC_Class_Name`
- Fonctions : `sac_function_name()`
- Meta keys : `_sac_meta_name`
- Options : `sac_option_name`
- CSS : `.sac-class-name`
- JS : `sacVariableName`

### Internationalisation
- Text domain : `site-avis-clients`
- Toutes les chaînes traduisibles
- Utilisation de `__()`, `_e()`, `_n()`

---

**Version :** 1.0.0
**Dernière mise à jour :** 2026-02-05
