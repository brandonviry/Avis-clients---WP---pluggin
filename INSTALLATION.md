# Guide d'installation - Site Avis Clients

## 📦 Installation du plugin

### Méthode 1 : Installation manuelle

1. Téléchargez le dossier `site-avis-clients`
2. Placez-le dans `/wp-content/plugins/`
3. Connectez-vous à votre administration WordPress
4. Allez dans **Extensions > Extensions installées**
5. Activez **Site Avis Clients**

### Méthode 2 : Via FTP

1. Compressez le dossier `site-avis-clients` en ZIP
2. Connectez-vous à votre administration WordPress
3. Allez dans **Extensions > Ajouter**
4. Cliquez sur **Téléverser une extension**
5. Sélectionnez le fichier ZIP et cliquez sur **Installer**
6. Activez l'extension

## 🚀 Configuration initiale

### Étape 1 : Vérification de l'activation

Après l'activation, vous devriez voir :
- Un nouveau menu **Avis Clients** dans la barre latérale
- Une icône en forme d'étoile ★

### Étape 2 : Créer une page pour le formulaire

1. Allez dans **Pages > Ajouter**
2. Créez une nouvelle page (ex: "Laisser un avis")
3. Ajoutez le shortcode suivant :
```
[avis_clients_form]
```
4. Publiez la page

### Étape 3 : Créer une page pour afficher les avis (optionnel)

Vous pouvez créer une page dédiée pour afficher tous les avis :

1. Créez une nouvelle page (ex: "Nos avis clients")
2. Utilisez les shortcodes suivants :

```
[avis_clients_stats]

[avis_clients_list limit="10"]
```

## 📝 Shortcodes disponibles

### Formulaire de soumission

```
[avis_clients_form]
```

Avec titre personnalisé :
```
[avis_clients_form title="Partagez votre expérience"]
```

### Liste des avis

Afficher tous les avis :
```
[avis_clients_list]
```

Afficher les 5 derniers avis :
```
[avis_clients_list limit="5"]
```

Afficher uniquement les avis 5 étoiles :
```
[avis_clients_list rating="5" limit="10"]
```

Trier par note (rating) :
```
[avis_clients_list orderby="meta_value_num" order="DESC"]
```

### Statistiques

Afficher les statistiques :
```
[avis_clients_stats]
```

Masquer la distribution :
```
[avis_clients_stats show_distribution="no"]
```

## 🔧 Configuration avancée

### Personnaliser les styles

Ajoutez votre CSS personnalisé dans **Apparence > Personnaliser > CSS additionnel** :

```css
/* Couleur du bouton d'envoi */
.sac-submit-button {
    background: #your-color !important;
}

/* Couleur des étoiles */
.sac-star.hover,
.sac-star.selected {
    color: #your-color !important;
}

/* Largeur maximale du formulaire */
.sac-review-form-wrapper {
    max-width: 800px;
}
```

### Modifier les textes

Les textes sont traductibles. Pour les modifier :

1. Installez et activez **Loco Translate**
2. Allez dans **Loco Translate > Extensions**
3. Sélectionnez **Site Avis Clients**
4. Créez une nouvelle traduction pour votre langue
5. Modifiez les textes souhaités

### Configuration de la modération

Par défaut, tous les avis sont en attente de modération. Pour modérer un avis :

1. Allez dans **Avis Clients > Tous les avis**
2. Survolez l'avis en question
3. Cliquez sur **Modification rapide**
4. Changez l'état de **En attente** à **Publié**
5. Cliquez sur **Mettre à jour**

Ou cliquez sur l'avis pour l'éditer et publiez-le directement.

## 📧 Notifications email

### Configuration SMTP (recommandé)

Pour garantir la réception des emails de notification, configurez un serveur SMTP :

1. Installez **WP Mail SMTP** ou **Easy WP SMTP**
2. Configurez vos paramètres SMTP
3. Testez l'envoi d'emails

Les notifications sont envoyées à l'adresse email d'administration définie dans **Réglages > Général**.

## 🎨 Intégration dans le thème

### Afficher les avis dans un template

