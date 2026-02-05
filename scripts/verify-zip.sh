#!/bin/bash

################################################################################
# Script de vérification du ZIP pour Site Avis Clients
# Auteur: VIRY Brandon
# Site: https://devweb.viry-brandon.fr
################################################################################

set -e

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
PLUGIN_SLUG="site-avis-clients"
DIST_DIR="dist"

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

# Trouver le dernier ZIP
find_latest_zip() {
    local LATEST_ZIP=$(ls -t "$DIST_DIR"/*.zip 2>/dev/null | head -1)

    if [ -z "$LATEST_ZIP" ]; then
        log_error "Aucun fichier ZIP trouvé dans $DIST_DIR/"
        log_info "Exécutez d'abord ./scripts/build.sh"
        exit 1
    fi

    echo "$LATEST_ZIP"
}

# Vérifier le checksum
verify_checksum() {
    local ZIP_FILE=$1
    local CHECKSUM_FILE="${ZIP_FILE}.sha256"

    log_info "Vérification du checksum..."

    if [ ! -f "$CHECKSUM_FILE" ]; then
        log_warning "Fichier checksum introuvable: $CHECKSUM_FILE"
        return 1
    fi

    local EXPECTED=$(cat "$CHECKSUM_FILE" | awk '{print $1}')

    if command -v sha256sum &> /dev/null; then
        local ACTUAL=$(sha256sum "$ZIP_FILE" | awk '{print $1}')
    elif command -v shasum &> /dev/null; then
        local ACTUAL=$(shasum -a 256 "$ZIP_FILE" | awk '{print $1}')
    else
        log_warning "sha256sum non disponible"
        return 1
    fi

    if [ "$EXPECTED" = "$ACTUAL" ]; then
        log_success "Checksum valide"
        return 0
    else
        log_error "Checksum invalide !"
        log_error "Attendu: $EXPECTED"
        log_error "Obtenu:  $ACTUAL"
        return 1
    fi
}

# Lister le contenu du ZIP
list_zip_contents() {
    local ZIP_FILE=$1

    log_info "Contenu de l'archive:"
    echo ""

    if command -v unzip &> /dev/null; then
        unzip -l "$ZIP_FILE" | tail -n +4 | head -n -2 | awk '{print "   " $4}'
    else
        log_warning "unzip non disponible, impossible de lister le contenu"
    fi

    echo ""
}

# Vérifier les fichiers requis
check_required_files() {
    local ZIP_FILE=$1
    local ERRORS=0

    log_info "Vérification des fichiers requis..."
    echo ""

    local REQUIRED_FILES=(
        "$PLUGIN_SLUG/site-avis-clients.php"
        "$PLUGIN_SLUG/uninstall.php"
        "$PLUGIN_SLUG/readme.txt"
        "$PLUGIN_SLUG/LICENSE"
        "$PLUGIN_SLUG/CHANGELOG.md"
        "$PLUGIN_SLUG/includes/"
        "$PLUGIN_SLUG/templates/"
        "$PLUGIN_SLUG/assets/"
        "$PLUGIN_SLUG/languages/"
    )

    for FILE in "${REQUIRED_FILES[@]}"; do
        if unzip -l "$ZIP_FILE" | grep -q "$FILE"; then
            log_success "$FILE"
        else
            log_error "$FILE (manquant)"
            ERRORS=$((ERRORS + 1))
        fi
    done

    echo ""
    return $ERRORS
}

# Vérifier les fichiers à exclure
check_excluded_files() {
    local ZIP_FILE=$1
    local WARNINGS=0

    log_info "Vérification des fichiers exclus..."
    echo ""

    local EXCLUDED_PATTERNS=(
        "README.md"
        "INSTALLATION.md"
        "EXAMPLES.md"
        ".github"
        ".agents"
        ".gitignore"
        "scripts/"
        "build/"
        "dist/"
    )

    for PATTERN in "${EXCLUDED_PATTERNS[@]}"; do
        if unzip -l "$ZIP_FILE" | grep -q "$PATTERN"; then
            log_warning "$PATTERN devrait être exclu"
            WARNINGS=$((WARNINGS + 1))
        else
            log_success "$PATTERN (bien exclu)"
        fi
    done

    echo ""
    return $WARNINGS
}

# Extraire et tester
test_extract() {
    local ZIP_FILE=$1
    local TEST_DIR="test-extract"

    log_info "Test d'extraction..."

    if [ -d "$TEST_DIR" ]; then
        rm -rf "$TEST_DIR"
    fi

    mkdir -p "$TEST_DIR"

    if unzip -q "$ZIP_FILE" -d "$TEST_DIR"; then
        log_success "Extraction réussie"

        # Vérifier la structure
        if [ -d "$TEST_DIR/$PLUGIN_SLUG" ]; then
            log_success "Structure correcte ($PLUGIN_SLUG/)"
        else
            log_error "Structure incorrecte (pas de dossier $PLUGIN_SLUG/)"
        fi

        # Nettoyer
        rm -rf "$TEST_DIR"
    else
        log_error "Échec de l'extraction"
        rm -rf "$TEST_DIR"
        return 1
    fi

    echo ""
}

# Afficher les informations
show_info() {
    local ZIP_FILE=$1

    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo -e "${BLUE}Informations sur l'archive${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""

    echo "📦 Fichier: $(basename "$ZIP_FILE")"

    if [ -f "$ZIP_FILE" ]; then
        local SIZE=$(du -h "$ZIP_FILE" | cut -f1)
        echo "📏 Taille: $SIZE"
    fi

    if command -v unzip &> /dev/null; then
        local FILE_COUNT=$(unzip -l "$ZIP_FILE" | tail -2 | head -1 | awk '{print $2}')
        echo "📄 Nombre de fichiers: $FILE_COUNT"
    fi

    echo ""
}

# Fonction principale
main() {
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "  Site Avis Clients - Vérification du ZIP"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""

    # Trouver le dernier ZIP
    ZIP_FILE=$(find_latest_zip)
    log_success "ZIP trouvé: $(basename "$ZIP_FILE")"
    echo ""

    # Afficher les infos
    show_info "$ZIP_FILE"

    # Vérifications
    local TOTAL_ERRORS=0
    local TOTAL_WARNINGS=0

    verify_checksum "$ZIP_FILE" || TOTAL_ERRORS=$((TOTAL_ERRORS + 1))
    list_zip_contents "$ZIP_FILE"
    check_required_files "$ZIP_FILE" || TOTAL_ERRORS=$((TOTAL_ERRORS + $?))
    check_excluded_files "$ZIP_FILE" || TOTAL_WARNINGS=$((TOTAL_WARNINGS + $?))
    test_extract "$ZIP_FILE" || TOTAL_ERRORS=$((TOTAL_ERRORS + 1))

    # Résultat final
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

    if [ $TOTAL_ERRORS -eq 0 ]; then
        echo -e "${GREEN}✓ Vérification réussie !${NC}"

        if [ $TOTAL_WARNINGS -gt 0 ]; then
            echo -e "${YELLOW}⚠ $TOTAL_WARNINGS avertissement(s)${NC}"
        fi
    else
        echo -e "${RED}✗ Vérification échouée${NC}"
        echo -e "${RED}✗ $TOTAL_ERRORS erreur(s)${NC}"

        if [ $TOTAL_WARNINGS -gt 0 ]; then
            echo -e "${YELLOW}⚠ $TOTAL_WARNINGS avertissement(s)${NC}"
        fi
    fi

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""

    exit $TOTAL_ERRORS
}

# Exécuter
main
