#!/bin/bash

################################################################################
# Script de build pour Site Avis Clients
# Auteur: VIRY Brandon
# Site: https://devweb.viry-brandon.fr
################################################################################

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PLUGIN_SLUG="site-avis-clients"
PLUGIN_FILE="site-avis-clients.php"
BUILD_DIR="build"
DIST_DIR="dist"

# Fonction pour afficher les messages
log_info() {
    echo -e "${BLUE}ℹ ${NC}$1"
}

log_success() {
    echo -e "${GREEN}✓${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

log_error() {
    echo -e "${RED}✗${NC} $1"
}

# Récupérer la version depuis le fichier principal
get_version() {
    if [ ! -f "$PLUGIN_FILE" ]; then
        log_error "Fichier $PLUGIN_FILE introuvable"
        exit 1
    fi

    VERSION=$(grep "Version:" "$PLUGIN_FILE" | head -1 | awk '{print $3}')

    if [ -z "$VERSION" ]; then
        log_error "Version introuvable dans $PLUGIN_FILE"
        exit 1
    fi

    echo "$VERSION"
}

# Nettoyer les anciens builds
clean() {
    log_info "Nettoyage des anciens builds..."

    if [ -d "$BUILD_DIR" ]; then
        rm -rf "$BUILD_DIR"
        log_success "Dossier $BUILD_DIR supprimé"
    fi

    if [ -d "$DIST_DIR" ]; then
        log_warning "Le dossier $DIST_DIR existe déjà"
    else
        mkdir -p "$DIST_DIR"
        log_success "Dossier $DIST_DIR créé"
    fi
}

# Créer la structure de build
create_build_structure() {
    log_info "Création de la structure de build..."

    mkdir -p "$BUILD_DIR/$PLUGIN_SLUG"
    log_success "Structure créée"
}

# Copier les fichiers nécessaires
copy_files() {
    log_info "Copie des fichiers du plugin..."

    # Fichiers PHP
    cp -r includes "$BUILD_DIR/$PLUGIN_SLUG/"
    cp -r templates "$BUILD_DIR/$PLUGIN_SLUG/"
    cp "$PLUGIN_FILE" "$BUILD_DIR/$PLUGIN_SLUG/"
    cp uninstall.php "$BUILD_DIR/$PLUGIN_SLUG/"
    log_success "Fichiers PHP copiés"

    # Assets
    cp -r assets "$BUILD_DIR/$PLUGIN_SLUG/"
    log_success "Assets copiés"

    # Traductions
    cp -r languages "$BUILD_DIR/$PLUGIN_SLUG/"
    log_success "Traductions copiées"

    # Documentation utilisateur (seulement les essentiels)
    cp readme.txt "$BUILD_DIR/$PLUGIN_SLUG/"
    cp LICENSE "$BUILD_DIR/$PLUGIN_SLUG/"
    cp CHANGELOG.md "$BUILD_DIR/$PLUGIN_SLUG/"
    log_success "Documentation copiée"
}

# Créer l'archive ZIP
create_zip() {
    VERSION=$1
    ZIP_NAME="${PLUGIN_SLUG}-${VERSION}.zip"

    log_info "Création de l'archive $ZIP_NAME..."

    cd "$BUILD_DIR"

    if command -v zip &> /dev/null; then
        zip -r "$ZIP_NAME" "$PLUGIN_SLUG" -q
    else
        log_error "La commande 'zip' n'est pas installée"
        exit 1
    fi

    mv "$ZIP_NAME" "../$DIST_DIR/"
    cd ..

    log_success "Archive créée: $DIST_DIR/$ZIP_NAME"
}

# Créer le fichier de version
create_version_file() {
    VERSION=$1
    VERSION_FILE="$DIST_DIR/version.txt"

    log_info "Création du fichier de version..."

    cat > "$VERSION_FILE" << EOF
Site Avis Clients - WordPress Plugin
Version: $VERSION
Date: $(date +"%Y-%m-%d %H:%M:%S")
Auteur: VIRY Brandon
Site: https://devweb.viry-brandon.fr
GitHub: https://github.com/brandonviry/Avis-clients---WP---pluggin

Archive: ${PLUGIN_SLUG}-${VERSION}.zip
EOF

    log_success "Fichier de version créé"
}

# Calculer le checksum
create_checksum() {
    VERSION=$1
    ZIP_FILE="$DIST_DIR/${PLUGIN_SLUG}-${VERSION}.zip"

    log_info "Calcul du checksum..."

    if [ -f "$ZIP_FILE" ]; then
        if command -v sha256sum &> /dev/null; then
            sha256sum "$ZIP_FILE" > "$ZIP_FILE.sha256"
            log_success "Checksum SHA256 créé"
        elif command -v shasum &> /dev/null; then
            shasum -a 256 "$ZIP_FILE" > "$ZIP_FILE.sha256"
            log_success "Checksum SHA256 créé"
        else
            log_warning "Impossible de créer le checksum (sha256sum non disponible)"
        fi
    fi
}

# Afficher le résumé
show_summary() {
    VERSION=$1
    ZIP_FILE="$DIST_DIR/${PLUGIN_SLUG}-${VERSION}.zip"

    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo -e "${GREEN}✓ Build terminé avec succès !${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "📦 Plugin: Site Avis Clients"
    echo "🏷️  Version: $VERSION"
    echo "📅 Date: $(date +"%Y-%m-%d %H:%M:%S")"
    echo ""
    echo "📂 Archive créée:"
    echo "   $ZIP_FILE"

    if [ -f "$ZIP_FILE" ]; then
        SIZE=$(du -h "$ZIP_FILE" | cut -f1)
        echo "   Taille: $SIZE"
    fi

    if [ -f "$ZIP_FILE.sha256" ]; then
        echo ""
        echo "🔒 Checksum SHA256:"
        cat "$ZIP_FILE.sha256" | awk '{print "   " $1}'
    fi

    echo ""
    echo "📝 Fichiers dans $DIST_DIR/:"
    ls -lh "$DIST_DIR" | tail -n +2 | awk '{print "   " $9 " (" $5 ")"}'

    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
}

# Fonction principale
main() {
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "  Site Avis Clients - Script de Build"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""

    # Récupérer la version
    VERSION=$(get_version)
    log_success "Version détectée: $VERSION"
    echo ""

    # Étapes du build
    clean
    create_build_structure
    copy_files
    create_zip "$VERSION"
    create_version_file "$VERSION"
    create_checksum "$VERSION"

    # Nettoyer le dossier de build temporaire
    rm -rf "$BUILD_DIR"
    log_success "Dossier temporaire nettoyé"

    # Afficher le résumé
    show_summary "$VERSION"

    echo ""
}

# Exécuter le script
main
