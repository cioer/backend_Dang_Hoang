#!/bin/bash

#############################################
# Quick Index Check - Run this from local machine
#############################################

echo "=========================================="
echo "QUICK DATABASE INDEX CHECK"
echo "=========================================="
echo ""
echo "This will SSH to VPS and check indexes..."
echo ""

ssh deploy@huuthang.online << 'ENDSSH'
cd /home/deploy/backend_Dang_Hoang

# Pull latest code
echo "Pulling latest code..."
git pull origin main

echo ""
echo "Running index check..."
echo ""

# Run check
bash scripts/check_indexes.sh

echo ""
echo "=========================================="
echo "WHAT TO DO NEXT:"
echo "=========================================="
echo ""
echo "If you see ✗ MISSING or ⚠ MISSING indexes:"
echo ""
echo "1. Run this command to add missing indexes:"
echo "   ssh deploy@huuthang.online"
echo "   cd /home/deploy/backend_Dang_Hoang"
echo "   docker exec -i school_management_db mysql -uroot -proot_password school_management < migrations/add_missing_indexes.sql"
echo ""
echo "2. Or run the migration via script:"
echo "   ssh deploy@huuthang.online 'cd /home/deploy/backend_Dang_Hoang && docker exec -i school_management_db mysql -uroot -proot_password school_management < migrations/add_missing_indexes.sql'"
echo ""
ENDSSH

echo ""
echo "Check completed!"
