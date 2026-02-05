# Guide de contribution - Site Avis Clients

Merci de votre intérêt pour contribuer au plugin Site Avis Clients ! 🎉

## 📋 Table des matières

- [Code de conduite](#code-de-conduite)
- [Comment contribuer](#comment-contribuer)
- [Standards de code](#standards-de-code)
- [Process de développement](#process-de-développement)
- [Signaler des bugs](#signaler-des-bugs)
- [Proposer des fonctionnalités](#proposer-des-fonctionnalités)
- [Pull Requests](#pull-requests)

---

## 🤝 Code de conduite

En participant à ce projet, vous vous engagez à respecter notre code de conduite :

- Soyez respectueux et professionnel
- Acceptez les critiques constructives
- Concentrez-vous sur ce qui est meilleur pour la communauté
- Faites preuve d'empathie envers les autres membres

---

## 🚀 Comment contribuer

### Types de contributions

Vous pouvez contribuer de plusieurs façons :

1. **🐛 Signaler des bugs** - Aidez-nous à identifier les problèmes
2. **✨ Proposer des fonctionnalités** - Suggérez de nouvelles idées
3. **📝 Améliorer la documentation** - Rendez-la plus claire
4. **💻 Soumettre du code** - Corrections ou nouvelles fonctionnalités
5. **🌍 Traduire** - Aidez à traduire le plugin
6. **✅ Tester** - Testez les nouvelles versions

---

## 📏 Standards de code

### WordPress Coding Standards

Le plugin suit strictement les [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/):

```php
// ✅ Bon
function sac_get_reviews( $limit = 10 ) {
    $reviews = get_posts( array(
        'post_type'      => 'review',
        'posts_per_page' => intval( $limit ),
    ) );

    return $reviews;
}

// ❌ Mauvais
function sacGetReviews($limit=10){
    $reviews=get_posts(array('post_type'=>'review','posts_per_page'=>$limit));
    return $reviews;
}
```

### Conventions de nommage

#### Classes
```php
class SAC_Review_Handler {
    // Préfixe SAC_ + PascalCase
}
```

#### Fonctions
```php
function sac_get_average_rating() {
    // Préfixe sac_ + snake_case
}
```

#### Meta keys
```php
update_post_meta( $post_id, '_sac_rating', $rating );
// Préfixe _sac_ + snake_case
```

#### Options
```php
get_option( 'sac_settings' );
// Préfixe sac_ + snake_case
```

#### CSS
```css
.sac-review-form {
    /* Préfixe sac- + kebab-case */
}
```

#### JavaScript
```javascript
var sacData = {
    // Préfixe sac + camelCase
};
```

### Documentation

Toutes les fonctions doivent avoir des commentaires PHPDoc :

```php
/**
 * Get the average rating of all reviews
 *
 * @since 1.0.0
 * @return float Average rating (0-5)
 */
public static function get_average_rating() {
    // Code...
}
```

### Sécurité

**TOUJOURS :**

1. **Validation**
```php
$rating = intval( $_POST['rating'] );
if ( $rating < 1 || $rating > 5 ) {
    wp_send_json_error();
}
```

2. **Sanitization**
```php
$name = sanitize_text_field( $_POST['name'] );
$email = sanitize_email( $_POST['email'] );
$content = wp_kses_post( $_POST['content'] );
```

3. **Échappement**
```php
echo esc_html( $name );
echo '<a href="' . esc_url( $url ) . '">';
echo '<input value="' . esc_attr( $value ) . '">';
```

4. **Nonces**
```php
wp_verify_nonce( $_POST['nonce'], 'sac_action' );
check_ajax_referer( 'sac_submit_review', 'nonce' );
```

5. **Capabilities**
```php
if ( ! current_user_can( 'edit_post', $post_id ) ) {
    return;
}
```

---

## 🔄 Process de développement

### 1. Fork et Clone

```bash
# Fork le projet sur GitHub, puis :
git clone https://github.com/VOTRE-USERNAME/Avis-clients---WP---pluggin.git
cd Avis-clients---WP---pluggin
```

### 2. Créer une branche

```bash
# Pour une nouvelle fonctionnalité
git checkout -b feature/ma-fonctionnalite

# Pour un bug fix
git checkout -b fix/nom-du-bug

# Pour de la documentation
git checkout -b docs/amelioration
```

### 3. Développer

- Écrivez du code propre et commenté
- Suivez les standards WordPress
- Testez votre code localement
- Vérifiez la sécurité

### 4. Tester

```bash
# Testez avec :
- WordPress 5.8+
- PHP 7.4, 8.0, 8.1
- Différents thèmes
- Plugins de cache désactivés/activés
- Mobile et desktop
```

### 5. Commit

```bash
git add .
git commit -m "feat: ajouter la fonctionnalité X"

# Format des commits :
# feat: nouvelle fonctionnalité
# fix: correction de bug
# docs: documentation
# style: formatage
# refactor: refactorisation
# test: ajout de tests
# chore: tâches diverses
```

### 6. Push et Pull Request

```bash
git push origin feature/ma-fonctionnalite
```

Puis ouvrez une Pull Request sur GitHub.

---

## 🐛 Signaler des bugs

### Avant de signaler

1. Vérifiez que le bug n'a pas déjà été signalé
2. Testez avec les plugins désactivés
3. Testez avec un thème par défaut
4. Vérifiez les logs d'erreur

### Créer un rapport de bug

Utilisez le [template de bug report](https://github.com/brandonviry/Avis-clients---WP---pluggin/issues/new?template=bug_report.md) et incluez :

- Description claire du problème
- Étapes pour reproduire
- Comportement attendu vs actuel
- Environnement (WP, PHP, thème, navigateur)
- Captures d'écran
- Logs d'erreur

---

## ✨ Proposer des fonctionnalités

### Avant de proposer

1. Vérifiez que la fonctionnalité n'existe pas
2. Vérifiez qu'elle n'a pas déjà été proposée
3. Réfléchissez à l'utilité pour la communauté

### Créer une demande de fonctionnalité

Utilisez le [template de feature request](https://github.com/brandonviry/Avis-clients---WP---pluggin/issues/new?template=feature_request.md) et incluez :

- Description claire de la fonctionnalité
- Cas d'usage concrets
- Exemples de code (si applicable)
- Bénéfices pour les utilisateurs

---

## 🔀 Pull Requests

### Checklist avant soumission

- [ ] Code testé localement
- [ ] Standards WordPress respectés
- [ ] Commentaires PHPDoc ajoutés
- [ ] Sécurité vérifiée (nonces, sanitization, échappement)
- [ ] Documentation mise à jour
- [ ] CHANGELOG.md mis à jour
- [ ] Pas de conflits avec master
- [ ] Commit messages clairs

### Format de la Pull Request

Utilisez le [template de PR](https://github.com/brandonviry/Avis-clients---WP---pluggin/blob/master/.github/PULL_REQUEST_TEMPLATE.md) et remplissez toutes les sections.

### Process de review

1. Soumission de la PR
2. Revue automatique (si CI configuré)
3. Revue par le mainteneur
4. Discussion et modifications si nécessaire
5. Approbation et merge

---

## 🌍 Traductions

### Ajouter une traduction

1. Copiez `languages/site-avis-clients-fr_FR.po`
2. Renommez selon votre langue (ex: `site-avis-clients-en_US.po`)
3. Traduisez les chaînes
4. Générez le fichier .mo avec Poedit
5. Testez la traduction
6. Soumettez une PR

---

## 🧪 Tests

### Tests manuels requis

- [ ] Formulaire de soumission
- [ ] Validation des champs
- [ ] Soumission AJAX
- [ ] Modération admin
- [ ] Affichage des avis
- [ ] Shortcodes
- [ ] Responsive design
- [ ] Compatibilité navigateurs

### Tests de sécurité

- [ ] Tentative CSRF
- [ ] Injection XSS
- [ ] Injection SQL
- [ ] Rate limiting
- [ ] Détection spam

---

## 📁 Structure du projet

```
site-avis-clients/
├── includes/           # Classes PHP
├── templates/          # Templates d'affichage
├── assets/            # CSS et JavaScript
├── languages/         # Traductions
└── .github/           # Templates GitHub
```

---

## 🔧 Environnement de développement

### Prérequis

- WordPress 5.8+ en local
- PHP 7.4+
- MySQL/MariaDB
- Éditeur compatible WordPress (VS Code recommandé)

### Extensions VS Code recommandées

- PHP Intelephense
- WordPress Snippets
- phpcs (WordPress Coding Standards)

### Configuration phpcs

```bash
composer global require "squizlabs/php_codesniffer=*"
composer global require "wp-coding-standards/wpcs=*"
phpcs --config-set installed_paths /path/to/wpcs
phpcs --standard=WordPress site-avis-clients.php
```

---

## 📞 Questions ?

- 💬 Ouvrez une [discussion](https://github.com/brandonviry/Avis-clients---WP---pluggin/discussions)
- 📧 Contactez [VIRY Brandon](https://devweb.viry-brandon.fr)
- 🐛 Créez une [issue](https://github.com/brandonviry/Avis-clients---WP---pluggin/issues)

---

## 🎉 Merci !

Merci de contribuer au projet Site Avis Clients ! Votre aide est précieuse pour la communauté WordPress.

---

**Développé par [VIRY Brandon](https://devweb.viry-brandon.fr)**
