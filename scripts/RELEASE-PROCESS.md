# Process de Release - Site Avis Clients

Guide complet pour créer et publier une nouvelle version du plugin.

**Auteur :** VIRY Brandon - https://devweb.viry-brandon.fr

---

## 📋 Checklist pré-release

### ✅ Code et tests
- [ ] Tous les tests manuels passent
- [ ] Pas d'erreurs PHP dans les logs
- [ ] JavaScript fonctionne sans erreur console
- [ ] CSS s'affiche correctement
- [ ] Formulaire de soumission fonctionne
- [ ] Modération admin fonctionne
- [ ] Shortcodes s'affichent correctement
- [ ] Responsive testé (mobile, tablette, desktop)
- [ ] Compatible avec WordPress 5.8+
- [ ] Compatible avec PHP 7.4+

### ✅ Sécurité
- [ ] Nonces vérifiés
- [ ] Sanitization appliquée
- [ ] Échappement en sortie
- [ ] Pas d'injection SQL possible
- [ ] Pas d'XSS possible
- [ ] Rate limiting fonctionnel

### ✅ Documentation
- [ ] README.md à jour
- [ ] CHANGELOG.md à jour
- [ ] readme.txt à jour
- [ ] Commentaires de code à jour
- [ ] Exemples fonctionnels

### ✅ Git
- [ ] Tous les fichiers commités
- [ ] Branche main à jour
- [ ] Pas de conflits

---

## 🔢 Versioning

