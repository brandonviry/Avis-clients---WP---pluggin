# 🚀 Démarrage rapide - Site Avis Clients

Guide de démarrage en 5 minutes pour mettre en place le plugin.

## ⚡ Installation express

### 1. Télécharger et activer (2 min)

```bash
# Placez le dossier dans wp-content/plugins/
site-avis-clients/
```

Puis dans WordPress :
1. **Extensions** → **Extensions installées**
2. Activez **Site Avis Clients**
3. Vérifiez que le menu **Avis Clients** ★ apparaît

✅ **Plugin activé !**

---

## 📄 Configuration minimale (3 min)

### 2. Créer la page formulaire

**Pages** → **Ajouter** → Créez une page "Laisser un avis"

Ajoutez le shortcode :
```
[avis_clients_form]
```

**Publier** → Notez l'URL de la page

✅ **Formulaire prêt !**

### 3. Premier test

1. Visitez la page que vous venez de créer
2. Remplissez le formulaire de test :
   - Nom : `Test Client`
   - Email : `test@example.com`
   - Titre : `Premier avis test`
   - Note : ★★★★★
   - Contenu : `Ceci est un test du système d'avis`
3. Cliquez sur **Envoyer mon avis**

✅ **Avis soumis !**

### 4. Modération

1. **Avis Clients** → **Tous les avis**
2. Trouvez votre avis (statut : *En attente*)
3. Survolez l'avis → **Modification rapide**
4. Changez le statut → **Publié**
5. **Mettre à jour**

✅ **Avis publié !**

---

## 🎨 Afficher les avis (optionnel)

### Option 1 : Page dédiée

Créez une page "Nos avis" avec :

```
[avis_clients_stats]

[avis_clients_list limit="10"]
```

### Option 2 : Dans la sidebar

**Apparence** → **Widgets** → **HTML personnalisé**

```html
<h3>Nos avis</h3>
[avis_clients_stats show_distribution="no"]
```

### Option 3 : Dans votre thème

```php
<?php
// Dans votre template
$average = SAC_Review_Handler::get_average_rating();
echo '<strong>' . $average . '</strong>/5 étoiles';
?>
```

---

## 📧 Recevoir les notifications

Par défaut, vous recevez un email à chaque nouvel avis à l'adresse définie dans :

**Réglages** → **Général** → **Adresse de messagerie**

### Si les emails n'arrivent pas :

1. Installez **WP Mail SMTP**
2. Configurez votre serveur SMTP
3. Testez l'envoi

---

## 🎯 Shortcodes essentiels

### Formulaire
```
[avis_clients_form]
[avis_clients_form title="Votre titre personnalisé"]
```

### Liste d'avis
```
[avis_clients_list]
[avis_clients_list limit="5"]
[avis_clients_list rating="5"]
```

### Statistiques
```
[avis_clients_stats]
[avis_clients_stats show_distribution="yes"]
```

---

## 🔧 Personnalisation rapide

### Changer la couleur du bouton

**Apparence** → **Personnaliser** → **CSS additionnel**

```css
.sac-submit-button {
    background: #FF5722 !important;
}

.sac-submit-button:hover {
    background: #E64A19 !important;
}
```

### Changer la couleur des étoiles

```css
.sac-star.hover,
.sac-star.selected,
.sac-review-stars {
    color: #FF9800 !important;
}
```

### Modifier la largeur du formulaire

```css
.sac-review-form-wrapper {
    max-width: 900px;
}
```

---

## 📋 Checklist de mise en production

- [ ] Plugin activé
- [ ] Page formulaire créée et testée
- [ ] Premier avis test soumis et publié
- [ ] Notification email reçue et testée
- [ ] Page d'affichage des avis créée (optionnel)
- [ ] Styles personnalisés appliqués (si souhaité)
- [ ] SMTP configuré pour les emails
- [ ] Lien ajouté dans le menu du site
- [ ] Test sur mobile effectué

---

## 🆘 Problèmes courants

### Le formulaire ne s'affiche pas
- Vérifiez que JavaScript est activé
- Videz le cache (plugin + navigateur)
- Vérifiez les erreurs dans la console (F12)

### L'avis ne s'envoie pas
- Ouvrez la console navigateur (F12)
- Vérifiez l'erreur affichée
- Désactivez les autres plugins temporairement

### Pas de notification email
- Vérifiez l'adresse dans **Réglages** → **Général**
- Installez **WP Mail SMTP**
- Testez avec **Check Email**

### Erreur 403 à la soumission
- Vérifiez votre pare-feu (Wordfence, etc.)
- Désactivez temporairement le pare-feu
- Ajoutez une exception pour `admin-ajax.php`

---

## 📚 Documentation complète

Pour aller plus loin :

- **README.md** - Documentation technique complète
- **INSTALLATION.md** - Guide d'installation détaillé
- **EXAMPLES.md** - Exemples de code pour développeurs
- **STRUCTURE.md** - Architecture du plugin

---

## 🎉 C'est terminé !

Votre système d'avis clients est maintenant opérationnel.

### Prochaines étapes recommandées :

1. Ajoutez le lien "Laisser un avis" dans votre menu
2. Créez une page "Témoignages" pour afficher les avis
3. Configurez SMTP pour garantir la réception des emails
4. Personnalisez les couleurs selon votre charte graphique
5. Testez sur différents appareils (mobile, tablette)

### Pour obtenir de l'aide :

- Consultez les fichiers de documentation
- Vérifiez les exemples de code dans EXAMPLES.md
- Ouvrez une issue sur [GitHub](https://github.com/brandonviry/Avis-clients---WP---pluggin/issues)

---

**Bon succès avec votre nouveau système d'avis clients ! ⭐**

**Développé par VIRY Brandon** - [devweb.viry-brandon.fr](https://devweb.viry-brandon.fr)
