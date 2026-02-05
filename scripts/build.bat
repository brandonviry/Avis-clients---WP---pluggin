@echo off
REM ============================================================================
REM Script de build pour Site Avis Clients (Windows)
REM Auteur: VIRY Brandon
REM Site: https://devweb.viry-brandon.fr
REM ============================================================================

setlocal enabledelayedexpansion

REM Configuration
set "PLUGIN_SLUG=site-avis-clients"
set "PLUGIN_FILE=site-avis-clients.php"
set "BUILD_DIR=build"
set "DIST_DIR=dist"

REM Couleurs (si supportées)
set "GREEN=[32m"
set "YELLOW=[33m"
set "RED=[31m"
set "BLUE=[34m"
set "NC=[0m"

echo.
echo ================================================================
echo   Site Avis Clients - Script de Build
echo ================================================================
echo.

REM Vérifier si le fichier principal existe
if not exist "%PLUGIN_FILE%" (
    echo [ERREUR] Fichier %PLUGIN_FILE% introuvable
    exit /b 1
)

REM Extraire la version du fichier PHP
echo [INFO] Extraction de la version...
for /f "tokens=3" %%a in ('findstr /C:"Version:" "%PLUGIN_FILE%"') do (
    set "VERSION=%%a"
    goto :version_found
)
:version_found

if "%VERSION%"=="" (
    echo [ERREUR] Version introuvable dans %PLUGIN_FILE%
    exit /b 1
)

echo [OK] Version detectee: %VERSION%
echo.

REM Nettoyer les anciens builds
echo [INFO] Nettoyage des anciens builds...
if exist "%BUILD_DIR%" (
    rmdir /s /q "%BUILD_DIR%"
    echo [OK] Dossier %BUILD_DIR% supprime
)

if not exist "%DIST_DIR%" (
    mkdir "%DIST_DIR%"
    echo [OK] Dossier %DIST_DIR% cree
) else (
    echo [WARNING] Le dossier %DIST_DIR% existe deja
)
echo.

REM Créer la structure de build
echo [INFO] Creation de la structure de build...
mkdir "%BUILD_DIR%\%PLUGIN_SLUG%"
echo [OK] Structure creee
echo.

REM Copier les fichiers
echo [INFO] Copie des fichiers du plugin...

REM Fichiers PHP
xcopy /E /I /Q "includes" "%BUILD_DIR%\%PLUGIN_SLUG%\includes" > nul
xcopy /E /I /Q "templates" "%BUILD_DIR%\%PLUGIN_SLUG%\templates" > nul
copy /Y "%PLUGIN_FILE%" "%BUILD_DIR%\%PLUGIN_SLUG%\" > nul
copy /Y "uninstall.php" "%BUILD_DIR%\%PLUGIN_SLUG%\" > nul
echo [OK] Fichiers PHP copies

REM Assets
xcopy /E /I /Q "assets" "%BUILD_DIR%\%PLUGIN_SLUG%\assets" > nul
echo [OK] Assets copies

REM Traductions
xcopy /E /I /Q "languages" "%BUILD_DIR%\%PLUGIN_SLUG%\languages" > nul
echo [OK] Traductions copiees

REM Documentation
copy /Y "readme.txt" "%BUILD_DIR%\%PLUGIN_SLUG%\" > nul
copy /Y "LICENSE" "%BUILD_DIR%\%PLUGIN_SLUG%\" > nul
copy /Y "CHANGELOG.md" "%BUILD_DIR%\%PLUGIN_SLUG%\" > nul
echo [OK] Documentation copiee
echo.

REM Créer l'archive ZIP
set "ZIP_NAME=%PLUGIN_SLUG%-%VERSION%.zip"
echo [INFO] Creation de l'archive %ZIP_NAME%...

REM Vérifier si PowerShell est disponible
where powershell >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    powershell -Command "Compress-Archive -Path '%BUILD_DIR%\%PLUGIN_SLUG%' -DestinationPath '%DIST_DIR%\%ZIP_NAME%' -Force"
    if %ERRORLEVEL% EQU 0 (
        echo [OK] Archive creee: %DIST_DIR%\%ZIP_NAME%
    ) else (
        echo [ERREUR] Echec de la creation de l'archive
        exit /b 1
    )
) else (
    echo [ERREUR] PowerShell n'est pas disponible
    echo [INFO] Veuillez installer PowerShell ou utiliser un outil de compression
    exit /b 1
)
echo.

REM Créer le fichier de version
set "VERSION_FILE=%DIST_DIR%\version.txt"
echo [INFO] Creation du fichier de version...

(
    echo Site Avis Clients - WordPress Plugin
    echo Version: %VERSION%
    echo Date: %DATE% %TIME%
    echo Auteur: VIRY Brandon
    echo Site: https://devweb.viry-brandon.fr
    echo GitHub: https://github.com/brandonviry/Avis-clients---WP---pluggin
    echo.
    echo Archive: %ZIP_NAME%
) > "%VERSION_FILE%"

echo [OK] Fichier de version cree
echo.

REM Calculer le checksum SHA256
echo [INFO] Calcul du checksum...
where powershell >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    powershell -Command "(Get-FileHash '%DIST_DIR%\%ZIP_NAME%' -Algorithm SHA256).Hash" > "%DIST_DIR%\%ZIP_NAME%.sha256"
    echo [OK] Checksum SHA256 cree
) else (
    echo [WARNING] Impossible de creer le checksum
)
echo.

REM Nettoyer le dossier temporaire
rmdir /s /q "%BUILD_DIR%"
echo [OK] Dossier temporaire nettoye
echo.

REM Afficher le résumé
echo ================================================================
echo   Build termine avec succes !
echo ================================================================
echo.
echo Plugin: Site Avis Clients
echo Version: %VERSION%
echo Date: %DATE% %TIME%
echo.
echo Archive creee:
echo   %DIST_DIR%\%ZIP_NAME%

REM Afficher la taille du fichier
for %%A in ("%DIST_DIR%\%ZIP_NAME%") do (
    set "SIZE=%%~zA"
    set /a "SIZE_KB=!SIZE! / 1024"
    echo   Taille: !SIZE_KB! KB
)

if exist "%DIST_DIR%\%ZIP_NAME%.sha256" (
    echo.
    echo Checksum SHA256:
    type "%DIST_DIR%\%ZIP_NAME%.sha256"
)

echo.
echo Fichiers dans %DIST_DIR%/:
dir /B "%DIST_DIR%"

echo.
echo ================================================================
echo.

REM Ouvrir le dossier dist dans l'explorateur
echo Ouverture du dossier dist...
explorer "%DIST_DIR%"

endlocal
pause
