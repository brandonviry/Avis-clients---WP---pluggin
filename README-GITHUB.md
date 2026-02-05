# Site Avis Clients - Plugin WordPress

![WordPress Plugin Version](https://img.shields.io/badge/version-1.0.0-blue)
![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple)
![License](https://img.shields.io/badge/license-GPL%20v2-green)

> Plugin WordPress professionnel pour la gestion des avis clients avec système de notation, formulaire front-end sécurisé et modération.

**Développé par :** [VIRY Brandon](https://devweb.viry-brandon.fr)

---

## 🌟 Fonctionnalités

- ⭐ **Système de notation 1-5 étoiles** avec interface interactive
- 📝 **Formulaire front-end AJAX** sans rechargement de page
- 🛡️ **Sécurité renforcée** - Protection CSRF, validation, sanitization
- 🚫 **Anti-spam intelligent** - Rate limiting + détection automatique
- ✅ **Modération intégrée** - Validation avant publication
- 📧 **Notifications email** - Alertes admin pour nouveaux avis
- 📱 **Design responsive** - Compatible mobile, tablette, desktop
- 🎨 **3 shortcodes** prêts à l'emploi
- 🌍 **Prêt pour la traduction** - i18n ready
- 🔧 **Extensible** - Hooks et filtres personnalisés

---

## 📦 Installation

### Via WordPress Admin (Recommandé)

1. Téléchargez la dernière version depuis [Releases](https://github.com/brandonviry/Avis-clients---WP---pluggin/releases)
2. Allez dans **Extensions > Ajouter**
3. Cliquez sur **Téléverser une extension**
4. Sélectionnez le fichier ZIP et installez
5. Activez le plugin

### Via Git

```bash
cd wp-content/plugins/
git clone https://github.com/brandonviry/Avis-clients---WP---pluggin.git site-avis-clients
```

Puis activez le plugin dans WordPress Admin.

---

## 🚀 Utilisation rapide

### 1. Créer une page avec le formulaire

Créez une nouvelle page et ajoutez :

```
[avis_clients_form]
```

### 2. Afficher la liste des avis

```
[avis_clients_list limit="10"]
```

### 3. Afficher les statistiques

```
[avis_clients_stats]
```

**📚 Documentation complète :** Consultez les fichiers dans le dossier du plugin

---

## 📋 Prérequis

- WordPress 5.8 ou supérieur
- PHP 7.4 ou supérieur
- MySQL 5.6 ou supérieur
- JavaScript activé

---

## 🔒 Sécurité

Ce plugin suit strictement les meilleures pratiques WordPress :

- ✅ Protection CSRF avec nonces
- ✅ Validation côté serveur
- ✅ Sanitization complète
- ✅ Échappement en sortie
- ✅ Rate limiting (3/h par IP, 1/jour par email)
- ✅ Détection de spam
- ✅ Vérification des capabilities

---

## 🎨 Shortcodes

### Formulaire de soumission

```php
[avis_clients_form]
[avis_clients_form title="Votre titre personnalisé"]
```

### Liste des avis

```php
[avis_clients_list]
[avis_clients_list limit="5"]
[avis_clients_list rating="5"]
[avis_clients_list orderby="date" order="DESC"]
```

### Statistiques

```php
[avis_clients_stats]
[avis_clients_stats show_distribution="no"]
```

---

## 🔧 API pour développeurs

### Récupérer des avis

```php
// Récupérer les 5 derniers avis 5 étoiles
$reviews = SAC_Review_Handler::get_reviews_by_rating(5, 5);

// Note moyenne
$average = SAC_Review_Handler::get_average_rating();

// Nombre total d'avis
$total = SAC_Review_Handler::get_total_reviews();

// Distribution des notes
$distribution = SAC_Review_Handler::get_rating_distribution();
```

### Hooks disponibles

**Actions :**

```php
do_action('sac_review_submitted', $post_id, $data);
do_action('sac_before_review_insert', $data);
do_action('sac_after_review_insert', $post_id);
```

**Filtres :**

```php
apply_filters('sac_review_default_status', 'pending');
apply_filters('sac_spam_keywords', $keywords);
apply_filters('sac_ip_rate_limit_duration', HOUR_IN_SECONDS);
```

**📚 Plus d'exemples :** Consultez `EXAMPLES.md` dans le plugin

---

## 📸 Captures d'écran

*À venir - Captures d'écran du formulaire, de l'administration et de l'affichage des avis*

---

## 🤝 Contribution

Les contributions sont les bienvenues !

1. **Fork** le projet
2. Créez votre branche (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une **Pull Request**

### Guidelines

- Respectez les WordPress Coding Standards
- Ajoutez des commentaires PHPDoc
- Testez votre code avant de soumettre
- Mettez à jour la documentation si nécessaire

---

## 🐛 Signaler un bug

Trouvé un bug ? [Créez une issue](https://github.com/brandonviry/Avis-clients---WP---pluggin/issues/new)

Merci d'inclure :
- Version de WordPress
- Version de PHP
- Description détaillée du problème
- Étapes pour reproduire
- Captures d'écran si possible

---

## 📝 Changelog

### [1.0.0] - 2026-02-05

#### Ajouté
- Custom Post Type "review"
- Système de notation 1-5 étoiles
- Formulaire front-end AJAX
- Validation et sécurité complète
- Anti-spam et rate limiting
- Modération intégrée
- Notifications email
- 3 shortcodes
- Documentation complète

**[Voir le changelog complet](CHANGELOG.md)**

---

## 📄 Licence

Ce projet est sous licence GPL v2 ou ultérieure - voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 👨‍💻 Auteur

**VIRY Brandon**

- Site web : [devweb.viry-brandon.fr](https://devweb.viry-brandon.fr)
- GitHub : [@brandonviry](https://github.com/brandonviry)

---

## 🌟 Vous aimez ce plugin ?

- ⭐ Mettez une étoile sur GitHub
- 🐛 Signalez les bugs
- 💡 Proposez des améliorations
- 📢 Partagez avec la communauté

---

## 📞 Support

- 📚 **Documentation :** Consultez les fichiers MD dans le plugin
- 🐛 **Issues :** [GitHub Issues](https://github.com/brandonviry/Avis-clients---WP---pluggin/issues)
- 💬 **Questions :** Ouvrez une issue avec le label "question"

---

## 📊 Statistiques du projet

- **2024+ lignes de code** (PHP, CSS, JavaScript)
- **90+ KB de documentation**
- **3 classes PHP** principales
- **8+ hooks** personnalisés
- **100% sécurisé** selon les standards WordPress

---

## 🔗 Liens utiles

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [WordPress Security Best Practices](https://developer.wordpress.org/plugins/security/)

---

<div align="center">

**Développé avec ❤️ par [VIRY Brandon](https://devweb.viry-brandon.fr)**

[![Website](https://img.shields.io/badge/Website-devweb.viry--brandon.fr-blue?style=flat-square)](https://devweb.viry-brandon.fr)
[![GitHub](https://img.shields.io/badge/GitHub-brandonviry-black?style=flat-square&logo=github)](https://github.com/brandonviry)

</div>
