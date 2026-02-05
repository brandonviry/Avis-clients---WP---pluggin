# Scripts de Build

Scripts automatisés pour créer une archive ZIP propre du plugin avec versioning.

**Auteur :** VIRY Brandon - https://devweb.viry-brandon.fr

---

## 📋 Scripts disponibles

### 🪟 Windows : `build.bat`
Script batch pour Windows

### 🐧 Linux/Mac : `build.sh`
Script bash pour Linux et macOS

---

## 🚀 Utilisation

### Windows

```batch
cd "D:\Avis Clients"
scripts\build.bat
```

### Linux/Mac

```bash
cd "/chemin/vers/Avis Clients"
chmod +x scripts/build.sh
./scripts/build.sh
```

---

## 📦 Ce que fait le script

### 1. ✅ Détection automatique de la version
- Lit la version depuis `site-avis-clients.php`
- Format : `Version: X.Y.Z`

### 2. 🧹 Nettoyage
- Supprime le dossier `build/` temporaire
- Crée le dossier `dist/` si nécessaire

### 3. 📁 Copie des fichiers essentiels
**Fichiers inclus dans le ZIP :**
- ✅ `site-avis-clients.php` (fichier principal)
- ✅ `uninstall.php`
- ✅ `includes/` (classes PHP)
- ✅ `templates/` (templates)
- ✅ `assets/` (CSS, JS)
- ✅ `languages/` (traductions)
- ✅ `readme.txt` (documentation WordPress)
- ✅ `LICENSE` (licence)
- ✅ `CHANGELOG.md` (historique)

**Fichiers exclus (documentation développeur) :**
- ❌ `README.md`
- ❌ `INSTALLATION.md`
- ❌ `EXAMPLES.md`
- ❌ `STRUCTURE.md`
- ❌ Autres fichiers `.md`
- ❌ `.github/`
- ❌ `.agents/`
- ❌ `scripts/`
- ❌ `dist/`
- ❌ `.gitignore`

### 4. 📦 Création du ZIP
- Nom : `site-avis-clients-{VERSION}.zip`
- Structure : `site-avis-clients/` à la racine
- Prêt pour installation WordPress

### 5. 📝 Fichier de version
Crée `dist/version.txt` avec :
```
Site Avis Clients - WordPress Plugin
Version: 1.0.0
Date: 2026-02-05 12:00:00
Auteur: VIRY Brandon
Site: https://devweb.viry-brandon.fr
GitHub: https://github.com/brandonviry/Avis-clients---WP---pluggin

Archive: site-avis-clients-1.0.0.zip
```

### 6. 🔒 Checksum SHA256
Crée `site-avis-clients-{VERSION}.zip.sha256` pour vérifier l'intégrité

---

## 📂 Structure générée

Après exécution :

```
dist/
├── site-avis-clients-1.0.0.zip          Archive du plugin
├── site-avis-clients-1.0.0.zip.sha256   Checksum
└── version.txt                           Informations de version
```

Contenu du ZIP :
```
site-avis-clients/
├── site-avis-clients.php
├── uninstall.php
├── readme.txt
├── LICENSE
├── CHANGELOG.md
├── includes/
│   ├── class-sac-review-handler.php
│   ├── class-sac-validator.php
│   └── class-sac-shortcodes.php
├── templates/
│   ├── review-form.php
│   └── review-display.php
├── assets/
│   ├── css/
│   │   ├── front.css
│   │   └── admin.css
│   └── js/
│       └── front.js
└── languages/
    └── site-avis-clients-fr_FR.po
```

---

## 🎯 Utilisation du ZIP généré

### Installation dans WordPress

1. Allez dans **Extensions** → **Ajouter**
2. Cliquez sur **Téléverser une extension**
3. Sélectionnez `site-avis-clients-{VERSION}.zip`
4. Cliquez sur **Installer maintenant**
5. Activez le plugin

### Upload FTP

1. Décompressez le ZIP
2. Uploadez le dossier `site-avis-clients/` dans `/wp-content/plugins/`
3. Activez le plugin dans WordPress

---

## 🔍 Vérification du checksum

### Windows (PowerShell)
```powershell
Get-FileHash "dist\site-avis-clients-1.0.0.zip" -Algorithm SHA256
```

### Linux/Mac
```bash
sha256sum dist/site-avis-clients-1.0.0.zip
```

Comparez avec le contenu de `.sha256`

---

## 🛠️ Personnalisation

### Modifier les fichiers inclus

Éditez la fonction `copy_files()` dans le script :

