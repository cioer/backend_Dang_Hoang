#!/bin/bash

#############################################
# Auto-fix file permissions script
# Run this after git pull to ensure correct permissions
#############################################

echo "======================================"
echo "AUTO-FIX FILE PERMISSIONS"
echo "======================================"

# Configuration
WEBROOT="/var/www/html"
WEB_USER="www-data"
WEB_GROUP="www-data"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running inside Docker container
if [ -f /.dockerenv ]; then
    echo -e "${GREEN}✓ Running inside Docker container${NC}"
    IN_DOCKER=true
else
    echo -e "${YELLOW}⚠ Running on host system${NC}"
    IN_DOCKER=false
fi

# Function to fix permissions
fix_permissions() {
    local path=$1
    local description=$2

    echo ""
    echo "→ Fixing: $description"

    if [ "$IN_DOCKER" = true ]; then
        # Running inside container
        find "$path" -type f -exec chmod 664 {} \; 2>/dev/null
        find "$path" -type d -exec chmod 775 {} \; 2>/dev/null
        chown -R $WEB_USER:$WEB_GROUP "$path" 2>/dev/null
    else
        # Running on host - need to use docker exec
        docker exec school_management_backend find "$path" -type f -exec chmod 664 {} \; 2>/dev/null
        docker exec school_management_backend find "$path" -type d -exec chmod 775 {} \; 2>/dev/null
        docker exec school_management_backend chown -R $WEB_USER:$WEB_GROUP "$path" 2>/dev/null
    fi

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}  ✓ Fixed${NC}"
    else
        echo -e "${RED}  ✗ Failed${NC}"
        return 1
    fi
}

# Start fixing permissions
echo ""
echo "Starting permission fix..."

# 1. Fix API directory
fix_permissions "$WEBROOT/api" "API files"

# 2. Fix uploads directory (critical for file uploads)
fix_permissions "$WEBROOT/uploads" "Upload directories"

# 3. Fix lib directory
if [ -d "$WEBROOT/lib" ]; then
    fix_permissions "$WEBROOT/lib" "Library files"
fi

# 4. Fix config directory
if [ -d "$WEBROOT/config" ]; then
    fix_permissions "$WEBROOT/config" "Config files"
fi

# 5. Make sure bootstrap.php is readable
if [ "$IN_DOCKER" = true ]; then
    chmod 644 "$WEBROOT/bootstrap.php" 2>/dev/null
    chown $WEB_USER:$WEB_GROUP "$WEBROOT/bootstrap.php" 2>/dev/null
else
    docker exec school_management_backend chmod 644 "$WEBROOT/bootstrap.php" 2>/dev/null
    docker exec school_management_backend chown $WEB_USER:$WEB_GROUP "$WEBROOT/bootstrap.php" 2>/dev/null
fi

# 6. Ensure uploads subdirectories exist with correct permissions
UPLOAD_DIRS=("avatars" "banners" "documents" "temp")
for dir in "${UPLOAD_DIRS[@]}"; do
    if [ "$IN_DOCKER" = true ]; then
        mkdir -p "$WEBROOT/uploads/$dir" 2>/dev/null
        chmod 775 "$WEBROOT/uploads/$dir" 2>/dev/null
        chown $WEB_USER:$WEB_GROUP "$WEBROOT/uploads/$dir" 2>/dev/null
    else
        docker exec school_management_backend mkdir -p "$WEBROOT/uploads/$dir" 2>/dev/null
        docker exec school_management_backend chmod 775 "$WEBROOT/uploads/$dir" 2>/dev/null
        docker exec school_management_backend chown $WEB_USER:$WEB_GROUP "$WEBROOT/uploads/$dir" 2>/dev/null
    fi
done

echo ""
echo "======================================"
echo -e "${GREEN}✓ Permission fix completed!${NC}"
echo "======================================"
echo ""

# Verify critical paths
echo "Verification:"
if [ "$IN_DOCKER" = true ]; then
    ls -la "$WEBROOT/api" | head -5
    ls -la "$WEBROOT/uploads" | head -5
else
    docker exec school_management_backend ls -la "$WEBROOT/api" | head -5
    docker exec school_management_backend ls -la "$WEBROOT/uploads" | head -5
fi

exit 0
