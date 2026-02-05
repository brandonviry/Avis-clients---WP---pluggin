# ============================================================================
# Script de build PowerShell pour Site Avis Clients
# Auteur: VIRY Brandon
# Site: https://devweb.viry-brandon.fr
# ============================================================================

# Configuration
$PluginSlug = "site-avis-clients"
$PluginFile = "site-avis-clients.php"
$BuildDir = "build"
$DistDir = "dist"

# Fonctions d'affichage
function Write-Info {
    param([string]$Message)
    Write-Host "[INFO] " -ForegroundColor Blue -NoNewline
    Write-Host $Message
}

function Write-Success {
    param([string]$Message)
    Write-Host "[OK] " -ForegroundColor Green -NoNewline
    Write-Host $Message
}

function Write-Warning {
    param([string]$Message)
    Write-Host "[WARNING] " -ForegroundColor Yellow -NoNewline
    Write-Host $Message
}

function Write-Error {
    param([string]$Message)
    Write-Host "[ERREUR] " -ForegroundColor Red -NoNewline
    Write-Host $Message
}

# En-tête
Write-Host ""
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "  Site Avis Clients - Script de Build" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""

# Vérifier si le fichier principal existe
if (-not (Test-Path $PluginFile)) {
    Write-Error "Fichier $PluginFile introuvable"
    exit 1
}

# Extraire la version
Write-Info "Extraction de la version..."
$VersionLine = Select-String -Path $PluginFile -Pattern "Version:" | Select-Object -First 1
if ($VersionLine) {
    $Version = ($VersionLine.Line -split ":")[1].Trim()
    Write-Success "Version detectee: $Version"
} else {
    Write-Error "Version introuvable dans $PluginFile"
    exit 1
}
Write-Host ""

# Nettoyer les anciens builds
Write-Info "Nettoyage des anciens builds..."
if (Test-Path $BuildDir) {
    Remove-Item -Path $BuildDir -Recurse -Force
    Write-Success "Dossier $BuildDir supprime"
}

if (-not (Test-Path $DistDir)) {
    New-Item -ItemType Directory -Path $DistDir | Out-Null
    Write-Success "Dossier $DistDir cree"
} else {
    Write-Warning "Le dossier $DistDir existe deja"
}
Write-Host ""

# Créer la structure de build
Write-Info "Creation de la structure de build..."
New-Item -ItemType Directory -Path "$BuildDir\$PluginSlug" -Force | Out-Null
Write-Success "Structure creee"
Write-Host ""

# Copier les fichiers
Write-Info "Copie des fichiers du plugin..."

# Fichiers PHP
Copy-Item -Path "includes" -Destination "$BuildDir\$PluginSlug\" -Recurse -Force
Copy-Item -Path "templates" -Destination "$BuildDir\$PluginSlug\" -Recurse -Force
Copy-Item -Path $PluginFile -Destination "$BuildDir\$PluginSlug\" -Force
Copy-Item -Path "uninstall.php" -Destination "$BuildDir\$PluginSlug\" -Force
Write-Success "Fichiers PHP copies"

# Assets
Copy-Item -Path "assets" -Destination "$BuildDir\$PluginSlug\" -Recurse -Force
Write-Success "Assets copies"

# Traductions
Copy-Item -Path "languages" -Destination "$BuildDir\$PluginSlug\" -Recurse -Force
Write-Success "Traductions copiees"

# Documentation
Copy-Item -Path "readme.txt" -Destination "$BuildDir\$PluginSlug\" -Force
Copy-Item -Path "LICENSE" -Destination "$BuildDir\$PluginSlug\" -Force
Copy-Item -Path "CHANGELOG.md" -Destination "$BuildDir\$PluginSlug\" -Force
Write-Success "Documentation copiee"
Write-Host ""

# Créer l'archive ZIP
$ZipName = "$PluginSlug-$Version.zip"
$ZipPath = "$DistDir\$ZipName"

Write-Info "Creation de l'archive $ZipName..."

# Supprimer l'ancien ZIP s'il existe
if (Test-Path $ZipPath) {
    Remove-Item -Path $ZipPath -Force
}

# Créer le ZIP
try {
    Compress-Archive -Path "$BuildDir\$PluginSlug" -DestinationPath $ZipPath -Force
    Write-Success "Archive creee: $ZipPath"
} catch {
    Write-Error "Echec de la creation de l'archive: $_"
    exit 1
}
Write-Host ""

# Créer le fichier de version
$VersionFile = "$DistDir\version.txt"
Write-Info "Creation du fichier de version..."

$VersionContent = @"
Site Avis Clients - WordPress Plugin
Version: $Version
Date: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
Auteur: VIRY Brandon
Site: https://devweb.viry-brandon.fr
GitHub: https://github.com/brandonviry/Avis-clients---WP---pluggin

Archive: $ZipName
"@

Set-Content -Path $VersionFile -Value $VersionContent -Encoding UTF8
Write-Success "Fichier de version cree"
Write-Host ""

# Calculer le checksum SHA256
Write-Info "Calcul du checksum..."
try {
    $Hash = Get-FileHash -Path $ZipPath -Algorithm SHA256
    $ChecksumFile = "$ZipPath.sha256"
    Set-Content -Path $ChecksumFile -Value $Hash.Hash -Encoding UTF8
    Write-Success "Checksum SHA256 cree"
} catch {
    Write-Warning "Impossible de creer le checksum: $_"
}
Write-Host ""

# Nettoyer le dossier temporaire
Remove-Item -Path $BuildDir -Recurse -Force
Write-Success "Dossier temporaire nettoye"
Write-Host ""

# Afficher le résumé
Write-Host "================================================================" -ForegroundColor Green
Write-Host "  Build termine avec succes !" -ForegroundColor Green
Write-Host "================================================================" -ForegroundColor Green
Write-Host ""
Write-Host "Plugin: Site Avis Clients"
Write-Host "Version: $Version"
Write-Host "Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
Write-Host ""
Write-Host "Archive creee:"
Write-Host "  $ZipPath"

# Afficher la taille
if (Test-Path $ZipPath) {
    $Size = (Get-Item $ZipPath).Length
    $SizeKB = [math]::Round($Size / 1KB, 2)
    Write-Host "  Taille: $SizeKB KB"
}

# Afficher le checksum
$ChecksumFile = "$ZipPath.sha256"
if (Test-Path $ChecksumFile) {
    Write-Host ""
    Write-Host "Checksum SHA256:"
    $Checksum = Get-Content $ChecksumFile
    Write-Host "  $Checksum"
}

Write-Host ""
Write-Host "Fichiers dans $DistDir/:"
Get-ChildItem -Path $DistDir | ForEach-Object {
    $FileSize = [math]::Round($_.Length / 1KB, 2)
    Write-Host "  $($_.Name) ($FileSize KB)"
}

Write-Host ""
Write-Host "================================================================" -ForegroundColor Green
Write-Host ""

# Ouvrir le dossier dist
Write-Info "Ouverture du dossier dist..."
Invoke-Item $DistDir

Write-Host ""
Write-Host "Appuyez sur une touche pour continuer..." -ForegroundColor Yellow
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
