=== Site Avis Clients ===
Contributors: virybrandon
Tags: reviews, testimonials, ratings, customer-reviews, avis
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author: VIRY Brandon
Author URI: https://devweb.viry-brandon.fr

Gestion complète des avis clients avec système de notation, formulaire front-end et modération.

== Description ==

Site Avis Clients est un plugin WordPress professionnel qui vous permet de collecter, gérer et afficher les avis de vos clients directement sur votre site.

= Fonctionnalités principales =

* **Custom Post Type "review"** - Type de contenu dédié pour les avis
* **Système de notation 1-5 étoiles** - Notation visuelle et intuitive
* **Formulaire front-end sécurisé** - Vos clients peuvent soumettre leurs avis facilement
* **Validation complète** - Validation des données côté client et serveur
* **Protection anti-spam** - Détection automatique des contenus suspects
* **Rate limiting** - Limite le nombre d'avis par IP et email
* **Modération** - Les avis sont en attente de validation par défaut
* **Notifications email** - Recevez un email pour chaque nouvel avis
* **Interface d'administration** - Gestion facile depuis le tableau de bord WordPress
* **Sécurité renforcée** - Nonces, sanitization, validation, échappement
* **Responsive** - Design adaptatif pour tous les appareils

= Utilisation =

Ajoutez le shortcode suivant sur n'importe quelle page ou article pour afficher le formulaire d'avis :

`[avis_clients_form]`

Vous pouvez personnaliser le titre du formulaire :

`[avis_clients_form title="Partagez votre expérience"]`

= Sécurité =

Le plugin suit strictement les meilleures pratiques WordPress :

* Vérification des nonces pour toutes les soumissions
* Sanitization de toutes les entrées utilisateur
* Échappement de toutes les sorties
* Validation côté serveur
* Protection CSRF
* Rate limiting anti-spam
* Détection de contenu malveillant

= Développeurs =

Le plugin utilise plusieurs hooks personnalisés :

* `sac_review_submitted` - Déclenché après la soumission d'un avis
* `sac_before_review_insert` - Avant l'insertion d'un avis
* `sac_after_review_insert` - Après l'insertion d'un avis

== Installation ==

1. Téléchargez le dossier `site-avis-clients` dans `/wp-content/plugins/`
2. Activez le plugin via le menu 'Extensions' dans WordPress
3. Ajoutez le shortcode `[avis_clients_form]` sur une page
4. Configurez les options dans Réglages > Avis Clients (à venir)

== Frequently Asked Questions ==

= Comment afficher le formulaire d'avis ? =

Utilisez le shortcode `[avis_clients_form]` dans n'importe quelle page ou article.

= Les avis sont-ils modérés ? =

Oui, par défaut tous les avis sont en attente de modération. Vous pouvez les approuver depuis l'administration.

= Peut-on limiter le nombre d'avis par utilisateur ? =

Oui, le plugin limite automatiquement à 3 avis par heure par IP et 1 avis par jour par email.

= Le plugin est-il sécurisé ? =

Oui, il suit toutes les meilleures pratiques de sécurité WordPress avec validation, sanitization et protection anti-spam.

= Comment personnaliser les styles ? =

Vous pouvez surcharger les styles en ajoutant votre propre CSS dans votre thème.

== Screenshots ==

1. Formulaire d'avis front-end
2. Liste des avis dans l'administration
3. Édition d'un avis
4. Meta box de notation
5. Colonne de notation dans la liste

== Changelog ==

= 1.0.0 =
* Version initiale
* Custom Post Type "review"
* Système de notation 1-5 étoiles
* Formulaire front-end avec AJAX
* Validation et sécurité complète
* Protection anti-spam
* Rate limiting
* Notifications email
* Interface d'administration

== Upgrade Notice ==

= 1.0.0 =
Version initiale du plugin.

== Configuration requise ==

* WordPress 5.8 ou supérieur
* PHP 7.4 ou supérieur
* JavaScript activé pour le formulaire AJAX

== Support ==

Pour toute question ou demande de support, veuillez créer un ticket sur le dépôt GitHub du projet :
https://github.com/brandonviry/Avis-clients---WP---pluggin/issues

== Crédits ==

Développé avec ❤️ par VIRY Brandon en suivant les standards WordPress et les meilleures pratiques de sécurité.

Site web : https://devweb.viry-brandon.fr
GitHub : https://github.com/brandonviry/Avis-clients---WP---pluggin