Suivre le [Semantic Versioning](https://semver.org/lang/fr/) : `MAJOR.MINOR.PATCH`

### MAJOR (1.0.0 → 2.0.0)
Changements incompatibles avec les versions précédentes
- Suppression de fonctionnalités
- Changement de structure de base de données
- Modification d'API publique

### MINOR (1.0.0 → 1.1.0)
Nouvelles fonctionnalités compatibles
- Ajout de shortcodes
- Nouvelles options
- Nouveaux hooks

### PATCH (1.0.0 → 1.0.1)
Corrections de bugs compatibles
- Correction de bugs
- Amélioration de sécurité
- Optimisation de performance

---

## 🚀 Processus de release

### Étape 1 : Préparation (30 min)

#### 1.1 Mettre à jour la version

**Fichier `site-avis-clients.php` :**
```php
/**
 * Version: 1.0.1  ← Changer ici
 */
```

**Fichier `readme.txt` :**
```
Stable tag: 1.0.1  ← Changer ici
```

#### 1.2 Mettre à jour le CHANGELOG

**Fichier `CHANGELOG.md` :**
```markdown
## [1.0.1] - 2026-02-10

### Fixed
- Correction du bug de validation email
- Fix de l'affichage des étoiles sur mobile

### Changed
- Amélioration du message d'erreur spam

### Security
- Renforcement de la validation des nonces
```

#### 1.3 Mettre à jour readme.txt

**Section Changelog :**
```
== Changelog ==

= 1.0.1 =
* Fixed: Correction du bug de validation email
* Fixed: Affichage des étoiles sur mobile
* Changed: Amélioration des messages d'erreur
* Security: Renforcement de la validation
```

---

### Étape 2 : Build (5 min)

#### 2.1 Exécuter le script de build

**Windows (PowerShell) :**
```powershell
.\scripts\build.ps1
```

**Windows (Batch) :**
```batch
scripts\build.bat
```

**Linux/Mac (Bash) :**
```bash
chmod +x scripts/build.sh
./scripts/build.sh
```

#### 2.2 Vérifier le résultat

Le script crée :
```
dist/
├── site-avis-clients-1.0.1.zip
├── site-avis-clients-1.0.1.zip.sha256
└── version.txt
```

#### 2.3 Vérifier le ZIP (optionnel)

**Linux/Mac :**
```bash
./scripts/verify-zip.sh
```

**Windows (PowerShell) :**
```powershell
# Vérifier le contenu
Expand-Archive -Path dist\site-avis-clients-1.0.1.zip -DestinationPath test-extract
ls test-extract\site-avis-clients
Remove-Item -Recurse test-extract
```

---

### Étape 3 : Tests (15 min)

#### 3.1 Tester l'installation

1. Sur un site WordPress de test/staging
2. Désinstaller l'ancienne version (si présente)
3. Installer le nouveau ZIP
4. Activer le plugin
5. Vérifier :
   - [ ] Activation sans erreur
   - [ ] Formulaire s'affiche
   - [ ] Soumission fonctionne
   - [ ] Admin fonctionne
   - [ ] Shortcodes fonctionnent

#### 3.2 Tester la mise à jour

1. Installer la version précédente
2. Créer quelques avis de test
3. Installer la nouvelle version par-dessus
4. Vérifier :
   - [ ] Données préservées
   - [ ] Paramètres préservés
   - [ ] Pas d'erreur
   - [ ] Tout fonctionne

---

### Étape 4 : Git (10 min)

#### 4.1 Commit des changements

```bash
git add .
git commit -m "Release v1.0.1

- Fixed: Bug de validation email
- Fixed: Affichage étoiles mobile
- Changed: Messages d'erreur
- Security: Validation nonces

Changelog complet dans CHANGELOG.md"
```

#### 4.2 Créer un tag

```bash
git tag -a v1.0.1 -m "Version 1.0.1

Corrections et améliorations de sécurité.

Voir CHANGELOG.md pour les détails."
```

#### 4.3 Push vers GitHub

```bash
git push origin main
git push origin v1.0.1
```

---

### Étape 5 : GitHub Release (10 min)

#### 5.1 Créer la release

1. Aller sur https://github.com/brandonviry/Avis-clients---WP---pluggin/releases
2. Cliquer **"Draft a new release"**
3. Remplir :

**Tag :** `v1.0.1` (sélectionner le tag créé)

**Title :** `Site Avis Clients v1.0.1`

**Description :** (copier depuis CHANGELOG.md)
```markdown
## 🐛 Corrections et améliorations

### Fixed
- Correction du bug de validation email
- Fix de l'affichage des étoiles sur mobile

### Changed
- Amélioration du message d'erreur spam

### Security
- Renforcement de la validation des nonces

---

## 📦 Installation

1. Téléchargez `site-avis-clients-1.0.1.zip`
2. Dans WordPress : Extensions > Ajouter > Téléverser
3. Sélectionnez le ZIP et installez
4. Activez le plugin

## 🔒 Vérification

SHA256: [copier depuis .sha256]

## 📚 Documentation

- [README](https://github.com/brandonviry/Avis-clients---WP---pluggin)
- [Installation](https://github.com/brandonviry/Avis-clients---WP---pluggin/blob/main/INSTALLATION.md)
- [Changelog complet](https://github.com/brandonviry/Avis-clients---WP---pluggin/blob/main/CHANGELOG.md)
```

#### 5.2 Attacher les fichiers

1. Cliquer **"Attach binaries"**
2. Uploader depuis `dist/` :
   - `site-avis-clients-1.0.1.zip`
   - `site-avis-clients-1.0.1.zip.sha256`
   - `version.txt`

#### 5.3 Publier

1. Cocher **"Set as the latest release"**
2. Cliquer **"Publish release"**

---

### Étape 6 : Communication (optionnel)

#### Annoncer la release

**Twitter/X :**
```
🎉 Site Avis Clients v1.0.1 est disponible !

✅ Corrections de bugs
🔒 Améliorations de sécurité
📱 Meilleur affichage mobile

⬇️ https://github.com/brandonviry/Avis-clients---WP---pluggin/releases/tag/v1.0.1

#WordPress #Plugin #WebDev
```

**Site web :**
Article de blog avec détails de la release

**Clients :**
Email de notification aux utilisateurs du plugin

---

## 🔄 Workflow complet (résumé)

```bash
# 1. Préparer
# - Modifier Version dans site-avis-clients.php
# - Mettre à jour CHANGELOG.md
# - Mettre à jour readme.txt

# 2. Build
./scripts/build.sh  # ou build.bat / build.ps1

# 3. Vérifier
./scripts/verify-zip.sh  # optionnel

# 4. Tester
# - Installer sur site de test
# - Vérifier fonctionnement

# 5. Git
git add .
git commit -m "Release v1.0.1"
git tag -a v1.0.1 -m "Version 1.0.1"
git push origin main --tags

# 6. GitHub
# - Créer release sur GitHub
# - Uploader ZIP et checksum
# - Publier

# 7. Communiquer (optionnel)
# - Annoncer sur réseaux sociaux
# - Informer les utilisateurs
```

---

## 📊 Templates utiles

### Message de commit
```
Release vX.Y.Z

- Fixed: Description du bug corrigé
- Added: Nouvelle fonctionnalité
- Changed: Modification de comportement
- Security: Amélioration de sécurité

Changelog complet dans CHANGELOG.md
```

### Description de tag
```
Version X.Y.Z

[Description courte de la release]

Voir CHANGELOG.md pour les détails complets.
```

### Description GitHub Release
```markdown
## [Type] Titre de la release

### Fixed
- Liste des corrections

### Added
- Liste des nouvelles fonctionnalités

### Changed
- Liste des changements

### Security
- Liste des améliorations de sécurité

---

## 📦 Installation
[Instructions]

## 🔒 Vérification
SHA256: [checksum]

## 📚 Documentation
[Liens]
```

---

## 🐛 Hotfix rapide

Pour une correction urgente :

```bash
# 1. Version PATCH
# Passer de 1.0.0 à 1.0.1

# 2. Fix le bug

# 3. Build et release immédiatement
./scripts/build.sh
git add .
git commit -m "Hotfix v1.0.1: Fix critical bug X"
git tag v1.0.1
git push origin main --tags

# 4. GitHub Release rapide
```

---

## 📝 Notes importantes

### ⚠️ Ne jamais
- Modifier une release publiée
- Supprimer un tag Git publié
- Changer un fichier ZIP après publication
- Oublier de mettre à jour CHANGELOG.md

### ✅ Toujours
- Tester avant de publier
- Vérifier le checksum
- Mettre à jour la documentation
- Garder un historique clair

### 💡 Bonnes pratiques
- Release le mardi/mercredi (éviter vendredi)
- Tester sur plusieurs environnements
- Garder un backup de l'ancienne version
- Documenter les breaking changes

---

## 📞 Support

**VIRY Brandon**
- Site : https://devweb.viry-brandon.fr
- GitHub : @brandonviry
- Issues : https://github.com/brandonviry/Avis-clients---WP---pluggin/issues

---

**Version de ce guide :** 1.0.0
**Dernière mise à jour :** 2026-02-05
