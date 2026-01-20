#!/bin/bash

#############################################
# Remote Database Indexes Check
# Chạy từ máy local, kết nối SSH đến VPS
#############################################

VPS_HOST="huuthang.online"
VPS_USER="deploy"
REPO_PATH="/home/deploy/backend_Dang_Hoang"

echo "=========================================="
echo "REMOTE DATABASE INDEXES CHECK"
echo "=========================================="
echo ""
echo "Connecting to VPS: $VPS_USER@$VPS_HOST"
echo "Repository: $REPO_PATH"
echo ""

# Upload check script to VPS and run it
ssh $VPS_USER@$VPS_HOST << 'ENDSSH'
cd /home/deploy/backend_Dang_Hoang

# Make sure script is executable
chmod +x scripts/check_indexes.sh

# Run the check script
bash scripts/check_indexes.sh
ENDSSH

echo ""
echo "Remote check completed!"