```php
<?php
// Dans votre template de thème
$reviews = SAC_Review_Handler::get_reviews_by_rating(0, 5);

if ($reviews->have_posts()) :
    while ($reviews->have_posts()) : $reviews->the_post();
        $rating = get_post_meta(get_the_ID(), '_sac_rating', true);
        ?>
        <div class="review">
            <h3><?php the_title(); ?></h3>
            <div class="rating">
                <?php echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating); ?>
            </div>
            <div class="content">
                <?php the_content(); ?>
            </div>
        </div>
        <?php
    endwhile;
    wp_reset_postdata();
endif;
?>
```

### Afficher la note moyenne

```php
<?php
$average = SAC_Review_Handler::get_average_rating();
$total = SAC_Review_Handler::get_total_reviews();

echo '<div class="average-rating">';
echo '<strong>' . number_format($average, 1) . '</strong> / 5';
echo '<span> (' . $total . ' avis)</span>';
echo '</div>';
?>
```

## 🔒 Sécurité et anti-spam

Le plugin inclut plusieurs mesures de protection :

### Rate limiting
- 3 avis maximum par heure par adresse IP
- 1 avis maximum par jour par email

### Détection de spam
- Limite de 3 liens maximum par avis
- Détection des MAJUSCULES excessives
- Mots-clés spam bloqués automatiquement

### Pour modifier les limites (via code)

Ajoutez dans votre `functions.php` :

```php
// Modifier la limite par IP (en secondes)
add_filter('sac_ip_rate_limit', function($limit) {
    return HOUR_IN_SECONDS; // 1 heure
});

// Modifier la limite par email (en secondes)
add_filter('sac_email_rate_limit', function($limit) {
    return DAY_IN_SECONDS; // 1 jour
});
```

## 📊 Widgets et Sidebar

Pour ajouter les statistiques dans une sidebar :

1. Allez dans **Apparence > Widgets**
2. Ajoutez un widget **HTML personnalisé**
3. Collez le shortcode :
```
[avis_clients_stats]
```

## 🐛 Dépannage

### Les avis ne s'affichent pas

1. Vérifiez que des avis sont publiés (pas en attente)
2. Videz le cache si vous utilisez un plugin de cache
3. Vérifiez que le shortcode est correctement écrit

### Le formulaire ne s'envoie pas

1. Vérifiez que JavaScript est activé
2. Ouvrez la console du navigateur (F12) pour voir les erreurs
3. Vérifiez que l'URL AJAX est correcte
4. Désactivez temporairement les autres plugins pour tester les conflits

### Les emails ne sont pas reçus

1. Testez avec **Check Email** ou **WP Mail Logging**
2. Configurez SMTP avec **WP Mail SMTP**
3. Vérifiez votre dossier spam
4. Vérifiez l'adresse email dans **Réglages > Général**

### Erreur 403 lors de la soumission

1. Vérifiez les règles de votre pare-feu (Wordfence, Cloudflare, etc.)
2. Ajoutez une exception pour `/wp-admin/admin-ajax.php`
3. Désactivez temporairement le pare-feu pour tester

## 🔄 Mise à jour

Pour mettre à jour le plugin :

1. Désactivez le plugin
2. Sauvegardez votre base de données
3. Remplacez les fichiers du plugin
4. Réactivez le plugin
5. Testez que tout fonctionne

## 🗑️ Désinstallation

Pour désinstaller complètement le plugin :

1. Allez dans **Extensions > Extensions installées**
2. Désactivez **Site Avis Clients**
3. Cliquez sur **Supprimer**

**Note** : Par défaut, les avis sont conservés même après la désinstallation. Pour supprimer toutes les données, ajoutez dans `wp-config.php` :

```php
define('SAC_DELETE_DATA_ON_UNINSTALL', true);
```

## 📞 Support

Pour toute question ou problème :

1. Consultez la documentation dans `README.md`
2. Vérifiez les [issues GitHub](https://github.com/brandonviry/Avis-clients---WP---pluggin/issues)
3. Ouvrez une nouvelle issue si nécessaire

**Auteur :** VIRY Brandon
**Site web :** https://devweb.viry-brandon.fr

## ✅ Checklist post-installation

- [ ] Plugin activé
- [ ] Page "Laisser un avis" créée avec le shortcode
- [ ] Test de soumission d'avis effectué
- [ ] Email de notification reçu
- [ ] Avis validé et publié
- [ ] Affichage des avis vérifié
- [ ] Styles personnalisés (si souhaité)
- [ ] Configuration SMTP (recommandé)

---

**Félicitations ! Votre plugin Site Avis Clients est maintenant opérationnel.**