```bash
# Ajouter des fichiers
cp mon-fichier.txt "$BUILD_DIR/$PLUGIN_SLUG/"

# Exclure des fichiers
# (commentez la ligne de copie)
```

### Changer le format de l'archive

**Linux/Mac** - Pour utiliser tar.gz :
```bash
tar -czf "$ZIP_NAME.tar.gz" "$PLUGIN_SLUG"
```

**Windows** - Utilise PowerShell `Compress-Archive` par défaut

---

## 🐛 Dépannage

### Erreur : "Version introuvable"
- Vérifiez que `site-avis-clients.php` existe
- Vérifiez le format : `* Version: 1.0.0`

### Erreur : "PowerShell non disponible" (Windows)
- Installez PowerShell 5.1+
- Ou utilisez un outil externe (7-Zip, WinRAR)

### Erreur : "Permission denied" (Linux/Mac)
```bash
chmod +x scripts/build.sh
```

### Le ZIP contient des fichiers en trop
- Modifiez la fonction `copy_files()` dans le script
- Supprimez les lignes `cp` non désirées

---

## 📊 Exemple de sortie

```
================================================================
  Site Avis Clients - Script de Build
================================================================

✓ Version detectee: 1.0.0

ℹ Nettoyage des anciens builds...
✓ Dossier build supprime
✓ Dossier dist cree

ℹ Creation de la structure de build...
✓ Structure creee

ℹ Copie des fichiers du plugin...
✓ Fichiers PHP copies
✓ Assets copies
✓ Traductions copiees
✓ Documentation copiee

ℹ Creation de l'archive site-avis-clients-1.0.0.zip...
✓ Archive creee: dist/site-avis-clients-1.0.0.zip

ℹ Creation du fichier de version...
✓ Fichier de version cree

ℹ Calcul du checksum...
✓ Checksum SHA256 cree

✓ Dossier temporaire nettoye

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✓ Build termine avec succes !
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📦 Plugin: Site Avis Clients
🏷️  Version: 1.0.0
📅 Date: 2026-02-05 12:00:00

📂 Archive creee:
   dist/site-avis-clients-1.0.0.zip
   Taille: 45K

🔒 Checksum SHA256:
   a1b2c3d4e5f6...

📝 Fichiers dans dist/:
   site-avis-clients-1.0.0.zip (45K)
   site-avis-clients-1.0.0.zip.sha256 (65 bytes)
   version.txt (250 bytes)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

## 🔄 Workflow recommandé

### 1. Développement
```bash
# Développez normalement
# Testez localement
```

### 2. Mise à jour de la version
```php
// Dans site-avis-clients.php
* Version: 1.0.1
```

### 3. Mise à jour du CHANGELOG
```markdown
## [1.0.1] - 2026-02-10
### Fixed
- Correction du bug X
```

### 4. Build
```bash
./scripts/build.sh
```

### 5. Test du ZIP
- Installez le ZIP sur un WordPress de test
- Vérifiez que tout fonctionne

### 6. Commit et tag Git
```bash
git add .
git commit -m "Release 1.0.1"
git tag v1.0.1
git push origin main --tags
```

### 7. Release GitHub
- Uploadez le ZIP depuis `dist/`
- Copiez le checksum
- Ajoutez les notes de version

---

## 📋 Checklist avant build

- [ ] Version mise à jour dans `site-avis-clients.php`
- [ ] CHANGELOG.md mis à jour
- [ ] Code testé localement
- [ ] Documentation à jour
- [ ] Tous les fichiers commités
- [ ] Pas de fichiers temporaires

---

## 🎯 Cas d'usage

### Build pour WordPress.org
```bash
./scripts/build.sh
# Upload du ZIP sur wordpress.org/plugins
```

### Build pour GitHub Release
```bash
./scripts/build.sh
# Créer une release avec le ZIP et le checksum
```

### Build pour client
```bash
./scripts/build.sh
# Envoyez dist/site-avis-clients-X.Y.Z.zip au client
```

### Build pour tests
```bash
./scripts/build.sh
# Installez le ZIP sur un site de staging
```

---

## 🔗 Liens utiles

- **Documentation :** `../README.md`
- **Changelog :** `../CHANGELOG.md`
- **GitHub :** https://github.com/brandonviry/Avis-clients---WP---pluggin

---

## 📞 Support

**VIRY Brandon**
- Site : https://devweb.viry-brandon.fr
- GitHub : @brandonviry

---

**Version des scripts :** 1.0.0
**Dernière mise à jour :** 2026-02-05
